<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GallerySampleCT extends Controller
{
    public function index()
    {
        $data['engines'] = DB::table('m_engines')->orderBy('name')->get();
        $data['categories'] = DB::table('m_categories')->where('name', '!=', 'Display')->orderBy('name')->get();

        $this->cleanTemporary();
        return view('admin.galery-sample.index')->with($data);
    }

    public function create()
    {
        $data['engines'] = DB::table('m_engines')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $data['categories'] = DB::table('m_categories')->where('name', '!=', 'Display')->orderBy('name')->get();

        return view('admin.galery-sample.create')->with($data);
    }

    public function store(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'engine_id' => [
                'required',
                'integer',
                'exists:m_engines,id',
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:m_categories,id',
            ],

            'images' => [
                'required',
                'array',
                'min:1',
                'max:10',
            ],

            'images.*' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'image_is_primary' => [
                'required',
                'array',
            ],

            'image_is_primary.*' => [
                'required',
                'in:0,1',
            ],
        ]);

        $engineId = $validated['engine_id'];

        $categoryId = $validated['category_id'];

        /*
    |--------------------------------------------------------------------------
    | IMAGE DATA
    |--------------------------------------------------------------------------
    */

        $images = $request->file('images', []);

        $primaryFlags = $request->input('image_is_primary', []);

        /*
    |--------------------------------------------------------------------------
    | HARUS TEPAT SATU GAMBAR UTAMA (DALAM KIRIMAN INI)
    |--------------------------------------------------------------------------
    */

        $primaryIndexes = [];

        foreach ($images as $index => $image) {
            if (
                isset($primaryFlags[$index]) &&
                (int) $primaryFlags[$index] === 1
            ) {
                $primaryIndexes[] = $index;
            }
        }

        if (count($primaryIndexes) !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Harus terdapat tepat satu gambar utama.',
            ], 422);
        }

        $primaryIndex = $primaryIndexes[0];

        /*
    |--------------------------------------------------------------------------
    | CHECK DUPLICATE IMAGE
    |--------------------------------------------------------------------------
    |
    | Duplikat berdasarkan:
    | engine_id + category_id + isi file (hash SHA-256)
    |
    | 1. Duplikat di dalam kiriman yang sama
    | 2. Duplikat dengan data yang sudah tersimpan
    |
    */

        $imageHashes = [];

        $seenHashes = [];

        foreach ($images as $index => $image) {

            $hash = hash_file('sha256', $image->getRealPath());

            /* DUPLIKAT DI DALAM KIRIMAN */
            if (isset($seenHashes[$hash])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gambar "' . $seenHashes[$hash] .
                        '" dan "' . $image->getClientOriginalName() .
                        '" memiliki isi yang sama.',
                ], 422);
            }

            $seenHashes[$hash] = $image->getClientOriginalName();

            $imageHashes[$index] = $hash;
        }

        /* DUPLIKAT DENGAN DATA YANG SUDAH ADA */
        $existingHashes = DB::table('m_sample_galleries')
            ->where('engine_id', $engineId)
            ->where('category_id', $categoryId)
            ->whereIn('image_hash', array_values($imageHashes))
            ->pluck('image_hash')
            ->all();

        if (!empty($existingHashes)) {

            foreach ($images as $index => $image) {
                if (in_array($imageHashes[$index], $existingHashes, true)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gambar "' . $image->getClientOriginalName() .
                            '" sudah ada pada mesin dan kategori tersebut.',
                    ], 422);
                }
            }
        }

        /*
    |--------------------------------------------------------------------------
    | IMAGE STORAGE PATH
    |--------------------------------------------------------------------------
    */

        $storedFiles = [];

        if (app()->environment('production')) {

            /*
        | Path production akan ditentukan kemudian.
        */

            $uploadPath = null;
        } else {

            $uploadPath = public_path('images/gallery-samples');
        }

        if (!$uploadPath) {
            return response()->json([
                'success' => false,
                'message' => 'Penyimpanan gambar untuk environment production belum dikonfigurasi.',
            ], 500);
        }

        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0755, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Folder penyimpanan gambar tidak dapat dibuat.',
                ], 500);
            }
        }

        try {

            DB::beginTransaction();

            /*
        |--------------------------------------------------------------------------
        | ACTOR
        |--------------------------------------------------------------------------
        */

            $actor = Auth::user()->name;

            /*
        |--------------------------------------------------------------------------
        | GAMBAR UTAMA BARU MENGGANTIKAN YANG LAMA
        |--------------------------------------------------------------------------
        | Reset flag utama pada kombinasi mesin + kategori yang sama.
        |--------------------------------------------------------------------------
        */

            DB::table('m_sample_galleries')
                ->where('engine_id', $engineId)
                ->where('category_id', $categoryId)
                ->where('is_primary', true)
                ->update([
                    'is_primary' => false,
                    'updated_by' => $actor,
                    'updated_at' => now(),
                ]);

            /*
        |--------------------------------------------------------------------------
        | URUTAN BERIKUTNYA
        |--------------------------------------------------------------------------
        */

            $nextOrder = (int) DB::table('m_sample_galleries')
                ->where('engine_id', $engineId)
                ->where('category_id', $categoryId)
                ->max('sort_order');

            /*
        |--------------------------------------------------------------------------
        | INSERT GALLERY IMAGES
        |--------------------------------------------------------------------------
        */

            foreach ($images as $index => $image) {

                $extension = strtolower($image->getClientOriginalExtension());

                $fileName =
                    'gallery_sample_' .
                    $engineId .
                    '_' .
                    $categoryId .
                    '_' .
                    uniqid() .
                    '.' .
                    $extension;

                $image->move($uploadPath, $fileName);

                $storedFiles[] = $uploadPath . DIRECTORY_SEPARATOR . $fileName;

                DB::table('m_sample_galleries')->insert([
                    'engine_id'   => $engineId,
                    'category_id' => $categoryId,
                    'image_path'  => 'images/gallery-samples/' . $fileName,
                    'image_name'  => $image->getClientOriginalName(),
                    'image_hash'  => $imageHashes[$index],
                    'is_primary'  => $index === $primaryIndex,
                    'sort_order'  => $nextOrder + $index + 1,
                    'created_by'  => $actor,
                    'created_at'  => now(),
                    'updated_by'  => $actor,
                    'updated_at'  => now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Galeri sampel berhasil disimpan.',
            ]);
        } catch (\Throwable $e) {

            /*
        |--------------------------------------------------------------------------
        | ROLLBACK DATABASE
        |--------------------------------------------------------------------------
        */

            DB::rollBack();

            /*
        |--------------------------------------------------------------------------
        | DELETE UPLOADED FILES
        |--------------------------------------------------------------------------
        */

            foreach ($storedFiles as $filePath) {
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            Log::error('Gagal menyimpan Galeri Sampel.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan galeri sampel.',
            ], 500);
        }
    }

    public function data(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'search'      => ['nullable', 'string', 'max:100'],
            'engine_id'   => ['nullable', 'integer'],
            'category_id' => ['nullable', 'integer'],
            'page'        => ['nullable', 'integer', 'min:1'],
        ]);

        /*
    |--------------------------------------------------------------------------
    | COVER IMAGE (SUBQUERY)
    |--------------------------------------------------------------------------
    |
    | Gambar utama lebih dulu. Kalau kombinasi tidak punya gambar utama,
    | pakai gambar dengan sort_order terkecil.
    |
    */

        $cover = DB::table('m_sample_galleries as g2')
            ->select('g2.image_path')
            ->whereColumn('g2.engine_id', 'g.engine_id')
            ->whereColumn('g2.category_id', 'g.category_id')
            ->orderByDesc('g2.is_primary')
            ->orderBy('g2.sort_order')
            ->orderBy('g2.id')
            ->limit(1);

        /*
    |--------------------------------------------------------------------------
    | MAIN QUERY (SATU BARIS PER KOMBINASI MESIN + KATEGORI)
    |--------------------------------------------------------------------------
    */

        $query = DB::table('m_sample_galleries as g')
            ->join('m_engines as e', 'e.id', '=', 'g.engine_id')
            ->join('m_categories as c', 'c.id', '=', 'g.category_id')
            ->select(
                'g.engine_id',
                'g.category_id',
                'e.name as engine_name',
                'c.name as category_name'
            )
            ->selectRaw('COUNT(g.id) as image_count')
            ->selectSub($cover, 'cover_path')
            ->groupBy(
                'g.engine_id',
                'g.category_id',
                'e.name',
                'c.name'
            );

        /*
    |--------------------------------------------------------------------------
    | FILTER
    |--------------------------------------------------------------------------
    */

        if (!empty($validated['engine_id'])) {
            $query->where('g.engine_id', $validated['engine_id']);
        }

        if (!empty($validated['category_id'])) {
            $query->where('g.category_id', $validated['category_id']);
        }

        if (!empty($validated['search'])) {

            $keyword = '%' . str_replace(
                ['\\', '%', '_'],
                ['\\\\', '\%', '\_'],
                trim($validated['search'])
            ) . '%';

            $query->where(function ($q) use ($keyword) {
                $q->whereRaw('e.name ILIKE ?', [$keyword])
                    ->orWhereRaw('c.name ILIKE ?', [$keyword]);
            });
        }

        /*
    |--------------------------------------------------------------------------
    | ORDER + PAGINATION
    |--------------------------------------------------------------------------
    */

        $paginator = $query
            ->orderBy('e.name')
            ->orderBy('c.name')
            ->paginate(12);

        /*
    |--------------------------------------------------------------------------
    | FORMAT RESPONSE
    |--------------------------------------------------------------------------
    */

        $items = $paginator->getCollection()->map(function ($row) {
            return [
                'engine_id'     => (int) $row->engine_id,
                'category_id'   => (int) $row->category_id,
                'engine_name'   => $row->engine_name,
                'category_name' => $row->category_name,
                'image_count'   => (int) $row->image_count,
                'cover_url'     => $row->cover_path ? asset($row->cover_path) : null,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data'    => $items,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ],
        ]);
    }

    public function edit($engineId, $categoryId)
    {
        $engine = DB::table('m_engines')
            ->where('id', $engineId)
            ->first();

        if (!$engine) {
            abort(404);
        }

        $category = DB::table('m_categories')
            ->where('id', $categoryId)
            ->first();

        if (!$category) {
            abort(404);
        }

        $galleryImages = DB::table('m_sample_galleries')
            ->where('engine_id', $engineId)
            ->where('category_id', $categoryId)
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($galleryImages->isEmpty()) {
            abort(404);
        }

        $engines = DB::table('m_engines')
            ->orderBy('name')
            ->get();

        $categories = DB::table('m_categories')
            ->where('name', '!=', 'Display')
            ->orderBy('name')
            ->get();

        return view(
            'admin.galery-sample.edit',
            compact(
                'engine',
                'category',
                'galleryImages',
                'engines',
                'categories'
            )
        );
    }

    public function image($id)
    {
        $galleryImage = DB::table('m_sample_galleries')
            ->where('id', $id)
            ->first();

        if (!$galleryImage) {
            abort(404);
        }

        if (app()->environment('production')) {
            $sourcePath = null;
        } else {
            $sourcePath = public_path('images/gallery-samples');
        }

        if (!$sourcePath) {
            abort(404);
        }

        $sourceFile = $sourcePath
            . DIRECTORY_SEPARATOR
            . basename($galleryImage->image_path);

        if (!file_exists($sourceFile)) {
            abort(404);
        }

        $sessionKey = hash(
            'sha256',
            session()->getId()
        );

        $temporaryPath = public_path(
            'temporary-files/gallery-samples/' . $sessionKey
        );

        if (!is_dir($temporaryPath)) {
            if (!mkdir($temporaryPath, 0755, true)) {
                abort(500);
            }
        }

        $temporaryFileName =
            'gallery_sample_' .
            $galleryImage->id .
            '_' .
            basename($galleryImage->image_path);

        $temporaryFilePath =
            $temporaryPath .
            DIRECTORY_SEPARATOR .
            $temporaryFileName;

        if (!file_exists($temporaryFilePath)) {
            if (!copy(
                $sourceFile,
                $temporaryFilePath
            )) {
                abort(500);
            }
        }

        return response()->file(
            $temporaryFilePath
        );
    }

    public function cleanTemporary()
    {
        $sessionKey = hash(
            'sha256',
            session()->getId()
        );

        $temporaryPath = public_path(
            'temporary-files/gallery-samples/' . $sessionKey
        );

        if (!is_dir($temporaryPath)) {
            return response()->json([
                'success' => true,
                'message' => 'Temporary file sudah bersih.'
            ]);
        }

        $files = glob(
            $temporaryPath . DIRECTORY_SEPARATOR . '*'
        );

        foreach ($files as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        @rmdir($temporaryPath);

        return response()->json([
            'success' => true,
            'message' => 'Temporary file berhasil dibersihkan.'
        ]);
    }

    public function destroy(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'engine_id' => [
                'required',
                'integer',
                'exists:m_engines,id',
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:m_categories,id',
            ],
        ]);

        $engineId = $validated['engine_id'];
        $categoryId = $validated['category_id'];

        /*
    |--------------------------------------------------------------------------
    | GET GALLERY DATA
    |--------------------------------------------------------------------------
    |
    | Satu kombinasi mesin + kategori dapat memiliki beberapa gambar.
    | Semua gambar pada kombinasi tersebut akan dihapus.
    |
    */

        $galleries = DB::table('m_sample_galleries')
            ->where('engine_id', $engineId)
            ->where('category_id', $categoryId)
            ->get();

        if ($galleries->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Gallery sample tidak ditemukan.',
            ], 404);
        }

        /*
    |--------------------------------------------------------------------------
    | DELETE FILE GAMBAR
    |--------------------------------------------------------------------------
    |
    | image_path disimpan di database seperti:
    |
    | images/gallery-samples/gallery_sample_xxx.jpg
    |
    | Karena saat store() file disimpan ke:
    |
    | public/images/gallery-samples
    |
    | maka public_path() digunakan untuk mendapatkan lokasi
    | file fisik pada environment local.
    |
    */

        $deletedFiles = [];

        try {

            DB::beginTransaction();

            foreach ($galleries as $gallery) {

                if (!empty($gallery->image_path)) {

                    $filePath = public_path($gallery->image_path);

                    if (file_exists($filePath)) {
                        if (!unlink($filePath)) {
                            throw new \Exception(
                                'File gambar tidak dapat dihapus: ' . $filePath
                            );
                        }

                        $deletedFiles[] = $filePath;
                    }
                }
            }

            /*
        |--------------------------------------------------------------------------
        | DELETE DATABASE
        |--------------------------------------------------------------------------
        */

            DB::table('m_sample_galleries')
                ->where('engine_id', $engineId)
                ->where('category_id', $categoryId)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Gallery sample berhasil dihapus.',
            ]);
        } catch (\Throwable $e) {

            /*
        |--------------------------------------------------------------------------
        | ROLLBACK DATABASE
        |--------------------------------------------------------------------------
        */

            DB::rollBack();

            /*
        |--------------------------------------------------------------------------
        | RESTORE FILE YANG SUDAH TERHAPUS
        |--------------------------------------------------------------------------
        |
        | Untuk saat ini tidak dilakukan restore karena file yang sudah
        | dihapus secara fisik tidak dapat dikembalikan hanya dari path.
        |
        */

            Log::error('Gagal menghapus Gallery Sampel.', [
                'engine_id' => $engineId,
                'category_id' => $categoryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gallery sample gagal dihapus.',
            ], 500);
        }
    }

    public function update(Request $request, $engineId, $categoryId)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'engine_id' => [
                'required',
                'integer',
                'exists:m_engines,id',
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:m_categories,id',
            ],

            'existing_image_ids' => [
                'nullable',
                'array',
            ],

            'existing_image_ids.*' => [
                'integer',
            ],

            'deleted_image_ids' => [
                'nullable',
                'array',
            ],

            'deleted_image_ids.*' => [
                'integer',
            ],

            'images' => [
                'nullable',
                'array',
                'max:10',
            ],

            'images.*' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],

            'image_is_primary' => [
                'nullable',
                'array',
            ],

            'image_is_primary.*' => [
                'required',
                'in:0,1',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | TARGET ENGINE & CATEGORY
        |--------------------------------------------------------------------------
        */

        $targetEngineId =
            (int) $validated['engine_id'];

        $targetCategoryId =
            (int) $validated['category_id'];


        /*
        |--------------------------------------------------------------------------
        | CHECK TARGET GALLERY
        |--------------------------------------------------------------------------
        */

        $targetGalleryExists = DB::table('m_sample_galleries')
            ->where('engine_id', $targetEngineId)
            ->where('category_id', $targetCategoryId)
            ->exists();

        if (
            $targetGalleryExists &&
            (
                $targetEngineId !== (int) $engineId ||
                $targetCategoryId !== (int) $categoryId
            )
        ) {
            return response()->json([
                'success' => false,
                'message' =>
                'Galeri sampel untuk mesin dan kategori tujuan sudah ada.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | EXISTING GALLERY
        |--------------------------------------------------------------------------
        */

        $galleryImages = DB::table('m_sample_galleries')
            ->where('engine_id', $engineId)
            ->where('category_id', $categoryId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($galleryImages->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Galeri sampel tidak ditemukan.',
            ], 404);
        }

        /*
    |--------------------------------------------------------------------------
    | IMAGE DATA
    |--------------------------------------------------------------------------
    */

        $existingImageIds = array_map(
            'intval',
            $request->input('existing_image_ids', [])
        );

        $deletedImageIds = array_map(
            'intval',
            $request->input('deleted_image_ids', [])
        );

        $images = $request->file('images', []);

        $primaryFlags = $request->input(
            'image_is_primary',
            []
        );

        /*
    |--------------------------------------------------------------------------
    | VALIDATE EXISTING IMAGE IDS
    |--------------------------------------------------------------------------
    */

        $galleryImageIds = $galleryImages
            ->pluck('id')
            ->map(function ($id) {
                return (int) $id;
            })
            ->all();

        foreach ($existingImageIds as $imageId) {

            if (!in_array($imageId, $galleryImageIds, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data gambar existing tidak valid.',
                ], 422);
            }
        }

        foreach ($deletedImageIds as $imageId) {

            if (!in_array($imageId, $galleryImageIds, true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data gambar yang akan dihapus tidak valid.',
                ], 422);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | CHECK CONFLICT EXISTING / DELETED
    |--------------------------------------------------------------------------
    */

        $conflictingImageIds = array_intersect(
            $existingImageIds,
            $deletedImageIds
        );

        if (!empty($conflictingImageIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat gambar yang ditandai untuk dipertahankan sekaligus dihapus.',
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | ACTIVE EXISTING IMAGES
    |--------------------------------------------------------------------------
    */

        $activeExistingImages = $galleryImages->filter(function ($image) use (
            $deletedImageIds
        ) {
            return !in_array(
                (int) $image->id,
                $deletedImageIds,
                true
            );
        });

        /*
    |--------------------------------------------------------------------------
    | IMAGE COUNT
    |--------------------------------------------------------------------------
    */

        $activeImageCount =
            $activeExistingImages->count() +
            count($images);

        if ($activeImageCount < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Galeri sampel harus memiliki minimal satu gambar.',
            ], 422);
        }

        if ($activeImageCount > 10) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah gambar maksimal 10.',
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | PRIMARY IMAGE
    |--------------------------------------------------------------------------
    |
    | Untuk sementara kita membaca primary dari:
    |
    | existing_image_primary_{id}
    | image_is_primary[index]
    |
    */

        $primaryExistingIds = [];

        foreach ($activeExistingImages as $image) {

            $fieldName =
                'existing_image_is_primary_' .
                $image->id;

            if (
                (int) $request->input($fieldName, 0) === 1
            ) {
                $primaryExistingIds[] = (int) $image->id;
            }
        }

        $primaryNewIndexes = [];

        foreach ($images as $index => $image) {

            if (
                isset($primaryFlags[$index]) &&
                (int) $primaryFlags[$index] === 1
            ) {
                $primaryNewIndexes[] = $index;
            }
        }

        $totalPrimary =
            count($primaryExistingIds) +
            count($primaryNewIndexes);

        if ($totalPrimary !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Harus terdapat tepat satu gambar utama.',
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | IMAGE HASH
    |--------------------------------------------------------------------------
    */

        $imageHashes = [];

        $seenHashes = [];

        foreach ($images as $index => $image) {

            $hash = hash_file(
                'sha256',
                $image->getRealPath()
            );

            /*
        |--------------------------------------------------------------------------
        | DUPLICATE DALAM UPLOAD YANG SAMA
        |--------------------------------------------------------------------------
        */

            if (isset($seenHashes[$hash])) {

                return response()->json([
                    'success' => false,
                    'message' =>
                    'Gambar "' .
                        $seenHashes[$hash] .
                        '" dan "' .
                        $image->getClientOriginalName() .
                        '" memiliki isi yang sama.',
                ], 422);
            }

            $seenHashes[$hash] =
                $image->getClientOriginalName();

            $imageHashes[$index] = $hash;
        }

        /*
    |--------------------------------------------------------------------------
    | EXISTING HASH
    |--------------------------------------------------------------------------
    |
    | Hanya gambar existing yang tetap dipertahankan
    | yang digunakan untuk pengecekan duplicate.
    |
    */

        $existingActiveHashes = $activeExistingImages
            ->pluck('image_hash')
            ->filter()
            ->all();

        foreach ($images as $index => $image) {

            if (
                in_array(
                    $imageHashes[$index],
                    $existingActiveHashes,
                    true
                )
            ) {
                return response()->json([
                    'success' => false,
                    'message' =>
                    'Gambar "' .
                        $image->getClientOriginalName() .
                        '" sudah ada pada galeri tersebut.',
                ], 422);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | IMAGE STORAGE PATH
    |--------------------------------------------------------------------------
    */

        $storedFiles = [];

        if (app()->environment('production')) {

            /*
        |--------------------------------------------------------------------------
        | Path production akan ditentukan kemudian.
        |--------------------------------------------------------------------------
        */

            $uploadPath = null;
        } else {

            $uploadPath =
                public_path('images/gallery-samples');
        }

        if (!$uploadPath) {

            return response()->json([
                'success' => false,
                'message' =>
                'Penyimpanan gambar untuk environment production belum dikonfigurasi.',
            ], 500);
        }

        if (!is_dir($uploadPath)) {

            if (!mkdir($uploadPath, 0755, true)) {

                return response()->json([
                    'success' => false,
                    'message' =>
                    'Folder penyimpanan gambar tidak dapat dibuat.',
                ], 500);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

        try {

            DB::beginTransaction();

            /*
        |--------------------------------------------------------------------------
        | ACTOR
        |--------------------------------------------------------------------------
        */

            $actor = Auth::user()->name;

            /*
        |--------------------------------------------------------------------------
        | DELETE EXISTING FILES
        |--------------------------------------------------------------------------
        */

            foreach ($galleryImages as $galleryImage) {

                if (
                    in_array(
                        (int) $galleryImage->id,
                        $deletedImageIds,
                        true
                    )
                ) {

                    $filePath =
                        public_path(
                            $galleryImage->image_path
                        );

                    if (file_exists($filePath)) {
                        @unlink($filePath);
                    }
                }
            }

            /*
        |--------------------------------------------------------------------------
        | DELETE EXISTING DATABASE RECORDS
        |--------------------------------------------------------------------------
        */

            if (!empty($deletedImageIds)) {

                DB::table('m_sample_galleries')
                    ->where('engine_id', $engineId)
                    ->where('category_id', $categoryId)
                    ->whereIn('id', $deletedImageIds)
                    ->delete();
            }

            /*
|--------------------------------------------------------------------------
| MOVE EXISTING GALLERY TO TARGET
|--------------------------------------------------------------------------
*/

            if (
                $targetEngineId !== (int) $engineId ||
                $targetCategoryId !== (int) $categoryId
            ) {

                DB::table('m_sample_galleries')
                    ->where('engine_id', $engineId)
                    ->where('category_id', $categoryId)
                    ->update([
                        'engine_id' =>
                        $targetEngineId,

                        'category_id' =>
                        $targetCategoryId,

                        'updated_by' =>
                        $actor,

                        'updated_at' =>
                        now(),
                    ]);
            }

            /*
        |--------------------------------------------------------------------------
        | RESET PRIMARY
        |--------------------------------------------------------------------------
        */

            DB::table('m_sample_galleries')
                ->where('engine_id', $targetEngineId)
                ->where('category_id', $targetCategoryId)
                ->update([
                    'is_primary' => false,
                    'updated_by' => $actor,
                    'updated_at' => now(),
                ]);

            /*
        |--------------------------------------------------------------------------
        | UPDATE EXISTING PRIMARY
        |--------------------------------------------------------------------------
        */

            if (!empty($primaryExistingIds)) {

                DB::table('m_sample_galleries')
                    ->where('id', $primaryExistingIds[0])
                    ->where('engine_id', $engineId)
                    ->where('category_id', $categoryId)
                    ->update([
                        'is_primary' => true,
                        'updated_by' => $actor,
                        'updated_at' => now(),
                    ]);
            }

            /*
        |--------------------------------------------------------------------------
        | NEXT SORT ORDER
        |--------------------------------------------------------------------------
        */

            $nextOrder = (int) DB::table(
                'm_sample_galleries'
            )
                ->where('engine_id', $targetEngineId)
                ->where('category_id', $targetCategoryId)
                ->max('sort_order');

            /*
        |--------------------------------------------------------------------------
        | INSERT NEW IMAGES
        |--------------------------------------------------------------------------
        */

            foreach ($images as $index => $image) {

                $extension =
                    strtolower(
                        $image->getClientOriginalExtension()
                    );

                $fileName =
                    'gallery_sample_' .
                    $targetEngineId  .
                    '_' .
                    $targetCategoryId  .
                    '_' .
                    uniqid() .
                    '.' .
                    $extension;

                $image->move(
                    $uploadPath,
                    $fileName
                );

                $storedFiles[] =
                    $uploadPath .
                    DIRECTORY_SEPARATOR .
                    $fileName;

                DB::table('m_sample_galleries')->insert([
                    'engine_id' =>
                    $targetEngineId,

                    'category_id' =>
                    $targetCategoryId,

                    'image_path' =>
                    'images/gallery-samples/' .
                        $fileName,

                    'image_name' =>
                    $image->getClientOriginalName(),

                    'image_hash' =>
                    $imageHashes[$index],

                    'is_primary' =>
                    in_array(
                        $index,
                        $primaryNewIndexes,
                        true
                    ),

                    'sort_order' =>
                    $nextOrder +
                        $index +
                        1,

                    'created_by' =>
                    $actor,

                    'created_at' =>
                    now(),

                    'updated_by' =>
                    $actor,

                    'updated_at' =>
                    now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' =>
                'Galeri sampel berhasil diperbarui.',
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            /*
        |--------------------------------------------------------------------------
        | DELETE NEWLY UPLOADED FILES
        |--------------------------------------------------------------------------
        */

            foreach ($storedFiles as $filePath) {

                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            Log::error(
                'Gagal memperbarui Galeri Sampel.',
                [
                    'error' =>
                    $e->getMessage(),

                    'trace' =>
                    $e->getTraceAsString(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' =>
                'Terjadi kesalahan saat memperbarui galeri sampel.',
            ], 500);
        }
    }

    public function details($engineId, $categoryId)
    {
        /*
    |--------------------------------------------------------------------------
    | ENGINE
    |--------------------------------------------------------------------------
    */

        $engine = DB::table('m_engines')
            ->where('id', $engineId)
            ->first();

        if (!$engine) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | CATEGORY
    |--------------------------------------------------------------------------
    */

        $category = DB::table('m_categories')
            ->where('id', $categoryId)
            ->first();

        if (!$category) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | GALLERY IMAGES
    |--------------------------------------------------------------------------
    */

        $galleryImages = DB::table('m_sample_galleries')
            ->where('engine_id', $engineId)
            ->where('category_id', $categoryId)
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($galleryImages->isEmpty()) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */

        return view(
            'admin.galery-sample.details',
            compact(
                'engine',
                'category',
                'galleryImages'
            )
        );
    }
}

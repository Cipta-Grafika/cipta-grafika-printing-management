<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DisplayCT extends Controller
{
    // public function index()
    // {
    //     $this->cleanTemporary();
    //     return view('admin.displays.index');
    // }

    public function index()
    {
        $this->cleanTemporary();

        $categories = DB::table('m_categories')
            ->orderBy('name')
            ->get();

        return view('admin.displays.index', compact('categories'));
    }

    public function create()
    {
        $data['categories'] = DB::table('m_categories')
            ->orderBy('name', 'asc')
            ->get()->toArray();
        return view('admin.displays.create')->with($data);
    }

    public function store(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'display_name' => [
                'required',
                'string',
                'max:255',
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:m_categories,id',
            ],

            'length' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'width' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'status' => [
                'required',
                'in:Active,Inactive',
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

        /*
    |--------------------------------------------------------------------------
    | IMAGE DATA
    |--------------------------------------------------------------------------
    */

        $images = $request->file('images', []);

        $primaryFlags = $request->input(
            'image_is_primary',
            []
        );

        /*
    |--------------------------------------------------------------------------
    | VALIDATE PRIMARY IMAGE
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

        /*
    |--------------------------------------------------------------------------
    | HARUS TEPAT SATU GAMBAR UTAMA
    |--------------------------------------------------------------------------
    */

        if (count($primaryIndexes) !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Harus terdapat tepat satu gambar utama.',
            ], 422);
        }

        $primaryIndex = $primaryIndexes[0];

        /*
    |--------------------------------------------------------------------------
    | CHECK DUPLICATE DISPLAY
    |--------------------------------------------------------------------------
    |
    | Duplikat berdasarkan:
    | display_name + length + width
    |
    */

        $displayName = trim(
            $validated['display_name']
        );

        $duplicateDisplay = DB::table(
            'm_display_products'
        )
            ->whereRaw(
                'LOWER(display_name) = ?',
                [
                    strtolower($displayName),
                ]
            )
            ->where(
                'length',
                $validated['length']
            )
            ->where(
                'width',
                $validated['width']
            )
            ->exists();

        if ($duplicateDisplay) {
            return response()->json([
                'success' => false,
                'message' => 'Display dengan nama dan ukuran tersebut sudah tersedia.',
            ], 422);
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
        | PRODUCTION
        |--------------------------------------------------------------------------
        | Path production akan ditentukan kemudian.
        |--------------------------------------------------------------------------
        */

            $uploadPath = null;
        } else {

            /*
        |--------------------------------------------------------------------------
        | LOCAL
        |--------------------------------------------------------------------------
        */

            $uploadPath = public_path(
                'images/display'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | PRODUCTION STORAGE BELUM DIKONFIGURASI
    |--------------------------------------------------------------------------
    */

        if (!$uploadPath) {
            return response()->json([
                'success' => false,
                'message' => 'Penyimpanan gambar untuk environment production belum dikonfigurasi.',
            ], 500);
        }

        /*
    |--------------------------------------------------------------------------
    | CREATE IMAGE DIRECTORY
    |--------------------------------------------------------------------------
    */

        if (!is_dir($uploadPath)) {
            if (!mkdir(
                $uploadPath,
                0755,
                true
            )) {
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
        | CREATED BY
        |--------------------------------------------------------------------------
        */

            $createdBy = Auth::user()->name;

            /*
        |--------------------------------------------------------------------------
        | INSERT DISPLAY PRODUCT
        |--------------------------------------------------------------------------
        */

            $displayProductId = DB::table(
                'm_display_products'
            )->insertGetId([
                'display_name' =>
                $displayName,

                'category_id' =>
                $validated['category_id'],

                'length' =>
                $validated['length'],

                'width' =>
                $validated['width'],

                'status' =>
                $validated['status'],

                'created_by' =>
                $createdBy,

                'created_at' =>
                now(),
            ]);

            /*
        |--------------------------------------------------------------------------
        | INSERT DISPLAY IMAGES
        |--------------------------------------------------------------------------
        */

            foreach (
                $images as $index => $image
            ) {

                /*
            |--------------------------------------------------------------------------
            | FILE EXTENSION
            |--------------------------------------------------------------------------
            */

                $extension = strtolower(
                    $image->getClientOriginalExtension()
                );

                /*
            |--------------------------------------------------------------------------
            | UNIQUE FILE NAME
            |--------------------------------------------------------------------------
            */

                $fileName =
                    'display_' .
                    $displayProductId .
                    '_' .
                    uniqid() .
                    '.' .
                    $extension;

                /*
            |--------------------------------------------------------------------------
            | MOVE IMAGE
            |--------------------------------------------------------------------------
            */

                $image->move(
                    $uploadPath,
                    $fileName
                );

                /*
            |--------------------------------------------------------------------------
            | TRACK STORED FILE
            |--------------------------------------------------------------------------
            */

                $storedFiles[] =
                    $uploadPath .
                    DIRECTORY_SEPARATOR .
                    $fileName;

                /*
            |--------------------------------------------------------------------------
            | RELATIVE IMAGE PATH
            |--------------------------------------------------------------------------
            */

                $imagePath =
                    'images/display/' .
                    $fileName;

                /*
            |--------------------------------------------------------------------------
            | PRIMARY IMAGE
            |--------------------------------------------------------------------------
            */

                $isPrimary =
                    $index === $primaryIndex;

                /*
            |--------------------------------------------------------------------------
            | INSERT IMAGE RECORD
            |--------------------------------------------------------------------------
            */

                DB::table(
                    'm_display_product_images'
                )->insert([
                    'display_product_id' =>
                    $displayProductId,

                    'image_path' =>
                    $imagePath,

                    'image_name' =>
                    $image->getClientOriginalName(),

                    'is_primary' =>
                    $isPrimary,

                    'created_by' =>
                    $createdBy,

                    'created_at' =>
                    now(),
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | COMMIT
        |--------------------------------------------------------------------------
        */

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Display berhasil disimpan.',
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

            /*
        |--------------------------------------------------------------------------
        | LOG ERROR
        |--------------------------------------------------------------------------
        */

            Log::error(
                'Gagal menyimpan Display Product.',
                [
                    'error' =>
                    $e->getMessage(),

                    'trace' =>
                    $e->getTraceAsString(),
                ]
            );

            /*
        |--------------------------------------------------------------------------
        | RESPONSE ERROR
        |--------------------------------------------------------------------------
        */

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan Display.',
            ], 500);
        }
    }

    // public function data(Request $request)
    // {
    //     /*
    // |--------------------------------------------------------------------------
    // | TOTAL SEMUA DATA
    // |--------------------------------------------------------------------------
    // */
    //     $totalRecords = DB::table('m_display_products')->count();

    //     /*
    // |--------------------------------------------------------------------------
    // | QUERY UTAMA
    // |--------------------------------------------------------------------------
    // */
    //     $query = DB::table('m_display_products')
    //         ->select(
    //             'm_display_products.id',
    //             'm_display_products.display_name',
    //             'm_display_products.length',
    //             'm_display_products.width',
    //             'm_display_products.status'
    //         );

    //     /*
    // |--------------------------------------------------------------------------
    // | SEARCH
    // |--------------------------------------------------------------------------
    // */
    //     $search = $request->input('search.value');

    //     if (!empty($search)) {
    //         $query->where(function ($q) use ($search) {

    //             $searchValue = strtolower($search);

    //             $q->whereRaw(
    //                 'LOWER(m_display_products.display_name) LIKE ?',
    //                 ['%' . $searchValue . '%']
    //             )
    //                 ->orWhereRaw(
    //                     'LOWER(m_display_products.status) LIKE ?',
    //                     ['%' . $searchValue . '%']
    //                 );
    //         });
    //     }

    //     /*
    // |--------------------------------------------------------------------------
    // | TOTAL DATA SETELAH FILTER
    // |--------------------------------------------------------------------------
    // */
    //     $totalFiltered = $query->count();

    //     /*
    // |--------------------------------------------------------------------------
    // | PAGINATION
    // |--------------------------------------------------------------------------
    // */
    //     $start = (int) $request->input('start', 0);
    //     $length = (int) $request->input('length', 10);

    //     /*
    // |--------------------------------------------------------------------------
    // | AMBIL DATA
    // |--------------------------------------------------------------------------
    // */
    //     $displayProducts = $query
    //         ->orderByRaw(
    //             "CASE WHEN m_display_products.status = 'Active' THEN 0 ELSE 1 END"
    //         )
    //         ->orderBy(
    //             'm_display_products.display_name',
    //             'asc'
    //         )
    //         ->offset($start)
    //         ->limit($length)
    //         ->get();

    //     /*
    // |--------------------------------------------------------------------------
    // | RESPONSE DATATABLES
    // |--------------------------------------------------------------------------
    // */
    //     return response()->json([
    //         'draw' => (int) $request->input('draw'),
    //         'recordsTotal' => $totalRecords,
    //         'recordsFiltered' => $totalFiltered,
    //         'data' => $displayProducts,
    //     ]);
    // }

    public function data(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'search'      => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer'],
            'status'      => ['nullable', 'in:Active,Inactive'],
            'page'        => ['nullable', 'integer', 'min:1'],
        ]);

        /*
    |--------------------------------------------------------------------------
    | COVER IMAGE (SUBQUERY)
    |--------------------------------------------------------------------------
    |
    | Gambar utama lebih dulu. Kalau tidak ada gambar utama,
    | pakai gambar dengan id terkecil.
    |
    */

        $cover = DB::table('m_display_product_images as i')
            ->select('i.image_path')
            ->whereColumn('i.display_product_id', 'p.id')
            ->orderByDesc('i.is_primary')
            ->orderBy('i.id')
            ->limit(1);

        /*
    |--------------------------------------------------------------------------
    | JUMLAH GAMBAR (SUBQUERY)
    |--------------------------------------------------------------------------
    */

        $imageCount = DB::table('m_display_product_images as ic')
            ->selectRaw('COUNT(*)')
            ->whereColumn('ic.display_product_id', 'p.id');

        /*
    |--------------------------------------------------------------------------
    | QUERY UTAMA
    |--------------------------------------------------------------------------
    */

        $query = DB::table('m_display_products as p')
            ->join('m_categories as c', 'c.id', '=', 'p.category_id')
            ->select(
                'p.id',
                'p.display_name',
                'p.length',
                'p.width',
                'p.status',
                'c.name as category_name'
            )
            ->selectSub($cover, 'cover_path')
            ->selectSub($imageCount, 'image_count');

        /*
    |--------------------------------------------------------------------------
    | FILTER
    |--------------------------------------------------------------------------
    */

        if (!empty($validated['category_id'])) {
            $query->where('p.category_id', $validated['category_id']);
        }

        if (!empty($validated['status'])) {
            $query->where('p.status', $validated['status']);
        }

        /*
    |--------------------------------------------------------------------------
    | SEARCH (NAMA DISPLAY ATAU NAMA KATEGORI)
    |--------------------------------------------------------------------------
    */

        if (!empty($validated['search'])) {

            $keyword = '%' . str_replace(
                ['\\', '%', '_'],
                ['\\\\', '\%', '\_'],
                trim($validated['search'])
            ) . '%';

            $query->where(function ($q) use ($keyword) {
                $q->whereRaw('p.display_name ILIKE ?', [$keyword])
                    ->orWhereRaw('c.name ILIKE ?', [$keyword]);
            });
        }

        /*
    |--------------------------------------------------------------------------
    | ORDER + PAGINATION
    |--------------------------------------------------------------------------
    */

        $paginator = $query
            ->orderByRaw("CASE WHEN p.status = 'Active' THEN 0 ELSE 1 END")
            ->orderBy('p.display_name', 'asc')
            ->orderBy('p.id', 'asc')
            ->paginate(12);

        /*
    |--------------------------------------------------------------------------
    | FORMAT RESPONSE
    |--------------------------------------------------------------------------
    */

        $items = $paginator->getCollection()->map(function ($row) {
            return [
                'id'            => (int) $row->id,
                'display_name'  => $row->display_name,
                'category_name' => $row->category_name,
                'length'        => (float) $row->length,
                'width'         => (float) $row->width,
                'status'        => $row->status,
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


    public function destroy($id)
    {
        /*
    |--------------------------------------------------------------------------
    | CARI DATA DISPLAY
    |--------------------------------------------------------------------------
    */
        $displayProduct = DB::table('m_display_products')
            ->where('id', $id)
            ->first();

        if (!$displayProduct) {
            return response()->json([
                'success' => false,
                'message' => 'Data Display tidak ditemukan.'
            ], 404);
        }

        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA GAMBAR
    |--------------------------------------------------------------------------
    */
        $displayImages = DB::table('m_display_product_images')
            ->where('display_product_id', $id)
            ->get();

        try {

            /*
        |--------------------------------------------------------------------------
        | HAPUS FILE GAMBAR FISIK
        |--------------------------------------------------------------------------
        */
            foreach ($displayImages as $image) {

                $filePath = public_path(
                    $image->image_path
                );

                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            /*
        |--------------------------------------------------------------------------
        | HAPUS DISPLAY
        |--------------------------------------------------------------------------
        |
        | m_display_product_images akan ikut terhapus
        | melalui FK ON DELETE CASCADE.
        |
        */
            DB::table('m_display_products')
                ->where('id', $id)
                ->delete();

            /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */
            return response()->json([
                'success' => true,
                'message' => 'Display berhasil dihapus.'
            ]);
        } catch (\Throwable $e) {

            /*
        |--------------------------------------------------------------------------
        | RESPONSE ERROR
        |--------------------------------------------------------------------------
        */
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus Display.'
            ], 500);
        }
    }

    public function image($id)
    {
        /*
    |--------------------------------------------------------------------------
    | CARI DATA GAMBAR
    |--------------------------------------------------------------------------
    */
        $displayImage = DB::table('m_display_product_images')
            ->where('id', $id)
            ->first();

        if (!$displayImage) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | SOURCE PATH
    |--------------------------------------------------------------------------
    */
        if (app()->environment('production')) {

            /*
        |--------------------------------------------------------------------------
        | PRODUCTION
        |--------------------------------------------------------------------------
        | Path production akan ditentukan kemudian.
        |--------------------------------------------------------------------------
        */

            $sourcePath = null;
        } else {

            /*
        |--------------------------------------------------------------------------
        | LOCAL
        |--------------------------------------------------------------------------
        */

            $sourcePath = public_path(
                'images/display'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | VALIDASI SOURCE PATH
    |--------------------------------------------------------------------------
    */
        if (!$sourcePath) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | FILE ASLI
    |--------------------------------------------------------------------------
    */
        $sourceFile = $sourcePath
            . DIRECTORY_SEPARATOR
            . basename($displayImage->image_path);

        if (!file_exists($sourceFile)) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | SESSION KEY
    |--------------------------------------------------------------------------
    */
        $sessionKey = hash(
            'sha256',
            session()->getId()
        );

        /*
    |--------------------------------------------------------------------------
    | TEMPORARY DIRECTORY
    |--------------------------------------------------------------------------
    */
        $temporaryPath = public_path(
            'temporary-files/display/' . $sessionKey
        );

        if (!is_dir($temporaryPath)) {

            if (!mkdir($temporaryPath, 0755, true)) {
                abort(500);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | NAMA FILE TEMPORARY
    |--------------------------------------------------------------------------
    */
        $temporaryFileName =
            'display_' .
            $displayImage->id .
            '_' .
            basename($displayImage->image_path);

        $temporaryFilePath =
            $temporaryPath .
            DIRECTORY_SEPARATOR .
            $temporaryFileName;

        /*
    |--------------------------------------------------------------------------
    | COPY KE TEMPORARY
    |--------------------------------------------------------------------------
    */
        if (!file_exists($temporaryFilePath)) {

            if (!copy(
                $sourceFile,
                $temporaryFilePath
            )) {
                abort(500);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | RETURN FILE
    |--------------------------------------------------------------------------
    */
        return response()->file(
            $temporaryFilePath
        );
    }

    public function cleanTemporary()
    {
        /*
    |--------------------------------------------------------------------------
    | SESSION KEY
    |--------------------------------------------------------------------------
    */
        $sessionKey = hash(
            'sha256',
            session()->getId()
        );

        /*
    |--------------------------------------------------------------------------
    | TEMPORARY DIRECTORY
    |--------------------------------------------------------------------------
    */
        $temporaryPath = public_path(
            'temporary-files/display/' . $sessionKey
        );

        /*
    |--------------------------------------------------------------------------
    | JIKA FOLDER BELUM ADA
    |--------------------------------------------------------------------------
    */
        if (!is_dir($temporaryPath)) {

            return response()->json([
                'success' => true,
                'message' => 'Temporary file sudah bersih.'
            ]);
        }

        /*
    |--------------------------------------------------------------------------
    | HAPUS FILE TEMPORARY
    |--------------------------------------------------------------------------
    */
        $files = glob(
            $temporaryPath . DIRECTORY_SEPARATOR . '*'
        );

        foreach ($files as $file) {

            if (is_file($file)) {
                @unlink($file);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | HAPUS FOLDER SESSION
    |--------------------------------------------------------------------------
    */
        @rmdir($temporaryPath);

        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */
        return response()->json([
            'success' => true,
            'message' => 'Temporary file berhasil dibersihkan.'
        ]);
    }

    public function details($id)
    {
        /*
    |--------------------------------------------------------------------------
    | DECODE ID
    |--------------------------------------------------------------------------
    */

        $displayProductId = base64_decode($id, true);

        if (!$displayProductId || !is_numeric($displayProductId)) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | DATA DISPLAY
    |--------------------------------------------------------------------------
    */

        $displayProduct = DB::table('m_display_products')
            ->leftJoin(
                'm_categories',
                'm_categories.id',
                '=',
                'm_display_products.category_id'
            )
            ->select(
                'm_display_products.id',
                'm_display_products.display_name',
                'm_display_products.category_id',
                'm_categories.name as category_name',
                'm_display_products.length',
                'm_display_products.width',
                'm_display_products.status',
                'm_display_products.created_by',
                'm_display_products.created_at',
                'm_display_products.updated_by',
                'm_display_products.updated_at'
            )
            ->where(
                'm_display_products.id',
                $displayProductId
            )
            ->first();

        if (!$displayProduct) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | DATA GAMBAR
    |--------------------------------------------------------------------------
    */

        $displayImages = DB::table(
            'm_display_product_images'
        )
            ->where(
                'display_product_id',
                $displayProductId
            )
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */

        return view(
            'admin.displays.details',
            compact(
                'displayProduct',
                'displayImages'
            )
        );
    }

    public function edit($id)
    {
        /*
    |--------------------------------------------------------------------------
    | DECODE ID
    |--------------------------------------------------------------------------
    */

        $displayProductId = base64_decode($id, true);

        if (!$displayProductId || !is_numeric($displayProductId)) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | DATA DISPLAY
    |--------------------------------------------------------------------------
    */

        $displayProduct = DB::table('m_display_products')
            ->leftJoin(
                'm_categories',
                'm_categories.id',
                '=',
                'm_display_products.category_id'
            )
            ->select(
                'm_display_products.id',
                'm_display_products.display_name',
                'm_display_products.category_id',
                'm_categories.name as category_name',
                'm_display_products.length',
                'm_display_products.width',
                'm_display_products.status',
                'm_display_products.created_by',
                'm_display_products.created_at',
                'm_display_products.updated_by',
                'm_display_products.updated_at'
            )
            ->where(
                'm_display_products.id',
                $displayProductId
            )
            ->first();

        if (!$displayProduct) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | DATA KATEGORI
    |--------------------------------------------------------------------------
    */

        $categories = DB::table('m_categories')
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | DATA GAMBAR
    |--------------------------------------------------------------------------
    */

        $displayImages = DB::table(
            'm_display_product_images'
        )
            ->where(
                'display_product_id',
                $displayProductId
            )
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */

        return view(
            'admin.displays.edit',
            compact(
                'displayProduct',
                'categories',
                'displayImages'
            )
        );
    }

    public function update(Request $request, $id)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'display_name' => [
                'required',
                'string',
                'max:255',
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:m_categories,id',
            ],

            'length' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'width' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'status' => [
                'required',
                'in:Active,Inactive',
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

            'primary_existing_image_id' => [
                'nullable',
                'integer',
            ],

            'primary_new_image_index' => [
                'nullable',
                'integer',
                'min:0',
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
    | DISPLAY ID
    |--------------------------------------------------------------------------
    */

        $displayProductId = $id;

        if (
            !is_numeric($displayProductId) ||
            (int) $displayProductId <= 0
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Data Display tidak valid.',
            ], 422);
        }

        $displayProductId = (int) $displayProductId;

        /*
    |--------------------------------------------------------------------------
    | GET DISPLAY
    |--------------------------------------------------------------------------
    */

        $displayProduct = DB::table(
            'm_display_products'
        )
            ->where(
                'id',
                $displayProductId
            )
            ->first();

        if (!$displayProduct) {
            return response()->json([
                'success' => false,
                'message' => 'Data Display tidak ditemukan.',
            ], 404);
        }

        /*
    |--------------------------------------------------------------------------
    | DISPLAY NAME
    |--------------------------------------------------------------------------
    */

        $displayName = trim(
            $validated['display_name']
        );

        /*
    |--------------------------------------------------------------------------
    | CHECK DUPLICATE DISPLAY
    |--------------------------------------------------------------------------
    |
    | Duplikat berdasarkan:
    | display_name + length + width
    |
    | ID Display yang sedang diedit dikecualikan.
    |
    */

        $duplicateDisplay = DB::table(
            'm_display_products'
        )
            ->where(
                'id',
                '!=',
                $displayProductId
            )
            ->whereRaw(
                'LOWER(display_name) = ?',
                [
                    strtolower($displayName),
                ]
            )
            ->where(
                'length',
                $validated['length']
            )
            ->where(
                'width',
                $validated['width']
            )
            ->exists();

        if ($duplicateDisplay) {
            return response()->json([
                'success' => false,
                'message' => 'Display dengan nama dan ukuran tersebut sudah tersedia.',
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | IMAGE DATA
    |--------------------------------------------------------------------------
    */

        $images = $request->file(
            'images',
            []
        );

        $existingImageIds = array_map(
            'intval',
            $request->input(
                'existing_image_ids',
                []
            )
        );

        $deletedImageIds = array_map(
            'intval',
            $request->input(
                'deleted_image_ids',
                []
            )
        );

        $primaryExistingImageId =
            $request->input(
                'primary_existing_image_id'
            );

        $primaryNewImageIndex =
            $request->input(
                'primary_new_image_index'
            );

        /*
    |--------------------------------------------------------------------------
    | GET CURRENT IMAGES
    |--------------------------------------------------------------------------
    */

        $currentImages = DB::table(
            'm_display_product_images'
        )
            ->where(
                'display_product_id',
                $displayProductId
            )
            ->get();

        $currentImageIds = $currentImages
            ->pluck('id')
            ->map(
                fn($imageId) => (int) $imageId
            )
            ->values()
            ->all();

        /*
    |--------------------------------------------------------------------------
    | NORMALIZE IMAGE IDS
    |--------------------------------------------------------------------------
    */

        $existingImageIds = array_values(
            array_unique(
                $existingImageIds
            )
        );

        $deletedImageIds = array_values(
            array_unique(
                $deletedImageIds
            )
        );

        /*
    |--------------------------------------------------------------------------
    | VALIDATE EXISTING IMAGE IDS
    |--------------------------------------------------------------------------
    |
    | Semua ID yang dikirim harus benar-benar milik
    | Display yang sedang diedit.
    |
    */

        $submittedExistingIds = array_values(
            array_unique(
                array_merge(
                    $existingImageIds,
                    $deletedImageIds
                )
            )
        );

        sort($currentImageIds);
        sort($submittedExistingIds);

        if (
            $currentImageIds !==
            $submittedExistingIds
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Data gambar Display tidak valid atau sudah berubah.',
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | VALIDATE IMAGE PARTITION
    |--------------------------------------------------------------------------
    |
    | Satu gambar existing tidak boleh sekaligus
    | berada di daftar keep dan delete.
    |
    */

        $duplicateImageIds = array_intersect(
            $existingImageIds,
            $deletedImageIds
        );

        if (count($duplicateImageIds) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Terdapat data gambar yang tidak valid.',
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | VALIDATE PRIMARY EXISTING IMAGE
    |--------------------------------------------------------------------------
    */

        if ($primaryExistingImageId !== null) {
            $primaryExistingImageId =
                (int) $primaryExistingImageId;

            if (
                !in_array(
                    $primaryExistingImageId,
                    $existingImageIds,
                    true
                )
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gambar utama yang dipilih tidak valid.',
                ], 422);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | VALIDATE PRIMARY NEW IMAGE
    |--------------------------------------------------------------------------
    */

        if ($primaryNewImageIndex !== null) {
            $primaryNewImageIndex =
                (int) $primaryNewImageIndex;

            if (
                !isset(
                    $images[$primaryNewImageIndex]
                )
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gambar utama baru yang dipilih tidak valid.',
                ], 422);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | PRIMARY IMAGE CONFLICT
    |--------------------------------------------------------------------------
    |
    | Tidak boleh ada primary existing dan primary new
    | secara bersamaan.
    |
    */

        if (
            $primaryExistingImageId !== null &&
            $primaryNewImageIndex !== null
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Gambar utama tidak valid.',
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | VALIDATE PRIMARY IMAGE
    |--------------------------------------------------------------------------
    |
    | Harus ada tepat satu gambar utama pada hasil akhir.
    |
    */

        $hasPrimaryExisting =
            $primaryExistingImageId !== null;

        $hasPrimaryNew =
            $primaryNewImageIndex !== null;

        if (
            !$hasPrimaryExisting &&
            !$hasPrimaryNew
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Harus terdapat tepat satu gambar utama.',
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | FINAL IMAGE COUNT
    |--------------------------------------------------------------------------
    */

        $finalImageCount =
            count($existingImageIds) +
            count($images);

        if ($finalImageCount < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Minimal harus terdapat satu gambar Display.',
            ], 422);
        }

        if ($finalImageCount > 10) {
            return response()->json([
                'success' => false,
                'message' => 'Maksimal 10 gambar untuk satu Display.',
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | IMAGE PRIMARY FLAGS
    |--------------------------------------------------------------------------
    */

        $primaryFlags = $request->input(
            'image_is_primary',
            []
        );

        /*
    |--------------------------------------------------------------------------
    | VALIDATE NEW IMAGE PRIMARY INDEX
    |--------------------------------------------------------------------------
    */

        if (
            count($images) > 0 &&
            count($primaryFlags) > 0
        ) {
            foreach (
                $primaryFlags as $index => $flag
            ) {
                if (
                    !isset($images[$index])
                ) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data gambar baru tidak valid.',
                    ], 422);
                }
            }
        }

        /*
    |--------------------------------------------------------------------------
    | STORAGE PATH
    |--------------------------------------------------------------------------
    */

        $storedFiles = [];

        if (app()->environment('production')) {

            /*
        |--------------------------------------------------------------------------
        | PRODUCTION
        |--------------------------------------------------------------------------
        | Path production akan ditentukan kemudian.
        |--------------------------------------------------------------------------
        */

            $uploadPath = null;
        } else {

            /*
        |--------------------------------------------------------------------------
        | LOCAL
        |--------------------------------------------------------------------------
        */

            $uploadPath = public_path(
                'images/display'
            );
        }

        /*
    |--------------------------------------------------------------------------
    | PRODUCTION STORAGE BELUM DIKONFIGURASI
    |--------------------------------------------------------------------------
    */

        if (!$uploadPath) {
            return response()->json([
                'success' => false,
                'message' => 'Penyimpanan gambar untuk environment production belum dikonfigurasi.',
            ], 500);
        }

        /*
    |--------------------------------------------------------------------------
    | CREATE IMAGE DIRECTORY
    |--------------------------------------------------------------------------
    */

        if (!is_dir($uploadPath)) {
            if (!mkdir(
                $uploadPath,
                0755,
                true
            )) {
                return response()->json([
                    'success' => false,
                    'message' => 'Folder penyimpanan gambar tidak dapat dibuat.',
                ], 500);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | OLD FILES TO DELETE
    |--------------------------------------------------------------------------
    */

        $deletedFilePaths = [];

        foreach ($currentImages as $currentImage) {

            if (
                in_array(
                    (int) $currentImage->id,
                    $deletedImageIds,
                    true
                )
            ) {
                $deletedFilePaths[] =
                    public_path(
                        $currentImage->image_path
                    );
            }
        }

        try {

            DB::beginTransaction();

            /*
        |--------------------------------------------------------------------------
        | UPDATED BY
        |--------------------------------------------------------------------------
        */

            $updatedBy = Auth::user()->name;

            /*
        |--------------------------------------------------------------------------
        | UPDATE DISPLAY PRODUCT
        |--------------------------------------------------------------------------
        */

            DB::table(
                'm_display_products'
            )
                ->where(
                    'id',
                    $displayProductId
                )
                ->update([
                    'display_name' =>
                    $displayName,

                    'category_id' =>
                    $validated['category_id'],

                    'length' =>
                    $validated['length'],

                    'width' =>
                    $validated['width'],

                    'status' =>
                    $validated['status'],

                    'updated_by' =>
                    $updatedBy,

                    'updated_at' =>
                    now(),
                ]);

            /*
        |--------------------------------------------------------------------------
        | RESET PRIMARY IMAGE
        |--------------------------------------------------------------------------
        */

            DB::table(
                'm_display_product_images'
            )
                ->where(
                    'display_product_id',
                    $displayProductId
                )
                ->update([
                    'is_primary' => false,
                    'updated_by' =>
                    $updatedBy,
                    'updated_at' =>
                    now(),
                ]);

            /*
        |--------------------------------------------------------------------------
        | DELETE OLD IMAGE RECORDS
        |--------------------------------------------------------------------------
        */

            if (
                count($deletedImageIds) > 0
            ) {

                DB::table(
                    'm_display_product_images'
                )
                    ->where(
                        'display_product_id',
                        $displayProductId
                    )
                    ->whereIn(
                        'id',
                        $deletedImageIds
                    )
                    ->delete();
            }

            /*
        |--------------------------------------------------------------------------
        | SET PRIMARY EXISTING IMAGE
        |--------------------------------------------------------------------------
        */

            if (
                $primaryExistingImageId !== null
            ) {

                DB::table(
                    'm_display_product_images'
                )
                    ->where(
                        'id',
                        $primaryExistingImageId
                    )
                    ->where(
                        'display_product_id',
                        $displayProductId
                    )
                    ->update([
                        'is_primary' => true,
                        'updated_by' =>
                        $updatedBy,
                        'updated_at' =>
                        now(),
                    ]);
            }

            /*
        |--------------------------------------------------------------------------
        | INSERT NEW IMAGES
        |--------------------------------------------------------------------------
        */

            foreach (
                $images as $index => $image
            ) {

                /*
            |--------------------------------------------------------------------------
            | FILE EXTENSION
            |--------------------------------------------------------------------------
            */

                $extension = strtolower(
                    $image->getClientOriginalExtension()
                );

                /*
            |--------------------------------------------------------------------------
            | UNIQUE FILE NAME
            |--------------------------------------------------------------------------
            */

                $fileName =
                    'display_' .
                    $displayProductId .
                    '_' .
                    uniqid() .
                    '.' .
                    $extension;

                /*
            |--------------------------------------------------------------------------
            | MOVE IMAGE
            |--------------------------------------------------------------------------
            */

                $image->move(
                    $uploadPath,
                    $fileName
                );

                /*
            |--------------------------------------------------------------------------
            | TRACK STORED FILE
            |--------------------------------------------------------------------------
            */

                $storedFiles[] =
                    $uploadPath .
                    DIRECTORY_SEPARATOR .
                    $fileName;

                /*
            |--------------------------------------------------------------------------
            | RELATIVE IMAGE PATH
            |--------------------------------------------------------------------------
            */

                $imagePath =
                    'images/display/' .
                    $fileName;

                /*
            |--------------------------------------------------------------------------
            | PRIMARY IMAGE
            |--------------------------------------------------------------------------
            */

                $isPrimary =
                    $primaryNewImageIndex !== null &&
                    $index === $primaryNewImageIndex;

                /*
            |--------------------------------------------------------------------------
            | INSERT IMAGE RECORD
            |--------------------------------------------------------------------------
            */

                DB::table(
                    'm_display_product_images'
                )->insert([
                    'display_product_id' =>
                    $displayProductId,

                    'image_path' =>
                    $imagePath,

                    'image_name' =>
                    $image->getClientOriginalName(),

                    'is_primary' =>
                    $isPrimary,

                    'created_by' =>
                    $updatedBy,

                    'created_at' =>
                    now(),
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | FINAL PRIMARY VALIDATION
        |--------------------------------------------------------------------------
        */

            $primaryCount = DB::table(
                'm_display_product_images'
            )
                ->where(
                    'display_product_id',
                    $displayProductId
                )
                ->where(
                    'is_primary',
                    true
                )
                ->count();

            if ($primaryCount !== 1) {
                throw new \RuntimeException(
                    'Jumlah gambar utama tidak valid.'
                );
            }

            /*
        |--------------------------------------------------------------------------
        | COMMIT
        |--------------------------------------------------------------------------
        */

            DB::commit();

            /*
        |--------------------------------------------------------------------------
        | DELETE OLD PHYSICAL FILES
        |--------------------------------------------------------------------------
        |
        | File lama baru dihapus setelah database berhasil commit.
        |
        */

            foreach (
                $deletedFilePaths as $filePath
            ) {
                if (file_exists($filePath)) {
                    if (!@unlink($filePath)) {
                        Log::warning(
                            'Gagal menghapus file gambar Display lama.',
                            [
                                'file_path' =>
                                $filePath,

                                'display_product_id' =>
                                $displayProductId,
                            ]
                        );
                    }
                }
            }

            /*
        |--------------------------------------------------------------------------
        | SUCCESS RESPONSE
        |--------------------------------------------------------------------------
        */

            return response()->json([
                'success' => true,
                'message' => 'Display berhasil diperbarui.',
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
        | DELETE NEWLY UPLOADED FILES
        |--------------------------------------------------------------------------
        */

            foreach (
                $storedFiles as $filePath
            ) {
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }

            /*
        |--------------------------------------------------------------------------
        | LOG ERROR
        |--------------------------------------------------------------------------
        */

            Log::error(
                'Gagal memperbarui Display Product.',
                [
                    'display_product_id' =>
                    $displayProductId,

                    'error' =>
                    $e->getMessage(),

                    'trace' =>
                    $e->getTraceAsString(),
                ]
            );

            /*
        |--------------------------------------------------------------------------
        | RESPONSE ERROR
        |--------------------------------------------------------------------------
        */

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui Display.',
            ], 500);
        }
    }
}

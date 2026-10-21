<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\admin\EstimationCT;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CatalogCT extends Controller
{
    public function index()
    {
        return view('user.index', [
            'products' => $this->landingProducts(),
        ]);
    }

    public function data()
    {
        return response()->json([
            'success' => true,
            'data'    => $this->landingProducts(),
        ]);
    }

    private function landingProducts()
    {
        $limit = 8;

        /*
    |--------------------------------------------------------------------------
    | 1. PRODUK DISPLAY
    |--------------------------------------------------------------------------
    */

        $displayCover = DB::table('m_display_product_images as i')
            ->select('i.id')
            ->whereColumn('i.display_product_id', 'p.id')
            ->orderByDesc('i.is_primary')
            ->orderBy('i.id')
            ->limit(1);

        $displayRows = DB::table('m_display_products as p')
            ->join('m_categories as c', 'c.id', '=', 'p.category_id')
            ->leftJoin(
                'm_display_product_configurations as pc',
                'pc.display_product_id',
                '=',
                'p.id'
            )
            ->leftJoin(
                'm_engines as e',
                'e.id',
                '=',
                'pc.engine_id'
            )
            ->where('p.status', 'Active')
            ->select(
                'p.id',
                'p.display_name',
                'p.length',
                'p.width',
                'p.package_qty',
                'p.package_unit',
                'c.name as category_name',
                'e.name as engine_name'
            )
            ->selectSub($displayCover, 'cover_image_id')
            ->orderBy('p.display_name', 'asc')
            ->orderBy('p.id', 'asc')
            ->limit($limit)
            ->get();

        $displayItems = $displayRows->map(function ($row) {
            $length = (float) $row->length;
            $width = (float) $row->width;
            $desc = $row->category_name;
            if (!empty($row->package_qty) && !empty($row->package_unit)) {
                $desc .= ' · ' . (int) $row->package_qty . ' pcs/' . $row->package_unit;
            } elseif ($width > 0 && $length > 0) {
                $desc .= ' · Ukuran ' . $width . ' × ' . $length;
            }

            return [
                'type'          => 'display',
                'id'            => (int) $row->id,
                'title'         => $row->display_name,
                'category_name' => $row->category_name,
                'engine_name'   => $row->engine_name,
                'length'        => $length,
                'width'         => $width,
                'desc'          => $desc,
                'cover_url'     => $row->cover_image_id
                    ? route('user_display_image', $row->cover_image_id)
                    : null,
                'url'           => route('user_product_details', [
                    'type' => 'display',
                    'id'   => (int) $row->id,
                ]),
            ];
        });

        /*
    |--------------------------------------------------------------------------
    | 2. KATEGORI DARI GALERI SAMPEL
    |--------------------------------------------------------------------------
    */

        $galleryCover = DB::table('m_sample_galleries as g2')
            ->select('g2.id')
            ->whereColumn('g2.category_id', 'g.category_id')
            ->whereColumn('g2.engine_id', 'g.engine_id')
            ->orderByDesc('g2.is_primary')
            ->orderBy('g2.sort_order')
            ->orderBy('g2.id')
            ->limit(1);

        $galleryRows = DB::table('m_sample_galleries as g')
            ->join('m_engines as e', 'e.id', '=', 'g.engine_id')
            ->join('m_categories as c', 'c.id', '=', 'g.category_id')
            ->where('e.status', 'Active')
            ->select(
                'g.engine_id',
                'g.category_id',
                'e.name as engine_name',
                'c.name as category_name'
            )
            ->selectSub($galleryCover, 'cover_image_id')
            ->groupBy(
                'g.engine_id',
                'g.category_id',
                'e.name',
                'c.name'
            )
            ->orderBy('c.name', 'asc')
            ->orderBy('e.name', 'asc')
            ->limit($limit)
            ->get();

        $galleryItems = $galleryRows->map(function ($row) {
            return [
                'type'          => 'gallery',
                'id'            => (int) $row->category_id,
                'engine_id'     => (int) $row->engine_id,
                'title'         => $row->category_name,
                'category_name' => $row->category_name,
                'engine_name'   => $row->engine_name,
                'length'        => null,
                'width'         => null,
                'desc'          => 'Contoh hasil cetak kategori ' . $row->category_name .
                    ' dengan ' . $row->engine_name . '.',
                'cover_url'     => $row->cover_image_id
                    ? route('user_gallery_image', $row->cover_image_id)
                    : null,
                'url'           => route('user_product_details', [
                    'type'        => 'gallery',
                    'engine_id'   => (int) $row->engine_id,
                    'category_id' => (int) $row->category_id,
                ]),
            ];
        });

        /*
    |--------------------------------------------------------------------------
    | 3. GABUNGKAN, URUTKAN A-Z, BATASI JUMLAH
    |--------------------------------------------------------------------------
    */

        return $displayItems
            ->concat($galleryItems)
            ->sortBy(function ($item) {
                return mb_strtolower($item['title'] . ' ' . ($item['engine_name'] ?? ''));
            })
            ->take($limit)
            ->values();
    }


    /*
|--------------------------------------------------------------------------
| GAMBAR DISPLAY (PUBLIK)
|--------------------------------------------------------------------------
|
| Hanya disajikan kalau display-nya Active.
|
*/

    public function displayImage($id)
    {
        $image = DB::table('m_display_product_images as i')
            ->join('m_display_products as p', 'p.id', '=', 'i.display_product_id')
            ->where('i.id', $id)
            ->where('p.status', 'Active')
            ->select('i.id', 'i.image_path')
            ->first();

        return $this->serveTemporaryImage(
            $image,
            'images/display',
            'temporary-files/display',
            'display_'
        );
    }


    /*
|--------------------------------------------------------------------------
| GAMBAR GALERI SAMPEL (PUBLIK)
|--------------------------------------------------------------------------
|
| Hanya disajikan kalau mesinnya Active.
|
*/

    public function galleryImage($id)
    {
        $image = DB::table('m_sample_galleries as g')
            ->join('m_engines as e', 'e.id', '=', 'g.engine_id')
            ->where('g.id', $id)
            ->where('e.status', 'Active')
            ->select('g.id', 'g.image_path')
            ->first();

        return $this->serveTemporaryImage(
            $image,
            'images/gallery-samples',
            'temporary-files/gallery-samples',
            'gallery_sample_'
        );
    }


    /*
|--------------------------------------------------------------------------
| SAJIKAN GAMBAR LEWAT TEMPORARY FILE
|--------------------------------------------------------------------------
|
| Metodenya sama dengan image() di admin:
| salin file asli ke folder sementara per sesi, lalu sajikan salinannya.
|
*/

    private function serveTemporaryImage($image, string $sourceFolder, string $temporaryFolder, string $prefix)
    {
        /*
    |--------------------------------------------------------------------------
    | DATA TIDAK DITEMUKAN ATAU TIDAK DIPUBLIKASIKAN
    |--------------------------------------------------------------------------
    */

        if (!$image) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | SOURCE PATH
    |--------------------------------------------------------------------------
    */

        $sourcePath = public_path($sourceFolder);

        /*
    |--------------------------------------------------------------------------
    | FILE ASLI
    |--------------------------------------------------------------------------
    */

        $baseName = basename($image->image_path);

        $sourceFile = $sourcePath . DIRECTORY_SEPARATOR . $baseName;

        if (!file_exists($sourceFile)) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | TEMPORARY DIRECTORY (PER SESI)
    |--------------------------------------------------------------------------
    */

        $sessionKey = hash('sha256', session()->getId());

        $temporaryPath = public_path($temporaryFolder . '/' . $sessionKey);

        if (!is_dir($temporaryPath)) {
            if (!mkdir($temporaryPath, 0755, true)) {
                abort(500);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | COPY KE TEMPORARY
    |--------------------------------------------------------------------------
    */

        $temporaryFilePath =
            $temporaryPath .
            DIRECTORY_SEPARATOR .
            $prefix .
            $image->id .
            '_' .
            $baseName;

        if (!file_exists($temporaryFilePath)) {
            if (!copy($sourceFile, $temporaryFilePath)) {
                abort(500);
            }
        }

        $response = response()->file($temporaryFilePath)
            ->deleteFileAfterSend(true);

        app()->terminating(function () use ($temporaryPath) {
            if (is_dir($temporaryPath)) {
                @rmdir($temporaryPath);
            }
        });

        return $response;
    }

    public function filters()
    {
        $engines = DB::table('m_engines')
            ->select('id', 'name')
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        $categories = DB::table('m_categories')
            ->select('id', 'name')
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'engines' => $engines,
                'categories' => $categories,
            ],
        ]);
    }

    public function productsList()
    {
        /*
        |--------------------------------------------------------------------------
        | 1. PRODUK DISPLAY (SEMUA YANG ACTIVE)
        |--------------------------------------------------------------------------
        |
        | Display tidak punya mesin, jadi engine_id dan engine_name bernilai null.
        |
        */

        $displayCover = DB::table('m_display_product_images as i')
            ->select('i.id')
            ->whereColumn('i.display_product_id', 'p.id')
            ->orderByDesc('i.is_primary')
            ->orderBy('i.id')
            ->limit(1);

        $displayRows = DB::table('m_display_products as p')
            ->join('m_categories as c', 'c.id', '=', 'p.category_id')
            ->leftJoin(
                'm_display_product_configurations as pc',
                'pc.display_product_id',
                '=',
                'p.id'
            )
            ->leftJoin(
                'm_engines as e',
                'e.id',
                '=',
                'pc.engine_id'
            )
            ->where('p.status', 'Active')
            ->select(
                'p.id',
                'p.display_name',
                'p.length',
                'p.width',
                'p.category_id',
                'p.package_qty',
                'p.package_unit',
                'c.name as category_name',
                'pc.engine_id',
                'e.name as engine_name'
            )
            ->selectSub($displayCover, 'cover_image_id')
            ->orderBy('p.display_name', 'asc')
            ->orderBy('p.id', 'asc')
            ->get();

        $displayItems = $displayRows->map(function ($row) {

            $length = (float) $row->length;
            $width = (float) $row->width;
            $desc = $row->category_name;
            if (!empty($row->package_qty) && !empty($row->package_unit)) {
                $desc .= ' · ' . (int) $row->package_qty . ' pcs/' . $row->package_unit;
            } elseif ($width > 0 && $length > 0) {
                $desc .= ' · Ukuran ' . $width . ' × ' . $length;
            }

            return [
                'key'           => 'display-' . $row->id,
                'type'          => 'display',
                'engine_id'     => $row->engine_id !== null
                    ? (int) $row->engine_id
                    : null,
                'category_id'   => (int) $row->category_id,
                'title'         => $row->display_name,
                'category_name' => $row->category_name,
                'engine_name'   => $row->engine_name,
                'desc'          => $desc,
                'cover_url'     => $row->cover_image_id
                    ? route('user_display_image', $row->cover_image_id)
                    : null,
                'url'           => route('user_product_details', [
                    'type' => 'display',
                    'id'   => (int) $row->id,
                ]),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | 2. GALERI SAMPEL (SATU KARTU PER KOMBINASI MESIN + KATEGORI)
        |--------------------------------------------------------------------------
        |
        | Hanya mesin yang Active.
        | Cover: gambar utama, kalau tidak ada pakai sort_order terkecil.
        |
        */

        $galleryCover = DB::table('m_sample_galleries as g2')
            ->select('g2.id')
            ->whereColumn('g2.engine_id', 'g.engine_id')
            ->whereColumn('g2.category_id', 'g.category_id')
            ->orderByDesc('g2.is_primary')
            ->orderBy('g2.sort_order')
            ->orderBy('g2.id')
            ->limit(1);

        $galleryRows = DB::table('m_sample_galleries as g')
            ->join('m_engines as e', 'e.id', '=', 'g.engine_id')
            ->join('m_categories as c', 'c.id', '=', 'g.category_id')
            ->where('e.status', 'Active')
            ->select(
                'g.engine_id',
                'g.category_id',
                'e.name as engine_name',
                'c.name as category_name'
            )
            ->selectSub($galleryCover, 'cover_image_id')
            ->groupBy(
                'g.engine_id',
                'g.category_id',
                'e.name',
                'c.name'
            )
            ->orderBy('c.name')
            ->orderBy('e.name')
            ->get();

        $galleryItems = $galleryRows->map(function ($row) {
            return [
                'key'           => 'gallery-' . $row->engine_id . '-' . $row->category_id,
                'type'          => 'gallery',
                'engine_id'     => (int) $row->engine_id,
                'category_id'   => (int) $row->category_id,
                'title'         => $row->category_name,
                'category_name' => $row->category_name,
                'engine_name'   => $row->engine_name,
                'desc'          => 'Contoh hasil cetak kategori ' . $row->category_name .
                    ' dengan ' . $row->engine_name . '.',
                'cover_url'     => $row->cover_image_id
                    ? route('user_gallery_image', $row->cover_image_id)
                    : null,
                'url'           => route('user_product_details', [
                    'type'        => 'gallery',
                    'engine_id'   => (int) $row->engine_id,
                    'category_id' => (int) $row->category_id,
                ]),
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | 3. GABUNGKAN DAN URUTKAN A-Z
        |--------------------------------------------------------------------------
        */

        $items = $displayItems
            ->concat($galleryItems)
            ->sortBy(function ($item) {
                return mb_strtolower($item['title'] . ' ' . ($item['engine_name'] ?? ''));
            })
            ->values();

        // dd($items);

        return response()->json([
            'success' => true,
            'data'    => $items,
        ]);
    }


    public function products()
    {
        return view('user.products');
    }

    public function baskets()
    {
        return view('user.baskets');
    }

    private const ESTIMATOR_LOCATION_KEYWORD = 'purwakarta';


    public function productDetails(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI PARAMETER
        |--------------------------------------------------------------------------
        */

        $validator = Validator::make($request->query(), [
            'type'        => ['required', 'in:display,gallery'],
            'id'          => ['nullable', 'integer', 'min:1'],
            'engine_id'   => ['nullable', 'integer', 'min:1'],
            'category_id' => ['nullable', 'integer', 'min:1'],
        ]);

        if ($validator->fails()) {
            abort(404);
        }

        $data = $validator->validated();

        /*
        |--------------------------------------------------------------------------
        | PRODUK DISPLAY
        |--------------------------------------------------------------------------
        */

        if ($data['type'] === 'display') {

            $displayId = $data['id'] ?? null;

            if (!$displayId) {
                abort(404);
            }

            $displayProduct = DB::table('m_display_products as p')
                ->join('m_categories as c', 'c.id', '=', 'p.category_id')
                ->where('p.id', $displayId)
                ->where('p.status', 'Active')
                ->select(
                    'p.id',
                    'p.display_name',
                    'p.category_id',
                    'p.package_qty',
                    'p.package_unit',
                    'p.length',
                    'p.width',
                    'c.name as category_name'
                )
                ->first();

            if (!$displayProduct) {
                abort(404);
            }

            $imageIds = DB::table('m_display_product_images')
                ->where('display_product_id', $displayProduct->id)
                ->orderByDesc('is_primary')
                ->orderBy('id')
                ->pluck('id');

            /*
            | Mesin display diambil dari konfigurasi. Kalau belum ada konfigurasi,
            | dianggap Large Format (sama seperti label di halaman daftar produk).
            */

            $displayEngine = DB::table('m_display_product_configurations as pc')
                ->join('m_engines as e', 'e.id', '=', 'pc.engine_id')
                ->where('pc.display_product_id', $displayProduct->id)
                ->orderBy('pc.id')
                ->select('e.id', 'e.name', 'pc.sheet_qty', 'pc.cost_rangka', 'pc.cost_finishing')
                ->first();

            $engineName = $displayEngine->name ?? 'Large Format';
            $discountEngineId = $displayEngine->id ?? DB::table('m_engines')
                ->whereRaw('LOWER(TRIM(name)) = ?', ['large format'])
                ->value('id');

            $product = [
                'type'   => 'display',
                'title'  => $displayProduct->display_name,
                'label'  => $displayProduct->category_name,
                'images' => $imageIds->map(function ($imageId) {
                    return route('user_display_image', $imageId);
                })->values()->all(),
                'basketItem' => [
                    'id' => 'display-' . $displayProduct->id,
                    'title' => $displayProduct->display_name,
                    'category' => $displayProduct->category_name,
                    'printType' => $engineName,
                    'printTypeLabel' => $engineName,
                    'img' => $imageIds->first()
                        ? route('user_display_image', $imageIds->first())
                        : null,
                    'url' => route('user_product_details', [
                        'type' => 'display',
                        'id' => (int) $displayProduct->id,
                    ]),
                ],
            ];

            $isLargeFormat = strtolower(trim($engineName)) === 'large format';
            $isA3Plus = strtolower(trim($engineName)) === 'digital print a3+';

            $estimator = [
                'type'             => 'display',
                'engineName'       => $engineName,
                'isLargeFormat'    => $isLargeFormat,
                'isA3Plus'         => $isA3Plus,
                'isDisplay'        => true,
                'displayProductId' => (int) $displayProduct->id,
                'categoryId'       => (int) $displayProduct->category_id,
                'packageQty'       => (int) ($displayProduct->package_qty ?? 100),
                'packageUnit'      => $displayProduct->package_unit ?? 'Box',
                'sheetQty'         => (int) ($displayEngine->sheet_qty ?? 4),
            ];

            $locations = DB::table('m_locations')
                ->whereRaw('LOWER(TRIM(status)) = ?', ['active'])
                ->orderBy('name', 'asc')
                ->get();

            $vendors = DB::table('m_vendors')
                ->where('status', 'Active')
                ->get();

            $a3Data = $isA3Plus
                ? $this->getA3PlusMasterData((int) $displayProduct->category_id, (int) $displayProduct->id)
                : [
                    'a3Materials'   => collect(),
                    'a3Laminations' => collect(),
                    'a3Cuttings'    => collect(),
                ];

            return view('user.product_details', array_merge([
                'product'   => $product,
                'estimator' => $estimator,
                'locations' => $locations,
                'vendors'   => $vendors,
                'activeDiscounts' => $this->activeProductDiscounts(
                    $discountEngineId ? (int) $discountEngineId : null,
                    (int) $displayProduct->category_id
                ),
            ], $a3Data));
        }

        /*
        |--------------------------------------------------------------------------
        | PRODUK GALERI SAMPEL
        |--------------------------------------------------------------------------
        */

        $categoryId = $data['category_id'] ?? $data['id'] ?? null;

        $engineId = $data['engine_id'] ?? null;

        if (!$categoryId) {
            abort(404);
        }

        $galleryQuery = DB::table('m_sample_galleries as g')
            ->join('m_engines as e', 'e.id', '=', 'g.engine_id')
            ->join('m_categories as c', 'c.id', '=', 'g.category_id')
            ->where('e.status', 'Active')
            ->where('g.category_id', $categoryId);

        if ($engineId) {
            $galleryQuery->where('g.engine_id', $engineId);
        }

        $galleryRows = $galleryQuery
            ->select(
                'g.id',
                'c.name as category_name',
                'e.name as engine_name'
            )
            ->orderByDesc('g.is_primary')
            ->orderBy('g.sort_order')
            ->orderBy('g.id')
            ->get();

        if ($galleryRows->isEmpty()) {
            abort(404);
        }

        $firstRow = $galleryRows->first();

        /*
        | Galeri dari landing (tanpa engine_id) tidak punya mesin tertentu,
        | jadi estimator dikosongkan.
        */

        $engineName = $engineId ? $firstRow->engine_name : null;

        $product = [
            'type'   => 'gallery',
            'title'  => $firstRow->category_name,
            'label'  => $engineId ? $firstRow->engine_name : 'Contoh Hasil Cetak',
            'images' => $galleryRows->map(function ($row) {
                return route('user_gallery_image', $row->id);
            })->values()->all(),
            'basketItem' => [
                'id' => 'gallery-' . ($engineId ? $engineId . '-' : '') . $categoryId,
                'title' => $firstRow->category_name,
                'category' => $firstRow->category_name,
                'printType' => $engineName ?? 'Large Format',
                'printTypeLabel' => $engineName ?? 'Large Format',
                'img' => route('user_gallery_image', $galleryRows->first()->id),
                'url' => route('user_product_details', array_filter([
                    'type' => 'gallery',
                    'engine_id' => $engineId ? (int) $engineId : null,
                    'category_id' => (int) $categoryId,
                ], static fn ($value) => $value !== null)),
            ],
        ];

        $isLargeFormat = $engineName !== null
            && strtolower(trim($engineName)) === 'large format';
        $isA3Plus = $engineName !== null
            && strtolower(trim($engineName)) === 'digital print a3+';

        $estimator = [
            'type'             => 'gallery',
            'engineName'       => $engineName,
            'isLargeFormat'    => $isLargeFormat,
            'isA3Plus'         => $isA3Plus,
            'displayProductId' => null,
            'categoryId'       => (int) $categoryId,
        ];

        $locations = DB::table('m_locations')
            ->whereRaw('LOWER(TRIM(status)) = ?', ['active'])
            ->orderBy('name', 'asc')
            ->get();

        $vendors = DB::table('m_vendors')
            ->where('status', 'Active')
            ->get();

        $a3Data = $isA3Plus
            ? $this->getA3PlusMasterData((int) $categoryId)
            : [
                'a3Materials'   => collect(),
                'a3Laminations' => collect(),
                'a3Cuttings'    => collect(),
            ];

        return view('user.product_details', array_merge([
            'product'   => $product,
            'estimator' => $estimator,
            'locations' => $locations,
            'vendors'   => $vendors,
            'activeDiscounts' => $this->activeProductDiscounts($engineId ? (int) $engineId : null, (int) $categoryId),
        ], $a3Data));
    }

    private function activeProductDiscounts(?int $engineId, int $categoryId): Collection
    {
        // Galeri tanpa mesin tertentu tidak boleh menampilkan promo dari mesin lain sebagai promo produk ini.
        if ($engineId === null) {
            return collect();
        }

        $now = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');

        return DB::table('m_discounts')
            ->where('status', 'Active')
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now)
            ->whereExists(function ($query) use ($engineId, $categoryId) {
                $query->selectRaw('1')
                    ->from('m_discount_scopes')
                    ->whereColumn('m_discount_scopes.discount_id', 'm_discounts.id')
                    ->where('engine_id', $engineId)
                    ->where('category_id', $categoryId);
            })
            ->orderBy('ends_at')
            ->orderBy('id')
            ->get(['id', 'name', 'code', 'discount_percentage', 'starts_at', 'ends_at']);
    }

    public function previewUserLFEstimation(Request $request)
    {
        $productQuantity = (int) $request->input('qty', 0);

        $promoCode = $request->input('promo_code');
        if (is_string($promoCode)) {
            $request->merge(['promo_code' => strtoupper(trim($promoCode))]);
        }

        $request->validate([
            'promo_code' => ['bail', 'nullable', 'string', 'min:3', 'max:30', 'regex:/\A[A-Z0-9_-]+\z/'],
            'additional_components' => ['nullable', 'array'],
            'additional_components.*' => ['array'],
        ], [
            'promo_code.string' => 'Kode promo harus berupa teks.',
            'promo_code.min' => 'Kode promo minimal 3 karakter.',
            'promo_code.max' => 'Kode promo maksimal 30 karakter.',
            'promo_code.regex' => 'Kode promo hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
        ]);

        $components = $request->input('additional_components', []);

        if (is_array($components)) {
            $components = array_values(array_filter(
                $components,
                function ($component) {
                    if (!is_array($component)) {
                        return false;
                    }

                    $hasRequiredDetails =
                        filled($component['type'] ?? null)
                        && filled($component['name'] ?? null)
                        && filled($component['qty'] ?? null);

                    if (!$hasRequiredDetails) {
                        return false;
                    }

                    return ($component['price_type'] ?? null) === 'hpp_margin'
                        ? filled($component['hpp'] ?? null)
                            && filled($component['margin'] ?? null)
                        : filled($component['unit_price'] ?? null);
                }
            ));

            foreach ($components as &$component) {
                foreach (['unit_price', 'hpp'] as $amountField) {
                    if (isset($component[$amountField])) {
                        $component[$amountField] = preg_replace(
                            '/\D/',
                            '',
                            (string) $component[$amountField]
                        );
                    }
                }
            }
            unset($component);
        }

        $request->merge([
            'additional_components' => $components,
            // Diskon manual tidak diterima dari web user; nilai promo dihitung dari master setelah estimasi berhasil.
            'discount_type' => 'none',
            'discount_value' => 0,
        ]);

        $calculationResponse = app(EstimationCT::class)->storeLF($request);
        $calculation = $calculationResponse->getData(true);

        if (
            $calculationResponse->getStatusCode() >= 400
            || !($calculation['success'] ?? false)
        ) {
            return $calculationResponse;
        }

        $data = $calculation['data'];
        $lamination = $data['lamination'] ?? null;
        $additionalComponents = $data['additional_components'] ?? [];
        $promo = null;
        $discountAmount = 0.0;
        $grandTotal = (float) $data['grand_total'];
        $roundingAmount = round($grandTotal - (float) $data['subtotal'], 2);

        if (filled($request->input('promo_code'))) {
            // Gunakan konfigurasi yang sudah lolos perhitungan, bukan engine_id kiriman client.
            $engineId = isset($data['display_configuration_id'])
                ? DB::table('m_display_product_configurations')
                    ->where('id', $data['display_configuration_id'])->value('engine_id')
                : DB::table('m_engines')
                    ->whereRaw('LOWER(TRIM(name)) = ?', ['large format'])
                    ->whereRaw('LOWER(TRIM(status)) = ?', ['active'])->value('id');

            $promo = $this->activeProductDiscounts(
                $engineId ? (int) $engineId : null,
                (int) $request->input('category_id')
            )->first(fn ($discount) => strtoupper($discount->code) === $request->input('promo_code'));

            if ($promo === null) {
                throw ValidationException::withMessages([
                    'promo_code' => 'Kode promo tidak valid, belum/sudah tidak berlaku, atau tidak sesuai dengan produk yang dipilih.',
                ]);
            }

            $discountAmount = round((float) $data['subtotal'] * (float) $promo->discount_percentage / 100, 2);
            $totalAfterDiscount = round(max(0, (float) $data['subtotal'] - $discountAmount), 2);
            $grandTotal = ceil($totalAfterDiscount / 1000) * 1000;
            $roundingAmount = round($grandTotal - $totalAfterDiscount, 2);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'material_name' => $data['material_name'] ?? null,
                'material_quantity' => (float) (
                    ($data['material_price_unit'] ?? null) === 'unit'
                        ? $productQuantity
                        : ($data['billing_area'] ?? 0)
                ),
                'material_product_quantity' => $productQuantity,
                'material_quantity_unit' => ($data['material_price_unit'] ?? null) === 'unit'
                    ? 'pcs'
                    : 'm²',
                'material_unit_price' => (float) ($data['material_price'] ?? 0),
                'material_subtotal' => (float) ($data['material_subtotal'] ?? 0),
                'lamination_name' => $lamination['name'] ?? null,
                'lamination_quantity' => (float) ($lamination['billing_length'] ?? 0),
                'lamination_unit_price' => (float) ($lamination['price'] ?? 0),
                'lamination_subtotal' => (float) ($lamination['subtotal'] ?? 0),
                'additional_components' => array_map(
                    static fn (array $component): array => [
                        'type' => $component['type'],
                        'name' => $component['name'],
                        'qty' => (int) $component['qty'],
                        'price_type' => $component['price_type'],
                        'unit_price' => (float) $component['sale_price_per_unit'],
                        'subtotal' => (float) $component['subtotal'],
                    ],
                    $additionalComponents
                ),
                'additional_components_subtotal' => (float) array_sum(
                    array_column($additionalComponents, 'subtotal')
                ),
                'promo' => $promo ? [
                    'code' => $promo->code,
                    'name' => $promo->name,
                    'percentage' => (float) $promo->discount_percentage,
                ] : null,
                'discount_amount' => $discountAmount,
                'rounding_amount' => $roundingAmount,
                'subtotal' => (float) ($data['subtotal'] ?? 0),
                'grand_total' => $grandTotal,
                'qty' => $productQuantity,
            ],
        ]);
    }

    public function previewUserA3PlusEstimation(Request $request)
    {
        $promoCode = $request->input('promo_code');
        if (is_string($promoCode)) {
            $request->merge(['promo_code' => strtoupper(trim($promoCode))]);
        }

        $categoryId = $request->input('category_id');
        $isDisplay = false;
        if (!empty($categoryId)) {
            $cat = DB::table('m_categories')->where('id', $categoryId)->first();
            if ($cat && stripos($cat->name, 'display') !== false) {
                $isDisplay = true;
            }
        }

        $request->validate([
            'promo_code' => ['bail', 'nullable', 'string', 'min:3', 'max:30', 'regex:/\A[A-Z0-9_-]+\z/'],
            'production_mode' => $isDisplay ? ['nullable', 'in:layout,direct,display'] : ['required', 'in:layout,direct'],
            'print_color' => $isDisplay ? ['nullable', 'in:fc,bw'] : ['required', 'in:fc,bw'],
            'print_side' => ['required', 'in:1,2'],
            'material_id' => $isDisplay ? ['required', 'integer'] : ['required', 'integer', 'exists:m_materials,id'],
            'length' => ['nullable', 'numeric', 'min:0.1'],
            'width' => ['nullable', 'numeric', 'min:0.1'],
            'qty' => ['nullable', 'integer', 'min:1'],
            'sheet_qty' => ['nullable', 'integer', 'min:1'],
            'lamination_type' => ['nullable', 'string'],
            'cutting_type' => ['nullable', 'string'],
            'potong_size_override' => ['nullable', 'string'],
        ], [
            'promo_code.string' => 'Kode promo harus berupa teks.',
            'promo_code.min' => 'Kode promo minimal 3 karakter.',
            'promo_code.max' => 'Kode promo maksimal 30 karakter.',
            'promo_code.regex' => 'Kode promo hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_).',
        ]);

        $purwakarta = DB::table('m_locations')
            ->whereRaw('LOWER(TRIM(name)) = ?', ['purwakarta'])
            ->first();
        $locationId = $purwakarta ? $purwakarta->id : 54;

        $mergeData = [
            'location_id' => $locationId,
            'price_type' => 'general',
            'vendor_id' => null,
            'estimation_time' => 1,
            'discount_type' => 'none',
            'discount_value' => 0,
            'additional_components' => null,
        ];

        if ($isDisplay) {
            $mergeData['production_mode'] = 'display';
            $mergeData['print_color'] = $request->input('print_color', 'fc') ?: 'fc';
        }

        $request->merge($mergeData);

        $calculationResponse = app(EstimationCT::class)->storeA3plus($request);
        $calculation = $calculationResponse->getData(true);

        if (
            $calculationResponse->getStatusCode() >= 400
            || !($calculation['success'] ?? false)
        ) {
            return $calculationResponse;
        }

        $data = $calculation['data'];
        $promo = null;
        $discountAmount = 0.0;
        $grandTotal = (float) $data['grand_total'];
        $subtotal = (float) ($data['subtotal'] ?? 0);
        $roundingAmount = max(0.0, round($grandTotal - $subtotal, 2));

        if (filled($request->input('promo_code'))) {
            $engineId = DB::table('m_engines')
                ->whereRaw('LOWER(TRIM(name)) = ?', ['digital print a3+'])
                ->value('id');

            $categoryId = (int) ($request->input('category_id') ?: ($data['category_id'] ?? 0));
            if (!$categoryId && !empty($data['material_id'])) {
                $categoryId = (int) DB::table('m_materials')->where('id', $data['material_id'])->value('category_id');
            }

            $promo = $this->activeProductDiscounts(
                $engineId ? (int) $engineId : 70,
                $categoryId
            )->first(fn ($discount) => strtoupper($discount->code) === $request->input('promo_code'));

            if ($promo === null) {
                throw ValidationException::withMessages([
                    'promo_code' => 'Kode promo tidak valid, belum/sudah tidak berlaku, atau tidak sesuai dengan produk yang dipilih.',
                ]);
            }

            $subtotal = (float) $data['subtotal'];
            $discountAmount = round($subtotal * (float) $promo->discount_percentage / 100, 2);
            $totalAfterDiscount = max(0, $subtotal - $discountAmount);
            $grandTotal = ceil(round($totalAfterDiscount, 2) / 1000) * 1000;
            $roundingAmount = round($grandTotal - $totalAfterDiscount, 2);
        }

        $qty = (int) ($data['qty'] ?? 1);
        $pricePerPcs = $qty > 0 ? round($grandTotal / $qty, 2) : 0.0;

        return response()->json([
            'success' => true,
            'data' => [
                'engine_name' => 'Digital Print A3+',
                'material_name' => $data['material_name'] ?? null,
                'category_name' => $data['category_name'] ?? null,
                'print_color' => $data['print_color'] ?? 'Full Color',
                'print_side' => $data['print_side'] ?? '1 Sisi',
                'color_side' => $data['color_side'] ?? null,
                'production_mode' => $data['production_mode'] ?? 'Hitung Layout',
                'finished_size' => $data['finished_size'] ?? null,
                'imposition_info' => $data['imposition_info'] ?? null,
                'up_count' => (int) ($data['up_count'] ?? 1),
                'orientation' => $data['orientation'] ?? 'Normal',
                'qty' => $qty,
                'total_sheets' => (int) ($data['total_sheets'] ?? 1),
                'unit_price_sheet' => (float) ($data['unit_price_sheet'] ?? 0),
                'print_subtotal' => (float) ($data['print_subtotal'] ?? 0),
                'lamination_name' => $data['lamination_name'] ?? 'Tanpa Laminasi',
                'lamination_subtotal' => (float) ($data['lamination_subtotal'] ?? 0),
                'cutting_name' => $data['cutting_name'] ?? 'Tanpa Cutting',
                'cutting_subtotal' => (float) ($data['cutting_subtotal'] ?? 0),
                'finishing_summary' => $data['finishing_summary'] ?? 'Tanpa Finishing',
                'promo' => $promo ? [
                    'code' => $promo->code,
                    'name' => $promo->name,
                    'percentage' => (float) $promo->discount_percentage,
                ] : null,
                'discount_amount' => (float) $discountAmount,
                'rounding_amount' => (float) $roundingAmount,
                'subtotal' => (float) ($data['subtotal'] ?? 0),
                'grand_total' => (float) $grandTotal,
                'price_per_pcs' => (float) $pricePerPcs,
                'lead_time' => $data['lead_time'] ?? '1 hari kerja',
            ],
        ]);
    }

    public function getUserMaterialsA3plus(Request $request)
    {
        $purwakarta = DB::table('m_locations')
            ->whereRaw('LOWER(TRIM(name)) = ?', ['purwakarta'])
            ->first();
        $locationId = $request->input('location_id') ?: ($purwakarta ? $purwakarta->id : 54);
        $printColor = $request->input('print_color', 'fc');
        $categoryId = $request->input('category_id');
        $displayProductId = $request->input('display_product_id');

        $isDisplay = false;
        if (!empty($categoryId)) {
            $cat = DB::table('m_categories')->where('id', $categoryId)->first();
            if ($cat && stripos($cat->name, 'display') !== false) {
                $isDisplay = true;
            }
        }

        if ($isDisplay) {
            $engine = DB::table('m_engines')->where('name', 'ILIKE', '%digital print%')->first();
            $displayComponents = DB::table('m_display_product_configurations as dpcfg')
                ->join('m_display_products as dp', 'dp.id', '=', 'dpcfg.display_product_id')
                ->join('m_display_product_component_prices as cpp', 'cpp.configuration_id', '=', 'dpcfg.id')
                ->join('m_display_product_components as dpc', 'dpc.id', '=', 'cpp.component_id')
                ->leftJoin('m_materials as m', 'm.id', '=', 'dpc.material_id')
                ->leftJoin('m_laminations as lam', 'lam.id', '=', 'dpc.lamination_id')
                ->where('dpcfg.location_id', $locationId)
                ->when($engine, function ($q) use ($engine) {
                    $q->where('dpcfg.engine_id', $engine->id);
                })
                ->when($displayProductId, function ($q) use ($displayProductId) {
                    $q->where('dp.id', $displayProductId);
                })
                ->selectRaw("
                    dpc.id AS id,
                    dpcfg.id AS configuration_id,
                    dp.id AS display_product_id,
                    dp.display_name,
                    dp.package_qty,
                    dp.package_unit,
                    dpcfg.sheet_qty,
                    dpcfg.cost_rangka,
                    dpcfg.cost_finishing,
                    cpp.general_price,
                    cpp.general_price_2_side,
                    cpp.division_price,
                    cpp.division_price_2_side,
                    CONCAT(dp.display_name, ' — ', COALESCE(m.material_name, 'Material'), CASE WHEN lam.name IS NOT NULL THEN CONCAT(' + ', CASE WHEN (lam.name ILIKE '%doff%' OR lam.name ILIKE '%glossy%') THEN 'Laminasi Doff / Glossy A3+' ELSE lam.name END) ELSE ' (Tanpa Laminasi)' END) AS name
                ")
                ->get();

            return response()->json([
                'success' => true,
                'data' => $displayComponents,
            ]);
        }

        $query = DB::table('m_materials as m')
            ->join('m_categories as c', 'c.id', '=', 'm.category_id')
            ->join('m_digital_production_cost_details as d', 'd.material_id', '=', 'm.id')
            ->join('m_digital_production_costs as pc', 'pc.id', '=', 'd.digital_production_cost_id')
            ->where('pc.location_id', $locationId)
            ->where('m.status', 'Active');

        if ($printColor === 'bw') {
            $query->whereRaw('LOWER(TRIM(c.name)) = ?', ['bw']);
        } else {
            $query->whereRaw('LOWER(TRIM(c.name)) != ?', ['bw']);
        }

        if (!empty($categoryId)) {
            $catQuery = clone $query;
            $catMaterials = $catQuery->where('m.category_id', $categoryId)
                ->select('m.id', 'm.material_name as name', 'c.id as category_id', 'c.name as category_name')
                ->distinct()
                ->orderBy('c.name', 'asc')
                ->orderBy('m.material_name', 'asc')
                ->get();

            if ($catMaterials->isNotEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => $catMaterials,
                ]);
            }
        }

        $materials = $query->select(
            'm.id',
            'm.material_name as name',
            'c.id as category_id',
            'c.name as category_name'
        )
        ->distinct()
        ->orderBy('c.name', 'asc')
        ->orderBy('m.material_name', 'asc')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $materials,
        ]);
    }

    private function getA3PlusMasterData(?int $categoryId = null, ?int $displayProductId = null): array
    {
        $purwakarta = DB::table('m_locations')
            ->whereRaw('LOWER(TRIM(name)) = ?', ['purwakarta'])
            ->first();
        $purwakartaId = $purwakarta ? $purwakarta->id : 54;

        $isDisplay = false;
        if (!empty($categoryId)) {
            $cat = DB::table('m_categories')->where('id', $categoryId)->first();
            if ($cat && stripos($cat->name, 'display') !== false) {
                $isDisplay = true;
            }
        }

        if ($isDisplay) {
            $engine = DB::table('m_engines')->where('name', 'ILIKE', '%digital print%')->first();
            $materials = DB::table('m_display_product_configurations as dpcfg')
                ->join('m_display_products as dp', 'dp.id', '=', 'dpcfg.display_product_id')
                ->join('m_display_product_component_prices as cpp', 'cpp.configuration_id', '=', 'dpcfg.id')
                ->join('m_display_product_components as dpc', 'dpc.id', '=', 'cpp.component_id')
                ->leftJoin('m_materials as m', 'm.id', '=', 'dpc.material_id')
                ->leftJoin('m_laminations as lam', 'lam.id', '=', 'dpc.lamination_id')
                ->where('dpcfg.location_id', $purwakartaId)
                ->when($engine, function ($q) use ($engine) {
                    $q->where('dpcfg.engine_id', $engine->id);
                })
                ->when($displayProductId, function ($q) use ($displayProductId) {
                    $q->where('dp.id', $displayProductId);
                })
                ->selectRaw("
                    dpc.id AS id,
                    dpcfg.id AS configuration_id,
                    dp.id AS display_product_id,
                    dp.display_name,
                    dp.package_qty,
                    dp.package_unit,
                    dpcfg.sheet_qty,
                    dpcfg.cost_rangka,
                    dpcfg.cost_finishing,
                    cpp.general_price,
                    cpp.general_price_2_side,
                    cpp.division_price,
                    cpp.division_price_2_side,
                    CONCAT(dp.display_name, ' — ', COALESCE(m.material_name, 'Material'), CASE WHEN lam.name IS NOT NULL THEN CONCAT(' + ', CASE WHEN (lam.name ILIKE '%doff%' OR lam.name ILIKE '%glossy%') THEN 'Laminasi Doff / Glossy A3+' ELSE lam.name END) ELSE ' (Tanpa Laminasi)' END) AS name
                ")
                ->get();
        } else {
            $materials = DB::table('m_materials as m')
                ->join('m_categories as c', 'c.id', '=', 'm.category_id')
                ->join('m_digital_production_cost_details as d', 'd.material_id', '=', 'm.id')
                ->join('m_digital_production_costs as pc', 'pc.id', '=', 'd.digital_production_cost_id')
                ->where('pc.location_id', $purwakartaId)
                ->where('m.status', 'Active')
                ->whereRaw('LOWER(TRIM(c.name)) != ?', ['bw'])
                ->when($categoryId, fn ($q) => $q->where('m.category_id', $categoryId))
                ->select('m.id', 'm.material_name as name', 'c.id as category_id', 'c.name as category_name')
                ->distinct()
                ->orderBy('c.name', 'asc')
                ->orderBy('m.material_name', 'asc')
                ->get();

            if ($materials->isEmpty()) {
                $materials = DB::table('m_materials as m')
                    ->join('m_categories as c', 'c.id', '=', 'm.category_id')
                    ->join('m_digital_production_cost_details as d', 'd.material_id', '=', 'm.id')
                    ->join('m_digital_production_costs as pc', 'pc.id', '=', 'd.digital_production_cost_id')
                    ->where('pc.location_id', $purwakartaId)
                    ->where('m.status', 'Active')
                    ->whereRaw('LOWER(TRIM(c.name)) != ?', ['bw'])
                    ->select('m.id', 'm.material_name as name', 'c.id as category_id', 'c.name as category_name')
                    ->distinct()
                    ->orderBy('c.name', 'asc')
                    ->orderBy('m.material_name', 'asc')
                    ->get();
            }
        }

        $laminations = DB::table('m_laminations as l')
            ->leftJoin('m_digital_lamination_production_cost_details as d', 'd.lamination_id', '=', 'l.id')
            ->leftJoin('m_digital_lamination_production_costs as pc', 'pc.id', '=', 'd.digital_lamination_production_cost_id')
            ->where('l.status', 'Active')
            ->where(function ($q) {
                $q->whereRaw('LOWER(l.name) LIKE ?', ['%a3+%'])
                    ->orWhereNotNull('d.id');
            })
            ->select('l.id', 'l.name')
            ->distinct()
            ->orderBy('l.name', 'asc')
            ->get();

        $cuttings = DB::table('m_cuttings')
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        return [
            'a3Materials'   => $materials,
            'a3Laminations' => $laminations,
            'a3Cuttings'    => $cuttings,
        ];
    }

    public function getUserLFCategories()
    {
        /*
        |--------------------------------------------------------------------------
        | AMBIL ENGINE LARGE FORMAT
        |--------------------------------------------------------------------------
        */

        $engine = DB::table('m_engines')
            ->whereRaw(
                'LOWER(TRIM(name)) = ?',
                ['large format']
            )
            ->whereRaw(
                'LOWER(TRIM(status)) = ?',
                ['active']
            )
            ->first();

        if (!$engine) {
            return response()->json([
                'success' => false,
                'message' => 'Mesin Large Format tidak ditemukan atau tidak aktif.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | AMBIL KATEGORI
        |--------------------------------------------------------------------------
        */

        $materialCategories = DB::table('m_categories as c')
            ->select([
                'c.id',
                'c.name',
            ])
            ->whereExists(function ($query) use ($engine) {
                $query->select(DB::raw(1))
                    ->from('m_materials as m')
                    ->join(
                        'm_production_cost_details as pcd',
                        'pcd.material_id',
                        '=',
                        'm.id'
                    )
                    ->join(
                        'm_production_costs as pc',
                        'pc.id',
                        '=',
                        'pcd.production_cost_id'
                    )
                    ->whereColumn('m.category_id', 'c.id')
                    ->where('pc.engine_id', $engine->id);
            });

        $displayCategories = DB::table('m_categories as c')
            ->join(
                'm_display_products as dp',
                'dp.category_id',
                '=',
                'c.id'
            )
            ->join(
                'm_display_pricing_rules as dpr',
                'dpr.category_id',
                '=',
                'c.id'
            )
            ->where('dpr.engine_id', $engine->id)
            ->select([
                'c.id',
                'c.name',
            ]);

        $categories = $materialCategories
            ->union($displayCategories)
            ->orderBy('name', 'asc')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function getUserMaterials(Request $request)
    {
        $validated = $request->validate([
            'location_id' => [
                'required',
                'integer',
                'exists:m_locations,id',
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:m_categories,id',
            ],
            'vendor_id' => [
                'nullable',
                'integer',
                'exists:m_vendors,id',
            ],
            'display_product_id' => [
                'nullable',
                'integer',
                'min:1',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 1. CEK LOKASI
        |--------------------------------------------------------------------------
        */

        $location = DB::table('m_locations')
            ->where('id', $validated['location_id'])
            ->first();

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi tidak ditemukan.',
            ], 422);
        }

        $isOutsourcing =
            strtolower(trim($location->name)) === 'outsourcing';

        /*
        |--------------------------------------------------------------------------
        | 2. VALIDASI VENDOR UNTUK OUTSOURCING
        |--------------------------------------------------------------------------
        */

        if ($isOutsourcing && empty($validated['vendor_id'])) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor wajib dipilih untuk lokasi Outsourcing.',
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | 3. CEK KATEGORI
        |--------------------------------------------------------------------------
        */

        $category = DB::table('m_categories')
            ->where('id', $validated['category_id'])
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan.',
            ], 422);
        }

        $isDisplay =
            strtolower(trim($category->name)) === 'display';

        $displayProduct = null;

        if ($isDisplay) {
            if (empty($validated['display_product_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk Display wajib ditentukan.',
                ], 422);
            }

            $displayProduct = DB::table('m_display_products')
                ->where('id', $validated['display_product_id'])
                ->where('category_id', $validated['category_id'])
                ->whereRaw('LOWER(TRIM(status)) = ?', ['active'])
                ->first();

            if (!$displayProduct) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk Display tidak ditemukan atau tidak aktif.',
                ], 422);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 4. QUERY KHUSUS DISPLAY
        |--------------------------------------------------------------------------
        */

        if ($isDisplay) {

            $frameMaterials = DB::table('m_display_product_configurations as dpcfg')

                ->join(
                    'm_display_products as dp',
                    'dp.id',
                    '=',
                    'dpcfg.display_product_id'
                )

                ->join(
                    'm_display_product_prices as dpp',
                    'dpp.display_product_id',
                    '=',
                    'dp.id'
                )

                ->where(
                    'dpcfg.location_id',
                    $validated['location_id']
                )

                ->where(
                    'dp.category_id',
                    $validated['category_id']
                )

                ->where(
                    'dp.id',
                    $displayProduct->id
                )

                ->selectRaw("
                dpcfg.id AS id,
                dpcfg.id AS configuration_id,
                dp.id AS display_product_id,

                dp.display_name,
                dp.length AS display_length,
                dp.width AS display_width,

                dpcfg.location_id,
                dpcfg.engine_id,

                NULL AS component_id,
                'frame' AS component_type,

                NULL AS material_id,
                NULL AS lamination_id,

                dpcfg.cost_rangka,
                dpcfg.cost_finishing,
                dpcfg.total_cost,

                dpp.general_price,
                dpp.division_price,
                dpp.plain_price,

                CONCAT(
                    dp.display_name,
                    ' —+ Rangka'
                ) AS material_name
            ");

            $componentMaterials = DB::table('m_display_product_configurations as dpcfg')

                ->join(
                    'm_display_products as dp',
                    'dp.id',
                    '=',
                    'dpcfg.display_product_id'
                )

                ->join(
                    'm_display_product_component_prices as cpp',
                    'cpp.configuration_id',
                    '=',
                    'dpcfg.id'
                )

                ->join(
                    'm_display_product_components as dpc',
                    'dpc.id',
                    '=',
                    'cpp.component_id'
                )

                ->leftJoin(
                    'm_materials as m',
                    'm.id',
                    '=',
                    'dpc.material_id'
                )

                ->leftJoin(
                    'm_laminations as lam',
                    'lam.id',
                    '=',
                    'dpc.lamination_id'
                )

                ->where(
                    'dpcfg.location_id',
                    $validated['location_id']
                )

                ->where(
                    'dp.category_id',
                    $validated['category_id']
                )

                ->where(
                    'dp.id',
                    $displayProduct->id
                )

                ->where(function ($query) {
                    $query->whereNotNull('dpc.material_id')
                        ->orWhereNotNull('dpc.lamination_id');
                })

                ->selectRaw("
                    dpc.id AS id,
                    dpcfg.id AS configuration_id,
                    dp.id AS display_product_id,

                    dp.display_name,
                    dp.length AS display_length,
                    dp.width AS display_width,

                    dpcfg.location_id,
                    dpcfg.engine_id,

                    dpc.id AS component_id,
                    dpc.component_type,

                    dpc.material_id,
                    dpc.lamination_id,

                    dpcfg.cost_rangka,
                    dpcfg.cost_finishing,
                    dpcfg.total_cost,

                    cpp.general_price,
                    cpp.division_price,
                    cpp.plain_price,

                    CONCAT(
                        dp.display_name,
                        ' —+ ',
                        CASE
                            WHEN dpc.material_id IS NOT NULL
                                AND dpc.lamination_id IS NOT NULL
                                THEN CONCAT(
                                    m.material_name,
                                    ' + ',
                                    lam.name
                                )

                            WHEN dpc.material_id IS NOT NULL
                                THEN m.material_name

                            WHEN dpc.lamination_id IS NOT NULL
                                THEN lam.name

                            ELSE '-'
                        END
                    ) AS material_name
                ");

            $materials = $frameMaterials
                ->unionAll($componentMaterials)
                ->orderBy('material_name', 'asc')
                ->get();

            // dd($materials);
        } else {

            /*
            |--------------------------------------------------------------------------
            | 5. QUERY LARGE FORMAT LAMA
            |--------------------------------------------------------------------------
            | Logika pengambilan material sebelumnya dipertahankan.
            */

            $engine = DB::table('m_engines')
                ->whereRaw(
                    'LOWER(TRIM(name)) = ?',
                    ['large format']
                )
                ->whereRaw(
                    'LOWER(TRIM(status)) = ?',
                    ['active']
                )
                ->first();

            if (!$engine) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mesin Large Format tidak ditemukan atau tidak aktif.',
                ], 422);
            }

            $materials = DB::table('m_materials as m')

                ->join(
                    'm_categories as c',
                    'c.id',
                    '=',
                    'm.category_id'
                )

                ->join(
                    'm_production_costs as pc',
                    'pc.engine_id',
                    '=',
                    DB::raw($engine->id)
                )

                ->join(
                    'm_production_cost_locations as pcl',
                    'pcl.production_cost_id',
                    '=',
                    'pc.id'
                )

                ->join(
                    'm_production_cost_details as pcd',
                    'pcd.production_cost_id',
                    '=',
                    'pc.id'
                )

                ->where(
                    'pcl.location_id',
                    $validated['location_id']
                )

                ->where(
                    'm.category_id',
                    $validated['category_id']
                )

                ->where(
                    'pcd.material_id',
                    '=',
                    DB::raw('m.id')
                );

            if ($isOutsourcing) {
                $materials->where(
                    'pcd.vendor_id',
                    $validated['vendor_id']
                );
            }

            $materials = $materials
                ->select([
                    'm.id',
                    'm.material_name',
                ])
                ->distinct()
                ->orderBy('m.material_name', 'asc')
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | 6. RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'data' => $materials,
        ]);
    }

    public function getUserMaterialSizes(Request $request)
    {
        $validated = $request->validate([
            'location_id' => [
                'required',
                'integer',
                'exists:m_locations,id',
            ],
            'category_id' => [
                'required',
                'integer',
                'exists:m_categories,id',
            ],
            'material_id' => [
                'required',
                'integer',
                'exists:m_materials,id',
            ],
            'vendor_id' => [
                'nullable',
                'integer',
                'exists:m_vendors,id',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 1. CEK LOKASI
        |--------------------------------------------------------------------------
        */

        $location = DB::table('m_locations')
            ->where(
                'id',
                $validated['location_id']
            )
            ->first();

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi tidak ditemukan.',
            ], 422);
        }

        $isOutsourcing =
            strtolower(trim($location->name)) === 'outsourcing';


        /*
        |--------------------------------------------------------------------------
        | 2. VALIDASI VENDOR UNTUK OUTSOURCING
        |--------------------------------------------------------------------------
        */

        if (
            $isOutsourcing &&
            empty($validated['vendor_id'])
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor wajib dipilih untuk lokasi Outsourcing.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | 3. AMBIL ENGINE LARGE FORMAT
        |--------------------------------------------------------------------------
        */

        $engine = DB::table('m_engines')
            ->whereRaw(
                'LOWER(TRIM(name)) = ?',
                ['large format']
            )
            ->whereRaw(
                'LOWER(TRIM(status)) = ?',
                ['active']
            )
            ->first();

        if (!$engine) {
            return response()->json([
                'success' => false,
                'message' => 'Mesin Large Format tidak ditemukan atau tidak aktif.',
            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | 4. AMBIL MATERIAL SIZE
        |--------------------------------------------------------------------------
        */

        $materialSizes = DB::table(
            'm_production_cost_detail_widths as pcdw'
        )

            // Material Size
            ->join(
                'm_material_sizes as ms',
                'ms.id',
                '=',
                'pcdw.material_size_id'
            )

            // Production Cost Detail
            ->join(
                'm_production_cost_details as pcd',
                'pcd.id',
                '=',
                'pcdw.production_cost_detail_id'
            )

            // Production Cost Header
            ->join(
                'm_production_costs as pc',
                'pc.id',
                '=',
                'pcd.production_cost_id'
            )

            // Production Cost Location
            ->join(
                'm_production_cost_locations as pcl',
                'pcl.production_cost_id',
                '=',
                'pc.id'
            )

            // Material
            ->join(
                'm_materials as m',
                'm.id',
                '=',
                'pcd.material_id'
            )

            ->where(
                'pc.engine_id',
                $engine->id
            )

            ->where(
                'pcl.location_id',
                $validated['location_id']
            )

            ->where(
                'm.category_id',
                $validated['category_id']
            )

            ->where(
                'm.id',
                $validated['material_id']
            );


        /*
        |--------------------------------------------------------------------------
        | 5. FILTER VENDOR KHUSUS OUTSOURCING
        |--------------------------------------------------------------------------
        */

        if ($isOutsourcing) {
            $materialSizes->where(
                'pcd.vendor_id',
                $validated['vendor_id']
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 6. HASIL MATERIAL SIZE
        |--------------------------------------------------------------------------
        */

        $materialSizes = $materialSizes
            ->select([
                'ms.id',
                'ms.width',
                'ms.unit',
            ])
            ->distinct()
            ->orderBy(
                'ms.width',
                'asc'
            )
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 7. RESPONSE
        |--------------------------------------------------------------------------
        */

        return response()->json([
            'success' => true,
            'data' => $materialSizes,
        ]);
    }

    public function getUserLaminations(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:m_locations,id',
            'category_id' => 'required|exists:m_categories,id',
        ]);

        try {
            // Ambil Engine Large Format yang aktif
            $engine = DB::table('m_engines')
                ->whereRaw('LOWER(TRIM(name)) = ?', ['large format'])
                ->where('status', 'Active')
                ->first();

            if (!$engine) {
                return response()->json([
                    'success' => false,
                    'message' => 'Engine Large Format tidak ditemukan atau tidak aktif.',
                ], 404);
            }

            $laminations = DB::table('m_lamination_production_cost_details as lpcd')
                ->join('m_production_costs as pc', 'pc.id', '=', 'lpcd.production_cost_id')
                ->join('m_production_cost_locations as pcl', 'pcl.production_cost_id', '=', 'pc.id')
                ->join('m_production_cost_categories as pcc', 'pcc.production_cost_id', '=', 'pc.id')
                ->join('m_laminations as l', 'l.id', '=', 'lpcd.lamination_id')
                ->where('pc.engine_id', $engine->id)
                ->where('pcl.location_id', $request->location_id)
                ->where('pcc.category_id', $request->category_id)
                ->where('l.status', 'Active')
                ->select(
                    'l.id',
                    'l.name'
                )
                ->distinct()
                ->orderBy('l.name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $laminations,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data laminasi.',
            ], 500);
        }
    }

    public function getUserLaminationSizes(Request $request)
    {
        $request->validate([
            'location_id' => 'required|exists:m_locations,id',
            'category_id' => 'required|exists:m_categories,id',
            'lamination_id' => 'required|exists:m_laminations,id',
        ]);

        try {
            // Ambil Engine Large Format yang aktif
            $engine = DB::table('m_engines')
                ->whereRaw('LOWER(TRIM(name)) = ?', ['large format'])
                ->where('status', 'Active')
                ->first();

            if (!$engine) {
                return response()->json([
                    'success' => false,
                    'message' => 'Engine Large Format tidak ditemukan atau tidak aktif.',
                ], 404);
            }

            $sizes = DB::table('m_lamination_production_cost_detail_sizes as lpcds')
                ->join(
                    'm_lamination_production_cost_details as lpcd',
                    'lpcd.id',
                    '=',
                    'lpcds.lamination_production_cost_detail_id'
                )
                ->join(
                    'm_production_costs as pc',
                    'pc.id',
                    '=',
                    'lpcd.production_cost_id'
                )
                ->join(
                    'm_production_cost_locations as pcl',
                    'pcl.production_cost_id',
                    '=',
                    'pc.id'
                )
                ->join(
                    'm_production_cost_categories as pcc',
                    'pcc.production_cost_id',
                    '=',
                    'pc.id'
                )
                ->join(
                    'm_lamination_sizes as ls',
                    'ls.id',
                    '=',
                    'lpcds.lamination_size_id'
                )
                ->where('pc.engine_id', $engine->id)
                ->where('pcl.location_id', $request->location_id)
                ->where('pcc.category_id', $request->category_id)
                ->where('lpcd.lamination_id', $request->lamination_id)
                ->select(
                    'ls.id',
                    'ls.width',
                    'ls.length',
                    'ls.unit'
                )
                ->distinct()
                ->orderBy('ls.width')
                ->orderBy('ls.length')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $sizes,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ukuran laminasi.',
            ], 500);
        }
    }
}

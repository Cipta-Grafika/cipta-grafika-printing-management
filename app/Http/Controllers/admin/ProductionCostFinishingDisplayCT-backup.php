<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductionCostFinishingDisplayCT extends Controller
{
    public function index()
    {
        return view('admin.production-cost-finishing-display.index');
    }

    public function create()
    {
        $data['displayProducts'] = DB::table('m_display_products')
            ->where('status', 'Active')
            ->orderBy('display_name')
            ->get();

        $data['locations'] = DB::table('m_locations')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $data['engines'] = DB::table('m_engines')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();


        return view('admin.production-cost-finishing-display.create')->with($data);
    }

    public function data(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | TOTAL HEADER CONFIGURATION
    |--------------------------------------------------------------------------
    */

        $totalRecords = DB::table('m_display_product_configurations')
            ->count();


        /*
    |--------------------------------------------------------------------------
    | SUBQUERY JUMLAH KOMPONEN
    |--------------------------------------------------------------------------
    */

        $componentQuery = DB::table('m_display_product_components')
            ->select(
                'display_product_id',
                DB::raw("
                COUNT(*) FILTER (
                    WHERE component_type = 'material'
                ) AS material_count
            "),
                DB::raw("
                COUNT(*) FILTER (
                    WHERE component_type = 'lamination'
                ) AS lamination_count
            ")
            )
            ->groupBy('display_product_id');


        /*
    |--------------------------------------------------------------------------
    | QUERY UTAMA HEADER DISPLAY
    |--------------------------------------------------------------------------
    */

        $query = DB::table('m_display_product_configurations as cfg')
            ->join(
                'm_display_products as products',
                'cfg.display_product_id',
                '=',
                'products.id'
            )
            ->leftJoin(
                'm_categories as categories',
                'products.category_id',
                '=',
                'categories.id'
            )
            ->leftJoin(
                'm_locations as locations',
                'cfg.location_id',
                '=',
                'locations.id'
            )
            ->leftJoin(
                'm_engines as engines',
                'cfg.engine_id',
                '=',
                'engines.id'
            )
            ->leftJoin(
                'm_display_product_prices as prices',
                'products.id',
                '=',
                'prices.display_product_id'
            )
            ->leftJoinSub(
                $componentQuery,
                'components',
                function ($join) {
                    $join->on(
                        'products.id',
                        '=',
                        'components.display_product_id'
                    );
                }
            )
            ->select(
                'cfg.id',
                'cfg.display_product_id',
                'cfg.location_id',
                'cfg.engine_id',

                'products.display_name',
                'products.length',
                'products.width',

                'categories.name as category_name',
                'locations.name as location_name',
                'engines.name as engine_name',

                'cfg.cost_rangka',
                'cfg.cost_finishing',
                'cfg.total_cost',

                'prices.general_price',
                'prices.division_price',
                'prices.plain_price',

                DB::raw('COALESCE(components.material_count, 0) AS material_count'),
                DB::raw('COALESCE(components.lamination_count, 0) AS lamination_count')
            );


        /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

        $search = trim($request->input('search.value', ''));

        if ($search !== '') {
            $searchValue = '%' . mb_strtolower($search, 'UTF-8') . '%';

            $query->where(function ($q) use ($searchValue) {
                $q->whereRaw(
                    'LOWER(products.display_name) LIKE ?',
                    [$searchValue]
                )
                    ->orWhereRaw(
                        'LOWER(categories.name) LIKE ?',
                        [$searchValue]
                    )
                    ->orWhereRaw(
                        'LOWER(locations.name) LIKE ?',
                        [$searchValue]
                    )
                    ->orWhereRaw(
                        'LOWER(engines.name) LIKE ?',
                        [$searchValue]
                    )
                    ->orWhereRaw(
                        'CAST(products.length AS TEXT) LIKE ?',
                        [$searchValue]
                    )
                    ->orWhereRaw(
                        'CAST(products.width AS TEXT) LIKE ?',
                        [$searchValue]
                    );
            });
        }


        /*
    |--------------------------------------------------------------------------
    | TOTAL DATA SETELAH FILTER
    |--------------------------------------------------------------------------
    */

        $totalFiltered = (clone $query)->count();


        /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

        $start = max(
            0,
            (int) $request->input('start', 0)
        );

        $length = (int) $request->input('length', 10);

        if ($length !== -1) {
            $length = max(1, min($length, 100));
        }


        /*
    |--------------------------------------------------------------------------
    | SORTING
    |--------------------------------------------------------------------------
    */

        $query->orderByRaw("
        CASE
            WHEN products.status = 'Active' THEN 0
            ELSE 1
        END
    ")
            ->orderBy('products.display_name', 'asc')
            ->orderBy('cfg.id', 'desc');


        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA
    |--------------------------------------------------------------------------
    */

        if ($length === -1) {
            $displays = $query->get();
        } else {
            $displays = $query
                ->offset($start)
                ->limit($length)
                ->get();
        }


        // dd($displays);
        /*
    |--------------------------------------------------------------------------
    | RESPONSE DATATABLES
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $displays,
        ]);
    }

    public function components(Request $request)
    {
        $request->validate([
            'location_id' => 'required|integer',
            'engine_id' => 'required|integer',
        ]);

        $locationId = $request->location_id;
        $engineId = $request->engine_id;

        /*
    |--------------------------------------------------------------------------
    | DATA MATERIAL
    |--------------------------------------------------------------------------
    */

        $materials = DB::table('m_production_cost_details as pcd')
            ->join(
                'm_production_costs as pc',
                'pc.id',
                '=',
                'pcd.production_cost_id'
            )
            ->join(
                'm_production_cost_locations as pcl',
                'pcl.production_cost_id',
                '=',
                'pc.id'
            )
            ->join(
                'm_materials as m',
                'm.id',
                '=',
                'pcd.material_id'
            )
            ->where('pc.engine_id', $engineId)
            ->where('pcl.location_id', $locationId)
            ->where('m.status', 'Active')
            ->select(
                'm.id',
                'm.material_name as name'
            )
            ->distinct()
            ->orderBy('m.material_name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | DATA LAMINASI
    |--------------------------------------------------------------------------
    */

        $laminations = DB::table('m_lamination_production_cost_details as lpcd')
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
                'm_laminations as l',
                'l.id',
                '=',
                'lpcd.lamination_id'
            )
            ->where('pc.engine_id', $engineId)
            ->where('pcl.location_id', $locationId)
            ->where('l.status', 'Active')
            ->select(
                'l.id',
                'l.name'
            )
            ->distinct()
            ->orderBy('l.name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | RESPONSE JSON
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'materials' => $materials,
            'laminations' => $laminations,
        ]);
    }


    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI REQUEST
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'configurations' => [
                'required',
                'array',
                'min:1',
            ],

            'configurations.*.display_product_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'configurations.*.location_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'configurations.*.engine_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'configurations.*.cost_rangka' => [
                'required',
                'numeric',
                'min:0',
            ],

            'configurations.*.cost_finishing' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        DB::beginTransaction();

        try {

            /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

            $userName = Auth::user()->name ?? 'system';

            $configurations = $validated['configurations'];

            /*
        |--------------------------------------------------------------------------
        | VALIDASI DUPLIKASI NAMA DISPLAY PRODUCT DALAM REQUEST
        |--------------------------------------------------------------------------
        |
        | Nama Display Product yang sama dianggap duplikat,
        | meskipun ID, Engine, Location, Cost Rangka,
        | atau Cost Finishing berbeda.
        |
        */

            $displayProductIds = array_map(
                'intval',
                array_column($configurations, 'display_product_id')
            );

            $displayProducts = DB::table('m_display_products')
                ->whereIn('id', $displayProductIds)
                ->get([
                    'id',
                    'display_name',
                ]);

            $displayNamesById = [];

            foreach ($displayProducts as $product) {

                $normalizedName = mb_strtolower(
                    preg_replace(
                        '/\s+/u',
                        ' ',
                        trim($product->display_name)
                    ),
                    'UTF-8'
                );

                $displayNamesById[$product->id] = $normalizedName;
            }

            $processedDisplayNames = [];

            foreach ($configurations as $index => $configuration) {

                $displayProductId =
                    (int) $configuration['display_product_id'];

                $normalizedName =
                    $displayNamesById[$displayProductId] ?? null;

                if ($normalizedName === null) {
                    continue;
                }

                if (isset($processedDisplayNames[$normalizedName])) {

                    throw new \Exception(
                        'Nama Display Product "' .
                            $displayProducts->firstWhere(
                                'id',
                                $displayProductId
                            )->display_name .
                            '" pada Header ' .
                            ($index + 1) .
                            ' duplikat dengan Header ' .
                            $processedDisplayNames[$normalizedName] .
                            '. Setiap Nama Display Product hanya boleh dipilih satu kali.'
                    );
                }

                $processedDisplayNames[$normalizedName] = $index + 1;
            }

            /*
        |--------------------------------------------------------------------------
        | PROSES SETIAP DISPLAY PRODUCT
        |--------------------------------------------------------------------------
        |
        | Setiap Header memiliki:
        |
        | - Display Product
        | - Location
        | - Engine
        | - Cost Rangka
        | - Cost Finishing
        |
        | Harga Umum dan Harga Divisi dihitung berdasarkan
        | Cost Rangka masing-masing Display Product.
        |
        */

            foreach ($configurations as $configuration) {

                $displayProductId =
                    (int) $configuration['display_product_id'];

                $locationId =
                    (int) $configuration['location_id'];

                $engineId =
                    (int) $configuration['engine_id'];

                $costRangka =
                    (float) $configuration['cost_rangka'];

                $costFinishing =
                    (float) $configuration['cost_finishing'];

                /*
            |--------------------------------------------------------------------------
            | DISPLAY PRODUCT
            |--------------------------------------------------------------------------
            */

                $displayProduct = DB::table(
                    'm_display_products'
                )
                    ->where('id', $displayProductId)
                    ->where('status', 'Active')
                    ->first();

                if (!$displayProduct) {

                    throw new \Exception(
                        'Display Product tidak ditemukan atau tidak aktif.'
                    );
                }

                $categoryId = (int) $displayProduct->category_id;

                /*
            |--------------------------------------------------------------------------
            | CEK HARGA RANGKA EXISTING BERDASARKAN NAMA
            |--------------------------------------------------------------------------
            |
            | Jika nama Display Product sudah memiliki
            | Harga Rangka master, penyimpanan ditolak.
            |
            */

                $normalizedCurrentName = mb_strtolower(
                    preg_replace(
                        '/\s+/u',
                        ' ',
                        trim($displayProduct->display_name)
                    ),
                    'UTF-8'
                );

                $existingDisplayPrice = DB::table(
                    'm_display_product_prices as prices'
                )
                    ->join(
                        'm_display_products as products',
                        'products.id',
                        '=',
                        'prices.display_product_id'
                    )
                    ->get([
                        'products.display_name',
                    ])
                    ->contains(function ($product) use (
                        $normalizedCurrentName
                    ) {

                        $existingName = mb_strtolower(
                            preg_replace(
                                '/\s+/u',
                                ' ',
                                trim($product->display_name)
                            ),
                            'UTF-8'
                        );

                        return $existingName === $normalizedCurrentName;
                    });

                if ($existingDisplayPrice) {

                    throw new \Exception(
                        'Harga Rangka untuk Display Product "' .
                            $displayProduct->display_name .
                            '" sudah tersedia.'
                    );
                }

                /*
            |--------------------------------------------------------------------------
            | DISPLAY PRICING RULE - HARGA UMUM
            |--------------------------------------------------------------------------
            */

                $generalRule = DB::table(
                    'm_display_pricing_rules'
                )
                    ->where('engine_id', $engineId)
                    ->where('location_id', $locationId)
                    ->where('category_id', $categoryId)
                    ->where('price_type', 'general')
                    ->where('status', 'Active')
                    ->first();

                if (!$generalRule) {

                    throw new \Exception(
                        'Pricing Rule Harga Umum untuk Display Product "' .
                            $displayProduct->display_name .
                            '" tidak ditemukan.'
                    );
                }

                /*
            |--------------------------------------------------------------------------
            | DISPLAY PRICING RULE - HARGA DIVISI
            |--------------------------------------------------------------------------
            */

                $divisionRule = DB::table(
                    'm_display_pricing_rules'
                )
                    ->where('engine_id', $engineId)
                    ->where('location_id', $locationId)
                    ->where('category_id', $categoryId)
                    ->where('price_type', 'division')
                    ->where('status', 'Active')
                    ->first();

                if (!$divisionRule) {

                    throw new \Exception(
                        'Pricing Rule Harga Divisi untuk Display Product "' .
                            $displayProduct->display_name .
                            '" tidak ditemukan.'
                    );
                }

                /*
            |--------------------------------------------------------------------------
            | GENERAL PRICING RULE
            |--------------------------------------------------------------------------
            */

                $generalMarkup =
                    (float) $generalRule->markup_percentage;

                $generalRounding =
                    (float) ($generalRule->rounding_value ?? 1);

                if ($generalRounding <= 0) {
                    $generalRounding = 1;
                }

                /*
            |--------------------------------------------------------------------------
            | DIVISION PRICING RULE
            |--------------------------------------------------------------------------
            */

                $divisionPercentage =
                    (float) $divisionRule->markup_percentage;

                /*
            |--------------------------------------------------------------------------
            | TOTAL COST HARGA RANGKA
            |--------------------------------------------------------------------------
            |
            | Tidak menggunakan Komponen.
            |
            | Total Cost = Cost Rangka.
            |
            */

                $totalCost = $costRangka;

                /*
            |--------------------------------------------------------------------------
            | HITUNG HARGA UMUM
            |--------------------------------------------------------------------------
            */

                $hargaUmum = (
                    $totalCost *
                    (1 + ($generalMarkup / 100))
                );

                $hargaUmum = ceil(
                    $hargaUmum / $generalRounding
                ) * $generalRounding;

                /*
            |--------------------------------------------------------------------------
            | HITUNG HARGA DIVISI
            |--------------------------------------------------------------------------
            |
            | Mengikuti formula existing:
            |
            | Harga Divisi = Harga Umum x Persentase Divisi
            |
            */

                $hargaDivisi =
                    $hargaUmum *
                    ($divisionPercentage / 100);

                /*
            |--------------------------------------------------------------------------
            | SIMPAN HARGA RANGKA MASTER
            |--------------------------------------------------------------------------
            */

                DB::table(
                    'm_display_product_prices'
                )->insert([

                    'display_product_id' => $displayProductId,

                    'general_price' => $hargaUmum,

                    'division_price' => $hargaDivisi,

                    'plain_price' => null,

                    'created_by' => $userName,

                    'created_at' => now(),

                ]);

                /*
            |--------------------------------------------------------------------------
            | SIMPAN HEADER CONFIGURATION
            |--------------------------------------------------------------------------
            |
            | Tidak ada:
            |
            | - Insert Komponen Display
            | - Insert Harga Komponen
            | - Pencarian harga Material
            | - Pencarian harga Laminasi
            |
            */

                DB::table(
                    'm_display_product_configurations'
                )->insert([

                    'display_product_id' => $displayProductId,

                    'engine_id' => $engineId,

                    'location_id' => $locationId,

                    'cost_rangka' => $costRangka,

                    'cost_finishing' => $costFinishing,

                    'total_cost' => $totalCost,

                    'created_by' => $userName,

                    'created_at' => now(),

                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | COMMIT
        |--------------------------------------------------------------------------
        */

            DB::commit();

            return redirect()
                ->route('admin_pc_finishing_displays')
                ->with(
                    'success',
                    'Harga Rangka dan Konfigurasi Header Display berhasil disimpan.'
                );
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    public function destroy($id)
    {
        $displayProduct = DB::table('m_display_products')
            ->where('id', $id)
            ->first();

        if (!$displayProduct) {
            return response()->json([
                'success' => false,
                'message' => 'Data Display Product tidak ditemukan.'
            ], 404);
        }

        try {

            DB::transaction(function () use ($id) {

                /*
            |--------------------------------------------------------------------------
            | AMBIL ID KONFIGURASI
            |--------------------------------------------------------------------------
            */

                $configurationIds = DB::table(
                    'm_display_product_configurations'
                )
                    ->where('display_product_id', $id)
                    ->pluck('id');

                /*
            |--------------------------------------------------------------------------
            | HAPUS HARGA KOMPONEN
            |--------------------------------------------------------------------------
            */

                if ($configurationIds->isNotEmpty()) {

                    DB::table(
                        'm_display_product_component_prices'
                    )
                        ->whereIn(
                            'configuration_id',
                            $configurationIds
                        )
                        ->delete();
                }

                /*
            |--------------------------------------------------------------------------
            | HAPUS KOMPONEN DISPLAY
            |--------------------------------------------------------------------------
            */

                DB::table('m_display_product_components')
                    ->where('display_product_id', $id)
                    ->delete();

                /*
            |--------------------------------------------------------------------------
            | HAPUS KONFIGURASI DISPLAY
            |--------------------------------------------------------------------------
            */

                DB::table('m_display_product_configurations')
                    ->where('display_product_id', $id)
                    ->delete();

                /*
            |--------------------------------------------------------------------------
            | HAPUS HARGA DISPLAY
            |--------------------------------------------------------------------------
            */

                DB::table('m_display_product_prices')
                    ->where('display_product_id', $id)
                    ->delete();

                //     /*
                // |--------------------------------------------------------------------------
                // | HAPUS GAMBAR DISPLAY
                // |--------------------------------------------------------------------------
                // */

                //     DB::table('m_display_product_images')
                //         ->where('display_product_id', $id)
                //         ->delete();

                //     /*
                // |--------------------------------------------------------------------------
                // | HAPUS MASTER DISPLAY PRODUCT
                // |--------------------------------------------------------------------------
                // */

                //     DB::table('m_display_products')
                //         ->where('id', $id)
                //         ->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Display Product berhasil dihapus.'
            ]);
        } catch (\Exception $e) {

            Log::error(
                'Gagal menghapus Display Product: ' . $e->getMessage(),
                [
                    'display_product_id' => $id
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Display Product gagal dihapus. Data mungkin masih digunakan oleh proses lain.'
            ], 500);
        }
    }

    public function addComponent($id)
    {
        /*
    |--------------------------------------------------------------------------
    | DECODE ID
    |--------------------------------------------------------------------------
    */

        $decodedId = base64_decode($id, true);

        if (
            $decodedId === false ||
            !ctype_digit($decodedId) ||
            (int) $decodedId <= 0
        ) {
            abort(404, 'ID Display Product tidak valid.');
        }

        $data['id'] = (int) $decodedId;


        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA DISPLAY PRODUCT
    |--------------------------------------------------------------------------
    */

        $data['displayProduct'] = DB::table('m_display_products')
            ->leftJoin(
                'm_categories',
                'm_display_products.category_id',
                '=',
                'm_categories.id'
            )
            ->where(
                'm_display_products.id',
                $data['id']
            )
            ->select(
                'm_display_products.id as display_product_id',
                'm_display_products.display_name',
                'm_display_products.length',
                'm_display_products.width',
                'm_display_products.category_id',
                'm_categories.name as category_name'
            )
            ->first();

        if (!$data['displayProduct']) {
            return response()->json([
                'success' => false,
                'message' => 'Data Display Product tidak ditemukan.'
            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | AMBIL SELURUH KONFIGURASI HEADER DISPLAY
    |--------------------------------------------------------------------------
    */

        $data['configurations'] = DB::table(
            'm_display_product_configurations'
        )
            ->join(
                'm_locations',
                'm_display_product_configurations.location_id',
                '=',
                'm_locations.id'
            )
            ->join(
                'm_engines',
                'm_display_product_configurations.engine_id',
                '=',
                'm_engines.id'
            )
            ->where(
                'm_display_product_configurations.display_product_id',
                $data['id']
            )
            ->select(
                'm_display_product_configurations.id as configuration_id',
                'm_display_product_configurations.display_product_id',
                'm_display_product_configurations.cost_rangka',
                'm_display_product_configurations.cost_finishing',
                'm_display_product_configurations.total_cost',
                'm_display_product_configurations.location_id',
                'm_display_product_configurations.engine_id',
                'm_locations.name as location_name',
                'm_engines.name as engine_name'
            )
            ->get();


        /*
    |--------------------------------------------------------------------------
    | AMBIL KONFIGURASI UNTUK TAMPILAN
    |--------------------------------------------------------------------------
    */

        $data['configuration'] = $data['configurations']->first();

        if (!$data['configuration']) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi Header Display tidak ditemukan.'
            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | AMBIL HARGA RANGKA
    |--------------------------------------------------------------------------
    */

        $data['framePrice'] = DB::table(
            'm_display_product_prices'
        )
            ->where(
                'display_product_id',
                $data['displayProduct']->display_product_id
            )
            ->select(
                'general_price',
                'division_price',
                'plain_price'
            )
            ->first();


        /*
    |--------------------------------------------------------------------------
    | TAMPILKAN HALAMAN TAMBAH KOMPONEN
    |--------------------------------------------------------------------------
    */

        return view(
            'admin.production-cost-finishing-display.create-component'
        )->with($data);
    }


    public function storeComponent(Request $request, $id)
    {
        /*
    |--------------------------------------------------------------------------
    | DECODE DISPLAY PRODUCT ID
    |--------------------------------------------------------------------------
    */

        $displayProductId = base64_decode($id, true);

        if (
            $displayProductId === false ||
            !ctype_digit((string) $displayProductId) ||
            (int) $displayProductId <= 0
        ) {
            return back()->with(
                'error',
                'ID Display Product tidak valid.'
            );
        }

        $displayProductId = (int) $displayProductId;

        /*
    |--------------------------------------------------------------------------
    | VALIDASI REQUEST
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'configuration_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'components' => [
                'required',
                'array',
                'min:1',
            ],

            'components.*.material_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'components.*.lamination_id' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'components.*.rounding_value' => [
                'required',
                'numeric',
                'min:0',
            ],
        ], [
            'configuration_id.required' =>
            'Konfigurasi Display wajib dipilih.',

            'components.required' =>
            'Minimal satu komponen harus ditambahkan.',

            'components.*.rounding_value.required' =>
            'Nilai pembulatan wajib diisi.',

            'components.*.rounding_value.numeric' =>
            'Nilai pembulatan harus berupa angka.',

            'components.*.rounding_value.min' =>
            'Nilai pembulatan tidak boleh negatif.',
        ]);

        $userName = Auth::user()->name ?? 'system';
        $now = now();

        try {

            DB::beginTransaction();

            /*
        |--------------------------------------------------------------------------
        | DISPLAY PRODUCT
        |--------------------------------------------------------------------------
        */

            $displayProduct = DB::table('m_display_products')
                ->where('id', $displayProductId)
                ->where('status', 'Active')
                ->first();

            if (!$displayProduct) {
                throw new \Exception(
                    'Display Product tidak ditemukan atau tidak aktif.'
                );
            }

            $categoryId = (int) $displayProduct->category_id;

            /*
        |--------------------------------------------------------------------------
        | KONFIGURASI DISPLAY
        |--------------------------------------------------------------------------
        */

            $configuration = DB::table(
                'm_display_product_configurations'
            )
                ->where('id', $validated['configuration_id'])
                ->where('display_product_id', $displayProductId)
                ->first();

            if (!$configuration) {
                throw new \Exception(
                    'Konfigurasi Display tidak ditemukan.'
                );
            }

            $configurationId = (int) $configuration->id;
            $engineId = (int) $configuration->engine_id;
            $locationId = (int) $configuration->location_id;

            /*
        |--------------------------------------------------------------------------
        | HARGA RANGKA
        |--------------------------------------------------------------------------
        */

            $displayPrice = DB::table('m_display_product_prices')
                ->where('display_product_id', $displayProductId)
                ->first();

            if (!$displayPrice) {
                throw new \Exception(
                    'Harga rangka Display belum tersedia.'
                );
            }

            $hargaRangka = (float) $displayPrice->general_price;

            /*
        |--------------------------------------------------------------------------
        | DISPLAY PRICING RULE
        |--------------------------------------------------------------------------
        */

            $generalRule = DB::table('m_display_pricing_rules')
                ->where('engine_id', $engineId)
                ->where('location_id', $locationId)
                ->where('category_id', $categoryId)
                ->where('price_type', 'general')
                ->where('status', 'Active')
                ->first();

            $divisionRule = DB::table('m_display_pricing_rules')
                ->where('engine_id', $engineId)
                ->where('location_id', $locationId)
                ->where('category_id', $categoryId)
                ->where('price_type', 'division')
                ->where('status', 'Active')
                ->first();

            if (!$generalRule) {
                throw new \Exception(
                    'Pricing Rule Harga Umum Display tidak ditemukan.'
                );
            }

            if (!$divisionRule) {
                throw new \Exception(
                    'Pricing Rule Harga Divisi Display tidak ditemukan.'
                );
            }

            $divisionPercentage =
                (float) $divisionRule->markup_percentage;

            /*
        |--------------------------------------------------------------------------
        | PEMBULATAN
        |--------------------------------------------------------------------------
        */

            $applyRounding = function (
                float $price,
                float $roundingValue
            ): float {

                if ($roundingValue <= 0) {
                    return $price;
                }

                return ceil(
                    $price / $roundingValue
                ) * $roundingValue;
            };

            /*
        |--------------------------------------------------------------------------
        | NORMALISASI NAMA
        |--------------------------------------------------------------------------
        */

            $normalizeName = function ($name): string {

                $name = strtolower(trim((string) $name));

                return preg_replace('/\s+/', ' ', $name);
            };

            /*
        |--------------------------------------------------------------------------
        | IDENTIFIKASI DISPLAY KHUSUS
        |--------------------------------------------------------------------------
        */

            $isRollUpBanner = (
                $normalizeName($displayProduct->display_name)
                === $normalizeName('Roll Up Banner 80 x 200 cm')
            );

            /*
        |--------------------------------------------------------------------------
        | PENCARIAN ID MATERIAL BERDASARKAN NAMA
        |--------------------------------------------------------------------------
        */

            $getMaterialIdByName = function (
                string $targetName
            ) use (
                $normalizeName
            ): int {

                $materials = DB::table('m_materials')
                    ->where('status', 'Active')
                    ->select('id', 'material_name')
                    ->get();

                $material = $materials->first(function ($row) use (
                    $targetName,
                    $normalizeName
                ) {
                    return $normalizeName($row->material_name)
                        === $normalizeName($targetName);
                });

                if (!$material) {
                    throw new \Exception(
                        'Material dasar "' . $targetName .
                            '" tidak ditemukan atau tidak aktif.'
                    );
                }

                return (int) $material->id;
            };

            /*
        |--------------------------------------------------------------------------
        | AMBIL HARGA SUMBER MATERIAL
        |--------------------------------------------------------------------------
        */

            $getMaterialSourcePrice = function (
                int $materialId
            ) use (
                $engineId,
                $locationId
            ) {

                return DB::table('m_production_cost_details as pcd')
                    ->join(
                        'm_production_costs as pc',
                        'pc.id',
                        '=',
                        'pcd.production_cost_id'
                    )
                    ->join(
                        'm_production_cost_locations as pcl',
                        'pcl.production_cost_id',
                        '=',
                        'pc.id'
                    )
                    ->where('pc.engine_id', $engineId)
                    ->where('pcl.location_id', $locationId)
                    ->where('pcd.material_id', $materialId)
                    ->select(
                        'pcd.general_price',
                        'pcd.division_price'
                    )
                    ->first();
            };

            /*
        |--------------------------------------------------------------------------
        | AMBIL HARGA SUMBER LAMINASI
        |--------------------------------------------------------------------------
        */

            $getLaminationSourcePrice = function (
                int $laminationId
            ) use (
                $engineId,
                $locationId
            ) {

                return DB::table(
                    'm_lamination_production_cost_details as lpcd'
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
                    ->where('pc.engine_id', $engineId)
                    ->where('pcl.location_id', $locationId)
                    ->where('lpcd.lamination_id', $laminationId)
                    ->select(
                        'lpcd.general_price',
                        'lpcd.division_price'
                    )
                    ->first();
            };

            /*
        |--------------------------------------------------------------------------
        | AMBIL HARGA MATERIAL DASAR UNTUK RUMUS BERJENJANG
        |--------------------------------------------------------------------------
        */

            $getSavedComponentPrice = function (
                int $sourceId,
                string $componentType
            ) use (
                $displayProductId,
                $configurationId
            ) {

                $component = DB::table(
                    'm_display_product_components'
                )
                    ->where('display_product_id', $displayProductId)
                    ->where('component_type', $componentType)
                    ->where('material_id', $sourceId)
                    ->whereNull('lamination_id')
                    ->first();

                if (!$component) {
                    throw new \Exception(
                        'Komponen dasar untuk rumus berjenjang belum terdaftar.'
                    );
                }

                $savedPrice = DB::table(
                    'm_display_product_component_prices'
                )
                    ->where('configuration_id', $configurationId)
                    ->where('component_id', $component->id)
                    ->first();

                if (!$savedPrice) {
                    throw new \Exception(
                        'Harga Umum komponen dasar belum tersimpan pada konfigurasi ini.'
                    );
                }

                return (float) $savedPrice->general_price;
            };

            /*
        |--------------------------------------------------------------------------
        | LOOP COMPONENT
        |--------------------------------------------------------------------------
        */

            $savedCount = 0;

            foreach ($validated['components'] as $index => $component) {

                $materialId = (int) $component['material_id'];

                $laminationId = !empty($component['lamination_id'])
                    ? (int) $component['lamination_id']
                    : null;

                $roundingValue = (float) $component['rounding_value'];

                if (!$materialId) {
                    throw new \Exception(
                        'Material pada baris ke-' . ($index + 1) .
                            ' wajib dipilih.'
                    );
                }

                /*
            |--------------------------------------------------------------------------
            | VALIDASI MATERIAL
            |--------------------------------------------------------------------------
            */

                $material = DB::table('m_materials')
                    ->where('id', $materialId)
                    ->where('status', 'Active')
                    ->first();

                if (!$material) {
                    throw new \Exception(
                        'Material tidak ditemukan atau tidak aktif.'
                    );
                }

                $materialName = $normalizeName(
                    $material->material_name
                );

                /*
            |--------------------------------------------------------------------------
            | VALIDASI LAMINASI
            |--------------------------------------------------------------------------
            */

                $lamination = null;
                $laminationName = null;

                if ($laminationId) {

                    $lamination = DB::table('m_laminations')
                        ->where('id', $laminationId)
                        ->where('status', 'Active')
                        ->first();

                    if (!$lamination) {
                        throw new \Exception(
                            'Laminasi tidak ditemukan atau tidak aktif.'
                        );
                    }

                    $laminationName = $normalizeName(
                        $lamination->name
                    );
                }

                /*
            |--------------------------------------------------------------------------
            | IDENTIFIKASI RUMUS
            |--------------------------------------------------------------------------
            */

                $formulaType = null;

                $flexy280 = 'flexy frontlite china 280 gsm';
                $flexy340 = 'flexy frontlite china 340 gsm';
                $korchin440 = 'flexy frontlite china korchin 440 gsm';
                $albatros = 'albatros';
                $lushter = 'lushter 180 gsm';

                $isAlbatrosLamination = (
                    $materialName === $albatros &&
                    $laminationId !== null &&
                    str_contains($laminationName, 'laminating') &&
                    str_contains($laminationName, '120 micron')
                );

                /*
            |--------------------------------------------------------------------------
            | VALIDASI KHUSUS ROLL UP BANNER
            |--------------------------------------------------------------------------
            */

                if ($isRollUpBanner) {

                    if ($materialName === $albatros) {

                        if ($laminationId === null) {

                            $formulaType = 'rollup_albatros';
                        } elseif (
                            $isAlbatrosLamination &&
                            (
                                str_contains($laminationName, 'doff') ||
                                str_contains($laminationName, 'glossy')
                            )
                        ) {

                            $formulaType = 'rollup_albatros_lamination';
                        } else {

                            throw new \Exception(
                                'Roll Up Banner 80 x 200 cm hanya memperbolehkan Albatros dengan Laminating Doff atau Glossy 120 Micron.'
                            );
                        }
                    } elseif (
                        $materialName === $lushter &&
                        $laminationId === null
                    ) {

                        $formulaType = 'rollup_lushter';
                    } else {

                        throw new \Exception(
                            'Komponen tidak diperbolehkan untuk Display Roll Up Banner 80 x 200 cm. Komponen yang tersedia hanya Albatros, Albatros dengan Laminating Doff/Glossy 120 Micron, dan Lushter.'
                        );
                    }
                } else {

                    if ($isAlbatrosLamination) {

                        $formulaType = 'albatros_lamination';
                    } elseif ($materialName === $flexy280) {

                        $formulaType = 'flexy280';
                    } elseif ($materialName === $flexy340) {

                        $formulaType = 'flexy340';
                    } elseif ($materialName === $korchin440) {

                        $formulaType = 'korchin440';
                    } elseif ($materialName === $albatros) {

                        $formulaType = 'albatros';
                    } elseif ($materialName === $lushter) {

                        $formulaType = 'lushter 180 gsm';
                    } else {

                        throw new \Exception(
                            'Rumus Harga Umum untuk material "' .
                                $material->material_name .
                                '" belum tersedia.'
                        );
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | MASTER COMPONENT: SATU BARIS UNTUK MATERIAL + LAMINASI
            |--------------------------------------------------------------------------
            |
            | Kombinasi disimpan sebagai:
            | component_type = material
            | material_id    = ID material
            | lamination_id  = ID laminasi, jika ada
            |
            */

                $displayComponentQuery = DB::table(
                    'm_display_product_components'
                )
                    ->where('display_product_id', $displayProductId)
                    ->where('component_type', 'material')
                    ->where('material_id', $materialId);

                if ($laminationId !== null) {

                    $displayComponentQuery->where(
                        'lamination_id',
                        $laminationId
                    );
                } else {

                    $displayComponentQuery->whereNull(
                        'lamination_id'
                    );
                }

                $displayComponent = $displayComponentQuery->first();

                /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKAT KOMBINASI ROLL UP
            |--------------------------------------------------------------------------
            |
            | Mempertahankan aturan sebelumnya:
            | satu kombinasi Albatros + Laminating untuk konfigurasi.
            |
            */

                if (
                    $isRollUpBanner &&
                    $formulaType === 'rollup_albatros_lamination'
                ) {

                    $existingCombination = DB::table(
                        'm_display_product_components as c'
                    )
                        ->join(
                            'm_display_product_component_prices as cp',
                            'cp.component_id',
                            '=',
                            'c.id'
                        )
                        ->where('c.display_product_id', $displayProductId)
                        ->where('c.component_type', 'material')
                        ->where('c.material_id', $materialId)
                        ->whereNotNull('c.lamination_id')
                        ->where('cp.configuration_id', $configurationId)
                        ->exists();

                    if ($existingCombination) {
                        throw new \Exception(
                            'Kombinasi Albatros + Laminating sudah terdaftar pada konfigurasi ini.'
                        );
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | BUAT MASTER JIKA BELUM ADA
            |--------------------------------------------------------------------------
            */


                if (!$displayComponent) {

                    $displayComponentId = DB::table(
                        'm_display_product_components'
                    )->insertGetId([
                        'display_product_id' => $displayProductId,
                        'component_type' => 'material',
                        'material_id' => $materialId,
                        'lamination_id' => $laminationId,
                        'created_by' => $userName,
                        'created_at' => $now,
                    ]);
                } else {

                    $displayComponentId =
                        (int) $displayComponent->id;
                }

                /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKAT HARGA
            |--------------------------------------------------------------------------
            */

                $existingPrice = DB::table(
                    'm_display_product_component_prices'
                )
                    ->where('configuration_id', $configurationId)
                    ->where('component_id', $displayComponentId)
                    ->exists();

                if ($existingPrice) {

                    throw new \Exception(
                        'Komponen yang dipilih sudah terdaftar pada konfigurasi ini.'
                    );
                }

                /*
            |--------------------------------------------------------------------------
            | AMBIL HARGA SUMBER
            |--------------------------------------------------------------------------
            */

                $materialDetail = $getMaterialSourcePrice(
                    $materialId
                );

                if (!$materialDetail) {
                    throw new \Exception(
                        'Harga Material tidak ditemukan untuk konfigurasi ini.'
                    );
                }

                $materialUnitPrice =
                    (float) $materialDetail->general_price;

                $laminationUnitPrice = 0;

                if ($laminationId !== null) {

                    $laminationDetail = $getLaminationSourcePrice(
                        $laminationId
                    );

                    if (!$laminationDetail) {
                        throw new \Exception(
                            'Harga Laminasi tidak ditemukan untuk konfigurasi ini.'
                        );
                    }

                    $laminationUnitPrice =
                        (float) $laminationDetail->general_price;
                }

                /*
            |--------------------------------------------------------------------------
            | HITUNG HARGA UMUM
            |--------------------------------------------------------------------------
            */

                $materialCalculated = 0;
                $laminationCalculated = 0;

                switch ($formulaType) {

                    case 'flexy280':

                        $materialCalculated =
                            1.6 * $materialUnitPrice;

                        $materialCalculated = $applyRounding(
                            $materialCalculated,
                            $roundingValue
                        );

                        $generalPrice =
                            $hargaRangka + $materialCalculated;

                        break;

                    case 'flexy340':

                        $materialCalculated =
                            1.6 * $materialUnitPrice;

                        $materialCalculated = $applyRounding(
                            $materialCalculated,
                            $roundingValue
                        );

                        $materialIdForFlexy280 =
                            $getMaterialIdByName(
                                'Flexy Frontlite China 280 GSM'
                            );

                        $previousPrice = $getSavedComponentPrice(
                            $materialIdForFlexy280,
                            'material'
                        );

                        $generalPrice =
                            $previousPrice + $materialCalculated;

                        break;

                    case 'korchin440':

                        $materialCalculated =
                            1.6 * $materialUnitPrice;

                        $materialCalculated = $applyRounding(
                            $materialCalculated,
                            $roundingValue
                        );

                        $materialIdForFlexy340 =
                            $getMaterialIdByName(
                                'Flexy Frontlite China 340 GSM'
                            );

                        $previousPrice = $getSavedComponentPrice(
                            $materialIdForFlexy340,
                            'material'
                        );

                        $generalPrice =
                            $previousPrice + $materialCalculated;

                        break;

                    case 'albatros':

                    case 'lushter 180 gsm':

                        $materialCalculated =
                            1.6 * 1.2 * $materialUnitPrice;

                        $materialCalculated = $applyRounding(
                            $materialCalculated,
                            $roundingValue
                        );

                        $generalPrice =
                            $hargaRangka + $materialCalculated;

                        break;

                    case 'albatros_lamination':

                        $materialCalculated =
                            1.6 * 1.2 * $materialUnitPrice;

                        $laminationCalculated =
                            1.6 * 1.2 * $laminationUnitPrice;

                        $combinedPrice =
                            $materialCalculated +
                            $laminationCalculated;

                        $combinedPrice = $applyRounding(
                            $combinedPrice,
                            $roundingValue
                        );

                        $generalPrice =
                            $hargaRangka + $combinedPrice;

                        break;

                    case 'rollup_albatros':

                    case 'rollup_lushter':

                        $materialCalculated =
                            2 * 1.2 * $materialUnitPrice;

                        $materialCalculated = $applyRounding(
                            $materialCalculated,
                            $roundingValue
                        );

                        $generalPrice =
                            $materialCalculated + $hargaRangka;

                        break;

                    case 'rollup_albatros_lamination':

                        $materialCalculated =
                            2 * 1.2 * $materialUnitPrice;

                        $laminationCalculated =
                            2 * 1.2 * $laminationUnitPrice;

                        $combinedPrice =
                            $materialCalculated +
                            $hargaRangka +
                            $laminationCalculated;

                        $generalPrice = $applyRounding(
                            $combinedPrice,
                            $roundingValue
                        );

                        break;

                    default:

                        throw new \Exception(
                            'Rumus Harga Umum belum tersedia.'
                        );
                }

                /*
            |--------------------------------------------------------------------------
            | HARGA DIVISI
            |--------------------------------------------------------------------------
            */

                $divisionPrice =
                    $generalPrice * ($divisionPercentage / 100);

                /*
            |--------------------------------------------------------------------------
            | SIMPAN SATU BARIS HARGA KOMPONEN
            |--------------------------------------------------------------------------
            |
            | Harga material dan laminasi sudah digabungkan dalam
            | $generalPrice.
            |
            | Tidak ada lagi insert terpisah untuk laminasi.
            |
            */

                DB::table(
                    'm_display_product_component_prices'
                )->insert([
                    'configuration_id' => $configurationId,
                    'component_id' => $displayComponentId,
                    'general_price' => $generalPrice,
                    'division_price' => $divisionPrice,
                    'plain_price' => null,
                    'rounding_value_component' => $roundingValue,
                    'created_by' => $userName,
                    'created_at' => $now,
                ]);

                $savedCount++;
            }

            /*
        |--------------------------------------------------------------------------
        | COMMIT
        |--------------------------------------------------------------------------
        */

            DB::commit();

            return redirect()
                ->route('admin_pc_finishing_displays')
                ->with(
                    'success',
                    $savedCount . ' komponen berhasil ditambahkan.'
                );
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    public function details($id)
    {
        /*
    |--------------------------------------------------------------------------
    | DECODE ID
    |--------------------------------------------------------------------------
    */

        $decodedId = base64_decode($id, true);

        // dd($decodedId);

        if (
            $decodedId === false ||
            !ctype_digit($decodedId) ||
            (int) $decodedId <= 0
        ) {
            abort(404, 'ID Display Product tidak valid.');
        }

        $data['id'] = (int) $decodedId;


        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA DISPLAY PRODUCT
    |--------------------------------------------------------------------------
    */

        $data['displayProduct'] = DB::table('m_display_products')
            ->leftJoin(
                'm_categories',
                'm_display_products.category_id',
                '=',
                'm_categories.id'
            )
            ->where(
                'm_display_products.id',
                $data['id']
            )
            ->select(
                'm_display_products.id as display_product_id',
                'm_display_products.display_name',
                'm_display_products.length',
                'm_display_products.width',
                'm_display_products.category_id',
                'm_categories.name as category_name'
            )
            ->first();

        if (!$data['displayProduct']) {
            return response()->json([
                'success' => false,
                'message' => 'Data Display Product tidak ditemukan.'
            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | AMBIL SELURUH KONFIGURASI HEADER DISPLAY
    |--------------------------------------------------------------------------
    */

        $data['configurations'] = DB::table(
            'm_display_product_configurations'
        )
            ->join(
                'm_locations',
                'm_display_product_configurations.location_id',
                '=',
                'm_locations.id'
            )
            ->join(
                'm_engines',
                'm_display_product_configurations.engine_id',
                '=',
                'm_engines.id'
            )
            ->where(
                'm_display_product_configurations.display_product_id',
                $data['id']
            )
            ->select(
                'm_display_product_configurations.id as configuration_id',
                'm_display_product_configurations.display_product_id',
                'm_display_product_configurations.cost_rangka',
                'm_display_product_configurations.cost_finishing',
                'm_display_product_configurations.total_cost',
                'm_display_product_configurations.location_id',
                'm_display_product_configurations.engine_id',
                'm_locations.name as location_name',
                'm_engines.name as engine_name'
            )
            ->get();


        /*
    |--------------------------------------------------------------------------
    | AMBIL KONFIGURASI UNTUK TAMPILAN
    |--------------------------------------------------------------------------
    */

        $data['configuration'] = $data['configurations']->first();

        if (!$data['configuration']) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi Header Display tidak ditemukan.'
            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | AMBIL HARGA RANGKA
    |--------------------------------------------------------------------------
    */

        $data['framePrice'] = DB::table(
            'm_display_product_prices'
        )
            ->where(
                'display_product_id',
                $data['displayProduct']->display_product_id
            )
            ->select(
                'general_price',
                'division_price',
                'plain_price'
            )
            ->first();

        /*
|--------------------------------------------------------------------------
| AMBIL DETAIL KOMPONEN DISPLAY
|--------------------------------------------------------------------------
*/

        // $data['components'] = DB::select("
        //     SELECT
        //         dpcfg.id AS configuration_id,
        //         dp.display_name,
        //         dp.length AS display_length,
        //         dp.width AS display_width,
        //         cat.name AS category_name,
        //         l.name AS location_name,
        //         e.name AS engine_name,
        //         dpcfg.cost_rangka,
        //         dpcfg.cost_finishing,
        //         dpcfg.total_cost,

        //         'frame' AS component_type,
        //         'Rangka' AS component_name,

        //         dpp.general_price,
        //         dpp.division_price,
        //         dpp.plain_price

        //     FROM m_display_product_configurations AS dpcfg

        //     INNER JOIN m_display_products AS dp
        //         ON dp.id = dpcfg.display_product_id

        //     LEFT JOIN m_categories AS cat
        //         ON cat.id = dp.category_id

        //     INNER JOIN m_engines AS e
        //         ON e.id = dpcfg.engine_id

        //     INNER JOIN m_locations AS l
        //         ON l.id = dpcfg.location_id

        //     INNER JOIN m_display_product_prices AS dpp
        //         ON dpp.display_product_id = dp.id

        //     WHERE dp.id = :display_product_id

        //     UNION ALL

        //     SELECT
        //         dpcfg.id AS configuration_id,
        //         dp.display_name,
        //         dp.length AS display_length,
        //         dp.width AS display_width,
        //         cat.name AS category_name,
        //         l.name AS location_name,
        //         e.name AS engine_name,
        //         dpcfg.cost_rangka,
        //         dpcfg.cost_finishing,
        //         dpcfg.total_cost,

        //         dpc.component_type,

        //         CASE
        //             WHEN dpc.material_id IS NOT NULL
        //                 AND dpc.lamination_id IS NOT NULL
        //                 THEN CONCAT(
        //                     m.material_name,
        //                     ' + ',
        //                     lam.name
        //                 )

        //             WHEN dpc.material_id IS NOT NULL
        //                 THEN m.material_name

        //             WHEN dpc.lamination_id IS NOT NULL
        //                 THEN lam.name

        //             ELSE '-'
        //         END AS component_name,

        //         cpp.general_price,
        //         cpp.division_price,
        //         cpp.plain_price

        //     FROM m_display_product_configurations AS dpcfg

        //     INNER JOIN m_display_products AS dp
        //         ON dp.id = dpcfg.display_product_id

        //     LEFT JOIN m_categories AS cat
        //         ON cat.id = dp.category_id

        //     INNER JOIN m_engines AS e
        //         ON e.id = dpcfg.engine_id

        //     INNER JOIN m_locations AS l
        //         ON l.id = dpcfg.location_id

        //     INNER JOIN m_display_product_component_prices AS cpp
        //         ON cpp.configuration_id = dpcfg.id

        //     INNER JOIN m_display_product_components AS dpc
        //         ON dpc.id = cpp.component_id

        //     LEFT JOIN m_materials AS m
        //         ON m.id = dpc.material_id

        //     LEFT JOIN m_laminations AS lam
        //         ON lam.id = dpc.lamination_id

        //     WHERE dp.id = :display_product_id

        //     ORDER BY
        //         display_name,
        //         location_name,
        //         engine_name,
        //         component_type,
        //         component_name
        // ", [
        //     'display_product_id' => $data['id']
        // ]);

        $data['components'] = DB::select("
            SELECT
                dpcfg.id AS configuration_id,
                dp.display_name,
                dp.length AS display_length,
                dp.width AS display_width,
                cat.name AS category_name,
                l.name AS location_name,
                e.name AS engine_name,
                dpcfg.cost_rangka,
                dpcfg.cost_finishing,
                dpcfg.total_cost,

                dpc.component_type,

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
                END AS component_name,

                cpp.general_price,
                cpp.division_price,
                cpp.plain_price

            FROM m_display_product_configurations AS dpcfg

            INNER JOIN m_display_products AS dp
                ON dp.id = dpcfg.display_product_id

            LEFT JOIN m_categories AS cat
                ON cat.id = dp.category_id

            INNER JOIN m_engines AS e
                ON e.id = dpcfg.engine_id

            INNER JOIN m_locations AS l
                ON l.id = dpcfg.location_id

            INNER JOIN m_display_product_component_prices AS cpp
                ON cpp.configuration_id = dpcfg.id

            INNER JOIN m_display_product_components AS dpc
                ON dpc.id = cpp.component_id

            LEFT JOIN m_materials AS m
                ON m.id = dpc.material_id

            LEFT JOIN m_laminations AS lam
                ON lam.id = dpc.lamination_id

            WHERE dp.id = :display_product_id
                AND dpc.component_type <> 'frame'

            ORDER BY
                display_name,
                location_name,
                engine_name,
                component_type,
                component_name
        ", [
            'display_product_id' => $data['id']
        ]);

        // dd($data['components']);
        /*
    |--------------------------------------------------------------------------
    | TAMPILKAN HALAMAN TAMBAH KOMPONEN
    |--------------------------------------------------------------------------
    */

        return view('admin.production-cost-finishing-display.details')->with($data);
    }

    public function editComponent($id)
    {
        /*
    |--------------------------------------------------------------------------
    | DECODE ID
    |--------------------------------------------------------------------------
    */

        $decodedId = base64_decode($id, true);

        // dd($decodedId);

        if (
            $decodedId === false ||
            !ctype_digit($decodedId) ||
            (int) $decodedId <= 0
        ) {
            abort(404, 'ID Display Product tidak valid.');
        }

        $data['id'] = (int) $decodedId;

        $data['displayProducts'] = DB::table('m_display_products')
            ->where('status', 'Active')
            ->orderBy('display_name')
            ->get();

        $data['locations'] = DB::table('m_locations')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $data['engines'] = DB::table('m_engines')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA DISPLAY PRODUCT
    |--------------------------------------------------------------------------
    */

        $data['displayProduct'] = DB::table('m_display_products')
            ->leftJoin(
                'm_categories',
                'm_display_products.category_id',
                '=',
                'm_categories.id'
            )
            ->where(
                'm_display_products.id',
                $data['id']
            )
            ->select(
                'm_display_products.id as id',
                'm_display_products.id as display_product_id',
                'm_display_products.display_name',
                'm_display_products.length',
                'm_display_products.width',
                'm_display_products.category_id',
                'm_categories.name as category_name'
            )
            ->first();

        if (!$data['displayProduct']) {
            return response()->json([
                'success' => false,
                'message' => 'Data Display Product tidak ditemukan.'
            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | AMBIL SELURUH KONFIGURASI HEADER DISPLAY
    |--------------------------------------------------------------------------
    */

        $data['configurations'] = DB::table(
            'm_display_product_configurations'
        )
            ->join(
                'm_locations',
                'm_display_product_configurations.location_id',
                '=',
                'm_locations.id'
            )
            ->join(
                'm_engines',
                'm_display_product_configurations.engine_id',
                '=',
                'm_engines.id'
            )
            ->where(
                'm_display_product_configurations.display_product_id',
                $data['id']
            )
            ->select(
                'm_display_product_configurations.id as configuration_id',
                'm_display_product_configurations.display_product_id',
                'm_display_product_configurations.cost_rangka',
                'm_display_product_configurations.cost_finishing',
                'm_display_product_configurations.total_cost',
                'm_display_product_configurations.location_id',
                'm_display_product_configurations.engine_id',
                'm_locations.name as location_name',
                'm_engines.name as engine_name'
            )
            ->get();


        /*
    |--------------------------------------------------------------------------
    | AMBIL KONFIGURASI UNTUK TAMPILAN
    |--------------------------------------------------------------------------
    */

        $data['configuration'] = $data['configurations']->first();

        if (!$data['configuration']) {
            return response()->json([
                'success' => false,
                'message' => 'Konfigurasi Header Display tidak ditemukan.'
            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | AMBIL HARGA RANGKA
    |--------------------------------------------------------------------------
    */

        $data['framePrice'] = DB::table(
            'm_display_product_prices'
        )
            ->where(
                'display_product_id',
                $data['displayProduct']->display_product_id
            )
            ->select(
                'general_price',
                'division_price',
                'plain_price'
            )
            ->first();

        /*
|--------------------------------------------------------------------------
| AMBIL DETAIL KOMPONEN DISPLAY
|--------------------------------------------------------------------------
*/

        $data['components'] = DB::select("
            SELECT
                dpcfg.id AS configuration_id,
                dp.display_name,
                dp.length AS display_length,
                dp.width AS display_width,
                cat.name AS category_name,
                l.name AS location_name,
                e.name AS engine_name,
                dpcfg.cost_rangka,
                dpcfg.cost_finishing,
                dpcfg.total_cost,

                'frame' AS component_type,
                'Rangka' AS component_name,

                dpp.general_price,
                dpp.division_price,
                dpp.plain_price

            FROM m_display_product_configurations AS dpcfg

            INNER JOIN m_display_products AS dp
                ON dp.id = dpcfg.display_product_id

            LEFT JOIN m_categories AS cat
                ON cat.id = dp.category_id

            INNER JOIN m_engines AS e
                ON e.id = dpcfg.engine_id

            INNER JOIN m_locations AS l
                ON l.id = dpcfg.location_id

            INNER JOIN m_display_product_prices AS dpp
                ON dpp.display_product_id = dp.id

            WHERE dp.id = :display_product_id

            UNION ALL

            SELECT
                dpcfg.id AS configuration_id,
                dp.display_name,
                dp.length AS display_length,
                dp.width AS display_width,
                cat.name AS category_name,
                l.name AS location_name,
                e.name AS engine_name,
                dpcfg.cost_rangka,
                dpcfg.cost_finishing,
                dpcfg.total_cost,

                dpc.component_type,

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
                END AS component_name,

                cpp.general_price,
                cpp.division_price,
                cpp.plain_price

            FROM m_display_product_configurations AS dpcfg

            INNER JOIN m_display_products AS dp
                ON dp.id = dpcfg.display_product_id

            LEFT JOIN m_categories AS cat
                ON cat.id = dp.category_id

            INNER JOIN m_engines AS e
                ON e.id = dpcfg.engine_id

            INNER JOIN m_locations AS l
                ON l.id = dpcfg.location_id

            INNER JOIN m_display_product_component_prices AS cpp
                ON cpp.configuration_id = dpcfg.id

            INNER JOIN m_display_product_components AS dpc
                ON dpc.id = cpp.component_id

            LEFT JOIN m_materials AS m
                ON m.id = dpc.material_id

            LEFT JOIN m_laminations AS lam
                ON lam.id = dpc.lamination_id

            WHERE dp.id = :display_product_id

            ORDER BY
                display_name,
                location_name,
                engine_name,
                component_type,
                component_name
        ", [
            'display_product_id' => $data['id']
        ]);

        $data['editComponents'] = DB::table('m_display_product_component_prices as cpp')
            ->join(
                'm_display_product_configurations as dpcfg',
                'dpcfg.id',
                '=',
                'cpp.configuration_id'
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
                'dpcfg.display_product_id',
                $data['id']
            )
            ->where(
                'dpc.component_type',
                'material'
            )
            ->select(
                'dpcfg.id as configuration_id',
                'dpcfg.location_id',
                'dpcfg.engine_id',

                'dpc.id as component_id',
                'dpc.component_type',
                'dpc.material_id',
                'dpc.lamination_id',

                'm.material_name',
                'lam.name as lamination_name',

                'cpp.rounding_value_component',
                'cpp.general_price',
                'cpp.division_price',
                'cpp.plain_price'
            )
            ->orderBy('dpcfg.id')
            ->orderBy('dpc.id')
            ->get();

        return view('admin.production-cost-finishing-display.edit')->with($data);
    }


    public function updateComponent(Request $request, $id)
    {
        $displayProductId = base64_decode($id, true);

        if (
            $displayProductId === false ||
            !is_numeric($displayProductId) ||
            (int) $displayProductId <= 0
        ) {
            abort(404);
        }

        $displayProductId = (int) $displayProductId;

        $validated = $request->validate([
            'display_product_id' => 'required|integer|min:1',

            'configurations' => 'required|array|min:1',

            'configurations.*.configuration_id' => 'required|integer|min:1',
            'configurations.*.location_id' => 'required|integer|min:1',
            'configurations.*.engine_id' => 'required|integer|min:1',
            'configurations.*.cost_rangka' => 'required|numeric|min:0',
            'configurations.*.cost_finishing' => 'required|numeric|min:0',

            'components' => 'nullable|array',
            'components.*' => 'nullable|array',

            'components.*.*.component_id' => 'nullable|integer|min:1',
            'components.*.*.material_id' => 'required|integer|min:1',
            'components.*.*.lamination_id' => 'nullable|integer|min:1',
            'components.*.*.rounding_value' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $userName = Auth::user()->name ?? 'System';

            $targetProductId = (int) $validated['display_product_id'];

            /*
         * 1. Validasi produk asal dan tujuan
         */
            $sourceProduct = DB::table('m_display_products')
                ->where('id', $displayProductId)
                ->first();

            if (!$sourceProduct) {
                throw new \Exception('Data Display Product tidak ditemukan.');
            }

            $targetProduct = DB::table('m_display_products')
                ->where('id', $targetProductId)
                ->where('status', 'Active')
                ->first();

            if (!$targetProduct) {
                throw new \Exception(
                    'Display Product tujuan tidak aktif atau tidak ditemukan.'
                );
            }

            /*
         * Validasi jika produk diganti
         */
            if ($targetProductId !== $displayProductId) {

                $targetHasConfiguration = DB::table('m_display_product_configurations')
                    ->where('display_product_id', $targetProductId)
                    ->exists();

                if ($targetHasConfiguration) {
                    throw new \Exception(
                        'Produk tujuan sudah memiliki konfigurasi. Perubahan produk dibatalkan.'
                    );
                }

                $targetHasPrice = DB::table('m_display_product_prices')
                    ->where('display_product_id', $targetProductId)
                    ->exists();

                if (!$targetHasPrice) {
                    throw new \Exception(
                        'Harga dasar produk tujuan belum tersedia.'
                    );
                }
            }

            /*
         * 2. Harga dasar produk
         */
            $framePrice = DB::table('m_display_product_prices')
                ->where('display_product_id', $targetProductId)
                ->first();

            if (!$framePrice) {
                throw new \Exception(
                    'Harga dasar Display Product tidak ditemukan.'
                );
            }

            $hargaRangka = (float) $framePrice->general_price;

            /*
         * 3. Helper normalisasi nama
         */
            $normalizeName = function ($value) {
                return strtolower(
                    preg_replace('/\s+/', ' ', trim((string) $value))
                );
            };

            /*
         * 4. Helper pembulatan
         */
            $roundPrice = function ($price, $rounding) {
                $price = (float) $price;
                $rounding = (float) $rounding;

                if ($rounding <= 0) {
                    return $price;
                }

                return ceil($price / $rounding) * $rounding;
            };

            /*
         * 5. Validasi konfigurasi
         */
            $configurationIds = [];

            foreach ($validated['configurations'] as $configuration) {
                $configurationIds[] = (int) $configuration['configuration_id'];
            }

            if (
                count($configurationIds) !==
                count(array_unique($configurationIds))
            ) {
                throw new \Exception(
                    'Terdapat konfigurasi duplikat pada form.'
                );
            }

            $existingConfigurations = DB::table('m_display_product_configurations')
                ->where('display_product_id', $displayProductId)
                ->whereIn('id', $configurationIds)
                ->get()
                ->keyBy('id');

            if ($existingConfigurations->count() !== count($configurationIds)) {
                throw new \Exception(
                    'Sebagian konfigurasi tidak ditemukan atau bukan milik produk ini.'
                );
            }

            /*
         * 6. Validasi dan update konfigurasi
         */
            foreach ($validated['configurations'] as $configuration) {

                $configurationId = (int) $configuration['configuration_id'];
                $locationId = (int) $configuration['location_id'];
                $engineId = (int) $configuration['engine_id'];

                $costRangka = (float) $configuration['cost_rangka'];
                $costFinishing = (float) $configuration['cost_finishing'];

                $location = DB::table('m_locations')
                    ->where('id', $locationId)
                    ->where('status', 'Active')
                    ->first();

                $engine = DB::table('m_engines')
                    ->where('id', $engineId)
                    ->where('status', 'Active')
                    ->first();

                if (!$location || !$engine) {
                    throw new \Exception(
                        'Lokasi atau mesin tidak aktif atau tidak ditemukan.'
                    );
                }

                $categoryId = (int) $targetProduct->category_id;

                $pricingRules = DB::table('m_display_pricing_rules')
                    ->where('engine_id', $engineId)
                    ->where('location_id', $locationId)
                    ->where('category_id', $categoryId)
                    ->where('status', 'Active')
                    ->get();

                $generalRule = $pricingRules->firstWhere(
                    'price_type',
                    'general'
                );

                $divisionRule = $pricingRules->firstWhere(
                    'price_type',
                    'division'
                );

                if (!$generalRule || !$divisionRule) {
                    throw new \Exception(
                        'Aturan harga umum atau divisi tidak ditemukan untuk konfigurasi ini.'
                    );
                }

                DB::table('m_display_product_configurations')
                    ->where('id', $configurationId)
                    ->update([
                        'display_product_id' => $targetProductId,
                        'location_id' => $locationId,
                        'engine_id' => $engineId,
                        'cost_rangka' => $costRangka,
                        'cost_finishing' => $costFinishing,
                        'total_cost' => $costRangka,
                        'updated_by' => $userName,
                        'updated_at' => now(),
                    ]);
            }

            /*
         * 7. Proses komponen
         */
            $componentsByConfiguration = $validated['components'] ?? [];

            $processedCombinations = [];

            foreach ($validated['configurations'] as $configuration) {

                $configurationId = (int) $configuration['configuration_id'];
                $locationId = (int) $configuration['location_id'];
                $engineId = (int) $configuration['engine_id'];

                $categoryId = (int) $targetProduct->category_id;

                $configurationComponents =
                    $componentsByConfiguration[$configurationId] ?? [];

                $divisionRule = DB::table('m_display_pricing_rules')
                    ->where('engine_id', $engineId)
                    ->where('location_id', $locationId)
                    ->where('category_id', $categoryId)
                    ->where('price_type', 'division')
                    ->where('status', 'Active')
                    ->first();

                if (!$divisionRule) {
                    throw new \Exception(
                        'Aturan harga divisi tidak ditemukan.'
                    );
                }

                $divisionPercentage = (float) $divisionRule->markup_percentage;

                /*
             * Urutkan komponen berdasarkan formula
             */
                $componentRows = [];

                foreach ($configurationComponents as $row) {

                    $materialId = (int) $row['material_id'];

                    $laminationId = !empty($row['lamination_id'])
                        ? (int) $row['lamination_id']
                        : null;

                    $material = DB::table('m_materials')
                        ->where('id', $materialId)
                        ->where('status', 'Active')
                        ->first();

                    if (!$material) {
                        throw new \Exception(
                            'Material tidak aktif atau tidak ditemukan.'
                        );
                    }

                    $lamination = null;

                    if ($laminationId !== null) {

                        $lamination = DB::table('m_laminations')
                            ->where('id', $laminationId)
                            ->where('status', 'Active')
                            ->first();

                        if (!$lamination) {
                            throw new \Exception(
                                'Laminasi tidak aktif atau tidak ditemukan.'
                            );
                        }
                    }

                    $materialName = $normalizeName(
                        $material->material_name
                    );

                    $laminationName = $normalizeName(
                        $lamination->name ?? ''
                    );

                    $formulaRank = 10;

                    if ($materialName === 'flexy frontlite china 280 gsm') {
                        $formulaRank = 1;
                    } elseif ($materialName === 'flexy frontlite china 340 gsm') {
                        $formulaRank = 2;
                    } elseif ($materialName === 'flexy frontlite china korchin 440 gsm') {
                        $formulaRank = 3;
                    }

                    $componentRows[] = [
                        'row' => $row,
                        'material' => $material,
                        'lamination' => $lamination,
                        'material_name' => $materialName,
                        'lamination_name' => $laminationName,
                        'rank' => $formulaRank,
                    ];
                }

                usort($componentRows, function ($a, $b) {
                    return $a['rank'] <=> $b['rank'];
                });

                foreach ($componentRows as $componentData) {

                    $row = $componentData['row'];

                    $material = $componentData['material'];
                    $lamination = $componentData['lamination'];

                    logger()->debug('DEBUG COMPONENT LAMINATION', [
                        'row' => $row,
                        'lamination_id_raw' => $row['lamination_id'] ?? null,
                        'lamination_id_processed' => $lamination
                            ? $lamination->id
                            : null,
                        'lamination_name' => $lamination->name ?? null,
                    ]);

                    $materialId = (int) $material->id;

                    $laminationId = $lamination
                        ? (int) $lamination->id
                        : null;

                    $materialName = $componentData['material_name'];
                    $laminationName = $componentData['lamination_name'];

                    $rounding = (float) $row['rounding_value'];

                    /*
                 * Cegah kombinasi duplikat
                 */
                    $combinationKey = $configurationId . '-' .
                        $materialId . '-' .
                        ($laminationId ?? 'null');

                    if (isset($processedCombinations[$combinationKey])) {
                        throw new \Exception(
                            'Material dan laminasi yang sama tidak boleh dimasukkan dua kali dalam konfigurasi.'
                        );
                    }

                    $processedCombinations[$combinationKey] = true;

                    /*
                 * Identifikasi material
                 */
                    // $isRollUp =
                    //     strtolower(trim($targetProduct->display_name)) === 'roll up banner' &&
                    //     (float) $targetProduct->width == 80 &&
                    //     (float) $targetProduct->length == 200;

                    $isRollUp =
                        $normalizeName($targetProduct->display_name) ===
                        $normalizeName('Roll Up Banner 80 x 200 cm');

                    $isFlexy280 =
                        $materialName === 'flexy frontlite china 280 gsm';

                    $isFlexy340 =
                        $materialName === 'flexy frontlite china 340 gsm';

                    $isKorchin440 =
                        $materialName === 'flexy frontlite china korchin 440 gsm';

                    $isAlbatros = $materialName === 'albatros';

                    $isLushter = $materialName === 'lushter 180 gsm';

                    /*
                    * Validasi laminasi
                    */
                    if ($laminationId !== null) {

                        if (!$isAlbatros) {
                            throw new \Exception(
                                'Laminasi hanya diperbolehkan untuk material Albatros.'
                            );
                        }

                        $materialNameNormalized = $normalizeName(
                            $material->material_name ?? ''
                        );

                        $laminationNameNormalized = $normalizeName(
                            $lamination->name ?? ''
                        );

                        $isValidLamination =
                            $materialNameNormalized === 'albatros' &&
                            $laminationNameNormalized === 'laminating doff/glossy 120 micron';

                        if (!$isValidLamination) {
                            throw new \Exception(
                                'Albatros hanya diperbolehkan menggunakan Laminating Doff/Glossy 120 Micron. ' .
                                    'Material terbaca: ' . ($material->material_name ?? '-') .
                                    ', Laminasi terbaca: ' . ($lamination->name ?? '-')
                            );
                        }
                    }

                    $isAlbatrosLamination =
                        $isAlbatros && $laminationId !== null;

                    /*
                 * Validasi material yang diperbolehkan
                 */
                    if ($isRollUp) {

                        if (
                            !(
                                ($isAlbatros && $laminationId === null) ||
                                ($isLushter && $laminationId === null) ||
                                $isAlbatrosLamination
                            )
                        ) {
                            throw new \Exception(
                                'Kombinasi material atau laminasi tidak diperbolehkan untuk Roll Up Banner.'
                            );
                        }
                    } else {

                        if (
                            !(
                                $isFlexy280 ||
                                $isFlexy340 ||
                                $isKorchin440 ||
                                $isAlbatros ||
                                $isLushter
                            )
                        ) {
                            throw new \Exception(
                                'Kombinasi material tidak diperbolehkan.'
                            );
                        }
                    }

                    /*
                 * 8. Ambil atau buat master komponen
                 */
                    $componentQuery = DB::table('m_display_product_components')
                        ->where('display_product_id', $targetProductId)
                        ->where('component_type', 'material')
                        ->where('material_id', $materialId);

                    if ($laminationId === null) {
                        $componentQuery->whereNull('lamination_id');
                    } else {
                        $componentQuery->where('lamination_id', $laminationId);
                    }

                    $component = $componentQuery->first();

                    if (!$component) {

                        $componentId = DB::table('m_display_product_components')
                            ->insertGetId([
                                'display_product_id' => $targetProductId,
                                'component_type' => 'material',
                                'material_id' => $materialId,
                                'lamination_id' => $laminationId,
                                'created_by' => $userName,
                                'created_at' => now(),
                                'updated_by' => $userName,
                                'updated_at' => now(),
                            ]);
                    } else {
                        $componentId = (int) $component->id;
                    }

                    /*
                 * 9. Ambil harga material
                 */
                    $materialCost = DB::table('m_production_cost_details as pcd')
                        ->join(
                            'm_production_costs as pc',
                            'pc.id',
                            '=',
                            'pcd.production_cost_id'
                        )
                        ->join(
                            'm_production_cost_locations as pcl',
                            'pcl.production_cost_id',
                            '=',
                            'pc.id'
                        )
                        ->where('pc.engine_id', $engineId)
                        ->where('pcl.location_id', $locationId)
                        ->where('pcd.material_id', $materialId)
                        ->select(
                            'pcd.general_price',
                            'pcd.division_price'
                        )
                        ->first();

                    if (!$materialCost) {
                        throw new \Exception(
                            'Harga material tidak ditemukan pada Production Cost untuk mesin dan lokasi terpilih.'
                        );
                    }

                    /*
                    * 10. Ambil harga laminasi
                    */
                    $laminationCost = null;

                    if ($laminationId !== null) {

                        $laminationCost = DB::table('m_lamination_production_cost_details as lpcd')
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
                            ->where('pc.engine_id', $engineId)
                            ->where('pcl.location_id', $locationId)
                            ->where('lpcd.lamination_id', $laminationId)
                            ->select(
                                'lpcd.general_price',
                                'lpcd.division_price'
                            )
                            ->first();

                        if (!$laminationCost) {
                            throw new \Exception(
                                'Harga laminasi tidak ditemukan untuk mesin dan lokasi terpilih.'
                            );
                        }
                    }

                    $materialSourcePrice =
                        (float) $materialCost->general_price;

                    $laminationSourcePrice = $laminationCost
                        ? (float) $laminationCost->general_price
                        : 0;

                    /*
                    * 11. Perhitungan harga komponen
                    */

                    $materialCalculation = 0;
                    $laminationCalculation = 0;
                    $generalPrice = 0;

                    if ($isRollUp) {

                        if ($isAlbatrosLamination) {

                            $materialCalculation =
                                2 * 1.2 * $materialSourcePrice;

                            $laminationCalculation =
                                2 * 1.2 * $laminationSourcePrice;

                            $combinedCalculation = $roundPrice(
                                $materialCalculation + $laminationCalculation,
                                $rounding
                            );

                            $generalPrice =
                                $hargaRangka + $combinedCalculation;
                        } else {

                            $materialCalculation =
                                2 * 1.2 * $materialSourcePrice;

                            $materialCalculation = $roundPrice(
                                $materialCalculation,
                                $rounding
                            );

                            $generalPrice =
                                $materialCalculation + $hargaRangka;
                        }
                    } elseif ($isFlexy280) {

                        $materialCalculation =
                            1.6 * $materialSourcePrice;

                        $materialCalculation = $roundPrice(
                            $materialCalculation,
                            $rounding
                        );

                        $generalPrice =
                            $hargaRangka + $materialCalculation;
                    } elseif ($isFlexy340) {

                        $materialCalculation =
                            1.6 * $materialSourcePrice;

                        $materialCalculation = $roundPrice(
                            $materialCalculation,
                            $rounding
                        );

                        $baseComponent = DB::table('m_display_product_components as c')
                            ->join(
                                'm_display_product_component_prices as cp',
                                'cp.component_id',
                                '=',
                                'c.id'
                            )
                            ->where('c.display_product_id', $targetProductId)
                            ->where('c.component_type', 'material')
                            ->where('c.material_id', function ($query) {
                                $query->select('id')
                                    ->from('m_materials')
                                    ->whereRaw(
                                        "LOWER(TRIM(material_name)) = ?",
                                        ['flexy frontlite china 280 gsm']
                                    )
                                    ->limit(1);
                            })
                            ->whereNull('c.lamination_id')
                            ->where('cp.configuration_id', $configurationId)
                            ->select('cp.general_price')
                            ->first();

                        if (!$baseComponent) {
                            throw new \Exception(
                                'Harga komponen Flexy 280 belum tersedia pada konfigurasi ini.'
                            );
                        }

                        $generalPrice =
                            (float) $baseComponent->general_price +
                            $materialCalculation;
                    } elseif ($isKorchin440) {

                        $materialCalculation =
                            1.6 * $materialSourcePrice;

                        $materialCalculation = $roundPrice(
                            $materialCalculation,
                            $rounding
                        );

                        $baseComponent = DB::table('m_display_product_components as c')
                            ->join(
                                'm_display_product_component_prices as cp',
                                'cp.component_id',
                                '=',
                                'c.id'
                            )
                            ->where('c.display_product_id', $targetProductId)
                            ->where('c.component_type', 'material')
                            ->where('c.material_id', function ($query) {
                                $query->select('id')
                                    ->from('m_materials')
                                    ->whereRaw(
                                        "LOWER(TRIM(material_name)) = ?",
                                        ['flexy frontlite china 340 gsm']
                                    )
                                    ->limit(1);
                            })
                            ->whereNull('c.lamination_id')
                            ->where('cp.configuration_id', $configurationId)
                            ->select('cp.general_price')
                            ->first();

                        if (!$baseComponent) {
                            throw new \Exception(
                                'Harga komponen Flexy 340 belum tersedia pada konfigurasi ini.'
                            );
                        }

                        $generalPrice =
                            (float) $baseComponent->general_price +
                            $materialCalculation;
                    } elseif ($isAlbatrosLamination) {

                        $materialCalculation =
                            1.6 * 1.2 * $materialSourcePrice;

                        $laminationCalculation =
                            1.6 * 1.2 * $laminationSourcePrice;

                        $combinedCalculation = $roundPrice(
                            $materialCalculation + $laminationCalculation,
                            $rounding
                        );

                        $generalPrice =
                            $hargaRangka + $combinedCalculation;
                    } else {

                        $materialCalculation =
                            1.6 * 1.2 * $materialSourcePrice;

                        $materialCalculation = $roundPrice(
                            $materialCalculation,
                            $rounding
                        );

                        $generalPrice =
                            $hargaRangka + $materialCalculation;
                    }

                    $divisionPrice =
                        $generalPrice * ($divisionPercentage / 100);


                    /*
                 * 12. Simpan harga komponen
                 */
                    $existingPrice = DB::table('m_display_product_component_prices')
                        ->where('configuration_id', $configurationId)
                        ->where('component_id', $componentId)
                        ->first();

                    $priceData = [
                        'general_price' => $generalPrice,
                        'division_price' => $divisionPrice,
                        'plain_price' => null,
                        'rounding_value_component' => $rounding,
                        'updated_by' => $userName,
                        'updated_at' => now(),
                    ];

                    if ($existingPrice) {

                        DB::table('m_display_product_component_prices')
                            ->where('id', $existingPrice->id)
                            ->update($priceData);
                    } else {

                        DB::table('m_display_product_component_prices')
                            ->insert(array_merge(
                                [
                                    'configuration_id' => $configurationId,
                                    'component_id' => $componentId,
                                    'created_by' => $userName,
                                    'created_at' => now(),
                                ],
                                $priceData
                            ));
                    }
                }
            }


            /*
|--------------------------------------------------------------------------
| PART 13: SYNC COMPONENTS
|--------------------------------------------------------------------------
| Menghapus relasi komponen yang tidak lagi dikirim dari frontend.
| Master component hanya dihapus jika sudah tidak digunakan oleh
| konfigurasi mana pun.
*/

            $usedComponentIds = [];

            foreach ($configurationIds as $configurationId) {

                $submittedRows = $componentsByConfiguration[$configurationId] ?? [];

                $configurationUsedIds = [];

                foreach ($submittedRows as $row) {

                    $materialId = (int) ($row['material_id'] ?? 0);

                    $laminationId = !empty($row['lamination_id'])
                        ? (int) $row['lamination_id']
                        : null;

                    if ($materialId <= 0) {
                        continue;
                    }

                    $componentQuery = DB::table('m_display_product_components')
                        ->where('display_product_id', $targetProductId)
                        ->where('component_type', 'material')
                        ->where('material_id', $materialId);

                    if ($laminationId === null) {
                        $componentQuery->whereNull('lamination_id');
                    } else {
                        $componentQuery->where('lamination_id', $laminationId);
                    }

                    $component = $componentQuery->first();

                    if ($component) {
                        $configurationUsedIds[] = (int) $component->id;
                    }
                }

                $configurationUsedIds = array_values(
                    array_unique($configurationUsedIds)
                );

                /*
    |--------------------------------------------------------------------------
    | Hapus relasi harga komponen yang tidak lagi digunakan
    |--------------------------------------------------------------------------
    */

                $deleteQuery = DB::table('m_display_product_component_prices')
                    ->where('configuration_id', $configurationId);

                if (!empty($configurationUsedIds)) {
                    $deleteQuery->whereNotIn(
                        'component_id',
                        $configurationUsedIds
                    );
                }

                $deletedCount = $deleteQuery->delete();

                $usedComponentIds = array_merge(
                    $usedComponentIds,
                    $configurationUsedIds
                );

                logger()->debug('DISPLAY COMPONENT PRICE SYNC', [
                    'configuration_id' => $configurationId,
                    'used_component_ids' => $configurationUsedIds,
                    'deleted_count' => $deletedCount,
                ]);
            }

            /*
|--------------------------------------------------------------------------
| Hapus master component yang sudah tidak memiliki relasi harga
|--------------------------------------------------------------------------
| Komponen yang masih digunakan konfigurasi lain akan tetap dipertahankan.
*/

            $orphanComponentsQuery = DB::table('m_display_product_components')
                ->where('display_product_id', $targetProductId)
                ->where('component_type', 'material')
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('m_display_product_component_prices as cp')
                        ->whereColumn(
                            'cp.component_id',
                            'm_display_product_components.id'
                        );
                });

            $deletedMasterCount = $orphanComponentsQuery->delete();

            logger()->debug('DISPLAY COMPONENT MASTER SYNC', [
                'display_product_id' => $targetProductId,
                'deleted_master_count' => $deletedMasterCount,
            ]);


            /*
         * 14. Pindahkan master komponen jika produk diganti
         */
            if ($targetProductId !== $displayProductId) {

                DB::table('m_display_product_components')
                    ->where('display_product_id', $displayProductId)
                    ->whereIn('id', function ($query) use ($configurationIds) {
                        $query->select('component_id')
                            ->from('m_display_product_component_prices')
                            ->whereIn('configuration_id', $configurationIds);
                    })
                    ->update([
                        'display_product_id' => $targetProductId,
                        'updated_by' => $userName,
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            return redirect()
                ->route('admin_pc_finishing_displays')
                ->with(
                    'success',
                    'Komponen Display Product berhasil diperbarui.'
                );
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}

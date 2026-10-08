<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Imports\ProductionCostsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductionCostsExport;
use Exception;

class ProductionCostCT extends Controller
{
    public function index()
    {
        return view('admin.production-cost.index');
    }

    public function data(Request $request)
    {
        $query = DB::table('m_production_costs as pc')
            ->join('m_locations as l', 'l.id', '=', 'pc.location_id')
            ->join('m_engines as e', 'e.id', '=', 'pc.engine_id')
            ->select([
                'pc.location_id',
                'pc.engine_id',
                'l.name as location_name',
                'e.name as engine_name',
                DB::raw('COUNT(pc.material_id) as material_count'),
            ])
            ->groupBy(
                'pc.location_id',
                'pc.engine_id',
                'l.name',
                'e.name'
            );

        // =========================================================
        // TOTAL DATA
        // =========================================================

        $recordsTotal = DB::table('m_production_costs')
            ->select('location_id', 'engine_id')
            ->groupBy('location_id', 'engine_id')
            ->get()
            ->count();


        // =========================================================
        // SEARCH
        // =========================================================

        $search = $request->input('search.value');

        if (!empty($search)) {
            $search = strtolower($search);

            $query->where(function ($q) use ($search) {
                $q->whereRaw(
                    'LOWER(l.name) LIKE ?',
                    ["%{$search}%"]
                )
                    ->orWhereRaw(
                        'LOWER(e.name) LIKE ?',
                        ["%{$search}%"]
                    );
            });
        }


        // =========================================================
        // TOTAL DATA SETELAH FILTER
        // =========================================================

        $recordsFiltered = $query->get()->count();


        // =========================================================
        // ORDERING
        // =========================================================

        $query->orderBy('l.name', 'asc')
            ->orderBy('e.name', 'asc');


        // =========================================================
        // PAGINATION
        // =========================================================

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if ($length != -1) {
            $query->offset($start)
                ->limit($length);
        }


        // =========================================================
        // GET DATA
        // =========================================================

        $data = $query->get();


        // =========================================================
        // RESPONSE DATATABLE
        // =========================================================

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function create()
    {
        $data['locations'] = DB::table('m_locations')
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        $data['engines'] = DB::table('m_engines')
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        $data['materials'] = DB::table('m_materials')
            ->where('status', 'Active')
            ->orderBy('material_name', 'asc')
            ->get();

        return view('admin.production-cost.create')->with($data);
    }

    public function store(Request $request)
    {
        // =========================================================
        // NORMALISASI FORMAT ANGKA INDONESIA
        // =========================================================
        //
        // Contoh:
        // 9.740,00  -> 9740.00
        // 25.590,00 -> 25590.00
        // =========================================================

        $data = $request->all();

        foreach ($data['items'] ?? [] as $index => $item) {

            foreach (
                [
                    'price_per_meter',
                    'production_cost',
                    'finishing_cost',
                ] as $field
            ) {

                if (
                    isset($data['items'][$index][$field]) &&
                    $data['items'][$index][$field] !== ''
                ) {

                    $value = $data['items'][$index][$field];

                    // Hapus pemisah ribuan
                    $value = str_replace('.', '', $value);

                    // Ubah pemisah desimal Indonesia
                    $value = str_replace(',', '.', $value);

                    $data['items'][$index][$field] = $value;
                }
            }
        }

        $request->merge($data);


        // =========================================================
        // VALIDASI INPUT
        // =========================================================

        $validated = $request->validate([

            // -----------------------------------------------------
            // LOCATION
            // -----------------------------------------------------

            'location_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'location_ids.*' => [
                'required',
                'integer',
                'exists:m_locations,id',
                'distinct',
            ],


            // -----------------------------------------------------
            // ENGINE
            // -----------------------------------------------------

            'engine_id' => [
                'required',
                'integer',
                'exists:m_engines,id',
            ],


            // -----------------------------------------------------
            // ITEMS
            // -----------------------------------------------------

            'items' => [
                'required',
                'array',
                'min:1',
            ],


            // -----------------------------------------------------
            // MATERIAL
            // -----------------------------------------------------

            'items.*.material_id' => [
                'required',
                'integer',
                'exists:m_materials,id',
            ],


            // -----------------------------------------------------
            // WIDTH
            // -----------------------------------------------------

            'items.*.width_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.width_ids.*' => [
                'required',
                'integer',
                'exists:m_material_sizes,id',
                'distinct',
            ],


            // -----------------------------------------------------
            // PRICE PER METER
            // -----------------------------------------------------

            'items.*.price_per_meter' => [
                'required',
                'numeric',
                'min:0',
            ],


            // -----------------------------------------------------
            // PRODUCTION COST
            // -----------------------------------------------------

            'items.*.production_cost' => [
                'required',
                'numeric',
                'min:0',
            ],


            // -----------------------------------------------------
            // FINISHING COST
            // -----------------------------------------------------

            'items.*.finishing_cost' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);


        // =========================================================
        // VALIDASI WIDTH SESUAI DENGAN MATERIAL
        // =========================================================
        //
        // material_size_id yang dipilih harus benar-benar milik
        // material_id pada row tersebut.
        // =========================================================

        foreach ($validated['items'] as $index => $item) {

            $validWidthCount = DB::table('m_material_sizes')
                ->where('material_id', $item['material_id'])
                ->whereIn('id', $item['width_ids'])
                ->count();

            if ($validWidthCount !== count($item['width_ids'])) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Ukuran material pada baris ' .
                            ($index + 1) .
                            ' tidak sesuai dengan material yang dipilih.'
                    );
            }
        }


        // =========================================================
        // SIAPKAN KOMBINASI YANG AKAN DISIMPAN
        // =========================================================
        //
        // Konsep:
        //
        // Location × Material × Width
        //
        // Contoh:
        //
        // 2 Location
        // 2 Material
        // 2 Width
        //
        // menghasilkan:
        //
        // 2 × 2 × 2 = 8 kombinasi.
        // =========================================================

        $configurations = [];

        foreach ($validated['location_ids'] as $locationId) {

            foreach ($validated['items'] as $item) {

                $pricePerMeter =
                    (float) $item['price_per_meter'];

                $productionCost =
                    (float) $item['production_cost'];

                $finishingCost =
                    (float) $item['finishing_cost'];


                // =================================================
                // TOTAL COST
                // =================================================

                $totalCost =
                    (1.2 * $pricePerMeter)
                    + $productionCost
                    + $finishingCost;


                // =================================================
                // HARGA POLOS
                // =================================================
                //
                // price_per_meter × 2
                // dibulatkan ke atas kelipatan Rp1.000
                // =================================================

                $hargaPolosRaw =
                    $pricePerMeter * 2;

                $hargaPolos =
                    ceil($hargaPolosRaw / 1000) * 1000;


                // =================================================
                // HARGA UMUM
                // =================================================
                //
                // Total Cost + 40%
                // dibulatkan ke atas kelipatan Rp1.000
                // =================================================

                $markup = 40;

                $hargaUmumRaw =
                    $totalCost
                    + (
                        $totalCost
                        * ($markup / 100)
                    );

                $hargaUmum =
                    ceil($hargaUmumRaw / 1000) * 1000;


                // =================================================
                // HARGA DIVISI
                // =================================================
                //
                // Harga Umum × 75%
                // dibulatkan ke atas kelipatan Rp1.000
                // =================================================

                $hargaDivisiRaw =
                    $hargaUmum * 0.75;

                $hargaDivisi =
                    ceil($hargaDivisiRaw / 1000) * 1000;


                // =================================================
                // SIMPAN KONFIGURASI PER WIDTH
                // =================================================

                foreach ($item['width_ids'] as $widthId) {

                    $configurations[] = [
                        'location_id' =>
                        $locationId,

                        'material_id' =>
                        $item['material_id'],

                        'width_id' =>
                        $widthId,

                        'price_per_meter' =>
                        $pricePerMeter,

                        'production_cost' =>
                        $productionCost,

                        'finishing_cost' =>
                        $finishingCost,

                        'total_cost' =>
                        $totalCost,

                        'general_price' =>
                        $hargaUmum,

                        'division_price' =>
                        $hargaDivisi,

                        'plain_price' =>
                        $hargaPolos,
                    ];
                }
            }
        }


        // =========================================================
        // CEK DUPLIKAT
        // =========================================================
        //
        // Satu konfigurasi dianggap duplikat jika seluruh:
        //
        // Engine
        // Location
        // Material
        // Width
        // Price per Meter
        // Production Cost
        // Finishing Cost
        //
        // sama.
        //
        // Jika salah satu konfigurasi sudah ada,
        // seluruh transaksi ditolak.
        // =========================================================

        foreach ($configurations as $configuration) {

            $exists = DB::table('m_production_costs as pc')

                // -------------------------------------------------
                // LOCATION
                // -------------------------------------------------

                ->join(
                    'm_production_cost_locations as pcl',
                    'pcl.production_cost_id',
                    '=',
                    'pc.id'
                )

                // -------------------------------------------------
                // DETAIL MATERIAL
                // -------------------------------------------------

                ->join(
                    'm_production_cost_details as pcd',
                    'pcd.production_cost_id',
                    '=',
                    'pc.id'
                )

                // -------------------------------------------------
                // WIDTH
                // -------------------------------------------------

                ->join(
                    'm_production_cost_detail_widths as pcdw',
                    'pcdw.production_cost_detail_id',
                    '=',
                    'pcd.id'
                )

                // -------------------------------------------------
                // ENGINE
                // -------------------------------------------------

                ->where(
                    'pc.engine_id',
                    $validated['engine_id']
                )

                // -------------------------------------------------
                // LOCATION
                // -------------------------------------------------

                ->where(
                    'pcl.location_id',
                    $configuration['location_id']
                )

                // -------------------------------------------------
                // MATERIAL
                // -------------------------------------------------

                ->where(
                    'pcd.material_id',
                    $configuration['material_id']
                )

                // -------------------------------------------------
                // WIDTH
                // -------------------------------------------------

                ->where(
                    'pcdw.material_size_id',
                    $configuration['width_id']
                )

                // -------------------------------------------------
                // PRICE PER METER
                // -------------------------------------------------

                ->where(
                    'pcd.price_per_meter',
                    $configuration['price_per_meter']
                )

                // -------------------------------------------------
                // PRODUCTION COST
                // -------------------------------------------------

                ->where(
                    'pcd.production_cost',
                    $configuration['production_cost']
                )

                // -------------------------------------------------
                // FINISHING COST
                // -------------------------------------------------

                ->where(
                    'pcd.finishing_cost',
                    $configuration['finishing_cost']
                )

                ->exists();


            if ($exists) {

                return back()
                    ->withInput()
                    ->with(
                        'error',
                        'Kombinasi engine, lokasi, material, ukuran, harga material, ongkos produksi, dan ongkos finishing sudah terdaftar.'
                    );
            }
        }


        // =========================================================
        // SIMPAN KE DATABASE
        // =========================================================
        //
        // Semua proses menggunakan satu transaction.
        //
        // Jika satu proses gagal:
        // seluruh data di-rollback.
        // =========================================================

        DB::transaction(function () use (
            $validated,
            $configurations
        ) {

            $now = Carbon::now();
            $user = Auth::user()->name ?? 'System';


            // =====================================================
            // INSERT HEADER
            // =====================================================
            //
            // Satu engine = satu production cost header.
            // =====================================================

            $productionCostId =
                DB::table('m_production_costs')
                ->insertGetId([

                    'engine_id' =>
                    $validated['engine_id'],

                    'created_by' =>
                    $user,

                    'created_at' =>
                    $now,

                    'updated_by' =>
                    $user,

                    'updated_at' =>
                    $now,
                ]);


            // =====================================================
            // INSERT LOCATIONS
            // =====================================================

            foreach ($validated['location_ids'] as $locationId) {

                DB::table('m_production_cost_locations')
                    ->insert([

                        'production_cost_id' =>
                        $productionCostId,

                        'location_id' =>
                        $locationId,

                        'created_by' =>
                        $user,

                        'created_at' =>
                        $now,

                        'updated_by' =>
                        $user,

                        'updated_at' =>
                        $now,
                    ]);
            }


            // =====================================================
            // GROUP ITEMS BERDASARKAN MATERIAL
            // =====================================================
            //
            // Satu material = satu detail.
            //
            // Beberapa width material disimpan pada:
            // m_production_cost_detail_widths
            // =====================================================

            $groupedItems = [];

            foreach ($validated['items'] as $item) {

                $materialId =
                    $item['material_id'];

                $groupedItems[$materialId] = $item;
            }


            // =====================================================
            // INSERT DETAIL
            // =====================================================

            foreach ($groupedItems as $item) {

                $pricePerMeter =
                    (float) $item['price_per_meter'];

                $productionCost =
                    (float) $item['production_cost'];

                $finishingCost =
                    (float) $item['finishing_cost'];


                // -------------------------------------------------
                // TOTAL COST
                // -------------------------------------------------

                $totalCost =
                    (1.2 * $pricePerMeter)
                    + $productionCost
                    + $finishingCost;


                // -------------------------------------------------
                // HARGA POLOS
                // -------------------------------------------------

                $hargaPolos =
                    ceil(
                        ($pricePerMeter * 2) / 1000
                    ) * 1000;


                // -------------------------------------------------
                // HARGA UMUM
                // -------------------------------------------------

                $hargaUmum =
                    ceil(
                        (
                            $totalCost
                            * 1.40
                        ) / 1000
                    ) * 1000;


                // -------------------------------------------------
                // HARGA DIVISI
                // -------------------------------------------------

                $hargaDivisi =
                    ceil(
                        (
                            $hargaUmum * 0.75
                        ) / 1000
                    ) * 1000;


                // -------------------------------------------------
                // INSERT DETAIL
                // -------------------------------------------------

                $detailId =
                    DB::table('m_production_cost_details')
                    ->insertGetId([

                        'production_cost_id' =>
                        $productionCostId,

                        'material_id' =>
                        $item['material_id'],

                        'production_cost' =>
                        $productionCost,

                        'finishing_cost' =>
                        $finishingCost,

                        'total_cost' =>
                        $totalCost,

                        'general_price' =>
                        $hargaUmum,

                        'division_price' =>
                        $hargaDivisi,

                        'plain_price' =>
                        $hargaPolos,

                        'price_per_meter' =>
                        $pricePerMeter,

                        'created_by' =>
                        $user,

                        'created_at' =>
                        $now,

                        'updated_by' =>
                        $user,

                        'updated_at' =>
                        $now,
                    ]);


                // =================================================
                // INSERT WIDTH
                // =================================================

                foreach ($item['width_ids'] as $widthId) {

                    DB::table(
                        'm_production_cost_detail_widths'
                    )->insert([

                        'production_cost_detail_id' =>
                        $detailId,

                        'material_size_id' =>
                        $widthId,

                        'created_by' =>
                        $user,

                        'created_at' =>
                        $now,

                        'updated_by' =>
                        $user,

                        'updated_at' =>
                        $now,
                    ]);
                }
            }
        });


        // =========================================================
        // REDIRECT
        // =========================================================

        return redirect()
            ->route('admin_pc_finishings')
            ->with(
                'success',
                'Ongkos produksi berhasil ditambahkan.'
            );
    }

    public function edit($id)
    {
        $decodedId = base64_decode($id);

        if (!$decodedId || !str_contains($decodedId, '|')) {
            abort(404);
        }

        $data['id'] = $id;

        [$locationId, $engineId] = explode('|', $decodedId);

        $data['locations'] = DB::table('m_locations')
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        $data['engines'] = DB::table('m_engines')
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        $data['materials'] = DB::table('m_materials')
            ->where('status', 'Active')
            ->orderBy('material_name', 'asc')
            ->get();

        $data['location'] = DB::table('m_locations')
            ->where('id', $locationId)
            ->first();

        $data['engine'] = DB::table('m_engines')
            ->where('id', $engineId)
            ->first();

        $data['productionCosts'] = DB::table('m_production_costs as pc')
            ->join('m_materials as m', 'm.id', '=', 'pc.material_id')
            ->where('pc.location_id', $locationId)
            ->where('pc.engine_id', $engineId)
            ->select([
                'pc.id',
                'pc.material_id',
                'pc.production_cost',
                'pc.finishing_cost',
                'm.material_name',
            ])
            ->orderBy('m.material_name', 'asc')
            ->get();

        if (!$data['location'] || !$data['engine'] || $data['productionCosts']->isEmpty()) {
            abort(404);
        }

        return view('admin.production-cost.edit')->with($data);
    }

    public function update(Request $request, $id)
    {
        /*
    |--------------------------------------------------------------------------
    | 1. Decode ID
    |--------------------------------------------------------------------------
    */

        $decodedId = base64_decode($id);

        if (!$decodedId || !str_contains($decodedId, '|')) {
            abort(404);
        }

        [$locationId, $engineId] = explode('|', $decodedId);


        /*
    |--------------------------------------------------------------------------
    | 2. Normalisasi nilai Rupiah
    |--------------------------------------------------------------------------
    |
    | Contoh:
    | 9.740,00 -> 9740.00
    | 25.590,00 -> 25590.00
    |
    */

        $data = $request->all();

        foreach ($data['items'] ?? [] as $index => $item) {

            foreach (
                [
                    'production_cost',
                    'finishing_cost',
                ] as $field
            ) {

                if (
                    isset($data['items'][$index][$field]) &&
                    $data['items'][$index][$field] !== ''
                ) {

                    $value = $data['items'][$index][$field];

                    // Hapus pemisah ribuan
                    $value = str_replace('.', '', $value);

                    // Ubah pemisah desimal Indonesia
                    $value = str_replace(',', '.', $value);

                    $data['items'][$index][$field] = $value;
                }
            }
        }

        $request->merge($data);


        /*
    |--------------------------------------------------------------------------
    | 3. Validasi request
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([

            'location_id' => [
                'required',
                'integer',
                'exists:m_locations,id',
            ],

            'engine_id' => [
                'required',
                'integer',
                'exists:m_engines,id',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.material_id' => [
                'required',
                'integer',
                'exists:m_materials,id',
                'distinct',
            ],

            'items.*.production_cost' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items.*.finishing_cost' => [
                'required',
                'numeric',
                'min:0',
            ],

        ]);


        /*
    |--------------------------------------------------------------------------
    | 4. Pastikan Location + Engine dari form
    |    sesuai dengan ID pada URL
    |--------------------------------------------------------------------------
    */

        if (
            (int) $validated['location_id'] !== (int) $locationId ||
            (int) $validated['engine_id'] !== (int) $engineId
        ) {
            abort(404);
        }


        /*
    |--------------------------------------------------------------------------
    | 5. Pastikan data yang diedit memang ada
    |--------------------------------------------------------------------------
    */

        $existingCosts = DB::table('m_production_costs')
            ->where('location_id', $locationId)
            ->where('engine_id', $engineId)
            ->get();

        if ($existingCosts->isEmpty()) {
            abort(404);
        }


        /*
    |--------------------------------------------------------------------------
    | 6. Simpan perubahan
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use (
            $validated,
            $locationId,
            $engineId,
            $existingCosts
        ) {

            /*
        |--------------------------------------------------------------------------
        | 6A. Material yang dikirim dari form
        |--------------------------------------------------------------------------
        */

            $submittedMaterialIds = collect($validated['items'])
                ->pluck('material_id')
                ->map(fn($id) => (int) $id)
                ->values();


            /*
        |--------------------------------------------------------------------------
        | 6B. Material lama
        |--------------------------------------------------------------------------
        */

            $existingMaterialIds = $existingCosts
                ->pluck('material_id')
                ->map(fn($id) => (int) $id);


            /*
        |--------------------------------------------------------------------------
        | 6C. Hapus material yang sudah tidak ada di form
        |--------------------------------------------------------------------------
        */

            $materialIdsToDelete = $existingMaterialIds
                ->diff($submittedMaterialIds);

            if ($materialIdsToDelete->isNotEmpty()) {

                DB::table('m_production_costs')
                    ->where('location_id', $locationId)
                    ->where('engine_id', $engineId)
                    ->whereIn(
                        'material_id',
                        $materialIdsToDelete->all()
                    )
                    ->delete();
            }


            /*
        |--------------------------------------------------------------------------
        | 6D. Update data lama / Insert data baru
        |--------------------------------------------------------------------------
        */

            foreach ($validated['items'] as $item) {

                $materialId = (int) $item['material_id'];


                /*
            |--------------------------------------------------------------------------
            | AMBIL HARGA MATERIAL SESUAI ROW
            |--------------------------------------------------------------------------
            |
            | Setiap material mempunyai price_per_meter masing-masing.
            |
            */

                $material = DB::table('m_materials')
                    ->select(
                        'id',
                        'price_per_meter'
                    )
                    ->where('id', $materialId)
                    ->first();

                if (!$material) {

                    throw new Exception(
                        'Data material tidak ditemukan.'
                    );
                }


                /*
            |--------------------------------------------------------------------------
            | HARGA MATERIAL / METER
            |--------------------------------------------------------------------------
            */

                $pricePerMeter = (float) $material->price_per_meter;


                /*
            |--------------------------------------------------------------------------
            | ONGKOS PRODUKSI
            |--------------------------------------------------------------------------
            */

                $productionCost =
                    (float) $item['production_cost'];


                /*
            |--------------------------------------------------------------------------
            | ONGKOS FINISHING
            |--------------------------------------------------------------------------
            */

                $finishingCost =
                    (float) $item['finishing_cost'];


                /*
            |--------------------------------------------------------------------------
            | TOTAL COST
            |--------------------------------------------------------------------------
            |
            | Rumus:
            |
            | (1,2 × Harga Material)
            | + Ongkos Produksi
            | + Ongkos Finishing
            |
            */

                $totalCost =
                    1.2
                    * $pricePerMeter
                    + $productionCost
                    + $finishingCost;


                /*
            |--------------------------------------------------------------------------
            | HARGA POLOS
            |--------------------------------------------------------------------------
            |
            | Rumus:
            |
            | Harga Material × 2
            |
            | Kemudian dibulatkan ke atas ke kelipatan Rp1.000.
            |
            */

                $hargaPolosRaw =
                    $pricePerMeter * 2;

                $hargaPolos =
                    ceil($hargaPolosRaw / 1000) * 1000;


                /*
            |--------------------------------------------------------------------------
            | HARGA UMUM
            |--------------------------------------------------------------------------
            |
            | Markup = 40%
            |
            | Rumus:
            |
            | Total Cost + (Total Cost × 40%)
            |
            | Kemudian dibulatkan ke atas ke kelipatan Rp1.000.
            |
            */

                $markup = 40;

                $hargaUmumRaw =
                    $totalCost
                    + (
                        $totalCost
                        * ($markup / 100)
                    );

                $hargaUmum =
                    ceil($hargaUmumRaw / 1000) * 1000;


                /*
            |--------------------------------------------------------------------------
            | HARGA DIVISI
            |--------------------------------------------------------------------------
            |
            | Rumus mengikuti store():
            |
            | Harga Umum × 75%
            |
            | Catatan:
            | Store saat ini TIDAK melakukan pembulatan Rp1.000
            | untuk Harga Divisi.
            |
            */

                $hargaDivisiRaw =
                    $hargaUmum * 0.75;

                $hargaDivisi =
                    $hargaDivisiRaw;


                /*
            |--------------------------------------------------------------------------
            | CEK DATA EXISTING
            |--------------------------------------------------------------------------
            */

                $existing = $existingCosts->firstWhere(
                    'material_id',
                    $materialId
                );


                /*
            |--------------------------------------------------------------------------
            | UPDATE DATA LAMA
            |--------------------------------------------------------------------------
            */

                if ($existing) {

                    DB::table('m_production_costs')
                        ->where('id', $existing->id)
                        ->update([

                            'production_cost' =>
                            $productionCost,

                            'finishing_cost' =>
                            $finishingCost,

                            'total_cost' =>
                            $totalCost,

                            'harga_polos' =>
                            $hargaPolos,

                            'harga_umum' =>
                            $hargaUmum,

                            'harga_divisi' =>
                            $hargaDivisi,

                            'updated_by' =>
                            Auth::user()->name,

                            'updated_at' =>
                            now(),

                        ]);
                }


                /*
            |--------------------------------------------------------------------------
            | INSERT DATA BARU
            |--------------------------------------------------------------------------
            */ else {

                    DB::table('m_production_costs')
                        ->insert([

                            'location_id' =>
                            $locationId,

                            'engine_id' =>
                            $engineId,

                            'material_id' =>
                            $materialId,

                            'production_cost' =>
                            $productionCost,

                            'finishing_cost' =>
                            $finishingCost,

                            'total_cost' =>
                            $totalCost,

                            'harga_polos' =>
                            $hargaPolos,

                            'harga_umum' =>
                            $hargaUmum,

                            'harga_divisi' =>
                            $hargaDivisi,

                            'created_by' =>
                            Auth::user()->name,

                            'created_at' =>
                            now(),

                            'updated_by' =>
                            Auth::user()->name,

                            'updated_at' =>
                            now(),

                        ]);
                }
            }
        });


        /*
    |--------------------------------------------------------------------------
    | 7. Redirect
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('admin_production_costs')
            ->with(
                'success',
                'Ongkos produksi berhasil diperbarui.'
            );
    }




    public function destroy($id)
    {
        /*
    |--------------------------------------------------------------------------
    | DECODE ID
    |--------------------------------------------------------------------------
    */

        $decodedId = base64_decode($id);

        if (!$decodedId || !str_contains($decodedId, '|')) {
            return response()->json([
                'success' => false,
                'message' => 'ID ongkos produksi tidak valid.'
            ], 404);
        }

        [$locationId, $engineId] = explode('|', $decodedId);


        /*
    |--------------------------------------------------------------------------
    | CEK LOKASI
    |--------------------------------------------------------------------------
    */

        $location = DB::table('m_locations')
            ->where('id', $locationId)
            ->first();

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Data lokasi tidak ditemukan.'
            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | CEK ENGINE
    |--------------------------------------------------------------------------
    */

        $engine = DB::table('m_engines')
            ->where('id', $engineId)
            ->first();

        if (!$engine) {
            return response()->json([
                'success' => false,
                'message' => 'Data engine tidak ditemukan.'
            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | CEK ONGKOS PRODUKSI
    |--------------------------------------------------------------------------
    */

        $hasProductionCosts = DB::table('m_production_costs')
            ->where('location_id', $locationId)
            ->where('engine_id', $engineId)
            ->exists();

        if (!$hasProductionCosts) {
            return response()->json([
                'success' => false,
                'message' => 'Data ongkos produksi tidak ditemukan.'
            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | HAPUS SELURUH ONGKOS PRODUKSI
    |--------------------------------------------------------------------------
    */

        DB::table('m_production_costs')
            ->where('location_id', $locationId)
            ->where('engine_id', $engineId)
            ->delete();


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'success' => true,
            'message' => 'Ongkos produksi berhasil dihapus.'
        ]);
    }

    public function details($id)
    {
        $decodedId = base64_decode($id);

        if (!$decodedId || !str_contains($decodedId, '|')) {
            abort(404);
        }

        [$locationId, $engineId] = explode('|', $decodedId);

        $data['id'] = $id;

        $data['location'] = DB::table('m_locations')
            ->where('id', $locationId)
            ->first();

        $data['engine'] = DB::table('m_engines')
            ->where('id', $engineId)
            ->first();

        $data['productionCosts'] = DB::table('m_production_costs as pc')
            ->join('m_materials as m', 'm.id', '=', 'pc.material_id')
            ->where('pc.location_id', $locationId)
            ->where('pc.engine_id', $engineId)
            ->select([
                'pc.id',
                'pc.material_id',
                'pc.production_cost',
                'pc.finishing_cost',
                'pc.total_cost',
                'pc.harga_polos',
                'pc.harga_umum',
                'pc.harga_divisi',
                'pc.created_by',
                'pc.created_at',
                'pc.updated_by',
                'pc.updated_at',
                'm.material_name',
            ])
            ->orderBy('m.material_name', 'asc')
            ->get();

        if (
            !$data['location'] ||
            !$data['engine'] ||
            $data['productionCosts']->isEmpty()
        ) {
            abort(404);
        }

        return view('admin.production-cost.details')->with($data);
    }



    public function export()
    {
        return Excel::download(
            new ProductionCostsExport(),
            'master_ongkos_produksi.xlsx'
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ], [
            'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'file.file' => 'File yang dipilih tidak valid.',
            'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls).',
        ]);

        $import = new ProductionCostsImport();

        Excel::import(
            $import,
            $request->file('file')
        );

        /*
    |--------------------------------------------------------------------------
    | JIKA ADA ERROR
    |--------------------------------------------------------------------------
    */

        if (count($import->errors) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Import dibatalkan. Terdapat data yang tidak valid.',
                'errors' => $import->errors,
                'imported' => 0,
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | TIDAK ADA DATA
    |--------------------------------------------------------------------------
    */

        if ($import->imported === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data ongkos produksi yang valid untuk diimport.',
                'imported' => 0,
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | BERHASIL
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'success' => true,
            'message' => $import->imported . ' data ongkos produksi berhasil diimport.',
            'imported' => $import->imported,
        ]);
    }
}

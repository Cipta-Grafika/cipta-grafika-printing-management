<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OffsetMasterCT extends Controller
{
    /**
     * Tampilkan halaman index Master Offset (Plano, Plat, Cetak, Pricing Rules)
     */
    public function index()
    {
        $data['totalPlano'] = DB::table('m_offset_plano_materials')->count();
        $data['totalPlates'] = DB::table('m_offset_plates')->count();
        $data['totalPrintCosts'] = DB::table('m_offset_print_costs')->count();
        $data['totalPricingRules'] = DB::table('m_offset_pricing_rules')->count();
        $data['totalProducts'] = DB::table('m_offset_products')->count();

        // Ambil kategori Large Format untuk dikecualikan dari Digital Print A3+
        $lfCategoryIds = DB::table('m_categories as c')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('m_materials as m')
                    ->join('m_production_cost_details as pcd', 'pcd.material_id', '=', 'm.id')
                    ->join('m_production_costs as pc', 'pc.id', '=', 'pcd.production_cost_id')
                    ->join('m_engines as e', 'e.id', '=', 'pc.engine_id')
                    ->whereColumn('m.category_id', 'c.id')
                    ->where('e.name', 'ILIKE', '%large format%');
            })
            ->pluck('id')
            ->toArray();

        // Kategori khusus Digital Print A3+
        $data['digitalCategories'] = DB::table('m_categories')
            ->whereNotIn('id', $lfCategoryIds)
            ->orderBy('name', 'asc')
            ->get();

        // Material khusus Digital Print A3+
        $data['digitalMaterials'] = DB::table('m_materials as m')
            ->join('m_categories as c', 'c.id', '=', 'm.category_id')
            ->whereNotIn('m.category_id', $lfCategoryIds)
            ->where('m.status', 'Active')
            ->select('m.id', 'm.material_code', 'm.material_name', 'c.id as category_id', 'c.name as category_name')
            ->orderBy('c.name', 'asc')
            ->orderBy('m.material_name', 'asc')
            ->get();

        return view('admin.offset.index', $data);
    }

    /**
     * DataTables JSON: Master Kertas Plano Offset
     */
    public function dataPlano(Request $request)
    {
        $query = DB::table('m_offset_plano_materials');

        $totalRecords = $query->count();

        $search = $request->input('search.value');
        if (!empty($search)) {
            $searchValue = strtolower($search);
            $query->where(function ($q) use ($searchValue) {
                $q->whereRaw('LOWER(material_name) LIKE ?', ['%' . $searchValue . '%'])
                  ->orWhereRaw('LOWER(material_code) LIKE ?', ['%' . $searchValue . '%'])
                  ->orWhereRaw('LOWER(paper_type) LIKE ?', ['%' . $searchValue . '%'])
                  ->orWhereRaw('LOWER(status) LIKE ?', ['%' . $searchValue . '%']);
            });
        }

        $totalFiltered = $query->count();

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $materials = $query->orderBy('id', 'asc')
            ->offset($start)
            ->limit($length)
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $materials,
        ]);
    }

    /**
     * Helper sanitasi input nilai mata uang (menghapus string Rp, titik ribuan, spasi)
     */
    protected function sanitizeCurrency($value)
    {
        if (is_null($value) || $value === '') {
            return 0;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }
        $clean = preg_replace('/[^\d]/', '', (string) $value);
        return (float) $clean;
    }

    /**
     * Store: Tambah Kertas Plano Baru (Terhubung ke Master Material Digital Print A3+)
     */
    public function storePlano(Request $request)
    {
        if ($request->has('price_per_plano')) {
            $request->merge([
                'price_per_plano' => $this->sanitizeCurrency($request->input('price_per_plano')),
            ]);
        }

        $validated = $request->validate([
            'material_id'     => 'required|exists:m_materials,id',
            'material_code'   => 'nullable|string|max:50',
            'width'           => 'required|numeric|min:1',
            'length'          => 'required|numeric|min:1',
            'gramature'       => 'nullable|integer|min:1',
            'price_per_plano' => 'required|numeric|min:0',
            'status'          => 'required|string|in:active,inactive,Active,Inactive',
        ]);

        $material = DB::table('m_materials as m')
            ->leftJoin('m_categories as c', 'c.id', '=', 'm.category_id')
            ->where('m.id', $validated['material_id'])
            ->select('m.id', 'm.material_code', 'm.material_name', 'c.name as category_name')
            ->first();

        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Material tidak valid atau tidak ditemukan.',
            ], 422);
        }

        $now = Carbon::now();
        $user = Auth::user()->name ?? 'Admin';

        // Deteksi gramatur otomatis jika tidak diisi manual
        $gramature = $validated['gramature'] ?? null;
        if (empty($gramature) && preg_match('/(\d+)\s*(?:gsm|gr|gram)/i', $material->material_name, $matches)) {
            $gramature = (int) $matches[1];
        }

        $code = !empty($validated['material_code']) 
            ? $validated['material_code'] 
            : (!empty($material->material_code) ? $material->material_code : ('PLN-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $material->material_name), 0, 3)) . ($gramature ?? '')));

        $id = DB::table('m_offset_plano_materials')->insertGetId([
            'material_id'     => $material->id,
            'material_code'   => $code,
            'material_name'   => $material->material_name,
            'width'           => $validated['width'],
            'length'          => $validated['length'],
            'gramature'       => $gramature,
            'paper_type'      => $material->category_name ?? 'Art Paper',
            'price_per_plano' => $validated['price_per_plano'],
            'status'          => strtolower($validated['status']),
            'created_by'      => $user,
            'created_at'      => $now,
            'updated_by'      => $user,
            'updated_at'      => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Kertas Plano \"{$material->material_name}\" berhasil ditambahkan!",
            'id'      => $id,
        ]);
    }

    /**
     * Delete: Hapus Kertas Plano
     */
    public function destroyPlano($id)
    {
        $item = DB::table('m_offset_plano_materials')->where('id', $id)->first();
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Data kertas plano tidak ditemukan.',
            ], 404);
        }

        DB::table('m_offset_plano_materials')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => "Kertas Plano \"{$item->material_name}\" berhasil dihapus!",
        ]);
    }

    /**
     * DataTables JSON: Master Plat CTP Offset
     */
    public function dataPlates(Request $request)
    {
        $query = DB::table('m_offset_plates');

        $totalRecords = $query->count();

        $search = $request->input('search.value');
        if (!empty($search)) {
            $searchValue = strtolower($search);
            $query->where(function ($q) use ($searchValue) {
                $q->whereRaw('LOWER(plate_name) LIKE ?', ['%' . $searchValue . '%'])
                  ->orWhereRaw('LOWER(plate_code) LIKE ?', ['%' . $searchValue . '%'])
                  ->orWhereRaw('LOWER(machine_name) LIKE ?', ['%' . $searchValue . '%'])
                  ->orWhereRaw('LOWER(status) LIKE ?', ['%' . $searchValue . '%']);
            });
        }

        $totalFiltered = $query->count();

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $plates = $query->orderBy('id', 'asc')
            ->offset($start)
            ->limit($length)
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $plates,
        ]);
    }

    /**
     * Store: Tambah Plat CTP Baru
     */
    public function storePlate(Request $request)
    {
        if ($request->has('price_per_plate')) {
            $request->merge([
                'price_per_plate' => $this->sanitizeCurrency($request->input('price_per_plate')),
            ]);
        }

        $validated = $request->validate([
            'plate_code'      => 'nullable|string|max:50',
            'plate_name'      => 'required|string|max:150',
            'machine_name'    => 'nullable|string|max:100',
            'price_per_plate' => 'required|numeric|min:0',
            'status'          => 'required|string|in:active,inactive,Active,Inactive',
        ]);

        $now = Carbon::now();
        $user = Auth::user()->name ?? 'Admin';

        if (empty($validated['plate_code'])) {
            $validated['plate_code'] = 'PLT-' . strtoupper(substr(uniqid(), -4));
        }

        $id = DB::table('m_offset_plates')->insertGetId([
            'plate_code'      => $validated['plate_code'],
            'plate_name'      => $validated['plate_name'],
            'machine_name'    => $validated['machine_name'] ?? 'SM 52 / Oliver 58',
            'price_per_plate' => $validated['price_per_plate'],
            'status'          => strtolower($validated['status']),
            'created_by'      => $user,
            'created_at'      => $now,
            'updated_by'      => $user,
            'updated_at'      => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Plat CTP \"{$validated['plate_name']}\" berhasil ditambahkan!",
            'id'      => $id,
        ]);
    }

    /**
     * Delete: Hapus Plat CTP
     */
    public function destroyPlate($id)
    {
        $item = DB::table('m_offset_plates')->where('id', $id)->first();
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Data plat CTP tidak ditemukan.',
            ], 404);
        }

        DB::table('m_offset_plates')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => "Plat CTP \"{$item->plate_name}\" berhasil dihapus!",
        ]);
    }

    /**
     * DataTables JSON: Master Parameter Ongkos Cetak & Mesin Offset
     */
    public function dataPrintCosts(Request $request)
    {
        $query = DB::table('m_offset_print_costs');

        $totalRecords = $query->count();

        $search = $request->input('search.value');
        if (!empty($search)) {
            $searchValue = strtolower($search);
            $query->where(function ($q) use ($searchValue) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . $searchValue . '%'])
                  ->orWhereRaw('LOWER(status) LIKE ?', ['%' . $searchValue . '%']);
            });
        }

        $totalFiltered = $query->count();

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $printCosts = $query->orderBy('id', 'asc')
            ->offset($start)
            ->limit($length)
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $printCosts,
        ]);
    }

    /**
     * Store: Tambah Profil Parameter Cetak & Mesin Offset
     */
    public function storePrintCost(Request $request)
    {
        $merge = [];
        if ($request->has('click_rate_per_color')) {
            $merge['click_rate_per_color'] = $this->sanitizeCurrency($request->input('click_rate_per_color'));
        }
        if ($request->has('min_print_per_color')) {
            $merge['min_print_per_color'] = $this->sanitizeCurrency($request->input('min_print_per_color'));
        }
        if ($request->has('cutting_fee_per_rim')) {
            $merge['cutting_fee_per_rim'] = $this->sanitizeCurrency($request->input('cutting_fee_per_rim'));
        }
        if (!empty($merge)) {
            $request->merge($merge);
        }

        $validated = $request->validate([
            'name'                   => 'required|string|max:150',
            'press_sheet_width'      => 'required|numeric|min:1',
            'press_sheet_length'     => 'required|numeric|min:1',
            'default_insheet_sheets' => 'required|integer|min:0',
            'click_rate_per_color'   => 'required|numeric|min:0',
            'min_print_per_color'    => 'required|numeric|min:0',
            'cutting_fee_per_rim'    => 'required|numeric|min:0',
            'status'                 => 'required|string|in:active,inactive,Active,Inactive',
        ]);

        $now = Carbon::now();
        $user = Auth::user()->name ?? 'Admin';

        $id = DB::table('m_offset_print_costs')->insertGetId([
            'name'                   => $validated['name'],
            'press_sheet_width'      => $validated['press_sheet_width'],
            'press_sheet_length'     => $validated['press_sheet_length'],
            'default_insheet_sheets' => $validated['default_insheet_sheets'],
            'click_rate_per_color'   => $validated['click_rate_per_color'],
            'min_print_per_color'    => $validated['min_print_per_color'],
            'cutting_fee_per_rim'    => $validated['cutting_fee_per_rim'],
            'status'                 => strtolower($validated['status']),
            'created_by'             => $user,
            'created_at'             => $now,
            'updated_by'             => $user,
            'updated_at'             => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Profil Mesin \"{$validated['name']}\" berhasil ditambahkan!",
            'id'      => $id,
        ]);
    }

    /**
     * Delete: Hapus Profil Parameter Cetak Offset
     */
    public function destroyPrintCost($id)
    {
        $item = DB::table('m_offset_print_costs')->where('id', $id)->first();
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Profil mesin cetak tidak ditemukan.',
            ], 404);
        }

        DB::table('m_offset_print_costs')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => "Profil Mesin \"{$item->name}\" berhasil dihapus!",
        ]);
    }

    /**
     * DataTables JSON: Master Pricing Rules Offset
     */
    public function dataPricingRules(Request $request)
    {
        $query = DB::table('m_offset_pricing_rules');

        $totalRecords = $query->count();

        $search = $request->input('search.value');
        if (!empty($search)) {
            $searchValue = strtolower($search);
            $query->where(function ($q) use ($searchValue) {
                $q->whereRaw('LOWER(rule_name) LIKE ?', ['%' . $searchValue . '%'])
                  ->orWhereRaw('LOWER(category_name) LIKE ?', ['%' . $searchValue . '%'])
                  ->orWhereRaw('LOWER(status) LIKE ?', ['%' . $searchValue . '%']);
            });
        }

        $totalFiltered = $query->count();

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $rules = $query->orderBy('id', 'asc')
            ->offset($start)
            ->limit($length)
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $rules,
        ]);
    }

    /**
     * DataTables JSON: Master Produk Cetak Offset (Brosur / Flyer)
     */
    public function dataProducts(Request $request)
    {
        $query = DB::table('m_offset_products');

        $totalRecords = $query->count();

        $search = $request->input('search.value');
        if (!empty($search)) {
            $searchValue = strtolower($search);
            $query->where(function ($q) use ($searchValue) {
                $q->whereRaw('LOWER(product_name) LIKE ?', ['%' . $searchValue . '%'])
                  ->orWhereRaw('LOWER(product_code) LIKE ?', ['%' . $searchValue . '%'])
                  ->orWhereRaw('LOWER(category) LIKE ?', ['%' . $searchValue . '%'])
                  ->orWhereRaw('LOWER(status) LIKE ?', ['%' . $searchValue . '%']);
            });
        }

        $totalFiltered = $query->count();

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $products = $query->orderBy('id', 'asc')
            ->offset($start)
            ->limit($length)
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $products,
        ]);
    }

    /**
     * Store: Tambah Master Produk Cetak Offset (Brosur / Flyer)
     */
    public function storeProduct(Request $request)
    {
        $merge = [];
        foreach (['final_width', 'final_length', 'print_sheet_width', 'print_sheet_length'] as $field) {
            if ($request->has($field) && is_string($request->input($field))) {
                $merge[$field] = str_replace(',', '.', trim($request->input($field)));
            }
        }
        // Sinkronisasi otomatis Qty Pokok Lembar per Rim (Kolom F Excel: 125, 250) dengan Up per Lembar Cetak
        if ($request->has('sheet_per_rim') && (int)$request->input('sheet_per_rim') > 0) {
            $sheetPerRim = (int) $request->input('sheet_per_rim');
            $upPerSheet = (int) round(500 / $sheetPerRim);
            if ($upPerSheet < 1) $upPerSheet = 1;
            $merge['sheet_per_rim'] = $sheetPerRim;
            $merge['up_per_sheet'] = $request->input('up_per_sheet', $upPerSheet) ?: $upPerSheet;
        } elseif ($request->has('up_per_sheet') && (int)$request->input('up_per_sheet') > 0) {
            $upPerSheet = (int) $request->input('up_per_sheet');
            $sheetPerRim = (int) ceil(500 / $upPerSheet);
            $merge['up_per_sheet'] = $upPerSheet;
            $merge['sheet_per_rim'] = $request->input('sheet_per_rim', $sheetPerRim) ?: $sheetPerRim;
        } else {
            $merge['sheet_per_rim'] = 125;
            $merge['up_per_sheet'] = 4;
        }

        if (!empty($merge)) {
            $request->merge($merge);
        }

        $validated = $request->validate([
            'product_code'       => 'nullable|string|max:50|unique:m_offset_products,product_code',
            'product_name'       => 'required|string|max:150',
            'category'           => 'nullable|string|max:100',
            'final_width'        => 'required|numeric|min:0.1',
            'final_length'       => 'required|numeric|min:0.1',
            'print_sheet_width'  => 'required|numeric|min:0.1',
            'print_sheet_length' => 'required|numeric|min:0.1',
            'sheet_per_rim'      => 'required|integer|min:1',
            'up_per_sheet'       => 'required|integer|min:1',
            'plano_cut_yield'    => 'required|integer|min:1',
            'description'        => 'nullable|string|max:500',
            'status'             => 'required|string|in:active,inactive,Active,Inactive',
        ]);

        // Auto-generate product code if empty
        if (empty($validated['product_code'])) {
            $cat = !empty($validated['category']) ? $validated['category'] : 'BRS';
            $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $cat), 0, 3));
            if (empty($prefix)) {
                $prefix = 'PRD';
            }
            $nextNum = DB::table('m_offset_products')->count() + 1;
            $validated['product_code'] = $prefix . '-' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        }

        $now = Carbon::now();
        $user = Auth::user()->name ?? 'Admin';

        $id = DB::table('m_offset_products')->insertGetId([
            'product_code'       => $validated['product_code'],
            'product_name'       => $validated['product_name'],
            'category'           => $validated['category'] ?? 'Brosur',
            'final_width'        => $validated['final_width'],
            'final_length'       => $validated['final_length'],
            'print_sheet_width'  => $validated['print_sheet_width'],
            'print_sheet_length' => $validated['print_sheet_length'],
            'sheet_per_rim'      => $validated['sheet_per_rim'],
            'up_per_sheet'       => $validated['up_per_sheet'],
            'plano_cut_yield'    => $validated['plano_cut_yield'],
            'description'        => $validated['description'] ?? null,
            'status'             => strtolower($validated['status']),
            'created_by'         => $user,
            'created_at'         => $now,
            'updated_by'         => $user,
            'updated_at'         => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Produk Cetak \"{$validated['product_name']}\" berhasil ditambahkan!",
            'id'      => $id,
        ]);
    }

    /**
     * Store: Tambah Pricing Rule Offset
     */
    public function storePricingRule(Request $request)
    {
        if ($request->has('rounding_value')) {
            $request->merge([
                'rounding_value' => $this->sanitizeCurrency($request->input('rounding_value')),
            ]);
        }

        $validated = $request->validate([
            'rule_name'       => 'required|string|max:150',
            'category_name'   => 'nullable|string|max:100',
            'min_rim'         => 'required|integer|min:1',
            'max_rim'         => 'required|integer|gte:min_rim',
            'markup_general'  => 'required|numeric|min:0',
            'markup_division' => 'required|numeric|min:0',
            'rounding_value'  => 'required|numeric|min:1',
            'status'          => 'required|string|in:active,inactive,Active,Inactive',
        ]);

        $now = Carbon::now();
        $user = Auth::user()->name ?? 'Admin';

        $id = DB::table('m_offset_pricing_rules')->insertGetId([
            'rule_name'       => $validated['rule_name'],
            'category_name'   => $validated['category_name'] ?? 'Brosur',
            'min_rim'         => $validated['min_rim'],
            'max_rim'         => $validated['max_rim'],
            'markup_general'  => $validated['markup_general'],
            'markup_division' => $validated['markup_division'],
            'rounding_value'  => $validated['rounding_value'],
            'status'          => strtolower($validated['status']),
            'created_by'      => $user,
            'created_at'      => $now,
            'updated_by'      => $user,
            'updated_at'      => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Pricing Rule \"{$validated['rule_name']}\" berhasil ditambahkan!",
            'id'      => $id,
        ]);
    }

    /**
     * Delete: Hapus Pricing Rule Offset
     */
    public function destroyPricingRule($id)
    {
        $item = DB::table('m_offset_pricing_rules')->where('id', $id)->first();
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Pricing rule tidak ditemukan.',
            ], 404);
        }

        DB::table('m_offset_pricing_rules')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => "Pricing Rule \"{$item->rule_name}\" berhasil dihapus!",
        ]);
    }

    /**
     * Delete: Hapus Produk Cetak Offset
     */
    public function destroyProduct($id)
    {
        $item = DB::table('m_offset_products')->where('id', $id)->first();
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Produk cetak tidak ditemukan.',
            ], 404);
        }

        DB::table('m_offset_products')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => "Produk Cetak \"{$item->product_name}\" berhasil dihapus!",
        ]);
    }

    /**
     * Update: Edit Kertas Plano
     */
    public function updatePlano(Request $request, $id)
    {
        $item = DB::table('m_offset_plano_materials')->where('id', $id)->first();
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Data kertas plano tidak ditemukan.',
            ], 404);
        }

        if ($request->has('price_per_plano')) {
            $request->merge([
                'price_per_plano' => $this->sanitizeCurrency($request->input('price_per_plano')),
            ]);
        }

        $validated = $request->validate([
            'material_id'     => 'nullable|exists:m_materials,id',
            'material_code'   => 'nullable|string|max:50',
            'width'           => 'required|numeric|min:1',
            'length'          => 'required|numeric|min:1',
            'gramature'       => 'nullable|integer|min:1',
            'price_per_plano' => 'required|numeric|min:0',
            'status'          => 'required|string|in:active,inactive,Active,Inactive',
        ]);

        if (!empty($validated['material_id'])) {
            $material = DB::table('m_materials as m')
                ->leftJoin('m_categories as c', 'c.id', '=', 'm.category_id')
                ->where('m.id', $validated['material_id'])
                ->select('m.id', 'm.material_code', 'm.material_name', 'c.name as category_name')
                ->first();

            if (!$material) {
                return response()->json([
                    'success' => false,
                    'message' => 'Material tidak valid.',
                ], 422);
            }
        } else {
            $material = (object) [
                'id'            => $item->material_id,
                'material_code' => $item->material_code,
                'material_name' => $item->material_name,
                'category_name' => $item->paper_type,
            ];
        }

        $now = Carbon::now();
        $user = Auth::user()->name ?? 'Admin';

        $gramature = $validated['gramature'] ?? null;
        if (empty($gramature) && preg_match('/(\d+)\s*(?:gsm|gr|gram)/i', $material->material_name, $matches)) {
            $gramature = (int) $matches[1];
        }

        $code = !empty($validated['material_code'])
            ? $validated['material_code']
            : ($item->material_code ?: (!empty($material->material_code) ? $material->material_code : ('PLN-' . strtoupper(substr(preg_replace('/[^a-zA-Z0-9]/', '', $material->material_name), 0, 3)) . ($gramature ?? ''))));

        DB::table('m_offset_plano_materials')->where('id', $id)->update([
            'material_id'     => $material->id,
            'material_code'   => $code,
            'material_name'   => $material->material_name,
            'width'           => $validated['width'],
            'length'          => $validated['length'],
            'gramature'       => $gramature,
            'paper_type'      => $material->category_name ?? 'Art Paper',
            'price_per_plano' => $validated['price_per_plano'],
            'status'          => strtolower($validated['status']),
            'updated_by'      => $user,
            'updated_at'      => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Kertas Plano \"{$material->material_name}\" berhasil diperbarui!",
        ]);
    }

    /**
     * Update: Edit Plat CTP
     */
    public function updatePlate(Request $request, $id)
    {
        $item = DB::table('m_offset_plates')->where('id', $id)->first();
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Data plat CTP tidak ditemukan.',
            ], 404);
        }

        if ($request->has('price_per_plate')) {
            $request->merge([
                'price_per_plate' => $this->sanitizeCurrency($request->input('price_per_plate')),
            ]);
        }

        $validated = $request->validate([
            'plate_code'      => 'nullable|string|max:50',
            'plate_name'      => 'required|string|max:150',
            'machine_name'    => 'nullable|string|max:100',
            'price_per_plate' => 'required|numeric|min:0',
            'status'          => 'required|string|in:active,inactive,Active,Inactive',
        ]);

        $now = Carbon::now();
        $user = Auth::user()->name ?? 'Admin';

        DB::table('m_offset_plates')->where('id', $id)->update([
            'plate_code'      => $validated['plate_code'] ?? $item->plate_code,
            'plate_name'      => $validated['plate_name'],
            'machine_name'    => $validated['machine_name'] ?? 'SM 52 / Oliver 58',
            'price_per_plate' => $validated['price_per_plate'],
            'status'          => strtolower($validated['status']),
            'updated_by'      => $user,
            'updated_at'      => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Plat CTP \"{$validated['plate_name']}\" berhasil diperbarui!",
        ]);
    }

    /**
     * Update: Edit Profil Mesin & Parameter Cetak Offset
     */
    public function updatePrintCost(Request $request, $id)
    {
        $item = DB::table('m_offset_print_costs')->where('id', $id)->first();
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Profil mesin tidak ditemukan.',
            ], 404);
        }

        $merge = [];
        if ($request->has('click_rate_per_color')) {
            $merge['click_rate_per_color'] = $this->sanitizeCurrency($request->input('click_rate_per_color'));
        }
        if ($request->has('min_print_per_color')) {
            $merge['min_print_per_color'] = $this->sanitizeCurrency($request->input('min_print_per_color'));
        }
        if ($request->has('cutting_fee_per_rim')) {
            $merge['cutting_fee_per_rim'] = $this->sanitizeCurrency($request->input('cutting_fee_per_rim'));
        }
        if (!empty($merge)) {
            $request->merge($merge);
        }

        $validated = $request->validate([
            'name'                   => 'required|string|max:150',
            'press_sheet_width'      => 'required|numeric|min:1',
            'press_sheet_length'     => 'required|numeric|min:1',
            'default_insheet_sheets' => 'required|integer|min:0',
            'click_rate_per_color'   => 'required|numeric|min:0',
            'min_print_per_color'    => 'required|numeric|min:0',
            'cutting_fee_per_rim'    => 'required|numeric|min:0',
            'status'                 => 'required|string|in:active,inactive,Active,Inactive',
        ]);

        $now = Carbon::now();
        $user = Auth::user()->name ?? 'Admin';

        DB::table('m_offset_print_costs')->where('id', $id)->update([
            'name'                   => $validated['name'],
            'press_sheet_width'      => $validated['press_sheet_width'],
            'press_sheet_length'     => $validated['press_sheet_length'],
            'default_insheet_sheets' => $validated['default_insheet_sheets'],
            'click_rate_per_color'   => $validated['click_rate_per_color'],
            'min_print_per_color'    => $validated['min_print_per_color'],
            'cutting_fee_per_rim'    => $validated['cutting_fee_per_rim'],
            'status'                 => strtolower($validated['status']),
            'updated_by'             => $user,
            'updated_at'             => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Profil Mesin \"{$validated['name']}\" berhasil diperbarui!",
        ]);
    }

    /**
     * Update: Edit Pricing Rule Offset
     */
    public function updatePricingRule(Request $request, $id)
    {
        $item = DB::table('m_offset_pricing_rules')->where('id', $id)->first();
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Pricing rule tidak ditemukan.',
            ], 404);
        }

        if ($request->has('rounding_value')) {
            $request->merge([
                'rounding_value' => $this->sanitizeCurrency($request->input('rounding_value')),
            ]);
        }

        $validated = $request->validate([
            'rule_name'       => 'required|string|max:150',
            'category_name'   => 'nullable|string|max:100',
            'min_rim'         => 'required|integer|min:1',
            'max_rim'         => 'required|integer|gte:min_rim',
            'markup_general'  => 'required|numeric|min:0',
            'markup_division' => 'required|numeric|min:0',
            'rounding_value'  => 'required|numeric|min:1',
            'status'          => 'required|string|in:active,inactive,Active,Inactive',
        ]);

        $now = Carbon::now();
        $user = Auth::user()->name ?? 'Admin';

        DB::table('m_offset_pricing_rules')->where('id', $id)->update([
            'rule_name'       => $validated['rule_name'],
            'category_name'   => $validated['category_name'] ?? 'Brosur',
            'min_rim'         => $validated['min_rim'],
            'max_rim'         => $validated['max_rim'],
            'markup_general'  => $validated['markup_general'],
            'markup_division' => $validated['markup_division'],
            'rounding_value'  => $validated['rounding_value'],
            'status'          => strtolower($validated['status']),
            'updated_by'      => $user,
            'updated_at'      => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Pricing Rule \"{$validated['rule_name']}\" berhasil diperbarui!",
        ]);
    }

    /**
     * Update: Edit Produk Cetak Offset (Brosur / Flyer)
     */
    public function updateProduct(Request $request, $id)
    {
        $item = DB::table('m_offset_products')->where('id', $id)->first();
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Produk cetak tidak ditemukan.',
            ], 404);
        }

        $merge = [];
        foreach (['final_width', 'final_length', 'print_sheet_width', 'print_sheet_length'] as $field) {
            if ($request->has($field) && is_string($request->input($field))) {
                $merge[$field] = str_replace(',', '.', trim($request->input($field)));
            }
        }

        // Sinkronisasi otomatis Qty Pokok Lembar per Rim (Kolom F Excel) dengan Up per Lembar Cetak
        if ($request->has('sheet_per_rim') && (int)$request->input('sheet_per_rim') > 0) {
            $sheetPerRim = (int) $request->input('sheet_per_rim');
            $upPerSheet = (int) round(500 / $sheetPerRim);
            if ($upPerSheet < 1) $upPerSheet = 1;
            $merge['sheet_per_rim'] = $sheetPerRim;
            $merge['up_per_sheet'] = $request->input('up_per_sheet', $upPerSheet) ?: $upPerSheet;
        } elseif ($request->has('up_per_sheet') && (int)$request->input('up_per_sheet') > 0) {
            $upPerSheet = (int) $request->input('up_per_sheet');
            $sheetPerRim = (int) ceil(500 / $upPerSheet);
            $merge['up_per_sheet'] = $upPerSheet;
            $merge['sheet_per_rim'] = $request->input('sheet_per_rim', $sheetPerRim) ?: $sheetPerRim;
        }

        if (!empty($merge)) {
            $request->merge($merge);
        }

        $validated = $request->validate([
            'product_code'       => 'nullable|string|max:50|unique:m_offset_products,product_code,' . $id,
            'product_name'       => 'required|string|max:150',
            'category'           => 'nullable|string|max:100',
            'final_width'        => 'required|numeric|min:0.1',
            'final_length'       => 'required|numeric|min:0.1',
            'print_sheet_width'  => 'required|numeric|min:0.1',
            'print_sheet_length' => 'required|numeric|min:0.1',
            'sheet_per_rim'      => 'required|integer|min:1',
            'up_per_sheet'       => 'required|integer|min:1',
            'plano_cut_yield'    => 'required|integer|min:1',
            'description'        => 'nullable|string|max:500',
            'status'             => 'required|string|in:active,inactive,Active,Inactive',
        ]);

        $now = Carbon::now();
        $user = Auth::user()->name ?? 'Admin';

        DB::table('m_offset_products')->where('id', $id)->update([
            'product_code'       => $validated['product_code'] ?: $item->product_code,
            'product_name'       => $validated['product_name'],
            'category'           => $validated['category'] ?? 'Brosur',
            'final_width'        => $validated['final_width'],
            'final_length'       => $validated['final_length'],
            'print_sheet_width'  => $validated['print_sheet_width'],
            'print_sheet_length' => $validated['print_sheet_length'],
            'sheet_per_rim'      => $validated['sheet_per_rim'],
            'up_per_sheet'       => $validated['up_per_sheet'],
            'plano_cut_yield'    => $validated['plano_cut_yield'],
            'description'        => $validated['description'] ?? null,
            'status'             => strtolower($validated['status']),
            'updated_by'         => $user,
            'updated_at'         => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Produk Cetak \"{$validated['product_name']}\" berhasil diperbarui!",
        ]);
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Exports\LaminationPricingRulesExport;
use App\Exports\DigitalLaminationPricingRulesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LaminationPricingRulesImport;
use App\Imports\DigitalLaminationPricingRulesImport;

class LaminationPricingRuleCT extends Controller
{
    public function index()
    {
        $data['engines'] = DB::table('m_engines')->orderBy('name')->where('status', 'Active')->get();
        $data['locations'] = DB::table('m_locations')
            ->orderBy('name')->where('status', 'Active')->get();

        $data['categories'] = DB::table('m_categories')
            ->orderBy('name')->get();

        $data['vendors'] = DB::table('m_vendors')
            ->orderBy('name')->where('status', 'Active')->get();

        return view('admin.lamination-pricing-rule.index')->with($data);
    }

    public function data(Request $request)
    {
        $validated = $request->validate([
            'pricing_engine' => ['nullable', 'in:large_format,digital_print_a3'],
        ]);

        $pricingEngine = $validated['pricing_engine'] ?? 'large_format';

        if ($pricingEngine === 'digital_print_a3') {
            $query = DB::table('m_digital_lamination_pricing_rules as a')
                ->join('m_engines as d', 'a.engine_id', '=', 'd.id')
                ->join('m_categories as b', 'a.category_id', '=', 'b.id')
                ->join('m_locations as c', 'a.location_id', '=', 'c.id')
                ->leftJoin('m_vendors as v', 'a.vendor_id', '=', 'v.id')
                ->leftJoin(
                    'm_digital_lamination_pricing_rule_details as drd',
                    'drd.digital_lamination_pricing_rule_id',
                    '=',
                    'a.id'
                )
                ->whereRaw('LOWER(d.name) = ?', ['digital print a3+'])
                ->select(
                    'a.id as digital_pricing_rule_id',
                    'd.id as engine_id',
                    'd.name as engine_name',
                    'b.id as category_id',
                    'b.name as category_name',
                    'c.id as location_id',
                    'c.name as location_name',
                    'v.id as vendor_id',
                    'v.name as vendor_name',
                    DB::raw('COUNT(drd.id) as jumlah_markup'),
                    DB::raw("'digital' as pricing_rule_source")
                )
                ->groupBy(
                    'a.id',
                    'd.id',
                    'd.name',
                    'b.id',
                    'b.name',
                    'c.id',
                    'c.name',
                    'v.id',
                    'v.name'
                );
        } else {
            $query = DB::table('m_lamination_pricing_rules as a')
                ->leftJoin('m_engines as d', 'a.engine_id', '=', 'd.id')
                ->leftJoin('m_categories as b', 'a.category_id', '=', 'b.id')
                ->leftJoin('m_locations as c', 'a.location_id', '=', 'c.id')
                ->leftJoin('m_vendors as v', 'a.vendor_id', '=', 'v.id')
                ->whereRaw('LOWER(d.name) = ?', ['large format'])
                ->select(
                    DB::raw('NULL as digital_pricing_rule_id'),
                    'd.id as engine_id',
                    'd.name as engine_name',
                    'b.id as category_id',
                    'b.name as category_name',
                    'c.id as location_id',
                    'c.name as location_name',
                    'v.id as vendor_id',
                    'v.name as vendor_name',
                    DB::raw('COUNT(a.id) as jumlah_markup'),
                    DB::raw("'large_format' as pricing_rule_source")
                )
                ->groupBy(
                    'd.id',
                    'd.name',
                    'b.id',
                    'b.name',
                    'c.id',
                    'c.name',
                    'v.id',
                    'v.name'
                );
        }

        $totalRecords = (clone $query)->get()->count();

        /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */
        $search = $request->input('search.value');

        if (!empty($search)) {
            $search = strtolower($search);

            $query->where(function ($q) use ($search) {
                $q->whereRaw(
                    'LOWER(d.name) LIKE ?',
                    ['%' . $search . '%']
                )
                    ->orWhereRaw(
                        'LOWER(b.name) LIKE ?',
                        ['%' . $search . '%']
                    )
                    ->orWhereRaw(
                        'LOWER(c.name) LIKE ?',
                        ['%' . $search . '%']
                    )
                    ->orWhereRaw(
                        'LOWER(v.name) LIKE ?',
                        ['%' . $search . '%']
                    );
            });
        }

        /*
    |--------------------------------------------------------------------------
    | TOTAL DATA SETELAH FILTER
    |--------------------------------------------------------------------------
    */
        $totalFiltered = $query
            ->get()
            ->count();

        /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA
    |--------------------------------------------------------------------------
    */
        $pricingRules = $query
            ->orderBy('d.name', 'asc')
            ->orderBy('c.name', 'asc')
            ->orderBy('v.name', 'asc')
            ->orderBy('b.name', 'asc')
            ->offset($start)
            ->limit($length)
            ->get();

        /*
    |--------------------------------------------------------------------------
    | RESPONSE DATATABLES
    |--------------------------------------------------------------------------
    */
        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $pricingRules,
        ]);
    }

    public function storeDigital(Request $request)
    {
        $tierRanges = [
            'tier_1' => [1, 1],
            'tier_2_5' => [2, 5],
            'tier_6_50' => [6, 50],
            'tier_100' => [51, 100],
            'tier_300' => [101, 300],
            'tier_500' => [301, 500],
        ];

        $request->validate([
            'pricing_rules' => ['required', 'array', 'min:1'],
            'pricing_rules.*.category_ids' => ['required', 'array', 'min:1'],
            'pricing_rules.*.category_ids.*' => ['required', 'integer', 'exists:m_categories,id'],
        ], [
            'pricing_rules.required' => 'Minimal harus ada satu markup laminasi Digital Print.',
            'pricing_rules.min' => 'Minimal harus ada satu markup laminasi Digital Print.',
            'pricing_rules.*.category_ids.required' => 'Minimal pilih satu kategori.',
            'pricing_rules.*.category_ids.min' => 'Minimal pilih satu kategori.',
            'pricing_rules.*.category_ids.*.exists' => 'Kategori yang dipilih tidak ditemukan.',
        ]);

        // Satu baris per kategori terpilih, detail markup tetap sama
        $expandedRules = [];

        foreach ($request->input('pricing_rules') as $rule) {
            foreach (array_unique($rule['category_ids']) as $categoryId) {
                $expanded = $rule;
                unset($expanded['category_ids']);
                $expanded['category_id'] = $categoryId;
                $expandedRules[] = $expanded;
            }
        }

        $request->merge(['pricing_rules' => $expandedRules]);

        $rules = [
            'pricing_rules' => ['required', 'array', 'min:1'],
            'pricing_rules.*.engine_id' => ['required', 'integer', 'exists:m_engines,id'],
            'pricing_rules.*.location_id' => ['required', 'integer', 'exists:m_locations,id'],
            'pricing_rules.*.vendor_id' => ['nullable', 'integer', 'exists:m_vendors,id'],
            'pricing_rules.*.category_id' => ['required', 'integer', 'exists:m_categories,id'],
            'pricing_rules.*.price_type' => ['required', 'in:general,division'],
            'pricing_rules.*.print_type' => ['required', 'in:single,double'],
            'pricing_rules.*.status' => ['required', 'in:Active,Inactive'],
        ];

        foreach (array_keys($tierRanges) as $tier) {
            $rules["pricing_rules.*.{$tier}_markup_percentage"] = [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ];
            $rules["pricing_rules.*.{$tier}_rounding_value"] = [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ];
        }

        $validated = $request->validate($rules, [
            'pricing_rules.required' => 'Minimal harus ada satu markup laminasi Digital Print.',
            'pricing_rules.min' => 'Minimal harus ada satu markup laminasi Digital Print.',
            'pricing_rules.*.engine_id.required' => 'Mesin wajib dipilih.',
            'pricing_rules.*.engine_id.exists' => 'Mesin yang dipilih tidak ditemukan.',
            'pricing_rules.*.location_id.required' => 'Lokasi wajib dipilih.',
            'pricing_rules.*.location_id.exists' => 'Lokasi yang dipilih tidak ditemukan.',
            'pricing_rules.*.vendor_id.exists' => 'Vendor yang dipilih tidak ditemukan.',
            'pricing_rules.*.category_id.required' => 'Kategori wajib dipilih.',
            'pricing_rules.*.category_id.exists' => 'Kategori yang dipilih tidak ditemukan.',
            'pricing_rules.*.price_type.required' => 'Tipe harga wajib dipilih.',
            'pricing_rules.*.price_type.in' => 'Tipe harga Digital Print hanya General atau Division.',
            'pricing_rules.*.print_type.required' => 'Harga print wajib dipilih.',
            'pricing_rules.*.print_type.in' => 'Harga print tidak valid.',
            'pricing_rules.*.status.required' => 'Status wajib dipilih.',
            'pricing_rules.*.status.in' => 'Status tidak valid.',
        ]);

        try {
            DB::transaction(function () use ($validated, $tierRanges) {
                $pricingRules = $validated['pricing_rules'];

                $engines = DB::table('m_engines')
                    ->whereIn('id', collect($pricingRules)->pluck('engine_id')->unique())
                    ->get(['id', 'name', 'status'])
                    ->keyBy('id');
                $locations = DB::table('m_locations')
                    ->whereIn('id', collect($pricingRules)->pluck('location_id')->unique())
                    ->get(['id', 'name', 'status'])
                    ->keyBy('id');
                $categories = DB::table('m_categories')
                    ->whereIn('id', collect($pricingRules)->pluck('category_id')->unique())
                    ->get(['id', 'name'])
                    ->keyBy('id');
                $vendors = DB::table('m_vendors')
                    ->whereIn('id', collect($pricingRules)->pluck('vendor_id')->filter()->unique())
                    ->get(['id', 'name', 'status'])
                    ->keyBy('id');

                $headerGroups = [];
                $detailCombinations = [];

                foreach ($pricingRules as $index => $rule) {
                    $row = $index + 1;
                    $engine = $engines[$rule['engine_id']] ?? null;
                    $location = $locations[$rule['location_id']] ?? null;
                    $category = $categories[$rule['category_id']] ?? null;

                    if (!$engine || !$location || !$category) {
                        throw new Exception(
                            "Mesin, lokasi, atau kategori pada baris {$row} tidak ditemukan."
                        );
                    }

                    $normalizedEngine = strtolower(preg_replace('/\s+/', '', trim($engine->name)));

                    if ($normalizedEngine !== 'digitalprinta3+') {
                        throw new Exception(
                            "Store laminasi Digital Print hanya menerima mesin Digital Print A3+ (baris {$row})."
                        );
                    }

                    if ($engine->status !== 'Active') {
                        throw new Exception("Mesin pada baris {$row} tidak aktif.");
                    }

                    if ($location->status !== 'Active') {
                        throw new Exception("Lokasi pada baris {$row} tidak aktif.");
                    }

                    $normalizedCategory = strtolower(preg_replace('/[\s\/]+/', '', trim($category->name)));

                    if (in_array($normalizedCategory, ['indoor&outdoor', 'indooroutdoor'], true)) {
                        throw new Exception(
                            "Store laminasi Digital Print tidak menerima kategori Indoor/Outdoor (baris {$row})."
                        );
                    }

                    $vendorId = isset($rule['vendor_id']) && $rule['vendor_id'] !== ''
                        ? (int) $rule['vendor_id']
                        : null;

                    if (strtolower(trim($location->name)) === 'outsourcing') {
                        $vendor = $vendorId !== null ? ($vendors[$vendorId] ?? null) : null;

                        if (!$vendor || $vendor->status !== 'Active') {
                            throw new Exception(
                                "Vendor aktif wajib dipilih untuk lokasi Outsourcing pada baris {$row}."
                            );
                        }
                    } elseif ($vendorId !== null) {
                        throw new Exception(
                            "Vendor hanya dapat dipilih untuk lokasi Outsourcing pada baris {$row}."
                        );
                    }

                    $headerKey = implode('|', [
                        (int) $rule['engine_id'],
                        (int) $rule['location_id'],
                        $vendorId ?? 'null',
                        (int) $rule['category_id'],
                    ]);

                    $headerGroups[$headerKey] = [
                        'engine_id' => (int) $rule['engine_id'],
                        'location_id' => (int) $rule['location_id'],
                        'vendor_id' => $vendorId,
                        'category_id' => (int) $rule['category_id'],
                    ];

                    $detailKey = $headerKey . '|' . $rule['price_type'] . '|' . $rule['print_type'];

                    if (isset($detailCombinations[$detailKey])) {
                        throw new Exception(
                            "Tipe harga dan jenis print duplikat ditemukan pada baris {$row}."
                        );
                    }

                    $detailCombinations[$detailKey] = true;
                }

                foreach ($headerGroups as $header) {
                    $exists = DB::table('m_digital_lamination_pricing_rules')
                        ->where('engine_id', $header['engine_id'])
                        ->where('location_id', $header['location_id'])
                        ->where('category_id', $header['category_id'])
                        ->when(
                            $header['vendor_id'] === null,
                            fn($query) => $query->whereNull('vendor_id'),
                            fn($query) => $query->where('vendor_id', $header['vendor_id'])
                        )
                        ->exists();

                    if ($exists) {
                        throw new Exception(
                            'Konfigurasi laminasi Digital Print untuk kombinasi mesin, lokasi, vendor, dan kategori tersebut sudah terdaftar.'
                        );
                    }
                }

                $now = Carbon::now();
                $userName = Auth::user()->name;
                $headerIds = [];

                foreach ($headerGroups as $key => $header) {
                    $headerIds[$key] = DB::table('m_digital_lamination_pricing_rules')
                        ->insertGetId([
                            ...$header,
                            'created_by' => $userName,
                            'created_at' => $now,
                            'updated_by' => $userName,
                            'updated_at' => $now,
                        ]);
                }

                $details = [];

                foreach ($pricingRules as $rule) {
                    $vendorId = isset($rule['vendor_id']) && $rule['vendor_id'] !== ''
                        ? (int) $rule['vendor_id']
                        : null;

                    $headerKey = implode('|', [
                        (int) $rule['engine_id'],
                        (int) $rule['location_id'],
                        $vendorId ?? 'null',
                        (int) $rule['category_id'],
                    ]);

                    foreach ($tierRanges as $tier => [$minimum, $maximum]) {
                        $roundingValue = $rule["{$tier}_rounding_value"] ?? null;

                        $details[] = [
                            'digital_lamination_pricing_rule_id' => $headerIds[$headerKey],
                            'price_type' => $rule['price_type'],
                            'print_type' => $rule['print_type'],
                            'quantity_tier' => $tier,
                            'minimum_quantity' => $minimum,
                            'maximum_quantity' => $maximum,
                            'markup_percentage' => $rule["{$tier}_markup_percentage"],
                            'rounding_value' => $roundingValue === '' ? null : $roundingValue,
                            'status' => $rule['status'],
                            'created_by' => $userName,
                            'created_at' => $now,
                            'updated_by' => $userName,
                            'updated_at' => $now,
                        ];
                    }
                }

                DB::table('m_digital_lamination_pricing_rule_details')->insert($details);
            });

            return response()->json([
                'success' => true,
                'message' => 'Markup laminasi Digital Print A3+ berhasil disimpan.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function detailsDigital($id)
    {
        $details = DB::table('m_digital_lamination_pricing_rule_details as d')
            ->join('m_digital_lamination_pricing_rules as a', 'd.digital_lamination_pricing_rule_id', '=', 'a.id')
            ->join('m_engines as e', 'a.engine_id', '=', 'e.id')
            ->join('m_locations as l', 'a.location_id', '=', 'l.id')
            ->join('m_categories as c', 'a.category_id', '=', 'c.id')
            ->leftJoin('m_vendors as v', 'a.vendor_id', '=', 'v.id')
            ->select(
                'd.id as detail_id',
                'd.price_type',
                'd.print_type',
                'd.quantity_tier',
                'd.minimum_quantity',
                'd.maximum_quantity',
                'd.markup_percentage',
                'd.rounding_value',
                'd.status',
                'e.name as engine_name',
                'l.name as location_name',
                'v.name as vendor_name',
                'c.name as category_name'
            )
            ->where('a.id', $id)
            ->orderByRaw("CASE d.price_type WHEN 'general' THEN 0 WHEN 'division' THEN 1 ELSE 2 END")
            ->orderByRaw("CASE d.print_type WHEN 'single' THEN 0 WHEN 'double' THEN 1 ELSE 2 END")
            ->orderBy('d.minimum_quantity')
            ->get();

        if ($details->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data markup laminasi Digital Print tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $details,
        ]);
    }

    public function updateDigital(Request $request, $id)
    {
        $tiers = ['tier_1', 'tier_2_5', 'tier_6_50', 'tier_100', 'tier_300', 'tier_500'];

        $rules = [
            'pricing_rules' => ['required', 'array', 'min:1'],
            'pricing_rules.*.detail_id' => [
                'required',
                'integer',
                'exists:m_digital_lamination_pricing_rule_details,id',
            ],
            'pricing_rules.*.price_type' => ['required', 'in:general,division'],
            'pricing_rules.*.print_type' => ['required', 'in:single,double'],
            'pricing_rules.*.status' => ['required', 'in:Active,Inactive'],
        ];

        foreach ($tiers as $tier) {
            $rules["pricing_rules.*.{$tier}_markup_percentage"] = [
                'required',
                'numeric',
                'min:0',
                'max:999999.99',
            ];
            $rules["pricing_rules.*.{$tier}_rounding_value"] = [
                'nullable',
                'numeric',
                'min:0',
                'max:9999999999.99',
            ];
        }

        $validated = $request->validate($rules);

        try {
            DB::transaction(function () use ($id, $validated, $tiers) {
                $header = DB::table('m_digital_lamination_pricing_rules as a')
                    ->join('m_engines as e', 'a.engine_id', '=', 'e.id')
                    ->select('a.id', 'e.name as engine_name')
                    ->where('a.id', $id)
                    ->first();

                if (!$header) {
                    throw new Exception('Konfigurasi laminasi Digital Print tidak ditemukan.');
                }

                if (strtolower(preg_replace('/\s+/', '', trim($header->engine_name))) !== 'digitalprinta3+') {
                    throw new Exception('Konfigurasi ini bukan milik mesin Digital Print A3+.');
                }

                $detailIds = collect($validated['pricing_rules'])->pluck('detail_id')->unique()->values();

                if ($detailIds->count() !== count($validated['pricing_rules'])) {
                    throw new Exception('Detail markup laminasi Digital Print duplikat.');
                }

                $existingDetails = DB::table('m_digital_lamination_pricing_rule_details')
                    ->where('digital_lamination_pricing_rule_id', $header->id)
                    ->whereIn('id', $detailIds)
                    ->get()
                    ->keyBy('id');

                if ($existingDetails->count() !== $detailIds->count()) {
                    throw new Exception(
                        'Salah satu detail markup tidak sesuai dengan konfigurasi laminasi Digital Print.'
                    );
                }

                $now = Carbon::now();
                $userName = Auth::user()->name;

                foreach ($validated['pricing_rules'] as $index => $rule) {
                    $existing = $existingDetails[$rule['detail_id']];
                    $row = $index + 1;

                    if (
                        $existing->price_type !== $rule['price_type'] ||
                        $existing->print_type !== $rule['print_type']
                    ) {
                        throw new Exception(
                            "Tipe harga atau harga print pada baris {$row} tidak sesuai dengan detail yang tersimpan."
                        );
                    }

                    foreach ($tiers as $tier) {
                        $rounding = $rule["{$tier}_rounding_value"] ?? null;

                        $query = DB::table('m_digital_lamination_pricing_rule_details')
                            ->where('digital_lamination_pricing_rule_id', $header->id)
                            ->where('price_type', $rule['price_type'])
                            ->where('print_type', $rule['print_type'])
                            ->where('quantity_tier', $tier);

                        if (!$query->exists()) {
                            throw new Exception("Tier {$tier} pada baris {$row} tidak ditemukan.");
                        }

                        $query->update([
                            'markup_percentage' => $rule["{$tier}_markup_percentage"],
                            'rounding_value' => $rounding === '' ? null : $rounding,
                            'status' => $rule['status'],
                            'updated_by' => $userName,
                            'updated_at' => $now,
                        ]);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Markup laminasi Digital Print A3+ berhasil diperbarui.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroyDigital($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $header = DB::table('m_digital_lamination_pricing_rules as a')
                    ->join('m_engines as e', 'a.engine_id', '=', 'e.id')
                    ->select('a.id', 'e.name as engine_name')
                    ->where('a.id', $id)
                    ->first();

                if (!$header) {
                    throw new Exception('Data markup laminasi Digital Print tidak ditemukan.');
                }

                if (strtolower(preg_replace('/\s+/', '', trim($header->engine_name))) !== 'digitalprinta3+') {
                    throw new Exception('Konfigurasi ini bukan milik mesin Digital Print A3+.');
                }

                DB::table('m_digital_lamination_pricing_rule_details')
                    ->where('digital_lamination_pricing_rule_id', $header->id)
                    ->delete();

                DB::table('m_digital_lamination_pricing_rules')
                    ->where('id', $header->id)
                    ->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Markup laminasi Digital Print A3+ berhasil dihapus.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'pricing_rules' => ['required', 'array', 'min:1'],
            'pricing_rules.*.engine_id' => ['required', 'integer', 'exists:m_engines,id'],
            'pricing_rules.*.location_id' => ['required', 'integer', 'exists:m_locations,id'],
            'pricing_rules.*.vendor_id' => ['nullable', 'integer', 'exists:m_vendors,id'],
            'pricing_rules.*.category_id' => ['required', 'integer', 'exists:m_categories,id'],
            'pricing_rules.*.price_type' => ['required', 'in:general,division,plain'],
            'pricing_rules.*.markup_percentage' => ['required', 'numeric', 'min:0'],
            'pricing_rules.*.rounding_value' => ['nullable', 'numeric', 'min:0'],
            'pricing_rules.*.status' => ['required', 'in:Active,Inactive'],
        ], [
            'pricing_rules.required' => 'Minimal harus ada satu markup harga.',
            'pricing_rules.array' => 'Format markup harga tidak valid.',
            'pricing_rules.min' => 'Minimal harus ada satu markup harga.',

            'pricing_rules.*.engine_id.required' => 'Mesin wajib dipilih.',
            'pricing_rules.*.engine_id.integer' => 'Mesin tidak valid.',
            'pricing_rules.*.engine_id.exists' => 'Mesin tidak ditemukan.',

            'pricing_rules.*.location_id.required' => 'Lokasi wajib dipilih.',
            'pricing_rules.*.location_id.integer' => 'Lokasi tidak valid.',
            'pricing_rules.*.location_id.exists' => 'Lokasi tidak ditemukan.',

            'pricing_rules.*.vendor_id.integer' => 'Vendor tidak valid.',
            'pricing_rules.*.vendor_id.exists' => 'Vendor tidak ditemukan.',

            'pricing_rules.*.category_id.required' => 'Kategori wajib dipilih.',
            'pricing_rules.*.category_id.integer' => 'Kategori tidak valid.',
            'pricing_rules.*.category_id.exists' => 'Kategori tidak ditemukan.',

            'pricing_rules.*.price_type.required' => 'Tipe harga wajib dipilih.',
            'pricing_rules.*.price_type.in' => 'Tipe harga tidak valid.',

            'pricing_rules.*.markup_percentage.required' => 'Markup wajib diisi.',
            'pricing_rules.*.markup_percentage.numeric' => 'Markup harus berupa angka.',
            'pricing_rules.*.markup_percentage.min' => 'Markup tidak boleh kurang dari 0.',

            'pricing_rules.*.rounding_value.numeric' => 'Nilai pembulatan harus berupa angka.',
            'pricing_rules.*.rounding_value.min' => 'Nilai pembulatan tidak boleh kurang dari 0.',

            'pricing_rules.*.status.required' => 'Status wajib dipilih.',
            'pricing_rules.*.status.in' => 'Status tidak valid.',
        ]);

        try {
            DB::beginTransaction();

            $pricingRules = $request->input('pricing_rules');

            /*
        |--------------------------------------------------------------------------
        | 1. Cek semua Engine harus Active
        |--------------------------------------------------------------------------
        */
            $engineIds = collect($pricingRules)
                ->pluck('engine_id')
                ->filter()
                ->unique()
                ->values();

            $inactiveEngines = DB::table('m_engines')
                ->whereIn('id', $engineIds)
                ->where('status', '!=', 'Active')
                ->pluck('name')
                ->toArray();

            if (!empty($inactiveEngines)) {
                throw new \Exception(
                    'Mesin berikut tidak aktif: ' . implode(', ', $inactiveEngines)
                );
            }

            /*
        |--------------------------------------------------------------------------
        | 2. Cek semua Location harus Active
        |--------------------------------------------------------------------------
        */
            $locationIds = collect($pricingRules)
                ->pluck('location_id')
                ->filter()
                ->unique()
                ->values();

            $inactiveLocations = DB::table('m_locations')
                ->whereIn('id', $locationIds)
                ->where('status', '!=', 'Active')
                ->pluck('name')
                ->toArray();

            if (!empty($inactiveLocations)) {
                throw new \Exception(
                    'Lokasi berikut tidak aktif: ' . implode(', ', $inactiveLocations)
                );
            }

            /*
        |--------------------------------------------------------------------------
        | 3. Ambil nama Location
        |--------------------------------------------------------------------------
        */
            $locationNames = DB::table('m_locations')
                ->whereIn('id', $locationIds)
                ->pluck('name', 'id');

            /*
        |--------------------------------------------------------------------------
        | 4. Validasi Vendor berdasarkan Location
        |--------------------------------------------------------------------------
        */
            foreach ($pricingRules as $index => $rule) {

                $locationId = (int) $rule['location_id'];

                $vendorId = isset($rule['vendor_id']) && $rule['vendor_id'] !== ''
                    ? (int) $rule['vendor_id']
                    : null;

                $locationName = strtolower(
                    trim($locationNames[$locationId] ?? '')
                );

                /*
            |--------------------------------------------------------------------------
            | Outsourcing â†’ Vendor wajib dan harus Active
            |--------------------------------------------------------------------------
            */
                if ($locationName === 'outsourcing') {

                    if (!$vendorId) {
                        throw new \Exception(
                            'Vendor wajib dipilih untuk lokasi Outsourcing.'
                        );
                    }

                    $vendor = DB::table('m_vendors')
                        ->where('id', $vendorId)
                        ->first();

                    if (!$vendor) {
                        throw new \Exception(
                            'Vendor yang dipilih tidak ditemukan.'
                        );
                    }

                    if ($vendor->status !== 'Active') {
                        throw new \Exception(
                            'Vendor yang dipilih tidak aktif.'
                        );
                    }

                    /*
            |--------------------------------------------------------------------------
            | Non-Outsourcing â†’ Vendor harus kosong
            |--------------------------------------------------------------------------
            */
                } else {

                    if ($vendorId !== null) {
                        throw new \Exception(
                            'Vendor hanya dapat dipilih untuk lokasi Outsourcing.'
                        );
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | Normalisasi vendor_id
            |--------------------------------------------------------------------------
            */
                $pricingRules[$index]['vendor_id'] =
                    $locationName === 'outsourcing'
                    ? $vendorId
                    : null;
            }

            /*
        |--------------------------------------------------------------------------
        | 5. Cek duplicate dalam request
        |--------------------------------------------------------------------------
        */
            $requestCombinations = [];

            foreach ($pricingRules as $rule) {

                $engineId = (int) $rule['engine_id'];
                $locationId = (int) $rule['location_id'];
                $categoryId = (int) $rule['category_id'];
                $priceType = $rule['price_type'];

                $vendorId = isset($rule['vendor_id']) && $rule['vendor_id'] !== ''
                    ? (int) $rule['vendor_id']
                    : null;

                $locationName = strtolower(
                    trim($locationNames[$locationId] ?? '')
                );

                /*
            |--------------------------------------------------------------------------
            | Outsourcing
            | engine + location + vendor + category + price_type
            |--------------------------------------------------------------------------
            */
                if ($locationName === 'outsourcing') {

                    $combination = implode('|', [
                        $engineId,
                        $locationId,
                        $vendorId,
                        $categoryId,
                        $priceType,
                    ]);

                    if (isset($requestCombinations[$combination])) {
                        throw new \Exception(
                            'Terdapat markup laminasi duplikat untuk mesin, lokasi, vendor, kategori, dan tipe harga yang sama.'
                        );
                    }

                    $requestCombinations[$combination] = true;

                    /*
            |--------------------------------------------------------------------------
            | Non-Outsourcing
            | engine + location + category + price_type
            |--------------------------------------------------------------------------
            */
                } else {

                    $combination = implode('|', [
                        $engineId,
                        $locationId,
                        $categoryId,
                        $priceType,
                    ]);

                    if (isset($requestCombinations[$combination])) {
                        throw new \Exception(
                            'Terdapat markup laminasi duplikat untuk mesin, lokasi, kategori, dan tipe harga yang sama.'
                        );
                    }

                    $requestCombinations[$combination] = true;
                }
            }

            /*
        |--------------------------------------------------------------------------
        | 6. Cek duplicate dengan data yang sudah ada di database
        |--------------------------------------------------------------------------
        */
            foreach ($pricingRules as $rule) {

                $engineId = (int) $rule['engine_id'];
                $locationId = (int) $rule['location_id'];
                $categoryId = (int) $rule['category_id'];
                $priceType = $rule['price_type'];

                $vendorId = isset($rule['vendor_id']) && $rule['vendor_id'] !== ''
                    ? (int) $rule['vendor_id']
                    : null;

                $locationName = strtolower(
                    trim($locationNames[$locationId] ?? '')
                );

                $query = DB::table('m_lamination_pricing_rules')
                    ->where('engine_id', $engineId)
                    ->where('location_id', $locationId)
                    ->where('category_id', $categoryId)
                    ->where('price_type', $priceType);

                if ($locationName === 'outsourcing') {

                    $query->where('vendor_id', $vendorId);
                } else {

                    $query->whereNull('vendor_id');
                }

                if ($query->exists()) {

                    if ($locationName === 'outsourcing') {
                        throw new \Exception(
                            'Markup laminasi dengan mesin, lokasi, vendor, kategori, dan tipe harga tersebut sudah ada.'
                        );
                    }

                    throw new \Exception(
                        'Markup laminasi dengan mesin, lokasi, kategori, dan tipe harga tersebut sudah ada.'
                    );
                }
            }

            /*
        |--------------------------------------------------------------------------
        | 7. Insert ke m_lamination_pricing_rules
        |--------------------------------------------------------------------------
        */
            foreach ($pricingRules as $rule) {

                $locationId = (int) $rule['location_id'];

                $vendorId = isset($rule['vendor_id']) && $rule['vendor_id'] !== ''
                    ? (int) $rule['vendor_id']
                    : null;

                $locationName = strtolower(
                    trim($locationNames[$locationId] ?? '')
                );

                DB::table('m_lamination_pricing_rules')->insert([
                    'engine_id' => (int) $rule['engine_id'],
                    'location_id' => $locationId,
                    'vendor_id' => $locationName === 'outsourcing'
                        ? $vendorId
                        : null,
                    'category_id' => (int) $rule['category_id'],
                    'price_type' => $rule['price_type'],
                    'markup_percentage' => (float) $rule['markup_percentage'],
                    'rounding_value' => $rule['rounding_value'] !== null
                        ? (float) $rule['rounding_value']
                        : null,
                    'status' => $rule['status'],
                    'created_by' => Auth::user()->name,
                    'updated_by' => Auth::user()->name,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Markup harga laminasi berhasil disimpan.',
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function edit(
        $engine_id,
        $location_id,
        $vendor_id,
        $category_id
    ) {
        $pricingRules = DB::table('m_lamination_pricing_rules as a')
            ->leftJoin(
                'm_engines as e',
                'a.engine_id',
                '=',
                'e.id'
            )
            ->leftJoin(
                'm_locations as b',
                'a.location_id',
                '=',
                'b.id'
            )
            ->leftJoin(
                'm_categories as c',
                'a.category_id',
                '=',
                'c.id'
            )
            ->leftJoin(
                'm_vendors as v',
                'a.vendor_id',
                '=',
                'v.id'
            )
            ->select(
                'a.id',
                'a.engine_id',
                'a.location_id',
                'a.vendor_id',
                'a.category_id',
                'a.price_type',
                'a.markup_percentage',
                'a.rounding_value',
                'a.status',
                'e.name as engine_name',
                'b.name as location_name',
                'v.name as vendor_name',
                'c.name as category_name'
            )
            ->where('a.engine_id', $engine_id)
            ->where('a.location_id', $location_id)
            ->where(function ($query) use ($vendor_id) {
                if ($vendor_id === 'null') {
                    $query->whereNull('a.vendor_id');
                } else {
                    $query->where('a.vendor_id', $vendor_id);
                }
            })
            ->where('a.category_id', $category_id)
            ->orderBy('a.id', 'asc')
            ->get();

        if ($pricingRules->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data markup harga laminasi tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pricingRules
        ]);
    }

    public function update(
        Request $request,
        $engine_id,
        $location_id,
        $vendor_id,
        $category_id
    ) {
        $request->validate(
            [
                'pricing_rules' => 'required|array|min:1',

                'pricing_rules.*.price_type' => [
                    'required',
                    'in:general,division,plain',
                ],

                'pricing_rules.*.markup_percentage' => [
                    'required',
                    'numeric',
                    'min:0',
                ],

                'pricing_rules.*.rounding_value' => [
                    'nullable',
                    'numeric',
                    'min:0',
                ],

                'pricing_rules.*.status' => [
                    'required',
                    'in:Active,Inactive',
                ],
            ],
            [
                'pricing_rules.required' =>
                'Data markup harga laminasi wajib diisi.',

                'pricing_rules.array' =>
                'Format data markup harga laminasi tidak valid.',

                'pricing_rules.min' =>
                'Minimal harus terdapat satu markup harga laminasi.',

                'pricing_rules.*.price_type.required' =>
                'Tipe harga wajib dipilih.',

                'pricing_rules.*.price_type.in' =>
                'Tipe harga yang dipilih tidak valid.',

                'pricing_rules.*.markup_percentage.required' =>
                'Markup persentase wajib diisi.',

                'pricing_rules.*.markup_percentage.numeric' =>
                'Markup persentase harus berupa angka.',

                'pricing_rules.*.markup_percentage.min' =>
                'Markup persentase tidak boleh kurang dari 0.',

                'pricing_rules.*.rounding_value.numeric' =>
                'Nilai pembulatan harus berupa angka.',

                'pricing_rules.*.rounding_value.min' =>
                'Nilai pembulatan tidak boleh kurang dari 0.',

                'pricing_rules.*.status.required' =>
                'Status wajib dipilih.',

                'pricing_rules.*.status.in' =>
                'Status yang dipilih tidak valid.',
            ]
        );

        /*
    |--------------------------------------------------------------------------
    | Validasi Mesin
    |--------------------------------------------------------------------------
    */

        $engine = DB::table('m_engines')
            ->where('id', $engine_id)
            ->first();

        if (!$engine) {
            return response()->json([
                'success' => false,
                'message' => 'Mesin yang dipilih tidak ditemukan.'
            ], 422);
        }

        if ($engine->status !== 'Active') {
            return response()->json([
                'success' => false,
                'message' => 'Mesin yang dipilih tidak aktif.'
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | Validasi Lokasi
    |--------------------------------------------------------------------------
    */

        $location = DB::table('m_locations')
            ->where('id', $location_id)
            ->first();

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi yang dipilih tidak ditemukan.'
            ], 422);
        }

        if ($location->status !== 'Active') {
            return response()->json([
                'success' => false,
                'message' => 'Lokasi yang dipilih tidak aktif.'
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | Validasi Vendor
    |--------------------------------------------------------------------------
    |
    | Outsourcing:
    | - Vendor wajib dipilih.
    | - Vendor harus ada dan Active.
    |
    | Non-Outsourcing:
    | - Vendor harus null.
    |
    */

        $vendor = null;

        $isOutsourcing =
            strtolower(trim($location->name)) === 'outsourcing';

        if ($isOutsourcing) {

            if ($vendor_id === 'null') {
                return response()->json([
                    'success' => false,
                    'message' =>
                    'Vendor wajib dipilih untuk lokasi Outsourcing.'
                ], 422);
            }

            $vendor = DB::table('m_vendors')
                ->where('id', $vendor_id)
                ->first();

            if (!$vendor) {
                return response()->json([
                    'success' => false,
                    'message' =>
                    'Vendor yang dipilih tidak ditemukan.'
                ], 422);
            }

            if ($vendor->status !== 'Active') {
                return response()->json([
                    'success' => false,
                    'message' =>
                    'Vendor yang dipilih tidak aktif.'
                ], 422);
            }
        } else {

            if ($vendor_id !== 'null') {
                return response()->json([
                    'success' => false,
                    'message' =>
                    'Vendor hanya dapat digunakan untuk lokasi Outsourcing.'
                ], 422);
            }
        }

        /*
    |--------------------------------------------------------------------------
    | Validasi Kategori
    |--------------------------------------------------------------------------
    */

        $category = DB::table('m_categories')
            ->where('id', $category_id)
            ->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' =>
                'Kategori yang dipilih tidak ditemukan.'
            ], 422);
        }

        $pricingRules = $request->input('pricing_rules');

        /*
    |--------------------------------------------------------------------------
    | Validasi Duplikat Price Type Dalam Request
    |--------------------------------------------------------------------------
    */

        $requestedTypes = [];

        foreach ($pricingRules as $index => $rule) {

            $priceType = $rule['price_type'];

            if (isset($requestedTypes[$priceType])) {

                $priceTypeLabel = match ($priceType) {
                    'general' => 'General',
                    'division' => 'Division',
                    'plain' => 'Plain',
                    default => $priceType,
                };

                return response()->json([
                    'success' => false,
                    'message' =>
                    'Terdapat markup duplikat pada baris ' .
                        ($index + 1) .
                        '. Tipe harga "' .
                        $priceTypeLabel .
                        '" hanya boleh digunakan satu kali.'
                ], 422);
            }

            $requestedTypes[$priceType] = true;
        }

        try {

            DB::transaction(function () use (
                $engine_id,
                $location_id,
                $vendor_id,
                $category_id,
                $pricingRules,
                $requestedTypes,
                $engine,
                $location,
                $vendor,
                $category
            ) {

                /*
            |--------------------------------------------------------------------------
            | Ambil Existing Rules
            |--------------------------------------------------------------------------
            */

                $existingRules = DB::table(
                    'm_lamination_pricing_rules'
                )
                    ->where('engine_id', $engine_id)
                    ->where('location_id', $location_id)
                    ->where(function ($query) use ($vendor_id) {

                        if ($vendor_id === 'null') {
                            $query->whereNull('vendor_id');
                        } else {
                            $query->where('vendor_id', $vendor_id);
                        }
                    })
                    ->where('category_id', $category_id)
                    ->get()
                    ->keyBy('price_type');

                /*
            |--------------------------------------------------------------------------
            | Validasi Duplicate Dengan Record Lain
            |--------------------------------------------------------------------------
            */

                foreach ($pricingRules as $index => $rule) {

                    $priceType = $rule['price_type'];

                    $currentRule =
                        $existingRules->get($priceType);

                    $duplicateQuery = DB::table(
                        'm_lamination_pricing_rules'
                    )
                        ->where('engine_id', $engine_id)
                        ->where('location_id', $location_id)
                        ->where(function ($query) use ($vendor_id) {

                            if ($vendor_id === 'null') {
                                $query->whereNull('vendor_id');
                            } else {
                                $query->where(
                                    'vendor_id',
                                    $vendor_id
                                );
                            }
                        })
                        ->where('category_id', $category_id)
                        ->where('price_type', $priceType);

                    /*
                |--------------------------------------------------------------------------
                | Exclude Record Yang Sedang Diedit
                |--------------------------------------------------------------------------
                */

                    if ($currentRule) {
                        $duplicateQuery->where(
                            'id',
                            '!=',
                            $currentRule->id
                        );
                    }

                    if ($duplicateQuery->exists()) {

                        $priceTypeLabel = match ($priceType) {
                            'general' => 'General',
                            'division' => 'Division',
                            'plain' => 'Plain',
                            default => $priceType,
                        };

                        $vendorMessage = $vendor
                            ? ', vendor "' . $vendor->name . '"'
                            : '';

                        throw new Exception(
                            'Markup "' .
                                $priceTypeLabel .
                                '" untuk mesin "' .
                                $engine->name .
                                '", lokasi "' .
                                $location->name .
                                $vendorMessage .
                                ', dan kategori "' .
                                $category->name .
                                '" sudah digunakan.'
                        );
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | Hapus Pricing Rule Yang Tidak Lagi Dikirim
            |--------------------------------------------------------------------------
            */

                foreach (
                    $existingRules as $priceType => $existingRule
                ) {

                    if (!isset($requestedTypes[$priceType])) {

                        DB::table(
                            'm_lamination_pricing_rules'
                        )
                            ->where('id', $existingRule->id)
                            ->delete();
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | Update Existing / Insert New
            |--------------------------------------------------------------------------
            */

                foreach ($pricingRules as $rule) {

                    $priceType =
                        $rule['price_type'];

                    $markupPercentage =
                        $rule['markup_percentage'];

                    $roundingValue =
                        $rule['rounding_value'] ?? null;

                    $status =
                        $rule['status'];

                    /*
                |--------------------------------------------------------------------------
                | Update Existing
                |--------------------------------------------------------------------------
                */

                    if (isset($existingRules[$priceType])) {

                        $existingRule =
                            $existingRules[$priceType];

                        DB::table(
                            'm_lamination_pricing_rules'
                        )
                            ->where(
                                'id',
                                $existingRule->id
                            )
                            ->update([
                                'markup_percentage' =>
                                $markupPercentage,

                                'rounding_value' =>
                                $roundingValue,

                                'status' =>
                                $status,

                                'updated_by' =>
                                Auth::user()->name,

                                'updated_at' =>
                                Carbon::now(),
                            ]);
                    }

                    /*
                |--------------------------------------------------------------------------
                | Insert New
                |--------------------------------------------------------------------------
                */ else {

                        DB::table(
                            'm_lamination_pricing_rules'
                        )
                            ->insert([
                                'engine_id' =>
                                $engine_id,

                                'location_id' =>
                                $location_id,

                                'vendor_id' =>
                                $vendor_id === 'null'
                                    ? null
                                    : $vendor_id,

                                'category_id' =>
                                $category_id,

                                'price_type' =>
                                $priceType,

                                'markup_percentage' =>
                                $markupPercentage,

                                'rounding_value' =>
                                $roundingValue,

                                'status' =>
                                $status,

                                'created_by' =>
                                Auth::user()->name,

                                'created_at' =>
                                Carbon::now(),

                                'updated_by' =>
                                Auth::user()->name,

                                'updated_at' =>
                                Carbon::now(),
                            ]);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' =>
                'Markup harga laminasi berhasil diperbarui.',
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function details(
        $engine_id,
        $location_id,
        $vendor_id,
        $category_id
    ) {
        $pricingRules = DB::table('m_lamination_pricing_rules as a')
            ->leftJoin(
                'm_engines as e',
                'a.engine_id',
                '=',
                'e.id'
            )
            ->leftJoin(
                'm_locations as b',
                'a.location_id',
                '=',
                'b.id'
            )
            ->leftJoin(
                'm_categories as c',
                'a.category_id',
                '=',
                'c.id'
            )
            ->leftJoin(
                'm_vendors as v',
                'a.vendor_id',
                '=',
                'v.id'
            )
            ->select(
                'a.id',
                'a.engine_id',
                'a.location_id',
                'a.vendor_id',
                'a.category_id',
                'a.price_type',
                'a.markup_percentage',
                'a.rounding_value',
                'a.status',
                'e.name as engine_name',
                'b.name as location_name',
                'v.name as vendor_name',
                'c.name as category_name'
            )
            ->where('a.engine_id', $engine_id)
            ->where('a.location_id', $location_id)
            ->where(function ($query) use ($vendor_id) {
                if ($vendor_id === 'null') {
                    $query->whereNull('a.vendor_id');
                } else {
                    $query->where('a.vendor_id', $vendor_id);
                }
            })
            ->where('a.category_id', $category_id)
            ->orderBy('a.id', 'asc')
            ->get();

        if ($pricingRules->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data markup harga laminasi tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pricingRules
        ]);
    }

    public function destroy(
        $engine_id,
        $location_id,
        $vendor_id,
        $category_id
    ) {
        try {
            DB::transaction(function () use (
                $engine_id,
                $location_id,
                $vendor_id,
                $category_id
            ) {
                /*
            |--------------------------------------------------------------------------
            | AMBIL DATA PRICING RULE
            |--------------------------------------------------------------------------
            */
                $pricingRules = DB::table(
                    'm_lamination_pricing_rules'
                )
                    ->where('engine_id', $engine_id)
                    ->where('location_id', $location_id)
                    ->where(function ($query) use ($vendor_id) {
                        if ($vendor_id === 'null') {
                            $query->whereNull('vendor_id');
                        } else {
                            $query->where(
                                'vendor_id',
                                $vendor_id
                            );
                        }
                    })
                    ->where('category_id', $category_id)
                    ->get();

                /*
            |--------------------------------------------------------------------------
            | CEK DATA
            |--------------------------------------------------------------------------
            */
                if ($pricingRules->isEmpty()) {
                    throw new Exception(
                        'Data markup harga laminasi tidak ditemukan.'
                    );
                }

                /*
            |--------------------------------------------------------------------------
            | CEK APAKAH PRICING RULE SUDAH DIGUNAKAN
            |--------------------------------------------------------------------------
            |
            | Satu group Pricing Rule bisa terdiri dari:
            |
            | - general
            | - division
            | - plain
            |
            | Jika salah satu saja sudah digunakan oleh Production Cost,
            | seluruh group tidak boleh dihapus.
            |
            */
                foreach ($pricingRules as $pricingRule) {
                    $used = DB::table(
                        'm_lamination_production_cost_details'
                    )
                        ->where(function ($query) use ($pricingRule) {
                            $query->where(
                                'general_pricing_rule_id',
                                $pricingRule->id
                            )
                                ->orWhere(
                                    'division_pricing_rule_id',
                                    $pricingRule->id
                                )
                                ->orWhere(
                                    'plain_pricing_rule_id',
                                    $pricingRule->id
                                );
                        })
                        ->exists();

                    if ($used) {
                        $priceTypeLabel = [
                            'general' => 'Harga Umum',
                            'division' => 'Harga Divisi',
                            'plain' => 'Harga Polos',
                        ][$pricingRule->price_type];

                        /*
                    |--------------------------------------------------------------------------
                    | AMBIL ENGINE, LOCATION, VENDOR & CATEGORY
                    |--------------------------------------------------------------------------
                    */
                        $engine = DB::table('m_engines')
                            ->where('id', $engine_id)
                            ->first();

                        $location = DB::table('m_locations')
                            ->where('id', $location_id)
                            ->first();

                        $vendor = null;

                        if ($vendor_id !== 'null') {
                            $vendor = DB::table('m_vendors')
                                ->where('id', $vendor_id)
                                ->first();
                        }

                        $category = DB::table('m_categories')
                            ->where('id', $category_id)
                            ->first();

                        /*
                    |--------------------------------------------------------------------------
                    | PESAN ERROR
                    |--------------------------------------------------------------------------
                    */
                        $message =
                            'Pricing Rule ' .
                            $priceTypeLabel .
                            ' untuk mesin "' .
                            ($engine->name ?? $engine_id) .
                            '", lokasi "' .
                            ($location->name ?? $location_id) .
                            '"';

                        if ($vendor_id !== 'null') {
                            $message .=
                                ', vendor "' .
                                ($vendor->name ?? $vendor_id) .
                                '"';
                        }

                        $message .=
                            ' dan kategori "' .
                            ($category->name ?? $category_id) .
                            '" sudah digunakan oleh Production Cost sehingga tidak dapat dihapus.';

                        throw new Exception($message);
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | HAPUS SELURUH PRICING RULE
            |--------------------------------------------------------------------------
            |
            | Vendor ikut menjadi bagian dari group Pricing Rule.
            |
            | Jika vendor_id = "null", hanya data dengan vendor_id NULL
            | yang akan dihapus.
            |
            | Jika vendor_id memiliki nilai, hanya data dengan vendor
            | tersebut yang akan dihapus.
            |
            */
                DB::table(
                    'm_lamination_pricing_rules'
                )
                    ->where('engine_id', $engine_id)
                    ->where('location_id', $location_id)
                    ->where(function ($query) use ($vendor_id) {
                        if ($vendor_id === 'null') {
                            $query->whereNull('vendor_id');
                        } else {
                            $query->where(
                                'vendor_id',
                                $vendor_id
                            );
                        }
                    })
                    ->where('category_id', $category_id)
                    ->delete();
            });

            /*
        |--------------------------------------------------------------------------
        | SUCCESS RESPONSE
        |--------------------------------------------------------------------------
        */
            return response()->json([
                'success' => true,
                'message' =>
                'Markup harga laminasi berhasil dihapus.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function export(Request $request)
    {
        $pricingEngine = $request->validate([
            'pricing_engine' => [
                'nullable',
                'in:large_format,digital_print_a3',
            ],
        ])['pricing_engine'] ?? 'large_format';

        if ($pricingEngine === 'digital_print_a3') {
            return Excel::download(
                new DigitalLaminationPricingRulesExport(),
                'digital_lamination_pricing_rules.xlsx'
            );
        }

        return Excel::download(
            new LaminationPricingRulesExport(),
            'lamination_pricing_rules.xlsx'
        );
    }

    public function import(Request $request)
    {
        $validated = $request->validate(
            [
                'file' => 'required|file|mimes:xlsx,xls',
                'pricing_engine' => [
                    'nullable',
                    'in:large_format,digital_print_a3',
                ],
            ],
            [
                'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
                'file.file' => 'File yang dipilih tidak valid.',
                'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls).',
            ]
        );

        try {
            $pricingEngine =
                $validated['pricing_engine'] ?? 'large_format';

            $import = $pricingEngine === 'digital_print_a3'
                ? new DigitalLaminationPricingRulesImport()
                : new LaminationPricingRulesImport();

            Excel::import(
                $import,
                $request->file('file')
            );

            return response()->json([
                'success' => true,
                'message' =>
                $import->importedRows .
                    ' data markup harga laminasi ' .
                    ($pricingEngine === 'digital_print_a3'
                        ? 'Digital Print A3+ '
                        : '') .
                    'berhasil diimport.',
                'imported_rows' =>
                $import->importedRows,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Exports\DigitalPricingRulesExport;
use App\Exports\PricingRulesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DigitalPricingRulesImport;
use App\Imports\PricingRulesImport;

class PricingRulesCT extends Controller
{
    public function index()
    {
        $data['locations'] = DB::table('m_locations')
            ->orderBy('name')->where('status', 'Active')->get();

        $data['categories'] = DB::table('m_categories')
            ->where('name', '!=', 'Display')
            ->orderBy('name')->get();

        $data['engines'] = DB::table('m_engines')
            ->orderBy('name')->where('status', 'Active')->get();

        $data['vendors'] = DB::table('m_vendors')
            ->orderBy('name')->where('status', 'Active')->get();

        return view('admin.markup-price.index')->with($data);
    }

    public function data(Request $request)
    {
        $validated = $request->validate([
            'pricing_engine' => [
                'nullable',
                'in:large_format,digital_print_a3',
            ],
        ]);

        $pricingEngine = $validated['pricing_engine'] ?? 'large_format';

        /*
    |--------------------------------------------------------------------------
    | TOTAL SEMUA DATA
    |--------------------------------------------------------------------------
    */

        if ($pricingEngine === 'digital_print_a3') {
            $query = DB::table('m_digital_pricing_rules as a')
                ->join(
                    'm_engines as d',
                    'a.engine_id',
                    '=',
                    'd.id'
                )
                ->join(
                    'm_categories as b',
                    'a.category_id',
                    '=',
                    'b.id'
                )
                ->join(
                    'm_locations as c',
                    'a.location_id',
                    '=',
                    'c.id'
                )
                ->leftJoin(
                    'm_vendors as v',
                    'a.vendor_id',
                    '=',
                    'v.id'
                )
                ->leftJoin(
                    'm_digital_pricing_rule_details as drd',
                    'drd.digital_pricing_rule_id',
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
                    'd.id',
                    'd.name',
                    'a.id',
                    'b.id',
                    'b.name',
                    'c.id',
                    'c.name',
                    'v.id',
                    'v.name'
                );
        } else {
            $query = DB::table('m_pricing_rules as a')
                ->join(
                    'm_engines as d',
                    'a.engine_id',
                    '=',
                    'd.id'
                )
                ->join(
                    'm_categories as b',
                    'a.category_id',
                    '=',
                    'b.id'
                )
                ->join(
                    'm_locations as c',
                    'a.location_id',
                    '=',
                    'c.id'
                )
                ->leftJoin(
                    'm_vendors as v',
                    'a.vendor_id',
                    '=',
                    'v.id'
                )
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

        $totalRecords = (clone $query)
            ->get()
            ->count();

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

            ->orderBy(
                'd.name',
                'asc'
            )

            ->orderBy(
                'c.name',
                'asc'
            )

            ->orderBy(
                'v.name',
                'asc'
            )

            ->orderBy(
                'b.name',
                'asc'
            )

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

        $rules = [
            'pricing_rules' => ['required', 'array', 'min:1'],
            'pricing_rules.*.engine_id' => [
                'required',
                'integer',
                'exists:m_engines,id',
            ],
            'pricing_rules.*.location_id' => [
                'required',
                'integer',
                'exists:m_locations,id',
            ],
            'pricing_rules.*.vendor_id' => [
                'nullable',
                'integer',
                'exists:m_vendors,id',
            ],
            'pricing_rules.*.category_id' => [
                'required',
                'integer',
                'exists:m_categories,id',
            ],
            'pricing_rules.*.price_type' => [
                'required',
                'in:general,division',
            ],
            'pricing_rules.*.print_type' => [
                'required',
                'in:single,double',
            ],
            'pricing_rules.*.status' => [
                'required',
                'in:Active,Inactive',
            ],
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
            'pricing_rules.required' => 'Minimal harus ada satu markup Digital Print.',
            'pricing_rules.min' => 'Minimal harus ada satu markup Digital Print.',
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
                $engineIds = collect($pricingRules)->pluck('engine_id')->unique();
                $locationIds = collect($pricingRules)->pluck('location_id')->unique();
                $categoryIds = collect($pricingRules)->pluck('category_id')->unique();
                $vendorIds = collect($pricingRules)
                    ->pluck('vendor_id')
                    ->filter()
                    ->unique();

                $engines = DB::table('m_engines')
                    ->whereIn('id', $engineIds)
                    ->get(['id', 'name', 'status'])
                    ->keyBy('id');
                $locations = DB::table('m_locations')
                    ->whereIn('id', $locationIds)
                    ->get(['id', 'name', 'status'])
                    ->keyBy('id');
                $categories = DB::table('m_categories')
                    ->whereIn('id', $categoryIds)
                    ->get(['id', 'name'])
                    ->keyBy('id');
                $vendors = DB::table('m_vendors')
                    ->whereIn('id', $vendorIds)
                    ->get(['id', 'name', 'status'])
                    ->keyBy('id');

                $headerGroups = [];
                $detailCombinations = [];

                foreach ($pricingRules as $index => $rule) {
                    $engine = $engines[$rule['engine_id']] ?? null;
                    $location = $locations[$rule['location_id']] ?? null;
                    $category = $categories[$rule['category_id']] ?? null;

                    if (!$engine || !$location || !$category) {
                        throw new Exception(
                            'Mesin, lokasi, atau kategori pada baris ' . ($index + 1) . ' tidak ditemukan.'
                        );
                    }

                    $normalizedEngine = strtolower(
                        preg_replace('/\s+/', '', trim($engine->name))
                    );
                    if ($normalizedEngine !== 'digitalprinta3+') {
                        throw new Exception(
                            'Store Digital Print hanya menerima mesin Digital Print A3+ (baris ' . ($index + 1) . ').'
                        );
                    }

                    if ($engine->status !== 'Active') {
                        throw new Exception(
                            'Mesin pada baris ' . ($index + 1) . ' tidak aktif.'
                        );
                    }

                    if ($location->status !== 'Active') {
                        throw new Exception(
                            'Lokasi pada baris ' . ($index + 1) . ' tidak aktif.'
                        );
                    }

                    $normalizedCategory = strtolower(
                        preg_replace('/[\s\/]+/', '', trim($category->name))
                    );
                    if (in_array($normalizedCategory, ['indoor&outdoor', 'indooroutdoor'], true)) {
                        throw new Exception(
                            'Store Digital Print tidak menerima kategori Indoor/Outdoor (baris ' . ($index + 1) . ').'
                        );
                    }

                    $locationName = strtolower(trim($location->name));
                    $vendorId = isset($rule['vendor_id']) && $rule['vendor_id'] !== ''
                        ? (int) $rule['vendor_id']
                        : null;

                    if ($locationName === 'outsourcing') {
                        $vendor = $vendorId !== null ? ($vendors[$vendorId] ?? null) : null;
                        if (!$vendor || $vendor->status !== 'Active') {
                            throw new Exception(
                                'Vendor aktif wajib dipilih untuk lokasi Outsourcing pada baris ' . ($index + 1) . '.'
                            );
                        }
                    } elseif ($vendorId !== null) {
                        throw new Exception(
                            'Vendor hanya dapat dipilih untuk lokasi Outsourcing pada baris ' . ($index + 1) . '.'
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

                    $detailKey = implode('|', [
                        $headerKey,
                        $rule['price_type'],
                        $rule['print_type'],
                    ]);
                    if (isset($detailCombinations[$detailKey])) {
                        throw new Exception(
                            'Tipe harga dan jenis print duplikat ditemukan pada baris ' . ($index + 1) . '.'
                        );
                    }
                    $detailCombinations[$detailKey] = true;
                }

                foreach ($headerGroups as $header) {
                    $existingHeader = DB::table('m_digital_pricing_rules')
                        ->where('engine_id', $header['engine_id'])
                        ->where('location_id', $header['location_id'])
                        ->where('category_id', $header['category_id'])
                        ->when(
                            $header['vendor_id'] === null,
                            fn($query) => $query->whereNull('vendor_id'),
                            fn($query) => $query->where('vendor_id', $header['vendor_id'])
                        )
                        ->exists();

                    if ($existingHeader) {
                        throw new Exception(
                            'Konfigurasi Digital Print untuk kombinasi mesin, lokasi, vendor, dan kategori tersebut sudah terdaftar.'
                        );
                    }
                }

                $now = Carbon::now();
                $userName = Auth::user()->name;
                $headerIds = [];

                foreach ($headerGroups as $key => $header) {
                    $headerIds[$key] = DB::table('m_digital_pricing_rules')
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
                            'digital_pricing_rule_id' => $headerIds[$headerKey],
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

                DB::table('m_digital_pricing_rule_details')->insert($details);
            });

            return response()->json([
                'success' => true,
                'message' => 'Markup Digital Print A3+ berhasil disimpan.',
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
        $request->validate(
            [
                'pricing_rules' => [
                    'required',
                    'array',
                    'min:1',
                ],
                'pricing_rules.*.engine_id' => [
                    'required',
                    'integer',
                    'exists:m_engines,id',
                ],
                'pricing_rules.*.location_id' => [
                    'required',
                    'integer',
                    'exists:m_locations,id',
                ],
                'pricing_rules.*.vendor_id' => [
                    'nullable',
                    'integer',
                    'exists:m_vendors,id',
                ],
                'pricing_rules.*.category_id' => [
                    'required',
                    'integer',
                    'exists:m_categories,id',
                ],
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
                'Minimal harus ada satu markup harga.',

                'pricing_rules.min' =>
                'Minimal harus ada satu markup harga.',

                'pricing_rules.*.engine_id.required' =>
                'Mesin wajib dipilih.',

                'pricing_rules.*.engine_id.integer' =>
                'Mesin tidak valid.',

                'pricing_rules.*.engine_id.exists' =>
                'Mesin yang dipilih tidak ditemukan.',

                'pricing_rules.*.location_id.required' =>
                'Lokasi wajib dipilih.',

                'pricing_rules.*.location_id.integer' =>
                'Lokasi tidak valid.',

                'pricing_rules.*.location_id.exists' =>
                'Lokasi yang dipilih tidak ditemukan.',

                'pricing_rules.*.vendor_id.integer' =>
                'Vendor tidak valid.',

                'pricing_rules.*.vendor_id.exists' =>
                'Vendor yang dipilih tidak ditemukan.',

                'pricing_rules.*.category_id.required' =>
                'Kategori wajib dipilih.',

                'pricing_rules.*.category_id.integer' =>
                'Kategori tidak valid.',

                'pricing_rules.*.category_id.exists' =>
                'Kategori yang dipilih tidak ditemukan.',

                'pricing_rules.*.price_type.required' =>
                'Tipe harga wajib dipilih.',

                'pricing_rules.*.price_type.in' =>
                'Tipe harga tidak valid.',

                'pricing_rules.*.markup_percentage.required' =>
                'Markup wajib diisi.',

                'pricing_rules.*.markup_percentage.numeric' =>
                'Markup harus berupa angka.',

                'pricing_rules.*.markup_percentage.min' =>
                'Markup tidak boleh kurang dari 0.',

                'pricing_rules.*.rounding_value.numeric' =>
                'Pembulatan harus berupa angka.',

                'pricing_rules.*.rounding_value.min' =>
                'Pembulatan tidak boleh kurang dari 0.',

                'pricing_rules.*.status.required' =>
                'Status wajib dipilih.',

                'pricing_rules.*.status.in' =>
                'Status tidak valid.',
            ]
        );

        try {

            DB::transaction(function () use ($request) {

                $pricingRules = $request->input('pricing_rules');

                /*
            |--------------------------------------------------------------------------
            | CEK MESIN HARUS ACTIVE
            |--------------------------------------------------------------------------
            */

                $engineIds = collect($pricingRules)
                    ->pluck('engine_id')
                    ->unique()
                    ->values();

                $inactiveEngines = DB::table('m_engines')
                    ->whereIn('id', $engineIds)
                    ->where('status', '!=', 'Active')
                    ->pluck('name');

                if ($inactiveEngines->isNotEmpty()) {
                    throw new Exception(
                        'Mesin berikut tidak aktif: ' .
                            $inactiveEngines->implode(', ')
                    );
                }

                /*
            |--------------------------------------------------------------------------
            | CEK LOKASI HARUS ACTIVE
            |--------------------------------------------------------------------------
            */

                $locationIds = collect($pricingRules)
                    ->pluck('location_id')
                    ->unique()
                    ->values();

                $inactiveLocations = DB::table('m_locations')
                    ->whereIn('id', $locationIds)
                    ->where('status', '!=', 'Active')
                    ->pluck('name');

                if ($inactiveLocations->isNotEmpty()) {
                    throw new Exception(
                        'Lokasi berikut tidak aktif: ' .
                            $inactiveLocations->implode(', ')
                    );
                }

                /*
            |--------------------------------------------------------------------------
            | CEK VENDOR BERDASARKAN LOKASI
            |--------------------------------------------------------------------------
            */

                $locationNames = DB::table('m_locations')
                    ->whereIn('id', $locationIds)
                    ->pluck('name', 'id');

                foreach ($pricingRules as $index => $rule) {

                    $locationName = $locationNames[$rule['location_id']] ?? '';
                    // $vendorId = $rule['vendor_id'] ?? null;

                    $vendorId = isset($rule['vendor_id']) &&
                        $rule['vendor_id'] !== ''
                        ? (int) $rule['vendor_id']
                        : null;

                    if (strtolower(trim($locationName)) === 'outsourcing') {

                        if ($vendorId === null || $vendorId === '') {
                            throw new Exception(
                                'Vendor wajib dipilih untuk lokasi Outsourcing pada baris ' .
                                    ($index + 1) .
                                    '.'
                            );
                        }

                        $vendor = DB::table('m_vendors')
                            ->where('id', $vendorId)
                            ->where('status', 'Active')
                            ->first();

                        if (!$vendor) {
                            throw new Exception(
                                'Vendor pada baris ' .
                                    ($index + 1) .
                                    ' tidak aktif atau tidak ditemukan.'
                            );
                        }
                    } else {

                        if ($vendorId !== null && $vendorId !== '') {
                            throw new Exception(
                                'Vendor hanya dapat dipilih untuk lokasi Outsourcing pada baris ' .
                                    ($index + 1) .
                                    '.'
                            );
                        }
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKASI DALAM REQUEST
            |--------------------------------------------------------------------------
            |
            | Non-Outsourcing:
            | engine + location + category + price_type
            |
            | Outsourcing:
            | engine + location + vendor + category + price_type
            |
            */

                // CHECK DUPLICATION IN REQUEST
                $combinations = [];

                foreach ($pricingRules as $index => $rule) {

                    $locationName = $locationNames[$rule['location_id']] ?? '';

                    $isOutsourcing =
                        strtolower(trim($locationName)) === 'outsourcing';

                    $vendorId = isset($rule['vendor_id']) &&
                        $rule['vendor_id'] !== ''
                        ? (int) $rule['vendor_id']
                        : null;

                    /*
    |--------------------------------------------------------------------------
    | COMBINATION
    |--------------------------------------------------------------------------
    | Non-Outsourcing:
    | engine + location + category + price_type
    |
    | Outsourcing:
    | engine + location + vendor + category + price_type
    |--------------------------------------------------------------------------
    */

                    $combination =
                        $rule['engine_id'] .
                        '|' .
                        $rule['location_id'] .
                        '|' .
                        (
                            $isOutsourcing
                            ? $vendorId
                            : 'null'
                        ) .
                        '|' .
                        $rule['category_id'] .
                        '|' .
                        $rule['price_type'];

                    if (isset($combinations[$combination])) {

                        if ($isOutsourcing) {
                            throw new Exception(
                                'Markup pada baris ' .
                                    ($index + 1) .
                                    ' memiliki kombinasi mesin, lokasi, vendor, kategori, dan tipe harga yang sama.'
                            );
                        }

                        throw new Exception(
                            'Markup pada baris ' .
                                ($index + 1) .
                                ' memiliki kombinasi mesin, lokasi, kategori, dan tipe harga yang sama.'
                        );
                    }

                    $combinations[$combination] = true;
                }

                /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKASI DENGAN DATABASE
            |--------------------------------------------------------------------------
            */

                foreach ($pricingRules as $index => $rule) {

                    $locationName = $locationNames[$rule['location_id']] ?? '';

                    $isOutsourcing =
                        strtolower(trim($locationName)) === 'outsourcing';

                    $query = DB::table('m_pricing_rules')
                        ->where('engine_id', $rule['engine_id'])
                        ->where('location_id', $rule['location_id'])
                        ->where('category_id', $rule['category_id'])
                        ->where('price_type', $rule['price_type']);

                    if ($isOutsourcing) {

                        $vendorId = isset($rule['vendor_id']) &&
                            $rule['vendor_id'] !== ''
                            ? (int) $rule['vendor_id']
                            : null;

                        $query->where('vendor_id', $vendorId);
                    } else {

                        $query->whereNull('vendor_id');
                    }

                    if ($query->exists()) {

                        if ($isOutsourcing) {
                            throw new Exception(
                                'Markup pada baris ' .
                                    ($index + 1) .
                                    ' sudah terdaftar dengan kombinasi mesin, lokasi, vendor, kategori, dan tipe harga tersebut.'
                            );
                        }

                        throw new Exception(
                            'Markup pada baris ' .
                                ($index + 1) .
                                ' sudah terdaftar dengan kombinasi mesin, lokasi, kategori, dan tipe harga tersebut.'
                        );
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | INSERT DATA
            |--------------------------------------------------------------------------
            */

                $now = Carbon::now();
                $currentUser = Auth::user()->name;
                $insertData = [];

                foreach ($pricingRules as $rule) {

                    $locationName =
                        $locationNames[$rule['location_id']] ?? '';

                    $vendorId =
                        strtolower(trim($locationName)) === 'outsourcing'
                        ? (
                            isset($rule['vendor_id']) &&
                            $rule['vendor_id'] !== ''
                            ? (int) $rule['vendor_id']
                            : null
                        )
                        : null;

                    $insertData[] = [

                        'engine_id' =>
                        (int) $rule['engine_id'],

                        'location_id' =>
                        (int) $rule['location_id'],

                        'vendor_id' =>
                        $vendorId,

                        'category_id' =>
                        (int) $rule['category_id'],

                        'price_type' =>
                        $rule['price_type'],

                        'markup_percentage' =>
                        (float) $rule['markup_percentage'],

                        'rounding_value' =>
                        $rule['rounding_value'] !== null &&
                            $rule['rounding_value'] !== ''
                            ? (float) $rule['rounding_value']
                            : null,

                        'status' =>
                        $rule['status'],

                        'created_by' =>
                        $currentUser,

                        'created_at' =>
                        $now,

                        'updated_by' =>
                        $currentUser,

                        'updated_at' =>
                        $now,
                    ];
                }

                DB::table('m_pricing_rules')
                    ->insert($insertData);
            });

            return response()->json([
                'success' => true,
                'message' =>
                'Markup harga berhasil disimpan.',
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' =>
                $e->getMessage(),
            ], 422);
        }
    }

    public function edit($engine_id, $location_id, $vendor_id, $category_id)
    {
        $pricingRules = DB::table('m_pricing_rules as a')
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
                'message' => 'Data markup harga tidak ditemukan.'
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
                'Data markup harga wajib diisi.',

                'pricing_rules.array' =>
                'Format data markup harga tidak valid.',

                'pricing_rules.min' =>
                'Minimal harus terdapat satu markup harga.',

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
                    'message' => 'Vendor wajib dipilih untuk lokasi Outsourcing.'
                ], 422);
            }

            $vendor = DB::table('m_vendors')
                ->where('id', $vendor_id)
                ->first();

            if (!$vendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor yang dipilih tidak ditemukan.'
                ], 422);
            }

            if ($vendor->status !== 'Active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor yang dipilih tidak aktif.'
                ], 422);
            }
        } else {
            if ($vendor_id !== 'null') {
                return response()->json([
                    'success' => false,
                    'message' => 'Vendor hanya dapat digunakan untuk lokasi Outsourcing.'
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
                'message' => 'Kategori yang dipilih tidak ditemukan.'
            ], 422);
        }

        $pricingRules = $request->input('pricing_rules');

        /*
    |--------------------------------------------------------------------------
    | Validasi Duplikat Price Type Dalam Request
    |--------------------------------------------------------------------------
    |
    | Satu konfigurasi hanya boleh memiliki:
    | - general
    | - division
    | - plain
    |
    | Masing-masing maksimal satu.
    |
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
            |
            | Identitas konfigurasi:
            |
            | engine + location + vendor + category
            |
            | Untuk non-Outsourcing:
            | vendor_id harus NULL.
            |
            */

                $existingRules = DB::table('m_pricing_rules')
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
            | Validasi Duplikat Dengan Record Lain Di Database
            |--------------------------------------------------------------------------
            |
            | Record yang sedang diedit boleh tetap menggunakan kombinasi
            | yang sama.
            |
            | Yang ditolak hanya jika ada record LAIN dengan kombinasi:
            |
            | engine + location + vendor + category + price_type
            |
            */

                foreach ($pricingRules as $index => $rule) {
                    $priceType = $rule['price_type'];

                    $currentRule =
                        $existingRules->get($priceType);

                    $duplicateQuery = DB::table('m_pricing_rules')
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
                        ->where('price_type', $priceType);

                    /*
                |--------------------------------------------------------------------------
                | Jika price type sudah ada pada konfigurasi ini,
                | exclude ID record yang sedang diedit.
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
            | Validasi Record Yang Akan Dihapus
            |--------------------------------------------------------------------------
            |
            | Jika markup lama tidak dikirim lagi dari frontend,
            | berarti markup tersebut akan dihapus.
            |
            | Tetapi tidak boleh dihapus jika masih digunakan
            | oleh Production Cost.
            |
            */

                foreach ($existingRules as $priceType => $existingRule) {
                    if (!isset($requestedTypes[$priceType])) {

                        $usedByProductionCost = DB::table(
                            'm_production_cost_details'
                        )
                            ->where(function ($query) use ($existingRule) {
                                $query->where(
                                    'general_pricing_rule_id',
                                    $existingRule->id
                                )
                                    ->orWhere(
                                        'division_pricing_rule_id',
                                        $existingRule->id
                                    )
                                    ->orWhere(
                                        'plain_pricing_rule_id',
                                        $existingRule->id
                                    );
                            })
                            ->exists();

                        if ($usedByProductionCost) {
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
                                    '" tidak dapat dihapus karena masih digunakan oleh Production Cost pada mesin "' .
                                    $engine->name .
                                    '", lokasi "' .
                                    $location->name .
                                    $vendorMessage .
                                    ', dan kategori "' .
                                    $category->name .
                                    '".'
                            );
                        }
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | Validasi Active -> Inactive
            |--------------------------------------------------------------------------
            |
            | Markup yang sedang digunakan Production Cost tidak boleh
            | dinonaktifkan.
            |
            */

                foreach ($pricingRules as $rule) {
                    $priceType = $rule['price_type'];

                    if (!isset($existingRules[$priceType])) {
                        continue;
                    }

                    $existingRule =
                        $existingRules[$priceType];

                    if (
                        $existingRule->status === 'Active' &&
                        $rule['status'] === 'Inactive'
                    ) {
                        $usedByProductionCost = DB::table(
                            'm_production_cost_details'
                        )
                            ->where(function ($query) use ($existingRule) {
                                $query->where(
                                    'general_pricing_rule_id',
                                    $existingRule->id
                                )
                                    ->orWhere(
                                        'division_pricing_rule_id',
                                        $existingRule->id
                                    )
                                    ->orWhere(
                                        'plain_pricing_rule_id',
                                        $existingRule->id
                                    );
                            })
                            ->exists();

                        if ($usedByProductionCost) {
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
                                    '" tidak dapat dinonaktifkan karena masih digunakan oleh Production Cost pada mesin "' .
                                    $engine->name .
                                    '", lokasi "' .
                                    $location->name .
                                    $vendorMessage .
                                    ', dan kategori "' .
                                    $category->name .
                                    '".'
                            );
                        }
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | Hapus Pricing Rule Yang Tidak Lagi Dikirim
            |--------------------------------------------------------------------------
            */

                foreach ($existingRules as $priceType => $existingRule) {
                    if (!isset($requestedTypes[$priceType])) {
                        DB::table('m_pricing_rules')
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
                    $priceType = $rule['price_type'];

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

                        DB::table('m_pricing_rules')
                            ->where('id', $existingRule->id)
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
                                now(),
                            ]);
                    }

                    /*
                |--------------------------------------------------------------------------
                | Insert New
                |--------------------------------------------------------------------------
                */ else {
                        DB::table('m_pricing_rules')
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
                                now(),

                                'updated_by' =>
                                Auth::user()->name,

                                'updated_at' =>
                                now(),
                            ]);
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | Ambil Pricing Rules Terbaru
            |--------------------------------------------------------------------------
            */

                $latestRules = DB::table('m_pricing_rules')
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
            | Pastikan General, Division, Plain Lengkap
            |--------------------------------------------------------------------------
            */

                $requiredPriceTypes = [
                    'general',
                    'division'
                ];

                foreach ($requiredPriceTypes as $requiredType) {
                    if (!isset($latestRules[$requiredType])) {
                        throw new Exception(
                            'Konfigurasi markup harga harus memiliki tipe General, Division, dan Plain.'
                        );
                    }

                    if (
                        $latestRules[$requiredType]->status !==
                        'Active'
                    ) {
                        $priceTypeLabel = match ($requiredType) {
                            'general' => 'General',
                            'division' => 'Division',
                            'plain' => 'Plain',
                            default => $requiredType,
                        };

                        throw new Exception(
                            'Markup "' .
                                $priceTypeLabel .
                                '" harus berstatus Active agar dapat digunakan.'
                        );
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | Production Cost
            |--------------------------------------------------------------------------
            */

                $productionCostDetails = DB::table(
                    'm_production_cost_details as pcd'
                )
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
                    ->where(
                        'pcl.location_id',
                        $location_id
                    )
                    ->where(
                        'm.category_id',
                        $category_id
                    )
                    ->select(
                        'pcd.*'
                    )
                    ->get();

                /*
            |--------------------------------------------------------------------------
            | Tidak Ada Production Cost
            |--------------------------------------------------------------------------
            */

                if ($productionCostDetails->isEmpty()) {
                    return;
                }

                /*
            |--------------------------------------------------------------------------
            | Ambil Nilai Markup
            |--------------------------------------------------------------------------
            */

                $generalRule =
                    $latestRules['general'];

                $divisionRule =
                    $latestRules['division'];

                $plainRule =
                    $latestRules['plain'];

                $generalMarkup =
                    (float) $generalRule->markup_percentage / 100;

                $divisionMarkup =
                    (float) $divisionRule->markup_percentage / 100;

                $plainMarkup =
                    (float) $plainRule->markup_percentage / 100;

                /*
            |--------------------------------------------------------------------------
            | Hitung Ulang Harga Production Cost
            |--------------------------------------------------------------------------
            */

                foreach ($productionCostDetails as $detail) {

                    $pricePerMeter =
                        (float) $detail->price_per_meter;

                    $productionCost =
                        (float) $detail->production_cost;

                    $finishingCost =
                        (float) $detail->finishing_cost;

                    /*
                |--------------------------------------------------------------------------
                | Total Cost
                |--------------------------------------------------------------------------
                */

                    $totalCost =
                        ($pricePerMeter * 1.2)
                        + $productionCost
                        + $finishingCost;

                    /*
                |--------------------------------------------------------------------------
                | General Price
                |--------------------------------------------------------------------------
                */

                    $generalPrice =
                        $totalCost * (1 + $generalMarkup);

                    if (
                        $generalRule->rounding_value !== null &&
                        (float) $generalRule->rounding_value > 0
                    ) {
                        $rounding =
                            (float) $generalRule->rounding_value;

                        $generalPrice =
                            ceil(
                                $generalPrice / $rounding
                            ) * $rounding;
                    }

                    /*
                |--------------------------------------------------------------------------
                | Division Price
                |--------------------------------------------------------------------------
                */

                    $divisionPrice =
                        $generalPrice * $divisionMarkup;

                    if (
                        $divisionRule->rounding_value !== null &&
                        (float) $divisionRule->rounding_value > 0
                    ) {
                        $rounding =
                            (float) $divisionRule->rounding_value;

                        $divisionPrice =
                            ceil(
                                $divisionPrice / $rounding
                            ) * $rounding;
                    }

                    /*
                |--------------------------------------------------------------------------
                | Plain Price
                |--------------------------------------------------------------------------
                */

                    $plainPrice =
                        $pricePerMeter * (1 + $plainMarkup);

                    if (
                        $plainRule->rounding_value !== null &&
                        (float) $plainRule->rounding_value > 0
                    ) {
                        $rounding =
                            (float) $plainRule->rounding_value;

                        $plainPrice =
                            ceil(
                                $plainPrice / $rounding
                            ) * $rounding;
                    }

                    /*
                |--------------------------------------------------------------------------
                | Update Production Cost Detail
                |--------------------------------------------------------------------------
                */

                    DB::table('m_production_cost_details')
                        ->where('id', $detail->id)
                        ->update([
                            'general_price' =>
                            $generalPrice,

                            'division_price' =>
                            $divisionPrice,

                            'plain_price' =>
                            $plainPrice,

                            'general_pricing_rule_id' =>
                            $generalRule->id,

                            'division_pricing_rule_id' =>
                            $divisionRule->id,

                            'plain_pricing_rule_id' =>
                            $plainRule->id,

                            'updated_by' =>
                            Auth::user()->name,

                            'updated_at' =>
                            now(),
                        ]);
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Markup harga berhasil diperbarui.',
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function details($engine_id, $location_id, $vendor_id, $category_id)
    {
        $pricingRules = DB::table('m_pricing_rules as a')
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
                'message' => 'Data markup harga tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pricingRules
        ]);
    }

    public function detailsDigital($id)
    {
        $pricingRules = DB::table('m_digital_pricing_rule_details as d')
            ->join(
                'm_digital_pricing_rules as a',
                'd.digital_pricing_rule_id',
                '=',
                'a.id'
            )
            ->join(
                'm_engines as e',
                'a.engine_id',
                '=',
                'e.id'
            )
            ->join(
                'm_locations as l',
                'a.location_id',
                '=',
                'l.id'
            )
            ->join(
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
            ->orderByRaw(
                "CASE d.price_type
                    WHEN 'general' THEN 0
                    WHEN 'division' THEN 1
                    ELSE 2
                END"
            )
            ->orderByRaw(
                "CASE d.print_type
                    WHEN 'single' THEN 0
                    WHEN 'double' THEN 1
                    ELSE 2
                END"
            )
            ->orderBy('d.minimum_quantity')
            ->get();

        if ($pricingRules->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data markup Digital Print tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pricingRules,
        ]);
    }

    public function updateDigital(Request $request, $id)
    {
        $tierRanges = [
            'tier_1',
            'tier_2_5',
            'tier_6_50',
            'tier_100',
            'tier_300',
            'tier_500',
        ];

        $rules = [
            'pricing_rules' => ['required', 'array', 'min:1'],
            'pricing_rules.*.detail_id' => [
                'required',
                'integer',
                'exists:m_digital_pricing_rule_details,id',
            ],
            'pricing_rules.*.price_type' => ['required', 'in:general,division'],
            'pricing_rules.*.print_type' => ['required', 'in:single,double'],
            'pricing_rules.*.status' => ['required', 'in:Active,Inactive'],
        ];

        foreach ($tierRanges as $tier) {
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
            DB::transaction(function () use ($id, $validated, $tierRanges) {
                $header = DB::table('m_digital_pricing_rules as a')
                    ->join('m_engines as e', 'a.engine_id', '=', 'e.id')
                    ->select('a.id', 'e.name as engine_name')
                    ->where('a.id', $id)
                    ->first();

                if (!$header) {
                    throw new Exception('Konfigurasi Digital Print tidak ditemukan.');
                }

                $engineName = strtolower(
                    preg_replace('/\s+/', '', trim($header->engine_name))
                );

                if ($engineName !== 'digitalprinta3+') {
                    throw new Exception(
                        'Konfigurasi ini bukan milik mesin Digital Print A3+.'
                    );
                }

                $detailIds = collect($validated['pricing_rules'])
                    ->pluck('detail_id')
                    ->unique()
                    ->values();

                if ($detailIds->count() !== count($validated['pricing_rules'])) {
                    throw new Exception('Detail markup Digital Print duplikat.');
                }

                $existingDetails = DB::table('m_digital_pricing_rule_details')
                    ->where('digital_pricing_rule_id', $header->id)
                    ->whereIn('id', $detailIds)
                    ->get()
                    ->keyBy('id');

                if ($existingDetails->count() !== $detailIds->count()) {
                    throw new Exception(
                        'Salah satu detail markup tidak sesuai dengan konfigurasi Digital Print.'
                    );
                }

                $now = Carbon::now();
                $userName = Auth::user()->name;

                foreach ($validated['pricing_rules'] as $index => $rule) {
                    $existingDetail = $existingDetails[$rule['detail_id']];

                    if (
                        $existingDetail->price_type !== $rule['price_type'] ||
                        $existingDetail->print_type !== $rule['print_type']
                    ) {
                        throw new Exception(
                            'Tipe harga atau harga print pada baris ' .
                            ($index + 1) .
                            ' tidak sesuai dengan detail yang tersimpan.'
                        );
                    }

                    foreach ($tierRanges as $tier) {
                        $roundingValue =
                            $rule["{$tier}_rounding_value"] ?? null;

                        $detailQuery = DB::table(
                            'm_digital_pricing_rule_details'
                        )
                            ->where('digital_pricing_rule_id', $header->id)
                            ->where('price_type', $rule['price_type'])
                            ->where('print_type', $rule['print_type'])
                            ->where('quantity_tier', $tier);

                        if (!$detailQuery->exists()) {
                            throw new Exception(
                                'Tier ' . $tier . ' pada baris ' .
                                ($index + 1) .
                                ' tidak ditemukan.'
                            );
                        }

                        $detailQuery->update([
                                'markup_percentage' =>
                                $rule["{$tier}_markup_percentage"],
                                'rounding_value' =>
                                $roundingValue === '' ? null : $roundingValue,
                                'status' => $rule['status'],
                                'updated_by' => $userName,
                                'updated_at' => $now,
                            ]);
                    }
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Markup Digital Print A3+ berhasil diperbarui.',
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
                $header = DB::table('m_digital_pricing_rules as a')
                    ->join('m_engines as e', 'a.engine_id', '=', 'e.id')
                    ->leftJoin('m_locations as l', 'a.location_id', '=', 'l.id')
                    ->leftJoin('m_categories as c', 'a.category_id', '=', 'c.id')
                    ->leftJoin('m_vendors as v', 'a.vendor_id', '=', 'v.id')
                    ->select(
                        'a.id',
                        'e.name as engine_name',
                        'l.name as location_name',
                        'c.name as category_name',
                        'v.name as vendor_name'
                    )
                    ->where('a.id', $id)
                    ->first();

                if (!$header) {
                    throw new Exception(
                        'Data markup Digital Print tidak ditemukan.'
                    );
                }

                $engineName = strtolower(
                    preg_replace('/\s+/', '', trim($header->engine_name))
                );

                if ($engineName !== 'digitalprinta3+') {
                    throw new Exception(
                        'Konfigurasi ini bukan milik mesin Digital Print A3+.'
                    );
                }

                DB::table('m_digital_pricing_rule_details')
                    ->where('digital_pricing_rule_id', $header->id)
                    ->delete();

                DB::table('m_digital_pricing_rules')
                    ->where('id', $header->id)
                    ->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Markup Digital Print A3+ berhasil dihapus.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
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

                $pricingRules = DB::table('m_pricing_rules')
                    ->where('engine_id', $engine_id)
                    ->where('location_id', $location_id)
                    ->where('category_id', $category_id)
                    ->where(function ($query) use ($vendor_id) {
                        if ($vendor_id === 'null') {
                            $query->whereNull('vendor_id');
                        } else {
                            $query->where('vendor_id', $vendor_id);
                        }
                    })
                    ->get();

                /*
            |--------------------------------------------------------------------------
            | CEK DATA
            |--------------------------------------------------------------------------
            */

                if ($pricingRules->isEmpty()) {
                    throw new Exception(
                        'Data markup harga tidak ditemukan.'
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
                        'm_production_cost_details'
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
            | Pada titik ini seluruh rule sudah dipastikan belum digunakan
            | oleh Production Cost.
            |
            */

                DB::table('m_pricing_rules')
                    ->where('engine_id', $engine_id)
                    ->where('location_id', $location_id)
                    ->where('category_id', $category_id)
                    ->where(function ($query) use ($vendor_id) {
                        if ($vendor_id === 'null') {
                            $query->whereNull('vendor_id');
                        } else {
                            $query->where('vendor_id', $vendor_id);
                        }
                    })
                    ->delete();
            });

            /*
        |--------------------------------------------------------------------------
        | SUCCESS RESPONSE
        |--------------------------------------------------------------------------
        */

            return response()->json([
                'success' => true,
                'message' => 'Markup harga berhasil dihapus.'
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
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
                ? new DigitalPricingRulesImport()
                : new PricingRulesImport();

            Excel::import(
                $import,
                $request->file('file')
            );

            return response()->json([
                'success' => true,
                'message' =>
                $import->importedRows .
                    ' data markup harga ' .
                    ($pricingEngine === 'digital_print_a3'
                        ? 'Digital Print A3+'
                        : 'Large Format') .
                    ' berhasil diimport.',
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
                new DigitalPricingRulesExport(),
                'digital_pricing_rules.xlsx'
            );
        }

        return Excel::download(
            new PricingRulesExport(),
            'large_format_pricing_rules.xlsx'
        );
    }
}

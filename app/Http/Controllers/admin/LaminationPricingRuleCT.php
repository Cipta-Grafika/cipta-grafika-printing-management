<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Exports\LaminationPricingRulesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LaminationPricingRulesImport;

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
        /*
    |--------------------------------------------------------------------------
    | TOTAL SEMUA DATA
    |--------------------------------------------------------------------------
    */
        $totalRecords = DB::table('m_lamination_pricing_rules')
            ->select(
                'engine_id',
                'category_id',
                'location_id',
                'vendor_id'
            )
            ->groupBy(
                'engine_id',
                'category_id',
                'location_id',
                'vendor_id'
            )
            ->get()
            ->count();

        /*
    |--------------------------------------------------------------------------
    | QUERY UTAMA
    |--------------------------------------------------------------------------
    */
        $query = DB::table('m_lamination_pricing_rules as a')
            ->leftJoin(
                'm_engines as d',
                'a.engine_id',
                '=',
                'd.id'
            )
            ->leftJoin(
                'm_categories as b',
                'a.category_id',
                '=',
                'b.id'
            )
            ->leftJoin(
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
            ->select(
                'd.id as engine_id',
                'd.name as engine_name',
                'b.id as category_id',
                'b.name as category_name',
                'c.id as location_id',
                'c.name as location_name',
                'v.id as vendor_id',
                'v.name as vendor_name',
                DB::raw('COUNT(a.id) as jumlah_markup')
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
            | Outsourcing → Vendor wajib dan harus Active
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
            | Non-Outsourcing → Vendor harus kosong
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

    public function export()
    {
        return Excel::download(
            new LaminationPricingRulesExport(),
            'lamination_pricing_rules.xlsx'
        );
    }

    public function import(Request $request)
    {
        $request->validate(
            [
                'file' => 'required|file|mimes:xlsx,xls',
            ],
            [
                'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
                'file.file' => 'File yang dipilih tidak valid.',
                'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls).',
            ]
        );

        try {
            $import = new LaminationPricingRulesImport();

            Excel::import(
                $import,
                $request->file('file')
            );

            return response()->json([
                'success' => true,
                'message' =>
                $import->importedRows .
                    ' data markup harga laminasi berhasil diimport.',
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

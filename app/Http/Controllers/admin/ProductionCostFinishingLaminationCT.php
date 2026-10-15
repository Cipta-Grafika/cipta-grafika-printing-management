<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductionCostFinishingLaminationsExport;
use App\Exports\DigitalLaminationProductionCostsExport;
use App\Support\DigitalLaminationConfiguration;
use App\Imports\ProductionCostFinishingLaminationsImport;
use Carbon\Carbon;

class ProductionCostFinishingLaminationCT extends Controller
{
    public function index()
    {
        return view('admin.production-cost-finishing-lamination.index');
    }

    public function data(Request $request)
    {
        $validated = $request->validate([
            'pricing_engine' => ['nullable', 'in:large_format,digital_print_a3'],
        ]);

        if (($validated['pricing_engine'] ?? 'large_format') === 'digital_print_a3') {
            return $this->dataDigital($request);
        }

        /*
    |--------------------------------------------------------------------------
    | TOTAL SEMUA DATA
    |--------------------------------------------------------------------------
    |
    | 1 baris DataTable = 1 Engine.
    | Karena itu total record dihitung berdasarkan distinct engine_id.
    |
    */
        $totalRecords = DB::table('m_production_costs as pc')
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
                'm_lamination_production_cost_details as lpcd',
                'lpcd.production_cost_id',
                '=',
                'pc.id'
            )
            ->join(
                'm_lamination_production_cost_detail_sizes as lpcds',
                'lpcds.lamination_production_cost_detail_id',
                '=',
                'lpcd.id'
            )
            ->join(
                'm_engines as e',
                'e.id',
                '=',
                'pc.engine_id'
            )
            ->whereRaw('LOWER(TRIM(e.name)) = ?', ['large format'])
            ->distinct()
            ->count('e.id');

        /*
    |--------------------------------------------------------------------------
    | QUERY UTAMA
    |--------------------------------------------------------------------------
    |
    | 1 baris DataTable = 1 Engine.
    |
    | Data yang ditampilkan:
    | - engine_name
    | - location_names
    | - category_names
    | - lamination_count
    | - size_count
    |
    */
        $query = DB::table('m_production_costs as pc')
            ->join(
                'm_engines as e',
                'e.id',
                '=',
                'pc.engine_id'
            )
            ->join(
                'm_production_cost_locations as pcl',
                'pcl.production_cost_id',
                '=',
                'pc.id'
            )
            ->join(
                'm_locations as l',
                'l.id',
                '=',
                'pcl.location_id'
            )
            ->join(
                'm_production_cost_categories as pcc',
                'pcc.production_cost_id',
                '=',
                'pc.id'
            )
            ->join(
                'm_categories as c',
                'c.id',
                '=',
                'pcc.category_id'
            )
            ->join(
                'm_lamination_production_cost_details as lpcd',
                'lpcd.production_cost_id',
                '=',
                'pc.id'
            )
            ->join(
                'm_lamination_production_cost_detail_sizes as lpcds',
                'lpcds.lamination_production_cost_detail_id',
                '=',
                'lpcd.id'
            )
            ->whereRaw('LOWER(TRIM(e.name)) = ?', ['large format'])
            ->select(
                'e.id as engine_id',
                'e.name as engine_name',
                DB::raw(
                    "STRING_AGG(
                    DISTINCT l.name,
                    ', '
                    ORDER BY l.name
                ) as location_names"
                ),
                DB::raw(
                    "STRING_AGG(
                    DISTINCT c.name,
                    ', '
                    ORDER BY c.name
                ) as category_names"
                ),
                DB::raw(
                    'COUNT(DISTINCT lpcd.lamination_id) as lamination_count'
                ),
                DB::raw(
                    'COUNT(DISTINCT lpcds.lamination_size_id) as size_count'
                )
            )
            ->groupBy(
                'e.id',
                'e.name'
            );

        /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    |
    | Search berdasarkan:
    | - Mesin
    | - Lokasi
    | - Kategori
    |
    */
        $search = $request->input('search.value');

        if (!empty($search)) {
            $search = strtolower($search);

            $query->where(function ($q) use ($search) {
                $q->whereRaw(
                    'LOWER(e.name) LIKE ?',
                    ['%' . $search . '%']
                )
                    ->orWhereRaw(
                        'LOWER(l.name) LIKE ?',
                        ['%' . $search . '%']
                    )
                    ->orWhereRaw(
                        'LOWER(c.name) LIKE ?',
                        ['%' . $search . '%']
                    );
            });
        }

        /*
    |--------------------------------------------------------------------------
    | TOTAL DATA SETELAH FILTER
    |--------------------------------------------------------------------------
    |
    | Karena query sudah GROUP BY engine,
    | jumlah hasil query = jumlah engine setelah filter.
    |
    */
        $totalFiltered = (clone $query)
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
        $data = $query
            ->orderBy('e.name', 'asc')
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
            'data' => $data,
        ]);
    }

    private function dataDigital(Request $request)
    {
        $query = DB::table('m_digital_lamination_production_costs as pc')
            ->join('m_engines as e', 'e.id', '=', 'pc.engine_id')
            ->join('m_locations as l', 'l.id', '=', 'pc.location_id')
            ->leftJoin('m_vendors as v', 'v.id', '=', 'pc.vendor_id')
            ->leftJoin('m_digital_lamination_production_cost_details as d', 'd.digital_lamination_production_cost_id', '=', 'pc.id')
            ->whereRaw('LOWER(TRIM(e.name)) = ?', ['digital print a3+'])
            ->select(
                'pc.id as digital_lamination_production_cost_id',
                'e.id as engine_id',
                'e.name as engine_name',
                'l.name as location_names',
                'v.name as vendor_name',
                DB::raw('COUNT(DISTINCT d.lamination_id) as lamination_count'),
                DB::raw("'digital_print_a3' as pricing_engine")
            )
            ->groupBy('pc.id', 'e.id', 'e.name', 'l.name', 'v.name');

        $totalRecords = (clone $query)->get()->count();
        $search = $request->input('search.value');

        if (!empty($search)) {
            $search = '%' . strtolower($search) . '%';
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(e.name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(l.name) LIKE ?', [$search])
                    ->orWhereRaw('LOWER(v.name) LIKE ?', [$search])
                    ->orWhereExists(function ($categories) use ($search) {
                        $categories->selectRaw('1')
                            ->from('m_digital_lamination_production_cost_details as cd')
                            ->join('m_categories as c', 'c.id', '=', 'cd.category_id')
                            ->whereColumn('cd.digital_lamination_production_cost_id', 'pc.id')
                            ->whereRaw('LOWER(c.name) LIKE ?', [$search]);
                    });
            });
        }

        $totalFiltered = (clone $query)->get()->count();
        $data = $query
            ->orderBy('e.name')
            ->orderBy('l.name')
            ->orderBy('v.name')
            ->orderBy('pc.id')
            ->offset((int) $request->input('start', 0))
            ->limit((int) $request->input('length', 10))
            ->get();

        $categoryNames = DB::table('m_digital_lamination_production_cost_details as d')
            ->join('m_categories as c', 'c.id', '=', 'd.category_id')
            ->whereIn('d.digital_lamination_production_cost_id', $data->pluck('digital_lamination_production_cost_id'))
            ->select('d.digital_lamination_production_cost_id', 'c.id as category_id', 'c.name')
            ->distinct()
            ->orderBy('c.name')
            ->get()
            ->groupBy('digital_lamination_production_cost_id');

        $data->each(function ($row) use ($categoryNames) {
            $row->category_count = $categoryNames
                ->get($row->digital_lamination_production_cost_id, collect())
                ->count();
            $row->category_names = $categoryNames
                ->get($row->digital_lamination_production_cost_id, collect())
                ->pluck('name')
                ->implode(', ');
        });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function create()
    {
        $data['engines'] = DB::table('m_engines')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $data['locations'] = DB::table('m_locations')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $data['categories'] = DB::table('m_categories')
            ->orderBy('name')
            ->get();

        $data['laminations'] = DB::table('m_laminations')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        $data['vendors'] = DB::table('m_vendors')
            ->where('status', 'Active')
            ->get();

        return view(
            'admin.production-cost-finishing-lamination.create'
        )->with($data);
    }

    public function store(Request $request)
    {
        if ($this->hasDigitalPrintConfiguration($request)) {
            return $this->storeDigital($request);
        }

        $request->validate([
            'configurations' => 'required|array|min:1',

            'configurations.*.engine_id' => 'required|integer|exists:m_engines,id',

            'configurations.*.location_ids' => 'required|array|min:1',
            'configurations.*.location_ids.*' => 'required|integer|exists:m_locations,id',

            'configurations.*.category_ids' => 'required|array|min:1',
            'configurations.*.category_ids.*' => 'required|integer|exists:m_categories,id',

            'configurations.*.vendor_id' => 'nullable|integer|exists:m_vendors,id',

            'configurations.*.items' => 'required|array|min:1',

            'configurations.*.items.*.lamination_id' => 'required|integer|exists:m_laminations,id',

            'configurations.*.items.*.size_ids' => 'required|array|min:1',
            'configurations.*.items.*.size_ids.*' => 'required|integer|exists:m_lamination_sizes,id',

            'configurations.*.items.*.price_per_meter' => 'nullable',
            'configurations.*.items.*.production_cost' => 'nullable',
            'configurations.*.items.*.finishing_cost' => 'nullable',
            'configurations.*.items.*.vendor_price' => 'nullable',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

        $parseNumber = function ($value) {
            if ($value === null || $value === '') {
                return 0;
            }

            $value = trim((string) $value);

            if (str_contains($value, ',')) {
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                $value = str_replace('.', '', $value);
            }

            return (float) $value;
        };

        $roundUp = function ($value, $rounding) {
            if ($rounding === null || $rounding <= 0) {
                return $value;
            }

            return ceil($value / $rounding) * $rounding;
        };

        DB::beginTransaction();

        try {

            /*
        |--------------------------------------------------------------------------
        | Load Master
        |--------------------------------------------------------------------------
        */

            $engineIds = collect($request->configurations)
                ->pluck('engine_id')
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            $engines = DB::table('m_engines')
                ->whereIn('id', $engineIds)
                ->get()
                ->keyBy('id');

            $locations = DB::table('m_locations')
                ->get()
                ->keyBy('id');

            $categories = DB::table('m_categories')
                ->get()
                ->keyBy('id');

            $laminations = DB::table('m_laminations')
                ->where('status', 'Active')
                ->get()
                ->keyBy('id');

            $laminationSizes = DB::table('m_lamination_sizes')
                ->get()
                ->keyBy('id');

            $vendors = DB::table('m_vendors')
                ->where('status', 'Active')
                ->get()
                ->keyBy('id');

            /*
        |--------------------------------------------------------------------------
        | Pricing Rules
        |--------------------------------------------------------------------------
        |
        | Key:
        | engine_id | location_id | vendor_id | category_id | price_type
        |
        | Untuk Non-Outsourcing vendor_id = null
        |
        */

            $pricingRules = DB::table('m_lamination_pricing_rules')
                ->where('status', 'Active')
                ->get()
                ->keyBy(function ($rule) {
                    return $rule->engine_id
                        . '|'
                        . $rule->location_id
                        . '|'
                        . ($rule->vendor_id ?? 'null')
                        . '|'
                        . $rule->category_id
                        . '|'
                        . $rule->price_type;
                });

            /*
        |--------------------------------------------------------------------------
        | Normalized Rows
        |--------------------------------------------------------------------------
        */

            $normalizedRows = [];

            foreach ($request->configurations as $configurationIndex => $configuration) {

                $engineId = (int) $configuration['engine_id'];

                /*
            |--------------------------------------------------------------------------
            | Validate Engine
            |--------------------------------------------------------------------------
            */

                $engine = $engines->get($engineId);

                if (!$engine) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'configurations' => [
                            'Engine tidak ditemukan.'
                        ]
                    ]);
                }

                $engineName = strtolower(trim($engine->name));

                if ($engineName === 'digital print a3+') {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'warning',
                        'message' => 'Mesin Digital Print A3+ belum mempunyai master calculation rules.'
                    ], 422);
                }

                if ($engineName !== 'large format') {
                    DB::rollBack();

                    return response()->json([
                        'status' => 'warning',
                        'message' => 'Engine ' . $engine->name . ' belum mempunyai master calculation rules.'
                    ], 422);
                }

                /*
            |--------------------------------------------------------------------------
            | Locations
            |--------------------------------------------------------------------------
            */

                $locationIds = collect($configuration['location_ids'])
                    ->map(fn($id) => (int) $id)
                    ->values()
                    ->all();

                $selectedLocations = collect($locationIds)
                    ->map(fn($id) => $locations->get($id))
                    ->filter();

                if ($selectedLocations->count() !== count($locationIds)) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'configurations' => [
                            'Terdapat location yang tidak ditemukan.'
                        ]
                    ]);
                }

                foreach ($selectedLocations as $location) {
                    if ($location->status !== 'Active') {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'configurations' => [
                                'Location ' . $location->name . ' tidak aktif.'
                            ]
                        ]);
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | Detect Outsourcing
            |--------------------------------------------------------------------------
            */

                $outsourcingLocations = $selectedLocations
                    ->filter(function ($location) {
                        return strtolower(trim($location->name)) === 'outsourcing';
                    });

                $isOutsourcing = $outsourcingLocations->isNotEmpty();

                /*
            |--------------------------------------------------------------------------
            | Outsourcing tidak boleh digabung dengan location lain
            |--------------------------------------------------------------------------
            */

                if ($isOutsourcing && count($locationIds) > 1) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'configurations' => [
                            'Lokasi Outsourcing tidak dapat digabung dengan lokasi lainnya.'
                        ]
                    ]);
                }

                /*
            |--------------------------------------------------------------------------
            | Vendor
            |--------------------------------------------------------------------------
            */

                $vendorId = $configuration['vendor_id'] ?? null;

                if ($isOutsourcing) {

                    if (empty($vendorId)) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'configurations' => [
                                'Vendor wajib dipilih untuk lokasi Outsourcing.'
                            ]
                        ]);
                    }

                    $vendorId = (int) $vendorId;

                    $vendor = $vendors->get($vendorId);

                    if (!$vendor) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'configurations' => [
                                'Vendor yang dipilih tidak aktif atau tidak ditemukan.'
                            ]
                        ]);
                    }
                } else {
                    /*
                |--------------------------------------------------------------------------
                | Non-Outsourcing tidak menggunakan vendor
                |--------------------------------------------------------------------------
                */

                    $vendorId = null;
                }

                /*
            |--------------------------------------------------------------------------
            | Categories
            |--------------------------------------------------------------------------
            */

                $categoryIds = collect($configuration['category_ids'])
                    ->map(fn($id) => (int) $id)
                    ->values()
                    ->all();

                foreach ($categoryIds as $categoryId) {
                    $category = $categories->get($categoryId);

                    if (!$category) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'configurations' => [
                                'Category tidak ditemukan.'
                            ]
                        ]);
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | Items
            |--------------------------------------------------------------------------
            */

                foreach ($configuration['items'] as $itemIndex => $item) {

                    $laminationId = (int) $item['lamination_id'];

                    $lamination = $laminations->get($laminationId);

                    if (!$lamination) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'configurations' => [
                                'Laminasi yang dipilih tidak aktif atau tidak ditemukan.'
                            ]
                        ]);
                    }

                    /*
                |--------------------------------------------------------------------------
                | Validate Lamination Sizes
                |--------------------------------------------------------------------------
                */

                    $sizeIds = collect($item['size_ids'])
                        ->map(fn($id) => (int) $id)
                        ->values()
                        ->all();

                    foreach ($sizeIds as $sizeId) {

                        $size = $laminationSizes->get($sizeId);

                        if (!$size) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'configurations' => [
                                    'Ukuran laminasi tidak ditemukan.'
                                ]
                            ]);
                        }

                        if ((int) $size->lamination_id !== $laminationId) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'configurations' => [
                                    'Ukuran laminasi tidak sesuai dengan laminasi yang dipilih.'
                                ]
                            ]);
                        }
                    }

                    /*
                |--------------------------------------------------------------------------
                | Cost
                |--------------------------------------------------------------------------
                */

                    if ($isOutsourcing) {

                        /*
                    |--------------------------------------------------------------------------
                    | Outsourcing
                    |--------------------------------------------------------------------------
                    |
                    | Untuk Outsourcing hanya vendor_price yang digunakan.
                    | Kolom cost lama tetap diisi 0 karena NOT NULL.
                    |
                    */

                        if (
                            !array_key_exists('vendor_price', $item) ||
                            $item['vendor_price'] === null ||
                            $item['vendor_price'] === ''
                        ) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'configurations' => [
                                    'Vendor Price wajib diisi untuk laminasi Outsourcing.'
                                ]
                            ]);
                        }

                        $vendorPrice = $parseNumber($item['vendor_price']);

                        if ($vendorPrice < 0) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'configurations' => [
                                    'Vendor Price tidak boleh kurang dari 0.'
                                ]
                            ]);
                        }

                        $pricePerMeter = 0;
                        $productionCost = 0;
                        $finishingCost = 0;

                        $totalCost = $vendorPrice;
                    } else {

                        /*
                    |--------------------------------------------------------------------------
                    | Non-Outsourcing
                    |--------------------------------------------------------------------------
                    |
                    | Logic existing dipertahankan.
                    |
                    */

                        if (
                            !array_key_exists('price_per_meter', $item) ||
                            $item['price_per_meter'] === null ||
                            $item['price_per_meter'] === ''
                        ) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'configurations' => [
                                    'Harga per meter wajib diisi.'
                                ]
                            ]);
                        }

                        if (
                            !array_key_exists('production_cost', $item) ||
                            $item['production_cost'] === null ||
                            $item['production_cost'] === ''
                        ) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'configurations' => [
                                    'Production Cost wajib diisi.'
                                ]
                            ]);
                        }

                        if (
                            !array_key_exists('finishing_cost', $item) ||
                            $item['finishing_cost'] === null ||
                            $item['finishing_cost'] === ''
                        ) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'configurations' => [
                                    'Finishing Cost wajib diisi.'
                                ]
                            ]);
                        }

                        $pricePerMeter = $parseNumber($item['price_per_meter']);
                        $productionCost = $parseNumber($item['production_cost']);
                        $finishingCost = $parseNumber($item['finishing_cost']);

                        if (
                            $pricePerMeter < 0 ||
                            $productionCost < 0 ||
                            $finishingCost < 0
                        ) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'configurations' => [
                                    'Biaya tidak boleh kurang dari 0.'
                                ]
                            ]);
                        }

                        $vendorPrice = 0;

                        /*
                    |--------------------------------------------------------------------------
                    | Existing calculation - jangan diubah
                    |--------------------------------------------------------------------------
                    */

                        $totalCost =
                            ($pricePerMeter * 1.2)
                            + $productionCost
                            + $finishingCost;
                    }

                    /*
                |--------------------------------------------------------------------------
                | Process Location + Category
                |--------------------------------------------------------------------------
                */

                    foreach ($locationIds as $locationId) {

                        foreach ($categoryIds as $categoryId) {

                            $vendorKey = $isOutsourcing
                                ? $vendorId
                                : 'null';

                            /*
                        |--------------------------------------------------------------------------
                        | Pricing Rule Key
                        |--------------------------------------------------------------------------
                        */

                            $generalKey =
                                $engineId
                                . '|'
                                . $locationId
                                . '|'
                                . $vendorKey
                                . '|'
                                . $categoryId
                                . '|general';

                            $divisionKey =
                                $engineId
                                . '|'
                                . $locationId
                                . '|'
                                . $vendorKey
                                . '|'
                                . $categoryId
                                . '|division';

                            $plainKey =
                                $engineId
                                . '|'
                                . $locationId
                                . '|'
                                . $vendorKey
                                . '|'
                                . $categoryId
                                . '|plain';

                            $generalRule = $pricingRules->get($generalKey);
                            $divisionRule = $pricingRules->get($divisionKey);
                            $plainRule = $pricingRules->get($plainKey);

                            /*
                        |--------------------------------------------------------------------------
                        | General + Division wajib
                        |--------------------------------------------------------------------------
                        */

                            if (!$generalRule || !$divisionRule) {
                                throw \Illuminate\Validation\ValidationException::withMessages([
                                    'configurations' => [
                                        'Pricing Rule General dan Division belum tersedia untuk '
                                            . 'Engine ' . $engine->name
                                            . ', Location ' . $locations->get($locationId)->name
                                            . ', Category ' . $categories->get($categoryId)->name
                                            . ($isOutsourcing
                                                ? ', Vendor ' . $vendors->get($vendorId)->name
                                                : '')
                                            . '.'
                                    ]
                                ]);
                            }

                            /*
                        |--------------------------------------------------------------------------
                        | General Price
                        |--------------------------------------------------------------------------
                        */

                            $generalMarkup = (float) $generalRule->markup_percentage;
                            $generalRounding = $generalRule->rounding_value !== null
                                ? (float) $generalRule->rounding_value
                                : null;

                            $generalPrice = $totalCost
                                + ($totalCost * ($generalMarkup / 100));

                            $generalPrice = $roundUp(
                                $generalPrice,
                                $generalRounding
                            );

                            /*
                        |--------------------------------------------------------------------------
                        | Division Price
                        |--------------------------------------------------------------------------
                        */

                            $divisionMarkup = (float) $divisionRule->markup_percentage;
                            $divisionRounding = $divisionRule->rounding_value !== null
                                ? (float) $divisionRule->rounding_value
                                : null;

                            $divisionPrice = $generalPrice
                                * ($divisionMarkup / 100);

                            $divisionPrice = $roundUp(
                                $divisionPrice,
                                $divisionRounding
                            );

                            /*
                        |--------------------------------------------------------------------------
                        | Plain Price
                        |--------------------------------------------------------------------------
                        */

                            $plainPrice = 0;
                            $plainPricingRuleId = null;

                            /*
                        |--------------------------------------------------------------------------
                        | Outsourcing:
                        | Plain Price = 0
                        |--------------------------------------------------------------------------
                        */

                            if (!$isOutsourcing && $plainRule) {

                                $plainMarkup = (float) $plainRule->markup_percentage;

                                $plainRounding = $plainRule->rounding_value !== null
                                    ? (float) $plainRule->rounding_value
                                    : null;

                                $plainPrice = $pricePerMeter
                                    + ($pricePerMeter * ($plainMarkup / 100));

                                $plainPrice = $roundUp(
                                    $plainPrice,
                                    $plainRounding
                                );

                                $plainPricingRuleId = $plainRule->id;
                            }

                            /*
                        |--------------------------------------------------------------------------
                        | Duplicate Check
                        |--------------------------------------------------------------------------
                        */

                            $duplicateQuery = DB::table(
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
                                ->join(
                                    'm_production_cost_categories as pcc',
                                    'pcc.production_cost_id',
                                    '=',
                                    'pc.id'
                                )
                                ->join(
                                    'm_lamination_production_cost_detail_sizes as lpcds',
                                    'lpcds.lamination_production_cost_detail_id',
                                    '=',
                                    'lpcd.id'
                                )
                                ->where('pc.engine_id', $engineId)
                                ->where('pcl.location_id', $locationId)
                                ->where('pcc.category_id', $categoryId)
                                ->where('lpcd.lamination_id', $laminationId)
                                ->whereIn('lpcds.lamination_size_id', $sizeIds);

                            if ($isOutsourcing) {

                                $duplicateQuery
                                    ->where('lpcd.vendor_id', $vendorId)
                                    ->where('lpcd.vendor_price', $vendorPrice);
                            } else {

                                $duplicateQuery
                                    ->whereNull('lpcd.vendor_id')
                                    ->where('lpcd.price_per_meter', $pricePerMeter)
                                    ->where('lpcd.production_cost', $productionCost)
                                    ->where('lpcd.finishing_cost', $finishingCost);
                            }

                            if ($duplicateQuery->exists()) {
                                throw \Illuminate\Validation\ValidationException::withMessages([
                                    'configurations' => [
                                        'Production Cost Laminasi dengan konfigurasi yang sama sudah tersedia.'
                                    ]
                                ]);
                            }

                            /*
                        |--------------------------------------------------------------------------
                        | Normalized Row
                        |--------------------------------------------------------------------------
                        */

                            $normalizedRows[] = [
                                'configuration_index' => $configurationIndex,

                                'engine_id' => $engineId,
                                'location_id' => $locationId,
                                'category_id' => $categoryId,

                                'vendor_id' => $vendorId,
                                'vendor_price' => $vendorPrice,

                                'lamination_id' => $laminationId,
                                'size_ids' => $sizeIds,

                                'price_per_meter' => $pricePerMeter,
                                'production_cost' => $productionCost,
                                'finishing_cost' => $finishingCost,
                                'total_cost' => $totalCost,

                                'general_price' => $generalPrice,
                                'division_price' => $divisionPrice,
                                'plain_price' => $plainPrice,

                                'general_pricing_rule_id' => $generalRule->id,
                                'division_pricing_rule_id' => $divisionRule->id,
                                'plain_pricing_rule_id' => $plainPricingRuleId,
                            ];
                        }
                    }
                }
            }

            /*
        |--------------------------------------------------------------------------
        | Group by Configuration + Location
        |--------------------------------------------------------------------------
        |
        | Satu Production Cost Header untuk:
        | configuration + location
        |
        */

            $groupedConfigurations = collect($normalizedRows)
                ->groupBy(function ($row) {
                    return $row['configuration_index']
                        . '|'
                        . $row['location_id'];
                });

            foreach ($groupedConfigurations as $rows) {

                $firstRow = $rows->first();

                /*
            |--------------------------------------------------------------------------
            | Production Cost Header
            |--------------------------------------------------------------------------
            */

                $productionCostId = DB::table('m_production_costs')
                    ->insertGetId([
                        'engine_id' => $firstRow['engine_id'],

                        'created_by' => Auth::user()->name ?? 'System',
                        'created_at' => Carbon::now(),

                        'updated_by' => Auth::user()->name ?? 'System',
                        'updated_at' => Carbon::now(),
                    ]);

                /*
            |--------------------------------------------------------------------------
            | Location
            |--------------------------------------------------------------------------
            */

                DB::table('m_production_cost_locations')
                    ->insert([
                        'production_cost_id' => $productionCostId,
                        'location_id' => $firstRow['location_id'],

                        'created_by' => Auth::user()->name ?? 'System',
                        'created_at' => Carbon::now(),

                        'updated_by' => Auth::user()->name ?? 'System',
                        'updated_at' => Carbon::now(),
                    ]);

                /*
            |--------------------------------------------------------------------------
            | Categories
            |--------------------------------------------------------------------------
            */

                $categoryIds = $rows
                    ->pluck('category_id')
                    ->unique()
                    ->values();

                foreach ($categoryIds as $categoryId) {

                    DB::table('m_production_cost_categories')
                        ->insert([
                            'production_cost_id' => $productionCostId,
                            'category_id' => $categoryId,

                            'created_by' => Auth::user()->name ?? 'System',
                            'created_at' => Carbon::now(),

                            'updated_by' => Auth::user()->name ?? 'System',
                            'updated_at' => Carbon::now(),
                        ]);
                }

                /*
            |--------------------------------------------------------------------------
            | Detail
            |--------------------------------------------------------------------------
            |
            | Satu detail untuk:
            | category + lamination
            |
            */

                $groupedDetails = $rows->groupBy(function ($row) {
                    return $row['category_id']
                        . '|'
                        . $row['lamination_id'];
                });

                foreach ($groupedDetails as $detailRows) {

                    $firstDetail = $detailRows->first();

                    /*
                |--------------------------------------------------------------------------
                | Lamination Production Cost Detail
                |--------------------------------------------------------------------------
                */

                    $detailId = DB::table(
                        'm_lamination_production_cost_details'
                    )->insertGetId([
                        'production_cost_id' => $productionCostId,

                        'lamination_id' => $firstDetail['lamination_id'],

                        'production_cost' => $firstDetail['production_cost'],
                        'finishing_cost' => $firstDetail['finishing_cost'],
                        'total_cost' => $firstDetail['total_cost'],
                        'price_per_meter' => $firstDetail['price_per_meter'],

                        'general_price' => $firstDetail['general_price'],
                        'division_price' => $firstDetail['division_price'],
                        'plain_price' => $firstDetail['plain_price'],

                        'general_pricing_rule_id' =>
                        $firstDetail['general_pricing_rule_id'],

                        'division_pricing_rule_id' =>
                        $firstDetail['division_pricing_rule_id'],

                        'plain_pricing_rule_id' =>
                        $firstDetail['plain_pricing_rule_id'],

                        'vendor_id' => $firstDetail['vendor_id'],
                        'vendor_price' => $firstDetail['vendor_price'],

                        'created_by' =>
                        Auth::user()->name ?? 'System',

                        'created_at' => Carbon::now(),

                        'updated_by' =>
                        Auth::user()->name ?? 'System',

                        'updated_at' => Carbon::now(),
                    ]);

                    /*
                |--------------------------------------------------------------------------
                | Lamination Sizes
                |--------------------------------------------------------------------------
                */

                    $sizeIds = $detailRows
                        ->pluck('size_ids')
                        ->flatten()
                        ->unique()
                        ->values();

                    foreach ($sizeIds as $sizeId) {

                        DB::table(
                            'm_lamination_production_cost_detail_sizes'
                        )->insert([
                            'lamination_production_cost_detail_id' => $detailId,
                            'lamination_size_id' => $sizeId,

                            'created_by' =>
                            Auth::user()->name ?? 'System',

                            'created_at' => Carbon::now(),

                            'updated_by' =>
                            Auth::user()->name ?? 'System',

                            'updated_at' => Carbon::now(),
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Production Cost Laminasi berhasil disimpan.'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {

            DB::rollBack();

            throw $e;
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan Production Cost Laminasi.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function hasDigitalPrintConfiguration(Request $request): bool
    {
        $configurations = $request->input('configurations');

        if (!is_array($configurations)) {
            return false;
        }

        $engineIds = collect($configurations)
            ->filter(fn($configuration) => is_array($configuration))
            ->pluck('engine_id')
            ->filter(fn($id) => is_numeric($id))
            ->map(fn($id) => (int) $id)
            ->unique();

        return DB::table('m_engines')
            ->whereIn('id', $engineIds)
            ->whereRaw('LOWER(TRIM(name)) = ?', ['digital print a3+'])
            ->exists();
    }

    private function storeDigital(Request $request)
    {
        return $this->saveDigital($request);
    }

    private function findDigitalProductionCost($id)
    {
        $decodedId = base64_decode($id, true);
        if ($decodedId === false || !ctype_digit($decodedId) || (int) $decodedId <= 0) {
            abort(404);
        }

        $productionCost = DB::table('m_digital_lamination_production_costs as pc')
            ->join('m_engines as e', 'e.id', '=', 'pc.engine_id')
            ->join('m_locations as l', 'l.id', '=', 'pc.location_id')
            ->leftJoin('m_vendors as v', 'v.id', '=', 'pc.vendor_id')
            ->where('pc.id', $decodedId)
            ->whereRaw('LOWER(TRIM(e.name)) = ?', ['digital print a3+'])
            ->select('pc.*', 'e.name as engine_name', 'l.name as location_name', 'v.name as vendor_name')
            ->first();

        abort_unless($productionCost, 404);

        return $productionCost;
    }

    public function detailsDigital($id)
    {
        $productionCost = $this->findDigitalProductionCost($id);
        $details = DB::table('m_digital_lamination_production_cost_details as d')
            ->join('m_laminations as l', 'l.id', '=', 'd.lamination_id')
            ->join('m_categories as c', 'c.id', '=', 'd.category_id')
            ->where('d.digital_lamination_production_cost_id', $productionCost->id)
            ->select('d.*', 'l.name as lamination_name', 'c.name as category_name')
            ->orderBy('c.name')
            ->orderBy('l.name')
            ->orderBy('d.id')
            ->get();
        $prices = DB::table('m_digital_lamination_production_cost_prices')
            ->whereIn('digital_lamination_production_cost_detail_id', $details->pluck('id'))
            ->get()
            ->groupBy('digital_lamination_production_cost_detail_id');

        $tierLabels = [
            'tier_1' => '1 lbr',
            'tier_2_5' => '2-5 lbr',
            'tier_6_50' => '6-50 lbr',
            'tier_100' => '100 lbr',
            'tier_300' => '300 lbr',
            'tier_500' => '500 lbr',
        ];
        $priceGroups = [
            'general_single' => ['general', 'single', 'Harga Umum · Laminasi 1 Muka'],
            'general_double' => ['general', 'double', 'Harga Umum · Laminasi 2 Muka'],
            'division_single' => ['division', 'single', 'Harga Divisi · Laminasi 1 Muka'],
            'division_double' => ['division', 'double', 'Harga Divisi · Laminasi 2 Muka'],
        ];

        $details->each(function ($detail) use ($prices, $priceGroups, $tierLabels) {
            $detailPrices = ($prices->get($detail->id) ?? collect())->keyBy(
                fn($price) => $price->price_type . '|' . $price->print_type . '|' . $price->quantity_tier
            );
            $detail->prices = [];
            foreach ($priceGroups as $groupKey => [$priceType, $printType]) {
                foreach (array_keys($tierLabels) as $tier) {
                    $detail->prices[$groupKey][$tier] = $detailPrices->get($priceType . '|' . $printType . '|' . $tier);
                }
            }
        });

        $categories = $details->groupBy('category_id')->map(function ($categoryDetails) use ($priceGroups, $tierLabels) {
            $markups = [];
            foreach (array_keys($priceGroups) as $groupKey) {
                foreach (array_keys($tierLabels) as $tier) {
                    // Append dapat menyimpan snapshot markup berbeda dalam kategori yang sama.
                    $values = $categoryDetails
                        ->map(fn($detail) => $detail->prices[$groupKey][$tier])
                        ->filter()
                        ->map(fn($price) => rtrim(rtrim(number_format((float) $price->markup_percentage, 2, ',', ''), '0'), ',') . '%')
                        ->unique()->values();
                    $markups[$groupKey][$tier] = $values->isEmpty() ? '-' : $values->implode(' / ');
                }
            }

            return (object) [
                'category_name' => $categoryDetails->first()->category_name,
                'markups' => $markups,
                'details' => $categoryDetails->values(),
            ];
        })->values();
        $categories = $categories->groupBy(
            fn($category) => DigitalLaminationConfiguration::signature($category->details, $prices)
        )->map(function ($sameCategories) {
            $category = $sameCategories->first();
            $category->category_names = $sameCategories->pluck('category_name')->values();

            return $category;
        })->values();
        $priceGroups = collect($priceGroups)->map(fn($group) => $group[2]);

        return view('admin.production-cost-finishing-lamination.details-digital', compact(
            'productionCost', 'categories', 'tierLabels', 'priceGroups'
        ));
    }

    public function editDigital($id)
    {
        $productionCost = $this->findDigitalProductionCost($id);
        $details = DB::table('m_digital_lamination_production_cost_details')
            ->where('digital_lamination_production_cost_id', $productionCost->id)
            ->orderBy('lamination_id')
            ->orderBy('id')
            ->get();

        // Gabungkan kategori hanya jika seluruh item dan biaya sama; data berbeda tetap terpisah.
        $groups = $details->groupBy('category_id')
            ->map(function ($categoryDetails, $categoryId) {
                return [
                    'category_ids' => [(int) $categoryId],
                    'items' => $categoryDetails->map(fn($detail) => [
                        'lamination_id' => (int) $detail->lamination_id,
                        'unit_price' => number_format((float) $detail->paper_cost, 2, ',', '.'),
                        'click_price' => number_format((float) $detail->click_cost, 2, ',', '.'),
                    ])->values()->all(),
                ];
            })
            ->groupBy(fn($group) => json_encode($group['items']))
            ->map(fn($sameGroups) => [
                'category_ids' => $sameGroups->pluck('category_ids')->flatten()->values()->all(),
                'items' => $sameGroups->first()['items'],
            ])
            ->values();

        $laminations = DB::table('m_laminations')
            ->where(function ($query) use ($details) {
                $query->where('status', 'Active')->orWhereIn('id', $details->pluck('lamination_id'));
            })
            ->orderBy('name')
            ->get(['id', 'name', 'status']);
        $categories = DB::table('m_categories')->orderBy('name')->get(['id', 'name']);

        return view('admin.production-cost-finishing-lamination.edit-digital', compact(
            'productionCost', 'groups', 'laminations', 'categories'
        ));
    }

    public function updateDigital(Request $request, $id)
    {
        $productionCost = $this->findDigitalProductionCost($id);
        $request->validate([
            'configurations' => 'required|array|min:1',
            'configurations.*' => 'required|array',
        ]);

        $configurations = array_map(function ($configuration) use ($productionCost) {
            $configuration['engine_id'] = $productionCost->engine_id;
            $configuration['location_ids'] = [$productionCost->location_id];
            $configuration['vendor_id'] = $productionCost->vendor_id;

            return $configuration;
        }, $request->input('configurations'));
        $request->merge(['configurations' => $configurations]);

        return $this->saveDigital($request, (int) $productionCost->id);
    }

    private function saveDigital(Request $request, ?int $editingId = null)
    {
        $moneyRules = ['required', 'string', 'max:20', 'regex:/^(?:[0-9]+|[0-9]{1,3}(?:\.[0-9]{3})+)(?:,[0-9]{1,2})?$/'];

        $request->validate([
            'configurations' => 'required|array|min:1',
            'configurations.*' => 'required|array',
            'configurations.*.engine_id' => 'required|integer|exists:m_engines,id',
            'configurations.*.location_ids' => 'required|array|min:1',
            'configurations.*.location_ids.*' => 'required|integer|exists:m_locations,id',
            'configurations.*.category_ids' => 'required|array|min:1',
            'configurations.*.category_ids.*' => 'required|integer|exists:m_categories,id',
            'configurations.*.vendor_id' => 'nullable|integer|exists:m_vendors,id',
            'configurations.*.items' => 'required|array|min:1',
            'configurations.*.items.*' => 'required|array',
            'configurations.*.items.*.lamination_id' => 'required|integer|exists:m_laminations,id',
            'configurations.*.items.*.unit_price' => $moneyRules,
            'configurations.*.items.*.click_price' => $moneyRules,
        ], [
            'configurations.*.category_ids.required' => 'Minimal pilih satu kategori.',
            'configurations.*.items.*.unit_price.required' => 'Harga Satuan wajib diisi.',
            'configurations.*.items.*.click_price.required' => 'Harga Klik wajib diisi.',
            'configurations.*.items.*.unit_price.regex' => 'Harga Satuan harus memakai titik untuk ribuan dan koma untuk desimal, maksimal 2 angka desimal.',
            'configurations.*.items.*.click_price.regex' => 'Harga Klik harus memakai titik untuk ribuan dan koma untuk desimal, maksimal 2 angka desimal.',
        ]);

        $roundUpToMultiple = function ($value, $rounding) {
            if ($rounding === null || (float) $rounding <= 0) {
                return $value;
            }

            return ceil(round($value / (float) $rounding, 6)) * (float) $rounding;
        };

        $tiers = ['tier_1', 'tier_2_5', 'tier_6_50', 'tier_100', 'tier_300', 'tier_500'];
        $maximumAmount = 9999999999.99;
        $userName = Auth::user()->name;

        try {
            DB::transaction(function () use ($request, $roundUpToMultiple, $tiers, $maximumAmount, $userName, $editingId) {
                // Serialisasi penyimpanan per mesin agar request bersamaan tidak membuat duplikasi.
                $engines = DB::table('m_engines')
                    ->whereIn('id', collect($request->input('configurations'))->pluck('engine_id')->unique())
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');
                $locations = DB::table('m_locations')->get()->keyBy('id');
                $categories = DB::table('m_categories')->get()->keyBy('id');
                $vendors = DB::table('m_vendors')->where('status', 'Active')->get()->keyBy('id');
                $savedLaminationIds = collect();
                if ($editingId !== null) {
                    $editingHeader = DB::table('m_digital_lamination_production_costs')
                        ->where('id', $editingId)->lockForUpdate()->first();
                    abort_unless($editingHeader, 404);
                    $configuration = $request->input('configurations')[array_key_first($request->input('configurations'))];
                    if (
                        (int) $editingHeader->engine_id !== (int) $configuration['engine_id'] ||
                        (int) $editingHeader->location_id !== (int) $configuration['location_ids'][0] ||
                        $editingHeader->vendor_id != $configuration['vendor_id']
                    ) {
                        throw ValidationException::withMessages([
                            'configurations' => 'Konfigurasi telah berubah. Muat ulang halaman edit.'
                        ]);
                    }
                    $savedLaminationIds = DB::table('m_digital_lamination_production_cost_details')
                        ->where('digital_lamination_production_cost_id', $editingId)->pluck('lamination_id');
                    DB::table('m_digital_lamination_production_cost_details')
                        ->where('digital_lamination_production_cost_id', $editingId)->delete();
                }
                $laminations = DB::table('m_laminations')
                    ->where(function ($query) use ($savedLaminationIds) {
                        $query->where('status', 'Active')->orWhereIn('id', $savedLaminationIds);
                    })
                    ->get()->keyBy('id');
                $usedKeys = [];

                foreach ($request->input('configurations') as $configurationIndex => $configuration) {
                    $prefix = 'configurations.' . $configurationIndex;
                    $engineId = (int) $configuration['engine_id'];
                    $engine = $engines->get($engineId);

                    if (!$engine || strtolower(trim($engine->name)) !== 'digital print a3+') {
                        throw ValidationException::withMessages([
                            $prefix . '.engine_id' =>
                            'Konfigurasi Digital Print A3+ harus disimpan terpisah dari konfigurasi mesin lain.'
                        ]);
                    }

                    if (strtolower(trim($engine->status)) !== 'active') {
                        throw ValidationException::withMessages([
                            $prefix . '.engine_id' => 'Mesin Digital Print A3+ tidak aktif.'
                        ]);
                    }

                    $locationIds = array_map('intval', $configuration['location_ids']);
                    $categoryIds = array_map('intval', $configuration['category_ids']);

                    foreach (['location_ids' => $locationIds, 'category_ids' => $categoryIds] as $field => $ids) {
                        if (count($ids) !== count(array_unique($ids))) {
                            throw ValidationException::withMessages([
                                $prefix . '.' . $field => 'Lokasi atau kategori tidak boleh dipilih lebih dari satu kali.'
                            ]);
                        }
                    }

                    $isOutsourcing = false;
                    foreach ($locationIds as $locationId) {
                        $location = $locations->get($locationId);
                        if (!$location || strtolower(trim($location->status)) !== 'active') {
                            throw ValidationException::withMessages([
                                $prefix . '.location_ids' => 'Lokasi tidak ditemukan atau tidak aktif.'
                            ]);
                        }
                        $isOutsourcing = $isOutsourcing || strtolower(trim($location->name)) === 'outsourcing';
                    }

                    if ($isOutsourcing && count($locationIds) > 1) {
                        throw ValidationException::withMessages([
                            $prefix . '.location_ids' => 'Lokasi Outsourcing tidak dapat digabung dengan lokasi lain.'
                        ]);
                    }

                    $vendorId = null;
                    if ($isOutsourcing) {
                        $vendorId = (int) ($configuration['vendor_id'] ?? 0);
                        if (!$vendors->has($vendorId)) {
                            throw ValidationException::withMessages([
                                $prefix . '.vendor_id' => 'Master Vendor wajib dipilih dan harus aktif untuk lokasi Outsourcing.'
                            ]);
                        }
                    }

                    foreach ($locationIds as $locationId) {
                        $scopeLabel = $locations->get($locationId)->name .
                            ($vendorId === null ? '' : ' - ' . $vendors->get($vendorId)->name);
                        $headerQuery = DB::table('m_digital_lamination_production_costs')
                            ->where('engine_id', $engineId)
                            ->where('location_id', $locationId)
                            ->where('vendor_id', $vendorId);
                        if ($editingId !== null) {
                            $headerQuery->where('id', $editingId);
                        }
                        $header = $headerQuery->lockForUpdate()->first();
                        $audit = ['updated_by' => $userName, 'updated_at' => now()];
                        if ($header) {
                            $headerId = $header->id;
                            DB::table('m_digital_lamination_production_costs')->where('id', $headerId)->update($audit);
                        } else {
                            $headerId = DB::table('m_digital_lamination_production_costs')->insertGetId([
                                'engine_id' => $engineId,
                                'location_id' => $locationId,
                                'vendor_id' => $vendorId,
                                'created_by' => $userName,
                                'created_at' => now(),
                            ] + $audit);
                        }

                        foreach ($categoryIds as $categoryId) {
                            $category = $categories->get($categoryId);
                            if (!$category) {
                                throw ValidationException::withMessages([
                                    $prefix . '.category_ids' => 'Kategori tidak ditemukan.'
                                ]);
                            }

                            $ruleGroups = DB::table('m_digital_lamination_pricing_rules as r')
                                ->join('m_digital_lamination_pricing_rule_details as d', 'd.digital_lamination_pricing_rule_id', '=', 'r.id')
                                ->where('r.engine_id', $engineId)
                                ->where('r.location_id', $locationId)
                                ->where('r.vendor_id', $vendorId)
                                ->where('r.category_id', $categoryId)
                                ->where('d.status', 'Active')
                                ->select('d.*')
                                ->get()
                                ->groupBy(fn($rule) => $rule->price_type . '|' . $rule->print_type . '|' . $rule->quantity_tier);

                            foreach (['general', 'division'] as $priceType) {
                                foreach (['single', 'double'] as $printType) {
                                    foreach ($tiers as $tier) {
                                        $rules = $ruleGroups->get($priceType . '|' . $printType . '|' . $tier);
                                        if (!$rules || $rules->count() !== 1) {
                                            throw ValidationException::withMessages([
                                                $prefix . '.category_ids' =>
                                                'Pricing rule laminasi Digital Print A3+ untuk kategori "' . $category->name .
                                                '" di lokasi ' . $scopeLabel .
                                                ' harus memiliki 24 tier aktif lengkap tanpa duplikasi (' .
                                                $priceType . ' ' . $printType . ' ' . $tier . ').'
                                            ]);
                                        }
                                    }
                                }
                            }

                            foreach ($configuration['items'] as $itemIndex => $item) {
                                $itemPrefix = $prefix . '.items.' . $itemIndex;
                                $laminationId = (int) $item['lamination_id'];
                                $lamination = $laminations->get($laminationId);
                                if (!$lamination) {
                                    throw ValidationException::withMessages([
                                        $itemPrefix . '.lamination_id' => 'Laminasi tidak ditemukan atau tidak aktif.'
                                    ]);
                                }

                                $key = $engineId . '|' . $locationId . '|' . ($vendorId ?? 'null') . '|' . $categoryId . '|' . $laminationId;
                                $exists = DB::table('m_digital_lamination_production_cost_details as d')
                                    ->join('m_digital_lamination_production_costs as h', 'd.digital_lamination_production_cost_id', '=', 'h.id')
                                    ->where('h.engine_id', $engineId)
                                    ->where('h.location_id', $locationId)
                                    ->where('h.vendor_id', $vendorId)
                                    ->where('d.category_id', $categoryId)
                                    ->where('d.lamination_id', $laminationId)
                                    ->exists();
                                if (isset($usedKeys[$key]) || $exists) {
                                    throw ValidationException::withMessages([
                                        $itemPrefix . '.lamination_id' =>
                                        'Ongkos produksi laminasi "' . $lamination->name . '" untuk kategori "' .
                                        $category->name . '" di lokasi ' . $scopeLabel . ' sudah ada atau duplikat dalam input.'
                                    ]);
                                }
                                $usedKeys[$key] = true;

                                $paperCost = (float) str_replace(',', '.', str_replace('.', '', $item['unit_price']));
                                $clickCost = (float) str_replace(',', '.', str_replace('.', '', $item['click_price']));
                                foreach (['unit_price' => $paperCost, 'click_price' => $clickCost] as $field => $amount) {
                                    if ($amount <= 0 || $amount > $maximumAmount) {
                                        throw ValidationException::withMessages([
                                            $itemPrefix . '.' . $field => 'Harga harus lebih dari 0 dan tidak melebihi 9.999.999.999,99.'
                                        ]);
                                    }
                                }

                                $singleCost = round($paperCost + $clickCost, 2);
                                $doubleCost = round($singleCost * 2, 2);
                                if ($doubleCost > $maximumAmount) {
                                    throw ValidationException::withMessages([
                                        $itemPrefix . '.click_price' => 'Hasil cost print melebihi batas maksimum penyimpanan.'
                                    ]);
                                }
                                $priceRows = [];
                                foreach (['single' => $singleCost, 'double' => $doubleCost] as $printType => $cost) {
                                    foreach ($tiers as $tier) {
                                        $generalRule = $ruleGroups->get('general|' . $printType . '|' . $tier)->first();
                                        $divisionRule = $ruleGroups->get('division|' . $printType . '|' . $tier)->first();
                                        $generalPrice = $roundUpToMultiple(
                                            ceil(round(((float) $generalRule->markup_percentage / 100) * $cost, 6)) + $cost,
                                            $generalRule->rounding_value
                                        );
                                        $divisionPrice = $roundUpToMultiple(
                                            round($generalPrice * ((float) $divisionRule->markup_percentage / 100), 2),
                                            $divisionRule->rounding_value
                                        );
                                        foreach ([[$generalRule, $generalPrice], [$divisionRule, $divisionPrice]] as [$rule, $price]) {
                                            if (!is_finite($price) || $price < 0 || $price > $maximumAmount) {
                                                throw ValidationException::withMessages([
                                                    $itemPrefix . '.unit_price' =>
                                                    'Hasil harga laminasi untuk kategori "' . $category->name . '" melebihi batas penyimpanan atau tidak valid.'
                                                ]);
                                            }
                                            $priceRows[] = [
                                                'digital_lamination_pricing_rule_detail_id' => $rule->id,
                                                'price_type' => $rule->price_type,
                                                'print_type' => $rule->print_type,
                                                'quantity_tier' => $rule->quantity_tier,
                                                'minimum_quantity' => $rule->minimum_quantity,
                                                'maximum_quantity' => $rule->maximum_quantity,
                                                'markup_percentage' => $rule->markup_percentage,
                                                'rounding_value' => $rule->rounding_value,
                                                'selling_price' => $price,
                                                'created_by' => $userName,
                                                'created_at' => now(),
                                            ] + $audit;
                                        }
                                    }
                                }

                                $detailId = DB::table('m_digital_lamination_production_cost_details')->insertGetId([
                                    'digital_lamination_production_cost_id' => $headerId,
                                    'lamination_id' => $laminationId,
                                    'category_id' => $categoryId,
                                    'paper_cost' => $paperCost,
                                    'click_cost' => $clickCost,
                                    'single_print_cost' => $singleCost,
                                    'double_print_cost' => $doubleCost,
                                    'created_by' => $userName,
                                    'created_at' => now(),
                                ] + $audit);
                                foreach ($priceRows as &$priceRow) {
                                    $priceRow['digital_lamination_production_cost_detail_id'] = $detailId;
                                }
                                unset($priceRow);
                                DB::table('m_digital_lamination_production_cost_prices')->insert($priceRows);
                            }
                        }
                    }
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => $editingId === null ?
                    'Production Cost Laminasi Digital Print A3+ berhasil disimpan.' :
                    'Production Cost Laminasi Digital Print A3+ berhasil diperbarui.'
            ]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan Production Cost Laminasi Digital Print A3+.'
            ], 500);
        }
    }

    public function destroyDigital($id)
    {
        $productionCost = $this->findDigitalProductionCost($id);

        try {
            return DB::transaction(function () use ($productionCost) {
                // Urutan lock sama dengan store/update Digital.
                $engine = DB::table('m_engines')
                    ->where('id', $productionCost->engine_id)
                    ->whereRaw('LOWER(TRIM(name)) = ?', ['digital print a3+'])
                    ->lockForUpdate()->first();
                abort_unless($engine, 404);

                $header = DB::table('m_digital_lamination_production_costs')
                    ->where('id', $productionCost->id)
                    ->where('engine_id', $productionCost->engine_id)
                    ->where('location_id', $productionCost->location_id)
                    ->where('vendor_id', $productionCost->vendor_id)
                    ->lockForUpdate()->first();
                abort_unless($header, 404);

                // Cascade hanya menghapus detail dan harga milik header laminasi ini.
                $deleted = DB::table('m_digital_lamination_production_costs')
                    ->where('id', $header->id)->delete();
                if ($deleted !== 1) {
                    abort(response()->json([
                        'success' => false,
                        'message' => 'Production cost laminasi Digital Print A3+ gagal dihapus.'
                    ], 409));
                }

                $scopeLabel = $productionCost->location_name .
                    ($productionCost->vendor_name ? ' - ' . $productionCost->vendor_name : '');

                return response()->json([
                    'success' => true,
                    'message' => 'Production cost laminasi Digital Print A3+ lokasi ' . $scopeLabel . ' berhasil dihapus.'
                ]);
            });
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus production cost laminasi Digital Print A3+.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            /*
        |--------------------------------------------------------------------------
        | CARI PRODUCTION COST LAMINASI BERDASARKAN ENGINE
        |--------------------------------------------------------------------------
        |
        | Hanya ambil production cost yang memiliki
        | detail Production Cost Laminasi.
        |
        | Production Cost Material tidak akan ikut terambil.
        |
        */

            $productionCostIds = DB::table('m_production_costs as pc')
                ->join(
                    'm_lamination_production_cost_details as lpcd',
                    'lpcd.production_cost_id',
                    '=',
                    'pc.id'
                )
                ->where('pc.engine_id', $id)
                ->pluck('pc.id')
                ->unique();

            /*
        |--------------------------------------------------------------------------
        | CEK DATA
        |--------------------------------------------------------------------------
        */

            if ($productionCostIds->isEmpty()) {
                abort(response()->json([
                    'success' => false,
                    'message' => 'Data production cost laminasi tidak ditemukan.'
                ], 404));
            }

            /*
        |--------------------------------------------------------------------------
        | HAPUS PRODUCTION COST LAMINASI
        |--------------------------------------------------------------------------
        |
        | FK cascade akan otomatis menghapus:
        |
        | - m_production_cost_locations
        | - m_production_cost_categories
        | - m_lamination_production_cost_details
        | - m_lamination_production_cost_detail_sizes
        |
        */

            DB::table('m_production_costs')
                ->whereIn('id', $productionCostIds)
                ->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Production cost laminasi berhasil dihapus.'
        ]);
    }

    public function edit($id)
    {
        /*
    |--------------------------------------------------------------------------
    | DECODE ENGINE ID
    |--------------------------------------------------------------------------
    */

        $engineId = base64_decode($id, true);

        if (
            $engineId === false ||
            !ctype_digit($engineId)
        ) {
            abort(404);
        }

        $engineId = (int) $engineId;

        /*
    |--------------------------------------------------------------------------
    | ENGINE
    |--------------------------------------------------------------------------
    */

        $data['engineId'] = $engineId;

        $data['engine'] = DB::table('m_engines')
            ->where('id', $engineId)
            ->first();

        if (!$data['engine']) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | MASTER ENGINE
    |--------------------------------------------------------------------------
    */

        $data['engines'] = DB::table('m_engines')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | MASTER LOCATION
    |--------------------------------------------------------------------------
    */

        $data['locations'] = DB::table('m_locations')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | MASTER CATEGORY
    |--------------------------------------------------------------------------
    */

        $data['categories'] = DB::table('m_categories')
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | MASTER LAMINATION
    |--------------------------------------------------------------------------
    */

        $data['laminations'] = DB::table('m_laminations')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | MASTER VENDOR
    |--------------------------------------------------------------------------
    */

        $data['vendors'] = DB::table('m_vendors')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | PRODUCTION COST LAMINATION
    |--------------------------------------------------------------------------
    |
    | m_production_costs digunakan bersama Material dan Laminasi.
    |
    | Karena halaman ini khusus Laminasi, production cost harus diambil
    | melalui m_lamination_production_cost_details.
    |
    */

        $productionCosts = DB::table('m_production_costs as pc')
            ->join(
                'm_lamination_production_cost_details as lpcd',
                'lpcd.production_cost_id',
                '=',
                'pc.id'
            )
            ->where('pc.engine_id', $engineId)
            ->select('pc.*')
            ->distinct()
            ->orderBy('pc.id')
            ->get();

        if ($productionCosts->isEmpty()) {
            abort(404);
        }

        $productionCostIds = $productionCosts
            ->pluck('id')
            ->unique()
            ->values();

        /*
    |--------------------------------------------------------------------------
    | SAVED LOCATIONS
    |--------------------------------------------------------------------------
    */

        $savedLocations = DB::table(
            'm_production_cost_locations as pcl'
        )
            ->join(
                'm_locations as l',
                'l.id',
                '=',
                'pcl.location_id'
            )
            ->whereIn(
                'pcl.production_cost_id',
                $productionCostIds
            )
            ->select(
                'pcl.production_cost_id',
                'pcl.location_id',
                'l.name as location_name'
            )
            ->orderBy('l.name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | SAVED CATEGORIES
    |--------------------------------------------------------------------------
    */

        $savedCategories = DB::table(
            'm_production_cost_categories as pcc'
        )
            ->join(
                'm_categories as c',
                'c.id',
                '=',
                'pcc.category_id'
            )
            ->whereIn(
                'pcc.production_cost_id',
                $productionCostIds
            )
            ->select(
                'pcc.production_cost_id',
                'pcc.category_id',
                'c.name as category_name'
            )
            ->orderBy('c.name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | LAMINATION DETAILS
    |--------------------------------------------------------------------------
    |
    | LEFT JOIN vendor digunakan karena:
    |
    | - Non-Outsourcing : vendor_id = NULL
    | - Outsourcing     : vendor_id memiliki nilai
    |
    */

        $details = DB::table(
            'm_lamination_production_cost_details as lpcd'
        )
            ->join(
                'm_laminations as l',
                'l.id',
                '=',
                'lpcd.lamination_id'
            )
            ->leftJoin(
                'm_vendors as v',
                'v.id',
                '=',
                'lpcd.vendor_id'
            )
            ->whereIn(
                'lpcd.production_cost_id',
                $productionCostIds
            )
            ->select(
                'lpcd.id',
                'lpcd.production_cost_id',
                'lpcd.lamination_id',
                'l.name as lamination_name',

                /*
            |--------------------------------------------------------------------------
            | VENDOR
            |--------------------------------------------------------------------------
            */

                'lpcd.vendor_id',
                'v.name as vendor_name',
                'lpcd.vendor_price',

                /*
            |--------------------------------------------------------------------------
            | NON-OUTSOURCING COST
            |--------------------------------------------------------------------------
            */

                'lpcd.price_per_meter',
                'lpcd.production_cost',
                'lpcd.finishing_cost',

                /*
            |--------------------------------------------------------------------------
            | CALCULATED PRICE
            |--------------------------------------------------------------------------
            */

                'lpcd.total_cost',
                'lpcd.general_price',
                'lpcd.division_price',
                'lpcd.plain_price',

                /*
            |--------------------------------------------------------------------------
            | PRICING RULE
            |--------------------------------------------------------------------------
            */

                'lpcd.general_pricing_rule_id',
                'lpcd.division_pricing_rule_id',
                'lpcd.plain_pricing_rule_id'
            )
            ->orderBy('l.name')
            ->get();

        if ($details->isEmpty()) {
            abort(404);
        }

        $detailIds = $details
            ->pluck('id')
            ->unique()
            ->values();

        /*
    |--------------------------------------------------------------------------
    | SAVED LAMINATION SIZES
    |--------------------------------------------------------------------------
    */

        $savedSizes = DB::table(
            'm_lamination_production_cost_detail_sizes as lpcds'
        )
            ->join(
                'm_lamination_sizes as ls',
                'ls.id',
                '=',
                'lpcds.lamination_size_id'
            )
            ->whereIn(
                'lpcds.lamination_production_cost_detail_id',
                $detailIds
            )
            ->select(
                'lpcds.lamination_production_cost_detail_id',
                'lpcds.lamination_size_id',
                'ls.lamination_id',
                'ls.width',
                'ls.length',
                'ls.unit'
            )
            ->orderBy('ls.width')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | ALL LAMINATION SIZES
    |--------------------------------------------------------------------------
    */

        $data['laminationSizes'] = DB::table(
            'm_lamination_sizes as ls'
        )
            ->select(
                'ls.id',
                'ls.lamination_id',
                'ls.width',
                'ls.length',
                'ls.unit'
            )
            ->orderBy('ls.width')
            ->get()
            ->groupBy('lamination_id');

        /*
    |--------------------------------------------------------------------------
    | BUILD CONFIGURATIONS
    |--------------------------------------------------------------------------
    |
    | 1 m_production_costs = 1 konfigurasi.
    |
    | Structure:
    | configurations
    | ├── production_cost_id
    | ├── engine_id
    | ├── location_ids[]
    | ├── category_ids[]
    | └── details[]
    |
    */

        $configurations = $productionCosts
            ->map(function ($productionCost) use (
                $savedLocations,
                $savedCategories,
                $details,
                $savedSizes
            ) {
                $productionCostId =
                    $productionCost->id;

                $configurationLocations =
                    $savedLocations
                    ->where(
                        'production_cost_id',
                        $productionCostId
                    )
                    ->values();

                $configurationCategories =
                    $savedCategories
                    ->where(
                        'production_cost_id',
                        $productionCostId
                    )
                    ->values();

                $configurationDetails =
                    $details
                    ->where(
                        'production_cost_id',
                        $productionCostId
                    )
                    ->values();

                /*
            |--------------------------------------------------------------------------
            | GROUP DETAIL
            |--------------------------------------------------------------------------
            |
            | Satu konfigurasi bisa memiliki detail yang sama untuk beberapa
            | kategori. Karena kategori sudah berada di header configuration,
            | detail yang identik perlu digabung kembali saat Edit.
            |
            | Vendor dan vendor_price ikut menjadi pembeda karena konfigurasi
            | Outsourcing dapat memiliki vendor / harga vendor yang berbeda.
            |
            | Pricing rule ID juga tetap menjadi pembeda.
            |
            */

                $groupedDetails =
                    $configurationDetails
                    ->groupBy(function ($detail) {
                        return implode('|', [
                            $detail->lamination_id,

                            /*
                        |--------------------------------------------------------------------------
                        | VENDOR
                        |--------------------------------------------------------------------------
                        */

                            $detail->vendor_id,
                            $detail->vendor_price,

                            /*
                        |--------------------------------------------------------------------------
                        | NON-OUTSOURCING COST
                        |--------------------------------------------------------------------------
                        */

                            $detail->price_per_meter,
                            $detail->production_cost,
                            $detail->finishing_cost,

                            /*
                        |--------------------------------------------------------------------------
                        | CALCULATED PRICE
                        |--------------------------------------------------------------------------
                        */

                            $detail->total_cost,
                            $detail->general_price,
                            $detail->division_price,
                            $detail->plain_price,

                            /*
                        |--------------------------------------------------------------------------
                        | PRICING RULE
                        |--------------------------------------------------------------------------
                        */

                            $detail->general_pricing_rule_id,
                            $detail->division_pricing_rule_id,
                            $detail->plain_pricing_rule_id,
                        ]);
                    })
                    ->map(function ($detailGroup) use (
                        $savedSizes
                    ) {
                        $firstDetail =
                            $detailGroup->first();

                        $detailIds =
                            $detailGroup
                            ->pluck('id')
                            ->unique()
                            ->values();

                        /*
                    |--------------------------------------------------------------------------
                    | MERGE SIZE
                    |--------------------------------------------------------------------------
                    */

                        $sizes =
                            $savedSizes
                            ->whereIn(
                                'lamination_production_cost_detail_id',
                                $detailIds
                            )
                            ->unique(
                                'lamination_size_id'
                            )
                            ->sortBy('width')
                            ->values();

                        return (object) [
                            'id' =>
                            $firstDetail->id,

                            'detail_ids' =>
                            $detailIds,

                            'production_cost_id' =>
                            $firstDetail->production_cost_id,

                            'lamination_id' =>
                            $firstDetail->lamination_id,

                            'lamination_name' =>
                            $firstDetail->lamination_name,

                            /*
                        |--------------------------------------------------------------------------
                        | VENDOR
                        |--------------------------------------------------------------------------
                        */

                            'vendor_id' =>
                            $firstDetail->vendor_id,

                            'vendor_name' =>
                            $firstDetail->vendor_name,

                            'vendor_price' =>
                            $firstDetail->vendor_price,

                            /*
                        |--------------------------------------------------------------------------
                        | NON-OUTSOURCING COST
                        |--------------------------------------------------------------------------
                        */

                            'price_per_meter' =>
                            $firstDetail->price_per_meter,

                            'production_cost' =>
                            $firstDetail->production_cost,

                            'finishing_cost' =>
                            $firstDetail->finishing_cost,

                            /*
                        |--------------------------------------------------------------------------
                        | CALCULATED PRICE
                        |--------------------------------------------------------------------------
                        */

                            'total_cost' =>
                            $firstDetail->total_cost,

                            'general_price' =>
                            $firstDetail->general_price,

                            'division_price' =>
                            $firstDetail->division_price,

                            'plain_price' =>
                            $firstDetail->plain_price,

                            /*
                        |--------------------------------------------------------------------------
                        | PRICING RULE
                        |--------------------------------------------------------------------------
                        */

                            'general_pricing_rule_id' =>
                            $firstDetail->general_pricing_rule_id,

                            'division_pricing_rule_id' =>
                            $firstDetail->division_pricing_rule_id,

                            'plain_pricing_rule_id' =>
                            $firstDetail->plain_pricing_rule_id,

                            /*
                        |--------------------------------------------------------------------------
                        | SIZES
                        |--------------------------------------------------------------------------
                        */

                            'sizes' =>
                            $sizes,
                        ];
                    })
                    ->values();

                return (object) [
                    'production_cost_id' =>
                    $productionCostId,

                    'engine_id' =>
                    $productionCost->engine_id,

                    /*
                |--------------------------------------------------------------------------
                | LOCATIONS
                |--------------------------------------------------------------------------
                */

                    'locations' =>
                    $configurationLocations,

                    'location_ids' =>
                    $configurationLocations
                        ->pluck('location_id')
                        ->unique()
                        ->values(),

                    /*
                |--------------------------------------------------------------------------
                | CATEGORIES
                |--------------------------------------------------------------------------
                */

                    'categories' =>
                    $configurationCategories,

                    'category_ids' =>
                    $configurationCategories
                        ->pluck('category_id')
                        ->unique()
                        ->values(),

                    /*
                |--------------------------------------------------------------------------
                | DETAILS
                |--------------------------------------------------------------------------
                */

                    'details' =>
                    $groupedDetails,
                ];
            })
            ->values();

        /*
    |--------------------------------------------------------------------------
    | DATA UNTUK VIEW
    |--------------------------------------------------------------------------
    */

        $data['configurations'] =
            $configurations;

        $data['savedLocations'] =
            $savedLocations;

        $data['savedCategories'] =
            $savedCategories;

        $data['savedSizes'] =
            $savedSizes;

        $data['productionCosts'] =
            $productionCosts;

        $data['details'] =
            $details;

        /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */

        return view(
            'admin.production-cost-finishing-lamination.edit'
        )->with($data);
    }

    public function update(Request $request, $id)
    {
        /*
    |--------------------------------------------------------------------------
    | Decode Engine ID dari URL
    |--------------------------------------------------------------------------
    */
        $engineId = base64_decode($id, true);

        if (
            $engineId === false ||
            !ctype_digit($engineId)
        ) {
            abort(404);
        }

        $engineId = (int) $engineId;

        /*
    |--------------------------------------------------------------------------
    | Validation dasar
    |--------------------------------------------------------------------------
    */
        $request->validate([
            'configurations' =>
            'required|array|min:1',

            'configurations.*.production_cost_id' =>
            'required|integer|distinct',

            'configurations.*.engine_id' =>
            'required|integer|exists:m_engines,id',

            'configurations.*.location_ids' =>
            'required|array|size:1',

            'configurations.*.location_ids.*' =>
            'required|integer|exists:m_locations,id',

            'configurations.*.category_ids' =>
            'required|array|min:1',

            'configurations.*.category_ids.*' =>
            'required|integer|exists:m_categories,id',

            'configurations.*.vendor_id' =>
            'nullable|integer|exists:m_vendors,id',

            'configurations.*.items' =>
            'required|array|min:1',

            'configurations.*.items.*.lamination_id' =>
            'required|integer|exists:m_laminations,id',

            'configurations.*.items.*.size_ids' =>
            'required|array|min:1',

            'configurations.*.items.*.size_ids.*' =>
            'required|integer|distinct|exists:m_lamination_sizes,id',

            'configurations.*.items.*.price_per_meter' =>
            'nullable',

            'configurations.*.items.*.production_cost' =>
            'nullable',

            'configurations.*.items.*.finishing_cost' =>
            'nullable',

            'configurations.*.items.*.vendor_price' =>
            'nullable',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Helper parse number
    |--------------------------------------------------------------------------
    */
        $parseNumber = function ($value) {
            if ($value === null || $value === '') {
                return 0;
            }

            $value = trim((string) $value);

            if (strpos($value, ',') !== false) {
                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {
                $value = str_replace('.', '', $value);
            }

            return (float) $value;
        };

        /*
    |--------------------------------------------------------------------------
    | Helper pembulatan
    |--------------------------------------------------------------------------
    */
        $roundUp = function ($value, $rounding) {
            if (
                $rounding === null ||
                $rounding <= 0
            ) {
                return $value;
            }

            return ceil(
                $value / $rounding
            ) * $rounding;
        };

        DB::beginTransaction();

        try {

            /*
        |--------------------------------------------------------------------------
        | Ambil Production Cost Laminasi yang sedang diedit
        |--------------------------------------------------------------------------
        |
        | HANYA production_cost yang memiliki
        | m_lamination_production_cost_details.
        |
        | Production Cost Material tidak akan masuk.
        |
        */
            $existingProductionCosts = DB::table(
                'm_production_costs as pc'
            )
                ->join(
                    'm_lamination_production_cost_details as lpcd',
                    'lpcd.production_cost_id',
                    '=',
                    'pc.id'
                )
                ->where(
                    'pc.engine_id',
                    $engineId
                )
                ->select('pc.id')
                ->distinct()
                ->orderBy('pc.id')
                ->get();

            if ($existingProductionCosts->isEmpty()) {
                abort(404);
            }

            $existingProductionCostIds =
                $existingProductionCosts
                ->pluck('id')
                ->map(function ($value) {
                    return (int) $value;
                })
                ->sort()
                ->values();

            /*
        |--------------------------------------------------------------------------
        | Ambil Production Cost ID dari request
        |--------------------------------------------------------------------------
        */
            $requestProductionCostIds =
                collect(
                    $request->input('configurations')
                )
                ->pluck('production_cost_id')
                ->map(function ($value) {
                    return (int) $value;
                })
                ->sort()
                ->values();

            /*
        |--------------------------------------------------------------------------
        | Pastikan konfigurasi yang diedit sama
        |--------------------------------------------------------------------------
        */
            if (
                $existingProductionCostIds->count() !==
                $requestProductionCostIds->count()
                ||
                $existingProductionCostIds->toArray() !==
                $requestProductionCostIds->toArray()
            ) {
                throw ValidationException::withMessages([
                    'configurations' =>
                    'Konfigurasi Production Cost yang diperbarui tidak sesuai dengan data yang tersedia.'
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | Ambil Engine
        |--------------------------------------------------------------------------
        */
            $engineIds = collect(
                $request->input('configurations')
            )
                ->pluck('engine_id')
                ->map(function ($value) {
                    return (int) $value;
                })
                ->unique()
                ->values();

            $engines = DB::table('m_engines')
                ->whereIn('id', $engineIds)
                ->get()
                ->keyBy('id');

            /*
        |--------------------------------------------------------------------------
        | Validasi Engine
        |--------------------------------------------------------------------------
        */
            foreach ($engineIds as $requestEngineId) {

                $engine = $engines->get($requestEngineId);

                if (!$engine) {
                    throw ValidationException::withMessages([
                        'configurations' =>
                        'Mesin tidak ditemukan.'
                    ]);
                }

                /*
            |--------------------------------------------------------------------------
            | Engine dari request harus sama dengan Engine pada URL
            |--------------------------------------------------------------------------
            */
                if ($requestEngineId !== $engineId) {
                    throw ValidationException::withMessages([
                        'configurations' =>
                        'Engine pada konfigurasi tidak sesuai dengan Production Cost yang sedang diedit.'
                    ]);
                }

                $engineName = strtolower(
                    trim($engine->name)
                );

                if (
                    $engineName ===
                    'digital print a3+'
                ) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'type' => 'warning',
                        'message' =>
                        'Mesin Digital Print A3+ belum mempunyai master calculation rules.'
                    ], 422);
                }

                if (
                    $engineName !==
                    'large format'
                ) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'type' => 'warning',
                        'message' =>
                        'Mesin "' .
                            $engine->name .
                            '" belum mempunyai master calculation rules.'
                    ], 422);
                }
            }

            /*
        |--------------------------------------------------------------------------
        | Ambil master
        |--------------------------------------------------------------------------
        */
            $locations = DB::table('m_locations')
                ->get()
                ->keyBy('id');

            $categories = DB::table('m_categories')
                ->get()
                ->keyBy('id');

            $laminations = DB::table('m_laminations')
                ->where('status', 'Active')
                ->get()
                ->keyBy('id');

            $laminationSizes = DB::table(
                'm_lamination_sizes'
            )
                ->get()
                ->keyBy('id');

            $vendors = DB::table('m_vendors')
                ->where('status', 'Active')
                ->get()
                ->keyBy('id');

            /*
        |--------------------------------------------------------------------------
        | Ambil Pricing Rule aktif
        |--------------------------------------------------------------------------
        |
        | Key:
        | engine | location | vendor | category | price_type
        |
        */
            $pricingRules = DB::table(
                'm_lamination_pricing_rules'
            )
                ->where('status', 'Active')
                ->get()
                ->keyBy(function ($rule) {

                    return $rule->engine_id
                        . '|'
                        . $rule->location_id
                        . '|'
                        . ($rule->vendor_id ?? 'null')
                        . '|'
                        . $rule->category_id
                        . '|'
                        . $rule->price_type;
                });

            /*
        |--------------------------------------------------------------------------
        | Normalisasi data request
        |--------------------------------------------------------------------------
        */
            $normalizedRows = [];

            foreach (
                $request->input('configurations')
                as $configurationIndex => $configuration
            ) {

                $productionCostId =
                    (int) $configuration['production_cost_id'];

                $configurationEngineId =
                    (int) $configuration['engine_id'];

                $locationIds =
                    array_map(
                        'intval',
                        $configuration['location_ids']
                    );

                $categoryIds =
                    array_map(
                        'intval',
                        $configuration['category_ids']
                    );

                /*
            |--------------------------------------------------------------------------
            | Production Cost ID harus merupakan PC Laminasi
            |--------------------------------------------------------------------------
            */
                if (
                    !$existingProductionCostIds
                        ->contains($productionCostId)
                ) {
                    throw ValidationException::withMessages([
                        'configurations.' .
                            $configurationIndex .
                            '.production_cost_id' =>
                        'Production Cost tidak ditemukan atau bukan Production Cost Laminasi.'
                    ]);
                }

                /*
            |--------------------------------------------------------------------------
            | Engine
            |--------------------------------------------------------------------------
            */
                $engine =
                    $engines->get(
                        $configurationEngineId
                    );

                if (!$engine) {
                    throw ValidationException::withMessages([
                        'configurations.' .
                            $configurationIndex .
                            '.engine_id' =>
                        'Mesin tidak ditemukan.'
                    ]);
                }

                if (
                    $configurationEngineId !==
                    $engineId
                ) {
                    throw ValidationException::withMessages([
                        'configurations.' .
                            $configurationIndex .
                            '.engine_id' =>
                        'Mesin tidak sesuai dengan Production Cost yang sedang diedit.'
                    ]);
                }

                $engineName = strtolower(
                    trim($engine->name)
                );

                if (
                    $engineName !==
                    'large format'
                ) {
                    throw ValidationException::withMessages([
                        'configurations.' .
                            $configurationIndex .
                            '.engine_id' =>
                        'Mesin "' .
                            $engine->name .
                            '" belum mempunyai master calculation rules.'
                    ]);
                }

                /*
            |--------------------------------------------------------------------------
            | Location
            |--------------------------------------------------------------------------
            |
            | Edit tetap menggunakan satu lokasi per Production Cost.
            |
            */
                if (
                    count($locationIds) !== 1
                ) {
                    throw ValidationException::withMessages([
                        'configurations.' .
                            $configurationIndex .
                            '.location_ids' =>
                        'Satu konfigurasi Production Cost hanya dapat memiliki satu lokasi.'
                    ]);
                }

                $locationId = $locationIds[0];

                if (
                    !$locations->has($locationId)
                ) {
                    throw ValidationException::withMessages([
                        'configurations.' .
                            $configurationIndex .
                            '.location_ids' =>
                        'Lokasi tidak ditemukan.'
                    ]);
                }

                $location =
                    $locations->get($locationId);

                if (
                    strtolower(
                        trim($location->status)
                    ) !== 'active'
                ) {
                    throw ValidationException::withMessages([
                        'configurations.' .
                            $configurationIndex .
                            '.location_ids' =>
                        'Lokasi "' .
                            $location->name .
                            '" tidak aktif.'
                    ]);
                }

                $isOutsourcing =
                    strtolower(
                        trim($location->name)
                    ) === 'outsourcing';

                /*
            |--------------------------------------------------------------------------
            | Vendor
            |--------------------------------------------------------------------------
            */
                $vendorId =
                    isset($configuration['vendor_id']) &&
                    $configuration['vendor_id'] !== ''
                    ? (int) $configuration['vendor_id']
                    : null;

                if ($isOutsourcing) {

                    if ($vendorId === null) {
                        throw ValidationException::withMessages([
                            'configurations.' .
                                $configurationIndex .
                                '.vendor_id' =>
                            'Vendor wajib dipilih untuk lokasi Outsourcing.'
                        ]);
                    }

                    if (
                        !$vendors->has($vendorId)
                    ) {
                        throw ValidationException::withMessages([
                            'configurations.' .
                                $configurationIndex .
                                '.vendor_id' =>
                            'Vendor tidak ditemukan atau tidak aktif.'
                        ]);
                    }
                } else {

                    if ($vendorId !== null) {
                        throw ValidationException::withMessages([
                            'configurations.' .
                                $configurationIndex .
                                '.vendor_id' =>
                            'Vendor hanya dapat digunakan untuk lokasi Outsourcing.'
                        ]);
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | Category
            |--------------------------------------------------------------------------
            */
                foreach ($categoryIds as $categoryId) {

                    if (
                        !$categories->has($categoryId)
                    ) {
                        throw ValidationException::withMessages([
                            'configurations.' .
                                $configurationIndex .
                                '.category_ids' =>
                            'Kategori tidak ditemukan.'
                        ]);
                    }
                }

                /*
            |--------------------------------------------------------------------------
            | Items
            |--------------------------------------------------------------------------
            */
                foreach (
                    $configuration['items']
                    as $itemIndex => $item
                ) {

                    $laminationId =
                        (int) $item['lamination_id'];

                    $sizeIds =
                        array_map(
                            'intval',
                            $item['size_ids']
                        );

                    /*
                |--------------------------------------------------------------------------
                | Laminasi
                |--------------------------------------------------------------------------
                */
                    $lamination =
                        $laminations->get(
                            $laminationId
                        );

                    if (!$lamination) {
                        throw ValidationException::withMessages([
                            'configurations.' .
                                $configurationIndex .
                                '.items.' .
                                $itemIndex .
                                '.lamination_id' =>
                            'Laminasi tidak ditemukan atau tidak aktif.'
                        ]);
                    }

                    /*
                |--------------------------------------------------------------------------
                | Ukuran Laminasi
                |--------------------------------------------------------------------------
                */
                    foreach ($sizeIds as $sizeId) {

                        $size =
                            $laminationSizes->get(
                                $sizeId
                            );

                        if (!$size) {
                            throw ValidationException::withMessages([
                                'configurations.' .
                                    $configurationIndex .
                                    '.items.' .
                                    $itemIndex .
                                    '.size_ids' =>
                                'Ukuran Laminasi tidak ditemukan.'
                            ]);
                        }

                        if (
                            (int) $size->lamination_id !==
                            $laminationId
                        ) {
                            throw ValidationException::withMessages([
                                'configurations.' .
                                    $configurationIndex .
                                    '.items.' .
                                    $itemIndex .
                                    '.size_ids' =>
                                'Ukuran Laminasi tidak sesuai dengan Laminasi yang dipilih.'
                            ]);
                        }
                    }

                    /*
                |--------------------------------------------------------------------------
                | Parse biaya
                |--------------------------------------------------------------------------
                */
                    $pricePerMeter =
                        $parseNumber(
                            $item['price_per_meter'] ?? null
                        );

                    $productionCost =
                        $parseNumber(
                            $item['production_cost'] ?? null
                        );

                    $finishingCost =
                        $parseNumber(
                            $item['finishing_cost'] ?? null
                        );

                    $vendorPrice =
                        $parseNumber(
                            $item['vendor_price'] ?? null
                        );

                    /*
                |--------------------------------------------------------------------------
                | Validasi biaya berdasarkan jenis lokasi
                |--------------------------------------------------------------------------
                */
                    if ($isOutsourcing) {

                        if (
                            !isset($item['vendor_price']) ||
                            $item['vendor_price'] === ''
                        ) {
                            throw ValidationException::withMessages([
                                'configurations.' .
                                    $configurationIndex .
                                    '.items.' .
                                    $itemIndex .
                                    '.vendor_price' =>
                                'Harga Vendor wajib diisi untuk lokasi Outsourcing.'
                            ]);
                        }

                        if ($vendorPrice < 0) {
                            throw ValidationException::withMessages([
                                'configurations.' .
                                    $configurationIndex .
                                    '.items.' .
                                    $itemIndex .
                                    '.vendor_price' =>
                                'Harga Vendor tidak boleh negatif.'
                            ]);
                        }

                        /*
                    |--------------------------------------------------------------------------
                    | Outsourcing tidak menggunakan biaya internal
                    |--------------------------------------------------------------------------
                    */
                        $pricePerMeter = 0;
                        $productionCost = 0;
                        $finishingCost = 0;

                        $totalCost = $vendorPrice;
                    } else {

                        if (
                            !isset($item['price_per_meter']) ||
                            $item['price_per_meter'] === '' ||
                            !isset($item['production_cost']) ||
                            $item['production_cost'] === '' ||
                            !isset($item['finishing_cost']) ||
                            $item['finishing_cost'] === ''
                        ) {
                            throw ValidationException::withMessages([
                                'configurations.' .
                                    $configurationIndex .
                                    '.items.' .
                                    $itemIndex =>
                                'Price per Meter, Production Cost, dan Finishing Cost wajib diisi.'
                            ]);
                        }

                        if (
                            $pricePerMeter < 0 ||
                            $productionCost < 0 ||
                            $finishingCost < 0
                        ) {
                            throw ValidationException::withMessages([
                                'configurations.' .
                                    $configurationIndex .
                                    '.items.' .
                                    $itemIndex =>
                                'Nilai biaya tidak boleh negatif.'
                            ]);
                        }

                        /*
                    |--------------------------------------------------------------------------
                    | Non-Outsourcing Total Cost
                    |--------------------------------------------------------------------------
                    */
                        $totalCost =
                            ($pricePerMeter * 1.2)
                            + $productionCost
                            + $finishingCost;

                        $vendorPrice = 0;
                    }

                    /*
                |--------------------------------------------------------------------------
                | Location + Category
                |--------------------------------------------------------------------------
                */
                    foreach ($categoryIds as $categoryId) {

                        /*
                    |--------------------------------------------------------------------------
                    | Pricing Rule Key
                    |--------------------------------------------------------------------------
                    */
                        $vendorKey =
                            $vendorId !== null
                            ? $vendorId
                            : 'null';

                        $generalKey =
                            $configurationEngineId .
                            '|' .
                            $locationId .
                            '|' .
                            $vendorKey .
                            '|' .
                            $categoryId .
                            '|general';

                        $divisionKey =
                            $configurationEngineId .
                            '|' .
                            $locationId .
                            '|' .
                            $vendorKey .
                            '|' .
                            $categoryId .
                            '|division';

                        $plainKey =
                            $configurationEngineId .
                            '|' .
                            $locationId .
                            '|' .
                            $vendorKey .
                            '|' .
                            $categoryId .
                            '|plain';

                        $generalRule =
                            $pricingRules->get(
                                $generalKey
                            );

                        $divisionRule =
                            $pricingRules->get(
                                $divisionKey
                            );

                        $plainRule =
                            $pricingRules->get(
                                $plainKey
                            );

                        /*
                    |--------------------------------------------------------------------------
                    | General + Division wajib
                    |--------------------------------------------------------------------------
                    */
                        if (
                            !$generalRule ||
                            !$divisionRule
                        ) {
                            throw ValidationException::withMessages([
                                'configurations.' .
                                    $configurationIndex .
                                    '.items.' .
                                    $itemIndex =>
                                'Pricing Rule General dan Division untuk lokasi, vendor, dan kategori belum lengkap.'
                            ]);
                        }

                        /*
                    |--------------------------------------------------------------------------
                    | Markup
                    |--------------------------------------------------------------------------
                    */
                        $generalMarkup =
                            ((float)
                            $generalRule->markup_percentage
                            ) / 100;

                        $divisionMarkup =
                            ((float)
                            $divisionRule->markup_percentage
                            ) / 100;

                        $generalRounding =
                            $generalRule->rounding_value !== null
                            ? (float)
                            $generalRule->rounding_value
                            : null;

                        $divisionRounding =
                            $divisionRule->rounding_value !== null
                            ? (float)
                            $divisionRule->rounding_value
                            : null;

                        /*
                    |--------------------------------------------------------------------------
                    | General Price
                    |--------------------------------------------------------------------------
                    */
                        $generalPrice =
                            $totalCost *
                            (1 + $generalMarkup);

                        $generalPrice =
                            $roundUp(
                                $generalPrice,
                                $generalRounding
                            );

                        /*
                    |--------------------------------------------------------------------------
                    | Division Price
                    |--------------------------------------------------------------------------
                    */
                        $divisionPrice =
                            $generalPrice *
                            $divisionMarkup;

                        $divisionPrice =
                            $roundUp(
                                $divisionPrice,
                                $divisionRounding
                            );

                        /*
                    |--------------------------------------------------------------------------
                    | Plain Price
                    |--------------------------------------------------------------------------
                    */
                        $plainPrice = 0;

                        if (
                            !$isOutsourcing &&
                            $plainRule
                        ) {

                            $plainMarkup =
                                ((float)
                                $plainRule->markup_percentage
                                ) / 100;

                            $plainRounding =
                                $plainRule->rounding_value !== null
                                ? (float)
                                $plainRule->rounding_value
                                : null;

                            $plainPrice =
                                $pricePerMeter *
                                (1 + $plainMarkup);

                            $plainPrice =
                                $roundUp(
                                    $plainPrice,
                                    $plainRounding
                                );
                        }

                        /*
                    |--------------------------------------------------------------------------
                    | Simpan normalized rows
                    |--------------------------------------------------------------------------
                    */
                        $normalizedRows[] = [

                            'production_cost_id' =>
                            $productionCostId,

                            'configuration_index' =>
                            $configurationIndex,

                            'engine_id' =>
                            $configurationEngineId,

                            'location_id' =>
                            $locationId,

                            'category_id' =>
                            $categoryId,

                            'vendor_id' =>
                            $vendorId,

                            'lamination_id' =>
                            $laminationId,

                            'general_pricing_rule_id' =>
                            $generalRule->id,

                            'division_pricing_rule_id' =>
                            $divisionRule->id,

                            'plain_pricing_rule_id' =>
                            $plainRule
                                ? $plainRule->id
                                : null,

                            'size_ids' =>
                            $sizeIds,

                            'vendor_price' =>
                            $vendorPrice,

                            'price_per_meter' =>
                            $pricePerMeter,

                            'production_cost' =>
                            $productionCost,

                            'finishing_cost' =>
                            $finishingCost,

                            'total_cost' =>
                            $totalCost,

                            'general_price' =>
                            $generalPrice,

                            'division_price' =>
                            $divisionPrice,

                            'plain_price' =>
                            $plainPrice,
                        ];
                    }
                }
            }

            /*
        |--------------------------------------------------------------------------
        | Duplicate check terhadap Production Cost lain
        |--------------------------------------------------------------------------
        |
        | Production Cost Laminasi yang sedang diedit dikecualikan.
        | Production Cost Material juga otomatis tidak ikut karena
        | query menggunakan m_lamination_production_cost_details.
        |
        */
            foreach ($normalizedRows as $row) {

                foreach ($row['size_ids'] as $sizeId) {

                    $duplicateQuery = DB::table(
                        'm_production_costs as pc'
                    )
                        ->join(
                            'm_production_cost_locations as pcl',
                            'pc.id',
                            '=',
                            'pcl.production_cost_id'
                        )
                        ->join(
                            'm_production_cost_categories as pcc',
                            'pc.id',
                            '=',
                            'pcc.production_cost_id'
                        )
                        ->join(
                            'm_lamination_production_cost_details as lpcd',
                            'pc.id',
                            '=',
                            'lpcd.production_cost_id'
                        )
                        ->join(
                            'm_lamination_production_cost_detail_sizes as lpcds',
                            'lpcd.id',
                            '=',
                            'lpcds.lamination_production_cost_detail_id'
                        )
                        ->where(
                            'pc.engine_id',
                            $row['engine_id']
                        )
                        ->where(
                            'pcl.location_id',
                            $row['location_id']
                        )
                        ->where(
                            'pcc.category_id',
                            $row['category_id']
                        )
                        ->where(
                            'lpcd.lamination_id',
                            $row['lamination_id']
                        )
                        ->where(
                            'lpcds.lamination_size_id',
                            $sizeId
                        )
                        ->whereNotIn(
                            'pc.id',
                            $existingProductionCostIds
                        );

                    if ($row['vendor_id'] !== null) {

                        $duplicateQuery
                            ->where(
                                'lpcd.vendor_id',
                                $row['vendor_id']
                            )
                            ->where(
                                'lpcd.vendor_price',
                                $row['vendor_price']
                            );
                    } else {

                        $duplicateQuery
                            ->whereNull(
                                'lpcd.vendor_id'
                            )
                            ->where(
                                'lpcd.price_per_meter',
                                $row['price_per_meter']
                            )
                            ->where(
                                'lpcd.production_cost',
                                $row['production_cost']
                            )
                            ->where(
                                'lpcd.finishing_cost',
                                $row['finishing_cost']
                            );
                    }

                    if ($duplicateQuery->exists()) {

                        throw ValidationException::withMessages([
                            'configurations' =>
                            'Data Production Cost dengan Engine, Lokasi, Kategori, Laminasi, Ukuran, Vendor, dan biaya yang sama sudah tersedia.'
                        ]);
                    }
                }
            }

            /*
        |--------------------------------------------------------------------------
        | Duplicate check di dalam request yang sama
        |--------------------------------------------------------------------------
        */
            $incomingDuplicateKeys = [];

            foreach ($normalizedRows as $row) {

                foreach ($row['size_ids'] as $sizeId) {

                    $duplicateKey =
                        $row['engine_id'] .
                        '|' .
                        $row['location_id'] .
                        '|' .
                        $row['category_id'] .
                        '|' .
                        ($row['vendor_id'] ?? 'null') .
                        '|' .
                        $row['lamination_id'] .
                        '|' .
                        $sizeId;

                    if ($row['vendor_id'] !== null) {

                        $duplicateKey .=
                            '|vendor|' .
                            $row['vendor_price'];
                    } else {

                        $duplicateKey .=
                            '|internal|' .
                            $row['price_per_meter'] .
                            '|' .
                            $row['production_cost'] .
                            '|' .
                            $row['finishing_cost'];
                    }

                    if (
                        isset(
                            $incomingDuplicateKeys[$duplicateKey]
                        )
                    ) {
                        throw ValidationException::withMessages([
                            'configurations' =>
                            'Terdapat data Production Cost yang sama di dalam konfigurasi yang diperbarui.'
                        ]);
                    }

                    $incomingDuplicateKeys[$duplicateKey] = true;
                }
            }

            /*
        |--------------------------------------------------------------------------
        | Group berdasarkan Production Cost
        |--------------------------------------------------------------------------
        */
            $groupedRows =
                collect($normalizedRows)
                ->groupBy(
                    'production_cost_id'
                );

            /*
        |--------------------------------------------------------------------------
        | Update setiap Production Cost Laminasi
        |--------------------------------------------------------------------------
        */
            foreach (
                $groupedRows
                as $productionCostId => $group
            ) {

                $firstRow =
                    $group->first();

                /*
            |--------------------------------------------------------------------------
            | Pastikan Production Cost masih merupakan PC Laminasi
            |--------------------------------------------------------------------------
            */
                $productionCostExists =
                    DB::table(
                        'm_production_costs as pc'
                    )
                    ->join(
                        'm_lamination_production_cost_details as lpcd',
                        'lpcd.production_cost_id',
                        '=',
                        'pc.id'
                    )
                    ->where(
                        'pc.id',
                        $productionCostId
                    )
                    ->where(
                        'pc.engine_id',
                        $engineId
                    )
                    ->exists();

                if (!$productionCostExists) {
                    throw ValidationException::withMessages([
                        'configurations' =>
                        'Production Cost Laminasi tidak ditemukan.'
                    ]);
                }

                /*
            |--------------------------------------------------------------------------
            | Update Header Production Cost
            |--------------------------------------------------------------------------
            */
                DB::table(
                    'm_production_costs'
                )
                    ->where(
                        'id',
                        $productionCostId
                    )
                    ->update([
                        'engine_id' =>
                        $firstRow['engine_id'],

                        'updated_by' =>
                        Auth::user()->name,

                        'updated_at' =>
                        Carbon::now(),
                    ]);

                /*
            |--------------------------------------------------------------------------
            | Replace Location
            |--------------------------------------------------------------------------
            */
                DB::table(
                    'm_production_cost_locations'
                )
                    ->where(
                        'production_cost_id',
                        $productionCostId
                    )
                    ->delete();

                DB::table(
                    'm_production_cost_locations'
                )
                    ->insert([
                        'production_cost_id' =>
                        $productionCostId,

                        'location_id' =>
                        $firstRow['location_id'],

                        'created_by' =>
                        Auth::user()->name,

                        'created_at' =>
                        Carbon::now(),

                        'updated_by' =>
                        Auth::user()->name,

                        'updated_at' =>
                        Carbon::now(),
                    ]);

                /*
            |--------------------------------------------------------------------------
            | Replace Category
            |--------------------------------------------------------------------------
            */
                DB::table(
                    'm_production_cost_categories'
                )
                    ->where(
                        'production_cost_id',
                        $productionCostId
                    )
                    ->delete();

                $categoryIds =
                    $group
                    ->pluck('category_id')
                    ->unique()
                    ->values();

                foreach ($categoryIds as $categoryId) {

                    DB::table(
                        'm_production_cost_categories'
                    )
                        ->insert([
                            'production_cost_id' =>
                            $productionCostId,

                            'category_id' =>
                            $categoryId,

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

                /*
            |--------------------------------------------------------------------------
            | Hapus Detail Laminasi Lama
            |--------------------------------------------------------------------------
            */
                $oldDetailIds =
                    DB::table(
                        'm_lamination_production_cost_details'
                    )
                    ->where(
                        'production_cost_id',
                        $productionCostId
                    )
                    ->pluck('id');

                if (
                    $oldDetailIds->isNotEmpty()
                ) {

                    DB::table(
                        'm_lamination_production_cost_detail_sizes'
                    )
                        ->whereIn(
                            'lamination_production_cost_detail_id',
                            $oldDetailIds
                        )
                        ->delete();

                    DB::table(
                        'm_lamination_production_cost_details'
                    )
                        ->where(
                            'production_cost_id',
                            $productionCostId
                        )
                        ->delete();
                }

                /*
            |--------------------------------------------------------------------------
            | Group Detail
            |--------------------------------------------------------------------------
            |
            | Category berada pada header Production Cost.
            |
            | Detail dibedakan berdasarkan konfigurasi biaya + pricing rule.
            | Ini penting karena satu Production Cost dapat memiliki
            | beberapa category dengan pricing rule berbeda.
            |
            */
                $laminationGroups =
                    $group->groupBy(function ($row) {

                        return implode('|', [
                            $row['lamination_id'],
                            $row['vendor_id'] ?? 'null',
                            $row['vendor_price'],
                            $row['price_per_meter'],
                            $row['production_cost'],
                            $row['finishing_cost'],
                            $row['total_cost'],
                            $row['general_price'],
                            $row['division_price'],
                            $row['plain_price'],
                            $row['general_pricing_rule_id'],
                            $row['division_pricing_rule_id'],
                            $row['plain_pricing_rule_id'],
                        ]);
                    });

                foreach (
                    $laminationGroups
                    as $laminationRows
                ) {

                    $laminationRow =
                        $laminationRows->first();

                    /*
                |--------------------------------------------------------------------------
                | Insert Detail
                |--------------------------------------------------------------------------
                */
                    $detailId =
                        DB::table(
                            'm_lamination_production_cost_details'
                        )
                        ->insertGetId([
                            'production_cost_id' =>
                            $productionCostId,

                            'lamination_id' =>
                            $laminationRow['lamination_id'],

                            'vendor_id' =>
                            $laminationRow['vendor_id'],

                            'vendor_price' =>
                            $laminationRow['vendor_price'],

                            'general_pricing_rule_id' =>
                            $laminationRow['general_pricing_rule_id'],

                            'division_pricing_rule_id' =>
                            $laminationRow['division_pricing_rule_id'],

                            'plain_pricing_rule_id' =>
                            $laminationRow['plain_pricing_rule_id'],

                            'production_cost' =>
                            $laminationRow['production_cost'],

                            'finishing_cost' =>
                            $laminationRow['finishing_cost'],

                            'total_cost' =>
                            $laminationRow['total_cost'],

                            'general_price' =>
                            $laminationRow['general_price'],

                            'division_price' =>
                            $laminationRow['division_price'],

                            'plain_price' =>
                            $laminationRow['plain_price'],

                            'price_per_meter' =>
                            $laminationRow['price_per_meter'],

                            'created_by' =>
                            Auth::user()->name,

                            'created_at' =>
                            Carbon::now(),

                            'updated_by' =>
                            Auth::user()->name,

                            'updated_at' =>
                            Carbon::now(),
                        ]);

                    /*
                |--------------------------------------------------------------------------
                | Gabungkan semua Size
                |--------------------------------------------------------------------------
                */
                    $sizeIds =
                        $laminationRows
                        ->flatMap(function ($row) {
                            return $row['size_ids'];
                        })
                        ->unique()
                        ->values();

                    foreach ($sizeIds as $sizeId) {

                        DB::table(
                            'm_lamination_production_cost_detail_sizes'
                        )
                            ->insert([
                                'lamination_production_cost_detail_id' =>
                                $detailId,

                                'lamination_size_id' =>
                                $sizeId,

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
            }

            /*
        |--------------------------------------------------------------------------
        | Commit
        |--------------------------------------------------------------------------
        */
            DB::commit();

            return response()->json([
                'success' => true,
                'message' =>
                'Production Cost Laminasi berhasil diperbarui.'
            ]);
        } catch (ValidationException $e) {

            DB::rollBack();

            throw $e;
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' =>
                $e->getMessage()
            ], 500);
        }
    }

    public function details($id)
    {
        /*
    |--------------------------------------------------------------------------
    | DECODE ENGINE ID
    |--------------------------------------------------------------------------
    */
        $data['engineId'] = base64_decode($id, true);

        if (
            $data['engineId'] === false ||
            !ctype_digit($data['engineId'])
        ) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | ENGINE
    |--------------------------------------------------------------------------
    */
        $data['engine'] = DB::table('m_engines')
            ->where('id', $data['engineId'])
            ->first();

        if (!$data['engine']) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | PRODUCTION COST
    |--------------------------------------------------------------------------
    |
    | Hanya ambil production cost yang memiliki
    | detail Laminasi.
    |
    */
        $data['productionCosts'] = DB::table(
            'm_production_costs as pc'
        )
            ->join(
                'm_lamination_production_cost_details as lpcd',
                'lpcd.production_cost_id',
                '=',
                'pc.id'
            )
            ->where(
                'pc.engine_id',
                $data['engineId']
            )
            ->select('pc.*')
            ->distinct()
            ->get();

        if ($data['productionCosts']->isEmpty()) {
            abort(404);
        }

        $data['productionCostIds'] = $data['productionCosts']
            ->pluck('id')
            ->unique()
            ->values()
            ->toArray();

        /*
    |--------------------------------------------------------------------------
    | LOCATION
    |--------------------------------------------------------------------------
    */
        $data['locations'] = DB::table(
            'm_production_cost_locations as pcl'
        )
            ->join(
                'm_locations as l',
                'l.id',
                '=',
                'pcl.location_id'
            )
            ->whereIn(
                'pcl.production_cost_id',
                $data['productionCostIds']
            )
            ->select(
                'pcl.production_cost_id',
                'l.id as location_id',
                'l.name as location_name'
            )
            ->orderBy('l.name')
            ->get();

        if ($data['locations']->isEmpty()) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | CATEGORY
    |--------------------------------------------------------------------------
    |
    | Category Laminasi berasal dari
    | m_production_cost_categories.
    |
    */
        $data['categories'] = DB::table(
            'm_production_cost_categories as pcc'
        )
            ->join(
                'm_categories as c',
                'c.id',
                '=',
                'pcc.category_id'
            )
            ->whereIn(
                'pcc.production_cost_id',
                $data['productionCostIds']
            )
            ->select(
                'pcc.production_cost_id',
                'c.id as category_id',
                'c.name as category_name'
            )
            ->orderBy('c.name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | LAMINATION + COST + VENDOR
    |--------------------------------------------------------------------------
    */
        $data['details'] = DB::table(
            'm_lamination_production_cost_details as lpcd'
        )
            ->join(
                'm_laminations as l',
                'l.id',
                '=',
                'lpcd.lamination_id'
            )
            ->leftJoin(
                'm_vendors as v',
                'v.id',
                '=',
                'lpcd.vendor_id'
            )
            ->whereIn(
                'lpcd.production_cost_id',
                $data['productionCostIds']
            )
            ->select(
                'lpcd.id',
                'lpcd.production_cost_id',

                // Lamination
                'lpcd.lamination_id',
                'l.name as lamination_name',

                // Vendor
                'lpcd.vendor_id',
                'v.name as vendor_name',
                'lpcd.vendor_price',

                // Cost
                'lpcd.price_per_meter',
                'lpcd.production_cost',
                'lpcd.finishing_cost',
                'lpcd.total_cost',

                // Price
                'lpcd.general_price',
                'lpcd.division_price',
                'lpcd.plain_price'
            )
            ->orderBy('l.name')
            ->get();

        if ($data['details']->isEmpty()) {
            abort(404);
        }

        /*
    |--------------------------------------------------------------------------
    | SIZE
    |--------------------------------------------------------------------------
    */
        $data['detailIds'] = $data['details']
            ->pluck('id')
            ->unique()
            ->values()
            ->toArray();

        $data['sizes'] = collect();

        if (!empty($data['detailIds'])) {
            $data['sizes'] = DB::table(
                'm_lamination_production_cost_detail_sizes as lpcds'
            )
                ->join(
                    'm_lamination_sizes as ls',
                    'ls.id',
                    '=',
                    'lpcds.lamination_size_id'
                )
                ->whereIn(
                    'lpcds.lamination_production_cost_detail_id',
                    $data['detailIds']
                )
                ->select(
                    'lpcds.lamination_production_cost_detail_id',
                    'lpcds.lamination_size_id',
                    'ls.lamination_id',
                    'ls.width',
                    'ls.length',
                    'ls.unit'
                )
                ->orderBy('ls.width')
                ->get();
        }

        /*
    |--------------------------------------------------------------------------
    | GROUP LOCATION
    |--------------------------------------------------------------------------
    |
    | Struktur:
    |
    | NON-OUTSOURCING
    | Location
    |     └── Category
    |             └── Lamination
    |                     └── Size
    |
    | OUTSOURCING
    | Location
    |     └── Vendor
    |             └── Category
    |                     └── Lamination
    |                             └── Size
    |
    |--------------------------------------------------------------------------
    */
        $data['locations'] = $data['locations']
            ->groupBy('location_id')
            ->map(function ($locationRows) use ($data) {

                $location = $locationRows->first();

                /*
            |--------------------------------------------------------------------------
            | DETECT OUTSOURCING
            |--------------------------------------------------------------------------
            */
                $isOutsourcing = strtolower(
                    trim($location->location_name)
                ) === 'outsourcing';

                /*
            |--------------------------------------------------------------------------
            | SEMUA PRODUCTION COST DALAM LOCATION
            |--------------------------------------------------------------------------
            */
                $productionCostIds = $locationRows
                    ->pluck('production_cost_id')
                    ->unique()
                    ->values();

                /*
            |--------------------------------------------------------------------------
            | SEMUA DETAIL DALAM LOCATION
            |--------------------------------------------------------------------------
            */
                $locationDetails = $data['details']
                    ->whereIn(
                        'production_cost_id',
                        $productionCostIds
                    )
                    ->values();

                /*
            |--------------------------------------------------------------------------
            | OUTSOURCING
            |--------------------------------------------------------------------------
            |
            | Location
            |     └── Vendor
            |             └── Category
            |
            */
                if ($isOutsourcing) {

                    /*
                |--------------------------------------------------------------------------
                | GROUP VENDOR
                |--------------------------------------------------------------------------
                */
                    $location->vendors = $locationDetails
                        ->filter(function ($detail) {
                            return $detail->vendor_id !== null;
                        })
                        ->groupBy('vendor_id')
                        ->map(function ($vendorDetails) use (
                            $data
                        ) {

                            $firstDetail = $vendorDetails->first();

                            /*
                        |--------------------------------------------------------------------------
                        | VENDOR DATA
                        |--------------------------------------------------------------------------
                        */
                            $vendor = (object) [
                                'vendor_id' => $firstDetail->vendor_id,
                                'vendor_name' => $firstDetail->vendor_name,
                                'vendor_price' => $firstDetail->vendor_price,
                            ];

                            /*
                        |--------------------------------------------------------------------------
                        | PRODUCTION COST ID UNTUK VENDOR
                        |--------------------------------------------------------------------------
                        */
                            $vendorProductionCostIds = $vendorDetails
                                ->pluck('production_cost_id')
                                ->unique()
                                ->values();

                            /*
                        |--------------------------------------------------------------------------
                        | CATEGORY DALAM VENDOR
                        |--------------------------------------------------------------------------
                        */
                            $vendorCategories = $data['categories']
                                ->whereIn(
                                    'production_cost_id',
                                    $vendorProductionCostIds
                                )
                                ->values();

                            /*
                        |--------------------------------------------------------------------------
                        | GROUP CATEGORY
                        |--------------------------------------------------------------------------
                        */
                            $vendor->categories = $vendorCategories
                                ->groupBy('category_id')
                                ->map(function ($categoryRows) use (
                                    $vendorDetails,
                                    $data
                                ) {

                                    $category = $categoryRows->first();

                                    /*
                                |--------------------------------------------------------------------------
                                | PRODUCTION COST ID UNTUK CATEGORY
                                |--------------------------------------------------------------------------
                                */
                                    $categoryProductionCostIds = $categoryRows
                                        ->pluck('production_cost_id')
                                        ->unique()
                                        ->values();

                                    /*
                                |--------------------------------------------------------------------------
                                | DETAIL LAMINASI DALAM CATEGORY
                                |--------------------------------------------------------------------------
                                */
                                    $categoryDetails = $vendorDetails
                                        ->whereIn(
                                            'production_cost_id',
                                            $categoryProductionCostIds
                                        )
                                        ->values();

                                    /*
                                |--------------------------------------------------------------------------
                                | GROUP LAMINATION + VENDOR PRICE + COST
                                |--------------------------------------------------------------------------
                                |
                                | Size tidak digunakan sebagai pembeda.
                                |
                                */
                                    $category->details = $categoryDetails
                                        ->groupBy(function ($detail) {

                                            return implode('|', [
                                                $detail->lamination_id,

                                                $detail->vendor_id,

                                                number_format(
                                                    (float) $detail->vendor_price,
                                                    2,
                                                    '.',
                                                    ''
                                                ),

                                                number_format(
                                                    (float) $detail->price_per_meter,
                                                    2,
                                                    '.',
                                                    ''
                                                ),

                                                number_format(
                                                    (float) $detail->production_cost,
                                                    2,
                                                    '.',
                                                    ''
                                                ),

                                                number_format(
                                                    (float) $detail->finishing_cost,
                                                    2,
                                                    '.',
                                                    ''
                                                ),

                                                number_format(
                                                    (float) $detail->total_cost,
                                                    2,
                                                    '.',
                                                    ''
                                                ),

                                                number_format(
                                                    (float) $detail->general_price,
                                                    2,
                                                    '.',
                                                    ''
                                                ),

                                                number_format(
                                                    (float) $detail->division_price,
                                                    2,
                                                    '.',
                                                    ''
                                                ),

                                                number_format(
                                                    (float) $detail->plain_price,
                                                    2,
                                                    '.',
                                                    ''
                                                ),
                                            ]);
                                        })
                                        ->map(function ($detailRows) use (
                                            $data
                                        ) {

                                            /*
                                        |--------------------------------------------------------------------------
                                        | DETAIL UTAMA
                                        |--------------------------------------------------------------------------
                                        */
                                            $detail = $detailRows->first();

                                            /*
                                        |--------------------------------------------------------------------------
                                        | SEMUA DETAIL ID DALAM GROUP
                                        |--------------------------------------------------------------------------
                                        */
                                            $detailIds = $detailRows
                                                ->pluck('id')
                                                ->unique()
                                                ->values();

                                            /*
                                        |--------------------------------------------------------------------------
                                        | GABUNG SEMUA SIZE
                                        |--------------------------------------------------------------------------
                                        */
                                            $detail->sizes = $data['sizes']
                                                ->whereIn(
                                                    'lamination_production_cost_detail_id',
                                                    $detailIds
                                                )
                                                ->unique('lamination_size_id')
                                                ->sortBy(function ($size) {

                                                    return [
                                                        (float) $size->width,
                                                        (float) ($size->length ?? 0),
                                                    ];
                                                })
                                                ->values();

                                            return $detail;
                                        })
                                        ->sortBy('lamination_name')
                                        ->values();

                                    /*
                                |--------------------------------------------------------------------------
                                | CATEGORY DATA
                                |--------------------------------------------------------------------------
                                */
                                    $category->category_id = $categoryRows
                                        ->first()
                                        ->category_id;

                                    $category->category_name = $categoryRows
                                        ->first()
                                        ->category_name;

                                    return $category;
                                })
                                ->sortBy('category_name')
                                ->values();

                            return $vendor;
                        })
                        ->sortBy('vendor_name')
                        ->values();

                    /*
                |--------------------------------------------------------------------------
                | OUTSOURCING TIDAK MENGGUNAKAN CATEGORIES LANGSUNG
                |--------------------------------------------------------------------------
                */
                    $location->categories = collect();

                    return $location;
                }

                /*
            |--------------------------------------------------------------------------
            | NON-OUTSOURCING
            |--------------------------------------------------------------------------
            |
            | Location
            |     └── Category
            |             └── Lamination
            |
            |--------------------------------------------------------------------------
            */

                $locationCategories = $data['categories']
                    ->whereIn(
                        'production_cost_id',
                        $productionCostIds
                    )
                    ->values();

                /*
            |--------------------------------------------------------------------------
            | GROUP CATEGORY
            |--------------------------------------------------------------------------
            */
                $location->categories = $locationCategories
                    ->groupBy('category_id')
                    ->map(function ($categoryRows) use (
                        $locationDetails,
                        $data
                    ) {

                        $category = $categoryRows->first();

                        /*
                    |--------------------------------------------------------------------------
                    | PRODUCTION COST ID UNTUK CATEGORY
                    |--------------------------------------------------------------------------
                    */
                        $categoryProductionCostIds = $categoryRows
                            ->pluck('production_cost_id')
                            ->unique()
                            ->values();

                        /*
                    |--------------------------------------------------------------------------
                    | DETAIL LAMINASI DALAM CATEGORY
                    |--------------------------------------------------------------------------
                    */
                        $categoryDetails = $locationDetails
                            ->whereIn(
                                'production_cost_id',
                                $categoryProductionCostIds
                            )
                            ->values();

                        /*
                    |--------------------------------------------------------------------------
                    | GROUP LAMINATION + COST
                    |--------------------------------------------------------------------------
                    |
                    | Laminasi dianggap sama apabila:
                    |
                    | - Laminasi sama
                    | - Harga Per Meter sama
                    | - Ongkos Produksi sama
                    | - Ongkos Finishing sama
                    | - Total Cost sama
                    | - Harga Umum sama
                    | - Harga Divisi sama
                    | - Harga Polos sama
                    |
                    | Size tidak digunakan sebagai pembeda.
                    |
                    |--------------------------------------------------------------------------
                    */
                        $category->details = $categoryDetails
                            ->groupBy(function ($detail) {

                                return implode('|', [
                                    $detail->lamination_id,

                                    number_format(
                                        (float) $detail->price_per_meter,
                                        2,
                                        '.',
                                        ''
                                    ),

                                    number_format(
                                        (float) $detail->production_cost,
                                        2,
                                        '.',
                                        ''
                                    ),

                                    number_format(
                                        (float) $detail->finishing_cost,
                                        2,
                                        '.',
                                        ''
                                    ),

                                    number_format(
                                        (float) $detail->total_cost,
                                        2,
                                        '.',
                                        ''
                                    ),

                                    number_format(
                                        (float) $detail->general_price,
                                        2,
                                        '.',
                                        ''
                                    ),

                                    number_format(
                                        (float) $detail->division_price,
                                        2,
                                        '.',
                                        ''
                                    ),

                                    number_format(
                                        (float) $detail->plain_price,
                                        2,
                                        '.',
                                        ''
                                    ),
                                ]);
                            })
                            ->map(function ($detailRows) use ($data) {

                                /*
                            |--------------------------------------------------------------------------
                            | DETAIL UTAMA
                            |--------------------------------------------------------------------------
                            */
                                $detail = $detailRows->first();

                                /*
                            |--------------------------------------------------------------------------
                            | SEMUA DETAIL ID DALAM GROUP
                            |--------------------------------------------------------------------------
                            */
                                $detailIds = $detailRows
                                    ->pluck('id')
                                    ->unique()
                                    ->values();

                                /*
                            |--------------------------------------------------------------------------
                            | GABUNG SEMUA SIZE
                            |--------------------------------------------------------------------------
                            */
                                $detail->sizes = $data['sizes']
                                    ->whereIn(
                                        'lamination_production_cost_detail_id',
                                        $detailIds
                                    )
                                    ->unique('lamination_size_id')
                                    ->sortBy(function ($size) {

                                        return [
                                            (float) $size->width,
                                            (float) ($size->length ?? 0),
                                        ];
                                    })
                                    ->values();

                                return $detail;
                            })
                            ->sortBy('lamination_name')
                            ->values();

                        /*
                    |--------------------------------------------------------------------------
                    | CATEGORY DATA
                    |--------------------------------------------------------------------------
                    */
                        $category->category_id = $categoryRows
                            ->first()
                            ->category_id;

                        $category->category_name = $categoryRows
                            ->first()
                            ->category_name;

                        return $category;
                    })
                    ->sortBy('category_name')
                    ->values();

                /*
            |--------------------------------------------------------------------------
            | NON-OUTSOURCING TIDAK MENGGUNAKAN VENDORS
            |--------------------------------------------------------------------------
            */
                $location->vendors = collect();

                return $location;
            })
            ->values();

        /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */
        return view(
            'admin.production-cost-finishing-lamination.details'
        )->with($data);
    }

    public function export(Request $request)
    {
        $request->validate(['pricing_engine' => 'nullable|in:large_format,digital_print_a3']);
        if ($request->query('pricing_engine') === 'digital_print_a3') {
            return Excel::download(
                new DigitalLaminationProductionCostsExport(),
                'production_cost_lamination_digital_print_a3.xlsx'
            );
        }

        return Excel::download(
            new ProductionCostFinishingLaminationsExport(),
            'production_cost_finishing_laminations.xlsx'
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
            $import = new ProductionCostFinishingLaminationsImport();

            Excel::import(
                $import,
                $request->file('file')
            );

            return response()->json([
                'success' => true,
                'message' =>
                $import->importedRows .
                    ' data Production Cost Laminasi berhasil diimport.',
                'imported_rows' => $import->importedRows,
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\ProductionCostFinishingsExport;
use App\Exports\DigitalProductionCostsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductionCostFinishingsImport;
use App\Support\ProductionCostFinishingMarkup;
use Exception;

class ProductionCostFinishingCT extends Controller
{
    public function index()
    {
        return view('admin.production-cost-finishing.index');
    }

    // 
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
    */

        $data['productionCosts'] = DB::table('m_production_costs')
            ->where('engine_id', $data['engineId'])
            ->get();

        if ($data['productionCosts']->isEmpty()) {
            abort(404);
        }

        $data['productionCostIds'] = $data['productionCosts']
            ->pluck('id')
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

        /*
    |--------------------------------------------------------------------------
    | MATERIAL + CATEGORY + COST
    |--------------------------------------------------------------------------
    */

        $data['details'] = DB::table(
            'm_production_cost_details as pcd'
        )
            ->join(
                'm_materials as m',
                'm.id',
                '=',
                'pcd.material_id'
            )
            ->join(
                'm_categories as c',
                'c.id',
                '=',
                'm.category_id'
            )
            ->leftJoin(
                'm_vendors as v',
                'v.id',
                '=',
                'pcd.vendor_id'
            )
            ->whereIn(
                'pcd.production_cost_id',
                $data['productionCostIds']
            )
            ->select(
                'pcd.id',
                'pcd.production_cost_id',

                // Category
                'c.id as category_id',
                'c.name as category_name',

                // Material
                'pcd.material_id',
                'm.material_name',

                // Vendor
                'pcd.vendor_id',
                'v.name as vendor_name',
                'pcd.vendor_price',

                // Cost
                'pcd.price_per_meter',
                'pcd.production_cost',
                'pcd.finishing_cost',
                'pcd.total_cost',

                // Price
                'pcd.general_price',
                'pcd.division_price',
                'pcd.plain_price'
            )
            ->orderBy('c.name')
            ->orderBy('m.material_name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | WIDTH
    |--------------------------------------------------------------------------
    */

        $data['detailIds'] = $data['details']
            ->pluck('id')
            ->toArray();

        $data['widths'] = collect();

        if (!empty($data['detailIds'])) {
            $data['widths'] = DB::table(
                'm_production_cost_detail_widths as pcdw'
            )
                ->join(
                    'm_material_sizes as ms',
                    'ms.id',
                    '=',
                    'pcdw.material_size_id'
                )
                ->whereIn(
                    'pcdw.production_cost_detail_id',
                    $data['detailIds']
                )
                ->select(
                    'pcdw.production_cost_detail_id',
                    'pcdw.material_size_id',
                    'ms.width',
                    'ms.unit'
                )
                ->orderBy('ms.width')
                ->get();
        }

        /*
    |--------------------------------------------------------------------------
    | GROUP LOCATION
    |--------------------------------------------------------------------------
    |
    | Non-Outsourcing:
    | Location
    |     └── Category
    |             └── Material
    |                     └── Width
    |
    | Outsourcing:
    | Location
    |     └── Vendor
    |             └── Category
    |                     └── Material
    |                             └── Width
    |
    */

        $data['locations'] = $data['locations']
            ->groupBy('location_id')
            ->map(function ($locationRows) use ($data) {

                $location = $locationRows->first();

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
            | CEK OUTSOURCING
            |--------------------------------------------------------------------------
            */

                $isOutsourcing =
                    strtolower(trim($location->location_name)) === 'outsourcing';

                /*
            |--------------------------------------------------------------------------
            | OUTSOURCING
            |--------------------------------------------------------------------------
            |
            | Location
            |     └── Vendor
            |             └── Category
            |                     └── Material
            |                             └── Width
            |
            */

                if ($isOutsourcing) {

                    $location->vendors = $locationDetails
                        ->groupBy('vendor_id')
                        ->map(function ($vendorRows) use ($data) {

                            $vendor = $vendorRows->first();

                            /*
                        |--------------------------------------------------------------------------
                        | GROUP CATEGORY
                        |--------------------------------------------------------------------------
                        */

                            $vendor->categories = $vendorRows
                                ->groupBy('category_id')
                                ->map(function ($categoryRows) use ($data) {

                                    $category = $categoryRows->first();

                                    /*
                                |--------------------------------------------------------------------------
                                | GROUP MATERIAL + COST
                                |--------------------------------------------------------------------------
                                |
                                | Material dianggap sama apabila:
                                |
                                | - Material sama
                                | - Harga Per Meter sama
                                | - Ongkos Produksi sama
                                | - Ongkos Finishing sama
                                | - Total Cost sama
                                | - Harga Umum sama
                                | - Harga Divisi sama
                                | - Harga Polos sama
                                |
                                | Width tidak digunakan sebagai pembeda.
                                |
                                */

                                    $category->details = $categoryRows
                                        ->groupBy(function ($detail) {

                                            return implode('|', [
                                                $detail->material_id,

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
                                        | GABUNG SEMUA WIDTH
                                        |--------------------------------------------------------------------------
                                        */

                                            $detail->widths = $data['widths']
                                                ->whereIn(
                                                    'production_cost_detail_id',
                                                    $detailIds
                                                )
                                                ->unique('material_size_id')
                                                ->sortBy(function ($width) {
                                                    return (float) $width->width;
                                                })
                                                ->values();

                                            return $detail;
                                        })
                                        ->sortBy('material_name')
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
                        | VENDOR DATA
                        |--------------------------------------------------------------------------
                        */

                            $vendor->vendor_id = $vendorRows
                                ->first()
                                ->vendor_id;

                            $vendor->vendor_name = $vendorRows
                                ->first()
                                ->vendor_name;

                            $vendor->vendor_price = $vendorRows
                                ->first()
                                ->vendor_price;

                            return $vendor;
                        })
                        ->sortBy('vendor_name')
                        ->values();

                    return $location;
                }

                /*
            |--------------------------------------------------------------------------
            | NON-OUTSOURCING
            |--------------------------------------------------------------------------
            |
            | Logic lama dipertahankan:
            |
            | Location
            |     └── Category
            |             └── Material
            |                     └── Width
            |
            */

                $location->categories = $locationDetails
                    ->groupBy('category_id')
                    ->map(function ($categoryRows) use ($data) {

                        $category = $categoryRows->first();

                        /*
                    |--------------------------------------------------------------------------
                    | GROUP MATERIAL + COST
                    |--------------------------------------------------------------------------
                    */

                        $category->details = $categoryRows
                            ->groupBy(function ($detail) {

                                return implode('|', [
                                    $detail->material_id,

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
                            | GABUNG SEMUA WIDTH
                            |--------------------------------------------------------------------------
                            */

                                $detail->widths = $data['widths']
                                    ->whereIn(
                                        'production_cost_detail_id',
                                        $detailIds
                                    )
                                    ->unique('material_size_id')
                                    ->sortBy(function ($width) {
                                        return (float) $width->width;
                                    })
                                    ->values();

                                return $detail;
                            })
                            ->sortBy('material_name')
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
            | RETURN LOCATION
            |--------------------------------------------------------------------------
            */

                return $location;
            })
            ->values();

        /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */

        return view(
            'admin.production-cost-finishing.details'
        )->with($data);
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
    | TOTAL SEMUA ENGINE
    |--------------------------------------------------------------------------
    */

        $totalRecords = DB::table('m_production_costs')
            ->distinct()
            ->count('engine_id');

        /*
    |--------------------------------------------------------------------------
    | QUERY UTAMA
    |--------------------------------------------------------------------------
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
                'm_production_cost_details as pcd',
                'pcd.production_cost_id',
                '=',
                'pc.id'
            )
            ->join(
                'm_production_cost_detail_widths as pcdw',
                'pcdw.production_cost_detail_id',
                '=',
                'pcd.id'
            )
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
                    'COUNT(DISTINCT pcd.material_id) as material_count'
                ),
                DB::raw(
                    'COUNT(DISTINCT pcdw.material_size_id) as width_count'
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
                    );
            });
        }

        /*
    |--------------------------------------------------------------------------
    | TOTAL DATA SETELAH FILTER
    |--------------------------------------------------------------------------
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
        $query = DB::table('m_digital_production_costs as pc')
            ->join('m_engines as e', 'e.id', '=', 'pc.engine_id')
            ->join('m_locations as l', 'l.id', '=', 'pc.location_id')
            ->leftJoin('m_vendors as v', 'v.id', '=', 'pc.vendor_id')
            ->leftJoin('m_digital_production_cost_details as pcd', 'pcd.digital_production_cost_id', '=', 'pc.id')
            ->whereRaw('LOWER(TRIM(e.name)) = ?', ['digital print a3+'])
            ->select(
                'pc.id as digital_production_cost_id',
                'e.id as engine_id',
                'e.name as engine_name',
                'l.name as location_names',
                'v.name as vendor_name',
                DB::raw('COUNT(DISTINCT pcd.material_id) as material_count'),
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
                    ->orWhereRaw('LOWER(v.name) LIKE ?', [$search]);
            });
        }

        $totalFiltered = (clone $query)->get()->count();

        $data = $query
            ->orderBy('e.name', 'asc')
            ->orderBy('l.name', 'asc')
            ->orderBy('v.name', 'asc')
            ->offset((int) $request->input('start', 0))
            ->limit((int) $request->input('length', 10))
            ->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    public function detailsDigital($id)
    {
        $productionCostId = base64_decode($id, true);

        if ($productionCostId === false || !ctype_digit($productionCostId)) {
            abort(404);
        }

        $productionCost = DB::table('m_digital_production_costs as pc')
            ->join('m_engines as e', 'e.id', '=', 'pc.engine_id')
            ->join('m_locations as l', 'l.id', '=', 'pc.location_id')
            ->leftJoin('m_vendors as v', 'v.id', '=', 'pc.vendor_id')
            ->where('pc.id', (int) $productionCostId)
            ->whereRaw('LOWER(TRIM(e.name)) = ?', ['digital print a3+'])
            ->select(
                'pc.id',
                'e.name as engine_name',
                'l.name as location_name',
                'v.name as vendor_name'
            )
            ->first();

        if (!$productionCost) {
            abort(404);
        }

        $details = DB::table('m_digital_production_cost_details as d')
            ->join('m_materials as m', 'm.id', '=', 'd.material_id')
            ->join('m_categories as c', 'c.id', '=', 'm.category_id')
            ->where('d.digital_production_cost_id', $productionCost->id)
            ->select(
                'd.id',
                'd.material_id',
                'm.material_name',
                'c.id as category_id',
                'c.name as category_name',
                'd.paper_cost',
                'd.click_cost',
                'd.single_print_cost',
                'd.double_print_cost'
            )
            ->orderBy('c.name')
            ->orderBy('m.material_name')
            ->get();

        $prices = DB::table('m_digital_production_cost_prices')
            ->whereIn('digital_production_cost_detail_id', $details->pluck('id'))
            ->select(
                'digital_production_cost_detail_id',
                'price_type',
                'print_type',
                'quantity_tier',
                'markup_percentage',
                'selling_price'
            )
            ->get()
            ->groupBy('digital_production_cost_detail_id');

        $tierLabels = [
            'tier_1' => '1 lbr',
            'tier_2_5' => '2-5 lbr',
            'tier_6_50' => '6-50 lbr',
            'tier_100' => '100 lbr',
            'tier_300' => '300 lbr',
            'tier_500' => '500 lbr',
        ];

        $priceGroups = [
            'general_single' => ['general', 'single', 'Harga Umum · Print 1 Muka'],
            'general_double' => ['general', 'double', 'Harga Umum · Print 2 Muka'],
            'division_single' => ['division', 'single', 'Harga Divisi · Print 1 Muka'],
            'division_double' => ['division', 'double', 'Harga Divisi · Print 2 Muka'],
        ];

        $materialSizes = DB::table('m_material_sizes')
            ->whereIn('material_id', $details->pluck('material_id')->unique()->values())
            ->orderBy('id')
            ->get()
            ->groupBy('material_id');

        $formatNumber = function ($value) {
            return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
        };

        $details->each(function ($detail) use ($prices, $priceGroups, $tierLabels, $materialSizes, $formatNumber) {
            $detailPrices = ($prices->get($detail->id) ?? collect())->keyBy(function ($price) {
                return $price->price_type . '|' . $price->print_type . '|' . $price->quantity_tier;
            });

            $detail->size_label = ($materialSizes->get($detail->material_id) ?? collect())
                ->map(function ($size) use ($formatNumber) {
                    return $formatNumber($size->width) . ' x ' . $formatNumber($size->length) . ' ' . $size->unit;
                })
                ->implode(', ');

            $detail->prices = [];

            foreach ($priceGroups as $groupKey => [$priceType, $printType]) {
                foreach (array_keys($tierLabels) as $tier) {
                    $detail->prices[$groupKey][$tier] = $detailPrices->get($priceType . '|' . $printType . '|' . $tier);
                }
            }
        });

        $data['productionCost'] = $productionCost;
        $data['tierLabels'] = $tierLabels;
        $data['priceGroups'] = collect($priceGroups)->map(function ($group) {
            return $group[2];
        });
        $data['categories'] = $details
            ->groupBy('category_id')
            ->map(function ($categoryDetails) use ($priceGroups, $tierLabels, $formatNumber) {
                // Markup per tier sama untuk satu kategori karena berasal dari pricing rule yang sama.
                $markups = [];

                foreach (array_keys($priceGroups) as $groupKey) {
                    foreach (array_keys($tierLabels) as $tier) {
                        $price = $categoryDetails
                            ->map(function ($detail) use ($groupKey, $tier) {
                                return $detail->prices[$groupKey][$tier];
                            })
                            ->filter()
                            ->first();

                        $markups[$groupKey][$tier] = $price ? $formatNumber($price->markup_percentage) . '%' : '-';
                    }
                }

                return (object) [
                    'category_name' => $categoryDetails->first()->category_name,
                    'markups' => $markups,
                    'details' => $categoryDetails->values(),
                ];
            })
            ->values();

        return view('admin.production-cost-finishing.details-digital')->with($data);
    }

    public function create()
    {
        $data['engines'] = DB::table('m_engines')->where('status', 'Active')->orderBy('name')->get();
        $data['locations'] = DB::table('m_locations')->where('status', 'Active')->orderBy('name')->get();
        $pricingCategories = DB::table('m_pricing_rules as pr')
            ->join('m_engines as e', 'pr.engine_id', '=', 'e.id')
            ->whereRaw('LOWER(TRIM(e.name)) = ?', ['large format'])
            ->select('pr.engine_id', 'pr.category_id')
            ->union(
                DB::table('m_digital_pricing_rules as pr')
                    ->join('m_engines as e', 'pr.engine_id', '=', 'e.id')
                    ->whereRaw('LOWER(TRIM(e.name)) = ?', ['digital print a3+'])
                    ->select('pr.engine_id', 'pr.category_id')
            );

        $data['materialsByEngine'] = DB::table('m_materials as a')
            ->joinSub($pricingCategories, 'pricing_categories', function ($join) {
                $join->on('a.category_id', '=', 'pricing_categories.category_id');
            })
            ->leftJoin(
                'm_categories as b',
                'a.category_id',
                '=',
                'b.id'
            )
            ->select(
                'pricing_categories.engine_id',
                'a.id',
                'a.material_name',
                'b.name as category_name'
            )
            ->distinct()
            ->where('a.status', 'Active')
            ->orderBy('a.material_name')
            ->get()
            ->groupBy('engine_id');

        $data['vendors'] = DB::table('m_vendors')
            ->where('status', 'Active')
            ->get();

        $data['materialSizes'] = DB::table('m_material_sizes')->orderBy('width')->get();
        return view('admin.production-cost-finishing.create')->with($data);
    }

    public function store(Request $request)
    {
        // Digital Print A3+ memiliki alur penyimpanan sendiri agar logika Large Format tidak tersentuh.
        if ($this->hasDigitalPrintConfiguration($request)) {
            return $this->storeDigital($request);
        }

        $request->validate([
            'configurations' => 'required|array|min:1',
            'configurations.*.engine_id' =>
            'required|integer|exists:m_engines,id',
            'configurations.*.location_ids' =>
            'required|array|min:1',
            'configurations.*.location_ids.*' =>
            'required|integer|exists:m_locations,id',
            'configurations.*.items' =>
            'required|array|min:1',
            'configurations.*.items.*.material_id' =>
            'required|integer|exists:m_materials,id',
            'configurations.*.items.*.width_ids' =>
            'required|array|min:1',
            'configurations.*.items.*.width_ids.*' =>
            'required|integer|exists:m_material_sizes,id',
            'configurations.*.vendor_id' =>
            'nullable|integer|exists:m_vendors,id',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Helper Parse Number
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
        | Helper Round Up
        |--------------------------------------------------------------------------
        */

        $roundUp = function ($value, $rounding) {
            if ($rounding === null || $rounding <= 0) {
                return $value;
            }

            return ceil($value / $rounding) * $rounding;
        };

        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Load Engine
            |--------------------------------------------------------------------------
            */

            $engineIds = collect($request->input('configurations'))
                ->pluck('engine_id')
                ->map(function ($id) {
                    return (int) $id;
                })
                ->unique()
                ->values();

            $engines = DB::table('m_engines')
                ->whereIn('id', $engineIds)
                ->get()
                ->keyBy('id');

            /*
            |--------------------------------------------------------------------------
            | Check Calculation Rules per Engine
            |--------------------------------------------------------------------------
            */

            foreach ($engineIds as $engineId) {

                $engine = $engines->get($engineId);

                if (!$engine) {
                    throw ValidationException::withMessages([
                        'configurations' =>
                        'Mesin tidak ditemukan.'
                    ]);
                }

                $engineName = strtolower(trim($engine->name));

                /*
                |--------------------------------------------------------------------------
                | Large Format
                |--------------------------------------------------------------------------
                */

                if ($engineName === 'large format') {
                    continue;
                }

                /*
                |--------------------------------------------------------------------------
                | Digital Print A3+
                |--------------------------------------------------------------------------
                */

                if ($engineName === 'digital print a3+') {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'type' => 'warning',
                        'message' =>
                        'Mesin Digital Print A3+ belum mempunyai master calculation rules.'
                    ], 422);
                }

                /*
                |--------------------------------------------------------------------------
                | Engine Lain
                |--------------------------------------------------------------------------
                */

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

            /*
            |--------------------------------------------------------------------------
            | Load Locations
            |--------------------------------------------------------------------------
            */

            $locations = DB::table('m_locations')
                ->get()
                ->keyBy('id');

            /*
            |--------------------------------------------------------------------------
            | Load Vendors
            |--------------------------------------------------------------------------
            */

            $vendors = DB::table('m_vendors')
                ->where('status', 'Active')
                ->get()
                ->keyBy('id');

            /*
            |--------------------------------------------------------------------------
            | Validate Configuration
            |--------------------------------------------------------------------------
            */

            foreach (
                $request->input('configurations')
                as $configurationIndex => $configuration
            ) {

                $locationIds = array_map(
                    'intval',
                    $configuration['location_ids']
                );

                $isOutsourcing = false;

                /*
                |--------------------------------------------------------------------------
                | Check Locations
                |--------------------------------------------------------------------------
                */

                foreach ($locationIds as $locationId) {

                    if (!$locations->has($locationId)) {
                        throw ValidationException::withMessages([
                            'configurations.' .
                                $configurationIndex .
                                '.location_ids' =>
                            'Lokasi tidak ditemukan.'
                        ]);
                    }

                    $locationName = strtolower(
                        trim(
                            $locations
                                ->get($locationId)
                                ->name
                        )
                    );

                    if ($locationName === 'outsourcing') {
                        $isOutsourcing = true;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Outsourcing Tidak Boleh Digabung dengan Lokasi Lain
                |--------------------------------------------------------------------------
                */

                if (
                    $isOutsourcing &&
                    count($locationIds) > 1
                ) {
                    throw ValidationException::withMessages([
                        'configurations.' .
                            $configurationIndex .
                            '.location_ids' =>
                        'Lokasi Outsourcing tidak dapat digabung dengan lokasi lain.'
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Vendor Validation
                |--------------------------------------------------------------------------
                */

                $vendorId = $configuration['vendor_id'] ?? null;

                if ($isOutsourcing) {

                    if (
                        $vendorId === null ||
                        $vendorId === ''
                    ) {
                        throw ValidationException::withMessages([
                            'configurations.' .
                                $configurationIndex .
                                '.vendor_id' =>
                            'Master Vendor wajib dipilih untuk lokasi Outsourcing.'
                        ]);
                    }

                    $vendorId = (int) $vendorId;

                    if (!$vendors->has($vendorId)) {
                        throw ValidationException::withMessages([
                            'configurations.' .
                                $configurationIndex .
                                '.vendor_id' =>
                            'Master Vendor tidak ditemukan atau tidak aktif.'
                        ]);
                    }
                } else {

                    $vendorId = null;
                }

                /*
                |--------------------------------------------------------------------------
                | Validate Item Input
                |--------------------------------------------------------------------------
                */

                foreach (
                    $configuration['items']
                    as $itemIndex => $item
                ) {

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
                                'Harga Vendor wajib diisi.'
                            ]);
                        }
                    } else {

                        if (
                            !isset($item['price_per_meter']) ||
                            $item['price_per_meter'] === ''
                        ) {
                            throw ValidationException::withMessages([
                                'configurations.' .
                                    $configurationIndex .
                                    '.items.' .
                                    $itemIndex .
                                    '.price_per_meter' =>
                                'Harga Permeter wajib diisi.'
                            ]);
                        }

                        if (
                            !isset($item['production_cost']) ||
                            $item['production_cost'] === ''
                        ) {
                            throw ValidationException::withMessages([
                                'configurations.' .
                                    $configurationIndex .
                                    '.items.' .
                                    $itemIndex .
                                    '.production_cost' =>
                                'Ongkos Produksi wajib diisi.'
                            ]);
                        }

                        if (
                            !isset($item['finishing_cost']) ||
                            $item['finishing_cost'] === ''
                        ) {
                            throw ValidationException::withMessages([
                                'configurations.' .
                                    $configurationIndex .
                                    '.items.' .
                                    $itemIndex .
                                    '.finishing_cost' =>
                                'Ongkos Finishing wajib diisi.'
                            ]);
                        }
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Load Materials
            |--------------------------------------------------------------------------
            |
            | Category Production Cost diambil dari material.
            |
            */

            $materials = DB::table('m_materials as m')
                ->join(
                    'm_categories as c',
                    'm.category_id',
                    '=',
                    'c.id'
                )
                ->select(
                    'm.id',
                    'm.category_id',
                    'm.material_code',
                    'm.material_name',
                    'c.name as category_name'
                )
                ->get()
                ->keyBy('id');

            /*
            |--------------------------------------------------------------------------
            | Load Active Pricing Rules
            |--------------------------------------------------------------------------
            |
            | Rule Non-Outsourcing:
            |
            | engine + location + category + price_type
            | vendor_id = NULL
            |
            | Rule Outsourcing:
            |
            | engine + location + vendor + category + price_type
            |
            */

            $pricingRules = DB::table('m_pricing_rules')
                ->where('status', 'Active')
                ->get();

            /*
            |--------------------------------------------------------------------------
            | Normalize Configurations
            |--------------------------------------------------------------------------
            */

            $normalizedRows = [];

            foreach (
                $request->input('configurations')
                as $configurationIndex => $configuration
            ) {

                $engineId = (int) $configuration['engine_id'];

                $locationIds = array_map(
                    'intval',
                    $configuration['location_ids']
                );

                $vendorId = $configuration['vendor_id'] ?? null;

                if ($vendorId !== null && $vendorId !== '') {
                    $vendorId = (int) $vendorId;
                } else {
                    $vendorId = null;
                }

                /*
                |--------------------------------------------------------------------------
                | Determine Outsourcing
                |--------------------------------------------------------------------------
                */

                $isOutsourcing = false;

                foreach ($locationIds as $locationId) {

                    $locationName = strtolower(
                        trim(
                            $locations
                                ->get($locationId)
                                ->name
                        )
                    );

                    if ($locationName === 'outsourcing') {
                        $isOutsourcing = true;
                        break;
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Process Location
                |--------------------------------------------------------------------------
                */

                foreach ($locationIds as $locationId) {

                    $locationName = strtolower(
                        trim(
                            $locations
                                ->get($locationId)
                                ->name
                        )
                    );

                    $locationIsOutsourcing =
                        $locationName === 'outsourcing';

                    /*
                    |--------------------------------------------------------------------------
                    | Vendor harus sesuai scope
                    |--------------------------------------------------------------------------
                    */

                    if ($locationIsOutsourcing) {

                        if ($vendorId === null) {
                            throw ValidationException::withMessages([
                                'configurations.' .
                                    $configurationIndex .
                                    '.vendor_id' =>
                                'Master Vendor wajib dipilih untuk lokasi Outsourcing.'
                            ]);
                        }
                    } else {

                        $vendorIdForRow = null;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Process Materials
                    |--------------------------------------------------------------------------
                    */

                    foreach (
                        $configuration['items']
                        as $itemIndex => $item
                    ) {

                        $materialId =
                            (int) $item['material_id'];

                        $material =
                            $materials->get($materialId);

                        if (!$material) {
                            throw ValidationException::withMessages([
                                'configurations.' .
                                    $configurationIndex .
                                    '.items.' .
                                    $itemIndex .
                                    '.material_id' =>
                                'Material tidak ditemukan.'
                            ]);
                        }

                        $categoryId =
                            (int) $material->category_id;

                        /*
                        |--------------------------------------------------------------------------
                        | Numeric Values
                        |--------------------------------------------------------------------------
                        */

                        $pricePerMeter = 0;
                        $productionCost = 0;
                        $finishingCost = 0;
                        $vendorPrice = null;

                        if ($locationIsOutsourcing) {

                            $vendorPrice =
                                $parseNumber(
                                    $item['vendor_price']
                                );

                            if ($vendorPrice <= 0) {
                                throw ValidationException::withMessages([
                                    'configurations.' .
                                        $configurationIndex .
                                        '.items.' .
                                        $itemIndex .
                                        '.vendor_price' =>
                                    'Harga Vendor harus lebih besar dari 0.'
                                ]);
                            }
                        } else {

                            $pricePerMeter =
                                $parseNumber(
                                    $item['price_per_meter']
                                );

                            $productionCost =
                                $parseNumber(
                                    $item['production_cost']
                                );

                            $finishingCost =
                                $parseNumber(
                                    $item['finishing_cost']
                                );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Pricing Rules
                        |--------------------------------------------------------------------------
                        */

                        $generalRule = null;
                        $divisionRule = null;
                        $plainRule = null;

                        if ($locationIsOutsourcing) {

                            /*
                            |--------------------------------------------------------------------------
                            | Outsourcing
                            |--------------------------------------------------------------------------
                            |
                            | Filter:
                            | Location = Outsourcing
                            | Vendor   = selected vendor
                            | Category = material category
                            |
                            */

                            $generalRule =
                                $pricingRules
                                ->first(function ($rule) use (
                                    $engineId,
                                    $locationId,
                                    $vendorId,
                                    $categoryId
                                ) {
                                    return
                                        (int) $rule->engine_id === $engineId &&
                                        (int) $rule->location_id === $locationId &&
                                        (int) $rule->vendor_id === $vendorId &&
                                        (int) $rule->category_id === $categoryId &&
                                        $rule->price_type === 'general';
                                });

                            $divisionRule =
                                $pricingRules
                                ->first(function ($rule) use (
                                    $engineId,
                                    $locationId,
                                    $vendorId,
                                    $categoryId
                                ) {
                                    return
                                        (int) $rule->engine_id === $engineId &&
                                        (int) $rule->location_id === $locationId &&
                                        (int) $rule->vendor_id === $vendorId &&
                                        (int) $rule->category_id === $categoryId &&
                                        $rule->price_type === 'division';
                                });

                            /*
                            |--------------------------------------------------------------------------
                            | General Rule wajib
                            |--------------------------------------------------------------------------
                            */

                            if (!$generalRule) {
                                throw ValidationException::withMessages([
                                    'configurations' =>
                                    'Pricing Rule Harga Umum untuk lokasi Outsourcing, vendor, dan kategori material tersebut tidak ditemukan.'
                                ]);
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Division Rule wajib
                            |--------------------------------------------------------------------------
                            */

                            if (!$divisionRule) {
                                throw ValidationException::withMessages([
                                    'configurations' =>
                                    'Pricing Rule Harga Divisi untuk lokasi Outsourcing, vendor, dan kategori material tersebut tidak ditemukan.'
                                ]);
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Outsourcing Price Calculation
                            |--------------------------------------------------------------------------
                            */

                            $generalMarkup =
                                ProductionCostFinishingMarkup::general(
                                    $engines->get($engineId)->name,
                                    $locations->get($locationId)->name,
                                    $material->category_name,
                                    $material->material_name,
                                    (float) $generalRule->markup_percentage
                                );

                            $generalRounding =
                                $generalRule->rounding_value;

                            $generalPrice =
                                (
                                    $vendorPrice +
                                    (
                                        $vendorPrice *
                                        ($generalMarkup / 100)
                                    )
                                );

                            $generalPrice =
                                $roundUp(
                                    $generalPrice,
                                    $generalRounding
                                );

                            /*
                            |--------------------------------------------------------------------------
                            | Division Price
                            |--------------------------------------------------------------------------
                            |
                            | Mengikuti rumus yang kamu berikan:
                            |
                            | CEILING(
                            |     Harga Umum * Markup Divisi;
                            |     Pembulatan Divisi
                            | )
                            |
                            */

                            $divisionMarkup =
                                (float) $divisionRule->markup_percentage;

                            $divisionRounding =
                                $divisionRule->rounding_value;

                            $divisionPrice =
                                $generalPrice *
                                ($divisionMarkup / 100);

                            $divisionPrice =
                                $roundUp(
                                    $divisionPrice,
                                    $divisionRounding
                                );

                            /*
                            |--------------------------------------------------------------------------
                            | Plain Price
                            |--------------------------------------------------------------------------
                            |
                            | Belum ada rumus Outsourcing dari requirement.
                            | Kolom database NOT NULL, sehingga sementara 0.
                            |
                            */

                            $plainPrice = 0;

                            /*
                            |--------------------------------------------------------------------------
                            | Total Cost
                            |--------------------------------------------------------------------------
                            |
                            | Untuk Outsourcing, dasar biaya adalah Harga Vendor.
                            | Nilai ini sementara disimpan sebagai total cost.
                            |
                            */

                            $totalCost = $vendorPrice;

                            /*
                            |--------------------------------------------------------------------------
                            | Karena biaya normal tidak digunakan Outsourcing
                            |--------------------------------------------------------------------------
                            */

                            $pricePerMeter = 0;
                            $productionCost = 0;
                            $finishingCost = 0;

                            /*
                            |--------------------------------------------------------------------------
                            | Pricing Rule ID
                            |--------------------------------------------------------------------------
                            */

                            $generalPricingRuleId =
                                $generalRule->id;

                            $divisionPricingRuleId =
                                $divisionRule->id;

                            $plainPricingRuleId = null;

                            $vendorIdForRow = $vendorId;
                        } else {

                            /*
                            |--------------------------------------------------------------------------
                            | Non-Outsourcing
                            |--------------------------------------------------------------------------
                            |
                            | Vendor harus NULL.
                            |
                            */

                            $vendorIdForRow = null;
                            $vendorPrice = null;

                            /*
                            |--------------------------------------------------------------------------
                            | General Pricing Rule
                            |--------------------------------------------------------------------------
                            */

                            $generalRule =
                                $pricingRules
                                ->first(function ($rule) use (
                                    $engineId,
                                    $locationId,
                                    $categoryId
                                ) {
                                    return
                                        (int) $rule->engine_id === $engineId &&
                                        (int) $rule->location_id === $locationId &&
                                        $rule->vendor_id === null &&
                                        (int) $rule->category_id === $categoryId &&
                                        $rule->price_type === 'general';
                                });

                            /*
                            |--------------------------------------------------------------------------
                            | Division Pricing Rule
                            |--------------------------------------------------------------------------
                            */

                            $divisionRule =
                                $pricingRules
                                ->first(function ($rule) use (
                                    $engineId,
                                    $locationId,
                                    $categoryId
                                ) {
                                    return
                                        (int) $rule->engine_id === $engineId &&
                                        (int) $rule->location_id === $locationId &&
                                        $rule->vendor_id === null &&
                                        (int) $rule->category_id === $categoryId &&
                                        $rule->price_type === 'division';
                                });

                            /*
                            |--------------------------------------------------------------------------
                            | Plain Pricing Rule
                            |--------------------------------------------------------------------------
                            */

                            $plainRule =
                                $pricingRules
                                ->first(function ($rule) use (
                                    $engineId,
                                    $locationId,
                                    $categoryId
                                ) {
                                    return
                                        (int) $rule->engine_id === $engineId &&
                                        (int) $rule->location_id === $locationId &&
                                        $rule->vendor_id === null &&
                                        (int) $rule->category_id === $categoryId &&
                                        $rule->price_type === 'plain';
                                });

                            /*
                            |--------------------------------------------------------------------------
                            | General + Division wajib
                            |--------------------------------------------------------------------------
                            */

                            if (!$generalRule) {
                                throw ValidationException::withMessages([
                                    'configurations' =>
                                    'Pricing Rule Harga Umum untuk mesin, lokasi, dan kategori tersebut tidak ditemukan.'
                                ]);
                            }

                            if (!$divisionRule) {
                                throw ValidationException::withMessages([
                                    'configurations' =>
                                    'Pricing Rule Harga Divisi untuk mesin, lokasi, dan kategori tersebut tidak ditemukan.'
                                ]);
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Existing Non-Outsourcing Calculation
                            |--------------------------------------------------------------------------
                            |
                            | BAGIAN INI HARUS DIISI DENGAN RUMUS LARGE FORMAT
                            | EXISTING YANG SUDAH KAMU VALIDASI.
                            |
                            | Untuk sementara dasar cost menggunakan:
                            |
                            | total_cost =
                            | price_per_meter +
                            | production_cost +
                            | finishing_cost
                            |
                            | Jika rumus existing kamu berbeda, bagian ini
                            | harus diganti dengan rumus existing tersebut.
                            |
                            */

                            $totalCost =
                                1.2 *
                                $pricePerMeter +
                                $productionCost +
                                $finishingCost;

                            /*
                            |--------------------------------------------------------------------------
                            | General Price
                            |--------------------------------------------------------------------------
                            */

                            $generalMarkup =
                                ProductionCostFinishingMarkup::general(
                                    $engines->get($engineId)->name,
                                    $locations->get($locationId)->name,
                                    $material->category_name,
                                    $material->material_name,
                                    (float) $generalRule->markup_percentage
                                );

                            $generalRounding =
                                $generalRule->rounding_value;

                            $generalPrice =
                                (($generalMarkup / 100) * $totalCost) + $totalCost;

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

                            $divisionMarkup =
                                (float) $divisionRule->markup_percentage;

                            $divisionRounding =
                                $divisionRule->rounding_value;

                            $divisionPrice =
                                $generalPrice * ($divisionMarkup / 100);

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

                            if ($plainRule) {

                                $plainMarkup =
                                    (float) $plainRule->markup_percentage;

                                $plainRounding =
                                    $plainRule->rounding_value;

                                $plainPrice = (($plainMarkup / 100) * $pricePerMeter) + $pricePerMeter;

                                $plainPrice =
                                    $roundUp(
                                        $plainPrice,
                                        $plainRounding
                                    );

                                $plainPricingRuleId =
                                    $plainRule->id;
                            } else {

                                $plainPrice = 0;
                                $plainPricingRuleId = null;
                            }

                            $generalPricingRuleId =
                                $generalRule->id;

                            $divisionPricingRuleId =
                                $divisionRule->id;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Normalize Width
                        |--------------------------------------------------------------------------
                        */

                        $widthIds =
                            array_map(
                                'intval',
                                $item['width_ids']
                            );

                        /*
                        |--------------------------------------------------------------------------
                        | Add Normalized Row
                        |--------------------------------------------------------------------------
                        */

                        $normalizedRows[] = [
                            'configuration_index' =>
                            $configurationIndex,

                            'engine_id' =>
                            $engineId,

                            'location_id' =>
                            $locationId,

                            'vendor_id' =>
                            $vendorIdForRow,

                            'material_id' =>
                            $materialId,

                            'category_id' =>
                            $categoryId,

                            'width_ids' =>
                            $widthIds,

                            'price_per_meter' =>
                            $pricePerMeter,

                            'production_cost' =>
                            $productionCost,

                            'finishing_cost' =>
                            $finishingCost,

                            'vendor_price' =>
                            $vendorPrice,

                            'total_cost' =>
                            $totalCost,

                            'general_price' =>
                            $generalPrice,

                            'division_price' =>
                            $divisionPrice,

                            'plain_price' =>
                            $plainPrice,

                            'general_pricing_rule_id' =>
                            $generalPricingRuleId,

                            'division_pricing_rule_id' =>
                            $divisionPricingRuleId,

                            'plain_pricing_rule_id' =>
                            $plainPricingRuleId,
                        ];
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Duplicate Check
            |--------------------------------------------------------------------------
            */

            foreach ($normalizedRows as $row) {

                foreach ($row['width_ids'] as $widthId) {

                    $query = DB::table('m_production_costs as pc')
                        ->join(
                            'm_production_cost_locations as pcl',
                            'pc.id',
                            '=',
                            'pcl.production_cost_id'
                        )
                        ->join(
                            'm_production_cost_details as pcd',
                            'pc.id',
                            '=',
                            'pcd.production_cost_id'
                        )
                        ->join(
                            'm_production_cost_detail_widths as pcdw',
                            'pcd.id',
                            '=',
                            'pcdw.production_cost_detail_id'
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
                            'pcd.material_id',
                            $row['material_id']
                        )
                        ->where(
                            'pcdw.material_size_id',
                            $widthId
                        );

                    /*
                |--------------------------------------------------------------------------
                | Duplicate Vendor
                |--------------------------------------------------------------------------
                */

                    if ($row['vendor_id'] === null) {

                        $query->whereNull(
                            'pcd.vendor_id'
                        );
                    } else {

                        $query->where(
                            'pcd.vendor_id',
                            $row['vendor_id']
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Duplicate Cost
                    |--------------------------------------------------------------------------
                    */

                    if ($row['vendor_id'] === null) {

                        $query
                            ->where(
                                'pcd.price_per_meter',
                                $row['price_per_meter']
                            )
                            ->where(
                                'pcd.production_cost',
                                $row['production_cost']
                            )
                            ->where(
                                'pcd.finishing_cost',
                                $row['finishing_cost']
                            );
                    } else {

                        $query->where(
                            'pcd.vendor_price',
                            $row['vendor_price']
                        );
                    }

                    $exists = $query->exists();

                    if ($exists) {

                        throw ValidationException::withMessages([
                            'configurations' =>
                            'Data Production Cost dengan Engine, Lokasi, Vendor, Material, Lebar, dan biaya yang sama sudah tersedia.'
                        ]);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Group Data
            |--------------------------------------------------------------------------
            |
            | Satu konfigurasi + satu lokasi
            | menjadi satu header Production Cost.
            |
            */

            $groupedRows = collect($normalizedRows)
                ->groupBy(function ($row) {
                    return $row['configuration_index']
                        . '|'
                        . $row['location_id'];
                });

            /*
            |--------------------------------------------------------------------------
            | Insert Production Cost
            |--------------------------------------------------------------------------
            */

            foreach ($groupedRows as $group) {

                $firstRow = $group->first();

                /*
                |--------------------------------------------------------------------------
                | Header
                |--------------------------------------------------------------------------
                */

                $productionCostId =
                    DB::table('m_production_costs')
                    ->insertGetId([
                        'engine_id' =>
                        $firstRow['engine_id'],

                        'created_by' =>
                        Auth::user()->name,

                        'created_at' =>
                        now(),

                        'updated_by' =>
                        Auth::user()->name,

                        'updated_at' =>
                        now(),
                    ]);

                /*
                |--------------------------------------------------------------------------
                | Location
                |--------------------------------------------------------------------------
                */

                DB::table('m_production_cost_locations')
                    ->insert([
                        'production_cost_id' =>
                        $productionCostId,

                        'location_id' =>
                        $firstRow['location_id'],

                        'created_by' =>
                        Auth::user()->name,

                        'created_at' =>
                        now(),

                        'updated_by' =>
                        Auth::user()->name,

                        'updated_at' =>
                        now(),
                    ]);

                /*
                |--------------------------------------------------------------------------
                | Group by Material
                |--------------------------------------------------------------------------
                */

                $materialGroups =
                    $group->groupBy('material_id');

                foreach ($materialGroups as $materialRows) {

                    $materialRow =
                        $materialRows->first();

                    /*
                    |--------------------------------------------------------------------------
                    | Detail
                    |--------------------------------------------------------------------------
                    */

                    $detailId =
                        DB::table('m_production_cost_details')
                        ->insertGetId([
                            'production_cost_id' =>
                            $productionCostId,

                            'material_id' =>
                            $materialRow['material_id'],

                            'general_pricing_rule_id' =>
                            $materialRow['general_pricing_rule_id'],

                            'division_pricing_rule_id' =>
                            $materialRow['division_pricing_rule_id'],

                            'plain_pricing_rule_id' =>
                            $materialRow['plain_pricing_rule_id'],

                            'production_cost' =>
                            $materialRow['production_cost'],

                            'finishing_cost' =>
                            $materialRow['finishing_cost'],

                            'total_cost' =>
                            $materialRow['total_cost'],

                            'general_price' =>
                            $materialRow['general_price'],

                            'division_price' =>
                            $materialRow['division_price'],

                            'plain_price' =>
                            $materialRow['plain_price'],

                            'price_per_meter' =>
                            $materialRow['price_per_meter'],

                            /*
                            |--------------------------------------------------------------------------
                            | Outsourcing
                            |--------------------------------------------------------------------------
                            */

                            'vendor_price' =>
                            $materialRow['vendor_price'],

                            'vendor_id' =>
                            $materialRow['vendor_id'],

                            'created_by' =>
                            Auth::user()->name,

                            'created_at' =>
                            now(),

                            'updated_by' =>
                            Auth::user()->name,

                            'updated_at' =>
                            now(),
                        ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Material Widths
                    |--------------------------------------------------------------------------
                    */

                    foreach ($materialRows as $materialRow) {

                        foreach (
                            $materialRow['width_ids']
                            as $widthId
                        ) {

                            DB::table(
                                'm_production_cost_detail_widths'
                            )->insert([
                                'production_cost_detail_id' =>
                                $detailId,

                                'material_size_id' =>
                                $widthId,

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
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Commit
            |--------------------------------------------------------------------------
            */

            DB::commit();

            /*
            |--------------------------------------------------------------------------
            | Success Response
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'success' => true,
                'message' =>
                'Production Cost berhasil disimpan.'
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

    private function hasDigitalPrintConfiguration(Request $request)
    {
        $configurations = $request->input('configurations');

        if (!is_array($configurations)) {
            return false;
        }

        $engineIds = collect($configurations)
            ->map(function ($configuration) {
                return is_array($configuration) ? ($configuration['engine_id'] ?? null) : null;
            })
            ->filter(function ($engineId) {
                return is_numeric($engineId);
            })
            ->map(function ($engineId) {
                return (int) $engineId;
            })
            ->unique()
            ->values();

        if ($engineIds->isEmpty()) {
            return false;
        }

        return DB::table('m_engines')
            ->whereIn('id', $engineIds)
            ->whereRaw('LOWER(TRIM(name)) = ?', ['digital print a3+'])
            ->exists();
    }

    private function storeDigital(Request $request)
    {
        $nonDigitalEngineExists = DB::table('m_engines')
            ->whereIn('id', collect($request->input('configurations'))
                ->pluck('engine_id')
                ->filter(function ($engineId) {
                    return is_numeric($engineId);
                })
                ->map(function ($engineId) {
                    return (int) $engineId;
                })
                ->unique()
                ->values())
            ->whereRaw('LOWER(TRIM(name)) != ?', ['digital print a3+'])
            ->exists();

        if ($nonDigitalEngineExists) {
            return response()->json([
                'success' => false,
                'type' => 'warning',
                'message' => 'Konfigurasi Digital Print A3+ harus disimpan terpisah dari konfigurasi mesin lain.'
            ], 422);
        }

        $request->validate([
            'configurations' => 'required|array|min:1',
            'configurations.*.engine_id' => 'required|integer|exists:m_engines,id',
            'configurations.*.location_ids' => 'required|array|min:1',
            'configurations.*.location_ids.*' => 'required|integer|exists:m_locations,id',
            'configurations.*.vendor_id' => 'nullable|integer|exists:m_vendors,id',
            'configurations.*.items' => 'required|array|min:1',
            'configurations.*.items.*.material_id' => 'required|integer|exists:m_materials,id',
            'configurations.*.items.*.unit_price' => 'required|string|max:20',
            'configurations.*.items.*.click_price' => 'required|string|max:20',
        ], [
            'configurations.*.items.*.unit_price.required' => 'Harga Satuan wajib diisi.',
            'configurations.*.items.*.click_price.required' => 'Harga Klik wajib diisi.',
        ]);

        $parseRupiah = function ($value) {
            $digits = preg_replace('/\D/', '', (string) $value);

            return $digits === '' ? null : (float) $digits;
        };

        // Dibulatkan ke 6 desimal dahulu untuk menghindari error floating point sebelum ceil.
        $ceilToInteger = function ($value) {
            return ceil(round($value, 6));
        };

        $roundUpToMultiple = function ($value, $rounding) {
            if ($rounding === null || (float) $rounding <= 0) {
                return $value;
            }

            $rounding = (float) $rounding;

            return ceil(round($value / $rounding, 6)) * $rounding;
        };

        $priceTypes = ['general', 'division'];
        $printTypes = ['single', 'double'];
        $quantityTiers = ['tier_1', 'tier_2_5', 'tier_6_50', 'tier_100', 'tier_300', 'tier_500'];

        $engines = DB::table('m_engines')->get()->keyBy('id');
        $locations = DB::table('m_locations')->get()->keyBy('id');
        $vendors = DB::table('m_vendors')->where('status', 'Active')->get()->keyBy('id');
        $materials = DB::table('m_materials as m')
            ->join('m_categories as c', 'm.category_id', '=', 'c.id')
            ->select('m.id', 'm.category_id', 'm.material_name', 'c.name as category_name')
            ->get()
            ->keyBy('id');

        $userName = Auth::user()->name;
        $headers = [];
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

            $locationIds = array_map('intval', $configuration['location_ids']);

            if (count($locationIds) !== count(array_unique($locationIds))) {
                throw ValidationException::withMessages([
                    $prefix . '.location_ids' => 'Lokasi tidak boleh dipilih lebih dari satu kali.'
                ]);
            }

            $isOutsourcing = collect($locationIds)->contains(function ($locationId) use ($locations) {
                return strtolower(trim($locations->get($locationId)->name)) === 'outsourcing';
            });

            if ($isOutsourcing && count($locationIds) > 1) {
                throw ValidationException::withMessages([
                    $prefix . '.location_ids' =>
                    'Lokasi Outsourcing tidak dapat digabung dengan lokasi lain.'
                ]);
            }

            $vendorId = null;

            if ($isOutsourcing) {
                $vendorId = $configuration['vendor_id'] ?? null;

                if ($vendorId === null || $vendorId === '') {
                    throw ValidationException::withMessages([
                        $prefix . '.vendor_id' =>
                        'Master Vendor wajib dipilih untuk lokasi Outsourcing.'
                    ]);
                }

                $vendorId = (int) $vendorId;

                if (!$vendors->has($vendorId)) {
                    throw ValidationException::withMessages([
                        $prefix . '.vendor_id' =>
                        'Master Vendor tidak ditemukan atau tidak aktif.'
                    ]);
                }
            }

            foreach ($locationIds as $locationId) {
                $location = $locations->get($locationId);
                $vendorName = $vendorId ? ' - ' . $vendors->get($vendorId)->name : '';
                $scopeLabel = $location->name . $vendorName;
                $headerKey = $engineId . '|' . $locationId . '|' . ($vendorId ?? 'null');

                if (!isset($headers[$headerKey])) {
                    $headers[$headerKey] = [
                        'engine_id' => $engineId,
                        'location_id' => $locationId,
                        'vendor_id' => $vendorId,
                        'details' => [],
                    ];
                }

                foreach ($configuration['items'] as $itemIndex => $item) {
                    $itemPrefix = $prefix . '.items.' . $itemIndex;
                    $material = $materials->get((int) $item['material_id']);

                    if (!$material) {
                        throw ValidationException::withMessages([
                            $itemPrefix . '.material_id' => 'Material tidak ditemukan.'
                        ]);
                    }

                    $materialKey = $headerKey . '|' . $material->id;

                    if (isset($usedKeys[$materialKey])) {
                        throw ValidationException::withMessages([
                            $itemPrefix . '.material_id' =>
                            'Material "' . $material->material_name . '" dipilih lebih dari satu kali untuk lokasi ' .
                                $scopeLabel . '.'
                        ]);
                    }

                    $usedKeys[$materialKey] = true;

                    $paperCost = $parseRupiah($item['unit_price']);
                    $clickCost = $parseRupiah($item['click_price']);

                    if ($paperCost === null || $paperCost <= 0) {
                        throw ValidationException::withMessages([
                            $itemPrefix . '.unit_price' =>
                            'Harga Satuan material "' . $material->material_name . '" harus berupa angka lebih dari 0.'
                        ]);
                    }

                    if ($clickCost === null || $clickCost <= 0) {
                        throw ValidationException::withMessages([
                            $itemPrefix . '.click_price' =>
                            'Harga Klik material "' . $material->material_name . '" harus berupa angka lebih dari 0.'
                        ]);
                    }

                    if ($paperCost > 9999999999.99 || $clickCost > 9999999999.99) {
                        throw ValidationException::withMessages([
                            $itemPrefix . '.unit_price' =>
                            'Harga material "' . $material->material_name . '" melebihi batas maksimum.'
                        ]);
                    }

                    $existingDetail = DB::table('m_digital_production_cost_details as d')
                        ->join('m_digital_production_costs as h', 'd.digital_production_cost_id', '=', 'h.id')
                        ->where('h.engine_id', $engineId)
                        ->where('h.location_id', $locationId)
                        ->where(function ($query) use ($vendorId) {
                            if ($vendorId === null) {
                                $query->whereNull('h.vendor_id');
                            } else {
                                $query->where('h.vendor_id', $vendorId);
                            }
                        })
                        ->where('d.material_id', $material->id)
                        ->exists();

                    if ($existingDetail) {
                        throw ValidationException::withMessages([
                            $itemPrefix . '.material_id' =>
                            'Ongkos produksi material "' . $material->material_name . '" untuk lokasi ' .
                                $scopeLabel . ' sudah ada.'
                        ]);
                    }

                    $pricingRuleDetails = DB::table('m_digital_pricing_rules as r')
                        ->join('m_digital_pricing_rule_details as d', 'd.digital_pricing_rule_id', '=', 'r.id')
                        ->where('r.engine_id', $engineId)
                        ->where('r.location_id', $locationId)
                        ->where('r.category_id', $material->category_id)
                        ->where(function ($query) use ($vendorId) {
                            if ($vendorId === null) {
                                $query->whereNull('r.vendor_id');
                            } else {
                                $query->where('r.vendor_id', $vendorId);
                            }
                        })
                        ->where('d.status', 'Active')
                        ->select(
                            'd.id',
                            'd.price_type',
                            'd.print_type',
                            'd.quantity_tier',
                            'd.minimum_quantity',
                            'd.maximum_quantity',
                            'd.markup_percentage',
                            'd.rounding_value'
                        )
                        ->get()
                        ->keyBy(function ($detail) {
                            return $detail->price_type . '|' . $detail->print_type . '|' . $detail->quantity_tier;
                        });

                    // Cek apakah ada rule double (2 muka) pada pricing rule detail
                    $hasDoubleRule = $pricingRuleDetails->contains(function ($detail) {
                        return $detail->print_type === 'double';
                    });

                    // Bahan 1 muka (seperti Sticker) atau yang memang tidak memiliki rule double
                    $isSingleSideOnly = (stripos($material->category_name, 'sticker') !== false) || !$hasDoubleRule;

                    $applicablePrintTypes = $isSingleSideOnly ? ['single'] : ['single', 'double'];
                    $expectedTiers = count($priceTypes) * count($applicablePrintTypes) * count($quantityTiers); // 12 atau 24

                    $missingRules = [];

                    foreach ($priceTypes as $priceType) {
                        foreach ($applicablePrintTypes as $printType) {
                            foreach ($quantityTiers as $quantityTier) {
                                if (!$pricingRuleDetails->has($priceType . '|' . $printType . '|' . $quantityTier)) {
                                    $missingRules[] = $priceType . ' ' . $printType . ' ' . $quantityTier;
                                }
                            }
                        }
                    }

                    if (!empty($missingRules)) {
                        throw ValidationException::withMessages([
                            $itemPrefix . '.material_id' =>
                            'Pricing rule Digital Print A3+ untuk kategori "' . $material->category_name .
                                '" di lokasi ' . $scopeLabel . ' belum ada atau belum lengkap (' .
                                ($expectedTiers - count($missingRules)) . '/' . $expectedTiers . ' tier aktif).'
                        ]);
                    }

                    $singlePrintCost = $paperCost + $clickCost;
                    $doublePrintCost = $isSingleSideOnly ? 0.0 : ($clickCost + $singlePrintCost);
                    $costs = [
                        'single' => $singlePrintCost,
                        'double' => $doublePrintCost,
                    ];

                    $prices = [];

                    foreach ($applicablePrintTypes as $printType) {
                        foreach ($quantityTiers as $quantityTier) {
                            $generalRule = $pricingRuleDetails->get('general|' . $printType . '|' . $quantityTier);
                            $divisionRule = $pricingRuleDetails->get('division|' . $printType . '|' . $quantityTier);
                            $cost = $costs[$printType];

                            $generalPrice = $roundUpToMultiple(
                                $ceilToInteger(((float) $generalRule->markup_percentage / 100) * $cost) + $cost,
                                $generalRule->rounding_value
                            );

                            $divisionPrice = $roundUpToMultiple(
                                round($generalPrice * ((float) $divisionRule->markup_percentage / 100), 2),
                                $divisionRule->rounding_value
                            );

                            $prices[] = [$generalRule, $generalPrice];
                            $prices[] = [$divisionRule, $divisionPrice];
                        }
                    }

                    $headers[$headerKey]['details'][] = [
                        'material_id' => $material->id,
                        'paper_cost' => $paperCost,
                        'click_cost' => $clickCost,
                        'single_print_cost' => $singlePrintCost,
                        'double_print_cost' => $doublePrintCost,
                        'prices' => $prices,
                    ];
                }
            }
        }

        DB::beginTransaction();

        try {
            foreach ($headers as $header) {
                if (empty($header['details'])) {
                    continue;
                }

                $productionCost = DB::table('m_digital_production_costs')
                    ->where('engine_id', $header['engine_id'])
                    ->where('location_id', $header['location_id'])
                    ->where(function ($query) use ($header) {
                        if ($header['vendor_id'] === null) {
                            $query->whereNull('vendor_id');
                        } else {
                            $query->where('vendor_id', $header['vendor_id']);
                        }
                    })
                    ->lockForUpdate()
                    ->first();

                if ($productionCost) {
                    $productionCostId = $productionCost->id;

                    DB::table('m_digital_production_costs')
                        ->where('id', $productionCostId)
                        ->update([
                            'updated_by' => $userName,
                            'updated_at' => now(),
                        ]);
                } else {
                    $productionCostId = DB::table('m_digital_production_costs')->insertGetId([
                        'engine_id' => $header['engine_id'],
                        'location_id' => $header['location_id'],
                        'vendor_id' => $header['vendor_id'],
                        'created_by' => $userName,
                        'created_at' => now(),
                        'updated_by' => $userName,
                        'updated_at' => now(),
                    ]);
                }

                foreach ($header['details'] as $detail) {
                    $detailId = DB::table('m_digital_production_cost_details')->insertGetId([
                        'digital_production_cost_id' => $productionCostId,
                        'material_id' => $detail['material_id'],
                        'paper_cost' => $detail['paper_cost'],
                        'click_cost' => $detail['click_cost'],
                        'single_print_cost' => $detail['single_print_cost'],
                        'double_print_cost' => $detail['double_print_cost'],
                        'created_by' => $userName,
                        'created_at' => now(),
                        'updated_by' => $userName,
                        'updated_at' => now(),
                    ]);

                    $priceRows = [];

                    foreach ($detail['prices'] as [$rule, $sellingPrice]) {
                        $priceRows[] = [
                            'digital_production_cost_detail_id' => $detailId,
                            'digital_pricing_rule_detail_id' => $rule->id,
                            'price_type' => $rule->price_type,
                            'print_type' => $rule->print_type,
                            'quantity_tier' => $rule->quantity_tier,
                            'minimum_quantity' => $rule->minimum_quantity,
                            'maximum_quantity' => $rule->maximum_quantity,
                            'markup_percentage' => $rule->markup_percentage,
                            'rounding_value' => $rule->rounding_value,
                            'selling_price' => $sellingPrice,
                            'created_by' => $userName,
                            'created_at' => now(),
                            'updated_by' => $userName,
                            'updated_at' => now(),
                        ];
                    }

                    DB::table('m_digital_production_cost_prices')->insert($priceRows);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Production Cost Digital Print A3+ berhasil disimpan.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function findDigitalProductionCost($id)
    {
        $productionCostId = base64_decode($id, true);

        if ($productionCostId === false || !ctype_digit($productionCostId)) {
            abort(404);
        }

        $productionCost = DB::table('m_digital_production_costs as pc')
            ->join('m_engines as e', 'e.id', '=', 'pc.engine_id')
            ->join('m_locations as l', 'l.id', '=', 'pc.location_id')
            ->leftJoin('m_vendors as v', 'v.id', '=', 'pc.vendor_id')
            ->where('pc.id', (int) $productionCostId)
            ->whereRaw('LOWER(TRIM(e.name)) = ?', ['digital print a3+'])
            ->select(
                'pc.id',
                'pc.engine_id',
                'pc.location_id',
                'pc.vendor_id',
                'e.name as engine_name',
                'l.name as location_name',
                'v.name as vendor_name'
            )
            ->first();

        if (!$productionCost) {
            abort(404);
        }

        return $productionCost;
    }

    public function editDigital($id)
    {
        $productionCost = $this->findDigitalProductionCost($id);

        $details = DB::table('m_digital_production_cost_details as d')
            ->join('m_materials as m', 'm.id', '=', 'd.material_id')
            ->join('m_categories as c', 'c.id', '=', 'm.category_id')
            ->where('d.digital_production_cost_id', $productionCost->id)
            ->select(
                'd.material_id',
                'd.paper_cost',
                'd.click_cost'
            )
            ->orderBy('c.name')
            ->orderBy('m.material_name')
            ->get();

        $savedMaterialIds = $details->pluck('material_id')->all();

        // Material tersimpan tetap ditampilkan walaupun sudah nonaktif agar pilihan lama tidak hilang.
        $data['materials'] = DB::table('m_materials as m')
            ->join('m_categories as c', 'c.id', '=', 'm.category_id')
            ->whereExists(function ($query) use ($productionCost) {
                $query->selectRaw('1')
                    ->from('m_digital_pricing_rules as pr')
                    ->whereColumn('pr.category_id', 'm.category_id')
                    ->where('pr.engine_id', $productionCost->engine_id);
            })
            ->where(function ($query) use ($savedMaterialIds) {
                $query->where('m.status', 'Active');

                if (!empty($savedMaterialIds)) {
                    $query->orWhereIn('m.id', $savedMaterialIds);
                }
            })
            ->select('m.id', 'm.material_name', 'c.name as category_name')
            ->orderBy('m.material_name')
            ->get();

        $data['productionCost'] = $productionCost;
        $data['details'] = $details->map(function ($detail) {
            return [
                'material_id' => (string) $detail->material_id,
                'unit_price' => number_format((float) $detail->paper_cost, 0, ',', '.'),
                'click_price' => number_format((float) $detail->click_cost, 0, ',', '.'),
            ];
        })->values();

        return view('admin.production-cost-finishing.edit-digital')->with($data);
    }

    public function updateDigital(Request $request, $id)
    {
        $productionCost = $this->findDigitalProductionCost($id);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|integer|exists:m_materials,id',
            'items.*.unit_price' => 'required|string|max:20',
            'items.*.click_price' => 'required|string|max:20',
        ], [
            'items.required' => 'Minimal harus ada 1 material.',
            'items.min' => 'Minimal harus ada 1 material.',
            'items.*.material_id.required' => 'Material wajib dipilih.',
            'items.*.unit_price.required' => 'Harga Satuan wajib diisi.',
            'items.*.click_price.required' => 'Harga Klik wajib diisi.',
        ]);

        $parseRupiah = function ($value) {
            $digits = preg_replace('/\D/', '', (string) $value);

            return $digits === '' ? null : (float) $digits;
        };

        // Dibulatkan ke 6 desimal dahulu untuk menghindari error floating point sebelum ceil.
        $ceilToInteger = function ($value) {
            return ceil(round($value, 6));
        };

        $roundUpToMultiple = function ($value, $rounding) {
            if ($rounding === null || (float) $rounding <= 0) {
                return $value;
            }

            $rounding = (float) $rounding;

            return ceil(round($value / $rounding, 6)) * $rounding;
        };

        $priceTypes = ['general', 'division'];
        $printTypes = ['single', 'double'];
        $quantityTiers = ['tier_1', 'tier_2_5', 'tier_6_50', 'tier_100', 'tier_300', 'tier_500'];

        $scopeLabel = $productionCost->location_name .
            ($productionCost->vendor_name ? ' - ' . $productionCost->vendor_name : '');

        $savedMaterialIds = DB::table('m_digital_production_cost_details')
            ->where('digital_production_cost_id', $productionCost->id)
            ->pluck('material_id')
            ->map(function ($materialId) {
                return (int) $materialId;
            })
            ->all();

        $materials = DB::table('m_materials as m')
            ->join('m_categories as c', 'm.category_id', '=', 'c.id')
            ->select('m.id', 'm.category_id', 'm.material_name', 'm.status', 'c.name as category_name')
            ->get()
            ->keyBy('id');

        $details = [];
        $usedMaterialIds = [];

        foreach ($request->input('items') as $itemIndex => $item) {
            $itemPrefix = 'items.' . $itemIndex;
            $material = $materials->get((int) $item['material_id']);

            if (!$material) {
                throw ValidationException::withMessages([
                    $itemPrefix . '.material_id' => 'Material tidak ditemukan.'
                ]);
            }

            if ($material->status !== 'Active' && !in_array($material->id, $savedMaterialIds, true)) {
                throw ValidationException::withMessages([
                    $itemPrefix . '.material_id' =>
                    'Material "' . $material->material_name . '" sudah tidak aktif.'
                ]);
            }

            if (isset($usedMaterialIds[$material->id])) {
                throw ValidationException::withMessages([
                    $itemPrefix . '.material_id' =>
                    'Material "' . $material->material_name . '" dipilih lebih dari satu kali untuk lokasi ' .
                        $scopeLabel . '.'
                ]);
            }

            $usedMaterialIds[$material->id] = true;

            $paperCost = $parseRupiah($item['unit_price']);
            $clickCost = $parseRupiah($item['click_price']);

            if ($paperCost === null || $paperCost <= 0) {
                throw ValidationException::withMessages([
                    $itemPrefix . '.unit_price' =>
                    'Harga Satuan material "' . $material->material_name . '" harus berupa angka lebih dari 0.'
                ]);
            }

            if ($clickCost === null || $clickCost <= 0) {
                throw ValidationException::withMessages([
                    $itemPrefix . '.click_price' =>
                    'Harga Klik material "' . $material->material_name . '" harus berupa angka lebih dari 0.'
                ]);
            }

            if ($paperCost > 9999999999.99 || $clickCost > 9999999999.99) {
                throw ValidationException::withMessages([
                    $itemPrefix . '.unit_price' =>
                    'Harga material "' . $material->material_name . '" melebihi batas maksimum.'
                ]);
            }

            $pricingRuleDetails = DB::table('m_digital_pricing_rules as r')
                ->join('m_digital_pricing_rule_details as d', 'd.digital_pricing_rule_id', '=', 'r.id')
                ->where('r.engine_id', $productionCost->engine_id)
                ->where('r.location_id', $productionCost->location_id)
                ->where('r.category_id', $material->category_id)
                ->where(function ($query) use ($productionCost) {
                    if ($productionCost->vendor_id === null) {
                        $query->whereNull('r.vendor_id');
                    } else {
                        $query->where('r.vendor_id', $productionCost->vendor_id);
                    }
                })
                ->where('d.status', 'Active')
                ->select(
                    'd.id',
                    'd.price_type',
                    'd.print_type',
                    'd.quantity_tier',
                    'd.minimum_quantity',
                    'd.maximum_quantity',
                    'd.markup_percentage',
                    'd.rounding_value'
                )
                ->get()
                ->keyBy(function ($detail) {
                    return $detail->price_type . '|' . $detail->print_type . '|' . $detail->quantity_tier;
                });

            // Cek apakah ada rule double (2 muka) pada pricing rule detail
            $hasDoubleRule = $pricingRuleDetails->contains(function ($detail) {
                return $detail->print_type === 'double';
            });

            // Bahan 1 muka (seperti Sticker) atau yang memang tidak memiliki rule double
            $isSingleSideOnly = (stripos($material->category_name, 'sticker') !== false) || !$hasDoubleRule;

            $applicablePrintTypes = $isSingleSideOnly ? ['single'] : ['single', 'double'];
            $expectedTiers = count($priceTypes) * count($applicablePrintTypes) * count($quantityTiers); // 12 atau 24

            $missingRuleCount = 0;

            foreach ($priceTypes as $priceType) {
                foreach ($applicablePrintTypes as $printType) {
                    foreach ($quantityTiers as $quantityTier) {
                        if (!$pricingRuleDetails->has($priceType . '|' . $printType . '|' . $quantityTier)) {
                            $missingRuleCount++;
                        }
                    }
                }
            }

            if ($missingRuleCount > 0) {
                throw ValidationException::withMessages([
                    $itemPrefix . '.material_id' =>
                    'Pricing rule Digital Print A3+ untuk kategori "' . $material->category_name .
                        '" di lokasi ' . $scopeLabel . ' belum ada atau belum lengkap (' .
                        ($expectedTiers - $missingRuleCount) . '/' . $expectedTiers . ' tier aktif).'
                ]);
            }

            $singlePrintCost = $paperCost + $clickCost;
            $doublePrintCost = $isSingleSideOnly ? 0.0 : ($clickCost + $singlePrintCost);
            $costs = [
                'single' => $singlePrintCost,
                'double' => $doublePrintCost,
            ];

            $prices = [];

            foreach ($applicablePrintTypes as $printType) {
                foreach ($quantityTiers as $quantityTier) {
                    $generalRule = $pricingRuleDetails->get('general|' . $printType . '|' . $quantityTier);
                    $divisionRule = $pricingRuleDetails->get('division|' . $printType . '|' . $quantityTier);
                    $cost = $costs[$printType];

                    $generalPrice = $roundUpToMultiple(
                        $ceilToInteger(((float) $generalRule->markup_percentage / 100) * $cost) + $cost,
                        $generalRule->rounding_value
                    );

                    $divisionPrice = $roundUpToMultiple(
                        round($generalPrice * ((float) $divisionRule->markup_percentage / 100), 2),
                        $divisionRule->rounding_value
                    );

                    $prices[] = [$generalRule, $generalPrice];
                    $prices[] = [$divisionRule, $divisionPrice];
                }
            }

            $details[] = [
                'material_id' => $material->id,
                'paper_cost' => $paperCost,
                'click_cost' => $clickCost,
                'single_print_cost' => $singlePrintCost,
                'double_print_cost' => $doublePrintCost,
                'prices' => $prices,
            ];
        }

        $userName = Auth::user()->name;

        DB::beginTransaction();

        try {
            DB::table('m_digital_production_costs')
                ->where('id', $productionCost->id)
                ->lockForUpdate()
                ->first();

            // Data pembuat material lama dipertahankan saat detail disusun ulang.
            $existingDetails = DB::table('m_digital_production_cost_details')
                ->where('digital_production_cost_id', $productionCost->id)
                ->get(['material_id', 'created_by', 'created_at'])
                ->keyBy('material_id');

            // Harga per tier ikut terhapus melalui foreign key cascade.
            DB::table('m_digital_production_cost_details')
                ->where('digital_production_cost_id', $productionCost->id)
                ->delete();

            foreach ($details as $detail) {
                $existingDetail = $existingDetails->get($detail['material_id']);

                $detailId = DB::table('m_digital_production_cost_details')->insertGetId([
                    'digital_production_cost_id' => $productionCost->id,
                    'material_id' => $detail['material_id'],
                    'paper_cost' => $detail['paper_cost'],
                    'click_cost' => $detail['click_cost'],
                    'single_print_cost' => $detail['single_print_cost'],
                    'double_print_cost' => $detail['double_print_cost'],
                    'created_by' => $existingDetail ? $existingDetail->created_by : $userName,
                    'created_at' => $existingDetail ? $existingDetail->created_at : now(),
                    'updated_by' => $userName,
                    'updated_at' => now(),
                ]);

                $priceRows = [];

                foreach ($detail['prices'] as [$rule, $sellingPrice]) {
                    $priceRows[] = [
                        'digital_production_cost_detail_id' => $detailId,
                        'digital_pricing_rule_detail_id' => $rule->id,
                        'price_type' => $rule->price_type,
                        'print_type' => $rule->print_type,
                        'quantity_tier' => $rule->quantity_tier,
                        'minimum_quantity' => $rule->minimum_quantity,
                        'maximum_quantity' => $rule->maximum_quantity,
                        'markup_percentage' => $rule->markup_percentage,
                        'rounding_value' => $rule->rounding_value,
                        'selling_price' => $sellingPrice,
                        'created_by' => $userName,
                        'created_at' => now(),
                        'updated_by' => $userName,
                        'updated_at' => now(),
                    ];
                }

                DB::table('m_digital_production_cost_prices')->insert($priceRows);
            }

            DB::table('m_digital_production_costs')
                ->where('id', $productionCost->id)
                ->update([
                    'updated_by' => $userName,
                    'updated_at' => now(),
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Production Cost Digital Print A3+ berhasil diperbarui.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function destroyDigital($id)
    {
        $productionCost = $this->findDigitalProductionCost($id);

        DB::beginTransaction();

        try {
            $lockedProductionCost = DB::table('m_digital_production_costs')
                ->where('id', $productionCost->id)
                ->where('engine_id', $productionCost->engine_id)
                ->lockForUpdate()
                ->first();

            if (!$lockedProductionCost) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Data production cost Digital Print A3+ tidak ditemukan.'
                ], 404);
            }

            // FK cascade hanya menghapus detail & harga tier milik header ini.
            $deleted = DB::table('m_digital_production_costs')
                ->where('id', $lockedProductionCost->id)
                ->delete();

            if ($deleted !== 1) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Production cost Digital Print A3+ gagal dihapus.'
                ], 409);
            }

            DB::commit();

            $scopeLabel = $productionCost->location_name .
                ($productionCost->vendor_name ? ' - ' . $productionCost->vendor_name : '');

            return response()->json([
                'success' => true,
                'message' => 'Production cost Digital Print A3+ lokasi ' . $scopeLabel . ' berhasil dihapus.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function edit($id)
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
    | AMBIL ENGINE YANG SEDANG DIEDIT
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
    | AMBIL MASTER ENGINE
    |--------------------------------------------------------------------------
    */

        $data['engines'] = DB::table('m_engines')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();


        /*
    |--------------------------------------------------------------------------
    | AMBIL MASTER LOCATIONS
    |--------------------------------------------------------------------------
    */

        $data['allLocations'] = DB::table('m_locations')
            ->orderBy('name')
            ->get();


        /*
    |--------------------------------------------------------------------------
    | AMBIL MASTER VENDORS
    |--------------------------------------------------------------------------
    */

        $data['vendors'] = DB::table('m_vendors')
            ->where('status', 'Active')
            ->orderBy('name')
            ->get();


        /*
    |--------------------------------------------------------------------------
    | AMBIL MASTER MATERIALS
    |--------------------------------------------------------------------------
    */

        $pricingRuleTable = strtolower(trim($data['engine']->name)) === 'digital print a3+'
            ? 'm_digital_pricing_rules'
            : 'm_pricing_rules';

        $data['materials'] = DB::table('m_materials as m')
            ->join(
                'm_categories as c',
                'c.id',
                '=',
                'm.category_id'
            )
            ->where('m.status', 'Active')
            ->whereExists(function ($query) use ($data, $pricingRuleTable) {
                $query->selectRaw('1')
                    ->from($pricingRuleTable . ' as pr')
                    ->whereColumn('pr.category_id', 'm.category_id')
                    ->where('pr.engine_id', $data['engineId']);
            })
            ->select(
                'm.id',
                'm.material_name',
                'm.category_id',
                'c.name as category_name'
            )
            ->orderBy('m.material_name')
            ->get();


        /*
    |--------------------------------------------------------------------------
    | AMBIL PRODUCTION COST
    |--------------------------------------------------------------------------
    */

        $data['productionCosts'] = DB::table(
            'm_production_costs as pc'
        )
            ->where(
                'pc.engine_id',
                $data['engineId']
            )
            ->get();

        if ($data['productionCosts']->isEmpty()) {
            abort(404);
        }


        /*
    |--------------------------------------------------------------------------
    | AMBIL PRODUCTION COST IDS
    |--------------------------------------------------------------------------
    */

        $data['productionCostIds'] = $data['productionCosts']
            ->pluck('id')
            ->toArray();


        /*
    |--------------------------------------------------------------------------
    | AMBIL LOCATIONS YANG SUDAH TERSIMPAN
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
                'pcl.id',
                'pcl.production_cost_id',
                'l.id as location_id',
                'l.name as location_name'
            )
            ->orderBy('l.name')
            ->get();


        /*
    |--------------------------------------------------------------------------
    | AMBIL MATERIAL DETAILS
    |--------------------------------------------------------------------------
    */

        $data['details'] = DB::table(
            'm_production_cost_details as pcd'
        )
            ->join(
                'm_materials as m',
                'm.id',
                '=',
                'pcd.material_id'
            )
            ->join(
                'm_categories as c',
                'c.id',
                '=',
                'm.category_id'
            )
            ->leftJoin(
                'm_vendors as v',
                'v.id',
                '=',
                'pcd.vendor_id'
            )
            ->whereIn(
                'pcd.production_cost_id',
                $data['productionCostIds']
            )
            ->select(
                'pcd.id',
                'pcd.production_cost_id',

                /*
            |--------------------------------------------------------------------------
            | CATEGORY
            |--------------------------------------------------------------------------
            */

                'c.id as category_id',
                'c.name as category_name',

                /*
            |--------------------------------------------------------------------------
            | MATERIAL
            |--------------------------------------------------------------------------
            */

                'pcd.material_id',
                'm.material_name',

                /*
            |--------------------------------------------------------------------------
            | VENDOR
            |--------------------------------------------------------------------------
            */

                'pcd.vendor_id',
                'v.name as vendor_name',
                'pcd.vendor_price',

                /*
            |--------------------------------------------------------------------------
            | COST
            |--------------------------------------------------------------------------
            */

                'pcd.price_per_meter',
                'pcd.production_cost',
                'pcd.finishing_cost',
                'pcd.total_cost',

                /*
            |--------------------------------------------------------------------------
            | PRICE
            |--------------------------------------------------------------------------
            */

                'pcd.general_price',
                'pcd.division_price',
                'pcd.plain_price'
            )
            ->orderBy('c.name')
            ->orderBy('m.material_name')
            ->get();


        /*
    |--------------------------------------------------------------------------
    | AMBIL DETAIL IDS
    |--------------------------------------------------------------------------
    */

        $data['detailIds'] = $data['details']
            ->pluck('id')
            ->toArray();


        /*
    |--------------------------------------------------------------------------
    | AMBIL WIDTH
    |--------------------------------------------------------------------------
    |
    | Width diambil SEBELUM grouping detail karena satu material
    | dapat berasal dari beberapa detail ID.
    |
    */

        $data['widths'] = collect();

        if (!empty($data['detailIds'])) {

            $data['widths'] = DB::table(
                'm_production_cost_detail_widths as pcdw'
            )
                ->join(
                    'm_material_sizes as ms',
                    'ms.id',
                    '=',
                    'pcdw.material_size_id'
                )
                ->whereIn(
                    'pcdw.production_cost_detail_id',
                    $data['detailIds']
                )
                ->select(
                    'pcdw.id',
                    'pcdw.production_cost_detail_id',
                    'pcdw.material_size_id',
                    'ms.width',
                    'ms.unit'
                )
                ->orderBy('ms.width')
                ->get();
        }


        /*
    |--------------------------------------------------------------------------
    | GROUPING LOKASI
    |--------------------------------------------------------------------------
    |
    | Satu location_id hanya menjadi satu location-block.
    |
    */

        $data['locations'] = $data['locations']
            ->groupBy('location_id')
            ->map(function ($locationRows) use ($data) {

                /*
            |--------------------------------------------------------------------------
            | AMBIL SATU DATA LOKASI SEBAGAI PARENT
            |--------------------------------------------------------------------------
            */

                $location = $locationRows->first();


                /*
            |--------------------------------------------------------------------------
            | AMBIL SEMUA PRODUCTION COST ID MILIK LOKASI
            |--------------------------------------------------------------------------
            */

                $productionCostIds = $locationRows
                    ->pluck('production_cost_id')
                    ->unique()
                    ->values();


                /*
            |--------------------------------------------------------------------------
            | AMBIL SEMUA DETAIL MATERIAL DALAM LOKASI
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
            | CEK OUTSOURCING
            |--------------------------------------------------------------------------
            */

                $isOutsourcing =
                    strtolower(trim($location->location_name))
                    === 'outsourcing';


                /*
            |--------------------------------------------------------------------------
            | OUTSOURCING
            |--------------------------------------------------------------------------
            |
            | Location
            |   └── Vendor
            |        └── Category
            |             └── Material
            |                  └── Width
            |
            */

                if ($isOutsourcing) {

                    $location->vendors = $locationDetails
                        ->groupBy('vendor_id')
                        ->map(function ($vendorRows) use ($data) {

                            $vendor = $vendorRows->first();


                            /*
                        |--------------------------------------------------------------------------
                        | CATEGORY
                        |--------------------------------------------------------------------------
                        */

                            $vendor->categories = $vendorRows
                                ->groupBy('category_id')
                                ->map(function ($categoryRows) use ($data) {

                                    $category = $categoryRows->first();


                                    /*
                                |--------------------------------------------------------------------------
                                | GROUP MATERIAL
                                |--------------------------------------------------------------------------
                                |
                                | Material dianggap sama apabila:
                                |
                                | - material_id sama
                                | - price_per_meter sama
                                | - production_cost sama
                                | - finishing_cost sama
                                | - total_cost sama
                                | - general_price sama
                                | - division_price sama
                                | - plain_price sama
                                |
                                | Width TIDAK menjadi pembeda.
                                |
                                */

                                    $category->details = $categoryRows
                                        ->groupBy(function ($detail) {

                                            return implode('|', [

                                                $detail->material_id,

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
                                        | REPRESENTATIVE DETAIL
                                        |--------------------------------------------------------------------------
                                        */

                                            $detail = $detailRows->first();


                                            /*
                                        |--------------------------------------------------------------------------
                                        | AMBIL SEMUA DETAIL ID
                                        |--------------------------------------------------------------------------
                                        */

                                            $detailIds = $detailRows
                                                ->pluck('id')
                                                ->unique()
                                                ->values()
                                                ->toArray();


                                            /*
                                        |--------------------------------------------------------------------------
                                        | GABUNG SEMUA WIDTH
                                        |--------------------------------------------------------------------------
                                        */

                                            $detail->widths = $data['widths']
                                                ->whereIn(
                                                    'production_cost_detail_id',
                                                    $detailIds
                                                )
                                                ->unique('material_size_id')
                                                ->sortBy(function ($width) {

                                                    return (float) $width->width;
                                                })
                                                ->values();


                                            /*
                                        |--------------------------------------------------------------------------
                                        | SIMPAN SEMUA DETAIL ID ASLI
                                        |--------------------------------------------------------------------------
                                        */

                                            $detail->detail_ids = $detailIds;


                                            return $detail;
                                        })
                                        ->sortBy('material_name')
                                        ->values();


                                    /*
                                |--------------------------------------------------------------------------
                                | CATEGORY DATA
                                |--------------------------------------------------------------------------
                                */

                                    $category->category_id =
                                        $categoryRows->first()->category_id;

                                    $category->category_name =
                                        $categoryRows->first()->category_name;


                                    return $category;
                                })
                                ->sortBy('category_name')
                                ->values();


                            /*
                        |--------------------------------------------------------------------------
                        | VENDOR DATA
                        |--------------------------------------------------------------------------
                        */

                            $vendor->vendor_id =
                                $vendorRows->first()->vendor_id;

                            $vendor->vendor_name =
                                $vendorRows->first()->vendor_name;

                            $vendor->vendor_price =
                                $vendorRows->first()->vendor_price;


                            return $vendor;
                        })
                        ->sortBy('vendor_name')
                        ->values();


                    return $location;
                }


                /*
            |--------------------------------------------------------------------------
            | NON-OUTSOURCING
            |--------------------------------------------------------------------------
            |
            | Location
            |   └── Category
            |        └── Material
            |             └── Width
            |
            */

                $location->categories = $locationDetails
                    ->groupBy('category_id')
                    ->map(function ($categoryRows) use ($data) {

                        $category = $categoryRows->first();


                        /*
                    |--------------------------------------------------------------------------
                    | GROUP MATERIAL
                    |--------------------------------------------------------------------------
                    */

                        $category->details = $categoryRows
                            ->groupBy(function ($detail) {

                                return implode('|', [

                                    $detail->material_id,

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
                            | REPRESENTATIVE DETAIL
                            |--------------------------------------------------------------------------
                            */

                                $detail = $detailRows->first();


                                /*
                            |--------------------------------------------------------------------------
                            | AMBIL SEMUA DETAIL ID
                            |--------------------------------------------------------------------------
                            */

                                $detailIds = $detailRows
                                    ->pluck('id')
                                    ->unique()
                                    ->values()
                                    ->toArray();


                                /*
                            |--------------------------------------------------------------------------
                            | GABUNG SEMUA WIDTH
                            |--------------------------------------------------------------------------
                            */

                                $detail->widths = $data['widths']
                                    ->whereIn(
                                        'production_cost_detail_id',
                                        $detailIds
                                    )
                                    ->unique('material_size_id')
                                    ->sortBy(function ($width) {

                                        return (float) $width->width;
                                    })
                                    ->values();


                                /*
                            |--------------------------------------------------------------------------
                            | SIMPAN SEMUA DETAIL ID ASLI
                            |--------------------------------------------------------------------------
                            */

                                $detail->detail_ids = $detailIds;


                                return $detail;
                            })
                            ->sortBy('material_name')
                            ->values();


                        /*
                    |--------------------------------------------------------------------------
                    | CATEGORY DATA
                    |--------------------------------------------------------------------------
                    */

                        $category->category_id =
                            $categoryRows->first()->category_id;

                        $category->category_name =
                            $categoryRows->first()->category_name;


                        return $category;
                    })
                    ->sortBy('category_name')
                    ->values();


                return $location;
            })
            ->values();


        /*
    |--------------------------------------------------------------------------
    | MASTER MATERIAL SIZES
    |--------------------------------------------------------------------------
    */

        $data['materialSizes'] = DB::table(
            'm_material_sizes'
        )
            ->orderBy('material_id')
            ->orderBy('width')
            ->get()
            ->groupBy('material_id');


        /*
    |--------------------------------------------------------------------------
    | RETURN VIEW
    |--------------------------------------------------------------------------
    */

        return view(
            'admin.production-cost-finishing.edit'
        )->with($data);
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            /*
    |--------------------------------------------------------------------------
    | Decode ID Production Cost
    |--------------------------------------------------------------------------
    */
            $engineId = base64_decode($id);

            if (!$engineId) {
                throw new \Exception('ID Production Cost tidak valid.');
            }

            /*
    |--------------------------------------------------------------------------
    | Validate Request
    |--------------------------------------------------------------------------
    |
    | Struktur request update tetap menggunakan:
    | locations[]
    |   - location_id
    |   - items[]
    |
    */
            $request->validate([
                'engine_id' => 'required|integer|exists:m_engines,id',

                'locations' => 'required|array|min:1',
                'locations.*.location_id' => 'required|integer|exists:m_locations,id',

                'locations.*.items' => 'required|array|min:1',

                'locations.*.items.*.material_id'
                => 'required|integer|exists:m_materials,id',

                'locations.*.items.*.width_ids'
                => 'required|array|min:1',

                'locations.*.items.*.width_ids.*'
                => 'required|integer|exists:m_material_sizes,id',

                'locations.*.items.*.vendor_id'
                => 'nullable|integer|exists:m_vendors,id',
            ]);

            /*
    |--------------------------------------------------------------------------
    | Validate Engine ID
    |--------------------------------------------------------------------------
    */
            if ((int) $request->engine_id !== (int) $engineId) {
                throw new \Exception('Engine tidak sesuai dengan data Production Cost.');
            }

            /*
    |--------------------------------------------------------------------------
    | Get Engine
    |--------------------------------------------------------------------------
    |
    | Disamakan dengan store():
    | - Tidak menggunakan filter status
    | - Engine yang tidak ditemukan akan ditolak
    |
    */
            $engine = DB::table('m_engines')
                ->where('id', $engineId)
                ->first();

            if (!$engine) {
                throw new \Exception('Engine tidak ditemukan.');
            }

            /*
    |--------------------------------------------------------------------------
    | Check Calculation Rules
    |--------------------------------------------------------------------------
    */
            $engineName = strtolower(trim($engine->name));

            if ($engineName === 'digital print a3+') {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'type' => 'warning',
                    'message' => 'Mesin Digital Print A3+ belum mempunyai master calculation rules.'
                ], 422);
            }

            if ($engineName !== 'large format') {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'type' => 'warning',
                    'message' => 'Mesin "' . $engine->name . '" belum mempunyai master calculation rules.'
                ], 422);
            }

            /*
    |--------------------------------------------------------------------------
    | Helper Parse Number
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
    | Helper Rounding
    |--------------------------------------------------------------------------
    */
            $roundUp = function ($value, $rounding) {
                if ($rounding === null || $rounding <= 0) {
                    return $value;
                }

                return ceil($value / $rounding) * $rounding;
            };

            /*
    |--------------------------------------------------------------------------
    | Check Duplicate Location in Request
    |--------------------------------------------------------------------------
    */
            $locationIds = [];

            foreach ($request->locations as $locationConfig) {
                $locationId = (int) $locationConfig['location_id'];

                if (in_array($locationId, $locationIds)) {
                    throw new \Exception(
                        'Lokasi tidak boleh digunakan lebih dari satu kali dalam satu Production Cost.'
                    );
                }

                $locationIds[] = $locationId;
            }

            /*
    |--------------------------------------------------------------------------
    | Get Locations
    |--------------------------------------------------------------------------
    |
    | Disamakan dengan store():
    | tidak memfilter status.
    |
    */
            $locations = DB::table('m_locations')
                ->whereIn('id', $locationIds)
                ->get()
                ->keyBy('id');

            foreach ($locationIds as $locationId) {
                if (!isset($locations[$locationId])) {
                    throw new \Exception('Lokasi tidak ditemukan.');
                }
            }

            /*
    |--------------------------------------------------------------------------
    | Collect Material IDs
    |--------------------------------------------------------------------------
    */
            $materialIds = [];

            foreach ($request->locations as $locationConfig) {
                foreach ($locationConfig['items'] as $item) {
                    $materialIds[] = (int) $item['material_id'];
                }
            }

            $materialIds = array_values(array_unique($materialIds));

            /*
    |--------------------------------------------------------------------------
    | Get Materials
    |--------------------------------------------------------------------------
    |
    | Disamakan dengan store():
    | tidak memfilter status material.
    |
    */
            $materials = DB::table('m_materials as m')
                ->join('m_categories as c', 'm.category_id', '=', 'c.id')
                ->whereIn('m.id', $materialIds)
                ->select(
                    'm.id',
                    'm.category_id',
                    'm.material_code',
                    'm.material_name as name',
                    'c.name as category_name'
                )
                ->get()
                ->keyBy('id');

            foreach ($materialIds as $materialId) {
                if (!isset($materials[$materialId])) {
                    throw new \Exception('Material tidak ditemukan.');
                }
            }

            /*
    |--------------------------------------------------------------------------
    | Collect Width IDs
    |--------------------------------------------------------------------------
    */
            $widthIds = [];

            foreach ($request->locations as $locationConfig) {
                foreach ($locationConfig['items'] as $item) {
                    foreach ($item['width_ids'] as $widthId) {
                        $widthIds[] = (int) $widthId;
                    }
                }
            }

            $widthIds = array_values(array_unique($widthIds));

            /*
    |--------------------------------------------------------------------------
    | Get Material Sizes
    |--------------------------------------------------------------------------
    |
    | m_material_sizes TIDAK memiliki kolom status.
    |
    */
            $materialSizes = DB::table('m_material_sizes')
                ->whereIn('id', $widthIds)
                ->get()
                ->keyBy('id');

            foreach ($widthIds as $widthId) {
                if (!isset($materialSizes[$widthId])) {
                    throw new \Exception('Ukuran material tidak ditemukan.');
                }
            }

            /*
    |--------------------------------------------------------------------------
    | Validate Material Size Belongs To Material
    |--------------------------------------------------------------------------
    */
            $materialSizes = DB::table('m_material_sizes')
                ->whereIn('id', $widthIds)
                ->get()
                ->keyBy('id');

            foreach ($widthIds as $widthId) {
                if (!isset($materialSizes[$widthId])) {
                    throw new \Exception('Ukuran material tidak ditemukan.');
                }
            }

            /*
    |--------------------------------------------------------------------------
    | Collect Vendor IDs
    |--------------------------------------------------------------------------
    */
            $vendorIds = [];

            foreach ($request->locations as $locationConfig) {
                foreach ($locationConfig['items'] as $item) {
                    if (
                        isset($item['vendor_id']) &&
                        $item['vendor_id'] !== null &&
                        $item['vendor_id'] !== ''
                    ) {
                        $vendorIds[] = (int) $item['vendor_id'];
                    }
                }
            }

            $vendorIds = array_values(array_unique($vendorIds));

            /*
    |--------------------------------------------------------------------------
    | Get Active Vendors
    |--------------------------------------------------------------------------
    */
            $vendors = DB::table('m_vendors')
                ->whereIn('id', $vendorIds)
                ->where('status', 'Active')
                ->get()
                ->keyBy('id');

            /*
    |--------------------------------------------------------------------------
    | Get Active Pricing Rules
    |--------------------------------------------------------------------------
    */
            $pricingRules = DB::table('m_pricing_rules')
                ->where('engine_id', $engineId)
                ->whereIn('location_id', $locationIds)
                ->where('status', 'Active')
                ->get();

            /*
    |--------------------------------------------------------------------------
    | Key Pricing Rules
    |--------------------------------------------------------------------------
    |
    | Key:
    | location | vendor | category | price_type
    |
    */
            $pricingRuleMap = [];

            foreach ($pricingRules as $rule) {
                $vendorKey = $rule->vendor_id === null
                    ? 'null'
                    : (string) $rule->vendor_id;

                $key =
                    $rule->location_id . '|' .
                    $vendorKey . '|' .
                    $rule->category_id . '|' .
                    strtolower(trim($rule->price_type));

                $pricingRuleMap[$key] = $rule;
            }

            /*
    |--------------------------------------------------------------------------
    | Normalize Data
    |--------------------------------------------------------------------------
    */
            $normalizedRows = [];

            foreach ($request->locations as $locationConfig) {
                $locationId = (int) $locationConfig['location_id'];

                $location = $locations[$locationId];

                $isOutsourcing =
                    strtolower(trim($location->name)) === 'outsourcing';

                /*
        |--------------------------------------------------------------------------
        | Duplicate Material in Same Location
        |--------------------------------------------------------------------------
        */
                $materialConfigKeys = [];

                foreach ($locationConfig['items'] as $item) {
                    $materialId = (int) $item['material_id'];

                    $widthIdsForKey = array_map(
                        function ($widthId) {
                            return (int) $widthId;
                        },
                        $item['width_ids']
                    );

                    sort($widthIdsForKey);

                    $vendorIdForKey = null;

                    if (
                        isset($item['vendor_id']) &&
                        $item['vendor_id'] !== null &&
                        $item['vendor_id'] !== ''
                    ) {
                        $vendorIdForKey = (int) $item['vendor_id'];
                    }

                    $duplicateKey =
                        $materialId . '|' .
                        $vendorIdForKey . '|' .
                        implode(',', $widthIdsForKey);

                    if (isset($materialConfigKeys[$duplicateKey])) {
                        throw new \Exception(
                            'Konfigurasi material tidak boleh duplikat pada lokasi yang sama.'
                        );
                    }

                    $materialConfigKeys[$duplicateKey] = true;
                }

                /*
        |--------------------------------------------------------------------------
        | Process Items
        |--------------------------------------------------------------------------
        */
                foreach ($locationConfig['items'] as $item) {
                    $materialId = (int) $item['material_id'];

                    if (!isset($materials[$materialId])) {
                        throw new \Exception('Material tidak ditemukan.');
                    }

                    $material = $materials[$materialId];

                    /*
            |--------------------------------------------------------------------------
            | Vendor
            |--------------------------------------------------------------------------
            */
                    $vendorId = null;
                    $vendor = null;

                    if ($isOutsourcing) {
                        if (
                            !isset($item['vendor_id']) ||
                            $item['vendor_id'] === null ||
                            $item['vendor_id'] === ''
                        ) {
                            throw new \Exception(
                                'Master Vendor wajib dipilih untuk lokasi Outsourcing.'
                            );
                        }

                        $vendorId = (int) $item['vendor_id'];

                        if (!isset($vendors[$vendorId])) {
                            throw new \Exception(
                                'Master Vendor tidak ditemukan atau tidak aktif.'
                            );
                        }

                        $vendor = $vendors[$vendorId];
                    }

                    /*
            |--------------------------------------------------------------------------
            | Pricing Rule Key
            |--------------------------------------------------------------------------
            */
                    $vendorKey = $isOutsourcing
                        ? (string) $vendorId
                        : 'null';

                    $categoryId = (int) $material->category_id;

                    /*
            |--------------------------------------------------------------------------
            | Outsourcing Calculation
            |--------------------------------------------------------------------------
            */
                    if ($isOutsourcing) {
                        /*
                |--------------------------------------------------------------------------
                | Vendor Price
                |--------------------------------------------------------------------------
                */
                        if (
                            !isset($item['vendor_price']) ||
                            $item['vendor_price'] === null ||
                            $item['vendor_price'] === ''
                        ) {
                            throw new \Exception(
                                'Harga Vendor wajib diisi untuk lokasi Outsourcing.'
                            );
                        }

                        $vendorPrice = $parseNumber($item['vendor_price']);

                        if ($vendorPrice <= 0) {
                            throw new \Exception(
                                'Harga Vendor harus lebih besar dari 0.'
                            );
                        }

                        /*
                |--------------------------------------------------------------------------
                | General Pricing Rule
                |--------------------------------------------------------------------------
                */
                        $generalKey =
                            $locationId . '|' .
                            $vendorKey . '|' .
                            $categoryId . '|general';

                        if (!isset($pricingRuleMap[$generalKey])) {
                            throw new \Exception(
                                'Pricing Rule General tidak ditemukan untuk material "' .
                                    $material->name .
                                    '" pada lokasi "' .
                                    $location->name .
                                    '".'
                            );
                        }

                        $generalRule = $pricingRuleMap[$generalKey];

                        /*
                |--------------------------------------------------------------------------
                | Division Pricing Rule
                |--------------------------------------------------------------------------
                */
                        $divisionKey =
                            $locationId . '|' .
                            $vendorKey . '|' .
                            $categoryId . '|division';

                        if (!isset($pricingRuleMap[$divisionKey])) {
                            throw new \Exception(
                                'Pricing Rule Division tidak ditemukan untuk material "' .
                                    $material->name .
                                    '" pada lokasi "' .
                                    $location->name .
                                    '".'
                            );
                        }

                        $divisionRule = $pricingRuleMap[$divisionKey];

                        /*
                |--------------------------------------------------------------------------
                | General Price
                |--------------------------------------------------------------------------
                */
                        $generalMarkup =
                            ProductionCostFinishingMarkup::general(
                                $engine->name,
                                $location->name,
                                $material->category_name,
                                $material->name,
                                (float) $generalRule->markup_percentage
                            );

                        $generalRounding =
                            $generalRule->rounding_value !== null
                            ? (float) $generalRule->rounding_value
                            : 0;

                        $generalPrice =
                            $vendorPrice +
                            ($vendorPrice * ($generalMarkup / 100));

                        $generalPrice =
                            $roundUp($generalPrice, $generalRounding);

                        /*
                |--------------------------------------------------------------------------
                | Division Price
                |--------------------------------------------------------------------------
                */
                        $divisionMarkup =
                            (float) $divisionRule->markup_percentage;

                        $divisionRounding =
                            $divisionRule->rounding_value !== null
                            ? (float) $divisionRule->rounding_value
                            : 0;

                        $divisionPrice =
                            $generalPrice *
                            ($divisionMarkup / 100);

                        $divisionPrice =
                            $roundUp($divisionPrice, $divisionRounding);

                        /*
                |--------------------------------------------------------------------------
                | Outsourcing does not use Plain Pricing
                |--------------------------------------------------------------------------
                */
                        $plainPrice = 0;
                        $plainPricingRuleId = null;

                        /*
                |--------------------------------------------------------------------------
                | Normalize Outsourcing Row
                |--------------------------------------------------------------------------
                */
                        $normalizedRows[] = [
                            'location_id' => $locationId,
                            'material_id' => $materialId,
                            'width_ids' => array_map(
                                function ($widthId) {
                                    return (int) $widthId;
                                },
                                $item['width_ids']
                            ),

                            'vendor_id' => $vendorId,
                            'vendor_price' => $vendorPrice,

                            'price_per_meter' => 0,
                            'production_cost' => 0,
                            'finishing_cost' => 0,

                            'total_cost' => $vendorPrice,

                            'general_price' => $generalPrice,
                            'division_price' => $divisionPrice,
                            'plain_price' => $plainPrice,

                            'general_pricing_rule_id' => $generalRule->id,
                            'division_pricing_rule_id' => $divisionRule->id,
                            'plain_pricing_rule_id' => $plainPricingRuleId,
                        ];
                    }

                    /*
            |--------------------------------------------------------------------------
            | Non-Outsourcing Calculation
            |--------------------------------------------------------------------------
            */ else {
                        /*
                |--------------------------------------------------------------------------
                | Vendor Must Be Null
                |--------------------------------------------------------------------------
                */
                        $vendorId = null;

                        /*
                |--------------------------------------------------------------------------
                | Price Per Meter
                |--------------------------------------------------------------------------
                */
                        if (
                            !isset($item['price_per_meter']) ||
                            $item['price_per_meter'] === null ||
                            $item['price_per_meter'] === ''
                        ) {
                            throw new \Exception(
                                'Harga per meter wajib diisi.'
                            );
                        }

                        /*
                |--------------------------------------------------------------------------
                | Production Cost
                |--------------------------------------------------------------------------
                */
                        if (
                            !isset($item['production_cost']) ||
                            $item['production_cost'] === null ||
                            $item['production_cost'] === ''
                        ) {
                            throw new \Exception(
                                'Production Cost wajib diisi.'
                            );
                        }

                        /*
                |--------------------------------------------------------------------------
                | Finishing Cost
                |--------------------------------------------------------------------------
                */
                        if (
                            !isset($item['finishing_cost']) ||
                            $item['finishing_cost'] === null ||
                            $item['finishing_cost'] === ''
                        ) {
                            throw new \Exception(
                                'Finishing Cost wajib diisi.'
                            );
                        }

                        $pricePerMeter =
                            $parseNumber($item['price_per_meter']);

                        $productionCost =
                            $parseNumber($item['production_cost']);

                        $finishingCost =
                            $parseNumber($item['finishing_cost']);

                        /*
                |--------------------------------------------------------------------------
                | General Pricing Rule
                |--------------------------------------------------------------------------
                */
                        $generalKey =
                            $locationId . '|' .
                            $vendorKey . '|' .
                            $categoryId . '|general';

                        if (!isset($pricingRuleMap[$generalKey])) {
                            throw new \Exception(
                                'Pricing Rule General tidak ditemukan untuk material "' .
                                    $material->name .
                                    '" pada lokasi "' .
                                    $location->name .
                                    '".'
                            );
                        }

                        $generalRule = $pricingRuleMap[$generalKey];

                        /*
                |--------------------------------------------------------------------------
                | Division Pricing Rule
                |--------------------------------------------------------------------------
                */
                        $divisionKey =
                            $locationId . '|' .
                            $vendorKey . '|' .
                            $categoryId . '|division';

                        if (!isset($pricingRuleMap[$divisionKey])) {
                            throw new \Exception(
                                'Pricing Rule Division tidak ditemukan untuk material "' .
                                    $material->name .
                                    '" pada lokasi "' .
                                    $location->name .
                                    '".'
                            );
                        }

                        $divisionRule = $pricingRuleMap[$divisionKey];

                        /*
                |--------------------------------------------------------------------------
                | General Price
                |--------------------------------------------------------------------------
                */
                        $totalCost =
                            (1.2 * $pricePerMeter) +
                            $productionCost +
                            $finishingCost;

                        $generalMarkup =
                            ProductionCostFinishingMarkup::general(
                                $engine->name,
                                $location->name,
                                $material->category_name,
                                $material->name,
                                (float) $generalRule->markup_percentage
                            );

                        $generalRounding =
                            $generalRule->rounding_value !== null
                            ? (float) $generalRule->rounding_value
                            : 0;

                        $generalPrice =
                            (($generalMarkup / 100) * $totalCost) +
                            $totalCost;

                        $generalPrice =
                            $roundUp($generalPrice, $generalRounding);

                        /*
                |--------------------------------------------------------------------------
                | Division Price
                |--------------------------------------------------------------------------
                */
                        $divisionMarkup =
                            (float) $divisionRule->markup_percentage;

                        $divisionRounding =
                            $divisionRule->rounding_value !== null
                            ? (float) $divisionRule->rounding_value
                            : 0;

                        $divisionPrice =
                            $generalPrice *
                            ($divisionMarkup / 100);

                        $divisionPrice =
                            $roundUp($divisionPrice, $divisionRounding);

                        /*
                |--------------------------------------------------------------------------
                | Plain Pricing Rule - Optional
                |--------------------------------------------------------------------------
                */
                        $plainKey =
                            $locationId . '|' .
                            $vendorKey . '|' .
                            $categoryId . '|plain';

                        $plainPrice = 0;
                        $plainPricingRuleId = null;

                        if (isset($pricingRuleMap[$plainKey])) {
                            $plainRule = $pricingRuleMap[$plainKey];

                            $plainMarkup =
                                (float) $plainRule->markup_percentage;

                            $plainRounding =
                                $plainRule->rounding_value !== null
                                ? (float) $plainRule->rounding_value
                                : 0;

                            $plainPrice =
                                (($plainMarkup / 100) * $pricePerMeter) +
                                $pricePerMeter;

                            $plainPrice =
                                $roundUp($plainPrice, $plainRounding);

                            $plainPricingRuleId = $plainRule->id;
                        }

                        /*
                |--------------------------------------------------------------------------
                | Normalize Non-Outsourcing Row
                |--------------------------------------------------------------------------
                */
                        $normalizedRows[] = [
                            'location_id' => $locationId,
                            'material_id' => $materialId,
                            'width_ids' => array_map(
                                function ($widthId) {
                                    return (int) $widthId;
                                },
                                $item['width_ids']
                            ),

                            'vendor_id' => null,
                            'vendor_price' => null,

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

            /*
    |--------------------------------------------------------------------------
    | Duplicate Validation Inside Request
    |--------------------------------------------------------------------------
    */
            $requestDuplicateKeys = [];

            foreach ($normalizedRows as $row) {
                $widthIdsForKey = $row['width_ids'];

                sort($widthIdsForKey);

                $key =
                    $engineId . '|' .
                    $row['location_id'] . '|' .
                    ($row['vendor_id'] === null
                        ? 'null'
                        : $row['vendor_id']) . '|' .
                    $row['material_id'] . '|' .
                    implode(',', $widthIdsForKey) . '|' .
                    $row['price_per_meter'] . '|' .
                    $row['production_cost'] . '|' .
                    $row['finishing_cost'] . '|' .
                    ($row['vendor_price'] === null
                        ? 'null'
                        : $row['vendor_price']);

                if (isset($requestDuplicateKeys[$key])) {
                    throw new \Exception(
                        'Terdapat konfigurasi Production Cost yang duplikat dalam data yang dikirim.'
                    );
                }

                $requestDuplicateKeys[$key] = true;
            }

            /*
    |--------------------------------------------------------------------------
    | Get Existing Production Cost IDs
    |--------------------------------------------------------------------------
    |
    | HANYA Production Cost Material.
    | Production Cost Laminasi tidak memiliki detail material,
    | sehingga tidak akan masuk ke dalam daftar ID yang dihapus.
    |
    */
            $oldProductionCostIds = DB::table('m_production_costs as pc')
                ->join(
                    'm_production_cost_details as pcd',
                    'pcd.production_cost_id',
                    '=',
                    'pc.id'
                )
                ->where('pc.engine_id', $engineId)
                ->pluck('pc.id')
                ->unique()
                ->toArray();

            /*
    |--------------------------------------------------------------------------
    | Duplicate Check Against Existing Database
    |--------------------------------------------------------------------------
    |
    | Production Cost lama milik engine yang sedang di-update
    | dikecualikan karena nantinya akan dihapus dan dibuat ulang.
    |
    */
            foreach ($normalizedRows as $row) {
                foreach ($row['width_ids'] as $widthId) {
                    $duplicateQuery = DB::table('m_production_costs as pc')
                        ->join(
                            'm_production_cost_locations as pcl',
                            'pc.id',
                            '=',
                            'pcl.production_cost_id'
                        )
                        ->join(
                            'm_production_cost_details as pcd',
                            'pc.id',
                            '=',
                            'pcd.production_cost_id'
                        )
                        ->join(
                            'm_production_cost_detail_widths as pcdw',
                            'pcd.id',
                            '=',
                            'pcdw.production_cost_detail_id'
                        )
                        ->where('pc.engine_id', $engineId)
                        ->where('pcl.location_id', $row['location_id'])
                        ->where('pcd.material_id', $row['material_id'])
                        ->where('pcdw.material_size_id', $widthId);

                    /*
            |--------------------------------------------------------------------------
            | Vendor Match
            |--------------------------------------------------------------------------
            */
                    if ($row['vendor_id'] === null) {
                        $duplicateQuery->whereNull('pcd.vendor_id');
                    } else {
                        $duplicateQuery->where(
                            'pcd.vendor_id',
                            $row['vendor_id']
                        );
                    }

                    /*
            |--------------------------------------------------------------------------
            | Cost Match
            |--------------------------------------------------------------------------
            */
                    if ($row['vendor_id'] === null) {
                        $duplicateQuery
                            ->where(
                                'pcd.price_per_meter',
                                $row['price_per_meter']
                            )
                            ->where(
                                'pcd.production_cost',
                                $row['production_cost']
                            )
                            ->where(
                                'pcd.finishing_cost',
                                $row['finishing_cost']
                            );
                    } else {
                        $duplicateQuery->where(
                            'pcd.vendor_price',
                            $row['vendor_price']
                        );
                    }

                    /*
            |--------------------------------------------------------------------------
            | Exclude Existing Records Being Updated
            |--------------------------------------------------------------------------
            */
                    if (!empty($oldProductionCostIds)) {
                        $duplicateQuery->whereNotIn(
                            'pc.id',
                            $oldProductionCostIds
                        );
                    }

                    if ($duplicateQuery->exists()) {
                        throw new \Exception(
                            'Production Cost dengan konfigurasi yang sama sudah tersedia.'
                        );
                    }
                }
            }

            /*
    |--------------------------------------------------------------------------
    | Delete Existing Production Cost
    |--------------------------------------------------------------------------
    |
    | HANYA Production Cost Material yang dihapus.
    | Production Cost Laminasi tetap dipertahankan.
    |
    */
            if (!empty($oldProductionCostIds)) {

                /*
        |--------------------------------------------------------------------------
        | Delete Width Details
        |--------------------------------------------------------------------------
        */
                DB::table('m_production_cost_detail_widths')
                    ->whereIn(
                        'production_cost_detail_id',
                        function ($query) use ($oldProductionCostIds) {
                            $query->select('id')
                                ->from('m_production_cost_details')
                                ->whereIn(
                                    'production_cost_id',
                                    $oldProductionCostIds
                                );
                        }
                    )
                    ->delete();

                /*
        |--------------------------------------------------------------------------
        | Delete Production Cost Details
        |--------------------------------------------------------------------------
        */
                DB::table('m_production_cost_details')
                    ->whereIn(
                        'production_cost_id',
                        $oldProductionCostIds
                    )
                    ->delete();

                /*
        |--------------------------------------------------------------------------
        | Delete Production Cost Locations
        |--------------------------------------------------------------------------
        */
                DB::table('m_production_cost_locations')
                    ->whereIn(
                        'production_cost_id',
                        $oldProductionCostIds
                    )
                    ->delete();

                /*
        |--------------------------------------------------------------------------
        | Delete Production Cost Headers
        |--------------------------------------------------------------------------
        */
                DB::table('m_production_costs')
                    ->whereIn('id', $oldProductionCostIds)
                    ->delete();
            }

            /*
    |--------------------------------------------------------------------------
    | Insert New Production Cost
    |--------------------------------------------------------------------------
    |
    | Update menggunakan mekanisme:
    | delete existing -> insert kembali berdasarkan request terbaru.
    |
    */
            $userId = Auth::user()->name;

            /*
    |--------------------------------------------------------------------------
    | Group Rows By Location
    |--------------------------------------------------------------------------
    */
            $rowsByLocation = [];

            foreach ($normalizedRows as $row) {
                $rowsByLocation[$row['location_id']][] = $row;
            }

            foreach ($rowsByLocation as $locationId => $rows) {

                /*
        |--------------------------------------------------------------------------
        | Insert Production Cost Header
        |--------------------------------------------------------------------------
        */
                $productionCostId = DB::table('m_production_costs')
                    ->insertGetId([
                        'engine_id' => $engineId,

                        'created_by' => $userId,
                        'created_at' => now(),
                        'updated_by' => $userId,
                        'updated_at' => now(),
                    ]);

                /*
        |--------------------------------------------------------------------------
        | Insert Location
        |--------------------------------------------------------------------------
        */
                DB::table('m_production_cost_locations')
                    ->insert([
                        'production_cost_id' => $productionCostId,
                        'location_id' => $locationId,

                        'created_by' => $userId,
                        'created_at' => now(),
                        'updated_by' => $userId,
                        'updated_at' => now(),
                    ]);

                /*
        |--------------------------------------------------------------------------
        | Insert Details
        |--------------------------------------------------------------------------
        */
                foreach ($rows as $row) {

                    $detailId = DB::table('m_production_cost_details')
                        ->insertGetId([
                            'production_cost_id' => $productionCostId,
                            'material_id' => $row['material_id'],

                            'general_pricing_rule_id' =>
                            $row['general_pricing_rule_id'],

                            'division_pricing_rule_id' =>
                            $row['division_pricing_rule_id'],

                            'plain_pricing_rule_id' =>
                            $row['plain_pricing_rule_id'],

                            'production_cost' =>
                            $row['production_cost'],

                            'finishing_cost' =>
                            $row['finishing_cost'],

                            'total_cost' =>
                            $row['total_cost'],

                            'general_price' =>
                            $row['general_price'],

                            'division_price' =>
                            $row['division_price'],

                            'plain_price' =>
                            $row['plain_price'],

                            'price_per_meter' =>
                            $row['price_per_meter'],

                            'vendor_price' =>
                            $row['vendor_price'],

                            'vendor_id' =>
                            $row['vendor_id'],

                            'created_by' => $userId,
                            'created_at' => now(),
                            'updated_by' => $userId,
                            'updated_at' => now(),
                        ]);

                    /*
            |--------------------------------------------------------------------------
            | Insert Widths
            |--------------------------------------------------------------------------
            */
                    foreach ($row['width_ids'] as $widthId) {
                        DB::table('m_production_cost_detail_widths')
                            ->insert([
                                'production_cost_detail_id' => $detailId,
                                'material_size_id' => $widthId,

                                'created_by' => $userId,
                                'created_at' => now(),
                                'updated_by' => $userId,
                                'updated_at' => now(),
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
                'message' => 'Production Cost berhasil diperbarui.'
            ]);
        }

        /*
|--------------------------------------------------------------------------
| Validation Exception
|--------------------------------------------------------------------------
*/ catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            throw $e;
        }

        /*
|--------------------------------------------------------------------------
| Other Exception
|--------------------------------------------------------------------------
*/ catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            /*
        |--------------------------------------------------------------------------
        | CARI PRODUCTION COST MATERIAL BERDASARKAN ENGINE
        |--------------------------------------------------------------------------
        |
        | Hanya ambil production cost yang memiliki detail Material.
        | Production Cost Laminasi tidak akan ikut terambil.
        |
        */

            $productionCostIds = DB::table('m_production_costs as pc')
                ->join(
                    'm_production_cost_details as pcd',
                    'pcd.production_cost_id',
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
                    'message' => 'Data production cost material tidak ditemukan.'
                ], 404));
            }

            /*
        |--------------------------------------------------------------------------
        | HAPUS PRODUCTION COST MATERIAL
        |--------------------------------------------------------------------------
        |
        | FK cascade akan otomatis menghapus:
        |
        | - m_production_cost_locations
        | - m_production_cost_details
        | - m_production_cost_detail_widths
        |
        */

            DB::table('m_production_costs')
                ->whereIn('id', $productionCostIds)
                ->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'Production cost material berhasil dihapus.'
        ]);
    }

    public function export(Request $request)
    {
        // Export mengikuti filter mesin aktif di halaman index.
        if ($request->query('pricing_engine') === 'digital_print_a3') {
            return Excel::download(
                new DigitalProductionCostsExport(),
                'production_cost_digital_print_a3.xlsx'
            );
        }

        return Excel::download(
            new ProductionCostFinishingsExport(),
            'production_cost_finishings.xlsx'
        );
    }

    public function import(Request $request)
    {
        $request->validate(
            [
                'file' => 'required|file|mimes:xlsx,xls',
            ],
            [
                'file.required' =>
                'Silakan pilih file Excel terlebih dahulu.',
                'file.file' =>
                'File yang dipilih tidak valid.',
                'file.mimes' =>
                'File harus berformat Excel (.xlsx atau .xls).',
            ]
        );

        try {
            $import = new ProductionCostFinishingsImport();

            Excel::import(
                $import,
                $request->file('file')
            );

            return response()->json([
                'success' => true,
                'message' =>
                $import->importedRows .
                    ' data Production Cost berhasil diimport.',
                'imported_rows' =>
                $import->importedRows,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}

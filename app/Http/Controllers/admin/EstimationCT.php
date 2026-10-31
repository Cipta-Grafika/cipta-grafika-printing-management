<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class EstimationCT extends Controller
{
    public function index() {}

    public function createLF()
    {
        /*
        |--------------------------------------------------------------------------
        | Lokasi
        |--------------------------------------------------------------------------
        */

        $data['locations'] = DB::table('m_locations')
            ->whereRaw(
                'LOWER(TRIM(status)) = ?',
                ['active']
            )
            ->orderBy('name', 'asc')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Kategori
        |--------------------------------------------------------------------------
        */

        // $data['categories'] = DB::table('m_categories')
        //     ->orderBy('name', 'asc')
        //     ->get();


        $data['vendors'] = DB::table('m_vendors')
            ->where('status', 'Active')
            ->get();

        $data['lfPriceList'] = $this->buildLargeFormatPriceListData();

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view('admin.estimation.large-format.create')->with($data);
    }

    public function getLFCategories()
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

    // public function getMaterials(Request $request)
    // {
    //     $validated = $request->validate([
    //         'location_id' => [
    //             'required',
    //             'integer',
    //             'exists:m_locations,id',
    //         ],
    //         'category_id' => [
    //             'required',
    //             'integer',
    //             'exists:m_categories,id',
    //         ],
    //         'vendor_id' => [
    //             'nullable',
    //             'integer',
    //             'exists:m_vendors,id',
    //         ],
    //     ]);

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 1. CEK LOKASI
    //     |--------------------------------------------------------------------------
    //     */

    //     $location = DB::table('m_locations')
    //         ->where('id', $validated['location_id'])
    //         ->first();

    //     if (!$location) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Lokasi tidak ditemukan.',
    //         ], 422);
    //     }

    //     $isOutsourcing =
    //         strtolower(trim($location->name)) === 'outsourcing';

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 2. VALIDASI VENDOR UNTUK OUTSOURCING
    //     |--------------------------------------------------------------------------
    //     */

    //     if ($isOutsourcing && empty($validated['vendor_id'])) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Vendor wajib dipilih untuk lokasi Outsourcing.',
    //         ], 422);
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 3. AMBIL ENGINE LARGE FORMAT
    //     |--------------------------------------------------------------------------
    //     */

    //     $engine = DB::table('m_engines')
    //         ->whereRaw(
    //             'LOWER(TRIM(name)) = ?',
    //             ['large format']
    //         )
    //         ->whereRaw(
    //             'LOWER(TRIM(status)) = ?',
    //             ['active']
    //         )
    //         ->first();

    //     if (!$engine) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Mesin Large Format tidak ditemukan atau tidak aktif.',
    //         ], 422);
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 4. AMBIL MATERIAL
    //     |--------------------------------------------------------------------------
    //     */

    //     $materials = DB::table('m_materials as m')

    //         // Category
    //         ->join(
    //             'm_categories as c',
    //             'c.id',
    //             '=',
    //             'm.category_id'
    //         )

    //         // Production Cost Header
    //         ->join(
    //             'm_production_costs as pc',
    //             'pc.engine_id',
    //             '=',
    //             DB::raw($engine->id)
    //         )

    //         // Production Cost Location
    //         ->join(
    //             'm_production_cost_locations as pcl',
    //             'pcl.production_cost_id',
    //             '=',
    //             'pc.id'
    //         )

    //         // Production Cost Detail
    //         ->join(
    //             'm_production_cost_details as pcd',
    //             'pcd.production_cost_id',
    //             '=',
    //             'pc.id'
    //         )

    //         ->where(
    //             'pcl.location_id',
    //             $validated['location_id']
    //         )

    //         ->where(
    //             'm.category_id',
    //             $validated['category_id']
    //         )

    //         ->where(
    //             'pcd.material_id',
    //             '=',
    //             DB::raw('m.id')
    //         );

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 5. FILTER VENDOR KHUSUS OUTSOURCING
    //     |--------------------------------------------------------------------------
    //     */

    //     if ($isOutsourcing) {
    //         $materials->where(
    //             'pcd.vendor_id',
    //             $validated['vendor_id']
    //         );
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 6. HASIL MATERIAL
    //     |--------------------------------------------------------------------------
    //     */

    //     $materials = $materials
    //         ->select([
    //             'm.id',
    //             'm.material_name',
    //         ])
    //         ->distinct()
    //         ->orderBy(
    //             'm.material_name',
    //             'asc'
    //         )
    //         ->get();

    //     /*
    //     |--------------------------------------------------------------------------
    //     | 7. RESPONSE
    //     |--------------------------------------------------------------------------
    //     */

    //     return response()->json([
    //         'success' => true,
    //         'data' => $materials,
    //     ]);
    // }


    public function getMaterials(Request $request)
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

                ->join(
                    'm_engines as e',
                    'e.id',
                    '=',
                    'dpcfg.engine_id'
                )

                ->where('e.name', 'ILIKE', '%large format%')

                ->where(
                    'dpcfg.location_id',
                    $validated['location_id']
                )

                ->where(
                    'dp.category_id',
                    $validated['category_id']
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

                ->join(
                    'm_engines as e',
                    'e.id',
                    '=',
                    'dpcfg.engine_id'
                )

                ->where('e.name', 'ILIKE', '%large format%')

                ->where(
                    'dpcfg.location_id',
                    $validated['location_id']
                )

                ->where(
                    'dp.category_id',
                    $validated['category_id']
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


    public function getMaterialSizes(Request $request)
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

    // all

    public function storeLF(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | IDENTIFIKASI DISPLAY PURWAKARTA
        |--------------------------------------------------------------------------
        */

        $displayLocation = DB::table('m_locations')
            ->where('id', $request->input('location_id'))
            ->value('name');

        $displayCategory = DB::table('m_categories')
            ->where('id', $request->input('category_id'))
            ->value('name');

        $isDisplayPurwakarta =
            strtolower(trim((string) $displayLocation)) === 'purwakarta'
            && strtolower(trim((string) $displayCategory)) === 'display';

        $additionalComponents =
            $request->input('additional_components', []);

        $additionalComponents = array_values(
            array_filter(
                $additionalComponents,
                function ($component) {
                    return
                        !empty($component['type']) ||
                        !empty($component['name']) ||
                        !empty($component['qty']);
                }
            )
        );

        $request->merge([
            'additional_components' =>
            $additionalComponents ?: null,
        ]);

        $displayRules = [
            'location_id' => [
                'required',
                'integer',
                'exists:m_locations,id',
            ],

            'vendor_id' => [
                'nullable',
                'integer',
                'exists:m_vendors,id',
            ],

            'price_type' => [
                'required',
                'string',
                'in:general,division,plain',
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:m_categories,id',
            ],

            'qty' => [
                'required',
                'integer',
                'gt:0',
            ],

            'discount_type' => [
                'required',
                'in:none,percentage,nominal',
            ],

            'discount_value' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'estimation_time' => [
                'nullable',
                'string',
            ],

            'lamination_id' => [
                'nullable',
                'integer',
                'exists:m_laminations,id',
            ],

            'lamination_size_id' => [
                'nullable',
                'integer',
                'exists:m_lamination_sizes,id',
            ],

            'additional_components' => [
                'nullable',
                'array',
            ],

            'additional_components.*.type' => [
                'required',
                'string',
                'in:Material,Finishing,Jasa Pemasangan',
            ],

            'additional_components.*.name' => [
                'required',
                'string',
            ],

            'additional_components.*.qty' => [
                'required',
                'integer',
                'gt:0',
            ],

            'additional_components.*.price_type' => [
                'required',
                'in:unit_price,hpp_margin',
            ],

            'additional_components.*.unit_price' => [
                'nullable',
                'numeric',
                'min:0',
                'required_if:additional_components.*.price_type,unit_price',
            ],

            'additional_components.*.hpp' => [
                'nullable',
                'numeric',
                'min:0',
                'required_if:additional_components.*.price_type,hpp_margin',
            ],

            'additional_components.*.margin' => [
                'nullable',
                'numeric',
                'min:0',
                'max:99.99',
                'required_if:additional_components.*.price_type,hpp_margin',
            ],
        ];

        if ($isDisplayPurwakarta) {

            $displayRules['display_configuration_id'] = [
                'required',
                'integer',
                'exists:m_display_product_configurations,id',
            ];

            $displayRules['display_product_id'] = [
                'required',
                'integer',
                'exists:m_display_products,id',
            ];

            $displayRules['display_component_id'] = [
                'nullable',
                'integer',
                'exists:m_display_product_components,id',
            ];

            $displayRules['display_component_type'] = [
                'nullable',
                'in:frame,material,lamination',
            ];
        } else {



            $displayRules['material_id'] = [
                'required',
                'integer',
                'exists:m_materials,id',
            ];

            $displayRules['material_size_id'] = [
                'nullable',
                'integer',
                'exists:m_material_sizes,id',
            ];

            $displayRules['material_price_size_id'] = [
                'nullable',
                'integer',
                'exists:m_material_sizes,id',
            ];

            $displayRules['material_custom_width'] = [
                'nullable',
                'numeric',
                'gt:0',
            ];

            $displayRules['lamination_size_id'] = [
                'nullable',
                'integer',
                'exists:m_lamination_sizes,id',
            ];

            $displayRules['lamination_price_size_id'] = [
                'nullable',
                'integer',
                'exists:m_lamination_sizes,id',
            ];

            $displayRules['lamination_custom_width'] = [
                'nullable',
                'numeric',
                'gt:0',
            ];

            $displayRules['length'] = [
                'required',
                'numeric',
                'gt:0',
            ];

            $displayRules['width'] = [
                'required',
                'numeric',
                'gt:0',
            ];
        }

        $validated = $request->validate($displayRules);

        try {

            /*
            |--------------------------------------------------------------------------
            | DISPLAY PURWAKARTA
            |--------------------------------------------------------------------------
            */

            if ($isDisplayPurwakarta) {

                return $this->calculateDisplayPurwakarta(
                    $validated,
                    $request
                );
            }

            /*
            |--------------------------------------------------------------------------
            | LOCATION
            |--------------------------------------------------------------------------
            */

            $location = DB::table('m_locations')
                ->where('id', $validated['location_id'])
                ->first();

            if (!$location) {
                throw new Exception(
                    'Lokasi tidak ditemukan.'
                );
            }

            $locationName = strtolower(trim($location->name));

            $isOutsourcing = $locationName === 'outsourcing';

            if ($isOutsourcing && empty($validated['vendor_id'])) {
                throw new Exception(
                    'Vendor wajib dipilih untuk lokasi Outsourcing.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | ENGINE LARGE FORMAT
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
                throw new Exception(
                    'Mesin Large Format tidak ditemukan atau tidak aktif.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | MINIMUM CHARGE
            |--------------------------------------------------------------------------
            */

            $minimumCharge =
                (float) $engine->minimum_charge;


            /*
            |--------------------------------------------------------------------------
            | MATERIAL
            |--------------------------------------------------------------------------
            */

            $material = DB::table('m_materials as m')
                ->join(
                    'm_categories as c',
                    'c.id',
                    '=',
                    'm.category_id'
                )
                ->where(
                    'm.id',
                    $validated['material_id']
                )
                ->where(
                    'm.category_id',
                    $validated['category_id']
                )
                ->select(
                    'm.id',
                    'm.material_name',
                    'm.category_id',
                    'c.name as category_name'
                )
                ->first();

            if (!$material) {
                throw new Exception(
                    'Material tidak sesuai dengan kategori yang dipilih.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | MATERIAL SIZE
            |--------------------------------------------------------------------------
            */

            $materialSizeId = !empty($validated['material_size_id'])
                ? $validated['material_size_id']
                : ($validated['material_price_size_id'] ?? null);

            $materialSize = null;

            if ($materialSizeId) {

                $materialSize = DB::table('m_material_sizes')
                    ->where('id', $materialSizeId)
                    ->where('material_id', $validated['material_id'])
                    ->first();

                if (!$materialSize) {
                    throw new Exception(
                        'Ukuran referensi harga material tidak valid.'
                    );
                }
            }

            if (
                empty($validated['material_custom_width']) &&
                !$materialSize
            ) {
                throw new Exception(
                    'Ukuran material wajib dipilih.'
                );
            }

            if (
                !empty($validated['material_custom_width']) &&
                !$materialSize
            ) {
                throw new Exception(
                    'Ukuran referensi harga material wajib tersedia untuk custom width.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | BASIC DATA
            |--------------------------------------------------------------------------
            */

            $productLength =
                (float) $validated['length'];

            $productWidth =
                (float) $validated['width'];

            $qty =
                (int) $validated['qty'];

            $materialWidth = !empty($validated['material_custom_width'])
                ? (float) $validated['material_custom_width']
                : (float) $materialSize->width;


            /*
            |--------------------------------------------------------------------------
            | VALIDASI MUAT
            |--------------------------------------------------------------------------
            */

            $objectsPerRow =
                (int) floor(
                    $materialWidth / $productWidth
                );

            if ($objectsPerRow < 1) {
                throw new Exception(
                    'Ukuran produk tidak dapat dimuat pada lebar material.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | JUMLAH BARIS
            |--------------------------------------------------------------------------
            */

            $totalRows =
                (int) ceil(
                    $qty / $objectsPerRow
                );


            /*
            |--------------------------------------------------------------------------
            | PANJANG PRODUKSI
            |--------------------------------------------------------------------------
            */

            $productionLength =
                $productLength * $totalRows;


            /*
            |--------------------------------------------------------------------------
            | PRODUCTION COST
            |--------------------------------------------------------------------------
            */

            $productionCostQuery = DB::table('m_production_cost_details as pcd')
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
                ->where('pc.engine_id', $engine->id)
                ->where('pcl.location_id', $validated['location_id'])
                ->where('pcd.material_id', $validated['material_id'])
                ->whereExists(function ($query) use ($materialSizeId) {
                    $query->select(DB::raw(1))
                        ->from('m_production_cost_detail_widths as pcdw')
                        ->whereColumn(
                            'pcdw.production_cost_detail_id',
                            'pcd.id'
                        )
                        ->where(
                            'pcdw.material_size_id',
                            $materialSizeId
                        );
                });

            if ($isOutsourcing) {
                $productionCostQuery
                    ->where('pcd.vendor_id', $validated['vendor_id']);
            }

            $productionCost = $productionCostQuery
                ->select(
                    'pcd.price_per_meter',
                    'pcd.production_cost',
                    'pcd.finishing_cost',
                    'pcd.total_cost',
                    'pcd.general_price',
                    'pcd.division_price',
                    'pcd.plain_price',
                    'pcd.vendor_id',
                    'pcd.vendor_price'
                )
                ->first();

            if (!$productionCost) {
                throw new Exception(
                    'Harga material untuk lokasi dan ukuran tersebut belum tersedia.'
                );
            }

            $vendorName = null;

            if ($isOutsourcing) {
                $vendorName = DB::table('m_vendors')
                    ->where('id', $productionCost->vendor_id)
                    ->value('name');
            }

            /*
            |--------------------------------------------------------------------------
            | LAMINASI
            |--------------------------------------------------------------------------
            */

            $lamination = null;
            $laminationWidth = null;

            if (
                empty($validated['lamination_id']) &&
                (
                    !empty($validated['lamination_size_id']) ||
                    !empty($validated['lamination_custom_width'])
                )
            ) {
                throw new Exception(
                    'Laminasi wajib dipilih terlebih dahulu.'
                );
            }

            if (!empty($validated['lamination_id'])) {

                if (
                    empty($validated['lamination_size_id']) &&
                    empty($validated['lamination_custom_width'])
                ) {
                    throw new Exception(
                        'Ukuran laminasi wajib dipilih atau diisi.'
                    );
                }

                $laminationSizeId = !empty($validated['lamination_size_id'])
                    ? $validated['lamination_size_id']
                    : ($validated['lamination_price_size_id'] ?? null);

                if (!$laminationSizeId) {
                    throw new Exception(
                        'Ukuran referensi harga laminasi wajib tersedia.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | LAMINASI MASTER
                |--------------------------------------------------------------------------
                */

                $laminationMaster = DB::table('m_laminations')
                    ->where('id', $validated['lamination_id'])
                    ->whereRaw('LOWER(TRIM(status)) = ?', ['active'])
                    ->first();

                if (!$laminationMaster) {
                    throw new Exception(
                        'Laminasi tidak ditemukan atau tidak aktif.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | PRODUCTION COST LAMINASI
                |--------------------------------------------------------------------------
                */

                $lamination = DB::table(
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
                    ->join(
                        'm_laminations as l',
                        'l.id',
                        '=',
                        'lpcd.lamination_id'
                    )
                    ->join(
                        'm_lamination_sizes as ls',
                        'ls.id',
                        '=',
                        'lpcds.lamination_size_id'
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
                        'pcc.category_id',
                        $validated['category_id']
                    )
                    ->where(
                        'lpcd.lamination_id',
                        $validated['lamination_id']
                    )
                    ->where(
                        'lpcds.lamination_size_id',
                        $laminationSizeId
                    )
                    // ->select([
                    //     'lpcd.price_per_meter',
                    //     'lpcd.production_cost',
                    //     'lpcd.finishing_cost',
                    //     'lpcd.total_cost',
                    //     'lpcd.general_price',
                    //     'lpcd.division_price',
                    //     'lpcd.plain_price',
                    //     'ls.width as lamination_width',
                    //     'ls.length as lamination_length',
                    //     'ls.unit as lamination_unit',
                    // ])
                    ->select([
                        'lpcd.price_per_meter',
                        'lpcd.production_cost',
                        'lpcd.finishing_cost',
                        'lpcd.total_cost',
                        'lpcd.general_price',
                        'lpcd.division_price',
                        'lpcd.plain_price',
                        'ls.width as lamination_width',
                        'ls.length as lamination_length',
                        'ls.unit as lamination_unit',
                        'l.name as lamination_name',
                    ])
                    ->first();

                if (!$lamination) {
                    throw new Exception(
                        'Harga laminasi untuk lokasi, kategori, laminasi, dan ukuran tersebut belum tersedia.'
                    );
                }

                $laminationWidth = !empty($validated['lamination_custom_width'])
                    ? (float) $validated['lamination_custom_width']
                    : (float) $lamination->lamination_width;

                /*
                |--------------------------------------------------------------------------
                | VALIDASI UKURAN LAMINASI
                |--------------------------------------------------------------------------
                */

                $laminationWidth = !empty($validated['lamination_custom_width'])
                    ? (float) $validated['lamination_custom_width']
                    : (float) $lamination->lamination_width;

                if ($laminationWidth < $productWidth) {
                    throw new Exception(
                        'Ukuran produk tidak dapat dimuat pada lebar laminasi.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | PRICE
            |--------------------------------------------------------------------------
            */

            $price = 0;

            switch (strtolower(
                trim(
                    $validated['price_type']
                )
            )) {

                case 'general':

                    $price =
                        (float) $productionCost->general_price;

                    break;


                case 'division':

                    $price =
                        (float) $productionCost->division_price;

                    break;


                case 'plain':

                    $price =
                        (float) $productionCost->plain_price;

                    break;


                default:

                    throw new Exception(
                        'Tipe harga tidak valid.'
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | PRICE LAMINASI
            |--------------------------------------------------------------------------
            */

            $laminationPrice = 0;

            if ($lamination) {

                switch (strtolower(trim($validated['price_type']))) {
                    case 'general':
                        $laminationPrice = (float) $lamination->general_price;
                        break;

                    case 'division':
                        $laminationPrice = (float) $lamination->division_price;
                        break;

                    case 'plain':

                        if ($lamination->plain_price === null) {
                            throw new Exception(
                                'Harga Plain untuk laminasi tersebut belum tersedia.'
                            );
                        }

                        $laminationPrice = (float) $lamination->plain_price;
                        break;

                    default:
                        throw new Exception('Tipe harga tidak valid.');
                }
            }

            /*
            |--------------------------------------------------------------------------
            | LAMINASI AREA
            |--------------------------------------------------------------------------
            */

            $laminationArea = 0;
            $laminationBillingLength = 0;
            $laminationSubtotal = 0;

            if ($lamination) {

                // Luas aktual produk yang dilaminasi
                $laminationArea =
                    ($productLength * $productWidth * $qty) / 10000;

                /*
                |--------------------------------------------------------------------------
                | Laminasi menggunakan panjang produksi sebagai basis harga
                |--------------------------------------------------------------------------
                |
                | Contoh:
                | Panjang produksi = 9 m
                | Harga laminasi   = Rp35.000
                | Subtotal         = 9 × Rp35.000
                |
                */

                // $laminationBillingLength = $productionLength;
                $laminationBillingLength = $productionLength / 100;

                $laminationSubtotal =
                    $laminationBillingLength * $laminationPrice;
            }

            /*
            |--------------------------------------------------------------------------
            | LOCATION-SPECIFIC AREA CALCULATION
            |--------------------------------------------------------------------------
            */

            $locationName =
                strtolower(
                    trim(
                        $location->name
                    )
                );


            if ($locationName === 'graha') {
                $areaCalculation = $this->calculateGrahaArea(
                    $productLength,
                    $productWidth,
                    $qty,
                    $materialWidth,
                    $objectsPerRow,
                    $totalRows,
                    $productionLength
                );
            } elseif ($locationName === 'purwakarta') {
                $areaCalculation = $this->calculatePurwakartaArea(
                    $productLength,
                    $productWidth,
                    $materialWidth,
                    $objectsPerRow,
                    $productionLength
                );
            } elseif ($locationName === 'outsourcing') {
                $areaCalculation = $this->calculateVendorArea(
                    $productLength,
                    $productWidth,
                    $qty,
                    $materialWidth,
                    $objectsPerRow,
                    $totalRows,
                    $productionLength,
                    $material->category_name
                );
            } else {
                throw new Exception(
                    'Perhitungan Large Format untuk lokasi tersebut belum tersedia.'
                );
            }


            /*
        |--------------------------------------------------------------------------
        | AREA
        |--------------------------------------------------------------------------
        */

            $productionWidth =
                $areaCalculation['production_width'];

            $productionArea =
                $areaCalculation['production_area'];


            /*
            |--------------------------------------------------------------------------
            | BILLING AREA
            |--------------------------------------------------------------------------
            */

            $billingArea =
                max(
                    $productionArea,
                    $minimumCharge
                );

            $additionalComponents = [];
            $additionalComponentsSubtotal = 0;
            $additionalComponentsHpp = 0;

            foreach ($validated['additional_components'] ?? [] as $component) {

                // Abaikan baris komponen yang benar-benar kosong
                if (
                    empty($component['type']) &&
                    empty($component['name']) &&
                    empty($component['qty']) &&
                    empty($component['price_type']) &&
                    empty($component['unit_price']) &&
                    empty($component['hpp']) &&
                    empty($component['margin'])
                ) {
                    continue;
                }

                $type = $component['type'];
                $name = trim($component['name']);
                $qty = (int) $component['qty'];
                $priceType = $component['price_type'];

                $unitPrice = 0;
                $hppPerUnit = 0;
                $margin = 0;
                $salePricePerUnit = 0;
                $subtotal = 0;
                $totalHpp = 0;
                $profit = 0;
                $marginPercentage = 0;

                if ($priceType === 'unit_price') {

                    $unitPrice = (float) ($component['unit_price'] ?? 0);

                    $salePricePerUnit = $unitPrice;

                    $subtotal = $qty * $salePricePerUnit;
                } elseif ($priceType === 'hpp_margin') {

                    $hppPerUnit = (float) ($component['hpp'] ?? 0);
                    $margin = (float) ($component['margin'] ?? 0);

                    if ($margin >= 100) {
                        throw new \Exception(
                            'Margin komponen tambahan tidak boleh 100% atau lebih.'
                        );
                    }

                    $salePricePerUnit =
                        $hppPerUnit / (1 - ($margin / 100));

                    $subtotal = $qty * $salePricePerUnit;

                    $totalHpp = $qty * $hppPerUnit;

                    $profit = $subtotal - $totalHpp;

                    $marginPercentage =
                        $subtotal > 0
                        ? ($profit / $subtotal) * 100
                        : 0;

                    $additionalComponentsHpp += $totalHpp;
                }

                $additionalComponentsSubtotal += $subtotal;

                $additionalComponents[] = [
                    'type' => $type,
                    'name' => $name,
                    'qty' => $qty,
                    'price_type' => $priceType,
                    'unit_price' => $unitPrice,
                    'hpp_per_unit' => $hppPerUnit,
                    'margin' => $margin,
                    'sale_price_per_unit' => $salePricePerUnit,
                    'subtotal' => $subtotal,
                    'total_hpp' => $totalHpp,
                    'profit' => $profit,
                    'margin_percentage' => $marginPercentage,
                ];
            }

            $materialSubtotal = $billingArea * $price;

            $subtotal =
                $materialSubtotal
                + $laminationSubtotal
                + $additionalComponentsSubtotal;

            /*
            |--------------------------------------------------------------------------
            | SUBTOTAL
            |--------------------------------------------------------------------------
            */

            /*
            |--------------------------------------------------------------------------
            | DISCOUNT
            |--------------------------------------------------------------------------
            */

            $discountType =
                $validated['discount_type'];

            $discountValue =
                (float) (
                    $validated['discount_value'] ?? 0
                );

            $discount = 0;


            if ($discountType === 'percentage') {

                if ($discountValue > 100) {
                    throw new Exception(
                        'Persentase diskon tidak boleh lebih dari 100%.'
                    );
                }

                $discount =
                    $subtotal *
                    ($discountValue / 100);
            } elseif ($discountType === 'nominal') {

                $discount =
                    $discountValue;
            }


            if ($discount > $subtotal) {
                $discount = $subtotal;
            }


            /*
            |--------------------------------------------------------------------------
            | GRAND TOTAL
            |--------------------------------------------------------------------------
            */

            $grandTotal = $this->roundGrandTotalUpToThousand(
                $subtotal - $discount
            );


            /*
            |--------------------------------------------------------------------------
            | HPP
            |--------------------------------------------------------------------------
            */

            // $hppPerM2 =
            //     (float) $productionCost->total_cost;

            // $materialTotalHpp =
            //     $billingArea * $hppPerM2;

            if ($isOutsourcing) {
                $hppPerM2 = (float) $productionCost->vendor_price;
            } else {
                $hppPerM2 = (float) $productionCost->total_cost;
            }

            $materialTotalHpp = $billingArea * $hppPerM2;

            $laminationHppPerMeter = 0;
            $laminationTotalHpp = 0;

            if ($lamination) {

                $laminationHppPerMeter =
                    (float) $lamination->total_cost;

                $laminationTotalHpp =
                    $laminationBillingLength
                    * $laminationHppPerMeter;
            }

            $totalHpp =
                $materialTotalHpp
                + $laminationTotalHpp
                + $additionalComponentsHpp;

            /*
            |--------------------------------------------------------------------------
            | PROFIT
            |--------------------------------------------------------------------------
            */

            $profit =
                $grandTotal - $totalHpp;


            /*
            |--------------------------------------------------------------------------
            | MARGIN
            |--------------------------------------------------------------------------
            */

            $margin =
                $grandTotal > 0
                ? ($profit / $grandTotal) * 100
                : 0;


            /*
            |--------------------------------------------------------------------------
            | MARKUP
            |--------------------------------------------------------------------------
            */

            $markup =
                $totalHpp > 0
                ? ($profit / $totalHpp) * 100
                : 0;

            return response()->json([
                'success' => true,
                'data' => [

                    /*
                    |--------------------------------------------------------------------------
                    | INFORMASI PRODUKSI
                    |--------------------------------------------------------------------------
                    */

                    'engine_name' =>
                    $engine->name,

                    'location_name' =>
                    $location->name,

                    'vendor_name' =>
                    $vendorName,

                    'category_name' =>
                    $material->category_name,

                    'material_name' =>
                    $material->material_name,

                    'price_type' =>
                    $validated['price_type'],

                    /*
                    |--------------------------------------------------------------------------
                    | PRODUK
                    |--------------------------------------------------------------------------
                    */

                    'qty' =>
                    $qty,

                    'length' =>
                    $productLength,

                    'width' =>
                    $productWidth,

                    'size' =>
                    $productLength
                        . ' × '
                        . $productWidth
                        . ' cm',

                    /*
                    |--------------------------------------------------------------------------
                    | PRODUKSI
                    |--------------------------------------------------------------------------
                    */

                    'material_width' =>
                    $materialWidth,

                    'objects_per_row' =>
                    $objectsPerRow,

                    'total_rows' =>
                    $totalRows,

                    'production_length' =>
                    $productionLength,

                    'production_width' =>
                    $productionWidth,

                    'production_area' =>
                    $productionArea,

                    'minimum_charge' =>
                    $minimumCharge,

                    'billing_area' =>
                    $billingArea,

                    /*
                    |--------------------------------------------------------------------------
                    | MATERIAL
                    |--------------------------------------------------------------------------
                    */

                    'material_price' =>
                    $price,

                    'material_subtotal' =>
                    $materialSubtotal,

                    'material_hpp_per_m2' =>
                    $hppPerM2,

                    'material_total_hpp' =>
                    $materialTotalHpp,

                    /*
                    |--------------------------------------------------------------------------
                    | LAMINASI
                    |--------------------------------------------------------------------------
                    */

                    'lamination' => $lamination
                        ? [

                            'name' =>
                            $lamination->lamination_name,

                            'width' =>
                            $laminationWidth,

                            'length' =>
                            $lamination->lamination_length !== null
                                ? (float) $lamination->lamination_length
                                : null,

                            'unit' =>
                            $lamination->lamination_unit,

                            'price' =>
                            $laminationPrice,

                            'area' =>
                            $laminationArea,

                            'billing_length' =>
                            $laminationBillingLength,

                            'subtotal' =>
                            $laminationSubtotal,

                            'hpp_per_meter' =>
                            $laminationHppPerMeter,

                            'total_hpp' =>
                            $laminationTotalHpp,

                        ]
                        : null,

                    'additional_components' => $additionalComponents,
                    /*
                    |--------------------------------------------------------------------------
                    | DISCOUNT
                    |--------------------------------------------------------------------------
                    */

                    'discount_type' =>
                    $discountType,

                    'discount_value' =>
                    $discountValue,

                    'discount_amount' =>
                    $discount,

                    /*
                    |--------------------------------------------------------------------------
                    | FINANCIAL SUMMARY
                    |--------------------------------------------------------------------------
                    */

                    'subtotal' =>
                    $subtotal,

                    'grand_total' =>
                    $grandTotal,

                    'total_hpp' =>
                    $totalHpp,

                    'profit' =>
                    $profit,

                    'total_profit' =>
                    $profit,

                    'margin' =>
                    $margin,

                    'markup' =>
                    $markup,

                    /*
                    |--------------------------------------------------------------------------
                    | ESTIMATION
                    |--------------------------------------------------------------------------
                    */

                    'estimation_time' =>
                    $validated['estimation_time'] ?? null,

                ],
            ]);
        } catch (Exception $e) {

            return response()->json([

                'success' => false,

                'message' =>
                $e->getMessage(),

            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULATE GRAHA AREA
    |--------------------------------------------------------------------------
    */

    private function calculateGrahaArea(
        float $productLength,
        float $productWidth,
        int $qty,
        float $materialWidth,
        int $objectsPerRow,
        int $totalRows,
        float $productionLength
    ): array {

        $productionWidth =
            $productWidth;

        $productionArea =
            (
                $productLength
                * $productWidth
                * $qty
            ) / 10000;

        return [

            'production_width' =>
            $productionWidth,

            'production_area' =>
            $productionArea,

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | CALCULATE PURWAKARTA AREA
    |--------------------------------------------------------------------------
    */

    private function calculatePurwakartaArea(
        float $productLength,
        float $productWidth,
        float $materialWidth,
        int $objectsPerRow,
        float $productionLength
    ): array {

        if ($objectsPerRow > 1) {

            $productionWidth =
                $productWidth * $objectsPerRow;
        } else {

            $productionWidth =
                $materialWidth;
        }


        $productionArea =
            (
                $productionWidth
                * $productionLength
            ) / 10000;

        return [

            'production_width' =>
            $productionWidth,

            'production_area' =>
            $productionArea,

        ];
    }

    private function calculateVendorArea(
        float $productLength,
        float $productWidth,
        int $qty,
        float $materialWidth,
        int $objectsPerRow,
        int $totalRows,
        float $productionLength,
        string $categoryName
    ): array {

        $productionWidth = $materialWidth;

        if (strtolower(trim($categoryName)) === 'outdoor') {
            $productionArea =
                (
                    $productLength
                    * $productWidth
                    * $qty
                ) / 10000;
        } else {
            $productionArea =
                (
                    $materialWidth
                    * $productionLength
                ) / 10000;
        }

        return [
            'production_width' => $productionWidth,
            'production_area' => $productionArea,
        ];
    }

    private function roundGrandTotalUpToThousand(float $amount): float
    {
        return ceil(round($amount, 2) / 1000) * 1000;
    }

    private function calculateDisplayPurwakarta(
        array $validated,
        Request $request
    ) {
        try {

            /*
            |--------------------------------------------------------------------------
            | DISPLAY CONFIGURATION
            |--------------------------------------------------------------------------
            */

            $configuration = DB::table(
                'm_display_product_configurations as cfg'
            )
                ->join(
                    'm_display_products as dp',
                    'dp.id',
                    '=',
                    'cfg.display_product_id'
                )
                ->where('cfg.id', $validated['display_configuration_id'])
                ->where('cfg.display_product_id', $validated['display_product_id'])
                ->where('cfg.location_id', $validated['location_id'])
                ->whereRaw("LOWER(TRIM(dp.status)) = 'active'")
                ->select(
                    'cfg.id',
                    'cfg.display_product_id',
                    'cfg.engine_id',
                    'cfg.location_id',
                    'cfg.cost_rangka',
                    'cfg.cost_finishing',
                    'cfg.total_cost',
                    'dp.display_name',
                    'dp.length',
                    'dp.width'
                )
                ->first();

            if (!$configuration) {
                throw new Exception(
                    'Konfigurasi produk Display tidak ditemukan atau tidak aktif.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI ENGINE
            |--------------------------------------------------------------------------
            */

            $engine = DB::table('m_engines')
                ->where('id', $configuration->engine_id)
                ->whereRaw("LOWER(TRIM(status)) = 'active'")
                ->first();

            if (!$engine) {
                throw new Exception(
                    'Mesin Display tidak ditemukan atau tidak aktif.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | DISPLAY PRODUCT DATA
            |--------------------------------------------------------------------------
            */

            $qty = (int) $validated['qty'];

            $productLength = (float) $configuration->length;
            $productWidth = (float) $configuration->width;
            $component = null;

            /*
            |--------------------------------------------------------------------------
            | VALIDASI KOMPONEN
            |--------------------------------------------------------------------------
            */

            if (
                ($validated['display_component_type'] ?? null) !== 'frame'
                && !empty($validated['display_component_id'])
            ) {

                $component = DB::table(
                    'm_display_product_component_prices as cpp'
                )
                    ->join(
                        'm_display_product_components as dpc',
                        'dpc.id',
                        '=',
                        'cpp.component_id'
                    )
                    ->where(
                        'cpp.configuration_id',
                        $configuration->id
                    )
                    ->where(
                        'cpp.component_id',
                        $validated['display_component_id']
                    )
                    ->where(
                        'dpc.display_product_id',
                        $configuration->display_product_id
                    )
                    ->select(
                        'cpp.*',
                        'dpc.component_type',
                        'dpc.material_id',
                        'dpc.lamination_id'
                    )
                    ->first();

                if (!$component) {
                    throw new Exception(
                        'Komponen tidak sesuai dengan konfigurasi produk Display.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | HARGA PRODUK DISPLAY
            |--------------------------------------------------------------------------
            */

            $priceQuery = DB::table('m_display_product_prices')
                ->where('display_product_id', $configuration->display_product_id);

            $priceColumn = match (strtolower(trim($validated['price_type']))) {
                'general' => 'general_price',
                'division' => 'division_price',
                'plain' => 'plain_price',
                default => throw new Exception('Tipe harga tidak valid.'),
            };

            $productPrice = $priceQuery->value($priceColumn);

            if ($productPrice === null) {
                throw new Exception(
                    'Harga produk Display untuk tipe harga tersebut belum tersedia.'
                );
            }

            $productPrice = (float) $productPrice;

            /*
            |--------------------------------------------------------------------------
            | HPP KONFIGURASI
            |--------------------------------------------------------------------------
            */

            $hppRangka = $configuration->cost_rangka !== null
                ? (float) $configuration->cost_rangka
                : null;

            $hppFinishing = $configuration->cost_finishing !== null
                ? (float) $configuration->cost_finishing
                : null;

            $hppConfiguration = $configuration->total_cost !== null
                ? (float) $configuration->total_cost
                : null;

            // $hppAvailable = $hppConfiguration !== null;
            $hppAvailable = $hppConfiguration !== null
                && $hppConfiguration > 0;


            /*
            |--------------------------------------------------------------------------
            | KOMPONEN DISPLAY
            |--------------------------------------------------------------------------
            */

            $componentPrice = 0;
            $componentHpp = 0;
            $componentName = null;
            $laminationName = null;

            if (
                !empty($validated['display_component_id']) &&
                ($validated['display_component_type'] ?? null) !== 'frame'
            ) {
                $componentPriceColumn = $priceColumn;

                $componentPrice = $component
                    ? $component->{$componentPriceColumn}
                    : null;

                if ($componentPrice === null) {
                    throw new Exception(
                        'Harga komponen Display belum tersedia.'
                    );
                }

                $componentPrice = (float) $componentPrice;

                $componentName = DB::table('m_display_product_components as dpc')
                    ->leftJoin(
                        'm_materials as m',
                        'm.id',
                        '=',
                        'dpc.material_id'
                    )
                    ->leftJoin(
                        'm_laminations as l',
                        'l.id',
                        '=',
                        'dpc.lamination_id'
                    )
                    ->where('dpc.id', $validated['display_component_id'])
                    ->selectRaw("
                        COALESCE(m.material_name, l.name, dpc.component_type)
                        as component_name
                    ")
                    ->value('component_name');

                if (!empty($component->lamination_id)) {
                    $laminationName = DB::table('m_laminations')
                        ->where('id', $component->lamination_id)
                        ->value('name');
                }

                // Harga jual komponen per unit ditambahkan ke harga produk.
                // $productPrice += $componentPrice;
                $productPrice = $componentPrice;
            }



            /*
|--------------------------------------------------------------------------
| KOMPONEN TAMBAHAN
|--------------------------------------------------------------------------
*/

            $additionalComponents = [];
            $additionalComponentsSubtotal = 0;
            $additionalComponentsHpp = 0;

            foreach ($validated['additional_components'] ?? [] as $componentData) {

                $type = $componentData['type'];
                $name = trim($componentData['name']);
                $componentQty = (int) $componentData['qty'];
                $priceType = $componentData['price_type'];

                $unitPrice = 0;
                $hppPerUnit = 0;
                $componentMargin = 0;
                $salePricePerUnit = 0;
                $componentSubtotal = 0;
                $componentTotalHpp = 0;
                $componentProfit = 0;
                $marginPercentage = 0;

                if ($priceType === 'unit_price') {

                    $unitPrice = (float) ($componentData['unit_price'] ?? 0);

                    $salePricePerUnit = $unitPrice;

                    $componentSubtotal =
                        $componentQty * $salePricePerUnit;
                } elseif ($priceType === 'hpp_margin') {

                    $hppPerUnit = (float) ($componentData['hpp'] ?? 0);
                    $componentMargin = (float) ($componentData['margin'] ?? 0);

                    if ($componentMargin >= 100) {
                        throw new Exception(
                            'Margin komponen tambahan tidak boleh 100% atau lebih.'
                        );
                    }

                    $salePricePerUnit =
                        $hppPerUnit / (1 - ($componentMargin / 100));

                    $componentSubtotal =
                        $componentQty * $salePricePerUnit;

                    $componentTotalHpp =
                        $componentQty * $hppPerUnit;

                    $componentProfit =
                        $componentSubtotal - $componentTotalHpp;

                    $marginPercentage =
                        $componentSubtotal > 0
                        ? ($componentProfit / $componentSubtotal) * 100
                        : 0;

                    $additionalComponentsHpp += $componentTotalHpp;
                }

                $additionalComponentsSubtotal += $componentSubtotal;

                $additionalComponents[] = [
                    'type' => $type,
                    'name' => $name,
                    'qty' => $componentQty,
                    'price_type' => $priceType,
                    'unit_price' => $unitPrice,
                    'hpp_per_unit' => $hppPerUnit,
                    'margin' => $componentMargin,
                    'sale_price_per_unit' => $salePricePerUnit,
                    'subtotal' => $componentSubtotal,
                    'total_hpp' => $componentTotalHpp,
                    'profit' => $componentProfit,
                    'margin_percentage' => $marginPercentage,
                ];
            }

            /*
|--------------------------------------------------------------------------
| SUBTOTAL
|--------------------------------------------------------------------------
*/

            $subtotal =
                ($productPrice * $qty)
                + $additionalComponentsSubtotal;

            // kalo ada perhitungan yang keliru, hapus kode di bawah.
            $materialSubtotal =  ($productPrice * $qty);

            $discountType = $validated['discount_type'];

            $discountValue = (float) ($validated['discount_value'] ?? 0);

            $discount = 0;

            if ($discountType === 'percentage') {

                if ($discountValue > 100) {
                    throw new Exception(
                        'Persentase diskon tidak boleh lebih dari 100%.'
                    );
                }

                $discount = $subtotal * ($discountValue / 100);
            } elseif ($discountType === 'nominal') {

                $discount = $discountValue;
            }

            $discount = min($discount, $subtotal);

            $grandTotal = $this->roundGrandTotalUpToThousand(
                $subtotal - $discount
            );

            /*
            |--------------------------------------------------------------------------
            | HPP DAN PROFIT DISPLAY
            |--------------------------------------------------------------------------
            */


            /*
            |--------------------------------------------------------------------------
            | HPP DAN PROFIT DISPLAY
            |--------------------------------------------------------------------------
            */

            // HPP dihitung hanya jika data HPP konfigurasi tersedia.
            $totalHpp =
                ($hppAvailable ? $hppConfiguration * $qty : 0)
                + $additionalComponentsHpp;

            $profit = $grandTotal - $totalHpp;

            $margin = $grandTotal > 0
                ? ($profit / $grandTotal) * 100
                : 0;

            // Markup tidak ditampilkan jika HPP tidak tersedia
            // atau total HPP bernilai nol.
            $markup = $hppAvailable && $totalHpp > 0
                ? ($profit / $totalHpp) * 100
                : null;

            /*
            |--------------------------------------------------------------------------
            | RESPONSE
            |--------------------------------------------------------------------------
            */

            return response()->json([
                'success' => true,

                'data' => [
                    'engine_name' => $engine->name,
                    'location_name' => 'Purwakarta',
                    'category_name' => 'Display',

                    // 'material_name' => $configuration->display_name
                    //     . ($componentName ? ' —+ ' . $componentName : '')
                    //     . (!empty($laminationName) ? ' + ' . $laminationName : ''),
                    'material_name' => $configuration->display_name
                        . ($componentName ? ' —+ ' . $componentName : '')
                        . (!empty($laminationName) ? ' + ' . $laminationName : ''),
                    'display_product_name' => $configuration->display_name,

                    'display_configuration_id' => $configuration->id,
                    'display_product_id' => $configuration->display_product_id,

                    'display_component_name' => $componentName,
                    'display_component_price' => $componentPrice,

                    'price_type' => $validated['price_type'],

                    'qty' => $qty,

                    'length' => $productLength,
                    'width' => $productWidth,

                    'size' => $productLength . ' x ' . $productWidth . ' cm',

                    'material_price' => $productPrice,
                    'material_price_unit' => 'unit',
                    'material_subtotal' => $materialSubtotal,

                    'hpp_available' => $hppAvailable,

                    'hpp_rangka' => $hppRangka,
                    'hpp_finishing' => $hppFinishing,

                    'hpp_per_unit' => $hppConfiguration,

                    // Display tidak menggunakan perhitungan area Large Format.
                    'material_width' => null,

                    'objects_per_row' => null,
                    'total_rows' => null,

                    'minimum_charge' => 0,
                    'billing_area' => 0,

                    'production_length' => null,
                    'production_width' => null,
                    'production_area' => null,

                    'lamination' => null,
                    'additional_components' => $additionalComponents,
                    'additional_components_subtotal' => $additionalComponentsSubtotal,
                    'additional_components_hpp' => $additionalComponentsHpp,

                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'discount_amount' => $discount,

                    'subtotal' => $subtotal,
                    'grand_total' => $grandTotal,

                    // 'total_hpp' => $totalHpp,
                    'total_hpp' => $totalHpp,

                    'hpp_status' => $hppAvailable
                        ? 'available'
                        : 'unavailable',

                    'profit' => $profit,
                    'total_profit' => $profit,

                    'margin' => $margin,
                    'markup' => $markup,

                    'estimation_time' => $validated['estimation_time'] ?? null,
                ],
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function editLF() {}

    public function createA3plus()
    {
        $data['locations'] = DB::table('m_locations')
            ->whereRaw('LOWER(TRIM(status)) = ?', ['active'])
            ->orderBy('name', 'asc')
            ->get();

        $data['vendors'] = DB::table('m_vendors')
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

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

        // Kategori untuk Digital Print A3+ (Semua kategori kecuali yang berelasi dengan Large Format)
        $data['categories'] = DB::table('m_categories as c')
            ->whereNotIn('c.id', $lfCategoryIds)
            ->orderBy('c.name', 'asc')
            ->get();

        $data['materials'] = DB::table('m_materials as m')
            ->join('m_categories as c', 'c.id', '=', 'm.category_id')
            ->leftJoin('m_digital_production_cost_details as d', 'd.material_id', '=', 'm.id')
            ->leftJoin('m_digital_production_costs as pc', 'pc.id', '=', 'd.digital_production_cost_id')
            ->whereNotIn('m.category_id', $lfCategoryIds)
            ->where('m.status', 'Active')
            ->select(
                'm.id',
                'm.material_name as name',
                'c.id as category_id',
                'c.name as category_name',
                'd.paper_cost',
                'd.click_cost',
                'd.single_print_cost',
                'd.double_print_cost',
                'pc.location_id'
            )
            ->distinct()
            ->orderBy('c.name', 'asc')
            ->orderBy('m.material_name', 'asc')
            ->get()
            ->groupBy('category_name');

        $data['laminations'] = DB::table('m_laminations as l')
            ->leftJoin('m_digital_lamination_production_cost_details as d', 'd.lamination_id', '=', 'l.id')
            ->leftJoin('m_digital_lamination_production_costs as pc', 'pc.id', '=', 'd.digital_lamination_production_cost_id')
            ->where('l.status', 'Active')
            ->where(function ($q) {
                $q->whereRaw('LOWER(l.name) LIKE ?', ['%a3+%'])
                    ->orWhereNotNull('d.id');
            })
            ->select(
                'l.id',
                'l.name',
                'd.paper_cost',
                'd.click_cost',
                'd.single_print_cost',
                'd.double_print_cost',
                'pc.location_id'
            )
            ->distinct()
            ->orderBy('l.name', 'asc')
            ->get();

        $data['cuttings'] = DB::table('m_cuttings')
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        $data['digitalPriceList'] = $this->buildDigitalPriceListData();

        return view('admin.estimation.a3-plus.create')->with($data);
    }

    /**
     * Endpoint AJAX untuk mengambil data Price List Large Format
     */
    public function getPriceListLF(Request $request)
    {
        $locationId = $request->query('location_id');
        $data = $this->buildLargeFormatPriceListData($locationId);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Helper untuk mengambil dan menyusun data Price List Large Format
     */
    private function buildLargeFormatPriceListData($locationId = null)
    {
        $engine = DB::table('m_engines')
            ->whereRaw('LOWER(TRIM(name)) = ?', ['large format'])
            ->first();

        if (!$engine) {
            return [
                'categories' => [],
                'details' => [],
            ];
        }

        $pcQuery = DB::table('m_production_costs as pc')
            ->join('m_production_cost_locations as pcl', 'pcl.production_cost_id', '=', 'pc.id')
            ->where('pc.engine_id', $engine->id);

        if (!empty($locationId)) {
            $locPcIds = (clone $pcQuery)->where('pcl.location_id', $locationId)->pluck('pc.id')->toArray();
            if (empty($locPcIds)) {
                $locPcIds = $pcQuery->pluck('pc.id')->toArray();
            }
        } else {
            $locPcIds = $pcQuery->pluck('pc.id')->toArray();
        }

        if (empty($locPcIds)) {
            return [
                'categories' => [],
                'details' => [],
            ];
        }

        $details = DB::table('m_production_cost_details as pcd')
            ->join('m_materials as m', 'm.id', '=', 'pcd.material_id')
            ->join('m_categories as c', 'c.id', '=', 'm.category_id')
            ->leftJoin('m_vendors as v', 'v.id', '=', 'pcd.vendor_id')
            ->whereIn('pcd.production_cost_id', $locPcIds)
            ->select(
                'pcd.id',
                'pcd.production_cost_id',
                'c.id as category_id',
                'c.name as category_name',
                'pcd.material_id',
                'm.material_name',
                'pcd.vendor_id',
                'v.name as vendor_name',
                'pcd.vendor_price',
                'pcd.price_per_meter',
                'pcd.production_cost',
                'pcd.finishing_cost',
                'pcd.total_cost',
                'pcd.general_price',
                'pcd.division_price',
                'pcd.plain_price'
            )
            ->orderBy('c.name')
            ->orderBy('m.material_name')
            ->get();

        $detailIds = $details->pluck('id')->toArray();
        $widthsGrouped = collect();

        if (!empty($detailIds)) {
            $widths = DB::table('m_production_cost_detail_widths as pcdw')
                ->join('m_material_sizes as ms', 'ms.id', '=', 'pcdw.material_size_id')
                ->whereIn('pcdw.production_cost_detail_id', $detailIds)
                ->select('pcdw.production_cost_detail_id', 'ms.width', 'ms.unit')
                ->orderBy('ms.width')
                ->get();
            $widthsGrouped = $widths->groupBy('production_cost_detail_id');
        }

        $categories = $details->pluck('category_name')->unique()->values()->toArray();

        $formattedDetails = [];
        foreach ($details as $d) {
            $wList = [];
            if (isset($widthsGrouped[$d->id])) {
                foreach ($widthsGrouped[$d->id] as $w) {
                    $val = (float) $w->width;
                    $wList[] = $val . ' ' . $w->unit;
                }
            }

            $formattedDetails[] = [
                'id' => $d->id,
                'category_id' => $d->category_id,
                'category_name' => $d->category_name,
                'material_id' => $d->material_id,
                'material_name' => $d->material_name,
                'vendor_name' => $d->vendor_name,
                'vendor_price' => (float) $d->vendor_price,
                'price_per_meter' => (float) $d->price_per_meter,
                'production_cost' => (float) $d->production_cost,
                'finishing_cost' => (float) $d->finishing_cost,
                'total_cost' => (float) $d->total_cost,
                'general_price' => (float) $d->general_price,
                'division_price' => (float) $d->division_price,
                'plain_price' => (float) $d->plain_price,
                'widths' => $wList,
                'widths_label' => !empty($wList) ? implode(', ', $wList) : '-',
            ];
        }

        return [
            'categories' => $categories,
            'details' => $formattedDetails,
        ];
    }

    /**
     * Endpoint AJAX untuk mengambil data Price List Digital Print A3+
     */
    public function getPriceListA3plus(Request $request)
    {
        $locationId = $request->query('location_id');
        $data = $this->buildDigitalPriceListData($locationId);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Helper untuk mengambil dan menyusun data Price List Digital Print A3+
     */
    private function buildDigitalPriceListData($locationId = null)
    {
        $query = DB::table('m_digital_production_costs as pc')
            ->join('m_engines as e', 'e.id', '=', 'pc.engine_id')
            ->whereRaw('LOWER(TRIM(e.name)) = ?', ['digital print a3+']);

        if (!empty($locationId)) {
            $cost = (clone $query)->where('pc.location_id', $locationId)->select('pc.id', 'pc.location_id')->first();
            if (!$cost) {
                $cost = $query->select('pc.id', 'pc.location_id')->first();
            }
        } else {
            $cost = $query->select('pc.id', 'pc.location_id')->first();
        }

        if (!$cost) {
            return [
                'tierLabels' => [],
                'priceGroups' => [],
                'categories' => [],
                'categoryMarkups' => [],
                'details' => [],
            ];
        }

        $costDetails = DB::table('m_digital_production_cost_details as d')
            ->join('m_materials as m', 'm.id', '=', 'd.material_id')
            ->join('m_categories as c', 'c.id', '=', 'm.category_id')
            ->where('d.digital_production_cost_id', $cost->id)
            ->where('m.status', 'Active')
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
            ->orderBy('c.name', 'asc')
            ->orderBy('m.material_name', 'asc')
            ->get();

        $costPrices = DB::table('m_digital_production_cost_prices')
            ->whereIn('digital_production_cost_detail_id', $costDetails->pluck('id'))
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

        $materialSizes = DB::table('m_material_sizes')
            ->whereIn('material_id', $costDetails->pluck('material_id')->unique()->values())
            ->orderBy('id')
            ->get()
            ->groupBy('material_id');

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

        $formatNum = function ($val) {
            return rtrim(rtrim(number_format((float) $val, 2, '.', ''), '0'), '.');
        };

        $categoryMarkups = [];

        $costDetails->each(function ($detail) use ($costPrices, $priceGroups, $tierLabels, $materialSizes, $formatNum, &$categoryMarkups) {
            $detailPrices = ($costPrices->get($detail->id) ?? collect())->keyBy(function ($p) {
                return $p->price_type . '|' . $p->print_type . '|' . $p->quantity_tier;
            });

            $detail->size_label = ($materialSizes->get($detail->material_id) ?? collect())
                ->map(function ($s) use ($formatNum) {
                    return $formatNum($s->width) . ' x ' . $formatNum($s->length) . ' ' . $s->unit;
                })
                ->implode(', ');

            $detail->prices = [];

            foreach ($priceGroups as $gKey => [$pType, $prType]) {
                foreach (array_keys($tierLabels) as $t) {
                    $p = $detailPrices->get($pType . '|' . $prType . '|' . $t);
                    $detail->prices[$gKey][$t] = $p ? (float) $p->selling_price : null;

                    if (!isset($categoryMarkups[$detail->category_name][$gKey][$t]) && $p) {
                        $categoryMarkups[$detail->category_name][$gKey][$t] = $formatNum($p->markup_percentage) . '%';
                    }
                }
            }
        });

        foreach ($costDetails->pluck('category_name')->unique() as $catName) {
            foreach (array_keys($priceGroups) as $gKey) {
                foreach (array_keys($tierLabels) as $t) {
                    if (!isset($categoryMarkups[$catName][$gKey][$t])) {
                        $categoryMarkups[$catName][$gKey][$t] = '-';
                    }
                }
            }
        }

        return [
            'tierLabels' => $tierLabels,
            'priceGroups' => $priceGroups,
            'categories' => $costDetails->pluck('category_name')->unique()->values()->toArray(),
            'categoryMarkups' => $categoryMarkups,
            'details' => $costDetails->values()->toArray(),
        ];
    }

    /**
     * Mengambil daftar material aktif untuk Digital Print A3+ berdasarkan Lokasi, Kategori, dan Warna Cetak
     */
    public function getMaterialsA3plus(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|integer|exists:m_locations,id',
            'category_id' => 'nullable|integer|exists:m_categories,id',
            'print_color' => 'nullable|in:fc,bw',
        ]);

        try {
            $isDisplay = false;
            if (!empty($validated['category_id'])) {
                $category = DB::table('m_categories')->where('id', $validated['category_id'])->first();
                if ($category && stripos($category->name, 'display') !== false) {
                    $isDisplay = true;
                }
            }

            // =========================================================================
            // KHUSUS KATEGORI DISPLAY PADA MESIN DIGITAL PRINT A3+
            // =========================================================================
            if ($isDisplay) {
                $engine = DB::table('m_engines')->where('name', 'ILIKE', '%digital print%')->first();

                $displayComponents = DB::table('m_display_product_configurations as dpcfg')
                    ->join('m_display_products as dp', 'dp.id', '=', 'dpcfg.display_product_id')
                    ->join('m_display_product_component_prices as cpp', 'cpp.configuration_id', '=', 'dpcfg.id')
                    ->join('m_display_product_components as dpc', 'dpc.id', '=', 'cpp.component_id')
                    ->leftJoin('m_materials as m', 'm.id', '=', 'dpc.material_id')
                    ->leftJoin('m_laminations as lam', 'lam.id', '=', 'dpc.lamination_id')
                    ->where('dpcfg.location_id', $validated['location_id'])
                    ->when($engine, function ($q) use ($engine) {
                        $q->where('dpcfg.engine_id', $engine->id);
                    })
                    ->selectRaw("
                        dpc.id AS id,
                        dpcfg.id AS configuration_id,
                        dp.id AS display_product_id,
                        dp.display_name,
                        dp.package_qty,
                        dpcfg.sheet_qty,
                        dpcfg.cost_rangka,
                        dpcfg.cost_finishing,
                        cpp.general_price,
                        cpp.general_price_2_side,
                        cpp.division_price,
                        cpp.division_price_2_side,
                        dpc.material_id,
                        dpc.lamination_id,
                        m.material_name,
                        lam.name AS lamination_name,
                        'Display' AS category_name,
                        CONCAT(
                            dp.display_name,
                            ' — ',
                            m.material_name,
                            CASE 
                                WHEN lam.name IS NOT NULL THEN CONCAT(
                                    ' + ',
                                    CASE 
                                        WHEN (lam.name ILIKE '%doff%' OR lam.name ILIKE '%glossy%')
                                        THEN 'Laminasi Doff / Glossy A3+'
                                        ELSE lam.name
                                    END
                                )
                                ELSE ' (Tanpa Laminasi)'
                            END
                        ) AS name
                    ")
                    ->orderBy('dp.display_name', 'asc')
                    ->orderBy('m.material_name', 'asc')
                    ->get();

                $data = $displayComponents->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'category_id' => null,
                        'category_name' => 'Display',
                        'is_display' => true,
                        'configuration_id' => $item->configuration_id,
                        'display_product_id' => $item->display_product_id,
                        'display_name' => $item->display_name,
                        'package_qty' => (int) ($item->package_qty ?? 100),
                        'sheet_qty' => (int) ($item->sheet_qty ?? 4),
                        'cost_rangka' => (float) $item->cost_rangka,
                        'cost_finishing' => (float) $item->cost_finishing,
                        'general_price' => (float) $item->general_price,
                        'general_price_2_side' => (float) $item->general_price_2_side,
                        'division_price' => (float) $item->division_price,
                        'division_price_2_side' => (float) $item->division_price_2_side,
                        'paper_cost' => 0,
                        'click_cost' => 0,
                        'single_print_cost' => 0,
                        'double_print_cost' => 0,
                    ];
                });

                return response()->json([
                    'success' => true,
                    'data' => $data,
                ]);
            }

            // =========================================================================
            // KATEGORI STANDAR DIGITAL PRINT A3+
            // =========================================================================
            $query = DB::table('m_materials as m')
                ->join('m_categories as c', 'c.id', '=', 'm.category_id')
                ->join('m_digital_production_cost_details as d', 'd.material_id', '=', 'm.id')
                ->join('m_digital_production_costs as pc', 'pc.id', '=', 'd.digital_production_cost_id')
                ->where('pc.location_id', $validated['location_id'])
                ->where('m.status', 'Active');

            if (!empty($validated['category_id'])) {
                $query->where('m.category_id', $validated['category_id']);
            }

            if (!empty($validated['print_color'])) {
                if ($validated['print_color'] === 'bw') {
                    $query->whereRaw('LOWER(TRIM(c.name)) = ?', ['bw']);
                } else {
                    $query->whereRaw('LOWER(TRIM(c.name)) != ?', ['bw']);
                }
            }

            $materials = $query->select(
                'm.id',
                'm.material_name as name',
                'c.id as category_id',
                'c.name as category_name',
                'd.paper_cost',
                'd.click_cost',
                'd.single_print_cost',
                'd.double_print_cost'
            )
            ->distinct()
            ->orderBy('c.name', 'asc')
            ->orderBy('m.material_name', 'asc')
            ->get();

            return response()->json([
                'success' => true,
                'data' => $materials,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data material Digital Print A3+: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hitung Estimasi Digital Print A3+ (Tanpa Insert ke DB)
     */
    public function storeA3plus(Request $request)
    {
        // Filter & sanitasi komponen tambahan
        $additionalComponents = $request->input('additional_components', []);
        if (is_array($additionalComponents)) {
            $additionalComponents = array_values(
                array_filter(
                    $additionalComponents,
                    function ($component) {
                        return !empty($component['type']) ||
                            !empty($component['name']) ||
                            !empty($component['qty']) ||
                            !empty($component['unit_price']) ||
                            !empty($component['hpp']);
                    }
                )
            );
            foreach ($additionalComponents as &$comp) {
                if (isset($comp['unit_price'])) {
                    $comp['unit_price'] = str_replace(['.', ','], '', (string) $comp['unit_price']);
                }
                if (isset($comp['hpp'])) {
                    $comp['hpp'] = str_replace(['.', ','], '', (string) $comp['hpp']);
                }
            }
            unset($comp);
            $request->merge([
                'additional_components' => $additionalComponents ?: null,
            ]);
        }

        $categoryId = $request->input('category_id');
        $isDisplay = false;
        if (!empty($categoryId)) {
            $cat = DB::table('m_categories')->where('id', $categoryId)->first();
            if ($cat && stripos($cat->name, 'display') !== false) {
                $isDisplay = true;
            }
        }

        $rules = [
            'location_id' => 'required|integer|exists:m_locations,id',
            'category_id' => 'nullable|integer|exists:m_categories,id',
            'vendor_id' => 'nullable|integer|exists:m_vendors,id',
            'price_type' => 'required|in:general,division',
            'production_mode' => $isDisplay ? 'nullable|in:layout,direct,display' : 'required|in:layout,direct',
            'material_id' => 'required|integer',
            'print_color' => $isDisplay ? 'nullable|in:fc,bw' : 'required|in:fc,bw',
            'print_side' => 'required|in:1,2',
            'length' => 'nullable|numeric|min:0.1',
            'width' => 'nullable|numeric|min:0.1',
            'qty' => 'nullable|integer|min:1',
            'sheet_qty' => 'nullable|integer|min:1',
            'lamination_type' => 'nullable|string',
            'cutting_type' => 'nullable|string',
            'potong_size_override' => 'nullable|string',
            'discount_type' => 'required|in:none,percentage,nominal',
            'discount_value' => 'nullable',
            'estimation_time' => 'required|integer|min:1',
            'additional_components' => 'nullable|array',
            'additional_components.*.type' => 'required|string',
            'additional_components.*.name' => 'required|string',
            'additional_components.*.qty' => 'required|integer|min:1',
            'additional_components.*.price_type' => 'required|in:unit_price,hpp_margin',
            'additional_components.*.unit_price' => 'nullable|numeric|min:0|required_if:additional_components.*.price_type,unit_price',
            'additional_components.*.hpp' => 'nullable|numeric|min:0|required_if:additional_components.*.price_type,hpp_margin',
            'additional_components.*.margin' => 'nullable|numeric|min:0|max:99.99|required_if:additional_components.*.price_type,hpp_margin',
        ];

        $validated = $request->validate($rules);

        // Cabang Khusus Kategori Display Digital Print A3+
        if ($isDisplay) {
            return $this->calculateDisplayA3plus($validated, $request);
        }

        try {
            // 1. ENGINE & LOKASI
            $engine = DB::table('m_engines')
                ->whereRaw('LOWER(TRIM(name)) = ?', ['digital print a3+'])
                ->first();

            $location = DB::table('m_locations')->where('id', $validated['location_id'])->first();
            $vendorName = null;
            if (!empty($validated['vendor_id'])) {
                $vendorName = DB::table('m_vendors')->where('id', $validated['vendor_id'])->value('name');
            }

            // 2. MATERIAL & HPP DASAR
            $material = DB::table('m_materials as m')
                ->join('m_categories as c', 'c.id', '=', 'm.category_id')
                ->where('m.id', $validated['material_id'])
                ->select('m.*', 'c.name as category_name')
                ->first();

            $costDetail = DB::table('m_digital_production_cost_details as d')
                ->join('m_digital_production_costs as pc', 'pc.id', '=', 'd.digital_production_cost_id')
                ->where('d.material_id', $validated['material_id'])
                ->where('pc.location_id', $validated['location_id'])
                ->select('d.*')
                ->first();

            if (!$costDetail) {
                $costDetail = DB::table('m_digital_production_cost_details')
                    ->where('material_id', $validated['material_id'])
                    ->first();
            }

            $paperCost = $costDetail ? (float) $costDetail->paper_cost : 800.0;
            $clickCost = $costDetail ? (float) $costDetail->click_cost : 1500.0;
            $isDouble = ($validated['print_side'] == '2');
            $printCostUnit = $paperCost + ($clickCost * ($isDouble ? 2 : 1));

            // 3. IMPOSISI LAYOUT & TOTAL LEMBAR CETAK
            $mode = $validated['production_mode'];
            $sheetPrintableL = 47.5; // cm (Area cetak efektif A3+)
            $sheetPrintableW = 31.5; // cm

            if ($mode === 'layout') {
                $productLength = (float) ($validated['length'] ?? 9.0);
                $productWidth = (float) ($validated['width'] ?? 5.5);
                $qty = (int) ($validated['qty'] ?? 1);

                $upNormal = floor($sheetPrintableL / $productLength) * floor($sheetPrintableW / $productWidth);
                $upRotated = floor($sheetPrintableL / $productWidth) * floor($sheetPrintableW / $productLength);
                $upCount = max(1, (int) max($upNormal, $upRotated));
                $orientation = ($upRotated > $upNormal) ? 'Rotated' : 'Normal';

                $totalSheets = (int) ceil($qty / $upCount);
                $finishedSize = $productLength . ' × ' . $productWidth . ' cm';
            } else {
                $totalSheets = (int) ($validated['sheet_qty'] ?? 1);
                $qty = $totalSheets;
                $upCount = 1;
                $orientation = 'Direct Sheet';
                $productLength = 48.5;
                $productWidth = 32.5;
                $finishedSize = 'Direct Sheet (32.5 × 48.5 cm)';
            }

            // 4. DETERMINASI TIER KUANTITI
            $tier = 'tier_1';
            if ($totalSheets >= 301) {
                $tier = 'tier_500';
            } elseif ($totalSheets >= 101) {
                $tier = 'tier_300';
            } elseif ($totalSheets >= 51) {
                $tier = 'tier_100';
            } elseif ($totalSheets >= 6) {
                $tier = 'tier_6_50';
            } elseif ($totalSheets >= 2) {
                $tier = 'tier_2_5';
            }

            // 5. HARGA JUAL CETAK (PRINT SELLING PRICE DARI MASTER TIER)
            $printType = $isDouble ? 'double' : 'single';
            $priceType = $validated['price_type']; // general | division

            $sellingPriceRow = null;
            if ($costDetail) {
                $sellingPriceRow = DB::table('m_digital_production_cost_prices')
                    ->where('digital_production_cost_detail_id', $costDetail->id)
                    ->where('price_type', $priceType)
                    ->where('print_type', $printType)
                    ->where('quantity_tier', $tier)
                    ->first();
            }

            if ($sellingPriceRow) {
                $printUnitPrice = (float) $sellingPriceRow->selling_price;
            } else {
                // Fallback jika tier belum digenerate di master
                $basePrice = 4000;
                if ($totalSheets >= 50) $basePrice = 3500;
                if ($totalSheets >= 100) $basePrice = 3000;
                if ($totalSheets >= 300) $basePrice = 2500;
                if ($isDouble) $basePrice *= 1.8;
                if ($priceType === 'division') $basePrice *= 0.8;
                $printUnitPrice = $basePrice;
            }

            $printSubtotal = $printUnitPrice * $totalSheets;
            $printTotalHpp = $printCostUnit * $totalSheets;

            // 6. FINISHING LAMINASI (DARI MASTER LAMINASI DIGITAL A3+)
            $laminationVal = $validated['lamination_type'] ?? 'none';
            $laminationData = null;
            $laminationSubtotal = 0.0;
            $laminationTotalHpp = 0.0;

            if ($laminationVal && $laminationVal !== 'none') {
                $parts = explode('_', $laminationVal);
                $lamId = (int) $parts[0];
                $lamSide = (isset($parts[1]) && $parts[1] === '2') ? 'double' : 'single';

                $lamMaster = DB::table('m_laminations')->where('id', $lamId)->first();
                $lamName = $lamMaster ? $lamMaster->name : 'Laminasi A3+';
                $lamLabel = $lamName . ' (' . ($lamSide === 'double' ? '2 Sisi' : '1 Sisi') . ')';

                $lamDetail = DB::table('m_digital_lamination_production_cost_details as d')
                    ->join('m_digital_lamination_production_costs as pc', 'pc.id', '=', 'd.digital_lamination_production_cost_id')
                    ->where('d.lamination_id', $lamId)
                    ->where('pc.location_id', $validated['location_id'])
                    ->select('d.*')
                    ->first();

                if (!$lamDetail) {
                    $lamDetail = DB::table('m_digital_lamination_production_cost_details')
                        ->where('lamination_id', $lamId)
                        ->first();
                }

                $lamHppUnit = 0.0;
                if ($lamDetail) {
                    $lamHppUnit = ($lamSide === 'double') ? (float) $lamDetail->double_print_cost : (float) $lamDetail->single_print_cost;
                } else {
                    $lamHppUnit = ($lamSide === 'double') ? 1200.0 : 700.0;
                }

                $lamPriceRow = null;
                if ($lamDetail) {
                    $lamPriceRow = DB::table('m_digital_lamination_production_cost_prices')
                        ->where('digital_lamination_production_cost_detail_id', $lamDetail->id)
                        ->where('price_type', $priceType)
                        ->where('print_type', $lamSide)
                        ->where('quantity_tier', $tier)
                        ->first();
                }

                if ($lamPriceRow) {
                    $lamUnitPrice = (float) $lamPriceRow->selling_price;
                } else {
                    $lamUnitPrice = ($lamSide === 'double') ? 2000.0 : 1200.0;
                    if ($priceType === 'division') $lamUnitPrice *= 0.8;
                }

                $laminationSubtotal = $lamUnitPrice * $totalSheets;
                $laminationTotalHpp = $lamHppUnit * $totalSheets;

                $laminationData = [
                    'name' => $lamLabel,
                    'unit_price' => $lamUnitPrice,
                    'subtotal' => $laminationSubtotal,
                    'hpp_unit' => $lamHppUnit,
                    'total_hpp' => $laminationTotalHpp,
                ];
            }

            // 7. FINISHING CUTTING
            $cuttingVal = $validated['cutting_type'] ?? 'none';
            $cuttingData = null;
            $cuttingSubtotal = 0.0;
            $cuttingTotalHpp = 0.0;
            $isPotong = false;

            if ($cuttingVal && $cuttingVal !== 'none') {
                $cuttingUnitPrice = 0.0;
                $cuttingHppUnit = 0.0;
                $cuttingName = 'Tanpa Cutting';

                $isPotong = ($cuttingVal === 'potong');
                if (!$isPotong && is_numeric($cuttingVal)) {
                    $chkCutting = DB::table('m_cuttings')->where('id', (int) $cuttingVal)->first();
                    if ($chkCutting && strtolower(trim($chkCutting->name)) === 'potong') {
                        $isPotong = true;
                    }
                }

                if ($isPotong) {
                    // Opsi Potong Guillotine (Menggunakan Master Ongkos Potong Minimal)
                    // 1. Tentukan Kelas Ukuran Jadi (A3, A4, A5, <=A6)
                    $potongOverride = trim((string) ($validated['potong_size_override'] ?? ''));
                    $potongSizeClass = null;

                    // Support format standar (A3, A4, A5, <=A6) dan format legacy (LARGE, MEDIUM, SMALL)
                    if (in_array(strtoupper($potongOverride), ['A3', 'A4', 'A5', '<=A6'], true)) {
                        $potongSizeClass = strtoupper($potongOverride);
                        if ($potongOverride === '<=a6' || $potongOverride === '<=A6') {
                            $potongSizeClass = '<=A6';
                        }
                    } elseif (strtoupper($potongOverride) === 'LARGE') {
                        $potongSizeClass = 'A4';
                    } elseif (strtoupper($potongOverride) === 'MEDIUM') {
                        $potongSizeClass = 'A5';
                    } elseif (strtoupper($potongOverride) === 'SMALL') {
                        $potongSizeClass = '<=A6';
                    }

                    // Jika otomatis (atau belum ditentukan), hitung dari luas produk jadi (P x L dalam cm)
                    if (!$potongSizeClass) {
                        $areaCm2 = (float) $productLength * (float) $productWidth;
                        if ($areaCm2 > 623.7) {
                            $potongSizeClass = 'A3';
                        } elseif ($areaCm2 > 310.8) {
                            $potongSizeClass = 'A4';
                        } elseif ($areaCm2 > 155.4) {
                            $potongSizeClass = 'A5';
                        } else {
                            $potongSizeClass = '<=A6';
                        }
                    }

                    $sizeLabel = match ($potongSizeClass) {
                        'A3' => 'A3',
                        'A4' => 'A4',
                        'A5' => 'A5',
                        '<=A6' => '≤ A6',
                        default => $potongSizeClass
                    };

                    $cuttingName = 'Potong Guillotine (' . $sizeLabel . ')';

                    // 2. Cari Header Master Ongkos Potong berdasarkan lokasi & engine Digital Print A3+
                    $potongHeader = DB::table('m_digital_potong_production_costs')
                        ->where('location_id', $validated['location_id'])
                        ->where('engine_id', $engine->id)
                        ->first();

                    if (!$potongHeader) {
                        $potongHeader = DB::table('m_digital_potong_production_costs')
                            ->where('engine_id', $engine->id)
                            ->first();
                    }

                    $potongCat = null;
                    if ($potongHeader && $material && !empty($material->category_id)) {
                        $potongCat = DB::table('m_digital_potong_production_cost_categories')
                            ->where('digital_potong_production_cost_id', $potongHeader->id)
                            ->where('category_id', $material->category_id)
                            ->first();
                    }

                    if ($potongHeader && !$potongCat) {
                        $potongCat = DB::table('m_digital_potong_production_cost_categories')
                            ->where('digital_potong_production_cost_id', $potongHeader->id)
                            ->first();
                    }

                    $potongPriceRow = null;
                    $potongDivRow = null;

                    if ($potongCat) {
                        $potongPriceRow = DB::table('m_digital_potong_production_cost_prices')
                            ->where('digital_potong_production_cost_category_id', $potongCat->id)
                            ->where('size_class', $potongSizeClass)
                            ->where('price_type', $priceType)
                            ->first();

                        $potongDivRow = DB::table('m_digital_potong_production_cost_prices')
                            ->where('digital_potong_production_cost_category_id', $potongCat->id)
                            ->where('size_class', $potongSizeClass)
                            ->where('price_type', 'division')
                            ->first();
                    }

                    if ($potongPriceRow) {
                        $minPriceSelling = (float) $potongPriceRow->min_price;
                    } else {
                        $minPriceSelling = match ($potongSizeClass) {
                            'A3' => 15000.0,
                            'A4' => 20000.0,
                            'A5' => 25000.0,
                            '<=A6' => 35000.0,
                            default => 20000.0
                        };
                        if ($priceType === 'division') {
                            $minPriceSelling *= 0.8;
                        }
                    }

                    // HPP Potong: di sample app harga jual = HPP (cost-only), atau tarif internal divisi
                    $minPriceHpp = $potongDivRow ? (float) $potongDivRow->min_price : $minPriceSelling;

                    $cuttingSubtotal = $minPriceSelling;
                    $cuttingTotalHpp = $minPriceHpp;
                    $cuttingUnitPrice = $totalSheets > 0 ? ($cuttingSubtotal / $totalSheets) : $cuttingSubtotal;
                    $cuttingHppUnit = $totalSheets > 0 ? ($cuttingTotalHpp / $totalSheets) : $cuttingTotalHpp;
                } else {
                    // Jasa Potong Dinamis dari Master Production Cost Finishing Cutting
                    $cuttingObj = null;
                    if (is_numeric($cuttingVal)) {
                        $cuttingObj = DB::table('m_cuttings')->where('id', (int) $cuttingVal)->first();
                    } else {
                        // Kompatibilitas dengan string bawaan (kiss, die, perforasi)
                        if ($cuttingVal === 'kiss') {
                            $cuttingObj = DB::table('m_cuttings')->whereRaw('LOWER(name) LIKE ?', ['%kiss%'])->first();
                        } elseif ($cuttingVal === 'die') {
                            $cuttingObj = DB::table('m_cuttings')->whereRaw('LOWER(name) LIKE ?', ['%die%'])->first();
                        } elseif ($cuttingVal === 'perforasi') {
                            $cuttingObj = DB::table('m_cuttings')->whereRaw('LOWER(name) LIKE ?', ['%perforasi%'])->first();
                        } else {
                            $cuttingObj = DB::table('m_cuttings')->whereRaw('LOWER(name) = ?', [strtolower(trim($cuttingVal))])->first();
                        }
                    }

                    if ($cuttingObj) {
                        $cuttingName = $cuttingObj->name;

                        // 1. Cari detail ongkos potong di master berdasarkan cutting_id, lokasi, dan kategori material
                        $cutDetail = null;
                        if ($material && !empty($material->category_id)) {
                            $cutDetail = DB::table('m_digital_cutting_production_cost_details as d')
                                ->join('m_digital_cutting_production_costs as pc', 'pc.id', '=', 'd.digital_cutting_production_cost_id')
                                ->where('d.cutting_id', $cuttingObj->id)
                                ->where('pc.location_id', $validated['location_id'])
                                ->where('d.category_id', $material->category_id)
                                ->select('d.*')
                                ->first();
                        }

                        // Fallback 1: Cari berdasarkan cutting_id & location_id (apabila kategori material ini belum spesifik)
                        if (!$cutDetail) {
                            $cutDetail = DB::table('m_digital_cutting_production_cost_details as d')
                                ->join('m_digital_cutting_production_costs as pc', 'pc.id', '=', 'd.digital_cutting_production_cost_id')
                                ->where('d.cutting_id', $cuttingObj->id)
                                ->where('pc.location_id', $validated['location_id'])
                                ->select('d.*')
                                ->first();
                        }

                        // Fallback 2: Cari detail cutting di master mana saja
                        if (!$cutDetail) {
                            $cutDetail = DB::table('m_digital_cutting_production_cost_details as d')
                                ->where('d.cutting_id', $cuttingObj->id)
                                ->select('d.*')
                                ->first();
                        }

                        // 2. Ambil harga dari tabel harga berdasarkan tier kuantiti dan price_type (general / division)
                        $cutPriceRow = null;
                        if ($cutDetail) {
                            $cutPriceRow = DB::table('m_digital_cutting_production_cost_prices')
                                ->where('digital_cutting_production_cost_detail_id', $cutDetail->id)
                                ->where('price_type', $priceType)
                                ->where('quantity_tier', $tier)
                                ->first();
                        }

                        if ($cutPriceRow) {
                            $cuttingUnitPrice = (float) $cutPriceRow->price;
                        } else {
                            // Fallback tarif jika belum diset di master
                            if (stripos($cuttingName, 'kiss') !== false) {
                                $cuttingUnitPrice = 2500.0;
                            } elseif (stripos($cuttingName, 'die') !== false) {
                                $cuttingUnitPrice = 3000.0;
                            } elseif (stripos($cuttingName, 'perforasi') !== false) {
                                $cuttingUnitPrice = 1500.0;
                            } else {
                                $cuttingUnitPrice = 2000.0;
                            }
                            if ($priceType === 'division') {
                                $cuttingUnitPrice *= 0.8;
                            }
                        }

                        // HPP mesin potong terstandarisasi Rp 1.000 / lembar A3+ (sesuai referensi perhitungan)
                        $cuttingHppUnit = 1000.0;

                        $cuttingSubtotal = $cuttingUnitPrice * $totalSheets;
                        $cuttingTotalHpp = $cuttingHppUnit * $totalSheets;
                    }
                }

                $cuttingData = [
                    'name' => $cuttingName,
                    'unit_price' => $cuttingUnitPrice,
                    'subtotal' => $cuttingSubtotal,
                    'hpp_unit' => $cuttingHppUnit,
                    'total_hpp' => $cuttingTotalHpp,
                    'is_potong' => $isPotong,
                ];
            }

            // 8. KOMPONEN TAMBAHAN
            $additionalComponents = $validated['additional_components'] ?? [];
            $extraSubtotal = 0.0;
            $extraTotalHpp = 0.0;
            $formattedAdditionalComponents = [];

            if (is_array($additionalComponents)) {
                foreach ($additionalComponents as $comp) {
                    $cType = $comp['type'] ?? 'Material';
                    $cName = trim((string) ($comp['name'] ?? ''));
                    $cQty = (int) ($comp['qty'] ?? 1);
                    $priceType = $comp['price_type'] ?? 'unit_price';

                    if (empty($cName) || $cQty <= 0) {
                        continue;
                    }

                    $unitPrice = 0.0;
                    $hppPerUnit = 0.0;
                    $componentMargin = 0.0;
                    $salePricePerUnit = 0.0;
                    $componentSubtotal = 0.0;
                    $componentTotalHpp = 0.0;
                    $componentProfit = 0.0;
                    $marginPercentage = 0.0;

                    if ($priceType === 'unit_price') {
                        $unitPrice = (float) str_replace(['.', ','], '', (string) ($comp['unit_price'] ?? 0));
                        $salePricePerUnit = $unitPrice;
                        $componentSubtotal = $cQty * $salePricePerUnit;
                        $componentTotalHpp = 0.0;
                        $componentProfit = $componentSubtotal;
                        $marginPercentage = $componentSubtotal > 0 ? 100.0 : 0.0;
                    } elseif ($priceType === 'hpp_margin') {
                        $hppPerUnit = (float) str_replace(['.', ','], '', (string) ($comp['hpp'] ?? 0));
                        $componentMargin = (float) ($comp['margin'] ?? 0);

                        if ($componentMargin >= 100) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Margin komponen tambahan tidak boleh 100% atau lebih.'
                            ], 422);
                        }

                        $divisor = 1 - ($componentMargin / 100);
                        $salePricePerUnit = $divisor > 0 ? ($hppPerUnit / $divisor) : 0.0;
                        $componentSubtotal = $cQty * $salePricePerUnit;
                        $componentTotalHpp = $cQty * $hppPerUnit;
                        $componentProfit = $componentSubtotal - $componentTotalHpp;
                        $marginPercentage = $componentSubtotal > 0
                            ? ($componentProfit / $componentSubtotal) * 100
                            : 0.0;

                        $extraTotalHpp += $componentTotalHpp;
                    }

                    $extraSubtotal += $componentSubtotal;

                    $formattedAdditionalComponents[] = [
                        'type' => $cType,
                        'name' => $cName,
                        'qty' => $cQty,
                        'price_type' => $priceType,
                        'unit_price' => $unitPrice,
                        'hpp_per_unit' => $hppPerUnit,
                        'margin' => $componentMargin,
                        'sale_price_per_unit' => $salePricePerUnit,
                        'subtotal' => $componentSubtotal,
                        'total_hpp' => $componentTotalHpp,
                        'profit' => $componentProfit,
                        'margin_percentage' => $marginPercentage,
                    ];
                }
            }

            // 9. FINANCIAL SUMMARY
            $subtotal = $printSubtotal + $laminationSubtotal + $cuttingSubtotal + $extraSubtotal;

            $discountType = $validated['discount_type'];
            $discountRaw = (string) ($validated['discount_value'] ?? '0');
            $discountValue = (float) str_replace(['.', ','], '', $discountRaw);
            $discountAmount = 0.0;

            if ($discountType === 'percentage') {
                $discountAmount = $subtotal * ($discountValue / 100);
            } elseif ($discountType === 'nominal') {
                $discountAmount = $discountValue;
            }

            $grandTotal = $this->roundGrandTotalUpToThousand(
                max(0.0, $subtotal - $discountAmount)
            );
            $totalHpp = $printTotalHpp + $laminationTotalHpp + $cuttingTotalHpp + $extraTotalHpp;

            $profit = $grandTotal - $totalHpp;
            $margin = $grandTotal > 0 ? ($profit / $grandTotal) * 100 : 0.0;
            $markup = $totalHpp > 0 ? ($profit / $totalHpp) * 100 : 0.0;
            $pricePerPcs = $qty > 0 ? ($grandTotal / $qty) : 0.0;

            $finishingParts = [];
            if ($laminationData) $finishingParts[] = $laminationData['name'];
            if ($cuttingData) $finishingParts[] = $cuttingData['name'];
            $finishingSummary = count($finishingParts) > 0 ? implode(' · ', $finishingParts) : 'Tanpa Finishing';

            return response()->json([
                'success' => true,
                'data' => [
                    'engine_name' => 'Digital Print A3+',
                    'location_name' => $location ? $location->name : '-',
                    'vendor_name' => $vendorName,
                    'price_type' => $priceType === 'division' ? 'Harga Divisi' : 'Harga Umum',
                    'material_name' => $material ? $material->material_name : '-',
                    'category_name' => $material ? $material->category_name : '-',
                    'print_color' => $validated['print_color'] === 'fc' ? 'Full Color' : 'Hitam Putih',
                    'print_side' => $isDouble ? '2 Sisi' : '1 Sisi',
                    'color_side' => ($validated['print_color'] === 'fc' ? 'Full Color' : 'Hitam Putih') . ' · ' . ($isDouble ? '2 Sisi' : '1 Sisi'),
                    'production_mode' => $mode === 'layout' ? 'Hitung Layout' : 'Direct Sheet',
                    'finished_size' => $finishedSize,
                    'imposition_info' => $mode === 'layout' ? ($upCount . ' pcs (' . $orientation . ')') : 'Direct Sheet',
                    'up_count' => $upCount,
                    'orientation' => $orientation,
                    'qty' => $qty,
                    'total_sheets' => $totalSheets,
                    'quantity_tier' => $tier,
                    'unit_price_sheet' => $printUnitPrice,
                    'print_subtotal' => $printSubtotal,
                    'print_cost_unit' => $printCostUnit,
                    'print_total_hpp' => $printTotalHpp,
                    'print_hpp' => $printTotalHpp,
                    'lamination' => $laminationData,
                    'lamination_name' => $laminationData ? $laminationData['name'] : 'Tanpa Laminasi',
                    'lamination_unit_price' => $laminationData ? $laminationData['unit_price'] : 0,
                    'lamination_subtotal' => $laminationSubtotal,
                    'lamination_hpp' => $laminationTotalHpp,
                    'cutting' => $cuttingData,
                    'cutting_name' => $cuttingData ? $cuttingData['name'] : 'Tanpa Cutting',
                    'cutting_unit_price' => $cuttingData ? $cuttingData['unit_price'] : 0,
                    'cutting_subtotal' => $cuttingSubtotal,
                    'cutting_hpp' => $cuttingTotalHpp,
                    'is_potong' => $isPotong,
                    'finishing_summary' => $finishingSummary,
                    'additional_components' => $formattedAdditionalComponents,
                    'extra_subtotal' => $extraSubtotal,
                    'extra_total_hpp' => $extraTotalHpp,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'discount_amount' => $discountAmount,
                    'subtotal' => $subtotal,
                    'grand_total' => $grandTotal,
                    'price_per_pcs' => $pricePerPcs,
                    'total_hpp' => $totalHpp,
                    'profit' => $profit,
                    'margin' => $margin,
                    'markup' => $markup,
                    'lead_time' => ($validated['estimation_time'] ?? 1) . ' hari kerja',
                    'estimation_time' => $validated['estimation_time'] ?? 1,
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung estimasi Digital Print A3+: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hitung Estimasi Display untuk Digital Print A3+
     */
    private function calculateDisplayA3plus(array $validated, Request $request)
    {
        try {
            $location = DB::table('m_locations')->where('id', $validated['location_id'])->first();
            $vendorName = null;
            if (!empty($validated['vendor_id'])) {
                $vendorName = DB::table('m_vendors')->where('id', $validated['vendor_id'])->value('name');
            }

            $engine = DB::table('m_engines')->where('name', 'ILIKE', '%digital print%')->first();

            // Cari component display
            $comp = DB::table('m_display_product_components as dpc')
                ->join('m_display_product_component_prices as cpp', 'cpp.component_id', '=', 'dpc.id')
                ->join('m_display_product_configurations as dpcfg', 'dpcfg.id', '=', 'cpp.configuration_id')
                ->join('m_display_products as dp', 'dp.id', '=', 'dpcfg.display_product_id')
                ->leftJoin('m_materials as m', 'm.id', '=', 'dpc.material_id')
                ->leftJoin('m_laminations as lam', 'lam.id', '=', 'dpc.lamination_id')
                ->where('dpc.id', $validated['material_id'])
                ->where('dpcfg.location_id', $validated['location_id'])
                ->when($engine, function ($q) use ($engine) {
                    $q->where('dpcfg.engine_id', $engine->id);
                })
                ->selectRaw("
                    dpc.id,
                    dpc.material_id,
                    dpc.lamination_id,
                    dpcfg.id as configuration_id,
                    dpcfg.sheet_qty,
                    dpcfg.cost_rangka,
                    dpcfg.cost_finishing,
                    dp.id as display_product_id,
                    dp.display_name,
                    dp.package_qty,
                    cpp.general_price,
                    cpp.general_price_2_side,
                    cpp.division_price,
                    cpp.division_price_2_side,
                    m.material_name,
                    lam.name as lamination_name
                ")
                ->first();

            if (!$comp) {
                return response()->json([
                    'success' => false,
                    'message' => 'Komponen produk Display A3+ tidak ditemukan untuk lokasi ini.'
                ], 422);
            }

            $isDouble = ($validated['print_side'] == '2');
            $priceType = $validated['price_type']; // general | division

            // Tentukan harga per paket berdasarkan tipe harga & sisi cetak
            if ($priceType === 'division') {
                $pkgPrice = $isDouble ? (float) $comp->division_price_2_side : (float) $comp->division_price;
            } else {
                $pkgPrice = $isDouble ? (float) $comp->general_price_2_side : (float) $comp->general_price;
            }

            $packageQty = (int) ($comp->package_qty ?? 100);
            if ($packageQty <= 0) $packageQty = 100;

            $packageCount = (int) ($validated['qty'] ?? 1);
            if ($packageCount <= 0) $packageCount = 1;

            $totalPcs = $packageCount * $packageQty;
            $sheetQtyPerPkg = (int) ($comp->sheet_qty ?? 4);
            if ($sheetQtyPerPkg <= 0) $sheetQtyPerPkg = 4;

            $totalSheets = $packageCount * $sheetQtyPerPkg;

            $printSubtotal = $pkgPrice * $packageCount;

            // Hitung Komponen Tambahan
            $additionalComponents = $validated['additional_components'] ?? [];
            $extraSubtotal = 0.0;
            $extraTotalHpp = 0.0;
            $formattedAdditionalComponents = [];

            if (is_array($additionalComponents)) {
                foreach ($additionalComponents as $compItem) {
                    $cType = $compItem['type'] ?? 'Material';
                    $cName = trim((string) ($compItem['name'] ?? ''));
                    $cQty = (int) ($compItem['qty'] ?? 1);
                    $compPriceType = $compItem['price_type'] ?? 'unit_price';

                    if (empty($cName) || $cQty <= 0) continue;

                    if ($compPriceType === 'unit_price') {
                        $uPrice = (float) str_replace(['.', ','], '', (string) ($compItem['unit_price'] ?? 0));
                        $cSubtotal = $cQty * $uPrice;
                        $formattedAdditionalComponents[] = [
                            'type' => $cType,
                            'name' => $cName,
                            'qty' => $cQty,
                            'price_type' => $compPriceType,
                            'unit_price' => $uPrice,
                            'hpp_per_unit' => 0,
                            'margin' => 0,
                            'sale_price_per_unit' => $uPrice,
                            'subtotal' => $cSubtotal,
                            'total_hpp' => 0,
                            'profit' => $cSubtotal,
                            'margin_percentage' => 100.0,
                        ];
                        $extraSubtotal += $cSubtotal;
                    } elseif ($compPriceType === 'hpp_margin') {
                        $hppUnit = (float) str_replace(['.', ','], '', (string) ($compItem['hpp'] ?? 0));
                        $marginVal = (float) ($compItem['margin'] ?? 0);
                        $divisor = 1 - ($marginVal / 100);
                        $saleUnit = $divisor > 0 ? ($hppUnit / $divisor) : 0;
                        $cSubtotal = $cQty * $saleUnit;
                        $cTotalHpp = $cQty * $hppUnit;
                        $cProfit = $cSubtotal - $cTotalHpp;
                        $formattedAdditionalComponents[] = [
                            'type' => $cType,
                            'name' => $cName,
                            'qty' => $cQty,
                            'price_type' => $compPriceType,
                            'unit_price' => 0,
                            'hpp_per_unit' => $hppUnit,
                            'margin' => $marginVal,
                            'sale_price_per_unit' => $saleUnit,
                            'subtotal' => $cSubtotal,
                            'total_hpp' => $cTotalHpp,
                            'profit' => $cProfit,
                            'margin_percentage' => $cSubtotal > 0 ? ($cProfit / $cSubtotal) * 100 : 0,
                        ];
                        $extraSubtotal += $cSubtotal;
                        $extraTotalHpp += $cTotalHpp;
                    }
                }
            }

            // Hitung HPP Dasar Display
            $costBoxKN = (float) $comp->cost_rangka * $packageCount;
            $costDieCut = (float) $comp->cost_finishing * $packageCount;

            $matCostDetail = null;
            if (!empty($comp->material_id)) {
                $matCostDetail = DB::table('m_digital_production_cost_details as d')
                    ->join('m_digital_production_costs as pc', 'pc.id', '=', 'd.digital_production_cost_id')
                    ->where('d.material_id', $comp->material_id)
                    ->where('pc.location_id', $validated['location_id'])
                    ->select('d.*')
                    ->first();
            }
            $paperCost = $matCostDetail ? (float) $matCostDetail->paper_cost : 800.0;
            $clickCost = $matCostDetail ? (float) $matCostDetail->click_cost : 1500.0;
            $printHppPerSheet = $paperCost + ($clickCost * ($isDouble ? 2 : 1));
            $printTotalHpp = $printHppPerSheet * $totalSheets;

            $lamTotalHpp = 0.0;
            if (!empty($comp->lamination_id)) {
                $lamCostDetail = DB::table('m_digital_lamination_production_cost_details as d')
                    ->join('m_digital_lamination_production_costs as pc', 'pc.id', '=', 'd.digital_lamination_production_cost_id')
                    ->where('d.lamination_id', $comp->lamination_id)
                    ->where('pc.location_id', $validated['location_id'])
                    ->select('d.*')
                    ->first();
                $lamCostPerSheet = $lamCostDetail ? (float) $lamCostDetail->single_print_cost : 700.0;
                $lamTotalHpp = $lamCostPerSheet * $totalSheets;
            }

            $totalHpp = $costBoxKN + $costDieCut + $printTotalHpp + $lamTotalHpp + $extraTotalHpp;

            $subtotal = $printSubtotal + $extraSubtotal;

            $discountType = $validated['discount_type'];
            $discountRaw = (string) ($validated['discount_value'] ?? '0');
            $discountValue = (float) str_replace(['.', ','], '', $discountRaw);
            $discountAmount = 0.0;

            if ($discountType === 'percentage') {
                $discountAmount = $subtotal * ($discountValue / 100);
            } elseif ($discountType === 'nominal') {
                $discountAmount = $discountValue;
            }

            $grandTotal = $this->roundGrandTotalUpToThousand(max(0.0, $subtotal - $discountAmount));
            $profit = $grandTotal - $totalHpp;
            $margin = $grandTotal > 0 ? ($profit / $grandTotal) * 100 : 0.0;
            $markup = $totalHpp > 0 ? ($profit / $totalHpp) * 100 : 0.0;
            $pricePerPcs = $totalPcs > 0 ? ($grandTotal / $totalPcs) : 0.0;

            $lamNameDisplay = $comp->lamination_name;
            if ($lamNameDisplay && (stripos($lamNameDisplay, 'doff') !== false || stripos($lamNameDisplay, 'glossy') !== false)) {
                $lamNameDisplay = 'Laminasi Doff / Glossy A3+';
            }

            $displayNameFull = $comp->display_name . ' — ' . ($comp->material_name ?? 'Material') . ($lamNameDisplay ? ' + ' . $lamNameDisplay : ' (Tanpa Laminasi)');

            return response()->json([
                'success' => true,
                'data' => [
                    'engine_name' => 'Digital Print A3+',
                    'location_name' => $location ? $location->name : '-',
                    'vendor_name' => $vendorName,
                    'price_type' => $priceType === 'division' ? 'Harga Divisi' : 'Harga Umum',
                    'material_name' => $displayNameFull,
                    'category_name' => 'Display',
                    'print_color' => 'Full Color',
                    'print_side' => $isDouble ? '2 Sisi' : '1 Sisi',
                    'color_side' => 'Full Color · ' . ($isDouble ? '2 Sisi' : '1 Sisi'),
                    'production_mode' => 'Paket Display (' . $packageQty . ' pcs/box)',
                    'finished_size' => 'Ukuran Jadi ' . $comp->display_name,
                    'imposition_info' => $packageQty . ' pcs/box (' . $sheetQtyPerPkg . ' Lembar A3+)',
                    'up_count' => 25,
                    'orientation' => 'Normal',
                    'qty' => $totalPcs,
                    'total_sheets' => $totalSheets,
                    'quantity_tier' => 'Paket Display',
                    'unit_price_sheet' => $pkgPrice,
                    'print_subtotal' => $printSubtotal,
                    'print_cost_unit' => $totalSheets > 0 ? ($totalHpp / $totalSheets) : 0,
                    'print_total_hpp' => $totalHpp,
                    'print_hpp' => $totalHpp,
                    'lamination' => null,
                    'lamination_name' => $lamNameDisplay ? ($lamNameDisplay . ' (Termasuk)') : 'Tanpa Laminasi',
                    'lamination_unit_price' => 0,
                    'lamination_subtotal' => 0,
                    'lamination_hpp' => $lamTotalHpp,
                    'cutting' => [
                        'name' => 'Die Cut (Termasuk)',
                        'unit_price' => 0,
                        'subtotal' => 0,
                        'hpp_unit' => 0,
                        'total_hpp' => $costDieCut,
                        'is_potong' => false,
                    ],
                    'cutting_name' => 'Die Cut (Termasuk Paket)',
                    'cutting_unit_price' => 0,
                    'cutting_subtotal' => 0,
                    'cutting_hpp' => $costDieCut,
                    'is_potong' => false,
                    'finishing_summary' => 'Termasuk Box KN & Die Cut' . ($lamNameDisplay ? ' · ' . $lamNameDisplay : ''),
                    'additional_components' => $formattedAdditionalComponents,
                    'extra_subtotal' => $extraSubtotal,
                    'extra_total_hpp' => $extraTotalHpp,
                    'discount_type' => $discountType,
                    'discount_value' => $discountValue,
                    'discount_amount' => $discountAmount,
                    'subtotal' => $subtotal,
                    'grand_total' => $grandTotal,
                    'price_per_pcs' => $pricePerPcs,
                    'total_hpp' => $totalHpp,
                    'profit' => $profit,
                    'margin' => $margin,
                    'markup' => $markup,
                    'lead_time' => ($validated['estimation_time'] ?? 1) . ' hari kerja',
                    'estimation_time' => $validated['estimation_time'] ?? 1,
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung estimasi Display Digital Print A3+: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getLaminations(Request $request)
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

    public function getLaminationSizes(Request $request)
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

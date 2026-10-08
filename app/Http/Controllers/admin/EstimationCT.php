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

            $grandTotal =
                $subtotal - $discount;


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

            $grandTotal = $subtotal - $discount;

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
        return view('admin.estimation.a3-plus.create');
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

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Exports\DisplayPricingRulesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DisplayPricingRulesImport;

class DisplayPricingRuleCT extends Controller
{
    public function index()
    {
        $data['locations'] = DB::table('m_locations')
            ->where('name', '!=', 'Outsourcing')
            ->orderBy('name')->where('status', 'Active')->get();

        $data['categories'] = DB::table('m_categories')
            ->orderBy('name')->get();

        $data['engines'] = DB::table('m_engines')
            ->orderBy('name')->where('status', 'Active')->get();

        $data['vendors'] = DB::table('m_vendors')
            ->orderBy('name')->where('status', 'Active')->get();

        return view('admin.display-pricing-rule.index')->with($data);
    }

    public function data(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | TOTAL SEMUA DATA
    |--------------------------------------------------------------------------
    */

        $totalRecords = DB::table('m_display_pricing_rules')
            ->select(
                'engine_id',
                'location_id',
                'category_id'
            )
            ->groupBy(
                'engine_id',
                'location_id',
                'category_id'
            )
            ->get()
            ->count();

        /*
    |--------------------------------------------------------------------------
    | QUERY UTAMA
    |--------------------------------------------------------------------------
    */

        $query = DB::table('m_display_pricing_rules as a')
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
            ->select(
                'd.id as engine_id',
                'd.name as engine_name',
                'b.id as category_id',
                'b.name as category_name',
                'c.id as location_id',
                'c.name as location_name',
                DB::raw('COUNT(a.id) as jumlah_markup')
            )
            ->groupBy(
                'd.id',
                'd.name',
                'b.id',
                'b.name',
                'c.id',
                'c.name'
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
                        'LOWER(c.name) LIKE ?',
                        ['%' . $search . '%']
                    )
                    ->orWhereRaw(
                        'LOWER(b.name) LIKE ?',
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

    public function store(Request $request)
    {
        $request->validate([
            'headers' => [
                'required',
                'array',
                'min:1',
            ],

            'headers.*.engine_id' => [
                'required',
                'integer',
                'exists:m_engines,id',
            ],

            'headers.*.location_id' => [
                'required',
                'integer',
                'exists:m_locations,id',
            ],

            'headers.*.category_id' => [
                'required',
                'integer',
                'exists:m_categories,id',
            ],

            'headers.*.pricing_rules' => [
                'required',
                'array',
                'min:1',
            ],

            'headers.*.pricing_rules.*.price_type' => [
                'required',
                'in:general,division,plain',
            ],

            'headers.*.pricing_rules.*.markup_percentage' => [
                'required',
                'numeric',
                'min:0',
                'max:999.99',
            ],

            'headers.*.pricing_rules.*.rounding_value' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'headers.*.pricing_rules.*.status' => [
                'required',
                'in:Active,Inactive',
            ],
        ]);

        try {
            DB::beginTransaction();

            $headers = $request->input('headers', []);

            /*
         * Cek duplikasi:
         * 1. Di dalam request yang sedang disubmit
         * 2. Dengan data yang sudah ada di database
         */
            $combinations = [];

            foreach ($headers as $headerIndex => $header) {

                $engineId = $header['engine_id'];
                $locationId = $header['location_id'];
                $categoryId = $header['category_id'];

                foreach ($header['pricing_rules'] as $ruleIndex => $rule) {

                    $priceType = $rule['price_type'];

                    /*
                 * Key kombinasi untuk pengecekan duplicate
                 */
                    $combinationKey = implode('|', [
                        $engineId,
                        $locationId,
                        $categoryId,
                        $priceType,
                    ]);

                    /*
                 * Duplicate di dalam request yang sama
                 */
                    if (isset($combinations[$combinationKey])) {
                        DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' => 'Terdapat tipe harga yang duplikat pada kombinasi Mesin, Lokasi, dan Kategori yang sama.',
                        ], 422);
                    }

                    $combinations[$combinationKey] = true;

                    /*
                 * Duplicate dengan data yang sudah ada di database
                 */
                    $exists = DB::table('m_display_pricing_rules')
                        ->where('engine_id', $engineId)
                        ->where('location_id', $locationId)
                        ->where('category_id', $categoryId)
                        ->where('price_type', $priceType)
                        ->exists();

                    if ($exists) {
                        DB::rollBack();

                        return response()->json([
                            'success' => false,
                            'message' => 'Tipe harga sudah terdaftar untuk kombinasi Mesin, Lokasi, dan Kategori tersebut.',
                        ], 422);
                    }

                    /*
                 * Simpan child markup
                 */
                    DB::table('m_display_pricing_rules')->insert([
                        'price_type' => $priceType,
                        'markup_percentage' => $rule['markup_percentage'],
                        'rounding_value' => $rule['rounding_value'],
                        'status' => $rule['status'],
                        'engine_id' => $engineId,
                        'location_id' => $locationId,
                        'category_id' => $categoryId,
                        'created_by' => Auth::user()->name,
                        'created_at' => now(),
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Markup Display berhasil ditambahkan.',
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan Markup Display.',
            ], 500);
        }
    }

    public function destroy(
        $engine_id,
        $location_id,
        $category_id
    ) {
        try {

            DB::transaction(function () use (
                $engine_id,
                $location_id,
                $category_id
            ) {

                /*
            |--------------------------------------------------------------------------
            | AMBIL DATA MARKUP DISPLAY
            |--------------------------------------------------------------------------
            */

                $pricingRules = DB::table('m_display_pricing_rules')
                    ->where('engine_id', $engine_id)
                    ->where('location_id', $location_id)
                    ->where('category_id', $category_id)
                    ->get();


                /*
            |--------------------------------------------------------------------------
            | CEK DATA
            |--------------------------------------------------------------------------
            */

                if ($pricingRules->isEmpty()) {

                    throw new Exception(
                        'Data markup Display tidak ditemukan.'
                    );
                }


                /*
            |--------------------------------------------------------------------------
            | HAPUS SELURUH MARKUP DISPLAY
            |--------------------------------------------------------------------------
            |
            | Satu group terdiri dari:
            |
            | - engine
            | - location
            | - category
            |
            | Seluruh child pricing rule dalam group tersebut
            | dihapus sekaligus.
            |
            */

                DB::table('m_display_pricing_rules')
                    ->where('engine_id', $engine_id)
                    ->where('location_id', $location_id)
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
                'message' => 'Markup Display berhasil dihapus.'
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function details(
        $engine_id,
        $location_id,
        $category_id
    ) {
        $pricingRules = DB::table('m_display_pricing_rules as a')
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
            ->select(
                'a.id',
                'a.engine_id',
                'a.location_id',
                'a.category_id',
                'a.price_type',
                'a.markup_percentage',
                'a.rounding_value',
                'a.status',
                'e.name as engine_name',
                'b.name as location_name',
                'c.name as category_name'
            )
            ->where('a.engine_id', $engine_id)
            ->where('a.location_id', $location_id)
            ->where('a.category_id', $category_id)
            ->orderBy('a.id', 'asc')
            ->get();

        if ($pricingRules->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data markup Display tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $pricingRules
        ]);
    }

    public function edit(
        $engine_id,
        $location_id,
        $category_id
    ) {
        $pricingRules = DB::table('m_display_pricing_rules as a')
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
            ->select(
                'a.id',
                'a.engine_id',
                'a.location_id',
                'a.category_id',
                'a.price_type',
                'a.markup_percentage',
                'a.rounding_value',
                'a.status',
                'e.name as engine_name',
                'b.name as location_name',
                'c.name as category_name'
            )
            ->where('a.engine_id', $engine_id)
            ->where('a.location_id', $location_id)
            ->where('a.category_id', $category_id)
            ->orderBy('a.id', 'asc')
            ->get();

        if ($pricingRules->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data markup Display tidak ditemukan.'
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
                'Data markup Display wajib diisi.',

                'pricing_rules.array' =>
                'Format data markup Display tidak valid.',

                'pricing_rules.min' =>
                'Minimal harus terdapat satu markup Display.',

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


        /*
    |--------------------------------------------------------------------------
    | Ambil Pricing Rules Dari Request
    |--------------------------------------------------------------------------
    */

        $pricingRules = $request->input('pricing_rules');


        /*
    |--------------------------------------------------------------------------
    | Validasi Duplikat Price Type Dalam Request
    |--------------------------------------------------------------------------
    |
    | Satu konfigurasi hanya boleh memiliki satu:
    |
    | - general
    | - division
    | - plain
    |
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
                $category_id,
                $pricingRules,
                $requestedTypes,
                $engine,
                $location,
                $category
            ) {


                /*
            |--------------------------------------------------------------------------
            | Ambil Existing Rules
            |--------------------------------------------------------------------------
            |
            | Identitas konfigurasi Display:
            |
            | engine + location + category
            |
            |--------------------------------------------------------------------------
            */

                $existingRules = DB::table(
                    'm_display_pricing_rules'
                )
                    ->where('engine_id', $engine_id)
                    ->where('location_id', $location_id)
                    ->where('category_id', $category_id)
                    ->get()
                    ->keyBy('price_type');


                /*
            |--------------------------------------------------------------------------
            | Hapus Pricing Rule Yang Tidak Lagi Dikirim
            |--------------------------------------------------------------------------
            |
            | Jika markup lama tidak dikirim lagi dari frontend,
            | berarti markup tersebut dihapus melalui modal Edit.
            |
            |--------------------------------------------------------------------------
            */

                foreach ($existingRules as $priceType => $existingRule) {

                    if (!isset($requestedTypes[$priceType])) {

                        DB::table('m_display_pricing_rules')
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

                        DB::table('m_display_pricing_rules')
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

                        DB::table('m_display_pricing_rules')
                            ->insert([

                                'engine_id' =>
                                $engine_id,

                                'location_id' =>
                                $location_id,

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
            });


            return response()->json([
                'success' => true,
                'message' =>
                'Markup Display berhasil diperbarui.',
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function export()
    {
        return Excel::download(
            new DisplayPricingRulesExport(),
            'display_pricing_rules.xlsx'
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

            $import = new DisplayPricingRulesImport();

            Excel::import(
                $import,
                $request->file('file')
            );

            return response()->json([
                'success' => true,
                'message' =>
                $import->importedRows .
                    ' data markup Display berhasil diimport.',
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

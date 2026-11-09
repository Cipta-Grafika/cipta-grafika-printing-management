<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Exception;
use App\Exports\DisplayPricingRulesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\DisplayPricingRulesImport;

class DisplayPricingRuleCT extends Controller
{
    private function displayCategory(): object
    {
        $category = DB::table('m_categories')
            ->select('id', 'name')
            ->where('name', 'Display')
            ->first();

        if ($category === null) {
            abort(500, 'Master kategori Display tidak ditemukan.');
        }

        return $category;
    }

    private function displayCategoryForId($categoryId): object
    {
        $category = $this->displayCategory();

        if ((int) $categoryId !== (int) $category->id) {
            abort(404);
        }

        return $category;
    }

    public function index()
    {
        $data['locations'] = DB::table('m_locations')
            ->where('name', '!=', 'Outsourcing')
            ->orderBy('name')->where('status', 'Active')->get();

        $data['displayCategory'] = $this->displayCategory();

        $data['engines'] = DB::table('m_engines')
            ->orderBy('name')->where('status', 'Active')->get();

        $data['vendors'] = DB::table('m_vendors')
            ->orderBy('name')->where('status', 'Active')->get();

        $data['filterEngines'] = DB::table('m_engines as e')
            ->join('m_display_pricing_rules as r', 'r.engine_id', '=', 'e.id')
            ->select('e.id', 'e.name')->distinct()->orderBy('e.name')->get();
        $data['filterLocations'] = DB::table('m_locations as l')
            ->join('m_display_pricing_rules as r', 'r.location_id', '=', 'l.id')
            ->select('l.id', 'l.name')->distinct()->orderBy('l.name')->get();

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

        foreach (['engine_id' => 'a.engine_id', 'location_id' => 'a.location_id'] as $filter => $column) {
            $value = $request->input($filter);
            if ($value !== null && $value !== '' && $value !== 'all') {
                $query->where($column, $value);
            }
        }
        $search = trim((string) $request->input('search.value', ''));

        if ($search !== '') {
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

        $start = max(0, (int) $request->input('start', 0));
        $length = min(100, max(1, (int) $request->input('length', 10)));

        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA
    |--------------------------------------------------------------------------
    */

        $columns = [2 => 'd.name', 3 => 'c.name', 4 => 'b.name', 5 => 'jumlah_markup'];
        $column = $columns[(int) $request->input('order.0.column', -1)] ?? null;
        $direction = $request->input('order.0.dir', 'asc');
        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }
        if ($column) {
            $query->orderBy($column, $direction);
        }
        $pricingRules = $query->orderBy('d.name')->orderBy('c.name')->orderBy('b.name')
            ->offset($start)
            ->limit($length)
            ->get();
        foreach ($pricingRules as $row) {
            $row->member_ids = DB::table('m_display_pricing_rules')
                ->where('engine_id', $row->engine_id)->where('location_id', $row->location_id)
                ->where('category_id', $row->category_id)->orderBy('id')->pluck('id')
                ->map(fn ($id) => (int) $id)->all();
        }

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
        $displayCategory = $this->displayCategory();

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
                $categoryId = $displayCategory->id;

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
        $this->displayCategoryForId($category_id);
        $ids = DB::table('m_display_pricing_rules')->where('engine_id', $engine_id)
            ->where('location_id', $location_id)->where('category_id', $category_id)
            ->orderBy('id')->pluck('id')->map(fn ($id) => (int) $id)->all();
        if (!$ids) {
            return response()->json(['success' => false, 'message' => 'Data markup Display tidak ditemukan.'], 404);
        }
        return $this->deleteGroups([[
            'engine_id' => $engine_id, 'location_id' => $location_id,
            'category_id' => $category_id, 'member_ids' => $ids,
        ]]);
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'groups' => ['required', 'array', 'min:1', 'max:1000'],
            'groups.*.engine_id' => ['required', 'integer', 'min:1'],
            'groups.*.location_id' => ['required', 'integer', 'min:1'],
            'groups.*.category_id' => ['required', 'integer', 'min:1'],
            'groups.*.member_ids' => ['required', 'array', 'min:1', 'max:1000'],
            'groups.*.member_ids.*' => ['required', 'integer', 'min:1'],
        ]);
        return $this->deleteGroups($validated['groups']);
    }

    private function deleteGroups(array $groups)
    {
        return DB::transaction(function () use ($groups) {
            $categoryId = $this->displayCategory()->id;
            $ids = [];
            foreach ($groups as $group) {
                if ((int) $group['category_id'] !== (int) $categoryId) {
                    return response()->json(['success' => false, 'message' => 'Kategori Display tidak valid. Tidak ada data yang dihapus.'], 422);
                }
                $expected = array_map('intval', $group['member_ids']);
                sort($expected);
                $current = DB::table('m_display_pricing_rules')->where('engine_id', $group['engine_id'])
                    ->where('location_id', $group['location_id'])->where('category_id', $categoryId)
                    ->orderBy('id')->lockForUpdate()->pluck('id')->map(fn ($id) => (int) $id)->all();
                if (!$current || $current !== $expected) {
                    return response()->json(['success' => false, 'message' => 'Kelompok pricing rule berubah atau tidak ditemukan. Tidak ada data yang dihapus.'], 422);
                }
                if (DB::table('m_display_product_configurations')->where('engine_id', $group['engine_id'])
                    ->where('location_id', $group['location_id'])->exists()) {
                    return response()->json(['success' => false, 'message' => 'Pricing rule masih digunakan oleh konfigurasi produk Display. Tidak ada data yang dihapus.'], 422);
                }
                $ids = array_merge($ids, $current);
            }
            if (count($ids) !== count(array_unique($ids))) {
                return response()->json(['success' => false, 'message' => 'Pilihan pricing rule duplikat. Tidak ada data yang dihapus.'], 422);
            }
            foreach (Schema::getTables() as $table) {
                if ($table['name'] === 'm_display_pricing_rules') {
                    continue;
                }
                $tableName = $table['schema_qualified_name'];
                $columns = Schema::hasColumn($tableName, 'display_pricing_rule_id') ? ['display_pricing_rule_id'] : [];
                foreach (Schema::getForeignKeys($tableName) as $foreignKey) {
                    if ($foreignKey['foreign_table'] === 'm_display_pricing_rules') {
                        foreach ($foreignKey['foreign_columns'] as $index => $column) {
                            if ($column === 'id') {
                                $columns[] = $foreignKey['columns'][$index];
                            }
                        }
                    }
                }
                foreach (array_unique($columns) as $column) {
                    if (DB::table($tableName)->whereIn($column, $ids)->exists()) {
                        return response()->json(['success' => false, 'message' => 'Pricing rule masih digunakan data lain. Tidak ada data yang dihapus.'], 422);
                    }
                }
            }
            DB::table('m_display_pricing_rules')->whereIn('id', $ids)->delete();
            return response()->json(['success' => true, 'message' => 'Markup Display berhasil dihapus.']);
        });
    }

    public function details(
        $engine_id,
        $location_id,
        $category_id
    ) {
        $this->displayCategoryForId($category_id);

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
        $this->displayCategoryForId($category_id);

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
        $category = $this->displayCategoryForId($category_id);

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

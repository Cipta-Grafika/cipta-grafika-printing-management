<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Imports\LocationsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LocationsExport;
use Throwable;

class LocationCT extends Controller
{
    public function index()
    {
        $creators = DB::table('m_locations')
            ->whereNotNull('created_by')
            ->where('created_by', '!=', '')
            ->distinct()
            ->orderBy('created_by')
            ->pluck('created_by');

        return view('admin.location.index', compact('creators'));
    }

    public function data(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | TOTAL SEMUA DATA
    |--------------------------------------------------------------------------
    */

        $totalRecords = DB::table('m_locations')->count();


        /*
    |--------------------------------------------------------------------------
    | QUERY UTAMA
    |--------------------------------------------------------------------------
    */

        $query = DB::table('m_locations');

        $creator = $request->input('creator');
        if ($creator && $creator !== 'all') {
            $query->where('created_by', $creator);
        }

        $period = $request->input('period');
        if ($period === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($period === '7_days') {
            $query->where('created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($period === 'month') {
            $query->where('created_at', '>=', Carbon::now()->startOfMonth());
        } elseif ($period === 'year') {
            $query->where('created_at', '>=', Carbon::now()->startOfYear());
        }


        /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

        $search = trim((string) $request->input('search.value', ''));

        if ($search !== '') {

            $query->where(function ($q) use ($search) {

                $q->whereRaw(
                    'LOWER(name) LIKE ?',
                    ['%' . strtolower($search) . '%']
                )

                    ->orWhereRaw(
                        'LOWER(status) LIKE ?',
                        ['%' . strtolower($search) . '%']
                    )

                    ->orWhereRaw(
                        'LOWER(created_by) LIKE ?',
                        ['%' . strtolower($search) . '%']
                    );
            });
        }


        /*
    |--------------------------------------------------------------------------
    | TOTAL DATA SETELAH FILTER
    |--------------------------------------------------------------------------
    */

        $totalFiltered = $query->count();


        /*
    |--------------------------------------------------------------------------
    | ORDERING
    |--------------------------------------------------------------------------
    */

        $columns = [

            2 => 'name',
            3 => 'status',
            4 => 'created_by',
            5 => 'created_at',

        ];

        $orderColumnIndex = (int) $request->input('order.0.column', 2);

        $orderColumn = $columns[$orderColumnIndex] ?? 'name';

        $orderDirection = $request->input('order.0.dir', 'asc');


        /*
    |--------------------------------------------------------------------------
    | SECURITY ORDER DIRECTION
    |--------------------------------------------------------------------------
    */

        if (!in_array($orderDirection, ['asc', 'desc'])) {

            $orderDirection = 'asc';
        }


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

        $locations = $query
            ->orderBy($orderColumn, $orderDirection)
            ->orderBy('id')

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

            'data' => $locations,

        ]);
    }

    public function store(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

        $request->validate(
            [
                'locations' => 'required|array|min:1',

                'locations.*.name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'locations.*.status' => [
                    'required',
                    'in:Active,Inactive',
                ],
            ],
            [
                'locations.required' => 'Data lokasi wajib diisi.',

                'locations.*.name.required' => 'Nama lokasi wajib diisi.',
                'locations.*.name.max' => 'Nama lokasi maksimal 255 karakter.',

                'locations.*.status.required' => 'Status lokasi wajib dipilih.',
                'locations.*.status.in' => 'Status lokasi tidak valid.',
            ]
        );


        /*
    |--------------------------------------------------------------------------
    | CEK DUPLIKASI
    |--------------------------------------------------------------------------
    */

        foreach ($request->locations as $location) {

            $name = trim($location['name']);

            $exists = DB::table('m_locations')
                ->whereRaw(
                    'LOWER(name) = ?',
                    [strtolower($name)]
                )
                ->exists();


            /*
        |--------------------------------------------------------------------------
        | JIKA SUDAH TERDAFTAR
        |--------------------------------------------------------------------------
        */

            if ($exists) {

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', "Lokasi '{$name}' sudah terdaftar.");
            }
        }


        /*
    |--------------------------------------------------------------------------
    | SIMPAN DATA
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use ($request) {

            foreach ($request->locations as $location) {

                DB::table('m_locations')->insert([

                    'name' => trim($location['name']),

                    'status' => $location['status'],

                    'created_by' => Auth::user()->name ?? 'System',

                    'created_at' => Carbon::now(),

                ]);
            }
        });


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('admin_locations')
            ->with('success', 'Data lokasi berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'status' => [
                'required',
                'in:Active,Inactive',
            ],
        ], [
            'name.required' => 'Nama lokasi wajib diisi.',

            'name.max' => 'Nama lokasi maksimal 255 karakter.',

            'status.required' => 'Status lokasi wajib dipilih.',

            'status.in' => 'Status lokasi tidak valid.',
        ]);


        /*
    |--------------------------------------------------------------------------
    | CEK LOKASI
    |--------------------------------------------------------------------------
    */

        $location = DB::table('m_locations')
            ->where('id', $id)
            ->first();


        if (!$location) {

            return response()->json([
                'success' => false,
                'message' => 'Data lokasi tidak ditemukan.'
            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | CEK DUPLIKASI NAMA LOKASI
    |--------------------------------------------------------------------------
    */

        $exists = DB::table('m_locations')
            ->whereRaw(
                'LOWER(name) = ?',
                [strtolower(trim($request->name))]
            )
            ->where('id', '!=', $id)
            ->exists();


        if ($exists) {

            return response()->json([
                'success' => false,
                'message' => 'Nama lokasi sudah terdaftar.'
            ], 422);
        }


        /*
    |--------------------------------------------------------------------------
    | UPDATE DATA
    |--------------------------------------------------------------------------
    */

        DB::table('m_locations')
            ->where('id', $id)
            ->update([

                'name' => trim($request->name),

                'status' => $request->status,

                'updated_by' => Auth::user()->name ?? 'System',

                'updated_at' => now(),

            ]);


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'success' => true,
            'message' => 'Data lokasi berhasil diperbarui.'
        ]);
    }

    public function destroy(int $id)
    {
        return $this->deleteLocations([$id]);
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:1000'],
            'ids.*' => ['required', 'integer', 'min:1', 'distinct'],
        ]);

        return $this->deleteLocations(array_map('intval', $validated['ids']));
    }

    private function deleteLocations(array $ids)
    {
        return DB::transaction(function () use ($ids) {
            $locations = DB::table('m_locations')->whereIn('id', $ids)->lockForUpdate()->get();

            if ($locations->count() !== count($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sebagian lokasi tidak ditemukan. Tidak ada data yang dihapus.',
                ], 404);
            }

            // Check actual foreign keys as well as unconstrained location_id columns.
            foreach (Schema::getTables() as $table) {
                if ($table['name'] === 'm_locations') {
                    continue;
                }

                $tableName = $table['schema_qualified_name'];
                $columns = [];
                if (Schema::hasColumn($tableName, 'location_id')) {
                    $columns[] = 'location_id';
                }
                foreach (Schema::getForeignKeys($tableName) as $foreignKey) {
                    if ($foreignKey['foreign_table'] === 'm_locations') {
                        foreach ($foreignKey['foreign_columns'] as $index => $foreignColumn) {
                            if ($foreignColumn === 'id') {
                                $columns[] = $foreignKey['columns'][$index];
                            }
                        }
                    }
                }

                foreach (array_unique($columns) as $column) {
                    if (DB::table($tableName)->whereIn($column, $ids)->exists()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Lokasi tidak dapat dihapus karena masih digunakan oleh data lain. Tidak ada data yang dihapus.',
                        ], 422);
                    }
                }
            }

            $deleted = DB::table('m_locations')->whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => $deleted === 1 ? 'Lokasi berhasil dihapus.' : "{$deleted} lokasi berhasil dihapus.",
            ]);
        });
    }

    public function import(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDASI FILE
    |--------------------------------------------------------------------------
    */

        $request->validate([

            'file' => 'required|file|mimes:xlsx,xls',

        ], [

            'file.required' => 'Silakan pilih file Excel terlebih dahulu.',

            'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls).',

        ]);


        try {

            /*
        |--------------------------------------------------------------------------
        | IMPORT EXCEL
        |--------------------------------------------------------------------------
        */

            Excel::import(

                new LocationsImport(),

                $request->file('file')

            );


            /*
        |--------------------------------------------------------------------------
        | RESPONSE BERHASIL
        |--------------------------------------------------------------------------
        */

            return response()->json([

                'success' => true,

                'message' => 'Data lokasi berhasil diimport.'

            ]);
        } catch (Throwable $e) {

            /*
        |--------------------------------------------------------------------------
        | RESPONSE ERROR
        |--------------------------------------------------------------------------
        */

            return response()->json([

                'success' => false,

                'message' => $e->getMessage()

            ], 422);
        }
    }

    public function export()
    {
        return Excel::download(

            new LocationsExport(),

            'master_lokasi.xlsx'

        );
    }
}

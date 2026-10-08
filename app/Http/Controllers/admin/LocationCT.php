<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Imports\LocationsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LocationsExport;
use Throwable;

class LocationCT extends Controller
{
    public function index()
    {
        return view('admin.location.index');
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


        /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

        $search = $request->input('search.value');

        if (!empty($search)) {

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

            0 => 'id',
            1 => 'name',
            2 => 'status',
            3 => 'created_by',
            4 => 'created_at',

        ];

        $orderColumnIndex = (int) $request->input('order.0.column', 1);

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

        $start = (int) $request->input('start', 0);

        $length = (int) $request->input('length', 10);


        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA
    |--------------------------------------------------------------------------
    */

        $locations = $query
            ->orderByRaw("
                CASE
                    WHEN status = 'Active' THEN 1
                    WHEN status = 'Inactive' THEN 2
                    ELSE 3
                END
            ")

            ->orderBy($orderColumn, $orderDirection)

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
        /*
    |--------------------------------------------------------------------------
    | CARI LOKASI
    |--------------------------------------------------------------------------
    */

        $location = DB::table('m_locations')
            ->where('id', $id)
            ->first();


        /*
    |--------------------------------------------------------------------------
    | JIKA LOKASI TIDAK DITEMUKAN
    |--------------------------------------------------------------------------
    */

        if (!$location) {

            return response()->json([

                'success' => false,

                'message' => 'Lokasi tidak ditemukan.'

            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | HAPUS LOKASI
    |--------------------------------------------------------------------------
    */

        DB::table('m_locations')
            ->where('id', $id)
            ->delete();


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return response()->json([

            'success' => true,

            'message' => 'Lokasi berhasil dihapus.'

        ]);
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

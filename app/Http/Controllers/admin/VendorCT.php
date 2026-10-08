<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\VendorsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\VendorsImport;

class VendorCT extends Controller
{
    public function index()
    {
        return view('admin.vendor.index');
    }

    public function store(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDASI DASAR
    |--------------------------------------------------------------------------
    */

        $request->validate(
            [
                'vendors' => 'required|array|min:1',

                'vendors.*.name' =>
                'required|string|max:255',

                'vendors.*.contact' =>
                'required|string|max:255',

                'vendors.*.status' =>
                'required|in:Active,Inactive',
            ],
            [
                'vendors.required' =>
                'Data vendor wajib diisi.',

                'vendors.min' =>
                'Minimal harus ada satu vendor.',

                'vendors.*.name.required' =>
                'Nama vendor wajib diisi.',

                'vendors.*.name.max' =>
                'Nama vendor maksimal 255 karakter.',

                'vendors.*.contact.required' =>
                'Kontak vendor wajib diisi.',

                'vendors.*.contact.max' =>
                'Kontak vendor maksimal 255 karakter.',

                'vendors.*.status.required' =>
                'Status vendor wajib dipilih.',

                'vendors.*.status.in' =>
                'Status vendor tidak valid.',
            ]
        );


        /*
    |--------------------------------------------------------------------------
    | NORMALISASI NAMA VENDOR
    |--------------------------------------------------------------------------
    */

        $vendorNames = collect($request->vendors)
            ->pluck('name')
            ->map(function ($name) {
                return strtolower(trim($name));
            });


        /*
    |--------------------------------------------------------------------------
    | CEK DUPLIKASI NAMA VENDOR DI DALAM FORM
    |--------------------------------------------------------------------------
    */

        if (
            $vendorNames->count() !==
            $vendorNames->unique()->count()
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'vendors' =>
                    'Terdapat nama vendor yang duplikat dalam data yang dimasukkan.'
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | CEK DUPLIKASI NAMA VENDOR DI DATABASE
    |--------------------------------------------------------------------------
    */

        foreach ($vendorNames as $vendorName) {

            $exists = DB::table('m_vendors')
                ->whereRaw(
                    'LOWER(TRIM(name)) = ?',
                    [$vendorName]
                )
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'vendors' =>
                        'Nama vendor "' .
                            $vendorName .
                            '" sudah terdaftar.'
                    ]);
            }
        }


        /*
    |--------------------------------------------------------------------------
    | SIMPAN SEMUA VENDOR
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use ($request) {

            foreach ($request->vendors as $vendor) {

                DB::table('m_vendors')->insert([
                    'name' => trim($vendor['name']),
                    'contact' => trim($vendor['contact']),
                    'status' => $vendor['status'],
                    'created_by' => Auth::user()->name ?? 'System',
                    'created_at' => now(),
                ]);
            }
        });


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('admin_vendors')
            ->with(
                'success',
                'Semua vendor berhasil ditambahkan.'
            );
    }

    public function edit($id)
    {
        $id = (int) $id;

        $vendor = DB::table('m_vendors')
            ->where('id', $id)
            ->first();

        if (!$vendor) {
            return response()->json([
                'message' => 'Data vendor tidak ditemukan.'
            ], 404);
        }

        return response()->json($vendor);
    }


    public function update(Request $request, $id)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

        $request->validate([
            'name' => 'required|string|max:255|unique:m_vendors,name,' . $id,
            'contact' => 'required|string|max:255',
            'status' => 'required|in:Active,Inactive',
        ], [
            'name.required' => 'Nama vendor wajib diisi.',
            'name.unique' => 'Nama vendor sudah terdaftar.',
            'contact.required' => 'Kontak wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status yang dipilih tidak valid.',
        ]);


        /*
    |--------------------------------------------------------------------------
    | CEK DATA VENDOR
    |--------------------------------------------------------------------------
    */

        $vendor = DB::table('m_vendors')
            ->where('id', $id)
            ->first();

        if (!$vendor) {
            return redirect()
                ->route('admin_vendors')
                ->with('error', 'Data vendor tidak ditemukan.');
        }


        /*
    |--------------------------------------------------------------------------
    | UPDATE DATA VENDOR
    |--------------------------------------------------------------------------
    */

        DB::table('m_vendors')
            ->where('id', $id)
            ->update([
                'name' => $request->name,
                'contact' => $request->contact,
                'status' => $request->status,
                'updated_by' => Auth::user()->name ?? 'System',
                'updated_at' => now(),
            ]);


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('admin_vendors')
            ->with('success', 'Data vendor berhasil diperbarui.');
    }

    public function data(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | TOTAL SEMUA DATA
    |--------------------------------------------------------------------------
    */

        $totalRecords = DB::table('m_vendors')->count();


        /*
    |--------------------------------------------------------------------------
    | QUERY UTAMA
    |--------------------------------------------------------------------------
    */

        $query = DB::table('m_vendors');


        /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

        $search = $request->input('search.value');

        if (!empty($search)) {

            $query->where(function ($query) use ($search) {

                $query->whereRaw(
                    'LOWER(name) LIKE ?',
                    ['%' . strtolower($search) . '%']
                )
                    ->orWhereRaw(
                        'LOWER(contact) LIKE ?',
                        ['%' . strtolower($search) . '%']
                    )
                    ->orWhereRaw(
                        'LOWER(status) LIKE ?',
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
            2 => 'contact',
            3 => 'status',
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

        $vendors = $query
            ->orderByRaw("CASE WHEN status = 'Active' THEN 0 ELSE 1 END")
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

            'data' => $vendors,

        ]);
    }

    public function destroy($id)
    {
        /*
    |--------------------------------------------------------------------------
    | CEK DATA VENDOR
    |--------------------------------------------------------------------------
    */

        $vendor = DB::table('m_vendors')
            ->where('id', $id)
            ->first();


        /*
    |--------------------------------------------------------------------------
    | JIKA DATA TIDAK DITEMUKAN
    |--------------------------------------------------------------------------
    */

        if (!$vendor) {

            return response()->json([

                'success' => false,

                'message' => 'Data vendor tidak ditemukan.'

            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | HAPUS DATA
    |--------------------------------------------------------------------------
    */

        DB::table('m_vendors')
            ->where('id', $id)
            ->delete();


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return response()->json([

            'success' => true,

            'message' => 'Vendor berhasil dihapus.'

        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | EXPORT DATA VENDOR
    |--------------------------------------------------------------------------
    */

    public function export()
    {
        return Excel::download(
            new VendorsExport(),
            'data_vendor.xlsx'
        );
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
            'file.file' => 'File yang dipilih tidak valid.',
            'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls).',
        ]);


        /*
    |--------------------------------------------------------------------------
    | IMPORT DATA
    |--------------------------------------------------------------------------
    */

        Excel::import(
            new VendorsImport(),
            $request->file('file')
        );


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'success' => true,
            'message' => 'Data vendor berhasil diimport.',
        ]);
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Imports\EnginesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EnginesExport;

class EngineCT extends Controller
{
    public function index()
    {
        return  view('admin.engine.index');
    }

    public function data(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | TOTAL SEMUA DATA
    |--------------------------------------------------------------------------
    */

        $totalRecords = DB::table('m_engines')->count();


        /*
    |--------------------------------------------------------------------------
    | QUERY UTAMA
    |--------------------------------------------------------------------------
    */

        $query = DB::table('m_engines');


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

            2 => 'calculation_method',

            3 => 'pricing_method',

            4 => 'unit',

            5 => 'status',

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

        $engines = $query

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

            'data' => $engines,

        ]);
    }

    public function store(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDASI DATA
    |--------------------------------------------------------------------------
    */

        $request->validate([
            'engines' => 'required|array|min:1',
            'engines.*.name' => 'required|string|max:255',
            'engines.*.minimum_charge' => 'nullable',
            'engines.*.status' => 'required|in:Active,Inactive',
        ], [
            'engines.*.name.required' => 'Nama mesin wajib diisi.',
            'engines.*.name.max' => 'Nama mesin maksimal 255 karakter.',
            'engines.*.status.required' => 'Status wajib dipilih.',
            'engines.*.status.in' => 'Status yang dipilih tidak valid.',
        ]);

        /*
    |--------------------------------------------------------------------------
    | CEK DUPLIKASI NAMA ANTAR-BARIS DI FORM
    |--------------------------------------------------------------------------
    */

        $engineNames = collect($request->engines)
            ->pluck('name')
            ->map(function ($name) {
                return strtolower(trim($name));
            });

        if ($engineNames->count() !== $engineNames->unique()->count()) {
            return back()
                ->withInput()
                ->withErrors([
                    'engines' => 'Terdapat nama mesin yang duplikat.'
                ]);
        }

        /*
    |--------------------------------------------------------------------------
    | CEK DUPLIKASI NAMA TERHADAP DATA DATABASE
    |--------------------------------------------------------------------------
    */

        $existingNames = DB::table('m_engines')
            ->whereIn(
                DB::raw('LOWER(TRIM(name))'),
                $engineNames->toArray()
            )
            ->pluck('name');

        if ($existingNames->isNotEmpty()) {
            return back()
                ->withInput()
                ->withErrors([
                    'engines' => 'Nama mesin "' .
                        $existingNames->implode(', ') .
                        '" sudah terdaftar.'
                ]);
        }

        /*
    |--------------------------------------------------------------------------
    | SIMPAN DATA MESIN
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use ($request) {

            foreach ($request->engines as $engine) {

                $minimumCharge = null;

                if (
                    isset($engine['minimum_charge']) &&
                    trim($engine['minimum_charge']) !== ''
                ) {
                    $minimumCharge = str_replace(
                        '.',
                        '',
                        $engine['minimum_charge']
                    );

                    $minimumCharge = str_replace(
                        ',',
                        '.',
                        $minimumCharge
                    );

                    $minimumCharge = (float) $minimumCharge;
                }

                DB::table('m_engines')->insert([
                    'name' => trim($engine['name']),

                    'minimum_charge' => $minimumCharge,

                    'status' => $engine['status'],

                    'created_by' => Auth::user()->name ?? 'System',

                    'created_at' => Carbon::now(),
                ]);
            }
        });

        /*
    |--------------------------------------------------------------------------
    | REDIRECT
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('admin_engines')
            ->with(
                'success',
                'Data mesin berhasil ditambahkan.'
            );
    }


    public function update(Request $request, $id)
    {

        /*
    |--------------------------------------------------------------------------
    | CEK DATA MESIN
    |--------------------------------------------------------------------------
    */

        $engine = DB::table('m_engines')
            ->where('id', $id)
            ->first();

        if (!$engine) {
            return redirect()
                ->route('admin_engines')
                ->withErrors([
                    'error' => 'Data mesin tidak ditemukan.'
                ]);
        }

        /*
    |--------------------------------------------------------------------------
    | VALIDASI DATA
    |--------------------------------------------------------------------------
    */

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($id) {

                    $name = strtolower(trim($value));

                    $exists = DB::table('m_engines')
                        ->whereRaw('LOWER(TRIM(name)) = ?', [$name])
                        ->where('id', '!=', $id)
                        ->exists();

                    if ($exists) {
                        $fail('Nama mesin sudah terdaftar.');
                    }
                },
            ],

            'minimum_charge' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'status' => [
                'required',
                'in:Active,Inactive',
            ],
        ], [
            'name.required' =>
            'Nama mesin wajib diisi.',

            'name.string' =>
            'Nama mesin harus berupa teks.',

            'name.max' =>
            'Nama mesin maksimal 255 karakter.',

            'minimum_charge.numeric' =>
            'Minimum charge harus berupa angka.',

            'minimum_charge.min' =>
            'Minimum charge tidak boleh kurang dari 0.',

            'status.required' =>
            'Status wajib dipilih.',

            'status.in' =>
            'Status yang dipilih tidak valid.',
        ]);

        /*
    |--------------------------------------------------------------------------
    | PARSE MINIMUM CHARGE
    |--------------------------------------------------------------------------
    */

        $minimumCharge = null;

        if (
            $request->filled('minimum_charge')
        ) {
            $minimumCharge = str_replace(
                '.',
                '',
                $request->minimum_charge
            );

            $minimumCharge = str_replace(
                ',',
                '.',
                $minimumCharge
            );

            $minimumCharge = (float) $minimumCharge;
        }

        /*
    |--------------------------------------------------------------------------
    | UPDATE DATA MESIN
    |--------------------------------------------------------------------------
    */

        DB::table('m_engines')
            ->where('id', $id)
            ->update([
                'name' => trim($request->name),
                'minimum_charge' => $minimumCharge,
                'status' => $request->status,
                'updated_by' => Auth::user()->name ?? 'System',
                'updated_at' => Carbon::now(),
            ]);

        /*
    |--------------------------------------------------------------------------
    | REDIRECT
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('admin_engines')
            ->with(
                'success',
                'Data mesin berhasil diperbarui.'
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
    | BUAT INSTANCE IMPORT
    |--------------------------------------------------------------------------
    */

        $import = new EnginesImport();


        /*
    |--------------------------------------------------------------------------
    | IMPORT DATA
    |--------------------------------------------------------------------------
    */

        Excel::import(

            $import,

            $request->file('file')

        );


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return response()->json([

            'success' => true,

            'message' => $import->imported . ' data mesin berhasil diimport.',

            'imported' => $import->imported,

            'skipped_duplicate' => $import->skippedDuplicate,

            'skipped_invalid' => $import->skippedInvalid,

        ]);
    }

    public function destroy($id)
    {
        /*
    |--------------------------------------------------------------------------
    | CEK DATA MESIN
    |--------------------------------------------------------------------------
    */

        $engine = DB::table('m_engines')
            ->where('id', $id)
            ->first();


        /*
    |--------------------------------------------------------------------------
    | JIKA DATA TIDAK DITEMUKAN
    |--------------------------------------------------------------------------
    */

        if (!$engine) {

            return response()->json([

                'success' => false,

                'message' => 'Data mesin tidak ditemukan.'

            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | HAPUS DATA
    |--------------------------------------------------------------------------
    */

        DB::table('m_engines')
            ->where('id', $id)
            ->delete();


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return response()->json([

            'success' => true,

            'message' => 'Mesin berhasil dihapus.'

        ]);
    }


    public function export()
    {
        return Excel::download(
            new EnginesExport(),
            'master_mesin.xlsx'
        );
    }
}

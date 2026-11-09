<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Imports\EnginesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EnginesExport;

class EngineCT extends Controller
{
    public function index()
    {
        $creators = DB::table('m_engines')
            ->whereNotNull('created_by')
            ->where('created_by', '!=', '')
            ->distinct()
            ->orderBy('created_by')
            ->pluck('created_by');

        return view('admin.engine.index', compact('creators'));
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
            3 => 'minimum_charge',
            4 => 'status',
            5 => 'created_by',
            6 => 'created_at',

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

        $engines = $query

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
        return $this->deleteEngines([(int) $id]);
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:1000'],
            'ids.*' => ['required', 'integer', 'min:1', 'distinct'],
        ]);

        return $this->deleteEngines(array_map('intval', $validated['ids']));
    }

    private function deleteEngines(array $ids)
    {
        return DB::transaction(function () use ($ids) {
            $engines = DB::table('m_engines')->whereIn('id', $ids)->lockForUpdate()->get();

            if ($engines->count() !== count($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sebagian mesin tidak ditemukan. Tidak ada data yang dihapus.',
                ], 404);
            }

            // Protect constrained references and conventional engine_id columns without a foreign key.
            foreach (Schema::getTables() as $table) {
                if ($table['name'] === 'm_engines') {
                    continue;
                }

                $tableName = $table['schema_qualified_name'];
                $columns = Schema::hasColumn($tableName, 'engine_id') ? ['engine_id'] : [];
                foreach (Schema::getForeignKeys($tableName) as $foreignKey) {
                    if ($foreignKey['foreign_table'] === 'm_engines') {
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
                            'message' => 'Mesin tidak dapat dihapus karena masih digunakan oleh data lain. Tidak ada data yang dihapus.',
                        ], 422);
                    }
                }
            }

            $deleted = DB::table('m_engines')->whereIn('id', $ids)->delete();

            return response()->json([
                'success' => true,
                'message' => $deleted === 1 ? 'Mesin berhasil dihapus.' : "{$deleted} mesin berhasil dihapus.",
            ]);
        });
    }


    public function export()
    {
        return Excel::download(
            new EnginesExport(),
            'master_mesin.xlsx'
        );
    }
}

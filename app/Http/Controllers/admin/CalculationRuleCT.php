<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Imports\CalculationRulesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CalculationRulesExport;

class CalculationRuleCT extends Controller
{
    public function index()
    {
        return view('admin.calculation-rule.index');
    }

    public function data(Request $request)
    {
        $query = DB::table('m_calculation_rules as cr')
            ->join('m_engines as e', 'e.id', '=', 'cr.engine_id')
            ->select([
                'cr.id',
                'cr.code',
                'cr.name',
                'cr.engine_id',
                'cr.description',
                'cr.minimum_charge',
                'cr.status',
                'e.name as engine_name',
            ]);

        $recordsTotal = DB::table('m_calculation_rules')
            ->count();

        $search = $request->input('search.value');

        if (!empty($search)) {
            $search = strtolower($search);

            $query->where(function ($q) use ($search) {
                $q->whereRaw(
                    'LOWER(cr.code) LIKE ?',
                    ["%{$search}%"]
                )
                    ->orWhereRaw(
                        'LOWER(cr.name) LIKE ?',
                        ["%{$search}%"]
                    )
                    ->orWhereRaw(
                        'LOWER(e.name) LIKE ?',
                        ["%{$search}%"]
                    )
                    ->orWhereRaw(
                        'LOWER(cr.description) LIKE ?',
                        ["%{$search}%"]
                    );
            });
        }

        $recordsFiltered = $query->get()->count();

        $query->orderBy('cr.code', 'asc');

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if ($length != -1) {
            $query->offset($start)
                ->limit($length);
        }

        $data = $query->get();

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }



    public function create()
    {
        $data['engines'] = DB::table('m_engines')->where('status', 'Active')->orderBy('name')->get();
        return view('admin.calculation-rule.create')->with($data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'nullable',
                'string',
                'max:50',
            ],
            'name' => [
                'required',
                'string',
                'max:150',
            ],
            'engine_id' => [
                'required',
                'integer',
                'exists:m_engines,id',
            ],
            'minimum_charge' => [
                'required',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'status' => [
                'required',
                'in:Active,Inactive',
            ],
        ], [
            'code.max' => 'Kode maksimal 50 karakter.',
            'name.required' => 'Nama aturan wajib diisi.',
            'name.max' => 'Nama aturan maksimal 150 karakter.',
            'engine_id.required' => 'Mesin wajib dipilih.',
            'engine_id.exists' => 'Mesin yang dipilih tidak ditemukan.',
            'minimum_charge.required' => 'Minimum Charge wajib diisi.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status tidak valid.',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Normalisasi Minimum Charge
    |--------------------------------------------------------------------------
    */

        $minimumCharge = str_replace(
            '.',
            '',
            $validated['minimum_charge']
        );

        $minimumCharge = str_replace(
            ',',
            '.',
            $minimumCharge
        );

        if (
            !is_numeric($minimumCharge) ||
            (float) $minimumCharge < 0
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'minimum_charge' =>
                    'Minimum Charge harus berupa angka yang valid dan tidak boleh negatif.',
                ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Cek Duplikasi Mesin
    |--------------------------------------------------------------------------
    |
    | Satu mesin hanya boleh memiliki satu Calculation Rule.
    | Kode tidak digunakan sebagai acuan duplikasi.
    |
    */

        $exists = DB::table('m_calculation_rules')
            ->where('engine_id', $validated['engine_id'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'engine_id' =>
                    'Calculation Rule untuk mesin yang dipilih sudah terdaftar.',
                ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Simpan Data
    |--------------------------------------------------------------------------
    */

        $user = Auth::user()->name ?? 'System';

        DB::table('m_calculation_rules')->insert([
            'code' =>
            !empty($validated['code'])
                ? trim($validated['code'])
                : null,

            'name' =>
            trim($validated['name']),

            'engine_id' =>
            $validated['engine_id'],

            'description' =>
            !empty($validated['description'])
                ? trim($validated['description'])
                : null,

            'minimum_charge' =>
            (float) $minimumCharge,

            'status' =>
            $validated['status'],

            'created_by' =>
            $user,

            'created_at' =>
            now(),

            'updated_by' =>
            $user,

            'updated_at' =>
            now(),
        ]);

        return redirect()
            ->route('admin_calculation_rules')
            ->with(
                'success',
                'Aturan perhitungan berhasil disimpan.'
            );
    }


    public function edit($id)
    {
        /*
    |--------------------------------------------------------------------------
    | DECODE ID
    |--------------------------------------------------------------------------
    */

        $data['decodedId'] = base64_decode($id, true);
        // dd($data['decodedId']);
        if ($data['decodedId'] === false || !ctype_digit($data['decodedId'])) {
            return redirect()
                ->route('admin_calculation_rules')
                ->with(
                    'error',
                    'ID aturan perhitungan tidak valid.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA CALCULATION RULE
    |--------------------------------------------------------------------------
    */

        $data['calculationRule'] = DB::table('m_calculation_rules')
            ->where('id', $data['decodedId'])
            ->first();

        if (!$data['calculationRule']) {
            return redirect()
                ->route('admin_calculation_rules')
                ->with(
                    'error',
                    'Aturan perhitungan tidak ditemukan.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA MESIN
    |--------------------------------------------------------------------------
    */

        $data['engines'] = DB::table('m_engines')
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | TAMPILKAN FORM EDIT
    |--------------------------------------------------------------------------
    */

        return view('admin.calculation-rule.edit')->with($data);
    }

    public function update(Request $request, $id)
    {
        /*
    |--------------------------------------------------------------------------
    | Decode ID
    |--------------------------------------------------------------------------
    */


        if (
            $id === false ||
            !ctype_digit($id)
        ) {
            return redirect()
                ->route('admin_calculation_rules')
                ->with(
                    'error',
                    'ID aturan perhitungan tidak valid.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Cari Data
    |--------------------------------------------------------------------------
    */

        $calculationRule = DB::table('m_calculation_rules')
            ->where('id', $id)
            ->first();

        if (!$calculationRule) {
            return redirect()
                ->route('admin_calculation_rules')
                ->with(
                    'error',
                    'Aturan perhitungan tidak ditemukan.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Validasi
    |--------------------------------------------------------------------------
    */

        $validated = $request->validate([
            'code' => [
                'nullable',
                'string',
                'max:50',
            ],

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'engine_id' => [
                'required',
                'integer',
                'exists:m_engines,id',
            ],

            'minimum_charge' => [
                'required',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:Active,Inactive',
            ],
        ], [
            'code.max' =>
            'Kode maksimal 50 karakter.',

            'name.required' =>
            'Nama aturan wajib diisi.',

            'name.max' =>
            'Nama aturan maksimal 150 karakter.',

            'engine_id.required' =>
            'Mesin wajib dipilih.',

            'engine_id.exists' =>
            'Mesin yang dipilih tidak ditemukan.',

            'minimum_charge.required' =>
            'Minimum Charge wajib diisi.',

            'status.required' =>
            'Status wajib dipilih.',

            'status.in' =>
            'Status tidak valid.',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Normalisasi Minimum Charge
    |--------------------------------------------------------------------------
    */

        $minimumCharge = str_replace(
            '.',
            '',
            $validated['minimum_charge']
        );

        $minimumCharge = str_replace(
            ',',
            '.',
            $minimumCharge
        );

        if (
            !is_numeric($minimumCharge) ||
            (float) $minimumCharge < 0
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'minimum_charge' =>
                    'Minimum Charge harus berupa angka yang valid dan tidak boleh negatif.',
                ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Cek Duplikasi Mesin
    |--------------------------------------------------------------------------
    |
    | Satu mesin hanya boleh memiliki satu Calculation Rule.
    | Record yang sedang diedit dikecualikan dari pengecekan.
    |
    */

        $exists = DB::table('m_calculation_rules')
            ->where('engine_id', $validated['engine_id'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'engine_id' =>
                    'Calculation Rule untuk mesin yang dipilih sudah terdaftar.',
                ]);
        }

        /*
    |--------------------------------------------------------------------------
    | Update Data
    |--------------------------------------------------------------------------
    */

        $user = Auth::user()->name ?? 'System';

        DB::table('m_calculation_rules')
            ->where('id', $id)
            ->update([
                'code' =>
                !empty($validated['code'])
                    ? trim($validated['code'])
                    : null,

                'name' =>
                trim($validated['name']),

                'engine_id' =>
                $validated['engine_id'],

                'description' =>
                !empty($validated['description'])
                    ? trim($validated['description'])
                    : null,

                'minimum_charge' =>
                (float) $minimumCharge,

                'status' =>
                $validated['status'],

                'updated_by' =>
                $user,

                'updated_at' =>
                now(),
            ]);

        /*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('admin_calculation_rules')
            ->with(
                'success',
                'Aturan perhitungan berhasil diperbarui.'
            );
    }



    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,xls',], ['file.required' => 'Silakan pilih file Excel terlebih dahulu.', 'file.file' => 'File yang dipilih tidak valid.', 'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls).',]); /* |-------------------------------------------------------------------------- | Jalankan Import |-------------------------------------------------------------------------- */
        $import = new CalculationRulesImport();
        Excel::import($import, $request->file('file')); /* |-------------------------------------------------------------------------- | Jika terdapat error |-------------------------------------------------------------------------- | | Semua data dibatalkan apabila terdapat minimal satu error. | */
        if (count($import->errors) > 0) {
            return response()->json(['success' => false, 'message' => 'Import dibatalkan. Terdapat data yang tidak valid.', 'errors' => $import->errors, 'imported' => 0,], 422);
        } /* |-------------------------------------------------------------------------- | Tidak Ada Data |-------------------------------------------------------------------------- */
        if ($import->imported === 0) {
            return response()->json(['success' => false, 'message' => 'Tidak ada data aturan perhitungan yang valid untuk diimport.', 'imported' => 0,], 422);
        } /* |-------------------------------------------------------------------------- | Berhasil |-------------------------------------------------------------------------- */
        return response()->json(['success' => true, 'message' => $import->imported . ' data aturan perhitungan berhasil diimport.', 'imported' => $import->imported,]);
    }

    public function export()
    {
        return Excel::download(
            new CalculationRulesExport(),
            'master_aturan_perhitungan.xlsx'
        );
    }

    public function destroy($id)
    {
        /*
    |--------------------------------------------------------------------------
    | CEK CALCULATION RULE
    |--------------------------------------------------------------------------
    */

        $calculationRule = DB::table('m_calculation_rules')
            ->where('id', $id)
            ->first();

        if (!$calculationRule) {
            return response()->json([
                'success' => false,
                'message' => 'Data aturan perhitungan tidak ditemukan.',
            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | HAPUS CALCULATION RULE
    |--------------------------------------------------------------------------
    */

        DB::table('m_calculation_rules')
            ->where('id', $id)
            ->delete();


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'success' => true,
            'message' => 'Aturan perhitungan berhasil dihapus.',
        ]);
    }

    public function details($id)
    {
        /*
    |--------------------------------------------------------------------------
    | Decode ID
    |--------------------------------------------------------------------------
    */

        $data['decodedId'] = base64_decode($id, true);

        if (
            $data['decodedId'] === false ||
            !ctype_digit($data['decodedId'])
        ) {
            return redirect()
                ->route('admin_calculation_rules')
                ->with(
                    'error',
                    'ID aturan perhitungan tidak valid.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Ambil Data Calculation Rule
    |--------------------------------------------------------------------------
    */

        $data['calculationRule'] = DB::table('m_calculation_rules as cr')
            ->join(
                'm_engines as e',
                'e.id',
                '=',
                'cr.engine_id'
            )
            ->select([
                'cr.id',
                'cr.code',
                'cr.name',
                'cr.engine_id',
                'cr.description',
                'cr.minimum_charge',
                'cr.status',
                'cr.created_by',
                'cr.created_at',
                'cr.updated_by',
                'cr.updated_at',
                'e.name as engine_name',
            ])
            ->where('cr.id', $data['decodedId'])
            ->first();

        /*
    |--------------------------------------------------------------------------
    | Data Tidak Ditemukan
    |--------------------------------------------------------------------------
    */

        if (!$data['calculationRule']) {
            return redirect()
                ->route('admin_calculation_rules')
                ->with(
                    'error',
                    'Aturan perhitungan tidak ditemukan.'
                );
        }

        /*
    |--------------------------------------------------------------------------
    | Tampilkan Detail
    |--------------------------------------------------------------------------
    */

        return view('admin.calculation-rule.details')->with($data);
    }
}

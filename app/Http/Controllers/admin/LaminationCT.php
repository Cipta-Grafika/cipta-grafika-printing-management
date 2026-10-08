<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaminationsExport;
use App\Imports\LaminationsImport;
use Exception;
use Illuminate\Validation\ValidationException;

class LaminationCT extends Controller
{
    public function index()
    {
        $data['engines'] = DB::table('m_engines')->where('status', 'Active')->orderBy('name')->get();
        $data['locations'] = DB::table('m_locations')->where('status', 'Active')->orderBy('name')->get();
        $data['categories'] = DB::table('m_categories')->get();

        return view('admin.lamination.index')->with($data);
    }

    public function data(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | TOTAL SEMUA DATA
    |--------------------------------------------------------------------------
    */

        $totalRecords = DB::table('m_laminations')->count();

        /*
    |--------------------------------------------------------------------------
    | QUERY UTAMA
    |--------------------------------------------------------------------------
    */

        $query = DB::table('m_laminations')
            ->select(
                'm_laminations.id',
                'm_laminations.lamination_code',
                'm_laminations.name',
                'm_laminations.status',
                DB::raw('
                (
                    SELECT COUNT(*)
                    FROM m_lamination_sizes
                    WHERE m_lamination_sizes.lamination_id = m_laminations.id
                ) AS total_sizes
            ')
            );

        /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

        $search = $request->input('search.value');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $searchValue = strtolower($search);

                $q->whereRaw(
                    'LOWER(m_laminations.lamination_code) LIKE ?',
                    ['%' . $searchValue . '%']
                )
                    ->orWhereRaw(
                        'LOWER(m_laminations.name) LIKE ?',
                        ['%' . $searchValue . '%']
                    )
                    ->orWhereRaw(
                        'LOWER(m_laminations.status) LIKE ?',
                        ['%' . $searchValue . '%']
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

        $laminations = $query
            ->orderByRaw(
                "CASE WHEN m_laminations.status = 'Active' THEN 0 ELSE 1 END"
            )
            ->orderBy('m_laminations.name', 'asc')
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
            'data' => $laminations,
        ]);
    }

    public function store(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDASI DASAR
    |--------------------------------------------------------------------------
    */

        $request->validate([
            'laminations' => [
                'required',
                'array',
                'min:1',
            ],

            'laminations.*.lamination_code' => [
                'nullable',
                'string',
                'max:255',
            ],

            'laminations.*.name' => [
                'required',
                'string',
                'max:255',
            ],

            'laminations.*.status' => [
                'required',
                'in:Active,Inactive',
            ],

            'laminations.*.sizes' => [
                'required',
                'array',
                'min:1',
            ],

            'laminations.*.sizes.*.width' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'laminations.*.sizes.*.length' => [
                'nullable',
                'numeric',
                'gt:0',
            ],

            'laminations.*.sizes.*.unit' => [
                'required',
                'string',
                'max:255',
            ],
        ]);


        /*
    |--------------------------------------------------------------------------
    | TRANSACTION
    |--------------------------------------------------------------------------
    |
    | Semua proses penyimpanan dibungkus dalam transaction.
    |
    | Jika ditemukan satu saja data duplikat atau error,
    | seluruh transaksi akan di-rollback.
    |
    */

        DB::transaction(function () use ($request) {

            /*
        |--------------------------------------------------------------------------
        | PENYIMPANAN DATA SEMENTARA
        |--------------------------------------------------------------------------
        |
        | Data belum langsung disimpan ke database.
        | Kita siapkan seluruh data terlebih dahulu.
        |
        */

            $preparedLaminations = [];


            /*
        |--------------------------------------------------------------------------
        | TRACK DUPLIKAT NAMA DALAM REQUEST
        |--------------------------------------------------------------------------
        */

            $laminationNames = [];


            foreach ($request->laminations as $lamination) {

                /*
            |--------------------------------------------------------------------------
            | NORMALISASI NAMA
            |--------------------------------------------------------------------------
            */

                $laminationName = trim($lamination['name']);

                $normalizedName = strtolower($laminationName);


                /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKAT NAMA DALAM REQUEST
            |--------------------------------------------------------------------------
            */

                if (
                    in_array(
                        $normalizedName,
                        $laminationNames,
                        true
                    )
                ) {

                    throw ValidationException::withMessages([
                        'laminations' =>
                        "Laminasi \"{$laminationName}\" terduplikat dalam data yang dikirim.",
                    ]);
                }


                $laminationNames[] = $normalizedName;


                /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKAT MASTER DI DATABASE
            |--------------------------------------------------------------------------
            */

                $existingLamination = DB::table('m_laminations')
                    ->whereRaw(
                        'LOWER(TRIM(name)) = ?',
                        [$normalizedName]
                    )
                    ->first();


                if ($existingLamination) {

                    throw ValidationException::withMessages([
                        'laminations' =>
                        "Laminasi \"{$laminationName}\" sudah terdaftar.",
                    ]);
                }


                /*
            |--------------------------------------------------------------------------
            | TRACK DUPLIKAT UKURAN DALAM REQUEST
            |--------------------------------------------------------------------------
            */

                $requestSizes = [];


                /*
            |--------------------------------------------------------------------------
            | SIAPKAN UKURAN
            |--------------------------------------------------------------------------
            */

                $preparedSizes = [];


                foreach ($lamination['sizes'] as $size) {

                    /*
                |--------------------------------------------------------------------------
                | WIDTH
                |--------------------------------------------------------------------------
                */

                    $width = $size['width'];


                    /*
                |--------------------------------------------------------------------------
                | LENGTH
                |--------------------------------------------------------------------------
                */

                    $length =
                        isset($size['length']) &&
                        $size['length'] !== ''
                        ? $size['length']
                        : null;


                    /*
                |--------------------------------------------------------------------------
                | UNIT
                |--------------------------------------------------------------------------
                */

                    $unit = trim($size['unit']);

                    $normalizedUnit = strtolower($unit);


                    /*
                |--------------------------------------------------------------------------
                | BUAT IDENTITAS UKURAN
                |--------------------------------------------------------------------------
                |
                | Kombinasi width + length + unit dianggap sebagai
                | satu ukuran.
                |
                */

                    $sizeKey =
                        $width .
                        '|' .
                        ($length ?? 'NULL') .
                        '|' .
                        $normalizedUnit;


                    /*
                |--------------------------------------------------------------------------
                | CEK DUPLIKAT UKURAN DALAM REQUEST
                |--------------------------------------------------------------------------
                */

                    if (
                        in_array(
                            $sizeKey,
                            $requestSizes,
                            true
                        )
                    ) {

                        throw ValidationException::withMessages([
                            'sizes' =>
                            "Ukuran {$width}" .
                                ($length !== null
                                    ? " × {$length}"
                                    : '') .
                                " {$unit} untuk Laminasi \"{$laminationName}\" terduplikat.",
                        ]);
                    }


                    $requestSizes[] = $sizeKey;


                    /*
                |--------------------------------------------------------------------------
                | SIMPAN DATA UKURAN SEMENTARA
                |--------------------------------------------------------------------------
                */

                    $preparedSizes[] = [
                        'width' => $width,
                        'length' => $length,
                        'unit' => $unit,
                    ];
                }


                /*
            |--------------------------------------------------------------------------
            | SIMPAN DATA LAMINASI SEMENTARA
            |--------------------------------------------------------------------------
            */

                $preparedLaminations[] = [

                    'lamination_code' =>
                    !empty($lamination['lamination_code'])
                        ? trim($lamination['lamination_code'])
                        : null,

                    'name' =>
                    $laminationName,

                    'status' =>
                    $lamination['status'],

                    'sizes' =>
                    $preparedSizes,

                ];
            }


            /*
        |--------------------------------------------------------------------------
        | INSERT MASTER LAMINASI
        |--------------------------------------------------------------------------
        |
        | Pada tahap ini seluruh data sudah lolos pengecekan.
        |
        */

            foreach ($preparedLaminations as $lamination) {

                $now = now();

                $userName =
                    Auth::user()->name ?? 'System';


                /*
            |--------------------------------------------------------------------------
            | INSERT M_LAMINATIONS
            |--------------------------------------------------------------------------
            */

                $laminationId = DB::table('m_laminations')
                    ->insertGetId([

                        'lamination_code' =>
                        $lamination['lamination_code'],

                        'name' =>
                        $lamination['name'],

                        'status' =>
                        $lamination['status'],

                        'created_by' =>
                        $userName,

                        'created_at' =>
                        $now,

                        'updated_by' =>
                        $userName,

                        'updated_at' =>
                        $now,

                    ]);


                /*
            |--------------------------------------------------------------------------
            | INSERT M_LAMINATION_SIZES
            |--------------------------------------------------------------------------
            */

                foreach ($lamination['sizes'] as $size) {

                    DB::table('m_lamination_sizes')
                        ->insert([

                            'lamination_id' =>
                            $laminationId,

                            'width' =>
                            $size['width'],

                            'length' =>
                            $size['length'],

                            'unit' =>
                            $size['unit'],

                            'created_by' =>
                            $userName,

                            'created_at' =>
                            $now,

                            'updated_by' =>
                            $userName,

                            'updated_at' =>
                            $now,

                        ]);
                }
            }
        });


        /*
    |--------------------------------------------------------------------------
    | REDIRECT
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('admin_laminations')
            ->with(
                'success',
                'Laminasi berhasil disimpan.'
            );
    }

    public function edit($id)
    {
        $lamination = DB::table('m_laminations')
            ->where('id', $id)
            ->first();

        if (!$lamination) {
            return response()->json([
                'success' => false,
                'message' => 'Data laminasi tidak ditemukan.'
            ], 404);
        }

        $sizes = DB::table('m_lamination_sizes')
            ->where('lamination_id', $id)
            ->orderBy('width')
            ->orderBy('length')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $lamination->id,
                'lamination_code' => $lamination->lamination_code,
                'name' => $lamination->name,
                'status' => $lamination->status,
                'sizes' => $sizes
                    ->map(function ($size) {
                        return [
                            'id' => $size->id,
                            'width' => $size->width,
                            'length' => $size->length,
                            'unit' => $size->unit,
                        ];
                    })
                    ->values()
                    ->all(),
            ]
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'lamination_code' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:Active,Inactive'],

            'sizes' => ['required', 'array', 'min:1'],
            'sizes.*.id' => ['nullable', 'integer'],
            'sizes.*.width' => ['required', 'numeric', 'gt:0'],
            'sizes.*.length' => ['nullable', 'numeric', 'gt:0'],
            'sizes.*.unit' => ['required', 'string', 'max:255'],
        ]);

        DB::beginTransaction();

        try {
            /*
        |--------------------------------------------------------------------------
        | 1. Ambil & lock data laminasi
        |--------------------------------------------------------------------------
        */

            $lamination = DB::table('m_laminations')
                ->where('id', $id)
                ->lockForUpdate()
                ->first();

            if (!$lamination) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Data laminasi tidak ditemukan.'
                ], 404);
            }

            /*
        |--------------------------------------------------------------------------
        | 2. Normalisasi nama
        |--------------------------------------------------------------------------
        */

            $laminationName = trim($request->name);
            $normalizedName = strtolower($laminationName);

            /*
        |--------------------------------------------------------------------------
        | 3. Cek duplikat nama laminasi
        |--------------------------------------------------------------------------
        |
        | Nama tidak boleh sama dengan laminasi lain.
        | Data yang sedang diedit dikecualikan.
        |
        */

            $duplicateName = DB::table('m_laminations')
                ->where('id', '<>', $id)
                ->whereRaw(
                    'LOWER(TRIM(name)) = ?',
                    [$normalizedName]
                )
                ->exists();

            if ($duplicateName) {
                throw ValidationException::withMessages([
                    'name' => "Laminasi \"{$laminationName}\" sudah terdaftar."
                ]);
            }

            /*
        |--------------------------------------------------------------------------
        | 4. Validasi & siapkan semua ukuran
        |--------------------------------------------------------------------------
        */

            $preparedSizes = [];
            $submittedSizeIds = [];
            $sizeKeys = [];

            foreach ($request->sizes as $size) {

                $sizeId = !empty($size['id'])
                    ? (int) $size['id']
                    : null;

                $width = $size['width'];

                $length = isset($size['length']) &&
                    $size['length'] !== ''
                    ? $size['length']
                    : null;

                $unit = trim($size['unit']);
                $normalizedUnit = strtolower($unit);

                /*
            |--------------------------------------------------------------------------
            | 4a. Jika ada ID ukuran, pastikan ukuran tersebut
            |     memang milik laminasi yang sedang diedit
            |--------------------------------------------------------------------------
            */

                if ($sizeId !== null) {

                    $existingSize = DB::table('m_lamination_sizes')
                        ->where('id', $sizeId)
                        ->where('lamination_id', $id)
                        ->first();

                    if (!$existingSize) {
                        throw ValidationException::withMessages([
                            'sizes' => 'Data ukuran laminasi tidak valid.'
                        ]);
                    }

                    /*
                |--------------------------------------------------------------------------
                | 4b. Cegah ID ukuran yang sama dikirim dua kali
                |--------------------------------------------------------------------------
                */

                    if (in_array($sizeId, $submittedSizeIds, true)) {
                        throw ValidationException::withMessages([
                            'sizes' =>
                            "Ukuran laminasi dengan ID {$sizeId} dikirim lebih dari satu kali."
                        ]);
                    }

                    $submittedSizeIds[] = $sizeId;
                }

                /*
            |--------------------------------------------------------------------------
            | 4c. Cek duplikat ukuran dalam request
            |--------------------------------------------------------------------------
            |
            | Identitas ukuran:
            | width + length + unit
            |
            */

                $sizeKey =
                    $width .
                    '|' .
                    ($length ?? 'NULL') .
                    '|' .
                    $normalizedUnit;

                if (in_array($sizeKey, $sizeKeys, true)) {
                    throw ValidationException::withMessages([
                        'sizes' =>
                        "Ukuran {$width}" .
                            ($length !== null
                                ? " × {$length}"
                                : '') .
                            " {$unit} untuk Laminasi \"{$laminationName}\" terduplikat."
                    ]);
                }

                $sizeKeys[] = $sizeKey;

                $preparedSizes[] = [
                    'id' => $sizeId,
                    'width' => $width,
                    'length' => $length,
                    'unit' => $unit,
                ];
            }

            /*
        |--------------------------------------------------------------------------
        | 5. Update data utama laminasi
        |--------------------------------------------------------------------------
        */

            $now = now();
            $userName = Auth::user()->name ?? 'System';

            DB::table('m_laminations')
                ->where('id', $id)
                ->update([
                    'lamination_code' => !empty($request->lamination_code)
                        ? trim($request->lamination_code)
                        : null,

                    'name' => $laminationName,
                    'status' => $request->status,

                    'updated_by' => $userName,
                    'updated_at' => $now,
                ]);

            /*
        |--------------------------------------------------------------------------
        | 6. Hapus ukuran yang dihapus dari modal
        |--------------------------------------------------------------------------
        */

            $existingSizeIds = DB::table('m_lamination_sizes')
                ->where('lamination_id', $id)
                ->pluck('id')
                ->map(function ($sizeId) {
                    return (int) $sizeId;
                })
                ->all();

            $sizeIdsToDelete = array_diff(
                $existingSizeIds,
                $submittedSizeIds
            );

            if (!empty($sizeIdsToDelete)) {
                DB::table('m_lamination_sizes')
                    ->where('lamination_id', $id)
                    ->whereIn('id', $sizeIdsToDelete)
                    ->delete();
            }

            /*
        |--------------------------------------------------------------------------
        | 7. Update ukuran lama / insert ukuran baru
        |--------------------------------------------------------------------------
        */

            foreach ($preparedSizes as $size) {

                if ($size['id'] !== null) {

                    /*
                |--------------------------------------------------------------------------
                | Ukuran lama
                |--------------------------------------------------------------------------
                */

                    DB::table('m_lamination_sizes')
                        ->where('id', $size['id'])
                        ->where('lamination_id', $id)
                        ->update([
                            'width' => $size['width'],
                            'length' => $size['length'],
                            'unit' => $size['unit'],

                            'updated_by' => $userName,
                            'updated_at' => $now,
                        ]);
                } else {

                    /*
                |--------------------------------------------------------------------------
                | Ukuran baru
                |--------------------------------------------------------------------------
                */

                    DB::table('m_lamination_sizes')
                        ->insert([
                            'lamination_id' => $id,
                            'width' => $size['width'],
                            'length' => $size['length'],
                            'unit' => $size['unit'],

                            'created_by' => $userName,
                            'created_at' => $now,
                            'updated_by' => $userName,
                            'updated_at' => $now,
                        ]);
                }
            }

            /*
        |--------------------------------------------------------------------------
        | 8. Commit
        |--------------------------------------------------------------------------
        */

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laminasi berhasil diperbarui.'
            ]);
        } catch (ValidationException $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors' => $e->errors()
            ], 422);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Laminasi gagal diperbarui. Silakan coba lagi.'
            ], 500);
        }
    }

    public function details($id)
    {
        /*
    |--------------------------------------------------------------------------
    | DATA UTAMA LAMINASI
    |--------------------------------------------------------------------------
    */

        $lamination = DB::table('m_laminations as l')
            ->where('l.id', $id)
            ->select(
                'l.id',
                'l.lamination_code',
                'l.name',
                'l.status',
                'l.created_by',
                'l.created_at',
                'l.updated_by',
                'l.updated_at'
            )
            ->first();

        /*
    |--------------------------------------------------------------------------
    | DATA LAMINASI TIDAK DITEMUKAN
    |--------------------------------------------------------------------------
    */

        if (!$lamination) {
            return response()->json([
                'success' => false,
                'message' => 'Data laminasi tidak ditemukan.'
            ], 404);
        }

        /*
    |--------------------------------------------------------------------------
    | DATA UKURAN LAMINASI
    |--------------------------------------------------------------------------
    */

        $sizes = DB::table('m_lamination_sizes')
            ->where('lamination_id', $lamination->id)
            ->select(
                'id',
                'width',
                'length',
                'unit'
            )
            ->orderBy('width')
            ->orderBy('length')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'success' => true,
            'data' => [
                /*
            |--------------------------------------------------------------------------
            | DATA UTAMA
            |--------------------------------------------------------------------------
            */

                'id' => $lamination->id,
                'lamination_code' => $lamination->lamination_code,
                'name' => $lamination->name,
                'status' => $lamination->status,

                /*
            |--------------------------------------------------------------------------
            | AUDIT
            |--------------------------------------------------------------------------
            */

                'created_by' => $lamination->created_by,
                'created_at' => $lamination->created_at,
                'updated_by' => $lamination->updated_by,
                'updated_at' => $lamination->updated_at,

                /*
            |--------------------------------------------------------------------------
            | UKURAN
            |--------------------------------------------------------------------------
            */

                'sizes' => $sizes
                    ->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'width' => $item->width,
                            'length' => $item->length,
                            'unit' => $item->unit,
                        ];
                    })
                    ->values()
                    ->all(),
            ]
        ]);
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            // Lock master laminasi
            $lamination = DB::table('m_laminations')
                ->where('id', $id)
                ->lockForUpdate()
                ->first();

            if (!$lamination) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Data laminasi tidak ditemukan.'
                ], 404);
            }

            // Hapus master laminasi
            // m_lamination_sizes akan ikut terhapus
            // melalui ON DELETE CASCADE.
            DB::table('m_laminations')
                ->where('id', $id)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Laminasi berhasil dihapus.'
            ]);
        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Laminasi gagal dihapus. Silakan coba lagi.'
            ], 500);
        }
    }

    public function export()
    {
        return Excel::download(
            new LaminationsExport(),
            'master_laminasi.xlsx'
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ], [
            'file.required' =>
            'Silakan pilih file Excel terlebih dahulu.',

            'file.file' =>
            'File yang dipilih tidak valid.',

            'file.mimes' =>
            'File harus berformat Excel (.xlsx atau .xls).',
        ]);

        try {

            $import = new LaminationsImport();

            Excel::import(
                $import,
                $request->file('file')
            );

            return response()->json([
                'success' => true,

                'message' =>
                $import->importedLaminations .
                    ' laminasi dan ' .
                    $import->importedSizes .
                    ' ukuran berhasil diimport.',

                'imported_laminations' =>
                $import->importedLaminations,

                'imported_sizes' =>
                $import->importedSizes,
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}


 
/*
|--------------------------------------------------------------------------
CREATE TABLE public.m_lamination_configurations (
	id bigserial NOT NULL,
	engine_id int8 NOT NULL,
	location_id int8 NOT NULL,
	lamination_id int8 NOT NULL,
	created_by varchar(255) NOT NULL,
	created_at timestamp(0) NOT NULL,
	updated_by varchar(255) NOT NULL,
	updated_at timestamp(0) NOT NULL,
	category_id int8 NOT NULL,
	CONSTRAINT m_lamination_configurations_pkey PRIMARY KEY (id),
	CONSTRAINT m_lamination_configurations_category_id_foreign FOREIGN KEY (category_id) REFERENCES public.m_categories(id) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT m_lamination_configurations_engine_id_foreign FOREIGN KEY (engine_id) REFERENCES public.m_engines(id) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT m_lamination_configurations_lamination_id_foreign FOREIGN KEY (lamination_id) REFERENCES public.m_laminations(id) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT m_lamination_configurations_location_id_foreign FOREIGN KEY (location_id) REFERENCES public.m_locations(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE public.m_calculation_rules (
	id bigserial NOT NULL,
	code varchar(50) NULL,
	"name" varchar(150) NOT NULL,
	description text NULL,
	minimum_charge numeric(12, 2) NULL,
	status varchar(255) NOT NULL,
	created_by varchar(255) NOT NULL,
	created_at timestamp(0) NOT NULL,
	updated_by varchar(255) NOT NULL,
	updated_at timestamp(0) NOT NULL,
	engine_id int8 NOT NULL,
	CONSTRAINT m_calculation_rules_pkey PRIMARY KEY (id),
	CONSTRAINT m_calculation_rules_engine_id_foreign FOREIGN KEY (engine_id) REFERENCES public.m_engines(id) ON DELETE CASCADE ON UPDATE CASCADE
);
|--------------------------------------------------------------------------
*/
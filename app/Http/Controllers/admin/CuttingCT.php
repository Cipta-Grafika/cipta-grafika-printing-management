<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CuttingsExport;
use App\Imports\CuttingsImport;
use Exception;

class CuttingCT extends Controller
{
    public function index()
    {
        $creators = DB::table('m_cuttings')->whereNotNull('created_by')
            ->where('created_by', '!=', '')->distinct()->orderBy('created_by')->pluck('created_by');
        return view('admin.cutting.index', compact('creators'));
    }

    public function data(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | TOTAL SEMUA DATA
        |--------------------------------------------------------------------------
        */

        $totalRecords = DB::table('m_cuttings')->count();

        /*
        |--------------------------------------------------------------------------
        | QUERY UTAMA
        |--------------------------------------------------------------------------
        */

        $query = DB::table('m_cuttings')
            ->select(
                'm_cuttings.id',
                'm_cuttings.name',
                'm_cuttings.status',
                'm_cuttings.created_by',
                'm_cuttings.created_at',
                DB::raw('
                (
                    SELECT COUNT(*)
                    FROM m_cutting_sizes
                    WHERE m_cutting_sizes.cutting_id = m_cuttings.id
                ) AS total_sizes
            ')
            );

        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        $creator = $request->input('creator');
        if ($creator && $creator !== 'all') {
            $query->where('m_cuttings.created_by', $creator);
        }
        $period = $request->input('period');
        if ($period === 'today') {
            $query->whereDate('m_cuttings.created_at', Carbon::today());
        } elseif ($period === '7_days') {
            $query->where('m_cuttings.created_at', '>=', Carbon::now()->subDays(7));
        } elseif ($period === 'month') {
            $query->where('m_cuttings.created_at', '>=', Carbon::now()->startOfMonth());
        } elseif ($period === 'year') {
            $query->where('m_cuttings.created_at', '>=', Carbon::now()->startOfYear());
        }

        $search = trim((string) $request->input('search.value', ''));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $searchValue = strtolower($search);

                $q->whereRaw(
                    'LOWER(m_cuttings.name) LIKE ?',
                    ['%' . $searchValue . '%']
                )
                    ->orWhereRaw(
                        'LOWER(m_cuttings.status) LIKE ?',
                        ['%' . $searchValue . '%']
                    )
                    ->orWhereRaw(
                        'LOWER(m_cuttings.created_by) LIKE ?',
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

        $start = max(0, (int) $request->input('start', 0));
        $length = min(100, max(1, (int) $request->input('length', 10)));

        /*
        |--------------------------------------------------------------------------
        | AMBIL DATA
        |--------------------------------------------------------------------------
        */

        $columns = [
            2 => 'm_cuttings.name',
            3 => 'total_sizes',
            4 => 'm_cuttings.status',
            5 => 'm_cuttings.created_by',
            6 => 'm_cuttings.created_at',
        ];
        $orderColumn = $columns[(int) $request->input('order.0.column', -1)] ?? null;
        $direction = $request->input('order.0.dir', 'asc');
        if (!in_array($direction, ['asc', 'desc'], true)) {
            $direction = 'asc';
        }
        if ($orderColumn) {
            $query->orderBy($orderColumn, $direction);
        } else {
            $query->orderByRaw("CASE WHEN m_cuttings.status = 'Active' THEN 0 ELSE 1 END")
                ->orderBy('m_cuttings.name');
        }
        $cuttings = $query->orderBy('m_cuttings.id')
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
            'data' => $cuttings,
        ]);
    }

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI DASAR
        |--------------------------------------------------------------------------
        */

        $rules = [
            'cuttings' => [
                'required',
                'array',
                'min:1',
            ],

            'cuttings.*.name' => [
                'required',
                'string',
                'max:255',
            ],

            'cuttings.*.status' => [
                'required',
                'in:Active,Inactive',
            ],
        ];

        foreach ($request->input('cuttings', []) as $index => $cutting) {
            $isPotong = strtolower(trim($cutting['name'] ?? '')) === 'potong';

            if (!$isPotong) {
                $rules["cuttings.{$index}.sizes"] = [
                    'required',
                    'array',
                    'min:1',
                ];

                $rules["cuttings.{$index}.sizes.*.width"] = [
                    'required',
                    'numeric',
                    'gt:0',
                ];

                $rules["cuttings.{$index}.sizes.*.length"] = [
                    'nullable',
                    'numeric',
                    'gt:0',
                ];

                $rules["cuttings.{$index}.sizes.*.unit"] = [
                    'required',
                    'string',
                    'max:255',
                ];
            } else {
                $rules["cuttings.{$index}.sizes"] = [
                    'nullable',
                    'array',
                ];

                $rules["cuttings.{$index}.sizes.*.width"] = [
                    'nullable',
                    'numeric',
                    'gt:0',
                ];

                $rules["cuttings.{$index}.sizes.*.length"] = [
                    'nullable',
                    'numeric',
                    'gt:0',
                ];

                $rules["cuttings.{$index}.sizes.*.unit"] = [
                    'nullable',
                    'string',
                    'max:255',
                ];
            }
        }

        $request->validate($rules);

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION
        |--------------------------------------------------------------------------
        |
        | Semua proses penyimpanan dibungkus dalam transaction.
        | Jika ditemukan satu saja data duplikat atau error,
        | seluruh transaksi akan di-rollback.
        |
        */

        DB::transaction(function () use ($request) {
            /*
            |--------------------------------------------------------------------------
            | PENYIMPANAN DATA SEMENTARA
            |--------------------------------------------------------------------------
            */
            $preparedCuttings = [];

            /*
            |--------------------------------------------------------------------------
            | TRACK DUPLIKAT NAMA DALAM REQUEST
            |--------------------------------------------------------------------------
            */
            $cuttingNames = [];

            foreach ($request->cuttings as $cutting) {
                /*
                |--------------------------------------------------------------------------
                | NORMALISASI NAMA
                |--------------------------------------------------------------------------
                */
                $cuttingName = trim($cutting['name']);
                $normalizedName = strtolower($cuttingName);

                /*
                |--------------------------------------------------------------------------
                | CEK DUPLIKAT NAMA DALAM REQUEST
                |--------------------------------------------------------------------------
                */
                if (in_array($normalizedName, $cuttingNames, true)) {
                    throw ValidationException::withMessages([
                        'cuttings' => "Jasa potong \"{$cuttingName}\" terduplikat dalam data yang dikirim.",
                    ]);
                }

                $cuttingNames[] = $normalizedName;

                /*
                |--------------------------------------------------------------------------
                | CEK DUPLIKAT MASTER DI DATABASE
                |--------------------------------------------------------------------------
                */
                $existingCutting = DB::table('m_cuttings')
                    ->whereRaw(
                        'LOWER(TRIM(name)) = ?',
                        [$normalizedName]
                    )
                    ->first();

                if ($existingCutting) {
                    throw ValidationException::withMessages([
                        'cuttings' => "Jasa potong \"{$cuttingName}\" sudah terdaftar.",
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
                $isPotong = ($normalizedName === 'potong');

                if (!$isPotong || (!empty($cutting['sizes']) && is_array($cutting['sizes']))) {
                    foreach ($cutting['sizes'] ?? [] as $size) {
                        if ($isPotong && (empty($size['width']) || empty($size['unit']))) {
                            continue;
                        }

                        $width = $size['width'];
                        $length = isset($size['length']) && $size['length'] !== '' ? $size['length'] : null;
                        $unit = trim($size['unit']);
                        $normalizedUnit = strtolower($unit);

                        /*
                        |--------------------------------------------------------------------------
                        | BUAT IDENTITAS UKURAN
                        |--------------------------------------------------------------------------
                        */
                        $sizeKey = $width . '|' . ($length ?? 'NULL') . '|' . $normalizedUnit;

                        if (in_array($sizeKey, $requestSizes, true)) {
                            throw ValidationException::withMessages([
                                'sizes' => "Ukuran {$width}" . ($length !== null ? " × {$length}" : '') . " {$unit} untuk Jasa Potong \"{$cuttingName}\" terduplikat.",
                            ]);
                        }

                        $requestSizes[] = $sizeKey;

                        $preparedSizes[] = [
                            'width' => $width,
                            'length' => $length,
                            'unit' => $unit,
                        ];
                    }
                }

                $preparedCuttings[] = [
                    'name' => $cuttingName,
                    'status' => $cutting['status'],
                    'sizes' => $preparedSizes,
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | INSERT MASTER JASA POTONG & UKURAN
            |--------------------------------------------------------------------------
            */
            foreach ($preparedCuttings as $cutting) {
                $now = now();
                $userName = Auth::user()->name ?? 'System';

                $cuttingId = DB::table('m_cuttings')->insertGetId([
                    'name' => $cutting['name'],
                    'status' => $cutting['status'],
                    'created_by' => $userName,
                    'created_at' => $now,
                    'updated_by' => $userName,
                    'updated_at' => $now,
                ]);

                foreach ($cutting['sizes'] as $size) {
                    DB::table('m_cutting_sizes')->insert([
                        'cutting_id' => $cuttingId,
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
        });

        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */
        return redirect()
            ->route('admin_cuttings')
            ->with(
                'success',
                'Jasa potong berhasil disimpan.'
            );
    }

    public function edit($id)
    {
        $cutting = DB::table('m_cuttings')
            ->where('id', $id)
            ->first();

        if (!$cutting) {
            return response()->json([
                'success' => false,
                'message' => 'Data jasa potong tidak ditemukan.'
            ], 404);
        }

        $sizes = DB::table('m_cutting_sizes')
            ->where('cutting_id', $id)
            ->orderBy('width')
            ->orderBy('length')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $cutting->id,
                'name' => $cutting->name,
                'status' => $cutting->status,
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
        $isPotong = strtolower(trim($request->name ?? '')) === 'potong';

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:Active,Inactive'],
        ];

        if (!$isPotong) {
            $rules['sizes'] = ['required', 'array', 'min:1'];
            $rules['sizes.*.id'] = ['nullable', 'integer'];
            $rules['sizes.*.width'] = ['required', 'numeric', 'gt:0'];
            $rules['sizes.*.length'] = ['nullable', 'numeric', 'gt:0'];
            $rules['sizes.*.unit'] = ['required', 'string', 'max:255'];
        } else {
            $rules['sizes'] = ['nullable', 'array'];
            $rules['sizes.*.id'] = ['nullable', 'integer'];
            $rules['sizes.*.width'] = ['nullable', 'numeric', 'gt:0'];
            $rules['sizes.*.length'] = ['nullable', 'numeric', 'gt:0'];
            $rules['sizes.*.unit'] = ['nullable', 'string', 'max:255'];
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {
            /*
            |--------------------------------------------------------------------------
            | 1. Ambil & lock data jasa potong
            |--------------------------------------------------------------------------
            */
            $cutting = DB::table('m_cuttings')
                ->where('id', $id)
                ->lockForUpdate()
                ->first();

            if (!$cutting) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Data jasa potong tidak ditemukan.'
                ], 404);
            }

            /*
            |--------------------------------------------------------------------------
            | 2. Normalisasi nama
            |--------------------------------------------------------------------------
            */
            $cuttingName = trim($request->name);
            $normalizedName = strtolower($cuttingName);

            /*
            |--------------------------------------------------------------------------
            | 3. Cek duplikat nama jasa potong
            |--------------------------------------------------------------------------
            */
            $duplicateName = DB::table('m_cuttings')
                ->where('id', '<>', $id)
                ->whereRaw(
                    'LOWER(TRIM(name)) = ?',
                    [$normalizedName]
                )
                ->exists();

            if ($duplicateName) {
                throw ValidationException::withMessages([
                    'name' => "Jasa potong \"{$cuttingName}\" sudah terdaftar."
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

            if (!$isPotong || (!empty($request->sizes) && is_array($request->sizes))) {
                foreach ($request->sizes ?? [] as $size) {
                    if ($isPotong && (empty($size['width']) || empty($size['unit']))) {
                        continue;
                    }

                    $sizeId = !empty($size['id'])
                        ? (int) $size['id']
                        : null;

                    $width = $size['width'];

                    $length = isset($size['length']) && $size['length'] !== ''
                        ? $size['length']
                        : null;

                    $unit = trim($size['unit']);
                    $normalizedUnit = strtolower($unit);

                    /*
                    |--------------------------------------------------------------------------
                    | 4a. Jika ada ID ukuran, pastikan ukuran tersebut milik cutting ini
                    |--------------------------------------------------------------------------
                    */
                    if ($sizeId !== null) {
                        $existingSize = DB::table('m_cutting_sizes')
                            ->where('id', $sizeId)
                            ->where('cutting_id', $id)
                            ->first();

                        if (!$existingSize) {
                            throw ValidationException::withMessages([
                                'sizes' => 'Data ukuran jasa potong tidak valid.'
                            ]);
                        }

                        if (in_array($sizeId, $submittedSizeIds, true)) {
                            throw ValidationException::withMessages([
                                'sizes' => "Ukuran jasa potong dengan ID {$sizeId} dikirim lebih dari satu kali."
                            ]);
                        }

                        $submittedSizeIds[] = $sizeId;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | 4b. Cek duplikat ukuran dalam request
                    |--------------------------------------------------------------------------
                    */
                    $sizeKey = $width . '|' . ($length ?? 'NULL') . '|' . $normalizedUnit;

                    if (in_array($sizeKey, $sizeKeys, true)) {
                        throw ValidationException::withMessages([
                            'sizes' => "Ukuran {$width}" . ($length !== null ? " × {$length}" : '') . " {$unit} untuk Jasa Potong \"{$cuttingName}\" terduplikat."
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
            }

            /*
            |--------------------------------------------------------------------------
            | 5. Update data utama jasa potong
            |--------------------------------------------------------------------------
            */
            $now = now();
            $userName = Auth::user()->name ?? 'System';

            DB::table('m_cuttings')
                ->where('id', $id)
                ->update([
                    'name' => $cuttingName,
                    'status' => $request->status,
                    'updated_by' => $userName,
                    'updated_at' => $now,
                ]);

            /*
            |--------------------------------------------------------------------------
            | 6. Hapus ukuran yang dihapus dari modal
            |--------------------------------------------------------------------------
            */
            $existingSizeIds = DB::table('m_cutting_sizes')
                ->where('cutting_id', $id)
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
                DB::table('m_cutting_sizes')
                    ->where('cutting_id', $id)
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
                    DB::table('m_cutting_sizes')
                        ->where('id', $size['id'])
                        ->where('cutting_id', $id)
                        ->update([
                            'width' => $size['width'],
                            'length' => $size['length'],
                            'unit' => $size['unit'],
                            'updated_by' => $userName,
                            'updated_at' => $now,
                        ]);
                } else {
                    DB::table('m_cutting_sizes')
                        ->insert([
                            'cutting_id' => $id,
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
                'message' => 'Jasa potong berhasil diperbarui.'
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
                'message' => 'Terjadi kesalahan pada server saat memperbarui data.'
            ], 500);
        }
    }

    public function details($id)
    {
        /*
        |--------------------------------------------------------------------------
        | DATA UTAMA JASA POTONG
        |--------------------------------------------------------------------------
        */
        $cutting = DB::table('m_cuttings as c')
            ->where('c.id', $id)
            ->select(
                'c.id',
                'c.name',
                'c.status',
                'c.created_by',
                'c.created_at',
                'c.updated_by',
                'c.updated_at'
            )
            ->first();

        if (!$cutting) {
            return response()->json([
                'success' => false,
                'message' => 'Data jasa potong tidak ditemukan.'
            ], 404);
        }

        /*
        |--------------------------------------------------------------------------
        | DATA UKURAN JASA POTONG
        |--------------------------------------------------------------------------
        */
        $sizes = DB::table('m_cutting_sizes')
            ->where('cutting_id', $cutting->id)
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
                'id' => $cutting->id,
                'name' => $cutting->name,
                'status' => $cutting->status,
                'created_by' => $cutting->created_by,
                'created_at' => $cutting->created_at,
                'updated_by' => $cutting->updated_by,
                'updated_at' => $cutting->updated_at,
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
        return $this->deleteCuttings([(int) $id]);
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array', 'min:1', 'max:1000'],
            'ids.*' => ['required', 'integer', 'min:1', 'distinct'],
        ]);
        return $this->deleteCuttings(array_map('intval', $validated['ids']));
    }

    private function deleteCuttings(array $ids)
    {
        return DB::transaction(function () use ($ids) {
            $cuttings = DB::table('m_cuttings')->whereIn('id', $ids)->lockForUpdate()->get(['id']);
            if ($cuttings->count() !== count($ids)) {
                return response()->json(['success' => false, 'message' => 'Sebagian jasa potong tidak ditemukan. Tidak ada data yang dihapus.'], 404);
            }
            foreach (Schema::getTables() as $table) {
                if ($table['name'] === 'm_cuttings') {
                    continue;
                }
                $tableName = $table['schema_qualified_name'];
                $columns = Schema::hasColumn($tableName, 'cutting_id') ? ['cutting_id'] : [];
                foreach (Schema::getForeignKeys($tableName) as $foreignKey) {
                    if ($foreignKey['foreign_table'] === 'm_cuttings') {
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
                            'message' => 'Jasa potong tidak dapat dihapus karena masih memiliki ukuran atau digunakan oleh data lain. Tidak ada data yang dihapus.',
                        ], 422);
                    }
                }
            }
            $deleted = DB::table('m_cuttings')->whereIn('id', $ids)->delete();
            return response()->json([
                'success' => true,
                'message' => $deleted === 1 ? 'Jasa potong berhasil dihapus.' : "{$deleted} jasa potong berhasil dihapus.",
            ]);
        });
    }

    public function export()
    {
        return Excel::download(
            new CuttingsExport(),
            'master_jasa_potong.xlsx'
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ], [
            'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'file.file'     => 'File yang dipilih tidak valid.',
            'file.mimes'    => 'File harus berformat Excel (.xlsx atau .xls).',
        ]);

        try {

            $import = new CuttingsImport();

            Excel::import(
                $import,
                $request->file('file')
            );

            return response()->json([
                'success' => true,
                'message' =>
                $import->importedCuttings .
                    ' jasa potong dan ' .
                    $import->importedSizes .
                    ' ukuran berhasil diimport.',
                'imported_cuttings' => $import->importedCuttings,
                'imported_sizes'    => $import->importedSizes,
            ]);
        } catch (Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}

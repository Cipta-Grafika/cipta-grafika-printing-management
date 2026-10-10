<?php

namespace App\Http\Controllers\admin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Imports\MaterialsImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MaterialsExport;
use Exception;

class MaterialCT extends Controller
{
    public function index()
    {
        $data['categories'] = DB::table('m_categories')
            ->orderBy('name', 'asc')
            ->get()->toArray();

        return view('admin.material.index')->with($data);
    }

    public function data(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | TOTAL SEMUA DATA
    |--------------------------------------------------------------------------
    */

        $totalRecords = DB::table('m_materials')->count();


        /*
    |--------------------------------------------------------------------------
    | QUERY UTAMA
    |--------------------------------------------------------------------------
    */

        $query = DB::table('m_materials')
            ->leftJoin(
                'm_categories',
                'm_materials.category_id',
                '=',
                'm_categories.id'
            )
            ->select(
                'm_materials.id',
                'm_materials.material_code',
                'm_materials.material_name',
                'm_materials.status',
                'm_categories.name as category_name'
            );

        $categoryId = $request->input('category_id');

        if ($categoryId !== null && $categoryId !== '') {
            $query->where('m_materials.category_id', $categoryId);
        }


        /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

        $search = $request->input('search.value');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw(
                    'LOWER(m_materials.material_code) LIKE ?',
                    ['%' . strtolower($search) . '%']
                )
                    ->orWhereRaw(
                        'LOWER(m_materials.material_name) LIKE ?',
                        ['%' . strtolower($search) . '%']
                    )
                    ->orWhereRaw(
                        'LOWER(m_categories.name) LIKE ?',
                        ['%' . strtolower($search) . '%']
                    )
                    ->orWhereRaw(
                        'LOWER(m_materials.status) LIKE ?',
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

        $materials = $query
            ->orderByRaw(
                "CASE WHEN m_materials.status = 'Active' THEN 0 ELSE 1 END"
            )
            ->orderBy('m_materials.material_name', 'asc')
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
            'data' => $materials,
        ]);
    }

    public function edit($id)
    {
        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA MATERIAL
    |--------------------------------------------------------------------------
    */

        $id = (int) $id;

        $material = DB::table('m_materials')
            ->where('id', $id)
            ->first();

        /*
    |--------------------------------------------------------------------------
    | JIKA MATERIAL TIDAK DITEMUKAN
    |--------------------------------------------------------------------------
    */

        if (!$material) {
            return response()->json([
                'message' => 'Data material tidak ditemukan.'
            ], 404);
        }

        /*
    |--------------------------------------------------------------------------
    | AMBIL MATERIAL SIZES
    |--------------------------------------------------------------------------
    */

        $sizes = DB::table('m_material_sizes')
            ->where('material_id', $id)
            ->orderBy('width')
            ->orderBy('unit')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | KIRIM DATA MATERIAL + SIZES
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'id' => $material->id,
            'category_id' => $material->category_id,
            'material_code' => $material->material_code,
            'material_name' => $material->material_name,
            'status' => $material->status,
            'sizes' => $sizes,
        ]);
    }


    public function update(Request $request, $id)
    {
        /*
    |--------------------------------------------------------------------------
    | CEK DATA MATERIAL
    |--------------------------------------------------------------------------
    */

        $id = (int) $id;

        $material = DB::table('m_materials')
            ->where('id', $id)
            ->first();

        /*
    |--------------------------------------------------------------------------
    | JIKA MATERIAL TIDAK DITEMUKAN
    |--------------------------------------------------------------------------
    */

        if (!$material) {
            return redirect()
                ->route('admin_materials')
                ->with(
                    'error',
                    'Data material tidak ditemukan.'
                );
        }


        /*
    |--------------------------------------------------------------------------
    | VALIDASI DATA DASAR
    |--------------------------------------------------------------------------
    */

        $request->validate(
            [
                'category_id' => [
                    'required',
                    'exists:m_categories,id',
                ],

                'material_code' => [
                    'nullable',
                    'string',
                    'max:255',
                ],

                'material_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'status' => [
                    'required',
                    'in:Active,Inactive',
                ],


                /*
            |--------------------------------------------------------------------------
            | MATERIAL SIZES
            |--------------------------------------------------------------------------
            */

                'sizes' => [
                    'required',
                    'array',
                    'min:1',
                ],

                'sizes.*.id' => [
                    'nullable',
                    'integer',
                ],

                'sizes.*.width' => [
                    'required',
                    'numeric',
                    'gt:0',
                ],

                'sizes.*.length' => [
                    'nullable',
                    'numeric',
                    'gt:0',
                ],

                'sizes.*.unit' => [
                    'required',
                    'string',
                    'max:255',
                ],


                /*
            |--------------------------------------------------------------------------
            | DELETED SIZES
            |--------------------------------------------------------------------------
            */

                'deleted_sizes' => [
                    'nullable',
                    'array',
                ],

                'deleted_sizes.*' => [
                    'integer',
                ],
            ],
            [
                'category_id.required' =>
                'Kategori wajib dipilih.',

                'category_id.exists' =>
                'Kategori yang dipilih tidak valid.',


                'material_name.required' =>
                'Nama material wajib diisi.',

                'material_name.string' =>
                'Nama material harus berupa teks.',

                'material_name.max' =>
                'Nama material terlalu panjang.',


                'status.required' =>
                'Status wajib dipilih.',

                'status.in' =>
                'Status tidak valid.',


                'sizes.required' =>
                'Minimal satu ukuran material harus diisi.',

                'sizes.min' =>
                'Minimal satu ukuran material harus diisi.',


                'sizes.*.width.required' =>
                'Lebar material wajib diisi.',

                'sizes.*.width.numeric' =>
                'Lebar material harus berupa angka.',

                'sizes.*.width.gt' =>
                'Lebar material harus lebih dari 0.',


                'sizes.*.length.numeric' =>
                'Panjang material harus berupa angka.',

                'sizes.*.length.gt' =>
                'Panjang material harus lebih dari 0.',


                'sizes.*.unit.required' =>
                'Satuan ukuran wajib dipilih.',

                'sizes.*.unit.max' =>
                'Satuan ukuran terlalu panjang.',
            ]
        );


        /*
    |--------------------------------------------------------------------------
    | NORMALISASI DATA
    |--------------------------------------------------------------------------
    */

        $materialName = trim(
            $request->material_name
        );

        $materialCode =
            !empty(trim($request->material_code ?? ''))
            ? trim($request->material_code)
            : null;


        /*
    |--------------------------------------------------------------------------
    | CEK KATEGORI
    |--------------------------------------------------------------------------
    */

        $category = DB::table('m_categories')
            ->where('id', $request->category_id)
            ->first();

        if (!$category) {
            return back()
                ->withInput()
                ->withErrors([
                    'category_id' =>
                    'Kategori yang dipilih tidak valid.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | TENTUKAN APAKAH LENGTH DIPERLUKAN
    |--------------------------------------------------------------------------
    |
    | Outdoor dan Indoor:
    | - Length tidak digunakan
    | - Length disimpan NULL
    |
    | Kategori lainnya:
    | - Length wajib
    |
    */

        $categoryName =
            strtolower(trim($category->name));

        $requiresLength =
            !in_array(
                $categoryName,
                [
                    'outdoor',
                    'indoor',
                ],
                true
            );


        /*
    |--------------------------------------------------------------------------
    | VALIDASI LENGTH BERDASARKAN KATEGORI
    |--------------------------------------------------------------------------
    */

        foreach ($request->sizes as $index => $size) {

            $length =
                isset($size['length']) &&
                $size['length'] !== ''
                ? $size['length']
                : null;


            /*
        |--------------------------------------------------------------------------
        | KATEGORI YANG MEMBUTUHKAN LENGTH
        |--------------------------------------------------------------------------
        */

            if ($requiresLength) {

                if ($length === null) {

                    return back()
                        ->withInput()
                        ->withErrors([
                            "sizes.$index.length" =>
                            'Panjang material wajib diisi untuk kategori ini.',
                        ]);
                }


                if ((float) $length <= 0) {

                    return back()
                        ->withInput()
                        ->withErrors([
                            "sizes.$index.length" =>
                            'Panjang material harus lebih dari 0.',
                        ]);
                }
            }
        }


        /*
    |--------------------------------------------------------------------------
    | CEK DUPLIKAT NAMA MATERIAL
    |--------------------------------------------------------------------------
    |
    | Nama material boleh sama dengan material yang sedang diedit.
    | Yang tidak boleh adalah sama dengan material lain.
    |
    */

        $duplicateMaterial = DB::table('m_materials')
            ->whereRaw(
                'LOWER(TRIM(material_name)) = ?',
                [
                    strtolower($materialName),
                ]
            )
            ->where('id', '!=', $id)
            ->exists();

        if ($duplicateMaterial) {

            return back()
                ->withInput()
                ->withErrors([
                    'material_name' =>
                    'Nama material sudah terdaftar.',
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | VALIDASI SIZE MILIK MATERIAL
    |--------------------------------------------------------------------------
    */

        foreach ($request->sizes as $index => $size) {

            if (!empty($size['id'])) {

                $sizeExists = DB::table('m_material_sizes')
                    ->where('id', $size['id'])
                    ->where('material_id', $id)
                    ->exists();

                if (!$sizeExists) {

                    return back()
                        ->withInput()
                        ->withErrors([
                            "sizes.$index.id" =>
                            'Data ukuran material tidak valid.',
                        ]);
                }
            }
        }


        /*
    |--------------------------------------------------------------------------
    | VALIDASI DUPLIKAT SIZE
    |--------------------------------------------------------------------------
    */

        $submittedSizes = [];


        foreach ($request->sizes as $index => $size) {

            /*
        |--------------------------------------------------------------------------
        | WIDTH
        |--------------------------------------------------------------------------
        */

            $width = (float) $size['width'];


            /*
        |--------------------------------------------------------------------------
        | UNIT
        |--------------------------------------------------------------------------
        */

            $unit = strtolower(
                trim($size['unit'])
            );


            /*
        |--------------------------------------------------------------------------
        | LENGTH
        |--------------------------------------------------------------------------
        */

            $length =
                isset($size['length']) &&
                $size['length'] !== ''
                ? (float) $size['length']
                : null;


            /*
        |--------------------------------------------------------------------------
        | OUTDOOR / INDOOR
        |--------------------------------------------------------------------------
        |
        | Length tidak digunakan.
        |
        */

            if (!$requiresLength) {
                $length = null;
            }


            /*
        |--------------------------------------------------------------------------
        | BUAT KEY DUPLIKAT FORM
        |--------------------------------------------------------------------------
        |
        | Kategori dengan length:
        | width + length + unit
        |
        | Outdoor / Indoor:
        | width + unit
        |
        */

            if ($requiresLength) {

                $sizeKey =
                    $this->normalizeDecimal($width)
                    . '|'
                    . $this->normalizeDecimal($length)
                    . '|'
                    . $unit;
            } else {

                $sizeKey =
                    $this->normalizeDecimal($width)
                    . '|'
                    . $unit;
            }


            /*
        |--------------------------------------------------------------------------
        | CEK DUPLIKAT ANTAR ROW FORM
        |--------------------------------------------------------------------------
        */

            if (isset($submittedSizes[$sizeKey])) {

                return back()
                    ->withInput()
                    ->withErrors([
                        "sizes.$index.width" =>
                        $requiresLength
                            ? 'Ukuran dengan lebar, panjang, dan satuan yang sama sudah ada.'
                            : 'Ukuran dengan lebar dan satuan yang sama sudah ada.',
                    ]);
            }


            $submittedSizes[$sizeKey] = true;


            /*
        |--------------------------------------------------------------------------
        | CEK DUPLIKAT DENGAN DATABASE
        |--------------------------------------------------------------------------
        */

            $query = DB::table('m_material_sizes')
                ->where('material_id', $id)
                ->where('width', $width)
                ->whereRaw(
                    'LOWER(TRIM(unit)) = ?',
                    [$unit]
                );


            /*
        |--------------------------------------------------------------------------
        | KATEGORI DENGAN LENGTH
        |--------------------------------------------------------------------------
        */

            if ($requiresLength) {

                $query->where(
                    'length',
                    $length
                );
            } else {

                /*
            |--------------------------------------------------------------------------
            | OUTDOOR / INDOOR
            |--------------------------------------------------------------------------
            */

                $query->whereNull(
                    'length'
                );
            }


            /*
        |--------------------------------------------------------------------------
        | JIKA UPDATE SIZE LAMA
        |--------------------------------------------------------------------------
        |
        | ID size yang sedang diedit tidak dianggap duplikat.
        |
        */

            if (!empty($size['id'])) {

                $query->where(
                    'id',
                    '!=',
                    $size['id']
                );
            }


            /*
        |--------------------------------------------------------------------------
        | CEK EXISTS
        |--------------------------------------------------------------------------
        */

            if ($query->exists()) {

                return back()
                    ->withInput()
                    ->withErrors([
                        "sizes.$index.width" =>
                        $requiresLength
                            ? 'Ukuran dengan lebar, panjang, dan satuan tersebut sudah terdaftar.'
                            : 'Ukuran dengan lebar dan satuan tersebut sudah terdaftar.',
                    ]);
            }
        }


        /*
    |--------------------------------------------------------------------------
    | VALIDASI DELETED SIZES
    |--------------------------------------------------------------------------
    */

        if ($request->filled('deleted_sizes')) {

            foreach ($request->deleted_sizes as $sizeId) {

                $sizeExists = DB::table('m_material_sizes')
                    ->where('id', $sizeId)
                    ->where('material_id', $id)
                    ->exists();

                if (!$sizeExists) {

                    return back()
                        ->withInput()
                        ->withErrors([
                            'deleted_sizes' =>
                            'Data ukuran yang akan dihapus tidak valid.',
                        ]);
                }
            }
        }


        /*
    |--------------------------------------------------------------------------
    | SIMPAN PERUBAHAN
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use (
            $request,
            $id,
            $materialName,
            $materialCode,
            $requiresLength
        ) {

            $now = now();

            $updatedBy =
                Auth::user()->name ?? 'System';


            /*
        |--------------------------------------------------------------------------
        | UPDATE DATA MATERIAL
        |--------------------------------------------------------------------------
        */

            DB::table('m_materials')
                ->where('id', $id)
                ->update([
                    'category_id' =>
                    $request->category_id,

                    'material_code' =>
                    $materialCode,

                    'material_name' =>
                    $materialName,

                    'status' =>
                    $request->status,

                    'updated_by' =>
                    $updatedBy,

                    'updated_at' =>
                    $now,
                ]);


            /*
        |--------------------------------------------------------------------------
        | HAPUS MATERIAL SIZES
        |--------------------------------------------------------------------------
        */

            if ($request->filled('deleted_sizes')) {

                DB::table('m_material_sizes')
                    ->where('material_id', $id)
                    ->whereIn(
                        'id',
                        $request->deleted_sizes
                    )
                    ->delete();
            }


            /*
        |--------------------------------------------------------------------------
        | UPDATE / INSERT MATERIAL SIZES
        |--------------------------------------------------------------------------
        */

            foreach ($request->sizes as $size) {

                /*
            |--------------------------------------------------------------------------
            | WIDTH
            |--------------------------------------------------------------------------
            */

                $width =
                    (float) $size['width'];


                /*
            |--------------------------------------------------------------------------
            | UNIT
            |--------------------------------------------------------------------------
            */

                $unit =
                    trim($size['unit']);


                /*
            |--------------------------------------------------------------------------
            | LENGTH
            |--------------------------------------------------------------------------
            */

                if ($requiresLength) {

                    $length =
                        isset($size['length']) &&
                        $size['length'] !== ''
                        ? (float) $size['length']
                        : null;
                } else {

                    /*
                |--------------------------------------------------------------------------
                | OUTDOOR / INDOOR
                |--------------------------------------------------------------------------
                |
                | Length selalu NULL.
                |
                */

                    $length = null;
                }


                /*
            |--------------------------------------------------------------------------
            | UPDATE SIZE LAMA
            |--------------------------------------------------------------------------
            */

                if (!empty($size['id'])) {

                    DB::table('m_material_sizes')
                        ->where('id', $size['id'])
                        ->where('material_id', $id)
                        ->update([
                            'width' =>
                            $width,

                            'length' =>
                            $length,

                            'unit' =>
                            $unit,

                            'updated_by' =>
                            $updatedBy,

                            'updated_at' =>
                            $now,
                        ]);


                    /*
            |--------------------------------------------------------------------------
            | INSERT SIZE BARU
            |--------------------------------------------------------------------------
            */
                } else {

                    DB::table('m_material_sizes')
                        ->insert([
                            'material_id' =>
                            $id,

                            'width' =>
                            $width,

                            'length' =>
                            $length,

                            'unit' =>
                            $unit,

                            'created_by' =>
                            $updatedBy,

                            'created_at' =>
                            $now,

                            'updated_by' =>
                            $updatedBy,

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
            ->route('admin_materials')
            ->with(
                'success',
                'Material berhasil diperbarui.'
            );
    }


    private function normalizeDecimal($value): string
    {
        return rtrim(
            rtrim(
                number_format(
                    (float) $value,
                    2,
                    '.',
                    ''
                ),
                '0'
            ),
            '.'
        );
    }

    public function store(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDASI DASAR
    |--------------------------------------------------------------------------
    */

        $request->validate([
            'materials' => [
                'required',
                'array',
                'min:1',
            ],

            'materials.*.category_id' => [
                'required',
                'exists:m_categories,id',
            ],

            'materials.*.material_code' => [
                'nullable',
                'string',
                'max:255',
            ],

            'materials.*.material_name' => [
                'required',
                'string',
                'max:255',
            ],

            'materials.*.status' => [
                'required',
                'in:Active,Inactive',
            ],

            'materials.*.sizes' => [
                'required',
                'array',
                'min:1',
            ],

            'materials.*.sizes.*.width' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'materials.*.sizes.*.length' => [
                'nullable',
                'numeric',
                'gt:0',
            ],

            'materials.*.sizes.*.unit' => [
                'required',
                'string',
                'max:255',
            ],
        ]);


        /*
    |--------------------------------------------------------------------------
    | TRANSACTION
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use ($request) {

            foreach ($request->materials as $material) {

                $now = now();

                $materialName =
                    trim($material['material_name']);


                /*
            |--------------------------------------------------------------------------
            | CARI MATERIAL BERDASARKAN NAMA
            |--------------------------------------------------------------------------
            */

                $existingMaterial = DB::table('m_materials')
                    ->whereRaw(
                        'LOWER(TRIM(material_name)) = ?',
                        [strtolower($materialName)]
                    )
                    ->first();


                /*
            |--------------------------------------------------------------------------
            | TENTUKAN MATERIAL
            |--------------------------------------------------------------------------
            */

                if (!$existingMaterial) {

                    /*
                |--------------------------------------------------------------------------
                | MATERIAL BARU
                |--------------------------------------------------------------------------
                */

                    $materialId = DB::table('m_materials')
                        ->insertGetId([
                            'category_id' =>
                            $material['category_id'],

                            'material_code' =>
                            !empty($material['material_code'])
                                ? trim($material['material_code'])
                                : null,

                            'material_name' =>
                            $materialName,

                            'status' =>
                            $material['status'],

                            'created_by' =>
                            Auth::user()->name ?? 'System',

                            'created_at' =>
                            $now,

                            'updated_at' =>
                            $now,
                        ]);


                    /*
                |--------------------------------------------------------------------------
                | KATEGORI MATERIAL BARU
                |--------------------------------------------------------------------------
                */

                    $categoryId =
                        $material['category_id'];
                } else {

                    /*
                |--------------------------------------------------------------------------
                | MATERIAL SUDAH ADA
                |--------------------------------------------------------------------------
                |
                | Jangan insert ulang m_materials.
                | Jangan mengubah kategori, kode, atau status.
                |
                */

                    $materialId =
                        $existingMaterial->id;


                    /*
                |--------------------------------------------------------------------------
                | GUNAKAN KATEGORI MATERIAL EXISTING
                |--------------------------------------------------------------------------
                */

                    $categoryId =
                        $existingMaterial->category_id;
                }


                /*
            |--------------------------------------------------------------------------
            | AMBIL KATEGORI
            |--------------------------------------------------------------------------
            */

                $category = DB::table('m_categories')
                    ->where('id', $categoryId)
                    ->first();


                /*
            |--------------------------------------------------------------------------
            | CEK APAKAH KATEGORI DISPLAY
            |--------------------------------------------------------------------------
            */

                $isLengthRequired =
                    $category &&
                    !in_array(
                        strtolower(trim($category->name)),
                        ['outdoor', 'indoor'],
                        true
                    );


                /*
            |--------------------------------------------------------------------------
            | SIMPAN MATERIAL SIZES
            |--------------------------------------------------------------------------
            */

                foreach ($material['sizes'] as $size) {

                    /*
                |--------------------------------------------------------------------------
                | WIDTH
                |--------------------------------------------------------------------------
                */

                    $width =
                        $size['width'];


                    /*
                |--------------------------------------------------------------------------
                | LENGTH
                |--------------------------------------------------------------------------
                |
                | Display:
                |   length digunakan.
                |
                | Non Display:
                |   length selalu NULL.
                |
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

                    $unit =
                        trim($size['unit']);


                    /*
                |--------------------------------------------------------------------------
                | VALIDASI KHUSUS DISPLAY
                |--------------------------------------------------------------------------
                */

                    if ($isLengthRequired) {

                        if ($length === null) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'length' =>
                                'Panjang wajib diisi untuk kategori ini.',
                            ]);
                        }

                        if ((float) $length <= 0) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'length' =>
                                'Panjang harus lebih besar dari 0.',
                            ]);
                        }
                    } else {

                        $length = null;
                    }


                    /*
                |--------------------------------------------------------------------------
                | CEK UKURAN EXISTING
                |--------------------------------------------------------------------------
                */

                    $sizeQuery = DB::table('m_material_sizes')
                        ->where(
                            'material_id',
                            $materialId
                        )
                        ->where(
                            'width',
                            $width
                        )
                        ->whereRaw(
                            'LOWER(TRIM(unit)) = ?',
                            [strtolower($unit)]
                        );


                    /*
                |--------------------------------------------------------------------------
                | JIKA DISPLAY
                |--------------------------------------------------------------------------
                |
                | Unique berdasarkan:
                |
                | material_id
                | width
                | length
                | unit
                |
                */

                    if ($isLengthRequired) {

                        $sizeQuery->where(
                            'length',
                            $length
                        );
                    } else {

                        $sizeQuery->whereNull('length');
                    }


                    $existingSize =
                        $sizeQuery->exists();


                    /*
                |--------------------------------------------------------------------------
                | INSERT JIKA BELUM ADA
                |--------------------------------------------------------------------------
                */

                    if (!$existingSize) {

                        DB::table('m_material_sizes')
                            ->insert([
                                'material_id' =>
                                $materialId,

                                'width' =>
                                $width,

                                'length' =>
                                $length,

                                'unit' =>
                                $unit,

                                'created_by' =>
                                Auth::user()->name ?? 'System',

                                'created_at' =>
                                $now,

                                'updated_by' =>
                                Auth::user()->name ?? 'System',

                                'updated_at' =>
                                $now,
                            ]);
                    }
                }
            }
        });


        /*
    |--------------------------------------------------------------------------
    | REDIRECT
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('admin_materials')
            ->with(
                'success',
                'Material berhasil disimpan.'
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
            'file.required' =>
            'Silakan pilih file Excel terlebih dahulu.',

            'file.file' =>
            'File yang dipilih tidak valid.',

            'file.mimes' =>
            'File harus berformat Excel (.xlsx atau .xls).',
        ]);

        try {

            /*
        |--------------------------------------------------------------------------
        | BUAT INSTANCE IMPORT
        |--------------------------------------------------------------------------
        */

            $import = new MaterialsImport();

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
        | RESPONSE SUCCESS
        |--------------------------------------------------------------------------
        */

            return response()->json([
                'success' => true,

                'message' =>
                $import->importedMaterials .
                    ' material dan ' .
                    $import->importedSizes .
                    ' ukuran berhasil diimport.',

                'imported_materials' =>
                $import->importedMaterials,

                'imported_sizes' =>
                $import->importedSizes,

                'skipped_duplicate' =>
                $import->skippedDuplicate,

                'skipped_invalid' =>
                $import->skippedInvalid,
            ]);
        } catch (Exception $e) {

            /*
        |--------------------------------------------------------------------------
        | RESPONSE ERROR
        |--------------------------------------------------------------------------
        */

            return response()->json([
                'success' => false,

                'message' =>
                $e->getMessage(),
            ], 422);
        }
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:m_categories,id'],
        ]);

        return Excel::download(
            new MaterialsExport(
                isset($validated['category_id'])
                    ? (int) $validated['category_id']
                    : null
            ),
            'master_material.xlsx'
        );
    }

    public function destroy($id)
    {
        $material = DB::table('m_materials')
            ->where('id', $id)
            ->first();

        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Data material tidak ditemukan.'
            ], 404);
        }

        DB::table('m_materials')
            ->where('id', $id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Material berhasil dihapus.'
        ]);
    }

    public function details($id)
    {
        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA MATERIAL
    |--------------------------------------------------------------------------
    */

        $id = (int) $id;

        $material = DB::table('m_materials')
            ->join(
                'm_categories',
                'm_materials.category_id',
                '=',
                'm_categories.id'
            )
            ->select(
                'm_materials.id',
                'm_materials.material_code',
                'm_materials.material_name',
                'm_materials.status',
                'm_categories.name as category_name'
            )
            ->where('m_materials.id', $id)
            ->first();

        /*
    |--------------------------------------------------------------------------
    | JIKA DATA TIDAK DITEMUKAN
    |--------------------------------------------------------------------------
    */

        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Data material tidak ditemukan.'
            ], 404);
        }

        /*
    |--------------------------------------------------------------------------
    | AMBIL MATERIAL SIZES
    |--------------------------------------------------------------------------
    */

        $sizes = DB::table('m_material_sizes')
            ->select(
                'id',
                'width',
                'length',
                'unit'
            )
            ->where('material_id', $id)
            ->orderBy('width')
            ->orderBy('unit')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'success' => true,

            'data' => [
                'id' => $material->id,
                'category_name' => $material->category_name,
                'material_code' => $material->material_code,
                'material_name' => $material->material_name,
                'status' => $material->status,
                'sizes' => $sizes,
            ]
        ]);
    }
}

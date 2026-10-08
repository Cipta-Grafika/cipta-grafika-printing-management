<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\MaterialSizesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MaterialSizesImport;

class MaterialSizeCT extends Controller
{
    public function index()
    {
        return view('admin.material-size.index');
    }

    public function data(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | TOTAL DATA
    |--------------------------------------------------------------------------
    | Menghitung jumlah material unik
    |--------------------------------------------------------------------------
    */

        $recordsTotal = DB::table('m_material_sizes')
            ->distinct('material_id')
            ->count('material_id');


        /*
    |--------------------------------------------------------------------------
    | QUERY
    |--------------------------------------------------------------------------
    */

        $query = DB::table('m_material_sizes')
            ->join(
                'm_materials',
                'm_material_sizes.material_id',
                '=',
                'm_materials.id'
            )
            ->select(
                'm_material_sizes.material_id',
                'm_materials.material_name',
                DB::raw('MAX(m_material_sizes.created_by) as created_by'),
                DB::raw('MAX(m_material_sizes.created_at) as created_at')
            )
            ->groupBy(
                'm_material_sizes.material_id',
                'm_materials.material_name'
            );


        /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

        if ($request->filled('search.value')) {

            $search = strtolower(
                $request->input('search.value')
            );

            $query->where(function ($q) use ($search) {

                $q->whereRaw(
                    'LOWER(m_materials.material_name) LIKE ?',
                    ["%{$search}%"]
                )
                    ->orWhereRaw(
                        'LOWER(m_material_sizes.created_by) LIKE ?',
                        ["%{$search}%"]
                    )
                    ->orWhereRaw(
                        'CAST(m_material_sizes.created_at AS TEXT) LIKE ?',
                        ["%{$search}%"]
                    );
            });
        }


        /*
    |--------------------------------------------------------------------------
    | FILTERED DATA
    |--------------------------------------------------------------------------
    */

        $recordsFiltered = $query->count();


        /*
    |--------------------------------------------------------------------------
    | ORDERING
    |--------------------------------------------------------------------------
    */

        $query->orderBy(
            'm_material_sizes.material_id',
            'desc'
        );


        /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $data = $query
            ->skip($start)
            ->take($length)
            ->get();


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function details($id)
    {
        /*
    |--------------------------------------------------------------------------
    | DECODE ID
    |--------------------------------------------------------------------------
    */

        $decodedId = base64_decode($id);


        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA MATERIAL
    |--------------------------------------------------------------------------
    */

        $data['material'] = DB::table('m_materials')
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
                'm_materials.created_by',
                'm_materials.created_at',
                'm_categories.name as category_name'
            )
            ->where('m_materials.id', $decodedId)
            ->first();


        /*
    |--------------------------------------------------------------------------
    | VALIDASI MATERIAL
    |--------------------------------------------------------------------------
    */

        if (!$data['material']) {
            abort(404);
        }


        /*
    |--------------------------------------------------------------------------
    | AMBIL SEMUA UKURAN MATERIAL
    |--------------------------------------------------------------------------
    */

        $data['sizes'] = DB::table('m_material_sizes')
            ->where('material_id', $decodedId)
            ->orderBy('width', 'asc')
            ->get();


        /*
    |--------------------------------------------------------------------------
    | KIRIM DATA KE VIEW
    |--------------------------------------------------------------------------
    */

        return view('admin.material-size.details')->with($data);
    }

    public function create()
    {
        $data['materials'] = DB::table('m_materials')
            ->where('status', 'Active')
            ->orderBy('material_name', 'asc')
            ->get();

        return view('admin.material-size.create')->with($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'material_sizes' => [
                'required',
                'array',
                'min:1',
            ],

            'material_sizes.*.material_id' => [
                'required',
                'integer',
                'exists:m_materials,id',
            ],

            'material_sizes.*.width' => [
                'required',
                'numeric',
                'min:0',
            ],

            'material_sizes.*.unit' => [
                'required',
                'string',
                'max:20',
            ],
        ]);

        DB::transaction(function () use ($request) {

            foreach ($request->material_sizes as $row) {

                DB::table('m_material_sizes')->insert([
                    'material_id' => $row['material_id'],
                    'width' => $row['width'],
                    'unit' => $row['unit'],
                    'created_by' => Auth::user()->name,
                    'created_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('admin_material_sizes')
            ->with(
                'success',
                'Ukuran material berhasil ditambahkan.'
            );
    }

    public function edit($id)
    {
        /*
    |--------------------------------------------------------------------------
    | DECODE ID
    |--------------------------------------------------------------------------
    */

        $decodedId = base64_decode($id);


        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA MATERIAL
    |--------------------------------------------------------------------------
    */

        $data['material'] = DB::table('m_materials')
            ->where('id', $decodedId)
            ->first();


        /*
    |--------------------------------------------------------------------------
    | VALIDASI MATERIAL
    |--------------------------------------------------------------------------
    */

        if (!$data['material']) {
            abort(404);
        }


        /*
    |--------------------------------------------------------------------------
    | AMBIL SEMUA UKURAN MATERIAL
    |--------------------------------------------------------------------------
    */

        $data['sizes'] = DB::table('m_material_sizes')
            ->where('material_id', $decodedId)
            ->orderBy('width', 'asc')
            ->get();


        /*
    |--------------------------------------------------------------------------
    | AMBIL DAFTAR MATERIAL
    |--------------------------------------------------------------------------
    */

        $data['materials'] = DB::table('m_materials')
            ->where('status', 'Active')
            ->orderBy('material_name', 'asc')
            ->get();


        /*
    |--------------------------------------------------------------------------
    | KIRIM DATA KE VIEW
    |--------------------------------------------------------------------------
    */

        return view('admin.material-size.edit')->with($data);
    }

    public function update(Request $request, $id)
    {
        /*
    |--------------------------------------------------------------------------
    | DECODE ID
    |--------------------------------------------------------------------------
    */

        $decodedId = base64_decode($id);


        /*
    |--------------------------------------------------------------------------
    | VALIDASI MATERIAL
    |--------------------------------------------------------------------------
    */

        $material = DB::table('m_materials')
            ->where('id', $decodedId)
            ->first();

        if (!$material) {
            abort(404);
        }


        /*
    |--------------------------------------------------------------------------
    | VALIDASI FORM
    |--------------------------------------------------------------------------
    */

        $request->validate([

            'material_sizes' => [
                'required',
                'array',
                'min:1',
            ],

            'material_sizes.*.material_id' => [
                'required',
                'integer',
                'exists:m_materials,id',
            ],

            'material_sizes.*.width' => [
                'required',
                'numeric',
                'min:0',
            ],

            'material_sizes.*.unit' => [
                'required',
                'string',
                'max:20',
            ],

        ]);


        /*
    |--------------------------------------------------------------------------
    | UPDATE MATERIAL SIZE
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use ($request, $decodedId) {

            /*
        | Hapus seluruh ukuran lama
        */

            DB::table('m_material_sizes')
                ->where('material_id', $decodedId)
                ->delete();


            /*
        | Insert ukuran baru
        */

            foreach ($request->material_sizes as $row) {

                DB::table('m_material_sizes')->insert([

                    'material_id' => $decodedId,
                    'width'       => $row['width'],
                    'unit'        => $row['unit'],

                    'created_by'  => Auth::user()->name,
                    'created_at'  => now(),

                ]);
            }
        });


        /*
    |--------------------------------------------------------------------------
    | REDIRECT
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('admin_material_sizes')
            ->with(
                'success',
                'Ukuran material berhasil diperbarui.'
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

        $import = new MaterialSizesImport();


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
    | MATERIAL TIDAK TERDAFTAR
    |--------------------------------------------------------------------------
    */

        if (count($import->invalidMaterials) > 0) {

            return response()->json([
                'success' => false,
                'message' => 'Import dibatalkan. Terdapat nama material yang tidak terdaftar di master material.',
                'invalid_materials' => array_values(
                    array_unique($import->invalidMaterials)
                ),
                'imported' => 0,
            ], 422);
        }


        /*
    |--------------------------------------------------------------------------
    | SATUAN TIDAK VALID
    |--------------------------------------------------------------------------
    */

        if (count($import->invalidUnits) > 0) {

            return response()->json([
                'success' => false,
                'message' => 'Import dibatalkan. Terdapat satuan yang tidak terdaftar atau tidak valid.',
                'invalid_units' => array_values(
                    array_unique($import->invalidUnits)
                ),
                'imported' => 0,
            ], 422);
        }


        /*
    |--------------------------------------------------------------------------
    | DUPLIKASI UKURAN MATERIAL
    |--------------------------------------------------------------------------
    */

        if (count($import->duplicateSizes) > 0) {

            return response()->json([
                'success' => false,
                'message' => 'Import dibatalkan. Terdapat data ukuran material yang duplikat.',
                'duplicate_sizes' => array_values(
                    array_unique($import->duplicateSizes)
                ),
                'imported' => 0,
            ], 422);
        }


        /*
    |--------------------------------------------------------------------------
    | TIDAK ADA DATA VALID
    |--------------------------------------------------------------------------
    */

        if ($import->imported === 0) {

            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data ukuran material yang valid untuk diimport.',
                'imported' => 0,
            ], 422);
        }


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'success' => true,
            'message' => $import->imported . ' data ukuran material berhasil diimport.',
            'imported' => $import->imported,
        ]);
    }



    public function export()
    {
        return Excel::download(
            new MaterialSizesExport(),
            'master_ukuran_material.xlsx'
        );
    }

    public function destroy($id)
    {
        /*
    |--------------------------------------------------------------------------
    | CEK MATERIAL
    |--------------------------------------------------------------------------
    */

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
                'success' => false,
                'message' => 'Data material tidak ditemukan.'
            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | CEK UKURAN MATERIAL
    |--------------------------------------------------------------------------
    */

        $hasSizes = DB::table('m_material_sizes')
            ->where('material_id', $id)
            ->exists();


        /*
    |--------------------------------------------------------------------------
    | JIKA TIDAK ADA UKURAN
    |--------------------------------------------------------------------------
    */

        if (!$hasSizes) {
            return response()->json([
                'success' => false,
                'message' => 'Data ukuran material tidak ditemukan.'
            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | HAPUS SEMUA UKURAN MATERIAL
    |--------------------------------------------------------------------------
    */

        DB::table('m_material_sizes')
            ->where('material_id', $id)
            ->delete();


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'success' => true,
            'message' => 'Ukuran material berhasil dihapus.'
        ]);
    }
}

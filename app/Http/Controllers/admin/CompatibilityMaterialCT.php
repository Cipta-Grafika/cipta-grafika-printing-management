<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompatibilityMaterialCT extends Controller
{
    public function index()
    {
        return view('admin.compatibility-material.index');
    }

    public function data(Request $request)
    {

        /*
    |--------------------------------------------------------------------------
    | TOTAL SEMUA DATA
    |--------------------------------------------------------------------------
    */

        $totalRecords = DB::table('m_engine_materials')
            ->count();


        /*
    |--------------------------------------------------------------------------
    | QUERY UTAMA
    |--------------------------------------------------------------------------
    */

        $query = DB::table('m_engine_materials')

            ->join(
                'm_engines',
                'm_engine_materials.engine_id',
                '=',
                'm_engines.id'
            )

            ->join(
                'm_materials',
                'm_engine_materials.material_id',
                '=',
                'm_materials.id'
            )

            ->leftJoin(
                'm_categories',
                'm_materials.category_id',
                '=',
                'm_categories.id'
            )

            ->select(

                'm_engine_materials.id',

                'm_engine_materials.engine_id',

                'm_engine_materials.material_id',

                'm_engine_materials.status',

                'm_engines.name as engine_name',

                'm_materials.material_code',

                'm_materials.material_name',

                'm_categories.name as category_name'

            );


        /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

        $search = $request->input('search.value');

        if (!empty($search)) {

            $query->where(function ($q) use ($search) {

                $q->whereRaw(
                    'LOWER(m_materials.material_name) LIKE ?',
                    ['%' . strtolower($search) . '%']
                )

                    ->orWhereRaw(
                        'LOWER(m_materials.material_code) LIKE ?',
                        ['%' . strtolower($search) . '%']
                    )

                    ->orWhereRaw(
                        'LOWER(m_engines.name) LIKE ?',
                        ['%' . strtolower($search) . '%']
                    )

                    ->orWhereRaw(
                        'LOWER(m_categories.name) LIKE ?',
                        ['%' . strtolower($search) . '%']
                    )

                    ->orWhereRaw(
                        'LOWER(m_engine_materials.status) LIKE ?',
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

            0 => 'm_engine_materials.id',

            1 => 'm_engine_materials.id',

            2 => 'm_materials.material_name',

            3 => 'm_engines.name',

            4 => 'm_categories.name',

            5 => 'm_engine_materials.status',

        ];


        $orderColumnIndex = (int) $request->input(
            'order.0.column',
            2
        );


        $orderColumn = $columns[$orderColumnIndex]
            ?? 'm_materials.material_name';


        $orderDirection = $request->input(
            'order.0.dir',
            'asc'
        );


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

        $start = (int) $request->input(
            'start',
            0
        );


        $length = (int) $request->input(
            'length',
            10
        );


        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA
    |--------------------------------------------------------------------------
    */

        $compatibilities = $query

            ->orderBy(
                $orderColumn,
                $orderDirection
            )

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

            'data' => $compatibilities,

        ]);
    }

    public function details($id)
    {
        /*
|--------------------------------------------------------------------------
| AMBIL DATA KOMPATIBILITAS
|--------------------------------------------------------------------------
*/

        $compatibility = DB::table('m_engine_materials')
            ->join(
                'm_engines',
                'm_engine_materials.engine_id',
                '=',
                'm_engines.id'
            )
            ->join(
                'm_materials',
                'm_engine_materials.material_id',
                '=',
                'm_materials.id'
            )
            ->join(
                'm_categories',
                'm_materials.category_id',
                '=',
                'm_categories.id'
            )
            ->select(
                'm_engine_materials.id',
                'm_engine_materials.status',
                'm_engines.name as engine_name',
                'm_materials.material_code',
                'm_materials.material_name',
                'm_categories.name as category_name'
            )
            ->where('m_engine_materials.id', $id)
            ->first();


        /*
|--------------------------------------------------------------------------
| JIKA DATA TIDAK DITEMUKAN
|--------------------------------------------------------------------------
*/

        if (!$compatibility) {

            return response()->json([

                'success' => false,

                'message' => 'Data kompatibilitas tidak ditemukan.'

            ], 404);
        }


        /*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

        return response()->json([

            'success' => true,

            'data' => $compatibility

        ]);
    }

    public function create()
    {
        $data['engines'] = DB::table('m_engines')
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        $data['materials'] = DB::table('m_materials')
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
                'm_materials.category_id',
                'm_categories.name as category_name'
            )
            ->where('m_materials.status', 'Active')
            ->orderBy('m_categories.name', 'asc')
            ->orderBy('m_materials.material_name', 'asc')
            ->get();

        return view('admin.compatibility-material.create')->with($data);
    }

    public function store(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

        $request->validate([
            'engine_id' => 'required|exists:m_engines,id',

            'materials' => 'required|array|min:1',

            'materials.*.material_id' => 'required|exists:m_materials,id',

            'materials.*.status' => 'required|in:Active,Inactive',
        ]);


        /*
    |--------------------------------------------------------------------------
    | CEK MATERIAL DUPLIKAT DI FORM
    |--------------------------------------------------------------------------
    */

        $materialIds = collect($request->materials)
            ->pluck('material_id');


        if ($materialIds->count() !== $materialIds->unique()->count()) {

            return back()
                ->withInput()
                ->withErrors([
                    'materials' => 'Terdapat material yang sama dalam daftar.'
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | CEK KOMPATIBILITAS SUDAH TERDAFTAR
    |--------------------------------------------------------------------------
    */

        $existingMaterialIds = DB::table('m_engine_materials')
            ->where('engine_id', $request->engine_id)
            ->whereIn('material_id', $materialIds)
            ->pluck('material_id');


        /*
    |--------------------------------------------------------------------------
    | JIKA SUDAH ADA
    |--------------------------------------------------------------------------
    */

        if ($existingMaterialIds->isNotEmpty()) {

            $materialNames = DB::table('m_materials')
                ->whereIn('id', $existingMaterialIds)
                ->pluck('material_name')
                ->implode(', ');


            return back()
                ->withInput()
                ->withErrors([
                    'materials' => 'Material berikut sudah terdaftar pada mesin ini: ' . $materialNames
                ]);
        }


        /*
    |--------------------------------------------------------------------------
    | SIMPAN DATA
    |--------------------------------------------------------------------------
    */

        DB::transaction(function () use ($request) {

            foreach ($request->materials as $material) {

                DB::table('m_engine_materials')
                    ->insert([

                        'engine_id' => $request->engine_id,

                        'material_id' => $material['material_id'],

                        'status' => $material['status'],

                        'created_by' => Auth::user()->name ?? 'System',

                        'created_at' => now(),

                        /*
                    |--------------------------------------------------------------------------
                    | UPDATED
                    |--------------------------------------------------------------------------
                    */

                        'updated_by' => Auth::user()->name ?? 'System',

                        'updated_at' => now(),

                    ]);
            }
        });


        /*
    |--------------------------------------------------------------------------
    | REDIRECT
    |--------------------------------------------------------------------------
    */

        return redirect()
            ->route('admin_compatibility_materials')
            ->with(
                'success',
                'Kompatibilitas mesin dan material berhasil disimpan.'
            );
    }

    public function edit($id)
    {
        /*
|--------------------------------------------------------------------------
| AMBIL DATA KOMPATIBILITAS
|--------------------------------------------------------------------------
*/

        $compatibility = DB::table('m_engine_materials')
            ->where('id', $id)
            ->first();


        /*
|--------------------------------------------------------------------------
| JIKA DATA TIDAK DITEMUKAN
|--------------------------------------------------------------------------
*/

        if (!$compatibility) {

            return redirect()
                ->route('admin_compatibility_materials')
                ->with('error', 'Data kompatibilitas tidak ditemukan.');
        }


        /*
|--------------------------------------------------------------------------
| AMBIL LIST MESIN
| TETAP TAMPILKAN MESIN YANG SEDANG DIPILIH
| WALAUPUN STATUSNYA INACTIVE
|--------------------------------------------------------------------------
*/

        $data['engines'] = DB::table('m_engines')
            ->where('status', 'Active')
            ->orWhere('id', $compatibility->engine_id)
            ->orderBy('name', 'asc')
            ->get();


        /*
|--------------------------------------------------------------------------
| AMBIL LIST MATERIAL
| TETAP TAMPILKAN MATERIAL YANG SEDANG DIPILIH
| WALAUPUN STATUSNYA INACTIVE
|--------------------------------------------------------------------------
*/

        $data['materials'] = DB::table('m_materials')
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
                'm_materials.category_id',
                'm_categories.name as category_name'
            )
            ->where('m_materials.status', 'Active')
            ->orWhere('m_materials.id', $compatibility->material_id)
            ->orderBy('m_categories.name', 'asc')
            ->orderBy('m_materials.material_name', 'asc')
            ->get();


        $data['compatibility'] = $compatibility;


        return view('admin.compatibility-material.edit')->with($data);
    }

    public function update(Request $request, $id)
    {
        /*
|--------------------------------------------------------------------------
| CEK DATA ADA ATAU TIDAK
|--------------------------------------------------------------------------
*/

        $compatibility = DB::table('m_engine_materials')
            ->where('id', $id)
            ->first();

        if (!$compatibility) {

            return redirect()
                ->route('admin_compatibility_materials')
                ->with('error', 'Data kompatibilitas tidak ditemukan.');
        }


        /*
|--------------------------------------------------------------------------
| VALIDASI
|--------------------------------------------------------------------------
*/

        $request->validate([
            'engine_id' => 'required|exists:m_engines,id',

            'material_id' => 'required|exists:m_materials,id',

            'status' => 'required|in:Active,Inactive',
        ]);


        /*
|--------------------------------------------------------------------------
| CEK DUPLIKASI
| (MESIN + MATERIAL YANG SAMA, KECUALI DATA INI SENDIRI)
|--------------------------------------------------------------------------
*/

        $exists = DB::table('m_engine_materials')
            ->where('engine_id', $request->engine_id)
            ->where('material_id', $request->material_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {

            return back()
                ->withInput()
                ->withErrors([
                    'material_id' => 'Kombinasi mesin dan material ini sudah terdaftar.'
                ]);
        }


        /*
|--------------------------------------------------------------------------
| UPDATE DATA
|--------------------------------------------------------------------------
*/

        DB::table('m_engine_materials')
            ->where('id', $id)
            ->update([

                'engine_id' => $request->engine_id,

                'material_id' => $request->material_id,

                'status' => $request->status,

                'updated_by' => Auth::user()->name ?? 'System',

                'updated_at' => now(),

            ]);


        /*
|--------------------------------------------------------------------------
| REDIRECT
|--------------------------------------------------------------------------
*/

        return redirect()
            ->route('admin_compatibility_materials')
            ->with('success', 'Kompatibilitas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        /*
|--------------------------------------------------------------------------
| CEK DATA KOMPATIBILITAS
|--------------------------------------------------------------------------
*/

        $compatibility = DB::table('m_engine_materials')
            ->where('id', $id)
            ->first();


        /*
|--------------------------------------------------------------------------
| JIKA DATA TIDAK DITEMUKAN
|--------------------------------------------------------------------------
*/

        if (!$compatibility) {

            return response()->json([

                'success' => false,

                'message' => 'Data kompatibilitas tidak ditemukan.'

            ], 404);
        }


        /*
|--------------------------------------------------------------------------
| HAPUS DATA
|--------------------------------------------------------------------------
*/

        DB::table('m_engine_materials')
            ->where('id', $id)
            ->delete();


        /*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

        return response()->json([

            'success' => true,

            'message' => 'Kompatibilitas berhasil dihapus.'

        ]);
    }
}

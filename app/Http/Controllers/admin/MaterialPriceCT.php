<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Imports\MaterialPricesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MaterialPricesExport;

class MaterialPriceCT extends Controller
{
    public function index()
    {
        return view('admin.material-price.index');
    }

    public function data(Request $request)
    {
        $query = DB::table('m_material_prices as mp')
            ->join('m_materials as m', 'm.id', '=', 'mp.material_id')
            ->select([
                'mp.id',
                'mp.material_id',
                'm.material_name',
                'mp.price_per_meter',
            ]);

        // =========================================================
        // TOTAL DATA
        // =========================================================

        $recordsTotal = DB::table('m_material_prices')
            ->count();

        // =========================================================
        // SEARCH
        // =========================================================

        $search = $request->input('search.value');

        if (!empty($search)) {
            $search = strtolower($search);

            $query->where(function ($q) use ($search) {
                $q->whereRaw(
                    'LOWER(m.material_name) LIKE ?',
                    ["%{$search}%"]
                );
            });
        }

        // =========================================================
        // TOTAL DATA SETELAH FILTER
        // =========================================================

        $recordsFiltered = $query->count();

        // =========================================================
        // ORDERING
        // =========================================================

        $query->orderBy('m.material_name', 'asc');

        // =========================================================
        // PAGINATION
        // =========================================================

        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        if ($length != -1) {
            $query->offset($start)
                ->limit($length);
        }

        // =========================================================
        // GET DATA
        // =========================================================

        $data = $query->get();

        // =========================================================
        // RESPONSE DATATABLE
        // =========================================================

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    public function create()
    {
        $data['materials'] = DB::table('m_materials')
            ->where('status', 'Active')
            ->orderBy('material_name')
            ->get();

        // dd($data['materials']);

        return  view('admin.material-price.create')->with($data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'material_prices' => 'required|array|min:1',
            'material_prices.*.material_id' => 'required|integer|exists:m_materials,id',
            'material_prices.*.price_per_meter' => 'required|string',
        ], [
            'material_prices.required' => 'Data harga material wajib diisi.',
            'material_prices.array' => 'Format data harga material tidak valid.',
            'material_prices.min' => 'Minimal satu harga material harus diisi.',
            'material_prices.*.material_id.required' => 'Material wajib dipilih.',
            'material_prices.*.material_id.exists' => 'Material yang dipilih tidak terdaftar.',
            'material_prices.*.price_per_meter.required' => 'Harga per meter wajib diisi.',
        ]);

        $materialPrices = $request->input('material_prices');

        $rows = [];
        $materialIds = [];

        foreach ($materialPrices as $item) {

            $materialId = (int) $item['material_id'];
            $price = trim((string) $item['price_per_meter']);

            // Cek material duplikat dalam form
            if (in_array($materialId, $materialIds, true)) {
                return back()
                    ->withInput()
                    ->with('error', 'Material yang sama tidak boleh ditambahkan lebih dari satu kali.');
            }

            $materialIds[] = $materialId;

            // Normalisasi harga: 6.000,00 → 6000.00
            $normalizedPrice = str_replace('.', '', $price);
            $normalizedPrice = str_replace(',', '.', $normalizedPrice);

            if (!is_numeric($normalizedPrice) || (float) $normalizedPrice < 0) {
                return back()
                    ->withInput()
                    ->with('error', 'Terdapat harga per meter yang tidak valid.');
            }

            $rows[] = [
                'material_id' => $materialId,
                'price_per_meter' => number_format(
                    (float) $normalizedPrice,
                    2,
                    '.',
                    ''
                ),
            ];
        }

        // Cek material aktif
        $inactiveMaterial = DB::table('m_materials')
            ->whereIn('id', $materialIds)
            ->where('status', 'Inactive')
            ->exists();

        if ($inactiveMaterial) {
            return back()
                ->withInput()
                ->with('error', 'Terdapat material yang sudah tidak aktif.');
        }

        // Cek material yang sudah memiliki harga
        $existingMaterialIds = DB::table('m_material_prices')
            ->whereIn('material_id', $materialIds)
            ->pluck('material_id')
            ->toArray();

        if (count($existingMaterialIds) > 0) {
            return back()
                ->withInput()
                ->with('error', 'Terdapat material yang sudah memiliki harga per meter.');
        }

        DB::transaction(function () use ($rows) {

            foreach ($rows as $row) {

                DB::table('m_material_prices')->insert([
                    'material_id' => $row['material_id'],
                    'price_per_meter' => $row['price_per_meter'],
                    'created_by' => Auth::user()->name ?? 'System',
                    'created_at' => Carbon::now(),
                ]);
            }
        });

        return redirect()
            ->route('admin_material_prices')
            ->with(
                'success',
                count($rows) . ' harga material berhasil disimpan.'
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

        if ($data['decodedId'] === false || !ctype_digit($data['decodedId'])) {
            abort(404, 'ID harga material tidak valid.');
        }

        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA HARGA MATERIAL
    |--------------------------------------------------------------------------
    */

        $data['materialPrice'] = DB::table('m_material_prices as mp')
            ->join('m_materials as m', 'm.id', '=', 'mp.material_id')
            ->select([
                'mp.id',
                'mp.material_id',
                'mp.price_per_meter',
                'm.material_name',
            ])
            ->where('mp.id', $data['decodedId'])
            ->first();

        /*
    |--------------------------------------------------------------------------
    | CEK DATA
    |--------------------------------------------------------------------------
    */

        if (!$data['materialPrice']) {
            abort(404, 'Data harga material tidak ditemukan.');
        }

        /*
    |--------------------------------------------------------------------------
    | AMBIL MASTER MATERIAL
    |--------------------------------------------------------------------------
    */

        $data['materials'] = DB::table('m_materials')
            ->where('status', 'Active')
            ->orderBy('material_name')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | ENCODE ID UNTUK FORM UPDATE
    |--------------------------------------------------------------------------
    */

        $data['encodedId'] = $id;

        /*
    |--------------------------------------------------------------------------
    | VIEW
    |--------------------------------------------------------------------------
    */

        return view('admin.material-price.edit')->with($data);
    }

    public function destroy($id)
    {
        /*
    |--------------------------------------------------------------------------
    | CEK DATA HARGA MATERIAL
    |--------------------------------------------------------------------------
    */

        $materialPrice = DB::table('m_material_prices')
            ->where('id', $id)
            ->first();

        if (!$materialPrice) {
            return response()->json([
                'success' => false,
                'message' => 'Data harga material tidak ditemukan.'
            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | HAPUS HARGA MATERIAL
    |--------------------------------------------------------------------------
    */

        DB::table('m_material_prices')
            ->where('id', $id)
            ->delete();


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'success' => true,
            'message' => 'Harga material berhasil dihapus.'
        ]);
    }

    public function update(Request $request, $id)
    {
        // Decode ID dari URL
        $decodedId = base64_decode($id, true);

        if ($decodedId === false || !ctype_digit($decodedId)) {
            abort(404, 'ID harga material tidak valid.');
        }

        $decodedId = (int) $decodedId;

        // Pastikan data harga material ada
        $materialPrice = DB::table('m_material_prices')
            ->where('id', $decodedId)
            ->first();

        if (!$materialPrice) {
            abort(404, 'Data harga material tidak ditemukan.');
        }

        // Validasi input
        $request->validate([
            'material_id' => 'required|integer|exists:m_materials,id',
            'price_per_meter' => 'required|string',
        ], [
            'material_id.required' => 'Material wajib dipilih.',
            'material_id.integer' => 'Material yang dipilih tidak valid.',
            'material_id.exists' => 'Material yang dipilih tidak terdaftar.',
            'price_per_meter.required' => 'Harga per meter wajib diisi.',
        ]);

        $materialId = (int) $request->material_id;
        $price = trim((string) $request->price_per_meter);

        // Normalisasi format harga Indonesia
        // Contoh:
        // 6.000,00 -> 6000.00
        // 6.500 -> 6500.00
        $normalizedPrice = str_replace('.', '', $price);
        $normalizedPrice = str_replace(',', '.', $normalizedPrice);

        // Validasi harga
        if (!is_numeric($normalizedPrice) || (float) $normalizedPrice < 0) {
            return back()
                ->withInput()
                ->with('error', 'Harga per meter tidak valid.');
        }

        $normalizedPrice = (float) $normalizedPrice;

        // Pastikan material masih aktif
        $materialActive = DB::table('m_materials')
            ->where('id', $materialId)
            ->where('status', 'Active')
            ->exists();

        if (!$materialActive) {
            return back()
                ->withInput()
                ->with('error', 'Material yang dipilih sudah tidak aktif.');
        }

        // Validasi duplikasi material
        // Material yang sama dengan data lain tidak diperbolehkan.
        // Data yang sedang diedit dikecualikan.
        $duplicateMaterial = DB::table('m_material_prices')
            ->where('material_id', $materialId)
            ->where('id', '!=', $decodedId)
            ->exists();

        if ($duplicateMaterial) {
            return back()
                ->withInput()
                ->with('error', 'Material yang dipilih sudah memiliki harga per meter.');
        }

        // Update data
        DB::table('m_material_prices')
            ->where('id', $decodedId)
            ->update([
                'material_id' => $materialId,
                'price_per_meter' => number_format(
                    $normalizedPrice,
                    2,
                    '.',
                    ''
                ),
                'updated_by' => Auth::user()->name ?? 'System',
                'updated_at' => Carbon::now(),
            ]);

        // Redirect + toast success
        return redirect()
            ->route('admin_material_prices')
            ->with('success', 'Harga material berhasil diperbarui.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ], [
            'file.required' => 'Silakan pilih file Excel terlebih dahulu.',
            'file.file' => 'File yang dipilih tidak valid.',
            'file.mimes' => 'File harus berformat Excel (.xlsx atau .xls).',
        ]);

        $import = new MaterialPricesImport();

        Excel::import(
            $import,
            $request->file('file')
        );

        /*
    |--------------------------------------------------------------------------
    | JIKA ADA ERROR
    |--------------------------------------------------------------------------
    */

        if (count($import->errors) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Import dibatalkan. Terdapat data yang tidak valid.',
                'errors' => $import->errors,
                'imported' => 0,
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | TIDAK ADA DATA
    |--------------------------------------------------------------------------
    */

        if ($import->imported === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada data harga material yang valid untuk diimport.',
                'imported' => 0,
            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | BERHASIL
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'success' => true,
            'message' => $import->imported . ' data harga material berhasil diimport.',
            'imported' => $import->imported,
        ]);
    }

    public function export()
    {
        return Excel::download(
            new MaterialPricesExport(),
            'master_harga_material.xlsx'
        );
    }
}

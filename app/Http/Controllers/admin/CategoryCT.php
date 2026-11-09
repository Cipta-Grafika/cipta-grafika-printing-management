<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exports\CategoriesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CategoriesImport;

class CategoryCT extends Controller
{
    public function index()
    {
        $creators = DB::table('m_categories')
            ->whereNotNull('created_by')
            ->where('created_by', '!=', '')
            ->distinct()
            ->pluck('created_by')
            ->filter()
            ->values();

        return view('admin.category.index', compact('creators'));
    }

    public function data(Request $request)
    {
        /*
    |--------------------------------------------------------------------------
    | TOTAL SEMUA DATA
    |--------------------------------------------------------------------------
    */

        $totalRecords = DB::table('m_categories')->count();


        /*
    |--------------------------------------------------------------------------
    | QUERY UTAMA
    |--------------------------------------------------------------------------
    */

        $query = DB::table('m_categories');


        /*
    |--------------------------------------------------------------------------
    | FILTERS (Dibuat Oleh & Periode)
    |--------------------------------------------------------------------------
    */
        $creator = $request->input('creator');
        if (!empty($creator) && $creator !== 'all') {
            $query->where('created_by', $creator);
        }

        $period = $request->input('period');
        if (!empty($period) && $period !== 'all') {
            if ($period === 'today') {
                $query->whereDate('created_at', Carbon::today());
            } elseif ($period === '7_days') {
                $query->where('created_at', '>=', Carbon::now()->subDays(7));
            } elseif ($period === 'month') {
                $query->where('created_at', '>=', Carbon::now()->startOfMonth());
            } elseif ($period === 'year') {
                $query->where('created_at', '>=', Carbon::now()->startOfYear());
            }
        }


        /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

        $search = $request->input('search.value');

        if (!empty($search)) {
            $keyword = '%' . strtolower(trim($search)) . '%';
            $query->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(name) LIKE ?', [$keyword])
                    ->orWhereRaw('LOWER(COALESCE(created_by, \'\')) LIKE ?', [$keyword]);
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
            3 => 'created_by',
            4 => 'created_at',
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

        $start = (int) $request->input('start', 0);

        $length = (int) $request->input('length', 10);


        /*
    |--------------------------------------------------------------------------
    | AMBIL DATA
    |--------------------------------------------------------------------------
    */

        $categories = $query
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
            'data' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'categories' => ['required', 'array'],
            'categories.*' => ['required', 'string', 'max:255'],
        ]);

        $categories = collect($request->categories)
            ->map(function ($category) {
                return trim($category);
            });

        /*
    |--------------------------------------------------------------------------
    | CEK DUPLIKAT DI DALAM INPUT
    |--------------------------------------------------------------------------
    */

        $duplicates = $categories
            ->map(function ($category) {
                return strtolower($category);
            })
            ->duplicates();

        if ($duplicates->isNotEmpty()) {

            return back()
                ->withInput()
                ->with('error', 'Terdapat nama kategori yang duplikat dalam input.');
        }

        /*
    |--------------------------------------------------------------------------
    | CEK DUPLIKAT DI DATABASE
    |--------------------------------------------------------------------------
    */

        foreach ($categories as $category) {

            $exists = DB::table('m_categories')
                ->whereRaw('LOWER(name) = ?', [strtolower($category)])
                ->exists();

            if ($exists) {

                return back()
                    ->withInput()
                    ->with('error', "Kategori '{$category}' sudah terdaftar.");
            }
        }

        /*
    |--------------------------------------------------------------------------
    | INSERT DATA
    |--------------------------------------------------------------------------
    */

        foreach ($categories as $category) {

            DB::table('m_categories')->insert([
                'name' => $category,
                'created_by' => Auth::user()->name,
                'created_at' => Carbon::now()
            ]);
        }

        return redirect()
            ->route('admin_categories')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, int $id)
    {
        /*
    |--------------------------------------------------------------------------
    | VALIDASI
    |--------------------------------------------------------------------------
    */

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);


        /*
    |--------------------------------------------------------------------------
    | BERSIHKAN NAMA KATEGORI
    |--------------------------------------------------------------------------
    */

        $categoryName = trim($request->input('name'));


        /*
    |--------------------------------------------------------------------------
    | CEK KATEGORI
    |--------------------------------------------------------------------------
    */

        $category = DB::table('m_categories')
            ->where('id', $id)
            ->first();


        if (!$category) {

            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan.'
            ], 404);
        }


        /*
    |--------------------------------------------------------------------------
    | CEK DUPLIKAT
    |--------------------------------------------------------------------------
    |
    | Nama kategori tidak boleh sama dengan kategori lain.
    | Tetapi kategori yang sedang diedit tidak dihitung.
    |
    */

        $exists = DB::table('m_categories')
            ->whereRaw('LOWER(name) = ?', [
                strtolower($categoryName)
            ])
            ->where('id', '!=', $id)
            ->exists();


        if ($exists) {

            return response()->json([
                'success' => false,
                'message' => 'Nama kategori sudah terdaftar.'
            ], 422);
        }


        /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

        DB::table('m_categories')
            ->where('id', $id)
            ->update([
                'name' => $categoryName,
                'updated_at' => now(),
            ]);


        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui.'
        ]);
    }

    public function destroy(int $id)
    {
        $category = DB::table('m_categories')
            ->where('id', $id)
            ->first();

        if (!$category) {

            return response()->json([
                'success' => false,
                'message' => 'Kategori tidak ditemukan.'
            ], 404);
        }

        // Cek foreign key constraint m_discount_scopes
        $isUsedInDiscount = DB::table('m_discount_scopes')
            ->where('category_id', $id)
            ->exists();

        if ($isUsedInDiscount) {
            return response()->json([
                'success' => false,
                'message' => "Kategori '{$category->name}' tidak dapat dihapus karena masih digunakan dalam konfigurasi diskon."
            ], 422);
        }

        try {
            DB::table('m_categories')
                ->where('id', $id)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Kategori berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus kategori: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids = array_map('intval', $request->input('ids', []));

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada kategori yang dipilih.'
            ], 400);
        }

        $usedCategories = DB::table('m_discount_scopes')
            ->join('m_categories', 'm_discount_scopes.category_id', '=', 'm_categories.id')
            ->whereIn('m_discount_scopes.category_id', $ids)
            ->distinct()
            ->pluck('m_categories.name')
            ->toArray();

        if (!empty($usedCategories)) {
            $categoryList = implode(', ', array_slice($usedCategories, 0, 3));
            if (count($usedCategories) > 3) {
                $categoryList .= ', dan lainnya';
            }

            return response()->json([
                'success' => false,
                'message' => "Kategori ({$categoryList}) tidak dapat dihapus karena masih digunakan dalam konfigurasi diskon."
            ], 422);
        }

        try {
            $deletedCount = DB::transaction(function () use ($ids) {
                return DB::table('m_categories')
                    ->whereIn('id', $ids)
                    ->delete();
            });

            return response()->json([
                'success' => true,
                'message' => "{$deletedCount} kategori berhasil dihapus."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export()
    {
        $fileName = 'kategori_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new CategoriesExport(),
            $fileName
        );
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        Excel::import(
            new CategoriesImport(),
            $request->file('file')
        );

        return response()->json([
            'success' => true,
            'message' => 'Data kategori berhasil diimport.'
        ]);
    }
}

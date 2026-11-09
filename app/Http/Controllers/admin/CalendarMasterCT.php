<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalendarMasterCT extends Controller
{
    private const INDEX_TABLES = [
        'product' => [
            'table' => 'm_calendar_products',
            'label' => 'produk kalender',
            'columns' => ['calendar_type', 'product_name', ['final_width', 'final_height'], 'orientation', 'digital_up', 'offset_machine', ['offset_up', 'offset_up_bw'], 'recommended_plano', 'default_leaf_count', 'status'],
            'search' => ['calendar_type', 'product_name', 'size_label', 'orientation', 'offset_machine', 'recommended_plano', 'status'],
            'order' => 'product_name',
        ],
        'material' => [
            'table' => 'm_calendar_materials',
            'label' => 'bahan kertas',
            'columns' => ['material_name', 'category', 'digital_cost_print_1_side', 'digital_cost_print_2_side', 'digital_paper_cost', 'offset_price_65x90', 'offset_price_65x100', 'offset_price_109x79', 'status'],
            'search' => ['material_code', 'material_name', 'category', 'status'],
            'order' => 'material_name',
        ],
        'component' => [
            'table' => 'm_calendar_components',
            'label' => 'komponen finishing',
            'columns' => ['component_code', 'name', 'component_type', 'unit', 'price', 'minimum_fee', 'description', 'status'],
            'search' => ['component_code', 'name', 'component_type', 'unit', 'description', 'status'],
            'order' => 'name',
        ],
        'rule' => [
            'table' => 'm_calendar_pricing_rules',
            'label' => 'pricing rule',
            'columns' => ['rule_name', 'min_qty', 'max_qty', 'markup_percent', 'rounding_value', 'status'],
            'search' => ['rule_name', 'label', 'status'],
            'order' => 'min_qty',
        ],
    ];

    public function data(Request $request, string $type)
    {
        abort_unless(isset(self::INDEX_TABLES[$type]), 404);
        $config = self::INDEX_TABLES[$type];
        $query = DB::table($config['table']);
        $total = (clone $query)->count();

        $search = trim((string) $request->input('search.value', ''));
        if ($search !== '') {
            $query->where(function ($query) use ($config, $search) {
                $columns = array_unique(array_merge($config['search'], array_merge(...array_map(
                    fn ($column) => (array) $column, $config['columns']
                ))));
                foreach ($columns as $column) {
                    $query->orWhereRaw("LOWER(CAST($column AS TEXT)) LIKE ?", ['%' . mb_strtolower($search) . '%']);
                }
            });
        }
        $status = strtolower((string) $request->input('status', ''));
        if (in_array($status, ['active', 'inactive'], true)) {
            $query->whereRaw('LOWER(status) = ?', [$status]);
        }
        switch ($request->input('period')) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case '7_days':
                $query->where('created_at', '>=', Carbon::now()->subDays(7)->startOfDay());
                break;
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
                break;
            case 'year':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
        }
        $filtered = (clone $query)->count();
        $column = (int) $request->input('order.0.column', -1);
        $direction = strtolower((string) $request->input('order.0.dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $sort = $config['columns'][$column - 2] ?? $config['order'];
        foreach ((array) $sort as $field) {
            $query->orderBy($field, $direction);
        }
        $rows = $query->orderBy('id')->offset(max(0, (int) $request->input('start', 0)))
            ->limit(max(1, min(100, (int) $request->input('length', 10))))->get();

        return response()->json([
            'draw' => (int) $request->input('draw', 0),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $rows,
        ]);
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|in:' . implode(',', array_keys(self::INDEX_TABLES)),
            'ids' => 'required|array|min:1|max:100',
            'ids.*' => 'required|integer|min:1|distinct',
        ]);
        $config = self::INDEX_TABLES[$validated['type']];

        try {
            return DB::transaction(function () use ($validated, $config) {
                $ids = DB::table($config['table'])->whereIn('id', $validated['ids'])->lockForUpdate()->pluck('id');
                if ($ids->count() !== count($validated['ids'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sebagian data ' . $config['label'] . ' tidak ditemukan. Tidak ada data yang dihapus. Muat ulang tabel dan coba lagi.',
                    ], 422);
                }
                $deleted = DB::table($config['table'])->whereIn('id', $ids)->delete();
                return response()->json([
                    'success' => true,
                    'deleted' => $deleted,
                    'message' => $deleted . ' ' . $config['label'] . ' berhasil dihapus.',
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus massal master kalender', ['type' => $validated['type'], 'exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data kalender. Tidak ada data yang dihapus.'], 500);
        }
    }

    /**
     * Tampilkan halaman index Master Kalender (4 Tab: Produk, Bahan, Komponen, Rules)
     */
    public function index()
    {
        $data['totalProducts'] = DB::table('m_calendar_products')->count();
        $data['totalMaterials'] = DB::table('m_calendar_materials')->count();
        $data['totalComponents'] = DB::table('m_calendar_components')->count();
        $data['totalRules'] = DB::table('m_calendar_pricing_rules')->count();

        return view('admin.calendar.index', $data);
    }

    /**
     * Simpan Produk Ukuran Kalender Baru
     */
    public function storeProduct(Request $request)
    {
        try {
            $width = (float) $request->input('final_width');
            $height = (float) $request->input('final_height');
            $machine = (string) $request->input('offset_machine', 'SM52');

            $pressW = $machine === 'SM72' ? 72.0 : ($machine === 'Oliver66' ? 66.0 : 52.0);
            $pressH = $machine === 'SM72' ? 52.0 : ($machine === 'Oliver66' ? 47.0 : 36.0);

            $id = DB::table('m_calendar_products')->insertGetId([
                'calendar_type'       => (string) $request->input('calendar_type', 'dinding'),
                'product_name'        => (string) $request->input('product_name'),
                'size_label'          => $width . ' x ' . $height . ' cm',
                'final_width'         => $width,
                'final_height'        => $height,
                'orientation'         => (string) $request->input('orientation', 'portrait'),
                'digital_available'   => (bool) (int) $request->input('digital_available', 1),
                'digital_up'          => (int) $request->input('digital_up', 1),
                'offset_machine'      => $machine,
                'offset_press_width'  => $pressW,
                'offset_press_height' => $pressH,
                'offset_up'           => (int) $request->input('offset_up', 1),
                'offset_up_bw'        => $request->filled('offset_up_bw') ? (int) $request->input('offset_up_bw') : null,
                'recommended_plano'   => (string) $request->input('recommended_plano', '65x90'),
                'default_leaf_count'  => (int) $request->input('default_leaf_count', 7),
                'status'              => 'active',
                'created_by'          => Auth::user()?->name ?? 'Admin',
                'created_at'          => Carbon::now(),
                'updated_by'          => Auth::user()?->name ?? 'Admin',
                'updated_at'          => Carbon::now(),
            ]);

            return response()->json(['success' => true, 'id' => $id, 'message' => 'Produk ukuran kalender berhasil ditambahkan!']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan produk: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Simpan Bahan Kertas Kalender Baru
     */
    public function storeMaterial(Request $request)
    {
        try {
            $name = (string) $request->input('material_name');
            $code = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 10));

            $id = DB::table('m_calendar_materials')->insertGetId([
                'material_code'             => $code ?: ('MAT-' . time()),
                'material_name'             => $name,
                'category'                  => (string) $request->input('category', 'isi'),
                'digital_cost_print_1_side' => (float) $request->input('digital_cost_print_1_side', 0),
                'digital_cost_print_2_side' => (float) $request->input('digital_cost_print_2_side', 0),
                'digital_paper_cost'        => (float) $request->input('digital_paper_cost', 0),
                'offset_price_65x90'        => $request->filled('offset_price_65x90') ? (float) $request->input('offset_price_65x90') : null,
                'offset_price_65x100'       => $request->filled('offset_price_65x100') ? (float) $request->input('offset_price_65x100') : null,
                'offset_price_109x79'       => $request->filled('offset_price_109x79') ? (float) $request->input('offset_price_109x79') : null,
                'status'                    => 'active',
                'created_by'                => Auth::user()?->name ?? 'Admin',
                'created_at'                => Carbon::now(),
                'updated_by'                => Auth::user()?->name ?? 'Admin',
                'updated_at'                => Carbon::now(),
            ]);

            return response()->json(['success' => true, 'id' => $id, 'message' => 'Bahan kertas kalender berhasil ditambahkan!']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan bahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Simpan Komponen / Finishing Kalender Baru
     */
    public function storeComponent(Request $request)
    {
        try {
            $code = (string) $request->input('component_code');
            if (empty($code)) {
                $code = 'COMP-' . strtoupper(substr(uniqid(), -6));
            }

            $id = DB::table('m_calendar_components')->insertGetId([
                'component_code' => $code,
                'component_type' => (string) $request->input('component_type', 'finishing_jilid'),
                'name'           => (string) $request->input('name'),
                'unit'           => (string) $request->input('unit', 'pcs'),
                'price'          => (float) $request->input('price', 0),
                'minimum_fee'    => (float) $request->input('minimum_fee', 0),
                'description'    => $request->input('description'),
                'status'         => 'active',
                'created_by'     => Auth::user()?->name ?? 'Admin',
                'created_at'     => Carbon::now(),
                'updated_by'     => Auth::user()?->name ?? 'Admin',
                'updated_at'     => Carbon::now(),
            ]);

            return response()->json(['success' => true, 'id' => $id, 'message' => 'Komponen finishing berhasil ditambahkan!']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan komponen: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Simpan Pricing Rule Kalender Baru
     */
    public function storeRule(Request $request)
    {
        try {
            $markup = (float) $request->input('markup_percent', 0);

            $id = DB::table('m_calendar_pricing_rules')->insertGetId([
                'rule_name'      => (string) $request->input('rule_name', 'Tier Custom'),
                'min_qty'        => (int) $request->input('min_qty', 1),
                'max_qty'        => $request->filled('max_qty') ? (int) $request->input('max_qty') : null,
                'markup_percent' => $markup,
                'label'          => $markup . '%',
                'rounding_value' => (int) $request->input('rounding_value', 500),
                'status'         => 'active',
                'created_by'     => Auth::user()?->name ?? 'Admin',
                'created_at'     => Carbon::now(),
                'updated_by'     => Auth::user()?->name ?? 'Admin',
                'updated_at'     => Carbon::now(),
            ]);

            return response()->json(['success' => true, 'id' => $id, 'message' => 'Aturan pricing markup berhasil ditambahkan!']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menambahkan aturan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update Bahan Kertas Kalender
     */
    public function updateMaterial(Request $request, $id)
    {
        try {
            $updateData = [
                'digital_cost_print_1_side' => (float) $request->input('digital_cost_print_1_side', 0),
                'digital_cost_print_2_side' => (float) $request->input('digital_cost_print_2_side', 0),
                'digital_paper_cost'        => (float) $request->input('digital_paper_cost', 0),
                'offset_price_65x90'        => $request->filled('offset_price_65x90') ? (float) $request->input('offset_price_65x90') : null,
                'offset_price_65x100'       => $request->filled('offset_price_65x100') ? (float) $request->input('offset_price_65x100') : null,
                'offset_price_109x79'       => $request->filled('offset_price_109x79') ? (float) $request->input('offset_price_109x79') : null,
                'updated_at'                => Carbon::now(),
                'updated_by'                => Auth::user()?->name ?? 'Admin',
            ];

            if ($request->filled('material_name')) {
                $updateData['material_name'] = (string) $request->input('material_name');
            }
            if ($request->filled('category')) {
                $updateData['category'] = (string) $request->input('category');
            }

            DB::table('m_calendar_materials')->where('id', $id)->update($updateData);

            return response()->json(['success' => true, 'message' => 'Tarif bahan kertas berhasil diperbarui!']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update Komponen / Aksesoris Jilid Kalender
     */
    public function updateComponent(Request $request, $id)
    {
        try {
            $updateData = [
                'price'       => (float) $request->input('price', 0),
                'minimum_fee' => (float) $request->input('minimum_fee', 0),
                'updated_at'  => Carbon::now(),
                'updated_by'  => Auth::user()?->name ?? 'Admin',
            ];

            if ($request->filled('name')) {
                $updateData['name'] = (string) $request->input('name');
            }
            if ($request->has('description')) {
                $updateData['description'] = $request->input('description');
            }

            DB::table('m_calendar_components')->where('id', $id)->update($updateData);

            return response()->json(['success' => true, 'message' => 'Tarif komponen berhasil diperbarui!']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update Pricing Rule Markup Kalender
     */
    public function updateRule(Request $request, $id)
    {
        try {
            $updateData = [
                'markup_percent' => (float) $request->input('markup_percent', 0),
                'rounding_value' => (int) $request->input('rounding_value', 500),
                'updated_at'     => Carbon::now(),
                'updated_by'     => Auth::user()?->name ?? 'Admin',
            ];

            if ($request->filled('min_qty')) {
                $updateData['min_qty'] = (int) $request->input('min_qty');
            }
            if ($request->has('max_qty')) {
                $updateData['max_qty'] = $request->filled('max_qty') ? (int) $request->input('max_qty') : null;
            }

            DB::table('m_calendar_pricing_rules')->where('id', $id)->update($updateData);

            return response()->json(['success' => true, 'message' => 'Pricing rule markup berhasil diperbarui!']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update Produk Ukuran Kalender
     */
    public function updateProduct(Request $request, $id)
    {
        try {
            $updateData = [
                'calendar_type'       => (string) $request->input('calendar_type', 'dinding'),
                'product_name'        => (string) $request->input('product_name'),
                'final_width'         => (float) $request->input('final_width'),
                'final_height'        => (float) $request->input('final_height'),
                'orientation'         => (string) $request->input('orientation', 'portrait'),
                'digital_available'   => (bool) (int) $request->input('digital_available', 1),
                'digital_up'          => (int) $request->input('digital_up', 0),
                'offset_machine'      => (string) $request->input('offset_machine', 'SM52'),
                'offset_up'           => (int) $request->input('offset_up', 1),
                'offset_up_bw'        => $request->filled('offset_up_bw') ? (int) $request->input('offset_up_bw') : null,
                'recommended_plano'   => (string) $request->input('recommended_plano', '65x90'),
                'default_leaf_count'  => (int) $request->input('default_leaf_count', 7),
                'updated_at'          => Carbon::now(),
                'updated_by'          => Auth::user()?->name ?? 'Admin',
            ];

            DB::table('m_calendar_products')->where('id', $id)->update($updateData);

            return response()->json(['success' => true, 'message' => 'Parameter produk ukuran kalender berhasil diperbarui!']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete Produk Ukuran Kalender
     */
    public function destroyProduct($id)
    {
        try {
            DB::table('m_calendar_products')->where('id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Produk ukuran kalender berhasil dihapus!']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus produk: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete Bahan Kertas Kalender
     */
    public function destroyMaterial($id)
    {
        try {
            DB::table('m_calendar_materials')->where('id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Bahan kertas kalender berhasil dihapus!']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus bahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete Komponen / Finishing Kalender
     */
    public function destroyComponent($id)
    {
        try {
            DB::table('m_calendar_components')->where('id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Komponen finishing kalender berhasil dihapus!']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus komponen: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete Pricing Rule Kalender
     */
    public function destroyRule($id)
    {
        try {
            DB::table('m_calendar_pricing_rules')->where('id', $id)->delete();
            return response()->json(['success' => true, 'message' => 'Aturan markup margin kalender berhasil dihapus!']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus aturan: ' . $e->getMessage()], 500);
        }
    }
}

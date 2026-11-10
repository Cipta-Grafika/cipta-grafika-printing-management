<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MerchandiseMasterCT extends Controller
{
    private const INDEX_TABLES = [
        'lanyard' => [
            'table' => 'm_lanyard_pricings', 'label' => 'tier lanyard',
            'columns' => ['width_type', 'package_type', 'tier_label', 'min_qty', 'max_qty', 'price', 'status'],
            'search' => ['width_type', 'package_type', 'tier_label', 'min_qty', 'max_qty', 'price', 'status'],
            'order' => ['width_type', 'package_type', 'min_qty'],
            'filters' => ['width_2cm' => ['width_type', '2cm'], 'width_25cm' => ['width_type', '2.5cm'], 'tali' => ['package_type', 'tali'], 'set' => ['package_type', 'set']],
            'aliases' => ['width_type' => ['2cm' => '2.0 cm', '2.5cm' => '2.5 cm'], 'package_type' => ['tali' => 'Hanya Tali', 'set' => '1 Set (Holder + PVC)']],
        ],
        'nametag' => [
            'table' => 'm_name_tag_pricings', 'label' => 'papan nama',
            'columns' => ['name', 'type_code', 'fastener_options', 'min_order_qty', 'price', 'status'],
            'search' => ['name', 'type_code', 'fastener_options', 'min_order_qty', 'price', 'description', 'status'],
            'order' => ['name'],
            'filters' => ['biasa' => ['type_code', 'biasa'], 'resin' => ['type_code', 'resin'], 'resin_emas' => ['type_code', 'resin_emas']],
            'aliases' => ['type_code' => ['biasa' => 'Akrilik Biasa', 'resin' => 'Resin Cembung', 'resin_emas' => 'Resin Emas']],
        ],
        'plakat' => [
            'table' => 'm_plakat_pricings', 'label' => 'plakat',
            'columns' => ['name', 'material_type', 'thickness', 'base_type', 'box_type', 'dimensions', 'price', 'status'],
            'search' => ['name', 'material_type', 'thickness', 'base_type', 'box_type', 'dimensions', 'price', 'description', 'status'],
            'order' => ['material_type', 'thickness', 'base_type', 'box_type'],
            'filters' => ['tanpa_box' => ['box_type', 'tanpa_box'], 'box' => ['box_type', 'box'], 'kayu' => ['material_type', 'kayu']],
            'aliases' => ['material_type' => ['akrilik' => 'Akrilik UV', 'kayu' => 'Kayu Solid'], 'box_type' => ['tanpa_box' => 'Tanpa Box', 'box' => 'Box Beludru']],
        ],
        'nota' => [
            'table' => 'm_nota_pricings', 'label' => 'buku nota',
            'columns' => ['name', 'paper_type', 'ply_count', 'ink_count', 'sheets_per_book', 'price', 'status'],
            'search' => ['name', 'paper_type', 'ply_count', 'ply_label', 'ink_count', 'ink_label', 'sheets_per_book', 'price', 'description', 'status'],
            'order' => ['paper_type', 'ply_count', 'ink_count'],
            'filters' => ['ncr' => ['paper_type', 'ncr'], 'hvs' => ['paper_type', 'hvs'], 'ink_1' => ['ink_count', 1], 'ink_2' => ['ink_count', 2], 'ink_3' => ['ink_count', 3]],
            'aliases' => ['paper_type' => ['ncr' => 'NCR Tembus', 'hvs' => 'HVS Putih']],
        ],
        'mug' => [
            'table' => 'm_mug_pricings', 'label' => 'tier mug',
            'columns' => ['tier_label', 'min_qty', 'max_qty', 'price', 'status'],
            'search' => ['name', 'tier_label', 'min_qty', 'max_qty', 'price', 'description', 'status'],
            'order' => ['min_qty'],
            'filters' => [],
            'aliases' => [],
        ],
        'pvccard' => [
            'table' => 'm_pvc_card_pricings', 'label' => 'tier kartu pvc',
            'columns' => ['tier_label', 'min_qty', 'max_qty', 'price', 'status'],
            'search' => ['name', 'tier_label', 'min_qty', 'max_qty', 'price', 'description', 'status'],
            'order' => ['min_qty'],
            'filters' => [],
            'aliases' => [],
        ],
        'akrilikuv' => [
            'table' => 'm_akrilik_uv_pricings', 'label' => 'akrilik uv',
            'columns' => ['name', 'spec_label', 'thickness', 'min_order_qty', 'price', 'status'],
            'search' => ['name', 'spec_label', 'thickness', 'min_order_qty', 'price', 'description', 'status'],
            'order' => ['name'],
            'filters' => [],
            'aliases' => [],
        ],
    ];

    public function data(Request $request, string $type)
    {
        abort_unless(isset(self::INDEX_TABLES[$type]), 404);
        $config = self::INDEX_TABLES[$type];
        $query = DB::table($config['table']);
        $total = (clone $query)->count();
        $search = mb_strtolower(trim((string) $request->input('search.value', '')));
        if ($search !== '') {
            $query->where(function ($query) use ($config, $search) {
                foreach ($config['search'] as $column) {
                    $query->orWhereRaw("LOWER(CAST($column AS TEXT)) LIKE ?", ['%' . $search . '%']);
                }
                foreach ($config['aliases'] as $column => $aliases) {
                    foreach ($aliases as $value => $label) {
                        if (str_contains(mb_strtolower($label), $search)) {
                            $query->orWhere($column, $value);
                        }
                    }
                }
            });
        }
        $status = strtolower((string) $request->input('status', ''));
        if (in_array($status, ['active', 'inactive'], true)) {
            $query->whereRaw('LOWER(status) = ?', [$status]);
        }
        $variant = (string) $request->input('variant', '');
        if (isset($config['filters'][$variant])) {
            [$column, $value] = $config['filters'][$variant];
            $query->where($column, $value);
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
            'recordsTotal' => $total, 'recordsFiltered' => $filtered, 'data' => $rows,
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
                    'success' => true, 'deleted' => $deleted,
                    'message' => $deleted . ' ' . $config['label'] . ' berhasil dihapus.',
                ]);
            });
        } catch (\Throwable $e) {
            Log::error('Gagal menghapus massal master merchandise', ['type' => $validated['type'], 'exception' => $e]);
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data merchandise. Tidak ada data yang dihapus.'], 500);
        }
    }

    /**
     * Tampilkan halaman Master Data Merchandise (Lanyard, Papan Nama, Plakat)
     */
    public function index()
    {
        $data['totalLanyardTiers'] = DB::table('m_lanyard_pricings')->count();

        // Ambil info produk display lanyard jika ada
        $data['displayLanyard'] = DB::table('m_display_products')
            ->whereRaw('LOWER(display_name) LIKE ?', ['%lanyard%'])
            ->first();

        // Ambil data Papan Nama (Name Tag Dada)
        $data['totalNameTags'] = DB::table('m_name_tag_pricings')->count();

        // Ambil info produk display papan nama jika ada
        $data['displayNameTag'] = DB::table('m_display_products')
            ->whereRaw('LOWER(display_name) LIKE ?', ['%papan nama%'])
            ->first();

        // Ambil data Plakat Akrilik & Kayu
        $data['totalPlakats'] = DB::table('m_plakat_pricings')->count();

        // Ambil info produk display plakat jika ada
        $data['displayPlakat'] = DB::table('m_display_products')
            ->whereRaw('LOWER(display_name) LIKE ?', ['%plakat%'])
            ->first();

        // Ambil data Buku Nota (NCR & HVS)
        $data['totalNotas'] = DB::table('m_nota_pricings')->count();

        // Ambil info produk display nota jika ada
        $data['displayNota'] = DB::table('m_display_products')
            ->whereRaw('LOWER(display_name) LIKE ?', ['%nota%'])
            ->first();

        // Ambil data Mug Sublimasi
        $data['totalMugs'] = DB::table('m_mug_pricings')->count();
        $data['displayMug'] = DB::table('m_display_products')
            ->whereRaw('LOWER(display_name) LIKE ?', ['%mug%'])
            ->first();

        // Ambil data Kartu PVC (ID Card / KTA)
        $data['totalPvcCards'] = DB::table('m_pvc_card_pricings')->count();
        $data['displayPvcCard'] = DB::table('m_display_products')
            ->whereRaw('LOWER(display_name) LIKE ?', ['%kartu pvc%'])
            ->orWhereRaw('LOWER(display_name) LIKE ?', ['%id card%'])
            ->first();

        // Ambil data Akrilik UV (2 Muka)
        $data['totalAkrilikUv'] = DB::table('m_akrilik_uv_pricings')->count();
        $data['displayAkrilikUv'] = DB::table('m_display_products')
            ->whereRaw('LOWER(display_name) LIKE ?', ['%akrilik uv%'])
            ->first();

        return view('admin.merchandise.index', $data);
    }

    /**
     * Simpan data tier lanyard baru (Create via Modal Popup)
     */
    public function storeLanyardTier(Request $request)
    {
        $validated = $request->validate([
            'width_type'   => 'required|string|in:2cm,2.5cm',
            'package_type' => 'required|string|in:tali,set',
            'tier_label'   => 'required|string|max:50',
            'min_qty'      => 'required|integer|min:1',
            'max_qty'      => 'nullable|integer|min:1',
            'price'        => 'required|numeric|min:0',
            'status'       => 'nullable|string|in:active,inactive',
        ]);

        $id = DB::table('m_lanyard_pricings')->insertGetId([
            'width_type'   => $validated['width_type'],
            'package_type' => $validated['package_type'],
            'tier_label'   => $validated['tier_label'],
            'min_qty'      => (int) $validated['min_qty'],
            'max_qty'      => !empty($validated['max_qty']) ? (int) $validated['max_qty'] : null,
            'price'        => (float) $validated['price'],
            'status'       => $validated['status'] ?? 'active',
            'created_by'   => Auth::user()->name ?? 'Administrator',
            'created_at'   => Carbon::now('Asia/Jakarta'),
            'updated_by'   => Auth::user()->name ?? 'Administrator',
            'updated_at'   => Carbon::now('Asia/Jakarta'),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tier Lanyard berhasil ditambahkan!',
                'id'      => $id,
            ]);
        }

        return redirect()->back()->with('success', 'Tier Lanyard berhasil ditambahkan!');
    }

    /**
     * Update data tier lanyard (Update via Modal Popup)
     */
    public function updateLanyardTier(Request $request, $id)
    {
        $validated = $request->validate([
            'width_type'   => 'nullable|string|in:2cm,2.5cm',
            'package_type' => 'nullable|string|in:tali,set',
            'tier_label'   => 'nullable|string|max:50',
            'min_qty'      => 'nullable|integer|min:1',
            'max_qty'      => 'nullable|integer|min:1',
            'price'        => 'required|numeric|min:0',
            'status'       => 'nullable|string|in:active,inactive',
        ]);

        $updateData = [
            'price'      => (float) $validated['price'],
            'updated_by' => Auth::user()->name ?? 'Administrator',
            'updated_at' => Carbon::now('Asia/Jakarta'),
        ];

        if (!empty($validated['width_type'])) {
            $updateData['width_type'] = $validated['width_type'];
        }
        if (!empty($validated['package_type'])) {
            $updateData['package_type'] = $validated['package_type'];
        }
        if (!empty($validated['tier_label'])) {
            $updateData['tier_label'] = $validated['tier_label'];
        }
        if (isset($validated['min_qty'])) {
            $updateData['min_qty'] = (int) $validated['min_qty'];
        }
        if (array_key_exists('max_qty', $validated)) {
            $updateData['max_qty'] = !empty($validated['max_qty']) ? (int) $validated['max_qty'] : null;
        }
        if (!empty($validated['status'])) {
            $updateData['status'] = $validated['status'];
        }

        DB::table('m_lanyard_pricings')->where('id', $id)->update($updateData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Tier Lanyard berhasil diperbarui.',
                'price'   => (float) $validated['price'],
            ]);
        }

        return redirect()->back()->with('success', 'Data Tier Lanyard berhasil diperbarui.');
    }

    /**
     * Hapus data tier lanyard (Delete via Modal Popup)
     */
    public function destroyLanyardTier(Request $request, $id)
    {
        $tier = DB::table('m_lanyard_pricings')->where('id', $id)->first();
        if (!$tier) {
            return response()->json([
                'success' => false,
                'message' => 'Data Tier Lanyard tidak ditemukan.',
            ], 404);
        }

        DB::table('m_lanyard_pricings')->where('id', $id)->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tier Lanyard berhasil dihapus.',
            ]);
        }

        return redirect()->back()->with('success', 'Tier Lanyard berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | PAPAN NAMA (NAME TAG DADA) CRUD HANDLERS
    |--------------------------------------------------------------------------
    */

    /**
     * Simpan varian papan nama baru (Create via Modal Popup)
     */
    public function storeNameTag(Request $request)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:100',
            'type_code'        => 'required|string|max:50',
            'fastener_options' => 'required|string|max:100',
            'price'            => 'required|numeric|min:0',
            'min_order_qty'    => 'nullable|integer|min:1',
            'description'      => 'nullable|string',
            'status'           => 'nullable|string|in:active,inactive',
        ]);

        $id = DB::table('m_name_tag_pricings')->insertGetId([
            'name'             => $validated['name'],
            'type_code'        => $validated['type_code'],
            'fastener_options' => $validated['fastener_options'],
            'price'            => (float) $validated['price'],
            'min_order_qty'    => !empty($validated['min_order_qty']) ? (int) $validated['min_order_qty'] : 1,
            'description'      => $validated['description'] ?? null,
            'status'           => $validated['status'] ?? 'active',
            'created_by'       => Auth::user()->name ?? 'Administrator',
            'created_at'       => Carbon::now('Asia/Jakarta'),
            'updated_by'       => Auth::user()->name ?? 'Administrator',
            'updated_at'       => Carbon::now('Asia/Jakarta'),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Papan Nama berhasil ditambahkan!',
                'id'      => $id,
            ]);
        }

        return redirect()->back()->with('success', 'Data Papan Nama berhasil ditambahkan!');
    }

    /**
     * Update varian papan nama (Update via Modal Popup)
     */
    public function updateNameTag(Request $request, $id)
    {
        $validated = $request->validate([
            'name'             => 'nullable|string|max:100',
            'type_code'        => 'nullable|string|max:50',
            'fastener_options' => 'nullable|string|max:100',
            'price'            => 'required|numeric|min:0',
            'min_order_qty'    => 'nullable|integer|min:1',
            'description'      => 'nullable|string',
            'status'           => 'nullable|string|in:active,inactive',
        ]);

        $updateData = [
            'price'      => (float) $validated['price'],
            'updated_by' => Auth::user()->name ?? 'Administrator',
            'updated_at' => Carbon::now('Asia/Jakarta'),
        ];

        if (!empty($validated['name'])) {
            $updateData['name'] = $validated['name'];
        }
        if (!empty($validated['type_code'])) {
            $updateData['type_code'] = $validated['type_code'];
        }
        if (!empty($validated['fastener_options'])) {
            $updateData['fastener_options'] = $validated['fastener_options'];
        }
        if (isset($validated['min_order_qty'])) {
            $updateData['min_order_qty'] = (int) $validated['min_order_qty'];
        }
        if (array_key_exists('description', $validated)) {
            $updateData['description'] = $validated['description'];
        }
        if (!empty($validated['status'])) {
            $updateData['status'] = $validated['status'];
        }

        DB::table('m_name_tag_pricings')->where('id', $id)->update($updateData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Papan Nama berhasil diperbarui.',
                'price'   => (float) $validated['price'],
            ]);
        }

        return redirect()->back()->with('success', 'Data Papan Nama berhasil diperbarui.');
    }

    /**
     * Hapus varian papan nama (Delete via Modal Popup)
     */
    public function destroyNameTag(Request $request, $id)
    {
        $item = DB::table('m_name_tag_pricings')->where('id', $id)->first();
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Data Papan Nama tidak ditemukan.',
            ], 404);
        }

        DB::table('m_name_tag_pricings')->where('id', $id)->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Papan Nama berhasil dihapus.',
            ]);
        }

        return redirect()->back()->with('success', 'Data Papan Nama berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | PLAKAT (AKRILIK UV & KAYU) CRUD HANDLERS
    |--------------------------------------------------------------------------
    */

    /**
     * Simpan varian plakat baru (Create via Modal Popup)
     */
    public function storePlakat(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:150',
            'material_type' => 'required|string|in:akrilik,kayu',
            'thickness'     => 'required|string|max:30',
            'base_type'     => 'required|string|in:akrilik,hitam,kayu',
            'box_type'      => 'required|string|in:tanpa_box,box',
            'dimensions'    => 'nullable|string|max:50',
            'price'         => 'required|numeric|min:0',
            'design_fee'    => 'nullable|numeric|min:0',
            'description'   => 'nullable|string',
            'status'        => 'nullable|string|in:active,inactive',
        ]);

        $id = DB::table('m_plakat_pricings')->insertGetId([
            'name'          => $validated['name'],
            'material_type' => $validated['material_type'],
            'thickness'     => $validated['thickness'],
            'base_type'     => $validated['base_type'],
            'box_type'      => $validated['box_type'],
            'dimensions'    => !empty($validated['dimensions']) ? $validated['dimensions'] : '20x15 cm',
            'price'         => (float) $validated['price'],
            'design_fee'    => !empty($validated['design_fee']) ? (float) $validated['design_fee'] : 0.00,
            'description'   => $validated['description'] ?? null,
            'status'        => $validated['status'] ?? 'active',
            'created_by'    => Auth::user()->name ?? 'Administrator',
            'created_at'    => Carbon::now('Asia/Jakarta'),
            'updated_by'    => Auth::user()->name ?? 'Administrator',
            'updated_at'    => Carbon::now('Asia/Jakarta'),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Plakat berhasil ditambahkan!',
                'id'      => $id,
            ]);
        }

        return redirect()->back()->with('success', 'Data Plakat berhasil ditambahkan!');
    }

    /**
     * Update varian plakat (Update via Modal Popup)
     */
    public function updatePlakat(Request $request, $id)
    {
        $validated = $request->validate([
            'name'          => 'nullable|string|max:150',
            'material_type' => 'nullable|string|in:akrilik,kayu',
            'thickness'     => 'nullable|string|max:30',
            'base_type'     => 'nullable|string|in:akrilik,hitam,kayu',
            'box_type'      => 'nullable|string|in:tanpa_box,box',
            'dimensions'    => 'nullable|string|max:50',
            'price'         => 'required|numeric|min:0',
            'design_fee'    => 'nullable|numeric|min:0',
            'description'   => 'nullable|string',
            'status'        => 'nullable|string|in:active,inactive',
        ]);

        $updateData = [
            'price'      => (float) $validated['price'],
            'updated_by' => Auth::user()->name ?? 'Administrator',
            'updated_at' => Carbon::now('Asia/Jakarta'),
        ];

        if (!empty($validated['name'])) {
            $updateData['name'] = $validated['name'];
        }
        if (!empty($validated['material_type'])) {
            $updateData['material_type'] = $validated['material_type'];
        }
        if (!empty($validated['thickness'])) {
            $updateData['thickness'] = $validated['thickness'];
        }
        if (!empty($validated['base_type'])) {
            $updateData['base_type'] = $validated['base_type'];
        }
        if (!empty($validated['box_type'])) {
            $updateData['box_type'] = $validated['box_type'];
        }
        if (array_key_exists('dimensions', $validated)) {
            $updateData['dimensions'] = !empty($validated['dimensions']) ? $validated['dimensions'] : '20x15 cm';
        }
        if (array_key_exists('design_fee', $validated)) {
            $updateData['design_fee'] = !empty($validated['design_fee']) ? (float) $validated['design_fee'] : 0.00;
        }
        if (array_key_exists('description', $validated)) {
            $updateData['description'] = $validated['description'];
        }
        if (!empty($validated['status'])) {
            $updateData['status'] = $validated['status'];
        }

        DB::table('m_plakat_pricings')->where('id', $id)->update($updateData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Plakat berhasil diperbarui.',
                'price'   => (float) $validated['price'],
            ]);
        }

        return redirect()->back()->with('success', 'Data Plakat berhasil diperbarui.');
    }

    /**
     * Hapus varian plakat (Delete via Modal Popup)
     */
    public function destroyPlakat(Request $request, $id)
    {
        $item = DB::table('m_plakat_pricings')->where('id', $id)->first();
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Data Plakat tidak ditemukan.',
            ], 404);
        }

        DB::table('m_plakat_pricings')->where('id', $id)->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Plakat berhasil dihapus.',
            ]);
        }

        return redirect()->back()->with('success', 'Data Plakat berhasil dihapus.');
    }

    /**
     * Simpan data varian buku nota baru (Create via Modal Popup)
     */
    public function storeNota(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:150',
            'paper_type'      => 'required|string|in:ncr,hvs',
            'ply_count'       => 'required|integer|min:1|max:4',
            'ply_label'       => 'required|string|max:50',
            'ink_count'       => 'required|integer|min:1|max:3',
            'ink_label'       => 'required|string|max:50',
            'sheets_per_book' => 'required|integer|min:1',
            'price'           => 'required|numeric|min:0',
            'description'     => 'nullable|string',
            'status'          => 'nullable|string|in:active,inactive',
        ]);

        $id = DB::table('m_nota_pricings')->insertGetId([
            'name'            => $validated['name'],
            'paper_type'      => $validated['paper_type'],
            'ply_count'       => (int) $validated['ply_count'],
            'ply_label'       => $validated['ply_label'],
            'ink_count'       => (int) $validated['ink_count'],
            'ink_label'       => $validated['ink_label'],
            'sheets_per_book' => (int) $validated['sheets_per_book'],
            'price'           => (float) $validated['price'],
            'description'     => $validated['description'] ?? null,
            'status'          => $validated['status'] ?? 'active',
            'created_by'      => Auth::user()->name ?? 'Administrator',
            'created_at'      => Carbon::now('Asia/Jakarta'),
            'updated_by'      => Auth::user()->name ?? 'Administrator',
            'updated_at'      => Carbon::now('Asia/Jakarta'),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Varian Buku Nota berhasil ditambahkan!',
                'id'      => $id,
            ]);
        }

        return redirect()->back()->with('success', 'Data Varian Buku Nota berhasil ditambahkan!');
    }

    /**
     * Update data varian buku nota (Update via Modal Popup)
     */
    public function updateNota(Request $request, $id)
    {
        $validated = $request->validate([
            'name'            => 'nullable|string|max:150',
            'paper_type'      => 'nullable|string|in:ncr,hvs',
            'ply_count'       => 'nullable|integer|min:1|max:4',
            'ply_label'       => 'nullable|string|max:50',
            'ink_count'       => 'nullable|integer|min:1|max:3',
            'ink_label'       => 'nullable|string|max:50',
            'sheets_per_book' => 'nullable|integer|min:1',
            'price'           => 'required|numeric|min:0',
            'description'     => 'nullable|string',
            'status'          => 'nullable|string|in:active,inactive',
        ]);

        $updateData = [
            'price'      => (float) $validated['price'],
            'updated_by' => Auth::user()->name ?? 'Administrator',
            'updated_at' => Carbon::now('Asia/Jakarta'),
        ];

        if (!empty($validated['name'])) {
            $updateData['name'] = $validated['name'];
        }
        if (!empty($validated['paper_type'])) {
            $updateData['paper_type'] = $validated['paper_type'];
        }
        if (isset($validated['ply_count'])) {
            $updateData['ply_count'] = (int) $validated['ply_count'];
        }
        if (!empty($validated['ply_label'])) {
            $updateData['ply_label'] = $validated['ply_label'];
        }
        if (isset($validated['ink_count'])) {
            $updateData['ink_count'] = (int) $validated['ink_count'];
        }
        if (!empty($validated['ink_label'])) {
            $updateData['ink_label'] = $validated['ink_label'];
        }
        if (isset($validated['sheets_per_book'])) {
            $updateData['sheets_per_book'] = (int) $validated['sheets_per_book'];
        }
        if (array_key_exists('description', $validated)) {
            $updateData['description'] = $validated['description'];
        }
        if (!empty($validated['status'])) {
            $updateData['status'] = $validated['status'];
        }

        DB::table('m_nota_pricings')->where('id', $id)->update($updateData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Varian Buku Nota berhasil diperbarui.',
                'price'   => (float) $validated['price'],
            ]);
        }

        return redirect()->back()->with('success', 'Data Varian Buku Nota berhasil diperbarui.');
    }

    /**
     * Hapus data varian buku nota (Delete via Modal Popup)
     */
    public function destroyNota(Request $request, $id)
    {
        $item = DB::table('m_nota_pricings')->where('id', $id)->first();
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Data Varian Buku Nota tidak ditemukan.',
            ], 404);
        }

        DB::table('m_nota_pricings')->where('id', $id)->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Varian Buku Nota berhasil dihapus.',
            ]);
        }

        return redirect()->back()->with('success', 'Varian Buku Nota berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | MUG SUBLIMASI CRUD HANDLERS
    |--------------------------------------------------------------------------
    */

    public function storeMug(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'nullable|string|max:100',
            'tier_label'  => 'required|string|max:50',
            'min_qty'     => 'required|integer|min:1',
            'max_qty'     => 'nullable|integer|min:1',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status'      => 'nullable|string|in:active,inactive',
        ]);

        $id = DB::table('m_mug_pricings')->insertGetId([
            'name'        => !empty($validated['name']) ? $validated['name'] : 'Mug Sublimasi Keramik SNI (Putih Standar)',
            'tier_label'  => $validated['tier_label'],
            'min_qty'     => (int) $validated['min_qty'],
            'max_qty'     => !empty($validated['max_qty']) ? (int) $validated['max_qty'] : null,
            'price'       => (float) $validated['price'],
            'description' => $validated['description'] ?? null,
            'status'      => $validated['status'] ?? 'active',
            'created_by'  => Auth::user()->name ?? 'Administrator',
            'created_at'  => Carbon::now('Asia/Jakarta'),
            'updated_by'  => Auth::user()->name ?? 'Administrator',
            'updated_at'  => Carbon::now('Asia/Jakarta'),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tier Mug Sublimasi berhasil ditambahkan!',
                'id'      => $id,
            ]);
        }

        return redirect()->back()->with('success', 'Tier Mug Sublimasi berhasil ditambahkan!');
    }

    public function updateMug(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => 'nullable|string|max:100',
            'tier_label'  => 'nullable|string|max:50',
            'min_qty'     => 'nullable|integer|min:1',
            'max_qty'     => 'nullable|integer|min:1',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status'      => 'nullable|string|in:active,inactive',
        ]);

        $updateData = [
            'price'      => (float) $validated['price'],
            'updated_by' => Auth::user()->name ?? 'Administrator',
            'updated_at' => Carbon::now('Asia/Jakarta'),
        ];

        if (!empty($validated['name'])) $updateData['name'] = $validated['name'];
        if (!empty($validated['tier_label'])) $updateData['tier_label'] = $validated['tier_label'];
        if (isset($validated['min_qty'])) $updateData['min_qty'] = (int) $validated['min_qty'];
        if (array_key_exists('max_qty', $validated)) $updateData['max_qty'] = !empty($validated['max_qty']) ? (int) $validated['max_qty'] : null;
        if (array_key_exists('description', $validated)) $updateData['description'] = $validated['description'];
        if (!empty($validated['status'])) $updateData['status'] = $validated['status'];

        DB::table('m_mug_pricings')->where('id', $id)->update($updateData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Tier Mug Sublimasi berhasil diperbarui.',
                'price'   => (float) $validated['price'],
            ]);
        }

        return redirect()->back()->with('success', 'Data Tier Mug Sublimasi berhasil diperbarui.');
    }

    public function destroyMug(Request $request, $id)
    {
        $tier = DB::table('m_mug_pricings')->where('id', $id)->first();
        if (!$tier) {
            return response()->json([
                'success' => false,
                'message' => 'Data Tier Mug Sublimasi tidak ditemukan.',
            ], 404);
        }

        DB::table('m_mug_pricings')->where('id', $id)->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tier Mug Sublimasi berhasil dihapus.',
            ]);
        }

        return redirect()->back()->with('success', 'Tier Mug Sublimasi berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | KARTU PVC (ID CARD / KTA) CRUD HANDLERS
    |--------------------------------------------------------------------------
    */

    public function storePvcCard(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'nullable|string|max:100',
            'tier_label'  => 'required|string|max:50',
            'min_qty'     => 'required|integer|min:1',
            'max_qty'     => 'nullable|integer|min:1',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status'      => 'nullable|string|in:active,inactive',
        ]);

        $id = DB::table('m_pvc_card_pricings')->insertGetId([
            'name'        => !empty($validated['name']) ? $validated['name'] : 'Kartu ID Card / KTA PVC (Standar ATM)',
            'tier_label'  => $validated['tier_label'],
            'min_qty'     => (int) $validated['min_qty'],
            'max_qty'     => !empty($validated['max_qty']) ? (int) $validated['max_qty'] : null,
            'price'       => (float) $validated['price'],
            'description' => $validated['description'] ?? null,
            'status'      => $validated['status'] ?? 'active',
            'created_by'  => Auth::user()->name ?? 'Administrator',
            'created_at'  => Carbon::now('Asia/Jakarta'),
            'updated_by'  => Auth::user()->name ?? 'Administrator',
            'updated_at'  => Carbon::now('Asia/Jakarta'),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tier Kartu PVC berhasil ditambahkan!',
                'id'      => $id,
            ]);
        }

        return redirect()->back()->with('success', 'Tier Kartu PVC berhasil ditambahkan!');
    }

    public function updatePvcCard(Request $request, $id)
    {
        $validated = $request->validate([
            'name'        => 'nullable|string|max:100',
            'tier_label'  => 'nullable|string|max:50',
            'min_qty'     => 'nullable|integer|min:1',
            'max_qty'     => 'nullable|integer|min:1',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'status'      => 'nullable|string|in:active,inactive',
        ]);

        $updateData = [
            'price'      => (float) $validated['price'],
            'updated_by' => Auth::user()->name ?? 'Administrator',
            'updated_at' => Carbon::now('Asia/Jakarta'),
        ];

        if (!empty($validated['name'])) $updateData['name'] = $validated['name'];
        if (!empty($validated['tier_label'])) $updateData['tier_label'] = $validated['tier_label'];
        if (isset($validated['min_qty'])) $updateData['min_qty'] = (int) $validated['min_qty'];
        if (array_key_exists('max_qty', $validated)) $updateData['max_qty'] = !empty($validated['max_qty']) ? (int) $validated['max_qty'] : null;
        if (array_key_exists('description', $validated)) $updateData['description'] = $validated['description'];
        if (!empty($validated['status'])) $updateData['status'] = $validated['status'];

        DB::table('m_pvc_card_pricings')->where('id', $id)->update($updateData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Tier Kartu PVC berhasil diperbarui.',
                'price'   => (float) $validated['price'],
            ]);
        }

        return redirect()->back()->with('success', 'Data Tier Kartu PVC berhasil diperbarui.');
    }

    public function destroyPvcCard(Request $request, $id)
    {
        $tier = DB::table('m_pvc_card_pricings')->where('id', $id)->first();
        if (!$tier) {
            return response()->json([
                'success' => false,
                'message' => 'Data Tier Kartu PVC tidak ditemukan.',
            ], 404);
        }

        DB::table('m_pvc_card_pricings')->where('id', $id)->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Tier Kartu PVC berhasil dihapus.',
            ]);
        }

        return redirect()->back()->with('success', 'Tier Kartu PVC berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | AKRILIK UV (2 MUKA 1 LAPIS) CRUD HANDLERS
    |--------------------------------------------------------------------------
    */

    public function storeAkrilikUv(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:150',
            'spec_label'    => 'required|string|max:100',
            'thickness'     => 'required|string|max:30',
            'min_order_qty' => 'nullable|integer|min:1',
            'price'         => 'required|numeric|min:0',
            'description'   => 'nullable|string',
            'status'        => 'nullable|string|in:active,inactive',
        ]);

        $id = DB::table('m_akrilik_uv_pricings')->insertGetId([
            'name'          => $validated['name'],
            'spec_label'    => $validated['spec_label'],
            'thickness'     => $validated['thickness'],
            'min_order_qty' => !empty($validated['min_order_qty']) ? (int) $validated['min_order_qty'] : 1,
            'price'         => (float) $validated['price'],
            'description'   => $validated['description'] ?? null,
            'status'        => $validated['status'] ?? 'active',
            'created_by'    => Auth::user()->name ?? 'Administrator',
            'created_at'    => Carbon::now('Asia/Jakarta'),
            'updated_by'    => Auth::user()->name ?? 'Administrator',
            'updated_at'    => Carbon::now('Asia/Jakarta'),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Varian Akrilik UV berhasil ditambahkan!',
                'id'      => $id,
            ]);
        }

        return redirect()->back()->with('success', 'Varian Akrilik UV berhasil ditambahkan!');
    }

    public function updateAkrilikUv(Request $request, $id)
    {
        $validated = $request->validate([
            'name'          => 'nullable|string|max:150',
            'spec_label'    => 'nullable|string|max:100',
            'thickness'     => 'nullable|string|max:30',
            'min_order_qty' => 'nullable|integer|min:1',
            'price'         => 'required|numeric|min:0',
            'description'   => 'nullable|string',
            'status'        => 'nullable|string|in:active,inactive',
        ]);

        $updateData = [
            'price'      => (float) $validated['price'],
            'updated_by' => Auth::user()->name ?? 'Administrator',
            'updated_at' => Carbon::now('Asia/Jakarta'),
        ];

        if (!empty($validated['name'])) $updateData['name'] = $validated['name'];
        if (!empty($validated['spec_label'])) $updateData['spec_label'] = $validated['spec_label'];
        if (!empty($validated['thickness'])) $updateData['thickness'] = $validated['thickness'];
        if (isset($validated['min_order_qty'])) $updateData['min_order_qty'] = (int) $validated['min_order_qty'];
        if (array_key_exists('description', $validated)) $updateData['description'] = $validated['description'];
        if (!empty($validated['status'])) $updateData['status'] = $validated['status'];

        DB::table('m_akrilik_uv_pricings')->where('id', $id)->update($updateData);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Varian Akrilik UV berhasil diperbarui.',
                'price'   => (float) $validated['price'],
            ]);
        }

        return redirect()->back()->with('success', 'Data Varian Akrilik UV berhasil diperbarui.');
    }

    public function destroyAkrilikUv(Request $request, $id)
    {
        $item = DB::table('m_akrilik_uv_pricings')->where('id', $id)->first();
        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Data Varian Akrilik UV tidak ditemukan.',
            ], 404);
        }

        DB::table('m_akrilik_uv_pricings')->where('id', $id)->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Varian Akrilik UV berhasil dihapus.',
            ]);
        }

        return redirect()->back()->with('success', 'Varian Akrilik UV berhasil dihapus.');
    }
}

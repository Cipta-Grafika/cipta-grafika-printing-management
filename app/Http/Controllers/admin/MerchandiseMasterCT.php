<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MerchandiseMasterCT extends Controller
{
    /**
     * Tampilkan halaman Master Data Merchandise (Lanyard, Papan Nama, Plakat)
     */
    public function index()
    {
        $rows = DB::table('m_lanyard_pricings')
            ->orderBy('width_type', 'asc')
            ->orderBy('package_type', 'asc')
            ->orderBy('min_qty', 'asc')
            ->get();

        $data['allLanyardTiers'] = $rows;
        $data['tiers2cmTali'] = $rows->where('width_type', '2cm')->where('package_type', 'tali')->values();
        $data['tiers2cmSet']  = $rows->where('width_type', '2cm')->where('package_type', 'set')->values();
        $data['tiers25cmTali'] = $rows->where('width_type', '2.5cm')->where('package_type', 'tali')->values();
        $data['tiers25cmSet']  = $rows->where('width_type', '2.5cm')->where('package_type', 'set')->values();

        $data['totalLanyardTiers'] = $rows->count();

        // Ambil info produk display lanyard jika ada
        $data['displayLanyard'] = DB::table('m_display_products')
            ->whereRaw('LOWER(display_name) LIKE ?', ['%lanyard%'])
            ->first();

        // Ambil data Papan Nama (Name Tag Dada)
        $nameTags = DB::table('m_name_tag_pricings')
            ->orderBy('id', 'asc')
            ->get();
        $data['allNameTags'] = $nameTags;
        $data['totalNameTags'] = $nameTags->count();

        // Ambil info produk display papan nama jika ada
        $data['displayNameTag'] = DB::table('m_display_products')
            ->whereRaw('LOWER(display_name) LIKE ?', ['%papan nama%'])
            ->first();

        // Ambil data Plakat Akrilik & Kayu
        $plakats = DB::table('m_plakat_pricings')
            ->orderBy('material_type', 'asc')
            ->orderBy('thickness', 'asc')
            ->orderBy('base_type', 'asc')
            ->orderBy('box_type', 'asc')
            ->get();
        $data['allPlakats'] = $plakats;
        $data['totalPlakats'] = $plakats->count();

        // Ambil info produk display plakat jika ada
        $data['displayPlakat'] = DB::table('m_display_products')
            ->whereRaw('LOWER(display_name) LIKE ?', ['%plakat%'])
            ->first();

        // Ambil data Buku Nota (NCR & HVS)
        $notas = DB::table('m_nota_pricings')
            ->orderBy('paper_type', 'asc')
            ->orderBy('ply_count', 'asc')
            ->orderBy('ink_count', 'asc')
            ->get();
        $data['allNotas'] = $notas;
        $data['ncrNotas'] = $notas->where('paper_type', 'ncr')->values();
        $data['hvsNotas'] = $notas->where('paper_type', 'hvs')->values();
        $data['totalNotas'] = $notas->count();

        // Ambil info produk display nota jika ada
        $data['displayNota'] = DB::table('m_display_products')
            ->whereRaw('LOWER(display_name) LIKE ?', ['%nota%'])
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
}


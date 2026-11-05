<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotaMasterCT extends Controller
{
    /**
     * Tampilkan halaman Master Data Buku Nota (NCR & HVS)
     */
    public function index()
    {
        $notas = DB::table('m_nota_pricings')
            ->orderBy('paper_type', 'asc')
            ->orderBy('ply_count', 'asc')
            ->orderBy('ink_count', 'asc')
            ->get();

        $data['allNotas'] = $notas;
        $data['ncrNotas'] = $notas->where('paper_type', 'ncr')->values();
        $data['hvsNotas'] = $notas->where('paper_type', 'hvs')->values();
        $data['totalNotas'] = $notas->count();
        $data['totalNcr'] = $data['ncrNotas']->count();
        $data['totalHvs'] = $data['hvsNotas']->count();

        // Ambil info produk display nota jika ada
        $data['displayNota'] = DB::table('m_display_products')
            ->whereRaw('LOWER(display_name) LIKE ?', ['%nota%'])
            ->first();

        return view('admin.nota.index', $data);
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


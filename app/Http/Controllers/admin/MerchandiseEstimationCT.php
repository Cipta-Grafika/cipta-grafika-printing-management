<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Support\CetakiaCustomer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MerchandiseEstimationCT extends Controller
{
    /**
     * Tampilkan halaman Form Buat Estimasi Merchandise (Lanyard, Papan Nama, Plakat, Nota)
     */
    public function create()
    {
        // 1. Data Lokasi Cabang
        $data['locations'] = DB::table('m_locations')
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        // 2. Data Pelanggan Cetakia (jika ada)
        try {
            $data['customers'] = CetakiaCustomer::all();
        } catch (\Throwable $e) {
            $data['customers'] = [];
        }

        // 3. Data Master Lanyard Tiers
        $data['lanyardTiers'] = DB::table('m_lanyard_pricings')
            ->where('status', 'active')
            ->orderBy('width_type', 'asc')
            ->orderBy('package_type', 'asc')
            ->orderBy('min_qty', 'asc')
            ->get();

        // 4. Data Master Papan Nama Dada
        $data['nameTagItems'] = DB::table('m_name_tag_pricings')
            ->where('status', 'active')
            ->orderBy('id', 'asc')
            ->get();

        // 5. Data Master Plakat Akrilik & Kayu
        $data['plakatItems'] = DB::table('m_plakat_pricings')
            ->where('status', 'active')
            ->orderBy('material_type', 'asc')
            ->orderBy('thickness', 'asc')
            ->orderBy('base_type', 'asc')
            ->orderBy('box_type', 'asc')
            ->get();

        // 6. Data Master Buku Nota NCR & HVS
        $data['notaItems'] = DB::table('m_nota_pricings')
            ->where('status', 'active')
            ->orderBy('paper_type', 'asc')
            ->orderBy('ply_count', 'asc')
            ->orderBy('ink_count', 'asc')
            ->get();

        // 7. Data Master Mug Sublimasi
        $data['mugItems'] = DB::table('m_mug_pricings')
            ->where('status', 'active')
            ->orderBy('min_qty', 'asc')
            ->get();

        // 8. Data Master Kartu PVC (ID Card & KTA)
        $data['pvcCardItems'] = DB::table('m_pvc_card_pricings')
            ->where('status', 'active')
            ->orderBy('min_qty', 'asc')
            ->get();

        // 9. Data Master Bahan Akrilik UV (2 Muka)
        $data['akrilikUvItems'] = DB::table('m_akrilik_uv_pricings')
            ->where('status', 'active')
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.estimation.merchandise.create', $data);
    }

    /**
     * AJAX Kalkulasi / Verifikasi Harga Merchandise
     */
    public function calculate(Request $request)
    {
        $category = $request->input('category', 'lanyard');

        return response()->json([
            'success' => true,
            'category' => $category,
            'message' => 'Kalkulasi merchandise berhasil',
        ]);
    }
}


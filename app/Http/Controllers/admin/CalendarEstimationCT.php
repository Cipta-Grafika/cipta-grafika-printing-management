<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\CalendarCalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalendarEstimationCT extends Controller
{
    protected CalendarCalculationService $calcService;

    public function __construct(CalendarCalculationService $calcService)
    {
        $this->calcService = $calcService;
    }

    /**
     * Tampilkan halaman Form Buat Estimasi Cetak Kalender (Dinding & Meja)
     */
    public function create()
    {
        // 1. Data Lokasi Cabang
        $data['locations'] = DB::table('m_locations')
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        // 2. Data Master Produk & Ukuran Kalender
        $data['products'] = DB::table('m_calendar_products')
            ->where('status', 'active')
            ->orderBy('id', 'asc')
            ->get();

        // 3. Data Master Kertas Kalender
        $data['materials'] = DB::table('m_calendar_materials')
            ->where('status', 'active')
            ->orderBy('id', 'asc')
            ->get();

        // 4. Data Komponen & Aksesoris Finishing
        $data['components'] = DB::table('m_calendar_components')
            ->where('status', 'active')
            ->orderBy('id', 'asc')
            ->get();

        // 5. Data Pricing Rules (Markup Tiering)
        $data['pricingRules'] = DB::table('m_calendar_pricing_rules')
            ->where('status', 'active')
            ->orderBy('min_qty', 'asc')
            ->get();

        return view('admin.estimation.calendar.create')->with($data);
    }

    /**
     * Hitung Estimasi Cetak Kalender secara Real-Time via AJAX
     */
    public function calculate(Request $request)
    {
        try {
            $productId = (int) $request->input('product_id');
            $materialId = (int) $request->input('material_id');
            $qty = max(1, (int) $request->input('qty', 100));
            $leafCount = max(1, (int) $request->input('leaf_count', 7));
            $lamination = (string) $request->input('lamination', 'none');
            $supportType = (string) $request->input('support_type', 'printing_fc');
            $boardType = (string) $request->input('board_type', 'boot_30');

            if ($productId <= 0 || $materialId <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk dan material harus dipilih terlebih dahulu.',
                ], 422);
            }

            $result = $this->calcService->calculate([
                'product_id'   => $productId,
                'material_id'  => $materialId,
                'qty'          => $qty,
                'leaf_count'   => $leafCount,
                'lamination'   => $lamination,
                'support_type' => $supportType,
                'board_type'   => $boardType,
            ]);

            return response()->json([
                'success' => true,
                'data'    => $result,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error calculating calendar estimate: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan kalkulasi: ' . $e->getMessage(),
            ], 500);
        }
    }
}


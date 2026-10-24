<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\OffsetCalculationService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OffsetEstimationCT extends Controller
{
    protected OffsetCalculationService $calcService;

    public function __construct(OffsetCalculationService $calcService)
    {
        $this->calcService = $calcService;
    }

    /**
     * Tampilkan halaman Form Buat Estimasi Cetak Offset (Brosur / Flyer)
     */
    public function create()
    {
        // 1. Data Lokasi Cabang
        $data['locations'] = DB::table('m_locations')
            ->where('status', 'Active')
            ->orderBy('name', 'asc')
            ->get();

        // 2. Data Master Produk Offset (Brosur A4, A5, DL)
        $data['products'] = DB::table('m_offset_products')
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->orderBy('product_name', 'asc')
            ->get();

        // 3. Data Master Kertas Plano Offset (Art Paper 120, 150, HVS 100, dll)
        $data['planoMaterials'] = DB::table('m_offset_plano_materials')
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->orderBy('material_name', 'asc')
            ->get();

        // 4. Data Plat CTP
        $data['plate'] = DB::table('m_offset_plates')
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->first();

        // 5. Data Ongkos Cetak Mesin & Potong
        $data['printCost'] = DB::table('m_offset_print_costs')
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->first();
        if (!$data['printCost']) {
            $data['printCost'] = DB::table('m_offset_print_costs')->first();
        }

        // 6. Data Pricing Rules (Markup & Rounding)
        $data['pricingRule'] = DB::table('m_offset_pricing_rules')
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->first();
        if (!$data['pricingRule']) {
            $data['pricingRule'] = DB::table('m_offset_pricing_rules')->first();
        }

        return view('admin.estimation.offset.create')->with($data);
    }

    /**
     * Hitung Estimasi Cetak Brosur & Flyer (Digital / Offset) (AJAX)
     * Mengembalikan rincian HPP, Harga Jual, serta Matriks Simulasi
     */
    public function calculate(Request $request)
    {
        $method = $request->input('production_method', 'offset');

        // Normalisasi format input diskon jika ada titik/koma/format rupiah
        if ($request->has('discount_value')) {
            $rawDiscount = $request->input('discount_value');
            if (is_string($rawDiscount)) {
                $cleaned = str_replace(['Rp', 'rp', 'RP', '.', ' '], '', $rawDiscount);
                $cleaned = str_replace(',', '.', $cleaned);
                $request->merge(['discount_value' => is_numeric($cleaned) ? (float) $cleaned : null]);
            }
        }

        $rules = [
            'production_method' => 'nullable|in:offset,digital',
            'product_id' => 'required|integer|exists:m_offset_products,id',
            'plano_material_id' => 'required|integer|exists:m_offset_plano_materials,id',
            'print_sides' => 'required|in:1,2',
            'price_type' => 'required|in:general,division',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'location_id' => 'nullable|integer|exists:m_locations,id',
            'lead_time' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:1000',
            'discount_type' => 'nullable|in:none,percentage,nominal',
            'discount_value' => 'nullable|numeric|min:0',
        ];

        if ($method === 'digital') {
            $rules['digital_pieces'] = 'nullable|integer|min:1';
        } else {
            $rules['rims'] = 'required|numeric|min:0.1';
        }

        $validated = $request->validate($rules);

        try {
            $locationName = '-';
            if (!empty($validated['location_id'])) {
                $locationName = DB::table('m_locations')->where('id', $validated['location_id'])->value('name') ?? '-';
            }

            $customerInfo = [
                'name' => $validated['customer_name'] ?? '-',
                'phone' => $validated['customer_phone'] ?? '-',
                'location' => $locationName,
                'lead_time' => ($validated['lead_time'] ?? 3) . ' hari kerja',
                'notes' => $validated['notes'] ?? '',
            ];

            if ($method === 'digital') {
                $pieces = (int) ($request->input('digital_pieces') ?? 100);
                if ($pieces <= 0) $pieces = 100;

                $calc = $this->calcService->calculateDigital(
                    (int) $validated['product_id'],
                    (int) $validated['plano_material_id'],
                    $pieces,
                    (int) $validated['print_sides'],
                    (string) $validated['price_type']
                );

                // Terapkan diskon
                $calc = $this->applyDiscount($calc, 'digital', $validated, $request);

                $matrix = $this->calcService->generateDigitalMatrix(
                    (int) $validated['product_id'],
                    (int) $validated['plano_material_id'],
                    (string) $validated['price_type']
                );

                // Perbandingan dengan Offset (1 Rim)
                $offsetCompare = null;
                try {
                    $offset1Rim = $this->calcService->calculate(
                        (int) $validated['product_id'],
                        (int) $validated['plano_material_id'],
                        1,
                        (int) $validated['print_sides'],
                        (string) $validated['price_type']
                    );
                    $offsetCompare = [
                        'rims' => 1,
                        'pieces' => 500,
                        'total_selling_price' => $offset1Rim['totals']['total_selling_price'],
                        'selling_price_per_pcs' => $offset1Rim['totals']['selling_price_per_pcs'],
                    ];
                } catch (\Throwable $e) {
                    // Ignore comparison failure
                }

                return response()->json([
                    'success' => true,
                    'method' => 'digital',
                    'data' => array_merge($calc, [
                        'customer' => $customerInfo,
                        'offset_compare' => $offsetCompare,
                    ]),
                    'matrix' => $matrix,
                ]);
            }

            // Default: Mode Cetak Offset
            $calc = $this->calcService->calculate(
                (int) $validated['product_id'],
                (int) $validated['plano_material_id'],
                (float) $validated['rims'],
                (int) $validated['print_sides'],
                (string) $validated['price_type']
            );

            // Terapkan diskon
            $calc = $this->applyDiscount($calc, 'offset', $validated, $request);

            $matrix = $this->calcService->generatePriceMatrix(
                (int) $validated['product_id'],
                (int) $validated['plano_material_id'],
                (string) $validated['price_type']
            );

            // Perbandingan dengan Digital (500 Lembar)
            $digitalCompare = null;
            try {
                $digital500 = $this->calcService->calculateDigital(
                    (int) $validated['product_id'],
                    (int) $validated['plano_material_id'],
                    500,
                    (int) $validated['print_sides'],
                    (string) $validated['price_type']
                );
                $digitalCompare = [
                    'pieces' => 500,
                    'total_selling_price' => $digital500['totals']['total_selling_price'],
                    'selling_price_per_pcs' => $digital500['totals']['selling_price_per_pcs'],
                ];
            } catch (\Throwable $e) {
                // Ignore comparison failure
            }

            return response()->json([
                'success' => true,
                'method' => 'offset',
                'data' => array_merge($calc, [
                    'customer' => $customerInfo,
                    'digital_compare' => $digitalCompare,
                ]),
                'matrix' => $matrix,
            ]);
        } catch (\Throwable $e) {
            Log::error('Brosur Estimation Calculation Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung estimasi brosur: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Terapkan kalkulasi diskon (persen atau nominal) ke data kalkulasi estimasi brosur.
     */
    private function applyDiscount(array $calc, string $method, array $validated, Request $request): array
    {
        $subtotal = (float) ($calc['totals']['total_selling_price'] ?? 0);
        $discountType = $validated['discount_type'] ?? 'none';
        $discountValue = (float) ($validated['discount_value'] ?? 0);
        $discountAmount = 0;

        if ($discountType === 'percentage' && $discountValue > 0) {
            if ($discountValue > 100) {
                throw new \Exception('Persentase diskon tidak boleh lebih dari 100%.');
            }
            $discountAmount = $subtotal * ($discountValue / 100);
        } elseif ($discountType === 'nominal' && $discountValue > 0) {
            $discountAmount = $discountValue;
        }

        $discountAmount = min($discountAmount, $subtotal);

        if ($discountAmount > 0) {
            // Pembulatan ke atas per kelipatan Rp 1.000 untuk grand total setelah diskon
            $grandTotal = (int) (ceil(round($subtotal - $discountAmount, 2) / 1000) * 1000);
            $discountAmount = max(0, (int) ($subtotal - $grandTotal));
        } else {
            $grandTotal = (int) $subtotal;
            $discountAmount = 0;
        }

        if ($method === 'digital') {
            $pieces = (int) ($request->input('digital_pieces') ?? 100);
            if ($pieces <= 0) $pieces = 100;
            $finalPricePerPcs = $pieces > 0 ? round($grandTotal / $pieces, 2) : 0;
            $finalPricePerRim = $finalPricePerPcs * 500;
        } else {
            $rims = (float) ($validated['rims'] ?? 1);
            if ($rims <= 0) $rims = 1;
            $finalPricePerRim = round($grandTotal / $rims);
            $finalPricePerPcs = round($grandTotal / ($rims * 500), 2);
        }

        $calc['totals']['subtotal'] = (int) $subtotal;
        $calc['totals']['discount_type'] = $discountType;
        $calc['totals']['discount_value'] = $discountValue;
        $calc['totals']['discount_amount'] = (int) $discountAmount;
        $calc['totals']['grand_total'] = (int) $grandTotal;
        $calc['totals']['total_selling_price'] = (int) $grandTotal;
        $calc['totals']['selling_price_per_rim'] = $finalPricePerRim;
        $calc['totals']['selling_price_per_pcs'] = $finalPricePerPcs;

        return $calc;
    }

    /**
     * Simpan Estimasi Cetak Offset (AJAX / Form Submit)
     */
    public function store(Request $request)
    {
        return $this->calculate($request);
    }
}


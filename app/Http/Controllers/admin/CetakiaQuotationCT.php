<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\CetakiaApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CetakiaQuotationCT extends Controller
{
    protected CetakiaApiService $cetakiaService;

    public function __construct(CetakiaApiService $cetakiaService)
    {
        $this->cetakiaService = $cetakiaService;
    }

    /**
     * Kirim pembuatan Quotation baru ke Cetakia API (Development Mode)
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_id'       => 'required|string',
            'user_id'           => 'required|string',
            'quotation_date'    => 'required|string',
            'quotation_expired' => 'nullable|string',
            'bill_to'           => 'required|string',
            'ship_to'           => 'required|string',
            'notes'             => 'nullable|string',
            'terms'             => 'nullable|integer',
            'sub_total'         => 'required|numeric|min:0',
            'discount_enabled'  => 'nullable|boolean',
            'discount_type'     => 'nullable|string',
            'discount'          => 'nullable|numeric|min:0',
            'discount_amount'   => 'nullable|numeric|min:0',
            'tax_enabled'       => 'nullable|boolean',
            'tax'               => 'nullable|numeric|min:0',
            'tax_amount'        => 'nullable|numeric|min:0',
            'pph_enabled'       => 'nullable|boolean',
            'pph'               => 'nullable|numeric|min:0',
            'pph_amount'        => 'nullable|numeric|min:0',
            'grand_total'       => 'required|numeric|min:0',
            'items'             => 'required|array|min:1',
            'items.*.item'      => 'required|string',
            'items.*.qty'       => 'required|numeric|min:0.01',
            'items.*.price'     => 'required|numeric|min:0',
            'items.*.amount'    => 'required|numeric|min:0',
            // Metadata opsional untuk sinkronisasi transaksi estimasi lokal
            'customer_name'             => 'nullable|string',
            'customer_phone'            => 'nullable|string',
            'sales_name'                => 'nullable|string',
            'estimation_type'           => 'nullable|string',
            'location_id'               => 'nullable',
            'price_type'                => 'nullable|string',
            'total_hpp'                 => 'nullable|numeric',
            'total_profit'              => 'nullable|numeric',
            'margin_percent'            => 'nullable|numeric',
            'markup_percent'            => 'nullable|numeric',
            'items.*.hpp_per_unit'      => 'nullable|numeric',
            'items.*.total_hpp'         => 'nullable|numeric',
            'items.*.profit'            => 'nullable|numeric',
            'items.*.margin_percent'    => 'nullable|numeric',
            'items.*.markup_percent'    => 'nullable|numeric',
            'items.*.technical_details' => 'nullable',
        ]);

        $subTotal       = round((float) $request->input('sub_total'), 2);
        $discountEnabled = (bool) $request->input('discount_enabled', false);
        $discountType   = $request->input('discount_type', 'percent');
        $discountVal    = round((float) $request->input('discount', 0), 2);
        $discountAmount = $discountEnabled ? round((float) $request->input('discount_amount', 0), 2) : 0.0;

        $taxableBase = max(0, $subTotal - $discountAmount);
        $taxEnabled  = (bool) $request->input('tax_enabled', false);
        $taxRate     = $taxEnabled ? 11.0 : 0.0;
        $taxAmount   = $taxEnabled ? round(($taxableBase * $taxRate) / 100, 2) : 0.0;

        $grandTotal  = round($taxableBase + $taxAmount, 2);

        // Cari atau cocokkan product_id dari katalog Cetakia
        $cetakiaProducts = $this->cetakiaService->getProducts();
        $defaultProductId = !empty($cetakiaProducts[0]['id']) ? $cetakiaProducts[0]['id'] : '019ff93e-3c29-74df-a370-dc4b66d84913';

        // Ambil daftar unit Cetakia untuk resolusi unit_id yang valid
        $cetakiaUnits = $this->cetakiaService->getUnits();
        $unitMap = [];
        foreach ($cetakiaUnits as $u) {
            $uName = strtolower(trim($u['unit_name'] ?? ''));
            $uCode = strtolower(trim($u['unit_code'] ?? ''));
            if ($uName !== '') {
                $unitMap[$uName] = $u['id'];
            }
            if ($uCode !== '') {
                $unitMap[$uCode] = $u['id'];
            }
        }

        $lembarUnitId = $unitMap['lembar'] ?? $unitMap['lbr'] ?? '019b8a5f-486a-7779-a895-9cbfce459ae8';
        $pcsUnitId    = $unitMap['pcs'] ?? $unitMap['pc'] ?? '019b8a5f-0400-77c0-aeac-3f9eeaed8889';
        $rimUnitId    = $unitMap['rim'] ?? '019b8a5f-2942-7383-be6b-a7984193d8ab';

        $items = [];
        foreach ($request->input('items', []) as $it) {
            $prodId = $it['product_id'] ?? null;

            // Jika belum ada product_id spesifik, cari berdasarkan kemiripan nama material
            if (empty($prodId) && !empty($it['material_name'])) {
                $matName = strtolower(trim($it['material_name']));
                foreach ($cetakiaProducts as $cp) {
                    $cpName = strtolower(trim($cp['product_name']));
                    if (str_contains($matName, $cpName) || str_contains($cpName, $matName)) {
                        $prodId = $cp['id'];
                        break;
                    }
                }
            }

            if (empty($prodId)) {
                $prodId = $defaultProductId;
            }

            // Resolusi unit_id sesuai aturan bisnis & API kontrak Cetakia:
            // 1. Digital Print A3+ -> Lembar (lbr)
            // 2. Large Format -> Pcs (pcs)
            // 3. Brosur (Offset) -> Rim (rim)
            // 4. Kalender -> Pcs (pcs)
            // 5. Merchandise:
            //    - Buku Nota (NCR & HVS) -> Rim (rim)
            //    - Tali Lanyard, Papan Nama Dada, Plakat, Mug, Kartu PVC, Akrilik UV -> Pcs (pcs)
            $unitId = null;

            // Tahap 1: Jika client mengirim unit_id langsung, validasi kecocokan dengan daftar unit Cetakia
            if (!empty($it['unit_id'])) {
                foreach ($cetakiaUnits as $u) {
                    if ($u['id'] === $it['unit_id']) {
                        $unitId = $u['id'];
                        break;
                    }
                }
            }

            // Tahap 2: Jika belum ada unit_id, cek dari unit_name, unit_code, atau unit string
            if (empty($unitId) && (!empty($it['unit_name']) || !empty($it['unit_code']) || !empty($it['unit']))) {
                $targetUnit = strtolower(trim((string) ($it['unit_name'] ?? $it['unit_code'] ?? $it['unit'])));
                if (isset($unitMap[$targetUnit])) {
                    $unitId = $unitMap[$targetUnit];
                }
            }

            // Tahap 3: Pemetaan cerdas otomatis berdasarkan konteks nama item, kategori, atau deskripsi produk
            if (empty($unitId)) {
                $itemContext = strtolower(($it['item'] ?? '') . ' ' . ($it['category'] ?? '') . ' ' . ($it['description'] ?? ''));

                // Brosur / Offset / Buku Nota -> Rim
                if (str_contains($itemContext, 'brosur') || str_contains($itemContext, 'offset') || str_contains($itemContext, 'buku nota') || str_contains($itemContext, 'nota') || str_contains($itemContext, 'rim')) {
                    $unitId = $rimUnitId;
                }
                // Digital Print A3+ -> Lembar
                elseif (str_contains($itemContext, 'a3+') || str_contains($itemContext, 'digital print') || str_contains($itemContext, 'lembar')) {
                    $unitId = $lembarUnitId;
                }
                // Large Format, Kalender, & Merchandise lain -> Pcs
                elseif (str_contains($itemContext, 'kalender') || str_contains($itemContext, 'large format') || str_contains($itemContext, 'banner') || str_contains($itemContext, 'display') || str_contains($itemContext, 'lanyard') || str_contains($itemContext, 'nama dada') || str_contains($itemContext, 'name tag') || str_contains($itemContext, 'plakat') || str_contains($itemContext, 'mug') || str_contains($itemContext, 'pvc') || str_contains($itemContext, 'akrilik')) {
                    $unitId = $pcsUnitId;
                }
            }

            // Tahap 4: Fallback ke unit_id bawaan produk Cetakia jika ada
            if (empty($unitId)) {
                foreach ($cetakiaProducts as $cp) {
                    if ($cp['id'] === $prodId && !empty($cp['unit_id'])) {
                        $unitId = $cp['unit_id'];
                        break;
                    }
                }
            }

            // Tahap 5: Default absolut jika tidak terdefinisi adalah Pcs
            if (empty($unitId)) {
                $unitId = $pcsUnitId;
            }

            $rawDesc = (string) ($it['description'] ?? '');
            // Format deskripsi ramah nama file produksi percetakan:
            // 1. Ganti pemisah titik tengah (·) menjadi underscore (_)
            $clean = preg_replace('/\s*·\s*/u', '_', $rawDesc);
            // 2. Ganti simbol kali (×) jadi x ascii
            $clean = str_replace('×', 'x', $clean);
            // 3. Bersihkan karakter terlarang file system (\ / * ? " < > |)
            $clean = preg_replace('/[\\\\\\/*?"<>|]/', '', $clean);
            // 4. Hapus prefix label seperti "Bahan:", "Cetak:", "Ukuran:", dll. agar langsung berisi nilai spesifikasi
            $parts = explode('_', $clean);
            $cleanedParts = [];
            foreach ($parts as $p) {
                $p = preg_replace('/^(Bahan|Cetak|Ukuran|Kuantitas|Kuantiti|Dudukan|Jilid|Finishing|Laminasi|Cutting)\s*[:：\-]?\s*/i', '', trim($p));
                $p = str_replace([':', '：'], '', $p);
                $p = trim($p);
                if ($p !== '') {
                    $cleanedParts[] = $p;
                }
            }
            $cleanDesc = implode('_', $cleanedParts);
            $cleanDesc = preg_replace('/\s*_\s*/', '_', $cleanDesc);
            $cleanDesc = preg_replace('/_+/', '_', $cleanDesc);
            $cleanDesc = trim($cleanDesc, " _\t\n\r\0\x0B");

            $items[] = [
                'type'        => $it['type'] ?? 'product',
                'product_id'  => $prodId,
                'item'        => $it['item'],
                'description' => $cleanDesc,
                'qty'         => (float) $it['qty'],
                'price'       => (float) $it['price'],
                'unit_id'     => $unitId,
                'markup'      => (float) ($it['markup'] ?? 0),
                'amount'      => round((float) $it['qty'] * (float) $it['price'], 2),
            ];
        }

        // Sinkronisasi integritas matematika: pastikan Sub Total & Grand Total selalu tepat sama dengan SUM(item.amount)
        $calculatedSubTotal = array_sum(array_column($items, 'amount'));
        if ($calculatedSubTotal > 0) {
            $subTotal = round($calculatedSubTotal, 2);
            if ($discountEnabled) {
                if ($discountType === 'percent' || $discountType === 'percentage') {
                    $discountAmount = round(($subTotal * $discountVal) / 100, 2);
                } else {
                    $discountAmount = min($subTotal, $discountAmount);
                }
            } else {
                $discountAmount = 0.0;
            }
            $taxableBase = max(0, $subTotal - $discountAmount);
            $taxAmount   = $taxEnabled ? round(($taxableBase * $taxRate) / 100, 2) : 0.0;
            $grandTotal  = round($taxableBase + $taxAmount, 2);
        }

        // Format tanggal ISO-8601
        $qDateRaw = $request->input('quotation_date');
        if (empty($qDateRaw)) {
            $qDateRaw = date('Y-m-d');
        }
        $qExpRaw  = $request->input('quotation_expired');
        if (empty($qExpRaw)) {
            $qExpRaw = date('Y-m-d', strtotime('+30 days'));
        }
        $qDate    = date('Y-m-d\TH:i:s.000\Z', strtotime($qDateRaw));
        $qExp     = date('Y-m-d\TH:i:s.000\Z', strtotime($qExpRaw . ' 23:59:59'));

        $payload = [
            'customer_id'       => $request->input('customer_id'),
            'user_id'           => $request->input('user_id'),
            'promotion_id'      => null,
            'quotation_date'    => $qDate,
            'quotation_expired' => $qExp,
            'bill_to'           => $request->input('bill_to'),
            'ship_to'           => $request->input('ship_to'),
            'notes'             => (string) ($request->input('notes') ?? ''),
            'terms'             => (int) ($request->input('terms') ?? 0),
            'sub_total'         => $subTotal,
            'discount_enabled'  => $discountEnabled,
            'discount_type'     => $discountType === 'percentage' ? 'percent' : ($discountType === 'nominal' ? 'nominal' : 'percent'),
            'discount'          => $discountVal,
            'discount_amount'   => $discountAmount,
            'tax_enabled'       => $taxEnabled,
            'tax'               => $taxRate,
            'tax_amount'        => $taxAmount,
            'pph_enabled'       => false,
            'pph'               => 0.0,
            'pph_amount'        => 0.0,
            'grand_total'       => $grandTotal,
            'status'            => 'draft',
            'items'             => $items,
        ];

        Log::info('CetakiaQuotationCT: Dispatching payload to Cetakia dev mode', [
            'customer_id' => $payload['customer_id'],
            'user_id'     => $payload['user_id'],
            'grand_total' => $payload['grand_total'],
        ]);

        $result = $this->cetakiaService->createQuotation($payload);

        if (!empty($result['data']['quotation']['quotation_code'])) {
            $result['data']['quotation_code'] = $result['data']['quotation']['quotation_code'];
        }

        // SIMPAN TRANSAKSI ESTIMASI KE DATABASE LOKAL JIKA DISPATCH KE CETAKIA BERHASIL
        if (!empty($result['success']) && !empty($result['data'])) {
            try {
                $cetakiaQuotationCode = $result['data']['quotation']['quotation_code'] 
                    ?? $result['data']['quotation_code'] 
                    ?? null;
                $cetakiaQuotationId = $result['data']['quotation']['id'] ?? null;

                // Resolusi Nama Pelanggan
                $customerName = trim((string) $request->input('customer_name'));
                if (empty($customerName)) {
                    $customerName = 'Pelanggan Cetakia';
                }

                // Resolusi Nama Sales PIC
                $salesName = trim((string) $request->input('sales_name'));
                if (empty($salesName)) {
                    $salesList = $this->cetakiaService->getSalesUsers();
                    foreach ($salesList as $s) {
                        if (($s['id'] ?? '') === $request->input('user_id')) {
                            $salesName = $s['name'] ?? $s['username'] ?? '';
                            break;
                        }
                    }
                }
                if (empty($salesName)) {
                    $salesName = 'Sales Cetakia';
                }

                // Resolusi Lokasi Cabang
                $locationId = $request->input('location_id');
                if ($locationId) {
                    $locationId = (int) $locationId;
                    if (!DB::table('m_locations')->where('id', $locationId)->exists()) {
                        $locationId = null;
                    }
                } else {
                    $locationId = null;
                }

                // Generate Kode Estimasi Lokal Unik: EST-{TYPE}-{YYYYMM}-{SEQ}
                $estType = (string) $request->input('estimation_type', 'a3_plus');
                $typeCodeMap = [
                    'a3_plus'      => 'A3',
                    'large_format' => 'LF',
                    'offset'       => 'OF',
                    'calendar'     => 'KL',
                    'merchandise'  => 'MC',
                ];
                $typePrefix = $typeCodeMap[$estType] ?? 'EST';
                $ymPrefix = 'EST-' . $typePrefix . '-' . date('Ym');

                DB::beginTransaction();

                $latest = DB::table('t_estimations')
                    ->where('estimation_code', 'like', $ymPrefix . '-%')
                    ->orderBy('id', 'desc')
                    ->lockForUpdate()
                    ->first();

                $seq = 1;
                if ($latest && preg_match('/-(\d+)$/', $latest->estimation_code, $matches)) {
                    $seq = (int) $matches[1] + 1;
                }
                $estimationCode = sprintf('%s-%04d', $ymPrefix, $seq);

                $currentUser = Auth::user();
                $authorName = $currentUser ? ($currentUser->name ?? $currentUser->username ?? 'User') : 'System';

                $estimationId = DB::table('t_estimations')->insertGetId([
                    'estimation_code'         => $estimationCode,
                    'cetakia_quotation_code'  => $cetakiaQuotationCode,
                    'cetakia_quotation_id'    => $cetakiaQuotationId,
                    'estimation_type'         => $estType,
                    'location_id'             => $locationId,
                    'customer_id'             => (string) $request->input('customer_id'),
                    'customer_name'           => $customerName,
                    'customer_phone'          => $request->input('customer_phone'),
                    'sales_user_id'           => (string) $request->input('user_id'),
                    'sales_name'              => $salesName,
                    'price_type'              => (string) $request->input('price_type', 'general'),
                    'quotation_date'          => $qDateRaw,
                    'quotation_expired'       => $qExpRaw,
                    'terms'                   => (int) ($request->input('terms') ?? 0),
                    'bill_to'                 => $request->input('bill_to'),
                    'ship_to'                 => $request->input('ship_to'),
                    'notes'                   => $request->input('notes'),
                    'subtotal'                => $subTotal,
                    'discount_enabled'        => $discountEnabled,
                    'discount_type'           => $discountType,
                    'discount_value'          => $discountVal,
                    'discount_amount'         => $discountAmount,
                    'tax_enabled'             => $taxEnabled,
                    'tax_rate'                => $taxRate,
                    'tax_amount'              => $taxAmount,
                    'grand_total'             => $grandTotal,
                    'total_hpp'               => (float) $request->input('total_hpp', 0),
                    'total_profit'            => (float) $request->input('total_profit', 0),
                    'margin_percent'          => $request->input('margin_percent') !== null ? (float) $request->input('margin_percent') : null,
                    'markup_percent'          => $request->input('markup_percent') !== null ? (float) $request->input('markup_percent') : null,
                    'status'                  => 'sent_to_cetakia',
                    'created_by'              => $authorName,
                    'updated_by'              => $authorName,
                    'created_at'              => now(),
                    'updated_at'              => now(),
                ]);

                // Insert Items
                $rawItems = $request->input('items', []);
                foreach ($rawItems as $it) {
                    $itPrice = (float) ($it['price'] ?? 0);
                    $itQty   = (float) ($it['qty'] ?? 1);
                    $itAmount = round($itQty * $itPrice, 2);

                    $techDetails = $it['technical_details'] ?? null;
                    if (empty($techDetails)) {
                        $techDetails = null;
                    } elseif (is_array($techDetails)) {
                        $techDetails = json_encode($techDetails);
                    }

                    DB::table('t_estimation_items')->insert([
                        'estimation_id'     => $estimationId,
                        'item_name'         => (string) ($it['item'] ?? 'Digital Print A3+'),
                        'item_category'     => (string) ($it['category'] ?? 'Digital Print A3+'),
                        'description'       => (string) ($it['description'] ?? ''),
                        'qty'               => $itQty,
                        'unit_id'           => $it['unit_id'] ?? null,
                        'unit_name'         => (string) ($it['unit_name'] ?? 'Lembar'),
                        'unit_price'        => $itPrice,
                        'subtotal'          => $itAmount,
                        'hpp_per_unit'      => (float) ($it['hpp_per_unit'] ?? 0),
                        'total_hpp'         => (float) ($it['total_hpp'] ?? 0),
                        'profit'            => (float) ($it['profit'] ?? 0),
                        'margin_percent'    => isset($it['margin_percent']) ? (float) $it['margin_percent'] : null,
                        'markup_percent'    => isset($it['markup_percent']) ? (float) $it['markup_percent'] : null,
                        'technical_details' => $techDetails,
                        'created_by'        => $authorName,
                        'updated_by'        => $authorName,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                }

                DB::commit();

                $result['data']['local_estimation_id'] = $estimationId;
                $result['data']['local_estimation_code'] = $estimationCode;

                Log::info('CetakiaQuotationCT: Berhasil menyimpan transaksi estimasi ke database lokal', [
                    'estimation_id'   => $estimationId,
                    'estimation_code' => $estimationCode,
                    'quotation_code'  => $cetakiaQuotationCode,
                ]);
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('CetakiaQuotationCT: Gagal menyimpan transaksi estimasi lokal', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        return response()->json($result, $result['statusCode'] ?? 200);
    }

    /**
     * Endpoint JSON untuk Select2 Infinite Scroll & Pencarian Pelanggan Cetakia
     */
    public function getCustomersAjax(Request $request): JsonResponse
    {
        $page    = max(1, (int) $request->input('page', 1));
        $limit   = max(1, min(100, (int) $request->input('limit', 50)));
        $keyword = (string) ($request->input('keyword') ?? $request->input('q') ?? $request->input('search') ?? '');

        $data = $this->cetakiaService->getCustomersPaginated($page, $limit, $keyword);

        return response()->json($data);
    }
}

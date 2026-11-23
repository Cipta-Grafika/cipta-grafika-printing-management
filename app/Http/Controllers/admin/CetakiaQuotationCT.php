<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Services\CetakiaApiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
                'unit_id'     => !empty($it['unit_id']) ? $it['unit_id'] : null,
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

        return response()->json($result, $result['statusCode'] ?? 200);
    }
}

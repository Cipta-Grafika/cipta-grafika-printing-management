<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class CalendarCalculationService
{
    /**
     * Machine rate defaults for Offset
     */
    protected array $machineRates = [
        'SM52' => [
            'name'                => 'Heidelberg Speedmaster 52 (SM52)',
            'max_paper'           => '37x52',
            'max_print'           => '35x50',
            'plate_price_per_w'   => 16000.0,
            'click_price_per_w'   => 15.0,
            'min_print_per_w'     => 65000.0,
            'min_qty'             => 1000,
        ],
        'Oliver66' => [
            'name'                => 'Sakurai Oliver 66',
            'max_paper'           => '66x48',
            'max_print'           => '64x46',
            'plate_price_per_w'   => 32000.0,
            'click_price_per_w'   => 20.0,
            'min_print_per_w'     => 80000.0,
            'min_qty'             => 1000,
        ],
        'SM72' => [
            'name'                => 'Heidelberg Speedmaster 72 (SM72)',
            'max_paper'           => '52x72',
            'max_print'           => '50x70',
            'plate_price_per_w'   => 35000.0,
            'click_price_per_w'   => 25.0,
            'min_print_per_w'     => 125000.0,
            'min_qty'             => 2500,
        ],
    ];

    /**
     * Main calculation entry point
     *
     * @param array $params
     *  - product_id: int (from m_calendar_products)
     *  - material_id: int (from m_calendar_materials)
     *  - qty: int
     *  - leaf_count: int (default 7 for wall, 13 for desk)
     *  - lamination: string ('none', 'glossy', 'doff')
     *  - support_type: string ('printing_fc', 'linen_hitam', 'hardcover_polos') -> for desk only
     *  - board_type: string ('boot_30', 'boot_40') -> for desk only
     * @return array
     */
    public function calculate(array $params): array
    {
        $productId = (int) ($params['product_id'] ?? 0);
        $materialId = (int) ($params['material_id'] ?? 0);
        $qty = max(1, (int) ($params['qty'] ?? 100));
        $leafCount = max(1, (int) ($params['leaf_count'] ?? 7));
        $lamination = strtolower(trim((string) ($params['lamination'] ?? 'none')));
        $supportType = strtolower(trim((string) ($params['support_type'] ?? 'printing_fc')));
        $boardType = strtolower(trim((string) ($params['board_type'] ?? 'boot_30')));

        // 1. Fetch Product
        $product = DB::table('m_calendar_products')->where('id', $productId)->first();
        if (!$product) {
            throw new \Exception('Produk kalender tidak ditemukan.');
        }

        // 2. Fetch Material
        $material = DB::table('m_calendar_materials')->where('id', $materialId)->first();
        if (!$material) {
            throw new \Exception('Material kalender tidak ditemukan.');
        }

        // 3. Determine Markup Rule
        $pricingRule = $this->getPricingRule($qty);
        $markupRate = $pricingRule ? ((float) $pricingRule->markup_percent / 100.0) : $this->getFallbackMarkupRate($qty);
        $roundingVal = $pricingRule ? (int) $pricingRule->rounding_value : 500;
        if ($roundingVal <= 0) {
            $roundingVal = 500;
        }

        // 4. Branch by Calendar Type
        $calendarType = strtolower(trim($product->calendar_type));
        if ($calendarType === 'meja') {
            return $this->calculateDeskCalendar($product, $material, $qty, $leafCount, $lamination, $supportType, $boardType, $markupRate, $roundingVal);
        } else {
            return $this->calculateWallCalendar($product, $material, $qty, $leafCount, $lamination, $markupRate, $roundingVal);
        }
    }

    /**
     * Hitung Kalender Dinding (Wall Calendar)
     */
    protected function calculateWallCalendar(
        object $product,
        object $material,
        int $qty,
        int $leafCount,
        string $lamination,
        float $markupRate,
        int $roundingVal
    ): array {
        $width = (float) $product->final_width;
        $height = (float) $product->final_height;
        $category = strtoupper(trim($material->category));

        // 1. Finishing Per Pcs (Otomatis: Jepit Kaleng atau Spiral Wire-O + Hanger)
        $finishing = $this->determineWallFinishing($category, $width);
        $finishCostPerPcs = $finishing['cost_per_pcs'];
        $finishTotal = $finishCostPerPcs * $qty;

        // 2. Digital Route
        $digitalAvailable = (bool) $product->digital_available && ($width <= 31.0 && $height <= 47.0);
        $digital = [
            'available'           => $digitalAvailable,
            'reason_unavailable' => $digitalAvailable ? null : 'Ukuran kalender melebihi area cetak mesin Digital A3+ (maks 31 x 47 cm).',
            'total_sheets_a3'     => 0,
            'print_cost'          => 0,
            'lamination_cost'     => 0,
            'finishing_cost'      => 0,
            'hpp_total'           => 0,
            'hpp_per_pcs'         => 0,
            'selling_price_pcs'   => 0,
            'total_offer'         => 0,
        ];

        if ($digitalAvailable) {
            $totalSheetsA3 = $qty * $leafCount;
            $costPrint1 = (float) $material->digital_cost_print_1_side;
            $printTotal = $totalSheetsA3 * $costPrint1;

            $lamRate = 0.0;
            if ($lamination !== 'none') {
                $lamRate = $this->getComponentPrice('LAM-DIG-' . strtoupper($lamination) . '-1', 816.70);
            }
            $lamTotal = $totalSheetsA3 * $lamRate;

            $hppDigitalTotal = $printTotal + $lamTotal + $finishTotal;
            $hppDigitalPcs = $hppDigitalTotal / $qty;
            $sellingPricePcs = $this->ceilingRound($hppDigitalPcs * (1.0 + $markupRate), $roundingVal);
            $totalOffer = $sellingPricePcs * $qty;

            $digital = [
                'available'           => true,
                'reason_unavailable' => null,
                'total_sheets_a3'     => $totalSheetsA3,
                'cost_print_per_sheet'=> $costPrint1,
                'print_cost'          => round($printTotal, 2),
                'lamination_rate'     => $lamRate,
                'lamination_cost'     => round($lamTotal, 2),
                'finishing_cost'      => round($finishTotal, 2),
                'hpp_total'           => round($hppDigitalTotal, 2),
                'hpp_per_pcs'         => round($hppDigitalPcs, 2),
                'selling_price_pcs'   => $sellingPricePcs,
                'total_offer'         => $totalOffer,
            ];
        }

        // 3. Offset Route
        $machineKey = trim($product->offset_machine ?: 'SM52');
        $machine = $this->machineRates[$machineKey] ?? $this->machineRates['SM52'];

        $forms = $leafCount; // 1 calendar page per form (OffsetUp = 1)
        $insheet = (int) $this->getComponentPrice('PARAM-INSHEET', 150);
        $pressQtyPerForm = $qty + $insheet;
        $totalPressSheets = $forms * $pressQtyPerForm;

        // Plano Yield
        $planoYield = 4;
        if ($width > 35.0 || $height > 50.0) {
            $planoYield = 1;
        }

        // Plano Price
        $planoPrice = $this->resolvePlanoPrice($material, $product->size_label, $product->recommended_plano);
        $planoNeeded = (int) ceil($totalPressSheets / $planoYield);
        $paperCost = $planoNeeded * $planoPrice;

        // Plat Cost: forms * 4 colors * plate rate
        $platCost = $forms * 4 * (float) $machine['plate_price_per_w'];

        // Print Cost: forms * 4 colors * max(pressQty * clickRate, minPrintRate)
        $clickRate = (float) $machine['click_price_per_w'];
        $minPrintRate = (float) $machine['min_print_per_w'];
        $printCostPerColor = max($pressQtyPerForm * $clickRate, $minPrintRate);
        $printCost = $forms * 4 * $printCostPerColor;

        // Offset Laminasi Cost
        $lamCostOffset = 0.0;
        if ($lamination !== 'none') {
            $pressW = (float) $product->offset_press_width;
            $pressH = (float) $product->offset_press_height;
            $lamRateCm2 = (float) $this->getComponentPrice('LAM-OFF-' . strtoupper($lamination), 0.18);
            $minLam = $lamination === 'doff' ? 180000.0 : 150000.0;
            $lamCostRaw = $pressW * $pressH * $totalPressSheets * $lamRateCm2;
            $lamCostOffset = max($lamCostRaw, $minLam);
        }

        $hppOffsetTotal = $paperCost + $platCost + $printCost + $lamCostOffset + $finishTotal;
        $hppOffsetPcs = $hppOffsetTotal / $qty;
        $sellingPriceOffsetPcs = $this->ceilingRound($hppOffsetPcs * (1.0 + $markupRate), $roundingVal);
        $totalOfferOffset = $sellingPriceOffsetPcs * $qty;

        $offset = [
            'machine_key'         => $machineKey,
            'machine_name'        => $machine['name'],
            'forms_count'         => $forms,
            'insheet'             => $insheet,
            'press_qty_per_form'  => $pressQtyPerForm,
            'total_press_sheets'  => $totalPressSheets,
            'plano_yield'         => $planoYield,
            'plano_needed'        => $planoNeeded,
            'plano_price'         => $planoPrice,
            'paper_cost'          => round($paperCost, 2),
            'plat_cost'           => round($platCost, 2),
            'print_cost'          => round($printCost, 2),
            'lamination_cost'     => round($lamCostOffset, 2),
            'finishing_cost'      => round($finishTotal, 2),
            'hpp_total'           => round($hppOffsetTotal, 2),
            'hpp_per_pcs'         => round($hppOffsetPcs, 2),
            'selling_price_pcs'   => $sellingPriceOffsetPcs,
            'total_offer'         => $totalOfferOffset,
        ];

        // 4. Recommendation Comparison
        if (!$digitalAvailable) {
            $recommendedRoute = 'offset';
            $recommendationLabel = 'OFFSET SAJA';
            $recommendationReason = 'Ukuran kalender melebihi batas area cetak Digital A3+. Wajib menggunakan Mesin Offset (' . $machineKey . ').';
        } elseif ($digital['hpp_per_pcs'] < $offset['hpp_per_pcs']) {
            $recommendedRoute = 'digital';
            $recommendationLabel = 'DIGITAL LEBIH EFISIEN';
            $diffPcs = $offset['hpp_per_pcs'] - $digital['hpp_per_pcs'];
            $recommendationReason = "Digital A3+ lebih hemat Rp " . number_format($diffPcs, 0, ',', '.') . " / pcs dibanding Offset.";
        } elseif ($offset['hpp_per_pcs'] < $digital['hpp_per_pcs']) {
            $recommendedRoute = 'offset';
            $recommendationLabel = 'OFFSET LEBIH EFISIEN';
            $diffPcs = $digital['hpp_per_pcs'] - $offset['hpp_per_pcs'];
            $recommendationReason = "Mesin Offset ({$machineKey}) lebih hemat Rp " . number_format($diffPcs, 0, ',', '.') . " / pcs dibanding Digital.";
        } else {
            $recommendedRoute = 'digital';
            $recommendationLabel = 'BIAYA SAMA';
            $recommendationReason = 'Biaya Digital dan Offset seimbang.';
        }

        $recommendedPricePcs = $recommendedRoute === 'digital' ? $digital['selling_price_pcs'] : $offset['selling_price_pcs'];
        $recommendedTotalOffer = $recommendedPricePcs * $qty;

        return [
            'calendar_type'          => 'dinding',
            'product_name'           => $product->product_name,
            'size_label'             => $product->size_label,
            'orientation'            => $product->orientation,
            'material_name'          => $material->material_name,
            'leaf_count'             => $leafCount,
            'lamination'             => $lamination,
            'qty'                    => $qty,
            'markup_percent'         => $markupRate * 100,
            'finishing_detail'       => $finishing,
            'digital'                => $digital,
            'offset'                 => $offset,
            'recommended_route'      => $recommendedRoute,
            'recommendation_label'   => $recommendationLabel,
            'recommendation_reason'  => $recommendationReason,
            'recommended_price_pcs'  => $recommendedPricePcs,
            'recommended_total_offer'=> $recommendedTotalOffer,
            'share_texts'            => $this->buildShareTexts(
                'Dinding',
                $product->size_label,
                $product->orientation,
                $material->material_name,
                $leafCount,
                $lamination,
                $finishing['name'],
                $qty,
                $recommendedPricePcs,
                $recommendedTotalOffer,
                $recommendedRoute,
                $machineKey
            ),
        ];
    }

    /**
     * Hitung Kalender Meja (Desk Calendar)
     */
    protected function calculateDeskCalendar(
        object $product,
        object $material,
        int $qty,
        int $leafCount,
        string $lamination,
        string $supportType,
        string $boardType,
        float $markupRate,
        int $roundingVal
    ): array {
        $width = (float) $product->final_width;
        $height = (float) $product->final_height;
        $orientation = strtolower(trim($product->orientation));
        $materialName = $material->material_name;

        // 1. Cost Penopang Meja (Stand Support)
        $supportDetail = $this->calculateDeskSupport($product->size_label, $orientation, $supportType, $boardType);
        $supportCostPerPcs = $supportDetail['cost_per_pcs'];
        $supportTotal = $supportCostPerPcs * $qty;

        // 2. Cost Spiral Wire-O & Finishing Rakit Meja
        $bindingWidth = $orientation === 'landscape' ? max($width, $height) : min($width, $height);
        $spiralRateCm = (float) $this->getComponentPrice('ACC-SPIRAL', 150.0);
        $spiralCostPcs = $bindingWidth * $spiralRateCm;
        $assemblyCostPcs = (float) $this->getComponentPrice('FIN-MEJA-GABUNG', 2000.0);
        $spiralFinCostPcs = $spiralCostPcs + $assemblyCostPcs;
        $spiralFinTotal = $spiralFinCostPcs * $qty;

        // 3. Digital Route (Kalender Meja selalu 2 sisi)
        $digitalUp = (int) $product->digital_up;
        if ($digitalUp <= 0) {
            $digitalUp = 4;
        }
        $totalLembarJadi = $qty * $leafCount;
        $kebutuhanA3 = (int) ceil($totalLembarJadi / $digitalUp);
        $costPrint2 = (float) $material->digital_cost_print_2_side;
        $printTotalDigital = $kebutuhanA3 * $costPrint2;

        $lamRate2 = 0.0;
        if ($lamination !== 'none') {
            $lamRate2 = $this->getComponentPrice('LAM-DIG-' . strtoupper($lamination) . '-2', 1633.0);
        }
        $lamTotalDigital = $kebutuhanA3 * $lamRate2;
        $costIsiLamDigital = $printTotalDigital + $lamTotalDigital;

        $hppDigitalTotal = $costIsiLamDigital + $supportTotal + $spiralFinTotal;
        $hppDigitalPcs = $hppDigitalTotal / $qty;
        $sellingPriceDigitalPcs = $this->ceilingRound($hppDigitalPcs * (1.0 + $markupRate), $roundingVal);
        $totalOfferDigital = $sellingPriceDigitalPcs * $qty;

        $digital = [
            'available'           => true,
            'reason_unavailable' => null,
            'digital_up'          => $digitalUp,
            'total_lembar_jadi'   => $totalLembarJadi,
            'kebutuhan_a3'        => $kebutuhanA3,
            'cost_print_per_sheet'=> $costPrint2,
            'print_cost'          => round($printTotalDigital, 2),
            'lamination_rate'     => $lamRate2,
            'lamination_cost'     => round($lamTotalDigital, 2),
            'cost_isi_lam_pcs'    => round($costIsiLamDigital / $qty, 2),
            'support_cost_pcs'    => round($supportCostPerPcs, 2),
            'support_total'       => round($supportTotal, 2),
            'spiral_fin_cost_pcs' => round($spiralFinCostPcs, 2),
            'spiral_fin_total'    => round($spiralFinTotal, 2),
            'hpp_total'           => round($hppDigitalTotal, 2),
            'hpp_per_pcs'         => round($hppDigitalPcs, 2),
            'selling_price_pcs'   => $sellingPriceDigitalPcs,
            'total_offer'         => $totalOfferDigital,
        ];

        // 4. Offset Route (SM52 multi-form calculation)
        $offsetUp = (int) $product->offset_up;
        if (str_contains($materialName, 'Blues White') && !empty($product->offset_up_bw)) {
            $offsetUp = (int) $product->offset_up_bw;
        }
        if ($offsetUp <= 0) {
            $offsetUp = 4;
        }

        $fullForms = intdiv($leafCount, $offsetUp);
        $remainder = $leafCount % $offsetUp;

        $insheet = (int) $this->getComponentPrice('PARAM-INSHEET', 150);
        $fullPressQty = $qty + $insheet;

        if ($remainder === 0) {
            $remRepeat = 0;
            $remPressQty = 0;
        } elseif ($remainder === 1) {
            $remRepeat = $offsetUp / 2.0;
            $remPressQty = (int) ceil($qty / $remRepeat) + $insheet;
        } else {
            $remRepeat = max(1, intdiv($offsetUp, $remainder));
            $remPressQty = (int) ceil($qty / $remRepeat) + $insheet;
        }

        $fullSets = $fullForms * 2; // 2 sides front & back
        $remSets = ($remainder === 0 ? 0 : ($remainder === 1 ? 1 : 2));

        $platCost = ($fullSets + $remSets) * 4 * 16000.0; // SM52 plate rate

        $fullPrintCost = $fullSets * 4 * max($fullPressQty * 15.0, 65000.0);
        $remClicks = $remainder === 1 ? ($remPressQty * 2) : $remPressQty;
        $remPrintCost = $remSets === 0 ? 0 : ($remSets * 4 * max($remClicks * 15.0, 65000.0));
        $printCostOffset = $fullPrintCost + $remPrintCost;

        // Paper Plano
        $fullPressSheets = $fullForms * $fullPressQty;
        $remPressSheets = $remSets === 0 ? 0 : $remPressQty;
        $totalPressSheets = $fullPressSheets + $remPressSheets;
        $planoNeeded = (int) ceil($totalPressSheets / 4.0); // 4 press sheets per plano SM52

        $planoPrice = $this->resolvePlanoPriceDesk($material, $product->size_label);
        $paperCost = $planoNeeded * $planoPrice;

        // Offset Laminasi (2 Sisi)
        $lamCostOffset = 0.0;
        if ($lamination !== 'none') {
            $pressW = ($product->size_label === '24 x 17 cm' || str_contains($materialName, 'Blues White')) ? 52.0 : 45.0;
            $pressH = ($product->size_label === '24 x 17 cm' || str_contains($materialName, 'Blues White')) ? 37.0 : 32.5;
            $lamRateCm2 = (float) $this->getComponentPrice('LAM-OFF-' . strtoupper($lamination), 0.18);
            $minLam = $lamination === 'doff' ? 180000.0 : 150000.0;
            $lamCostRaw = $pressW * $pressH * $totalPressSheets * 2.0 * $lamRateCm2;
            $lamCostOffset = max($lamCostRaw, $minLam);
        }

        $costIsiLamOffset = $paperCost + $platCost + $printCostOffset + $lamCostOffset;
        $hppOffsetTotal = $costIsiLamOffset + $supportTotal + $spiralFinTotal;
        $hppOffsetPcs = $hppOffsetTotal / $qty;
        $sellingPriceOffsetPcs = $this->ceilingRound($hppOffsetPcs * (1.0 + $markupRate), $roundingVal);
        $totalOfferOffset = $sellingPriceOffsetPcs * $qty;

        $offset = [
            'machine_key'         => 'SM52',
            'machine_name'        => 'Heidelberg Speedmaster 52 (SM52)',
            'offset_up'           => $offsetUp,
            'full_forms'          => $fullForms,
            'remainder_pages'     => $remainder,
            'full_press_qty'      => $fullPressQty,
            'rem_press_qty'       => $remPressQty,
            'full_sets'           => $fullSets,
            'rem_sets'            => $remSets,
            'total_press_sheets'  => $totalPressSheets,
            'plano_needed'        => $planoNeeded,
            'plano_price'         => $planoPrice,
            'paper_cost'          => round($paperCost, 2),
            'plat_cost'           => round($platCost, 2),
            'print_cost'          => round($printCostOffset, 2),
            'lamination_cost'     => round($lamCostOffset, 2),
            'cost_isi_lam_pcs'    => round($costIsiLamOffset / $qty, 2),
            'support_cost_pcs'    => round($supportCostPerPcs, 2),
            'support_total'       => round($supportTotal, 2),
            'spiral_fin_cost_pcs' => round($spiralFinCostPcs, 2),
            'spiral_fin_total'    => round($spiralFinTotal, 2),
            'hpp_total'           => round($hppOffsetTotal, 2),
            'hpp_per_pcs'         => round($hppOffsetPcs, 2),
            'selling_price_pcs'   => $sellingPriceOffsetPcs,
            'total_offer'         => $totalOfferOffset,
        ];

        // 5. Recommendation
        if ($digital['hpp_per_pcs'] < $offset['hpp_per_pcs']) {
            $recommendedRoute = 'digital';
            $recommendationLabel = 'DIGITAL LEBIH EFISIEN';
            $diffPcs = $offset['hpp_per_pcs'] - $digital['hpp_per_pcs'];
            $recommendationReason = "Digital A3+ lebih hemat Rp " . number_format($diffPcs, 0, ',', '.') . " / pcs dibanding Offset SM52.";
        } elseif ($offset['hpp_per_pcs'] < $digital['hpp_per_pcs']) {
            $recommendedRoute = 'offset';
            $recommendationLabel = 'OFFSET SM52 LEBIH EFISIEN';
            $diffPcs = $digital['hpp_per_pcs'] - $offset['hpp_per_pcs'];
            $recommendationReason = "Offset SM52 lebih hemat Rp " . number_format($diffPcs, 0, ',', '.') . " / pcs dibanding Digital A3+.";
        } else {
            $recommendedRoute = 'digital';
            $recommendationLabel = 'BIAYA SAMA';
            $recommendationReason = 'Biaya Digital dan Offset SM52 seimbang.';
        }

        $recommendedPricePcs = $recommendedRoute === 'digital' ? $digital['selling_price_pcs'] : $offset['selling_price_pcs'];
        $recommendedTotalOffer = $recommendedPricePcs * $qty;

        return [
            'calendar_type'          => 'meja',
            'product_name'           => $product->product_name,
            'size_label'             => $product->size_label,
            'orientation'            => $product->orientation,
            'material_name'          => $material->material_name,
            'leaf_count'             => $leafCount,
            'lamination'             => $lamination,
            'support_type'           => $supportType,
            'board_type'             => $boardType,
            'qty'                    => $qty,
            'markup_percent'         => $markupRate * 100,
            'support_detail'         => $supportDetail,
            'spiral_finishing_detail'=> [
                'binding_width_cm'   => $bindingWidth,
                'spiral_cost_pcs'    => $spiralCostPcs,
                'assembly_cost_pcs'  => $assemblyCostPcs,
                'total_per_pcs'      => $spiralFinCostPcs,
            ],
            'digital'                => $digital,
            'offset'                 => $offset,
            'recommended_route'      => $recommendedRoute,
            'recommendation_label'   => $recommendationLabel,
            'recommendation_reason'  => $recommendationReason,
            'recommended_price_pcs'  => $recommendedPricePcs,
            'recommended_total_offer'=> $recommendedTotalOffer,
            'share_texts'            => $this->buildShareTexts(
                'Meja',
                $product->size_label,
                $product->orientation,
                $material->material_name,
                $leafCount,
                $lamination,
                $supportDetail['name'] . ' + Spiral Wire-O',
                $qty,
                $recommendedPricePcs,
                $recommendedTotalOffer,
                $recommendedRoute,
                'SM52'
            ),
        ];
    }

    /**
     * Tentukan finishing kalender dinding otomatis
     */
    protected function determineWallFinishing(string $category, float $width): array
    {
        if ($category === 'AP') {
            if ($width <= 31.0) {
                return [
                    'code'         => 'ACC-JK-S',
                    'name'         => 'Jepit Kaleng Kecil (30–31 cm)',
                    'type'         => 'jepit_kaleng',
                    'cost_per_pcs' => (float) $this->getComponentPrice('ACC-JK-S', 2000.0),
                ];
            } elseif ($width <= 40.0) {
                return [
                    'code'         => 'ACC-JK-M',
                    'name'         => 'Jepit Kaleng Medium (35–40 cm)',
                    'type'         => 'jepit_kaleng',
                    'cost_per_pcs' => (float) $this->getComponentPrice('ACC-JK-M', 2500.0),
                ];
            } else {
                return [
                    'code'         => 'ACC-JK-L',
                    'name'         => 'Jepit Kaleng Besar (44–50 cm)',
                    'type'         => 'jepit_kaleng',
                    'cost_per_pcs' => (float) $this->getComponentPrice('ACC-JK-L', 3000.0),
                ];
            }
        } else {
            // Art Carton uses Spiral Wire-O + Hanger
            $spiralRateCm = (float) $this->getComponentPrice('ACC-SPIRAL', 150.0);
            $hangerRate = (float) $this->getComponentPrice('ACC-HANGER', 500.0);
            $spiralCost = $width * $spiralRateCm;
            $totalFin = $spiralCost + $hangerRate;
            return [
                'code'         => 'ACC-SPIRAL-HANGER',
                'name'         => 'Spiral Wire-O + Hanger Kawat',
                'type'         => 'spiral_hanger',
                'cost_per_pcs' => $totalFin,
            ];
        }
    }

    /**
     * Hitung biaya penopang kalender meja
     */
    protected function calculateDeskSupport(
        string $sizeLabel,
        string $orientation,
        string $supportType,
        string $boardType
    ): array {
        $isPrinting = ($supportType === 'printing_fc' || str_contains($supportType, 'print'));
        $isBoot30 = ($boardType === 'boot_30' || str_contains($boardType, '30'));

        if (!$isPrinting) {
            $compCode = $isBoot30 ? 'STAND-B30-JADI' : 'STAND-B40-JADI';
            $defaultPrice = $isBoot30 ? 8000.0 : 6500.0;
            $costPcs = (float) $this->getComponentPrice($compCode, $defaultPrice);
            return [
                'type'         => 'beli_jadi',
                'name'         => ($isBoot30 ? 'Penopang Boot 30 Beli Jadi (Polos)' : 'Penopang Boot 40 Beli Jadi (Polos)'),
                'cost_per_pcs' => $costPcs,
            ];
        }

        // Penopang Printing Full Color (Dinamis dari m_calendar_components)
        $bootCode = $isBoot30 ? 'STAND-B30-PLANO' : 'STAND-B40-PLANO';
        $bootPrice = (float) $this->getComponentPrice($bootCode, $isBoot30 ? 11500.0 : 7000.0);
        $wrapOutRate = (float) $this->getComponentPrice('STAND-WRAP-OUT', 2125.0);
        $wrapInRate = (float) $this->getComponentPrice('STAND-WRAP-IN', 413.0);

        // Boot yield / plano (BootUp)
        if ($sizeLabel === '15 x 10.5 cm') {
            $bootUp = $orientation === 'landscape' ? 8 : 7;
            $coverPrintA3 = $wrapOutRate * 1; // 1 lembar A3+ cover luar
            $innerPaperA3 = $wrapInRate * 1;  // 1 lembar A3+ tutup dalam
        } elseif ($sizeLabel === '21 x 15 cm') {
            $bootUp = $orientation === 'landscape' ? 3 : 5;
            $coverPrintA3 = $orientation === 'landscape' ? ($wrapOutRate * 1) : ($wrapOutRate * 2);
            $innerPaperA3 = $orientation === 'landscape' ? ($wrapInRate * 1) : ($wrapInRate * 2);
        } else {
            // 24 x 17 cm
            $bootUp = $orientation === 'landscape' ? 3 : 4;
            $coverPrintA3 = $wrapOutRate * 2; // 2 lembar A3+ cover luar
            $innerPaperA3 = $wrapInRate * 2;  // 2 lembar A3+ tutup dalam
        }

        $costPcs = ($bootPrice / $bootUp) + $coverPrintA3 + $innerPaperA3;

        return [
            'type'         => 'printing_fc',
            'name'         => 'Penopang ' . ($isBoot30 ? 'Boot 30' : 'Boot 40') . ' Printing Full Color',
            'board_price'  => $bootPrice,
            'boot_up'      => $bootUp,
            'cover_print'  => $coverPrintA3,
            'inner_paper'  => $innerPaperA3,
            'cost_per_pcs' => $costPcs,
        ];
    }

    /**
     * Resolusi harga kertas plano untuk Kalender Dinding (Dinamis dari m_calendar_materials)
     */
    protected function resolvePlanoPrice(object $material, string $sizeLabel, ?string $recommendedPlano): float
    {
        if ($sizeLabel === '30 x 42 cm' && !empty($material->offset_price_65x90)) {
            return (float) $material->offset_price_65x90;
        } elseif (($sizeLabel === '30 x 46 cm' || $sizeLabel === '31 x 47 cm') && !empty($material->offset_price_65x100)) {
            return (float) $material->offset_price_65x100;
        } elseif (!empty($material->offset_price_109x79)) {
            return (float) $material->offset_price_109x79;
        }

        return (float) ($material->offset_price_65x90 ?: 2000.0);
    }

    /**
     * Resolusi harga kertas plano untuk Kalender Meja (Dinamis dari m_calendar_materials)
     */
    protected function resolvePlanoPriceDesk(object $material, string $sizeLabel): float
    {
        if ($sizeLabel === '24 x 17 cm' || str_contains($material->material_name, 'Blues White')) {
            return (float) ($material->offset_price_109x79 ?: 5800.0);
        }

        return (float) ($material->offset_price_65x90 ?: ($material->offset_price_109x79 ?: 3600.0));
    }

    /**
     * Helper ambil harga komponen dari DB atau fallback
     */
    protected function getComponentPrice(string $code, float $fallback): float
    {
        try {
            $comp = DB::table('m_calendar_components')->where('component_code', $code)->first();
            return $comp ? (float) $comp->price : $fallback;
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    /**
     * Ambil pricing rule (markup tier) dari database
     */
    protected function getPricingRule(int $qty): ?object
    {
        try {
            return DB::table('m_calendar_pricing_rules')
                ->where('status', 'active')
                ->where('min_qty', '<=', $qty)
                ->where(function ($query) use ($qty) {
                    $query->whereNull('max_qty')
                        ->orWhere('max_qty', '>=', $qty);
                })
                ->orderBy('min_qty', 'desc')
                ->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Fallback markup jika DB tidak terakses
     */
    protected function getFallbackMarkupRate(int $qty): float
    {
        if ($qty >= 500) return 0.30;
        if ($qty >= 300) return 0.35;
        if ($qty >= 200) return 0.40;
        if ($qty >= 100) return 0.45;
        return 0.50;
    }

    /**
     * Pembulatan ke atas kelipatan nominal tertentu (Ceiling)
     */
    protected function ceilingRound(float $value, int $multiple = 500): int
    {
        if ($multiple <= 0) return (int) ceil($value);
        return (int) (ceil($value / $multiple) * $multiple);
    }

    /**
     * Generate format teks WA dan ringkasan harga untuk sales
     */
    protected function buildShareTexts(
        string $calendarCategory,
        string $sizeLabel,
        string $orientation,
        string $materialName,
        int $leafCount,
        string $lamination,
        string $finishing,
        int $qty,
        int $pricePcs,
        int $totalOffer,
        string $recommendedRoute,
        string $machineKey
    ): array {
        $laminationLabel = $lamination === 'none' ? 'Tanpa Laminasi' : ucfirst($lamination);
        $routeLabel = $recommendedRoute === 'digital' ? 'Digital A3+' : "Offset ({$machineKey})";

        // 1. Teks Harga / Pcs Saja
        $textPricePcs = "Rp " . number_format($pricePcs, 0, ',', '.') . " / pcs (Min order {$qty} pcs)";

        // 2. Teks Rincian Spesifikasi Saja untuk WA
        $textWa = "Spesifikasi Kalender {$calendarCategory}\n\n";
        $textWa .= "Detail Cetak:\n";
        $textWa .= "- Ukuran: {$sizeLabel} (" . ucfirst($orientation) . ")\n";
        $textWa .= "- Isi: {$leafCount} Lembar\n";
        $textWa .= "- Kertas: {$materialName}\n";
        $textWa .= "- Laminasi: {$laminationLabel}\n";
        $textWa .= "- Finishing: {$finishing}\n";
        $textWa .= "- Qty: " . number_format($qty, 0, ',', '.') . " pcs\n\n";
        $textWa .= "Harga Penawaran:\n";
        $textWa .= "- Harga Satuan: Rp " . number_format($pricePcs, 0, ',', '.') . " / pcs\n";
        $textWa .= "- Total Order: Rp " . number_format($totalOffer, 0, ',', '.') . "\n";
        $textWa .= "- Rekomendasi Mesin: {$routeLabel}";

        return [
            'copy_price_pcs' => $textPricePcs,
            'copy_wa_full'   => $textWa,
        ];
    }
}


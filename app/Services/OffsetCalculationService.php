<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class OffsetCalculationService
{
    /**
     * Hitung kalkulasi biaya dan harga jual cetak offset untuk spesifikasi tertentu.
     *
     * @param int $productId
     * @param int $planoMaterialId
     * @param float $rims Jumlah rim (misal: 1, 2, 3, 5, 10)
     * @param int $printSides 1 untuk 1 Muka, 2 untuk 2 Muka
     * @param string $priceType 'general' (Harga Umum) atau 'division' (Harga Divisi)
     * @return array
     */
    public function calculate(
        int $productId,
        int $planoMaterialId,
        float $rims,
        int $printSides = 1,
        string $priceType = 'general'
    ): array {
        // 1. Ambil data master produk offset
        $product = DB::table('m_offset_products')
            ->where('id', $productId)
            ->first();

        if (!$product) {
            throw new \Exception('Produk offset tidak ditemukan.');
        }

        // 2. Ambil data master kertas plano
        $planoMaterial = DB::table('m_offset_plano_materials')
            ->where('id', $planoMaterialId)
            ->first();

        if (!$planoMaterial) {
            throw new \Exception('Material kertas plano tidak ditemukan.');
        }

        // 3. Ambil data master plat CTP
        $plate = DB::table('m_offset_plates')
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->first();
        if (!$plate) {
            $plate = DB::table('m_offset_plates')->first();
        }
        $platePricePerUnit = $plate ? (float) ($plate->price_per_plate ?? $plate->price ?? 16000) : 16000;

        // 4. Ambil data tarif cetak mesin besar & potong
        $printCost = DB::table('m_offset_print_costs')
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->first();
        if (!$printCost) {
            $printCost = DB::table('m_offset_print_costs')->first();
        }
        $minPrintPerColor = $printCost ? (float) ($printCost->min_print_per_color ?? 65000) : 65000;
        $clickRatePerColor = $printCost ? (float) ($printCost->click_rate_per_color ?? 15) : 15;
        $cutCostPerRim = $printCost ? (float) ($printCost->cutting_fee_per_rim ?? 20000) : 20000;
        $inschietSheet = $printCost ? (int) ($printCost->default_insheet_sheets ?? 150) : 150;

        // 5. Ambil data pricing rules (margin & rounding)
        $pricingRule = DB::table('m_offset_pricing_rules')
            ->whereRaw('LOWER(status) = ?', ['active'])
            ->first();
        if (!$pricingRule) {
            $pricingRule = DB::table('m_offset_pricing_rules')->first();
        }
        $markupPercentage = 100.0;
        if ($pricingRule) {
            if ($priceType === 'division') {
                $markupPercentage = (float) ($pricingRule->markup_division ?? 80);
            } else {
                $markupPercentage = (float) ($pricingRule->markup_general ?? 100);
            }
        }
        $roundingValue = $pricingRule ? (int) ($pricingRule->rounding_value ?? 5000) : 5000;
        if ($roundingValue <= 0) {
            $roundingValue = 5000;
        }

        // Variabel produk & kalkulasi sesuai Excel sheet 'Brosur':
        $sheetPerRim = (int) ($product->sheet_per_rim ?? 125); // Kolom F Excel (125 lbr untuk A5, 250 lbr untuk A4)
        $planoCutYield = (int) ($product->plano_cut_yield ?? 4); // Kolom G pembagi (default 4 lembar per plano)
        if ($planoCutYield <= 0) {
            $planoCutYield = 4;
        }

        // Plano pokok per rim: CEILING(sheetPerRim / planoCutYield, 1) -> Kolom G Excel
        $planoPerRim = (int) ceil($sheetPerRim / $planoCutYield);

        // Plano inschiet: CEILING(inschietSheet / planoCutYield, 1) -> Kolom I Excel
        $planoInschiet = (int) ceil($inschietSheet / $planoCutYield);

        // Total Plano dibutuhkan: (Plano Pokok * Qty Rim) + Plano Inschiet
        $totalPlanoNeeded = ($planoPerRim * $rims) + $planoInschiet;

        // A. Biaya Kertas Plano
        $pricePerPlano = (float) $planoMaterial->price_per_plano;
        $paperCost = $totalPlanoNeeded * $pricePerPlano;

        // B. Biaya Plat CTP (4 Plat CMYK untuk 1 Sisi atau 2 Sisi balikan)
        $plateCount = 4;
        $plateCost = $plateCount * $platePricePerUnit;

        // C. Ongkos Cetak Mesin Offset
        // Lembar mesin jalan = (Qty Rim * sheetPerRim * faktorSisi) + inschietSheet
        // Pada Excel: 1 Sisi faktor = 1, 2 Sisi faktor = 2
        $sideFactor = ($printSides === 2) ? 2 : 1;
        $machineSheetsRun = ($rims * $sheetPerRim * $sideFactor) + $inschietSheet;

        // Biaya cetak 4 warna: 4 * IF(SheetsRun * clickRatePerColor < minPrintPerColor, minPrintPerColor, SheetsRun * clickRatePerColor)
        $printCostPerColor = max($machineSheetsRun * $clickRatePerColor, $minPrintPerColor);
        $totalPrintCost = 4 * $printCostPerColor;

        // D. Ongkos Potong (Plano & Jadi)
        $cuttingCost = $cutCostPerRim * $rims;

        // Total HPP Keseluruhan (Total Production Cost)
        $totalHpp = $paperCost + $plateCost + $totalPrintCost + $cuttingCost;
        $hppPerRim = ($rims > 0) ? ($totalHpp / $rims) : $totalHpp;
        $hppPerPcs = ($rims > 0) ? ($totalHpp / ($rims * 500)) : 0;

        // Harga Jual (Markup & Pembulatan per Rim sesuai rumus Excel =CEILING((HPP_Rim * Markup) + HPP_Rim, 5000))
        $markupRate = $markupPercentage / 100;
        $rawSellingPricePerRim = $hppPerRim * (1 + $markupRate);
        $sellingPricePerRim = (int) (ceil($rawSellingPricePerRim / $roundingValue) * $roundingValue);
        $totalSellingPrice = (int) ($sellingPricePerRim * $rims);
        $sellingPricePerPcs = ($rims > 0) ? ($totalSellingPrice / ($rims * 500)) : 0;

        return [
            'product' => [
                'id' => $product->id,
                'name' => $product->product_name,
                'code' => $product->product_code,
                'category' => $product->category,
                'final_length' => $product->final_length,
                'final_width' => $product->final_width,
                'sheet_per_rim' => $sheetPerRim,
                'plano_cut_yield' => $planoCutYield,
                'print_sheet_length' => $product->print_sheet_length,
                'print_sheet_width' => $product->print_sheet_width,
            ],
            'material' => [
                'id' => $planoMaterial->id,
                'name' => $planoMaterial->material_name,
                'gramature' => $planoMaterial->gramature,
                'paper_type' => $planoMaterial->paper_type,
                'length' => $planoMaterial->length,
                'width' => $planoMaterial->width,
                'price_per_plano' => $pricePerPlano,
            ],
            'input' => [
                'rims' => $rims,
                'total_pcs' => $rims * 500,
                'print_sides' => $printSides,
                'price_type' => $priceType,
            ],
            'breakdown' => [
                // Kertas
                'plano_per_rim' => $planoPerRim,
                'plano_inschiet' => $planoInschiet,
                'total_plano_needed' => $totalPlanoNeeded,
                'paper_cost' => $paperCost,

                // Plat CTP
                'plate_count' => $plateCount,
                'plate_price_unit' => $platePricePerUnit,
                'plate_cost' => $plateCost,

                // Cetak Mesin
                'machine_sheets_run' => $machineSheetsRun,
                'min_print_cost' => $minPrintPerColor * 4,
                'min_print_per_color' => $minPrintPerColor,
                'unit_run_cost' => $clickRatePerColor,
                'print_cost' => $totalPrintCost,

                // Potong
                'cut_cost_per_rim' => $cutCostPerRim,
                'cutting_cost' => $cuttingCost,
            ],
            'totals' => [
                'total_hpp' => round($totalHpp),
                'hpp_per_rim' => round($hppPerRim),
                'hpp_per_pcs' => round($hppPerPcs, 2),
                'markup_percentage' => $markupPercentage,
                'rounding_value' => $roundingValue,
                'total_selling_price' => $totalSellingPrice,
                'selling_price_per_rim' => $sellingPricePerRim,
                'selling_price_per_pcs' => round($sellingPricePerPcs, 2),
            ],
        ];
    }

    /**
     * Generate matriks simulasi harga bertingkat untuk 1 sampai 10 rim (1 Muka dan 2 Muka)
     * seperti pada baris 16-38 sheet 'Brosur' di Excel.
     *
     * @param int $productId
     * @param int $planoMaterialId
     * @param string $priceType
     * @return array
     */
    public function generatePriceMatrix(
        int $productId,
        int $planoMaterialId,
        string $priceType = 'general'
    ): array {
        $matrix = [
            'one_side' => [], // 1 Muka
            'two_sides' => [], // 2 Muka
        ];

        // Daftar tier kuantitas rim standar: 1 s/d 10 rim
        $rimTiers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        foreach ($rimTiers as $rim) {
            // Kalkulasi 1 Sisi
            $calc1 = $this->calculate($productId, $planoMaterialId, $rim, 1, $priceType);
            $matrix['one_side'][$rim] = [
                'rims' => $rim,
                'pcs' => $rim * 500,
                'total_hpp' => $calc1['totals']['total_hpp'],
                'hpp_per_rim' => $calc1['totals']['hpp_per_rim'],
                'total_selling_price' => $calc1['totals']['total_selling_price'],
                'selling_price_per_rim' => $calc1['totals']['selling_price_per_rim'],
                'selling_price_per_pcs' => $calc1['totals']['selling_price_per_pcs'],
            ];

            // Kalkulasi 2 Sisi
            $calc2 = $this->calculate($productId, $planoMaterialId, $rim, 2, $priceType);
            $matrix['two_sides'][$rim] = [
                'rims' => $rim,
                'pcs' => $rim * 500,
                'total_hpp' => $calc2['totals']['total_hpp'],
                'hpp_per_rim' => $calc2['totals']['hpp_per_rim'],
                'total_selling_price' => $calc2['totals']['total_selling_price'],
                'selling_price_per_rim' => $calc2['totals']['selling_price_per_rim'],
                'selling_price_per_pcs' => $calc2['totals']['selling_price_per_pcs'],
            ];
        }

        return $matrix;
    }

    /**
     * Hitung kalkulasi biaya dan harga jual paket digital print A3+ untuk brosur
     * Sesuai tabel atas sheet 'Brosur' di Excel (baris 3-13).
     *
     * @param int $productId
     * @param int $materialId (id dari m_offset_plano_materials)
     * @param int $pieces (misal 100, 500)
     * @param int $printSides (1 atau 2)
     * @param string $priceType ('general' atau 'division')
     * @return array
     */
    public function calculateDigital(
        int $productId,
        int $materialId,
        int $pieces = 100,
        int $printSides = 1,
        string $priceType = 'general'
    ): array {
        // Ambil data produk
        $product = DB::table('m_offset_products')->where('id', $productId)->first();
        if (!$product) {
            throw new \Exception('Produk brosur tidak ditemukan.');
        }

        // Ambil data material
        $material = DB::table('m_offset_plano_materials')->where('id', $materialId)->first();
        if (!$material) {
            throw new \Exception('Material tidak ditemukan.');
        }

        // Tentukan ukuran: A5 atau A4
        $prodName = strtolower($product->product_name . ' ' . $product->product_code);
        $size = 'A5';
        $up = 4;
        if (str_contains($prodName, 'a4') && !str_contains($prodName, '1/3') && !str_contains($prodName, 'dl')) {
            $size = 'A4';
            $up = 2;
        }

        // Tentukan jenis bahan: AP150, AP120, HVS100
        $matName = strtolower($material->material_name);
        $matKey = 'AP120';
        if (str_contains($matName, '150')) {
            $matKey = 'AP150';
        } elseif (str_contains($matName, 'hvs') || str_contains($matName, '100')) {
            $matKey = 'HVS100';
        } elseif (str_contains($matName, '120')) {
            $matKey = 'AP120';
        }

        // Lembar A3+ yang dibutuhkan
        $a3Sheets = (int) ceil($pieces / $up);

        // Rate map dari Excel 'Print A3+ Full Color'
        $rateMap = [
            'A5' => [
                'AP150' => [
                    100 => [1 => 5500, 2 => 9500],
                    500 => [1 => 4600, 2 => 7800],
                ],
                'AP120' => [
                    100 => [1 => 5000, 2 => 9000],
                    500 => [1 => 4200, 2 => 7200],
                ],
                'HVS100' => [
                    100 => [1 => 5000, 2 => 8500],
                    500 => [1 => 4000, 2 => 7200],
                ],
            ],
            'A4' => [
                'AP120' => [
                    100 => [1 => 5500, 2 => 9500],
                    500 => [1 => 4600, 2 => 7800],
                ],
                'AP150' => [
                    100 => [1 => 5000, 2 => 9000],
                    500 => [1 => 4200, 2 => 7200],
                ],
                'HVS100' => [
                    100 => [1 => 5000, 2 => 8500],
                    500 => [1 => 4000, 2 => 7200],
                ],
            ]
        ];

        // Tentukan tier kuantitas terdekat
        $tier = ($pieces <= 250) ? 100 : 500;
        $ratePerA3 = $rateMap[$size][$matKey][$tier][$printSides] ?? 5000;

        // Ongkos Print & Potong
        $printCost = $a3Sheets * $ratePerA3;
        $cuttingCost = 35000; // Flat minimum ongkos potong digital A3+
        if ($size === 'A5' && $matKey === 'AP150' && $pieces === 500 && $printSides === 2) {
            $cuttingCost = 20000; // Sesuai formula sel N8 di Excel (merujuk L5)
        }
        $totalBaseHpp = $printCost + $cuttingCost;

        // Harga Jual Umum & Divisi (persis rumus Excel)
        if ($tier === 100) {
            $sellingPrice = (int) (floor(($totalBaseHpp * 0.95) / 5000) * 5000);
        } else {
            // Tier 500 Lembar (1 Rim) sesuai rumus Excel baris 8-13:
            // AA (1 Sisi Umum): =floor(L8*80%, 5000) -> totalBaseHpp * 0.80
            // AB (2 Sisi Umum): =floor(M8*80%, 5000) -> printCost * 0.80
            // AC (1 Sisi Divisi): =floor(AA8*80%, 5000) -> sellingUmum * 0.80
            // AD (2 Sisi Divisi): =floor(O8*80%, 5000) -> totalBaseHpp * 0.80
            if ($printSides === 2) {
                $sellingUmum = (int) (floor(($printCost * 0.80) / 5000) * 5000);
                if ($priceType === 'division') {
                    $sellingPrice = (int) (floor(($totalBaseHpp * 0.80) / 5000) * 5000);
                } else {
                    $sellingPrice = $sellingUmum;
                }
            } else {
                $sellingUmum = (int) (floor(($totalBaseHpp * 0.80) / 5000) * 5000);
                if ($priceType === 'division') {
                    $sellingPrice = (int) (floor(($sellingUmum * 0.80) / 5000) * 5000);
                } else {
                    $sellingPrice = $sellingUmum;
                }
            }
        }

        $pricePerPcs = $pieces > 0 ? round($sellingPrice / $pieces, 2) : 0;

        return [
            'mode' => 'digital',
            'product' => [
                'id' => $product->id,
                'name' => $product->product_name,
                'code' => $product->product_code,
                'size_name' => $size,
                'up_per_sheet' => $up,
            ],
            'material' => [
                'id' => $material->id,
                'name' => $material->material_name,
                'key' => $matKey,
            ],
            'specs' => [
                'pieces' => $pieces,
                'print_sides' => $printSides,
                'a3_sheets_needed' => $a3Sheets,
                'rate_per_a3' => $ratePerA3,
            ],
            'breakdown' => [
                'print_cost' => $printCost,
                'cutting_cost' => $cuttingCost,
                'total_hpp' => $totalBaseHpp,
            ],
            'totals' => [
                'total_hpp' => $totalBaseHpp,
                'hpp_per_pcs' => round($totalBaseHpp / $pieces, 2),
                'total_selling_price' => $sellingPrice,
                'selling_price_per_pcs' => $pricePerPcs,
            ]
        ];
    }

    /**
     * Generate tabel simulasi 4 paket digital:
     * 100 Lbr (1 Sisi & 2 Sisi), 500 Lbr / 1 Rim (1 Sisi & 2 Sisi)
     */
    public function generateDigitalMatrix(
        int $productId,
        int $materialId,
        string $priceType = 'general'
    ): array {
        $packages = [
            [
                'label' => '100 Lembar (1 Sisi)',
                'pieces' => 100,
                'sides' => 1,
            ],
            [
                'label' => '100 Lembar (2 Sisi)',
                'pieces' => 100,
                'sides' => 2,
            ],
            [
                'label' => '500 Lembar / 1 Rim (1 Sisi)',
                'pieces' => 500,
                'sides' => 1,
            ],
            [
                'label' => '500 Lembar / 1 Rim (2 Sisi)',
                'pieces' => 500,
                'sides' => 2,
            ],
        ];

        $matrix = [];
        foreach ($packages as $pkg) {
            $calc = $this->calculateDigital($productId, $materialId, $pkg['pieces'], $pkg['sides'], $priceType);
            $matrix[] = [
                'label' => $pkg['label'],
                'pieces' => $pkg['pieces'],
                'print_sides' => $pkg['sides'],
                'a3_sheets' => $calc['specs']['a3_sheets_needed'],
                'print_cost' => $calc['breakdown']['print_cost'],
                'cutting_cost' => $calc['breakdown']['cutting_cost'],
                'total_hpp' => $calc['totals']['total_hpp'],
                'total_selling_price' => $calc['totals']['total_selling_price'],
                'selling_price_per_pcs' => $calc['totals']['selling_price_per_pcs'],
            ];
        }

        return $matrix;
    }
}

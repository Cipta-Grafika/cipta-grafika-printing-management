<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DigitalProductionCostsSheetExport implements FromArray, WithEvents, WithTitle
{
    private const FIXED_COLUMNS = 7;

    private $tierLabels = [
        'tier_1' => '1 lbr',
        'tier_2_5' => '2-5 lbr',
        'tier_6_50' => '6-50 lbr',
        'tier_100' => '100 lbr',
        'tier_300' => '300 lbr',
        'tier_500' => '500 lbr',
    ];

    private $priceGroups = [
        'general_single' => ['general', 'single', 'HARGA UMUM - PRINT 1 MUKA', 'FEF3C7'],
        'general_double' => ['general', 'double', 'HARGA UMUM - PRINT 2 MUKA', 'FEF3C7'],
        'division_single' => ['division', 'single', 'HARGA DIVISI - PRINT 1 MUKA', 'D1FAE5'],
        'division_double' => ['division', 'double', 'HARGA DIVISI - PRINT 2 MUKA', 'D1FAE5'],
    ];

    private $sectionRows = [];
    private $tableHeaderRows = [];
    private $dataRows = [];

    public function array(): array
    {
        $details = DB::table('m_digital_production_cost_details as d')
            ->join('m_digital_production_costs as pc', 'pc.id', '=', 'd.digital_production_cost_id')
            ->join('m_engines as e', 'e.id', '=', 'pc.engine_id')
            ->join('m_locations as l', 'l.id', '=', 'pc.location_id')
            ->leftJoin('m_vendors as v', 'v.id', '=', 'pc.vendor_id')
            ->join('m_materials as m', 'm.id', '=', 'd.material_id')
            ->join('m_categories as c', 'c.id', '=', 'm.category_id')
            ->whereRaw('LOWER(TRIM(e.name)) = ?', ['digital print a3+'])
            ->select(
                'd.id',
                'd.material_id',
                'pc.id as production_cost_id',
                'e.name as engine_name',
                'l.name as location_name',
                'v.name as vendor_name',
                'm.material_name',
                'c.id as category_id',
                'c.name as category_name',
                'd.paper_cost',
                'd.click_cost',
                'd.single_print_cost',
                'd.double_print_cost'
            )
            ->orderBy('l.name')
            ->orderBy('v.name')
            ->orderBy('c.name')
            ->orderBy('m.material_name')
            ->get();

        $prices = DB::table('m_digital_production_cost_prices')
            ->whereIn('digital_production_cost_detail_id', $details->pluck('id'))
            ->select(
                'digital_production_cost_detail_id',
                'price_type',
                'print_type',
                'quantity_tier',
                'markup_percentage',
                'selling_price'
            )
            ->get()
            ->groupBy('digital_production_cost_detail_id')
            ->map(function ($detailPrices) {
                return $detailPrices->keyBy(function ($price) {
                    return $price->price_type . '|' . $price->print_type . '|' . $price->quantity_tier;
                });
            });

        $materialSizes = DB::table('m_material_sizes')
            ->whereIn('material_id', $details->pluck('material_id')->unique()->values())
            ->orderBy('id')
            ->get()
            ->groupBy('material_id');

        $columnCount = $this->columnCount();
        $emptyRow = array_fill(0, $columnCount, '');

        $rows = [];
        $rows[] = $this->padRow(['ONGKOS PRODUKSI DIGITAL PRINT A3+'], $columnCount);
        $rows[] = $emptyRow;

        if ($details->isEmpty()) {
            $rows[] = $this->padRow(['Belum ada data ongkos produksi Digital Print A3+.'], $columnCount);

            return $rows;
        }

        foreach ($details->groupBy('production_cost_id') as $headerDetails) {
            $header = $headerDetails->first();

            foreach ([
                ['MESIN', $header->engine_name],
                ['LOKASI', $header->location_name],
                ['VENDOR', $header->vendor_name ?: '-'],
            ] as $sectionRow) {
                $rows[] = $this->padRow($sectionRow, $columnCount);
                $this->sectionRows[] = count($rows);
            }

            foreach ($headerDetails->groupBy('category_id') as $categoryDetails) {
                $rows[] = $this->padRow(['KATEGORI', $categoryDetails->first()->category_name], $columnCount);
                $this->sectionRows[] = count($rows);

                $groupRow = ['No', 'Item', 'Ukuran', 'Kertas', 'Klik', 'Cost Print 1 Sisi', 'Cost Print 2 Sisi'];
                $tierRow = array_fill(0, self::FIXED_COLUMNS, '');
                $markupRow = array_fill(0, self::FIXED_COLUMNS, '');

                foreach ($this->priceGroups as [$priceType, $printType, $groupLabel]) {
                    foreach ($this->tierLabels as $tier => $tierLabel) {
                        $groupRow[] = $tier === 'tier_1' ? $groupLabel : '';
                        $tierRow[] = $tierLabel;

                        // Markup per tier sama untuk satu kategori karena berasal dari pricing rule yang sama.
                        $markup = $categoryDetails
                            ->map(function ($detail) use ($prices, $priceType, $printType, $tier) {
                                $detailPrices = $prices->get($detail->id);

                                return $detailPrices ? $detailPrices->get($priceType . '|' . $printType . '|' . $tier) : null;
                            })
                            ->filter()
                            ->first();

                        $markupRow[] = $markup ? $this->formatNumber($markup->markup_percentage) . '%' : '-';
                    }
                }

                $rows[] = $groupRow;
                $this->tableHeaderRows[] = count($rows);
                $rows[] = $tierRow;
                $rows[] = $markupRow;

                $dataStart = count($rows) + 1;

                foreach ($categoryDetails->values() as $index => $detail) {
                    $detailPrices = $prices->get($detail->id) ?? collect();
                    $sizeLabel = ($materialSizes->get($detail->material_id) ?? collect())
                        ->map(function ($size) {
                            return $this->formatNumber($size->width) . ' x ' .
                                $this->formatNumber($size->length) . ' ' . $size->unit;
                        })
                        ->implode(', ');

                    $row = [
                        $index + 1,
                        $detail->material_name,
                        $sizeLabel ?: '-',
                        (float) $detail->paper_cost,
                        (float) $detail->click_cost,
                        (float) $detail->single_print_cost,
                        (float) $detail->double_print_cost,
                    ];

                    foreach ($this->priceGroups as [$priceType, $printType]) {
                        foreach (array_keys($this->tierLabels) as $tier) {
                            $price = $detailPrices->get($priceType . '|' . $printType . '|' . $tier);
                            $row[] = $price ? (float) $price->selling_price : '-';
                        }
                    }

                    $rows[] = $row;
                }

                $this->dataRows[] = [$dataStart, count($rows)];
                $rows[] = $emptyRow;
            }

            $rows[] = $emptyRow;
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Digital Print A3+';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastColumn = Coordinate::stringFromColumnIndex($this->columnCount());
                $fixedLastColumn = Coordinate::stringFromColumnIndex(self::FIXED_COLUMNS);
                $tierCount = count($this->tierLabels);

                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(18);

                for ($column = 4; $column <= $this->columnCount(); $column++) {
                    $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($column))->setWidth(13);
                }

                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                foreach ($this->sectionRows as $row) {
                    $sheet->mergeCells("B{$row}:{$fixedLastColumn}{$row}");
                    $sheet->getStyle("A{$row}:{$fixedLastColumn}{$row}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'E2E8F0'],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FFD1D5DB'],
                            ],
                        ],
                    ]);
                }

                foreach ($this->tableHeaderRows as $index => $headerRow) {
                    $markupRow = $headerRow + 2;
                    [$dataStart, $dataEnd] = $this->dataRows[$index];

                    for ($column = 1; $column <= self::FIXED_COLUMNS; $column++) {
                        $letter = Coordinate::stringFromColumnIndex($column);
                        $sheet->mergeCells("{$letter}{$headerRow}:{$letter}{$markupRow}");
                    }

                    $sheet->getStyle("A{$headerRow}:{$lastColumn}{$markupRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F1F5F9'],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                            'wrapText' => true,
                        ],
                    ]);

                    $groupIndex = 0;

                    foreach ($this->priceGroups as [, , , $color]) {
                        $startColumn = self::FIXED_COLUMNS + 1 + ($groupIndex * $tierCount);
                        $start = Coordinate::stringFromColumnIndex($startColumn);
                        $end = Coordinate::stringFromColumnIndex($startColumn + $tierCount - 1);

                        $sheet->mergeCells("{$start}{$headerRow}:{$end}{$headerRow}");
                        $sheet->getStyle("{$start}{$headerRow}:{$end}{$dataEnd}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setRGB($color);

                        $groupIndex++;
                    }

                    $sheet->getStyle("A{$headerRow}:{$lastColumn}{$dataEnd}")->getBorders()->getAllBorders()
                        ->setBorderStyle(Border::BORDER_THIN)
                        ->getColor()->setARGB('FFD1D5DB');

                    // Garis tebal pemisah antar kelompok harga, diterapkan setelah border tipis.
                    for ($groupIndex = 0; $groupIndex < count($this->priceGroups); $groupIndex++) {
                        $start = Coordinate::stringFromColumnIndex(self::FIXED_COLUMNS + 1 + ($groupIndex * $tierCount));
                        $sheet->getStyle("{$start}{$headerRow}:{$start}{$dataEnd}")->getBorders()
                            ->getLeft()->setBorderStyle(Border::BORDER_MEDIUM);
                    }

                    $sheet->getStyle("A{$dataStart}:A{$dataEnd}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("C{$dataStart}:C{$dataEnd}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("D{$dataStart}:{$lastColumn}{$dataEnd}")->applyFromArray([
                        'numberFormat' => ['formatCode' => '"Rp"#,##0'],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    ]);
                    $sheet->getStyle("F{$dataStart}:G{$dataEnd}")->getFont()->setBold(true);
                }

                $sheet->freezePane('C3');
            },
        ];
    }

    private function columnCount()
    {
        return self::FIXED_COLUMNS + (count($this->priceGroups) * count($this->tierLabels));
    }

    private function padRow(array $values, $columnCount)
    {
        return array_pad($values, $columnCount, '');
    }

    private function formatNumber($value)
    {
        return rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');
    }
}

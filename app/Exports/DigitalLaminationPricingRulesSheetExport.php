<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DigitalLaminationPricingRulesSheetExport implements FromArray, WithEvents, WithTitle
{
    public function array(): array
    {
        $pricingRules = DB::table('m_digital_lamination_pricing_rule_details as d')
            ->join(
                'm_digital_lamination_pricing_rules as pr',
                'd.digital_lamination_pricing_rule_id',
                '=',
                'pr.id'
            )
            ->join('m_engines as e', 'pr.engine_id', '=', 'e.id')
            ->join('m_locations as l', 'pr.location_id', '=', 'l.id')
            ->join('m_categories as c', 'pr.category_id', '=', 'c.id')
            ->leftJoin('m_vendors as v', 'pr.vendor_id', '=', 'v.id')
            ->whereRaw('LOWER(e.name) = ?', ['digital print a3+'])
            ->select(
                'pr.id as pricing_rule_id',
                'e.name as engine_name',
                'l.name as location_name',
                'v.name as vendor_name',
                'c.name as category_name',
                'd.price_type',
                'd.print_type',
                'd.minimum_quantity',
                'd.maximum_quantity',
                'd.markup_percentage',
                'd.rounding_value',
                'd.status'
            )
            ->orderBy('e.name')
            ->orderBy('l.name')
            ->orderBy('v.name')
            ->orderBy('c.name')
            ->orderByRaw(
                "CASE d.price_type
                    WHEN 'general' THEN 0
                    WHEN 'division' THEN 1
                    ELSE 2
                END"
            )
            ->orderByRaw(
                "CASE d.print_type
                    WHEN 'single' THEN 0
                    WHEN 'double' THEN 1
                    ELSE 2
                END"
            )
            ->orderBy('d.minimum_quantity')
            ->get();

        $rows = [
            ['MARKUP HARGA LAMINASI DIGITAL PRINT A3+', '', '', '', '', '', ''],
            ['', '', '', '', '', '', ''],
        ];

        $priceTypeLabels = [
            'general' => 'Harga Umum',
            'division' => 'Harga Divisi',
        ];
        $printTypeLabels = [
            'single' => '1 Muka',
            'double' => '2 Muka',
        ];

        foreach ($pricingRules->groupBy('pricing_rule_id') as $group) {
            $first = $group->first();

            $rows[] = ['MESIN', $first->engine_name, '', '', '', '', ''];
            $rows[] = ['LOKASI', $first->location_name, '', '', '', '', ''];
            $rows[] = ['VENDOR', $first->vendor_name ?: '-', '', '', '', '', ''];
            $rows[] = ['KATEGORI', $first->category_name, '', '', '', '', ''];
            $rows[] = [
                'No',
                'Tipe Harga',
                'Harga Print',
                'Jumlah Lembar',
                'Markup',
                'Pembulatan',
                'Status',
            ];

            $number = 1;

            foreach ($group as $item) {
                $quantity = $item->maximum_quantity === null
                    ? $item->minimum_quantity . '+'
                    : $item->minimum_quantity . ' - ' . $item->maximum_quantity;

                $rows[] = [
                    $number++,
                    $priceTypeLabels[$item->price_type] ?? $item->price_type,
                    $printTypeLabels[$item->print_type] ?? $item->print_type,
                    $quantity,
                    $item->markup_percentage,
                    $item->rounding_value,
                    $item->status,
                ];
            }

            $rows[] = ['', '', '', '', '', '', ''];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Laminasi Digital Print A3+';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                foreach (['A' => 12, 'B' => 22, 'C' => 18, 'D' => 18, 'E' => 16, 'F' => 18, 'G' => 16] as $column => $width) {
                    $sheet->getColumnDimension($column)->setWidth($width);
                }

                $sheet->mergeCells('A1:G1');
                $sheet->getStyle('A1:G1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                for ($row = 1; $row <= $highestRow; $row++) {
                    $firstCell = $sheet->getCell("A{$row}")->getValue();

                    if (in_array($firstCell, ['MESIN', 'LOKASI', 'VENDOR', 'KATEGORI'], true)) {
                        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                            'font' => ['bold' => true],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'E2E8F0'],
                            ],
                        ]);
                    }

                    if ($firstCell === 'No') {
                        $sheet->getStyle("A{$row}:G{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => '2563EB'],
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);
                    }
                }

                $sheet->getStyle("A1:G{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFD1D5DB'],
                        ],
                    ],
                ]);
            },
        ];
    }
}

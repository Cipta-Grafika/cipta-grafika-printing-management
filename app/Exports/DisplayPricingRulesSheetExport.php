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

class DisplayPricingRulesSheetExport implements
    FromArray,
    WithEvents,
    WithTitle
{
    /**
     * Data yang akan diexport.
     */
    public function array(): array
    {
        $pricingRules = DB::table('m_display_pricing_rules as pr')
            ->join(
                'm_engines as e',
                'pr.engine_id',
                '=',
                'e.id'
            )
            ->join(
                'm_locations as l',
                'pr.location_id',
                '=',
                'l.id'
            )
            ->join(
                'm_categories as c',
                'pr.category_id',
                '=',
                'c.id'
            )
            ->select(
                'pr.engine_id',
                'e.name as engine_name',
                'pr.location_id',
                'l.name as location_name',
                'pr.category_id',
                'c.name as category_name',
                'pr.price_type',
                'pr.markup_percentage',
                'pr.rounding_value',
                'pr.status'
            )
            ->orderBy('e.name', 'asc')
            ->orderBy('l.name', 'asc')
            ->orderBy('c.name', 'asc')
            ->get();

        $rows = [];

        /*
         * TITLE
         */
        $rows[] = [
            'MARKUP DISPLAY · RANGKA',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = ['', '', '', '', '', ''];

        /*
         * GROUPING
         */
        $lastEngineId = null;
        $lastLocationId = null;
        $lastCategoryId = null;

        /*
         * Urutan tipe harga.
         */
        $priceTypeOrder = [
            'general' => 1,
            'division' => 2,
            'plain' => 3,
        ];

        /*
         * Label tipe harga.
         */
        $priceTypeLabels = [
            'general' => 'Harga Umum',
            'division' => 'Harga Divisi',
            'plain' => 'Harga Polos',
        ];

        $pricingRules = $pricingRules
            ->sortBy(function ($item) use ($priceTypeOrder) {
                return $priceTypeOrder[$item->price_type] ?? 999;
            })
            ->values();

        foreach ($pricingRules as $pricingRule) {

            /*
             * ENGINE BARU
             */
            if ($lastEngineId !== $pricingRule->engine_id) {

                $rows[] = ['', '', '', '', '', ''];

                $rows[] = [
                    'MESIN',
                    $pricingRule->engine_name,
                    '',
                    '',
                    '',
                    '',
                ];

                $rows[] = [
                    'LOKASI',
                    $pricingRule->location_name,
                    '',
                    '',
                    '',
                    '',
                ];

                $rows[] = [
                    'KATEGORI',
                    $pricingRule->category_name,
                    '',
                    '',
                    '',
                    '',
                ];

                $rows[] = [
                    'No',
                    'Tipe Harga',
                    'Markup',
                    'Pembulatan',
                    'Status',
                    '',
                ];

                $lastEngineId = $pricingRule->engine_id;
                $lastLocationId = $pricingRule->location_id;
                $lastCategoryId = $pricingRule->category_id;

                continue;
            }

            /*
             * LOKASI BARU
             */
            if ($lastLocationId !== $pricingRule->location_id) {

                $rows[] = ['', '', '', '', '', ''];

                $rows[] = [
                    'LOKASI',
                    $pricingRule->location_name,
                    '',
                    '',
                    '',
                    '',
                ];

                $rows[] = [
                    'KATEGORI',
                    $pricingRule->category_name,
                    '',
                    '',
                    '',
                    '',
                ];

                $rows[] = [
                    'No',
                    'Tipe Harga',
                    'Markup',
                    'Pembulatan',
                    'Status',
                    '',
                ];

                $lastLocationId = $pricingRule->location_id;
                $lastCategoryId = $pricingRule->category_id;

                continue;
            }

            /*
             * KATEGORI BARU
             */
            if ($lastCategoryId !== $pricingRule->category_id) {

                $rows[] = ['', '', '', '', '', ''];

                $rows[] = [
                    'KATEGORI',
                    $pricingRule->category_name,
                    '',
                    '',
                    '',
                    '',
                ];

                $rows[] = [
                    'No',
                    'Tipe Harga',
                    'Markup',
                    'Pembulatan',
                    'Status',
                    '',
                ];

                $lastCategoryId = $pricingRule->category_id;

                continue;
            }
        }

        /*
         * Rebuild data per grouping supaya
         * seluruh pricing rule masuk ke group yang benar.
         */
        $rows = [];

        $rows[] = [
            'MARKUP DISPLAY · RANGKA',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = ['', '', '', '', '', ''];

        $grouped = $pricingRules->groupBy(function ($item) {
            return implode('-', [
                $item->engine_id,
                $item->location_id,
                $item->category_id,
            ]);
        });

        foreach ($grouped as $group) {

            $first = $group->first();

            $rows[] = ['', '', '', '', '', ''];

            $rows[] = [
                'MESIN',
                $first->engine_name,
                '',
                '',
                '',
                '',
            ];

            $rows[] = [
                'LOKASI',
                $first->location_name,
                '',
                '',
                '',
                '',
            ];

            $rows[] = [
                'KATEGORI',
                $first->category_name,
                '',
                '',
                '',
                '',
            ];

            $rows[] = [
                'No',
                'Tipe Harga',
                'Markup',
                'Pembulatan',
                'Status',
                '',
            ];

            $number = 1;

            foreach (
                $group
                    ->sortBy(function ($item) use ($priceTypeOrder) {
                        return $priceTypeOrder[$item->price_type] ?? 999;
                    })
                as $pricingRule
            ) {
                $rows[] = [
                    $number,
                    $priceTypeLabels[$pricingRule->price_type]
                        ?? $pricingRule->price_type,
                    $pricingRule->markup_percentage,
                    $pricingRule->rounding_value,
                    $pricingRule->status,
                    '',
                ];

                $number++;
            }
        }

        return $rows;
    }

    /**
     * Nama worksheet.
     */
    public function title(): string
    {
        return 'Display Pricing Rules';
    }

    /**
     * Styling worksheet.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                /*
                 * Lebar kolom.
                 */
                $sheet->getColumnDimension('A')->setWidth(12);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(18);
                $sheet->getColumnDimension('F')->setWidth(3);

                /*
                 * Merge title.
                 */
                $sheet->mergeCells('A1:E1');

                $sheet->getStyle('A1:E1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getRowDimension(1)->setRowHeight(26);

                $highestRow = $sheet->getHighestRow();

                for ($row = 3; $row <= $highestRow; $row++) {

                    $label = $sheet
                        ->getCell("A{$row}")
                        ->getValue();

                    /*
                     * MESIN / LOKASI
                     */
                    if (
                        $label === 'MESIN' ||
                        $label === 'LOKASI'
                    ) {

                        $sheet->mergeCells(
                            "B{$row}:E{$row}"
                        );

                        $sheet->getStyle(
                            "A{$row}:E{$row}"
                        )->applyFromArray([
                            'font' => [
                                'bold' => true,
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => [
                                    'rgb' => 'D9EAF7',
                                ],
                            ],
                            'alignment' => [
                                'vertical' =>
                                Alignment::VERTICAL_CENTER,
                            ],
                        ]);

                        $sheet
                            ->getRowDimension($row)
                            ->setRowHeight(22);

                        continue;
                    }

                    /*
                     * KATEGORI
                     */
                    if ($label === 'KATEGORI') {

                        $sheet->mergeCells(
                            "B{$row}:E{$row}"
                        );

                        $sheet->getStyle(
                            "A{$row}:E{$row}"
                        )->applyFromArray([
                            'font' => [
                                'bold' => true,
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => [
                                    'rgb' => 'EDEDED',
                                ],
                            ],
                            'alignment' => [
                                'vertical' =>
                                Alignment::VERTICAL_CENTER,
                            ],
                        ]);

                        $sheet
                            ->getRowDimension($row)
                            ->setRowHeight(22);

                        continue;
                    }

                    /*
                     * HEADER TABLE
                     */
                    if ($label === 'No') {

                        $sheet->getStyle(
                            "A{$row}:E{$row}"
                        )->applyFromArray([
                            'font' => [
                                'bold' => true,
                            ],
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => [
                                    'rgb' => 'F3F4F6',
                                ],
                            ],
                            'alignment' => [
                                'horizontal' =>
                                Alignment::HORIZONTAL_CENTER,
                                'vertical' =>
                                Alignment::VERTICAL_CENTER,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' =>
                                    Border::BORDER_THIN,
                                ],
                            ],
                        ]);

                        $sheet
                            ->getRowDimension($row)
                            ->setRowHeight(22);

                        continue;
                    }

                    /*
                     * DATA ROW
                     */
                    if (
                        $label !== null &&
                        $label !== ''
                    ) {

                        $sheet->getStyle(
                            "A{$row}:E{$row}"
                        )->applyFromArray([
                            'alignment' => [
                                'vertical' =>
                                Alignment::VERTICAL_CENTER,
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' =>
                                    Border::BORDER_THIN,
                                ],
                            ],
                        ]);

                        $sheet->getStyle(
                            "A{$row}"
                        )->getAlignment()->setHorizontal(
                            Alignment::HORIZONTAL_CENTER
                        );

                        $sheet->getStyle(
                            "C{$row}"
                        )->getAlignment()->setHorizontal(
                            Alignment::HORIZONTAL_RIGHT
                        );

                        $sheet->getStyle(
                            "D{$row}"
                        )->getAlignment()->setHorizontal(
                            Alignment::HORIZONTAL_RIGHT
                        );

                        $sheet->getStyle(
                            "E{$row}"
                        )->getAlignment()->setHorizontal(
                            Alignment::HORIZONTAL_CENTER
                        );

                        /*
                         * Format markup percentage.
                         */
                        $sheet->getStyle(
                            "C{$row}"
                        )->getNumberFormat()
                            ->setFormatCode(
                                '0.00"%"'
                            );

                        /*
                         * Format pembulatan.
                         */
                        $sheet->getStyle(
                            "D{$row}"
                        )->getNumberFormat()
                            ->setFormatCode(
                                '"Rp" #,##0'
                            );
                    }
                }

                /*
                 * Wrap text.
                 */
                $sheet->getStyle(
                    "A1:E{$highestRow}"
                )->getAlignment()->setWrapText(true);

                /*
                 * Freeze pane.
                 */
                $sheet->freezePane('A3');
            },
        ];
    }
}

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

class LaminationPricingRulesSheetExport implements FromArray, WithEvents, WithTitle
{
    /**
     * Data yang akan ditampilkan pada Excel.
     */
    public function array(): array
    {
        $pricingRules = DB::table(
            'm_lamination_pricing_rules as pr'
        )
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
            ->leftJoin(
                'm_vendors as v',
                'pr.vendor_id',
                '=',
                'v.id'
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
                'pr.vendor_id',
                'v.name as vendor_name',
                'pr.category_id',
                'c.name as category_name',
                'pr.price_type',
                'pr.markup_percentage',
                'pr.rounding_value',
                'pr.status'
            )
            ->orderBy('e.name', 'asc')
            ->orderBy('l.name', 'asc')
            ->orderBy('v.name', 'asc')
            ->orderBy('c.name', 'asc')
            ->get();

        $rows = [];

        /*
        |--------------------------------------------------------------------------
        | JUDUL UTAMA
        |--------------------------------------------------------------------------
        */

        $rows[] = [
            'MARKUP HARGA LAMINASI',
            '',
            '',
            '',
            '',
            '',
        ];

        $rows[] = [
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        /*
        |--------------------------------------------------------------------------
        | GROUP BERDASARKAN
        | MESIN + LOKASI + VENDOR + KATEGORI
        |--------------------------------------------------------------------------
        */

        $grouped = $pricingRules->groupBy(function ($item) {
            return
                $item->engine_id . '-' .
                $item->location_id . '-' .
                ($item->vendor_id ?? 'null') . '-' .
                $item->category_id;
        });

        $lastEngineId = null;
        $lastLocationId = null;
        $lastVendorId = null;

        foreach ($grouped as $group) {
            $first = $group->first();

            /*
            |--------------------------------------------------------------------------
            | JIKA PINDAH MESIN
            |--------------------------------------------------------------------------
            */

            if ($lastEngineId !== $first->engine_id) {

                /*
                |--------------------------------------------------------------------------
                | SPACER ANTAR MESIN
                |--------------------------------------------------------------------------
                */

                if ($lastEngineId !== null) {
                    $rows[] = [
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | HEADER MESIN
                |--------------------------------------------------------------------------
                */

                $rows[] = [
                    'MESIN',
                    $first->engine_name,
                    '',
                    '',
                    '',
                    '',
                ];

                /*
                |--------------------------------------------------------------------------
                | HEADER LOKASI
                |--------------------------------------------------------------------------
                */

                $rows[] = [
                    'LOKASI',
                    $first->location_name,
                    '',
                    '',
                    '',
                    '',
                ];

                /*
                |--------------------------------------------------------------------------
                | HEADER VENDOR
                |--------------------------------------------------------------------------
                */

                $rows[] = [
                    'VENDOR',
                    $first->vendor_name ?: '-',
                    '',
                    '',
                    '',
                    '',
                ];

                /*
                |--------------------------------------------------------------------------
                | HEADER KATEGORI
                |--------------------------------------------------------------------------
                */

                $rows[] = [
                    'KATEGORI',
                    $first->category_name,
                    '',
                    '',
                    '',
                    '',
                ];

                /*
                |--------------------------------------------------------------------------
                | HEADER TABEL
                |--------------------------------------------------------------------------
                */

                $rows[] = [
                    'No',
                    'Tipe Harga',
                    'Markup',
                    'Pembulatan',
                    'Status',
                    '',
                ];

                $lastEngineId = $first->engine_id;
                $lastLocationId = $first->location_id;
                $lastVendorId = $first->vendor_id ?? null;
            } else {

                /*
                |--------------------------------------------------------------------------
                | MASIH MESIN YANG SAMA
                | TETAPI LOKASI BERBEDA
                |--------------------------------------------------------------------------
                */

                if ($lastLocationId !== $first->location_id) {

                    $rows[] = [
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                    ];

                    /*
                    |--------------------------------------------------------------------------
                    | HEADER LOKASI
                    |--------------------------------------------------------------------------
                    */

                    $rows[] = [
                        'LOKASI',
                        $first->location_name,
                        '',
                        '',
                        '',
                        '',
                    ];

                    /*
                    |--------------------------------------------------------------------------
                    | HEADER VENDOR
                    |--------------------------------------------------------------------------
                    */

                    $rows[] = [
                        'VENDOR',
                        $first->vendor_name ?: '-',
                        '',
                        '',
                        '',
                        '',
                    ];

                    /*
                    |--------------------------------------------------------------------------
                    | HEADER KATEGORI
                    |--------------------------------------------------------------------------
                    */

                    $rows[] = [
                        'KATEGORI',
                        $first->category_name,
                        '',
                        '',
                        '',
                        '',
                    ];

                    /*
                    |--------------------------------------------------------------------------
                    | HEADER TABEL
                    |--------------------------------------------------------------------------
                    */

                    $rows[] = [
                        'No',
                        'Tipe Harga',
                        'Markup',
                        'Pembulatan',
                        'Status',
                        '',
                    ];

                    $lastLocationId = $first->location_id;
                    $lastVendorId = $first->vendor_id ?? null;
                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | MESIN + LOKASI SAMA
                    | TETAPI VENDOR BERBEDA
                    |--------------------------------------------------------------------------
                    */

                    if ($lastVendorId !== ($first->vendor_id ?? null)) {

                        $rows[] = [
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                        ];

                        /*
                        |--------------------------------------------------------------------------
                        | HEADER VENDOR
                        |--------------------------------------------------------------------------
                        */

                        $rows[] = [
                            'VENDOR',
                            $first->vendor_name ?: '-',
                            '',
                            '',
                            '',
                            '',
                        ];

                        /*
                        |--------------------------------------------------------------------------
                        | HEADER KATEGORI
                        |--------------------------------------------------------------------------
                        */

                        $rows[] = [
                            'KATEGORI',
                            $first->category_name,
                            '',
                            '',
                            '',
                            '',
                        ];

                        /*
                        |--------------------------------------------------------------------------
                        | HEADER TABEL
                        |--------------------------------------------------------------------------
                        */

                        $rows[] = [
                            'No',
                            'Tipe Harga',
                            'Markup',
                            'Pembulatan',
                            'Status',
                            '',
                        ];

                        $lastVendorId = $first->vendor_id ?? null;
                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | MESIN + LOKASI + VENDOR SAMA
                        | TETAPI KATEGORI BERBEDA
                        |--------------------------------------------------------------------------
                        */

                        $rows[] = [
                            '',
                            '',
                            '',
                            '',
                            '',
                            '',
                        ];

                        /*
                        |--------------------------------------------------------------------------
                        | HEADER KATEGORI
                        |--------------------------------------------------------------------------
                        */

                        $rows[] = [
                            'KATEGORI',
                            $first->category_name,
                            '',
                            '',
                            '',
                            '',
                        ];

                        /*
                        |--------------------------------------------------------------------------
                        | HEADER TABEL
                        |--------------------------------------------------------------------------
                        */

                        $rows[] = [
                            'No',
                            'Tipe Harga',
                            'Markup',
                            'Pembulatan',
                            'Status',
                            '',
                        ];
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | URUTAN TIPE HARGA
            |--------------------------------------------------------------------------
            |
            | 1. Harga Umum
            | 2. Harga Divisi
            | 3. Harga Polos
            |--------------------------------------------------------------------------
            */

            $priceTypeOrder = [
                'general' => 1,
                'division' => 2,
                'plain' => 3,
            ];

            $group = $group->sortBy(
                function ($item) use ($priceTypeOrder) {
                    return $priceTypeOrder[$item->price_type] ?? 99;
                }
            );

            $no = 1;

            foreach ($group as $item) {
                $priceTypeLabels = [
                    'general' => 'Harga Umum',
                    'division' => 'Harga Divisi',
                    'plain' => 'Harga Polos',
                ];

                $rows[] = [
                    $no,
                    $priceTypeLabels[$item->price_type] ?? $item->price_type,
                    $item->markup_percentage,
                    $item->rounding_value,
                    $item->status,
                    '',
                ];

                $no++;
            }
        }

        return $rows;
    }

    /**
     * Nama worksheet.
     */
    public function title(): string
    {
        return 'Pricing Rules Laminasi';
    }

    /**
     * Styling Excel setelah sheet dibuat.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet =
                    $event->sheet->getDelegate();

                $highestRow =
                    $sheet->getHighestRow();

                /*
                |--------------------------------------------------------------------------
                | LEBAR KOLOM
                |--------------------------------------------------------------------------
                */

                $sheet->getColumnDimension('A')
                    ->setWidth(12);

                $sheet->getColumnDimension('B')
                    ->setWidth(25);

                $sheet->getColumnDimension('C')
                    ->setWidth(18);

                $sheet->getColumnDimension('D')
                    ->setWidth(20);

                $sheet->getColumnDimension('E')
                    ->setWidth(18);

                $sheet->getColumnDimension('F')
                    ->setWidth(3);

                /*
                |--------------------------------------------------------------------------
                | JUDUL UTAMA
                |--------------------------------------------------------------------------
                */

                $sheet->mergeCells('A1:E1');

                $sheet->getStyle('A1:E1')
                    ->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'size' => 14,
                        ],
                        'alignment' => [
                            'horizontal' =>
                            Alignment::HORIZONTAL_CENTER,
                            'vertical' =>
                            Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                $sheet->getRowDimension(1)
                    ->setRowHeight(26);

                /*
                |--------------------------------------------------------------------------
                | CARI HEADER
                |--------------------------------------------------------------------------
                */

                for (
                    $row = 3;
                    $row <= $highestRow;
                    $row++
                ) {
                    $value =
                        $sheet
                        ->getCell('A' . $row)
                        ->getValue();

                    /*
                    |--------------------------------------------------------------------------
                    | HEADER MESIN
                    |--------------------------------------------------------------------------
                    */

                    if ($value === 'MESIN') {
                        $sheet->mergeCells(
                            'B' . $row . ':E' . $row
                        );

                        $sheet
                            ->getStyle(
                                'A' . $row . ':E' . $row
                            )
                            ->applyFromArray([
                                'font' => [
                                    'bold' => true,
                                ],
                                'fill' => [
                                    'fillType' =>
                                    Fill::FILL_SOLID,
                                    'color' => [
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
                    |--------------------------------------------------------------------------
                    | HEADER LOKASI
                    |--------------------------------------------------------------------------
                    */

                    if ($value === 'LOKASI') {
                        $sheet->mergeCells(
                            'B' . $row . ':E' . $row
                        );

                        $sheet
                            ->getStyle(
                                'A' . $row . ':E' . $row
                            )
                            ->applyFromArray([
                                'font' => [
                                    'bold' => true,
                                ],
                                'fill' => [
                                    'fillType' =>
                                    Fill::FILL_SOLID,
                                    'color' => [
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
                    |--------------------------------------------------------------------------
                    | HEADER VENDOR
                    |--------------------------------------------------------------------------
                    */

                    if ($value === 'VENDOR') {
                        $sheet->mergeCells(
                            'B' . $row . ':E' . $row
                        );

                        $sheet
                            ->getStyle(
                                'A' . $row . ':E' . $row
                            )
                            ->applyFromArray([
                                'font' => [
                                    'bold' => true,
                                ],
                                'fill' => [
                                    'fillType' =>
                                    Fill::FILL_SOLID,
                                    'color' => [
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
                    |--------------------------------------------------------------------------
                    | HEADER KATEGORI
                    |--------------------------------------------------------------------------
                    */

                    if ($value === 'KATEGORI') {
                        $sheet->mergeCells(
                            'B' . $row . ':E' . $row
                        );

                        $sheet
                            ->getStyle(
                                'A' . $row . ':E' . $row
                            )
                            ->applyFromArray([
                                'font' => [
                                    'bold' => true,
                                ],
                                'fill' => [
                                    'fillType' =>
                                    Fill::FILL_SOLID,
                                    'color' => [
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
                    |--------------------------------------------------------------------------
                    | HEADER TABEL
                    |--------------------------------------------------------------------------
                    */

                    if ($value === 'No') {
                        $sheet
                            ->getStyle(
                                'A' . $row . ':E' . $row
                            )
                            ->applyFromArray([
                                'font' => [
                                    'bold' => true,
                                ],
                                'fill' => [
                                    'fillType' =>
                                    Fill::FILL_SOLID,
                                    'color' => [
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
                }

                /*
                |--------------------------------------------------------------------------
                | STYLING DATA ROWS
                |--------------------------------------------------------------------------
                */

                for (
                    $row = 3;
                    $row <= $highestRow;
                    $row++
                ) {
                    $value =
                        $sheet
                        ->getCell('A' . $row)
                        ->getValue();

                    /*
                    |--------------------------------------------------------------------------
                    | LEWATI HEADER DAN SPACER
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $value === 'MESIN' ||
                        $value === 'LOKASI' ||
                        $value === 'VENDOR' ||
                        $value === 'KATEGORI' ||
                        $value === 'No' ||
                        $value === null ||
                        $value === ''
                    ) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | BORDER DATA
                    |--------------------------------------------------------------------------
                    */

                    $sheet
                        ->getStyle(
                            'A' . $row . ':E' . $row
                        )
                        ->applyFromArray([
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' =>
                                    Border::BORDER_THIN,
                                ],
                            ],
                            'alignment' => [
                                'vertical' =>
                                Alignment::VERTICAL_CENTER,
                            ],
                        ]);

                    /*
                    |--------------------------------------------------------------------------
                    | NO CENTER
                    |--------------------------------------------------------------------------
                    */

                    $sheet
                        ->getStyle('A' . $row)
                        ->getAlignment()
                        ->setHorizontal(
                            Alignment::HORIZONTAL_CENTER
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | MARKUP RIGHT
                    |--------------------------------------------------------------------------
                    */

                    $sheet
                        ->getStyle('C' . $row)
                        ->getAlignment()
                        ->setHorizontal(
                            Alignment::HORIZONTAL_RIGHT
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | PEMBULATAN RIGHT
                    |--------------------------------------------------------------------------
                    */

                    $sheet
                        ->getStyle('D' . $row)
                        ->getAlignment()
                        ->setHorizontal(
                            Alignment::HORIZONTAL_RIGHT
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | STATUS CENTER
                    |--------------------------------------------------------------------------
                    */

                    $sheet
                        ->getStyle('E' . $row)
                        ->getAlignment()
                        ->setHorizontal(
                            Alignment::HORIZONTAL_CENTER
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | FORMAT MARKUP
                    |--------------------------------------------------------------------------
                    |
                    | Contoh:
                    | 10 -> 10.00%
                    |--------------------------------------------------------------------------
                    */

                    $sheet
                        ->getStyle('C' . $row)
                        ->getNumberFormat()
                        ->setFormatCode(
                            '0.00"%"'
                        );

                    /*
                    |--------------------------------------------------------------------------
                    | FORMAT PEMBULATAN RUPIAH
                    |--------------------------------------------------------------------------
                    |
                    | Contoh:
                    | 5000 -> Rp 5.000
                    |--------------------------------------------------------------------------
                    */

                    $sheet
                        ->getStyle('D' . $row)
                        ->getNumberFormat()
                        ->setFormatCode(
                            '"Rp" #,##0'
                        );
                }

                /*
                |--------------------------------------------------------------------------
                | WRAP TEXT
                |--------------------------------------------------------------------------
                */

                $sheet
                    ->getStyle(
                        'A1:E' . $highestRow
                    )
                    ->getAlignment()
                    ->setWrapText(true);

                /*
                |--------------------------------------------------------------------------
                | FREEZE PANE
                |--------------------------------------------------------------------------
                */

                $sheet->freezePane('A3');
            },
        ];
    }
}

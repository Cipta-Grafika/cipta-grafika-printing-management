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

class ProductionCostFinishingsSheetExport implements
    FromArray,
    WithTitle,
    WithEvents
{
    protected int $engineId;
    protected string $engineName;

    public function __construct(
        int $engineId,
        string $engineName
    ) {
        $this->engineId = $engineId;
        $this->engineName = $engineName;
    }

    /*
    |--------------------------------------------------------------------------
    | DATA EXCEL
    |--------------------------------------------------------------------------
    */

    public function array(): array
    {
        $rows = [];

        /*
        |--------------------------------------------------------------------------
        | AMBIL PRODUCTION COST
        |--------------------------------------------------------------------------
        */

        $productionCosts = DB::table(
            'm_production_costs as pc'
        )
            ->join(
                'm_production_cost_locations as pcl',
                'pc.id',
                '=',
                'pcl.production_cost_id'
            )
            ->join(
                'm_locations as l',
                'pcl.location_id',
                '=',
                'l.id'
            )
            ->where(
                'pc.engine_id',
                $this->engineId
            )
            ->select(
                'pc.id as production_cost_id',
                'l.id as location_id',
                'l.name as location_name'
            )
            ->orderBy('l.name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | JIKA TIDAK ADA DATA
        |--------------------------------------------------------------------------
        */

        if ($productionCosts->isEmpty()) {
            return [
                [
                    'Tidak ada data Production Cost untuk mesin ini.'
                ]
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | PRODUCTION COST IDS
        |--------------------------------------------------------------------------
        */

        $productionCostIds = $productionCosts
            ->pluck('production_cost_id')
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | AMBIL DETAILS
        |--------------------------------------------------------------------------
        */

        $details = DB::table(
            'm_production_cost_details as pcd'
        )
            ->join(
                'm_materials as m',
                'm.id',
                '=',
                'pcd.material_id'
            )
            ->join(
                'm_categories as c',
                'c.id',
                '=',
                'm.category_id'
            )
            ->whereIn(
                'pcd.production_cost_id',
                $productionCostIds
            )
            ->select(
                'pcd.id',
                'pcd.production_cost_id',
                'c.id as category_id',
                'c.name as category_name',
                'm.id as material_id',
                'm.material_name',
                'pcd.price_per_meter',
                'pcd.production_cost',
                'pcd.finishing_cost',
                'pcd.total_cost',
                'pcd.vendor_price',
                'pcd.general_price',
                'pcd.division_price',
                'pcd.plain_price'
            )
            ->orderBy('c.name')
            ->orderBy('m.material_name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | DETAIL IDS
        |--------------------------------------------------------------------------
        */

        $detailIds = $details
            ->pluck('id')
            ->unique()
            ->values();

        /*
        |--------------------------------------------------------------------------
        | AMBIL WIDTH
        |--------------------------------------------------------------------------
        */

        $widths = DB::table(
            'm_production_cost_detail_widths as pcdw'
        )
            ->join(
                'm_material_sizes as ms',
                'pcdw.material_size_id',
                '=',
                'ms.id'
            )
            ->whereIn(
                'pcdw.production_cost_detail_id',
                $detailIds
            )
            ->select(
                'pcdw.production_cost_detail_id',
                'pcdw.material_size_id',
                'ms.width',
                'ms.unit'
            )
            ->orderBy('ms.width')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GROUP LOCATION
        |--------------------------------------------------------------------------
        */

        $locations = $productionCosts
            ->groupBy('location_id');

        $no = 1;

        /*
        |--------------------------------------------------------------------------
        | MESIN HEADER
        |--------------------------------------------------------------------------
        */

        $rows[] = [
            'MESIN',
            strtoupper($this->engineName),
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];

        /*
        |--------------------------------------------------------------------------
        | SPACER SETELAH MESIN
        |--------------------------------------------------------------------------
        */

        $rows[] = array_fill(
            0,
            11,
            ' '
        );

        /*
        |--------------------------------------------------------------------------
        | LOOP LOKASI
        |--------------------------------------------------------------------------
        */

        foreach ($locations as $locationRows) {

            $location = $locationRows->first();

            /*
            |--------------------------------------------------------------------------
            | CEK OUTSOURCING
            |--------------------------------------------------------------------------
            */

            $isOutsourcing =
                strtolower(trim($location->location_name)) ===
                'outsourcing';

            /*
            |--------------------------------------------------------------------------
            | PRODUCTION COST IDS PER LOKASI
            |--------------------------------------------------------------------------
            */

            $locationProductionCostIds =
                $locationRows
                ->pluck('production_cost_id')
                ->unique()
                ->values();

            /*
            |--------------------------------------------------------------------------
            | DETAILS PER LOKASI
            |--------------------------------------------------------------------------
            */

            $locationDetails =
                $details->whereIn(
                    'production_cost_id',
                    $locationProductionCostIds
                );

            if ($locationDetails->isEmpty()) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | LOKASI HEADER
            |--------------------------------------------------------------------------
            */

            $rows[] = [
                'LOKASI',
                strtoupper(
                    $location->location_name
                ),
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ];

            /*
            |--------------------------------------------------------------------------
            | SPACER SETELAH LOKASI
            |--------------------------------------------------------------------------
            */

            $rows[] = array_fill(
                0,
                11,
                ' '
            );

            /*
            |--------------------------------------------------------------------------
            | GROUP KATEGORI
            |--------------------------------------------------------------------------
            */

            $categories = $locationDetails
                ->groupBy('category_id')
                ->sortBy(function ($categoryRows) {
                    return strtolower(
                        $categoryRows
                            ->first()
                            ->category_name
                    );
                });

            /*
            |--------------------------------------------------------------------------
            | LOOP KATEGORI
            |--------------------------------------------------------------------------
            */

            foreach ($categories as $categoryRows) {

                $category =
                    $categoryRows->first();

                /*
                |--------------------------------------------------------------------------
                | KATEGORI HEADER
                |--------------------------------------------------------------------------
                */

                $rows[] = [
                    'KATEGORI',
                    strtoupper(
                        $category->category_name
                    ),
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                ];

                /*
                |--------------------------------------------------------------------------
                | TABLE HEADER
                |--------------------------------------------------------------------------
                */

                if ($isOutsourcing) {

                    /*
                    |------------------------------------------------------------------
                    | OUTSOURCING
                    |------------------------------------------------------------------
                    */

                    $rows[] = [
                        'No',
                        'Item',
                        'Lebar',
                        'Panjang',
                        'Harga Vendor',
                        'Harga Umum',
                        'Harga Divisi',
                        'Harga Polos',
                    ];
                } else {

                    /*
                    |------------------------------------------------------------------
                    | NON-OUTSOURCING
                    |------------------------------------------------------------------
                    */

                    $rows[] = [
                        'No',
                        'Item',
                        'Lebar',
                        'Panjang',
                        'Per Meter',
                        'Produksi',
                        'Finishing',
                        'Total Cost',
                        'Harga Umum',
                        'Harga Divisi',
                        'Harga Polos',
                    ];
                }

                /*
                |--------------------------------------------------------------------------
                | GROUP MATERIAL
                |--------------------------------------------------------------------------
                */

                $materialGroups = $categoryRows
                    ->groupBy(function ($detail) use ($isOutsourcing) {

                        $groupData = [
                            $detail->material_id,
                        ];

                        /*
                        |--------------------------------------------------------------------------
                        | OUTSOURCING
                        |--------------------------------------------------------------------------
                        |
                        | Vendor Price menjadi bagian dari identitas
                        | grouping material.
                        |
                        */

                        if ($isOutsourcing) {

                            $groupData[] = number_format(
                                (float) $detail->vendor_price,
                                2,
                                '.',
                                ''
                            );
                        } else {

                            /*
                            |--------------------------------------------------------------------------
                            | NON-OUTSOURCING
                            |--------------------------------------------------------------------------
                            */

                            $groupData[] = number_format(
                                (float) $detail->price_per_meter,
                                2,
                                '.',
                                ''
                            );

                            $groupData[] = number_format(
                                (float) $detail->production_cost,
                                2,
                                '.',
                                ''
                            );

                            $groupData[] = number_format(
                                (float) $detail->finishing_cost,
                                2,
                                '.',
                                ''
                            );

                            $groupData[] = number_format(
                                (float) $detail->total_cost,
                                2,
                                '.',
                                ''
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | HARGA JUAL
                        |--------------------------------------------------------------------------
                        */

                        $groupData[] = number_format(
                            (float) $detail->general_price,
                            2,
                            '.',
                            ''
                        );

                        $groupData[] = number_format(
                            (float) $detail->division_price,
                            2,
                            '.',
                            ''
                        );

                        $groupData[] = number_format(
                            (float) $detail->plain_price,
                            2,
                            '.',
                            ''
                        );

                        return implode('|', $groupData);
                    });

                /*
                |--------------------------------------------------------------------------
                | LOOP MATERIAL
                |--------------------------------------------------------------------------
                */

                foreach ($materialGroups as $detailRows) {

                    $detail =
                        $detailRows->first();

                    /*
                    |--------------------------------------------------------------------------
                    | DETAIL IDS UNTUK WIDTH
                    |--------------------------------------------------------------------------
                    */

                    $detailIdsForWidths =
                        $detailRows
                        ->pluck('id')
                        ->unique()
                        ->values();

                    /*
                    |--------------------------------------------------------------------------
                    | WIDTH MATERIAL
                    |--------------------------------------------------------------------------
                    */

                    $materialWidths =
                        $widths
                        ->whereIn(
                            'production_cost_detail_id',
                            $detailIdsForWidths
                        )
                        ->unique(
                            'material_size_id'
                        )
                        ->sortBy(
                            function ($width) {
                                return (float)
                                $width->width;
                            }
                        )
                        ->values();

                    /*
                    |--------------------------------------------------------------------------
                    | FORMAT WIDTH
                    |--------------------------------------------------------------------------
                    */

                    $widthText =
                        $materialWidths
                        ->map(
                            function ($width) {
                                return
                                    $width->width
                                    . ' '
                                    . $width->unit;
                            }
                        )
                        ->implode(', ');

                    /*
                    |--------------------------------------------------------------------------
                    | DATA MATERIAL
                    |--------------------------------------------------------------------------
                    */

                    if ($isOutsourcing) {

                        /*
                        |--------------------------------------------------------------------------
                        | OUTSOURCING
                        |--------------------------------------------------------------------------
                        */

                        $rows[] = [
                            $no++,
                            $detail->material_name,
                            $widthText,
                            '-',
                            $detail->vendor_price,
                            $detail->general_price,
                            $detail->division_price,
                            $detail->plain_price,
                        ];
                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | NON-OUTSOURCING
                        |--------------------------------------------------------------------------
                        */

                        $rows[] = [
                            $no++,
                            $detail->material_name,
                            $widthText,
                            '-',
                            $detail->price_per_meter,
                            $detail->production_cost,
                            $detail->finishing_cost,
                            $detail->total_cost,
                            $detail->general_price,
                            $detail->division_price,
                            $detail->plain_price,
                        ];
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | SPACER SETELAH KATEGORI
                |--------------------------------------------------------------------------
                */

                $rows[] = array_fill(
                    0,
                    11,
                    ' '
                );
            }

            /*
            |--------------------------------------------------------------------------
            | SPACER SETELAH LOKASI
            |--------------------------------------------------------------------------
            */

            $rows[] = array_fill(
                0,
                11,
                ' '
            );
        }

        return $rows;
    }

    /*
    |--------------------------------------------------------------------------
    | SHEET TITLE
    |--------------------------------------------------------------------------
    */

    public function title(): string
    {
        $title = preg_replace(
            '/[\\\\\/\?\*\[\]\:]/',
            '',
            $this->engineName
        );

        $title = trim($title);

        if ($title === '') {
            $title = 'Mesin';
        }

        return substr(
            $title,
            0,
            31
        );
    }

    /*
    |--------------------------------------------------------------------------
    | EVENTS
    |--------------------------------------------------------------------------
    */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (
                AfterSheet $event
            ) {

                $sheet =
                    $event
                    ->sheet
                    ->getDelegate();

                $highestRow =
                    $sheet->getHighestRow();

                $highestColumn =
                    $sheet->getHighestColumn();

                /*
                |--------------------------------------------------------------------------
                | COLUMN WIDTH
                |--------------------------------------------------------------------------
                */

                $columnWidths = [
                    'A' => 8,
                    'B' => 38,
                    'C' => 25,
                    'D' => 14,
                    'E' => 18,
                    'F' => 18,
                    'G' => 18,
                    'H' => 18,
                    'I' => 18,
                    'J' => 18,
                    'K' => 18,
                ];

                foreach (
                    $columnWidths
                    as $column => $width
                ) {
                    $sheet
                        ->getColumnDimension($column)
                        ->setWidth($width);
                }

                /*
                |--------------------------------------------------------------------------
                | VERTICAL ALIGNMENT
                |--------------------------------------------------------------------------
                */

                $sheet
                    ->getStyle(
                        'A1:' .
                            $highestColumn .
                            $highestRow
                    )
                    ->getAlignment()
                    ->setVertical(
                        Alignment::VERTICAL_CENTER
                    );

                /*
                |--------------------------------------------------------------------------
                | MESIN HEADER
                |--------------------------------------------------------------------------
                */

                $sheet->mergeCells(
                    'B1:K1'
                );

                $sheet
                    ->getStyle('A1:K1')
                    ->getFont()
                    ->setBold(true);

                $sheet
                    ->getStyle('A1:K1')
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_LEFT
                    );

                /*
                |--------------------------------------------------------------------------
                | TRACKING TABLE
                |--------------------------------------------------------------------------
                |
                | 11 = Non-Outsourcing
                |  8 = Outsourcing
                |
                */

                $currentTableColumnCount = 11;

                /*
                |--------------------------------------------------------------------------
                | LOOP ROW
                |--------------------------------------------------------------------------
                */

                for (
                    $row = 1;
                    $row <= $highestRow;
                    $row++
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL TIPE ROW
                    |--------------------------------------------------------------------------
                    */

                    $type =
                        $sheet
                        ->getCell("A{$row}")
                        ->getValue();

                    /*
                    |--------------------------------------------------------------------------
                    | SPACER ROW
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $type === null
                        || trim((string) $type) === ''
                    ) {
                        $sheet
                            ->getRowDimension($row)
                            ->setRowHeight(10);

                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | LOKASI
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $type === 'LOKASI'
                    ) {

                        $sheet->mergeCells(
                            "B{$row}:K{$row}"
                        );

                        $sheet
                            ->getStyle(
                                "A{$row}:K{$row}"
                            )
                            ->getFont()
                            ->setBold(true);

                        $sheet
                            ->getStyle(
                                "A{$row}:K{$row}"
                            )
                            ->getFill()
                            ->setFillType(
                                Fill::FILL_SOLID
                            )
                            ->getStartColor()
                            ->setARGB(
                                'D9EAF7'
                            );

                        $sheet
                            ->getStyle(
                                "A{$row}:K{$row}"
                            )
                            ->getAlignment()
                            ->setHorizontal(
                                Alignment::HORIZONTAL_LEFT
                            );

                        $sheet
                            ->getRowDimension($row)
                            ->setRowHeight(22);

                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | KATEGORI
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $type === 'KATEGORI'
                    ) {

                        $sheet->mergeCells(
                            "B{$row}:K{$row}"
                        );

                        $sheet
                            ->getStyle(
                                "A{$row}:K{$row}"
                            )
                            ->getFont()
                            ->setBold(true);

                        $sheet
                            ->getStyle(
                                "A{$row}:K{$row}"
                            )
                            ->getFill()
                            ->setFillType(
                                Fill::FILL_SOLID
                            )
                            ->getStartColor()
                            ->setARGB(
                                'EDEDED'
                            );

                        $sheet
                            ->getStyle(
                                "A{$row}:K{$row}"
                            )
                            ->getAlignment()
                            ->setHorizontal(
                                Alignment::HORIZONTAL_LEFT
                            );

                        $sheet
                            ->getRowDimension($row)
                            ->setRowHeight(20);

                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | TABLE HEADER
                    |--------------------------------------------------------------------------
                    */

                    if (
                        $type === 'No'
                        && $sheet
                        ->getCell("B{$row}")
                        ->getValue()
                        === 'Item'
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | DETEKSI JUMLAH KOLOM
                        |--------------------------------------------------------------------------
                        */

                        $headerE =
                            $sheet
                            ->getCell("E{$row}")
                            ->getValue();

                        if (
                            $headerE === 'Harga Vendor'
                        ) {
                            $currentTableColumnCount = 8;
                        } else {
                            $currentTableColumnCount = 11;
                        }

                        $lastTableColumn =
                            $currentTableColumnCount === 8
                            ? 'H'
                            : 'K';

                        /*
                        |--------------------------------------------------------------------------
                        | STYLE TABLE HEADER
                        |--------------------------------------------------------------------------
                        */

                        $sheet
                            ->getStyle(
                                "A{$row}:{$lastTableColumn}{$row}"
                            )
                            ->getFont()
                            ->setBold(true);

                        $sheet
                            ->getStyle(
                                "A{$row}:{$lastTableColumn}{$row}"
                            )
                            ->getAlignment()
                            ->setHorizontal(
                                Alignment::HORIZONTAL_CENTER
                            );

                        $sheet
                            ->getStyle(
                                "A{$row}:{$lastTableColumn}{$row}"
                            )
                            ->getBorders()
                            ->getAllBorders()
                            ->setBorderStyle(
                                Border::BORDER_THIN
                            );

                        $sheet
                            ->getStyle(
                                "A{$row}:{$lastTableColumn}{$row}"
                            )
                            ->getFill()
                            ->setFillType(
                                Fill::FILL_SOLID
                            )
                            ->getStartColor()
                            ->setARGB(
                                'F3F4F6'
                            );

                        $sheet
                            ->getRowDimension($row)
                            ->setRowHeight(24);

                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | DATA MATERIAL
                    |--------------------------------------------------------------------------
                    */

                    if (
                        is_numeric(
                            $sheet
                                ->getCell("A{$row}")
                                ->getValue()
                        )
                    ) {

                        $lastTableColumn =
                            $currentTableColumnCount === 8
                            ? 'H'
                            : 'K';

                        /*
                        |--------------------------------------------------------------------------
                        | BORDER
                        |--------------------------------------------------------------------------
                        */

                        $sheet
                            ->getStyle(
                                "A{$row}:{$lastTableColumn}{$row}"
                            )
                            ->getBorders()
                            ->getAllBorders()
                            ->setBorderStyle(
                                Border::BORDER_THIN
                            );

                        /*
                        |--------------------------------------------------------------------------
                        | NO
                        |--------------------------------------------------------------------------
                        */

                        $sheet
                            ->getStyle("A{$row}")
                            ->getAlignment()
                            ->setHorizontal(
                                Alignment::HORIZONTAL_CENTER
                            );

                        /*
                        |--------------------------------------------------------------------------
                        | WIDTH & LENGTH
                        |--------------------------------------------------------------------------
                        */

                        $sheet
                            ->getStyle(
                                "C{$row}:D{$row}"
                            )
                            ->getAlignment()
                            ->setHorizontal(
                                Alignment::HORIZONTAL_CENTER
                            );

                        /*
                        |--------------------------------------------------------------------------
                        | NUMBER FORMAT
                        |--------------------------------------------------------------------------
                        */

                        $numberStartColumn = 'E';

                        $numberEndColumn =
                            $currentTableColumnCount === 8
                            ? 'H'
                            : 'K';

                        $sheet
                            ->getStyle(
                                "{$numberStartColumn}{$row}:{$numberEndColumn}{$row}"
                            )
                            ->getNumberFormat()
                            ->setFormatCode(
                                '#,##0.00'
                            );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | FREEZE PANE
                |--------------------------------------------------------------------------
                */

                $sheet->freezePane('A4');
            },
        ];
    }
}

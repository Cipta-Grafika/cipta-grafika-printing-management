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

class ProductionCostFinishingLaminationsSheetExport implements
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
        | AMBIL PRODUCTION COST LAMINASI
        |--------------------------------------------------------------------------
        */

        $productionCosts = DB::table(
            'm_production_costs as pc'
        )
            ->join(
                'm_lamination_production_cost_details as lpcd',
                'pc.id',
                '=',
                'lpcd.production_cost_id'
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
            ->distinct()
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
                    'Tidak ada data Production Cost Laminasi untuk mesin ini.'
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
        | AMBIL CATEGORY
        |--------------------------------------------------------------------------
        */

        $categories = DB::table(
            'm_production_cost_categories as pcc'
        )
            ->join(
                'm_categories as c',
                'c.id',
                '=',
                'pcc.category_id'
            )
            ->whereIn(
                'pcc.production_cost_id',
                $productionCostIds
            )
            ->select(
                'pcc.production_cost_id',
                'c.id as category_id',
                'c.name as category_name'
            )
            ->orderBy('c.name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | AMBIL DETAILS LAMINASI
        |--------------------------------------------------------------------------
        */

        $details = DB::table(
            'm_lamination_production_cost_details as lpcd'
        )
            ->join(
                'm_laminations as l',
                'l.id',
                '=',
                'lpcd.lamination_id'
            )
            ->leftJoin(
                'm_vendors as v',
                'v.id',
                '=',
                'lpcd.vendor_id'
            )
            ->whereIn(
                'lpcd.production_cost_id',
                $productionCostIds
            )
            ->select(
                'lpcd.id',
                'lpcd.production_cost_id',
                'lpcd.lamination_id',
                'l.name as lamination_name',

                'lpcd.vendor_id',
                'v.name as vendor_name',
                'lpcd.vendor_price',

                'lpcd.price_per_meter',
                'lpcd.production_cost',
                'lpcd.finishing_cost',
                'lpcd.total_cost',
                'lpcd.general_price',
                'lpcd.division_price',
                'lpcd.plain_price'
            )
            ->orderBy('l.name')
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
        | AMBIL SIZE LAMINASI
        |--------------------------------------------------------------------------
        */

        $sizes = collect();

        if ($detailIds->isNotEmpty()) {
            $sizes = DB::table(
                'm_lamination_production_cost_detail_sizes as lpcds'
            )
                ->join(
                    'm_lamination_sizes as ls',
                    'lpcds.lamination_size_id',
                    '=',
                    'ls.id'
                )
                ->whereIn(
                    'lpcds.lamination_production_cost_detail_id',
                    $detailIds
                )
                ->select(
                    'lpcds.lamination_production_cost_detail_id',
                    'lpcds.lamination_size_id',
                    'ls.width',
                    'ls.length',
                    'ls.unit'
                )
                ->orderBy('ls.width')
                ->orderBy('ls.length')
                ->get();
        }

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
            | DETEKSI OUTSOURCING
            |--------------------------------------------------------------------------
            */

            $isOutsourcing =
                strtolower(
                    trim($location->location_name)
                ) === 'outsourcing';

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
            | OUTSOURCING
            |--------------------------------------------------------------------------
            */

            if ($isOutsourcing) {

                /*
                |--------------------------------------------------------------------------
                | GROUP VENDOR
                |--------------------------------------------------------------------------
                */

                $vendors = $locationDetails
                    ->groupBy(function ($detail) {
                        return $detail->vendor_id ?? 'null';
                    })
                    ->sortBy(function ($vendorRows) {
                        $vendor = $vendorRows->first();

                        return strtolower(
                            $vendor->vendor_name ?? '-'
                        );
                    });

                foreach ($vendors as $vendorRows) {

                    $vendor = $vendorRows->first();

                    /*
                    |--------------------------------------------------------------------------
                    | VENDOR HEADER
                    |--------------------------------------------------------------------------
                    */

                    $rows[] = [
                        'VENDOR',
                        strtoupper(
                            $vendor->vendor_name ?? '-'
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
                    | GROUP KATEGORI PER VENDOR
                    |--------------------------------------------------------------------------
                    */

                    $vendorProductionCostIds =
                        $vendorRows
                        ->pluck('production_cost_id')
                        ->unique()
                        ->values();

                    $vendorCategories =
                        $categories
                        ->whereIn(
                            'production_cost_id',
                            $vendorProductionCostIds
                        )
                        ->groupBy('category_id')
                        ->sortBy(function ($categoryRows) {
                            return strtolower(
                                $categoryRows
                                    ->first()
                                    ->category_name
                            );
                        });

                    foreach (
                        $vendorCategories as $categoryRows
                    ) {

                        $category =
                            $categoryRows->first();

                        /*
                        |--------------------------------------------------------------------------
                        | PRODUCTION COST IDS PER KATEGORI
                        |--------------------------------------------------------------------------
                        */

                        $categoryProductionCostIds =
                            $categoryRows
                            ->pluck(
                                'production_cost_id'
                            )
                            ->unique()
                            ->values();

                        /*
                        |--------------------------------------------------------------------------
                        | DETAILS PER KATEGORI
                        |--------------------------------------------------------------------------
                        */

                        $categoryDetails =
                            $vendorRows->whereIn(
                                'production_cost_id',
                                $categoryProductionCostIds
                            );

                        if (
                            $categoryDetails->isEmpty()
                        ) {
                            continue;
                        }

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
                        | TABLE HEADER OUTSOURCING
                        |--------------------------------------------------------------------------
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
                            '',
                            '',
                            '',
                        ];

                        /*
                        |--------------------------------------------------------------------------
                        | GROUP LAMINASI OUTSOURCING
                        |--------------------------------------------------------------------------
                        */

                        $laminationGroups =
                            $categoryDetails
                            ->groupBy(
                                function ($detail) {
                                    return implode('|', [
                                        $detail->lamination_id,

                                        $detail->vendor_id,

                                        number_format(
                                            (float) $detail->vendor_price,
                                            2,
                                            '.',
                                            ''
                                        ),

                                        number_format(
                                            (float) $detail->general_price,
                                            2,
                                            '.',
                                            ''
                                        ),

                                        number_format(
                                            (float) $detail->division_price,
                                            2,
                                            '.',
                                            ''
                                        ),

                                        number_format(
                                            (float) $detail->plain_price,
                                            2,
                                            '.',
                                            ''
                                        ),
                                    ]);
                                }
                            );

                        /*
                        |--------------------------------------------------------------------------
                        | LOOP LAMINASI OUTSOURCING
                        |--------------------------------------------------------------------------
                        */

                        foreach (
                            $laminationGroups as $detailRows
                        ) {

                            $detail =
                                $detailRows->first();

                            /*
                            |--------------------------------------------------------------------------
                            | DETAIL IDS UNTUK SIZE
                            |--------------------------------------------------------------------------
                            */

                            $detailIdsForSizes =
                                $detailRows
                                ->pluck('id')
                                ->unique()
                                ->values();

                            /*
                            |--------------------------------------------------------------------------
                            | SIZE LAMINASI
                            |--------------------------------------------------------------------------
                            */

                            $laminationSizes =
                                $sizes
                                ->whereIn(
                                    'lamination_production_cost_detail_id',
                                    $detailIdsForSizes
                                )
                                ->unique(
                                    'lamination_size_id'
                                )
                                ->sortBy(function ($size) {
                                    return [
                                        (float) $size->width,
                                        (float) (
                                            $size->length ?? 0
                                        ),
                                    ];
                                })
                                ->values();

                            /*
                            |--------------------------------------------------------------------------
                            | FORMAT WIDTH
                            |--------------------------------------------------------------------------
                            */

                            $widthText =
                                $laminationSizes
                                ->map(function ($size) {
                                    return
                                        $size->width
                                        . ' '
                                        . $size->unit;
                                })
                                ->implode(', ');

                            /*
                            |--------------------------------------------------------------------------
                            | FORMAT LENGTH
                            |--------------------------------------------------------------------------
                            */

                            $lengthText =
                                $laminationSizes
                                ->map(function ($size) {

                                    if (
                                        $size->length === null
                                    ) {
                                        return '-';
                                    }

                                    return
                                        $size->length
                                        . ' '
                                        . $size->unit;
                                })
                                ->implode(', ');

                            /*
                            |--------------------------------------------------------------------------
                            | DATA LAMINASI OUTSOURCING
                            |--------------------------------------------------------------------------
                            */

                            $rows[] = [
                                $no++,
                                $detail->lamination_name,
                                $widthText,
                                $lengthText,
                                $detail->vendor_price,
                                $detail->general_price,
                                $detail->division_price,
                                $detail->plain_price,
                                '',
                                '',
                                '',
                            ];
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
                    | SPACER SETELAH VENDOR
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
                | SPACER SETELAH LOKASI OUTSOURCING
                |--------------------------------------------------------------------------
                */

                $rows[] = array_fill(
                    0,
                    11,
                    ' '
                );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | NON-OUTSOURCING
            |--------------------------------------------------------------------------
            */

            $locationCategories =
                $categories
                ->whereIn(
                    'production_cost_id',
                    $locationProductionCostIds
                )
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

            foreach (
                $locationCategories as $categoryRows
            ) {

                $category =
                    $categoryRows->first();

                /*
                |--------------------------------------------------------------------------
                | PRODUCTION COST IDS PER KATEGORI
                |--------------------------------------------------------------------------
                */

                $categoryProductionCostIds =
                    $categoryRows
                    ->pluck(
                        'production_cost_id'
                    )
                    ->unique()
                    ->values();

                /*
                |--------------------------------------------------------------------------
                | DETAILS PER KATEGORI
                |--------------------------------------------------------------------------
                */

                $categoryDetails =
                    $locationDetails->whereIn(
                        'production_cost_id',
                        $categoryProductionCostIds
                    );

                if ($categoryDetails->isEmpty()) {
                    continue;
                }

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
                | TABLE HEADER NON-OUTSOURCING
                |--------------------------------------------------------------------------
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

                /*
                |--------------------------------------------------------------------------
                | GROUP LAMINASI
                |--------------------------------------------------------------------------
                |
                | Size tidak digunakan sebagai bagian grouping.
                | Semua size yang memiliki konfigurasi biaya
                | sama akan digabung menjadi satu baris laminasi.
                |
                */

                $laminationGroups =
                    $categoryDetails
                    ->groupBy(function ($detail) {

                        return implode('|', [
                            $detail->lamination_id,

                            number_format(
                                (float) $detail->price_per_meter,
                                2,
                                '.',
                                ''
                            ),

                            number_format(
                                (float) $detail->production_cost,
                                2,
                                '.',
                                ''
                            ),

                            number_format(
                                (float) $detail->finishing_cost,
                                2,
                                '.',
                                ''
                            ),

                            number_format(
                                (float) $detail->total_cost,
                                2,
                                '.',
                                ''
                            ),

                            number_format(
                                (float) $detail->general_price,
                                2,
                                '.',
                                ''
                            ),

                            number_format(
                                (float) $detail->division_price,
                                2,
                                '.',
                                ''
                            ),

                            number_format(
                                (float) $detail->plain_price,
                                2,
                                '.',
                                ''
                            ),
                        ]);
                    });

                /*
                |--------------------------------------------------------------------------
                | LOOP LAMINASI
                |--------------------------------------------------------------------------
                */

                foreach (
                    $laminationGroups as $detailRows
                ) {

                    $detail =
                        $detailRows->first();

                    /*
                    |--------------------------------------------------------------------------
                    | DETAIL IDS UNTUK SIZE
                    |--------------------------------------------------------------------------
                    */

                    $detailIdsForSizes =
                        $detailRows
                        ->pluck('id')
                        ->unique()
                        ->values();

                    /*
                    |--------------------------------------------------------------------------
                    | SIZE LAMINASI
                    |--------------------------------------------------------------------------
                    */

                    $laminationSizes =
                        $sizes
                        ->whereIn(
                            'lamination_production_cost_detail_id',
                            $detailIdsForSizes
                        )
                        ->unique(
                            'lamination_size_id'
                        )
                        ->sortBy(function ($size) {
                            return [
                                (float) $size->width,
                                (float) (
                                    $size->length ?? 0
                                ),
                            ];
                        })
                        ->values();

                    /*
                    |--------------------------------------------------------------------------
                    | FORMAT WIDTH
                    |--------------------------------------------------------------------------
                    */

                    $widthText =
                        $laminationSizes
                        ->map(function ($size) {
                            return
                                $size->width
                                . ' '
                                . $size->unit;
                        })
                        ->implode(', ');

                    /*
                    |--------------------------------------------------------------------------
                    | FORMAT LENGTH
                    |--------------------------------------------------------------------------
                    */

                    $lengthText =
                        $laminationSizes
                        ->map(function ($size) {

                            if (
                                $size->length === null
                            ) {
                                return '-';
                            }

                            return
                                $size->length
                                . ' '
                                . $size->unit;
                        })
                        ->implode(', ');

                    /*
                    |--------------------------------------------------------------------------
                    | DATA LAMINASI
                    |--------------------------------------------------------------------------
                    */

                    $rows[] = [
                        $no++,
                        $detail->lamination_name,
                        $widthText,
                        $lengthText,
                        $detail->price_per_meter,
                        $detail->production_cost,
                        $detail->finishing_cost,
                        $detail->total_cost,
                        $detail->general_price,
                        $detail->division_price,
                        $detail->plain_price,
                    ];
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
                    'D' => 25,
                    'E' => 18,
                    'F' => 18,
                    'G' => 18,
                    'H' => 18,
                    'I' => 18,
                    'J' => 18,
                    'K' => 18,
                ];

                foreach (
                    $columnWidths as $column => $width
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

                    if ($type === 'LOKASI') {

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
                    | VENDOR
                    |--------------------------------------------------------------------------
                    */

                    if ($type === 'VENDOR') {

                        $sheet->mergeCells(
                            "B{$row}:H{$row}"
                        );

                        $sheet
                            ->getStyle(
                                "A{$row}:H{$row}"
                            )
                            ->getFont()
                            ->setBold(true);

                        $sheet
                            ->getStyle(
                                "A{$row}:H{$row}"
                            )
                            ->getFill()
                            ->setFillType(
                                Fill::FILL_SOLID
                            )
                            ->getStartColor()
                            ->setARGB(
                                'E8F1F8'
                            );

                        $sheet
                            ->getStyle(
                                "A{$row}:H{$row}"
                            )
                            ->getAlignment()
                            ->setHorizontal(
                                Alignment::HORIZONTAL_LEFT
                            );

                        $sheet
                            ->getRowDimension($row)
                            ->setRowHeight(21);

                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | KATEGORI
                    |--------------------------------------------------------------------------
                    */

                    if ($type === 'KATEGORI') {

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

                        $isOutsourcing =
                            $sheet
                            ->getCell("E{$row}")
                            ->getValue()
                            === 'Harga Vendor';

                        $lastColumn =
                            $isOutsourcing
                            ? 'H'
                            : 'K';

                        $sheet
                            ->getStyle(
                                "A{$row}:{$lastColumn}{$row}"
                            )
                            ->getFont()
                            ->setBold(true);

                        $sheet
                            ->getStyle(
                                "A{$row}:{$lastColumn}{$row}"
                            )
                            ->getAlignment()
                            ->setHorizontal(
                                Alignment::HORIZONTAL_CENTER
                            );

                        $sheet
                            ->getStyle(
                                "A{$row}:{$lastColumn}{$row}"
                            )
                            ->getBorders()
                            ->getAllBorders()
                            ->setBorderStyle(
                                Border::BORDER_THIN
                            );

                        $sheet
                            ->getStyle(
                                "A{$row}:{$lastColumn}{$row}"
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
                    | DATA LAMINASI
                    |--------------------------------------------------------------------------
                    */

                    if (
                        is_numeric(
                            $sheet
                                ->getCell("A{$row}")
                                ->getValue()
                        )
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | DETEKSI JUMLAH KOLOM
                        |--------------------------------------------------------------------------
                        */

                        $isOutsourcing =
                            $sheet
                            ->getCell("E" . ($row - 1))
                            ->getValue()
                            === 'Harga Vendor';

                        /*
                        |--------------------------------------------------------------------------
                        | BORDER
                        |--------------------------------------------------------------------------
                        */

                        $lastColumn =
                            $isOutsourcing
                            ? 'H'
                            : 'K';

                        $sheet
                            ->getStyle(
                                "A{$row}:{$lastColumn}{$row}"
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
                        | SIZE
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

                        $numberEndColumn =
                            $isOutsourcing
                            ? 'H'
                            : 'K';

                        $sheet
                            ->getStyle(
                                "E{$row}:{$numberEndColumn}{$row}"
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

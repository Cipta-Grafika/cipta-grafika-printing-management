<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CuttingsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithEvents
{
    /*
    |--------------------------------------------------------------------------
    | NOMOR URUT
    |--------------------------------------------------------------------------
    */

    private int $no = 0;

    /*
    |--------------------------------------------------------------------------
    | DATA EXPORT
    |--------------------------------------------------------------------------
    */

    private ?Collection $exportData = null;

    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA JASA POTONG
    |--------------------------------------------------------------------------
    |
    | Urutan:
    | 1. Active terlebih dahulu
    | 2. Inactive setelahnya
    | 3. Nama jasa potong A-Z
    |
    */

    public function collection(): Collection
    {
        if ($this->exportData !== null) {
            return $this->exportData;
        }

        $this->exportData = DB::table('m_cuttings as c')
            ->select(
                'c.id as cutting_id',
                'c.name as cutting_name',
                'c.status',
                'c.created_by',
                'c.created_at',
                'c.updated_by',
                'c.updated_at'
            )
            ->selectSub(
                DB::table('m_cutting_sizes as cs')
                    ->selectRaw("
                        STRING_AGG(
                            CASE
                                WHEN cs.length IS NULL THEN
                                    CONCAT(
                                        RTRIM(
                                            RTRIM(
                                                TO_CHAR(
                                                    cs.width,
                                                    'FM999999990.##'
                                                ),
                                                '0'
                                            ),
                                            '.'
                                        ),
                                        ' ',
                                        cs.unit
                                    )
                                ELSE
                                    CONCAT(
                                        RTRIM(
                                            RTRIM(
                                                TO_CHAR(
                                                    cs.width,
                                                    'FM999999990.##'
                                                ),
                                                '0'
                                            ),
                                            '.'
                                        ),
                                        ' × ',
                                        RTRIM(
                                            RTRIM(
                                                TO_CHAR(
                                                    cs.length,
                                                    'FM999999990.##'
                                                ),
                                                '0'
                                            ),
                                            '.'
                                        ),
                                        ' ',
                                        cs.unit
                                    )
                            END,
                            ', '
                            ORDER BY
                                cs.width ASC,
                                cs.length ASC
                        )
                    ")
                    ->whereColumn(
                        'cs.cutting_id',
                        'c.id'
                    ),
                'cutting_sizes'
            )
            ->orderByRaw(
                "CASE
                    WHEN c.status = 'Active' THEN 0
                    ELSE 1
                END"
            )
            ->orderBy(
                'c.name',
                'asc'
            )
            ->get();

        return $this->exportData;
    }

    /*
    |--------------------------------------------------------------------------
    | HEADER EXCEL
    |--------------------------------------------------------------------------
    */

    public function headings(): array
    {
        return [
            'No',
            'Nama Jasa Potong',
            'Ukuran Jasa Potong',
            'Status',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MAPPING DATA
    |--------------------------------------------------------------------------
    */

    public function map($cutting): array
    {
        return [
            ++$this->no,

            $cutting->cutting_name
                ?? '-',

            $cutting->cutting_sizes
                ?? '-',

            $cutting->status
                ?? '-',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | EVENT EXCEL
    |--------------------------------------------------------------------------
    |
    | Digunakan untuk:
    | - Alignment
    | - Header
    | - Border
    | - Lebar kolom
    | - Filter
    | - Freeze header
    | - Tinggi header
    |
    */

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $data = $this->collection();

                /*
                |--------------------------------------------------------------------------
                | JIKA DATA KOSONG
                |--------------------------------------------------------------------------
                */

                if ($data->isEmpty()) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | RANGE
                |--------------------------------------------------------------------------
                */

                $startRow = 2;

                $lastRow = $data->count() + 1;

                /*
                |--------------------------------------------------------------------------
                | ALIGNMENT
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle("A1:D{$lastRow}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);

                $sheet->getStyle("A1:D{$lastRow}")
                    ->getAlignment()
                    ->setWrapText(true);

                /*
                |--------------------------------------------------------------------------
                | ALIGNMENT NOMOR & STATUS
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle("A2:A{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("D2:D{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                /*
                |--------------------------------------------------------------------------
                | HEADER
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A1:D1')
                    ->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                /*
                |--------------------------------------------------------------------------
                | BORDER
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle("A1:D{$lastRow}")
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                /*
                |--------------------------------------------------------------------------
                | LEBAR KOLOM
                |--------------------------------------------------------------------------
                */

                $sheet->getColumnDimension('A')->setWidth(8);
                $sheet->getColumnDimension('B')->setWidth(38);
                $sheet->getColumnDimension('C')->setWidth(40);
                $sheet->getColumnDimension('D')->setWidth(15);

                /*
                |--------------------------------------------------------------------------
                | FILTER
                |--------------------------------------------------------------------------
                */

                $sheet->setAutoFilter("A1:D{$lastRow}");

                /*
                |--------------------------------------------------------------------------
                | FREEZE HEADER
                |--------------------------------------------------------------------------
                */

                $sheet->freezePane('A2');

                /*
                |--------------------------------------------------------------------------
                | TINGGI HEADER & DATA
                |--------------------------------------------------------------------------
                */

                $sheet->getRowDimension(1)->setRowHeight(25);

                for ($row = $startRow; $row <= $lastRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(30);
                }
            },
        ];
    }
}


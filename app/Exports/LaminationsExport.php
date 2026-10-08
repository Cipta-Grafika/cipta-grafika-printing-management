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

class LaminationsExport implements
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
    | AMBIL DATA LAMINASI
    |--------------------------------------------------------------------------
    |
    | Urutan:
    | 1. Active terlebih dahulu
    | 2. Inactive setelahnya
    | 3. Nama laminasi A-Z
    |
    | Tidak menggunakan:
    | - m_lamination_configurations
    | - m_lamination_categories
    | - m_categories
    | - m_engines
    | - m_locations
    |
    */

    public function collection(): Collection
    {
        if ($this->exportData !== null) {
            return $this->exportData;
        }

        $this->exportData = DB::table('m_laminations as l')
            ->select(
                'l.id as lamination_id',
                'l.lamination_code',
                'l.name as lamination_name',
                'l.status',
                'l.created_by',
                'l.created_at',
                'l.updated_by',
                'l.updated_at'
            )
            ->selectSub(
                DB::table('m_lamination_sizes as ls')
                    ->selectRaw("
                        STRING_AGG(
                            CASE
                                WHEN ls.length IS NULL THEN
                                    CONCAT(
                                        RTRIM(
                                            RTRIM(
                                                TO_CHAR(
                                                    ls.width,
                                                    'FM999999990.##'
                                                ),
                                                '0'
                                            ),
                                            '.'
                                        ),
                                        ' ',
                                        ls.unit
                                    )
                                ELSE
                                    CONCAT(
                                        RTRIM(
                                            RTRIM(
                                                TO_CHAR(
                                                    ls.width,
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
                                                    ls.length,
                                                    'FM999999990.##'
                                                ),
                                                '0'
                                            ),
                                            '.'
                                        ),
                                        ' ',
                                        ls.unit
                                    )
                            END,
                            ', '
                            ORDER BY
                                ls.width ASC,
                                ls.length ASC
                        )
                    ")
                    ->whereColumn(
                        'ls.lamination_id',
                        'l.id'
                    ),
                'lamination_sizes'
            )
            ->orderByRaw(
                "CASE
                    WHEN l.status = 'Active' THEN 0
                    ELSE 1
                END"
            )
            ->orderBy(
                'l.name',
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
            'Kode Laminasi',
            'Nama Laminasi',
            'Ukuran Laminasi',
            'Status',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MAPPING DATA
    |--------------------------------------------------------------------------
    */

    public function map($lamination): array
    {
        return [
            ++$this->no,

            $lamination->lamination_code
                ?? '-',

            $lamination->lamination_name
                ?? '-',

            $lamination->lamination_sizes
                ?? '-',

            $lamination->status
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

                /*
                |--------------------------------------------------------------------------
                | DATA EXPORT
                |--------------------------------------------------------------------------
                |
                | Gunakan data yang sudah diambil oleh collection().
                | Tidak melakukan query ulang.
                |
                */

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

                $sheet->getStyle(
                    "A1:E{$lastRow}"
                )
                    ->getAlignment()
                    ->setVertical(
                        Alignment::VERTICAL_CENTER
                    );

                $sheet->getStyle(
                    "A1:E{$lastRow}"
                )
                    ->getAlignment()
                    ->setWrapText(true);

                /*
                |--------------------------------------------------------------------------
                | ALIGNMENT NOMOR
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle(
                    "A2:A{$lastRow}"
                )
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    );

                /*
                |--------------------------------------------------------------------------
                | ALIGNMENT STATUS
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle(
                    "E2:E{$lastRow}"
                )
                    ->getAlignment()
                    ->setHorizontal(
                        Alignment::HORIZONTAL_CENTER
                    );

                /*
                |--------------------------------------------------------------------------
                | HEADER
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle('A1:E1')
                    ->applyFromArray([
                        'font' => [
                            'bold' => true,
                        ],

                        'alignment' => [
                            'horizontal' =>
                            Alignment::HORIZONTAL_CENTER,

                            'vertical' =>
                            Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                /*
                |--------------------------------------------------------------------------
                | BORDER
                |--------------------------------------------------------------------------
                */

                $sheet->getStyle(
                    "A1:E{$lastRow}"
                )
                    ->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' =>
                                Border::BORDER_THIN,
                            ],
                        ],
                    ]);

                /*
                |--------------------------------------------------------------------------
                | LEBAR KOLOM
                |--------------------------------------------------------------------------
                */

                $sheet->getColumnDimension('A')
                    ->setWidth(8);

                $sheet->getColumnDimension('B')
                    ->setWidth(25);

                $sheet->getColumnDimension('C')
                    ->setWidth(38);

                $sheet->getColumnDimension('D')
                    ->setWidth(35);

                $sheet->getColumnDimension('E')
                    ->setWidth(15);

                /*
                |--------------------------------------------------------------------------
                | FILTER
                |--------------------------------------------------------------------------
                */

                $sheet->setAutoFilter(
                    "A1:E{$lastRow}"
                );

                /*
                |--------------------------------------------------------------------------
                | FREEZE HEADER
                |--------------------------------------------------------------------------
                */

                $sheet->freezePane('A2');

                /*
                |--------------------------------------------------------------------------
                | TINGGI HEADER
                |--------------------------------------------------------------------------
                */

                $sheet->getRowDimension(1)
                    ->setRowHeight(25);

                /*
                |--------------------------------------------------------------------------
                | TINGGI DATA
                |--------------------------------------------------------------------------
                */

                for (
                    $row = $startRow;
                    $row <= $lastRow;
                    $row++
                ) {
                    $sheet->getRowDimension($row)
                        ->setRowHeight(30);
                }
            },
        ];
    }
}

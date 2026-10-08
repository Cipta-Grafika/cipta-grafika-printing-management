<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EnginesExport implements FromCollection, WithHeadings, WithMapping
{
    /*
    |--------------------------------------------------------------------------
    | NOMOR URUT
    |--------------------------------------------------------------------------
    */

    private int $no = 0;


    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA MESIN
    |--------------------------------------------------------------------------
    */

    public function collection(): Collection
    {
        return DB::table('m_engines')
            ->orderByRaw(
                "CASE WHEN status = 'Active' THEN 0 ELSE 1 END"
            )
            ->orderBy('name', 'asc')
            ->get();
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
            'Nama Mesin',
            'Minimum Charge',
            'Status',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | MAPPING DATA
    |--------------------------------------------------------------------------
    */

    public function map($engine): array
    {
        return [
            ++$this->no,
            $engine->name,
            $engine->minimum_charge,
            $engine->status,
        ];
    }
}

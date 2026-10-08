<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LocationsExport implements FromCollection, WithHeadings, WithMapping
{
    /*
    |--------------------------------------------------------------------------
    | NOMOR URUT
    |--------------------------------------------------------------------------
    */

    private int $no = 0;


    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    public function collection(): Collection
    {
        return DB::table('m_locations')
            ->select(
                'id',
                'name',
                'status'
            )

            /*
            |--------------------------------------------------------------------------
            | ACTIVE TERLEBIH DAHULU
            |--------------------------------------------------------------------------
            */

            ->orderByRaw("
                CASE
                    WHEN status = 'Active' THEN 1
                    ELSE 2
                END
            ")

            /*
            |--------------------------------------------------------------------------
            | URUTKAN NAMA LOKASI
            |--------------------------------------------------------------------------
            */

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

            'Nama Lokasi',

            'Status'

        ];
    }


    /*
    |--------------------------------------------------------------------------
    | MAPPING DATA
    |--------------------------------------------------------------------------
    */

    public function map($location): array
    {
        return [

            ++$this->no,

            $location->name,

            $location->status

        ];
    }
}

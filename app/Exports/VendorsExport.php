<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VendorsExport implements FromCollection, WithHeadings, WithMapping
{
    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA VENDOR
    |--------------------------------------------------------------------------
    */

    public function collection(): Collection
    {
        return DB::table('m_vendors')
            ->select(
                'id',
                'name',
                'contact',
                'status'
            )
            ->orderByRaw("CASE WHEN status = 'Active' THEN 0 ELSE 1 END")
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
            'Nama Vendor',
            'Kontak',
            'Status',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | FORMAT DATA EXCEL
    |--------------------------------------------------------------------------
    */

    public function map($vendor): array
    {
        static $no = 0;

        return [
            ++$no,
            $vendor->name,
            $vendor->contact ?? '-',
            $vendor->status,
        ];
    }
}

<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MaterialPricesExport implements FromCollection, WithHeadings, WithMapping
{
    /*
    |--------------------------------------------------------------------------
    | NOMOR URUT
    |--------------------------------------------------------------------------
    */

    private int $no = 0;

    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA HARGA MATERIAL
    | DIURUTKAN BERDASARKAN:
    | MATERIAL
    |--------------------------------------------------------------------------
    */

    public function collection(): Collection
    {
        return DB::table('m_material_prices as mp')
            ->join(
                'm_materials as m',
                'mp.material_id',
                '=',
                'm.id'
            )
            ->select(
                'm.material_name',
                'mp.price_per_meter'
            )
            ->orderBy('m.material_name', 'asc')
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
            'Nama Material',
            'Harga Permeter',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MAPPING DATA
    |--------------------------------------------------------------------------
    */

    public function map($materialPrice): array
    {
        return [
            ++$this->no,
            $materialPrice->material_name,
            $materialPrice->price_per_meter,
        ];
    }
}

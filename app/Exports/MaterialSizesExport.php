<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MaterialSizesExport implements FromCollection, WithHeadings, WithMapping
{
    /*
    |--------------------------------------------------------------------------
    | NOMOR URUT
    |--------------------------------------------------------------------------
    */

    private int $no = 0;

    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA UKURAN MATERIAL
    | DIURUTKAN BERDASARKAN NAMA MATERIAL
    | LALU BERDASARKAN LEBAR
    |--------------------------------------------------------------------------
    */

    public function collection(): Collection
    {
        return DB::table('m_material_sizes')
            ->join(
                'm_materials',
                'm_material_sizes.material_id',
                '=',
                'm_materials.id'
            )
            ->select(
                'm_material_sizes.width',
                'm_material_sizes.unit',
                'm_materials.material_name'
            )
            ->orderBy('m_materials.material_name', 'asc')
            ->orderBy('m_material_sizes.width', 'asc')
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
            'Material',
            'Lebar',
            'Satuan',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MAPPING DATA
    |--------------------------------------------------------------------------
    */

    public function map($materialSize): array
    {
        return [
            ++$this->no,
            $materialSize->material_name,
            $materialSize->width,
            $materialSize->unit,
        ];
    }
}

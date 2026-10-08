<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MaterialsExport implements FromCollection, WithHeadings, WithMapping
{
    /*
    |--------------------------------------------------------------------------
    | NOMOR URUT
    |--------------------------------------------------------------------------
    */

    private int $no = 0;


    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA MATERIAL
    |--------------------------------------------------------------------------
    |
    | Active dulu, baru Inactive.
    | Kemudian diurutkan berdasarkan:
    | - Nama material
    | - Lebar
    | - Panjang
    | - Unit
    |
    */

    public function collection(): Collection
    {
        return DB::table('m_materials')
            ->join(
                'm_categories',
                'm_materials.category_id',
                '=',
                'm_categories.id'
            )
            ->leftJoin(
                'm_material_sizes',
                'm_materials.id',
                '=',
                'm_material_sizes.material_id'
            )
            ->select(
                'm_materials.material_code',
                'm_materials.material_name',
                'm_materials.status',
                'm_categories.name as category_name',
                'm_material_sizes.width',
                'm_material_sizes.length',
                'm_material_sizes.unit'
            )
            ->orderByRaw(
                "CASE
                    WHEN m_materials.status = 'Active' THEN 0
                    ELSE 1
                END"
            )
            ->orderBy(
                'm_materials.material_name',
                'asc'
            )
            ->orderBy(
                'm_material_sizes.width',
                'asc'
            )
            ->orderBy(
                'm_material_sizes.length',
                'asc'
            )
            ->orderBy(
                'm_material_sizes.unit',
                'asc'
            )
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
            'Kategori',
            'Kode Material',
            'Nama Material',
            'Status',
            'Lebar',
            'Panjang',
            'Unit',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | MAPPING DATA
    |--------------------------------------------------------------------------
    */

    public function map($material): array
    {
        return [
            ++$this->no,
            $material->category_name,
            $material->material_code,
            $material->material_name,
            $material->status,
            $material->width,
            $material->length,
            $material->unit,
        ];
    }
}

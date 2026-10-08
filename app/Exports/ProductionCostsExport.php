<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductionCostsExport implements FromCollection, WithHeadings, WithMapping
{
    /*
    |--------------------------------------------------------------------------
    | NOMOR URUT
    |--------------------------------------------------------------------------
    */

    private int $no = 0;

    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA ONGKOS PRODUKSI
    | DIURUTKAN BERDASARKAN:
    | LOKASI → MESIN → MATERIAL
    |--------------------------------------------------------------------------
    */

    public function collection(): Collection
    {
        return DB::table('m_production_costs as pc')
            ->join(
                'm_locations as l',
                'pc.location_id',
                '=',
                'l.id'
            )
            ->join(
                'm_engines as e',
                'pc.engine_id',
                '=',
                'e.id'
            )
            ->join(
                'm_materials as m',
                'pc.material_id',
                '=',
                'm.id'
            )
            ->select(
                'l.name as location_name',
                'e.name as engine_name',
                'm.material_name',
                'pc.production_cost',
                'pc.finishing_cost',
                'pc.total_cost',
                'pc.harga_polos',
                'pc.harga_umum',
                'pc.harga_divisi'
            )
            ->orderBy('l.name', 'asc')
            ->orderBy('e.name', 'asc')
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
            'Lokasi',
            'Mesin',
            'Material',
            'Ongkos Produksi',
            'Ongkos Finishing',
            'Total Cost',
            'Harga Polos',
            'Harga Umum',
            'Harga Divisi',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MAPPING DATA
    |--------------------------------------------------------------------------
    */

    public function map($productionCost): array
    {
        return [
            ++$this->no,
            $productionCost->location_name,
            $productionCost->engine_name,
            $productionCost->material_name,
            $productionCost->production_cost,
            $productionCost->finishing_cost,
            $productionCost->total_cost,
            $productionCost->harga_polos,
            $productionCost->harga_umum,
            $productionCost->harga_divisi,
        ];
    }
}

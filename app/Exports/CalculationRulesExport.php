<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CalculationRulesExport implements FromCollection, WithHeadings, WithMapping
{
    /*
    |--------------------------------------------------------------------------
    | NOMOR URUT
    |--------------------------------------------------------------------------
    */

    private int $no = 0;


    /*
    |--------------------------------------------------------------------------
    | AMBIL DATA ATURAN PERHITUNGAN
    | DIURUTKAN BERDASARKAN:
    | MESIN → NAMA ATURAN
    |--------------------------------------------------------------------------
    */

    public function collection(): Collection
    {
        return DB::table('m_calculation_rules as cr')
            ->join(
                'm_engines as e',
                'cr.engine_id',
                '=',
                'e.id'
            )
            ->select(
                'cr.code',
                'cr.name',
                'e.name as engine_name',
                'cr.minimum_charge',
                'cr.description',
                'cr.status'
            )
            ->orderBy('e.name', 'asc')
            ->orderBy('cr.name', 'asc')
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
            'Kode',
            'Nama Aturan',
            'Mesin',
            'Minimum Charge',
            'Deskripsi',
            'Status',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | MAPPING DATA
    |--------------------------------------------------------------------------
    */

    public function map($calculationRule): array
    {
        return [
            ++$this->no,
            $calculationRule->code ?? '-',
            $calculationRule->name,
            $calculationRule->engine_name,
            $calculationRule->minimum_charge,
            $calculationRule->description ?? '-',
            $calculationRule->status,
        ];
    }
}

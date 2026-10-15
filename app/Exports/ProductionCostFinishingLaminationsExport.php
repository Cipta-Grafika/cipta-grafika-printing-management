<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductionCostFinishingLaminationsExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        $engines = DB::table('m_engines')
            ->where('status', 'Active')
            ->whereRaw('LOWER(TRIM(name)) = ?', ['large format'])
            ->orderBy('name')
            ->get();

        $sheets = [];

        foreach ($engines as $engine) {

            $sheets[] =
                new ProductionCostFinishingLaminationsSheetExport(
                    $engine->id,
                    $engine->name
                );
        }

        return $sheets;
    }
}

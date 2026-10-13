<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DigitalProductionCostsExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new DigitalProductionCostsSheetExport(),
        ];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DigitalPricingRulesExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new DigitalPricingRulesSheetExport(),
        ];
    }
}

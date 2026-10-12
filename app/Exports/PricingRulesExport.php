<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PricingRulesExport implements WithMultipleSheets
{
    /**
     * Membuat worksheet export.
     */
    public function sheets(): array
    {
        return [
            new PricingRulesSheetExport(),
        ];
    }
}

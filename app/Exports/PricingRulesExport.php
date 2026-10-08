<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PricingRulesExport implements Export, WithMultipleSheets
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

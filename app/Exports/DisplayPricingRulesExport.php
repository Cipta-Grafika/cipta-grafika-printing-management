<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DisplayPricingRulesExport implements Export, WithMultipleSheets
{
    /**
     * Membuat worksheet export.
     */
    public function sheets(): array
    {
        return [
            new DisplayPricingRulesSheetExport(),
        ];
    }
}

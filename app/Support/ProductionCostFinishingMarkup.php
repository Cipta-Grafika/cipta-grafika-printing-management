<?php

namespace App\Support;

class ProductionCostFinishingMarkup
{
    public static function general(
        string $engineName,
        string $locationName,
        string $categoryName,
        string $materialName,
        float $masterMarkup
    ): float {
        // Pengecualian hanya untuk ongkos produksi material ini di Purwakarta.
        if (
            strtolower(trim($engineName)) === 'large format'
            && strtolower(trim($locationName)) === 'purwakarta'
            && strtolower(trim($categoryName)) === 'outdoor'
            && strtolower(trim($materialName)) === 'flexy frontlite china 280 gsm'
        ) {
            return 40.0;
        }

        return $masterMarkup;
    }
}

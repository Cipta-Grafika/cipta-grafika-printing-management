<?php

namespace App\Support;

use Illuminate\Support\Collection;

class DigitalLaminationConfiguration
{
    public static function signature(Collection $details, Collection $prices): string
    {
        $signature = $details->sortBy('lamination_id')->map(function ($detail) use ($prices) {
            $detailPrices = ($prices->get($detail->id) ?? collect())
                ->sortBy(fn($price) => $price->price_type . '|' . $price->print_type . '|' . $price->quantity_tier)
                ->map(fn($price) => [
                    'price_type' => $price->price_type,
                    'print_type' => $price->print_type,
                    'quantity_tier' => $price->quantity_tier,
                    'minimum_quantity' => (int) $price->minimum_quantity,
                    'maximum_quantity' => $price->maximum_quantity === null ? null : (int) $price->maximum_quantity,
                    'markup_percentage' => number_format((float) $price->markup_percentage, 2, '.', ''),
                    'rounding_value' => $price->rounding_value === null ? null : number_format((float) $price->rounding_value, 2, '.', ''),
                    'selling_price' => number_format((float) $price->selling_price, 2, '.', ''),
                ])->values()->all();

            return [
                'lamination_id' => (int) $detail->lamination_id,
                'paper_cost' => number_format((float) $detail->paper_cost, 2, '.', ''),
                'click_cost' => number_format((float) $detail->click_cost, 2, '.', ''),
                'single_print_cost' => number_format((float) $detail->single_print_cost, 2, '.', ''),
                'double_print_cost' => number_format((float) $detail->double_print_cost, 2, '.', ''),
                'prices' => $detailPrices,
            ];
        })->values()->all();

        // ID kategori, rule, dan audit berbeda tidak mengubah kesamaan konfigurasi.
        return json_encode($signature, JSON_THROW_ON_ERROR);
    }
}

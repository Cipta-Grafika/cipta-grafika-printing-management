<?php

namespace Tests\Unit;

use App\Support\ProductionCostFinishingMarkup;
use PHPUnit\Framework\TestCase;

class ProductionCostFinishingMarkupTest extends TestCase
{
    public function test_target_material_uses_forty_percent(): void
    {
        $this->assertSame(40.0, ProductionCostFinishingMarkup::general(
            'Large Format', 'Purwakarta', 'Outdoor', 'Flexy Frontlite China 280 gsm', 75.0
        ));
        $this->assertSame(40.0, ProductionCostFinishingMarkup::general(
            ' LARGE FORMAT ', ' PURWAKARTA ', ' outdoor ', ' flexy frontlite china 280 GSM ', 0.0
        ));
    }

    public function test_other_engines_categories_and_materials_keep_master_markup(): void
    {
        foreach ([
            ['Digital Print A3+', 'Outdoor', 'Flexy Frontlite China 280 gsm'],
            ['Large Format', 'Indoor', 'Flexy Frontlite China 280 gsm'],
            ['Large Format', 'Outdoor', 'Flexy Frontlite China 340 gsm'],
            ['Large Format', 'Outdoor', 'Flexy Frontlite China 280 gsm Premium'],
        ] as [$engine, $category, $material]) {
            $this->assertSame(75.5, ProductionCostFinishingMarkup::general(
                $engine, 'Purwakarta', $category, $material, 75.5
            ));
        }

    }

    public function test_other_locations_keep_master_markup(): void
    {
        foreach (['Graha', 'Outsourcing', 'Purwakarta Barat', ''] as $location) {
            $this->assertSame(75.5, ProductionCostFinishingMarkup::general(
                'Large Format', $location, 'Outdoor', 'Flexy Frontlite China 280 gsm', 75.5
            ));
        }
    }
}

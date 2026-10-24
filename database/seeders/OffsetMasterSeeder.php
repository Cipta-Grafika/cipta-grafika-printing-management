<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OffsetMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Master Bahan Plano Offset
        $planoMaterials = [
            [
                'material_id'     => 198,
                'material_code'   => 'PLN-AP120',
                'material_name'   => 'Art Paper 120 gsm',
                'width'           => 65.00,
                'length'          => 90.00,
                'gramature'       => 120,
                'paper_type'      => 'Art Carton/Art Paper',
                'price_per_plano' => 1500.00,
                'status'          => 'active',
                'created_by'      => 'System Seeder',
                'created_at'      => $now,
                'updated_by'      => 'System Seeder',
                'updated_at'      => $now,
            ],
            [
                'material_id'     => 199,
                'material_code'   => 'PLN-AP150',
                'material_name'   => 'Art Paper 150 gsm',
                'width'           => 65.00,
                'length'          => 90.00,
                'gramature'       => 150,
                'paper_type'      => 'Art Carton/Art Paper',
                'price_per_plano' => 1900.00,
                'status'          => 'active',
                'created_by'      => 'System Seeder',
                'created_at'      => $now,
                'updated_by'      => 'System Seeder',
                'updated_at'      => $now,
            ],
            [
                'material_id'     => null,
                'material_code'   => 'PLN-HVS100',
                'material_name'   => 'HVS 100 gsm',
                'width'           => 65.00,
                'length'          => 90.00,
                'gramature'       => 100,
                'paper_type'      => 'HVS',
                'price_per_plano' => 1600.00,
                'status'          => 'active',
                'created_by'      => 'System Seeder',
                'created_at'      => $now,
                'updated_by'      => 'System Seeder',
                'updated_at'      => $now,
            ],
        ];

        foreach ($planoMaterials as $material) {
            DB::table('m_offset_plano_materials')->updateOrInsert(
                ['material_code' => $material['material_code']],
                $material
            );
        }

        // 2. Master Plat CTP Offset
        $plate = [
            'plate_code'      => 'PLT-SM52',
            'plate_name'      => 'Plat CTP Mesin SM 52 / Oliver',
            'machine_name'    => 'SM 52 / Oliver 58',
            'price_per_plate' => 16000.00,
            'status'          => 'active',
            'created_by'      => 'System Seeder',
            'created_at'      => $now,
            'updated_by'      => 'System Seeder',
            'updated_at'      => $now,
        ];

        DB::table('m_offset_plates')->updateOrInsert(
            ['plate_code' => $plate['plate_code']],
            $plate
        );

        // 3. Master Parameter Cetak & Potong Offset
        $printCost = [
            'name'                   => 'Standar Mesin Offset SM 52',
            'press_sheet_width'      => 45.00,
            'press_sheet_length'     => 32.50,
            'default_insheet_sheets' => 150,
            'click_rate_per_color'   => 15.00,
            'min_print_per_color'    => 65000.00,
            'cutting_fee_per_rim'    => 20000.00,
            'status'                 => 'active',
            'created_by'             => 'System Seeder',
            'created_at'             => $now,
            'updated_by'             => 'System Seeder',
            'updated_at'             => $now,
        ];

        DB::table('m_offset_print_costs')->updateOrInsert(
            ['name' => $printCost['name']],
            $printCost
        );

        // 4. Master Pricing Rules Offset
        $pricingRule = [
            'rule_name'       => 'Pricing Rule Brosur Offset',
            'category_name'   => 'Brosur',
            'min_rim'         => 1,
            'max_rim'         => 10,
            'markup_general'  => 100.00,
            'markup_division' => 80.00,
            'rounding_value'  => 5000.00,
            'status'          => 'active',
            'created_by'      => 'System Seeder',
            'created_at'      => $now,
            'updated_by'      => 'System Seeder',
            'updated_at'      => $now,
        ];

        DB::table('m_offset_pricing_rules')->updateOrInsert(
            ['rule_name' => $pricingRule['rule_name']],
            $pricingRule
        );

        // 5. Master Produk Cetak Offset (Brosur / Flyer)
        $products = [
            [
                'product_code'       => 'BRS-A5',
                'product_name'       => 'Brosur A5',
                'category'           => 'Brosur',
                'final_width'        => 14.80,
                'final_length'       => 21.00,
                'print_sheet_width'  => 32.50,
                'print_sheet_length' => 45.00,
                'sheet_per_rim'      => 125,
                'up_per_sheet'       => 4,
                'plano_cut_yield'    => 4,
                'description'        => 'Brosur ukuran A5 (14.8 x 21 cm), layout 4-up pada lembar cetak mesin SM 52 (45 x 32.5 cm). Dari 1 lembar plano menghasilkan 4 lembar cetak.',
                'status'             => 'active',
                'created_by'         => 'System Seeder',
                'created_at'         => $now,
                'updated_by'         => 'System Seeder',
                'updated_at'         => $now,
            ],
            [
                'product_code'       => 'BRS-A4',
                'product_name'       => 'Brosur A4',
                'category'           => 'Brosur',
                'final_width'        => 21.00,
                'final_length'       => 29.70,
                'print_sheet_width'  => 32.50,
                'print_sheet_length' => 45.00,
                'sheet_per_rim'      => 250,
                'up_per_sheet'       => 2,
                'plano_cut_yield'    => 4,
                'description'        => 'Brosur ukuran A4 (21 x 29.7 cm), layout 2-up pada lembar cetak mesin SM 52 (45 x 32.5 cm). Dari 1 lembar plano menghasilkan 4 lembar cetak.',
                'status'             => 'active',
                'created_by'         => 'System Seeder',
                'created_at'         => $now,
                'updated_by'         => 'System Seeder',
                'updated_at'         => $now,
            ],
            [
                'product_code'       => 'FLY-DL',
                'product_name'       => 'Flyer 1/3 A4 (DL)',
                'category'           => 'Flyer',
                'final_width'        => 10.00,
                'final_length'       => 21.00,
                'print_sheet_width'  => 32.50,
                'print_sheet_length' => 45.00,
                'sheet_per_rim'      => 84,
                'up_per_sheet'       => 6,
                'plano_cut_yield'    => 4,
                'description'        => 'Flyer ukuran DL (10 x 21 cm), layout 6-up pada lembar cetak mesin SM 52 (45 x 32.5 cm).',
                'status'             => 'active',
                'created_by'         => 'System Seeder',
                'created_at'         => $now,
                'updated_by'         => 'System Seeder',
                'updated_at'         => $now,
            ],
        ];

        foreach ($products as $prod) {
            DB::table('m_offset_products')->updateOrInsert(
                ['product_code' => $prod['product_code']],
                $prod
            );
        }
    }
}


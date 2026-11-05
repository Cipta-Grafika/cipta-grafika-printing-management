<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LanyardMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        $tiers = [
            // ==========================================
            // LANYARD 2 CM - HANYA TALI
            // ==========================================
            ['width_type' => '2cm', 'package_type' => 'tali', 'tier_label' => '1–4 pcs', 'min_qty' => 1, 'max_qty' => 4, 'price' => 20000],
            ['width_type' => '2cm', 'package_type' => 'tali', 'tier_label' => '5–12 pcs', 'min_qty' => 5, 'max_qty' => 12, 'price' => 14000],
            ['width_type' => '2cm', 'package_type' => 'tali', 'tier_label' => '13–25 pcs', 'min_qty' => 13, 'max_qty' => 25, 'price' => 13000],
            ['width_type' => '2cm', 'package_type' => 'tali', 'tier_label' => '26–50 pcs', 'min_qty' => 26, 'max_qty' => 50, 'price' => 12000],
            ['width_type' => '2cm', 'package_type' => 'tali', 'tier_label' => '51–100 pcs', 'min_qty' => 51, 'max_qty' => 100, 'price' => 11000],
            ['width_type' => '2cm', 'package_type' => 'tali', 'tier_label' => '> 100 pcs', 'min_qty' => 101, 'max_qty' => null, 'price' => 10000],

            // ==========================================
            // LANYARD 2 CM - 1 SET (HOLDER + PVC)
            // ==========================================
            ['width_type' => '2cm', 'package_type' => 'set', 'tier_label' => '1–4 pcs', 'min_qty' => 1, 'max_qty' => 4, 'price' => 25000],
            ['width_type' => '2cm', 'package_type' => 'set', 'tier_label' => '5–12 pcs', 'min_qty' => 5, 'max_qty' => 12, 'price' => 24000],
            ['width_type' => '2cm', 'package_type' => 'set', 'tier_label' => '13–25 pcs', 'min_qty' => 13, 'max_qty' => 25, 'price' => 23000],
            ['width_type' => '2cm', 'package_type' => 'set', 'tier_label' => '26–50 pcs', 'min_qty' => 26, 'max_qty' => 50, 'price' => 22000],
            ['width_type' => '2cm', 'package_type' => 'set', 'tier_label' => '51–100 pcs', 'min_qty' => 51, 'max_qty' => 100, 'price' => 21000],
            ['width_type' => '2cm', 'package_type' => 'set', 'tier_label' => '> 100 pcs', 'min_qty' => 101, 'max_qty' => null, 'price' => 20000],

            // ==========================================
            // LANYARD 2.5 CM - HANYA TALI
            // ==========================================
            ['width_type' => '2.5cm', 'package_type' => 'tali', 'tier_label' => '1–4 pcs', 'min_qty' => 1, 'max_qty' => 4, 'price' => 23000],
            ['width_type' => '2.5cm', 'package_type' => 'tali', 'tier_label' => '5–12 pcs', 'min_qty' => 5, 'max_qty' => 12, 'price' => 16000],
            ['width_type' => '2.5cm', 'package_type' => 'tali', 'tier_label' => '13–25 pcs', 'min_qty' => 13, 'max_qty' => 25, 'price' => 15000],
            ['width_type' => '2.5cm', 'package_type' => 'tali', 'tier_label' => '26–50 pcs', 'min_qty' => 26, 'max_qty' => 50, 'price' => 14000],
            ['width_type' => '2.5cm', 'package_type' => 'tali', 'tier_label' => '51–100 pcs', 'min_qty' => 51, 'max_qty' => 100, 'price' => 13000],
            ['width_type' => '2.5cm', 'package_type' => 'tali', 'tier_label' => '> 100 pcs', 'min_qty' => 101, 'max_qty' => null, 'price' => 12000],

            // ==========================================
            // LANYARD 2.5 CM - 1 SET (HOLDER + PVC)
            // ==========================================
            ['width_type' => '2.5cm', 'package_type' => 'set', 'tier_label' => '1–4 pcs', 'min_qty' => 1, 'max_qty' => 4, 'price' => 30000],
            ['width_type' => '2.5cm', 'package_type' => 'set', 'tier_label' => '5–12 pcs', 'min_qty' => 5, 'max_qty' => 12, 'price' => 29000],
            ['width_type' => '2.5cm', 'package_type' => 'set', 'tier_label' => '13–25 pcs', 'min_qty' => 13, 'max_qty' => 25, 'price' => 28000],
            ['width_type' => '2.5cm', 'package_type' => 'set', 'tier_label' => '26–50 pcs', 'min_qty' => 26, 'max_qty' => 50, 'price' => 26000],
            ['width_type' => '2.5cm', 'package_type' => 'set', 'tier_label' => '51–100 pcs', 'min_qty' => 51, 'max_qty' => 100, 'price' => 25000],
            ['width_type' => '2.5cm', 'package_type' => 'set', 'tier_label' => '> 100 pcs', 'min_qty' => 101, 'max_qty' => null, 'price' => 24000],
        ];

        foreach ($tiers as $tier) {
            DB::table('m_lanyard_pricings')->updateOrInsert(
                [
                    'width_type'   => $tier['width_type'],
                    'package_type' => $tier['package_type'],
                    'min_qty'      => $tier['min_qty'],
                ],
                [
                    'tier_label'   => $tier['tier_label'],
                    'max_qty'      => $tier['max_qty'],
                    'price'        => $tier['price'],
                    'status'       => 'active',
                    'created_by'   => 'Administrator',
                    'created_at'   => $now,
                    'updated_by'   => 'Administrator',
                    'updated_at'   => $now,
                ]
            );
        }

        // Daftarkan produk ke m_display_products untuk katalog pelanggan jika belum ada
        $displayExisting = DB::table('m_display_products')
            ->whereRaw('LOWER(display_name) LIKE ?', ['%lanyard%'])
            ->first();

        if (!$displayExisting) {
            // Ambil ID Kategori secara dinamis (tanpa hardcode agar aman di semua server/VPS)
            $category = DB::table('m_categories')
                ->whereRaw('LOWER(name) LIKE ?', ['%merchandise%'])
                ->orWhereRaw('LOWER(name) LIKE ?', ['%display%'])
                ->first() ?? DB::table('m_categories')->first();

            $categoryId = $category ? $category->id : DB::table('m_categories')->insertGetId([
                'name' => 'Merchandise',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ]);

            $engine = DB::table('m_engines')
                ->whereRaw('LOWER(name) LIKE ?', ['%digital%'])
                ->first() ?? DB::table('m_engines')->first();

            $location = DB::table('m_locations')->first();

            $displayId = DB::table('m_display_products')->insertGetId([
                'display_name' => 'Tali Lanyard & ID Card Custom',
                'length' => 90.00,
                'width' => 2.00,
                'package_qty' => 1,
                'package_unit' => 'Pcs',
                'category_id' => $categoryId,
                'status' => 'Active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ]);

            DB::table('m_display_product_images')->insert([
                'display_product_id' => $displayId,
                'image_path' => 'images/display/display_lanyard_01.webp',
                'image_name' => 'lanyard_sample.webp',
                'is_primary' => true,
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ]);

            if ($engine && $location) {
                DB::table('m_display_product_configurations')->insert([
                    'display_product_id' => $displayId,
                    'engine_id' => $engine->id,
                    'location_id' => $location->id,
                    'sheet_qty' => 1,
                    'cost_rangka' => 0.00,
                    'cost_finishing' => 0.00,
                    'total_cost' => 0.00,
                    'created_by' => 'Administrator',
                    'created_at' => $now,
                    'updated_by' => 'Administrator',
                    'updated_at' => $now,
                ]);
            }
        }
    }
}

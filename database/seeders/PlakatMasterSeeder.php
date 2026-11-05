<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlakatMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        $items = [
            // --- Akrilik Tanpa Box ---
            [
                'name' => 'Plakat Akrilik 2mm Dudukan Akrilik',
                'material_type' => 'akrilik',
                'thickness' => '2mm',
                'base_type' => 'akrilik',
                'box_type' => 'tanpa_box',
                'dimensions' => '20x15 cm',
                'price' => 66000.00,
                'design_fee' => 0.00,
                'description' => 'Plakat Akrilik cetak UV tebal 2mm dengan dudukan akrilik bening (20x15 cm) tanpa box.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Plakat Akrilik 3mm Dudukan Akrilik',
                'material_type' => 'akrilik',
                'thickness' => '3mm',
                'base_type' => 'akrilik',
                'box_type' => 'tanpa_box',
                'dimensions' => '20x15 cm',
                'price' => 116000.00,
                'design_fee' => 0.00,
                'description' => 'Plakat Akrilik cetak UV tebal 3mm dengan dudukan akrilik bening (20x15 cm) tanpa box.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Plakat Akrilik 3mm Dudukan Hitam',
                'material_type' => 'akrilik',
                'thickness' => '3mm',
                'base_type' => 'hitam',
                'box_type' => 'tanpa_box',
                'dimensions' => '20x15 cm',
                'price' => 158000.00,
                'design_fee' => 0.00,
                'description' => 'Plakat Akrilik cetak UV tebal 3mm dengan tatakan/dudukan hitam elegan (20x15 cm) tanpa box.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Plakat Akrilik 5mm Dudukan Akrilik',
                'material_type' => 'akrilik',
                'thickness' => '5mm',
                'base_type' => 'akrilik',
                'box_type' => 'tanpa_box',
                'dimensions' => '20x15 cm',
                'price' => 134000.00,
                'design_fee' => 0.00,
                'description' => 'Plakat Akrilik cetak UV tebal 5mm dengan dudukan akrilik bening (20x15 cm) tanpa box.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Plakat Akrilik 5mm Dudukan Hitam',
                'material_type' => 'akrilik',
                'thickness' => '5mm',
                'base_type' => 'hitam',
                'box_type' => 'tanpa_box',
                'dimensions' => '20x15 cm',
                'price' => 175000.00,
                'design_fee' => 0.00,
                'description' => 'Plakat Akrilik cetak UV tebal 5mm dengan tatakan/dudukan hitam elegan (20x15 cm) tanpa box.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Plakat Akrilik 1 cm Dudukan Akrilik',
                'material_type' => 'akrilik',
                'thickness' => '1cm',
                'base_type' => 'akrilik',
                'box_type' => 'tanpa_box',
                'dimensions' => '20x15 cm',
                'price' => 250000.00,
                'design_fee' => 0.00,
                'description' => 'Plakat Akrilik cetak UV tebal 1 cm (10mm) solid kokoh dengan dudukan akrilik (20x15 cm) tanpa box.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],

            // --- Akrilik + Box ---
            [
                'name' => 'Plakat Akrilik 2mm Dudukan Akrilik + Box',
                'material_type' => 'akrilik',
                'thickness' => '2mm',
                'base_type' => 'akrilik',
                'box_type' => 'box',
                'dimensions' => '20x15 cm',
                'price' => 150000.00,
                'design_fee' => 0.00,
                'description' => 'Plakat Akrilik cetak UV tebal 2mm dudukan akrilik lengkap dengan box bludru eksklusif.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Plakat Akrilik 3mm Dudukan Akrilik + Box',
                'material_type' => 'akrilik',
                'thickness' => '3mm',
                'base_type' => 'akrilik',
                'box_type' => 'box',
                'dimensions' => '20x15 cm',
                'price' => 210000.00,
                'design_fee' => 0.00,
                'description' => 'Plakat Akrilik cetak UV tebal 3mm dudukan akrilik lengkap dengan box bludru eksklusif.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Plakat Akrilik 3mm Dudukan Hitam + Box',
                'material_type' => 'akrilik',
                'thickness' => '3mm',
                'base_type' => 'hitam',
                'box_type' => 'box',
                'dimensions' => '20x15 cm',
                'price' => 240000.00,
                'design_fee' => 0.00,
                'description' => 'Plakat Akrilik cetak UV tebal 3mm tatakan/dudukan hitam lengkap dengan box bludru eksklusif.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Plakat Akrilik 5mm Dudukan Akrilik + Box',
                'material_type' => 'akrilik',
                'thickness' => '5mm',
                'base_type' => 'akrilik',
                'box_type' => 'box',
                'dimensions' => '20x15 cm',
                'price' => 217000.00,
                'design_fee' => 0.00,
                'description' => 'Plakat Akrilik cetak UV tebal 5mm dudukan akrilik lengkap dengan box bludru eksklusif.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Plakat Akrilik 5mm Dudukan Hitam + Box',
                'material_type' => 'akrilik',
                'thickness' => '5mm',
                'base_type' => 'hitam',
                'box_type' => 'box',
                'dimensions' => '20x15 cm',
                'price' => 258000.00,
                'design_fee' => 0.00,
                'description' => 'Plakat Akrilik cetak UV tebal 5mm tatakan/dudukan hitam lengkap dengan box bludru eksklusif.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Plakat Akrilik 1 cm Dudukan Akrilik + Box',
                'material_type' => 'akrilik',
                'thickness' => '1cm',
                'base_type' => 'akrilik',
                'box_type' => 'box',
                'dimensions' => '20x15 cm',
                'price' => 334000.00,
                'design_fee' => 0.00,
                'description' => 'Plakat Akrilik cetak UV tebal 1 cm (10mm) solid dudukan akrilik lengkap dengan box bludru eksklusif.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],

            // --- Plakat Kayu ---
            [
                'name' => 'Plakat Kayu + Box',
                'material_type' => 'kayu',
                'thickness' => 'standar',
                'base_type' => 'kayu',
                'box_type' => 'box',
                'dimensions' => '20x15 cm',
                'price' => 180000.00,
                'design_fee' => 0.00,
                'description' => 'Plakat kayu ukir/finishing plitur mewah elegan (20x15 cm) lengkap dengan box bludru eksklusif.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
        ];

        foreach ($items as $item) {
            DB::table('m_plakat_pricings')->updateOrInsert(
                [
                    'material_type' => $item['material_type'],
                    'thickness' => $item['thickness'],
                    'base_type' => $item['base_type'],
                    'box_type' => $item['box_type'],
                ],
                $item
            );
        }

        // Daftarkan produk ke m_display_products untuk katalog pelanggan
        $displayExisting = DB::table('m_display_products')
            ->whereRaw('LOWER(display_name) LIKE ?', ['%plakat%'])
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
                'display_name' => 'Plakat Akrilik UV & Kayu (Custom)',
                'length' => 20.00,
                'width' => 15.00,
                'package_qty' => 1,
                'package_unit' => 'Pcs',
                'category_id' => $categoryId,
                'status' => 'Active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ]);

            // Tambahkan gambar produk
            DB::table('m_display_product_images')->insert([
                'display_product_id' => $displayId,
                'image_path' => 'images/display/display_plakat_01.webp',
                'image_name' => 'display_plakat_01.webp',
                'is_primary' => 1,
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ]);

            // Tambahkan konfigurasi display jika engine & location tersedia
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


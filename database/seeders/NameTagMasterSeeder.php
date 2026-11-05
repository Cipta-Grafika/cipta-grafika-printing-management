<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NameTagMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        $items = [
            [
                'name' => 'Papan Nama Biasa',
                'type_code' => 'biasa',
                'fastener_options' => 'Peniti, Magnet',
                'price' => 25000.00,
                'min_order_qty' => 1,
                'description' => 'Papan nama dada formal standar rapi, bahan akrilik putih/hitam tanpa lapisan resin cembung. Pilihan pengait peniti kuat atau magnet neodymium.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Papan Nama Resin',
                'type_code' => 'resin',
                'fastener_options' => 'Peniti, Magnet',
                'price' => 35000.00,
                'min_order_qty' => 1,
                'description' => 'Papan nama dada eksklusif dilapisi resin bening cembung lycal mengkilap, tahan gores dan elegan. Pilihan pengait peniti atau magnet neodymium.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Papan Nama Resin Emas',
                'type_code' => 'resin_emas',
                'fastener_options' => 'Peniti, Magnet',
                'price' => 35000.00,
                'min_order_qty' => 1,
                'description' => 'Papan nama dada premium berlatar emas (gold mirror / brass plate) dengan lapisan resin cembung bening mengkilap. Sangat berkelas untuk eksekutif & instansi.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
        ];

        foreach ($items as $item) {
            DB::table('m_name_tag_pricings')->updateOrInsert(
                ['type_code' => $item['type_code']],
                $item
            );
        }

        // Daftarkan produk ke m_display_products untuk katalog pelanggan
        $displayExisting = DB::table('m_display_products')
            ->whereRaw('LOWER(display_name) LIKE ?', ['%papan nama%'])
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
                'display_name' => 'Papan Nama Dada (Name Tag Custom)',
                'length' => 8.00,
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

            // Tambahkan gambar produk
            DB::table('m_display_product_images')->insert([
                'display_product_id' => $displayId,
                'image_path' => 'images/display/display_papannama_01.webp',
                'image_name' => 'display_papannama_01.webp',
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


<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotaMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        $items = [
            // --- NCR 1 TINTA ---
            [
                'name' => 'Nota NCR 2 Ply (1 Tinta)',
                'paper_type' => 'ncr',
                'ply_count' => 2,
                'ply_label' => '2 Ply',
                'ink_count' => 1,
                'ink_label' => '1 Tinta',
                'sheets_per_book' => 50,
                'price' => 329000.00,
                'description' => 'Nota carbonless NCR rangkap 2 isi 50 set per buku, cetak 1 warna tinta. Tembus otomatis tanpa kertas karbon.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Nota NCR 3 Ply (1 Tinta)',
                'paper_type' => 'ncr',
                'ply_count' => 3,
                'ply_label' => '3 Ply',
                'ink_count' => 1,
                'ink_label' => '1 Tinta',
                'sheets_per_book' => 50,
                'price' => 414000.00,
                'description' => 'Nota carbonless NCR rangkap 3 isi 50 set per buku, cetak 1 warna tinta. Tembus otomatis tanpa kertas karbon.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Nota NCR 4 Ply (1 Tinta)',
                'paper_type' => 'ncr',
                'ply_count' => 4,
                'ply_label' => '4 Ply',
                'ink_count' => 1,
                'ink_label' => '1 Tinta',
                'sheets_per_book' => 50,
                'price' => 600000.00,
                'description' => 'Nota carbonless NCR rangkap 4 isi 50 set per buku, cetak 1 warna tinta. Tembus otomatis tanpa kertas karbon.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],

            // --- NCR 2 TINTA ---
            [
                'name' => 'Nota NCR 2 Ply (2 Tinta)',
                'paper_type' => 'ncr',
                'ply_count' => 2,
                'ply_label' => '2 Ply',
                'ink_count' => 2,
                'ink_label' => '2 Tinta',
                'sheets_per_book' => 50,
                'price' => 414000.00,
                'description' => 'Nota carbonless NCR rangkap 2 isi 50 set per buku, cetak kombinasi 2 warna tinta (misal: Biru + Merah).',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Nota NCR 3 Ply (2 Tinta)',
                'paper_type' => 'ncr',
                'ply_count' => 3,
                'ply_label' => '3 Ply',
                'ink_count' => 2,
                'ink_label' => '2 Tinta',
                'sheets_per_book' => 50,
                'price' => 528000.00,
                'description' => 'Nota carbonless NCR rangkap 3 isi 50 set per buku, cetak kombinasi 2 warna tinta (misal: Biru + Merah).',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Nota NCR 4 Ply (2 Tinta)',
                'paper_type' => 'ncr',
                'ply_count' => 4,
                'ply_label' => '4 Ply',
                'ink_count' => 2,
                'ink_label' => '2 Tinta',
                'sheets_per_book' => 50,
                'price' => 642000.00,
                'description' => 'Nota carbonless NCR rangkap 4 isi 50 set per buku, cetak kombinasi 2 warna tinta (misal: Biru + Merah).',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],

            // --- NCR 3 TINTA ---
            [
                'name' => 'Nota NCR 2 Ply (3 Tinta)',
                'paper_type' => 'ncr',
                'ply_count' => 2,
                'ply_label' => '2 Ply',
                'ink_count' => 3,
                'ink_label' => '3 Tinta',
                'sheets_per_book' => 50,
                'price' => 528000.00,
                'description' => 'Nota carbonless NCR rangkap 2 isi 50 set per buku, cetak kombinasi 3 warna tinta eksklusif dan tajam.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Nota NCR 3 Ply (3 Tinta)',
                'paper_type' => 'ncr',
                'ply_count' => 3,
                'ply_label' => '3 Ply',
                'ink_count' => 3,
                'ink_label' => '3 Tinta',
                'sheets_per_book' => 50,
                'price' => 642000.00,
                'description' => 'Nota carbonless NCR rangkap 3 isi 50 set per buku, cetak kombinasi 3 warna tinta eksklusif dan tajam.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Nota NCR 4 Ply (3 Tinta)',
                'paper_type' => 'ncr',
                'ply_count' => 4,
                'ply_label' => '4 Ply',
                'ink_count' => 3,
                'ink_label' => '3 Tinta',
                'sheets_per_book' => 50,
                'price' => 785000.00,
                'description' => 'Nota carbonless NCR rangkap 4 isi 50 set per buku, cetak kombinasi 3 warna tinta eksklusif dan tajam.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],

            // --- HVS 100 SHEET/BUKU ---
            [
                'name' => 'Nota HVS 1 Ply (1 Tinta)',
                'paper_type' => 'hvs',
                'ply_count' => 1,
                'ply_label' => '1 Ply',
                'ink_count' => 1,
                'ink_label' => '1 Tinta',
                'sheets_per_book' => 100,
                'price' => 200000.00,
                'description' => 'Form nota kertas HVS putih tebal isi 100 lembar per buku (tanpa tembusan), cetak 1 warna tinta.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
            [
                'name' => 'Nota HVS 1 Ply (2 Tinta)',
                'paper_type' => 'hvs',
                'ply_count' => 1,
                'ply_label' => '1 Ply',
                'ink_count' => 2,
                'ink_label' => '2 Tinta',
                'sheets_per_book' => 100,
                'price' => 228500.00,
                'description' => 'Form nota kertas HVS putih tebal isi 100 lembar per buku (tanpa tembusan), cetak kombinasi 2 warna tinta.',
                'status' => 'active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ],
        ];

        foreach ($items as $item) {
            DB::table('m_nota_pricings')->updateOrInsert(
                [
                    'paper_type' => $item['paper_type'],
                    'ply_count'  => $item['ply_count'],
                    'ink_count'  => $item['ink_count'],
                ],
                $item
            );
        }

        // Daftarkan produk ke m_display_products untuk katalog pelanggan
        $displayExisting = DB::table('m_display_products')
            ->whereRaw('LOWER(display_name) LIKE ?', ['%nota%'])
            ->first();

        if (!$displayExisting) {
            $displayId = DB::table('m_display_products')->insertGetId([
                'display_name' => 'Buku Nota (NCR & HVS Custom)',
                'length' => 21.50,
                'width' => 16.50,
                'package_qty' => 1,
                'package_unit' => 'Paket Rim',
                'category_id' => 236, // Display
                'status' => 'Active',
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ]);

            // Tambahkan gambar produk
            DB::table('m_display_product_images')->insert([
                'display_product_id' => $displayId,
                'image_path' => 'images/display/display_nota_01.webp',
                'image_name' => 'display_nota_01.webp',
                'is_primary' => 1,
                'created_by' => 'Administrator',
                'created_at' => $now,
                'updated_by' => 'Administrator',
                'updated_at' => $now,
            ]);

            // Tambahkan konfigurasi display
            DB::table('m_display_product_configurations')->insert([
                'display_product_id' => $displayId,
                'engine_id' => 70,
                'location_id' => 54,
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


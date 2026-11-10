<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;

class MerchandiseAdditionalMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Aman dan idempoten: Tidak merusak data lama, tidak menduplikasi data,
     * dan otomatis membuat tabel jika belum sempat di-migrate.
     */
    public function run(): void
    {
        $now = Carbon::now('Asia/Jakarta');

        $this->command?->info('=====================================================');
        $this->command?->info('  MEMULAI SEEDER MASTER MERCHANDISE TAMBAHAN');
        $this->command?->info('  (Mug Sublimasi, Kartu PVC, & Akrilik UV 2 Muka)');
        $this->command?->info('=====================================================');

        // =========================================================================
        // 0. PASTIKAN STRUKTUR TABEL TERSEDIA (FAIL-SAFE JIKA BELUM MIGRATE)
        // =========================================================================
        if (!Schema::hasTable('m_mug_pricings')) {
            $this->command?->info('Membuat tabel m_mug_pricings...');
            Schema::create('m_mug_pricings', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->default('Mug Sublimasi Keramik SNI (Putih Standar)');
                $table->string('tier_label', 50);
                $table->integer('min_qty');
                $table->integer('max_qty')->nullable();
                $table->decimal('price', 12, 2);
                $table->string('status', 20)->default('active');
                $table->text('description')->nullable();
                $table->string('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['min_qty', 'max_qty', 'status']);
            });
        }

        if (!Schema::hasTable('m_pvc_card_pricings')) {
            $this->command?->info('Membuat tabel m_pvc_card_pricings...');
            Schema::create('m_pvc_card_pricings', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->default('Kartu ID Card / KTA PVC (Standar ATM)');
                $table->string('tier_label', 50);
                $table->integer('min_qty');
                $table->integer('max_qty')->nullable();
                $table->decimal('price', 12, 2);
                $table->string('status', 20)->default('active');
                $table->text('description')->nullable();
                $table->string('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['min_qty', 'max_qty', 'status']);
            });
        }

        if (!Schema::hasTable('m_akrilik_uv_pricings')) {
            $this->command?->info('Membuat tabel m_akrilik_uv_pricings...');
            Schema::create('m_akrilik_uv_pricings', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150);
                $table->string('spec_label', 100)->default('2 Muka (1 Lapis Akrilik 3mm)');
                $table->string('thickness', 30)->default('3mm');
                $table->integer('min_order_qty')->default(1);
                $table->decimal('price', 12, 2);
                $table->string('status', 20)->default('active');
                $table->text('description')->nullable();
                $table->string('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamp('updated_at')->nullable();
                $table->index(['status']);
            });
        }

        // =========================================================================
        // 1. SEED DATA MUG SUBLIMASI KERAMIK SNI (6 TIER RESMI)
        // =========================================================================
        $mugTiers = [
            ['tier_label' => '1–5 pcs',   'min_qty' => 1,  'max_qty' => 5,    'price' => 30000.00],
            ['tier_label' => '6–10 pcs',  'min_qty' => 6,  'max_qty' => 10,   'price' => 28000.00],
            ['tier_label' => '11–20 pcs', 'min_qty' => 11, 'max_qty' => 20,   'price' => 26000.00],
            ['tier_label' => '21–30 pcs', 'min_qty' => 21, 'max_qty' => 30,   'price' => 25000.00],
            ['tier_label' => '31–50 pcs', 'min_qty' => 31, 'max_qty' => 50,   'price' => 24000.00],
            ['tier_label' => '> 50 pcs',  'min_qty' => 51, 'max_qty' => null, 'price' => 23000.00],
        ];

        foreach ($mugTiers as $tier) {
            DB::table('m_mug_pricings')->updateOrInsert(
                ['min_qty' => $tier['min_qty']],
                [
                    'name'        => 'Mug Sublimasi Keramik SNI (Putih Standar)',
                    'tier_label'  => $tier['tier_label'],
                    'max_qty'     => $tier['max_qty'],
                    'price'       => $tier['price'],
                    'status'      => 'active',
                    'description' => 'Mug keramik putih standar SNI kualitas premium (11 oz / ~325 ml), cetak sublimasi full color tajam, anti luntur, food-grade aman dipakai minum.',
                    'created_by'  => 'Administrator',
                    'created_at'  => $now,
                    'updated_by'  => 'Administrator',
                    'updated_at'  => $now,
                ]
            );
        }
        $this->command?->info('✓ Master Tier Mug Sublimasi (6 Tier) berhasil disimpan.');

        // =========================================================================
        // 2. SEED DATA KARTU PVC (ID CARD & KTA) (7 TIER RESMI)
        // =========================================================================
        $pvcTiers = [
            ['tier_label' => '1 pcs',       'min_qty' => 1,   'max_qty' => 1,    'price' => 30000.00],
            ['tier_label' => '2–5 pcs',     'min_qty' => 2,   'max_qty' => 5,    'price' => 20000.00],
            ['tier_label' => '6–10 pcs',    'min_qty' => 6,   'max_qty' => 10,   'price' => 15000.00],
            ['tier_label' => '11–50 pcs',   'min_qty' => 11,  'max_qty' => 50,   'price' => 10000.00],
            ['tier_label' => '51–100 pcs',  'min_qty' => 51,  'max_qty' => 100,  'price' => 7000.00],
            ['tier_label' => '101–300 pcs', 'min_qty' => 101, 'max_qty' => 300,  'price' => 5000.00],
            ['tier_label' => '> 300 pcs',   'min_qty' => 301, 'max_qty' => null, 'price' => 5000.00],
        ];

        foreach ($pvcTiers as $tier) {
            DB::table('m_pvc_card_pricings')->updateOrInsert(
                ['min_qty' => $tier['min_qty']],
                [
                    'name'        => 'Kartu ID Card / KTA PVC (Standar ATM)',
                    'tier_label'  => $tier['tier_label'],
                    'max_qty'     => $tier['max_qty'],
                    'price'       => $tier['price'],
                    'status'      => 'active',
                    'description' => 'Cetak kartu bahan PVC tebal 0.76mm standar kartu ATM perbankan (CR-80), cetak 2 sisi full color resolusi tinggi, tahan air, tidak patah & tidak luntur.',
                    'created_by'  => 'Administrator',
                    'created_at'  => $now,
                    'updated_by'  => 'Administrator',
                    'updated_at'  => $now,
                ]
            );
        }
        $this->command?->info('✓ Master Tier Kartu PVC ID Card & KTA (7 Tier) berhasil disimpan.');

        // =========================================================================
        // 3. SEED DATA BAHAN AKRILIK UV (2 MUKA 1 LAPIS)
        // =========================================================================
        $akrilikItems = [
            [
                'name'          => 'Gantungan Kunci / Merchandise Akrilik UV 2 Muka',
                'spec_label'    => '2 Muka (1 Lapis Akrilik 3mm)',
                'thickness'     => '3mm',
                'min_order_qty' => 1,
                'price'         => 20000.00,
                'status'        => 'active',
                'description'   => 'Cetak UV 2 muka bolak-balik langsung di atas 1 lapis akrilik bening 3mm dengan mesin UV flatbed Jepang. Potong laser presisi sesuai bentuk pola desain (cutting custom). Termasuk ring gantungan kunci kuat.',
            ],
            [
                'name'          => 'Standee / Plakat Mini Akrilik UV 2 Muka',
                'spec_label'    => '2 Muka (1 Lapis Akrilik 3mm + Dudukan)',
                'thickness'     => '3mm',
                'min_order_qty' => 1,
                'price'         => 20000.00,
                'status'        => 'active',
                'description'   => 'Merchandise standee karakter / display meja mini 2 muka bolak-balik akrilik 3mm, sudah termasuk dudukan alas tancap akrilik bening.',
            ],
        ];

        foreach ($akrilikItems as $item) {
            DB::table('m_akrilik_uv_pricings')->updateOrInsert(
                ['name' => $item['name']],
                [
                    'spec_label'    => $item['spec_label'],
                    'thickness'     => $item['thickness'],
                    'min_order_qty' => $item['min_order_qty'],
                    'price'         => $item['price'],
                    'status'        => $item['status'],
                    'description'   => $item['description'],
                    'created_by'    => 'Administrator',
                    'created_at'    => $now,
                    'updated_by'    => 'Administrator',
                    'updated_at'    => $now,
                ]
            );
        }
        $this->command?->info('✓ Master Bahan Akrilik UV 2 Muka berhasil disimpan.');

        // =========================================================================
        // 4. DAFTARKAN PRODUK DISPLAY KE KATALOG CUSTOMER JIKA BELUM ADA
        // =========================================================================
        $category = DB::table('m_categories')
            ->whereRaw('LOWER(name) LIKE ?', ['%display%'])
            ->orWhereRaw('LOWER(name) LIKE ?', ['%merchandise%'])
            ->first() ?? DB::table('m_categories')->first();

        $categoryId = $category ? $category->id : 236;

        $engine = DB::table('m_engines')
            ->whereRaw('LOWER(name) LIKE ?', ['%digital%'])
            ->first() ?? DB::table('m_engines')->first();

        $location = DB::table('m_locations')->first();

        $displayProducts = [
            [
                'name'   => 'Mug Sublimasi Keramik Custom',
                'length' => 11.00,
                'width'  => 8.50,
                'img'    => 'images/display/display_mug_01.webp',
            ],
            [
                'name'   => 'Cetak Kartu PVC (ID Card & KTA)',
                'length' => 8.60,
                'width'  => 5.40,
                'img'    => 'images/display/display_idcard_pvc_01.webp',
            ],
            [
                'name'   => 'Merchandise Akrilik UV Custom (2 Muka)',
                'length' => 7.00,
                'width'  => 5.00,
                'img'    => 'images/display/display_akrilik_uv_01.webp',
            ],
        ];

        foreach ($displayProducts as $dp) {
            $existing = DB::table('m_display_products')
                ->whereRaw('LOWER(display_name) = ?', [strtolower($dp['name'])])
                ->first();

            if (!$existing) {
                $displayId = DB::table('m_display_products')->insertGetId([
                    'display_name' => $dp['name'],
                    'length'       => $dp['length'],
                    'width'        => $dp['width'],
                    'package_qty'  => 1,
                    'package_unit' => 'Pcs',
                    'category_id'  => $categoryId,
                    'status'       => 'Active',
                    'created_by'   => 'Administrator',
                    'created_at'   => $now,
                    'updated_by'   => 'Administrator',
                    'updated_at'   => $now,
                ]);

                DB::table('m_display_product_images')->insert([
                    'display_product_id' => $displayId,
                    'image_path'         => $dp['img'],
                    'image_name'         => basename($dp['img']),
                    'is_primary'         => 1,
                    'created_by'         => 'Administrator',
                    'created_at'         => $now,
                    'updated_by'         => 'Administrator',
                    'updated_at'         => $now,
                ]);

                if ($engine && $location) {
                    DB::table('m_display_product_configurations')->insert([
                        'display_product_id' => $displayId,
                        'engine_id'          => $engine->id,
                        'location_id'        => $location->id,
                        'sheet_qty'          => 1,
                        'cost_rangka'        => 0.00,
                        'cost_finishing'     => 0.00,
                        'total_cost'         => 0.00,
                        'created_by'         => 'Administrator',
                        'created_at'         => $now,
                        'updated_by'         => 'Administrator',
                        'updated_at'         => $now,
                    ]);
                }

                $this->command?->info("✓ Produk katalog '{$dp['name']}' baru berhasil didaftarkan.");
            } else {
                $this->command?->info("• Produk katalog '{$dp['name']}' sudah ada (ID: {$existing->id}), dilewati.");
            }
        }

        $this->command?->info('=====================================================');
        $this->command?->info('  SEMUA DATA MASTER BERHASIL DIPROSES DENGAN AMAN!');
        $this->command?->info('=====================================================');
    }
}

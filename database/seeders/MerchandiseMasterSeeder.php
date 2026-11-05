<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MerchandiseMasterSeeder extends Seeder
{
    /**
     * Run all Merchandise Master Seeders (Lanyard, Papan Nama Dada, Plakat, Buku Nota).
     *
     * Seeder ini 100% aman (idempotent), menggunakan `updateOrInsert` sehingga
     * tidak akan menggandakan data, tidak menghapus tabel lain, dan tidak
     * merusak struktur atau formula perhitungan yang sudah berjalan.
     */
    public function run(): void
    {
        $this->command?->info('Mulai melakukan seeding Master Merchandise (Cipta Grafika)...');

        // 1. Tali Lanyard & ID Card Custom (24 Tier)
        $this->call(LanyardMasterSeeder::class);
        $this->command?->info('✓ [1/4] Master Lanyard (24 Tier) berhasil di-seed.');

        // 2. Papan Nama Dada / Name Tag Custom (3 Varian)
        $this->call(NameTagMasterSeeder::class);
        $this->command?->info('✓ [2/4] Master Papan Nama Dada (3 Varian) berhasil di-seed.');

        // 3. Plakat Akrilik UV & Kayu Custom (13 Tipe)
        $this->call(PlakatMasterSeeder::class);
        $this->command?->info('✓ [3/4] Master Plakat Akrilik & Kayu (13 Tipe) berhasil di-seed.');

        // 4. Buku Nota NCR & HVS Custom (11 Varian)
        $this->call(NotaMasterSeeder::class);
        $this->command?->info('✓ [4/4] Master Buku Nota NCR & HVS (11 Varian) berhasil di-seed.');

        $this->command?->info('Semua data Master Merchandise (51 Item Pricelist) berhasil disinkronkan!');
    }
}


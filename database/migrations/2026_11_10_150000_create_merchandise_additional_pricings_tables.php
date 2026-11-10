<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabel Master Tier Harga Mug Sublimation
        if (!Schema::hasTable('m_mug_pricings')) {
            Schema::create('m_mug_pricings', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->default('Mug Sublimasi Keramik SNI')->comment('Nama produk/varian mug');
                $table->string('tier_label', 50)->comment('Label rentang kuantiti, misal: 1-5 pcs, > 51 pcs');
                $table->integer('min_qty')->comment('Batas bawah kuantiti');
                $table->integer('max_qty')->nullable()->comment('Batas atas kuantiti (null jika tak terhingga)');
                $table->decimal('price', 12, 2)->comment('Harga satuan rupiah');
                $table->string('status', 20)->default('active')->comment('active atau inactive');
                $table->text('description')->nullable()->comment('Keterangan spesifikasi mug');
                $table->string('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->index(['min_qty', 'max_qty', 'status']);
            });
        }

        // 2. Tabel Master Tier Harga Kartu PVC (ID Card & KTA)
        if (!Schema::hasTable('m_pvc_card_pricings')) {
            Schema::create('m_pvc_card_pricings', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->default('Kartu ID Card / KTA PVC (Standar ATM)')->comment('Nama varian kartu');
                $table->string('tier_label', 50)->comment('Label rentang kuantiti, misal: 1 pcs, 2-5 pcs, > 300 pcs');
                $table->integer('min_qty')->comment('Batas bawah kuantiti');
                $table->integer('max_qty')->nullable()->comment('Batas atas kuantiti (null jika tak terhingga)');
                $table->decimal('price', 12, 2)->comment('Harga satuan rupiah');
                $table->string('status', 20)->default('active')->comment('active atau inactive');
                $table->text('description')->nullable()->comment('Keterangan bahan kartu');
                $table->string('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->index(['min_qty', 'max_qty', 'status']);
            });
        }

        // 3. Tabel Master Harga Bahan Akrilik UV (2 Muka 1 Lapis)
        if (!Schema::hasTable('m_akrilik_uv_pricings')) {
            Schema::create('m_akrilik_uv_pricings', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150)->comment('Nama produk, misal: Gantungan Kunci / Merchandise Akrilik UV 2 Muka');
                $table->string('spec_label', 100)->default('2 Muka (1 Lapis Akrilik)')->comment('Label spesifikasi cetak');
                $table->string('thickness', 30)->default('3mm')->comment('Ketebalan akrilik');
                $table->integer('min_order_qty')->default(1)->comment('Minimal order pcs');
                $table->decimal('price', 12, 2)->comment('Harga satuan rupiah');
                $table->string('status', 20)->default('active')->comment('active atau inactive');
                $table->text('description')->nullable()->comment('Keterangan spesifikasi akrilik UV');
                $table->string('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->index(['status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_akrilik_uv_pricings');
        Schema::dropIfExists('m_pvc_card_pricings');
        Schema::dropIfExists('m_mug_pricings');
    }
};

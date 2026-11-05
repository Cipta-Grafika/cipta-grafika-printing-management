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
        if (!Schema::hasTable('m_plakat_pricings')) {
            Schema::create('m_plakat_pricings', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150)->comment('Nama plakat, misal: Plakat Akrilik 2mm Dudukan Akrilik');
                $table->string('material_type', 50)->comment('akrilik atau kayu');
                $table->string('thickness', 30)->comment('2mm, 3mm, 5mm, 1cm, standar');
                $table->string('base_type', 50)->comment('akrilik, hitam, kayu');
                $table->string('box_type', 50)->comment('tanpa_box, box');
                $table->string('dimensions', 50)->default('20x15 cm')->comment('Ukuran plakat');
                $table->decimal('price', 12, 2)->comment('Harga satuan rupiah');
                $table->decimal('design_fee', 12, 2)->default(0)->comment('Biaya opsional jika belum ada desain');
                $table->text('description')->nullable()->comment('Keterangan atau spesifikasi produk');
                $table->string('status', 20)->default('active')->comment('active atau inactive');
                $table->string('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->index(['material_type', 'thickness', 'base_type', 'box_type', 'status'], 'idx_plakat_search');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_plakat_pricings');
    }
};


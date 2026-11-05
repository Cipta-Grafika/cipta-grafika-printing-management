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
        if (!Schema::hasTable('m_name_tag_pricings')) {
            Schema::create('m_name_tag_pricings', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->comment('Nama varian papan nama, misal: Papan Nama Biasa, Papan Nama Resin');
                $table->string('type_code', 50)->comment('Kode varian: biasa, resin, resin_emas');
                $table->string('fastener_options', 100)->default('Peniti, Magnet')->comment('Pilihan pengait yang tersedia');
                $table->decimal('price', 12, 2)->comment('Harga satuan rupiah');
                $table->integer('min_order_qty')->default(1)->comment('Minimal order pcs');
                $table->text('description')->nullable()->comment('Keterangan atau keunggulan spesifikasi');
                $table->string('status', 20)->default('active')->comment('active atau inactive');
                $table->string('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->index(['type_code', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_name_tag_pricings');
    }
};


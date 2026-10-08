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
        Schema::table('m_display_product_configurations', function (Blueprint $table) {
            // Hapus relasi component lama
            $table->dropForeign([
                'display_product_component_id'
            ]);

            $table->dropColumn(
                'display_product_component_id'
            );

            // Tambahkan relasi configuration
            $table->foreignId('display_product_id')
                ->constrained('m_display_products')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('engine_id')
                ->constrained('m_engines')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('location_id')
                ->constrained('m_locations')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            // Harga component dipindahkan ke tabel detail nanti
            $table->dropColumn([
                'general_price',
                'division_price',
                'plain_price',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_display_product_configurations', function (Blueprint $table) {
            $table->foreignId('display_product_component_id')
                ->constrained('m_display_product_components')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->decimal(
                'general_price',
                12,
                2
            );

            $table->decimal(
                'division_price',
                12,
                2
            );

            $table->decimal(
                'plain_price',
                12,
                2
            );

            $table->dropForeign([
                'display_product_id'
            ]);

            $table->dropForeign([
                'engine_id'
            ]);

            $table->dropForeign([
                'location_id'
            ]);

            $table->dropColumn([
                'display_product_id',
                'engine_id',
                'location_id',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('m_production_costs', function (Blueprint $table) {

            // Hapus foreign key
            $table->dropForeign(['location_id']);
            $table->dropForeign(['material_id']);

            // Hapus kolom yang tidak digunakan
            $table->dropColumn([
                'location_id',
                'material_id',
                'production_cost',
                'finishing_cost',
                'total_cost',
                'harga_polos',
                'harga_umum',
                'harga_divisi',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('m_production_costs', function (Blueprint $table) {

            // Kembalikan kolom
            $table->foreignId('location_id')
                ->constrained('m_locations')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('material_id')
                ->constrained('m_materials')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->decimal('production_cost', 12, 2);

            $table->decimal('finishing_cost', 12, 2);

            $table->decimal('total_cost', 12, 2)
                ->nullable();

            $table->decimal('harga_polos', 12, 2)
                ->nullable();

            $table->decimal('harga_umum', 12, 2)
                ->nullable();

            $table->decimal('harga_divisi', 12, 2)
                ->nullable();
        });
    }
};

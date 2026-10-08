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
        Schema::table('m_production_costs', function (Blueprint $table) {
            Schema::table('m_production_costs', function (Blueprint $table) {
                $table->decimal('total_cost', 12, 2)->nullable()->after('finishing_cost');
                $table->decimal('harga_polos', 12, 2)->nullable()->after('total_cost');
                $table->decimal('harga_umum', 12, 2)->nullable()->after('harga_polos');
                $table->decimal('harga_divisi', 12, 2)->nullable()->after('harga_umum');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_production_costs', function (Blueprint $table) {
            //
        });
    }
};

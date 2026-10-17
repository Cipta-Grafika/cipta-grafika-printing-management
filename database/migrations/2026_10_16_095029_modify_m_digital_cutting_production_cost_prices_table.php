<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Bersihkan data lama sebelum modifikasi struktur agar tidak melanggar unique constraint baru
        DB::table('m_digital_cutting_production_cost_prices')->delete();
        DB::table('m_digital_cutting_production_cost_details')->delete();
        DB::table('m_digital_cutting_production_costs')->delete();

        Schema::table('m_digital_cutting_production_cost_prices', function (Blueprint $table) {
            $table->dropUnique('dig_cut_pc_prices_unique_tier');
            $table->dropColumn('side_type');

            $table->unique([
                'digital_cutting_production_cost_detail_id',
                'price_type',
                'quantity_tier',
            ], 'dig_cut_pc_prices_unique_tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_digital_cutting_production_cost_prices', function (Blueprint $table) {
            $table->dropUnique('dig_cut_pc_prices_unique_tier');
            $table->string('side_type')->after('price_type')->default('single');

            $table->unique([
                'digital_cutting_production_cost_detail_id',
                'price_type',
                'side_type',
                'quantity_tier',
            ], 'dig_cut_pc_prices_unique_tier');
        });
    }
};

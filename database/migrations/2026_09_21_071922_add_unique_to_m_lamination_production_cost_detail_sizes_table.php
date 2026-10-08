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
        Schema::table('m_lamination_production_cost_detail_sizes', function (Blueprint $table) {
            $table->unique(
                [
                    'lamination_production_cost_detail_id',
                    'lamination_size_id',
                ],
                'm_lpc_detail_size_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_lamination_production_cost_detail_sizes', function (Blueprint $table) {
            $table->dropUnique('m_lpc_detail_size_unique');
        });
    }
};

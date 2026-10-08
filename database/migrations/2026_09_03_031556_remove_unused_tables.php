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
        Schema::dropIfExists('m_services');
        Schema::dropIfExists('m_pricing_policies');
        Schema::dropIfExists('m_pricing_lists');
        Schema::dropIfExists('m_materials');
        Schema::dropIfExists('m_engine_rates');
        Schema::dropIfExists('m_calculation_rules');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

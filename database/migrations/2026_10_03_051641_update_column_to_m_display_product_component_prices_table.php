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
        Schema::table('m_display_product_component_prices', function (Blueprint $table) {
            $table->decimal('rounding_value_component', 15, 2)->nullable()->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_display_product_component_prices', function (Blueprint $table) {
            // $table->dropColumn('rounding_value_component');
        });
    }
};

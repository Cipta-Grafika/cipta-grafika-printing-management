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
            $table->decimal('general_price_2_side', 15, 2)->nullable()->after('division_price');
            $table->decimal('division_price_2_side', 15, 2)->nullable()->after('general_price_2_side');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_display_product_component_prices', function (Blueprint $table) {
            $table->dropColumn(['general_price_2_side', 'division_price_2_side']);
        });
    }
};


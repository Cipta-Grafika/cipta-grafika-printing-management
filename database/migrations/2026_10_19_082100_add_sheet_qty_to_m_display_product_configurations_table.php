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
            $table->integer('sheet_qty')->nullable()->after('total_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_display_product_configurations', function (Blueprint $table) {
            $table->dropColumn('sheet_qty');
        });
    }
};


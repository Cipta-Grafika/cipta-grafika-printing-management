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
        Schema::table('m_display_products', function (Blueprint $table) {
            $table->integer('package_qty')->default(1)->nullable()->after('width');
            $table->string('package_unit', 50)->default('Pcs')->nullable()->after('package_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_display_products', function (Blueprint $table) {
            $table->dropColumn(['package_qty', 'package_unit']);
        });
    }
};


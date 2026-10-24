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
        Schema::table('m_offset_products', function (Blueprint $table) {
            $table->integer('sheet_per_rim')->default(125)->after('up_per_sheet')->comment('Qty Pokok Lembar Cetak Mesin per Rim (Kolom F Excel)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_offset_products', function (Blueprint $table) {
            $table->dropColumn('sheet_per_rim');
        });
    }
};


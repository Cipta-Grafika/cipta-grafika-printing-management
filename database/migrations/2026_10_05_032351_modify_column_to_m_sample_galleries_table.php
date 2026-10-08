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
        Schema::table('m_sample_galleries', function (Blueprint $table) {
            $table->string('image_hash', 64)->nullable()->after('image_name');

            $table->unique(
                ['engine_id', 'category_id', 'image_hash'],
                'm_sample_galleries_engine_category_hash_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_sample_galleries', function (Blueprint $table) {
            //
        });
    }
};

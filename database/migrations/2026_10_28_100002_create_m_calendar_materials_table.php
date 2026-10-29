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
        Schema::create('m_calendar_materials', function (Blueprint $table) {
            $table->id();
            $table->string('material_code', 50)->nullable();
            $table->string('material_name', 150);
            $table->string('category', 20)->comment('AP, AC, BW');
            $table->decimal('digital_cost_print_1_side', 15, 2)->default(0)->comment('Cost print 1 sisi A3+');
            $table->decimal('digital_cost_print_2_side', 15, 2)->default(0)->comment('Cost print 2 sisi A3+');
            $table->decimal('digital_paper_cost', 15, 2)->default(0)->comment('Harga kertas per lembar A3+');
            $table->decimal('offset_price_65x90', 15, 2)->nullable()->comment('Harga per plano 65x90');
            $table->decimal('offset_price_65x100', 15, 2)->nullable()->comment('Harga per plano 65x100');
            $table->decimal('offset_price_109x79', 15, 2)->nullable()->comment('Harga per plano 109x79');
            $table->string('status', 20)->default('active');
            $table->string('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_calendar_materials');
    }
};


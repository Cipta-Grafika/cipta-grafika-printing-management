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
        Schema::create('m_offset_print_costs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->default('Standar Mesin Offset SM 52');
            $table->decimal('press_sheet_width', 10, 2)->default(45.00);
            $table->decimal('press_sheet_length', 10, 2)->default(32.50);
            $table->integer('default_insheet_sheets')->default(150);
            $table->decimal('click_rate_per_color', 12, 2)->default(15.00);
            $table->decimal('min_print_per_color', 15, 2)->default(65000.00);
            $table->decimal('cutting_fee_per_rim', 15, 2)->default(20000.00);
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
        Schema::dropIfExists('m_offset_print_costs');
    }
};


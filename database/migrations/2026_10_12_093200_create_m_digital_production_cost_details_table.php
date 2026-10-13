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
        Schema::create('m_digital_production_cost_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('digital_production_cost_id');
            $table->foreign('digital_production_cost_id', 'digital_pc_details_cost_fk')
                ->references('id')
                ->on('m_digital_production_costs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('material_id');
            $table->foreign('material_id')->references('id')->on('m_materials')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('paper_cost', 12, 2);
            $table->decimal('click_cost', 12, 2);
            $table->decimal('single_print_cost', 12, 2);
            $table->decimal('double_print_cost', 12, 2);
            $table->string('created_by');
            $table->timestamp('created_at');
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(
                ['digital_production_cost_id', 'material_id'],
                'digital_production_cost_details_unique_material'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_digital_production_cost_details');
    }
};

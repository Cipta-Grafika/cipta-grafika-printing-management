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
        Schema::create('m_digital_cutting_production_cost_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('digital_cutting_production_cost_id');
            $table->foreign('digital_cutting_production_cost_id', 'dig_cut_pc_details_cost_fk')
                ->references('id')
                ->on('m_digital_cutting_production_costs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('cutting_id');
            $table->foreign('cutting_id', 'dig_cut_pc_details_cutting_fk')
                ->references('id')->on('m_cuttings')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id', 'dig_cut_pc_details_category_fk')
                ->references('id')->on('m_categories')->onDelete('cascade')->onUpdate('cascade');
            $table->string('created_by');
            $table->timestamp('created_at');
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(
                ['digital_cutting_production_cost_id', 'cutting_id', 'category_id'],
                'dig_cut_pc_details_unique_cutting_category'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_digital_cutting_production_cost_details');
    }
};

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
        Schema::create('m_digital_lamination_production_cost_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('digital_lamination_production_cost_id');
            $table->foreign('digital_lamination_production_cost_id', 'dig_lam_pc_details_cost_fk')
                ->references('id')
                ->on('m_digital_lamination_production_costs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('lamination_id');
            $table->foreign('lamination_id', 'dig_lam_pc_details_lamination_fk')
                ->references('id')->on('m_laminations')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id', 'dig_lam_pc_details_category_fk')
                ->references('id')->on('m_categories')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('paper_cost', 12, 2);
            $table->decimal('click_cost', 12, 2);
            $table->decimal('single_print_cost', 12, 2);
            $table->decimal('double_print_cost', 12, 2);
            $table->string('created_by');
            $table->timestamp('created_at');
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(
                ['digital_lamination_production_cost_id', 'lamination_id', 'category_id'],
                'dig_lam_pc_details_unique_lamination_category'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_digital_lamination_production_cost_details');
    }
};

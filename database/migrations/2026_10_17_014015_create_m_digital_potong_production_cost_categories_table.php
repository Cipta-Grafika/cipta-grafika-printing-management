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
        Schema::create('m_digital_potong_production_cost_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('digital_potong_production_cost_id');
            $table->foreign('digital_potong_production_cost_id', 'dig_pot_pc_cats_cost_fk')
                ->references('id')
                ->on('m_digital_potong_production_costs')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id', 'dig_pot_pc_cats_category_fk')
                ->references('id')->on('m_categories')->onDelete('cascade')->onUpdate('cascade');
            $table->string('created_by');
            $table->timestamp('created_at');
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(
                ['digital_potong_production_cost_id', 'category_id'],
                'dig_pot_pc_cats_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_digital_potong_production_cost_categories');
    }
};

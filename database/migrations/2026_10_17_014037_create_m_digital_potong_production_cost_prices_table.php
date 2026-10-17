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
        Schema::create('m_digital_potong_production_cost_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('digital_potong_production_cost_category_id');
            $table->foreign('digital_potong_production_cost_category_id', 'dig_pot_pc_prices_cat_fk')
                ->references('id')
                ->on('m_digital_potong_production_cost_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('size_class');
            $table->string('price_type');
            $table->decimal('min_price', 15, 2);
            $table->string('created_by');
            $table->timestamp('created_at');
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique(
                ['digital_potong_production_cost_category_id', 'size_class', 'price_type'],
                'dig_pot_pc_prices_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_digital_potong_production_cost_prices');
    }
};

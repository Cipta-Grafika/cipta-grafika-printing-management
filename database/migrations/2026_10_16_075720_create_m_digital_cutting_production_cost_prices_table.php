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
        Schema::create('m_digital_cutting_production_cost_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('digital_cutting_production_cost_detail_id');
            $table->foreign('digital_cutting_production_cost_detail_id', 'dig_cut_pc_prices_detail_fk')
                ->references('id')
                ->on('m_digital_cutting_production_cost_details')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('price_type');
            $table->string('side_type');
            $table->string('quantity_tier');
            $table->unsignedInteger('minimum_quantity');
            $table->unsignedInteger('maximum_quantity')->nullable();
            $table->decimal('price', 12, 2);
            $table->string('created_by');
            $table->timestamp('created_at');
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique([
                'digital_cutting_production_cost_detail_id',
                'price_type',
                'side_type',
                'quantity_tier',
            ], 'dig_cut_pc_prices_unique_tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_digital_cutting_production_cost_prices');
    }
};

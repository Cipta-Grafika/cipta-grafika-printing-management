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
        Schema::create('m_digital_production_cost_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('digital_production_cost_detail_id');
            $table->foreign('digital_production_cost_detail_id', 'digital_pc_prices_cost_detail_fk')
                ->references('id')
                ->on('m_digital_production_cost_details')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->unsignedBigInteger('digital_pricing_rule_detail_id');
            $table->foreign('digital_pricing_rule_detail_id', 'digital_pc_prices_pricing_detail_fk')
                ->references('id')
                ->on('m_digital_pricing_rule_details')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->string('price_type');
            $table->string('print_type');
            $table->string('quantity_tier');
            $table->unsignedInteger('minimum_quantity');
            $table->unsignedInteger('maximum_quantity')->nullable();
            $table->decimal('markup_percentage', 8, 2);
            $table->decimal('rounding_value', 12, 2)->nullable();
            $table->decimal('selling_price', 12, 2);
            $table->string('created_by');
            $table->timestamp('created_at');
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique([
                'digital_production_cost_detail_id',
                'price_type',
                'print_type',
                'quantity_tier',
            ], 'digital_production_cost_prices_unique_tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_digital_production_cost_prices');
    }
};

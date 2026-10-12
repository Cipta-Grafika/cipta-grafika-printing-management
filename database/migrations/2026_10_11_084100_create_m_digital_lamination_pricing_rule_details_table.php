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
        Schema::create('m_digital_lamination_pricing_rule_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('digital_lamination_pricing_rule_id');
            $table->foreign('digital_lamination_pricing_rule_id', 'dig_lam_pr_details_rule_fk')
                ->references('id')
                ->on('m_digital_lamination_pricing_rules')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('price_type');
            $table->string('print_type');
            $table->string('quantity_tier');
            $table->unsignedInteger('minimum_quantity');
            $table->unsignedInteger('maximum_quantity')->nullable();
            $table->decimal('markup_percentage', 8, 2);
            $table->decimal('rounding_value', 12, 2)->nullable();
            $table->string('status');
            $table->string('created_by');
            $table->timestamp('created_at');
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->unique([
                'digital_lamination_pricing_rule_id',
                'price_type',
                'print_type',
                'quantity_tier',
            ], 'dig_lam_pr_details_unique_tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_digital_lamination_pricing_rule_details');
    }
};

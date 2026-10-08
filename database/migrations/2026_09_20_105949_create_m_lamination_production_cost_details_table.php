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
        Schema::create('m_lamination_production_cost_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('production_cost_id');
            $table->foreign('production_cost_id')
                ->references('id')
                ->on('m_production_costs')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedBigInteger('lamination_id');
            $table->foreign('lamination_id')
                ->references('id')
                ->on('m_laminations')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->decimal('production_cost', 12, 2);
            $table->decimal('finishing_cost', 12, 2);
            $table->decimal('total_cost', 12, 2);

            $table->decimal('price_per_meter', 12, 2);

            $table->decimal('general_price', 12, 2);
            $table->decimal('division_price', 12, 2);
            $table->decimal('plain_price', 12, 2);

            $table->unsignedBigInteger('general_pricing_rule_id')->nullable();
            $table->foreign('general_pricing_rule_id')
                ->references('id')
                ->on('m_lamination_pricing_rules')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->unsignedBigInteger('division_pricing_rule_id')->nullable();
            $table->foreign('division_pricing_rule_id')
                ->references('id')
                ->on('m_lamination_pricing_rules')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->unsignedBigInteger('plain_pricing_rule_id')->nullable();
            $table->foreign('plain_pricing_rule_id')
                ->references('id')
                ->on('m_lamination_pricing_rules')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->string('created_by');
            $table->timestamp('created_at');
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_lamination_production_cost_details');
    }
};

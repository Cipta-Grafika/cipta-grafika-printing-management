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
        Schema::create('m_production_cost_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_cost_id');
            $table->foreign('production_cost_id')->references('id')->on('m_production_costs')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('material_id');
            $table->foreign('material_id')->references('id')->on('m_materials')->onUpdate('cascade')->onDelete('cascade');
            $table->decimal('production_cost', 12, 2);
            $table->decimal('finishing_cost', 12, 2);
            $table->decimal('total_cost', 12, 2);
            $table->string('created_by');
            $table->timestamp('created_at');
            $table->string('updated_by');
            $table->timestamp('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_production_cost_details');
    }
};

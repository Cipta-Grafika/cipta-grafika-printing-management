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
        Schema::create('m_production_cost_detail_widths', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_cost_detail_id');
            $table->foreign('production_cost_detail_id')->references('id')->on('m_production_cost_details')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('material_size_id');
            $table->foreign('material_size_id')->references('id')->on('m_material_sizes')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('m_production_cost_detail_widths');
    }
};

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
        Schema::create('m_display_product_components', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('display_product_id');
            $table->foreign('display_product_id')->references('id')->on('m_display_products')->onDelete('cascade')->onUpdate('cascade');
            $table->string('component_type');
            $table->unsignedBigInteger('material_id');
            $table->foreign('material_id')->references('id')->on('m_materials')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('lamination_id');
            $table->foreign('lamination_id')->references('id')->on('m_laminations')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('m_display_product_components');
    }
};

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
        Schema::create('m_display_product_component_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('configuration_id');
            $table->foreign('configuration_id')->references('id')->on('m_display_product_configurations')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('component_id');
            $table->foreign('component_id')->references('id')->on('m_display_product_components')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('general_price', 12, 2);
            $table->decimal('division_price', 12, 2);
            $table->decimal('plain_price', 12, 2)->nullable();
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
        Schema::dropIfExists('m_display_product_component_prices');
    }
};

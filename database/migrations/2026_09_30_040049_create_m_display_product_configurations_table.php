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
        Schema::create('m_display_product_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('display_product_component_id');
            $table->foreign('display_product_component_id')->references('id')->on('m_display_product_components')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('cost_rangka', 12, 2);
            $table->decimal('cost_finishing', 12, 2);
            $table->decimal('total_cost', 12, 2);
            $table->decimal('general_price', 12, 2);
            $table->decimal('division_price', 12, 2);
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
        Schema::dropIfExists('m_display_product_configurations');
    }
};

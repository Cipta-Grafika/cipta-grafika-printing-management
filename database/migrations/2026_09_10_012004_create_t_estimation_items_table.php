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
        Schema::create('t_estimation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimation_id');
            $table->foreign('estimation_id')->references('id')->on('t_estimations')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('m_categories')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('material_id');
            $table->foreign('material_id')->references('id')->on('m_materials')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('material_size_id');
            $table->foreign('material_size_id')->references('id')->on('m_material_sizes')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('length', 10, 2);
            $table->decimal('width', 10, 2);
            $table->integer('qty');
            $table->integer('objects_per_row');
            $table->integer('total_rows');
            $table->decimal('production_length', 10, 2);
            $table->decimal('production_area', 12, 4);
            $table->decimal('billing_area', 12, 4);
            $table->decimal('material_price', 14, 2);
            $table->decimal('hpp_per_m2', 14, 2);
            $table->decimal('subtotal', 14, 2);
            $table->decimal('total_hpp', 14, 2);
            $table->decimal('profit', 14, 2);
            $table->decimal('margin', 8, 2);
            $table->decimal('markup', 8, 2);
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
        Schema::dropIfExists('t_estimation_items');
    }
};

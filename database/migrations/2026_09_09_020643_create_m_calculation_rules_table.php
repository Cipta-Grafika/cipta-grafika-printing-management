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
        Schema::create('m_calculation_rules', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->nullable();
            $table->string('name', 150);
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('m_categories')->onDelete('cascade')->onUpdate('cascade');
            $table->text('description')->nullable();
            $table->decimal('minimum_charge', 12, 2)->nullable();
            $table->enum('discount_type', ['percentage', 'nominal',])->nullable();
            $table->decimal('discount_value', 12, 2)->nullable();
            $table->string('status');
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
        Schema::dropIfExists('m_calculation_rules');
    }
};

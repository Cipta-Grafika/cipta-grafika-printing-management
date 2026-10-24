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
        Schema::create('m_offset_plano_materials', function (Blueprint $table) {
            $table->id();
            $table->string('material_code', 50)->nullable();
            $table->string('material_name', 150);
            $table->decimal('width', 10, 2)->default(65.00);
            $table->decimal('length', 10, 2)->default(90.00);
            $table->integer('gramature')->nullable();
            $table->string('paper_type', 100)->nullable();
            $table->decimal('price_per_plano', 15, 2);
            $table->string('status', 20)->default('active');
            $table->string('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_offset_plano_materials');
    }
};


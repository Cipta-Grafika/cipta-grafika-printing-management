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
        Schema::create('m_offset_plates', function (Blueprint $table) {
            $table->id();
            $table->string('plate_code', 50)->nullable();
            $table->string('plate_name', 150);
            $table->string('machine_name', 100)->nullable();
            $table->decimal('price_per_plate', 15, 2);
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
        Schema::dropIfExists('m_offset_plates');
    }
};


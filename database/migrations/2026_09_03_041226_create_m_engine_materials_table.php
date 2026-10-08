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
        Schema::create('m_engine_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('engine_id');
            $table->foreign('engine_id')->references('id')->on('m_engines')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('material_id');
            $table->foreign('material_id')->references('id')->on('m_materials')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('m_engine_materials');
    }
};

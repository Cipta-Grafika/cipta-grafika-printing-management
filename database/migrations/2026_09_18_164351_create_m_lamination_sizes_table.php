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
        Schema::create('m_lamination_sizes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lamination_id');
            $table->foreign('lamination_id')->references('id')->on('m_laminations')->onDelete('cascade')->onUpdate('cascade');
            $table->decimal('width', 10, 2);
            $table->decimal('length', 10, 2)->nullable();
            $table->string('unit');
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
        Schema::dropIfExists('m_lamination_sizes');
    }
};

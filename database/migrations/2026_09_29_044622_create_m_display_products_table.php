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
        Schema::create('m_display_products', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
            $table->decimal('length', 12, 2);
            $table->decimal('width', 12, 2);
            $table->decimal('frame_cost', 15, 2);
            $table->decimal('finishing_cost', 15, 2);
            $table->decimal('total_cost', 15, 2);
            $table->string('status');
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
        Schema::dropIfExists('m_display_products');
    }
};

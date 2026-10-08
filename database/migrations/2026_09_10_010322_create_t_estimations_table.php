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
        Schema::create('t_estimations', function (Blueprint $table) {
            $table->id();
            $table->string('estimation_code');
            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id')->references('id')->on('m_locations')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('engine_id');
            $table->foreign('engine_id')->references('id')->on('m_engines')->onDelete('cascade')->onUpdate('cascade');
            $table->string('price_type');
            $table->string('discount_type');
            $table->decimal('discount_value', 12, 2);
            $table->decimal('subtotal', 14, 2);
            $table->decimal('discount_amount', 14, 2);
            $table->decimal('grand_total', 14, 2);
            $table->decimal('total_hpp', 14, 2);
            $table->decimal('total_profit', 14, 2);
            $table->decimal('margin', 8, 2);
            $table->decimal('markup', 8, 2);
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
        Schema::dropIfExists('t_estimations');
    }
};

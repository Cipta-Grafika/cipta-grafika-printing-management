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
        Schema::create('m_production_cost_categories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('production_cost_id');
            $table->foreign('production_cost_id')
                ->references('id')
                ->on('m_production_costs')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')
                ->references('id')
                ->on('m_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade');

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
        Schema::dropIfExists('m_production_cost_categories');
    }
};

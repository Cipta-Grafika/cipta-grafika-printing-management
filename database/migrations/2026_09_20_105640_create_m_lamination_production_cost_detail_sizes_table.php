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
        Schema::create('m_lamination_production_cost_detail_sizes', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('lamination_production_cost_detail_id');
            $table->foreign('lamination_production_cost_detail_id')
                ->references('id')
                ->on('m_lamination_production_cost_details')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->unsignedBigInteger('lamination_size_id');
            $table->foreign('lamination_size_id')
                ->references('id')
                ->on('m_lamination_sizes')
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
        Schema::dropIfExists('m_lamination_production_cost_detail_sizes');
    }
};

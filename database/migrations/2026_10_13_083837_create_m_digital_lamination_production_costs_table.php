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
        Schema::create('m_digital_lamination_production_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('engine_id');
            $table->foreign('engine_id', 'dig_lam_pc_engine_fk')
                ->references('id')->on('m_engines')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('location_id');
            $table->foreign('location_id', 'dig_lam_pc_location_fk')
                ->references('id')->on('m_locations')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->foreign('vendor_id', 'dig_lam_pc_vendor_fk')
                ->references('id')->on('m_vendors')->onDelete('cascade')->onUpdate('cascade');
            $table->string('created_by');
            $table->timestamp('created_at');
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index(
                ['engine_id', 'location_id', 'vendor_id'],
                'dig_lam_pc_configuration_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_digital_lamination_production_costs');
    }
};

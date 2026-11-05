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
        if (!Schema::hasTable('m_lanyard_pricings')) {
            Schema::create('m_lanyard_pricings', function (Blueprint $table) {
                $table->id();
                $table->string('width_type', 20)->comment('2cm atau 2.5cm');
                $table->string('package_type', 20)->comment('tali atau set');
                $table->string('tier_label', 50)->comment('Label rentang qty, misal 1-4 pcs, >100 pcs');
                $table->integer('min_qty')->comment('Batas bawah kuantiti');
                $table->integer('max_qty')->nullable()->comment('Batas atas kuantiti (null jika tak hingga)');
                $table->decimal('price', 12, 2)->comment('Harga satuan rupiah');
                $table->string('status', 20)->default('active');
                $table->string('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->index(['width_type', 'package_type', 'status']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_lanyard_pricings');
    }
};


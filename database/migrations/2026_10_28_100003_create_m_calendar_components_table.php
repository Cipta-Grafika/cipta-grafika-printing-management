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
        Schema::create('m_calendar_components', function (Blueprint $table) {
            $table->id();
            $table->string('component_code', 50)->nullable();
            $table->string('component_type', 50)->comment('jepit_kaleng, spiral, hanger, penopang_beli_jadi, penopang_plano, finishing_rakit, cover_penopang, insheet');
            $table->string('name', 150);
            $table->string('unit', 50)->comment('pcs, cm, plano, lembar, rp_per_cm2');
            $table->decimal('price', 15, 2);
            $table->decimal('min_size', 8, 2)->nullable()->comment('Batas bawah ukuran cm jika ada');
            $table->decimal('max_size', 8, 2)->nullable()->comment('Batas atas ukuran cm jika ada');
            $table->decimal('minimum_fee', 15, 2)->default(0)->comment('Biaya minimum jika ada');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('m_calendar_components');
    }
};


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
        Schema::create('m_calendar_pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name', 150)->default('Standard Calendar Markup');
            $table->integer('min_qty')->comment('Batas bawah qty order');
            $table->integer('max_qty')->nullable()->comment('Batas atas qty order (null jika tak hingga)');
            $table->decimal('markup_percent', 5, 2)->comment('Persentase markup profit (misal 50.00, 45.00)');
            $table->string('label', 50)->nullable()->comment('Label markup, misal 50%');
            $table->integer('rounding_value')->default(1000)->comment('Pembulatan harga rupiah');
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
        Schema::dropIfExists('m_calendar_pricing_rules');
    }
};


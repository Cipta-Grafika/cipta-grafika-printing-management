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
        Schema::create('m_offset_pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->string('rule_name', 150)->default('Pricing Rule Brosur Offset');
            $table->string('category_name', 100)->nullable()->default('Brosur');
            $table->integer('min_rim')->default(1);
            $table->integer('max_rim')->default(10);
            $table->decimal('markup_general', 8, 2)->default(100.00);
            $table->decimal('markup_division', 8, 2)->default(80.00);
            $table->decimal('rounding_value', 12, 2)->default(5000.00);
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
        Schema::dropIfExists('m_offset_pricing_rules');
    }
};


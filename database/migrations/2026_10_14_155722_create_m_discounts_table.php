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
        Schema::create('m_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('discount_percentage', 5, 2);
            $table->timestamp('starts_at');
            $table->timestamp('ends_at');
            $table->string('status');
            $table->string('created_by');
            $table->timestamp('created_at');
            $table->string('updated_by')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->index(['status', 'starts_at', 'ends_at'], 'discounts_active_period_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_discounts');
    }
};

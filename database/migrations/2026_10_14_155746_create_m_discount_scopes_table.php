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
        Schema::create('m_discount_scopes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('discount_id');
            $table->foreign('discount_id', 'discount_scopes_discount_fk')
                ->references('id')->on('m_discounts')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedBigInteger('engine_id');
            $table->foreign('engine_id', 'discount_scopes_engine_fk')
                ->references('id')->on('m_engines')->onDelete('restrict')->onUpdate('cascade');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id', 'discount_scopes_category_fk')
                ->references('id')->on('m_categories')->onDelete('restrict')->onUpdate('cascade');

            $table->unique(['discount_id', 'engine_id', 'category_id'], 'discount_scopes_unique_configuration');
            $table->index(['engine_id', 'category_id'], 'discount_scopes_engine_category_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_discount_scopes');
    }
};

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
        Schema::dropIfExists('t_estimation_items');

        Schema::create('t_estimation_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimation_id');
            $table->foreign('estimation_id')->references('id')->on('t_estimations')->cascadeOnDelete();

            $table->string('item_name', 255);
            $table->string('item_category', 100)->nullable();
            $table->text('description')->nullable();

            // Kuantitas & Satuan Cetakia
            $table->decimal('qty', 12, 2)->default(1);
            $table->string('unit_id', 100)->nullable();
            $table->string('unit_name', 50)->nullable();

            // Nilai Finansial Item
            $table->decimal('unit_price', 16, 2)->default(0);
            $table->decimal('subtotal', 16, 2)->default(0);
            $table->decimal('hpp_per_unit', 16, 2)->default(0);
            $table->decimal('total_hpp', 16, 2)->default(0);
            $table->decimal('profit', 16, 2)->default(0);
            $table->decimal('margin_percent', 8, 2)->nullable();
            $table->decimal('markup_percent', 8, 2)->nullable();

            // Snapshot Lengkap Parameter Teknis (Mesin, Bahan, Laminasi, Potong, Jilid, dll.)
            $table->jsonb('technical_details')->nullable();

            $table->string('created_by', 100)->nullable();
            $table->string('updated_by', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_estimation_items');
    }
};

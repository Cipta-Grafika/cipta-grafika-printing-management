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
        Schema::create('m_offset_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code', 50)->nullable()->unique();
            $table->string('product_name', 150);
            $table->string('category', 100)->default('Brosur');
            $table->decimal('final_width', 8, 2)->comment('Lebar ukuran jadi (cm)');
            $table->decimal('final_length', 8, 2)->comment('Panjang ukuran jadi (cm)');
            $table->decimal('print_sheet_width', 8, 2)->default(32.50)->comment('Lebar lembar cetak mesin (cm)');
            $table->decimal('print_sheet_length', 8, 2)->default(45.00)->comment('Panjang lembar cetak mesin (cm)');
            $table->integer('up_per_sheet')->default(1)->comment('Jumlah pcs per lembar cetak mesin (N-Up)');
            $table->integer('plano_cut_yield')->default(4)->comment('Jumlah lembar cetak dari 1 plano');
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
        Schema::dropIfExists('m_offset_products');
    }
};


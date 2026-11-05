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
        if (!Schema::hasTable('m_nota_pricings')) {
            Schema::create('m_nota_pricings', function (Blueprint $table) {
                $table->id();
                $table->string('name', 150)->comment('Nama nota, misal: Nota NCR 2 Ply (1 Tinta)');
                $table->string('paper_type', 30)->comment('ncr atau hvs');
                $table->integer('ply_count')->comment('1, 2, 3, 4');
                $table->string('ply_label', 50)->comment('1 Ply, 2 Ply, 3 Ply, 4 Ply');
                $table->integer('ink_count')->comment('1, 2, 3');
                $table->string('ink_label', 50)->comment('1 Tinta, 2 Tinta, 3 Tinta');
                $table->integer('sheets_per_book')->comment('50 untuk NCR, 100 untuk HVS');
                $table->decimal('price', 12, 2)->comment('Harga per 1 paket rim cetak rupiah');
                $table->text('description')->nullable()->comment('Keterangan spesifikasi nota');
                $table->string('status', 20)->default('active')->comment('active atau inactive');
                $table->string('created_by')->nullable();
                $table->timestamp('created_at')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->index(['paper_type', 'ply_count', 'ink_count', 'status'], 'idx_nota_search');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_nota_pricings');
    }
};


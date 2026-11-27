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
        // Pastikan tabel lama (jika ada) diturunkan secara aman
        Schema::dropIfExists('t_estimation_items');
        Schema::dropIfExists('t_estimations');

        Schema::create('t_estimations', function (Blueprint $table) {
            $table->id();
            $table->string('estimation_code', 50)->unique();
            $table->string('cetakia_quotation_code', 50)->nullable()->index();
            $table->string('cetakia_quotation_id', 100)->nullable()->index();
            $table->string('estimation_type', 30)->index(); // a3_plus, large_format, offset, calendar, merchandise
            
            // Lokasi Cabang
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('m_locations')->nullOnDelete();

            // Data Pelanggan Cetakia
            $table->string('customer_id', 100)->index();
            $table->string('customer_name', 255);
            $table->string('customer_phone', 50)->nullable();

            // Data Sales / PIC Cetakia
            $table->string('sales_user_id', 100)->index();
            $table->string('sales_name', 255);

            // Data Komersial & Dokumen Penawaran
            $table->string('price_type', 30)->default('general'); // general, division, plain
            $table->date('quotation_date');
            $table->date('quotation_expired')->nullable();
            $table->integer('terms')->default(0); // durasi tempo pembayaran dlm hari
            $table->text('bill_to')->nullable();
            $table->text('ship_to')->nullable();
            $table->text('notes')->nullable();

            // Nilai Finansial, Diskon & Pajak
            $table->decimal('subtotal', 16, 2)->default(0);
            $table->boolean('discount_enabled')->default(false);
            $table->string('discount_type', 20)->nullable(); // percent, nominal
            $table->decimal('discount_value', 12, 2)->default(0);
            $table->decimal('discount_amount', 16, 2)->default(0);
            $table->boolean('tax_enabled')->default(false);
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('tax_amount', 16, 2)->default(0);
            $table->decimal('grand_total', 16, 2)->default(0);

            // Analisis Biaya Pokok Produksi & Profitabilitas
            $table->decimal('total_hpp', 16, 2)->default(0);
            $table->decimal('total_profit', 16, 2)->default(0);
            $table->decimal('margin_percent', 8, 2)->nullable();
            $table->decimal('markup_percent', 8, 2)->nullable();

            // Status & Audit Log
            $table->string('status', 30)->default('sent_to_cetakia'); // draft, sent_to_cetakia, cancelled
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
        Schema::dropIfExists('t_estimations');
    }
};

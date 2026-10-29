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
        Schema::create('m_calendar_products', function (Blueprint $table) {
            $table->id();
            $table->string('calendar_type', 30)->comment('dinding / meja');
            $table->string('product_name', 150);
            $table->string('size_label', 50)->comment('e.g. 30 x 42 cm, 24 x 17 cm');
            $table->string('orientation', 20)->default('landscape')->comment('portrait / landscape / all');
            $table->decimal('final_width', 8, 2)->comment('Lebar ukuran jadi (cm)');
            $table->decimal('final_height', 8, 2)->comment('Tinggi ukuran jadi (cm)');
            $table->boolean('digital_available')->default(true);
            $table->integer('digital_up')->default(1)->comment('Pcs per lembar A3+');
            $table->string('offset_machine', 50)->default('SM52')->comment('SM52, Oliver66, SM72');
            $table->decimal('offset_press_width', 8, 2)->default(45.00);
            $table->decimal('offset_press_height', 8, 2)->default(32.50);
            $table->integer('offset_up')->default(1)->comment('Pcs per lembar mesin offset');
            $table->integer('offset_up_bw')->nullable()->comment('Pcs per lembar khusus bahan BW jika beda');
            $table->string('recommended_plano', 50)->default('65x90');
            $table->integer('default_leaf_count')->default(7)->comment('Default jumlah lembar kalender');
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
        Schema::dropIfExists('m_calendar_products');
    }
};


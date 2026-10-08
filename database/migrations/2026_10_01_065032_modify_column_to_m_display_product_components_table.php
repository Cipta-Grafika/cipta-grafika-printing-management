<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('m_display_product_components', function (Blueprint $table) {
            DB::statement('
            ALTER TABLE m_display_product_components
            DROP CONSTRAINT m_display_product_components_component_type_check
        ');

            DB::statement("
            ALTER TABLE m_display_product_components
            ADD CONSTRAINT m_display_product_components_component_type_check
            CHECK (component_type IN ('material', 'lamination'))
        ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('
            ALTER TABLE m_display_product_components
            DROP CONSTRAINT m_display_product_components_component_type_check
        ');

        DB::statement("
            ALTER TABLE m_display_product_components
            ADD CONSTRAINT m_display_product_components_component_type_check
            CHECK (component_type IN ('frame', 'material', 'lamination'))
        ");
    }
};

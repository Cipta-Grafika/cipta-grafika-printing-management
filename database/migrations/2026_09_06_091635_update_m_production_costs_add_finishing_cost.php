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
        //

        Schema::table('m_production_costs', function (Blueprint $table) {
            $table->renameColumn('cost', 'production_cost');

            $table->decimal('finishing_cost', 12, 2)
                ->after('production_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

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
        Schema::table('m_production_cost_details', function (Blueprint $table) {
            $table->unsignedBigInteger('general_pricing_rule_id')
                ->nullable()
                ->after('material_id');

            $table->unsignedBigInteger('division_pricing_rule_id')
                ->nullable()
                ->after('general_pricing_rule_id');

            $table->unsignedBigInteger('plain_pricing_rule_id')
                ->nullable()
                ->after('division_pricing_rule_id');

            $table->foreign('general_pricing_rule_id')
                ->references('id')
                ->on('m_pricing_rules')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('division_pricing_rule_id')
                ->references('id')
                ->on('m_pricing_rules')
                ->onDelete('restrict')
                ->onUpdate('cascade');

            $table->foreign('plain_pricing_rule_id')
                ->references('id')
                ->on('m_pricing_rules')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_production_cost_details', function (Blueprint $table) {
            $table->dropForeign(['general_pricing_rule_id']);
            $table->dropForeign(['division_pricing_rule_id']);
            $table->dropForeign(['plain_pricing_rule_id']);

            $table->dropColumn([
                'general_pricing_rule_id',
                'division_pricing_rule_id',
                'plain_pricing_rule_id',
            ]);
        });
    }
};

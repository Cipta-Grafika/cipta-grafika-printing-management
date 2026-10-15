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
        Schema::table('m_discounts', function (Blueprint $table) {
            $table->string('code', 30)->nullable()->after('id');
        });

        DB::table('m_discounts')->whereNull('code')->orderBy('id')->get(['id'])
            ->each(fn ($discount) => DB::table('m_discounts')
                ->where('id', $discount->id)
                ->update(['code' => 'DSC-' . $discount->id]));

        Schema::table('m_discounts', function (Blueprint $table) {
            $table->string('code', 30)->nullable(false)->change();
            $table->unique('code', 'discounts_code_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_discounts', function (Blueprint $table) {
            $table->dropUnique('discounts_code_unique');
            $table->dropColumn('code');
        });
    }
};

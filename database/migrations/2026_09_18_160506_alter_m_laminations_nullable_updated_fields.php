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
        Schema::table('m_laminations', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->after('id')
                ->constrained('m_categories')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('lamination_code')
                ->nullable()
                ->after('category_id');

            $table->renameColumn('name', 'lamination_name');

            $table->string('updated_by')
                ->nullable()
                ->change();

            $table->timestamp('updated_at')
                ->nullable()
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_laminations', function (Blueprint $table) {
            $table->dropForeign(['category_id']);

            $table->dropColumn([
                'category_id',
                'lamination_code',
            ]);

            $table->renameColumn('lamination_name', 'name');

            $table->string('updated_by')
                ->nullable(false)
                ->change();

            $table->timestamp('updated_at')
                ->nullable(false)
                ->change();
        });
    }
};

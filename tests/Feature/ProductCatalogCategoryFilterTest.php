<?php

namespace Tests\Feature;

use App\Http\Controllers\user\CatalogCT;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductCatalogCategoryFilterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.url' => null,
        ]);
        DB::purge('sqlite');

        Schema::create('m_engines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status');
        });
        Schema::create('m_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('m_display_products', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
            $table->decimal('length', 8, 2);
            $table->decimal('width', 8, 2);
            $table->unsignedBigInteger('category_id');
            $table->unsignedInteger('package_qty')->nullable();
            $table->string('package_unit')->nullable();
            $table->string('status');
        });
        Schema::create('m_display_product_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('display_product_id');
            $table->unsignedBigInteger('engine_id')->nullable();
        });
        Schema::create('m_display_product_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('display_product_id');
            $table->boolean('is_primary');
        });
        Schema::create('m_sample_galleries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('engine_id');
            $table->unsignedBigInteger('category_id');
            $table->boolean('is_primary');
            $table->integer('sort_order');
            $table->string('title_alias')->nullable();
        });

        DB::table('m_categories')->insert(['id' => 1, 'name' => 'Merchandise']);
        DB::table('m_display_products')->insert([
            ['id' => 1, 'display_name' => 'Cetak Kartu PVC (ID Card & KTA)', 'length' => 8.6, 'width' => 5.4, 'category_id' => 1, 'package_qty' => 1, 'package_unit' => 'Pcs', 'status' => 'Active'],
            ['id' => 2, 'display_name' => 'Tali Lanyard & ID Card Custom', 'length' => 90, 'width' => 2, 'category_id' => 1, 'package_qty' => 1, 'package_unit' => 'Pcs', 'status' => 'Active'],
            ['id' => 3, 'display_name' => 'Papan Nama Dada (Name Tag Custom)', 'length' => 8, 'width' => 2, 'category_id' => 1, 'package_qty' => 1, 'package_unit' => 'Pcs', 'status' => 'Active'],
            ['id' => 4, 'display_name' => 'Buku Nota (NCR & HVS Custom)', 'length' => 21.5, 'width' => 16.5, 'category_id' => 1, 'package_qty' => 1, 'package_unit' => 'Paket Rim', 'status' => 'Active'],
            ['id' => 5, 'display_name' => 'Mug Sublimasi Keramik Custom', 'length' => 11, 'width' => 8.5, 'category_id' => 1, 'package_qty' => 1, 'package_unit' => 'Pcs', 'status' => 'Active'],
        ]);
    }

    public function test_filter_options_are_indonesian_and_merchandise_remains_available(): void
    {
        $options = (new CatalogCT())->filters()->getData(true)['data']['engines'];
        $optionsById = collect($options)->keyBy('id');

        $this->assertSame('Identitas Korporat', $optionsById['corporate_id']['name']);
        $this->assertSame('Formulir Administrasi', $optionsById['form_administrasi']['name']);
        $this->assertSame('Souvenir & Merchandise', $optionsById['souvenir']['name']);
    }

    public function test_only_the_four_requested_products_are_split_from_merchandise(): void
    {
        $products = (new CatalogCT())->productsList()->getData(true)['data'];
        $typesByTitle = collect($products)->pluck('type', 'title')->all();

        $this->assertSame('corporate_id', $typesByTitle['Cetak Kartu PVC (ID Card & KTA)']);
        $this->assertSame('corporate_id', $typesByTitle['Tali Lanyard & ID Card Custom']);
        $this->assertSame('corporate_id', $typesByTitle['Papan Nama Dada (Name Tag Custom)']);
        $this->assertSame('form_administrasi', $typesByTitle['Buku Nota (NCR & HVS Custom)']);
        $this->assertSame('souvenir', $typesByTitle['Mug Sublimasi Keramik Custom']);
    }
}

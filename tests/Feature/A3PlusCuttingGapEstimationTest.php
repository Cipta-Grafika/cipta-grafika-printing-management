<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\EstimationCT;
use App\Services\CetakiaApiService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class A3PlusCuttingGapEstimationTest extends TestCase
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
        });

        Schema::create('m_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('m_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('m_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('material_name');
        });

        Schema::create('m_digital_production_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
        });

        Schema::create('m_digital_production_cost_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('digital_production_cost_id');
            $table->unsignedBigInteger('material_id');
            $table->decimal('paper_cost', 12, 2)->default(800);
            $table->decimal('click_cost', 12, 2)->default(1500);
        });

        Schema::create('m_digital_production_cost_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('digital_production_cost_detail_id');
            $table->string('price_type');
            $table->string('print_type');
            $table->string('quantity_tier');
            $table->decimal('selling_price', 12, 2);
        });

        Schema::create('m_cuttings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('Active');
        });

        Schema::create('m_digital_potong_production_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('engine_id');
        });

        Schema::create('m_digital_potong_production_cost_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('digital_potong_production_cost_id');
            $table->unsignedBigInteger('category_id');
        });

        Schema::create('m_digital_potong_production_cost_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('digital_potong_production_cost_category_id');
            $table->string('size_class');
            $table->string('price_type');
            $table->decimal('min_price', 12, 2);
        });

        Schema::create('m_digital_cutting_production_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
        });

        Schema::create('m_digital_cutting_production_cost_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('digital_cutting_production_cost_id');
            $table->unsignedBigInteger('cutting_id');
            $table->unsignedBigInteger('category_id')->nullable();
        });

        Schema::create('m_digital_cutting_production_cost_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('digital_cutting_production_cost_detail_id');
            $table->string('price_type');
            $table->string('quantity_tier');
            $table->decimal('price', 12, 2);
        });

        DB::table('m_engines')->insert(['id' => 70, 'name' => 'Digital Print A3+']);
        DB::table('m_locations')->insert(['id' => 1, 'name' => 'Purwakarta']);
        DB::table('m_categories')->insert(['id' => 1, 'name' => 'Sticker']);
        DB::table('m_materials')->insert(['id' => 1, 'category_id' => 1, 'material_name' => 'Vinyl Glossy']);
        DB::table('m_cuttings')->insert([
            ['id' => 1, 'name' => 'Die Cut', 'status' => 'Active'],
            ['id' => 4, 'name' => 'Perforasi', 'status' => 'Active'],
            ['id' => 7, 'name' => 'Kiss Cut', 'status' => 'Active'],
            ['id' => 9, 'name' => 'Potong', 'status' => 'Active'],
        ]);
    }

    private function basePayload(float $length, float $width, int $qty, string $cuttingType): array
    {
        return [
            'location_id' => 1,
            'price_type' => 'general',
            'production_mode' => 'layout',
            'material_id' => 1,
            'print_color' => 'fc',
            'print_side' => '1',
            'length' => $length,
            'width' => $width,
            'qty' => $qty,
            'lamination_type' => 'none',
            'cutting_type' => $cuttingType,
            'discount_type' => 'none',
            'discount_value' => 0,
            'estimation_time' => 1,
        ];
    }

    public function test_store_a3plus_applies_cutting_gaps_to_imposition_and_sheet_count(): void
    {
        $controller = new EstimationCT(Mockery::mock(CetakiaApiService::class));

        $run = function (float $l, float $w, int $qty, string $cutting) use ($controller): array {
            $request = Request::create('/admin/estimations/a3-plus', 'POST', $this->basePayload($l, $w, $qty, $cutting));
            $response = $controller->storeA3plus($request);
            $this->assertSame(200, $response->getStatusCode());

            $json = $response->getData(true);
            $this->assertTrue($json['success'] ?? false);

            return $json['data'];
        };

        // Baseline 9 x 5.5 cm tanpa cutting: 25 pcs/lbr, 4 lembar untuk 100 pcs
        $baseline = $run(9.0, 5.5, 100, 'none');
        $this->assertSame(25, $baseline['up_count']);
        $this->assertSame(4, $baseline['total_sheets']);
        $this->assertSame(0, $baseline['cutting_gap_mm']);

        // 5 x 5 cm, 100 pcs: none = 54 pcs (2 lbr), Die Cut (5mm) = 40 pcs (3 lbr)
        $noCutFive = $run(5.0, 5.0, 100, 'none');
        $this->assertSame(54, $noCutFive['up_count']);
        $this->assertSame(2, $noCutFive['total_sheets']);

        $dieCutData = $run(5.0, 5.0, 100, '1');
        $this->assertSame(40, $dieCutData['up_count']);
        $this->assertSame(3, $dieCutData['total_sheets']);
        $this->assertSame(5, $dieCutData['cutting_gap_mm']);

        // 6.2 x 6.2 cm, 100 pcs:
        // none => 7*6.2=43.4<=47.5 (7), 5*6.2=31.0<=31.5 (5) => 35 pcs (3 lembar)
        // Kiss Cut (2mm) => 5*6.2+4*0.2=31.8 > 31.5, turun menjadi 7*4 = 28 pcs (4 lembar)
        $noCutEdge = $run(6.2, 6.2, 100, 'none');
        $this->assertSame(35, $noCutEdge['up_count']);
        $this->assertSame(3, $noCutEdge['total_sheets']);

        $kissCutData = $run(6.2, 6.2, 100, '7');
        $this->assertSame(28, $kissCutData['up_count']);
        $this->assertSame(4, $kissCutData['total_sheets']);
        $this->assertSame(2, $kissCutData['cutting_gap_mm']);

        // Perforasi & Potong Lurus => tanpa jarak (tetap 35 pcs / 3 lembar untuk 6.2 x 6.2)
        $perforasiData = $run(6.2, 6.2, 100, '4');
        $this->assertSame(35, $perforasiData['up_count']);
        $this->assertSame(3, $perforasiData['total_sheets']);
        $this->assertSame(0, $perforasiData['cutting_gap_mm']);

        $potongData = $run(6.2, 6.2, 100, 'potong');
        $this->assertSame(35, $potongData['up_count']);
        $this->assertSame(3, $potongData['total_sheets']);
        $this->assertSame(0, $potongData['cutting_gap_mm']);
    }
}

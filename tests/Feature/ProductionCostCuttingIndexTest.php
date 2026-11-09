<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\ProductionCostFinishingCuttingCT;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductionCostCuttingIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.url' => null,
            'database.connections.sqlite.foreign_key_constraints' => true,
        ]);
        DB::purge('sqlite');

        foreach (['m_engines', 'm_locations', 'm_vendors', 'm_categories', 'm_cuttings'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('status')->default('Active');
            });
        }
        foreach ([
            '2026_10_16_075641_create_m_digital_cutting_production_costs_table.php',
            '2026_10_16_075707_create_m_digital_cutting_production_cost_details_table.php',
            '2026_10_16_075720_create_m_digital_cutting_production_cost_prices_table.php',
            '2026_10_17_013959_create_m_digital_potong_production_costs_table.php',
            '2026_10_17_014015_create_m_digital_potong_production_cost_categories_table.php',
            '2026_10_17_014037_create_m_digital_potong_production_cost_prices_table.php',
        ] as $file) {
            $migration = require database_path('migrations\\' . $file);
            $migration->up();
        }

        $user = new User();
        $user->name = 'Cutting Index Test';
        Auth::setUser($user);
        \Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag());

        DB::table('m_engines')->insert([['id' => 1, 'name' => 'Digital Print A3+']]);
        DB::table('m_locations')->insert([
            ['id' => 1, 'name' => 'Purwakarta'],
            ['id' => 2, 'name' => 'Graha'],
        ]);
        DB::table('m_vendors')->insert([['id' => 1, 'name' => 'Vendor A']]);
        DB::table('m_categories')->insert([['id' => 1, 'name' => 'Category A']]);
        DB::table('m_cuttings')->insert([['id' => 1, 'name' => 'Kiss Cut']]);
    }

    private function createCuttingCost(int $locationId): int
    {
        $headerId = DB::table('m_digital_cutting_production_costs')->insertGetId([
            'engine_id' => 1,
            'location_id' => $locationId,
            'vendor_id' => null,
            'created_by' => 'Test',
            'created_at' => now(),
        ]);
        $detailId = DB::table('m_digital_cutting_production_cost_details')->insertGetId([
            'digital_cutting_production_cost_id' => $headerId,
            'cutting_id' => 1,
            'category_id' => 1,
            'created_by' => 'Test',
            'created_at' => now(),
        ]);
        DB::table('m_digital_cutting_production_cost_prices')->insert([
            'digital_cutting_production_cost_detail_id' => $detailId,
            'price_type' => 'production',
            'side_type' => 'single',
            'quantity_tier' => '1',
            'minimum_quantity' => 1,
            'maximum_quantity' => null,
            'price' => 1000,
            'created_by' => 'Test',
            'created_at' => now(),
        ]);

        return $headerId;
    }

    private function createPotongCost(int $locationId): int
    {
        $headerId = DB::table('m_digital_potong_production_costs')->insertGetId([
            'engine_id' => 1,
            'location_id' => $locationId,
            'vendor_id' => null,
            'created_by' => 'Test',
            'created_at' => now(),
        ]);
        $categoryId = DB::table('m_digital_potong_production_cost_categories')->insertGetId([
            'digital_potong_production_cost_id' => $headerId,
            'category_id' => 1,
            'created_by' => 'Test',
            'created_at' => now(),
        ]);
        DB::table('m_digital_potong_production_cost_prices')->insert([
            'digital_potong_production_cost_category_id' => $categoryId,
            'size_class' => 'A3',
            'price_type' => 'production',
            'min_price' => 1000,
            'created_by' => 'Test',
            'created_at' => now(),
        ]);

        return $headerId;
    }

    private function bulkDelete(array $payload)
    {
        return app(ProductionCostFinishingCuttingCT::class)->bulkDestroy(
            Request::create('/admin/production-cost-finishing-cuttings/bulk-delete', 'POST', $payload)
        );
    }

    public function test_index_renders_category_style_filters_selection_and_bulk_route(): void
    {
        $html = view('admin.production-cost-finishing-cutting.index', [
            'filterLocations' => DB::table('m_locations')->orderBy('name')->get(['id', 'name']),
        ])->render();

        $this->assertStringContainsString('cat-modern-table', $html);
        $this->assertStringContainsString('id="checkAllProductionCostCuttings"', $html);
        $this->assertStringContainsString('id="productionCostCuttingSearch"', $html);
        $this->assertStringContainsString('id="productionCostCuttingTypeFilter"', $html);
        $this->assertStringContainsString('<option value="2">Graha</option>', $html);
        $this->assertStringContainsString('id="productionCostCuttingPageLength"', $html);
        $this->assertStringContainsString('topStart: null', $html);
        $this->assertStringContainsString(route('admin_bulk_destroy_pc_finishing_cuttings'), $html);
        $this->assertStringContainsString('btn-delete-production-cost-cutting', $html);
        $this->assertStringContainsString('id="modalDeleteProductionCostCutting"', $html);
    }

    public function test_data_filters_by_location_for_cutting_and_potong(): void
    {
        $this->createCuttingCost(1);
        $grahaCutting = $this->createCuttingCost(2);
        $this->createPotongCost(1);
        $grahaPotong = $this->createPotongCost(2);
        $controller = app(ProductionCostFinishingCuttingCT::class);

        $cutting = $controller->data(Request::create('/data', 'GET', [
            'draw' => 1, 'start' => 0, 'length' => 10, 'location_id' => 2,
        ]))->getData(true);
        $this->assertSame(2, $cutting['recordsTotal']);
        $this->assertSame(1, $cutting['recordsFiltered']);
        $this->assertSame($grahaCutting, $cutting['data'][0]['digital_cutting_production_cost_id']);
        $this->assertSame(1, (int) $cutting['data'][0]['category_count']);
        $this->assertSame(1, (int) $cutting['data'][0]['cutting_count']);

        $potong = $controller->data(Request::create('/data', 'GET', [
            'draw' => 1, 'start' => 0, 'length' => 10, 'cutting_filter' => 'potong', 'location_id' => 2,
        ]))->getData(true);
        $this->assertSame(1, $potong['recordsFiltered']);
        $this->assertSame($grahaPotong, $potong['data'][0]['digital_potong_production_cost_id']);

        $sorted = $controller->data(Request::create('/data', 'GET', [
            'draw' => 1, 'start' => 0, 'length' => 10, 'order' => [['column' => 3, 'dir' => 'asc']],
        ]))->getData(true);
        $this->assertSame(['Graha', 'Purwakarta'], array_column($sorted['data'], 'location_names'));
    }

    public function test_bulk_delete_removes_only_selected_cutting_and_potong_rows(): void
    {
        $deletedCutting = $this->createCuttingCost(1);
        $keptCutting = $this->createCuttingCost(2);
        $deletedPotong = $this->createPotongCost(1);
        $keptPotong = $this->createPotongCost(2);

        $response = $this->bulkDelete(['cutting' => [$deletedCutting], 'potong' => [$deletedPotong]]);

        $this->assertTrue($response->getData(true)['success']);
        $this->assertSame([$keptCutting], DB::table('m_digital_cutting_production_costs')->pluck('id')->all());
        $this->assertSame([$keptPotong], DB::table('m_digital_potong_production_costs')->pluck('id')->all());
        $this->assertSame(1, DB::table('m_digital_cutting_production_cost_details')->count());
        $this->assertSame(1, DB::table('m_digital_cutting_production_cost_prices')->count());
        $this->assertSame(1, DB::table('m_digital_potong_production_cost_categories')->count());
        $this->assertSame(1, DB::table('m_digital_potong_production_cost_prices')->count());
        $this->assertSame(1, DB::table('m_categories')->count());
        $this->assertSame(1, DB::table('m_cuttings')->count());
    }

    public function test_bulk_delete_is_atomic_when_any_selected_row_is_missing(): void
    {
        $existing = $this->createCuttingCost(1);

        $response = $this->bulkDelete(['cutting' => [$existing, 9999]]);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
        $this->assertSame([$existing], DB::table('m_digital_cutting_production_costs')->pluck('id')->all());
        $this->assertSame(1, DB::table('m_digital_cutting_production_cost_prices')->count());
    }
}

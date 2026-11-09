<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\ProductionCostFinishingCT;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class ProductionCostFinishingIndexTest extends TestCase
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

        Schema::create('m_engines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('Active');
        });
        Schema::create('m_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status')->default('Active');
        });
        Schema::create('m_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('m_production_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('engine_id');
        });
        Schema::create('m_production_cost_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_cost_id')->constrained('m_production_costs')->cascadeOnDelete();
            $table->unsignedBigInteger('location_id');
        });
        Schema::create('m_production_cost_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_cost_id')->constrained('m_production_costs')->cascadeOnDelete();
            $table->unsignedBigInteger('material_id');
        });
        Schema::create('m_production_cost_detail_widths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_cost_detail_id')->constrained('m_production_cost_details')->cascadeOnDelete();
            $table->unsignedBigInteger('material_size_id');
        });
        Schema::create('m_lamination_production_cost_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('production_cost_id');
        });
        Schema::create('m_digital_production_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('engine_id');
            $table->unsignedBigInteger('location_id');
            $table->unsignedBigInteger('vendor_id')->nullable();
        });
        Schema::create('m_digital_production_cost_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('digital_production_cost_id')->constrained('m_digital_production_costs')->cascadeOnDelete();
            $table->unsignedBigInteger('material_id');
        });

        DB::table('m_engines')->insert([
            ['id' => 1, 'name' => 'Large Format'],
            ['id' => 2, 'name' => 'Digital Print A3+'],
        ]);
        DB::table('m_locations')->insert([
            ['id' => 1, 'name' => 'Bandung'],
            ['id' => 2, 'name' => 'Jakarta'],
        ]);
        DB::table('m_vendors')->insert(['id' => 1, 'name' => 'Vendor A']);
        DB::table('m_production_costs')->insert([
            ['id' => 11, 'engine_id' => 1],
            ['id' => 12, 'engine_id' => 1],
        ]);
        DB::table('m_production_cost_locations')->insert([
            ['id' => 1, 'production_cost_id' => 11, 'location_id' => 1],
            ['id' => 2, 'production_cost_id' => 12, 'location_id' => 2],
        ]);
        DB::table('m_production_cost_details')->insert([
            ['id' => 101, 'production_cost_id' => 11, 'material_id' => 1],
            ['id' => 102, 'production_cost_id' => 12, 'material_id' => 2],
        ]);
        DB::table('m_production_cost_detail_widths')->insert([
            ['production_cost_detail_id' => 101, 'material_size_id' => 1],
            ['production_cost_detail_id' => 102, 'material_size_id' => 2],
        ]);
        DB::table('m_digital_production_costs')->insert([
            ['id' => 21, 'engine_id' => 2, 'location_id' => 1, 'vendor_id' => 1],
            ['id' => 22, 'engine_id' => 2, 'location_id' => 2, 'vendor_id' => null],
        ]);
        DB::table('m_digital_production_cost_details')->insert([
            ['digital_production_cost_id' => 21, 'material_id' => 1],
            ['digital_production_cost_id' => 22, 'material_id' => 2],
        ]);

        $user = new User();
        $user->name = 'Test Admin';
        $user->email = 'admin@example.test';
        Auth::setUser($user);
        View::share('errors', new ViewErrorBag());
    }

    public function test_index_renders_custom_table_controls_and_preserves_existing_actions(): void
    {
        $html = (new ProductionCostFinishingCT())->index()->render();

        foreach ([
            'productionCostTable',
            'productionCostSearch',
            'productionCostLocationFilter',
            'productionCostEngineFilter',
            'productionCostPageLength',
            'checkAllProductionCosts',
            'modalBulkDeleteProductionCosts',
            'btnImportFinishingService',
            'btnDownloadTemplateFinishingService',
        ] as $id) {
            $this->assertStringContainsString('id="' . $id . '"', $html);
        }

        foreach ([
            'cat-hero-compact',
            'cat-card-container',
            'cat-card-header',
            'cat-filter-card',
            'cat-custom-checkbox',
            'cat-modern-table',
            'cat-bulk-toolbar',
        ] as $class) {
            $this->assertStringContainsString($class, $html);
        }

        $this->assertStringContainsString(route('admin_bulk_destroy_pc_finishings'), $html);
        $this->assertStringContainsString(route('admin_create_pc_finishing'), $html);
        $this->assertStringContainsString(route('admin_export_pc_finishings'), $html);
    }

    public function test_digital_listing_filters_searches_and_sorts(): void
    {
        $response = (new ProductionCostFinishingCT())->data(Request::create('/', 'GET', [
            'pricing_engine' => 'digital_print_a3',
            'location_id' => 2,
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => 'jakarta'],
            'order' => [['column' => 3, 'dir' => 'desc']],
        ]))->getData(true);

        $this->assertSame(2, $response['recordsTotal']);
        $this->assertSame(1, $response['recordsFiltered']);
        $this->assertSame(22, $response['data'][0]['digital_production_cost_id']);
        $this->assertSame('Jakarta', $response['data'][0]['location_names']);
    }

    public function test_large_format_listing_returns_location_and_delete_snapshots(): void
    {
        $controller = new ProductionCostFinishingCT();
        $all = $controller->data(Request::create('/', 'GET', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => ''],
        ]))->getData(true);

        $this->assertSame(1, $all['recordsTotal']);
        $this->assertSame([11, 12], $all['data'][0]['production_cost_ids']);
        $this->assertSame('Bandung, Jakarta', $all['data'][0]['location_names']);

        $filtered = $controller->data(Request::create('/', 'GET', [
            'location_id' => 2,
            'draw' => 2,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => 'jakarta'],
        ]))->getData(true);

        $this->assertSame(1, $filtered['recordsFiltered']);
        $this->assertSame([11, 12], $filtered['data'][0]['production_cost_ids']);
        $this->assertSame('Bandung, Jakarta', $filtered['data'][0]['location_names']);
    }

    public function test_changed_large_format_selection_is_rejected_without_deletion(): void
    {
        $response = (new ProductionCostFinishingCT())->bulkDestroy(Request::create('/', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'large_format' => [['engine_id' => 1, 'production_cost_ids' => [11]]],
        ])));

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(2, DB::table('m_production_costs')->count());
    }

    public function test_bulk_delete_is_atomic_when_a_large_format_cost_is_used_by_lamination(): void
    {
        DB::table('m_lamination_production_cost_details')->insert([
            'production_cost_id' => 12,
        ]);

        $response = (new ProductionCostFinishingCT())->bulkDestroy(Request::create('/', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'large_format' => [['engine_id' => 1, 'production_cost_ids' => [11, 12]]],
            'digital_print_a3' => [21],
        ])));

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(2, DB::table('m_production_costs')->count());
        $this->assertSame(2, DB::table('m_digital_production_costs')->count());
        $this->assertSame(2, DB::table('m_digital_production_cost_details')->count());
    }

    public function test_single_delete_also_preserves_large_format_costs_used_by_lamination(): void
    {
        DB::table('m_lamination_production_cost_details')->insert([
            'production_cost_id' => 11,
        ]);

        $response = (new ProductionCostFinishingCT())->destroy(1);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(2, DB::table('m_production_costs')->count());
    }

    public function test_bulk_delete_removes_only_selected_groups_and_owned_details(): void
    {
        $response = (new ProductionCostFinishingCT())->bulkDestroy(Request::create('/', 'POST', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'large_format' => [['engine_id' => 1, 'production_cost_ids' => [11, 12]]],
            'digital_print_a3' => [21],
        ])));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(0, DB::table('m_production_costs')->count());
        $this->assertSame(1, DB::table('m_digital_production_costs')->count());
        $this->assertSame(1, DB::table('m_digital_production_cost_details')->count());
        $this->assertSame(2, DB::table('m_locations')->count());
        $this->assertSame(2, DB::table('m_engines')->count());
    }
}

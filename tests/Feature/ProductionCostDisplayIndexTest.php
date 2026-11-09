<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\ProductionCostFinishingDisplayCT;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ProductionCostDisplayIndexTest extends TestCase
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

        foreach (['m_engines', 'm_locations', 'm_categories'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('status')->default('Active');
            });
        }
        Schema::create('m_display_products', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
            $table->decimal('length', 10, 2);
            $table->decimal('width', 10, 2);
            $table->string('status')->default('Active');
            $table->foreignId('category_id')->constrained('m_categories');
        });
        Schema::create('m_display_product_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('display_product_id')->constrained('m_display_products');
            $table->foreignId('location_id')->constrained('m_locations');
            $table->foreignId('engine_id')->constrained('m_engines');
            $table->decimal('cost_rangka', 15, 2)->default(0);
            $table->decimal('cost_finishing', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
        });
        Schema::create('m_display_product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('display_product_id')->constrained('m_display_products');
            $table->decimal('general_price', 15, 2)->default(0);
            $table->decimal('division_price', 15, 2)->default(0);
            $table->decimal('plain_price', 15, 2)->nullable();
        });
        Schema::create('m_display_product_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('display_product_id')->constrained('m_display_products');
            $table->string('component_type');
        });
        Schema::create('m_display_product_component_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('configuration_id')->constrained('m_display_product_configurations');
            $table->foreignId('component_id')->constrained('m_display_product_components');
            $table->decimal('general_price', 15, 2)->default(0);
        });

        $user = new User();
        $user->name = 'Display Index Test';
        Auth::setUser($user);
        \Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag());

        DB::table('m_engines')->insert([['id' => 1, 'name' => 'Indoor']]);
        DB::table('m_locations')->insert([
            ['id' => 1, 'name' => 'Purwakarta'],
            ['id' => 2, 'name' => 'Graha'],
        ]);
        DB::table('m_categories')->insert([
            ['id' => 1, 'name' => 'Display'],
            ['id' => 2, 'name' => 'Banner'],
        ]);
    }

    private function createDisplay(string $name, int $locationId, int $categoryId = 1): int
    {
        $productId = DB::table('m_display_products')->insertGetId([
            'display_name' => $name,
            'length' => 160,
            'width' => 60,
            'category_id' => $categoryId,
        ]);
        $configurationId = DB::table('m_display_product_configurations')->insertGetId([
            'display_product_id' => $productId,
            'location_id' => $locationId,
            'engine_id' => 1,
        ]);
        DB::table('m_display_product_prices')->insert(['display_product_id' => $productId]);
        $materialId = DB::table('m_display_product_components')->insertGetId([
            'display_product_id' => $productId,
            'component_type' => 'material',
        ]);
        DB::table('m_display_product_components')->insert([
            'display_product_id' => $productId,
            'component_type' => 'lamination',
        ]);
        DB::table('m_display_product_component_prices')->insert([
            'configuration_id' => $configurationId,
            'component_id' => $materialId,
        ]);

        return $productId;
    }

    private function data(array $params): array
    {
        return app(ProductionCostFinishingDisplayCT::class)->data(
            Request::create('/data', 'GET', array_merge(['draw' => 1, 'start' => 0, 'length' => 10], $params))
        )->getData(true);
    }

    private function bulkDelete(array $ids)
    {
        return app(ProductionCostFinishingDisplayCT::class)->bulkDestroy(
            Request::create('/admin/production-cost-finishing-displays/bulk-delete', 'POST', ['ids' => $ids])
        );
    }

    public function test_index_renders_category_style_filters_selection_and_bulk_route(): void
    {
        $html = view('admin.production-cost-finishing-display.index', [
            'filterLocations' => DB::table('m_locations')->orderBy('name')->get(['id', 'name']),
            'filterCategories' => DB::table('m_categories')->orderBy('name')->get(['id', 'name']),
        ])->render();

        $this->assertStringContainsString('cat-modern-table', $html);
        $this->assertStringContainsString('id="checkAllDisplayProducts"', $html);
        $this->assertStringContainsString('id="displayProductSearch"', $html);
        $this->assertStringContainsString('<option value="2">Banner</option>', $html);
        $this->assertStringContainsString('<option value="2">Graha</option>', $html);
        $this->assertStringContainsString('id="displayProductPageLength"', $html);
        $this->assertStringContainsString('topStart: null', $html);
        $this->assertStringContainsString(route('admin_bulk_destroy_pc_finishing_displays'), $html);
        $this->assertStringContainsString('btn-delete-display', $html);
        $this->assertStringContainsString('btn-add-display-component', $html);
        $this->assertStringContainsString('id="modalDeleteDisplay"', $html);
    }

    public function test_data_filters_by_location_and_category_and_sorts(): void
    {
        $this->createDisplay('Roll Banner', 1);
        $graha = $this->createDisplay('X Banner', 2);
        $banner = $this->createDisplay('Y Banner', 1, 2);

        $byLocation = $this->data(['location_id' => 2]);
        $this->assertSame(3, $byLocation['recordsTotal']);
        $this->assertSame(1, $byLocation['recordsFiltered']);
        $this->assertSame($graha, (int) $byLocation['data'][0]['display_product_id']);
        $this->assertSame(1, (int) $byLocation['data'][0]['material_count']);
        $this->assertSame(1, (int) $byLocation['data'][0]['lamination_count']);

        $byCategory = $this->data(['category_id' => 2]);
        $this->assertSame(1, $byCategory['recordsFiltered']);
        $this->assertSame($banner, (int) $byCategory['data'][0]['display_product_id']);

        $this->assertSame(3, $this->data(['location_id' => 'all', 'category_id' => 'x'])['recordsFiltered']);

        $sorted = $this->data(['order' => [['column' => 2, 'dir' => 'desc']]]);
        $this->assertSame(['Y Banner', 'X Banner', 'Roll Banner'], array_column($sorted['data'], 'display_name'));

        $byLocationName = $this->data(['order' => [['column' => 4, 'dir' => 'asc']]]);
        $this->assertSame('Graha', $byLocationName['data'][0]['location_name']);
    }

    public function test_bulk_delete_removes_only_selected_display_costs(): void
    {
        $deleted = $this->createDisplay('Roll Banner', 1);
        $kept = $this->createDisplay('X Banner', 2);

        $response = $this->bulkDelete([$deleted]);

        $this->assertTrue($response->getData(true)['success']);
        $this->assertSame([$kept], DB::table('m_display_product_configurations')->pluck('display_product_id')->all());
        $this->assertSame([$kept], DB::table('m_display_product_prices')->pluck('display_product_id')->all());
        $this->assertSame(2, DB::table('m_display_product_components')->count());
        $this->assertSame(1, DB::table('m_display_product_component_prices')->count());
        $this->assertSame(2, DB::table('m_display_products')->count());
        $this->assertSame(2, DB::table('m_categories')->count());
    }

    public function test_bulk_delete_is_atomic_when_any_selected_row_is_missing(): void
    {
        $existing = $this->createDisplay('Roll Banner', 1);

        $response = $this->bulkDelete([$existing, 9999]);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
        $this->assertSame(1, DB::table('m_display_product_configurations')->count());
        $this->assertSame(1, DB::table('m_display_product_component_prices')->count());
    }
}

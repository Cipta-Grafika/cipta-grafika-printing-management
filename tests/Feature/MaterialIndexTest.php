<?php

namespace Tests\Feature;

use App\Exports\MaterialsExport;
use App\Http\Controllers\admin\MaterialCT;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class MaterialIndexTest extends TestCase
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

        Schema::create('m_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('m_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('m_categories');
            $table->string('material_code');
            $table->string('material_name');
            $table->string('status');
            $table->string('created_by');
            $table->timestamp('created_at');
            $table->string('updated_by');
            $table->timestamp('updated_at');
        });
        Schema::create('m_material_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('m_materials')->cascadeOnDelete();
            $table->decimal('width', 12, 2);
            $table->decimal('length', 12, 2)->nullable();
            $table->string('unit');
        });
        Schema::create('other_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('material_id');
        });
        Schema::create('named_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_id')->constrained('m_materials')->cascadeOnDelete();
        });
        DB::table('m_categories')->insert([
            ['id' => 1, 'name' => 'Kertas'],
            ['id' => 2, 'name' => 'Vinyl'],
        ]);

        Carbon::setTestNow('2026-11-07 12:00:00');
        foreach ([
            [1, 1, 'K01', 'Art Paper', 'Active', 'Alice', '2026-11-07 10:00:00'],
            [2, 2, 'V02', 'Vinyl Putih', 'Inactive', 'Bob', '2026-11-02 10:00:00'],
            [3, 1, 'K03', 'HVS', 'Active', 'Alice', '2026-10-01 10:00:00'],
        ] as [$id, $category, $code, $name, $status, $creator, $date]) {
            DB::table('m_materials')->insert([
                'id' => $id, 'category_id' => $category, 'material_code' => $code, 'material_name' => $name,
                'status' => $status, 'created_by' => $creator, 'created_at' => $date,
                'updated_by' => $creator, 'updated_at' => $date,
            ]);
        }

        $user = new User();
        $user->name = 'Test Admin';
        $user->email = 'admin@example.test';
        Auth::setUser($user);
        View::share('errors', new ViewErrorBag());
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function fetch(array $params = []): array
    {
        return (new MaterialCT())->data(Request::create('/admin/materials/data', 'GET', array_replace([
            'draw' => 1, 'start' => 0, 'length' => 10, 'search' => ['value' => ''],
        ], $params)))->getData(true);
    }

    public function test_listing_search_filters_order_and_pagination(): void
    {
        $this->assertSame([1, 3, 2], array_column($this->fetch()['data'], 'id'));
        $this->assertSame([2], array_column($this->fetch(['search' => ['value' => 'v02']])['data'], 'id'));
        $this->assertSame([1, 3], array_column($this->fetch(['search' => ['value' => 'kertas']])['data'], 'id'));
        $this->assertSame([1, 3], array_column($this->fetch(['search' => ['value' => 'alice']])['data'], 'id'));
        $filtered = $this->fetch(['creator' => 'Alice', 'period' => 'today', 'category_id' => 1]);
        $this->assertSame(3, $filtered['recordsTotal']);
        $this->assertSame(1, $filtered['recordsFiltered']);
        $this->assertSame([1], array_column($filtered['data'], 'id'));
        $this->assertSame([2], array_column($this->fetch(['category_id' => 2])['data'], 'id'));
        $this->assertSame([2, 3, 1], array_column($this->fetch(['order' => [['column' => 3, 'dir' => 'desc']]])['data'], 'id'));
        $this->assertSame([3], array_column($this->fetch(['start' => 1, 'length' => 1])['data'], 'id'));
        $this->assertSame(2, $this->fetch(['period' => '7_days'])['recordsFiltered']);
        $this->assertSame(2, $this->fetch(['period' => 'month'])['recordsFiltered']);
        $this->assertSame(3, $this->fetch(['period' => 'year'])['recordsFiltered']);
    }

    public function test_category_filtered_export_is_preserved(): void
    {
        $export = new MaterialsExport(2);
        $this->assertSame(['V02'], $export->collection()->pluck('material_code')->all());
        $this->assertSame(3, (new MaterialsExport())->collection()->count());
    }

    public function test_index_renders_controls_and_existing_actions(): void
    {
        $html = (new MaterialCT())->index()->render();
        foreach (['materialTable', 'materialSearch', 'filterMaterialCategory', 'materialCreator', 'materialPeriod', 'materialPageLength', 'checkAllMaterials', 'modalBulkDeleteMaterials', 'btnTambahMaterial', 'btnImportMaterial', 'btnExportMaterial', 'modalEditMaterial', 'modalViewMaterial'] as $id) {
            $this->assertStringContainsString('id="'.$id.'"', $html);
        }
        $this->assertStringContainsString(route('admin_bulk_destroy_materials'), $html);
        $this->assertStringContainsString('Kertas', $html);
    }

    public function test_bulk_delete_is_atomic_when_a_material_has_sizes(): void
    {
        DB::table('m_material_sizes')->insert(['material_id' => 2, 'width' => 100, 'unit' => 'cm']);
        $response = (new MaterialCT())->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 2]]));
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(3, DB::table('m_materials')->count());
        $this->assertSame(1, DB::table('m_material_sizes')->count());
    }

    public function test_single_delete_protects_unconstrained_material_references(): void
    {
        DB::table('other_records')->insert(['material_id' => 1]);
        $this->assertSame(422, (new MaterialCT())->destroy(1)->getStatusCode());
        $this->assertSame(3, DB::table('m_materials')->count());
        $this->assertSame(1, DB::table('other_records')->count());
    }

    public function test_delete_protects_foreign_keys_with_other_column_names(): void
    {
        DB::table('named_references')->insert(['stock_id' => 3]);
        $this->assertSame(422, (new MaterialCT())->destroy(3)->getStatusCode());
        $this->assertSame(1, DB::table('named_references')->count());
    }

    public function test_bulk_delete_rejects_missing_ids_and_deletes_only_selected_unused_materials(): void
    {
        $controller = new MaterialCT();
        $this->assertSame(404, $controller->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 99]]))->getStatusCode());
        $this->assertSame(3, DB::table('m_materials')->count());
        $this->assertSame(200, $controller->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 3]]))->getStatusCode());
        $this->assertSame([2], DB::table('m_materials')->pluck('id')->all());
        $this->assertSame(2, DB::table('m_categories')->count());
    }

    public function test_bulk_delete_route_rejects_invalid_input_without_changing_data(): void
    {
        $this->postJson(route('admin_bulk_destroy_materials'), ['ids' => [1, 1]])->assertUnprocessable();
        $this->postJson(route('admin_bulk_destroy_materials'), ['ids' => []])->assertUnprocessable();
        $this->assertSame(3, DB::table('m_materials')->count());
    }
}

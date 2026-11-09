<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\CuttingCT;
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

class CuttingIndexTest extends TestCase
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
        Schema::create('m_cuttings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->string('created_by');
            $table->timestamp('created_at');
        });
        Schema::create('m_cutting_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cutting_id')->constrained('m_cuttings')->cascadeOnDelete();
        });
        Schema::create('related_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cutting_id');
        });
        Schema::create('named_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('m_cuttings')->cascadeOnDelete();
        });
        Carbon::setTestNow('2026-11-07 12:00:00');
        foreach ([
            [1, 'Potong', 'Active', 'Alice', '2026-11-07 10:00:00'],
            [2, 'Kiss Cut', 'Inactive', 'Bob', '2026-11-02 10:00:00'],
            [3, 'Die Cut', 'Active', 'Alice', '2026-10-01 10:00:00'],
        ] as [$id, $name, $status, $creator, $date]) {
            DB::table('m_cuttings')->insert([
                'id' => $id, 'name' => $name, 'status' => $status,
                'created_by' => $creator, 'created_at' => $date,
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
        return (new CuttingCT())->data(Request::create('/admin/cuttings/data', 'GET', array_replace([
            'draw' => 1, 'start' => 0, 'length' => 10, 'search' => ['value' => ''],
        ], $params)))->getData(true);
    }

    public function test_listing_search_filters_order_and_pagination(): void
    {
        DB::table('m_cutting_sizes')->insert([['cutting_id' => 3], ['cutting_id' => 3], ['cutting_id' => 1]]);
        $this->assertSame([3, 1, 2], array_column($this->fetch()['data'], 'id'));
        $this->assertSame([2], array_column($this->fetch(['search' => ['value' => 'kiss']])['data'], 'id'));
        $this->assertSame([3, 1], array_column($this->fetch(['search' => ['value' => 'alice']])['data'], 'id'));
        $this->assertSame(0, $this->fetch(['search' => ['value' => '0']])['recordsFiltered']);
        $filtered = $this->fetch(['creator' => 'Alice', 'period' => 'today']);
        $this->assertSame(3, $filtered['recordsTotal']);
        $this->assertSame([1], array_column($filtered['data'], 'id'));
        $this->assertSame(2, $this->fetch(['period' => '7_days'])['recordsFiltered']);
        $this->assertSame(2, $this->fetch(['period' => 'month'])['recordsFiltered']);
        $this->assertSame(3, $this->fetch(['period' => 'year'])['recordsFiltered']);
        $this->assertSame([3, 1, 2], array_column($this->fetch(['order' => [['column' => 3, 'dir' => 'desc']]])['data'], 'id'));
        $this->assertSame([1], array_column($this->fetch(['start' => 1, 'length' => 1])['data'], 'id'));
    }

    public function test_index_renders_controls_and_existing_actions(): void
    {
        $html = (new CuttingCT())->index()->render();
        foreach (['cuttingTable', 'cuttingSearch', 'cuttingCreator', 'cuttingPeriod', 'cuttingPageLength', 'checkAllCuttings', 'modalBulkDeleteCuttings', 'btnTambahCutting', 'btnImportCutting', 'btnExportCutting', 'modalDeleteCutting', 'modalDetailCutting', 'modalEditCutting'] as $id) {
            $this->assertStringContainsString('id="'.$id.'"', $html);
        }
        $this->assertStringContainsString(route('admin_bulk_destroy_cuttings'), $html);
        $this->assertStringContainsString('Alice', $html);
    }

    public function test_delete_protects_sizes_and_is_atomic(): void
    {
        DB::table('m_cutting_sizes')->insert(['cutting_id' => 2]);
        $controller = new CuttingCT();
        $this->assertSame(422, $controller->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 2]]))->getStatusCode());
        $this->assertSame(422, $controller->destroy(2)->getStatusCode());
        $this->assertSame(3, DB::table('m_cuttings')->count());
        $this->assertSame(1, DB::table('m_cutting_sizes')->count());
    }

    public function test_delete_protects_foreign_and_unconstrained_references(): void
    {
        DB::table('related_records')->insert(['cutting_id' => 1]);
        DB::table('named_references')->insert(['service_id' => 3]);
        $this->assertSame(422, (new CuttingCT())->destroy(1)->getStatusCode());
        $this->assertSame(422, (new CuttingCT())->destroy(3)->getStatusCode());
        $this->assertSame(3, DB::table('m_cuttings')->count());
        $this->assertSame(1, DB::table('named_references')->count());
    }

    public function test_bulk_delete_rejects_missing_ids_and_deletes_only_selected_unused_cuttings(): void
    {
        $controller = new CuttingCT();
        $this->assertSame(404, $controller->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 99]]))->getStatusCode());
        $this->assertSame(3, DB::table('m_cuttings')->count());
        $this->assertSame(200, $controller->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 3]]))->getStatusCode());
        $this->assertSame([2], DB::table('m_cuttings')->pluck('id')->all());
    }

    public function test_bulk_delete_route_rejects_invalid_input_without_changing_data(): void
    {
        $this->postJson(route('admin_bulk_destroy_cuttings'), ['ids' => [1, 1]])->assertUnprocessable();
        $this->assertSame(3, DB::table('m_cuttings')->count());
    }
}

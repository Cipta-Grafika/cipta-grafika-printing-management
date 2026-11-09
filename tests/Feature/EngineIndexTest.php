<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\EngineCT;
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

class EngineIndexTest extends TestCase
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
            $table->decimal('minimum_charge', 12, 2)->nullable();
            $table->string('status');
            $table->string('created_by');
            $table->timestamp('created_at');
            $table->string('updated_by');
            $table->timestamp('updated_at');
        });
        Schema::create('related_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('engine_id')->constrained('m_engines')->cascadeOnDelete();
        });
        Schema::create('other_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('engine_id');
        });
        Schema::create('named_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained('m_engines')->cascadeOnDelete();
        });

        Carbon::setTestNow('2026-11-07 12:00:00');
        foreach ([
            [1, 'Digital', 1.5, 'Active', 'Alice', '2026-11-07 10:00:00'],
            [2, 'Offset', null, 'Inactive', 'Bob', '2026-11-02 10:00:00'],
            [3, 'Banner', 4, 'Active', 'Alice', '2026-10-01 10:00:00'],
        ] as [$id, $name, $charge, $status, $creator, $date]) {
            DB::table('m_engines')->insert([
                'id' => $id, 'name' => $name, 'minimum_charge' => $charge, 'status' => $status,
                'created_by' => $creator, 'created_at' => $date,
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
        $request = Request::create('/admin/engines/data', 'GET', array_replace([
            'draw' => 1, 'start' => 0, 'length' => 10, 'search' => ['value' => ''],
        ], $params));

        return (new EngineCT())->data($request)->getData(true);
    }

    public function test_listing_search_filters_order_and_pagination(): void
    {
        $this->assertSame([3, 1, 2], array_column($this->fetch()['data'], 'id'));
        $this->assertSame([2], array_column($this->fetch(['search' => ['value' => 'inactive']])['data'], 'id'));
        $this->assertSame([3, 1], array_column($this->fetch(['search' => ['value' => 'alice']])['data'], 'id'));
        $filtered = $this->fetch(['creator' => 'Alice', 'period' => 'today']);
        $this->assertSame(3, $filtered['recordsTotal']);
        $this->assertSame(1, $filtered['recordsFiltered']);
        $this->assertSame([1], array_column($filtered['data'], 'id'));
        $this->assertSame([2], array_column($this->fetch(['period' => '7_days', 'order' => [['column' => 2, 'dir' => 'desc']], 'length' => 1])['data'], 'id'));
        $this->assertSame([1], array_column($this->fetch(['start' => 1, 'length' => 1])['data'], 'id'));
        $this->assertSame([1, 2], array_column($this->fetch(['period' => 'month'])['data'], 'id'));
        $this->assertSame(3, $this->fetch(['period' => 'year'])['recordsFiltered']);
        $this->assertSame(0, $this->fetch(['search' => ['value' => '0']])['recordsFiltered']);
        $this->assertSame([3, 1, 2], array_column($this->fetch(['order' => [['column' => 3, 'dir' => 'desc']]])['data'], 'id'));
        $this->assertSame(1.5, (float) $this->fetch(['search' => ['value' => 'digital']])['data'][0]['minimum_charge']);
    }

    public function test_index_renders_controls_and_existing_actions(): void
    {
        $html = (new EngineCT())->index()->render();
        foreach (['engineTable', 'engineSearch', 'engineCreator', 'enginePeriod', 'enginePageLength', 'checkAllEngines', 'modalBulkDeleteEngines', 'btnTambahMesin', 'btnImportEngine', 'modalEditMesin', 'modalDeleteEngine'] as $id) {
            $this->assertStringContainsString('id="'.$id.'"', $html);
        }
        $this->assertStringContainsString(route('admin_bulk_destroy_engines'), $html);
        $this->assertStringContainsString(route('admin_export_engine'), $html);
        $this->assertStringContainsString('Alice', $html);
    }

    public function test_bulk_delete_is_atomic_when_a_machine_is_referenced(): void
    {
        DB::table('related_records')->insert(['engine_id' => 2]);
        $response = (new EngineCT())->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 2]]));
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(3, DB::table('m_engines')->count());
        $this->assertSame(1, DB::table('related_records')->count());
    }

    public function test_single_delete_protects_unconstrained_engine_references(): void
    {
        DB::table('other_records')->insert(['engine_id' => 1]);
        $this->assertSame(422, (new EngineCT())->destroy(1)->getStatusCode());
        $this->assertSame(3, DB::table('m_engines')->count());
        $this->assertSame(1, DB::table('other_records')->count());
    }

    public function test_delete_protects_foreign_keys_with_other_column_names(): void
    {
        DB::table('named_references')->insert(['machine_id' => 3]);
        $this->assertSame(422, (new EngineCT())->destroy(3)->getStatusCode());
        $this->assertSame(1, DB::table('named_references')->count());
    }

    public function test_bulk_delete_rejects_missing_ids_and_deletes_only_selected_unused_machines(): void
    {
        $controller = new EngineCT();
        $this->assertSame(404, $controller->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 99]]))->getStatusCode());
        $this->assertSame(3, DB::table('m_engines')->count());
        $this->assertSame(200, $controller->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 3]]))->getStatusCode());
        $this->assertSame([2], DB::table('m_engines')->pluck('id')->all());
    }

    public function test_bulk_delete_route_rejects_invalid_input_without_changing_data(): void
    {
        $this->postJson(route('admin_bulk_destroy_engines'), ['ids' => [1, 1]])->assertUnprocessable();
        $this->postJson(route('admin_bulk_destroy_engines'), ['ids' => []])->assertUnprocessable();
        $this->assertSame(3, DB::table('m_engines')->count());
    }
}

<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\LocationCT;
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

class LocationIndexTest extends TestCase
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

        Schema::create('m_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status');
            $table->string('created_by');
            $table->timestamp('created_at');
            $table->string('updated_by');
            $table->timestamp('updated_at');
        });
        Schema::create('related_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('m_locations')->cascadeOnDelete();
        });
        Schema::create('other_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('location_id');
        });
        Schema::create('named_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('m_locations')->cascadeOnDelete();
        });

        Carbon::setTestNow('2026-11-07 12:00:00');
        foreach ([
            [1, 'Bandung', 'Active', 'Alice', '2026-11-07 10:00:00'],
            [2, 'Jakarta', 'Inactive', 'Bob', '2026-11-02 10:00:00'],
            [3, 'Cirebon', 'Active', 'Alice', '2026-10-01 10:00:00'],
        ] as [$id, $name, $status, $creator, $date]) {
            DB::table('m_locations')->insert([
                'id' => $id, 'name' => $name, 'status' => $status,
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
        $request = Request::create('/admin/locations/data', 'GET', array_replace([
            'draw' => 1, 'start' => 0, 'length' => 10, 'search' => ['value' => ''],
        ], $params));

        return (new LocationCT())->data($request)->getData(true);
    }

    public function test_listing_search_filters_order_and_pagination(): void
    {
        $this->assertSame([1, 3, 2], array_column($this->fetch()['data'], 'id'));
        $this->assertSame([2], array_column($this->fetch(['search' => ['value' => 'inactive']])['data'], 'id'));
        $filtered = $this->fetch(['creator' => 'Alice', 'period' => 'today']);
        $this->assertSame(3, $filtered['recordsTotal']);
        $this->assertSame(1, $filtered['recordsFiltered']);
        $this->assertSame([1], array_column($filtered['data'], 'id'));
        $this->assertSame([2], array_column($this->fetch(['period' => '7_days', 'order' => [['column' => 2, 'dir' => 'desc']], 'length' => 1])['data'], 'id'));
        $this->assertSame([3], array_column($this->fetch(['start' => 1, 'length' => 1])['data'], 'id'));
        $this->assertSame([1, 2], array_column($this->fetch(['period' => 'month'])['data'], 'id'));
        $this->assertSame(3, $this->fetch(['period' => 'year'])['recordsFiltered']);
        $this->assertSame(0, $this->fetch(['search' => ['value' => '0']])['recordsFiltered']);
    }

    public function test_index_renders_location_controls_and_existing_actions(): void
    {
        $html = (new LocationCT())->index()->render();
        foreach (['locationTable', 'locationSearch', 'locationCreator', 'locationPeriod', 'locationPageLength', 'checkAllLocations', 'modalBulkDeleteLocations', 'btnTambahLokasi', 'btnImportLocation', 'btnExportLokasi'] as $id) {
            $this->assertStringContainsString('id="'.$id.'"', $html);
        }
        $this->assertStringContainsString(route('admin_bulk_destroy_locations'), $html);
        $this->assertStringContainsString('Alice', $html);
    }

    public function test_bulk_delete_is_atomic_when_a_location_is_referenced(): void
    {
        DB::table('related_records')->insert(['location_id' => 2]);
        $response = (new LocationCT())->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 2]]));
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(3, DB::table('m_locations')->count());
        $this->assertSame(1, DB::table('related_records')->count());
    }

    public function test_single_delete_protects_unconstrained_location_references(): void
    {
        DB::table('other_records')->insert(['location_id' => 1]);
        $response = (new LocationCT())->destroy(1);
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(3, DB::table('m_locations')->count());
        $this->assertSame(1, DB::table('other_records')->count());
    }

    public function test_delete_protects_foreign_keys_with_other_column_names(): void
    {
        DB::table('named_references')->insert(['branch_id' => 3]);
        $this->assertSame(422, (new LocationCT())->destroy(3)->getStatusCode());
        $this->assertSame(1, DB::table('named_references')->count());
    }

    public function test_bulk_delete_rejects_missing_ids_and_deletes_only_selected_unused_locations(): void
    {
        $controller = new LocationCT();
        $this->assertSame(404, $controller->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 99]]))->getStatusCode());
        $this->assertSame(3, DB::table('m_locations')->count());
        $this->assertSame(200, $controller->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 3]]))->getStatusCode());
        $this->assertSame([2], DB::table('m_locations')->pluck('id')->all());
    }

    public function test_bulk_delete_route_rejects_invalid_input_without_changing_data(): void
    {
        $this->postJson(route('admin_bulk_destroy_locations'), ['ids' => [1, 1]])->assertUnprocessable();
        $this->postJson(route('admin_bulk_destroy_locations'), ['ids' => []])->assertUnprocessable();
        $this->assertSame(3, DB::table('m_locations')->count());
    }
}

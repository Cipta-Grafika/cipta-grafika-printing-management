<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\LaminationCT;
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

class LaminationIndexTest extends TestCase
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

        Schema::create('m_laminations', function (Blueprint $table) {
            $table->id();
            $table->string('lamination_code');
            $table->string('name');
            $table->string('status');
            $table->string('created_by');
            $table->timestamp('created_at');
        });
        Schema::create('m_lamination_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lamination_id')->constrained('m_laminations')->cascadeOnDelete();
        });
        Schema::create('related_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lamination_id');
        });
        Schema::create('named_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finish_id')->constrained('m_laminations')->cascadeOnDelete();
        });
        foreach (['m_engines', 'm_locations'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('status');
            });
        }
        Schema::create('m_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Carbon::setTestNow('2026-11-07 12:00:00');
        foreach ([
            [1, 'L01', 'Glossy', 'Active', 'Alice', '2026-11-07 10:00:00'],
            [2, 'L02', 'Doff', 'Inactive', 'Bob', '2026-11-02 10:00:00'],
            [3, 'L03', 'Matte', 'Active', 'Alice', '2026-10-01 10:00:00'],
        ] as [$id, $code, $name, $status, $creator, $date]) {
            DB::table('m_laminations')->insert([
                'id' => $id, 'lamination_code' => $code, 'name' => $name, 'status' => $status,
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
        return (new LaminationCT())->data(Request::create('/admin/laminations/data', 'GET', array_replace([
            'draw' => 1, 'start' => 0, 'length' => 10, 'search' => ['value' => ''],
        ], $params)))->getData(true);
    }

    public function test_listing_search_filters_order_and_pagination(): void
    {
        DB::table('m_lamination_sizes')->insert([['lamination_id' => 3], ['lamination_id' => 3], ['lamination_id' => 1]]);
        $this->assertSame([1, 3, 2], array_column($this->fetch()['data'], 'id'));
        $this->assertSame([2], array_column($this->fetch(['search' => ['value' => 'l02']])['data'], 'id'));
        $this->assertSame([1, 3], array_column($this->fetch(['search' => ['value' => 'alice']])['data'], 'id'));
        $this->assertSame(0, $this->fetch(['search' => ['value' => '0x']])['recordsFiltered']);
        $filtered = $this->fetch(['creator' => 'Alice', 'period' => 'today']);
        $this->assertSame(3, $filtered['recordsTotal']);
        $this->assertSame([1], array_column($filtered['data'], 'id'));
        $this->assertSame(2, $this->fetch(['period' => '7_days'])['recordsFiltered']);
        $this->assertSame(2, $this->fetch(['period' => 'month'])['recordsFiltered']);
        $this->assertSame(3, $this->fetch(['period' => 'year'])['recordsFiltered']);
        $this->assertSame([3, 1, 2], array_column($this->fetch(['order' => [['column' => 4, 'dir' => 'desc']]])['data'], 'id'));
        $this->assertSame([3], array_column($this->fetch(['start' => 1, 'length' => 1])['data'], 'id'));
    }

    public function test_index_renders_controls_and_existing_actions(): void
    {
        $html = (new LaminationCT())->index()->render();
        foreach (['laminationTable', 'laminationSearch', 'laminationCreator', 'laminationPeriod', 'laminationPageLength', 'checkAllLaminations', 'modalBulkDeleteLaminations', 'btnTambahLaminasi', 'btnImportLamination', 'modalDeleteLaminasi'] as $id) {
            $this->assertStringContainsString('id="'.$id.'"', $html);
        }
        $this->assertStringContainsString(route('admin_bulk_destroy_laminations'), $html);
        $this->assertStringContainsString('Alice', $html);
    }

    public function test_bulk_delete_is_atomic_when_a_lamination_has_sizes(): void
    {
        DB::table('m_lamination_sizes')->insert(['lamination_id' => 2]);
        $response = (new LaminationCT())->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 2]]));
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(422, (new LaminationCT())->destroy(2)->getStatusCode());
        $this->assertSame(3, DB::table('m_laminations')->count());
        $this->assertSame(1, DB::table('m_lamination_sizes')->count());
    }

    public function test_single_delete_protects_foreign_and_unconstrained_references(): void
    {
        DB::table('related_records')->insert(['lamination_id' => 1]);
        DB::table('named_references')->insert(['finish_id' => 3]);
        $this->assertSame(422, (new LaminationCT())->destroy(1)->getStatusCode());
        $this->assertSame(422, (new LaminationCT())->destroy(3)->getStatusCode());
        $this->assertSame(3, DB::table('m_laminations')->count());
        $this->assertSame(1, DB::table('named_references')->count());
    }

    public function test_bulk_delete_rejects_missing_ids_and_deletes_only_selected_unused_laminations(): void
    {
        $controller = new LaminationCT();
        $this->assertSame(404, $controller->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 99]]))->getStatusCode());
        $this->assertSame(3, DB::table('m_laminations')->count());
        $this->assertSame(200, $controller->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 3]]))->getStatusCode());
        $this->assertSame([2], DB::table('m_laminations')->pluck('id')->all());
    }

    public function test_bulk_delete_route_rejects_invalid_input_without_changing_data(): void
    {
        $this->postJson(route('admin_bulk_destroy_laminations'), ['ids' => [1, 1]])->assertUnprocessable();
        $this->assertSame(3, DB::table('m_laminations')->count());
    }
}

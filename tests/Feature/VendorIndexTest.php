<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\VendorCT;
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

class VendorIndexTest extends TestCase
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

        Schema::create('m_vendors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact');
            $table->string('status');
            $table->string('created_by');
            $table->timestamp('created_at');
            $table->string('updated_by');
            $table->timestamp('updated_at');
        });
        Schema::create('related_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('m_vendors')->cascadeOnDelete();
        });
        Schema::create('other_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
        });
        Schema::create('named_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained('m_vendors')->cascadeOnDelete();
        });

        Carbon::setTestNow('2026-11-07 12:00:00');
        foreach ([
            [1, 'Digital', 'b-contact', 'Active', 'Alice', '2026-11-07 10:00:00'],
            [2, 'Offset', 'a-contact', 'Inactive', 'Bob', '2026-11-02 10:00:00'],
            [3, 'Banner', 'c-contact', 'Active', 'Alice', '2026-10-01 10:00:00'],
        ] as [$id, $name, $contact, $status, $creator, $date]) {
            DB::table('m_vendors')->insert([
                'id' => $id, 'name' => $name, 'contact' => $contact, 'status' => $status,
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
        $request = Request::create('/admin/vendors/data', 'GET', array_replace([
            'draw' => 1, 'start' => 0, 'length' => 10, 'search' => ['value' => ''],
        ], $params));

        return (new VendorCT())->data($request)->getData(true);
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
        $this->assertSame([1], array_column($this->fetch(['search' => ['value' => 'b-contact']])['data'], 'id'));
        $this->assertSame('b-contact', $this->fetch(['search' => ['value' => 'digital']])['data'][0]['contact']);
    }

    public function test_index_renders_controls_and_existing_actions(): void
    {
        $html = (new VendorCT())->index()->render();
        foreach (['vendorTable', 'vendorSearch', 'vendorCreator', 'vendorPeriod', 'vendorPageLength', 'checkAllVendors', 'modalBulkDeleteVendors', 'btnTambahVendor', 'btnImportVendor', 'modalEditVendor', 'modalDeleteVendor'] as $id) {
            $this->assertStringContainsString('id="'.$id.'"', $html);
        }
        $this->assertStringContainsString(route('admin_bulk_destroy_vendors'), $html);
        $this->assertStringContainsString(route('admin_export_vendor'), $html);
        $this->assertStringContainsString('Alice', $html);
    }

    public function test_bulk_delete_is_atomic_when_a_vendor_is_referenced(): void
    {
        DB::table('related_records')->insert(['vendor_id' => 2]);
        $response = (new VendorCT())->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 2]]));
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(3, DB::table('m_vendors')->count());
        $this->assertSame(1, DB::table('related_records')->count());
    }

    public function test_single_delete_protects_unconstrained_vendor_references(): void
    {
        DB::table('other_records')->insert(['vendor_id' => 1]);
        $this->assertSame(422, (new VendorCT())->destroy(1)->getStatusCode());
        $this->assertSame(3, DB::table('m_vendors')->count());
        $this->assertSame(1, DB::table('other_records')->count());
    }

    public function test_delete_protects_foreign_keys_with_other_column_names(): void
    {
        DB::table('named_references')->insert(['supplier_id' => 3]);
        $this->assertSame(422, (new VendorCT())->destroy(3)->getStatusCode());
        $this->assertSame(1, DB::table('named_references')->count());
    }

    public function test_bulk_delete_rejects_missing_ids_and_deletes_only_selected_unused_vendors(): void
    {
        $controller = new VendorCT();
        $this->assertSame(404, $controller->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 99]]))->getStatusCode());
        $this->assertSame(3, DB::table('m_vendors')->count());
        $this->assertSame(200, $controller->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 3]]))->getStatusCode());
        $this->assertSame([2], DB::table('m_vendors')->pluck('id')->all());
    }

    public function test_bulk_delete_route_rejects_invalid_input_without_changing_data(): void
    {
        $this->postJson(route('admin_bulk_destroy_vendors'), ['ids' => [1, 1]])->assertUnprocessable();
        $this->postJson(route('admin_bulk_destroy_vendors'), ['ids' => []])->assertUnprocessable();
        $this->assertSame(3, DB::table('m_vendors')->count());
    }
}

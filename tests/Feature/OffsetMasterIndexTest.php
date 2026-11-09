<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\OffsetMasterCT;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class OffsetMasterIndexTest extends TestCase
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

        Schema::create('m_offset_plano_materials', function (Blueprint $table) {
            $table->id();
            $table->string('material_code')->nullable();
            $table->string('material_name');
            $table->decimal('width', 10, 2)->default(0);
            $table->decimal('length', 10, 2)->default(0);
            $table->integer('gramature')->nullable();
            $table->string('paper_type')->nullable();
            $table->decimal('price_per_plano', 15, 2)->default(0);
            $table->string('status')->default('active');
            $table->string('created_by')->nullable();
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('m_offset_plates', function (Blueprint $table) {
            $table->id();
            $table->string('plate_code')->nullable();
            $table->string('plate_name');
            $table->string('machine_name')->nullable();
            $table->decimal('price_per_plate', 15, 2)->default(0);
            $table->string('status')->default('active');
            $table->timestamp('created_at')->nullable();
        });
        Schema::create('m_offset_products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->nullable();
            $table->string('product_name');
            $table->string('category')->nullable();
            $table->decimal('final_width', 10, 2)->default(0);
            $table->decimal('final_length', 10, 2)->default(0);
            $table->decimal('print_sheet_width', 10, 2)->default(0);
            $table->decimal('print_sheet_length', 10, 2)->default(0);
            $table->integer('up_per_sheet')->default(1);
            $table->integer('plano_cut_yield')->default(4);
            $table->integer('sheet_per_rim')->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('active');
            $table->timestamp('created_at')->nullable();
        });

        $user = new User();
        $user->name = 'Offset Index Test';
        Auth::setUser($user);
        \Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag());
    }

    private function plano(string $name, array $extra = []): int
    {
        return DB::table('m_offset_plano_materials')->insertGetId(array_merge([
            'material_code' => 'PL-' . $name,
            'material_name' => $name,
            'width' => 65,
            'length' => 100,
            'gramature' => 150,
            'paper_type' => 'Art Paper',
            'price_per_plano' => 2500,
            'status' => 'active',
            'created_at' => Carbon::now(),
        ], $extra));
    }

    private function data(string $method, array $params): array
    {
        return app(OffsetMasterCT::class)->{$method}(
            Request::create('/data', 'GET', array_merge(['draw' => 1, 'start' => 0, 'length' => 10], $params))
        )->getData(true);
    }

    private function bulkDelete(array $payload)
    {
        return app(OffsetMasterCT::class)->bulkDestroy(
            Request::create('/admin/offset-master/bulk-delete', 'POST', $payload)
        );
    }

    public function test_index_renders_category_style_tables_for_every_tab(): void
    {
        $html = view('admin.offset.index', [
            'totalPlano' => 0,
            'totalPlates' => 0,
            'totalPrintCosts' => 0,
            'totalPricingRules' => 0,
            'totalProducts' => 0,
            'digitalCategories' => collect(),
            'digitalMaterials' => collect(),
        ])->render();

        foreach (['offsetPlano', 'offsetPlates', 'offsetPrintCosts', 'offsetPricingRules', 'offsetProducts'] as $prefix) {
            $this->assertStringContainsString('id="' . $prefix . 'Table" class="cat-modern-table"', $html);
            $this->assertStringContainsString('id="' . $prefix . 'CheckAll"', $html);
            $this->assertStringContainsString('id="' . $prefix . 'Search"', $html);
            $this->assertStringContainsString('id="' . $prefix . 'Status"', $html);
            $this->assertStringContainsString('id="' . $prefix . 'Period"', $html);
            $this->assertStringContainsString('id="' . $prefix . 'PageLength"', $html);
            $this->assertStringContainsString('id="' . $prefix . 'BulkToolbar"', $html);
        }

        $this->assertSame(5, substr_count($html, 'class="cat-table-wrap"'));
        $this->assertStringContainsString('topStart: null', $html);
        $this->assertStringContainsString(route('admin_bulk_delete_offset'), $html);
        $this->assertStringContainsString('data-open-modal="modalTambahProduct"', $html);
        $this->assertStringContainsString('btn-delete-offset', $html);
        $this->assertStringContainsString('id="modalDeleteOffset"', $html);
        $this->assertStringNotContainsString('dtr-control', $html);
    }

    public function test_data_filters_status_and_period_and_sorts_by_whitelisted_columns(): void
    {
        $this->plano('Art Carton', ['gramature' => 260, 'price_per_plano' => 5000]);
        $this->plano('HVS', ['gramature' => 80, 'status' => 'inactive', 'price_per_plano' => 1000]);
        $this->plano('Old Paper', ['created_at' => Carbon::now()->subYears(2)]);

        $inactive = $this->data('dataPlano', ['status' => 'inactive']);
        $this->assertSame(3, $inactive['recordsTotal']);
        $this->assertSame(1, $inactive['recordsFiltered']);
        $this->assertSame('HVS', $inactive['data'][0]['material_name']);

        $this->assertSame(2, $this->data('dataPlano', ['period' => 'year'])['recordsFiltered']);
        $this->assertSame(3, $this->data('dataPlano', ['status' => 'all', 'period' => 'bogus'])['recordsFiltered']);

        $byName = $this->data('dataPlano', ['order' => [['column' => 3, 'dir' => 'desc']]]);
        $this->assertSame(['Old Paper', 'HVS', 'Art Carton'], array_column($byName['data'], 'material_name'));

        $byPrice = $this->data('dataPlano', ['order' => [['column' => 7, 'dir' => 'asc']]]);
        $this->assertSame('HVS', $byPrice['data'][0]['material_name']);

        $unknownColumn = $this->data('dataPlano', ['order' => [['column' => 99, 'dir' => 'desc']]]);
        $this->assertSame('Art Carton', $unknownColumn['data'][0]['material_name']);

        $clamped = $this->data('dataPlano', ['start' => -5, 'length' => 1]);
        $this->assertCount(1, $clamped['data']);

        DB::table('m_offset_products')->insert([
            ['product_name' => 'Brosur A4', 'category' => 'Brosur', 'status' => 'active', 'created_at' => Carbon::now()],
            ['product_name' => 'Flyer A5', 'category' => 'Flyer', 'status' => 'inactive', 'created_at' => Carbon::now()],
        ]);
        $products = $this->data('dataProducts', ['status' => 'active', 'order' => [['column' => 4, 'dir' => 'asc']]]);
        $this->assertSame(1, $products['recordsFiltered']);
        $this->assertSame('Brosur A4', $products['data'][0]['product_name']);
    }

    public function test_bulk_delete_removes_only_selected_rows_of_selected_type(): void
    {
        $deleted = $this->plano('Art Carton');
        $kept = $this->plano('HVS');
        DB::table('m_offset_plates')->insert(['id' => $deleted, 'plate_name' => 'CTP 52']);

        $response = $this->bulkDelete(['type' => 'plano', 'ids' => [$deleted]]);

        $this->assertTrue($response->getData(true)['success']);
        $this->assertSame([$kept], DB::table('m_offset_plano_materials')->pluck('id')->all());
        $this->assertSame(1, DB::table('m_offset_plates')->count());
    }

    public function test_bulk_delete_is_atomic_and_rejects_invalid_type(): void
    {
        $existing = $this->plano('Art Carton');

        $response = $this->bulkDelete(['type' => 'plano', 'ids' => [$existing, 9999]]);

        $this->assertSame(422, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
        $this->assertSame(1, DB::table('m_offset_plano_materials')->count());

        $this->expectException(ValidationException::class);
        $this->bulkDelete(['type' => 'm_materials', 'ids' => [$existing]]);
    }
}

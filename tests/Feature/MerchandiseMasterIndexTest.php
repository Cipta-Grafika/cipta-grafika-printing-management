<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\MerchandiseMasterCT;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MerchandiseMasterIndexTest extends TestCase
{
    private const TABLES = [
        'lanyard' => 'm_lanyard_pricings',
        'nametag' => 'm_name_tag_pricings',
        'plakat' => 'm_plakat_pricings',
        'nota' => 'm_nota_pricings',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.url' => null,
        ]);
        DB::purge('sqlite');
        foreach ([
            '2026_11_04_160000_create_m_lanyard_pricings_table.php',
            '2026_11_04_220000_create_m_name_tag_pricings_table.php',
            '2026_11_04_230000_create_m_plakat_pricings_table.php',
            '2026_11_05_000000_create_m_nota_pricings_table.php',
        ] as $migration) {
            (require database_path('migrations' . DIRECTORY_SEPARATOR . $migration))->up();
        }
        Schema::create('m_display_products', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
        });
        Carbon::setTestNow('2026-11-08 12:00:00');
        $user = new User();
        $user->name = 'Merchandise Index Test';
        Auth::setUser($user);
        View::share('errors', new ViewErrorBag());
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function insert(string $type, string $name, array $extra = []): int
    {
        $defaults = [
            'lanyard' => ['tier_label' => $name, 'width_type' => '2cm', 'package_type' => 'tali', 'min_qty' => 1],
            'nametag' => ['name' => $name, 'type_code' => 'biasa'],
            'plakat' => ['name' => $name, 'material_type' => 'akrilik', 'thickness' => '2mm', 'base_type' => 'akrilik', 'box_type' => 'tanpa_box'],
            'nota' => ['name' => $name, 'paper_type' => 'ncr', 'ply_count' => 2, 'ply_label' => '2 Ply', 'ink_count' => 1, 'ink_label' => '1 Tinta', 'sheets_per_book' => 50],
        ];
        return DB::table(self::TABLES[$type])->insertGetId(array_merge(
            $defaults[$type], ['price' => 25000, 'status' => 'active', 'created_at' => Carbon::now()], $extra
        ));
    }

    private function data(string $type, array $params = []): array
    {
        return app(MerchandiseMasterCT::class)->data(
            Request::create('/data', 'GET', array_merge(['draw' => 3, 'start' => 0, 'length' => 10], $params)),
            $type
        )->getData(true);
    }

    private function bulk(array $params)
    {
        return app(MerchandiseMasterCT::class)->bulkDestroy(Request::create('/bulk', 'POST', $params));
    }

    public function test_all_tabs_render_category_controls_and_preserve_existing_modals(): void
    {
        $html = app(MerchandiseMasterCT::class)->index()->render();
        foreach (['Lanyard', 'NameTag', 'Plakat', 'Nota'] as $suffix) {
            foreach (['Table', 'CheckAll', 'TotalCount', 'Search', 'ClearSearch', 'Variant', 'Status', 'Period', 'Reset', 'PageLength', 'BulkToolbar', 'BulkDelete', 'CancelSelection'] as $control) {
                $this->assertStringContainsString('id="merch' . $suffix . $control . '"', $html);
            }
            foreach (['Create', 'Edit', 'Detail', 'Delete'] as $modal) {
                $this->assertStringContainsString('id="modal' . $modal . $suffix . '"', $html);
            }
        }
        $this->assertSame(4, substr_count($html, 'class="cat-modern-table"'));
        $this->assertSame(4, substr_count($html, 'class="cat-table-wrap"'));
        $this->assertStringContainsString(route('admin_bulk_delete_merchandise'), $html);
        $this->assertStringContainsString('js/merchandise-index.js', $html);
        $this->assertStringNotContainsString('dtr-control', $html);
        $this->assertStringNotContainsString('responsive.recalc', $html);
        $this->assertStringNotContainsString('        .select2-container {', $html);
        $this->assertStringContainsString("['lanyard', 'papannama', 'plakat', 'nota'].includes(initialTab)", $html);
    }

    public function test_search_status_period_and_raw_row_shape_for_all_tabs(): void
    {
        foreach (array_keys(self::TABLES) as $type) {
            $id = $this->insert($type, 'Alpha');
            $this->insert($type, 'Beta', ['status' => 'inactive', 'created_at' => Carbon::now()->subDays(3)]);
            $this->insert($type, 'Old', ['created_at' => Carbon::now()->subYears(2)]);
            $data = $this->data($type);
            $this->assertSame(3, $data['draw']);
            $this->assertSame(3, $data['recordsTotal']);
            $this->assertSame(3, $data['recordsFiltered']);
            $this->assertCount(3, $data['data']);
            $this->assertSame(1, $this->data($type, ['status' => 'INACTIVE'])['recordsFiltered']);
            $this->assertSame(2, $this->data($type, ['status' => 'active'])['recordsFiltered']);
            $filtered = $this->data($type, ['search' => ['value' => 'aLPHa'], 'period' => 'today', 'status' => 'active']);
            $this->assertSame(1, $filtered['recordsFiltered']);
            $this->assertSame($id, $filtered['data'][0]['id']);
            $this->assertArrayHasKey('created_at', $filtered['data'][0]);
            foreach (['7_days', 'month', 'year'] as $period) {
                $this->assertSame(2, $this->data($type, ['period' => $period])['recordsFiltered']);
            }
            $this->assertSame(3, $this->data($type, ['status' => 'all', 'period' => 'invalid', 'variant' => 'invalid'])['recordsFiltered']);
            $this->assertSame(0, $this->data($type, ['search' => ['value' => "Alpha' OR 1=1 --"]])['recordsFiltered']);
        }
    }

    public function test_specific_product_filters_and_localized_search_labels(): void
    {
        $this->insert('lanyard', 'Tali', ['max_qty' => null]);
        $this->insert('lanyard', 'Set', ['width_type' => '2.5cm', 'package_type' => 'set']);
        foreach (['width_2cm', 'width_25cm', 'tali', 'set'] as $variant) {
            $this->assertSame(1, $this->data('lanyard', ['variant' => $variant])['recordsFiltered']);
        }
        $this->assertSame('Tali', $this->data('lanyard', ['search' => ['value' => '2.0 cm']])['data'][0]['tier_label']);
        $this->assertSame(1, $this->data('lanyard', ['search' => ['value' => 'Holder']])['recordsFiltered']);
        $this->assertNull($this->data('lanyard')['data'][0]['max_qty']);
        foreach (['biasa', 'resin', 'resin_emas'] as $type) {
            $this->insert('nametag', $type, ['type_code' => $type, 'fastener_options' => 'Magnet']);
            $this->assertSame(1, $this->data('nametag', ['variant' => $type])['recordsFiltered']);
        }
        $this->assertSame(1, $this->data('nametag', ['search' => ['value' => 'Akrilik Biasa']])['recordsFiltered']);
        $this->assertSame(3, $this->data('nametag', ['search' => ['value' => 'Magnet']])['recordsFiltered']);
        $this->insert('plakat', 'Akrilik');
        $this->insert('plakat', 'Box', ['box_type' => 'box']);
        $this->insert('plakat', 'Kayu', ['material_type' => 'kayu', 'box_type' => 'box']);
        $this->assertSame(1, $this->data('plakat', ['variant' => 'tanpa_box'])['recordsFiltered']);
        $this->assertSame(2, $this->data('plakat', ['variant' => 'box'])['recordsFiltered']);
        $this->assertSame(1, $this->data('plakat', ['search' => ['value' => 'Kayu Solid']])['recordsFiltered']);
        $this->assertSame(1, $this->data('plakat', ['variant' => 'kayu'])['recordsFiltered']);
        foreach ([1, 2, 3] as $ink) {
            $this->insert('nota', 'NCR ' . $ink, ['ink_count' => $ink, 'ink_label' => $ink . ' Tinta']);
            $this->assertSame(1, $this->data('nota', ['variant' => 'ink_' . $ink])['recordsFiltered']);
        }
        $this->insert('nota', 'HVS', ['paper_type' => 'hvs', 'ply_count' => 1, 'ply_label' => '1 Ply', 'sheets_per_book' => 100]);
        $this->assertSame(3, $this->data('nota', ['variant' => 'ncr'])['recordsFiltered']);
        $this->assertSame(1, $this->data('nota', ['variant' => 'hvs'])['recordsFiltered']);
        $this->assertSame(1, $this->data('nota', ['variant' => 'ncr', 'search' => ['value' => '2 Tinta']])['recordsFiltered']);
        $this->assertSame(1, $this->data('nota', ['search' => ['value' => 'HVS Putih']])['recordsFiltered']);
    }

    public function test_numeric_sorting_whitelist_and_bounded_pagination(): void
    {
        foreach (['lanyard' => 7, 'nametag' => 6, 'plakat' => 8, 'nota' => 7] as $type => $priceColumn) {
            $cheap = $this->insert($type, 'Cheap', ['price' => 9]);
            $dear = $this->insert($type, 'Dear', ['price' => 100]);
            $this->assertSame([$dear, $cheap], array_column($this->data($type, ['order' => [['column' => $priceColumn, 'dir' => 'desc']]])['data'], 'id'));
            $this->assertCount(2, $this->data($type, ['order' => [['column' => 999, 'dir' => 'invalid']]])['data']);
        }
        for ($i = 0; $i < 103; $i++) {
            $this->insert('nametag', 'Tag ' . $i);
        }
        $this->assertCount(100, $this->data('nametag', ['start' => -10, 'length' => 1000])['data']);
        $this->assertCount(1, $this->data('nametag', ['length' => -1])['data']);
        $this->assertCount(5, $this->data('nametag', ['start' => 100])['data']);
        $this->assertSame(105, $this->data('nametag', ['search' => ['value' => 'Tag 102']])['recordsTotal']);
    }

    public function test_bulk_delete_is_atomic_and_only_affects_selected_type_and_rows(): void
    {
        foreach (array_keys(self::TABLES) as $type) {
            $this->insert($type, 'Delete');
            $this->insert($type, 'Keep');
        }
        foreach (self::TABLES as $type => $table) {
            $response = $this->bulk(['type' => $type, 'ids' => [1, 999]]);
            $this->assertSame(422, $response->getStatusCode());
            $this->assertFalse($response->getData(true)['success']);
            $this->assertSame(2, DB::table($table)->count());
            $response = $this->bulk(['type' => $type, 'ids' => [1]]);
            $this->assertSame(1, $response->getData(true)['deleted']);
            $this->assertTrue($response->getData(true)['success']);
            $this->assertSame([2], DB::table($table)->pluck('id')->all());
        }
    }

    public function test_invalid_bulk_payloads_are_rejected_without_deleting_data(): void
    {
        $this->insert('lanyard', 'Keep');
        foreach ([
            ['type' => 'users', 'ids' => [1]], ['type' => 'lanyard', 'ids' => []],
            ['type' => 'lanyard', 'ids' => [1, 1]], ['type' => 'lanyard', 'ids' => [0]],
            ['type' => 'lanyard', 'ids' => ['invalid']], ['type' => 'lanyard', 'ids' => range(1, 101)],
        ] as $payload) {
            try {
                $this->bulk($payload);
                $this->fail('Invalid payload must be rejected.');
            } catch (ValidationException $e) {
                $this->assertNotEmpty($e->errors());
            }
            $this->assertSame(1, DB::table('m_lanyard_pricings')->count());
        }
    }
}

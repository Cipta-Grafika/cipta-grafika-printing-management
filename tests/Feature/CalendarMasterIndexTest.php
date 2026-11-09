<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\CalendarMasterCT;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CalendarMasterIndexTest extends TestCase
{
    private const TABLES = [
        'product' => 'm_calendar_products',
        'material' => 'm_calendar_materials',
        'component' => 'm_calendar_components',
        'rule' => 'm_calendar_pricing_rules',
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
            '2026_10_28_100001_create_m_calendar_products_table.php',
            '2026_10_28_100002_create_m_calendar_materials_table.php',
            '2026_10_28_100003_create_m_calendar_components_table.php',
            '2026_10_28_100004_create_m_calendar_pricing_rules_table.php',
        ] as $migration) {
            (require database_path('migrations' . DIRECTORY_SEPARATOR . $migration))->up();
        }
        Carbon::setTestNow('2026-11-08 12:00:00');
        $user = new User();
        $user->name = 'Calendar Index Test';
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
            'product' => ['product_name' => $name, 'calendar_type' => 'dinding', 'size_label' => '30 x 42 cm', 'final_width' => 30, 'final_height' => 42],
            'material' => ['material_name' => $name, 'category' => 'AP'],
            'component' => ['name' => $name, 'component_type' => 'spiral', 'unit' => 'cm', 'price' => 0.15],
            'rule' => ['rule_name' => $name, 'min_qty' => 100, 'markup_percent' => 35],
        ];
        return DB::table(self::TABLES[$type])->insertGetId(array_merge(
            $defaults[$type], ['status' => 'active', 'created_at' => Carbon::now()], $extra
        ));
    }

    private function data(string $type, array $params = []): array
    {
        return app(CalendarMasterCT::class)->data(
            Request::create('/data', 'GET', array_merge(['draw' => 3, 'start' => 0, 'length' => 10], $params)),
            $type
        )->getData(true);
    }

    private function bulk(array $params)
    {
        return app(CalendarMasterCT::class)->bulkDestroy(Request::create('/bulk', 'POST', $params));
    }

    public function test_every_tab_renders_category_controls_and_preserves_forms(): void
    {
        $html = app(CalendarMasterCT::class)->index()->render();
        foreach (['Products', 'Materials', 'Components', 'Rules'] as $suffix) {
            foreach (['Table', 'CheckAll', 'TotalCount', 'Search', 'ClearSearch', 'Status', 'Period', 'Reset', 'PageLength', 'BulkToolbar', 'BulkDelete', 'CancelSelection'] as $control) {
                $this->assertStringContainsString('id="calendar' . $suffix . $control . '"', $html);
            }
        }
        $this->assertSame(4, substr_count($html, 'class="cat-modern-table"'));
        $this->assertSame(4, substr_count($html, 'class="cat-table-wrap"'));
        foreach (['Product', 'Material', 'Component', 'Rule'] as $type) {
            $this->assertStringContainsString('id="modalCreate' . $type . '"', $html);
            $this->assertStringContainsString('id="modalEdit' . $type . '"', $html);
        }
        $this->assertStringContainsString('modalDetailCalendar', $html);
        $this->assertStringContainsString(route('admin_bulk_delete_calendar'), $html);
        $this->assertStringContainsString('js/calendar-index.js', $html);
        $this->assertStringNotContainsString('dtr-control', $html);
        $this->assertStringNotContainsString('responsive.recalc', $html);
        $this->assertStringNotContainsString('        .select2-container {', $html);
    }

    public function test_all_types_filter_search_status_period_and_return_raw_details(): void
    {
        foreach (array_keys(self::TABLES) as $type) {
            $today = $this->insert($type, 'Alpha');
            $this->insert($type, 'Beta', ['status' => 'inactive', 'created_at' => Carbon::now()->subDays(3)]);
            $this->insert($type, 'Old', ['created_at' => Carbon::now()->subYears(2)]);
            $all = $this->data($type);
            $this->assertSame(3, $all['draw']);
            $this->assertSame(3, $all['recordsTotal']);
            $this->assertSame(3, $all['recordsFiltered']);
            $this->assertCount(3, $all['data']);
            $this->assertSame(2, $this->data($type, ['status' => 'active'])['recordsFiltered']);
            $this->assertSame(1, $this->data($type, ['status' => 'INACTIVE'])['recordsFiltered']);
            $filtered = $this->data($type, ['search' => ['value' => 'aLPHa'], 'status' => 'active', 'period' => 'today']);
            $this->assertSame(1, $filtered['recordsFiltered']);
            $this->assertSame($today, $filtered['data'][0]['id']);
            $this->assertArrayHasKey('created_at', $filtered['data'][0]);
            foreach (['7_days', 'month', 'year'] as $period) {
                $this->assertSame(2, $this->data($type, ['period' => $period])['recordsFiltered']);
            }
            $this->assertSame(3, $this->data($type, ['period' => 'invalid', 'status' => 'all'])['recordsFiltered']);
            $this->assertSame(0, $this->data($type, ['search' => ['value' => "Alpha' OR 1=1 --"]])['recordsFiltered']);
        }
        $this->assertSame(3, $this->data('product', ['search' => ['value' => '42']])['recordsFiltered']);
    }

    public function test_sorting_dimensions_prices_and_quantity_are_numeric_and_whitelisted(): void
    {
        $small = $this->insert('product', 'Zeta', ['final_width' => 9]);
        $large = $this->insert('product', 'Alpha', ['final_width' => 100]);
        $this->assertSame([$small, $large], array_column($this->data('product', ['order' => [['column' => 4, 'dir' => 'asc']]])['data'], 'id'));
        $this->assertSame([$small, $large], array_column($this->data('product', ['order' => [['column' => 3, 'dir' => 'desc']]])['data'], 'id'));
        $this->assertSame([$large, $small], array_column($this->data('product', ['order' => [['column' => 999, 'dir' => 'bad']]])['data'], 'id'));
        $cheap = $this->insert('component', 'Cheap', ['price' => 0.15]);
        $dear = $this->insert('component', 'Dear', ['price' => 150]);
        $this->assertSame([$dear, $cheap], array_column($this->data('component', ['order' => [['column' => 6, 'dir' => 'desc']]])['data'], 'id'));
        $this->insert('rule', 'Large Tier', ['min_qty' => 1000]);
        $this->insert('rule', 'Small Tier', ['min_qty' => 10]);
        $this->assertSame([10, 1000], array_column($this->data('rule')['data'], 'min_qty'));
    }

    public function test_pagination_is_bounded_and_total_is_not_filtered(): void
    {
        for ($i = 0; $i < 105; $i++) {
            $this->insert('material', 'Paper ' . $i);
        }
        $this->assertCount(100, $this->data('material', ['start' => -20, 'length' => 1000])['data']);
        $this->assertCount(1, $this->data('material', ['length' => -1])['data']);
        $this->assertCount(5, $this->data('material', ['start' => 100])['data']);
        $this->assertSame(105, $this->data('material', ['search' => ['value' => 'Paper 104']])['recordsTotal']);
    }

    public function test_bulk_delete_is_isolated_and_atomic_for_each_tab(): void
    {
        foreach (array_keys(self::TABLES) as $type) {
            $this->insert($type, 'First');
            $this->insert($type, 'Keep');
        }
        foreach (self::TABLES as $type => $table) {
            $response = $this->bulk(['type' => $type, 'ids' => [1, 999]]);
            $this->assertSame(422, $response->getStatusCode());
            $this->assertFalse($response->getData(true)['success']);
            $this->assertSame(2, DB::table($table)->count());
            $response = $this->bulk(['type' => $type, 'ids' => [1]]);
            $this->assertTrue($response->getData(true)['success']);
            $this->assertSame(1, $response->getData(true)['deleted']);
            $this->assertSame([2], DB::table($table)->pluck('id')->all());
        }
    }

    public function test_invalid_bulk_payloads_do_not_delete_anything(): void
    {
        $this->insert('product', 'Keep');
        foreach ([
            ['type' => 'users', 'ids' => [1]],
            ['type' => 'product', 'ids' => []],
            ['type' => 'product', 'ids' => [1, 1]],
            ['type' => 'product', 'ids' => [0]],
            ['type' => 'product', 'ids' => ['invalid']],
            ['type' => 'product', 'ids' => range(1, 101)],
        ] as $payload) {
            try {
                $this->bulk($payload);
                $this->fail('Invalid payload must be rejected.');
            } catch (ValidationException $e) {
                $this->assertNotEmpty($e->errors());
            }
            $this->assertSame(1, DB::table('m_calendar_products')->count());
        }
    }
}

<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\DiscountCT;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class DiscountIndexTest extends TestCase
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

        foreach (['m_engines', 'm_categories'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('name');
            });
        }

        foreach ([
            '2026_10_14_155722_create_m_discounts_table.php',
            '2026_10_14_155746_create_m_discount_scopes_table.php',
        ] as $file) {
            (require database_path('migrations\\' . $file))->up();
        }

        $user = new User();
        $user->name = 'Discount Test';
        $user->email = 'discount@example.test';
        Auth::setUser($user);
        View::share('errors', new ViewErrorBag());

        DB::table('m_engines')->insert([
            ['id' => 1, 'name' => 'Digital Print A3+'],
            ['id' => 2, 'name' => 'Large Format'],
        ]);
        DB::table('m_categories')->insert([
            ['id' => 1, 'name' => 'Sticker'],
            ['id' => 2, 'name' => 'HVS'],
            ['id' => 3, 'name' => 'Banner'],
        ]);

        $now = now();
        DB::table('m_discounts')->insert([
            [
                'id' => 1, 'name' => 'Promo Kemerdekaan', 'description' => 'Diskon 17 Agustus',
                'discount_percentage' => 17.5, 'starts_at' => $now->copy()->subDay(), 'ends_at' => $now->copy()->addDay(),
                'status' => 'Active', 'created_by' => 'Admin', 'created_at' => $now,
            ],
            [
                'id' => 2, 'name' => 'Promo Akhir Tahun', 'description' => null,
                'discount_percentage' => 10, 'starts_at' => $now->copy()->addMonth(), 'ends_at' => $now->copy()->addMonths(2),
                'status' => 'Inactive', 'created_by' => 'Admin', 'created_at' => $now,
            ],
            [
                'id' => 3, 'name' => 'Promo Lama', 'description' => null,
                'discount_percentage' => 5, 'starts_at' => $now->copy()->subMonths(2), 'ends_at' => $now->copy()->subMonth(),
                'status' => 'Active', 'created_by' => 'Admin', 'created_at' => $now,
            ],
        ]);
        DB::table('m_discount_scopes')->insert([
            ['discount_id' => 1, 'engine_id' => 1, 'category_id' => 1],
            ['discount_id' => 1, 'engine_id' => 1, 'category_id' => 2],
            ['discount_id' => 1, 'engine_id' => 2, 'category_id' => 3],
            ['discount_id' => 3, 'engine_id' => 2, 'category_id' => 3],
        ]);
    }

    private function fetch(array $params = []): array
    {
        $request = Request::create('/admin/discounts/data', 'GET', array_replace([
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'search' => ['value' => ''],
        ], $params));

        return (new DiscountCT())->data($request)->getData(true);
    }

    public function test_data_returns_discounts_with_scope_and_period_state(): void
    {
        $response = $this->fetch();

        $this->assertSame(3, $response['recordsTotal']);
        $this->assertSame(3, $response['recordsFiltered']);
        $this->assertSame([1, 3, 2], array_column($response['data'], 'id'));

        $byId = array_column($response['data'], null, 'id');

        $this->assertSame('running', $byId[1]['period_state']);
        $this->assertSame('upcoming', $byId[2]['period_state']);
        $this->assertSame('expired', $byId[3]['period_state']);

        $this->assertSame(3, $byId[1]['category_total']);
        $this->assertSame([
            ['name' => 'Digital Print A3+', 'categories' => ['HVS', 'Sticker']],
            ['name' => 'Large Format', 'categories' => ['Banner']],
        ], $byId[1]['engines']);
        $this->assertSame([], $byId[2]['engines']);
    }

    public function test_search_matches_name_and_scope_names(): void
    {
        $this->assertSame([2], array_column($this->fetch(['search' => ['value' => 'akhir']])['data'], 'id'));
        $this->assertSame([1], array_column($this->fetch(['search' => ['value' => 'sticker']])['data'], 'id'));
        $this->assertSame([1, 3], array_column($this->fetch(['search' => ['value' => 'large']])['data'], 'id'));
    }

    public function test_pagination_is_applied(): void
    {
        $response = $this->fetch(['start' => 1, 'length' => 1]);

        $this->assertSame(3, $response['recordsFiltered']);
        $this->assertSame([3], array_column($response['data'], 'id'));
    }

    public function test_period_state_uses_wib_wall_clock(): void
    {
        \Illuminate\Support\Carbon::setTestNow('2026-10-14 17:30:00');

        DB::table('m_discounts')->insert([
            'id' => 4, 'name' => 'Promo WIB', 'description' => null,
            'discount_percentage' => 5, 'starts_at' => '2026-10-15 00:00:00', 'ends_at' => '2026-10-15 23:59:59',
            'status' => 'Active', 'created_by' => 'Admin', 'created_at' => '2026-10-14 00:00:00',
        ]);

        $byId = array_column($this->fetch(['search' => ['value' => 'wib']])['data'], null, 'id');

        $this->assertSame('running', $byId[4]['period_state']);

        \Illuminate\Support\Carbon::setTestNow();
    }

    public function test_index_view_renders_datatable(): void
    {
        $html = view('admin.discount.index')->render();

        $this->assertStringContainsString('id="discountTable"', $html);
        $this->assertStringContainsString(route('admin_data_discounts'), $html);
        foreach (['discountSearch', 'discountCreator', 'discountPeriod', 'discountPageLength', 'checkAllDiscounts', 'modalBulkDeleteDiscounts', 'btnTambahDiskon', 'modalEditDiskon', 'modalDeleteDiskon'] as $id) {
            $this->assertStringContainsString('id="'.$id.'"', $html);
        }
        $this->assertStringContainsString(route('admin_bulk_destroy_discounts'), $html);
    }

    public function test_creator_and_creation_period_filters_and_sorting(): void
    {
        \Illuminate\Support\Carbon::setTestNow('2026-11-07 12:00:00');
        DB::table('m_discounts')->where('id', 1)->update(['created_by' => 'Alice', 'created_at' => '2026-11-07 10:00:00']);
        DB::table('m_discounts')->where('id', 2)->update(['created_by' => 'Bob', 'created_at' => '2026-11-02 10:00:00']);
        DB::table('m_discounts')->where('id', 3)->update(['created_by' => 'Alice', 'created_at' => '2026-10-01 10:00:00']);

        $filtered = $this->fetch(['creator' => 'Alice', 'period' => 'today']);
        $this->assertSame(3, $filtered['recordsTotal']);
        $this->assertSame(1, $filtered['recordsFiltered']);
        $this->assertSame([1], array_column($filtered['data'], 'id'));
        $this->assertSame([1, 2], array_column($this->fetch(['period' => '7_days', 'order' => [['column' => 2, 'dir' => 'desc']]])['data'], 'id'));
        $this->assertSame(2, $this->fetch(['period' => 'month'])['recordsFiltered']);
        $this->assertSame(3, $this->fetch(['period' => 'year'])['recordsFiltered']);
        $this->assertSame([3, 2, 1], array_column($this->fetch(['order' => [['column' => 3, 'dir' => 'asc']]])['data'], 'id'));
        \Illuminate\Support\Carbon::setTestNow();
    }

    public function test_bulk_delete_removes_only_selected_discounts_and_their_scopes(): void
    {
        $response = (new DiscountCT())->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 2]]));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame([3], DB::table('m_discounts')->pluck('id')->all());
        $this->assertSame([3], DB::table('m_discount_scopes')->pluck('discount_id')->all());
        $this->assertSame(2, DB::table('m_engines')->count());
        $this->assertSame(3, DB::table('m_categories')->count());
    }

    public function test_bulk_delete_is_atomic_for_missing_or_referenced_discounts(): void
    {
        $controller = new DiscountCT();
        $response = $controller->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 99]]));
        $this->assertSame(404, $response->getStatusCode());

        Schema::create('discount_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('m_discounts')->cascadeOnDelete();
        });
        DB::table('discount_references')->insert(['promotion_id' => 2]);
        $response = $controller->bulkDestroy(Request::create('/', 'POST', ['ids' => [1, 2]]));
        $this->assertSame(422, $response->getStatusCode());
        $this->assertSame(3, DB::table('m_discounts')->count());
        $this->assertSame(4, DB::table('m_discount_scopes')->count());
        $this->assertSame(1, DB::table('discount_references')->count());
    }

    public function test_single_delete_protects_references_to_discount_scopes(): void
    {
        Schema::create('scope_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scope_id')->constrained('m_discount_scopes')->cascadeOnDelete();
        });
        $scopeId = DB::table('m_discount_scopes')->where('discount_id', 1)->value('id');
        DB::table('scope_references')->insert(['scope_id' => $scopeId]);
        $this->assertSame(422, (new DiscountCT())->destroy(1)->getStatusCode());
        $this->assertSame(3, DB::table('m_discounts')->count());
        $this->assertSame(4, DB::table('m_discount_scopes')->count());
        $this->assertSame(1, DB::table('scope_references')->count());
    }

    public function test_single_delete_protects_unconstrained_discount_references(): void
    {
        Schema::create('unconstrained_discount_references', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('discount_id');
        });
        DB::table('unconstrained_discount_references')->insert(['discount_id' => 1]);
        $this->assertSame(422, (new DiscountCT())->destroy(1)->getStatusCode());
        $this->assertSame(3, DB::table('m_discounts')->count());
        $this->assertSame(4, DB::table('m_discount_scopes')->count());
        $this->assertSame(1, DB::table('unconstrained_discount_references')->count());
    }

    public function test_bulk_delete_rejects_invalid_input_without_changing_data(): void
    {
        $this->postJson(route('admin_bulk_destroy_discounts'), ['ids' => [1, 1]])->assertUnprocessable();
        $this->postJson(route('admin_bulk_destroy_discounts'), ['ids' => []])->assertUnprocessable();
        $this->assertSame(3, DB::table('m_discounts')->count());
    }
}

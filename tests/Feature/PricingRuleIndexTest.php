<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\PricingRulesCT;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class PricingRuleIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.url' => null, 'database.connections.sqlite.foreign_key_constraints' => true]);
        DB::purge('sqlite');
        foreach (['m_engines', 'm_locations', 'm_vendors', 'm_categories'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('status')->default('Active');
            });
        }
        foreach (['m_pricing_rules', 'm_digital_pricing_rules'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('engine_id');
                $table->unsignedBigInteger('location_id');
                $table->unsignedBigInteger('category_id');
                $table->unsignedBigInteger('vendor_id')->nullable();
            });
        }
        Schema::create('m_digital_pricing_rule_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('digital_pricing_rule_id')->constrained('m_digital_pricing_rules')->cascadeOnDelete();
        });
        Schema::create('m_production_cost_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('general_pricing_rule_id')->nullable();
            $table->unsignedBigInteger('division_pricing_rule_id')->nullable();
            $table->unsignedBigInteger('plain_pricing_rule_id')->nullable();
        });
        Schema::create('digital_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('digital_pricing_rule_detail_id')->constrained('m_digital_pricing_rule_details')->cascadeOnDelete();
        });
        DB::table('m_engines')->insert([['id' => 1, 'name' => 'Large Format'], ['id' => 2, 'name' => 'Digital Print A3+']]);
        DB::table('m_locations')->insert([['id' => 1, 'name' => 'Bandung'], ['id' => 2, 'name' => 'Jakarta']]);
        DB::table('m_categories')->insert([['id' => 1, 'name' => 'Kertas'], ['id' => 2, 'name' => 'Vinyl']]);
        DB::table('m_vendors')->insert(['id' => 1, 'name' => 'Vendor A']);
        DB::table('m_pricing_rules')->insert([
            ['id' => 1, 'engine_id' => 1, 'location_id' => 1, 'category_id' => 1, 'vendor_id' => null],
            ['id' => 2, 'engine_id' => 1, 'location_id' => 1, 'category_id' => 1, 'vendor_id' => null],
            ['id' => 3, 'engine_id' => 1, 'location_id' => 2, 'category_id' => 2, 'vendor_id' => 1],
        ]);
        DB::table('m_digital_pricing_rules')->insert(['id' => 1, 'engine_id' => 2, 'location_id' => 1, 'category_id' => 1, 'vendor_id' => null]);
        DB::table('m_digital_pricing_rule_details')->insert([['id' => 10, 'digital_pricing_rule_id' => 1], ['id' => 11, 'digital_pricing_rule_id' => 1]]);
        $user = new User();
        $user->name = 'Test Admin';
        $user->email = 'admin@example.test';
        Auth::setUser($user);
        View::share('errors', new ViewErrorBag());
    }

    private function fetch(array $params = []): array
    {
        return (new PricingRulesCT())->data(Request::create('/', 'GET', array_replace([
            'draw' => 1, 'start' => 0, 'length' => 10, 'search' => ['value' => ''],
        ], $params)))->getData(true);
    }

    private function group(): array
    {
        return ['source' => 'large_format', 'member_ids' => [1, 2], 'engine_id' => 1, 'location_id' => 1, 'category_id' => 1, 'vendor_id' => null];
    }

    private function digitalGroup(): array
    {
        return ['source' => 'digital', 'member_ids' => [1], 'detail_ids' => [10, 11], 'engine_id' => 2, 'location_id' => 1, 'category_id' => 1, 'vendor_id' => null];
    }

    public function test_grouped_listing_filter_sort_pagination_and_snapshots(): void
    {
        $data = $this->fetch();
        $this->assertSame(2, $data['recordsTotal']);
        $this->assertSame(2, $data['data'][0]['jumlah_markup']);
        $this->assertSame([1, 2], $data['data'][0]['member_ids']);
        $this->assertSame(1, $this->fetch(['search' => ['value' => 'vendor a']])['recordsFiltered']);
        $this->assertSame(1, $this->fetch(['vendor_id' => 'none'])['recordsFiltered']);
        $this->assertSame(1, $this->fetch(['location_id' => 2, 'category_id' => 2])['recordsFiltered']);
        $this->assertSame('Jakarta', $this->fetch(['order' => [['column' => 3, 'dir' => 'desc']], 'length' => 1])['data'][0]['location_name']);
        $digital = $this->fetch(['pricing_engine' => 'digital_print_a3']);
        $this->assertSame(1, $digital['recordsTotal']);
        $this->assertSame(2, $digital['data'][0]['jumlah_markup']);
        $this->assertSame([10, 11], $digital['data'][0]['detail_ids']);
    }

    public function test_renders_existing_actions_and_new_controls(): void
    {
        $html = (new PricingRulesCT())->index()->render();
        foreach (['pricingRuleTable', 'pricingRuleSearch', 'pricingRuleEngineFilter', 'pricingRuleLocationFilter', 'pricingRuleCategoryFilter', 'pricingRuleVendorFilter', 'pricingRulePageLength', 'checkAllPricingRules', 'modalBulkDeletePricingRules', 'btnExportPricingRules', 'btnImportPricingRules', 'btnTambahMarkup'] as $id) {
            $this->assertStringContainsString('id="'.$id.'"', $html);
        }
        $this->assertStringContainsString(route('admin_bulk_destroy_pricing_rules'), $html);
    }

    public function test_bulk_delete_rejects_referenced_rules_atomically(): void
    {
        DB::table('m_production_cost_details')->insert(['general_pricing_rule_id' => 2]);
        $this->postJson(route('admin_bulk_destroy_pricing_rules'), ['groups' => [$this->group(), $this->digitalGroup()]])
            ->assertUnprocessable()->assertJsonPath('message', 'Pricing rule masih digunakan oleh data lain. Tidak ada data yang dihapus.');
        $this->assertSame(3, DB::table('m_pricing_rules')->count());
        $this->assertSame(2, DB::table('m_digital_pricing_rule_details')->count());
        $this->assertSame(422, (new PricingRulesCT())->destroy(1, 1, 'null', 1)->getStatusCode());
    }

    public function test_digital_delete_protects_referenced_details(): void
    {
        DB::table('digital_prices')->insert(['digital_pricing_rule_detail_id' => 11]);
        $this->postJson(route('admin_bulk_destroy_pricing_rules'), ['groups' => [$this->group(), $this->digitalGroup()]])
            ->assertUnprocessable()->assertJsonPath('message', 'Pricing rule masih digunakan oleh data lain. Tidak ada data yang dihapus.');
        $this->assertSame(422, (new PricingRulesCT())->destroyDigital(1)->getStatusCode());
        $this->assertSame(3, DB::table('m_pricing_rules')->count());
        $this->assertSame(1, DB::table('digital_prices')->count());
    }

    public function test_bulk_rejects_changed_group_snapshots(): void
    {
        DB::table('m_pricing_rules')->insert(['id' => 4, 'engine_id' => 1, 'location_id' => 1, 'category_id' => 1, 'vendor_id' => null]);
        $this->postJson(route('admin_bulk_destroy_pricing_rules'), ['groups' => [$this->group()]])->assertUnprocessable();
        $this->assertSame(4, DB::table('m_pricing_rules')->count());
        DB::table('m_digital_pricing_rule_details')->insert(['id' => 12, 'digital_pricing_rule_id' => 1]);
        $this->postJson(route('admin_bulk_destroy_pricing_rules'), ['groups' => [$this->digitalGroup()]])->assertUnprocessable();
        $this->assertSame(3, DB::table('m_digital_pricing_rule_details')->count());
    }

    public function test_bulk_deletes_only_selected_unused_groups(): void
    {
        $this->postJson(route('admin_bulk_destroy_pricing_rules'), ['groups' => [$this->group(), $this->digitalGroup()]])->assertOk();
        $this->assertSame([3], DB::table('m_pricing_rules')->pluck('id')->all());
        $this->assertSame(0, DB::table('m_digital_pricing_rules')->count());
        $this->assertSame(0, DB::table('m_digital_pricing_rule_details')->count());
        $this->assertSame(2, DB::table('m_categories')->count());
    }

    public function test_invalid_or_duplicate_selections_do_not_delete_data(): void
    {
        $digital = $this->digitalGroup();
        unset($digital['detail_ids']);
        $this->postJson(route('admin_bulk_destroy_pricing_rules'), ['groups' => [$digital]])->assertUnprocessable();
        $this->postJson(route('admin_bulk_destroy_pricing_rules'), ['groups' => [$this->group(), $this->group()]])->assertUnprocessable();
        $this->assertSame(3, DB::table('m_pricing_rules')->count());
        $this->assertSame(2, DB::table('m_digital_pricing_rule_details')->count());
    }

    public function test_single_delete_removes_only_unused_digital_group(): void
    {
        $this->assertSame(200, (new PricingRulesCT())->destroyDigital(1)->getStatusCode());
        $this->assertSame(0, DB::table('m_digital_pricing_rules')->count());
        $this->assertSame(0, DB::table('m_digital_pricing_rule_details')->count());
        $this->assertSame(3, DB::table('m_pricing_rules')->count());
    }
}

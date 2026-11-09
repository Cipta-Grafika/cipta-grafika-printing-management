<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\DisplayPricingRuleCT;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class DisplayPricingRuleIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.url' => null, 'database.connections.sqlite.foreign_key_constraints' => true]);
        DB::purge('sqlite');
        foreach (['m_engines', 'm_locations', 'm_vendors', 'm_categories'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id(); $table->string('name'); $table->string('status')->default('Active');
            });
        }
        Schema::create('m_display_pricing_rules', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('engine_id'); $table->unsignedBigInteger('location_id'); $table->unsignedBigInteger('category_id');
        });
        Schema::create('m_display_product_configurations', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('engine_id'); $table->unsignedBigInteger('location_id');
        });
        Schema::create('named_references', function (Blueprint $table) {
            $table->id(); $table->foreignId('rule_id')->constrained('m_display_pricing_rules')->cascadeOnDelete();
        });
        DB::table('m_engines')->insert([['id' => 1, 'name' => 'Large Format'], ['id' => 2, 'name' => 'Digital']]);
        DB::table('m_locations')->insert([['id' => 1, 'name' => 'Bandung'], ['id' => 2, 'name' => 'Jakarta']]);
        DB::table('m_categories')->insert([['id' => 1, 'name' => 'Display'], ['id' => 2, 'name' => 'Vinyl']]);
        DB::table('m_display_pricing_rules')->insert([
            ['id' => 1, 'engine_id' => 1, 'location_id' => 1, 'category_id' => 1],
            ['id' => 2, 'engine_id' => 1, 'location_id' => 1, 'category_id' => 1],
            ['id' => 3, 'engine_id' => 2, 'location_id' => 2, 'category_id' => 1],
        ]);
        $user = new User(); $user->name = 'Test Admin'; $user->email = 'admin@example.test';
        Auth::setUser($user); View::share('errors', new ViewErrorBag());
    }

    private function fetch(array $params = []): array
    {
        return (new DisplayPricingRuleCT())->data(Request::create('/', 'GET', array_replace([
            'draw' => 1, 'start' => 0, 'length' => 10, 'search' => ['value' => ''],
        ], $params)))->getData(true);
    }

    private function group(int $engine, int $location, array $ids): array
    {
        return ['engine_id' => $engine, 'location_id' => $location, 'category_id' => 1, 'member_ids' => $ids];
    }

    public function test_grouped_listing_filters_sort_and_snapshots(): void
    {
        $data = $this->fetch(['engine_id' => 1]);
        $this->assertSame(2, $data['recordsTotal']);
        $this->assertSame(1, $data['recordsFiltered']);
        $this->assertSame([1, 2], $data['data'][0]['member_ids']);
        $this->assertSame(2, $data['data'][0]['jumlah_markup']);
        $this->assertSame(1, $this->fetch(['search' => ['value' => 'jakarta']])['recordsFiltered']);
        $this->assertSame(0, $this->fetch(['search' => ['value' => '0']])['recordsFiltered']);
        $this->assertSame('Jakarta', $this->fetch(['order' => [['column' => 3, 'dir' => 'desc']], 'length' => 1])['data'][0]['location_name']);
        $this->assertSame(1, $this->fetch(['location_id' => 2])['recordsFiltered']);
    }

    public function test_view_renders_controls_and_existing_actions(): void
    {
        $html = (new DisplayPricingRuleCT())->index()->render();
        foreach (['displayPricingRuleTable', 'displayPricingRuleSearch', 'displayPricingRuleEngineFilter', 'displayPricingRuleLocationFilter', 'displayPricingRulePageLength', 'checkAllDisplayPricingRules', 'modalBulkDeleteDisplayPricingRules', 'btnTambahDisplayMarkup', 'btnImportDisplayPricingRules', 'modalEditDisplayPricingRule', 'modalDetailDisplayPricingRule'] as $id) {
            $this->assertStringContainsString('id="'.$id.'"', $html);
        }
        $this->assertStringContainsString(route('admin_bulk_destroy_display_pricing_rules'), $html);
    }

    public function test_configuration_blocks_single_and_bulk_delete_atomically(): void
    {
        DB::table('m_display_product_configurations')->insert(['engine_id' => 2, 'location_id' => 2]);
        $this->postJson(route('admin_bulk_destroy_display_pricing_rules'), ['groups' => [$this->group(1, 1, [1, 2]), $this->group(2, 2, [3])]])
            ->assertUnprocessable()->assertJsonPath('message', 'Pricing rule masih digunakan oleh konfigurasi produk Display. Tidak ada data yang dihapus.');
        $this->assertSame(422, (new DisplayPricingRuleCT())->destroy(2, 2, 1)->getStatusCode());
        $this->assertSame(3, DB::table('m_display_pricing_rules')->count());
    }

    public function test_foreign_references_and_changed_groups_are_protected(): void
    {
        DB::table('named_references')->insert(['rule_id' => 2]);
        $this->assertSame(422, (new DisplayPricingRuleCT())->destroy(1, 1, 1)->getStatusCode());
        $this->postJson(route('admin_bulk_destroy_display_pricing_rules'), ['groups' => [$this->group(1, 1, [1])]])->assertUnprocessable();
        $this->postJson(route('admin_bulk_destroy_display_pricing_rules'), ['groups' => [$this->group(2, 2, [3]), $this->group(2, 2, [3])]])->assertUnprocessable();
        $this->assertSame(3, DB::table('m_display_pricing_rules')->count());
    }

    public function test_only_selected_unused_group_is_deleted(): void
    {
        $this->postJson(route('admin_bulk_destroy_display_pricing_rules'), ['groups' => [$this->group(1, 1, [1, 2])]])->assertOk();
        $this->assertSame([3], DB::table('m_display_pricing_rules')->pluck('id')->all());
        $this->assertSame(2, DB::table('m_categories')->count());
    }
}

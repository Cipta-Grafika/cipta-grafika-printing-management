<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\ProductionCostFinishingLaminationCT;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DigitalLaminationProductionCostStoreTest extends TestCase
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

        foreach (['m_engines', 'm_locations', 'm_vendors', 'm_categories', 'm_laminations'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('status')->default('Active');
            });
        }
        foreach ([
            '2026_10_11_084022_create_m_digital_lamination_pricing_rules_table.php',
            '2026_10_11_084100_create_m_digital_lamination_pricing_rule_details_table.php',
            '2026_10_13_083837_create_m_digital_lamination_production_costs_table.php',
            '2026_10_13_083859_create_m_digital_lamination_production_cost_details_table.php',
            '2026_10_13_083929_create_m_digital_lamination_production_cost_prices_table.php',
        ] as $file) {
            $migration = require database_path('migrations\\' . $file);
            $migration->up();
        }
        $user = new User();
        $user->name = 'Digital Lamination Test';
        Auth::setUser($user);
        \Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag());
        DB::table('m_engines')->insert([
            ['id' => 1, 'name' => 'Digital Print A3+'],
            ['id' => 2, 'name' => 'Large Format'],
        ]);
        DB::table('m_locations')->insert([
            ['id' => 1, 'name' => 'Purwakarta'],
            ['id' => 2, 'name' => 'Graha'],
            ['id' => 3, 'name' => 'Outsourcing'],
        ]);
        DB::table('m_vendors')->insert([
            ['id' => 1, 'name' => 'Vendor A'],
            ['id' => 2, 'name' => 'Vendor B'],
        ]);
        DB::table('m_categories')->insert([
            ['id' => 1, 'name' => 'Category A'],
            ['id' => 2, 'name' => 'Category B'],
        ]);
        DB::table('m_laminations')->insert([
            ['id' => 1, 'name' => 'Glossy'],
            ['id' => 2, 'name' => 'Doff'],
        ]);
        foreach ([1, 2, 3] as $locationId) {
            foreach ($locationId === 3 ? [1, 2] : [null] as $vendorId) {
                foreach ([1, 2] as $categoryId) {
                    $this->seedRules($locationId, $categoryId, $vendorId);
                }
            }
        }
    }

    private function seedRules(int $locationId, int $categoryId, ?int $vendorId): void
    {
        $headerId = DB::table('m_digital_lamination_pricing_rules')->insertGetId([
            'engine_id' => 1, 'location_id' => $locationId, 'vendor_id' => $vendorId,
            'category_id' => $categoryId, 'created_by' => 'Test', 'created_at' => now(),
        ]);
        $tiers = [
            'tier_1' => [1, 1], 'tier_2_5' => [2, 5], 'tier_6_50' => [6, 50],
            'tier_100' => [51, 100], 'tier_300' => [101, 300], 'tier_500' => [301, null],
        ];
        foreach (['general', 'division'] as $priceType) {
            foreach (['single', 'double'] as $printType) {
                foreach ($tiers as $tier => [$min, $max]) {
                    DB::table('m_digital_lamination_pricing_rule_details')->insert([
                        'digital_lamination_pricing_rule_id' => $headerId,
                        'price_type' => $priceType, 'print_type' => $printType, 'quantity_tier' => $tier,
                        'minimum_quantity' => $min, 'maximum_quantity' => $max,
                        'markup_percentage' => $priceType === 'general' ? ($categoryId === 1 ? 40 : 60) : 80,
                        'rounding_value' => $tier === 'tier_500' ? null : ($categoryId === 1 ? 100 : 250),
                        'status' => 'Active', 'created_by' => 'Test', 'created_at' => now(),
                    ]);
                }
            }
        }
    }

    private function configuration(): array
    {
        return [
            'engine_id' => 1, 'location_ids' => [1], 'category_ids' => [1],
            'items' => [['lamination_id' => 1, 'unit_price' => '1.001', 'click_price' => '203']],
        ];
    }

    private function store(array $configurations)
    {
        return app(ProductionCostFinishingLaminationCT::class)->store(
            Request::create('/test', 'POST', ['configurations' => $configurations])
        );
    }

    private function assertEmptyCosts(): void
    {
        foreach ([
            'm_digital_lamination_production_costs',
            'm_digital_lamination_production_cost_details',
            'm_digital_lamination_production_cost_prices',
        ] as $table) {
            $this->assertSame(0, DB::table($table)->count());
        }
    }

    public function test_multiple_categories_locations_and_laminations_save_exact_prices(): void
    {
        $configuration = $this->configuration();
        $configuration['location_ids'] = [1, 2];
        $configuration['category_ids'] = [1, 2];
        $configuration['items'][] = ['lamination_id' => 2, 'unit_price' => '1001', 'click_price' => '203'];
        $response = $this->store([$configuration]);
        $this->assertSame('success', $response->getData(true)['status']);
        $this->assertSame(2, DB::table('m_digital_lamination_production_costs')->count());
        $details = DB::table('m_digital_lamination_production_cost_details')->get();
        $this->assertCount(8, $details);
        $this->assertSame(192, DB::table('m_digital_lamination_production_cost_prices')->count());
        foreach ($details as $detail) {
            $this->assertSame(1001.0, (float) $detail->paper_cost);
            $this->assertSame(203.0, (float) $detail->click_cost);
            $this->assertSame(1204.0, (float) $detail->single_print_cost);
            $this->assertSame(2408.0, (float) $detail->double_print_cost);
            $this->assertSame('Digital Lamination Test', $detail->created_by);
            $prices = DB::table('m_digital_lamination_production_cost_prices')
                ->where('digital_lamination_production_cost_detail_id', $detail->id)->get();
            $this->assertCount(24, $prices);
            foreach ($prices as $price) {
                $cost = $price->print_type === 'single' ? 1204 : 2408;
                $markup = (int) $detail->category_id === 1 ? 40 : 60;
                $rounding = $price->quantity_tier === 'tier_500' ? null : ((int) $detail->category_id === 1 ? 100 : 250);
                $general = ceil(round($cost * $markup / 100, 6)) + $cost;
                if ($rounding !== null) {
                    $general = ceil(round($general / $rounding, 6)) * $rounding;
                }
                $expected = $price->price_type === 'general' ? $general : round($general * 0.8, 2);
                if ($rounding !== null) {
                    $expected = ceil(round($expected / $rounding, 6)) * $rounding;
                }
                $this->assertEqualsWithDelta($expected, (float) $price->selling_price, 0.000001);
                $rule = DB::table('m_digital_lamination_pricing_rule_details')
                    ->where('id', $price->digital_lamination_pricing_rule_detail_id)->first();
                foreach (['price_type', 'print_type', 'quantity_tier', 'minimum_quantity', 'maximum_quantity', 'markup_percentage', 'rounding_value'] as $field) {
                    $this->assertEquals($rule->$field, $price->$field);
                }
            }
        }
    }

    public function test_existing_header_is_reused_and_different_category_is_allowed(): void
    {
        $configuration = $this->configuration();
        $this->store([$configuration]);
        $header = DB::table('m_digital_lamination_production_costs')->first();
        $configuration['category_ids'] = [2];
        $this->assertSame('success', $this->store([$configuration])->getData(true)['status']);
        $this->assertSame(1, DB::table('m_digital_lamination_production_costs')->count());
        $this->assertSame(2, DB::table('m_digital_lamination_production_cost_details')->count());
        $this->assertEquals($header->created_at, DB::table('m_digital_lamination_production_costs')->first()->created_at);
    }

    public static function decimalPrices(): array
    {
        return [
            'Excel example' => ['316,7', '1.500,00', 316.70, 1500.00, 1816.70, 3633.40],
            'two decimal places' => ['1.234,56', '100,25', 1234.56, 100.25, 1334.81, 2669.62],
            'small amounts' => ['0,10', '0,20', 0.10, 0.20, 0.30, 0.60],
            'integer grouping remains thousands' => ['1.500', '1000', 1500.00, 1000.00, 2500.00, 5000.00],
        ];
    }

    #[DataProvider('decimalPrices')]
    public function test_decimal_costs_and_prices_are_not_misinterpreted(
        string $unit,
        string $click,
        float $paperCost,
        float $clickCost,
        float $single,
        float $double
    ): void {
        $configuration = $this->configuration();
        $configuration['items'][0]['unit_price'] = $unit;
        $configuration['items'][0]['click_price'] = $click;
        $this->assertSame('success', $this->store([$configuration])->getData(true)['status']);
        $detail = DB::table('m_digital_lamination_production_cost_details')->first();
        foreach ([
            'paper_cost' => $paperCost, 'click_cost' => $clickCost,
            'single_print_cost' => $single, 'double_print_cost' => $double,
        ] as $field => $expected) {
            $this->assertEqualsWithDelta($expected, (float) $detail->$field, 0.000001);
        }
        foreach (DB::table('m_digital_lamination_production_cost_prices')->get() as $price) {
            $cost = $price->print_type === 'single' ? $single : $double;
            $general = ceil(round($cost * 0.4, 6)) + $cost;
            if ($price->rounding_value !== null) {
                $general = ceil(round($general / 100, 6)) * 100;
            }
            $expected = $price->price_type === 'general' ? $general : round($general * 0.8, 2);
            if ($price->rounding_value !== null) {
                $expected = ceil(round($expected / 100, 6)) * 100;
            }
            $this->assertEqualsWithDelta($expected, (float) $price->selling_price, 0.000001);
        }
    }

    public function test_existing_detail_is_rejected_without_overwriting_prices(): void
    {
        $configuration = $this->configuration();
        $this->store([$configuration]);
        $before = DB::table('m_digital_lamination_production_cost_prices')->get()->toJson();
        $configuration['items'][0]['unit_price'] = '2000';
        try {
            $this->store([$configuration]);
            $this->fail('Duplicate must be rejected.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('configurations.0.items.0.lamination_id', $e->errors());
        }
        $this->assertSame($before, DB::table('m_digital_lamination_production_cost_prices')->get()->toJson());
        $this->assertSame(1, DB::table('m_digital_lamination_production_cost_details')->count());
    }

    public function test_outsourcing_is_scoped_by_vendor(): void
    {
        $configuration = $this->configuration();
        $configuration['location_ids'] = [3];
        $configuration['vendor_id'] = 1;
        $this->store([$configuration]);
        $configuration['vendor_id'] = 2;
        $this->store([$configuration]);
        $this->assertSame(2, DB::table('m_digital_lamination_production_costs')->count());
        $this->assertSame(2, DB::table('m_digital_lamination_production_cost_details')->count());
    }

    public static function invalidInputs(): array
    {
        return [
            'zero unit price' => ['items.0.unit_price', '0'],
            'zero click price' => ['items.0.click_price', '0'],
            'negative price' => ['items.0.unit_price', '-100'],
            'letters' => ['items.0.unit_price', 'abc100'],
            'too many decimals' => ['items.0.unit_price', '100,501'],
            'multiple commas' => ['items.0.unit_price', '100,5,0'],
            'empty decimals' => ['items.0.unit_price', '100,'],
            'zero decimal amount' => ['items.0.unit_price', '0,00'],
            'English number format' => ['items.0.unit_price', '1,500.00'],
            'malformed grouping' => ['items.0.unit_price', '1.00'],
            'price overflows' => ['items.0.unit_price', '10000000000'],
            'cost overflows' => ['items.0.unit_price', '9999999999'],
            'selling price overflows' => ['items.0.unit_price', '8000000000'],
            'missing price' => ['items.0.click_price', null],
            'missing categories' => ['category_ids', []],
            'duplicate category' => ['category_ids', [1, 1]],
            'unknown category' => ['category_ids', [999]],
            'duplicate location' => ['location_ids', [1, 1]],
            'unknown location' => ['location_ids', [999]],
            'outsourcing mixed locations' => ['location_ids', [1, 3]],
            'outsourcing without vendor' => ['location_ids', [3]],
            'unknown lamination' => ['items.0.lamination_id', 999],
            'missing items' => ['items', []],
        ];
    }

    #[DataProvider('invalidInputs')]
    public function test_invalid_inputs_leave_no_partial_records(string $path, $value): void
    {
        $configuration = $this->configuration();
        data_set($configuration, $path, $value);
        try {
            $this->store([$configuration]);
            $this->fail('Invalid input must be rejected.');
        } catch (ValidationException $e) {
            $this->assertNotEmpty($e->errors());
        }
        $this->assertEmptyCosts();
    }

    public function test_duplicate_items_across_configurations_roll_back_everything(): void
    {
        try {
            $this->store([$this->configuration(), $this->configuration()]);
            $this->fail('Duplicate must be rejected.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('configurations.1.items.0.lamination_id', $e->errors());
        }
        $this->assertEmptyCosts();
    }

    public function test_missing_rule_in_second_category_rolls_back_first_category(): void
    {
        $configuration = $this->configuration();
        $configuration['category_ids'] = [1, 2];
        $headerId = DB::table('m_digital_lamination_pricing_rules')
            ->where('location_id', 1)->where('category_id', 2)->value('id');
        DB::table('m_digital_lamination_pricing_rule_details')
            ->where('digital_lamination_pricing_rule_id', $headerId)
            ->where('price_type', 'division')->where('print_type', 'double')
            ->where('quantity_tier', 'tier_500')->update(['status' => 'Inactive']);
        try {
            $this->store([$configuration]);
            $this->fail('Incomplete rules must be rejected.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('Category B', $e->getMessage());
        }
        $this->assertEmptyCosts();
    }

    public function test_mixed_engines_are_rejected_atomically(): void
    {
        $largeFormat = $this->configuration();
        $largeFormat['engine_id'] = 2;
        try {
            $this->store([$this->configuration(), $largeFormat]);
            $this->fail('Mixed engines must be rejected.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('configurations.1.engine_id', $e->errors());
        }
        $this->assertEmptyCosts();
    }

    public function test_large_format_still_uses_existing_size_validation(): void
    {
        $configuration = $this->configuration();
        $configuration['engine_id'] = 2;
        try {
            $this->store([$configuration]);
            $this->fail('Large Format requires its existing size input.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('configurations.0.items.0.size_ids', $e->errors());
        }
        $this->assertEmptyCosts();
    }

    public static function inactiveMasters(): array
    {
        return [
            'engine' => ['m_engines'],
            'location' => ['m_locations'],
            'lamination' => ['m_laminations'],
            'vendor' => ['m_vendors'],
        ];
    }

    #[DataProvider('inactiveMasters')]
    public function test_inactive_masters_are_rejected(string $table): void
    {
        $configuration = $this->configuration();
        if ($table === 'm_vendors') {
            $configuration['location_ids'] = [3];
            $configuration['vendor_id'] = 1;
        }
        DB::table($table)->where('id', 1)->update(['status' => 'Inactive']);
        try {
            $this->store([$configuration]);
            $this->fail('Inactive master must be rejected.');
        } catch (ValidationException $e) {
            $this->assertNotEmpty($e->errors());
        }
        $this->assertEmptyCosts();
    }

    public function test_non_outsourcing_does_not_use_submitted_vendor(): void
    {
        $configuration = $this->configuration();
        $configuration['vendor_id'] = 1;
        $this->store([$configuration]);
        $this->assertNull(DB::table('m_digital_lamination_production_costs')->value('vendor_id'));
    }

    public function test_duplicate_pricing_rule_headers_are_not_silently_selected(): void
    {
        $this->seedRules(1, 1, null);
        try {
            $this->store([$this->configuration()]);
            $this->fail('Ambiguous pricing rules must be rejected.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('tanpa duplikasi', $e->getMessage());
        }
        $this->assertEmptyCosts();
    }

    public function test_outsourcing_cannot_use_another_vendors_rules(): void
    {
        $configuration = $this->configuration();
        $configuration['location_ids'] = [3];
        $configuration['vendor_id'] = 2;
        DB::table('m_digital_lamination_pricing_rules')->where('vendor_id', 2)->delete();
        try {
            $this->store([$configuration]);
            $this->fail('Another vendor cannot supply pricing rules.');
        } catch (ValidationException $e) {
            $this->assertStringContainsString('Vendor B', $e->getMessage());
        }
        $this->assertEmptyCosts();
    }

    private function listDigital(array $parameters = []): array
    {
        return app(ProductionCostFinishingLaminationCT::class)->data(
            Request::create('/test', 'GET', $parameters + [
                'pricing_engine' => 'digital_print_a3', 'draw' => 7, 'start' => 0, 'length' => 10,
            ])
        )->getData(true);
    }

    public function test_digital_datatable_groups_by_header_and_counts_distinct_laminations(): void
    {
        $configuration = $this->configuration();
        $configuration['location_ids'] = [1, 2];
        $configuration['category_ids'] = [1, 2];
        $configuration['items'][] = ['lamination_id' => 2, 'unit_price' => '1000', 'click_price' => '200'];
        $this->store([$configuration]);
        $response = $this->listDigital();
        $this->assertSame(7, $response['draw']);
        $this->assertSame(2, $response['recordsTotal']);
        $this->assertSame(2, $response['recordsFiltered']);
        $this->assertCount(2, $response['data']);
        foreach ($response['data'] as $row) {
            $this->assertSame('digital_print_a3', $row['pricing_engine']);
            $this->assertSame('Digital Print A3+', $row['engine_name']);
            $this->assertSame('Category A, Category B', $row['category_names']);
            $this->assertSame(2, $row['category_count']);
            $this->assertSame(2, $row['lamination_count']);
            $this->assertNull($row['vendor_name']);
            $this->assertArrayHasKey('digital_lamination_production_cost_id', $row);
            $this->assertArrayNotHasKey('size_count', $row);
        }
        $this->assertSame('Graha', $response['data'][0]['location_names']);
        $page = $this->listDigital(['start' => 1, 'length' => 1]);
        $this->assertCount(1, $page['data']);
        $this->assertSame('Purwakarta', $page['data'][0]['location_names']);
        $this->assertSame(2, $page['recordsFiltered']);
    }

    public function test_digital_category_search_preserves_all_categories_and_lamination_counts(): void
    {
        $configuration = $this->configuration();
        $configuration['category_ids'] = [1, 2];
        $this->store([$configuration]);
        $configuration['location_ids'] = [2];
        $configuration['category_ids'] = [1];
        $this->store([$configuration]);
        $response = $this->listDigital(['search' => ['value' => 'CATEGORY B']]);
        $this->assertSame(2, $response['recordsTotal']);
        $this->assertSame(1, $response['recordsFiltered']);
        $this->assertCount(1, $response['data']);
        $this->assertSame('Category A, Category B', $response['data'][0]['category_names']);
        $this->assertSame(2, $response['data'][0]['category_count']);
        $this->assertSame(1, $response['data'][0]['lamination_count']);
        $empty = $this->listDigital(['search' => ['value' => 'not found']]);
        $this->assertSame(2, $empty['recordsTotal']);
        $this->assertSame(0, $empty['recordsFiltered']);
        $this->assertSame([], $empty['data']);
    }

    public function test_digital_datatable_searches_location_engine_and_vendor(): void
    {
        $configuration = $this->configuration();
        $configuration['location_ids'] = [3];
        $configuration['vendor_id'] = 1;
        $this->store([$configuration]);
        $configuration['vendor_id'] = 2;
        $this->store([$configuration]);
        $vendor = $this->listDigital(['search' => ['value' => 'vendor b']]);
        $this->assertSame(2, $vendor['recordsTotal']);
        $this->assertSame(1, $vendor['recordsFiltered']);
        $this->assertSame('Vendor B', $vendor['data'][0]['vendor_name']);
        foreach (['Outsourcing', 'digital print'] as $search) {
            $response = $this->listDigital(['search' => ['value' => $search]]);
            $this->assertSame(2, $response['recordsFiltered']);
        }
    }

    public function test_digital_datatable_empty_shape_and_invalid_filter(): void
    {
        $this->assertSame([
            'draw' => 7, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => [],
        ], $this->listDigital());
        try {
            $this->listDigital(['pricing_engine' => 'all']);
            $this->fail('Unknown engine filter must be rejected.');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('pricing_engine', $e->errors());
        }
    }

    private function editRequest(array $configurations, int $headerId)
    {
        return app(ProductionCostFinishingLaminationCT::class)->updateDigital(
            Request::create('/test', 'PUT', ['configurations' => $configurations]),
            base64_encode((string) $headerId)
        );
    }

    public function test_edit_round_trip_preserves_non_uniform_category_item_costs(): void
    {
        $first = $this->configuration();
        $first['category_ids'] = [1, 2];
        $this->store([$first]);
        $second = $this->configuration();
        $second['items'][0] = ['lamination_id' => 2, 'unit_price' => '316,70', 'click_price' => '1.500,00'];
        $this->store([$second]);
        $header = DB::table('m_digital_lamination_production_costs')->first();
        $controller = app(ProductionCostFinishingLaminationCT::class);
        $view = $controller->editDigital(base64_encode((string) $header->id));
        $groups = $view->getData()['groups']->all();
        $this->assertCount(2, $groups);
        $this->assertSame([1], $groups[0]['category_ids']);
        $this->assertCount(2, $groups[0]['items']);
        $this->assertSame([2], $groups[1]['category_ids']);
        $this->assertCount(1, $groups[1]['items']);
        $before = DB::table('m_digital_lamination_production_cost_details')
            ->orderBy('category_id')->orderBy('lamination_id')
            ->get(['lamination_id', 'category_id', 'paper_cost', 'click_cost'])->toJson();
        $this->assertSame('success', $this->editRequest($groups, $header->id)->getData(true)['status']);
        $after = DB::table('m_digital_lamination_production_cost_details')
            ->orderBy('category_id')->orderBy('lamination_id')
            ->get(['lamination_id', 'category_id', 'paper_cost', 'click_cost'])->toJson();
        $this->assertSame($before, $after);
        $this->assertSame(72, DB::table('m_digital_lamination_production_cost_prices')->count());
        $updatedHeader = DB::table('m_digital_lamination_production_costs')->first();
        $this->assertEquals($header->id, $updatedHeader->id);
        $this->assertEquals($header->created_at, $updatedHeader->created_at);
        $this->assertEquals($header->created_by, $updatedHeader->created_by);
    }

    public function test_uniform_categories_share_one_edit_group_and_form_renders(): void
    {
        $configuration = $this->configuration();
        $configuration['category_ids'] = [1, 2];
        $this->store([$configuration]);
        $id = DB::table('m_digital_lamination_production_costs')->value('id');
        $view = app(ProductionCostFinishingLaminationCT::class)->editDigital(base64_encode((string) $id));
        $groups = $view->getData()['groups'];
        $this->assertCount(1, $groups);
        $this->assertSame([1, 2], $groups[0]['category_ids']);
        $this->assertSame('1.001,00', $groups[0]['items'][0]['unit_price']);
        \Illuminate\Support\Facades\View::share('errors', new \Illuminate\Support\ViewErrorBag());
        $html = $view->render();
        $this->assertStringContainsString('digitalLaminationEditForm', $html);
        $this->assertStringContainsString('name="_method" value="PUT"', $html);
        $this->assertStringContainsString('function formatDigitalRupiahInput', $html);
        $this->assertStringContainsString('/digital/' . base64_encode((string) $id) . '/update', $html);
    }

    public function test_edit_only_replaces_target_header_and_recalculates_current_rules(): void
    {
        $configuration = $this->configuration();
        $this->store([$configuration]);
        $id = DB::table('m_digital_lamination_production_costs')->value('id');
        $other = $configuration;
        $other['location_ids'] = [2];
        $this->store([$other]);
        $otherId = DB::table('m_digital_lamination_production_costs')->where('location_id', 2)->value('id');
        $otherDetails = DB::table('m_digital_lamination_production_cost_details')
            ->where('digital_lamination_production_cost_id', $otherId)->get();
        $otherPrices = DB::table('m_digital_lamination_production_cost_prices')
            ->whereIn('digital_lamination_production_cost_detail_id', $otherDetails->pluck('id'))->get()->toJson();
        DB::table('m_digital_lamination_pricing_rule_details')
            ->where('price_type', 'general')->update(['markup_percentage' => 50, 'rounding_value' => 100]);
        $configuration['items'][0]['unit_price'] = '316,7';
        $configuration['items'][0]['click_price'] = '1.500,00';
        // Read-only scope comes from the stored header, never from submitted fields.
        $configuration['engine_id'] = 2;
        $configuration['location_ids'] = [2];
        $configuration['vendor_id'] = 1;
        $this->assertSame('success', $this->editRequest([$configuration], $id)->getData(true)['status']);
        $detail = DB::table('m_digital_lamination_production_cost_details')
            ->where('digital_lamination_production_cost_id', $id)->first();
        $this->assertEqualsWithDelta(1816.70, (float) $detail->single_print_cost, 0.000001);
        $this->assertEqualsWithDelta(3633.40, (float) $detail->double_print_cost, 0.000001);
        $price = DB::table('m_digital_lamination_production_cost_prices')
            ->where('digital_lamination_production_cost_detail_id', $detail->id)
            ->where('price_type', 'general')->where('print_type', 'single')->where('quantity_tier', 'tier_1')->first();
        $this->assertSame(2800.0, (float) $price->selling_price);
        $this->assertSame(50.0, (float) $price->markup_percentage);
        $doublePrices = DB::table('m_digital_lamination_production_cost_prices')
            ->where('digital_lamination_production_cost_detail_id', $detail->id)
            ->where('print_type', 'double')->get();
        $this->assertCount(12, $doublePrices);
        foreach ($doublePrices as $doublePrice) {
            $general = ceil(round(3633.40 * 0.5, 6)) + 3633.40;
            $general = ceil(round($general / 100, 6)) * 100;
            $expected = $doublePrice->price_type === 'general' ? $general : round($general * 0.8, 2);
            if ($doublePrice->rounding_value !== null) {
                $expected = ceil(round($expected / 100, 6)) * 100;
            }
            $this->assertEqualsWithDelta($expected, (float) $doublePrice->selling_price, 0.000001);
        }
        $this->assertSame(1, (int) DB::table('m_digital_lamination_production_costs')->where('id', $id)->value('location_id'));
        $this->assertSame($otherDetails->toJson(), DB::table('m_digital_lamination_production_cost_details')
            ->where('digital_lamination_production_cost_id', $otherId)->get()->toJson());
        $this->assertSame($otherPrices, DB::table('m_digital_lamination_production_cost_prices')
            ->whereIn('digital_lamination_production_cost_detail_id', $otherDetails->pluck('id'))->get()->toJson());
    }

    public function test_edit_can_remove_items_and_categories(): void
    {
        $configuration = $this->configuration();
        $configuration['category_ids'] = [1, 2];
        $configuration['items'][] = ['lamination_id' => 2, 'unit_price' => '1000', 'click_price' => '200'];
        $this->store([$configuration]);
        $id = DB::table('m_digital_lamination_production_costs')->value('id');
        $configuration['category_ids'] = [2];
        array_shift($configuration['items']);
        $this->editRequest([$configuration], $id);
        $this->assertSame(1, DB::table('m_digital_lamination_production_cost_details')->count());
        $detail = DB::table('m_digital_lamination_production_cost_details')->first();
        $this->assertSame(2, (int) $detail->category_id);
        $this->assertSame(2, (int) $detail->lamination_id);
        $this->assertSame(24, DB::table('m_digital_lamination_production_cost_prices')->count());
    }

    public static function invalidEdits(): array
    {
        return [
            'no group' => ['no-group'],
            'no item' => ['no-item'],
            'no category' => ['no-category'],
            'bad price' => ['bad-price'],
            'duplicate groups' => ['duplicate'],
            'incomplete rules' => ['incomplete'],
            'conflict other header' => ['conflict'],
        ];
    }

    #[DataProvider('invalidEdits')]
    public function test_failed_edit_preserves_all_original_data(string $case): void
    {
        $configuration = $this->configuration();
        $this->store([$configuration]);
        $id = DB::table('m_digital_lamination_production_costs')->value('id');
        $groups = [$configuration];
        switch ($case) {
            case 'no-group': $groups = []; break;
            case 'no-item': $groups[0]['items'] = []; break;
            case 'no-category': $groups[0]['category_ids'] = []; break;
            case 'bad-price': $groups[0]['items'][0]['unit_price'] = '316.70'; break;
            case 'duplicate': $groups[] = $configuration; break;
            case 'incomplete':
                DB::table('m_digital_lamination_pricing_rule_details')->where('quantity_tier', 'tier_500')->update(['status' => 'Inactive']);
                break;
            case 'conflict':
                $otherId = DB::table('m_digital_lamination_production_costs')->insertGetId([
                    'engine_id' => 1, 'location_id' => 1, 'created_by' => 'Test', 'created_at' => now(),
                ]);
                DB::table('m_digital_lamination_production_cost_details')->insert([
                    'digital_lamination_production_cost_id' => $otherId, 'lamination_id' => 2,
                    'category_id' => 1, 'paper_cost' => 100, 'click_cost' => 200,
                    'single_print_cost' => 300, 'double_print_cost' => 500, 'created_by' => 'Test', 'created_at' => now(),
                ]);
                $groups[0]['items'][0]['lamination_id'] = 2;
                break;
        }
        $snapshots = [];
        foreach (['costs', 'cost_details', 'cost_prices'] as $suffix) {
            $table = 'm_digital_lamination_production_' . $suffix;
            $snapshots[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }
        try {
            $this->editRequest($groups, $id);
            $this->fail('Invalid edit must be rejected.');
        } catch (ValidationException $e) {
            $this->assertNotEmpty($e->errors());
        }
        foreach ($snapshots as $table => $snapshot) {
            $this->assertSame($snapshot, DB::table($table)->orderBy('id')->get()->toJson());
        }
    }

    public function test_saved_inactive_lamination_is_allowed_but_new_inactive_is_rejected(): void
    {
        $configuration = $this->configuration();
        $this->store([$configuration]);
        $id = DB::table('m_digital_lamination_production_costs')->value('id');
        DB::table('m_laminations')->update(['status' => 'Inactive']);
        $this->assertSame('success', $this->editRequest([$configuration], $id)->getData(true)['status']);
        $this->assertCount(1, app(ProductionCostFinishingLaminationCT::class)
            ->editDigital(base64_encode((string) $id))->getData()['laminations']);
        $configuration['items'][0]['lamination_id'] = 2;
        try {
            $this->editRequest([$configuration], $id);
            $this->fail('New inactive laminations must be rejected.');
        } catch (ValidationException $e) {
            $this->assertNotEmpty($e->errors());
        }
        $this->assertSame(1, (int) DB::table('m_digital_lamination_production_cost_details')->value('lamination_id'));
    }

    public function test_digital_edit_rejects_invalid_missing_or_non_digital_ids(): void
    {
        $configuration = $this->configuration();
        $this->store([$configuration]);
        $id = DB::table('m_digital_lamination_production_costs')->value('id');
        DB::table('m_digital_lamination_production_costs')->where('id', $id)->update(['engine_id' => 2]);
        foreach (['bad', base64_encode('0'), base64_encode('9999'), base64_encode((string) $id)] as $encoded) {
            foreach (['editDigital', 'updateDigital'] as $method) {
                try {
                    $controller = app(ProductionCostFinishingLaminationCT::class);
                    if ($method === 'editDigital') {
                        $controller->editDigital($encoded);
                    } else {
                        $controller->updateDigital(Request::create('/test', 'PUT'), $encoded);
                    }
                    $this->fail('Invalid ID must return 404.');
                } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                    $this->assertSame(404, $e->getStatusCode());
                }
            }

        }
    }

    public function test_digital_details_group_by_category_and_show_exact_saved_prices(): void
    {
        $configuration = $this->configuration();
        $configuration['category_ids'] = [1, 2];
        $configuration['items'][0]['unit_price'] = '316,70';
        $configuration['items'][0]['click_price'] = '1.500,00';
        $configuration['items'][] = ['lamination_id' => 2, 'unit_price' => '200', 'click_price' => '100'];
        $this->store([$configuration]);
        $id = DB::table('m_digital_lamination_production_costs')->value('id');
        $savedPrices = DB::table('m_digital_lamination_production_cost_prices')->get()->toJson();
        DB::table('m_digital_lamination_pricing_rule_details')->update(['markup_percentage' => 99]);
        DB::table('m_laminations')->where('id', 1)->update(['status' => 'Inactive']);

        $view = app(ProductionCostFinishingLaminationCT::class)->detailsDigital(base64_encode((string) $id));
        $data = $view->getData();
        $this->assertSame('admin.production-cost-finishing-lamination.details-digital', $view->name());
        $this->assertCount(2, $data['categories']);
        $this->assertCount(4, $data['priceGroups']);
        $this->assertCount(6, $data['tierLabels']);
        foreach ($data['categories'] as $index => $category) {
            $this->assertCount(2, $category->details);
            $this->assertSame($index === 0 ? '40%' : '60%', $category->markups['general_single']['tier_1']);
            $this->assertSame('80%', $category->markups['division_double']['tier_500']);
            $this->assertSame('Doff', $category->details[0]->lamination_name);
            $glossy = $category->details[1];
            $this->assertSame('Glossy', $glossy->lamination_name);
            $this->assertSame(1816.7, (float) $glossy->single_print_cost);
            $this->assertSame(3633.4, (float) $glossy->double_print_cost);
            foreach ($glossy->prices as $tiers) {
                $this->assertCount(6, $tiers);
                foreach ($tiers as $price) {
                    $this->assertNotNull($price);
                    $this->assertSame((int) $glossy->id, (int) $price->digital_lamination_production_cost_detail_id);
                }
            }
        }
        $html = $view->render();
        $this->assertStringContainsString('Rp316,70', $html);
        $this->assertStringContainsString('Rp1.816,70', $html);
        $this->assertStringContainsString('Rp3.633,40', $html);
        $this->assertStringNotContainsString('>Ukuran<', $html);
        $this->assertStringContainsString('aria-controls="category-content-digital-1"', $html);
        $this->assertStringContainsString(route('admin_pc_finishing_laminations'), $html);
        $dom = new \DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new \DOMXPath($dom);
        $rows = $xpath->query('//table[contains(@class, "details-digital-lamination-table")]/tbody/tr');
        $this->assertSame(4, $rows->length);
        foreach ($rows as $row) {
            $this->assertSame(30, $xpath->query('./td', $row)->length);
        }
        $this->assertSame($savedPrices, DB::table('m_digital_lamination_production_cost_prices')->get()->toJson());
    }

    private function storeIdenticalCategoryDetails(): int
    {
        $sourceRuleId = DB::table('m_digital_lamination_pricing_rules')
            ->where('location_id', 1)->where('category_id', 1)->value('id');
        $targetRuleId = DB::table('m_digital_lamination_pricing_rules')
            ->where('location_id', 1)->where('category_id', 2)->value('id');
        foreach (DB::table('m_digital_lamination_pricing_rule_details')->where('digital_lamination_pricing_rule_id', $sourceRuleId)->get() as $rule) {
            DB::table('m_digital_lamination_pricing_rule_details')
                ->where('digital_lamination_pricing_rule_id', $targetRuleId)
                ->where('price_type', $rule->price_type)->where('print_type', $rule->print_type)
                ->where('quantity_tier', $rule->quantity_tier)
                ->update(['markup_percentage' => $rule->markup_percentage, 'rounding_value' => $rule->rounding_value]);
        }
        $configuration = $this->configuration();
        $configuration['category_ids'] = [1, 2];
        $configuration['items'][] = ['lamination_id' => 2, 'unit_price' => '316,70', 'click_price' => '1.500,00'];
        $this->store([$configuration]);

        return (int) DB::table('m_digital_lamination_production_costs')->value('id');
    }

    public function test_identical_category_configurations_render_one_table_without_modifying_saved_data(): void
    {
        $id = $this->storeIdenticalCategoryDetails();
        $snapshots = [];
        foreach (['costs', 'cost_details', 'cost_prices'] as $suffix) {
            $table = 'm_digital_lamination_production_' . $suffix;
            $snapshots[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }
        $view = app(ProductionCostFinishingLaminationCT::class)->detailsDigital(base64_encode((string) $id));
        $categories = $view->getData()['categories'];
        $this->assertCount(1, $categories);
        $this->assertSame(['Category A', 'Category B'], $categories[0]->category_names->all());
        $this->assertCount(2, $categories[0]->details);
        $html = $view->render();
        $this->assertSame(1, substr_count($html, '<table class="details-digital-table details-digital-lamination-table">'));
        $this->assertStringContainsString('2 Kategori · Konfigurasi laminasi sama', $html);
        $this->assertSame(2, substr_count($html, '<span class="details-lamination-category-chip">'));
        $this->assertStringContainsString('<span class="details-lamination-category-chip">Category A</span>', $html);
        $this->assertStringContainsString('<span class="details-lamination-category-chip">Category B</span>', $html);
        foreach ($snapshots as $table => $snapshot) {
            $this->assertSame($snapshot, DB::table($table)->orderBy('id')->get()->toJson());
        }
    }

    public static function differentDetailConfigurations(): array
    {
        return [
            'different item set' => ['item'],
            'same name but different lamination master' => ['lamination_id'],
            'unit cost' => ['paper_cost'],
            'click cost' => ['click_cost'],
            'single cost' => ['single_print_cost'],
            'double cost' => ['double_print_cost'],
            'selling price' => ['selling_price'],
            'markup' => ['markup_percentage'],
            'rounding' => ['rounding_value'],
            'minimum quantity' => ['minimum_quantity'],
            'maximum quantity' => ['maximum_quantity'],
            'missing tier' => ['missing_price'],
        ];
    }

    #[DataProvider('differentDetailConfigurations')]
    public function test_categories_with_any_different_configuration_are_not_combined(string $field): void
    {
        $id = $this->storeIdenticalCategoryDetails();
        $detail = DB::table('m_digital_lamination_production_cost_details')->where('category_id', 2)->first();
        if ($field === 'item') {
            DB::table('m_digital_lamination_production_cost_details')->where('id', $detail->id)->delete();
        } elseif ($field === 'lamination_id') {
            DB::table('m_laminations')->insert(['id' => 3, 'name' => 'Glossy']);
            DB::table('m_digital_lamination_production_cost_details')->where('id', $detail->id)->update(['lamination_id' => 3]);
        } elseif (in_array($field, ['paper_cost', 'click_cost', 'single_print_cost', 'double_print_cost'], true)) {
            DB::table('m_digital_lamination_production_cost_details')->where('id', $detail->id)->update([$field => 123.45]);
        } else {
            $query = DB::table('m_digital_lamination_production_cost_prices')
                ->where('digital_lamination_production_cost_detail_id', $detail->id)
                ->where('price_type', 'division')->where('print_type', 'double')->where('quantity_tier', 'tier_500');
            if ($field === 'missing_price') {
                $query->delete();
            } else {
                $query->update([$field => in_array($field, ['minimum_quantity', 'maximum_quantity'], true) ? 999 : 123.45]);
            }
        }
        $view = app(ProductionCostFinishingLaminationCT::class)->detailsDigital(base64_encode((string) $id));
        $this->assertCount(2, $view->getData()['categories']);
        $this->assertSame(2, substr_count($view->render(), '<table class="details-digital-table details-digital-lamination-table">'));
        $rows = (new \App\Exports\DigitalLaminationProductionCostsExport())->array();
        $tableHeaders = array_filter($rows, fn($row) => $row[0] === 'No');
        $this->assertCount(2, $tableHeaders);
        $categoryRows = array_values(array_filter($rows, fn($row) => $row[0] === 'KATEGORI'));
        $this->assertSame(['Category A', 'Category B'], array_column($categoryRows, 1));
    }

    public function test_identical_and_different_categories_can_render_together_with_long_escaped_names(): void
    {
        $id = $this->storeIdenticalCategoryDetails();
        $longName = str_repeat('Long category name ', 15) . '<script>alert(1)</script>';
        DB::table('m_categories')->where('id', 2)->update(['name' => $longName]);
        DB::table('m_categories')->insert(['id' => 3, 'name' => 'Category C']);
        $this->seedRules(1, 3, null);
        $configuration = $this->configuration();
        $configuration['category_ids'] = [3];
        $this->store([$configuration]);
        $view = app(ProductionCostFinishingLaminationCT::class)->detailsDigital(base64_encode((string) $id));
        $groups = $view->getData()['categories'];
        $this->assertCount(2, $groups);
        $this->assertSame(['Category A', $longName], $groups[0]->category_names->all());
        $this->assertSame(['Category C'], $groups[1]->category_names->all());
        $html = $view->render();
        $this->assertStringContainsString(e($longName), $html);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('details-lamination-category', $html);
        $rows = (new \App\Exports\DigitalLaminationProductionCostsExport())->array();
        $this->assertCount(2, array_filter($rows, fn($row) => $row[0] === 'No'));
        $this->assertCount(3, array_filter($rows, fn($row) => is_int($row[0])));
        $this->assertSame(['Category A, ' . $longName, 'Category C'], array_column(
            array_values(array_filter($rows, fn($row) => $row[0] === 'KATEGORI')), 1
        ));
    }

    public function test_identical_export_categories_share_one_table_with_one_comma_separated_header_in_xlsx(): void
    {
        $this->storeIdenticalCategoryDetails();
        $snapshots = [];
        foreach (['costs', 'cost_details', 'cost_prices'] as $suffix) {
            $table = 'm_digital_lamination_production_' . $suffix;
            $snapshots[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }
        $export = new \App\Exports\DigitalLaminationProductionCostsExport();
        $rows = $export->array();
        $this->assertCount(1, array_filter($rows, fn($row) => $row[0] === 'No'));
        $this->assertCount(2, array_filter($rows, fn($row) => is_int($row[0])));
        $categoryIndices = array_keys(array_filter($rows, fn($row) => $row[0] === 'KATEGORI'));
        $this->assertCount(1, $categoryIndices);
        $this->assertSame('Category A, Category B', $rows[$categoryIndices[0]][1]);
        $this->assertSame($rows, $export->array());
        $path = tempnam(sys_get_temp_dir(), 'lamination-grouped-export-');
        try {
            file_put_contents($path, \Maatwebsite\Excel\Facades\Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX));
            \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder());
            $book = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $sheet = $book->getActiveSheet();
            foreach ($categoryIndices as $index) {
                $rowNumber = $index + 1;
                $this->assertSame($rows[$index][1], $sheet->getCell("B{$rowNumber}")->getValue());
                $this->assertContains("B{$rowNumber}:AD{$rowNumber}", $sheet->getMergeCells());
            }
            foreach ($rows as $index => $row) {
                if (!is_int($row[0])) {
                    continue;
                }
                for ($column = 2; $column < 30; $column++) {
                    $cell = $sheet->getCell(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column + 1) . ($index + 1));
                    $this->assertSame('n', $cell->getDataType());
                    $this->assertEqualsWithDelta($row[$column], $cell->getValue(), 0.000001);
                    $this->assertSame('"Rp"#,##0.00', $cell->getStyle()->getNumberFormat()->getFormatCode());
                }
            }
            $book->disconnectWorksheets();
        } finally {
            unlink($path);
        }
        foreach ($snapshots as $table => $snapshot) {
            $this->assertSame($snapshot, DB::table($table)->orderBy('id')->get()->toJson());
        }
    }

    public function test_export_grouping_does_not_merge_different_location_or_vendor_headers(): void
    {
        $configuration = $this->configuration();
        $configuration['location_ids'] = [1, 2];
        $this->store([$configuration]);
        $configuration['location_ids'] = [3];
        foreach ([1, 2] as $vendor) {
            $configuration['vendor_id'] = $vendor;
            $this->store([$configuration]);
        }
        $rows = (new \App\Exports\DigitalLaminationProductionCostsExport())->array();
        $this->assertCount(4, array_filter($rows, fn($row) => $row[0] === 'No'));
        $this->assertCount(4, array_filter($rows, fn($row) => is_int($row[0])));
        $this->assertSame(['-', 'Vendor A', 'Vendor B', '-'], array_column(
            array_values(array_filter($rows, fn($row) => $row[0] === 'VENDOR')), 1
        ));
    }

    public function test_digital_lamination_export_writes_exact_numeric_prices_and_scope_to_xlsx(): void
    {
        $configuration = $this->configuration();
        $configuration['category_ids'] = [1, 2];
        $configuration['location_ids'] = [1, 2];
        $configuration['items'][0]['unit_price'] = '316,70';
        $configuration['items'][0]['click_price'] = '1.500,00';
        $this->store([$configuration]);
        $configuration['location_ids'] = [3];
        $configuration['vendor_id'] = 1;
        $this->store([$configuration]);
        $configuration['vendor_id'] = 2;
        $this->store([$configuration]);
        DB::table('m_laminations')->where('id', 1)->update(['name' => '=1+1', 'status' => 'Inactive']);
        $savedPrices = DB::table('m_digital_lamination_production_cost_prices')->get()->toJson();
        DB::table('m_digital_lamination_pricing_rule_details')->update(['markup_percentage' => 99]);
        $export = new \App\Exports\DigitalLaminationProductionCostsExport();
        $rows = $export->array();
        $dataRows = array_filter($rows, fn($row) => is_int($row[0]));
        $this->assertCount(8, $dataRows);
        foreach ($dataRows as $row) {
            $this->assertCount(30, $row);
            $this->assertSame('=1+1', $row[1]);
            $this->assertSame(316.7, $row[2]);
            $this->assertSame(1500.0, $row[3]);
            $this->assertSame(1816.7, $row[4]);
            $this->assertSame(3633.4, $row[5]);
        }
        $this->assertContains('Vendor A', array_column($rows, 1));
        $this->assertContains('Vendor B', array_column($rows, 1));
        $this->assertContains('Purwakarta', array_column($rows, 1));
        $this->assertContains('Graha', array_column($rows, 1));
        $this->assertContains('Category A', array_column($rows, 1));
        $this->assertContains('Category B', array_column($rows, 1));
        $this->assertSame($rows, $export->array());
        $path = tempnam(sys_get_temp_dir(), 'lamination-export-');
        try {
            file_put_contents($path, \Maatwebsite\Excel\Facades\Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX));
            \PhpOffice\PhpSpreadsheet\Cell\Cell::setValueBinder(new \PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder());
            $book = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $this->assertSame(1, $book->getSheetCount());
            $sheet = $book->getActiveSheet();
            $this->assertSame('Digital Print A3+', $sheet->getTitle());
            $this->assertSame('AD', $sheet->getHighestColumn());
            $found = 0;
            foreach ($rows as $index => $row) {
                if (!is_int($row[0])) {
                    continue;
                }
                $excelRow = $index + 1;
                $this->assertSame('s', $sheet->getCell("B{$excelRow}")->getDataType());
                $this->assertSame('=1+1', $sheet->getCell("B{$excelRow}")->getValue());
                for ($column = 2; $column < 30; $column++) {
                    $cell = $sheet->getCell(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column + 1) . $excelRow);
                    $this->assertSame('n', $cell->getDataType());
                    $this->assertEqualsWithDelta($row[$column], $cell->getValue(), 0.000001);
                    $this->assertSame('"Rp"#,##0.00', $cell->getStyle()->getNumberFormat()->getFormatCode());
                }
                $found++;
            }
            $this->assertSame(8, $found);
            $book->disconnectWorksheets();
        } finally {
            unlink($path);
        }
        $this->assertSame($savedPrices, DB::table('m_digital_lamination_production_cost_prices')->get()->toJson());
    }

    public function test_digital_export_excludes_non_digital_headers_and_handles_empty_data(): void
    {
        $this->store([$this->configuration()]);
        $id = DB::table('m_digital_lamination_production_costs')->value('id');
        DB::table('m_digital_lamination_production_cost_prices')
            ->where('price_type', 'general')->where('print_type', 'single')->where('quantity_tier', 'tier_1')->delete();
        $export = new \App\Exports\DigitalLaminationProductionCostsExport();
        $dataRows = array_values(array_filter($export->array(), fn($row) => is_int($row[0])));
        $this->assertSame('-', $dataRows[0][6]);
        $this->assertSame(24, count(array_slice($dataRows[0], 6)));
        DB::table('m_digital_lamination_production_costs')->where('id', $id)->update(['engine_id' => 2]);
        $rows = $export->array();
        $this->assertCount(3, $rows);
        $this->assertSame('Belum ada data ongkos produksi laminasi Digital Print A3+.', $rows[2][0]);
        $this->assertNotEmpty(\Maatwebsite\Excel\Facades\Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX));
    }

    public function test_export_dispatch_follows_active_engine_and_large_format_sheets_are_filtered(): void
    {
        $sheets = (new \App\Exports\ProductionCostFinishingLaminationsExport())->sheets();
        $this->assertCount(1, $sheets);
        $this->assertSame('Large Format', $sheets[0]->title());
        \Maatwebsite\Excel\Facades\Excel::fake();
        $controller = app(ProductionCostFinishingLaminationCT::class);
        $controller->export(Request::create('/test', 'GET', ['pricing_engine' => 'digital_print_a3']));
        \Maatwebsite\Excel\Facades\Excel::assertDownloaded('production_cost_lamination_digital_print_a3.xlsx', fn($export) => $export instanceof \App\Exports\DigitalLaminationProductionCostsExport);
        foreach ([[], ['pricing_engine' => 'large_format']] as $query) {
            $controller->export(Request::create('/test', 'GET', $query));
            \Maatwebsite\Excel\Facades\Excel::assertDownloaded('production_cost_finishing_laminations.xlsx', fn($export) => $export instanceof \App\Exports\ProductionCostFinishingLaminationsExport);
        }
        $html = view('admin.production-cost-finishing-lamination.index')->render();
        $this->assertStringContainsString("url.searchParams.set('pricing_engine', engineFilter.value)", $html);
        $this->assertStringNotContainsString('Export ongkos produksi laminasi Digital Print A3+ belum tersedia.', $html);
    }

    public function test_export_rejects_unknown_engine(): void
    {
        $this->expectException(ValidationException::class);
        app(ProductionCostFinishingLaminationCT::class)->export(Request::create('/test', 'GET', ['pricing_engine' => 'unknown']));
    }

    public function test_digital_details_preserve_vendor_and_header_scope(): void
    {
        $configuration = $this->configuration();
        $configuration['location_ids'] = [3];
        $configuration['vendor_id'] = 1;
        $this->store([$configuration]);
        $id = DB::table('m_digital_lamination_production_costs')->value('id');
        $configuration['vendor_id'] = 2;
        $configuration['category_ids'] = [2];
        $configuration['items'][0]['lamination_id'] = 2;
        $this->store([$configuration]);
        $view = app(ProductionCostFinishingLaminationCT::class)->detailsDigital(base64_encode((string) $id));
        $data = $view->getData();
        $this->assertSame('Outsourcing', $data['productionCost']->location_name);
        $this->assertSame('Vendor A', $data['productionCost']->vendor_name);
        $this->assertCount(1, $data['categories']);
        $this->assertSame('Category A', $data['categories'][0]->category_name);
        $this->assertSame('Glossy', $data['categories'][0]->details[0]->lamination_name);
        $html = $view->render();
        $this->assertStringContainsString('vendor-content-digital', $html);
        $this->assertStringNotContainsString('Vendor B', $html);
        $this->assertStringNotContainsString('Category B', $html);
    }

    public function test_digital_details_handle_empty_header_and_missing_tier(): void
    {
        $this->store([$this->configuration()]);
        $id = DB::table('m_digital_lamination_production_costs')->value('id');
        DB::table('m_digital_lamination_production_cost_prices')
            ->where('price_type', 'general')->where('print_type', 'single')
            ->where('quantity_tier', 'tier_1')->delete();
        $controller = app(ProductionCostFinishingLaminationCT::class);
        $view = $controller->detailsDigital(base64_encode((string) $id));
        $category = $view->getData()['categories'][0];
        $this->assertNull($category->details[0]->prices['general_single']['tier_1']);
        $this->assertSame('-', $category->markups['general_single']['tier_1']);
        $this->assertStringNotContainsString('Rp0,00', $view->render());
        DB::table('m_digital_lamination_production_cost_details')->delete();
        $view = $controller->detailsDigital(base64_encode((string) $id));
        $this->assertCount(0, $view->getData()['categories']);
        $this->assertStringContainsString('Belum ada laminasi.', $view->render());
    }

    public function test_digital_details_report_multiple_saved_markups_without_overwriting_them(): void
    {
        $this->store([$this->configuration()]);
        DB::table('m_digital_lamination_pricing_rule_details')
            ->where('price_type', 'general')->update(['markup_percentage' => 50.25]);
        $configuration = $this->configuration();
        $configuration['items'][0]['lamination_id'] = 2;
        $this->store([$configuration]);
        $id = DB::table('m_digital_lamination_production_costs')->value('id');
        $view = app(ProductionCostFinishingLaminationCT::class)->detailsDigital(base64_encode((string) $id));
        $this->assertSame('50,25% / 40%', $view->getData()['categories'][0]->markups['general_single']['tier_1']);
        $this->assertStringContainsString('title="Markup 50,25%"', $view->render());
    }

    public function test_digital_details_reject_invalid_missing_and_non_digital_ids(): void
    {
        $this->store([$this->configuration()]);
        $id = DB::table('m_digital_lamination_production_costs')->value('id');
        DB::table('m_digital_lamination_production_costs')->where('id', $id)->update(['engine_id' => 2]);
        foreach (['bad', base64_encode('-1'), base64_encode('0'), base64_encode('9999'), base64_encode((string) $id)] as $encoded) {
            try {
                app(ProductionCostFinishingLaminationCT::class)->detailsDigital($encoded);
                $this->fail('Invalid details ID must return 404.');
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                $this->assertSame(404, $e->getStatusCode());
            }
        }
    }

    public function test_digital_details_route_and_index_action_are_wired_separately(): void
    {
        $route = app('router')->getRoutes()->getByName('admin_details_pc_finishing_lamination_digital');
        $this->assertNotNull($route);
        $this->assertSame(ProductionCostFinishingLaminationCT::class . '@detailsDigital', $route->getActionName());
        $this->assertContains('GET', $route->methods());
        $html = view('admin.production-cost-finishing-lamination.index')->render();
        $this->assertStringContainsString(json_encode(route('admin_details_pc_finishing_lamination_digital', ['id' => '__ID__'])), $html);
        $this->assertStringContainsString('Detail ongkos produksi laminasi Digital', $html);
        $this->assertStringNotContainsString('Detail, hapus, export', $html);
    }

    public function test_digital_delete_cascades_only_target_header_and_preserves_material_and_masters(): void
    {
        Schema::create('m_materials', function (Blueprint $table) {
            $table->id();
            $table->string('material_name');
            $table->foreignId('category_id')->constrained('m_categories');
        });
        foreach ([
            '2026_10_10_092121_create_m_digital_pricing_rules_table.php',
            '2026_10_10_092633_create_m_digital_pricing_rule_details_table.php',
            '2026_10_10_094324_modify_to_m_digital_pricing_rules_table.php',
            '2026_10_12_093132_create_m_digital_production_costs_table.php',
            '2026_10_12_093200_create_m_digital_production_cost_details_table.php',
            '2026_10_12_093224_create_m_digital_production_cost_prices_table.php',
        ] as $file) {
            (require database_path('migrations\\' . $file))->up();
        }
        DB::table('m_materials')->insert(['id' => 1, 'material_name' => 'Art Paper', 'category_id' => 1]);
        $materialRuleId = DB::table('m_digital_pricing_rules')->insertGetId([
            'engine_id' => 1, 'location_id' => 3, 'vendor_id' => 1, 'category_id' => 1,
            'created_by' => 'Test', 'created_at' => now(),
        ]);
        $materialRuleDetailId = DB::table('m_digital_pricing_rule_details')->insertGetId([
            'digital_pricing_rule_id' => $materialRuleId, 'price_type' => 'general',
            'print_type' => 'double', 'quantity_tier' => 'tier_1', 'minimum_quantity' => 1,
            'maximum_quantity' => 1, 'markup_percentage' => 40, 'rounding_value' => 100,
            'status' => 'Active', 'created_by' => 'Test', 'created_at' => now(),
        ]);
        $materialHeaderId = DB::table('m_digital_production_costs')->insertGetId([
            'engine_id' => 1, 'location_id' => 3, 'vendor_id' => 1,
            'created_by' => 'Test', 'created_at' => now(),
        ]);
        $materialDetailId = DB::table('m_digital_production_cost_details')->insertGetId([
            'digital_production_cost_id' => $materialHeaderId, 'material_id' => 1,
            'paper_cost' => 1000, 'click_cost' => 200, 'single_print_cost' => 1200,
            'double_print_cost' => 1400, 'created_by' => 'Test', 'created_at' => now(),
        ]);
        DB::table('m_digital_production_cost_prices')->insert([
            'digital_production_cost_detail_id' => $materialDetailId,
            'digital_pricing_rule_detail_id' => $materialRuleDetailId, 'price_type' => 'general',
            'print_type' => 'double', 'quantity_tier' => 'tier_1', 'minimum_quantity' => 1,
            'maximum_quantity' => 1, 'markup_percentage' => 40, 'rounding_value' => 100,
            'selling_price' => 2000, 'created_by' => 'Test', 'created_at' => now(),
        ]);
        $configuration = $this->configuration();
        $configuration['location_ids'] = [3];
        $configuration['vendor_id'] = 1;
        $configuration['category_ids'] = [1, 2];
        $configuration['items'][] = ['lamination_id' => 2, 'unit_price' => '316,70', 'click_price' => '1.500,00'];
        $this->store([$configuration]);
        $targetId = DB::table('m_digital_lamination_production_costs')->value('id');
        $this->assertSame($materialHeaderId, $targetId);
        $targetDetails = DB::table('m_digital_lamination_production_cost_details')
            ->where('digital_lamination_production_cost_id', $targetId)->pluck('id');
        $this->assertCount(4, $targetDetails);
        $configuration['vendor_id'] = 2;
        $this->store([$configuration]);
        $configuration['location_ids'] = [1, 2];
        $configuration['vendor_id'] = null;
        $this->store([$configuration]);

        $snapshots = [];
        foreach ([
            'm_engines', 'm_locations', 'm_vendors', 'm_categories', 'm_laminations', 'm_materials',
            'm_digital_lamination_pricing_rules', 'm_digital_lamination_pricing_rule_details',
            'm_digital_pricing_rules', 'm_digital_pricing_rule_details',
            'm_digital_production_costs', 'm_digital_production_cost_details', 'm_digital_production_cost_prices',
        ] as $table) {
            $snapshots[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }
        $otherHeaders = DB::table('m_digital_lamination_production_costs')->where('id', '!=', $targetId)->orderBy('id')->get();
        $otherDetails = DB::table('m_digital_lamination_production_cost_details')
            ->whereNotIn('id', $targetDetails)->orderBy('id')->get();
        $otherPrices = DB::table('m_digital_lamination_production_cost_prices')
            ->whereNotIn('digital_lamination_production_cost_detail_id', $targetDetails)->orderBy('id')->get();

        $response = app(ProductionCostFinishingLaminationCT::class)->destroyDigital(base64_encode((string) $targetId));
        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue($response->getData(true)['success']);
        $this->assertStringContainsString('Outsourcing - Vendor A', $response->getData(true)['message']);
        $this->assertFalse(DB::table('m_digital_lamination_production_costs')->where('id', $targetId)->exists());
        $this->assertSame(0, DB::table('m_digital_lamination_production_cost_details')->whereIn('id', $targetDetails)->count());
        $this->assertSame(0, DB::table('m_digital_lamination_production_cost_prices')
            ->whereIn('digital_lamination_production_cost_detail_id', $targetDetails)->count());
        $this->assertSame($otherHeaders->toJson(), DB::table('m_digital_lamination_production_costs')->orderBy('id')->get()->toJson());
        $this->assertSame($otherDetails->toJson(), DB::table('m_digital_lamination_production_cost_details')->orderBy('id')->get()->toJson());
        $this->assertSame($otherPrices->toJson(), DB::table('m_digital_lamination_production_cost_prices')->orderBy('id')->get()->toJson());
        foreach ($snapshots as $table => $snapshot) {
            $this->assertSame($snapshot, DB::table($table)->orderBy('id')->get()->toJson(), $table . ' must not change.');
        }
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_digital_delete_rejects_invalid_missing_and_non_digital_ids_without_changes(): void
    {
        $this->store([$this->configuration()]);
        $id = DB::table('m_digital_lamination_production_costs')->value('id');
        DB::table('m_digital_lamination_production_costs')->where('id', $id)->update(['engine_id' => 2]);
        $snapshots = [];
        foreach (['costs', 'cost_details', 'cost_prices'] as $suffix) {
            $table = 'm_digital_lamination_production_' . $suffix;
            $snapshots[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }
        foreach (['bad', base64_encode('-1'), base64_encode('0'), base64_encode('9999'), base64_encode((string) $id)] as $encoded) {
            try {
                app(ProductionCostFinishingLaminationCT::class)->destroyDigital($encoded);
                $this->fail('Invalid delete must return 404.');
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                $this->assertSame(404, $e->getStatusCode());
            }
        }
        foreach ($snapshots as $table => $snapshot) {
            $this->assertSame($snapshot, DB::table($table)->orderBy('id')->get()->toJson());
        }
    }

    public function test_digital_delete_rolls_back_cascades_on_database_failure(): void
    {
        $this->store([$this->configuration()]);
        $id = DB::table('m_digital_lamination_production_costs')->value('id');
        $snapshots = [];
        foreach (['costs', 'cost_details', 'cost_prices'] as $suffix) {
            $table = 'm_digital_lamination_production_' . $suffix;
            $snapshots[$table] = DB::table($table)->orderBy('id')->get()->toJson();
        }
        DB::unprepared("CREATE TRIGGER fail_lamination_delete AFTER DELETE ON m_digital_lamination_production_costs
            BEGIN SELECT RAISE(ABORT, 'Simulated deletion failure'); END");
        $response = app(ProductionCostFinishingLaminationCT::class)->destroyDigital(base64_encode((string) $id));
        $this->assertSame(500, $response->getStatusCode());
        $this->assertFalse($response->getData(true)['success']);
        $this->assertStringNotContainsString('Simulated', $response->getData(true)['message']);
        foreach ($snapshots as $table => $snapshot) {
            $this->assertSame($snapshot, DB::table($table)->orderBy('id')->get()->toJson());
        }
        $this->assertSame(0, DB::transactionLevel());
    }

    public function test_digital_delete_handles_empty_header_and_repeated_delete(): void
    {
        $this->store([$this->configuration()]);
        $id = DB::table('m_digital_lamination_production_costs')->value('id');
        DB::table('m_digital_lamination_production_cost_details')->delete();
        $controller = app(ProductionCostFinishingLaminationCT::class);
        $this->assertTrue($controller->destroyDigital(base64_encode((string) $id))->getData(true)['success']);
        $this->assertEmptyCosts();
        try {
            $controller->destroyDigital(base64_encode((string) $id));
            $this->fail('Repeated deletion must return 404.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }
    }

    public function test_digital_delete_route_and_confirmation_use_separate_scope(): void
    {
        $route = app('router')->getRoutes()->getByName('admin_delete_pc_finishing_lamination_digital');
        $this->assertNotNull($route);
        $this->assertSame(ProductionCostFinishingLaminationCT::class . '@destroyDigital', $route->getActionName());
        $this->assertSame(['DELETE'], $route->methods());
        $this->assertContains('auth', $route->gatherMiddleware());
        $html = view('admin.production-cost-finishing-lamination.index')->render();
        $this->assertStringContainsString(json_encode(route('admin_delete_pc_finishing_lamination_digital', ['id' => '__ID__'])), $html);
        $this->assertStringContainsString('data-pricing-engine="digital_print_a3"', $html);
        $this->assertStringContainsString('Master dan ongkos material tidak ikut dihapus.', $html);
        $this->assertStringContainsString('konfigurasi production cost laminasi untuk mesin ini?', $html);
        $this->assertStringNotContainsString('Hapus, export, template', $html);
    }

    public function test_outsourcing_edit_preserves_vendor_scope_and_other_vendor_data(): void
    {
        $configuration = $this->configuration();
        $configuration['location_ids'] = [3];
        $configuration['vendor_id'] = 1;
        $this->store([$configuration]);
        $configuration['vendor_id'] = 2;
        $this->store([$configuration]);
        $id = DB::table('m_digital_lamination_production_costs')->where('vendor_id', 1)->value('id');
        $otherId = DB::table('m_digital_lamination_production_costs')->where('vendor_id', 2)->value('id');
        $otherBefore = DB::table('m_digital_lamination_production_cost_details')
            ->where('digital_lamination_production_cost_id', $otherId)->get()->toJson();
        $configuration['items'][0]['unit_price'] = '316,70';
        $this->assertSame('success', $this->editRequest([$configuration], $id)->getData(true)['status']);
        $this->assertSame(1, (int) DB::table('m_digital_lamination_production_costs')->where('id', $id)->value('vendor_id'));
        $detail = DB::table('m_digital_lamination_production_cost_details')
            ->where('digital_lamination_production_cost_id', $id)->first();
        $price = DB::table('m_digital_lamination_production_cost_prices')
            ->where('digital_lamination_production_cost_detail_id', $detail->id)->first();
        $rule = DB::table('m_digital_lamination_pricing_rules as r')
            ->join('m_digital_lamination_pricing_rule_details as d', 'r.id', '=', 'd.digital_lamination_pricing_rule_id')
            ->where('d.id', $price->digital_lamination_pricing_rule_detail_id)->first(['r.vendor_id']);
        $this->assertSame(1, (int) $rule->vendor_id);
        $this->assertSame($otherBefore, DB::table('m_digital_lamination_production_cost_details')
            ->where('digital_lamination_production_cost_id', $otherId)->get()->toJson());
    }
}

<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\EstimationCT;
use App\Http\Controllers\user\CatalogCT;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CatalogDiscountTest extends TestCase
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
        Carbon::setTestNow(Carbon::parse('2026-10-15 00:30:00', 'Asia/Jakarta'));

        Schema::create('m_engines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status');
        });
        Schema::create('m_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('m_display_products', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
            $table->unsignedBigInteger('category_id');
            $table->string('status');
        });
        Schema::create('m_display_product_configurations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('display_product_id');
            $table->unsignedBigInteger('engine_id');
        });
        Schema::create('m_display_product_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('display_product_id');
            $table->boolean('is_primary');
        });
        Schema::create('m_sample_galleries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('engine_id');
            $table->unsignedBigInteger('category_id');
            $table->boolean('is_primary');
            $table->integer('sort_order');
        });
        foreach (['m_locations', 'm_vendors'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('status');
            });
        }
        foreach ([
            '2026_10_14_155722_create_m_discounts_table.php',
            '2026_10_14_155746_create_m_discount_scopes_table.php',
            '2026_10_14_175203_add_code_to_m_discounts_table.php',
        ] as $file) {
            (require database_path('migrations\\' . $file))->up();
        }
        DB::table('m_engines')->insert([
            ['id' => 1, 'name' => 'Large Format', 'status' => 'Active'],
            ['id' => 2, 'name' => 'Digital Print A3+', 'status' => 'Active'],
        ]);
        DB::table('m_categories')->insert([
            ['id' => 1, 'name' => 'Display'],
            ['id' => 2, 'name' => 'Sticker'],
        ]);
        DB::table('m_display_products')->insert([
            'id' => 1, 'display_name' => 'Standing Banner', 'category_id' => 1, 'status' => 'Active',
        ]);
        DB::table('m_display_product_images')->insert([
            'id' => 1, 'display_product_id' => 1, 'is_primary' => true,
        ]);
        DB::table('m_sample_galleries')->insert([
            ['id' => 1, 'engine_id' => 1, 'category_id' => 2, 'is_primary' => true, 'sort_order' => 0],
            ['id' => 2, 'engine_id' => 2, 'category_id' => 2, 'is_primary' => true, 'sort_order' => 0],
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function promo(array $overrides = [], int $engineId = 1, int $categoryId = 1): int
    {
        $id = DB::table('m_discounts')->insertGetId(array_replace([
            'name' => 'Promo Produk',
            'code' => 'PROMO-' . (DB::table('m_discounts')->count() + 1),
            'discount_percentage' => 12.5,
            'starts_at' => '2026-10-15 00:00:00',
            'ends_at' => '2026-10-31 23:59:59',
            'status' => 'Active',
            'created_by' => 'Admin',
            'created_at' => now(),
        ], $overrides));
        DB::table('m_discount_scopes')->insert([
            'discount_id' => $id, 'engine_id' => $engineId, 'category_id' => $categoryId,
        ]);

        return $id;
    }

    private function details(array $query = [])
    {
        return (new CatalogCT())->productDetails(Request::create('/product-details', 'GET',
            array_replace(['type' => 'display', 'id' => 1], $query)));
    }

    public function test_display_only_shows_active_current_matching_promos_using_wib(): void
    {
        $id = $this->promo();
        $this->promo(['status' => 'Inactive']);
        $this->promo(['starts_at' => '2026-10-15 00:31:00']);
        $this->promo(['ends_at' => '2026-10-15 00:29:59']);
        $this->promo([], 2);
        $this->promo([], 1, 2);

        $view = $this->details();
        $this->assertSame([$id], $view->getData()['activeDiscounts']->pluck('id')->all());
        $html = $view->render();
        $this->assertStringContainsString('Kode Promo:', $html);
        $this->assertStringContainsString('PROMO-1', $html);
        $this->assertMatchesRegularExpression('/Diskon\s+12,5%/', $html);
        $this->assertStringContainsString('15 Okt 2026, 00:00', $html);
        $this->assertStringContainsString('Masukkan kode promo pada estimator', $html);
        $this->assertStringContainsString('id="promo_code"', $html);
        $this->assertStringNotContainsString('name="discount_type"', $html);
        $this->assertStringNotContainsString('PROMO-2', $html);
    }

    public function test_display_configuration_engine_takes_precedence_over_large_format_fallback(): void
    {
        $this->promo();
        $id = $this->promo([], 2);
        DB::table('m_display_product_configurations')->insert(['display_product_id' => 1, 'engine_id' => 2]);

        $this->assertSame([$id], $this->details()->getData()['activeDiscounts']->pluck('id')->all());
    }

    public function test_gallery_requires_specific_matching_engine(): void
    {
        $id = $this->promo([], 1, 2);
        $this->promo([], 2, 2);

        $this->assertSame([$id], $this->details([
            'type' => 'gallery', 'category_id' => 2, 'engine_id' => 1,
        ])->getData()['activeDiscounts']->pluck('id')->all());
        $this->assertTrue($this->details(['type' => 'gallery', 'category_id' => 2])
            ->getData()['activeDiscounts']->isEmpty());
    }

    public function test_period_boundaries_are_inclusive_and_scope_matches_do_not_duplicate_promos(): void
    {
        $start = $this->promo(['starts_at' => '2026-10-15 00:30:00']);
        $end = $this->promo(['ends_at' => '2026-10-15 00:30:00']);
        DB::table('m_discount_scopes')->insert(['discount_id' => $end, 'engine_id' => 1, 'category_id' => 2]);

        $this->assertSame([$end, $start], $this->details()->getData()['activeDiscounts']->pluck('id')->all());
    }

    public function test_no_promo_does_not_render_promo_section(): void
    {
        $html = $this->details()->render();

        $this->assertStringNotContainsString('aria-label="Promo aktif produk"', $html);
        $this->assertStringContainsString('Standing Banner', $html);
    }

    private function mockCalculation(array $overrides = []): void
    {
        $this->mock(EstimationCT::class, function ($mock) use ($overrides) {
            $mock->shouldReceive('storeLF')->andReturnUsing(function (Request $request) use ($overrides) {
                $this->assertSame('none', $request->input('discount_type'));
                $this->assertSame(0, $request->input('discount_value'));

                return response()->json(['success' => true, 'data' => array_replace([
                    'material_name' => 'Banner',
                    'material_price_unit' => 'unit',
                    'material_price' => 100000,
                    'material_subtotal' => 100000,
                    'subtotal' => 125050.75,
                    'grand_total' => 125050.75,
                ], $overrides)]);
            });
        });
    }

    private function preview(array $overrides = [])
    {
        return $this->postJson(route('user_preview_lf_estimation'), array_replace([
            'qty' => 1, 'category_id' => 1,
            'discount_type' => 'percentage', 'discount_value' => 100,
            'engine_id' => 2,
        ], $overrides));
    }

    public function test_preview_applies_master_percentage_to_subtotal_and_ignores_client_discount(): void
    {
        $this->promo(['code' => 'PROMO-MASTER']);
        $this->mockCalculation();

        $this->preview(['promo_code' => ' promo-master ', 'discount_value' => 90])
            ->assertOk()->assertJsonPath('data.promo.code', 'PROMO-MASTER')
            ->assertJsonPath('data.promo.percentage', 12.5)
            ->assertJsonPath('data.discount_amount', 15631.34)
            ->assertJsonPath('data.rounding_amount', 580.59)
            ->assertJsonPath('data.grand_total', 110000);
    }

    public function test_preview_without_code_has_no_discount_even_when_manual_discount_is_forged(): void
    {
        $this->mockCalculation();
        $this->preview()->assertOk()->assertJsonPath('data.promo', null)
            ->assertJsonPath('data.discount_amount', 0)
            ->assertJsonPath('data.rounding_amount', 0)
            ->assertJsonPath('data.grand_total', 125050.75);
    }

    public function test_preview_without_promo_reports_existing_rounding_without_changing_total(): void
    {
        $this->mockCalculation(['subtotal' => 171600, 'grand_total' => 172000]);

        $this->preview()->assertOk()
            ->assertJsonPath('data.promo', null)
            ->assertJsonPath('data.discount_amount', 0)
            ->assertJsonPath('data.subtotal', 171600)
            ->assertJsonPath('data.rounding_amount', 400)
            ->assertJsonPath('data.grand_total', 172000);
    }

    public function test_preview_without_promo_reports_zero_rounding_for_exact_thousand(): void
    {
        $this->mockCalculation(['subtotal' => 172000, 'grand_total' => 172000]);

        $this->preview()->assertOk()
            ->assertJsonPath('data.rounding_amount', 0)
            ->assertJsonPath('data.grand_total', 172000);
    }

    public function test_promo_rounding_replaces_base_rounding_instead_of_adding_it_twice(): void
    {
        $this->promo(['code' => 'ROUND-ONCE', 'discount_percentage' => 5]);
        $this->mockCalculation(['subtotal' => 171600, 'grand_total' => 172000]);

        $this->preview(['promo_code' => 'ROUND-ONCE'])->assertOk()
            ->assertJsonPath('data.discount_amount', 8580)
            ->assertJsonPath('data.rounding_amount', 980)
            ->assertJsonPath('data.grand_total', 164000);
    }

    public function test_preview_rejects_invalid_inactive_out_of_period_or_wrong_scope_code(): void
    {
        $this->promo(['code' => 'INACTIVE', 'status' => 'Inactive']);
        $this->promo(['code' => 'EXPIRED', 'ends_at' => '2026-10-15 00:29:59']);
        $this->promo(['code' => 'FUTURE', 'starts_at' => '2026-10-15 00:30:01']);
        $this->promo(['code' => 'OTHER-ENGINE'], 2);
        $this->promo(['code' => 'OTHER-CATEGORY'], 1, 2);
        $this->mockCalculation();

        foreach (['UNKNOWN', 'INACTIVE', 'EXPIRED', 'FUTURE', 'OTHER-ENGINE', 'OTHER-CATEGORY'] as $code) {
            $this->preview(['promo_code' => $code])->assertUnprocessable()->assertJsonValidationErrors('promo_code');
        }
    }

    public function test_preview_validates_code_input_shape(): void
    {
        foreach (['AB', str_repeat('X', 31), 'BAD CODE', ['CODE'], 123] as $code) {
            $this->preview(['promo_code' => $code])->assertUnprocessable()->assertJsonValidationErrors('promo_code');
        }
    }

    public function test_preview_uses_validated_display_configuration_engine_not_client_engine(): void
    {
        $this->promo(['code' => 'DISPLAY-PROMO'], 2);
        DB::table('m_display_product_configurations')->insert([
            'id' => 7, 'display_product_id' => 1, 'engine_id' => 2,
        ]);
        $this->mockCalculation(['display_configuration_id' => 7]);

        $this->preview(['promo_code' => 'DISPLAY-PROMO', 'engine_id' => 1])
            ->assertOk()->assertJsonPath('data.promo.code', 'DISPLAY-PROMO');
    }

    public function test_preview_rechecks_deleted_and_expired_promo_and_allows_full_discount(): void
    {
        $id = $this->promo(['code' => 'FULL-PROMO', 'discount_percentage' => 100]);
        $this->mockCalculation();

        $this->preview(['promo_code' => 'FULL-PROMO'])->assertOk()
            ->assertJsonPath('data.discount_amount', 125050.75)->assertJsonPath('data.grand_total', 0)
            ->assertJsonPath('data.rounding_amount', 0);
        Carbon::setTestNow(Carbon::parse('2026-11-01 00:00:00', 'Asia/Jakarta'));
        $this->preview(['promo_code' => 'FULL-PROMO'])->assertUnprocessable()->assertJsonValidationErrors('promo_code');
        Carbon::setTestNow(Carbon::parse('2026-10-15 00:30:00', 'Asia/Jakarta'));
        DB::table('m_discounts')->where('id', $id)->delete();
        $this->preview(['promo_code' => 'FULL-PROMO'])->assertUnprocessable()->assertJsonValidationErrors('promo_code');
    }

    public function test_preview_reads_current_master_percentage_on_every_request(): void
    {
        $id = $this->promo(['code' => 'CURRENT-PROMO', 'discount_percentage' => 5]);
        $this->mockCalculation();

        $this->preview(['promo_code' => 'CURRENT-PROMO'])->assertOk()->assertJsonPath('data.promo.percentage', 5);
        DB::table('m_discounts')->where('id', $id)->update(['discount_percentage' => 10]);
        $this->preview(['promo_code' => 'CURRENT-PROMO'])->assertOk()->assertJsonPath('data.promo.percentage', 10)
            ->assertJsonPath('data.discount_amount', 12505.08)->assertJsonPath('data.grand_total', 113000)
            ->assertJsonPath('data.rounding_amount', 454.33);
    }

    public function test_failed_base_calculation_is_not_replaced_with_promo_success(): void
    {
        $this->mock(EstimationCT::class, function ($mock) {
            $mock->shouldReceive('storeLF')->once()->andReturn(response()->json([
                'success' => false, 'message' => 'Konfigurasi produk tidak tersedia.',
            ], 422));
        });

        $this->preview(['promo_code' => 'PROMO-MASTER'])->assertUnprocessable()
            ->assertJson(['success' => false, 'message' => 'Konfigurasi produk tidak tersedia.']);
    }

    public function test_promo_rounds_total_up_to_next_thousand_without_changing_discount(): void
    {
        $this->promo(['code' => 'ROUND-PROMO', 'discount_percentage' => 5]);
        $this->mockCalculation(['subtotal' => 205000, 'grand_total' => 205000]);

        $data = $this->preview(['promo_code' => 'ROUND-PROMO'])->assertOk()
            ->assertJsonPath('data.discount_amount', 10250)
            ->assertJsonPath('data.rounding_amount', 250)
            ->assertJsonPath('data.grand_total', 195000)->json('data');

        $this->assertEquals($data['subtotal'] - $data['discount_amount'] + $data['rounding_amount'], $data['grand_total']);
    }

    public function test_exact_thousand_after_promo_is_not_increased(): void
    {
        $this->promo(['code' => 'EXACT-PROMO', 'discount_percentage' => 5]);
        $this->mockCalculation(['subtotal' => 200000, 'grand_total' => 200000]);

        $this->preview(['promo_code' => 'EXACT-PROMO'])->assertOk()
            ->assertJsonPath('data.discount_amount', 10000)
            ->assertJsonPath('data.rounding_amount', 0)
            ->assertJsonPath('data.grand_total', 190000);
    }

    public function test_fractional_total_just_above_thousand_is_rounded_up(): void
    {
        $this->promo(['code' => 'CENT-PROMO', 'discount_percentage' => 50]);
        $this->mockCalculation(['subtotal' => 2000.02, 'grand_total' => 2000.02]);

        $this->preview(['promo_code' => 'CENT-PROMO'])->assertOk()
            ->assertJsonPath('data.discount_amount', 1000.01)
            ->assertJsonPath('data.rounding_amount', 999.99)
            ->assertJsonPath('data.grand_total', 2000);
    }
}

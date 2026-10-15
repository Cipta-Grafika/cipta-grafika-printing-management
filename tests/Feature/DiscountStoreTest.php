<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\DiscountCT;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class DiscountStoreTest extends TestCase
{
    private User $user;

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

        // 2026-10-14 17:30 UTC = 2026-10-15 00:30 WIB
        Carbon::setTestNow('2026-10-14 17:30:00');

        Schema::create('m_engines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status');
        });
        Schema::create('m_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        foreach ([
            'm_pricing_rules',
            'm_lamination_pricing_rules',
            'm_display_pricing_rules',
            'm_digital_pricing_rules',
            'm_digital_lamination_pricing_rules',
        ] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('engine_id');
                $table->unsignedBigInteger('category_id');
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
            ['id' => 1, 'name' => 'Digital Print A3+', 'status' => 'Active'],
            ['id' => 2, 'name' => 'Large Format', 'status' => 'Active'],
            ['id' => 3, 'name' => 'Mesin Nonaktif', 'status' => 'Inactive'],
        ]);
        DB::table('m_categories')->insert([
            ['id' => 1, 'name' => 'Sticker'],
            ['id' => 2, 'name' => 'HVS'],
            ['id' => 3, 'name' => 'Banner'],
        ]);
        DB::table('m_digital_pricing_rules')->insert([
            ['engine_id' => 1, 'category_id' => 1],
            ['engine_id' => 1, 'category_id' => 2],
        ]);
        DB::table('m_digital_lamination_pricing_rules')->insert([
            ['engine_id' => 1, 'category_id' => 1],
        ]);
        DB::table('m_pricing_rules')->insert([
            ['engine_id' => 2, 'category_id' => 3],
            ['engine_id' => 3, 'category_id' => 3],
        ]);

        $this->user = new User();
        $this->user->name = 'Discount Admin';
        $this->user->email = 'discount-admin@example.test';
        View::share('errors', new ViewErrorBag());
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'form_context' => 'create_discount',
            'code' => 'PROMO-AGUSTUS26',
            'name' => 'Promo Kemerdekaan',
            'description' => 'Diskon 17 Agustus',
            'discount_percentage' => '17,5',
            'starts_at' => '2026-10-15T00:00',
            'ends_at' => '2026-10-20T23:59',
            'status' => 'Active',
            'scopes' => ['1:1', '1:2'],
        ], $overrides);
    }

    private function submit(array $overrides = [])
    {
        return $this->actingAs($this->user)
            ->from(route('admin_discounts'))
            ->post(route('admin_store_discount'), $this->payload($overrides));
    }

    private function seedDiscount(string $name, string $status, string $startsAt, string $endsAt, array $scopes): void
    {
        $id = DB::table('m_discounts')->insertGetId([
            'code' => 'DSC-' . (DB::table('m_discounts')->max('id') + 1),
            'name' => $name, 'description' => null, 'discount_percentage' => 10,
            'starts_at' => $startsAt, 'ends_at' => $endsAt, 'status' => $status,
            'created_by' => 'Admin', 'created_at' => now(),
        ]);

        foreach ($scopes as [$engineId, $categoryId]) {
            DB::table('m_discount_scopes')->insert([
                'discount_id' => $id, 'engine_id' => $engineId, 'category_id' => $categoryId,
            ]);
        }
    }

    public function test_index_lists_only_available_active_engine_scopes(): void
    {
        $scopeOptions = (new DiscountCT())->index()->getData()['scopeOptions'];

        $this->assertSame(['Digital Print A3+', 'Large Format'], $scopeOptions->pluck('name')->all());
        $this->assertSame(['HVS', 'Sticker'], $scopeOptions[0]->categories->pluck('name')->sort()->values()->all());
        $this->assertSame(['Banner'], $scopeOptions[1]->categories->pluck('name')->all());
    }

    public function test_index_renders_create_modal(): void
    {
        $this->actingAs($this->user);
        $html = (new DiscountCT())->index()->render();

        $this->assertStringContainsString('id="modalTambahDiskon"', $html);
        $this->assertStringContainsString('id="discountCode"', $html);
        $this->assertStringContainsString(route('admin_store_discount'), $html);
        $this->assertStringContainsString('value="1:1"', $html);
        $this->assertStringNotContainsString('value="3:3"', $html);
    }

    public function test_store_saves_discount_and_scopes(): void
    {
        $this->submit(['name' => '  Promo   Kemerdekaan  ', 'code' => '  promo-agustus26  '])
            ->assertRedirect(route('admin_discounts'))
            ->assertSessionHas('success', 'Diskon "Promo Kemerdekaan" berhasil ditambahkan.');

        $discount = DB::table('m_discounts')->first();

        $this->assertSame('Promo Kemerdekaan', $discount->name);
        $this->assertSame('PROMO-AGUSTUS26', $discount->code);
        $this->assertSame(17.5, (float) $discount->discount_percentage);
        $this->assertSame('2026-10-15 00:00:00', $discount->starts_at);
        $this->assertSame('2026-10-20 23:59:59', $discount->ends_at);
        $this->assertSame('Active', $discount->status);
        $this->assertSame('Discount Admin', $discount->created_by);
        $this->assertNull($discount->updated_at);
        $this->assertSame(
            [[1, 1], [1, 2]],
            DB::table('m_discount_scopes')->orderBy('category_id')->get()
                ->map(fn ($s) => [(int) $s->engine_id, (int) $s->category_id])->all()
        );
    }

    public function test_required_fields_are_validated(): void
    {
        $this->submit([
            'code' => '',
            'name' => '   ', 'discount_percentage' => '', 'starts_at' => '', 'ends_at' => '',
            'status' => '', 'scopes' => [],
        ])->assertSessionHasErrors(['code', 'name', 'discount_percentage', 'starts_at', 'ends_at', 'status', 'scopes']);

        $this->assertSame(0, DB::table('m_discounts')->count());
    }

    public function test_percentage_rules(): void
    {
        foreach (['0', '100.01', '12.345', 'abc', '-5'] as $value) {
            $this->submit(['discount_percentage' => $value])->assertSessionHasErrors('discount_percentage');
        }

        $this->submit(['discount_percentage' => '100'])->assertSessionHasNoErrors();
    }

    public function test_code_rejects_invalid_format_length_and_input_type(): void
    {
        foreach (['', 'AB', str_repeat('A', 31), 'PROMO AGUSTUS', 'PROMO!', 'PROMO/26', 'PROMO.26', "PROMO\u{00C9}", ['PROMO'], 123] as $code) {
            $this->submit(['code' => $code])->assertSessionHasErrors('code');
        }

        $this->assertSame(0, DB::table('m_discounts')->count());
        $this->assertSame(0, DB::table('m_discount_scopes')->count());
    }

    public function test_code_accepts_minimum_and_maximum_length(): void
    {
        $this->submit(['code' => 'a_1'])->assertSessionHasNoErrors();
        $this->assertSame('A_1', DB::table('m_discounts')->value('code'));

        $this->submit([
            'code' => str_repeat('b', 29) . '-', 'name' => 'Promo Maksimal', 'status' => 'Inactive',
        ])->assertSessionHasNoErrors();

        $this->assertSame(str_repeat('B', 29) . '-', DB::table('m_discounts')->where('name', 'Promo Maksimal')->value('code'));
    }

    public function test_duplicate_code_is_rejected_case_insensitively_even_for_inactive_discount(): void
    {
        $this->seedDiscount('Promo Lain', 'Inactive', '2025-01-01 00:00:00', '2025-01-02 23:59:59', []);
        DB::table('m_discounts')->update(['code' => 'promo-agustus26']);

        $this->submit(['code' => 'PROMO-AGUSTUS26'])->assertSessionHasErrors('code');

        $this->assertSame(1, DB::table('m_discounts')->count());
        $this->assertSame(0, DB::table('m_discount_scopes')->count());
    }

    public function test_code_constraint_conflict_returns_validation_error_and_rolls_back(): void
    {
        DB::unprepared("CREATE TRIGGER simulate_code_conflict BEFORE INSERT ON m_discounts
            BEGIN SELECT RAISE(ABORT, 'UNIQUE constraint failed: m_discounts.code'); END");

        $this->submit()->assertSessionHasErrors('code');

        $this->assertSame(0, DB::table('m_discounts')->count());
        $this->assertSame(0, DB::table('m_discount_scopes')->count());
    }

    public function test_code_error_reopens_create_modal_and_preserves_input(): void
    {
        $this->submit(['code' => 'BAD CODE'])->assertSessionHasErrors('code');

        View::share('errors', session('errors'));
        $html = (new DiscountCT())->index()->render();

        $this->assertStringContainsString('id="modalTambahDiskon" data-reopen="true"', $html);
        $this->assertStringContainsString('value="BAD CODE"', $html);
        $this->assertStringContainsString('Kode diskon hanya boleh berisi huruf', $html);
    }

    public function test_period_must_be_valid_and_not_expired(): void
    {
        $this->submit(['ends_at' => '2026-10-15T00:00'])->assertSessionHasErrors('ends_at');
        $this->submit(['starts_at' => '2026-10-10T00:00', 'ends_at' => '2026-10-15T00:29'])
            ->assertSessionHasErrors('ends_at');
        $this->submit(['starts_at' => '15-10-2026'])->assertSessionHasErrors('starts_at');

        $this->assertSame(0, DB::table('m_discounts')->count());
    }

    public function test_duplicate_name_is_rejected_case_insensitively(): void
    {
        $this->seedDiscount('Promo Kemerdekaan', 'Inactive', '2025-01-01 00:00:00', '2025-01-02 23:59:59', []);

        $this->submit(['name' => 'promo KEMERDEKAAN'])->assertSessionHasErrors('name');
        $this->assertSame(1, DB::table('m_discounts')->count());
    }

    public function test_invalid_unavailable_or_duplicate_scopes_are_rejected(): void
    {
        $this->submit(['scopes' => ['abc']])->assertSessionHasErrors('scopes.0');
        $this->submit(['scopes' => ['1:1', '1:1']])->assertSessionHasErrors('scopes.0');
        $this->submit(['scopes' => ['2:1']])->assertSessionHasErrors('scopes');
        $this->submit(['scopes' => ['3:3']])->assertSessionHasErrors('scopes');

        $this->assertSame(0, DB::table('m_discounts')->count());
    }

    public function test_active_overlap_on_same_scope_is_rejected(): void
    {
        $this->seedDiscount('Promo Oktober', 'Active', '2026-10-01 00:00:00', '2026-10-15 00:00:59', [[1, 2]]);

        $this->submit()->assertSessionHasErrors('scopes');
        $this->assertSame(0, DB::table('m_discount_scopes')->where('discount_id', '!=', 1)->count());
        $this->assertSame(1, DB::table('m_discounts')->count());
    }

    public function test_overlap_is_allowed_for_inactive_other_scope_or_other_period(): void
    {
        $this->seedDiscount('Promo Draft', 'Inactive', '2026-10-01 00:00:00', '2026-10-31 23:59:59', [[1, 1]]);
        $this->seedDiscount('Promo Banner', 'Active', '2026-10-01 00:00:00', '2026-10-31 23:59:59', [[2, 3]]);
        $this->seedDiscount('Promo Lama', 'Active', '2026-09-01 00:00:00', '2026-10-14 23:59:59', [[1, 1]]);

        $this->submit()->assertSessionHasNoErrors();

        $this->submit([
            'name' => 'Promo Inactive', 'code' => 'PROMO-INACTIVE', 'status' => 'Inactive', 'scopes' => ['1:1'],
        ])->assertSessionHasNoErrors();

        $this->assertSame(5, DB::table('m_discounts')->count());
    }

    private function seedEditable(): int
    {
        $this->seedDiscount('Promo Lama Edit', 'Active', '2026-10-01 00:00:00', '2026-10-31 23:59:59', [[1, 1], [3, 3]]);

        return (int) DB::table('m_discounts')->where('name', 'Promo Lama Edit')->value('id');
    }

    private function submitUpdate(int $id, array $overrides = [])
    {
        return $this->actingAs($this->user)
            ->from(route('admin_discounts'))
            ->put(route('admin_update_discount', $id), $this->payload(array_replace([
                'form_context' => 'edit_discount',
                'code' => DB::table('m_discounts')->where('id', $id)->value('code') ?? 'MISSING-999',
                'discount_id' => $id,
                'name' => 'Promo Lama Edit',
                'discount_percentage' => '12,25',
                'starts_at' => '2026-10-01T00:00',
                'ends_at' => '2026-10-31T23:59',
                'status' => 'Active',
                'scopes' => ['1:1', '3:3'],
            ], $overrides)));
    }

    public function test_edit_returns_discount_with_scopes(): void
    {
        $id = $this->seedEditable();

        $data = $this->actingAs($this->user)->getJson(route('admin_edit_discount', $id))->assertOk()->json();

        $this->assertSame('Promo Lama Edit', $data['name']);
        $this->assertSame('DSC-1', $data['code']);
        $this->assertSame('10', $data['discount_percentage']);
        $this->assertSame('2026-10-01T00:00', $data['starts_at']);
        $this->assertSame('2026-10-31T23:59', $data['ends_at']);
        $this->assertSame([
            ['value' => '1:1', 'label' => 'Digital Print A3+ – Sticker', 'available' => true],
            ['value' => '3:3', 'label' => 'Mesin Nonaktif – Banner', 'available' => false],
        ], $data['scopes']);

        $this->actingAs($this->user)->getJson(route('admin_edit_discount', 999))->assertNotFound();
    }

    public function test_update_saves_changes_and_syncs_scopes(): void
    {
        $id = $this->seedEditable();

        $this->submitUpdate($id, ['name' => 'Promo Baru', 'code' => ' promo_baru ', 'scopes' => ['1:2', '2:3']])
            ->assertRedirect(route('admin_discounts'))
            ->assertSessionHas('success', 'Diskon "Promo Baru" berhasil diperbarui.');

        $discount = DB::table('m_discounts')->find($id);

        $this->assertSame('Promo Baru', $discount->name);
        $this->assertSame('PROMO_BARU', $discount->code);
        $this->assertSame(12.25, (float) $discount->discount_percentage);
        $this->assertSame('2026-10-31 23:59:59', $discount->ends_at);
        $this->assertSame('Admin', $discount->created_by);
        $this->assertSame('Discount Admin', $discount->updated_by);
        $this->assertNotNull($discount->updated_at);
        $this->assertSame(
            [[1, 2], [2, 3]],
            DB::table('m_discount_scopes')->where('discount_id', $id)->orderBy('engine_id')->get()
                ->map(fn ($s) => [(int) $s->engine_id, (int) $s->category_id])->all()
        );
    }

    public function test_update_keeps_own_name_period_and_legacy_scope(): void
    {
        $id = $this->seedEditable();

        $this->submitUpdate($id, ['name' => 'PROMO lama edit', 'code' => 'dsc-1'])->assertSessionHasNoErrors();

        $this->assertSame('DSC-1', DB::table('m_discounts')->where('id', $id)->value('code'));
        $this->assertSame(2, DB::table('m_discount_scopes')->where('discount_id', $id)->count());
    }

    public function test_update_rejects_duplicate_code_without_changing_discount_or_scopes(): void
    {
        $id = $this->seedEditable();
        $this->seedDiscount('Promo Lain', 'Inactive', '2025-01-01 00:00:00', '2025-01-02 23:59:59', [[1, 2]]);
        DB::table('m_discounts')->where('name', 'Promo Lain')->update(['code' => 'promo-lain']);
        $before = DB::table('m_discounts')->find($id);

        $this->submitUpdate($id, ['code' => 'PROMO-LAIN', 'scopes' => ['2:3']])
            ->assertSessionHasErrors('code');

        $this->assertEquals($before, DB::table('m_discounts')->find($id));
        $this->assertSame(['1:1', '3:3'], DB::table('m_discount_scopes')->where('discount_id', $id)
            ->orderBy('engine_id')->get()->map(fn ($scope) => $scope->engine_id . ':' . $scope->category_id)->all());
    }

    public function test_update_validates_code_format_and_required_value(): void
    {
        $id = $this->seedEditable();

        foreach (['', 'AB', str_repeat('A', 31), 'PROMO LAIN', ['PROMO']] as $code) {
            $this->submitUpdate($id, ['code' => $code])->assertSessionHasErrors('code');
        }

        $this->assertSame('DSC-1', DB::table('m_discounts')->where('id', $id)->value('code'));
    }

    public function test_update_rejects_duplicate_name_and_overlap_with_other_discount(): void
    {
        $id = $this->seedEditable();
        $this->seedDiscount('Promo Lain', 'Active', '2026-10-20 00:00:00', '2026-11-05 23:59:59', [[1, 2]]);

        $this->submitUpdate($id, ['name' => 'promo lain'])->assertSessionHasErrors('name');
        $this->submitUpdate($id, ['scopes' => ['1:1', '1:2']])->assertSessionHasErrors('scopes');
        $this->submitUpdate($id, ['status' => 'Inactive', 'scopes' => ['1:1', '1:2']])->assertSessionHasNoErrors();
    }

    public function test_update_allows_unchanged_past_end_but_rejects_new_past_end(): void
    {
        $this->seedDiscount('Promo Kedaluwarsa', 'Active', '2026-09-01 00:00:00', '2026-09-30 23:59:59', [[1, 1]]);
        $id = (int) DB::table('m_discounts')->where('name', 'Promo Kedaluwarsa')->value('id');

        $this->submitUpdate($id, [
            'name' => 'Promo Kedaluwarsa', 'starts_at' => '2026-09-01T00:00', 'ends_at' => '2026-09-30T23:59',
            'scopes' => ['1:1'],
        ])->assertSessionHasNoErrors();

        $this->submitUpdate($id, [
            'name' => 'Promo Kedaluwarsa', 'starts_at' => '2026-09-01T00:00', 'ends_at' => '2026-10-01T23:59',
            'scopes' => ['1:1'],
        ])->assertSessionHasErrors('ends_at');
    }

    public function test_code_error_reopens_edit_modal_and_preserves_input(): void
    {
        $id = $this->seedEditable();
        $this->submitUpdate($id, ['code' => 'BAD CODE'])->assertSessionHasErrors('code');

        View::share('errors', session('errors'));
        $html = (new DiscountCT())->index()->render();

        $this->assertStringContainsString('id="modalEditDiskon" data-reopen="true"', $html);
        $this->assertStringContainsString('value="BAD CODE"', $html);
        $this->assertStringContainsString('Kode diskon hanya boleh berisi huruf', $html);
    }

    public function test_update_code_constraint_conflict_preserves_existing_discount_and_scopes(): void
    {
        $id = $this->seedEditable();
        $before = DB::table('m_discounts')->find($id);
        DB::unprepared("CREATE TRIGGER simulate_code_conflict BEFORE UPDATE ON m_discounts
            BEGIN SELECT RAISE(ABORT, 'UNIQUE constraint failed: m_discounts.code'); END");

        $this->submitUpdate($id, ['code' => 'NEW-CODE', 'scopes' => ['2:3']])->assertSessionHasErrors('code');

        $this->assertEquals($before, DB::table('m_discounts')->find($id));
        $this->assertSame(2, DB::table('m_discount_scopes')->where('discount_id', $id)->count());
    }

    public function test_update_rejects_unavailable_new_scope_and_missing_discount(): void
    {
        $id = $this->seedEditable();

        $this->submitUpdate($id, ['scopes' => ['2:1']])->assertSessionHasErrors('scopes');

        $this->submitUpdate(999)->assertRedirect(route('admin_discounts'))
            ->assertSessionHas('error', 'Data diskon tidak ditemukan.');
    }

    public function test_index_renders_edit_modal(): void
    {
        $this->actingAs($this->user);
        $html = (new DiscountCT())->index()->render();

        $this->assertStringContainsString('id="modalEditDiskon"', $html);
        $this->assertStringContainsString('id="editDiscountCode"', $html);
        $this->assertStringContainsString('btn-edit-discount', $html);
        $this->assertStringNotContainsString('<th>Dibuat Pada</th>', $html);
    }

    public function test_details_renders_discount_summary_and_scopes(): void
    {
        $id = $this->seedEditable();
        $this->actingAs($this->user);

        $html = (new DiscountCT())->details($id)->render();

        $this->assertStringContainsString('Promo Lama Edit', $html);
        $this->assertStringContainsString('Kode Promo:', $html);
        $this->assertStringContainsString('<strong>DSC-1</strong>', $html);
        $this->assertStringContainsString('10%', $html);
        $this->assertStringContainsString('Berlangsung', $html);
        $this->assertStringContainsString('Sisa 17 hari lagi', $html);
        $this->assertStringContainsString('2 mesin · 2 kategori', $html);
        $this->assertStringContainsString('Mesin nonaktif', $html);
        $this->assertStringContainsString('Sticker', $html);
        $this->assertStringContainsString('is-unavailable', $html);
        $this->assertStringContainsString('Belum pernah diperbarui', $html);
    }

    public function test_details_redirects_when_discount_missing(): void
    {
        $this->actingAs($this->user)->get(route('admin_discount_details', 999))
            ->assertRedirect(route('admin_discounts'))
            ->assertSessionHas('error', 'Data diskon tidak ditemukan.');
    }

    public function test_index_renders_detail_button(): void
    {
        $this->actingAs($this->user);
        $html = (new DiscountCT())->index()->render();

        $this->assertStringContainsString('btn-view-discount', $html);
        $this->assertStringContainsString(route('admin_discount_details', ':id'), $html);
    }

    public function test_destroy_deletes_discount_and_scopes_only(): void
    {
        $id = $this->seedEditable();
        $this->seedDiscount('Promo Lain', 'Active', '2026-10-20 00:00:00', '2026-11-05 23:59:59', [[1, 2]]);

        $this->actingAs($this->user)->deleteJson(route('admin_destroy_discount', $id))
            ->assertOk()
            ->assertJson(['success' => true, 'message' => 'Diskon "Promo Lama Edit" berhasil dihapus.']);

        $this->assertNull(DB::table('m_discounts')->find($id));
        $this->assertSame(0, DB::table('m_discount_scopes')->where('discount_id', $id)->count());
        $this->assertSame(1, DB::table('m_discounts')->count());
        $this->assertSame(1, DB::table('m_discount_scopes')->count());
        $this->assertSame(3, DB::table('m_engines')->count());
        $this->assertSame(3, DB::table('m_categories')->count());
    }

    public function test_destroy_returns_not_found_for_missing_discount(): void
    {
        $this->actingAs($this->user)->deleteJson(route('admin_destroy_discount', 999))
            ->assertNotFound()
            ->assertJson(['success' => false, 'message' => 'Data diskon tidak ditemukan.']);
    }

    public function test_index_renders_delete_button_and_modal(): void
    {
        $this->actingAs($this->user);
        $html = (new DiscountCT())->index()->render();

        $this->assertStringContainsString('btn-delete-discount', $html);
        $this->assertStringContainsString('id="modalDeleteDiskon"', $html);
        $this->assertStringContainsString(route('admin_destroy_discount', ':id'), $html);
    }
}

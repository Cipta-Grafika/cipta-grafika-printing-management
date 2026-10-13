<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\ProductionCostFinishingCT;
use App\Imports\ProductionCostFinishingsImport;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProductionCostFinishingMarkupTest extends TestCase
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

        $tables = [
            'm_engines' => ['name', 'status'],
            'm_locations' => ['name', 'status'],
            'm_vendors' => ['name', 'status'],
            'm_categories' => ['name'],
            'm_materials' => ['category_id', 'material_code', 'material_name', 'status'],
            'm_material_sizes' => ['material_id', 'width', 'length', 'unit'],
            'm_pricing_rules' => [
                'engine_id', 'location_id', 'vendor_id', 'category_id',
                'price_type', 'markup_percentage', 'rounding_value', 'status',
            ],
            'm_production_costs' => ['engine_id'],
            'm_production_cost_locations' => ['production_cost_id', 'location_id'],
            'm_production_cost_details' => [
                'production_cost_id', 'material_id', 'general_pricing_rule_id',
                'division_pricing_rule_id', 'plain_pricing_rule_id',
                'price_per_meter', 'production_cost', 'finishing_cost',
                'total_cost', 'general_price', 'division_price', 'plain_price',
                'vendor_id', 'vendor_price',
            ],
            'm_production_cost_detail_widths' => [
                'production_cost_detail_id', 'material_size_id',
            ],
        ];

        foreach ($tables as $name => $columns) {
            Schema::create($name, function (Blueprint $table) use ($columns) {
                $table->id();
                foreach ($columns as $column) {
                    if (str_ends_with($column, '_id')) {
                        $table->unsignedBigInteger($column)->nullable();
                    } else {
                        $table->string($column)->nullable();
                    }
                }
                $table->string('created_by')->nullable();
                $table->string('updated_by')->nullable();
                $table->timestamps();
            });
        }

        $user = new User();
        $user->name = 'Markup Test';
        Auth::setUser($user);
        DB::table('m_engines')->insert(['id' => 1, 'name' => 'Large Format', 'status' => 'Active']);
        DB::table('m_locations')->insert([
            ['id' => 1, 'name' => 'Purwakarta', 'status' => 'Active'],
            ['id' => 2, 'name' => 'Outsourcing', 'status' => 'Active'],
            ['id' => 3, 'name' => 'Graha', 'status' => 'Active'],
        ]);
        DB::table('m_vendors')->insert(['id' => 1, 'name' => 'Test Vendor', 'status' => 'Active']);
        DB::table('m_categories')->insert([
            ['id' => 1, 'name' => 'Outdoor'],
            ['id' => 2, 'name' => 'Indoor'],
        ]);
        foreach ([
            [1, 'Flexy Frontlite China 280 gsm'],
            [1, 'Other Material'],
            [2, 'Indoor Material'],
        ] as $index => [$categoryId, $name]) {
            $id = $index + 1;
            DB::table('m_materials')->insert([
                'id' => $id, 'category_id' => $categoryId,
                'material_code' => 'TEST-' . $id, 'material_name' => $name, 'status' => 'Active',
            ]);
            DB::table('m_material_sizes')->insert([
                'id' => $id, 'material_id' => $id, 'width' => 100, 'unit' => 'cm',
            ]);
        }
        foreach ([1, 2, 3] as $locationId) {
            foreach ([1, 2] as $categoryId) {
                foreach (['general' => 75, 'division' => 80, 'plain' => 20] as $type => $markup) {
                    DB::table('m_pricing_rules')->insert([
                        'engine_id' => 1, 'location_id' => $locationId,
                        'category_id' => $categoryId, 'vendor_id' => $locationId === 2 ? 1 : null,
                        'price_type' => $type, 'markup_percentage' => $markup,
                        'rounding_value' => 100, 'status' => 'Active',
                    ]);
                }
            }
        }
    }

    public static function flows(): array
    {
        return [
            'create Purwakarta' => ['create', 1],
            'create outsourcing' => ['create', 2],
            'create Graha' => ['create', 3],
            'edit Purwakarta' => ['edit', 1],
            'edit outsourcing' => ['edit', 2],
            'edit Graha' => ['edit', 3],
            'import Purwakarta' => ['import', 1],
            'import outsourcing' => ['import', 2],
            'import Graha' => ['import', 3],
        ];
    }

    #[DataProvider('flows')]
    public function test_saved_prices_use_only_general_override_and_master_rounding(
        string $flow,
        int $locationId
    ): void {
        $outsourcing = $locationId === 2;
        $locationName = DB::table('m_locations')->where('id', $locationId)->value('name');
        $items = [];
        foreach ([1, 2, 3] as $materialId) {
            $items[] = [
                'material_id' => $materialId, 'width_ids' => [$materialId],
                'price_per_meter' => '1001', 'production_cost' => '123',
                'finishing_cost' => '77', 'vendor_price' => '1001',
                'vendor_id' => $outsourcing ? 1 : null,
            ];
        }

        $controller = app(ProductionCostFinishingCT::class);
        if ($flow === 'import') {
            $rows = DB::table('m_materials')->get()->map(function ($material) use ($outsourcing, $locationName) {
                if ($outsourcing) {
                    return collect([
                        'mesin' => 'Large Format', 'lokasi' => 'Outsourcing',
                        'nama_vendor' => 'Test Vendor',
                        'kategori' => $material->category_id === 1 ? 'Outdoor' : 'Indoor',
                        'nama_material' => $material->material_name, 'lebar' => '100 cm',
                        'panjang' => '', 'harga_vendor' => '1001',
                    ]);
                }
                return collect([
                    'engine' => 'Large Format', 'lokasi' => $locationName,
                    'nama_material' => $material->material_name, 'lebar' => '100 cm',
                    'harga_per_meter' => '1001', 'ongkos_produksi' => '123',
                    'ongkos_finishing' => '77',
                ]);
            });
            $import = new ProductionCostFinishingsImport();
            $import->collection($rows);
            $this->assertSame(3, $import->importedRows);
        } else {
            $response = $controller->store(Request::create('/test', 'POST', [
                'configurations' => [[
                    'engine_id' => 1, 'location_ids' => [$locationId],
                    'vendor_id' => $outsourcing ? 1 : null, 'items' => $items,
                ]],
            ]));
            $this->assertTrue($response->getData(true)['success'], $response->getContent());
            if ($flow === 'edit') {
                // A different master markup still must not replace the target override.
                DB::table('m_pricing_rules')->where('price_type', 'general')
                    ->update(['markup_percentage' => 90, 'rounding_value' => 250]);
                $response = $controller->update(Request::create('/test', 'PUT', [
                    'engine_id' => 1,
                    'locations' => [['location_id' => $locationId, 'items' => $items]],
                ]), base64_encode('1'));
                $this->assertTrue($response->getData(true)['success'], $response->getContent());
            }
        }

        $rounding = $flow === 'edit' ? 250 : 100;
        $masterMarkup = $flow === 'edit' ? 90 : 75;
        $cost = $outsourcing ? 1001 : 1001 * 1.2 + 123 + 77;
        foreach (DB::table('m_production_cost_details')->get() as $detail) {
            $markup = $locationId === 1 && (int) $detail->material_id === 1 ? 40 : $masterMarkup;
            $general = ceil(($cost + $cost * $markup / 100) / $rounding) * $rounding;
            $this->assertEqualsWithDelta($cost, (float) $detail->total_cost, 0.00001);
            $this->assertSame($general, (float) $detail->general_price);
            $this->assertSame(ceil($general * 0.8 / 100) * 100, (float) $detail->division_price);
            $this->assertSame($outsourcing ? 0.0 : 1300.0, (float) $detail->plain_price);
        }
        $this->assertSame(3, DB::table('m_production_cost_details')->count());
        $this->assertSame(3, DB::table('m_production_cost_detail_widths')->count());
        $this->assertSame($masterMarkup, (int) DB::table('m_pricing_rules')
            ->where('price_type', 'general')->value('markup_percentage'));
    }
}

<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Exception;
use Illuminate\Support\Facades\Auth;

class ProductionCostFinishingsImport implements
    ToCollection,
    WithHeadingRow
{
    public int $importedRows = 0;

    /**
     * IMPORT
     */
    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            throw new Exception(
                'File Excel tidak memiliki data.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DETEKSI FORMAT IMPORT
        |--------------------------------------------------------------------------
        |
        | Outsourcing dan Non-Outsourcing menggunakan format Excel yang berbeda.
        |
        | Jika header Outsourcing terdeteksi, proses dialihkan ke flow
        | Outsourcing. Jika tidak, flow Non-Outsourcing existing di bawah ini
        | tetap dijalankan tanpa perubahan.
        |
        */

        $firstRow = $rows->first();

        if ($this->isOutsourcingImport($firstRow)) {
            $this->importOutsourcing($rows);

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | HEADER WAJIB
        |--------------------------------------------------------------------------
        */

        $requiredHeaders = [
            'engine',
            'lokasi',
            'nama_material',
            'lebar',
            'harga_per_meter',
            'ongkos_produksi',
            'ongkos_finishing',
        ];

        foreach ($requiredHeaders as $header) {
            if (!array_key_exists(
                $header,
                $firstRow->toArray()
            )) {
                throw new Exception(
                    'Kolom "' .
                        $header .
                        '" tidak ditemukan di file Excel.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | LOAD MASTER DATA
        |--------------------------------------------------------------------------
        */

        $engines = DB::table('m_engines')
            ->get()
            ->keyBy(function ($engine) {
                return strtolower(
                    trim($engine->name)
                );
            });

        $locations = DB::table('m_locations')
            ->get()
            ->keyBy(function ($location) {
                return strtolower(
                    trim($location->name)
                );
            });

        $materials = DB::table('m_materials as m')
            ->join(
                'm_categories as c',
                'm.category_id',
                '=',
                'c.id'
            )
            ->select(
                'm.id',
                'm.material_name',
                'm.category_id',
                'm.status as material_status',
                'c.name as category_name'
            )
            ->get()
            ->keyBy(function ($material) {
                return strtolower(
                    trim($material->material_name)
                );
            });

        $materialSizes = DB::table('m_material_sizes')
            ->get()
            ->groupBy('material_id');

        /*
        |--------------------------------------------------------------------------
        | PRICING RULES
        |--------------------------------------------------------------------------
        |
        | Large Format tetap menggunakan:
        | location + category + price_type
        |
        */

        $pricingRules = DB::table('m_pricing_rules')
            ->where('status', 'Active')
            ->get()
            ->keyBy(function ($rule) {
                return
                    $rule->location_id .
                    '|' .
                    $rule->category_id .
                    '|' .
                    $rule->price_type;
            });

        /*
        |--------------------------------------------------------------------------
        | PREPARE DATA
        |--------------------------------------------------------------------------
        */

        $preparedRows = [];

        foreach ($rows as $index => $row) {
            $excelRow = $index + 2;

            $rowArray = $row->toArray();

            /*
            |--------------------------------------------------------------------------
            | SKIP BARIS KOSONG
            |--------------------------------------------------------------------------
            */

            $isEmptyRow = true;

            foreach ($rowArray as $value) {
                if (
                    $value !== null &&
                    trim((string) $value) !== ''
                ) {
                    $isEmptyRow = false;
                    break;
                }
            }

            if ($isEmptyRow) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | REQUIRED VALUE
            |--------------------------------------------------------------------------
            */

            if (
                !isset($row['engine']) ||
                trim((string) $row['engine']) === ''
            ) {
                throw new Exception(
                    'Mesin wajib diisi pada baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            if (
                !isset($row['lokasi']) ||
                trim((string) $row['lokasi']) === ''
            ) {
                throw new Exception(
                    'Lokasi wajib diisi pada baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            if (
                !isset($row['nama_material']) ||
                trim((string) $row['nama_material']) === ''
            ) {
                throw new Exception(
                    'Nama Material wajib diisi pada baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            if (
                !isset($row['lebar']) ||
                trim((string) $row['lebar']) === ''
            ) {
                throw new Exception(
                    'Lebar wajib diisi pada baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | ENGINE
            |--------------------------------------------------------------------------
            */

            $engineName = trim(
                (string) $row['engine']
            );

            $engineKey = strtolower(
                $engineName
            );

            $engine = $engines->get(
                $engineKey
            );

            if (!$engine) {
                throw new Exception(
                    'Mesin "' .
                        $engineName .
                        '" tidak ditemukan pada master mesin. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            if (
                strtolower(
                    trim($engine->status)
                ) !== 'active'
            ) {
                throw new Exception(
                    'Mesin "' .
                        $engine->name .
                        '" tidak aktif. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | ENGINE CALCULATION RULE
            |--------------------------------------------------------------------------
            |
            | Saat ini baru Large Format yang memiliki
            | master calculation rules.
            |
            */

            $engineType = strtolower(
                trim($engine->name)
            );

            if (
                $engineType ===
                'digital print a3+'
            ) {
                throw new Exception(
                    'Mesin Digital Print A3+ belum mempunyai master calculation rules.'
                );
            }

            if (
                $engineType !==
                'large format'
            ) {
                throw new Exception(
                    'Mesin "' .
                        $engine->name .
                        '" belum mempunyai master calculation rules.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | LOCATION
            |--------------------------------------------------------------------------
            */

            $locationName = trim(
                (string) $row['lokasi']
            );

            $locationKey = strtolower(
                $locationName
            );

            $location = $locations->get(
                $locationKey
            );

            if (!$location) {
                throw new Exception(
                    'Lokasi "' .
                        $locationName .
                        '" tidak ditemukan pada master lokasi. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            if (
                strtolower(
                    trim($location->status)
                ) !== 'active'
            ) {
                throw new Exception(
                    'Lokasi "' .
                        $location->name .
                        '" tidak aktif. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | MATERIAL
            |--------------------------------------------------------------------------
            */

            $materialName = trim(
                (string) $row['nama_material']
            );

            $materialKey = strtolower(
                $materialName
            );

            $material = $materials->get(
                $materialKey
            );

            if (!$material) {
                throw new Exception(
                    'Material "' .
                        $materialName .
                        '" tidak ditemukan pada master material. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            if (
                strtolower(
                    trim($material->material_status)
                ) !== 'active'
            ) {
                throw new Exception(
                    'Material "' .
                        $material->material_name .
                        '" tidak aktif. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | MATERIAL SIZE
            |--------------------------------------------------------------------------
            */

            $materialSizeList =
                $materialSizes->get(
                    $material->id,
                    collect()
                );

            /*
            |--------------------------------------------------------------------------
            | LEBAR
            |--------------------------------------------------------------------------
            |
            | Contoh:
            | 320 cm, 500 cm
            |
            */

            $widthValues = preg_split(
                '/\s*,\s*/',
                trim((string) $row['lebar'])
            );

            $selectedWidths = [];

            foreach ($widthValues as $widthValue) {
                $widthValue = trim(
                    $widthValue
                );

                if ($widthValue === '') {
                    continue;
                }

                if (
                    !preg_match(
                        '/^(\d+(?:[.,]\d+)?)\s*([a-zA-Z]+)$/',
                        $widthValue,
                        $matches
                    )
                ) {
                    throw new Exception(
                        'Format Lebar "' .
                            $widthValue .
                            '" tidak valid pada baris Excel ke-' .
                            $excelRow .
                            '.'
                    );
                }

                $widthNumber = $this->parseNumber(
                    $matches[1]
                );

                $widthUnit = strtolower(
                    trim($matches[2])
                );

                $matchedSize = null;

                foreach (
                    $materialSizeList
                    as $materialSize
                ) {
                    $masterUnit = strtolower(
                        trim($materialSize->unit)
                    );

                    $masterWidth = (float)
                    $materialSize->width;

                    if (
                        abs(
                            $masterWidth -
                                $widthNumber
                        ) < 0.00001 &&
                        $masterUnit ===
                        $widthUnit
                    ) {
                        $matchedSize =
                            $materialSize;

                        break;
                    }
                }

                if (!$matchedSize) {
                    throw new Exception(
                        'Lebar "' .
                            $widthValue .
                            '" tidak ditemukan pada master ukuran material "' .
                            $material->material_name .
                            '". Baris Excel ke-' .
                            $excelRow .
                            '.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | DUPLIKAT WIDTH DALAM SATU BARIS EXCEL
                |--------------------------------------------------------------------------
                */

                $widthDuplicateKey =
                    $matchedSize->id;

                if (
                    isset(
                        $selectedWidths[$widthDuplicateKey]
                    )
                ) {
                    throw new Exception(
                        'Lebar "' .
                            $widthValue .
                            '" duplikat pada baris Excel ke-' .
                            $excelRow .
                            '.'
                    );
                }

                $selectedWidths[$widthDuplicateKey] = $matchedSize;
            }

            if (empty($selectedWidths)) {
                throw new Exception(
                    'Tidak ada ukuran material yang valid pada baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | PARSE BIAYA
            |--------------------------------------------------------------------------
            */

            $pricePerMeter =
                $this->parseNumber(
                    $row['harga_per_meter']
                );

            $productionCost =
                $this->parseNumber(
                    $row['ongkos_produksi']
                );

            $finishingCost =
                $this->parseNumber(
                    $row['ongkos_finishing']
                );

            if (
                $pricePerMeter < 0 ||
                $productionCost < 0 ||
                $finishingCost < 0
            ) {
                throw new Exception(
                    'Harga per meter, ongkos produksi, dan ongkos finishing tidak boleh bernilai negatif. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | PRICING RULES
            |--------------------------------------------------------------------------
            */

            $generalRuleKey =
                $location->id .
                '|' .
                $material->category_id .
                '|general';

            $divisionRuleKey =
                $location->id .
                '|' .
                $material->category_id .
                '|division';

            $plainRuleKey =
                $location->id .
                '|' .
                $material->category_id .
                '|plain';

            $generalRule =
                $pricingRules->get(
                    $generalRuleKey
                );

            $divisionRule =
                $pricingRules->get(
                    $divisionRuleKey
                );

            $plainRule =
                $pricingRules->get(
                    $plainRuleKey
                );

            if (!$generalRule) {
                throw new Exception(
                    'Pricing Rule Harga Umum tidak ditemukan untuk Lokasi "' .
                        $location->name .
                        '" dan Kategori "' .
                        $material->category_name .
                        '". Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            if (!$divisionRule) {
                throw new Exception(
                    'Pricing Rule Harga Divisi tidak ditemukan untuk Lokasi "' .
                        $location->name .
                        '" dan Kategori "' .
                        $material->category_name .
                        '". Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            if (!$plainRule) {
                throw new Exception(
                    'Pricing Rule Harga Polos tidak ditemukan untuk Lokasi "' .
                        $location->name .
                        '" dan Kategori "' .
                        $material->category_name .
                        '". Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | MARKUP
            |--------------------------------------------------------------------------
            */

            $generalMarkup =
                ((float) $generalRule->markup_percentage)
                / 100;

            $divisionMarkup =
                ((float) $divisionRule->markup_percentage)
                / 100;

            $plainMarkup =
                ((float) $plainRule->markup_percentage)
                / 100;

            /*
            |--------------------------------------------------------------------------
            | ROUNDING
            |--------------------------------------------------------------------------
            */

            $generalRounding =
                $generalRule->rounding_value !== null
                ? (float) $generalRule->rounding_value
                : null;

            $divisionRounding =
                $divisionRule->rounding_value !== null
                ? (float) $divisionRule->rounding_value
                : null;

            $plainRounding =
                $plainRule->rounding_value !== null
                ? (float) $plainRule->rounding_value
                : null;

            /*
            |--------------------------------------------------------------------------
            | LARGE FORMAT CALCULATION
            |--------------------------------------------------------------------------
            |
            | RUMUS TETAP
            |
            | totalCost
            | = pricePerMeter * 1.2
            |   + productionCost
            |   + finishingCost
            |
            | generalPrice
            | = totalCost * (1 + generalMarkup)
            |
            | divisionPrice
            | = generalPrice * divisionMarkup
            |
            | plainPrice
            | = pricePerMeter * (1 + plainMarkup)
            |
            */

            $totalCost =
                ($pricePerMeter * 1.2) +
                $productionCost +
                $finishingCost;

            $generalPrice =
                $totalCost *
                (1 + $generalMarkup);

            $generalPrice =
                $this->roundUp(
                    $generalPrice,
                    $generalRounding
                );

            $divisionPrice =
                $generalPrice *
                $divisionMarkup;

            $divisionPrice =
                $this->roundUp(
                    $divisionPrice,
                    $divisionRounding
                );

            $plainPrice =
                $pricePerMeter *
                (1 + $plainMarkup);

            $plainPrice =
                $this->roundUp(
                    $plainPrice,
                    $plainRounding
                );

            /*
            |--------------------------------------------------------------------------
            | PREPARE ROW PER WIDTH
            |--------------------------------------------------------------------------
            */

            foreach (
                $selectedWidths
                as $materialSize
            ) {
                $preparedRows[] = [
                    'engine_id' =>
                    $engine->id,

                    'location_id' =>
                    $location->id,

                    'material_id' =>
                    $material->id,

                    'material_size_id' =>
                    $materialSize->id,

                    'price_per_meter' =>
                    $pricePerMeter,

                    'production_cost' =>
                    $productionCost,

                    'finishing_cost' =>
                    $finishingCost,

                    'total_cost' =>
                    $totalCost,

                    'general_price' =>
                    $generalPrice,

                    'division_price' =>
                    $divisionPrice,

                    'plain_price' =>
                    $plainPrice,

                    /*
                    |--------------------------------------------------------------------------
                    | PRICING RULE ID
                    |--------------------------------------------------------------------------
                    */

                    'general_pricing_rule_id' =>
                    $generalRule->id,

                    'division_pricing_rule_id' =>
                    $divisionRule->id,

                    'plain_pricing_rule_id' =>
                    $plainRule->id,

                    'excel_row' =>
                    $excelRow,
                ];
            }
        }

        if (empty($preparedRows)) {
            throw new Exception(
                'Tidak ada data valid yang dapat diimport.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI KONSISTENSI BIAYA DALAM EXCEL
        |--------------------------------------------------------------------------
        |
        | Engine + Location + Material
        |
        | Biaya harus sama walaupun width berbeda.
        |
        */

        $costConfigurations = [];

        foreach ($preparedRows as $row) {
            $key =
                $this->configurationCostKey(
                    $row
                );

            $costValue = [
                'price_per_meter' =>
                $row['price_per_meter'],

                'production_cost' =>
                $row['production_cost'],

                'finishing_cost' =>
                $row['finishing_cost'],
            ];

            if (
                isset(
                    $costConfigurations[$key]
                )
            ) {
                $existing =
                    $costConfigurations[$key];

                if (
                    $existing['price_per_meter'] !=
                    $costValue['price_per_meter'] ||
                    $existing['production_cost'] !=
                    $costValue['production_cost'] ||
                    $existing['finishing_cost'] !=
                    $costValue['finishing_cost']
                ) {
                    throw new Exception(
                        'Biaya untuk kombinasi Engine, Lokasi, dan Material yang sama harus konsisten. ' .
                            'Masalah ditemukan pada baris Excel ke-' .
                            $row['excel_row'] .
                            '.'
                    );
                }
            } else {
                $costConfigurations[$key] =
                    $costValue;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DUPLIKAT WIDTH DALAM EXCEL
        |--------------------------------------------------------------------------
        */

        $excelWidths = [];

        foreach ($preparedRows as $row) {
            $key =
                $this->configurationWidthKey(
                    $row
                );

            if (
                isset(
                    $excelWidths[$key]
                )
            ) {
                throw new Exception(
                    'Data dengan Engine, Lokasi, Material, dan Lebar yang sama ditemukan lebih dari satu kali dalam Excel. ' .
                        'Baris Excel ke-' .
                        $row['excel_row'] .
                        '.'
                );
            }

            $excelWidths[$key] = true;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DATABASE
        |--------------------------------------------------------------------------
        |
        | Existing configuration boleh digunakan kembali
        | jika biaya sama.
        |
        | Width baru boleh ditambahkan.
        |
        | Width yang sudah ada tidak boleh diimport ulang.
        |
        */

        foreach ($preparedRows as $row) {
            $existingDetail =
                DB::table(
                    'm_production_costs as pc'
                )
                ->join(
                    'm_production_cost_locations as pcl',
                    'pc.id',
                    '=',
                    'pcl.production_cost_id'
                )
                ->join(
                    'm_production_cost_details as pcd',
                    'pc.id',
                    '=',
                    'pcd.production_cost_id'
                )
                ->where(
                    'pc.engine_id',
                    $row['engine_id']
                )
                ->where(
                    'pcl.location_id',
                    $row['location_id']
                )
                ->where(
                    'pcd.material_id',
                    $row['material_id']
                )
                ->where(
                    'pcd.price_per_meter',
                    $row['price_per_meter']
                )
                ->where(
                    'pcd.production_cost',
                    $row['production_cost']
                )
                ->where(
                    'pcd.finishing_cost',
                    $row['finishing_cost']
                )
                ->select(
                    'pc.id as production_cost_id',
                    'pcd.id as detail_id'
                )
                ->first();

            /*
            |--------------------------------------------------------------------------
            | EXISTING CONFIGURATION DENGAN BIAYA BERBEDA
            |--------------------------------------------------------------------------
            */

            $existingConfiguration =
                DB::table(
                    'm_production_costs as pc'
                )
                ->join(
                    'm_production_cost_locations as pcl',
                    'pc.id',
                    '=',
                    'pcl.production_cost_id'
                )
                ->join(
                    'm_production_cost_details as pcd',
                    'pc.id',
                    '=',
                    'pcd.production_cost_id'
                )
                ->where(
                    'pc.engine_id',
                    $row['engine_id']
                )
                ->where(
                    'pcl.location_id',
                    $row['location_id']
                )
                ->where(
                    'pcd.material_id',
                    $row['material_id']
                )
                ->select(
                    'pc.id as production_cost_id',
                    'pcd.id as detail_id',
                    'pcd.price_per_meter',
                    'pcd.production_cost',
                    'pcd.finishing_cost'
                )
                ->get();

            foreach (
                $existingConfiguration
                as $existing
            ) {
                if (
                    (float) $existing->price_per_meter !=
                    (float) $row['price_per_meter'] ||
                    (float) $existing->production_cost !=
                    (float) $row['production_cost'] ||
                    (float) $existing->finishing_cost !=
                    (float) $row['finishing_cost']
                ) {
                    throw new Exception(
                        'Konfigurasi Production Cost untuk Engine, Lokasi, dan Material tersebut sudah tersedia dengan biaya yang berbeda. ' .
                            'Baris Excel ke-' .
                            $row['excel_row'] .
                            '.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CEK WIDTH YANG SUDAH ADA
            |--------------------------------------------------------------------------
            */

            if ($existingDetail) {
                $existingWidth =
                    DB::table(
                        'm_production_cost_detail_widths as pcdw'
                    )
                    ->where(
                        'pcdw.production_cost_detail_id',
                        $existingDetail->detail_id
                    )
                    ->where(
                        'pcdw.material_size_id',
                        $row['material_size_id']
                    )
                    ->exists();

                if ($existingWidth) {
                    throw new Exception(
                        'Data Production Cost dengan Engine, Lokasi, Material, dan Lebar yang sama sudah tersedia. ' .
                            'Baris Excel ke-' .
                            $row['excel_row'] .
                            '.'
                    );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $preparedRows
        ) {
            $groupedConfigurations =
                collect($preparedRows)
                ->groupBy(function ($row) {
                    return
                        $row['engine_id'] .
                        '|' .
                        $row['location_id'];
                });

            foreach (
                $groupedConfigurations
                as $configurationRows
            ) {
                $firstRow =
                    $configurationRows->first();

                /*
                |--------------------------------------------------------------------------
                | HEADER
                |--------------------------------------------------------------------------
                */

                $productionCost =
                    DB::table(
                        'm_production_costs as pc'
                    )
                    ->join(
                        'm_production_cost_locations as pcl',
                        'pc.id',
                        '=',
                        'pcl.production_cost_id'
                    )
                    ->where(
                        'pc.engine_id',
                        $firstRow['engine_id']
                    )
                    ->where(
                        'pcl.location_id',
                        $firstRow['location_id']
                    )
                    ->select(
                        'pc.*'
                    )
                    ->first();

                if (!$productionCost) {
                    $productionCostId =
                        DB::table(
                            'm_production_costs'
                        )->insertGetId([
                            'engine_id' =>
                            $firstRow['engine_id'],

                            'created_by' =>
                            Auth::user()->name,

                            'created_at' =>
                            now(),

                            'updated_by' =>
                            Auth::user()->name,

                            'updated_at' =>
                            now(),
                        ]);

                    DB::table(
                        'm_production_cost_locations'
                    )->insert([
                        'production_cost_id' =>
                        $productionCostId,

                        'location_id' =>
                        $firstRow['location_id'],

                        'created_by' =>
                        Auth::user()->name,

                        'created_at' =>
                        now(),

                        'updated_by' =>
                        Auth::user()->name,

                        'updated_at' =>
                        now(),
                    ]);
                } else {
                    $productionCostId =
                        $productionCost->id;
                }

                /*
                |--------------------------------------------------------------------------
                | GROUP MATERIAL
                |--------------------------------------------------------------------------
                */

                $groupedMaterials =
                    $configurationRows
                    ->groupBy(function ($row) {
                        return
                            $row['material_id'] .
                            '|' .
                            $row['price_per_meter'] .
                            '|' .
                            $row['production_cost'] .
                            '|' .
                            $row['finishing_cost'];
                    });

                foreach (
                    $groupedMaterials
                    as $materialRows
                ) {
                    $firstMaterialRow =
                        $materialRows->first();

                    /*
                    |--------------------------------------------------------------------------
                    | CEK DETAIL MATERIAL
                    |--------------------------------------------------------------------------
                    */

                    $detail =
                        DB::table(
                            'm_production_cost_details'
                        )
                        ->where(
                            'production_cost_id',
                            $productionCostId
                        )
                        ->where(
                            'material_id',
                            $firstMaterialRow['material_id']
                        )
                        ->first();

                    if (!$detail) {
                        $detailId =
                            DB::table(
                                'm_production_cost_details'
                            )->insertGetId([
                                'production_cost_id' =>
                                $productionCostId,

                                'material_id' =>
                                $firstMaterialRow['material_id'],

                                /*
                                |--------------------------------------------------------------------------
                                | PRICING RULE ID
                                |--------------------------------------------------------------------------
                                */

                                'general_pricing_rule_id' =>
                                $firstMaterialRow['general_pricing_rule_id'],

                                'division_pricing_rule_id' =>
                                $firstMaterialRow['division_pricing_rule_id'],

                                'plain_pricing_rule_id' =>
                                $firstMaterialRow['plain_pricing_rule_id'],

                                'price_per_meter' =>
                                $firstMaterialRow['price_per_meter'],

                                'production_cost' =>
                                $firstMaterialRow['production_cost'],

                                'finishing_cost' =>
                                $firstMaterialRow['finishing_cost'],

                                'total_cost' =>
                                $firstMaterialRow['total_cost'],

                                'general_price' =>
                                $firstMaterialRow['general_price'],

                                'division_price' =>
                                $firstMaterialRow['division_price'],

                                'plain_price' =>
                                $firstMaterialRow['plain_price'],

                                'created_by' =>
                                Auth::user()->name,

                                'created_at' =>
                                now(),

                                'updated_by' =>
                                Auth::user()->name,

                                'updated_at' =>
                                now(),
                            ]);
                    } else {
                        $detailId =
                            $detail->id;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | WIDTH
                    |--------------------------------------------------------------------------
                    */

                    foreach (
                        $materialRows
                        as $materialRow
                    ) {
                        $widthExists =
                            DB::table(
                                'm_production_cost_detail_widths'
                            )
                            ->where(
                                'production_cost_detail_id',
                                $detailId
                            )
                            ->where(
                                'material_size_id',
                                $materialRow['material_size_id']
                            )
                            ->exists();

                        if (
                            $widthExists
                        ) {
                            continue;
                        }

                        DB::table(
                            'm_production_cost_detail_widths'
                        )->insert([
                            'production_cost_detail_id' =>
                            $detailId,

                            'material_size_id' =>
                            $materialRow['material_size_id'],

                            'created_by' =>
                            Auth::user()->name,

                            'created_at' =>
                            now(),

                            'updated_by' =>
                            Auth::user()->name,

                            'updated_at' =>
                            now(),
                        ]);

                        $this->importedRows++;
                    }
                }
            }
        });
    }

    /**
     * DETEKSI FORMAT IMPORT OUTSOURCING
     *
     * Header Outsourcing:
     * No | Lokasi | Mesin | Nama Vendor | Nama Material | Lebar | Harga Vendor
     *
     * Kolom "No" tidak digunakan dalam proses import.
     */
    private function isOutsourcingImport($firstRow): bool
    {
        $headers = array_keys(
            $firstRow->toArray()
        );

        /*
        |--------------------------------------------------------------------------
        | OUTSOURCING SIGNATURE
        |--------------------------------------------------------------------------
        |
        | Template Outsourcing menggunakan:
        | No | Lokasi | Mesin | Nama Vendor | Kategori |
        | Nama Material | Lebar | Panjang | Harga Vendor
        |
        | Nama Vendor dan Harga Vendor merupakan header khusus
        | Outsourcing. Jika salah satunya ditemukan, file diarahkan
        | ke flow Outsourcing agar template yang tidak lengkap tetap
        | menghasilkan pesan validasi yang sesuai.
        |
        */

        return
            in_array('nama_vendor', $headers, true) ||
            in_array('harga_vendor', $headers, true);
    }

    /**
     * IMPORT OUTSOURCING
     *
     * Template:
     * No | Lokasi | Mesin | Nama Vendor | Kategori |
     * Nama Material | Lebar | Panjang | Harga Vendor
     *
     * Flow ini terpisah dari flow Non-Outsourcing existing.
     */
    private function importOutsourcing(Collection $rows): void
    {
        /*
        |--------------------------------------------------------------------------
        | HEADER WAJIB OUTSOURCING
        |--------------------------------------------------------------------------
        */

        $requiredHeaders = [
            'lokasi',
            'mesin',
            'nama_vendor',
            'kategori',
            'nama_material',
            'lebar',
            'panjang',
            'harga_vendor',
        ];

        $firstRow = $rows->first();
        $firstRowArray = $firstRow->toArray();

        foreach ($requiredHeaders as $header) {
            if (!array_key_exists($header, $firstRowArray)) {
                throw new Exception(
                    'Kolom "' .
                        $header .
                        '" tidak ditemukan pada template Import Outsourcing.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | LOAD MASTER DATA
        |--------------------------------------------------------------------------
        */

        $engines = DB::table('m_engines')
            ->get()
            ->keyBy(function ($engine) {
                return strtolower(
                    trim($engine->name)
                );
            });

        $locations = DB::table('m_locations')
            ->get()
            ->keyBy(function ($location) {
                return strtolower(
                    trim($location->name)
                );
            });

        $vendors = DB::table('m_vendors')
            ->where('status', 'Active')
            ->get()
            ->keyBy(function ($vendor) {
                return strtolower(
                    trim($vendor->name)
                );
            });

        $materials = DB::table('m_materials as m')
            ->join(
                'm_categories as c',
                'm.category_id',
                '=',
                'c.id'
            )
            ->select(
                'm.id',
                'm.material_name',
                'm.category_id',
                'm.status as material_status',
                'c.name as category_name'
            )
            ->get()
            ->keyBy(function ($material) {
                return strtolower(
                    trim($material->material_name)
                );
            });

        $categories = DB::table('m_categories')
            ->get()
            ->keyBy(function ($category) {
                return strtolower(
                    trim($category->name)
                );
            });

        $materialSizes = DB::table('m_material_sizes')
            ->get()
            ->groupBy('material_id');

        /*
        |--------------------------------------------------------------------------
        | PRICING RULES
        |--------------------------------------------------------------------------
        |
        | Outsourcing:
        | engine + location + vendor + category + price_type
        |
        */

        $pricingRules = DB::table('m_pricing_rules')
            ->where('status', 'Active')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | PREPARE DATA
        |--------------------------------------------------------------------------
        */

        $preparedRows = [];
        $dimensionUnit = null;

        foreach ($rows as $index => $row) {
            $excelRow = $index + 2;
            $rowArray = $row->toArray();

            /*
            |--------------------------------------------------------------------------
            | SKIP BARIS KOSONG
            |--------------------------------------------------------------------------
            */

            $isEmptyRow = true;

            foreach ($rowArray as $value) {
                if (
                    $value !== null &&
                    trim((string) $value) !== ''
                ) {
                    $isEmptyRow = false;
                    break;
                }
            }

            if ($isEmptyRow) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | REQUIRED VALUE
            |--------------------------------------------------------------------------
            |
            | Panjang boleh kosong untuk Outsourcing.
            | Jika diisi, format dan pencocokan master tetap divalidasi.
            |
            */

            $requiredValues = [
                'lokasi' => 'Lokasi',
                'mesin' => 'Mesin',
                'nama_vendor' => 'Nama Vendor',
                'kategori' => 'Kategori',
                'nama_material' => 'Nama Material',
                'lebar' => 'Lebar',
                'harga_vendor' => 'Harga Vendor',
            ];

            foreach ($requiredValues as $field => $label) {
                if (
                    !isset($row[$field]) ||
                    trim((string) $row[$field]) === ''
                ) {
                    throw new Exception(
                        $label .
                            ' wajib diisi pada baris Excel ke-' .
                            $excelRow .
                            '.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | ENGINE
            |--------------------------------------------------------------------------
            */

            $engineName = trim(
                (string) $row['mesin']
            );

            $engine = $engines->get(
                strtolower($engineName)
            );

            if (!$engine) {
                throw new Exception(
                    'Mesin "' .
                        $engineName .
                        '" tidak ditemukan pada master mesin. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            if (
                strtolower(
                    trim($engine->status)
                ) !== 'active'
            ) {
                throw new Exception(
                    'Mesin "' .
                        $engine->name .
                        '" tidak aktif. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            $engineType = strtolower(
                trim($engine->name)
            );

            if ($engineType === 'digital print a3+') {
                throw new Exception(
                    'Mesin Digital Print A3+ belum mempunyai master calculation rules.'
                );
            }

            if ($engineType !== 'large format') {
                throw new Exception(
                    'Mesin "' .
                        $engine->name .
                        '" belum mempunyai master calculation rules.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | LOCATION
            |--------------------------------------------------------------------------
            */

            $locationName = trim(
                (string) $row['lokasi']
            );

            $location = $locations->get(
                strtolower($locationName)
            );

            if (!$location) {
                throw new Exception(
                    'Lokasi "' .
                        $locationName .
                        '" tidak ditemukan pada master lokasi. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            if (
                strtolower(
                    trim($location->status)
                ) !== 'active'
            ) {
                throw new Exception(
                    'Lokasi "' .
                        $location->name .
                        '" tidak aktif. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            if (
                strtolower(
                    trim($location->name)
                ) !== 'outsourcing'
            ) {
                throw new Exception(
                    'Template Outsourcing hanya dapat digunakan untuk lokasi Outsourcing. ' .
                        'Lokasi "' .
                        $location->name .
                        '" ditemukan pada baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | VENDOR
            |--------------------------------------------------------------------------
            */

            $vendorName = trim(
                (string) $row['nama_vendor']
            );

            $vendor = $vendors->get(
                strtolower($vendorName)
            );

            if (!$vendor) {
                throw new Exception(
                    'Vendor "' .
                        $vendorName .
                        '" tidak ditemukan pada master vendor atau tidak aktif. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | MATERIAL
            |--------------------------------------------------------------------------
            */

            $materialName = trim(
                (string) $row['nama_material']
            );

            $material = $materials->get(
                strtolower($materialName)
            );

            if (!$material) {
                throw new Exception(
                    'Material "' .
                        $materialName .
                        '" tidak ditemukan pada master material. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            if (
                strtolower(
                    trim($material->material_status)
                ) !== 'active'
            ) {
                throw new Exception(
                    'Material "' .
                        $material->material_name .
                        '" tidak aktif. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | KATEGORI
            |--------------------------------------------------------------------------
            |
            | Kategori pada Excel wajib sama dengan kategori master Material.
            |
            */

            $categoryName = trim(
                (string) $row['kategori']
            );

            $category = $categories->get(
                strtolower($categoryName)
            );

            if (!$category) {
                throw new Exception(
                    'Kategori "' .
                        $categoryName .
                        '" tidak ditemukan pada master kategori. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            if (
                (int) $category->id !==
                (int) $material->category_id
            ) {
                throw new Exception(
                    'Kategori "' .
                        $categoryName .
                        '" tidak sesuai dengan kategori Material "' .
                        $material->material_name .
                        '". Kategori material yang terdaftar adalah "' .
                        $material->category_name .
                        '". Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | MATERIAL SIZE
            |--------------------------------------------------------------------------
            */

            $materialSizeList =
                $materialSizes->get(
                    $material->id,
                    collect()
                );

            if ($materialSizeList->isEmpty()) {
                throw new Exception(
                    'Material "' .
                        $material->material_name .
                        '" belum memiliki master ukuran material. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | LEBAR
            |--------------------------------------------------------------------------
            |
            | Format wajib: angka + satu spasi + cm/m.
            | Contoh valid: 127 cm, 1.27 m
            | Contoh tidak valid: 127cm, 127, 127 centimeter
            |
            | Lebar tetap boleh berisi beberapa ukuran yang dipisahkan koma.
            |
            */

            $widthValues = preg_split(
                '/\s*,\s*/',
                trim((string) $row['lebar'])
            );

            $selectedWidths = [];

            foreach ($widthValues as $widthValue) {
                $widthValue = trim($widthValue);

                if ($widthValue === '') {
                    continue;
                }

                $widthDimension =
                    $this->parseDimension(
                        $widthValue,
                        'Lebar',
                        $excelRow
                    );

                $widthNumber = $widthDimension['value'];
                $widthUnit = $widthDimension['unit'];

                if ($dimensionUnit === null) {
                    $dimensionUnit = $widthUnit;
                } elseif ($dimensionUnit !== $widthUnit) {
                    throw new Exception(
                        'Satuan Lebar harus konsisten dalam satu file Excel. ' .
                            'Satuan sebelumnya adalah "' .
                            $dimensionUnit .
                            '", sedangkan ditemukan "' .
                            $widthUnit .
                            '" pada baris Excel ke-' .
                            $excelRow .
                            '.'
                    );
                }

                $matchedSizes = [];

                foreach ($materialSizeList as $materialSize) {
                    $masterWidth = (float) $materialSize->width;
                    $masterUnit = strtolower(
                        trim((string) $materialSize->unit)
                    );

                    if (
                        abs(
                            $this->convertDimension(
                                $widthNumber,
                                $widthUnit,
                                $masterUnit
                            ) - $masterWidth
                        ) < 0.00001
                    ) {
                        $matchedSizes[] = $materialSize;
                    }
                }

                if (empty($matchedSizes)) {
                    throw new Exception(
                        'Lebar "' .
                            $widthValue .
                            '" tidak ditemukan pada master ukuran material "' .
                            $material->material_name .
                            '". Baris Excel ke-' .
                            $excelRow .
                            '.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | PANJANG
                |--------------------------------------------------------------------------
                |
                | Jika Panjang kosong, hanya master dengan Panjang NULL/kosong
                | yang boleh dipilih.
                |
                | Jika Panjang diisi, pencarian size harus cocok dengan:
                | Lebar + Panjang.
                |
                */

                $lengthValue = trim(
                    (string) ($row['panjang'] ?? '')
                );

                $matchedSize = null;

                if ($lengthValue === '') {
                    foreach ($matchedSizes as $materialSize) {
                        if (
                            $materialSize->length === null ||
                            trim((string) $materialSize->length) === ''
                        ) {
                            if (!$matchedSize) {
                                $matchedSize = $materialSize;
                            } else {
                                throw new Exception(
                                    'Ukuran material dengan Lebar "' .
                                        $widthValue .
                                        '" memiliki lebih dari satu master ukuran dengan Panjang kosong. ' .
                                        'Baris Excel ke-' .
                                        $excelRow .
                                        '.'
                                );
                            }
                        }
                    }
                } else {
                    $lengthDimension =
                        $this->parseDimension(
                            $lengthValue,
                            'Panjang',
                            $excelRow
                        );

                    $lengthNumber = $lengthDimension['value'];
                    $lengthUnit = $lengthDimension['unit'];

                    if ($dimensionUnit === null) {
                        $dimensionUnit = $lengthUnit;
                    } elseif ($dimensionUnit !== $lengthUnit) {
                        throw new Exception(
                            'Satuan Panjang harus sama dengan satuan Lebar dalam satu file Excel. ' .
                                'Lebar menggunakan "' .
                                $dimensionUnit .
                                '", sedangkan Panjang menggunakan "' .
                                $lengthUnit .
                                '" pada baris Excel ke-' .
                                $excelRow .
                                '.'
                        );
                    }

                    foreach ($matchedSizes as $materialSize) {
                        if (
                            $materialSize->length === null ||
                            trim((string) $materialSize->length) === ''
                        ) {
                            continue;
                        }

                        $masterLength = (float) $materialSize->length;
                        $masterUnit = strtolower(
                            trim((string) $materialSize->unit)
                        );

                        if (
                            abs(
                                $this->convertDimension(
                                    $lengthNumber,
                                    $lengthUnit,
                                    $masterUnit
                                ) - $masterLength
                            ) < 0.00001
                        ) {
                            if (!$matchedSize) {
                                $matchedSize = $materialSize;
                            } else {
                                throw new Exception(
                                    'Ukuran material dengan Lebar "' .
                                        $widthValue .
                                        '" dan Panjang "' .
                                        $lengthValue .
                                        '" memiliki lebih dari satu master ukuran yang cocok. ' .
                                        'Baris Excel ke-' .
                                        $excelRow .
                                        '.'
                                );
                            }
                        }
                    }
                }

                if (!$matchedSize) {
                    $dimensionText =
                        $lengthValue === ''
                        ? 'Lebar "' . $widthValue . '" dengan Panjang kosong'
                        : 'Lebar "' . $widthValue . '" dan Panjang "' . $lengthValue . '"';

                    throw new Exception(
                        $dimensionText .
                            ' tidak ditemukan pada master ukuran material "' .
                            $material->material_name .
                            '". Baris Excel ke-' .
                            $excelRow .
                            '.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | DUPLIKAT SIZE DALAM SATU BARIS EXCEL
                |--------------------------------------------------------------------------
                */

                if (
                    isset(
                        $selectedWidths[$matchedSize->id]
                    )
                ) {
                    throw new Exception(
                        'Ukuran material dengan Lebar "' .
                            $widthValue .
                            '" ditemukan lebih dari satu kali pada baris Excel ke-' .
                            $excelRow .
                            '.'
                    );
                }

                $selectedWidths[$matchedSize->id] = $matchedSize;
            }

            if (empty($selectedWidths)) {
                throw new Exception(
                    'Tidak ada ukuran material yang valid pada baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | HARGA VENDOR
            |--------------------------------------------------------------------------
            */

            $vendorPrice =
                $this->parseNumber(
                    $row['harga_vendor']
                );

            if ($vendorPrice <= 0) {
                throw new Exception(
                    'Harga Vendor harus lebih besar dari 0. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | PRICING RULES OUTSOURCING
            |--------------------------------------------------------------------------
            |
            | Engine + Location + Vendor + Category + Price Type
            |
            */

            $generalRule =
                $pricingRules->first(
                    function ($rule) use (
                        $engine,
                        $location,
                        $vendor,
                        $material
                    ) {
                        return
                            (int) $rule->engine_id ===
                            (int) $engine->id &&
                            (int) $rule->location_id ===
                            (int) $location->id &&
                            (int) $rule->vendor_id ===
                            (int) $vendor->id &&
                            (int) $rule->category_id ===
                            (int) $material->category_id &&
                            $rule->price_type ===
                            'general';
                    }
                );

            $divisionRule =
                $pricingRules->first(
                    function ($rule) use (
                        $engine,
                        $location,
                        $vendor,
                        $material
                    ) {
                        return
                            (int) $rule->engine_id ===
                            (int) $engine->id &&
                            (int) $rule->location_id ===
                            (int) $location->id &&
                            (int) $rule->vendor_id ===
                            (int) $vendor->id &&
                            (int) $rule->category_id ===
                            (int) $material->category_id &&
                            $rule->price_type ===
                            'division';
                    }
                );

            if (!$generalRule) {
                throw new Exception(
                    'Pricing Rule Harga Umum untuk lokasi Outsourcing, vendor, dan kategori material tersebut tidak ditemukan. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            if (!$divisionRule) {
                throw new Exception(
                    'Pricing Rule Harga Divisi untuk lokasi Outsourcing, vendor, dan kategori material tersebut tidak ditemukan. ' .
                        'Baris Excel ke-' .
                        $excelRow .
                        '.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | OUTSOURCING PRICE CALCULATION
            |--------------------------------------------------------------------------
            |
            | total_cost = vendor_price
            |
            | general_price =
            | vendor_price + (vendor_price * general markup)
            |
            | division_price =
            | general_price * division markup
            |
            | plain_price = 0
            |
            */

            $generalMarkup =
                (float) $generalRule->markup_percentage;

            $generalRounding =
                $generalRule->rounding_value !== null
                ? (float) $generalRule->rounding_value
                : null;

            $generalPrice =
                $vendorPrice +
                (
                    $vendorPrice *
                    ($generalMarkup / 100)
                );

            $generalPrice =
                $this->roundUp(
                    $generalPrice,
                    $generalRounding
                );

            $divisionMarkup =
                (float) $divisionRule->markup_percentage;

            $divisionRounding =
                $divisionRule->rounding_value !== null
                ? (float) $divisionRule->rounding_value
                : null;

            $divisionPrice =
                $generalPrice *
                ($divisionMarkup / 100);

            $divisionPrice =
                $this->roundUp(
                    $divisionPrice,
                    $divisionRounding
                );

            /*
            |--------------------------------------------------------------------------
            | PREPARE ROW PER SIZE
            |--------------------------------------------------------------------------
            */

            foreach ($selectedWidths as $materialSize) {
                $preparedRows[] = [
                    'engine_id' =>
                    $engine->id,

                    'location_id' =>
                    $location->id,

                    'vendor_id' =>
                    $vendor->id,

                    'material_id' =>
                    $material->id,

                    'material_size_id' =>
                    $materialSize->id,

                    'price_per_meter' =>
                    0,

                    'production_cost' =>
                    0,

                    'finishing_cost' =>
                    0,

                    'vendor_price' =>
                    $vendorPrice,

                    'total_cost' =>
                    $vendorPrice,

                    'general_price' =>
                    $generalPrice,

                    'division_price' =>
                    $divisionPrice,

                    'plain_price' =>
                    0,

                    'general_pricing_rule_id' =>
                    $generalRule->id,

                    'division_pricing_rule_id' =>
                    $divisionRule->id,

                    'plain_pricing_rule_id' =>
                    null,

                    'excel_row' =>
                    $excelRow,
                ];
            }
        }

        if (empty($preparedRows)) {
            throw new Exception(
                'Tidak ada data valid yang dapat diimport.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI KONSISTENSI HARGA VENDOR DALAM EXCEL
        |--------------------------------------------------------------------------
        |
        | Engine + Location + Vendor + Material
        |
        | Harga Vendor harus sama walaupun width/panjang berbeda.
        |
        */

        $vendorPriceConfigurations = [];

        foreach ($preparedRows as $row) {
            $key =
                $row['engine_id'] .
                '|' .
                $row['location_id'] .
                '|' .
                $row['vendor_id'] .
                '|' .
                $row['material_id'];

            if (isset($vendorPriceConfigurations[$key])) {
                if (
                    (float) $vendorPriceConfigurations[$key] !=
                    (float) $row['vendor_price']
                ) {
                    throw new Exception(
                        'Harga Vendor untuk kombinasi Engine, Lokasi, Vendor, dan Material yang sama harus konsisten. ' .
                            'Masalah ditemukan pada baris Excel ke-' .
                            $row['excel_row'] .
                            '.'
                    );
                }
            } else {
                $vendorPriceConfigurations[$key] =
                    $row['vendor_price'];
            }
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DUPLIKAT SIZE DALAM EXCEL
        |--------------------------------------------------------------------------
        */

        $excelSizes = [];

        foreach ($preparedRows as $row) {
            $key =
                $row['engine_id'] .
                '|' .
                $row['location_id'] .
                '|' .
                $row['vendor_id'] .
                '|' .
                $row['material_id'] .
                '|' .
                $row['material_size_id'];

            if (isset($excelSizes[$key])) {
                throw new Exception(
                    'Data dengan Engine, Lokasi, Vendor, Material, dan ukuran yang sama ditemukan lebih dari satu kali dalam Excel. ' .
                        'Baris Excel ke-' .
                        $row['excel_row'] .
                        '.'
                );
            }

            $excelSizes[$key] = true;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DATABASE
        |--------------------------------------------------------------------------
        |
        | Konfigurasi existing boleh digunakan kembali jika Harga Vendor sama.
        | Size baru boleh ditambahkan.
        | Size yang sudah ada tidak boleh diimport ulang.
        |
        */

        foreach ($preparedRows as $row) {
            /*
            |--------------------------------------------------------------------------
            | EXISTING CONFIGURATION DENGAN HARGA VENDOR SAMA
            |--------------------------------------------------------------------------
            |
            | Ambil semua detail yang cocok, bukan ->first(), supaya pengecekan
            | size tidak hanya bergantung pada satu detail.
            |
            */

            $existingDetails =
                DB::table(
                    'm_production_costs as pc'
                )
                ->join(
                    'm_production_cost_locations as pcl',
                    'pc.id',
                    '=',
                    'pcl.production_cost_id'
                )
                ->join(
                    'm_production_cost_details as pcd',
                    'pc.id',
                    '=',
                    'pcd.production_cost_id'
                )
                ->where(
                    'pc.engine_id',
                    $row['engine_id']
                )
                ->where(
                    'pcl.location_id',
                    $row['location_id']
                )
                ->where(
                    'pcd.material_id',
                    $row['material_id']
                )
                ->where(
                    'pcd.vendor_id',
                    $row['vendor_id']
                )
                ->where(
                    'pcd.vendor_price',
                    $row['vendor_price']
                )
                ->select(
                    'pc.id as production_cost_id',
                    'pcd.id as detail_id'
                )
                ->get();

            /*
            |--------------------------------------------------------------------------
            | EXISTING CONFIGURATION DENGAN HARGA VENDOR BERBEDA
            |--------------------------------------------------------------------------
            */

            $existingConfigurations =
                DB::table(
                    'm_production_costs as pc'
                )
                ->join(
                    'm_production_cost_locations as pcl',
                    'pc.id',
                    '=',
                    'pcl.production_cost_id'
                )
                ->join(
                    'm_production_cost_details as pcd',
                    'pc.id',
                    '=',
                    'pcd.production_cost_id'
                )
                ->where(
                    'pc.engine_id',
                    $row['engine_id']
                )
                ->where(
                    'pcl.location_id',
                    $row['location_id']
                )
                ->where(
                    'pcd.material_id',
                    $row['material_id']
                )
                ->where(
                    'pcd.vendor_id',
                    $row['vendor_id']
                )
                ->select(
                    'pc.id as production_cost_id',
                    'pcd.id as detail_id',
                    'pcd.vendor_price'
                )
                ->get();

            foreach ($existingConfigurations as $existing) {
                if (
                    (float) $existing->vendor_price !=
                    (float) $row['vendor_price']
                ) {
                    throw new Exception(
                        'Konfigurasi Production Cost untuk Engine, Lokasi, Vendor, dan Material tersebut sudah tersedia dengan Harga Vendor yang berbeda. ' .
                            'Baris Excel ke-' .
                            $row['excel_row'] .
                            '.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CEK SIZE YANG SUDAH ADA
            |--------------------------------------------------------------------------
            |
            | Pengecekan dilakukan ke seluruh detail yang cocok.
            |
            */

            foreach ($existingDetails as $existingDetail) {
                $existingSize =
                    DB::table(
                        'm_production_cost_detail_widths as pcdw'
                    )
                    ->where(
                        'pcdw.production_cost_detail_id',
                        $existingDetail->detail_id
                    )
                    ->where(
                        'pcdw.material_size_id',
                        $row['material_size_id']
                    )
                    ->exists();

                if ($existingSize) {
                    throw new Exception(
                        'Data Production Cost dengan Engine, Lokasi, Vendor, Material, dan ukuran yang sama sudah tersedia. ' .
                            'Baris Excel ke-' .
                            $row['excel_row'] .
                            '.'
                    );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SAVE
        |--------------------------------------------------------------------------
        |
        | Vendor menjadi bagian dari konfigurasi Outsourcing.
        | Karena vendor disimpan pada detail, konfigurasi berbeda vendor
        | dipisahkan menjadi header Production Cost yang berbeda.
        |
        */

        DB::transaction(function () use (
            $preparedRows
        ) {
            $groupedConfigurations =
                collect($preparedRows)
                ->groupBy(function ($row) {
                    return
                        $row['engine_id'] .
                        '|' .
                        $row['location_id'] .
                        '|' .
                        $row['vendor_id'];
                });

            foreach (
                $groupedConfigurations
                as $configurationRows
            ) {
                $firstRow =
                    $configurationRows->first();

                /*
                |--------------------------------------------------------------------------
                | HEADER
                |--------------------------------------------------------------------------
                */

                $productionCost =
                    DB::table(
                        'm_production_costs as pc'
                    )
                    ->join(
                        'm_production_cost_locations as pcl',
                        'pc.id',
                        '=',
                        'pcl.production_cost_id'
                    )
                    ->join(
                        'm_production_cost_details as pcd',
                        'pc.id',
                        '=',
                        'pcd.production_cost_id'
                    )
                    ->where(
                        'pc.engine_id',
                        $firstRow['engine_id']
                    )
                    ->where(
                        'pcl.location_id',
                        $firstRow['location_id']
                    )
                    ->where(
                        'pcd.vendor_id',
                        $firstRow['vendor_id']
                    )
                    ->select(
                        'pc.*'
                    )
                    ->first();

                if (!$productionCost) {
                    $productionCostId =
                        DB::table(
                            'm_production_costs'
                        )->insertGetId([
                            'engine_id' =>
                            $firstRow['engine_id'],

                            'created_by' =>
                            Auth::user()->name,

                            'created_at' =>
                            now(),

                            'updated_by' =>
                            Auth::user()->name,

                            'updated_at' =>
                            now(),
                        ]);

                    DB::table(
                        'm_production_cost_locations'
                    )->insert([
                        'production_cost_id' =>
                        $productionCostId,

                        'location_id' =>
                        $firstRow['location_id'],

                        'created_by' =>
                        Auth::user()->name,

                        'created_at' =>
                        now(),

                        'updated_by' =>
                        Auth::user()->name,

                        'updated_at' =>
                        now(),
                    ]);
                } else {
                    $productionCostId =
                        $productionCost->id;
                }

                /*
                |--------------------------------------------------------------------------
                | GROUP MATERIAL
                |--------------------------------------------------------------------------
                */

                $groupedMaterials =
                    $configurationRows
                    ->groupBy('material_id');

                foreach (
                    $groupedMaterials
                    as $materialRows
                ) {
                    $firstMaterialRow =
                        $materialRows->first();

                    /*
                    |--------------------------------------------------------------------------
                    | DETAIL
                    |--------------------------------------------------------------------------
                    */

                    $detail =
                        DB::table(
                            'm_production_cost_details'
                        )
                        ->where(
                            'production_cost_id',
                            $productionCostId
                        )
                        ->where(
                            'material_id',
                            $firstMaterialRow['material_id']
                        )
                        ->where(
                            'vendor_id',
                            $firstMaterialRow['vendor_id']
                        )
                        ->first();

                    if (!$detail) {
                        $detailId =
                            DB::table(
                                'm_production_cost_details'
                            )->insertGetId([
                                'production_cost_id' =>
                                $productionCostId,

                                'material_id' =>
                                $firstMaterialRow['material_id'],

                                'general_pricing_rule_id' =>
                                $firstMaterialRow['general_pricing_rule_id'],

                                'division_pricing_rule_id' =>
                                $firstMaterialRow['division_pricing_rule_id'],

                                'plain_pricing_rule_id' =>
                                $firstMaterialRow['plain_pricing_rule_id'],

                                'price_per_meter' =>
                                $firstMaterialRow['price_per_meter'],

                                'production_cost' =>
                                $firstMaterialRow['production_cost'],

                                'finishing_cost' =>
                                $firstMaterialRow['finishing_cost'],

                                'total_cost' =>
                                $firstMaterialRow['total_cost'],

                                'general_price' =>
                                $firstMaterialRow['general_price'],

                                'division_price' =>
                                $firstMaterialRow['division_price'],

                                'plain_price' =>
                                $firstMaterialRow['plain_price'],

                                'vendor_price' =>
                                $firstMaterialRow['vendor_price'],

                                'vendor_id' =>
                                $firstMaterialRow['vendor_id'],

                                'created_by' =>
                                Auth::user()->name,

                                'created_at' =>
                                now(),

                                'updated_by' =>
                                Auth::user()->name,

                                'updated_at' =>
                                now(),
                            ]);
                    } else {
                        $detailId =
                            $detail->id;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | WIDTH
                    |--------------------------------------------------------------------------
                    */

                    foreach (
                        $materialRows
                        as $materialRow
                    ) {
                        $widthExists =
                            DB::table(
                                'm_production_cost_detail_widths'
                            )
                            ->where(
                                'production_cost_detail_id',
                                $detailId
                            )
                            ->where(
                                'material_size_id',
                                $materialRow['material_size_id']
                            )
                            ->exists();

                        if ($widthExists) {
                            continue;
                        }

                        DB::table(
                            'm_production_cost_detail_widths'
                        )->insert([
                            'production_cost_detail_id' =>
                            $detailId,

                            'material_size_id' =>
                            $materialRow['material_size_id'],

                            'created_by' =>
                            Auth::user()->name,

                            'created_at' =>
                            now(),

                            'updated_by' =>
                            Auth::user()->name,

                            'updated_at' =>
                            now(),
                        ]);

                        $this->importedRows++;
                    }
                }
            }
        });
    }

    /**
     * PARSE DIMENSION
     *
     * Format wajib:
     * angka + satu spasi + cm/m
     *
     * Contoh valid:
     * 127 cm
     * 1.27 m
     * 127,5 cm
     *
     * Contoh tidak valid:
     * 127cm
     * 127
     * 127 centimeter
     */
    private function parseDimension(
        string $value,
        string $label,
        int $excelRow
    ): array {
        if (
            !preg_match(
                '/^(\d+(?:[.,]\d+)?) (cm|m)$/i',
                trim($value),
                $matches
            )
        ) {
            throw new Exception(
                'Format ' .
                    $label .
                    ' "' .
                    $value .
                    '" tidak valid. Gunakan format angka + satu spasi + cm/m, contoh "127 cm" atau "1.27 m". ' .
                    'Baris Excel ke-' .
                    $excelRow .
                    '.'
            );
        }

        $number = trim($matches[1]);
        $unit = strtolower(
            trim($matches[2])
        );

        /*
        |--------------------------------------------------------------------------
        | DIMENSION NUMBER
        |--------------------------------------------------------------------------
        |
        | Untuk ukuran, titik/koma diperlakukan sebagai desimal.
        | Ini berbeda dengan parseNumber() yang digunakan untuk harga.
        |
        */

        if (str_contains($number, ',') && str_contains($number, '.')) {
            $number = str_replace('.', '', $number);
            $number = str_replace(',', '.', $number);
        } elseif (str_contains($number, ',')) {
            $number = str_replace(',', '.', $number);
        }

        $number = (float) $number;

        if ($number <= 0) {
            throw new Exception(
                $label .
                    ' harus lebih besar dari 0. ' .
                    'Baris Excel ke-' .
                    $excelRow .
                    '.'
            );
        }

        return [
            'value' => $number,
            'unit' => $unit,
        ];
    }

    /**
     * CONVERT DIMENSION
     *
     * Digunakan agar input cm/m tetap dapat dicocokkan dengan unit
     * yang tersimpan pada master m_material_sizes.
     */
    private function convertDimension(
        float $value,
        string $fromUnit,
        string $toUnit
    ): float {
        $fromUnit = strtolower(trim($fromUnit));
        $toUnit = strtolower(trim($toUnit));

        if ($fromUnit === $toUnit) {
            return $value;
        }

        if ($fromUnit === 'cm' && $toUnit === 'm') {
            return $value / 100;
        }

        if ($fromUnit === 'm' && $toUnit === 'cm') {
            return $value * 100;
        }

        return $value;
    }

    /**
     * PARSE NUMBER
     */
    private function parseNumber($value): float
    {
        if (
            $value === null ||
            trim((string) $value) === ''
        ) {
            return 0;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $value = trim(
            (string) $value
        );

        /*
        |--------------------------------------------------------------------------
        | FORMAT INDONESIA
        |--------------------------------------------------------------------------
        |
        | Contoh:
        | 1.500.000,50
        |
        */

        if (str_contains($value, ',')) {
            $value = str_replace(
                '.',
                '',
                $value
            );

            $value = str_replace(
                ',',
                '.',
                $value
            );
        } else {
            /*
            |--------------------------------------------------------------------------
            | Jika tidak ada koma, titik dianggap
            | sebagai pemisah ribuan.
            |--------------------------------------------------------------------------
            */

            $value = str_replace(
                '.',
                '',
                $value
            );
        }

        return (float) $value;
    }

    /**
     * ROUND UP
     */
    private function roundUp(
        ?float $value,
        ?float $rounding
    ): float {
        if (
            $value === null ||
            $rounding === null ||
            $rounding <= 0
        ) {
            return $value ?? 0;
        }

        return ceil(
            $value / $rounding
        ) * $rounding;
    }

    /**
     * CONFIGURATION COST KEY
     */
    private function configurationCostKey(
        array $row
    ): string {
        return
            $row['engine_id'] .
            '|' .
            $row['location_id'] .
            '|' .
            $row['material_id'];
    }

    /**
     * CONFIGURATION WIDTH KEY
     */
    private function configurationWidthKey(
        array $row
    ): string {
        return
            $row['engine_id'] .
            '|' .
            $row['location_id'] .
            '|' .
            $row['material_id'] .
            '|' .
            $row['material_size_id'];
    }
}

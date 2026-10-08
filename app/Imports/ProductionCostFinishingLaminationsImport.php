<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductionCostFinishingLaminationsImport implements ToCollection, WithHeadingRow
{
    public int $importedRows = 0;

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            throw new \Exception(
                'File Excel tidak memiliki data.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Deteksi Template
        |--------------------------------------------------------------------------
        |
        | Non-Outsourcing:
        |
        | No | Lokasi | Engine | Kategori | Nama Laminasi |
        | Lebar | Panjang | Harga Per Meter |
        | Ongkos Produksi | Ongkos Finishing
        |
        | Outsourcing:
        |
        | No | Lokasi | Mesin | Nama Vendor | Kategori |
        | Nama Laminasi | Lebar | Panjang | Harga Vendor
        |
        */

        $firstRow = $rows->first()->toArray();

        $isOutsourcingTemplate =
            array_key_exists('mesin', $firstRow) &&
            array_key_exists('nama_vendor', $firstRow) &&
            array_key_exists('harga_vendor', $firstRow);

        $isNonOutsourcingTemplate =
            array_key_exists('engine', $firstRow) &&
            array_key_exists('harga_per_meter', $firstRow) &&
            array_key_exists('ongkos_produksi', $firstRow) &&
            array_key_exists('ongkos_finishing', $firstRow);

        if (
            !$isOutsourcingTemplate &&
            !$isNonOutsourcingTemplate
        ) {
            throw new \Exception(
                'Format template Excel tidak dikenali. Pastikan menggunakan template Production Cost Laminasi yang sesuai.'
            );
        }

        if (
            $isOutsourcingTemplate &&
            $isNonOutsourcingTemplate
        ) {
            throw new \Exception(
                'Format template Excel tidak valid. Jangan mencampurkan struktur template Outsourcing dan Non-Outsourcing.'
            );
        }

        $isOutsourcing = $isOutsourcingTemplate;

        /*
        |--------------------------------------------------------------------------
        | Header Wajib
        |--------------------------------------------------------------------------
        */

        if ($isOutsourcing) {

            $requiredHeaders = [
                'lokasi',
                'mesin',
                'nama_vendor',
                'kategori',
                'nama_laminasi',
                'lebar',
                'panjang',
                'harga_vendor',
            ];
        } else {

            $requiredHeaders = [
                'lokasi',
                'engine',
                'kategori',
                'nama_laminasi',
                'lebar',
                'panjang',
                'harga_per_meter',
                'ongkos_produksi',
                'ongkos_finishing',
            ];
        }

        foreach ($requiredHeaders as $header) {

            if (!array_key_exists($header, $firstRow)) {
                throw new \Exception(
                    "Kolom '{$header}' wajib tersedia di file Excel."
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Helper Parse Number
        |--------------------------------------------------------------------------
        */

        $parseNumber = function ($value) {

            if ($value === null || $value === '') {
                return 0;
            }

            $value = trim((string) $value);

            if ($value === '') {
                return 0;
            }

            if (strpos($value, ',') !== false) {

                $value = str_replace('.', '', $value);
                $value = str_replace(',', '.', $value);
            } else {

                if (
                    substr_count($value, '.') > 1 ||
                    preg_match(
                        '/^\d{1,3}(\.\d{3})+$/',
                        $value
                    )
                ) {
                    $value = str_replace('.', '', $value);
                }
            }

            if (!is_numeric($value)) {
                return 0;
            }

            return (float) $value;
        };

        /*
|--------------------------------------------------------------------------
| Helper Parse Ukuran
|--------------------------------------------------------------------------
|
| Format wajib:
| 127 cm
| 152 cm
| 1.27 m
|
| Format seperti:
| 127cm
| 127
| 127 inch
|
| akan ditolak.
|
*/

        $parseDimension = function ($value, $fieldName, $rowNumber) {

            if (
                $value === null ||
                trim((string) $value) === ''
            ) {
                return null;
            }

            $value = trim((string) $value);

            /*
    |--------------------------------------------------------------------------
    | Format wajib:
    |
    | angka + satu spasi + cm/m
    |
    */

            if (
                !preg_match(
                    '/^(\d+(?:[.,]\d+)?) (cm|m)$/',
                    $value,
                    $matches
                )
            ) {

                throw new \Exception(
                    "Baris {$rowNumber}: {$fieldName} harus menggunakan format angka + spasi + satuan (contoh: 127 cm atau 1.27 m)."
                );
            }

            $number = $matches[1];
            $unit = $matches[2];

            /*
    |--------------------------------------------------------------------------
    | Normalisasi angka
    |--------------------------------------------------------------------------
    */

            $number = str_replace(',', '.', $number);

            if (!is_numeric($number)) {

                throw new \Exception(
                    "Baris {$rowNumber}: {$fieldName} memiliki nilai angka yang tidak valid."
                );
            }

            $number = (float) $number;

            if ($number <= 0) {

                throw new \Exception(
                    "Baris {$rowNumber}: {$fieldName} harus lebih besar dari 0."
                );
            }

            return [
                'value' => $number,
                'unit' => $unit,
            ];
        };

        $dimensionUnit = null;

        /*
        |--------------------------------------------------------------------------
        | Helper Rounding
        |--------------------------------------------------------------------------
        */

        $roundUp = function ($value, $rounding) {

            if (
                $rounding === null ||
                $rounding <= 0
            ) {
                return $value;
            }

            return ceil(
                $value / $rounding
            ) * $rounding;
        };

        /*
        |--------------------------------------------------------------------------
        | Load Master Engine
        |--------------------------------------------------------------------------
        */

        $engines = DB::table('m_engines')
            ->get()
            ->keyBy(function ($item) {

                return strtolower(
                    trim($item->name)
                );
            });

        /*
        |--------------------------------------------------------------------------
        | Load Master Location
        |--------------------------------------------------------------------------
        */

        $locations = DB::table('m_locations')
            ->get()
            ->keyBy(function ($item) {

                return strtolower(
                    trim($item->name)
                );
            });

        /*
        |--------------------------------------------------------------------------
        | Load Master Category
        |--------------------------------------------------------------------------
        */

        $categories = DB::table('m_categories')
            ->get()
            ->keyBy(function ($item) {

                return strtolower(
                    trim($item->name)
                );
            });

        /*
        |--------------------------------------------------------------------------
        | Load Master Laminasi
        |--------------------------------------------------------------------------
        */

        $laminations = DB::table('m_laminations')
            ->where('status', 'Active')
            ->get()
            ->keyBy(function ($item) {

                return strtolower(
                    trim($item->name)
                );
            });

        /*
        |--------------------------------------------------------------------------
        | Load Master Vendor
        |--------------------------------------------------------------------------
        */

        $vendors = DB::table('m_vendors')
            ->where('status', 'Active')
            ->get()
            ->keyBy(function ($item) {

                return strtolower(
                    trim($item->name)
                );
            });

        /*
        |--------------------------------------------------------------------------
        | Load Master Ukuran Laminasi
        |--------------------------------------------------------------------------
        */

        $laminationSizes = DB::table('m_lamination_sizes')
            ->get()
            ->groupBy('lamination_id');

        /*
        |--------------------------------------------------------------------------
        | Load Pricing Rules
        |--------------------------------------------------------------------------
        |
        | Non-Outsourcing:
        |
        | engine_id
        | location_id
        | vendor_id = NULL
        | category_id
        | price_type
        |
        | Outsourcing:
        |
        | engine_id
        | location_id
        | vendor_id
        | category_id
        | price_type
        |
        */

        $pricingRules = DB::table('m_lamination_pricing_rules')
            ->where('status', 'Active')
            ->get()
            ->keyBy(function ($item) {

                return
                    $item->engine_id . '|' .
                    $item->location_id . '|' .
                    ($item->vendor_id ?? '') . '|' .
                    $item->category_id . '|' .
                    strtolower(
                        trim($item->price_type)
                    );
            });

        /*
        |--------------------------------------------------------------------------
        | Prepare Data
        |--------------------------------------------------------------------------
        */

        $preparedRows = [];

        foreach ($rows as $excelIndex => $row) {

            /*
            |--------------------------------------------------------------------------
            | Excel Row Number
            |--------------------------------------------------------------------------
            */

            $rowNumber = $excelIndex + 2;

            /*
            |--------------------------------------------------------------------------
            | Skip Baris Kosong
            |--------------------------------------------------------------------------
            */

            $hasData = false;

            foreach ($requiredHeaders as $header) {

                if (
                    isset($row[$header]) &&
                    trim((string) $row[$header]) !== ''
                ) {
                    $hasData = true;
                    break;
                }
            }

            if (!$hasData) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Lokasi
            |--------------------------------------------------------------------------
            */

            $locationName =
                trim(
                    (string) ($row['lokasi'] ?? '')
                );

            if ($locationName === '') {

                throw new \Exception(
                    "Baris {$rowNumber}: Lokasi wajib diisi."
                );
            }

            $location =
                $locations->get(
                    strtolower($locationName)
                );

            if (!$location) {

                throw new \Exception(
                    "Baris {$rowNumber}: Lokasi '{$locationName}' tidak ditemukan."
                );
            }

            if ($location->status !== 'Active') {

                throw new \Exception(
                    "Baris {$rowNumber}: Lokasi '{$location->name}' tidak aktif."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi Template vs Lokasi
            |--------------------------------------------------------------------------
            */

            $locationIsOutsourcing =
                strtolower(
                    trim($location->name)
                ) === 'outsourcing';

            if (
                $isOutsourcing &&
                !$locationIsOutsourcing
            ) {

                throw new \Exception(
                    "Baris {$rowNumber}: Template Outsourcing hanya dapat digunakan untuk Lokasi 'Outsourcing'."
                );
            }

            if (
                !$isOutsourcing &&
                $locationIsOutsourcing
            ) {

                throw new \Exception(
                    "Baris {$rowNumber}: Lokasi 'Outsourcing' harus menggunakan template Outsourcing."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Engine / Mesin
            |--------------------------------------------------------------------------
            */

            if ($isOutsourcing) {

                $engineName =
                    trim(
                        (string) ($row['mesin'] ?? '')
                    );
            } else {

                $engineName =
                    trim(
                        (string) ($row['engine'] ?? '')
                    );
            }

            if ($engineName === '') {

                throw new \Exception(
                    "Baris {$rowNumber}: Mesin wajib diisi."
                );
            }

            $engine =
                $engines->get(
                    strtolower($engineName)
                );

            if (!$engine) {

                throw new \Exception(
                    "Baris {$rowNumber}: Mesin '{$engineName}' tidak ditemukan."
                );
            }

            if ($engine->status !== 'Active') {

                throw new \Exception(
                    "Baris {$rowNumber}: Mesin '{$engine->name}' tidak aktif."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Calculation Rules
            |--------------------------------------------------------------------------
            */

            if (
                strtolower(
                    trim($engine->name)
                ) !== 'large format'
            ) {

                if (
                    strtolower(
                        trim($engine->name)
                    ) === 'digital print a3+'
                ) {

                    throw new \Exception(
                        'Mesin Digital Print A3+ belum mempunyai master calculation rules.'
                    );
                }

                throw new \Exception(
                    "Mesin \"{$engine->name}\" belum mempunyai master calculation rules."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Category
            |--------------------------------------------------------------------------
            */

            $categoryName =
                trim(
                    (string) ($row['kategori'] ?? '')
                );

            if ($categoryName === '') {

                throw new \Exception(
                    "Baris {$rowNumber}: Kategori wajib diisi."
                );
            }

            $category =
                $categories->get(
                    strtolower($categoryName)
                );

            if (!$category) {

                throw new \Exception(
                    "Baris {$rowNumber}: Kategori '{$categoryName}' tidak ditemukan."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Laminasi
            |--------------------------------------------------------------------------
            */

            $laminationName =
                trim(
                    (string) ($row['nama_laminasi'] ?? '')
                );

            if ($laminationName === '') {

                throw new \Exception(
                    "Baris {$rowNumber}: Nama laminasi wajib diisi."
                );
            }

            $lamination =
                $laminations->get(
                    strtolower($laminationName)
                );

            if (!$lamination) {

                throw new \Exception(
                    "Baris {$rowNumber}: Laminasi '{$laminationName}' tidak ditemukan atau tidak aktif."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Vendor
            |--------------------------------------------------------------------------
            */

            $vendor = null;

            if ($isOutsourcing) {

                $vendorName =
                    trim(
                        (string) ($row['nama_vendor'] ?? '')
                    );

                if ($vendorName === '') {

                    throw new \Exception(
                        "Baris {$rowNumber}: Nama Vendor wajib diisi."
                    );
                }

                $vendor =
                    $vendors->get(
                        strtolower($vendorName)
                    );

                if (!$vendor) {

                    throw new \Exception(
                        "Baris {$rowNumber}: Vendor '{$vendorName}' tidak ditemukan atau tidak aktif."
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Lebar
            |--------------------------------------------------------------------------
            */

            // $width =
            //     $parseNumber(
            //         $row['lebar'] ?? null
            //     );

            // if ($width <= 0) {

            //     throw new \Exception(
            //         "Baris {$rowNumber}: Lebar harus lebih besar dari 0."
            //     );
            // }

            $widthData = $parseDimension(
                $row['lebar'] ?? null,
                'Lebar',
                $rowNumber
            );

            if ($widthData === null) {

                throw new \Exception(
                    "Baris {$rowNumber}: Lebar wajib diisi."
                );
            }

            $width = $widthData['value'];
            $currentUnit = $widthData['unit'];

            /*
            |--------------------------------------------------------------------------
            | Validasi Konsistensi Satuan
            |--------------------------------------------------------------------------
            |
            | Satuan pertama menjadi acuan untuk seluruh file.
            |
            */

            if ($dimensionUnit === null) {

                $dimensionUnit = $currentUnit;
            } elseif ($dimensionUnit !== $currentUnit) {

                throw new \Exception(
                    "Baris {$rowNumber}: Satuan ukuran tidak konsisten. Seluruh Lebar dan Panjang dalam satu file harus menggunakan satuan '{$dimensionUnit}'."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Panjang
            |--------------------------------------------------------------------------
            |
            | Panjang boleh kosong.
            |
            */

            // $rawLength =
            //     $row['panjang'] ?? null;

            // $lengthIsEmpty =
            //     $rawLength === null ||
            //     trim((string) $rawLength) === '';

            // $length = null;

            // if (!$lengthIsEmpty) {

            //     $length =
            //         $parseNumber($rawLength);

            //     if ($length <= 0) {

            //         throw new \Exception(
            //             "Baris {$rowNumber}: Panjang harus lebih besar dari 0 jika diisi."
            //         );
            //     }
            // }

            $rawLength =
                $row['panjang'] ?? null;

            $lengthData = $parseDimension(
                $rawLength,
                'Panjang',
                $rowNumber
            );

            $length = null;

            $lengthIsEmpty =
                $lengthData === null;

            if (!$lengthIsEmpty) {

                if (
                    $lengthData['unit'] !== $dimensionUnit
                ) {

                    throw new \Exception(
                        "Baris {$rowNumber}: Satuan Panjang '{$lengthData['unit']}' tidak konsisten dengan satuan file '{$dimensionUnit}'."
                    );
                }

                $length =
                    $lengthData['value'];
            }

            /*
            |--------------------------------------------------------------------------
            | Cari Master Ukuran
            |--------------------------------------------------------------------------
            */

            $availableSizes =
                $laminationSizes->get(
                    $lamination->id,
                    collect()
                );

            $matchedSizes = collect();

            foreach ($availableSizes as $size) {

                $masterWidth =
                    (float) $size->width;

                $masterLength =
                    $size->length !== null
                    ? (float) $size->length
                    : null;

                /*
                | Lebar harus sama.
                */

                if ($masterWidth != $width) {
                    continue;
                }

                /*
                | Panjang Excel diisi.
                */

                if (!$lengthIsEmpty) {

                    if ($masterLength === null) {
                        continue;
                    }

                    if ($masterLength != $length) {
                        continue;
                    }

                    $matchedSizes->push($size);

                    continue;
                }

                /*
                | Panjang Excel kosong.
                |
                | Hanya match dengan master
                | yang length-nya NULL.
                */

                if ($masterLength !== null) {
                    continue;
                }

                $matchedSizes->push($size);
            }

            /*
            |--------------------------------------------------------------------------
            | Validasi Ukuran
            |--------------------------------------------------------------------------
            */

            if ($matchedSizes->isEmpty()) {

                $sizeLabel =
                    (string) $width;

                if (!$lengthIsEmpty) {

                    $sizeLabel .=
                        ' x ' . $length;
                } else {

                    $sizeLabel .=
                        ' x kosong';
                }

                throw new \Exception(
                    "Baris {$rowNumber}: Ukuran '{$sizeLabel}' tidak ditemukan pada master ukuran laminasi '{$lamination->name}'."
                );
            }

            /*
            | Lebih dari satu master size berarti ambigu.
            */

            if ($matchedSizes->count() > 1) {

                $sizeLabel =
                    (string) $width;

                if (!$lengthIsEmpty) {

                    $sizeLabel .=
                        ' x ' . $length;
                } else {

                    $sizeLabel .=
                        ' x kosong';
                }

                throw new \Exception(
                    "Baris {$rowNumber}: Ukuran '{$sizeLabel}' pada laminasi '{$lamination->name}' memiliki lebih dari satu data master yang cocok. Periksa duplikasi pada m_lamination_sizes."
                );
            }

            $masterSize =
                $matchedSizes->first();

            /*
            |--------------------------------------------------------------------------
            | Harga / Cost
            |--------------------------------------------------------------------------
            |
            | Inisialisasi vendorPrice terlebih dahulu.
            |
            | Ini mencegah warning:
            |
            | Possible undefined variable '$vendorPrice'
            |
            */

            $vendorPrice = null;

            if ($isOutsourcing) {

                /*
                | Harga Vendor
                */

                if (
                    !isset($row['harga_vendor']) ||
                    trim((string) $row['harga_vendor']) === ''
                ) {

                    throw new \Exception(
                        "Baris {$rowNumber}: Harga Vendor wajib diisi."
                    );
                }

                $vendorPrice =
                    $parseNumber(
                        $row['harga_vendor']
                    );

                if ($vendorPrice < 0) {

                    throw new \Exception(
                        "Baris {$rowNumber}: Harga Vendor tidak boleh negatif."
                    );
                }

                /*
                | Outsourcing tidak menggunakan:
                |
                | Harga Per Meter
                | Ongkos Produksi
                | Ongkos Finishing
                */

                $pricePerMeter = 0;
                $productionCost = 0;
                $finishingCost = 0;

                /*
                | Total Cost Outsourcing
                */

                $totalCost =
                    $vendorPrice;
            } else {

                /*
                | Harga Per Meter
                */

                $pricePerMeter =
                    $parseNumber(
                        $row['harga_per_meter'] ?? null
                    );

                /*
                | Ongkos Produksi
                */

                $productionCost =
                    $parseNumber(
                        $row['ongkos_produksi'] ?? null
                    );

                /*
                | Ongkos Finishing
                */

                $finishingCost =
                    $parseNumber(
                        $row['ongkos_finishing'] ?? null
                    );

                if ($pricePerMeter < 0) {

                    throw new \Exception(
                        "Baris {$rowNumber}: Harga per meter tidak boleh negatif."
                    );
                }

                if ($productionCost < 0) {

                    throw new \Exception(
                        "Baris {$rowNumber}: Ongkos produksi tidak boleh negatif."
                    );
                }

                if ($finishingCost < 0) {

                    throw new \Exception(
                        "Baris {$rowNumber}: Ongkos finishing tidak boleh negatif."
                    );
                }

                /*
                | Total Cost Non-Outsourcing
                */

                $totalCost =
                    ($pricePerMeter * 1.2)
                    + $productionCost
                    + $finishingCost;
            }

            /*
            |--------------------------------------------------------------------------
            | Pricing Rules
            |--------------------------------------------------------------------------
            */

            $rulePrefix =
                $engine->id . '|' .
                $location->id . '|' .
                (
                    $isOutsourcing
                    ? $vendor->id
                    : ''
                ) . '|' .
                $category->id . '|';

            $generalRule =
                $pricingRules->get(
                    $rulePrefix . 'general'
                );

            $divisionRule =
                $pricingRules->get(
                    $rulePrefix . 'division'
                );

            $plainRule =
                $pricingRules->get(
                    $rulePrefix . 'plain'
                );

            /*
            |--------------------------------------------------------------------------
            | General + Division wajib
            |--------------------------------------------------------------------------
            */

            if (
                !$generalRule ||
                !$divisionRule
            ) {

                $vendorText =
                    $isOutsourcing
                    ? ", Vendor '{$vendor->name}'"
                    : '';

                throw new \Exception(
                    "Baris {$rowNumber}: Pricing Rule General dan Division belum lengkap untuk Engine '{$engine->name}', Lokasi '{$location->name}'{$vendorText}, Kategori '{$category->name}'."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | General Markup
            |--------------------------------------------------------------------------
            */

            $generalMarkup =
                ((float) $generalRule->markup_percentage)
                / 100;

            /*
            |--------------------------------------------------------------------------
            | Division Markup
            |--------------------------------------------------------------------------
            */

            $divisionMarkup =
                ((float) $divisionRule->markup_percentage)
                / 100;

            /*
            |--------------------------------------------------------------------------
            | Harga Umum
            |--------------------------------------------------------------------------
            */

            $generalPrice =
                $totalCost *
                (1 + $generalMarkup);

            $generalPrice =
                $roundUp(
                    $generalPrice,
                    $generalRule->rounding_value
                );

            /*
            |--------------------------------------------------------------------------
            | Harga Divisi
            |--------------------------------------------------------------------------
            |
            | Mengikuti logic store() yang sudah digunakan:
            |
            | General Price × Division Markup
            |
            */

            $divisionPrice =
                $generalPrice *
                $divisionMarkup;

            $divisionPrice =
                $roundUp(
                    $divisionPrice,
                    $divisionRule->rounding_value
                );

            /*
            |--------------------------------------------------------------------------
            | Harga Polos
            |--------------------------------------------------------------------------
            */

            if ($isOutsourcing) {

                $plainPrice = 0;
                $plainPricingRuleId = null;
            } else {

                $plainPrice = null;
                $plainPricingRuleId = null;

                if ($plainRule) {

                    $plainMarkup =
                        ((float) $plainRule->markup_percentage)
                        / 100;

                    $plainPrice =
                        $pricePerMeter *
                        (1 + $plainMarkup);

                    $plainPrice =
                        $roundUp(
                            $plainPrice,
                            $plainRule->rounding_value
                        );

                    $plainPricingRuleId =
                        $plainRule->id;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Prepare Row
            |--------------------------------------------------------------------------
            */

            $preparedRows[] = [

                'row_number' =>
                $rowNumber,

                'is_outsourcing' =>
                $isOutsourcing,

                'engine_id' =>
                $engine->id,

                'location_id' =>
                $location->id,

                'vendor_id' =>
                $isOutsourcing
                    ? $vendor->id
                    : null,

                'vendor_price' =>
                $vendorPrice,

                'category_id' =>
                $category->id,

                'lamination_id' =>
                $lamination->id,

                'lamination_size_id' =>
                $masterSize->id,

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

                'general_pricing_rule_id' =>
                $generalRule->id,

                'division_pricing_rule_id' =>
                $divisionRule->id,

                'plain_pricing_rule_id' =>
                $plainPricingRuleId,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Tidak Ada Data
        |--------------------------------------------------------------------------
        */

        if (empty($preparedRows)) {

            throw new \Exception(
                'Tidak ada data yang dapat diimport.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Duplicate di Dalam Excel
        |--------------------------------------------------------------------------
        |
        | Non-Outsourcing:
        |
        | Engine + Location + Category + Laminasi + Size
        |
        | Outsourcing:
        |
        | Engine + Location + Vendor + Category + Laminasi + Size
        |
        */

        $excelKeys = [];

        foreach ($preparedRows as $row) {

            $key =
                $row['engine_id'] . '|' .
                $row['location_id'] . '|' .
                ($row['vendor_id'] ?? '') . '|' .
                $row['category_id'] . '|' .
                $row['lamination_id'] . '|' .
                $row['lamination_size_id'];

            if (isset($excelKeys[$key])) {

                $vendorText =
                    $row['is_outsourcing']
                    ? ' dan Vendor'
                    : '';

                throw new \Exception(
                    "Baris {$row['row_number']}: Kombinasi Engine, Lokasi{$vendorText}, Kategori, Laminasi, dan Ukuran yang sama muncul lebih dari satu kali di Excel."
                );
            }

            $excelKeys[$key] = true;
        }

        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        DB::beginTransaction();

        try {

            $now =
                now();

            $userName =
                Auth::user()->name;

            /*
            |--------------------------------------------------------------------------
            | Duplicate Database
            |--------------------------------------------------------------------------
            |
            | Identitas konfigurasi:
            |
            | Engine
            | Location
            | Vendor
            | Category
            | Laminasi
            | Size
            |
            */

            foreach ($preparedRows as $row) {

                $query =
                    DB::table(
                        'm_lamination_production_cost_detail_sizes'
                    )
                    ->join(
                        'm_lamination_production_cost_details as lpcd',
                        'lpcd.id',
                        '=',
                        'm_lamination_production_cost_detail_sizes.lamination_production_cost_detail_id'
                    )
                    ->join(
                        'm_production_costs as pc',
                        'pc.id',
                        '=',
                        'lpcd.production_cost_id'
                    )
                    ->join(
                        'm_production_cost_locations as pcl',
                        'pcl.production_cost_id',
                        '=',
                        'pc.id'
                    )
                    ->join(
                        'm_production_cost_categories as pcc',
                        'pcc.production_cost_id',
                        '=',
                        'pc.id'
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
                        'pcc.category_id',
                        $row['category_id']
                    )
                    ->where(
                        'lpcd.lamination_id',
                        $row['lamination_id']
                    )
                    ->where(
                        'm_lamination_production_cost_detail_sizes.lamination_size_id',
                        $row['lamination_size_id']
                    );

                /*
                |--------------------------------------------------------------------------
                | Vendor
                |--------------------------------------------------------------------------
                */

                if ($row['is_outsourcing']) {

                    $query->where(
                        'lpcd.vendor_id',
                        $row['vendor_id']
                    );
                } else {

                    $query->whereNull(
                        'lpcd.vendor_id'
                    );
                }

                $exists =
                    $query->exists();

                if ($exists) {

                    $vendorText =
                        $row['is_outsourcing']
                        ? ', Vendor'
                        : '';

                    throw ValidationException::withMessages([
                        'file' =>
                        "Baris {$row['row_number']}: Data Production Cost dengan Engine, Lokasi{$vendorText}, Kategori, Laminasi, dan Ukuran yang sama sudah tersedia.",
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Group Production Cost
            |--------------------------------------------------------------------------
            |
            | Non-Outsourcing:
            |
            | Engine + Location
            |
            | Outsourcing:
            |
            | Engine + Location + Vendor
            |
            */

            $groupedConfigurations =
                collect($preparedRows)
                ->groupBy(function ($row) {

                    return
                        $row['engine_id'] . '|' .
                        $row['location_id'] . '|' .
                        (
                            $row['vendor_id'] ?? ''
                        );
                });

            foreach (
                $groupedConfigurations
                as $configurationRows
            ) {

                $first =
                    $configurationRows->first();

                /*
                |--------------------------------------------------------------------------
                | Header Production Cost
                |--------------------------------------------------------------------------
                */

                $productionCostId =
                    DB::table(
                        'm_production_costs'
                    )->insertGetId([

                        'engine_id' =>
                        $first['engine_id'],

                        'created_by' =>
                        $userName,

                        'created_at' =>
                        $now,

                        'updated_by' =>
                        $userName,

                        'updated_at' =>
                        $now,
                    ]);

                /*
                |--------------------------------------------------------------------------
                | Location
                |--------------------------------------------------------------------------
                */

                DB::table(
                    'm_production_cost_locations'
                )->insert([

                    'production_cost_id' =>
                    $productionCostId,

                    'location_id' =>
                    $first['location_id'],

                    'created_by' =>
                    $userName,

                    'created_at' =>
                    $now,

                    'updated_by' =>
                    $userName,

                    'updated_at' =>
                    $now,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Categories
                |--------------------------------------------------------------------------
                */

                $categoryIds =
                    $configurationRows
                    ->pluck('category_id')
                    ->unique()
                    ->values();

                foreach ($categoryIds as $categoryId) {

                    DB::table(
                        'm_production_cost_categories'
                    )->insert([

                        'production_cost_id' =>
                        $productionCostId,

                        'category_id' =>
                        $categoryId,

                        'created_by' =>
                        $userName,

                        'created_at' =>
                        $now,

                        'updated_by' =>
                        $userName,

                        'updated_at' =>
                        $now,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Group Detail
                |--------------------------------------------------------------------------
                |
                | Non-Outsourcing:
                |
                | Category + Laminasi
                |
                | Outsourcing:
                |
                | Vendor + Category + Laminasi
                |
                */

                $details =
                    $configurationRows
                    ->groupBy(function ($row) {

                        return ($row['vendor_id'] ?? '') . '|' .
                            $row['category_id'] . '|' .
                            $row['lamination_id'];
                    });

                foreach ($details as $detailRows) {

                    $detail =
                        $detailRows->first();

                    /*
                    |--------------------------------------------------------------------------
                    | Detail
                    |--------------------------------------------------------------------------
                    */

                    $detailId =
                        DB::table(
                            'm_lamination_production_cost_details'
                        )->insertGetId([

                            'production_cost_id' =>
                            $productionCostId,

                            'lamination_id' =>
                            $detail['lamination_id'],

                            'vendor_id' =>
                            $detail['vendor_id'],

                            'vendor_price' =>
                            $detail['vendor_price'],

                            'production_cost' =>
                            $detail['production_cost'],

                            'finishing_cost' =>
                            $detail['finishing_cost'],

                            'total_cost' =>
                            $detail['total_cost'],

                            'price_per_meter' =>
                            $detail['price_per_meter'],

                            'general_price' =>
                            $detail['general_price'],

                            'division_price' =>
                            $detail['division_price'],

                            'plain_price' =>
                            $detail['plain_price'],

                            'general_pricing_rule_id' =>
                            $detail['general_pricing_rule_id'],

                            'division_pricing_rule_id' =>
                            $detail['division_pricing_rule_id'],

                            'plain_pricing_rule_id' =>
                            $detail['plain_pricing_rule_id'],

                            'created_by' =>
                            $userName,

                            'created_at' =>
                            $now,

                            'updated_by' =>
                            $userName,

                            'updated_at' =>
                            $now,
                        ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Sizes
                    |--------------------------------------------------------------------------
                    */

                    $sizeIds =
                        $detailRows
                        ->pluck('lamination_size_id')
                        ->unique()
                        ->values();

                    foreach ($sizeIds as $sizeId) {

                        DB::table(
                            'm_lamination_production_cost_detail_sizes'
                        )->insert([

                            'lamination_production_cost_detail_id' =>
                            $detailId,

                            'lamination_size_id' =>
                            $sizeId,

                            'created_by' =>
                            $userName,

                            'created_at' =>
                            $now,

                            'updated_by' =>
                            $userName,

                            'updated_at' =>
                            $now,
                        ]);

                        $this->importedRows++;
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Commit
            |--------------------------------------------------------------------------
            */

            DB::commit();
        } catch (ValidationException $e) {

            DB::rollBack();

            throw $e;
        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }
}

<?php

namespace App\Imports;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PricingRulesImport implements ToCollection, WithHeadingRow
{
    public $importedRows = 0;

    protected $requiredHeaders = [
        'no',
        'mesin',
        'lokasi',
        'vendor',
        'kategori',
        'tipe_harga',
        'markup',
        'pembulatan',
        'status',
    ];

    /**
     * Import data dari Excel.
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
        | 1. VALIDASI HEADER
        |--------------------------------------------------------------------------
        */

        $firstRow = $rows->first();

        $headers = array_keys(
            $firstRow->toArray()
        );

        $headers = array_map(function ($header) {
            return strtolower(trim($header));
        }, $headers);

        $missingHeaders = array_diff(
            $this->requiredHeaders,
            $headers
        );

        if (!empty($missingHeaders)) {
            throw new Exception(
                'Format Excel tidak sesuai. Kolom yang wajib tersedia: ' .
                    implode(', ', $this->requiredHeaders)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. PREPARE DATA
        |--------------------------------------------------------------------------
        */

        $preparedRows = [];

        foreach ($rows as $index => $row) {

            /*
             * Header = row 1
             * Data pertama = row 2
             */
            $excelRow = $index + 2;

            $no = $this->normalizeText(
                $row['no'] ?? null
            );

            $mesin = $this->normalizeText(
                $row['mesin'] ?? null
            );

            $lokasi = $this->normalizeText(
                $row['lokasi'] ?? null
            );

            $vendor = $this->normalizeText(
                $row['vendor'] ?? null
            );

            $kategori = $this->normalizeText(
                $row['kategori'] ?? null
            );

            $tipeHarga = $this->normalizeText(
                $row['tipe_harga'] ?? null
            );

            $markup = $row['markup'] ?? null;

            $pembulatan = $row['pembulatan'] ?? null;

            $status = $this->normalizeText(
                $row['status'] ?? null
            );

            /*
            |--------------------------------------------------------------------------
            | Skip baris yang benar-benar kosong
            |--------------------------------------------------------------------------
            */

            if (
                $no === null &&
                $mesin === null &&
                $lokasi === null &&
                $vendor === null &&
                $kategori === null &&
                $tipeHarga === null &&
                ($markup === null || $markup === '') &&
                ($pembulatan === null || $pembulatan === '') &&
                $status === null
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI FIELD WAJIB
            |--------------------------------------------------------------------------
            */

            if ($mesin === null) {
                throw new Exception(
                    "Baris {$excelRow}: Mesin wajib diisi."
                );
            }

            if ($lokasi === null) {
                throw new Exception(
                    "Baris {$excelRow}: Lokasi wajib diisi."
                );
            }

            if ($kategori === null) {
                throw new Exception(
                    "Baris {$excelRow}: Kategori wajib diisi."
                );
            }

            if ($tipeHarga === null) {
                throw new Exception(
                    "Baris {$excelRow}: Tipe Harga wajib diisi."
                );
            }

            if ($markup === null || $markup === '') {
                throw new Exception(
                    "Baris {$excelRow}: Markup wajib diisi."
                );
            }

            /*
             * Pembulatan tidak wajib diisi.
             */

            if ($status === null) {
                throw new Exception(
                    "Baris {$excelRow}: Status wajib diisi."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI MESIN
            |--------------------------------------------------------------------------
            */

            $engine = DB::table('m_engines')
                ->whereRaw(
                    'LOWER(TRIM(name)) = LOWER(?)',
                    [$mesin]
                )
                ->where('status', 'Active')
                ->first();

            if (!$engine) {
                throw new Exception(
                    "Baris {$excelRow}: Mesin \"{$mesin}\" tidak ditemukan atau tidak aktif."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI LOKASI
            |--------------------------------------------------------------------------
            */

            $location = DB::table('m_locations')
                ->whereRaw(
                    'LOWER(TRIM(name)) = LOWER(?)',
                    [$lokasi]
                )
                ->where('status', 'Active')
                ->first();

            if (!$location) {
                throw new Exception(
                    "Baris {$excelRow}: Lokasi \"{$lokasi}\" tidak ditemukan atau tidak aktif."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI VENDOR
            |--------------------------------------------------------------------------
            */

            $vendorData = null;

            $isOutsourcing =
                strtolower(trim($location->name)) === 'outsourcing';

            if ($isOutsourcing) {

                /*
                 * Vendor wajib untuk Outsourcing.
                 */
                if ($vendor === null) {
                    throw new Exception(
                        "Baris {$excelRow}: Vendor wajib diisi untuk lokasi Outsourcing."
                    );
                }

                $vendorData = DB::table('m_vendors')
                    ->whereRaw(
                        'LOWER(TRIM(name)) = LOWER(?)',
                        [$vendor]
                    )
                    ->where('status', 'Active')
                    ->first();

                if (!$vendorData) {
                    throw new Exception(
                        "Baris {$excelRow}: Vendor \"{$vendor}\" tidak ditemukan atau tidak aktif."
                    );
                }
            } else {

                /*
                 * Vendor tidak boleh digunakan
                 * untuk lokasi selain Outsourcing.
                 */
                if ($vendor !== null) {
                    throw new Exception(
                        "Baris {$excelRow}: Vendor hanya dapat digunakan untuk lokasi Outsourcing."
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI KATEGORI
            |--------------------------------------------------------------------------
            */

            $category = DB::table('m_categories')
                ->whereRaw(
                    'LOWER(TRIM(name)) = LOWER(?)',
                    [$kategori]
                )
                ->first();

            if (!$category) {
                throw new Exception(
                    "Baris {$excelRow}: Kategori \"{$kategori}\" tidak ditemukan."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | MAPPING TIPE HARGA
            |--------------------------------------------------------------------------
            */

            $priceType = $this->mapPriceType(
                $tipeHarga,
                $excelRow
            );

            /*
            |--------------------------------------------------------------------------
            | NORMALISASI STATUS
            |--------------------------------------------------------------------------
            */

            $normalizedStatus = $this->normalizeStatus(
                $status,
                $excelRow
            );

            /*
            |--------------------------------------------------------------------------
            | VALIDASI MARKUP
            |--------------------------------------------------------------------------
            */

            $markupValue = $this->parseNumber(
                $markup,
                'Markup',
                $excelRow
            );

            if ($markupValue < 0) {
                throw new Exception(
                    "Baris {$excelRow}: Markup tidak boleh kurang dari 0."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI PEMBULATAN
            |--------------------------------------------------------------------------
            */

            if (
                $pembulatan === null ||
                $pembulatan === ''
            ) {
                $roundingValue = null;
            } else {

                $roundingValue = $this->parseNumber(
                    $pembulatan,
                    'Pembulatan',
                    $excelRow
                );

                if ($roundingValue < 0) {
                    throw new Exception(
                        "Baris {$excelRow}: Pembulatan tidak boleh kurang dari 0."
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | SIMPAN DATA YANG SUDAH DINORMALISASI
            |--------------------------------------------------------------------------
            */

            $preparedRows[] = [
                'excel_row' => $excelRow,

                'engine_id' => $engine->id,
                'engine_name' => $engine->name,

                'location_id' => $location->id,
                'location_name' => $location->name,

                'vendor_id' =>
                $vendorData
                    ? $vendorData->id
                    : null,

                'vendor_name' =>
                $vendorData
                    ? $vendorData->name
                    : null,

                'category_id' => $category->id,
                'category_name' => $category->name,

                'price_type' => $priceType,

                'markup_percentage' => $markupValue,

                'rounding_value' => $roundingValue,

                'status' => $normalizedStatus,
            ];
        }

        if (empty($preparedRows)) {
            throw new Exception(
                'Tidak ada data yang dapat diimport.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. VALIDASI DUPLICATE DI DALAM EXCEL
        |--------------------------------------------------------------------------
        |
        | Kombinasi:
        |
        | engine_id
        | + location_id
        | + vendor_id
        | + category_id
        | + price_type
        |
        | harus unik.
        |--------------------------------------------------------------------------
        */

        $duplicateKeys = [];

        foreach ($preparedRows as $row) {

            $key =
                $row['engine_id'] . '|' .
                $row['location_id'] . '|' .
                ($row['vendor_id'] ?? 'null') . '|' .
                $row['category_id'] . '|' .
                $row['price_type'];

            if (isset($duplicateKeys[$key])) {

                $vendorText = $row['vendor_name']
                    ? ', Vendor "' . $row['vendor_name'] . '"'
                    : '';

                throw new Exception(
                    "Baris {$row['excel_row']}: Tipe Harga \"" .
                        $this->priceTypeLabel(
                            $row['price_type']
                        ) .
                        "\" untuk Mesin \"" .
                        $row['engine_name'] .
                        "\", Lokasi \"" .
                        $row['location_name'] .
                        "\"" .
                        $vendorText .
                        ", dan Kategori \"" .
                        $row['category_name'] .
                        "\" duplikat dengan baris " .
                        $duplicateKeys[$key] .
                        "."
                );
            }

            $duplicateKeys[$key] =
                $row['excel_row'];
        }

        /*
        |--------------------------------------------------------------------------
        | 4. VALIDASI CONFLICT DENGAN DATABASE
        |--------------------------------------------------------------------------
        */

        foreach ($preparedRows as $row) {

            $query = DB::table('m_pricing_rules')
                ->where(
                    'engine_id',
                    $row['engine_id']
                )
                ->where(
                    'location_id',
                    $row['location_id']
                )
                ->where(
                    'category_id',
                    $row['category_id']
                )
                ->where(
                    'price_type',
                    $row['price_type']
                );

            if ($row['vendor_id'] === null) {
                $query->whereNull('vendor_id');
            } else {
                $query->where(
                    'vendor_id',
                    $row['vendor_id']
                );
            }

            $exists = $query->exists();

            if ($exists) {

                $vendorText = $row['vendor_name']
                    ? ', Vendor "' . $row['vendor_name'] . '"'
                    : '';

                throw new Exception(
                    "Baris {$row['excel_row']}: Tipe Harga \"" .
                        $this->priceTypeLabel(
                            $row['price_type']
                        ) .
                        "\" untuk Mesin \"" .
                        $row['engine_name'] .
                        "\", Lokasi \"" .
                        $row['location_name'] .
                        "\"" .
                        $vendorText .
                        ", dan Kategori \"" .
                        $row['category_name'] .
                        "\" sudah tersedia."
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 5. VALIDASI KELOMPOK HARGA
        |--------------------------------------------------------------------------
        |
        | General = wajib
        | Division = wajib
        | Plain = opsional
        |
        | Group:
        |
        | Mesin + Lokasi + Vendor + Kategori
        |--------------------------------------------------------------------------
        */

        $groupedRows = [];

        foreach ($preparedRows as $row) {

            $groupKey =
                $row['engine_id'] . '|' .
                $row['location_id'] . '|' .
                ($row['vendor_id'] ?? 'null') . '|' .
                $row['category_id'];

            if (!isset($groupedRows[$groupKey])) {
                $groupedRows[$groupKey] = [];
            }

            $groupedRows[$groupKey][] = $row;
        }

        foreach ($groupedRows as $groupRows) {

            $priceTypes = array_column(
                $groupRows,
                'price_type'
            );

            $firstRow = $groupRows[0];

            $vendorText = $firstRow['vendor_name']
                ? ', Vendor "' . $firstRow['vendor_name'] . '"'
                : '';

            /*
            |--------------------------------------------------------------------------
            | GENERAL WAJIB
            |--------------------------------------------------------------------------
            */

            if (!in_array(
                'general',
                $priceTypes,
                true
            )) {
                throw new Exception(
                    "Konfigurasi untuk Mesin \"" .
                        $firstRow['engine_name'] .
                        "\", Lokasi \"" .
                        $firstRow['location_name'] .
                        "\"" .
                        $vendorText .
                        ", dan Kategori \"" .
                        $firstRow['category_name'] .
                        "\" harus memiliki Tipe Harga \"Harga Umum\"."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | DIVISION WAJIB
            |--------------------------------------------------------------------------
            */

            if (!in_array(
                'division',
                $priceTypes,
                true
            )) {
                throw new Exception(
                    "Konfigurasi untuk Mesin \"" .
                        $firstRow['engine_name'] .
                        "\", Lokasi \"" .
                        $firstRow['location_name'] .
                        "\"" .
                        $vendorText .
                        ", dan Kategori \"" .
                        $firstRow['category_name'] .
                        "\" harus memiliki Tipe Harga \"Harga Divisi\"."
                );
            }

            /*
            |--------------------------------------------------------------------------
            | PLAIN OPSIONAL
            |--------------------------------------------------------------------------
            |
            | Tidak perlu validasi keberadaan Plain.
            |--------------------------------------------------------------------------
            */

            /*
            |--------------------------------------------------------------------------
            | VALIDASI STATUS HARUS KONSISTEN
            |--------------------------------------------------------------------------
            */

            $statuses = array_unique(
                array_column(
                    $groupRows,
                    'status'
                )
            );

            if (count($statuses) > 1) {

                throw new Exception(
                    "Konfigurasi untuk Mesin \"" .
                        $firstRow['engine_name'] .
                        "\", Lokasi \"" .
                        $firstRow['location_name'] .
                        "\"" .
                        $vendorText .
                        ", dan Kategori \"" .
                        $firstRow['category_name'] .
                        "\" memiliki Status yang tidak konsisten. " .
                        "Status untuk seluruh Tipe Harga harus sama."
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 6. INSERT DALAM TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($preparedRows) {

            foreach ($preparedRows as $row) {

                DB::table('m_pricing_rules')->insert([
                    'engine_id' =>
                    $row['engine_id'],

                    'location_id' =>
                    $row['location_id'],

                    'vendor_id' =>
                    $row['vendor_id'],

                    'category_id' =>
                    $row['category_id'],

                    'price_type' =>
                    $row['price_type'],

                    'markup_percentage' =>
                    $row['markup_percentage'],

                    'rounding_value' =>
                    $row['rounding_value'],

                    'status' =>
                    $row['status'],

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
        });
    }

    /**
     * Normalisasi text.
     */
    protected function normalizeText($value)
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === ''
            ? null
            : $value;
    }

    /**
     * Mapping tipe harga.
     */
    protected function mapPriceType(
        $value,
        $excelRow
    ) {
        $normalized = strtolower(
            trim($value)
        );

        $mapping = [
            'harga umum' => 'general',
            'harga divisi' => 'division',
            'harga polos' => 'plain',
        ];

        if (!isset($mapping[$normalized])) {
            throw new Exception(
                "Baris {$excelRow}: Tipe Harga \"{$value}\" tidak valid. " .
                    'Gunakan Harga Umum, Harga Divisi, atau Harga Polos.'
            );
        }

        return $mapping[$normalized];
    }

    /**
     * Normalisasi status.
     */
    protected function normalizeStatus(
        $value,
        $excelRow
    ) {
        $normalized = strtolower(
            trim($value)
        );

        if ($normalized === 'active') {
            return 'Active';
        }

        if ($normalized === 'inactive') {
            return 'Inactive';
        }

        throw new Exception(
            "Baris {$excelRow}: Status \"{$value}\" tidak valid. " .
                'Gunakan Active atau Inactive.'
        );
    }

    /**
     * Parse angka.
     */
    protected function parseNumber(
        $value,
        $fieldName,
        $excelRow
    ) {
        if (
            !is_numeric($value) ||
            is_bool($value)
        ) {
            throw new Exception(
                "Baris {$excelRow}: {$fieldName} harus berupa angka."
            );
        }

        return (float) $value;
    }

    /**
     * Label tipe harga.
     */
    protected function priceTypeLabel(
        $priceType
    ) {
        return [
            'general' => 'Harga Umum',
            'division' => 'Harga Divisi',
            'plain' => 'Harga Polos',
        ][$priceType] ?? $priceType;
    }
}

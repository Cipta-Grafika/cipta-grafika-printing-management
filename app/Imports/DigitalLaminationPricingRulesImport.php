<?php

namespace App\Imports;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DigitalLaminationPricingRulesImport implements ToCollection, WithHeadingRow
{
    public $importedRows = 0;

    protected $requiredHeaders = [
        'no',
        'mesin',
        'lokasi',
        'vendor',
        'kategori',
        'tipe_harga',
        'harga_print',
        'minimum',
        'maksimum',
        'markup',
        'pembulatan',
        'status',
    ];

    protected $tierRanges = [
        'tier_1' => [1, 1],
        'tier_2_5' => [2, 5],
        'tier_6_50' => [6, 50],
        'tier_100' => [51, 100],
        'tier_300' => [101, 300],
        'tier_500' => [301, 500],
    ];

    protected $priceTypeLabels = [
        'general' => 'Harga Umum',
        'division' => 'Harga Divisi',
    ];

    protected $printTypeLabels = [
        'single' => '1 Muka',
        'double' => '2 Muka',
    ];

    // Cache master data agar tidak query berulang untuk setiap baris
    protected $engineCache = [];
    protected $locationCache = [];
    protected $vendorCache = [];
    protected $categoryCache = [];

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            throw new Exception('File Excel tidak memiliki data.');
        }

        $headers = array_map(
            fn($header) => strtolower(trim((string) $header)),
            array_keys($rows->first()->toArray())
        );

        $missingHeaders = array_diff($this->requiredHeaders, $headers);

        if (!empty($missingHeaders)) {
            throw new Exception(
                'Format Excel tidak sesuai template laminasi Digital Print A3+. Kolom yang wajib tersedia: ' .
                    implode(', ', $this->requiredHeaders)
            );
        }

        $preparedRows = [];

        foreach ($rows as $index => $row) {
            $excelRow = $index + 2;
            $data = $row->toArray();
            $mesin = $this->normalizeText($data['mesin'] ?? null);
            $lokasi = $this->normalizeText($data['lokasi'] ?? null);
            $vendor = $this->normalizeText($data['vendor'] ?? null);
            $kategori = $this->normalizeText($data['kategori'] ?? null);
            $tipeHarga = $this->normalizeText($data['tipe_harga'] ?? null);
            $hargaPrint = $this->normalizeText($data['harga_print'] ?? null);
            $minimum = $this->normalizeText($data['minimum'] ?? null);
            $maksimum = $this->normalizeText($data['maksimum'] ?? null);
            $markup = $this->normalizeText($data['markup'] ?? null);
            $pembulatan = $this->normalizeText($data['pembulatan'] ?? null);
            $status = $this->normalizeText($data['status'] ?? null);

            if ($this->isEmptyRow([
                $mesin,
                $lokasi,
                $vendor,
                $kategori,
                $tipeHarga,
                $hargaPrint,
                $minimum,
                $maksimum,
                $markup,
                $pembulatan,
                $status,
            ])) {
                continue;
            }

            foreach ([
                'Mesin' => $mesin,
                'Lokasi' => $lokasi,
                'Kategori' => $kategori,
                'Tipe Harga' => $tipeHarga,
                'Harga Print' => $hargaPrint,
                'Minimum' => $minimum,
                'Maksimum' => $maksimum,
                'Markup' => $markup,
                'Status' => $status,
            ] as $field => $value) {
                if ($value === null) {
                    throw new Exception("Baris {$excelRow}: {$field} wajib diisi.");
                }
            }

            $minimum = $this->parseInteger($minimum, 'Minimum', $excelRow);
            $maksimum = $this->parseInteger($maksimum, 'Maksimum', $excelRow);

            if ($minimum <= 0 || $maksimum <= 0 || $minimum > $maksimum) {
                throw new Exception(
                    "Baris {$excelRow}: Minimum dan Maksimum harus lebih dari 0, serta Minimum tidak boleh melebihi Maksimum."
                );
            }

            $tier = $this->resolveTier($minimum, $maksimum, $excelRow);
            $markupValue = $this->parseNumber($markup, 'Markup', $excelRow);

            if ($markupValue < 0 || $markupValue > 999999.99) {
                throw new Exception(
                    "Baris {$excelRow}: Markup harus bernilai antara 0 dan 999999.99."
                );
            }

            $roundingValue = null;
            if ($pembulatan !== null) {
                $roundingValue = $this->parseNumber(
                    $pembulatan,
                    'Pembulatan',
                    $excelRow
                );

                if ($roundingValue < 0 || $roundingValue > 9999999999.99) {
                    throw new Exception(
                        "Baris {$excelRow}: Pembulatan harus bernilai antara 0 dan 9999999999.99."
                    );
                }
            }

            $priceType = $this->mapPriceType($tipeHarga, $excelRow);
            $printType = $this->mapPrintType($hargaPrint, $excelRow);
            $statusValue = $this->normalizeStatus($status, $excelRow);

            $engine = $this->findEngine($mesin);

            if (!$engine) {
                throw new Exception(
                    "Baris {$excelRow}: Mesin \"{$mesin}\" tidak ditemukan atau tidak aktif."
                );
            }

            if ($this->normalizeKey($engine->name) !== 'digitalprinta3+') {
                throw new Exception(
                    "Baris {$excelRow}: Mesin harus \"Digital Print A3+\". Gunakan template Large Format untuk mesin lain."
                );
            }

            $location = $this->findLocation($lokasi);

            if (!$location) {
                throw new Exception(
                    "Baris {$excelRow}: Lokasi \"{$lokasi}\" tidak ditemukan atau tidak aktif."
                );
            }

            $category = $this->findCategory($kategori);

            if (!$category) {
                throw new Exception(
                    "Baris {$excelRow}: Kategori \"{$kategori}\" tidak ditemukan."
                );
            }

            if (in_array($this->normalizeKey($category->name), [
                'indooroutdoor',
                'indoor&outdoor',
            ], true)) {
                throw new Exception(
                    "Baris {$excelRow}: Kategori Indoor/Outdoor tidak dapat digunakan untuk laminasi Digital Print A3+."
                );
            }

            $vendorData = null;
            if (strtolower(trim($location->name)) === 'outsourcing') {
                if ($vendor === null) {
                    throw new Exception(
                        "Baris {$excelRow}: Vendor wajib diisi untuk lokasi Outsourcing."
                    );
                }

                $vendorData = $this->findVendor($vendor);

                if (!$vendorData) {
                    throw new Exception(
                        "Baris {$excelRow}: Vendor \"{$vendor}\" tidak ditemukan atau tidak aktif."
                    );
                }
            } elseif ($vendor !== null && $vendor !== '-') {
                throw new Exception(
                    "Baris {$excelRow}: Vendor hanya dapat diisi untuk lokasi Outsourcing."
                );
            }

            $preparedRows[] = [
                'excel_row' => $excelRow,
                'engine_id' => $engine->id,
                'engine_name' => $engine->name,
                'location_id' => $location->id,
                'location_name' => $location->name,
                'vendor_id' => $vendorData?->id,
                'vendor_name' => $vendorData?->name,
                'category_id' => $category->id,
                'category_name' => $category->name,
                'price_type' => $priceType,
                'print_type' => $printType,
                'quantity_tier' => $tier,
                'minimum_quantity' => $minimum,
                'maximum_quantity' => $maksimum,
                'markup_percentage' => $markupValue,
                'rounding_value' => $roundingValue,
                'status' => $statusValue,
            ];
        }

        if (empty($preparedRows)) {
            throw new Exception('Tidak ada data yang dapat diimport.');
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI DUPLIKASI DI DALAM FILE
        |--------------------------------------------------------------------------
        */

        $groups = [
            'headers' => [],
            'details' => [],
        ];

        foreach ($preparedRows as $row) {
            $headerKey = $this->headerKey($row);
            $detailKey = implode('|', [
                $headerKey,
                $row['price_type'],
                $row['print_type'],
                $row['quantity_tier'],
            ]);

            if (isset($groups['details'][$detailKey])) {
                throw new Exception(
                    "Baris {$row['excel_row']}: Data duplikat dengan baris {$groups['details'][$detailKey]} " .
                        '(' . $this->configLabel($row) . ', ' .
                        $this->priceTypeLabels[$row['price_type']] . ', ' .
                        $this->printTypeLabels[$row['print_type']] . ', ' .
                        "{$row['minimum_quantity']}-{$row['maximum_quantity']})."
                );
            }

            $groups['details'][$detailKey] = $row['excel_row'];
            $groups['headers'][$headerKey][] = $row;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI KELENGKAPAN, KONSISTENSI STATUS, DAN DUPLIKASI DATABASE
        |--------------------------------------------------------------------------
        */

        foreach ($groups['headers'] as $headerRows) {
            $firstRow = $headerRows[0];
            $expectedDetails = [];
            $statuses = [];

            foreach (['general', 'division'] as $priceType) {
                foreach (['single', 'double'] as $printType) {
                    foreach (array_keys($this->tierRanges) as $tier) {
                        $expectedDetails[
                            implode('|', [$priceType, $printType, $tier])
                        ] = true;
                    }
                }
            }

            foreach ($headerRows as $row) {
                unset($expectedDetails[
                    implode('|', [
                        $row['price_type'],
                        $row['print_type'],
                        $row['quantity_tier'],
                    ])
                ]);

                $statuses[
                    $row['price_type'] . '|' . $row['print_type']
                ][] = $row['status'];
            }

            if (!empty($expectedDetails)) {
                $missing = array_map(function ($key) {
                    [$priceType, $printType, $tier] = explode('|', $key);
                    [$min, $max] = $this->tierRanges[$tier];

                    return $this->priceTypeLabels[$priceType] . ' ' .
                        $this->printTypeLabels[$printType] . " {$min}-{$max}";
                }, array_keys($expectedDetails));

                throw new Exception(
                    'Konfigurasi ' . $this->configLabel($firstRow) .
                        ' belum lengkap. Setiap konfigurasi wajib memiliki 24 baris (Harga Umum/Harga Divisi × 1 Muka/2 Muka × rentang 1-1, 2-5, 6-50, 51-100, 101-300, 301-500). Yang belum ada: ' .
                        implode(', ', array_slice($missing, 0, 5)) .
                        (count($missing) > 5 ? ', dan ' . (count($missing) - 5) . ' lainnya.' : '.')
                );
            }

            foreach ($statuses as $combination => $combinationStatuses) {
                if (count(array_unique($combinationStatuses)) > 1) {
                    [$priceType, $printType] = explode('|', $combination);

                    throw new Exception(
                        'Status pada konfigurasi ' . $this->configLabel($firstRow) .
                            ' untuk ' . $this->priceTypeLabels[$priceType] . ' ' .
                            $this->printTypeLabels[$printType] .
                            ' harus sama (Active atau Inactive) di seluruh rentang jumlah lembar.'
                    );
                }
            }

            $headerExists = DB::table('m_digital_lamination_pricing_rules')
                ->where('engine_id', $firstRow['engine_id'])
                ->where('location_id', $firstRow['location_id'])
                ->where('category_id', $firstRow['category_id'])
                ->when(
                    $firstRow['vendor_id'] === null,
                    fn($query) => $query->whereNull('vendor_id'),
                    fn($query) => $query->where(
                        'vendor_id',
                        $firstRow['vendor_id']
                    )
                )
                ->exists();

            if ($headerExists) {
                throw new Exception(
                    'Markup laminasi untuk ' . $this->configLabel($firstRow) .
                        ' sudah tersedia dan tidak dapat diimport ulang. Gunakan fitur edit untuk mengubah data tersebut.'
                );
            }
        }

        DB::transaction(function () use ($groups) {
            $now = Carbon::now();
            $userName = Auth::user()->name;
            $details = [];

            foreach ($groups['headers'] as $headerRows) {
                $firstRow = $headerRows[0];
                $headerId = DB::table('m_digital_lamination_pricing_rules')
                    ->insertGetId([
                        'engine_id' => $firstRow['engine_id'],
                        'location_id' => $firstRow['location_id'],
                        'vendor_id' => $firstRow['vendor_id'],
                        'category_id' => $firstRow['category_id'],
                        'created_by' => $userName,
                        'created_at' => $now,
                        'updated_by' => $userName,
                        'updated_at' => $now,
                    ]);

                foreach ($headerRows as $row) {
                    $details[] = [
                        'digital_lamination_pricing_rule_id' => $headerId,
                        'price_type' => $row['price_type'],
                        'print_type' => $row['print_type'],
                        'quantity_tier' => $row['quantity_tier'],
                        'minimum_quantity' => $row['minimum_quantity'],
                        'maximum_quantity' => $row['maximum_quantity'],
                        'markup_percentage' => $row['markup_percentage'],
                        'rounding_value' => $row['rounding_value'],
                        'status' => $row['status'],
                        'created_by' => $userName,
                        'created_at' => $now,
                        'updated_by' => $userName,
                        'updated_at' => $now,
                    ];
                }
            }

            foreach (array_chunk($details, 500) as $chunk) {
                DB::table('m_digital_lamination_pricing_rule_details')->insert($chunk);
            }

            $this->importedRows = count($details);
        });
    }

    protected function findEngine(string $name)
    {
        $key = strtolower($name);

        if (!array_key_exists($key, $this->engineCache)) {
            $this->engineCache[$key] = DB::table('m_engines')
                ->whereRaw('LOWER(TRIM(name)) = LOWER(?)', [$name])
                ->where('status', 'Active')
                ->first();
        }

        return $this->engineCache[$key];
    }

    protected function findLocation(string $name)
    {
        $key = strtolower($name);

        if (!array_key_exists($key, $this->locationCache)) {
            $this->locationCache[$key] = DB::table('m_locations')
                ->whereRaw('LOWER(TRIM(name)) = LOWER(?)', [$name])
                ->where('status', 'Active')
                ->first();
        }

        return $this->locationCache[$key];
    }

    protected function findVendor(string $name)
    {
        $key = strtolower($name);

        if (!array_key_exists($key, $this->vendorCache)) {
            $this->vendorCache[$key] = DB::table('m_vendors')
                ->whereRaw('LOWER(TRIM(name)) = LOWER(?)', [$name])
                ->where('status', 'Active')
                ->first();
        }

        return $this->vendorCache[$key];
    }

    protected function findCategory(string $name)
    {
        $key = strtolower($name);

        if (!array_key_exists($key, $this->categoryCache)) {
            $this->categoryCache[$key] = DB::table('m_categories')
                ->whereRaw('LOWER(TRIM(name)) = LOWER(?)', [$name])
                ->first();
        }

        return $this->categoryCache[$key];
    }

    protected function configLabel(array $row): string
    {
        return 'Mesin "' . $row['engine_name'] .
            '", Lokasi "' . $row['location_name'] . '"' .
            ($row['vendor_name'] ? ', Vendor "' . $row['vendor_name'] . '"' : '') .
            ', Kategori "' . $row['category_name'] . '"';
    }

    protected function headerKey(array $row): string
    {
        return implode('|', [
            $row['engine_id'],
            $row['location_id'],
            $row['vendor_id'] ?? 'null',
            $row['category_id'],
        ]);
    }

    protected function isEmptyRow(array $values): bool
    {
        foreach ($values as $value) {
            if ($value !== null && $value !== '') {
                return false;
            }
        }

        return true;
    }

    protected function normalizeText($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function normalizeKey(string $value): string
    {
        return strtolower(preg_replace('/[\s\/]+/', '', trim($value)));
    }

    protected function parseInteger($value, string $fieldName, int $excelRow): int
    {
        if (!is_numeric($value) || (float) $value != (int) $value) {
            throw new Exception(
                "Baris {$excelRow}: {$fieldName} harus berupa bilangan bulat."
            );
        }

        return (int) $value;
    }

    protected function parseNumber($value, string $fieldName, int $excelRow): float
    {
        if (!is_numeric($value)) {
            throw new Exception(
                "Baris {$excelRow}: {$fieldName} harus berupa angka."
            );
        }

        return (float) $value;
    }

    protected function resolveTier(int $minimum, int $maximum, int $excelRow): string
    {
        foreach ($this->tierRanges as $tier => [$tierMinimum, $tierMaximum]) {
            if ($minimum === $tierMinimum && $maximum === $tierMaximum) {
                return $tier;
            }
        }

        throw new Exception(
            "Baris {$excelRow}: Rentang Minimum dan Maksimum ({$minimum}-{$maximum}) tidak valid. Gunakan 1-1, 2-5, 6-50, 51-100, 101-300, atau 301-500."
        );
    }

    protected function mapPriceType(string $value, int $excelRow): string
    {
        return match (strtolower(trim($value))) {
            'harga umum' => 'general',
            'harga divisi' => 'division',
            default => throw new Exception(
                "Baris {$excelRow}: Tipe Harga \"{$value}\" tidak valid. Gunakan Harga Umum atau Harga Divisi."
            ),
        };
    }

    protected function mapPrintType(string $value, int $excelRow): string
    {
        return match (strtolower(trim($value))) {
            '1 muka' => 'single',
            '2 muka' => 'double',
            default => throw new Exception(
                "Baris {$excelRow}: Harga Print \"{$value}\" tidak valid. Gunakan 1 Muka atau 2 Muka."
            ),
        };
    }

    protected function normalizeStatus(string $value, int $excelRow): string
    {
        return match (strtolower(trim($value))) {
            'active' => 'Active',
            'inactive' => 'Inactive',
            default => throw new Exception(
                "Baris {$excelRow}: Status \"{$value}\" tidak valid. Gunakan Active atau Inactive."
            ),
        };
    }
}

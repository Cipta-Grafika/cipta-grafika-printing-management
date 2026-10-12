<?php

namespace App\Imports;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DigitalPricingRulesImport implements ToCollection, WithHeadingRow
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

    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            throw new Exception('File Excel tidak memiliki data.');
        }

        $headers = array_map(
            fn($header) => strtolower(trim($header)),
            array_keys($rows->first()->toArray())
        );

        $missingHeaders = array_diff($this->requiredHeaders, $headers);

        if (!empty($missingHeaders)) {
            throw new Exception(
                'Format Excel tidak sesuai. Kolom yang wajib tersedia: ' .
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
            $minimum = $data['minimum'] ?? null;
            $maksimum = $data['maksimum'] ?? null;
            $markup = $data['markup'] ?? null;
            $pembulatan = $data['pembulatan'] ?? null;
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
            if ($pembulatan !== null && $pembulatan !== '') {
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

            $engine = DB::table('m_engines')
                ->whereRaw('LOWER(TRIM(name)) = LOWER(?)', [$mesin])
                ->where('status', 'Active')
                ->first();

            if (!$engine) {
                throw new Exception(
                    "Baris {$excelRow}: Mesin \"{$mesin}\" tidak ditemukan atau tidak aktif."
                );
            }

            if ($this->normalizeKey($engine->name) !== 'digitalprinta3+') {
                throw new Exception(
                    "Baris {$excelRow}: Mesin harus \"Digital Print A3+\"."
                );
            }

            $location = DB::table('m_locations')
                ->whereRaw('LOWER(TRIM(name)) = LOWER(?)', [$lokasi])
                ->where('status', 'Active')
                ->first();

            if (!$location) {
                throw new Exception(
                    "Baris {$excelRow}: Lokasi \"{$lokasi}\" tidak ditemukan atau tidak aktif."
                );
            }

            $category = DB::table('m_categories')
                ->whereRaw('LOWER(TRIM(name)) = LOWER(?)', [$kategori])
                ->first();

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
                    "Baris {$excelRow}: Kategori Indoor/Outdoor tidak dapat digunakan untuk Digital Print A3+."
                );
            }

            $vendorData = null;
            if (strtolower(trim($location->name)) === 'outsourcing') {
                if ($vendor === null) {
                    throw new Exception(
                        "Baris {$excelRow}: Vendor wajib diisi untuk lokasi Outsourcing."
                    );
                }

                $vendorData = DB::table('m_vendors')
                    ->whereRaw('LOWER(TRIM(name)) = LOWER(?)', [$vendor])
                    ->where('status', 'Active')
                    ->first();

                if (!$vendorData) {
                    throw new Exception(
                        "Baris {$excelRow}: Vendor \"{$vendor}\" tidak ditemukan atau tidak aktif."
                    );
                }
            } elseif ($vendor !== null) {
                throw new Exception(
                    "Baris {$excelRow}: Vendor hanya dapat digunakan untuk lokasi Outsourcing."
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
                'price_type' => $this->mapPriceType($tipeHarga, $excelRow),
                'print_type' => $this->mapPrintType($hargaPrint, $excelRow),
                'quantity_tier' => $tier,
                'minimum_quantity' => $minimum,
                'maximum_quantity' => $maksimum,
                'markup_percentage' => $markupValue,
                'rounding_value' => $roundingValue,
                'status' => $this->normalizeStatus($status, $excelRow),
            ];
        }

        if (empty($preparedRows)) {
            throw new Exception('Tidak ada data yang dapat diimport.');
        }

        $groups = [];

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
                    "Baris {$row['excel_row']}: Kombinasi Tipe Harga, Harga Print, Minimum, dan Maksimum duplikat dengan baris {$groups['details'][$detailKey]}."
                );
            }

            $groups['details'][$detailKey] = $row['excel_row'];
            $groups['headers'][$headerKey][] = $row;
        }

        foreach ($groups['headers'] as $headerRows) {
            $firstRow = $headerRows[0];
            $expectedDetails = [];
            $statuses = [];

            foreach (array_keys($this->tierRanges) as $tier) {
                foreach (['general', 'division'] as $priceType) {
                    foreach (['single', 'double'] as $printType) {
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
                throw new Exception(
                    'Konfigurasi untuk Mesin "' . $firstRow['engine_name'] .
                        '", Lokasi "' . $firstRow['location_name'] .
                        '", dan Kategori "' . $firstRow['category_name'] .
                        '" belum lengkap. Setiap Harga Umum/Harga Divisi dan 1 Muka/2 Muka wajib memiliki seluruh rentang 1-1, 2-5, 6-50, 51-100, 101-300, dan 301-500.'
                );
            }

            foreach ($statuses as $combinationStatuses) {
                if (count(array_unique($combinationStatuses)) > 1) {
                    throw new Exception(
                        'Status harus konsisten untuk setiap kombinasi Tipe Harga dan Harga Print dalam satu konfigurasi.'
                    );
                }
            }

            $headerExists = DB::table('m_digital_pricing_rules')
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
                    'Konfigurasi Digital Print untuk Mesin "' .
                        $firstRow['engine_name'] . '", Lokasi "' .
                        $firstRow['location_name'] . '", Kategori "' .
                        $firstRow['category_name'] .
                        '" sudah tersedia dan tidak dapat diimport ulang.'
                );
            }
        }

        DB::transaction(function () use ($groups) {
            $now = Carbon::now();
            $userName = Auth::user()->name;
            $details = [];

            foreach ($groups['headers'] as $headerRows) {
                $firstRow = $headerRows[0];
                $headerId = DB::table('m_digital_pricing_rules')
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
                        'digital_pricing_rule_id' => $headerId,
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

            DB::table('m_digital_pricing_rule_details')->insert($details);
            $this->importedRows = count($details);
        });
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
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    protected function normalizeKey(string $value): string
    {
        return strtolower(preg_replace('/[\s\/]+/', '', trim($value)));
    }

    protected function parseInteger($value, string $fieldName, int $excelRow): int
    {
        if (!is_numeric($value) || is_bool($value) || (float) $value != (int) $value) {
            throw new Exception(
                "Baris {$excelRow}: {$fieldName} harus berupa bilangan bulat."
            );
        }

        return (int) $value;
    }

    protected function parseNumber($value, string $fieldName, int $excelRow): float
    {
        if (!is_numeric($value) || is_bool($value)) {
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
            "Baris {$excelRow}: Rentang Minimum dan Maksimum tidak valid. Gunakan 1-1, 2-5, 6-50, 51-100, 101-300, atau 301-500."
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

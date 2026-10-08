<?php

namespace App\Imports;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductionCostsImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;

    public array $errors = [];


    public function collection(Collection $collection): void
    {
        $rows = [];
        $combinations = [];


        /*
        |--------------------------------------------------------------------------
        | VALIDASI SELURUH DATA EXCEL
        |--------------------------------------------------------------------------
        */

        foreach ($collection as $index => $row) {

            $excelRow = $index + 2;

            $locationName =
                trim((string) ($row['lokasi'] ?? ''));

            $engineName =
                trim((string) ($row['mesin'] ?? ''));

            $materialName =
                trim((string) ($row['material'] ?? ''));

            $productionCost =
                trim((string) ($row['ongkos_produksi'] ?? ''));

            $finishingCost =
                trim((string) ($row['ongkos_finishing'] ?? ''));


            /*
            |--------------------------------------------------------------------------
            | SKIP BARIS KOSONG
            |--------------------------------------------------------------------------
            */

            if (
                $locationName === '' &&
                $engineName === '' &&
                $materialName === '' &&
                $productionCost === '' &&
                $finishingCost === ''
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDASI FIELD WAJIB
            |--------------------------------------------------------------------------
            */

            if (
                $locationName === '' ||
                $engineName === '' ||
                $materialName === '' ||
                $productionCost === '' ||
                $finishingCost === ''
            ) {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' => 'Terdapat data yang belum lengkap.'
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | CARI LOKASI
            |--------------------------------------------------------------------------
            */

            $location = DB::table('m_locations')
                ->whereRaw(
                    'LOWER(TRIM(name)) = ?',
                    [strtolower($locationName)]
                )
                ->where('status', 'Active')
                ->first();

            if (!$location) {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' =>
                    "Lokasi '{$locationName}' tidak ditemukan atau tidak Active."
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | CARI ENGINE
            |--------------------------------------------------------------------------
            */

            $engine = DB::table('m_engines')
                ->whereRaw(
                    'LOWER(TRIM(name)) = ?',
                    [strtolower($engineName)]
                )
                ->where('status', 'Active')
                ->first();

            if (!$engine) {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' =>
                    "Mesin '{$engineName}' tidak ditemukan atau tidak Active."
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | CARI MATERIAL
            |--------------------------------------------------------------------------
            */

            $material = DB::table('m_materials')
                ->whereRaw(
                    'LOWER(TRIM(material_name)) = ?',
                    [strtolower($materialName)]
                )
                ->where('status', 'Active')
                ->first();

            if (!$material) {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' =>
                    "Material '{$materialName}' tidak ditemukan atau tidak Active."
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | CEK HARGA MATERIAL
            |--------------------------------------------------------------------------
            |
            | Harga material diambil dari:
            |
            | m_materials.price_per_meter
            |
            */

            if ($material->price_per_meter === null) {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' =>
                    "Material '{$materialName}' belum memiliki harga per meter."
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | NORMALISASI ONGKOS
            |--------------------------------------------------------------------------
            */

            $productionCost =
                $this->normalizeAmount($productionCost);

            $finishingCost =
                $this->normalizeAmount($finishingCost);


            if (
                $productionCost === null ||
                $finishingCost === null
            ) {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' =>
                    'Ongkos Produksi dan Ongkos Finishing harus berupa angka.'
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDASI NILAI TIDAK BOLEH NEGATIF
            |--------------------------------------------------------------------------
            */

            if (
                (float) $productionCost < 0 ||
                (float) $finishingCost < 0
            ) {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' =>
                    'Ongkos Produksi dan Ongkos Finishing tidak boleh kurang dari 0.'
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKAT DALAM FILE EXCEL
            |--------------------------------------------------------------------------
            */

            $combinationKey =
                $location->id . '|' .
                $engine->id . '|' .
                $material->id;


            if (isset($combinations[$combinationKey])) {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' =>
                    'Kombinasi lokasi, mesin, dan material duplikat di dalam file Excel.'
                ];

                continue;
            }

            $combinations[$combinationKey] = true;


            /*
            |--------------------------------------------------------------------------
            | CEK DATA YANG SUDAH ADA DI DATABASE
            |--------------------------------------------------------------------------
            */

            $exists = DB::table('m_production_costs')
                ->where(
                    'location_id',
                    $location->id
                )
                ->where(
                    'engine_id',
                    $engine->id
                )
                ->where(
                    'material_id',
                    $material->id
                )
                ->exists();


            if ($exists) {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' =>
                    "Ongkos produksi untuk lokasi '{$locationName}', " .
                        "mesin '{$engineName}', material '{$materialName}' " .
                        "sudah tersedia."
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | SIMPAN DATA SEMENTARA
            |--------------------------------------------------------------------------
            */

            $rows[] = [
                'location_id' =>
                $location->id,

                'engine_id' =>
                $engine->id,

                'material_id' =>
                $material->id,

                'price_per_meter' =>
                (float) $material->price_per_meter,

                'production_cost' =>
                (float) $productionCost,

                'finishing_cost' =>
                (float) $finishingCost,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | JIKA ADA ERROR → BATALKAN SELURUH IMPORT
        |--------------------------------------------------------------------------
        */

        if (count($this->errors) > 0) {

            $this->imported = 0;

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | TIDAK ADA DATA VALID
        |--------------------------------------------------------------------------
        */

        if (count($rows) === 0) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | INSERT DATABASE
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($rows) {

            foreach ($rows as $row) {

                /*
                |--------------------------------------------------------------------------
                | HARGA MATERIAL / METER
                |--------------------------------------------------------------------------
                */

                $pricePerMeter =
                    $row['price_per_meter'];


                /*
                |--------------------------------------------------------------------------
                | ONGKOS PRODUKSI
                |--------------------------------------------------------------------------
                */

                $productionCost =
                    $row['production_cost'];


                /*
                |--------------------------------------------------------------------------
                | ONGKOS FINISHING
                |--------------------------------------------------------------------------
                */

                $finishingCost =
                    $row['finishing_cost'];


                /*
                |--------------------------------------------------------------------------
                | TOTAL COST
                |--------------------------------------------------------------------------
                |
                | Rumus:
                |
                | (1,2 × Harga Material)
                | + Ongkos Produksi
                | + Ongkos Finishing
                |
                */

                $totalCost =
                    1.2
                    * $pricePerMeter
                    + $productionCost
                    + $finishingCost;


                /*
                |--------------------------------------------------------------------------
                | HARGA POLOS
                |--------------------------------------------------------------------------
                |
                | Rumus:
                |
                | Harga Material × 2
                |
                | Dibulatkan ke atas ke kelipatan Rp1.000.
                |
                */

                $hargaPolosRaw =
                    $pricePerMeter * 2;

                $hargaPolos =
                    ceil($hargaPolosRaw / 1000) * 1000;


                /*
                |--------------------------------------------------------------------------
                | HARGA UMUM
                |--------------------------------------------------------------------------
                |
                | Markup = 40%
                |
                | Rumus:
                |
                | Total Cost + (Total Cost × 40%)
                |
                | Dibulatkan ke atas ke kelipatan Rp1.000.
                |
                */

                $markup = 40;

                $hargaUmumRaw =
                    $totalCost
                    + (
                        $totalCost
                        * ($markup / 100)
                    );

                $hargaUmum =
                    ceil($hargaUmumRaw / 1000) * 1000;


                /*
                |--------------------------------------------------------------------------
                | HARGA DIVISI
                |--------------------------------------------------------------------------
                |
                | Rumus mengikuti store():
                |
                | Harga Umum × 75%
                |
                | Tidak dibulatkan ke Rp1.000.
                |
                */

                $hargaDivisiRaw =
                    $hargaUmum * 0.75;

                $hargaDivisi =
                    $hargaDivisiRaw;


                /*
                |--------------------------------------------------------------------------
                | INSERT
                |--------------------------------------------------------------------------
                */

                DB::table('m_production_costs')
                    ->insert([

                        'location_id' =>
                        $row['location_id'],

                        'engine_id' =>
                        $row['engine_id'],

                        'material_id' =>
                        $row['material_id'],

                        'production_cost' =>
                        $productionCost,

                        'finishing_cost' =>
                        $finishingCost,

                        'total_cost' =>
                        $totalCost,

                        'harga_polos' =>
                        $hargaPolos,

                        'harga_umum' =>
                        $hargaUmum,

                        'harga_divisi' =>
                        $hargaDivisi,

                        'created_by' =>
                        Auth::user()->name ?? 'System',

                        'created_at' =>
                        Carbon::now(),

                        'updated_by' =>
                        Auth::user()->name ?? 'System',

                        'updated_at' =>
                        Carbon::now(),

                    ]);


                /*
                |--------------------------------------------------------------------------
                | COUNTER IMPORT
                |--------------------------------------------------------------------------
                */

                $this->imported++;
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALISASI FORMAT ANGKA
    |--------------------------------------------------------------------------
    */

    private function normalizeAmount(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | FORMAT INDONESIA
        |--------------------------------------------------------------------------
        |
        | 9.740,00 -> 9740.00
        |
        */

        if (str_contains($value, ',')) {

            $value = str_replace('.', '', $value);

            $value = str_replace(',', '.', $value);
        } else {

            /*
            |--------------------------------------------------------------------------
            | FORMAT RIBUAN
            |--------------------------------------------------------------------------
            |
            | 9.740 -> 9740
            |
            | FORMAT DECIMAL
            |--------------------------------------------------------------------------
            |
            | 9740.50 -> tetap 9740.50
            |
            */

            if (
                preg_match(
                    '/^\d{1,3}(\.\d{3})+$/',
                    $value
                )
            ) {

                $value = str_replace('.', '', $value);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI NUMERIC
        |--------------------------------------------------------------------------
        */

        if (!is_numeric($value)) {
            return null;
        }


        return $value;
    }
}

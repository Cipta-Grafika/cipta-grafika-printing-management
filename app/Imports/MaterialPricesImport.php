<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MaterialPricesImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;

    public array $errors = [];

    public function collection(Collection $collection): void
    {
        $rows = [];
        $materialIds = [];

        foreach ($collection as $index => $row) {

            $excelRow = $index + 2;

            $materialName = trim((string) ($row['nama_material'] ?? ''));
            $pricePerMeter = trim((string) ($row['harga_permeter'] ?? ''));

            /*
            |--------------------------------------------------------------------------
            | SKIP BARIS KOSONG
            |--------------------------------------------------------------------------
            */

            if (
                $materialName === '' &&
                $pricePerMeter === ''
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI FIELD WAJIB
            |--------------------------------------------------------------------------
            */

            if (
                $materialName === '' ||
                $pricePerMeter === ''
            ) {
                $this->errors[] = [
                    'row' => $excelRow,
                    'message' => 'Terdapat data yang belum lengkap.'
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
                    'message' => "Material '{$materialName}' tidak ditemukan atau tidak Active."
                ];

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | NORMALISASI HARGA
            |--------------------------------------------------------------------------
            */

            $pricePerMeter = $this->normalizeAmount($pricePerMeter);

            if ($pricePerMeter === null) {
                $this->errors[] = [
                    'row' => $excelRow,
                    'message' => 'Harga Permeter harus berupa angka.'
                ];

                continue;
            }

            if ((float) $pricePerMeter < 0) {
                $this->errors[] = [
                    'row' => $excelRow,
                    'message' => 'Harga Permeter tidak boleh kurang dari 0.'
                ];

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKAT DALAM FILE EXCEL
            |--------------------------------------------------------------------------
            */

            if (isset($materialIds[$material->id])) {
                $this->errors[] = [
                    'row' => $excelRow,
                    'message' => 'Material duplikat di dalam file Excel.'
                ];

                continue;
            }

            $materialIds[$material->id] = true;

            /*
            |--------------------------------------------------------------------------
            | CEK DATA YANG SUDAH ADA DI DATABASE
            |--------------------------------------------------------------------------
            */

            $exists = DB::table('m_material_prices')
                ->where('material_id', $material->id)
                ->exists();

            if ($exists) {
                $this->errors[] = [
                    'row' => $excelRow,
                    'message' =>
                    "Harga Permeter untuk material '{$materialName}' sudah tersedia."
                ];

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | SIMPAN DATA SEMENTARA
            |--------------------------------------------------------------------------
            */

            $rows[] = [
                'material_id' => $material->id,
                'price_per_meter' => $pricePerMeter,
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
        | INSERT
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($rows) {

            foreach ($rows as $row) {

                DB::table('m_material_prices')->insert([
                    'material_id' => $row['material_id'],
                    'price_per_meter' => $row['price_per_meter'],
                    'created_by' => Auth::user()->name ?? 'System',
                    'created_at' => Carbon::now(),
                    'updated_by' => Auth::user()->name ?? 'System',
                    'updated_at' => Carbon::now(),
                ]);

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
        | Format Indonesia:
        | 6.000,00 → 6000.00
        */

        if (str_contains($value, ',')) {

            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        } else {

            /*
            | Format ribuan:
            | 6.000 → 6000
            |
            | Format decimal:
            | 6000.50 → tetap 6000.50
            */

            if (preg_match('/^\d{1,3}(\.\d{3})+$/', $value)) {
                $value = str_replace('.', '', $value);
            }
        }

        if (!is_numeric($value)) {
            return null;
        }

        return $value;
    }
}

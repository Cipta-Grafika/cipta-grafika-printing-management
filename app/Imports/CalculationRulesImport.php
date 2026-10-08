<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CalculationRulesImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;

    public array $errors = [];

    public function collection(Collection $collection): void
    {
        $rows = [];

        /*
        |--------------------------------------------------------------------------
        | Menyimpan mesin yang sudah ditemukan di Excel
        |--------------------------------------------------------------------------
        */

        $excelKeys = [];

        foreach ($collection as $index => $row) {

            /*
            |--------------------------------------------------------------------------
            | Nomor baris Excel
            |--------------------------------------------------------------------------
            |
            | Heading berada di baris 1,
            | sehingga data pertama dimulai dari baris 2.
            |
            */

            $excelRow = $index + 2;


            /*
            |--------------------------------------------------------------------------
            | Ambil nilai dari Excel
            |--------------------------------------------------------------------------
            */

            $code = trim((string) ($row['kode'] ?? ''));

            $name = trim((string) ($row['nama_aturan'] ?? ''));

            $engineName = trim((string) ($row['mesin'] ?? ''));

            $minimumChargeValue =
                trim((string) ($row['minimum_charge'] ?? ''));

            $description =
                trim((string) ($row['deskripsi'] ?? ''));

            $statusValue =
                trim((string) ($row['status'] ?? ''));


            /*
            |--------------------------------------------------------------------------
            | Skip baris kosong
            |--------------------------------------------------------------------------
            */

            if (
                $code === '' &&
                $name === '' &&
                $engineName === '' &&
                $minimumChargeValue === '' &&
                $description === '' &&
                $statusValue === ''
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Validasi Kode
            |--------------------------------------------------------------------------
            |
            | Kode boleh kosong dan tidak harus unik.
            |
            */

            if ($code !== '' && mb_strlen($code) > 50) {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' => 'Kode maksimal 50 karakter.',
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Validasi Nama Aturan
            |--------------------------------------------------------------------------
            */

            if ($name === '') {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' => 'Nama aturan wajib diisi.',
                ];

                continue;
            }


            if (mb_strlen($name) > 150) {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' => 'Nama aturan maksimal 150 karakter.',
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Validasi Mesin
            |--------------------------------------------------------------------------
            */

            if ($engineName === '') {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' => 'Mesin wajib diisi.',
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Cari Mesin
            |--------------------------------------------------------------------------
            |
            | Tidak membedakan huruf besar dan kecil.
            |
            | Contoh:
            |
            | Digital Print A3+
            | digital print a3+
            | DIGITAL PRINT A3+
            |
            | dianggap mesin yang sama.
            |
            */

            $engine = DB::table('m_engines')
                ->whereRaw(
                    'LOWER(TRIM(name)) = ?',
                    [strtolower($engineName)]
                )
                ->first();


            if (!$engine) {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' =>
                    'Mesin "' .
                        $engineName .
                        '" tidak terdaftar di master mesin.',
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Cek Duplikasi Mesin Dalam Excel
            |--------------------------------------------------------------------------
            |
            | Satu mesin hanya boleh memiliki satu Calculation Rule.
            |
            */

            $duplicateKey = $engine->id;


            if (isset($excelKeys[$duplicateKey])) {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' =>
                    'Mesin "' .
                        $engine->name .
                        '" duplikat dengan baris Excel ' .
                        $excelKeys[$duplicateKey] .
                        '.',
                ];

                continue;
            }


            $excelKeys[$duplicateKey] = $excelRow;


            /*
            |--------------------------------------------------------------------------
            | Cek Duplikasi Mesin di Database
            |--------------------------------------------------------------------------
            */

            $exists = DB::table('m_calculation_rules')
                ->where('engine_id', $engine->id)
                ->exists();


            if ($exists) {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' =>
                    'Calculation Rule untuk mesin "' .
                        $engine->name .
                        '" sudah terdaftar.',
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Validasi Minimum Charge
            |--------------------------------------------------------------------------
            */

            if ($minimumChargeValue === '') {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' => 'Minimum Charge wajib diisi.',
                ];

                continue;
            }


            $minimumCharge =
                $this->normalizeAmount(
                    $minimumChargeValue
                );


            if (
                $minimumCharge === null ||
                (float) $minimumCharge < 0
            ) {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' =>
                    'Minimum Charge harus berupa angka yang valid dan tidak boleh negatif.',
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Normalisasi Status
            |--------------------------------------------------------------------------
            |
            | Active / active / ACTIVE -> Active
            | Inactive / inactive / INACTIVE -> Inactive
            |
            */

            $statusLower =
                strtolower($statusValue);


            if ($statusLower === 'active') {

                $status = 'Active';
            } elseif ($statusLower === 'inactive') {

                $status = 'Inactive';
            } else {

                $this->errors[] = [
                    'row' => $excelRow,
                    'message' =>
                    'Status harus berupa Active atau Inactive.',
                ];

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Simpan Data Valid ke Temporary Rows
            |--------------------------------------------------------------------------
            */

            $rows[] = [
                'code' =>
                $code !== ''
                    ? $code
                    : null,

                'name' =>
                $name,

                'engine_id' =>
                $engine->id,

                'minimum_charge' =>
                (float) $minimumCharge,

                'description' =>
                $description !== ''
                    ? $description
                    : null,

                'status' =>
                $status,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | Jika Ada Error
        |--------------------------------------------------------------------------
        |
        | Import dibatalkan seluruhnya.
        |
        */

        if (count($this->errors) > 0) {

            $this->imported = 0;

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Tidak Ada Data Valid
        |--------------------------------------------------------------------------
        */

        if (count($rows) === 0) {

            $this->imported = 0;

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Insert ke Database
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($rows) {

            $user =
                Auth::user()->name ?? 'System';


            foreach ($rows as $row) {

                DB::table('m_calculation_rules')
                    ->insert([
                        'code' =>
                        $row['code'],

                        'name' =>
                        $row['name'],

                        'engine_id' =>
                        $row['engine_id'],

                        'description' =>
                        $row['description'],

                        'minimum_charge' =>
                        $row['minimum_charge'],

                        'status' =>
                        $row['status'],

                        'created_by' =>
                        $user,

                        'created_at' =>
                        now(),

                        'updated_by' =>
                        $user,

                        'updated_at' =>
                        now(),
                    ]);


                $this->imported++;
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Normalize Amount
    |--------------------------------------------------------------------------
    |
    | Contoh:
    |
    | 1
    | 1,00
    | 1.00
    | 1.000
    | 1.000,00
    |
    | akan dikonversi menjadi format angka database.
    |
    */

    private function normalizeAmount($value): ?string
    {
        if ($value === null) {
            return null;
        }


        $value = trim((string) $value);


        if ($value === '') {
            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Jika menggunakan koma sebagai decimal separator
        |--------------------------------------------------------------------------
        */

        if (str_contains($value, ',')) {

            $value =
                str_replace('.', '', $value);

            $value =
                str_replace(',', '.', $value);
        } else {

            /*
            |--------------------------------------------------------------------------
            | Jika menggunakan titik sebagai pemisah ribuan
            |--------------------------------------------------------------------------
            */

            if (
                preg_match(
                    '/^\d{1,3}(\.\d{3})+$/',
                    $value
                )
            ) {
                $value =
                    str_replace('.', '', $value);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Validasi Numeric
        |--------------------------------------------------------------------------
        */

        if (!is_numeric($value)) {
            return null;
        }


        return $value;
    }
}

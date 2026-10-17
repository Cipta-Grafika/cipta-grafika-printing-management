<?php

namespace App\Imports;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CuttingsImport implements ToCollection, WithHeadingRow
{
    public int $importedCuttings = 0;

    public int $importedSizes = 0;

    /*
    |--------------------------------------------------------------------------
    | COLLECTION
    |--------------------------------------------------------------------------
    |
    | Format template:
    | No | Nama Jasa Potong | Status | Lebar | Panjang | Unit
    |
    | Satu baris = satu ukuran.
    | Jasa potong dengan beberapa ukuran ditulis di beberapa baris
    | dengan Nama Jasa Potong & Status yang sama.
    |
    */

    public function collection(Collection $collection): void
    {
        /*
        |--------------------------------------------------------------------------
        | SIMPAN DATA YANG SUDAH DIVALIDASI
        |--------------------------------------------------------------------------
        */

        $cuttings = [];

        /*
        |--------------------------------------------------------------------------
        | TRACKING DUPLIKAT UKURAN DALAM FILE
        |--------------------------------------------------------------------------
        */

        $sizeKeys = [];

        /*
        |--------------------------------------------------------------------------
        | TRACKING STATUS PER JASA POTONG
        |--------------------------------------------------------------------------
        */

        $cuttingStatuses = [];

        /*
        |--------------------------------------------------------------------------
        | LOOP DATA EXCEL
        |--------------------------------------------------------------------------
        */

        foreach ($collection as $index => $row) {

            $excelRow = $index + 2;

            /*
            |--------------------------------------------------------------------------
            | AMBIL DATA EXCEL
            |--------------------------------------------------------------------------
            */

            $no = trim((string) ($row['no'] ?? ''));

            $cuttingName = trim((string) ($row['nama_jasa_potong'] ?? ''));

            $status = trim((string) ($row['status'] ?? ''));

            $widthValue = trim((string) ($row['lebar'] ?? ''));

            $lengthValue = trim((string) ($row['panjang'] ?? ''));

            $unit = trim((string) ($row['unit'] ?? ''));

            /*
            |--------------------------------------------------------------------------
            | SKIP BARIS BENAR-BENAR KOSONG
            |--------------------------------------------------------------------------
            */

            if (
                $no === '' &&
                $cuttingName === '' &&
                $status === '' &&
                $widthValue === '' &&
                $lengthValue === '' &&
                $unit === ''
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI NO
            |--------------------------------------------------------------------------
            */

            if (
                $no === '' ||
                !is_numeric($no) ||
                (int) $no <= 0
            ) {
                throw new Exception(
                    'Baris ' . $excelRow .
                        ': Kolom No wajib berupa angka lebih dari 0.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI NO SEQUENTIAL
            |--------------------------------------------------------------------------
            */

            $expectedNo = count($cuttings) + 1;

            if ((int) $no !== $expectedNo) {
                throw new Exception(
                    'Baris ' . $excelRow .
                        ': Nomor pada kolom No harus berurutan mulai dari 1.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI NAMA JASA POTONG
            |--------------------------------------------------------------------------
            */

            if ($cuttingName === '') {
                throw new Exception(
                    'Baris ' . $excelRow .
                        ': Nama Jasa Potong wajib diisi.'
                );
            }

            if (mb_strlen($cuttingName) > 255) {
                throw new Exception(
                    'Baris ' . $excelRow .
                        ': Nama Jasa Potong maksimal 255 karakter.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | NORMALISASI NAMA
            |--------------------------------------------------------------------------
            */

            $cuttingNameKey = mb_strtolower(trim($cuttingName));

            /*
            |--------------------------------------------------------------------------
            | NORMALISASI STATUS
            |--------------------------------------------------------------------------
            */

            $statusLower = strtolower($status);

            if ($statusLower === 'active') {
                $status = 'Active';
            } elseif ($statusLower === 'inactive') {
                $status = 'Inactive';
            } else {
                throw new Exception(
                    'Baris ' . $excelRow .
                        ': Status harus berupa Active atau Inactive.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI STATUS PER NAMA JASA POTONG
            |--------------------------------------------------------------------------
            */

            if (
                isset($cuttingStatuses[$cuttingNameKey]) &&
                $cuttingStatuses[$cuttingNameKey] !== $status
            ) {
                throw new Exception(
                    'Baris ' . $excelRow .
                        ': Status jasa potong "' .
                        $cuttingName .
                        '" tidak konsisten. ' .
                        'Semua baris dengan nama jasa potong yang sama ' .
                        'harus menggunakan status yang sama.'
                );
            }

            $cuttingStatuses[$cuttingNameKey] = $status;

            /*
            |--------------------------------------------------------------------------
            | VALIDASI LEBAR
            |--------------------------------------------------------------------------
            */

            if (
                $widthValue === '' ||
                !is_numeric($widthValue) ||
                (float) $widthValue <= 0
            ) {
                throw new Exception(
                    'Baris ' . $excelRow .
                        ': Lebar wajib berupa angka lebih dari 0.'
                );
            }

            $width = $this->normalizeDecimal($widthValue);

            /*
            |--------------------------------------------------------------------------
            | VALIDASI PANJANG (OPSIONAL)
            |--------------------------------------------------------------------------
            |
            | Kosong -> NULL
            | Diisi  -> harus angka lebih dari 0
            |
            */

            $length = null;

            if ($lengthValue !== '') {

                if (
                    !is_numeric($lengthValue) ||
                    (float) $lengthValue <= 0
                ) {
                    throw new Exception(
                        'Baris ' . $excelRow .
                            ': Panjang harus berupa angka lebih dari 0.'
                    );
                }

                $length = $this->normalizeDecimal($lengthValue);
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI UNIT
            |--------------------------------------------------------------------------
            */

            if ($unit === '') {
                throw new Exception(
                    'Baris ' . $excelRow .
                        ': Unit wajib diisi.'
                );
            }

            $unitNormalized = strtolower(trim($unit));

            if (
                $unitNormalized !== 'cm' &&
                $unitNormalized !== 'm'
            ) {
                throw new Exception(
                    'Baris ' . $excelRow .
                        ': Unit harus berupa cm atau m.'
                );
            }

            $unit = $unitNormalized;

            /*
            |--------------------------------------------------------------------------
            | VALIDASI DUPLIKAT UKURAN DALAM FILE
            |--------------------------------------------------------------------------
            */

            $cuttingSizeKey =
                $cuttingNameKey .
                '|' .
                $width .
                '|' .
                ($length ?? 'NULL') .
                '|' .
                $unit;

            if (isset($sizeKeys[$cuttingSizeKey])) {
                throw new Exception(
                    'Baris ' . $excelRow .
                        ': Ukuran ' .
                        $width .
                        ($length !== null ? ' × ' . $length : '') .
                        ' ' .
                        $unit .
                        ' untuk jasa potong "' .
                        $cuttingName .
                        '" merupakan duplikat di dalam file Excel.'
                );
            }

            $sizeKeys[$cuttingSizeKey] = true;

            /*
            |--------------------------------------------------------------------------
            | SIMPAN DATA VALID KE MEMORY
            |--------------------------------------------------------------------------
            */

            $cuttings[] = [
                'excel_row' => $excelRow,
                'no'        => (int) $no,
                'name'      => $cuttingName,
                'name_key'  => $cuttingNameKey,
                'status'    => $status,
                'width'     => $width,
                'length'    => $length,
                'unit'      => $unit,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | FILE TIDAK MEMILIKI DATA
        |--------------------------------------------------------------------------
        */

        if (empty($cuttings)) {
            throw new Exception(
                'File Excel tidak memiliki data jasa potong yang dapat diimport.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION DATABASE
        |--------------------------------------------------------------------------
        |
        | Jika terjadi satu error saja, seluruh insert akan rollback.
        |
        */

        DB::transaction(function () use ($cuttings) {

            $userName = Auth::user()->name ?? 'System';

            /*
            |--------------------------------------------------------------------------
            | GROUP BERDASARKAN NAMA JASA POTONG
            |--------------------------------------------------------------------------
            */

            $groups = collect($cuttings)->groupBy('name_key');

            /*
            |--------------------------------------------------------------------------
            | VALIDASI SEMUA MASTER TERLEBIH DAHULU
            |--------------------------------------------------------------------------
            |
            | Kalau satu nama saja sudah ada di database, seluruh import gagal.
            |
            */

            foreach ($groups as $group) {

                $first = $group->first();

                $existingCutting = DB::table('m_cuttings')
                    ->whereRaw('LOWER(TRIM(name)) = ?', [$first['name_key']])
                    ->lockForUpdate()
                    ->first();

                if ($existingCutting) {
                    throw new Exception(
                        'Jasa potong "' .
                            $first['name'] .
                            '" sudah terdaftar di database. ' .
                            'Import dibatalkan dan tidak ada data yang disimpan.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | INSERT SEMUA MASTER DAN UKURAN
            |--------------------------------------------------------------------------
            */

            foreach ($groups as $group) {

                $first = $group->first();

                $now = now();

                $cuttingId = DB::table('m_cuttings')
                    ->insertGetId([
                        'name'       => $first['name'],
                        'status'     => $first['status'],
                        'created_by' => $userName,
                        'created_at' => $now,
                        'updated_by' => $userName,
                        'updated_at' => $now,
                    ]);

                $this->importedCuttings++;

                foreach ($group as $item) {

                    DB::table('m_cutting_sizes')
                        ->insert([
                            'cutting_id' => $cuttingId,
                            'width'      => $item['width'],
                            'length'     => $item['length'],
                            'unit'       => $item['unit'],
                            'created_by' => $userName,
                            'created_at' => $now,
                            'updated_by' => $userName,
                            'updated_at' => $now,
                        ]);

                    $this->importedSizes++;
                }
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALIZE DECIMAL
    |--------------------------------------------------------------------------
    |
    | 21 -> 21 | 21.5 -> 21.5 | 21.50 -> 21.5
    |
    */

    private function normalizeDecimal($value): float
    {
        return round((float) $value, 2);
    }
}


<?php

namespace App\Imports;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LaminationsImport implements ToCollection, WithHeadingRow
{
    public int $importedLaminations = 0;

    public int $importedSizes = 0;

    /*
    |--------------------------------------------------------------------------
    | COLLECTION
    |--------------------------------------------------------------------------
    */

    public function collection(Collection $collection): void
    {
        /*
        |--------------------------------------------------------------------------
        | SIMPAN DATA YANG SUDAH DIVALIDASI
        |--------------------------------------------------------------------------
        */

        $laminations = [];

        /*
        |--------------------------------------------------------------------------
        | TRACKING DUPLIKAT UKURAN DALAM FILE
        |--------------------------------------------------------------------------
        */

        $sizeKeys = [];

        /*
        |--------------------------------------------------------------------------
        | TRACKING STATUS PER LAMINASI
        |--------------------------------------------------------------------------
        */

        $laminationStatuses = [];

        /*
        |--------------------------------------------------------------------------
        | TRACKING KODE PER LAMINASI
        |--------------------------------------------------------------------------
        */

        $laminationCodes = [];

        /*
        |--------------------------------------------------------------------------
        | TRACKING NAMA LAMINASI DALAM FILE
        |--------------------------------------------------------------------------
        */

        $laminationNames = [];

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

            $no = trim(
                (string) ($row['no'] ?? '')
            );

            $laminationCode = trim(
                (string) ($row['kode_laminasi'] ?? '')
            );

            $laminationName = trim(
                (string) ($row['nama_laminasi'] ?? '')
            );

            $status = trim(
                (string) ($row['status'] ?? '')
            );

            $widthValue = trim(
                (string) ($row['lebar'] ?? '')
            );

            $lengthValue = trim(
                (string) ($row['panjang'] ?? '')
            );

            $unit = trim(
                (string) ($row['unit'] ?? '')
            );

            /*
            |--------------------------------------------------------------------------
            | SKIP BARIS BENAR-BENAR KOSONG
            |--------------------------------------------------------------------------
            */

            if (
                $no === '' &&
                $laminationCode === '' &&
                $laminationName === '' &&
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

            $expectedNo = count($laminations) + 1;

            if ((int) $no !== $expectedNo) {
                throw new Exception(
                    'Baris ' . $excelRow .
                        ': Nomor pada kolom No harus berurutan mulai dari 1.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI KODE LAMINASI
            |--------------------------------------------------------------------------
            */

            if (mb_strlen($laminationCode) > 255) {
                throw new Exception(
                    'Baris ' . $excelRow .
                        ': Kode Laminasi maksimal 255 karakter.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI NAMA LAMINASI
            |--------------------------------------------------------------------------
            */

            if ($laminationName === '') {
                throw new Exception(
                    'Baris ' . $excelRow .
                        ': Nama Laminasi wajib diisi.'
                );
            }

            if (mb_strlen($laminationName) > 255) {
                throw new Exception(
                    'Baris ' . $excelRow .
                        ': Nama Laminasi maksimal 255 karakter.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | NORMALISASI NAMA
            |--------------------------------------------------------------------------
            */

            $laminationNameKey = strtolower(
                trim($laminationName)
            );

            $laminationNames[$laminationNameKey] = true;

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
            | VALIDASI STATUS PER NAMA LAMINASI
            |--------------------------------------------------------------------------
            */

            if (
                isset($laminationStatuses[$laminationNameKey]) &&
                $laminationStatuses[$laminationNameKey] !== $status
            ) {
                throw new Exception(
                    'Baris ' . $excelRow .
                        ': Status laminasi "' .
                        $laminationName .
                        '" tidak konsisten. ' .
                        'Semua baris dengan nama laminasi yang sama ' .
                        'harus menggunakan status yang sama.'
                );
            }

            $laminationStatuses[$laminationNameKey] = $status;

            /*
            |--------------------------------------------------------------------------
            | VALIDASI KODE PER NAMA LAMINASI
            |--------------------------------------------------------------------------
            |
            | Kode boleh kosong.
            |
            | Tetapi apabila nama laminasi yang sama muncul beberapa kali,
            | kode harus konsisten.
            |
            */

            $codeKey = $laminationCode !== ''
                ? strtolower($laminationCode)
                : null;

            if (
                isset($laminationCodes[$laminationNameKey])
            ) {
                $existingCode =
                    $laminationCodes[$laminationNameKey];

                if ($existingCode !== $codeKey) {
                    throw new Exception(
                        'Baris ' . $excelRow .
                            ': Kode Laminasi "' .
                            $laminationName .
                            '" tidak konsisten. ' .
                            'Semua baris dengan nama laminasi yang sama ' .
                            'harus menggunakan kode yang sama.'
                    );
                }
            } else {
                $laminationCodes[$laminationNameKey] =
                    $codeKey;
            }

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

            /*
            |--------------------------------------------------------------------------
            | NORMALISASI LEBAR
            |--------------------------------------------------------------------------
            */

            $width = $this->normalizeDecimal(
                $widthValue
            );

            /*
            |--------------------------------------------------------------------------
            | VALIDASI PANJANG
            |--------------------------------------------------------------------------
            |
            | Karena Master Laminasi sekarang tidak lagi bergantung pada kategori,
            | panjang bersifat opsional.
            |
            | Kosong -> NULL
            | Diisi -> harus angka lebih dari 0
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

                $length = $this->normalizeDecimal(
                    $lengthValue
                );
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

            $unitNormalized = strtolower(
                trim($unit)
            );

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
            | KEY UKURAN
            |--------------------------------------------------------------------------
            */

            $sizeKey =
                $width .
                '|' .
                ($length ?? 'NULL') .
                '|' .
                $unit;

            /*
            |--------------------------------------------------------------------------
            | KEY DUPLIKAT UKURAN DALAM SATU LAMINASI
            |--------------------------------------------------------------------------
            */

            $laminationSizeKey =
                $laminationNameKey .
                '|' .
                $sizeKey;

            /*
            |--------------------------------------------------------------------------
            | VALIDASI DUPLIKAT UKURAN DALAM FILE
            |--------------------------------------------------------------------------
            |
            | Jika ukuran yang sama muncul dua kali untuk laminasi yang sama,
            | seluruh import ditolak.
            |
            */

            if (
                isset($sizeKeys[$laminationSizeKey])
            ) {
                throw new Exception(
                    'Baris ' . $excelRow .
                        ': Ukuran ' .
                        $width .
                        (
                            $length !== null
                            ? ' × ' . $length
                            : ''
                        ) .
                        ' ' .
                        $unit .
                        ' untuk laminasi "' .
                        $laminationName .
                        '" merupakan duplikat di dalam file Excel.'
                );
            }

            $sizeKeys[$laminationSizeKey] = true;

            /*
            |--------------------------------------------------------------------------
            | SIMPAN DATA VALID KE MEMORY
            |--------------------------------------------------------------------------
            */

            $laminations[] = [
                'excel_row' =>
                $excelRow,

                'no' =>
                (int) $no,

                'lamination_code' =>
                $laminationCode !== ''
                    ? $laminationCode
                    : null,

                'name' =>
                $laminationName,

                'name_key' =>
                $laminationNameKey,

                'status' =>
                $status,

                'width' =>
                $width,

                'length' =>
                $length,

                'unit' =>
                $unit,

                'size_key' =>
                $sizeKey,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | FILE TIDAK MEMILIKI DATA
        |--------------------------------------------------------------------------
        */

        if (empty($laminations)) {
            throw new Exception(
                'File Excel tidak memiliki data laminasi yang dapat diimport.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TRANSACTION DATABASE
        |--------------------------------------------------------------------------
        |
        | Semua proses database berada dalam satu transaction.
        |
        | Jika terjadi satu error saja, seluruh insert akan rollback.
        |
        */

        DB::transaction(function () use ($laminations) {

            $userName =
                Auth::user()->name ?? 'System';

            /*
            |--------------------------------------------------------------------------
            | GROUP BERDASARKAN NAMA LAMINASI
            |--------------------------------------------------------------------------
            */

            $groups = collect($laminations)
                ->groupBy('name_key');

            /*
            |--------------------------------------------------------------------------
            | VALIDASI SEMUA MASTER TERLEBIH DAHULU
            |--------------------------------------------------------------------------
            |
            | Kita cek seluruh master sebelum melakukan INSERT.
            |
            | Kalau satu nama saja sudah ada, seluruh import gagal.
            |
            */

            foreach ($groups as $group) {

                $first = $group->first();

                $existingLamination =
                    DB::table('m_laminations')
                    ->whereRaw(
                        'LOWER(TRIM(name)) = ?',
                        [
                            $first['name_key']
                        ]
                    )
                    ->lockForUpdate()
                    ->first();

                if ($existingLamination) {
                    throw new Exception(
                        'Laminasi "' .
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

                /*
                |--------------------------------------------------------------------------
                | INSERT MASTER LAMINASI
                |--------------------------------------------------------------------------
                */

                $now = now();

                $laminationId =
                    DB::table('m_laminations')
                    ->insertGetId([
                        'lamination_code' =>
                        $first['lamination_code'],

                        'name' =>
                        $first['name'],

                        'status' =>
                        $first['status'],

                        'created_by' =>
                        $userName,

                        'created_at' =>
                        $now,

                        'updated_by' =>
                        $userName,

                        'updated_at' =>
                        $now,
                    ]);

                $this->importedLaminations++;

                /*
                |--------------------------------------------------------------------------
                | INSERT UKURAN
                |--------------------------------------------------------------------------
                */

                foreach ($group as $item) {

                    /*
                    |--------------------------------------------------------------------------
                    | CEK UKURAN DI DATABASE
                    |--------------------------------------------------------------------------
                    |
                    | Pengaman tambahan.
                    |
                    */

                    $existingSizeQuery =
                        DB::table('m_lamination_sizes')
                        ->where(
                            'lamination_id',
                            $laminationId
                        )
                        ->where(
                            'width',
                            $item['width']
                        )
                        ->where(
                            'unit',
                            $item['unit']
                        );

                    if ($item['length'] === null) {

                        $existingSizeQuery
                            ->whereNull('length');
                    } else {

                        $existingSizeQuery
                            ->where(
                                'length',
                                $item['length']
                            );
                    }

                    if ($existingSizeQuery->exists()) {
                        throw new Exception(
                            'Ukuran ' .
                                $item['width'] .
                                (
                                    $item['length'] !== null
                                    ? ' × ' . $item['length']
                                    : ''
                                ) .
                                ' ' .
                                $item['unit'] .
                                ' untuk laminasi "' .
                                $item['name'] .
                                '" sudah terdaftar. ' .
                                'Import dibatalkan dan tidak ada data yang disimpan.'
                        );
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | INSERT UKURAN
                    |--------------------------------------------------------------------------
                    */

                    DB::table('m_lamination_sizes')
                        ->insert([
                            'lamination_id' =>
                            $laminationId,

                            'width' =>
                            $item['width'],

                            'length' =>
                            $item['length'],

                            'unit' =>
                            $item['unit'],

                            'created_by' =>
                            $userName,

                            'created_at' =>
                            $now,

                            'updated_by' =>
                            $userName,

                            'updated_at' =>
                            $now,
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
    | Menyamakan format angka decimal untuk:
    |
    | - width
    | - length
    |
    | Contoh:
    |
    | 21      -> 21
    | 21.5    -> 21.5
    | 21.50   -> 21.5
    |
    */

    private function normalizeDecimal($value): float
    {
        return round(
            (float) $value,
            2
        );
    }
}

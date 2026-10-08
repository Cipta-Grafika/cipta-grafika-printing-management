<?php

namespace App\Imports;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MaterialsImport implements ToCollection, WithHeadingRow
{
    public int $importedMaterials = 0;

    public int $importedSizes = 0;

    public int $skippedDuplicate = 0;

    public int $skippedInvalid = 0;


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

        $materials = [];


        /*
        |--------------------------------------------------------------------------
        | KUMPULKAN KATEGORI YANG TIDAK DITEMUKAN
        |--------------------------------------------------------------------------
        */

        $invalidCategories = [];


        /*
        |--------------------------------------------------------------------------
        | SIMPAN STATUS PER MATERIAL
        |--------------------------------------------------------------------------
        |
        | Satu material tidak boleh memiliki status Active dan Inactive
        | sekaligus dalam satu file Excel.
        |
        */

        $materialStatuses = [];


        /*
        |--------------------------------------------------------------------------
        | LOOP DATA EXCEL
        |--------------------------------------------------------------------------
        */

        foreach ($collection as $row) {

            /*
            |--------------------------------------------------------------------------
            | AMBIL DATA EXCEL
            |--------------------------------------------------------------------------
            */

            $categoryName = trim(
                (string) ($row['kategori'] ?? '')
            );

            $materialCode = trim(
                (string) ($row['kode_material'] ?? '')
            );

            $materialName = trim(
                (string) ($row['nama_material'] ?? '')
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
            | LEWATI BARIS BENAR-BENAR KOSONG
            |--------------------------------------------------------------------------
            */

            if (
                $categoryName === '' &&
                $materialCode === '' &&
                $materialName === '' &&
                $status === '' &&
                $widthValue === '' &&
                $lengthValue === '' &&
                $unit === ''
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDASI DATA WAJIB
            |--------------------------------------------------------------------------
            */

            if (
                $categoryName === '' ||
                $materialName === '' ||
                $status === '' ||
                $widthValue === '' ||
                $unit === ''
            ) {
                $this->skippedInvalid++;

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | CARI KATEGORI
            | CASE INSENSITIVE
            |--------------------------------------------------------------------------
            */

            $category = DB::table('m_categories')
                ->whereRaw(
                    'LOWER(TRIM(name)) = ?',
                    [
                        strtolower($categoryName),
                    ]
                )
                ->first();


            /*
            |--------------------------------------------------------------------------
            | JIKA KATEGORI TIDAK DITEMUKAN
            |--------------------------------------------------------------------------
            */

            if (!$category) {

                $invalidCategories[] = $categoryName;

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | TENTUKAN APAKAH LENGTH DIPERLUKAN
            |--------------------------------------------------------------------------
            |
            | Outdoor dan Indoor:
            | - Length tidak digunakan
            | - Length disimpan NULL
            |
            | Kategori lainnya:
            | - Length wajib
            |
            */

            $categoryNameNormalized =
                strtolower(trim($category->name));

            $requiresLength =
                !in_array(
                    $categoryNameNormalized,
                    [
                        'outdoor',
                        'indoor',
                    ],
                    true
                );


            /*
            |--------------------------------------------------------------------------
            | NORMALISASI STATUS
            |--------------------------------------------------------------------------
            */

            $statusLower =
                strtolower($status);

            if ($statusLower === 'active') {

                $status = 'Active';
            } elseif ($statusLower === 'inactive') {

                $status = 'Inactive';
            } else {

                $this->skippedInvalid++;

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDASI LEBAR
            |--------------------------------------------------------------------------
            |
            | Width mendukung angka decimal.
            |
            | Contoh:
            | 21
            | 21.5
            | 21.50
            |
            */

            if (
                !is_numeric($widthValue) ||
                (float) $widthValue <= 0
            ) {

                $this->skippedInvalid++;

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | NORMALISASI WIDTH
            |--------------------------------------------------------------------------
            */

            $width =
                $this->normalizeDecimal($widthValue);


            /*
            |--------------------------------------------------------------------------
            | VALIDASI PANJANG
            |--------------------------------------------------------------------------
            |
            | Outdoor / Indoor:
            | - Tidak membutuhkan panjang
            | - Nilai Excel diabaikan
            |
            | Kategori lainnya:
            | - Wajib diisi
            | - Harus numeric
            | - Harus > 0
            |
            */

            $length = null;


            if ($requiresLength) {

                if ($lengthValue === '') {

                    throw new Exception(
                        'Panjang wajib diisi untuk material kategori "' .
                            $category->name .
                            '". Material: "' .
                            $materialName .
                            '".'
                    );
                }


                if (
                    !is_numeric($lengthValue) ||
                    (float) $lengthValue <= 0
                ) {

                    throw new Exception(
                        'Panjang untuk material "' .
                            $materialName .
                            '" harus berupa angka lebih dari 0.'
                    );
                }


                $length =
                    $this->normalizeDecimal(
                        $lengthValue
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | CEK KONSISTENSI STATUS MATERIAL
            |--------------------------------------------------------------------------
            |
            | Satu material hanya boleh memiliki satu status
            | dalam satu file Excel.
            |
            */

            $materialKey =
                strtolower(
                    trim($materialName)
                );


            if (
                isset($materialStatuses[$materialKey]) &&
                $materialStatuses[$materialKey] !== $status
            ) {

                throw new Exception(
                    'Status material "' .
                        $materialName .
                        '" tidak konsisten. ' .
                        'Semua baris dengan material yang sama ' .
                        'harus menggunakan status yang sama.'
                );
            }


            $materialStatuses[$materialKey] = $status;


            /*
            |--------------------------------------------------------------------------
            | SIMPAN DATA SEMENTARA
            |--------------------------------------------------------------------------
            */

            $materials[] = [

                'category_id' =>
                $category->id,

                'category_name' =>
                $category->name,

                'requires_length' =>
                $requiresLength,

                'material_code' =>
                $materialCode !== ''
                    ? $materialCode
                    : null,

                'material_name' =>
                $materialName,

                'status' =>
                $status,

                'width' =>
                $width,

                'length' =>
                $length,

                'unit' =>
                $unit,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | CEK ADA KATEGORI YANG TIDAK TERDAFTAR?
        |--------------------------------------------------------------------------
        */

        if (!empty($invalidCategories)) {

            $invalidCategories =
                array_unique(
                    $invalidCategories
                );

            throw new Exception(
                'Kategori "' .
                    implode(
                        ', ',
                        $invalidCategories
                    ) .
                    '" tidak terdaftar. ' .
                    'Silakan cek kategori terlebih dahulu.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | JIKA TIDAK ADA DATA VALID
        |--------------------------------------------------------------------------
        */

        if (empty($materials)) {

            throw new Exception(
                'Tidak ada data material yang valid untuk diimport.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SEMUA DATA SUDAH VALID
        | MULAI TRANSACTION
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($materials) {

            foreach ($materials as $material) {

                /*
                |--------------------------------------------------------------------------
                | CARI MATERIAL BERDASARKAN NAMA
                | CASE INSENSITIVE
                |--------------------------------------------------------------------------
                */

                $existingMaterial = DB::table('m_materials')
                    ->whereRaw(
                        'LOWER(TRIM(material_name)) = ?',
                        [
                            strtolower(
                                trim(
                                    $material['material_name']
                                )
                            )
                        ]
                    )
                    ->first();


                /*
                |--------------------------------------------------------------------------
                | JIKA MATERIAL BELUM ADA
                |--------------------------------------------------------------------------
                */

                if (!$existingMaterial) {

                    $now = now();

                    $createdBy =
                        Auth::user()->name ?? 'System';


                    $materialId =
                        DB::table('m_materials')
                        ->insertGetId([
                            'category_id' =>
                            $material['category_id'],

                            'material_code' =>
                            $material['material_code'],

                            'material_name' =>
                            $material['material_name'],

                            'status' =>
                            $material['status'],

                            'created_by' =>
                            $createdBy,

                            'created_at' =>
                            $now,

                            'updated_by' =>
                            $createdBy,

                            'updated_at' =>
                            $now,
                        ]);


                    $this->importedMaterials++;
                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | MATERIAL SUDAH ADA
                    |--------------------------------------------------------------------------
                    |
                    | Jangan insert ulang.
                    |
                    | Kategori, kode, dan status dari Excel
                    | diabaikan.
                    |
                    */

                    $materialId =
                        $existingMaterial->id;
                }


                /*
                |--------------------------------------------------------------------------
                | CEK APAKAH UKURAN SUDAH ADA
                |--------------------------------------------------------------------------
                |
                | Kategori dengan length:
                | material_id + width + length + unit
                |
                | Outdoor / Indoor:
                | material_id + width + unit
                |
                */

                $sizeQuery = DB::table('m_material_sizes')
                    ->where(
                        'material_id',
                        $materialId
                    )
                    ->where(
                        'width',
                        $material['width']
                    )
                    ->whereRaw(
                        'LOWER(TRIM(unit)) = ?',
                        [
                            strtolower(
                                trim(
                                    $material['unit']
                                )
                            )
                        ]
                    );


                /*
                |--------------------------------------------------------------------------
                | FILTER PANJANG
                |--------------------------------------------------------------------------
                */

                if ($material['requires_length']) {

                    $sizeQuery->where(
                        'length',
                        $material['length']
                    );
                } else {

                    $sizeQuery->whereNull(
                        'length'
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | JIKA UKURAN SUDAH ADA → SKIP
                |--------------------------------------------------------------------------
                */

                $existingSize =
                    $sizeQuery->exists();


                if ($existingSize) {

                    $this->skippedDuplicate++;

                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | INSERT UKURAN BARU
                |--------------------------------------------------------------------------
                */

                $now = now();

                $createdBy =
                    Auth::user()->name ?? 'System';


                DB::table('m_material_sizes')
                    ->insert([
                        'material_id' =>
                        $materialId,

                        'width' =>
                        $material['width'],

                        'length' =>
                        $material['length'],

                        'unit' =>
                        $material['unit'],

                        'created_by' =>
                        $createdBy,

                        'created_at' =>
                        $now,

                        'updated_by' =>
                        $createdBy,

                        'updated_at' =>
                        $now,
                    ]);


                $this->importedSizes++;
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | NORMALIZE DECIMAL
    |--------------------------------------------------------------------------
    |
    | Menyamakan format angka decimal untuk:
    | - width
    | - length
    |
    | Contoh:
    | 21     → 21
    | 21.5   → 21.5
    | 21.50  → 21.5
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

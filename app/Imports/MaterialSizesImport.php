<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MaterialSizesImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;
    public int $skippedInvalid = 0;

    public array $invalidMaterials = [];
    public array $invalidUnits = [];
    public array $duplicateSizes = [];


    public function collection(Collection $collection): void
    {
        /*
        |--------------------------------------------------------------------------
        | DATA SEMENTARA
        |--------------------------------------------------------------------------
        */

        $rows = [];


        /*
        |--------------------------------------------------------------------------
        | PENAMPUNG DATA EXCEL
        |--------------------------------------------------------------------------
        |
        | Digunakan untuk mendeteksi duplikasi di dalam file Excel.
        |
        */

        $excelKeys = [];


        /*
        |--------------------------------------------------------------------------
        | SATUAN YANG DIPERBOLEHKAN
        |--------------------------------------------------------------------------
        */

        $allowedUnits = [
            'cm',
            'mm',
            'm',
        ];


        /*
        |--------------------------------------------------------------------------
        | CEK SEMUA DATA EXCEL TERLEBIH DAHULU
        |--------------------------------------------------------------------------
        */

        foreach ($collection as $row) {

            /*
            |--------------------------------------------------------------------------
            | AMBIL DATA EXCEL
            |--------------------------------------------------------------------------
            */

            $materialName = trim((string) (
                $row['nama_material'] ?? ''
            ));

            $width = trim((string) (
                $row['lebar'] ?? ''
            ));

            $unit = trim((string) (
                $row['satuan'] ?? ''
            ));


            /*
            |--------------------------------------------------------------------------
            | LEWATI BARIS BENAR-BENAR KOSONG
            |--------------------------------------------------------------------------
            */

            if (
                $materialName === '' &&
                $width === '' &&
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
                $materialName === '' ||
                $width === '' ||
                $unit === ''
            ) {
                $this->skippedInvalid++;

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | NORMALISASI SATUAN
            |--------------------------------------------------------------------------
            |
            | CM -> cm
            | Cm -> cm
            | MM -> mm
            | M  -> m
            |
            */

            $unit = strtolower($unit);


            /*
            |--------------------------------------------------------------------------
            | VALIDASI SATUAN
            |--------------------------------------------------------------------------
            |
            | Hanya:
            | cm
            | mm
            | m
            |
            */

            if (!in_array($unit, $allowedUnits, true)) {

                $this->invalidUnits[] = $unit;

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | CEK MATERIAL
            |
            | Case-insensitive dan mengabaikan spasi
            | di awal/akhir.
            |--------------------------------------------------------------------------
            */

            $material = DB::table('m_materials')
                ->whereRaw(
                    'LOWER(TRIM(material_name)) = ?',
                    [strtolower($materialName)]
                )
                ->first();


            /*
            |--------------------------------------------------------------------------
            | MATERIAL TIDAK TERDAFTAR
            |--------------------------------------------------------------------------
            */

            if (!$material) {

                $this->invalidMaterials[] = $materialName;

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDASI LEBAR
            |--------------------------------------------------------------------------
            */

            if (
                !is_numeric($width) ||
                (float) $width < 0
            ) {

                $this->skippedInvalid++;

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | NORMALISASI WIDTH
            |--------------------------------------------------------------------------
            |
            | 220
            | 220.0
            | 220.00
            |
            | dianggap sebagai ukuran yang sama.
            |
            */

            $normalizedWidth = number_format(
                (float) $width,
                2,
                '.',
                ''
            );


            /*
            |--------------------------------------------------------------------------
            | UNIQUE KEY
            |--------------------------------------------------------------------------
            |
            | Material + Width + Unit
            |
            */

            $key =
                $material->id .
                '|' .
                $normalizedWidth .
                '|' .
                $unit;


            /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKASI DI DALAM EXCEL
            |--------------------------------------------------------------------------
            */

            if (isset($excelKeys[$key])) {

                $this->duplicateSizes[] =
                    "{$materialName} - {$width} {$unit}";

                continue;
            }


            $excelKeys[$key] = true;


            /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKASI DI DATABASE
            |--------------------------------------------------------------------------
            */

            $exists = DB::table('m_material_sizes')
                ->where('material_id', $material->id)
                ->where('unit', $unit)
                ->whereRaw(
                    'ROUND(width, 2) = ?',
                    [(float) $width]
                )
                ->exists();


            if ($exists) {

                $this->duplicateSizes[] =
                    "{$materialName} - {$width} {$unit}";

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | SIMPAN KE DATA SEMENTARA
            |--------------------------------------------------------------------------
            */

            $rows[] = [
                'material_id' => $material->id,
                'width' => $width,
                'unit' => $unit,
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | JIKA ADA MATERIAL TIDAK TERDAFTAR
        |
        | SELURUH IMPORT DITOLAK.
        |--------------------------------------------------------------------------
        */

        if (count($this->invalidMaterials) > 0) {

            $this->imported = 0;

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | JIKA ADA SATUAN TIDAK VALID
        |
        | SELURUH IMPORT DITOLAK.
        |--------------------------------------------------------------------------
        */

        if (count($this->invalidUnits) > 0) {

            $this->imported = 0;

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | JIKA ADA DUPLIKASI
        |
        | SELURUH IMPORT DITOLAK.
        |--------------------------------------------------------------------------
        */

        if (count($this->duplicateSizes) > 0) {

            $this->imported = 0;

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | JIKA TIDAK ADA DATA VALID
        |--------------------------------------------------------------------------
        */

        if (count($rows) === 0) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | INSERT SEMUA DATA
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($rows) {

            foreach ($rows as $row) {

                DB::table('m_material_sizes')->insert([

                    'material_id' => $row['material_id'],

                    'width' => $row['width'],

                    'unit' => $row['unit'],

                    'created_by' =>
                    Auth::user()->name ?? 'System',

                    'created_at' =>
                    Carbon::now(),

                ]);

                $this->imported++;
            }
        });
    }
}

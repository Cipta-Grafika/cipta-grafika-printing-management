<?php

namespace App\Imports;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LocationsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection): void
    {
        /*
        |--------------------------------------------------------------------------
        | ARRAY UNTUK MENYIMPAN DATA
        |--------------------------------------------------------------------------
        */

        $locations = [];


        /*
        |--------------------------------------------------------------------------
        | ARRAY CEK DUPLIKASI DALAM EXCEL
        |--------------------------------------------------------------------------
        */

        $excelLocations = [];


        /*
        |--------------------------------------------------------------------------
        | VALIDASI SEMUA DATA TERLEBIH DAHULU
        |--------------------------------------------------------------------------
        */

        foreach ($collection as $index => $row) {

            /*
            |--------------------------------------------------------------------------
            | NOMOR BARIS EXCEL
            |--------------------------------------------------------------------------
            */

            // +2 karena baris pertama adalah heading Excel
            $rowNumber = $index + 2;


            /*
            |--------------------------------------------------------------------------
            | AMBIL NAMA LOKASI
            |--------------------------------------------------------------------------
            */

            $name = trim($row['nama_lokasi'] ?? '');


            /*
            |--------------------------------------------------------------------------
            | AMBIL STATUS
            |--------------------------------------------------------------------------
            */

            $status = trim($row['status'] ?? '');


            /*
            |--------------------------------------------------------------------------
            | VALIDASI NAMA LOKASI
            |--------------------------------------------------------------------------
            */

            if ($name === '') {

                throw new Exception(
                    "Nama lokasi pada baris {$rowNumber} wajib diisi."
                );
            }


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
                    "Status pada lokasi '{$name}' di baris {$rowNumber} tidak valid. Gunakan Active atau Inactive."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | NORMALISASI NAMA UNTUK CEK DUPLIKASI
            |--------------------------------------------------------------------------
            */

            $normalizedName = strtolower($name);


            /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKASI DI FILE EXCEL
            |--------------------------------------------------------------------------
            */

            if (in_array($normalizedName, $excelLocations)) {

                throw new Exception(
                    "Nama lokasi '{$name}' duplikat di file Excel pada baris {$rowNumber}."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | SIMPAN KE ARRAY CEK EXCEL
            |--------------------------------------------------------------------------
            */

            $excelLocations[] = $normalizedName;


            /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKASI DI DATABASE
            |--------------------------------------------------------------------------
            */

            $exists = DB::table('m_locations')
                ->whereRaw(
                    'LOWER(name) = ?',
                    [$normalizedName]
                )
                ->exists();


            /*
            |--------------------------------------------------------------------------
            | JIKA SUDAH ADA DI DATABASE
            |--------------------------------------------------------------------------
            */

            if ($exists) {

                throw new Exception(
                    "Nama lokasi '{$name}' sudah terdaftar di sistem."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | SIMPAN DATA SEMENTARA
            |--------------------------------------------------------------------------
            */

            $locations[] = [

                'name' => $name,

                'status' => $status,

                'created_by' => Auth::user()->name ?? 'System',

                'created_at' => now(),

            ];
        }


        /*
        |--------------------------------------------------------------------------
        | INSERT SETELAH SEMUA DATA LOLOS VALIDASI
        |--------------------------------------------------------------------------
        */

        if (!empty($locations)) {

            DB::table('m_locations')->insert($locations);
        }
    }
}

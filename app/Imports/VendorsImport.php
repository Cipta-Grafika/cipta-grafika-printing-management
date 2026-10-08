<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class VendorsImport implements ToCollection, WithHeadingRow
{
    /*
    |--------------------------------------------------------------------------
    | COUNTER
    |--------------------------------------------------------------------------
    */

    public int $imported = 0;

    public int $skippedDuplicate = 0;

    public int $skippedInvalid = 0;


    /*
    |--------------------------------------------------------------------------
    | IMPORT DATA VENDOR
    |--------------------------------------------------------------------------
    */

    public function collection(Collection $collection): void
    {
        foreach ($collection as $row) {

            /*
            |--------------------------------------------------------------------------
            | AMBIL DATA DARI EXCEL
            |--------------------------------------------------------------------------
            */

            $name = trim($row['nama_vendor'] ?? '');

            $contact = trim($row['kontak'] ?? '');

            $status = trim($row['status'] ?? '');


            /*
            |--------------------------------------------------------------------------
            | LEWATI BARIS KOSONG
            |--------------------------------------------------------------------------
            */

            if ($name === '') {

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | NORMALISASI STATUS
            |--------------------------------------------------------------------------
            */

            $status = strtolower(trim($status));


            /*
            |--------------------------------------------------------------------------
            | DEFAULT STATUS
            |--------------------------------------------------------------------------
            */

            if ($status === '') {

                $status = 'active';
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDASI DAN STANDARISASI STATUS
            |--------------------------------------------------------------------------
            */

            $statusMap = [

                'active' => 'Active',

                'inactive' => 'Inactive',

                'in active' => 'Inactive',

            ];


            /*
            |--------------------------------------------------------------------------
            | JIKA STATUS TIDAK VALID → SKIP
            |--------------------------------------------------------------------------
            */

            if (!array_key_exists($status, $statusMap)) {

                $this->skippedInvalid++;

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | UBAH KE FORMAT STANDAR DATABASE
            |--------------------------------------------------------------------------
            */

            $status = $statusMap[$status];


            /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKASI VENDOR
            |--------------------------------------------------------------------------
            */

            $exists = DB::table('m_vendors')
                ->whereRaw(
                    'LOWER(name) = ?',
                    [strtolower($name)]
                )
                ->exists();


            /*
            |--------------------------------------------------------------------------
            | JIKA SUDAH ADA → SKIP
            |--------------------------------------------------------------------------
            */

            if ($exists) {

                $this->skippedDuplicate++;

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | SIMPAN DATA VENDOR BARU
            |--------------------------------------------------------------------------
            */

            DB::table('m_vendors')->insert([

                'name' => $name,

                'contact' => $contact,

                'status' => $status,

                'created_by' => Auth::user()->name ?? 'System',

                'created_at' => now(),

            ]);


            /*
            |--------------------------------------------------------------------------
            | TAMBAH JUMLAH BERHASIL
            |--------------------------------------------------------------------------
            */

            $this->imported++;
        }
    }
}

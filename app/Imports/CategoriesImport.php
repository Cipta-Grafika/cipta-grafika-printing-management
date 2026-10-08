<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoriesImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $collection): void
    {
        foreach ($collection as $row) {

            /*
            |--------------------------------------------------------------------------
            | AMBIL NAMA KATEGORI
            |--------------------------------------------------------------------------
            */

            $name = trim($row['nama_kategori'] ?? '');


            /*
            |--------------------------------------------------------------------------
            | LEWATI DATA KOSONG
            |--------------------------------------------------------------------------
            */

            if ($name === '') {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | CEK KATEGORI SUDAH ADA
            |--------------------------------------------------------------------------
            */

            $exists = DB::table('m_categories')
                ->where('name', $name)
                ->exists();


            /*
            |--------------------------------------------------------------------------
            | JIKA SUDAH ADA → LEWATI
            |--------------------------------------------------------------------------
            */

            if ($exists) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | SIMPAN KATEGORI BARU
            |--------------------------------------------------------------------------
            */

            DB::table('m_categories')->insert([
                'name' => $name,
                'created_by' => Auth::user()->name ?? 'System',
                'created_at' => now(),
            ]);
        }
    }
}

<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EnginesImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;
    public int $skippedDuplicate = 0;
    public int $skippedInvalid = 0;

    public function collection(Collection $collection): void
    {
        foreach ($collection as $row) {

            /*
            |--------------------------------------------------------------------------
            | AMBIL DATA EXCEL
            |--------------------------------------------------------------------------
            */

            $name = trim((string) ($row['nama_mesin'] ?? ''));

            $minimumCharge = trim(
                (string) ($row['minimum_charge'] ?? '')
            );

            $status = trim((string) ($row['status'] ?? ''));


            /*
            |--------------------------------------------------------------------------
            | LEWATI BARIS BENAR-BENAR KOSONG
            |--------------------------------------------------------------------------
            */

            if (
                $name === '' &&
                $minimumCharge === '' &&
                $status === ''
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDASI DATA WAJIB
            |--------------------------------------------------------------------------
            */

            if ($name === '') {
                $this->skippedInvalid++;
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | PARSE MINIMUM CHARGE
            |--------------------------------------------------------------------------
            */

            $parsedMinimumCharge = null;

            if ($minimumCharge !== '') {

                /*
                 * Hilangkan pemisah ribuan.
                 * Contoh:
                 * 1.000     -> 1000
                 * 10.000    -> 10000
                 */

                $minimumCharge = str_replace('.', '', $minimumCharge);

                /*
                 * Jika menggunakan koma sebagai desimal:
                 * 1,5 -> 1.5
                 */

                $minimumCharge = str_replace(',', '.', $minimumCharge);

                if (!is_numeric($minimumCharge)) {
                    $this->skippedInvalid++;
                    continue;
                }

                $parsedMinimumCharge = (float) $minimumCharge;

                if ($parsedMinimumCharge < 0) {
                    $this->skippedInvalid++;
                    continue;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | NORMALISASI STATUS
            |--------------------------------------------------------------------------
            */

            if ($status === '') {
                $status = 'Active';
            }

            $status = strtolower(trim($status));

            if ($status === 'active') {

                $status = 'Active';
            } elseif (
                $status === 'inactive' ||
                $status === 'in active'
            ) {

                $status = 'Inactive';
            } else {

                $this->skippedInvalid++;
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | CEK DUPLIKASI NAMA MESIN
            |--------------------------------------------------------------------------
            */

            $exists = DB::table('m_engines')
                ->whereRaw(
                    'LOWER(TRIM(name)) = ?',
                    [strtolower(trim($name))]
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
            | INSERT DATA BARU
            |--------------------------------------------------------------------------
            */

            DB::table('m_engines')->insert([
                'name' => $name,
                'minimum_charge' => $parsedMinimumCharge,
                'status' => $status,
                'created_by' => Auth::user()->name ?? 'System',
                'created_at' => Carbon::now(),
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

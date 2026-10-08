<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardCT extends Controller
{

    public function index()
    {
        /*
    |--------------------------------------------------------------------------
    | Ambil Data Mesin
    |--------------------------------------------------------------------------
    */

        $data['engines'] = DB::table('m_engines')
            ->orderBy('name', 'asc')
            ->get();

        /*
    |--------------------------------------------------------------------------
    | Tampilkan Halaman Transaksi
    |--------------------------------------------------------------------------
    */

        return view('admin.index')->with($data);
    }
}

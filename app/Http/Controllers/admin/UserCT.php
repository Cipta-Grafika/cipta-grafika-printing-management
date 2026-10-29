<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserCT extends Controller
{
    /**
     * Tampilkan halaman Informasi Akun & Pengguna Admin (Murni Informasi / Read-Only)
     */
    public function index()
    {
        $currentUser = Auth::user();
        $users = DB::table('users')->orderBy('id', 'asc')->get();

        // Data rincian hak akses modul operasional (Read-Only)
        $accessModules = [
            [
                'group'       => 'Estimasi Cetak (Core Engines)',
                'icon'        => 'bi-calculator',
                'badge'       => 'Akses Penuh',
                'badge_class' => 'badge-success',
                'description' => 'Akses seluruh mesin kalkulasi HPP, komparasi rute cerdas Digital vs Offset, dan penerbitan penawaran resmi.',
                'features'    => [
                    'Estimasi Digital Print A3+ (Brosur, Kartu Nama, Flyer)',
                    'Estimasi Cetak Offset (Heidelberg SM52 / Oliver / SM72)',
                    'Estimasi Cetak Kalender Dinding & Meja (Dual Engine & Pricelist)',
                    'Estimasi Cetak Large Format Outdoor & Indoor',
                    'Pratinjau & Cetak PDF Surat Penawaran Resmi',
                ],
            ],
            [
                'group'       => 'Master Data Percetakan',
                'icon'        => 'bi-database-check',
                'badge'       => 'Akses Penuh',
                'badge_class' => 'badge-success',
                'description' => 'Kewenangan melihat dan mengontrol seluruh katalog data dasar operasional percetakan.',
                'features'    => [
                    'Kategori Produk & Ukuran Jadi Standar',
                    'Lokasi Cabang & Workshop Operasional',
                    'Daftar Mesin Cetak (Digital A3+ & Offset Press)',
                    'Katalog Kertas Plano & Bahan Baku Cetak',
                    'Master Komponen & Aksesoris Kalender (Klem, Spiral, Dudukan)',
                    'Katalog Bahan Display & Galeri Sampel Cetak',
                    'Daftar Rekanan Vendor & Supplier Kertas',
                ],
            ],
            [
                'group'       => 'Konfigurasi Biaya & Margin',
                'icon'        => 'bi-sliders',
                'badge'       => 'Akses Penuh',
                'badge_class' => 'badge-success',
                'description' => 'Kewenangan melihat kebijakan margin keuntungan, ongkos produksi, dan aturan harga.',
                'features'    => [
                    'Aturan Margin Markup Tiering Kuantitas Order',
                    'Ongkos Cetak per Rim / per Druk Mesin Offset',
                    'Ongkos Plat CTP & Biaya Setelan Mesin',
                    'Ongkos Produksi Finishing (Jepit Kaleng, Wire-O, Dudukan Boot)',
                    'Ongkos Produksi Laminasi (Glossy, Doff, dsb.)',
                    'Ongkos Jasa Potong Bahan Lembaran',
                    'Kebijakan Kupon Diskon & Potongan Harga',
                ],
            ],
            [
                'group'       => 'Keamanan & Manajemen Sistem',
                'icon'        => 'bi-shield-check',
                'badge'       => 'Super Admin',
                'badge_class' => 'badge-primary',
                'description' => 'Kewenangan tingkat tertinggi atas otorisasi akun pengguna dan keamanan sistem.',
                'features'    => [
                    'Akses Dashboard Admin Cipta Grafika Estimator',
                    'Informasi Profil Akun Administrator & Status Sesi',
                    'Enkripsi Password Standar Industri (Bcrypt 60-Char)',
                    'Pemantauan Status Keaktifan Seluruh Akun Pengguna',
                ],
            ],
        ];

        return view('admin.user.index', compact('currentUser', 'users', 'accessModules'));
    }
}


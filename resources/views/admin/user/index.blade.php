@extends('admin_master')

@section('contents')
    <style>
        .user-account-page {
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        .user-profile-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        @media (min-width: 992px) {
            .user-profile-grid {
                grid-template-columns: 1.15fr 0.85fr;
            }
        }

        .avatar-wrap-lg {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary) 0%, #ca8a04 100%);
            color: #ffffff;
            font-size: 26px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(202, 138, 4, 0.35);
            flex-shrink: 0;
            letter-spacing: 1px;
        }

        .profile-hero-card {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 24px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-sm);
            box-sizing: border-box;
            width: 100%;
        }

        .profile-meta-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--t-base);
            margin: 0 0 4px;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .profile-meta-email {
            font-size: 13.5px;
            color: var(--t-muted);
            margin: 0 0 10px;
            display: flex;
            align-items: center;
            gap: 6px;
            word-break: break-all;
            overflow-wrap: break-word;
        }

        .badge-pill-role {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--primary-soft);
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid var(--primary-ring);
        }

        .badge-pill-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        /* Scoped Custom Cards for Robust Responsiveness */
        .user-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow-sm);
            display: flex;
            flex-direction: column;
            min-width: 0;
            max-width: 100%;
            overflow: hidden;
            box-sizing: border-box;
            width: 100%;
        }

        .user-card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 8px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            background: var(--bg-card);
            min-width: 0;
            max-width: 100%;
            width: 100%;
            box-sizing: border-box;
        }

        .user-card-head-title-wrap {
            flex: 1 1 auto;
            min-width: 0;
            max-width: 100%;
        }

        .user-card-eyebrow {
            display: block;
            font-family: JetBrains Mono, monospace;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--primary);
            line-height: 1.45;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: break-word;
            margin: 0 0 4px;
        }

        .user-card-sub {
            margin: 0;
            font-size: 13px;
            color: var(--t-muted);
            line-height: 1.5;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .user-card-body {
            padding: 20px;
            min-width: 0;
            max-width: 100%;
            box-sizing: border-box;
        }

        .user-card-footer {
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            background: var(--bg-muted);
            font-size: 12px;
            color: var(--t-muted);
            line-height: 1.5;
            word-break: break-word;
            overflow-wrap: break-word;
            box-sizing: border-box;
        }

        .info-spec-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            padding: 16px 20px;
            box-sizing: border-box;
            min-width: 0;
        }

        @media (min-width: 640px) {
            .info-spec-list {
                grid-template-columns: 1fr 1fr;
            }
        }

        .info-spec-item {
            display: flex;
            flex-direction: column;
            gap: 3px;
            padding: 10px 14px;
            background: var(--bg-muted);
            border-radius: 8px;
            border: 1px solid var(--border);
            min-width: 0;
            box-sizing: border-box;
        }

        .info-spec-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--t-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .info-spec-value {
            font-size: 13.5px;
            font-weight: 600;
            color: var(--t-base);
            word-break: break-word;
            overflow-wrap: break-word;
        }

        /* Access Modules Grid */
        .permissions-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            min-width: 0;
            width: 100%;
        }

        @media (min-width: 768px) {
            .permissions-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .perm-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            box-shadow: var(--shadow-sm);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
            min-width: 0;
            box-sizing: border-box;
        }

        .perm-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .perm-card-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 10px;
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
            min-width: 0;
        }

        .perm-title-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1 1 200px;
            min-width: 0;
        }

        .perm-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--primary-soft);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .perm-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--t-base);
            margin: 0;
            line-height: 1.35;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .perm-badge {
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 12px;
            background: rgba(16, 185, 129, 0.12);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.25);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .perm-badge.badge-primary {
            background: var(--primary-soft);
            color: var(--primary);
            border-color: var(--primary-ring);
        }

        .perm-desc {
            font-size: 12.5px;
            color: var(--t-muted);
            margin: 0;
            line-height: 1.5;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .perm-feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .perm-feature-list li {
            font-size: 12.5px;
            color: var(--t-base);
            display: flex;
            align-items: flex-start;
            gap: 8px;
            line-height: 1.4;
            word-break: break-word;
            overflow-wrap: break-word;
        }

        .perm-feature-list li i {
            color: #10b981;
            font-size: 13px;
            margin-top: 1px;
            flex-shrink: 0;
        }

        /* Read Only Badge in Hero */
        .badge-read-only {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            background: var(--bg-muted);
            color: var(--t-muted);
            font-size: 12px;
            font-weight: 600;
            border: 1px solid var(--border);
            white-space: nowrap;
        }

        /* User Directory Table */
        .user-table-wrap {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .user-table thead tr {
            background: var(--bg-muted);
            border-bottom: 2px solid var(--border);
            text-align: left;
        }

        .user-table th {
            padding: 12px 16px;
            font-weight: 700;
            color: var(--t-base);
            white-space: nowrap;
        }

        .user-table td {
            padding: 12px 16px;
            color: var(--t-base);
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            white-space: nowrap;
        }

        .user-avatar-sm {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--primary-soft);
            color: var(--primary);
            font-weight: 700;
            font-size: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-right: 8px;
            vertical-align: middle;
        }

        /* Mobile Viewport Optimizations */
        @media (max-width: 576px) {
            .profile-hero-card {
                flex-direction: column;
                text-align: center;
                padding: 20px 16px;
                gap: 14px;
            }

            .profile-meta-email {
                justify-content: center;
            }

            .profile-meta-badges {
                justify-content: center !important;
            }

            .user-card-head {
                padding: 14px 16px;
            }

            .user-card-body {
                padding: 14px 16px;
            }

            .user-card-footer {
                padding: 12px 16px;
            }

            .info-spec-list {
                padding: 14px 16px;
                gap: 10px;
            }

            .info-spec-item {
                padding: 10px 12px;
            }

            .perm-card {
                padding: 14px;
            }

            .perm-card-head {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .hero-text {
                min-width: 0 !important;
            }

            .hero-title {
                font-size: 22px !important;
                line-height: 1.3 !important;
            }
        }
    </style>

    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content user-account-page">

                {{-- HERO HEADER (READ-ONLY) --}}
                <section class="hero" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; min-width: 0;">
                    <div class="hero-text" style="flex: 1 1 280px; min-width: 0;">
                        <span class="user-card-eyebrow" style="margin-bottom: 6px;">
                            MANAGEMENT &bull; INFORMASI PENGGUNA &amp; HAK AKSES
                        </span>
                        <h1 class="hero-title" style="margin: 6px 0 8px; word-break: break-word;">
                            Informasi Akun Administrator
                        </h1>
                        <p class="hero-sub" style="margin: 0; word-break: break-word;">
                            Rincian profil akun aktif, kewenangan hak akses operasional, spesifikasi sesi sistem, serta daftar pengguna terdaftar.
                        </p>
                    </div>
                    <div>
                        <span class="badge-read-only">
                            <i class="bi bi-info-circle"></i> Mode Informasi (Read-Only)
                        </span>
                    </div>
                </section>

                <div class="user-profile-grid">

                    {{-- =========================================================
                        KOLOM KIRI: KARTU IDENTITAS PROFIL ADMINISTRATOR
                    ========================================================== --}}
                    <div>
                        {{-- PROFILE HEADER CARD --}}
                        <div class="profile-hero-card">
                            <div class="avatar-wrap-lg">
                                {{ strtoupper(substr($currentUser->name ?? 'A', 0, 2)) }}
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <h2 class="profile-meta-title">{{ $currentUser->name ?? 'Administrator' }}</h2>
                                <p class="profile-meta-email">
                                    <i class="bi bi-envelope"></i>
                                    <span>{{ $currentUser->email ?? '-' }}</span>
                                </p>
                                <div class="profile-meta-badges" style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                    <span class="badge-pill-role">
                                        <i class="bi bi-shield-check"></i> Super Administrator
                                    </span>
                                    <span class="badge-pill-status">
                                        <i class="bi bi-check-circle-fill"></i> Status: {{ ucfirst($currentUser->status ?? 'Active') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- DETAILED SPECIFICATIONS CARD --}}
                        <div class="user-card">
                            <div class="user-card-head">
                                <div class="user-card-head-title-wrap">
                                    <span class="user-card-eyebrow">
                                        ATRIBUT DETAIL PENGGUNA
                                    </span>
                                    <p class="user-card-sub">
                                        Informasi data entitas pengguna yang tersimpan di sistem.
                                    </p>
                                </div>
                            </div>
                            <div class="info-spec-list">
                                <div class="info-spec-item">
                                    <span class="info-spec-label">ID Akun (User ID)</span>
                                    <span class="info-spec-value">#{{ $currentUser->id }}</span>
                                </div>
                                <div class="info-spec-item">
                                    <span class="info-spec-label">Peran Sistem (Role)</span>
                                    <span class="info-spec-value">Super Administrator</span>
                                </div>
                                <div class="info-spec-item">
                                    <span class="info-spec-label">Status Akun</span>
                                    <span class="info-spec-value" style="color: #10b981; font-weight: 700;">
                                        <i class="bi bi-dot" style="font-size: 20px; vertical-align: middle;"></i> Aktif (Active)
                                    </span>
                                </div>
                                <div class="info-spec-item">
                                    <span class="info-spec-label">Tipe Otoritas</span>
                                    <span class="info-spec-value">Owner / Full Control</span>
                                </div>
                                <div class="info-spec-item">
                                    <span class="info-spec-label">Terdaftar Pada</span>
                                    <span class="info-spec-value">
                                        {{ $currentUser->created_at ? \Carbon\Carbon::parse($currentUser->created_at)->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                                    </span>
                                </div>
                                <div class="info-spec-item">
                                    <span class="info-spec-label">Pembaruan Terakhir</span>
                                    <span class="info-spec-value">
                                        {{ $currentUser->updated_at ? \Carbon\Carbon::parse($currentUser->updated_at)->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- =========================================================
                        KOLOM KANAN: SPESIFIKASI KEAMANAN & SESI SERVER
                    ========================================================== --}}
                    <div>
                        <div class="user-card">
                            <div class="user-card-head">
                                <div class="user-card-head-title-wrap">
                                    <span class="user-card-eyebrow">
                                        SPESIFIKASI KEAMANAN &amp; SESI
                                    </span>
                                    <p class="user-card-sub">
                                        Parameter teknis keamanan sesi dan proteksi akun server.
                                    </p>
                                </div>
                            </div>
                            <div class="info-spec-list" style="grid-template-columns: 1fr;">
                                <div class="info-spec-item">
                                    <span class="info-spec-label">Guard Autentikasi</span>
                                    <span class="info-spec-value">
                                        <code>web</code> (Stateful Session Guard)
                                    </span>
                                </div>
                                <div class="info-spec-item">
                                    <span class="info-spec-label">Standar Enkripsi Password</span>
                                    <span class="info-spec-value">
                                        <i class="bi bi-lock-fill" style="color: #10b981;"></i> Bcrypt Hash (Panjang 60 Karakter Terproteksi)
                                    </span>
                                </div>
                                <div class="info-spec-item">
                                    <span class="info-spec-label">Fitur "Ingat Saya" (Remember Token)</span>
                                    <span class="info-spec-value">
                                        <i class="bi bi-shield-check" style="color: var(--primary);"></i> Aktif &amp; Terenkripsi
                                    </span>
                                </div>
                                <div class="info-spec-item">
                                    <span class="info-spec-label">Waktu Server Saat Ini</span>
                                    <span class="info-spec-value">
                                        <i class="bi bi-clock"></i> {{ now()->translatedFormat('l, d F Y - H:i:s') }} WIB
                                    </span>
                                </div>
                                <div class="info-spec-item">
                                    <span class="info-spec-label">Zona Waktu Sistem</span>
                                    <span class="info-spec-value">Asia/Jakarta (UTC +07:00)</span>
                                </div>
                                <div class="info-spec-item">
                                    <span class="info-spec-label">Integritas Data</span>
                                    <span class="info-spec-value" style="color: #10b981;">
                                        <i class="bi bi-check2-all"></i> Terverifikasi &amp; Aman
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- =========================================================
                    BAGIAN TENGAH: MATRIKS HAK AKSES SISTEM (PERMISSIONS)
                ========================================================== --}}
                <div style="margin-top: 24px;">
                    <div class="user-card">
                        <div class="user-card-head">
                            <div class="user-card-head-title-wrap">
                                <span class="user-card-eyebrow">
                                    MATRIKS HAK AKSES &amp; KEWENANGAN OPERASIONAL
                                </span>
                                <p class="user-card-sub">
                                    Daftar modul aplikasi percetakan yang memiliki hak akses penuh oleh akun Administrator.
                                </p>
                            </div>
                        </div>

                        <div class="user-card-body">
                            <div class="permissions-grid">
                                @foreach ($accessModules as $mod)
                                    <div class="perm-card">
                                        <div class="perm-card-head">
                                            <div class="perm-title-wrap">
                                                <div class="perm-icon">
                                                    <i class="bi {{ $mod['icon'] }}"></i>
                                                </div>
                                                <h3 class="perm-title">{{ $mod['group'] }}</h3>
                                            </div>
                                            <span class="perm-badge {{ $mod['badge_class'] }}">
                                                {{ $mod['badge'] }}
                                            </span>
                                        </div>
                                        <p class="perm-desc">{{ $mod['description'] }}</p>
                                        <ul class="perm-feature-list">
                                            @foreach ($mod['features'] as $feat)
                                                <li>
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    <span>{{ $feat }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- =========================================================
                    BAGIAN BAWAH: DAFTAR PENGGUNA TERDAFTAR (DIREKTORI USER READ-ONLY)
                ========================================================== --}}
                <div style="margin-top: 24px;">
                    <div class="user-card">
                        <div class="user-card-head">
                            <div class="user-card-head-title-wrap">
                                <span class="user-card-eyebrow">
                                    DIREKTORI PENGGUNA SISTEM (READ-ONLY)
                                </span>
                                <p class="user-card-sub">
                                    Daftar akun pengguna yang terdaftar di dalam database Cipta Grafika Estimator.
                                </p>
                            </div>
                        </div>

                        <div class="user-table-wrap">
                            <table class="user-table">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">No</th>
                                        <th>Nama Pengguna</th>
                                        <th>Alamat Email</th>
                                        <th>Peran / Akses</th>
                                        <th>Status Akun</th>
                                        <th>Tanggal Dibuat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($users as $index => $u)
                                        <tr>
                                            <td style="color: var(--t-muted); font-weight: 600;">{{ $index + 1 }}</td>
                                            <td style="font-weight: 700;">
                                                <span class="user-avatar-sm">
                                                    {{ strtoupper(substr($u->name ?? 'U', 0, 2)) }}
                                                </span>
                                                <span>{{ $u->name }}</span>
                                                @if ($u->id === $currentUser->id)
                                                    <span style="font-size: 11px; color: var(--primary); font-weight: 700; margin-left: 6px;">(Akun Anda)</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span style="color: var(--t-muted);"><i class="bi bi-envelope"></i></span>
                                                <span style="margin-left: 4px;">{{ $u->email }}</span>
                                            </td>
                                            <td>
                                                <span class="badge-pill-role" style="font-size: 11px; padding: 2px 8px;">
                                                    <i class="bi bi-shield-check"></i> Super Administrator
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge-pill-status" style="font-size: 11px; padding: 2px 8px;">
                                                    <i class="bi bi-check-circle-fill"></i> {{ ucfirst($u->status ?? 'Active') }}
                                                </span>
                                            </td>
                                            <td style="color: var(--t-muted);">
                                                {{ $u->created_at ? \Carbon\Carbon::parse($u->created_at)->translatedFormat('d M Y, H:i') : '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" style="text-align: center; padding: 24px; color: var(--t-muted);">
                                                Tidak ada data pengguna.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="user-card-footer">
                            <i class="bi bi-info-circle"></i> <strong>Catatan:</strong> Halaman ini bersifat <em>read-only</em> untuk kebutuhan audit dan pemantauan informasi akun administrator. Tidak ada data yang dapat diubah melalui halaman ini.
                        </div>
                    </div>
                </div>

            </main>
            <div data-shell-footer></div>
        </div>
    </div>
@endsection

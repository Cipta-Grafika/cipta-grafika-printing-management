@extends('admin_master')

@section('contents')
    <style>
        .calendar-tabs {
            display: flex;
            gap: 10px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .calendar-tab-btn {
            background: var(--bg-card);
            border: 1px solid var(--border);
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            color: var(--t-muted);
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .calendar-tab-btn:hover {
            border-color: var(--primary);
            background: var(--bg-hover);
            color: var(--t-base);
        }

        .calendar-tab-btn.active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        .cal-badge-counter {
            font-size: 11px;
            padding: 2px 7px;
            border-radius: 9999px;
            background: var(--bg-muted);
            color: var(--t-base);
            font-weight: 600;
        }

        .calendar-tab-btn.active .cal-badge-counter {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .cal-metric-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        .cal-metric-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        /* ACTION BUTTONS IN DATATABLE (PERSIS MASTER KALENDER) */
        .btn-detail-cal {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            color: var(--t-base);
            transition: opacity 0.2s ease, transform 0.1s ease, color 0.15s ease;
            font-size: 14px;
            cursor: pointer;
            border: none;
            background: transparent;
        }

        .btn-detail-cal:hover {
            opacity: 0.9;
            transform: scale(1.08);
            color: var(--primary);
        }

        .btn-edit-cal {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            color: var(--t-base);
            transition: opacity 0.2s ease, transform 0.1s ease, color 0.15s ease;
            font-size: 14px;
            cursor: pointer;
            border: none;
            background: transparent;
        }

        .btn-edit-cal:hover {
            opacity: 0.9;
            transform: scale(1.08);
            color: #d97706;
        }

        .btn-delete-cal {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            color: var(--t-base);
            transition: opacity 0.2s ease, transform 0.1s ease, color 0.15s ease;
            font-size: 14px;
            cursor: pointer;
            border: none;
            background: transparent;
        }

        .btn-delete-cal:hover {
            opacity: 0.9;
            transform: scale(1.08);
            color: var(--danger, #ef4444);
        }

        .merch-filter-pill,
        .merch-filter-pill-plakat,
        .merch-filter-pill-nota {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--t-muted);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .merch-filter-pill:hover,
        .merch-filter-pill-plakat:hover,
        .merch-filter-pill-nota:hover {
            color: var(--t-base);
            border-color: var(--primary);
        }

        .merch-filter-pill.active,
        .merch-filter-pill-plakat.active,
        .merch-filter-pill-nota.active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
            font-weight: 600;
        }

        /* =========================================================
           MODAL DIALOGS (OVERLAY POPUP DI TENGAH LAYAR)
        ========================================================= */
        .modal-cal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 16px;
        }

        .modal-cal.is-open {
            display: flex !important;
        }

        .modal-cal-content {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            animation: modalFadeIn 0.2s ease-out;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.96);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .modal-cal-head {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-cal-body {
            padding: 20px;
            max-height: 75vh;
            overflow-y: auto;
        }

        .modal-cal-content.modal-lg {
            max-width: 680px;
        }

        .modal-cal-foot {
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: var(--bg-surface-2);
        }

        /* SELECT2 THEME FIXES INSIDE MODAL */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border) !important;
            border-radius: 8px !important;
            height: 40px !important;
            display: flex !important;
            align-items: center !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--t-base) !important;
            line-height: 38px !important;
            padding-left: 12px !important;
            font-size: 13.5px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
            right: 8px !important;
        }

        .select2-dropdown {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border) !important;
            border-radius: 8px !important;
            z-index: 999999 !important;
        }

        .select2-container--default .select2-results__option {
            color: var(--t-base) !important;
            padding: 8px 12px !important;
            font-size: 13px !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--primary) !important;
            color: #ffffff !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: var(--bg-muted) !important;
        }

        .select2-search--dropdown .select2-search__field {
            background-color: var(--bg-body) !important;
            border: 1px solid var(--border) !important;
            color: var(--t-base) !important;
            border-radius: 6px !important;
            padding: 6px 10px !important;
        }

        /* =========================================================
           RESPONSIVE DESIGN (DESKTOP, TABLET, MOBILE)
        ========================================================= */
        .cal-metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-top: 16px;
            margin-bottom: 20px;
        }

        .calendar-tabs {
            display: flex;
            gap: 10px;
            margin-top: 18px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 6px;
            scrollbar-width: thin;
        }

        .calendar-tabs::-webkit-scrollbar {
            height: 4px;
        }

        .calendar-tabs::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 4px;
        }

        .calendar-tab-btn {
            white-space: nowrap;
            flex-shrink: 0;
            padding: 9px 18px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        .table-responsive table {
            width: 100% !important;
        }

        @media (max-width: 1100px) {
            .cal-metrics-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 12px !important;
            }

            .hero {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 16px !important;
            }

            .hero-actions {
                display: flex !important;
                flex-wrap: wrap !important;
                width: 100% !important;
                gap: 10px !important;
            }

            .hero-actions a,
            .hero-actions button {
                flex: 1 1 auto !important;
                justify-content: center !important;
            }
        }

        @media (max-width: 640px) {
            .cal-metrics-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 8px !important;
            }

            .cal-metric-box {
                padding: 12px 14px !important;
                gap: 10px !important;
            }

            .cal-metric-icon {
                width: 36px !important;
                height: 36px !important;
                font-size: 17px !important;
            }

            .calendar-tabs {
                gap: 6px !important;
            }

            .calendar-tab-btn {
                padding: 8px 12px !important;
                font-size: 12px !important;
                gap: 6px !important;
            }

            .modal-cal {
                padding: 10px !important;
            }

            .modal-cal-content {
                width: 96% !important;
                max-width: 96% !important;
                border-radius: 12px !important;
            }

            .modal-cal-body {
                padding: 14px !important;
                max-height: 80vh !important;
            }

            .modal-cal-head,
            .modal-cal-foot {
                padding: 12px 14px !important;
            }
        }

        @media (max-width: 480px) {
            .cal-metrics-grid {
                grid-template-columns: 1fr !important;
                gap: 8px !important;
            }
        }
    </style>

    <div class="shell">

        <div data-shell-sidebar></div>

        <div class="main">

            <div data-shell-topbar></div>

            <main class="content">

                {{-- =========================================================
                     HERO SECTION
                ========================================================= --}}
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">MASTER DATA · MERCHANDISE</span>
                        <h1 class="hero-title" style="margin: 6px 0 8px;">Master Data Merchandise</h1>
                        <p class="hero-sub">
                            Kelola katalog dan daftar harga produk merchandise kantor, promosi, cinderamata, dan buku nota (Lanyard, Papan Nama, Plakat, Buku Nota NCR &amp; HVS).
                        </p>
                    </div>

                    <div class="hero-actions">
                        {{-- <a id="heroCustomerCatalogLink"
                            href="{{ route('user_product_details', ['type' => 'display', 'id' => ($displayLanyard->id ?? 45)]) }}"
                            target="_blank" class="btn btn--ghost" style="display: inline-flex; align-items: center; gap: 8px;">
                            <i class="bi bi-box-arrow-up-right"></i>
                            <span id="labelHeroCatalog">Lihat Lanyard di Katalog Pelanggan</span>
                        </a> --}}

                        <button type="button" class="btn btn--primary" id="btnHeroMainCreate" onclick="openActiveCreateModal()"
                            style="display: inline-flex; align-items: center; gap: 8px;">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            <span id="labelHeroMainCreate">Tambah Tier Lanyard</span>
                        </button>
                    </div>
                </section>

                {{-- =========================================================
                     METRICS SUMMARY CARDS (PERSIS SEPERTI MASTER KALENDER)
                ========================================================= --}}
                <div class="cal-metrics-grid">
                    <div class="cal-metric-box">
                        <div class="cal-metric-icon" style="background: rgba(194, 168, 87, 0.15); color: #c2a857;">
                            <i class="bi bi-award"></i>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--t-muted); font-weight: 600;">TOTAL TIER LANYARD</div>
                            <div id="statTotalLanyard" style="font-size: 22px; font-weight: 800; color: var(--t-base);">
                                {{ $totalLanyardTiers }} Tier</div>
                        </div>
                    </div>

                    <div class="cal-metric-box">
                        <div class="cal-metric-icon" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--t-muted); font-weight: 600;">PAPAN NAMA DADA</div>
                            <div id="statTotalNameTags" style="font-size: 22px; font-weight: 800; color: var(--t-base);">
                                {{ $totalNameTags }} Varian</div>
                        </div>
                    </div>

                    <div class="cal-metric-box">
                        <div class="cal-metric-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="bi bi-trophy"></i>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--t-muted); font-weight: 600;">PLAKAT AKRILIK &amp; KAYU</div>
                            <div style="font-size: 22px; font-weight: 800; color: var(--t-base);">
                                {{ $totalPlakats }} Tipe</div>
                        </div>
                    </div>

                    <div class="cal-metric-box">
                        <div class="cal-metric-icon" style="background: rgba(139, 92, 246, 0.12); color: #8b5cf6;">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--t-muted); font-weight: 600;">BUKU NOTA (NCR &amp; HVS)</div>
                            <div id="statTotalNotas" style="font-size: 22px; font-weight: 800; color: var(--t-base);">
                                {{ $totalNotas }} Varian</div>
                        </div>
                    </div>
                </div>

                {{-- =========================================================
                     TABS NAVIGATION (PERSIS SEPERTI MASTER KALENDER)
                ========================================================= --}}
                <div class="calendar-tabs">
                    <button type="button" class="calendar-tab-btn active" id="tabBtnLanyard" onclick="switchMerchTab('lanyard')">
                        <i class="bi bi-award"></i>
                        <span>1. Tali Lanyard &amp; ID Card</span>
                        <span id="badgeCountLanyard" class="cal-badge-counter">{{ $totalLanyardTiers }}</span>
                    </button>
                    <button type="button" class="calendar-tab-btn" id="tabBtnPapanNama" onclick="switchMerchTab('papannama')">
                        <i class="bi bi-person-badge"></i>
                        <span>2. Papan Nama (Name Tag)</span>
                        <span id="badgeCountPapanNama" class="cal-badge-counter">{{ $totalNameTags }}</span>
                    </button>
                    <button type="button" class="calendar-tab-btn" id="tabBtnPlakat" onclick="switchMerchTab('plakat')">
                        <i class="bi bi-trophy"></i>
                        <span>3. Plakat Akrilik &amp; Kayu</span>
                        <span id="badgeCountPlakat" class="cal-badge-counter">{{ $totalPlakats }}</span>
                    </button>
                    <button type="button" class="calendar-tab-btn" id="tabBtnNota" onclick="switchMerchTab('nota')">
                        <i class="bi bi-journal-bookmark"></i>
                        <span>4. Buku Nota (NCR &amp; HVS)</span>
                        <span id="badgeCountNota" class="cal-badge-counter">{{ $totalNotas }}</span>
                    </button>
                </div>

                {{-- =========================================================
                     TAB CONTENT WRAPPER
                ========================================================= --}}
                <div class="card" style="margin-top: 16px;">

                    {{-- =====================================================
                         TAB 1: LANYARD
                    ===================================================== --}}
                    <div id="tabContentLanyard" class="merch-tab-pane">
                        
                        {{-- Action & Quick Filter Bar --}}
                        <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                                <span style="font-size: 13px; font-weight: 600; color: var(--t-muted); margin-right: 4px;">
                                    <i class="bi bi-funnel"></i> Filter Cepat:
                                </span>
                                <button type="button" class="merch-filter-pill active" data-filter="">Semua ({{ $totalLanyardTiers }})</button>
                                <button type="button" class="merch-filter-pill" data-filter="2.0 cm">Lebar 2.0 cm</button>
                                <button type="button" class="merch-filter-pill" data-filter="2.5 cm">Lebar 2.5 cm</button>
                                <button type="button" class="merch-filter-pill" data-filter="Hanya Tali">Hanya Tali</button>
                                <button type="button" class="merch-filter-pill" data-filter="1 Set">1 Set Komplit</button>
                            </div>

                            <button type="button" class="btn btn--primary" onclick="openCreateLanyardModal()"
                                style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; font-size: 12.5px;">
                                <i class="bi bi-plus-lg"></i>
                                <span>Tambah Tier Baru</span>
                            </button>
                        </div>

                        {{-- Responsive DataTable Lanyard --}}
                        <div class="table-responsive">
                            <table id="merchLanyardTable" class="data-table display responsive nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 28px;"></th>
                                        <th style="width: 45px;" class="text-center">No</th>
                                        <th class="text-center" style="width: 110px;">Lebar Tali</th>
                                        <th class="text-center" style="width: 150px;">Jenis Paket</th>
                                        <th>Rentang Kuantiti (Tier)</th>
                                        <th class="text-center">Min Qty</th>
                                        <th class="text-center">Max Qty</th>
                                        <th style="text-align: right;">Harga Satuan</th>
                                        <th class="text-center" style="width: 105px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allLanyardTiers as $tier)
                                        <tr id="row-tier-{{ $tier->id }}">
                                            <td class="dtr-control"></td>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td class="text-center">
                                                <span class="badge {{ $tier->width_type === '2cm' ? 'primary' : 'success' }}" style="font-weight: 600;">
                                                    {{ $tier->width_type === '2cm' ? '2.0 cm' : '2.5 cm' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if ($tier->package_type === 'tali')
                                                    <span class="badge" style="background: rgba(194, 168, 87, 0.15); color: #c2a857; font-weight: 600;">
                                                        Hanya Tali
                                                    </span>
                                                @else
                                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-weight: 600;">
                                                        1 Set (Holder + PVC)
                                                    </span>
                                                @endif
                                            </td>
                                            <td><strong>{{ $tier->tier_label }}</strong></td>
                                            <td class="text-center">{{ $tier->min_qty }} pcs</td>
                                            <td class="text-center">{{ $tier->max_qty ? $tier->max_qty . ' pcs' : 'Tak hingga (>100)' }}</td>
                                            <td style="text-align: right; font-weight: 700; color: #c2a857;">
                                                Rp {{ number_format($tier->price, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                                    <button type="button" class="btn-detail-cal"
                                                        onclick='openDetailLanyardModal(@json($tier))'
                                                        title="Lihat Detail Tier">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn-edit-cal"
                                                        onclick='openEditLanyardModal(@json($tier))'
                                                        title="Ubah Data Tier">
                                                        <i class="bi bi-pen"></i>
                                                    </button>
                                                    <button type="button" class="btn-delete-cal"
                                                        onclick='confirmDeleteLanyard({{ $tier->id }}, "{{ addslashes($tier->tier_label) }} (Lebar {{ $tier->width_type }} {{ $tier->package_type == 'tali' ? 'Tali Saja' : '1 Set' }})")'
                                                        title="Hapus Tier">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    

                    

                    {{-- =====================================================
                         TAB 2: PAPAN NAMA (NAME TAG DADA) - FULL CRUD
                    ===================================================== --}}
                    <div id="tabContentPapanNama" class="merch-tab-pane" style="display: none;">
                        
                        {{-- Action & Quick Info Bar --}}
                        <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                                <span style="font-size: 13px; font-weight: 600; color: var(--t-muted); margin-right: 4px;">
                                    <i class="bi bi-person-badge"></i> Data Master Papan Nama Dada ({{ $totalNameTags }} Varian Terdaftar)
                                </span>
                            </div>

                            <button type="button" class="btn btn--primary" onclick="openCreateNameTagModal()"
                                style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; font-size: 12.5px;">
                                <i class="bi bi-plus-lg"></i>
                                <span>Tambah Varian Papan Nama</span>
                            </button>
                        </div>

                        {{-- Responsive DataTable Papan Nama --}}
                        <div class="table-responsive">
                            <table id="merchNameTagTable" class="data-table display responsive nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 28px;"></th>
                                        <th style="width: 45px;" class="text-center">No</th>
                                        <th>Nama Varian Papan Nama</th>
                                        <th class="text-center" style="width: 130px;">Tipe Bahan</th>
                                        <th>Pilihan Pengait</th>
                                        <th class="text-center" style="width: 100px;">Min. Order</th>
                                        <th style="text-align: right;">Harga Satuan</th>
                                        <th class="text-center" style="width: 80px;">Status</th>
                                        <th class="text-center" style="width: 105px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allNameTags as $nt)
                                        <tr id="row-nametag-{{ $nt->id }}">
                                            <td class="dtr-control"></td>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $nt->name }}</strong>
                                                @if (!empty($nt->description))
                                                    <div style="font-size: 11.5px; color: var(--t-muted); margin-top: 2px;">
                                                        {{ Str::limit($nt->description, 60) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($nt->type_code === 'biasa')
                                                    <span class="badge" style="background: rgba(100, 116, 139, 0.15); color: #64748b; font-weight: 600;">
                                                        Akrilik Biasa
                                                    </span>
                                                @elseif ($nt->type_code === 'resin')
                                                    <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; font-weight: 600;">
                                                        Resin Cembung
                                                    </span>
                                                @else
                                                    <span class="badge" style="background: rgba(194, 168, 87, 0.2); color: #c2a857; font-weight: 700;">
                                                        Resin Emas
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                                    <span class="badge" style="font-size: 11px; background: var(--bg-surface-2);">🧷 Peniti</span>
                                                    <span class="badge" style="font-size: 11px; background: var(--bg-surface-2);">🧲 Magnet</span>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $nt->min_order_qty }} pcs</td>
                                            <td style="text-align: right; font-weight: 700; color: #c2a857;">
                                                Rp {{ number_format($nt->price, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge success">Aktif</span>
                                            </td>
                                            <td class="text-center">
                                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                                    <button type="button" class="btn-detail-cal"
                                                        onclick='openDetailNameTagModal(@json($nt))'
                                                        title="Lihat Detail Varian">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn-edit-cal"
                                                        onclick='openEditNameTagModal(@json($nt))'
                                                        title="Ubah Data Varian">
                                                        <i class="bi bi-pen"></i>
                                                    </button>
                                                    <button type="button" class="btn-delete-cal"
                                                        onclick='confirmDeleteNameTag({{ $nt->id }}, "{{ addslashes($nt->name) }}")'
                                                        title="Hapus Varian">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    

                    

                    {{-- =====================================================
                         TAB 3: PLAKAT (AKRILIK & KAYU)
                    ===================================================== --}}
                    <div id="tabContentPlakat" class="merch-tab-pane" style="display: none;">
                        
                        {{-- Action & Quick Info Bar --}}
                        <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                                <span style="font-size: 13px; font-weight: 600; color: var(--t-muted); margin-right: 4px;">
                                    <i class="bi bi-funnel"></i> Filter:
                                </span>
                                <button type="button" class="merch-filter-pill-plakat active" data-filter="">Semua ({{ $totalPlakats }})</button>
                                <button type="button" class="merch-filter-pill-plakat" data-filter="Tanpa Box">Akrilik Tanpa Box</button>
                                <button type="button" class="merch-filter-pill-plakat" data-filter="Box Beludru">Akrilik + Box</button>
                                <button type="button" class="merch-filter-pill-plakat" data-filter="Kayu Solid">Plakat Kayu</button>
                            </div>

                            <button type="button" class="btn btn--primary" onclick="openCreatePlakatModal()"
                                style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; font-size: 12.5px;">
                                <i class="bi bi-plus-lg"></i>
                                <span>Tambah Varian Plakat</span>
                            </button>
                        </div>

                        {{-- Responsive DataTable Plakat --}}
                        <div class="table-responsive">
                            <table id="merchPlakatTable" class="data-table display responsive nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 28px;"></th>
                                        <th style="width: 45px;" class="text-center">No</th>
                                        <th>Nama Varian Plakat</th>
                                        <th class="text-center" style="width: 110px;">Bahan</th>
                                        <th class="text-center" style="width: 85px;">Tebal</th>
                                        <th class="text-center" style="width: 130px;">Dudukan</th>
                                        <th class="text-center" style="width: 120px;">Kemasan</th>
                                        <th class="text-center" style="width: 95px;">Ukuran</th>
                                        <th style="text-align: right;">Harga Satuan</th>
                                        <th class="text-center" style="width: 80px;">Status</th>
                                        <th class="text-center" style="width: 105px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allPlakats as $pk)
                                        <tr id="row-plakat-{{ $pk->id }}">
                                            <td class="dtr-control"></td>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $pk->name }}</strong>
                                                @if (!empty($pk->description))
                                                    <div style="font-size: 11.5px; color: var(--t-muted); margin-top: 2px;">
                                                        {{ Str::limit($pk->description, 60) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($pk->material_type === 'kayu')
                                                    <span class="badge" style="background: rgba(180, 83, 9, 0.15); color: #b45309; font-weight: 700;">
                                                        Kayu Solid
                                                    </span>
                                                @else
                                                    <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; font-weight: 600;">
                                                        Akrilik UV
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge" style="background: var(--bg-surface-2); font-weight: 600;">
                                                    {{ strtoupper($pk->thickness) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if ($pk->base_type === 'hitam')
                                                    <span class="badge" style="background: rgba(15, 23, 42, 0.7); color: #ffffff; font-weight: 600;">
                                                        Dudukan Hitam
                                                    </span>
                                                @elseif ($pk->base_type === 'kayu')
                                                    <span class="badge" style="background: rgba(180, 83, 9, 0.15); color: #b45309; font-weight: 600;">
                                                        Dudukan Kayu
                                                    </span>
                                                @else
                                                    <span class="badge" style="background: rgba(100, 116, 139, 0.15); color: #64748b; font-weight: 600;">
                                                        Dudukan Akrilik
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($pk->box_type === 'box')
                                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-weight: 700;">
                                                        <i class="bi bi-box2-fill"></i> Box Beludru
                                                    </span>
                                                @else
                                                    <span class="badge" style="background: rgba(100, 116, 139, 0.1); color: var(--t-muted);">
                                                        Tanpa Box
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">{{ $pk->dimensions }}</td>
                                            <td style="text-align: right; font-weight: 700; color: #c2a857;">
                                                Rp {{ number_format($pk->price, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge success">Aktif</span>
                                            </td>
                                            <td class="text-center">
                                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                                    <button type="button" class="btn-detail-cal"
                                                        onclick='openDetailPlakatModal(@json($pk))'
                                                        title="Lihat Detail Plakat">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn-edit-cal"
                                                        onclick='openEditPlakatModal(@json($pk))'
                                                        title="Ubah Data Plakat">
                                                        <i class="bi bi-pen"></i>
                                                    </button>
                                                    <button type="button" class="btn-delete-cal"
                                                        onclick='confirmDeletePlakat({{ $pk->id }}, "{{ addslashes($pk->name) }}")'
                                                        title="Hapus Plakat">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>




                    

                
                    

                    

                
                    {{-- =====================================================
                         TAB 4: BUKU NOTA (NCR & HVS)
                    ===================================================== --}}
                    <div id="tabContentNota" class="merch-tab-pane" style="display: none;">
                        
                        {{-- Action & Quick Info Bar --}}
                        <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                            <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                                <span style="font-size: 13px; font-weight: 600; color: var(--t-muted); margin-right: 4px;">
                                    <i class="bi bi-funnel"></i> Filter:
                                </span>
                                <button type="button" class="merch-filter-pill-nota active" data-filter="">Semua ({{ $totalNotas }})</button>
                                <button type="button" class="merch-filter-pill-nota" data-filter="NCR Tembus">NCR Tembus ({{ $ncrNotas->count() }})</button>
                                <button type="button" class="merch-filter-pill-nota" data-filter="HVS Putih">HVS Putih ({{ $hvsNotas->count() }})</button>
                                <button type="button" class="merch-filter-pill-nota" data-filter="1 Tinta">1 Tinta</button>
                                <button type="button" class="merch-filter-pill-nota" data-filter="2 Tinta">2 Tinta</button>
                                <button type="button" class="merch-filter-pill-nota" data-filter="3 Tinta">3 Tinta</button>
                            </div>

                            <button type="button" class="btn btn--primary" onclick="openCreateNotaModal()"
                                style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; font-size: 12.5px;">
                                <i class="bi bi-plus-lg"></i>
                                <span>Tambah Varian Nota</span>
                            </button>
                        </div>

                        {{-- Responsive DataTable Nota --}}
                        <div class="table-responsive">
                            <table id="merchNotaTable" class="data-table display responsive nowrap" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 28px;"></th>
                                        <th style="width: 45px;" class="text-center">No</th>
                                        <th>Nama Varian Buku Nota</th>
                                        <th class="text-center" style="width: 110px;">Bahan Kertas</th>
                                        <th class="text-center" style="width: 90px;">Rangkap (Ply)</th>
                                        <th class="text-center" style="width: 110px;">Warna Tinta</th>
                                        <th class="text-center" style="width: 120px;">Isi / Buku</th>
                                        <th style="text-align: right;">Harga Satuan (Per Rim)</th>
                                        <th class="text-center" style="width: 80px;">Status</th>
                                        <th class="text-center" style="width: 105px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($allNotas as $nt)
                                        <tr id="row-nota-{{ $nt->id }}">
                                            <td class="dtr-control"></td>
                                            <td class="text-center">{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>{{ $nt->name }}</strong>
                                                @if (!empty($nt->description))
                                                    <div style="font-size: 11.5px; color: var(--t-muted); margin-top: 2px;">
                                                        {{ Str::limit($nt->description, 60) }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if ($nt->paper_type === 'ncr')
                                                    <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; font-weight: 700;">
                                                        NCR Tembus
                                                    </span>
                                                @else
                                                    <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-weight: 700;">
                                                        HVS Putih
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge" style="background: var(--bg-surface-2); font-weight: 600;">
                                                    {{ $nt->ply_label }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if ($nt->ink_count === 1)
                                                    <span class="badge" style="background: rgba(100, 116, 139, 0.15); color: #64748b; font-weight: 600;">
                                                        1 Tinta
                                                    </span>
                                                @elseif ($nt->ink_count === 2)
                                                    <span class="badge" style="background: rgba(217, 119, 6, 0.15); color: #d97706; font-weight: 600;">
                                                        2 Tinta
                                                    </span>
                                                @else
                                                    <span class="badge" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6; font-weight: 700;">
                                                        3 Tinta
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                {{ $nt->sheets_per_book }} {{ $nt->paper_type === 'ncr' ? 'Set/Buku' : 'Lbr/Buku' }}
                                            </td>
                                            <td style="text-align: right; font-weight: 700; color: #c2a857;">
                                                Rp {{ number_format($nt->price, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge success">Aktif</span>
                                            </td>
                                            <td class="text-center">
                                                <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                                    <button type="button" class="btn-detail-cal"
                                                        onclick='openDetailNotaModal(@json($nt))'
                                                        title="Lihat Detail Nota">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn-edit-cal"
                                                        onclick='openEditNotaModal(@json($nt))'
                                                        title="Ubah Data Nota">
                                                        <i class="bi bi-pen"></i>
                                                    </button>
                                                    <button type="button" class="btn-delete-cal"
                                                        onclick='confirmDeleteNota({{ $nt->id }}, "{{ addslashes($nt->name) }}")'
                                                        title="Hapus Nota">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </main>

            <div data-shell-footer></div>

        </div>
    </div>

    {{-- =========================================================
         1. MODAL TAMBAH TIER LANYARD (FORM STYLE PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalCreateLanyard" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 520px;">
            <div class="modal-cal-head">
                <div>
                    <span class="eyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">TAMBAH DATA · MASTER LANYARD</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);">Tambah Tier Kuantiti Lanyard</h3>
                </div>
                <button type="button" onclick="closeCalModal('modalCreateLanyard')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>

            <form id="formCreateLanyard">
                @csrf
                <div class="modal-cal-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Lebar Tali <span class="req" style="color: var(--danger);">*</span></label>
                            <select id="createLanyardWidth" name="width_type" class="select select2-modal" required>
                                <option value="2cm" selected>Lanyard Lebar 2.0 cm</option>
                                <option value="2.5cm">Lanyard Lebar 2.5 cm</option>
                            </select>
                        </div>

                        <div class="field">
                            <label class="field-label">Jenis Paket <span class="req" style="color: var(--danger);">*</span></label>
                            <select id="createLanyardPackage" name="package_type" class="select select2-modal" required>
                                <option value="tali" selected>Hanya Tali Saja</option>
                                <option value="set">1 Set (Holder + ID Card PVC)</option>
                            </select>
                        </div>
                    </div>

                    

                    

                    <div class="field" style="margin-bottom: 12px;">
                        <label class="field-label">Label Rentang Kuantiti <span class="req" style="color: var(--danger);">*</span></label>
                        <input type="text" id="createLanyardLabel" name="tier_label" class="input" placeholder="Contoh: 1-4 pcs atau >100 pcs" required>
                        <span class="field-help">Nama rentang yang muncul pada tabel pricelist pelanggan.</span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Min. Qty (pcs) <span class="req" style="color: var(--danger);">*</span></label>
                            <input type="number" id="createLanyardMin" name="min_qty" class="input" min="1" placeholder="Misal: 1" required>
                        </div>

                        <div class="field">
                            <label class="field-label">Max. Qty (pcs)</label>
                            <input type="number" id="createLanyardMax" name="max_qty" class="input" min="1" placeholder="Tak Terbatas (>100)">
                            <span class="field-help">Kosongkan jika batas atas tak hingga.</span>
                        </div>
                    </div>

                    

                    

                    <div class="field">
                        <label class="field-label">Harga Satuan (Rp) <span class="req" style="color: var(--danger);">*</span></label>
                        <input type="text" id="createLanyardPrice" name="price" class="input input-rupiah" placeholder="Rp 0" required
                            style="font-size: 15px; font-weight: 700; color: #c2a857;">
                        <span class="field-help">Tarif satuan per pcs untuk rentang kuantiti ini.</span>
                    </div>
                </div>

                <div class="modal-cal-foot">
                    <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalCreateLanyard')">Batal</button>
                    <button type="submit" id="btnSaveCreateLanyard" class="btn btn--primary">
                        <i class="bi bi-check2"></i>
                        <span>Simpan Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- =========================================================
         2. MODAL EDIT TIER LANYARD (FORM STYLE PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalEditLanyard" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 520px;">
            <div class="modal-cal-head">
                <div>
                    <span class="eyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">UBAH DATA · MASTER LANYARD</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);" id="editModalHeading">
                        Edit Tier Lanyard
                    </h3>
                </div>
                <button type="button" onclick="closeCalModal('modalEditLanyard')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>

            <form id="formEditLanyard">
                @csrf
                @method('PUT')
                <input type="hidden" id="editLanyardId">

                <div class="modal-cal-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Lebar Tali <span class="req" style="color: var(--danger);">*</span></label>
                            <select id="editLanyardWidth" name="width_type" class="select select2-modal" required>
                                <option value="2cm">Lanyard Lebar 2.0 cm</option>
                                <option value="2.5cm">Lanyard Lebar 2.5 cm</option>
                            </select>
                        </div>

                        <div class="field">
                            <label class="field-label">Jenis Paket <span class="req" style="color: var(--danger);">*</span></label>
                            <select id="editLanyardPackage" name="package_type" class="select select2-modal" required>
                                <option value="tali">Hanya Tali Saja</option>
                                <option value="set">1 Set (Holder + ID Card PVC)</option>
                            </select>
                        </div>
                    </div>

                    

                    

                    <div class="field" style="margin-bottom: 12px;">
                        <label class="field-label">Label Rentang Kuantiti <span class="req" style="color: var(--danger);">*</span></label>
                        <input type="text" id="editLanyardLabel" name="tier_label" class="input" required>
                        <span class="field-help">Nama rentang yang muncul pada tabel pricelist pelanggan.</span>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Min. Qty (pcs) <span class="req" style="color: var(--danger);">*</span></label>
                            <input type="number" id="editLanyardMin" name="min_qty" class="input" min="1" required>
                        </div>

                        <div class="field">
                            <label class="field-label">Max. Qty (pcs)</label>
                            <input type="number" id="editLanyardMax" name="max_qty" class="input" min="1" placeholder="Tak Terbatas (>100)">
                            <span class="field-help">Kosongkan jika batas atas tak hingga.</span>
                        </div>
                    </div>

                    

                    

                    <div class="field">
                        <label class="field-label">Harga Satuan (Rp) <span class="req" style="color: var(--danger);">*</span></label>
                        <input type="text" id="editLanyardPrice" name="price" class="input input-rupiah" required
                            style="font-size: 15px; font-weight: 700; color: #c2a857;">
                        <span class="field-help">Tarif satuan per pcs untuk rentang kuantiti ini.</span>
                    </div>
                </div>

                <div class="modal-cal-foot">
                    <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalEditLanyard')">Batal</button>
                    <button type="submit" id="btnSaveEditLanyard" class="btn btn--primary">
                        <i class="bi bi-check2"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- =========================================================
         3. MODAL DETAIL TIER LANYARD (STYLE KEY-VALUE PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalDetailLanyard" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 500px;">
            <div class="modal-cal-head">
                <div>
                    <span class="eyebrow" id="lanyardDetailEyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">DETAIL · SPESIFIKASI TIER LANYARD</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);" id="lanyardDetailTitle">
                        Detail Spesifikasi
                    </h3>
                </div>
                <button type="button" onclick="closeCalModal('modalDetailLanyard')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>

            <div class="modal-cal-body">
                <div id="lanyardDetailContainer" style="display: flex; flex-direction: column; gap: 8px;"></div>
            </div>

            <div class="modal-cal-foot">
                <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalDetailLanyard')">Tutup</button>
                <button type="button" class="btn btn--primary" id="btnDetailToEdit">
                    <i class="bi bi-pen"></i>
                    <span>Ubah Data Ini</span>
                </button>
            </div>
        </div>
    </div>

    {{-- =========================================================
         4. MODAL KONFIRMASI HAPUS LANYARD (PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalDeleteLanyard" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 440px;">
            <div class="modal-cal-head" style="border-bottom: 1px solid var(--border);">
                <div>
                    <span class="eyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">HAPUS DATA · LANYARD</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);">Konfirmasi Hapus</h3>
                </div>
                <button type="button" onclick="closeCalModal('modalDeleteLanyard')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>
            <div class="modal-cal-body" style="padding: 22px;">
                <div style="display: flex; gap: 16px; align-items: flex-start;">
                    <div style="width: 48px; height: 48px; min-width: 48px; border-radius: 12px; background: rgba(239, 68, 68, 0.12); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="bi bi-trash3-fill"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 6px; font-size: 15px; font-weight: 700; color: var(--t-base);">Apakah Anda Yakin?</h4>
                        <p style="margin: 0; font-size: 13px; color: var(--t-muted); line-height: 1.5;">
                            Anda akan menghapus tier <strong id="deleteLanyardTargetName" style="color: var(--t-base);"></strong>. Data yang sudah dihapus tidak dapat dipulihkan kembali.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-cal-foot" style="padding: 14px 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; background: var(--bg-muted);">
                <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalDeleteLanyard')">Batal</button>
                <button type="button" class="btn btn--danger" id="btnConfirmDeleteLanyard" onclick="executeDeleteLanyard()"
                    style="padding: 8px 16px; border-radius: 6px; border: none; background: #dc2626; color: #ffffff; cursor: pointer; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="bi bi-trash3-fill"></i>
                    <span>Ya, Hapus</span>
                </button>
            </div>
        </div>
    </div>

    {{-- =========================================================
         5. MODAL TAMBAH PAPAN NAMA (FORM STYLE PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalCreateNameTag" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 520px;">
            <div class="modal-cal-head">
                <div>
                    <span class="eyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">TAMBAH DATA · MASTER PAPAN NAMA</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);">Tambah Varian Papan Nama Dada</h3>
                </div>
                <button type="button" onclick="closeCalModal('modalCreateNameTag')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>

            <form id="formCreateNameTag">
                @csrf
                <div class="modal-cal-body">
                    <div class="field" style="margin-bottom: 12px;">
                        <label class="field-label">Nama Varian Papan Nama <span class="req" style="color: var(--danger);">*</span></label>
                        <input type="text" name="name" class="input" placeholder="Misal: Papan Nama Biasa / Papan Nama Resin" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Tipe / Bahan <span class="req" style="color: var(--danger);">*</span></label>
                            <select name="type_code" class="select select2-modal" required>
                                <option value="biasa" selected>Akrilik Biasa (Standar)</option>
                                <option value="resin">Resin Cembung Bening</option>
                                <option value="resin_emas">Resin Cembung Plat Emas</option>
                            </select>
                        </div>

                        <div class="field">
                            <label class="field-label">Pilihan Pengait <span class="req" style="color: var(--danger);">*</span></label>
                            <input type="text" name="fastener_options" class="input" value="Peniti, Magnet" required>
                        </div>
                    </div>

                    

                    

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Harga Satuan (Rp) <span class="req" style="color: var(--danger);">*</span></label>
                            <input type="text" name="price" class="input input-rupiah" placeholder="Rp 25.000" required
                                style="font-size: 15px; font-weight: 700; color: #c2a857;">
                        </div>

                        <div class="field">
                            <label class="field-label">Min. Order (pcs) <span class="req" style="color: var(--danger);">*</span></label>
                            <input type="number" name="min_order_qty" class="input" min="1" value="1" required>
                        </div>
                    </div>

                    

                    

                    <div class="field">
                        <label class="field-label">Keterangan / Spesifikasi</label>
                        <textarea name="description" class="input" rows="2" placeholder="Keterangan bahan atau spesifikasi papan nama"></textarea>
                    </div>
                </div>

                <div class="modal-cal-foot">
                    <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalCreateNameTag')">Batal</button>
                    <button type="submit" id="btnSaveCreateNameTag" class="btn btn--primary">
                        <i class="bi bi-check2"></i>
                        <span>Simpan Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- =========================================================
         6. MODAL EDIT PAPAN NAMA (FORM STYLE PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalEditNameTag" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 520px;">
            <div class="modal-cal-head">
                <div>
                    <span class="eyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">UBAH DATA · MASTER PAPAN NAMA</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);" id="editNameTagHeading">
                        Edit Varian Papan Nama
                    </h3>
                </div>
                <button type="button" onclick="closeCalModal('modalEditNameTag')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>

            <form id="formEditNameTag">
                @csrf
                @method('PUT')
                <input type="hidden" id="editNameTagId">

                <div class="modal-cal-body">
                    <div class="field" style="margin-bottom: 12px;">
                        <label class="field-label">Nama Varian Papan Nama <span class="req" style="color: var(--danger);">*</span></label>
                        <input type="text" id="editNameTagName" name="name" class="input" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Tipe / Bahan <span class="req" style="color: var(--danger);">*</span></label>
                            <select id="editNameTagType" name="type_code" class="select select2-modal" required>
                                <option value="biasa">Akrilik Biasa (Standar)</option>
                                <option value="resin">Resin Cembung Bening</option>
                                <option value="resin_emas">Resin Cembung Plat Emas</option>
                            </select>
                        </div>

                        <div class="field">
                            <label class="field-label">Pilihan Pengait <span class="req" style="color: var(--danger);">*</span></label>
                            <input type="text" id="editNameTagFasteners" name="fastener_options" class="input" required>
                        </div>
                    </div>

                    

                    

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Harga Satuan (Rp) <span class="req" style="color: var(--danger);">*</span></label>
                            <input type="text" id="editNameTagPrice" name="price" class="input input-rupiah" required
                                style="font-size: 15px; font-weight: 700; color: #c2a857;">
                        </div>

                        <div class="field">
                            <label class="field-label">Min. Order (pcs) <span class="req" style="color: var(--danger);">*</span></label>
                            <input type="number" id="editNameTagMinOrder" name="min_order_qty" class="input" min="1" required>
                        </div>
                    </div>

                    

                    

                    <div class="field">
                        <label class="field-label">Keterangan / Spesifikasi</label>
                        <textarea id="editNameTagDesc" name="description" class="input" rows="2"></textarea>
                    </div>
                </div>

                <div class="modal-cal-foot">
                    <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalEditNameTag')">Batal</button>
                    <button type="submit" id="btnSaveEditNameTag" class="btn btn--primary">
                        <i class="bi bi-check2"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- =========================================================
         7. MODAL DETAIL PAPAN NAMA (STYLE KEY-VALUE PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalDetailNameTag" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 500px;">
            <div class="modal-cal-head">
                <div>
                    <span class="eyebrow" id="nameTagDetailEyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">DETAIL · PAPAN NAMA DADA</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);" id="nameTagDetailTitle">
                        Detail Spesifikasi
                    </h3>
                </div>
                <button type="button" onclick="closeCalModal('modalDetailNameTag')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>

            <div class="modal-cal-body">
                <div id="nameTagDetailContainer" style="display: flex; flex-direction: column; gap: 8px;"></div>
            </div>

            <div class="modal-cal-foot">
                <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalDetailNameTag')">Tutup</button>
                <button type="button" class="btn btn--primary" id="btnDetailNameTagToEdit">
                    <i class="bi bi-pen"></i>
                    <span>Ubah Data Ini</span>
                </button>
            </div>
        </div>
    </div>

    {{-- =========================================================
         8. MODAL KONFIRMASI HAPUS PAPAN NAMA (PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalDeleteNameTag" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 440px;">
            <div class="modal-cal-head" style="border-bottom: 1px solid var(--border);">
                <div>
                    <span class="eyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">HAPUS DATA · PAPAN NAMA</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);">Konfirmasi Hapus</h3>
                </div>
                <button type="button" onclick="closeCalModal('modalDeleteNameTag')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>
            <div class="modal-cal-body" style="padding: 22px;">
                <div style="display: flex; gap: 16px; align-items: flex-start;">
                    <div style="width: 48px; height: 48px; min-width: 48px; border-radius: 12px; background: rgba(239, 68, 68, 0.12); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="bi bi-trash3-fill"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 6px; font-size: 15px; font-weight: 700; color: var(--t-base);">Apakah Anda Yakin?</h4>
                        <p style="margin: 0; font-size: 13px; color: var(--t-muted); line-height: 1.5;">
                            Anda akan menghapus varian <strong id="deleteNameTagTargetName" style="color: var(--t-base);"></strong>. Data yang sudah dihapus tidak dapat dipulihkan kembali.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-cal-foot" style="padding: 14px 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; background: var(--bg-muted);">
                <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalDeleteNameTag')">Batal</button>
                <button type="button" class="btn btn--danger" id="btnConfirmDeleteNameTag" onclick="executeDeleteNameTag()"
                    style="padding: 8px 16px; border-radius: 6px; border: none; background: #dc2626; color: #ffffff; cursor: pointer; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="bi bi-trash3-fill"></i>
                    <span>Ya, Hapus</span>
                </button>
            </div>
        </div>
    </div>

    {{-- =========================================================
         9. MODAL TAMBAH PLAKAT (FORM STYLE PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalCreatePlakat" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 540px;">
            <div class="modal-cal-head">
                <div>
                    <span class="eyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">TAMBAH DATA · MASTER PLAKAT</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);">Tambah Varian Plakat Cinderamata</h3>
                </div>
                <button type="button" onclick="closeCalModal('modalCreatePlakat')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>

            <form id="formCreatePlakat">
                @csrf
                <div class="modal-cal-body">
                    <div class="field" style="margin-bottom: 12px;">
                        <label class="field-label">Nama Varian Plakat <span class="req" style="color: var(--danger);">*</span></label>
                        <input type="text" name="name" class="input" placeholder="Misal: Plakat Akrilik 3mm Dudukan Akrilik + Box" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Jenis Bahan <span class="req" style="color: var(--danger);">*</span></label>
                            <select name="material_type" class="select select2-modal" required>
                                <option value="akrilik" selected>Akrilik Cetak UV</option>
                                <option value="kayu">Kayu Solid Mewah</option>
                            </select>
                        </div>

                        <div class="field">
                            <label class="field-label">Ketebalan <span class="req" style="color: var(--danger);">*</span></label>
                            <select name="thickness" class="select select2-modal" required>
                                <option value="2mm">2 mm</option>
                                <option value="3mm" selected>3 mm</option>
                                <option value="5mm">5 mm</option>
                                <option value="1cm">1 cm (10 mm)</option>
                                <option value="standar">Standar (Khusus Kayu)</option>
                            </select>
                        </div>
                    </div>

                    

                    

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Jenis Dudukan / Tatakan <span class="req" style="color: var(--danger);">*</span></label>
                            <select name="base_type" class="select select2-modal" required>
                                <option value="akrilik" selected>Dudukan Akrilik</option>
                                <option value="hitam">Dudukan Hitam</option>
                                <option value="kayu">Dudukan Kayu</option>
                            </select>
                        </div>

                        <div class="field">
                            <label class="field-label">Kemasan / Packaging <span class="req" style="color: var(--danger);">*</span></label>
                            <select name="box_type" class="select select2-modal" required>
                                <option value="tanpa_box" selected>Tanpa Box</option>
                                <option value="box">Termasuk Box Beludru</option>
                            </select>
                        </div>
                    </div>

                    

                    

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Ukuran (Dimensi) <span class="req" style="color: var(--danger);">*</span></label>
                            <input type="text" name="dimensions" class="input" value="20x15 cm" required>
                        </div>

                        <div class="field">
                            <label class="field-label">Harga Satuan (Rp) <span class="req" style="color: var(--danger);">*</span></label>
                            <input type="text" name="price" class="input input-rupiah" placeholder="Rp 116.000" required
                                style="font-size: 15px; font-weight: 700; color: #c2a857;">
                        </div>
                    </div>

                    

                    

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Biaya Jasa Desain (Rp)</label>
                            <input type="text" name="design_fee" class="input input-rupiah" placeholder="Rp 0" value="Rp 0">
                        </div>

                        <div class="field">
                            <label class="field-label">Status Produk <span class="req" style="color: var(--danger);">*</span></label>
                            <select name="status" class="select select2-modal">
                                <option value="active" selected>Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    

                    

                    <div class="field">
                        <label class="field-label">Keterangan / Spesifikasi</label>
                        <textarea name="description" class="input" rows="2" placeholder="Keterangan tambahan untuk plakat"></textarea>
                    </div>
                </div>

                <div class="modal-cal-foot">
                    <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalCreatePlakat')">Batal</button>
                    <button type="submit" id="btnSaveCreatePlakat" class="btn btn--primary">
                        <i class="bi bi-check2"></i>
                        <span>Simpan Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- =========================================================
         10. MODAL EDIT PLAKAT (FORM STYLE PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalEditPlakat" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 540px;">
            <div class="modal-cal-head">
                <div>
                    <span class="eyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">UBAH DATA · MASTER PLAKAT</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);" id="editPlakatHeading">
                        Edit Varian Plakat
                    </h3>
                </div>
                <button type="button" onclick="closeCalModal('modalEditPlakat')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>

            <form id="formEditPlakat">
                @csrf
                @method('PUT')
                <input type="hidden" id="editPlakatId">

                <div class="modal-cal-body">
                    <div class="field" style="margin-bottom: 12px;">
                        <label class="field-label">Nama Varian Plakat <span class="req" style="color: var(--danger);">*</span></label>
                        <input type="text" id="editPlakatName" name="name" class="input" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Jenis Bahan <span class="req" style="color: var(--danger);">*</span></label>
                            <select id="editPlakatMaterial" name="material_type" class="select select2-modal" required>
                                <option value="akrilik">Akrilik Cetak UV</option>
                                <option value="kayu">Kayu Solid Mewah</option>
                            </select>
                        </div>

                        <div class="field">
                            <label class="field-label">Ketebalan <span class="req" style="color: var(--danger);">*</span></label>
                            <select id="editPlakatThickness" name="thickness" class="select select2-modal" required>
                                <option value="2mm">2 mm</option>
                                <option value="3mm">3 mm</option>
                                <option value="5mm">5 mm</option>
                                <option value="1cm">1 cm (10 mm)</option>
                                <option value="standar">Standar (Khusus Kayu)</option>
                            </select>
                        </div>
                    </div>

                    

                    

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Jenis Dudukan / Tatakan <span class="req" style="color: var(--danger);">*</span></label>
                            <select id="editPlakatBase" name="base_type" class="select select2-modal" required>
                                <option value="akrilik">Dudukan Akrilik</option>
                                <option value="hitam">Dudukan Hitam</option>
                                <option value="kayu">Dudukan Kayu</option>
                            </select>
                        </div>

                        <div class="field">
                            <label class="field-label">Kemasan / Packaging <span class="req" style="color: var(--danger);">*</span></label>
                            <select id="editPlakatBox" name="box_type" class="select select2-modal" required>
                                <option value="tanpa_box">Tanpa Box</option>
                                <option value="box">Termasuk Box Beludru</option>
                            </select>
                        </div>
                    </div>

                    

                    

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Ukuran (Dimensi) <span class="req" style="color: var(--danger);">*</span></label>
                            <input type="text" id="editPlakatDimensions" name="dimensions" class="input" required>
                        </div>

                        <div class="field">
                            <label class="field-label">Harga Satuan (Rp) <span class="req" style="color: var(--danger);">*</span></label>
                            <input type="text" id="editPlakatPrice" name="price" class="input input-rupiah" required
                                style="font-size: 15px; font-weight: 700; color: #c2a857;">
                        </div>
                    </div>

                    

                    

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Biaya Jasa Desain (Rp)</label>
                            <input type="text" id="editPlakatDesignFee" name="design_fee" class="input input-rupiah">
                        </div>

                        <div class="field">
                            <label class="field-label">Status Produk <span class="req" style="color: var(--danger);">*</span></label>
                            <select id="editPlakatStatus" name="status" class="select select2-modal">
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    

                    

                    <div class="field">
                        <label class="field-label">Keterangan / Spesifikasi</label>
                        <textarea id="editPlakatDesc" name="description" class="input" rows="2"></textarea>
                    </div>
                </div>

                <div class="modal-cal-foot">
                    <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalEditPlakat')">Batal</button>
                    <button type="submit" id="btnSaveEditPlakat" class="btn btn--primary">
                        <i class="bi bi-check2"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- =========================================================
         11. MODAL DETAIL PLAKAT (VIEW DETAIL PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalDetailPlakat" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 520px;">
            <div class="modal-cal-head">
                <div>
                    <span class="eyebrow" id="plakatDetailEyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">DETAIL · SPESIFIKASI PLAKAT</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);" id="plakatDetailTitle">
                        Detail Plakat Cinderamata
                    </h3>
                </div>
                <button type="button" onclick="closeCalModal('modalDetailPlakat')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>

            <div class="modal-cal-body">
                <div id="plakatDetailContainer" style="display: flex; flex-direction: column; gap: 8px;">
                    {{-- Diisi secara dinamis oleh JavaScript --}}
                </div>
            </div>

            <div class="modal-cal-foot">
                <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalDetailPlakat')">Tutup</button>
                <button type="button" class="btn btn--primary" id="btnEditPlakatFromDetail"
                    style="display: inline-flex; align-items: center; gap: 6px;">
                    <i class="bi bi-pen"></i>
                    <span>Ubah Plakat Ini</span>
                </button>
            </div>
        </div>
    </div>

    {{-- =========================================================
         12. MODAL KONFIRMASI HAPUS PLAKAT (PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalDeletePlakat" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 440px;">
            <div class="modal-cal-head" style="border-bottom: 1px solid var(--border);">
                <div>
                    <span class="eyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">HAPUS DATA · MASTER PLAKAT</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);">Konfirmasi Hapus</h3>
                </div>
                <button type="button" onclick="closeCalModal('modalDeletePlakat')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>
            <div class="modal-cal-body" style="padding: 22px;">
                <div style="display: flex; gap: 16px; align-items: flex-start;">
                    <div style="width: 48px; height: 48px; min-width: 48px; border-radius: 12px; background: rgba(239, 68, 68, 0.12); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="bi bi-trash3-fill"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 6px; font-size: 15px; font-weight: 700; color: var(--t-base);">Apakah Anda Yakin?</h4>
                        <p style="margin: 0; font-size: 13px; color: var(--t-muted); line-height: 1.5;">
                            Anda akan menghapus data plakat <strong id="deletePlakatTargetName" style="color: var(--t-base);"></strong>. Data yang sudah dihapus tidak dapat dipulihkan kembali.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-cal-foot" style="padding: 14px 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; background: var(--bg-muted);">
                <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalDeletePlakat')">Batal</button>
                <button type="button" class="btn btn--danger" id="btnConfirmDeletePlakat" onclick="executeDeletePlakat()"
                    style="padding: 8px 16px; border-radius: 6px; border: none; background: #dc2626; color: #ffffff; cursor: pointer; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="bi bi-trash3-fill"></i>
                    <span>Ya, Hapus</span>
                </button>
            </div>
        </div>
    </div>

    {{-- =========================================================
         DATATABLES & JAVASCRIPT CRUD (PERSIS SEPERTI MASTER KALENDER)
    ========================================================= --}}
    <div id="modalCreateNota" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 540px;">
            <div class="modal-cal-head">
                <div>
                    <span class="eyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">TAMBAH DATA · MASTER BUKU NOTA</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);">Tambah Varian Buku Nota</h3>
                </div>
                <button type="button" onclick="closeCalModal('modalCreateNota')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>

            <form id="formCreateNota">
                @csrf
                <div class="modal-cal-body">
                    <div class="field" style="margin-bottom: 12px;">
                        <label class="field-label">Nama Varian Nota <span class="req">*</span></label>
                        <input type="text" name="name" class="input" placeholder="Misal: Nota NCR 2 Ply (1 Tinta)" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Tipe Kertas <span class="req">*</span></label>
                            <select name="paper_type" id="createPaperType" class="select select2-modal" required onchange="handlePaperTypeChange('create')">
                                <option value="ncr" selected>NCR (Carbonless Paper)</option>
                                <option value="hvs">HVS (Kertas Putih Single)</option>
                            </select>
                        </div>

                        <div class="field">
                            <label class="field-label">Rangkap / Ply <span class="req">*</span></label>
                            <select name="ply_count" id="createPlyCount" class="select select2-modal" required>
                                <option value="1">1 Ply (Khusus HVS)</option>
                                <option value="2" selected>2 Ply (Rangkap 2)</option>
                                <option value="3">3 Ply (Rangkap 3)</option>
                                <option value="4">4 Ply (Rangkap 4)</option>
                            </select>
                            <input type="hidden" name="ply_label" id="createPlyLabel" value="2 Ply">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Jumlah Warna Tinta <span class="req">*</span></label>
                            <select name="ink_count" id="createInkCount" class="select select2-modal" required>
                                <option value="1" selected>1 Tinta (1 Warna)</option>
                                <option value="2">2 Tinta (2 Warna)</option>
                                <option value="3">3 Tinta (3 Warna)</option>
                            </select>
                            <input type="hidden" name="ink_label" id="createInkLabel" value="1 Tinta">
                        </div>

                        <div class="field">
                            <label class="field-label">Isi Sheet per Buku <span class="req">*</span></label>
                            <input type="number" name="sheets_per_book" id="createSheetsPerBook" class="input" value="50" min="1" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Harga per Paket Rim (Rp) <span class="req">*</span></label>
                            <input type="text" name="price" class="input input-rupiah" placeholder="Rp 329.000" required
                                style="font-size: 15px; font-weight: 700; color: #c2a857;">
                        </div>

                        <div class="field">
                            <label class="field-label">Status Produk <span class="req">*</span></label>
                            <select name="status" class="select select2-modal">
                                <option value="active" selected>Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label class="field-label">Keterangan / Spesifikasi Nota</label>
                        <textarea name="description" class="input" rows="2" placeholder="Keterangan spesifikasi nota cetak"></textarea>
                    </div>
                </div>

                <div class="modal-cal-foot">
                    <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalCreateNota')">Batal</button>
                    <button type="submit" id="btnSaveCreateNota" class="btn btn--primary">
                        <i class="bi bi-check2"></i>
                        <span>Simpan Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- =========================================================
         2. MODAL EDIT BUKU NOTA (FORM STYLE PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalEditNota" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 540px;">
            <div class="modal-cal-head">
                <div>
                    <span class="eyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">UBAH DATA · MASTER BUKU NOTA</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);" id="editNotaHeading">
                        Edit Varian Buku Nota
                    </h3>
                </div>
                <button type="button" onclick="closeCalModal('modalEditNota')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>

            <form id="formEditNota">
                @csrf
                @method('PUT')
                <input type="hidden" id="editNotaId">

                <div class="modal-cal-body">
                    <div class="field" style="margin-bottom: 12px;">
                        <label class="field-label">Nama Varian Nota <span class="req">*</span></label>
                        <input type="text" id="editNotaName" name="name" class="input" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Tipe Kertas <span class="req">*</span></label>
                            <select id="editPaperType" name="paper_type" class="select select2-modal" required onchange="handlePaperTypeChange('edit')">
                                <option value="ncr">NCR (Carbonless Paper)</option>
                                <option value="hvs">HVS (Kertas Putih Single)</option>
                            </select>
                        </div>

                        <div class="field">
                            <label class="field-label">Rangkap / Ply <span class="req">*</span></label>
                            <select id="editPlyCount" name="ply_count" class="select select2-modal" required>
                                <option value="1">1 Ply (Khusus HVS)</option>
                                <option value="2">2 Ply (Rangkap 2)</option>
                                <option value="3">3 Ply (Rangkap 3)</option>
                                <option value="4">4 Ply (Rangkap 4)</option>
                            </select>
                            <input type="hidden" id="editPlyLabel" name="ply_label" value="2 Ply">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Jumlah Warna Tinta <span class="req">*</span></label>
                            <select id="editInkCount" name="ink_count" class="select select2-modal" required>
                                <option value="1">1 Tinta (1 Warna)</option>
                                <option value="2">2 Tinta (2 Warna)</option>
                                <option value="3">3 Tinta (3 Warna)</option>
                            </select>
                            <input type="hidden" id="editInkLabel" name="ink_label" value="1 Tinta">
                        </div>

                        <div class="field">
                            <label class="field-label">Isi Sheet per Buku <span class="req">*</span></label>
                            <input type="number" id="editSheetsPerBook" name="sheets_per_book" class="input" min="1" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Harga per Paket Rim (Rp) <span class="req">*</span></label>
                            <input type="text" id="editNotaPrice" name="price" class="input input-rupiah" required
                                style="font-size: 15px; font-weight: 700; color: #c2a857;">
                        </div>

                        <div class="field">
                            <label class="field-label">Status Produk <span class="req">*</span></label>
                            <select id="editNotaStatus" name="status" class="select select2-modal">
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label class="field-label">Keterangan / Spesifikasi Nota</label>
                        <textarea id="editNotaDesc" name="description" class="input" rows="2"></textarea>
                    </div>
                </div>

                <div class="modal-cal-foot">
                    <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalEditNota')">Batal</button>
                    <button type="submit" id="btnSaveEditNota" class="btn btn--primary">
                        <i class="bi bi-check2"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- =========================================================
         3. MODAL DETAIL BUKU NOTA (VIEW DETAIL PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalDetailNota" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 520px;">
            <div class="modal-cal-head">
                <div>
                    <span class="eyebrow" id="notaDetailEyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">DETAIL · SPESIFIKASI BUKU NOTA</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);" id="notaDetailTitle">
                        Detail Varian Buku Nota
                    </h3>
                </div>
                <button type="button" onclick="closeCalModal('modalDetailNota')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>

            <div class="modal-cal-body">
                <div id="notaDetailContainer" style="display: flex; flex-direction: column; gap: 8px;">
                    {{-- Diisi secara dinamis oleh JavaScript --}}
                </div>
            </div>

            <div class="modal-cal-foot">
                <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalDetailNota')">Tutup</button>
                <button type="button" class="btn btn--primary" id="btnEditNotaFromDetail"
                    style="display: inline-flex; align-items: center; gap: 6px;">
                    <i class="bi bi-pen"></i>
                    <span>Ubah Varian Ini</span>
                </button>
            </div>
        </div>
    </div>

    {{-- =========================================================
         4. MODAL KONFIRMASI HAPUS BUKU NOTA (PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalDeleteNota" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 440px;">
            <div class="modal-cal-head" style="border-bottom: 1px solid var(--border);">
                <div>
                    <span class="eyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">HAPUS DATA · MASTER BUKU NOTA</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);">Konfirmasi Hapus</h3>
                </div>
                <button type="button" onclick="closeCalModal('modalDeleteNota')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>
            <div class="modal-cal-body" style="padding: 22px;">
                <div style="display: flex; gap: 16px; align-items: flex-start;">
                    <div style="width: 48px; height: 48px; min-width: 48px; border-radius: 12px; background: rgba(239, 68, 68, 0.12); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="bi bi-trash3-fill"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 6px; font-size: 15px; font-weight: 700; color: var(--t-base);">Apakah Anda Yakin?</h4>
                        <p style="margin: 0; font-size: 13px; color: var(--t-muted); line-height: 1.5;">
                            Anda akan menghapus varian nota <strong id="deleteNotaTargetName" style="color: var(--t-base);"></strong>. Data yang sudah dihapus tidak dapat dipulihkan kembali.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-cal-foot" style="padding: 14px 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; background: var(--bg-muted);">
                <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalDeleteNota')">Batal</button>
                <button type="button" class="btn btn--danger" id="btnConfirmDeleteNota" onclick="executeDeleteNota()"
                    style="padding: 8px 16px; border-radius: 6px; border: none; background: #dc2626; color: #ffffff; cursor: pointer; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="bi bi-trash3-fill"></i>
                    <span>Ya, Hapus</span>
                </button>
            </div>
        </div>
    </div>

    {{-- =========================================================
         DATATABLES & JAVASCRIPT CRUD (PERSIS SEPERTI MASTER KALENDER)
    ========================================================= --}}
    

    <script>
        let currentMerchTab = 'lanyard';
        let currentActiveTier = null;
        let currentActiveNameTag = null;
        let currentActivePlakat = null;
        let lanyardItemToDelete = { id: null, name: '' };
        let nameTagItemToDelete = { id: null, name: '' };
        let plakatItemToDelete = { id: null, name: '' };

        // Helper Format Rupiah
        function formatRupiahDisplay(val) {
            if (val === null || val === undefined || val === '') return '';
            let num = Number(val);
            if (isNaN(num)) return val;
            return 'Rp ' + num.toLocaleString('id-ID');
        }

        // Helper Pembersih Nilai Rupiah dari Input Masking (Persis Kalender)
        function cleanRupiahValue(val) {
            if (val === null || val === undefined || val === '') return 0;
            let s = String(val).replace(/Rp\s*/g, '').trim();
            if (s.includes(',')) {
                s = s.replace(/\./g, '').replace(',', '.');
            } else {
                let dotCount = (s.match(/\./g) || []).length;
                if (dotCount > 1) {
                    s = s.replace(/\./g, '');
                } else if (dotCount === 1) {
                    let parts = s.split('.');
                    if (parts[1].length === 3) {
                        s = s.replace(/\./g, '');
                    }
                }
            }
            return parseFloat(s) || 0;
        }

        // Helper Baris Detail Key-Value & Section Box (Persis Kalender)
        function makeDetailRow(label, value) {
            return `
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 9px 13px; background: var(--bg-surface-2); border: 1px solid var(--border); border-radius: 8px; gap: 10px;">
                    <span style="font-size: 12.5px; color: var(--t-muted); font-weight: 500;">${label}</span>
                    <span style="font-size: 12.5px; color: var(--t-base); font-weight: 600; text-align: right;">${value}</span>
                </div>
            `;
        }

        function makeDetailSectionTitle(title) {
            return `<div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--primary); margin-top: 10px; margin-bottom: 2px;">${title}</div>`;
        }

        function makeDetailInfoBox(icon, title, desc) {
            return `
                <div style="padding: 10px 14px; background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 8px; font-size: 12.5px; color: var(--t-base); line-height: 1.45;">
                    <div style="color: var(--primary); font-weight: 700; display: flex; align-items: center; gap: 6px; margin-bottom: 3px;">
                        <span>${icon}</span> <span>${title}</span>
                    </div>
                    <div style="color: var(--t-muted); font-size: 11.5px;">${desc}</div>
                </div>
            `;
        }

        // Inisialisasi Select2 dalam Modal (Persis Kalender)
        function initCalModalSelect2(modalId) {
            if (typeof $.fn.select2 !== 'undefined') {
                $(`#${modalId} .select2-modal`).each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                    $(this).select2({
                        dropdownParent: $(`#${modalId}`),
                        width: '100%',
                        minimumResultsForSearch: 10
                    });
                });
            }
        }

        // Helper Tutup Modal
        function closeCalModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('is-open');
        }

        // Tombol Tambah Dinamis di Hero Header (Persis Kalender)
        function openActiveCreateModal() {
            if (currentMerchTab === 'lanyard') {
                openCreateLanyardModal();
            } else if (currentMerchTab === 'papannama') {
                openCreateNameTagModal();
            } else if (currentMerchTab === 'plakat') {
                openCreatePlakatModal();
            } else if (currentMerchTab === 'nota') {
                openCreateNotaModal();
            }
        }

        // Switch Tab Utama (Lanyard, Papan Nama, Plakat)
        function switchMerchTab(tabName) {
            currentMerchTab = tabName;
            
            document.querySelectorAll('.calendar-tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.merch-tab-pane').forEach(pane => pane.style.display = 'none');

            const heroCatalogLink = document.getElementById('heroCustomerCatalogLink');
            const labelHeroCatalog = document.getElementById('labelHeroCatalog');
            const labelHeroMainCreate = document.getElementById('labelHeroMainCreate');

            if (tabName === 'lanyard') {
                document.getElementById('tabBtnLanyard').classList.add('active');
                document.getElementById('tabContentLanyard').style.display = 'block';
                if (labelHeroMainCreate) labelHeroMainCreate.textContent = 'Tambah Tier Lanyard';
                if (labelHeroCatalog) labelHeroCatalog.textContent = 'Lihat Lanyard di Katalog Pelanggan';
                if (heroCatalogLink) heroCatalogLink.href = "{{ route('user_product_details', ['type' => 'display', 'id' => ($displayLanyard->id ?? 45)]) }}";
            } else if (tabName === 'papannama') {
                document.getElementById('tabBtnPapanNama').classList.add('active');
                document.getElementById('tabContentPapanNama').style.display = 'block';
                if (labelHeroMainCreate) labelHeroMainCreate.textContent = 'Tambah Papan Nama';
                if (labelHeroCatalog) labelHeroCatalog.textContent = 'Lihat Papan Nama di Katalog Pelanggan';
                if (heroCatalogLink) heroCatalogLink.href = "{{ route('user_product_details', ['type' => 'display', 'id' => ($displayNameTag->id ?? 46)]) }}";
            } else if (tabName === 'plakat') {
                document.getElementById('tabBtnPlakat').classList.add('active');
                document.getElementById('tabContentPlakat').style.display = 'block';
                if (labelHeroMainCreate) labelHeroMainCreate.textContent = 'Tambah Plakat';
                if (labelHeroCatalog) labelHeroCatalog.textContent = 'Lihat Plakat di Katalog Pelanggan';
                if (heroCatalogLink) heroCatalogLink.href = "{{ route('user_product_details', ['type' => 'display', 'id' => ($displayPlakat->id ?? 47)]) }}";
            } else if (tabName === 'nota') {
                const tabBtnNota = document.getElementById('tabBtnNota');
                const tabContentNota = document.getElementById('tabContentNota');
                if (tabBtnNota) tabBtnNota.classList.add('active');
                if (tabContentNota) tabContentNota.style.display = 'block';
                if (labelHeroMainCreate) labelHeroMainCreate.textContent = 'Tambah Varian Nota';
                if (labelHeroCatalog) labelHeroCatalog.textContent = 'Lihat Buku Nota di Katalog Pelanggan';
                if (heroCatalogLink) heroCatalogLink.href = "{{ route('user_product_details', ['type' => 'display', 'id' => ($displayNota->id ?? 48)]) }}";
            }

            // Recalculate & adjust responsive DataTables pada switch tab (persis kalender)
            setTimeout(() => {
                if (typeof $.fn.dataTable !== 'undefined') {
                    $.fn.dataTable.tables({
                        visible: true,
                        api: true
                    }).columns.adjust().responsive.recalc();
                }
            }, 50);
        }

        /*
        |--------------------------------------------------------------------------
        | LANYARD HANDLERS
        |--------------------------------------------------------------------------
        */
        function openCreateLanyardModal() {
            document.getElementById('formCreateLanyard').reset();
            document.getElementById('modalCreateLanyard').classList.add('is-open');
            initCalModalSelect2('modalCreateLanyard');
        }

        function openDetailLanyardModal(tier) {
            currentActiveTier = tier;
            const container = document.getElementById('lanyardDetailContainer');
            container.innerHTML = '';

            document.getElementById('lanyardDetailEyebrow').textContent = 'DETAIL · SPESIFIKASI TIER LANYARD';
            document.getElementById('lanyardDetailTitle').textContent = `${tier.tier_label} (${tier.width_type === '2cm' ? '2.0 cm' : '2.5 cm'})`;

            const widthBadge = `<span class="badge ${tier.width_type === '2cm' ? 'primary' : 'success'}">${tier.width_type === '2cm' ? '2.0 cm' : '2.5 cm'}</span>`;
            const packageBadge = tier.package_type === 'tali' 
                ? '<span class="badge" style="background: rgba(194, 168, 87, 0.15); color: #c2a857;">Hanya Tali Saja</span>' 
                : '<span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">1 Set (Holder + ID Card PVC)</span>';
            const statusBadge = '<span class="badge success">AKTIF</span>';
            const rangeText = `${tier.min_qty} pcs s/d ${tier.max_qty ? tier.max_qty + ' pcs' : 'Tak Terbatas (>100)'}`;

            container.innerHTML += makeDetailSectionTitle('1. Spesifikasi Tier Kuantiti');
            container.innerHTML += makeDetailRow('ID Tier Terdaftar', `<strong>#${tier.id}</strong>`);
            container.innerHTML += makeDetailRow('Lebar Tali', widthBadge);
            container.innerHTML += makeDetailRow('Jenis Paket', packageBadge);
            container.innerHTML += makeDetailRow('Rentang Kuantitas', `<strong>${tier.tier_label}</strong> (${rangeText})`);
            container.innerHTML += makeDetailRow('Harga Satuan', `<span style="font-size: 15px; font-weight: 800; color: #c2a857;">Rp ${Number(tier.price).toLocaleString('id-ID')}</span>`);
            container.innerHTML += makeDetailRow('Status Katalog', statusBadge);

            container.innerHTML += makeDetailSectionTitle('2. Keterangan & Kelengkapan Paket');
            if (tier.package_type === 'tali') {
                container.innerHTML += makeDetailInfoBox('🎗️', 'Paket Tali Printing Saja', 'Hanya tali lanyard printing 2 sisi full color bahan tissue premium lembut dilengkapi stopper & pengait besi.');
            } else {
                container.innerHTML += makeDetailInfoBox('🪪', 'Paket 1 Set Komplit', 'Termasuk Tali Printing 2 sisi + Casing Card Holder Plastik + Kartu ID Card bahan PVC Tebal standar perbankan tahan luntur.');
            }

            container.innerHTML += makeDetailSectionTitle('3. Jejak Audit & Riwayat');
            container.innerHTML += makeDetailRow('Diperbarui Oleh', `${tier.updated_by || 'Administrator'}`);
            container.innerHTML += makeDetailRow('Waktu Pembaruan', `${tier.updated_at || '-'}`);

            document.getElementById('btnDetailToEdit').onclick = function() {
                closeCalModal('modalDetailLanyard');
                openEditLanyardModal(tier);
            };

            document.getElementById('modalDetailLanyard').classList.add('is-open');
        }

        function openEditLanyardModal(tier) {
            currentActiveTier = tier;
            $('#editLanyardId').val(tier.id);
            $('#editLanyardWidth').val(tier.width_type).trigger('change');
            $('#editLanyardPackage').val(tier.package_type).trigger('change');
            $('#editLanyardLabel').val(tier.tier_label);
            $('#editLanyardMin').val(tier.min_qty);
            $('#editLanyardMax').val(tier.max_qty || '');
            $('#editLanyardPrice').val(formatRupiahDisplay(tier.price));
            $('#editModalHeading').text(`Edit Tier: ${tier.tier_label}`);

            document.getElementById('modalEditLanyard').classList.add('is-open');
            initCalModalSelect2('modalEditLanyard');
        }

        function confirmDeleteLanyard(id, name) {
            lanyardItemToDelete.id = id;
            lanyardItemToDelete.name = name;

            document.getElementById('deleteLanyardTargetName').textContent = name || ('Tier #' + id);
            document.getElementById('modalDeleteLanyard').classList.add('is-open');
        }

        function executeDeleteLanyard() {
            if (!lanyardItemToDelete.id) return;

            const id = lanyardItemToDelete.id;
            const $btn = $('#btnConfirmDeleteLanyard');
            const origHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Menghapus...');

            const token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();

            fetch(`/admin/merchandise-master/delete-lanyard/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                $btn.prop('disabled', false).html(origHtml);
                closeCalModal('modalDeleteLanyard');

                if (data.success) {
                    if (typeof showToast === 'function') {
                        showToast('success', data.message || 'Tier Lanyard berhasil dihapus!');
                    } else {
                        alert(data.message || 'Tier Lanyard berhasil dihapus!');
                    }
                    setTimeout(() => location.reload(), 400);
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan saat menghapus data.'));
                }
            })
            .catch(err => {
                $btn.prop('disabled', false).html(origHtml);
                closeCalModal('modalDeleteLanyard');
                alert('Terjadi kesalahan jaringan: ' + err.message);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | PAPAN NAMA (NAME TAG DADA) HANDLERS
        |--------------------------------------------------------------------------
        */
        function openCreateNameTagModal() {
            document.getElementById('formCreateNameTag').reset();
            document.getElementById('modalCreateNameTag').classList.add('is-open');
            initCalModalSelect2('modalCreateNameTag');
        }

        function openDetailNameTagModal(nt) {
            currentActiveNameTag = nt;
            const container = document.getElementById('nameTagDetailContainer');
            container.innerHTML = '';

            document.getElementById('nameTagDetailEyebrow').textContent = 'DETAIL · PAPAN NAMA DADA';
            document.getElementById('nameTagDetailTitle').textContent = nt.name;

            let typeBadge = '';
            if (nt.type_code === 'biasa') {
                typeBadge = '<span class="badge" style="background: rgba(100, 116, 139, 0.15); color: #64748b;">Akrilik Biasa</span>';
            } else if (nt.type_code === 'resin') {
                typeBadge = '<span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6;">Resin Cembung Bening</span>';
            } else {
                typeBadge = '<span class="badge" style="background: rgba(194, 168, 87, 0.2); color: #c2a857;">Resin Cembung Plat Emas</span>';
            }

            container.innerHTML += makeDetailSectionTitle('1. Spesifikasi Varian');
            container.innerHTML += makeDetailRow('ID Varian', `<strong>#${nt.id}</strong>`);
            container.innerHTML += makeDetailRow('Nama Produk', `<strong>${nt.name}</strong>`);
            container.innerHTML += makeDetailRow('Tipe Bahan', typeBadge);
            container.innerHTML += makeDetailRow('Pilihan Pengait', `<span class="badge" style="background: var(--bg-surface-2);">${nt.fastener_options || 'Peniti, Magnet'}</span>`);
            container.innerHTML += makeDetailRow('Min. Order Kuantitas', `${nt.min_order_qty} pcs`);
            container.innerHTML += makeDetailRow('Harga Satuan', `<span style="font-size: 15px; font-weight: 800; color: #c2a857;">Rp ${Number(nt.price).toLocaleString('id-ID')}</span>`);
            container.innerHTML += makeDetailRow('Status Katalog', '<span class="badge success">AKTIF</span>');

            container.innerHTML += makeDetailSectionTitle('2. Deskripsi & Keunggulan');
            container.innerHTML += makeDetailInfoBox('🏷️', nt.name, nt.description || 'Papan nama dada profesional seragam kantor.');

            container.innerHTML += makeDetailSectionTitle('3. Riwayat Sistem');
            container.innerHTML += makeDetailRow('Diperbarui Oleh', `${nt.updated_by || 'Administrator'}`);
            container.innerHTML += makeDetailRow('Waktu Pembaruan', `${nt.updated_at || '-'}`);

            document.getElementById('btnDetailNameTagToEdit').onclick = function() {
                closeCalModal('modalDetailNameTag');
                openEditNameTagModal(nt);
            };

            document.getElementById('modalDetailNameTag').classList.add('is-open');
        }

        function openEditNameTagModal(nt) {
            currentActiveNameTag = nt;
            $('#editNameTagId').val(nt.id);
            $('#editNameTagName').val(nt.name);
            $('#editNameTagType').val(nt.type_code).trigger('change');
            $('#editNameTagFasteners').val(nt.fastener_options);
            $('#editNameTagMinOrder').val(nt.min_order_qty);
            $('#editNameTagPrice').val(formatRupiahDisplay(nt.price));
            $('#editNameTagDesc').val(nt.description || '');
            $('#editNameTagHeading').text(`Edit Varian: ${nt.name}`);

            document.getElementById('modalEditNameTag').classList.add('is-open');
            initCalModalSelect2('modalEditNameTag');
        }

        function confirmDeleteNameTag(id, name) {
            nameTagItemToDelete.id = id;
            nameTagItemToDelete.name = name;

            document.getElementById('deleteNameTagTargetName').textContent = name || ('Varian #' + id);
            document.getElementById('modalDeleteNameTag').classList.add('is-open');
        }

        function executeDeleteNameTag() {
            if (!nameTagItemToDelete.id) return;

            const id = nameTagItemToDelete.id;
            const $btn = $('#btnConfirmDeleteNameTag');
            const origHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Menghapus...');

            const token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();

            fetch(`/admin/merchandise-master/delete-nametag/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                $btn.prop('disabled', false).html(origHtml);
                closeCalModal('modalDeleteNameTag');

                if (data.success) {
                    if (typeof showToast === 'function') {
                        showToast('success', data.message || 'Papan Nama berhasil dihapus!');
                    } else {
                        alert(data.message || 'Papan Nama berhasil dihapus!');
                    }
                    setTimeout(() => location.reload(), 400);
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan saat menghapus data.'));
                }
            })
            .catch(err => {
                $btn.prop('disabled', false).html(origHtml);
                closeCalModal('modalDeleteNameTag');
                alert('Terjadi kesalahan jaringan: ' + err.message);
            });
        }

        /*
        |--------------------------------------------------------------------------
        | PLAKAT (AKRILIK & KAYU) HANDLERS
        |--------------------------------------------------------------------------
        */
        function openCreatePlakatModal() {
            document.getElementById('formCreatePlakat').reset();
            $('input[name="design_fee"]', '#formCreatePlakat').val('Rp 0');
            document.getElementById('modalCreatePlakat').classList.add('is-open');
            initCalModalSelect2('modalCreatePlakat');
        }

        function openDetailPlakatModal(pk) {
            currentActivePlakat = pk;
            const container = document.getElementById('plakatDetailContainer');
            container.innerHTML = '';

            document.getElementById('plakatDetailEyebrow').textContent = 'DETAIL · SPESIFIKASI PLAKAT';
            document.getElementById('plakatDetailTitle').textContent = pk.name;

            container.innerHTML += makeDetailRow('Nama Plakat', pk.name);
            container.innerHTML += makeDetailRow('Jenis Bahan', pk.material_type === 'kayu' ? '🪵 Kayu Solid Mewah' : '✨ Akrilik Cetak UV');
            container.innerHTML += makeDetailRow('Ketebalan', pk.thickness.toUpperCase());
            container.innerHTML += makeDetailRow('Jenis Dudukan', pk.base_type === 'hitam' ? 'Dudukan Hitam Elegan' : (pk.base_type === 'kayu' ? 'Dudukan Kayu' : 'Dudukan Akrilik'));
            container.innerHTML += makeDetailRow('Kemasan (Packaging)', pk.box_type === 'box' ? '🎁 Termasuk Box Beludru Eksklusif' : 'Tanpa Box');
            container.innerHTML += makeDetailRow('Dimensi / Ukuran', pk.dimensions || '20x15 cm');
            container.innerHTML += makeDetailRow('Harga Satuan', `<span style="color: #c2a857; font-weight: 700; font-size: 14px;">${formatRupiahDisplay(pk.price)}</span>`);
            container.innerHTML += makeDetailRow('Biaya Desain Standar', formatRupiahDisplay(pk.design_fee || 0));
            container.innerHTML += makeDetailRow('Status Katalog', pk.status === 'active' ? '<span class="badge success">Aktif</span>' : '<span class="badge">Nonaktif</span>');

            if (pk.description) {
                container.innerHTML += makeDetailSectionTitle('Deskripsi & Spesifikasi');
                container.innerHTML += `
                    <div style="padding: 10px 14px; background: var(--bg-surface-2); border: 1px solid var(--border); border-radius: 8px; font-size: 12.5px; color: var(--t-base); line-height: 1.5;">
                        ${pk.description}
                    </div>
                `;
            }

            container.innerHTML += makeDetailInfoBox(
                '💡',
                'Ketentuan Jasa Desain',
                'Harga tercantum belum termasuk biaya jasa pembuatan desain grafis jika pemesan belum memiliki file desain siap cetak (vector / high-res PDF).'
            );

            document.getElementById('btnEditPlakatFromDetail').onclick = function() {
                closeCalModal('modalDetailPlakat');
                openEditPlakatModal(pk);
            };

            document.getElementById('modalDetailPlakat').classList.add('is-open');
        }

        function openEditPlakatModal(pk) {
            currentActivePlakat = pk;
            $('#editPlakatId').val(pk.id);
            $('#editPlakatName').val(pk.name);
            $('#editPlakatMaterial').val(pk.material_type).trigger('change');
            $('#editPlakatThickness').val(pk.thickness).trigger('change');
            $('#editPlakatBase').val(pk.base_type).trigger('change');
            $('#editPlakatBox').val(pk.box_type).trigger('change');
            $('#editPlakatDimensions').val(pk.dimensions || '20x15 cm');
            $('#editPlakatPrice').val(formatRupiahDisplay(pk.price));
            $('#editPlakatDesignFee').val(formatRupiahDisplay(pk.design_fee || 0));
            $('#editPlakatStatus').val(pk.status || 'active').trigger('change');
            $('#editPlakatDesc').val(pk.description || '');
            $('#editPlakatHeading').text(`Edit Varian: ${pk.name}`);

            document.getElementById('modalEditPlakat').classList.add('is-open');
            initCalModalSelect2('modalEditPlakat');
        }

        function confirmDeletePlakat(id, name) {
            plakatItemToDelete.id = id;
            plakatItemToDelete.name = name;

            document.getElementById('deletePlakatTargetName').textContent = name || ('Plakat #' + id);
            document.getElementById('modalDeletePlakat').classList.add('is-open');
        }

        function executeDeletePlakat() {
            if (!plakatItemToDelete.id) return;

            const id = plakatItemToDelete.id;
            const $btn = $('#btnConfirmDeletePlakat');
            const origHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Menghapus...');

            const token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();

            fetch(`/admin/merchandise-master/delete-plakat/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                $btn.prop('disabled', false).html(origHtml);
                closeCalModal('modalDeletePlakat');

                if (data.success) {
                    if (typeof showToast === 'function') {
                        showToast('success', data.message || 'Plakat berhasil dihapus!');
                    } else {
                        alert(data.message || 'Plakat berhasil dihapus!');
                    }
                    setTimeout(() => location.reload(), 400);
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan saat menghapus data.'));
                }
            })
            .catch(err => {
                $btn.prop('disabled', false).html(origHtml);
                closeCalModal('modalDeletePlakat');
                alert('Terjadi kesalahan jaringan: ' + err.message);
            });
        }

        // Inisialisasi jQuery & DataTables
        $(document).ready(function() {
            function initMerchDataTable(selector, placeholder) {
                if (!$.fn.DataTable.isDataTable(selector)) {
                    return $(selector).DataTable({
                        pageLength: 10,
                        lengthMenu: [10, 25, 50, 100],
                        ordering: true,
                        responsive: {
                            details: {
                                type: 'column',
                                target: 0
                            }
                        },
                        columnDefs: [
                            {
                                className: 'dtr-control',
                                orderable: false,
                                searchable: false,
                                targets: 0
                            },
                            {
                                orderable: false,
                                searchable: false,
                                responsivePriority: 1,
                                targets: -1
                            }
                        ],
                        layout: {
                            topStart: 'pageLength',
                            topEnd: 'search',
                            bottomStart: 'info',
                            bottomEnd: 'paging'
                        },
                        language: {
                            search: 'Search:',
                            lengthMenu: '_MENU_ entries per page',
                            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                            infoEmpty: 'Showing 0 to 0 of 0 entries',
                            zeroRecords: 'Data tidak ditemukan',
                            processing: 'Memuat data...',
                            searchPlaceholder: placeholder || 'Cari data...'
                        },
                        initComplete: function() {
                            $(this).closest('.dt-container, .dataTables_wrapper')
                                .find('.dt-search input, .dataTables_filter input')
                                .css({
                                    'padding': '8px 14px',
                                    'border-radius': '8px'
                                });
                        }
                    });
                }
            }

            // Inisialisasi Ketiga Table (Lanyard, Papan Nama, Plakat)
            window.merchTableLanyard = initMerchDataTable('#merchLanyardTable', 'Cari tier, ukuran, paket, harga...');
            window.merchTableNameTag = initMerchDataTable('#merchNameTagTable', 'Cari nama, bahan, pengait, harga...');
            window.merchTablePlakat  = initMerchDataTable('#merchPlakatTable',  'Cari nama, bahan, tebal, dudukan, kemasan, harga...');
            window.merchTableNota    = initMerchDataTable('#merchNotaTable',    'Cari varian, kertas, ply, tinta, harga...');

            // Filter Cepat Pills Nota
            $('.merch-filter-pill-nota').on('click', function() {
                $('.merch-filter-pill-nota').removeClass('active');
                $(this).addClass('active');

                const filterVal = $(this).data('filter');
                if (window.merchTableNota) {
                    window.merchTableNota.search(filterVal).draw();
                }
            });

            // Auto-switch tab from URL query param (?tab=nota / ?tab=plakat / etc.)
            const urlParams = new URLSearchParams(window.location.search);
            const initialTab = urlParams.get('tab');
            if (initialTab && ['lanyard', 'papannama', 'plakat', 'nota'].includes(initialTab)) {
                switchMerchTab(initialTab);
            }

            // Sync hidden labels for select2 in Nota modals
            $('#createPlyCount').on('change', function() {
                $('#createPlyLabel').val($(this).find('option:selected').text());
            });
            $('#createInkCount').on('change', function() {
                $('#createInkLabel').val($(this).find('option:selected').text());
            });
            $('#editPlyCount').on('change', function() {
                $('#editPlyLabel').val($(this).find('option:selected').text());
            });
            $('#editInkCount').on('change', function() {
                $('#editInkLabel').val($(this).find('option:selected').text());
            });

            // Form Submit: Tambah Nota
            $('#formCreateNota').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSaveCreateNota');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Menyimpan...');

                const formData = new FormData(this);
                const rawPrice = formData.get('price');
                formData.set('price', cleanRupiahValue(rawPrice));

                fetch('{{ route("admin_store_merchandise_nota") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    $btn.prop('disabled', false).html(origHtml);
                    if (data.success) {
                        closeCalModal('modalCreateNota');
                        if (typeof showToast === 'function') {
                            showToast('success', data.message || 'Varian Nota berhasil ditambahkan!');
                        } else {
                            alert(data.message || 'Varian Nota berhasil ditambahkan!');
                        }
                        setTimeout(() => location.reload(), 400);
                    } else {
                        alert('Gagal: ' + (data.message || 'Terjadi kesalahan.'));
                    }
                })
                .catch(err => {
                    $btn.prop('disabled', false).html(origHtml);
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
            });

            // Form Submit: Edit Nota
            $('#formEditNota').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSaveEditNota');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Menyimpan...');

                const id = $('#editNotaId').val();
                const formData = new FormData(this);
                const rawPrice = formData.get('price');
                formData.set('price', cleanRupiahValue(rawPrice));

                const updateUrl = '{{ route("admin_update_merchandise_nota", ":id") }}'.replace(':id', id);

                fetch(updateUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    $btn.prop('disabled', false).html(origHtml);
                    if (data.success) {
                        closeCalModal('modalEditNota');
                        if (typeof showToast === 'function') {
                            showToast('success', data.message || 'Varian Nota berhasil diperbarui!');
                        } else {
                            alert(data.message || 'Varian Nota berhasil diperbarui!');
                        }
                        setTimeout(() => location.reload(), 400);
                    } else {
                        alert('Gagal: ' + (data.message || 'Terjadi kesalahan.'));
                    }
                })
                .catch(err => {
                    $btn.prop('disabled', false).html(origHtml);
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
            });

            // Filter Cepat Pills Lanyard
            $('.merch-filter-pill').on('click', function() {
                $('.merch-filter-pill').removeClass('active');
                $(this).addClass('active');

                const filterVal = $(this).data('filter');
                if (window.merchTableLanyard) {
                    window.merchTableLanyard.search(filterVal).draw();
                }
            });

            // Filter Cepat Pills Plakat
            $('.merch-filter-pill-plakat').on('click', function() {
                $('.merch-filter-pill-plakat').removeClass('active');
                $(this).addClass('active');

                const filterVal = $(this).data('filter');
                if (window.merchTablePlakat) {
                    window.merchTablePlakat.search(filterVal).draw();
                }
            });

            // Live Format Rupiah saat pengguna mengetik (Persis Kalender)
            $(document).on('input', '.input-rupiah', function() {
                let val = $(this).val();
                if (!val) return;
                let clean = val.replace(/[^0-9]/g, '');
                let formatted = clean ? clean.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
                $(this).val(formatted ? 'Rp ' + formatted : '');
            });

            // Close on backdrop overlay click
            document.querySelectorAll('.modal-cal').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.remove('is-open');
                    }
                });
            });

            // Close on ESC key press
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.modal-cal.is-open').forEach(modal => {
                        modal.classList.remove('is-open');
                    });
                }
            });

            // Submit Form Create Lanyard via AJAX
            $('#formCreateLanyard').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSaveCreateLanyard');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Menyimpan...');

                const token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
                const rawPrice = $('input[name="price"]', this).val();
                const formData = {
                    width_type: $('select[name="width_type"]', this).val(),
                    package_type: $('select[name="package_type"]', this).val(),
                    tier_label: $('input[name="tier_label"]', this).val(),
                    min_qty: $('input[name="min_qty"]', this).val(),
                    max_qty: $('input[name="max_qty"]', this).val() || null,
                    price: cleanRupiahValue(rawPrice)
                };

                fetch('{{ route("admin_store_merchandise_lanyard_tier") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(res => res.json())
                .then(data => {
                    $btn.prop('disabled', false).html(origHtml);
                    if (data.success) {
                        closeCalModal('modalCreateLanyard');
                        if (typeof showToast === 'function') {
                            showToast('success', data.message || 'Tier Lanyard berhasil ditambahkan!');
                        } else {
                            alert(data.message || 'Tier Lanyard berhasil ditambahkan!');
                        }
                        setTimeout(() => location.reload(), 400);
                    } else {
                        alert('Gagal: ' + (data.message || 'Terjadi kesalahan.'));
                    }
                })
                .catch(err => {
                    $btn.prop('disabled', false).html(origHtml);
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
            });

            // Submit Form Edit Lanyard via AJAX
            $('#formEditLanyard').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editLanyardId').val();
                const $btn = $('#btnSaveEditLanyard');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Menyimpan...');

                const token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
                const rawPrice = $('#editLanyardPrice').val();
                const formData = {
                    width_type: $('#editLanyardWidth').val(),
                    package_type: $('#editLanyardPackage').val(),
                    tier_label: $('#editLanyardLabel').val(),
                    min_qty: $('#editLanyardMin').val(),
                    max_qty: $('#editLanyardMax').val() || null,
                    price: cleanRupiahValue(rawPrice)
                };

                fetch(`/admin/merchandise-master/update-lanyard/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(res => res.json())
                .then(data => {
                    $btn.prop('disabled', false).html(origHtml);
                    if (data.success) {
                        closeCalModal('modalEditLanyard');
                        if (typeof showToast === 'function') {
                            showToast('success', data.message || 'Tier Lanyard berhasil diperbarui!');
                        } else {
                            alert(data.message || 'Tier Lanyard berhasil diperbarui!');
                        }
                        setTimeout(() => location.reload(), 400);
                    } else {
                        alert('Gagal: ' + (data.message || 'Terjadi kesalahan.'));
                    }
                })
                .catch(err => {
                    $btn.prop('disabled', false).html(origHtml);
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
            });

            // Submit Form Create Name Tag via AJAX
            $('#formCreateNameTag').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSaveCreateNameTag');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Menyimpan...');

                const token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
                const rawPrice = $('input[name="price"]', this).val();
                const formData = {
                    name: $('input[name="name"]', this).val(),
                    type_code: $('select[name="type_code"]', this).val(),
                    fastener_options: $('input[name="fastener_options"]', this).val(),
                    min_order_qty: $('input[name="min_order_qty"]', this).val() || 1,
                    price: cleanRupiahValue(rawPrice),
                    description: $('textarea[name="description"]', this).val()
                };

                fetch('{{ route("admin_store_merchandise_nametag") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(res => res.json())
                .then(data => {
                    $btn.prop('disabled', false).html(origHtml);
                    if (data.success) {
                        closeCalModal('modalCreateNameTag');
                        if (typeof showToast === 'function') {
                            showToast('success', data.message || 'Papan Nama berhasil ditambahkan!');
                        } else {
                            alert(data.message || 'Papan Nama berhasil ditambahkan!');
                        }
                        setTimeout(() => location.reload(), 400);
                    } else {
                        alert('Gagal: ' + (data.message || 'Terjadi kesalahan.'));
                    }
                })
                .catch(err => {
                    $btn.prop('disabled', false).html(origHtml);
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
            });

            // Submit Form Edit Name Tag via AJAX
            $('#formEditNameTag').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editNameTagId').val();
                const $btn = $('#btnSaveEditNameTag');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Menyimpan...');

                const token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
                const rawPrice = $('#editNameTagPrice').val();
                const formData = {
                    name: $('#editNameTagName').val(),
                    type_code: $('#editNameTagType').val(),
                    fastener_options: $('#editNameTagFasteners').val(),
                    min_order_qty: $('#editNameTagMinOrder').val() || 1,
                    price: cleanRupiahValue(rawPrice),
                    description: $('#editNameTagDesc').val()
                };

                fetch(`/admin/merchandise-master/update-nametag/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(res => res.json())
                .then(data => {
                    $btn.prop('disabled', false).html(origHtml);
                    if (data.success) {
                        closeCalModal('modalEditNameTag');
                        if (typeof showToast === 'function') {
                            showToast('success', data.message || 'Papan Nama berhasil diperbarui!');
                        } else {
                            alert(data.message || 'Papan Nama berhasil diperbarui!');
                        }
                        setTimeout(() => location.reload(), 400);
                    } else {
                        alert('Gagal: ' + (data.message || 'Terjadi kesalahan.'));
                    }
                })
                .catch(err => {
                    $btn.prop('disabled', false).html(origHtml);
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
            });

            // Submit Form Create Plakat via AJAX
            $('#formCreatePlakat').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSaveCreatePlakat');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Menyimpan...');

                const token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
                const rawPrice = $('input[name="price"]', this).val();
                const rawFee = $('input[name="design_fee"]', this).val();
                const formData = {
                    name: $('input[name="name"]', this).val(),
                    material_type: $('select[name="material_type"]', this).val(),
                    thickness: $('select[name="thickness"]', this).val(),
                    base_type: $('select[name="base_type"]', this).val(),
                    box_type: $('select[name="box_type"]', this).val(),
                    dimensions: $('input[name="dimensions"]', this).val() || '20x15 cm',
                    price: cleanRupiahValue(rawPrice),
                    design_fee: cleanRupiahValue(rawFee),
                    status: $('select[name="status"]', this).val() || 'active',
                    description: $('textarea[name="description"]', this).val()
                };

                fetch('{{ route("admin_store_merchandise_plakat") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(res => res.json())
                .then(data => {
                    $btn.prop('disabled', false).html(origHtml);
                    if (data.success) {
                        closeCalModal('modalCreatePlakat');
                        if (typeof showToast === 'function') {
                            showToast('success', data.message || 'Plakat berhasil ditambahkan!');
                        } else {
                            alert(data.message || 'Plakat berhasil ditambahkan!');
                        }
                        setTimeout(() => location.reload(), 400);
                    } else {
                        alert('Gagal: ' + (data.message || 'Terjadi kesalahan.'));
                    }
                })
                .catch(err => {
                    $btn.prop('disabled', false).html(origHtml);
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
            });

            // Submit Form Edit Plakat via AJAX
            $('#formEditPlakat').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editPlakatId').val();
                const $btn = $('#btnSaveEditPlakat');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Menyimpan...');

                const token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
                const rawPrice = $('#editPlakatPrice').val();
                const rawFee = $('#editPlakatDesignFee').val();
                const formData = {
                    name: $('#editPlakatName').val(),
                    material_type: $('#editPlakatMaterial').val(),
                    thickness: $('#editPlakatThickness').val(),
                    base_type: $('#editPlakatBase').val(),
                    box_type: $('#editPlakatBox').val(),
                    dimensions: $('#editPlakatDimensions').val() || '20x15 cm',
                    price: cleanRupiahValue(rawPrice),
                    design_fee: cleanRupiahValue(rawFee),
                    status: $('#editPlakatStatus').val() || 'active',
                    description: $('#editPlakatDesc').val()
                };

                fetch(`/admin/merchandise-master/update-plakat/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(res => res.json())
                .then(data => {
                    $btn.prop('disabled', false).html(origHtml);
                    if (data.success) {
                        closeCalModal('modalEditPlakat');
                        if (typeof showToast === 'function') {
                            showToast('success', data.message || 'Plakat berhasil diperbarui!');
                        } else {
                            alert(data.message || 'Plakat berhasil diperbarui!');
                        }
                        setTimeout(() => location.reload(), 400);
                    } else {
                        alert('Gagal: ' + (data.message || 'Terjadi kesalahan.'));
                    }
                })
                .catch(err => {
                    $btn.prop('disabled', false).html(origHtml);
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
            });
        });
    
        // =========================================================
        // JAVASCRIPT CRUD BUKU NOTA (NCR & HVS)
        // =========================================================
        let currentActiveNota = null;
        let notaItemToDelete = { id: null, name: '' };

        function handlePaperTypeChange(mode) {
            const paperType = document.getElementById(mode === 'create' ? 'createPaperType' : 'editPaperType').value;
            const plySelect = document.getElementById(mode === 'create' ? 'createPlyCount' : 'editPlyCount');
            const sheetsInput = document.getElementById(mode === 'create' ? 'createSheetsPerBook' : 'editSheetsPerBook');
            const inkSelect = document.getElementById(mode === 'create' ? 'createInkCount' : 'editInkCount');

            if (paperType === 'hvs') {
                if (sheetsInput) sheetsInput.value = 100;
                if (plySelect) {
                    plySelect.value = "1";
                    $(plySelect).val("1").trigger('change');
                }
            } else {
                if (sheetsInput && parseInt(sheetsInput.value) === 100) sheetsInput.value = 50;
                if (plySelect && plySelect.value === "1") {
                    plySelect.value = "2";
                    $(plySelect).val("2").trigger('change');
                }
            }
        }

        function openCreateNotaModal() {
            const form = document.getElementById('formCreateNota');
            if (form) form.reset();
            $('#createPaperType').val('ncr').trigger('change');
            $('#createPlyCount').val(2).trigger('change');
            $('#createInkCount').val(1).trigger('change');
            $('#createSheetsPerBook').val(50);
            const modal = document.getElementById('modalCreateNota');
            if (modal) modal.classList.add('is-open');
            initCalModalSelect2('modalCreateNota');
        }

        function openDetailNotaModal(nt) {
            currentActiveNota = nt;
            const titleEl = document.getElementById('notaDetailTitle');
            if (titleEl) titleEl.textContent = nt.name || 'Detail Varian Nota';

            const container = document.getElementById('notaDetailContainer');
            if (container) {
                const paperBadge = nt.paper_type === 'ncr' 
                    ? '<span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; font-weight: 700;">NCR (Carbonless)</span>'
                    : '<span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-weight: 700;">HVS Putih</span>';

                const statusBadge = (nt.status || 'active') === 'active'
                    ? '<span class="badge success">Aktif</span>'
                    : '<span class="badge danger">Nonaktif</span>';

                let html = '';
                html += makeDetailSectionTitle('SPESIFIKASI FORM NOTA');
                html += makeDetailRow('Nama Varian', nt.name || '-');
                html += makeDetailRow('Jenis Kertas', paperBadge);
                html += makeDetailRow('Jumlah Rangkap', (nt.ply_label || (nt.ply_count + ' Ply')));
                html += makeDetailRow('Warna Tinta', (nt.ink_label || (nt.ink_count + ' Tinta')));
                html += makeDetailRow('Isi per Buku', (nt.sheets_per_book || 50) + (nt.paper_type === 'ncr' ? ' Set/Buku' : ' Lembar/Buku'));

                html += makeDetailSectionTitle('TARIF & HARGA JUAL');
                html += makeDetailRow('Harga Satuan', '<strong style="color: #c2a857; font-size: 14px;">' + formatRupiahDisplay(nt.price) + '</strong> <span style="font-size: 11px; color: var(--t-muted);">/ paket rim</span>');
                html += makeDetailRow('Status Katalog', statusBadge);

                if (nt.description) {
                    html += makeDetailSectionTitle('KETERANGAN');
                    html += '<div style="padding: 10px 14px; background: var(--bg-surface-2); border: 1px solid var(--border); border-radius: 8px; font-size: 12.5px; color: var(--t-muted); line-height: 1.5;">' + nt.description + '</div>';
                }

                container.innerHTML = html;
            }

            const btnEdit = document.getElementById('btnEditNotaFromDetail');
            if (btnEdit) {
                btnEdit.onclick = function() {
                    closeCalModal('modalDetailNota');
                    openEditNotaModal(currentActiveNota);
                };
            }

            const modal = document.getElementById('modalDetailNota');
            if (modal) modal.classList.add('is-open');
        }

        function openEditNotaModal(nt) {
            currentActiveNota = nt;
            const heading = document.getElementById('editNotaHeading');
            if (heading) heading.textContent = 'Edit: ' + nt.name;

            $('#editNotaId').val(nt.id);
            $('#editNotaName').val(nt.name);
            $('#editPaperType').val(nt.paper_type).trigger('change');
            $('#editPlyCount').val(nt.ply_count).trigger('change');
            $('#editPlyLabel').val(nt.ply_label || (nt.ply_count + ' Ply'));
            $('#editInkCount').val(nt.ink_count).trigger('change');
            $('#editInkLabel').val(nt.ink_label || (nt.ink_count + ' Tinta'));
            $('#editSheetsPerBook').val(nt.sheets_per_book);
            $('#editNotaPrice').val(formatRupiahDisplay(nt.price));
            $('#editNotaStatus').val(nt.status || 'active').trigger('change');
            $('#editNotaDesc').val(nt.description || '');

            const modal = document.getElementById('modalEditNota');
            if (modal) modal.classList.add('is-open');
            initCalModalSelect2('modalEditNota');
        }

        function confirmDeleteNota(id, name) {
            notaItemToDelete = { id: id, name: name };
            const targetEl = document.getElementById('deleteNotaTargetName');
            if (targetEl) targetEl.textContent = name;
            const modal = document.getElementById('modalDeleteNota');
            if (modal) modal.classList.add('is-open');
        }

        function executeDeleteNota() {
            if (!notaItemToDelete.id) return;
            const $btn = $('#btnConfirmDeleteNota');
            const origHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Menghapus...');

            const deleteUrl = '{{ route("admin_destroy_merchandise_nota", ":id") }}'.replace(':id', notaItemToDelete.id);

            fetch(deleteUrl, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                $btn.prop('disabled', false).html(origHtml);
                if (data.success) {
                    closeCalModal('modalDeleteNota');
                    if (typeof showToast === 'function') {
                        showToast('success', data.message || 'Varian Nota berhasil dihapus!');
                    } else {
                        alert(data.message || 'Varian Nota berhasil dihapus!');
                    }
                    setTimeout(() => location.reload(), 400);
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan.'));
                }
            })
            .catch(err => {
                $btn.prop('disabled', false).html(origHtml);
                alert('Terjadi kesalahan jaringan: ' + err.message);
            });
        }

    </script>
@endsection
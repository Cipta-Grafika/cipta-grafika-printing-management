@extends('admin_master')

@section('contents')
    <link rel="stylesheet" href="{{ asset('css/production-cost-finishing-index.css') }}">
    <style>
        /* TABS THEME COMPATIBILITY */
        .offset-tabs {
            display: flex;
            gap: 10px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .offset-tab-btn {
            background: var(--bg-card);
            border: 1px solid var(--border);
            padding: 8px 18px;
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

        .offset-tab-btn:hover {
            border-color: var(--primary);
            background: var(--bg-hover);
            color: var(--t-base);
        }

        .offset-tab-btn.active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        .offset-badge-counter {
            font-size: 11px;
            padding: 2px 7px;
            border-radius: 9999px;
            background: var(--bg-muted);
            color: var(--t-base);
            font-weight: 600;
        }

        .offset-tab-btn.active .offset-badge-counter {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* METRIC CARDS THEME COMPATIBILITY */
        .offset-card-metric {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .offset-metric-item {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .offset-metric-item:hover {
            border-color: var(--primary);
        }

        .offset-metric-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: var(--bg-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            flex-shrink: 0;
        }

        .offset-metric-icon svg {
            width: 22px;
            height: 22px;
            fill: none;
            stroke: currentColor;
            stroke-width: 2;
        }

        .offset-metric-val {
            font-size: 22px;
            font-weight: 700;
            color: var(--t-base);
            line-height: 1.2;
        }

        .offset-metric-lbl {
            font-size: 12.5px;
            color: var(--t-muted);
            margin-top: 2px;
        }

        .offset-table-section {
            display: none;
        }

        .offset-table-section.active {
            display: block;
        }

        /* BADGES */
        .badge-active {
            background: var(--success-soft);
            color: var(--success);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            /* font-weight: 600; */
            display: inline-block;
        }

        .badge-inactive {
            background: var(--bg-muted);
            color: var(--t-muted);
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        /* FORM IN MODALS */
        .modal-body .field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 14px;
        }

        @media (max-width: 640px) {
            .modal-body .field-row {
                grid-template-columns: 1fr;
            }
        }

        .modal-body .field-full {
            margin-bottom: 14px;
        }

        .modal-body label {
            display: block;
            font-size: 12.5px;
            font-weight: 500;
            color: var(--t-base);
            margin-bottom: 6px;
        }

        .modal-body .input {
            width: 100%;
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--t-base);
            border-radius: 8px;
            padding: 9px 12px;
            font-size: 13.5px;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .modal-body .input:focus {
            border-color: var(--primary);
        }

        .modal-dialog {
            background: var(--bg-card) !important;
            border: 1px solid var(--border) !important;
            max-width: 540px;
            border-radius: 14px;
        }

        .modal-header {
            border-bottom: 1px solid var(--border);
            padding: 18px 22px;
        }

        .modal-footer {
            border-top: 1px solid var(--border);
            padding: 16px 22px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .modal-close {
            background: transparent;
            border: none;
            color: var(--t-muted);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
        }

        .modal-close:hover {
            color: var(--t-base);
            background: var(--bg-hover);
        }

        /* SELECT2 DARK & LIGHT FIXES IN MODAL */
        .select2-container {
            width: 100% !important;
        }

        /* Wrapper dropdown select2 (tanpa kelas .select2) tidak boleh 100% agar tidak memicu scroll horizontal */
        .select2-container:not(.select2) {
            width: auto !important;
        }

        .select2-container--default .select2-selection--single {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border) !important;
            border-radius: 8px !important;
            height: 42px !important;
            display: flex !important;
            align-items: center !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--t-base) !important;
            line-height: 40px !important;
            padding-left: 12px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px !important;
            right: 8px !important;
        }

        .select2-dropdown {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border) !important;
            border-radius: 8px !important;
            z-index: 99999 !important;
        }

        .select2-container--default .select2-results__option {
            color: var(--t-base) !important;
            padding: 8px 12px !important;
            font-size: 13.5px !important;
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

        /* DATATABLE FILTER (pola master kategori) */
        .cat-length-box .select2-container {
            width: 76px !important;
        }

        .offset-table-section .cat-card-container {
            min-width: 0;
        }
    </style>

    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">

                {{-- HERO SECTION --}}
                <section class="hero cat-hero-compact">
                    <div class="hero-text">
                        <span class="eyebrow">MASTER DATA · OFFSET PRINTING</span>
                        <p class="hero-sub">
                            Kelola master data bahan kertas plano, plat CTP, parameter operasional mesin cetak offset, serta
                            aturan harga jual (pricing rules).
                        </p>

                        {{-- TABS SWITCHER --}}
                        <div class="offset-tabs">
                            <button type="button" class="offset-tab-btn active" data-target="tabPlano"
                                data-create-label="Tambah Kertas Plano" data-modal="modalTambahPlano">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                </svg>
                                <span>Kertas Plano</span>
                                <span class="offset-badge-counter" id="badgeCountPlano">{{ $totalPlano }}</span>
                            </button>

                            <button type="button" class="offset-tab-btn" data-target="tabPlates"
                                data-create-label="Tambah Plat CTP" data-modal="modalTambahPlate">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="3" y1="9" x2="21" y2="9"></line>
                                    <line x1="9" y1="21" x2="9" y2="9"></line>
                                </svg>
                                <span>Plat CTP</span>
                                <span class="offset-badge-counter" id="badgeCountPlates">{{ $totalPlates }}</span>
                            </button>

                            <button type="button" class="offset-tab-btn" data-target="tabPrintCosts"
                                data-create-label="Tambah Profil Mesin" data-modal="modalTambahPrintCost">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2">
                                    </path>
                                    <rect x="6" y="14" width="12" height="8"></rect>
                                </svg>
                                <span>Tarif Mesin & Potong</span>
                                <span class="offset-badge-counter" id="badgeCountPrintCosts">{{ $totalPrintCosts }}</span>
                            </button>

                            <button type="button" class="offset-tab-btn" data-target="tabPricingRules"
                                data-create-label="Tambah Pricing Rule" data-modal="modalTambahPricingRule">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M19 5 5 19"></path>
                                    <circle cx="7" cy="7" r="2"></circle>
                                    <circle cx="17" cy="17" r="2"></circle>
                                    <path d="M15 5h4v4"></path>
                                </svg>
                                <span>Pricing Rules</span>
                                <span class="offset-badge-counter"
                                    id="badgeCountPricingRules">{{ $totalPricingRules }}</span>
                            </button>

                            <button type="button" class="offset-tab-btn" data-target="tabProducts"
                                data-create-label="Tambah Produk Cetak" data-modal="modalTambahProduct">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                                </svg>
                                <span>Produk Cetak</span>
                                <span class="offset-badge-counter"
                                    id="badgeCountProducts">{{ $totalProducts ?? 0 }}</span>
                            </button>
                        </div>
                    </div>
                </section>

                {{-- STATS CARDS --}}
                <div class="offset-card-metric">
                    <div class="offset-metric-item">
                        <div class="offset-metric-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                            </svg>
                        </div>
                        <div>
                            <div class="offset-metric-val" id="statValPlano">{{ $totalPlano }}</div>
                            <div class="offset-metric-lbl">Varian Bahan Plano</div>
                        </div>
                    </div>

                    <div class="offset-metric-item">
                        <div class="offset-metric-icon">
                            <svg viewBox="0 0 24 24">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                <line x1="3" y1="9" x2="21" y2="9" />
                                <line x1="9" y1="21" x2="9" y2="9" />
                            </svg>
                        </div>
                        <div>
                            <div class="offset-metric-val" id="statValPlates">{{ $totalPlates }}</div>
                            <div class="offset-metric-lbl">Master Plat CTP</div>
                        </div>
                    </div>

                    <div class="offset-metric-item">
                        <div class="offset-metric-icon">
                            <svg viewBox="0 0 24 24">
                                <polyline points="6 9 6 2 18 2 18 9" />
                                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                                <rect x="6" y="14" width="12" height="8" />
                            </svg>
                        </div>
                        <div>
                            <div class="offset-metric-val" id="statValPrintCosts">{{ $totalPrintCosts }}</div>
                            <div class="offset-metric-lbl">Profil Tarif Mesin</div>
                        </div>
                    </div>

                    <div class="offset-metric-item">
                        <div class="offset-metric-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M19 5 5 19" />
                                <circle cx="7" cy="7" r="2" />
                                <circle cx="17" cy="17" r="2" />
                                <path d="M15 5h4v4" />
                            </svg>
                        </div>
                        <div>
                            <div class="offset-metric-val" id="statValPricingRules">{{ $totalPricingRules }}</div>
                            <div class="offset-metric-lbl">Aturan Markup Rim</div>
                        </div>
                    </div>

                    <div class="offset-metric-item">
                        <div class="offset-metric-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z" />
                            </svg>
                        </div>
                        <div>
                            <div class="offset-metric-val" id="statValProducts">{{ $totalProducts ?? 0 }}</div>
                            <div class="offset-metric-lbl">Item Produk Cetak</div>
                        </div>
                    </div>
                </div>

                {{-- TABEL MASTER OFFSET PER TAB (pola DataTable master kategori) --}}
                @php
                    $offsetTabs = [
                        [
                            'section' => 'tabPlano',
                            'prefix' => 'offsetPlano',
                            'active' => true,
                            'icon' => 'bi-file-earmark-text-fill',
                            'title' => 'Daftar Kertas Plano Offset',
                            'desc' => 'Master bahan lembaran plano baku yang digunakan untuk kalkulasi cetak mesin offset.',
                            'add' => 'Tambah Kertas Plano',
                            'modal' => 'modalTambahPlano',
                            'entity' => 'Kertas plano',
                            'search' => 'Cari kode, nama bahan, jenis, atau status...',
                            'columns' => [
                                ['label' => 'Kode', 'class' => 'cat-th-date text-center', 'width' => 130],
                                ['label' => 'Nama Bahan', 'class' => 'cat-th-name', 'width' => 230],
                                ['label' => 'Ukuran Plano (P x L)', 'class' => 'cat-th-date text-center', 'width' => 160],
                                ['label' => 'Gramatur', 'class' => 'cat-th-date text-center', 'width' => 120],
                                ['label' => 'Jenis', 'class' => 'cat-th-date text-center', 'width' => 130],
                                ['label' => 'Harga Satuan Plano', 'class' => 'cat-th-date text-end', 'width' => 160],
                                ['label' => 'Status', 'class' => 'cat-th-date text-center', 'width' => 110],
                            ],
                        ],
                        [
                            'section' => 'tabPlates',
                            'prefix' => 'offsetPlates',
                            'active' => false,
                            'icon' => 'bi-grid-1x2-fill',
                            'title' => 'Daftar Plat CTP Offset',
                            'desc' => 'Master plat cetak CTP per warna untuk cetak offset (CMYK = 4 plat per job).',
                            'add' => 'Tambah Plat CTP',
                            'modal' => 'modalTambahPlate',
                            'entity' => 'Plat CTP',
                            'search' => 'Cari kode, nama plat, mesin, atau status...',
                            'columns' => [
                                ['label' => 'Kode Plat', 'class' => 'cat-th-date text-center', 'width' => 130],
                                ['label' => 'Nama Plat CTP', 'class' => 'cat-th-name', 'width' => 230],
                                ['label' => 'Mesin Terkait', 'class' => 'cat-th-creator', 'width' => 190],
                                ['label' => 'Harga per Plat', 'class' => 'cat-th-date text-end', 'width' => 150],
                                ['label' => 'Status', 'class' => 'cat-th-date text-center', 'width' => 110],
                            ],
                        ],
                        [
                            'section' => 'tabPrintCosts',
                            'prefix' => 'offsetPrintCosts',
                            'active' => false,
                            'icon' => 'bi-printer-fill',
                            'title' => 'Parameter Operasional & Tarif Mesin Offset',
                            'desc' => 'Ukuran lembar potong mesin, toleransi insheet setelan awal, tarif klik per warna, dan ongkos potong.',
                            'add' => 'Tambah Profil Mesin',
                            'modal' => 'modalTambahPrintCost',
                            'entity' => 'Profil mesin',
                            'search' => 'Cari nama profil mesin atau status...',
                            'columns' => [
                                ['label' => 'Nama Profil Mesin', 'class' => 'cat-th-name', 'width' => 230],
                                ['label' => 'Ukuran Cetak Mesin', 'class' => 'cat-th-date text-center', 'width' => 160],
                                ['label' => 'Insheet Setelan', 'class' => 'cat-th-date text-center', 'width' => 140],
                                ['label' => 'Tarif per Klik/Warna', 'class' => 'cat-th-date text-end', 'width' => 160],
                                ['label' => 'Minim per Warna', 'class' => 'cat-th-date text-end', 'width' => 150],
                                ['label' => 'Ongkos Potong / Rim', 'class' => 'cat-th-date text-end', 'width' => 160],
                                ['label' => 'Status', 'class' => 'cat-th-date text-center', 'width' => 110],
                            ],
                        ],
                        [
                            'section' => 'tabPricingRules',
                            'prefix' => 'offsetPricingRules',
                            'active' => false,
                            'icon' => 'bi-percent',
                            'title' => 'Aturan Markup & Harga Jual Offset',
                            'desc' => 'Ketentuan persentase margin keuntungan untuk Harga Jual Umum dan Harga Divisi.',
                            'add' => 'Tambah Pricing Rule',
                            'modal' => 'modalTambahPricingRule',
                            'entity' => 'Pricing rule',
                            'search' => 'Cari nama aturan, kategori, atau status...',
                            'columns' => [
                                ['label' => 'Nama Aturan', 'class' => 'cat-th-name', 'width' => 230],
                                ['label' => 'Kategori', 'class' => 'cat-th-date text-center', 'width' => 150],
                                ['label' => 'Rentang Rim', 'class' => 'cat-th-date text-center', 'width' => 140],
                                ['label' => 'Markup Umum', 'class' => 'cat-th-date text-center', 'width' => 130],
                                ['label' => 'Markup Divisi', 'class' => 'cat-th-date text-center', 'width' => 130],
                                ['label' => 'Pembulatan', 'class' => 'cat-th-date text-end', 'width' => 140],
                                ['label' => 'Status', 'class' => 'cat-th-date text-center', 'width' => 110],
                            ],
                        ],
                        [
                            'section' => 'tabProducts',
                            'prefix' => 'offsetProducts',
                            'active' => false,
                            'icon' => 'bi-journal-richtext',
                            'title' => 'Daftar Produk Cetak / Brosur Offset',
                            'desc' => 'Identitas spesifikasi produk cetak lembaran, ukuran jadi, dan rasio potong (up) per lembar mesin.',
                            'add' => 'Tambah Produk Cetak',
                            'modal' => 'modalTambahProduct',
                            'entity' => 'Produk cetak',
                            'search' => 'Cari kode, nama produk, kategori, atau status...',
                            'columns' => [
                                ['label' => 'Kode', 'class' => 'cat-th-date text-center', 'width' => 130],
                                ['label' => 'Nama Produk', 'class' => 'cat-th-name', 'width' => 250],
                                ['label' => 'Kategori', 'class' => 'cat-th-date text-center', 'width' => 130],
                                ['label' => 'Ukuran Jadi (P x L)', 'class' => 'cat-th-date text-center', 'width' => 150],
                                ['label' => 'Qty / Rim (Kolom F)', 'class' => 'cat-th-date text-center', 'width' => 170],
                                ['label' => 'Plano Yield', 'class' => 'cat-th-date text-center', 'width' => 160],
                                ['label' => 'Lembar Mesin', 'class' => 'cat-th-date text-center', 'width' => 150],
                                ['label' => 'Status', 'class' => 'cat-th-date text-center', 'width' => 110],
                            ],
                        ],
                    ];
                @endphp

                @foreach ($offsetTabs as $tab)
                    @php($p = $tab['prefix'])
                    <div class="offset-table-section {{ $tab['active'] ? 'active' : '' }}" id="{{ $tab['section'] }}">
                        <div class="grid">
                            <section class="col-12 card cat-card-container">
                                <div class="cat-card-header">
                                    <div class="cat-card-header-main">
                                        <div class="cat-card-title-wrap">
                                            <div class="cat-card-icon"><i class="bi {{ $tab['icon'] }}"></i></div>
                                            <div>
                                                <div class="cat-title-row">
                                                    <h3 class="cat-card-title">{{ $tab['title'] }}</h3>
                                                    <span class="cat-badge-total">
                                                        <span class="cat-pulse-dot"></span>
                                                        <span id="{{ $p }}TotalCount">Memuat data...</span>
                                                    </span>
                                                </div>
                                                <p class="cat-card-desc">{{ $tab['desc'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cat-card-actions">
                                        <button type="button" class="cat-btn-head-primary offset-btn-create"
                                            data-open-modal="{{ $tab['modal'] }}">
                                            <i class="bi bi-plus-lg"></i><span>{{ $tab['add'] }}</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="cat-bulk-toolbar" id="{{ $p }}BulkToolbar" style="display:none;">
                                    <div class="cat-bulk-info">
                                        <span class="cat-bulk-badge" id="{{ $p }}BulkCount">0 terpilih</span>
                                        <span class="cat-bulk-text">{{ $tab['entity'] }} terpilih siap untuk tindakan massal</span>
                                    </div>
                                    <div class="cat-bulk-actions">
                                        <button type="button" class="btn btn--ghost btn--sm" id="{{ $p }}CancelSelection">
                                            <i class="bi bi-x-circle me-1"></i>Batal Pilihan
                                        </button>
                                        <button type="button" class="btn btn--danger btn--sm" id="{{ $p }}BulkDelete">
                                            <i class="bi bi-trash3-fill me-1"></i>Hapus Terpilih
                                        </button>
                                    </div>
                                </div>

                                <div class="cat-filter-card">
                                    <div class="cat-filter-group-left">
                                        <div class="cat-search-box">
                                            <i class="bi bi-search cat-search-icon"></i>
                                            <input type="text" id="{{ $p }}Search" class="cat-filter-input"
                                                placeholder="{{ $tab['search'] }}" aria-label="Cari {{ strtolower($tab['entity']) }}">
                                            <button type="button" id="{{ $p }}ClearSearch" class="cat-clear-btn"
                                                title="Hapus pencarian" aria-label="Hapus pencarian" style="display:none;">
                                                <i class="bi bi-x-circle-fill"></i>
                                            </button>
                                        </div>
                                        <div class="cat-select-box">
                                            <i class="bi bi-toggle-on cat-select-icon"></i>
                                            <select id="{{ $p }}Status" class="select2" style="width:100%;"
                                                aria-label="Filter status">
                                                <option value="all">Semua Status</option>
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                            </select>
                                        </div>
                                        <div class="cat-filter-subgroup">
                                            <div class="cat-select-box">
                                                <i class="bi bi-calendar3 cat-select-icon"></i>
                                                <select id="{{ $p }}Period" class="select2" style="width:100%;"
                                                    aria-label="Filter periode">
                                                    <option value="all">Semua Periode</option>
                                                    <option value="today">Hari Ini</option>
                                                    <option value="7_days">7 Hari Terakhir</option>
                                                    <option value="month">Bulan Ini</option>
                                                    <option value="year">Tahun Ini</option>
                                                </select>
                                            </div>
                                            <button type="button" id="{{ $p }}Reset" class="cat-btn-reset"
                                                title="Reset semua filter">
                                                <i class="bi bi-arrow-counterclockwise"></i><span>Reset</span>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="cat-filter-group-right">
                                        <div class="cat-length-box">
                                            <span class="cat-length-label">Baris:</span>
                                            <select id="{{ $p }}PageLength" class="select2" style="width:76px;"
                                                aria-label="Jumlah baris">
                                                <option value="10" selected>10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="cat-table-wrap">
                                    <table id="{{ $p }}Table" class="cat-modern-table" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th class="cat-th-check text-center">
                                                    <label class="cat-custom-checkbox" title="Pilih Semua di Halaman Ini">
                                                        <input type="checkbox" id="{{ $p }}CheckAll"
                                                            aria-label="Pilih semua {{ strtolower($tab['entity']) }} di halaman ini">
                                                        <span class="cat-checkbox-mark">
                                                            <svg class="check-icon" viewBox="0 0 14 14" fill="none">
                                                                <path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#ffffff"
                                                                    stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                            <svg class="minus-icon" viewBox="0 0 14 14" fill="none">
                                                                <path d="M3 7H11" stroke="#ffffff" stroke-width="2.2"
                                                                    stroke-linecap="round" />
                                                            </svg>
                                                        </span>
                                                    </label>
                                                </th>
                                                <th class="cat-th-no text-center" style="width:60px;">No</th>
                                                @foreach ($tab['columns'] as $column)
                                                    <th class="{{ $column['class'] }}" style="min-width:{{ $column['width'] }}px;">{{ $column['label'] }}</th>
                                                @endforeach
                                                <th class="cat-th-actions text-center" style="width:130px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </section>
                        </div>
                    </div>
                @endforeach
            </main>
            <div data-shell-footer></div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 1: TAMBAH KERTAS PLANO                               --}}
    {{-- ========================================================= --}}
    <div class="modal-overlay" id="modalTambahPlano">
        <div class="modal-dialog">
            <form id="formTambahPlano" method="POST" novalidate>
                @csrf
                <div class="modal-header">
                    <div>
                        <span class="eyebrow">MASTER DATA · OFFSET</span>
                        <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 600; color: var(--t-base);">Tambah Kertas
                            Plano</h3>
                    </div>
                    <button type="button" class="modal-close" data-close-modal aria-label="Tutup">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="field-row">
                        <div>
                            <label>Kategori Kertas <span style="color: var(--danger);">*</span></label>
                            <select class="select select2" id="planoCategorySelect" required>
                                <option value="">-- Pilih Kategori Kertas --</option>
                                @if (isset($digitalCategories))
                                    @foreach ($digitalCategories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label>Bahan Material (Digital Print A3+) <span style="color: var(--danger);">*</span></label>
                            <select name="material_id" class="select select2" id="planoMaterialSelect" required disabled>
                                <option value="">-- Pilih Kategori Dahulu --</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Kode Bahan (Opsional / Otomatis)</label>
                            <input type="text" name="material_code" id="planoMaterialCode" class="input"
                                placeholder="Otomatis dari material">
                        </div>
                        <div>
                            <label>Gramatur (gsm)</label>
                            <input type="number" name="gramature" id="planoGramature" class="input"
                                placeholder="Otomatis / Contoh: 150">
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Panjang Plano (cm) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="length" class="input" value="90.00" required>
                        </div>
                        <div>
                            <label>Lebar Plano (cm) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="width" class="input" value="65.00" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Harga Satuan Beli per Lembar Plano (Rp) <span
                                    style="color: var(--danger);">*</span></label>
                            <input type="text" name="price_per_plano" class="input input-rupiah"
                                placeholder="Contoh: Rp 1.900" required>
                        </div>
                        <div>
                            <label>Status <span style="color: var(--danger);">*</span></label>
                            <select name="status" class="select select2" id="planoStatus" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary" data-close-modal>Batal</button>
                    <button type="submit" class="btn btn--primary" id="btnSubmitPlano">Simpan Kertas Plano</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 2: TAMBAH PLAT CTP                                  --}}
    {{-- ========================================================= --}}
    <div class="modal-overlay" id="modalTambahPlate">
        <div class="modal-dialog">
            <form id="formTambahPlate" method="POST" novalidate>
                @csrf
                <div class="modal-header">
                    <div>
                        <span class="eyebrow">MASTER DATA · OFFSET</span>
                        <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 600; color: var(--t-base);">Tambah Plat
                            CTP Offset</h3>
                    </div>
                    <button type="button" class="modal-close" data-close-modal aria-label="Tutup">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="field-full">
                        <label>Nama Plat CTP <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="plate_name" class="input"
                            placeholder="Contoh: Plat CTP Mesin SM 52 / Oliver" required>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Kode Plat (Opsional)</label>
                            <input type="text" name="plate_code" class="input" placeholder="Contoh: PLT-SM52">
                        </div>
                        <div>
                            <label>Mesin Terkait</label>
                            <input type="text" name="machine_name" class="input" value="SM 52 / Oliver 58"
                                placeholder="Contoh: SM 52">
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Harga per Lembar Plat (Rp) <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="price_per_plate" class="input input-rupiah"
                                placeholder="Contoh: Rp 16.000" required>
                        </div>
                        <div>
                            <label>Status <span style="color: var(--danger);">*</span></label>
                            <select name="status" class="select select2" id="plateStatus" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary" data-close-modal>Batal</button>
                    <button type="submit" class="btn btn--primary" id="btnSubmitPlate">Simpan Plat CTP</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 3: TAMBAH PROFIL MESIN & TARIF CETAK                --}}
    {{-- ========================================================= --}}
    <div class="modal-overlay" id="modalTambahPrintCost">
        <div class="modal-dialog">
            <form id="formTambahPrintCost" method="POST" novalidate>
                @csrf
                <div class="modal-header">
                    <div>
                        <span class="eyebrow">MASTER DATA · OFFSET</span>
                        <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 600; color: var(--t-base);">Tambah Profil
                            Mesin & Tarif Cetak</h3>
                    </div>
                    <button type="button" class="modal-close" data-close-modal aria-label="Tutup">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="field-full">
                        <label>Nama Profil Mesin <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="name" class="input"
                            placeholder="Contoh: Standar Mesin Offset SM 52" required>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Lebar Lembar Cetak (cm) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="press_sheet_width" class="input" value="45.00"
                                required>
                        </div>
                        <div>
                            <label>Panjang Lembar Cetak (cm) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="press_sheet_length" class="input"
                                value="32.50" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Insheet Setelan Awal (Lembar) <span style="color: var(--danger);">*</span></label>
                            <input type="number" name="default_insheet_sheets" class="input" value="150" required>
                        </div>
                        <div>
                            <label>Tarif per Klik/Warna (Rp) <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="click_rate_per_color" class="input input-rupiah" value="Rp 15"
                                placeholder="Contoh: Rp 15" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Tarif Minim per Warna (Rp) <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="min_print_per_color" class="input input-rupiah"
                                value="Rp 65.000" placeholder="Contoh: Rp 65.000" required>
                        </div>
                        <div>
                            <label>Ongkos Potong / Rim (Rp) <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="cutting_fee_per_rim" class="input input-rupiah"
                                value="Rp 20.000" placeholder="Contoh: Rp 20.000" required>
                        </div>
                    </div>
                    <div class="field-full">
                        <label>Status <span style="color: var(--danger);">*</span></label>
                        <select name="status" class="select select2" id="printCostStatus" required>
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary" data-close-modal>Batal</button>
                    <button type="submit" class="btn btn--primary" id="btnSubmitPrintCost">Simpan Profil Mesin</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 4: TAMBAH PRICING RULES OFFSET                      --}}
    {{-- ========================================================= --}}
    <div class="modal-overlay" id="modalTambahPricingRule">
        <div class="modal-dialog">
            <form id="formTambahPricingRule" method="POST" novalidate>
                @csrf
                <div class="modal-header">
                    <div>
                        <span class="eyebrow">MASTER DATA · OFFSET</span>
                        <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 600; color: var(--t-base);">Tambah
                            Pricing Rule Offset</h3>
                    </div>
                    <button type="button" class="modal-close" data-close-modal aria-label="Tutup">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="field-row">
                        <div>
                            <label>Nama Aturan <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="rule_name" class="input"
                                placeholder="Contoh: Pricing Rule Brosur Offset" required>
                        </div>
                        <div>
                            <label>Kategori Produk</label>
                            <input type="text" name="category_name" class="input" value="Brosur"
                                placeholder="Contoh: Brosur">
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Minimal Rim <span style="color: var(--danger);">*</span></label>
                            <input type="number" name="min_rim" class="input" value="1" required>
                        </div>
                        <div>
                            <label>Maksimal Rim <span style="color: var(--danger);">*</span></label>
                            <input type="number" name="max_rim" class="input" value="10" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Markup Harga Umum (%) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="markup_general" class="input" value="100.00"
                                required>
                        </div>
                        <div>
                            <label>Markup Harga Divisi (%) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="markup_division" class="input" value="80.00"
                                required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Pembulatan (Rp) <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="rounding_value" class="input input-rupiah" value="Rp 5.000"
                                placeholder="Contoh: Rp 5.000" required>
                        </div>
                        <div>
                            <label>Status <span style="color: var(--danger);">*</span></label>
                            <select name="status" class="select select2" id="pricingRuleStatus" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary" data-close-modal>Batal</button>
                    <button type="submit" class="btn btn--primary" id="btnSubmitPricingRule">Simpan Pricing
                        Rule</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 5: TAMBAH MASTER PRODUK CETAK (BROSUR / FLYER)      --}}
    {{-- ========================================================= --}}
    <div class="modal-overlay" id="modalTambahProduct">
        <div class="modal-dialog">
            <form id="formTambahProduct" method="POST" novalidate>
                @csrf
                <div class="modal-header">
                    <div>
                        <span class="eyebrow">MASTER DATA · OFFSET</span>
                        <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 600; color: var(--t-base);">Tambah
                            Produk Cetak Offset</h3>
                    </div>
                    <button type="button" class="modal-close" data-close-modal aria-label="Tutup">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="field-row">
                        <div>
                            <label>Kode Produk</label>
                            <input type="text" name="product_code" class="input"
                                placeholder="Contoh: BRS-A5 (Kosongkan utk auto)">
                        </div>
                        <div>
                            <label>Nama Produk <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="product_name" class="input"
                                placeholder="Contoh: Brosur A5" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Kategori Produk <span style="color: var(--danger);">*</span></label>
                            <select name="category" class="select select2" id="productCategory" required>
                                <option value="Brosur" selected>Brosur</option>
                                <option value="Flyer">Flyer</option>
                                <option value="Leaflet">Leaflet</option>
                                <option value="Poster">Poster</option>
                                <option value="Kop Surat">Kop Surat</option>
                                <option value="Amplop">Amplop</option>
                                <option value="Kalender">Kalender</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label>Status <span style="color: var(--danger);">*</span></label>
                            <select name="status" class="select select2" id="productStatus" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Panjang Jadi (cm) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="final_length" class="input"
                                placeholder="Contoh: 21.00" required>
                        </div>
                        <div>
                            <label>Lebar Jadi (cm) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="final_width" class="input"
                                placeholder="Contoh: 14.80" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Panjang Lembar Mesin (cm) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="print_sheet_length" class="input" value="45.00"
                                placeholder="Contoh: 45.00" required>
                        </div>
                        <div>
                            <label>Lebar Lembar Mesin (cm) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="print_sheet_width" class="input" value="32.50"
                                placeholder="Contoh: 32.50" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Qty Pokok per Rim (Lembar Cetak) <span style="color: var(--danger);">*</span></label>
                            <input type="number" name="sheet_per_rim" id="inputSheetPerRim" class="input" value="125" min="1"
                                placeholder="Sesuai Kolom F Excel: 125 (A5) atau 250 (A4)" required>
                            <small id="hintSheetPerRim" style="display: block; margin-top: 4px; font-size: 11px; color: var(--primary); font-weight: 600;">
                                Sesuai Kolom F Excel (Setara 4 Up / pcs per lembar)
                            </small>
                            <input type="hidden" name="up_per_sheet" id="hiddenUpPerSheet" value="4">
                        </div>
                        <div>
                            <label>Hasil Potong dari 1 Plano (lbr) <span style="color: var(--danger);">*</span></label>
                            <input type="number" name="plano_cut_yield" class="input" value="4" min="1"
                                placeholder="Contoh: 4" required>
                            <small style="display: block; margin-top: 4px; font-size: 11px; color: var(--t-muted);">
                                1 Plano dipotong jadi 4 lembar mesin (45 × 32.5 cm)
                            </small>
                        </div>
                    </div>
                    <div style="margin-top: 12px;">
                        <label>Keterangan / Catatan Spesifikasi</label>
                        <textarea name="description" class="input" rows="2"
                            placeholder="Catatan layout, spesifikasi standar, dsb. (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary" data-close-modal>Batal</button>
                    <button type="submit" class="btn btn--primary" id="btnSubmitProduct">Simpan Produk Cetak</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 5: KONFIRMASI HAPUS DATA OFFSET                     --}}
    {{-- ========================================================= --}}
    <div class="modal-overlay" id="modalDeleteOffset">
        <div class="modal-dialog modal-delete">
            <div class="modal-header">
                <div>
                    <span class="eyebrow">MASTER DATA · OFFSET</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 600; color: var(--t-base);">Hapus Data Offset
                    </h3>
                </div>
                <button type="button" class="modal-close" data-close-modal aria-label="Tutup">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="modal-body" style="padding: 22px;">
                <div class="delete-confirmation">
                    <div class="delete-icon">
                        <i class="bi bi-trash3-fill"></i>
                    </div>
                    <div class="delete-content">
                        <h3>Konfirmasi Hapus</h3>
                        <p>Apakah Anda yakin ingin menghapus <strong id="deleteTargetName"
                                style="color: var(--t-base);"></strong>? Data yang dihapus tidak dapat dipulihkan kembali.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--ghost" data-close-modal>Batal</button>
                <button type="button" class="btn btn--danger" id="btnConfirmDeleteOffset">
                    <i class="bi bi-trash3-fill"></i>
                    <span>Hapus Data</span>
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL: KONFIRMASI HAPUS MASSAL DATA OFFSET                --}}
    {{-- ========================================================= --}}
    <div class="modal-overlay" id="modalBulkDeleteOffset">
        <div class="modal-dialog modal-delete">
            <div class="modal-header">
                <div>
                    <span class="eyebrow">MASTER DATA · OFFSET</span><br>
                    <span class="eyebrow">Hapus Pilihan</span>
                </div>
                <button type="button" class="modal-close" id="closeBulkDeleteOffset" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="delete-confirmation">
                    <div class="delete-icon"><i class="bi bi-trash3-fill"></i></div>
                    <div class="delete-content">
                        <h3 id="bulkDeleteOffsetTitle">Hapus data terpilih?</h3>
                        <p id="bulkDeleteOffsetDescription"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--ghost" id="cancelBulkDeleteOffset">Batal</button>
                <button type="button" class="btn btn--danger" id="confirmBulkDeleteOffset"
                    data-bulk-url="{{ route('admin_bulk_delete_offset') }}">
                    <i class="bi bi-trash3-fill"></i> Hapus
                </button>
            </div>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 6: DETAIL DATA OFFSET                               --}}
    {{-- ========================================================= --}}
    <div class="modal-overlay" id="modalDetailOffset">
        <div class="modal-dialog" style="max-width: 560px;">
            <div class="modal-header">
                <div>
                    <span class="eyebrow" id="detailEyebrow">DETAIL DATA · OFFSET</span>
                    <h3 id="detailTitle"
                        style="margin: 4px 0 0; font-size: 16px; font-weight: 600; color: var(--t-base);">Detail Data</h3>
                </div>
                <button type="button" class="modal-close" data-close-modal aria-label="Tutup">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                        stroke-width="2">
                        <path d="M18 6L6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <div id="detailContainer" style="display: flex; flex-direction: column; gap: 10px;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--secondary" data-close-modal>Tutup</button>
            </div>
        </div>
    </div>
    {{-- ========================================================= --}}
    {{-- MODAL 7: EDIT KERTAS PLANO                                --}}
    {{-- ========================================================= --}}
    <div class="modal-overlay" id="modalEditPlano">
        <div class="modal-dialog">
            <form id="formEditPlano" method="POST" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editPlanoId">
                <div class="modal-header">
                    <div>
                        <span class="eyebrow">EDIT DATA · OFFSET</span>
                        <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 600; color: var(--t-base);">Edit Kertas
                            Plano</h3>
                    </div>
                    <button type="button" class="modal-close" data-close-modal aria-label="Tutup">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="field-row">
                        <div>
                            <label>Kategori Kertas <span style="color: var(--danger);">*</span></label>
                            <select class="select select2" id="editPlanoCategorySelect" required>
                                <option value="">-- Pilih Kategori Kertas --</option>
                                @if (isset($digitalCategories))
                                    @foreach ($digitalCategories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <label>Bahan Material (Digital Print A3+) <span style="color: var(--danger);">*</span></label>
                            <select name="material_id" class="select select2" id="editPlanoMaterialSelect" required>
                                <option value="">-- Pilih Kategori Dahulu --</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Kode Bahan (Opsional / Otomatis)</label>
                            <input type="text" name="material_code" id="editPlanoMaterialCode" class="input"
                                placeholder="Otomatis dari material">
                        </div>
                        <div>
                            <label>Gramatur (gsm)</label>
                            <input type="number" name="gramature" id="editPlanoGramature" class="input"
                                placeholder="Otomatis / Contoh: 150">
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Panjang Plano (cm) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="length" id="editPlanoLength" class="input"
                                required>
                        </div>
                        <div>
                            <label>Lebar Plano (cm) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="width" id="editPlanoWidth" class="input"
                                required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Harga Satuan Beli per Lembar Plano (Rp) <span
                                    style="color: var(--danger);">*</span></label>
                            <input type="text" name="price_per_plano" id="editPlanoPrice" class="input input-rupiah"
                                placeholder="Contoh: Rp 1.900" required>
                        </div>
                        <div>
                            <label>Status <span style="color: var(--danger);">*</span></label>
                            <select name="status" class="select select2" id="editPlanoStatus" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary" data-close-modal>Batal</button>
                    <button type="submit" class="btn btn--primary" id="btnSubmitEditPlano">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 8: EDIT PLAT CTP                                    --}}
    {{-- ========================================================= --}}
    <div class="modal-overlay" id="modalEditPlate">
        <div class="modal-dialog">
            <form id="formEditPlate" method="POST" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editPlateId">
                <div class="modal-header">
                    <div>
                        <span class="eyebrow">EDIT DATA · OFFSET</span>
                        <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 600; color: var(--t-base);">Edit Plat CTP
                            Offset</h3>
                    </div>
                    <button type="button" class="modal-close" data-close-modal aria-label="Tutup">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="field-full">
                        <label>Nama Plat CTP <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="plate_name" id="editPlateName" class="input" required>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Kode Plat (Opsional)</label>
                            <input type="text" name="plate_code" id="editPlateCode" class="input">
                        </div>
                        <div>
                            <label>Mesin Terkait</label>
                            <input type="text" name="machine_name" id="editPlateMachine" class="input">
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Harga per Lembar Plat (Rp) <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="price_per_plate" id="editPlatePrice" class="input input-rupiah"
                                required>
                        </div>
                        <div>
                            <label>Status <span style="color: var(--danger);">*</span></label>
                            <select name="status" class="select select2" id="editPlateStatus" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary" data-close-modal>Batal</button>
                    <button type="submit" class="btn btn--primary" id="btnSubmitEditPlate">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 9: EDIT PROFIL MESIN & TARIF CETAK                  --}}
    {{-- ========================================================= --}}
    <div class="modal-overlay" id="modalEditPrintCost">
        <div class="modal-dialog">
            <form id="formEditPrintCost" method="POST" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editPrintCostId">
                <div class="modal-header">
                    <div>
                        <span class="eyebrow">EDIT DATA · OFFSET</span>
                        <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 600; color: var(--t-base);">Edit Profil
                            Mesin & Tarif Cetak</h3>
                    </div>
                    <button type="button" class="modal-close" data-close-modal aria-label="Tutup">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="field-full">
                        <label>Nama Profil Mesin <span style="color: var(--danger);">*</span></label>
                        <input type="text" name="name" id="editPrintCostName" class="input" required>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Lebar Lembar Cetak (cm) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="press_sheet_width" id="editPressSheetWidth"
                                class="input" required>
                        </div>
                        <div>
                            <label>Panjang Lembar Cetak (cm) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="press_sheet_length" id="editPressSheetLength"
                                class="input" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Insheet Setelan Awal (Lembar) <span style="color: var(--danger);">*</span></label>
                            <input type="number" name="default_insheet_sheets" id="editDefaultInsheet" class="input"
                                required>
                        </div>
                        <div>
                            <label>Tarif per Klik/Warna (Rp) <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="click_rate_per_color" id="editClickRate"
                                class="input input-rupiah" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Tarif Minim per Warna (Rp) <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="min_print_per_color" id="editMinPrint"
                                class="input input-rupiah" required>
                        </div>
                        <div>
                            <label>Ongkos Potong / Rim (Rp) <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="cutting_fee_per_rim" id="editCuttingFee"
                                class="input input-rupiah" required>
                        </div>
                    </div>
                    <div class="field-full">
                        <label>Status <span style="color: var(--danger);">*</span></label>
                        <select name="status" class="select select2" id="editPrintCostStatus" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary" data-close-modal>Batal</button>
                    <button type="submit" class="btn btn--primary" id="btnSubmitEditPrintCost">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 10: EDIT PRICING RULES OFFSET                       --}}
    {{-- ========================================================= --}}
    <div class="modal-overlay" id="modalEditPricingRule">
        <div class="modal-dialog">
            <form id="formEditPricingRule" method="POST" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editPricingRuleId">
                <div class="modal-header">
                    <div>
                        <span class="eyebrow">EDIT DATA · OFFSET</span>
                        <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 600; color: var(--t-base);">Edit Pricing
                            Rule Offset</h3>
                    </div>
                    <button type="button" class="modal-close" data-close-modal aria-label="Tutup">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="field-row">
                        <div>
                            <label>Nama Aturan <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="rule_name" id="editRuleName" class="input" required>
                        </div>
                        <div>
                            <label>Kategori Produk</label>
                            <input type="text" name="category_name" id="editCategoryName" class="input">
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Minimal Rim <span style="color: var(--danger);">*</span></label>
                            <input type="number" name="min_rim" id="editMinRim" class="input" required>
                        </div>
                        <div>
                            <label>Maksimal Rim <span style="color: var(--danger);">*</span></label>
                            <input type="number" name="max_rim" id="editMaxRim" class="input" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Markup Harga Umum (%) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="markup_general" id="editMarkupGeneral"
                                class="input" required>
                        </div>
                        <div>
                            <label>Markup Harga Divisi (%) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="markup_division" id="editMarkupDivision"
                                class="input" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Pembulatan (Rp) <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="rounding_value" id="editRoundingValue"
                                class="input input-rupiah" required>
                        </div>
                        <div>
                            <label>Status <span style="color: var(--danger);">*</span></label>
                            <select name="status" class="select select2" id="editPricingRuleStatus" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary" data-close-modal>Batal</button>
                    <button type="submit" class="btn btn--primary" id="btnSubmitEditPricingRule">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL 11: EDIT MASTER PRODUK CETAK (BROSUR / FLYER)       --}}
    {{-- ========================================================= --}}
    <div class="modal-overlay" id="modalEditProduct">
        <div class="modal-dialog">
            <form id="formEditProduct" method="POST" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editProductId">
                <div class="modal-header">
                    <div>
                        <span class="eyebrow">EDIT DATA · OFFSET</span>
                        <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 600; color: var(--t-base);">Edit
                            Produk Cetak Offset</h3>
                    </div>
                    <button type="button" class="modal-close" data-close-modal aria-label="Tutup">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 6L6 18M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="modal-body" style="padding: 20px;">
                    <div class="field-row">
                        <div>
                            <label>Kode Produk</label>
                            <input type="text" name="product_code" id="editProductCode" class="input"
                                placeholder="Contoh: BRS-A5">
                        </div>
                        <div>
                            <label>Nama Produk <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="product_name" id="editProductName" class="input"
                                placeholder="Contoh: Brosur A5" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Kategori Produk <span style="color: var(--danger);">*</span></label>
                            <select name="category" class="select select2" id="editProductCategory" required>
                                <option value="Brosur">Brosur</option>
                                <option value="Flyer">Flyer</option>
                                <option value="Leaflet">Leaflet</option>
                                <option value="Poster">Poster</option>
                                <option value="Kop Surat">Kop Surat</option>
                                <option value="Amplop">Amplop</option>
                                <option value="Kalender">Kalender</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label>Status <span style="color: var(--danger);">*</span></label>
                            <select name="status" class="select select2" id="editProductStatus" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Panjang Jadi (cm) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="final_length" id="editFinalLength" class="input"
                                placeholder="Contoh: 21.00" required>
                        </div>
                        <div>
                            <label>Lebar Jadi (cm) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="final_width" id="editFinalWidth" class="input"
                                placeholder="Contoh: 14.80" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Panjang Lembar Mesin (cm) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="print_sheet_length" id="editPrintSheetLength" class="input" value="45.00"
                                placeholder="Contoh: 45.00" required>
                        </div>
                        <div>
                            <label>Lebar Lembar Mesin (cm) <span style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" name="print_sheet_width" id="editPrintSheetWidth" class="input" value="32.50"
                                placeholder="Contoh: 32.50" required>
                        </div>
                    </div>
                    <div class="field-row">
                        <div>
                            <label>Qty Pokok per Rim (Lembar Cetak) <span style="color: var(--danger);">*</span></label>
                            <input type="number" name="sheet_per_rim" id="editSheetPerRim" class="input" value="125" min="1"
                                placeholder="Sesuai Kolom F Excel: 125 (A5) atau 250 (A4)" required>
                            <small id="editHintSheetPerRim" style="display: block; margin-top: 4px; font-size: 11px; color: var(--primary); font-weight: 600;">
                                Sesuai Kolom F Excel (Setara 4 Up / pcs per lembar)
                            </small>
                            <input type="hidden" name="up_per_sheet" id="editHiddenUpPerSheet" value="4">
                        </div>
                        <div>
                            <label>Hasil Potong dari 1 Plano (lbr) <span style="color: var(--danger);">*</span></label>
                            <input type="number" name="plano_cut_yield" id="editPlanoCutYield" class="input" value="4" min="1"
                                placeholder="Contoh: 4" required>
                            <small style="display: block; margin-top: 4px; font-size: 11px; color: var(--t-muted);">
                                1 Plano dipotong jadi 4 lembar mesin (45 × 32.5 cm)
                            </small>
                        </div>
                    </div>
                    <div style="margin-top: 12px;">
                        <label>Keterangan / Catatan Spesifikasi</label>
                        <textarea name="description" id="editDescription" class="input" rows="2"
                            placeholder="Catatan layout, spesifikasi standar, dsb. (opsional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary" data-close-modal>Batal</button>
                    <button type="submit" class="btn btn--primary" id="btnUpdateProduct">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Helper rupiah format
            function formatRupiah(num) {
                if (num === null || num === undefined) return 'Rp 0';
                return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Live format Rupiah saat user mengetik di input dengan class 'input-rupiah'
            $(document).on('input', '.input-rupiah', function() {
                let val = $(this).val();
                let clean = val.replace(/\D/g, '');
                if (clean === '') {
                    $(this).val('');
                } else {
                    $(this).val('Rp ' + clean.replace(/\B(?=(\d{3})+(?!\d))/g, '.'));
                }
            });

            // Helper untuk membersihkan format rupiah (hanya menyisakan angka murni) saat submit form
            function serializeFormWithCleanRupiah($form) {
                let formData = $form.serializeArray();
                formData.forEach(item => {
                    let $el = $form.find(`[name="${item.name}"]`);
                    if ($el.hasClass('input-rupiah')) {
                        item.value = item.value.replace(/\D/g, '');
                    }
                });
                return $.param(formData);
            }

            // Data Master Material Digital Print A3+ dari backend
            const digitalMaterials = @json($digitalMaterials ?? []);

            // Cascading Dropdown: Filter Material berdasarkan Kategori Digital Print A3+
            $('#planoCategorySelect').on('change', function() {
                const catId = $(this).val();
                const $matSelect = $('#planoMaterialSelect');
                $matSelect.empty();

                if (!catId) {
                    $matSelect.append('<option value="">-- Pilih Kategori Dahulu --</option>');
                    $matSelect.prop('disabled', true).trigger('change');
                    $('#planoMaterialCode').val('');
                    $('#planoGramature').val('');
                    return;
                }

                const filtered = digitalMaterials.filter(m => String(m.category_id) === String(catId));

                if (filtered.length === 0) {
                    $matSelect.append(
                        '<option value="">-- Tidak ada material aktif di kategori ini --</option>');
                    $matSelect.prop('disabled', true).trigger('change');
                } else {
                    $matSelect.append('<option value="">-- Pilih Material Bahan --</option>');
                    filtered.forEach(m => {
                        $matSelect.append(`<option value="${m.id}">${m.material_name}</option>`);
                    });
                    $matSelect.prop('disabled', false).trigger('change');
                }
            });

            // Auto-fill Kode dan Gramatur saat Material dipilih
            $('#planoMaterialSelect').on('change', function() {
                const matId = $(this).val();
                if (!matId) {
                    $('#planoMaterialCode').val('');
                    $('#planoGramature').val('');
                    return;
                }

                const selected = digitalMaterials.find(m => String(m.id) === String(matId));
                if (selected) {
                    if (selected.material_code) {
                        $('#planoMaterialCode').val(selected.material_code);
                    } else {
                        $('#planoMaterialCode').val('');
                    }

                    const match = selected.material_name.match(/(\d+)\s*(?:gsm|gr|gram)/i);
                    if (match && match[1]) {
                        $('#planoGramature').val(match[1]);
                    } else {
                        $('#planoGramature').val('');
                    }
                }
            });

            // Cascading Dropdown Edit: Filter Material berdasarkan Kategori Digital Print A3+
            $('#editPlanoCategorySelect').on('change', function() {
                const catId = $(this).val();
                const $matSelect = $('#editPlanoMaterialSelect');
                const prevVal = $matSelect.val();
                $matSelect.empty();

                if (!catId) {
                    $matSelect.append('<option value="">-- Pilih Kategori Dahulu --</option>');
                    $matSelect.prop('disabled', true).trigger('change');
                    return;
                }

                const filtered = digitalMaterials.filter(m => String(m.category_id) === String(catId));

                if (filtered.length === 0) {
                    $matSelect.append(
                        '<option value="">-- Tidak ada material aktif di kategori ini --</option>');
                    $matSelect.prop('disabled', true).trigger('change');
                } else {
                    $matSelect.append('<option value="">-- Pilih Material Bahan --</option>');
                    filtered.forEach(m => {
                        $matSelect.append(`<option value="${m.id}">${m.material_name}</option>`);
                    });
                    $matSelect.prop('disabled', false);
                    if (prevVal) {
                        $matSelect.val(prevVal);
                    }
                    $matSelect.trigger('change');
                }
            });

            // Auto-fill Kode dan Gramatur saat Material dipilih pada Modal Edit
            $('#editPlanoMaterialSelect').on('change', function() {
                const matId = $(this).val();
                if (!matId) return;

                const selected = digitalMaterials.find(m => String(m.id) === String(matId));
                if (selected) {
                    if (selected.material_code) {
                        $('#editPlanoMaterialCode').val(selected.material_code);
                    }

                    const match = selected.material_name.match(/(\d+)\s*(?:gsm|gr|gram)/i);
                    if (match && match[1]) {
                        $('#editPlanoGramature').val(match[1]);
                    }
                }
            });

            // Inisialisasi Select2 pada masing-masing modal dengan dropdownParent
            function initModalSelect2() {
                if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                    // Modal Tambah
                    $('#modalTambahPlano .select2').select2({
                        width: '100%',
                        dropdownParent: $('#modalTambahPlano')
                    });

                    $('#modalTambahPlate .select2').select2({
                        width: '100%',
                        dropdownParent: $('#modalTambahPlate')
                    });

                    $('#modalTambahPrintCost .select2').select2({
                        width: '100%',
                        dropdownParent: $('#modalTambahPrintCost')
                    });

                    $('#modalTambahPricingRule .select2').select2({
                        width: '100%',
                        dropdownParent: $('#modalTambahPricingRule')
                    });

                    $('#productCategory').select2({
                        width: '100%',
                        dropdownParent: $('#modalTambahProduct'),
                        tags: true
                    });

                    $('#productStatus').select2({
                        width: '100%',
                        dropdownParent: $('#modalTambahProduct')
                    });

                    // Modal Edit
                    $('#modalEditPlano .select2').select2({
                        width: '100%',
                        dropdownParent: $('#modalEditPlano')
                    });

                    $('#modalEditPlate .select2').select2({
                        width: '100%',
                        dropdownParent: $('#modalEditPlate')
                    });

                    $('#modalEditPrintCost .select2').select2({
                        width: '100%',
                        dropdownParent: $('#modalEditPrintCost')
                    });

                    $('#modalEditPricingRule .select2').select2({
                        width: '100%',
                        dropdownParent: $('#modalEditPricingRule')
                    });

                    $('#editProductCategory').select2({
                        width: '100%',
                        dropdownParent: $('#modalEditProduct'),
                        tags: true
                    });

                    $('#editProductStatus').select2({
                        width: '100%',
                        dropdownParent: $('#modalEditProduct')
                    });
                }
            }

            initModalSelect2();

            // Tab Switcher and Dynamic Action Button
            const tabButtons = document.querySelectorAll('.offset-tab-btn');
            const tabSections = document.querySelectorAll('.offset-table-section');
            const btnMainCreate = document.getElementById('btnMainCreate');
            const labelMainCreate = document.getElementById('labelMainCreate');

            let currentModalId = 'modalTambahPlano';

            tabButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const createLabel = this.getAttribute('data-create-label');
                    currentModalId = this.getAttribute('data-modal');

                    tabButtons.forEach(b => b.classList.remove('active'));
                    tabSections.forEach(s => s.classList.remove('active'));

                    this.classList.add('active');
                    const targetSection = document.getElementById(targetId);
                    if (targetSection) {
                        targetSection.classList.add('active');
                    }

                    if (labelMainCreate) {
                        labelMainCreate.textContent = createLabel || 'Tambah Data';
                    }

                    // Sesuaikan lebar kolom DataTable saat tab berganti
                    setTimeout(() => {
                        $.fn.dataTable.tables({
                            visible: true,
                            api: true
                        }).columns.adjust();
                    }, 50);
                });
            });

            function openCreateModal(modalId) {
                const targetModal = document.getElementById(modalId);
                if (targetModal) {
                    targetModal.classList.add('is-open');
                    initModalSelect2();
                }
            }

            // Open Active Modal
            if (btnMainCreate) {
                btnMainCreate.addEventListener('click', function() {
                    openCreateModal(currentModalId);
                });
            }

            document.querySelectorAll('.offset-btn-create[data-open-modal]').forEach(btn => {
                btn.addEventListener('click', function() {
                    openCreateModal(this.getAttribute('data-open-modal'));
                });
            });

            // Close Modals
            document.querySelectorAll('[data-close-modal]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const overlay = this.closest('.modal-overlay');
                    if (overlay) {
                        overlay.classList.remove('is-open');
                    }
                });
            });

            // Close on overlay click
            document.querySelectorAll('.modal-overlay').forEach(overlay => {
                overlay.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.remove('is-open');
                    }
                });
            });

            // Close on ESC key press
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' || e.keyCode === 27) {
                    $('.modal-overlay.is-open').removeClass('is-open');
                }
            });

            // Helper baris detail (key-value)
            function makeDetailRow(label, value) {
                return `
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 9px 12px; background: var(--bg-hover, rgba(0,0,0,0.02)); border: 1px solid var(--border); border-radius: 8px;">
                        <span style="font-size: 13px; color: var(--t-muted); font-weight: 500;">${label}</span>
                        <span style="font-size: 13px; color: var(--t-base); font-weight: 600; text-align: right;">${value}</span>
                    </div>
                `;
            }

            // Function to populate and display Detail Modal
            function renderDetailModal(type, data) {
                const $container = $('#detailContainer');
                $container.empty();

                const isAct = (data.status || '').toLowerCase() === 'active';
                const statusBadge =
                    `<span class="${isAct ? 'badge-active' : 'badge-inactive'}">${data.status || 'Active'}</span>`;

                if (type === 'plano') {
                    $('#detailEyebrow').text('DETAIL · KERTAS PLANO');
                    $('#detailTitle').text(data.material_name || 'Detail Kertas Plano');

                    $container.append(makeDetailRow('Nama Bahan Kertas', data.material_name || '-'));
                    $container.append(makeDetailRow('Kode Bahan',
                        `<code style="padding: 2px 6px; background: var(--bg-card); border: 1px solid var(--border); border-radius: 4px;">${data.material_code || '-'}</code>`
                    ));
                    $container.append(makeDetailRow('Jenis / Kategori Kertas', data.paper_type || '-'));
                    $container.append(makeDetailRow('Ukuran Plano (P × L)',
                        `${data.length || '-'} × ${data.width || '-'} cm`));
                    $container.append(makeDetailRow('Gramatur', data.gramature ? `${data.gramature} gsm` : '-'));
                    $container.append(makeDetailRow('Harga Satuan per Plano',
                        `<span style="color: var(--primary); font-weight: 700;">${formatRupiah(data.price_per_plano)}</span>`
                    ));
                    $container.append(makeDetailRow('Status Bahan', statusBadge));
                    if (data.created_by || data.created_at) {
                        $container.append(makeDetailRow('Dibuat Oleh',
                            `${data.created_by || 'System'} (${data.created_at ? data.created_at.substring(0, 10) : '-'})`
                        ));
                    }
                } else if (type === 'plate') {
                    $('#detailEyebrow').text('DETAIL · PLAT CTP');
                    $('#detailTitle').text(data.plate_name || 'Detail Plat CTP');

                    $container.append(makeDetailRow('Nama Plat CTP', data.plate_name || '-'));
                    $container.append(makeDetailRow('Kode Plat',
                        `<code style="padding: 2px 6px; background: var(--bg-card); border: 1px solid var(--border); border-radius: 4px;">${data.plate_code || '-'}</code>`
                    ));
                    $container.append(makeDetailRow('Mesin Terkait', data.machine_name || 'SM 52 / Oliver 58'));
                    $container.append(makeDetailRow('Harga Plat (1 Warna)',
                        `<span style="color: var(--primary); font-weight: 700;">${formatRupiah(data.price_per_plate)}</span>`
                    ));
                    $container.append(makeDetailRow('Estimasi 4 Plat (CMYK)',
                        `<span style="color: var(--success, #10b981); font-weight: 700;">${formatRupiah(data.price_per_plate * 4)}</span> <span style="font-size: 11px; color: var(--t-muted);">(4 Plat per Job)</span>`
                    ));
                    $container.append(makeDetailRow('Status Plat', statusBadge));
                    if (data.created_by || data.created_at) {
                        $container.append(makeDetailRow('Dibuat Oleh',
                            `${data.created_by || 'System'} (${data.created_at ? data.created_at.substring(0, 10) : '-'})`
                        ));
                    }
                } else if (type === 'print_cost') {
                    $('#detailEyebrow').text('DETAIL · PROFIL MESIN OFFSET');
                    $('#detailTitle').text(data.name || 'Detail Mesin Offset');

                    $container.append(makeDetailRow('Nama Profil Mesin', data.name || '-'));
                    $container.append(makeDetailRow('Ukuran Lembar Masuk Mesin',
                        `${data.press_sheet_width || '-'} × ${data.press_sheet_length || '-'} cm`));
                    $container.append(makeDetailRow('Insheet Setelan Awal',
                        `${data.default_insheet_sheets || 0} Lembar`));
                    $container.append(makeDetailRow('Tarif per Klik / Warna',
                        `<span style="color: var(--primary); font-weight: 700;">${formatRupiah(data.click_rate_per_color)}</span>`
                    ));
                    $container.append(makeDetailRow('Tarif Minim per Warna',
                        `<span style="color: var(--primary); font-weight: 700;">${formatRupiah(data.min_print_per_color)}</span>`
                    ));
                    $container.append(makeDetailRow('Ongkos Potong per Rim',
                        `<span style="color: var(--primary); font-weight: 700;">${formatRupiah(data.cutting_fee_per_rim)}</span>`
                    ));
                    $container.append(makeDetailRow('Status Mesin', statusBadge));
                    if (data.created_by || data.created_at) {
                        $container.append(makeDetailRow('Dibuat Oleh',
                            `${data.created_by || 'System'} (${data.created_at ? data.created_at.substring(0, 10) : '-'})`
                        ));
                    }
                } else if (type === 'pricing_rule') {
                    $('#detailEyebrow').text('DETAIL · ATURAN PRICING OFFSET');
                    $('#detailTitle').text(data.rule_name || 'Detail Pricing Rule');

                    $container.append(makeDetailRow('Nama Aturan Markup', data.rule_name || '-'));
                    $container.append(makeDetailRow('Kategori Produk', data.category_name || 'Brosur'));
                    $container.append(makeDetailRow('Rentang Volume Order',
                        `${data.min_rim || 1} s.d. ${data.max_rim || 10} Rim`));
                    $container.append(makeDetailRow('Markup Harga Umum',
                        `<span style="color: var(--primary); font-weight: 700;">${data.markup_general}%</span>`
                    ));
                    $container.append(makeDetailRow('Markup Harga Divisi',
                        `<span style="color: var(--success, #10b981); font-weight: 700;">${data.markup_division}%</span>`
                    ));
                    $container.append(makeDetailRow('Nilai Pembulatan',
                        `<span style="font-weight: 700;">${formatRupiah(data.rounding_value)}</span> (Kelipatan Ke Atas)`
                    ));
                    $container.append(makeDetailRow('Status Aturan', statusBadge));
                    if (data.created_by || data.created_at) {
                        $container.append(makeDetailRow('Dibuat Oleh',
                            `${data.created_by || 'System'} (${data.created_at ? data.created_at.substring(0, 10) : '-'})`
                        ));
                    }
                } else if (type === 'product') {
                    $('#detailEyebrow').text('DETAIL · PRODUK CETAK');
                    $('#detailTitle').text(data.product_name || 'Detail Produk Cetak');

                    $container.append(makeDetailRow('Nama Produk', data.product_name || '-'));
                    $container.append(makeDetailRow('Kode Produk',
                        `<code style="padding: 2px 6px; background: var(--bg-card); border: 1px solid var(--border); border-radius: 4px;">${data.product_code || '-'}</code>`
                    ));
                    $container.append(makeDetailRow('Kategori Produk', data.category || 'Brosur'));
                    $container.append(makeDetailRow('Ukuran Jadi (P × L)',
                        `${data.final_length || '-'} × ${data.final_width || '-'} cm`));
                    $container.append(makeDetailRow('Ukuran Lembar Masuk Mesin',
                        `${data.print_sheet_length || '-'} × ${data.print_sheet_width || '-'} cm`));
                    $container.append(makeDetailRow('Qty Pokok per Rim (Kolom F)',
                        `<span style="color: var(--primary); font-weight: 700;">${data.sheet_per_rim || Math.round(500 / (data.up_per_sheet || 1))} Lembar Cetak</span> <span style="font-size: 12px; color: var(--t-muted);">(${data.up_per_sheet || 1} pcs per lembar)</span>`));
                    $container.append(makeDetailRow('Hasil Potong dari 1 Plano',
                        `${data.plano_cut_yield || 4} Lembar Mesin / Plano`));
                    if (data.description) {
                        $container.append(makeDetailRow('Keterangan', data.description));
                    }
                    $container.append(makeDetailRow('Status Produk', statusBadge));
                    if (data.created_by || data.created_at) {
                        $container.append(makeDetailRow('Dibuat Oleh',
                            `${data.created_by || 'System'} (${data.created_at ? data.created_at.substring(0, 10) : '-'})`
                        ));
                    }
                }
            }

            // Event Delegation for Detail Button Click
            $(document).on('click', '.btn-detail-offset', function() {
                const type = $(this).data('type');
                const $tr = $(this).closest('tr');
                let rowData = null;

                if (type === 'plano') {
                    rowData = tablePlano.row($tr.hasClass('child') ? $tr.prev() : $tr).data();
                } else if (type === 'plate') {
                    rowData = tablePlates.row($tr.hasClass('child') ? $tr.prev() : $tr).data();
                } else if (type === 'print_cost') {
                    rowData = tablePrintCosts.row($tr.hasClass('child') ? $tr.prev() : $tr).data();
                } else if (type === 'pricing_rule') {
                    rowData = tablePricingRules.row($tr.hasClass('child') ? $tr.prev() : $tr).data();
                } else if (type === 'product') {
                    rowData = tableProducts.row($tr.hasClass('child') ? $tr.prev() : $tr).data();
                }

                if (rowData) {
                    renderDetailModal(type, rowData);
                    $('#modalDetailOffset').addClass('is-open');
                }
            });

            // Global Delete Object Tracker
            let itemToDelete = {
                id: null,
                type: null,
                name: null
            };

            // Event Delegation for Delete Button Click
            $(document).on('click', '.btn-delete-offset', function() {
                itemToDelete.id = $(this).data('id');
                itemToDelete.type = $(this).data('type');
                itemToDelete.name = $(this).data('name');

                $('#deleteTargetName').text(itemToDelete.name || 'data ini');
                $('#modalDeleteOffset').addClass('is-open');
            });

            // Delete Confirm Click
            $('#btnConfirmDeleteOffset').on('click', function() {
                if (!itemToDelete.id || !itemToDelete.type) return;

                const $btn = $(this);
                $btn.prop('disabled', true).find('span').text('Menghapus...');

                let deleteUrl = '';
                if (itemToDelete.type === 'plano') {
                    deleteUrl = "{{ url('/admin/offset-master/delete-plano') }}/" + itemToDelete.id;
                } else if (itemToDelete.type === 'plate') {
                    deleteUrl = "{{ url('/admin/offset-master/delete-plate') }}/" + itemToDelete.id;
                } else if (itemToDelete.type === 'print_cost') {
                    deleteUrl = "{{ url('/admin/offset-master/delete-print-cost') }}/" + itemToDelete.id;
                } else if (itemToDelete.type === 'pricing_rule') {
                    deleteUrl = "{{ url('/admin/offset-master/delete-pricing-rule') }}/" + itemToDelete.id;
                } else if (itemToDelete.type === 'product') {
                    deleteUrl = "{{ url('/admin/offset-master/delete-product') }}/" + itemToDelete.id;
                }

                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        $('#modalDeleteOffset').removeClass('is-open');
                        if (offsetSelections[itemToDelete.type]) {
                            offsetSelections[itemToDelete.type].delete(Number(itemToDelete.id));
                        }
                        if (typeof showToast === 'function') {
                            showToast('success', res.message || 'Data berhasil dihapus!');
                        }

                        // Reload table and decrement counters
                        if (itemToDelete.type === 'plano') {
                            tablePlano.ajax.reload(null, false);
                            const cur = Math.max(0, (parseInt($('#badgeCountPlano').text()) ||
                                1) - 1);
                            $('#badgeCountPlano').text(cur);
                            $('#statValPlano').text(cur);
                        } else if (itemToDelete.type === 'plate') {
                            tablePlates.ajax.reload(null, false);
                            const cur = Math.max(0, (parseInt($('#badgeCountPlates').text()) ||
                                1) - 1);
                            $('#badgeCountPlates').text(cur);
                            $('#statValPlates').text(cur);
                        } else if (itemToDelete.type === 'print_cost') {
                            tablePrintCosts.ajax.reload(null, false);
                            const cur = Math.max(0, (parseInt($('#badgeCountPrintCosts')
                                .text()) || 1) - 1);
                            $('#badgeCountPrintCosts').text(cur);
                            $('#statValPrintCosts').text(cur);
                        } else if (itemToDelete.type === 'pricing_rule') {
                            tablePricingRules.ajax.reload(null, false);
                            const cur = Math.max(0, (parseInt($('#badgeCountPricingRules')
                                .text()) || 1) - 1);
                            $('#badgeCountPricingRules').text(cur);
                            $('#statValPricingRules').text(cur);
                        } else if (itemToDelete.type === 'product') {
                            tableProducts.ajax.reload(null, false);
                            const cur = Math.max(0, (parseInt($('#badgeCountProducts')
                                .text()) || 1) - 1);
                            $('#badgeCountProducts').text(cur);
                            $('#statValProducts').text(cur);
                        }
                    },
                    error: function(xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr
                            .responseJSON.message : 'Gagal menghapus data.';
                        if (typeof showToast === 'function') {
                            showToast('error', msg);
                        } else {
                            alert(msg);
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).find('span').text('Hapus Data');
                    }
                });
            });

            /*
            |--------------------------------------------------------------------------
            | DATATABLE MASTER OFFSET (pola master kategori)
            |--------------------------------------------------------------------------
            */
            const escapeHtml = DataTable.render.text().display;
            const offsetMaxSelected = 100;
            const offsetSelections = {};
            const offsetCheckMark = `
                <span class="cat-checkbox-mark">
                    <svg class="check-icon" viewBox="0 0 14 14" fill="none">
                        <path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>`;

            function offsetNumber(value) {
                const number = Number(value);
                if (value === null || value === undefined || value === '' || !Number.isFinite(number)) {
                    return '-';
                }
                return number.toLocaleString('id-ID', {
                    maximumFractionDigits: 2
                });
            }

            function offsetChip(icon, text) {
                return `
                    <span class="cat-date-chip">
                        <i class="bi ${icon}"></i>
                        <span>${escapeHtml(String(text))}</span>
                    </span>
                `;
            }

            function offsetNameBox(icon, primary, badge, note) {
                const noteHtml = note ?
                    `<span style="font-size: 11px; color: var(--t-muted); max-width: 260px; white-space: normal;">${escapeHtml(note)}</span>` :
                    '';
                return `
                    <div class="cat-cell-name-box">
                        <div class="cat-icon-symbol"><i class="bi ${icon}"></i></div>
                        <div class="cat-name-details">
                            <span class="cat-name-primary">${escapeHtml(primary || '-')}</span>
                            <span class="cat-name-badge">${escapeHtml(badge)}</span>
                            ${noteHtml}
                        </div>
                    </div>
                `;
            }

            function offsetMoney(data, type) {
                if (type !== 'display') {
                    return Number(data) || 0;
                }
                return `<span style="font-weight: 600; color: var(--t-base);">${escapeHtml(formatRupiah(data))}</span>`;
            }

            function offsetStatus(data, type) {
                const status = (data || 'active').toString().toLowerCase();
                if (type !== 'display') {
                    return status;
                }
                const isActive = status === 'active';
                const label = isActive ? 'Active' : (status === 'inactive' ? 'Inactive' : data);
                return `<span class="badge ${isActive ? 'success' : 'danger'} dot">${escapeHtml(label)}</span>`;
            }

            function createOffsetTable(cfg) {
                const p = cfg.prefix;
                const tableElement = document.getElementById(`${p}Table`);
                const tableWrap = tableElement.closest('.cat-table-wrap');
                const searchInput = document.getElementById(`${p}Search`);
                const clearSearchButton = document.getElementById(`${p}ClearSearch`);
                const statusFilter = document.getElementById(`${p}Status`);
                const periodFilter = document.getElementById(`${p}Period`);
                const pageLength = document.getElementById(`${p}PageLength`);
                const bulkToolbar = document.getElementById(`${p}BulkToolbar`);
                const bulkCount = document.getElementById(`${p}BulkCount`);
                const checkAll = document.getElementById(`${p}CheckAll`);
                const checkAllLabel = checkAll.closest('.cat-custom-checkbox');
                const rowCheckClass = `${p}-row-check`;
                const selected = new Set();
                offsetSelections[cfg.type] = selected;

                function updateSelectionUI() {
                    bulkToolbar.style.display = selected.size ? 'flex' : 'none';
                    bulkCount.textContent = `${selected.size} terpilih`;

                    const checkboxes = tableElement.querySelectorAll(`.${rowCheckClass}`);
                    let checkedCount = 0;
                    checkboxes.forEach((checkbox) => {
                        const isSelected = selected.has(Number(checkbox.value));
                        checkbox.checked = isSelected;
                        checkbox.closest('tr')?.classList.toggle('is-selected', isSelected);
                        if (isSelected) checkedCount++;
                    });

                    checkAll.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
                    checkAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
                    checkAllLabel.classList.toggle('is-indeterminate', checkAll.indeterminate);
                }

                function centerProcessingIndicator() {
                    const indicator = document.getElementById(`${tableElement.id}_processing`);
                    if (indicator && indicator.parentElement !== tableWrap) {
                        indicator.classList.add('cat-processing-centered');
                        tableWrap.appendChild(indicator);
                    }
                }

                $(`#${p}Status, #${p}Period`).select2({
                    width: '100%',
                    minimumResultsForSearch: Infinity
                });
                $(`#${p}PageLength`).select2({
                    width: '76px',
                    minimumResultsForSearch: -1
                });

                $(tableElement).on('processing.dt', centerProcessingIndicator);

                const table = new DataTable(tableElement, {
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    order: cfg.defaultOrder,
                    ajax: {
                        url: cfg.url,
                        type: 'GET',
                        data: function(d) {
                            d.status = statusFilter.value === 'all' ? '' : statusFilter.value;
                            d.period = periodFilter.value === 'all' ? '' : periodFilter.value;
                        }
                    },
                    columns: [{
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-center cat-td-check',
                            render: function(data, type, row) {
                                const id = Number(row.id);
                                return `
                                    <label class="cat-custom-checkbox">
                                        <input type="checkbox" class="${rowCheckClass}" value="${id}"
                                            ${selected.has(id) ? 'checked' : ''}
                                            aria-label="Pilih ${escapeHtml(cfg.entity)} ${escapeHtml(row[cfg.nameField] || '')}">
                                        ${offsetCheckMark}
                                    </label>
                                `;
                            }
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-center cat-td-no',
                            render: function(data, type, row, meta) {
                                return `<span class="cat-num-pill">${meta.settings._iDisplayStart + meta.row + 1}</span>`;
                            }
                        },
                        ...cfg.columns,
                        {
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            className: 'text-center cat-td-actions',
                            render: function(data, type, row) {
                                const id = Number(data);
                                const name = escapeHtml(row[cfg.nameField] || '');
                                return `
                                    <div class="cat-action-group">
                                        <button type="button" class="cat-btn-action cat-btn-view btn-detail-offset" data-type="${cfg.type}"
                                            aria-label="View" title="Lihat detail ${escapeHtml(cfg.entity)}">
                                            <i class="bi bi-arrows-fullscreen"></i>
                                        </button>
                                        <button type="button" class="cat-btn-action cat-btn-edit btn-edit-offset" data-type="${cfg.type}"
                                            aria-label="Edit" title="Edit ${escapeHtml(cfg.entity)}">
                                            <i class="bi bi-pen"></i>
                                        </button>
                                        <button type="button" class="cat-btn-action cat-btn-del btn-delete-offset" data-id="${id}"
                                            data-type="${cfg.type}" data-name="${name}" aria-label="Hapus" title="Hapus ${escapeHtml(cfg.entity)}">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </div>
                                `;
                            }
                        }
                    ],
                    pageLength: 10,
                    layout: {
                        topStart: null,
                        topEnd: null,
                        bottomStart: 'info',
                        bottomEnd: 'paging'
                    },
                    language: {
                        info: `Menampilkan _START_ sampai _END_ dari _TOTAL_ ${cfg.entity}`,
                        infoEmpty: `Menampilkan 0 sampai 0 dari 0 ${cfg.entity}`,
                        zeroRecords: `Data ${cfg.entity} tidak ditemukan`,
                        processing: 'Memuat data...'
                    },
                    drawCallback: function() {
                        centerProcessingIndicator();
                    }
                });
                centerProcessingIndicator();

                table.on('draw', function() {
                    const total = table.page.info().recordsTotal;
                    const totalEl = document.getElementById(`${p}TotalCount`);
                    if (totalEl) {
                        totalEl.textContent = `${total.toLocaleString('id-ID')} Total ${cfg.totalLabel}`;
                    }
                    $(`#${cfg.badgeId}`).text(total);
                    $(`#${cfg.statId}`).text(total);
                    updateSelectionUI();
                });

                let searchTimer;
                searchInput.addEventListener('input', function() {
                    const query = searchInput.value.trim();
                    clearSearchButton.style.display = query.length > 0 ? 'flex' : 'none';
                    clearTimeout(searchTimer);
                    searchTimer = setTimeout(() => table.search(query).draw(), 250);
                });
                clearSearchButton.addEventListener('click', function() {
                    clearTimeout(searchTimer);
                    searchInput.value = '';
                    clearSearchButton.style.display = 'none';
                    table.search('').draw();
                    searchInput.focus();
                });
                $(`#${p}Status, #${p}Period`).on('change', function() {
                    table.ajax.reload(null, true);
                });
                $(pageLength).on('change', function() {
                    table.page.len(Number(pageLength.value)).draw();
                });
                document.getElementById(`${p}Reset`).addEventListener('click', function() {
                    clearTimeout(searchTimer);
                    searchInput.value = '';
                    clearSearchButton.style.display = 'none';
                    $(`#${p}Status, #${p}Period`).val('all').trigger('change.select2');
                    $(pageLength).val('10').trigger('change.select2');
                    selected.clear();
                    table.search('').order(cfg.defaultOrder).page.len(10).ajax.reload(null, true);
                });

                $(tableElement).on('change', `.${rowCheckClass}`, function() {
                    const id = Number(this.value);
                    if (this.checked && !selected.has(id) && selected.size >= offsetMaxSelected) {
                        this.checked = false;
                        showToast('error', `Maksimal ${offsetMaxSelected} ${cfg.entity} dapat dipilih sekaligus.`);
                        return;
                    }
                    if (this.checked) selected.add(id);
                    else selected.delete(id);
                    updateSelectionUI();
                });
                checkAll.addEventListener('change', function() {
                    let reachedLimit = false;
                    tableElement.querySelectorAll(`.${rowCheckClass}`).forEach((checkbox) => {
                        const id = Number(checkbox.value);
                        if (!checkAll.checked) {
                            selected.delete(id);
                        } else if (!selected.has(id)) {
                            if (selected.size >= offsetMaxSelected) reachedLimit = true;
                            else selected.add(id);
                        }
                    });
                    updateSelectionUI();
                    if (reachedLimit) {
                        showToast('error', `Maksimal ${offsetMaxSelected} ${cfg.entity} dapat dipilih sekaligus.`);
                    }
                });
                document.getElementById(`${p}CancelSelection`).addEventListener('click', function() {
                    selected.clear();
                    updateSelectionUI();
                });
                document.getElementById(`${p}BulkDelete`).addEventListener('click', function() {
                    openOffsetBulkDelete({
                        type: cfg.type,
                        entity: cfg.entity,
                        table: table,
                        selected: selected,
                        refresh: updateSelectionUI
                    });
                });

                return table;
            }

            /*
            |--------------------------------------------------------------------------
            | BULK DELETE MASTER OFFSET
            |--------------------------------------------------------------------------
            */
            const offsetBulkModal = document.getElementById('modalBulkDeleteOffset');
            const confirmOffsetBulkButton = document.getElementById('confirmBulkDeleteOffset');
            let activeOffsetBulk = null;
            let isOffsetBulkDeleting = false;

            function closeOffsetBulkModal() {
                if (!isOffsetBulkDeleting) offsetBulkModal.classList.remove('is-open');
            }

            function openOffsetBulkDelete(context) {
                const count = context.selected.size;
                if (!count) return;
                activeOffsetBulk = context;
                document.getElementById('bulkDeleteOffsetTitle').textContent = `Hapus ${context.entity} terpilih?`;
                document.getElementById('bulkDeleteOffsetDescription').textContent =
                    `${count} ${context.entity} terpilih akan dihapus permanen. Data yang sudah dihapus tidak dapat dikembalikan.`;
                offsetBulkModal.classList.add('is-open');
            }

            ['closeBulkDeleteOffset', 'cancelBulkDeleteOffset'].forEach((id) => {
                document.getElementById(id).addEventListener('click', closeOffsetBulkModal);
            });

            confirmOffsetBulkButton.addEventListener('click', async function() {
                if (isOffsetBulkDeleting || !activeOffsetBulk || !activeOffsetBulk.selected.size) return;
                const context = activeOffsetBulk;
                isOffsetBulkDeleting = true;
                confirmOffsetBulkButton.disabled = true;
                const originalContent = confirmOffsetBulkButton.innerHTML;
                confirmOffsetBulkButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Menghapus...
                `;
                const payload = new FormData();
                payload.append('type', context.type);
                context.selected.forEach((id) => payload.append('ids[]', id));

                try {
                    const response = await fetch(confirmOffsetBulkButton.dataset.bulkUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: payload
                    });
                    const result = await response.json();
                    if (!response.ok || !result.success) {
                        throw new Error(result.message || `${context.entity} gagal dihapus.`);
                    }

                    context.selected.clear();
                    context.refresh();
                    isOffsetBulkDeleting = false;
                    closeOffsetBulkModal();
                    context.table.ajax.reload(null, false);
                    showToast('success', result.message);
                } catch (error) {
                    showToast('error', error.message || `Terjadi kesalahan saat menghapus ${context.entity}.`);
                } finally {
                    isOffsetBulkDeleting = false;
                    confirmOffsetBulkButton.disabled = false;
                    confirmOffsetBulkButton.innerHTML = originalContent;
                }
            });

            const tablePlano = createOffsetTable({
                type: 'plano',
                prefix: 'offsetPlano',
                url: "{{ route('admin_data_offset_plano') }}",
                entity: 'kertas plano',
                totalLabel: 'Kertas Plano',
                nameField: 'material_name',
                badgeId: 'badgeCountPlano',
                statId: 'statValPlano',
                defaultOrder: [[3, 'asc']],
                columns: [{
                        data: 'material_code',
                        className: 'text-center cat-td-date',
                        render: (data, type) => type !== 'display' ? (data || '') : offsetChip('bi-upc-scan', data || '-')
                    },
                    {
                        data: 'material_name',
                        className: 'cat-td-name',
                        render: (data, type) => type !== 'display' ? (data || '') : offsetNameBox('bi-file-earmark-text-fill', data, 'Kertas Plano')
                    },
                    {
                        data: 'length',
                        className: 'text-center cat-td-date',
                        render: (data, type, row) => {
                            const size = `${offsetNumber(row.length)} × ${offsetNumber(row.width)} cm`;
                            return type !== 'display' ? size : offsetChip('bi-aspect-ratio', size);
                        }
                    },
                    {
                        data: 'gramature',
                        className: 'text-center cat-td-date',
                        render: (data, type) => type !== 'display' ? (data || 0) : offsetChip('bi-layers-fill', data ? `${offsetNumber(data)} gsm` : '-')
                    },
                    {
                        data: 'paper_type',
                        className: 'text-center cat-td-date',
                        render: (data, type) => type !== 'display' ? (data || '') : offsetChip('bi-tag-fill', data || '-')
                    },
                    {
                        data: 'price_per_plano',
                        className: 'text-end',
                        render: offsetMoney
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        render: offsetStatus
                    }
                ]
            });

            // Form Submit: Tambah Kertas Plano
            $('#formTambahPlano').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSubmitPlano');
                $btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: "{{ route('admin_store_offset_plano') }}",
                    type: "POST",
                    data: serializeFormWithCleanRupiah($(this)),
                    success: function(res) {
                        $('#modalTambahPlano').removeClass('is-open');
                        $('#formTambahPlano')[0].reset();
                        $('#modalTambahPlano .input-rupiah').val('');
                        $('#planoCategorySelect').val('').trigger('change');
                        $('#planoMaterialSelect').empty().append(
                            '<option value="">-- Pilih Kategori Dahulu --</option>').prop(
                            'disabled', true).trigger('change');
                        $('#planoStatus').val('active').trigger('change');
                        tablePlano.ajax.reload(null, false);
                        if (typeof showToast === 'function') {
                            showToast('success', res.message ||
                                'Kertas plano berhasil disimpan!');
                        }
                        const curCount = parseInt($('#badgeCountPlano').text()) || 0;
                        $('#badgeCountPlano').text(curCount + 1);
                        $('#statValPlano').text(curCount + 1);
                    },
                    error: function(xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr
                            .responseJSON.message : 'Gagal menyimpan kertas plano.';
                        if (typeof showToast === 'function') {
                            showToast('error', msg);
                        } else {
                            alert(msg);
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Simpan Kertas Plano');
                    }
                });
            });

            const tablePlates = createOffsetTable({
                type: 'plate',
                prefix: 'offsetPlates',
                url: "{{ route('admin_data_offset_plates') }}",
                entity: 'plat CTP',
                totalLabel: 'Plat CTP',
                nameField: 'plate_name',
                badgeId: 'badgeCountPlates',
                statId: 'statValPlates',
                defaultOrder: [[3, 'asc']],
                columns: [{
                        data: 'plate_code',
                        className: 'text-center cat-td-date',
                        render: (data, type) => type !== 'display' ? (data || '') : offsetChip('bi-upc-scan', data || '-')
                    },
                    {
                        data: 'plate_name',
                        className: 'cat-td-name',
                        render: (data, type) => type !== 'display' ? (data || '') : offsetNameBox('bi-grid-1x2-fill', data, 'Plat CTP')
                    },
                    {
                        data: 'machine_name',
                        className: 'cat-td-creator',
                        render: (data, type) => {
                            if (type !== 'display') return data || '';
                            return `
                                <div class="cat-user-badge">
                                    <span class="cat-user-avatar" style="background: #2563eb;"><i class="bi bi-printer-fill"></i></span>
                                    <div class="cat-user-info">
                                        <span class="cat-user-name">${escapeHtml(data || '-')}</span>
                                        <span class="cat-user-role">Mesin Offset</span>
                                    </div>
                                </div>
                            `;
                        }
                    },
                    {
                        data: 'price_per_plate',
                        className: 'text-end',
                        render: offsetMoney
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        render: offsetStatus
                    }
                ]
            });

            // Form Submit: Tambah Plat CTP
            $('#formTambahPlate').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSubmitPlate');
                $btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: "{{ route('admin_store_offset_plate') }}",
                    type: "POST",
                    data: serializeFormWithCleanRupiah($(this)),
                    success: function(res) {
                        $('#modalTambahPlate').removeClass('is-open');
                        $('#formTambahPlate')[0].reset();
                        $('#modalTambahPlate .input-rupiah').val('');
                        $('#modalTambahPlate .select2').val('active').trigger('change');
                        tablePlates.ajax.reload(null, false);
                        if (typeof showToast === 'function') {
                            showToast('success', res.message || 'Plat CTP berhasil disimpan!');
                        }
                        const curCount = parseInt($('#badgeCountPlates').text()) || 0;
                        $('#badgeCountPlates').text(curCount + 1);
                        $('#statValPlates').text(curCount + 1);
                    },
                    error: function(xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr
                            .responseJSON.message : 'Gagal menyimpan plat CTP.';
                        if (typeof showToast === 'function') {
                            showToast('error', msg);
                        } else {
                            alert(msg);
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Simpan Plat CTP');
                    }
                });
            });

            const tablePrintCosts = createOffsetTable({
                type: 'print_cost',
                prefix: 'offsetPrintCosts',
                url: "{{ route('admin_data_offset_print_costs') }}",
                entity: 'profil mesin',
                totalLabel: 'Profil Mesin',
                nameField: 'name',
                badgeId: 'badgeCountPrintCosts',
                statId: 'statValPrintCosts',
                defaultOrder: [[2, 'asc']],
                columns: [{
                        data: 'name',
                        className: 'cat-td-name',
                        render: (data, type) => type !== 'display' ? (data || '') : offsetNameBox('bi-printer-fill', data, 'Profil Mesin')
                    },
                    {
                        data: 'press_sheet_width',
                        className: 'text-center cat-td-date',
                        render: (data, type, row) => {
                            const size = `${offsetNumber(row.press_sheet_width)} × ${offsetNumber(row.press_sheet_length)} cm`;
                            return type !== 'display' ? size : offsetChip('bi-aspect-ratio', size);
                        }
                    },
                    {
                        data: 'default_insheet_sheets',
                        className: 'text-center cat-td-date',
                        render: (data, type) => type !== 'display' ? (data || 0) : offsetChip('bi-stack', `${offsetNumber(data || 0)} lembar`)
                    },
                    {
                        data: 'click_rate_per_color',
                        className: 'text-end',
                        render: offsetMoney
                    },
                    {
                        data: 'min_print_per_color',
                        className: 'text-end',
                        render: offsetMoney
                    },
                    {
                        data: 'cutting_fee_per_rim',
                        className: 'text-end',
                        render: offsetMoney
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        render: offsetStatus
                    }
                ]
            });

            // Form Submit: Tambah Profil Mesin Cetak
            $('#formTambahPrintCost').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSubmitPrintCost');
                $btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: "{{ route('admin_store_offset_print_cost') }}",
                    type: "POST",
                    data: serializeFormWithCleanRupiah($(this)),
                    success: function(res) {
                        $('#modalTambahPrintCost').removeClass('is-open');
                        $('#formTambahPrintCost')[0].reset();
                        $('#formTambahPrintCost [name="click_rate_per_color"]').val('Rp 15');
                        $('#formTambahPrintCost [name="min_print_per_color"]').val('Rp 65.000');
                        $('#formTambahPrintCost [name="cutting_fee_per_rim"]').val('Rp 20.000');
                        $('#modalTambahPrintCost .select2').val('active').trigger('change');
                        tablePrintCosts.ajax.reload(null, false);
                        if (typeof showToast === 'function') {
                            showToast('success', res.message ||
                                'Profil mesin berhasil disimpan!');
                        }
                        const curCount = parseInt($('#badgeCountPrintCosts').text()) || 0;
                        $('#badgeCountPrintCosts').text(curCount + 1);
                        $('#statValPrintCosts').text(curCount + 1);
                    },
                    error: function(xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr
                            .responseJSON.message : 'Gagal menyimpan profil mesin.';
                        if (typeof showToast === 'function') {
                            showToast('error', msg);
                        } else {
                            alert(msg);
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Simpan Profil Mesin');
                    }
                });
            });

            const tablePricingRules = createOffsetTable({
                type: 'pricing_rule',
                prefix: 'offsetPricingRules',
                url: "{{ route('admin_data_offset_pricing_rules') }}",
                entity: 'pricing rule',
                totalLabel: 'Pricing Rule',
                nameField: 'rule_name',
                badgeId: 'badgeCountPricingRules',
                statId: 'statValPricingRules',
                defaultOrder: [[2, 'asc']],
                columns: [{
                        data: 'rule_name',
                        className: 'cat-td-name',
                        render: (data, type) => type !== 'display' ? (data || '') : offsetNameBox('bi-percent', data, 'Pricing Rule')
                    },
                    {
                        data: 'category_name',
                        className: 'text-center cat-td-date',
                        render: (data, type) => type !== 'display' ? (data || '') : offsetChip('bi-folder-fill', data || '-')
                    },
                    {
                        data: 'min_rim',
                        className: 'text-center cat-td-date',
                        render: (data, type, row) => {
                            const range = `${row.min_rim || 1} – ${row.max_rim || 10} Rim`;
                            return type !== 'display' ? range : offsetChip('bi-arrow-left-right', range);
                        }
                    },
                    {
                        data: 'markup_general',
                        className: 'text-center',
                        render: (data, type) => type !== 'display' ? (Number(data) || 0) :
                            `<span style="font-weight: 600; color: var(--primary);">${escapeHtml(offsetNumber(data || 0))}%</span>`
                    },
                    {
                        data: 'markup_division',
                        className: 'text-center',
                        render: (data, type) => type !== 'display' ? (Number(data) || 0) :
                            `<span style="font-weight: 600; color: var(--success);">${escapeHtml(offsetNumber(data || 0))}%</span>`
                    },
                    {
                        data: 'rounding_value',
                        className: 'text-end',
                        render: offsetMoney
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        render: offsetStatus
                    }
                ]
            });

            // DataTable 5: Master Produk Cetak Offset (Brosur / Flyer)
            const tableProducts = createOffsetTable({
                type: 'product',
                prefix: 'offsetProducts',
                url: "{{ route('admin_data_offset_products') }}",
                entity: 'produk cetak',
                totalLabel: 'Produk Cetak',
                nameField: 'product_name',
                badgeId: 'badgeCountProducts',
                statId: 'statValProducts',
                defaultOrder: [[3, 'asc']],
                columns: [{
                        data: 'product_code',
                        className: 'text-center cat-td-date',
                        render: (data, type) => type !== 'display' ? (data || '') : offsetChip('bi-upc-scan', data || '-')
                    },
                    {
                        data: 'product_name',
                        className: 'cat-td-name',
                        render: (data, type, row) => type !== 'display' ? (data || '') :
                            offsetNameBox('bi-journal-richtext', data, 'Produk Cetak', row.description || '')
                    },
                    {
                        data: 'category',
                        className: 'text-center cat-td-date',
                        render: (data, type) => type !== 'display' ? (data || '') : offsetChip('bi-folder-fill', data || 'Brosur')
                    },
                    {
                        data: 'final_length',
                        className: 'text-center cat-td-date',
                        render: (data, type, row) => {
                            const size = `${offsetNumber(row.final_length)} × ${offsetNumber(row.final_width)} cm`;
                            return type !== 'display' ? size : offsetChip('bi-aspect-ratio', size);
                        }
                    },
                    {
                        data: 'sheet_per_rim',
                        className: 'text-center cat-td-date',
                        render: (data, type, row) => {
                            const sheets = data || Math.round(500 / (row.up_per_sheet || 1));
                            const text = `${offsetNumber(sheets)} lbr (${row.up_per_sheet || 1} Up)`;
                            return type !== 'display' ? sheets : offsetChip('bi-files', text);
                        }
                    },
                    {
                        data: 'plano_cut_yield',
                        className: 'text-center cat-td-date',
                        render: (data, type) => type !== 'display' ? (data || 4) : offsetChip('bi-scissors', `${data || 4} Lembar / Plano`)
                    },
                    {
                        data: 'print_sheet_length',
                        className: 'text-center cat-td-date',
                        render: (data, type, row) => {
                            const size = `${offsetNumber(row.print_sheet_length)} × ${offsetNumber(row.print_sheet_width)} cm`;
                            return type !== 'display' ? size : offsetChip('bi-aspect-ratio', size);
                        }
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        render: offsetStatus
                    }
                ]
            });

            // Form Submit: Tambah Pricing Rule
            $('#formTambahPricingRule').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSubmitPricingRule');
                $btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: "{{ route('admin_store_offset_pricing_rule') }}",
                    type: "POST",
                    data: serializeFormWithCleanRupiah($(this)),
                    success: function(res) {
                        $('#modalTambahPricingRule').removeClass('is-open');
                        $('#formTambahPricingRule')[0].reset();
                        $('#formTambahPricingRule [name="rounding_value"]').val('Rp 5.000');
                        $('#modalTambahPricingRule .select2').val('active').trigger('change');
                        tablePricingRules.ajax.reload(null, false);
                        if (typeof showToast === 'function') {
                            showToast('success', res.message ||
                                'Pricing rule berhasil disimpan!');
                        }
                        const curCount = parseInt($('#badgeCountPricingRules').text()) || 0;
                        $('#badgeCountPricingRules').text(curCount + 1);
                        $('#statValPricingRules').text(curCount + 1);
                    },
                    error: function(xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr
                            .responseJSON.message : 'Gagal menyimpan pricing rule.';
                        if (typeof showToast === 'function') {
                            showToast('error', msg);
                        } else {
                            alert(msg);
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Simpan Pricing Rule');
                    }
                });
            });

            // Form Submit: Tambah Produk Cetak Offset
            $('#formTambahProduct').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSubmitProduct');
                $btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: "{{ route('admin_store_offset_product') }}",
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#modalTambahProduct').removeClass('is-open');
                        $('#formTambahProduct')[0].reset();
                        $('#formTambahProduct [name="print_sheet_width"]').val('32.50');
                        $('#formTambahProduct [name="print_sheet_length"]').val('45.00');
                        $('#formTambahProduct #inputSheetPerRim').val('125');
                        $('#formTambahProduct #hiddenUpPerSheet').val('4');
                        $('#hintSheetPerRim').text('Sesuai Kolom F Excel (Setara 4 Up / pcs per lembar)');
                        $('#formTambahProduct [name="plano_cut_yield"]').val('4');
                        $('#productCategory').val('Brosur').trigger('change');
                        $('#productStatus').val('active').trigger('change');
                        tableProducts.ajax.reload(null, false);
                        if (typeof showToast === 'function') {
                            showToast('success', res.message || 'Produk cetak berhasil disimpan!');
                        }
                        const curCount = parseInt($('#badgeCountProducts').text()) || 0;
                        $('#badgeCountProducts').text(curCount + 1);
                        $('#statValProducts').text(curCount + 1);
                    },
                    error: function(xhr) {
                        let msg = 'Gagal menyimpan produk cetak.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                const errs = Object.values(xhr.responseJSON.errors).flat();
                                msg = errs.join('\n');
                            } else if (xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                        }
                        if (typeof showToast === 'function') {
                            showToast('error', msg);
                        } else {
                            alert(msg);
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Simpan Produk Cetak');
                    }
                });
            });

            // Live calculation helper for sheet_per_rim (Kolom F Excel)
            $('#inputSheetPerRim').on('input', function() {
                const val = parseInt($(this).val()) || 0;
                if (val > 0) {
                    const up = Math.max(1, Math.round(500 / val));
                    $('#hiddenUpPerSheet').val(up);
                    $('#hintSheetPerRim').text(`Sesuai Kolom F Excel (Setara ${up} Up / pcs per lembar)`);
                } else {
                    $('#hintSheetPerRim').text('Sesuai Kolom F Excel');
                }
            });

            // =========================================================
            // FITUR EDIT DATA MASTER OFFSET
            // =========================================================

            function openEditModal(type, data) {
                initModalSelect2();

                if (type === 'plano') {
                    // Temukan material yang cocok dari array digitalMaterials
                    let matId = data.material_id;
                    let foundMat = null;
                    if (matId) {
                        foundMat = digitalMaterials.find(m => String(m.id) === String(matId));
                    }
                    if (!foundMat && data.material_name) {
                        foundMat = digitalMaterials.find(m => m.material_name.toLowerCase().trim() === data
                            .material_name.toLowerCase().trim());
                        if (foundMat) matId = foundMat.id;
                    }

                    if (foundMat && foundMat.category_id) {
                        $('#editPlanoCategorySelect').val(foundMat.category_id).trigger('change');
                        $('#editPlanoMaterialSelect').val(matId).trigger('change');
                    } else {
                        $('#editPlanoCategorySelect').val('').trigger('change');
                    }

                    // Isi nilai input setelah cascading select selesai
                    $('#editPlanoId').val(data.id);
                    $('#editPlanoLength').val(data.length || '');
                    $('#editPlanoWidth').val(data.width || '');
                    $('#editPlanoMaterialCode').val(data.material_code || '');
                    $('#editPlanoGramature').val(data.gramature || '');
                    $('#editPlanoPrice').val(formatRupiah(data.price_per_plano));
                    $('#editPlanoStatus').val((data.status || 'active').toLowerCase()).trigger('change');

                    $('#modalEditPlano').addClass('is-open');
                } else if (type === 'plate') {
                    $('#editPlateId').val(data.id);
                    $('#editPlateName').val(data.plate_name || '');
                    $('#editPlateCode').val(data.plate_code || '');
                    $('#editPlateMachine').val(data.machine_name || '');
                    $('#editPlatePrice').val(formatRupiah(data.price_per_plate));
                    $('#editPlateStatus').val((data.status || 'active').toLowerCase()).trigger('change');

                    $('#modalEditPlate').addClass('is-open');
                } else if (type === 'print_cost') {
                    $('#editPrintCostId').val(data.id);
                    $('#editPrintCostName').val(data.name || '');
                    $('#editPressSheetWidth').val(data.press_sheet_width || '');
                    $('#editPressSheetLength').val(data.press_sheet_length || '');
                    $('#editDefaultInsheet').val(data.default_insheet_sheets || 0);
                    $('#editClickRate').val(formatRupiah(data.click_rate_per_color));
                    $('#editMinPrint').val(formatRupiah(data.min_print_per_color));
                    $('#editCuttingFee').val(formatRupiah(data.cutting_fee_per_rim));
                    $('#editPrintCostStatus').val((data.status || 'active').toLowerCase()).trigger('change');

                    $('#modalEditPrintCost').addClass('is-open');
                } else if (type === 'pricing_rule') {
                    $('#editPricingRuleId').val(data.id);
                    $('#editRuleName').val(data.rule_name || '');
                    $('#editCategoryName').val(data.category_name || '');
                    $('#editMinRim').val(data.min_rim || 1);
                    $('#editMaxRim').val(data.max_rim || 10);
                    $('#editMarkupGeneral').val(data.markup_general || 0);
                    $('#editMarkupDivision').val(data.markup_division || 0);
                    $('#editRoundingValue').val(formatRupiah(data.rounding_value));
                    $('#editPricingRuleStatus').val((data.status || 'active').toLowerCase()).trigger('change');

                    $('#modalEditPricingRule').addClass('is-open');
                } else if (type === 'product') {
                    $('#editProductId').val(data.id);
                    $('#editProductCode').val(data.product_code || '');
                    $('#editProductName').val(data.product_name || '');
                    $('#editProductCategory').val(data.category || 'Brosur').trigger('change');
                    $('#editProductStatus').val((data.status || 'active').toLowerCase()).trigger('change');
                    $('#editFinalLength').val(data.final_length || '');
                    $('#editFinalWidth').val(data.final_width || '');
                    $('#editPrintSheetLength').val(data.print_sheet_length || '45.00');
                    $('#editPrintSheetWidth').val(data.print_sheet_width || '32.50');
                    const sheetRim = data.sheet_per_rim || Math.round(500 / (data.up_per_sheet || 1));
                    const upPcs = data.up_per_sheet || Math.max(1, Math.round(500 / sheetRim));
                    $('#editSheetPerRim').val(sheetRim);
                    $('#editHiddenUpPerSheet').val(upPcs);
                    $('#editHintSheetPerRim').text(`Sesuai Kolom F Excel (Setara ${upPcs} Up / pcs per lembar)`);
                    $('#editPlanoCutYield').val(data.plano_cut_yield || 4);
                    $('#editDescription').val(data.description || '');

                    $('#modalEditProduct').addClass('is-open');
                }
            }

            // Event Delegation untuk Tombol Edit
            $(document).on('click', '.btn-edit-offset', function() {
                const type = $(this).data('type');
                const $tr = $(this).closest('tr');
                let rowData = null;

                if (type === 'plano') {
                    rowData = tablePlano.row($tr.hasClass('child') ? $tr.prev() : $tr).data();
                } else if (type === 'plate') {
                    rowData = tablePlates.row($tr.hasClass('child') ? $tr.prev() : $tr).data();
                } else if (type === 'print_cost') {
                    rowData = tablePrintCosts.row($tr.hasClass('child') ? $tr.prev() : $tr).data();
                } else if (type === 'pricing_rule') {
                    rowData = tablePricingRules.row($tr.hasClass('child') ? $tr.prev() : $tr).data();
                } else if (type === 'product') {
                    rowData = tableProducts.row($tr.hasClass('child') ? $tr.prev() : $tr).data();
                }

                if (rowData) {
                    openEditModal(type, rowData);
                }
            });

            // Form Submit: Edit Plano
            $('#formEditPlano').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editPlanoId').val();
                const $btn = $('#btnSubmitEditPlano');
                $btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: "{{ url('admin/offset-master/update-plano') }}/" + id,
                    type: "POST",
                    data: serializeFormWithCleanRupiah($(this)),
                    success: function(res) {
                        $('#modalEditPlano').removeClass('is-open');
                        tablePlano.ajax.reload(null, false);
                        if (typeof showToast === 'function') {
                            showToast('success', res.message ||
                                'Kertas plano berhasil diperbarui!');
                        }
                    },
                    error: function(xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr
                            .responseJSON.message : 'Gagal memperbarui kertas plano.';
                        if (typeof showToast === 'function') {
                            showToast('error', msg);
                        } else {
                            alert(msg);
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Simpan Perubahan');
                    }
                });
            });

            // Form Submit: Edit Plate
            $('#formEditPlate').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editPlateId').val();
                const $btn = $('#btnSubmitEditPlate');
                $btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: "{{ url('admin/offset-master/update-plate') }}/" + id,
                    type: "POST",
                    data: serializeFormWithCleanRupiah($(this)),
                    success: function(res) {
                        $('#modalEditPlate').removeClass('is-open');
                        tablePlates.ajax.reload(null, false);
                        if (typeof showToast === 'function') {
                            showToast('success', res.message ||
                                'Plat CTP berhasil diperbarui!');
                        }
                    },
                    error: function(xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr
                            .responseJSON.message : 'Gagal memperbarui plat CTP.';
                        if (typeof showToast === 'function') {
                            showToast('error', msg);
                        } else {
                            alert(msg);
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Simpan Perubahan');
                    }
                });
            });

            // Form Submit: Edit Print Cost
            $('#formEditPrintCost').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editPrintCostId').val();
                const $btn = $('#btnSubmitEditPrintCost');
                $btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: "{{ url('admin/offset-master/update-print-cost') }}/" + id,
                    type: "POST",
                    data: serializeFormWithCleanRupiah($(this)),
                    success: function(res) {
                        $('#modalEditPrintCost').removeClass('is-open');
                        tablePrintCosts.ajax.reload(null, false);
                        if (typeof showToast === 'function') {
                            showToast('success', res.message ||
                                'Profil mesin berhasil diperbarui!');
                        }
                    },
                    error: function(xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr
                            .responseJSON.message : 'Gagal memperbarui profil mesin.';
                        if (typeof showToast === 'function') {
                            showToast('error', msg);
                        } else {
                            alert(msg);
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Simpan Perubahan');
                    }
                });
            });

            // Form Submit: Edit Pricing Rule
            $('#formEditPricingRule').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editPricingRuleId').val();
                const $btn = $('#btnSubmitEditPricingRule');
                $btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: "{{ url('admin/offset-master/update-pricing-rule') }}/" + id,
                    type: "POST",
                    data: serializeFormWithCleanRupiah($(this)),
                    success: function(res) {
                        $('#modalEditPricingRule').removeClass('is-open');
                        tablePricingRules.ajax.reload(null, false);
                        if (typeof showToast === 'function') {
                            showToast('success', res.message ||
                                'Pricing rule berhasil diperbarui!');
                        }
                    },
                    error: function(xhr) {
                        const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr
                            .responseJSON.message : 'Gagal memperbarui pricing rule.';
                        if (typeof showToast === 'function') {
                            showToast('error', msg);
                        } else {
                            alert(msg);
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Simpan Perubahan');
                    }
                });
            });

            // Form Submit: Edit Produk Cetak Offset
            $('#formEditProduct').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editProductId').val();
                const $btn = $('#btnUpdateProduct');
                $btn.prop('disabled', true).text('Menyimpan...');

                $.ajax({
                    url: "{{ url('/admin/offset-master/update-product') }}/" + id,
                    type: "POST",
                    data: $(this).serialize(),
                    success: function(res) {
                        $('#modalEditProduct').removeClass('is-open');
                        tableProducts.ajax.reload(null, false);
                        if (typeof showToast === 'function') {
                            showToast('success', res.message || 'Produk cetak berhasil diperbarui!');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Gagal memperbarui produk cetak.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.errors) {
                                const errs = Object.values(xhr.responseJSON.errors).flat();
                                msg = errs.join('\n');
                            } else if (xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                        }
                        if (typeof showToast === 'function') {
                            showToast('error', msg);
                        } else {
                            alert(msg);
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Simpan Perubahan');
                    }
                });
            });

            // Live calculation helper for editSheetPerRim (Kolom F Excel)
            $('#editSheetPerRim').on('input', function() {
                const val = parseInt($(this).val()) || 0;
                if (val > 0) {
                    const up = Math.max(1, Math.round(500 / val));
                    $('#editHiddenUpPerSheet').val(up);
                    $('#editHintSheetPerRim').text(`Sesuai Kolom F Excel (Setara ${up} Up / pcs per lembar)`);
                } else {
                    $('#editHintSheetPerRim').text('Sesuai Kolom F Excel');
                }
            });
        });
    </script>
@endsection

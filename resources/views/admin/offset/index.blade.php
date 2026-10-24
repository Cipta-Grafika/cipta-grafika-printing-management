@extends('admin_master')

@section('contents')
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

        /* ACTION BUTTONS IN DATATABLE */
        .btn-detail-offset {
            border-radius: 6px;
            padding: 6px 8px;
            /* border: 1px solid var(--border); */
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            /* transition: opacity 0.2s ease, transform 0.1s ease; */
            /* background: var(--bg-hover, rgba(59, 130, 246, 0.08)); */
            /* color: var(--primary, #3b82f6); */
        }

        .btn-detail-offset:hover {
            opacity: 0.85;
            transform: scale(1.05);
        }

        .btn-edit-offset {
            border-radius: 6px;
            padding: 6px 8px;
            /* border: 1px solid var(--border); */
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            /* transition: opacity 0.2s ease, transform 0.1s ease; */
            /* background: var(--bg-hover, rgba(245, 158, 11, 0.08)); */
            /* color: #d97706; */
        }

        .btn-edit-offset:hover {
            opacity: 0.85;
            transform: scale(1.05);
        }

        .btn-delete-offset {
            border-radius: 6px;
            padding: 6px 8px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.2s ease, transform 0.1s ease;
        }

        .btn-delete-offset:hover {
            opacity: 0.85;
            transform: scale(1.05);
        }
    </style>

    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">

                {{-- HERO SECTION --}}
                <section class="hero">
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

                    {{-- HERO ACTIONS (DYNAMIC TAMBAH BUTTON) --}}
                    <div class="hero-actions" style="display: flex; justify-content: flex-end; align-items: flex-start;">
                        <button type="button" class="btn btn--primary" id="btnMainCreate">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            <span id="labelMainCreate">Tambah Kertas Plano</span>
                        </button>
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

                {{-- SECTION 1: TAB KERTAS PLANO --}}
                <div class="offset-table-section active" id="tabPlano">
                    <div class="grid">
                        <section class="col-12 card">
                            <div
                                style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--t-base);">Daftar
                                        Kertas Plano Offset</h3>
                                    <p style="margin: 4px 0 0; font-size: 13px; color: var(--t-muted);">Master bahan
                                        lembaran plano baku yang digunakan untuk kalkulasi cetak mesin offset.</p>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="offsetPlanoTable" class="data-table display responsive nowrap"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 32px;"></th>
                                            <th style="width: 50px;" class="text-center">No</th>
                                            <th>Kode</th>
                                            <th>Nama Bahan</th>
                                            <th class="text-center">Ukuran Plano (P x L)</th>
                                            <th class="text-center">Gramatur</th>
                                            <th class="text-center">Jenis</th>
                                            <th class="text-end">Harga Satuan Plano</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center" style="width: 70px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                </div>

                {{-- SECTION 2: TAB PLAT CTP --}}
                <div class="offset-table-section" id="tabPlates">
                    <div class="grid">
                        <section class="col-12 card">
                            <div
                                style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--t-base);">Daftar
                                        Plat CTP Offset</h3>
                                    <p style="margin: 4px 0 0; font-size: 13px; color: var(--t-muted);">Master plat cetak
                                        CTP per warna untuk cetak offset (CMYK = 4 plat per job).</p>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="offsetPlatesTable" class="data-table display responsive nowrap"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 32px;"></th>
                                            <th style="width: 50px;" class="text-center">No</th>
                                            <th>Kode Plat</th>
                                            <th>Nama Plat CTP</th>
                                            <th>Mesin Terkait</th>
                                            <th class="text-end">Harga per Plat</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center" style="width: 70px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                </div>

                {{-- SECTION 3: TAB TARIF CETAK & MESIN --}}
                <div class="offset-table-section" id="tabPrintCosts">
                    <div class="grid">
                        <section class="col-12 card">
                            <div
                                style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--t-base);">
                                        Parameter Operasional & Tarif Mesin Offset</h3>
                                    <p style="margin: 4px 0 0; font-size: 13px; color: var(--t-muted);">Ukuran lembar
                                        potong mesin, toleransi insheet setelan awal, tarif klik per warna, dan ongkos
                                        potong.</p>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="offsetPrintCostsTable" class="data-table display responsive nowrap"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 32px;"></th>
                                            <th style="width: 50px;" class="text-center">No</th>
                                            <th>Nama Profil Mesin</th>
                                            <th class="text-center">Ukuran Cetak Mesin</th>
                                            <th class="text-center">Insheet Setelan</th>
                                            <th class="text-end">Tarif per Klik/Warna</th>
                                            <th class="text-end">Minim per Warna</th>
                                            <th class="text-end">Ongkos Potong / Rim</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center" style="width: 70px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                </div>

                {{-- SECTION 4: TAB PRICING RULES --}}
                <div class="offset-table-section" id="tabPricingRules">
                    <div class="grid">
                        <section class="col-12 card">
                            <div
                                style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--t-base);">Aturan
                                        Markup & Harga Jual Offset</h3>
                                    <p style="margin: 4px 0 0; font-size: 13px; color: var(--t-muted);">Ketentuan
                                        persentase margin keuntungan untuk Harga Jual Umum dan Harga Divisi.</p>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="offsetPricingRulesTable" class="data-table display responsive nowrap"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 32px;"></th>
                                            <th style="width: 50px;" class="text-center">No</th>
                                            <th>Nama Aturan</th>
                                            <th>Kategori</th>
                                            <th class="text-center">Rentang Rim</th>
                                            <th class="text-center">Markup Umum</th>
                                            <th class="text-center">Markup Divisi</th>
                                            <th class="text-end">Pembulatan</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center" style="width: 70px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                </div>

                {{-- SECTION 5: TAB PRODUK CETAK (BROSUR / FLYER) --}}
                <div class="offset-table-section" id="tabProducts">
                    <div class="grid">
                        <section class="col-12 card">
                            <div
                                style="margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--t-base);">Daftar
                                        Produk Cetak / Brosur Offset</h3>
                                    <p style="margin: 4px 0 0; font-size: 13px; color: var(--t-muted);">Identitas spesifikasi
                                        produk cetak lembaran, ukuran jadi, dan rasio potong (up) per lembar mesin.</p>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="offsetProductsTable" class="data-table display responsive nowrap"
                                    style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th style="width: 32px;"></th>
                                            <th style="width: 50px;" class="text-center">No</th>
                                            <th>Kode</th>
                                            <th>Nama Produk</th>
                                            <th>Kategori</th>
                                            <th class="text-center">Ukuran Jadi (P × L)</th>
                                            <th class="text-center">Qty / Rim (Kolom F)</th>
                                            <th class="text-center">Plano Yield</th>
                                            <th class="text-center">Lembar Mesin</th>
                                            <th class="text-center">Status</th>
                                            <th class="text-center" style="width: 70px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </section>
                    </div>
                </div>

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

                    // Adjust and recalculate responsive DataTables on tab switch
                    setTimeout(() => {
                        $.fn.dataTable.tables({
                            visible: true,
                            api: true
                        }).columns.adjust().responsive.recalc();
                    }, 50);
                });
            });

            // Open Active Modal
            btnMainCreate.addEventListener('click', function() {
                const targetModal = document.getElementById(currentModalId);
                if (targetModal) {
                    targetModal.classList.add('is-open');
                    initModalSelect2();
                }
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

            // 1. DataTable Kertas Plano (Responsive Mobile Support)
            const tablePlano = $('#offsetPlanoTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                responsive: {
                    details: {
                        type: 'column',
                        target: 0
                    }
                },
                ajax: {
                    url: "{{ route('admin_data_offset_plano') }}",
                    type: "GET"
                },
                columns: [{
                        data: null,
                        defaultContent: '',
                        className: 'dtr-control',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'material_code',
                        render: function(data) {
                            return data ? '<strong>' + data + '</strong>' : '-';
                        }
                    },
                    {
                        data: 'material_name',
                        responsivePriority: 1,
                        render: function(data) {
                            return '<span style="font-weight: 600; color: var(--t-base);">' + (
                                data || '-') + '</span>';
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return (row.length || '-') + ' × ' + (row.width || '-') + ' cm';
                        }
                    },
                    {
                        data: 'gramature',
                        className: 'text-center',
                        render: function(data) {
                            return data ? data + ' gsm' : '-';
                        }
                    },
                    {
                        data: 'paper_type',
                        className: 'text-center',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'price_per_plano',
                        className: 'text-end',
                        responsivePriority: 2,
                        render: function(data) {
                            return '<span style="font-weight: 600; color: var(--t-base);">' +
                                formatRupiah(data) + '</span>';
                        }
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        responsivePriority: 2,
                        render: function(data) {
                            const isAct = (data || '').toLowerCase() === 'active';
                            return '<span class="' + (isAct ? 'badge-active' : 'badge-inactive') +
                                '">' + (data || 'Active') + '</span>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                                <div style="display: flex; justify-content: center; gap: 6px;">
                                    <button type="button" class="btn-detail-offset" data-type="plano" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn-edit-offset" data-type="plano" title="Edit Data">
                                        <i class="bi bi-pen"></i>
                                    </button>
                                    <button type="button" class="btn-delete-offset" data-id="${row.id}" data-type="plano" data-name="${row.material_name}" title="Hapus">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
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
                    zeroRecords: 'Data kertas plano tidak ditemukan',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari kertas plano...'
                },
                initComplete: function() {
                    $(this).closest('.dt-container, .dataTables_wrapper')
                        .find('.dt-search input, .dataTables_filter input')
                        .css({
                            'padding': '9px 14px',
                            'border-radius': '8px',
                            'border': '1px solid #e2e8f0'
                        });
                }
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

            // 2. DataTable Plat CTP (Responsive Mobile Support)
            const tablePlates = $('#offsetPlatesTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                responsive: {
                    details: {
                        type: 'column',
                        target: 0
                    }
                },
                ajax: {
                    url: "{{ route('admin_data_offset_plates') }}",
                    type: "GET"
                },
                columns: [{
                        data: null,
                        defaultContent: '',
                        className: 'dtr-control',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'plate_code',
                        render: function(data) {
                            return data ? '<strong>' + data + '</strong>' : '-';
                        }
                    },
                    {
                        data: 'plate_name',
                        responsivePriority: 1,
                        render: function(data) {
                            return '<span style="font-weight: 600; color: var(--t-base);">' + (
                                data || '-') + '</span>';
                        }
                    },
                    {
                        data: 'machine_name',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: 'price_per_plate',
                        className: 'text-end',
                        responsivePriority: 2,
                        render: function(data) {
                            return '<span style="font-weight: 600; color: var(--t-base);">' +
                                formatRupiah(data) + '</span>';
                        }
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        responsivePriority: 2,
                        render: function(data) {
                            const isAct = (data || '').toLowerCase() === 'active';
                            return '<span class="' + (isAct ? 'badge-active' : 'badge-inactive') +
                                '">' + (data || 'Active') + '</span>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                                <div style="display: flex; justify-content: center; gap: 6px;">
                                    <button type="button" class="btn-detail-offset" data-type="plate" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn-edit-offset" data-type="plate" title="Edit Data">
                                        <i class="bi bi-pen"></i>
                                    </button>
                                    <button type="button" class="btn-delete-offset" data-id="${row.id}" data-type="plate" data-name="${row.plate_name}" title="Hapus">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
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
                    zeroRecords: 'Data plat CTP tidak ditemukan',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari plat CTP...'
                },
                initComplete: function() {
                    $(this).closest('.dt-container, .dataTables_wrapper')
                        .find('.dt-search input, .dataTables_filter input')
                        .css({
                            'padding': '9px 14px',
                            'border-radius': '8px',
                            'border': '1px solid #e2e8f0'
                        });
                }
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

            // 3. DataTable Parameter Cetak & Potong (Responsive Mobile Support)
            const tablePrintCosts = $('#offsetPrintCostsTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                responsive: {
                    details: {
                        type: 'column',
                        target: 0
                    }
                },
                ajax: {
                    url: "{{ route('admin_data_offset_print_costs') }}",
                    type: "GET"
                },
                columns: [{
                        data: null,
                        defaultContent: '',
                        className: 'dtr-control',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'name',
                        responsivePriority: 1,
                        render: function(data) {
                            return '<span style="font-weight: 600; color: var(--t-base);">' + (
                                data || '-') + '</span>';
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return (row.press_sheet_width || '-') + ' × ' + (row
                                .press_sheet_length || '-') + ' cm';
                        }
                    },
                    {
                        data: 'default_insheet_sheets',
                        className: 'text-center',
                        render: function(data) {
                            return (data || 0) + ' lembar';
                        }
                    },
                    {
                        data: 'click_rate_per_color',
                        className: 'text-end',
                        render: function(data) {
                            return formatRupiah(data);
                        }
                    },
                    {
                        data: 'min_print_per_color',
                        className: 'text-end',
                        responsivePriority: 2,
                        render: function(data) {
                            return formatRupiah(data);
                        }
                    },
                    {
                        data: 'cutting_fee_per_rim',
                        className: 'text-end',
                        render: function(data) {
                            return formatRupiah(data);
                        }
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        responsivePriority: 2,
                        render: function(data) {
                            const isAct = (data || '').toLowerCase() === 'active';
                            return '<span class="' + (isAct ? 'badge-active' : 'badge-inactive') +
                                '">' + (data || 'Active') + '</span>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                                <div style="display: flex; justify-content: center; gap: 6px;">
                                    <button type="button" class="btn-detail-offset" data-type="print_cost" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn-edit-offset" data-type="print_cost" title="Edit Data">
                                        <i class="bi bi-pen"></i>
                                    </button>
                                    <button type="button" class="btn-delete-offset" data-id="${row.id}" data-type="print_cost" data-name="${row.name}" title="Hapus">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
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
                    zeroRecords: 'Data profil mesin tidak ditemukan',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari profil mesin...'
                },
                initComplete: function() {
                    $(this).closest('.dt-container, .dataTables_wrapper')
                        .find('.dt-search input, .dataTables_filter input')
                        .css({
                            'padding': '9px 14px',
                            'border-radius': '8px',
                            'border': '1px solid #e2e8f0'
                        });
                }
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

            // 4. DataTable Pricing Rules (Responsive Mobile Support)
            const tablePricingRules = $('#offsetPricingRulesTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                responsive: {
                    details: {
                        type: 'column',
                        target: 0
                    }
                },
                ajax: {
                    url: "{{ route('admin_data_offset_pricing_rules') }}",
                    type: "GET"
                },
                columns: [{
                        data: null,
                        defaultContent: '',
                        className: 'dtr-control',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'rule_name',
                        responsivePriority: 1,
                        render: function(data) {
                            return '<span style="font-weight: 600; color: var(--t-base);">' + (
                                data || '-') + '</span>';
                        }
                    },
                    {
                        data: 'category_name',
                        render: function(data) {
                            return data || '-';
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return (row.min_rim || 1) + ' – ' + (row.max_rim || 10) + ' Rim';
                        }
                    },
                    {
                        data: 'markup_general',
                        className: 'text-center',
                        responsivePriority: 2,
                        render: function(data) {
                            return '<span style="font-weight: 600; color: var(--primary);">' + (
                                data || 0) + '%</span>';
                        }
                    },
                    {
                        data: 'markup_division',
                        className: 'text-center',
                        render: function(data) {
                            return '<span style="font-weight: 600; color: var(--success);">' + (
                                data || 0) + '%</span>';
                        }
                    },
                    {
                        data: 'rounding_value',
                        className: 'text-end',
                        render: function(data) {
                            return formatRupiah(data);
                        }
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        responsivePriority: 2,
                        render: function(data) {
                            const isAct = (data || '').toLowerCase() === 'active';
                            return '<span class="' + (isAct ? 'badge-active' : 'badge-inactive') +
                                '">' + (data || 'Active') + '</span>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                                <div style="display: flex; justify-content: center; gap: 6px;">
                                    <button type="button" class="btn-detail-offset" data-type="pricing_rule" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn-edit-offset" data-type="pricing_rule" title="Edit Data">
                                        <i class="bi bi-pen"></i>
                                    </button>
                                    <button type="button" class="btn-delete-offset" data-id="${row.id}" data-type="pricing_rule" data-name="${row.rule_name}" title="Hapus">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
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
                    zeroRecords: 'Data aturan ongkos cetak tidak ditemukan',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari aturan ongkos cetak...'
                },
                initComplete: function() {
                    $(this).closest('.dt-container, .dataTables_wrapper')
                        .find('.dt-search input, .dataTables_filter input')
                        .css({
                            'padding': '9px 14px',
                            'border-radius': '8px',
                            'border': '1px solid #e2e8f0'
                        });
                }
            });

            // DataTable 5: Master Produk Cetak Offset (Brosur / Flyer)
            const tableProducts = $('#offsetProductsTable').DataTable({
                processing: true,
                serverSide: true,
                responsive: {
                    details: {
                        type: 'column',
                        target: 0
                    }
                },
                ajax: {
                    url: "{{ route('admin_data_offset_products') }}",
                    type: "GET"
                },
                order: [
                    [1, 'asc']
                ],
                columns: [
                    {
                        data: null,
                        defaultContent: '',
                        className: 'dtr-control',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        responsivePriority: 2,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'product_code',
                        responsivePriority: 2,
                        render: function(data) {
                            return data ?
                                `<code style="font-weight: 600; padding: 2px 6px; background: var(--bg-muted); border: 1px solid var(--border); border-radius: 4px;">${data}</code>` :
                                '-';
                        }
                    },
                    {
                        data: 'product_name',
                        responsivePriority: 1,
                        render: function(data, type, row) {
                            return `
                                <div style="font-weight: 600; color: var(--t-base);">${data}</div>
                                <div style="font-size: 11px; color: var(--t-muted); max-width: 260px; white-space: normal;">${row.description || ''}</div>
                            `;
                        }
                    },
                    {
                        data: 'category',
                        className: 'text-center',
                        responsivePriority: 3,
                        render: function(data) {
                            return `<span style="display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; background: var(--bg-hover); color: var(--primary); border: 1px solid var(--border);">${data || 'Brosur'}</span>`;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        responsivePriority: 2,
                        render: function(data, type, row) {
                            return `<span style="font-weight: 600;">${row.final_length} × ${row.final_width} cm</span>`;
                        }
                    },
                    {
                        data: 'sheet_per_rim',
                        className: 'text-center',
                        responsivePriority: 2,
                        render: function(data, type, row) {
                            const sheets = data || Math.round(500 / (row.up_per_sheet || 1));
                            return `<span style="display: inline-block; padding: 3px 10px; border-radius: 999px; background: rgba(59, 130, 246, 0.12); color: #2563eb; font-weight: 700; font-size: 12px;">${sheets} lbr <span style="font-size: 11px; font-weight: 500; opacity: 0.85;">(${row.up_per_sheet || 1} Up)</span></span>`;
                        }
                    },
                    {
                        data: 'plano_cut_yield',
                        className: 'text-center',
                        responsivePriority: 3,
                        render: function(data) {
                            return `<span style="color: var(--t-muted); font-size: 13px;">${data || 4} Lembar / Plano</span>`;
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        responsivePriority: 3,
                        render: function(data, type, row) {
                            return `<span style="font-size: 12px; color: var(--t-muted);">${row.print_sheet_length} × ${row.print_sheet_width} cm</span>`;
                        }
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        responsivePriority: 2,
                        render: function(data) {
                            const isAct = (data || '').toLowerCase() === 'active';
                            return '<span class="' + (isAct ? 'badge-active' : 'badge-inactive') +
                                '">' + (data || 'Active') + '</span>';
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                                <div style="display: flex; justify-content: center; gap: 6px;">
                                    <button type="button" class="btn-detail-offset" data-type="product" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <button type="button" class="btn-edit-offset" data-type="product" title="Edit Data">
                                        <i class="bi bi-pen"></i>
                                    </button>
                                    <button type="button" class="btn-delete-offset" data-id="${row.id}" data-type="product" data-name="${row.product_name}" title="Hapus">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],
                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
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
                    zeroRecords: 'Data produk offset tidak ditemukan',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari produk offset...'
                },
                initComplete: function() {
                    $(this).closest('.dt-container, .dataTables_wrapper')
                        .find('.dt-search input, .dataTables_filter input')
                        .css({
                            'padding': '9px 14px',
                            'border-radius': '8px',
                            'border': '1px solid #e2e8f0'
                        });
                }
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

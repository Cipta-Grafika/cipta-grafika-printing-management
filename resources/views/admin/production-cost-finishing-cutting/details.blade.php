@extends('admin_master')

@section('contents')
    @php
        $categoriesList = collect();
        $rawDetails = collect();

        foreach ($categories as $catIdx => $cat) {
            $catNames = collect($cat->category_names ?? [$cat->category_name])->values()->toArray();
            foreach ($catNames as $cn) {
                $categoriesList->push($cn);
            }

            foreach ($cat->details as $d) {
                $formattedPrices = [
                    'general'  => [],
                    'division' => [],
                ];

                foreach (['general', 'division'] as $gKey) {
                    foreach (['tier_1', 'tier_2_5', 'tier_6_50', 'tier_100', 'tier_300', 'tier_500'] as $tKey) {
                        $pObj = $d->prices[$gKey][$tKey] ?? null;
                        $formattedPrices[$gKey][$tKey] = $pObj ? (float) $pObj->price : null;
                    }
                }

                $rawDetails->push([
                    'id'             => $d->id,
                    'cutting_name'   => $d->cutting_name,
                    'category_names' => $catNames,
                    'category_name'  => implode(', ', $catNames),
                    'prices'         => $formattedPrices,
                ]);
            }
        }

        $categoriesList = $categoriesList->unique()->sort()->values()->toArray();
        $hasBc = in_array('BC', $categoriesList);
        $hasBw = in_array('BW', $categoriesList);
        $defaultCat = $hasBc ? 'BC' : ($hasBw ? 'BW' : ($categoriesList[0] ?? 'all'));

        $rawPriceList = [
            'categories' => $categoriesList,
            'details'    => $rawDetails->toArray(),
        ];
    @endphp

    <style>
        /* =========================================================
           SOLID, OPAQUE & MODERN JASA POTONG (CUTTING) MATRIX STYLING
        ========================================================= */
        .matrix-card {
            margin-top: 14px;
            border-radius: 12px;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border);
            background: var(--bg-card);
            overflow: visible;
        }

        .matrix-card-head {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 20px 22px;
            border-bottom: 1px solid var(--border);
            background: var(--bg-card);
            margin-bottom: 0;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }

        .matrix-head-top-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            width: 100%;
            flex-wrap: wrap;
        }

        .matrix-head-title-wrap {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            flex: 1;
            min-width: 260px;
        }

        .matrix-head-icon {
            width: 44px;
            height: 44px;
            border-radius: 11px;
            background: linear-gradient(135deg, rgba(37,99,235,0.14) 0%, rgba(37,99,235,0.04) 100%);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
            border: 1px solid rgba(37,99,235,0.2);
        }

        .matrix-head-text {
            flex: 1;
            min-width: 0;
        }

        .matrix-head-eyebrow-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .matrix-eyebrow {
            color: var(--primary);
            font-weight: 700;
            letter-spacing: 0.6px;
            font-size: 11px;
        }

        .matrix-cat-badge {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
            font-weight: 700;
            border: 1px solid rgba(37, 99, 235, 0.25);
            padding: 2px 8px;
            border-radius: 20px;
            font-size: 11px;
        }

        .matrix-title {
            font-size: 1.18rem;
            font-weight: 700;
            color: var(--t-base);
            margin: 3px 0 2px;
            letter-spacing: -0.01em;
            line-height: 1.3;
        }

        .matrix-subtitle {
            margin: 0;
            font-size: 12.5px;
            color: var(--t-muted);
            line-height: 1.4;
        }

        /* Matrix Toolbar: Structured Two-Row Responsive Layout */
        .matrix-toolbar {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
        }

        .matrix-toolbar-row-main {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            flex-wrap: wrap;
        }

        .matrix-toolbar-row-sub {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            width: 100%;
            padding-top: 4px;
            border-top: 1px dashed var(--border);
        }

        .matrix-filter-group {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .matrix-filter-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--t-muted);
            white-space: nowrap;
            margin: 0;
        }

        .matrix-cat-select-wrap {
            width: 170px;
        }

        .matrix-pagesize-select-wrap {
            width: 110px;
        }

        .matrix-tab-cluster {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex-wrap: nowrap;
        }

        .matrix-tab-cluster-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--t-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        /* Matrix Tabs Segmented Control */
        .matrix-tab-container {
            display: inline-flex;
            gap: 4px;
            background: var(--bg-muted);
            padding: 3px;
            border-radius: 8px;
            border: 1px solid var(--border);
            height: 38px;
            align-items: center;
        }

        .matrix-tab-btn {
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            background: transparent;
            color: var(--t-muted);
            transition: all 0.15s ease;
            white-space: nowrap;
            height: 30px;
            display: inline-flex;
            align-items: center;
        }

        .matrix-tab-btn:hover:not(.is-active) {
            color: var(--t-base);
            background: var(--bg-hover);
        }

        .matrix-tab-btn.is-active {
            background: var(--bg-card);
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }

        /* Toolbar Select2 Styling */
        .toolbar-select2-wrap .select2-container--default .select2-selection--single {
            height: 38px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            border-color: var(--border);
            background-color: var(--bg-card);
            display: flex;
            align-items: center;
        }

        .toolbar-select2-wrap .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 10px;
            padding-right: 24px;
            color: var(--t-base);
        }

        .toolbar-select2-wrap .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 6px;
        }

        .toolbar-select2-wrap .select2-container--default.select2-container--focus .select2-selection--single,
        .toolbar-select2-wrap .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-ring);
        }

        /* Select2 Dropdown Global Dark/Light Theme Compatibility */
        .select2-container--default .select2-dropdown {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border) !important;
            border-radius: 8px !important;
            box-shadow: var(--shadow-lg) !important;
            z-index: 99999 !important;
            overflow: hidden;
        }

        .select2-container--default .select2-results__option {
            color: var(--t-base) !important;
            font-size: 12.5px !important;
            padding: 8px 12px !important;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--primary) !important;
            color: #ffffff !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: var(--bg-muted) !important;
            color: var(--primary) !important;
            font-weight: 600 !important;
        }

        /* Matrix Search Filter with Flex 1 */
        .matrix-search-box {
            position: relative;
            display: inline-flex;
            align-items: center;
            flex: 1 1 200px;
            min-width: 170px;
        }

        .matrix-search-box i.bi-search {
            position: absolute;
            left: 12px;
            color: var(--t-muted);
            font-size: 13px;
            pointer-events: none;
        }

        .matrix-search-input {
            width: 100%;
            height: 38px;
            padding: 6px 30px 6px 34px;
            font-size: 12.5px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--bg-card);
            color: var(--t-base);
            outline: none;
            transition: all 0.15s ease;
        }

        .matrix-search-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-ring);
        }

        .matrix-search-clear {
            position: absolute;
            right: 8px;
            background: none;
            border: none;
            color: var(--t-muted);
            font-size: 16px;
            cursor: pointer;
            padding: 2px 6px;
            line-height: 1;
            display: none;
        }

        .matrix-search-clear:hover {
            color: var(--t-base);
        }

        /* =========================================================
           100% SOLID & OPAQUE ROW HOVER & STICKY CELL BACKGROUNDS
           (ZERO TRANSPARENCY, ZERO BLEED-THROUGH WHEN SCROLLED)
        ========================================================= */
        .details-digital-table td.details-digital-col-material,
        .details-digital-table th.details-digital-col-material {
            box-shadow: 3px 0 6px -2px rgba(0, 0, 0, 0.25);
        }

        .details-digital-table td.details-digital-price {
            font-variant-numeric: tabular-nums;
            font-weight: 700;
        }

        /* 1. Normal State: Sticky Cells Must Be 100% Solid Opaque */
        .details-digital-table thead th.details-digital-sticky {
            position: sticky !important;
            z-index: 20 !important;
            background-color: var(--bg-muted) !important;
        }

        .details-digital-table tbody td.details-digital-sticky {
            position: sticky !important;
            z-index: 5 !important;
            background-color: #141b2d !important;
        }

        :root[data-theme="light"] .details-digital-table tbody td.details-digital-sticky {
            background-color: #ffffff !important;
        }

        /* 2. Hover State: 100% Solid Opaque Hover for Entire Row */
        .details-digital-table tbody tr:hover td {
            background-color: #1c2538 !important;
            background-image: none !important;
        }

        :root[data-theme="light"] .details-digital-table tbody tr:hover td {
            background-color: #f1f5f9 !important;
            background-image: none !important;
        }

        /* 3. Hover State for Sticky Cells: 100% Solid Opaque (No Scrolled Text Bleed-Through) */
        .details-digital-table tbody tr:hover td.details-digital-sticky {
            background-color: #1c2538 !important;
            background-image: none !important;
        }

        :root[data-theme="light"] .details-digital-table tbody tr:hover td.details-digital-sticky {
            background-color: #f1f5f9 !important;
            background-image: none !important;
        }

        /* Table Column Group Headers (Theme Adaptive) */
        .details-digital-table thead th.details-digital-group--general {
            background-color: rgba(245, 158, 11, 0.09) !important;
            color: #92400e !important;
            font-weight: 700;
            border-bottom: 1px solid rgba(245, 158, 11, 0.25);
        }

        .details-digital-table thead th.details-digital-group--division {
            background-color: rgba(139, 92, 246, 0.08) !important;
            color: #5b21b6 !important;
            font-weight: 700;
            border-bottom: 1px solid rgba(139, 92, 246, 0.25);
        }

        :root[data-theme="dark"] .details-digital-table thead th.details-digital-group--general {
            background-color: rgba(245, 158, 11, 0.16) !important;
            color: #fbbf24 !important;
            border-bottom-color: rgba(245, 158, 11, 0.35);
        }

        :root[data-theme="dark"] .details-digital-table thead th.details-digital-group--division {
            background-color: rgba(139, 92, 246, 0.15) !important;
            color: #c4b5fd !important;
            border-bottom-color: rgba(139, 92, 246, 0.35);
        }

        /* Divider Tegas antara Harga Umum dan Harga Divisi */
        .details-digital-table th.details-digital-divider,
        .details-digital-table td.details-digital-divider {
            border-left: 2px solid var(--border) !important;
        }

        /* Pagination Buttons */
        .price-page-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 7px;
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--t-base);
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .price-page-btn:hover:not(:disabled) {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--bg-hover);
        }

        .price-page-btn.is-active {
            background: var(--primary) !important;
            color: #fff !important;
            border-color: var(--primary) !important;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35);
        }

        .price-page-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .col-hide {
            display: none !important;
        }

        /* Responsive Media Queries */
        @media (max-width: 991px) {
            .matrix-card-head {
                padding: 16px 16px;
                gap: 14px;
            }

            .matrix-toolbar-row-main {
                gap: 10px;
            }

            .matrix-search-box {
                flex: 1 1 100%;
                order: 3;
            }

            .matrix-filter-group.matrix-pagesize-group {
                margin-left: auto;
            }
        }

        @media (max-width: 640px) {
            .matrix-cat-select-wrap {
                width: 130px;
            }

            .matrix-pagesize-select-wrap {
                width: 90px;
            }

            .matrix-tab-cluster {
                width: 100%;
                justify-content: space-between;
            }

            .matrix-tab-container {
                flex: 1;
                justify-content: space-between;
            }

            .matrix-tab-btn {
                flex: 1;
                justify-content: center;
                padding: 5px 8px;
                font-size: 11px;
            }
        }
    </style>

    <div class="shell">

        <div data-shell-sidebar></div>

        <div class="main">

            <div data-shell-topbar></div>

            <main class="content">

                {{-- =========================================================
                    HERO
                ========================================================= --}}
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">
                            BIAYA · JASA POTONG (CUTTING)
                        </span>
                        <p class="hero-sub">
                            Detail acuan tarif jasa potong berdasarkan mesin, lokasi, kategori, dan kuantiti lembaran.
                        </p>
                    </div>
                </section>

                <div class="grid">
                    <div class="col-12">
                        <section class="card matrix-card">

                            {{-- CARD HEAD: JUDUL & TOOLBAR DUA BARIS RESPONSIVE --}}
                            <div class="card-head matrix-card-head">

                                {{-- Baris Judul & Tombol Aksi --}}
                                <div class="matrix-head-top-row">
                                    <div class="matrix-head-title-wrap">
                                        <div class="matrix-head-icon">
                                            <i class="bi bi-scissors"></i>
                                        </div>
                                        <div class="matrix-head-text">
                                            <div class="matrix-head-eyebrow-row">
                                                <span class="eyebrow matrix-eyebrow">
                                                    TABEL ACUAN TARIF JASA POTONG · {{ strtoupper($productionCost->engine_name) }}
                                                </span>
                                                <span id="currentCatBadge" class="badge matrix-cat-badge">
                                                    <i class="bi bi-folder2-open me-1"></i> Kategori: {{ $defaultCat }}
                                                </span>
                                                <span class="badge" style="background: rgba(14, 165, 233, 0.1); color: #38bdf8; font-weight: 700; border: 1px solid rgba(14, 165, 233, 0.25); padding: 2px 8px; border-radius: 20px; font-size: 11px;">
                                                    <i class="bi bi-geo-alt-fill me-1"></i> {{ $productionCost->location_name }}{{ $productionCost->vendor_name ? ' · ' . $productionCost->vendor_name : '' }}
                                                </span>
                                            </div>
                                            <h3 class="matrix-title" id="priceListTitle">
                                                Price List Bertingkat Jasa Potong Lembaran
                                            </h3>
                                            <p class="matrix-subtitle" id="priceListSubtitle">
                                                Daftar Rekomendasi Tarif Jasa Potong Bertingkat (Harga Umum &amp; Harga Divisi 80%).
                                            </p>
                                        </div>
                                    </div>

                                    <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                        <a href="{{ route('admin_production_cost_finishing_cuttings') }}" class="btn btn--danger" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; padding: 7px 16px; border-radius: 8px; font-size: 13px; flex-shrink: 0;">
                                            <i class="bi bi-arrow-left"></i>
                                            Kembali
                                        </a>
                                        <a href="{{ route('admin_edit_pc_finishing_cutting', base64_encode($productionCost->id)) }}" class="btn btn--primary" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; padding: 7px 16px; border-radius: 8px; font-size: 13px; flex-shrink: 0;">
                                            <i class="bi bi-pen"></i>
                                            Edit Ongkos
                                        </a>
                                    </div>
                                </div>

                                {{-- TOOLBAR RESPONSIVE --}}
                                <div class="matrix-toolbar">

                                    {{-- BARIS 1: FILTER KATEGORI, SEARCH JASA POTONG (FLEX 1), & JUMLAH BARIS --}}
                                    <div class="matrix-toolbar-row-main">

                                        {{-- 1. Filter Kategori --}}
                                        <div class="matrix-filter-group" id="wrapFilterCategory">
                                            <label for="priceListCategoryFilter" class="matrix-filter-label">
                                                <i class="bi bi-tag-fill text-primary"></i> Kategori:
                                            </label>
                                            <div class="toolbar-select2-wrap matrix-cat-select-wrap">
                                                <select id="priceListCategoryFilter" class="select select2" style="width: 100%;">
                                                    @foreach ($categoriesList as $cat)
                                                        <option value="{{ $cat }}" {{ $cat === $defaultCat ? 'selected' : '' }}>
                                                            {{ $cat }}
                                                        </option>
                                                    @endforeach
                                                    <option value="all">Semua Kategori</option>
                                                </select>
                                            </div>
                                        </div>

                                        {{-- 2. Input Search Filter (Otomatis mengisi ruang tengah / flex-1) --}}
                                        <div class="matrix-search-box">
                                            <i class="bi bi-search"></i>
                                            <input type="text" id="priceListSearch" class="matrix-search-input"
                                                placeholder="Cari nama jasa potong..." autocomplete="off">
                                            <button type="button" class="matrix-search-clear" id="priceListSearchClear" title="Hapus pencarian">&times;</button>
                                        </div>

                                        {{-- 3. Selector Jumlah Baris (Select2) --}}
                                        <div class="matrix-filter-group matrix-pagesize-group">
                                            <label for="priceListPageSize" class="matrix-filter-label">
                                                Baris:
                                            </label>
                                            <div class="toolbar-select2-wrap matrix-pagesize-select-wrap">
                                                <select id="priceListPageSize" class="select select2" style="width: 100%;">
                                                    <option value="5">5 / hal</option>
                                                    <option value="10" selected>10 / hal</option>
                                                    <option value="25">25 / hal</option>
                                                    <option value="all">Semua</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- BARIS 2: TAB SWITCHER TIPE HARGA --}}
                                    <div class="matrix-toolbar-row-sub">

                                        {{-- Tab Filter Tipe Harga (Semua / Umum / Divisi) --}}
                                        <div class="matrix-tab-cluster">
                                            <span class="matrix-tab-cluster-label">Tipe Harga:</span>
                                            <div class="matrix-tab-container" id="priceTypeTabWrap">
                                                <button type="button" class="matrix-tab-btn is-active" data-group="all" title="Tampilkan Semua Harga">
                                                    Semua Harga
                                                </button>
                                                <button type="button" class="matrix-tab-btn" data-group="general" title="Harga Umum Saja">
                                                    Harga Umum
                                                </button>
                                                <button type="button" class="matrix-tab-btn" data-group="division" title="Harga Divisi Saja">
                                                    Harga Divisi
                                                </button>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>

                            {{-- CARD BODY: TABEL PRICE LIST BERTINGKAT JASA POTONG --}}
                            <div style="padding: 16px 20px;">
                                <div class="details-digital-table-wrapper" id="sheetTableWrapper" style="max-height: 65vh;">
                                    <table class="details-digital-table details-digital-cutting-table" id="cuttingPriceListTable">
                                        <thead>
                                            <tr class="details-digital-group-row">
                                                <th rowspan="2" class="details-digital-sticky details-digital-col-no" style="font-weight: 700;">No</th>
                                                <th rowspan="2" class="details-digital-sticky details-digital-col-material" style="font-weight: 700;">Item Jasa Potong</th>

                                                <th colspan="6" class="details-digital-group details-digital-group--general col-tier-gen" style="font-weight: 700;">
                                                    Harga Umum
                                                </th>
                                                <th colspan="6" class="details-digital-group details-digital-group--division details-digital-divider col-tier-div" style="font-weight: 700;">
                                                    Harga Divisi (80%)
                                                </th>
                                            </tr>

                                            <tr class="details-digital-tier-row">
                                                {{-- General Tiers --}}
                                                <th class="details-digital-group--general col-tier-gen" style="font-weight: 600;">1 lbr</th>
                                                <th class="details-digital-group--general col-tier-gen" style="font-weight: 600;">2-5 lbr</th>
                                                <th class="details-digital-group--general col-tier-gen" style="font-weight: 600;">6-50 lbr</th>
                                                <th class="details-digital-group--general col-tier-gen" style="font-weight: 600;">100 lbr</th>
                                                <th class="details-digital-group--general col-tier-gen" style="font-weight: 600;">300 lbr</th>
                                                <th class="details-digital-group--general col-tier-gen" style="font-weight: 600;">500 lbr</th>

                                                {{-- Division Tiers --}}
                                                <th class="details-digital-group--division details-digital-divider col-tier-div" style="font-weight: 600;">1 lbr</th>
                                                <th class="details-digital-group--division col-tier-div" style="font-weight: 600;">2-5 lbr</th>
                                                <th class="details-digital-group--division col-tier-div" style="font-weight: 600;">6-50 lbr</th>
                                                <th class="details-digital-group--division col-tier-div" style="font-weight: 600;">100 lbr</th>
                                                <th class="details-digital-group--division col-tier-div" style="font-weight: 600;">300 lbr</th>
                                                <th class="details-digital-group--division col-tier-div" style="font-weight: 600;">500 lbr</th>
                                            </tr>
                                        </thead>
                                        <tbody id="cuttingPriceListBody">
                                            {{-- Rows populated via JavaScript --}}
                                        </tbody>
                                    </table>
                                </div>

                                {{-- CARD FOOTER: INFO JUMLAH & PAGINATION RESPONSIVE --}}
                                <div class="matrix-card-footer" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--border);">
                                    <div id="priceListInfo" style="font-size: 12.5px; color: var(--t-muted);">
                                        Memuat data tarif jasa potong...
                                    </div>
                                    <div id="priceListPagination" style="display: flex; gap: 6px; align-items: center;">
                                        {{-- Pagination buttons --}}
                                    </div>
                                </div>
                            </div>

                        </section>
                    </div>
                </div>

            </main>
        </div>
    </div>

    {{-- =========================================================
        INTERACTIVE CLIENT-SIDE JAVASCRIPT
        (FILTER, PENCARIAN REAL-TIME, PAGINASI & TIPE HARGA)
    ========================================================= --}}
    <script>
        $(function() {
            const priceListRaw = @json($rawPriceList);

            let plState = {
                category: '{{ $defaultCat }}',
                group: 'all', // 'all' | 'general' | 'division'
                search: '',
                page: 1,
                pageSize: 10
            };

            const plCatSelect = $('#priceListCategoryFilter');
            const plPageSizeSelect = $('#priceListPageSize');
            const plSearchInput = document.getElementById('priceListSearch');
            const plSearchClear = document.getElementById('priceListSearchClear');
            const plTableBody = document.getElementById('cuttingPriceListBody');
            const plInfo = document.getElementById('priceListInfo');
            const plPagination = document.getElementById('priceListPagination');
            const currentCatBadge = document.getElementById('currentCatBadge');

            // Format Rupiah Helper
            function formatPlRupiah(val) {
                if (val === null || val === undefined || isNaN(val) || Number(val) <= 0) {
                    return '<span style="color:var(--t-muted); opacity:0.4;">-</span>';
                }
                return 'Rp ' + Math.round(Number(val)).toLocaleString('id-ID');
            }

            // Update Visibility Kolom Tipe Harga
            function updateColumnVisibility() {
                const table = document.getElementById('cuttingPriceListTable');
                if (!table) return;

                const gGen = table.querySelectorAll('.col-tier-gen');
                const gDiv = table.querySelectorAll('.col-tier-div');

                const showGen = (plState.group === 'all' || plState.group === 'general');
                const showDiv = (plState.group === 'all' || plState.group === 'division');

                gGen.forEach(el => el.classList.toggle('col-hide', !showGen));
                gDiv.forEach(el => el.classList.toggle('col-hide', !showDiv));
            }

            // Render Body Tabel Jasa Potong
            function renderCuttingPriceList() {
                if (!plTableBody || !priceListRaw || !Array.isArray(priceListRaw.details)) {
                    if (plTableBody) {
                        plTableBody.innerHTML = '<tr><td colspan="14" style="text-align:center; padding:28px; color:var(--t-muted);">Data tarif jasa potong belum tersedia.</td></tr>';
                    }
                    return;
                }

                const filtered = priceListRaw.details.filter(item => {
                    const matchCat = (plState.category === 'all') || (item.category_names && item.category_names.includes(plState.category));
                    const matchSearch = (!plState.search) ||
                        (item.cutting_name && item.cutting_name.toLowerCase().includes(plState.search.toLowerCase())) ||
                        (item.category_name && item.category_name.toLowerCase().includes(plState.search.toLowerCase()));
                    return matchCat && matchSearch;
                });

                const totalItems = filtered.length;
                const pageSize = plState.pageSize === 'all' ? (totalItems || 1) : parseInt(plState.pageSize, 10);
                const totalPages = Math.ceil(totalItems / pageSize) || 1;

                if (plState.page > totalPages) plState.page = totalPages;
                if (plState.page < 1) plState.page = 1;

                const startIdx = (plState.page - 1) * pageSize;
                const endIdx = plState.pageSize === 'all' ? totalItems : Math.min(startIdx + pageSize, totalItems);
                const pageItems = filtered.slice(startIdx, endIdx);

                if (currentCatBadge) {
                    currentCatBadge.innerHTML = `<i class="bi bi-folder2-open me-1"></i> Kategori: ${plState.category === 'all' ? 'Semua' : plState.category}`;
                }

                if (pageItems.length === 0) {
                    plTableBody.innerHTML = `
                        <tr>
                            <td colspan="14" style="text-align: center; padding: 42px 16px; color: var(--t-muted);">
                                <div style="font-size: 28px; margin-bottom: 8px; color: var(--t-muted); opacity: 0.6;"><i class="bi bi-inbox-fill"></i></div>
                                <div style="font-size: 14px; font-weight: 700; color: var(--t-base); margin-bottom: 4px;">Jasa Potong Tidak Ditemukan</div>
                                <div style="font-size: 12.5px;">Tidak ada jasa potong yang cocok dengan filter kategori atau kata kunci pencarian.</div>
                            </td>
                        </tr>
                    `;
                } else {
                    let tbodyHtml = '';
                    const tierKeys = ['tier_1', 'tier_2_5', 'tier_6_50', 'tier_100', 'tier_300', 'tier_500'];

                    pageItems.forEach((item, idx) => {
                        const no = startIdx + idx + 1;
                        let tr = `<tr>`;
                        tr += `<td class="details-digital-sticky details-digital-col-no" style="text-align:center; color:var(--t-muted); font-size:12px;">${no}</td>`;
                        tr += `<td class="details-digital-sticky details-digital-col-material font-semibold" style="font-weight:700; color:var(--t-base);">
                            <div style="display:flex; flex-direction:column; gap:3px; width:100%; min-width:0; max-width:100%;">
                                <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; width:100%; min-width:0;">
                                    <span style="flex:1 1 auto; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="${item.cutting_name}">${item.cutting_name}</span>
                                    ${plState.category === 'all' ? `<span class="badge" style="font-size:10px; font-weight:600; padding:2px 7px; background:var(--bg-muted); color:var(--t-muted); border:1px solid var(--border); border-radius:4px; flex-shrink:0; max-width:140px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="${item.category_name}">${item.category_name}</span>` : ''}
                                </div>
                            </div>
                        </td>`;

                        // General Tiers
                        tierKeys.forEach(t => {
                            const p = (item.prices && item.prices.general) ? item.prices.general[t] : null;
                            tr += `<td class="details-digital-price details-digital-group--general col-tier-gen" style="text-align:right;">${formatPlRupiah(p)}</td>`;
                        });

                        // Division Tiers
                        tierKeys.forEach((t, tIdx) => {
                            const p = (item.prices && item.prices.division) ? item.prices.division[t] : null;
                            const dividerCls = (tIdx === 0) ? 'details-digital-divider' : '';
                            tr += `<td class="details-digital-price details-digital-group--division col-tier-div ${dividerCls}" style="text-align:right;">${formatPlRupiah(p)}</td>`;
                        });

                        tr += `</tr>`;
                        tbodyHtml += tr;
                    });

                    plTableBody.innerHTML = tbodyHtml;
                }

                updateColumnVisibility();

                const catLabel = plState.category === 'all' ? 'Semua Kategori' : `Kategori: ${plState.category}`;
                if (totalItems > 0) {
                    plInfo.innerHTML = `<i class="bi bi-info-circle me-1 text-primary"></i> Menampilkan <strong>${startIdx + 1}</strong> sampai <strong>${endIdx}</strong> dari <strong>${totalItems}</strong> jasa potong (${catLabel})`;
                } else {
                    plInfo.innerHTML = `<i class="bi bi-info-circle me-1 text-muted"></i> Tidak ada jasa potong (${catLabel})`;
                }

                renderPaginationButtons(totalPages, totalItems);
            }

            // Render Pagination Buttons
            function renderPaginationButtons(totalPages, totalItems) {
                if (!plPagination) return;
                if (totalItems <= 0 || totalPages <= 1 || plState.pageSize === 'all') {
                    plPagination.innerHTML = '';
                    return;
                }

                let pagHtml = '';
                // Prev Button
                pagHtml += `<button type="button" class="price-page-btn" ${plState.page <= 1 ? 'disabled' : ''} data-page="${plState.page - 1}" title="Halaman Sebelumnya"><i class="bi bi-chevron-left"></i></button>`;

                const maxBtns = 5;
                let startPage = Math.max(1, plState.page - 2);
                let endPage = Math.min(totalPages, startPage + maxBtns - 1);
                if (endPage - startPage < maxBtns - 1) {
                    startPage = Math.max(1, endPage - maxBtns + 1);
                }

                if (startPage > 1) {
                    pagHtml += `<button type="button" class="price-page-btn" data-page="1">1</button>`;
                    if (startPage > 2) pagHtml += `<span style="color:var(--t-muted); padding:0 3px;">...</span>`;
                }

                for (let p = startPage; p <= endPage; p++) {
                    pagHtml += `<button type="button" class="price-page-btn ${p === plState.page ? 'is-active' : ''}" data-page="${p}">${p}</button>`;
                }

                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) pagHtml += `<span style="color:var(--t-muted); padding:0 3px;">...</span>`;
                    pagHtml += `<button type="button" class="price-page-btn" data-page="${totalPages}">${totalPages}</button>`;
                }

                // Next Button
                pagHtml += `<button type="button" class="price-page-btn" ${plState.page >= totalPages ? 'disabled' : ''} data-page="${plState.page + 1}" title="Halaman Selanjutnya"><i class="bi bi-chevron-right"></i></button>`;

                plPagination.innerHTML = pagHtml;

                // Event Listeners for Pagination
                plPagination.querySelectorAll('.price-page-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const targetPage = parseInt(this.getAttribute('data-page'), 10);
                        if (!isNaN(targetPage) && targetPage >= 1 && targetPage <= totalPages && targetPage !== plState.page) {
                            plState.page = targetPage;
                            renderCuttingPriceList();
                        }
                    });
                });
            }

            // Inisialisasi Select2 pada Toolbar
            if (plCatSelect.length) {
                plCatSelect.select2({
                    width: '100%',
                    placeholder: 'Pilih Kategori'
                }).on('change', function() {
                    plState.category = $(this).val();
                    plState.page = 1;
                    renderCuttingPriceList();
                });
            }

            if (plPageSizeSelect.length) {
                plPageSizeSelect.select2({
                    width: '100%',
                    minimumResultsForSearch: -1
                }).on('change', function() {
                    plState.pageSize = $(this).val();
                    plState.page = 1;
                    renderCuttingPriceList();
                });
            }

            // Tipe Harga Tabs (Semua / Umum / Divisi)
            const priceTypeTabs = document.querySelectorAll('#priceTypeTabWrap .matrix-tab-btn');
            priceTypeTabs.forEach(btn => {
                btn.addEventListener('click', function() {
                    priceTypeTabs.forEach(b => b.classList.remove('is-active'));
                    this.classList.add('is-active');
                    plState.group = this.getAttribute('data-group') || 'all';
                    updateColumnVisibility();
                });
            });

            // Search Filter Real-Time
            if (plSearchInput) {
                plSearchInput.addEventListener('input', function() {
                    plState.search = this.value.trim();
                    plState.page = 1;
                    if (plSearchClear) {
                        plSearchClear.style.display = plState.search ? 'inline-block' : 'none';
                    }
                    renderCuttingPriceList();
                });
            }

            if (plSearchClear) {
                plSearchClear.addEventListener('click', function() {
                    if (plSearchInput) {
                        plSearchInput.value = '';
                        plState.search = '';
                        plState.page = 1;
                        this.style.display = 'none';
                        plSearchInput.focus();
                        renderCuttingPriceList();
                    }
                });
            }

            // Initial Render
            renderCuttingPriceList();
        });
    </script>
@endsection

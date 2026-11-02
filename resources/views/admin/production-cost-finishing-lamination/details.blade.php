@extends('admin_master')

@section('contents')
    @php
        $allLaminations = collect();
        $categoriesList = collect();
        $locationsList = collect();

        foreach ($locations as $loc) {
            $locName = $loc->location_name;
            $locationsList->push($locName);

            // Non-outsourcing: Location -> Category -> Details
            if (isset($loc->categories) && count($loc->categories) > 0) {
                foreach ($loc->categories as $cat) {
                    $categoriesList->push($cat->category_name);
                    foreach ($cat->details as $d) {
                        $sizeLabels = collect($d->sizes ?? [])->map(function ($s) {
                            $w = rtrim(rtrim(number_format((float) ($s->width ?? 0), 2, '.', ''), '0'), '.');
                            $u = $s->unit ?: 'cm';
                            if (!empty($s->length) && (float)$s->length > 0) {
                                $l = rtrim(rtrim(number_format((float) $s->length, 2, '.', ''), '0'), '.');
                                return "{$w} x {$l} {$u}";
                            }
                            return "{$w} {$u}";
                        })->values()->toArray();

                        $allLaminations->push([
                            'location_name'   => $locName,
                            'category_name'   => $cat->category_name,
                            'lamination_name' => $d->lamination_name,
                            'vendor_name'     => null,
                            'sizes'           => $sizeLabels,
                            'sizes_label'     => !empty($sizeLabels) ? implode(', ', $sizeLabels) : '-',
                            'price_per_meter' => (float) ($d->price_per_meter ?? 0),
                            'production_cost' => (float) ($d->production_cost ?? 0),
                            'finishing_cost'  => (float) ($d->finishing_cost ?? 0),
                            'total_cost'      => (float) ($d->total_cost ?? 0),
                            'general_price'   => (float) ($d->general_price ?? 0),
                            'division_price'  => (float) ($d->division_price ?? 0),
                            'plain_price'     => (float) ($d->plain_price ?? 0),
                        ]);
                    }
                }
            }

            // Outsourcing: Location -> Vendor -> Category -> Details
            if (isset($loc->vendors) && count($loc->vendors) > 0) {
                foreach ($loc->vendors as $v) {
                    foreach ($v->categories as $cat) {
                        $categoriesList->push($cat->category_name);
                        foreach ($cat->details as $d) {
                            $sizeLabels = collect($d->sizes ?? [])->map(function ($s) {
                                $w = rtrim(rtrim(number_format((float) ($s->width ?? 0), 2, '.', ''), '0'), '.');
                                $u = $s->unit ?: 'cm';
                                if (!empty($s->length) && (float)$s->length > 0) {
                                    $l = rtrim(rtrim(number_format((float) $s->length, 2, '.', ''), '0'), '.');
                                    return "{$w} x {$l} {$u}";
                                }
                                return "{$w} {$u}";
                            })->values()->toArray();

                            $allLaminations->push([
                                'location_name'   => $locName,
                                'category_name'   => $cat->category_name,
                                'lamination_name' => $d->lamination_name,
                                'vendor_name'     => $v->vendor_name,
                                'sizes'           => $sizeLabels,
                                'sizes_label'     => !empty($sizeLabels) ? implode(', ', $sizeLabels) : '-',
                                'price_per_meter' => (float) ($d->vendor_price ?? 0),
                                'production_cost' => 0,
                                'finishing_cost'  => 0,
                                'total_cost'      => (float) ($d->vendor_price ?? 0),
                                'general_price'   => (float) ($d->general_price ?? 0),
                                'division_price'  => (float) ($d->division_price ?? 0),
                                'plain_price'     => (float) ($d->plain_price ?? 0),
                            ]);
                        }
                    }
                }
            }
        }

        $categoriesList = $categoriesList->unique()->values()->toArray();
        $locationsList  = $locationsList->unique()->values()->toArray();
        $defaultCat     = $categoriesList[0] ?? 'all';
    @endphp

    <style>
        /* =========================================================
           SOLID, OPAQUE & MODERN LARGE FORMAT MATRIX STYLING
           (IDENTIK DENGAN DETAIL MATERIAL LARGE FORMAT)
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
           SOLID, OPAQUE & PERFECTLY ALIGNED LARGE FORMAT TABLE
           (NO OVERLAP / TRANSPARENCY)
        ========================================================= */
        .details-digital-table-wrapper {
            width: 100%;
            max-width: 100%;
            overflow: auto;
            border: 1px solid var(--border);
            border-radius: 10px;
            background-color: #141b2d !important;
            -webkit-overflow-scrolling: touch;
        }

        :root[data-theme="light"] .details-digital-table-wrapper {
            background-color: #ffffff !important;
        }

        .details-digital-table-wrapper::-webkit-scrollbar-corner {
            background-color: transparent !important;
        }

        #laminationPriceListTable {
            border-collapse: separate !important;
            border-spacing: 0 !important;
            width: max-content;
            min-width: 100%;
            font-size: 12.5px;
            color: var(--t-base);
            background-color: #141b2d !important;
        }

        :root[data-theme="light"] #laminationPriceListTable {
            background-color: #ffffff !important;
        }

        /* Base Header & Cell Styling */
        #laminationPriceListTable th,
        #laminationPriceListTable td {
            box-sizing: border-box !important;
            white-space: nowrap !important;
            border-right: 1px solid var(--border) !important;
            border-bottom: 1px solid var(--border) !important;
            vertical-align: middle !important;
            padding: 9px 14px !important;
        }

        #laminationPriceListTable thead th {
            font-size: 11.5px;
        }

        /* 1. Header Row 1 (Group Row: Biaya Produksi & Rekomendasi Harga Jual) - 42px Solid */
        #laminationPriceListTable thead tr:nth-child(1) {
            height: 42px !important;
        }

        #laminationPriceListTable thead tr:nth-child(1) th {
            position: sticky !important;
            top: 0 !important;
            height: 42px !important;
            max-height: 42px !important;
            line-height: 42px !important;
            padding: 0 14px !important;
            background-color: #172033 !important;
            color: #f1f5f9 !important;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            z-index: 12 !important;
        }

        :root[data-theme="light"] #laminationPriceListTable thead tr:nth-child(1) th {
            background-color: #e2e8f0 !important;
            color: #1e293b !important;
            border-bottom: 1px solid #cbd5e1 !important;
        }

        /* 2. Header Row 2 (Sub-Headers) - Pinned at top: 42px exactly, 38px Solid */
        #laminationPriceListTable thead tr:nth-child(2) {
            height: 38px !important;
        }

        #laminationPriceListTable thead tr:nth-child(2) th {
            position: sticky !important;
            top: 42px !important;
            height: 38px !important;
            max-height: 38px !important;
            line-height: 38px !important;
            padding: 0 14px !important;
            background-color: #121826 !important;
            color: #94a3b8 !important;
            font-weight: 600;
            z-index: 11 !important;
        }

        :root[data-theme="light"] #laminationPriceListTable thead tr:nth-child(2) th {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border-bottom: 1px solid #cbd5e1 !important;
        }

        /* 3. Rowspan=2 Cells (No, Item Laminasi, Ukuran Tersedia) - 80px Solid spanning row 1 & 2 */
        #laminationPriceListTable thead th[rowspan="2"] {
            top: 0 !important;
            height: 80px !important;
            max-height: 80px !important;
            line-height: normal !important;
            vertical-align: middle !important;
            z-index: 15 !important;
            background-color: #172033 !important;
            color: #f8fafc !important;
        }

        :root[data-theme="light"] #laminationPriceListTable thead th[rowspan="2"] {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }

        /* 4. Left Sticky Columns (No & Item Laminasi) */
        #laminationPriceListTable .details-digital-col-no {
            left: 0 !important;
            width: 44px;
            min-width: 44px;
            max-width: 44px;
            text-align: center;
        }

        #laminationPriceListTable .details-digital-col-material {
            left: 44px !important;
            width: 320px;
            min-width: 300px;
            max-width: 360px;
            text-align: left;
            box-shadow: 3px 0 6px -2px rgba(0, 0, 0, 0.25);
            overflow: hidden;
        }

        #laminationPriceListTable thead th.details-digital-sticky {
            z-index: 20 !important;
        }

        #laminationPriceListTable tbody td.details-digital-sticky {
            position: sticky !important;
            z-index: 5 !important;
            background-color: #141b2d !important;
        }

        :root[data-theme="light"] #laminationPriceListTable tbody td.details-digital-sticky {
            background-color: #ffffff !important;
        }

        /* SOLID OPAQUE HOVER (TIDAK ADA TRANSPARANSI) */
        #laminationPriceListTable tbody tr:hover td {
            background-color: #1c2538 !important;
            background-image: none !important;
        }

        #laminationPriceListTable tbody tr:hover td.details-digital-sticky {
            background-color: #1c2538 !important;
            background-image: none !important;
        }

        :root[data-theme="light"] #laminationPriceListTable tbody tr:hover td {
            background-color: #f1f5f9 !important;
            background-image: none !important;
        }

        :root[data-theme="light"] #laminationPriceListTable tbody tr:hover td.details-digital-sticky {
            background-color: #f1f5f9 !important;
            background-image: none !important;
        }

        /* 5. 100% Solid Colors for Selling Price Headers */
        #laminationPriceListTable thead th.col-price-general {
            background-color: #2b1f08 !important;
            color: #fbbf24 !important;
            font-weight: 700;
            border-bottom: 2px solid #f59e0b !important;
        }

        :root[data-theme="light"] #laminationPriceListTable thead th.col-price-general {
            background-color: #fef3c7 !important;
            color: #92400e !important;
            border-bottom: 2px solid #d97706 !important;
        }

        #laminationPriceListTable thead th.col-price-division {
            background-color: #1e172e !important;
            color: #c4b5fd !important;
            font-weight: 700;
            border-bottom: 2px solid #8b5cf6 !important;
        }

        :root[data-theme="light"] #laminationPriceListTable thead th.col-price-division {
            background-color: #ede9fe !important;
            color: #5b21b6 !important;
            border-bottom: 2px solid #7c3aed !important;
        }

        #laminationPriceListTable thead th.col-price-plain {
            background-color: #0f2231 !important;
            color: #7dd3fc !important;
            font-weight: 700;
            border-bottom: 2px solid #0ea5e9 !important;
        }

        :root[data-theme="light"] #laminationPriceListTable thead th.col-price-plain {
            background-color: #e0f2fe !important;
            color: #0369a1 !important;
            border-bottom: 2px solid #0284c7 !important;
        }

        /* 6. Body Cells Color Coding for Selling Prices */
        #laminationPriceListTable td.details-digital-price {
            font-variant-numeric: tabular-nums;
            font-weight: 700;
        }

        #laminationPriceListTable td.details-digital-cost {
            font-variant-numeric: tabular-nums;
        }

        #laminationPriceListTable tbody td.col-price-general {
            color: #fbbf24 !important;
            font-weight: 700;
        }

        :root[data-theme="light"] #laminationPriceListTable tbody td.col-price-general {
            color: #b45309 !important;
        }

        #laminationPriceListTable tbody td.col-price-division {
            color: #c4b5fd !important;
            font-weight: 700;
        }

        :root[data-theme="light"] #laminationPriceListTable tbody td.col-price-division {
            color: #6d28d9 !important;
        }

        #laminationPriceListTable tbody td.col-price-plain {
            color: #7dd3fc !important;
            font-weight: 700;
        }

        :root[data-theme="light"] #laminationPriceListTable tbody td.col-price-plain {
            color: #0284c7 !important;
        }

        .col-hide {
            display: none !important;
        }

        /* Large Format Size Badges */
        .lf-width-badge {
            display: inline-block;
            padding: 2px 7px;
            background: var(--bg-muted);
            border-radius: 5px;
            font-size: 11.5px;
            color: var(--t-muted);
            border: 1px solid var(--border);
            margin: 1px 2px;
            white-space: nowrap;
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

        .price-page-btn:hover:not(:disabled):not(.is-active) {
            background: var(--bg-hover);
            border-color: var(--primary);
            color: var(--primary);
        }

        .price-page-btn.is-active {
            background: var(--primary);
            border-color: var(--primary);
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
        }

        .price-page-btn:disabled {
            opacity: 0.35;
            cursor: not-allowed;
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
                            BIAYA · LAMINASI LARGE FORMAT
                        </span>
                        <p class="hero-sub">
                            Detail acuan ongkos produksi (HPP) dan rekomendasi harga jual laminasi large format.
                        </p>
                    </div>
                </section>

                <div class="grid">
                    <div class="col-12">
                        <section class="card matrix-card">

                            {{-- CARD HEAD: JUDUL & TOOLBAR DUA BARIS RESPONSIVE --}}
                            <div class="card-head matrix-card-head">

                                {{-- Baris Judul & Tombol Kembali --}}
                                <div class="matrix-head-top-row">
                                    <div class="matrix-head-title-wrap">
                                        <div class="matrix-head-icon">
                                            <i class="bi bi-grid-3x3-gap-fill"></i>
                                        </div>
                                        <div class="matrix-head-text">
                                            <div class="matrix-head-eyebrow-row">
                                                <span class="eyebrow matrix-eyebrow">
                                                    TABEL ACUAN BIAYA &amp; HARGA JUAL RESMI · {{ strtoupper($engine->name) }}
                                                </span>
                                                <span id="currentCatBadge" class="badge matrix-cat-badge">
                                                    <i class="bi bi-folder2-open me-1"></i> Kategori: {{ $defaultCat }}
                                                </span>
                                                @if (count($locationsList) === 1)
                                                    <span class="badge" style="background: rgba(14, 165, 233, 0.1); color: #38bdf8; font-weight: 700; border: 1px solid rgba(14, 165, 233, 0.25); padding: 2px 8px; border-radius: 20px; font-size: 11px;">
                                                        <i class="bi bi-geo-alt-fill me-1"></i> Lokasi: {{ $locationsList[0] }}
                                                    </span>
                                                @endif
                                            </div>
                                            <h3 class="matrix-title" id="priceListTitle">
                                                Price List Laminasi Large Format
                                            </h3>
                                            <p class="matrix-subtitle" id="priceListSubtitle">
                                                Daftar HPP (Bahan &amp; Ongkos Laminasi) dan Rekomendasi Harga Jual (Harga Umum, Divisi, &amp; Polos).
                                            </p>
                                        </div>
                                    </div>

                                    <a href="{{ route('admin_pc_finishing_laminations') }}" class="btn btn--danger" style="display: inline-flex; align-items: center; gap: 6px; font-weight: 600; padding: 7px 16px; border-radius: 8px; font-size: 13px; flex-shrink: 0;">
                                        <i class="bi bi-arrow-left"></i>
                                        Kembali
                                    </a>
                                </div>

                                {{-- TOOLBAR RESPONSIVE --}}
                                <div class="matrix-toolbar">

                                    {{-- BARIS 1: FILTER KATEGORI, (LOKASI), SEARCH MATERIAL (FLEX 1), & JUMLAH BARIS --}}
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

                                        {{-- 2. Filter Lokasi (Hanya muncul jika lebih dari 1 lokasi) --}}
                                        @if (count($locationsList) > 1)
                                            <div class="matrix-filter-group" id="wrapFilterLocation">
                                                <label for="priceListLocationFilter" class="matrix-filter-label">
                                                    <i class="bi bi-geo-alt-fill text-primary"></i> Lokasi:
                                                </label>
                                                <div class="toolbar-select2-wrap matrix-cat-select-wrap">
                                                    <select id="priceListLocationFilter" class="select select2" style="width: 100%;">
                                                        <option value="all" selected>Semua Lokasi</option>
                                                        @foreach ($locationsList as $loc)
                                                            <option value="{{ $loc }}">{{ $loc }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- 3. Input Search Filter (Otomatis mengisi ruang tengah / flex-1) --}}
                                        <div class="matrix-search-box">
                                            <i class="bi bi-search"></i>
                                            <input type="text" id="priceListSearch" class="matrix-search-input"
                                                placeholder="Cari nama laminasi / ukuran..." autocomplete="off">
                                            <button type="button" class="matrix-search-clear" id="priceListSearchClear" title="Hapus pencarian">&times;</button>
                                        </div>

                                        {{-- 4. Selector Jumlah Baris (Select2) --}}
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
                                                <button type="button" class="matrix-tab-btn" id="btnPriceTypePlain" data-group="plain" title="Harga Polos Saja">
                                                    Harga Polos
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                            {{-- CARD BODY: TABEL PRICE LIST LAMINASI LARGE FORMAT --}}
                            <div style="padding: 16px 20px;">
                                <div class="details-digital-table-wrapper" id="rollTableWrapper" style="max-height: 65vh;">
                                    <table class="details-digital-table" id="laminationPriceListTable">
                                        <thead>
                                            <tr class="details-digital-group-row">
                                                <th rowspan="2" class="details-digital-sticky details-digital-col-no" style="font-weight: 700;">No</th>
                                                <th rowspan="2" class="details-digital-sticky details-digital-col-material" style="font-weight: 700;">Item Laminasi</th>
                                                <th rowspan="2" style="font-weight: 700; text-align: center; min-width: 140px;">Ukuran Tersedia</th>

                                                <th colspan="4" class="details-digital-cost" style="font-weight: 700; text-align: center;">
                                                    Biaya Produksi (HPP) / m²
                                                </th>

                                                <th colspan="3" class="details-digital-group col-price-group-all" style="font-weight: 700; text-align: center;">
                                                    Rekomendasi Harga Jual / m²
                                                </th>
                                            </tr>

                                            <tr class="details-digital-tier-row">
                                                {{-- Biaya Sub-Headers --}}
                                                <th class="details-digital-cost" style="font-weight: 600; text-align: right;">Bahan</th>
                                                <th class="details-digital-cost" style="font-weight: 600; text-align: right;">Produksi</th>
                                                <th class="details-digital-cost" style="font-weight: 600; text-align: right;">Finishing</th>
                                                <th class="details-digital-cost" style="font-weight: 700; text-align: right;">Total Cost</th>

                                                {{-- Harga Jual Sub-Headers --}}
                                                <th class="col-price-general" style="font-weight: 700; text-align: right;">Harga Umum</th>
                                                <th class="col-price-division" style="font-weight: 700; text-align: right;">Harga Divisi</th>
                                                <th class="col-price-plain" style="font-weight: 700; text-align: right;">Harga Polos</th>
                                            </tr>
                                        </thead>
                                        <tbody id="laminationPriceListBody">
                                            {{-- Rows populated via JavaScript --}}
                                        </tbody>
                                    </table>
                                </div>

                                {{-- CARD FOOTER: INFO JUMLAH & PAGINATION RESPONSIVE --}}
                                <div class="matrix-card-footer" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--border);">
                                    <div id="priceListInfo" style="font-size: 12.5px; color: var(--t-muted);">
                                        Memuat data price list...
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
        (FILTER, PENCARIAN REAL-TIME, PAGINASI, TIPE HARGA)
    ========================================================= --}}
    <script>
        $(function() {
            const rawDetails = @json($allLaminations);

            let plState = {
                location: 'all',
                category: '{{ $defaultCat }}',
                group: 'all', // 'all' | 'general' | 'division' | 'plain'
                search: '',
                page: 1,
                pageSize: 10
            };

            const plCatSelect = $('#priceListCategoryFilter');
            const plLocSelect = $('#priceListLocationFilter');
            const plPageSizeSelect = $('#priceListPageSize');
            const plSearchInput = document.getElementById('priceListSearch');
            const plSearchClear = document.getElementById('priceListSearchClear');
            const plTableBody = document.getElementById('laminationPriceListBody');
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

            // Update Kolom Tampil / Sembunyi berdasarkan Tipe Harga
            function updateColumnVisibility() {
                const table = document.getElementById('laminationPriceListTable');
                if (!table) return;

                const gGen = table.querySelectorAll('.col-price-general');
                const gDiv = table.querySelectorAll('.col-price-division');
                const gPlain = table.querySelectorAll('.col-price-plain');

                const showGen = (plState.group === 'all' || plState.group === 'general');
                const showDiv = (plState.group === 'all' || plState.group === 'division');
                const showPlain = (plState.group === 'all' || plState.group === 'plain');

                gGen.forEach(el => el.classList.toggle('col-hide', !showGen));
                gDiv.forEach(el => el.classList.toggle('col-hide', !showDiv));
                gPlain.forEach(el => el.classList.toggle('col-hide', !showPlain));

                const visibleCount = (showGen ? 1 : 0) + (showDiv ? 1 : 0) + (showPlain ? 1 : 0);
                const groupTh = table.querySelector('.col-price-group-all');
                if (groupTh) {
                    groupTh.setAttribute('colspan', visibleCount > 0 ? visibleCount : 1);
                    groupTh.classList.toggle('col-hide', visibleCount === 0);
                }
            }

            // Render Body Tabel dan Pagination
            function renderPriceList() {
                if (!plTableBody || !rawDetails || !Array.isArray(rawDetails)) {
                    if (plTableBody) {
                        plTableBody.innerHTML = '<tr><td colspan="10" style="text-align:center; padding:28px; color:var(--t-muted);">Data price list belum tersedia.</td></tr>';
                    }
                    return;
                }

                // 1. Filter Lokasi, Kategori & Search
                const filtered = rawDetails.filter(item => {
                    const matchLoc = (plState.location === 'all') || (item.location_name === plState.location);
                    const matchCat = (plState.category === 'all') || (item.category_name === plState.category);
                    const matchSearch = (!plState.search) ||
                        (item.lamination_name && item.lamination_name.toLowerCase().includes(plState.search.toLowerCase())) ||
                        (item.sizes_label && item.sizes_label.toLowerCase().includes(plState.search.toLowerCase())) ||
                        (item.vendor_name && item.vendor_name.toLowerCase().includes(plState.search.toLowerCase()));
                    return matchLoc && matchCat && matchSearch;
                });

                // 2. Pagination Slice
                const totalItems = filtered.length;
                const pageSize = plState.pageSize === 'all' ? (totalItems || 1) : parseInt(plState.pageSize, 10);
                const totalPages = Math.ceil(totalItems / pageSize) || 1;

                if (plState.page > totalPages) plState.page = totalPages;
                if (plState.page < 1) plState.page = 1;

                const startIdx = (plState.page - 1) * pageSize;
                const endIdx = plState.pageSize === 'all' ? totalItems : Math.min(startIdx + pageSize, totalItems);
                const pageItems = filtered.slice(startIdx, endIdx);

                // Update Badge Header Kategori
                if (currentCatBadge) {
                    currentCatBadge.innerHTML = `<i class="bi bi-folder2-open me-1"></i> Kategori: ${plState.category === 'all' ? 'Semua' : plState.category}`;
                }

                // 3. Render Baris Data
                if (pageItems.length === 0) {
                    plTableBody.innerHTML = `
                        <tr>
                            <td colspan="10" style="text-align: center; padding: 42px 16px; color: var(--t-muted);">
                                <div style="font-size: 28px; margin-bottom: 8px; color: var(--t-muted); opacity: 0.6;"><i class="bi bi-inbox-fill"></i></div>
                                <div style="font-size: 14px; font-weight: 700; color: var(--t-base); margin-bottom: 4px;">Laminasi Tidak Ditemukan</div>
                                <div style="font-size: 12.5px;">Tidak ada laminasi yang cocok dengan filter kategori atau kata kunci pencarian.</div>
                            </td>
                        </tr>
                    `;
                } else {
                    let tbodyHtml = '';

                    pageItems.forEach((item, idx) => {
                        const no = startIdx + idx + 1;
                        let tr = `<tr>`;
                        tr += `<td class="details-digital-sticky details-digital-col-no" style="text-align:center; color:var(--t-muted); font-size:12px;">${no}</td>`;
                        tr += `<td class="details-digital-sticky details-digital-col-material font-semibold" style="font-weight:700; color:var(--t-base);">
                            <div style="display:flex; flex-direction:column; gap:3px; width:100%; min-width:0; max-width:100%;">
                                <div style="display:flex; align-items:center; justify-content:space-between; gap:8px; width:100%; min-width:0;">
                                    <span style="flex:1 1 auto; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="${item.lamination_name}">${item.lamination_name}</span>
                                    ${plState.category === 'all' ? `<span class="badge" style="font-size:10px; font-weight:600; padding:2px 7px; background:var(--bg-muted); color:var(--t-muted); border:1px solid var(--border); border-radius:4px; flex-shrink:0; white-space:nowrap;">${item.category_name}</span>` : ''}
                                </div>
                                ${item.vendor_name ? `<span style="font-size:10.5px; color:var(--primary); font-weight:600; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="Vendor: ${item.vendor_name}"><i class="bi bi-building"></i> Vendor: ${item.vendor_name}</span>` : ''}
                            </div>
                        </td>`;

                        // Ukuran Tersedia Badges
                        let sizesHtml = '-';
                        if (Array.isArray(item.sizes) && item.sizes.length > 0) {
                            sizesHtml = item.sizes.map(s => `<span class="lf-width-badge">${s}</span>`).join('');
                        }
                        tr += `<td style="text-align:center;"><div style="display:flex; flex-wrap:wrap; gap:3px; justify-content:center;">${sizesHtml}</div></td>`;

                        // Biaya Produksi (HPP)
                        tr += `<td class="details-digital-cost" style="text-align:right;">${formatPlRupiah(item.price_per_meter)}</td>`;
                        tr += `<td class="details-digital-cost" style="text-align:right;">${formatPlRupiah(item.production_cost)}</td>`;
                        tr += `<td class="details-digital-cost" style="text-align:right;">${formatPlRupiah(item.finishing_cost)}</td>`;
                        tr += `<td class="details-digital-cost" style="text-align:right; font-weight:700;">${formatPlRupiah(item.total_cost)}</td>`;

                        // Rekomendasi Harga Jual
                        tr += `<td class="details-digital-price col-price-general" style="text-align:right;">${formatPlRupiah(item.general_price)}</td>`;
                        tr += `<td class="details-digital-price col-price-division" style="text-align:right;">${formatPlRupiah(item.division_price)}</td>`;
                        tr += `<td class="details-digital-price col-price-plain" style="text-align:right;">${formatPlRupiah(item.plain_price)}</td>`;

                        tr += `</tr>`;
                        tbodyHtml += tr;
                    });

                    plTableBody.innerHTML = tbodyHtml;
                }

                // Update Kolom Visibility
                updateColumnVisibility();

                // 4. Update Info Text
                const catLabel = plState.category === 'all' ? 'Semua Kategori' : `Kategori: ${plState.category}`;
                const locLabel = plState.location === 'all' ? '' : ` · Lokasi: ${plState.location}`;
                if (totalItems > 0) {
                    plInfo.innerHTML = `<i class="bi bi-info-circle me-1 text-primary"></i> Menampilkan <strong>${startIdx + 1}</strong> sampai <strong>${endIdx}</strong> dari <strong>${totalItems}</strong> laminasi (${catLabel}${locLabel})`;
                } else {
                    plInfo.innerHTML = `<i class="bi bi-info-circle me-1 text-muted"></i> Tidak ada laminasi (${catLabel}${locLabel})`;
                }

                // 5. Render Pagination
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
                            renderPriceList();
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
                    renderPriceList();
                });
            }

            if (plLocSelect.length) {
                plLocSelect.select2({
                    width: '100%',
                    placeholder: 'Pilih Lokasi'
                }).on('change', function() {
                    plState.location = $(this).val();
                    plState.page = 1;
                    renderPriceList();
                });
            }

            if (plPageSizeSelect.length) {
                plPageSizeSelect.select2({
                    width: '100%',
                    minimumResultsForSearch: -1
                }).on('change', function() {
                    plState.pageSize = $(this).val();
                    plState.page = 1;
                    renderPriceList();
                });
            }

            // Tipe Harga Tabs (Semua / Umum / Divisi / Polos)
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
                    renderPriceList();
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
                        renderPriceList();
                    }
                });
            }

            // Initial Render
            renderPriceList();
        });
    </script>
@endsection

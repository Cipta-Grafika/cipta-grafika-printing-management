@extends('admin_master')

@section('contents')
    <style>
        /* =========================================================
           LAYOUT & OVERFLOW PROTECTION (PRESERVING SIDEBAR RESPONSIVENESS)
        ========================================================== */
        /*
         * Catatan: Hindari overflow-x: hidden pada html, body, atau .shell.
         * Agar position: sticky pada .d-sidebar tetap berfungsi normal,
         * animasi toggle collapse sidebar (72px <-> 248px) berjalan mulus,
         * serta drawer mobile/tablet (transform: translateX) tidak terpotong.
         */
        .main {
            min-width: 0;
            max-width: 100%;
        }

        .content {
            min-width: 0;
            max-width: 100%;
        }

        /* =========================================================
           SELECT2 STYLING HARMONIZATION (THEME-AWARE & RESPONSIVE)
        ========================================================== */
        /* Scope 100% width ONLY to the field select2 container, NEVER globally */
        .field > .select2-container,
        .field .select2-container--default {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }

        /* Prevent Select2 dropdown container from causing horizontal overflow */
        .select2-container {
            max-width: 100vw !important;
            box-sizing: border-box !important;
        }

        .select2-container--default .select2-selection--single {
            background-color: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            height: 40px;
            display: flex;
            align-items: center;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-ring);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--t-base);
            padding-left: 12px;
            padding-right: 28px;
            font-size: 13.5px;
            line-height: 38px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
            right: 8px;
        }

        .select2-dropdown {
            background-color: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
            z-index: 99999;
            box-sizing: border-box !important;
            max-width: 100vw !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            background-color: var(--bg-muted);
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--t-base);
            padding: 6px 10px;
            font-size: 13px;
        }

        .select2-container--default .select2-results__option {
            color: var(--t-base);
            font-size: 13px;
            padding: 8px 12px;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--primary);
            color: #ffffff;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: var(--bg-muted);
            color: var(--primary);
            font-weight: 600;
        }

        /* Back Header Button & Secondary Custom Button */
        .btn-back-header {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--t-base);
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            white-space: nowrap;
        }

        .btn-back-header:hover {
            color: var(--primary);
            border-color: var(--primary);
            background: var(--primary-soft);
            transform: translateY(-1px);
        }

        .btn-secondary-custom {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-card);
            color: var(--t-base);
            border: 1px solid var(--border);
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-secondary-custom:hover {
            background: var(--bg-hover);
            color: var(--primary);
            border-color: var(--primary);
        }

        /* =========================================================
           CALENDAR ESTIMATOR RESPONSIVE GRID SYSTEM
        ========================================================== */
        .calendar-estimator-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            width: 100%;
            min-width: 0;
            margin-top: 16px;
        }

        .calendar-form-col {
            min-width: 0;
            width: 100%;
        }

        .calendar-summary-col {
            min-width: 0;
            width: 100%;
        }

        @media (min-width: 1024px) {
            .calendar-estimator-grid {
                grid-template-columns: 1.15fr 0.85fr;
                gap: 24px;
                align-items: start;
            }

            .calendar-sticky-wrap {
                position: sticky;
                top: 20px;
            }
        }

        /* PRODUCT TYPE SELECTOR CARDS (PREMIUM THEME-AWARE) */
        .cal-type-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }

        .cal-card-option {
            position: relative;
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            border-radius: 12px;
            border: 2px solid var(--border);
            background: var(--bg-card);
            color: var(--t-base);
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
        }

        .cal-card-option:hover {
            border-color: var(--primary);
            background: var(--bg-hover);
            transform: translateY(-1px);
        }

        .cal-card-option.active {
            border-color: var(--primary);
            background: var(--primary-soft);
            box-shadow: 0 4px 14px var(--primary-ring);
        }

        .cal-card-icon-wrap {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: var(--bg-muted);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--t-muted);
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .cal-card-option.active .cal-card-icon-wrap {
            background: var(--primary);
            border-color: var(--primary);
            color: #ffffff;
            box-shadow: 0 2px 8px var(--primary-ring);
        }

        .cal-card-text {
            flex: 1;
            min-width: 0;
            padding-right: 22px;
        }

        .cal-card-title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--t-base);
            line-height: 1.3;
            margin-bottom: 2px;
            transition: color 0.15s ease;
        }

        .cal-card-option.active .cal-card-title {
            color: var(--primary);
        }

        .cal-card-desc {
            font-size: 11.5px;
            color: var(--t-muted);
            line-height: 1.35;
        }

        .cal-card-indicator {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid var(--border);
            background: var(--bg-card);
            display: flex;
            align-items: center;
            justify-content: center;
            color: transparent;
            transition: all 0.2s ease;
        }

        .cal-card-option.active .cal-card-indicator {
            border-color: var(--primary);
            background: var(--primary);
            color: #ffffff;
        }

        .qty-quick-buttons {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .btn-qty {
            padding: 6px 14px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--bg-muted);
            color: var(--t-base);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-qty:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--bg-hover);
        }

        .btn-qty.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #ffffff;
        }

        /* =========================================================
           SMART RECOMMENDATION CARD (THEME-AWARE: LIGHT & DARK)
        ========================================================== */
        .smart-rec-card {
            border-radius: 14px;
            padding: 22px;
            transition: all 0.2s ease;
        }

        /* Default & Light Theme */
        :root[data-theme="light"] .smart-rec-card,
        html:not([data-theme="dark"]) .smart-rec-card {
            background: var(--bg-card);
            border: 1.5px solid var(--border);
            box-shadow: 0 4px 20px -2px rgba(15, 23, 42, 0.06);
            color: var(--t-base);
        }

        :root[data-theme="light"] .rec-price-label,
        html:not([data-theme="dark"]) .rec-price-label {
            color: var(--t-muted);
        }

        :root[data-theme="light"] .rec-price-val,
        html:not([data-theme="dark"]) .rec-price-val {
            color: var(--primary);
        }

        :root[data-theme="light"] .rec-total-val,
        html:not([data-theme="dark"]) .rec-total-val {
            color: var(--t-muted);
        }

        :root[data-theme="light"] .rec-reason-box,
        html:not([data-theme="dark"]) .rec-reason-box {
            background: var(--bg-muted);
            border: 1px solid var(--border);
            color: var(--t-base);
        }

        :root[data-theme="light"] .rec-info-grid,
        html:not([data-theme="dark"]) .rec-info-grid {
            border-top: 1px solid var(--border);
        }

        :root[data-theme="light"] .rec-info-item,
        html:not([data-theme="dark"]) .rec-info-item {
            background: var(--bg-muted);
            border: 1px solid var(--border);
        }

        :root[data-theme="light"] .rec-info-label,
        html:not([data-theme="dark"]) .rec-info-label {
            color: var(--t-muted);
        }

        :root[data-theme="light"] .rec-info-val,
        html:not([data-theme="dark"]) .rec-info-val {
            color: var(--t-base);
        }

        :root[data-theme="light"] .rec-badge,
        html:not([data-theme="dark"]) .rec-badge {
            background: var(--success-soft);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: var(--success);
        }

        /* Dark Theme */
        :root[data-theme="dark"] .smart-rec-card,
        html[data-theme="dark"] .smart-rec-card {
            background: linear-gradient(135deg, #141b2d 0%, #1a2237 100%);
            border: 1px solid var(--border);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.35);
            color: var(--t-base);
        }

        :root[data-theme="dark"] .rec-price-label,
        html[data-theme="dark"] .rec-price-label {
            color: var(--t-muted);
        }

        :root[data-theme="dark"] .rec-price-val,
        html[data-theme="dark"] .rec-price-val {
            color: #ffffff;
        }

        :root[data-theme="dark"] .rec-total-val,
        html[data-theme="dark"] .rec-total-val {
            color: var(--primary-light);
        }

        :root[data-theme="dark"] .rec-reason-box,
        html[data-theme="dark"] .rec-reason-box {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border);
            color: var(--t-base);
        }

        :root[data-theme="dark"] .rec-info-grid,
        html[data-theme="dark"] .rec-info-grid {
            border-top: 1px solid var(--border);
        }

        :root[data-theme="dark"] .rec-info-item,
        html[data-theme="dark"] .rec-info-item {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid var(--border);
        }

        :root[data-theme="dark"] .rec-info-label,
        html[data-theme="dark"] .rec-info-label {
            color: var(--t-muted);
        }

        :root[data-theme="dark"] .rec-info-val,
        html[data-theme="dark"] .rec-info-val {
            color: #ffffff;
        }

        :root[data-theme="dark"] .rec-badge,
        html[data-theme="dark"] .rec-badge {
            background: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.4);
            color: #34d399;
        }

        .rec-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 14px;
        }

        .pulse-dot {
            width: 7px;
            height: 7px;
            background: var(--success);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--success);
        }

        .rec-price-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .rec-price-val {
            font-size: 32px;
            font-weight: 800;
            line-height: 1.2;
            margin: 4px 0 2px;
        }

        .rec-total-val {
            font-size: 13.5px;
            font-weight: 600;
        }

        .rec-reason-box {
            margin-top: 14px;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .rec-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 16px;
            padding-top: 16px;
        }

        .rec-info-item {
            border-radius: 8px;
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .rec-info-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .rec-info-val {
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* SINGLE HERO ACTION BUTTON: PREVIEW QUOTATION */
        .btn-preview-offer {
            width: 100%;
            margin-top: 16px;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            border: none;
            padding: 12px 18px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
            transition: all 0.2s ease;
        }

        .btn-preview-offer:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
        }

        .dual-engine-table-wrap {
            overflow-x: auto;
        }

        .table-compare {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .table-compare th, .table-compare td {
            padding: 10px 14px;
            text-align: left;
            border-bottom: 1px solid var(--border);
            color: var(--t-base);
        }

        .table-compare th {
            font-weight: 700;
            background: var(--bg-muted);
            color: var(--t-base);
        }

        .breakdown-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 12.5px;
        }

        .breakdown-item {
            display: flex;
            justify-content: space-between;
            color: var(--t-muted);
        }

        .breakdown-item.total {
            font-weight: 700;
            font-size: 13px;
            color: var(--t-base);
        }

        .breakdown-item.markup {
            color: var(--primary);
            font-weight: 600;
        }

        .breakdown-divider {
            height: 1px;
            background: var(--border);
            margin: 4px 0;
        }

        .toast-popup {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: var(--bg-card);
            color: var(--t-base);
            border: 1px solid var(--border);
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 9999;
            opacity: 0;
            pointer-events: none;
            transform: translateY(12px);
            transition: all 0.25s ease;
        }

        .toast-popup.show {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        /* MODAL PREVIEW SURAT PENAWARAN */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            padding: 16px;
        }

        .modal-overlay.is-open {
            display: flex;
        }

        .modal-container-custom {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 20px 35px rgba(0, 0, 0, 0.35);
            overflow: hidden;
            color: var(--t-base);
        }

        .modal-head-custom {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body-custom {
            padding: 20px;
            max-height: 75vh;
            overflow-y: auto;
            color: var(--t-base);
        }

        .modal-foot-custom {
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: var(--bg-muted);
        }
    </style>

    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">

                {{-- HERO HEADER --}}
                <section class="hero" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <div class="hero-text" style="flex: 1; min-width: 280px;">
                        <span class="eyebrow" style="color: var(--primary); font-weight: 700; letter-spacing: 0.5px;">
                            ESTIMASI CETAK KALENDER
                        </span>
                        <h1 class="hero-title" style="margin: 6px 0 8px;">
                            Kalkulator Kalender (Dinding &amp; Meja)
                        </h1>
                        <p class="hero-sub" style="margin: 0;">
                            Kalkulasi cerdas paket <strong>Digital Print A3+</strong> vs <strong>Mesin Offset (SM52 / Oliver66 / SM72)</strong> dengan deteksi rute paling efisien secara otomatis.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('admin_home') }}" class="btn-back-header">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-6-6 6-6"/>
                            </svg>
                            <span>Kembali</span>
                        </a>
                    </div>
                </section>

                <div class="calendar-estimator-grid">

                    {{-- =========================================================
                        KOLOM KIRI: FORMULIR INPUT SPESIFIKASI (7 COLS)
                    ========================================================== --}}
                    <div class="calendar-form-col">
                        <section class="card" style="margin-bottom: 24px;">

                            <div class="card-head" style="border-bottom: 1px solid var(--border);">
                                <div class="card-title-wrap">
                                    <span class="eyebrow" style="color: var(--primary); font-weight: 700;">
                                        FORMULIR SPESIFIKASI PESANAN
                                    </span>
                                </div>
                            </div>

                            <form id="calendarEstimationForm" onsubmit="return false;">
                                @csrf

                                {{-- 1. INFORMASI PELANGGAN & CABANG --}}
                                <div class="form-section" style="padding: 18px 20px; border-bottom: 1px solid var(--border);">
                                    <div class="form-section-head" style="margin-bottom: 14px;">
                                        <span class="eyebrow" style="font-weight: 700; color: var(--primary);">
                                            1. INFORMASI CABANG &amp; HARGA
                                        </span>
                                    </div>

                                    <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                                        {{-- Lokasi Cabang --}}
                                        <div class="field">
                                            <label class="field-label" for="location_id">
                                                Lokasi Cabang <span class="req">*</span>
                                            </label>
                                            <select id="location_id" name="location_id" class="select select2" required>
                                                @foreach ($locations ?? [] as $loc)
                                                    <option value="{{ $loc->id }}" {{ $loop->first ? 'selected' : '' }}>
                                                        {{ $loc->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Profil Harga --}}
                                        <div class="field">
                                            <label class="field-label" for="price_type">
                                                Profil Harga <span class="req">*</span>
                                            </label>
                                            <select id="price_type" name="price_type" class="select select2" required>
                                                <option value="general" selected>Harga Umum</option>
                                                <option value="division">Harga Divisi (Internal)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. TIPE KALENDER SWITCHER (CARD SELECTOR) --}}
                                <div class="form-section" style="padding: 18px 20px; border-bottom: 1px solid var(--border);">
                                    <label class="field-label" style="margin-bottom: 12px; font-weight: 700;">
                                        Kategori Kalender <span class="req">*</span>
                                    </label>
                                    <div class="cal-type-grid">
                                        <div class="cal-card-option active" id="btnTypeDinding" onclick="selectCalendarType('dinding')">
                                            <div class="cal-card-indicator">
                                                <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="cal-card-icon-wrap">
                                                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect width="18" height="18" x="3" y="3" rx="2"/>
                                                    <path d="M3 9h18"/>
                                                    <path d="M9 21V9"/>
                                                </svg>
                                            </div>
                                            <div class="cal-card-text">
                                                <div class="cal-card-title">Kalender Dinding (Wall)</div>
                                                <div class="cal-card-desc">Cetak Lembaran, Jepit Klem Seng / Hanger Spiral</div>
                                            </div>
                                        </div>

                                        <div class="cal-card-option" id="btnTypeMeja" onclick="selectCalendarType('meja')">
                                            <div class="cal-card-indicator">
                                                <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="cal-card-icon-wrap">
                                                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2">
                                                    <rect width="18" height="14" x="3" y="5" rx="2"/>
                                                    <path d="M8 21h8"/>
                                                    <path d="M12 17v4"/>
                                                </svg>
                                            </div>
                                            <div class="cal-card-text">
                                                <div class="cal-card-title">Kalender Meja (Desk)</div>
                                                <div class="cal-card-desc">Dudukan Hardcover Karton Boot + Spiral Kawat</div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="calendar_type" name="calendar_type" value="dinding">
                                </div>

                                {{-- 3. SPESIFIKASI PRODUK & BAHAN --}}
                                <div class="form-section" style="padding: 18px 20px; border-bottom: 1px solid var(--border);">
                                    <div class="form-section-head" style="margin-bottom: 14px;">
                                        <span class="eyebrow" style="font-weight: 700; color: var(--primary);">
                                            2. UKURAN &amp; BAHAN KERTAS
                                        </span>
                                    </div>

                                    <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                                        {{-- Ukuran Produk --}}
                                        <div class="field">
                                            <label class="field-label" for="product_id">
                                                Ukuran Kalender <span class="req">*</span>
                                            </label>
                                            <select id="product_id" name="product_id" class="select select2" required>
                                                {{-- Populated dynamically via JS --}}
                                            </select>
                                        </div>

                                        {{-- Bahan Kertas --}}
                                        <div class="field">
                                            <label class="field-label" for="material_id">
                                                Bahan Kertas <span class="req">*</span>
                                            </label>
                                            <select id="material_id" name="material_id" class="select select2" required>
                                                @foreach ($materials as $m)
                                                    <option value="{{ $m->id }}"
                                                        data-category="{{ $m->category }}"
                                                        {{ str_contains($m->material_name, '150') ? 'selected' : '' }}>
                                                        {{ $m->material_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Jumlah Lembar Isi --}}
                                        <div class="field">
                                            <label class="field-label" for="leaf_count">
                                                Jumlah Lembar Isi <span class="req">*</span>
                                            </label>
                                            <select id="leaf_count" name="leaf_count" class="select select2" required>
                                                {{-- Populated dynamically via JS --}}
                                            </select>
                                        </div>

                                        {{-- Laminasi --}}
                                        <div class="field">
                                            <label class="field-label" for="lamination">
                                                Laminasi Cover / Lembar
                                            </label>
                                            <select id="lamination" name="lamination" class="select select2">
                                                <option value="none" selected>Tanpa Laminasi</option>
                                                <option value="glossy">Laminasi Glossy</option>
                                                <option value="doff">Laminasi Doff</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- 4. KHUSUS KALENDER MEJA: DUDUKAN / PENOPANG --}}
                                <div class="form-section" id="deskOptionsSection" style="padding: 18px 20px; border-bottom: 1px solid var(--border); display: none;">
                                    <div class="form-section-head" style="margin-bottom: 14px;">
                                        <span class="eyebrow" style="font-weight: 700; color: var(--primary);">
                                            3. SPESIFIKASI DUDUKAN MEJA (BOARD)
                                        </span>
                                    </div>

                                    <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                                        {{-- Jenis Dudukan --}}
                                        <div class="field">
                                            <label class="field-label" for="support_type">
                                                Model Dudukan / Penopang
                                            </label>
                                            <select id="support_type" name="support_type" class="select select2">
                                                <option value="printing_fc" selected>Printing Full Color (Cover Custom)</option>
                                                <option value="hardcover_polos">Hardcover Polos (Beli Jadi)</option>
                                            </select>
                                        </div>

                                        {{-- Jenis Karton Board --}}
                                        <div class="field">
                                            <label class="field-label" for="board_type">
                                                Ketebalan Board
                                            </label>
                                            <select id="board_type" name="board_type" class="select select2">
                                                <option value="boot_30" selected>Karton Boot 30 (Tebal / Premium)</option>
                                                <option value="boot_40">Karton Boot 40 (Standar)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- 5. KUANTITAS & PROFIL HARGA --}}
                                <div class="form-section" style="padding: 18px 20px;">
                                    <div class="form-section-head" style="margin-bottom: 14px;">
                                        <span class="eyebrow" style="font-weight: 700; color: var(--primary);">
                                            4. JUMLAH PESANAN (QTY)
                                        </span>
                                    </div>

                                    <div class="form-grid" style="display: grid; grid-template-columns: 1fr; gap: 16px;">
                                        <div class="field">
                                            <label class="field-label" for="qty">
                                                Kuantitas Cetak (Pcs) <span class="req">*</span>
                                            </label>
                                            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                                <input type="number" id="qty" name="qty" min="1" step="1" value="300"
                                                    class="input" style="max-width: 180px; font-weight: 700; font-size: 16px;" required oninput="triggerCalculation()">
                                                <div class="qty-quick-buttons">
                                                    <button type="button" class="btn-qty" onclick="setQty(50)">50</button>
                                                    <button type="button" class="btn-qty" onclick="setQty(100)">100</button>
                                                    <button type="button" class="btn-qty" onclick="setQty(200)">200</button>
                                                    <button type="button" class="btn-qty active" onclick="setQty(300)">300</button>
                                                    <button type="button" class="btn-qty" onclick="setQty(500)">500</button>
                                                    <button type="button" class="btn-qty" onclick="setQty(1000)">1.000</button>
                                                </div>
                                            </div>
                                            <small class="field-help" id="markupTierNotice" style="margin-top: 6px; display: block; color: var(--t-muted);">
                                                Markup bertingkat: 1-99 pcs (50%), 100-199 pcs (45%), 200-299 pcs (40%), 300-499 pcs (35%), ≥500 pcs (30%).
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                {{-- ACTION BUTTONS: KEMBALI & RESET --}}
                                <div class="card-foot" style="padding: 16px 20px; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: var(--bg-muted);">
                                    <a href="{{ route('admin_home') }}" class="btn-secondary-custom">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="m15 18-6-6 6-6"/>
                                        </svg>
                                        <span>Kembali</span>
                                    </a>
                                    <button type="button" class="btn-secondary-custom" onclick="resetCalendarForm()">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                            <path d="M3 3v5h5"/>
                                        </svg>
                                        <span>Reset Form</span>
                                    </button>
                                </div>

                            </form>
                        </section>
                    </div>

                    {{-- =========================================================
                        KOLOM KANAN: REKOMENDASI & BREAKDOWN PENAWARAN (5 COLS)
                    ========================================================== --}}
                    <div class="calendar-summary-col">
                        <div class="calendar-sticky-wrap">

                            {{-- SMART RECOMMENDATION HIGHLIGHT CARD (THEME-AWARE) --}}
                        <div class="smart-rec-card" id="smartRecCard">
                            <div class="rec-badge" id="recBadge">
                                <span class="pulse-dot"></span>
                                <span id="recBadgeText">REKOMENDASI SISTEM</span>
                            </div>

                            <div class="rec-price-block">
                                <span class="rec-price-label">HARGA JUAL REKOMENDASI</span>
                                <div class="rec-price-val" id="recPricePcs">Rp 0</div>
                                <div class="rec-total-val" id="recTotalOffer">Total Order: Rp 0</div>
                            </div>

                            <div class="rec-reason-box" id="recReasonBox">
                                <i class="bi bi-info-circle"></i>
                                <span id="recReasonText">Memuat rekomendasi...</span>
                            </div>

                            {{-- INFORMASI PRODUKSI & ESTIMASI (PENGGANTI TOMBOL SALIN) --}}
                            <div class="rec-info-grid">
                                <div class="rec-info-item">
                                    <span class="rec-info-label">MESIN PRODUKSI</span>
                                    <span class="rec-info-val" id="infoProdEngine">-</span>
                                </div>
                                <div class="rec-info-item">
                                    <span class="rec-info-label">HPP / PCS</span>
                                    <span class="rec-info-val" id="infoHppPcs">-</span>
                                </div>
                                <div class="rec-info-item">
                                    <span class="rec-info-label">MARGIN TIER</span>
                                    <span class="rec-info-val" id="infoMargin">-</span>
                                </div>
                                <div class="rec-info-item">
                                    <span class="rec-info-label">KEBUTUHAN KERTAS</span>
                                    <span class="rec-info-val" id="infoPaperNeed">-</span>
                                </div>
                            </div>

                            {{-- TOMBOL PRATINJAU SURAT PENAWARAN --}}
                            <button type="button" class="btn-preview-offer" onclick="openOfferModal()">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14 2 14 8 20 8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10 9 9 9 8 9"/>
                                </svg>
                                <span>Pratinjau Surat Penawaran</span>
                            </button>
                        </div>

                        {{-- DUAL ENGINE COMPARISON (DIGITAL vs OFFSET) --}}
                        <div class="card" style="margin-top: 16px;">
                            <div class="card-head" style="border-bottom: 1px solid var(--border); padding: 12px 18px;">
                                <div class="card-title-wrap">
                                    <span class="eyebrow" style="font-weight: 700; color: var(--t-base);">
                                        KOMPARASI DIGITAL VS OFFSET
                                    </span>
                                </div>
                            </div>

                            <div class="dual-engine-table-wrap">
                                <table class="table-compare">
                                    <thead>
                                        <tr>
                                            <th>Metrik Kalkulasi</th>
                                            <th style="color: var(--teal, #0d9488);">Digital A3+</th>
                                            <th style="color: var(--primary);">Offset Press</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Rute Mesin</td>
                                            <td id="compDigitalRoute">Digital A3+</td>
                                            <td id="compOffsetRoute">SM52 / Oliver / SM72</td>
                                        </tr>
                                        <tr>
                                            <td>HPP per Pcs</td>
                                            <td id="compDigitalHpp">Rp 0</td>
                                            <td id="compOffsetHpp">Rp 0</td>
                                        </tr>
                                        <tr>
                                            <td>Harga Jual / Pcs</td>
                                            <td id="compDigitalSell" style="font-weight: 700;">Rp 0</td>
                                            <td id="compOffsetSell" style="font-weight: 700;">Rp 0</td>
                                        </tr>
                                        <tr style="background: var(--bg-muted);">
                                            <td><strong>Total Order</strong></td>
                                            <td id="compDigitalTotal" style="font-weight: 800; color: var(--teal, #0d9488);">Rp 0</td>
                                            <td id="compOffsetTotal" style="font-weight: 800; color: var(--primary);">Rp 0</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- RINCIAN BREAKDOWN BIAYA --}}
                        <div class="card" style="margin-top: 16px;">
                            <div class="card-head" style="border-bottom: 1px solid var(--border); padding: 12px 18px;">
                                <div class="card-title-wrap">
                                    <span class="eyebrow" style="font-weight: 700; color: var(--t-base);">
                                        BREAKDOWN HPP REKOMENDASI
                                    </span>
                                </div>
                            </div>
                            <div class="breakdown-list" id="breakdownList" style="padding: 14px 18px;">
                                <div class="breakdown-item">
                                    <span class="b-label" id="bLabelPaper">Kertas / Plano</span>
                                    <span class="b-val" id="bValPaper">Rp 0</span>
                                </div>
                                <div class="breakdown-item">
                                    <span class="b-label" id="bLabelPrint">Ongkos Cetak</span>
                                    <span class="b-val" id="bValPrint">Rp 0</span>
                                </div>
                                <div class="breakdown-item" id="rowPlatCost">
                                    <span class="b-label">Plat CTP Offset</span>
                                    <span class="b-val" id="bValPlat">Rp 0</span>
                                </div>
                                <div class="breakdown-item" id="rowLamCost">
                                    <span class="b-label">Laminasi</span>
                                    <span class="b-val" id="bValLam">Rp 0</span>
                                </div>
                                <div class="breakdown-item" id="bRowFinishing">
                                    <span class="b-label" id="bLabelFinishing">Finishing / Jilid</span>
                                    <span class="b-val" id="bValFinishing">Rp 0</span>
                                </div>
                                <div class="breakdown-item" id="rowSupportCost" style="display: none;">
                                    <span class="b-label">Penopang / Dudukan Meja</span>
                                    <span class="b-val" id="bValSupport">Rp 0</span>
                                </div>
                                <div class="breakdown-divider"></div>
                                <div class="breakdown-item total">
                                    <span>Total HPP Produksi</span>
                                    <span id="bValHppTotal">Rp 0</span>
                                </div>
                                <div class="breakdown-item markup">
                                    <span id="bLabelMarkup">Margin Markup</span>
                                    <span id="bValMarkupPercent">+35%</span>
                                </div>
                            </div>
                        </div>
                        </div> {{-- /.calendar-sticky-wrap --}}

                    </div>

                </div>

            </main>
            <div data-shell-footer></div>
        </div>
    </div>

    {{-- MODAL PRATINJAU SURAT PENAWARAN --}}
    <div class="modal-overlay" id="modalOfferPreview">
        <div class="modal-container-custom">
            <div class="modal-head-custom">
                <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: var(--t-base);">
                    Surat Penawaran Cetak Kalender
                </h3>
                <button type="button" onclick="closeOfferModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted);">&times;</button>
            </div>
            <div class="modal-body-custom" id="modalOfferContent">
                <div style="text-align: center; margin-bottom: 18px; border-bottom: 2px solid var(--border); padding-bottom: 12px;">
                    <h2 style="margin: 0 0 4px; font-size: 18px; font-weight: 800; color: var(--primary);">CIPTA GRAFIKA</h2>
                    <p style="margin: 0; font-size: 12px; color: var(--t-muted);">Printing Management System &bull; Surat Penawaran Harga Kalender</p>
                </div>
                <div style="font-size: 13px; line-height: 1.6; color: var(--t-base);">
                    <div style="margin-bottom: 12px;">
                        <strong>Spesifikasi Pesanan:</strong>
                        <ul style="margin: 4px 0 0 18px; padding: 0;" id="modalSpecList"></ul>
                    </div>
                    <div style="background: var(--bg-muted); padding: 12px 16px; border-radius: 8px; margin-bottom: 14px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <span>Harga Satuan:</span>
                            <strong id="modalPricePcs" style="color: var(--primary); font-size: 15px;">Rp 0 / pcs</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Total Penawaran:</span>
                            <strong id="modalTotalPrice" style="font-size: 16px; color: var(--t-base);">Rp 0</strong>
                        </div>
                    </div>
                    <p style="font-size: 11px; color: var(--t-muted); margin: 0;">
                        * Penawaran ini berlaku selama 14 hari kerja sejak diterbitkan. Harga sudah termasuk finishing dan jilid sesuai spesifikasi.
                    </p>
                </div>
            </div>
            <div class="modal-foot-custom">
                <button type="button" class="btn btn--ghost" onclick="closeOfferModal()">Tutup</button>
                <button type="button" class="btn btn--primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Cetak / PDF
                </button>
            </div>
        </div>
    </div>

    {{-- TOAST NOTIFICATION --}}
    <div id="calendarToast" class="toast-popup">
        <i class="bi bi-check-circle-fill" style="color: #10b981;"></i>
        <span id="calendarToastMsg">Berhasil disalin!</span>
    </div>

    {{-- JAVASCRIPT LOGIC & SELECT2 INTEGRATION --}}
    <script>
        const allProducts = @json($products);
        let latestResult = null;
        let debounceTimer = null;

        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inisialisasi Select2
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').select2({
                    width: '100%'
                });

                // Trigger kalkulasi saat ada select2 yang berubah
                $('.select2').on('change', function() {
                    triggerCalculation();
                });
            }

            // 2. Setup Awal Kategori
            selectCalendarType('dinding');
        });

        function selectCalendarType(type) {
            document.getElementById('calendar_type').value = type;

            const btnDinding = document.getElementById('btnTypeDinding');
            const btnMeja = document.getElementById('btnTypeMeja');
            const deskSection = document.getElementById('deskOptionsSection');

            if (type === 'meja') {
                btnDinding.classList.remove('active');
                btnMeja.classList.add('active');
                deskSection.style.display = 'block';
            } else {
                btnMeja.classList.remove('active');
                btnDinding.classList.add('active');
                deskSection.style.display = 'none';
            }

            updateProductOptions(type);
            updateLeafOptions(type);
            triggerCalculation();
        }

        function updateProductOptions(type) {
            const $productSelect = $('#product_id');
            $productSelect.empty();

            allProducts.forEach(p => {
                if (p.calendar_type === type) {
                    const opt = new Option(p.product_name, p.id, false, false);
                    $(opt).data('product', p);
                    $productSelect.append(opt);
                }
            });

            // Re-render select2
            $productSelect.trigger('change.select2');
        }

        function updateLeafOptions(type) {
            const $leafSelect = $('#leaf_count');
            $leafSelect.empty();

            let leaves = (type === 'meja') ? [3, 4, 6, 7, 12, 13] : [1, 3, 4, 6, 7, 12];
            leaves.forEach(val => {
                let label = val + ' Lembar';
                if (val === 1) label += ' (Poster 1 Lembar)';
                else if (val === 3) label += ' (4 Bulanan / Triwulan)';
                else if (val === 4) label += ' (3 Bulanan / Caturwulan)';
                else if (val === 6) label += ' (2 Bulanan / Bimestral)';
                else if (val === 7) label += ' (2 Bulanan + Cover)';
                else if (val === 12) label += ' (Bulanan)';
                else if (val === 13) label += ' (Bulanan + Cover)';

                const isSelected = (type === 'dinding' && val === 7) || (type === 'meja' && val === 13);
                const opt = new Option(label, val, isSelected, isSelected);
                $leafSelect.append(opt);
            });

            $leafSelect.trigger('change.select2');
        }

        function setQty(val) {
            document.getElementById('qty').value = val;
            document.querySelectorAll('.btn-qty').forEach(btn => {
                btn.classList.toggle('active', parseInt(btn.textContent.replace('.', '')) === val);
            });
            triggerCalculation();
        }

        function resetCalendarForm() {
            selectCalendarType('dinding');
            $('#location_id').val($('#location_id option:first').val()).trigger('change.select2');
            $('#price_type').val('general').trigger('change.select2');
            $('#lamination').val('none').trigger('change.select2');
            $('#support_type').val('printing_fc').trigger('change.select2');
            $('#board_type').val('boot_30').trigger('change.select2');
            setQty(300);
        }

        function triggerCalculation() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(calculateEstimate, 250);
        }

        function calculateEstimate() {
            const form = document.getElementById('calendarEstimationForm');
            const formData = new FormData(form);

            const payload = {
                product_id: formData.get('product_id'),
                material_id: formData.get('material_id'),
                qty: formData.get('qty'),
                leaf_count: formData.get('leaf_count'),
                lamination: formData.get('lamination'),
                support_type: formData.get('support_type'),
                board_type: formData.get('board_type'),
                _token: document.querySelector('input[name="_token"]').value
            };

            if (!payload.product_id || !payload.material_id) {
                return;
            }

            fetch("{{ route('admin_calculate_calendar_estimate') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": payload._token,
                    "Accept": "application/json"
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(res => {
                if (res.success && res.data) {
                    latestResult = res.data;
                    renderResult(res.data);
                } else {
                    console.error("Gagal kalkulasi:", res.message);
                }
            })
            .catch(err => {
                console.error("Error request kalkulasi:", err);
            });
        }

        function renderResult(data) {
            // 1. Highlight Rekomendasi
            document.getElementById('recPricePcs').textContent = 'Rp ' + Number(data.recommended_price_pcs).toLocaleString('id-ID');
            document.getElementById('recTotalOffer').textContent = 'Total Order: Rp ' + Number(data.recommended_total_offer).toLocaleString('id-ID');
            document.getElementById('recBadgeText').textContent = data.recommendation_label;
            document.getElementById('recReasonText').textContent = data.recommendation_reason;

            // 2. Tabel Komparasi
            document.getElementById('compDigitalRoute').textContent = data.digital.available ? 'Digital Print A3+' : 'Tidak Tersedia (Over Area)';
            document.getElementById('compDigitalHpp').textContent = data.digital.available ? 'Rp ' + Number(data.digital.hpp_per_pcs).toLocaleString('id-ID') : '-';
            document.getElementById('compDigitalSell').textContent = data.digital.available ? 'Rp ' + Number(data.digital.selling_price_pcs).toLocaleString('id-ID') : '-';
            document.getElementById('compDigitalTotal').textContent = data.digital.available ? 'Rp ' + Number(data.digital.total_offer).toLocaleString('id-ID') : '-';

            document.getElementById('compOffsetRoute').textContent = data.offset.machine_name;
            document.getElementById('compOffsetHpp').textContent = 'Rp ' + Number(data.offset.hpp_per_pcs).toLocaleString('id-ID');
            document.getElementById('compOffsetSell').textContent = 'Rp ' + Number(data.offset.selling_price_pcs).toLocaleString('id-ID');
            document.getElementById('compOffsetTotal').textContent = 'Rp ' + Number(data.offset.total_offer).toLocaleString('id-ID');

            // 3. Breakdown Rekomendasi
            const isDigital = data.recommended_route === 'digital';
            const rec = isDigital ? data.digital : data.offset;

            document.getElementById('bValPaper').textContent = 'Rp ' + Number(isDigital ? 0 : rec.paper_cost).toLocaleString('id-ID');
            document.getElementById('bValPrint').textContent = 'Rp ' + Number(rec.print_cost).toLocaleString('id-ID');

            const rowPlat = document.getElementById('rowPlatCost');
            if (isDigital) {
                rowPlat.style.display = 'none';
            } else {
                rowPlat.style.display = 'flex';
                document.getElementById('bValPlat').textContent = 'Rp ' + Number(rec.plat_cost).toLocaleString('id-ID');
            }

            const rowLam = document.getElementById('rowLamCost');
            if (rec.lamination_cost > 0) {
                rowLam.style.display = 'flex';
                document.getElementById('bValLam').textContent = 'Rp ' + Number(rec.lamination_cost).toLocaleString('id-ID');
            } else {
                rowLam.style.display = 'none';
            }

            // Finishing
            if (data.calendar_type === 'meja') {
                document.getElementById('bLabelFinishing').textContent = 'Spiral Wire-O + Rakit Meja';
                document.getElementById('bValFinishing').textContent = 'Rp ' + Number(rec.spiral_fin_total).toLocaleString('id-ID');

                document.getElementById('rowSupportCost').style.display = 'flex';
                document.getElementById('bValSupport').textContent = 'Rp ' + Number(rec.support_total).toLocaleString('id-ID');
            } else {
                document.getElementById('bLabelFinishing').textContent = data.finishing_detail.name;
                document.getElementById('bValFinishing').textContent = 'Rp ' + Number(rec.finishing_cost).toLocaleString('id-ID');
                document.getElementById('rowSupportCost').style.display = 'none';
            }

            document.getElementById('bValHppTotal').textContent = 'Rp ' + Number(rec.hpp_total).toLocaleString('id-ID');
            document.getElementById('bValMarkupPercent').textContent = '+' + data.markup_percent + '% (HPP: Rp ' + Number(rec.hpp_per_pcs).toLocaleString('id-ID') + '/pcs)';

            // 4. Update Quick Info Metrics (Pengganti Tombol Salin)
            document.getElementById('infoProdEngine').textContent = isDigital
                ? 'Digital A3+'
                : (data.offset.machine_key || 'Offset Press');
            document.getElementById('infoHppPcs').textContent = 'Rp ' + Number(rec.hpp_per_pcs).toLocaleString('id-ID');
            document.getElementById('infoMargin').textContent = '+' + data.markup_percent + '% (Tier Qty)';
            document.getElementById('infoPaperNeed').textContent = isDigital
                ? (Number(rec.total_sheets_a3).toLocaleString('id-ID') + ' Lbr A3+')
                : (Number(rec.plano_needed).toLocaleString('id-ID') + ' Lbr Plano');
        }

        function copyHargaPcs() {
            if (!latestResult) return;
            const text = latestResult.share_texts.copy_price_pcs;
            navigator.clipboard.writeText(text).then(() => {
                showToast("Harga / pcs berhasil disalin!");
            });
        }

        function copySpesifikasiWA() {
            if (!latestResult) return;
            const text = latestResult.share_texts.copy_wa_full;
            navigator.clipboard.writeText(text).then(() => {
                showToast("Rincian spesifikasi untuk WhatsApp berhasil disalin!");
            });
        }

        function openOfferModal() {
            if (!latestResult) return;
            const r = latestResult;
            const list = document.getElementById('modalSpecList');
            list.innerHTML = `
                <li><strong>Kategori:</strong> Kalender ${r.calendar_type === 'meja' ? 'Meja' : 'Dinding'}</li>
                <li><strong>Ukuran:</strong> ${r.size_label} (${r.orientation})</li>
                <li><strong>Bahan Kertas:</strong> ${r.material_name}</li>
                <li><strong>Jumlah Lembar:</strong> ${r.leaf_count} Lembar</li>
                <li><strong>Laminasi:</strong> ${r.lamination === 'none' ? 'Tanpa Laminasi' : r.lamination.toUpperCase()}</li>
                <li><strong>Finishing:</strong> ${r.calendar_type === 'meja' ? (r.support_detail.name + ' + Spiral Wire-O') : r.finishing_detail.name}</li>
                <li><strong>Kuantitas:</strong> ${Number(r.qty).toLocaleString('id-ID')} pcs</li>
                <li><strong>Rute Mesin Rekomendasi:</strong> ${r.recommended_route === 'digital' ? 'Digital Print A3+' : ('Offset ' + r.offset.machine_key)}</li>
            `;

            document.getElementById('modalPricePcs').textContent = 'Rp ' + Number(r.recommended_price_pcs).toLocaleString('id-ID') + ' / pcs';
            document.getElementById('modalTotalPrice').textContent = 'Rp ' + Number(r.recommended_total_offer).toLocaleString('id-ID');

            document.getElementById('modalOfferPreview').classList.add('is-open');
        }

        function closeOfferModal() {
            document.getElementById('modalOfferPreview').classList.remove('is-open');
        }

        // Tutup modal ketika klik di luar popup (pada overlay background)
        const offerModalOverlay = document.getElementById('modalOfferPreview');
        if (offerModalOverlay) {
            offerModalOverlay.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeOfferModal();
                }
            });
        }

        // Tutup modal ketika tombol Escape (Esc) ditekan
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.key === 'Esc') {
                const modal = document.getElementById('modalOfferPreview');
                if (modal && modal.classList.contains('is-open')) {
                    closeOfferModal();
                }
            }
        });

        function showToast(msg) {
            const toast = document.getElementById('calendarToast');
            document.getElementById('calendarToastMsg').textContent = msg;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 2500);
        }
    </script>
@endsection

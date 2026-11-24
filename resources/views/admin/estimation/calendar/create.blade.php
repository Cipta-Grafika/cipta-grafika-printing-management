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

        /* Quotation & Date Picker Theming (Dark & Light) */
        :root[data-theme="dark"] input[type="date"],
        [data-theme="dark"] input[type="date"] {
            color-scheme: dark !important;
        }
        :root[data-theme="light"] input[type="date"],
        [data-theme="light"] input[type="date"] {
            color-scheme: light !important;
        }
        input[type="date"].input {
            cursor: pointer;
            font-family: inherit;
            font-size: 13px;
            font-weight: 500;
        }
        :root[data-theme="dark"] input[type="date"]::-webkit-calendar-picker-indicator,
        [data-theme="dark"] input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            opacity: 0.85;
            cursor: pointer;
            border-radius: 4px;
            padding: 4px;
            transition: all 0.15s ease;
        }
        :root[data-theme="dark"] input[type="date"]::-webkit-calendar-picker-indicator:hover,
        [data-theme="dark"] input[type="date"]::-webkit-calendar-picker-indicator:hover {
            filter: invert(1);
            opacity: 1;
            background: rgba(255, 255, 255, 0.15);
        }
        :root[data-theme="light"] input[type="date"]::-webkit-calendar-picker-indicator,
        [data-theme="light"] input[type="date"]::-webkit-calendar-picker-indicator {
            filter: none;
            opacity: 0.7;
            cursor: pointer;
            border-radius: 4px;
            padding: 4px;
            transition: all 0.15s ease;
        }
        :root[data-theme="light"] input[type="date"]::-webkit-calendar-picker-indicator:hover,
        [data-theme="light"] input[type="date"]::-webkit-calendar-picker-indicator:hover {
            filter: none;
            opacity: 1;
            background: rgba(0, 0, 0, 0.08);
        }
        .quote-preset-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--t-muted);
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            line-height: 1.4;
            user-select: none;
        }
        .quote-preset-btn:hover {
            background: var(--primary-soft);
            color: var(--primary);
            border-color: var(--primary);
        }
        .quote-preset-btn.active,
        .quote-preset-btn.is-active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 1px 3px var(--primary-ring);
        }
        .btn-copy-address {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 2px 8px;
            font-size: 11px;
            font-weight: 500;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--t-muted);
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            line-height: 1.4;
            user-select: none;
        }
        .btn-copy-address:hover {
            background: var(--primary-soft);
            color: var(--primary);
            border-color: var(--primary);
        }

        /* Modern PPN 11% Toggle Switch Styling */
        .ppn-toggle-switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 22px;
            margin: 0;
            cursor: pointer;
            flex-shrink: 0;
        }
        .ppn-toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
            pointer-events: none;
        }
        .ppn-toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #334155;
            border: 1px solid rgba(255, 255, 255, 0.22);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 999px;
        }
        .ppn-toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 2px;
            bottom: 2px;
            background-color: #ffffff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.35);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 50%;
        }
        .ppn-toggle-switch input:checked + .ppn-toggle-slider {
            background-color: #2563eb;
            border-color: #2563eb;
        }
        .ppn-toggle-switch input:checked + .ppn-toggle-slider:before {
            transform: translateX(18px);
        }
        .ppn-toggle-switch input:focus-visible + .ppn-toggle-slider {
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.35);
        }

        /* =========================================================
               SELECT2 STYLING HARMONIZATION (THEME-AWARE & RESPONSIVE)
            ========================================================== */
        /* Scope 100% width ONLY to the field select2 container, NEVER globally */
        .field>.select2-container,
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

        /* WHATSAPP COPY ACTION BUTTON & TOAST */
        .btn-action-wa {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            margin-top: 8px;
            padding: 11px 18px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            font-weight: 600;
            font-size: 13px;
            border: 1.5px solid rgba(255, 255, 255, 0.28);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        :root[data-theme="dark"] .btn-action-wa,
        html[data-theme="dark"] .btn-action-wa {
            background: var(--bg-card);
            color: var(--t-base);
            border: 1.5px solid var(--border);
        }

        .btn-action-wa:hover,
        :root[data-theme="dark"] .btn-action-wa:hover,
        html[data-theme="dark"] .btn-action-wa:hover {
            background: rgba(34, 197, 94, 0.15);
            border-color: #22c55e;
            color: #22c55e;
        }

        .wa-toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 1000000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .wa-toast {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 320px;
            max-width: 440px;
            padding: 14px 18px;
            border-radius: 12px;
            background: var(--bg-card);
            color: var(--t-base);
            border: 1px solid var(--border);
            border-left: 4.5px solid #22c55e;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.3);
            pointer-events: auto;
            transform: translateX(120%);
            opacity: 0;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
        }

        .wa-toast.is-show {
            transform: translateX(0);
            opacity: 1;
        }

        .wa-toast-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(34, 197, 94, 0.15);
            color: #22c55e;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .wa-toast-content {
            flex: 1;
        }

        .wa-toast-title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--t-base);
            margin: 0 0 3px;
        }

        .wa-toast-desc {
            font-size: 12px;
            color: var(--t-muted);
            margin: 0;
            line-height: 1.45;
        }

        .wa-toast-close {
            background: transparent;
            border: none;
            color: var(--t-muted);
            font-size: 18px;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.15s;
        }

        .wa-toast-close:hover {
            color: var(--t-base);
            background: var(--bg-hover);
        }

        .dual-engine-table-wrap {
            overflow-x: auto;
        }

        .table-compare {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .table-compare th,
        .table-compare td {
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

        /* MATRIKS PRICELIST BERTINGKAT */
        .calendar-matrix-col {
            grid-column: 1 / -1;
            width: 100%;
            min-width: 0;
            margin-top: 10px;
        }

        .matrix-table-wrap {
            overflow-x: auto;
            width: 100%;
            -webkit-overflow-scrolling: touch;
        }

        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .matrix-table thead tr {
            background: var(--bg-muted);
            border-bottom: 2px solid var(--border);
            text-align: left;
        }

        .matrix-table th {
            padding: 12px 14px;
            font-weight: 700;
            color: var(--t-base);
            white-space: nowrap;
        }

        .matrix-table th.col-highlight {
            color: var(--primary);
            background: var(--primary-soft);
        }

        .matrix-table td {
            padding: 11px 14px;
            color: var(--t-base);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
            vertical-align: middle;
        }

        .matrix-table td.col-highlight {
            font-weight: 700;
            color: var(--primary);
            background: var(--primary-soft);
        }

        .matrix-table tr:hover {
            background: rgba(0, 0, 0, 0.02);
        }

        html[data-theme="dark"] .matrix-table tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        .matrix-table tr.is-selected {
            background: var(--primary-soft) !important;
        }

        .badge-selected {
            font-size: 10.5px;
            background: var(--primary);
            color: #fff;
            padding: 2px 7px;
            border-radius: 4px;
            margin-left: 6px;
            font-weight: 600;
        }

        .btn-select-qty {
            padding: 5px 12px;
            font-size: 12px;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: var(--bg-card);
            color: var(--t-base);
            cursor: pointer;
            font-weight: 600;
            transition: all 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .btn-select-qty:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }


        /* Matrix Search Filter */
        .matrix-search-box {
            position: relative;
            display: inline-flex;
            align-items: center;
            min-width: 200px;
            max-width: 280px;
        }

        .matrix-search-box i.bi-search {
            position: absolute;
            left: 11px;
            color: var(--t-muted);
            font-size: 13px;
            pointer-events: none;
        }

        .matrix-search-input {
            width: 100%;
            height: 36px;
            padding: 6px 30px 6px 32px;
            font-size: 12.5px;
            border: 1px solid var(--border);
            border-radius: 7px;
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

        .btn-copy-matrix {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #25D366;
            color: #fff;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-copy-matrix:hover {
            background: #1ebd5a;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(37, 211, 102, 0.3);
        }

        .badge-route-digital {
            background: rgba(13, 148, 136, 0.12);
            color: #0d9488;
            border: 1px solid rgba(13, 148, 136, 0.3);
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .badge-route-offset {
            background: var(--primary-soft);
            color: var(--primary);
            border: 1px solid var(--primary-ring);
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
    </style>

    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">

                {{-- HERO HEADER --}}
                <section class="hero"
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <div class="hero-text" style="flex: 1; min-width: 280px;">
                        <span class="eyebrow" style="color: var(--primary); font-weight: 700; letter-spacing: 0.5px;">
                            ESTIMASI CETAK KALENDER
                        </span>
                        <h1 class="hero-title" style="margin: 6px 0 8px;">
                            Kalkulator Kalender (Dinding &amp; Meja)
                        </h1>
                        <p class="hero-sub" style="margin: 0;">
                            Kalkulasi cerdas paket <strong>Digital Print A3+</strong> vs <strong>Mesin Offset (SM52 /
                                Oliver66 / SM72)</strong> dengan deteksi rute paling efisien secara otomatis.
                        </p>
                    </div>
                    {{-- <div>
                        <a href="{{ route('admin_home') }}" class="btn-back-header">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m15 18-6-6 6-6"/>
                            </svg>
                            <span>Kembali</span>
                        </a>
                    </div> --}}
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
                                <div class="form-section"
                                    style="padding: 18px 20px; border-bottom: 1px solid var(--border);">
                                    <div class="form-section-head" style="margin-bottom: 14px;">
                                        <span class="eyebrow" style="font-weight: 700; color: var(--primary);">
                                            1. INFORMASI CABANG &amp; HARGA
                                        </span>
                                    </div>

                                    <div class="form-grid"
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                                        {{-- Lokasi Cabang --}}
                                        <div class="field">
                                            <label class="field-label" for="location_id">
                                                Lokasi Cabang <span class="req">*</span>
                                            </label>
                                            <select id="location_id" name="location_id" class="select select2" required>
                                                @foreach ($locations ?? [] as $loc)
                                                    <option value="{{ $loc->id }}"
                                                        {{ $loop->first ? 'selected' : '' }}>
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

                                    {{-- Informasi Sales & Syarat Pembayaran (Cetakia Integration) --}}
                                    <div class="form-grid"
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 14px;">
                                        {{-- Sales / PIC Penanggung Jawab --}}
                                        <div class="field">
                                            <label class="field-label" for="cetakia_user_id">
                                                Sales / PIC Penanggung Jawab <span style="color: #ef4444;">*</span>
                                            </label>
                                            <select id="cetakia_user_id" name="user_id" class="select select2">
                                                <option value="">-- Pilih Sales (Cetakia) --</option>
                                                @foreach ($salesUsers ?? [] as $sales)
                                                    <option value="{{ $sales['id'] }}" {{ $loop->first ? 'selected' : '' }}>
                                                        {{ $sales['name'] }} ({{ $sales['role_name'] ?? $sales['role'] ?? 'Sales' }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="field-help" style="font-size: 11px; color: var(--t-muted); display: block; margin-top: 3px;">Digunakan untuk penerbitan kode penawaran resmi Cetakia.</small>
                                        </div>

                                        {{-- Syarat Pembayaran (Terms) --}}
                                        <div class="field">
                                            <label class="field-label" for="cetakia_terms">
                                                Syarat Pembayaran (Terms)
                                            </label>
                                            <select id="cetakia_terms" name="terms" class="select select2">
                                                <option value="0" selected>Cash / Tunai (0 Hari)</option>
                                                <option value="7">Kredit 7 Hari</option>
                                                <option value="14">Kredit 14 Hari</option>
                                                <option value="30">Kredit 30 Hari</option>
                                            </select>
                                            <small class="field-help" style="font-size: 11px; color: var(--t-muted); display: block; margin-top: 3px;">Durasi tempo pembayaran yang disepakati.</small>
                                        </div>
                                    </div>

                                    {{-- Informasi Pelanggan (Cetakia Customer) --}}
                                    <div class="form-grid"
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 14px;">
                                        {{-- Nama Pelanggan (Select2 Cetakia) --}}
                                        <div class="field">
                                            <label class="field-label" for="customer_id">
                                                Nama Pelanggan / Perusahaan (Cetakia) <span style="color: #ef4444;">*</span>
                                            </label>
                                            <select id="customer_id" name="customer_id" class="select select2">
                                                <option value="">-- Pilih Pelanggan (Cetakia) --</option>
                                                @foreach ($customers ?? \App\Support\CetakiaCustomer::all() as $cust)
                                                    <option value="{{ $cust['id'] }}" 
                                                        data-name="{{ $cust['name'] ?? '' }}"
                                                        data-code="{{ $cust['customer_code'] ?? $cust['code'] ?? '' }}"
                                                        data-bill-to="{{ $cust['bill_to'] ?? '' }}"
                                                        data-ship-to="{{ $cust['ship_to'] ?? '' }}">
                                                        {{ $cust['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" id="customer_name" name="customer_name" value="">
                                        </div>

                                        {{-- No. WhatsApp / Telepon --}}
                                        <div class="field">
                                            <label class="field-label" for="customer_phone">
                                                No. WhatsApp / Telepon (Opsional)
                                            </label>
                                            <input type="text" id="customer_phone" name="customer_phone" class="input"
                                                placeholder="Contoh: 08123456789">
                                        </div>
                                    </div>

                                    {{-- Alamat Penagihan & Pengiriman (Cetakia Quotation) --}}
                                    <div class="form-grid"
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 14px;">
                                        {{-- Alamat Penagihan (Bill To) --}}
                                        <div class="field">
                                            <label class="field-label" for="bill_to">
                                                Alamat Penagihan (Bill To)
                                            </label>
                                            <textarea id="bill_to" name="bill_to" class="input" rows="2" style="height: auto; resize: vertical;"
                                                placeholder="Alamat penagihan pelanggan..."></textarea>
                                        </div>

                                        {{-- Alamat Pengiriman (Ship To) --}}
                                        <div class="field">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                                <label class="field-label" for="ship_to" style="margin-bottom: 0;">
                                                    Alamat Pengiriman (Ship To)
                                                </label>
                                                <button type="button" id="btn_copy_bill_to_cal" class="btn-copy-address" title="Salin dari Alamat Penagihan">
                                                    <i class="bi bi-copy"></i> Sama dgn Bill To
                                                </button>
                                            </div>
                                            <textarea id="ship_to" name="ship_to" class="input" rows="2" style="height: auto; resize: vertical;"
                                                placeholder="Alamat tujuan pengiriman..."></textarea>
                                        </div>
                                    </div>

                                    {{-- Masa Berlaku & Tanggal Penawaran (Cetakia Quotation) --}}
                                    <div class="form-grid"
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 14px;">
                                        {{-- Tanggal Penawaran --}}
                                        <div class="field">
                                            <label class="field-label" for="quotation_date">
                                                Tanggal Penawaran
                                            </label>
                                            <input type="date" id="quotation_date" name="quotation_date" class="input"
                                                value="{{ date('Y-m-d') }}">
                                        </div>

                                        {{-- Masa Berlaku Penawaran --}}
                                        <div class="field">
                                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                                <label class="field-label" for="quotation_expired" style="margin-bottom: 0;">
                                                    Masa Berlaku Penawaran (Expired)
                                                </label>
                                                <div style="display: flex; gap: 5px;">
                                                    <button type="button" class="quote-preset-btn btn-expire-preset-cal" data-days="3" title="Berlaku 3 Hari">+3 Hr</button>
                                                    <button type="button" class="quote-preset-btn btn-expire-preset-cal" data-days="7" title="Berlaku 7 Hari">+7 Hr</button>
                                                    <button type="button" class="quote-preset-btn btn-expire-preset-cal" data-days="14" title="Berlaku 14 Hari">+14 Hr</button>
                                                    <button type="button" class="quote-preset-btn btn-expire-preset-cal active" data-days="30" title="Berlaku 30 Hari">+30 Hr</button>
                                                </div>
                                            </div>
                                            <input type="date" id="quotation_expired" name="quotation_expired" class="input"
                                                value="{{ date('Y-m-d', strtotime('+30 days')) }}">
                                        </div>
                                    </div>

                                    {{-- Catatan Khusus Penawaran / Produksi --}}
                                    <div class="field" style="margin-top: 14px;">
                                        <label class="field-label" for="customer_notes">
                                            Catatan Khusus Penawaran / Produksi (Opsional)
                                        </label>
                                        <input type="text" id="customer_notes" name="customer_notes" class="input"
                                            placeholder="Contoh: Packing per 50 pcs kardus rapi, jilid spiral putih...">
                                    </div>
                                </div>

                                {{-- 2. TIPE KALENDER SWITCHER (CARD SELECTOR) --}}
                                <div class="form-section"
                                    style="padding: 18px 20px; border-bottom: 1px solid var(--border);">
                                    <label class="field-label" style="margin-bottom: 12px; font-weight: 700;">
                                        Kategori Kalender <span class="req">*</span>
                                    </label>
                                    <div class="cal-type-grid">
                                        <div class="cal-card-option active" id="btnTypeDinding"
                                            onclick="selectCalendarType('dinding')">
                                            <div class="cal-card-indicator">
                                                <svg viewBox="0 0 20 20" fill="currentColor" width="13" height="13">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="cal-card-icon-wrap">
                                                <svg viewBox="0 0 24 24" width="22" height="22" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <rect width="18" height="18" x="3" y="3" rx="2" />
                                                    <path d="M3 9h18" />
                                                    <path d="M9 21V9" />
                                                </svg>
                                            </div>
                                            <div class="cal-card-text">
                                                <div class="cal-card-title">Kalender Dinding (Wall)</div>
                                                <div class="cal-card-desc">Cetak Lembaran, Jepit Klem Seng / Hanger Spiral
                                                </div>
                                            </div>
                                        </div>

                                        <div class="cal-card-option" id="btnTypeMeja"
                                            onclick="selectCalendarType('meja')">
                                            <div class="cal-card-indicator">
                                                <svg viewBox="0 0 20 20" fill="currentColor" width="13"
                                                    height="13">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="cal-card-icon-wrap">
                                                <svg viewBox="0 0 24 24" width="22" height="22" fill="none"
                                                    stroke="currentColor" stroke-width="2">
                                                    <rect width="18" height="14" x="3" y="5" rx="2" />
                                                    <path d="M8 21h8" />
                                                    <path d="M12 17v4" />
                                                </svg>
                                            </div>
                                            <div class="cal-card-text">
                                                <div class="cal-card-title">Kalender Meja (Desk)</div>
                                                <div class="cal-card-desc">Dudukan Hardcover Karton Boot + Spiral Kawat
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="calendar_type" name="calendar_type" value="dinding">
                                </div>

                                {{-- 3. SPESIFIKASI PRODUK & BAHAN --}}
                                <div class="form-section"
                                    style="padding: 18px 20px; border-bottom: 1px solid var(--border);">
                                    <div class="form-section-head" style="margin-bottom: 14px;">
                                        <span class="eyebrow" style="font-weight: 700; color: var(--primary);">
                                            2. UKURAN &amp; BAHAN KERTAS
                                        </span>
                                    </div>

                                    <div class="form-grid"
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
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

                                        {{-- Pilihan Finishing Kalender (Khusus Kalender Dinding) --}}
                                        <div class="field" id="wallFinishingSection">
                                            <label class="field-label" for="finishing">
                                                Pilihan Finishing Kalender <span class="req">*</span>
                                            </label>
                                            <select id="finishing" name="finishing" class="select select2">
                                                <option value="auto" selected>✨ AUTO (Rekomendasi Bahan)</option>
                                                <option value="jepit_kaleng">📎 Jepit Kaleng / Seng</option>
                                                <option value="spiral_hanger">🌀 Spiral Wire + Hanger</option>
                                                <option value="mata_itik">🔘 Mata Itik / Mata Ayam (Eyelet)</option>
                                            </select>
                                            <div id="finishingBadge" style="margin-top: 8px; font-size: 12px; font-weight: 600; color: var(--primary); display: flex; align-items: center; gap: 6px; padding: 7px 12px; background: var(--primary-soft); border-radius: 6px; border: 1px solid var(--primary-ring);">
                                                <span>💡</span>
                                                <span id="finishingBadgeText">Finishing Aktif: Jepit Kaleng (Auto)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- 4. KHUSUS KALENDER MEJA: DUDUKAN / PENOPANG --}}
                                <div class="form-section" id="deskOptionsSection"
                                    style="padding: 18px 20px; border-bottom: 1px solid var(--border); display: none;">
                                    <div class="form-section-head" style="margin-bottom: 14px;">
                                        <span class="eyebrow" style="font-weight: 700; color: var(--primary);">
                                            3. SPESIFIKASI DUDUKAN MEJA (BOARD)
                                        </span>
                                    </div>

                                    <div class="form-grid"
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                                        {{-- Jenis Dudukan --}}
                                        <div class="field">
                                            <label class="field-label" for="support_type">
                                                Model Dudukan / Penopang
                                            </label>
                                            <select id="support_type" name="support_type" class="select select2">
                                                <option value="printing_fc" selected>Printing Full Color (Cover Custom)
                                                </option>
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
                                                <input type="number" id="qty" name="qty" min="1"
                                                    step="1" value="300" class="input"
                                                    style="max-width: 180px; font-weight: 700; font-size: 16px;" required
                                                    oninput="triggerCalculation()">
                                                <div class="qty-quick-buttons">
                                                    <button type="button" class="btn-qty"
                                                        onclick="setQty(50)">50</button>
                                                    <button type="button" class="btn-qty"
                                                        onclick="setQty(100)">100</button>
                                                    <button type="button" class="btn-qty"
                                                        onclick="setQty(200)">200</button>
                                                    <button type="button" class="btn-qty active"
                                                        onclick="setQty(300)">300</button>
                                                    <button type="button" class="btn-qty"
                                                        onclick="setQty(500)">500</button>
                                                    <button type="button" class="btn-qty"
                                                        onclick="setQty(1000)">1.000</button>
                                                </div>
                                            </div>
                                            <small class="field-help" id="markupTierNotice"
                                                style="margin-top: 6px; display: block; color: var(--t-muted);">
                                                Markup bertingkat: 1-99 pcs (50%), 100-199 pcs (45%), 200-299 pcs (40%),
                                                300-499 pcs (35%), ≥500 pcs (30%).
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                {{-- 5. DISKON PENJUALAN --}}
                                <div class="form-section" style="padding: 18px 20px;">
                                    <div class="form-section-head" style="margin-bottom: 14px;">
                                        <span class="eyebrow" style="font-weight: 700; color: var(--primary);">
                                            5. DISKON PENJUALAN
                                        </span>
                                    </div>

                                    <div class="field" style="margin-bottom: 16px;">
                                        <label class="field-label">
                                            Jenis Diskon
                                            <span class="req">*</span>
                                        </label>

                                        <div class="discount-options">
                                            {{-- Tanpa Diskon --}}
                                            <label class="discount-option">
                                                <input type="radio" name="discount_type" value="none" checked>
                                                <span class="discount-option__content">
                                                    <span class="discount-option__icon">
                                                        <i class="bi bi-tag"></i>
                                                    </span>
                                                    <span class="discount-option__text">
                                                        <strong>Tanpa Diskon</strong>
                                                        <small>Harga normal</small>
                                                    </span>
                                                </span>
                                            </label>

                                            {{-- Diskon Persen --}}
                                            <label class="discount-option">
                                                <input type="radio" name="discount_type" value="percentage">
                                                <span class="discount-option__content">
                                                    <span class="discount-option__icon">
                                                        <i class="bi bi-percent"></i>
                                                    </span>
                                                    <span class="discount-option__text">
                                                        <strong>Diskon Persen</strong>
                                                        <small>Potongan berdasarkan persentase</small>
                                                    </span>
                                                </span>
                                            </label>

                                            {{-- Diskon Nominal --}}
                                            <label class="discount-option">
                                                <input type="radio" name="discount_type" value="nominal">
                                                <span class="discount-option__content">
                                                    <span class="discount-option__icon">
                                                        <i class="bi bi-cash-stack"></i>
                                                    </span>
                                                    <span class="discount-option__text">
                                                        <strong>Diskon Nominal</strong>
                                                        <small>Potongan berdasarkan nominal rupiah</small>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-grid">
                                        {{-- Input Diskon --}}
                                        <div class="field" id="discountValueField" hidden>
                                            <div style="display: flex; align-items: center; gap: 5px;">
                                                <label class="field-label" for="discount_value" id="discountValueLabel">
                                                    Nilai Diskon
                                                </label>
                                                <span class="input-suffix__text" id="discountSuffix">
                                                    %
                                                </span>
                                            </div>

                                            <div class="input-suffix" id="discountInputWrapper">
                                                <input id="discount_value" name="discount_value" class="input"
                                                    type="text" inputmode="decimal" placeholder="0" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ACTION BUTTONS: KEMBALI & RESET --}}
                                <div class="card-foot"
                                    style="padding: 16px 20px; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: var(--bg-muted);">
                                    <a href="{{ route('admin_home') }}" class="btn-secondary-custom">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
                                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="m15 18-6-6 6-6" />
                                        </svg>
                                        <span>Kembali</span>
                                    </a>
                                    <button type="button" class="btn-secondary-custom" onclick="resetCalendarForm()">
                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8" />
                                            <path d="M3 3v5h5" />
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
                                    <div class="rec-price-val" id="recPricePcs">Rp 0,00</div>
                                    <div class="rec-total-val" id="recTotalOffer">Total Order: Rp 0,00</div>
                                </div>

                                {{-- Info Diskon --}}
                                <div id="liveDiscountWrap"
                                    style="display: none; margin: 8px 0 10px; padding: 8px 12px; background: rgba(239, 68, 68, 0.08); border-radius: 8px; border: 1px dashed rgba(239, 68, 68, 0.35);">
                                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12.5px;">
                                        <span style="color: var(--t-muted);">Harga Sebelum Diskon:</span>
                                        <s id="liveSubtotalPrice" style="color: var(--t-muted); font-weight: 600;">Rp 0</s>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 12.5px; margin-top: 4px;">
                                        <span style="color: #ef4444; font-weight: 600;">Hemat Diskon (<span id="liveDiscountDesc">-</span>):</span>
                                        <strong id="liveDiscountAmount" style="color: #ef4444; font-weight: 700;">- Rp 0</strong>
                                    </div>
                                </div>

                                {{-- Opsi PPN 11% (Cetakia Compatible - Modern iOS/Dashboard Switch) --}}
                                <div id="liveTaxToggleWrap"
                                    style="margin: 10px 0 12px; padding: 10px 14px; background: rgba(59, 130, 246, 0.08); border-radius: 9px; border: 1px solid rgba(59, 130, 246, 0.25); transition: all 0.2s ease;">
                                    {{-- Toggle PPN 11% --}}
                                    <label for="tax_enabled_check" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; user-select: none; margin: 0; width: 100%;">
                                        <span style="font-size: 13px; font-weight: 600; color: var(--t-base); display: flex; align-items: center; gap: 8px;">
                                            <i class="bi bi-receipt-cutoff" style="color: #60a5fa; font-size: 15px;"></i>
                                            <span>Kenakan PPN 11%</span>
                                        </span>
                                        <span class="ppn-toggle-switch">
                                            <input type="checkbox" id="tax_enabled_check" name="tax_enabled" value="1">
                                            <span class="ppn-toggle-slider"></span>
                                        </span>
                                    </label>

                                    {{-- Rincian Pajak (Hanya tampil saat PPN aktif) --}}
                                    <div id="liveTaxDetailsRow" style="display: none; margin-top: 8px; padding-top: 8px; border-top: 1px dashed rgba(59, 130, 246, 0.25); font-size: 12px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; color: var(--t-muted);">
                                            <span>Dasar Pengenaan Pajak (DPP):</span>
                                            <span id="liveTaxBaseAmount" style="font-weight: 600;">Rp 0,00</span>
                                        </div>
                                        <div id="liveTaxPpnRow" style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                                            <span style="color: #60a5fa; font-weight: 600;">PPN (11%):</span>
                                            <strong id="liveTaxAmount" style="color: #60a5fa; font-weight: 700;">+ Rp 0,00</strong>
                                        </div>
                                    </div>
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

                                {{-- TOMBOL PRATINJAU SURAT PENAWARAN & SALIN WHATSAPP --}}
                                <button type="button" class="btn-preview-offer" onclick="openOfferModal()">
                                    <svg viewBox="0 0 24 24" width="16" height="16" fill="none"
                                        stroke="currentColor" stroke-width="2">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                        <polyline points="14 2 14 8 20 8" />
                                        <line x1="16" y1="13" x2="8" y2="13" />
                                        <line x1="16" y1="17" x2="8" y2="17" />
                                        <polyline points="10 9 9 9 8 9" />
                                    </svg>
                                    <span>Pratinjau Surat Penawaran</span>
                                </button>
                                <button type="button" id="btnCopyWaCalendar" class="btn-action-wa" onclick="copyFormattedWhatsAppCalendar(event)">
                                    <i class="bi bi-whatsapp"></i>
                                    <span>Salin Penawaran WhatsApp</span>
                                </button>
                                <button type="button" id="btnSendToCetakia" class="btn-send-cetakia" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; border-radius: 9px; font-size: 13.5px; font-weight: 700; background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: #fff; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25); transition: all 0.2s ease; margin-top: 8px;">
                                    <i class="bi bi-cloud-arrow-up-fill"></i>
                                    <span>Kirim Penawaran ke Cetakia</span>
                                </button>
                            </div>

                            {{-- DUAL ENGINE COMPARISON (DIGITAL vs OFFSET) --}}
                            <div class="card" style="margin-top: 16px;">
                                <div class="card-head"
                                    style="border-bottom: 1px solid var(--border); padding: 12px 18px;">
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
                                                <td id="compDigitalTotal"
                                                    style="font-weight: 800; color: var(--teal, #0d9488);">Rp 0</td>
                                                <td id="compOffsetTotal" style="font-weight: 800; color: var(--primary);">
                                                    Rp 0</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- RINCIAN BREAKDOWN BIAYA --}}
                            <div class="card" style="margin-top: 16px;">
                                <div class="card-head"
                                    style="border-bottom: 1px solid var(--border); padding: 12px 18px;">
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

                    {{-- =========================================================
                        BAGIAN BAWAH: MATRIKS SIMULASI PRICELIST (FULL WIDTH)
                    ========================================================== --}}
                    <div class="calendar-matrix-col">
                        <section class="card" style="margin-top: 10px;">
                            <div class="card-head"
                                style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px; border-bottom: 1px solid var(--border); padding: 16px 20px; margin-bottom: 0;">
                                <div>
                                    <span class="eyebrow"
                                        style="color: var(--primary); font-weight: 700; letter-spacing: 0.5px;">
                                        MATRIKS SIMULASI PRICELIST BERTINGKAT (50 S/D 5.000 PCS)
                                    </span>
                                    <p style="margin: 2px 0 0; font-size: 13px; color: var(--t-muted);">
                                        Perbandingan lengkap HPP dan harga jual rekomendasi per tier kuantitas. Klik
                                        <strong>"Pilih"</strong> untuk menerapkan kuantitas tersebut ke form.
                                    </p>
                                </div>
                                <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                    <div class="matrix-search-box">
                                        <i class="bi bi-search"></i>
                                        <input type="text" id="calendarMatrixSearch" class="matrix-search-input"
                                            placeholder="Cari qty, mesin, harga..." autocomplete="off">
                                        <button type="button" class="matrix-search-clear" id="calendarMatrixSearchClear"
                                            title="Hapus pencarian">&times;</button>
                                    </div>
                                    <button type="button" class="btn-copy-matrix" id="btnCopyMatrixWa"
                                        onclick="copyMatrixToClipboard()">
                                        <i class="bi bi-whatsapp"></i>
                                        <span id="btnCopyMatrixWaText">Salin Daftar Harga (WA)</span>
                                    </button>
                                </div>
                            </div>

                            <div class="matrix-table-wrap">
                                <table class="matrix-table">
                                    <thead>
                                        <tr>
                                            <th>Qty (Pcs)</th>
                                            <th>Rute Mesin Rekomendasi</th>
                                            <th>Total HPP</th>
                                            <th>HPP / Pcs</th>
                                            <th class="col-highlight">Harga Jual / Pcs</th>
                                            <th class="col-highlight">Total Penawaran</th>
                                            <th style="text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="calendarMatrixTableBody">
                                        <tr>
                                            <td colspan="7"
                                                style="text-align: center; padding: 24px; color: var(--t-muted);">
                                                <i class="bi bi-hourglass-split"></i> Memuat simulasi matriks harga...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </section>

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
                <button type="button" onclick="closeOfferModal()"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted);">&times;</button>
            </div>
            <div class="modal-body-custom" id="modalOfferContent">
                <div
                    style="text-align: center; margin-bottom: 18px; border-bottom: 2px solid var(--border); padding-bottom: 12px;">
                    <h2 style="margin: 0 0 4px; font-size: 18px; font-weight: 800; color: var(--primary);">CIPTA GRAFIKA
                    </h2>
                    <p style="margin: 0; font-size: 12px; color: var(--t-muted);">Printing Management System &bull; Surat
                        Penawaran Harga Kalender</p>
                </div>
                <div style="font-size: 13px; line-height: 1.6; color: var(--t-base);">
                    <div style="display: flex; justify-content: space-between; font-size: 12px; color: var(--t-muted); margin-bottom: 10px; border-bottom: 1px dashed var(--border); padding-bottom: 6px;">
                        <div>Pelanggan: <strong id="modalCustName" style="color: var(--t-base);">-</strong></div>
                        <div>Tanggal: <strong id="modalQuoteDate">{{ date('d/m/Y') }}</strong> &middot; Berlaku s/d: <strong id="modalQuoteExpired">{{ date('d/m/Y', strtotime('+30 days')) }}</strong></div>
                    </div>
                    <div style="margin-bottom: 12px;">
                        <strong>Spesifikasi Pesanan:</strong>
                        <ul style="margin: 4px 0 0 18px; padding: 0;" id="modalSpecList"></ul>
                    </div>
                    <div style="background: var(--bg-muted); padding: 12px 16px; border-radius: 8px; margin-bottom: 14px;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                            <span>Harga Satuan:</span>
                            <strong id="modalPricePcs" style="color: var(--primary); font-size: 15px;">Rp 0,00 / pcs</strong>
                        </div>
                        <div id="modalDiscountRow" style="display: none; justify-content: space-between; margin-bottom: 4px; color: #ef4444;">
                            <span>Hemat Diskon (<span id="modalDiscountDesc">-</span>):</span>
                            <strong id="modalDiscountAmount">- Rp 0,00</strong>
                        </div>
                        <div id="modalTaxRow" style="display: none; justify-content: space-between; margin-bottom: 4px; color: #2563eb;">
                            <span>PPN (11%):</span>
                            <strong id="modalTaxAmount">+ Rp 0,00</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Total Penawaran:</span>
                            <strong id="modalTotalPrice" style="font-size: 16px; color: var(--t-base);">Rp 0,00</strong>
                        </div>
                    </div>
                    <p style="font-size: 11px; color: var(--t-muted); margin: 0;">
                        * Penawaran ini berlaku hingga tanggal <strong id="modalQuoteExpiredNotice">{{ date('d/m/Y', strtotime('+30 days')) }}</strong>. Harga sudah termasuk finishing dan
                        jilid sesuai spesifikasi.
                    </p>
                </div>
            </div>
            <div class="modal-foot-custom">
                <button type="button" class="btn btn--ghost" onclick="closeOfferModal()">Tutup</button>
                <button type="button" id="btnModalCopyWaCalendar" onclick="copyFormattedWhatsAppCalendar(event)"
                    style="display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 8px; border: 1px solid #22c55e; background: rgba(34, 197, 94, 0.12); color: #22c55e; font-size: 13px; font-weight: 600; cursor: pointer;">
                    <i class="bi bi-whatsapp"></i> Salin WhatsApp
                </button>
                <button type="button" class="btn btn--primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Cetak / PDF
                </button>
            </div>
        </div>
    </div>

    {{-- =========================================================
        MODAL: KONFIRMASI PENGIRIMAN KE CETAKIA (DEV MODE)
    ========================================================== --}}
    <div class="modal-overlay" id="modalCetakiaConfirm">
        <div class="modal modal-content-custom" style="max-width: 530px; border-radius: 16px; overflow: hidden; padding: 0; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.45);">
            {{-- HEADER DENGAN GRADASI BIRU-CYAN ELEGAN --}}
            <div style="background: linear-gradient(135deg, #1e3a8a 0%, #0284c7 100%); color: #fff; padding: 22px 24px; position: relative;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: rgba(255, 255, 255, 0.18); display: flex; align-items: center; justify-content: center; font-size: 22px;">
                            <i class="bi bi-cloud-arrow-up-fill"></i>
                        </div>
                        <div>
                            <span class="badge" style="background: rgba(255, 255, 255, 0.2); color: #fff; font-size: 11px; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 4px;">
                                CETAKIA &middot; DEV MODE
                            </span>
                            <h3 style="margin: 0; font-size: 17.5px; font-weight: 700; color: #fff;">Konfirmasi Kirim Penawaran</h3>
                        </div>
                    </div>
                    <button type="button" id="btnCloseConfirmModal" class="modal-close-btn" style="color: #fff; opacity: 0.8; font-size: 18px;" aria-label="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            {{-- BODY DENGAN RINGKASAN STRUKTUR RAPI --}}
            <div style="padding: 22px 24px; background: var(--bg-card);">
                <p style="margin: 0 0 16px; font-size: 13px; color: var(--t-muted); line-height: 1.5;">
                    Data penawaran akan dikirim dan dicatat sebagai draf transaksi resmi di sistem Cetakia. Silakan periksa kembali rincian di bawah ini:
                </p>

                {{-- CARD DETAIL TRANSAKSI --}}
                <div style="background: var(--bg-muted); border: 1px solid var(--border); border-radius: 12px; padding: 16px; margin-bottom: 16px;">
                    <div style="display: flex; flex-direction: column; gap: 11px; font-size: 13px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed var(--border); padding-bottom: 9px;">
                            <span style="color: var(--t-muted);">Pelanggan (Customer):</span>
                            <strong id="confirmCustomerName" style="color: var(--t-base); font-size: 13.5px;">-</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed var(--border); padding-bottom: 9px;">
                            <span style="color: var(--t-muted);">Sales PIC:</span>
                            <strong id="confirmSalesName" style="color: var(--t-base); font-size: 13.5px;">-</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed var(--border); padding-bottom: 9px;">
                            <span style="color: var(--t-muted);">Produk &amp; Kuantiti:</span>
                            <span id="confirmItemDesc" style="color: var(--t-base); font-weight: 600; text-align: right; max-width: 60%;">-</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed var(--border); padding-bottom: 9px;">
                            <span style="color: var(--t-muted);">Syarat Pembayaran:</span>
                            <span id="confirmTermsText" class="badge" style="background: var(--bg-card); color: var(--t-base); border: 1px solid var(--border); font-size: 11.5px;">Cash / Tunai</span>
                        </div>
                        <div id="confirmDiscountRow" style="display: none; justify-content: space-between; align-items: center; border-bottom: 1px dashed var(--border); padding-bottom: 9px;">
                            <span style="color: var(--t-muted);">Diskon:</span>
                            <span id="confirmDiscountText" style="font-weight: 600; color: #ef4444;">- Rp 0,00</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px dashed var(--border); padding-bottom: 9px;">
                            <span style="color: var(--t-muted);">PPN (11%):</span>
                            <span id="confirmTaxText" style="font-weight: 600; color: var(--t-base);">Non-PPN (0%)</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 4px;">
                            <span style="font-weight: 700; font-size: 14px; color: var(--t-base);">Grand Total Penawaran:</span>
                            <strong id="confirmGrandTotal" style="font-size: 18px; color: #10b981; font-weight: 800;">Rp 0,00</strong>
                        </div>
                    </div>
                </div>

                {{-- INFO AMAN / BADGE KEAMANAN --}}
                <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--t-muted); margin-bottom: 20px; padding: 8px 12px; background: rgba(16, 185, 129, 0.08); border-radius: 8px; border: 1px solid rgba(16, 185, 129, 0.2);">
                    <i class="bi bi-shield-check" style="color: #10b981; font-size: 15px;"></i>
                    <span>Tercatat sebagai Draf (Draft). Aman dan tidak mengubah data master apapun.</span>
                </div>

                {{-- BUTTONS --}}
                <div style="display: flex; gap: 10px;">
                    <button type="button" id="btnCancelConfirmModal" class="btn" style="flex: 1; padding: 12px 18px; font-size: 13.5px; font-weight: 600; background: var(--bg-muted); color: var(--t-base); border: 1.5px solid var(--border); border-radius: 10px; cursor: pointer; transition: all 0.2s ease;">
                        <i class="bi bi-arrow-left"></i> Periksa Lagi
                    </button>
                    <button type="button" id="btnExecuteSendToCetakia" class="btn" style="flex: 1.4; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 18px; font-size: 14px; font-weight: 700; background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: #fff; border: none; border-radius: 10px; cursor: pointer; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3); transition: all 0.2s ease;">
                        <i class="bi bi-send-fill"></i>
                        <span>Ya, Kirim Sekarang</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================
        MODAL: NOTIFIKASI BERHASIL KIRIM KE CETAKIA
    ========================================================== --}}
    <div class="modal-overlay" id="modalCetakiaSuccess">
        <div class="modal modal-content-custom" style="max-width: 520px; text-align: center; padding: 0; overflow: hidden; border-radius: 14px;">
            <div style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); color: #fff; padding: 28px 24px 22px;">
                <div style="width: 58px; height: 58px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px; font-size: 28px;">
                    <i class="bi bi-check-lg"></i>
                </div>
                <h3 style="margin: 0 0 6px; font-size: 19px; font-weight: 700; color: #fff;">Quotation Berhasil Terkirim!</h3>
                <p style="margin: 0; font-size: 13px; opacity: 0.9;">Penawaran harga telah resmi tercatat di sistem Cetakia (Dev Mode).</p>
            </div>

            <div style="padding: 24px; text-align: left;">
                <div style="background: var(--bg-muted); border: 1px solid var(--border); border-radius: 10px; padding: 14px 16px; margin-bottom: 18px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                        <span style="font-size: 12px; color: var(--t-muted);">Nomor Transaksi Resmi:</span>
                        <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #059669; font-weight: 700; font-size: 11.5px;">201 Created</span>
                    </div>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                        <strong id="cetakiaSuccessCode" style="font-size: 17px; letter-spacing: 0.5px; color: var(--t-base); font-family: monospace;">-</strong>
                        <button type="button" id="btnCopyCetakiaCode" class="btn btn--sm btn--ghost" style="padding: 4px 10px; font-size: 12px; border: 1px solid var(--border); border-radius: 6px; cursor: pointer;">
                            <i class="bi bi-copy"></i> Salin
                        </button>
                    </div>
                </div>

                <div style="font-size: 13px; display: flex; flex-direction: column; gap: 8px; color: var(--t-base); margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--t-muted);">Pelanggan:</span>
                        <strong id="cetakiaSuccessCustomer">-</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--t-muted);">Sales PIC:</span>
                        <strong id="cetakiaSuccessSales">-</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: var(--t-muted);">Total Penawaran:</span>
                        <strong id="cetakiaSuccessGrandTotal" style="color: #059669; font-size: 14.5px;">Rp 0,00</strong>
                    </div>
                </div>

                <div style="margin-top: 10px;">
                    <button type="button" id="btnCloseCetakiaModal" class="btn" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 18px; font-size: 14px; font-weight: 700; background: var(--primary); color: #fff; border: none; border-radius: 10px; cursor: pointer; transition: all 0.2s ease;">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Selesai / Tutup</span>
                    </button>
                </div>
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
        let pendingCetakiaPayload = null;

        function formatRupiah(num) {
            const n = Number(num) || 0;
            return 'Rp ' + n.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function calculateTaxAndTotal() {
            if (!latestResult) return;
            const isTax = $('#tax_enabled_check').is(':checked');
            const subtotal = Number(latestResult.recommended_total_offer) || 0;
            const qty = Number(latestResult.qty) || 1;

            // Hitung Diskon
            const discountType = document.querySelector('input[name="discount_type"]:checked')?.value || 'none';
            const discountRaw = ($('#discount_value').val() || '').replace(/\./g, '').replace(',', '.');
            const discountVal = parseFloat(discountRaw) || 0;
            let discAmt = 0;
            if (discountType === 'percentage') {
                discAmt = Math.min(subtotal, Math.round(subtotal * (discountVal / 100)));
            } else if (discountType === 'nominal') {
                discAmt = Math.min(subtotal, discountVal);
            }

            const dpp = Math.max(0, subtotal - discAmt);
            const taxAmt = isTax ? Math.round(dpp * 0.11) : 0;
            const grandTotal = dpp + taxAmt;
            const unitPrice = qty > 0 ? (grandTotal / qty) : 0;

            const pricePcsEl = document.getElementById('recPricePcs');
            const totalOfferEl = document.getElementById('recTotalOffer');
            if (pricePcsEl) pricePcsEl.textContent = formatRupiah(unitPrice);
            if (totalOfferEl) totalOfferEl.textContent = 'Total Order: ' + formatRupiah(grandTotal);

            // Tampilkan / Sembunyikan Live Discount Wrap
            const discWrap = document.getElementById('liveDiscountWrap');
            if (discWrap) {
                if (discAmt > 0) {
                    discWrap.style.display = 'block';
                    const subtotalEl = document.getElementById('liveSubtotalPrice');
                    if (subtotalEl) subtotalEl.textContent = formatRupiah(subtotal);
                    const discAmtEl = document.getElementById('liveDiscountAmount');
                    if (discAmtEl) discAmtEl.textContent = '- ' + formatRupiah(discAmt);
                    const discDescEl = document.getElementById('liveDiscountDesc');
                    if (discDescEl) discDescEl.textContent = (discountType === 'percentage' ? `${discountVal}%` : 'Nominal');
                } else {
                    discWrap.style.display = 'none';
                }
            }

            const taxWrap = document.getElementById('liveTaxDetailsRow');
            const baseAmtEl = document.getElementById('liveTaxBaseAmount');
            const ppnRow = document.getElementById('liveTaxPpnRow');
            const taxAmtEl = document.getElementById('liveTaxAmount');

            if (taxWrap) taxWrap.style.display = isTax ? 'block' : 'none';
            if (baseAmtEl) baseAmtEl.textContent = formatRupiah(dpp);
            if (ppnRow) ppnRow.style.display = isTax ? 'flex' : 'none';
            if (taxAmtEl) taxAmtEl.textContent = '+ ' + formatRupiah(taxAmt);
        }

        function showNotificationToast(title, desc, type = 'success') {
            let container = document.getElementById('notifToastContainer');
            if (!container) {
                container = document.createElement('div');
                container.id = 'notifToastContainer';
                container.style.cssText = 'position: fixed; top: 24px; right: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 10px; pointer-events: none;';
                document.body.appendChild(container);
            }
            const toast = document.createElement('div');
            toast.style.cssText = 'pointer-events: auto; min-width: 300px; max-width: 420px; background: var(--bg-card); border-left: 4px solid ' +
                (type === 'error' ? '#ef4444' : (type === 'warning' ? '#f59e0b' : '#10b981')) +
                '; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.25); border-radius: 10px; padding: 14px 16px; display: flex; align-items: flex-start; gap: 12px; transition: all 0.3s ease; transform: translateX(120%); opacity: 0;';
            const iconColor = type === 'error' ? '#ef4444' : (type === 'warning' ? '#f59e0b' : '#10b981');
            const iconClass = type === 'error' ? 'bi-exclamation-circle-fill' : (type === 'warning' ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill');
            toast.innerHTML = `
                <i class="bi ${iconClass}" style="color: ${iconColor}; font-size: 20px; margin-top: 2px;"></i>
                <div style="flex: 1;">
                    <div style="font-weight: 700; font-size: 13.5px; color: var(--t-base); margin-bottom: 3px;">${title}</div>
                    <div style="font-size: 12.5px; color: var(--t-muted); line-height: 1.4;">${desc}</div>
                </div>
                <button type="button" style="background: none; border: none; font-size: 16px; color: var(--t-muted); cursor: pointer; padding: 0 4px; line-height: 1;">&times;</button>
            `;
            container.appendChild(toast);
            requestAnimationFrame(() => {
                toast.style.transform = 'translateX(0)';
                toast.style.opacity = '1';
            });
            const closeToast = () => {
                toast.style.transform = 'translateX(120%)';
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 350);
            };
            toast.querySelector('button').addEventListener('click', closeToast);
            setTimeout(closeToast, 3500);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // 1. Inisialisasi Select2
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').select2({
                    width: '100%'
                });

                $('#customer_id').on('change', function() {
                    const custVal = $(this).val();
                    const sel = $(this).find('option:selected');
                    const cNameInput = document.getElementById('customer_name');
                    if (!custVal) {
                        if (cNameInput) cNameInput.value = '';
                        $('#bill_to').val('');
                        $('#ship_to').val('');
                    } else {
                        const custName = (sel.data('name') || sel.text() || '').trim();
                        if (cNameInput) {
                            cNameInput.value = custName;
                        }
                        const rawBillTo = (sel.data('bill-to') || '').toString().trim();
                        const rawShipTo = (sel.data('ship-to') || '').toString().trim();
                        const finalBillTo = rawBillTo || custName;
                        const finalShipTo = rawShipTo || finalBillTo;
                        $('#bill_to').val(finalBillTo);
                        $('#ship_to').val(finalShipTo);
                    }
                });

                // Trigger kalkulasi saat ada select2 teknis yang berubah
                $('.select2').not('#customer_id, #cetakia_user_id, #cetakia_terms').on('change', function() {
                    triggerCalculation();
                });

                // Salin Bill To ke Ship To
                $('#btn_copy_bill_to_cal').on('click', function(e) {
                    e.preventDefault();
                    $('#ship_to').val($('#bill_to').val());
                });

                // Preset Masa Berlaku Penawaran
                $('.btn-expire-preset-cal').on('click', function(e) {
                    e.preventDefault();
                    $('.btn-expire-preset-cal').removeClass('active');
                    $(this).addClass('active');
                    const days = parseInt($(this).data('days')) || 30;
                    const baseDateVal = $('#quotation_date').val() || new Date().toISOString().split('T')[0];
                    const baseDate = new Date(baseDateVal);
                    baseDate.setDate(baseDate.getDate() + days);
                    const yyyy = baseDate.getFullYear();
                    const mm = String(baseDate.getMonth() + 1).padStart(2, '0');
                    const dd = String(baseDate.getDate()).padStart(2, '0');
                    $('#quotation_expired').val(`${yyyy}-${mm}-${dd}`);
                });

                $('#quotation_date').on('change', function() {
                    const qDate = $(this).val();
                    const expDate = $('#quotation_expired').val();
                    if (qDate && (!expDate || expDate < qDate)) {
                        const d = new Date(qDate);
                        d.setDate(d.getDate() + 30);
                        const yyyy = d.getFullYear();
                        const mm = String(d.getMonth() + 1).padStart(2, '0');
                        const dd = String(d.getDate()).padStart(2, '0');
                        $('#quotation_expired').val(`${yyyy}-${mm}-${dd}`);
                        $('.btn-expire-preset-cal').removeClass('active');
                        $('.btn-expire-preset-cal[data-days="30"]').addClass('active');
                    }
                });

                $('#quotation_expired').on('input change', function() {
                    $('.btn-expire-preset-cal').removeClass('active');
                });
            }

            // Logika Diskon (Sama Persis dengan A3+ & Large Format)
            const discountRadios = document.querySelectorAll('input[name="discount_type"]');
            const discountValueField = document.getElementById('discountValueField');
            const discountValueInput = document.getElementById('discount_value');
            const discountSuffix = document.getElementById('discountSuffix');
            const discountValueLabel = document.getElementById('discountValueLabel');

            if (discountRadios.length) {
                discountRadios.forEach(radio => {
                    radio.addEventListener('change', function() {
                        const type = this.value;
                        if (type === 'none') {
                            if (discountValueField) discountValueField.hidden = true;
                            if (discountValueInput) {
                                discountValueInput.disabled = true;
                                discountValueInput.value = '';
                            }
                        } else if (type === 'percentage') {
                            if (discountValueField) discountValueField.hidden = false;
                            if (discountValueInput) {
                                discountValueInput.disabled = false;
                                discountValueInput.value = '';
                                discountValueInput.placeholder = 'Contoh: 10';
                            }
                            if (discountSuffix) discountSuffix.textContent = '%';
                            if (discountValueLabel) discountValueLabel.textContent = 'Persentase Diskon';
                        } else if (type === 'nominal') {
                            if (discountValueField) discountValueField.hidden = false;
                            if (discountValueInput) {
                                discountValueInput.disabled = false;
                                discountValueInput.value = '';
                                discountValueInput.placeholder = 'Contoh: 25.000';
                            }
                            if (discountSuffix) discountSuffix.textContent = 'Rp';
                            if (discountValueLabel) discountValueLabel.textContent = 'Nominal Diskon';
                        }
                        calculateTaxAndTotal();
                    });
                });
            }

            if (discountValueInput) {
                discountValueInput.addEventListener('input', function() {
                    const selectedType = document.querySelector('input[name="discount_type"]:checked')?.value;

                    if (selectedType === 'percentage') {
                        let value = this.value.replace(/[^0-9.]/g, '');
                        const parts = value.split('.');
                        if (parts.length > 2) {
                            value = parts[0] + '.' + parts.slice(1).join('');
                        }
                        this.value = value;
                    }

                    if (selectedType === 'nominal') {
                        const rawValue = this.value.replace(/\D/g, '');
                        this.value = rawValue ?
                            new Intl.NumberFormat('id-ID', {
                                maximumFractionDigits: 0
                            }).format(Number(rawValue)) :
                            '';
                    }
                    calculateTaxAndTotal();
                });
            }

            // Listener Toggle PPN 11%
            $('#tax_enabled_check').on('change', function() {
                calculateTaxAndTotal();
            });

            // 2. Setup Awal Kategori
            selectCalendarType('dinding');
        });

        function selectCalendarType(type) {
            document.getElementById('calendar_type').value = type;

            const btnDinding = document.getElementById('btnTypeDinding');
            const btnMeja = document.getElementById('btnTypeMeja');
            const deskSection = document.getElementById('deskOptionsSection');
            const wallFinishingSection = document.getElementById('wallFinishingSection');

            if (type === 'meja') {
                btnDinding.classList.remove('active');
                btnMeja.classList.add('active');
                deskSection.style.display = 'block';
                if (wallFinishingSection) wallFinishingSection.style.display = 'none';
            } else {
                btnMeja.classList.remove('active');
                btnDinding.classList.add('active');
                deskSection.style.display = 'none';
                if (wallFinishingSection) wallFinishingSection.style.display = 'block';
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

            let leaves = (type === 'meja') ? [3, 4, 6, 7, 12, 13] : [1, 2, 3, 4, 6, 7, 12, 13];
            leaves.forEach(val => {
                let label = val + ' Lembar';
                if (val === 1) label += ' (Poster 1 Lembar)';
                else if (val === 2) label += ' (6 Bulanan / Semester)';
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
            $('#finishing').val('auto').trigger('change.select2');
            $('#support_type').val('printing_fc').trigger('change.select2');
            $('#board_type').val('boot_30').trigger('change.select2');
            $('#customer_id').val('').trigger('change.select2');
            $('#cetakia_user_id').val('').trigger('change.select2');
            $('#cetakia_terms').val('0').trigger('change.select2');
            $('#tax_enabled_check').prop('checked', false);
            const liveTaxDetails = document.getElementById('liveTaxDetailsRow');
            if (liveTaxDetails) liveTaxDetails.style.display = 'none';

            // Reset Diskon
            const discountNoneRadio = document.querySelector('input[name="discount_type"][value="none"]');
            if (discountNoneRadio) discountNoneRadio.checked = true;
            const discountValueField = document.getElementById('discountValueField');
            if (discountValueField) discountValueField.hidden = true;
            const discountValueInput = document.getElementById('discount_value');
            if (discountValueInput) {
                discountValueInput.disabled = true;
                discountValueInput.value = '';
            }
            const discountSuffix = document.getElementById('discountSuffix');
            if (discountSuffix) discountSuffix.textContent = '%';
            const discWrap = document.getElementById('liveDiscountWrap');
            if (discWrap) discWrap.style.display = 'none';

            const cName = document.getElementById('customer_name');
            if (cName) cName.value = '';
            $('#bill_to').val('');
            $('#ship_to').val('');
            const cNotes = document.getElementById('customer_notes');
            if (cNotes) cNotes.value = '';
            const today = new Date().toISOString().split('T')[0];
            const next30 = new Date();
            next30.setDate(next30.getDate() + 30);
            const next30Str = `${next30.getFullYear()}-${String(next30.getMonth() + 1).padStart(2, '0')}-${String(next30.getDate()).padStart(2, '0')}`;
            $('#quotation_date').val(today);
            $('#quotation_expired').val(next30Str);
            $('.btn-expire-preset-cal').removeClass('active');
            $('.btn-expire-preset-cal[data-days="30"]').addClass('active');
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
                finishing: formData.get('finishing') || 'auto',
                support_type: formData.get('support_type'),
                board_type: formData.get('board_type'),
                price_type: formData.get('price_type') || 'general',
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
                        if (res.matrix) {
                            renderCalendarMatrixTable(res.matrix);
                        }
                    } else {
                        console.error("Gagal kalkulasi:", res.message);
                    }
                })
                .catch(err => {
                    console.error("Error request kalkulasi:", err);
                });
        }

        function renderResult(data) {
            // 1. Highlight Rekomendasi & PPN 11%
            calculateTaxAndTotal();
            document.getElementById('recBadgeText').textContent = data.recommendation_label;
            document.getElementById('recReasonText').textContent = data.recommendation_reason;

            // 2. Tabel Komparasi
            document.getElementById('compDigitalRoute').textContent = data.digital.available ? 'Digital Print A3+' :
                'Tidak Tersedia (Over Area)';
            document.getElementById('compDigitalHpp').textContent = data.digital.available ? 'Rp ' + Number(data.digital
                .hpp_per_pcs).toLocaleString('id-ID') : '-';
            document.getElementById('compDigitalSell').textContent = data.digital.available ? 'Rp ' + Number(data.digital
                .selling_price_pcs).toLocaleString('id-ID') : '-';
            document.getElementById('compDigitalTotal').textContent = data.digital.available ? 'Rp ' + Number(data.digital
                .total_offer).toLocaleString('id-ID') : '-';

            document.getElementById('compOffsetRoute').textContent = data.offset.machine_name;
            document.getElementById('compOffsetHpp').textContent = 'Rp ' + Number(data.offset.hpp_per_pcs).toLocaleString(
                'id-ID');
            document.getElementById('compOffsetSell').textContent = 'Rp ' + Number(data.offset.selling_price_pcs)
                .toLocaleString('id-ID');
            document.getElementById('compOffsetTotal').textContent = 'Rp ' + Number(data.offset.total_offer).toLocaleString(
                'id-ID');

            // 3. Breakdown Rekomendasi
            const isDigital = data.recommended_route === 'digital';
            const rec = isDigital ? data.digital : data.offset;

            document.getElementById('bValPaper').textContent = 'Rp ' + Number(isDigital ? 0 : rec.paper_cost)
                .toLocaleString('id-ID');
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
                document.getElementById('bValLam').textContent = 'Rp ' + Number(rec.lamination_cost).toLocaleString(
                'id-ID');
            } else {
                rowLam.style.display = 'none';
            }

            // Finishing
            if (data.calendar_type === 'meja') {
                document.getElementById('bLabelFinishing').textContent = 'Spiral Wire-O + Rakit Meja';
                document.getElementById('bValFinishing').textContent = 'Rp ' + Number(rec.spiral_fin_total).toLocaleString(
                    'id-ID');

                document.getElementById('rowSupportCost').style.display = 'flex';
                document.getElementById('bValSupport').textContent = 'Rp ' + Number(rec.support_total).toLocaleString(
                    'id-ID');
            } else {
                document.getElementById('bLabelFinishing').textContent = data.finishing_detail.name;
                document.getElementById('bValFinishing').textContent = 'Rp ' + Number(rec.finishing_cost).toLocaleString(
                    'id-ID');
                document.getElementById('rowSupportCost').style.display = 'none';

                const badgeTextEl = document.getElementById('finishingBadgeText');
                if (badgeTextEl && data.finishing_detail) {
                    badgeTextEl.textContent = 'Finishing Aktif: ' + data.finishing_detail.name + ' (Rp ' + Number(data.finishing_detail.cost_per_pcs).toLocaleString('id-ID') + '/pcs)';
                }
            }

            document.getElementById('bValHppTotal').textContent = 'Rp ' + Number(rec.hpp_total).toLocaleString('id-ID');
            document.getElementById('bValMarkupPercent').textContent = '+' + data.markup_percent + '% (HPP: Rp ' + Number(
                rec.hpp_per_pcs).toLocaleString('id-ID') + '/pcs)';

            // 4. Update Quick Info Metrics (Pengganti Tombol Salin)
            document.getElementById('infoProdEngine').textContent = isDigital ?
                'Digital A3+' :
                (data.offset.machine_key || 'Offset Press');
            document.getElementById('infoHppPcs').textContent = 'Rp ' + Number(rec.hpp_per_pcs).toLocaleString('id-ID');
            document.getElementById('infoMargin').textContent = '+' + data.markup_percent + '% (Tier Qty)';
            document.getElementById('infoPaperNeed').textContent = isDigital ?
                (Number(rec.total_sheets_a3).toLocaleString('id-ID') + ' Lbr A3+') :
                (Number(rec.plano_needed).toLocaleString('id-ID') + ' Lbr Plano');
        }

        function copyHargaPcs() {
            if (!latestResult || !latestResult.share_texts) return;
            const text = latestResult.share_texts.copy_price_pcs;
            copyTextToClipboard(
                text,
                function() {
                    showToast("success", "Harga / pcs berhasil disalin!");
                },
                function() {
                    showToast("error", "Gagal menyalin harga.");
                }
            );
        }

        function copySpesifikasiWA() {
            if (!latestResult || !latestResult.share_texts) return;
            const text = latestResult.share_texts.copy_wa_full;
            copyTextToClipboard(
                text,
                function() {
                    showToast("success", "Rincian spesifikasi untuk WhatsApp berhasil disalin!");
                },
                function() {
                    showToast("error", "Gagal menyalin spesifikasi.");
                }
            );
        }

        function showWaToastCalendar(title, desc) {
            let container = document.getElementById('waToastContainerCalendar');
            if (!container) {
                container = document.createElement('div');
                container.id = 'waToastContainerCalendar';
                container.className = 'wa-toast-container';
                document.body.appendChild(container);
            }
            const toast = document.createElement('div');
            toast.className = 'wa-toast';
            toast.innerHTML = `
                <div class="wa-toast-icon">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div class="wa-toast-content">
                    <div class="wa-toast-title">${title}</div>
                    <div class="wa-toast-desc">${desc}</div>
                </div>
                <button type="button" class="wa-toast-close" aria-label="Tutup">&times;</button>
            `;
            container.appendChild(toast);
            requestAnimationFrame(() => toast.classList.add('is-show'));
            const closeFn = () => {
                toast.classList.remove('is-show');
                setTimeout(() => toast.remove(), 350);
            };
            toast.querySelector('.wa-toast-close').addEventListener('click', closeFn);
            setTimeout(closeFn, 3000);
        }

        function fallbackCopyTextCalendar(text, cb) {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            try {
                document.execCommand('copy');
                if (cb) cb();
            } catch (err) {
                console.error('Fallback copy error:', err);
            }
            document.body.removeChild(ta);
        }

        function copyFormattedWhatsAppCalendar(e) {
            if (!latestResult) return;
            const btn = e ? e.currentTarget : null;
            const r = latestResult;

            const custName = (document.getElementById('customer_name')?.value || '').trim() ||
                (typeof $ !== 'undefined' ? $('#customer_id option:selected').data('name') : '') ||
                'Pelanggan';
            const notes = (document.getElementById('customer_notes')?.value || '').trim();
            const calTypeLabel = r.calendar_type === 'meja' ? 'Kalender Meja' : 'Kalender Dinding';
            const sizeLabel = `${r.size_label || '-'} (${r.orientation || '-'})`;
            const matName = r.material_name || '-';
            const leafText = `${r.leaf_count || '-'} Lembar`;
            const lamText = (!r.lamination || r.lamination === 'none') ? 'Tanpa Laminasi' : r.lamination.toUpperCase();
            const finText = r.calendar_type === 'meja' ?
                ((r.support_detail ? r.support_detail.name : 'Dudukan') + ' + Spiral Wire-O') :
                (r.finishing_detail ? r.finishing_detail.name : 'Klem Seng');
            const qtyText = `${Number(r.qty || 0).toLocaleString('id-ID')} pcs`;
            const isTax = $('#tax_enabled_check').is(':checked');
            const subtotal = Number(r.recommended_total_offer || 0);
            const qty = Number(r.qty || 1);

            const discountType = document.querySelector('input[name="discount_type"]:checked')?.value || 'none';
            const discountRaw = ($('#discount_value').val() || '').replace(/\./g, '').replace(',', '.');
            const discountVal = parseFloat(discountRaw) || 0;
            let discAmt = 0;
            if (discountType === 'percentage') {
                discAmt = Math.min(subtotal, Math.round(subtotal * (discountVal / 100)));
            } else if (discountType === 'nominal') {
                discAmt = Math.min(subtotal, discountVal);
            }

            const dpp = Math.max(0, subtotal - discAmt);
            const taxAmt = isTax ? Math.round(dpp * 0.11) : 0;
            const grandTotal = dpp + taxAmt;
            const unitPrice = qty > 0 ? (grandTotal / qty) : 0;
            const pricePcs = formatRupiah(unitPrice);
            const totalOffer = formatRupiah(grandTotal);

            const textLines = [
                `*SURAT PENAWARAN HARGA (SPH) - CIPTA GRAFIKA*`,
                `Kepada Yth: *${custName}*`,
                `Tanggal: ${new Date().toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}`,
                `---------------------------------------------`,
                `*Produk:* Cetak ${calTypeLabel}`,
                `*Ukuran:* ${sizeLabel}`,
                `*Material / Bahan:* ${matName} (${leafText})`,
                `*Laminasi:* ${lamText}`,
                `*Finishing:* ${finText}`,
                `*Kuantitas:* ${qtyText}`,
                discAmt > 0 ? `*Diskon:* -${formatRupiah(discAmt)} (${discountType === 'percentage' ? discountVal + '%' : 'Nominal'})` : '',
                `*Total Estimasi:* *${totalOffer}* (${pricePcs} / pcs)${isTax ? ' _(Termasuk PPN 11%)_' : ''}`,
                notes ? `*Catatan Khusus:* ${notes}` : '',
                `---------------------------------------------`,
                `_Harga resmi terstandar dan berlaku selama penawaran masih aktif._`,
                `Terima kasih telah mempercayakan kebutuhan percetakan & merchandise kepada Cipta Grafika!`
            ].filter(Boolean).join('\n');

            const onSuccess = () => {
                if (btn) {
                    const originalHtml = btn.innerHTML;
                    btn.innerHTML = `<i class="bi bi-check-circle-fill"></i> <span>Tersalin ke Clipboard!</span>`;
                    btn.style.pointerEvents = 'none';
                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                        btn.style.pointerEvents = '';
                    }, 2200);
                }
                showWaToastCalendar(
                    'Format Penawaran Disalin!',
                    'Format teks penawaran harga Kalender telah disalin ke clipboard dan siap dikirim ke WhatsApp.'
                );
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(textLines).then(onSuccess).catch(() => {
                    fallbackCopyTextCalendar(textLines, onSuccess);
                });
            } else {
                fallbackCopyTextCalendar(textLines, onSuccess);
            }
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

            const isTax = $('#tax_enabled_check').is(':checked');
            const subtotal = Number(r.recommended_total_offer) || 0;
            const qty = Number(r.qty) || 1;

            const discountType = document.querySelector('input[name="discount_type"]:checked')?.value || 'none';
            const discountRaw = ($('#discount_value').val() || '').replace(/\./g, '').replace(',', '.');
            const discountVal = parseFloat(discountRaw) || 0;
            let discAmt = 0;
            if (discountType === 'percentage') {
                discAmt = Math.min(subtotal, Math.round(subtotal * (discountVal / 100)));
            } else if (discountType === 'nominal') {
                discAmt = Math.min(subtotal, discountVal);
            }

            const dpp = Math.max(0, subtotal - discAmt);
            const taxAmt = isTax ? Math.round(dpp * 0.11) : 0;
            const grandTotal = dpp + taxAmt;
            const unitPrice = qty > 0 ? (grandTotal / qty) : 0;

            document.getElementById('modalPricePcs').textContent = formatRupiah(unitPrice) + ' / pcs';
            document.getElementById('modalTotalPrice').textContent = formatRupiah(grandTotal);

            const modalDiscRow = document.getElementById('modalDiscountRow');
            const modalDiscAmt = document.getElementById('modalDiscountAmount');
            const modalDiscDesc = document.getElementById('modalDiscountDesc');
            if (modalDiscRow) modalDiscRow.style.display = discAmt > 0 ? 'flex' : 'none';
            if (modalDiscAmt) modalDiscAmt.textContent = '- ' + formatRupiah(discAmt);
            if (modalDiscDesc) modalDiscDesc.textContent = (discountType === 'percentage' ? `${discountVal}%` : 'Nominal');

            const modalTaxRow = document.getElementById('modalTaxRow');
            const modalTaxAmount = document.getElementById('modalTaxAmount');
            if (modalTaxRow) modalTaxRow.style.display = isTax ? 'flex' : 'none';
            if (modalTaxAmount) modalTaxAmount.textContent = '+ ' + formatRupiah(taxAmt);

            // Update Info Pelanggan & Tanggal di Modal
            const cName = (document.getElementById('customer_name')?.value || '').trim() || $('#customer_id option:selected').data('name') || 'Pelanggan Umum';
            const qDateVal = document.getElementById('quotation_date')?.value;
            const qExpVal = document.getElementById('quotation_expired')?.value;
            const formatDmy = (dStr) => {
                if (!dStr) return '-';
                const parts = dStr.split('-');
                return parts.length === 3 ? `${parts[2]}/${parts[1]}/${parts[0]}` : dStr;
            };
            const elCust = document.getElementById('modalCustName');
            const elQDate = document.getElementById('modalQuoteDate');
            const elQExp = document.getElementById('modalQuoteExpired');
            const elQExpNotice = document.getElementById('modalQuoteExpiredNotice');
            if (elCust) elCust.textContent = cName;
            if (elQDate && qDateVal) elQDate.textContent = formatDmy(qDateVal);
            if (elQExp && qExpVal) elQExp.textContent = formatDmy(qExpVal);
            if (elQExpNotice && qExpVal) elQExpNotice.textContent = formatDmy(qExpVal);

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

        let latestMatrixData = null;

        function renderCalendarMatrixTable(matrixData) {
            if (!matrixData || !matrixData.tiers) return;
            latestMatrixData = matrixData;

            const tbody = document.getElementById('calendarMatrixTableBody');
            if (!tbody) return;

            const currentQty = parseInt(document.getElementById('qty').value) || 0;
            let rowsHtml = '';

            matrixData.tiers.forEach(row => {
                const isSelected = (row.qty === currentQty);
                const rowClass = isSelected ? 'is-selected' : '';

                const isDigital = (row.recommended_route === 'digital');
                const routeBadge = isDigital ?
                    `<span class="badge-route-digital"><i class="bi bi-printer"></i> Digital A3+</span>` :
                    `<span class="badge-route-offset"><i class="bi bi-gear-wide-connected"></i> ${row.machine_label}</span>`;

                rowsHtml += `
                    <tr class="${rowClass}">
                        <td style="font-weight: 700;">
                            ${row.qty.toLocaleString('id-ID')} pcs
                            ${isSelected ? '<span class="badge-selected">Dipilih</span>' : ''}
                        </td>
                        <td>${routeBadge}</td>
                        <td style="color: var(--t-muted);">Rp ${Number(row.total_hpp).toLocaleString('id-ID')}</td>
                        <td>Rp ${Number(row.hpp_per_pcs).toLocaleString('id-ID')}</td>
                        <td class="col-highlight">Rp ${Number(row.selling_price_pcs).toLocaleString('id-ID')}</td>
                        <td class="col-highlight">Rp ${Number(row.total_selling_price).toLocaleString('id-ID')}</td>
                        <td style="text-align: center;">
                            <button type="button" class="btn-select-qty" onclick="applyMatrixQty(${row.qty})">
                                <i class="bi bi-check2"></i> Pilih
                            </button>
                        </td>
                    </tr>
                `;
            });

            tbody.innerHTML = rowsHtml;
            filterCalendarMatrixRows();
        }


        function filterCalendarMatrixRows() {
            const input = document.getElementById("calendarMatrixSearch");
            const clearBtn = document.getElementById("calendarMatrixSearchClear");
            const tbody = document.getElementById("calendarMatrixTableBody");
            if (!input || !tbody) return;

            const query = (input.value || "").trim().toLowerCase();
            if (clearBtn) {
                clearBtn.style.display = query ? "block" : "none";
            }

            const rows = Array.from(tbody.querySelectorAll("tr:not(.no-search-result)"));
            const prevNoResult = tbody.querySelector(".no-search-result");
            if (prevNoResult) prevNoResult.remove();

            if (rows.length === 0) return;
            if (rows.length === 1 && rows[0].cells.length === 1 && rows[0].cells[0].colSpan > 1) {
                return;
            }

            let matchCount = 0;
            const cleanQuery = query.replace(/\./g, "");

            rows.forEach(row => {
                const text = row.innerText.toLowerCase();
                const cleanText = text.replace(/\./g, "");
                const isMatch = !query || text.includes(query) || (cleanQuery && cleanText.includes(cleanQuery));

                row.style.display = isMatch ? "" : "none";
                if (isMatch) matchCount++;
            });

            if (query && matchCount === 0) {
                const noRow = document.createElement("tr");
                noRow.className = "no-search-result";
                noRow.innerHTML =
                    `<td colspan="7" style="text-align: center; padding: 24px; color: var(--t-muted);"><i class="bi bi-search"></i> Tidak ada baris yang sesuai dengan pencarian "<strong>${query}</strong>".</td>`;
                tbody.appendChild(noRow);
            }
        }

        function bindCalendarSearchEvents() {
            const calSearchInput = document.getElementById("calendarMatrixSearch");
            if (calSearchInput) {
                calSearchInput.addEventListener("input", filterCalendarMatrixRows);
                calSearchInput.addEventListener("keyup", filterCalendarMatrixRows);
            }
            const calSearchClear = document.getElementById("calendarMatrixSearchClear");
            if (calSearchClear) {
                calSearchClear.addEventListener("click", function() {
                    if (calSearchInput) {
                        calSearchInput.value = "";
                        filterCalendarMatrixRows();
                        calSearchInput.focus();
                    }
                });
            }
        }
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", bindCalendarSearchEvents);
        } else {
            bindCalendarSearchEvents();
        }

        function applyMatrixQty(val) {
            setQty(val);
            const target = document.getElementById('smartRecCard');
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }
        }

        // Fungsi penyalin teks handal (support Modern Clipboard & Fallback execCommand)
        function copyTextToClipboard(text, onSuccess, onError) {
            if (!text) {
                if (onError) onError(new Error("Teks kosong"));
                return;
            }

            function fallbackCopy() {
                try {
                    const textarea = document.createElement("textarea");
                    textarea.value = text;
                    textarea.setAttribute("readonly", "");
                    textarea.style.position = "fixed";
                    textarea.style.left = "-9999px";
                    textarea.style.top = "0";
                    textarea.style.opacity = "0";
                    document.body.appendChild(textarea);
                    textarea.focus();
                    textarea.select();
                    textarea.setSelectionRange(0, textarea.value.length);
                    const successful = document.execCommand("copy");
                    document.body.removeChild(textarea);
                    if (successful) {
                        if (onSuccess) onSuccess();
                    } else {
                        if (onError) onError(new Error("execCommand failed"));
                    }
                } catch (err) {
                    if (onError) onError(err);
                }
            }

            if (navigator.clipboard && typeof navigator.clipboard.writeText === "function") {
                navigator.clipboard.writeText(text)
                    .then(() => {
                        if (onSuccess) onSuccess();
                    })
                    .catch(() => {
                        fallbackCopy();
                    });
            } else {
                fallbackCopy();
            }
        }

        function copyMatrixToClipboard() {
            if (!latestMatrixData || !latestMatrixData.wa_matrix) {
                showToast("error", "Data matriks harga belum selesai dimuat, silakan tunggu sejenak.");
                return;
            }

            copyTextToClipboard(
                latestMatrixData.wa_matrix,
                function() {
                    const btn = document.getElementById("btnCopyMatrixWa");
                    const btnText = document.getElementById("btnCopyMatrixWaText");
                    if (btn && btnText) {
                        const originalText = btnText.textContent;
                        btnText.textContent = "✓ Tersalin!";
                        btn.style.background = "#15803d";
                        setTimeout(() => {
                            btnText.textContent = originalText;
                            btn.style.background = "#25D366";
                        }, 2500);
                    }
                    showToast("success", "Daftar harga bertingkat berhasil disalin ke clipboard!");
                },
                function(err) {
                    console.error("Gagal menyalin matriks WA:", err);
                    showToast("error", "Gagal menyalin teks ke clipboard.");
                }
            );
        }

        // =========================================================
        // CETAKIA INTEGRATION LOGIC (DEV MODE)
        // =========================================================

        // Buka Modal Konfirmasi Kirim Penawaran
        $('#btnSendToCetakia').on('click', function(e) {
            e.preventDefault();

            if (!latestResult) {
                showNotificationToast('Perhitungan Belum Siap', 'Silakan lengkapi formulir estimasi terlebih dahulu hingga hasil hitungan muncul.', 'warning');
                return;
            }

            const customerId = $('#customer_id').val();
            if (!customerId) {
                showNotificationToast('Pilih Pelanggan', 'Silakan pilih Nama Pelanggan (Cetakia) terlebih dahulu.', 'warning');
                $('#customer_id').select2('open');
                return;
            }

            const userId = $('#cetakia_user_id').val();
            if (!userId) {
                showNotificationToast('Pilih Sales PIC', 'Silakan pilih Sales / PIC Penanggung Jawab terlebih dahulu.', 'warning');
                $('#cetakia_user_id').select2('open');
                return;
            }

            const billTo = ($('#bill_to').val() || '').trim();
            const shipTo = ($('#ship_to').val() || '').trim();
            if (!billTo || !shipTo) {
                showNotificationToast('Alamat Belum Lengkap', 'Silakan lengkapi Alamat Penagihan (Bill To) dan Alamat Pengiriman (Ship To).', 'warning');
                return;
            }

            const isTax = $('#tax_enabled_check').is(':checked');
            const subtotal = Number(latestResult.recommended_total_offer) || 0;
            const qty = Number(latestResult.qty) || 1;

            const discountType = document.querySelector('input[name="discount_type"]:checked')?.value || 'none';
            const discountRaw = ($('#discount_value').val() || '').replace(/\./g, '').replace(',', '.');
            const discountVal = parseFloat(discountRaw) || 0;
            let discAmt = 0;
            if (discountType === 'percentage') {
                discAmt = Math.min(subtotal, Math.round(subtotal * (discountVal / 100)));
            } else if (discountType === 'nominal') {
                discAmt = Math.min(subtotal, discountVal);
            }

            const dpp = Math.max(0, subtotal - discAmt);
            const taxAmt = isTax ? Math.round(dpp * 0.11) : 0;
            const grandTotal = dpp + taxAmt;

            const custName = $('#customer_id option:selected').data('name') || $('#customer_id option:selected').text().trim() || 'Pelanggan Terpilih';
            const salesName = $('#cetakia_user_id option:selected').text().trim() || '-';
            const termsVal = parseInt($('#cetakia_terms').val() || '0');
            const termsText = $('#cetakia_terms option:selected').text().trim() || 'Cash / Tunai';

            const r = latestResult;
            const isMeja = (r.calendar_type === 'meja');
            let itemName = '';
            let itemDesc = '';

            if (isMeja) {
                itemName = 'Cetak Kalender Meja ' + (r.size_label || '') + ' (' + (r.recommended_route === 'digital' ? 'Digital Print A3+' : 'Offset SM 52') + ')';
                itemDesc = [
                    (r.material_name || '-'),
                    (r.leaf_count || 1) + ' Lembar',
                    (r.support_detail?.name || 'Board Stand'),
                    'Spiral Wire-O',
                    (r.lamination === 'none' ? 'Tanpa Laminasi' : r.lamination.toUpperCase()),
                    qty.toLocaleString('id-ID') + ' pcs'
                ].join('_');
            } else {
                const finishingName = (r.finishing_detail?.name || 'Klem Seng Spiral').replace(/[\/\\:]/g, ' ');
                itemName = 'Cetak Kalender Dinding ' + (r.size_label || '') + ' (' + (r.recommended_route === 'digital' ? 'Digital Print A3+' : ('Offset ' + (r.offset?.machine_key || 'SM52'))) + ')';
                itemDesc = [
                    (r.material_name || '-'),
                    (r.leaf_count || 1) + ' Lembar',
                    finishingName,
                    (r.lamination === 'none' ? 'Tanpa Laminasi' : r.lamination.toUpperCase()),
                    qty.toLocaleString('id-ID') + ' pcs'
                ].join('_');
            }

            // Update info di Modal Konfirmasi
            $('#confirmCustomerName').text(custName);
            $('#confirmSalesName').text(salesName);
            $('#confirmItemDesc').text(itemName + ' (' + qty.toLocaleString('id-ID') + ' pcs)');
            $('#confirmTermsText').text(termsText);
            if (discAmt > 0) {
                $('#confirmDiscountRow').show();
                $('#confirmDiscountText').text('- ' + formatRupiah(discAmt) + ' (' + (discountType === 'percentage' ? discountVal + '%' : 'Nominal') + ')');
            } else {
                $('#confirmDiscountRow').hide();
            }
            $('#confirmTaxText').text(isTax ? 'PPN 11% (+ ' + formatRupiah(taxAmt) + ')' : 'Non-PPN (0%)');
            $('#confirmGrandTotal').text(formatRupiah(grandTotal));

            // Simpan payload siap kirim
            pendingCetakiaPayload = {
                customer_id: customerId,
                user_id: userId,
                quotation_date: $('#quotation_date').val() || new Date().toISOString().split('T')[0],
                quotation_expired: $('#quotation_expired').val() || new Date(Date.now() + 30*86400000).toISOString().split('T')[0],
                bill_to: billTo,
                ship_to: shipTo,
                notes: ($('#customer_notes').val() || '').trim(),
                terms: termsVal,
                sub_total: subtotal,
                discount_enabled: discAmt > 0,
                discount_type: discountType === 'percentage' ? 'percent' : 'nominal',
                discount: discountVal,
                discount_amount: discAmt,
                tax_enabled: isTax,
                tax: isTax ? 11 : 0,
                tax_amount: taxAmt,
                pph_enabled: false,
                pph: 0,
                pph_amount: 0,
                grand_total: grandTotal,
                items: [
                    {
                        type: 'product',
                        item: itemName,
                        category: 'Cetak Kalender',
                        material_name: r.material_name || '',
                        description: itemDesc,
                        qty: qty,
                        price: Number(r.recommended_price_pcs),
                        unit_id: '019b8a5f-0400-77c0-aeac-3f9eeaed8889',
                        unit_name: 'Pcs',
                        unit_code: 'pcs',
                        amount: subtotal
                    }
                ],
                _display: {
                    customerName: custName,
                    salesName: salesName,
                    grandTotal: grandTotal
                }
            };

            $('#modalCetakiaConfirm').addClass('is-open');
        });

        // Tombol Tutup / Batal di Modal Konfirmasi
        $('#btnCloseConfirmModal, #btnCancelConfirmModal').on('click', function() {
            $('#modalCetakiaConfirm').removeClass('is-open');
        });

        // Eksekusi Kirim dari Modal Konfirmasi
        $('#btnExecuteSendToCetakia').on('click', function() {
            if (!pendingCetakiaPayload) return;

            const btn = $(this);
            const originalHtml = btn.html();
            btn.prop('disabled', true).html('<i class=\"bi bi-arrow-repeat spin\"></i> Mengirim...');

            const disp = pendingCetakiaPayload._display || {};
            const payloadData = Object.assign({}, pendingCetakiaPayload);
            delete payloadData._display;

            $.ajax({
                url: '{{ route("admin_cetakia_quotation_store") }}',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(payloadData),
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(res) {
                    btn.prop('disabled', false).html(originalHtml);
                    $('#modalCetakiaConfirm').removeClass('is-open');

                    if (res.success && res.data) {
                        const qCode = (res.data.quotation && res.data.quotation.quotation_code) ? res.data.quotation.quotation_code : (res.data.quotation_code || res.data.code || '-');
                        $('#cetakiaSuccessCode').text(qCode);
                        $('#cetakiaSuccessCustomer').text(disp.customerName || '-');
                        $('#cetakiaSuccessSales').text(disp.salesName || '-');
                        $('#cetakiaSuccessGrandTotal').text(formatRupiah(disp.grandTotal || 0));
                        $('#modalCetakiaSuccess').addClass('is-open');
                        showNotificationToast('Quotation Berhasil!', 'Penawaran ' + qCode + ' telah resmi tercatat di Cetakia (Dev Mode).', 'success');
                    } else {
                        showNotificationToast('Pemberitahuan Cetakia', res.message || 'Gagal membuat penawaran di Cetakia.', 'error');
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false).html(originalHtml);
                    $('#modalCetakiaConfirm').removeClass('is-open');
                    let errMsg = 'Terjadi kesalahan sistem saat menghubungi server.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    showNotificationToast('Gagal Terkirim', errMsg, 'error');
                }
            });
        });

        // Modal Cetakia Success Handlers
        $('#btnCloseCetakiaModal').on('click', function() {
            $('#modalCetakiaSuccess').removeClass('is-open');
        });
        $('#btnCopyCetakiaCode').on('click', function() {
            const code = $('#cetakiaSuccessCode').text().trim();
            if (code && code !== '-') {
                copyTextToClipboard(code, function() {
                    showNotificationToast('Nomor Disalin!', 'Kode penawaran ' + code + ' telah disalin ke clipboard.', 'success');
                });
            }
        });

        // Overlay & Esc Key Close Listeners
        const confirmOverlay = document.getElementById('modalCetakiaConfirm');
        if (confirmOverlay) {
            confirmOverlay.addEventListener('click', function(e) {
                if (e.target === this) $('#modalCetakiaConfirm').removeClass('is-open');
            });
        }
        const successOverlay = document.getElementById('modalCetakiaSuccess');
        if (successOverlay) {
            successOverlay.addEventListener('click', function(e) {
                if (e.target === this) $('#modalCetakiaSuccess').removeClass('is-open');
            });
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.key === 'Esc') {
                $('#modalCetakiaConfirm').removeClass('is-open');
                $('#modalCetakiaSuccess').removeClass('is-open');
            }
        });


                // Safe Toast Adapter: kompatibel dengan pemanggilan 1 argumen maupun 2 argumen
        (function() {
            const systemToast = window.showToast;
            window.showToast = function(typeOrMsg, msg) {
                if (typeof msg === "undefined") {
                    const text = String(typeOrMsg || "");
                    const isErr = text.toLowerCase().includes("gagal") ||
                        text.toLowerCase().includes("belum") ||
                        text.toLowerCase().includes("tidak") ||
                        text.toLowerCase().includes("error");
                    const detectedType = isErr ? "error" : "success";
                    if (typeof systemToast === "function") {
                        systemToast(detectedType, text);
                    } else {
                        const toast = document.getElementById("calendarToast");
                        if (toast) {
                            document.getElementById("calendarToastMsg").textContent = text;
                            toast.classList.add("show");
                            setTimeout(() => toast.classList.remove("show"), 2500);
                        }
                    }
                } else {
                    if (typeof systemToast === "function") {
                        systemToast(typeOrMsg, msg);
                    } else {
                        const toast = document.getElementById("calendarToast");
                        if (toast) {
                            document.getElementById("calendarToastMsg").textContent = msg;
                            toast.classList.add("show");
                            setTimeout(() => toast.classList.remove("show"), 2500);
                        }
                    }
                }
            };
        })();
    </script>
@endsection

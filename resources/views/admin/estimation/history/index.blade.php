@extends('admin_master')

@section('contents')
    <style>
        /* Card Container & Header */
        .cat-card-container {
            padding: 24px;
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            border-radius: 16px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            box-sizing: border-box;
            width: 100%;
        }

        .cat-hero-compact {
            margin-bottom: 20px;
        }

        .cat-hero-compact .hero-text {
            max-width: 100%;
        }

        .cat-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-soft);
            margin-bottom: 18px;
        }

        .cat-card-header-main {
            display: flex;
            align-items: center;
            flex: 1;
            min-width: 260px;
        }

        .cat-card-title-wrap {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .cat-title-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 4px;
        }

        .cat-card-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.15), rgba(59, 130, 246, 0.05));
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            border: 1px solid rgba(59, 130, 246, 0.2);
            flex-shrink: 0;
        }

        html[data-theme="dark"] .cat-card-icon {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.25), rgba(37, 99, 235, 0.1));
            color: #60a5fa;
            border-color: rgba(96, 165, 250, 0.3);
        }

        .cat-card-title {
            font-size: 17px;
            font-weight: 700;
            color: var(--t-base);
            margin: 0;
            letter-spacing: -0.01em;
        }

        .cat-card-desc {
            font-size: 13px;
            color: var(--t-muted);
            margin: 0;
        }

        .cat-badge-total {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            background: var(--bg-hover);
            color: var(--t-base);
            border: 1px solid var(--border-soft);
        }

        .cat-pulse-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            animation: catPulse 2s infinite;
        }

        @keyframes catPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
            }
        }

        /* Actions in Card Header */
        .cat-card-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .cat-btn-head-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            height: 38px;
            padding: 0 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            background: #2563eb !important;
            color: #ffffff !important;
            border: 1px solid #2563eb !important;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.28);
            transition: all 0.18s ease;
            white-space: nowrap;
            cursor: pointer;
            box-sizing: border-box;
            text-decoration: none;
        }

        .cat-btn-head-primary:hover {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.38);
            color: #ffffff !important;
        }

        .cat-btn-head-primary i {
            font-size: 14px;
        }

        .cat-btn-head-primary svg {
            flex-shrink: 0;
        }

        /* Filter Toolbar */
        .cat-filter-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 12px 16px;
            margin-bottom: 18px;
            background: rgba(248, 250, 252, 0.85);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 12px;
            transition: all 0.2s ease;
            box-sizing: border-box;
            width: 100%;
        }

        html[data-theme="dark"] .cat-filter-card {
            background: rgba(11, 17, 32, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.02);
        }

        .cat-filter-group-left {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            flex: 1;
            min-width: 0;
            box-sizing: border-box;
        }

        .cat-search-box {
            position: relative;
            flex: 1;
            min-width: 220px;
            max-width: 320px;
            height: 38px;
            box-sizing: border-box;
        }

        .cat-search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--t-muted);
            font-size: 13px;
            pointer-events: none;
            z-index: 1;
        }

        .cat-search-hint {
            position: absolute;
            top: calc(100% + 6px);
            left: 8px;
            z-index: 5;
            padding: 5px 8px;
            border: 1px solid var(--border-soft);
            border-radius: 6px;
            background: var(--bg-card);
            color: var(--t-muted);
            font-size: 11px;
            white-space: nowrap;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        }

        .cat-search-hint[hidden] {
            display: none;
        }

        .cat-filter-input {
            width: 100% !important;
            box-sizing: border-box !important;
            height: 38px;
            padding: 8px 32px 8px 36px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--t-base);
            font-size: 13px;
            transition: all 0.15s ease;
        }

        html[data-theme="light"] .cat-filter-input {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        html[data-theme="dark"] .cat-filter-input {
            background: rgba(20, 27, 45, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--t-base);
        }

        html[data-theme="dark"] .cat-filter-input:focus {
            background: rgba(20, 27, 45, 0.95);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.18);
        }

        .cat-filter-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .cat-clear-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: 0;
            color: var(--t-muted);
            cursor: pointer;
            font-size: 13px;
            padding: 2px;
            display: flex;
            align-items: center;
            z-index: 1;
        }

        .cat-clear-btn:hover {
            color: var(--t-base);
        }

        .cat-select-box {
            position: relative;
            min-width: 180px;
            height: 38px;
            box-sizing: border-box;
        }

        .cat-filter-subgroup {
            display: flex;
            align-items: center;
            gap: 10px;
            box-sizing: border-box;
        }

        .cat-select-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13.5px;
            pointer-events: none !important;
            user-select: none !important;
            -webkit-user-select: none !important;
            background: transparent !important;
            z-index: 2;
            transition: color 0.18s ease;
            line-height: 1;
        }

        html[data-theme="light"] .cat-select-icon {
            color: #64748b !important;
            background: transparent !important;
        }

        html[data-theme="light"] .cat-select-box:hover .cat-select-icon,
        html[data-theme="light"] .cat-select-box:focus-within .cat-select-icon,
        html[data-theme="light"] .cat-select-box.is-open .cat-select-icon,
        html[data-theme="light"] .cat-select-box:has(.select2-container--open) .cat-select-icon {
            color: #2563eb !important;
        }

        .cat-select-box .select2-container {
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .cat-select-box .select2-container .select2-selection--single {
            height: 38px !important;
            border-radius: 8px !important;
            border: 1px solid var(--border) !important;
            background-color: var(--bg-card) !important;
            display: flex !important;
            align-items: center !important;
            transition: all 0.2s ease !important;
            box-sizing: border-box !important;
            width: 100% !important;
        }

        html[data-theme="light"] .cat-select-box .select2-container .select2-selection--single,
        html[data-theme="light"] .cat-length-box .select2-container .select2-selection--single {
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #0f172a !important;
        }

        html[data-theme="light"] .cat-select-box .select2-container:hover .select2-selection--single,
        html[data-theme="light"] .cat-length-box .select2-container:hover .select2-selection--single,
        html[data-theme="light"] .cat-select-box .select2-container--open .select2-selection--single,
        html[data-theme="light"] .cat-length-box .select2-container--open .select2-selection--single,
        html[data-theme="light"] .cat-select-box.is-open .select2-selection--single {
            border-color: #2563eb !important;
            background-color: #ffffff !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14) !important;
        }

        .cat-select-box .select2-container .select2-selection--single .select2-selection__rendered {
            padding-left: 32px !important;
            padding-right: 24px !important;
            line-height: 36px !important;
            font-size: 13px !important;
            color: var(--t-base) !important;
            font-weight: 500 !important;
        }

        .cat-select-box .select2-container .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            right: 8px !important;
        }

        /* Date Range Group (Clean, Tanpa Label Dari/Sampai, 100% Support Dark & Light) */
        .est-daterange-group {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-sizing: border-box;
        }

        .est-date-box {
            position: relative;
            display: inline-flex;
            align-items: center;
            height: 38px;
            width: 155px;
            min-width: 145px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--bg-card);
            box-sizing: border-box;
            transition: all 0.2s ease;
            cursor: pointer;
            overflow: hidden;
        }

        html[data-theme="light"] .est-date-box {
            background: #ffffff;
            border-color: #cbd5e1;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        html[data-theme="light"] .est-date-box:hover,
        html[data-theme="light"] .est-date-box:focus-within {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
        }

        html[data-theme="dark"] .est-date-box {
            background: rgba(20, 27, 45, 0.75);
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.02);
        }

        html[data-theme="dark"] .est-date-box:hover,
        html[data-theme="dark"] .est-date-box:focus-within {
            border-color: rgba(96, 165, 250, 0.45);
            background: rgba(26, 36, 61, 0.9);
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.16);
        }

        .est-date-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13.5px;
            color: var(--t-muted);
            pointer-events: none;
            z-index: 3;
            transition: color 0.18s ease;
            line-height: 1;
        }

        html[data-theme="light"] .est-date-icon {
            color: #64748b !important;
        }

        html[data-theme="light"] .est-date-box:hover .est-date-icon,
        html[data-theme="light"] .est-date-box:focus-within .est-date-icon {
            color: #2563eb !important;
        }

        html[data-theme="dark"] .est-date-icon {
            color: #94a3b8 !important;
        }

        html[data-theme="dark"] .est-date-box:hover .est-date-icon,
        html[data-theme="dark"] .est-date-box:focus-within .est-date-icon {
            color: #60a5fa !important;
        }

        .est-date-input {
            width: 100% !important;
            height: 38px !important;
            padding: 0 8px 0 32px !important;
            border: none !important;
            outline: none !important;
            box-shadow: none !important;
            background: transparent !important;
            font-family: JetBrains Mono, monospace !important;
            font-size: 12.5px !important;
            font-weight: 500 !important;
            box-sizing: border-box !important;
            cursor: pointer !important;
            position: relative;
            z-index: 1;
        }

        html[data-theme="light"] .est-date-input {
            color-scheme: light !important;
            color: #0f172a !important;
        }

        html[data-theme="dark"] .est-date-input {
            color-scheme: dark !important;
            color: #f1f5f9 !important;
        }

        /* Native indicator transparan menutupi seluruh input:
               - Menghilangkan icon hitam ganda (double calendar icon)
               - Memastikan setiap klik di area box/icon memicu calendar picker browser */
        .est-date-input::-webkit-calendar-picker-indicator {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            opacity: 0 !important;
            cursor: pointer !important;
            z-index: 2 !important;
        }

        .est-date-separator {
            font-size: 12px;
            font-weight: 700;
            color: var(--t-muted);
            user-select: none;
            white-space: nowrap;
            padding: 0 2px;
        }

        /* Modern Empty State in DataTable */
        .est-empty-state {
            padding: 40px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: var(--t-muted);
        }

        .est-empty-icon-box {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: var(--bg-hover);
            border: 1px solid var(--border-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: var(--t-muted);
            margin-bottom: 12px;
        }

        html[data-theme="dark"] .est-empty-icon-box {
            background: rgba(30, 41, 69, 0.5);
            border-color: rgba(255, 255, 255, 0.08);
            color: #94a3b8;
        }

        .est-empty-title {
            font-size: 14.5px;
            font-weight: 700;
            color: var(--t-base);
            margin-bottom: 5px;
        }

        .est-empty-desc {
            font-size: 12.5px;
            color: var(--t-muted);
            max-width: 440px;
            line-height: 1.5;
            margin: 0;
        }

        .est-loading-box {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 8px;
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            font-size: 13px;
            font-weight: 600;
            color: var(--t-base);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .cat-length-box {
            display: flex;
            align-items: center;
            gap: 8px;
            box-sizing: border-box;
        }

        .cat-length-box .select2-container .select2-selection--single {
            height: 38px !important;
            border-radius: 8px !important;
            border: 1px solid var(--border) !important;
            background-color: var(--bg-card) !important;
            display: flex !important;
            align-items: center !important;
            transition: all 0.2s ease !important;
            box-sizing: border-box !important;
        }

        .cat-length-box .select2-container .select2-selection--single .select2-selection__rendered {
            padding-left: 10px !important;
            padding-right: 20px !important;
            line-height: 36px !important;
            font-size: 13px !important;
            color: var(--t-base) !important;
            font-weight: 600 !important;
            text-align: center;
        }

        .cat-length-box .select2-container .select2-selection--single .select2-selection__arrow {
            height: 36px !important;
            right: 4px !important;
        }

        html[data-theme="dark"] .cat-select-icon {
            color: #94a3b8 !important;
            background: transparent !important;
        }

        html[data-theme="dark"] .cat-select-box:hover .cat-select-icon,
        html[data-theme="dark"] .cat-select-box:focus-within .cat-select-icon,
        html[data-theme="dark"] .cat-select-box.is-open .cat-select-icon,
        html[data-theme="dark"] .cat-select-box:has(.select2-container--open) .cat-select-icon {
            color: #60a5fa !important;
        }

        html[data-theme="dark"] .cat-select-box .select2-container .select2-selection--single,
        html[data-theme="dark"] .cat-length-box .select2-container .select2-selection--single {
            background-color: rgba(20, 27, 45, 0.75) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            color: var(--t-base) !important;
        }

        html[data-theme="dark"] .cat-select-box .select2-container:hover .select2-selection--single,
        html[data-theme="dark"] .cat-length-box .select2-container:hover .select2-selection--single,
        html[data-theme="dark"] .cat-select-box .select2-container--open .select2-selection--single,
        html[data-theme="dark"] .cat-length-box .select2-container--open .select2-selection--single,
        html[data-theme="dark"] .cat-select-box.is-open .select2-selection--single {
            border-color: rgba(96, 165, 250, 0.45) !important;
            background-color: rgba(26, 36, 61, 0.9) !important;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.16) !important;
        }

        html[data-theme="dark"] .select2-dropdown {
            background-color: #141b2d !important;
            border: 1px solid rgba(255, 255, 255, 0.12) !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.6) !important;
        }

        html[data-theme="dark"] .select2-container--default .select2-results__option {
            color: var(--t-base) !important;
            font-size: 13px !important;
        }

        html[data-theme="dark"] .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: rgba(96, 165, 250, 0.18) !important;
            color: #60a5fa !important;
        }

        html[data-theme="dark"] .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: rgba(255, 255, 255, 0.06) !important;
            color: #ffffff !important;
            font-weight: 600 !important;
        }

        .cat-btn-reset {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 38px;
            padding: 0 14px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--t-muted);
            font-size: 12.5px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s ease;
            box-sizing: border-box;
            white-space: nowrap;
        }

        html[data-theme="light"] .cat-btn-reset {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #475569;
        }

        html[data-theme="light"] .cat-btn-reset:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
            color: #0f172a;
        }

        html[data-theme="dark"] .cat-btn-reset {
            background: rgba(20, 27, 45, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.08);
            color: var(--t-muted);
        }

        html[data-theme="dark"] .cat-btn-reset:hover {
            background: rgba(30, 41, 69, 0.9);
            border-color: rgba(96, 165, 250, 0.35);
            color: #ffffff;
        }

        .cat-btn-reset i {
            transition: transform 0.25s ease;
        }

        .cat-btn-reset:hover i {
            transform: rotate(-90deg);
        }

        .cat-filter-group-right {
            display: flex;
            align-items: center;
            gap: 8px;
            box-sizing: border-box;
        }

        .cat-length-label {
            font-size: 12.5px;
            color: var(--t-muted);
            font-weight: 500;
            white-space: nowrap;
        }

        /* Modern Table & Scroll Wrapper */
        .cat-table-wrap {
            width: 100%;
            max-height: 560px;
            overflow: auto;
            -webkit-overflow-scrolling: touch;
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            background: var(--bg-card);
            position: relative;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            scrollbar-width: thin;
            scrollbar-color: var(--border) transparent;
        }

        .cat-table-wrap::-webkit-scrollbar {
            height: 7px;
            width: 7px;
        }

        .cat-table-wrap::-webkit-scrollbar-track {
            background: transparent;
        }

        .cat-table-wrap::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 999px;
        }

        .cat-table-wrap::-webkit-scrollbar-thumb:hover {
            background: var(--t-muted);
        }

        .cat-modern-table {
            width: 100% !important;
            min-width: 960px;
            border-collapse: separate;
            border-spacing: 0;
        }

        .cat-modern-table thead th {
            position: sticky !important;
            top: 0 !important;
            z-index: 10 !important;
            background: var(--bg-card) !important;
            border-bottom: 2px solid var(--border) !important;
            color: var(--t-light);
            font-family: JetBrains Mono, monospace;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            padding: 14px 16px;
            white-space: nowrap;
            user-select: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
        }

        html[data-theme="light"] .cat-modern-table thead th {
            background: #f1f5f9 !important;
            border-bottom-color: #cbd5e1 !important;
            color: #475569;
        }

        html[data-theme="dark"] .cat-modern-table thead th {
            background: #0b1329 !important;
            border-bottom-color: #1e293b !important;
            color: #94a3b8;
        }

        .cat-modern-table tbody td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--border-soft);
            color: var(--t-base);
            font-size: 13px;
            vertical-align: middle;
            white-space: nowrap;
            transition: background 0.15s ease;
        }

        .cat-modern-table tbody tr:last-child td {
            border-bottom: none;
        }

        .cat-modern-table tbody tr:hover td {
            background: var(--bg-hover);
        }

        .cat-num-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 24px;
            padding: 0 6px;
            border-radius: 6px;
            background: var(--bg-hover);
            color: var(--t-muted);
            font-family: JetBrains Mono, monospace;
            font-size: 11.5px;
            font-weight: 700;
            border: 1px solid var(--border-soft);
        }

        /* Specific Cell Elements */
        .est-code-box {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .est-code-quo {
            font-family: JetBrains Mono, monospace;
            font-size: 13px;
            font-weight: 700;
            color: #2563eb;
            letter-spacing: -0.01em;
        }

        html[data-theme="dark"] .est-code-quo {
            color: #60a5fa;
        }

        .est-code-draft {
            font-family: JetBrains Mono, monospace;
            font-size: 12px;
            font-weight: 600;
            color: var(--t-muted);
            font-style: italic;
        }

        .est-code-sub {
            font-family: JetBrains Mono, monospace;
            font-size: 11px;
            color: var(--t-muted);
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }

        .est-date-box {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .est-date-main {
            font-family: JetBrains Mono, monospace;
            font-size: 12.5px;
            font-weight: 600;
            color: var(--t-base);
        }

        .est-terms-badge {
            font-size: 10.5px;
            color: var(--t-muted);
            font-weight: 500;
        }

        /* Category Badges */
        .est-badge-type {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 7px;
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: 0.01em;
            white-space: nowrap;
        }

        .est-badge-a3plus {
            background: rgba(37, 99, 235, 0.1);
            color: #2563eb;
            border: 1px solid rgba(37, 99, 235, 0.25);
        }

        html[data-theme="dark"] .est-badge-a3plus {
            background: rgba(37, 99, 235, 0.2);
            color: #93c5fd;
            border-color: rgba(96, 165, 250, 0.35);
        }

        .est-badge-lf {
            background: rgba(14, 165, 233, 0.1);
            color: #0284c7;
            border: 1px solid rgba(14, 165, 233, 0.25);
        }

        html[data-theme="dark"] .est-badge-lf {
            background: rgba(14, 165, 233, 0.2);
            color: #7dd3fc;
            border-color: rgba(56, 189, 248, 0.35);
        }

        .est-badge-offset {
            background: rgba(147, 51, 234, 0.1);
            color: #9333ea;
            border: 1px solid rgba(147, 51, 234, 0.25);
        }

        html[data-theme="dark"] .est-badge-offset {
            background: rgba(147, 51, 234, 0.2);
            color: #c084fc;
            border-color: rgba(192, 132, 252, 0.35);
        }

        .est-badge-calendar {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.25);
        }

        html[data-theme="dark"] .est-badge-calendar {
            background: rgba(245, 158, 11, 0.2);
            color: #fcd34d;
            border-color: rgba(251, 191, 36, 0.35);
        }

        .est-badge-merch {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.25);
        }

        html[data-theme="dark"] .est-badge-merch {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
            border-color: rgba(52, 211, 153, 0.35);
        }

        .est-badge-default {
            background: var(--bg-hover);
            color: var(--t-muted);
            border: 1px solid var(--border-soft);
        }

        /* Customer Box */
        .est-customer-box {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .est-customer-name {
            font-size: 13px;
            font-weight: 700;
            color: var(--t-base);
        }

        .est-phone-link {
            display: inline-flex;
            align-items: center;
            font-size: 11.5px;
            color: var(--t-muted);
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .est-phone-link:hover {
            color: #10b981;
        }

        /* User Badge */
        .cat-user-badge {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cat-user-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .cat-user-name {
            font-size: 13px;
            font-weight: 600;
            color: var(--t-base);
        }

        .cat-user-role {
            font-size: 10.5px;
            color: var(--t-muted);
            font-weight: 500;
        }

        /* Grand Total */
        .est-total-box {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .est-total-amount {
            font-family: JetBrains Mono, monospace;
            font-size: 14px;
            font-weight: 700;
            color: #059669;
        }

        html[data-theme="dark"] .est-total-amount {
            color: #34d399;
        }

        /* Status Badges */
        .est-status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 600;
        }

        .est-status-sent {
            background: rgba(16, 185, 129, 0.12);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.28);
        }

        html[data-theme="dark"] .est-status-sent {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
            border-color: rgba(52, 211, 153, 0.35);
        }

        .est-status-draft {
            background: rgba(100, 116, 139, 0.12);
            color: #64748b;
            border: 1px solid rgba(100, 116, 139, 0.25);
        }

        html[data-theme="dark"] .est-status-draft {
            background: rgba(100, 116, 139, 0.22);
            color: #94a3b8;
            border-color: rgba(148, 163, 184, 0.3);
        }

        .est-status-cancelled {
            background: rgba(239, 68, 68, 0.12);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }

        html[data-theme="dark"] .est-status-cancelled {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border-color: rgba(248, 113, 113, 0.35);
        }

        /* Action Buttons */
        .cat-action-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .cat-btn-action {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: 1px solid var(--border-soft);
            background: var(--bg-card);
            color: var(--t-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.16s ease;
        }

        .cat-btn-action:hover {
            background: rgba(37, 99, 235, 0.12) !important;
            color: #2563eb !important;
            border-color: rgba(37, 99, 235, 0.35) !important;
            transform: translateY(-1px);
        }

        /* DataTables Footer Spacing */
        .dt-container .dt-layout-row:last-child {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            gap: 12px !important;
            box-sizing: border-box !important;
            padding: 8px 18px 14px 18px !important;
        }

        .dt-container .dt-info,
        .dataTables_wrapper .dataTables_info,
        .dt-container .dt-layout-cell.dt-start {
            padding-left: 20px !important;
            margin-top: 14px !important;
            margin-bottom: 12px !important;
            color: var(--t-muted) !important;
            font-size: 13px !important;
            font-weight: 500 !important;
        }

        .dt-container .dt-paging,
        .dataTables_wrapper .dataTables_paginate,
        .dt-container .dt-layout-cell.dt-end {
            padding-right: 20px !important;
            margin-top: 14px !important;
            margin-bottom: 12px !important;
        }

        /* Detail Modal Items Table */
        .est-detail-item-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
            font-size: 12.5px;
        }

        .est-detail-item-table th {
            background: var(--bg-hover);
            padding: 8px 12px;
            border-bottom: 1px solid var(--border);
            color: var(--t-muted);
            font-weight: 600;
            text-align: left;
        }

        .est-detail-item-table td {
            padding: 9px 12px;
            border-bottom: 1px solid var(--border-soft);
            color: var(--t-base);
        }

        .est-meta-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
            padding: 14px;
            background: var(--bg-hover);
            border-radius: 10px;
            border: 1px solid var(--border-soft);
            margin-bottom: 16px;
        }

        .est-meta-item {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .est-meta-label {
            font-size: 11px;
            color: var(--t-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 600;
        }

        .est-meta-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--t-base);
        }

        .est-financial-summary {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 14px;
            background: var(--bg-hover);
            border-radius: 10px;
            border: 1px solid var(--border-soft);
            margin-top: 16px;
            margin-left: auto;
            max-width: 320px;
        }

        .est-fin-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12.5px;
            color: var(--t-muted);
        }

        .est-fin-row.est-fin-total {
            font-size: 14px;
            font-weight: 700;
            color: #059669;
            padding-top: 6px;
            border-top: 1px dashed var(--border);
        }

        html[data-theme="dark"] .est-fin-row.est-fin-total {
            color: #34d399;
        }

        /* Clickable Quotation Rows */
        #estimationTable tbody tr:not(:has(td.dt-empty)) {
            cursor: pointer;
            transition: background-color 0.16s ease;
        }

        html[data-theme="light"] #estimationTable tbody tr:not(:has(td.dt-empty)):hover td {
            background-color: rgba(37, 99, 235, 0.055) !important;
        }

        html[data-theme="dark"] #estimationTable tbody tr:not(:has(td.dt-empty)):hover td {
            background-color: rgba(59, 130, 246, 0.09) !important;
        }

        /* Quotation Detail Modal (System Theme Light & Dark, Responsive Wide Layout) */
        #modalDetailQuotation {
            z-index: 1200;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            padding: max(24px, env(safe-area-inset-top, 24px)) 20px max(24px, env(safe-area-inset-bottom, 24px)) !important;
            box-sizing: border-box;
        }

        #modalDetailQuotation .modal-dialog--wide {
            width: min(1060px, calc(100vw - 40px));
            max-width: 1060px;
            max-height: calc(100vh - 48px);
            max-height: calc(100dvh - 48px);
            margin: auto;
            border-radius: 16px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            box-shadow: 0 24px 60px -12px rgba(15, 23, 42, 0.28);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        html[data-theme="dark"] #modalDetailQuotation .modal-dialog--wide {
            box-shadow: 0 28px 70px -12px rgba(0, 0, 0, 0.65);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .qdet-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 24px;
            border-bottom: 1px solid var(--border-soft);
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.07), rgba(59, 130, 246, 0.015));
            flex-shrink: 0;
            position: relative;
        }

        html[data-theme="dark"] .qdet-header {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.14), rgba(15, 23, 42, 0.2));
        }

        .qdet-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
            flex: 1 1 auto;
        }

        .qdet-doc-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.16), rgba(59, 130, 246, 0.06));
            border: 1px solid rgba(59, 130, 246, 0.25);
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            flex-shrink: 0;
        }

        html[data-theme="dark"] .qdet-doc-icon {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.24), rgba(37, 99, 235, 0.1));
            border-color: rgba(96, 165, 250, 0.3);
            color: #60a5fa;
        }

        .qdet-code-row {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: nowrap;
            min-width: 0;
        }

        .qdet-code-title {
            margin: 0;
            font-family: JetBrains Mono, monospace;
            font-size: 16px;
            font-weight: 700;
            color: var(--t-base);
            letter-spacing: -0.01em;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .qdet-subtitle {
            margin: 3px 0 0;
            font-size: 12.5px;
            color: var(--t-muted);
        }

        .qdet-header-right {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .qdet-body {
            padding: 20px 24px;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            flex: 1 1 auto;
            min-height: 0;
            display: block;
        }

        .qdet-body > * + * {
            margin-top: 16px;
        }

        .qdet-body::-webkit-scrollbar {
            width: 5px;
        }

        .qdet-body::-webkit-scrollbar-track {
            background: transparent;
        }

        .qdet-body::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 5px;
        }

        .qdet-body::-webkit-scrollbar-button {
            display: none;
            width: 0;
            height: 0;
        }

        @supports not selector(::-webkit-scrollbar) {
            .qdet-body {
                scrollbar-width: thin;
                scrollbar-color: var(--border) transparent;
            }
        }

        .qdet-meta-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px 18px;
            padding: 16px 18px;
            border-radius: 12px;
            background: var(--bg-hover);
            border: 1px solid var(--border-soft);
        }

        html[data-theme="dark"] .qdet-meta-grid {
            background: rgba(11, 17, 32, 0.45);
            border-color: rgba(255, 255, 255, 0.07);
        }

        .qdet-meta-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            min-width: 0;
        }

        .qdet-meta-icon {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: var(--bg-card);
            border: 1px solid var(--border-soft);
            color: var(--t-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .qdet-meta-content {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .qdet-meta-label {
            font-size: 10.5px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--t-muted);
        }

        .qdet-meta-value {
            font-size: 13px;
            font-weight: 600;
            color: var(--t-base);
            word-break: break-word;
            line-height: 1.4;
        }

        .qdet-notice {
            display: none;
            align-items: center;
            gap: 10px;
            padding: 11px 14px;
            border-radius: 10px;
            font-size: 12.5px;
            background: rgba(245, 158, 11, 0.12);
            border: 1px solid rgba(245, 158, 11, 0.28);
            color: #d97706;
        }

        html[data-theme="dark"] .qdet-notice {
            color: #fbbf24;
        }

        .qdet-items-card {
            border: 1px solid var(--border-soft);
            border-radius: 12px;
            overflow: hidden;
            background: var(--bg-card);
        }

        html[data-theme="dark"] .qdet-items-card {
            border-color: rgba(255, 255, 255, 0.08);
            background: rgba(15, 23, 42, 0.35);
        }

        .qdet-items-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            border-bottom: 1px solid var(--border-soft);
            background: var(--bg-hover);
        }

        html[data-theme="dark"] .qdet-items-head {
            background: rgba(11, 17, 32, 0.55);
        }

        .qdet-tab-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 4px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: #2563eb;
            border-bottom: 2px solid #2563eb;
        }

        html[data-theme="dark"] .qdet-tab-pill {
            color: #60a5fa;
            border-bottom-color: #60a5fa;
        }

        .qdet-tab-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            background: rgba(37, 99, 235, 0.14);
            color: #2563eb;
        }

        html[data-theme="dark"] .qdet-tab-count {
            background: rgba(96, 165, 250, 0.2);
            color: #93c5fd;
        }

        .qdet-table-scroll {
            width: 100%;
            overflow-x: auto;
        }

        .qdet-items-table {
            width: 100%;
            min-width: 720px;
            border-collapse: collapse;
            font-size: 12.5px;
        }

        .qdet-items-table th {
            padding: 10px 14px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--t-muted);
            border-bottom: 1px solid var(--border-soft);
            background: var(--bg-hover);
            white-space: nowrap;
        }

        html[data-theme="dark"] .qdet-items-table th {
            background: rgba(15, 23, 42, 0.45);
        }

        .qdet-items-table td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border-soft);
            color: var(--t-base);
            vertical-align: middle;
        }

        .qdet-items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .qdet-type-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 7px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            background: var(--bg-hover);
            border: 1px solid var(--border);
            color: var(--t-muted);
            flex-shrink: 0;
        }

        .qdet-item-cell {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qdet-item-thumb {
            width: 34px;
            height: 34px;
            border-radius: 7px;
            object-fit: cover;
            border: 1px solid var(--border);
            flex-shrink: 0;
        }

        .qdet-desc-cell {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
            color: var(--t-muted);
            max-width: 320px;
            line-height: 1.45;
            word-break: break-word;
        }

        .qdet-unit-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            background: var(--bg-hover);
            border: 1px solid var(--border-soft);
            color: var(--t-muted);
        }

        .qdet-amount-val {
            font-weight: 700;
            color: #2563eb;
            white-space: nowrap;
        }

        html[data-theme="dark"] .qdet-amount-val {
            color: #60a5fa;
        }

        .qdet-bottom-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(0, 0.85fr);
            gap: 16px;
            align-items: stretch;
        }

        .qdet-subcard {
            padding: 15px 18px;
            border-radius: 12px;
            border: 1px solid var(--border-soft);
            background: var(--bg-hover);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        html[data-theme="dark"] .qdet-subcard {
            background: rgba(11, 17, 32, 0.45);
            border-color: rgba(255, 255, 255, 0.07);
        }

        .qdet-subcard-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--t-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .qdet-notes-text {
            margin: 0;
            font-size: 13px;
            line-height: 1.55;
            color: var(--t-base);
            white-space: pre-line;
            word-break: break-word;
        }

        .qdet-notes-text.is-empty {
            color: var(--t-muted);
            font-style: italic;
        }

        .qdet-summary-rows {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .qdet-sum-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-size: 13px;
            color: var(--t-muted);
        }

        .qdet-sum-row strong {
            color: var(--t-base);
            font-weight: 600;
        }

        .qdet-sum-row.is-tax {
            color: #059669;
        }

        .qdet-sum-row.is-tax strong {
            color: #059669;
        }

        html[data-theme="dark"] .qdet-sum-row.is-tax,
        html[data-theme="dark"] .qdet-sum-row.is-tax strong {
            color: #34d399;
        }

        .qdet-sum-row.is-discount {
            color: #d97706;
        }

        .qdet-sum-row.is-discount strong {
            color: #d97706;
        }

        html[data-theme="dark"] .qdet-sum-row.is-discount,
        html[data-theme="dark"] .qdet-sum-row.is-discount strong {
            color: #fbbf24;
        }

        .qdet-sum-total {
            margin-top: 4px;
            padding-top: 10px;
            border-top: 1px dashed var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .qdet-sum-total-label {
            font-size: 12.5px;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: var(--t-base);
        }

        .qdet-sum-total-value {
            font-size: 17px;
            font-weight: 800;
            color: #2563eb;
        }

        html[data-theme="dark"] .qdet-sum-total-value {
            color: #60a5fa;
        }

        .qdet-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 24px;
            border-top: 1px solid var(--border-soft);
            background: var(--bg-hover);
            flex-shrink: 0;
            flex-wrap: wrap;
        }

        html[data-theme="dark"] .qdet-footer {
            background: rgba(11, 17, 32, 0.55);
        }

        .qdet-footer-meta {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            font-size: 12.5px;
            color: var(--t-muted);
        }

        .qdet-footer-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .qdet-footer-code {
            font-family: JetBrains Mono, monospace;
            font-size: 12px;
            color: var(--t-muted);
        }

        .qdet-loading-state {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 26px 16px;
            color: var(--t-muted);
            font-size: 13px;
        }

        .qdet-copy-inline {
            background: transparent;
            border: none;
            padding: 3px 5px;
            border-radius: 5px;
            color: var(--t-muted);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
            flex-shrink: 0;
        }

        .qdet-copy-inline:hover {
            background: var(--bg-hover);
            color: var(--t-base);
        }

        @media (max-width: 900px) {
            .qdet-meta-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .qdet-bottom-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            #modalDetailQuotation {
                padding: max(22px, env(safe-area-inset-top, 22px)) 12px max(22px, env(safe-area-inset-bottom, 22px)) !important;
            }

            #modalDetailQuotation .modal-dialog--wide {
                width: calc(100vw - 24px);
                max-width: none;
                max-height: calc(100vh - 52px);
                max-height: calc(100dvh - 52px);
                border-radius: 14px;
            }

            .qdet-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                padding: 14px 48px 14px 14px;
            }

            .qdet-header-left {
                width: 100%;
                gap: 11px;
            }

            .qdet-doc-icon {
                width: 38px;
                height: 38px;
                font-size: 17px;
                border-radius: 10px;
            }

            .qdet-code-title {
                font-size: 14.5px;
            }

            .qdet-subtitle {
                font-size: 12px;
            }

            .qdet-header-right {
                padding-left: 49px;
            }

            .qdet-header-right .modal-close {
                position: absolute;
                top: 12px;
                right: 10px;
            }

            .qdet-body {
                padding: 14px;
            }

            .qdet-body > * + * {
                margin-top: 12px;
            }

            .qdet-meta-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
                padding: 12px;
            }

            .qdet-items-table {
                min-width: 0;
            }

            .qdet-items-table thead {
                display: none;
            }

            .qdet-items-table tr.qdet-item-row {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px 12px;
                padding: 13px 14px;
                border-bottom: 1px solid var(--border-soft);
            }

            .qdet-items-table tr.qdet-item-row:last-child {
                border-bottom: none;
            }

            .qdet-items-table tr.qdet-item-row td {
                display: flex;
                flex-direction: column;
                gap: 3px;
                padding: 0 !important;
                border: none !important;
                text-align: left !important;
            }

            .qdet-items-table tr.qdet-item-row td.qdet-td-full {
                grid-column: 1 / -1;
            }

            .qdet-items-table tr.qdet-item-row td[data-label]::before {
                content: attr(data-label);
                font-size: 10px;
                font-weight: 700;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                color: var(--t-muted);
            }

            .qdet-desc-cell {
                max-width: none;
                width: 100%;
            }

            .qdet-subcard {
                padding: 13px 14px;
            }

            .qdet-footer {
                padding: 12px 14px;
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }

            .qdet-footer-right {
                justify-content: space-between;
            }
        }

        @media (max-width: 420px) {
            .qdet-meta-grid {
                grid-template-columns: 1fr;
            }
        }

        .cat-filter-group-right {
            display: flex;
            align-items: center;
            gap: 10px;
            box-sizing: border-box;
        }

        @media (max-width: 1200px) {
            .cat-search-box {
                max-width: 250px;
            }
        }

        @media (max-width: 992px) {
            .cat-filter-card {
                padding: 14px;
            }

            .cat-search-box {
                min-width: 180px;
                max-width: none;
                flex: 1 1 200px;
            }

            .cat-select-box {
                min-width: 180px;
                flex: 1 1 180px;
            }

            .est-daterange-group {
                flex: 1 1 auto;
                justify-content: flex-start;
            }
        }

        @media (max-width: 768px) {
            .cat-card-container {
                padding: 14px !important;
            }

            .cat-card-header {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 14px !important;
            }

            .cat-card-actions {
                width: 100% !important;
            }

            .cat-btn-head-primary {
                width: 100% !important;
                justify-content: center !important;
            }

            .cat-filter-card {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 10px !important;
                padding: 12px !important;
            }

            .cat-filter-group-left {
                flex-direction: column !important;
                align-items: stretch !important;
                width: 100% !important;
                gap: 10px !important;
                flex: none !important;
            }

            .cat-search-box {
                flex: none !important;
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
                height: 38px !important;
            }

            .cat-select-box {
                flex: none !important;
                width: 100% !important;
                min-width: 0 !important;
                height: 38px !important;
            }

            .est-daterange-group {
                flex: none !important;
                width: 100% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                gap: 8px !important;
                height: 38px !important;
            }

            .est-date-box {
                flex: 1 1 0% !important;
                width: 0 !important;
                min-width: 0 !important;
                height: 38px !important;
            }

            .est-date-input {
                font-size: 12px !important;
                padding: 0 4px 0 28px !important;
            }

            .est-date-icon {
                left: 8px !important;
                font-size: 13px !important;
            }

            .est-date-separator {
                flex-shrink: 0 !important;
                font-size: 11.5px !important;
                padding: 0 2px !important;
            }

            .cat-filter-group-right {
                width: 100% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                padding-top: 10px !important;
                border-top: 1px dashed var(--border-soft) !important;
                margin-top: 2px !important;
            }

            .cat-btn-reset {
                flex: 0 0 auto !important;
                height: 38px !important;
                padding: 0 14px !important;
            }

            .cat-length-box {
                flex: 0 0 auto !important;
            }
        }

        @media (max-width: 480px) {
            .cat-card-container {
                padding: 10px !important;
                border-radius: 12px !important;
            }

            .cat-title-row {
                gap: 6px;
            }

            .cat-card-title {
                font-size: 15.5px !important;
            }

            .cat-card-icon {
                width: 38px !important;
                height: 38px !important;
                font-size: 17px !important;
            }

            .est-daterange-group {
                gap: 5px !important;
            }

            .est-date-input {
                font-size: 11px !important;
                padding: 0 2px 0 26px !important;
                letter-spacing: -0.02em;
            }

            .est-date-icon {
                left: 7px !important;
                font-size: 12px !important;
            }

            .est-date-separator {
                font-size: 11px !important;
                padding: 0 1px !important;
            }

            .cat-modern-table {
                min-width: 860px;
            }
        }
    </style>

    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero cat-hero-compact">
                    <div class="hero-text">
                        <span class="eyebrow">ESTIMASI · RIWAYAT PENAWARAN</span>
                        <p class="hero-sub">Daftar seluruh riwayat estimasi harga dan penawaran resmi yang pernah dibuat dan
                            dikirim ke Cetakia.</p>
                    </div>
                </section>

                <div class="grid">
                    <section class="col-12 card cat-card-container">
                        <!-- Card Header -->
                        <div class="cat-card-header">
                            <div class="cat-card-header-main">
                                <div class="cat-card-title-wrap">
                                    <div class="cat-card-icon">
                                        <i class="bi bi-file-earmark-text"></i>
                                    </div>
                                    <div>
                                        <div class="cat-title-row">
                                            <h3 class="cat-card-title">Riwayat Penawaran Estimasi</h3>
                                            <span class="cat-badge-total" id="estTotalBadge">
                                                <span class="cat-pulse-dot"></span>
                                                <span id="estTotalCount">Memuat data...</span>
                                            </span>
                                        </div>
                                        <p class="cat-card-desc">Riwayat kalkulasi harga dan transaksi penawaran resmi yang
                                            terintegrasi dengan Cetakia.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Integrated Action Buttons -->
                            <div class="cat-card-actions">
                                <a href="{{ route('admin_home') }}" class="cat-btn-head-primary">
                                    {{-- <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/>
                                    </svg> --}}
                                    <i class="bi bi-plus"></i>
                                    <span>Kalkulasi Cetak Baru</span>
                                </a>
                            </div>
                        </div>

                        <!-- Filter & Action Toolbar -->
                        <div class="cat-filter-card">
                            <div class="cat-filter-group-left">
                                <div class="cat-search-box">
                                    <i class="bi bi-search cat-search-icon"></i>
                                    <input type="text" id="estCustomSearch" class="cat-filter-input"
                                        placeholder="Cari kode penawaran, pelanggan, sales..."
                                        aria-describedby="estSearchHint">
                                    <button type="button" id="estClearSearch" class="cat-clear-btn"
                                        title="Hapus pencarian" style="display: none;">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                    <span id="estSearchHint" class="cat-search-hint" role="status" aria-live="polite"
                                        hidden></span>
                                </div>

                                <div class="cat-select-box">
                                    <i class="bi bi-funnel-fill cat-select-icon"></i>
                                    <select id="filterStatus" class="select2" style="width: 100%;">
                                        <option value="all">Semua Status</option>
                                        <option value="draft">Draft</option>
                                        <option value="sent">Sent (Terkirim)</option>
                                        <option value="accepted">Accepted (SO / Invoice)</option>
                                        <option value="rejected">Rejected (Ditolak)</option>
                                        <option value="expired">Expired (Kedaluwarsa)</option>
                                    </select>
                                </div>

                                <!-- Clean Date Range Group (Default 1 Minggu Terakhir) -->
                                <div class="est-daterange-group" title="Filter rentang tanggal penawaran (default 1 minggu terakhir)">
                                    <div class="est-date-box" id="boxStartDate">
                                        <i class="bi bi-calendar3 est-date-icon"></i>
                                        <input type="date" id="filterStartDate" class="est-date-input"
                                            value="{{ $defaultStartDate ?? '' }}"
                                            title="Pilih Tanggal Mulai" aria-label="Tanggal Mulai">
                                    </div>
                                    <span class="est-date-separator">s/d</span>
                                    <div class="est-date-box" id="boxEndDate">
                                        <i class="bi bi-calendar3 est-date-icon"></i>
                                        <input type="date" id="filterEndDate" class="est-date-input"
                                            value="{{ $defaultEndDate ?? '' }}"
                                            title="Pilih Tanggal Akhir" aria-label="Tanggal Akhir">
                                    </div>
                                </div>
                            </div>

                            <div class="cat-filter-group-right">
                                <button type="button" id="btnResetFilters" class="cat-btn-reset"
                                    title="Reset semua filter dan muat ulang data terbaru">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                    <span>Reset</span>
                                </button>
                                <div class="cat-length-box">
                                    <span class="cat-length-label">Baris:</span>
                                    <select id="estPageLength" class="select2" style="width: 76px;">
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="estErrorBanner" hidden
                            style="margin-bottom: 14px; padding: 10px 14px; border-radius: 10px; background: rgba(239, 68, 68, 0.12); border: 1px solid rgba(239, 68, 68, 0.28); color: #f87171; font-size: 13px; display: none; align-items: center; gap: 8px;"
                            role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <span id="estErrorBannerText"></span>
                        </div>

                        <!-- Table Responsive Scroll Container -->
                        <div class="cat-table-wrap">
                            <table id="estimationTable" class="cat-modern-table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="cat-th-no text-center" style="width: 50px;">No</th>
                                        <th style="min-width: 180px;">QUOTATION CODE</th>
                                        <th class="text-center" style="min-width: 120px;">STATUS</th>
                                        <th style="min-width: 200px;">CUSTOMER</th>
                                        <th style="min-width: 140px;">QUOTATION DATE</th>
                                        <th style="min-width: 140px;">VALID UNTIL</th>
                                        <th class="text-end" style="min-width: 160px;">GRAND TOTAL</th>
                                        <th style="min-width: 160px;">CREATED BY</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </main>
            <div data-shell-footer></div>
        </div>
    </div>

    <!-- Modal Detail Quotation (Read-Only) -->
    <div class="modal-overlay" id="modalDetailQuotation" role="dialog" aria-modal="true" aria-labelledby="qdetCodeTitle">
        <div class="modal-dialog modal-dialog--wide">
            <!-- Header -->
            <div class="qdet-header">
                <div class="qdet-header-left">
                    <div class="qdet-doc-icon">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div style="min-width: 0;">
                        <div class="qdet-code-row">
                            <h3 class="qdet-code-title" id="qdetCodeTitle">#-</h3>
                            <button type="button" id="qdetCopyCodeBtn" class="qdet-copy-inline" data-code="" title="Salin Kode Penawaran">
                                <i class="bi bi-copy" style="font-size: 13px;"></i>
                            </button>
                        </div>
                        <p class="qdet-subtitle" id="qdetHeaderDate">Quotation Date: -</p>
                    </div>
                </div>
                <div class="qdet-header-right">
                    <span id="qdetHeaderStatus"></span>
                    <button type="button" class="modal-close" id="btnCloseDetailModal" aria-label="Tutup modal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Body -->
            <div class="qdet-body">
                <div class="qdet-meta-grid">
                    <div class="qdet-meta-item">
                        <span class="qdet-meta-icon"><i class="bi bi-building"></i></span>
                        <div class="qdet-meta-content">
                            <span class="qdet-meta-label">Customer</span>
                            <span class="qdet-meta-value" id="qdetCustomerName">-</span>
                        </div>
                    </div>
                    <div class="qdet-meta-item">
                        <span class="qdet-meta-icon"><i class="bi bi-geo-alt"></i></span>
                        <div class="qdet-meta-content">
                            <span class="qdet-meta-label">Bill To</span>
                            <span class="qdet-meta-value" id="qdetBillTo">-</span>
                        </div>
                    </div>
                    <div class="qdet-meta-item">
                        <span class="qdet-meta-icon"><i class="bi bi-truck"></i></span>
                        <div class="qdet-meta-content">
                            <span class="qdet-meta-label">Ship To</span>
                            <span class="qdet-meta-value" id="qdetShipTo">-</span>
                        </div>
                    </div>
                    <div class="qdet-meta-item">
                        <span class="qdet-meta-icon"><i class="bi bi-person"></i></span>
                        <div class="qdet-meta-content">
                            <span class="qdet-meta-label">Sales Rep</span>
                            <span class="qdet-meta-value" id="qdetSalesRep">-</span>
                        </div>
                    </div>
                    <div class="qdet-meta-item">
                        <span class="qdet-meta-icon"><i class="bi bi-calendar-event"></i></span>
                        <div class="qdet-meta-content">
                            <span class="qdet-meta-label">Expired Date</span>
                            <span class="qdet-meta-value" id="qdetExpiredDate">-</span>
                        </div>
                    </div>
                    <div class="qdet-meta-item">
                        <span class="qdet-meta-icon"><i class="bi bi-clock-history"></i></span>
                        <div class="qdet-meta-content">
                            <span class="qdet-meta-label">Terms</span>
                            <span class="qdet-meta-value" id="qdetTerms">-</span>
                        </div>
                    </div>
                </div>

                <div class="qdet-notice" id="qdetNotice" role="status">
                    <i class="bi bi-info-circle-fill"></i>
                    <span id="qdetNoticeText"></span>
                </div>

                <div class="qdet-items-card">
                    <div class="qdet-items-head">
                        <div class="qdet-tab-pill">
                            <i class="bi bi-file-earmark-text"></i>
                            <span>ITEMS</span>
                            <span class="qdet-tab-count" id="qdetItemsCountBadge">0</span>
                        </div>
                    </div>
                    <div class="qdet-table-scroll">
                        <table class="qdet-items-table">
                            <thead>
                                <tr>
                                    <th style="min-width: 220px;">ITEM</th>
                                    <th style="min-width: 240px;">DESCRIPTION</th>
                                    <th class="text-center" style="width: 80px;">QTY</th>
                                    <th class="text-end" style="min-width: 130px;">PRICE</th>
                                    <th class="text-center" style="width: 80px;">UNIT</th>
                                    <th class="text-end" style="min-width: 140px;">AMOUNT</th>
                                </tr>
                            </thead>
                            <tbody id="qdetItemsBody">
                                <tr>
                                    <td colspan="6">
                                        <div class="qdet-loading-state">
                                            <i class="bi bi-arrow-repeat spin-icon"></i>
                                            <span>Memuat rincian item penawaran...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="qdet-bottom-grid">
                    <div class="qdet-subcard">
                        <div class="qdet-subcard-title">
                            <i class="bi bi-chat-left-text"></i>
                            <span>Notes &amp; Remarks</span>
                        </div>
                        <p class="qdet-notes-text is-empty" id="qdetNotesText">Tidak ada catatan tambahan.</p>
                    </div>
                    <div class="qdet-subcard">
                        <div class="qdet-subcard-title">
                            <i class="bi bi-receipt"></i>
                            <span>Payment Summary</span>
                        </div>
                        <div class="qdet-summary-rows" id="qdetPaymentSummary">
                            <div class="qdet-sum-row">
                                <span>Sub Total:</span>
                                <strong>Rp 0,00</strong>
                            </div>
                        </div>
                        <div class="qdet-sum-total">
                            <span class="qdet-sum-total-label">GRAND TOTAL:</span>
                            <span class="qdet-sum-total-value" id="qdetSummaryGrandTotal">Rp 0,00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="qdet-footer">
                <div class="qdet-footer-meta">
                    <span>Total Items: <strong id="qdetFooterItemsCount" style="color: var(--t-base);">0</strong></span>
                    <span>•</span>
                    <span>Grand Total: <strong id="qdetFooterGrandTotal" class="qdet-amount-val">Rp 0,00</strong></span>
                </div>
                <div class="qdet-footer-right">
                    <span class="qdet-footer-code" id="qdetFooterCode">#-</span>
                    <button type="button" class="btn btn--ghost" id="btnFooterCloseDetailModal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.__initialEstimationsPayload = @json($initialQuotationsPayload ?? null);
        const DEFAULT_START_DATE = @json($defaultStartDate ?? '');
        const DEFAULT_END_DATE = @json($defaultEndDate ?? '');
        let estimationTable = null;
        let activeQuotationXhr = null;
        let forceRefreshNext = false;
        let lastKnownOverallTotal = (window.__initialEstimationsPayload && Number(window.__initialEstimationsPayload
            .recordsTotal)) || 0;

        function hasCustomEstimationFilters() {
            const searchEl = document.getElementById('estCustomSearch');
            const statusEl = document.getElementById('filterStatus');
            const startEl = document.getElementById('filterStartDate');
            const endEl = document.getElementById('filterEndDate');
            const searchQuery = searchEl ? searchEl.value.trim() : '';
            const startVal = startEl ? startEl.value : '';
            const endVal = endEl ? endEl.value : '';

            return (
                searchQuery.length >= 3 ||
                (statusEl && statusEl.value !== 'all') ||
                startVal !== DEFAULT_START_DATE ||
                endVal !== DEFAULT_END_DATE
            );
        }

        function isUnfilteredAllTimeView() {
            const searchEl = document.getElementById('estCustomSearch');
            const statusEl = document.getElementById('filterStatus');
            const startEl = document.getElementById('filterStartDate');
            const endEl = document.getElementById('filterEndDate');
            const searchQuery = searchEl ? searchEl.value.trim() : '';

            return (
                searchQuery.length < 3 &&
                (!statusEl || statusEl.value === 'all') &&
                (!startEl || startEl.value === '') &&
                (!endEl || endEl.value === '')
            );
        }

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatRupiah(value) {
            const num = Number(value || 0);
            return 'Rp ' + num.toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function formatDateId(dateStr, withTime = false) {
            if (!dateStr) return '-';
            const normalized = String(dateStr).replace(' ', 'T');
            const d = new Date(normalized);
            if (isNaN(d.getTime())) return escapeHtml(dateStr);
            const datePart = d.toLocaleDateString('id-ID', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
            if (!withTime) return datePart;
            const timePart = d.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: false
            });
            return `${datePart}, ${timePart}`;
        }

        function buildStatusBadgeHtml(status, convertSo, convertInvoice) {
            let statusText = String(status || 'DRAFT').toUpperCase();
            let badgeStyle = 'background: rgba(59, 130, 246, 0.12); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.25);';

            if (convertInvoice) {
                statusText = 'INVOICE';
                badgeStyle = 'background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3);';
            } else if (convertSo) {
                statusText = 'SO';
                badgeStyle = 'background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);';
            } else if (statusText === 'ACCEPTED') {
                badgeStyle = 'background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3);';
            } else if (statusText === 'SENT') {
                badgeStyle = 'background: rgba(99, 102, 241, 0.15); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.3);';
            } else if (statusText === 'EXPIRED') {
                badgeStyle = 'background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3);';
            } else if (statusText === 'REJECTED' || statusText === 'CANCELLED') {
                badgeStyle = 'background: rgba(239, 68, 68, 0.12); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25);';
            }

            return `<span style="display: inline-flex; align-items: center; justify-content: center; padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; letter-spacing: 0.03em; ${badgeStyle}">${escapeHtml(statusText)}</span>`;
        }

        function copyTextWithFeedback(text, btnElement) {
            if (!text || text === '-') return;
            if (!navigator.clipboard || typeof navigator.clipboard.writeText !== 'function') return;
            navigator.clipboard.writeText(text).then(() => {
                const icon = btnElement ? btnElement.querySelector('i') : null;
                if (!icon) return;
                const prevClass = icon.className;
                icon.className = 'bi bi-check2 text-success';
                setTimeout(() => {
                    icon.className = prevClass;
                }, 1500);
            }).catch(() => {});
        }

        function setResetButtonBusy(isBusy) {
            const resetBtn = document.getElementById('btnResetFilters');
            if (!resetBtn) return;
            resetBtn.disabled = isBusy;
            const icon = resetBtn.querySelector('i');
            if (icon) {
                icon.classList.toggle('spin-icon', isBusy);
            }
        }

        function showTableError(message) {
            const banner = document.getElementById('estErrorBanner');
            const textEl = document.getElementById('estErrorBannerText');
            if (!banner || !textEl) return;
            if (message) {
                textEl.textContent = message;
                banner.hidden = false;
                banner.style.display = 'flex';
            } else {
                textEl.textContent = '';
                banner.hidden = true;
                banner.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Nonaktifkan popup alert bawaan DataTables agar error ditangani lewat banner UI
            if ($.fn && $.fn.dataTable && $.fn.dataTable.ext) {
                $.fn.dataTable.ext.errMode = 'none';
            }
            showTableError('');

            // Inisialisasi DataTable Server-Side
            estimationTable = new DataTable('#estimationTable', {
                processing: true,
                serverSide: true,
                ordering: true,
                order: [
                    [4, 'desc']
                ],
                stripeClasses: [],

                ajax: function(d, callback) {
                    const filterStatus = document.getElementById('filterStatus');
                    const filterStartDate = document.getElementById('filterStartDate');
                    const filterEndDate = document.getElementById('filterEndDate');
                    const statusVal = filterStatus ? filterStatus.value : 'all';
                    const startVal = filterStartDate ? filterStartDate.value : '';
                    const endVal = filterEndDate ? filterEndDate.value : '';
                    const searchVal = (d.search && d.search.value) ? d.search.value.trim() : '';
                    const orderCol = (d.order && d.order[0]) ? d.order[0].column : 4;
                    const orderDir = (d.order && d.order[0]) ? d.order[0].dir : 'desc';

                    // Gunakan payload preload halaman 1 bila masih pada filter default (1 minggu terakhir) agar 0ms tanpa waterfall AJAX
                    if (
                        window.__initialEstimationsPayload &&
                        !forceRefreshNext &&
                        d.start === 0 &&
                        d.length === 10 &&
                        searchVal === '' &&
                        statusVal === 'all' &&
                        startVal === DEFAULT_START_DATE &&
                        endVal === DEFAULT_END_DATE &&
                        orderCol === 4 &&
                        orderDir === 'desc'
                    ) {
                        const initialPayload = window.__initialEstimationsPayload;
                        window.__initialEstimationsPayload = null;
                        initialPayload.draw = d.draw;
                        showTableError('');
                        // Jalankan async 0ms agar inisialisasi DataTable, Select2, & event listener selesai dulu
                        setTimeout(function() {
                            callback(initialPayload);
                        }, 0);
                        return;
                    }

                    d.status = statusVal;
                    d.start_date = startVal;
                    d.end_date = endVal;
                    const isResetRefresh = forceRefreshNext;
                    if (forceRefreshNext) {
                        d.refresh = 1;
                        forceRefreshNext = false;
                    }

                    // Batalkan request sebelumnya jika masih berjalan agar tidak menumpuk di server
                    if (activeQuotationXhr && activeQuotationXhr.readyState !== 4) {
                        activeQuotationXhr.abort();
                    }

                    if (isResetRefresh) {
                        setResetButtonBusy(true);
                    }
                    activeQuotationXhr = $.ajax({
                        url: "{{ route('admin_estimations_data') }}",
                        type: "GET",
                        data: d,
                        timeout: 25000,
                        success: function(json) {
                            setResetButtonBusy(false);
                            if (json && typeof json.recordsTotal === 'number' && json
                                .recordsTotal > 0) {
                                lastKnownOverallTotal = json.recordsTotal;
                            }
                            if (json && json.error) {
                                showTableError(json.error);
                            } else {
                                showTableError('');
                            }
                            callback(json);
                        },
                        error: function(xhr, textStatus) {
                            setResetButtonBusy(false);
                            if (textStatus === 'abort') {
                                return;
                            }
                            showTableError(
                                'Terjadi kendala koneksi saat memuat data dari Cetakia. Silakan coba lagi.'
                                );
                            callback({
                                draw: d.draw,
                                recordsTotal: lastKnownOverallTotal,
                                recordsFiltered: 0,
                                data: []
                            });
                        }
                    });
                },

                columns: [
                    /* 0: Nomor Urut */
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center cat-td-no',
                        render: function(data, type, row, meta) {
                            const startOffset = estimationTable ?
                                estimationTable.page.info().start :
                                (meta.settings ? (meta.settings._iDisplayStart || 0) : 0);
                            return `<span class="cat-num-pill">${startOffset + meta.row + 1}</span>`;
                        }
                    },

                    /* 1: QUOTATION CODE */
                    {
                        data: 'quotation_code',
                        orderable: true,
                        render: function(data) {
                            const code = escapeHtml(data || '-');
                            return `
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-family: monospace; font-weight: 700; color: #3b82f6; font-size: 13.5px;">${code}</span>
                                    <button type="button" class="btn-copy-code" data-code="${code}" title="Salin Kode Penawaran" style="background: none; border: none; padding: 2px 5px; color: var(--t-muted); cursor: pointer; border-radius: 4px; transition: all 0.15s ease;">
                                        <i class="bi bi-copy" style="font-size: 12px;"></i>
                                    </button>
                                </div>
                            `;
                        }
                    },

                    /* 2: STATUS */
                    {
                        data: 'status',
                        orderable: true,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return buildStatusBadgeHtml(data, row.convert_so, row.convert_invoice);
                        }
                    },

                    /* 3: CUSTOMER */
                    {
                        data: 'customer_name',
                        orderable: false,
                        render: function(data) {
                            return `<span style="font-weight: 600; color: var(--t-base); font-size: 13px;">${escapeHtml(data || '-')}</span>`;
                        }
                    },

                    /* 4: QUOTATION DATE */
                    {
                        data: 'quotation_date',
                        orderable: true,
                        render: function(data) {
                            if (!data) return '-';
                            const d = new Date(data);
                            if (isNaN(d.getTime())) return escapeHtml(data);
                            return `<span style="font-size: 12.5px; color: var(--t-base);">${d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}</span>`;
                        }
                    },

                    /* 5: VALID UNTIL */
                    {
                        data: 'quotation_expired',
                        orderable: true,
                        render: function(data) {
                            if (!data) return '-';
                            const d = new Date(data);
                            if (isNaN(d.getTime())) return escapeHtml(data);
                            return `<span style="font-size: 12.5px; color: var(--t-muted);">${d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })}</span>`;
                        }
                    },

                    /* 6: GRAND TOTAL */
                    {
                        data: 'amount',
                        orderable: true,
                        className: 'text-end',
                        render: function(data) {
                            const val = Number(data || 0);
                            return `<span style="font-weight: 700; color: var(--t-base); font-size: 13.5px;">Rp ${val.toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>`;
                        }
                    },

                    /* 7: CREATED BY */
                    {
                        data: 'user_name',
                        orderable: false,
                        render: function(data) {
                            return `<span style="color: var(--t-muted); font-size: 12.5px; font-weight: 500;">${escapeHtml(data || '-')}</span>`;
                        }
                    }
                ],

                columnDefs: [],
                pageLength: 10,

                layout: {
                    topStart: null,
                    topEnd: null,
                    bottomStart: 'info',
                    bottomEnd: {
                        paging: {
                            buttons: 3,
                            boundaryNumbers: false,
                            firstLast: true,
                            previousNext: true,
                            numbers: true
                        }
                    }
                },

                language: {
                    emptyTable: `
                        <div class="est-empty-state">
                            <div class="est-empty-icon-box">
                                <i class="bi bi-inbox"></i>
                            </div>
                            <div class="est-empty-title">Belum Ada Riwayat Penawaran</div>
                            <div class="est-empty-desc">Data kalkulasi estimasi yang dikirim ke Cetakia akan tersimpan dan tampil di sini.</div>
                        </div>
                    `,
                    zeroRecords: `
                        <div class="est-empty-state">
                            <div class="est-empty-icon-box">
                                <i class="bi bi-search"></i>
                            </div>
                            <div class="est-empty-title">Tidak Ada Data Ditemukan</div>
                            <div class="est-empty-desc">Tidak ada data penawaran yang sesuai dengan kata kunci atau rentang tanggal yang dipilih.</div>
                        </div>
                    `,
                    info: 'Menampilkan _START_ - _END_',
                    infoEmpty: 'Menampilkan 0 penawaran',
                    infoFiltered: '',
                    processing: '<div class="est-loading-box"><i class="bi bi-arrow-repeat spin-icon"></i><span>Memuat data penawaran...</span></div>'
                },

                infoCallback: function(settings, start, end, max, total) {
                    if (total === 0) {
                        return 'Menampilkan 0 penawaran';
                    }
                    const pageLen = settings._iDisplayLength || 10;
                    const currentPage = Math.floor((settings._iDisplayStart || 0) / pageLen) + 1;
                    if (isUnfilteredAllTimeView()) {
                        return `Menampilkan ${start} - ${end} (Halaman ${currentPage})`;
                    }
                    return `Menampilkan ${start} - ${end} dari ${total.toLocaleString('id-ID')} penawaran`;
                },

                drawCallback: function() {
                    const info = this.api().page.info();
                    const countEl = document.getElementById('estTotalCount');
                    if (countEl) {
                        const currentPage = (info.page || 0) + 1;
                        if (hasCustomEstimationFilters() && !isUnfilteredAllTimeView()) {
                            const filteredTotal = Number(info.recordsDisplay || 0);
                            countEl.textContent = `${filteredTotal.toLocaleString('id-ID')} Hasil Filter · Halaman ${currentPage}`;
                        } else {
                            countEl.textContent = `Terhubung Cetakia · Halaman ${currentPage}`;
                        }
                    }
                }
            });

            // Klik pada box tanggal maupun input langsung membuka calendar picker
            document.querySelectorAll('.est-date-box').forEach(function(box) {
                const input = box.querySelector('input[type="date"]');
                if (!input) return;

                function openCalendar() {
                    try {
                        if (typeof input.showPicker === 'function') {
                            input.showPicker();
                        } else {
                            input.focus();
                        }
                    } catch (err) {
                        input.focus();
                    }
                }

                // Klik container box atau icon kalender
                box.addEventListener('click', function(e) {
                    openCalendar();
                });

                // Klik langsung pada input date
                input.addEventListener('click', function(e) {
                    e.stopPropagation();
                    openCalendar();
                });
            });

            // Live search dengan minimal 3 karakter, debounce 650ms, dan support tekan Enter untuk langsung cari.
            const searchInput = document.getElementById('estCustomSearch');
            const clearSearchBtn = document.getElementById('estClearSearch');
            const searchHint = document.getElementById('estSearchHint');
            let searchDebounceTimer;
            let appliedSearch = '';

            function applySearchQuery(query) {
                if (query.length > 0 && query.length < 3) {
                    if (searchHint) {
                        searchHint.textContent = 'Masukkan minimal 3 karakter untuk mencari.';
                        searchHint.hidden = false;
                    }
                    if (appliedSearch !== '') {
                        appliedSearch = '';
                        estimationTable.search('').draw();
                    }
                    return;
                }

                if (searchHint) {
                    searchHint.hidden = true;
                }
                if (query === appliedSearch) {
                    return;
                }

                appliedSearch = query;
                estimationTable.search(query).draw();
            }

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    if (clearSearchBtn) {
                        clearSearchBtn.style.display = query.length > 0 ? 'flex' : 'none';
                    }
                    clearTimeout(searchDebounceTimer);
                    if (query.length >= 3 && searchHint) {
                        searchHint.textContent = 'Tekan Enter untuk cari langsung...';
                        searchHint.hidden = false;
                    }
                    searchDebounceTimer = setTimeout(function() {
                        applySearchQuery(query);
                    }, 650);
                });

                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(searchDebounceTimer);
                        applySearchQuery(this.value.trim());
                    }
                });
            }

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', function() {
                    clearTimeout(searchDebounceTimer);
                    if (searchInput) {
                        searchInput.value = '';
                        this.style.display = 'none';
                        searchInput.focus();
                    }
                    if (searchHint) {
                        searchHint.hidden = true;
                    }
                    if (appliedSearch !== '') {
                        appliedSearch = '';
                        estimationTable.search('').draw();
                    }
                });
            }

            // Listener Perubahan Rentang Tanggal (Live Reload)
            const filterStatus = document.getElementById('filterStatus');
            const filterStartDate = document.getElementById('filterStartDate');
            const filterEndDate = document.getElementById('filterEndDate');

            function handleDateFilterChange() {
                if (filterStartDate && filterEndDate && filterStartDate.value && filterEndDate.value) {
                    if (filterStartDate.value > filterEndDate.value) {
                        filterEndDate.value = filterStartDate.value;
                    }
                }
                estimationTable.ajax.reload(null, true);
            }

            if (filterStartDate) filterStartDate.addEventListener('change', handleDateFilterChange);
            if (filterEndDate) filterEndDate.addEventListener('change', handleDateFilterChange);

            // Inisialisasi Select2
            if (typeof $.fn.select2 !== 'undefined') {
                $('#filterStatus').select2({
                    minimumResultsForSearch: -1,
                    width: '100%'
                });

                $('#estPageLength').select2({
                    minimumResultsForSearch: -1,
                    width: '76px'
                });

                $('#filterStatus').on('change', function() {
                    estimationTable.ajax.reload(null, true);
                });

                $('#estPageLength').on('change', function() {
                    estimationTable.page.len(Number(this.value)).draw();
                });

                $('#filterStatus').on('select2:open', function() {
                    $(this).closest('.cat-select-box').addClass('is-open');
                }).on('select2:close', function() {
                    $(this).closest('.cat-select-box').removeClass('is-open');
                });

                $(document).on('click', '.cat-select-icon', function(e) {
                    e.preventDefault();
                    const select = $(this).closest('.cat-select-box').find('select.select2');
                    if (select.length) {
                        select.select2('open');
                    }
                });
            }

            // Tombol Reset Filters (Sekaligus Muat Ulang Data Terbaru dari Cetakia)
            const btnResetFilters = document.getElementById('btnResetFilters');
            if (btnResetFilters) {
                btnResetFilters.addEventListener('click', function() {
                    clearTimeout(searchDebounceTimer);
                    if (searchInput) {
                        searchInput.value = '';
                    }
                    if (clearSearchBtn) {
                        clearSearchBtn.style.display = 'none';
                    }
                    if (searchHint) {
                        searchHint.hidden = true;
                    }
                    if (filterStartDate) {
                        filterStartDate.value = DEFAULT_START_DATE;
                    }
                    if (filterEndDate) {
                        filterEndDate.value = DEFAULT_END_DATE;
                    }
                    if (filterStatus) {
                        filterStatus.value = 'all';
                    }
                    if (typeof $.fn.select2 !== 'undefined') {
                        $('#filterStatus').trigger('change.select2');
                    }
                    if (appliedSearch !== '') {
                        appliedSearch = '';
                    }
                    forceRefreshNext = true;
                    estimationTable.order([
                        [4, 'desc']
                    ]).search('').draw();
                });
            }

            // Salin Kode Penawaran ke Clipboard (stopPropagation agar tidak ikut membuka modal detail)
            $(document).on('click', '.btn-copy-code', function(e) {
                e.preventDefault();
                e.stopPropagation();
                copyTextWithFeedback($(this).attr('data-code'), this);
            });

            // Salin Kode & Deskripsi dari dalam Modal Detail
            const qdetCopyCodeBtn = document.getElementById('qdetCopyCodeBtn');
            if (qdetCopyCodeBtn) {
                qdetCopyCodeBtn.addEventListener('click', function() {
                    copyTextWithFeedback(this.getAttribute('data-code') || '', this);
                });
            }

            $(document).on('click', '.qdet-copy-desc', function(e) {
                e.preventDefault();
                copyTextWithFeedback($(this).attr('data-desc'), this);
            });

            // Manajemen Modal Detail Quotation (Read-Only)
            const detailModal = document.getElementById('modalDetailQuotation');
            const detailUrlTemplate = "{{ route('admin_estimations_detail', ['id' => '__ID__']) }}";
            let activeDetailXhr = null;

            function setDetailNotice(message) {
                const noticeEl = document.getElementById('qdetNotice');
                const noticeTextEl = document.getElementById('qdetNoticeText');
                if (!noticeEl || !noticeTextEl) return;
                if (message) {
                    noticeTextEl.textContent = message;
                    noticeEl.style.display = 'flex';
                } else {
                    noticeTextEl.textContent = '';
                    noticeEl.style.display = 'none';
                }
            }

            function renderDetailSkeleton(rowData) {
                const code = rowData.quotation_code || '-';
                const totalAmount = Number(rowData.amount ?? rowData.grand_total ?? 0);

                document.getElementById('qdetCodeTitle').textContent = '#' + code;
                if (qdetCopyCodeBtn) qdetCopyCodeBtn.setAttribute('data-code', code);
                document.getElementById('qdetHeaderDate').textContent = 'Quotation Date: ' + formatDateId(rowData.quotation_date, true);
                document.getElementById('qdetHeaderStatus').innerHTML = buildStatusBadgeHtml(rowData.status, rowData.convert_so, rowData.convert_invoice);

                document.getElementById('qdetCustomerName').textContent = rowData.customer_name || '-';
                document.getElementById('qdetBillTo').textContent = 'Memuat...';
                document.getElementById('qdetShipTo').textContent = 'Memuat...';
                document.getElementById('qdetSalesRep').textContent = rowData.user_name || '-';
                document.getElementById('qdetExpiredDate').textContent = formatDateId(rowData.quotation_expired, true);
                document.getElementById('qdetTerms').textContent = '-';

                setDetailNotice('');
                document.getElementById('qdetItemsCountBadge').textContent = '...';
                document.getElementById('qdetItemsBody').innerHTML = `
                    <tr>
                        <td colspan="6">
                            <div class="qdet-loading-state">
                                <i class="bi bi-arrow-repeat spin-icon"></i>
                                <span>Memuat rincian item penawaran...</span>
                            </div>
                        </td>
                    </tr>
                `;

                const notesEl = document.getElementById('qdetNotesText');
                notesEl.textContent = 'Tidak ada catatan tambahan.';
                notesEl.classList.add('is-empty');

                document.getElementById('qdetPaymentSummary').innerHTML = `
                    <div class="qdet-sum-row">
                        <span>Sub Total:</span>
                        <strong>${formatRupiah(totalAmount)}</strong>
                    </div>
                `;
                document.getElementById('qdetSummaryGrandTotal').textContent = formatRupiah(totalAmount);
                document.getElementById('qdetFooterItemsCount').textContent = '0';
                document.getElementById('qdetFooterGrandTotal').textContent = formatRupiah(totalAmount);
                document.getElementById('qdetFooterCode').textContent = '#' + code;
            }

            function renderDetailPayload(detail, fallbackRow) {
                const code = detail.quotation_code || fallbackRow.quotation_code || '-';
                const grandTotal = Number(detail.grand_total ?? detail.amount ?? fallbackRow.amount ?? 0);
                const subTotal = Number(detail.sub_total ?? grandTotal);
                const items = Array.isArray(detail.items) ? detail.items : [];
                const totalItemsCount = Number(detail.total_items ?? items.length);

                document.getElementById('qdetCodeTitle').textContent = '#' + code;
                if (qdetCopyCodeBtn) qdetCopyCodeBtn.setAttribute('data-code', code);
                document.getElementById('qdetHeaderDate').textContent = 'Quotation Date: ' + formatDateId(detail.quotation_date || fallbackRow.quotation_date, true);
                document.getElementById('qdetHeaderStatus').innerHTML = buildStatusBadgeHtml(
                    detail.status || fallbackRow.status,
                    detail.convert_so ?? fallbackRow.convert_so,
                    detail.convert_invoice ?? fallbackRow.convert_invoice
                );

                document.getElementById('qdetCustomerName').textContent = detail.customer_name || fallbackRow.customer_name || '-';
                document.getElementById('qdetBillTo').textContent = detail.bill_to || detail.customer_name || fallbackRow.customer_name || '-';
                document.getElementById('qdetShipTo').textContent = detail.ship_to || detail.bill_to || detail.customer_name || fallbackRow.customer_name || '-';
                document.getElementById('qdetSalesRep').textContent = detail.user_name || fallbackRow.user_name || '-';
                document.getElementById('qdetExpiredDate').textContent = formatDateId(detail.quotation_expired || fallbackRow.quotation_expired, true);

                const termsVal = (detail.terms !== undefined && detail.terms !== null)
                    ? Number(detail.terms)
                    : Number(detail.quotation_terms ?? 0);
                document.getElementById('qdetTerms').textContent = termsVal > 0 ? `${termsVal} Days` : 'Cash (0 Days)';

                document.getElementById('qdetItemsCountBadge').textContent = totalItemsCount;
                const itemsBody = document.getElementById('qdetItemsBody');
                if (items.length === 0) {
                    itemsBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center" style="padding: 24px; color: var(--t-muted);">
                                Tidak ada rincian item yang tersedia untuk penawaran ini.
                            </td>
                        </tr>
                    `;
                } else {
                    itemsBody.innerHTML = items.map(function(it) {
                        const typeLabel = escapeHtml((it.type || it.sales_type || 'PRODUCT').toUpperCase());
                        const itemName = escapeHtml(it.item || '-');
                        const rawDesc = it.description ? String(it.description) : '';
                        const descHtml = rawDesc
                            ? `<div class="qdet-desc-cell"><span>${escapeHtml(rawDesc)}</span><button type="button" class="qdet-copy-inline qdet-copy-desc" data-desc="${escapeHtml(rawDesc)}" title="Salin Deskripsi"><i class="bi bi-copy" style="font-size: 12px;"></i></button></div>`
                            : `<span style="color: var(--t-muted);">-</span>`;
                        const qtyNum = Number(it.qty ?? 0);
                        const qtyFormatted = qtyNum.toLocaleString('id-ID', { maximumFractionDigits: 2 });
                        const unitCode = escapeHtml((it.unit_code || it.unit_name || '-').toUpperCase());
                        const imgThumb = it.image
                            ? `<img src="${escapeHtml(it.image)}" alt="${itemName}" class="qdet-item-thumb">`
                            : '';

                        return `
                            <tr class="qdet-item-row">
                                <td class="qdet-td-full">
                                    <div class="qdet-item-cell">
                                        ${imgThumb}
                                        <span class="qdet-type-badge">${typeLabel}</span>
                                        <strong style="color: var(--t-base); font-size: 13px;">${itemName}</strong>
                                    </div>
                                </td>
                                <td class="qdet-td-full" data-label="Description">${descHtml}</td>
                                <td class="text-center" data-label="Qty" style="font-weight: 700;">${qtyFormatted}</td>
                                <td class="text-end" data-label="Price" style="white-space: nowrap;">${formatRupiah(it.price)}</td>
                                <td class="text-center" data-label="Unit"><span class="qdet-unit-badge">${unitCode}</span></td>
                                <td class="text-end" data-label="Amount"><span class="qdet-amount-val">${formatRupiah(it.amount)}</span></td>
                            </tr>
                        `;
                    }).join('');
                }

                const notesEl = document.getElementById('qdetNotesText');
                const notesText = detail.notes ? String(detail.notes).trim() : '';
                if (notesText !== '') {
                    notesEl.textContent = notesText;
                    notesEl.classList.remove('is-empty');
                } else {
                    notesEl.textContent = 'Tidak ada catatan tambahan.';
                    notesEl.classList.add('is-empty');
                }

                const summaryRows = [
                    `<div class="qdet-sum-row"><span>Sub Total:</span><strong>${formatRupiah(subTotal)}</strong></div>`
                ];

                const discountAmt = Number(detail.discount_amount ?? 0);
                if (detail.discount_enabled && discountAmt > 0) {
                    const discLabel = detail.discount_type === 'percent' && Number(detail.discount ?? 0) > 0
                        ? `Diskon (${Number(detail.discount)}%):`
                        : 'Diskon:';
                    summaryRows.push(`<div class="qdet-sum-row is-discount"><span>${discLabel}</span><strong>- ${formatRupiah(discountAmt)}</strong></div>`);
                }

                const taxAmt = Number(detail.tax_amount ?? 0);
                if (detail.tax_enabled && taxAmt > 0) {
                    const taxRate = Number(detail.tax ?? 11);
                    summaryRows.push(`<div class="qdet-sum-row is-tax"><span>PPN (${taxRate}%):</span><strong>+ ${formatRupiah(taxAmt)}</strong></div>`);
                }

                const pphAmt = Number(detail.pph_amount ?? 0);
                if (detail.pph_enabled && pphAmt > 0) {
                    const pphRate = Number(detail.pph ?? 0);
                    summaryRows.push(`<div class="qdet-sum-row is-discount"><span>PPh (${pphRate}%):</span><strong>- ${formatRupiah(pphAmt)}</strong></div>`);
                }

                document.getElementById('qdetPaymentSummary').innerHTML = summaryRows.join('');
                document.getElementById('qdetSummaryGrandTotal').textContent = formatRupiah(grandTotal);
                document.getElementById('qdetFooterItemsCount').textContent = totalItemsCount;
                document.getElementById('qdetFooterGrandTotal').textContent = formatRupiah(grandTotal);
                document.getElementById('qdetFooterCode').textContent = '#' + code;
            }

            function openQuotationDetailModal(rowData) {
                if (!detailModal || !rowData || !rowData.id) return;

                if (activeDetailXhr && activeDetailXhr.readyState !== 4) {
                    activeDetailXhr.abort();
                }

                renderDetailSkeleton(rowData);
                detailModal.classList.add('is-open');
                document.body.style.overflow = 'hidden';

                const detailUrl = detailUrlTemplate.replace('__ID__', encodeURIComponent(rowData.id));
                activeDetailXhr = $.ajax({
                    url: detailUrl,
                    type: 'GET',
                    timeout: 18000,
                    success: function(res) {
                        if (res && res.success && res.data) {
                            setDetailNotice('');
                            renderDetailPayload(res.data, rowData);
                            return;
                        }
                        renderDetailPayload({ items: [], total_items: 0 }, rowData);
                        setDetailNotice((res && res.message) || 'Rincian detail quotation tidak dapat dimuat.');
                    },
                    error: function(xhr, textStatus) {
                        if (textStatus === 'abort') return;
                        const serverMsg = xhr && xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'Gagal memuat rincian detail dari server Cetakia.';
                        renderDetailPayload({ items: [], total_items: 0 }, rowData);
                        document.getElementById('qdetBillTo').textContent = '-';
                        document.getElementById('qdetShipTo').textContent = '-';
                        setDetailNotice(serverMsg);
                    }
                });
            }

            function closeQuotationDetailModal() {
                if (!detailModal || !detailModal.classList.contains('is-open')) return;
                if (activeDetailXhr && activeDetailXhr.readyState !== 4) {
                    activeDetailXhr.abort();
                }
                detailModal.classList.remove('is-open');
                document.body.style.overflow = '';
            }

            // Klik baris tabel untuk membuka modal detail
            $('#estimationTable tbody').on('click', 'tr', function(e) {
                if ($(e.target).closest('.btn-copy-code, a, button').length) {
                    return;
                }
                const rowData = estimationTable ? estimationTable.row(this).data() : null;
                if (!rowData || !rowData.id) {
                    return;
                }
                openQuotationDetailModal(rowData);
            });

            // Tutup modal via tombol tutup, klik di luar modal, dan tombol Escape
            const btnCloseDetailModal = document.getElementById('btnCloseDetailModal');
            const btnFooterCloseDetailModal = document.getElementById('btnFooterCloseDetailModal');
            if (btnCloseDetailModal) {
                btnCloseDetailModal.addEventListener('click', closeQuotationDetailModal);
            }
            if (btnFooterCloseDetailModal) {
                btnFooterCloseDetailModal.addEventListener('click', closeQuotationDetailModal);
            }
            if (detailModal) {
                detailModal.addEventListener('click', function(e) {
                    if (e.target === detailModal) {
                        closeQuotationDetailModal();
                    }
                });
            }
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && detailModal && detailModal.classList.contains('is-open')) {
                    closeQuotationDetailModal();
                }
            });
        });
    </script>
@endsection

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
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        /* Actions in Card Header */
        .cat-card-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .cat-btn-head {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            height: 38px;
            padding: 0 13px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.18s ease;
            white-space: nowrap;
            cursor: pointer;
            box-sizing: border-box;
        }

        .cat-btn-head svg {
            width: 15px;
            height: 15px;
            flex-shrink: 0;
            stroke-width: 2.2;
        }

        /* Light Theme Buttons */
        html[data-theme="light"] .cat-btn-head {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #334155;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        html[data-theme="light"] .cat-btn-head:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        }

        /* Dark Theme Buttons */
        html[data-theme="dark"] .cat-btn-head {
            background: rgba(20, 27, 45, 0.75);
            border: 1px solid rgba(255, 255, 255, 0.09);
            color: var(--t-base);
        }

        html[data-theme="dark"] .cat-btn-head:hover {
            background: rgba(30, 41, 69, 0.95);
            border-color: rgba(96, 165, 250, 0.35);
            color: #ffffff;
            transform: translateY(-1px);
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
        }

        .cat-btn-head-primary:hover {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.38);
        }

        .cat-btn-head-primary i {
            font-size: 14px;
        }

        /* Filter & Controls Toolbar (Adminator Style) */
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
            min-width: 200px;
            max-width: 300px;
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
            min-width: 170px;
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

        /* Light Theme Select Box & Icon */
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

        /* Select2 Styling inside Filter Card */
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

        /* Dark Theme Select Box & Icon */
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

        /* Bulk Action Toolbar */
        .cat-bulk-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            padding: 12px 18px;
            margin-bottom: 16px;
            border-radius: 10px;
            background: rgba(37, 99, 235, 0.08);
            border: 1px solid rgba(37, 99, 235, 0.25);
            animation: catFadeInSlide 0.2s ease-out;
        }

        html[data-theme="dark"] .cat-bulk-toolbar {
            background: rgba(37, 99, 235, 0.16);
            border-color: rgba(59, 130, 246, 0.35);
        }

        @keyframes catFadeInSlide {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .cat-bulk-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cat-bulk-badge {
            background: #2563eb;
            color: #ffffff;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        .cat-bulk-text {
            font-size: 13px;
            color: var(--t-base);
            font-weight: 500;
        }

        .cat-bulk-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Responsive Horizontal & Vertical Scroll Container (Sticky Header) */
        .cat-table-wrap {
            width: 100%;
            max-height: 540px;
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

        /* Modern Category Table */
        .cat-modern-table {
            width: 100% !important;
            min-width: 760px;
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

        .cat-modern-table tbody tr.is-selected td {
            background: rgba(37, 99, 235, 0.09) !important;
        }

        html[data-theme="light"] .cat-modern-table tbody tr.is-selected td {
            background: #eff6ff !important;
        }

        .cat-modern-table th:first-child,
        .cat-modern-table td:first-child {
            border-left: 3px solid transparent;
        }

        .cat-modern-table tbody tr.is-selected td:first-child {
            border-left: 3px solid #2563eb !important;
        }

        html[data-theme="dark"] .cat-modern-table tbody tr.is-selected td {
            background: rgba(59, 130, 246, 0.16) !important;
        }

        /* Checkbox Column Width and Exact Vertical Centering */
        .cat-modern-table th.cat-th-check,
        .cat-modern-table td.cat-td-check {
            width: 52px !important;
            min-width: 52px !important;
            max-width: 52px !important;
            padding: 12px 0 !important;
            text-align: center !important;
            vertical-align: middle !important;
        }

        .cat-modern-table th.cat-th-check .cat-custom-checkbox,
        .cat-modern-table td.cat-td-check .cat-custom-checkbox {
            margin: 0 auto !important;
            display: inline-flex !important;
            justify-content: center !important;
            align-items: center !important;
        }

        /* Stylized Custom Checkbox Component (Light & Dark Theme) */
        .cat-custom-checkbox {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin: 0 !important;
            padding: 0 !important;
            user-select: none;
            line-height: 1;
            vertical-align: middle;
        }

        .cat-custom-checkbox input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            margin: 0;
            pointer-events: none;
        }

        .cat-checkbox-mark {
            width: 19px;
            height: 19px;
            border-radius: 5px;
            border: 1.5px solid #94a3b8;
            background-color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            box-sizing: border-box;
            color: #ffffff !important;
        }

        /* Light Theme Checkbox */
        html[data-theme="light"] .cat-checkbox-mark {
            border-color: #94a3b8;
            background-color: #ffffff;
        }

        html[data-theme="light"] .cat-custom-checkbox:hover .cat-checkbox-mark {
            border-color: #2563eb;
            background-color: #eff6ff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
        }

        /* Dark Theme Checkbox */
        html[data-theme="dark"] .cat-checkbox-mark {
            border-color: #475569;
            background-color: #0f172a;
        }

        html[data-theme="dark"] .cat-custom-checkbox:hover .cat-checkbox-mark {
            border-color: #60a5fa;
            background-color: #1e293b;
            box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.18);
        }

        /* Checked State */
        .cat-custom-checkbox input:checked + .cat-checkbox-mark,
        .cat-custom-checkbox.is-indeterminate .cat-checkbox-mark {
            background-color: #2563eb !important;
            border-color: #2563eb !important;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35) !important;
            color: #ffffff !important;
        }

        /* Checkbox SVG Icons */
        .cat-checkbox-mark svg {
            display: none;
            width: 12px;
            height: 12px;
        }

        .cat-checkbox-mark svg path {
            stroke: #ffffff !important;
            stroke-width: 2.2 !important;
        }

        .cat-custom-checkbox input:checked + .cat-checkbox-mark .check-icon {
            display: block !important;
        }

        .cat-custom-checkbox.is-indeterminate .cat-checkbox-mark .minus-icon {
            display: block !important;
        }

        .cat-custom-checkbox.is-indeterminate .cat-checkbox-mark .check-icon {
            display: none !important;
        }

        /* Responsive Breakpoints */
        @media (max-width: 991px) {
            .cat-card-header {
                flex-direction: column;
                align-items: stretch;
                gap: 16px;
            }

            .cat-card-actions {
                width: 100%;
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }

            .cat-btn-head-primary {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 768px) {
            .cat-card-container {
                padding: 16px !important;
            }

            .cat-filter-card {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
                padding: 12px;
            }

            .cat-filter-group-left {
                flex-direction: column;
                align-items: stretch;
                width: 100%;
                gap: 10px;
            }

            .cat-search-box {
                width: 100% !important;
                max-width: 100% !important;
                min-width: 0 !important;
            }

            .cat-select-box {
                width: 100% !important;
                min-width: 0 !important;
            }

            .cat-filter-subgroup {
                width: 100% !important;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .cat-filter-subgroup .cat-select-box {
                flex: 1;
                min-width: 0;
            }

            .cat-filter-subgroup .cat-btn-reset {
                flex-shrink: 0;
            }

            .cat-filter-group-right {
                width: 100%;
                justify-content: flex-end;
                padding-top: 8px;
                border-top: 1px dashed var(--border-soft);
            }
        }

        @media (max-width: 576px) {
            .cat-card-container {
                padding: 12px !important;
                border-radius: 12px;
            }

            .cat-card-header {
                padding-bottom: 14px;
                margin-bottom: 14px;
            }

            .cat-card-icon {
                width: 38px;
                height: 38px;
                font-size: 18px;
                border-radius: 10px;
            }

            .cat-card-title {
                font-size: 15px;
            }

            .cat-card-desc {
                font-size: 12px;
            }

            .cat-title-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 6px;
            }

            .cat-card-actions {
                grid-template-columns: repeat(3, 1fr);
                gap: 6px;
            }

            .cat-btn-head {
                height: 36px;
                padding: 0 6px;
                font-size: 11.5px;
                gap: 4px;
            }

            .cat-btn-head-primary {
                height: 38px;
                font-size: 13px;
            }

            .cat-table-wrap {
                max-height: 480px;
            }
        }

        /* Rich Cell Details */
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

        .cat-cell-name-box {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cat-icon-symbol {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.12), rgba(59, 130, 246, 0.05));
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            border: 1px solid rgba(59, 130, 246, 0.18);
            flex-shrink: 0;
            transition: transform 0.15s ease;
        }

        html[data-theme="dark"] .cat-icon-symbol {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.22), rgba(37, 99, 235, 0.08));
            color: #60a5fa;
            border-color: rgba(96, 165, 250, 0.25);
        }

        .cat-modern-table tbody tr:hover .cat-icon-symbol {
            transform: scale(1.08);
        }

        .cat-name-details {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .cat-name-primary {
            font-weight: 700;
            color: var(--t-base);
            font-size: 13.5px;
            letter-spacing: -0.01em;
        }

        .cat-name-badge {
            font-size: 10.5px;
            color: var(--t-muted);
            font-weight: 500;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .cat-user-badge {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cat-user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            color: #ffffff;
            font-size: 11.5px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.18);
            letter-spacing: 0.02em;
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

        .cat-date-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 5px 10px;
            border-radius: 7px;
            background: var(--bg-hover);
            border: 1px solid var(--border-soft);
            font-family: JetBrains Mono, monospace;
            font-size: 11.5px;
            color: var(--t-muted);
            font-weight: 500;
        }

        .cat-date-chip i {
            color: var(--primary);
            font-size: 11px;
        }

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

        .cat-btn-edit:hover {
            background: rgba(37, 99, 235, 0.15) !important;
            color: #2563eb !important;
            border-color: rgba(37, 99, 235, 0.35) !important;
            transform: translateY(-1px);
        }

        .cat-btn-del:hover {
            background: rgba(239, 68, 68, 0.15) !important;
            color: #ef4444 !important;
            border-color: rgba(239, 68, 68, 0.35) !important;
            transform: translateY(-1px);
        }

        .spin-icon {
            display: inline-block;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }

        /* DataTables Footer (Info & Paging) Spacing & Layout */
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

        @media (max-width: 576px) {
            .dt-container .dt-layout-row:last-child {
                padding: 6px 10px 10px 10px !important;
            }

            .dt-container .dt-info,
            .dataTables_wrapper .dataTables_info,
            .dt-container .dt-layout-cell.dt-start {
                padding-left: 10px !important;
                font-size: 12px !important;
            }

            .dt-container .dt-paging,
            .dataTables_wrapper .dataTables_paginate,
            .dt-container .dt-layout-cell.dt-end {
                padding-right: 10px !important;
            }
        }
    </style>
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero cat-hero-compact">
                    <div class="hero-text"><span class="eyebrow">MASTER DATA · KATEGORI</span>
                        <p class="hero-sub">Kelola dan pantau seluruh data kategori yang digunakan dalam proses produksi dan
                            perhitungan estimasi harga.</p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card cat-card-container">
                        <!-- Card Header (Terintegrasi dengan Tombol Aksi) -->
                        <div class="cat-card-header">
                            <div class="cat-card-header-main">
                                <div class="cat-card-title-wrap">
                                    <div class="cat-card-icon">
                                        <i class="bi bi-tags-fill"></i>
                                    </div>
                                    <div>
                                        <div class="cat-title-row">
                                            <h3 class="cat-card-title">Daftar Kategori Produk</h3>
                                            <span class="cat-badge-total" id="catTotalBadge">
                                                <span class="cat-pulse-dot"></span>
                                                <span id="catTotalCount">Memuat data...</span>
                                            </span>
                                        </div>
                                        <p class="cat-card-desc">Master klasifikasi untuk produk digital printing, offset, dan merchandise.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Integrated Action Buttons -->
                            <div class="cat-card-actions">
                                <button type="button" class="cat-btn-head" id="btnExportKategori" title="Export Excel">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <path d="M7 10l5 5 5-5" />
                                        <path d="M12 15V3" />
                                    </svg>
                                    <span>Export</span>
                                </button>
                                <a href="{{ asset('templates/template_kategori.xlsx') }}" class="cat-btn-head" title="Download Template Excel">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <path d="M7 10l5 5 5-5" />
                                        <path d="M12 15V3" />
                                    </svg>
                                    <span>Template</span>
                                </a>
                                <button type="button" class="cat-btn-head" id="btnImportKategori" title="Import Excel">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M12 16V3" />
                                        <path d="m7 8 5-5 5 5" />
                                        <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                                    </svg>
                                    <span>Import</span>
                                </button>
                                <button type="button" class="cat-btn-head-primary" id="btnTambahKategori">
                                    <i class="bi bi-plus-lg"></i>
                                    <span>Tambah Kategori</span>
                                </button>
                            </div>
                        </div>

                        <!-- Bulk Action Toolbar (Tampil saat baris dipilih) -->
                        <div class="cat-bulk-toolbar" id="catBulkToolbar" style="display: none;">
                            <div class="cat-bulk-info">
                                <span class="cat-bulk-badge" id="catBulkCount">0 terpilih</span>
                                <span class="cat-bulk-text">Kategori yang dipilih siap untuk tindakan massal</span>
                            </div>
                            <div class="cat-bulk-actions">
                                <button type="button" class="btn btn--ghost btn--sm" id="btnBatalPilihan">
                                    <i class="bi bi-x-circle me-1"></i> Batal Pilihan
                                </button>
                                <button type="button" class="btn btn--danger btn--sm" id="btnBulkDelete">
                                    <i class="bi bi-trash3-fill me-1"></i> Hapus Terpilih
                                </button>
                            </div>
                        </div>

                        <!-- Filter & Action Toolbar (Referensi Adminator - Responsive) -->
                        <div class="cat-filter-card">
                            <div class="cat-filter-group-left">
                                <div class="cat-search-box">
                                    <i class="bi bi-search cat-search-icon"></i>
                                    <input type="text" id="catCustomSearch" class="cat-filter-input" placeholder="Cari nama kategori atau pembuat...">
                                    <button type="button" id="catClearSearch" class="cat-clear-btn" title="Hapus pencarian" style="display: none;">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>

                                <div class="cat-select-box">
                                    <i class="bi bi-person-fill cat-select-icon"></i>
                                    <select id="filterCreator" class="select2" style="width: 100%;">
                                        <option value="all">Semua Pembuat</option>
                                        @if(isset($creators))
                                            @foreach($creators as $creator)
                                                <option value="{{ $creator }}">{{ $creator }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>

                                <div class="cat-filter-subgroup">
                                    <div class="cat-select-box">
                                        <i class="bi bi-calendar3 cat-select-icon"></i>
                                        <select id="filterPeriod" class="select2" style="width: 100%;">
                                            <option value="all">Semua Periode</option>
                                            <option value="today">Hari Ini</option>
                                            <option value="7_days">7 Hari Terakhir</option>
                                            <option value="month">Bulan Ini</option>
                                            <option value="year">Tahun Ini</option>
                                        </select>
                                    </div>

                                    <button type="button" id="btnResetFilters" class="cat-btn-reset" title="Reset semua filter">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                        <span>Reset</span>
                                    </button>
                                </div>
                            </div>

                            <div class="cat-filter-group-right">
                                <div class="cat-length-box">
                                    <span class="cat-length-label">Baris:</span>
                                    <select id="catPageLength" class="select2" style="width: 76px;">
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Table Responsive Scroll Container (Scroll horizontal + Sticky header, tanpa collapse child row) -->
                        <div class="cat-table-wrap">
                            <table id="categoryTable" class="cat-modern-table" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="cat-th-check text-center">
                                            <label class="cat-custom-checkbox" title="Pilih Semua di Halaman Ini">
                                                <input type="checkbox" id="checkAllCategories">
                                                <span class="cat-checkbox-mark">
                                                    <svg class="check-icon" viewBox="0 0 14 14" fill="none">
                                                        <path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                                    </svg>
                                                    <svg class="minus-icon" viewBox="0 0 14 14" fill="none">
                                                        <path d="M3 7H11" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"/>
                                                    </svg>
                                                </span>
                                            </label>
                                        </th>
                                        <th class="cat-th-no text-center" style="width: 60px;">No</th>
                                        <th class="cat-th-name" style="min-width: 220px;">Nama Kategori</th>
                                        <th class="cat-th-creator" style="min-width: 170px;">Dibuat Oleh</th>
                                        <th class="cat-th-date" style="min-width: 150px;">Dibuat Pada</th>
                                        <th class="cat-th-actions text-center" style="width: 110px;">Aksi</th>
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

    <div class="modal-overlay" id="modalTambahKategori">

        <div class="modal-dialog">

            <form method="POST" action="{{ route('admin_store_categories') }}">
                @csrf
                <div class="modal-header">
                    <div>
                        <span class="eyebrow">MASTER DATA · KATEGORI</span>
                        <br>
                        <span class="eyebrow" style="text-transform: uppercase;">Tambah Kategori</span>
                    </div>

                    <button type="button" class="modal-close" id="btnTutupModal" aria-label="Tutup">
                        <svg viewBox="0 0 24 24">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="category-form-header">

                        <div>
                            <label class="category-label">
                                Nama Kategori
                            </label>

                            <p class="category-description">
                                Tambahkan satu atau beberapa kategori sekaligus.
                            </p>
                        </div>

                        <button type="button" class="btn btn--ghost" id="btnTambahBarisKategori">

                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>

                            Tambah Baris

                        </button>

                    </div>


                    <div id="categoryRows">

                        <!-- Baris pertama -->

                        <div class="category-row">

                            <input type="text" name="categories[]" class="input"
                                placeholder="Masukkan nama kategori...">

                            <button type="button" class="btn-remove-category" aria-label="Hapus kategori">

                                <i class="bi bi-trash3-fill"></i>

                            </button>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn--ghost" id="btnBatalKategori">
                        Batal
                    </button>

                    <button type="submit" class="btn btn--primary">
                        <svg viewBox="0 0 24 24">
                            <path d="M5 12l4 4L19 6" />
                        </svg>

                        Simpan
                    </button>

                </div>
            </form>

        </div>

    </div>

    <!-- Modal Edit Kategori -->
    <div class="modal-overlay" id="modalEditKategori">

        <div class="modal-dialog">

            <form id="formEditKategori">

                <div class="modal-header">

                    <div>

                        <span class="eyebrow">
                            MASTER DATA · KATEGORI
                        </span>
                        <br>
                        <span class="eyebrow">
                            Edit Kategori
                        </span>

                    </div>


                    <button type="button" class="modal-close" id="btnTutupEditModal" aria-label="Tutup">

                        <svg viewBox="0 0 24 24">

                            <path d="M18 6 6 18" />

                            <path d="m6 6 12 12" />

                        </svg>

                    </button>

                </div>


                <div class="modal-body">

                    <div class="category-form-header">

                        <div>

                            <label for="editCategoryName" class="category-label">

                                Nama Kategori

                            </label>


                            <p class="category-description">

                                Perbarui nama kategori yang dipilih.

                            </p>

                        </div>

                    </div>


                    <div class="category-row">

                        <input type="text" id="editCategoryName" name="name" class="input"
                            placeholder="Masukkan nama kategori..." autocomplete="off">

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button" class="btn btn--ghost" id="btnBatalEditKategori">

                        Batal

                    </button>


                    <button type="submit" class="btn btn--primary">

                        <svg viewBox="0 0 24 24">

                            <path d="M5 12l4 4L19 6" />

                        </svg>

                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>

    </div>

    <div class="modal-overlay" id="modalImportKategori">

        <div class="modal modal-import">

            <!-- HEADER -->

            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MASTER DATA · KATEGORI
                    </span>
                    <br>
                    <span class="eyebrow">
                        Import Kategori
                    </span>

                </div>


                <button type="button" class="modal-close" id="btnCloseImportKategori" aria-label="Tutup">

                    <svg viewBox="0 0 24 24">

                        <path d="M18 6 6 18" />

                        <path d="m6 6 12 12" />

                    </svg>

                </button>

            </div>


            <!-- BODY -->

            <div class="modal-body">

                <div class="import-description">

                    <p>
                        Pilih file Excel yang berisi data kategori
                        untuk diimport ke dalam sistem.
                    </p>

                </div>


                <!-- CUSTOM FILE INPUT -->

                <label for="fileImportKategori" class="file-upload-box" id="fileUploadBox">

                    <input type="file" id="fileImportKategori" name="file" accept=".xlsx,.xls" hidden>


                    <div class="file-upload-content">

                        <div class="file-upload-icon">

                            <svg viewBox="0 0 24 24">

                                <path d="M12 16V3" />

                                <path d="m7 8 5-5 5 5" />

                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />

                            </svg>

                        </div>


                        <div class="file-upload-text">

                            <strong>
                                Pilih file Excel
                            </strong>

                            <span>
                                Klik untuk memilih file dari komputer
                            </span>

                        </div>

                    </div>


                    <div class="file-selected-name" id="fileSelectedName">

                        Belum ada file dipilih

                    </div>

                </label>

            </div>


            <!-- FOOTER -->

            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCancelImportKategori">

                    Batal

                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportKategori">

                    <svg viewBox="0 0 24 24">

                        <path d="M12 16V3" />

                        <path d="m7 8 5-5 5 5" />

                    </svg>

                    Import Data

                </button>

            </div>

        </div>

    </div>

    <!-- =========================================================
                                                                                                                                                                                                                                                                                                                                                                                                                                 MODAL KONFIRMASI HAPUS KATEGORI
                                                                                                                                                                                                                                                                                                                                                                                                                            ========================================================= -->

    <div class="modal-overlay" id="modalDeleteKategori">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->

            <div class="modal-header">

                <div>
                    <span class="eyebrow">
                        MASTER DATA · KATEGORI
                    </span>
                    <br>
                    <span class="eyebrow">
                        Hapus Kategori
                    </span>
                </div>

                <button type="button" class="modal-close" id="btnTutupDeleteModal" aria-label="Tutup">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">

                        <path d="M18 6L6 18" />
                        <path d="M6 6L18 18" />

                    </svg>

                </button>

            </div>


            <!-- BODY -->

            <div class="modal-body">

                <div class="delete-confirmation">

                    <div class="delete-icon">

                        <i class="bi bi-trash3-fill"></i>

                    </div>


                    <div class="delete-content">

                        <h3>
                            Hapus kategori?
                        </h3>
                        <p>
                            Apakah kamu yakin ingin menghapus kategori ini?
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->

            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDelete">

                    Batal

                </button>


                <button type="button" class="btn btn--danger" id="btnConfirmDelete">

                    <i class="bi bi-trash3-fill"></i>

                    Hapus

                </button>

            </div>

        </div>

    </div>

    <!-- =========================================================
         MODAL KONFIRMASI BULK DELETE KATEGORI
    ========================================================= -->
    <div class="modal-overlay" id="modalBulkDeleteKategori">
        <div class="modal-dialog modal-delete">
            <div class="modal-header">
                <div>
                    <span class="eyebrow">MASTER DATA · KATEGORI</span>
                    <br>
                    <span class="eyebrow" style="color: #ef4444;">Hapus Massal Kategori</span>
                </div>
                <button type="button" class="modal-close" id="btnTutupBulkDeleteModal" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18" />
                        <path d="M6 6L18 18" />
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="delete-confirmation">
                    <div class="delete-icon" style="background: rgba(239, 68, 68, 0.12); color: #ef4444;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <div class="delete-content">
                        <h3>Hapus Kategori Terpilih?</h3>
                        <p id="bulkDeleteDescription">
                            Apakah Anda yakin ingin menghapus kategori yang dipilih? Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--ghost" id="btnBatalBulkDelete">Batal</button>
                <button type="button" class="btn btn--danger" id="btnConfirmBulkDelete">
                    <i class="bi bi-trash3-fill me-1"></i> Ya, Hapus Terpilih
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let categoryTable;
        const selectedCategoryIds = new Set();

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function getInitials(name) {
            if (!name) return 'AD';
            const parts = name.trim().split(/\s+/);
            if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
            return (parts[0][0] + parts[1][0]).toUpperCase();
        }

        function getAvatarColor(name) {
            const colors = [
                '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6',
                '#ec4899', '#06b6d4', '#6366f1', '#14b8a6'
            ];
            let hash = 0;
            const str = name || 'Admin';
            for (let i = 0; i < str.length; i++) {
                hash = str.charCodeAt(i) + ((hash << 5) - hash);
            }
            const index = Math.abs(hash) % colors.length;
            return colors[index];
        }

        function updateSelectionUI() {
            const bulkToolbar = document.getElementById('catBulkToolbar');
            const bulkCountEl = document.getElementById('catBulkCount');
            const checkAll = document.getElementById('checkAllCategories');
            const checkAllLabel = checkAll ? checkAll.closest('.cat-custom-checkbox') : null;
            const rowCheckboxes = document.querySelectorAll('.cat-row-check');

            const count = selectedCategoryIds.size;
            if (bulkToolbar) {
                if (count > 0) {
                    bulkToolbar.style.display = 'flex';
                    if (bulkCountEl) bulkCountEl.textContent = `${count} terpilih`;
                } else {
                    bulkToolbar.style.display = 'none';
                }
            }

            let visibleCheckedCount = 0;
            rowCheckboxes.forEach(cb => {
                const id = Number(cb.value);
                const isChecked = selectedCategoryIds.has(id);
                cb.checked = isChecked;

                const tr = cb.closest('tr');
                if (tr) {
                    if (isChecked) {
                        tr.classList.add('is-selected');
                    } else {
                        tr.classList.remove('is-selected');
                    }
                }

                if (isChecked) visibleCheckedCount++;
            });

            if (checkAll && checkAllLabel) {
                const totalVisible = rowCheckboxes.length;
                if (totalVisible === 0) {
                    checkAll.checked = false;
                    checkAll.indeterminate = false;
                    checkAllLabel.classList.remove('is-indeterminate');
                } else if (visibleCheckedCount === totalVisible) {
                    checkAll.checked = true;
                    checkAll.indeterminate = false;
                    checkAllLabel.classList.remove('is-indeterminate');
                } else if (visibleCheckedCount > 0) {
                    checkAll.checked = false;
                    checkAll.indeterminate = true;
                    checkAllLabel.classList.add('is-indeterminate');
                } else {
                    checkAll.checked = false;
                    checkAll.indeterminate = false;
                    checkAllLabel.classList.remove('is-indeterminate');
                }
            }
        }
        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('modalTambahKategori');

            const btnTambah = document.getElementById('btnTambahKategori');

            const btnTutup = document.getElementById('btnTutupModal');

            const btnBatal = document.getElementById('btnBatalKategori');

            const btnTambahBaris = document.getElementById('btnTambahBarisKategori');

            const categoryRows = document.getElementById('categoryRows');


            /* =========================
               BUKA MODAL
            ========================= */

            btnTambah.addEventListener('click', function() {

                modal.classList.add('is-open');

            });


            /* =========================
               TUTUP MODAL
            ========================= */

            function tutupModal() {

                modal.classList.remove('is-open');

            }


            btnTutup.addEventListener('click', tutupModal);

            btnBatal.addEventListener('click', tutupModal);


            /* Klik background */

            modal.addEventListener('click', function(event) {

                if (event.target === modal) {

                    tutupModal();

                }

            });


            /* Tombol ESC */

            document.addEventListener('keydown', function(event) {

                if (event.key === 'Escape') {

                    tutupModal();

                }

            });


            /* =========================
               TAMBAH BARIS KATEGORI
            ========================= */

            btnTambahBaris.addEventListener('click', function() {

                const row = document.createElement('div');

                row.classList.add('category-row');


                row.innerHTML = `

            <input
                type="text"
                name="categories[]"
                class="input"
                placeholder="Masukkan nama kategori..."
            >

            <button
                type="button"
                class="btn-remove-category"
                aria-label="Hapus kategori">

                <i class="bi bi-trash3-fill"></i>

            </button>

        `;


                categoryRows.appendChild(row);


                /* Fokus ke input baru */

                row.querySelector('input').focus();

            });


            /* =========================
               HAPUS BARIS
            ========================= */

            categoryRows.addEventListener('click', function(event) {

                const removeButton = event.target.closest('.btn-remove-category');


                if (!removeButton) {

                    return;

                }


                const rows = categoryRows.querySelectorAll('.category-row');


                /* Jangan hapus jika tinggal 1 */

                if (rows.length === 1) {

                    rows[0].querySelector('input').value = '';

                    rows[0].querySelector('input').focus();

                    return;

                }


                removeButton.closest('.category-row').remove();

            });

        });

        // document.addEventListener('DOMContentLoaded', function() {

        //     const btnImport = document.getElementById('btnImportMaterial');

        //     const modal = document.getElementById('modalImportMaterial');

        //     const btnClose = document.getElementById('btnCloseImportMaterial');

        //     const btnCancel = document.getElementById('btnCancelImportMaterial');

        //     const fileInput = document.getElementById('fileImportMaterial');

        //     const fileName = document.getElementById('fileSelectedName');


        //     /* =========================
        //        BUKA MODAL
        //     ========================= */

        //     btnImport.addEventListener('click', function() {

        //         modal.classList.add('is-open');

        //     });


        //     /* =========================
        //        TUTUP MODAL
        //     ========================= */

        //     function closeModal() {

        //         modal.classList.remove('is-open');

        //     }


        //     btnClose.addEventListener('click', closeModal);

        //     btnCancel.addEventListener('click', closeModal);


        //     /* Klik background */

        //     modal.addEventListener('click', function(event) {

        //         if (event.target === modal) {

        //             closeModal();

        //         }

        //     });


        //     /* Tombol ESC */

        //     document.addEventListener('keydown', function(event) {

        //         if (event.key === 'Escape') {

        //             closeModal();

        //         }

        //     });


        //     /* =========================
        //        FILE SELECTED
        //     ========================= */

        //     fileInput.addEventListener('change', function() {

        //         if (this.files.length > 0) {

        //             fileName.textContent = this.files[0].name;

        //             fileName.classList.add('has-file');

        //         } else {

        //             fileName.textContent = 'Belum ada file dipilih';

        //             fileName.classList.remove('has-file');

        //         }

        //     });

        // });

        document.addEventListener('DOMContentLoaded', function() {

            const btnImport = document.getElementById('btnImportKategori');

            const modal = document.getElementById('modalImportKategori');

            const btnClose = document.getElementById('btnCloseImportKategori');

            const btnCancel = document.getElementById('btnCancelImportKategori');

            const btnSave = document.getElementById('btnSaveImportKategori');

            const fileInput = document.getElementById('fileImportKategori');

            const fileName = document.getElementById('fileSelectedName');


            /* =========================
               BUKA MODAL
            ========================= */

            btnImport.addEventListener('click', function() {

                modal.classList.add('is-open');

            });


            /* =========================
               TUTUP MODAL
            ========================= */

            function closeModal() {

                modal.classList.remove('is-open');

            }


            btnClose.addEventListener('click', closeModal);

            btnCancel.addEventListener('click', closeModal);


            /* Klik Background */

            modal.addEventListener('click', function(event) {

                if (event.target === modal) {

                    closeModal();

                }

            });


            /* Tombol ESC */

            document.addEventListener('keydown', function(event) {

                if (event.key === 'Escape') {

                    closeModal();

                }

            });


            /* =========================
               FILE SELECTED
            ========================= */

            fileInput.addEventListener('change', function() {

                if (this.files.length > 0) {

                    fileName.textContent = this.files[0].name;

                    fileName.classList.add('has-file');

                } else {

                    fileName.textContent = 'Belum ada file dipilih';

                    fileName.classList.remove('has-file');

                }

            });


            /* =========================
               IMPORT DATA
            ========================= */

            btnSave.addEventListener('click', async function() {

                /*
                |--------------------------------------------------------------------------
                | VALIDASI FILE
                |--------------------------------------------------------------------------
                */

                if (fileInput.files.length === 0) {

                    showToast(
                        'error',
                        'Silakan pilih file Excel terlebih dahulu.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | SIAPKAN FORM DATA
                |--------------------------------------------------------------------------
                */

                const formData = new FormData();

                formData.append(
                    'file',
                    fileInput.files[0]
                );


                /*
                |--------------------------------------------------------------------------
                | IMPORT DATA
                |--------------------------------------------------------------------------
                */

                try {

                    const response = await fetch(
                        "{{ route('admin_import_category') }}", {
                            method: 'POST',

                            headers: {

                                'X-CSRF-TOKEN': document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),

                                'Accept': 'application/json'

                            },

                            body: formData

                        }
                    );


                    const data = await response.json();


                    /*
                    |--------------------------------------------------------------------------
                    | JIKA BERHASIL
                    |--------------------------------------------------------------------------
                    */

                    if (data.success) {

                        // Tutup modal
                        closeModal();


                        // Tampilkan notifikasi
                        showToast(
                            'success',
                            data.message
                        );


                        // Reset input file
                        fileInput.value = '';

                        fileName.textContent = 'Belum ada file dipilih';

                        fileName.classList.remove('has-file');


                        // Reload DataTable tanpa reload halaman
                        categoryTable.ajax.reload(null, false);

                    } else {

                        showToast(
                            'error',
                            data.message || 'Import data gagal.'
                        );

                    }

                } catch (error) {

                    console.error(error);

                    showToast(
                        'error',
                        'Terjadi kesalahan saat mengimport data.'
                    );

                }

            });

        });
        document.addEventListener('DOMContentLoaded', function() {

            categoryTable = new DataTable('#categoryTable', {

                processing: true,
                serverSide: true,
                ordering: true,
                order: [
                    [2, 'asc']
                ],
                stripeClasses: [],

                ajax: {
                    url: "{{ route('admin_categories_data') }}",
                    type: "GET",
                    data: function(d) {
                        const filterCreator = document.getElementById('filterCreator');
                        const filterPeriod = document.getElementById('filterPeriod');
                        d.creator = filterCreator ? filterCreator.value : 'all';
                        d.period = filterPeriod ? filterPeriod.value : 'all';
                    }
                },

                columns: [
                    /*
                    |--------------------------------------------------------------------------
                    | 0: CHECKBOX MASSAL (CUSTOM STYLIZED COMPONENT)
                    |--------------------------------------------------------------------------
                    */
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center cat-td-check',
                        render: function(data, type, row) {
                            const isChecked = selectedCategoryIds.has(Number(data));
                            const safeName = escapeHtml(row.name || '');
                            return `
                                <label class="cat-custom-checkbox">
                                    <input type="checkbox"
                                        class="cat-row-check"
                                        value="${data}"
                                        data-id="${data}"
                                        data-name="${safeName}"
                                        ${isChecked ? 'checked' : ''}
                                        aria-label="Pilih kategori ${safeName}">
                                    <span class="cat-checkbox-mark">
                                        <svg class="check-icon" viewBox="0 0 14 14" fill="none">
                                            <path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </label>
                            `;
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | 1: NOMOR URUT
                    |--------------------------------------------------------------------------
                    */
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center cat-td-no',
                        render: function(data, type, row, meta) {
                            const pageInfo = categoryTable.page.info();
                            return `<span class="cat-num-pill">${pageInfo.start + meta.row + 1}</span>`;
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | 2: NAMA KATEGORI
                    |--------------------------------------------------------------------------
                    */
                    {
                        data: 'name',
                        orderable: true,
                        className: 'cat-td-name',
                        render: function(data, type, row) {
                            return `
                                <div class="cat-cell-name-box">
                                    <div class="cat-icon-symbol">
                                        <i class="bi bi-folder-fill"></i>
                                    </div>
                                    <div class="cat-name-details">
                                        <span class="cat-name-primary">${escapeHtml(data || '-')}</span>
                                        <span class="cat-name-badge">Kategori Produk</span>
                                    </div>
                                </div>
                            `;
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | 3: DIBUAT OLEH
                    |--------------------------------------------------------------------------
                    */
                    {
                        data: 'created_by',
                        orderable: true,
                        className: 'cat-td-creator',
                        render: function(data, type, row) {
                            const creator = data ? data.trim() : 'Administrator';
                            const initials = getInitials(creator);
                            const avatarColor = getAvatarColor(creator);
                            return `
                                <div class="cat-user-badge">
                                    <span class="cat-user-avatar" style="background: ${avatarColor};">
                                        ${initials}
                                    </span>
                                    <div class="cat-user-info">
                                        <span class="cat-user-name">${escapeHtml(creator)}</span>
                                        <span class="cat-user-role">Administrator</span>
                                    </div>
                                </div>
                            `;
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | 4: DIBUAT PADA
                    |--------------------------------------------------------------------------
                    */
                    {
                        data: 'created_at',
                        orderable: true,
                        className: 'cat-td-date',
                        render: function(data) {
                            if (!data) {
                                return '<span class="text-muted">-</span>';
                            }

                            const date = new Date(data);
                            const formatted = date.toLocaleDateString('id-ID', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            });

                            return `
                                <span class="cat-date-chip">
                                    <i class="bi bi-calendar3"></i>
                                    <span>${formatted}</span>
                                </span>
                            `;
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | 5: AKSI
                    |--------------------------------------------------------------------------
                    */
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center cat-td-actions',
                        render: function(data, type, row) {
                            const safeName = escapeHtml(row.name || '');
                            return `
                                <div class="cat-action-group">
                                    <button
                                        type="button"
                                        class="cat-btn-action cat-btn-edit btn-edit-category"
                                        data-id="${data}"
                                        data-name="${safeName}"
                                        title="Edit Kategori">
                                        <i class="bi bi-pen"></i>
                                    </button>
                                    <button
                                        type="button"
                                        class="cat-btn-action cat-btn-del btn-delete-category"
                                        data-id="${data}"
                                        data-name="${safeName}"
                                        title="Hapus Kategori">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],

                columnDefs: [],

                pageLength: 10,

                layout: {
                    topStart: null,
                    topEnd: null,
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },

                language: {
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ kategori',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 kategori',
                    zeroRecords: 'Data kategori tidak ditemukan',
                    processing: 'Memuat data...'
                }
            });

            // Update total badge dan checklist saat data digambar ulang
            categoryTable.on('draw', function() {
                const info = categoryTable.page.info();
                const totalEl = document.getElementById('catTotalCount');
                if (totalEl) {
                    totalEl.textContent = `${info.recordsTotal.toLocaleString('id-ID')} Total Kategori`;
                }
                updateSelectionUI();
            });

            // Listener Pencarian Kustom (Debounce 250ms)
            const customSearchInput = document.getElementById('catCustomSearch');
            const clearSearchBtn = document.getElementById('catClearSearch');
            let searchDebounceTimer;

            if (customSearchInput) {
                customSearchInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    if (clearSearchBtn) {
                        clearSearchBtn.style.display = query.length > 0 ? 'flex' : 'none';
                    }
                    clearTimeout(searchDebounceTimer);
                    searchDebounceTimer = setTimeout(function() {
                        categoryTable.search(query).draw();
                    }, 250);
                });
            }

            if (clearSearchBtn) {
                clearSearchBtn.addEventListener('click', function() {
                    if (customSearchInput) {
                        customSearchInput.value = '';
                        this.style.display = 'none';
                        categoryTable.search('').draw();
                        customSearchInput.focus();
                    }
                });
            }

            // Inisialisasi Select2 pada filter & page length
            if (typeof $.fn.select2 !== 'undefined') {
                $('#filterCreator').select2({
                    minimumResultsForSearch: 6,
                    width: '100%'
                });

                $('#filterPeriod').select2({
                    minimumResultsForSearch: -1,
                    width: '100%'
                });

                $('#catPageLength').select2({
                    minimumResultsForSearch: -1,
                    width: '76px'
                });

                $('#filterCreator').on('change', function() {
                    selectedCategoryIds.clear();
                    categoryTable.ajax.reload();
                });

                $('#filterPeriod').on('change', function() {
                    selectedCategoryIds.clear();
                    categoryTable.ajax.reload();
                });

                $('#catPageLength').on('change', function() {
                    categoryTable.page.len(Number(this.value)).draw();
                });

                // Sinkronisasi status open/close pada wrapper agar icon berubah warna secara mulus
                $('#filterCreator, #filterPeriod').on('select2:open', function() {
                    $(this).closest('.cat-select-box').addClass('is-open');
                }).on('select2:close', function() {
                    $(this).closest('.cat-select-box').removeClass('is-open');
                });

                // Klik langsung pada icon membuka dropdown Select2
                $(document).on('click', '.cat-select-icon', function(e) {
                    e.preventDefault();
                    const select = $(this).closest('.cat-select-box').find('select.select2');
                    if (select.length) {
                        select.select2('open');
                    }
                });
            } else {
                // Fallback jika select2 belum siap
                const filterCreator = document.getElementById('filterCreator');
                const filterPeriod = document.getElementById('filterPeriod');
                const catPageLength = document.getElementById('catPageLength');

                if (filterCreator) {
                    filterCreator.addEventListener('change', function() {
                        selectedCategoryIds.clear();
                        categoryTable.ajax.reload();
                    });
                }

                if (filterPeriod) {
                    filterPeriod.addEventListener('change', function() {
                        selectedCategoryIds.clear();
                        categoryTable.ajax.reload();
                    });
                }

                if (catPageLength) {
                    catPageLength.addEventListener('change', function() {
                        categoryTable.page.len(Number(this.value)).draw();
                    });
                }
            }

            // Listener Reset Filter
            const btnResetFilters = document.getElementById('btnResetFilters');
            if (btnResetFilters) {
                btnResetFilters.addEventListener('click', function() {
                    if (customSearchInput) customSearchInput.value = '';
                    if (clearSearchBtn) clearSearchBtn.style.display = 'none';

                    if (typeof $.fn.select2 !== 'undefined') {
                        $('#filterCreator').val('all').trigger('change.select2');
                        $('#filterPeriod').val('all').trigger('change.select2');
                        $('#catPageLength').val('10').trigger('change.select2');
                    } else {
                        const filterCreator = document.getElementById('filterCreator');
                        const filterPeriod = document.getElementById('filterPeriod');
                        const catPageLength = document.getElementById('catPageLength');
                        if (filterCreator) filterCreator.value = 'all';
                        if (filterPeriod) filterPeriod.value = 'all';
                        if (catPageLength) catPageLength.value = '10';
                    }

                    selectedCategoryIds.clear();
                    categoryTable.search('');
                    categoryTable.page.len(10);
                    categoryTable.ajax.reload();
                });
            }

            // Listener Select All Checkbox
            document.addEventListener('change', function(e) {
                if (e.target && e.target.id === 'checkAllCategories') {
                    const isChecked = e.target.checked;
                    const rowCheckboxes = document.querySelectorAll('.cat-row-check');
                    rowCheckboxes.forEach(cb => {
                        const id = Number(cb.value);
                        if (isChecked) {
                            selectedCategoryIds.add(id);
                        } else {
                            selectedCategoryIds.delete(id);
                        }
                    });
                    updateSelectionUI();
                }
            });

            // Listener Individual Row Checkbox
            document.addEventListener('change', function(e) {
                if (e.target && e.target.classList.contains('cat-row-check')) {
                    const id = Number(e.target.value);
                    if (e.target.checked) {
                        selectedCategoryIds.add(id);
                    } else {
                        selectedCategoryIds.delete(id);
                    }
                    updateSelectionUI();
                }
            });

            // Listener Batal Pilihan
            const btnBatalPilihan = document.getElementById('btnBatalPilihan');
            if (btnBatalPilihan) {
                btnBatalPilihan.addEventListener('click', function() {
                    selectedCategoryIds.clear();
                    updateSelectionUI();
                });
            }

            // Bulk Delete Modal Handlers
            const btnBulkDelete = document.getElementById('btnBulkDelete');
            const modalBulkDelete = document.getElementById('modalBulkDeleteKategori');
            const btnTutupBulkDelete = document.getElementById('btnTutupBulkDeleteModal');
            const btnBatalBulkDelete = document.getElementById('btnBatalBulkDelete');
            const btnConfirmBulkDelete = document.getElementById('btnConfirmBulkDelete');
            const bulkDeleteDesc = document.getElementById('bulkDeleteDescription');

            function bukaBulkDeleteModal() {
                if (selectedCategoryIds.size === 0) return;
                if (bulkDeleteDesc) {
                    bulkDeleteDesc.innerHTML = `Apakah Anda yakin ingin menghapus <strong>${selectedCategoryIds.size} kategori</strong> yang dipilih? Data yang sudah dihapus tidak dapat dikembalikan.`;
                }
                modalBulkDelete.classList.add('is-open');
            }

            function tutupBulkDeleteModal() {
                if (modalBulkDelete) {
                    modalBulkDelete.classList.remove('is-open');
                }
            }

            if (btnBulkDelete) btnBulkDelete.addEventListener('click', bukaBulkDeleteModal);
            if (btnTutupBulkDelete) btnTutupBulkDelete.addEventListener('click', tutupBulkDeleteModal);
            if (btnBatalBulkDelete) btnBatalBulkDelete.addEventListener('click', tutupBulkDeleteModal);
            if (modalBulkDelete) {
                modalBulkDelete.addEventListener('click', function(e) {
                    if (e.target === modalBulkDelete) tutupBulkDeleteModal();
                });
            }
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modalBulkDelete && modalBulkDelete.classList.contains('is-open')) {
                    tutupBulkDeleteModal();
                }
            });

            if (btnConfirmBulkDelete) {
                btnConfirmBulkDelete.addEventListener('click', async function() {
                    if (selectedCategoryIds.size === 0) return;

                    const originalHtml = btnConfirmBulkDelete.innerHTML;
                    btnConfirmBulkDelete.disabled = true;
                    btnConfirmBulkDelete.innerHTML = '<i class="bi bi-arrow-repeat spin-icon me-1"></i> Menghapus...';

                    try {
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                        const response = await fetch("{{ route('admin_bulk_destroy_categories') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ids: Array.from(selectedCategoryIds)
                            })
                        });

                        const result = await response.json();

                        if (response.ok && result.success) {
                            tutupBulkDeleteModal();
                            selectedCategoryIds.clear();
                            updateSelectionUI();
                            categoryTable.ajax.reload(null, false);
                            showToast('success', result.message || 'Kategori terpilih berhasil dihapus.');
                        } else {
                            showToast('error', result.message || 'Gagal menghapus kategori terpilih.');
                        }
                    } catch (err) {
                        console.error('Bulk delete error:', err);
                        showToast('error', 'Terjadi kesalahan sistem saat menghapus data.');
                    } finally {
                        btnConfirmBulkDelete.disabled = false;
                        btnConfirmBulkDelete.innerHTML = originalHtml;
                    }
                });
            }

        });

        document.addEventListener('DOMContentLoaded', function() {

            let categoryIdToDelete = null;


            const modalDeleteKategori = document.getElementById(
                'modalDeleteKategori'
            );

            const btnTutupDeleteModal = document.getElementById(
                'btnTutupDeleteModal'
            );

            const btnBatalDelete = document.getElementById(
                'btnBatalDelete'
            );

            const btnConfirmDelete = document.getElementById(
                'btnConfirmDelete'
            );

            btnConfirmDelete.addEventListener('click', async function() {

                if (!categoryIdToDelete) {
                    return;
                }

                try {

                    const response = await fetch(
                        `/admin/categories/${categoryIdToDelete}`, {
                            method: 'DELETE',

                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).getAttribute('content'),

                                'Accept': 'application/json'
                            }
                        }
                    );


                    const result = await response.json();


                    if (response.ok && result.success) {

                        // Tutup modal
                        tutupDeleteModal();

                        // Bersihkan dari daftar terpilih jika ada
                        if (selectedCategoryIds.has(Number(categoryIdToDelete))) {
                            selectedCategoryIds.delete(Number(categoryIdToDelete));
                            updateSelectionUI();
                        }

                        // Reload DataTable
                        categoryTable.ajax.reload(null, false);
                        showToast('success', result.message);

                    } else {

                        showToast(
                            'error',
                            result.message || 'Kategori gagal dihapus.'
                        );

                    }

                } catch (error) {

                    console.error(error);

                    alert('Terjadi kesalahan saat menghapus kategori.');

                }

            });


            /* =====================================================
               BUKA MODAL DELETE
            ===================================================== */

            document.addEventListener('click', function(event) {

                const deleteButton = event.target.closest(
                    '.btn-delete-category'
                );

                if (!deleteButton) {
                    return;
                }


                categoryIdToDelete = deleteButton.dataset.id;


                modalDeleteKategori.classList.add('is-open');

            });


            /* =====================================================
               FUNCTION TUTUP MODAL
            ===================================================== */

            function tutupDeleteModal() {

                modalDeleteKategori.classList.remove('is-open');

                categoryIdToDelete = null;

            }


            /* =====================================================
               TOMBOL X
            ===================================================== */

            btnTutupDeleteModal.addEventListener(
                'click',
                tutupDeleteModal
            );


            /* =====================================================
               TOMBOL BATAL
            ===================================================== */

            btnBatalDelete.addEventListener(
                'click',
                tutupDeleteModal
            );


            /* =====================================================
               KLIK BACKGROUND
            ===================================================== */

            modalDeleteKategori.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalDeleteKategori) {

                        tutupDeleteModal();

                    }

                }
            );


            /* =====================================================
               TOMBOL ESC
            ===================================================== */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modalDeleteKategori.classList.contains('is-open')
                    ) {

                        tutupDeleteModal();

                    }

                }
            );
        });

        document.addEventListener('DOMContentLoaded', function() {

            const modalEdit = document.getElementById('modalEditKategori');

            const btnTutupEdit = document.getElementById('btnTutupEditModal');

            const btnBatalEdit = document.getElementById('btnBatalEditKategori');

            const inputEditName = document.getElementById('editCategoryName');


            /*
            |--------------------------------------------------------------------------
            | ID KATEGORI YANG SEDANG DIEDIT
            |--------------------------------------------------------------------------
            */

            let editCategoryId = null;


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL EDIT
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const editButton = event.target.closest('.btn-edit-category');

                if (!editButton) {
                    return;
                }


                editCategoryId = editButton.dataset.id;

                const categoryName = editButton.dataset.name;


                // Masukkan nama kategori lama

                inputEditName.value = categoryName;


                // Buka modal

                modalEdit.classList.add('is-open');


                // Fokus ke input

                setTimeout(function() {

                    inputEditName.focus();

                }, 200);

            });


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupEditModal() {

                modalEdit.classList.remove('is-open');

                editCategoryId = null;

                inputEditName.value = '';

            }


            /*
            |--------------------------------------------------------------------------
            | TOMBOL CLOSE
            |--------------------------------------------------------------------------
            */

            btnTutupEdit.addEventListener('click', tutupEditModal);


            /*
            |--------------------------------------------------------------------------
            | TOMBOL BATAL
            |--------------------------------------------------------------------------
            */

            btnBatalEdit.addEventListener('click', tutupEditModal);


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modalEdit.addEventListener('click', function(event) {

                if (event.target === modalEdit) {

                    tutupEditModal();

                }

            });


            /*
            |--------------------------------------------------------------------------
            | TOMBOL ESC
            |--------------------------------------------------------------------------
            */

            document.addEventListener('keydown', function(event) {

                if (event.key === 'Escape') {

                    if (modalEdit.classList.contains('is-open')) {

                        tutupEditModal();

                    }

                }

            });

            /*
            |--------------------------------------------------------------------------
            | SUBMIT UPDATE KATEGORI
            |--------------------------------------------------------------------------
            */

            const formEditKategori = document.getElementById('formEditKategori');


            formEditKategori.addEventListener('submit', async function(event) {

                event.preventDefault();


                const categoryName = inputEditName.value.trim();


                /*
                |--------------------------------------------------------------------------
                | VALIDASI FRONTEND
                |--------------------------------------------------------------------------
                */

                if (!categoryName) {

                    showToast(
                        'error',
                        'Nama kategori tidak boleh kosong.'
                    );

                    inputEditName.focus();

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | KIRIM REQUEST UPDATE
                |--------------------------------------------------------------------------
                */

                try {

                    const response = await fetch(
                        `/admin/categories/${editCategoryId}`, {

                            method: 'PUT',

                            headers: {

                                'Content-Type': 'application/json',

                                'Accept': 'application/json',

                                'X-CSRF-TOKEN': document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')

                            },

                            body: JSON.stringify({

                                name: categoryName

                            })

                        }
                    );


                    const result = await response.json();


                    /*
                    |--------------------------------------------------------------------------
                    | JIKA GAGAL
                    |--------------------------------------------------------------------------
                    */

                    if (!response.ok) {

                        throw new Error(
                            result.message || 'Terjadi kesalahan.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | JIKA BERHASIL
                    |--------------------------------------------------------------------------
                    */

                    if (result.success) {

                        // Tutup modal

                        tutupEditModal();


                        // Reload DataTable TANPA reload halaman

                        categoryTable.ajax.reload(null, false);


                        // Toast berhasil

                        showToast('success', result.message);

                    }

                } catch (error) {

                    showToast(
                        'error',
                        error.message
                    );

                }

            });

        });

        document.addEventListener('DOMContentLoaded', function() {

            const btnExportKategori = document.getElementById('btnExportKategori');


            btnExportKategori.addEventListener('click', function() {

                window.location.href = "{{ route('admin_export_category') }}";

            });

        });

        document.addEventListener('DOMContentLoaded', function() {
            const btnSaveImport = document.getElementById('btnSaveImportKategori');

            btnSaveImport.addEventListener('click', function() {

                const fileInput = document.getElementById('fileImportKategori');


                // Pastikan file dipilih

                if (!fileInput.files.length) {

                    alert('Silakan pilih file Excel terlebih dahulu.');

                    return;

                }


                // Buat FormData

                const formData = new FormData();

                formData.append('file', fileInput.files[0]);


                // Kirim ke Laravel

                fetch("{{ route('admin_import_category') }}", {

                        method: 'POST',

                        headers: {

                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]'
                            ).getAttribute('content'),

                        },

                        body: formData,

                    })

                    .then(response => response.text())

                    .then(data => {

                        console.log(data);

                    })

                    .catch(error => {

                        console.error(error);

                    });

            });
        });
    </script>
@endsection

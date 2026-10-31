@extends('admin_master')

@section('contents')
    <style>
        /* =========================================================
           RESPONSIVE GRID SYSTEM (KONSISTEN DENGAN DIGITAL PRINT A3+)
        ========================================================= */
        .lf-estimator-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            width: 100%;
        }

        .lf-form-col {
            min-width: 0;
            width: 100%;
        }

        .lf-summary-col {
            min-width: 0;
            width: 100%;
        }

        @media (min-width: 1024px) {
            .lf-estimator-grid {
                grid-template-columns: 1.25fr 1fr;
                gap: 24px;
                align-items: start;
            }

            .lf-sticky-card {
                position: sticky;
                top: 20px;
            }
        }
        /* =========================================================
           PRICE LIST LARGE FORMAT MATRIX COMPONENT
        ========================================================= */
        .lf-matrix-col {
            grid-column: 1 / -1;
            min-width: 0;
            width: 100%;
            margin-top: 16px;
        }

        /* Matrix Card & Header Styling */
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

        .matrix-head-title-wrap {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            width: 100%;
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
           SOLID, OPAQUE & PERFECTLY ALIGNED LARGE FORMAT TABLE (NO OVERLAP / TRANSPARENCY)
        ========================================================= */
        #lfPriceListTable {
            border-collapse: separate !important;
            border-spacing: 0 !important;
            width: max-content;
            min-width: 100%;
        }

        /* Base Header Cell Styling */
        #lfPriceListTable thead th {
            box-sizing: border-box !important;
            white-space: nowrap !important;
            border-right: 1px solid var(--border) !important;
            border-bottom: 1px solid var(--border) !important;
            vertical-align: middle !important;
            font-size: 11.5px;
        }

        /* 1. Header Row 1 (Group Row: Biaya Produksi & Rekomendasi Harga Jual) - 38px Solid */
        #lfPriceListTable thead tr:nth-child(1) th {
            position: sticky !important;
            top: 0 !important;
            height: 38px !important;
            max-height: 38px !important;
            line-height: 38px !important;
            padding: 0 12px !important;
            background-color: #172033 !important;
            color: #f1f5f9 !important;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            z-index: 10 !important;
        }

        :root[data-theme="light"] #lfPriceListTable thead tr:nth-child(1) th {
            background-color: #e2e8f0 !important;
            color: #1e293b !important;
            border-bottom: 1px solid #cbd5e1 !important;
        }

        /* 2. Header Row 2 (Sub-Headers) - Pinned at top: 38px exactly, 36px Solid */
        #lfPriceListTable thead tr:nth-child(2) th {
            position: sticky !important;
            top: 38px !important;
            height: 36px !important;
            max-height: 36px !important;
            line-height: 36px !important;
            padding: 0 12px !important;
            background-color: #121826 !important;
            color: #94a3b8 !important;
            font-weight: 600;
            z-index: 10 !important;
        }

        :root[data-theme="light"] #lfPriceListTable thead tr:nth-child(2) th {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border-bottom: 1px solid #cbd5e1 !important;
        }

        /* 3. Rowspan=2 Cells (No, Item Material, Ukuran Lebar) - 74px Solid spanning row 1 & 2 */
        #lfPriceListTable thead th[rowspan="2"] {
            top: 0 !important;
            height: 74px !important;
            max-height: 74px !important;
            line-height: normal !important;
            vertical-align: middle !important;
            z-index: 15 !important;
            background-color: #172033 !important;
            color: #f8fafc !important;
        }

        :root[data-theme="light"] #lfPriceListTable thead th[rowspan="2"] {
            background-color: #e2e8f0 !important;
            color: #0f172a !important;
        }

        /* 4. Left Sticky Columns (No & Item Material) */
        #lfPriceListTable .details-digital-col-no {
            left: 0 !important;
            width: 44px;
            min-width: 44px;
            text-align: center;
        }

        #lfPriceListTable .details-digital-col-material {
            left: 44px !important;
            min-width: 220px;
            max-width: 280px;
            text-align: left;
            box-shadow: 3px 0 6px -2px rgba(0, 0, 0, 0.25);
        }

        #lfPriceListTable thead th.details-digital-sticky {
            z-index: 20 !important;
        }

        #lfPriceListTable tbody td.details-digital-sticky {
            position: sticky !important;
            z-index: 5 !important;
            background-color: var(--bg-card) !important;
        }

        #lfPriceListTable tbody tr:hover td.details-digital-sticky {
            background-color: var(--bg-hover) !important;
        }

        /* 5. 100% Solid Opaque Colors for Selling Price Headers (NO TRANSPARENCY / BLEEDING) */
        /* Harga Umum Header (Amber) */
        #lfPriceListTable thead th.col-price-general {
            background-color: #2b1f08 !important;
            color: #fbbf24 !important;
            font-weight: 700;
            border-bottom: 2px solid #f59e0b !important;
        }
        :root[data-theme="light"] #lfPriceListTable thead th.col-price-general {
            background-color: #fef3c7 !important;
            color: #92400e !important;
            border-bottom: 2px solid #d97706 !important;
        }

        /* Harga Divisi Header (Violet) */
        #lfPriceListTable thead th.col-price-division {
            background-color: #1e172e !important;
            color: #c4b5fd !important;
            font-weight: 700;
            border-bottom: 2px solid #8b5cf6 !important;
        }
        :root[data-theme="light"] #lfPriceListTable thead th.col-price-division {
            background-color: #ede9fe !important;
            color: #5b21b6 !important;
            border-bottom: 2px solid #7c3aed !important;
        }

        /* Harga Polos Header (Sky Cyan) */
        #lfPriceListTable thead th.col-price-plain {
            background-color: #0f2231 !important;
            color: #7dd3fc !important;
            font-weight: 700;
            border-bottom: 2px solid #0ea5e9 !important;
        }
        :root[data-theme="light"] #lfPriceListTable thead th.col-price-plain {
            background-color: #e0f2fe !important;
            color: #0369a1 !important;
            border-bottom: 2px solid #0284c7 !important;
        }

        /* 6. Body Cells Color Coding for Selling Prices */
        #lfPriceListTable td.details-digital-price {
            font-variant-numeric: tabular-nums;
            font-weight: 700;
        }

        #lfPriceListTable td.details-digital-cost {
            font-variant-numeric: tabular-nums;
        }

        #lfPriceListTable tbody td.col-price-general {
            color: #fbbf24 !important;
            font-weight: 700;
        }
        :root[data-theme="light"] #lfPriceListTable tbody td.col-price-general {
            color: #b45309 !important;
        }

        #lfPriceListTable tbody td.col-price-division {
            color: #c4b5fd !important;
            font-weight: 700;
        }
        :root[data-theme="light"] #lfPriceListTable tbody td.col-price-division {
            color: #6d28d9 !important;
        }

        #lfPriceListTable tbody td.col-price-plain {
            color: #7dd3fc !important;
            font-weight: 700;
        }
        :root[data-theme="light"] #lfPriceListTable tbody td.col-price-plain {
            color: #0284c7 !important;
        }

        #lfPriceListTable tbody tr:hover td {
            background-color: rgba(37, 99, 235, 0.05) !important;
        }

        /* Large Format Width Badges */
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

        /* RESPONSIVE MEDIA QUERIES FOR LARGE FORMAT MATRIX */
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

            .matrix-toolbar-row-sub {
                gap: 12px;
            }
        }

        @media (max-width: 640px) {
            .matrix-card-head {
                padding: 14px 12px;
            }

            .matrix-head-icon {
                width: 38px;
                height: 38px;
                font-size: 17px;
            }

            .matrix-title {
                font-size: 1.05rem;
            }

            .matrix-subtitle {
                font-size: 11.5px;
            }

            .matrix-toolbar-row-main {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }

            .matrix-filter-group {
                display: flex;
                justify-content: space-between;
                width: 100%;
            }

            .matrix-cat-select-wrap,
            .matrix-pagesize-select-wrap {
                width: 160px;
            }

            .matrix-search-box {
                width: 100%;
                order: unset;
            }

            .matrix-toolbar-row-sub {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }

            .matrix-tab-cluster {
                display: flex;
                flex-direction: column;
                align-items: flex-start;
                gap: 4px;
                width: 100%;
            }

            .matrix-tab-container {
                width: 100%;
                display: flex;
            }

            .matrix-tab-btn {
                flex: 1;
                justify-content: center;
                padding: 5px 6px;
                font-size: 11px;
            }

            .matrix-card-footer {
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 10px;
            }

            #priceListInfo {
                text-align: center;
                width: 100%;
            }

            #priceListPagination {
                justify-content: center;
                width: 100%;
            }
        }

        /* Selling Price Box - Konsisten dengan A3+ */
        .selling-price-box {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(37, 99, 235, 0.02) 100%);
            border: 1.5px solid rgba(37, 99, 235, 0.28);
            border-radius: 12px;
            padding: 18px;
            margin-bottom: 20px;
        }

        .selling-price-box-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--primary);
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .selling-price-box-val {
            font-size: 28px;
            font-weight: 800;
            color: var(--t-base);
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .selling-price-box-sub {
            display: flex;
            gap: 16px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed var(--border);
            font-size: 13px;
        }

        .selling-price-box-sub .sub-label {
            color: var(--t-muted);
            font-size: 11.5px;
            display: block;
        }

        .selling-price-box-sub .sub-val {
            color: var(--t-base);
            font-weight: 700;
        }

        .sub-divider {
            padding-left: 16px;
            border-left: 1px solid var(--border);
        }

        .rounding-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--bg-muted);
            color: var(--t-muted);
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid var(--border);
        }

        /* Breakdown Rows */
        .breakdown-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding: 10px 12px;
            background: var(--bg-muted);
            border-radius: 8px;
            border: 1px solid var(--border);
            gap: 10px;
        }

        .breakdown-row-title {
            font-weight: 700;
            color: var(--t-base);
            font-size: 12.5px;
        }

        .breakdown-row-sub {
            color: var(--t-muted);
            font-size: 11.5px;
            display: block;
            margin-top: 2px;
        }

        .breakdown-row-cost {
            font-weight: 700;
            color: var(--t-base);
            font-size: 13px;
            white-space: nowrap;
        }

        /* Total HPP Bar */
        .total-hpp-bar {
            padding: 12px 14px;
            background: var(--bg-card);
            border-radius: 8px;
            border: 1.5px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 4px;
        }

        .total-hpp-bar-title {
            font-size: 12px;
            font-weight: 800;
            color: var(--t-base);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .total-hpp-bar-sub {
            font-size: 11.5px;
            color: var(--t-muted);
            margin-top: 2px;
        }

        .total-hpp-bar-val {
            font-size: 15px;
            font-weight: 800;
            color: var(--t-base);
        }

        .btn-back-header {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 8px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--t-base);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-back-header:hover {
            background: var(--bg-hover);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Modal Custom Styles */
        .modal-overlay {
            position: fixed !important;
            inset: 0 !important;
            display: none;
            align-items: center !important;
            justify-content: center !important;
            background: rgba(11, 17, 32, 0.75) !important;
            backdrop-filter: blur(4px) !important;
            z-index: 99999 !important;
            padding: 20px !important;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease !important;
        }

        .modal-overlay.is-open {
            display: flex !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .modal-content-custom {
            background: var(--bg-card);
            color: var(--t-base);
            border: 1px solid var(--border);
            border-radius: 12px;
            width: 100%;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
            overflow: hidden;
        }

        .modal-head-custom {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--bg-card);
        }

        .modal-title-custom {
            margin: 2px 0 0;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--t-base);
        }

        .modal-close-btn {
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 20px;
            color: var(--t-muted);
            transition: color 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
            border-radius: 6px;
        }

        .modal-close-btn:hover {
            color: var(--t-base);
            background: var(--bg-hover);
        }

        .modal-foot-custom {
            padding: 14px 20px;
            background: var(--bg-muted);
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-radius: 0 0 12px 12px;
        }

        /* Printable Area */
        .printable-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--border);
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .printable-company-name {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--t-base);
        }

        .printable-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 18px;
        }

        .printable-info-box {
            background: var(--bg-muted);
            padding: 12px 14px;
            border-radius: 6px;
            border: 1px solid var(--border);
            color: var(--t-base);
            font-size: 12.5px;
            line-height: 1.6;
        }

        .printable-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .printable-table thead tr {
            background: var(--bg-muted);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            color: var(--t-base);
        }

        .printable-table tbody tr {
            border-bottom: 1px solid var(--border);
            color: var(--t-base);
        }

        .printable-table tfoot tr {
            border-top: 2px solid var(--border);
            font-size: 14px;
            color: var(--t-base);
        }

        /* Detail Teknis Grid & Badges */
        .tech-spec-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .tech-spec-item {
            background: var(--bg-muted);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 10px 12px;
        }

        .tech-spec-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--t-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .tech-spec-value {
            font-size: 13px;
            font-weight: 700;
            color: var(--t-base);
            word-break: break-word;
        }

        .tech-kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
            margin-top: 18px;
        }

        .tech-kpi-card {
            background: var(--bg-muted);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 12px 14px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .tech-kpi-card.highlight {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(37, 99, 235, 0.02) 100%);
            border-color: rgba(37, 99, 235, 0.3);
        }

        .tech-kpi-card.profit {
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.06) 0%, rgba(37, 99, 235, 0.02) 100%);
            border-color: rgba(37, 99, 235, 0.25);
        }

        .tech-kpi-label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--t-muted);
            margin-bottom: 4px;
        }

        .tech-kpi-val {
            font-size: 17px;
            font-weight: 800;
            color: var(--t-base);
        }

        /* Buttons Hover */
        .btn-preview-estimation:hover {
            opacity: 0.92;
            transform: translateY(-1px);
        }

        .btn-detail-estimation:hover {
            background: var(--bg-hover) !important;
            border-color: var(--primary) !important;
            color: var(--primary) !important;
            transform: translateY(-1px);
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .printable-info-grid {
                grid-template-columns: 1fr;
            }

            .printable-header {
                flex-direction: column;
                gap: 8px;
            }

            .tech-kpi-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        /* Media Print Rules */
        @media print {
            body {
                background: #fff !important;
                color: #000 !important;
            }

            .shell,
            .hero,
            nav,
            header,
            footer,
            aside,
            #modalLFTechnical {
                display: none !important;
            }

            #modalLFQuotation.modal-overlay {
                display: block !important;
                position: static !important;
                background: transparent !important;
                padding: 0 !important;
                opacity: 1 !important;
                visibility: visible !important;
                backdrop-filter: none !important;
            }

            #modalLFQuotation .modal-head-custom,
            #modalLFQuotation .modal-foot-custom {
                display: none !important;
            }

            #modalLFQuotation .modal-content-custom {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                color: #000 !important;
            }

            #printableEstimationArea {
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
                color: #000 !important;
                background: #fff !important;
            }

            #printableEstimationArea * {
                color: #000 !important;
                background: transparent !important;
            }

            .printable-info-box {
                border: 1px solid #d1d5db !important;
                background: #f9fafb !important;
            }

            .printable-table,
            .printable-table th,
            .printable-table td {
                border: 1px solid #d1d5db !important;
            }

            .printable-table thead tr {
                background: #f3f4f6 !important;
            }
        }

        .spin {
            display: inline-block;
            animation: lfspin 1s linear infinite;
        }
        @keyframes lfspin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

    <div class="shell">

        <div data-shell-sidebar></div>

        <div class="main">

            <div data-shell-topbar></div>

            <main class="content">

                {{-- HERO --}}
                <section class="hero"
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <div class="hero-text" style="flex: 1; min-width: 280px;">
                        <span class="eyebrow">
                            ESTIMASI PRODUKSI LARGE FORMAT
                        </span>
                        <h1 class="hero-title" style="margin: 6px 0 8px;">
                            Buat Estimasi Large Format
                        </h1>
                        <p class="hero-sub" style="margin: 0;">
                            Kalkulasi cepat dan presisi untuk spanduk, banner, sticker outdoor/indoor, display, dan konfigurasi bahan roll dengan simulasi layout baris serta finishing.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('admin_home') }}" class="btn-back-header">
                            <i class="bi bi-arrow-left"></i>
                            <span>Kembali</span>
                        </a>
                    </div>
                </section>

                <div class="lf-estimator-grid">

                    {{-- =========================================================
                        KOLOM KIRI: FORM ESTIMASI LARGE FORMAT
                    ========================================================== --}}
                    <div class="lf-form-col">
                        <section class="card" style="margin-bottom: 24px;">

                            {{-- CARD HEADER --}}
                            <div class="card-head">
                                <div class="card-title-wrap">
                                    <span class="eyebrow">
                                        FORMULIR SPESIFIKASI LARGE FORMAT
                                    </span>
                                </div>
                            </div>

                            {{-- FORM ESTIMASI LF --}}
                            <form action="{{ route('admin_store_lf') }}" method="POST" id="largeFormatEstimationForm">
                                @csrf

                                {{-- Hidden inputs custom size & display identity --}}
                                <input type="hidden" name="material_price_size_id" id="material_price_size_id">
                                <input type="hidden" name="lamination_price_size_id" id="lamination_price_size_id">
                                <input type="hidden" name="display_configuration_id" id="display_configuration_id">
                                <input type="hidden" name="display_product_id" id="display_product_id">
                                <input type="hidden" name="display_component_id" id="display_component_id">
                                <input type="hidden" name="display_component_type" id="display_component_type">

                                {{-- =========================================================
                                    1. INFORMASI PELANGGAN & LOKASI
                                ========================================================== --}}
                                <div class="form-section">
                                    <div class="form-section-head">
                                        <span class="eyebrow" style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-person-badge"></i> 1. INFORMASI PELANGGAN &amp; LOKASI
                                        </span>
                                    </div>

                                    <div class="form-grid">
                                        {{-- Lokasi --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="location_id">
                                                Lokasi
                                                <span class="req">*</span>
                                            </label>

                                            <select id="location_id" name="location_id" class="select select2" required>
                                                <option value="">Pilih Lokasi</option>
                                                @foreach ($locations as $location)
                                                    <option value="{{ $location->id }}"
                                                        {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                                        {{ $location->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Vendor (Khusus Outsourcing) --}}
                                        <div class="field" id="vendorField" style="margin-bottom: 20px; display: none;">
                                            <label class="field-label" for="vendor_id">
                                                Vendor
                                                <span class="req">*</span>
                                            </label>

                                            <select id="vendor_id" name="vendor_id" class="select select2">
                                                <option value="">Pilih Vendor</option>
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}">
                                                        {{ $vendor->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Pilihan Harga --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="price_type">
                                                Pilihan Harga
                                                <span class="req">*</span>
                                            </label>

                                            <select id="price_type" name="price_type" class="select select2" required>
                                                <option value="general" selected>Harga Umum</option>
                                                <option value="division">Harga Divisi</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Informasi Pelanggan (Untuk Surat Penawaran) --}}
                                    <div class="form-grid">
                                        {{-- Nama Pelanggan --}}
                                        <div class="field" style="margin-bottom: 16px;">
                                            <label class="field-label" for="customer_name">
                                                Nama Pelanggan / Perusahaan (Opsional)
                                            </label>
                                            <input type="text" id="customer_name" name="customer_name" class="input"
                                                placeholder="Contoh: PT. Sumber Makmur / Bpk. Hendra">
                                        </div>

                                        {{-- No. WhatsApp / Telepon --}}
                                        <div class="field" style="margin-bottom: 16px;">
                                            <label class="field-label" for="customer_phone">
                                                No. WhatsApp / Telepon (Opsional)
                                            </label>
                                            <input type="text" id="customer_phone" name="customer_phone" class="input"
                                                placeholder="Contoh: 08123456789">
                                        </div>
                                    </div>

                                    {{-- Catatan Khusus --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="customer_notes">
                                            Catatan Khusus Penawaran / Produksi (Opsional)
                                        </label>
                                        <input type="text" id="customer_notes" name="customer_notes" class="input"
                                            placeholder="Contoh: Mata ayam 4 sudut, lebihan bahan keliling 2 cm...">
                                    </div>
                                </div>

                                {{-- =========================================================
                                    2. SPESIFIKASI CETAK LARGE FORMAT
                                ========================================================== --}}
                                <div class="form-section">
                                    <div class="form-section-head">
                                        <span class="eyebrow" style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-aspect-ratio"></i> 2. SPESIFIKASI CETAK LARGE FORMAT
                                        </span>
                                    </div>

                                    <div class="form-grid">
                                        {{-- Kategori --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="category_id">
                                                Kategori
                                                <span class="req">*</span>
                                            </label>

                                            <select id="category_id" name="category_id" class="select select2" required>
                                                <option value="">Pilih Kategori</option>
                                            </select>
                                        </div>

                                        {{-- Material --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="material_id">
                                                Material
                                                <span class="req">*</span>
                                            </label>

                                            <select name="material_id" id="material_id" class="select select2" required>
                                                <option value="">Pilih Material</option>
                                            </select>

                                            <small class="field-help" id="materialHelp">
                                                Pilih lokasi dan kategori terlebih dahulu.
                                            </small>
                                        </div>
                                    </div>

                                    {{-- Dimensi Panjang & Lebar (Hidden jika Display) --}}
                                    <div class="form-grid" id="dimensionFieldsGrid">
                                        {{-- Panjang --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="length">
                                                Panjang (cm)
                                                <span class="req">*</span>
                                            </label>

                                            <div class="input-suffix">
                                                <input id="length" name="length" class="input" type="number"
                                                    min="0.01" step="0.01" inputmode="decimal" placeholder="Contoh: 300"
                                                    value="{{ old('length') }}" required>
                                            </div>
                                        </div>

                                        {{-- Lebar --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="width">
                                                Lebar (cm)
                                                <span class="req">*</span>
                                            </label>

                                            <div class="input-suffix">
                                                <input id="width" name="width" class="input" type="number"
                                                    min="0.01" step="0.01" inputmode="decimal"
                                                    placeholder="Contoh: 150" value="{{ old('width') }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-grid">
                                        {{-- Quantity --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="qty">
                                                Jumlah (pcs)
                                                <span class="req">*</span>
                                            </label>

                                            <div class="input-suffix">
                                                <input id="qty" name="qty" class="input" type="number"
                                                    min="1" step="1" inputmode="numeric"
                                                    placeholder="Contoh: 2" value="{{ old('qty', 1) }}" required>
                                            </div>
                                        </div>

                                        {{-- Lebar Bahan Tersedia --}}
                                        <div class="field" id="materialSizeField" style="margin-bottom: 20px;">
                                            <label class="field-label" for="material_size_id">
                                                Lebar Bahan Tersedia
                                                <span class="req">*</span>
                                            </label>

                                            <select id="material_size_id" name="material_size_id" class="select select2" required>
                                                <option value="">Pilih Lebar Bahan</option>
                                            </select>

                                            {{-- Custom Input Lebar Bahan --}}
                                            <input type="number" id="material_custom_width" name="material_custom_width"
                                                class="form-control mt-2" placeholder="Masukkan lebar bahan custom (cm)"
                                                min="0.01" step="any" style="display: none;" disabled>

                                            <div class="field mt-2" id="materialPriceReferenceField" style="display: none;">
                                                <label class="field-label" for="material_price_reference_select">
                                                    Ukuran Referensi Harga Vendor <span class="req">*</span>
                                                </label>
                                                <select id="material_price_reference_select" class="select select2">
                                                    <option value=""></option>
                                                </select>
                                                <small class="field-help">Pilih ukuran vendor sebagai acuan harga untuk lebar custom.</small>
                                            </div>

                                            <small class="field-help" id="materialSizeHelp">
                                                Pilih material terlebih dahulu.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="form-grid">
                                        {{-- Laminasi --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="lamination_id">
                                                Laminasi
                                            </label>

                                            <select id="lamination_id" name="lamination_id" class="select select2">
                                                <option value="none">Tanpa Laminasi</option>
                                            </select>

                                            <small class="field-help">
                                                Pilih lokasi dan kategori terlebih dahulu.
                                            </small>
                                        </div>

                                        {{-- Ukuran Laminasi --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="lamination_size_id">
                                                Ukuran Laminasi
                                            </label>

                                            <select id="lamination_size_id" name="lamination_size_id" class="select select2">
                                                <option value="">Pilih Ukuran Laminasi</option>
                                            </select>

                                            {{-- Custom Input Lebar Laminasi --}}
                                            <input type="number" id="lamination_custom_width" name="lamination_custom_width"
                                                class="form-control mt-2" placeholder="Masukkan lebar laminasi custom (cm)"
                                                min="0.01" step="any" style="display: none;" disabled>

                                            <small class="field-help">
                                                Pilih laminasi terlebih dahulu.
                                            </small>
                                        </div>
                                    </div>
                                </div>

                                {{-- =========================================================
                                    3. KOMPONEN TAMBAHAN
                                ========================================================== --}}
                                <div class="form-section" style="margin-top: 20px; margin-bottom: 20px; display: flex; flex-direction: column; gap: 16px;">
                                    <div class="form-section-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                                        <div>
                                            <div class="form-section-title" style="font-size: 14px; font-weight: 700; color: var(--primary);">
                                                <i class="bi bi-puzzle"></i> 3. KOMPONEN TAMBAHAN
                                            </div>
                                            <div class="form-section-description eyebrow" style="font-size: 11.5px; color: var(--t-muted); margin-top: 2px;">
                                                Tambahkan komponen finishing, mata ayam, lem, selongsong, atau jasa pemasangan.
                                            </div>
                                        </div>

                                        <button type="button" class="btn btn--primary" id="addAdditionalComponentButton">
                                            <i class="bi bi-plus-lg"></i> Tambah Komponen
                                        </button>
                                    </div>

                                    <div id="additionalComponentsContainer">
                                        {{-- Komponen tambahan dinamis di-generate oleh JS --}}
                                    </div>
                                </div>

                                {{-- =========================================================
                                    4. DISKON & ESTIMASI WAKTU
                                ========================================================== --}}
                                <div class="form-section">
                                    <div class="form-section-head">
                                        <span class="eyebrow" style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-percent"></i> 4. DISKON &amp; ESTIMASI WAKTU
                                        </span>
                                    </div>

                                    <div class="field" style="margin-bottom: 20px;">
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

                                        {{-- Estimasi Waktu (Lead Time) --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="estimation_time">
                                                Estimasi Waktu Pengerjaan (Hari Kerja)
                                                <span class="req">*</span>
                                            </label>

                                            <div class="input-suffix">
                                                <input id="estimation_time" name="estimation_time" class="input"
                                                    type="number" min="1" step="1" inputmode="numeric"
                                                    placeholder="Contoh: 1" value="{{ old('estimation_time', 1) }}" required>
                                            </div>
                                            <small class="field-help">Ditampilkan pada ringkasan penawaran pelanggan.</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- =========================================================
                                    5. STATUS & RESET
                                ========================================================== --}}
                                <div class="form-actions" style="padding: 16px 20px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                                    <span class="badge" id="realtimeStatusBadge" style="background: rgba(37, 99, 235, 0.1); color: var(--primary); border: 1px solid rgba(37, 99, 235, 0.25);">
                                        <i class="bi bi-lightning-charge-fill" style="margin-right: 4px;"></i> Kalkulasi Otomatis Aktif
                                    </span>

                                    <div style="display: flex; gap: 8px;">
                                        <button type="button" class="btn btn--ghost" id="btnResetForm">
                                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                                        </button>
                                        <a href="{{ route('admin_home') }}" class="btn btn--ghost">
                                            Batal
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </section>
                    </div>

                    {{-- =========================================================
                        KOLOM KANAN: STICKY LIVE SUMMARY (KONSISTEN DENGAN DIGITAL PRINT A3+)
                    ========================================================== --}}
                    <div class="lf-summary-col">
                        <section class="card lf-sticky-card">

                            {{-- CARD HEAD DENGAN GRADASI BIRU IDENTIK DENGAN DIGITAL PRINT A3+ --}}
                            <div class="card-head"
                                style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color: #fff; padding: 18px 20px; border-radius: 13px 13px 0 0; margin-bottom: 0;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                                    <div>
                                        <span class="eyebrow" style="color: #93c5fd; font-size: 11px; letter-spacing: 0.8px;">
                                            HASIL KALKULASI REAL-TIME
                                        </span>
                                        <h2 style="font-size: 1.15rem; font-weight: 700; color: #ffffff; margin: 4px 0 0;">
                                            Ringkasan Estimasi Large Format
                                        </h2>
                                    </div>
                                    <span id="livePriceTypeBadge" class="badge"
                                        style="background: rgba(255,255,255,0.18); color: #fff; border: 1px solid rgba(255,255,255,0.3); padding: 4px 10px; border-radius: 20px; font-size: 11.5px;">
                                        Harga Umum
                                    </span>
                                </div>
                            </div>

                            <div style="padding: 20px;">
                                {{-- INITIAL PLACEHOLDER --}}
                                <div id="liveEmptyState" style="text-align: center; padding: 36px 16px; color: var(--t-muted);">
                                    <div style="width: 54px; height: 54px; margin: 0 auto 14px; border-radius: 14px; background: rgba(59, 130, 246, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                        <i class="bi bi-calculator"></i>
                                    </div>
                                    <h4 style="font-size: 15px; font-weight: 700; color: var(--t-base); margin: 0 0 6px;">Kalkulasi Otomatis Siap</h4>
                                    <p style="font-size: 12.5px; margin: 0; line-height: 1.5;">
                                        Silakan lengkapi pilihan <strong>Material</strong>, <strong>Ukuran (cm)</strong>, dan <strong>Jumlah</strong> untuk melihat kalkulasi harga real-time.
                                    </p>
                                </div>

                                {{-- ACTIVE RESULT CONTAINER --}}
                                <div id="liveResultContainer" style="display: none;">

                                    {{-- HIGHLIGHT TOTAL HARGA JUAL --}}
                                    <div class="selling-price-box">
                                        <div class="selling-price-box-label" style="display: flex; justify-content: space-between; align-items: center;">
                                            <span>Total Rekomendasi Harga Jual</span>
                                            <span id="liveCalcSpinner" style="display: none; font-size: 11px; color: var(--primary); text-transform: none; font-weight: 600;">
                                                <i class="bi bi-arrow-repeat spin"></i> Menghitung...
                                            </span>
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

                                        <div id="liveTotalSellingPrice" class="selling-price-box-val">
                                            Rp 0
                                        </div>

                                        <div class="selling-price-box-sub">
                                            <div>
                                                <span class="sub-label">Harga Satuan / Pcs:</span>
                                                <strong id="liveSellingPricePerPcs" class="sub-val">Rp 0</strong>
                                            </div>
                                            <div class="sub-divider">
                                                <span class="sub-label">Luas Tagihan Final:</span>
                                                <strong id="liveBillingAreaSub" class="sub-val">0 m²</strong>
                                            </div>
                                        </div>

                                        <div style="margin-top: 10px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                            <small class="rounding-pill">
                                                <i class="bi bi-aspect-ratio"></i> <span id="liveSizeBadge">Ukuran Cetak</span>
                                            </small>
                                            <small class="rounding-pill" id="liveMinChargeBadge" style="display: none; background: rgba(245, 158, 11, 0.1); color: #d97706; border-color: rgba(245, 158, 11, 0.3);">
                                                <i class="bi bi-exclamation-triangle"></i> Min Charge 1 m²
                                            </small>
                                        </div>
                                    </div>

                                    {{-- RINCIAN KOMPONEN BIAYA (HPP BREAKDOWN) --}}
                                    <div style="margin-bottom: 18px;">
                                        <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--t-muted); letter-spacing: 0.5px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
                                            <span>Rincian Komponen Biaya</span>
                                            <span id="liveTotalHppBadge" class="badge" style="background: var(--bg-muted); color: var(--t-base); border: 1px solid var(--border); font-size: 12px;">
                                                HPP: Rp 0
                                            </span>
                                        </div>

                                        <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px;">

                                            {{-- 1. Biaya Material Cetak LF --}}
                                            <div class="breakdown-row">
                                                <div>
                                                    <div class="breakdown-row-title">1. Biaya Cetak Material LF</div>
                                                    <small id="liveMaterialDetail" class="breakdown-row-sub">-</small>
                                                </div>
                                                <strong id="liveMaterialCost" class="breakdown-row-cost">Rp 0</strong>
                                            </div>

                                            {{-- 2. Laminasi --}}
                                            <div class="breakdown-row" id="liveLaminationRow" style="display: none;">
                                                <div>
                                                    <div class="breakdown-row-title">2. Finishing Laminasi</div>
                                                    <small id="liveLaminationDetail" class="breakdown-row-sub">Tanpa Laminasi</small>
                                                </div>
                                                <strong id="liveLaminationCost" class="breakdown-row-cost">Rp 0</strong>
                                            </div>

                                            {{-- 3. Komponen Tambahan (Jika ada) --}}
                                            <div class="breakdown-row" id="liveAdditionalRow" style="display: none;">
                                                <div>
                                                    <div class="breakdown-row-title">3. Komponen Tambahan</div>
                                                    <small id="liveAdditionalDetail" class="breakdown-row-sub">-</small>
                                                </div>
                                                <strong id="liveAdditionalCost" class="breakdown-row-cost">Rp 0</strong>
                                            </div>

                                            {{-- TOTAL HPP BAR --}}
                                            <div class="total-hpp-bar">
                                                <div>
                                                    <strong class="total-hpp-bar-title">TOTAL HPP PRODUKSI</strong>
                                                    <div class="total-hpp-bar-sub">Profit: <span id="liveProfitVal" style="color: var(--primary); font-weight: 700;">Rp 0</span> (Margin: <span id="liveMarginVal">0%</span>)</div>
                                                </div>
                                                <strong id="liveTotalHppVal" class="total-hpp-bar-val">Rp 0</strong>
                                            </div>

                                        </div>
                                    </div>

                                    {{-- ACTION BUTTONS: CETAK SURAT PENAWARAN & DETAIL ESTIMASI --}}
                                    <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 12px;">
                                        <button type="button" id="btnOpenQuotationModal" class="btn-preview-estimation" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 16px; border-radius: 9px; font-size: 13.5px; font-weight: 700; background: var(--primary); color: #fff; border: none; cursor: pointer; transition: all 0.2s ease;">
                                            <i class="bi bi-printer"></i> Preview &amp; Cetak Surat Penawaran
                                        </button>
                                        <button type="button" id="btnOpenTechnicalModal" class="btn-detail-estimation" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; padding: 11px 16px; border-radius: 9px; font-size: 13px; font-weight: 600; background: var(--bg-muted); color: var(--t-base); border: 1.5px solid var(--border); cursor: pointer; transition: all 0.2s ease;">
                                            <i class="bi bi-file-earmark-spreadsheet"></i> Detail Rincian Estimasi (HPP &amp; Biaya)
                                        </button>
                                    </div>

                                    <div style="font-size: 11.5px; color: var(--t-muted); text-align: center;">
                                        <i class="bi bi-lightning-charge"></i> Kalkulasi otomatis diperbarui saat Anda mengubah formulir di samping.
                                    </div>

                                </div>
                            </div>
                        </section>
                    </div>

                {{-- =========================================================
                    BAGIAN BAWAH: PRICE LIST LARGE FORMAT (FULL WIDTH)
                ========================================================== --}}
                <div class="lf-matrix-col">
                    <section class="card matrix-card">

                        {{-- CARD HEAD: JUDUL & TOOLBAR DUA BARIS RESPONSIVE --}}
                        <div class="card-head matrix-card-head">
                            <div class="matrix-head-title-wrap">
                                <div class="matrix-head-icon">
                                    <i class="bi bi-grid-3x3-gap-fill"></i>
                                </div>
                                <div class="matrix-head-text">
                                    <div class="matrix-head-eyebrow-row">
                                        <span class="eyebrow matrix-eyebrow">
                                            TABEL ACUAN BIAYA &amp; HARGA JUAL RESMI
                                        </span>
                                        <span id="currentCatBadge" class="badge matrix-cat-badge">
                                            <i class="bi bi-folder2-open me-1"></i> Kategori: Indoor
                                        </span>
                                    </div>
                                    <h3 class="matrix-title">
                                        Price List Cetak Large Format
                                    </h3>
                                    <p class="matrix-subtitle">
                                        Daftar HPP (Bahan &amp; Ongkos Cetak) dan Rekomendasi Harga Jual (Harga Umum, Divisi, &amp; Polos).
                                    </p>
                                </div>
                            </div>

                            {{-- TOOLBAR RESPONSIVE --}}
                            <div class="matrix-toolbar">
                                {{-- BARIS 1: FILTER KATEGORI, SEARCH MATERIAL (FLEX 1), & JUMLAH BARIS --}}
                                <div class="matrix-toolbar-row-main">
                                    {{-- 1. Filter Kategori (Select2) --}}
                                    <div class="matrix-filter-group">
                                        <label for="priceListCategoryFilter" class="matrix-filter-label">
                                            <i class="bi bi-tag-fill text-primary"></i> Kategori:
                                        </label>
                                        @php
                                            $lfCategoriesList = $lfPriceList['categories'] ?? [];
                                            $defaultLfCat = $lfCategoriesList[0] ?? 'all';
                                        @endphp
                                        <div class="toolbar-select2-wrap matrix-cat-select-wrap">
                                            <select id="priceListCategoryFilter" class="select select2" style="width: 100%;">
                                                @foreach ($lfCategoriesList as $cat)
                                                    <option value="{{ $cat }}" {{ $cat === $defaultLfCat ? 'selected' : '' }}>
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
                                            placeholder="Cari nama material / ukuran lebar..." autocomplete="off">
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
                                            <button type="button" class="matrix-tab-btn" data-group="plain" title="Harga Polos Saja">
                                                Harga Polos
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CARD BODY: TABEL PRICE LIST LARGE FORMAT --}}
                        <div style="padding: 16px 20px;">
                            <div class="details-digital-table-wrapper" style="max-height: 60vh; border-radius: 10px; border: 1px solid var(--border);">
                                <table class="details-digital-table" id="lfPriceListTable">
                                    <thead>
                                        <tr class="details-digital-group-row">
                                            <th rowspan="2" class="details-digital-sticky details-digital-col-no" style="font-weight: 700;">No</th>
                                            <th rowspan="2" class="details-digital-sticky details-digital-col-material" style="font-weight: 700;">Item Material</th>
                                            <th rowspan="2" style="font-weight: 700; text-align: center;">Ukuran Lebar</th>

                                            <th colspan="4" class="details-digital-cost" style="font-weight: 700; text-align: center;">
                                                Biaya Produksi (HPP) / m²
                                            </th>

                                            <th colspan="3" class="details-digital-group" style="font-weight: 700; text-align: center;">
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
                                    <tbody id="lfPriceListBody">
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

            <div data-shell-footer></div>
        </div>
    </div>

    {{-- =========================================================
        MODAL 1: PREVIEW & CETAK SURAT PENAWARAN (LARGE FORMAT)
    ========================================================== --}}
    <div class="modal-overlay" id="modalLFQuotation">
        <div class="modal modal-content-custom" style="max-width: 750px;">

            {{-- MODAL HEAD --}}
            <div class="modal-head-custom">
                <div>
                    <span class="eyebrow" style="color: var(--primary); font-weight: 700;">PREVIEW PENAWARAN HARGA</span>
                    <h3 class="modal-title-custom">Surat Penawaran Cetak Large Format</h3>
                </div>
                <button type="button" id="btnCloseQuotationModal" class="modal-close-btn" aria-label="Tutup">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- MODAL BODY (PRINTABLE) --}}
            <div id="printableEstimationArea" style="padding: 24px; overflow-y: auto; flex: 1;">
                <div class="printable-header">
                    <div>
                        <h2 class="printable-company-name">CIPTA GRAFIKA</h2>
                        <div style="font-size: 12px; color: var(--t-muted);">Printing Management System &middot; Estimator Large Format</div>
                    </div>
                    <div style="text-align: right; font-size: 12px; color: var(--t-muted);">
                        <div>Tanggal: <strong>{{ date('d/m/Y') }}</strong></div>
                        <div>Lead Time: <strong id="modalQuoteLeadTime">-</strong></div>
                    </div>
                </div>

                {{-- Customer & Specs Info Grid --}}
                <div class="printable-info-grid">
                    <div class="printable-info-box">
                        <div style="font-weight: 700; color: var(--t-base); margin-bottom: 4px;">INFORMASI PELANGGAN:</div>
                        <div>Nama: <strong id="modalQuoteCustomerName">-</strong></div>
                        <div>Telepon: <strong id="modalQuoteCustomerPhone">-</strong></div>
                        <div>Cabang: <strong id="modalQuoteCustomerLocation">-</strong></div>
                        <div id="modalQuoteNotesWrap" style="display: none; margin-top: 4px; padding-top: 4px; border-top: 1px dashed var(--border);">
                            Catatan: <strong id="modalQuoteCustomerNotes">-</strong>
                        </div>
                    </div>
                    <div class="printable-info-box">
                        <div style="font-weight: 700; color: var(--t-base); margin-bottom: 4px;">SPESIFIKASI CETAK:</div>
                        <div>Mesin: <strong>Large Format</strong></div>
                        <div>Kategori: <strong id="modalQuoteCategoryName">-</strong></div>
                        <div>Material: <strong id="modalQuoteMaterialName">-</strong></div>
                        <div>Ukuran &amp; Qty: <strong id="modalQuoteSizeQty">-</strong></div>
                        <div>Luas Tagihan: <strong id="modalQuoteBillingArea">-</strong></div>
                        <div>Finishing: <strong id="modalQuoteFinishing">-</strong></div>
                    </div>
                </div>

                {{-- Table Summary Penawaran --}}
                <table class="printable-table">
                    <thead>
                        <tr>
                            <th style="padding: 8px 10px; text-align: left;">Item Deskripsi</th>
                            <th style="padding: 8px 10px; text-align: center;">Luas / Qty</th>
                            <th style="padding: 8px 10px; text-align: right;">Harga Satuan</th>
                            <th style="padding: 8px 10px; text-align: right;">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody id="modalQuoteTableBody">
                        <tr>
                            <td style="padding: 10px;">
                                <strong id="modalQuoteItemTitle">Cetak Large Format</strong>
                                <div id="modalQuoteItemDesc" style="font-size: 11.5px; color: var(--t-muted);">-</div>
                            </td>
                            <td id="modalQuoteItemQty" style="padding: 10px; text-align: center;">-</td>
                            <td id="modalQuoteItemUnitPrice" style="padding: 10px; text-align: right;">-</td>
                            <td id="modalQuoteItemSubtotal" style="padding: 10px; text-align: right; font-weight: 700;">-</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr id="modalQuoteSubtotalRow" style="display: none;">
                            <th colspan="3" style="padding: 6px 10px; text-align: right; color: var(--t-muted); font-weight: 500;">
                                Subtotal (Sebelum Diskon):
                            </th>
                            <th id="modalQuoteSubtotalAmount" style="padding: 6px 10px; text-align: right; font-weight: 600; color: var(--t-base);">
                                -
                            </th>
                        </tr>
                        <tr id="modalQuoteDiscountRow" style="display: none;">
                            <th colspan="3" style="padding: 6px 10px; text-align: right; color: #ef4444; font-weight: 600;">
                                Potongan Diskon (<span id="modalQuoteDiscountLabel">-</span>):
                            </th>
                            <th id="modalQuoteDiscountAmount" style="padding: 6px 10px; text-align: right; font-weight: 700; color: #ef4444;">
                                -
                            </th>
                        </tr>
                        <tr>
                            <th colspan="3" style="padding: 12px 10px; text-align: right; text-transform: uppercase;">
                                Total Harga Jual:
                            </th>
                            <th id="modalQuoteGrandTotal" style="padding: 12px 10px; text-align: right; font-weight: 800; color: var(--primary); font-size: 1.15rem;">
                                -
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <div style="font-size: 11px; color: var(--t-muted); border-top: 1px dashed var(--border); padding-top: 10px;">
                    * Penawaran ini berlaku selama 14 hari sejak tanggal diterbitkan. Harga sudah termasuk cetak mesin Large Format, material bahan pilihan, dan finishing yang dipilih.
                </div>
            </div>

            {{-- MODAL FOOT --}}
            <div class="modal-foot-custom">
                <button type="button" id="btnCancelQuotationModal" class="btn btn-secondary-custom">
                    Tutup
                </button>
                <button type="button" id="btnPrintQuotationModal" class="btn btn--primary" style="display: flex; align-items: center; gap: 6px;">
                    <i class="bi bi-printer-fill"></i> Cetak Dokumen
                </button>
            </div>

        </div>
    </div>

    {{-- =========================================================
        MODAL 2: DETAIL RINCIAN ESTIMASI (HPP & BIAYA LARGE FORMAT)
    ========================================================== --}}
    <div class="modal-overlay" id="modalLFTechnical">
        <div class="modal modal-content-custom" style="max-width: 820px;">

            {{-- MODAL HEAD --}}
            <div class="modal-head-custom">
                <div>
                    <span class="eyebrow" style="color: var(--primary); font-weight: 700;">RINCIAN TEKNIS PRODUKSI &amp; BIAYA</span>
                    <h3 class="modal-title-custom">Detail Rincian Estimasi Large Format</h3>
                </div>
                <button type="button" id="btnCloseTechModal" class="modal-close-btn" aria-label="Tutup">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- MODAL BODY --}}
            <div style="padding: 24px; overflow-y: auto; flex: 1;">

                {{-- SPESIFIKASI PRODUKSI --}}
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--primary); letter-spacing: 0.5px; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                        <i class="bi bi-gear-fill"></i> PARAMETER PRODUKSI &amp; MESIN
                    </div>

                    <div class="tech-spec-grid">
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Mesin Produksi</div>
                            <div class="tech-spec-value">Large Format</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Lokasi Cabang</div>
                            <div class="tech-spec-value" id="techLocationName">-</div>
                        </div>
                        <div class="tech-spec-item" id="techVendorItem" style="display: none;">
                            <div class="tech-spec-label">Vendor Outsourcing</div>
                            <div class="tech-spec-value" id="techVendorName">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Profil Harga</div>
                            <div class="tech-spec-value" id="techPriceType">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Kategori</div>
                            <div class="tech-spec-value" id="techCategoryName">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Material Bahan</div>
                            <div class="tech-spec-value" id="techMaterialName">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Ukuran Produk</div>
                            <div class="tech-spec-value" id="techFinishedSize">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Lebar Bahan Roll</div>
                            <div class="tech-spec-value" id="techMaterialWidth">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Objek / Baris</div>
                            <div class="tech-spec-value" id="techObjectsPerRow">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Jumlah Baris</div>
                            <div class="tech-spec-value" id="techTotalRows">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Panjang Produksi</div>
                            <div class="tech-spec-value" id="techProductionLength">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Luas vs Min Charge</div>
                            <div class="tech-spec-value" id="techAreaCompare">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Luas Tagihan Final</div>
                            <div class="tech-spec-value" id="techBillingArea" style="color: var(--primary);">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Estimasi Waktu</div>
                            <div class="tech-spec-value" id="techLeadTime">-</div>
                        </div>
                    </div>
                </div>

                {{-- RINCIAN KOMPONEN BIAYA & HPP --}}
                <div style="margin-bottom: 20px;">
                    <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--primary); letter-spacing: 0.5px; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                        <i class="bi bi-calculator"></i> RINCIAN BIAYA POKOK PRODUKSI (HPP) &amp; HARGA JUAL
                    </div>

                    <table class="printable-table" style="margin-bottom: 10px;">
                        <thead>
                            <tr>
                                <th style="padding: 10px; text-align: left;">Komponen Biaya</th>
                                <th style="padding: 10px; text-align: left;">Spesifikasi / Luas</th>
                                <th style="padding: 10px; text-align: right;">HPP Produksi</th>
                                <th style="padding: 10px; text-align: right;">Subtotal Harga Jual</th>
                            </tr>
                        </thead>
                        <tbody id="techBreakdownTableBody">
                            <tr>
                                <td style="padding: 10px;">
                                    <strong>1. Cetak Material Large Format</strong>
                                    <div style="font-size: 11px; color: var(--t-muted);">Biaya bahan cetak &amp; operasional mesin</div>
                                </td>
                                <td id="techRow1Spec" style="padding: 10px; font-size: 12.5px;">-</td>
                                <td id="techRow1Hpp" style="padding: 10px; text-align: right; font-weight: 600;">Rp 0</td>
                                <td id="techRow1Subtotal" style="padding: 10px; text-align: right; font-weight: 700; color: var(--primary);">Rp 0</td>
                            </tr>
                            <tr id="techRowLamination" style="display: none;">
                                <td style="padding: 10px;">
                                    <strong>2. Finishing Laminasi</strong>
                                    <div style="font-size: 11px; color: var(--t-muted);">Lapisan pelindung cetak outdoor/indoor</div>
                                </td>
                                <td id="techRow2Spec" style="padding: 10px; font-size: 12.5px;">Tanpa Laminasi</td>
                                <td id="techRow2Hpp" style="padding: 10px; text-align: right; font-weight: 600;">Rp 0</td>
                                <td id="techRow2Subtotal" style="padding: 10px; text-align: right; font-weight: 700; color: var(--primary);">Rp 0</td>
                            </tr>
                        </tbody>
                        <tbody id="techExtraComponentsRows">
                            {{-- Dynamically inserted additional components --}}
                        </tbody>
                        <tfoot>
                            <tr id="techSubtotalRow" style="display: none; border-top: 1.5px solid var(--border);">
                                <th colspan="3" style="padding: 8px 10px; text-align: right; color: var(--t-muted); font-weight: 600;">
                                    Subtotal Harga Jual (Sebelum Diskon):
                                </th>
                                <th id="techSubtotalAmount" style="padding: 8px 10px; text-align: right; font-weight: 700; color: var(--t-base);">
                                    Rp 0
                                </th>
                            </tr>
                            <tr id="techDiscountRow" style="display: none; background: rgba(239, 68, 68, 0.06);">
                                <th colspan="3" style="padding: 8px 10px; text-align: right; color: #ef4444; font-weight: 700;">
                                    <i class="bi bi-tag-fill" style="margin-right: 4px;"></i> Potongan Diskon (<span id="techDiscountLabel">-</span>):
                                </th>
                                <th id="techDiscountAmount" style="padding: 8px 10px; text-align: right; font-weight: 800; color: #ef4444;">
                                    - Rp 0
                                </th>
                            </tr>
                            <tr style="border-top: 2px solid var(--border);">
                                <th colspan="2" style="padding: 10px; text-align: left; font-size: 12.5px; font-weight: 800; text-transform: uppercase;">
                                    TOTAL AKHIR HARGA JUAL
                                </th>
                                <th id="techTotalHppCol" style="padding: 10px; text-align: right; font-weight: 800; color: var(--t-base);">
                                    HPP: Rp 0
                                </th>
                                <th id="techGrandTotalCol" style="padding: 10px; text-align: right; font-weight: 800; color: var(--primary); font-size: 1.1rem;">
                                    Rp 0
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- INFO DISKON BANNER JIKA ADA DISKON AKTIF --}}
                <div id="techDiscountBanner" style="display: none; margin-bottom: 20px; padding: 12px 16px; border-radius: 9px; background: rgba(239, 68, 68, 0.08); border: 1.5px dashed rgba(239, 68, 68, 0.35);">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="width: 30px; height: 30px; border-radius: 6px; background: rgba(239, 68, 68, 0.15); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 14px;">
                                <i class="bi bi-tag-fill"></i>
                            </span>
                            <div>
                                <strong style="color: #ef4444; font-size: 13.5px;">Potongan Diskon Diterapkan: <span id="techBannerDiscountDesc">-</span></strong>
                                <div style="font-size: 12px; color: var(--t-muted); margin-top: 2px;">
                                    Harga sebelum diskon: <s id="techBannerOriginalSubtotal" style="font-weight: 600;">Rp 0</s>
                                </div>
                            </div>
                        </div>
                        <strong id="techBannerDiscountVal" style="color: #ef4444; font-size: 16px; font-weight: 800;">- Rp 0</strong>
                    </div>
                </div>

                {{-- 4-METRIC FINANCIAL KPI CARDS --}}
                <div>
                    <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--primary); letter-spacing: 0.5px; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                        <i class="bi bi-graph-up-arrow"></i> RINGKASAN PROFITABILITAS &amp; FINANSIAL
                    </div>

                    <div class="tech-kpi-grid">
                        <div class="tech-kpi-card">
                            <span class="tech-kpi-label">Total HPP Produksi</span>
                            <span class="tech-kpi-val" id="techKpiTotalHpp">Rp 0</span>
                        </div>
                        <div class="tech-kpi-card highlight">
                            <span class="tech-kpi-label" style="color: var(--primary);">Total Harga Jual</span>
                            <span class="tech-kpi-val" id="techKpiGrandTotal" style="color: var(--primary);">Rp 0</span>
                        </div>
                        <div class="tech-kpi-card profit">
                            <span class="tech-kpi-label" style="color: var(--primary);">Estimasi Profit</span>
                            <span class="tech-kpi-val" id="techKpiProfit" style="color: var(--primary);">Rp 0</span>
                        </div>
                        <div class="tech-kpi-card">
                            <span class="tech-kpi-label">Margin / Markup</span>
                            <span class="tech-kpi-val" style="font-size: 14.5px;">
                                <span id="techKpiMargin">0%</span> <span style="font-size: 11px; color: var(--t-muted); font-weight: 400;">(Markup <span id="techKpiMarkup">0%</span>)</span>
                            </span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- MODAL FOOT --}}
            <div class="modal-foot-custom">
                <button type="button" id="btnCancelTechModal" class="btn btn-secondary-custom">
                    Tutup
                </button>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            /* =====================================================
               1. SELECT2 INITIALIZATION
            ===================================================== */
            $('.select2').select2({
                width: '100%',
                placeholder: 'Pilih...'
            });

            /* =====================================================
               2. ELEMENTS & REFERENCES
            ===================================================== */
            const form = document.getElementById('largeFormatEstimationForm');
            const locationSelect = $('#location_id');
            const vendorSelect = $('#vendor_id');
            const vendorField = $('#vendorField');
            const categorySelect = $('#category_id');
            const materialSelect = $('#material_id');
            const materialSizeSelect = $('#material_size_id');
            const laminationSelect = $('#lamination_id');
            const laminationSizeSelect = $('#lamination_size_id');
            const priceTypeSelect = $('#price_type');

            const lengthInput = document.getElementById('length');
            const widthInput = document.getElementById('width');
            const qtyInput = document.getElementById('qty');
            const estTimeInput = document.getElementById('estimation_time');

            const dimensionFieldsGrid = document.getElementById('dimensionFieldsGrid');
            const materialSizeField = document.getElementById('materialSizeField');

            const materialPriceSizeInput = $('#material_price_size_id');
            const laminationPriceSizeInput = $('#lamination_price_size_id');

            const materialCustomWidthInput = $('#material_custom_width');
            const materialPriceReferenceField = $('#materialPriceReferenceField');
            const materialPriceReferenceSelect = $('#material_price_reference_select');
            const laminationCustomWidthInput = $('#lamination_custom_width');

            const displayConfigurationInput = $('#display_configuration_id');
            const displayProductInput = $('#display_product_id');
            const displayComponentInput = $('#display_component_id');
            const displayComponentTypeInput = $('#display_component_type');

            // Diskon
            const discountRadios = document.querySelectorAll('input[name="discount_type"]');
            const discountValueField = document.getElementById('discountValueField');
            const discountValueInput = document.getElementById('discount_value');
            const discountValueLabel = document.getElementById('discountValueLabel');
            const discountSuffix = document.getElementById('discountSuffix');

            // Modals
            const modalQuotation = document.getElementById('modalLFQuotation');
            const btnOpenQuotationModal = document.getElementById('btnOpenQuotationModal');
            const btnCloseQuotationModal = document.getElementById('btnCloseQuotationModal');
            const btnCancelQuotationModal = document.getElementById('btnCancelQuotationModal');
            const btnPrintQuotationModal = document.getElementById('btnPrintQuotationModal');

            const modalTechnical = document.getElementById('modalLFTechnical');
            const btnOpenTechnicalModal = document.getElementById('btnOpenTechnicalModal');
            const btnCloseTechModal = document.getElementById('btnCloseTechModal');
            const btnCancelTechModal = document.getElementById('btnCancelTechModal');

            // Summary card elements
            const liveEmptyState = document.getElementById('liveEmptyState');
            const liveResultContainer = document.getElementById('liveResultContainer');
            const liveCalcSpinner = document.getElementById('liveCalcSpinner');
            const livePriceTypeBadge = document.getElementById('livePriceTypeBadge');
            const btnResetForm = document.getElementById('btnResetForm');

            /* =====================================================
               3. URLS & CONSTANTS
            ===================================================== */
            const materialsUrl = "{{ route('admin_get_materials') }}";
            const materialSizesUrl = "{{ route('admin_get_material_sizes') }}";
            const laminationsUrl = "{{ route('admin_get_laminations') }}";
            const laminationSizesUrl = "{{ route('admin_get_laminations_sizes') }}";
            const categoriesUrl = "{{ route('admin_get_lf_categories') }}";
            const engineName = 'Large Format';

            /* =====================================================
               4. HELPER FUNCTIONS
            ===================================================== */
            function isDisplayCategory() {
                return (
                    categorySelect.find('option:selected')
                    .text()
                    .trim()
                    .toLowerCase() === 'display'
                );
            }

            function isOutsourcingLocation() {
                return locationSelect
                    .find('option:selected')
                    .text()
                    .trim()
                    .toLowerCase() === 'outsourcing';
            }

            function resetDisplayIdentity() {
                displayConfigurationInput.val('');
                displayProductInput.val('');
                displayComponentInput.val('');
                displayComponentTypeInput.val('');
            }

            function formatRupiah(num) {
                const n = Number(num) || 0;
                return 'Rp ' + Math.round(n).toLocaleString('id-ID');
            }

            function formatNumber(value, maxDecimals = 2) {
                const n = Number(value) || 0;
                return new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: maxDecimals
                }).format(n);
            }

            function formatPercentage(num) {
                const n = Number(num) || 0;
                return n.toFixed(1).replace('.', ',') + '%';
            }

            function updatePriceTypeBadge() {
                const pt = priceTypeSelect.val();
                if (livePriceTypeBadge) {
                    livePriceTypeBadge.textContent = pt === 'division' ? 'Harga Divisi' : 'Harga Umum';
                }
            }

            /* =====================================================
               5. TOGGLE FIELDS (DISPLAY & VENDOR & CUSTOM WIDTH)
            ===================================================== */
            function toggleDisplayFields() {
                const isDisplay = isDisplayCategory();

                if (dimensionFieldsGrid) dimensionFieldsGrid.style.display = isDisplay ? 'none' : '';
                if (materialSizeField) materialSizeField.style.display = isDisplay ? 'none' : '';

                $(lengthInput).prop('required', !isDisplay);
                $(widthInput).prop('required', !isDisplay);
                materialSizeSelect.prop('required', !isDisplay);

                if (isDisplay) {
                    lengthInput.value = '';
                    widthInput.value = '';
                    materialSizeSelect.val(null).trigger('change.select2');
                }
            }

            function toggleVendorField() {
                if (isOutsourcingLocation()) {
                    vendorField.show();
                    vendorSelect.prop('required', true);
                } else {
                    vendorSelect.val('').trigger('change.select2');
                    vendorSelect.prop('required', false);
                    vendorField.hide();
                }
            }

            function toggleCustomWidth(type) {
                const isMaterial = type === 'material';
                const select = isMaterial ? materialSizeSelect : laminationSizeSelect;
                const input = isMaterial ? materialCustomWidthInput : laminationCustomWidthInput;
                const isCustom = select.val() === '__custom__';

                if (isCustom) {
                    const priceSizeInput = isMaterial ? materialPriceSizeInput : laminationPriceSizeInput;
                    const referenceOptions = select.find('option').filter(function() {
                        return this.value && this.value !== '__custom__';
                    });
                    const currentReferenceId = priceSizeInput.val();
                    const hasCurrentReference = referenceOptions.toArray().some(function(option) {
                        return option.value === currentReferenceId;
                    });

                    priceSizeInput.val(
                        hasCurrentReference ?
                        currentReferenceId :
                        (referenceOptions.first().val() || '')
                    );

                    input.prop('disabled', false).prop('required', true).show();

                    if (isMaterial && isOutsourcingLocation()) {
                        materialPriceReferenceField.show();
                        materialPriceReferenceSelect.prop('required', true).val(priceSizeInput.val());
                    }
                } else {
                    input.val('').prop('disabled', true).prop('required', false).hide();

                    if (isMaterial) {
                        materialPriceReferenceField.hide();
                        materialPriceReferenceSelect.prop('required', false).val('');
                    }
                }
            }

            /* =====================================================
               6. AJAX CASCADES (CATEGORIES, MATERIALS, SIZES)
            ===================================================== */
            function loadCategories() {
                categorySelect.empty().append('<option value="">Pilih Kategori</option>').trigger('change.select2');

                fetch(categoriesUrl, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data && data.success && Array.isArray(data.data)) {
                        data.data.forEach(category => {
                            categorySelect.append(new Option(category.name, category.id, false, false));
                        });
                        categorySelect.trigger('change.select2');
                        toggleDisplayFields();
                    }
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                });
            }

            async function loadMaterials() {
                const locationId = locationSelect.val();
                const categoryId = categorySelect.val();
                const vendorId = vendorSelect.val();
                const isDisplay = isDisplayCategory();
                const isOutsourcing = isOutsourcingLocation();

                resetDisplayIdentity();

                materialSelect.empty().append('<option value="">Pilih Material</option>').val(null).trigger('change.select2');
                materialSizeSelect.empty().append('<option value="">Pilih Lebar Bahan</option>').val(null).trigger('change.select2');

                if (!locationId || !categoryId) return;
                if (isOutsourcing && !vendorId) return;

                try {
                    const url = new URL(materialsUrl, window.location.origin);
                    url.searchParams.set('location_id', locationId);
                    url.searchParams.set('category_id', categoryId);
                    url.searchParams.set('engine', engineName);
                    if (isOutsourcing) url.searchParams.set('vendor_id', vendorId);

                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Gagal mengambil data material.');
                    const result = await response.json();
                    if (!result.success) throw new Error(result.message || 'Data material gagal diambil.');

                    result.data.forEach(function(material) {
                        const option = new Option(material.material_name, material.id, false, false);
                        if (isDisplay) {
                            option.dataset.configurationId = material.configuration_id || '';
                            option.dataset.displayProductId = material.display_product_id || '';
                            option.dataset.componentId = material.component_id || '';
                            option.dataset.componentType = material.component_type || '';
                        }
                        materialSelect.append(option);
                    });

                    materialSelect.trigger('change.select2');
                } catch (error) {
                    console.error('Load Material Error:', error);
                }
            }

            async function loadMaterialSizes() {
                const locationId = locationSelect.val();
                const categoryId = categorySelect.val();
                const materialId = materialSelect.val();
                const vendorId = vendorSelect.val();
                const isOutsourcing = isOutsourcingLocation();

                materialSizeSelect.empty().append('<option value="">Pilih Lebar Bahan</option>').val(null).trigger('change.select2');
                materialPriceSizeInput.val('');
                materialPriceReferenceSelect.empty().append('<option value=""></option>');
                materialPriceReferenceField.hide();
                materialCustomWidthInput.val('').prop('disabled', true).prop('required', false).hide();

                if (!locationId || !categoryId || !materialId) return;
                if (isOutsourcing && !vendorId) return;

                try {
                    const url = new URL(materialSizesUrl, window.location.origin);
                    url.searchParams.set('location_id', locationId);
                    url.searchParams.set('category_id', categoryId);
                    url.searchParams.set('material_id', materialId);
                    url.searchParams.set('engine', engineName);
                    if (isOutsourcing) url.searchParams.set('vendor_id', vendorId);

                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Gagal mengambil data lebar bahan.');
                    const result = await response.json();
                    if (!result.success) throw new Error(result.message || 'Data lebar bahan gagal diambil.');

                    result.data.forEach(function(size) {
                        const optText = size.width + ' cm';
                        materialSizeSelect.append(new Option(optText, size.id, false, false));
                        materialPriceReferenceSelect.append(new Option(optText, size.id, false, false));
                    });

                    // Opsi Custom
                    const customOption = new Option('+ Custom / Input Manual', '__custom__', false, false);
                    materialSizeSelect.append(customOption);

                    materialSizeSelect.trigger('change.select2');
                } catch (error) {
                    console.error('Load Material Size Error:', error);
                }
            }

            async function loadLaminations() {
                const locationId = locationSelect.val();
                const categoryId = categorySelect.val();

                laminationSelect.find('option').not('[value="none"]').remove();
                if (laminationSelect.find('option[value="none"]').length === 0) {
                    laminationSelect.prepend('<option value="none">Tanpa Laminasi</option>');
                }
                laminationSelect.val('none').trigger('change.select2');

                laminationSizeSelect.empty().append('<option value="">Pilih Ukuran Laminasi</option>').val(null).trigger('change.select2');
                laminationCustomWidthInput.val('').prop('disabled', true).prop('required', false).hide();

                if (!locationId || !categoryId) return;

                try {
                    const url = new URL(laminationsUrl, window.location.origin);
                    url.searchParams.set('location_id', locationId);
                    url.searchParams.set('category_id', categoryId);

                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Gagal mengambil data laminasi.');
                    const result = await response.json();
                    if (!result.success) throw new Error(result.message || 'Data laminasi gagal diambil.');

                    result.data.forEach(function(lamination) {
                        laminationSelect.append(new Option(lamination.name, lamination.id, false, false));
                    });

                    laminationSelect.val('none').trigger('change.select2');
                } catch (error) {
                    console.error('Load Lamination Error:', error);
                }
            }

            async function loadLaminationSizes() {
                const locationId = locationSelect.val();
                const categoryId = categorySelect.val();
                const laminationId = laminationSelect.val();

                laminationSizeSelect.empty().append('<option value="">Pilih Ukuran Laminasi</option>').val(null).trigger('change.select2');
                laminationCustomWidthInput.val('').prop('disabled', true).prop('required', false).hide();

                if (!locationId || !categoryId || !laminationId || laminationId === 'none') return;

                try {
                    const url = new URL(laminationSizesUrl, window.location.origin);
                    url.searchParams.set('location_id', locationId);
                    url.searchParams.set('category_id', categoryId);
                    url.searchParams.set('lamination_id', laminationId);

                    const response = await fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Gagal mengambil ukuran laminasi.');
                    const result = await response.json();
                    if (!result.success) throw new Error(result.message || 'Ukuran laminasi gagal diambil.');

                    result.data.forEach(function(size) {
                        let label = formatNumber(size.width) + ' cm';
                        if (size.length !== null && size.length !== undefined) {
                            label += ' × ' + formatNumber(size.length) + ' cm';
                        }
                        laminationSizeSelect.append(new Option(label, size.id, false, false));
                    });

                    laminationSizeSelect.append(new Option('+ Custom / Input Manual', '__custom__', false, false));
                    laminationSizeSelect.trigger('change.select2');
                } catch (error) {
                    console.error('Load Lamination Size Error:', error);
                }
            }

            /* =====================================================
               7. EVENT BINDINGS FOR CASCADES
            ===================================================== */
            locationSelect.on('change', function() {
                toggleVendorField();
                loadMaterials();
                loadLaminations();
                updateQuotationCustomerInfo();
                triggerRealtimeCalculation();
            });

            vendorSelect.on('change', function() {
                loadMaterials();
                triggerRealtimeCalculation();
            });

            categorySelect.on('change', function() {
                resetDisplayIdentity();
                toggleDisplayFields();
                loadMaterials();
                loadLaminations();
                triggerRealtimeCalculation();
            });

            materialSelect.on('change', function() {
                if (isDisplayCategory()) {
                    const selectedOption = this.options[this.selectedIndex];
                    displayConfigurationInput.val(selectedOption?.dataset.configurationId || '');
                    displayProductInput.val(selectedOption?.dataset.displayProductId || '');
                    displayComponentInput.val(selectedOption?.dataset.componentId || '');
                    displayComponentTypeInput.val(selectedOption?.dataset.componentType || '');

                    materialSizeSelect.empty().append('<option value=""></option>').val(null).trigger('change.select2');
                    triggerRealtimeCalculation();
                    return;
                }

                resetDisplayIdentity();
                loadMaterialSizes();
                triggerRealtimeCalculation();
            });

            materialSizeSelect.on('change', function() {
                const val = this.value;
                if (val && val !== '__custom__') {
                    materialPriceSizeInput.val(val);
                } else if (!val) {
                    materialPriceSizeInput.val('');
                }
                toggleCustomWidth('material');
                triggerRealtimeCalculation();
            });

            materialPriceReferenceSelect.on('change', function() {
                materialPriceSizeInput.val(this.value);
                triggerRealtimeCalculation();
            });

            laminationSelect.on('change', function() {
                loadLaminationSizes();
                triggerRealtimeCalculation();
            });

            laminationSizeSelect.on('change', function() {
                const val = this.value;
                if (val && val !== '__custom__') {
                    laminationPriceSizeInput.val(val);
                } else if (!val) {
                    laminationPriceSizeInput.val('');
                }
                toggleCustomWidth('lamination');
                triggerRealtimeCalculation();
            });

            priceTypeSelect.on('change', function() {
                updatePriceTypeBadge();
                triggerRealtimeCalculation();
            });

            // Initialize cascades on startup
            toggleVendorField();
            loadCategories();

            /* =====================================================
               8. KOMPONEN TAMBAHAN REPEATER
            ===================================================== */
            const addAdditionalComponentButton = document.getElementById('addAdditionalComponentButton');
            const additionalComponentsContainer = document.getElementById('additionalComponentsContainer');
            let additionalComponentIndex = 0;

            function addAdditionalComponent() {
                const index = additionalComponentIndex++;
                const componentHtml = `
                    <div class="additional-component-item" data-component-index="${index}"
                        style="border: 1px solid var(--border); border-radius: 12px; padding: 18px; margin-bottom: 14px; background: var(--bg-card);">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px;">
                            <div style="font-size: 13.5px; font-weight: 700; color: var(--t-base);">
                                Komponen Tambahan #<span class="additional-component-number">${index + 1}</span>
                            </div>
                            <button type="button" class="btn btn-danger btn-remove-additional-component" style="padding: 4px 8px; font-size: 12px;">
                                <i class="bi bi-trash3-fill"></i> Hapus
                            </button>
                        </div>

                        <div class="form-grid">
                            {{-- Tipe Komponen --}}
                            <div class="field">
                                <label class="field-label" for="additional_component_type_${index}">Tipe</label>
                                <select id="additional_component_type_${index}" name="additional_components[${index}][type]"
                                    class="select select2 additional-component-type">
                                    <option value=""></option>
                                    <option value="Material">Material</option>
                                    <option value="Finishing">Finishing</option>
                                    <option value="Jasa Pemasangan">Jasa Pemasangan</option>
                                </select>
                            </div>

                            {{-- Nama Komponen --}}
                            <div class="field">
                                <label class="field-label" for="additional_component_name_${index}">Nama Komponen</label>
                                <input type="text" id="additional_component_name_${index}" name="additional_components[${index}][name]"
                                    class="input additional-component-name" placeholder="Contoh: Mata Ayam / Keling" autocomplete="off">
                            </div>

                            {{-- Qty --}}
                            <div class="field">
                                <label class="field-label" for="additional_component_qty_${index}">Qty</label>
                                <input type="number" id="additional_component_qty_${index}" name="additional_components[${index}][qty]"
                                    class="input additional-component-qty" min="1" step="1" value="1">
                            </div>

                            {{-- Tipe Harga --}}
                            <div class="field">
                                <label class="field-label" for="additional_component_price_type_${index}">Tipe Harga</label>
                                <select id="additional_component_price_type_${index}" name="additional_components[${index}][price_type]"
                                    class="select select2 additional-component-price-type">
                                    <option value="unit_price" selected>Harga Satuan (Rp)</option>
                                    <option value="hpp_margin">HPP + Margin %</option>
                                </select>
                            </div>
                        </div>

                        {{-- Harga Satuan Wrapper --}}
                        <div class="additional-component-unit-price-wrapper" style="margin-top: 14px;">
                            <div class="field">
                                <label class="field-label" for="additional_component_unit_price_${index}">Harga Satuan (Rp)</label>
                                <input type="text" id="additional_component_unit_price_${index}" name="additional_components[${index}][unit_price]"
                                    class="input additional-component-unit-price" inputmode="numeric" placeholder="Contoh: 15.000">
                                <small class="field-help">Harga jual per unit komponen.</small>
                            </div>
                        </div>

                        {{-- HPP + Margin Wrapper --}}
                        <div class="additional-component-hpp-margin-wrapper" style="display: none; margin-top: 14px;">
                            <div class="form-grid">
                                <div class="field">
                                    <label class="field-label" for="additional_component_hpp_${index}">HPP / Unit (Rp)</label>
                                    <input type="text" id="additional_component_hpp_${index}" name="additional_components[${index}][hpp]"
                                        class="input additional-component-hpp" inputmode="numeric" placeholder="Contoh: 10.000">
                                </div>
                                <div class="field">
                                    <label class="field-label" for="additional_component_margin_${index}">Margin</label>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <input type="number" id="additional_component_margin_${index}" name="additional_components[${index}][margin]"
                                            class="input additional-component-margin" min="0" max="99.99" step="0.01" placeholder="Contoh: 40">
                                        <span>%</span>
                                    </div>
                                </div>
                            </div>
                            <div class="field" style="margin-top: 10px;">
                                <label class="field-label">Harga Jual / Unit (Otomatis)</label>
                                <input type="text" class="input additional-component-calculated-price" value="Rp 0" readonly>
                            </div>
                        </div>
                    </div>
                `;

                additionalComponentsContainer.insertAdjacentHTML('beforeend', componentHtml);
                const component = additionalComponentsContainer.lastElementChild;

                // Init Select2
                $(component).find('.select2').select2({
                    width: '100%',
                    placeholder: 'Pilih...'
                });

                initializeAdditionalComponent(component);
                updateAdditionalComponentNumbers();
            }

            function initializeAdditionalComponent(component) {
                const priceTypeSelect = component.querySelector('.additional-component-price-type');
                const unitPriceWrapper = component.querySelector('.additional-component-unit-price-wrapper');
                const hppMarginWrapper = component.querySelector('.additional-component-hpp-margin-wrapper');
                const hppInput = component.querySelector('.additional-component-hpp');
                const unitPriceInput = component.querySelector('.additional-component-unit-price');
                const marginInput = component.querySelector('.additional-component-margin');
                const calculatedPriceInput = component.querySelector('.additional-component-calculated-price');
                const removeButton = component.querySelector('.btn-remove-additional-component');

                function formatCompNumber(val) {
                    const raw = String(val || '').replace(/\D/g, '');
                    return raw ? new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Number(raw)) : '';
                }

                function recalcPrice() {
                    if (priceTypeSelect.value !== 'hpp_margin') {
                        calculatedPriceInput.value = 'Rp 0';
                        return;
                    }
                    const hpp = Number(String(hppInput.value || '').replace(/\./g, '')) || 0;
                    const margin = Number(marginInput.value) || 0;
                    if (hpp <= 0 || margin <= 0) {
                        calculatedPriceInput.value = 'Rp 0';
                        return;
                    }
                    const divisor = 1 - (margin / 100);
                    if (divisor <= 0) {
                        calculatedPriceInput.value = 'Rp 0';
                        return;
                    }
                    calculatedPriceInput.value = formatRupiah(hpp / divisor);
                }

                unitPriceInput.addEventListener('input', function() {
                    this.value = formatCompNumber(this.value);
                    triggerRealtimeCalculation();
                });

                hppInput.addEventListener('input', function() {
                    this.value = formatCompNumber(this.value);
                    recalcPrice();
                    triggerRealtimeCalculation();
                });

                marginInput.addEventListener('input', function() {
                    recalcPrice();
                    triggerRealtimeCalculation();
                });

                $(priceTypeSelect).on('change', function() {
                    if (this.value === 'hpp_margin') {
                        unitPriceWrapper.style.display = 'none';
                        hppMarginWrapper.style.display = 'block';
                        recalcPrice();
                    } else {
                        unitPriceWrapper.style.display = 'block';
                        hppMarginWrapper.style.display = 'none';
                        calculatedPriceInput.value = 'Rp 0';
                    }
                    triggerRealtimeCalculation();
                });

                $(component).find('.additional-component-type').on('change', triggerRealtimeCalculation);
                component.querySelector('.additional-component-name').addEventListener('input', triggerRealtimeCalculation);
                component.querySelector('.additional-component-qty').addEventListener('input', triggerRealtimeCalculation);

                removeButton.addEventListener('click', function() {
                    const selects = $(component).find('.select2-hidden-accessible');
                    selects.each(function() { $(this).select2('destroy'); });
                    component.remove();
                    updateAdditionalComponentNumbers();
                    triggerRealtimeCalculation();
                });
            }

            function updateAdditionalComponentNumbers() {
                const components = additionalComponentsContainer.querySelectorAll('.additional-component-item');
                components.forEach(function(comp, idx) {
                    const numEl = comp.querySelector('.additional-component-number');
                    if (numEl) numEl.textContent = idx + 1;
                });
            }

            if (addAdditionalComponentButton) {
                addAdditionalComponentButton.addEventListener('click', addAdditionalComponent);
            }

            /* =====================================================
               9. DISKON CONTROLS
            ===================================================== */
            discountRadios.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    const type = this.value;
                    if (type === 'none') {
                        discountValueField.hidden = true;
                        discountValueInput.disabled = true;
                        discountValueInput.value = '';
                    } else if (type === 'percentage') {
                        discountValueField.hidden = false;
                        discountValueInput.disabled = false;
                        discountValueInput.value = '';
                        discountSuffix.textContent = '%';
                        discountValueLabel.textContent = 'Persentase Diskon';
                        discountValueInput.placeholder = 'Contoh: 10';
                    } else if (type === 'nominal') {
                        discountValueField.hidden = false;
                        discountValueInput.disabled = false;
                        discountValueInput.value = '';
                        discountSuffix.textContent = 'Rp';
                        discountValueLabel.textContent = 'Nominal Diskon';
                        discountValueInput.placeholder = 'Contoh: 25.000';
                    }
                    triggerRealtimeCalculation();
                });
            });

            discountValueInput.addEventListener('input', function() {
                const selectedType = document.querySelector('input[name="discount_type"]:checked')?.value;
                if (selectedType === 'percentage') {
                    let val = this.value.replace(/[^0-9.]/g, '');
                    const parts = val.split('.');
                    if (parts.length > 2) val = parts[0] + '.' + parts.slice(1).join('');
                    this.value = val;
                } else if (selectedType === 'nominal') {
                    const raw = this.value.replace(/\D/g, '');
                    this.value = raw ? new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(Number(raw)) : '';
                }
                triggerRealtimeCalculation();
            });

            /* =====================================================
               10. MODAL CONTROLS (QUOTATION & TECHNICAL)
            ===================================================== */
            let lastCalculatedData = null;

            function updateQuotationCustomerInfo() {
                const custName = (document.getElementById('customer_name')?.value || '').trim() || 'Pelanggan Umum';
                const custPhone = (document.getElementById('customer_phone')?.value || '').trim() || '-';
                const locOption = locationSelect.find('option:selected');
                const locName = locOption.text().trim() || '-';
                const custNotes = (document.getElementById('customer_notes')?.value || '').trim();

                const elName = document.getElementById('modalQuoteCustomerName');
                const elPhone = document.getElementById('modalQuoteCustomerPhone');
                const elLoc = document.getElementById('modalQuoteCustomerLocation');
                const elNotesWrap = document.getElementById('modalQuoteNotesWrap');
                const elNotes = document.getElementById('modalQuoteCustomerNotes');

                if (elName) elName.textContent = custName;
                if (elPhone) elPhone.textContent = custPhone;
                if (elLoc) elLoc.textContent = locName;

                if (elNotesWrap && elNotes) {
                    if (custNotes) {
                        elNotesWrap.style.display = 'block';
                        elNotes.textContent = custNotes;
                    } else {
                        elNotesWrap.style.display = 'none';
                    }
                }
            }

            function openQuotationModal() {
                updateQuotationCustomerInfo();
                if (modalQuotation) modalQuotation.classList.add('is-open');
            }

            function closeQuotationModal() {
                if (modalQuotation) modalQuotation.classList.remove('is-open');
            }

            function openTechnicalModal() {
                if (modalTechnical) modalTechnical.classList.add('is-open');
            }

            function closeTechnicalModal() {
                if (modalTechnical) modalTechnical.classList.remove('is-open');
            }

            if (btnOpenQuotationModal) btnOpenQuotationModal.addEventListener('click', openQuotationModal);
            if (btnCloseQuotationModal) btnCloseQuotationModal.addEventListener('click', closeQuotationModal);
            if (btnCancelQuotationModal) btnCancelQuotationModal.addEventListener('click', closeQuotationModal);

            if (btnOpenTechnicalModal) btnOpenTechnicalModal.addEventListener('click', openTechnicalModal);
            if (btnCloseTechModal) btnCloseTechModal.addEventListener('click', closeTechnicalModal);
            if (btnCancelTechModal) btnCancelTechModal.addEventListener('click', closeTechnicalModal);

            if (btnPrintQuotationModal) {
                btnPrintQuotationModal.addEventListener('click', function() {
                    window.print();
                });
            }

            if (modalQuotation) {
                modalQuotation.addEventListener('click', function(e) {
                    if (e.target === modalQuotation) closeQuotationModal();
                });
            }
            if (modalTechnical) {
                modalTechnical.addEventListener('click', function(e) {
                    if (e.target === modalTechnical) closeTechnicalModal();
                });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeQuotationModal();
                    closeTechnicalModal();
                }
            });

            /* =====================================================
               11. REAL-TIME AUTO CALCULATION (DEBOUNCED 350ms)
            ===================================================== */
            let calcDebounceTimer = null;
            let isCalculating = false;

            async function executeRealtimeCalculation() {
                const locId = locationSelect.val();
                const catId = categorySelect.val();
                const matId = materialSelect.val();
                const isDisplay = isDisplayCategory();
                const isOutsourcing = isOutsourcingLocation();
                const vendorId = vendorSelect.val();

                // Validasi data minimal sebelum kalkulasi
                let isReady = true;
                if (!locId || !catId || !matId) isReady = false;
                if (isOutsourcing && !vendorId) isReady = false;

                const q = parseInt(qtyInput.value) || 0;
                if (q <= 0) isReady = false;

                if (!isDisplay) {
                    const l = parseFloat(lengthInput.value) || 0;
                    const w = parseFloat(widthInput.value) || 0;
                    if (l <= 0 || w <= 0) isReady = false;

                    const matSizeVal = materialSizeSelect.val();
                    if (!matSizeVal) isReady = false;
                    if (matSizeVal === '__custom__') {
                        const customW = parseFloat(materialCustomWidthInput.val()) || 0;
                        if (customW <= 0) isReady = false;
                        if (isOutsourcing && !materialPriceReferenceSelect.val()) isReady = false;
                    }
                }

                if (!isReady) {
                    if (liveEmptyState) liveEmptyState.style.display = 'block';
                    if (liveResultContainer) liveResultContainer.style.display = 'none';
                    return;
                }

                if (isCalculating) return;
                isCalculating = true;

                if (liveCalcSpinner) liveCalcSpinner.style.display = 'inline-block';

                try {
                    const formData = new FormData(form);

                    // Custom Material Width Handling
                    if (materialSizeSelect.val() === '__custom__') {
                        const customWidth = materialCustomWidthInput.val();
                        const referenceSizeId = isOutsourcing ?
                            materialPriceReferenceSelect.val() :
                            materialPriceSizeInput.val();

                        formData.delete('material_size_id');
                        formData.set('material_price_size_id', referenceSizeId || '');
                        formData.set('material_custom_width', customWidth || '');
                    }

                    // Custom Lamination Width Handling
                    if (laminationSizeSelect.val() === '__custom__') {
                        const customWidth = laminationCustomWidthInput.val();
                        const referenceSizeId = laminationPriceSizeInput.val();

                        formData.delete('lamination_size_id');
                        formData.set('lamination_price_size_id', referenceSizeId || '');
                        formData.set('lamination_custom_width', customWidth || '');
                    }

                    // Lamination "none"
                    if (formData.get('lamination_id') === 'none') {
                        formData.set('lamination_id', '');
                        formData.set('lamination_size_id', '');
                    }

                    // Clean Nominal Discount
                    if (formData.get('discount_type') === 'nominal') {
                        const rawDisc = String(formData.get('discount_value') || '').replace(/\./g, '');
                        formData.set('discount_value', rawDisc);
                    }

                    // Clean and filter additional components
                    const additionalComponents = additionalComponentsContainer.querySelectorAll('.additional-component-item');
                    additionalComponents.forEach(function(comp) {
                        const typeInput = comp.querySelector('.additional-component-type');
                        const nameInput = comp.querySelector('.additional-component-name');
                        const qtyVal = comp.querySelector('.additional-component-qty');
                        const priceTypeVal = comp.querySelector('.additional-component-price-type');
                        const unitPriceInput = comp.querySelector('.additional-component-unit-price');
                        const hppInput = comp.querySelector('.additional-component-hpp');
                        const marginInput = comp.querySelector('.additional-component-margin');

                        const type = String(typeInput?.value || '').trim();
                        const name = String(nameInput?.value || '').trim();
                        const qty = String(qtyVal?.value || '').trim();
                        const priceType = String(priceTypeVal?.value || '').trim();
                        const unitPrice = String(unitPriceInput?.value || '').trim();
                        const hpp = String(hppInput?.value || '').trim();
                        const margin = String(marginInput?.value || '').trim();

                        const isEmpty = !type && !name && qty === '1' && priceType === 'unit_price' && !unitPrice && !hpp && !margin;
                        if (isEmpty) {
                            const fields = comp.querySelectorAll('[name^="additional_components["]');
                            fields.forEach(f => formData.delete(f.name));
                        }
                    });

                    // Remove dots from additional component numeric inputs
                    const addInputs = additionalComponentsContainer.querySelectorAll('.additional-component-unit-price, .additional-component-hpp');
                    addInputs.forEach(function(inp) {
                        const raw = String(inp.value || '').replace(/\./g, '');
                        formData.set(inp.name, raw);
                    });

                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const res = await response.json();

                    if (!response.ok || !res.success) {
                        console.warn('Realtime calculation warning:', res.message || res.errors);
                        return;
                    }

                    const resultData = res.data;
                    lastCalculatedData = resultData;

                    // =========================================================
                    // 1. POPULATE LIVE SUMMARY PANEL (STICKY CARD KANAN)
                    // =========================================================
                    if (liveEmptyState) liveEmptyState.style.display = 'none';
                    if (liveResultContainer) liveResultContainer.style.display = 'block';

                    // Total Harga Jual
                    const elTotalSelling = document.getElementById('liveTotalSellingPrice');
                    if (elTotalSelling) elTotalSelling.textContent = formatRupiah(resultData.grand_total);

                    // Harga Satuan / Pcs
                    const elPerPcs = document.getElementById('liveSellingPricePerPcs');
                    const pcsPrice = Number(resultData.qty) > 0 ? (Number(resultData.grand_total) / Number(resultData.qty)) : 0;
                    if (elPerPcs) elPerPcs.textContent = formatRupiah(pcsPrice);

                    // Luas Tagihan Sub
                    const elBillingSub = document.getElementById('liveBillingAreaSub');
                    if (elBillingSub) elBillingSub.textContent = `${formatNumber(resultData.billing_area)} m²`;

                    // Badges
                    const elSizeBadge = document.getElementById('liveSizeBadge');
                    if (elSizeBadge) {
                        elSizeBadge.textContent = isDisplay ?
                            `Display (${resultData.qty} pcs)` :
                            `${formatNumber(resultData.length)} × ${formatNumber(resultData.width)} cm (${resultData.qty} pcs)`;
                    }

                    const elMinChargeBadge = document.getElementById('liveMinChargeBadge');
                    if (elMinChargeBadge) {
                        elMinChargeBadge.style.display = (Number(resultData.minimum_charge) > Number(resultData.production_area)) ?
                            'inline-flex' : 'none';
                    }

                    // Diskon Wrap
                    const discountWrap = document.getElementById('liveDiscountWrap');
                    if (discountWrap) {
                        if (Number(resultData.discount_amount) > 0) {
                            discountWrap.style.display = 'block';
                            document.getElementById('liveSubtotalPrice').textContent = formatRupiah(resultData.subtotal);
                            document.getElementById('liveDiscountAmount').textContent = '- ' + formatRupiah(resultData.discount_amount);
                            const discDesc = resultData.discount_type === 'percentage' ?
                                `${resultData.discount_value}%` : 'Nominal';
                            document.getElementById('liveDiscountDesc').textContent = discDesc;
                        } else {
                            discountWrap.style.display = 'none';
                        }
                    }

                    // Breakdown Rows
                    // 1. Material LF
                    const elMatDetail = document.getElementById('liveMaterialDetail');
                    if (elMatDetail) {
                        elMatDetail.textContent = isDisplay ?
                            `${resultData.material_name || '-'} (${resultData.qty} pcs)` :
                            `${resultData.material_name || '-'} · ${formatNumber(resultData.billing_area)} m² @ ${formatRupiah(resultData.material_price)}/m²`;
                    }
                    const elMatCost = document.getElementById('liveMaterialCost');
                    if (elMatCost) elMatCost.textContent = formatRupiah(resultData.material_subtotal);

                    // 2. Laminasi
                    const elLamRow = document.getElementById('liveLaminationRow');
                    if (elLamRow) {
                        if (resultData.lamination && Number(resultData.lamination.subtotal) > 0) {
                            elLamRow.style.display = 'flex';
                            document.getElementById('liveLaminationDetail').textContent = `${resultData.lamination.name || 'Laminasi'} (${formatNumber(resultData.lamination.billing_length)} m)`;
                            document.getElementById('liveLaminationCost').textContent = formatRupiah(resultData.lamination.subtotal);
                        } else {
                            elLamRow.style.display = 'none';
                        }
                    }

                    // 3. Komponen Tambahan
                    const elAddRow = document.getElementById('liveAdditionalRow');
                    if (elAddRow) {
                        if (Array.isArray(resultData.additional_components) && resultData.additional_components.length > 0) {
                            elAddRow.style.display = 'flex';
                            const totalCompCost = resultData.additional_components.reduce((sum, c) => sum + (Number(c.subtotal) || 0), 0);
                            document.getElementById('liveAdditionalDetail').textContent = `${resultData.additional_components.length} komponen tambahan`;
                            document.getElementById('liveAdditionalCost').textContent = formatRupiah(totalCompCost);
                        } else {
                            elAddRow.style.display = 'none';
                        }
                    }

                    // Total HPP Bar
                    const elTotalHppBadge = document.getElementById('liveTotalHppBadge');
                    if (elTotalHppBadge) elTotalHppBadge.textContent = 'HPP: ' + formatRupiah(resultData.total_hpp);
                    const elTotalHppVal = document.getElementById('liveTotalHppVal');
                    if (elTotalHppVal) elTotalHppVal.textContent = formatRupiah(resultData.total_hpp);
                    const elProfitVal = document.getElementById('liveProfitVal');
                    if (elProfitVal) elProfitVal.textContent = formatRupiah(resultData.profit);
                    const elMarginVal = document.getElementById('liveMarginVal');
                    if (elMarginVal) elMarginVal.textContent = formatPercentage(resultData.margin);

                    // =========================================================
                    // 2. POPULATE MODALS (SURAT PENAWARAN & DETAIL TEKNIS)
                    // =========================================================
                    const setTxt = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };

                    // A. MODAL 1: SURAT PENAWARAN (QUOTATION)
                    updateQuotationCustomerInfo();
                    const quoteLead = (resultData.estimation_time || document.getElementById('estimation_time')?.value || 1) + ' Hari Kerja';
                    setTxt('modalQuoteLeadTime', quoteLead);
                    setTxt('modalQuoteCategoryName', resultData.category_name || '-');
                    setTxt('modalQuoteMaterialName', resultData.material_name || '-');
                    setTxt('modalQuoteSizeQty', isDisplay ? `${resultData.qty} pcs` : `${formatNumber(resultData.length)} × ${formatNumber(resultData.width)} cm · ${resultData.qty} pcs`);
                    setTxt('modalQuoteBillingArea', `${formatNumber(resultData.billing_area)} m²`);

                    const finishingText = resultData.lamination ?
                        `${resultData.lamination.name} (${formatNumber(resultData.lamination.width)} cm)` :
                        'Tanpa Laminasi';
                    setTxt('modalQuoteFinishing', finishingText);

                    // Quotation Table Rows
                    let quoteRowsHtml = `
                        <tr>
                            <td style="padding: 10px;">
                                <strong style="color: var(--t-base);">Cetak Large Format (${resultData.material_name || '-'})</strong>
                                <div style="font-size: 11.5px; color: var(--t-muted); margin-top: 2px;">
                                    ${isDisplay ? 'Display Product' : `${formatNumber(resultData.length)} × ${formatNumber(resultData.width)} cm &middot; Lebar Bahan ${formatNumber(resultData.material_width)} cm`}
                                </div>
                            </td>
                            <td style="padding: 10px; text-align: center;">${formatNumber(resultData.billing_area)} m²</td>
                            <td style="padding: 10px; text-align: right;">${formatRupiah(resultData.material_price)}/m²</td>
                            <td style="padding: 10px; text-align: right; font-weight: 700;">${formatRupiah(resultData.material_subtotal)}</td>
                        </tr>
                    `;

                    if (resultData.lamination && Number(resultData.lamination.subtotal) > 0) {
                        quoteRowsHtml += `
                            <tr>
                                <td style="padding: 10px;">
                                    <strong style="color: var(--t-base);">Finishing: ${resultData.lamination.name || 'Laminasi'}</strong>
                                    <div style="font-size: 11.5px; color: var(--t-muted); margin-top: 2px;">Lapisan pelindung cetak roll</div>
                                </td>
                                <td style="padding: 10px; text-align: center;">${formatNumber(resultData.lamination.billing_length)} m</td>
                                <td style="padding: 10px; text-align: right;">${formatRupiah(resultData.lamination.price)}/${resultData.lamination.unit || 'm'}</td>
                                <td style="padding: 10px; text-align: right; font-weight: 700;">${formatRupiah(resultData.lamination.subtotal)}</td>
                            </tr>
                        `;
                    }

                    if (Array.isArray(resultData.additional_components) && resultData.additional_components.length > 0) {
                        resultData.additional_components.forEach(function(comp) {
                            quoteRowsHtml += `
                                <tr>
                                    <td style="padding: 10px;">
                                        <strong style="color: var(--t-base);">${comp.type ? comp.type.toUpperCase() + ': ' : ''}${comp.name || 'Komponen Tambahan'}</strong>
                                        <div style="font-size: 11.5px; color: var(--t-muted); margin-top: 2px;">Komponen pendukung produksi</div>
                                    </td>
                                    <td style="padding: 10px; text-align: center;">${(comp.qty || 1).toLocaleString('id-ID')}</td>
                                    <td style="padding: 10px; text-align: right;">${formatRupiah(comp.sale_price_per_unit || 0)}</td>
                                    <td style="padding: 10px; text-align: right; font-weight: 700;">${formatRupiah(comp.subtotal || 0)}</td>
                                </tr>
                            `;
                        });
                    }

                    const quoteTableBody = document.getElementById('modalQuoteTableBody');
                    if (quoteTableBody) quoteTableBody.innerHTML = quoteRowsHtml;

                    const quoteSubtotalRow = document.getElementById('modalQuoteSubtotalRow');
                    const quoteDiscountRow = document.getElementById('modalQuoteDiscountRow');
                    if (Number(resultData.discount_amount) > 0) {
                        if (quoteSubtotalRow) quoteSubtotalRow.style.display = 'table-row';
                        setTxt('modalQuoteSubtotalAmount', formatRupiah(resultData.subtotal));
                        if (quoteDiscountRow) quoteDiscountRow.style.display = 'table-row';
                        const discLbl = resultData.discount_type === 'percentage' ? `${resultData.discount_value}%` : 'Nominal';
                        setTxt('modalQuoteDiscountLabel', discLbl);
                        setTxt('modalQuoteDiscountAmount', '- ' + formatRupiah(resultData.discount_amount));
                    } else {
                        if (quoteSubtotalRow) quoteSubtotalRow.style.display = 'none';
                        if (quoteDiscountRow) quoteDiscountRow.style.display = 'none';
                    }
                    setTxt('modalQuoteGrandTotal', formatRupiah(resultData.grand_total));

                    // B. MODAL 2: DETAIL TEKNIS PRODUKSI & BIAYA (TECHNICAL)
                    setTxt('techLocationName', resultData.location_name || locationSelect.find('option:selected').text().trim() || '-');
                    const techVendorItem = document.getElementById('techVendorItem');
                    if (techVendorItem) {
                        if (resultData.vendor_name) {
                            techVendorItem.style.display = 'block';
                            setTxt('techVendorName', resultData.vendor_name);
                        } else {
                            techVendorItem.style.display = 'none';
                        }
                    }

                    setTxt('techPriceType', resultData.price_type === 'division' ? 'Harga Divisi' : 'Harga Umum');
                    setTxt('techCategoryName', resultData.category_name || '-');
                    setTxt('techMaterialName', resultData.material_name || '-');
                    setTxt('techFinishedSize', isDisplay ? '—' : `${formatNumber(resultData.length)} × ${formatNumber(resultData.width)} cm`);
                    setTxt('techMaterialWidth', isDisplay ? '—' : `${formatNumber(resultData.material_width)} cm`);
                    setTxt('techObjectsPerRow', isDisplay ? '—' : (resultData.objects_per_row || '-'));
                    setTxt('techTotalRows', isDisplay ? '—' : (resultData.total_rows || '-'));
                    setTxt('techProductionLength', isDisplay ? '—' : `${formatNumber(resultData.production_length / 100)} m`);
                    setTxt('techAreaCompare', `${formatNumber(resultData.production_area)} m² vs Min ${formatNumber(resultData.minimum_charge)} m²`);
                    setTxt('techBillingArea', `${formatNumber(resultData.billing_area)} m²`);
                    setTxt('techLeadTime', quoteLead);

                    // Technical Breakdown Rows
                    setTxt('techRow1Spec', isDisplay ? `${resultData.qty} pcs` : `${formatNumber(resultData.billing_area)} m² @ ${formatRupiah(resultData.material_price)}/m²`);
                    setTxt('techRow1Hpp', formatRupiah(resultData.material_total_hpp || 0));
                    setTxt('techRow1Subtotal', formatRupiah(resultData.material_subtotal || 0));

                    const techRowLam = document.getElementById('techRowLamination');
                    if (techRowLam) {
                        if (resultData.lamination && Number(resultData.lamination.subtotal) > 0) {
                            techRowLam.style.display = 'table-row';
                            setTxt('techRow2Spec', `${resultData.lamination.name} (${formatNumber(resultData.lamination.billing_length)} m @ ${formatRupiah(resultData.lamination.price)}/m)`);
                            setTxt('techRow2Hpp', formatRupiah(resultData.lamination.total_hpp || 0));
                            setTxt('techRow2Subtotal', formatRupiah(resultData.lamination.subtotal || 0));
                        } else {
                            techRowLam.style.display = 'none';
                        }
                    }

                    // Extra components repeater rows
                    const techExtraTbody = document.getElementById('techExtraComponentsRows');
                    if (techExtraTbody) {
                        techExtraTbody.innerHTML = '';
                        if (Array.isArray(resultData.additional_components) && resultData.additional_components.length > 0) {
                            resultData.additional_components.forEach(function(comp, idx) {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                    <td style="padding: 10px;">
                                        <strong>${idx + 3}. ${comp.type ? comp.type.toUpperCase() + ': ' : ''}${comp.name || 'Komponen Tambahan'}</strong>
                                        <div style="font-size: 11px; color: var(--t-muted);">${comp.price_type === 'hpp_margin' ? 'HPP + Margin' : 'Harga Satuan'}</div>
                                    </td>
                                    <td style="padding: 10px; font-size: 12.5px;">${(comp.qty || 1).toLocaleString('id-ID')} unit @ ${formatRupiah(comp.sale_price_per_unit || 0)}</td>
                                    <td style="padding: 10px; text-align: right; font-weight: 600;">${formatRupiah(comp.total_hpp || 0)}</td>
                                    <td style="padding: 10px; text-align: right; font-weight: 700; color: var(--primary);">${formatRupiah(comp.subtotal || 0)}</td>
                                `;
                                techExtraTbody.appendChild(tr);
                            });
                        }
                    }

                    // 4-Card KPI Financial Summary
                    setTxt('techKpiTotalHpp', formatRupiah(resultData.total_hpp));
                    setTxt('techKpiGrandTotal', formatRupiah(resultData.grand_total));
                    setTxt('techKpiProfit', formatRupiah(resultData.profit));
                    setTxt('techKpiMargin', formatPercentage(resultData.margin));
                    setTxt('techKpiMarkup', formatPercentage(resultData.markup));

                    // Technical Discount Rows & Banner
                    const techSubtotalRow = document.getElementById('techSubtotalRow');
                    const techDiscountRow = document.getElementById('techDiscountRow');
                    const techDiscountBanner = document.getElementById('techDiscountBanner');

                    if (Number(resultData.discount_amount) > 0) {
                        const discLbl = resultData.discount_type === 'percentage' ? `${resultData.discount_value}%` : 'Nominal';

                        if (techSubtotalRow) techSubtotalRow.style.display = 'table-row';
                        setTxt('techSubtotalAmount', formatRupiah(resultData.subtotal));

                        if (techDiscountRow) techDiscountRow.style.display = 'table-row';
                        setTxt('techDiscountLabel', discLbl);
                        setTxt('techDiscountAmount', '- ' + formatRupiah(resultData.discount_amount));

                        if (techDiscountBanner) {
                            techDiscountBanner.style.display = 'block';
                            setTxt('techBannerDiscountDesc', discLbl);
                            setTxt('techBannerOriginalSubtotal', formatRupiah(resultData.subtotal));
                            setTxt('techBannerDiscountVal', '- ' + formatRupiah(resultData.discount_amount));
                        }
                    } else {
                        if (techSubtotalRow) techSubtotalRow.style.display = 'none';
                        if (techDiscountRow) techDiscountRow.style.display = 'none';
                        if (techDiscountBanner) techDiscountBanner.style.display = 'none';
                    }

                    setTxt('techTotalHppCol', 'HPP: ' + formatRupiah(resultData.total_hpp));
                    setTxt('techGrandTotalCol', formatRupiah(resultData.grand_total));

                } catch (err) {
                    console.error('Calculation Error:', err);
                } finally {
                    isCalculating = false;
                    if (liveCalcSpinner) liveCalcSpinner.style.display = 'none';
                }
            }

            function triggerRealtimeCalculation() {
                clearTimeout(calcDebounceTimer);
                calcDebounceTimer = setTimeout(function() {
                    executeRealtimeCalculation();
                }, 350);
            }

            /* =====================================================
               12. INPUT LISTENERS UNTUK REAL-TIME CALCULATION
            ===================================================== */
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                executeRealtimeCalculation();
            });

            lengthInput.addEventListener('input', triggerRealtimeCalculation);
            widthInput.addEventListener('input', triggerRealtimeCalculation);
            qtyInput.addEventListener('input', triggerRealtimeCalculation);
            if (estTimeInput) estTimeInput.addEventListener('input', triggerRealtimeCalculation);
            materialCustomWidthInput.on('input', triggerRealtimeCalculation);
            laminationCustomWidthInput.on('input', triggerRealtimeCalculation);

            const custNameInput = document.getElementById('customer_name');
            const custPhoneInput = document.getElementById('customer_phone');
            const custNotesInput = document.getElementById('customer_notes');
            if (custNameInput) custNameInput.addEventListener('input', updateQuotationCustomerInfo);
            if (custPhoneInput) custPhoneInput.addEventListener('input', updateQuotationCustomerInfo);
            if (custNotesInput) custNotesInput.addEventListener('input', updateQuotationCustomerInfo);

            /* =====================================================
               13. RESET FORMULIR ESTIMASI (BERSIH & AMAN)
            ===================================================== */
            function resetFormEstimation() {
                clearTimeout(calcDebounceTimer);
                isCalculating = false;
                lastCalculatedData = null;

                form.reset();

                locationSelect.val('').trigger('change.select2');
                vendorSelect.val('').trigger('change.select2');
                categorySelect.val('').trigger('change.select2');
                materialSelect.empty().append('<option value="">Pilih Material</option>').val('').trigger('change.select2');
                materialSizeSelect.empty().append('<option value="">Pilih Lebar Bahan</option>').val('').trigger('change.select2');
                laminationSelect.val('none').trigger('change.select2');
                laminationSizeSelect.empty().append('<option value="">Pilih Ukuran Laminasi</option>').val('').trigger('change.select2');
                priceTypeSelect.val('general').trigger('change.select2');

                materialPriceSizeInput.val('');
                laminationPriceSizeInput.val('');
                resetDisplayIdentity();

                materialCustomWidthInput.val('').prop('disabled', true).prop('required', false).hide();
                materialPriceReferenceField.hide();
                materialPriceReferenceSelect.empty().append('<option value=""></option>').val('');
                laminationCustomWidthInput.val('').prop('disabled', true).prop('required', false).hide();

                toggleVendorField();
                toggleDisplayFields();

                // Hapus semua komponen tambahan dinamis
                const components = additionalComponentsContainer.querySelectorAll('.additional-component-item');
                components.forEach(function(comp) {
                    const selects = $(comp).find('.select2-hidden-accessible');
                    selects.each(function() { $(this).select2('destroy'); });
                    comp.remove();
                });
                additionalComponentIndex = 0;

                // Reset Diskon
                const discountNoneRadio = document.querySelector('input[name="discount_type"][value="none"]');
                if (discountNoneRadio) discountNoneRadio.checked = true;
                if (discountValueField) discountValueField.hidden = true;
                if (discountValueInput) {
                    discountValueInput.disabled = true;
                    discountValueInput.value = '';
                }
                if (discountSuffix) discountSuffix.textContent = '%';

                // Reset Pelanggan & Info Penawaran
                if (custNameInput) custNameInput.value = '';
                if (custPhoneInput) custPhoneInput.value = '';
                if (custNotesInput) custNotesInput.value = '';
                updateQuotationCustomerInfo();

                // Reset Summary Card
                updatePriceTypeBadge();
                if (liveEmptyState) liveEmptyState.style.display = 'block';
                if (liveResultContainer) liveResultContainer.style.display = 'none';
                if (liveCalcSpinner) liveCalcSpinner.style.display = 'none';
            }

            if (btnResetForm) {
                btnResetForm.addEventListener('click', resetFormEstimation);
            }

            /* =====================================================
               PRICE LIST LARGE FORMAT INTERACTIVE MODULE
            ===================================================== */
            let priceListRaw = @json($lfPriceList ?? null);

            let plState = {
                category: '{{ $defaultLfCat ?? 'Indoor' }}',
                group: 'all', // 'all' | 'general' | 'division' | 'plain'
                search: '',
                page: 1,
                pageSize: 10
            };

            const plCatSelect = $('#priceListCategoryFilter');
            const plPageSizeSelect = $('#priceListPageSize');
            const plSearchInput = document.getElementById('priceListSearch');
            const plSearchClear = document.getElementById('priceListSearchClear');
            const plTableBody = document.getElementById('lfPriceListBody');
            const plInfo = document.getElementById('priceListInfo');
            const plPagination = document.getElementById('priceListPagination');
            const currentCatBadge = document.getElementById('currentCatBadge');

            // Format Rupiah
            function formatPlRupiah(val) {
                if (val === null || val === undefined || isNaN(val) || Number(val) <= 0) return '<span style="color:var(--t-muted); opacity:0.4;">-</span>';
                return 'Rp ' + Math.round(Number(val)).toLocaleString('id-ID');
            }

            // Update Kolom Tampil / Sembunyi berdasarkan Tipe Harga
            function updateColumnVisibility() {
                const table = document.getElementById('lfPriceListTable');
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
            }

            // Render Body Tabel dan Pagination
            function renderPriceList() {
                if (!plTableBody || !priceListRaw || !Array.isArray(priceListRaw.details)) {
                    if (plTableBody) {
                        plTableBody.innerHTML = '<tr><td colspan="10" style="text-align:center; padding:28px; color:var(--t-muted);">Data price list belum tersedia.</td></tr>';
                    }
                    return;
                }

                // 1. Filter Kategori & Search
                const filtered = priceListRaw.details.filter(item => {
                    const matchCat = (plState.category === 'all') || (item.category_name === plState.category);
                    const matchSearch = (!plState.search) ||
                        (item.material_name && item.material_name.toLowerCase().includes(plState.search.toLowerCase())) ||
                        (item.widths_label && item.widths_label.toLowerCase().includes(plState.search.toLowerCase()));
                    return matchCat && matchSearch;
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
                                <div style="font-size: 14px; font-weight: 700; color: var(--t-base); margin-bottom: 4px;">Material Tidak Ditemukan</div>
                                <div style="font-size: 12.5px;">Tidak ada material yang cocok dengan filter kategori atau kata kunci pencarian.</div>
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
                            <div style="display:flex; flex-direction:column; gap:3px;">
                                <div style="display:flex; align-items:center; justify-content:space-between; gap:6px;">
                                    <span>${item.material_name}</span>
                                    ${plState.category === 'all' ? `<span class="badge" style="font-size:10px; padding:2px 6px; background:var(--bg-muted); color:var(--t-muted); border:1px solid var(--border);">${item.category_name}</span>` : ''}
                                </div>
                                ${item.vendor_name ? `<span style="font-size:10.5px; color:var(--primary); font-weight:600;"><i class="bi bi-building"></i> Vendor: ${item.vendor_name}</span>` : ''}
                            </div>
                        </td>`;

                        // Ukuran Lebar Badges
                        let widthsHtml = '-';
                        if (Array.isArray(item.widths) && item.widths.length > 0) {
                            widthsHtml = item.widths.map(w => `<span class="lf-width-badge">${w}</span>`).join('');
                        }
                        tr += `<td style="text-align:center;"><div style="display:flex; flex-wrap:wrap; gap:3px; justify-content:center;">${widthsHtml}</div></td>`;

                        // Biaya & HPP
                        tr += `<td class="details-digital-cost" style="text-align:right;">${formatPlRupiah(item.price_per_meter)}</td>`;
                        tr += `<td class="details-digital-cost" style="text-align:right;">${formatPlRupiah(item.production_cost)}</td>`;
                        tr += `<td class="details-digital-cost" style="text-align:right;">${formatPlRupiah(item.finishing_cost)}</td>`;
                        tr += `<td class="details-digital-cost details-digital-cost-total" style="text-align:right; font-weight:700;">${formatPlRupiah(item.total_cost || item.vendor_price)}</td>`;

                        // Harga Jual (Warna diatur oleh CSS yang solid dan adaptif terhadap tema)
                        tr += `<td class="details-digital-price col-price-general" style="text-align:right;">${formatPlRupiah(item.general_price)}</td>`;
                        tr += `<td class="details-digital-price col-price-division" style="text-align:right;">${formatPlRupiah(item.division_price)}</td>`;
                        tr += `<td class="details-digital-price col-price-plain" style="text-align:right;">${formatPlRupiah(item.plain_price)}</td>`;

                        tr += `</tr>`;
                        tbodyHtml += tr;
                    });

                    plTableBody.innerHTML = tbodyHtml;
                }

                // 4. Update Kolom Tampil/Sembunyi
                updateColumnVisibility();

                // 5. Update Info Bar
                const catLabel = plState.category === 'all' ? 'Semua Kategori' : `Kategori: ${plState.category}`;
                if (totalItems > 0) {
                    plInfo.innerHTML = `<i class="bi bi-info-circle me-1 text-primary"></i> Menampilkan <strong>${startIdx + 1}</strong> sampai <strong>${endIdx}</strong> dari <strong>${totalItems}</strong> material (${catLabel})`;
                } else {
                    plInfo.innerHTML = `<i class="bi bi-info-circle me-1 text-muted"></i> Tidak ada material (${catLabel})`;
                }

                // 6. Render Pagination Buttons
                renderPaginationButtons(totalPages, totalItems);
            }

            function renderPaginationButtons(totalPages, totalItems) {
                if (!plPagination) return;
                if (totalItems <= 0 || totalPages <= 1 || plState.pageSize === 'all') {
                    plPagination.innerHTML = '';
                    return;
                }

                let pagHtml = '';
                // Prev Button
                pagHtml += `<button type="button" class="price-page-btn" ${plState.page <= 1 ? 'disabled' : ''} data-page="${plState.page - 1}" title="Halaman Sebelumnya"><i class="bi bi-chevron-left"></i></button>`;

                // Numbered Buttons
                let startPage = Math.max(1, plState.page - 2);
                let endPage = Math.min(totalPages, startPage + 4);
                if (endPage - startPage < 4) {
                    startPage = Math.max(1, endPage - 4);
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

            // Inisialisasi Select2 pada Price List Toolbar
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

            // Search Filter
            if (plSearchInput) {
                plSearchInput.addEventListener('input', function() {
                    plState.search = this.value.trim();
                    if (plSearchClear) {
                        plSearchClear.style.display = plState.search ? 'block' : 'none';
                    }
                    plState.page = 1;
                    renderPriceList();
                });
            }

            if (plSearchClear) {
                plSearchClear.addEventListener('click', function() {
                    if (plSearchInput) plSearchInput.value = '';
                    plState.search = '';
                    this.style.display = 'none';
                    plState.page = 1;
                    renderPriceList();
                });
            }

            // Re-fetch saat lokasi diubah di form utama Large Format
            locationSelect.on('change', async function() {
                const locId = $(this).val();
                if (!locId) return;
                try {
                    const url = `{{ route('admin_get_pricelist_lf') }}?location_id=${locId}`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    const json = await res.json();
                    if (json.success && json.data) {
                        priceListRaw = json.data;
                        if (plCatSelect.length && Array.isArray(json.data.categories)) {
                            const curVal = plCatSelect.val();
                            plCatSelect.empty();
                            json.data.categories.forEach(cat => {
                                const opt = new Option(cat, cat);
                                plCatSelect.append(opt);
                            });
                            plCatSelect.append(new Option('Semua Kategori', 'all'));

                            if (json.data.categories.includes(curVal)) {
                                plCatSelect.val(curVal);
                            } else if (json.data.categories.length > 0) {
                                plCatSelect.val(json.data.categories[0]);
                            } else {
                                plCatSelect.val('all');
                            }
                            plCatSelect.trigger('change.select2');
                            plState.category = plCatSelect.val();
                        }
                        renderPriceList();
                    }
                } catch (err) {
                    console.error('Gagal mengambil price list Large Format untuk lokasi terpilih:', err);
                }
            });

            // Inisialisasi awal render tabel
            renderPriceList();

        });

        $(document).on('wheel', 'input[type="number"]', function() {
            this.blur();
        });
    </script>
@endsection

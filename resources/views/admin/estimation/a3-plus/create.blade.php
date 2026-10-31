@extends('admin_master')

@section('contents')
    <style>
        /* Responsive Grid System Konsisten dengan Brosur / Offset */
        .a3plus-estimator-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            width: 100%;
        }

        .a3plus-form-col {
            min-width: 0;
            width: 100%;
        }

        .a3plus-summary-col {
            min-width: 0;
            width: 100%;
        }

        .a3plus-matrix-col {
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

        /* Table Sticky Separator & Typography Enhancements */
        .details-digital-table td.details-digital-col-material,
        .details-digital-table th.details-digital-col-material {
            box-shadow: 3px 0 6px -2px rgba(0, 0, 0, 0.08);
        }

        .details-digital-table td.details-digital-price {
            font-variant-numeric: tabular-nums;
            font-weight: 700;
        }

        .details-digital-table td.details-digital-cost {
            font-variant-numeric: tabular-nums;
            background-color: rgba(0, 0, 0, 0.015);
        }

        .details-digital-table tbody tr:hover td {
            background-color: rgba(37, 99, 235, 0.04) !important;
        }

        /* Table Column Group Headers (Theme Adaptive, No Dull Blue) */
        .details-digital-table thead th.details-digital-group--general_single,
        .details-digital-table thead th.details-digital-group--general_double {
            background-color: rgba(245, 158, 11, 0.09) !important;
            color: #92400e !important;
            font-weight: 700;
            border-bottom: 1px solid rgba(245, 158, 11, 0.25);
        }

        .details-digital-table thead th.details-digital-group--division_single,
        .details-digital-table thead th.details-digital-group--division_double {
            background-color: rgba(139, 92, 246, 0.08) !important;
            color: #5b21b6 !important;
            font-weight: 700;
            border-bottom: 1px solid rgba(139, 92, 246, 0.25);
        }

        :root[data-theme="dark"] .details-digital-table thead th.details-digital-group--general_single,
        :root[data-theme="dark"] .details-digital-table thead th.details-digital-group--general_double {
            background-color: rgba(245, 158, 11, 0.16) !important;
            color: #fbbf24 !important;
            border-bottom-color: rgba(245, 158, 11, 0.35);
        }

        :root[data-theme="dark"] .details-digital-table thead th.details-digital-group--division_single,
        :root[data-theme="dark"] .details-digital-table thead th.details-digital-group--division_double {
            background-color: rgba(139, 92, 246, 0.15) !important;
            color: #c4b5fd !important;
            border-bottom-color: rgba(139, 92, 246, 0.35);
        }

        /* Markup Pill Styling - Amber / Gold for General, Violet / Lavender for Division */
        .markup-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 2px 7px;
            border-radius: 5px;
            font-size: 11px;
            font-weight: 700;
            line-height: 1.25;
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.02em;
        }

        /* Light Theme: Amber for General, Violet for Division */
        .markup-pill--general {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .markup-pill--division {
            background: #ede9fe;
            color: #5b21b6;
            border: 1px solid #ddd6fe;
        }

        /* Dark Theme: Vibrant Amber for General, Lavender for Division */
        :root[data-theme="dark"] .markup-pill--general {
            background: rgba(245, 158, 11, 0.2);
            color: #fde68a;
            border: 1px solid rgba(245, 158, 11, 0.45);
        }

        :root[data-theme="dark"] .markup-pill--division {
            background: rgba(139, 92, 246, 0.2);
            color: #ddd6fe;
            border: 1px solid rgba(139, 92, 246, 0.45);
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

        /* RESPONSIVE MEDIA QUERIES FOR PRICE LIST MATRIX */
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

        @media (min-width: 1024px) {
            .a3plus-estimator-grid {
                grid-template-columns: 1.25fr 1fr;
                gap: 24px;
                align-items: start;
            }

            .a3plus-sticky-card {
                position: sticky;
                top: 20px;
            }
        }

        /* Selling Price Box */
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

        /* Breakdown Row */
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
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(16, 185, 129, 0.02) 100%);
            border-color: rgba(16, 185, 129, 0.3);
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
            #modalA3plusTechnical {
                display: none !important;
            }

            #modalA3plusQuotation.modal-overlay {
                display: block !important;
                position: static !important;
                background: transparent !important;
                padding: 0 !important;
                opacity: 1 !important;
                visibility: visible !important;
                backdrop-filter: none !important;
            }

            #modalA3plusQuotation .modal-head-custom,
            #modalA3plusQuotation .modal-foot-custom {
                display: none !important;
            }

            #modalA3plusQuotation .modal-content-custom {
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
            animation: a3spin 1s linear infinite;
        }
        @keyframes a3spin {
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
                            ESTIMASI PRODUKSI DIGITAL PRINT A3+
                        </span>
                        <h1 class="hero-title" style="margin: 6px 0 8px;">
                            Buat Estimasi Digital Print A3+
                        </h1>
                        <p class="hero-sub" style="margin: 0;">
                            Kalkulasi cepat dan akurat cetak lembaran A3+ (Brosur, Kartu Nama, Flyer, Sertifikat, Display) dengan simulasi biaya bahan, klik mesin, laminasi, dan potong.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('admin_home') }}" class="btn-back-header">
                            <i class="bi bi-arrow-left"></i>
                            <span>Kembali</span>
                        </a>
                    </div>
                </section>

                <div class="a3plus-estimator-grid">

                    {{-- =========================================================
                        KOLOM KIRI: FORM ESTIMASI DIGITAL PRINT A3+
                    ========================================================== --}}
                    <div class="a3plus-form-col">
                        <section class="card" style="margin-bottom: 24px;">

                            {{-- CARD HEADER --}}
                            <div class="card-head">
                                <div class="card-title-wrap">
                                    <span class="eyebrow">
                                        FORMULIR SPESIFIKASI DIGITAL PRINT A3+
                                    </span>
                                </div>
                            </div>

                            {{-- FORM ESTIMASI A3+ --}}
                            <form action="{{ route('admin_store_a3plus') }}" method="POST" id="a3PlusEstimationForm">
                                @csrf

                            {{-- =========================================================
                                1. INFORMASI ESTIMASI & PELANGGAN
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
                                            @foreach ($locations ?? [] as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Pilihan Harga / Pricing Profile --}}
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

                                {{-- Catatan Produksi / Penawaran --}}
                                <div class="field" style="margin-bottom: 20px;">
                                    <label class="field-label" for="customer_notes">
                                        Catatan Khusus Penawaran / Produksi (Opsional)
                                    </label>
                                    <input type="text" id="customer_notes" name="customer_notes" class="input"
                                        placeholder="Contoh: Packing per 100 pcs plastik mika, cetak prioritas...">
                                </div>
                            </div>

                            {{-- =========================================================
                                2. MODE PRODUKSI & SPESIFIKASI CETAK
                            ========================================================== --}}
                            <div class="form-section">
                                <div class="form-section-head">
                                    <span class="eyebrow">
                                        SPESIFIKASI CETAK A3+
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
                                            @foreach ($categories ?? [] as $category)
                                                <option value="{{ $category->id }}"
                                                    data-name="{{ strtolower(trim($category->name)) }}"
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="field-help" id="categoryHelp">Pilih kategori untuk memfilter material
                                            atau produk.</small>
                                    </div>

                                    {{-- Warna Cetak --}}
                                    <div class="field" id="printColorField" style="margin-bottom: 20px;">
                                        <label class="field-label" for="print_color">
                                            Warna Cetak
                                            <span class="req">*</span>
                                        </label>

                                        <select id="print_color" name="print_color" class="select select2" required>
                                            <option value="fc" selected>Full Color (FC)</option>
                                            <option value="bw">Hitam Putih (B/W)</option>
                                        </select>
                                        <small class="field-help">Pilih mode warna cetak.</small>
                                    </div>
                                </div>

                                <div class="form-grid">
                                    {{-- Material A3+ / Komponen Display --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="material_id">
                                            <span id="materialLabelText">Material Kertas / Stiker</span>
                                            <span class="req">*</span>
                                        </label>

                                        <select name="material_id" id="material_id" class="select select2" required>
                                            <option value="">Pilih Lokasi &amp; Kategori terlebih dahulu</option>
                                        </select>
                                        <small class="field-help" id="materialHelp">
                                            Material akan ditampilkan otomatis berdasarkan Lokasi, Kategori, dan Warna
                                            Cetak.
                                        </small>

                                        {{-- Info Ringkas HPP Material dari Master --}}
                                        <div id="materialMasterInfo"
                                            style="display: none; font-size: 11.5px; color: #3b82f6; margin-top: 6px; padding: 6px 10px;  background: rgba(59, 130, 246, 0.14); border-radius: 6px;">
                                            <span>Paper Cost: <strong id="infoPaperCost">Rp 0</strong></span> &middot;
                                            <span>Biaya 1 Sisi: <strong id="infoSingleCost">Rp 0</strong></span> &middot;
                                            <span>Biaya 2 Sisi: <strong id="infoDoubleCost">Rp 0</strong></span>
                                        </div>
                                    </div>

                                    {{-- Sisi Cetak --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="print_side">
                                            Sisi Cetak
                                            <span class="req">*</span>
                                        </label>

                                        <select id="print_side" name="print_side" class="select select2" required>
                                            <option value="1" selected>1 Sisi</option>
                                            <option value="2">2 Sisi (Bolak-Balik)</option>
                                        </select>
                                        <small class="field-help">Pilih cetak 1 sisi atau 2 sisi bolak-balik.</small>
                                    </div>
                                </div>

                                {{-- Mode Produksi (Layout vs Direct Sheet - Khusus Non-Display) --}}
                                <div class="form-grid" id="productionModeWrapper">
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="production_mode">
                                            Mode Produksi
                                            <span class="req">*</span>
                                        </label>

                                        <select id="production_mode" name="production_mode" class="select select2"
                                            required>
                                            <option value="layout" selected>Hitung Layout (Hitung Muat per Lembar A3+)
                                            </option>
                                            <option value="direct">Cetak Langsung per Lembar (Direct Sheet)</option>
                                        </select>
                                        <small class="field-help" id="productionModeHelp">
                                            Mode layout otomatis menghitung berapa objek produk jadi yang muat di lembar
                                            A3+.
                                        </small>
                                    </div>
                                </div>

                                {{-- CONTAINER MODE DISPLAY (KHUSUS DISPLAY) --}}
                                <div id="displayFieldsContainer" style="display: none;">
                                    <div class="form-grid">
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="display_qty">
                                                Jumlah Paket (Box)
                                                <span class="req">*</span>
                                            </label>
                                            <div class="input-suffix">
                                                <input id="display_qty" class="input" type="number" min="1"
                                                    step="1" placeholder="Contoh: 1" value="1">
                                            </div>
                                            <small class="field-help" id="displayQtyHelp">1 Paket = 100 pcs (Kebutuhan
                                                Cetak: 4 Lembar A3+).</small>
                                        </div>
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label">Informasi Produk Display</label>
                                            <div
                                                style="padding: 10px 14px; border-radius: 8px; background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.2); font-size: 13px; color: #1e40af;">
                                                <i class="bi bi-box-seam me-1"></i>
                                                <span id="displayPackageSummary">Paket display siap cetak lengkap dengan
                                                    Box KN dan Finishing Die Cut.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- CONTAINER MODE HITUNG LAYOUT (DEFAULT) --}}
                                <div id="layoutFieldsContainer">
                                    <div class="form-grid">
                                        {{-- Panjang Jadi --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="length">
                                                Panjang Jadi (cm)
                                                <span class="req">*</span>
                                            </label>
                                            <div class="input-suffix">
                                                <input id="length" name="length" class="input" type="number"
                                                    min="0.1" step="0.1" inputmode="decimal"
                                                    placeholder="Contoh: 9" value="{{ old('length') }}">
                                            </div>
                                            <small class="field-help">Ukuran panjang potongan produk jadi.</small>
                                        </div>

                                        {{-- Lebar Jadi --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="width">
                                                Lebar Jadi (cm)
                                                <span class="req">*</span>
                                            </label>
                                            <div class="input-suffix">
                                                <input id="width" name="width" class="input" type="number"
                                                    min="0.1" step="0.1" inputmode="decimal"
                                                    placeholder="Contoh: 5.5" value="{{ old('width') }}">
                                            </div>
                                            <small class="field-help">Ukuran lebar potongan produk jadi.</small>
                                        </div>
                                    </div>

                                    <div class="form-grid">
                                        {{-- Jumlah Produk Jadi (pcs) --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="qty">
                                                Jumlah Produk Jadi (pcs)
                                                <span class="req">*</span>
                                            </label>
                                            <div class="input-suffix">
                                                <input id="qty" name="qty" class="input" type="number"
                                                    min="1" step="1" inputmode="numeric"
                                                    placeholder="Contoh: 500" value="{{ old('qty') }}">
                                            </div>
                                            <small class="field-help">Total kuantiti pcs produk jadi yang diinginkan
                                                pelanggan.</small>
                                        </div>

                                        {{-- Info Card Imposisi Realtime --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label">
                                                Hasil Imposisi Lembar
                                            </label>
                                            <div class="estimation-info"
                                                style="border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px; background: rgba(0,0,0,0.02);">
                                                <div
                                                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                                    <span style="font-size: 12px; color: var(--text-muted, #64748b);">Muat
                                                        per Lembar:</span>
                                                    <strong id="layoutUpCount" style="font-size: 14px;">—</strong>
                                                </div>
                                                <div
                                                    style="display: flex; justify-content: space-between; align-items: center;">
                                                    <span
                                                        style="font-size: 12px; color: var(--text-muted, #64748b);">Kebutuhan
                                                        Lembar A3+:</span>
                                                    <strong id="layoutTotalSheets"
                                                        style="font-size: 14px; color: var(--primary, #1e3a8a);">—</strong>
                                                </div>
                                            </div>
                                            <small class="field-help" id="impositionNotes">Dihitung otomatis berdasarkan
                                                ukuran lembar A3+ (31.5 × 47.5 cm area cetak).</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- CONTAINER MODE CETAK LANGSUNG (DIRECT SHEET) --}}
                                <div id="directSheetFieldsContainer" style="display: none;">
                                    <div class="form-grid">
                                        {{-- Ukuran Lembar --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="sheet_size_id">
                                                Ukuran Lembar A3+
                                                <span class="req">*</span>
                                            </label>
                                            <select id="sheet_size_id" name="sheet_size_id" class="select select2">
                                                <option value="standard" selected>A3+ Standar (32.5 × 48.5 cm)</option>
                                                <option value="custom">Ukuran Khusus</option>
                                            </select>
                                        </div>

                                        {{-- Jumlah Lembar Cetak --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="sheet_qty">
                                                Jumlah Lembar Cetak
                                                <span class="req">*</span>
                                            </label>
                                            <div class="input-suffix">
                                                <input id="sheet_qty" name="sheet_qty" class="input" type="number"
                                                    min="1" step="1" inputmode="numeric"
                                                    placeholder="Contoh: 100" value="{{ old('sheet_qty') }}">
                                            </div>
                                            <small class="field-help">Langsung cetak per lembar tanpa pemotongan
                                                n-up.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- =========================================================
                                3. FINISHING: LAMINASI & CUTTING
                            ========================================================== --}}
                            <div class="form-section" id="finishingSection">
                                <div class="form-section-head">
                                    <span class="eyebrow">
                                        FINISHING: LAMINASI &amp; CUTTING
                                    </span>
                                </div>

                                <div class="form-grid">
                                    {{-- Laminasi --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="lamination_type">
                                            Finishing Laminasi
                                        </label>

                                        <select id="lamination_type" name="lamination_type" class="select select2">
                                            <option value="none" selected>Tanpa Laminasi</option>
                                            @foreach ($laminations ?? [] as $lam)
                                                <option value="{{ $lam->id }}_1"
                                                    data-lamination-id="{{ $lam->id }}" data-side="1"
                                                    data-paper-cost="{{ $lam->paper_cost ?? 0 }}"
                                                    data-click-cost="{{ $lam->click_cost ?? 0 }}"
                                                    data-cost="{{ $lam->single_print_cost ?? 0 }}">
                                                    {{ $lam->name }} (1 Sisi)
                                                </option>
                                                <option value="{{ $lam->id }}_2"
                                                    data-lamination-id="{{ $lam->id }}" data-side="2"
                                                    data-paper-cost="{{ $lam->paper_cost ?? 0 }}"
                                                    data-click-cost="{{ $lam->click_cost ?? 0 }}"
                                                    data-cost="{{ $lam->double_print_cost ?? 0 }}">
                                                    {{ $lam->name }} (2 Sisi)
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="field-help" id="laminationHelp">
                                            Pilihan laminasi aktif dari Master Biaya Produksi Laminasi Digital Print A3+.
                                        </small>

                                        {{-- Info Ringkas HPP Laminasi dari Master --}}
                                        <div id="laminationMasterInfo"
                                            style="display: none; font-size: 11.5px; color: rgb(59, 130, 246); margin-top: 6px; padding: 6px 10px; background: rgba(59, 130, 246, 0.14); border-radius: 6px;">
                                            <span>Biaya Dasar Laminasi: <strong id="infoLaminationCost">Rp 0</strong>
                                                /lbr</span>
                                        </div>
                                    </div>

                                    {{-- Cutting / Potong --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="cutting_type">
                                            Finishing Cutting / Potong
                                        </label>

                                        <select id="cutting_type" name="cutting_type" class="select select2">
                                            <option value="none" selected>Tanpa Cutting / Tanpa Potong</option>
                                            @foreach ($cuttings ?? [] as $cut)
                                                @php
                                                    $desc = '';
                                                    $lowerName = strtolower(trim($cut->name));
                                                    $isPotong = ($lowerName === 'potong');
                                                    if ($isPotong) {
                                                        $desc = ' Lurus (Guillotine)';
                                                    } elseif (str_contains($lowerName, 'kiss')) {
                                                        $desc = ' (Cutting Sticker Setengah Putus)';
                                                    } elseif (str_contains($lowerName, 'die')) {
                                                        $desc = ' (Cutting Sticker Putus)';
                                                    } elseif (str_contains($lowerName, 'perforasi')) {
                                                        $desc = ' (Garis Cacah)';
                                                    }
                                                    $val = $isPotong ? 'potong' : $cut->id;
                                                @endphp
                                                <option value="{{ $val }}" {{ old('cutting_type') == $val ? 'selected' : '' }}>
                                                    {{ $cut->name }}{{ $desc }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="field-help" id="cuttingHelpText">
                                            Pilihan jasa potong aktif dari Master Biaya Produksi Finishing Jasa Potong.
                                        </small>

                                        {{-- Info Ringkas HPP Cutting dari Master --}}
                                        <div id="cuttingMasterInfo"
                                            style="display: none; font-size: 11.5px; color: rgb(16, 185, 129); margin-top: 6px; padding: 6px 10px; background: rgba(16, 185, 129, 0.14); border-radius: 6px;">
                                            <span>HPP Mesin Cutting: <strong>Rp 1.000</strong> /lbr</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Opsi Potong Override (Hanya muncul jika Potong dipilih) --}}
                                <div class="form-grid" id="potongOverrideContainer" style="display: none;">
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="potong_size_override">
                                            Kategori Ukuran Potong
                                        </label>
                                        <select id="potong_size_override" name="potong_size_override"
                                            class="select select2">
                                            <option value="" selected>Otomatis (Berdasarkan Luas Ukuran Jadi)
                                            </option>
                                            <option value="A3">A3 (Besar / 29.7 × 42 cm)</option>
                                            <option value="A4">A4 (Standar / 21 × 29.7 cm)</option>
                                            <option value="A5">A5 (Sedang / 14.8 × 21 cm)</option>
                                            <option value="<=A6">≤ A6 (Kecil / Kartu Nama)</option>
                                        </select>
                                        <small class="field-help" id="potongOverrideHelp">
                                            Biaya potong dihitung per batch produksi berdasarkan tarif ongkos potong minimal
                                            dari master data.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Notice Finishing untuk Display --}}
                            <div id="displayFinishingNotice" class="form-section"
                                style="display: none; margin-bottom: 20px;">
                                <div
                                    style="padding: 14px 18px; border-radius: 8px; background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); font-size: 13.5px; color: #065f46;">
                                    <i class="bi bi-check-circle-fill me-2 text-success"></i>
                                    <strong>Finishing Otomatis:</strong> Ongkos Die Cut dan Box KN sudah otomatis
                                    diperhitungkan ke dalam paket Display.
                                </div>
                            </div>

                            {{-- =========================================================
                                4. KOMPONEN TAMBAHAN
                            ========================================================== --}}
                            <div class="form-section"
                                style="margin-top: 20px; margin-bottom: 20px; display: flex; flex-direction: column; gap: 20px;">

                                <div class="form-section-header"
                                    style="display: flex; flex-direction: column; gap: 15px;">
                                    <div>
                                        <div class="form-section-title">
                                            Komponen Tambahan
                                        </div>
                                        <div class="form-section-description eyebrow">
                                            Tambahkan komponen tambahan / jasa khusus yang digunakan dalam estimasi
                                            (opsional).
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn--primary" id="addAdditionalComponentButton"
                                        style="max-width: 220px;">
                                        + Tambah Komponen
                                    </button>
                                </div>

                                <div id="additionalComponentsContainer">
                                    {{-- Komponen tambahan akan digenerate via JavaScript --}}
                                </div>
                            </div>

                            {{-- =========================================================
                                5. DISKON & LEAD TIME
                            ========================================================== --}}
                            <div class="form-section">
                                <div class="form-section-head">
                                    <span class="eyebrow">
                                        DISKON &amp; ESTIMASI WAKTU
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
                                6. STATUS & RESET
                            ========================================================== --}}
                            <div class="form-actions" style="padding: 16px 20px; border-top: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                                <span class="badge dot success" id="realtimeStatusBadge">
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
                    KOLOM KANAN: STICKY LIVE SUMMARY (KONSISTEN DENGAN BROSUR)
                ========================================================== --}}
                <div class="a3plus-summary-col">
                    <section class="card a3plus-sticky-card">

                        {{-- CARD HEAD DENGAN GRADASI BIRU KHAS DIGITAL PRINT A3+ --}}
                        <div class="card-head"
                            style="background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color: #fff; padding: 18px 20px; border-radius: 13px 13px 0 0; margin-bottom: 0;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                                <div>
                                    <span class="eyebrow" style="color: #93c5fd; font-size: 11px; letter-spacing: 0.8px;">
                                        HASIL KALKULASI REAL-TIME
                                    </span>
                                    <h2 style="font-size: 1.15rem; font-weight: 700; color: #ffffff; margin: 4px 0 0;">
                                        Ringkasan Estimasi Digital A3+
                                    </h2>
                                </div>
                                <span id="livePriceTypeBadge" class="badge"
                                    style="background: rgba(255,255,255,0.18); color: #fff; border: 1px solid rgba(255,255,255,0.3); padding: 4px 10px; border-radius: 20px; font-size: 11.5px;">
                                    Harga Umum
                                </span>
                            </div>
                        </div>

                        <div style="padding: 20px;">
                            {{-- INITIAL PLACEHOLDER (KETIKA BELUM ADA DATA LENGKAP) --}}
                            <div id="liveEmptyState" style="text-align: center; padding: 36px 16px; color: var(--t-muted);">
                                <div style="width: 54px; height: 54px; margin: 0 auto 14px; border-radius: 14px; background: rgba(59, 130, 246, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 24px;">
                                    <i class="bi bi-calculator"></i>
                                </div>
                                <h4 style="font-size: 15px; font-weight: 700; color: var(--t-base); margin: 0 0 6px;">Kalkulasi Otomatis Siap</h4>
                                <p style="font-size: 12.5px; margin: 0; line-height: 1.5;">
                                    Silakan lengkapi pilihan <strong>Material</strong> dan <strong>Kuantiti</strong> cetak untuk melihat kalkulasi harga real-time.
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
                                            <span class="sub-label">Kebutuhan Bahan:</span>
                                            <strong id="liveTotalSheetsSub" class="sub-val">0 Lembar A3+</strong>
                                        </div>
                                    </div>

                                    <div style="margin-top: 10px; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                        <small class="rounding-pill">
                                            <i class="bi bi-check2-circle"></i> <span id="liveImpositionBadge">1 Lembar</span>
                                        </small>
                                        <small class="rounding-pill" id="liveColorSideBadge" style="background: rgba(59, 130, 246, 0.1); color: var(--primary);">
                                            <i class="bi bi-printer"></i> Full Color · 1 Sisi
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

                                        {{-- 1. Cetak Lembaran A3+ --}}
                                        <div class="breakdown-row">
                                            <div>
                                                <div class="breakdown-row-title">1. Biaya Cetak A3+ (Bahan &amp; Klik)</div>
                                                <small id="livePrintDetail" class="breakdown-row-sub">-</small>
                                            </div>
                                            <strong id="livePrintCost" class="breakdown-row-cost">Rp 0</strong>
                                        </div>

                                        {{-- 2. Laminasi --}}
                                        <div class="breakdown-row" id="liveLaminationRow" style="display: none;">
                                            <div>
                                                <div class="breakdown-row-title">2. Finishing Laminasi</div>
                                                <small id="liveLaminationDetail" class="breakdown-row-sub">Tanpa Laminasi</small>
                                            </div>
                                            <strong id="liveLaminationCost" class="breakdown-row-cost">Rp 0</strong>
                                        </div>

                                        {{-- 3. Cutting / Potong --}}
                                        <div class="breakdown-row" id="liveCuttingRow" style="display: none;">
                                            <div>
                                                <div class="breakdown-row-title">3. Finishing Potong / Cutting</div>
                                                <small id="liveCuttingDetail" class="breakdown-row-sub">Tanpa Cutting</small>
                                            </div>
                                            <strong id="liveCuttingCost" class="breakdown-row-cost">Rp 0</strong>
                                        </div>

                                        {{-- 4. Komponen Tambahan (Jika ada) --}}
                                        <div class="breakdown-row" id="liveExtraRow" style="display: none;">
                                            <div>
                                                <div class="breakdown-row-title">4. Komponen Tambahan</div>
                                                <small id="liveExtraDetail" class="breakdown-row-sub">-</small>
                                            </div>
                                            <strong id="liveExtraCost" class="breakdown-row-cost">Rp 0</strong>
                                        </div>

                                        {{-- TOTAL HPP BAR --}}
                                        <div class="total-hpp-bar">
                                            <div>
                                                <strong class="total-hpp-bar-title">TOTAL HPP PRODUKSI</strong>
                                                <div class="total-hpp-bar-sub">Profit: <span id="liveProfitVal" style="color: #10b981; font-weight: 700;">Rp 0</span> (Margin: <span id="liveMarginVal">0%</span>)</div>
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
                    BAGIAN BAWAH: PRICE LIST DIGITAL PRINT A3+ (FULL WIDTH)
                ========================================================== --}}
                <div class="a3plus-matrix-col">
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
                                            <i class="bi bi-folder2-open me-1"></i> Kategori: BC
                                        </span>
                                    </div>
                                    <h3 class="matrix-title">
                                        Price List Bertingkat Cetak Lembaran A3+
                                    </h3>
                                    <p class="matrix-subtitle">
                                        Daftar HPP (Kertas &amp; Klik Mesin) dan Rekomendasi Harga Jual Bertingkat (Harga Umum &amp; Harga Divisi).
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
                                            $categoriesList = $digitalPriceList['categories'] ?? [];
                                            $hasBc = in_array('BC', $categoriesList);
                                            $hasBw = in_array('BW', $categoriesList);
                                            $defaultCat = $hasBc ? 'BC' : ($hasBw ? 'BW' : ($categoriesList[0] ?? 'all'));
                                        @endphp
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
                                            placeholder="Cari nama material / ukuran..." autocomplete="off">
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

                                {{-- BARIS 2: TAB SWITCHER TIPE HARGA & SISI CETAK --}}
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

                                    {{-- Tab Filter Sisi Cetak (Semua / 1 Muka / 2 Muka) --}}
                                    <div class="matrix-tab-cluster">
                                        <span class="matrix-tab-cluster-label">Sisi Cetak:</span>
                                        <div class="matrix-tab-container" id="printSideTabWrap">
                                            <button type="button" class="matrix-tab-btn is-active" data-side="all" title="Semua Sisi Cetak">
                                                Semua Sisi
                                            </button>
                                            <button type="button" class="matrix-tab-btn" data-side="single" title="Print 1 Muka">
                                                1 Muka
                                            </button>
                                            <button type="button" class="matrix-tab-btn" data-side="double" title="Print 2 Muka">
                                                2 Muka
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CARD BODY: TABEL PRICE LIST BERTINGKAT --}}
                        <div style="padding: 16px 20px;">
                            <div class="details-digital-table-wrapper">
                                <table class="details-digital-table" id="a3plusPriceListTable">
                                    <thead>
                                        <tr class="details-digital-group-row">
                                            <th rowspan="3" class="details-digital-sticky details-digital-col-no" style="font-weight: 700;">No</th>
                                            <th rowspan="3" class="details-digital-sticky details-digital-col-material" style="font-weight: 700;">Item Material</th>
                                            <th rowspan="3" style="font-weight: 700;">Ukuran</th>
                                            <th rowspan="3" class="details-digital-cost" style="font-weight: 700;">Kertas</th>
                                            <th rowspan="3" class="details-digital-cost" style="font-weight: 700;">Klik</th>
                                            <th rowspan="3" class="details-digital-cost" style="font-weight: 700;">Cost Print<br>1 Sisi</th>
                                            <th rowspan="3" class="details-digital-cost" style="font-weight: 700;">Cost Print<br>2 Sisi</th>

                                            <th colspan="6" class="details-digital-group details-digital-group--general_single col-tier-gen-1">
                                                Harga Umum · Print 1 Muka
                                            </th>
                                            <th colspan="6" class="details-digital-group details-digital-group--general_double col-tier-gen-2">
                                                Harga Umum · Print 2 Muka
                                            </th>
                                            <th colspan="6" class="details-digital-group details-digital-group--division_single col-tier-div-1">
                                                Harga Divisi · Print 1 Muka
                                            </th>
                                            <th colspan="6" class="details-digital-group details-digital-group--division_double col-tier-div-2">
                                                Harga Divisi · Print 2 Muka
                                            </th>
                                        </tr>

                                        <tr class="details-digital-tier-row">
                                            {{-- General Single Tiers --}}
                                            <th class="details-digital-group--general_single col-tier-gen-1" style="font-weight: 600;">1 lbr</th>
                                            <th class="details-digital-group--general_single col-tier-gen-1" style="font-weight: 600;">2-5 lbr</th>
                                            <th class="details-digital-group--general_single col-tier-gen-1" style="font-weight: 600;">6-50 lbr</th>
                                            <th class="details-digital-group--general_single col-tier-gen-1" style="font-weight: 600;">100 lbr</th>
                                            <th class="details-digital-group--general_single col-tier-gen-1" style="font-weight: 600;">300 lbr</th>
                                            <th class="details-digital-group--general_single col-tier-gen-1" style="font-weight: 600;">500 lbr</th>

                                            {{-- General Double Tiers --}}
                                            <th class="details-digital-group--general_double col-tier-gen-2" style="font-weight: 600;">1 lbr</th>
                                            <th class="details-digital-group--general_double col-tier-gen-2" style="font-weight: 600;">2-5 lbr</th>
                                            <th class="details-digital-group--general_double col-tier-gen-2" style="font-weight: 600;">6-50 lbr</th>
                                            <th class="details-digital-group--general_double col-tier-gen-2" style="font-weight: 600;">100 lbr</th>
                                            <th class="details-digital-group--general_double col-tier-gen-2" style="font-weight: 600;">300 lbr</th>
                                            <th class="details-digital-group--general_double col-tier-gen-2" style="font-weight: 600;">500 lbr</th>

                                            {{-- Division Single Tiers --}}
                                            <th class="details-digital-group--division_single col-tier-div-1" style="font-weight: 600;">1 lbr</th>
                                            <th class="details-digital-group--division_single col-tier-div-1" style="font-weight: 600;">2-5 lbr</th>
                                            <th class="details-digital-group--division_single col-tier-div-1" style="font-weight: 600;">6-50 lbr</th>
                                            <th class="details-digital-group--division_single col-tier-div-1" style="font-weight: 600;">100 lbr</th>
                                            <th class="details-digital-group--division_single col-tier-div-1" style="font-weight: 600;">300 lbr</th>
                                            <th class="details-digital-group--division_single col-tier-div-1" style="font-weight: 600;">500 lbr</th>

                                            {{-- Division Double Tiers --}}
                                            <th class="details-digital-group--division_double col-tier-div-2" style="font-weight: 600;">1 lbr</th>
                                            <th class="details-digital-group--division_double col-tier-div-2" style="font-weight: 600;">2-5 lbr</th>
                                            <th class="details-digital-group--division_double col-tier-div-2" style="font-weight: 600;">6-50 lbr</th>
                                            <th class="details-digital-group--division_double col-tier-div-2" style="font-weight: 600;">100 lbr</th>
                                            <th class="details-digital-group--division_double col-tier-div-2" style="font-weight: 600;">300 lbr</th>
                                            <th class="details-digital-group--division_double col-tier-div-2" style="font-weight: 600;">500 lbr</th>
                                        </tr>

                                        {{-- Markup Row (Warna Amber & Violet Kontras) --}}
                                        <tr class="details-digital-markup-row" id="markupHeaderRow">
                                            {{-- Populated dynamically by JavaScript based on active category --}}
                                        </tr>
                                    </thead>
                                    <tbody id="a3plusPriceListBody">
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
        MODAL 1: PREVIEW & CETAK SURAT PENAWARAN (SURAT PENAWARAN CETAK)
    ========================================================== --}}
    <div class="modal-overlay" id="modalA3plusQuotation">
        <div class="modal modal-content-custom" style="max-width: 750px;">

            {{-- MODAL HEAD --}}
            <div class="modal-head-custom">
                <div>
                    <span class="eyebrow" style="color: var(--primary); font-weight: 700;">PREVIEW PENAWARAN HARGA</span>
                    <h3 class="modal-title-custom">Surat Penawaran Cetak Digital A3+</h3>
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
                        <div style="font-size: 12px; color: var(--t-muted);">Printing Management System &middot; Estimator Digital Print A3+</div>
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
                        <div>Produk: <strong id="modalQuoteProductName">Digital Print A3+</strong></div>
                        <div>Kertas: <strong id="modalQuoteMaterialName">-</strong></div>
                        <div>Sisi &amp; Warna: <strong id="modalQuotePrintSide">-</strong></div>
                        <div>Kuantitas Jadi: <strong id="modalQuoteQty">-</strong></div>
                        <div>Finishing: <strong id="modalQuoteFinishing">-</strong></div>
                    </div>
                </div>

                {{-- Table Summary Penawaran --}}
                <table class="printable-table">
                    <thead>
                        <tr>
                            <th style="padding: 8px 10px; text-align: left;">Item Deskripsi</th>
                            <th style="padding: 8px 10px; text-align: center;">Jumlah</th>
                            <th style="padding: 8px 10px; text-align: right;">Harga Satuan</th>
                            <th style="padding: 8px 10px; text-align: right;">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody id="modalQuoteTableBody">
                        <tr>
                            <td style="padding: 10px;">
                                <strong id="modalQuoteItemTitle">Cetak Digital Print A3+</strong>
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
                    * Penawaran ini berlaku selama 14 hari sejak tanggal diterbitkan. Harga sudah termasuk cetak mesin digital A3+, material kertas pilihan, dan finishing yang dipilih.
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
        MODAL 2: DETAIL RINCIAN ESTIMASI (HPP & BIAYA LENGKAP)
    ========================================================== --}}
    <div class="modal-overlay" id="modalA3plusTechnical">
        <div class="modal modal-content-custom" style="max-width: 820px;">

            {{-- MODAL HEAD --}}
            <div class="modal-head-custom">
                <div>
                    <span class="eyebrow" style="color: var(--primary); font-weight: 700;">RINCIAN TEKNIS PRODUKSI &amp; BIAYA</span>
                    <h3 class="modal-title-custom">Detail Rincian Estimasi Digital Print A3+</h3>
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
                        <i class="bi bi-gear-fill"></i> PARAMETER PRODUKSI &amp; IMPOSISI
                    </div>

                    <div class="tech-spec-grid">
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Mesin Produksi</div>
                            <div class="tech-spec-value">Digital Print A3+</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Lokasi Cabang</div>
                            <div class="tech-spec-value" id="techLocationName">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Profil Harga</div>
                            <div class="tech-spec-value" id="techPriceType">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Material Kertas</div>
                            <div class="tech-spec-value" id="techMaterialName">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Warna &amp; Sisi Cetak</div>
                            <div class="tech-spec-value" id="techPrintColorSide">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Mode Produksi</div>
                            <div class="tech-spec-value" id="techProductionMode">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Ukuran Jadi</div>
                            <div class="tech-spec-value" id="techFinishedSize">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Hasil Imposisi</div>
                            <div class="tech-spec-value" id="techImposition">-</div>
                        </div>
                        <div class="tech-spec-item">
                            <div class="tech-spec-label">Kebutuhan Bahan</div>
                            <div class="tech-spec-value" id="techTotalSheets" style="color: var(--primary);">-</div>
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
                                <th style="padding: 10px; text-align: left;">Spesifikasi / Parameter</th>
                                <th style="padding: 10px; text-align: right;">HPP Produksi</th>
                                <th style="padding: 10px; text-align: right;">Subtotal Harga Jual</th>
                            </tr>
                        </thead>
                        <tbody id="techBreakdownTableBody">
                            <tr>
                                <td style="padding: 10px;">
                                    <strong>1. Kertas &amp; Cetak Lembaran A3+</strong>
                                    <div style="font-size: 11px; color: var(--t-muted);">Biaya kertas + Klik mesin digital</div>
                                </td>
                                <td id="techRow1Spec" style="padding: 10px; font-size: 12.5px;">-</td>
                                <td id="techRow1Hpp" style="padding: 10px; text-align: right; font-weight: 600;">Rp 0</td>
                                <td id="techRow1Subtotal" style="padding: 10px; text-align: right; font-weight: 700; color: var(--primary);">Rp 0</td>
                            </tr>
                            <tr id="techRowLamination">
                                <td style="padding: 10px;">
                                    <strong>2. Finishing Laminasi</strong>
                                    <div style="font-size: 11px; color: var(--t-muted);">Lapisan plastik pelindung cetak</div>
                                </td>
                                <td id="techRow2Spec" style="padding: 10px; font-size: 12.5px;">Tanpa Laminasi</td>
                                <td id="techRow2Hpp" style="padding: 10px; text-align: right; font-weight: 600;">Rp 0</td>
                                <td id="techRow2Subtotal" style="padding: 10px; text-align: right; font-weight: 700; color: var(--primary);">Rp 0</td>
                            </tr>
                            <tr id="techRowCutting">
                                <td style="padding: 10px;">
                                    <strong>3. Finishing Cutting / Potong</strong>
                                    <div style="font-size: 11px; color: var(--t-muted);">Ongkos potong mesin</div>
                                </td>
                                <td id="techRow3Spec" style="padding: 10px; font-size: 12.5px;">Tanpa Potong</td>
                                <td id="techRow3Hpp" style="padding: 10px; text-align: right; font-weight: 600;">Rp 0</td>
                                <td id="techRow3Subtotal" style="padding: 10px; text-align: right; font-weight: 700; color: var(--primary);">Rp 0</td>
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
                            <span class="tech-kpi-label" style="color: #10b981;">Estimasi Profit</span>
                            <span class="tech-kpi-val" id="techKpiProfit" style="color: #10b981;">Rp 0</span>
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
               2. ELEMENTS
            ===================================================== */
            const form = document.getElementById('a3PlusEstimationForm');
            const calculateButton = document.getElementById('calculateEstimationButton');

            const modeSelect = $('#production_mode');
            const layoutFields = document.getElementById('layoutFieldsContainer');
            const directFields = document.getElementById('directSheetFieldsContainer');

            const lengthInput = document.getElementById('length');
            const widthInput = document.getElementById('width');
            const qtyInput = document.getElementById('qty');
            const sheetQtyInput = document.getElementById('sheet_qty');

            const layoutUpCountEl = document.getElementById('layoutUpCount');
            const layoutTotalSheetsEl = document.getElementById('layoutTotalSheets');

            const cuttingSelect = $('#cutting_type');
            const potongOverrideWrap = document.getElementById('potongOverrideContainer');

            // Diskon
            const discountRadios = document.querySelectorAll('input[name="discount_type"]');
            const discountValueField = document.getElementById('discountValueField');
            const discountValueInput = document.getElementById('discount_value');
            const discountSuffix = document.getElementById('discountSuffix');

            // Modal 1: Quotation (Surat Penawaran)
            const modalQuotation = document.getElementById('modalA3plusQuotation');
            const btnOpenQuotationModal = document.getElementById('btnOpenQuotationModal');
            const btnCloseQuotationModal = document.getElementById('btnCloseQuotationModal');
            const btnCancelQuotationModal = document.getElementById('btnCancelQuotationModal');
            const btnPrintQuotationModal = document.getElementById('btnPrintQuotationModal');

            // Modal 2: Technical Breakdown (Detail Estimasi)
            const modalTechnical = document.getElementById('modalA3plusTechnical');
            const btnOpenTechnicalModal = document.getElementById('btnOpenTechnicalModal');
            const btnCloseTechModal = document.getElementById('btnCloseTechModal');
            const btnCancelTechModal = document.getElementById('btnCancelTechModal');

            /* =====================================================
               3. PRODUCTION MODE TOGGLE (Layout vs Direct Sheet)
            ===================================================== */
            modeSelect.on('change', function() {
                const val = $(this).val();
                const potongHelp = document.getElementById('potongOverrideHelp');
                if (val === 'layout') {
                    layoutFields.style.display = 'block';
                    directFields.style.display = 'none';
                    lengthInput.setAttribute('required', 'required');
                    widthInput.setAttribute('required', 'required');
                    qtyInput.setAttribute('required', 'required');
                    sheetQtyInput.removeAttribute('required');
                    if (potongHelp) {
                        potongHelp.textContent =
                            'Biaya potong dihitung per batch produksi berdasarkan tarif ongkos potong minimal dari master data.';
                    }
                } else {
                    layoutFields.style.display = 'none';
                    directFields.style.display = 'block';
                    lengthInput.removeAttribute('required');
                    widthInput.removeAttribute('required');
                    qtyInput.removeAttribute('required');
                    sheetQtyInput.setAttribute('required', 'required');
                    if (potongHelp) {
                        potongHelp.textContent =
                            'Pada mode cetak langsung, silakan tentukan kategori ukuran potong secara spesifik (A3, A4, A5, atau ≤ A6).';
                    }
                }
            });

            /* =====================================================
               4. REALTIME IMPOSITION PREVIEW (N-UP CALCULATION)
            ===================================================== */
            // Standar area cetak A3+: 31.5 x 47.5 cm (dengan toleransi potong/bleed)
            const SHEET_PRINTABLE_L = 47.5;
            const SHEET_PRINTABLE_W = 31.5;

            function updateImpositionCalculation() {
                const l = parseFloat(lengthInput.value) || 0;
                const w = parseFloat(widthInput.value) || 0;
                const qty = parseInt(qtyInput.value) || 0;

                if (l > 0 && w > 0) {
                    const upNormal = Math.floor(SHEET_PRINTABLE_L / l) * Math.floor(SHEET_PRINTABLE_W / w);
                    const upRotated = Math.floor(SHEET_PRINTABLE_L / w) * Math.floor(SHEET_PRINTABLE_W / l);
                    const up = Math.max(upNormal, upRotated);
                    const orientation = upRotated > upNormal ? 'Rotated' : 'Normal';

                    if (up > 0) {
                        layoutUpCountEl.textContent = `${up} pcs (${orientation})`;
                        if (qty > 0) {
                            const totalSheets = Math.ceil(qty / up);
                            layoutTotalSheetsEl.textContent = `${totalSheets.toLocaleString('id-ID')} lembar A3+`;
                        } else {
                            layoutTotalSheetsEl.textContent = '—';
                        }
                    } else {
                        layoutUpCountEl.textContent = 'Ukuran melebihi lembar A3+';
                        layoutTotalSheetsEl.textContent = '—';
                    }
                } else {
                    layoutUpCountEl.textContent = '—';
                    layoutTotalSheetsEl.textContent = '—';
                }
            }

            lengthInput.addEventListener('input', updateImpositionCalculation);
            widthInput.addEventListener('input', updateImpositionCalculation);
            qtyInput.addEventListener('input', updateImpositionCalculation);

            /* =====================================================
               5. CUTTING TYPE TOGGLE
            ===================================================== */
            const cuttingMasterInfo = document.getElementById('cuttingMasterInfo');
            cuttingSelect.on('change', function() {
                const val = $(this).val();
                if (val === 'potong') {
                    potongOverrideWrap.style.display = 'block';
                    if (cuttingMasterInfo) cuttingMasterInfo.style.display = 'none';
                } else if (val && val !== 'none') {
                    potongOverrideWrap.style.display = 'none';
                    if (cuttingMasterInfo) cuttingMasterInfo.style.display = 'block';
                } else {
                    potongOverrideWrap.style.display = 'none';
                    if (cuttingMasterInfo) cuttingMasterInfo.style.display = 'none';
                }
            });

            /* =====================================================
               5B. MATERIAL FILTER BERDASARKAN LOKASI, KATEGORI & WARNA CETAK
            ===================================================== */
            const locationSelect = $('#location_id');
            const categorySelect = $('#category_id');
            const printColorSelect = $('#print_color');
            const materialSelect = $('#material_id');
            const displayQtyInput = document.getElementById('display_qty');
            const displayPackageSummary = document.getElementById('displayPackageSummary');
            const displayQtyHelp = document.getElementById('displayQtyHelp');

            const materialMasterInfo = document.getElementById('materialMasterInfo');
            const infoPaperCost = document.getElementById('infoPaperCost');
            const infoSingleCost = document.getElementById('infoSingleCost');
            const infoDoubleCost = document.getElementById('infoDoubleCost');
            const materialHelp = document.getElementById('materialHelp');

            const getMaterialsUrl = "{{ route('admin_get_materials_a3plus') }}";

            function toggleDisplayMode(isDisplay) {
                const materialLabel = document.getElementById('materialLabelText');
                const productionModeWrapper = document.getElementById('productionModeWrapper');
                const printColorField = document.getElementById('printColorField');
                const layoutFields = document.getElementById('layoutFieldsContainer');
                const directFields = document.getElementById('directSheetFieldsContainer');
                const displayFields = document.getElementById('displayFieldsContainer');
                const finishingSection = document.getElementById('finishingSection');
                const displayFinishingNotice = document.getElementById('displayFinishingNotice');

                if (isDisplay) {
                    if (materialLabel) materialLabel.textContent = 'Produk & Komponen Display';
                    if (productionModeWrapper) productionModeWrapper.style.display = 'none';
                    if (printColorField) printColorField.style.display = 'none';
                    if (layoutFields) layoutFields.style.display = 'none';
                    if (directFields) directFields.style.display = 'none';
                    if (displayFields) displayFields.style.display = 'block';
                    if (finishingSection) finishingSection.style.display = 'none';
                    if (displayFinishingNotice) displayFinishingNotice.style.display = 'block';

                    lengthInput.removeAttribute('required');
                    widthInput.removeAttribute('required');
                    qtyInput.removeAttribute('required');
                    sheetQtyInput.removeAttribute('required');
                } else {
                    if (materialLabel) materialLabel.textContent = 'Material Kertas / Stiker';
                    if (productionModeWrapper) productionModeWrapper.style.display = 'grid';
                    if (printColorField) printColorField.style.display = 'block';
                    if (displayFields) displayFields.style.display = 'none';
                    if (finishingSection) finishingSection.style.display = 'block';
                    if (displayFinishingNotice) displayFinishingNotice.style.display = 'none';

                    modeSelect.trigger('change');
                }
            }

            async function loadMaterials() {
                const locationId = locationSelect.val();
                const categoryId = categorySelect.val();
                const selectedCatOption = categorySelect.find('option:selected');
                const catName = (selectedCatOption.data('name') || selectedCatOption.text() || '').trim()
                    .toLowerCase();
                const isDisplay = catName.includes('display');
                const printColor = printColorSelect.val() || 'fc';
                const currentMaterialId = materialSelect.val();

                toggleDisplayMode(isDisplay);

                if (!locationId) {
                    materialSelect.empty();
                    materialSelect.append('<option value="">Pilih Lokasi terlebih dahulu</option>');
                    materialSelect.trigger('change.select2');
                    materialSelect.trigger('change');
                    if (materialHelp) {
                        materialHelp.textContent = 'Silakan pilih lokasi terlebih dahulu.';
                    }
                    return;
                }

                materialSelect.prop('disabled', true);
                if (materialHelp) {
                    materialHelp.textContent = isDisplay ?
                        'Memuat produk & komponen display untuk lokasi terpilih...' :
                        'Memuat material untuk lokasi terpilih...';
                }

                try {
                    const url = new URL(getMaterialsUrl, window.location.origin);
                    url.searchParams.set('location_id', locationId);
                    if (categoryId) {
                        url.searchParams.set('category_id', categoryId);
                    }
                    if (!isDisplay) {
                        url.searchParams.set('print_color', printColor);
                    }

                    const response = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const res = await response.json();

                    materialSelect.empty();
                    materialSelect.append('<option value="">' + (isDisplay ? 'Pilih Produk & Komponen Display' :
                        'Pilih Material') + '</option>');

                    if (response.ok && res.success && Array.isArray(res.data) && res.data.length > 0) {
                        if (isDisplay) {
                            res.data.forEach(function(item) {
                                const $option = $('<option>')
                                    .val(item.id)
                                    .text(item.name)
                                    .attr('data-is-display', 'true')
                                    .attr('data-display-name', item.display_name)
                                    .attr('data-package-qty', item.package_qty)
                                    .attr('data-sheet-qty', item.sheet_qty)
                                    .attr('data-gen1', item.general_price)
                                    .attr('data-gen2', item.general_price_2_side)
                                    .attr('data-div1', item.division_price)
                                    .attr('data-div2', item.division_price_2_side);
                                materialSelect.append($option);
                            });
                        } else {
                            // Kelompokkan data berdasarkan category_name
                            const grouped = {};
                            res.data.forEach(function(mat) {
                                const cat = mat.category_name || 'Lainnya';
                                if (!grouped[cat]) grouped[cat] = [];
                                grouped[cat].push(mat);
                            });

                            Object.keys(grouped).forEach(function(cName) {
                                const $optgroup = $('<optgroup>').attr('label', cName);
                                grouped[cName].forEach(function(mat) {
                                    const $option = $('<option>')
                                        .val(mat.id)
                                        .text(mat.name)
                                        .attr('data-category', mat.category_name)
                                        .attr('data-paper-cost', mat.paper_cost || 0)
                                        .attr('data-single-cost', mat.single_print_cost || 0)
                                        .attr('data-double-cost', mat.double_print_cost || 0);
                                    $optgroup.append($option);
                                });
                                materialSelect.append($optgroup);
                            });
                        }

                        // Re-select jika sebelumnya sudah memilih material yang sama
                        if (currentMaterialId && materialSelect.find(`option[value="${currentMaterialId}"]`)
                            .length > 0) {
                            materialSelect.val(currentMaterialId);
                        } else {
                            materialSelect.val('');
                        }

                        if (materialHelp) {
                            if (isDisplay) {
                                materialHelp.textContent = 'Pilih kombinasi produk display dan bahan cetak.';
                            } else {
                                const colorLabel = printColor === 'bw' ? 'Hitam Putih (BW)' : 'Full Color';
                                materialHelp.textContent =
                                    `Menampilkan material ${colorLabel} yang aktif pada kategori dan lokasi terpilih.`;
                            }
                        }
                    } else {
                        materialSelect.append(
                            `<option value="" disabled>${isDisplay ? 'Belum ada produk display di lokasi ini' : 'Belum ada material untuk kategori ini di lokasi ini'}</option>`
                        );
                        if (materialHelp) {
                            materialHelp.textContent = isDisplay ?
                                'Belum ada produk display yang dikonfigurasi untuk lokasi ini.' :
                                'Belum ada material di master untuk kategori dan lokasi terpilih.';
                        }
                    }
                } catch (err) {
                    console.error('Gagal mengambil material:', err);
                    materialSelect.empty();
                    materialSelect.append('<option value="" disabled>Gagal memuat material</option>');
                    if (materialHelp) {
                        materialHelp.textContent = 'Gagal memuat material dari server.';
                    }
                } finally {
                    materialSelect.prop('disabled', false);
                    materialSelect.trigger('change.select2');
                    materialSelect.trigger('change');
                }
            }

            // Event listener saat Lokasi, Kategori, atau Warna Cetak berubah
            locationSelect.on('change', loadMaterials);
            categorySelect.on('change', loadMaterials);
            printColorSelect.on('change', loadMaterials);

            // Panggil saat inisialisasi jika lokasi sudah terpilih
            if (locationSelect.val()) {
                loadMaterials();
            }

            materialSelect.on('change', function() {
                const opt = $(this).find(':selected');
                const isDisplay = opt.attr('data-is-display') === 'true';

                if (isDisplay) {
                    const pkgQty = parseInt(opt.attr('data-package-qty')) || 100;
                    const sheetQty = parseInt(opt.attr('data-sheet-qty')) || 4;
                    const dName = opt.attr('data-display-name') || 'Produk Display';

                    if (displayPackageSummary) {
                        displayPackageSummary.textContent =
                            `1 Box ${dName} berisi ${pkgQty} pcs. Memerlukan ${sheetQty} lembar A3+. Ongkos Die Cut dan Box KN sudah otomatis diperhitungkan.`;
                    }
                    if (displayQtyHelp) {
                        displayQtyHelp.textContent =
                            `1 Paket = ${pkgQty} pcs (Kebutuhan Cetak: ${sheetQty} Lembar A3+).`;
                    }
                    materialMasterInfo.style.display = 'none';
                } else {
                    const paperCost = parseFloat(opt.data('paper-cost') || opt.attr('data-paper-cost')) ||
                    0;
                    const singleCost = parseFloat(opt.data('single-cost') || opt.attr(
                        'data-single-cost')) || 0;
                    const doubleCost = parseFloat(opt.data('double-cost') || opt.attr(
                        'data-double-cost')) || 0;

                    if ($(this).val()) {
                        infoPaperCost.textContent = 'Rp ' + Math.round(paperCost).toLocaleString('id-ID');
                        infoSingleCost.textContent = 'Rp ' + Math.round(singleCost).toLocaleString('id-ID');
                        infoDoubleCost.textContent = 'Rp ' + Math.round(doubleCost).toLocaleString('id-ID');
                        materialMasterInfo.style.display = 'block';
                    } else {
                        materialMasterInfo.style.display = 'none';
                    }
                }
            });

            const laminationSelect = $('#lamination_type');
            const laminationMasterInfo = document.getElementById('laminationMasterInfo');
            const infoLaminationCost = document.getElementById('infoLaminationCost');

            laminationSelect.on('change', function() {
                const opt = $(this).find(':selected');
                const cost = parseFloat(opt.data('cost')) || 0;

                if ($(this).val() && $(this).val() !== 'none') {
                    infoLaminationCost.textContent = 'Rp ' + Math.round(cost).toLocaleString('id-ID');
                    laminationMasterInfo.style.display = 'block';
                } else {
                    laminationMasterInfo.style.display = 'none';
                }
            });

            /* =====================================================
               6. KOMPONEN TAMBAHAN DINAMIS
            ===================================================== */
            const addBtn = document.getElementById('addAdditionalComponentButton');
            const compContainer = document.getElementById('additionalComponentsContainer');
            let compIdx = 0;

            function formatAdditionalComponentNumber(value) {
                const rawValue = String(value || '').replace(/\D/g, '');
                if (!rawValue) {
                    return '';
                }
                return new Intl.NumberFormat('id-ID', {
                    maximumFractionDigits: 0
                }).format(Number(rawValue));
            }

            function updateAdditionalComponentNumbers() {
                const components = compContainer.querySelectorAll('.additional-component-item');
                components.forEach(function(component, index) {
                    const numberElement = component.querySelector('.additional-component-number');
                    if (numberElement) {
                        numberElement.textContent = index + 1;
                    }
                });
            }

            function calculateAdditionalComponentPrice(component) {
                const priceType = component.querySelector('.additional-component-price-type').value;
                const calculatedPriceInput = component.querySelector('.additional-component-calculated-price');

                if (priceType !== 'hpp_margin') {
                    calculatedPriceInput.value = 'Rp 0';
                    return;
                }

                const hpp = Number(
                    String(component.querySelector('.additional-component-hpp').value || '').replace(/\./g, '')
                ) || 0;

                const margin = Number(
                    component.querySelector('.additional-component-margin').value
                ) || 0;

                if (hpp <= 0 || margin <= 0) {
                    calculatedPriceInput.value = 'Rp 0';
                    return;
                }

                const marginDecimal = margin / 100;
                const divisor = 1 - marginDecimal;

                if (divisor <= 0) {
                    calculatedPriceInput.value = 'Rp 0';
                    return;
                }

                const sellingPrice = hpp / divisor;
                calculatedPriceInput.value = formatRupiah(sellingPrice);
            }

            function initializeAdditionalComponent(component) {
                const priceTypeSelect = component.querySelector('.additional-component-price-type');
                const unitPriceWrapper = component.querySelector('.additional-component-unit-price-wrapper');
                const hppMarginWrapper = component.querySelector('.additional-component-hpp-margin-wrapper');
                const hppInput = component.querySelector('.additional-component-hpp');
                const unitPriceInput = component.querySelector('.additional-component-unit-price');
                const marginInput = component.querySelector('.additional-component-margin');
                const calculatedPriceInput = component.querySelector('.additional-component-calculated-price');

                unitPriceInput.addEventListener('input', function() {
                    this.value = formatAdditionalComponentNumber(this.value);
                    if (typeof triggerRealtimeCalculation === 'function') triggerRealtimeCalculation();
                });

                hppInput.addEventListener('input', function() {
                    this.value = formatAdditionalComponentNumber(this.value);
                    calculateAdditionalComponentPrice(component);
                    if (typeof triggerRealtimeCalculation === 'function') triggerRealtimeCalculation();
                });

                $(priceTypeSelect).on('change', function() {
                    if (this.value === 'hpp_margin') {
                        unitPriceWrapper.style.display = 'none';
                        hppMarginWrapper.style.display = 'block';
                        calculateAdditionalComponentPrice(component);
                    } else {
                        unitPriceWrapper.style.display = 'block';
                        hppMarginWrapper.style.display = 'none';
                        calculatedPriceInput.value = 'Rp 0';
                    }
                    if (typeof triggerRealtimeCalculation === 'function') triggerRealtimeCalculation();
                });

                marginInput.addEventListener('input', function() {
                    calculateAdditionalComponentPrice(component);
                    if (typeof triggerRealtimeCalculation === 'function') triggerRealtimeCalculation();
                });

                const compQtyInput = component.querySelector('.additional-component-qty');
                if (compQtyInput) {
                    compQtyInput.addEventListener('input', function() {
                        if (typeof triggerRealtimeCalculation === 'function') triggerRealtimeCalculation();
                    });
                }

                const removeButton = component.querySelector('.btn-remove-additional-component');
                removeButton.addEventListener('click', function() {
                    const pSelect = $(component).find('.additional-component-price-type');
                    const cSelect = $(component).find('.additional-component-type');

                    if (pSelect.hasClass('select2-hidden-accessible')) {
                        pSelect.select2('destroy');
                    }
                    if (cSelect.hasClass('select2-hidden-accessible')) {
                        cSelect.select2('destroy');
                    }

                    component.remove();
                    updateAdditionalComponentNumbers();
                    if (typeof triggerRealtimeCalculation === 'function') triggerRealtimeCalculation();
                });
            }

            function addAdditionalComponent() {
                const index = compIdx++;
                const componentHtml = `
                    <div class="additional-component-item" data-component-index="${index}" style="border: 1px solid var(--border-color, #e5e7eb); border-radius: 12px; padding: 20px; margin-bottom: 16px; background: rgba(0,0,0,0.01);">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px;">
                            <div style="font-size: 15px; font-weight: 600;">
                                Komponen Tambahan #<span class="additional-component-number">${index + 1}</span>
                            </div>
                            <button type="button" class="btn btn-danger btn-remove-additional-component" style="border: none; outline: none; border-radius: 8px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; box-shadow: none;">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>

                        <div class="form-grid">
                            {{-- Tipe Komponen --}}
                            <div class="field">
                                <label class="field-label" for="additional_component_type_${index}">
                                    Tipe
                                </label>
                                <select id="additional_component_type_${index}" name="additional_components[${index}][type]" class="select select2 additional-component-type">
                                    <option value=""></option>
                                    <option value="Material">Material</option>
                                    <option value="Finishing">Finishing</option>
                                    <option value="Jasa Pemasangan">Jasa Pemasangan</option>
                                </select>
                            </div>

                            {{-- Nama Komponen --}}
                            <div class="field">
                                <label class="field-label" for="additional_component_name_${index}">
                                    Nama Komponen
                                </label>
                                <input type="text" id="additional_component_name_${index}" name="additional_components[${index}][name]" class="input additional-component-name" placeholder="Contoh: Mata Ayam, Dus Kemasan" autocomplete="off">
                            </div>

                            {{-- Qty --}}
                            <div class="field">
                                <label class="field-label" for="additional_component_qty_${index}">
                                    Qty
                                </label>
                                <input type="number" id="additional_component_qty_${index}" name="additional_components[${index}][qty]" class="input additional-component-qty" min="1" step="1" value="1">
                            </div>

                            {{-- Tipe Harga --}}
                            <div class="field">
                                <label class="field-label" for="additional_component_price_type_${index}">
                                    Tipe Harga
                                </label>
                                <select id="additional_component_price_type_${index}" name="additional_components[${index}][price_type]" class="select select2 additional-component-price-type">
                                    <option value="unit_price">Harga Satuan (Rp)</option>
                                    <option value="hpp_margin">HPP + Margin %</option>
                                </select>
                            </div>
                        </div>

                        {{-- HARGA SATUAN --}}
                        <div class="additional-component-unit-price-wrapper" style="margin-top: 18px;">
                            <div class="field">
                                <label class="field-label" for="additional_component_unit_price_${index}">
                                    Harga Satuan
                                </label>
                                <input type="text" id="additional_component_unit_price_${index}" name="additional_components[${index}][unit_price]" class="input additional-component-unit-price" inputmode="numeric" placeholder="Contoh: 200.000">
                                <small class="field-help">Harga jual per unit komponen.</small>
                            </div>
                        </div>

                        {{-- HPP + MARGIN --}}
                        <div class="additional-component-hpp-margin-wrapper" style="display: none; margin-top: 18px;">
                            <div class="form-grid">
                                {{-- HPP / Unit --}}
                                <div class="field">
                                    <label class="field-label" for="additional_component_hpp_${index}">
                                        HPP / Unit
                                    </label>
                                    <input type="text" id="additional_component_hpp_${index}" name="additional_components[${index}][hpp]" class="input additional-component-hpp" inputmode="numeric" placeholder="Contoh: 100.000">
                                    <small class="field-help">HPP per unit komponen.</small>
                                </div>

                                {{-- Margin --}}
                                <div class="field">
                                    <label class="field-label" for="additional_component_margin_${index}">
                                        Margin
                                    </label>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <input type="number" id="additional_component_margin_${index}" name="additional_components[${index}][margin]" class="input additional-component-margin" min="0" max="99.99" step="0.01" placeholder="Contoh: 50">
                                        <span>%</span>
                                    </div>
                                    <small class="field-help">Margin dihitung berdasarkan harga jual.</small>
                                </div>
                            </div>

                            {{-- Harga Jual / Unit --}}
                            <div class="field" style="margin-top: 18px;">
                                <label class="field-label">
                                    Harga Jual / Unit
                                </label>
                                <input type="text" class="input additional-component-calculated-price" value="Rp 0" readonly>
                                <small class="field-help">Otomatis dihitung dari HPP dan margin.</small>
                            </div>
                        </div>
                    </div>
                `;

                compContainer.insertAdjacentHTML('beforeend', componentHtml);
                const component = compContainer.lastElementChild;

                $(component).find('.additional-component-price-type').select2({
                    width: '100%',
                    placeholder: 'Pilih...'
                });

                $(component).find('.additional-component-type').select2({
                    width: '100%',
                    placeholder: 'Pilih...'
                });

                initializeAdditionalComponent(component);
                updateAdditionalComponentNumbers();
            }

            if (addBtn) {
                addBtn.addEventListener('click', addAdditionalComponent);
            }

            /* =====================================================
               7. DISCOUNT RADIO TOGGLE & NOMINAL RUPIAH FORMAT
            ===================================================== */
            discountRadios.forEach(radio => {
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
                });
            });

            discountValueInput.addEventListener('input', function() {
                const selectedType = document.querySelector('input[name="discount_type"]:checked')?.value;

                if (selectedType === 'percentage') {
                    // Hanya izinkan angka dan satu titik desimal
                    let value = this.value.replace(/[^0-9.]/g, '');
                    const parts = value.split('.');
                    if (parts.length > 2) {
                        value = parts[0] + '.' + parts.slice(1).join('');
                    }
                    this.value = value;
                }

                if (selectedType === 'nominal') {
                    // Format ribuan menggunakan titik
                    const rawValue = this.value.replace(/\D/g, '');
                    this.value = rawValue ?
                        new Intl.NumberFormat('id-ID', {
                            maximumFractionDigits: 0
                        }).format(Number(rawValue)) :
                        '';
                }
            });

            /* =====================================================
               8. MODAL CONTROLS (QUOTATION & TECHNICAL)
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
               9. FORM SUBMIT (UI/UX SIMULATOR / AJAX READY)
            ===================================================== */
            function formatRupiah(num) {
                const n = Number(num) || 0;
                return 'Rp ' + Math.round(n).toLocaleString('id-ID');
            }

            function formatPercentage(num) {
                const n = Number(num) || 0;
                return n.toFixed(1).replace('.', ',') + '%';
            }

            function renderAdditionalComponentsPreview(components) {
                const container = document.getElementById('previewAdditionalComponentsContainer');
                if (!container) return;
                container.innerHTML = '';
                if (!Array.isArray(components) || components.length === 0) return;

                components.forEach(function(component) {
                    const componentElement = document.createElement('div');
                    componentElement.className = 'preview-component';
                    componentElement.style.marginBottom = '12px';

                    const priceTypeLabel = component.price_type === 'hpp_margin' ? 'HPP + Margin' :
                        'Harga Satuan';
                    const salePricePerUnit = Number(component.sale_price_per_unit) || 0;
                    const subtotal = Number(component.subtotal) || 0;
                    const qty = Number(component.qty) || 0;

                    let additionalInfo = '';
                    if (component.price_type === 'hpp_margin') {
                        const hppPerUnit = Number(component.hpp_per_unit) || 0;
                        const margin = Number(component.margin) || 0;
                        const totalHpp = Number(component.total_hpp) || 0;
                        const profit = Number(component.profit) || 0;

                        additionalInfo = `
                            <div class="col-md-6">
                                <small class="text-muted">HPP / Unit</small>
                                <div>${formatRupiah(hppPerUnit)}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Margin</small>
                                <div>${formatPercentage(margin)}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Total HPP</small>
                                <div>${formatRupiah(totalHpp)}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Profit</small>
                                <div>${formatRupiah(profit)}</div>
                            </div>
                        `;
                    }

                    componentElement.innerHTML = `
                        <div class="preview-component-header">
                            <strong>${component.type ? (component.type.toUpperCase() + ': ') : ''}${component.name || 'KOMPONEN TAMBAHAN'}</strong>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted">Nama Komponen</small>
                                <div>${component.name || '-'}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Qty</small>
                                <div>${qty.toLocaleString('id-ID')}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Jenis Harga</small>
                                <div>${priceTypeLabel}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Harga Jual / Unit</small>
                                <div>${formatRupiah(salePricePerUnit)}</div>
                            </div>
                            ${additionalInfo}
                            <div class="col-md-6">
                                <small class="text-muted">Subtotal</small>
                                <div>${formatRupiah(subtotal)}</div>
                            </div>
                        </div>
                    `;
                    container.appendChild(componentElement);
                });
            }

            function renderAdditionalComponentsHppPreview(components) {
                const container = document.getElementById('previewAdditionalComponentsHppContainer');
                if (!container) return;
                container.innerHTML = '';
                if (!Array.isArray(components) || components.length === 0) return;

                components.forEach(function(component) {
                    if (component.price_type !== 'hpp_margin') return;

                    const hppPerUnit = Number(component.hpp_per_unit) || 0;
                    const totalHpp = Number(component.total_hpp) || 0;
                    const profit = Number(component.profit) || 0;
                    const margin = Number(component.margin_percentage) || 0;

                    const componentElement = document.createElement('div');
                    componentElement.className = 'preview-component';
                    componentElement.style.marginBottom = '12px';

                    componentElement.innerHTML = `
                        <div class="preview-component-header">
                            <strong>${component.type ? (component.type.toUpperCase() + ': ') : ''}${component.name || '-'}</strong>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted">HPP / Unit</small>
                                <div>${formatRupiah(hppPerUnit)}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Total HPP</small>
                                <div>${formatRupiah(totalHpp)}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Profit</small>
                                <div>${formatRupiah(profit)}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Margin</small>
                                <div>${formatPercentage(margin)}</div>
                            </div>
                        </div>
                    `;
                    container.appendChild(componentElement);
                });
            }

            /* =====================================================
               9. REAL-TIME AUTO CALCULATION (KONSISTEN DENGAN BROSUR)
            ===================================================== */
            let calcDebounceTimer = null;
            let isCalculating = false;

            const liveEmptyState = document.getElementById('liveEmptyState');
            const liveResultContainer = document.getElementById('liveResultContainer');
            const liveCalcSpinner = document.getElementById('liveCalcSpinner');
            const btnOpenModalPreview = document.getElementById('btnOpenModalPreview');
            const btnResetForm = document.getElementById('btnResetForm');
            const livePriceTypeBadge = document.getElementById('livePriceTypeBadge');

            function updatePriceTypeBadge() {
                const pt = $('#price_type').val();
                if (livePriceTypeBadge) {
                    livePriceTypeBadge.textContent = pt === 'division' ? 'Harga Divisi' : 'Harga Umum';
                }
            }

            async function executeRealtimeCalculation() {
                const locId = locationSelect.val();
                const matId = materialSelect.val();
                const mode = modeSelect.val();
                const catOption = categorySelect.find('option:selected');
                const catName = (catOption.data('name') || catOption.text() || '').trim().toLowerCase();
                const isDisplay = catName.includes('display');

                // Validasi data minimal sebelum hitung agar tidak spam server
                let isReady = true;
                if (!locId || !matId) isReady = false;

                if (isDisplay) {
                    const dQty = parseInt(displayQtyInput ? displayQtyInput.value : 0) || 0;
                    if (dQty <= 0) isReady = false;
                } else if (mode === 'layout') {
                    const l = parseFloat(lengthInput.value) || 0;
                    const w = parseFloat(widthInput.value) || 0;
                    const q = parseInt(qtyInput.value) || 0;
                    if (l <= 0 || w <= 0 || q <= 0) isReady = false;
                } else {
                    const sq = parseInt(sheetQtyInput.value) || 0;
                    if (sq <= 0) isReady = false;
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

                    // Khusus mode display
                    if (isDisplay) {
                        const dQty = displayQtyInput ? (parseInt(displayQtyInput.value) || 1) : 1;
                        formData.set('qty', dQty);
                        formData.set('production_mode', 'display');
                    }

                    // Bersihkan tanda titik pada diskon nominal
                    const discountType = formData.get('discount_type');
                    if (discountType === 'nominal') {
                        const rawDisc = String(formData.get('discount_value') || '').replace(/\./g, '');
                        formData.set('discount_value', rawDisc);
                    }

                    // Bersihkan tanda titik pada input komponen tambahan
                    const additionalComponentInputs = compContainer.querySelectorAll(
                        '.additional-component-unit-price, .additional-component-hpp'
                    );
                    additionalComponentInputs.forEach(function(input) {
                        const fieldName = input.name;
                        const rawVal = String(input.value || '').replace(/\./g, '');
                        formData.set(fieldName, rawVal);
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
                    if (elPerPcs) elPerPcs.textContent = formatRupiah(resultData.price_per_pcs);

                    // Kebutuhan Lembar A3+
                    const elSheetsSub = document.getElementById('liveTotalSheetsSub');
                    if (elSheetsSub) elSheetsSub.textContent = `${(resultData.total_sheets || 0).toLocaleString('id-ID')} Lembar A3+`;

                    // Badges
                    const elImpositionBadge = document.getElementById('liveImpositionBadge');
                    if (elImpositionBadge) {
                        elImpositionBadge.textContent = resultData.imposition_info || `${resultData.total_sheets} Lembar A3+`;
                    }
                    const elColorSideBadge = document.getElementById('liveColorSideBadge');
                    if (elColorSideBadge) {
                        elColorSideBadge.innerHTML = `<i class="bi bi-printer"></i> ${resultData.color_side || 'Full Color'}`;
                    }

                    // Diskon Wrap
                    const discountWrap = document.getElementById('liveDiscountWrap');
                    if (discountWrap) {
                        if (resultData.discount_amount > 0) {
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

                    // Rincian Komponen Biaya (HPP Breakdown)
                    // 1. Biaya Cetak A3+
                    const elPrintDetail = document.getElementById('livePrintDetail');
                    if (elPrintDetail) elPrintDetail.textContent = `${resultData.material_name || '-'} (${resultData.color_side || '-'})`;
                    const elPrintCost = document.getElementById('livePrintCost');
                    if (elPrintCost) elPrintCost.textContent = formatRupiah(resultData.print_subtotal);

                    // 2. Laminasi
                    const elLamRow = document.getElementById('liveLaminationRow');
                    if (elLamRow) {
                        if (resultData.lamination && resultData.lamination_subtotal > 0) {
                            elLamRow.style.display = 'flex';
                            document.getElementById('liveLaminationDetail').textContent = resultData.lamination_name || 'Laminasi';
                            document.getElementById('liveLaminationCost').textContent = formatRupiah(resultData.lamination_subtotal);
                        } else {
                            elLamRow.style.display = 'none';
                        }
                    }

                    // 3. Cutting
                    const elCutRow = document.getElementById('liveCuttingRow');
                    if (elCutRow) {
                        if (resultData.cutting && resultData.cutting_subtotal > 0) {
                            elCutRow.style.display = 'flex';
                            document.getElementById('liveCuttingDetail').textContent = resultData.cutting_name || 'Finishing Cutting';
                            document.getElementById('liveCuttingCost').textContent = formatRupiah(resultData.cutting_subtotal);
                        } else {
                            elCutRow.style.display = 'none';
                        }
                    }

                    // 4. Komponen Tambahan
                    const elExtraRow = document.getElementById('liveExtraRow');
                    if (elExtraRow) {
                        if (resultData.extra_subtotal > 0) {
                            elExtraRow.style.display = 'flex';
                            const compCount = Array.isArray(resultData.additional_components) ? resultData.additional_components.length : 1;
                            document.getElementById('liveExtraDetail').textContent = `${compCount} item tambahan`;
                            document.getElementById('liveExtraCost').textContent = formatRupiah(resultData.extra_subtotal);
                        } else {
                            elExtraRow.style.display = 'none';
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
                    // 2. POPULATE MODAL DATA (PENAWARAN & DETAIL TEKNIS)
                    // =========================================================
                    lastCalculatedData = resultData;
                    const setTxt = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };

                    // A. MODAL 1: SURAT PENAWARAN (QUOTATION)
                    updateQuotationCustomerInfo();
                    const quoteLead = (resultData.lead_time || document.getElementById('estimation_time')?.value || 1) + ' Hari Kerja';
                    setTxt('modalQuoteLeadTime', quoteLead);
                    setTxt('modalQuoteMaterialName', resultData.material_name || '-');
                    setTxt('modalQuotePrintSide', (resultData.color_side || 'Full Color') + ' · ' + (resultData.print_side == 2 ? '2 Sisi Bolak-Balik' : '1 Sisi'));
                    setTxt('modalQuoteQty', `${(resultData.qty || 0).toLocaleString('id-ID')} pcs`);

                    const finishingParts = [];
                    if (resultData.lamination && resultData.lamination_subtotal > 0) finishingParts.push(resultData.lamination_name || 'Laminasi');
                    if (resultData.cutting && resultData.cutting_subtotal > 0) finishingParts.push(resultData.cutting_name || 'Cutting');
                    setTxt('modalQuoteFinishing', finishingParts.length > 0 ? finishingParts.join(', ') : 'Tanpa Finishing');

                    // Quotation Table Rows
                    let quoteRowsHtml = `
                        <tr>
                            <td style="padding: 10px;">
                                <strong style="color: var(--t-base);">Cetak Digital Print A3+ (${resultData.material_name || '-'})</strong>
                                <div style="font-size: 11.5px; color: var(--t-muted); margin-top: 2px;">
                                    ${resultData.color_side || 'Full Color'} &middot; ${resultData.imposition_info || ((resultData.total_sheets || 0) + ' Lembar A3+')}
                                </div>
                            </td>
                            <td style="padding: 10px; text-align: center;">${(resultData.total_sheets || 0).toLocaleString('id-ID')} Lembar</td>
                            <td style="padding: 10px; text-align: right;">${formatRupiah(resultData.unit_price_sheet || (resultData.print_subtotal / (resultData.total_sheets || 1)))}</td>
                            <td style="padding: 10px; text-align: right; font-weight: 700;">${formatRupiah(resultData.print_subtotal)}</td>
                        </tr>
                    `;

                    if (resultData.lamination && resultData.lamination_subtotal > 0) {
                        quoteRowsHtml += `
                            <tr>
                                <td style="padding: 10px;">
                                    <strong style="color: var(--t-base);">Finishing: ${resultData.lamination_name || 'Laminasi'}</strong>
                                    <div style="font-size: 11.5px; color: var(--t-muted); margin-top: 2px;">Lapisan pelindung cetak A3+</div>
                                </td>
                                <td style="padding: 10px; text-align: center;">${(resultData.total_sheets || 0).toLocaleString('id-ID')} Lembar</td>
                                <td style="padding: 10px; text-align: right;">${formatRupiah(resultData.lamination_subtotal / (resultData.total_sheets || 1))}</td>
                                <td style="padding: 10px; text-align: right; font-weight: 700;">${formatRupiah(resultData.lamination_subtotal)}</td>
                            </tr>
                        `;
                    }

                    if (resultData.cutting && resultData.cutting_subtotal > 0) {
                        quoteRowsHtml += `
                            <tr>
                                <td style="padding: 10px;">
                                    <strong style="color: var(--t-base);">Finishing: ${resultData.cutting_name || 'Cutting / Potong'}</strong>
                                    <div style="font-size: 11.5px; color: var(--t-muted); margin-top: 2px;">Jasa potong / cutting presisi</div>
                                </td>
                                <td style="padding: 10px; text-align: center;">${resultData.is_potong ? '1 Paket' : ((resultData.total_sheets || 0).toLocaleString('id-ID') + ' Lembar')}</td>
                                <td style="padding: 10px; text-align: right;">${formatRupiah(resultData.cutting_subtotal)}</td>
                                <td style="padding: 10px; text-align: right; font-weight: 700;">${formatRupiah(resultData.cutting_subtotal)}</td>
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
                    if (resultData.discount_amount > 0) {
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
                    setTxt('techPriceType', resultData.price_type || ($('#price_type').val() === 'division' ? 'Harga Divisi' : 'Harga Umum'));
                    setTxt('techMaterialName', resultData.material_name || '-');
                    setTxt('techPrintColorSide', (resultData.color_side || 'Full Color') + ' (' + (resultData.print_side == 2 ? '2 Sisi' : '1 Sisi') + ')');
                    setTxt('techProductionMode', resultData.production_mode || (modeSelect.val() === 'layout' ? 'Hitung Layout' : 'Direct Sheet'));
                    setTxt('techFinishedSize', resultData.finished_size || ((lengthInput.value || '-') + ' × ' + (widthInput.value || '-') + ' cm'));
                    setTxt('techImposition', resultData.imposition_info || (resultData.up_count ? `${resultData.up_count} objek/lbr` : '-'));
                    setTxt('techTotalSheets', `${(resultData.total_sheets || 0).toLocaleString('id-ID')} Lembar A3+`);
                    setTxt('techLeadTime', quoteLead);

                    // Technical Breakdown Rows
                    setTxt('techRow1Spec', `${(resultData.total_sheets || 0).toLocaleString('id-ID')} lbr A3+ @ ${formatRupiah(resultData.unit_price_sheet || (resultData.print_subtotal / (resultData.total_sheets || 1)))}`);
                    setTxt('techRow1Hpp', formatRupiah(resultData.print_hpp || resultData.print_total_hpp || 0));
                    setTxt('techRow1Subtotal', formatRupiah(resultData.print_subtotal || 0));

                    setTxt('techRow2Spec', resultData.lamination && resultData.lamination_subtotal > 0 ? `${resultData.lamination_name} (${formatRupiah(resultData.lamination_subtotal / (resultData.total_sheets || 1))}/lbr)` : 'Tanpa Laminasi');
                    setTxt('techRow2Hpp', formatRupiah(resultData.lamination_hpp || 0));
                    setTxt('techRow2Subtotal', formatRupiah(resultData.lamination_subtotal || 0));

                    setTxt('techRow3Spec', resultData.cutting && resultData.cutting_subtotal > 0 ? `${resultData.cutting_name} ${resultData.is_potong ? '(Min Ongkos Potong)' : '(' + formatRupiah(resultData.cutting_subtotal / (resultData.total_sheets || 1)) + '/lbr)'}` : 'Tanpa Potong');
                    setTxt('techRow3Hpp', formatRupiah(resultData.cutting_hpp || 0));
                    setTxt('techRow3Subtotal', formatRupiah(resultData.cutting_subtotal || 0));

                    // Extra components repeater rows
                    const techExtraTbody = document.getElementById('techExtraComponentsRows');
                    if (techExtraTbody) {
                        techExtraTbody.innerHTML = '';
                        if (Array.isArray(resultData.additional_components) && resultData.additional_components.length > 0) {
                            resultData.additional_components.forEach(function(comp, idx) {
                                const tr = document.createElement('tr');
                                tr.innerHTML = `
                                    <td style="padding: 10px;">
                                        <strong>${idx + 4}. ${comp.type ? comp.type.toUpperCase() + ': ' : ''}${comp.name || 'Komponen Tambahan'}</strong>
                                        <div style="font-size: 11px; color: var(--t-muted);">Komponen tambahan pilihan</div>
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

                    // Populate Technical Discount Rows & Banner
                    const techSubtotalRow = document.getElementById('techSubtotalRow');
                    const techDiscountRow = document.getElementById('techDiscountRow');
                    const techDiscountBanner = document.getElementById('techDiscountBanner');

                    if (resultData.discount_amount > 0) {
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

            // =========================================================
            // EVENT LISTENERS UNTUK REAL-TIME CALCULATION
            // =========================================================
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                executeRealtimeCalculation();
            });

            // Input teks & angka (debounced)
            lengthInput.addEventListener('input', triggerRealtimeCalculation);
            widthInput.addEventListener('input', triggerRealtimeCalculation);
            qtyInput.addEventListener('input', triggerRealtimeCalculation);
            sheetQtyInput.addEventListener('input', triggerRealtimeCalculation);
            if (displayQtyInput) displayQtyInput.addEventListener('input', triggerRealtimeCalculation);
            discountValueInput.addEventListener('input', triggerRealtimeCalculation);
            const estTimeInput = document.getElementById('estimation_time');
            if (estTimeInput) estTimeInput.addEventListener('input', triggerRealtimeCalculation);

            // Pelanggan & Catatan Input Listeners (Real-time updates)
            const custNameInput = document.getElementById('customer_name');
            const custPhoneInput = document.getElementById('customer_phone');
            const custNotesInput = document.getElementById('customer_notes');
            if (custNameInput) custNameInput.addEventListener('input', updateQuotationCustomerInfo);
            if (custPhoneInput) custPhoneInput.addEventListener('input', updateQuotationCustomerInfo);
            if (custNotesInput) custNotesInput.addEventListener('input', updateQuotationCustomerInfo);

            // Select & Radio (Langsung kalkulasi)
            locationSelect.on('change', function() {
                updateQuotationCustomerInfo();
                triggerRealtimeCalculation();
            });
            $('#price_type').on('change', function() {
                updatePriceTypeBadge();
                triggerRealtimeCalculation();
            });
            materialSelect.on('change', triggerRealtimeCalculation);
            printColorSelect.on('change', triggerRealtimeCalculation);
            $('#print_side').on('change', triggerRealtimeCalculation);
            modeSelect.on('change', triggerRealtimeCalculation);
            $('#sheet_size_id').on('change', triggerRealtimeCalculation);
            laminationSelect.on('change', triggerRealtimeCalculation);
            cuttingSelect.on('change', triggerRealtimeCalculation);
            $('#potong_size_override').on('change', triggerRealtimeCalculation);
            discountRadios.forEach(radio => radio.addEventListener('change', triggerRealtimeCalculation));

            // =========================================================
            // RESET FORMULIR ESTIMASI (LENGKAP & AMAN)
            // =========================================================
            function resetFormEstimation() {
                // 1. Batalkan debounce timer & request aktif
                clearTimeout(calcDebounceTimer);
                isCalculating = false;
                lastCalculatedData = null;

                // 2. Native form reset
                form.reset();

                // 3. Reset Dropdown Select2
                locationSelect.val('').trigger('change.select2');
                categorySelect.val('').trigger('change.select2');
                printColorSelect.val('fc').trigger('change.select2');
                $('#price_type').val('general').trigger('change.select2');
                modeSelect.val('layout').trigger('change.select2');
                laminationSelect.val('none').trigger('change.select2');
                cuttingSelect.val('none').trigger('change.select2');
                $('#potong_size_override').val('').trigger('change.select2');
                $('#sheet_size_id').val('standard').trigger('change.select2');

                // 4. Reset Material dropdown & info
                materialSelect.empty().append('<option value="">Pilih Lokasi &amp; Kategori terlebih dahulu</option>');
                materialSelect.val('').trigger('change.select2');
                if (materialMasterInfo) materialMasterInfo.style.display = 'none';
                if (materialHelp) {
                    materialHelp.textContent = 'Material akan ditampilkan otomatis berdasarkan Lokasi, Kategori, dan Warna Cetak.';
                }

                // 5. Kembalikan Mode Tampilan (Non-Display Layout)
                toggleDisplayMode(false);
                modeSelect.trigger('change');

                // 6. Sembunyikan info master finishing & override
                if (laminationMasterInfo) laminationMasterInfo.style.display = 'none';
                if (cuttingMasterInfo) cuttingMasterInfo.style.display = 'none';
                if (potongOverrideWrap) potongOverrideWrap.style.display = 'none';

                // 7. Hapus semua Komponen Tambahan dinamis
                const dynamicComponents = compContainer.querySelectorAll('.additional-component-item');
                dynamicComponents.forEach(function(comp) {
                    const pSelect = $(comp).find('.additional-component-price-type');
                    const cSelect = $(comp).find('.additional-component-type');
                    if (pSelect.hasClass('select2-hidden-accessible')) pSelect.select2('destroy');
                    if (cSelect.hasClass('select2-hidden-accessible')) cSelect.select2('destroy');
                    comp.remove();
                });
                compIdx = 0;

                // 8. Reset opsi & input diskon
                const discountNoneRadio = document.querySelector('input[name="discount_type"][value="none"]');
                if (discountNoneRadio) discountNoneRadio.checked = true;
                if (discountValueField) discountValueField.hidden = true;
                if (discountValueInput) {
                    discountValueInput.disabled = true;
                    discountValueInput.value = '';
                }
                if (discountSuffix) discountSuffix.textContent = '%';
                const discWrap = document.getElementById('liveDiscountWrap');
                if (discWrap) discWrap.style.display = 'none';

                // 9. Reset preview teks imposisi
                if (layoutUpCountEl) layoutUpCountEl.textContent = '—';
                if (layoutTotalSheetsEl) layoutTotalSheetsEl.textContent = '—';

                // 10. Reset Input Pelanggan & Info Modal Penawaran
                const cName = document.getElementById('customer_name');
                const cPhone = document.getElementById('customer_phone');
                const cNotes = document.getElementById('customer_notes');
                if (cName) cName.value = '';
                if (cPhone) cPhone.value = '';
                if (cNotes) cNotes.value = '';
                updateQuotationCustomerInfo();

                // 11. Reset Sticky Summary Card ke State Awal (Empty State)
                updatePriceTypeBadge();
                if (liveEmptyState) liveEmptyState.style.display = 'block';
                if (liveResultContainer) liveResultContainer.style.display = 'none';
                if (liveCalcSpinner) liveCalcSpinner.style.display = 'none';

                // 12. Reset elemen-elemen modal agar bersih
                const modalQuoteBody = document.getElementById('modalQuoteTableBody');
                if (modalQuoteBody) {
                    modalQuoteBody.innerHTML = `
                        <tr>
                            <td style="padding: 10px;">
                                <strong id="modalQuoteItemTitle">Cetak Digital Print A3+</strong>
                                <div id="modalQuoteItemDesc" style="font-size: 11.5px; color: var(--t-muted);">-</div>
                            </td>
                            <td id="modalQuoteItemQty" style="padding: 10px; text-align: center;">-</td>
                            <td id="modalQuoteItemUnitPrice" style="padding: 10px; text-align: right;">-</td>
                            <td id="modalQuoteItemSubtotal" style="padding: 10px; text-align: right; font-weight: 700;">-</td>
                        </tr>
                    `;
                }
                const quoteSubRow = document.getElementById('modalQuoteSubtotalRow');
                const quoteDiscRow = document.getElementById('modalQuoteDiscountRow');
                if (quoteSubRow) quoteSubRow.style.display = 'none';
                if (quoteDiscRow) quoteDiscRow.style.display = 'none';

                const techExtraBody = document.getElementById('techExtraComponentsRows');
                if (techExtraBody) techExtraBody.innerHTML = '';
                const techSubRow = document.getElementById('techSubtotalRow');
                const techDiscRow = document.getElementById('techDiscountRow');
                const techBanner = document.getElementById('techDiscountBanner');
                if (techSubRow) techSubRow.style.display = 'none';
                if (techDiscRow) techDiscRow.style.display = 'none';
                if (techBanner) techBanner.style.display = 'none';

                setTxt('techRow1Spec', '-');
                setTxt('techRow1Hpp', 'Rp 0');
                setTxt('techRow1Subtotal', 'Rp 0');
                setTxt('techRow2Spec', 'Tanpa Laminasi');
                setTxt('techRow2Hpp', 'Rp 0');
                setTxt('techRow2Subtotal', 'Rp 0');
                setTxt('techRow3Spec', 'Tanpa Potong');
                setTxt('techRow3Hpp', 'Rp 0');
                setTxt('techRow3Subtotal', 'Rp 0');
                setTxt('techKpiTotalHpp', 'Rp 0');
                setTxt('techKpiGrandTotal', 'Rp 0');
                setTxt('techKpiProfit', 'Rp 0');
                setTxt('techKpiMargin', '0%');
                setTxt('techKpiMarkup', '0%');

                if (typeof showToast === 'function') {
                    showToast('info', 'Formulir estimasi telah direset.');
                }
            }

            if (btnResetForm) {
                btnResetForm.addEventListener('click', function(e) {
                    e.preventDefault();
                    resetFormEstimation();
                });
            }

            form.addEventListener('reset', function(e) {
                e.preventDefault();
                resetFormEstimation();
            });

            /* =====================================================
               PRICE LIST DIGITAL PRINT A3+ INTERACTIVE MODULE
            ===================================================== */
            let priceListRaw = @json($digitalPriceList ?? null);

            let plState = {
                category: '{{ $defaultCat ?? 'BC' }}',
                group: 'all', // 'all' | 'general' | 'division'
                side: 'all',  // 'all' | 'single' | 'double'
                search: '',
                page: 1,
                pageSize: 10
            };

            const plCatSelect = $('#priceListCategoryFilter');
            const plPageSizeSelect = $('#priceListPageSize');
            const plSearchInput = document.getElementById('priceListSearch');
            const plSearchClear = document.getElementById('priceListSearchClear');
            const plTableBody = document.getElementById('a3plusPriceListBody');
            const plMarkupRow = document.getElementById('markupHeaderRow');
            const plInfo = document.getElementById('priceListInfo');
            const plPagination = document.getElementById('priceListPagination');
            const currentCatBadge = document.getElementById('currentCatBadge');

            // Format Rupiah
            function formatPlRupiah(val) {
                if (val === null || val === undefined || isNaN(val) || Number(val) <= 0) return '<span style="color:var(--t-muted); opacity:0.4;">-</span>';
                return 'Rp ' + Math.round(Number(val)).toLocaleString('id-ID');
            }

            // Update Markup Header Row berdasarkan Kategori Terpilih
            function updateMarkupHeaderRow() {
                if (!plMarkupRow || !priceListRaw) return;
                const cat = plState.category;
                const markups = (priceListRaw.categoryMarkups && priceListRaw.categoryMarkups[cat]) ? priceListRaw.categoryMarkups[cat] : null;

                const tierKeys = ['tier_1', 'tier_2_5', 'tier_6_50', 'tier_100', 'tier_300', 'tier_500'];
                const groups = [
                    { key: 'general_single', cls: 'details-digital-group--general_single col-tier-gen-1', pillCls: 'markup-pill--general' },
                    { key: 'general_double', cls: 'details-digital-group--general_double col-tier-gen-2', pillCls: 'markup-pill--general' },
                    { key: 'division_single', cls: 'details-digital-group--division_single col-tier-div-1', pillCls: 'markup-pill--division' },
                    { key: 'division_double', cls: 'details-digital-group--division_double col-tier-div-2', pillCls: 'markup-pill--division' }
                ];

                let html = '';
                groups.forEach(g => {
                    tierKeys.forEach(t => {
                        const mVal = (markups && markups[g.key] && markups[g.key][t]) ? markups[g.key][t] : '-';
                        html += `<th class="${g.cls}" title="Markup: ${mVal}"><span class="markup-pill ${g.pillCls}">${mVal}</span></th>`;
                    });
                });

                plMarkupRow.innerHTML = html;
            }

            // Update Visibility Kolom Tipe Harga & Sisi Cetak
            function updateColumnVisibility() {
                const table = document.getElementById('a3plusPriceListTable');
                if (!table) return;

                const gGen1 = table.querySelectorAll('.col-tier-gen-1');
                const gGen2 = table.querySelectorAll('.col-tier-gen-2');
                const gDiv1 = table.querySelectorAll('.col-tier-div-1');
                const gDiv2 = table.querySelectorAll('.col-tier-div-2');

                const showGen1 = (plState.group === 'all' || plState.group === 'general') && (plState.side === 'all' || plState.side === 'single');
                const showGen2 = (plState.group === 'all' || plState.group === 'general') && (plState.side === 'all' || plState.side === 'double');
                const showDiv1 = (plState.group === 'all' || plState.group === 'division') && (plState.side === 'all' || plState.side === 'single');
                const showDiv2 = (plState.group === 'all' || plState.group === 'division') && (plState.side === 'all' || plState.side === 'double');

                gGen1.forEach(el => el.classList.toggle('col-hide', !showGen1));
                gGen2.forEach(el => el.classList.toggle('col-hide', !showGen2));
                gDiv1.forEach(el => el.classList.toggle('col-hide', !showDiv1));
                gDiv2.forEach(el => el.classList.toggle('col-hide', !showDiv2));
            }

            // Render Body Tabel dan Pagination
            function renderPriceList() {
                if (!plTableBody || !priceListRaw || !Array.isArray(priceListRaw.details)) {
                    if (plTableBody) {
                        plTableBody.innerHTML = '<tr><td colspan="31" style="text-align:center; padding:28px; color:var(--t-muted);">Data price list belum tersedia.</td></tr>';
                    }
                    return;
                }

                // 1. Filter Kategori & Search
                const filtered = priceListRaw.details.filter(item => {
                    const matchCat = (plState.category === 'all') || (item.category_name === plState.category);
                    const matchSearch = (!plState.search) ||
                        (item.material_name && item.material_name.toLowerCase().includes(plState.search.toLowerCase())) ||
                        (item.size_label && item.size_label.toLowerCase().includes(plState.search.toLowerCase()));
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

                // Update Markup Row
                updateMarkupHeaderRow();

                // Update Badge Header Kategori
                if (currentCatBadge) {
                    currentCatBadge.innerHTML = `<i class="bi bi-folder2-open me-1"></i> Kategori: ${plState.category === 'all' ? 'Semua' : plState.category}`;
                }

                // 3. Render Baris Data
                if (pageItems.length === 0) {
                    plTableBody.innerHTML = `
                        <tr>
                            <td colspan="31" style="text-align: center; padding: 42px 16px; color: var(--t-muted);">
                                <div style="font-size: 28px; margin-bottom: 8px; color: var(--t-muted); opacity: 0.6;"><i class="bi bi-inbox-fill"></i></div>
                                <div style="font-size: 14px; font-weight: 700; color: var(--t-base); margin-bottom: 4px;">Material Tidak Ditemukan</div>
                                <div style="font-size: 12.5px;">Tidak ada material yang cocok dengan filter kategori atau kata kunci pencarian.</div>
                            </td>
                        </tr>
                    `;
                } else {
                    const tierKeys = ['tier_1', 'tier_2_5', 'tier_6_50', 'tier_100', 'tier_300', 'tier_500'];
                    let tbodyHtml = '';

                    pageItems.forEach((item, idx) => {
                        const no = startIdx + idx + 1;
                        let tr = `<tr>`;
                        tr += `<td class="details-digital-sticky details-digital-col-no" style="text-align:center; color:var(--t-muted); font-size:12px;">${no}</td>`;
                        tr += `<td class="details-digital-sticky details-digital-col-material font-semibold" style="font-weight:700; color:var(--t-base);">
                            <div style="display:flex; align-items:center; justify-content:space-between; gap:6px;">
                                <span>${item.material_name}</span>
                                ${plState.category === 'all' ? `<span class="badge" style="font-size:10px; padding:2px 6px; background:var(--bg-muted); color:var(--t-muted); border:1px solid var(--border);">${item.category_name}</span>` : ''}
                            </div>
                        </td>`;
                        tr += `<td class="details-digital-size" style="text-align:center;"><span style="display:inline-block; padding:2px 7px; background:var(--bg-muted); border-radius:5px; font-size:11.5px; color:var(--t-muted); border:1px solid var(--border);">${item.size_label || '-'}</span></td>`;
                        tr += `<td class="details-digital-cost" style="text-align:right;">${formatPlRupiah(item.paper_cost)}</td>`;
                        tr += `<td class="details-digital-cost" style="text-align:right;">${formatPlRupiah(item.click_cost)}</td>`;
                        tr += `<td class="details-digital-cost details-digital-cost-total" style="text-align:right; font-weight:700;">${formatPlRupiah(item.single_print_cost)}</td>`;
                        tr += `<td class="details-digital-cost details-digital-cost-total" style="text-align:right; font-weight:700;">${formatPlRupiah(item.double_print_cost)}</td>`;

                        // General Single
                        tierKeys.forEach(t => {
                            const p = (item.prices && item.prices.general_single) ? item.prices.general_single[t] : null;
                            tr += `<td class="details-digital-price details-digital-group--general_single col-tier-gen-1" style="text-align:right;">${formatPlRupiah(p)}</td>`;
                        });

                        // General Double
                        tierKeys.forEach(t => {
                            const p = (item.prices && item.prices.general_double) ? item.prices.general_double[t] : null;
                            tr += `<td class="details-digital-price details-digital-group--general_double col-tier-gen-2" style="text-align:right;">${formatPlRupiah(p)}</td>`;
                        });

                        // Division Single
                        tierKeys.forEach(t => {
                            const p = (item.prices && item.prices.division_single) ? item.prices.division_single[t] : null;
                            tr += `<td class="details-digital-price details-digital-group--division_single col-tier-div-1" style="text-align:right;">${formatPlRupiah(p)}</td>`;
                        });

                        // Division Double
                        tierKeys.forEach(t => {
                            const p = (item.prices && item.prices.division_double) ? item.prices.division_double[t] : null;
                            tr += `<td class="details-digital-price details-digital-group--division_double col-tier-div-2" style="text-align:right;">${formatPlRupiah(p)}</td>`;
                        });

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

            // Sisi Cetak Tabs (Semua / 1 Muka / 2 Muka)
            const printSideTabs = document.querySelectorAll('#printSideTabWrap .matrix-tab-btn');
            printSideTabs.forEach(btn => {
                btn.addEventListener('click', function() {
                    printSideTabs.forEach(b => b.classList.remove('is-active'));
                    this.classList.add('is-active');
                    plState.side = this.getAttribute('data-side') || 'all';
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

            // Re-fetch saat lokasi diubah di form utama
            locationSelect.on('change', async function() {
                const locId = $(this).val();
                if (!locId) return;
                try {
                    const url = `{{ route('admin_get_pricelist_a3plus') }}?location_id=${locId}`;
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
                            } else if (json.data.categories.includes('BC')) {
                                plCatSelect.val('BC');
                            } else {
                                plCatSelect.val(json.data.categories[0] || 'all');
                            }
                            plCatSelect.trigger('change.select2');
                            plState.category = plCatSelect.val();
                        }
                        renderPriceList();
                    }
                } catch (err) {
                    console.error('Gagal mengambil price list untuk lokasi terpilih:', err);
                }
            });

            // Inisialisasi awal render tabel
            renderPriceList();

            // Panggil inisialisasi awal jika form sudah memiliki nilai
            updatePriceTypeBadge();
            setTimeout(function() {
                executeRealtimeCalculation();
            }, 600);

        });

        $(document).on('wheel', 'input[type="number"]', function() {
            this.blur();
        });
    </script>
@endsection

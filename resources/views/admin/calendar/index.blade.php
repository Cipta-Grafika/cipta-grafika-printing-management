@extends('admin_master')

@section('contents')
    <style>
        .calendar-tabs {
            display: flex;
            gap: 10px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .calendar-tab-btn {
            background: var(--bg-card);
            border: 1px solid var(--border);
            padding: 9px 18px;
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

        .calendar-tab-btn:hover {
            border-color: var(--primary);
            background: var(--bg-hover);
            color: var(--t-base);
        }

        .calendar-tab-btn.active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        .cal-badge-counter {
            font-size: 11px;
            padding: 2px 7px;
            border-radius: 9999px;
            background: var(--bg-muted);
            color: var(--t-base);
            font-weight: 600;
        }

        .calendar-tab-btn.active .cal-badge-counter {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        .cal-metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .cal-metric-box {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .cal-metric-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .table-custom {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .table-custom th {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 2px solid var(--border);
            background: var(--bg-surface-2);
            color: var(--t-base);
            font-weight: 600;
        }

        .table-custom td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            color: var(--t-base);
        }

        .btn-detail-cal {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            color: var(--t-base);
            transition: opacity 0.2s ease, transform 0.1s ease, color 0.15s ease;
            font-size: 14px;
            cursor: pointer;
            /* transition: all 0.15s ease; */
        }

        .btn-detail-cal:hover {
            opacity: 0.9;
            transform: scale(1.08);
            color: var(--primary);
        }

        .btn-edit-cal {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            color: var(--t-base);
            transition: opacity 0.2s ease, transform 0.1s ease, color 0.15s ease;
            font-size: 14px;
            cursor: pointer;
            /* transition: all 0.15s ease; */
        }

        .btn-edit-cal:hover {
            opacity: 0.9;
            transform: scale(1.08);
            color: #d97706;
        }

        .btn-delete-cal {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            color: var(--t-base);
            transition: opacity 0.2s ease, transform 0.1s ease, color 0.15s ease;
            font-size: 14px;
            cursor: pointer;
            border: none;
            background: transparent;
        }

        .btn-delete-cal:hover {
            opacity: 0.9;
            transform: scale(1.08);
            color: var(--danger, #ef4444);
        }

        .modal-cal {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 16px;
        }

        .modal-cal.is-open {
            display: flex;
        }

        .modal-cal-content {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 20px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .modal-cal-head {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-cal-body {
            padding: 20px;
            max-height: 75vh;
            overflow-y: auto;
        }

        .modal-cal-content.modal-lg {
            max-width: 680px;
        }

        .modal-cal-foot {
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: var(--bg-surface-2);
        }

        /* SELECT2 THEME FIXES INSIDE MODAL */
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border) !important;
            border-radius: 8px !important;
            height: 40px !important;
            display: flex !important;
            align-items: center !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--t-base) !important;
            line-height: 38px !important;
            padding-left: 12px !important;
            font-size: 13.5px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
            right: 8px !important;
        }

        .select2-dropdown {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border) !important;
            border-radius: 8px !important;
            z-index: 999999 !important;
        }

        .select2-container--default .select2-results__option {
            color: var(--t-base) !important;
            padding: 8px 12px !important;
            font-size: 13px !important;
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
                        <span class="eyebrow" style="color: var(--primary); font-weight: 700;">
                            MASTER DATA MANAGEMENT
                        </span>
                        <h1 class="hero-title" style="margin: 6px 0 8px;">
                            Master Cetak Kalender
                        </h1>
                        <p class="hero-sub" style="margin: 0;">
                            Kelola ukuran produk, tarif bahan kertas, aksesoris/finishing, dan margin keuntungan kalender
                            secara terpusat dan 100% dinamis.
                        </p>
                    </div>
                    <div>
                        <button type="button" class="btn btn--primary" id="btnMainCreateCal"
                            onclick="openActiveCreateModal()" style="display: inline-flex; align-items: center; gap: 8px;">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            <span id="labelMainCreateCal">Tambah Ukuran Produk</span>
                        </button>
                    </div>
                </section>

                {{-- METRIC SUMMARY CARDS --}}
                <div class="cal-metrics-grid" style="margin-top: 16px;">
                    <div class="cal-metric-box">
                        <div class="cal-metric-icon" style="background: rgba(37, 99, 235, 0.1); color: #2563eb;">
                            <i class="bi bi-calendar3"></i>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--t-muted); font-weight: 600;">PRODUK &amp; UKURAN</div>
                            <div id="statTotalProducts" style="font-size: 22px; font-weight: 800; color: var(--t-base);">
                                {{ $totalProducts }}</div>
                        </div>
                    </div>
                    <div class="cal-metric-box">
                        <div class="cal-metric-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--t-muted); font-weight: 600;">TARIF BAHAN KERTAS</div>
                            <div id="statTotalMaterials" style="font-size: 22px; font-weight: 800; color: var(--t-base);">
                                {{ $totalMaterials }}</div>
                        </div>
                    </div>
                    <div class="cal-metric-box">
                        <div class="cal-metric-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                            <i class="bi bi-gear-wide-connected"></i>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--t-muted); font-weight: 600;">KOMPONEN &amp; FINISHING
                            </div>
                            <div id="statTotalComponents" style="font-size: 22px; font-weight: 800; color: var(--t-base);">
                                {{ $totalComponents }}</div>
                        </div>
                    </div>
                    <div class="cal-metric-box">
                        <div class="cal-metric-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                            <i class="bi bi-percent"></i>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--t-muted); font-weight: 600;">PRICING RULES (MARKUP)
                            </div>
                            <div id="statTotalRules" style="font-size: 22px; font-weight: 800; color: var(--t-base);">
                                {{ $totalRules }}</div>
                        </div>
                    </div>
                </div>

                {{-- TABS NAVIGATION --}}
                <div class="calendar-tabs">
                    <button type="button" class="calendar-tab-btn active" id="tabBtnProducts"
                        onclick="switchCalTab('products')">
                        <i class="bi bi-calendar2-range"></i>
                        <span>1. Ukuran &amp; Produk</span>
                        <span id="badgeCountProducts" class="cal-badge-counter">{{ $totalProducts }}</span>
                    </button>
                    <button type="button" class="calendar-tab-btn" id="tabBtnMaterials"
                        onclick="switchCalTab('materials')">
                        <i class="bi bi-file-earmark-ruled"></i>
                        <span>2. Tarif Bahan Kertas</span>
                        <span id="badgeCountMaterials" class="cal-badge-counter">{{ $totalMaterials }}</span>
                    </button>
                    <button type="button" class="calendar-tab-btn" id="tabBtnComponents"
                        onclick="switchCalTab('components')">
                        <i class="bi bi-tools"></i>
                        <span>3. Aksesoris &amp; Jilid Finishing</span>
                        <span id="badgeCountComponents" class="cal-badge-counter">{{ $totalComponents }}</span>
                    </button>
                    <button type="button" class="calendar-tab-btn" id="tabBtnRules" onclick="switchCalTab('rules')">
                        <i class="bi bi-graph-up-arrow"></i>
                        <span>4. Pricing Rules (Markup Tier)</span>
                        <span id="badgeCountRules" class="cal-badge-counter">{{ $totalRules }}</span>
                    </button>
                </div>

                {{-- TAB CONTENT WRAPPER --}}
                <div class="card" style="margin-top: 16px;">

                    {{-- TAB 1: PRODUK & UKURAN --}}
                    <div id="tabContentProducts" class="cal-tab-pane">
                        <div class="table-responsive">
                            <table id="calendarProductsTable" class="data-table display responsive nowrap"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 28px;"></th>
                                        <th style="width: 45px;" class="text-center">ID</th>
                                        <th class="text-center" style="width: 85px;">Kategori</th>
                                        <th>Nama Produk</th>
                                        <th class="text-center">Ukuran (cm)</th>
                                        <th class="text-center">Orientasi</th>
                                        <th class="text-center">Up A3+</th>
                                        <th>Mesin Offset</th>
                                        <th class="text-center">Up Offset</th>
                                        <th class="text-center">Plano</th>
                                        <th class="text-center">Def. Lembar</th>
                                        <th class="text-center" style="width: 105px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($products as $p)
                                        <tr id="row-product-{{ $p->id }}">
                                            <td class="dtr-control"></td>
                                            <td class="text-center">{{ $p->id }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge {{ $p->calendar_type === 'meja' ? 'primary' : 'success' }}"
                                                    style="text-transform: uppercase;">
                                                    {{ $p->calendar_type }}
                                                </span>
                                            </td>
                                            <td><strong>{{ $p->product_name }}</strong></td>
                                            <td class="text-center">{{ $p->final_width }} x {{ $p->final_height }}</td>
                                            <td class="text-center" style="text-transform: capitalize;">
                                                {{ $p->orientation }}</td>
                                            <td class="text-center">
                                                @if ($p->digital_available)
                                                    <span>{{ $p->digital_up }} up</span>
                                                @else
                                                    <span
                                                        style="color: var(--t-muted); font-size: 11.5px; font-style: italic;">Over
                                                        Area</span>
                                                @endif
                                            </td>
                                            <td>{{ $p->offset_machine }}</td>
                                            <td class="text-center">{{ $p->offset_up }} up
                                                {{ $p->offset_up_bw ? '(BW: ' . $p->offset_up_bw . ')' : '' }}</td>
                                            <td class="text-center">{{ $p->recommended_plano }}</td>
                                            <td class="text-center">{{ $p->default_leaf_count }} lbr</td>
                                            <td class="text-center">
                                                <div
                                                    style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                                    <button type="button" class="btn-detail-cal"
                                                        onclick='openDetailModal("product", @json($p))'
                                                        title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn-edit-cal"
                                                        onclick='openEditProductModal(@json($p))'
                                                        title="Ubah Parameter">
                                                        <i class="bi bi-pen"></i>
                                                    </button>
                                                    <button type="button" class="btn-delete-cal"
                                                        onclick='confirmDeleteCalendar("product", {{ $p->id }}, "{{ addslashes($p->product_name) }}")'
                                                        title="Hapus Produk">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 2: BAHAN KERTAS --}}
                    <div id="tabContentMaterials" class="cal-tab-pane" style="display: none;">
                        <div class="table-responsive">
                            <table id="calendarMaterialsTable" class="data-table display responsive nowrap"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 28px;"></th>
                                        <th style="width: 45px;" class="text-center">ID</th>
                                        <th>Nama Kertas</th>
                                        <th class="text-center" style="width: 80px;">Kat.</th>
                                        <th class="text-end">Print 1 Sisi A3+</th>
                                        <th class="text-end">Print 2 Sisi A3+</th>
                                        <th class="text-end">Kertas A3+</th>
                                        <th class="text-end">Plano 65x90</th>
                                        <th class="text-end">Plano 65x100</th>
                                        <th class="text-end">Plano 109x79</th>
                                        <th class="text-center" style="width: 105px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($materials as $m)
                                        <tr id="row-material-{{ $m->id }}">
                                            <td class="dtr-control"></td>
                                            <td class="text-center">{{ $m->id }}</td>
                                            <td><strong>{{ $m->material_name }}</strong></td>
                                            <td class="text-center"><span class="badge">{{ $m->category }}</span></td>
                                            <td class="text-end">Rp
                                                {{ number_format($m->digital_cost_print_1_side, 0, ',', '.') }}</td>
                                            <td class="text-end">Rp
                                                {{ number_format($m->digital_cost_print_2_side, 0, ',', '.') }}</td>
                                            <td class="text-end">Rp
                                                {{ number_format($m->digital_paper_cost, 0, ',', '.') }}</td>
                                            <td class="text-end">
                                                {{ $m->offset_price_65x90 ? 'Rp ' . number_format($m->offset_price_65x90, 0, ',', '.') : '-' }}
                                            </td>
                                            <td class="text-end">
                                                {{ $m->offset_price_65x100 ? 'Rp ' . number_format($m->offset_price_65x100, 0, ',', '.') : '-' }}
                                            </td>
                                            <td class="text-end">
                                                {{ $m->offset_price_109x79 ? 'Rp ' . number_format($m->offset_price_109x79, 0, ',', '.') : '-' }}
                                            </td>
                                            <td class="text-center">
                                                <div
                                                    style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                                    <button type="button" class="btn-detail-cal"
                                                        onclick='openDetailModal("material", @json($m))'
                                                        title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn-edit-cal"
                                                        onclick='openEditMaterialModal(@json($m))'
                                                        title="Ubah Tarif">
                                                        <i class="bi bi-pen"></i>
                                                    </button>
                                                    <button type="button" class="btn-delete-cal"
                                                        onclick='confirmDeleteCalendar("material", {{ $m->id }}, "{{ addslashes($m->material_name) }}")'
                                                        title="Hapus Bahan Kertas">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 3: KOMPONEN & AKSESORIS --}}
                    <div id="tabContentComponents" class="cal-tab-pane" style="display: none;">
                        <div class="table-responsive">
                            <table id="calendarComponentsTable" class="data-table display responsive nowrap"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 28px;"></th>
                                        <th style="width: 90px;">Kode</th>
                                        <th>Nama Komponen</th>
                                        <th class="text-center" style="width: 90px;">Tipe</th>
                                        <th class="text-center" style="width: 70px;">Satuan</th>
                                        <th class="text-end">Harga Satuan</th>
                                        <th class="text-end">Min. Biaya</th>
                                        <th>Keterangan</th>
                                        <th class="text-center" style="width: 105px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($components as $c)
                                        <tr id="row-component-{{ $c->id }}">
                                            <td class="dtr-control"></td>
                                            <td><code>{{ $c->component_code }}</code></td>
                                            <td><strong>{{ $c->name }}</strong></td>
                                            <td class="text-center"><span class="badge">{{ $c->component_type }}</span>
                                            </td>
                                            <td class="text-center">{{ $c->unit }}</td>
                                            <td class="text-end" style="font-weight: 700;">Rp
                                                {{ number_format($c->price, $c->price < 10 ? 2 : 0, ',', '.') }}</td>
                                            <td class="text-end">
                                                {{ $c->minimum_fee > 0 ? 'Rp ' . number_format($c->minimum_fee, 0, ',', '.') : '-' }}
                                            </td>
                                            <td style="font-size: 12px; color: var(--t-muted);">{{ $c->description }}</td>
                                            <td class="text-center">
                                                <div
                                                    style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                                    <button type="button" class="btn-detail-cal"
                                                        onclick='openDetailModal("component", @json($c))'
                                                        title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn-edit-cal"
                                                        onclick='openEditComponentModal(@json($c))'
                                                        title="Ubah Tarif">
                                                        <i class="bi bi-pen"></i>
                                                    </button>
                                                    <button type="button" class="btn-delete-cal"
                                                        onclick='confirmDeleteCalendar("component", {{ $c->id }}, "{{ addslashes($c->name) }}")'
                                                        title="Hapus Komponen">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 4: PRICING RULES --}}
                    <div id="tabContentRules" class="cal-tab-pane" style="display: none;">
                        <div class="table-responsive">
                            <table id="calendarRulesTable" class="data-table display responsive nowrap"
                                style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th style="width: 28px;"></th>
                                        <th style="width: 45px;" class="text-center">ID</th>
                                        <th>Nama Aturan Tier</th>
                                        <th class="text-center">Min Qty</th>
                                        <th class="text-center">Max Qty</th>
                                        <th class="text-center">Markup Keuntungan</th>
                                        <th class="text-end">Pembulatan Rupiah</th>
                                        <th class="text-center" style="width: 105px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pricingRules as $r)
                                        <tr id="row-rule-{{ $r->id }}">
                                            <td class="dtr-control"></td>
                                            <td class="text-center">{{ $r->id }}</td>
                                            <td><strong>{{ $r->rule_name }}</strong></td>
                                            <td class="text-center">{{ number_format($r->min_qty, 0, ',', '.') }} pcs</td>
                                            <td class="text-center">
                                                {{ $r->max_qty ? number_format($r->max_qty, 0, ',', '.') . ' pcs' : 'Tak Terbatas' }}
                                            </td>
                                            <td class="text-center" style="font-weight: 800; color: var(--primary);">
                                                +{{ $r->markup_percent }}%</td>
                                            <td class="text-end">Rp {{ number_format($r->rounding_value, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                <div
                                                    style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                                    <button type="button" class="btn-detail-cal"
                                                        onclick='openDetailModal("rule", @json($r))'
                                                        title="Lihat Detail">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn-edit-cal"
                                                        onclick='openEditRuleModal(@json($r))'
                                                        title="Ubah Markup">
                                                        <i class="bi bi-pen"></i>
                                                    </button>
                                                    <button type="button" class="btn-delete-cal"
                                                        onclick='confirmDeleteCalendar("rule", {{ $r->id }}, "Aturan Markup Tier Qty {{ $r->min_qty }} - {{ $r->max_qty ? $r->max_qty : '+' }} pcs")'
                                                        title="Hapus Aturan">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            </main>
            <div data-shell-footer></div>
        </div>
    </div>

    {{-- MODAL KONFIRMASI HAPUS DATA KALENDER (ALL TABS) --}}
    <div id="modalDeleteCalendar" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 440px;">
            <div class="modal-cal-head" style="border-bottom: 1px solid var(--border);">
                <div>
                    <span class="eyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">HAPUS DATA ·
                        KALENDER</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);">Konfirmasi Hapus
                    </h3>
                </div>
                <button type="button" onclick="closeDeleteModal()"
                    style="background: none; border: none; font-size: 22px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>
            <div class="modal-cal-body" style="padding: 22px;">
                <div style="display: flex; gap: 16px; align-items: flex-start;">
                    <div
                        style="width: 48px; height: 48px; min-width: 48px; border-radius: 12px; background: rgba(239, 68, 68, 0.12); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="bi bi-trash3-fill"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 6px; font-size: 15px; font-weight: 700; color: var(--t-base);">Apakah Anda
                            Yakin?</h4>
                        <p style="margin: 0; font-size: 13px; color: var(--t-muted); line-height: 1.5;">
                            Anda akan menghapus <strong id="deleteCalendarTargetName"
                                style="color: var(--t-base);"></strong>. Data yang sudah dihapus tidak dapat dipulihkan
                            kembali.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-cal-foot"
                style="padding: 14px 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; background: var(--bg-muted);">
                <button type="button" class="btn btn--secondary" onclick="closeDeleteModal()"
                    style="padding: 8px 16px; border-radius: 6px; border: 1px solid var(--border); background: var(--bg-card); color: var(--t-base); cursor: pointer; font-weight: 600; font-size: 13px;">Batal</button>
                <button type="button" class="btn btn--danger" id="btnConfirmDeleteCalendar"
                    onclick="executeDeleteCalendar()"
                    style="padding: 8px 16px; border-radius: 6px; border: none; background: #dc2626; color: #ffffff; cursor: pointer; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="bi bi-trash3-fill"></i>
                    <span>Ya, Hapus</span>
                </button>
            </div>
        </div>
    </div>

    {{-- MODAL DETAIL DATA KALENDER (ALL TABS) --}}
    <div id="modalDetailCalendar" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 620px;">
            <div class="modal-cal-head">
                <div>
                    <span class="eyebrow" id="calDetailEyebrow"
                        style="color: var(--primary); font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">DETAIL
                        DATA · KALENDER</span>
                    <h3 id="calDetailTitle"
                        style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);">Detail Data</h3>
                </div>
                <button type="button" onclick="closeCalModal('modalDetailCalendar')"
                    style="background: none; border: none; font-size: 22px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>
            <div class="modal-cal-body" style="padding: 20px;">
                <div id="calDetailContainer" style="display: flex; flex-direction: column; gap: 10px;"></div>
            </div>
            <div class="modal-cal-foot">
                <button type="button" class="btn btn--secondary"
                    onclick="closeCalModal('modalDetailCalendar')">Tutup</button>
            </div>
        </div>
    </div>

    {{-- MODAL EDIT MATERIAL --}}
    <div id="modalEditMaterial" class="modal-cal">
        <div class="modal-cal-content modal-lg">
            <div class="modal-cal-head">
                <h3 style="margin: 0; font-size: 15px;" id="modalMatTitle">Ubah Tarif Kertas Kalender</h3>
                <button type="button" onclick="closeCalModal('modalEditMaterial')"
                    style="background: none; border: none; font-size: 18px; cursor: pointer; color: var(--t-muted);">&times;</button>
            </div>
            <form id="formEditMaterial" onsubmit="submitEditMaterial(event)">
                @csrf
                <input type="hidden" id="matId" name="mat_id">
                <div class="modal-cal-body">
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px; margin-bottom: 14px;">
                        <div class="field">
                            <label class="field-label">Nama Kertas <span style="color: var(--danger);">*</span></label>
                            <input type="text" id="matName" name="material_name" class="input" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Kategori Kertas <span
                                    style="color: var(--danger);">*</span></label>
                            <select id="matCategory" name="category" class="select select2-modal" required>
                                <option value="AP">Art Paper (AP)</option>
                                <option value="AC">Art Carton (AC)</option>
                                <option value="BW">Blues White (BW)</option>
                            </select>
                        </div>
                    </div>

                    <div
                        style="padding: 12px 14px; background: var(--bg-surface-2); border: 1px solid var(--border); border-radius: 8px; margin-bottom: 14px;">
                        <div
                            style="font-size: 12px; font-weight: 700; color: var(--primary); margin-bottom: 10px; text-transform: uppercase;">
                            <i class="bi bi-printer me-1"></i> Tarif Cetak & Kertas Digital A3+
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                            <div class="field">
                                <label class="field-label">Print 1 Sisi A3+ <span
                                        style="color: var(--danger);">*</span></label>
                                <input type="text" id="matPrint1" name="digital_cost_print_1_side"
                                    class="input input-rupiah" required>
                            </div>
                            <div class="field">
                                <label class="field-label">Print 2 Sisi A3+ <span
                                        style="color: var(--danger);">*</span></label>
                                <input type="text" id="matPrint2" name="digital_cost_print_2_side"
                                    class="input input-rupiah" required>
                            </div>
                            <div class="field">
                                <label class="field-label">Harga Kertas A3+ <span
                                        style="color: var(--danger);">*</span></label>
                                <input type="text" id="matPaperA3" name="digital_paper_cost"
                                    class="input input-rupiah" required>
                            </div>
                        </div>
                    </div>

                    <div
                        style="padding: 12px 14px; background: var(--bg-surface-2); border: 1px solid var(--border); border-radius: 8px;">
                        <div
                            style="font-size: 12px; font-weight: 700; color: #10b981; margin-bottom: 10px; text-transform: uppercase;">
                            <i class="bi bi-box-seam me-1"></i> Harga Bahan Plano Offset (per Lembar Plano)
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                            <div class="field">
                                <label class="field-label">Plano 65 x 90 cm</label>
                                <input type="text" id="matPlano65x90" name="offset_price_65x90"
                                    class="input input-rupiah" placeholder="Rp -">
                            </div>
                            <div class="field">
                                <label class="field-label">Plano 65 x 100 cm</label>
                                <input type="text" id="matPlano65x100" name="offset_price_65x100"
                                    class="input input-rupiah" placeholder="Rp -">
                            </div>
                            <div class="field">
                                <label class="field-label">Plano 109 x 79 cm</label>
                                <input type="text" id="matPlano109x79" name="offset_price_109x79"
                                    class="input input-rupiah" placeholder="Rp -">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-cal-foot">
                    <button type="button" class="btn btn--ghost"
                        onclick="closeCalModal('modalEditMaterial')">Batal</button>
                    <button type="submit" class="btn btn--primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL TAMBAH PRODUK (LENGKAP: KATEGORI, UKURAN, DIGITAL, OFFSET & PLANO) --}}
    <div id="modalCreateProduct" class="modal-cal">
        <div class="modal-cal-content modal-lg">
            <div class="modal-cal-head">
                <h3 style="margin: 0; font-size: 15px;">Tambah Ukuran &amp; Produk Kalender</h3>
                <button type="button" onclick="closeCalModal('modalCreateProduct')"
                    style="background: none; border: none; font-size: 18px; cursor: pointer; color: var(--t-muted);">&times;</button>
            </div>
            <form id="formCreateProduct" onsubmit="submitCreateProduct(event)">
                @csrf
                <div class="modal-cal-body">
                    <!-- Baris 1: Kategori & Nama Produk -->
                    <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 12px; margin-bottom: 14px;">
                        <div class="field">
                            <label class="field-label">Kategori Kalender <span
                                    style="color: var(--danger);">*</span></label>
                            <select id="createProdCategory" name="calendar_type" class="select select2-modal" required>
                                <option value="dinding" selected>Kalender Dinding</option>
                                <option value="meja">Kalender Meja</option>
                            </select>
                        </div>
                        <div class="field">
                            <label class="field-label">Nama Produk <span style="color: var(--danger);">*</span></label>
                            <input type="text" id="createProdName" name="product_name" class="input"
                                placeholder="Misal: Kalender Dinding 32 x 48 cm" required>
                        </div>
                    </div>

                    <!-- Baris 2: Ukuran (Lebar x Tinggi cm) & Orientasi -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                        <div class="field">
                            <label class="field-label">Lebar Jadi (cm) <span
                                    style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" id="createProdWidth" name="final_width" class="input"
                                placeholder="32" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Tinggi Jadi (cm) <span
                                    style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" id="createProdHeight" name="final_height"
                                class="input" placeholder="48" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Orientasi <span style="color: var(--danger);">*</span></label>
                            <select id="createProdOrientation" name="orientation" class="select select2-modal" required>
                                <option value="portrait" selected>Portrait</option>
                                <option value="landscape">Landscape</option>
                            </select>
                        </div>
                    </div>

                    <!-- Baris 3: Parameter Digital A3+ -->
                    <div
                        style="padding: 12px 14px; background: var(--bg-surface-2); border: 1px solid var(--border); border-radius: 8px; margin-bottom: 14px;">
                        <div
                            style="font-size: 12px; font-weight: 700; color: var(--primary); margin-bottom: 10px; text-transform: uppercase;">
                            <i class="bi bi-printer me-1"></i> Parameter Digital Print A3+
                        </div>
                        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 12px;">
                            <div class="field">
                                <label class="field-label">Ketersediaan Digital</label>
                                <select id="createProdDigAvail" name="digital_available" class="select select2-modal">
                                    <option value="1" selected>Tersedia (Bisa Digital A3+)</option>
                                    <option value="0">Over Area (Khusus Offset)</option>
                                </select>
                            </div>
                            <div class="field">
                                <label class="field-label">Up A3+ (Pcs / lembar A3+) <span
                                        style="color: var(--danger);">*</span></label>
                                <input type="number" id="createProdDigUp" name="digital_up" class="input"
                                    value="1" min="0" required>
                            </div>
                        </div>
                    </div>

                    <!-- Baris 4: Parameter Mesin Offset & Plano -->
                    <div
                        style="padding: 12px 14px; background: var(--bg-surface-2); border: 1px solid var(--border); border-radius: 8px;">
                        <div
                            style="font-size: 12px; font-weight: 700; color: #10b981; margin-bottom: 10px; text-transform: uppercase;">
                            <i class="bi bi-gear me-1"></i> Parameter Mesin Offset &amp; Bahan Baku
                        </div>
                        <div style="display: grid; grid-template-columns: 1.2fr 1fr 1fr; gap: 12px; margin-bottom: 10px;">
                            <div class="field">
                                <label class="field-label">Mesin Offset Rekomendasi</label>
                                <select id="createProdOffsetMachine" name="offset_machine" class="select select2-modal">
                                    <option value="SM52" selected>Heidelberg SM52</option>
                                    <option value="Oliver66">Sakurai Oliver 66</option>
                                    <option value="SM72">Heidelberg SM72</option>
                                </select>
                            </div>
                            <div class="field">
                                <label class="field-label">Up Offset (FC) <span
                                        style="color: var(--danger);">*</span></label>
                                <input type="number" id="createProdOffUp" name="offset_up" class="input"
                                    value="1" min="1" required>
                            </div>
                            <div class="field">
                                <label class="field-label">Up Offset (BW - Opsional)</label>
                                <input type="number" id="createProdOffUpBw" name="offset_up_bw" class="input"
                                    min="0" placeholder="Opsional">
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 12px;">
                            <div class="field">
                                <label class="field-label">Ukuran Plano Rekomendasi</label>
                                <select id="createProdPlano" name="recommended_plano" class="select select2-modal">
                                    <option value="65x90" selected>Plano 65 x 90 cm</option>
                                    <option value="65x100">Plano 65 x 100 cm</option>
                                    <option value="109x79">Plano 109 x 79 cm</option>
                                </select>
                            </div>
                            <div class="field">
                                <label class="field-label">Default Lembar Isi <span
                                        style="color: var(--danger);">*</span></label>
                                <input type="number" id="createProdDefLeaf" name="default_leaf_count" class="input"
                                    value="7" min="1" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-cal-foot">
                    <button type="button" class="btn btn--ghost"
                        onclick="closeCalModal('modalCreateProduct')">Batal</button>
                    <button type="submit" class="btn btn--primary" id="btnSubmitCreateProduct">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL TAMBAH BAHAN KERTAS --}}
    <div id="modalCreateMaterial" class="modal-cal">
        <div class="modal-cal-content modal-lg">
            <div class="modal-cal-head">
                <h3 style="margin: 0; font-size: 15px;">Tambah Tarif Bahan Kertas Kalender</h3>
                <button type="button" onclick="closeCalModal('modalCreateMaterial')"
                    style="background: none; border: none; font-size: 18px; cursor: pointer; color: var(--t-muted);">&times;</button>
            </div>
            <form id="formCreateMaterial" onsubmit="submitCreateMaterial(event)">
                @csrf
                <div class="modal-cal-body">
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px; margin-bottom: 14px;">
                        <div class="field">
                            <label class="field-label">Nama Kertas <span style="color: var(--danger);">*</span></label>
                            <input type="text" id="createMatName" name="material_name" class="input"
                                placeholder="Misal: Art Paper 150 gsm" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Kategori Kertas <span
                                    style="color: var(--danger);">*</span></label>
                            <select id="createMatCategory" name="category" class="select select2-modal" required>
                                <option value="AP" selected>Art Paper (AP)</option>
                                <option value="AC">Art Carton (AC)</option>
                                <option value="BW">Blues White (BW)</option>
                            </select>
                        </div>
                    </div>

                    <div
                        style="padding: 12px 14px; background: var(--bg-surface-2); border: 1px solid var(--border); border-radius: 8px; margin-bottom: 14px;">
                        <div
                            style="font-size: 12px; font-weight: 700; color: var(--primary); margin-bottom: 10px; text-transform: uppercase;">
                            <i class="bi bi-printer me-1"></i> Tarif Cetak &amp; Kertas Digital A3+
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                            <div class="field">
                                <label class="field-label">Print 1 Sisi A3+ <span
                                        style="color: var(--danger);">*</span></label>
                                <input type="text" id="createMatPrint1" name="digital_cost_print_1_side"
                                    class="input input-rupiah" placeholder="Rp 0" required>
                            </div>
                            <div class="field">
                                <label class="field-label">Print 2 Sisi A3+ <span
                                        style="color: var(--danger);">*</span></label>
                                <input type="text" id="createMatPrint2" name="digital_cost_print_2_side"
                                    class="input input-rupiah" placeholder="Rp 0" required>
                            </div>
                            <div class="field">
                                <label class="field-label">Harga Kertas A3+ <span
                                        style="color: var(--danger);">*</span></label>
                                <input type="text" id="createMatPaperA3" name="digital_paper_cost"
                                    class="input input-rupiah" placeholder="Rp 0" required>
                            </div>
                        </div>
                    </div>

                    <div
                        style="padding: 12px 14px; background: var(--bg-surface-2); border: 1px solid var(--border); border-radius: 8px;">
                        <div
                            style="font-size: 12px; font-weight: 700; color: #10b981; margin-bottom: 10px; text-transform: uppercase;">
                            <i class="bi bi-box-seam me-1"></i> Harga Bahan Plano Offset (per Lembar Plano)
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px;">
                            <div class="field">
                                <label class="field-label">Plano 65 x 90 cm</label>
                                <input type="text" id="createMatPlano65x90" name="offset_price_65x90"
                                    class="input input-rupiah" placeholder="Rp -">
                            </div>
                            <div class="field">
                                <label class="field-label">Plano 65 x 100 cm</label>
                                <input type="text" id="createMatPlano65x100" name="offset_price_65x100"
                                    class="input input-rupiah" placeholder="Rp -">
                            </div>
                            <div class="field">
                                <label class="field-label">Plano 109 x 79 cm</label>
                                <input type="text" id="createMatPlano109x79" name="offset_price_109x79"
                                    class="input input-rupiah" placeholder="Rp -">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-cal-foot">
                    <button type="button" class="btn btn--ghost"
                        onclick="closeCalModal('modalCreateMaterial')">Batal</button>
                    <button type="submit" class="btn btn--primary" id="btnSubmitCreateMaterial">Simpan Bahan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL TAMBAH KOMPONEN / FINISHING --}}
    <div id="modalCreateComponent" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 540px;">
            <div class="modal-cal-head">
                <h3 style="margin: 0; font-size: 15px;">Tambah Komponen &amp; Jilid Finishing</h3>
                <button type="button" onclick="closeCalModal('modalCreateComponent')"
                    style="background: none; border: none; font-size: 18px; cursor: pointer; color: var(--t-muted);">&times;</button>
            </div>
            <form id="formCreateComponent" onsubmit="submitCreateComponent(event)">
                @csrf
                <div class="modal-cal-body">
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Nama Komponen <span style="color: var(--danger);">*</span></label>
                            <input type="text" id="createCompName" name="name" class="input"
                                placeholder="Misal: Klem Seng Kalender" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Kode (Opsional)</label>
                            <input type="text" id="createCompCode" name="component_code" class="input"
                                placeholder="Auto jika kosong">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Tipe Komponen <span style="color: var(--danger);">*</span></label>
                            <select id="createCompType" name="component_type" class="select select2-modal" required>
                                <option value="finishing_jilid" selected>Finishing Jilid</option>
                                <option value="aksesoris">Aksesoris / Stand</option>
                                <option value="laminasi">Laminasi</option>
                                <option value="packaging">Packaging</option>
                            </select>
                        </div>
                        <div class="field">
                            <label class="field-label">Satuan <span style="color: var(--danger);">*</span></label>
                            <input type="text" id="createCompUnit" name="unit" class="input" value="pcs"
                                placeholder="pcs / exemplar" required>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Tarif / Harga Satuan <span
                                    style="color: var(--danger);">*</span></label>
                            <input type="text" id="createCompPrice" name="price" class="input input-rupiah"
                                placeholder="Rp 0" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Biaya Minimum Job (Opsional)</label>
                            <input type="text" id="createCompMinFee" name="minimum_fee" class="input input-rupiah"
                                placeholder="Rp 0">
                        </div>
                    </div>
                    <div class="field">
                        <label class="field-label">Keterangan / Fungsi</label>
                        <input type="text" id="createCompDesc" name="description" class="input"
                            placeholder="Keterangan komponen">
                    </div>
                </div>
                <div class="modal-cal-foot">
                    <button type="button" class="btn btn--ghost"
                        onclick="closeCalModal('modalCreateComponent')">Batal</button>
                    <button type="submit" class="btn btn--primary" id="btnSubmitCreateComponent">Simpan
                        Komponen</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL TAMBAH PRICING RULE (MARKUP TIER) --}}
    <div id="modalCreateRule" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 540px;">
            <div class="modal-cal-head">
                <h3 style="margin: 0; font-size: 15px;">Tambah Aturan Pricing Markup Tier</h3>
                <button type="button" onclick="closeCalModal('modalCreateRule')"
                    style="background: none; border: none; font-size: 18px; cursor: pointer; color: var(--t-muted);">&times;</button>
            </div>
            <form id="formCreateRule" onsubmit="submitCreateRule(event)">
                @csrf
                <div class="modal-cal-body">
                    <div class="field" style="margin-bottom: 12px;">
                        <label class="field-label">Nama Aturan Tier <span style="color: var(--danger);">*</span></label>
                        <input type="text" id="createRuleName" name="rule_name" class="input"
                            placeholder="Misal: Tier 500 - 1.000 pcs" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Min. Qty (pcs) <span style="color: var(--danger);">*</span></label>
                            <input type="number" id="createRuleMinQty" name="min_qty" class="input" placeholder="500"
                                required>
                        </div>
                        <div class="field">
                            <label class="field-label">Max. Qty (pcs - Kosongkan jika Tak Terbatas)</label>
                            <input type="number" id="createRuleMaxQty" name="max_qty" class="input"
                                placeholder="Tak Terbatas">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                        <div class="field">
                            <label class="field-label">Markup Persen (%) <span
                                    style="color: var(--danger);">*</span></label>
                            <input type="number" step="0.01" id="createRuleMarkup" name="markup_percent"
                                class="input" placeholder="30" required>
                        </div>
                        <div class="field">
                            <label class="field-label">Pembulatan Rupiah (Ceiling) <span
                                    style="color: var(--danger);">*</span></label>
                            <input type="text" id="createRuleRounding" name="rounding_value"
                                class="input input-rupiah" value="Rp 500" required>
                        </div>
                    </div>
                </div>
                <div class="modal-cal-foot">
                    <button type="button" class="btn btn--ghost"
                        onclick="closeCalModal('modalCreateRule')">Batal</button>
                    <button type="submit" class="btn btn--primary" id="btnSubmitCreateRule">Simpan Aturan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentCalTab = 'products';

        function switchCalTab(tabName) {
            currentCalTab = tabName;
            try { sessionStorage.setItem('activeCalTab', tabName); } catch (e) {}
            document.querySelectorAll('.calendar-tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.cal-tab-pane').forEach(pane => pane.style.display = 'none');

            const labelMap = {
                'products': 'Tambah Ukuran Produk',
                'materials': 'Tambah Bahan Kertas',
                'components': 'Tambah Komponen Finishing',
                'rules': 'Tambah Pricing Rule'
            };

            const btnLabel = document.getElementById('labelMainCreateCal');
            if (btnLabel && labelMap[tabName]) {
                btnLabel.textContent = labelMap[tabName];
            }

            if (tabName === 'products') {
                document.getElementById('tabBtnProducts').classList.add('active');
                document.getElementById('tabContentProducts').style.display = 'block';
            } else if (tabName === 'materials') {
                document.getElementById('tabBtnMaterials').classList.add('active');
                document.getElementById('tabContentMaterials').style.display = 'block';
            } else if (tabName === 'components') {
                document.getElementById('tabBtnComponents').classList.add('active');
                document.getElementById('tabContentComponents').style.display = 'block';
            } else if (tabName === 'rules') {
                document.getElementById('tabBtnRules').classList.add('active');
                document.getElementById('tabContentRules').style.display = 'block';
            }

            // Recalculate and adjust responsive DataTables on tab switch (referensi offset)
            setTimeout(() => {
                if (typeof $.fn.dataTable !== 'undefined') {
                    $.fn.dataTable.tables({
                        visible: true,
                        api: true
                    }).columns.adjust().responsive.recalc();
                }
            }, 50);
        }

        // Buka modal Create dinamis sesuai tab yang sedang aktif
        function openActiveCreateModal() {
            let modalId = 'modalCreateProduct';
            if (currentCalTab === 'materials') {
                modalId = 'modalCreateMaterial';
            } else if (currentCalTab === 'components') {
                modalId = 'modalCreateComponent';
            } else if (currentCalTab === 'rules') {
                modalId = 'modalCreateRule';
            }

            const modalEl = document.getElementById(modalId);
            if (modalEl) {
                const form = modalEl.querySelector('form');
                if (form) form.reset();
                modalEl.classList.add('is-open');
                initCalModalSelect2(modalId);
            }
        }

        // =========================================================
        // CREATE HANDLERS FOR CALENDAR MASTER DATA (ALL TABS)
        // =========================================================
        function submitCreateProduct(e) {
            e.preventDefault();
            const form = document.getElementById('formCreateProduct');
            const data = new FormData(form);
            const $btn = $('#btnSubmitCreateProduct');
            const origHtml = $btn.html();
            $btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status"></span> Menyimpan...');

            fetch("{{ route('admin_store_calendar_product') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    $btn.prop('disabled', false).html(origHtml);
                    if (res.success) {
                        if (typeof showToast === 'function') {
                            showToast('success', res.message);
                        } else {
                            alert(res.message);
                        }
                        closeCalModal('modalCreateProduct');
                        setTimeout(() => location.reload(), 500);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast('error', res.message);
                        } else {
                            alert('Gagal: ' + res.message);
                        }
                    }
                })
                .catch(err => {
                    $btn.prop('disabled', false).html(origHtml);
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
        }

        function submitCreateMaterial(e) {
            e.preventDefault();
            const form = document.getElementById('formCreateMaterial');
            const data = new FormData(form);
            const $btn = $('#btnSubmitCreateMaterial');
            const origHtml = $btn.html();
            $btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status"></span> Menyimpan...');

            form.querySelectorAll('.input-rupiah').forEach(input => {
                const clean = cleanRupiahValue(input.value);
                if (clean !== '') {
                    data.set(input.name, clean);
                } else {
                    data.delete(input.name);
                }
            });

            fetch("{{ route('admin_store_calendar_material') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    $btn.prop('disabled', false).html(origHtml);
                    if (res.success) {
                        if (typeof showToast === 'function') {
                            showToast('success', res.message);
                        } else {
                            alert(res.message);
                        }
                        closeCalModal('modalCreateMaterial');
                        setTimeout(() => location.reload(), 500);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast('error', res.message);
                        } else {
                            alert('Gagal: ' + res.message);
                        }
                    }
                })
                .catch(err => {
                    $btn.prop('disabled', false).html(origHtml);
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
        }

        function submitCreateComponent(e) {
            e.preventDefault();
            const form = document.getElementById('formCreateComponent');
            const data = new FormData(form);
            const $btn = $('#btnSubmitCreateComponent');
            const origHtml = $btn.html();
            $btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status"></span> Menyimpan...');

            form.querySelectorAll('.input-rupiah').forEach(input => {
                const clean = cleanRupiahValue(input.value);
                data.set(input.name, clean);
            });

            fetch("{{ route('admin_store_calendar_component') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    $btn.prop('disabled', false).html(origHtml);
                    if (res.success) {
                        if (typeof showToast === 'function') {
                            showToast('success', res.message);
                        } else {
                            alert(res.message);
                        }
                        closeCalModal('modalCreateComponent');
                        setTimeout(() => location.reload(), 500);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast('error', res.message);
                        } else {
                            alert('Gagal: ' + res.message);
                        }
                    }
                })
                .catch(err => {
                    $btn.prop('disabled', false).html(origHtml);
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
        }

        function submitCreateRule(e) {
            e.preventDefault();
            const form = document.getElementById('formCreateRule');
            const data = new FormData(form);
            const $btn = $('#btnSubmitCreateRule');
            const origHtml = $btn.html();
            $btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status"></span> Menyimpan...');

            form.querySelectorAll('.input-rupiah').forEach(input => {
                const clean = cleanRupiahValue(input.value);
                data.set(input.name, clean);
            });

            fetch("{{ route('admin_store_calendar_rule') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    $btn.prop('disabled', false).html(origHtml);
                    if (res.success) {
                        if (typeof showToast === 'function') {
                            showToast('success', res.message);
                        } else {
                            alert(res.message);
                        }
                        closeCalModal('modalCreateRule');
                        setTimeout(() => location.reload(), 500);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast('error', res.message);
                        } else {
                            alert('Gagal: ' + res.message);
                        }
                    }
                })
                .catch(err => {
                    $btn.prop('disabled', false).html(origHtml);
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
        }

        // Inisialisasi DataTables Responsif untuk Seluruh Tab Kalender
        $(document).ready(function() {
            function initCalDataTable(selector, placeholder) {
                if (!$.fn.DataTable.isDataTable(selector)) {
                    return $(selector).DataTable({
                        pageLength: 10,
                        lengthMenu: [10, 25, 50, 100],
                        ordering: true,
                        responsive: {
                            details: {
                                type: 'column',
                                target: 0
                            }
                        },
                        columnDefs: [{
                                className: 'dtr-control',
                                orderable: false,
                                searchable: false,
                                targets: 0
                            },
                            {
                                orderable: false,
                                searchable: false,
                                responsivePriority: 1,
                                targets: -1
                            }
                        ],
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
                            zeroRecords: 'Data tidak ditemukan',
                            processing: 'Memuat data...',
                            searchPlaceholder: placeholder || 'Cari data...'
                        },
                        initComplete: function() {
                            $(this).closest('.dt-container, .dataTables_wrapper')
                                .find('.dt-search input, .dataTables_filter input')
                                .css({
                                    'padding': '8px 14px',
                                    'border-radius': '8px'
                                });
                        }
                    });
                }
            }

            window.calTables = {
                product: initCalDataTable('#calendarProductsTable', 'Cari ukuran produk...'),
                material: initCalDataTable('#calendarMaterialsTable', 'Cari bahan kertas...'),
                component: initCalDataTable('#calendarComponentsTable', 'Cari komponen finishing...'),
                rule: initCalDataTable('#calendarRulesTable', 'Cari aturan tier markup...')
            };
        });

        function closeCalModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('is-open');
        }

        // Helper Format Rupiah
        function formatRupiah(num) {
            return Number(num || 0).toLocaleString('id-ID');
        }

                // Helper Baris Detail Key-Value & Section Box
        function makeDetailRow(label, value) {
            return `
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 9px 13px; background: var(--bg-surface-2); border: 1px solid var(--border); border-radius: 8px; gap: 10px;">
                    <span style="font-size: 12.5px; color: var(--t-muted); font-weight: 500;">${label}</span>
                    <span style="font-size: 12.5px; color: var(--t-base); font-weight: 600; text-align: right;">${value}</span>
                </div>
            `;
        }

        function makeDetailSectionTitle(title) {
            return `<div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--primary); margin-top: 10px; margin-bottom: 2px;">${title}</div>`;
        }

        function makeDetailInfoBox(icon, title, desc) {
            return `
                <div style="padding: 10px 14px; background: rgba(59, 130, 246, 0.05); border: 1px solid rgba(59, 130, 246, 0.2); border-radius: 8px; font-size: 12.5px; color: var(--t-base); line-height: 1.45;">
                    <div style="color: var(--primary); font-weight: 700; display: flex; align-items: center; gap: 6px; margin-bottom: 3px;">
                        <span>${icon}</span> <span>${title}</span>
                    </div>
                    <div style="color: var(--t-muted); font-size: 11.5px;">${desc}</div>
                </div>
            `;
        }

        // Fungsi Buka Modal Detail untuk Seluruh Tab Kalender (Lengkap dengan Komponen Terkait)
        function openDetailModal(type, data) {
            const container = document.getElementById('calDetailContainer');
            container.innerHTML = '';

            if (type === 'product') {
                document.getElementById('calDetailEyebrow').textContent = 'DETAIL · UKURAN & PRODUK KALENDER';
                document.getElementById('calDetailTitle').textContent = data.product_name;

                const catBadge = data.calendar_type === 'meja' ?
                    '<span class="badge primary" style="text-transform: uppercase;">KALENDER MEJA</span>' :
                    '<span class="badge success" style="text-transform: uppercase;">KALENDER DINDING</span>';

                const digSupport = data.digital_available ?
                    `<span style="color: #10b981; font-weight: 700;"><i class="bi bi-check-circle-fill"></i> Mendukung (${data.digital_up} pcs / Lbr A3+, max ${data.digital_max_leaf || 13} lbr)</span>` :
                    '<span style="color: var(--t-muted); font-style: italic;">Over Area (Hanya Mesin Offset)</span>';

                const statusBadge = (data.status || 'active').toLowerCase() === 'active' ?
                    '<span class="badge success">AKTIF</span>' : '<span class="badge danger">NON-AKTIF</span>';

                container.innerHTML += makeDetailSectionTitle('1. Informasi Dasar Produk');
                container.innerHTML += makeDetailRow('Nama Produk', `<strong>${data.product_name}</strong>`);
                container.innerHTML += makeDetailRow('Kategori Kalender', catBadge);
                container.innerHTML += makeDetailRow('Ukuran Jadi (P × L)', `${data.final_width} × ${data.final_height} cm`);
                container.innerHTML += makeDetailRow('Orientasi Desain', `<span style="text-transform: capitalize;">${data.orientation}</span>`);
                container.innerHTML += makeDetailRow('Default Lembar Isi', `${data.default_leaf_count} Lembar`);
                container.innerHTML += makeDetailRow('Min. Order Kuantitas', `${formatRupiah(data.min_order_qty || 1)} pcs`);
                container.innerHTML += makeDetailRow('Status Produk', statusBadge);

                container.innerHTML += makeDetailSectionTitle('2. Kapasitas Jalur Produksi');
                container.innerHTML += makeDetailRow('Digital Print A3+', digSupport);
                container.innerHTML += makeDetailRow('Mesin Offset Utama', `<strong>${data.offset_machine}</strong>`);
                container.innerHTML += makeDetailRow('Kapasitas Up Offset', `${data.offset_up} up ${data.offset_up_bw ? '<span style="font-size: 11px; color: var(--t-muted);">(BW: ' + data.offset_up_bw + ' up)</span>' : ''}`);
                container.innerHTML += makeDetailRow('Plano Rekomendasi', `<code style="padding: 2px 6px; background: var(--bg-card); border: 1px solid var(--border); border-radius: 4px;">${data.recommended_plano}</code>`);

                container.innerHTML += makeDetailSectionTitle('3. Komponen Finishing Terkait (Auto Estimasi)');
                if (data.calendar_type === 'dinding') {
                    const jepitType = data.final_width <= 31 ? 'Kecil (30–31 cm, ACC-JK-S: Rp 2.000/pcs)' : (data.final_width <= 40 ? 'Medium (35–40 cm, ACC-JK-M: Rp 2.500/pcs)' : 'Besar (44–50 cm, ACC-JK-L: Rp 3.000/pcs)');
                    container.innerHTML += makeDetailInfoBox('📎', 'Finishing Utama: Jepit Klem Seng', `Menggunakan Jepit Kaleng ${jepitType}. Dihitung otomatis per pcs saat estimasi kalender dinding.`);
                    container.innerHTML += makeDetailInfoBox('🌀', 'Finishing Opsional: Spiral Kawat + Hanger', 'Menggunakan Kawat Spiral Wire-O (ACC-SPIRAL: Rp 150/cm panjang jilid) + Hanger Gantungan Kawat (ACC-HANGER: Rp 500/pcs).');
                } else {
                    container.innerHTML += makeDetailInfoBox('📐', 'Dudukan / Penopang Hardcover', 'Menggunakan Karton Board Boot 30/40 (STAND-B30/40 Plano) dilapisi Art Paper 150 cetak full color (luar) dan Art Paper 120 polos (dalam).');
                    container.innerHTML += makeDetailInfoBox('🌀', 'Jilid Spiral Wire-O & Rakit Meja', `Kawat spiral binding panjang ${data.final_width} cm (ACC-SPIRAL: Rp 150/cm) + Ongkos potong & perakitan dudukan meja (FIN-MEJA-GABUNG: Rp 2.000/pcs).`);
                }
            } else if (type === 'material') {
                document.getElementById('calDetailEyebrow').textContent = 'DETAIL · TARIF BAHAN KERTAS';
                document.getElementById('calDetailTitle').textContent = data.material_name;

                const statusBadge = (data.status || 'active').toLowerCase() === 'active' ?
                    '<span class="badge success">AKTIF</span>' : '<span class="badge danger">NON-AKTIF</span>';

                const recomText = data.category === 'AP' ?
                    'Sangat direkomendasikan untuk <strong>Kalender Dinding</strong> (120 gsm ekonomis, 150 gsm standar terfavorit) serta kertas lapisan penopang kalender meja.' :
                    'Sangat direkomendasikan untuk <strong>Kalender Meja</strong> (210/230/260/310 gsm kaku & kokoh) serta poster kalender dinding premium/eksklusif.';

                container.innerHTML += makeDetailSectionTitle('1. Informasi Bahan');
                container.innerHTML += makeDetailRow('Nama Bahan Kertas', `<strong>${data.material_name}</strong>`);
                container.innerHTML += makeDetailRow('Kategori Kertas', `<span class="badge">${data.category}</span>`);
                container.innerHTML += makeDetailRow('Status Bahan', statusBadge);
                container.innerHTML += makeDetailInfoBox('💡', 'Rekomendasi Penggunaan', recomText);

                container.innerHTML += makeDetailSectionTitle('2. Ongkos Produksi Digital Print A3+');
                container.innerHTML += makeDetailRow('Ongkos Cetak 1 Sisi (A3+)', `<span style="color: var(--primary); font-weight: 700;">Rp ${formatRupiah(data.digital_cost_print_1_side)}</span>`);
                container.innerHTML += makeDetailRow('Ongkos Cetak 2 Sisi (A3+)', `<span style="color: var(--primary); font-weight: 700;">Rp ${formatRupiah(data.digital_cost_print_2_side)}</span>`);
                container.innerHTML += makeDetailRow('Harga Kertas A3+ per Lembar', `Rp ${formatRupiah(data.digital_paper_cost)}`);

                container.innerHTML += makeDetailSectionTitle('3. Tarif Kertas Plano (Cetak Offset)');
                container.innerHTML += makeDetailRow('Harga Plano 65 × 90 cm', data.offset_price_65x90 ? `Rp ${formatRupiah(data.offset_price_65x90)}` : '-');
                container.innerHTML += makeDetailRow('Harga Plano 65 × 100 cm', data.offset_price_65x100 ? `Rp ${formatRupiah(data.offset_price_65x100)}` : '-');
                container.innerHTML += makeDetailRow('Harga Plano 109 × 79 cm', data.offset_price_109x79 ? `Rp ${formatRupiah(data.offset_price_109x79)}` : '-');
            } else if (type === 'component') {
                document.getElementById('calDetailEyebrow').textContent = 'DETAIL · AKSESORIS & JILID FINISHING';
                document.getElementById('calDetailTitle').textContent = data.name;

                const statusBadge = (data.status || 'active').toLowerCase() === 'active' ?
                    '<span class="badge success">AKTIF</span>' : '<span class="badge danger">NON-AKTIF</span>';

                let appTarget = 'Umum / Aksesoris Kalender';
                let appDesc = 'Komponen pendukung biaya produksi kalender.';
                const cCode = data.component_code || '';
                const cType = data.component_type || '';

                if (cCode.startsWith('ACC-JK') || cType === 'jepit_kaleng') {
                    appTarget = 'Kalender Dinding (Jepit Klem Seng)';
                    appDesc = 'Diterapkan otomatis per pcs pada kalender dinding sesuai ukuran lebar produk.';
                } else if (cCode === 'ACC-SPIRAL' || cType === 'spiral') {
                    appTarget = 'Kalender Meja & Kalender Dinding Spiral';
                    appDesc = 'Kawat spiral Wire-O dihitung berdasarkan tarif per cm dikali lebar binding produk.';
                } else if (cCode === 'ACC-HANGER' || cType === 'hanger') {
                    appTarget = 'Kalender Dinding Spiral';
                    appDesc = 'Hanger kawat lengkung gantungan tengah kalender dinding jilid spiral.';
                } else if (cCode.startsWith('STAND-') || cType.includes('penopang') || cType === 'cover_penopang') {
                    appTarget = 'Dudukan Kalender Meja (Hardcover Stand)';
                    appDesc = 'Karton boot & kertas pelapis dudukan segitiga kalender meja (printing custom atau beli jadi polos).';
                } else if (cCode === 'FIN-MEJA-GABUNG' || cType === 'finishing_rakit') {
                    appTarget = 'Kalender Meja (Ongkos Perakitan)';
                    appDesc = 'Paket jasa potong isi lembaran, plong lubang spiral, pasang kawat, dan rakit ke dudukan.';
                } else if (cCode.startsWith('LAM-') || cType.includes('laminasi')) {
                    appTarget = 'Laminasi Cover / Lembar (Opsional)';
                    appDesc = 'Lapisan pelindung doff/glossy baik rute digital (per lembar A3+) maupun offset (per cm² luas cetak).';
                } else if (cCode.startsWith('PARAM-') || cType === 'insheet') {
                    appTarget = 'Parameter Cetak Offset (Toleransi Buangan)';
                    appDesc = 'Kebutuhan lembar insheet tambahan per form/plat untuk proses stel warna mesin offset.';
                }

                container.innerHTML += makeDetailSectionTitle('1. Identitas Komponen');
                container.innerHTML += makeDetailRow('Nama Komponen', `<strong>${data.name}</strong>`);
                container.innerHTML += makeDetailRow('Kode Komponen', `<code>${data.component_code}</code>`);
                container.innerHTML += makeDetailRow('Tipe Komponen', `<span class="badge">${data.component_type}</span>`);
                container.innerHTML += makeDetailRow('Status Komponen', statusBadge);

                container.innerHTML += makeDetailSectionTitle('2. Tarif & Biaya');
                container.innerHTML += makeDetailRow('Satuan Hitungan', data.unit);
                container.innerHTML += makeDetailRow('Tarif Satuan', `<span style="color: var(--primary); font-weight: 700;">Rp ${Number(data.price).toLocaleString('id-ID', { minimumFractionDigits: (data.price < 10 ? 2 : 0) })} / ${data.unit}</span>`);
                container.innerHTML += makeDetailRow('Biaya Minimum Job', data.minimum_fee > 0 ? `Rp ${formatRupiah(data.minimum_fee)}` : 'Tanpa Minimum');

                container.innerHTML += makeDetailSectionTitle('3. Peruntukan & Cara Hitung');
                container.innerHTML += makeDetailRow('Kategori Aplikasi', `<strong>${appTarget}</strong>`);
                container.innerHTML += makeDetailInfoBox('⚙️', 'Fungsi dalam Kalkulasi', `${data.description || '-'} &bull; ${appDesc}`);
            } else if (type === 'rule') {
                document.getElementById('calDetailEyebrow').textContent = 'DETAIL · PRICING RULE (MARKUP TIER)';
                document.getElementById('calDetailTitle').textContent = data.rule_name;

                const statusBadge = (data.status || 'active').toLowerCase() === 'active' ?
                    '<span class="badge success">AKTIF</span>' : '<span class="badge danger">NON-AKTIF</span>';

                container.innerHTML += makeDetailSectionTitle('1. Spesifikasi Tier Margin');
                container.innerHTML += makeDetailRow('Nama Aturan Tier', `<strong>${data.rule_name}</strong>`);
                container.innerHTML += makeDetailRow('Rentang Kuantitas Order', `${formatRupiah(data.min_qty)} s.d. ${data.max_qty ? formatRupiah(data.max_qty) + ' pcs' : 'Tak Terbatas'}`);
                container.innerHTML += makeDetailRow('Markup Keuntungan', `<span style="color: var(--primary); font-weight: 800; font-size: 15px;">+${data.markup_percent}%</span>`);
                container.innerHTML += makeDetailRow('Nilai Pembulatan (Ceiling)', `Rp ${formatRupiah(data.rounding_value)} (Dibulatkan ke atas)`);
                container.innerHTML += makeDetailRow('Status Aturan', statusBadge);

                container.innerHTML += makeDetailSectionTitle('2. Simulasi Penerapan Harga');
                const contohHPP = 10000;
                const markupVal = contohHPP * (parseFloat(data.markup_percent) / 100);
                const hargaSebelum = contohHPP + markupVal;
                const ceiling = parseInt(data.rounding_value) || 500;
                const hargaSetelah = Math.ceil(hargaSebelum / ceiling) * ceiling;

                container.innerHTML += makeDetailInfoBox('📊', 'Contoh Perhitungan Tier Ini',
                    `Jika HPP = Rp ${formatRupiah(contohHPP)}, ditambah markup ${data.markup_percent}% (+Rp ${formatRupiah(markupVal)}) = Rp ${formatRupiah(hargaSebelum)}. ` +
                    `Setelah pembulatan ceiling Rp ${formatRupiah(ceiling)}, Harga Jual Final = <strong>Rp ${formatRupiah(hargaSetelah)} / pcs</strong>.`
                );
            }

            document.getElementById('modalDetailCalendar').classList.add('is-open');
        }

        // Close on backdrop overlay click
        document.addEventListener('DOMContentLoaded', function() {
            try {
                const savedTab = sessionStorage.getItem('activeCalTab');
                if (savedTab && ['products', 'materials', 'components', 'rules'].includes(savedTab)) {
                    switchCalTab(savedTab);
                }
            } catch (e) {}
            document.querySelectorAll('.modal-cal').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.remove('is-open');
                    }
                });
            });
        });

        // Close on ESC key press
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.key === 'Esc') {
                document.querySelectorAll('.modal-cal.is-open').forEach(modal => {
                    modal.classList.remove('is-open');
                });
            }
        });

        // Inisialisasi Select2 dalam Modal
        function initCalModalSelect2(modalId) {
            if (typeof $.fn.select2 !== 'undefined') {
                $(`#${modalId} .select2-modal`).each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                    $(this).select2({
                        dropdownParent: $(`#${modalId}`),
                        width: '100%',
                        minimumResultsForSearch: 10
                    });
                });
            }
        }

        // Helper Format Rupiah Input & Display
        function formatRupiahDisplay(val) {
            if (val === null || val === undefined || val === '') return '';
            let num = Number(val);
            if (isNaN(num)) return val;
            if (num % 1 !== 0) {
                return 'Rp ' + num.toLocaleString('id-ID', {
                    minimumFractionDigits: 1,
                    maximumFractionDigits: 2
                });
            }
            return 'Rp ' + num.toLocaleString('id-ID');
        }

        function cleanRupiahValue(val) {
            if (val === null || val === undefined || val === '') return '';
            let s = String(val).replace(/Rps*/g, '').trim();
            if (s.includes(',')) {
                s = s.replace(/\./g, '').replace(',', '.');
            } else {
                let dotCount = (s.match(/\./g) || []).length;
                if (dotCount > 1) {
                    s = s.replace(/\./g, '');
                } else if (dotCount === 1) {
                    let parts = s.split('.');
                    if (parts[1].length === 3) {
                        s = s.replace(/\./g, '');
                    }
                }
            }
            return parseFloat(s) || 0;
        }

        // Live Format Rupiah saat pengguna mengetik
        $(document).on('input', '.input-rupiah', function() {
            let val = $(this).val();
            if (!val) return;
            let clean = val.replace(/[^0-9,]/g, '');
            let parts = clean.split(',');
            let intPart = parts[0].replace(/\D/g, '');
            let formatted = intPart ? intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
            if (parts.length > 1) {
                formatted += ',' + parts[1].slice(0, 2);
            }
            $(this).val(formatted ? 'Rp ' + formatted : '');
        });

        // 1. Material Modal
        function openEditMaterialModal(mat) {
            document.getElementById('matId').value = mat.id;
            document.getElementById('modalMatTitle').textContent = 'Ubah Tarif: ' + mat.material_name;
            document.getElementById('matName').value = mat.material_name;
            $('#matCategory').val(mat.category);

            document.getElementById('matPrint1').value = formatRupiahDisplay(mat.digital_cost_print_1_side);
            document.getElementById('matPrint2').value = formatRupiahDisplay(mat.digital_cost_print_2_side);
            document.getElementById('matPaperA3').value = formatRupiahDisplay(mat.digital_paper_cost);
            document.getElementById('matPlano65x90').value = mat.offset_price_65x90 ? formatRupiahDisplay(mat
                .offset_price_65x90) : '';
            document.getElementById('matPlano65x100').value = mat.offset_price_65x100 ? formatRupiahDisplay(mat
                .offset_price_65x100) : '';
            document.getElementById('matPlano109x79').value = mat.offset_price_109x79 ? formatRupiahDisplay(mat
                .offset_price_109x79) : '';

            document.getElementById('modalEditMaterial').classList.add('is-open');
            initCalModalSelect2('modalEditMaterial');
        }

        function submitEditMaterial(e) {
            e.preventDefault();
            const id = document.getElementById('matId').value;
            const form = document.getElementById('formEditMaterial');
            const data = new FormData(form);

            // Bersihkan format rupiah
            form.querySelectorAll('.input-rupiah').forEach(input => {
                const clean = cleanRupiahValue(input.value);
                if (clean !== '') {
                    data.set(input.name, clean);
                } else {
                    data.delete(input.name);
                }
            });

            fetch(`/admin/calendar-master/update-material/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-HTTP-Method-Override': 'PUT',
                        'Accept': 'application/json'
                    },
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        if (typeof showToast === 'function') {
                            showToast('success', res.message);
                        } else {
                            alert(res.message);
                        }
                        closeCalModal('modalEditMaterial');
                        setTimeout(() => location.reload(), 600);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast('error', res.message);
                        } else {
                            alert('Gagal: ' + res.message);
                        }
                    }
                })
                .catch(err => {
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
        }

        // 2. Component Modal
        function openEditComponentModal(comp) {
            document.getElementById('compId').value = comp.id;
            document.getElementById('modalCompTitle').textContent = 'Ubah Tarif: ' + comp.name;
            document.getElementById('compName').value = comp.name;
            document.getElementById('compUnit').value = comp.unit;
            document.getElementById('compPrice').value = formatRupiahDisplay(comp.price);
            document.getElementById('compMinFee').value = comp.minimum_fee ? formatRupiahDisplay(comp.minimum_fee) : '';
            document.getElementById('compDesc').value = comp.description || '';

            document.getElementById('modalEditComponent').classList.add('is-open');
        }

        function submitEditComponent(e) {
            e.preventDefault();
            const id = document.getElementById('compId').value;
            const form = document.getElementById('formEditComponent');
            const data = new FormData(form);

            // Bersihkan format rupiah
            form.querySelectorAll('.input-rupiah').forEach(input => {
                const clean = cleanRupiahValue(input.value);
                data.set(input.name, clean);
            });

            fetch(`/admin/calendar-master/update-component/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-HTTP-Method-Override': 'PUT',
                        'Accept': 'application/json'
                    },
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        if (typeof showToast === 'function') {
                            showToast('success', res.message);
                        } else {
                            alert(res.message);
                        }
                        closeCalModal('modalEditComponent');
                        setTimeout(() => location.reload(), 600);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast('error', res.message);
                        } else {
                            alert('Gagal: ' + res.message);
                        }
                    }
                })
                .catch(err => {
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
        }

        // 3. Rule Modal
        function openEditRuleModal(rule) {
            document.getElementById('ruleId').value = rule.id;
            document.getElementById('modalRuleTitle').textContent = 'Ubah Markup: ' + rule.rule_name;
            document.getElementById('ruleName').value = rule.rule_name;
            document.getElementById('ruleMinQty').value = rule.min_qty;
            document.getElementById('ruleMaxQty').value = rule.max_qty || '';
            document.getElementById('ruleMarkup').value = rule.markup_percent;
            document.getElementById('ruleRounding').value = formatRupiahDisplay(rule.rounding_value || 500);

            document.getElementById('modalEditRule').classList.add('is-open');
        }

        function submitEditRule(e) {
            e.preventDefault();
            const id = document.getElementById('ruleId').value;
            const form = document.getElementById('formEditRule');
            const data = new FormData(form);

            form.querySelectorAll('.input-rupiah').forEach(input => {
                const clean = cleanRupiahValue(input.value);
                data.set(input.name, clean);
            });

            fetch(`/admin/calendar-master/update-rule/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-HTTP-Method-Override': 'PUT',
                        'Accept': 'application/json'
                    },
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        if (typeof showToast === 'function') {
                            showToast('success', res.message);
                        } else {
                            alert(res.message);
                        }
                        closeCalModal('modalEditRule');
                        setTimeout(() => location.reload(), 600);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast('error', res.message);
                        } else {
                            alert('Gagal: ' + res.message);
                        }
                    }
                })
                .catch(err => {
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
        }

        // 4. Product Modal (Lengkap: Kategori, Ukuran, Orientasi, Digital, Offset, Plano)
        function openEditProductModal(prod) {
            document.getElementById('prodId').value = prod.id;
            document.getElementById('modalProdTitle').textContent = 'Ubah Parameter: ' + prod.product_name;
            document.getElementById('prodName').value = prod.product_name;
            document.getElementById('prodWidth').value = prod.final_width;
            document.getElementById('prodHeight').value = prod.final_height;
            document.getElementById('prodDigUp').value = prod.digital_up;
            document.getElementById('prodOffUp').value = prod.offset_up;
            document.getElementById('prodOffUpBw').value = prod.offset_up_bw || '';
            document.getElementById('prodDefLeaf').value = prod.default_leaf_count;

            // Set Select2 values
            $('#prodCategory').val(prod.calendar_type);
            $('#prodOrientation').val(prod.orientation);
            $('#prodDigAvail').val(prod.digital_available ? '1' : '0');
            $('#prodOffsetMachine').val(prod.offset_machine);
            $('#prodPlano').val(prod.recommended_plano);

            document.getElementById('modalEditProduct').classList.add('is-open');
            initCalModalSelect2('modalEditProduct');
        }

        function submitEditProduct(e) {
            e.preventDefault();
            const id = document.getElementById('prodId').value;
            const form = document.getElementById('formEditProduct');
            const data = new FormData(form);

            fetch(`/admin/calendar-master/update-product/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-HTTP-Method-Override': 'PUT',
                        'Accept': 'application/json'
                    },
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        if (typeof showToast === 'function') {
                            showToast('success', res.message);
                        } else {
                            alert(res.message);
                        }
                        closeCalModal('modalEditProduct');
                        setTimeout(() => location.reload(), 600);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast('error', res.message);
                        } else {
                            alert('Gagal: ' + res.message);
                        }
                    }
                })
                .catch(err => {
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
        }

        // =========================================================
        // DELETE CALENDAR MASTER DATA (ALL TABS)
        // =========================================================
        let calendarItemToDelete = {
            type: null,
            id: null,
            name: null
        };

        function confirmDeleteCalendar(type, id, name) {
            calendarItemToDelete.type = type;
            calendarItemToDelete.id = id;
            calendarItemToDelete.name = name;

            document.getElementById('deleteCalendarTargetName').textContent = name || 'data ini';
            document.getElementById('modalDeleteCalendar').classList.add('is-open');
        }

        function closeDeleteModal() {
            const el = document.getElementById('modalDeleteCalendar');
            if (el) el.classList.remove('is-open');
            calendarItemToDelete = {
                type: null,
                id: null,
                name: null
            };
        }

                function executeDeleteCalendar() {
            if (!calendarItemToDelete.type || !calendarItemToDelete.id) return;

            const targetType = calendarItemToDelete.type;
            const targetId = calendarItemToDelete.id;
            const targetName = calendarItemToDelete.name;

            const $btn = $('#btnConfirmDeleteCalendar');
            const originalHtml = $btn.html();
            $btn.prop('disabled', true).html(
                '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghapus...');

            let deleteUrl = '';
            if (targetType === 'product') {
                deleteUrl = `/admin/calendar-master/delete-product/${targetId}`;
            } else if (targetType === 'material') {
                deleteUrl = `/admin/calendar-master/delete-material/${targetId}`;
            } else if (targetType === 'component') {
                deleteUrl = `/admin/calendar-master/delete-component/${targetId}`;
            } else if (targetType === 'rule') {
                deleteUrl = `/admin/calendar-master/delete-rule/${targetId}`;
            }

            fetch(deleteUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-HTTP-Method-Override': 'DELETE',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(res => {
                    $btn.prop('disabled', false).html(originalHtml);
                    closeDeleteModal();

                    if (res.success) {
                        if (typeof showToast === 'function') {
                            showToast('success', res.message || 'Data berhasil dihapus!');
                        }

                        const $row = $('#row-' + targetType + '-' + targetId);

                        if (window.calTables && window.calTables[targetType]) {
                            window.calTables[targetType].row($row).remove().draw(false);
                        } else {
                            $row.remove();
                        }

                        if (targetType === 'product') {
                            decrementCounter('badgeCountProducts', 'statTotalProducts');
                        } else if (targetType === 'material') {
                            decrementCounter('badgeCountMaterials', 'statTotalMaterials');
                        } else if (targetType === 'component') {
                            decrementCounter('badgeCountComponents', 'statTotalComponents');
                        } else if (targetType === 'rule') {
                            decrementCounter('badgeCountRules', 'statTotalRules');
                        }

                        // Simpan tab aktif saat ini ke sessionStorage sebelum reload
                        const tabKeyMap = {
                            'product': 'products',
                            'material': 'materials',
                            'component': 'components',
                            'rule': 'rules'
                        };
                        try {
                            sessionStorage.setItem('activeCalTab', tabKeyMap[targetType] || 'products');
                        } catch (e) {}

                        // Auto reload untuk memastikan semua counter & tabel sinkron 100%
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    } else {
                        if (typeof showToast === 'function') {
                            showToast('error', res.message || 'Gagal menghapus data.');
                        } else {
                            alert('Gagal: ' + res.message);
                        }
                    }
                })
                .catch(err => {
                    $btn.prop('disabled', false).html(originalHtml);
                    closeDeleteModal();
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
        }

        function decrementCounter(badgeId, statId) {
            const $badge = $('#' + badgeId);
            const $stat = $('#' + statId);
            if ($badge.length) {
                const cur = Math.max(0, (parseInt($badge.text()) || 1) - 1);
                $badge.text(cur);
            }
            if ($stat.length) {
                const cur = Math.max(0, (parseInt($stat.text()) || 1) - 1);
                $stat.text(cur);
            }
        }
    </script>
@endsection

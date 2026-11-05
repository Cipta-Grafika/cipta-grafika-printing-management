@extends('admin_master')

@section('contents')
    <style>
        .merch-hero {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 24px 28px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
            position: relative;
            overflow: hidden;
        }

        .merch-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--primary);
        }

        .hero-title-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            flex-wrap: wrap;
        }

        .hero-left h1 {
            font-size: 22px;
            font-weight: 800;
            margin: 0 0 6px;
            color: var(--t-base);
            letter-spacing: -0.3px;
        }

        .hero-left p {
            margin: 0;
            font-size: 13.5px;
            color: var(--t-muted);
            max-width: 680px;
            line-height: 1.5;
        }

        .hero-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .hero-actions .btn {
            font-size: 13px;
            font-weight: 600;
            padding: 9px 18px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        /* METRIC CARDS */
        .cal-metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
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

        /* ACTION BUTTONS IN DATATABLE (PERSIS MASTER KALENDER) */
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
            border: none;
            background: transparent;
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
            border: none;
            background: transparent;
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

        /* TABS (PERSIS MASTER KALENDER) */
        .calendar-tabs {
            display: flex;
            gap: 4px;
            border-bottom: 2px solid var(--border);
            margin-bottom: 20px;
            background: transparent;
            overflow-x: auto;
        }

        .calendar-tab-btn {
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 12px 20px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--t-muted);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            margin-bottom: -2px;
            white-space: nowrap;
        }

        .calendar-tab-btn:hover {
            color: var(--t-base);
        }

        .calendar-tab-btn.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .cal-badge-counter {
            background: var(--bg-surface-2);
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11.5px;
            font-weight: 700;
        }

        .calendar-tab-btn.active .cal-badge-counter {
            background: rgba(194, 168, 87, 0.2);
            color: #c2a857;
        }

        .nota-filter-pill {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12.5px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s ease;
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--t-muted);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .nota-filter-pill:hover {
            color: var(--t-base);
            border-color: var(--primary);
        }

        .nota-filter-pill.active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
            font-weight: 600;
        }

        /* MODAL STYLING (PERSIS MASTER KALENDER) */
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
            animation: modalFadeIn 0.2s ease-out;
        }

        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.96) translateY(-8px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
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

        .modal-cal-foot {
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            background: var(--bg-muted);
        }

        /* FORM CONTROLS (PERSIS MASTER KALENDER) */
        .field {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .field-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--t-muted);
            letter-spacing: 0.2px;
        }

        .field-label .req {
            color: var(--danger, #ef4444);
        }

        .input,
        .select {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--bg-surface-2);
            color: var(--t-base);
            font-size: 13.5px;
            outline: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .input:focus,
        .select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(194, 168, 87, 0.15);
        }

        /* DATATABLES SEARCH & LENGTH CONTROLS */
        .dt-container .dt-search input,
        .dataTables_wrapper .dataTables_filter input {
            padding: 7px 12px !important;
            border-radius: 8px !important;
            border: 1px solid var(--border) !important;
            background: var(--bg-surface-2) !important;
            color: var(--t-base) !important;
            font-size: 13px !important;
            outline: none !important;
        }
    </style>

    <div class="shell">

        <div data-shell-sidebar></div>

        <div class="main">

            <div data-shell-topbar></div>

            <main class="content">

                {{-- =========================================================
                     HERO SECTION
                ========================================================= --}}
                <div class="merch-hero">
                    <div class="hero-title-row">
                        <div class="hero-left">
                            <span class="eyebrow" style="font-size: 11px; font-weight: 700; color: var(--primary); letter-spacing: 0.8px;">MASTER DATA · BUKU NOTA</span>
                            <h1>Master Data Buku Nota (NCR &amp; HVS)</h1>
                            <p>
                                Kelola daftar harga cetak buku nota kantor, faktur penjualan, surat jalan, kwitansi, dan form tanda terima (Bahan NCR Tembus Tanpa Karbon isi 50 sheet &amp; Kertas HVS isi 100 sheet).
                            </p>
                        </div>

                        <div class="hero-actions">
                            <a href="{{ route('user_product_details', ['type' => 'display', 'id' => ($displayNota->id ?? 48)]) }}"
                                target="_blank" class="btn btn--ghost" style="border: 1px solid var(--border);">
                                <i class="bi bi-box-arrow-up-right"></i>
                                <span>Lihat Buku Nota di Katalog Pelanggan</span>
                            </a>
                            <button type="button" class="btn btn--primary" onclick="openCreateNotaModal()">
                                <i class="bi bi-plus-lg"></i>
                                <span>Tambah Varian Nota</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- =========================================================
                     METRIC CARDS
                ========================================================= --}}
                <div class="cal-metrics-grid">
                    <div class="cal-metric-box">
                        <div class="cal-metric-icon" style="background: rgba(194, 168, 87, 0.12); color: #c2a857;">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--t-muted); font-weight: 600;">TOTAL VARIAN NOTA</div>
                            <div style="font-size: 22px; font-weight: 800; color: var(--t-base);">
                                {{ $totalNotas }} Varian</div>
                        </div>
                    </div>

                    <div class="cal-metric-box">
                        <div class="cal-metric-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">
                            <i class="bi bi-files"></i>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--t-muted); font-weight: 600;">FORM NCR (50 SHEET)</div>
                            <div style="font-size: 22px; font-weight: 800; color: var(--t-base);">
                                {{ $totalNcr }} Varian</div>
                        </div>
                    </div>

                    <div class="cal-metric-box">
                        <div class="cal-metric-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--t-muted); font-weight: 600;">FORM HVS (100 SHEET)</div>
                            <div style="font-size: 22px; font-weight: 800; color: var(--t-base);">
                                {{ $totalHvs }} Varian</div>
                        </div>
                    </div>

                    <div class="cal-metric-box">
                        <div class="cal-metric-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <div style="font-size: 11px; color: var(--t-muted); font-weight: 600;">STATUS KATALOG</div>
                            <div style="font-size: 22px; font-weight: 800; color: var(--t-base);">
                                Aktif</div>
                        </div>
                    </div>
                </div>

                {{-- =========================================================
                     TABS NAVIGATION (PERSIS MASTER KALENDER)
                ========================================================= --}}
                <div class="calendar-tabs">
                    <button type="button" class="calendar-tab-btn active" id="tabBtnSemua" onclick="switchNotaTab('semua')">
                        <i class="bi bi-journal-bookmark"></i>
                        <span>Semua Varian Nota</span>
                        <span class="cal-badge-counter">{{ $totalNotas }}</span>
                    </button>
                    <button type="button" class="calendar-tab-btn" id="tabBtnNcr" onclick="switchNotaTab('ncr')">
                        <i class="bi bi-files"></i>
                        <span>Form NCR (50 Sheet/Buku)</span>
                        <span class="cal-badge-counter">{{ $totalNcr }}</span>
                    </button>
                    <button type="button" class="calendar-tab-btn" id="tabBtnHvs" onclick="switchNotaTab('hvs')">
                        <i class="bi bi-file-earmark-text"></i>
                        <span>Form HVS (100 Sheet/Buku)</span>
                        <span class="cal-badge-counter">{{ $totalHvs }}</span>
                    </button>
                </div>

                {{-- =========================================================
                     DATA TABLE CONTAINER
                ========================================================= --}}
                <div class="card" style="background: var(--bg-card); border: 1px solid var(--border); border-radius: 12px; padding: 20px;">
                    
                    {{-- Action & Quick Info Bar --}}
                    <div style="display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; align-items: center; justify-content: space-between;">
                        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
                            <span style="font-size: 13px; font-weight: 600; color: var(--t-muted); margin-right: 4px;">
                                <i class="bi bi-funnel"></i> Filter Tinta:
                            </span>
                            <button type="button" class="nota-filter-pill active" data-filter="">Semua ({{ $totalNotas }})</button>
                            <button type="button" class="nota-filter-pill" data-filter="1 Tinta">1 Tinta (4 Varian)</button>
                            <button type="button" class="nota-filter-pill" data-filter="2 Tinta">2 Tinta (4 Varian)</button>
                            <button type="button" class="nota-filter-pill" data-filter="3 Tinta">3 Tinta (3 Varian)</button>
                        </div>

                        <button type="button" class="btn btn--primary" onclick="openCreateNotaModal()"
                            style="display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; font-size: 12.5px;">
                            <i class="bi bi-plus-lg"></i>
                            <span>Tambah Varian Nota</span>
                        </button>
                    </div>

                    {{-- Responsive DataTable Nota --}}
                    <div class="table-responsive">
                        <table id="merchNotaTable" class="data-table display responsive nowrap" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th style="width: 28px;"></th>
                                    <th style="width: 45px;" class="text-center">ID</th>
                                    <th>Nama Varian Buku Nota</th>
                                    <th class="text-center" style="width: 110px;">Bahan Kertas</th>
                                    <th class="text-center" style="width: 90px;">Rangkap (Ply)</th>
                                    <th class="text-center" style="width: 110px;">Warna Tinta</th>
                                    <th class="text-center" style="width: 120px;">Isi / Buku</th>
                                    <th style="text-align: right;">Harga Satuan (Per Rim)</th>
                                    <th class="text-center" style="width: 80px;">Status</th>
                                    <th class="text-center" style="width: 105px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allNotas as $nt)
                                    <tr id="row-nota-{{ $nt->id }}">
                                        <td class="dtr-control"></td>
                                        <td class="text-center">{{ $nt->id }}</td>
                                        <td>
                                            <strong>{{ $nt->name }}</strong>
                                            @if (!empty($nt->description))
                                                <div style="font-size: 11.5px; color: var(--t-muted); margin-top: 2px;">
                                                    {{ Str::limit($nt->description, 60) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if ($nt->paper_type === 'ncr')
                                                <span class="badge" style="background: rgba(59, 130, 246, 0.15); color: #3b82f6; font-weight: 700;">
                                                    NCR Tembus
                                                </span>
                                            @else
                                                <span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-weight: 700;">
                                                    HVS Putih
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge" style="background: var(--bg-surface-2); font-weight: 600;">
                                                {{ $nt->ply_label }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if ($nt->ink_count === 1)
                                                <span class="badge" style="background: rgba(100, 116, 139, 0.15); color: #64748b; font-weight: 600;">
                                                    1 Tinta
                                                </span>
                                            @elseif ($nt->ink_count === 2)
                                                <span class="badge" style="background: rgba(217, 119, 6, 0.15); color: #d97706; font-weight: 600;">
                                                    2 Tinta
                                                </span>
                                            @else
                                                <span class="badge" style="background: rgba(139, 92, 246, 0.15); color: #8b5cf6; font-weight: 700;">
                                                    3 Tinta
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{ $nt->sheets_per_book }} {{ $nt->paper_type === 'ncr' ? 'Set/Buku' : 'Lbr/Buku' }}
                                        </td>
                                        <td style="text-align: right; font-weight: 700; color: #c2a857;">
                                            Rp {{ number_format($nt->price, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge success">Aktif</span>
                                        </td>
                                        <td class="text-center">
                                            <div style="display: flex; gap: 6px; justify-content: center; align-items: center;">
                                                <button type="button" class="btn-detail-cal"
                                                    onclick='openDetailNotaModal(@json($nt))'
                                                    title="Lihat Detail Nota">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn-edit-cal"
                                                    onclick='openEditNotaModal(@json($nt))'
                                                    title="Ubah Data Nota">
                                                    <i class="bi bi-pen"></i>
                                                </button>
                                                <button type="button" class="btn-delete-cal"
                                                    onclick='confirmDeleteNota({{ $nt->id }}, "{{ addslashes($nt->name) }}")'
                                                    title="Hapus Nota">
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

            </main>

            <div data-shell-footer></div>

        </div>

    </div>

    {{-- =========================================================
         1. MODAL TAMBAH BUKU NOTA (FORM STYLE PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalCreateNota" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 540px;">
            <div class="modal-cal-head">
                <div>
                    <span class="eyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">TAMBAH DATA · MASTER BUKU NOTA</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);">Tambah Varian Buku Nota</h3>
                </div>
                <button type="button" onclick="closeCalModal('modalCreateNota')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>

            <form id="formCreateNota">
                @csrf
                <div class="modal-cal-body">
                    <div class="field" style="margin-bottom: 12px;">
                        <label class="field-label">Nama Varian Nota <span class="req">*</span></label>
                        <input type="text" name="name" class="input" placeholder="Misal: Nota NCR 2 Ply (1 Tinta)" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Tipe Kertas <span class="req">*</span></label>
                            <select name="paper_type" id="createPaperType" class="select select2-modal" required onchange="handlePaperTypeChange('create')">
                                <option value="ncr" selected>NCR (Carbonless Paper)</option>
                                <option value="hvs">HVS (Kertas Putih Single)</option>
                            </select>
                        </div>

                        <div class="field">
                            <label class="field-label">Rangkap / Ply <span class="req">*</span></label>
                            <select name="ply_count" id="createPlyCount" class="select select2-modal" required>
                                <option value="1">1 Ply (Khusus HVS)</option>
                                <option value="2" selected>2 Ply (Rangkap 2)</option>
                                <option value="3">3 Ply (Rangkap 3)</option>
                                <option value="4">4 Ply (Rangkap 4)</option>
                            </select>
                            <input type="hidden" name="ply_label" id="createPlyLabel" value="2 Ply">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Jumlah Warna Tinta <span class="req">*</span></label>
                            <select name="ink_count" id="createInkCount" class="select select2-modal" required>
                                <option value="1" selected>1 Tinta (1 Warna)</option>
                                <option value="2">2 Tinta (2 Warna)</option>
                                <option value="3">3 Tinta (3 Warna)</option>
                            </select>
                            <input type="hidden" name="ink_label" id="createInkLabel" value="1 Tinta">
                        </div>

                        <div class="field">
                            <label class="field-label">Isi Sheet per Buku <span class="req">*</span></label>
                            <input type="number" name="sheets_per_book" id="createSheetsPerBook" class="input" value="50" min="1" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Harga per Paket Rim (Rp) <span class="req">*</span></label>
                            <input type="text" name="price" class="input input-rupiah" placeholder="Rp 329.000" required
                                style="font-size: 15px; font-weight: 700; color: #c2a857;">
                        </div>

                        <div class="field">
                            <label class="field-label">Status Produk <span class="req">*</span></label>
                            <select name="status" class="select select2-modal">
                                <option value="active" selected>Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label class="field-label">Keterangan / Spesifikasi Nota</label>
                        <textarea name="description" class="input" rows="2" placeholder="Keterangan spesifikasi nota cetak"></textarea>
                    </div>
                </div>

                <div class="modal-cal-foot">
                    <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalCreateNota')">Batal</button>
                    <button type="submit" id="btnSaveCreateNota" class="btn btn--primary">
                        <i class="bi bi-check2"></i>
                        <span>Simpan Data</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- =========================================================
         2. MODAL EDIT BUKU NOTA (FORM STYLE PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalEditNota" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 540px;">
            <div class="modal-cal-head">
                <div>
                    <span class="eyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">UBAH DATA · MASTER BUKU NOTA</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);" id="editNotaHeading">
                        Edit Varian Buku Nota
                    </h3>
                </div>
                <button type="button" onclick="closeCalModal('modalEditNota')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>

            <form id="formEditNota">
                @csrf
                @method('PUT')
                <input type="hidden" id="editNotaId">

                <div class="modal-cal-body">
                    <div class="field" style="margin-bottom: 12px;">
                        <label class="field-label">Nama Varian Nota <span class="req">*</span></label>
                        <input type="text" id="editNotaName" name="name" class="input" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Tipe Kertas <span class="req">*</span></label>
                            <select id="editPaperType" name="paper_type" class="select select2-modal" required onchange="handlePaperTypeChange('edit')">
                                <option value="ncr">NCR (Carbonless Paper)</option>
                                <option value="hvs">HVS (Kertas Putih Single)</option>
                            </select>
                        </div>

                        <div class="field">
                            <label class="field-label">Rangkap / Ply <span class="req">*</span></label>
                            <select id="editPlyCount" name="ply_count" class="select select2-modal" required>
                                <option value="1">1 Ply (Khusus HVS)</option>
                                <option value="2">2 Ply (Rangkap 2)</option>
                                <option value="3">3 Ply (Rangkap 3)</option>
                                <option value="4">4 Ply (Rangkap 4)</option>
                            </select>
                            <input type="hidden" id="editPlyLabel" name="ply_label" value="2 Ply">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Jumlah Warna Tinta <span class="req">*</span></label>
                            <select id="editInkCount" name="ink_count" class="select select2-modal" required>
                                <option value="1">1 Tinta (1 Warna)</option>
                                <option value="2">2 Tinta (2 Warna)</option>
                                <option value="3">3 Tinta (3 Warna)</option>
                            </select>
                            <input type="hidden" id="editInkLabel" name="ink_label" value="1 Tinta">
                        </div>

                        <div class="field">
                            <label class="field-label">Isi Sheet per Buku <span class="req">*</span></label>
                            <input type="number" id="editSheetsPerBook" name="sheets_per_book" class="input" min="1" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                        <div class="field">
                            <label class="field-label">Harga per Paket Rim (Rp) <span class="req">*</span></label>
                            <input type="text" id="editNotaPrice" name="price" class="input input-rupiah" required
                                style="font-size: 15px; font-weight: 700; color: #c2a857;">
                        </div>

                        <div class="field">
                            <label class="field-label">Status Produk <span class="req">*</span></label>
                            <select id="editNotaStatus" name="status" class="select select2-modal">
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="field">
                        <label class="field-label">Keterangan / Spesifikasi Nota</label>
                        <textarea id="editNotaDesc" name="description" class="input" rows="2"></textarea>
                    </div>
                </div>

                <div class="modal-cal-foot">
                    <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalEditNota')">Batal</button>
                    <button type="submit" id="btnSaveEditNota" class="btn btn--primary">
                        <i class="bi bi-check2"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- =========================================================
         3. MODAL DETAIL BUKU NOTA (VIEW DETAIL PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalDetailNota" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 520px;">
            <div class="modal-cal-head">
                <div>
                    <span class="eyebrow" id="notaDetailEyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">DETAIL · SPESIFIKASI BUKU NOTA</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);" id="notaDetailTitle">
                        Detail Varian Buku Nota
                    </h3>
                </div>
                <button type="button" onclick="closeCalModal('modalDetailNota')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>

            <div class="modal-cal-body">
                <div id="notaDetailContainer" style="display: flex; flex-direction: column; gap: 8px;">
                    {{-- Diisi secara dinamis oleh JavaScript --}}
                </div>
            </div>

            <div class="modal-cal-foot">
                <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalDetailNota')">Tutup</button>
                <button type="button" class="btn btn--primary" id="btnEditNotaFromDetail"
                    style="display: inline-flex; align-items: center; gap: 6px;">
                    <i class="bi bi-pen"></i>
                    <span>Ubah Varian Ini</span>
                </button>
            </div>
        </div>
    </div>

    {{-- =========================================================
         4. MODAL KONFIRMASI HAPUS BUKU NOTA (PERSIS KALENDER)
    ========================================================= --}}
    <div id="modalDeleteNota" class="modal-cal">
        <div class="modal-cal-content" style="max-width: 440px;">
            <div class="modal-cal-head" style="border-bottom: 1px solid var(--border);">
                <div>
                    <span class="eyebrow" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">HAPUS DATA · MASTER BUKU NOTA</span>
                    <h3 style="margin: 4px 0 0; font-size: 16px; font-weight: 700; color: var(--t-base);">Konfirmasi Hapus</h3>
                </div>
                <button type="button" onclick="closeCalModal('modalDeleteNota')"
                    style="background: none; border: none; font-size: 20px; cursor: pointer; color: var(--t-muted); line-height: 1;">&times;</button>
            </div>
            <div class="modal-cal-body" style="padding: 22px;">
                <div style="display: flex; gap: 16px; align-items: flex-start;">
                    <div style="width: 48px; height: 48px; min-width: 48px; border-radius: 12px; background: rgba(239, 68, 68, 0.12); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 22px;">
                        <i class="bi bi-trash3-fill"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0 0 6px; font-size: 15px; font-weight: 700; color: var(--t-base);">Apakah Anda Yakin?</h4>
                        <p style="margin: 0; font-size: 13px; color: var(--t-muted); line-height: 1.5;">
                            Anda akan menghapus varian nota <strong id="deleteNotaTargetName" style="color: var(--t-base);"></strong>. Data yang sudah dihapus tidak dapat dipulihkan kembali.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-cal-foot" style="padding: 14px 20px; border-top: 1px solid var(--border); display: flex; justify-content: flex-end; gap: 10px; background: var(--bg-muted);">
                <button type="button" class="btn btn--ghost" onclick="closeCalModal('modalDeleteNota')">Batal</button>
                <button type="button" class="btn btn--danger" id="btnConfirmDeleteNota" onclick="executeDeleteNota()"
                    style="padding: 8px 16px; border-radius: 6px; border: none; background: #dc2626; color: #ffffff; cursor: pointer; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="bi bi-trash3-fill"></i>
                    <span>Ya, Hapus</span>
                </button>
            </div>
        </div>
    </div>

    {{-- =========================================================
         DATATABLES & JAVASCRIPT CRUD (PERSIS SEPERTI MASTER KALENDER)
    ========================================================= --}}
    <script>
        let currentActiveNota = null;
        let notaItemToDelete = { id: null, name: '' };

        // Helper Format Rupiah
        function formatRupiahDisplay(val) {
            if (val === null || val === undefined || val === '') return '';
            let num = Number(val);
            if (isNaN(num)) return val;
            return 'Rp ' + num.toLocaleString('id-ID');
        }

        // Helper Pembersih Nilai Rupiah dari Input Masking
        function cleanRupiahValue(val) {
            if (val === null || val === undefined || val === '') return 0;
            let s = String(val).replace(/Rp\s*/g, '').trim();
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

        function closeCalModal(id) {
            const el = document.getElementById(id);
            if (el) el.classList.remove('is-open');
        }

        // Handle auto-adjust sheet & ply when paper type changes
        function handlePaperTypeChange(mode) {
            const isCreate = mode === 'create';
            const paperSelect = document.getElementById(isCreate ? 'createPaperType' : 'editPaperType');
            const sheetsInput = document.getElementById(isCreate ? 'createSheetsPerBook' : 'editSheetsPerBook');
            const plySelect = $(isCreate ? '#createPlyCount' : '#editPlyCount');

            if (paperSelect.value === 'hvs') {
                sheetsInput.value = 100;
                plySelect.val(1).trigger('change');
            } else {
                sheetsInput.value = 50;
                if (plySelect.val() == 1) {
                    plySelect.val(2).trigger('change');
                }
            }
        }

        // Switch Tab (Semua, NCR, HVS)
        function switchNotaTab(tabName) {
            document.querySelectorAll('.calendar-tab-btn').forEach(btn => btn.classList.remove('active'));

            if (tabName === 'semua') {
                document.getElementById('tabBtnSemua').classList.add('active');
                if (window.merchTableNota) window.merchTableNota.column(3).search('').draw();
            } else if (tabName === 'ncr') {
                document.getElementById('tabBtnNcr').classList.add('active');
                if (window.merchTableNota) window.merchTableNota.column(3).search('NCR').draw();
            } else if (tabName === 'hvs') {
                document.getElementById('tabBtnHvs').classList.add('active');
                if (window.merchTableNota) window.merchTableNota.column(3).search('HVS').draw();
            }
        }

        // Handlers Modal
        function openCreateNotaModal() {
            document.getElementById('formCreateNota').reset();
            $('#createPaperType').val('ncr').trigger('change');
            $('#createPlyCount').val(2).trigger('change');
            $('#createInkCount').val(1).trigger('change');
            $('#createSheetsPerBook').val(50);
            document.getElementById('modalCreateNota').classList.add('is-open');
            initCalModalSelect2('modalCreateNota');
        }

        function openDetailNotaModal(nt) {
            currentActiveNota = nt;
            const container = document.getElementById('notaDetailContainer');
            container.innerHTML = '';

            document.getElementById('notaDetailEyebrow').textContent = 'DETAIL · SPESIFIKASI BUKU NOTA';
            document.getElementById('notaDetailTitle').textContent = nt.name;

            container.innerHTML += makeDetailRow('Nama Varian', nt.name);
            container.innerHTML += makeDetailRow('Tipe Kertas', nt.paper_type === 'ncr' ? '📑 NCR Tembus Otomatis' : '📄 Kertas HVS Putih');
            container.innerHTML += makeDetailRow('Jumlah Rangkap', `${nt.ply_count} Ply (${nt.ply_label})`);
            container.innerHTML += makeDetailRow('Jumlah Warna Tinta', `${nt.ink_count} Warna (${nt.ink_label})`);
            container.innerHTML += makeDetailRow('Isi per Buku', `${nt.sheets_per_book} ${nt.paper_type === 'ncr' ? 'Set/Buku' : 'Lembar/Buku'}`);
            container.innerHTML += makeDetailRow('Harga Satuan', `<span style="color: #c2a857; font-weight: 700; font-size: 14px;">${formatRupiahDisplay(nt.price)}</span> / Paket Rim`);
            container.innerHTML += makeDetailRow('Status Katalog', nt.status === 'active' ? '<span class="badge success">Aktif</span>' : '<span class="badge">Nonaktif</span>');

            if (nt.description) {
                container.innerHTML += makeDetailSectionTitle('Deskripsi & Spesifikasi');
                container.innerHTML += `
                    <div style="padding: 10px 14px; background: var(--bg-surface-2); border: 1px solid var(--border); border-radius: 8px; font-size: 12.5px; color: var(--t-base); line-height: 1.5;">
                        ${nt.description}
                    </div>
                `;
            }

            container.innerHTML += makeDetailInfoBox(
                '💡',
                'Perhitungan Isi Buku per Rim Plano',
                '1 Paket Rim standar menghasilkan: 40 Buku ukuran 1/4 Folio (populer), atau 20 Buku ukuran 1/2 Folio, atau 10 Buku ukuran 1 Folio penuh.'
            );

            document.getElementById('btnEditNotaFromDetail').onclick = function() {
                closeCalModal('modalDetailNota');
                openEditNotaModal(nt);
            };

            document.getElementById('modalDetailNota').classList.add('is-open');
        }

        function openEditNotaModal(nt) {
            currentActiveNota = nt;
            $('#editNotaId').val(nt.id);
            $('#editNotaName').val(nt.name);
            $('#editPaperType').val(nt.paper_type).trigger('change');
            $('#editPlyCount').val(nt.ply_count).trigger('change');
            $('#editPlyLabel').val(nt.ply_label);
            $('#editInkCount').val(nt.ink_count).trigger('change');
            $('#editInkLabel').val(nt.ink_label);
            $('#editSheetsPerBook').val(nt.sheets_per_book);
            $('#editNotaPrice').val(formatRupiahDisplay(nt.price));
            $('#editNotaStatus').val(nt.status || 'active').trigger('change');
            $('#editNotaDesc').val(nt.description || '');
            $('#editNotaHeading').text(`Edit Varian: ${nt.name}`);

            document.getElementById('modalEditNota').classList.add('is-open');
            initCalModalSelect2('modalEditNota');
        }

        function confirmDeleteNota(id, name) {
            notaItemToDelete.id = id;
            notaItemToDelete.name = name;

            document.getElementById('deleteNotaTargetName').textContent = name || ('Nota #' + id);
            document.getElementById('modalDeleteNota').classList.add('is-open');
        }

        function executeDeleteNota() {
            if (!notaItemToDelete.id) return;

            const id = notaItemToDelete.id;
            const $btn = $('#btnConfirmDeleteNota');
            const origHtml = $btn.html();
            $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Menghapus...');

            const token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();

            fetch(`/admin/nota-master/delete-nota/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                $btn.prop('disabled', false).html(origHtml);
                closeCalModal('modalDeleteNota');

                if (data.success) {
                    if (typeof showToast === 'function') {
                        showToast('success', data.message || 'Varian Nota berhasil dihapus!');
                    } else {
                        alert(data.message || 'Varian Nota berhasil dihapus!');
                    }
                    setTimeout(() => location.reload(), 400);
                } else {
                    alert('Gagal: ' + (data.message || 'Terjadi kesalahan saat menghapus data.'));
                }
            })
            .catch(err => {
                $btn.prop('disabled', false).html(origHtml);
                closeCalModal('modalDeleteNota');
                alert('Terjadi kesalahan jaringan: ' + err.message);
            });
        }

        // Document Ready Initialization
        $(document).ready(function() {
            function initDataTable(selector, placeholder) {
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
                        columnDefs: [
                            {
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
                            searchPlaceholder: placeholder || 'Cari varian nota...'
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

            window.merchTableNota = initDataTable('#merchNotaTable', 'Cari bahan, ply, tinta, harga...');

            // Filter Pills Tinta
            $('.nota-filter-pill').on('click', function() {
                $('.nota-filter-pill').removeClass('active');
                $(this).addClass('active');

                const filterVal = $(this).data('filter');
                if (window.merchTableNota) {
                    window.merchTableNota.column(5).search(filterVal).draw();
                }
            });

            // Live Format Rupiah Input
            $(document).on('input', '.input-rupiah', function() {
                let val = $(this).val();
                if (!val) return;
                let clean = val.replace(/[^0-9]/g, '');
                let formatted = clean ? clean.replace(/\B(?=(\d{3})+(?!\d))/g, '.') : '';
                $(this).val(formatted ? 'Rp ' + formatted : '');
            });

            // Auto-update ply_label and ink_label on change
            $('#createPlyCount').on('change', function() {
                $('#createPlyLabel').val($(this).val() + ' Ply');
            });
            $('#createInkCount').on('change', function() {
                $('#createInkLabel').val($(this).val() + ' Tinta');
            });
            $('#editPlyCount').on('change', function() {
                $('#editPlyLabel').val($(this).val() + ' Ply');
            });
            $('#editInkCount').on('change', function() {
                $('#editInkLabel').val($(this).val() + ' Tinta');
            });

            // Modal Overlay Click to Close
            document.querySelectorAll('.modal-cal').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        this.classList.remove('is-open');
                    }
                });
            });

            // Close on ESC
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('.modal-cal.is-open').forEach(modal => {
                        modal.classList.remove('is-open');
                    });
                }
            });

            // Form Submit Create Nota
            $('#formCreateNota').on('submit', function(e) {
                e.preventDefault();
                const $btn = $('#btnSaveCreateNota');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Menyimpan...');

                const token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
                const rawPrice = $('input[name="price"]', this).val();
                const formData = {
                    name: $('input[name="name"]', this).val(),
                    paper_type: $('select[name="paper_type"]', this).val(),
                    ply_count: $('select[name="ply_count"]', this).val(),
                    ply_label: $('#createPlyLabel').val() || ($('select[name="ply_count"]', this).val() + ' Ply'),
                    ink_count: $('select[name="ink_count"]', this).val(),
                    ink_label: $('#createInkLabel').val() || ($('select[name="ink_count"]', this).val() + ' Tinta'),
                    sheets_per_book: $('input[name="sheets_per_book"]', this).val(),
                    price: cleanRupiahValue(rawPrice),
                    status: $('select[name="status"]', this).val() || 'active',
                    description: $('textarea[name="description"]', this).val()
                };

                fetch('{{ route("admin_store_nota") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(res => res.json())
                .then(data => {
                    $btn.prop('disabled', false).html(origHtml);
                    if (data.success) {
                        closeCalModal('modalCreateNota');
                        if (typeof showToast === 'function') {
                            showToast('success', data.message || 'Varian Nota berhasil ditambahkan!');
                        } else {
                            alert(data.message || 'Varian Nota berhasil ditambahkan!');
                        }
                        setTimeout(() => location.reload(), 400);
                    } else {
                        alert('Gagal: ' + (data.message || 'Terjadi kesalahan.'));
                    }
                })
                .catch(err => {
                    $btn.prop('disabled', false).html(origHtml);
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
            });

            // Form Submit Edit Nota
            $('#formEditNota').on('submit', function(e) {
                e.preventDefault();
                const id = $('#editNotaId').val();
                const $btn = $('#btnSaveEditNota');
                const origHtml = $btn.html();
                $btn.prop('disabled', true).html('<i class="bi bi-arrow-repeat spin"></i> Menyimpan...');

                const token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
                const rawPrice = $('#editNotaPrice').val();
                const formData = {
                    name: $('#editNotaName').val(),
                    paper_type: $('#editPaperType').val(),
                    ply_count: $('#editPlyCount').val(),
                    ply_label: $('#editPlyLabel').val() || ($('#editPlyCount').val() + ' Ply'),
                    ink_count: $('#editInkCount').val(),
                    ink_label: $('#editInkLabel').val() || ($('#editInkCount').val() + ' Tinta'),
                    sheets_per_book: $('#editSheetsPerBook').val(),
                    price: cleanRupiahValue(rawPrice),
                    status: $('#editNotaStatus').val() || 'active',
                    description: $('#editNotaDesc').val()
                };

                fetch(`/admin/nota-master/update-nota/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                })
                .then(res => res.json())
                .then(data => {
                    $btn.prop('disabled', false).html(origHtml);
                    if (data.success) {
                        closeCalModal('modalEditNota');
                        if (typeof showToast === 'function') {
                            showToast('success', data.message || 'Varian Nota berhasil diperbarui!');
                        } else {
                            alert(data.message || 'Varian Nota berhasil diperbarui!');
                        }
                        setTimeout(() => location.reload(), 400);
                    } else {
                        alert('Gagal: ' + (data.message || 'Terjadi kesalahan.'));
                    }
                })
                .catch(err => {
                    $btn.prop('disabled', false).html(origHtml);
                    alert('Terjadi kesalahan jaringan: ' + err.message);
                });
            });
        });
    </script>
@endsection

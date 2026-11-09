@extends('admin_master')
@section('contents')
    <link rel="stylesheet" href="{{ asset('css/production-cost-finishing-index.css') }}">
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero cat-hero-compact">
                    <div class="hero-text"><span class="eyebrow">BIAYA · LAMINASI</span>
                        {{-- <h1 class="hero-title">Daftar Finishing & Jasa</h1> --}}
                        <p class="hero-sub">Kelola konfigurasi biaya laminasi yang digunakan sebagai dasar perhitungan
                            biaya serta estimasi harga produk.</p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card cat-card-container">
                        <div class="cat-card-header">
                            <div class="cat-card-header-main">
                                <div class="cat-card-title-wrap">
                                    <div class="cat-card-icon"><i class="bi bi-layers-fill"></i></div>
                                    <div>
                                        <div class="cat-title-row">
                                            <h3 class="cat-card-title">Daftar Biaya Laminasi</h3>
                                            <span class="cat-badge-total">
                                                <span class="cat-pulse-dot"></span>
                                                <span id="productionCostLaminationTotalCount">Memuat data...</span>
                                            </span>
                                        </div>
                                        <p class="cat-card-desc">Konfigurasi biaya laminasi berdasarkan mesin, lokasi, dan kategori.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="cat-card-actions">
                                <a id="btnExportProductionCostLamination"
                                    href="{{ route('admin_export_pc_finishing_laminations') }}" class="cat-btn-head"
                                    title="Export Excel">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <path d="M7 10l5 5 5-5" /><path d="M12 15V3" />
                                    </svg><span>Export</span>
                                </a>
                                <button type="button" class="cat-btn-head" id="btnDownloadTemplateLamination"
                                    title="Download Template Excel">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <path d="M7 10l5 5 5-5" /><path d="M12 15V3" />
                                    </svg><span>Template</span>
                                </button>
                                <button type="button" class="cat-btn-head" id="btnImportFinishingLamination" title="Import Excel">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M12 16V3" /><path d="m7 8 5-5 5 5" />
                                        <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                                    </svg><span>Import</span>
                                </button>
                                <a href="{{ route('admin_create_pc_finishing_lamination') }}" class="cat-btn-head-primary">
                                    <i class="bi bi-plus-lg"></i><span>Tambah Ongkos</span>
                                </a>
                            </div>
                        </div>

                        <div class="cat-bulk-toolbar" id="productionCostLaminationBulkToolbar" style="display:none;">
                            <div class="cat-bulk-info">
                                <span class="cat-bulk-badge" id="productionCostLaminationBulkCount">0 terpilih</span>
                                <span class="cat-bulk-text">Biaya laminasi terpilih siap untuk tindakan massal</span>
                            </div>
                            <div class="cat-bulk-actions">
                                <button type="button" class="btn btn--ghost btn--sm" id="cancelProductionCostLaminationSelection">
                                    <i class="bi bi-x-circle me-1"></i>Batal Pilihan
                                </button>
                                <button type="button" class="btn btn--danger btn--sm" id="bulkDeleteProductionCostLaminations">
                                    <i class="bi bi-trash3-fill me-1"></i>Hapus Terpilih
                                </button>
                            </div>
                        </div>

                        <div class="cat-filter-card">
                            <div class="cat-filter-group-left">
                                <div class="cat-search-box">
                                    <i class="bi bi-search cat-search-icon"></i>
                                    <input type="text" id="productionCostLaminationSearch" class="cat-filter-input"
                                        placeholder="Cari mesin, lokasi, kategori, atau vendor...">
                                    <button type="button" id="clearProductionCostLaminationSearch"
                                        class="cat-clear-btn" title="Hapus pencarian" style="display:none;">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                                <div class="cat-select-box">
                                    <i class="bi bi-gear-fill cat-select-icon"></i>
                                    <select id="productionCostLaminationEngineFilter" class="select2" style="width:100%;"
                                        aria-label="Filter mesin ongkos produksi laminasi">
                                        <option value="large_format" selected>Large Format</option>
                                        <option value="digital_print_a3">Digital Print A3+</option>
                                    </select>
                                </div>
                                <div class="cat-filter-subgroup">
                                    <div class="cat-select-box">
                                        <i class="bi bi-geo-alt-fill cat-select-icon"></i>
                                        <select id="productionCostLaminationLocationFilter" class="select2"
                                            style="width:100%;" aria-label="Filter lokasi">
                                            <option value="all">Semua Lokasi</option>
                                            @if (isset($filterLocations))
                                                @foreach ($filterLocations as $location)
                                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <button type="button" id="resetProductionCostLaminationFilters" class="cat-btn-reset"
                                        title="Reset semua filter">
                                        <i class="bi bi-arrow-counterclockwise"></i><span>Reset</span>
                                    </button>
                                </div>
                            </div>
                            <div class="cat-filter-group-right">
                                <div class="cat-length-box">
                                    <span class="cat-length-label">Baris:</span>
                                    <select id="productionCostLaminationPageLength" class="select2" style="width:76px;">
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <p id="digitalLaminationListNotice" class="field-help" hidden>
                            Template dan import Digital Print A3+ belum tersedia.
                        </p>
                        <div class="cat-table-wrap">
                        <table id="productionCostLaminationTable" class="cat-modern-table" style="width:100%"
                            data-url="{{ route('admin_data_pc_finishing_lamination') }}"
                            data-bulk-url="{{ route('admin_bulk_destroy_pc_finishing_laminations') }}">
                            <thead>
                                <tr>
                                    <th class="cat-th-check text-center">
                                        <label class="cat-custom-checkbox" title="Pilih Semua di Halaman Ini">
                                            <input type="checkbox" id="checkAllProductionCostLaminations"
                                                aria-label="Pilih semua pada halaman ini">
                                            <span class="cat-checkbox-mark">
                                                <svg class="check-icon" viewBox="0 0 14 14" fill="none">
                                                    <path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#ffffff"
                                                        stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <svg class="minus-icon" viewBox="0 0 14 14" fill="none">
                                                    <path d="M3 7H11" stroke="#ffffff" stroke-width="2.2"
                                                        stroke-linecap="round" />
                                                </svg>
                                            </span>
                                        </label>
                                    </th>
                                    <th class="cat-th-no text-center" style="width:60px;">No</th>
                                    <th class="cat-th-name" style="min-width:220px;">Mesin</th>
                                    <th class="cat-th-creator" style="min-width:190px;">Lokasi / Vendor</th>
                                    <th class="cat-th-date text-center" style="min-width:160px;">Kategori</th>
                                    <th class="cat-th-date text-center" style="min-width:140px;">Laminasi</th>
                                    <th class="cat-th-date text-center" style="min-width:130px;">Ukuran</th>
                                    <th class="cat-th-actions text-center" style="width:120px;">Aksi</th>
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

    <div class="modal-overlay" id="modalBulkDeleteProductionCostLaminations">
        <div class="modal-dialog modal-delete">
            <div class="modal-header">
                <div>
                    <span class="eyebrow">KONFIGURASI · BIAYA LAMINASI</span><br>
                    <span class="eyebrow">Hapus Pilihan</span>
                </div>
                <button type="button" class="modal-close" id="closeBulkDeleteProductionCostLaminations" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="delete-confirmation">
                    <div class="delete-icon"><i class="bi bi-trash3-fill"></i></div>
                    <div class="delete-content">
                        <h3>Hapus biaya laminasi terpilih?</h3>
                        <p id="bulkDeleteProductionCostLaminationDescription"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--ghost" id="cancelBulkDeleteProductionCostLaminations">Batal</button>
                <button type="button" class="btn btn--danger" id="confirmBulkDeleteProductionCostLaminations">
                    <i class="bi bi-trash3-fill"></i> Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL DELETE PRODUCTION COST LAMINASI -->
    <div class="modal-overlay" id="modalDeleteProductionCostLamination">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        HARGA & TARIF · PRODUCTION COST LAMINASI
                    </span>
                    <br>

                    <span class="eyebrow">
                        Hapus Production Cost Laminasi
                    </span>

                </div>

                <button type="button" class="modal-close" id="btnTutupDeleteProductionCostLamination" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
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
                            Hapus production cost laminasi?
                        </h3>

                        <p id="largeFormatLaminationDeleteMessage">
                            Apakah kamu yakin ingin menghapus seluruh
                            konfigurasi production cost laminasi untuk mesin ini?
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>
                        <p id="digitalLaminationDeleteMessage" hidden>
                            Hapus konfigurasi ongkos laminasi Digital Print A3+ untuk
                            <strong id="digitalLaminationDeleteScope"></strong> beserta seluruh kategori, detail laminasi,
                            dan harga tier pada baris ini? Master dan ongkos material tidak ikut dihapus.
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteProductionCostLamination">
                    Batal
                </button>

                <button type="button" class="btn btn--danger" id="btnConfirmDeleteProductionCostLamination">
                    <i class="bi bi-trash3-fill"></i>
                    Hapus
                </button>

            </div>

        </div>

    </div>

    <!-- Modal Import Production Cost Laminasi -->
    <div class="modal-overlay" id="modalImportFinishingLamination">
        <div class="modal modal-import">

            <div class="modal-header">
                <div>
                    <span class="eyebrow">KONFIGURASI</span>
                    <br>
                    <span class="eyebrow">BIAYA LAMINASI</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseImportFinishingLamination" aria-label="Tutup">
                    <svg viewBox="0 0 24 24">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            <div class="modal-body">

                <div class="import-description">
                    <p>
                        Pilih file Excel yang berisi data Production Cost
                        Laminasi untuk diimport ke dalam sistem.
                    </p>
                </div>

                <label for="fileImportFinishingLamination" class="file-upload-box" id="fileUploadBoxFinishingLamination">

                    <input type="file" id="fileImportFinishingLamination" name="file" accept=".xlsx,.xls" hidden>

                    <div class="file-upload-content">

                        <div class="file-upload-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg>
                        </div>

                        <div class="file-upload-text">
                            <strong>Pilih file Excel</strong>
                            <span>
                                Klik untuk memilih file dari komputer
                            </span>
                        </div>

                    </div>

                    <div class="file-selected-name" id="fileSelectedNameFinishingLamination">
                        Belum ada file dipilih
                    </div>

                </label>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCancelImportFinishingLamination">
                    Batal
                </button>

                <button type="button" class="btn btn--primary" id="btnSaveImportFinishingLamination">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8-5 5" />
                        <path d="m17 8-5-5" />
                    </svg>
                    Import Data
                </button>

            </div>

        </div>
    </div>

    {{-- Modal download template excel --}}
    <div class="modal-overlay" id="modalDownloadTemplateLamination">
        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">
                <div>
                    <span class="eyebrow">KONFIGURASI</span>
                    <br>
                    <span class="eyebrow">BIAYA FINISHING LAMINASI</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseDownloadTemplateLamination" aria-label="Tutup">
                    <svg viewBox="0 0 24 24">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <div class="import-description">
                    <p>
                        Download template Excel yang digunakan untuk
                        mengisi data biaya produksi finishing laminasi.
                    </p>
                </div>

                <div class="template-download-options">

                    <!-- Template Laminasi -->
                    <a href="{{ asset('templates/template_ongkos_produksi_laminasi_outsourcing.xlsx') }}"
                        class="template-download-card file-upload-box">

                        <div class="template-download-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 3v12" />
                                <path d="m7 10 5 5 5-5" />
                                <path d="M5 21h14" />
                                <path d="M5 17v4" />
                                <path d="M19 17v4" />
                            </svg>
                        </div>

                        <div class="template-download-content">
                            <strong>Template Outsourcing</strong>

                            <span>
                                Template Excel untuk laminasi dengan proses outsourcing dan harga vendor.
                            </span>
                        </div>

                        <div class="template-download-arrow">
                            <svg viewBox="0 0 24 24">
                                <path d="M5 12h14" />
                                <path d="m13 6 6 6-6 6" />
                            </svg>
                        </div>

                    </a>

                    <a href="{{ asset('templates/template_ongkos_produksi_laminasi.xlsx') }}"
                        class="template-download-card file-upload-box">

                        <div class="template-download-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 3v12" />
                                <path d="m7 10 5 5 5-5" />
                                <path d="M5 21h14" />
                                <path d="M5 17v4" />
                                <path d="M19 17v4" />
                            </svg>
                        </div>

                        <div class="template-download-content">
                            <strong>Template Non-Outsourcing</strong>

                            <span>
                                Template untuk laminasi dengan perhitungan biaya produksi internal.
                            </span>
                        </div>

                        <div class="template-download-arrow">
                            <svg viewBox="0 0 24 24">
                                <path d="M5 12h14" />
                                <path d="m13 6 6 6-6 6" />
                            </svg>
                        </div>

                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn--ghost" id="btnCancelDownloadTemplateLamination">
                    Batal
                </button>
            </div>

        </div>
    </div>
@endsection


@section('scripts')
    <script>
        let productionCostLaminationTable;
        const selectedLargeFormatLaminations = new Map();
        const selectedDigitalLaminations = new Set();

        document.addEventListener('DOMContentLoaded', function() {
            const engineFilter = document.getElementById('productionCostLaminationEngineFilter');
            const locationFilter = document.getElementById('productionCostLaminationLocationFilter');
            const tableElement = document.getElementById('productionCostLaminationTable');
            const tableWrap = tableElement.closest('.cat-table-wrap');
            const searchInput = document.getElementById('productionCostLaminationSearch');
            const clearSearchButton = document.getElementById('clearProductionCostLaminationSearch');
            const pageLength = document.getElementById('productionCostLaminationPageLength');
            const exportButton = document.getElementById('btnExportProductionCostLamination');
            const exportUrl = exportButton.href;
            const escapeHtml = DataTable.render.text().display;
            const bulkToolbar = document.getElementById('productionCostLaminationBulkToolbar');
            const bulkCount = document.getElementById('productionCostLaminationBulkCount');
            const checkAll = document.getElementById('checkAllProductionCostLaminations');
            const checkAllLabel = checkAll.closest('.cat-custom-checkbox');
            const maxSelected = 100;

            function selectedLaminationCount() {
                return selectedLargeFormatLaminations.size + selectedDigitalLaminations.size;
            }

            function isDigitalFilter() {
                return engineFilter.value === 'digital_print_a3';
            }

            $('#productionCostLaminationEngineFilter, #productionCostLaminationLocationFilter')
                .select2({ width: '100%', minimumResultsForSearch: Infinity });
            $('#productionCostLaminationPageLength').select2({ width: '76px', minimumResultsForSearch: -1 });

            function updateExportLaminationLink() {
                const url = new URL(exportUrl);
                url.searchParams.set('pricing_engine', engineFilter.value);
                exportButton.href = url.toString();
            }

            function updateSelectionUI() {
                const selectedCount = selectedLaminationCount();
                bulkToolbar.style.display = selectedCount ? 'flex' : 'none';
                bulkCount.textContent = `${selectedCount} terpilih`;

                const checkboxes = document.querySelectorAll('.production-cost-lamination-row-check');
                let checkedCount = 0;
                checkboxes.forEach((checkbox) => {
                    const selected = checkbox.dataset.kind === 'digital_print_a3'
                        ? selectedDigitalLaminations.has(Number(checkbox.value))
                        : selectedLargeFormatLaminations.has(Number(checkbox.value));
                    checkbox.checked = selected;
                    checkbox.closest('tr')?.classList.toggle('is-selected', selected);
                    if (selected) checkedCount++;
                });

                checkAll.checked = checkboxes.length > 0 && checkedCount === checkboxes.length;
                checkAll.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length;
                checkAllLabel.classList.toggle('is-indeterminate', checkAll.indeterminate);
            }

            function centerProcessingIndicator() {
                const indicator = document.getElementById(`${tableElement.id}_processing`);
                if (indicator && indicator.parentElement !== tableWrap) {
                    indicator.classList.add('cat-processing-centered');
                    tableWrap.appendChild(indicator);
                }
            }

            $('#productionCostLaminationTable').on('processing.dt', centerProcessingIndicator);
            updateExportLaminationLink();

            productionCostLaminationTable = new DataTable(
                '#productionCostLaminationTable', {
                    processing: true,
                    serverSide: true,
                    ordering: true,
                    order: [[2, 'asc']],

                    ajax: {
                        url: document.getElementById('productionCostLaminationTable').dataset.url,
                        type: 'GET',
                        data: function(data) {
                            data.pricing_engine = engineFilter.value;
                            data.location_id = locationFilter.value === 'all' ? '' : locationFilter.value;
                        },
                        dataSrc: function(response) {
                            const total = Number(response.recordsTotal ?? 0);
                            document.getElementById('productionCostLaminationTotalCount').textContent =
                                `${total.toLocaleString('id-ID')} Total Biaya Laminasi`;
                            return response.data;
                        },
                    },

                    columns: [{
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'cat-td-check text-center',
                            render: function(data, type, row) {
                                const isDigital = row.pricing_engine === 'digital_print_a3';
                                const id = isDigital ? row.digital_lamination_production_cost_id : row.engine_id;
                                const kind = isDigital ? 'digital_print_a3' : 'large_format';
                                return `<label class="cat-custom-checkbox">
                                    <input type="checkbox" class="production-cost-lamination-row-check"
                                        value="${Number(id)}" data-kind="${kind}"
                                        ${isDigital ? '' : `data-production-cost-ids="${(row.production_cost_ids || []).map(Number).join(',')}"`}>
                                    <span class="cat-checkbox-mark">
                                        <svg class="check-icon" viewBox="0 0 14 14" fill="none">
                                            <path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#fff" stroke-width="2.2"
                                                stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </label>`;
                            },
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-center cat-td-no',
                            render: function(data, type, row, meta) {
                                const pageInfo = productionCostLaminationTable.page.info();
                                return `<span class="cat-num-pill">${pageInfo.start + meta.row + 1}</span>`;
                            },
                        },
                        {
                            data: 'engine_name',
                            defaultContent: '-',
                            className: 'cat-td-name',
                            render: function(data, type, row) {
                                if (type !== 'display') {
                                    return data || '';
                                }
                                const engineLabel = row.pricing_engine === 'digital_print_a3' ? 'Digital Print' : 'Large Format';
                                return `
                                    <div class="cat-cell-name-box">
                                        <div class="cat-icon-symbol">
                                            <i class="bi bi-layers-fill"></i>
                                        </div>
                                        <div class="cat-name-details">
                                            <span class="cat-name-primary">${escapeHtml(data || '-')}</span>
                                            <span class="cat-name-badge">${engineLabel}</span>
                                        </div>
                                    </div>
                                `;
                            },
                        },
                        {
                            data: 'location_names',
                            defaultContent: '-',
                            className: 'cat-td-creator',
                            render: function(data, type, row) {
                                if (type !== 'display') {
                                    return row.vendor_name ? `${data} (${row.vendor_name})` : (data || '');
                                }
                                const isVendor = Boolean(row.vendor_name);
                                return `
                                    <div class="cat-user-badge">
                                        <span class="cat-user-avatar" style="background: ${isVendor ? '#f59e0b' : '#2563eb'};">
                                            <i class="bi ${isVendor ? 'bi-truck' : 'bi-geo-alt-fill'}"></i>
                                        </span>
                                        <div class="cat-user-info">
                                            <span class="cat-user-name">${escapeHtml(data || '-')}</span>
                                            <span class="cat-user-role">${isVendor ? escapeHtml(row.vendor_name) : 'Internal'}</span>
                                        </div>
                                    </div>
                                `;
                            },
                        },
                        {
                            data: 'category_names',
                            className: 'text-center cat-td-date',
                            defaultContent: '-',
                            render: function(data, type, row) {
                                const isDigital = row.pricing_engine === 'digital_print_a3';
                                if (type !== 'display') {
                                    return isDigital ? row.category_count : data;
                                }
                                const label = isDigital ? `${Number(row.category_count || 0).toLocaleString('id-ID')} Kategori` : (data || '-');
                                return `
                                    <span class="cat-date-chip">
                                        <i class="bi bi-folder-fill"></i>
                                        <span>${escapeHtml(label)}</span>
                                    </span>
                                `;
                            },
                        },
                        {
                            data: 'lamination_count',
                            className: 'text-center cat-td-date',
                            defaultContent: 0,
                            render: function(data, type) {
                                if (type !== 'display') {
                                    return data;
                                }
                                return `
                                    <span class="cat-date-chip">
                                        <i class="bi bi-stack"></i>
                                        <span>${Number(data || 0).toLocaleString('id-ID')} Laminasi</span>
                                    </span>
                                `;
                            },
                        },
                        {
                            data: 'size_count',
                            className: 'text-center cat-td-date',
                            defaultContent: 0,
                            render: function(data, type) {
                                if (type !== 'display') {
                                    return data;
                                }
                                return `
                                    <span class="cat-date-chip">
                                        <i class="bi bi-aspect-ratio"></i>
                                        <span>${Number(data || 0).toLocaleString('id-ID')} Ukuran</span>
                                    </span>
                                `;
                            },
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'text-center cat-td-actions',
                            render: function(data, type, row) {
                                if (row.pricing_engine === 'digital_print_a3') {
                                    const editUrl = @json(route('admin_edit_pc_finishing_lamination_digital', ['id' => '__ID__']));
                                    const detailUrl = @json(route('admin_details_pc_finishing_lamination_digital', ['id' => '__ID__']));
                                    const encodedId = btoa(String(row.digital_lamination_production_cost_id));
                                    const scope = row.vendor_name ? `${row.location_names} (${row.vendor_name})` : row.location_names;
                                    return `
                                        <div class="cat-action-group digital-lamination-actions">
                                            <a class="cat-btn-action cat-btn-view"
                                                href="${detailUrl.replace('__ID__', encodedId)}"
                                                aria-label="View" title="Detail ongkos produksi laminasi Digital">
                                                <i class="bi bi-arrows-fullscreen"></i>
                                            </a>
                                            <a class="cat-btn-action cat-btn-edit"
                                                href="${editUrl.replace('__ID__', encodedId)}"
                                                aria-label="Edit" title="Edit ongkos produksi laminasi Digital">
                                                <i class="bi bi-pen"></i>
                                            </a>
                                            <button type="button"
                                                class="cat-btn-action cat-btn-del btn-delete-production-cost-lamination"
                                                data-id="${encodedId}"
                                                data-pricing-engine="digital_print_a3"
                                                data-scope="${encodeURIComponent(scope || '')}"
                                                aria-label="Hapus" title="Hapus ongkos produksi laminasi Digital">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </div>
                                    `;
                                }
                                return `
                                    <div class="cat-action-group">
                                        <button type="button"
                                            class="cat-btn-action cat-btn-view btn-detail"
                                            data-engine-id="${row.engine_id}"
                                            aria-label="Detail" title="Detail Biaya Laminasi">
                                            <i class="bi bi-arrows-fullscreen"></i>
                                        </button>
                                        <button type="button"
                                            class="cat-btn-action cat-btn-edit btn-edit"
                                            data-engine-id="${row.engine_id}"
                                            aria-label="Edit" title="Edit Biaya Laminasi">
                                            <i class="bi bi-pen"></i>
                                        </button>
                                        <button type="button"
                                            class="cat-btn-action cat-btn-del btn-delete-production-cost-lamination"
                                            data-id="${row.engine_id}"
                                            aria-label="Hapus" title="Hapus Biaya Laminasi">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </div>
                                `;
                            },
                        },
                    ],

                    columnDefs: [],

                    pageLength: 10,

                    layout: {
                        topStart: null,
                        topEnd: null,
                        bottomStart: 'info',
                        bottomEnd: 'paging',
                    },

                    language: {
                        info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ biaya laminasi',
                        infoEmpty: 'Menampilkan 0 sampai 0 dari 0 biaya laminasi',
                        zeroRecords: 'Data biaya laminasi tidak ditemukan',
                        processing: 'Memuat data...',
                    },

                    drawCallback: function() {
                        centerProcessingIndicator();
                        updateSelectionUI();
                    },
                }
            );
            centerProcessingIndicator();

            let searchTimer;
            searchInput.addEventListener('input', function() {
                const query = searchInput.value.trim();
                clearSearchButton.style.display = query.length > 0 ? 'flex' : 'none';
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => productionCostLaminationTable.search(query).draw(), 250);
            });
            clearSearchButton.addEventListener('click', function() {
                searchInput.value = '';
                clearSearchButton.style.display = 'none';
                productionCostLaminationTable.search('').draw();
                searchInput.focus();
            });
            $('#productionCostLaminationLocationFilter').on('change', function() {
                productionCostLaminationTable.ajax.reload(null, true);
            });
            $('#productionCostLaminationPageLength').on('change', function() {
                productionCostLaminationTable.page.len(Number(pageLength.value)).draw();
            });
            $('#productionCostLaminationEngineFilter').on('change', function() {
                const digital = isDigitalFilter();
                productionCostLaminationTable.column(6).visible(!digital, false);
                productionCostLaminationTable.columns.adjust();
                productionCostLaminationTable.ajax.reload(null, true);
                document.getElementById('digitalLaminationListNotice').hidden = !digital;
                document.getElementById('btnDownloadTemplateLamination').disabled = digital;
                document.getElementById('btnImportFinishingLamination').disabled = digital;
                updateExportLaminationLink();
            });

            $('#productionCostLaminationTable').on('change', '.production-cost-lamination-row-check', function() {
                const id = Number(this.value);
                const alreadySelected = this.dataset.kind === 'digital_print_a3'
                    ? selectedDigitalLaminations.has(id)
                    : selectedLargeFormatLaminations.has(id);
                if (this.checked && !alreadySelected && selectedLaminationCount() >= maxSelected) {
                    this.checked = false;
                    showToast('error', `Maksimal ${maxSelected} biaya laminasi dapat dipilih sekaligus.`);
                    return;
                }
                if (this.dataset.kind === 'digital_print_a3') {
                    if (this.checked) selectedDigitalLaminations.add(id);
                    else selectedDigitalLaminations.delete(id);
                } else if (this.checked) {
                    selectedLargeFormatLaminations.set(id, (this.dataset.productionCostIds || '')
                        .split(',').filter(Boolean).map(Number));
                } else {
                    selectedLargeFormatLaminations.delete(id);
                }
                updateSelectionUI();
            });
            checkAll.addEventListener('change', function() {
                let reachedLimit = false;
                document.querySelectorAll('.production-cost-lamination-row-check').forEach((checkbox) => {
                    const id = Number(checkbox.value);
                    if (checkbox.dataset.kind === 'digital_print_a3') {
                        if (checkAll.checked) {
                            if (!selectedDigitalLaminations.has(id)) {
                                if (selectedLaminationCount() >= maxSelected) reachedLimit = true;
                                else selectedDigitalLaminations.add(id);
                            }
                        }
                        else {
                            selectedDigitalLaminations.delete(id);
                        }
                    } else if (checkAll.checked) {
                        if (!selectedLargeFormatLaminations.has(id)) {
                            if (selectedLaminationCount() >= maxSelected) reachedLimit = true;
                            else selectedLargeFormatLaminations.set(id, (checkbox.dataset.productionCostIds || '')
                                .split(',').filter(Boolean).map(Number));
                        }
                    } else {
                        selectedLargeFormatLaminations.delete(id);
                    }
                });
                updateSelectionUI();
                if (reachedLimit) showToast('error', `Maksimal ${maxSelected} biaya laminasi dapat dipilih sekaligus.`);
            });
            document.getElementById('cancelProductionCostLaminationSelection').addEventListener('click', function() {
                selectedLargeFormatLaminations.clear();
                selectedDigitalLaminations.clear();
                updateSelectionUI();
            });
            document.getElementById('resetProductionCostLaminationFilters').addEventListener('click', function() {
                searchInput.value = '';
                clearSearchButton.style.display = 'none';
                $('#productionCostLaminationEngineFilter').val('large_format').trigger('change.select2');
                $('#productionCostLaminationLocationFilter').val('all').trigger('change.select2');
                $('#productionCostLaminationPageLength').val('10').trigger('change.select2');
                productionCostLaminationTable.column(6).visible(true, false);
                document.getElementById('digitalLaminationListNotice').hidden = true;
                document.getElementById('btnDownloadTemplateLamination').disabled = false;
                document.getElementById('btnImportFinishingLamination').disabled = false;
                selectedLargeFormatLaminations.clear();
                selectedDigitalLaminations.clear();
                productionCostLaminationTable.search('').page.len(10).ajax.reload(null, true);
                updateExportLaminationLink();
            });

            const bulkModal = document.getElementById('modalBulkDeleteProductionCostLaminations');
            const closeBulkModal = () => bulkModal.classList.remove('is-open');
            ['closeBulkDeleteProductionCostLaminations', 'cancelBulkDeleteProductionCostLaminations'].forEach((id) => {
                document.getElementById(id).addEventListener('click', closeBulkModal);
            });
            bulkModal.addEventListener('click', (event) => {
                if (event.target === bulkModal) closeBulkModal();
            });
            document.getElementById('bulkDeleteProductionCostLaminations').addEventListener('click', function() {
                const count = selectedLaminationCount();
                if (!count) return;
                document.getElementById('bulkDeleteProductionCostLaminationDescription').textContent =
                    `${count} baris dipilih. Untuk Large Format, seluruh konfigurasi laminasi pada mesin terpilih akan dihapus; sistem akan menolak penghapusan jika header masih dipakai biaya material.`;
                bulkModal.classList.add('is-open');
            });
            document.getElementById('confirmBulkDeleteProductionCostLaminations').addEventListener('click', async function() {
                const button = this;
                if (button.disabled) return;
                button.disabled = true;
                const payload = new FormData();
                let index = 0;
                selectedLargeFormatLaminations.forEach((ids, engineId) => {
                    payload.append(`large_format[${index}][engine_id]`, engineId);
                    ids.forEach((id, idIndex) => payload.append(`large_format[${index}][production_cost_ids][${idIndex}]`, id));
                    index++;
                });
                selectedDigitalLaminations.forEach((id) => payload.append('digital_print_a3[]', id));

                try {
                    const response = await fetch(document.getElementById('productionCostLaminationTable').dataset.bulkUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: payload,
                    });
                    const result = await response.json();
                    if (!response.ok || !result.success) throw new Error(result.message || 'Biaya laminasi gagal dihapus.');

                    selectedLargeFormatLaminations.clear();
                    selectedDigitalLaminations.clear();
                    closeBulkModal();
                    productionCostLaminationTable.ajax.reload(null, false);
                    showToast('success', result.message);
                } catch (error) {
                    showToast('error', error.message || 'Terjadi kesalahan saat menghapus biaya laminasi.');
                } finally {
                    button.disabled = false;
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | DELETE PRODUCTION COST LAMINASI
            |--------------------------------------------------------------------------
            */

            let productionCostLaminationIdToDelete = null;
            let productionCostLaminationIsDigitalToDelete = false;

            const modalDeleteProductionCostLamination =
                document.getElementById(
                    'modalDeleteProductionCostLamination'
                );

            const btnTutupDeleteProductionCostLamination =
                document.getElementById(
                    'btnTutupDeleteProductionCostLamination'
                );

            const btnBatalDeleteProductionCostLamination =
                document.getElementById(
                    'btnBatalDeleteProductionCostLamination'
                );

            const btnConfirmDeleteProductionCostLamination =
                document.getElementById(
                    'btnConfirmDeleteProductionCostLamination'
                );


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL DELETE
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const deleteButton = event.target.closest(
                    '.btn-delete-production-cost-lamination'
                );

                if (!deleteButton) {
                    return;
                }
                if (btnConfirmDeleteProductionCostLamination.disabled) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | AMBIL ENGINE ID
                |--------------------------------------------------------------------------
                */

                productionCostLaminationIdToDelete =
                    deleteButton.dataset.id;
                productionCostLaminationIsDigitalToDelete = deleteButton.dataset.pricingEngine === 'digital_print_a3';
                document.getElementById('largeFormatLaminationDeleteMessage').hidden = productionCostLaminationIsDigitalToDelete;
                document.getElementById('digitalLaminationDeleteMessage').hidden = !productionCostLaminationIsDigitalToDelete;
                document.getElementById('digitalLaminationDeleteScope').textContent =
                    productionCostLaminationIsDigitalToDelete ? decodeURIComponent(deleteButton.dataset.scope) : '';

                modalDeleteProductionCostLamination.classList.add(
                    'is-open'
                );
            });


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupDeleteProductionCostLaminationModal() {

                modalDeleteProductionCostLamination.classList.remove(
                    'is-open'
                );

                productionCostLaminationIdToDelete = null;
                productionCostLaminationIsDigitalToDelete = false;
            }


            btnTutupDeleteProductionCostLamination.addEventListener(
                'click',
                tutupDeleteProductionCostLaminationModal
            );

            btnBatalDeleteProductionCostLamination.addEventListener(
                'click',
                tutupDeleteProductionCostLaminationModal
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modalDeleteProductionCostLamination.addEventListener(
                'click',
                function(event) {

                    if (
                        event.target ===
                        modalDeleteProductionCostLamination
                    ) {
                        tutupDeleteProductionCostLaminationModal();
                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | TEKAN ESC
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modalDeleteProductionCostLamination.classList.contains(
                            'is-open'
                        )
                    ) {
                        tutupDeleteProductionCostLaminationModal();
                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI HAPUS PRODUCTION COST LAMINASI
            |--------------------------------------------------------------------------
            */

            btnConfirmDeleteProductionCostLamination.addEventListener(
                'click',
                async function() {

                    /*
                    |--------------------------------------------------------------------------
                    | CEK ENGINE ID
                    |--------------------------------------------------------------------------
                    */

                    if (!productionCostLaminationIdToDelete || btnConfirmDeleteProductionCostLamination.disabled) {
                        return;
                    }

                    const digitalDeleteUrl = @json(route('admin_delete_pc_finishing_lamination_digital', ['id' => '__ID__']));
                    const deleteUrl = productionCostLaminationIsDigitalToDelete ?
                        digitalDeleteUrl.replace('__ID__', productionCostLaminationIdToDelete) :
                        `/admin/production-cost-finishing-laminations/${productionCostLaminationIdToDelete}/delete`;
                    btnConfirmDeleteProductionCostLamination.disabled = true;

                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | REQUEST DELETE
                        |--------------------------------------------------------------------------
                        */

                        const response = await fetch(
                            deleteUrl, {
                                method: 'DELETE',

                                headers: {
                                    'X-CSRF-TOKEN': document
                                        .querySelector(
                                            'meta[name="csrf-token"]'
                                        )
                                        .getAttribute('content'),

                                    'Accept': 'application/json'
                                }
                            }
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | RESPONSE JSON
                        |--------------------------------------------------------------------------
                        */

                        const result = await response.json();


                        /*
                        |--------------------------------------------------------------------------
                        | BERHASIL
                        |--------------------------------------------------------------------------
                        */

                        if (
                            response.ok &&
                            result.success
                        ) {

                            /*
                            |--------------------------------------------------------------------------
                            | TUTUP MODAL
                            |--------------------------------------------------------------------------
                            */

                            tutupDeleteProductionCostLaminationModal();


                            /*
                            |--------------------------------------------------------------------------
                            | RELOAD DATATABLE
                            |--------------------------------------------------------------------------
                            */

                            productionCostLaminationTable.ajax.reload(
                                null,
                                false
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | NOTIFIKASI
                            |--------------------------------------------------------------------------
                            */

                            showToast(
                                'success',
                                result.message
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | GAGAL
                        |--------------------------------------------------------------------------
                        */
                        else {

                            showToast(
                                'error',
                                result.message ||
                                'Production cost laminasi gagal dihapus.'
                            );
                        }

                    } catch (error) {

                        console.error(error);


                        /*
                        |--------------------------------------------------------------------------
                        | ERROR SERVER
                        |--------------------------------------------------------------------------
                        */

                        showToast(
                            'error',
                            'Terjadi kesalahan saat menghapus production cost laminasi.'
                        );
                    } finally {
                        btnConfirmDeleteProductionCostLamination.disabled = false;
                    }

                }
            );

        });

        document.addEventListener('click', function(event) {
            const editButton = event.target.closest('.btn-edit');

            if (!editButton) {
                return;
            }

            const engineId = editButton.dataset.engineId;

            if (!engineId) {
                return;
            }

            window.location.href =
                `{{ url('/admin/production-cost-finishing-laminations') }}/${btoa(String(engineId))}/edit`;
        });

        document.addEventListener('click', function(event) {
            const detailButton = event.target.closest(
                '#productionCostLaminationTable .btn-detail'
            );

            if (!detailButton) {
                return;
            }

            const engineId = detailButton.dataset.engineId;

            if (!engineId) {
                return;
            }

            const encodedId = btoa(String(engineId));

            const url = @json(route('admin_details_pc_finishing_lamination', ['id' => '__ID__']));

            window.location.href = url.replace(
                '__ID__',
                encodedId
            );
        });

        document.addEventListener('DOMContentLoaded', function() {

            const btnImport =
                document.getElementById('btnImportFinishingLamination');

            const modal =
                document.getElementById('modalImportFinishingLamination');

            const btnClose =
                document.getElementById('btnCloseImportFinishingLamination');

            const btnCancel =
                document.getElementById('btnCancelImportFinishingLamination');

            const btnSave =
                document.getElementById('btnSaveImportFinishingLamination');

            const fileInput =
                document.getElementById('fileImportFinishingLamination');

            const fileUploadBox =
                document.getElementById('fileUploadBoxFinishingLamination');

            const fileSelectedName =
                document.getElementById('fileSelectedNameFinishingLamination');


            /*
            |--------------------------------------------------------------------------
            | Open Modal
            |--------------------------------------------------------------------------
            */

            btnImport?.addEventListener('click', function() {
                modal.classList.add('is-open');
            });


            /*
            |--------------------------------------------------------------------------
            | Close Modal
            |--------------------------------------------------------------------------
            */

            function closeImportModal() {
                modal.classList.remove('is-open');
            }

            btnClose?.addEventListener('click', closeImportModal);

            btnCancel?.addEventListener('click', closeImportModal);


            /*
            |--------------------------------------------------------------------------
            | Close ketika klik overlay
            |--------------------------------------------------------------------------
            */

            modal?.addEventListener('click', function(event) {

                if (event.target === modal) {
                    closeImportModal();
                }

            });


            /*
            |--------------------------------------------------------------------------
            | Close dengan ESC
            |--------------------------------------------------------------------------
            */

            document.addEventListener('keydown', function(event) {

                if (
                    event.key === 'Escape' &&
                    modal.classList.contains('is-open')
                ) {
                    closeImportModal();
                }

            });


            /*
            |--------------------------------------------------------------------------
            | File Change
            |--------------------------------------------------------------------------
            */

            fileInput?.addEventListener('change', function() {

                if (this.files && this.files.length > 0) {

                    fileSelectedName.textContent =
                        this.files[0].name;

                    fileUploadBox.classList.add('has-file');

                } else {

                    fileSelectedName.textContent =
                        'Belum ada file dipilih';

                    fileUploadBox.classList.remove('has-file');

                }

            });


            /*
            |--------------------------------------------------------------------------
            | Import
            |--------------------------------------------------------------------------
            */

            btnSave?.addEventListener('click', async function() {

                if (!fileInput.files || fileInput.files.length === 0) {

                    showToast(
                        'error',
                        'Silakan pilih file Excel terlebih dahulu.'
                    );

                    return;
                }


                const originalButtonHtml =
                    btnSave.innerHTML;

                btnSave.disabled = true;

                btnSave.innerHTML =
                    'Memproses...';


                try {

                    const formData = new FormData();

                    formData.append(
                        'file',
                        fileInput.files[0]
                    );


                    const csrfToken =
                        document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute('content');


                    formData.append(
                        '_token',
                        csrfToken
                    );


                    const response = await fetch(
                        "{{ route('admin_import_pc_finishing_laminations') }}", {
                            method: 'POST',

                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },

                            body: formData
                        }
                    );


                    const result =
                        await response.json();


                    if (!response.ok || !result.success) {

                        showToast(
                            result.type || 'error',
                            result.message ||
                            'Import data gagal.'
                        );

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Success
                    |--------------------------------------------------------------------------
                    */

                    showToast(
                        'success',
                        result.message ||
                        'Production Cost Laminasi berhasil diimport.'
                    );


                    /*
                    | Reset file
                    */

                    fileInput.value = '';

                    fileSelectedName.textContent =
                        'Belum ada file dipilih';

                    fileUploadBox.classList.remove(
                        'has-file'
                    );


                    /*
                    | Close modal
                    */

                    closeImportModal();


                    /*
                    | Reload DataTables jika tersedia
                    */

                    if (
                        typeof table !== 'undefined' &&
                        table.ajax
                    ) {

                        table.ajax.reload(null, false);

                    } else {

                        window.location.reload();

                    }

                } catch (error) {

                    console.error(
                        'Import Production Cost Laminasi error:',
                        error
                    );

                    showToast(
                        'error',
                        'Terjadi kesalahan saat mengimport data.'
                    );

                } finally {

                    btnSave.disabled = false;

                    btnSave.innerHTML =
                        originalButtonHtml;

                }

            });

        });

        document.addEventListener('DOMContentLoaded', function() {

            /* =========================
               DOWNLOAD TEMPLATE
            ========================= */

            const btnDownloadTemplate = document.getElementById(
                'btnDownloadTemplateLamination'
            );

            const modalDownloadTemplate = document.getElementById(
                'modalDownloadTemplateLamination'
            );

            const btnCloseDownloadTemplate = document.getElementById(
                'btnCloseDownloadTemplateLamination'
            );

            const btnCancelDownloadTemplate = document.getElementById(
                'btnCancelDownloadTemplateLamination'
            );


            /* =========================
               BUKA MODAL
            ========================= */

            btnDownloadTemplate?.addEventListener('click', function() {
                modalDownloadTemplate?.classList.add('is-open');
            });


            /* =========================
               TUTUP MODAL
            ========================= */

            function closeDownloadTemplateModal() {
                modalDownloadTemplate?.classList.remove('is-open');
            }


            btnCloseDownloadTemplate?.addEventListener(
                'click',
                closeDownloadTemplateModal
            );

            btnCancelDownloadTemplate?.addEventListener(
                'click',
                closeDownloadTemplateModal
            );


            /* =========================
               KLIK BACKGROUND
            ========================= */

            modalDownloadTemplate?.addEventListener('click', function(event) {

                if (event.target === modalDownloadTemplate) {
                    closeDownloadTemplateModal();
                }

            });


            /* =========================
               TOMBOL ESC
            ========================= */

            document.addEventListener('keydown', function(event) {

                if (event.key === 'Escape') {
                    closeDownloadTemplateModal();
                }

            });

        });
    </script>
@endsection

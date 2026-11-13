@extends('admin_master')
@section('contents')
    <link rel="stylesheet" href="{{ asset('css/production-cost-finishing-index.css') }}">
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero cat-hero-compact">
                    <div class="hero-text"><span class="eyebrow">BIAYA · MATERIAL</span>
                        <p class="hero-sub">Kelola biaya material dan finishing yang digunakan sebagai dasar perhitungan
                            biaya serta estimasi harga produk.</p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card cat-card-container">
                        <div class="cat-card-header">
                            <div class="cat-card-header-main">
                                <div class="cat-card-title-wrap">
                                    <div class="cat-card-icon"><i class="bi bi-cash-stack"></i></div>
                                    <div>
                                        <div class="cat-title-row">
                                            <h3 class="cat-card-title">Daftar Biaya Material</h3>
                                            <span class="cat-badge-total">
                                                <span class="cat-pulse-dot"></span>
                                                <span id="productionCostTotalCount">Memuat data...</span>
                                            </span>
                                        </div>
                                        <p class="cat-card-desc">Konfigurasi biaya material berdasarkan mesin dan lokasi.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="cat-card-actions">
                                <a id="btnExportProductionCost" href="{{ route('admin_export_pc_finishings') }}"
                                    class="cat-btn-head" title="Export Excel">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <path d="M7 10l5 5 5-5" /><path d="M12 15V3" />
                                    </svg><span>Export</span>
                                </a>
                                <button type="button" class="cat-btn-head" id="btnDownloadTemplateFinishingService"
                                    title="Download Template Excel">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                        <path d="M7 10l5 5 5-5" /><path d="M12 15V3" />
                                    </svg><span>Template</span>
                                </button>
                                <button type="button" class="cat-btn-head" id="btnImportFinishingService" title="Import Excel">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M12 16V3" /><path d="m7 8 5-5 5 5" />
                                        <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                                    </svg><span>Import</span>
                                </button>
                                <a href="{{ route('admin_create_pc_finishing') }}" class="cat-btn-head-primary">
                                    <i class="bi bi-plus-lg"></i><span>Tambah Ongkos</span>
                                </a>
                            </div>
                        </div>

                        <div class="cat-bulk-toolbar" id="productionCostBulkToolbar" style="display:none;">
                            <div class="cat-bulk-info">
                                <span class="cat-bulk-badge" id="productionCostBulkCount">0 terpilih</span>
                                <span class="cat-bulk-text">Biaya material terpilih siap untuk tindakan massal</span>
                            </div>
                            <div class="cat-bulk-actions">
                                <button type="button" class="btn btn--ghost btn--sm" id="cancelProductionCostSelection">
                                    <i class="bi bi-x-circle me-1"></i>Batal Pilihan
                                </button>
                                <button type="button" class="btn btn--danger btn--sm" id="bulkDeleteProductionCosts">
                                    <i class="bi bi-trash3-fill me-1"></i>Hapus Terpilih
                                </button>
                            </div>
                        </div>

                        <div class="cat-filter-card">
                            <div class="cat-filter-group-left">
                                <div class="cat-search-box">
                                    <i class="bi bi-search cat-search-icon"></i>
                                    <input type="text" id="productionCostSearch" class="cat-filter-input"
                                        placeholder="Cari mesin, lokasi, atau vendor...">
                                    <button type="button" id="clearProductionCostSearch" class="cat-clear-btn"
                                        title="Hapus pencarian" style="display:none;">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                                <div class="cat-select-box">
                                    <i class="bi bi-gear-fill cat-select-icon"></i>
                                    <select id="productionCostEngineFilter" class="select2" style="width:100%;"
                                        aria-label="Filter mesin ongkos produksi">
                                        <option value="large_format" selected>Large Format</option>
                                        <option value="digital_print_a3">Digital Print A3+</option>
                                    </select>
                                </div>
                                <div class="cat-filter-subgroup">
                                    <div class="cat-select-box">
                                        <i class="bi bi-geo-alt-fill cat-select-icon"></i>
                                        <select id="productionCostLocationFilter" class="select2" style="width:100%;"
                                            aria-label="Filter lokasi">
                                            <option value="all">Semua Lokasi</option>
                                            @foreach ($filterLocations as $location)
                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="button" id="resetProductionCostFilters" class="cat-btn-reset"
                                        title="Reset semua filter">
                                        <i class="bi bi-arrow-counterclockwise"></i><span>Reset</span>
                                    </button>
                                </div>
                            </div>
                            <div class="cat-filter-group-right">
                                <div class="cat-length-box">
                                    <span class="cat-length-label">Baris:</span>
                                    <select id="productionCostPageLength" class="select2" style="width:76px;">
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="cat-table-wrap">
                            <table id="productionCostTable" class="cat-modern-table" style="width:100%"
                                data-url="{{ route('admin_data_pc_finishings') }}"
                                data-bulk-url="{{ route('admin_bulk_destroy_pc_finishings') }}">
                                <thead>
                                    <tr>
                                        <th class="cat-th-check text-center">
                                            <label class="cat-custom-checkbox" title="Pilih Semua di Halaman Ini">
                                                <input type="checkbox" id="checkAllProductionCosts"
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
                                        <th style="min-width:200px;">Lokasi / Vendor</th>
                                        <th class="text-center" style="min-width:130px;">Material</th>
                                        <th class="text-center" style="min-width:120px;">Lebar</th>
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

        <div class="modal-overlay" id="modalBulkDeleteProductionCosts">
            <div class="modal-dialog modal-delete">
                <div class="modal-header">
                    <div>
                        <span class="eyebrow">KONFIGURASI · BIAYA MATERIAL</span><br>
                        <span class="eyebrow">Hapus Pilihan</span>
                    </div>
                    <button type="button" class="modal-close" id="closeBulkDeleteProductionCosts" aria-label="Tutup">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="delete-confirmation">
                        <div class="delete-icon"><i class="bi bi-trash3-fill"></i></div>
                        <div class="delete-content">
                            <h3>Hapus biaya material terpilih?</h3>
                            <p id="bulkDeleteProductionCostDescription"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--ghost" id="cancelBulkDeleteProductionCosts">Batal</button>
                    <button type="button" class="btn btn--danger" id="confirmBulkDeleteProductionCosts">
                        <i class="bi bi-trash3-fill"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Import FinishingService -->
    <div class="modal-overlay" id="modalImportFinishingService">

        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">

                <div>
                    <span class="eyebrow">KONFIGURASI</span>
                    <br>
                    <span class="eyebrow">BIAYA MATERIAL</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseImportFinishingService" aria-label="Tutup">

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
                        Pilih file Excel yang berisi data mesin untuk diimport
                        ke dalam sistem.
                    </p>
                </div>


                <!-- Custom File Input -->
                <label for="fileImportFinishingService" class="file-upload-box" id="fileUploadBox">

                    <input type="file" id="fileImportFinishingService" name="file" accept=".xlsx,.xls" hidden>


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


                    <div class="file-selected-name" id="fileSelectedName">

                        Belum ada file dipilih

                    </div>

                </label>

            </div>


            <!-- Footer -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCancelImportFinishingService">

                    Batal

                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportFinishingService">

                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8 5-5 5 5" />
                    </svg>

                    Import Data

                </button>

            </div>

        </div>

    </div>

    <!-- MODAL DELETE PRODUCTION COST -->
    <div class="modal-overlay" id="modalDeleteProductionCost">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        HARGA & TARIF · PRODUCTION COST
                    </span>
                    <br>

                    <span class="eyebrow">
                        Hapus Production Cost
                    </span>

                </div>

                <button type="button" class="modal-close" id="btnTutupDeleteProductionCost" aria-label="Tutup">
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
                            Hapus production cost?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus seluruh
                            konfigurasi production cost untuk mesin ini?
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteProductionCost">
                    Batal
                </button>

                <button type="button" class="btn btn--danger" id="btnConfirmDeleteProductionCost">
                    <i class="bi bi-trash3-fill"></i>
                    Hapus
                </button>

            </div>

        </div>

    </div>

    <!-- MODAL DELETE PRODUCTION COST DIGITAL PRINT A3+ -->
    <div class="modal-overlay" id="modalDeleteDigitalProductionCost">

        <div class="modal-dialog modal-delete">

            <div class="modal-header">

                <div>
                    <span class="eyebrow">
                        HARGA & TARIF · PRODUCTION COST
                    </span>
                    <br>
                    <span class="eyebrow">
                        Hapus Production Cost Digital Print A3+
                    </span>
                </div>

                <button type="button" class="modal-close" id="btnTutupDeleteDigitalProductionCost"
                    aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M18 6L6 18" />
                        <path d="M6 6L18 18" />
                    </svg>
                </button>

            </div>

            <div class="modal-body">

                <div class="delete-confirmation">

                    <div class="delete-icon">
                        <i class="bi bi-trash3-fill"></i>
                    </div>

                    <div class="delete-content">

                        <h3>
                            Hapus production cost?
                        </h3>

                        <p>
                            Ongkos produksi Digital Print A3+ untuk lokasi
                            <strong id="deleteDigitalLocation"></strong>
                            beserta <strong id="deleteDigitalMaterialCount"></strong> material dan seluruh harga
                            per tier-nya akan dihapus. Data lokasi lain dan mesin Large Format tidak ikut terhapus.
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteDigitalProductionCost">
                    Batal
                </button>

                <button type="button" class="btn btn--danger" id="btnConfirmDeleteDigitalProductionCost">
                    <i class="bi bi-trash3-fill"></i>
                    Hapus
                </button>

            </div>

        </div>

    </div>

    <div class="modal-overlay" id="modalDownloadTemplateFinishingService">

        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">

                <div>
                    <span class="eyebrow">KONFIGURASI</span>
                    <br>
                    <span class="eyebrow">BIAYA MATERIAL</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseDownloadTemplateFinishingService" aria-label="Tutup">
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
                        Pilih jenis template Excel yang ingin digunakan untuk
                        mengisi data biaya material.
                    </p>
                </div>


                <div class="template-download-options">

                    <!-- Outsourcing -->
                    <a href="{{ asset('/templates/template_ongkos_produksi_outsourcing.xlsx') }}"
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

                            <strong>
                                Template Outsourcing
                            </strong>

                            <span>
                                Template untuk material dengan proses
                                outsourcing dan harga vendor.
                            </span>

                        </div>

                        <div class="template-download-arrow">
                            <svg viewBox="0 0 24 24">
                                <path d="M5 12h14" />
                                <path d="m13 6 6 6-6 6" />
                            </svg>
                        </div>

                    </a>


                    <!-- Non-Outsourcing -->
                    <a href="{{ asset('templates/template_ongkos_produksi_finishing.xlsx') }}"
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

                            <strong>
                                Template Non-Outsourcing
                            </strong>

                            <span>
                                Template untuk material dengan perhitungan
                                biaya produksi internal.
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

                <button type="button" class="btn btn--ghost" id="btnCancelDownloadTemplateFinishingService">
                    Batal
                </button>

            </div>

        </div>

    </div>
@endsection


@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const btnImport = document.getElementById(
                'btnImportFinishingService'
            );

            const modal = document.getElementById(
                'modalImportFinishingService'
            );

            const btnClose = document.getElementById(
                'btnCloseImportFinishingService'
            );

            const btnCancel = document.getElementById(
                'btnCancelImportFinishingService'
            );

            const btnSave = document.getElementById(
                'btnSaveImportFinishingService'
            );

            const fileInput = document.getElementById(
                'fileImportFinishingService'
            );

            const fileName = document.getElementById(
                'fileSelectedName'
            );


            /* =========================
               BUKA MODAL
            ========================= */

            btnImport?.addEventListener('click', function() {

                modal?.classList.add('is-open');

            });


            /* =========================
               TUTUP MODAL
            ========================= */

            function closeModal() {

                modal?.classList.remove('is-open');

            }

            btnClose?.addEventListener('click', closeModal);

            btnCancel?.addEventListener('click', closeModal);


            /* =========================
               KLIK BACKGROUND
            ========================= */

            modal?.addEventListener('click', function(event) {

                if (event.target === modal) {

                    closeModal();

                }

            });


            /* =========================
               TOMBOL ESC
            ========================= */

            document.addEventListener('keydown', function(event) {

                if (event.key === 'Escape') {

                    closeModal();

                }

            });


            /* =========================
               FILE SELECTED
            ========================= */

            fileInput?.addEventListener('change', function() {

                if (this.files.length > 0) {

                    fileName.textContent = this.files[0].name;

                    fileName.classList.add('has-file');

                } else {

                    fileName.textContent =
                        'Belum ada file dipilih';

                    fileName.classList.remove('has-file');

                }

            });


            /* =========================
               IMPORT DATA
            ========================= */

            btnSave?.addEventListener('click', async function() {

                /*
                |--------------------------------------------------------------------------
                | VALIDASI FILE
                |--------------------------------------------------------------------------
                */

                if (!fileInput || fileInput.files.length === 0) {

                    showToast('error', 'Silakan pilih file Excel terlebih dahulu.');

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | SIMPAN STATE BUTTON
                |--------------------------------------------------------------------------
                */

                const originalButtonHtml = btnSave.innerHTML;

                btnSave.disabled = true;

                btnSave.innerHTML = `
                    <span>Memproses...</span>
                `;


                /*
                |--------------------------------------------------------------------------
                | FORM DATA
                |--------------------------------------------------------------------------
                */

                const formData = new FormData();

                formData.append(
                    'file',
                    fileInput.files[0]
                );


                /*
                |--------------------------------------------------------------------------
                | CSRF
                |--------------------------------------------------------------------------
                */

                const csrfToken = document.querySelector(
                    'meta[name="csrf-token"]'
                )?.getAttribute('content');


                if (csrfToken) {

                    formData.append(
                        '_token',
                        csrfToken
                    );

                }


                try {

                    /*
                    |--------------------------------------------------------------------------
                    | POST KE ROUTE IMPORT
                    |--------------------------------------------------------------------------
                    */

                    const response = await fetch(
                        "{{ route('admin_import_pc_finishing') }}", {
                            method: 'POST',

                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },

                            body: formData
                        }
                    );


                    const result = await response.json();


                    /*
                    |--------------------------------------------------------------------------
                    | ERROR DARI BACKEND
                    |--------------------------------------------------------------------------
                    */

                    if (!response.ok || !result.success) {
                        showToast(
                            result.type || 'error',
                            result.message || 'Import data gagal.'
                        );

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SUCCESS
                    |--------------------------------------------------------------------------
                    */

                    showToast(
                        'success',
                        result.message || 'Data berhasil diimport.'
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | RESET FILE
                    |--------------------------------------------------------------------------
                    */

                    fileInput.value = '';

                    fileName.textContent =
                        'Belum ada file dipilih';

                    fileName.classList.remove('has-file');


                    /*
                    |--------------------------------------------------------------------------
                    | TUTUP MODAL
                    |--------------------------------------------------------------------------
                    */

                    closeModal();


                    /*
                    |--------------------------------------------------------------------------
                    | RELOAD DATATABLE
                    |--------------------------------------------------------------------------
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
                        'Import Production Cost Error:',
                        error
                    );


                    showToast(
                        'error',
                        error.message || 'Terjadi kesalahan saat melakukan import.'
                    );

                } finally {

                    /*
                    |--------------------------------------------------------------------------
                    | KEMBALIKAN BUTTON
                    |--------------------------------------------------------------------------
                    */

                    btnSave.disabled = false;

                    btnSave.innerHTML = originalButtonHtml;

                }

            });

        });

        let productionCostTable;

        document.addEventListener('DOMContentLoaded', function() {

            const engineFilter = document.getElementById('productionCostEngineFilter');
            const locationFilter = document.getElementById('productionCostLocationFilter');
            const customSearch = document.getElementById('productionCostSearch');
            const clearSearch = document.getElementById('clearProductionCostSearch');
            const pageLength = document.getElementById('productionCostPageLength');
            const selectedProductionCosts = new Map();
            let searchTimer;

            function isDigitalPrintFilter() {
                return engineFilter.value === 'digital_print_a3';
            }

            function escapeDigitalHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            }

            function selectionKey(row) {
                return row.pricing_engine === 'digital_print_a3' ?
                    `digital:${row.digital_production_cost_id}` :
                    `large-format:${row.engine_id}`;
            }

            function updateSelection() {
                const rows = Array.from(document.querySelectorAll('.cat-row-check'));
                const checkedCount = rows.filter(input => selectedProductionCosts.has(input.value)).length;
                const checkAll = document.getElementById('checkAllProductionCosts');
                const toolbar = document.getElementById('productionCostBulkToolbar');
                const checkAllLabel = checkAll.closest('.cat-custom-checkbox');

                toolbar.style.display = selectedProductionCosts.size ? 'flex' : 'none';
                document.getElementById('productionCostBulkCount').textContent =
                    `${selectedProductionCosts.size} terpilih`;

                rows.forEach(input => {
                    input.checked = selectedProductionCosts.has(input.value);
                    input.closest('tr').classList.toggle('is-selected', input.checked);
                });

                checkAll.checked = rows.length > 0 && checkedCount === rows.length;
                checkAll.indeterminate = checkedCount > 0 && checkedCount < rows.length;
                checkAllLabel.classList.toggle('is-indeterminate', checkAll.indeterminate);
            }

            function clearSelection() {
                selectedProductionCosts.clear();
                updateSelection();
            }

            if (typeof $.fn.select2 !== 'undefined') {
                $('#productionCostEngineFilter, #productionCostLocationFilter').select2({
                    minimumResultsForSearch: 6,
                    width: '100%'
                }).on('select2:open', function() {
                    $(this).closest('.cat-select-box').addClass('is-open');
                }).on('select2:close', function() {
                    $(this).closest('.cat-select-box').removeClass('is-open');
                });
                $('#productionCostPageLength').select2({ minimumResultsForSearch: -1, width: '76px' });
                $(document).on('click', '.cat-select-icon', function(event) {
                    event.preventDefault();
                    $(this).closest('.cat-select-box').find('select.select2').select2('open');
                });
            }

            productionCostTable = new DataTable('#productionCostTable', {
                processing: true,
                serverSide: true,
                ordering: true,
                order: [[2, 'asc']],
                stripeClasses: [],

                ajax: {
                    url: document.getElementById('productionCostTable').dataset.url,
                    type: 'GET',
                    data: function(data) {
                        data.pricing_engine = engineFilter.value;
                        data.location_id = locationFilter.value === 'all' ? '' : locationFilter.value;
                    },
                },

                columns: [
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center cat-td-check',
                        render: function(data, type, row) {
                            const key = escapeDigitalHtml(selectionKey(row));
                            const label = escapeDigitalHtml(`${row.engine_name} ${row.location_names || ''}`);
                            return `
                                <label class="cat-custom-checkbox">
                                    <input type="checkbox" class="cat-row-check" value="${key}"
                                        aria-label="Pilih biaya material ${label}">
                                    <span class="cat-checkbox-mark">
                                        <svg class="check-icon" viewBox="0 0 14 14" fill="none">
                                            <path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#ffffff" stroke-width="2.2"
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
                            return `<span class="cat-num-pill">${meta.settings._iDisplayStart + meta.row + 1}</span>`;
                        },
                    },
                    {
                        data: 'engine_name',
                        defaultContent: '-',
                        className: 'cat-td-name',
                        render: function(data) {
                            return `
                                <div class="cat-cell-name-box">
                                    <div class="cat-name-details">
                                        <span class="cat-name-primary">${escapeDigitalHtml(data || '-')}</span>
                                        <span class="cat-name-badge">Biaya Material</span>
                                    </div>
                                </div>`;
                        },
                    },

                    {
                        data: 'location_names',
                        defaultContent: '-',
                        render: function(data, type, row) {
                            const location = escapeDigitalHtml(data ?? '-');
                            return row.vendor_name ?
                                `<div class="cat-name-details"><span>${location}</span><span class="cat-name-badge">Vendor: ${escapeDigitalHtml(row.vendor_name)}</span></div>` :
                                `<span class="cat-date-chip"><span>${location}</span></span>`;
                        },
                    },
                    {
                        data: 'material_count',
                        className: 'text-center cat-td-date',
                        render: function(data) {
                            return `<span class="cat-date-chip"><span>${Number(data || 0).toLocaleString('id-ID')} Material</span></span>`;
                        },
                    },
                    {
                        data: 'width_count',
                        className: 'text-center cat-td-date',
                        defaultContent: '-',
                        render: function(data) {
                            return data === undefined || data === null ? '-' :
                                `<span class="cat-date-chip"><span>${Number(data).toLocaleString('id-ID')} Lebar</span></span>`;
                        },
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center cat-td-actions',
                        render: function(data, type, row) {
                            if (row.pricing_engine === 'digital_print_a3') {
                                return `
                                    <div class="cat-action-group">
                                        <button type="button" class="cat-btn-action cat-btn-edit btn-detail-digital-production-cost"
                                            data-id="${Number(row.digital_production_cost_id)}" title="Detail" aria-label="Detail">
                                            <i class="bi bi-arrows-fullscreen"></i>
                                        </button>
                                        <button type="button" class="cat-btn-action cat-btn-edit btn-edit-digital-production-cost"
                                            data-id="${Number(row.digital_production_cost_id)}" title="Edit" aria-label="Edit">
                                            <i class="bi bi-pen"></i>
                                        </button>
                                        <button type="button" class="cat-btn-action cat-btn-del btn-delete-digital-production-cost"
                                            data-id="${Number(row.digital_production_cost_id)}"
                                            data-location="${escapeDigitalHtml(row.location_names)}"
                                            data-vendor="${escapeDigitalHtml(row.vendor_name)}"
                                            data-material-count="${Number(row.material_count) || 0}"
                                            title="Hapus" aria-label="Hapus">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </div>
                                `;
                            }

                            return `
                                <div class="cat-action-group">
                                    <button
                                        class="cat-btn-action cat-btn-edit btn-detail"
                                        data-engine-id="${row.engine_id}"
                                        title="Detail" aria-label="Detail"
                                    >
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </button>

                                    <button
                                        class="cat-btn-action cat-btn-edit btn-edit"
                                        data-engine-id="${row.engine_id}"
                                        title="Edit" aria-label="Edit"
                                    >
                                        <i class="bi bi-pen"></i>
                                    </button>

                                    <button
                                        type="button"
                                        class="cat-btn-action cat-btn-del btn-delete-production-cost"
                                        data-id="${row.engine_id}"
                                        title="Hapus" aria-label="Hapus"
                                    >
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            `;
                        },
                    },
                ],

                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],
                layout: {
                    topStart: null,
                    topEnd: null,
                    bottomStart: 'info',
                    bottomEnd: 'paging',
                },

                language: {
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ konfigurasi',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 konfigurasi',
                    zeroRecords: 'Data production cost tidak ditemukan',
                    processing: 'Memuat data...',
                },
            });

            productionCostTable.on('draw', function() {
                const info = productionCostTable.page.info();
                document.getElementById('productionCostTotalCount').textContent =
                    `${info.recordsTotal.toLocaleString('id-ID')} Total Biaya`;
                updateSelection();
            });

            function filterChanged() {
                clearSelection();
                productionCostTable.column(5).visible(!isDigitalPrintFilter(), false);
                productionCostTable.columns.adjust();
                productionCostTable.ajax.reload(null, true);
                updateExportProductionCostLink();
            }

            $('#productionCostEngineFilter, #productionCostLocationFilter').on('change', filterChanged);

            customSearch.addEventListener('input', function() {
                clearSearch.style.display = this.value.trim() ? 'flex' : 'none';
                clearTimeout(searchTimer);
                clearSelection();
                searchTimer = setTimeout(() => productionCostTable.search(this.value.trim()).draw(), 250);
            });

            clearSearch.addEventListener('click', function() {
                clearTimeout(searchTimer);
                customSearch.value = '';
                clearSearch.style.display = 'none';
                clearSelection();
                productionCostTable.search('').draw();
                customSearch.focus();
            });

            $('#productionCostPageLength').on('change', function() {
                clearSelection();
                productionCostTable.page.len(Number(this.value)).draw();
            });

            document.getElementById('resetProductionCostFilters').addEventListener('click', function() {
                clearTimeout(searchTimer);
                customSearch.value = '';
                clearSearch.style.display = 'none';
                engineFilter.value = 'large_format';
                locationFilter.value = 'all';
                pageLength.value = '10';
                if (typeof $.fn.select2 !== 'undefined') {
                    $('#productionCostEngineFilter, #productionCostLocationFilter, #productionCostPageLength')
                        .trigger('change.select2');
                }
                clearSelection();
                productionCostTable.search('').page.len(10).column(5).visible(true, false);
                productionCostTable.columns.adjust();
                productionCostTable.ajax.reload(null, true);
                updateExportProductionCostLink();
            });

            document.getElementById('checkAllProductionCosts').addEventListener('change', function() {
                productionCostTable.rows({ page: 'current' }).data().each(row => {
                    const key = selectionKey(row);
                    if (this.checked) {
                        selectedProductionCosts.set(key, row);
                    } else {
                        selectedProductionCosts.delete(key);
                    }
                });
                updateSelection();
            });

            document.getElementById('productionCostTable').addEventListener('change', function(event) {
                if (!event.target.classList.contains('cat-row-check')) return;
                const row = productionCostTable.row(event.target.closest('tr')).data();
                if (!row) return;
                const key = selectionKey(row);
                if (event.target.checked) {
                    selectedProductionCosts.set(key, row);
                } else {
                    selectedProductionCosts.delete(key);
                }
                updateSelection();
            });

            document.getElementById('cancelProductionCostSelection').addEventListener('click', clearSelection);

            const bulkModal = document.getElementById('modalBulkDeleteProductionCosts');
            const bulkConfirm = document.getElementById('confirmBulkDeleteProductionCosts');

            function closeBulkModal() {
                if (!bulkConfirm.disabled) bulkModal.classList.remove('is-open');
            }

            document.getElementById('bulkDeleteProductionCosts').addEventListener('click', function() {
                if (!selectedProductionCosts.size) return;
                const largeFormatCount = Array.from(selectedProductionCosts.values())
                    .filter(row => row.pricing_engine !== 'digital_print_a3').length;
                const digitalCount = selectedProductionCosts.size - largeFormatCount;
                document.getElementById('bulkDeleteProductionCostDescription').textContent =
                    `${selectedProductionCosts.size} pilihan akan dihapus (${largeFormatCount} mesin Large Format beserta seluruh lokasi konfigurasinya dan ${digitalCount} konfigurasi Digital Print A3+). Penghapusan dibatalkan seluruhnya jika data Large Format dipakai oleh rincian biaya laminasi.`;
                bulkModal.classList.add('is-open');
            });

            document.getElementById('closeBulkDeleteProductionCosts').addEventListener('click', closeBulkModal);
            document.getElementById('cancelBulkDeleteProductionCosts').addEventListener('click', closeBulkModal);
            bulkModal.addEventListener('click', event => {
                if (event.target === bulkModal) closeBulkModal();
            });
            document.addEventListener('keydown', event => {
                if (event.key === 'Escape' && bulkModal.classList.contains('is-open')) closeBulkModal();
            });

            bulkConfirm.addEventListener('click', async function() {
                if (!selectedProductionCosts.size) return;
                const largeFormat = [];
                const digitalPrintA3 = [];
                selectedProductionCosts.forEach(row => {
                    if (row.pricing_engine === 'digital_print_a3') {
                        digitalPrintA3.push(Number(row.digital_production_cost_id));
                    } else {
                        largeFormat.push({
                            engine_id: Number(row.engine_id),
                            production_cost_ids: row.production_cost_ids.map(Number),
                        });
                    }
                });

                bulkConfirm.disabled = true;
                try {
                    const response = await fetch(document.getElementById('productionCostTable').dataset.bulkUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ large_format: largeFormat, digital_print_a3: digitalPrintA3 }),
                    });
                    const result = await response.json();
                    if (!response.ok || !result.success) {
                        showToast('error', result.message || 'Biaya material gagal dihapus.');
                        return;
                    }
                    clearSelection();
                    bulkModal.classList.remove('is-open');
                    productionCostTable.ajax.reload(null, false);
                    showToast('success', result.message);
                } catch (error) {
                    console.error('Gagal menghapus biaya material:', error);
                    showToast('error', 'Terjadi kesalahan saat menghapus biaya material.');
                } finally {
                    bulkConfirm.disabled = false;
                }
            });

            function updateExportProductionCostLink() {
                const exportUrl = "{{ route('admin_export_pc_finishings') }}";

                document.getElementById('btnExportProductionCost').href = isDigitalPrintFilter() ?
                    exportUrl + '?pricing_engine=digital_print_a3' :
                    exportUrl;
            }

            updateExportProductionCostLink();

            document.addEventListener('click', function(event) {
                const detailDigitalButton = event.target.closest('.btn-detail-digital-production-cost');

                if (detailDigitalButton && detailDigitalButton.dataset.id) {
                    window.location.href =
                        "{{ route('admin_pc_finishing_digital_details', ':id') }}"
                        .replace(':id', btoa(String(detailDigitalButton.dataset.id)));

                    return;
                }

                const editDigitalButton = event.target.closest('.btn-edit-digital-production-cost');

                if (editDigitalButton && editDigitalButton.dataset.id) {
                    window.location.href =
                        "{{ route('admin_edit_pc_finishing_digital', ':id') }}"
                        .replace(':id', btoa(String(editDigitalButton.dataset.id)));

                    return;
                }

                const pendingButton = event.target.closest('.btn-digital-production-cost-pending');

                if (!pendingButton) {
                    return;
                }

                showToast(
                    'warning',
                    `Fitur ${pendingButton.dataset.action} ongkos produksi Digital Print A3+ belum tersedia.`
                );
            });

            /*
            |--------------------------------------------------------------------------
            | DELETE PRODUCTION COST DIGITAL PRINT A3+
            |--------------------------------------------------------------------------
            */

            const modalDeleteDigital = document.getElementById('modalDeleteDigitalProductionCost');
            const btnConfirmDeleteDigital = document.getElementById('btnConfirmDeleteDigitalProductionCost');
            let digitalProductionCostIdToDelete = null;

            function closeDeleteDigitalModal() {
                if (btnConfirmDeleteDigital.disabled) {
                    return;
                }

                modalDeleteDigital.classList.remove('is-open');
                digitalProductionCostIdToDelete = null;
            }

            document.addEventListener('click', function(event) {
                const deleteDigitalButton = event.target.closest('.btn-delete-digital-production-cost');

                if (!deleteDigitalButton || !deleteDigitalButton.dataset.id) {
                    return;
                }

                const vendorName = deleteDigitalButton.dataset.vendor;

                digitalProductionCostIdToDelete = deleteDigitalButton.dataset.id;

                document.getElementById('deleteDigitalLocation').textContent =
                    deleteDigitalButton.dataset.location + (vendorName ? ' - ' + vendorName : '');
                document.getElementById('deleteDigitalMaterialCount').textContent =
                    deleteDigitalButton.dataset.materialCount;

                modalDeleteDigital.classList.add('is-open');
            });

            document.getElementById('btnTutupDeleteDigitalProductionCost')
                .addEventListener('click', closeDeleteDigitalModal);
            document.getElementById('btnBatalDeleteDigitalProductionCost')
                .addEventListener('click', closeDeleteDigitalModal);

            modalDeleteDigital.addEventListener('click', function(event) {
                if (event.target === modalDeleteDigital) {
                    closeDeleteDigitalModal();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modalDeleteDigital.classList.contains('is-open')) {
                    closeDeleteDigitalModal();
                }
            });

            btnConfirmDeleteDigital.addEventListener('click', async function() {
                if (!digitalProductionCostIdToDelete) {
                    return;
                }

                const originalText = btnConfirmDeleteDigital.innerHTML;

                btnConfirmDeleteDigital.disabled = true;
                btnConfirmDeleteDigital.innerHTML = 'Menghapus...';

                try {
                    const response = await fetch(
                        "{{ route('admin_delete_pc_finishing_digital', ':id') }}"
                        .replace(':id', btoa(String(digitalProductionCostIdToDelete))), {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        }
                    );

                    const result = await response.json();

                    btnConfirmDeleteDigital.disabled = false;

                    if (response.ok && result.success) {
                        closeDeleteDigitalModal();
                        productionCostTable.ajax.reload(null, false);
                        showToast('success', result.message);
                        return;
                    }

                    showToast(
                        'error',
                        result.message || 'Production cost Digital Print A3+ gagal dihapus.'
                    );
                } catch (error) {
                    console.error(error);

                    showToast('error', 'Terjadi kesalahan saat menghapus production cost Digital Print A3+.');
                } finally {
                    btnConfirmDeleteDigital.disabled = false;
                    btnConfirmDeleteDigital.innerHTML = originalText;
                }
            });

        });

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | DELETE PRODUCTION COST
            |--------------------------------------------------------------------------
            */

            let productionCostIdToDelete = null;

            const modalDeleteProductionCost = document.getElementById(
                'modalDeleteProductionCost'
            );

            const btnTutupDeleteProductionCost = document.getElementById(
                'btnTutupDeleteProductionCost'
            );

            const btnBatalDeleteProductionCost = document.getElementById(
                'btnBatalDeleteProductionCost'
            );

            const btnConfirmDeleteProductionCost = document.getElementById(
                'btnConfirmDeleteProductionCost'
            );


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL DELETE
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const deleteButton = event.target.closest(
                    '.btn-delete-production-cost'
                );

                if (!deleteButton) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | AMBIL ENGINE ID
                |--------------------------------------------------------------------------
                */

                productionCostIdToDelete = deleteButton.dataset.id;

                modalDeleteProductionCost.classList.add('is-open');

            });


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupDeleteProductionCostModal() {

                modalDeleteProductionCost.classList.remove('is-open');

                productionCostIdToDelete = null;

            }


            btnTutupDeleteProductionCost.addEventListener(
                'click',
                tutupDeleteProductionCostModal
            );


            btnBatalDeleteProductionCost.addEventListener(
                'click',
                tutupDeleteProductionCostModal
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modalDeleteProductionCost.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalDeleteProductionCost) {

                        tutupDeleteProductionCostModal();

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
                        modalDeleteProductionCost.classList.contains('is-open')
                    ) {

                        tutupDeleteProductionCostModal();

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI HAPUS PRODUCTION COST
            |--------------------------------------------------------------------------
            */

            btnConfirmDeleteProductionCost.addEventListener(
                'click',
                async function() {

                    /*
                    |--------------------------------------------------------------------------
                    | CEK ENGINE ID
                    |--------------------------------------------------------------------------
                    */

                    if (!productionCostIdToDelete) {
                        return;
                    }


                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | REQUEST DELETE
                        |--------------------------------------------------------------------------
                        */

                        const response = await fetch(
                            `/admin/production-cost-finishings/${productionCostIdToDelete}/delete`, {
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

                        if (response.ok && result.success) {

                            /*
                            |--------------------------------------------------------------------------
                            | TUTUP MODAL
                            |--------------------------------------------------------------------------
                            */

                            tutupDeleteProductionCostModal();


                            /*
                            |--------------------------------------------------------------------------
                            | RELOAD DATATABLE
                            |--------------------------------------------------------------------------
                            */

                            productionCostTable.ajax.reload(
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
                                'Production cost gagal dihapus.'
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
                            'Terjadi kesalahan saat menghapus production cost.'
                        );

                    }

                }
            );

        });

        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(event) {
                const editButton = event.target.closest('.btn-edit');

                if (!editButton) {
                    return;
                }

                event.preventDefault();

                const engineId = editButton.dataset.engineId;

                if (!engineId) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | ENCODE ID
                |--------------------------------------------------------------------------
                */

                const encodedId = btoa(String(engineId));

                /*
                |--------------------------------------------------------------------------
                | URL EDIT
                |--------------------------------------------------------------------------
                */

                const url = "{{ route('admin_edit_pc_finishing', ':id') }}"
                    .replace(':id', encodedId);

                /*
                |--------------------------------------------------------------------------
                | PINDAH KE HALAMAN EDIT
                |--------------------------------------------------------------------------
                */

                window.location.href = url;
            });

            document.addEventListener('click', function(event) {
                const detailsButton = event.target.closest('.btn-detail');

                if (!detailsButton) {
                    return;
                }

                event.preventDefault();

                const engineId = detailsButton.dataset.engineId;

                if (!engineId) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | ENCODE ID
                |--------------------------------------------------------------------------
                */

                const encodedId = btoa(String(engineId));

                /*
                |--------------------------------------------------------------------------
                | URL EDIT
                |--------------------------------------------------------------------------
                */

                const url = "{{ route('admin_pc_finishing_details', ':id') }}"
                    .replace(':id', encodedId);

                /*
                |--------------------------------------------------------------------------
                | PINDAH KE HALAMAN EDIT
                |--------------------------------------------------------------------------
                */

                window.location.href = url;
            });

        })

        document.addEventListener('DOMContentLoaded', function() {
            /* =========================
               DOWNLOAD TEMPLATE
            ========================= */

            const btnDownloadTemplate = document.getElementById(
                'btnDownloadTemplateFinishingService'
            );

            const modalDownloadTemplate = document.getElementById(
                'modalDownloadTemplateFinishingService'
            );

            const btnCloseDownloadTemplate = document.getElementById(
                'btnCloseDownloadTemplateFinishingService'
            );

            const btnCancelDownloadTemplate = document.getElementById(
                'btnCancelDownloadTemplateFinishingService'
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
        })

        // =========================================================
        // TOAST NOTIFICATION
        // =========================================================

        @if (session('success'))

            showToast(
                'success',
                @json(session('success'))
            );
        @endif


        @if (session('error'))

            showToast(
                'error',
                @json(session('error'))
            );
        @endif
    </script>
@endsection

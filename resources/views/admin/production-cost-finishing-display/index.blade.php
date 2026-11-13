@extends('admin_master')

@section('contents')
    <link rel="stylesheet" href="{{ asset('css/production-cost-finishing-index.css') }}">
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero cat-hero-compact">
                    <div class="hero-text">
                        <span class="eyebrow">BIAYA · DISPLAY</span>
                        <p class="hero-sub">Kelola biaya material dan finishing yang digunakan sebagai dasar perhitungan
                            biaya serta estimasi harga produk.</p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card cat-card-container">
                        <div class="cat-card-header">
                            <div class="cat-card-header-main">
                                <div class="cat-card-title-wrap">
                                    <div class="cat-card-icon"><i class="bi bi-easel2-fill"></i></div>
                                    <div>
                                        <div class="cat-title-row">
                                            <h3 class="cat-card-title">Daftar Biaya Display</h3>
                                            <span class="cat-badge-total">
                                                <span class="cat-pulse-dot"></span>
                                                <span id="displayProductTotalCount">Memuat data...</span>
                                            </span>
                                        </div>
                                        <p class="cat-card-desc">Konfigurasi biaya display berdasarkan produk, kategori, lokasi, dan komponen.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="cat-card-actions">
                                <a href="{{ route('admin_create_pc_finishing_display') }}" class="cat-btn-head-primary"
                                    id="btnTambahOngkosDisplay">
                                    <i class="bi bi-plus-lg"></i><span>Tambah Ongkos</span>
                                </a>
                            </div>
                        </div>

                        <div class="cat-bulk-toolbar" id="displayProductBulkToolbar" style="display:none;">
                            <div class="cat-bulk-info">
                                <span class="cat-bulk-badge" id="displayProductBulkCount">0 terpilih</span>
                                <span class="cat-bulk-text">Biaya display terpilih siap untuk tindakan massal</span>
                            </div>
                            <div class="cat-bulk-actions">
                                <button type="button" class="btn btn--ghost btn--sm" id="cancelDisplayProductSelection">
                                    <i class="bi bi-x-circle me-1"></i>Batal Pilihan
                                </button>
                                <button type="button" class="btn btn--danger btn--sm" id="bulkDeleteDisplayProducts">
                                    <i class="bi bi-trash3-fill me-1"></i>Hapus Terpilih
                                </button>
                            </div>
                        </div>

                        <div class="cat-filter-card">
                            <div class="cat-filter-group-left">
                                <div class="cat-search-box">
                                    <i class="bi bi-search cat-search-icon"></i>
                                    <input type="text" id="displayProductSearch" class="cat-filter-input"
                                        placeholder="Cari display, kategori, lokasi, mesin, atau ukuran...">
                                    <button type="button" id="clearDisplayProductSearch" class="cat-clear-btn"
                                        title="Hapus pencarian" style="display:none;">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                                <div class="cat-select-box">
                                    <i class="bi bi-folder-fill cat-select-icon"></i>
                                    <select id="displayProductCategoryFilter" class="select2" style="width:100%;"
                                        aria-label="Filter kategori">
                                        <option value="all">Semua Kategori</option>
                                        @if (isset($filterCategories))
                                            @foreach ($filterCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="cat-filter-subgroup">
                                    <div class="cat-select-box">
                                        <i class="bi bi-geo-alt-fill cat-select-icon"></i>
                                        <select id="displayProductLocationFilter" class="select2" style="width:100%;"
                                            aria-label="Filter lokasi">
                                            <option value="all">Semua Lokasi</option>
                                            @if (isset($filterLocations))
                                                @foreach ($filterLocations as $location)
                                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <button type="button" id="resetDisplayProductFilters" class="cat-btn-reset"
                                        title="Reset semua filter">
                                        <i class="bi bi-arrow-counterclockwise"></i><span>Reset</span>
                                    </button>
                                </div>
                            </div>
                            <div class="cat-filter-group-right">
                                <div class="cat-length-box">
                                    <span class="cat-length-label">Baris:</span>
                                    <select id="displayProductPageLength" class="select2" style="width:76px;">
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="cat-table-wrap">
                            <table id="displayProductTable" class="cat-modern-table" style="width:100%"
                                data-url="{{ route('admin_data_pc_finishing_display') }}"
                                data-bulk-url="{{ route('admin_bulk_destroy_pc_finishing_displays') }}">
                                <thead>
                                    <tr>
                                        <th class="cat-th-check text-center">
                                            <label class="cat-custom-checkbox" title="Pilih Semua di Halaman Ini">
                                                <input type="checkbox" id="checkAllDisplayProducts"
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
                                        <th class="cat-th-name" style="min-width:220px;">Nama Display</th>
                                        <th class="cat-th-date text-center" style="min-width:140px;">Kategori</th>
                                        <th class="cat-th-creator" style="min-width:180px;">Lokasi / Mesin</th>
                                        <th class="cat-th-date text-center" style="min-width:140px;">Ukuran</th>
                                        <th class="cat-th-date text-center" style="min-width:170px;">Komponen</th>
                                        <th class="cat-th-actions text-center" style="width:160px;">Aksi</th>
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

    <div class="modal-overlay" id="modalBulkDeleteDisplayProducts">
        <div class="modal-dialog modal-delete">
            <div class="modal-header">
                <div>
                    <span class="eyebrow">PRODUCTION COST · DISPLAY</span><br>
                    <span class="eyebrow">Hapus Pilihan</span>
                </div>
                <button type="button" class="modal-close" id="closeBulkDeleteDisplayProducts" aria-label="Tutup">
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
                        <h3>Hapus biaya display terpilih?</h3>
                        <p id="bulkDeleteDisplayProductDescription"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--ghost" id="cancelBulkDeleteDisplayProducts">Batal</button>
                <button type="button" class="btn btn--danger" id="confirmBulkDeleteDisplayProducts">
                    <i class="bi bi-trash3-fill"></i> Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL DELETE DISPLAY PRODUCT -->
    <div class="modal-overlay" id="modalDeleteDisplay">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        PRODUCTION COST · DISPLAY
                    </span>

                    <br>

                    <span class="eyebrow">
                        Hapus Display Product
                    </span>

                </div>

                <button type="button" class="modal-close" id="btnTutupDeleteDisplay" aria-label="Tutup">

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
                            Hapus Display Product?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus Display Product ini?
                            Data konfigurasi, komponen, harga, dan gambar yang
                            terkait juga akan dihapus.

                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteDisplay">
                    Batal
                </button>

                <button type="button" class="btn btn--danger" id="btnConfirmDeleteDisplay">

                    <i class="bi bi-trash3-fill"></i>

                    Hapus

                </button>

            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script>
        let displayProductTable;
        const selectedDisplayProducts = new Set();

        document.addEventListener('DOMContentLoaded', function() {
            const escapeHtml = DataTable.render.text().display;
            const tableElement = document.getElementById('displayProductTable');
            const tableWrap = tableElement.closest('.cat-table-wrap');
            const categoryFilter = document.getElementById('displayProductCategoryFilter');
            const locationFilter = document.getElementById('displayProductLocationFilter');
            const searchInput = document.getElementById('displayProductSearch');
            const clearSearchButton = document.getElementById('clearDisplayProductSearch');
            const pageLength = document.getElementById('displayProductPageLength');
            const bulkToolbar = document.getElementById('displayProductBulkToolbar');
            const bulkCount = document.getElementById('displayProductBulkCount');
            const checkAll = document.getElementById('checkAllDisplayProducts');
            const checkAllLabel = checkAll.closest('.cat-custom-checkbox');
            const maxSelected = 100;

            function formatSize(value) {
                const number = Number(value);

                if (!Number.isFinite(number)) {
                    return '-';
                }

                return number.toLocaleString('id-ID', {
                    maximumFractionDigits: 2
                });
            }

            function updateSelectionUI() {
                const selectedCount = selectedDisplayProducts.size;
                bulkToolbar.style.display = selectedCount ? 'flex' : 'none';
                bulkCount.textContent = `${selectedCount} terpilih`;

                const checkboxes = document.querySelectorAll('.display-product-row-check');
                let checkedCount = 0;
                checkboxes.forEach((checkbox) => {
                    const selected = selectedDisplayProducts.has(Number(checkbox.value));
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

            $('#displayProductCategoryFilter, #displayProductLocationFilter')
                .select2({ width: '100%', minimumResultsForSearch: Infinity });
            $('#displayProductPageLength').select2({ width: '76px', minimumResultsForSearch: -1 });

            $('#displayProductTable').on('processing.dt', centerProcessingIndicator);

            /*
            |--------------------------------------------------------------------------
            | DATATABLE INITIALIZATION
            |--------------------------------------------------------------------------
            */
            displayProductTable = new DataTable('#displayProductTable', {
                processing: true,
                serverSide: true,
                ordering: true,
                order: [[2, 'asc']],

                ajax: {
                    url: tableElement.dataset.url,
                    type: 'GET',
                    data: function(d) {
                        d.category_id = categoryFilter.value === 'all' ? '' : categoryFilter.value;
                        d.location_id = locationFilter.value === 'all' ? '' : locationFilter.value;
                    },
                },

                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center cat-td-check',
                        render: function(data, type, row) {
                            const id = Number(row.display_product_id);
                            const safeName = escapeHtml(row.display_name || '');
                            return `
                                <label class="cat-custom-checkbox">
                                    <input type="checkbox"
                                        class="display-product-row-check"
                                        value="${id}"
                                        ${selectedDisplayProducts.has(id) ? 'checked' : ''}
                                        aria-label="Pilih biaya display ${safeName}">
                                    <span class="cat-checkbox-mark">
                                        <svg class="check-icon" viewBox="0 0 14 14" fill="none">
                                            <path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </label>
                            `;
                        },
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center cat-td-no',
                        render: function(data, type, row, meta) {
                            const pageInfo = displayProductTable.page.info();
                            return `<span class="cat-num-pill">${pageInfo.start + meta.row + 1}</span>`;
                        },
                    },
                    {
                        data: 'display_name',
                        defaultContent: '-',
                        className: 'cat-td-name',
                        render: function(data, type) {
                            if (type !== 'display') {
                                return data || '';
                            }
                            return `
                                <div class="cat-cell-name-box">
                                    <div class="cat-name-details">
                                        <span class="cat-name-primary">${escapeHtml(data || '-')}</span>
                                        <span class="cat-name-badge">Biaya Display</span>
                                    </div>
                                </div>
                            `;
                        },
                    },
                    {
                        data: 'category_name',
                        defaultContent: '-',
                        className: 'text-center cat-td-date',
                        render: function(data, type) {
                            if (type !== 'display') {
                                return data || '';
                            }
                            return `
                                <span class="cat-date-chip">
                                    <span>${escapeHtml(data || '-')}</span>
                                </span>
                            `;
                        },
                    },
                    {
                        data: 'location_name',
                        defaultContent: '-',
                        className: 'cat-td-creator',
                        render: function(data, type, row) {
                            if (type !== 'display') {
                                return data || '';
                            }
                            return `
                                <div class="cat-user-badge">
                                    <div class="cat-user-info">
                                        <span class="cat-user-name">${escapeHtml(data || '-')}</span>
                                        <span class="cat-user-role">${escapeHtml(row.engine_name || '-')}</span>
                                    </div>
                                </div>
                            `;
                        },
                    },
                    {
                        data: null,
                        className: 'text-center cat-td-date',
                        render: function(data, type, row) {
                            const size = `${formatSize(row.width)} x ${formatSize(row.length)} cm`;
                            if (type !== 'display') {
                                return size;
                            }
                            return `
                                <span class="cat-date-chip">
                                    <span>${escapeHtml(size)}</span>
                                </span>
                            `;
                        },
                    },
                    {
                        data: null,
                        className: 'text-center cat-td-date',
                        render: function(data, type, row) {
                            const materialCount = Number(row.material_count) || 0;
                            const laminationCount = Number(row.lamination_count) || 0;
                            if (type !== 'display') {
                                return materialCount + laminationCount;
                            }
                            if (materialCount + laminationCount === 0) {
                                return `
                                    <span class="cat-date-chip">
                                        <span>Belum ada komponen</span>
                                    </span>
                                `;
                            }

                            const parts = [];
                            if (materialCount > 0) {
                                parts.push(`<span class="cat-date-chip"><span>${materialCount.toLocaleString('id-ID')} Material</span></span>`);
                            }
                            if (laminationCount > 0) {
                                parts.push(`<span class="cat-date-chip"><span>${laminationCount.toLocaleString('id-ID')} Laminasi</span></span>`);
                            }
                            return `<div style="display:flex; flex-wrap:wrap; justify-content:center; gap:5px;">${parts.join('')}</div>`;
                        },
                    },
                    {
                        data: 'display_product_id',
                        orderable: false,
                        searchable: false,
                        className: 'text-center cat-td-actions',
                        render: function(data) {
                            const id = encodeURIComponent(data);
                            return `
                                <div class="cat-action-group">
                                    <button type="button" class="cat-btn-action cat-btn-view btn-details-display" data-id="${id}"
                                        aria-label="View" title="Detail biaya display">
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </button>
                                    <button type="button" class="cat-btn-action cat-btn-add btn-add-display-component" data-id="${id}"
                                        aria-label="Tambah komponen" title="Tambah komponen display">
                                        <i class="bi bi-folder-plus"></i>
                                    </button>
                                    <button type="button" class="cat-btn-action cat-btn-edit btn-edit-display" data-id="${id}"
                                        aria-label="Edit" title="Edit biaya display">
                                        <i class="bi bi-pen"></i>
                                    </button>
                                    <button type="button" class="cat-btn-action cat-btn-del btn-delete-display" data-id="${id}"
                                        aria-label="Hapus" title="Hapus biaya display">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            `;
                        },
                    },
                ],

                pageLength: 10,

                layout: {
                    topStart: null,
                    topEnd: null,
                    bottomStart: 'info',
                    bottomEnd: 'paging',
                },

                language: {
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ biaya display',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 biaya display',
                    zeroRecords: 'Data biaya display tidak ditemukan',
                    processing: 'Memuat data...',
                },

                drawCallback: function() {
                    centerProcessingIndicator();
                },
            });
            centerProcessingIndicator();

            displayProductTable.on('draw', function() {
                const info = displayProductTable.page.info();
                const totalEl = document.getElementById('displayProductTotalCount');
                if (totalEl) {
                    totalEl.textContent = `${info.recordsTotal.toLocaleString('id-ID')} Total Biaya Display`;
                }
                updateSelectionUI();
            });

            /*
            |--------------------------------------------------------------------------
            | FILTER, PENCARIAN & JUMLAH BARIS
            |--------------------------------------------------------------------------
            */
            let searchTimer;
            searchInput.addEventListener('input', function() {
                const query = searchInput.value.trim();
                clearSearchButton.style.display = query.length > 0 ? 'flex' : 'none';
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => displayProductTable.search(query).draw(), 250);
            });
            clearSearchButton.addEventListener('click', function() {
                searchInput.value = '';
                clearSearchButton.style.display = 'none';
                displayProductTable.search('').draw();
                searchInput.focus();
            });
            $('#displayProductCategoryFilter, #displayProductLocationFilter').on('change', function() {
                displayProductTable.ajax.reload(null, true);
            });
            $('#displayProductPageLength').on('change', function() {
                displayProductTable.page.len(Number(pageLength.value)).draw();
            });
            document.getElementById('resetDisplayProductFilters').addEventListener('click', function() {
                clearTimeout(searchTimer);
                searchInput.value = '';
                clearSearchButton.style.display = 'none';
                $('#displayProductCategoryFilter').val('all').trigger('change.select2');
                $('#displayProductLocationFilter').val('all').trigger('change.select2');
                $('#displayProductPageLength').val('10').trigger('change.select2');
                selectedDisplayProducts.clear();
                displayProductTable.search('').order([[2, 'asc']]).page.len(10).ajax.reload(null, true);
            });

            /*
            |--------------------------------------------------------------------------
            | CHECKBOX SELECTION
            |--------------------------------------------------------------------------
            */
            $('#displayProductTable').on('change', '.display-product-row-check', function() {
                const id = Number(this.value);
                if (this.checked && !selectedDisplayProducts.has(id) && selectedDisplayProducts.size >= maxSelected) {
                    this.checked = false;
                    showToast('error', `Maksimal ${maxSelected} biaya display dapat dipilih sekaligus.`);
                    return;
                }
                if (this.checked) selectedDisplayProducts.add(id);
                else selectedDisplayProducts.delete(id);
                updateSelectionUI();
            });
            checkAll.addEventListener('change', function() {
                let reachedLimit = false;
                document.querySelectorAll('.display-product-row-check').forEach((checkbox) => {
                    const id = Number(checkbox.value);
                    if (!checkAll.checked) {
                        selectedDisplayProducts.delete(id);
                    } else if (!selectedDisplayProducts.has(id)) {
                        if (selectedDisplayProducts.size >= maxSelected) reachedLimit = true;
                        else selectedDisplayProducts.add(id);
                    }
                });
                updateSelectionUI();
                if (reachedLimit) showToast('error', `Maksimal ${maxSelected} biaya display dapat dipilih sekaligus.`);
            });
            document.getElementById('cancelDisplayProductSelection').addEventListener('click', function() {
                selectedDisplayProducts.clear();
                updateSelectionUI();
            });

            /*
            |--------------------------------------------------------------------------
            | BULK DELETE
            |--------------------------------------------------------------------------
            */
            const bulkModal = document.getElementById('modalBulkDeleteDisplayProducts');
            const confirmBulkButton = document.getElementById('confirmBulkDeleteDisplayProducts');
            let isBulkDeleting = false;
            const closeBulkModal = () => {
                if (!isBulkDeleting) bulkModal.classList.remove('is-open');
            };
            ['closeBulkDeleteDisplayProducts', 'cancelBulkDeleteDisplayProducts'].forEach((id) => {
                document.getElementById(id).addEventListener('click', closeBulkModal);
            });
            bulkModal.addEventListener('click', (event) => {
                if (event.target === bulkModal) closeBulkModal();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && bulkModal.classList.contains('is-open')) closeBulkModal();
            });
            document.getElementById('bulkDeleteDisplayProducts').addEventListener('click', function() {
                const count = selectedDisplayProducts.size;
                if (!count) return;
                document.getElementById('bulkDeleteDisplayProductDescription').textContent =
                    `${count} biaya display terpilih beserta data konfigurasi, komponen, dan harga yang terkait akan dihapus. Master produk display, kategori, lokasi, dan mesin tidak ikut dihapus. Data yang sudah dihapus tidak dapat dikembalikan.`;
                bulkModal.classList.add('is-open');
            });
            confirmBulkButton.addEventListener('click', async function() {
                if (isBulkDeleting || !selectedDisplayProducts.size) return;
                isBulkDeleting = true;
                confirmBulkButton.disabled = true;
                const originalContent = confirmBulkButton.innerHTML;
                confirmBulkButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Menghapus...
                `;
                const payload = new FormData();
                selectedDisplayProducts.forEach((id) => payload.append('ids[]', id));

                try {
                    const response = await fetch(tableElement.dataset.bulkUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                                document.querySelector('input[name="_token"]')?.value || '',
                        },
                        body: payload,
                    });
                    const result = await response.json();
                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Biaya display gagal dihapus.');
                    }

                    selectedDisplayProducts.clear();
                    isBulkDeleting = false;
                    closeBulkModal();
                    displayProductTable.ajax.reload(null, false);
                    showToast('success', result.message);
                } catch (error) {
                    showToast('error', error.message || 'Terjadi kesalahan saat menghapus biaya display.');
                } finally {
                    isBulkDeleting = false;
                    confirmBulkButton.disabled = false;
                    confirmBulkButton.innerHTML = originalContent;
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            /*
            |--------------------------------------------------------------------------
            | DELETE DISPLAY PRODUCT
            |--------------------------------------------------------------------------
            */

            let displayProductIdToDelete = null;

            const modalDeleteDisplay = document.getElementById(
                'modalDeleteDisplay'
            );

            const btnTutupDeleteDisplay = document.getElementById(
                'btnTutupDeleteDisplay'
            );

            const btnBatalDeleteDisplay = document.getElementById(
                'btnBatalDeleteDisplay'
            );

            const btnConfirmDeleteDisplay = document.getElementById(
                'btnConfirmDeleteDisplay'
            );

            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL DELETE
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const deleteButton = event.target.closest(
                    '.btn-delete-display'
                );

                if (!deleteButton) {
                    return;
                }

                displayProductIdToDelete = deleteButton.dataset.id;

                modalDeleteDisplay.classList.add('is-open');

            });

            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupDeleteDisplayModal() {

                modalDeleteDisplay.classList.remove('is-open');

                displayProductIdToDelete = null;

                btnConfirmDeleteDisplay.disabled = false;

                btnConfirmDeleteDisplay.innerHTML =
                    '<i class="bi bi-trash3-fill"></i> Hapus';

            }

            /*
            |--------------------------------------------------------------------------
            | TOMBOL TUTUP DAN BATAL
            |--------------------------------------------------------------------------
            */

            btnTutupDeleteDisplay.addEventListener(
                'click',
                tutupDeleteDisplayModal
            );

            btnBatalDeleteDisplay.addEventListener(
                'click',
                tutupDeleteDisplayModal
            );

            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modalDeleteDisplay.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalDeleteDisplay) {

                        tutupDeleteDisplayModal();

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
                        modalDeleteDisplay.classList.contains('is-open')
                    ) {

                        tutupDeleteDisplayModal();

                    }

                }
            );

            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI HAPUS DISPLAY PRODUCT
            |--------------------------------------------------------------------------
            */

            btnConfirmDeleteDisplay.addEventListener(
                'click',
                async function() {

                    /*
                    |--------------------------------------------------------------------------
                    | CEK ID DISPLAY PRODUCT
                    |--------------------------------------------------------------------------
                    */

                    if (!displayProductIdToDelete) {
                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CEGAH DOUBLE CLICK
                    |--------------------------------------------------------------------------
                    */

                    btnConfirmDeleteDisplay.disabled = true;

                    btnConfirmDeleteDisplay.textContent = 'Menghapus...';

                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | REQUEST DELETE
                        |--------------------------------------------------------------------------
                        */

                        const response = await fetch(
                            `/admin/production-cost-finishing-displays/${displayProductIdToDelete}/delete`, {
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

                            selectedDisplayProducts.delete(
                                Number(decodeURIComponent(displayProductIdToDelete))
                            );

                            /*
                            |--------------------------------------------------------------------------
                            | TUTUP MODAL
                            |--------------------------------------------------------------------------
                            */

                            tutupDeleteDisplayModal();

                            /*
                            |--------------------------------------------------------------------------
                            | RELOAD DATATABLE
                            |--------------------------------------------------------------------------
                            */

                            displayProductTable.ajax.reload(
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
                                'Display Product gagal dihapus.'
                            );

                            btnConfirmDeleteDisplay.disabled = false;

                            btnConfirmDeleteDisplay.innerHTML =
                                '<i class="bi bi-trash3-fill"></i> Hapus';

                        }

                    } catch (error) {

                        console.error(error);

                        showToast(
                            'error',
                            'Terjadi kesalahan saat menghapus Display Product.'
                        );

                        btnConfirmDeleteDisplay.disabled = false;

                        btnConfirmDeleteDisplay.innerHTML =
                            '<i class="bi bi-trash3-fill"></i> Hapus';

                    }

                }
            );

        })

        document.addEventListener('DOMContentLoaded', function() {
            /*
            |--------------------------------------------------------------------------
            | NAVIGASI TAMBAH KOMPONEN
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const addComponentButton = event.target.closest(
                    '.btn-add-display-component'
                );

                if (!addComponentButton) {
                    return;
                }

                const configurationId =
                    addComponentButton.dataset.id;

                if (!configurationId) {

                    showToast(
                        'error',
                        'ID Header Display tidak ditemukan.'
                    );

                    return;
                }

                const encodedId = btoa(configurationId);

                window.location.href =
                    `/admin/production-cost-finishing-displays/${encodedId}/add-component`;

            });
        });

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | NAVIGASI DETAILS KONFIGURASI DISPLAY
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const detailsButton = event.target.closest(
                    '.btn-details-display'
                );

                if (!detailsButton) {
                    return;
                }

                const configurationId = detailsButton.dataset.id;

                if (!configurationId) {

                    showToast(
                        'error',
                        'ID Konfigurasi Display tidak ditemukan.'
                    );

                    return;
                }

                const encodedId = btoa(
                    decodeURIComponent(configurationId)
                );

                window.location.href =
                    `/admin/production-cost-finishing-displays/${encodedId}/details`;

            });

        });

        document.addEventListener('DOMContentLoaded', function() {
            // =========================================
            // EDIT DISPLAY PRODUCT
            // =========================================
            $(document).on('click', '.btn-edit-display', function() {
                const id = $(this).data('id');

                if (!id) {
                    showToast('error', 'ID Display Product tidak ditemukan.');
                    return;
                }

                const encodedId = btoa(
                    decodeURIComponent(id)
                );

                const url = "{{ route('admin_edit_component_pc_finishing_display', ':id') }}"
                    .replace(':id', encodeURIComponent(encodedId));

                window.location.href = url;
            });
        })
    </script>
@endsection

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
                        <span class="eyebrow">BIAYA · JASA POTONG</span>
                        <p class="hero-sub">Kelola biaya jasa potong (cutting) yang digunakan sebagai dasar perhitungan
                            biaya serta estimasi harga produk.</p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card cat-card-container">
                        <div class="cat-card-header">
                            <div class="cat-card-header-main">
                                <div class="cat-card-title-wrap">
                                    <div class="cat-card-icon"><i class="bi bi-scissors"></i></div>
                                    <div>
                                        <div class="cat-title-row">
                                            <h3 class="cat-card-title">Daftar Biaya Jasa Potong</h3>
                                            <span class="cat-badge-total">
                                                <span class="cat-pulse-dot"></span>
                                                <span id="productionCostCuttingTotalCount">Memuat data...</span>
                                            </span>
                                        </div>
                                        <p class="cat-card-desc">Konfigurasi biaya jasa potong berdasarkan mesin, lokasi, dan kategori.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="cat-card-actions">
                                <a href="{{ route('admin_create_pc_finishing_cutting') }}" class="cat-btn-head-primary"
                                    id="btnTambahOngkos">
                                    <i class="bi bi-plus-lg"></i><span id="labelTambahOngkos">Tambah Ongkos</span>
                                </a>
                            </div>
                        </div>

                        <div class="cat-bulk-toolbar" id="productionCostCuttingBulkToolbar" style="display:none;">
                            <div class="cat-bulk-info">
                                <span class="cat-bulk-badge" id="productionCostCuttingBulkCount">0 terpilih</span>
                                <span class="cat-bulk-text">Biaya jasa potong terpilih siap untuk tindakan massal</span>
                            </div>
                            <div class="cat-bulk-actions">
                                <button type="button" class="btn btn--ghost btn--sm" id="cancelProductionCostCuttingSelection">
                                    <i class="bi bi-x-circle me-1"></i>Batal Pilihan
                                </button>
                                <button type="button" class="btn btn--danger btn--sm" id="bulkDeleteProductionCostCuttings">
                                    <i class="bi bi-trash3-fill me-1"></i>Hapus Terpilih
                                </button>
                            </div>
                        </div>

                        <div class="cat-filter-card">
                            <div class="cat-filter-group-left">
                                <div class="cat-search-box">
                                    <i class="bi bi-search cat-search-icon"></i>
                                    <input type="text" id="productionCostCuttingSearch" class="cat-filter-input"
                                        placeholder="Cari mesin, lokasi, kategori, atau vendor...">
                                    <button type="button" id="clearProductionCostCuttingSearch" class="cat-clear-btn"
                                        title="Hapus pencarian" style="display:none;">
                                        <i class="bi bi-x-circle-fill"></i>
                                    </button>
                                </div>
                                <div class="cat-select-box">
                                    <i class="bi bi-scissors cat-select-icon"></i>
                                    <select id="productionCostCuttingTypeFilter" class="select2" style="width:100%;"
                                        aria-label="Filter jenis ongkos potong">
                                        <option value="cutting" selected>Jasa Potong (Cutting / Selain Potong)</option>
                                        <option value="potong">Potong (Guillotine / Ongkos Minimal)</option>
                                    </select>
                                </div>
                                <div class="cat-filter-subgroup">
                                    <div class="cat-select-box">
                                        <i class="bi bi-geo-alt-fill cat-select-icon"></i>
                                        <select id="productionCostCuttingLocationFilter" class="select2" style="width:100%;"
                                            aria-label="Filter lokasi">
                                            <option value="all">Semua Lokasi</option>
                                            @if (isset($filterLocations))
                                                @foreach ($filterLocations as $location)
                                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <button type="button" id="resetProductionCostCuttingFilters" class="cat-btn-reset"
                                        title="Reset semua filter">
                                        <i class="bi bi-arrow-counterclockwise"></i><span>Reset</span>
                                    </button>
                                </div>
                            </div>
                            <div class="cat-filter-group-right">
                                <div class="cat-length-box">
                                    <span class="cat-length-label">Baris:</span>
                                    <select id="productionCostCuttingPageLength" class="select2" style="width:76px;">
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="cat-table-wrap">
                            <table id="productionCostCuttingTable" class="cat-modern-table" style="width:100%"
                                data-url="{{ route('admin_data_pc_finishing_cuttings') }}"
                                data-bulk-url="{{ route('admin_bulk_destroy_pc_finishing_cuttings') }}">
                                <thead>
                                    <tr>
                                        <th class="cat-th-check text-center">
                                            <label class="cat-custom-checkbox" title="Pilih Semua di Halaman Ini">
                                                <input type="checkbox" id="checkAllProductionCostCuttings"
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
                                        <th class="cat-th-date text-center" style="min-width:150px;">Kategori</th>
                                        <th class="cat-th-date text-center" style="min-width:170px;" id="thCuttingOrPotong">Jasa Potong</th>
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

    <div class="modal-overlay" id="modalBulkDeleteProductionCostCuttings">
        <div class="modal-dialog modal-delete">
            <div class="modal-header">
                <div>
                    <span class="eyebrow">KONFIGURASI · BIAYA JASA POTONG</span><br>
                    <span class="eyebrow">Hapus Pilihan</span>
                </div>
                <button type="button" class="modal-close" id="closeBulkDeleteProductionCostCuttings" aria-label="Tutup">
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
                        <h3>Hapus biaya jasa potong terpilih?</h3>
                        <p id="bulkDeleteProductionCostCuttingDescription"></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--ghost" id="cancelBulkDeleteProductionCostCuttings">Batal</button>
                <button type="button" class="btn btn--danger" id="confirmBulkDeleteProductionCostCuttings">
                    <i class="bi bi-trash3-fill"></i> Hapus
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL DELETE PRODUCTION COST JASA POTONG -->
    <div class="modal-overlay" id="modalDeleteProductionCostCutting">
        <div class="modal-dialog modal-delete">
            <!-- HEADER -->
            <div class="modal-header">
                <div>
                    <span class="eyebrow" id="digitalCuttingDeleteEyebrow">
                        HARGA & TARIF · PRODUCTION COST JASA POTONG
                    </span>
                    <br>
                    <span class="eyebrow" id="digitalCuttingDeleteTitle">
                        Hapus Production Cost Jasa Potong
                    </span>
                </div>
                <button type="button" class="modal-close" id="btnTutupDeleteProductionCostCutting" aria-label="Tutup">
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
                        <h3 id="digitalCuttingDeleteHeading">
                            Hapus production cost jasa potong?
                        </h3>
                        <p id="digitalCuttingDeleteDescription">
                            Hapus konfigurasi ongkos jasa potong untuk
                            <strong id="digitalCuttingDeleteScope"></strong> beserta seluruh kategori, detail jasa potong,
                            dan harga tier pada baris ini? Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
                <button type="button" class="btn btn--ghost" id="btnBatalDeleteProductionCostCutting">
                    Batal
                </button>
                <button type="button" class="btn btn--danger" id="btnConfirmDeleteProductionCostCutting">
                    <i class="bi bi-trash3-fill"></i>
                    Hapus
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let productionCostCuttingTable;
        const selectedCuttingCosts = new Set();
        const selectedPotongCosts = new Set();

        document.addEventListener('DOMContentLoaded', function() {
            const escapeHtml = DataTable.render.text().display;
            const tableElement = document.getElementById('productionCostCuttingTable');
            const tableWrap = tableElement.closest('.cat-table-wrap');
            const typeFilter = document.getElementById('productionCostCuttingTypeFilter');
            const locationFilter = document.getElementById('productionCostCuttingLocationFilter');
            const searchInput = document.getElementById('productionCostCuttingSearch');
            const clearSearchButton = document.getElementById('clearProductionCostCuttingSearch');
            const pageLength = document.getElementById('productionCostCuttingPageLength');
            const bulkToolbar = document.getElementById('productionCostCuttingBulkToolbar');
            const bulkCount = document.getElementById('productionCostCuttingBulkCount');
            const checkAll = document.getElementById('checkAllProductionCostCuttings');
            const checkAllLabel = checkAll.closest('.cat-custom-checkbox');
            const maxSelected = 100;

            function selectedCuttingCount() {
                return selectedCuttingCosts.size + selectedPotongCosts.size;
            }

            function selectionSetFor(kind) {
                return kind === 'potong' ? selectedPotongCosts : selectedCuttingCosts;
            }

            function updateSelectionUI() {
                const selectedCount = selectedCuttingCount();
                bulkToolbar.style.display = selectedCount ? 'flex' : 'none';
                bulkCount.textContent = `${selectedCount} terpilih`;

                const checkboxes = document.querySelectorAll('.production-cost-cutting-row-check');
                let checkedCount = 0;
                checkboxes.forEach((checkbox) => {
                    const selected = selectionSetFor(checkbox.dataset.kind).has(Number(checkbox.value));
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

            function syncTypeDependentUI() {
                const isPotong = typeFilter.value === 'potong';
                document.getElementById('btnTambahOngkos').href = isPotong ?
                    "{{ route('admin_create_pc_finishing_potong') }}" :
                    "{{ route('admin_create_pc_finishing_cutting') }}";
                document.getElementById('thCuttingOrPotong').textContent = isPotong ? 'Spesifikasi / Ukuran' : 'Jasa Potong';
            }

            $('#productionCostCuttingTypeFilter, #productionCostCuttingLocationFilter')
                .select2({ width: '100%', minimumResultsForSearch: Infinity });
            $('#productionCostCuttingPageLength').select2({ width: '76px', minimumResultsForSearch: -1 });

            $('#productionCostCuttingTable').on('processing.dt', centerProcessingIndicator);

            /*
            |--------------------------------------------------------------------------
            | DATATABLE INITIALIZATION
            |--------------------------------------------------------------------------
            */
            productionCostCuttingTable = new DataTable('#productionCostCuttingTable', {
                processing: true,
                serverSide: true,
                ordering: true,
                order: [[2, 'asc']],

                ajax: {
                    url: tableElement.dataset.url,
                    type: 'GET',
                    data: function(d) {
                        d.cutting_filter = typeFilter.value || 'cutting';
                        d.location_id = locationFilter.value === 'all' ? '' : locationFilter.value;
                    },
                },

                columns: [{
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center cat-td-check',
                        render: function(data, type, row) {
                            const isPotong = !!row.is_potong;
                            const id = isPotong ? row.digital_potong_production_cost_id : row.digital_cutting_production_cost_id;
                            const kind = isPotong ? 'potong' : 'cutting';
                            const safeName = escapeHtml(`${row.engine_name || ''} - ${row.location_names || ''}`);
                            const isChecked = selectionSetFor(kind).has(Number(id));
                            return `
                                <label class="cat-custom-checkbox">
                                    <input type="checkbox"
                                        class="production-cost-cutting-row-check"
                                        value="${Number(id)}"
                                        data-kind="${kind}"
                                        ${isChecked ? 'checked' : ''}
                                        aria-label="Pilih biaya jasa potong ${safeName}">
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
                            const pageInfo = productionCostCuttingTable.page.info();
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
                            return `
                                <div class="cat-cell-name-box">
                                    <div class="cat-icon-symbol">
                                        <i class="bi bi-scissors"></i>
                                    </div>
                                    <div class="cat-name-details">
                                        <span class="cat-name-primary">${escapeHtml(data || '-')}</span>
                                        <span class="cat-name-badge">${row.is_potong ? 'Potong Guillotine' : 'Jasa Potong'}</span>
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
                        data: 'category_count',
                        className: 'text-center cat-td-date',
                        defaultContent: 0,
                        render: function(data, type, row) {
                            if (type !== 'display') {
                                return data;
                            }
                            return `
                                <span class="cat-date-chip" title="${escapeHtml(row.category_names || '')}">
                                    <i class="bi bi-folder-fill"></i>
                                    <span>${Number(data || 0).toLocaleString('id-ID')} Kategori</span>
                                </span>
                            `;
                        },
                    },
                    {
                        data: 'cutting_count',
                        className: 'text-center cat-td-date',
                        defaultContent: 0,
                        render: function(data, type, row) {
                            if (type !== 'display') {
                                return data;
                            }
                            if (row.is_potong) {
                                return `
                                    <span class="cat-date-chip">
                                        <i class="bi bi-aspect-ratio"></i>
                                        <span>4 Ukuran (A3 - &le;A6)</span>
                                    </span>
                                `;
                            }
                            return `
                                <span class="cat-date-chip">
                                    <i class="bi bi-scissors"></i>
                                    <span>${Number(data || 0).toLocaleString('id-ID')} Jasa Potong</span>
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
                            const isPotong = !!row.is_potong;
                            const rowId = isPotong ? row.digital_potong_production_cost_id : row
                                .digital_cutting_production_cost_id;
                            const encodedId = btoa(String(rowId));
                            const scopeLabel = row.vendor_name ?
                                `${row.location_names} (${row.vendor_name})` : row.location_names;
                            const editUrl = isPotong ?
                                @json(route('admin_edit_pc_finishing_potong', ['id' => '__ID__'])) :
                                @json(route('admin_edit_pc_finishing_cutting', ['id' => '__ID__']));
                            const detailUrl = isPotong ?
                                @json(route('admin_details_pc_finishing_potong', ['id' => '__ID__'])) :
                                @json(route('admin_details_pc_finishing_cutting', ['id' => '__ID__']));

                            return `
                                <div class="cat-action-group">
                                    <a href="${detailUrl.replace('__ID__', encodedId)}" class="cat-btn-action cat-btn-view btn-detail-cutting"
                                        aria-label="View" title="${isPotong ? 'Detail ongkos produksi potong' : 'Detail ongkos produksi jasa potong'}">
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </a>
                                    <a href="${editUrl.replace('__ID__', encodedId)}" class="cat-btn-action cat-btn-edit btn-edit-cutting"
                                        aria-label="Edit" title="${isPotong ? 'Edit ongkos produksi potong' : 'Edit ongkos produksi jasa potong'}">
                                        <i class="bi bi-pen"></i>
                                    </a>
                                    <button type="button" class="cat-btn-action cat-btn-del btn-delete-production-cost-cutting"
                                        data-id="${encodedId}"
                                        data-row-id="${Number(rowId)}"
                                        data-type="${isPotong ? 'potong' : 'cutting'}"
                                        data-scope="${encodeURIComponent(scopeLabel || '')}"
                                        aria-label="Hapus" title="${isPotong ? 'Hapus ongkos produksi potong' : 'Hapus ongkos produksi jasa potong'}">
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
                    info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ biaya jasa potong',
                    infoEmpty: 'Menampilkan 0 sampai 0 dari 0 biaya jasa potong',
                    zeroRecords: 'Data biaya jasa potong tidak ditemukan',
                    processing: 'Memuat data...',
                },

                drawCallback: function() {
                    centerProcessingIndicator();
                },
            });
            centerProcessingIndicator();

            productionCostCuttingTable.on('draw', function() {
                const info = productionCostCuttingTable.page.info();
                const totalEl = document.getElementById('productionCostCuttingTotalCount');
                if (totalEl) {
                    totalEl.textContent = `${info.recordsTotal.toLocaleString('id-ID')} Total Biaya Jasa Potong`;
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
                searchTimer = setTimeout(() => productionCostCuttingTable.search(query).draw(), 250);
            });
            clearSearchButton.addEventListener('click', function() {
                searchInput.value = '';
                clearSearchButton.style.display = 'none';
                productionCostCuttingTable.search('').draw();
                searchInput.focus();
            });
            $('#productionCostCuttingTypeFilter').on('change', function() {
                syncTypeDependentUI();
                productionCostCuttingTable.ajax.reload(null, true);
            });
            $('#productionCostCuttingLocationFilter').on('change', function() {
                productionCostCuttingTable.ajax.reload(null, true);
            });
            $('#productionCostCuttingPageLength').on('change', function() {
                productionCostCuttingTable.page.len(Number(pageLength.value)).draw();
            });
            document.getElementById('resetProductionCostCuttingFilters').addEventListener('click', function() {
                clearTimeout(searchTimer);
                searchInput.value = '';
                clearSearchButton.style.display = 'none';
                $('#productionCostCuttingTypeFilter').val('cutting').trigger('change.select2');
                $('#productionCostCuttingLocationFilter').val('all').trigger('change.select2');
                $('#productionCostCuttingPageLength').val('10').trigger('change.select2');
                syncTypeDependentUI();
                selectedCuttingCosts.clear();
                selectedPotongCosts.clear();
                productionCostCuttingTable.search('').order([[2, 'asc']]).page.len(10).ajax.reload(null, true);
            });

            /*
            |--------------------------------------------------------------------------
            | CHECKBOX SELECTION
            |--------------------------------------------------------------------------
            */
            $('#productionCostCuttingTable').on('change', '.production-cost-cutting-row-check', function() {
                const id = Number(this.value);
                const selection = selectionSetFor(this.dataset.kind);
                if (this.checked && !selection.has(id) && selectedCuttingCount() >= maxSelected) {
                    this.checked = false;
                    showToast('error', `Maksimal ${maxSelected} biaya jasa potong dapat dipilih sekaligus.`);
                    return;
                }
                if (this.checked) selection.add(id);
                else selection.delete(id);
                updateSelectionUI();
            });
            checkAll.addEventListener('change', function() {
                let reachedLimit = false;
                document.querySelectorAll('.production-cost-cutting-row-check').forEach((checkbox) => {
                    const id = Number(checkbox.value);
                    const selection = selectionSetFor(checkbox.dataset.kind);
                    if (!checkAll.checked) {
                        selection.delete(id);
                    } else if (!selection.has(id)) {
                        if (selectedCuttingCount() >= maxSelected) reachedLimit = true;
                        else selection.add(id);
                    }
                });
                updateSelectionUI();
                if (reachedLimit) showToast('error', `Maksimal ${maxSelected} biaya jasa potong dapat dipilih sekaligus.`);
            });
            document.getElementById('cancelProductionCostCuttingSelection').addEventListener('click', function() {
                selectedCuttingCosts.clear();
                selectedPotongCosts.clear();
                updateSelectionUI();
            });

            /*
            |--------------------------------------------------------------------------
            | BULK DELETE
            |--------------------------------------------------------------------------
            */
            const bulkModal = document.getElementById('modalBulkDeleteProductionCostCuttings');
            const confirmBulkButton = document.getElementById('confirmBulkDeleteProductionCostCuttings');
            let isBulkDeleting = false;
            const closeBulkModal = () => {
                if (!isBulkDeleting) bulkModal.classList.remove('is-open');
            };
            ['closeBulkDeleteProductionCostCuttings', 'cancelBulkDeleteProductionCostCuttings'].forEach((id) => {
                document.getElementById(id).addEventListener('click', closeBulkModal);
            });
            bulkModal.addEventListener('click', (event) => {
                if (event.target === bulkModal) closeBulkModal();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && bulkModal.classList.contains('is-open')) closeBulkModal();
            });
            document.getElementById('bulkDeleteProductionCostCuttings').addEventListener('click', function() {
                const count = selectedCuttingCount();
                if (!count) return;
                document.getElementById('bulkDeleteProductionCostCuttingDescription').textContent =
                    `${count} biaya jasa potong terpilih beserta seluruh kategori, detail, dan harga pada baris tersebut akan dihapus. Master mesin, lokasi, vendor, kategori, dan jasa potong tidak ikut dihapus. Data yang sudah dihapus tidak dapat dikembalikan.`;
                bulkModal.classList.add('is-open');
            });
            confirmBulkButton.addEventListener('click', async function() {
                if (isBulkDeleting || !selectedCuttingCount()) return;
                isBulkDeleting = true;
                confirmBulkButton.disabled = true;
                const originalContent = confirmBulkButton.innerHTML;
                confirmBulkButton.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Menghapus...
                `;
                const payload = new FormData();
                selectedCuttingCosts.forEach((id) => payload.append('cutting[]', id));
                selectedPotongCosts.forEach((id) => payload.append('potong[]', id));

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
                        throw new Error(result.message || 'Biaya jasa potong gagal dihapus.');
                    }

                    selectedCuttingCosts.clear();
                    selectedPotongCosts.clear();
                    isBulkDeleting = false;
                    closeBulkModal();
                    productionCostCuttingTable.ajax.reload(null, false);
                    showToast('success', result.message);
                } catch (error) {
                    showToast('error', error.message || 'Terjadi kesalahan saat menghapus biaya jasa potong.');
                } finally {
                    isBulkDeleting = false;
                    confirmBulkButton.disabled = false;
                    confirmBulkButton.innerHTML = originalContent;
                }
            });

            /*
            |--------------------------------------------------------------------------
            | DELETE PRODUCTION COST JASA POTONG
            |--------------------------------------------------------------------------
            */
            let cuttingIdToDelete = null;
            let cuttingRowIdToDelete = null;
            let cuttingTypeToDelete = 'cutting';
            let isDeletingCutting = false;

            const modalDelete = document.getElementById('modalDeleteProductionCostCutting');
            const btnTutup = document.getElementById('btnTutupDeleteProductionCostCutting');
            const btnBatal = document.getElementById('btnBatalDeleteProductionCostCutting');
            const btnConfirm = document.getElementById('btnConfirmDeleteProductionCostCutting');
            const scopeEl = document.getElementById('digitalCuttingDeleteScope');

            function tutupDeleteModal() {
                if (!modalDelete) {
                    return;
                }
                modalDelete.classList.remove('is-open');
                cuttingIdToDelete = null;
                cuttingTypeToDelete = 'cutting';
                isDeletingCutting = false;
            }

            document.addEventListener('click', function(event) {
                const deleteBtn = event.target.closest('.btn-delete-production-cost-cutting');
                if (!deleteBtn) {
                    return;
                }

                cuttingIdToDelete = deleteBtn.dataset.id;
                cuttingRowIdToDelete = Number(deleteBtn.dataset.rowId);
                cuttingTypeToDelete = deleteBtn.dataset.type || 'cutting';
                const scopeText = decodeURIComponent(deleteBtn.dataset.scope || '-');

                const eyebrowEl = document.getElementById('digitalCuttingDeleteEyebrow');
                const titleEl = document.getElementById('digitalCuttingDeleteTitle');
                const headingEl = document.getElementById('digitalCuttingDeleteHeading');
                const descEl = document.getElementById('digitalCuttingDeleteDescription');

                if (cuttingTypeToDelete === 'potong') {
                    if (eyebrowEl) eyebrowEl.textContent = 'HARGA & TARIF · ONGKOS POTONG (GUILLOTINE)';
                    if (titleEl) titleEl.textContent = 'Hapus Ongkos Produksi Potong';
                    if (headingEl) headingEl.textContent = 'Hapus ongkos produksi potong?';
                    if (descEl) {
                        descEl.innerHTML = `Hapus konfigurasi ongkos potong untuk <strong id="digitalCuttingDeleteScope">${escapeHtml(scopeText)}</strong> beserta seluruh kategori dan rincian ongkos potong minimal pada baris ini? Data yang sudah dihapus tidak dapat dikembalikan.`;
                    }
                } else {
                    if (eyebrowEl) eyebrowEl.textContent = 'HARGA & TARIF · PRODUCTION COST JASA POTONG';
                    if (titleEl) titleEl.textContent = 'Hapus Production Cost Jasa Potong';
                    if (headingEl) headingEl.textContent = 'Hapus production cost jasa potong?';
                    if (descEl) {
                        descEl.innerHTML = `Hapus konfigurasi ongkos jasa potong untuk <strong id="digitalCuttingDeleteScope">${escapeHtml(scopeText)}</strong> beserta seluruh kategori, detail jasa potong, dan harga tier pada baris ini? Data yang sudah dihapus tidak dapat dikembalikan.`;
                    }
                }

                if (modalDelete) {
                    modalDelete.classList.add('is-open');
                }
            });

            if (btnTutup) {
                btnTutup.addEventListener('click', function() {
                    if (!isDeletingCutting) {
                        tutupDeleteModal();
                    }
                });
            }

            if (btnBatal) {
                btnBatal.addEventListener('click', function() {
                    if (!isDeletingCutting) {
                        tutupDeleteModal();
                    }
                });
            }

            if (modalDelete) {
                modalDelete.addEventListener('click', function(event) {
                    if (event.target === modalDelete && !isDeletingCutting) {
                        tutupDeleteModal();
                    }
                });
            }

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modalDelete && modalDelete.classList.contains('is-open') && !
                    isDeletingCutting) {
                    tutupDeleteModal();
                }
            });

            if (btnConfirm) {
                btnConfirm.addEventListener('click', async function() {
                    if (!cuttingIdToDelete || isDeletingCutting) {
                        return;
                    }

                    isDeletingCutting = true;
                    const originalContent = btnConfirm.innerHTML;
                    btnConfirm.disabled = true;
                    btnConfirm.innerHTML = `
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Menghapus...
                    `;

                    try {
                        const deleteEndpoint = (cuttingTypeToDelete === 'potong') ?
                            `{{ url('/admin/production-cost-finishing-potong') }}/${cuttingIdToDelete}/delete` :
                            `{{ url('/admin/production-cost-finishing-cuttings') }}/${cuttingIdToDelete}/delete`;

                        const response = await fetch(deleteEndpoint, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]')?.getAttribute('content') ||
                                    document.querySelector('input[name="_token"]')?.value || '',
                                'Accept': 'application/json'
                            }
                        });

                        const result = await response.json();

                        if (response.ok && result && result.success) {
                            selectionSetFor(cuttingTypeToDelete).delete(cuttingRowIdToDelete);
                            tutupDeleteModal();
                            if (productionCostCuttingTable) {
                                productionCostCuttingTable.ajax.reload(null, false);
                            }
                            showToast('success', result.message ||
                                'Ongkos produksi berhasil dihapus.');
                        } else {
                            tutupDeleteModal();
                            showToast('error', result?.message ||
                                'Ongkos produksi gagal dihapus.');
                        }
                    } catch (error) {
                        console.error(error);
                        tutupDeleteModal();
                        showToast('error', 'Terjadi kesalahan saat menghapus data.');
                    } finally {
                        isDeletingCutting = false;
                        btnConfirm.disabled = false;
                        btnConfirm.innerHTML = originalContent;
                    }
                });
            }
        });
    </script>
@endsection

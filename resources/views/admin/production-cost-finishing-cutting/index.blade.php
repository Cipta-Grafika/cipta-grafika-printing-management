@extends('admin_master')

@section('contents')
    <style>
        #productionCostCuttingTable th.action-column,
        #productionCostCuttingTable td.action-column {
            text-align: center !important;
            vertical-align: middle !important;
        }

        #productionCostCuttingTable td.action-column .data-cell-actions {
            display: flex !important;
            justify-content: center !important;
            align-items: center !important;
            gap: 8px !important;
            width: 100% !important;
            margin: 0 auto !important;
        }

        #productionCostCuttingTable td.action-column .btn--icon {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
    </style>
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">BIAYA · JASA POTONG</span>
                        <p class="hero-sub">Kelola biaya jasa potong (cutting) yang digunakan sebagai dasar perhitungan
                            biaya serta estimasi harga produk.</p>
                        <div class="material-category-filter" style="margin-top: 14px; min-width: 320px; max-width: 100%;">
                            <select id="productionCostCuttingTypeFilter" class="select select2"
                                aria-label="Filter jenis ongkos potong">
                                <option value="cutting" selected>Jasa Potong (Cutting / Selain Potong)</option>
                                <option value="potong">Potong (Guillotine / Ongkos Minimal)</option>
                            </select>
                        </div>
                    </div>
                    <div class="hero-actions" style="display: flex; justify-content: flex-end; align-items: flex-start;">
                        <a href="{{ route('admin_create_pc_finishing_cutting') }}" class="btn btn--primary"
                            id="btnTambahOngkos">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            <span id="labelTambahOngkos">Tambah Ongkos</span>
                        </a>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="productionCostCuttingTable" class="data-table">
                            <thead>
                                <tr>
                                    <th style="width:32px"></th>
                                    <th>No</th>
                                    <th>Mesin</th>
                                    <th>Lokasi</th>
                                    <th class="text-center" style="text-align: center;">Kategori</th>
                                    <th class="text-center" style="text-align: center;" id="thCuttingOrPotong">Jasa Potong
                                    </th>
                                    <th class="action-column text-center" style="text-align: center;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </section>
                </div>
            </main>
            <div data-shell-footer></div>
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

        document.addEventListener('DOMContentLoaded', function() {
            const escapeHtml = DataTable.render.text().display;

            /*
            |--------------------------------------------------------------------------
            | DATATABLE INITIALIZATION
            |--------------------------------------------------------------------------
            */
            productionCostCuttingTable = new DataTable('#productionCostCuttingTable', {
                processing: true,
                serverSide: true,
                ordering: false,

                ajax: {
                    url: "{{ route('admin_data_pc_finishing_cuttings') }}",
                    type: 'GET',
                    data: function(d) {
                        d.cutting_filter = $('#productionCostCuttingTypeFilter').val() || 'cutting';
                    }
                },

                responsive: {
                    details: {
                        type: 'column',
                        target: 0,
                    },
                },

                columns: [{
                        className: 'dtr-control',
                        orderable: false,
                        data: null,
                        defaultContent: '',
                    },
                    {
                        data: null,
                        render: function(data, type, row, meta) {
                            return productionCostCuttingTable.page.info().start + meta.row + 1;
                        },
                    },
                    {
                        data: 'engine_name',
                        defaultContent: '-',
                        render: DataTable.render.text(),
                    },
                    {
                        data: 'location_names',
                        defaultContent: '-',
                        render: function(data, type, row) {
                            const label = row.vendor_name ? `${data} (${row.vendor_name})` : (
                                data || '-');
                            return type === 'display' ? escapeHtml(label) : label;
                        },
                    },
                    {
                        data: 'category_names',
                        className: 'text-center',
                        defaultContent: '-',
                        render: function(data, type, row) {
                            return type === 'display' ? `${row.category_count} Kategori` : row
                                .category_count;
                        },
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css('text-align', 'center');
                        }
                    },
                    {
                        data: null,
                        className: 'text-center',
                        defaultContent: 0,
                        render: function(data, type, row) {
                            if (row.is_potong) {
                                return `<span class="badge" style="background: rgba(16, 185, 129, 0.15); color: #10b981; font-weight: 600; padding: 4px 10px; border-radius: 6px;">4 Ukuran (A3 - &le;A6)</span>`;
                            }
                            return `${row.cutting_count || 0} Jasa Potong`;
                        },
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css('text-align', 'center');
                        }
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'action-column text-center',
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
                                <div class="data-cell-actions" style="display: flex; justify-content: center; align-items: center; gap: 8px; width: 100%;">
                                    <a href="${detailUrl.replace('__ID__', encodedId)}" class="btn--icon btn-detail-cutting"
                                        aria-label="View" title="${isPotong ? 'Detail ongkos produksi potong' : 'Detail ongkos produksi jasa potong'}">
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </a>
                                    <a href="${editUrl.replace('__ID__', encodedId)}" class="btn--icon btn-edit-cutting"
                                        aria-label="Edit" title="${isPotong ? 'Edit ongkos produksi potong' : 'Edit ongkos produksi jasa potong'}">
                                        <i class="bi bi-pen"></i>
                                    </a>
                                    <button type="button" class="btn--icon btn-delete-production-cost-cutting"
                                        data-id="${encodedId}"
                                        data-type="${isPotong ? 'potong' : 'cutting'}"
                                        data-scope="${encodeURIComponent(scopeLabel)}"
                                        aria-label="Hapus" title="${isPotong ? 'Hapus ongkos produksi potong' : 'Hapus ongkos produksi jasa potong'}">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            `;
                        },
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css({
                                'text-align': 'center',
                                'vertical-align': 'middle'
                            });
                        }
                    },
                ],

                columnDefs: [{
                    targets: 0,
                    className: 'dtr-control',
                    orderable: false,
                }, ],

                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],

                layout: {
                    topStart: 'pageLength',
                    topEnd: 'search',
                    bottomStart: 'info',
                    bottomEnd: 'paging',
                },

                language: {
                    search: 'Search:',
                    lengthMenu: '_MENU_ entries per page',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    zeroRecords: 'Data production cost jasa potong tidak ditemukan',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari mesin, lokasi, kategori, atau jasa potong...',
                },

                initComplete: function() {
                    const searchInput = document.querySelector(
                        '#productionCostCuttingTable_wrapper .dt-search input'
                    );

                    if (searchInput) {
                        searchInput.style.padding = '9px 14px';
                        searchInput.style.borderRadius = '8px';
                        searchInput.style.border = '1px solid #e2e8f0';
                    }
                },
            });

            /*
            |--------------------------------------------------------------------------
            | FILTER JENIS ONGKOS POTONG (CUTTING VS POTONG GUILLOTINE)
            |--------------------------------------------------------------------------
            */
            $('#productionCostCuttingTypeFilter').select2({
                width: '100%',
                minimumResultsForSearch: Infinity
            });

            $('#productionCostCuttingTypeFilter').on('change', function() {
                const val = $(this).val();
                const btnTambah = document.getElementById('btnTambahOngkos');
                const thHeader = document.getElementById('thCuttingOrPotong');

                if (val === 'potong') {
                    if (btnTambah) {
                        btnTambah.href = "{{ route('admin_create_pc_finishing_potong') }}";
                    }
                    if (thHeader) {
                        thHeader.textContent = 'Spesifikasi / Ukuran';
                    }
                } else {
                    if (btnTambah) {
                        btnTambah.href = "{{ route('admin_create_pc_finishing_cutting') }}";
                    }
                    if (thHeader) {
                        thHeader.textContent = 'Jasa Potong';
                    }
                }

                if (productionCostCuttingTable) {
                    productionCostCuttingTable.ajax.reload();
                }
            });

            /*
            |--------------------------------------------------------------------------
            | DELETE PRODUCTION COST JASA POTONG
            |--------------------------------------------------------------------------
            */
            let cuttingIdToDelete = null;
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

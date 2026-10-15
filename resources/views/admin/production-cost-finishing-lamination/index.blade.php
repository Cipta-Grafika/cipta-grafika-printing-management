@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">BIAYA · LAMINASI</span>
                        {{-- <h1 class="hero-title">Daftar Finishing & Jasa</h1> --}}
                        <p class="hero-sub">Kelola biaya material dan finishing yang digunakan sebagai dasar perhitungan
                            biaya serta estimasi harga produk.</p>
                    </div>
                    <div class="hero-actions material-hero-actions"><a id="btnExportProductionCostLamination"
                            href="{{ route('admin_export_pc_finishing_laminations') }}"
                            class="btn btn--ghost"><svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg> Export</a>
                        <div class="material-category-filter">
                            <select id="productionCostLaminationEngineFilter" class="select select2"
                                aria-label="Filter mesin ongkos produksi laminasi">
                                <option value="large_format" selected>Large Format</option>
                                <option value="digital_print_a3">Digital Print A3+</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn--ghost" id="btnDownloadTemplateLamination">
                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Download Template
                        </button>
                        <button class="btn btn--ghost" id="btnImportFinishingLamination"><svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg> Import</button>
                        <a href="{{ route('admin_create_pc_finishing_lamination') }}" class="btn btn--primary"><svg
                                viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg> Tambah Ongkos</a>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">

                        <p id="digitalLaminationListNotice" class="field-help" hidden>
                            Template dan import Digital Print A3+ belum tersedia.
                        </p>
                        <table id="productionCostLaminationTable" class="data-table">
                            <thead>
                                <tr>
                                    <th style="width:32px"></th>
                                    <th>No</th>
                                    <th>Mesin</th>
                                    <th>Lokasi</th>
                                    <th class="text-center">Kategori</th>
                                    <th class="text-center">Laminasi</th>
                                    <th>Ukuran</th>
                                    <th class="action-column">Aksi</th>
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

        document.addEventListener('DOMContentLoaded', function() {
            const engineFilter = document.getElementById('productionCostLaminationEngineFilter');
            const exportButton = document.getElementById('btnExportProductionCostLamination');
            const exportUrl = exportButton.href;
            const escapeHtml = DataTable.render.text().display;

            function isDigitalFilter() {
                return engineFilter.value === 'digital_print_a3';
            }

            $('#productionCostLaminationEngineFilter').select2({ width: '100%' });

            function updateExportLaminationLink() {
                const url = new URL(exportUrl);
                url.searchParams.set('pricing_engine', engineFilter.value);
                exportButton.href = url.toString();
            }

            updateExportLaminationLink();

            productionCostLaminationTable = new DataTable(
                '#productionCostLaminationTable', {
                    processing: true,
                    serverSide: true,
                    ordering: false,

                    ajax: {
                        url: "{{ route('admin_data_pc_finishing_lamination') }}",
                        type: 'GET',
                        data: function(data) {
                            data.pricing_engine = engineFilter.value;
                        },
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
                                return productionCostLaminationTable
                                    .page
                                    .info()
                                    .start + meta.row + 1;
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
                                const label = row.vendor_name ? `${data} (${row.vendor_name})` : (data || '-');
                                return type === 'display' ? escapeHtml(label) : label;
                            },
                        },
                        {
                            data: 'category_names',
                            className: 'text-center',
                            defaultContent: '-',
                            render: function(data, type, row) {
                                if (row.pricing_engine === 'digital_print_a3') {
                                    return type === 'display' ? `${row.category_count} Kategori` : row.category_count;
                                }

                                return type === 'display' ? escapeHtml(data || '-') : data;
                            },
                        },
                        {
                            data: 'lamination_count',
                            className: 'text-center',
                            defaultContent: 0,
                            render: function(data) {
                                return `${data} Laminasi`;
                            },
                        },
                        {
                            data: 'size_count',
                            defaultContent: 0,
                            render: function(data) {
                                return `${data} Ukuran`;
                            },
                        },
                        {
                            data: null,
                            orderable: false,
                            searchable: false,
                            className: 'action-column',
                            render: function(data, type, row) {
                                if (row.pricing_engine === 'digital_print_a3') {
                                    const editUrl = @json(route('admin_edit_pc_finishing_lamination_digital', ['id' => '__ID__']));
                                    const detailUrl = @json(route('admin_details_pc_finishing_lamination_digital', ['id' => '__ID__']));
                                    return `<div class="data-cell-actions digital-lamination-actions">
                                        <a class="btn--icon"
                                            href="${detailUrl.replace('__ID__', btoa(String(row.digital_lamination_production_cost_id)))}"
                                            aria-label="View" title="Detail ongkos produksi laminasi Digital">
                                            <i class="bi bi-arrows-fullscreen"></i>
                                        </a>
                                        <a class="btn--icon"
                                            href="${editUrl.replace('__ID__', btoa(String(row.digital_lamination_production_cost_id)))}"
                                            aria-label="Edit" title="Edit ongkos produksi laminasi Digital">
                                            <i class="bi bi-pen"></i>
                                        </a>
                                        <button type="button" class="btn--icon btn-delete-production-cost-lamination"
                                            data-id="${btoa(String(row.digital_lamination_production_cost_id))}"
                                            data-pricing-engine="digital_print_a3"
                                            data-scope="${encodeURIComponent(row.vendor_name ? `${row.location_names} (${row.vendor_name})` : row.location_names)}"
                                            aria-label="Hapus" title="Hapus ongkos produksi laminasi Digital">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>
                                    </div>`;
                                }
                                return `
                            <div
                                class="data-cell-actions"
                                style="
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    gap: 5px;
                                "
                            >
                                <button
                                    class="btn--icon btn-detail"
                                    data-engine-id="${row.engine_id}"
                                    aria-label="View"
                                >
                                    <i class="bi bi-arrows-fullscreen"></i>
                                </button>

                                <button
                                    class="btn--icon btn-edit"
                                    data-engine-id="${row.engine_id}"
                                    aria-label="Edit"
                                >
                                    <i class="bi bi-pen"></i>
                                </button>

                                <button
                                    type="button"
                                    class="btn--icon btn-delete-production-cost-lamination"
                                    data-id="${row.engine_id}"
                                    aria-label="Hapus"
                                >
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        `;
                            },
                        },
                    ],

                    columnDefs: [{
                        targets: 0,
                        className: 'dtr-control',
                        orderable: false,
                    }, ],

                    pageLength: 10,

                    lengthMenu: [
                        10,
                        25,
                        50,
                        100
                    ],

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
                        zeroRecords: 'Data production cost laminasi tidak ditemukan',
                        processing: 'Memuat data...',
                        searchPlaceholder: 'Cari mesin, lokasi, atau kategori...',
                    },

                    initComplete: function() {
                        const searchInput = document.querySelector(
                            '#productionCostLaminationTable_wrapper .dt-search input'
                        );

                        if (searchInput) {
                            searchInput.style.padding = '9px 14px';
                            searchInput.style.borderRadius = '8px';
                            searchInput.style.border = '1px solid #e2e8f0';
                        }
                    },
                }
            );

            $('#productionCostLaminationEngineFilter').on('change', function() {
                const digital = isDigitalFilter();
                productionCostLaminationTable.column(6).visible(!digital, false);
                productionCostLaminationTable.columns.adjust();
                productionCostLaminationTable.responsive.recalc();
                productionCostLaminationTable.ajax.reload(null, true);
                document.getElementById('digitalLaminationListNotice').hidden = !digital;
                document.getElementById('btnDownloadTemplateLamination').disabled = digital;
                document.getElementById('btnImportFinishingLamination').disabled = digital;
                updateExportLaminationLink();
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

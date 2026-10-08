@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">BIAYA · MATERIAL</span>
                        {{-- <h1 class="hero-title">Daftar Finishing & Jasa</h1> --}}
                        <p class="hero-sub">Kelola biaya material dan finishing yang digunakan sebagai dasar perhitungan
                            biaya serta estimasi harga produk.</p>
                    </div>
                    <div class="hero-actions"><a href="{{ route('admin_export_pc_finishings') }}" class="btn btn--ghost"><svg
                                viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg> Export</a>
                        <button type="button" class="btn btn--ghost" id="btnDownloadTemplateFinishingService">
                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Download Template
                        </button>
                        <button class="btn btn--ghost" id="btnImportFinishingService"><svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg> Import</button>
                        <a href="{{ route('admin_create_pc_finishing') }}" class="btn btn--primary"><svg
                                viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg> Tambah Ongkos Produksi</a>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="productionCostTable" class="data-table">
                            <thead>
                                <tr>
                                    <th style="width:32px"></th>

                                    <th>No</th>

                                    <th>Mesin</th>

                                    <th>Lokasi</th>

                                    <th>Material</th>

                                    <th>Lebar</th>

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

            productionCostTable = new DataTable('#productionCostTable', {
                processing: true,
                serverSide: true,
                ordering: false,

                ajax: {
                    url: "{{ route('admin_data_pc_finishings') }}",
                    type: 'GET',
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
                            return productionCostTable.page.info().start +
                                meta.row +
                                1;
                        },
                    },

                    {
                        data: 'engine_name',
                        defaultContent: '-',
                    },

                    {
                        data: 'location_names',
                        defaultContent: '-',
                    },

                    {
                        data: 'material_count',
                        render: function(data) {
                            return `${data} Material`;
                        },
                    },

                    {
                        data: 'width_count',
                        render: function(data) {
                            return `${data} Lebar`;
                        },
                    },

                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'action-column',
                        render: function(data, type, row) {
                            return `
                                <div class="data-cell-actions"
                                    style="display: flex; align-items: center; justify-content: center; gap: 5px;">

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
                                        class="btn--icon btn-delete-production-cost"
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
                    zeroRecords: 'Data production cost tidak ditemukan',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari mesin atau lokasi...',
                },

                initComplete: function() {
                    const searchInput = document.querySelector(
                        '#productionCostTable_wrapper .dt-search input'
                    );

                    if (searchInput) {
                        searchInput.style.padding = '9px 14px';
                        searchInput.style.borderRadius = '8px';
                        searchInput.style.border = '1px solid #e2e8f0';
                    }
                },
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

@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">MASTER DATA · DISPLAY</span>
                        {{-- <h1 class="hero-title">Daftar Material</h1> --}}
                        <p class="hero-sub">Kelola dan pantau seluruh data display yang digunakan dalam proses produksi dan
                            perhitungan estimasi harga.</p>
                    </div>
                    <div class="hero-actions" style="display: flex; justify-content: right;">
                        {{-- <a href="{{ route('admin_export_material') }}" class="btn btn--ghost"><svg
                                viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg> Export</a>
                        <a href="{{ asset('templates/template_material.xlsx') }}" class="btn btn--ghost">

                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Download Template

                        </a>
                        <button class="btn btn--ghost" id="btnImportMaterial"><svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg> Import</button> --}}

                        <a href="{{ route('admin_create_display') }}" class="btn btn--primary" id="btnTambahMaterial">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Display
                        </a>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="displayProductTable" class="display data-table">

                            <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Nama Display</th>
                                    <th style="text-align: center;">Ukuran</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
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

    <!-- Modal Import Material -->
    <div class="modal-overlay" id="modalImportMaterial">

        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">

                <div>
                    <span class="eyebrow">MASTER DATA · MATERIAL</span>
                    <br>
                    <span class="eyebrow">Import Material</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseImportMaterial" aria-label="Tutup">

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
                        Pilih file Excel yang berisi data material untuk diimport
                        ke dalam sistem.
                    </p>
                </div>


                <!-- Custom File Input -->
                <label for="fileImportMaterial" class="file-upload-box" id="fileUploadBox">

                    <input type="file" id="fileImportMaterial" name="file" accept=".xlsx,.xls" hidden>


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

                <button type="button" class="btn btn--ghost" id="btnCancelImportMaterial">

                    Batal

                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportMaterial">

                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8 5-5 5 5" />
                    </svg>

                    Import Data

                </button>

            </div>

        </div>

    </div>



    <!-- MODAL DELETE DISPLAY -->
    <div class="modal-overlay" id="modalDeleteDisplay">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MASTER DATA · DISPLAY
                    </span>
                    <br>
                    <span class="eyebrow">
                        Hapus Display
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
                            Hapus Display?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus display ini?
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

        document.addEventListener('DOMContentLoaded', function() {

            displayProductTable = new DataTable('#displayProductTable', {

                /*
                |--------------------------------------------------------------------------
                | PROCESSING
                |--------------------------------------------------------------------------
                */

                processing: true,
                serverSide: true,
                ordering: false,

                /*
                |--------------------------------------------------------------------------
                | RESPONSIVE
                |--------------------------------------------------------------------------
                */

                responsive: {
                    details: {
                        type: 'column',
                        target: 0
                    }
                },

                /*
                |--------------------------------------------------------------------------
                | AJAX
                |--------------------------------------------------------------------------
                */

                ajax: {
                    url: "{{ route('admin_display_data') }}",
                    type: "GET"
                },

                /*
                |--------------------------------------------------------------------------
                | COLUMNS
                |--------------------------------------------------------------------------
                */

                columns: [

                    /*
                    |--------------------------------------------------------------------------
                    | RESPONSIVE CONTROL
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: null,
                        defaultContent: '',
                        className: 'dtr-control',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | NO
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',

                        render: function(data, type, row, meta) {

                            const pageInfo =
                                displayProductTable.page.info();

                            return pageInfo.start +
                                meta.row +
                                1;
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | NAMA DISPLAY
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'display_name',
                        responsivePriority: 1,

                        render: function(data) {

                            return `
                        <div>
                            ${data ?? '-'}
                        </div>
                    `;
                        },

                        createdCell: function(td) {

                            td.style.whiteSpace = 'normal';
                            td.style.wordBreak = 'break-word';
                            td.style.overflowWrap = 'break-word';
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | UKURAN
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: null,
                        responsivePriority: 100,
                        className: 'text-center',

                        render: function(data, type, row) {

                            const length = row.length ?? 0;
                            const width = row.width ?? 0;

                            return `${width} × ${length}`;
                        },

                        createdCell: function(td) {

                            td.style.textAlign = 'center';
                            td.style.whiteSpace = 'nowrap';
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | STATUS
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'status',
                        responsivePriority: 100,

                        render: function(data) {

                            const statusClass =
                                data === 'Active' ?
                                'success' :
                                'danger';

                            return `
                        <span class="badge ${statusClass} dot">
                            ${data}
                        </span>
                    `;
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | AKSI
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 100,
                        className: 'text-center',

                        render: function(data, type, row) {

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

                            <!-- VIEW -->

                            <button
                                type="button"
                                class="btn--icon btn-view-display-product"
                                data-id="${data}"
                                aria-label="View"
                            >
                                <i class="bi bi-arrows-fullscreen"></i>
                            </button>

                            <!-- EDIT -->

                            <button
                                type="button"
                                class="btn--icon btn-edit-display-product"
                                data-id="${data}"
                                aria-label="Edit"
                            >
                                <i class="bi bi-pen"></i>
                            </button>

                            <!-- DELETE -->

                            <button
                                type="button"
                                class="btn--icon btn-delete-display-product"
                                data-id="${data}"
                                aria-label="Hapus"
                            >
                                <i class="bi bi-trash3-fill"></i>
                            </button>

                        </div>
                    `;
                        }
                    }
                ],

                /*
                |--------------------------------------------------------------------------
                | COLUMN DEFINITION
                |--------------------------------------------------------------------------
                */

                columnDefs: [{
                    targets: 0,
                    className: 'dtr-control'
                }],

                /*
                |--------------------------------------------------------------------------
                | PAGE LENGTH
                |--------------------------------------------------------------------------
                */

                pageLength: 10,

                /*
                |--------------------------------------------------------------------------
                | PAGE LENGTH OPTIONS
                |--------------------------------------------------------------------------
                */

                lengthMenu: [
                    10,
                    25,
                    50,
                    100
                ],

                /*
                |--------------------------------------------------------------------------
                | LAYOUT
                |--------------------------------------------------------------------------
                */

                layout: {
                    topStart: 'pageLength',
                    topEnd: 'search',
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },

                /*
                |--------------------------------------------------------------------------
                | LANGUAGE
                |--------------------------------------------------------------------------
                */

                language: {
                    search: 'Search:',
                    lengthMenu: '_MENU_ entries per page',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    zeroRecords: 'Data display tidak ditemukan',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari display...'
                },

                /*
                |--------------------------------------------------------------------------
                | INIT COMPLETE
                |--------------------------------------------------------------------------
                */

                initComplete: function() {

                    $(this)
                        .closest(
                            '.dt-container, .dataTables_wrapper'
                        )
                        .find(
                            '.dt-search input, .dataTables_filter input'
                        )
                        .css({
                            'padding': '9px 14px',
                            'border-radius': '8px',
                            'border': '1px solid #e2e8f0'
                        });
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | DELETE DISPLAY
            |--------------------------------------------------------------------------
            */

            let displayIdToDelete = null;

            const modalDeleteDisplay =
                document.getElementById(
                    'modalDeleteDisplay'
                );

            const btnTutupDeleteDisplay =
                document.getElementById(
                    'btnTutupDeleteDisplay'
                );

            const btnBatalDeleteDisplay =
                document.getElementById(
                    'btnBatalDeleteDisplay'
                );

            const btnConfirmDeleteDisplay =
                document.getElementById(
                    'btnConfirmDeleteDisplay'
                );


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL DELETE
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                function(event) {

                    const deleteButton =
                        event.target.closest(
                            '.btn-delete-display-product'
                        );

                    if (!deleteButton) {
                        return;
                    }

                    displayIdToDelete =
                        deleteButton.dataset.id;

                    modalDeleteDisplay.classList.add(
                        'is-open'
                    );
                }
            );


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupDeleteDisplayModal() {

                modalDeleteDisplay.classList.remove(
                    'is-open'
                );

                displayIdToDelete = null;
            }


            /*
            |--------------------------------------------------------------------------
            | BUTTON TUTUP
            |--------------------------------------------------------------------------
            */

            btnTutupDeleteDisplay.addEventListener(
                'click',
                tutupDeleteDisplayModal
            );


            /*
            |--------------------------------------------------------------------------
            | BUTTON BATAL
            |--------------------------------------------------------------------------
            */

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

                    if (
                        event.target ===
                        modalDeleteDisplay
                    ) {
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
                        modalDeleteDisplay.classList.contains(
                            'is-open'
                        )
                    ) {
                        tutupDeleteDisplayModal();
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI HAPUS DISPLAY
            |--------------------------------------------------------------------------
            */

            btnConfirmDeleteDisplay.addEventListener(
                'click',
                async function() {

                    /*
                    |--------------------------------------------------------------------------
                    | CEK ID DISPLAY
                    |--------------------------------------------------------------------------
                    */

                    if (!displayIdToDelete) {
                        return;
                    }


                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | REQUEST DELETE
                        |--------------------------------------------------------------------------
                        */

                        const response =
                            await fetch(
                                `/admin/displays/${displayIdToDelete}/delete`, {
                                    method: 'DELETE',

                                    headers: {

                                        'X-CSRF-TOKEN': document
                                            .querySelector(
                                                'meta[name="csrf-token"]'
                                            )
                                            .getAttribute(
                                                'content'
                                            ),

                                        'Accept': 'application/json'
                                    }
                                }
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | RESPONSE JSON
                        |--------------------------------------------------------------------------
                        */

                        const result =
                            await response.json();


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

                            tutupDeleteDisplayModal();


                            /*
                            |--------------------------------------------------------------------------
                            | RELOAD DATATABLE
                            |--------------------------------------------------------------------------
                            */

                            displayProductTable
                                .ajax
                                .reload(
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
                                'Display gagal dihapus.'
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
                            'Terjadi kesalahan saat menghapus Display.'
                        );
                    }
                }
            );

            document.addEventListener('click', function(event) {

                const detailsButton = event.target.closest(
                    '.btn-view-display-product'
                );

                if (!detailsButton) {
                    return;
                }

                event.preventDefault();

                const displayId = detailsButton.dataset.id;

                if (!displayId) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | ENCODE ID
                |--------------------------------------------------------------------------
                */
                const encodedId = btoa(String(displayId));

                /*
                |--------------------------------------------------------------------------
                | URL DETAILS
                |--------------------------------------------------------------------------
                */
                const url = "{{ route('admin_display_details', ':id') }}"
                    .replace(':id', encodedId);

                /*
                |--------------------------------------------------------------------------
                | PINDAH KE HALAMAN DETAILS
                |--------------------------------------------------------------------------
                */
                window.location.href = url;
            });

            document.addEventListener('click', function(event) {

                const editButton = event.target.closest(
                    '.btn-edit-display-product'
                );

                if (!editButton) {
                    return;
                }

                event.preventDefault();

                const displayId = editButton.dataset.id;

                if (!displayId) {
                    return;
                }

                const encodedId = btoa(String(displayId));

                const url = "{{ route('admin_edit_display', ':id') }}"
                    .replace(':id', encodedId);

                window.location.href = url;
            });
        });
    </script>
@endsection

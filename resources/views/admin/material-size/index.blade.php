@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">MASTER DATA · UKURAN MATERIAL</span>
                        {{-- <h1 class="hero-title">Ukuran Material</h1> --}}
                        <p class="hero-sub">Kelola dan pantau seluruh daftar harga yang digunakan sebagai acuan dalam
                            penentuan harga jual dan perhitungan estimasi harga.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin_export_material_size') }}" class="btn btn--ghost">
                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>
                            Export
                        </a>
                        <a href="{{ asset('templates/template_ukuran_material.xlsx') }}" class="btn btn--ghost">

                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Download Template

                        </a>
                        <button class="btn btn--ghost" id="btnImportMaterialSize"><svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg> Import</button>
                        <a href="{{ route('admin_create_material_size') }}" class="btn btn--primary"><svg
                                viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg> Tambah Ukuran</a>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="materialSizeTable" class="display data-table">

                            <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Material</th>
                                    <th>Dibuat Oleh</th>
                                    <th>Dibuat Pada</th>
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

    <!-- Modal Import Material Size -->
    <div class="modal-overlay" id="modalImportMaterialSize">

        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MASTER DATA · UKURAN MATERIAL
                    </span>
                    <br>
                    <span class="eyebrow">
                        Import Ukuran Material
                    </span>

                </div>


                <button type="button" class="modal-close" id="btnCloseImportMaterialSize" aria-label="Tutup">

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
                        Pilih file Excel yang berisi data ukuran material
                        untuk diimport ke dalam sistem.
                    </p>

                </div>


                <!-- Custom File Input -->
                <label for="fileImportMaterialSize" class="file-upload-box" id="fileUploadBox">

                    <input type="file" id="fileImportMaterialSize" name="file" accept=".xlsx,.xls" hidden>


                    <div class="file-upload-content">

                        <div class="file-upload-icon">

                            <svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg>

                        </div>


                        <div class="file-upload-text">

                            <strong>
                                Pilih file Excel
                            </strong>

                            <span>
                                Klik untuk memilih file dari komputer
                            </span>

                        </div>

                    </div>


                    <div class="file-selected-name" id="fileSelectedMaterialSize">

                        Belum ada file dipilih

                    </div>

                </label>

            </div>


            <!-- Footer -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCancelImportMaterialSize">

                    Batal

                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportMaterialSize">

                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8 5-5 5 5" />
                    </svg>

                    Import Data

                </button>

            </div>

        </div>

    </div>

    <!-- MODAL DELETE MATERIAL SIZE -->
    <div class="modal-overlay" id="modalDeleteMaterialSize">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MASTER DATA · UKURAN MATERIAL
                    </span>
                    <br>
                    <span class="eyebrow">
                        Hapus Ukuran Material
                    </span>

                </div>

                <button type="button" class="modal-close" id="btnTutupDeleteMaterialSize" aria-label="Tutup">

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
                            Hapus ukuran material?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus seluruh ukuran
                            material ini? Data yang sudah dihapus tidak dapat
                            dikembalikan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteMaterialSize">

                    Batal

                </button>


                <button type="button" class="btn btn--danger" id="btnConfirmDeleteMaterialSize">

                    <i class="bi bi-trash3-fill"></i>


                    Hapus

                </button>

            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script>
        let materialSizeTable;

        document.addEventListener('DOMContentLoaded', function() {

            materialSizeTable = new DataTable('#materialSizeTable', {

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
                    url: "{{ route('admin_data_material_size') }}",
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
                                materialSizeTable.page.info();

                            return pageInfo.start + meta.row + 1;
                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | MATERIAL
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'material_name',
                        responsivePriority: 100,

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
                    | DIBUAT OLEH
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'created_by',
                        responsivePriority: 100,

                        render: function(data) {

                            return data ?? '-';
                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | DIBUAT PADA
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'created_at',
                        responsivePriority: 100,

                        render: function(data) {

                            if (!data) {
                                return '-';
                            }

                            return `
                <span class="data-cell-mono">
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
                        data: 'material_id',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',

                        render: function(data, type, row) {

                            return `
                <div class="data-cell-actions" style="display: flex; align-items: center; justify-content: center; gap: 5px;">

                    <!-- VIEW -->
                    <button
                        type="button"
                        class="btn--icon btn-view-material-size"
                        data-id="${data}"
                        aria-label="View">

                        <i class="bi bi-arrows-fullscreen"></i>

                    </button>


                    <!-- EDIT -->
                    <button
                        type="button"
                        class="btn--icon btn-edit-material-size"
                        data-id="${data}"
                        aria-label="Edit">

                        <i class="bi bi-pen"></i>


                    </button>


                    <!-- DELETE -->
                    <button
                        type="button"
                        class="btn--icon btn-delete-material-size"
                        data-id="${data}"
                        aria-label="Hapus">

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

                columnDefs: [

                    {
                        targets: 0,
                        className: 'dtr-control'
                    }

                ],


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

                    zeroRecords: 'Data ukuran material tidak ditemukan',

                    processing: 'Memuat data...',

                    searchPlaceholder: 'Cari material...'
                },
                initComplete: function() {
                    $(this).closest('.dt-container, .dataTables_wrapper')
                        .find('.dt-search input, .dataTables_filter input')
                        .css({
                            'padding': '9px 14px',
                            'border-radius': '8px',
                            'border': '1px solid #e2e8f0'
                        });
                }

            });

        });

        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(event) {
                const editButton = event.target.closest('.btn-edit-material-size');

                if (!editButton) {
                    return;
                }

                const id = editButton.dataset.id;

                const encodedId = btoa(id);

                window.location.href =
                    `/admin/material-sizes/${encodedId}/edit`;
            });
        })

        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(e) {

                const button = e.target.closest('.btn-view-material-size');

                if (!button) {
                    return;
                }

                const id = button.dataset.id;

                const encodedId = btoa(id);

                window.location.href =
                    `/admin/material-sizes/${encodedId}/details`;
            });
        })

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | DELETE MATERIAL SIZE
            |--------------------------------------------------------------------------
            */

            let materialIdToDelete = null;

            const modalDeleteMaterialSize = document.getElementById(
                'modalDeleteMaterialSize'
            );

            const btnTutupDeleteMaterialSize = document.getElementById(
                'btnTutupDeleteMaterialSize'
            );

            const btnBatalDeleteMaterialSize = document.getElementById(
                'btnBatalDeleteMaterialSize'
            );

            const btnConfirmDeleteMaterialSize = document.getElementById(
                'btnConfirmDeleteMaterialSize'
            );


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL DELETE
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const deleteButton = event.target.closest(
                    '.btn-delete-material-size'
                );

                if (!deleteButton) {
                    return;
                }

                materialIdToDelete = deleteButton.dataset.id;

                modalDeleteMaterialSize.classList.add('is-open');

            });


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupDeleteMaterialSizeModal() {

                modalDeleteMaterialSize.classList.remove('is-open');

                materialIdToDelete = null;

            }


            btnTutupDeleteMaterialSize.addEventListener(
                'click',
                tutupDeleteMaterialSizeModal
            );


            btnBatalDeleteMaterialSize.addEventListener(
                'click',
                tutupDeleteMaterialSizeModal
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modalDeleteMaterialSize.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalDeleteMaterialSize) {
                        tutupDeleteMaterialSizeModal();
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
                        modalDeleteMaterialSize.classList.contains('is-open')
                    ) {
                        tutupDeleteMaterialSizeModal();
                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI HAPUS MATERIAL SIZE
            |--------------------------------------------------------------------------
            */

            btnConfirmDeleteMaterialSize.addEventListener(
                'click',
                async function() {

                    /*
                    |--------------------------------------------------------------------------
                    | CEK ID MATERIAL
                    |--------------------------------------------------------------------------
                    */

                    if (!materialIdToDelete) {
                        return;
                    }


                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | REQUEST DELETE
                        |--------------------------------------------------------------------------
                        */

                        const response = await fetch(
                            `/admin/material-sizes/${materialIdToDelete}/delete`, {
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

                            tutupDeleteMaterialSizeModal();


                            /*
                            |--------------------------------------------------------------------------
                            | RELOAD DATATABLE
                            |--------------------------------------------------------------------------
                            */

                            materialSizeTable.ajax.reload(
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
                                'Ukuran material gagal dihapus.'
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
                            'Terjadi kesalahan saat menghapus ukuran material.'
                        );

                    }

                }
            );

        });

        document.addEventListener('DOMContentLoaded', function() {

            const btnImport =
                document.getElementById('btnImportMaterialSize');

            const modal =
                document.getElementById('modalImportMaterialSize');

            const btnClose =
                document.getElementById('btnCloseImportMaterialSize');

            const btnCancel =
                document.getElementById('btnCancelImportMaterialSize');

            const btnSave =
                document.getElementById('btnSaveImportMaterialSize');

            const fileInput =
                document.getElementById('fileImportMaterialSize');

            const fileName =
                document.getElementById('fileSelectedMaterialSize');


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL
            |--------------------------------------------------------------------------
            */

            if (btnImport) {

                btnImport.addEventListener('click', function() {

                    modal.classList.add('is-open');

                });

            }


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function closeModal() {

                modal.classList.remove('is-open');

            }


            btnClose.addEventListener('click', closeModal);

            btnCancel.addEventListener('click', closeModal);


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modal.addEventListener('click', function(event) {

                if (event.target === modal) {

                    closeModal();

                }

            });


            /*
            |--------------------------------------------------------------------------
            | TEKAN ESC
            |--------------------------------------------------------------------------
            */

            document.addEventListener('keydown', function(event) {

                if (
                    event.key === 'Escape' &&
                    modal.classList.contains('is-open')
                ) {

                    closeModal();

                }

            });


            /*
            |--------------------------------------------------------------------------
            | FILE DIPILIH
            |--------------------------------------------------------------------------
            */

            fileInput.addEventListener('change', function() {

                if (this.files.length > 0) {

                    fileName.textContent =
                        this.files[0].name;

                    fileName.classList.add('has-file');

                } else {

                    fileName.textContent =
                        'Belum ada file dipilih';

                    fileName.classList.remove('has-file');

                }

            });


            /*
            |--------------------------------------------------------------------------
            | IMPORT DATA
            |--------------------------------------------------------------------------
            */

            btnSave.addEventListener('click', function() {

                /*
                |--------------------------------------------------------------------------
                | VALIDASI FILE
                |--------------------------------------------------------------------------
                */

                if (!fileInput.files.length) {

                    showToast(
                        'error',
                        'Silakan pilih file Excel terlebih dahulu.'
                    );

                    return;
                }


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
                | DISABLE BUTTON
                |--------------------------------------------------------------------------
                */

                btnSave.disabled = true;

                btnSave.innerHTML = 'Mengimport...';


                /*
                |--------------------------------------------------------------------------
                | KIRIM KE CONTROLLER
                |--------------------------------------------------------------------------
                */

                fetch(
                        "{{ route('admin_import_material_size') }}", {
                            method: 'POST',

                            headers: {

                                'X-CSRF-TOKEN': "{{ csrf_token() }}",

                                'Accept': 'application/json'

                            },

                            body: formData

                        }
                    )

                    .then(response => {

                        if (!response.ok) {

                            return response.json()
                                .then(error => {

                                    throw error;

                                });

                        }

                        return response.json();

                    })

                    .then(data => {

                        if (data.success) {

                            showToast(
                                'success',
                                data.message
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | TUTUP MODAL
                            |--------------------------------------------------------------------------
                            */

                            closeModal();


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
                            | REFRESH DATATABLE
                            |--------------------------------------------------------------------------
                            */

                            if (
                                typeof materialSizeTable !==
                                'undefined'
                            ) {

                                materialSizeTable.ajax.reload(
                                    null,
                                    false
                                );

                            }

                        }

                    })

                    .catch(error => {

                        console.error(error);


                        /*
                        |--------------------------------------------------------------------------
                        | MATERIAL TIDAK TERDAFTAR
                        |--------------------------------------------------------------------------
                        */

                        if (
                            error.invalid_materials &&
                            error.invalid_materials.length > 0
                        ) {

                            const materials =
                                error.invalid_materials.join(', ');

                            showToast(
                                'error',
                                error.message +
                                ' Material: ' +
                                materials
                            );

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | VALIDATION ERROR
                        |--------------------------------------------------------------------------
                        */

                        if (error.errors) {

                            const messages =
                                Object.values(error.errors)
                                .flat()
                                .join('\n');

                            showToast(
                                'error',
                                messages
                            );

                        } else {

                            showToast(
                                'error',
                                error.message ||
                                'Terjadi kesalahan saat mengimport data.'
                            );

                        }

                    })

                    .finally(() => {

                        /*
                        |--------------------------------------------------------------------------
                        | AKTIFKAN KEMBALI BUTTON
                        |--------------------------------------------------------------------------
                        */

                        btnSave.disabled = false;

                        btnSave.innerHTML = `

                <svg viewBox="0 0 24 24">

                    <path d="M12 16V3" />

                    <path d="m7 8 5-5 5 5" />

                </svg>

                Import Data

            `;

                    });

            });

        });
    </script>
@endsection

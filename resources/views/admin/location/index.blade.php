@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">MASTER DATA · LOKASI</span>
                        {{-- <h1 class="hero-title">Lokasi</h1> --}}
                        <p class="hero-sub">Kelola dan pantau seluruh lokasi operasional yang digunakan dalam pengelolaan
                            data dan proses bisnis perusahaan.</p>
                    </div>
                    <div class="hero-actions"><button class="btn btn--ghost" id="btnExportLokasi"><svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg> Export</button>
                        <a href="{{ asset('templates/template_lokasi.xlsx') }}" class="btn btn--ghost">

                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Download Template

                        </a>
                        <button class="btn btn--ghost" id="btnImportLocation"><svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg> Import</button>
                        <button type="button" class="btn btn--primary" id="btnTambahLokasi">

                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>

                            Tambah Lokasi

                        </button>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="locationTable" class="display data-table">

                            <thead>

                                <tr>

                                    <th></th>

                                    <th>No</th>

                                    <th>Nama Lokasi</th>

                                    <th>Status</th>

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

    <!-- Modal Import location -->
    <div class="modal-overlay" id="modalImportLocation">

        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        IMPORT DATA · LOKASI
                    </span>
                    <br>
                    <span class="eyebrow">
                        Import Lokasi
                    </span>

                </div>

                <button type="button" class="modal-close" id="btnCloseImportLocation" aria-label="Tutup">

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
                        Pilih file Excel yang berisi data lokasi
                        untuk diimport ke dalam sistem.
                    </p>

                </div>


                <!-- Custom File Input -->

                <label for="fileImportLocation" class="file-upload-box" id="fileUploadBox">

                    <input type="file" id="fileImportLocation" name="file" accept=".xlsx,.xls" hidden>


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


                    <div class="file-selected-name" id="fileSelectedNameLocation">

                        Belum ada file dipilih

                    </div>

                </label>

            </div>


            <!-- Footer -->

            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCancelImportLocation">

                    Batal

                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportLocation">

                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8 5-5 5 5" />
                    </svg>

                    Import Data

                </button>

            </div>

        </div>

    </div>

    {{-- Modal Tambah lokasi --}}

    <div class="modal-overlay" id="modalTambahLokasi">

        <div class="modal-dialog">

            <form method="POST" action="{{ route('admin_store_location') }}">
                @csrf

                <div class="modal-header">
                    <div>
                        <span class="eyebrow">MASTER DATA · LOKASI</span>
                        <br>
                        <span class="eyebrow">Tambah Lokasi</span>
                    </div>

                    <button type="button" class="modal-close" id="btnTutupModalLokasi" aria-label="Tutup">
                        <svg viewBox="0 0 24 24">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="category-form-header">

                        <div>
                            <label class="category-label">
                                Nama Lokasi
                            </label>

                            <p class="category-description">
                                Tambahkan satu atau beberapa lokasi sekaligus.
                            </p>
                        </div>

                        <button type="button" class="btn btn--ghost" id="btnTambahBarisLokasi">

                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>

                            Tambah Baris

                        </button>

                    </div>


                    <div id="locationRows">

                        {{-- Baris pertama --}}

                        <div class="location-row">

                            <div class="location-row-top">

                                <input type="text" name="locations[0][name]" class="input"
                                    placeholder="Masukkan nama lokasi...">

                            </div>

                            <div class="location-row-bottom">

                                <select name="locations[0][status]" class="select select2">
                                    <option value="Active" selected>Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>

                                <button type="button" class="btn-remove-category" aria-label="Hapus lokasi">

                                    <i class="bi bi-trash3-fill"></i>

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn--ghost" id="btnBatalLokasi">
                        Batal
                    </button>

                    <button type="submit" class="btn btn--primary">
                        <svg viewBox="0 0 24 24">
                            <path d="M5 12l4 4L19 6" />
                        </svg>

                        Simpan
                    </button>

                </div>
            </form>

        </div>

    </div>

    <!-- Modal Hapus Lokasi -->

    <div class="modal-overlay" id="modalDeleteLokasi">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->

            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MASTER DATA · LOKASI
                    </span>
                    <br>
                    <span class="eyebrow">
                        Hapus Lokasi
                    </span>

                </div>


                <button type="button" class="modal-close" id="btnTutupDeleteLokasi" aria-label="Tutup">

                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">

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
                            Hapus lokasi?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus lokasi ini?
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->

            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteLokasi">

                    Batal

                </button>


                <button type="button" class="btn btn--danger" id="btnConfirmDeleteLokasi">

                    <i class="bi bi-trash3-fill"></i>

                    Hapus

                </button>

            </div>

        </div>

    </div>

    <!-- Modal Edit Lokasi -->

    <div class="modal-overlay" id="modalEditLokasi">

        <div class="modal-dialog">

            <form id="formEditLokasi">

                @csrf

                <div class="modal-header">

                    <div>

                        <span class="eyebrow">
                            MASTER DATA · LOKASI
                        </span>
                        <br>
                        <span class="eyebrow">
                            Edit Lokasi
                        </span>

                    </div>


                    <button type="button" class="modal-close" id="btnTutupEditLokasi" aria-label="Tutup">

                        <svg viewBox="0 0 24 24">

                            <path d="M18 6 6 18" />

                            <path d="m6 6 12 12" />

                        </svg>

                    </button>

                </div>


                <!-- BODY -->

                <div class="modal-body">

                    <div class="category-form-header">

                        <div>

                            <label class="category-label">
                                Nama Lokasi
                            </label>

                            <p class="category-description">
                                Perbarui informasi lokasi dan statusnya.
                            </p>

                        </div>

                    </div>


                    <!-- NAMA LOKASI -->

                    <div class="location-row">

                        <div class="location-row-top">

                            <input type="text" id="editLocationName" name="name" class="input"
                                placeholder="Masukkan nama lokasi..." required>

                        </div>


                        <!-- STATUS -->

                        <div class="location-row-bottom">

                            <select id="editLocationStatus" name="status" class="select select2" required>

                                <option value="Active">
                                    Active
                                </option>

                                <option value="Inactive">
                                    Inactive
                                </option>

                            </select>

                        </div>

                    </div>

                </div>


                <!-- FOOTER -->

                <div class="modal-footer">

                    <button type="button" class="btn btn--ghost" id="btnBatalEditLokasi">

                        Batal

                    </button>


                    <button type="submit" class="btn btn--primary" id="btnSimpanEditLokasi">

                        <svg viewBox="0 0 24 24">

                            <path d="M5 12l4 4L19 6" />

                        </svg>

                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>

    </div>
@endsection

@section('scripts')
    <script>
        let locationTable;
        document.addEventListener('DOMContentLoaded', function() {

            locationTable = new DataTable('#locationTable', {

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

                    url: "{{ route('admin_data_locations') }}",

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
                    | NOMOR
                    |--------------------------------------------------------------------------
                    */

                    {

                        data: null,

                        orderable: false,

                        searchable: false,

                        responsivePriority: 1,

                        className: 'text-center',

                        render: function(data, type, row, meta) {

                            const pageInfo = locationTable.page.info();

                            return pageInfo.start + meta.row + 1;

                        }

                    },


                    /*
                    |--------------------------------------------------------------------------
                    | NAMA LOKASI
                    |--------------------------------------------------------------------------
                    */

                    {

                        data: 'name',

                        responsivePriority: 1,

                        createdCell: function(td) {

                            td.style.whiteSpace = 'normal';

                            td.style.wordBreak = 'break-word';

                            td.style.overflowWrap = 'break-word';

                        }

                    },


                    /*
                    |--------------------------------------------------------------------------
                    | STATUS
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'status',

                        responsivePriority: 1,

                        // className: 'text-center',

                        render: function(data) {

                            const statusClass =
                                data === 'Active' ?
                                'success' :
                                'danger';

                            return `
                                <span class="badge ${statusClass} dot" style="font-weight: 400;">
                                    ${data}
                                </span>
                            `;
                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | DIBUAT OLEH
                    |--------------------------------------------------------------------------
                    */

                    {

                        data: 'created_by',

                        responsivePriority: 100

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

                            const date = new Date(data);

                            return date.toLocaleDateString('id-ID', {

                                day: '2-digit',

                                month: '2-digit',

                                year: 'numeric'

                            }).replace(/\//g, '-');

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

                        responsivePriority: 1,

                        className: 'text-center',

                        render: function(data, type, row) {

                            return `

                        <div
                            class="data-cell-actions"
                            style="
                                display: flex;
                                justify-content: center;
                                align-items: center;
                                gap: 5px;
                            "
                        >

                            <!-- EDIT -->

                            <button
                                type="button"
                                class="btn--icon btn-edit-location"
                                data-id="${data}"
                                data-name="${row.name}"
                                data-status="${row.status}"
                                aria-label="Edit"
                            >

                                <i class="bi bi-pen"></i>


                            </button>


                            <!-- HAPUS -->

                            <button
                                type="button"
                                class="btn--icon btn-delete-location"
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

                    zeroRecords: 'Data lokasi tidak ditemukan',

                    processing: 'Memuat data...',

                    searchPlaceholder: 'Cari lokasi...'

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

            const btnImport = document.getElementById('btnImportLocation');

            const modal = document.getElementById('modalImportLocation');

            const btnClose = document.getElementById('btnCloseImportLocation');

            const btnCancel = document.getElementById('btnCancelImportLocation');

            const btnSave = document.getElementById('btnSaveImportLocation');

            const fileInput = document.getElementById('fileImportLocation');

            const fileName = document.getElementById('fileSelectedNameLocation');


            /* =========================
               BUKA MODAL
            ========================= */

            btnImport.addEventListener('click', function() {

                modal.classList.add('is-open');

            });


            /* =========================
               TUTUP MODAL
            ========================= */

            function closeModal() {

                modal.classList.remove('is-open');

            }


            btnClose.addEventListener('click', closeModal);

            btnCancel.addEventListener('click', closeModal);


            /* =========================
               KLIK BACKGROUND
            ========================= */

            modal.addEventListener('click', function(event) {

                if (event.target === modal) {

                    closeModal();

                }

            });


            /* =========================
               TOMBOL ESC
            ========================= */

            document.addEventListener('keydown', function(event) {

                if (
                    event.key === 'Escape' &&
                    modal.classList.contains('is-open')
                ) {

                    closeModal();

                }

            });


            /* =========================
               FILE SELECTED
            ========================= */

            fileInput.addEventListener('change', function() {

                if (this.files.length > 0) {

                    fileName.textContent = this.files[0].name;

                    fileName.classList.add('has-file');

                } else {

                    fileName.textContent = 'Belum ada file dipilih';

                    fileName.classList.remove('has-file');

                }

            });


            /* =========================
               IMPORT DATA
            ========================= */

            btnSave.addEventListener('click', async function() {

                /*
                |--------------------------------------------------------------------------
                | VALIDASI FILE
                |--------------------------------------------------------------------------
                */

                if (fileInput.files.length === 0) {

                    showToast(
                        'error',
                        'Silakan pilih file Excel terlebih dahulu.'
                    );

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | SIAPKAN FORM DATA
                |--------------------------------------------------------------------------
                */

                const formData = new FormData();

                formData.append(
                    'file',
                    fileInput.files[0]
                );


                /*
                |--------------------------------------------------------------------------
                | IMPORT DATA
                |--------------------------------------------------------------------------
                */

                try {

                    const response = await fetch(
                        "{{ route('admin_import_location') }}", {

                            method: 'POST',

                            headers: {

                                'X-CSRF-TOKEN': document
                                    .querySelector(
                                        'meta[name="csrf-token"]'
                                    )
                                    .getAttribute('content'),

                                'Accept': 'application/json'

                            },

                            body: formData

                        }
                    );


                    const data = await response.json();


                    /*
                    |--------------------------------------------------------------------------
                    | BERHASIL
                    |--------------------------------------------------------------------------
                    */

                    if (data.success) {

                        closeModal();


                        showToast(
                            'success',
                            data.message
                        );


                        /*
                        | Reset File
                        */

                        fileInput.value = '';

                        fileName.textContent =
                            'Belum ada file dipilih';

                        fileName.classList.remove('has-file');


                        /*
                        | Reload DataTable
                        */

                        locationTable.ajax.reload(null, false);

                    } else {

                        showToast(
                            'error',
                            data.message || 'Import data gagal.'
                        );

                    }

                } catch (error) {

                    console.error(error);


                    showToast(
                        'error',
                        'Terjadi kesalahan saat mengimport data.'
                    );

                }

            });

        });

        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('modalTambahLokasi');

            const btnTambah = document.getElementById('btnTambahLokasi');

            const btnTutup = document.getElementById('btnTutupModalLokasi');

            const btnBatal = document.getElementById('btnBatalLokasi');

            const btnTambahBaris = document.getElementById('btnTambahBarisLokasi');

            const locationRows = document.getElementById('locationRows');

            let locationIndex = 1;


            /* =========================
               INIT SELECT2 UNTUK BARIS PERTAMA
            ========================= */

            $(locationRows).find('.select2').select2({
                width: '100%',
                dropdownParent: $(modal)
            });


            /* =========================
               BUKA MODAL
            ========================= */

            btnTambah.addEventListener('click', function() {

                modal.classList.add('is-open');

            });


            /* =========================
               TUTUP MODAL
            ========================= */

            function tutupModal() {

                modal.classList.remove('is-open');

            }


            btnTutup.addEventListener('click', tutupModal);

            btnBatal.addEventListener('click', tutupModal);


            /* Klik background */

            modal.addEventListener('click', function(event) {

                if (event.target === modal) {

                    tutupModal();

                }

            });


            /* Tombol ESC */

            document.addEventListener('keydown', function(event) {

                if (event.key === 'Escape' && modal.classList.contains('is-open')) {

                    tutupModal();

                }

            });


            /* =========================
               TAMBAH BARIS LOKASI
            ========================= */

            btnTambahBaris.addEventListener('click', function() {

                const row = document.createElement('div');

                row.classList.add('location-row');


                row.innerHTML = `

            <div class="location-row-top">

                <input
                    type="text"
                    name="locations[${locationIndex}][name]"
                    class="input"
                    placeholder="Masukkan nama lokasi..."
                >

            </div>

            <div class="location-row-bottom">

                <select name="locations[${locationIndex}][status]" class="select select2">
                    <option value="Active" selected>Active</option>
                    <option value="Inactive">Inactive</option>
                </select>

                <button
                    type="button"
                    class="btn-remove-category"
                    aria-label="Hapus lokasi">

                    <i class="bi bi-trash3-fill"></i>

                </button>

            </div>

        `;


                locationRows.appendChild(row);


                /* Init select2 untuk baris baru */

                $(row).find('.select2').select2({
                    width: '100%',
                    dropdownParent: $(modal)
                });


                /* Fokus ke input baru */

                row.querySelector('input').focus();


                locationIndex++;

            });


            /* =========================
               HAPUS BARIS
            ========================= */

            locationRows.addEventListener('click', function(event) {

                const removeButton = event.target.closest('.btn-remove-category');


                if (!removeButton) {

                    return;

                }


                const rows = locationRows.querySelectorAll('.location-row');


                /* Jangan hapus jika tinggal 1 */

                if (rows.length === 1) {

                    rows[0].querySelector('input').value = '';

                    $(rows[0]).find('.select2').val('Active').trigger('change');

                    rows[0].querySelector('input').focus();

                    return;

                }


                removeButton.closest('.location-row').remove();

            });

        });

        document.addEventListener('DOMContentLoaded', function() {

            let locationIdToDelete = null;


            /*
            |--------------------------------------------------------------------------
            | ELEMENT MODAL
            |--------------------------------------------------------------------------
            */

            const modalDeleteLokasi = document.getElementById(
                'modalDeleteLokasi'
            );

            const btnTutupDeleteLokasi = document.getElementById(
                'btnTutupDeleteLokasi'
            );

            const btnBatalDeleteLokasi = document.getElementById(
                'btnBatalDeleteLokasi'
            );

            const btnConfirmDeleteLokasi = document.getElementById(
                'btnConfirmDeleteLokasi'
            );


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL DELETE
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const deleteButton = event.target.closest(
                    '.btn-delete-location'
                );


                if (!deleteButton) {

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | AMBIL ID LOKASI
                |--------------------------------------------------------------------------
                */

                locationIdToDelete = deleteButton.dataset.id;


                /*
                |--------------------------------------------------------------------------
                | BUKA MODAL
                |--------------------------------------------------------------------------
                */

                modalDeleteLokasi.classList.add('is-open');

            });


            /*
            |--------------------------------------------------------------------------
            | FUNCTION TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupDeleteModal() {

                modalDeleteLokasi.classList.remove('is-open');

                locationIdToDelete = null;

            }


            /*
            |--------------------------------------------------------------------------
            | TOMBOL X
            |--------------------------------------------------------------------------
            */

            btnTutupDeleteLokasi.addEventListener(
                'click',
                tutupDeleteModal
            );


            /*
            |--------------------------------------------------------------------------
            | TOMBOL BATAL
            |--------------------------------------------------------------------------
            */

            btnBatalDeleteLokasi.addEventListener(
                'click',
                tutupDeleteModal
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modalDeleteLokasi.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalDeleteLokasi) {

                        tutupDeleteModal();

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | TOMBOL ESC
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modalDeleteLokasi.classList.contains('is-open')
                    ) {

                        tutupDeleteModal();

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI HAPUS
            |--------------------------------------------------------------------------
            */

            btnConfirmDeleteLokasi.addEventListener(
                'click',
                async function() {

                    if (!locationIdToDelete) {

                        return;

                    }


                    try {

                        const response = await fetch(

                            `/admin/locations/${locationIdToDelete}/delete`,

                            {
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


                        const result = await response.json();


                        /*
                        |--------------------------------------------------------------------------
                        | JIKA BERHASIL
                        |--------------------------------------------------------------------------
                        */

                        if (response.ok && result.success) {

                            /*
                            | Tutup Modal
                            */

                            tutupDeleteModal();


                            /*
                            | Reload DataTable
                            */

                            locationTable.ajax.reload(null, false);


                            /*
                            | Toast Success
                            */

                            showToast(
                                'success',
                                result.message
                            );

                        } else {

                            showToast(

                                'error',

                                result.message ||
                                'Lokasi gagal dihapus.'

                            );

                        }

                    } catch (error) {

                        console.error(error);

                        showToast(
                            'error',
                            'Terjadi kesalahan saat menghapus lokasi.'
                        );

                    }

                }
            );

        });

        document.addEventListener('DOMContentLoaded', function() {

            const btnExportLokasi = document.getElementById(
                'btnExportLokasi'
            );


            btnExportLokasi.addEventListener('click', function() {

                window.location.href =
                    "{{ route('admin_export_location') }}";

            });

        });

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | ELEMENT
            |--------------------------------------------------------------------------
            */

            const modalEditLokasi = document.getElementById(
                'modalEditLokasi'
            );

            const formEditLokasi = document.getElementById(
                'formEditLokasi'
            );

            const btnTutupEditLokasi = document.getElementById(
                'btnTutupEditLokasi'
            );

            const btnBatalEditLokasi = document.getElementById(
                'btnBatalEditLokasi'
            );

            const editLocationName = document.getElementById(
                'editLocationName'
            );

            const editLocationStatus = document.getElementById(
                'editLocationStatus'
            );


            /*
            |--------------------------------------------------------------------------
            | SIMPAN ID LOKASI YANG SEDANG DIEDIT
            |--------------------------------------------------------------------------
            */

            let locationIdToEdit = null;


            /*
            |--------------------------------------------------------------------------
            | INIT SELECT2
            |--------------------------------------------------------------------------
            */

            $(editLocationStatus).select2({

                width: '100%',

                dropdownParent: $(modalEditLokasi)

            });


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL EDIT
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const editButton = event.target.closest(
                    '.btn-edit-location'
                );


                if (!editButton) {

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | AMBIL DATA
                |--------------------------------------------------------------------------
                */

                locationIdToEdit = editButton.dataset.id;

                const name = editButton.dataset.name;

                const status = editButton.dataset.status;


                /*
                |--------------------------------------------------------------------------
                | ISI FORM
                |--------------------------------------------------------------------------
                */

                editLocationName.value = name;

                $(editLocationStatus)
                    .val(status)
                    .trigger('change');


                /*
                |--------------------------------------------------------------------------
                | BUKA MODAL
                |--------------------------------------------------------------------------
                */

                modalEditLokasi.classList.add('is-open');


                /*
                |--------------------------------------------------------------------------
                | FOCUS INPUT
                |--------------------------------------------------------------------------
                */

                setTimeout(function() {

                    editLocationName.focus();

                }, 100);

            });


            /*
            |--------------------------------------------------------------------------
            | FUNCTION TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupModalEditLokasi() {

                modalEditLokasi.classList.remove('is-open');

                locationIdToEdit = null;

                formEditLokasi.reset();

                $(editLocationStatus)
                    .val('Active')
                    .trigger('change');

            }


            /*
            |--------------------------------------------------------------------------
            | TOMBOL X
            |--------------------------------------------------------------------------
            */

            btnTutupEditLokasi.addEventListener(
                'click',
                tutupModalEditLokasi
            );


            /*
            |--------------------------------------------------------------------------
            | TOMBOL BATAL
            |--------------------------------------------------------------------------
            */

            btnBatalEditLokasi.addEventListener(
                'click',
                tutupModalEditLokasi
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modalEditLokasi.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalEditLokasi) {

                        tutupModalEditLokasi();

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | TOMBOL ESC
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modalEditLokasi.classList.contains('is-open')
                    ) {

                        tutupModalEditLokasi();

                    }

                }
            );

            /*
            |--------------------------------------------------------------------------
            | SIMPAN PERUBAHAN
            |--------------------------------------------------------------------------
            */

            formEditLokasi.addEventListener('submit', async function(event) {

                event.preventDefault();


                /*
                |--------------------------------------------------------------------------
                | CEK ID
                |--------------------------------------------------------------------------
                */

                if (!locationIdToEdit) {

                    showToast(
                        'error',
                        'Data lokasi tidak ditemukan.'
                    );

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | SIAPKAN DATA
                |--------------------------------------------------------------------------
                */

                const formData = new FormData();

                formData.append(
                    'name',
                    editLocationName.value
                );

                formData.append(
                    'status',
                    editLocationStatus.value
                );


                /*
                |--------------------------------------------------------------------------
                | REQUEST UPDATE
                |--------------------------------------------------------------------------
                */

                try {

                    const response = await fetch(

                        `/admin/locations/${locationIdToEdit}/edit`,

                        {

                            method: 'POST',

                            headers: {

                                'X-CSRF-TOKEN': document
                                    .querySelector(
                                        'meta[name="csrf-token"]'
                                    )
                                    .getAttribute('content'),

                                'Accept': 'application/json',

                            },

                            body: (() => {

                                formData.append('_method', 'PUT');

                                return formData;

                            })()

                        }

                    );


                    const result = await response.json();


                    /*
                    |--------------------------------------------------------------------------
                    | BERHASIL
                    |--------------------------------------------------------------------------
                    */

                    if (response.ok && result.success) {

                        /*
                        | Tutup modal
                        */

                        tutupModalEditLokasi();


                        /*
                        | Reload DataTable
                        */

                        locationTable.ajax.reload(null, false);


                        /*
                        | Toast
                        */

                        showToast(
                            'success',
                            result.message
                        );

                    } else {

                        /*
                        | Error dari controller
                        */

                        showToast(
                            'error',
                            result.message ||
                            'Data lokasi gagal diperbarui.'
                        );

                    }

                } catch (error) {

                    console.error(error);

                    showToast(
                        'error',
                        'Terjadi kesalahan saat memperbarui data lokasi.'
                    );

                }

            });

        });
    </script>
@endsection

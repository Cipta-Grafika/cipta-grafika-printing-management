@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">MASTER DATA · VENDOR</span>
                        {{-- <h1 class="hero-title">Daftar Vendor</h1> --}}
                        <p class="hero-sub">Kelola dan pantau seluruh data vendor yang menyediakan material, layanan, dan
                            kebutuhan pendukung proses produksi.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin_export_vendor') }}" class="btn btn--ghost">

                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Export

                        </a>
                        <a href="{{ asset('templates/template_vendor.xlsx') }}" class="btn btn--ghost">

                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Download Template

                        </a>
                        <button class="btn btn--ghost" id="btnImportVendor"><svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg> Import</button>

                        <button type="button" class="btn btn--primary" id="btnTambahVendor">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Vendor
                        </button>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="vendorTable" class="display data-table">

                            <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Nama Vendor</th>
                                    <th>Kontak</th>
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

    <!-- Modal Import Vendor -->
    <div class="modal-overlay" id="modalImportVendor">

        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">

                <div>
                    <span class="eyebrow">MASTER DATA · VENDOR</span>
                    <br>
                    <span class="eyebrow">Import Vendor</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseImportVendor" aria-label="Tutup">

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
                        Pilih file Excel yang berisi data vendor untuk diimport
                        ke dalam sistem.
                    </p>
                </div>


                <!-- Custom File Input -->
                <label for="fileImportVendor" class="file-upload-box" id="fileUploadBox">

                    <input type="file" id="fileImportVendor" name="file" accept=".xlsx,.xls" hidden>


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

                <button type="button" class="btn btn--ghost" id="btnCancelImportVendor">

                    Batal

                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportVendor">

                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8 5-5 5 5" />
                    </svg>

                    Import Data

                </button>

            </div>

        </div>

    </div>

    {{-- Modal Delete Vendor --}}
    <div class="modal-overlay" id="modalDeleteVendor">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->

            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MASTER DATA · VENDOR
                    </span>
                    <br>
                    <span class="eyebrow">
                        Hapus Vendor
                    </span>

                </div>


                <button type="button" class="modal-close" id="btnTutupDeleteVendor" aria-label="Tutup">

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
                            Hapus vendor?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus vendor ini?
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->

            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteVendor">

                    Batal

                </button>


                <button type="button" class="btn btn--danger" id="btnConfirmDeleteVendor">

                    <i class="bi bi-trash3-fill"></i>

                    Hapus

                </button>

            </div>

        </div>

    </div>

    {{-- Tambah vendor --}}
    <div class="modal-overlay" id="modalTambahVendor">
        <div class="modal-dialog modal-dialog--wide">

            <form method="POST" action="{{ route('admin_store_vendor') }}">
                @csrf

                <div class="modal-header">
                    <div>
                        <span class="eyebrow">
                            MASTER DATA · VENDOR
                        </span>
                        <br>
                        <span class="eyebrow">
                            Tambah Vendor
                        </span>
                    </div>

                    <button type="button" class="modal-close" id="btnTutupModalVendor" aria-label="Tutup">
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
                                Data Vendor
                            </label>

                            <p class="category-description">
                                Tambahkan satu atau beberapa vendor sekaligus.
                            </p>
                        </div>

                        <button type="button" class="btn btn--ghost" id="btnTambahBarisVendor">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Baris
                        </button>
                    </div>

                    <div id="vendorRows">

                        {{-- VENDOR PERTAMA --}}
                        <div class="vendor-row">

                            <div class="form-grid vendor-form-grid">

                                {{-- NAMA VENDOR --}}
                                <div class="field">
                                    <label class="category-label">
                                        Nama Vendor
                                    </label>

                                    <input type="text" name="vendors[0][name]" class="input"
                                        placeholder="Masukkan nama vendor..." required>
                                </div>

                                {{-- KONTAK --}}
                                <div class="field">
                                    <label class="category-label">
                                        Kontak
                                    </label>

                                    <input type="text" name="vendors[0][contact]" class="input"
                                        placeholder="Masukkan kontak vendor..." required>
                                </div>

                                {{-- STATUS --}}
                                <div class="field">
                                    <label class="category-label">
                                        Status
                                    </label>

                                    <select name="vendors[0][status]" class="select select2" required>
                                        <option value="Active" selected>
                                            Active
                                        </option>

                                        <option value="Inactive">
                                            Inactive
                                        </option>
                                    </select>
                                </div>

                                {{-- HAPUS --}}
                                <div class="field vendor-action">
                                    <label class="category-label">
                                        Aksi
                                    </label>

                                    <button type="button" class="btn-remove-category remove-vendor"
                                        aria-label="Hapus vendor">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>

                            </div>

                        </div>


                    </div>


                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn--ghost" id="btnBatalVendor">
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

    {{-- Edit Vendor --}}

    <div class="modal-overlay" id="modalEditVendor">
        <div class="modal-dialog modal-dialog--wide">

            <form method="POST" id="formEditVendor">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <div>
                        <span class="eyebrow">
                            MASTER DATA · VENDOR
                        </span>
                        <br>
                        <span class="eyebrow">
                            Edit Vendor
                        </span>
                    </div>

                    <button type="button" class="modal-close" id="btnTutupModalEditVendor" aria-label="Tutup">
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
                                Data Vendor
                            </label>

                            <p class="category-description">
                                Perbarui informasi vendor yang dipilih.
                            </p>
                        </div>
                    </div>

                    {{-- ID VENDOR --}}
                    <input type="hidden" id="edit_vendor_id">

                    <div class="form-grid vendor-form-grid">

                        {{-- NAMA VENDOR --}}
                        <div class="field">
                            <label class="category-label" for="edit_vendor_name">
                                Nama Vendor
                            </label>

                            <input type="text" id="edit_vendor_name" name="name" class="input"
                                placeholder="Masukkan nama vendor..." required>
                        </div>


                        {{-- KONTAK --}}
                        <div class="field">
                            <label class="category-label" for="edit_vendor_contact">
                                Kontak
                            </label>

                            <input type="text" id="edit_vendor_contact" name="contact" class="input"
                                placeholder="Masukkan kontak vendor..." required>
                        </div>


                        {{-- STATUS --}}
                        <div class="field">
                            <label class="category-label" for="edit_vendor_status">
                                Status
                            </label>

                            <select id="edit_vendor_status" name="status" class="select select2" required>
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

                <div class="modal-footer">

                    <button type="button" class="btn btn--ghost" id="btnBatalEditVendor">
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
@endsection

@section('scripts')
    <script>
        let vendorTable;

        document.addEventListener('DOMContentLoaded', function() {

            vendorTable = new DataTable('#vendorTable', {

                processing: true,

                serverSide: true,
                ordering: false,
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

                    url: "{{ route('admin_data_vendor') }}",

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

                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',

                        render: function(data, type, row, meta) {

                            const pageInfo = vendorTable.page.info();

                            return pageInfo.start + meta.row + 1;

                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | NAMA VENDOR
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'name',
                        responsivePriority: 2,

                        render: function(data) {

                            return `
                <div>
                    ${data}
                </div>
            `;
                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | KONTAK
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'contact',

                        // Prioritas rendah → masuk collapse lebih dulu
                        responsivePriority: 100,

                        render: function(data) {

                            return data ? data : '-';

                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | STATUS
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'status',
                        responsivePriority: 3,

                        render: function(data) {

                            const statusClass = data === 'Active' ?
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

                        // Sangat diprioritaskan agar tidak hilang
                        responsivePriority: 1,

                        render: function(data, type, row) {

                            return `
                <div class="data-cell-actions" style="width: 100%; display: flex; justify-content: center; gap: 5px;">

                    <!-- EDIT -->

                    <button
                        type="button"
                        class="btn--icon btn-edit-vendor"
                        data-id="${data}"
                        aria-label="Edit">

                        <i class="bi bi-pen"></i>


                    </button>


                    <!-- HAPUS -->

                    <button
                        type="button"
                        class="btn--icon btn-delete-vendor"
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
                | DEFAULT ORDER
                |--------------------------------------------------------------------------
                */

                order: [

                    [0, 'asc']

                ],


                /*
                |--------------------------------------------------------------------------
                | PAGE LENGTH
                |--------------------------------------------------------------------------
                */

                pageLength: 10,


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

                    zeroRecords: 'Data vendor tidak ditemukan',

                    processing: 'Memuat data...',

                    searchPlaceholder: 'Cari vendor...'

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

            const btnImport = document.getElementById('btnImportVendor');

            const modal = document.getElementById('modalImportVendor');

            const btnClose = document.getElementById(
                'btnCloseImportVendor'
            );

            const btnCancel = document.getElementById(
                'btnCancelImportVendor'
            );

            const btnSave = document.getElementById(
                'btnSaveImportVendor'
            );

            const fileInput = document.getElementById(
                'fileImportVendor'
            );

            const fileName = document.getElementById(
                'fileSelectedName'
            );


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

                /*
                | Reset File
                */

                fileInput.value = '';

                fileName.textContent = 'Belum ada file dipilih';

                fileName.classList.remove('has-file');

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
                | VALIDASI FILE
                */

                if (fileInput.files.length === 0) {

                    showToast(
                        'error',
                        'Silakan pilih file Excel terlebih dahulu.'
                    );

                    return;

                }


                /*
                | BUAT FORM DATA
                */

                const formData = new FormData();

                formData.append(
                    'file',
                    fileInput.files[0]
                );


                try {

                    /*
                    | KIRIM FILE KE LARAVEL
                    */

                    const response = await fetch(
                        "{{ route('admin_import_vendor') }}", {
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


                    /*
                    | AMBIL RESPONSE
                    */

                    const result = await response.json();


                    /*
                    | JIKA BERHASIL
                    */

                    if (response.ok && result.success) {

                        /*
                        | Tutup Modal
                        */

                        closeModal();


                        /*
                        | Reload DataTable Vendor
                        */

                        vendorTable.ajax.reload(
                            null,
                            false
                        );


                        /*
                        | Tampilkan Notifikasi
                        */

                        showToast(
                            'success',
                            result.message
                        );

                    } else {

                        /*
                        | JIKA GAGAL
                        */

                        showToast(
                            'error',
                            result.message ||
                            'Data vendor gagal diimport.'
                        );

                    }

                } catch (error) {

                    console.error(error);

                    showToast(
                        'error',
                        'Terjadi kesalahan saat mengimport data vendor.'
                    );

                }

            });

        });

        document.addEventListener('DOMContentLoaded', function() {

            let vendorIdToDelete = null;


            /*
            =====================================================

            ELEMENT MODAL

            =====================================================
            */

            const modalDeleteVendor = document.getElementById(
                'modalDeleteVendor'
            );

            const btnTutupDeleteVendor = document.getElementById(
                'btnTutupDeleteVendor'
            );

            const btnBatalDeleteVendor = document.getElementById(
                'btnBatalDeleteVendor'
            );

            const btnConfirmDeleteVendor = document.getElementById(
                'btnConfirmDeleteVendor'
            );


            /*
            =====================================================

            KONFIRMASI HAPUS

            =====================================================
            */

            btnConfirmDeleteVendor.addEventListener(
                'click',
                async function() {

                    if (!vendorIdToDelete) {

                        return;

                    }


                    try {

                        const response = await fetch(

                            `/admin/vendors/${vendorIdToDelete}/delete`,

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
                        =====================================================

                        BERHASIL

                        =====================================================
                        */

                        if (response.ok && result.success) {

                            // Tutup modal

                            tutupDeleteModal();


                            // Reload DataTable

                            vendorTable.ajax.reload(null, false);


                            // Notifikasi

                            showToast(
                                'success',
                                result.message
                            );

                        }


                        /*
                        =====================================================

                        GAGAL

                        =====================================================
                        */
                        else {

                            showToast(

                                'error',

                                result.message ||
                                'Vendor gagal dihapus.'

                            );

                        }

                    } catch (error) {

                        console.error(error);

                        showToast(

                            'error',

                            'Terjadi kesalahan saat menghapus vendor.'

                        );

                    }

                }
            );


            /*
            =====================================================

            BUKA MODAL DELETE

            =====================================================
            */

            document.addEventListener(
                'click',
                function(event) {

                    const deleteButton = event.target.closest(
                        '.btn-delete-vendor'
                    );


                    if (!deleteButton) {

                        return;

                    }


                    vendorIdToDelete = deleteButton.dataset.id;


                    modalDeleteVendor.classList.add('is-open');

                }
            );


            /*
            =====================================================

            FUNCTION TUTUP MODAL

            =====================================================
            */

            function tutupDeleteModal() {

                modalDeleteVendor.classList.remove('is-open');

                vendorIdToDelete = null;

            }


            /*
            =====================================================

            TOMBOL X

            =====================================================
            */

            btnTutupDeleteVendor.addEventListener(
                'click',
                tutupDeleteModal
            );


            /*
            =====================================================

            TOMBOL BATAL

            =====================================================
            */

            btnBatalDeleteVendor.addEventListener(
                'click',
                tutupDeleteModal
            );


            /*
            =====================================================

            KLIK BACKGROUND

            =====================================================
            */

            modalDeleteVendor.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalDeleteVendor) {

                        tutupDeleteModal();

                    }

                }
            );


            /*
            =====================================================

            TOMBOL ESC

            =====================================================
            */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (

                        event.key === 'Escape' &&

                        modalDeleteVendor.classList.contains('is-open')

                    ) {

                        tutupDeleteModal();

                    }

                }
            );

        });

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | ELEMENT MODAL EDIT
            |--------------------------------------------------------------------------
            */

            const modalEdit = document.getElementById('modalEditVendor');
            const formEdit = document.getElementById('formEditVendor');

            const btnTutup = document.getElementById(
                'btnTutupModalEditVendor'
            );

            const btnBatal = document.getElementById(
                'btnBatalEditVendor'
            );

            const editId = document.getElementById(
                'edit_vendor_id'
            );

            const editName = document.getElementById(
                'edit_vendor_name'
            );

            const editContact = document.getElementById(
                'edit_vendor_contact'
            );

            const editStatus = document.getElementById(
                'edit_vendor_status'
            );


            /*
            |--------------------------------------------------------------------------
            | CEK MODAL
            |--------------------------------------------------------------------------
            */

            if (
                !modalEdit ||
                !formEdit ||
                !editId ||
                !editName ||
                !editContact ||
                !editStatus
            ) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | INIT SELECT2
            |--------------------------------------------------------------------------
            */

            $(editStatus).select2({
                width: '100%',
                dropdownParent: $(modalEdit)
            });


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL
            |--------------------------------------------------------------------------
            */

            function openEditModal() {
                modalEdit.classList.add('is-open');
            }


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function closeEditModal() {
                modalEdit.classList.remove('is-open');

                /*
                |--------------------------------------------------------------------------
                | RESET FORM
                |--------------------------------------------------------------------------
                */

                editId.value = '';
                editName.value = '';
                editContact.value = '';

                $(editStatus)
                    .val('Active')
                    .trigger('change');

                /*
                |--------------------------------------------------------------------------
                | RESET ACTION FORM
                |--------------------------------------------------------------------------
                */

                formEdit.removeAttribute('action');
            }


            /*
            |--------------------------------------------------------------------------
            | EVENT TOMBOL TUTUP
            |--------------------------------------------------------------------------
            */

            btnTutup?.addEventListener('click', function() {
                closeEditModal();
            });


            /*
            |--------------------------------------------------------------------------
            | EVENT TOMBOL BATAL
            |--------------------------------------------------------------------------
            */

            btnBatal?.addEventListener('click', function() {
                closeEditModal();
            });


            /*
            |--------------------------------------------------------------------------
            | KLIK AREA OVERLAY
            |--------------------------------------------------------------------------
            */

            modalEdit.addEventListener('click', function(event) {

                if (event.target === modalEdit) {
                    closeEditModal();
                }

            });


            /*
            |--------------------------------------------------------------------------
            | ESCAPE
            |--------------------------------------------------------------------------
            */

            document.addEventListener('keydown', function(event) {

                if (
                    event.key === 'Escape' &&
                    modalEdit.classList.contains('is-open')
                ) {
                    closeEditModal();
                }

            });


            /*
            |--------------------------------------------------------------------------
            | EVENT TOMBOL EDIT
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const editButton = event.target.closest(
                    '.btn-edit-vendor'
                );

                if (!editButton) {
                    return;
                }

                const id = editButton.dataset.id;

                if (!id) {
                    showToast(
                        'error',
                        'ID vendor tidak ditemukan.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | AMBIL DATA VENDOR
                |--------------------------------------------------------------------------
                */

                fetch(`/admin/vendors/${id}/edit`)
                    .then(response => {

                        if (!response.ok) {
                            throw new Error(
                                'Data vendor tidak ditemukan.'
                            );
                        }

                        return response.json();
                    })

                    .then(vendor => {

                        /*
                        |--------------------------------------------------------------------------
                        | ISI DATA FORM
                        |--------------------------------------------------------------------------
                        */

                        editId.value = vendor.id;

                        editName.value =
                            vendor.name ?? '';

                        editContact.value =
                            vendor.contact ?? '';


                        /*
                        |--------------------------------------------------------------------------
                        | SET STATUS SELECT2
                        |--------------------------------------------------------------------------
                        */

                        $(editStatus)
                            .val(vendor.status)
                            .trigger('change');


                        /*
                        |--------------------------------------------------------------------------
                        | SET ACTION FORM
                        |--------------------------------------------------------------------------
                        */

                        formEdit.action =
                            `/admin/vendors/${vendor.id}/update`;


                        /*
                        |--------------------------------------------------------------------------
                        | BUKA MODAL
                        |--------------------------------------------------------------------------
                        */

                        openEditModal();

                    })

                    .catch(error => {

                        console.error(error);

                        showToast(
                            'error',
                            'Data vendor gagal dimuat.'
                        );

                    });

            });


            /*
            |--------------------------------------------------------------------------
            | SUBMIT FORM
            |--------------------------------------------------------------------------
            */

            formEdit.addEventListener('submit', function(event) {

                /*
                |--------------------------------------------------------------------------
                | VALIDASI ID
                |--------------------------------------------------------------------------
                */

                if (!editId.value) {

                    event.preventDefault();

                    showToast(
                        'error',
                        'Data vendor tidak ditemukan.'
                    );

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | PASTIKAN ACTION TERISI
                |--------------------------------------------------------------------------
                */

                if (!formEdit.action) {

                    event.preventDefault();

                    showToast(
                        'error',
                        'Tujuan update vendor tidak ditemukan.'
                    );

                    return;
                }

            });

        });


        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('modalTambahVendor');
            const btnTambah = document.getElementById('btnTambahVendor');
            const btnTutup = document.getElementById('btnTutupModalVendor');
            const btnBatal = document.getElementById('btnBatalVendor');
            const btnTambahBaris = document.getElementById('btnTambahBarisVendor');
            const vendorRows = document.getElementById('vendorRows');

            if (!modal || !vendorRows) {
                return;
            }

            let vendorIndex = 1;

            /* ============================================================
               INIT SELECT2
            ============================================================ */

            function initSelect2(element) {

                $(element).find('.select2').each(function() {

                    if (!$(this).hasClass('select2-hidden-accessible')) {

                        $(this).select2({
                            width: '100%',
                            dropdownParent: $(modal)
                        });

                    }

                });

            }

            /* ============================================================
               UPDATE BUTTON HAPUS
            ============================================================ */

            function updateRemoveButtons() {

                const rows = vendorRows.querySelectorAll('.vendor-row');

                rows.forEach(function(row) {

                    const button = row.querySelector('.remove-vendor');

                    if (!button) {
                        return;
                    }

                    button.disabled = rows.length === 1;

                });

            }

            /* ============================================================
               BUKA MODAL
            ============================================================ */

            btnTambah?.addEventListener('click', function() {

                modal.classList.add('is-open');

                initSelect2(modal);

                updateRemoveButtons();

            });

            /* ============================================================
               TUTUP MODAL
            ============================================================ */

            function closeModal() {
                modal.classList.remove('is-open');
            }

            btnTutup?.addEventListener('click', closeModal);

            btnBatal?.addEventListener('click', closeModal);

            /* ============================================================
               KLIK BACKDROP
            ============================================================ */

            modal.addEventListener('click', function(event) {

                if (event.target === modal) {
                    closeModal();
                }

            });

            /* ============================================================
               ESCAPE
            ============================================================ */

            document.addEventListener('keydown', function(event) {

                if (
                    event.key === 'Escape' &&
                    modal.classList.contains('is-open')
                ) {
                    closeModal();
                }

            });

            /* ============================================================
               TAMBAH BARIS VENDOR
            ============================================================ */

            btnTambahBaris?.addEventListener('click', function() {

                const row = document.createElement('div');

                row.classList.add('vendor-row');

                row.innerHTML = `
                    <div class="form-grid vendor-form-grid" style="margin-top: 20px;">

                        {{-- NAMA VENDOR --}}
                        <div class="field">
                            <label class="category-label">
                                Nama Vendor
                            </label>

                            <input
                                type="text"
                                name="vendors[${vendorIndex}][name]"
                                class="input"
                                placeholder="Masukkan nama vendor..."
                                required
                            >
                        </div>

                        {{-- KONTAK --}}
                        <div class="field">
                            <label class="category-label">
                                Kontak
                            </label>

                            <input
                                type="text"
                                name="vendors[${vendorIndex}][contact]"
                                class="input"
                                placeholder="Masukkan kontak vendor..."
                                required
                            >
                        </div>

                        {{-- STATUS --}}
                        <div class="field">
                            <label class="category-label">
                                Status
                            </label>

                            <select
                                name="vendors[${vendorIndex}][status]"
                                class="select select2"
                                required
                            >
                                <option value="Active" selected>
                                    Active
                                </option>

                                <option value="Inactive">
                                    Inactive
                                </option>
                            </select>
                        </div>

                        {{-- HAPUS --}}
                        <div class="field vendor-action">
                            <label class="category-label">
                                Aksi
                            </label>

                            <button
                                type="button"
                                class="btn-remove-category remove-vendor"
                                aria-label="Hapus vendor"
                            >
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>

                    </div>
                `;

                vendorRows.appendChild(row);

                /*
                |--------------------------------------------------------------------------
                | INIT SELECT2 ROW BARU
                |--------------------------------------------------------------------------
                */

                initSelect2(row);

                /*
                |--------------------------------------------------------------------------
                | TAMBAH INDEX
                |--------------------------------------------------------------------------
                */

                vendorIndex++;

                /*
                |--------------------------------------------------------------------------
                | UPDATE BUTTON HAPUS
                |--------------------------------------------------------------------------
                */

                updateRemoveButtons();

                /*
                |--------------------------------------------------------------------------
                | FOCUS KE NAMA VENDOR
                |--------------------------------------------------------------------------
                */

                const nameInput = row.querySelector(
                    'input[name*="[name]"]'
                );

                nameInput?.focus();

            });



            /* ============================================================
               HAPUS BARIS VENDOR
            ============================================================ */

            vendorRows.addEventListener('click', function(event) {

                const removeButton =
                    event.target.closest('.remove-vendor');

                if (!removeButton || removeButton.disabled) {
                    return;
                }

                const vendorRow =
                    removeButton.closest('.vendor-row');

                if (!vendorRow) {
                    return;
                }

                /* --------------------------------------------------------
                   DESTROY SELECT2
                -------------------------------------------------------- */

                $(vendorRow).find('.select2').each(function() {

                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }

                });

                /* --------------------------------------------------------
                   HAPUS ROW
                -------------------------------------------------------- */

                vendorRow.remove();

                updateRemoveButtons();

            });

        });
    </script>
@endsection

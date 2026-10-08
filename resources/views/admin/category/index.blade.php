@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">MASTER DATA · KATEGORI</span>
                        <p class="hero-sub">Kelola dan pantau seluruh data material yang digunakan dalam proses produksi dan
                            perhitungan estimasi harga.</p>
                    </div>
                    <div class="hero-actions"><button class="btn btn--ghost" id="btnExportKategori"><svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg> Export</button>
                        <a href="{{ asset('templates/template_kategori.xlsx') }}" class="btn btn--ghost">

                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Download Template

                        </a>
                        <button type="button" class="btn btn--ghost" id="btnImportKategori">

                            <svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg>

                            Import

                        </button>
                        <button class="btn btn--primary" id="btnTambahKategori">
                            <svg viewBox="0 0 24 24">
                                <!-- Folder -->
                                <path d="M3 7a2 2 0 0 1 2-2h5l2 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />

                                <!-- Tanda Plus -->
                                <path d="M12 11v6" />
                                <path d="M9 14h6" />
                            </svg>
                            Tambah Kategori
                        </button>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="categoryTable" class="display data-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Nama Kategori</th>
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

    <div class="modal-overlay" id="modalTambahKategori">

        <div class="modal-dialog">

            <form method="POST" action="{{ route('admin_store_categories') }}">
                @csrf
                <div class="modal-header">
                    <div>
                        <span class="eyebrow">MASTER DATA · KATEGORI</span>
                        <br>
                        <span class="eyebrow" style="text-transform: uppercase;">Tambah Kategori</span>
                    </div>

                    <button type="button" class="modal-close" id="btnTutupModal" aria-label="Tutup">
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
                                Nama Kategori
                            </label>

                            <p class="category-description">
                                Tambahkan satu atau beberapa kategori sekaligus.
                            </p>
                        </div>

                        <button type="button" class="btn btn--ghost" id="btnTambahBarisKategori">

                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>

                            Tambah Baris

                        </button>

                    </div>


                    <div id="categoryRows">

                        <!-- Baris pertama -->

                        <div class="category-row">

                            <input type="text" name="categories[]" class="input"
                                placeholder="Masukkan nama kategori...">

                            <button type="button" class="btn-remove-category" aria-label="Hapus kategori">

                                <i class="bi bi-trash3-fill"></i>

                            </button>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn--ghost" id="btnBatalKategori">
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

    <!-- Modal Edit Kategori -->
    <div class="modal-overlay" id="modalEditKategori">

        <div class="modal-dialog">

            <form id="formEditKategori">

                <div class="modal-header">

                    <div>

                        <span class="eyebrow">
                            MASTER DATA · KATEGORI
                        </span>
                        <br>
                        <span class="eyebrow">
                            Edit Kategori
                        </span>

                    </div>


                    <button type="button" class="modal-close" id="btnTutupEditModal" aria-label="Tutup">

                        <svg viewBox="0 0 24 24">

                            <path d="M18 6 6 18" />

                            <path d="m6 6 12 12" />

                        </svg>

                    </button>

                </div>


                <div class="modal-body">

                    <div class="category-form-header">

                        <div>

                            <label for="editCategoryName" class="category-label">

                                Nama Kategori

                            </label>


                            <p class="category-description">

                                Perbarui nama kategori yang dipilih.

                            </p>

                        </div>

                    </div>


                    <div class="category-row">

                        <input type="text" id="editCategoryName" name="name" class="input"
                            placeholder="Masukkan nama kategori..." autocomplete="off">

                    </div>

                </div>


                <div class="modal-footer">

                    <button type="button" class="btn btn--ghost" id="btnBatalEditKategori">

                        Batal

                    </button>


                    <button type="submit" class="btn btn--primary">

                        <svg viewBox="0 0 24 24">

                            <path d="M5 12l4 4L19 6" />

                        </svg>

                        Simpan Perubahan

                    </button>

                </div>

            </form>

        </div>

    </div>

    <div class="modal-overlay" id="modalImportKategori">

        <div class="modal modal-import">

            <!-- HEADER -->

            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MASTER DATA · KATEGORI
                    </span>
                    <br>
                    <span class="eyebrow">
                        Import Kategori
                    </span>

                </div>


                <button type="button" class="modal-close" id="btnCloseImportKategori" aria-label="Tutup">

                    <svg viewBox="0 0 24 24">

                        <path d="M18 6 6 18" />

                        <path d="m6 6 12 12" />

                    </svg>

                </button>

            </div>


            <!-- BODY -->

            <div class="modal-body">

                <div class="import-description">

                    <p>
                        Pilih file Excel yang berisi data kategori
                        untuk diimport ke dalam sistem.
                    </p>

                </div>


                <!-- CUSTOM FILE INPUT -->

                <label for="fileImportKategori" class="file-upload-box" id="fileUploadBox">

                    <input type="file" id="fileImportKategori" name="file" accept=".xlsx,.xls" hidden>


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


                    <div class="file-selected-name" id="fileSelectedName">

                        Belum ada file dipilih

                    </div>

                </label>

            </div>


            <!-- FOOTER -->

            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCancelImportKategori">

                    Batal

                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportKategori">

                    <svg viewBox="0 0 24 24">

                        <path d="M12 16V3" />

                        <path d="m7 8 5-5 5 5" />

                    </svg>

                    Import Data

                </button>

            </div>

        </div>

    </div>

    <!-- =========================================================
                                                                                                                                                                                                                                                                                                                                                                                                                             MODAL KONFIRMASI HAPUS KATEGORI
                                                                                                                                                                                                                                                                                                                                                                                                                        ========================================================= -->

    <div class="modal-overlay" id="modalDeleteKategori">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->

            <div class="modal-header">

                <div>
                    <span class="eyebrow">
                        MASTER DATA · KATEGORI
                    </span>
                    <br>
                    <span class="eyebrow">
                        Hapus Kategori
                    </span>
                </div>

                <button type="button" class="modal-close" id="btnTutupDeleteModal" aria-label="Tutup">

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
                            Hapus kategori?
                        </h3>
                        <p>
                            Apakah kamu yakin ingin menghapus kategori ini?
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->

            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDelete">

                    Batal

                </button>


                <button type="button" class="btn btn--danger" id="btnConfirmDelete">

                    <i class="bi bi-trash3-fill"></i>

                    Hapus

                </button>

            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script>
        let categoryTable;
        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('modalTambahKategori');

            const btnTambah = document.getElementById('btnTambahKategori');

            const btnTutup = document.getElementById('btnTutupModal');

            const btnBatal = document.getElementById('btnBatalKategori');

            const btnTambahBaris = document.getElementById('btnTambahBarisKategori');

            const categoryRows = document.getElementById('categoryRows');


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

                if (event.key === 'Escape') {

                    tutupModal();

                }

            });


            /* =========================
               TAMBAH BARIS KATEGORI
            ========================= */

            btnTambahBaris.addEventListener('click', function() {

                const row = document.createElement('div');

                row.classList.add('category-row');


                row.innerHTML = `

            <input
                type="text"
                name="categories[]"
                class="input"
                placeholder="Masukkan nama kategori..."
            >

            <button
                type="button"
                class="btn-remove-category"
                aria-label="Hapus kategori">

                <i class="bi bi-trash3-fill"></i>

            </button>

        `;


                categoryRows.appendChild(row);


                /* Fokus ke input baru */

                row.querySelector('input').focus();

            });


            /* =========================
               HAPUS BARIS
            ========================= */

            categoryRows.addEventListener('click', function(event) {

                const removeButton = event.target.closest('.btn-remove-category');


                if (!removeButton) {

                    return;

                }


                const rows = categoryRows.querySelectorAll('.category-row');


                /* Jangan hapus jika tinggal 1 */

                if (rows.length === 1) {

                    rows[0].querySelector('input').value = '';

                    rows[0].querySelector('input').focus();

                    return;

                }


                removeButton.closest('.category-row').remove();

            });

        });

        // document.addEventListener('DOMContentLoaded', function() {

        //     const btnImport = document.getElementById('btnImportMaterial');

        //     const modal = document.getElementById('modalImportMaterial');

        //     const btnClose = document.getElementById('btnCloseImportMaterial');

        //     const btnCancel = document.getElementById('btnCancelImportMaterial');

        //     const fileInput = document.getElementById('fileImportMaterial');

        //     const fileName = document.getElementById('fileSelectedName');


        //     /* =========================
        //        BUKA MODAL
        //     ========================= */

        //     btnImport.addEventListener('click', function() {

        //         modal.classList.add('is-open');

        //     });


        //     /* =========================
        //        TUTUP MODAL
        //     ========================= */

        //     function closeModal() {

        //         modal.classList.remove('is-open');

        //     }


        //     btnClose.addEventListener('click', closeModal);

        //     btnCancel.addEventListener('click', closeModal);


        //     /* Klik background */

        //     modal.addEventListener('click', function(event) {

        //         if (event.target === modal) {

        //             closeModal();

        //         }

        //     });


        //     /* Tombol ESC */

        //     document.addEventListener('keydown', function(event) {

        //         if (event.key === 'Escape') {

        //             closeModal();

        //         }

        //     });


        //     /* =========================
        //        FILE SELECTED
        //     ========================= */

        //     fileInput.addEventListener('change', function() {

        //         if (this.files.length > 0) {

        //             fileName.textContent = this.files[0].name;

        //             fileName.classList.add('has-file');

        //         } else {

        //             fileName.textContent = 'Belum ada file dipilih';

        //             fileName.classList.remove('has-file');

        //         }

        //     });

        // });

        document.addEventListener('DOMContentLoaded', function() {

            const btnImport = document.getElementById('btnImportKategori');

            const modal = document.getElementById('modalImportKategori');

            const btnClose = document.getElementById('btnCloseImportKategori');

            const btnCancel = document.getElementById('btnCancelImportKategori');

            const btnSave = document.getElementById('btnSaveImportKategori');

            const fileInput = document.getElementById('fileImportKategori');

            const fileName = document.getElementById('fileSelectedName');


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


            /* Klik Background */

            modal.addEventListener('click', function(event) {

                if (event.target === modal) {

                    closeModal();

                }

            });


            /* Tombol ESC */

            document.addEventListener('keydown', function(event) {

                if (event.key === 'Escape') {

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
                        "{{ route('admin_import_category') }}", {
                            method: 'POST',

                            headers: {

                                'X-CSRF-TOKEN': document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),

                                'Accept': 'application/json'

                            },

                            body: formData

                        }
                    );


                    const data = await response.json();


                    /*
                    |--------------------------------------------------------------------------
                    | JIKA BERHASIL
                    |--------------------------------------------------------------------------
                    */

                    if (data.success) {

                        // Tutup modal
                        closeModal();


                        // Tampilkan notifikasi
                        showToast(
                            'success',
                            data.message
                        );


                        // Reset input file
                        fileInput.value = '';

                        fileName.textContent = 'Belum ada file dipilih';

                        fileName.classList.remove('has-file');


                        // Reload DataTable tanpa reload halaman
                        categoryTable.ajax.reload(null, false);

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

            categoryTable = new DataTable('#categoryTable', {

                processing: true,

                serverSide: true,
                ordering: false,
                stripeClasses: [],
                responsive: {
                    details: {
                        type: 'column',
                        target: 0
                    }
                },

                ajax: {
                    url: "{{ route('admin_categories_data') }}",
                    type: "GET"
                },

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

                            const pageInfo = categoryTable.page.info();

                            return pageInfo.start + meta.row + 1;

                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | NAMA KATEGORI
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

                            <div class="data-cell-actions" style="display: flex; justify-content: center; align-items: center; gap: 5px;">

                                <button
                                    type="button"
                                    class="btn--icon btn-edit-category"
                                    data-id="${data}"
                                    data-name="${row.name}"
                                    aria-label="Edit">

                                    <i class="bi bi-pen"></i>

                                </button>


                                <button
                                    type="button"
                                    class="btn--icon btn-delete-category"
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
                | DEFAULT ORDER
                |--------------------------------------------------------------------------
                */

                order: [
                    [1, 'asc']
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
                | LAYOUT DATATABLES 3
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

                    searchPlaceholder: 'Cari kategori...',

                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',

                    infoEmpty: 'Showing 0 to 0 of 0 entries',

                    zeroRecords: 'Data kategori tidak ditemukan',

                    processing: 'Memuat data...'

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

            let categoryIdToDelete = null;


            const modalDeleteKategori = document.getElementById(
                'modalDeleteKategori'
            );

            const btnTutupDeleteModal = document.getElementById(
                'btnTutupDeleteModal'
            );

            const btnBatalDelete = document.getElementById(
                'btnBatalDelete'
            );

            const btnConfirmDelete = document.getElementById(
                'btnConfirmDelete'
            );

            btnConfirmDelete.addEventListener('click', async function() {

                if (!categoryIdToDelete) {
                    return;
                }

                try {

                    const response = await fetch(
                        `/admin/categories/${categoryIdToDelete}`, {
                            method: 'DELETE',

                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).getAttribute('content'),

                                'Accept': 'application/json'
                            }
                        }
                    );


                    const result = await response.json();


                    if (response.ok && result.success) {

                        console.log(result.message);

                        // Tutup modal
                        tutupDeleteModal();


                        // Nanti reload DataTable di sini
                        categoryTable.ajax.reload(null, false);
                        showToast('success', result.message);

                    } else {

                        showToast(
                            'error',
                            result.message || 'Kategori gagal dihapus.'
                        );

                    }

                } catch (error) {

                    console.error(error);

                    alert('Terjadi kesalahan saat menghapus kategori.');

                }

            });


            /* =====================================================
               BUKA MODAL DELETE
            ===================================================== */

            document.addEventListener('click', function(event) {

                const deleteButton = event.target.closest(
                    '.btn-delete-category'
                );

                if (!deleteButton) {
                    return;
                }


                categoryIdToDelete = deleteButton.dataset.id;


                modalDeleteKategori.classList.add('is-open');

            });


            /* =====================================================
               FUNCTION TUTUP MODAL
            ===================================================== */

            function tutupDeleteModal() {

                modalDeleteKategori.classList.remove('is-open');

                categoryIdToDelete = null;

            }


            /* =====================================================
               TOMBOL X
            ===================================================== */

            btnTutupDeleteModal.addEventListener(
                'click',
                tutupDeleteModal
            );


            /* =====================================================
               TOMBOL BATAL
            ===================================================== */

            btnBatalDelete.addEventListener(
                'click',
                tutupDeleteModal
            );


            /* =====================================================
               KLIK BACKGROUND
            ===================================================== */

            modalDeleteKategori.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalDeleteKategori) {

                        tutupDeleteModal();

                    }

                }
            );


            /* =====================================================
               TOMBOL ESC
            ===================================================== */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modalDeleteKategori.classList.contains('is-open')
                    ) {

                        tutupDeleteModal();

                    }

                }
            );
        });

        document.addEventListener('DOMContentLoaded', function() {

            const modalEdit = document.getElementById('modalEditKategori');

            const btnTutupEdit = document.getElementById('btnTutupEditModal');

            const btnBatalEdit = document.getElementById('btnBatalEditKategori');

            const inputEditName = document.getElementById('editCategoryName');


            /*
            |--------------------------------------------------------------------------
            | ID KATEGORI YANG SEDANG DIEDIT
            |--------------------------------------------------------------------------
            */

            let editCategoryId = null;


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL EDIT
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const editButton = event.target.closest('.btn-edit-category');

                if (!editButton) {
                    return;
                }


                editCategoryId = editButton.dataset.id;

                const categoryName = editButton.dataset.name;


                // Masukkan nama kategori lama

                inputEditName.value = categoryName;


                // Buka modal

                modalEdit.classList.add('is-open');


                // Fokus ke input

                setTimeout(function() {

                    inputEditName.focus();

                }, 200);

            });


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupEditModal() {

                modalEdit.classList.remove('is-open');

                editCategoryId = null;

                inputEditName.value = '';

            }


            /*
            |--------------------------------------------------------------------------
            | TOMBOL CLOSE
            |--------------------------------------------------------------------------
            */

            btnTutupEdit.addEventListener('click', tutupEditModal);


            /*
            |--------------------------------------------------------------------------
            | TOMBOL BATAL
            |--------------------------------------------------------------------------
            */

            btnBatalEdit.addEventListener('click', tutupEditModal);


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modalEdit.addEventListener('click', function(event) {

                if (event.target === modalEdit) {

                    tutupEditModal();

                }

            });


            /*
            |--------------------------------------------------------------------------
            | TOMBOL ESC
            |--------------------------------------------------------------------------
            */

            document.addEventListener('keydown', function(event) {

                if (event.key === 'Escape') {

                    if (modalEdit.classList.contains('is-open')) {

                        tutupEditModal();

                    }

                }

            });

            /*
            |--------------------------------------------------------------------------
            | SUBMIT UPDATE KATEGORI
            |--------------------------------------------------------------------------
            */

            const formEditKategori = document.getElementById('formEditKategori');


            formEditKategori.addEventListener('submit', async function(event) {

                event.preventDefault();


                const categoryName = inputEditName.value.trim();


                /*
                |--------------------------------------------------------------------------
                | VALIDASI FRONTEND
                |--------------------------------------------------------------------------
                */

                if (!categoryName) {

                    showToast(
                        'error',
                        'Nama kategori tidak boleh kosong.'
                    );

                    inputEditName.focus();

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | KIRIM REQUEST UPDATE
                |--------------------------------------------------------------------------
                */

                try {

                    const response = await fetch(
                        `/admin/categories/${editCategoryId}`, {

                            method: 'PUT',

                            headers: {

                                'Content-Type': 'application/json',

                                'Accept': 'application/json',

                                'X-CSRF-TOKEN': document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')

                            },

                            body: JSON.stringify({

                                name: categoryName

                            })

                        }
                    );


                    const result = await response.json();


                    /*
                    |--------------------------------------------------------------------------
                    | JIKA GAGAL
                    |--------------------------------------------------------------------------
                    */

                    if (!response.ok) {

                        throw new Error(
                            result.message || 'Terjadi kesalahan.'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | JIKA BERHASIL
                    |--------------------------------------------------------------------------
                    */

                    if (result.success) {

                        // Tutup modal

                        tutupEditModal();


                        // Reload DataTable TANPA reload halaman

                        categoryTable.ajax.reload(null, false);


                        // Toast berhasil

                        showToast('success', result.message);

                    }

                } catch (error) {

                    showToast(
                        'error',
                        error.message
                    );

                }

            });

        });

        document.addEventListener('DOMContentLoaded', function() {

            const btnExportKategori = document.getElementById('btnExportKategori');


            btnExportKategori.addEventListener('click', function() {

                window.location.href = "{{ route('admin_export_category') }}";

            });

        });

        document.addEventListener('DOMContentLoaded', function() {
            const btnSaveImport = document.getElementById('btnSaveImportKategori');

            btnSaveImport.addEventListener('click', function() {

                const fileInput = document.getElementById('fileImportKategori');


                // Pastikan file dipilih

                if (!fileInput.files.length) {

                    alert('Silakan pilih file Excel terlebih dahulu.');

                    return;

                }


                // Buat FormData

                const formData = new FormData();

                formData.append('file', fileInput.files[0]);


                // Kirim ke Laravel

                fetch("{{ route('admin_import_category') }}", {

                        method: 'POST',

                        headers: {

                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]'
                            ).getAttribute('content'),

                        },

                        body: formData,

                    })

                    .then(response => response.text())

                    .then(data => {

                        console.log(data);

                    })

                    .catch(error => {

                        console.error(error);

                    });

            });
        });
    </script>
@endsection

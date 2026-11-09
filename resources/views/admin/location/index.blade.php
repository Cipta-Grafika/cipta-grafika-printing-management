@extends('admin_master')
@section('contents')
    <link rel="stylesheet" href="{{ asset('css/location-index.css') }}">
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero cat-hero-compact">
                    <div class="hero-text"><span class="eyebrow">MASTER DATA · LOKASI</span>
                        {{-- <h1 class="hero-title">Lokasi</h1> --}}
                        <p class="hero-sub">Kelola dan pantau seluruh lokasi operasional yang digunakan dalam pengelolaan
                            data dan proses bisnis perusahaan.</p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card cat-card-container">
                        <div class="cat-card-header">
                            <div class="cat-card-header-main">
                                <div class="cat-card-title-wrap">
                                    <div class="cat-card-icon"><i class="bi bi-geo-alt-fill"></i></div>
                                    <div>
                                        <div class="cat-title-row">
                                            <h3 class="cat-card-title">Daftar Lokasi</h3>
                                            <span class="cat-badge-total"><span class="cat-pulse-dot"></span><span id="locationTotalCount">Memuat data...</span></span>
                                        </div>
                                        <p class="cat-card-desc">Master lokasi operasional untuk pengelolaan data dan proses bisnis.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="cat-card-actions">
                                <button type="button" class="cat-btn-head" id="btnExportLokasi" title="Export Excel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg><span>Export</span></button>
                                <a href="{{ asset('templates/template_lokasi.xlsx') }}" class="cat-btn-head" title="Download Template Excel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg><span>Template</span></a>
                                <button type="button" class="cat-btn-head" id="btnImportLocation" title="Import Excel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 16V3"/><path d="m7 8 5-5 5 5"/><path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6"/></svg><span>Import</span></button>
                                <button type="button" class="cat-btn-head-primary" id="btnTambahLokasi"><i class="bi bi-plus-lg"></i><span>Tambah Lokasi</span></button>
                            </div>
                        </div>
                        <div class="cat-bulk-toolbar" id="locationBulkToolbar" style="display: none;">
                            <div class="cat-bulk-info"><span class="cat-bulk-badge" id="locationBulkCount">0 terpilih</span><span class="cat-bulk-text">Lokasi yang dipilih siap untuk tindakan massal</span></div>
                            <div class="cat-bulk-actions">
                                <button type="button" class="btn btn--ghost btn--sm" id="cancelLocationSelection"><i class="bi bi-x-circle me-1"></i> Batal Pilihan</button>
                                <button type="button" class="btn btn--danger btn--sm" id="bulkDeleteLocations"><i class="bi bi-trash3-fill me-1"></i> Hapus Terpilih</button>
                            </div>
                        </div>
                        <div class="cat-filter-card">
                            <div class="cat-filter-group-left">
                                <div class="cat-search-box">
                                    <i class="bi bi-search cat-search-icon"></i>
                                    <input type="text" id="locationSearch" class="cat-filter-input" placeholder="Cari nama, status, atau pembuat..." aria-label="Cari lokasi">
                                    <button type="button" id="clearLocationSearch" class="cat-clear-btn" title="Hapus pencarian" style="display: none;"><i class="bi bi-x-circle-fill"></i></button>
                                </div>
                                <div class="cat-select-box">
                                    <i class="bi bi-person-fill cat-select-icon"></i>
                                    <select id="locationCreator" class="select2" style="width: 100%;" aria-label="Filter pembuat">
                                        <option value="all">Semua Pembuat</option>
                                        @foreach ($creators as $creator)
                                            <option value="{{ $creator }}">{{ $creator }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="cat-filter-subgroup">
                                    <div class="cat-select-box">
                                        <i class="bi bi-calendar3 cat-select-icon"></i>
                                        <select id="locationPeriod" class="select2" style="width: 100%;" aria-label="Filter periode">
                                            <option value="all">Semua Periode</option><option value="today">Hari Ini</option><option value="7_days">7 Hari Terakhir</option><option value="month">Bulan Ini</option><option value="year">Tahun Ini</option>
                                        </select>
                                    </div>
                                    <button type="button" id="resetLocationFilters" class="cat-btn-reset" title="Reset semua filter"><i class="bi bi-arrow-counterclockwise"></i><span>Reset</span></button>
                                </div>
                            </div>
                            <div class="cat-filter-group-right"><div class="cat-length-box"><span class="cat-length-label">Baris:</span>
                                <select id="locationPageLength" class="select2" style="width: 76px;" aria-label="Jumlah baris"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select>
                            </div></div>
                        </div>
                        <div class="cat-table-wrap">
                            <table id="locationTable" class="cat-modern-table" style="width: 100%;" data-url="{{ route('admin_data_locations') }}" data-bulk-url="{{ route('admin_bulk_destroy_locations') }}">
                                <thead><tr>
                                    <th class="cat-th-check text-center"><label class="cat-custom-checkbox" title="Pilih semua di halaman ini"><input type="checkbox" id="checkAllLocations" aria-label="Pilih semua lokasi di halaman ini"><span class="cat-checkbox-mark"><svg class="check-icon" viewBox="0 0 14 14" fill="none"><path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#fff" stroke-width="2.2"/></svg><svg class="minus-icon" viewBox="0 0 14 14" fill="none"><path d="M3 7H11" stroke="#fff" stroke-width="2.2"/></svg></span></label></th>
                                    <th class="cat-th-no text-center" style="width: 60px;">No</th>
                                    <th style="min-width: 220px;">Nama Lokasi</th>
                                    <th style="min-width: 120px;">Status</th>
                                    <th style="min-width: 170px;">Dibuat Oleh</th>
                                    <th style="min-width: 150px;">Dibuat Pada</th>
                                    <th class="text-center" style="width: 110px;">Aksi</th>
                                </tr></thead><tbody></tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </main>
            <div data-shell-footer></div>
        </div>
    </div>

    <div class="modal-overlay" id="modalBulkDeleteLocations">
        <div class="modal-dialog modal-delete">
            <div class="modal-header">
                <div><span class="eyebrow">MASTER DATA · LOKASI</span><br><span class="eyebrow">Hapus Lokasi Terpilih</span></div>
                <button type="button" class="modal-close" id="closeBulkDeleteLocations" aria-label="Tutup"><svg viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
            </div>
            <div class="modal-body"><div class="delete-confirmation"><div class="delete-icon"><i class="bi bi-trash3-fill"></i></div><div class="delete-content"><h3>Hapus lokasi terpilih?</h3><p id="bulkDeleteLocationDescription"></p><p>Lokasi yang masih digunakan oleh data lain tidak dapat dihapus.</p></div></div></div>
            <div class="modal-footer"><button type="button" class="btn btn--ghost" id="cancelBulkDeleteLocations">Batal</button><button type="button" class="btn btn--danger" id="confirmBulkDeleteLocations"><i class="bi bi-trash3-fill"></i> Hapus Terpilih</button></div>
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
    <script src="{{ asset('js/location-index.js') }}"></script>
    <script>
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

                            selectedLocationIds.delete(Number(locationIdToDelete));

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

@extends('admin_master')
@section('contents')
    <link rel="stylesheet" href="{{ asset('css/engine-index.css') }}">
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero cat-hero-compact">
                    <div class="hero-text"><span class="eyebrow">MASTER DATA · MESIN</span>
                        {{-- <h1 class="hero-title">Daftar Mesin</h1> --}}
                        <p class="hero-sub">Kelola dan pantau seluruh data mesin yang digunakan dalam proses produksi.</p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card cat-card-container">
                        <div class="cat-card-header">
                            <div class="cat-card-header-main">
                                <div class="cat-card-title-wrap">
                                    <div class="cat-card-icon"><i class="bi bi-printer-fill"></i></div>
                                    <div>
                                        <div class="cat-title-row">
                                            <h3 class="cat-card-title">Daftar Mesin</h3>
                                            <span class="cat-badge-total"><span class="cat-pulse-dot"></span><span id="engineTotalCount">Memuat data...</span></span>
                                        </div>
                                        <p class="cat-card-desc">Master mesin yang digunakan dalam proses produksi.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="cat-card-actions">
                                <a href="{{ route('admin_export_engine') }}" class="cat-btn-head" title="Export Excel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg><span>Export</span></a>
                                <a href="{{ asset('templates/template_mesin.xlsx') }}" class="cat-btn-head" title="Download Template Excel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg><span>Template</span></a>
                                <button type="button" class="cat-btn-head" id="btnImportEngine" title="Import Excel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 16V3"/><path d="m7 8 5-5 5 5"/><path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6"/></svg><span>Import</span></button>
                                <button type="button" class="cat-btn-head-primary" id="btnTambahMesin"><i class="bi bi-plus-lg"></i><span>Tambah Mesin</span></button>
                            </div>
                        </div>
                        <div class="cat-bulk-toolbar" id="engineBulkToolbar" style="display: none;">
                            <div class="cat-bulk-info"><span class="cat-bulk-badge" id="engineBulkCount">0 terpilih</span><span class="cat-bulk-text">Mesin yang dipilih siap untuk tindakan massal</span></div>
                            <div class="cat-bulk-actions">
                                <button type="button" class="btn btn--ghost btn--sm" id="cancelEngineSelection"><i class="bi bi-x-circle me-1"></i> Batal Pilihan</button>
                                <button type="button" class="btn btn--danger btn--sm" id="bulkDeleteEngines"><i class="bi bi-trash3-fill me-1"></i> Hapus Terpilih</button>
                            </div>
                        </div>
                        <div class="cat-filter-card">
                            <div class="cat-filter-group-left">
                                <div class="cat-search-box">
                                    <i class="bi bi-search cat-search-icon"></i>
                                    <input type="text" id="engineSearch" class="cat-filter-input" placeholder="Cari nama, status, atau pembuat..." aria-label="Cari mesin">
                                    <button type="button" id="clearEngineSearch" class="cat-clear-btn" title="Hapus pencarian" aria-label="Hapus pencarian" style="display: none;"><i class="bi bi-x-circle-fill"></i></button>
                                </div>
                                <div class="cat-select-box">
                                    <i class="bi bi-person-fill cat-select-icon"></i>
                                    <select id="engineCreator" class="select2" style="width: 100%;" aria-label="Filter pembuat">
                                        <option value="all">Semua Pembuat</option>
                                        @foreach ($creators as $creator)
                                            <option value="{{ $creator }}">{{ $creator }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="cat-filter-subgroup">
                                    <div class="cat-select-box">
                                        <i class="bi bi-calendar3 cat-select-icon"></i>
                                        <select id="enginePeriod" class="select2" style="width: 100%;" aria-label="Filter periode">
                                            <option value="all">Semua Periode</option><option value="today">Hari Ini</option><option value="7_days">7 Hari Terakhir</option><option value="month">Bulan Ini</option><option value="year">Tahun Ini</option>
                                        </select>
                                    </div>
                                    <button type="button" id="resetEngineFilters" class="cat-btn-reset" title="Reset semua filter"><i class="bi bi-arrow-counterclockwise"></i><span>Reset</span></button>
                                </div>
                            </div>
                            <div class="cat-filter-group-right"><div class="cat-length-box"><span class="cat-length-label">Baris:</span>
                                <select id="enginePageLength" class="select2" style="width: 76px;" aria-label="Jumlah baris"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select>
                            </div></div>
                        </div>
                        <div class="cat-table-wrap">
                            <table id="engineTable" class="cat-modern-table" style="width: 100%;" data-url="{{ route('admin_data_engine') }}" data-bulk-url="{{ route('admin_bulk_destroy_engines') }}">
                                <thead><tr>
                                    <th class="cat-th-check text-center"><label class="cat-custom-checkbox" title="Pilih semua di halaman ini"><input type="checkbox" id="checkAllEngines" aria-label="Pilih semua mesin di halaman ini"><span class="cat-checkbox-mark"><svg class="check-icon" viewBox="0 0 14 14" fill="none"><path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#fff" stroke-width="2.2"/></svg><svg class="minus-icon" viewBox="0 0 14 14" fill="none"><path d="M3 7H11" stroke="#fff" stroke-width="2.2"/></svg></span></label></th>
                                    <th class="cat-th-no text-center" style="width: 60px;">No</th>
                                    <th style="min-width: 220px;">Nama Mesin</th>
                                    <th style="min-width: 150px;">Minimum Charge</th>
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

    <div class="modal-overlay" id="modalBulkDeleteEngines">
        <div class="modal-dialog modal-delete">
            <div class="modal-header">
                <div><span class="eyebrow">MASTER DATA · MESIN</span><br><span class="eyebrow">Hapus Mesin Terpilih</span></div>
                <button type="button" class="modal-close" id="closeBulkDeleteEngines" aria-label="Tutup"><svg viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
            </div>
            <div class="modal-body"><div class="delete-confirmation"><div class="delete-icon"><i class="bi bi-trash3-fill"></i></div><div class="delete-content"><h3>Hapus mesin terpilih?</h3><p id="bulkDeleteEngineDescription"></p><p>Mesin yang masih digunakan oleh data lain tidak dapat dihapus.</p></div></div></div>
            <div class="modal-footer"><button type="button" class="btn btn--ghost" id="cancelBulkDeleteEngines">Batal</button><button type="button" class="btn btn--danger" id="confirmBulkDeleteEngines"><i class="bi bi-trash3-fill"></i> Hapus Terpilih</button></div>
        </div>
    </div>

    <!-- Modal Import Engine -->
    <div class="modal-overlay" id="modalImportEngine">

        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">

                <div>
                    <span class="eyebrow">MASTER DATA · MESIN</span>
                    <br>
                    <span class="eyebrow">Import Mesin</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseImportEngine" aria-label="Tutup">

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
                <label for="fileImportEngine" class="file-upload-box" id="fileUploadBox">

                    <input type="file" id="fileImportEngine" name="file" accept=".xlsx,.xls" hidden>


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

                <button type="button" class="btn btn--ghost" id="btnCancelImportEngine">

                    Batal

                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportEngine">

                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8 5-5 5 5" />
                    </svg>

                    Import Data

                </button>

            </div>

        </div>

    </div>

    <!-- MODAL DELETE ENGINE -->
    <div class="modal-overlay" id="modalDeleteEngine">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->

            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MASTER DATA · MESIN
                    </span>
                    <br>
                    <span class="eyebrow">
                        Hapus Mesin
                    </span>

                </div>


                <button type="button" class="modal-close" id="btnTutupDeleteEngine" aria-label="Tutup">

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
                            Hapus mesin?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus mesin ini?
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->

            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteEngine">

                    Batal

                </button>


                <button type="button" class="btn btn--danger" id="btnConfirmDeleteEngine">

                    <i class="bi bi-trash3-fill"></i>

                    Hapus

                </button>

            </div>

        </div>

    </div>

    {{-- Modal Tambah Mesin --}}
    <div class="modal-overlay" id="modalTambahMesin">
        <div class="modal-dialog modal-dialog--wide">

            <form method="POST" action="{{ route('admin_store_engine') }}">
                @csrf

                <div class="modal-header">
                    <div>
                        <span class="eyebrow">MASTER DATA · MESIN</span>
                        <br>
                        <span class="eyebrow">Tambah Mesin</span>
                    </div>

                    <button type="button" class="modal-close" id="btnTutupModalMesin" aria-label="Tutup">
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
                                Data Mesin
                            </label>

                            <p class="category-description">
                                Tambahkan satu atau beberapa mesin sekaligus.
                            </p>
                        </div>

                        <button type="button" class="btn btn--ghost" id="btnTambahBarisMesin">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Baris
                        </button>
                    </div>

                    <div id="engineRows">

                        {{-- Baris pertama --}}
                        <div class="engine-row">

                            <div class="engine-row-top">

                                <div class="engine-field">
                                    <label class="category-label">
                                        Nama Mesin
                                    </label>

                                    <input type="text" name="engines[0][name]" class="input"
                                        placeholder="Masukkan nama mesin..." required>
                                </div>

                                <div class="engine-field">
                                    <label class="category-label">
                                        Minimum Charge
                                    </label>

                                    <input type="text" name="engines[0][minimum_charge]" class="input rupiah-input"
                                        inputmode="decimal" placeholder="Masukkan minimum charge...">
                                </div>

                            </div>

                            <div class="engine-row-bottom">

                                <select name="engines[0][status]" class="select select2" required>
                                    <option value="Active" selected>
                                        Active
                                    </option>
                                    <option value="Inactive">
                                        Inactive
                                    </option>
                                </select>

                                <button type="button" class="btn-remove-category" aria-label="Hapus mesin">
                                    <i class="bi bi-trash3-fill"></i>
                                </button>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button" class="btn btn--ghost" id="btnBatalMesin">
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

    {{-- Modal Edit Mesin --}}
    <div class="modal-overlay" id="modalEditMesin">
        <div class="modal-dialog modal-dialog--wide">

            <form method="POST" action="{{ route('admin_update_engine', ['id' => '__ID__']) }}" id="formEditMesin">
                @csrf
                @method('PUT')

                <div class="modal-header">

                    <div>
                        <span class="eyebrow">
                            MASTER DATA · MESIN
                        </span>
                        <br>
                        <span class="eyebrow">
                            Edit Mesin
                        </span>
                    </div>

                    <button type="button" class="modal-close" id="btnTutupModalEditMesin" aria-label="Tutup">
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
                                Data Mesin
                            </label>

                            <p class="category-description">
                                Perbarui informasi mesin.
                            </p>
                        </div>
                    </div>

                    <div class="engine-row">

                        <div class="engine-row-top">

                            {{-- NAMA MESIN --}}
                            <div class="engine-field">

                                <label class="category-label">
                                    Nama Mesin
                                </label>

                                <input type="text" name="name" id="editEngineName" class="input"
                                    placeholder="Masukkan nama mesin..." required>

                            </div>

                            {{-- MINIMUM CHARGE --}}
                            <div class="engine-field">

                                <label class="category-label">
                                    Minimum Charge
                                </label>

                                <input type="text" name="minimum_charge" id="editEngineMinimumCharge" class="input"
                                    inputmode="decimal" placeholder="Masukkan minimum charge...">

                            </div>

                        </div>

                        <div class="engine-row-bottom">

                            {{-- STATUS --}}
                            <select name="status" id="editEngineStatus" class="select select2" required>
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

                    <button type="button" class="btn btn--ghost" id="btnBatalEditMesin">
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
@endsection

@section('scripts')
    <script src="{{ asset('js/engine-index.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const btnImport = document.getElementById('btnImportEngine');
            const modal = document.getElementById('modalImportEngine');

            const btnClose = document.getElementById('btnCloseImportEngine');
            const btnCancel = document.getElementById('btnCancelImportEngine');
            const btnSave = document.getElementById('btnSaveImportEngine');

            const fileInput = document.getElementById('fileImportEngine');
            const fileName = document.getElementById('fileSelectedName');


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

                if (event.key === 'Escape') {

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

                    fileName.textContent = this.files[0].name;

                    fileName.classList.add('has-file');

                } else {

                    fileName.textContent = 'Belum ada file dipilih';

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

                    // alert('Silakan pilih file Excel terlebih dahulu.');
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

                formData.append('file', fileInput.files[0]);


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

                fetch("{{ route('admin_import_engine') }}", {

                        method: 'POST',

                        headers: {

                            'X-CSRF-TOKEN': "{{ csrf_token() }}",

                            'Accept': 'application/json'

                        },

                        body: formData

                    })

                    .then(response => {

                        if (!response.ok) {

                            return response.json().then(error => {

                                throw error;

                            });

                        }

                        return response.json();

                    })

                    .then(data => {

                        if (data.success) {

                            // alert(data.message);
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

                            fileName.textContent = 'Belum ada file dipilih';

                            fileName.classList.remove('has-file');


                            /*
                            |--------------------------------------------------------------------------
                            | REFRESH DATATABLE
                            |--------------------------------------------------------------------------
                            */

                            if (typeof engineTable !== 'undefined') {

                                engineTable.ajax.reload(null, false);

                            }

                        }

                    })

                    .catch(error => {

                        console.error(error);


                        /*
                        |--------------------------------------------------------------------------
                        | VALIDATION ERROR
                        |--------------------------------------------------------------------------
                        */

                        if (error.errors) {

                            const messages = Object.values(error.errors)
                                .flat()
                                .join('\n');

                            // alert(messages);
                            showToast(
                                'error',
                                messages
                            );

                        } else {

                            // alert(
                            //     error.message ||
                            //     'Terjadi kesalahan saat mengimport data.'
                            // );

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

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | MODAL EDIT MESIN
            |--------------------------------------------------------------------------
            */
            const modalEditMesin =
                document.getElementById('modalEditMesin');

            const formEditMesin =
                document.getElementById('formEditMesin');

            const btnTutupModalEditMesin =
                document.getElementById('btnTutupModalEditMesin');

            const btnBatalEditMesin =
                document.getElementById('btnBatalEditMesin');

            const editEngineName =
                document.getElementById('editEngineName');

            const editEngineMinimumCharge =
                document.getElementById('editEngineMinimumCharge');

            const editEngineStatus =
                document.getElementById('editEngineStatus');


            /*
            |--------------------------------------------------------------------------
            | SELECT2
            |--------------------------------------------------------------------------
            */
            if (editEngineStatus) {
                $(editEngineStatus).select2({
                    width: '100%',
                    dropdownParent: $(modalEditMesin)
                });
            }


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL EDIT
            |--------------------------------------------------------------------------
            */
            document.addEventListener('click', function(event) {

                const button =
                    event.target.closest('.btn-edit-engine');

                if (!button) {
                    return;
                }

                event.preventDefault();

                const id = button.dataset.id;

                if (!id) {
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | AMBIL DATA DARI DATATABLE
                |--------------------------------------------------------------------------
                */
                const rowData = engineTable
                    .rows()
                    .data()
                    .toArray()
                    .find(function(row) {
                        return String(row.id) === String(id);
                    });

                if (!rowData) {
                    console.error('Data mesin tidak ditemukan:', id);
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | ISI FORM
                |--------------------------------------------------------------------------
                */
                editEngineName.value =
                    rowData.name ?? '';

                editEngineMinimumCharge.value =
                    rowData.minimum_charge !== null &&
                    rowData.minimum_charge !== undefined &&
                    rowData.minimum_charge !== '' ?
                    parseInt(rowData.minimum_charge, 10) :
                    '';


                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */
                $(editEngineStatus)
                    .val(rowData.status ?? 'Active')
                    .trigger('change');


                /*
                |--------------------------------------------------------------------------
                | ACTION FORM
                |--------------------------------------------------------------------------
                */
                formEditMesin.action =
                    `{{ url('/admin/engines') }}/${id}/edit`;


                /*
                |--------------------------------------------------------------------------
                | BUKA MODAL
                |--------------------------------------------------------------------------
                */
                modalEditMesin.classList.add('is-open');


                /*
                |--------------------------------------------------------------------------
                | TUTUP MODAL
                |--------------------------------------------------------------------------
                */
                function tutupModalEditMesin() {

                    modalEditMesin.classList.remove('is-open');

                }


                /*
                |--------------------------------------------------------------------------
                | BUTTON CLOSE
                |--------------------------------------------------------------------------
                */
                if (btnTutupModalEditMesin) {

                    btnTutupModalEditMesin.addEventListener(
                        'click',
                        tutupModalEditMesin
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | BUTTON BATAL
                |--------------------------------------------------------------------------
                */
                if (btnBatalEditMesin) {

                    btnBatalEditMesin.addEventListener(
                        'click',
                        tutupModalEditMesin
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | KLIK BACKDROP
                |--------------------------------------------------------------------------
                */
                if (modalEditMesin) {

                    modalEditMesin.addEventListener(
                        'click',
                        function(event) {

                            if (event.target === modalEditMesin) {
                                tutupModalEditMesin();
                            }

                        }
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | ESCAPE
                |--------------------------------------------------------------------------
                */
                document.addEventListener(
                    'keydown',
                    function(event) {

                        if (
                            event.key === 'Escape' &&
                            modalEditMesin &&
                            modalEditMesin.classList.contains('is-open')
                        ) {
                            tutupModalEditMesin();
                        }

                    }
                );

            });


        });

        document.addEventListener('DOMContentLoaded', function() {

            let engineIdToDelete = null;


            /*
            =====================================================
            ELEMENT MODAL
            =====================================================
            */

            const modalDeleteEngine = document.getElementById(
                'modalDeleteEngine'
            );

            const btnTutupDeleteEngine = document.getElementById(
                'btnTutupDeleteEngine'
            );

            const btnBatalDeleteEngine = document.getElementById(
                'btnBatalDeleteEngine'
            );

            const btnConfirmDeleteEngine = document.getElementById(
                'btnConfirmDeleteEngine'
            );


            /*
            =====================================================
            KONFIRMASI HAPUS
            =====================================================
            */

            btnConfirmDeleteEngine.addEventListener(
                'click',

                async function() {

                    if (!engineIdToDelete) {

                        return;

                    }


                    try {

                        const response = await fetch(

                            `/admin/engines/${engineIdToDelete}/delete`,

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

                            selectedEngineIds.delete(Number(engineIdToDelete));

                            /*
                            =====================================================
                            TUTUP MODAL
                            =====================================================
                            */

                            tutupDeleteModal();


                            /*
                            =====================================================
                            RELOAD DATATABLE
                            =====================================================
                            */

                            engineTable.ajax.reload(
                                null,
                                false
                            );


                            /*
                            =====================================================
                            NOTIFIKASI
                            =====================================================
                            */

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
                                'Mesin gagal dihapus.'

                            );

                        }

                    } catch (error) {

                        console.error(error);


                        showToast(

                            'error',

                            'Terjadi kesalahan saat menghapus mesin.'

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

                        '.btn-delete-engine'

                    );


                    if (!deleteButton) {

                        return;

                    }


                    /*
                    =====================================================
                    AMBIL ID MESIN
                    =====================================================
                    */

                    engineIdToDelete = deleteButton.dataset.id;


                    /*
                    =====================================================
                    BUKA MODAL
                    =====================================================
                    */

                    modalDeleteEngine.classList.add('is-open');

                }

            );


            /*
            =====================================================
            FUNCTION TUTUP MODAL
            =====================================================
            */

            function tutupDeleteModal() {

                modalDeleteEngine.classList.remove('is-open');

                engineIdToDelete = null;

            }


            /*
            =====================================================
            TOMBOL X
            =====================================================
            */

            btnTutupDeleteEngine.addEventListener(

                'click',

                tutupDeleteModal

            );


            /*
            =====================================================
            TOMBOL BATAL
            =====================================================
            */

            btnBatalDeleteEngine.addEventListener(

                'click',

                tutupDeleteModal

            );


            /*
            =====================================================
            KLIK BACKGROUND
            =====================================================
            */

            modalDeleteEngine.addEventListener(

                'click',

                function(event) {

                    if (event.target === modalDeleteEngine) {

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

                        modalDeleteEngine.classList.contains('is-open')

                    ) {

                        tutupDeleteModal();

                    }

                }

            );

        });

        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('modalTambahMesin');
            const btnTambah = document.getElementById('btnTambahMesin');
            const btnTutup = document.getElementById('btnTutupModalMesin');
            const btnBatal = document.getElementById('btnBatalMesin');
            const btnTambahBaris = document.getElementById('btnTambahBarisMesin');
            const engineRows = document.getElementById('engineRows');

            let engineIndex = 1;

            if (!modal || !btnTambah || !engineRows) {
                return;
            }

            // =========================
            // INIT SELECT2 BARIS PERTAMA
            // =========================

            $(engineRows).find('.select2').select2({
                width: '100%',
                dropdownParent: $(modal)
            });

            // =========================
            // BUKA MODAL
            // =========================

            btnTambah.addEventListener('click', function() {
                modal.classList.add('is-open');
            });

            // =========================
            // TUTUP MODAL
            // =========================

            function tutupModal() {
                modal.classList.remove('is-open');
            }

            btnTutup.addEventListener('click', tutupModal);
            btnBatal.addEventListener('click', tutupModal);

            // =========================
            // KLIK BACKGROUND
            // =========================

            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    tutupModal();
                }
            });

            // =========================
            // ESC
            // =========================

            document.addEventListener('keydown', function(event) {
                if (
                    event.key === 'Escape' &&
                    modal.classList.contains('is-open')
                ) {
                    tutupModal();
                }
            });

            // =========================
            // TAMBAH BARIS MESIN
            // =========================

            btnTambahBaris.addEventListener('click', function() {

                const row = document.createElement('div');

                row.classList.add('engine-row');

                row.innerHTML = `
            <div class="engine-row-top">

                <div class="engine-field">
                    <label class="category-label">
                        Nama Mesin
                    </label>

                    <input
                        type="text"
                        name="engines[${engineIndex}][name]"
                        class="input"
                        placeholder="Masukkan nama mesin..."
                        required
                    >
                </div>

                <div class="engine-field">
                    <label class="category-label">
                        Minimum Charge
                    </label>

                    <input
                        type="text"
                        name="engines[${engineIndex}][minimum_charge]"
                        class="input rupiah-input"
                        inputmode="decimal"
                        placeholder="Masukkan minimum charge..."
                    >
                </div>

            </div>

            <div class="engine-row-bottom">

                <select
                    name="engines[${engineIndex}][status]"
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

                <button
                    type="button"
                    class="btn-remove-category"
                    aria-label="Hapus mesin"
                >
                    <i class="bi bi-trash3-fill"></i>
                </button>

            </div>
        `;

                engineRows.appendChild(row);

                // Init Select2
                $(row).find('.select2').select2({
                    width: '100%',
                    dropdownParent: $(modal)
                });

                // Fokus ke nama mesin
                row.querySelector('input[name*="[name]"]').focus();

                engineIndex++;
            });

            // =========================
            // HAPUS BARIS
            // =========================

            engineRows.addEventListener('click', function(event) {

                const removeButton = event.target.closest(
                    '.btn-remove-category'
                );

                if (!removeButton) {
                    return;
                }

                const rows = engineRows.querySelectorAll('.engine-row');

                // Jangan hapus jika tinggal satu
                if (rows.length === 1) {

                    rows[0]
                        .querySelector('input[name*="[name]"]')
                        .value = '';

                    rows[0]
                        .querySelector(
                            'input[name*="[minimum_charge]"]'
                        )
                        .value = '';

                    $(rows[0])
                        .find('.select2')
                        .val('Active')
                        .trigger('change');

                    rows[0]
                        .querySelector('input[name*="[name]"]')
                        .focus();

                    return;
                }

                removeButton
                    .closest('.engine-row')
                    .remove();
            });

        });
    </script>
@endsection

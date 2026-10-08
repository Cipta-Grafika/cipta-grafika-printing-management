@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">MASTER DATA · MESIN</span>
                        {{-- <h1 class="hero-title">Daftar Mesin</h1> --}}
                        <p class="hero-sub">Kelola dan pantau seluruh data mesin yang digunakan dalam proses produksi.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin_export_engine') }}" class="btn btn--ghost">

                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Export

                        </a>
                        <a href="{{ asset('templates/template_mesin.xlsx') }}" class="btn btn--ghost">

                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Download Template

                        </a>
                        <button class="btn btn--ghost" id="btnImportEngine"><svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg> Import</button>

                        <button type="button" class="btn btn--primary" id="btnTambahMesin">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Mesin
                        </button>


                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">

                        <table id="engineTable" class="display data-table">

                            <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Nama Mesin</th>
                                    <th>Minimum Charge</th>
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

        let engineTable;

        document.addEventListener('DOMContentLoaded', function() {

            engineTable = new DataTable('#engineTable', {

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

                    url: "{{ route('admin_data_engine') }}",

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

                            const pageInfo = engineTable.page.info();

                            return pageInfo.start + meta.row + 1;
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | NAMA MESIN
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'name',
                        responsivePriority: 1,

                        render: function(data) {

                            return `
                                <div>
                                    ${data ?? '-'}
                                </div>
                            `;
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | MINIMUM CHARGE
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'minimum_charge',
                        responsivePriority: 2,

                        render: function(data) {

                            if (
                                data === null ||
                                data === undefined ||
                                data === ''
                            ) {
                                return '-';
                            }

                            const number = Number(data);

                            return `
                                <span>
                                    ${Number.isInteger(number) ? number : number.toString()} m²
                                </span>
                            `;
                        }
                    },



                    /*
                    |--------------------------------------------------------------------------
                    | STATUS
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'status',
                        responsivePriority: 2,

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
                        responsivePriority: 1,
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
                                        class="btn--icon btn-edit-engine"
                                        data-id="${data}"
                                        aria-label="Edit"
                                    >
                                        <i class="bi bi-pen"></i>
                                    </button>

                                    <!-- DELETE -->
                                    <button
                                        type="button"
                                        class="btn--icon btn-delete-engine"
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

                    zeroRecords: 'Data mesin tidak ditemukan',

                    processing: 'Memuat data...',

                    searchPlaceholder: 'Cari mesin...'
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

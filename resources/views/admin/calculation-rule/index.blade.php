@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">PERHITUNGAN · ATURAN PERHITUNGAN</span>
                        {{-- <h1 class="hero-title">Aturan Perhitungan</h1> --}}
                        <p class="hero-sub">Kelola dan pantau seluruh daftar harga yang digunakan sebagai acuan dalam
                            penentuan harga jual dan perhitungan estimasi harga.</p>
                    </div>
                    <div class="hero-actions"><a href="{{ route('admin_export_calculation_rule') }}"
                            class="btn btn--ghost"><svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg> Export</a>
                        <a href="{{ asset('templates/template_aturan_perhitungan.xlsx') }}" class="btn btn--ghost">

                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Download Template

                        </a>
                        <button class="btn btn--ghost" id="btnImportCalculationRule"><svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg> Import</button>
                        <a href="{{ route('admin_create_calculation_rule') }}" class="btn btn--primary"><svg
                                viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg> Tambah Perhitungan</a>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="calculationRuleTable" class="display data-table">

                            <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Aturan</th>
                                    <th>Mesin</th>
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

    <!-- Modal Import CalculationRule -->
    <div class="modal-overlay" id="modalImportCalculationRule">

        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">

                <div>
                    <span class="eyebrow">PERHITUNGAN · ATURAN PERHITUNGAN</span>
                    <br>
                    <h2 class="eyebrow">Import Perhitungan</h2>
                </div>

                <button type="button" class="modal-close" id="btnCloseImportCalculationRule" aria-label="Tutup">

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
                        Pilih file Excel yang berisi data aturan perhitungan
                        untuk diimport ke dalam sistem.
                    </p>
                </div>


                <!-- Custom File Input -->
                <label for="fileImportCalculationRule" class="file-upload-box" id="fileUploadBox">

                    <input type="file" id="fileImportCalculationRule" name="file" accept=".xlsx,.xls" hidden>


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

                <button type="button" class="btn btn--ghost" id="btnCancelImportCalculationRule">

                    Batal

                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportCalculationRule">

                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8 5-5 5 5" />
                    </svg>

                    Import Data

                </button>

            </div>

        </div>

    </div>

    <!-- Modal Delete Calculation Rule -->
    <div class="modal-overlay" id="modalDeleteCalculationRule">

        <div class="modal">

            <!-- Header -->
            <div class="modal-header">

                <div>
                    <span class="eyebrow">
                        HARGA & TARIF · ATURAN PERHITUNGAN
                    </span>

                    <br>

                    <span class="eyebrow">
                        Hapus Aturan Perhitungan
                    </span>
                </div>

                <button type="button" class="modal-close" id="btnCloseDeleteCalculationRule" aria-label="Tutup">

                    <svg viewBox="0 0 24 24">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
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
                            Hapus aturan perhitungan?
                        </h3>

                        <p>
                            Apakah Anda yakin ingin menghapus aturan perhitungan ini?
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- Footer -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCancelDeleteCalculationRule">
                    Batal
                </button>

                <button type="button" class="btn btn--danger" id="btnConfirmDeleteCalculationRule">

                    <i class="bi bi-trash3-fill"></i>

                    Hapus

                </button>

            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const btnImport =
                document.getElementById('btnImportCalculationRule');

            const modal =
                document.getElementById('modalImportCalculationRule');

            const btnClose =
                document.getElementById('btnCloseImportCalculationRule');

            const btnCancel =
                document.getElementById('btnCancelImportCalculationRule');

            const btnSave =
                document.getElementById('btnSaveImportCalculationRule');

            const fileInput =
                document.getElementById('fileImportCalculationRule');

            const fileName =
                document.getElementById('fileSelectedName');


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

                if (event.key === 'Escape') {
                    closeModal();
                }

            });


            /* =========================
               FILE SELECTED
            ========================= */

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


            /* =========================
               IMPORT DATA
            ========================= */

            btnSave.addEventListener('click', async function() {

                const file = fileInput.files[0];

                /* =========================
                   VALIDASI FILE
                ========================= */

                if (!file) {

                    showToast(
                        'error',
                        'Silakan pilih file Excel terlebih dahulu.'
                    );

                    return;
                }


                /* =========================
                   VALIDASI EXTENSION
                ========================= */

                const fileNameValue = file.name.toLowerCase();

                const allowedExtensions = [
                    '.xlsx',
                    '.xls'
                ];

                const isValidExtension =
                    allowedExtensions.some(function(extension) {
                        return fileNameValue.endsWith(extension);
                    });

                if (!isValidExtension) {

                    showToast(
                        'error',
                        'File harus berformat Excel (.xlsx atau .xls).'
                    );

                    return;
                }


                /* =========================
                   FORM DATA
                ========================= */

                const formData = new FormData();

                formData.append('file', file);


                /* =========================
                   DISABLE BUTTON
                ========================= */

                btnSave.disabled = true;

                btnSave.innerHTML = `
            <svg viewBox="0 0 24 24">
                <path d="M12 16V3" />
                <path d="m7 8 5-5 5 5" />
                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
            </svg>
            Mengimport...
        `;


                try {

                    /* =========================
                       REQUEST IMPORT
                    ========================= */

                    const response = await fetch(
                        "{{ route('admin_import_calculation_rule') }}", {
                            method: 'POST',

                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',

                                'Accept': 'application/json'
                            },

                            body: formData
                        }
                    );


                    /* =========================
                       RESPONSE
                    ========================= */

                    const result =
                        await response.json();


                    /* =========================
                       ERROR RESPONSE
                    ========================= */

                    if (!response.ok) {

                        /*
                         * Jika terdapat error per baris
                         */
                        if (Array.isArray(result.errors)) {

                            const messages =
                                result.errors.map(function(error) {

                                    return `Baris ${error.row}: ${error.message}`;

                                });


                            showToast(
                                'error',
                                messages.join('<br>')
                            );

                        } else {

                            showToast(
                                'error',
                                result.message ||
                                'Import aturan perhitungan gagal.'
                            );

                        }

                        return;
                    }


                    /* =========================
                       IMPORT BERHASIL
                    ========================= */

                    closeModal();


                    /* Reset file */

                    fileInput.value = '';

                    fileName.textContent =
                        'Belum ada file dipilih';

                    fileName.classList.remove(
                        'has-file'
                    );


                    /* =========================
                       RELOAD DATATABLE
                    ========================= */

                    if (
                        typeof calculationRuleTable !==
                        'undefined' &&
                        calculationRuleTable
                    ) {

                        calculationRuleTable.ajax.reload(
                            null,
                            false
                        );

                    }


                    /* =========================
                       SUCCESS TOAST
                    ========================= */

                    showToast(
                        'success',
                        result.message ||
                        'Data aturan perhitungan berhasil diimport.'
                    );


                } catch (error) {

                    console.error(
                        'Import Calculation Rule Error:',
                        error
                    );

                    showToast(
                        'error',
                        'Terjadi kesalahan saat mengimport data.'
                    );


                } finally {

                    /* =========================
                       ENABLE BUTTON
                    ========================= */

                    btnSave.disabled = false;

                    btnSave.innerHTML = `
                <svg viewBox="0 0 24 24">
                    <path d="M12 16V3" />
                    <path d="m7 8 5-5 5 5" />
                    <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                </svg>
                Import Data
            `;

                }

            });

        });



        let calculationRuleTable;

        document.addEventListener('DOMContentLoaded', function() {

            calculationRuleTable = new DataTable('#calculationRuleTable', {

                processing: true,
                serverSide: true,
                ordering: false,

                responsive: {
                    details: {
                        type: 'column',
                        target: 0
                    }
                },

                ajax: {
                    url: "{{ route('admin_data_calculation_rules') }}",
                    type: "GET"
                },

                columns: [

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
                            const pageInfo =
                                calculationRuleTable.page.info();

                            return pageInfo.start + meta.row + 1;
                        }
                    },

                    {
                        data: 'code',
                        responsivePriority: 1,

                        render: function(data) {
                            return `
                        <div class="data-cell-user-name">
                            ${data ?? '-'}
                        </div>
                    `;
                        }
                    },

                    {
                        data: null,
                        responsivePriority: 100,

                        render: function(data, type, row) {
                            return `
                        <div>
                            <div class="data-cell-user-name">
                                ${row.name ?? '-'}
                            </div>
                        </div>
                    `;
                        }
                    },

                    {
                        data: 'engine_name',
                        responsivePriority: 1,

                        render: function(data) {
                            return `
                        <div>
                            ${data ?? '-'}
                        </div>
                    `;
                        }
                    },

                    {
                        data: 'minimum_charge',
                        responsivePriority: 100,
                        className: 'text-center',

                        render: function(data) {

                            if (data === null || data === undefined) {
                                return '-';
                            }

                            return `
                        <span class="data-cell-mono">
                            ${Number(data).toLocaleString('id-ID', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            })} m²
                        </span>
                    `;
                        }
                    },

                    {
                        data: 'status',
                        responsivePriority: 100,
                        className: 'text-center',

                        render: function(data) {

                            if (data === 'Active') {
                                return `
                            <span class="badge dot success">
                                Active
                            </span>
                        `;
                            }

                            return `
                        <span class="badge dot">
                            ${data ?? 'Inactive'}
                        </span>
                    `;
                        }
                    },

                    {
                        data: null,
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

                            <button
                                type="button"
                                class="btn--icon btn-view-calculation-rule"
                                data-id="${row.id}"
                                aria-label="Detail"
                            >
                                <i class="bi bi-arrows-fullscreen"></i>
                            </button>

                            <button
                                type="button"
                                class="btn--icon btn-edit-calculation-rule"
                                data-id="${row.id}"
                                aria-label="Edit"
                            >
                                <i class="bi bi-pen"></i>
                            </button>

                            <button
                                type="button"
                                class="btn--icon btn-delete-calculation-rule"
                                data-id="${row.id}"
                                aria-label="Hapus"
                            >
                                <i class="bi bi-trash3-fill"></i>
                            </button>

                        </div>
                    `;
                        }
                    }

                ],

                columnDefs: [{
                    targets: 0,
                    className: 'dtr-control'
                }],

                order: [
                    [2, 'asc']
                ],

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
                    bottomEnd: 'paging'
                },

                language: {
                    search: 'Search:',
                    lengthMenu: '_MENU_ entries per page',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    zeroRecords: 'Data aturan perhitungan tidak ditemukan',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari aturan perhitungan...'
                },

                initComplete: function() {

                    $(this)
                        .closest('.dt-container, .dataTables_wrapper')
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

            /*
            |--------------------------------------------------------------------------
            | EDIT CALCULATION RULE
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const button =
                    event.target.closest('.btn-edit-calculation-rule');

                if (!button) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | AMBIL ID
                |--------------------------------------------------------------------------
                */

                const id = button.dataset.id;

                /*
                |--------------------------------------------------------------------------
                | ENCODE ID
                |--------------------------------------------------------------------------
                */

                const encodedId = btoa(id);

                /*
                |--------------------------------------------------------------------------
                | REDIRECT
                |--------------------------------------------------------------------------
                */

                window.location.href =
                    `/admin/calculation-rules/${encodedId}/edit`;

            });

        });

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | EDIT CALCULATION RULE
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const button =
                    event.target.closest('.btn-view-calculation-rule');

                if (!button) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | AMBIL ID
                |--------------------------------------------------------------------------
                */

                const id = button.dataset.id;

                /*
                |--------------------------------------------------------------------------
                | ENCODE ID
                |--------------------------------------------------------------------------
                */

                const encodedId = btoa(id);

                /*
                |--------------------------------------------------------------------------
                | REDIRECT
                |--------------------------------------------------------------------------
                */

                window.location.href =
                    `/admin/calculation-rules/${encodedId}/details`;

            });

        });

        document.addEventListener('DOMContentLoaded', function() {

            const modal =
                document.getElementById(
                    'modalDeleteCalculationRule'
                );

            const btnClose =
                document.getElementById(
                    'btnCloseDeleteCalculationRule'
                );

            const btnCancel =
                document.getElementById(
                    'btnCancelDeleteCalculationRule'
                );

            const btnConfirm =
                document.getElementById(
                    'btnConfirmDeleteCalculationRule'
                );


            /*
            |--------------------------------------------------------------------------
            | ID CALCULATION RULE YANG AKAN DIHAPUS
            |--------------------------------------------------------------------------
            */

            let calculationRuleId = null;


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL DELETE
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const button =
                    event.target.closest(
                        '.btn-delete-calculation-rule'
                    );

                if (!button) {
                    return;
                }


                calculationRuleId =
                    button.dataset.id;


                if (!calculationRuleId) {

                    showToast(
                        'error',
                        'ID aturan perhitungan tidak ditemukan.'
                    );

                    return;
                }


                modal.classList.add('is-open');

            });


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function closeModal() {

                modal.classList.remove('is-open');

                calculationRuleId = null;

            }


            btnClose.addEventListener(
                'click',
                closeModal
            );


            btnCancel.addEventListener(
                'click',
                closeModal
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modal.addEventListener(
                'click',
                function(event) {

                    if (event.target === modal) {
                        closeModal();
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
                        modal.classList.contains('is-open')
                    ) {
                        closeModal();
                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI DELETE
            |--------------------------------------------------------------------------
            */

            btnConfirm.addEventListener(
                'click',
                async function() {

                    if (!calculationRuleId) {

                        showToast(
                            'error',
                            'ID aturan perhitungan tidak ditemukan.'
                        );

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DISABLE BUTTON
                    |--------------------------------------------------------------------------
                    */

                    const originalHtml =
                        btnConfirm.innerHTML;


                    btnConfirm.disabled = true;


                    btnConfirm.innerHTML = `
                <i class="bi bi-hourglass-split"></i>
                Menghapus...
            `;


                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | REQUEST DELETE
                        |--------------------------------------------------------------------------
                        */

                        const response =
                            await fetch(
                                "{{ route('admin_delete_calculation_rules', ':id') }}"
                                .replace(
                                    ':id',
                                    calculationRuleId
                                ), {
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
                        | RESPONSE
                        |--------------------------------------------------------------------------
                        */

                        const result =
                            await response.json();


                        /*
                        |--------------------------------------------------------------------------
                        | ERROR
                        |--------------------------------------------------------------------------
                        */

                        if (
                            !response.ok ||
                            !result.success
                        ) {

                            showToast(
                                'error',
                                result.message ||
                                'Aturan perhitungan gagal dihapus.'
                            );

                            return;
                        }


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
                            typeof calculationRuleTable !==
                            'undefined' &&
                            calculationRuleTable
                        ) {

                            calculationRuleTable.ajax.reload(
                                null,
                                false
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | SUCCESS TOAST
                        |--------------------------------------------------------------------------
                        */

                        showToast(
                            'success',
                            result.message ||
                            'Aturan perhitungan berhasil dihapus.'
                        );


                    } catch (error) {

                        console.error(
                            'Delete Calculation Rule Error:',
                            error
                        );


                        showToast(
                            'error',
                            'Terjadi kesalahan saat menghapus aturan perhitungan.'
                        );


                    } finally {

                        /*
                        |--------------------------------------------------------------------------
                        | RESTORE BUTTON
                        |--------------------------------------------------------------------------
                        */

                        btnConfirm.disabled = false;

                        btnConfirm.innerHTML =
                            originalHtml;

                    }

                }
            );

        });
    </script>
@endsection

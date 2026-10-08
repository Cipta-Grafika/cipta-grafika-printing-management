@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">HARGA & TARIF · HARGA MATERIAL</span>
                        {{-- <h1 class="hero-title">Kebijakan Harga</h1> --}}
                        <p class="hero-sub">Kelola harga material per meter yang digunakan sebagai dasar dalam perhitungan
                            estimasi.</p>
                    </div>
                    <div class="hero-actions"><a href="{{ route('admin_export_material_price') }}" class="btn btn--ghost"><svg
                                viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg> Export</a>
                        <a href="{{ asset('templates/template_harga_material.xlsx') }}" class="btn btn--ghost">

                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Download Template

                        </a>
                        <button class="btn btn--ghost" id="btnImportMaterialPrice"><svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg> Import</button>
                        <a href="{{ route('admin_create_material_price') }}" class="btn btn--primary"><svg
                                viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg> Tambah Harga Material</a>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="materialPriceTable" class="data-table">
                            <thead>
                                <tr>
                                    <th style="width:32px"></th>
                                    <th>No</th>
                                    <th>Material</th>
                                    <th>Harga per Meter</th>
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

    <!-- Modal Import Material Price -->
    <div class="modal-overlay" id="modalImportMaterialPrice">

        <div class="modal modal-import">

            <div class="modal-header">

                <div>
                    <span class="eyebrow">HARGA & TARIF · HARGA MATERIAL</span>
                    <br>
                    <span class="eyebrow">Import Harga Material</h2>
                </div>

                <button type="button" class="modal-close" id="btnCloseImportMaterialPrice" aria-label="Tutup">

                    <svg viewBox="0 0 24 24">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>

                </button>

            </div>

            <div class="modal-body">

                <div class="import-description">
                    <p>
                        Pilih file Excel yang berisi data harga material
                        untuk diimport ke dalam sistem.
                    </p>
                </div>

                <label for="fileImportMaterialPrice" class="file-upload-box" id="fileUploadBox">

                    <input type="file" id="fileImportMaterialPrice" name="file" accept=".xlsx,.xls" hidden>

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

                    <div class="file-selected-name" id="fileSelectedMaterialPrice">

                        Belum ada file dipilih

                    </div>

                </label>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCancelImportMaterialPrice">

                    Batal

                </button>

                <button type="button" class="btn btn--primary" id="btnSaveImportMaterialPrice">

                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8 5-5 5 5" />
                    </svg>

                    Import Data

                </button>

            </div>

        </div>

    </div>

    <!-- MODAL DELETE MATERIAL PRICE -->
    <div class="modal-overlay" id="modalDeleteMaterialPrice">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        HARGA & TARIF · HARGA MATERIAL
                    </span>
                    <br>
                    <span class="eyebrow">
                        Hapus Harga Material
                    </span>

                </div>

                <button type="button" class="modal-close" id="btnTutupDeleteMaterialPrice" aria-label="Tutup">

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
                            Hapus harga material?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus harga per meter
                            material ini? Data yang sudah dihapus tidak dapat
                            dikembalikan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteMaterialPrice">

                    Batal

                </button>


                <button type="button" class="btn btn--danger" id="btnConfirmDeleteMaterialPrice">

                    <i class="bi bi-trash3-fill"></i>

                    Hapus

                </button>

            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script>
        let materialPriceTable;

        document.addEventListener('DOMContentLoaded', function() {

            materialPriceTable = new DataTable('#materialPriceTable', {

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
                    url: "{{ route('admin_data_material_prices') }}",
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
                                materialPriceTable.page.info();

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
                     | HARGA PER METER
                     |--------------------------------------------------------------------------
                     */

                    {
                        data: 'price_per_meter',
                        responsivePriority: 100,
                        className: 'text-center',

                        render: function(data) {

                            if (data === null || data === '') {
                                return '-';
                            }

                            const value = Number(data);

                            if (isNaN(value)) {
                                return '-';
                            }

                            return `
                        <div>
                            Rp ${value.toLocaleString('id-ID', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            })}
                        </div>
                    `;
                        }
                    },


                    /*
                     |--------------------------------------------------------------------------
                     | AKSI
                     |--------------------------------------------------------------------------
                     */

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

                            <!-- EDIT -->
                            <button
                                type="button"
                                class="btn--icon btn-edit-material-price"
                                data-id="${row.id}"
                                aria-label="Edit"
                            >
                                <i class="bi bi-pen"></i>
                            </button>

                            <!-- DELETE -->
                            <button
                                type="button"
                                class="btn--icon btn-delete-material-price"
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
                 | DEFAULT ORDER
                 |--------------------------------------------------------------------------
                 */

                order: [
                    [2, 'asc']
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
                    zeroRecords: 'Data harga material tidak ditemukan',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari material...'
                }

            });

            /*
            |--------------------------------------------------------------------------
            | EVENT DELETE
            |--------------------------------------------------------------------------
            */

            let materialPriceIdToDelete = null;

            const modalDeleteMaterialPrice =
                document.getElementById('modalDeleteMaterialPrice');

            const btnTutupDeleteMaterialPrice =
                document.getElementById('btnTutupDeleteMaterialPrice');

            const btnBatalDeleteMaterialPrice =
                document.getElementById('btnBatalDeleteMaterialPrice');


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL DELETE
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const button =
                    event.target.closest('.btn-delete-material-price');

                if (!button) {
                    return;
                }

                const id = button.dataset.id;

                if (!id) {
                    return;
                }

                materialPriceIdToDelete = id;

                modalDeleteMaterialPrice.style.display = 'flex';
                modalDeleteMaterialPrice.style.visibility = 'visible';
                modalDeleteMaterialPrice.style.opacity = '1';

            });


            /*
            |--------------------------------------------------------------------------
            | FUNGSI TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupDeleteMaterialPriceModal() {

                modalDeleteMaterialPrice.style.display = 'none';
                modalDeleteMaterialPrice.style.visibility = 'hidden';
                modalDeleteMaterialPrice.style.opacity = '0';

                materialPriceIdToDelete = null;

            }


            /*
            |--------------------------------------------------------------------------
            | TOMBOL X
            |--------------------------------------------------------------------------
            */

            btnTutupDeleteMaterialPrice.addEventListener(
                'click',
                function() {

                    tutupDeleteMaterialPriceModal();

                }
            );


            /*
            |--------------------------------------------------------------------------
            | TOMBOL BATAL
            |--------------------------------------------------------------------------
            */

            btnBatalDeleteMaterialPrice.addEventListener(
                'click',
                function() {

                    tutupDeleteMaterialPriceModal();

                }
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK DI LUAR MODAL / OVERLAY
            |--------------------------------------------------------------------------
            */

            modalDeleteMaterialPrice.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalDeleteMaterialPrice) {

                        tutupDeleteMaterialPriceModal();

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI HAPUS MATERIAL PRICE
            |--------------------------------------------------------------------------
            */

            const btnConfirmDeleteMaterialPrice =
                document.getElementById('btnConfirmDeleteMaterialPrice');

            btnConfirmDeleteMaterialPrice.addEventListener(
                'click',
                async function() {

                    /*
                    |--------------------------------------------------------------------------
                    | CEK ID
                    |--------------------------------------------------------------------------
                    */

                    if (!materialPriceIdToDelete) {
                        return;
                    }


                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | REQUEST DELETE
                        |--------------------------------------------------------------------------
                        */

                        const response = await fetch(
                            '/admin/material-prices/' +
                            materialPriceIdToDelete +
                            '/delete', {
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

                            tutupDeleteMaterialPriceModal();


                            /*
                            |--------------------------------------------------------------------------
                            | RELOAD DATATABLE
                            |--------------------------------------------------------------------------
                            */

                            materialPriceTable.ajax.reload(
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
                                'Harga material gagal dihapus.'
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
                            'Terjadi kesalahan saat menghapus harga material.'
                        );

                    }

                }
            );

            /*
            |--------------------------------------------------------------------------
            | EVENT EDIT
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const button =
                    event.target.closest('.btn-edit-material-price');

                if (!button) {
                    return;
                }

                const id = button.dataset.id;

                if (!id) {
                    return;
                }

                const encodedId = btoa(id.toString());

                window.location.href =
                    `/admin/material-prices/${encodedId}/edit`;

            });

        });

        document.addEventListener('DOMContentLoaded', function() {

            const btnImport =
                document.getElementById('btnImportMaterialPrice');

            const modal =
                document.getElementById('modalImportMaterialPrice');

            const btnClose =
                document.getElementById('btnCloseImportMaterialPrice');

            const btnCancel =
                document.getElementById('btnCancelImportMaterialPrice');

            const btnSave =
                document.getElementById('btnSaveImportMaterialPrice');

            const fileInput =
                document.getElementById('fileImportMaterialPrice');

            const fileName =
                document.getElementById('fileSelectedMaterialPrice');


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL
            |--------------------------------------------------------------------------
            */

            btnImport.addEventListener('click', function() {
                modal.classList.add('is-open');
            });


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
            | TOMBOL ESC
            |--------------------------------------------------------------------------
            */

            document.addEventListener('keydown', function(event) {

                if (event.key === 'Escape') {
                    closeModal();
                }

            });


            /*
            |--------------------------------------------------------------------------
            | FILE SELECTED
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

            btnSave.addEventListener('click', async function() {

                /*
                |--------------------------------------------------------------------------
                | CEK FILE
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

                const originalText =
                    btnSave.innerHTML;

                btnSave.disabled = true;

                btnSave.innerHTML =
                    'Mengimport...';


                try {

                    /*
                    |--------------------------------------------------------------------------
                    | REQUEST
                    |--------------------------------------------------------------------------
                    */

                    const response = await fetch(
                        "{{ route('admin_import_material_price') }}", {
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


                    const result =
                        await response.json();


                    /*
                    |--------------------------------------------------------------------------
                    | ERROR RESPONSE
                    |--------------------------------------------------------------------------
                    */

                    if (!response.ok) {

                        if (
                            result.errors &&
                            Array.isArray(result.errors) &&
                            result.errors.length > 0
                        ) {

                            const messages =
                                result.errors.map(function(error) {

                                    return `Baris ${error.row}: ${error.message}`;

                                });

                            showToast(
                                'error',
                                result.message +
                                '<br>' +
                                messages.join('<br>')
                            );

                        } else {

                            showToast(
                                'error',
                                result.message ??
                                'Import gagal.'
                            );

                        }

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | IMPORT BERHASIL
                    |--------------------------------------------------------------------------
                    */

                    if (result.success) {

                        closeModal();


                        /*
                        |--------------------------------------------------------------------------
                        | RESET FILE
                        |--------------------------------------------------------------------------
                        */

                        fileInput.value = '';

                        fileName.textContent =
                            'Belum ada file dipilih';

                        fileName.classList.remove(
                            'has-file'
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | RELOAD DATATABLE
                        |--------------------------------------------------------------------------
                        */

                        if (
                            typeof materialPriceTable !==
                            'undefined'
                        ) {

                            materialPriceTable.ajax.reload(
                                null,
                                false
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | TOAST SUCCESS
                        |--------------------------------------------------------------------------
                        */

                        showToast(
                            'success',
                            result.message
                        );

                    } else {

                        showToast(
                            'error',
                            result.message ??
                            'Import gagal.'
                        );

                    }

                } catch (error) {

                    console.error(
                        'Import Material Price Error:',
                        error
                    );

                    showToast(
                        'error',
                        'Terjadi kesalahan saat melakukan import.'
                    );

                } finally {

                    /*
                    |--------------------------------------------------------------------------
                    | RESTORE BUTTON
                    |--------------------------------------------------------------------------
                    */

                    btnSave.disabled = false;

                    btnSave.innerHTML =
                        originalText;

                }

            });

        });
    </script>
@endsection

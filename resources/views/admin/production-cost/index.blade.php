@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">HARGA & TARIF · ONGKOS PRODUKSI</span>
                        {{-- <h1 class="hero-title">Ongkos Produksi</h1> --}}
                        <p class="hero-sub">Kelola ongkos produksi berdasarkan lokasi, engine, dan material sebagai acuan
                            dalam perhitungan estimasi biaya.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin_export_production_cost') }}" class="btn btn--ghost">
                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>
                            Export
                        </a>
                        <a href="{{ asset('templates/template_ongkos_produksi.xlsx') }}" class="btn btn--ghost">

                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Download Template

                        </a>
                        <button class="btn btn--ghost" id="btnImportProductionCost"><svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg> Import</button>
                        <a href="{{ route('admin_create_production_cost') }}" class="btn btn--primary"><svg
                                viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg> Tambah Ongkos Produksi</a>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="productionCostTable" class="display data-table">

                            <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Lokasi</th>
                                    <th>Engine</th>
                                    <th>Jumlah Material</th>
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

    <!-- Modal Import Production Cost -->
    <div class="modal-overlay" id="modalImportProductionCost">

        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">

                <div>
                    <span class="eyebrow">
                        HARGA & TARIF · ONGKOS PRODUKSI
                    </span>
                    <br>
                    <span class="eyebrow">
                        Import Ongkos Produksi
                    </span>
                </div>

                <button type="button" class="modal-close" id="btnCloseImportProductionCost" aria-label="Tutup">

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
                        Pilih file Excel yang berisi data ongkos produksi
                        untuk diimport ke dalam sistem.
                    </p>
                </div>


                <!-- Custom File Input -->
                <label for="fileImportProductionCost" class="file-upload-box" id="fileUploadBox">

                    <input type="file" id="fileImportProductionCost" name="file" accept=".xlsx,.xls" hidden>


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


                    <div class="file-selected-name" id="fileSelectedProductionCost">
                        Belum ada file dipilih
                    </div>

                </label>

            </div>


            <!-- Footer -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCancelImportProductionCost">
                    Batal
                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportProductionCost">

                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8 5-5 5 5" />
                    </svg>

                    Import Data

                </button>

            </div>

        </div>

    </div>

    <!-- MODAL DELETE PRODUCTION COST -->
    <div class="modal-overlay" id="modalDeleteProductionCost">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        HARGA & TARIF · ONGKOS PRODUKSI
                    </span>
                    <br>
                    <span class="eyebrow">
                        Hapus Ongkos Produksi
                    </span>

                </div>

                <button type="button" class="modal-close" id="btnTutupDeleteProductionCost" aria-label="Tutup">

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
                            Hapus ongkos produksi?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus seluruh
                            ongkos produksi pada kombinasi lokasi dan
                            engine ini? Data yang sudah dihapus tidak
                            dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteProductionCost">

                    Batal

                </button>


                <button type="button" class="btn btn--danger" id="btnConfirmDeleteProductionCost">

                    <i class="bi bi-trash3-fill"></i>

                    Hapus

                </button>

            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script>
        let productionCostTable;

        document.addEventListener('DOMContentLoaded', function() {

            productionCostTable = new DataTable('#productionCostTable', {

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
                    url: "{{ route('admin_data_production_cost') }}",
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
                                productionCostTable.page.info();

                            return pageInfo.start + meta.row + 1;
                        }
                    },


                    /*
                     |--------------------------------------------------------------------------
                     | LOKASI
                     |--------------------------------------------------------------------------
                     */

                    {
                        data: 'location_name',
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
                     | ENGINE
                     |--------------------------------------------------------------------------
                     */

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


                    /*
                     |--------------------------------------------------------------------------
                     | JUMLAH MATERIAL
                     |--------------------------------------------------------------------------
                     */

                    {
                        data: 'material_count',
                        responsivePriority: 100,
                        className: 'text-center',

                        render: function(data) {

                            return `
                            <span>
                                ${data ?? 0} Material
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

                                <!-- DETAIL -->
                                <button
                                    type="button"
                                    class="btn--icon btn-view-production-cost"
                                    data-location-id="${row.location_id}"
                                    data-engine-id="${row.engine_id}"
                                    aria-label="Detail"
                                >
                                    <i class="bi bi-arrows-fullscreen"></i>
                                </button>

                                <!-- EDIT -->
                                <button
                                    type="button"
                                    class="btn--icon btn-edit-production-cost"
                                    data-location-id="${row.location_id}"
                                    data-engine-id="${row.engine_id}"
                                    aria-label="Edit"
                                >
                                    <i class="bi bi-pen"></i>
                                </button>

                                <!-- DELETE -->
                                <button
                                    type="button"
                                    class="btn--icon btn-delete-production-cost"
                                    data-location-id="${row.location_id}"
                                    data-engine-id="${row.engine_id}"
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
                    [2, 'asc'],
                    [3, 'asc']
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
                    zeroRecords: 'Data ongkos produksi tidak ditemukan',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari ongkos produksi...'
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
             | EVENT DETAIL
             |--------------------------------------------------------------------------
             */

            document.addEventListener('click', function(event) {
                const button =
                    event.target.closest('.btn-view-production-cost');

                if (!button) {
                    return;
                }

                const locationId =
                    button.dataset.locationId;

                const engineId =
                    button.dataset.engineId;

                const id = locationId + '|' + engineId;

                const encodedId = btoa(id);

                window.location.href =
                    `{{ url('/admin/production-costs') }}/${encodedId}/details`;
            });


            /*
             |--------------------------------------------------------------------------
             | EVENT EDIT
             |--------------------------------------------------------------------------
             */

            document.addEventListener('click', function(event) {
                const button = event.target.closest('.btn-edit-production-cost');

                if (!button) return;

                const locationId = button.dataset.locationId;
                const engineId = button.dataset.engineId;

                const id = `${locationId}|${engineId}`;
                const encodedId = btoa(id);

                window.location.href =
                    `{{ url('/admin/production-costs') }}/${encodedId}/edit`;
            });


            /*
             |--------------------------------------------------------------------------
             | EVENT DELETE
             |--------------------------------------------------------------------------
             */

            let productionCostIdToDelete = null;

            const modalDeleteProductionCost =
                document.getElementById('modalDeleteProductionCost');

            console.log('Modal:', modalDeleteProductionCost);

            const btnTutupDeleteProductionCost =
                document.getElementById('btnTutupDeleteProductionCost');

            const btnBatalDeleteProductionCost =
                document.getElementById('btnBatalDeleteProductionCost');


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL DELETE
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const button = event.target.closest('.btn-delete-production-cost');

                if (!button) {
                    return;
                }

                console.log('DELETE TERKLIK');
                console.log('Button:', button);
                console.log('Modal:', modalDeleteProductionCost);

                const locationId = button.dataset.locationId;
                const engineId = button.dataset.engineId;

                const id = locationId + '|' + engineId;

                productionCostIdToDelete = btoa(id);

                modalDeleteProductionCost.style.display = 'flex';
                modalDeleteProductionCost.style.visibility = 'visible';
                modalDeleteProductionCost.style.opacity = '1';
            });


            /*
            |--------------------------------------------------------------------------
            | FUNGSI TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupDeleteProductionCostModal() {
                modalDeleteProductionCost.style.display = 'none';
                modalDeleteProductionCost.style.visibility = 'hidden';
                modalDeleteProductionCost.style.opacity = '0';

                productionCostIdToDelete = null;
            }


            /*
            |--------------------------------------------------------------------------
            | TOMBOL X
            |--------------------------------------------------------------------------
            */

            btnTutupDeleteProductionCost.addEventListener(
                'click',
                function() {

                    tutupDeleteProductionCostModal();

                }
            );


            /*
            |--------------------------------------------------------------------------
            | TOMBOL BATAL
            |--------------------------------------------------------------------------
            */

            btnBatalDeleteProductionCost.addEventListener(
                'click',
                function() {

                    tutupDeleteProductionCostModal();

                }
            );

            /*
            |--------------------------------------------------------------------------
            | KLIK DI LUAR MODAL / OVERLAY
            |--------------------------------------------------------------------------
            */
            modalDeleteProductionCost.addEventListener('click', function(event) {
                if (event.target === modalDeleteProductionCost) {
                    tutupDeleteProductionCostModal();
                }
            });

            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI HAPUS PRODUCTION COST
            |--------------------------------------------------------------------------
            */

            const btnConfirmDeleteProductionCost =
                document.getElementById('btnConfirmDeleteProductionCost');

            btnConfirmDeleteProductionCost.addEventListener(
                'click',
                async function() {

                    /*
                    |--------------------------------------------------------------------------
                    | CEK ID
                    |--------------------------------------------------------------------------
                    */

                    if (!productionCostIdToDelete) {
                        return;
                    }

                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | REQUEST DELETE
                        |--------------------------------------------------------------------------
                        */

                        const response = await fetch(
                            '/admin/production-costs/' +
                            productionCostIdToDelete +
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

                            tutupDeleteProductionCostModal();


                            /*
                            |--------------------------------------------------------------------------
                            | RELOAD DATATABLE
                            |--------------------------------------------------------------------------
                            */

                            productionCostTable.ajax.reload(
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
                                'Ongkos produksi gagal dihapus.'
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
                            'Terjadi kesalahan saat menghapus ongkos produksi.'
                        );

                    }

                }
            );

        });


        document.addEventListener('DOMContentLoaded', function() {

            const btnImport =
                document.getElementById('btnImportProductionCost');

            const modal =
                document.getElementById('modalImportProductionCost');

            const btnClose =
                document.getElementById('btnCloseImportProductionCost');

            const btnCancel =
                document.getElementById('btnCancelImportProductionCost');

            const btnSave =
                document.getElementById('btnSaveImportProductionCost');

            const fileInput =
                document.getElementById('fileImportProductionCost');

            const fileName =
                document.getElementById('fileSelectedProductionCost');


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

                btnSave.innerHTML = 'Mengimport...';


                try {

                    /*
                    |--------------------------------------------------------------------------
                    | REQUEST
                    |--------------------------------------------------------------------------
                    */

                    const response = await fetch(
                        "{{ route('admin_import_production_cost') }}", {
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

                            const messages = result.errors.map(function(error) {

                                return `Baris ${error.row}: ${error.message}`;

                            });

                            showToast(
                                'error',
                                result.message + '<br>' + messages.join('<br>')
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
                            typeof productionCostTable !==
                            'undefined'
                        ) {

                            productionCostTable.ajax.reload(
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
                        'Import Production Cost Error:',
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

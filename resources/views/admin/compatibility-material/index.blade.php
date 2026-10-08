@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">MASTER DATA · KOMPATIBILITAS</span>
                        {{-- <h1 class="hero-title">Kompatibilitas Mesin & Material</h1> --}}
                        <p class="hero-sub">Kelola dan pantau hubungan kompatibilitas antara mesin dan material yang dapat
                            digunakan dalam proses produksi.</p>
                    </div>
                    <div class="hero-actions">
                        {{-- <button class="btn btn--ghost"><svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg> Export</button> --}}
                        <a href="{{ route('admin_create_compatibility_material') }}" class="btn btn--primary"><svg
                                viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg> Tambah Kompatibilitas</a>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="compatibilityMaterialTable" class="display data-table">

                            <thead>

                                <tr>

                                    <th></th>

                                    <th>No</th>

                                    <th>Material</th>

                                    <th>Mesin</th>

                                    <th>Kategori</th>

                                    <th>Status</th>

                                    <th class="action-column">Aksi</th>

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

    <!-- MODAL DELETE COMPATIBILITY -->
    <div class="modal-overlay" id="modalDeleteCompatibility">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->

            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MASTER DATA · KOMPATIBILITAS
                    </span>
                    <br>
                    <span class="eyebrow">
                        Hapus Kompatibilitas
                    </span>

                </div>


                <button type="button" class="modal-close" id="btnTutupDeleteCompatibility" aria-label="Tutup">

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
                            Hapus kompatibilitas?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus data kompatibilitas ini?
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->

            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteCompatibility">

                    Batal

                </button>


                <button type="button" class="btn btn--danger" id="btnConfirmDeleteCompatibility">

                    <i class="bi bi-trash3-fill"></i>


                    Hapus

                </button>

            </div>

        </div>

    </div>

    <!-- MODAL VIEW COMPATIBILITY -->
    <div class="modal-overlay" id="modalViewCompatibility">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MASTER DATA · KOMPATIBILITAS
                    </span>
                    <br>
                    <span class="eyebrow">
                        Detail Kompatibilitas
                    </span>

                </div>


                <button type="button" class="modal-close" id="btnTutupViewCompatibility" aria-label="Tutup">

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

                    <!-- CONTENT -->
                    <div class="delete-content">

                        <p>
                            Berikut adalah detail data kompatibilitas mesin dan material yang dipilih.
                        </p>

                    </div>

                </div>


                <!-- DETAIL COMPATIBILITY -->
                <div style="margin-top: 20px;">

                    <div style="margin-bottom: 14px;">

                        <div class="eyebrow">
                            Mesin
                        </div>

                        <div id="viewCompatibilityEngine">
                            -
                        </div>

                    </div>


                    <div style="margin-bottom: 14px;">

                        <div class="eyebrow">
                            Kategori
                        </div>

                        <div id="viewCompatibilityCategory">
                            -
                        </div>

                    </div>


                    <div style="margin-bottom: 14px;">

                        <div class="eyebrow">
                            Kode Material
                        </div>

                        <div id="viewCompatibilityMaterialCode">
                            -
                        </div>

                    </div>


                    <div style="margin-bottom: 14px;">

                        <div class="eyebrow">
                            Nama Material
                        </div>

                        <div id="viewCompatibilityMaterialName">
                            -
                        </div>

                    </div>


                    <div style="margin-bottom: 14px;">

                        <div class="eyebrow">
                            Status
                        </div>

                        <div id="viewCompatibilityStatus">
                            -
                        </div>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCloseViewCompatibility">

                    Tutup

                </button>

            </div>

        </div>

    </div>
@endsection


@section('scripts')
    <script>
        let compatibilityMaterialTable;

        document.addEventListener('DOMContentLoaded', function() {

            compatibilityMaterialTable = new DataTable(
                '#compatibilityMaterialTable', {

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

                        url: "{{ route('admin_compatibility_materials_data') }}",

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

                                const pageInfo =
                                    compatibilityMaterialTable
                                    .page
                                    .info();

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

                            name: 'material_name',

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
                        | MESIN
                        |--------------------------------------------------------------------------
                        */

                        {
                            data: 'engine_name',

                            name: 'engine_name',

                            responsivePriority: 2,

                            render: function(data) {

                                return `
                                <span>
                                    ${data ?? '-'}
                                </span>
                            `;

                            }
                        },


                        /*
                        |--------------------------------------------------------------------------
                        | KATEGORI
                        |--------------------------------------------------------------------------
                        */

                        {
                            data: 'category_name',

                            name: 'category_name',

                            responsivePriority: 100,

                            render: function(data) {

                                return `
                                <span class="data-cell-mono">
                                    ${data ?? '-'}
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

                            name: 'status',

                            responsivePriority: 100,

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

                            render: function(data) {

                                return `

                                    <div class="data-cell-actions" style="width: 100%; display: flex; justify-content: center; gap: 5px;">


                                        <!-- VIEW -->

                                        <button
                                            type="button"
                                            class="btn--icon btn-view-compatibility"
                                            data-id="${data}"
                                            aria-label="View"
                                        >

                                            <i class="bi bi-arrows-fullscreen"></i>

                                        </button>


                                        <!-- EDIT -->

                                        <button
                                            type="button"
                                            class="btn--icon btn-edit-compatibility"
                                            data-id="${data}"
                                            aria-label="Edit"
                                        >

                                            <i class="bi bi-pen"></i>


                                        </button>


                                        <!-- DELETE -->

                                        <button
                                            type="button"
                                            class="btn--icon btn-delete-compatibility"
                                            data-id="${data}"
                                            aria-label="Delete"
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

                        zeroRecords: 'Data kompatibilitas mesin dan material tidak ditemukan',

                        processing: 'Memuat data...',

                        searchPlaceholder: 'Cari material / mesin...'
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

                }
            );

            /*
            =====================================================
            KLIK TOMBOL EDIT
            =====================================================
            */

            document.addEventListener(

                'click',

                function(event) {

                    const editButton = event.target.closest(

                        '.btn-edit-compatibility'

                    );


                    if (!editButton) {

                        return;

                    }


                    const compatibilityId = editButton.dataset.id;


                    window.location.href = `/admin/compatibility-material/${compatibilityId}/edit`;

                }

            );

            document.addEventListener('click', async function(event) {

                const button = event.target.closest(
                    '.btn-view-compatibility'
                );

                if (!button) {

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | AMBIL ID KOMPATIBILITAS
                |--------------------------------------------------------------------------
                */

                const compatibilityId = button.dataset.id;


                /*
                |--------------------------------------------------------------------------
                | AMBIL DATA KOMPATIBILITAS
                |--------------------------------------------------------------------------
                */

                try {

                    const response = await fetch(

                        `/admin/compatibility-material/${compatibilityId}/details`,

                        {
                            method: 'GET',

                            headers: {

                                'Accept': 'application/json'

                            }

                        }

                    );


                    /*
                    |--------------------------------------------------------------------------
                    | CEK RESPONSE
                    |--------------------------------------------------------------------------
                    */

                    const result = await response.json();


                    if (!response.ok || !result.success) {

                        showToast(

                            'error',

                            result.message ||
                            'Data kompatibilitas tidak ditemukan.'

                        );

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | ISI DATA KE MODAL
                    |--------------------------------------------------------------------------
                    */

                    document.getElementById(
                        'viewCompatibilityEngine'
                    ).textContent = result.data.engine_name ?? '-';


                    document.getElementById(
                        'viewCompatibilityCategory'
                    ).textContent = result.data.category_name ?? '-';


                    document.getElementById(
                        'viewCompatibilityMaterialCode'
                    ).textContent = result.data.material_code ?? '-';


                    document.getElementById(
                        'viewCompatibilityMaterialName'
                    ).textContent = result.data.material_name ?? '-';


                    document.getElementById(
                        'viewCompatibilityStatus'
                    ).textContent = result.data.status ?? '-';


                    /*
                    |--------------------------------------------------------------------------
                    | BUKA MODAL
                    |--------------------------------------------------------------------------
                    */

                    bukaViewCompatibility();


                } catch (error) {

                    console.error(error);


                    showToast(

                        'error',

                        'Terjadi kesalahan saat mengambil data kompatibilitas.'

                    );

                }

            });

        });

        document.addEventListener('DOMContentLoaded', function() {

            const modalViewCompatibility = document.getElementById(
                'modalViewCompatibility'
            );

            const btnTutupViewCompatibility = document.getElementById(
                'btnTutupViewCompatibility'
            );

            const btnCloseViewCompatibility = document.getElementById(
                'btnCloseViewCompatibility'
            );


            /*
            =====================================================
            FUNCTION BUKA MODAL
            =====================================================
            */

            window.bukaViewCompatibility = function() {

                modalViewCompatibility.classList.add('is-open');

            };


            /*
            =====================================================
            FUNCTION TUTUP MODAL
            =====================================================
            */

            function tutupViewModal() {

                modalViewCompatibility.classList.remove('is-open');

            }


            /*
            =====================================================
            TOMBOL X
            =====================================================
            */

            btnTutupViewCompatibility.addEventListener(
                'click',
                tutupViewModal
            );


            /*
            =====================================================
            TOMBOL TUTUP (FOOTER)
            =====================================================
            */

            btnCloseViewCompatibility.addEventListener(
                'click',
                tutupViewModal
            );


            /*
            =====================================================
            KLIK BACKGROUND
            =====================================================
            */

            modalViewCompatibility.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalViewCompatibility) {

                        tutupViewModal();

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
                        modalViewCompatibility.classList.contains('is-open')
                    ) {

                        tutupViewModal();

                    }

                }
            );

        });

        document.addEventListener('DOMContentLoaded', function() {

            let compatibilityIdToDelete = null;


            /*
            =====================================================
            ELEMENT MODAL
            =====================================================
            */

            const modalDeleteCompatibility = document.getElementById(
                'modalDeleteCompatibility'
            );

            const btnTutupDeleteCompatibility = document.getElementById(
                'btnTutupDeleteCompatibility'
            );

            const btnBatalDeleteCompatibility = document.getElementById(
                'btnBatalDeleteCompatibility'
            );

            const btnConfirmDeleteCompatibility = document.getElementById(
                'btnConfirmDeleteCompatibility'
            );


            /*
            =====================================================
            KONFIRMASI HAPUS
            =====================================================
            */

            btnConfirmDeleteCompatibility.addEventListener(
                'click',

                async function() {

                    if (!compatibilityIdToDelete) {

                        return;

                    }


                    try {

                        const response = await fetch(

                            `/admin/compatibility-material/${compatibilityIdToDelete}/delete`,

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

                            compatibilityMaterialTable.ajax.reload(
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
                                'Kompatibilitas gagal dihapus.'

                            );

                        }

                    } catch (error) {

                        console.error(error);


                        showToast(

                            'error',

                            'Terjadi kesalahan saat menghapus kompatibilitas.'

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

                        '.btn-delete-compatibility'

                    );


                    if (!deleteButton) {

                        return;

                    }


                    /*
                    =====================================================
                    AMBIL ID KOMPATIBILITAS
                    =====================================================
                    */

                    compatibilityIdToDelete = deleteButton.dataset.id;


                    /*
                    =====================================================
                    BUKA MODAL
                    =====================================================
                    */

                    modalDeleteCompatibility.classList.add('is-open');

                }

            );


            /*
            =====================================================
            FUNCTION TUTUP MODAL
            =====================================================
            */

            function tutupDeleteModal() {

                modalDeleteCompatibility.classList.remove('is-open');

                compatibilityIdToDelete = null;

            }


            /*
            =====================================================
            TOMBOL X
            =====================================================
            */

            btnTutupDeleteCompatibility.addEventListener(

                'click',

                tutupDeleteModal

            );


            /*
            =====================================================
            TOMBOL BATAL
            =====================================================
            */

            btnBatalDeleteCompatibility.addEventListener(

                'click',

                tutupDeleteModal

            );


            /*
            =====================================================
            KLIK BACKGROUND
            =====================================================
            */

            modalDeleteCompatibility.addEventListener(

                'click',

                function(event) {

                    if (event.target === modalDeleteCompatibility) {

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

                        modalDeleteCompatibility.classList.contains('is-open')

                    ) {

                        tutupDeleteModal();

                    }

                }

            );

        });
    </script>
@endsection

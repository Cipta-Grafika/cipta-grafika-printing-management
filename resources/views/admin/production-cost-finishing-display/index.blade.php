@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">BIAYA · DISPLAY</span>
                        {{-- <h1 class="hero-title">Daftar Finishing & Jasa</h1> --}}
                        <p class="hero-sub">Kelola biaya material dan finishing yang digunakan sebagai dasar perhitungan
                            biaya serta estimasi harga produk.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin_create_pc_finishing_display') }}" class="btn btn--primary"><svg
                                viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg> Tambah Ongkos Produksi</a>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">

                        <table id="displayProductTable" class="display data-table">

                            <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Nama Display</th>
                                    <th style="text-align: center;">Kategori</th>
                                    <th style="text-align: center;">Ukuran</th>
                                    <th style="text-align: center;">Komponen</th>
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

    <!-- MODAL DELETE DISPLAY PRODUCT -->
    <div class="modal-overlay" id="modalDeleteDisplay">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        PRODUCTION COST · DISPLAY
                    </span>

                    <br>

                    <span class="eyebrow">
                        Hapus Display Product
                    </span>

                </div>

                <button type="button" class="modal-close" id="btnTutupDeleteDisplay" aria-label="Tutup">

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
                            Hapus Display Product?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus Display Product ini?
                            Data konfigurasi, komponen, harga, dan gambar yang
                            terkait juga akan dihapus.

                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteDisplay">
                    Batal
                </button>

                <button type="button" class="btn btn--danger" id="btnConfirmDeleteDisplay">

                    <i class="bi bi-trash3-fill"></i>

                    Hapus

                </button>

            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script>
        let displayProductTable;

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | HTML ESCAPE
            |--------------------------------------------------------------------------
            */

            function escapeHtml(value) {
                return String(value ?? '').replace(/[&<>"']/g, function(char) {
                    const entities = {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#039;'
                    };
                    return entities[char];
                });
            }
            /*
                           |-------------------------------------------------------------------------- | FORMAT ANGKA UKURAN
                           |-------------------------------------------------------------------------- */
            function formatSize(value) {
                const number = Number(value);

                if (!Number.isFinite(number)) {
                    return '-';
                }

                return number.toLocaleString('id-ID', {
                    maximumFractionDigits: 2
                });
            }

            /*
                           |-------------------------------------------------------------------------- | DATATABLE
                           |-------------------------------------------------------------------------- */
            displayProductTable = new
            DataTable('#displayProductTable', {
                processing: true,
                serverSide: true,
                ordering: false,
                /*
                               |-------------------------------------------------------------------------- | RESPONSIVE
                               |-------------------------------------------------------------------------- */
                responsive: {
                    details: {
                        type: 'column',
                        target: 0
                    }
                },
                /*
                               |-------------------------------------------------------------------------- | AJAX
                               |-------------------------------------------------------------------------- */
                ajax: {
                    url: "{{ route('admin_data_pc_finishing_display') }}",
                    type: "GET"
                },
                /*
                               |-------------------------------------------------------------------------- | COLUMNS
                               |-------------------------------------------------------------------------- */
                columns: [
                    /*
                                   |-------------------------------------------------------------------------- | RESPONSIVE CONTROL
                                   |-------------------------------------------------------------------------- */
                    {
                        data: null,
                        defaultContent: '',
                        className: 'dtr-control',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1
                    },
                    /* |-------------------------------------------------------------------------- | NO
                                   |-------------------------------------------------------------------------- */
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',
                        render: function(data, type,
                            row, meta) {
                            const pageInfo = displayProductTable.page.info();
                            return pageInfo.start + meta.row + 1;
                        }
                    },
                    /*
                                   |-------------------------------------------------------------------------- | NAMA DISPLAY
                                   |-------------------------------------------------------------------------- */
                    {
                        data: 'display_name',
                        responsivePriority: 1,
                        render: function(data) {
                            return ` <div>
                ${escapeHtml(data || '-')}
                </div>
                `;
                        },

                        createdCell: function(td) {
                            td.style.whiteSpace = 'normal';
                            td.style.wordBreak = 'break-word';
                            td.style.overflowWrap = 'break-word';
                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | KATEGORI
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'category_name',
                        responsivePriority: 100,

                        render: function(data) {
                            return escapeHtml(data || '-');
                        },
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css('text-align', 'center');
                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | UKURAN
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: null,
                        responsivePriority: 100,
                        className: 'text-center',

                        render: function(data, type, row) {
                            const length = formatSize(row.length);
                            const width = formatSize(row.width);

                            return `
                                <span>
                                    ${escapeHtml(width)}
                                    x
                                    ${escapeHtml(length)}
                                    cm
                                </span>
                            `;
                        },
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css('text-align', 'center');
                        }
                    },



                    /*
                    |--------------------------------------------------------------------------
                    | KOMPONEN
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: null,
                        responsivePriority: 100,

                        render: function(data, type, row) {
                            const materialCount =
                                Number(row.material_count) || 0;

                            const laminationCount =
                                Number(row.lamination_count) || 0;

                            const total =
                                materialCount + laminationCount;

                            if (total === 0) {
                                return `
                                <span class="text-center">
                                    Belum ada komponen
                                </span>
                                `;
                            }

                            const parts = [];

                            if (materialCount > 0) {
                                parts.push(
                                    `${materialCount} Material`
                                );
                            }

                            if (laminationCount > 0) {
                                parts.push(
                                    `${laminationCount} Laminasi`
                                );
                            }

                            return `
                                    <div
                                        style="
                                                display: flex;
                                                flex-wrap: wrap;
                                                gap: 5px;
                                            ">
                                        ${parts.map(function (part) {
                                        return `
                                                                                <span class="badge">
                                                                                    ${escapeHtml(part)}
                                                                                </span>
                                                                        `;
                                        }).join('')}
                                    </div>
                                `;
                        },
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css('text-align', 'center');
                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | AKSI
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'display_product_id',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 100,
                        className: 'text-center',

                        render: function(data) {
                            const id = encodeURIComponent(data);
                            console.log('Encoded ID:', data);
                            return `
                                <div class="data-cell-actions"
                                    style="
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                gap: 5px;
                                            ">

                                    <!-- VIEW -->
                                    <button type="button" class="btn--icon btn-details-display" data-id="${id}" aria-label="View">
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </button>


                                    <!-- ADD COMPONENT -->
                                    <button
                                        type="button"
                                        class="btn--icon btn-add-display-component"
                                        data-id="${id}"
                                        aria-label="Tambah Komponen"
                                    >
                                        <i class="bi bi-folder-plus"></i>
                                    </button>

                                    <!-- EDIT -->
                                    <button type="button" class="btn--icon btn-edit-display" data-id="${id}" aria-label="Edit">
                                        <i class="bi bi-pen"></i>
                                    </button>


                                    <!-- DELETE -->
                                    <button type="button" class="btn--icon btn-delete-display" data-id="${id}" aria-label="Hapus">
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
                    zeroRecords: 'Data Display tidak ditemukan',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari Display...'
                },


                /*
                |--------------------------------------------------------------------------
                | INIT COMPLETE
                |--------------------------------------------------------------------------
                */

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
            | DELETE DISPLAY PRODUCT
            |--------------------------------------------------------------------------
            */

            let displayProductIdToDelete = null;

            const modalDeleteDisplay = document.getElementById(
                'modalDeleteDisplay'
            );

            const btnTutupDeleteDisplay = document.getElementById(
                'btnTutupDeleteDisplay'
            );

            const btnBatalDeleteDisplay = document.getElementById(
                'btnBatalDeleteDisplay'
            );

            const btnConfirmDeleteDisplay = document.getElementById(
                'btnConfirmDeleteDisplay'
            );

            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL DELETE
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const deleteButton = event.target.closest(
                    '.btn-delete-display'
                );

                if (!deleteButton) {
                    return;
                }

                displayProductIdToDelete = deleteButton.dataset.id;

                modalDeleteDisplay.classList.add('is-open');

            });

            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupDeleteDisplayModal() {

                modalDeleteDisplay.classList.remove('is-open');

                displayProductIdToDelete = null;

                btnConfirmDeleteDisplay.disabled = false;

                btnConfirmDeleteDisplay.innerHTML =
                    '<i class="bi bi-trash3-fill"></i> Hapus';

            }

            /*
            |--------------------------------------------------------------------------
            | TOMBOL TUTUP DAN BATAL
            |--------------------------------------------------------------------------
            */

            btnTutupDeleteDisplay.addEventListener(
                'click',
                tutupDeleteDisplayModal
            );

            btnBatalDeleteDisplay.addEventListener(
                'click',
                tutupDeleteDisplayModal
            );

            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modalDeleteDisplay.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalDeleteDisplay) {

                        tutupDeleteDisplayModal();

                    }

                }
            );

            /*
            |--------------------------------------------------------------------------
            | TEKAN ESC
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modalDeleteDisplay.classList.contains('is-open')
                    ) {

                        tutupDeleteDisplayModal();

                    }

                }
            );

            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI HAPUS DISPLAY PRODUCT
            |--------------------------------------------------------------------------
            */

            btnConfirmDeleteDisplay.addEventListener(
                'click',
                async function() {

                    /*
                    |--------------------------------------------------------------------------
                    | CEK ID DISPLAY PRODUCT
                    |--------------------------------------------------------------------------
                    */

                    if (!displayProductIdToDelete) {
                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CEGAH DOUBLE CLICK
                    |--------------------------------------------------------------------------
                    */

                    btnConfirmDeleteDisplay.disabled = true;

                    btnConfirmDeleteDisplay.textContent = 'Menghapus...';

                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | REQUEST DELETE
                        |--------------------------------------------------------------------------
                        */

                        const response = await fetch(
                            `/admin/production-cost-finishing-displays/${displayProductIdToDelete}/delete`, {
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

                            tutupDeleteDisplayModal();

                            /*
                            |--------------------------------------------------------------------------
                            | RELOAD DATATABLE
                            |--------------------------------------------------------------------------
                            */

                            displayProductTable.ajax.reload(
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
                                'Display Product gagal dihapus.'
                            );

                            btnConfirmDeleteDisplay.disabled = false;

                            btnConfirmDeleteDisplay.innerHTML =
                                '<i class="bi bi-trash3-fill"></i> Hapus';

                        }

                    } catch (error) {

                        console.error(error);

                        showToast(
                            'error',
                            'Terjadi kesalahan saat menghapus Display Product.'
                        );

                        btnConfirmDeleteDisplay.disabled = false;

                        btnConfirmDeleteDisplay.innerHTML =
                            '<i class="bi bi-trash3-fill"></i> Hapus';

                    }

                }
            );

        })

        document.addEventListener('DOMContentLoaded', function() {
            /*
            |--------------------------------------------------------------------------
            | NAVIGASI TAMBAH KOMPONEN
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const addComponentButton = event.target.closest(
                    '.btn-add-display-component'
                );

                if (!addComponentButton) {
                    return;
                }

                const configurationId =
                    addComponentButton.dataset.id;

                if (!configurationId) {

                    showToast(
                        'error',
                        'ID Header Display tidak ditemukan.'
                    );

                    return;
                }

                const encodedId = btoa(configurationId);

                window.location.href =
                    `/admin/production-cost-finishing-displays/${encodedId}/add-component`;

            });
        });

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | NAVIGASI DETAILS KONFIGURASI DISPLAY
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const detailsButton = event.target.closest(
                    '.btn-details-display'
                );

                if (!detailsButton) {
                    return;
                }

                const configurationId = detailsButton.dataset.id;

                if (!configurationId) {

                    showToast(
                        'error',
                        'ID Konfigurasi Display tidak ditemukan.'
                    );

                    return;
                }

                const encodedId = btoa(
                    decodeURIComponent(configurationId)
                );

                window.location.href =
                    `/admin/production-cost-finishing-displays/${encodedId}/details`;

            });

        });

        document.addEventListener('DOMContentLoaded', function() {
            // =========================================
            // EDIT DISPLAY PRODUCT
            // =========================================
            $(document).on('click', '.btn-edit-display', function() {
                const id = $(this).data('id');

                if (!id) {
                    showToast('error', 'ID Display Product tidak ditemukan.');
                    return;
                }

                const encodedId = btoa(
                    decodeURIComponent(id)
                );

                const url = "{{ route('admin_edit_component_pc_finishing_display', ':id') }}"
                    .replace(':id', encodeURIComponent(encodedId));

                window.location.href = url;
            });
        })
    </script>
@endsection

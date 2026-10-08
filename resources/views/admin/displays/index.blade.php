@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">MASTER DATA · DISPLAY</span>
                        {{-- <h1 class="hero-title">Daftar Material</h1> --}}
                        <p class="hero-sub">Kelola dan pantau seluruh data display yang digunakan dalam proses produksi dan
                            perhitungan estimasi harga.</p>
                    </div>
                    <div class="hero-actions" style="display: flex; justify-content: right;">
                        {{-- <a href="{{ route('admin_export_material') }}" class="btn btn--ghost"><svg
                                viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg> Export</a>
                        <a href="{{ asset('templates/template_material.xlsx') }}" class="btn btn--ghost">

                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Download Template

                        </a>
                        <button class="btn btn--ghost" id="btnImportMaterial"><svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg> Import</button> --}}

                        <a href="{{ route('admin_create_display') }}" class="btn btn--primary" id="btnTambahMaterial">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Display
                        </a>
                    </div>
                </section>
                <div class="grid">

                    <section class="col-12 card">

                        <div class="card-head">

                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Daftar Display
                                </span>

                            </div>

                        </div>


                        {{-- TOOLBAR: PENCARIAN + FILTER --}}

                        <div id="displayToolbar"
                            style="
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                align-items: center;
                margin-bottom: 20px;
            ">

                            <div style="flex: 1; min-width: 220px;">
                                <input type="text" id="filterSearch" class="input"
                                    placeholder="Cari display atau kategori" autocomplete="off">
                            </div>

                            <div style="flex: 1; width: 220px;">
                                <select id="filterCategory" class="select select2">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"> {{ $category->name }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div style="flex: 1; width: 180px;">
                                <select id="filterStatus" class="select select2">
                                    <option value="">Semua Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>

                        </div>


                        {{-- GRID CARD --}}

                        <div id="displayGrid"
                            style="
                display: grid;
                grid-template-columns:
                    repeat(auto-fill, minmax(200px, 1fr));
                gap: 16px;
            ">
                        </div>


                        <div id="displayState"
                            style="
                display: none;
                padding: 24px;
                text-align: center;
                border: 1px dashed var(--border) !important;
                border-radius: 8px;
                color: #64748b;
            ">
                        </div>


                        {{-- PAGINATION --}}

                        <div id="displayPagination"
                            style="
                display: none;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                flex-wrap: wrap;
                margin-top: 24px;
            ">

                            <span id="displayPaginationInfo" style="font-size: 13px; color: #64748b;"></span>

                            <div id="displayPaginationButtons" style="display: flex; gap: 6px; flex-wrap: wrap;"></div>

                        </div>

                    </section>

                </div>
            </main>
            <div data-shell-footer></div>
        </div>
    </div>

    <!-- Modal Import Material -->
    <div class="modal-overlay" id="modalImportMaterial">

        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">

                <div>
                    <span class="eyebrow">MASTER DATA · MATERIAL</span>
                    <br>
                    <span class="eyebrow">Import Material</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseImportMaterial" aria-label="Tutup">

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
                        Pilih file Excel yang berisi data material untuk diimport
                        ke dalam sistem.
                    </p>
                </div>


                <!-- Custom File Input -->
                <label for="fileImportMaterial" class="file-upload-box" id="fileUploadBox">

                    <input type="file" id="fileImportMaterial" name="file" accept=".xlsx,.xls" hidden>


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

                <button type="button" class="btn btn--ghost" id="btnCancelImportMaterial">

                    Batal

                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportMaterial">

                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8 5-5 5 5" />
                    </svg>

                    Import Data

                </button>

            </div>

        </div>

    </div>



    <!-- MODAL DELETE DISPLAY -->
    <div class="modal-overlay" id="modalDeleteDisplay">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MASTER DATA · DISPLAY
                    </span>
                    <br>
                    <span class="eyebrow">
                        Hapus Display
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
                            Hapus Display?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus display ini?
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
        $('#filterCategory').select2({
            placeholder: 'Semua Kategori',
            allowClear: false,
            width: '100%'
        });

        $('#filterStatus').select2({
            placeholder: 'Semua Status',
            allowClear: false,
            width: '100%'
        });

        document.addEventListener('DOMContentLoaded', function() {

            const DATA_URL = "{{ route('admin_display_data') }}";

            const filterSearch = document.getElementById('filterSearch');
            const displayGrid = document.getElementById('displayGrid');
            const displayState = document.getElementById('displayState');
            const displayPagination = document.getElementById('displayPagination');
            const displayPaginationInfo = document.getElementById('displayPaginationInfo');
            const displayPaginationButtons = document.getElementById('displayPaginationButtons');


            /*
            |--------------------------------------------------------------------------
            | STATE
            |--------------------------------------------------------------------------
            */

            let currentPage = 1;
            let searchTimer = null;
            let requestCounter = 0;


            /*
            |--------------------------------------------------------------------------
            | SKELETON LOADING
            |--------------------------------------------------------------------------
            */

            function showSkeleton(count = 8) {

                displayGrid.innerHTML = '';

                for (let i = 0; i < count; i++) {

                    const skeleton = document.createElement('div');

                    skeleton.className = 'display-skeleton';

                    skeleton.innerHTML = `
                <div class="display-skeleton-cover"></div>
                <div class="display-skeleton-body">
                    <div class="display-skeleton-line"></div>
                    <div class="display-skeleton-line short"></div>
                </div>
            `;

                    displayGrid.appendChild(skeleton);
                }

                displayState.style.display = 'none';

                displayPagination.style.display = 'none';
            }


            /*
            |--------------------------------------------------------------------------
            | LOAD DATA
            |--------------------------------------------------------------------------
            */

            async function loadDisplays() {

                const requestId = ++requestCounter;

                const params = new URLSearchParams();

                params.set('page', currentPage);

                const search = filterSearch.value.trim();
                const categoryId = $('#filterCategory').val();
                const status = $('#filterStatus').val();

                if (search) {
                    params.set('search', search);
                }

                if (categoryId) {
                    params.set('category_id', categoryId);
                }

                if (status) {
                    params.set('status', status);
                }

                showSkeleton();

                try {

                    const response = await fetch(DATA_URL + '?' + params.toString(), {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const result = await response.json();

                    /* ABAIKAN RESPONS LAMA */
                    if (requestId !== requestCounter) {
                        return;
                    }

                    if (!response.ok || !result.success) {

                        displayGrid.innerHTML = '';
                        displayPagination.style.display = 'none';
                        displayState.textContent = 'Data display tidak dapat dimuat.';
                        displayState.style.display = '';

                        showToast(
                            'error',
                            result.message || 'Data display tidak dapat dimuat.'
                        );

                        return;
                    }

                    /*
                    | HALAMAN KOSONG SETELAH HAPUS (MISAL CARD TERAKHIR DI HALAMAN AKHIR):
                    | MUNDUR KE HALAMAN TERAKHIR YANG MASIH ADA DATANYA.
                    */
                    if (!result.data.length && result.pagination.total > 0 && currentPage > 1) {

                        currentPage = Math.max(1, result.pagination.last_page);

                        loadDisplays();

                        return;
                    }

                    renderDisplays(result.data);

                    renderPagination(result.pagination);

                } catch (error) {

                    if (requestId !== requestCounter) {
                        return;
                    }

                    console.error('Gagal memuat display:', error);

                    displayGrid.innerHTML = '';
                    displayPagination.style.display = 'none';
                    displayState.textContent = 'Data display tidak dapat dimuat.';
                    displayState.style.display = '';

                    showToast(
                        'error',
                        'Terjadi kesalahan saat memuat display.'
                    );
                }
            }

            /* DIPAKAI OLEH JS HAPUS (BAGIAN D) */
            window.reloadDisplayCards = loadDisplays;


            /*
            |--------------------------------------------------------------------------
            | RENDER CARD
            |--------------------------------------------------------------------------
            */

            function createActionButton(extraClass, iconClass, title, id) {

                const button = document.createElement('button');

                button.type = 'button';

                /* CLASS DAN data-id SAMA DENGAN VERSI DATATABLE */
                button.className = extraClass;

                button.dataset.id = id;

                button.title = title;

                button.setAttribute('aria-label', title);

                button.innerHTML = '<i class="' + iconClass + '"></i>';

                button.style.cssText = `
            width: 30px;
            height: 30px;
            padding: 0;
            border: 0;
            border-radius: 6px;
            background: rgba(15, 23, 42, 0.75);
            color: #fff;
            cursor: pointer;
        `;

                return button;
            }

            function renderDisplays(items) {

                displayGrid.innerHTML = '';

                /* EMPTY STATE */
                if (!items.length) {

                    displayState.textContent = 'Data display tidak ditemukan.';
                    displayState.style.display = '';

                    return;
                }

                displayState.style.display = 'none';

                items.forEach(function(item) {

                    const card = document.createElement('div');

                    card.className = 'display-product-card';

                    card.dataset.id = item.id;

                    card.style.cssText = `
                position: relative;
                border: 1px solid var(--border);
                border-radius: 8px;
                overflow: hidden;
            `;


                    /* COVER */
                    const cover = document.createElement('div');

                    cover.style.cssText = `
                position: relative;
                width: 100%;
                height: 160px;
                overflow: hidden;
                background: #f8fafc;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #64748b;
                font-size: 13px;
            `;

                    if (item.cover_url) {

                        const image = document.createElement('img');

                        image.src = item.cover_url;
                        image.alt = item.display_name;

                        image.style.cssText = `
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                `;

                        cover.appendChild(image);

                    } else {

                        cover.textContent = 'Tidak ada gambar';
                    }


                    /* BADGE JUMLAH GAMBAR */
                    const countBadge = document.createElement('span');

                    countBadge.textContent = item.image_count + ' gambar';

                    countBadge.style.cssText = `
                position: absolute;
                top: 10px;
                right: 10px;
                padding: 2px 8px;
                font-size: 12px;
                border-radius: 6px;
                background: rgba(15, 23, 42, 0.75);
                color: #fff;
            `;

                    cover.appendChild(countBadge);


                    /* ACTION BUTTONS */
                    const actions = document.createElement('div');

                    actions.style.cssText = `
                position: absolute;
                top: 10px;
                left: 10px;
                display: flex;
                gap: 6px;
                z-index: 2;
            `;

                    actions.appendChild(
                        createActionButton(
                            'btn-view-display-product',
                            'bi bi-arrows-fullscreen',
                            'Lihat',
                            item.id
                        )
                    );

                    actions.appendChild(
                        createActionButton(
                            'btn-edit-display-product',
                            'bi bi-pen',
                            'Edit',
                            item.id
                        )
                    );

                    actions.appendChild(
                        createActionButton(
                            'btn-delete-display-product',
                            'bi bi-trash3-fill',
                            'Hapus',
                            item.id
                        )
                    );


                    /* INFO */
                    const info = document.createElement('div');

                    info.style.cssText = `
                padding: 12px 14px;
            `;

                    const displayName = document.createElement('div');

                    displayName.textContent = item.display_name;

                    displayName.title = item.display_name;

                    displayName.style.cssText = `
                font-weight: 600;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            `;

                    const categoryName = document.createElement('div');

                    categoryName.textContent = item.category_name;

                    categoryName.style.cssText = `
                margin-top: 2px;
                font-size: 13px;
                color: #64748b;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            `;

                    const meta = document.createElement('div');

                    meta.style.cssText = `
                margin-top: 10px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
            `;

                    /* UKURAN: LEBAR × PANJANG (SAMA DENGAN VERSI DATATABLE) */
                    const size = document.createElement('span');

                    size.textContent = item.width + ' × ' + item.length;

                    size.style.cssText = `
                font-size: 13px;
                white-space: nowrap;
            `;

                    /* STATUS */
                    const statusBadge = document.createElement('span');

                    statusBadge.className =
                        'badge ' + (item.status === 'Active' ? 'success' : 'danger') + ' dot';

                    statusBadge.textContent = item.status;

                    meta.appendChild(size);

                    meta.appendChild(statusBadge);

                    info.appendChild(displayName);

                    info.appendChild(categoryName);

                    info.appendChild(meta);


                    /* APPEND ELEMENT */
                    card.appendChild(actions);

                    card.appendChild(cover);

                    card.appendChild(info);

                    displayGrid.appendChild(card);
                });
            }


            /*
            |--------------------------------------------------------------------------
            | RENDER PAGINATION
            |--------------------------------------------------------------------------
            */

            function renderPagination(pagination) {

                displayPaginationButtons.innerHTML = '';

                if (!pagination || !pagination.total) {

                    displayPagination.style.display = 'none';

                    return;
                }

                displayPagination.style.display = 'flex';

                displayPaginationInfo.textContent =
                    `Menampilkan ${pagination.from} sampai ${pagination.to} dari ${pagination.total} display`;

                /* SATU HALAMAN SAJA: TIDAK PERLU TOMBOL */
                if (pagination.last_page <= 1) {
                    return;
                }

                function addButton(label, page, disabled, active) {

                    const button = document.createElement('button');

                    button.type = 'button';

                    button.textContent = label;

                    button.className = active ? 'btn btn--primary' : 'btn btn--ghost';

                    if (disabled) {
                        button.disabled = true;
                    }

                    button.addEventListener('click', function() {

                        currentPage = page;

                        loadDisplays();
                    });

                    displayPaginationButtons.appendChild(button);
                }

                addButton(
                    'Sebelumnya',
                    pagination.current_page - 1,
                    pagination.current_page <= 1,
                    false
                );

                let end = Math.min(pagination.last_page, Math.max(1, pagination.current_page - 2) + 4);
                let start = Math.max(1, end - 4);

                for (let page = start; page <= end; page++) {
                    addButton(String(page), page, false, page === pagination.current_page);
                }

                addButton(
                    'Berikutnya',
                    pagination.current_page + 1,
                    pagination.current_page >= pagination.last_page,
                    false
                );
            }


            /*
            |--------------------------------------------------------------------------
            | EVENT FILTER
            |--------------------------------------------------------------------------
            */

            filterSearch.addEventListener('input', function() {

                clearTimeout(searchTimer);

                searchTimer = setTimeout(function() {

                    currentPage = 1;

                    loadDisplays();

                }, 400);
            });

            /* select2 memicu event jQuery, bukan event native */
            $('#filterCategory, #filterStatus').on('change', function() {

                currentPage = 1;

                loadDisplays();
            });

            /*
            |--------------------------------------------------------------------------
            | DELETE DISPLAY
            |--------------------------------------------------------------------------
            */

            let displayIdToDelete = null;

            const modalDeleteDisplay =
                document.getElementById(
                    'modalDeleteDisplay'
                );

            const btnTutupDeleteDisplay =
                document.getElementById(
                    'btnTutupDeleteDisplay'
                );

            const btnBatalDeleteDisplay =
                document.getElementById(
                    'btnBatalDeleteDisplay'
                );

            const btnConfirmDeleteDisplay =
                document.getElementById(
                    'btnConfirmDeleteDisplay'
                );


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL DELETE
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                function(event) {

                    const deleteButton =
                        event.target.closest(
                            '.btn-delete-display-product'
                        );

                    if (!deleteButton) {
                        return;
                    }

                    displayIdToDelete =
                        deleteButton.dataset.id;

                    modalDeleteDisplay.classList.add(
                        'is-open'
                    );
                }
            );


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupDeleteDisplayModal() {

                modalDeleteDisplay.classList.remove(
                    'is-open'
                );

                displayIdToDelete = null;
            }


            /*
            |--------------------------------------------------------------------------
            | BUTTON TUTUP
            |--------------------------------------------------------------------------
            */

            btnTutupDeleteDisplay.addEventListener(
                'click',
                tutupDeleteDisplayModal
            );


            /*
            |--------------------------------------------------------------------------
            | BUTTON BATAL
            |--------------------------------------------------------------------------
            */

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

                    if (
                        event.target ===
                        modalDeleteDisplay
                    ) {
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
                        modalDeleteDisplay.classList.contains(
                            'is-open'
                        )
                    ) {
                        tutupDeleteDisplayModal();
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI HAPUS DISPLAY
            |--------------------------------------------------------------------------
            */

            btnConfirmDeleteDisplay.addEventListener(
                'click',
                async function() {

                    /*
                    |--------------------------------------------------------------------------
                    | CEK ID DISPLAY
                    |--------------------------------------------------------------------------
                    */

                    if (!displayIdToDelete) {
                        return;
                    }


                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | REQUEST DELETE
                        |--------------------------------------------------------------------------
                        */

                        const response =
                            await fetch(
                                `/admin/displays/${displayIdToDelete}/delete`, {
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
                        | RESPONSE JSON
                        |--------------------------------------------------------------------------
                        */

                        const result =
                            await response.json();


                        /*
                        |--------------------------------------------------------------------------
                        | BERHASIL
                        |--------------------------------------------------------------------------
                        */

                        if (
                            response.ok &&
                            result.success
                        ) {

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

                            // displayProductTable
                            //     .ajax
                            //     .reload(
                            //         null,
                            //         false
                            //     );

                            // window.reloadDisplayCards = loadDisplays;

                            loadDisplays();

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
                                'Display gagal dihapus.'
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
                            'Terjadi kesalahan saat menghapus Display.'
                        );
                    }
                }
            );

            /*
            |--------------------------------------------------------------------------
            | INITIAL LOAD
            |--------------------------------------------------------------------------
            */

            loadDisplays();

        });

        document.addEventListener('DOMContentLoaded', function() {



            document.addEventListener('click', function(event) {

                const detailsButton = event.target.closest(
                    '.btn-view-display-product'
                );

                if (!detailsButton) {
                    return;
                }

                event.preventDefault();

                const displayId = detailsButton.dataset.id;

                if (!displayId) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | ENCODE ID
                |--------------------------------------------------------------------------
                */
                const encodedId = btoa(String(displayId));

                /*
                |--------------------------------------------------------------------------
                | URL DETAILS
                |--------------------------------------------------------------------------
                */
                const url = "{{ route('admin_display_details', ':id') }}"
                    .replace(':id', encodedId);

                /*
                |--------------------------------------------------------------------------
                | PINDAH KE HALAMAN DETAILS
                |--------------------------------------------------------------------------
                */
                window.location.href = url;
            });

            document.addEventListener('click', function(event) {

                const editButton = event.target.closest(
                    '.btn-edit-display-product'
                );

                if (!editButton) {
                    return;
                }

                event.preventDefault();

                const displayId = editButton.dataset.id;

                if (!displayId) {
                    return;
                }

                const encodedId = btoa(String(displayId));

                const url = "{{ route('admin_edit_display', ':id') }}"
                    .replace(':id', encodedId);

                window.location.href = url;
            });
        });
    </script>
@endsection

@extends('admin_master')

@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">MASTER DATA · GALERI SAMPEL</span>
                        {{-- <h1 class="hero-title">Daftar Material</h1> --}}
                        <p class="hero-sub">Kelola gambar contoh hasil cetak untuk setiap kategori produk sebagai
                            referensi visual saat membuat estimasi.</p>
                    </div>
                    <div class="hero-actions" style="display: flex; justify-content: right;">

                        <a href="{{ route('admin_create_gallery_samples') }}" class="btn btn--primary"
                            id="btnTambahMaterial">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Galeri
                        </a>
                    </div>
                </section>

                <div class="grid">

                    <section class="col-12 card">

                        <div class="card-head">

                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Daftar Galeri Sampel
                                </span>

                            </div>

                        </div>


                        {{-- =========================================================
                            TOOLBAR: PENCARIAN + FILTER
                        ========================================================== --}}

                        <div id="galleryToolbar"
                            style="
                                display: flex;
                                gap: 12px;
                                flex-wrap: wrap;
                                align-items: center;
                                margin-bottom: 20px;
                            ">

                            <div style="flex: 1; min-width: 220px;">
                                <input type="text" id="filterSearch" class="input"
                                    placeholder="Cari mesin atau kategori" autocomplete="off">
                            </div>

                            <div style="flex: 1; width: 220px;">
                                <select id="filterEngine" class="select select2">
                                    <option value="">Semua Mesin</option>
                                    @foreach ($engines as $engine)
                                        <option value="{{ $engine->id }}"> {{ $engine->name }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div style="flex: 1; width: 220px;">
                                <select id="filterCategory" class="select select2">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"> {{ $category->name }} </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>


                        {{-- =========================================================
                            GRID CARD
                        ========================================================== --}}

                        <div id="galleryGrid"
                            style="
                                display: grid;
                                grid-template-columns:
                                    repeat(auto-fill, minmax(200px, 1fr));
                                gap: 16px;
                            ">
                        </div>


                        <div id="galleryState"
                            style="
                                display: none;
                                padding: 24px;
                                text-align: center;
                                border: 1px dashed var(--border) !important;
                                border-radius: 8px;
                                color: #64748b;
                            ">
                            Memuat data...
                        </div>


                        {{-- =========================================================
                            PAGINATION
                        ========================================================== --}}

                        <div id="galleryPagination"
                            style="
                                display: none;
                                align-items: center;
                                justify-content: space-between;
                                gap: 12px;
                                flex-wrap: wrap;
                                margin-top: 24px;
                            ">

                            <span id="galleryPaginationInfo" style="font-size: 13px; color: #64748b;"></span>

                            <div id="galleryPaginationButtons" style="display: flex; gap: 6px; flex-wrap: wrap;"></div>

                        </div>

                    </section>

                </div>

            </main>
        </div>
    </div>

    <!-- DELETE GALLERY SAMPLE -->

    <div class="modal-overlay" id="modalDeleteGallerySample">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->
            <div class="modal-header">

                <div>
                    <span class="eyebrow">
                        MASTER DATA · GALERI SAMPEL
                    </span>
                    <br>
                    <span class="eyebrow">
                        Hapus Galeri Sampel
                    </span>
                </div>

                <button type="button" class="modal-close" id="btnTutupDeleteGallerySample" aria-label="Tutup">
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
                            Hapus Galeri Sampel?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus seluruh galeri sampel
                            untuk mesin dan kategori ini?
                            Data gambar yang sudah dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteGallerySample">
                    Batal
                </button>

                <button type="button" class="btn btn--danger" id="btnConfirmDeleteGallerySample">
                    <i class="bi bi-trash3-fill"></i>
                    Hapus
                </button>

            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script>
        $('#filterEngine').select2({
            placeholder: 'Semua Mesin',
            allowClear: false,
            width: '100%'
        });

        $('#filterCategory').select2({
            placeholder: 'Semua Kategori',
            allowClear: false,
            width: '100%'
        });

        document.addEventListener('DOMContentLoaded', function() {

            const DATA_URL = "{{ route('admin_gallery_samples_data') }}";

            const filterSearch = document.getElementById('filterSearch');
            const galleryGrid = document.getElementById('galleryGrid');
            const galleryState = document.getElementById('galleryState');
            const galleryPagination = document.getElementById('galleryPagination');
            const galleryPaginationInfo = document.getElementById('galleryPaginationInfo');
            const galleryPaginationButtons = document.getElementById('galleryPaginationButtons');


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
            | DELETE GALLERY SAMPLE STATE
            |--------------------------------------------------------------------------
            */

            let galleryEngineIdToDelete = null;
            let galleryCategoryIdToDelete = null;

            const modalDeleteGallerySample =
                document.getElementById('modalDeleteGallerySample');

            const btnTutupDeleteGallerySample =
                document.getElementById('btnTutupDeleteGallerySample');

            const btnBatalDeleteGallerySample =
                document.getElementById('btnBatalDeleteGallerySample');

            const btnConfirmDeleteGallerySample =
                document.getElementById('btnConfirmDeleteGallerySample');

            const DELETE_URL =
                "{{ route('admin_gallery_samples_destroy') }}";

            function showSkeleton(count = 8) {

                galleryGrid.innerHTML = '';

                for (let i = 0; i < count; i++) {

                    const skeleton = document.createElement('div');

                    skeleton.className = 'gallery-skeleton';

                    skeleton.innerHTML = `
                        <div class="gallery-skeleton-cover"></div>
                        <div class="gallery-skeleton-body">
                            <div class="gallery-skeleton-line"></div>
                            <div class="gallery-skeleton-line short"></div>
                        </div>
                    `;

                    galleryGrid.appendChild(skeleton);
                }

                galleryState.style.display = 'none';

                galleryPagination.style.display = 'none';
            }

            /*
            |--------------------------------------------------------------------------
            | LOAD DATA
            |--------------------------------------------------------------------------
            */

            async function loadGallery() {

                const requestId = ++requestCounter;

                const params = new URLSearchParams();

                params.set('page', currentPage);

                const search = filterSearch.value.trim();
                const engineId = $('#filterEngine').val();
                const categoryId = $('#filterCategory').val();

                if (search) {
                    params.set('search', search);
                }

                if (engineId) {
                    params.set('engine_id', engineId);
                }

                if (categoryId) {
                    params.set('category_id', categoryId);
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

                        galleryGrid.innerHTML = '';
                        galleryPagination.style.display = 'none';
                        galleryState.textContent = 'Data galeri sampel tidak dapat dimuat.';

                        showToast(
                            'error',
                            result.message || 'Data galeri sampel tidak dapat dimuat.'
                        );

                        return;
                    }

                    renderGallery(result.data);

                    renderPagination(result.pagination);

                } catch (error) {

                    if (requestId !== requestCounter) {
                        return;
                    }

                    console.error('Gagal memuat galeri sampel:', error);

                    galleryGrid.innerHTML = '';
                    galleryPagination.style.display = 'none';
                    galleryState.textContent = 'Data galeri sampel tidak dapat dimuat.';

                    showToast(
                        'error',
                        'Terjadi kesalahan saat memuat galeri sampel.'
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | RENDER CARD
            |--------------------------------------------------------------------------
            */

            function renderGallery(items) {

                galleryGrid.innerHTML = '';

                /* EMPTY STATE */
                if (!items.length) {

                    galleryState.textContent = 'Belum ada galeri sampel yang sesuai.';
                    galleryState.style.display = '';

                    return;
                }

                galleryState.style.display = 'none';

                items.forEach(function(item) {

                    const card = document.createElement('div');

                    card.className = 'gallery-sample-card';

                    card.dataset.engineId = item.engine_id;
                    card.dataset.categoryId = item.category_id;

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
                        image.alt = item.engine_name + ' - ' + item.category_name;
                        // image.loading = 'lazy';

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


                    /* INFO */
                    const info = document.createElement('div');

                    info.style.cssText = `
                        padding: 12px 14px;
                    `;

                    const engineName = document.createElement('div');

                    engineName.textContent = item.engine_name;

                    engineName.style.cssText = `
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

                    info.appendChild(engineName);

                    info.appendChild(categoryName);

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

                    const detailButton = document.createElement('button');

                    detailButton.type = 'button';
                    detailButton.innerHTML = '<i class="bi bi-arrows-fullscreen"></i>';
                    detailButton.title = 'Detail';

                    detailButton.style.cssText = `
                        width: 30px;
                        height: 30px;
                        padding: 0;
                        border: 0;
                        border-radius: 6px;
                        background: rgba(15, 23, 42, 0.75);
                        color: #fff;
                        cursor: pointer;
                    `;

                    detailButton.addEventListener(
                        'click',
                        function() {

                            const detailUrl =
                                "{{ route('admin_gallery_samples_details', ['engine_id' => '__ENGINE_ID__', 'category_id' => '__CATEGORY_ID__']) }}"
                                .replace(
                                    '__ENGINE_ID__',
                                    item.engine_id
                                )
                                .replace(
                                    '__CATEGORY_ID__',
                                    item.category_id
                                );

                            window.location.href = detailUrl;

                        });

                    const editButton = document.createElement('button');

                    editButton.type = 'button';
                    editButton.innerHTML = '<i class="bi bi-pen"></i>';
                    editButton.title = 'Edit';

                    editButton.style.cssText = `
                        width: 30px;
                        height: 30px;
                        padding: 0;
                        border: 0;
                        border-radius: 6px;
                        background: rgba(15, 23, 42, 0.75);
                        color: #fff;
                        cursor: pointer;
                    `;

                    /*
                    |--------------------------------------------------------------------------
                    | EDIT
                    |--------------------------------------------------------------------------
                    */

                    editButton.addEventListener('click', function() {

                        const editUrl =
                            "{{ route('admin_gallery_samples_edit', ['engine_id' => '__ENGINE_ID__', 'category_id' => '__CATEGORY_ID__']) }}"
                            .replace(
                                '__ENGINE_ID__',
                                item.engine_id
                            )
                            .replace(
                                '__CATEGORY_ID__',
                                item.category_id
                            );

                        window.location.href = editUrl;

                    });


                    const deleteButton = document.createElement('button');

                    deleteButton.type = 'button';
                    deleteButton.innerHTML = '<i class="bi bi-trash3-fill"></i>';
                    deleteButton.title = 'Hapus';

                    deleteButton.style.cssText = `
                        width: 30px;
                        height: 30px;
                        padding: 0;
                        border: 0;
                        border-radius: 6px;
                        background: rgba(15, 23, 42, 0.75);
                        color: #fff;
                        cursor: pointer;
                    `;

                    deleteButton.addEventListener('click', function() {

                        galleryEngineIdToDelete = item.engine_id;
                        galleryCategoryIdToDelete = item.category_id;

                        modalDeleteGallerySample.classList.add('is-open');
                    });

                    actions.appendChild(detailButton);
                    actions.appendChild(editButton);
                    actions.appendChild(deleteButton);

                    card.appendChild(actions);

                    /* APPEND ELEMENT */
                    card.appendChild(cover);

                    card.appendChild(info);

                    galleryGrid.appendChild(card);
                });
            }

            /*
            |--------------------------------------------------------------------------
            | DELETE GALLERY SAMPLE
            |--------------------------------------------------------------------------
            */

            function tutupDeleteGallerySampleModal() {

                modalDeleteGallerySample.classList.remove('is-open');

                galleryEngineIdToDelete = null;
                galleryCategoryIdToDelete = null;
            }

            /*
            |--------------------------------------------------------------------------
            | BUTTON TUTUP
            |--------------------------------------------------------------------------
            */

            btnTutupDeleteGallerySample.addEventListener(
                'click',
                tutupDeleteGallerySampleModal
            );


            /*
            |--------------------------------------------------------------------------
            | BUTTON BATAL
            |--------------------------------------------------------------------------
            */

            btnBatalDeleteGallerySample.addEventListener(
                'click',
                tutupDeleteGallerySampleModal
            );

            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modalDeleteGallerySample.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalDeleteGallerySample) {
                        tutupDeleteGallerySampleModal();
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
                        modalDeleteGallerySample.classList.contains('is-open')
                    ) {
                        tutupDeleteGallerySampleModal();
                    }
                }
            );

            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI HAPUS GALLERY SAMPLE
            |--------------------------------------------------------------------------
            */

            btnConfirmDeleteGallerySample.addEventListener(
                'click',
                async function() {

                    if (
                        !galleryEngineIdToDelete ||
                        !galleryCategoryIdToDelete
                    ) {
                        return;
                    }

                    try {

                        const response = await fetch(
                            DELETE_URL, {
                                method: 'DELETE',

                                headers: {

                                    'X-CSRF-TOKEN': document
                                        .querySelector(
                                            'meta[name="csrf-token"]'
                                        )
                                        .getAttribute(
                                            'content'
                                        ),

                                    'Content-Type': 'application/json',

                                    'Accept': 'application/json'
                                },

                                body: JSON.stringify({
                                    engine_id: galleryEngineIdToDelete,
                                    category_id: galleryCategoryIdToDelete
                                })
                            }
                        );

                        const result = await response.json();

                        if (
                            response.ok &&
                            result.success
                        ) {

                            tutupDeleteGallerySampleModal();

                            loadGallery();

                            showToast(
                                'success',
                                result.message
                            );

                            return;
                        }

                        showToast(
                            'error',
                            result.message ||
                            'Gallery sample gagal dihapus.'
                        );

                    } catch (error) {

                        console.error(
                            'Gagal menghapus gallery sample:',
                            error
                        );

                        showToast(
                            'error',
                            'Terjadi kesalahan saat menghapus gallery sample.'
                        );
                    }
                }
            );

            /*
            |--------------------------------------------------------------------------
            | RENDER PAGINATION
            |--------------------------------------------------------------------------
            */

            function renderPagination(pagination) {

                galleryPaginationButtons.innerHTML = '';

                if (!pagination || !pagination.total) {

                    galleryPagination.style.display = 'none';

                    return;
                }

                galleryPagination.style.display = 'flex';

                galleryPaginationInfo.textContent =
                    `Menampilkan ${pagination.from} sampai ${pagination.to} dari ${pagination.total} kombinasi`;

                /* SATU HALAMAN SAJA: TIDAK PERLU TOMBOL */
                if (pagination.last_page <= 1) {
                    return;
                }

                function addButton(label, page, disabled) {

                    const button = document.createElement('button');

                    button.type = 'button';

                    button.textContent = label;

                    button.className = 'btn btn--ghost';

                    if (page === pagination.current_page && !isNaN(Number(label))) {
                        button.className = 'btn btn--primary';
                    }

                    if (disabled) {
                        button.disabled = true;
                    }

                    button.addEventListener('click', function() {

                        currentPage = page;

                        loadGallery();
                    });

                    galleryPaginationButtons.appendChild(button);
                }

                addButton(
                    'Sebelumnya',
                    pagination.current_page - 1,
                    pagination.current_page <= 1
                );

                let start = Math.max(1, pagination.current_page - 2);
                let end = Math.min(pagination.last_page, start + 4);

                start = Math.max(1, end - 4);

                for (let page = start; page <= end; page++) {
                    addButton(String(page), page, false);
                }

                addButton(
                    'Berikutnya',
                    pagination.current_page + 1,
                    pagination.current_page >= pagination.last_page
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

                    loadGallery();

                }, 400);
            });

            /* select2 memicu event jQuery, bukan event native */
            $('#filterEngine, #filterCategory').on('change', function() {

                currentPage = 1;

                loadGallery();
            });


            /*
            |--------------------------------------------------------------------------
            | INITIAL LOAD
            |--------------------------------------------------------------------------
            */

            loadGallery();

        });
    </script>
@endsection

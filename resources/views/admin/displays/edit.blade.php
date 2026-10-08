@extends('admin_master')

@section('contents')
    <div class="shell">

        <div data-shell-sidebar></div>

        <div class="main">

            <div data-shell-topbar></div>

            <main class="content">

                <section class="hero">

                    <div class="hero-text">

                        <span class="eyebrow">
                            MASTER · DISPLAY
                        </span>

                        <p class="hero-sub">
                            Ubah informasi display beserta ukuran dan gambar produk.
                        </p>

                    </div>

                </section>

                <div class="grid">

                    <section class="col-12 card">

                        <div class="card-head">

                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Edit Display
                                </span>

                            </div>

                        </div>

                        <form method="POST" action="{{ route('admin_update_display', $displayProduct->id) }}"
                            id="displayProductForm" enctype="multipart/form-data">

                            @csrf
                            @method('PUT')

                            {{-- =========================================================
                            INFORMASI DISPLAY
                        ========================================================== --}}

                            <div class="form-grid">

                                {{-- NAMA DISPLAY --}}

                                <div class="field">

                                    <label class="field-label">

                                        Nama Display

                                        <span class="req">
                                            *
                                        </span>

                                    </label>

                                    <input type="text" name="display_name" class="input" placeholder="Contoh: X-Banner"
                                        autocomplete="off" value="{{ $displayProduct->display_name }}" required>

                                </div>

                                {{-- KATEGORI --}}
                                <div class="field">
                                    <label class="field-label"> Kategori <span class="req"> * </span></label>
                                    <select name="category_id" class="select select2" required>
                                        <option value=""> Pilih Kategori </option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"
                                                {{ $displayProduct->category_id == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- STATUS --}}

                                <div class="field">

                                    <label class="field-label">

                                        Status

                                        <span class="req">
                                            *
                                        </span>

                                    </label>

                                    <select name="status" class="select select2" required>

                                        <option value="Active" {{ $displayProduct->status === 'Active' ? 'selected' : '' }}>
                                            Active
                                        </option>

                                        <option value="Inactive"
                                            {{ $displayProduct->status === 'Inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>

                                    </select>

                                </div>

                            </div>


                            {{-- =========================================================
                            UKURAN
                        ========================================================== --}}

                            <div class="field" style="margin-top: 24px;">

                                <label class="field-label">
                                    Ukuran
                                </label>

                                <div class="form-grid">

                                    {{-- PANJANG --}}

                                    <div class="field">

                                        <label class="field-label">

                                            Panjang

                                            <span class="req">
                                                *
                                            </span>

                                        </label>

                                        <input type="number" name="length" class="input" placeholder="Contoh: 160"
                                            min="0" step="0.01" value="{{ $displayProduct->length }}" required>

                                    </div>


                                    {{-- LEBAR --}}

                                    <div class="field">

                                        <label class="field-label">

                                            Lebar

                                            <span class="req">
                                                *
                                            </span>

                                        </label>

                                        <input type="number" name="width" class="input" placeholder="Contoh: 60"
                                            min="0" step="0.01" value="{{ $displayProduct->width }}" required>

                                    </div>

                                </div>

                            </div>


                            {{-- =========================================================
                            GAMBAR PRODUK
                        ========================================================== --}}

                            <div class="field"
                                style="
                                margin-top: 24px;
                                overflow: hidden;
                            ">

                                <div
                                    style="
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                    margin-bottom: 12px;
                                ">

                                    <label class="field-label" style="margin-bottom: 0;">

                                        Gambar Produk

                                        <span class="req">
                                            *
                                        </span>

                                    </label>

                                    <button type="button" id="btnAddImage" class="btn btn--ghost">
                                        + Tambah Gambar
                                    </button>

                                </div>


                                {{-- =====================================================
                                INPUT FILE
                            ====================================================== --}}

                                <input type="file" id="displayImages" name="images[]" accept="image/*" multiple hidden>


                                {{-- =====================================================
                                IMAGE PREVIEW
                            ====================================================== --}}

                                <div id="imagePreviewContainer"
                                    style="
                                    display: grid;
                                    grid-template-columns:
                                        repeat(auto-fill, minmax(180px, 1fr));
                                    gap: 16px;
                                ">

                                    @foreach ($displayImages as $image)
                                        <div class="display-image-preview existing-image"
                                            data-image-id="{{ $image->id }}"
                                            data-image-primary="{{ $image->is_primary ? 1 : 0 }}"
                                            style="
                                            position: relative;
                                            border-radius: 8px;
                                            padding: 10px;
                                            overflow: hidden;
                                        ">

                                            {{-- IMAGE WRAPPER --}}

                                            <div
                                                style="
                                                width: 100%;
                                                height: 180px;
                                                overflow: hidden;
                                                border-radius: 6px;
                                                background: #f8fafc;
                                            ">

                                                <img src="{{ route('admin_display_image', $image->id) }}"
                                                    alt="{{ $image->image_name }}"
                                                    style="
                                                    width: 100%;
                                                    height: 100%;
                                                    object-fit: cover;
                                                    display: block;
                                                ">

                                            </div>


                                            {{-- INFO --}}

                                            <div
                                                style="
                                                margin-top: 10px;
                                                display: flex;
                                                gap: 10px;
                                                padding: 0 !important;
                                                justify-content: center;
                                                align-items: center;
                                            ">

                                                {{-- PRIMARY --}}

                                                <button type="button" class="btn btn--ghost btn-existing-primary"
                                                    data-image-id="{{ $image->id }}"
                                                    style="
                                                    width: 100%;
                                                    margin-top: 8px;
                                                    display: flex;
                                                    justify-content: center;
                                                "
                                                    {{ $image->is_primary ? 'disabled' : '' }}>

                                                    {{ $image->is_primary ? 'Gambar Utama' : 'Jadikan Utama' }}

                                                </button>


                                                {{-- DELETE --}}

                                                <button type="button" class="btn btn--ghost btn-existing-delete"
                                                    data-image-id="{{ $image->id }}"
                                                    style="
                                                    width: 40px;
                                                    margin-top: 8px;
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                ">

                                                    <i class="bi bi-trash3-fill"></i>

                                                </button>

                                            </div>

                                        </div>
                                    @endforeach

                                </div>


                                {{-- =====================================================
                                EMPTY STATE
                            ====================================================== --}}

                                <div id="imageEmptyState"
                                    style="
                                    padding: 24px;
                                    text-align: center;
                                    border: 1px dashed var(--border) !important;
                                    border-radius: 8px;
                                    color: #64748b;
                                    {{ $displayImages->count() ? 'display: none;' : '' }}
                                ">

                                    Belum ada gambar yang dipilih.

                                </div>

                            </div>


                            {{-- =========================================================
                            ACTION
                        ========================================================== --}}

                            <div class="form-actions" style="margin-top: 24px;">

                                <span class="badge dot success">
                                    Siap diperbarui
                                </span>

                                <span class="spacer"></span>

                                <a href="{{ route('admin_display') }}" class="btn btn--ghost">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn--primary">
                                    Simpan Perubahan
                                </button>

                            </div>

                        </form>

                    </section>

                </div>

            </main>

            <div data-shell-footer></div>

        </div>

    </div>
@endsection

@section('scripts')
    <script>
        $('.select2').select2({
            allowClear: false,
            width: '100%'
        });

        $('select[name="category_id"]').select2({
            placeholder: 'Pilih Kategori',
            allowClear: false,
            width: '100%'
        });

        $('select[name="status"]').select2({
            placeholder: 'Pilih Status',
            allowClear: false,
            width: '100%'
        });

        document.addEventListener('DOMContentLoaded', function() {

            const displayProductForm =
                document.getElementById('displayProductForm');

            const btnAddImage =
                document.getElementById('btnAddImage');

            const displayImages =
                document.getElementById('displayImages');

            const imagePreviewContainer =
                document.getElementById('imagePreviewContainer');

            const imageEmptyState =
                document.getElementById('imageEmptyState');

            const displayNameInput =
                displayProductForm.querySelector(
                    '[name="display_name"]'
                );

            const categoryInput = displayProductForm.querySelector('[name="category_id"]');

            const lengthInput =
                displayProductForm.querySelector(
                    '[name="length"]'
                );

            const widthInput =
                displayProductForm.querySelector(
                    '[name="width"]'
                );

            const statusInput =
                displayProductForm.querySelector(
                    '[name="status"]'
                );

            /*
            |--------------------------------------------------------------------------
            | CONFIGURATION
            |--------------------------------------------------------------------------
            */

            const MAX_IMAGE_SIZE = 2 * 1024 * 1024;

            const MAX_IMAGE_COUNT = 10;

            const ALLOWED_IMAGE_TYPES = [
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            /*
            |--------------------------------------------------------------------------
            | EXISTING IMAGE DATA
            |--------------------------------------------------------------------------
            */

            let existingImages = [];

            document
                .querySelectorAll('.existing-image')
                .forEach(function(element) {

                    existingImages.push({
                        id: element.dataset.imageId,
                        imageUrl: element.querySelector('img').src,
                        imageName: element.querySelector('img').alt,
                        isPrimary: element.dataset.imagePrimary === '1',
                        isDeleted: false
                    });

                    element.remove();
                });

            /*
            |--------------------------------------------------------------------------
            | NEW IMAGE DATA
            |--------------------------------------------------------------------------
            */

            let selectedImages = [];

            /*
            |--------------------------------------------------------------------------
            | ADD IMAGE BUTTON
            |--------------------------------------------------------------------------
            */

            btnAddImage.addEventListener(
                'click',
                function() {

                    const totalImages =
                        getActiveImageCount();

                    if (
                        totalImages >=
                        MAX_IMAGE_COUNT
                    ) {
                        showToast(
                            'error',
                            `Maksimal ${MAX_IMAGE_COUNT} gambar yang dapat ditambahkan.`
                        );

                        return;
                    }

                    displayImages.click();
                }
            );

            /*
            |--------------------------------------------------------------------------
            | HANDLE IMAGE SELECT
            |--------------------------------------------------------------------------
            */

            displayImages.addEventListener(
                'change',
                function(event) {

                    const files =
                        Array.from(event.target.files);

                    if (!files.length) {
                        return;
                    }

                    const currentCount =
                        getActiveImageCount();

                    if (
                        currentCount +
                        files.length >
                        MAX_IMAGE_COUNT
                    ) {
                        showToast(
                            'error',
                            `Maksimal ${MAX_IMAGE_COUNT} gambar yang dapat ditambahkan.`
                        );

                        displayImages.value = '';

                        return;
                    }

                    files.forEach(function(file) {

                        /*
                        |--------------------------------------------------------------------------
                        | VALIDASI TIPE FILE
                        |--------------------------------------------------------------------------
                        */

                        if (
                            !ALLOWED_IMAGE_TYPES.includes(
                                file.type
                            )
                        ) {
                            showToast(
                                'error',
                                `File "${file.name}" bukan format gambar yang diperbolehkan.`
                            );

                            return;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | VALIDASI UKURAN FILE
                        |--------------------------------------------------------------------------
                        */

                        if (
                            file.size >
                            MAX_IMAGE_SIZE
                        ) {
                            showToast(
                                'error',
                                `Ukuran "${file.name}" melebihi 2 MB.`
                            );

                            return;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | CEK DUPLIKAT FILE BARU
                        |--------------------------------------------------------------------------
                        */

                        const isDuplicate =
                            selectedImages.some(
                                function(item) {

                                    return (
                                        item.file.name ===
                                        file.name &&

                                        item.file.size ===
                                        file.size &&

                                        item.file.lastModified ===
                                        file.lastModified
                                    );
                                }
                            );

                        if (isDuplicate) {

                            showToast(
                                'error',
                                `Gambar "${file.name}" sudah ditambahkan.`
                            );

                            return;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | TAMBAHKAN FILE BARU
                        |--------------------------------------------------------------------------
                        */

                        selectedImages.push({
                            file: file,
                            isPrimary: getActiveImageCount() === 0
                        });
                    });

                    /*
                    |--------------------------------------------------------------------------
                    | RENDER PREVIEW
                    |--------------------------------------------------------------------------
                    */

                    renderImagePreview();

                    /*
                    |--------------------------------------------------------------------------
                    | RESET INPUT
                    |--------------------------------------------------------------------------
                    */

                    displayImages.value = '';
                }
            );

            /*
            |--------------------------------------------------------------------------
            | GET ACTIVE IMAGE COUNT
            |--------------------------------------------------------------------------
            */

            function getActiveImageCount() {

                const existingCount =
                    existingImages.filter(
                        function(item) {
                            return !item.isDeleted;
                        }
                    ).length;

                const newCount =
                    selectedImages.length;

                return existingCount + newCount;
            }

            /*
            |--------------------------------------------------------------------------
            | GET ACTIVE IMAGES
            |--------------------------------------------------------------------------
            */

            function getActiveImages() {

                return [
                    ...existingImages.filter(
                        function(item) {
                            return !item.isDeleted;
                        }
                    ),
                    ...selectedImages
                ];
            }

            /*
            |--------------------------------------------------------------------------
            | SET PRIMARY IMAGE
            |--------------------------------------------------------------------------
            */

            function setPrimaryImage(
                type,
                target
            ) {

                existingImages.forEach(
                    function(item) {
                        item.isPrimary = false;
                    }
                );

                selectedImages.forEach(
                    function(item) {
                        item.isPrimary = false;
                    }
                );

                if (type === 'existing') {

                    const image =
                        existingImages.find(
                            function(item) {
                                return item.id === target;
                            }
                        );

                    if (image) {
                        image.isPrimary = true;
                    }

                } else {

                    const image =
                        selectedImages[target];

                    if (image) {
                        image.isPrimary = true;
                    }
                }

                renderImagePreview();
            }

            /*
            |--------------------------------------------------------------------------
            | RENDER IMAGE PREVIEW
            |--------------------------------------------------------------------------
            */

            function renderImagePreview() {

                imagePreviewContainer.innerHTML = '';

                const activeImages =
                    getActiveImages();

                /*
                |--------------------------------------------------------------------------
                | EMPTY STATE
                |--------------------------------------------------------------------------
                */

                if (!activeImages.length) {

                    imageEmptyState.style.display = '';

                    return;
                }

                imageEmptyState.style.display = 'none';

                /*
                |--------------------------------------------------------------------------
                | EXISTING IMAGE
                |--------------------------------------------------------------------------
                */

                existingImages.forEach(
                    function(item) {

                        if (item.isDeleted) {
                            return;
                        }

                        renderExistingImage(item);
                    }
                );

                /*
                |--------------------------------------------------------------------------
                | NEW IMAGE
                |--------------------------------------------------------------------------
                */

                selectedImages.forEach(
                    function(item, index) {

                        renderNewImage(
                            item,
                            index
                        );
                    }
                );
            }

            /*
            |--------------------------------------------------------------------------
            | RENDER EXISTING IMAGE
            |--------------------------------------------------------------------------
            */

            function renderExistingImage(item) {

                const previewCard =
                    document.createElement('div');

                previewCard.className =
                    'display-image-preview';

                previewCard.style.cssText = `
                    position: relative;
                    border-radius: 8px;
                    padding: 10px;
                    overflow: hidden;
                `;

                /*
                |--------------------------------------------------------------------------
                | IMAGE WRAPPER
                |--------------------------------------------------------------------------
                */

                const imageWrapper =
                    document.createElement('div');

                imageWrapper.style.cssText = `
                    width: 100%;
                    height: 180px;
                    overflow: hidden;
                    border-radius: 6px;
                    background: #f8fafc;
                `;

                /*
                |--------------------------------------------------------------------------
                | IMAGE
                |--------------------------------------------------------------------------
                */

                const image =
                    document.createElement('img');

                image.src =
                    item.imageUrl;

                image.alt =
                    item.imageName;

                image.style.cssText = `
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                `;

                imageWrapper.appendChild(image);

                /*
                |--------------------------------------------------------------------------
                | INFO
                |--------------------------------------------------------------------------
                */

                const info =
                    document.createElement('div');

                info.style.cssText = `
                    margin-top: 10px;
                    display: flex;
                    gap: 10px;
                    padding: 0 !important;
                    justify-content: center;
                    align-items: center;
                `;

                /*
                |--------------------------------------------------------------------------
                | PRIMARY BUTTON
                |--------------------------------------------------------------------------
                */

                const primaryButton =
                    document.createElement('button');

                primaryButton.type = 'button';

                primaryButton.textContent =
                    item.isPrimary ?
                    'Gambar Utama' :
                    'Jadikan Utama';

                primaryButton.className =
                    'btn btn--ghost';

                primaryButton.style.cssText = `
                    width: 100%;
                    margin-top: 8px;
                    display: flex;
                    justify-content: center;
                `;

                if (item.isPrimary) {
                    primaryButton.disabled = true;
                }

                primaryButton.addEventListener(
                    'click',
                    function() {

                        setPrimaryImage(
                            'existing',
                            item.id
                        );
                    }
                );

                /*
                |--------------------------------------------------------------------------
                | DELETE BUTTON
                |--------------------------------------------------------------------------
                */

                const deleteButton =
                    document.createElement('button');

                deleteButton.type = 'button';

                deleteButton.innerHTML =
                    '<i class="bi bi-trash3-fill"></i>';

                deleteButton.className =
                    'btn btn--ghost';

                deleteButton.style.cssText = `
                    width: 40px;
                    margin-top: 8px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                `;

                deleteButton.addEventListener(
                    'click',
                    function() {

                        const wasPrimary =
                            item.isPrimary;

                        item.isDeleted = true;
                        item.isPrimary = false;

                        /*
                        |--------------------------------------------------------------------------
                        | JIKA GAMBAR UTAMA DIHAPUS
                        |--------------------------------------------------------------------------
                        */

                        if (
                            wasPrimary &&
                            getActiveImageCount() > 0
                        ) {
                            setFirstActiveImageAsPrimary();
                            return;
                        }

                        renderImagePreview();
                    }
                );

                /*
                |--------------------------------------------------------------------------
                | APPEND ELEMENT
                |--------------------------------------------------------------------------
                */

                info.appendChild(
                    primaryButton
                );

                info.appendChild(
                    deleteButton
                );

                previewCard.appendChild(
                    imageWrapper
                );

                previewCard.appendChild(
                    info
                );

                imagePreviewContainer.appendChild(
                    previewCard
                );
            }

            /*
            |--------------------------------------------------------------------------
            | RENDER NEW IMAGE
            |--------------------------------------------------------------------------
            */

            function renderNewImage(
                item,
                index
            ) {

                const file =
                    item.file;

                const previewCard =
                    document.createElement('div');

                previewCard.className =
                    'display-image-preview';

                previewCard.style.cssText = `
                    position: relative;
                    border-radius: 8px;
                    padding: 10px;
                    overflow: hidden;
                `;

                /*
                |--------------------------------------------------------------------------
                | IMAGE WRAPPER
                |--------------------------------------------------------------------------
                */

                const imageWrapper =
                    document.createElement('div');

                imageWrapper.style.cssText = `
                    width: 100%;
                    height: 180px;
                    overflow: hidden;
                    border-radius: 6px;
                    background: #f8fafc;
                `;

                /*
                |--------------------------------------------------------------------------
                | IMAGE
                |--------------------------------------------------------------------------
                */

                const image =
                    document.createElement('img');

                image.style.cssText = `
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                `;

                const reader =
                    new FileReader();

                reader.onload =
                    function(event) {

                        image.src =
                            event.target.result;
                    };

                reader.readAsDataURL(file);

                imageWrapper.appendChild(
                    image
                );

                /*
                |--------------------------------------------------------------------------
                | INFO
                |--------------------------------------------------------------------------
                */

                const info =
                    document.createElement('div');

                info.style.cssText = `
                    margin-top: 10px;
                    display: flex;
                    gap: 10px;
                    padding: 0 !important;
                    justify-content: center;
                    align-items: center;
                `;

                /*
                |--------------------------------------------------------------------------
                | FILE NAME
                |--------------------------------------------------------------------------
                */

                const fileName =
                    document.createElement('div');

                fileName.textContent =
                    file.name;

                fileName.style.cssText = `
                    font-size: 13px;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    display: none;
                `;

                /*
                |--------------------------------------------------------------------------
                | PRIMARY BUTTON
                |--------------------------------------------------------------------------
                */

                const primaryButton =
                    document.createElement('button');

                primaryButton.type = 'button';

                primaryButton.textContent =
                    item.isPrimary ?
                    'Gambar Utama' :
                    'Jadikan Utama';

                primaryButton.className =
                    'btn btn--ghost';

                primaryButton.style.cssText = `
                    width: 100%;
                    margin-top: 8px;
                    display: flex;
                    justify-content: center;
                `;

                if (item.isPrimary) {
                    primaryButton.disabled = true;
                }

                primaryButton.addEventListener(
                    'click',
                    function() {

                        setPrimaryImage(
                            'new',
                            index
                        );
                    }
                );

                /*
                |--------------------------------------------------------------------------
                | DELETE BUTTON
                |--------------------------------------------------------------------------
                */

                const deleteButton =
                    document.createElement('button');

                deleteButton.type = 'button';

                deleteButton.innerHTML =
                    '<i class="bi bi-trash3-fill"></i>';

                deleteButton.className =
                    'btn btn--ghost';

                deleteButton.style.cssText = `
                    width: 40px;
                    margin-top: 8px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                `;

                deleteButton.addEventListener(
                    'click',
                    function() {

                        const wasPrimary =
                            item.isPrimary;

                        selectedImages.splice(
                            index,
                            1
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | JIKA GAMBAR UTAMA DIHAPUS
                        |--------------------------------------------------------------------------
                        */

                        if (
                            wasPrimary &&
                            getActiveImageCount() > 0
                        ) {
                            setFirstActiveImageAsPrimary();
                            return;
                        }

                        renderImagePreview();
                    }
                );

                /*
                |--------------------------------------------------------------------------
                | APPEND ELEMENT
                |--------------------------------------------------------------------------
                */

                info.appendChild(
                    fileName
                );

                info.appendChild(
                    primaryButton
                );

                info.appendChild(
                    deleteButton
                );

                previewCard.appendChild(
                    imageWrapper
                );

                previewCard.appendChild(
                    info
                );

                imagePreviewContainer.appendChild(
                    previewCard
                );
            }

            /*
            |--------------------------------------------------------------------------
            | SET FIRST ACTIVE IMAGE AS PRIMARY
            |--------------------------------------------------------------------------
            */

            function setFirstActiveImageAsPrimary() {

                existingImages.forEach(
                    function(item) {
                        item.isPrimary = false;
                    }
                );

                selectedImages.forEach(
                    function(item) {
                        item.isPrimary = false;
                    }
                );

                const firstExisting =
                    existingImages.find(
                        function(item) {
                            return !item.isDeleted;
                        }
                    );

                if (firstExisting) {

                    firstExisting.isPrimary =
                        true;

                } else if (
                    selectedImages.length
                ) {

                    selectedImages[0].isPrimary =
                        true;
                }

                renderImagePreview();
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDATE FORM
            |--------------------------------------------------------------------------
            */

            function validateDisplayForm() {

                /*
                |--------------------------------------------------------------------------
                | DISPLAY NAME
                |--------------------------------------------------------------------------
                */

                if (
                    !displayNameInput ||
                    !displayNameInput.value.trim()
                ) {

                    showToast(
                        'error',
                        'Nama Display wajib diisi.'
                    );

                    if (displayNameInput) {
                        displayNameInput.focus();
                    }

                    return false;
                }

                if (
                    !categoryInput ||
                    !categoryInput.value
                ) {
                    showToast(
                        'error',
                        'Kategori Display wajib dipilih.'
                    );

                    if (categoryInput) {
                        categoryInput.select2('open');
                    }

                    return false;
                }

                /*
                |--------------------------------------------------------------------------
                | LENGTH
                |--------------------------------------------------------------------------
                */

                const length =
                    Number(lengthInput.value);

                if (
                    !lengthInput.value ||
                    !Number.isFinite(length) ||
                    length <= 0
                ) {

                    showToast(
                        'error',
                        'Panjang Display harus lebih dari 0.'
                    );

                    lengthInput.focus();

                    return false;
                }

                /*
                |--------------------------------------------------------------------------
                | WIDTH
                |--------------------------------------------------------------------------
                */

                const width =
                    Number(widthInput.value);

                if (
                    !widthInput.value ||
                    !Number.isFinite(width) ||
                    width <= 0
                ) {

                    showToast(
                        'error',
                        'Lebar Display harus lebih dari 0.'
                    );

                    widthInput.focus();

                    return false;
                }

                /*
                |--------------------------------------------------------------------------
                | STATUS
                |--------------------------------------------------------------------------
                */

                if (
                    !statusInput ||
                    !statusInput.value
                ) {

                    showToast(
                        'error',
                        'Status Display wajib dipilih.'
                    );

                    return false;
                }

                /*
                |--------------------------------------------------------------------------
                | IMAGE
                |--------------------------------------------------------------------------
                */

                const activeImages =
                    getActiveImages();

                if (!activeImages.length) {

                    showToast(
                        'error',
                        'Minimal satu gambar Display harus tersedia.'
                    );

                    return false;
                }

                /*
                |--------------------------------------------------------------------------
                | PRIMARY IMAGE
                |--------------------------------------------------------------------------
                */

                const primaryImages =
                    activeImages.filter(
                        function(item) {
                            return item.isPrimary;
                        }
                    );

                if (
                    primaryImages.length !== 1
                ) {

                    showToast(
                        'error',
                        'Harus ada satu gambar utama Display.'
                    );

                    return false;
                }

                /*
                |--------------------------------------------------------------------------
                | VALID
                |--------------------------------------------------------------------------
                */

                return true;
            }

            /*
            |--------------------------------------------------------------------------
            | INITIAL RENDER
            |--------------------------------------------------------------------------
            */

            renderImagePreview();

            /*
            |--------------------------------------------------------------------------
            | SUBMIT DISPLAY PRODUCT
            |--------------------------------------------------------------------------
            */

            displayProductForm.addEventListener(
                'submit',
                async function(event) {

                    event.preventDefault();

                    const form = this;

                    /*
                    |--------------------------------------------------------------------------
                    | HTML VALIDATION
                    |--------------------------------------------------------------------------
                    */

                    if (!form.checkValidity()) {

                        form.reportValidity();

                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | CUSTOM VALIDATION
                    |--------------------------------------------------------------------------
                    */

                    if (!validateDisplayForm()) {
                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | SUBMIT BUTTON
                    |--------------------------------------------------------------------------
                    */

                    const submitButton =
                        form.querySelector(
                            'button[type="submit"]'
                        );

                    const originalText =
                        submitButton.innerHTML;

                    submitButton.disabled = true;

                    submitButton.innerHTML =
                        'Menyimpan...';

                    try {

                        const formData =
                            new FormData(form);

                        /*
                        |--------------------------------------------------------------------------
                        | HAPUS INPUT FILE DEFAULT
                        |--------------------------------------------------------------------------
                        */

                        formData.delete(
                            'images[]'
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | EXISTING IMAGE YANG DIHAPUS
                        |--------------------------------------------------------------------------
                        */

                        existingImages
                            .filter(
                                function(item) {
                                    return item.isDeleted;
                                }
                            )
                            .forEach(
                                function(item) {

                                    formData.append(
                                        'deleted_image_ids[]',
                                        item.id
                                    );
                                }
                            );

                        /*
                        |--------------------------------------------------------------------------
                        | EXISTING IMAGE YANG DIPERTAHANKAN
                        |--------------------------------------------------------------------------
                        */

                        existingImages
                            .filter(
                                function(item) {
                                    return !item.isDeleted;
                                }
                            )
                            .forEach(
                                function(item) {

                                    formData.append(
                                        'existing_image_ids[]',
                                        item.id
                                    );
                                }
                            );

                        /*
                        |--------------------------------------------------------------------------
                        | PRIMARY EXISTING IMAGE
                        |--------------------------------------------------------------------------
                        */

                        const primaryExisting =
                            existingImages.find(
                                function(item) {

                                    return (
                                        !item.isDeleted &&
                                        item.isPrimary
                                    );
                                }
                            );

                        if (primaryExisting) {

                            formData.append(
                                'primary_existing_image_id',
                                primaryExisting.id
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | PRIMARY NEW IMAGE
                        |--------------------------------------------------------------------------
                        */

                        const primaryNewIndex =
                            selectedImages.findIndex(
                                function(item) {
                                    return item.isPrimary;
                                }
                            );

                        if (
                            primaryNewIndex !== -1
                        ) {

                            formData.append(
                                'primary_new_image_index',
                                primaryNewIndex
                            );
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | APPEND NEW IMAGE
                        |--------------------------------------------------------------------------
                        */

                        selectedImages.forEach(
                            function(item, index) {

                                formData.append(
                                    'images[]',
                                    item.file
                                );

                                formData.append(
                                    `image_is_primary[${index}]`,
                                    item.isPrimary ? 1 : 0
                                );
                            }
                        );

                        /*
                        |--------------------------------------------------------------------------
                        | REQUEST
                        |--------------------------------------------------------------------------
                        */

                        const response =
                            await fetch(
                                form.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',

                                        'Accept': 'application/json'
                                    }
                                }
                            );

                        const data =
                            await response.json();

                        /*
                        |--------------------------------------------------------------------------
                        | BERHASIL
                        |--------------------------------------------------------------------------
                        */

                        if (
                            response.ok &&
                            data.success
                        ) {

                            showToast(
                                'success',
                                data.message
                            );

                            setTimeout(
                                function() {

                                    window.location.href =
                                        "{{ route('admin_display') }}";

                                },
                                800
                            );

                            return;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | VALIDATION ERROR
                        |--------------------------------------------------------------------------
                        */

                        if (
                            response.status === 422 &&
                            data.errors
                        ) {

                            const firstError =
                                Object.values(
                                    data.errors
                                )[0];

                            const message =
                                Array.isArray(firstError) ?
                                firstError[0] :
                                firstError;

                            showToast(
                                'error',
                                message ||
                                'Data Display tidak valid.'
                            );

                            return;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | ERROR
                        |--------------------------------------------------------------------------
                        */

                        showToast(
                            'error',
                            data.message ||
                            'Terjadi kesalahan saat memperbarui Display.'
                        );

                    } catch (error) {

                        console.error(
                            'Gagal memperbarui Display:',
                            error
                        );

                        showToast(
                            'error',
                            'Terjadi kesalahan saat memperbarui Display.'
                        );

                    } finally {

                        submitButton.disabled = false;

                        submitButton.innerHTML =
                            originalText;
                    }
                }
            );
        });
    </script>
@endsection

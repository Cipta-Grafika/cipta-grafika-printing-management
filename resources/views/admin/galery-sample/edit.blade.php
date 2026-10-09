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
                            MASTER · GALERI SAMPEL
                        </span>

                        <p class="hero-sub">
                            Edit gambar contoh hasil cetak untuk mesin dan kategori produk yang dipilih.
                        </p>

                    </div>

                </section>


                <div class="grid">

                    <section class="col-12 card">

                        <div class="card-head">

                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Informasi Galeri Sampel
                                </span>

                            </div>

                        </div>


                        <form method="POST"
                            action="{{ route('admin_gallery_samples_update', [$engine->id, $category->id]) }}"
                            id="sampleGalleryEditForm" enctype="multipart/form-data">

                            @csrf

                            @method('PUT')


                            {{-- INFORMASI MESIN & KATEGORI --}}

                            <div class="form-grid">

                                <div class="field">

                                    <label class="field-label">
                                        Mesin
                                    </label>

                                    <select class="select select2" name="engine_id" id="engine_id">
                                        @foreach ($engines as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $item->id == $engine->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>


                                <div class="field">

                                    <label class="field-label">
                                        Kategori
                                    </label>

                                    <select class="select select2" name="category_id" id="category_id">
                                        @foreach ($categories as $item)
                                            <option value="{{ $item->id }}"
                                                {{ $item->id == $category->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                </div>

                            </div>


                            {{-- HIDDEN VALUE UNTUK DATA ENGINE & CATEGORY --}}

                            {{-- <input type="hidden" name="engine_id" value="{{ $engine->id }}">

                            <input type="hidden" name="category_id" value="{{ $category->id }}"> --}}


                            {{-- GAMBAR --}}

                            <div class="field" style="margin-top: 24px; overflow: hidden;">

                                <div
                                    style="
                                        display:flex;
                                        justify-content:space-between;
                                        align-items:center;
                                        margin-bottom:12px;
                                    ">

                                    <label class="field-label" style="margin-bottom:0;">
                                        Gambar Sampel
                                        <span class="req">*</span>
                                    </label>


                                    <button type="button" id="btnAddImage" class="btn btn--ghost">
                                        + Tambah Gambar
                                    </button>

                                </div>


                                <input type="file" id="galleryImages" name="images[]" accept="image/*" multiple hidden>


                                <div id="imagePreviewContainer"
                                    style="
                                        display:grid;
                                        grid-template-columns:
                                            repeat(
                                                auto-fill,
                                                minmax(180px, 1fr)
                                            );
                                        gap:16px;
                                    ">
                                </div>

                                {{-- EXISTING IMAGES --}}
                                @foreach ($galleryImages as $image)
                                    <div class="existing-image" data-image-id="{{ $image->id }}"
                                        data-image-primary="{{ $image->is_primary ? 1 : 0 }}" style="display:none;">
                                        <img src="{{ route('admin_gallery_sample_image', $image->id) }}"
                                            alt="{{ $image->image_name }}">
                                    </div>
                                @endforeach

                                <div id="imageEmptyState"
                                    style="
                                        display:none;
                                        padding:24px;
                                        text-align:center;
                                        border:1px dashed var(--border)!important;
                                        border-radius:8px;
                                        color:#64748b;
                                    ">
                                    Belum ada gambar yang dipilih.
                                </div>

                            </div>


                            {{-- ACTION --}}

                            <div class="form-actions" style="margin-top:24px;">

                                <span class="badge dot success">
                                    Siap disimpan
                                </span>

                                <span class="spacer"></span>


                                <a href="{{ route('admin_gallery_samples') }}" class="btn btn--ghost">
                                    Batal
                                </a>


                                <button type="submit" class="btn btn--primary" id="btnSubmit">
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
        $(document).ready(function() {

            $('.select2').select2({
                allowClear: false,
                width: '100%'
            });

        });


        document.addEventListener(
            'DOMContentLoaded',
            function() {

                /*
                |--------------------------------------------------------------------------
                | ELEMENT
                |--------------------------------------------------------------------------
                */

                const sampleGalleryEditForm =
                    document.getElementById(
                        'sampleGalleryEditForm'
                    );

                const btnAddImage =
                    document.getElementById(
                        'btnAddImage'
                    );

                const galleryImages =
                    document.getElementById(
                        'galleryImages'
                    );

                const imagePreviewContainer =
                    document.getElementById(
                        'imagePreviewContainer'
                    );

                const imageEmptyState =
                    document.getElementById(
                        'imageEmptyState'
                    );


                /*
                |--------------------------------------------------------------------------
                | CONFIG
                |--------------------------------------------------------------------------
                */

                const MAX_IMAGE_SIZE =
                    2 * 1024 * 1024;

                const MAX_IMAGE_COUNT =
                    10;

                const ALLOWED_IMAGE_TYPES = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];


                /*
                |--------------------------------------------------------------------------
                | EXISTING IMAGES
                |--------------------------------------------------------------------------
                */

                let existingImages = [];


                document
                    .querySelectorAll('.existing-image')
                    .forEach(function(element) {

                        const image =
                            element.querySelector('img');


                        if (!image) {
                            return;
                        }


                        existingImages.push({
                            id: element.dataset.imageId,

                            imageUrl: image.src,

                            imageName: image.alt,

                            isPrimary: element.dataset.imagePrimary === '1',

                            isDeleted: false
                        });


                        element.remove();

                    });


                /*
                |--------------------------------------------------------------------------
                | NEW IMAGES
                |--------------------------------------------------------------------------
                */

                let selectedImages = [];


                /*
                |--------------------------------------------------------------------------
                | ACTIVE IMAGE COUNT
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


                    return (
                        existingCount +
                        newCount
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | ACTIVE IMAGES
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

                        firstExisting.isPrimary = true;

                    } else if (
                        selectedImages.length > 0
                    ) {

                        selectedImages[0].isPrimary = true;

                    }


                    renderImagePreview();

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

                                    return (
                                        item.id ===
                                        target
                                    );

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

                    imagePreviewContainer.innerHTML =
                        '';


                    const activeImages =
                        getActiveImages();


                    /*
                    |--------------------------------------------------------------------------
                    | EMPTY STATE
                    |--------------------------------------------------------------------------
                    */

                    if (
                        activeImages.length === 0
                    ) {

                        imageEmptyState.style.display =
                            'block';

                        return;

                    }


                    imageEmptyState.style.display =
                        'none';


                    /*
                    |--------------------------------------------------------------------------
                    | EXISTING IMAGES
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

                                const card =
                                    document.createElement(
                                        'div'
                                    );

                                card.className =
                                    'gallery-image-preview';


                                /*
                                |--------------------------------------------------------------------------
                                | IMAGE
                                |--------------------------------------------------------------------------
                                */

                                const imageWrapper =
                                    document.createElement(
                                        'div'
                                    );

                                imageWrapper.className =
                                    'gallery-image-preview__image';


                                const image =
                                    document.createElement(
                                        'img'
                                    );

                                image.src =
                                    item.imageUrl;

                                image.alt =
                                    item.imageName;


                                imageWrapper.appendChild(
                                    image
                                );


                                /*
                                |--------------------------------------------------------------------------
                                | BODY
                                |--------------------------------------------------------------------------
                                */

                                const body =
                                    document.createElement(
                                        'div'
                                    );

                                body.className =
                                    'gallery-image-preview__body';


                                /*
                                |--------------------------------------------------------------------------
                                | IMAGE NAME
                                |--------------------------------------------------------------------------
                                */

                                const name =
                                    document.createElement(
                                        'div'
                                    );

                                name.className =
                                    'gallery-image-preview__name';

                                name.textContent =
                                    item.imageName;


                                /*
                                |--------------------------------------------------------------------------
                                | ACTIONS
                                |--------------------------------------------------------------------------
                                */

                                const actions =
                                    document.createElement(
                                        'div'
                                    );

                                actions.className =
                                    'gallery-image-preview__actions';


                                /*
                                |--------------------------------------------------------------------------
                                | PRIMARY BUTTON
                                |--------------------------------------------------------------------------
                                */

                                const primaryButton =
                                    document.createElement(
                                        'button'
                                    );

                                primaryButton.type =
                                    'button';

                                primaryButton.className =
                                    'btn btn--ghost';


                                if (item.isPrimary) {

                                    primaryButton.textContent =
                                        'Gambar Utama';

                                    primaryButton.disabled =
                                        true;

                                } else {

                                    primaryButton.textContent =
                                        'Jadikan Utama';


                                    primaryButton.addEventListener(
                                        'click',
                                        function() {

                                            setPrimaryImage(
                                                'existing',
                                                item.id
                                            );

                                        }
                                    );

                                }


                                /*
                                |--------------------------------------------------------------------------
                                | DELETE BUTTON
                                |--------------------------------------------------------------------------
                                */

                                const deleteButton =
                                    document.createElement(
                                        'button'
                                    );

                                deleteButton.type =
                                    'button';

                                deleteButton.className =
                                    'btn btn--ghost';

                                deleteButton.innerHTML =
                                    '<i class="bi bi-trash3-fill"></i>';

                                deleteButton.title =
                                    'Hapus';


                                deleteButton.addEventListener(
                                    'click',
                                    function() {

                                        const wasPrimary =
                                            item.isPrimary;


                                        item.isDeleted =
                                            true;

                                        item.isPrimary =
                                            false;


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
                                | APPEND ACTIONS
                                |--------------------------------------------------------------------------
                                */

                                actions.appendChild(
                                    primaryButton
                                );

                                actions.appendChild(
                                    deleteButton
                                );


                                /*
                                |--------------------------------------------------------------------------
                                | APPEND BODY
                                |--------------------------------------------------------------------------
                                */

                                body.appendChild(
                                    name
                                );

                                body.appendChild(
                                    actions
                                );


                                /*
                                |--------------------------------------------------------------------------
                                | APPEND CARD
                                |--------------------------------------------------------------------------
                                */

                                card.appendChild(
                                    imageWrapper
                                );

                                card.appendChild(
                                    body
                                );


                                imagePreviewContainer.appendChild(
                                    card
                                );

                            }
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | NEW IMAGES
                    |--------------------------------------------------------------------------
                    */

                    selectedImages.forEach(
                        function(item, index) {

                            const card =
                                document.createElement(
                                    'div'
                                );

                            card.className =
                                'gallery-image-preview';


                            /*
                            |--------------------------------------------------------------------------
                            | IMAGE
                            |--------------------------------------------------------------------------
                            */

                            const imageWrapper =
                                document.createElement(
                                    'div'
                                );

                            imageWrapper.className =
                                'gallery-image-preview__image';


                            const image =
                                document.createElement(
                                    'img'
                                );

                            image.alt =
                                item.file.name;


                            imageWrapper.appendChild(
                                image
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | FILE READER
                            |--------------------------------------------------------------------------
                            */

                            const reader =
                                new FileReader();


                            reader.onload =
                                function(event) {

                                    image.src =
                                        event.target.result;

                                };


                            reader.readAsDataURL(
                                item.file
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | BODY
                            |--------------------------------------------------------------------------
                            */

                            const body =
                                document.createElement(
                                    'div'
                                );

                            body.className =
                                'gallery-image-preview__body';


                            /*
                            |--------------------------------------------------------------------------
                            | IMAGE NAME
                            |--------------------------------------------------------------------------
                            */

                            const name =
                                document.createElement(
                                    'div'
                                );

                            name.className =
                                'gallery-image-preview__name';

                            name.textContent =
                                item.file.name;


                            /*
                            |--------------------------------------------------------------------------
                            | ACTIONS
                            |--------------------------------------------------------------------------
                            */

                            const actions =
                                document.createElement(
                                    'div'
                                );

                            actions.className =
                                'gallery-image-preview__actions';


                            /*
                            |--------------------------------------------------------------------------
                            | PRIMARY BUTTON
                            |--------------------------------------------------------------------------
                            */

                            const primaryButton =
                                document.createElement(
                                    'button'
                                );

                            primaryButton.type =
                                'button';

                            primaryButton.className =
                                'btn btn--ghost';


                            if (item.isPrimary) {

                                primaryButton.textContent =
                                    'Gambar Utama';

                                primaryButton.disabled =
                                    true;

                            } else {

                                primaryButton.textContent =
                                    'Jadikan Utama';


                                primaryButton.addEventListener(
                                    'click',
                                    function() {

                                        setPrimaryImage(
                                            'new',
                                            index
                                        );

                                    }
                                );

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | DELETE BUTTON
                            |--------------------------------------------------------------------------
                            */

                            const deleteButton =
                                document.createElement(
                                    'button'
                                );

                            deleteButton.type =
                                'button';

                            deleteButton.className =
                                'btn btn--ghost';

                            deleteButton.innerHTML =
                                '<i class="bi bi-trash3-fill"></i>';

                            deleteButton.title =
                                'Hapus';


                            deleteButton.addEventListener(
                                'click',
                                function() {

                                    const wasPrimary =
                                        item.isPrimary;


                                    selectedImages.splice(
                                        index,
                                        1
                                    );


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
                            | APPEND ACTIONS
                            |--------------------------------------------------------------------------
                            */

                            actions.appendChild(
                                primaryButton
                            );

                            actions.appendChild(
                                deleteButton
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | APPEND BODY
                            |--------------------------------------------------------------------------
                            */

                            body.appendChild(
                                name
                            );

                            body.appendChild(
                                actions
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | APPEND CARD
                            |--------------------------------------------------------------------------
                            */

                            card.appendChild(
                                imageWrapper
                            );

                            card.appendChild(
                                body
                            );


                            imagePreviewContainer.appendChild(
                                card
                            );

                        }
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | ADD IMAGE BUTTON
                |--------------------------------------------------------------------------
                */

                btnAddImage.addEventListener(
                    'click',
                    function() {

                        if (
                            getActiveImageCount() >=
                            MAX_IMAGE_COUNT
                        ) {

                            showToast(
                                'error',
                                'Maksimal 10 gambar.'
                            );

                            return;

                        }


                        galleryImages.click();

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | IMAGE INPUT CHANGE
                |--------------------------------------------------------------------------
                */

                galleryImages.addEventListener(
                    'change',
                    function(event) {

                        const files =
                            Array.from(
                                event.target.files
                            );


                        if (!files.length) {

                            return;

                        }


                        const remainingSlots =
                            MAX_IMAGE_COUNT -
                            getActiveImageCount();


                        if (
                            files.length >
                            remainingSlots
                        ) {

                            showToast(
                                'error',
                                'Jumlah gambar maksimal 10.'
                            );

                            galleryImages.value =
                                '';

                            return;

                        }


                        files.forEach(
                            function(file) {

                                /*
                                |--------------------------------------------------------------------------
                                | FORMAT
                                |--------------------------------------------------------------------------
                                */

                                if (
                                    !ALLOWED_IMAGE_TYPES.includes(
                                        file.type
                                    )
                                ) {

                                    showToast(
                                        'error',
                                        'Format gambar harus JPG, JPEG, PNG, atau WEBP.'
                                    );

                                    return;

                                }


                                /*
                                |--------------------------------------------------------------------------
                                | SIZE
                                |--------------------------------------------------------------------------
                                */

                                if (
                                    file.size >
                                    MAX_IMAGE_SIZE
                                ) {

                                    showToast(
                                        'error',
                                        'Ukuran setiap gambar maksimal 2 MB.'
                                    );

                                    return;

                                }


                                /*
                                |--------------------------------------------------------------------------
                                | DUPLICATE
                                |--------------------------------------------------------------------------
                                */

                                const duplicate =
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


                                if (duplicate) {

                                    showToast(
                                        'error',
                                        'Gambar yang sama sudah dipilih.'
                                    );

                                    return;

                                }


                                /*
                                |--------------------------------------------------------------------------
                                | PUSH NEW IMAGE
                                |--------------------------------------------------------------------------
                                */

                                selectedImages.push({

                                    file: file,

                                    isPrimary: getActiveImageCount() === 0

                                });

                            }
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | ENSURE PRIMARY
                        |--------------------------------------------------------------------------
                        */

                        const activeImages =
                            getActiveImages();


                        const hasPrimary =
                            activeImages.some(
                                function(item) {

                                    return item.isPrimary;

                                }
                            );


                        if (
                            !hasPrimary &&
                            activeImages.length > 0
                        ) {

                            setFirstActiveImageAsPrimary();

                        } else {

                            renderImagePreview();

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | RESET FILE INPUT
                        |--------------------------------------------------------------------------
                        */

                        galleryImages.value =
                            '';

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | FORM VALIDATION
                |--------------------------------------------------------------------------
                */

                function validateForm() {

                    const activeImages =
                        getActiveImages();


                    /*
                    |--------------------------------------------------------------------------
                    | MINIMUM IMAGE
                    |--------------------------------------------------------------------------
                    */

                    if (
                        activeImages.length === 0
                    ) {

                        showToast(
                            'error',
                            'Minimal harus ada satu gambar.'
                        );

                        return false;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PRIMARY COUNT
                    |--------------------------------------------------------------------------
                    */

                    const primaryCount =
                        activeImages.filter(
                            function(item) {

                                return item.isPrimary;

                            }
                        ).length;


                    if (
                        primaryCount !== 1
                    ) {

                        showToast(
                            'error',
                            'Harus ada tepat satu gambar utama.'
                        );

                        return false;

                    }


                    return true;

                }

                /*
                |--------------------------------------------------------------------------
                | FORM SUBMIT
                |--------------------------------------------------------------------------
                */

                sampleGalleryEditForm.addEventListener(
                    'submit',
                    function(event) {

                        event.preventDefault();


                        /*
                        |--------------------------------------------------------------------------
                        | VALIDATION
                        |--------------------------------------------------------------------------
                        */

                        if (!validateForm()) {
                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | FORM DATA
                        |--------------------------------------------------------------------------
                        */

                        const formData =
                            new FormData(
                                sampleGalleryEditForm
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | EXISTING IMAGES
                        |--------------------------------------------------------------------------
                        */

                        existingImages.forEach(
                            function(item) {

                                if (!item.isDeleted) {

                                    formData.append(
                                        'existing_image_ids[]',
                                        item.id
                                    );

                                }

                            }
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | DELETED EXISTING IMAGES
                        |--------------------------------------------------------------------------
                        */

                        existingImages.forEach(
                            function(item) {

                                if (item.isDeleted) {

                                    formData.append(
                                        'deleted_image_ids[]',
                                        item.id
                                    );

                                }

                            }
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | NEW IMAGES
                        |--------------------------------------------------------------------------
                        */

                        selectedImages.forEach(
                            function(item, index) {

                                formData.append(
                                    'images[' + index + ']',
                                    item.file
                                );

                            }
                        );

                        const primaryImage =
                            getActiveImages().find(
                                function(item) {
                                    return item.isPrimary;
                                }
                            );

                        const primaryNewImageIndex =
                            selectedImages.indexOf(primaryImage);

                        if (primaryNewImageIndex !== -1) {

                            formData.append(
                                'primary_image',
                                'new:' + primaryNewImageIndex
                            );

                        } else if (primaryImage) {

                            formData.append(
                                'primary_image',
                                'existing:' + primaryImage.id
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | SUBMIT
                        |--------------------------------------------------------------------------
                        */

                        fetch(
                                sampleGalleryEditForm.action, {
                                    method: 'POST',

                                    headers: {
                                        'X-CSRF-TOKEN': document
                                            .querySelector(
                                                'meta[name="csrf-token"]'
                                            )
                                            .getAttribute('content'),

                                        'X-Requested-With': 'XMLHttpRequest',

                                        'Accept': 'application/json'
                                    },

                                    body: formData
                                }
                            )
                            .then(
                                async function(response) {

                                    const result =
                                        await response.json();


                                    if (!response.ok) {

                                        if (
                                            response.status === 422
                                        ) {

                                            let message =
                                                result.message ||
                                                'Data tidak valid.';


                                            if (result.errors) {

                                                const firstError =
                                                    Object.values(
                                                        result.errors
                                                    )[0];


                                                if (
                                                    Array.isArray(
                                                        firstError
                                                    ) &&
                                                    firstError.length
                                                ) {

                                                    message =
                                                        firstError[0];

                                                }

                                            }


                                            throw new Error(
                                                message
                                            );

                                        }


                                        throw new Error(
                                            result.message ||
                                            'Terjadi kesalahan saat memperbarui galeri sampel.'
                                        );

                                    }


                                    return result;

                                }
                            )
                            .then(
                                function(result) {

                                    if (!result.success) {

                                        showToast(
                                            'error',
                                            result.message ||
                                            'Gagal memperbarui galeri sampel.'
                                        );

                                        return;

                                    }


                                    showToast(
                                        'success',
                                        result.message ||
                                        'Galeri sampel berhasil diperbarui.'
                                    );


                                    setTimeout(
                                        function() {

                                            window.location.href =
                                                "{{ route('admin_gallery_samples') }}";

                                        },
                                        800
                                    );

                                }
                            )
                            .catch(
                                function(error) {

                                    showToast(
                                        'error',
                                        error.message ||
                                        'Terjadi kesalahan saat memperbarui galeri sampel.'
                                    );

                                }
                            );

                    }
                );

                /*
                |--------------------------------------------------------------------------
                | INITIAL RENDER
                |--------------------------------------------------------------------------
                */

                renderImagePreview();

            }
        );
    </script>
@endsection

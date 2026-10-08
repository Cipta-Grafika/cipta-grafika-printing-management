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
                            Tambahkan gambar contoh hasil cetak untuk kategori produk yang dipilih.
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

                        <form method="POST" action="{{ route('admin_store_sample_gallery') }}" id="sampleGalleryForm"
                            enctype="multipart/form-data">

                            @csrf

                            {{-- =========================================================
                            INFORMASI GALERI SAMPEL
                        ========================================================== --}}

                            <div class="form-grid">

                                {{-- MESIN --}}
                                <div class="field">
                                    <label class="field-label"> Mesin <span class="req">*</span></label>
                                    <select name="engine_id" class="select select2" required>
                                        <option value="">Pilih Mesin</option>
                                        @foreach ($engines as $engine)
                                            <option value="{{ $engine->id }}"> {{ $engine->name }} </option>
                                        @endforeach
                                    </select>

                                </div>

                                {{-- KATEGORI --}}
                                <div class="field">
                                    <label class="field-label"> Kategori <span class="req">*</span></label>
                                    <select name="category_id" class="select select2" required>
                                        <option value="">Pilih Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}"> {{ $category->name }} </option>
                                        @endforeach
                                    </select>

                                </div>

                            </div>


                            {{-- =========================================================
                            GAMBAR SAMPEL
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
                                        Gambar Sampel
                                        <span class="req">*</span>
                                    </label>

                                    <button type="button" id="btnAddImage" class="btn btn--ghost">
                                        + Tambah Gambar
                                    </button>

                                </div>


                                <input type="file" id="galleryImages" name="images[]" accept="image/*" multiple hidden>


                                {{-- IMAGE PREVIEW --}}

                                <div id="imagePreviewContainer"
                                    style="
                                    display: grid;
                                    grid-template-columns:
                                        repeat(auto-fill, minmax(180px, 1fr));
                                    gap: 16px;
                                ">
                                </div>


                                <div id="imageEmptyState"
                                    style="
                                    padding: 24px;
                                    text-align: center;
                                    border: 1px dashed var(--border) !important;
                                    border-radius: 8px;
                                    color: #64748b;
                                ">
                                    Belum ada gambar yang dipilih.
                                </div>

                            </div>


                            {{-- =========================================================
                            ACTION
                        ========================================================== --}}

                            <div class="form-actions" style="margin-top: 24px;">

                                <span class="badge dot success">
                                    Siap disimpan
                                </span>

                                <span class="spacer"></span>

                                <a href="{{ route('admin_gallery_samples') }}" class="btn btn--ghost">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn--primary">
                                    Simpan
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

        $('select[name="engine_id"]').select2({
            placeholder: 'Pilih Mesin',
            allowClear: false,
            width: '100%'
        });

        document.addEventListener('DOMContentLoaded', function() {

            const sampleGalleryForm = document.getElementById('sampleGalleryForm');
            const btnAddImage = document.getElementById('btnAddImage');
            const galleryImages = document.getElementById('galleryImages');
            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
            const imageEmptyState = document.getElementById('imageEmptyState');
            const engineInput = sampleGalleryForm.querySelector('[name="engine_id"]');
            const categoryInput = sampleGalleryForm.querySelector('[name="category_id"]');


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
            | IMAGE DATA
            |--------------------------------------------------------------------------
            */

            let selectedImages = [];


            /*
            |--------------------------------------------------------------------------
            | ADD IMAGE BUTTON
            |--------------------------------------------------------------------------
            */

            btnAddImage.addEventListener('click', function() {

                if (selectedImages.length >= MAX_IMAGE_COUNT) {
                    showToast(
                        'error',
                        `Maksimal ${MAX_IMAGE_COUNT} gambar yang dapat ditambahkan.`
                    );

                    return;
                }

                galleryImages.click();
            });


            /*
            |--------------------------------------------------------------------------
            | HANDLE IMAGE SELECT
            |--------------------------------------------------------------------------
            */

            galleryImages.addEventListener('change', function(event) {

                const files = Array.from(event.target.files);

                if (!files.length) {
                    return;
                }

                if (selectedImages.length + files.length > MAX_IMAGE_COUNT) {
                    showToast(
                        'error',
                        `Maksimal ${MAX_IMAGE_COUNT} gambar yang dapat ditambahkan.`
                    );

                    galleryImages.value = '';

                    return;
                }

                files.forEach(function(file) {

                    /* VALIDASI TIPE FILE */
                    if (!ALLOWED_IMAGE_TYPES.includes(file.type)) {

                        showToast(
                            'error',
                            `File "${file.name}" bukan format gambar yang diperbolehkan.`
                        );

                        return;
                    }

                    /* VALIDASI UKURAN FILE */
                    if (file.size > MAX_IMAGE_SIZE) {

                        showToast(
                            'error',
                            `Ukuran "${file.name}" melebihi 2 MB.`
                        );

                        return;
                    }

                    /* CEK DUPLIKAT */
                    const isDuplicate = selectedImages.some(function(item) {

                        return (
                            item.file.name === file.name &&
                            item.file.size === file.size &&
                            item.file.lastModified === file.lastModified
                        );
                    });

                    if (isDuplicate) {

                        showToast(
                            'error',
                            `Gambar "${file.name}" sudah ditambahkan.`
                        );

                        return;
                    }

                    /* TAMBAHKAN */
                    selectedImages.push({
                        file: file,
                        isPrimary: selectedImages.length === 0
                    });
                });

                renderImagePreview();

                galleryImages.value = '';
            });


            /*
            |--------------------------------------------------------------------------
            | RENDER IMAGE PREVIEW
            |--------------------------------------------------------------------------
            */

            function renderImagePreview() {

                imagePreviewContainer.innerHTML = '';

                /* EMPTY STATE */
                if (!selectedImages.length) {

                    imageEmptyState.style.display = '';

                    return;
                }

                imageEmptyState.style.display = 'none';

                selectedImages.forEach(function(item, index) {

                    const file = item.file;

                    const previewCard = document.createElement('div');

                    previewCard.className = 'display-image-preview';

                    previewCard.style.cssText = `
                    position: relative;
                    border-radius: 8px;
                    padding: 10px;
                    overflow: hidden;
                `;


                    /* IMAGE WRAPPER */
                    const imageWrapper = document.createElement('div');

                    imageWrapper.style.cssText = `
                    width: 100%;
                    height: 180px;
                    overflow: hidden;
                    border-radius: 6px;
                    background: #f8fafc;
                `;


                    /* IMAGE */
                    const image = document.createElement('img');

                    image.style.cssText = `
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: block;
                `;

                    const reader = new FileReader();

                    reader.onload = function(event) {
                        image.src = event.target.result;
                    };

                    reader.readAsDataURL(file);

                    imageWrapper.appendChild(image);


                    /* INFO */
                    const info = document.createElement('div');

                    info.style.cssText = `
                    margin-top: 10px;
                    display: flex; gap: 10px;
                    padding: 0 !important;
                    justify-content: center;
                    align-items: center;
                `;


                    /* FILE NAME */
                    const fileName = document.createElement('div');

                    fileName.textContent = file.name;

                    fileName.style.cssText = `
                    font-size: 13px;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    display: none;
                `;


                    /* PRIMARY BUTTON */
                    const primaryButton = document.createElement('button');

                    primaryButton.type = 'button';

                    primaryButton.textContent = item.isPrimary ? 'Gambar Utama' : 'Jadikan Utama';

                    primaryButton.className = 'btn btn--ghost';

                    primaryButton.style.cssText = `
                    width: 100%;
                    margin-top: 8px;
                    display: flex;
                    justify-content:center;
                `;

                    if (item.isPrimary) {
                        primaryButton.disabled = true;
                    }

                    primaryButton.addEventListener('click', function() {

                        selectedImages.forEach(function(imageItem) {

                            imageItem.isPrimary = false;
                        });

                        item.isPrimary = true;

                        renderImagePreview();
                    });


                    /* DELETE BUTTON */
                    const deleteButton = document.createElement('button');

                    deleteButton.type = 'button';

                    deleteButton.innerHTML = '<i class="bi bi-trash3-fill"></i>';

                    deleteButton.className = 'btn btn--ghost';

                    deleteButton.style.cssText = `
                    width: 40px;
                    margin-top: 8px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                `;

                    deleteButton.addEventListener('click', function() {

                        const wasPrimary = item.isPrimary;

                        selectedImages.splice(index, 1);

                        /* JIKA GAMBAR UTAMA DIHAPUS */
                        if (wasPrimary && selectedImages.length) {

                            selectedImages[0].isPrimary = true;
                        }

                        renderImagePreview();
                    });


                    /* APPEND ELEMENT */
                    info.appendChild(fileName);

                    info.appendChild(primaryButton);

                    info.appendChild(deleteButton);

                    previewCard.appendChild(imageWrapper);

                    previewCard.appendChild(info);

                    imagePreviewContainer.appendChild(previewCard);
                });
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDATE FORM
            |--------------------------------------------------------------------------
            */

            function validateSampleGalleryForm() {

                /* MESIN */
                if (!engineInput || !engineInput.value) {

                    showToast('error', 'Mesin wajib dipilih.');

                    if (engineInput) {
                        $(engineInput).select2('open');
                    }

                    return false;
                }

                /* KATEGORI */
                if (!categoryInput || !categoryInput.value) {

                    showToast('error', 'Kategori wajib dipilih.');

                    if (categoryInput) {
                        $(categoryInput).select2('open');
                    }

                    return false;
                }

                /* IMAGE */
                if (!selectedImages.length) {

                    showToast(
                        'error',
                        'Minimal satu gambar sampel harus dipilih.'
                    );

                    return false;
                }

                return true;
            }


            /*
            |--------------------------------------------------------------------------
            | SUBMIT GALERI SAMPEL
            |--------------------------------------------------------------------------
            */

            sampleGalleryForm.addEventListener('submit', async function(event) {

                event.preventDefault();

                const form = this;

                /* HTML VALIDATION */
                if (!form.checkValidity()) {

                    form.reportValidity();

                    return;
                }

                /* CUSTOM VALIDATION */
                if (!validateSampleGalleryForm()) {
                    return;
                }

                /* SUBMIT BUTTON */
                const submitButton = form.querySelector('button[type="submit"]');

                const originalText = submitButton.innerHTML;

                submitButton.disabled = true;

                submitButton.innerHTML = 'Menyimpan...';

                try {

                    const formData = new FormData(form);

                    /* HAPUS INPUT FILE DEFAULT */
                    formData.delete('images[]');

                    /* APPEND IMAGE */
                    selectedImages.forEach(function(item, index) {

                        formData.append('images[]', item.file);

                        formData.append(
                            `image_is_primary[${index}]`,
                            item.isPrimary ? 1 : 0
                        );
                    });

                    /* REQUEST */
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    /* BERHASIL */
                    if (response.ok && data.success) {

                        showToast('success', data.message);

                        setTimeout(function() {

                            window.location.href = "{{ route('admin_gallery_samples') }}";

                        }, 800);

                        return;
                    }

                    /* VALIDATION ERROR */
                    if (response.status === 422 && data.errors) {

                        const firstError = Object.values(data.errors)[0];

                        const message = Array.isArray(firstError) ? firstError[0] : firstError;

                        showToast(
                            'error',
                            message || 'Data galeri sampel tidak valid.'
                        );

                        return;
                    }

                    /* ERROR */
                    showToast(
                        'error',
                        data.message || 'Terjadi kesalahan saat menyimpan galeri sampel.'
                    );

                } catch (error) {

                    console.error('Gagal menyimpan galeri sampel:', error);

                    showToast(
                        'error',
                        'Terjadi kesalahan saat menyimpan galeri sampel.'
                    );

                } finally {

                    submitButton.disabled = false;

                    submitButton.innerHTML = originalText;
                }
            });

        });
    </script>
@endsection

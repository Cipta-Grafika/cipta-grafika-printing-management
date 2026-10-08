@extends('admin_master')

@section('contents')
    <div class="shell">

        <div data-shell-sidebar></div>

        <div class="main">

            <div data-shell-topbar></div>

            <main class="content">

                {{-- =========================================================
                HERO
            ========================================================= --}}
                <section class="hero">

                    <div class="hero-text">

                        <span class="eyebrow">
                            MASTER · DISPLAY
                        </span>

                        <p class="hero-sub">
                            Detail produk display beserta informasi ukuran dan gambar produk.
                        </p>

                    </div>

                </section>


                <div class="grid">

                    <section class="col-12 card">

                        {{-- =================================================
                        CARD HEADER
                    ================================================= --}}
                        <div class="card-head">

                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Detail Display
                                </span>

                            </div>

                        </div>


                        {{-- =================================================
                        PRODUCT OVERVIEW
                    ================================================= --}}
                        <div class="display-detail-overview">

                            {{-- =================================================
                            INFORMASI UTAMA
                        ================================================= --}}
                            <div class="display-detail-info">

                                <span class="eyebrow">
                                    Informasi Display
                                </span>

                                <h2 class="display-detail-name" style="text-transform: uppercase;">
                                    {{ $displayProduct->display_name }}
                                </h2>

                                <div class="display-detail-category">

                                    <span class="display-detail-label">
                                        Kategori
                                    </span>

                                    <strong style="text-transform: uppercase;">{{ $displayProduct->category_name }}</strong>

                                </div>

                                <div class="display-detail-size">

                                    <span class="display-detail-label">
                                        Ukuran
                                    </span>

                                    <strong>
                                        {{ number_format($displayProduct->width, 2) }}
                                        ×
                                        {{ number_format($displayProduct->length, 2) }}
                                        cm
                                    </strong>

                                </div>


                                <div class="display-detail-status">

                                    <span class="display-detail-label">
                                        Status
                                    </span>

                                    @if ($displayProduct->status === 'Active')
                                        <span class="badge success dot">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge danger dot">
                                            Inactive
                                        </span>
                                    @endif

                                </div>

                            </div>


                            {{-- =================================================
                            IMAGE AREA
                            NANTI AKAN DIISI GAMBAR
                        ================================================= --}}
                            <div class="display-detail-gallery">

                                @php
                                    $primaryImage = $displayImages->first();
                                @endphp

                                <div class="display-gallery-main">

                                    @if ($primaryImage)
                                        <img src="{{ route('admin_display_image', $primaryImage->id) }}"
                                            alt="{{ $displayProduct->display_name }}" id="displayMainImage">
                                    @else
                                        <div class="display-gallery-placeholder">

                                            <i class="bi bi-image"></i>

                                            <span>
                                                Gambar Produk
                                            </span>

                                        </div>
                                    @endif

                                </div>


                                <div class="display-gallery-thumbnails">

                                    @foreach ($displayImages as $image)
                                        <button type="button"
                                            class="display-gallery-thumbnail {{ $image->is_primary ? 'is-active' : '' }}"
                                            data-image-url="{{ route('admin_display_image', $image->id) }}">

                                            <img src="{{ route('admin_display_image', $image->id) }}"
                                                alt="{{ $image->image_name }}">

                                        </button>
                                    @endforeach

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                        SPESIFIKASI
                    ================================================= --}}
                        <div class="display-detail-section">

                            <div class="display-detail-section-head">

                                <span class="eyebrow">
                                    Spesifikasi
                                </span>

                            </div>


                            <div class="display-spec-table">

                                <div class="display-spec-row">

                                    <div class="display-spec-label">
                                        Nama
                                    </div>

                                    <div class="display-spec-value">
                                        {{ $displayProduct->display_name }}
                                    </div>

                                </div>


                                <div class="display-spec-row">

                                    <div class="display-spec-label">
                                        Panjang
                                    </div>

                                    <div class="display-spec-value">
                                        {{ number_format($displayProduct->length, 2) }} cm
                                    </div>

                                </div>


                                <div class="display-spec-row">

                                    <div class="display-spec-label">
                                        Lebar
                                    </div>

                                    <div class="display-spec-value">
                                        {{ number_format($displayProduct->width, 2) }} cm
                                    </div>

                                </div>


                                <div class="display-spec-row">

                                    <div class="display-spec-label">
                                        Status
                                    </div>

                                    <div class="display-spec-value">

                                        @if ($displayProduct->status === 'Active')
                                            <span class="badge success dot">
                                                Active
                                            </span>
                                        @else
                                            <span class="badge danger dot">
                                                Inactive
                                            </span>
                                        @endif

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                        ACTION
                    ================================================= --}}
                        <div class="form-actions">

                            <span class="spacer"></span>

                            <a href="{{ route('admin_display') }}" class="btn btn--danger">
                                <i class="bi bi-arrow-left"></i>
                                Kembali
                            </a>

                        </div>

                    </section>

                </div>

            </main>

            <div data-shell-footer></div>

        </div>

    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.addEventListener('click', function(event) {

                const thumbnail = event.target.closest(
                    '.display-gallery-thumbnail'
                );

                if (!thumbnail) {
                    return;
                }

                event.preventDefault();

                const imageUrl = thumbnail.dataset.imageUrl;

                if (!imageUrl) {
                    return;
                }

                const mainImage = document.querySelector(
                    '#displayMainImage'
                );

                if (!mainImage) {
                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | GANTI GAMBAR UTAMA
                |--------------------------------------------------------------------------
                */
                mainImage.src = imageUrl;

                /*
                |--------------------------------------------------------------------------
                | UPDATE THUMBNAIL AKTIF
                |--------------------------------------------------------------------------
                */
                document
                    .querySelectorAll('.display-gallery-thumbnail')
                    .forEach(function(item) {
                        item.classList.remove('is-active');
                    });

                thumbnail.classList.add('is-active');

            });
        });
    </script>
@endsection

@extends('admin_master')

@section('contents')
    <div class="shell">

        <div data-shell-sidebar></div>

        <div class="main">

            <div data-shell-topbar></div>

            <main class="content">

                {{-- HERO --}}
                <section class="hero">

                    <div class="hero-text">

                        <span class="eyebrow">
                            MASTER DATA · GALERI SAMPEL
                        </span>

                        <h1>
                            Detail Galeri Sampel
                        </h1>

                        <p class="hero-sub">
                            Lihat seluruh gambar contoh hasil cetak untuk
                            mesin dan kategori yang dipilih.
                        </p>

                    </div>

                </section>


                <div class="grid">

                    {{-- INFORMASI GALERI --}}
                    <section class="col-12 card">

                        <div class="card-head">

                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Informasi Galeri
                                </span>

                            </div>

                        </div>


                        <div class="form-grid">

                            <div class="field">

                                <label class="field-label">
                                    Mesin
                                </label>

                                <div class="input">
                                    {{ $engine->name }}
                                </div>

                            </div>


                            <div class="field">

                                <label class="field-label">
                                    Kategori
                                </label>

                                <div class="input">
                                    {{ $category->name }}
                                </div>

                            </div>

                        </div>

                    </section>


                    {{-- GAMBAR --}}
                    <section class="col-12 card">

                        <div class="card-head"
                            style="
                            display:flex;
                            align-items:center;
                            justify-content:space-between;
                            gap:12px;
                        ">

                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Gambar Sampel
                                </span>

                            </div>

                            <span class="badge dot success">
                                {{ $galleryImages->count() }} Gambar
                            </span>

                        </div>


                        <div id="galleryDetailsGrid"
                            style="
                            display:grid;
                            grid-template-columns:
                                repeat(
                                    auto-fill,
                                    minmax(220px, 1fr)
                                );
                            gap:16px;
                        ">

                            @foreach ($galleryImages as $image)
                                <div class="gallery-detail-card" data-image-id="{{ $image->id }}">

                                    <div class="gallery-detail-card__image">

                                        <img src="{{ route('admin_gallery_sample_image', $image->id) }}"
                                            alt="{{ $image->image_name }}">

                                        @if ($image->is_primary)
                                            <span class="gallery-detail-card__primary">
                                                Gambar Utama
                                            </span>
                                        @endif

                                    </div>


                                    <div class="gallery-detail-card__body">

                                        <div class="gallery-detail-card__name">
                                            {{ $image->image_name }}
                                        </div>

                                    </div>

                                </div>
                            @endforeach

                        </div>
                        <div class="form-actions">
                            <span class="spacer"></span>
                            <a href="{{ route('admin_gallery_samples') }}" class="btn btn--ghost">
                                Kembali
                            </a>

                            <a href="{{ route('admin_gallery_samples_edit', [
                                'engine_id' => $engine->id,
                                'category_id' => $category->id,
                            ]) }}"
                                class="btn btn--primary">
                                Edit Galeri
                            </a>
                        </div>

                    </section>

                </div>

            </main>

        </div>

    </div>
@endsection

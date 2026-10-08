@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">MASTER DATA · UKURAN MATERIAL</span>
                        {{-- <h1 class="hero-title">Detail Ukuran Material</h1> --}}
                        <p class="hero-sub">Lihat informasi dan daftar ukuran material yang tersedia sebagai acuan dalam
                            proses perhitungan dan estimasi harga.</p>
                    </div>
                </section>
                <div class="grid">

                    <section class="col-12 card">

                        {{-- HEADER --}}
                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">Informasi Ukuran Material</span>
                                {{-- <h2 class="card-title">Detail Konfigurasi Ukuran Material</h2> --}}
                            </div>
                        </div>

                        {{-- CONTENT --}}
                        <div class="card-body">

                            {{-- ============================= --}}
                            {{-- MATERIAL SUMMARY --}}
                            {{-- ============================= --}}
                            <div class="material-detail-header">

                                <div class="material-detail-icon">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M4 5h16v14H4z" />
                                        <path d="M8 9h8" />
                                        <path d="M8 13h5" />
                                    </svg>
                                </div>

                                <div class="material-detail-main">

                                    <div class="detail-label">
                                        MATERIAL
                                    </div>

                                    <div class="detail-material-name">
                                        {{ $material->material_name ?? '-' }}
                                    </div>

                                    <div class="material-detail-meta">

                                        <span class="data-cell-mono">
                                            {{ $material->material_code ?? '-' }}
                                        </span>

                                        <span class="meta-separator">
                                            •
                                        </span>

                                        <span>
                                            {{ $material->category_name ?? '-' }}
                                        </span>

                                    </div>

                                </div>

                                <div class="material-detail-status">

                                    <span class="badge {{ $material->status === 'Active' ? 'success' : 'danger' }} dot">
                                        {{ $material->status ?? '-' }}
                                    </span>

                                </div>

                            </div>


                            {{-- ============================= --}}
                            {{-- MATERIAL INFORMATION --}}
                            {{-- ============================= --}}
                            <div class="detail-info-grid">

                                {{-- KODE MATERIAL --}}
                                <div class="detail-info-item">

                                    <span class="detail-info-label">
                                        Kode Material
                                    </span>

                                    <span class="detail-info-value data-cell-mono">
                                        {{ $material->material_code ?? '-' }}
                                    </span>

                                </div>


                                {{-- KATEGORI --}}
                                <div class="detail-info-item">

                                    <span class="detail-info-label">
                                        Kategori
                                    </span>

                                    <span class="detail-info-value">
                                        {{ $material->category_name ?? '-' }}
                                    </span>

                                </div>


                                {{-- DIBUAT OLEH --}}
                                <div class="detail-info-item">

                                    <span class="detail-info-label">
                                        Dibuat Oleh
                                    </span>

                                    <span class="detail-info-value">
                                        {{ $material->created_by ?? '-' }}
                                    </span>

                                </div>


                                {{-- DIBUAT PADA --}}
                                <div class="detail-info-item">

                                    <span class="detail-info-label">
                                        Dibuat Pada
                                    </span>

                                    <span class="detail-info-value data-cell-mono">
                                        {{ $material->created_at ?? '-' }}
                                    </span>

                                </div>

                            </div>


                            {{-- ============================= --}}
                            {{-- DIVIDER --}}
                            {{-- ============================= --}}
                            <div class="detail-divider"></div>


                            {{-- ============================= --}}
                            {{-- SIZE SECTION --}}
                            {{-- ============================= --}}
                            <div class="size-detail-section">

                                <div class="size-detail-header">

                                    <div>

                                        <span class="detail-label">
                                            UKURAN MATERIAL
                                        </span>

                                        <p class="detail-section-description">
                                            Daftar lebar material yang tersedia untuk material ini.
                                        </p>

                                    </div>

                                    <div class="size-total">

                                        <strong>
                                            {{ $sizes->count() }}
                                        </strong>

                                        <span>
                                            ukuran
                                        </span>

                                    </div>

                                </div>


                                {{-- ============================= --}}
                                {{-- SIZE LIST --}}
                                {{-- ============================= --}}
                                <div class="size-list">

                                    @forelse ($sizes as $index => $size)
                                        <div class="size-item">

                                            {{-- NOMOR --}}
                                            <div class="size-number">
                                                {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                            </div>


                                            {{-- LEBAR --}}
                                            <div class="size-value">

                                                <span class="size-value-label">
                                                    Lebar
                                                </span>

                                                <span class="size-value-number data-cell-mono">
                                                    {{ number_format((float) $size->width, 2, '.', '') }}
                                                </span>

                                            </div>


                                            {{-- SATUAN --}}
                                            <div class="size-unit">

                                                <span class="size-value-label">
                                                    Satuan
                                                </span>

                                                <span class="size-unit-value">
                                                    {{ strtoupper($size->unit ?? '-') }}
                                                </span>

                                            </div>

                                        </div>

                                    @empty

                                        <div class="size-empty">

                                            <svg viewBox="0 0 24 24">
                                                <path d="M4 5h16v14H4z" />
                                                <path d="M8 9h8" />
                                                <path d="M8 13h5" />
                                            </svg>

                                            <span>
                                                Belum ada ukuran material.
                                            </span>

                                        </div>
                                    @endforelse

                                </div>

                            </div>

                            <div class="form-actions">
                                <span class="spacer"></span>

                                <a href="{{ route('admin_material_sizes') }}" class="btn btn--danger">
                                    <i class="bi bi-arrow-left"></i> Kembali
                                </a>
                            </div>

                        </div>


                    </section>

                </div>
            </main>
            <div data-shell-footer></div>
        </div>
    </div>
@endsection

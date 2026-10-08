@extends('admin_master')

@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>

        <div class="main">
            <div data-shell-topbar></div>

            <main class="content">

                {{-- ==========================================================
                HERO
            =========================================================== --}}
                <section class="hero">
                    <div class="hero-text">

                        <span class="eyebrow">
                            HARGA & TARIF · ATURAN PERHITUNGAN
                        </span>

                        <p class="hero-sub">
                            Informasi aturan perhitungan yang digunakan sebagai
                            dasar perhitungan biaya dan penentuan estimasi harga.
                        </p>

                    </div>
                </section>


                <div class="grid">

                    {{-- ======================================================
                    INFORMASI KONFIGURASI
                ======================================================= --}}
                    <section class="col-12 card">

                        <div class="card-head">
                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Informasi Aturan Perhitungan
                                </span>

                                {{-- <h2 class="card-title">
                                    Konfigurasi Aturan Perhitungan
                                </h2> --}}

                            </div>
                        </div>


                        <div class="card-body">

                            <div class="detail-overview">

                                {{-- MESIN --}}
                                <div class="detail-overview-item">

                                    <div class="detail-overview-icon">
                                        <svg viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="3" />
                                            <path
                                                d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.1 2.1-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-3v-.2a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1-2.1-2.1.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H5.2v-3h.2a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1 2.1-2.1.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.6v-.2h3v.2a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1 2.1 2.1-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1z" />
                                        </svg>
                                    </div>

                                    <div class="detail-overview-text">

                                        <div class="detail-overview-label">
                                            Mesin
                                        </div>

                                        <div class="detail-overview-value">
                                            {{ $calculationRule->engine_name ?? '-' }}
                                        </div>

                                    </div>

                                </div>


                                {{-- STATUS --}}
                                <div class="detail-overview-item">

                                    <div class="detail-overview-icon">
                                        <svg viewBox="0 0 24 24">
                                            <path d="M12 3v18" />
                                            <path d="M3 12h18" />
                                        </svg>
                                    </div>

                                    <div class="detail-overview-text">

                                        <div class="detail-overview-label">
                                            Status
                                        </div>

                                        <div class="detail-overview-value">
                                            {{ $calculationRule->status ?? '-' }}
                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </section>


                    {{-- ======================================================
                    DETAIL ATURAN
                ======================================================= --}}
                    <section class="col-12 card">

                        <div class="card-head">
                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Detail Aturan Perhitungan
                                </span>

                                {{-- <h2 class="card-title">
                                    Detail Aturan Perhitungan
                                </h2> --}}

                            </div>
                        </div>


                        <div class="card-body">

                            <div class="detail-info-grid">

                                {{-- KODE --}}
                                <div class="detail-info-item">

                                    <span class="detail-info-label">
                                        Kode
                                    </span>

                                    <span class="detail-info-value">
                                        {{ $calculationRule->code ?? '-' }}
                                    </span>

                                </div>


                                {{-- NAMA --}}
                                <div class="detail-info-item">

                                    <span class="detail-info-label">
                                        Nama Aturan
                                    </span>

                                    <span class="detail-info-value">
                                        {{ $calculationRule->name ?? '-' }}
                                    </span>

                                </div>


                                {{-- MINIMUM CHARGE --}}
                                <div class="detail-info-item">

                                    <span class="detail-info-label">
                                        Minimum Charge
                                    </span>

                                    <span class="detail-info-value">
                                        {{ number_format($calculationRule->minimum_charge ?? 0, 2, ',', '.') }}
                                        m²
                                    </span>

                                </div>


                                {{-- DESKRIPSI --}}
                                <div class="detail-info-item">

                                    <span class="detail-info-label">
                                        Deskripsi
                                    </span>

                                    <span class="detail-info-value">
                                        {{ $calculationRule->description ?? '-' }}
                                    </span>

                                </div>

                            </div>

                        </div>

                    </section>


                    {{-- ======================================================
                    INFORMASI AUDIT
                ======================================================= --}}
                    <section class="col-12 card">

                        <div class="card-head">
                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Informasi Data
                                </span>

                                {{-- <h2 class="card-title">
                                    Informasi Data
                                </h2> --}}

                            </div>
                        </div>


                        <div class="card-body">

                            <div class="detail-info-grid">

                                {{-- DIBUAT OLEH --}}
                                <div class="detail-info-item">

                                    <span class="detail-info-label">
                                        Dibuat Oleh
                                    </span>

                                    <span class="detail-info-value">
                                        {{ $calculationRule->created_by ?? '-' }}
                                    </span>

                                </div>


                                {{-- DIBUAT PADA --}}
                                <div class="detail-info-item">

                                    <span class="detail-info-label">
                                        Dibuat Pada
                                    </span>

                                    <span class="detail-info-value">
                                        {{ $calculationRule->created_at ?? '-' }}
                                    </span>

                                </div>


                                {{-- DIUBAH OLEH --}}
                                <div class="detail-info-item">

                                    <span class="detail-info-label">
                                        Diubah Oleh
                                    </span>

                                    <span class="detail-info-value">
                                        {{ $calculationRule->updated_by ?? '-' }}
                                    </span>

                                </div>


                                {{-- DIUBAH PADA --}}
                                <div class="detail-info-item">

                                    <span class="detail-info-label">
                                        Diubah Pada
                                    </span>

                                    <span class="detail-info-value">
                                        {{ $calculationRule->updated_at ?? '-' }}
                                    </span>

                                </div>

                            </div>


                            {{-- ACTION --}}
                            <div class="form-actions">

                                <span class="spacer"></span>

                                <a href="{{ route('admin_calculation_rules') }}" class="btn btn--danger">
                                    <i class="bi bi-arrow-left"></i>
                                    Kembali
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

@section('scripts')
@endsection

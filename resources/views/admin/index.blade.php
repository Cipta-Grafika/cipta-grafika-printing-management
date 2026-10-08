@extends('admin_master')

@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow" id="heroDate">Thursday · April 23 · 2026</span>
                        <h1 class="hero-title">Selamat datang, <span class="accent">{{ Auth::user()->name }}</span></h1>
                        <p class="hero-sub">Buat dan kelola estimasi harga berdasarkan produk, material, proses produksi,
                            serta komponen biaya yang tersedia.</p>
                    </div>
                </section>

                {{-- <section class="kpi-grid estimator-menu-grid" aria-label="Menu Estimasi">

                    @foreach ($engines as $engine)
                        <article class="estimator-card">

                            <div class="estimator-card__visual">

                                <div class="estimator-card__icon">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M6 9V4h12v5" />
                                        <path
                                            d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                        <path d="M6 14h12v7H6z" />
                                        <path d="M17 12h2" />
                                    </svg>
                                </div>

                                <div class="estimator-card__pattern">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>

                            </div>


                            <div class="estimator-card__content">

                                <span class="estimator-card__eyebrow">
                                    ESTIMASI CETAK
                                </span>

                                <h2 class="estimator-card__title">
                                    {{ $engine->name }}
                                </h2>

                                <p class="estimator-card__description">
                                    Buat estimasi biaya untuk kebutuhan cetak
                                    menggunakan mesin {{ $engine->name }}
                                    dengan pilihan material dan konfigurasi produksi
                                    yang tersedia.
                                </p>

                                <a href="#" class="estimator-card__action">
                                    <span>Buat Estimasi</span>

                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M5 12h14" />
                                        <path d="m13 6 6 6-6 6" />
                                    </svg>
                                </a>

                            </div>

                        </article>
                    @endforeach

                </section> --}}

                {{-- blade --}}
                <section class="kpi-grid estimator-menu-grid" aria-label="Menu Estimasi">

                    @foreach ($engines as $engine)
                        @php
                            $isActive = strtolower($engine->status) === 'active';
                        @endphp

                        <article class="estimator-card">

                            {{-- STATUS --}}
                            <div class="estimator-card__status {{ $isActive ? 'is-active' : 'is-inactive' }}">
                                <span class="estimator-card__status-dot"></span>
                                {{ $engine->status }}
                            </div>


                            <div class="estimator-card__visual">

                                <div class="estimator-card__icon">

                                    @if ($engine->name === 'Large Format')
                                        {{-- Large Format / Banner Printer --}}
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path d="M4 6h16" />
                                            <path d="M4 18h16" />
                                            <path d="M6 6v12" />
                                            <path d="M18 6v12" />
                                            <path d="M8 9h8v6H8z" />
                                            <path d="M10 9v6" />
                                            <path d="M14 9v6" />
                                        </svg>
                                    @elseif ($engine->name === 'Digital Print A3+')
                                        {{-- Digital Print A3+ --}}
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path d="M6 3h9l3 3v15H6z" />
                                            <path d="M15 3v4h4" />
                                            <path d="M9 11h6" />
                                            <path d="M9 14h6" />
                                            <path d="M9 17h4" />
                                        </svg>
                                    @elseif ($engine->name === 'Sticker Cutting')
                                        {{-- Sticker Cutting / Cutting Plotter --}}
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path d="M5 4h14v16H5z" />
                                            <path d="M8 8h8" />
                                            <path d="M8 12h5" />
                                            <path d="M15 15l3 3" />
                                            <path d="m18 15-3 3" />
                                            <circle cx="15" cy="15" r="2" />
                                        </svg>
                                    @else
                                        {{-- Default / Engine Baru --}}
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path d="M6 9V4h12v5" />
                                            <path
                                                d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                            <path d="M6 14h12v7H6z" />
                                            <path d="M17 12h2" />
                                        </svg>
                                    @endif

                                </div>





                                <div class="estimator-card__pattern">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>

                            </div>


                            <div class="estimator-card__content">

                                <span class="estimator-card__eyebrow">
                                    ESTIMASI CETAK
                                </span>

                                <h2 class="estimator-card__title">
                                    {{ $engine->name }}
                                </h2>

                                <p class="estimator-card__description">
                                    Buat estimasi biaya untuk kebutuhan cetak
                                    menggunakan mesin {{ $engine->name }}
                                    dengan pilihan material dan konfigurasi produksi
                                    yang tersedia.
                                </p>


                                {{-- ACTION --}}
                                @if ($isActive)
                                    @if ($engine->name === 'Large Format')
                                        <a href="{{ route('admin_create_lf_estimation') }}" class="estimator-card__action">
                                            <span>Buat Estimasi</span>

                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path d="M5 12h14" />
                                                <path d="m13 6 6 6-6 6" />
                                            </svg>
                                        </a>
                                    @else
                                        <a href="{{ route('admin_create_a3plus_estimation') }}"
                                            class="estimator-card__action">
                                            <span>Buat Estimasi</span>

                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path d="M5 12h14" />
                                                <path d="m13 6 6 6-6 6" />
                                            </svg>
                                        </a>
                                    @endif
                                @else
                                    <span class="estimator-card__action is-disabled" aria-disabled="true"
                                        style="cursor: not-allowed;">
                                        <span>Tidak Tersedia</span>

                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path d="M18 6 6 18" />
                                            <path d="m6 6 12 12" />
                                        </svg>
                                    </span>
                                @endif

                            </div>

                        </article>
                    @endforeach

                </section>


            </main>
            <div data-shell-footer></div>
        </div>
    </div>
@endsection

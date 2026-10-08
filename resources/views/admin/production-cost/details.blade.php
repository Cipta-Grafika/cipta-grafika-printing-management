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
                            HARGA & TARIF · ONGKOS PRODUKSI
                        </span>

                        {{-- <h1 class="hero-title">
                            Detail Ongkos Produksi
                        </h1> --}}

                        <p class="hero-sub">
                            Informasi ongkos produksi berdasarkan lokasi, engine,
                            dan material sebagai acuan dalam perhitungan estimasi biaya.
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
                                    Informasi Ongkos Produksi
                                </span>

                                {{-- <h2 class="card-title">
                                    Konfigurasi Ongkos Produksi
                                </h2> --}}
                            </div>
                        </div>


                        <div class="card-body">

                            <div class="detail-overview">

                                {{-- LOKASI --}}
                                <div class="detail-overview-item">
                                    <div class="detail-overview-icon">
                                        <svg viewBox="0 0 24 24">
                                            <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>
                                    </div>
                                    <div class="detail-overview-text">
                                        <div class="detail-overview-label">Lokasi</div>
                                        <div class="detail-overview-value">{{ $location->name ?? '-' }}</div>
                                    </div>
                                </div>

                                {{-- ENGINE --}}
                                <div class="detail-overview-item">
                                    <div class="detail-overview-icon">
                                        <svg viewBox="0 0 24 24">
                                            <circle cx="12" cy="12" r="3" />
                                            <path
                                                d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.1 2.1-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-3v-.2a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1-2.1-2.1.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H5.2v-3h.2a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1 2.1-2.1.1.1a1.7 1.7 0 0 0 1.9.3 1.7 1.7 0 0 0 1-1.6v-.2h3v.2a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1 2.1 2.1-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1z" />
                                        </svg>
                                    </div>
                                    <div class="detail-overview-text">
                                        <div class="detail-overview-label">Engine</div>
                                        <div class="detail-overview-value">{{ $engine->name ?? '-' }}</div>
                                    </div>
                                </div>

                            </div>

                        </div>

                    </section>


                    {{-- ======================================================
                    DAFTAR ONGKOS
                ======================================================= --}}
                    <section class="col-12 card">

                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">
                                    Daftar Ongkos Produksi
                                </span>

                                {{-- <h2 class="card-title">
                                    Daftar Ongkos Produksi
                                </h2> --}}
                            </div>
                        </div>

                        <div class="card-body">

                            <div class="table-responsive">
                                <table class="display data-table">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;">No</th>
                                            <th style="text-align: center;">Material</th>
                                            <th style="text-align: center;">Ongkos Produksi</th>
                                            <th style="text-align: center;">Ongkos Finishing</th>
                                            <th style="text-align: center;">Total Cost</th>
                                            <th style="text-align: center;">Harga Polos</th>
                                            <th style="text-align: center;">Harga Umum</th>
                                            <th style="text-align: center;">Harga Divisi</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach ($productionCosts as $index => $item)
                                            <tr>
                                                <td class="text-center">
                                                    {{ $index + 1 }}
                                                </td>

                                                <td class="text-center">
                                                    {{ $item->material_name }}
                                                </td>

                                                <td class="text-center">
                                                    Rp
                                                    {{ number_format($item->production_cost, 2, ',', '.') }}
                                                </td>

                                                <td class="text-center">
                                                    Rp
                                                    {{ number_format($item->finishing_cost, 2, ',', '.') }}
                                                </td>

                                                <td class="text-center">
                                                    Rp
                                                    {{ number_format($item->total_cost, 2, ',', '.') }}
                                                </td>

                                                <td class="text-center">
                                                    Rp
                                                    {{ number_format($item->harga_polos, 2, ',', '.') }}
                                                </td>

                                                <td class="text-center">
                                                    Rp
                                                    {{ number_format($item->harga_umum, 2, ',', '.') }}
                                                </td>

                                                <td class="text-center">
                                                    Rp
                                                    {{ number_format($item->harga_divisi, 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
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

                            @php
                                $firstProductionCost = $productionCosts->first();
                            @endphp

                            <div class="detail-info-grid">

                                <div class="detail-info-item">
                                    <span class="detail-info-label">Dibuat Oleh</span>
                                    <span class="detail-info-value">{{ $firstProductionCost->created_by ?? '-' }}</span>
                                </div>

                                <div class="detail-info-item">
                                    <span class="detail-info-label">Dibuat Pada</span>
                                    <span class="detail-info-value">{{ $firstProductionCost->created_at ?? '-' }}</span>
                                </div>

                                <div class="detail-info-item">
                                    <span class="detail-info-label">Diubah Oleh</span>
                                    <span class="detail-info-value">{{ $firstProductionCost->updated_by ?? '-' }}</span>
                                </div>

                                <div class="detail-info-item">
                                    <span class="detail-info-label">Diubah Pada</span>
                                    <span class="detail-info-value">{{ $firstProductionCost->updated_at ?? '-' }}</span>
                                </div>

                            </div>

                            <div class="form-actions">
                                <span class="spacer"></span>

                                <a href="{{ route('admin_production_costs') }}" class="btn btn--danger">
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

@section('scripts')
@endsection

@extends('admin_master')

@section('contents')
    <div class="shell display-component-page">

        <div data-shell-sidebar></div>

        <div class="main">

            <div data-shell-topbar></div>

            <main class="content">

                {{-- HERO: STYLE BAWAAN --}}
                <section class="hero">

                    <div class="hero-text">

                        <span class="eyebrow">
                            DISPLAY · COMPONENT
                        </span>

                        <p class="hero-sub">
                            Informasi konfigurasi Display, lokasi produksi, mesin, biaya, serta rincian komponen dan harga
                            yang telah tersimpan.
                        </p>

                    </div>

                </section>

                <div class="grid">

                    <section class="col-12 card">

                        {{-- CARD HEADER --}}
                        <div class="card-head">

                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Detail Komponen Display
                                </span>

                            </div>

                        </div>

                        <div class="display-form-body">
                            {{-- Konten halaman yang sudah ada --}}

                            {{-- =========================================
                                INFORMASI KONFIGURASI
                            ========================================== --}}
                            <section class="display-configuration-container">

                                <div class="display-configuration-item">

                                    {{-- HEADER IDENTITAS --}}
                                    <div class="display-overview-header">

                                        <div class="display-overview-identity">

                                            <div class="display-overview-icon">
                                                <i class="bi bi-display"></i>
                                            </div>

                                            <div class="display-overview-title">

                                                <span class="display-overview-label">
                                                    DISPLAY PRODUCT
                                                </span>

                                                <h3 class="display-name">
                                                    {{ $displayProduct->display_name }}
                                                </h3>

                                            </div>

                                        </div>

                                        <span class="badge dot success">
                                            Header Terpilih
                                        </span>

                                    </div>

                                    {{-- INFORMASI KONFIGURASI --}}
                                    <div class="display-overview-details">


                                        {{-- LOKASI PRODUKSI --}}
                                        <div class="configuration-info-section">

                                            <div class="configuration-info-header">

                                                <div class="configuration-info-icon">
                                                    <i class="bi bi-pin-map"></i>
                                                </div>

                                                <div class="configuration-info-heading">
                                                    <span class="configuration-info-title">
                                                        Lokasi Produksi
                                                    </span>
                                                </div>

                                            </div>

                                            <div class="configuration-info-body">

                                                <div class="configuration-info-value">
                                                    {{ $configuration->location_name }}
                                                </div>

                                            </div>

                                        </div>


                                        {{-- MESIN PRODUKSI --}}
                                        <div class="configuration-info-section">

                                            <div class="configuration-info-header">

                                                <div class="configuration-info-icon">
                                                    <i class="bi bi-printer"></i>
                                                </div>

                                                <div class="configuration-info-heading">
                                                    <span class="configuration-info-title">
                                                        Mesin Produksi
                                                    </span>
                                                </div>

                                            </div>

                                            <div class="configuration-info-body">

                                                <div class="configuration-info-value">
                                                    {{ $configuration->engine_name }}
                                                </div>

                                            </div>

                                        </div>



                                        {{-- HARGA RANGKA --}}
                                        <div class="frame-price-section">

                                            {{-- HEADER --}}
                                            <div class="frame-price-header">

                                                <div class="frame-price-icon">
                                                    <i class="bi bi-rulers"></i>
                                                </div>

                                                <div class="frame-price-heading">
                                                    <span class="frame-price-title">
                                                        Rangka
                                                    </span>
                                                </div>

                                            </div>

                                            {{-- PRICE DETAILS --}}
                                            <div class="frame-price-body">

                                                {{-- HARGA UMUM --}}
                                                <div class="frame-price-row">

                                                    <span class="frame-price-label">
                                                        Harga Umum
                                                    </span>

                                                    <strong class="frame-price-value">
                                                        Rp
                                                        {{ number_format((float) ($framePrice->general_price ?? 0), 0, ',', '.') }}
                                                    </strong>

                                                </div>

                                                {{-- HARGA DIVISI --}}
                                                <div class="frame-price-row">

                                                    <span class="frame-price-label">
                                                        Harga Divisi
                                                    </span>

                                                    <strong class="frame-price-value">
                                                        Rp
                                                        {{ number_format((float) ($framePrice->division_price ?? 0), 0, ',', '.') }}
                                                    </strong>

                                                </div>

                                            </div>

                                        </div>


                                    </div>

                                    {{-- RINGKASAN BIAYA --}}
                                    <div class="display-cost-section">

                                        <div class="display-cost-heading">

                                            <span class="display-cost-heading-icon">
                                                <i class="bi bi-receipt"></i>
                                            </span>

                                            <span>
                                                Ringkasan Biaya Produksi
                                            </span>

                                        </div>

                                        <div class="display-cost-grid">

                                            <div class="display-cost-card">

                                                <span class="display-cost-label">
                                                    Cost Rangka
                                                </span>

                                                <strong class="display-cost-value">
                                                    Rp
                                                    {{ number_format((float) $configuration->cost_rangka, 0, ',', '.') }}
                                                </strong>

                                            </div>

                                            <div class="display-cost-card">

                                                <span class="display-cost-label">
                                                    Cost Finishing
                                                </span>

                                                <strong class="display-cost-value">
                                                    Rp
                                                    {{ number_format((float) $configuration->cost_finishing, 0, ',', '.') }}
                                                </strong>

                                            </div>

                                            <div class="display-cost-card display-cost-card-total">

                                                <span class="display-cost-label">
                                                    Total Cost
                                                </span>

                                                <strong class="display-cost-value">
                                                    Rp
                                                    {{ number_format((float) $configuration->total_cost, 0, ',', '.') }}
                                                </strong>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </section>

                            {{-- =========================================
                                AREA KOMPONEN
                            ========================================== --}}
                            <section class="display-configuration-container" style="margin-top: 20px;">

                                <div class="display-configuration-item">

                                    {{-- HEADER DETAIL KOMPONEN --}}
                                    <div class="display-detail-header">
                                        <div class="display-detail-heading">
                                            <div class="display-detail-icon">
                                                <i class="bi bi-layers"></i>
                                            </div>

                                            <div>
                                                <h3 class="display-detail-title">Detail Komponen</h3>
                                                <p class="display-detail-subtitle">
                                                    Daftar komponen dan harga berdasarkan konfigurasi Display.
                                                </p>
                                            </div>
                                        </div>

                                        <span class="badge dot success">Read Only</span>
                                    </div>

                                    {{-- GROUPING BERDASARKAN KONFIGURASI --}}
                                    @php
                                        $groupedComponents = collect($components)->groupBy('configuration_id');
                                    @endphp

                                    @forelse ($groupedComponents as $configurationId => $items)
                                        @php
                                            $config = $items->first();
                                        @endphp

                                        <div class="display-detail-group">
                                            {{-- TABEL KOMPONEN --}}
                                            <div class="display-detail-table-wrapper">

                                                <table class="display-detail-table">
                                                    <thead>
                                                        <tr>
                                                            <th class="display-detail-col-number">No.</th>
                                                            <th>Nama Komponen</th>
                                                            <th class="text-center">Ukuran</th>
                                                            <th class="text-center">Tipe</th>
                                                            <th class="text-right">Harga Umum</th>
                                                            <th class="text-right">Harga Divisi</th>
                                                            <th class="text-right">Harga Polos</th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        @foreach ($items as $index => $component)
                                                            <tr>
                                                                <td class="display-detail-number">
                                                                    {{ $index + 1 }}
                                                                </td>

                                                                <td>
                                                                    <div class="display-detail-component-name">
                                                                        {{ $component->component_name }}
                                                                    </div>
                                                                </td>

                                                                <td class="display-detail-component-name"
                                                                    style="text-align: center;">
                                                                    {{ number_format((float) $component->display_length, 0, ',', '.') }}
                                                                    x
                                                                    {{ number_format((float) $component->display_width, 0, ',', '.') }}
                                                                    cm
                                                                </td>

                                                                <td class="text-center">
                                                                    <span
                                                                        class="display-detail-type {{ $component->component_type === 'frame' ? 'is-frame' : 'is-material' }}">
                                                                        {{ $component->component_type === 'frame' ? 'Rangka' : ucfirst($component->component_type) }}
                                                                    </span>
                                                                </td>

                                                                <td class="text-right display-detail-price">
                                                                    Rp
                                                                    {{ number_format((float) ($component->general_price ?? 0), 0, ',', '.') }}
                                                                </td>

                                                                <td class="text-right display-detail-price">
                                                                    Rp
                                                                    {{ number_format((float) ($component->division_price ?? 0), 0, ',', '.') }}
                                                                </td>

                                                                <td class="text-right display-detail-price">
                                                                    Rp
                                                                    {{ number_format((float) ($component->plain_price ?? 0), 0, ',', '.') }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>

                                            </div>

                                            {{-- FOOTER KONFIGURASI --}}
                                            <div class="display-detail-footer">
                                                <span>
                                                    <i class="bi bi-check-circle"></i>
                                                    Data komponen tersimpan
                                                </span>

                                                <strong>{{ $items->count() }} Komponen</strong>
                                            </div>

                                        </div>

                                    @empty

                                        {{-- EMPTY STATE --}}
                                        <div class="display-detail-empty">
                                            <div class="display-detail-empty-icon">
                                                <i class="bi bi-inbox"></i>
                                            </div>

                                            <strong>Belum Ada Komponen</strong>

                                            <p>
                                                Belum terdapat data komponen yang tersimpan
                                                untuk Display Product ini.
                                            </p>
                                        </div>
                                    @endforelse

                                </div>

                            </section>

                            {{-- =========================================
                                ACTION
                            ========================================== --}}
                            <div class="form-actions">

                                {{-- <span class="badge dot success">
                                    Siap disimpan
                                </span>

                                <span class="spacer"></span> --}}

                                <a href="{{ route('admin_pc_finishing_displays') }}" class="btn btn--ghost">
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

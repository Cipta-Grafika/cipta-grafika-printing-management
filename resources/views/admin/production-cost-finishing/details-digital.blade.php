@extends('admin_master')

@section('contents')
    @php
        $formatRupiah = function ($value) {
            return $value === null ? '-' : 'Rp' . number_format((float) $value, 0, ',', '.');
        };
    @endphp

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
                            BIAYA · MATERIAL
                        </span>

                        <p class="hero-sub">
                            Detail ongkos produksi berdasarkan engine, lokasi,
                            dan material.
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
                                    Detail Ongkos Produksi
                                </span>

                            </div>

                        </div>


                        {{-- =================================================
                            ENGINE TITLE
                        ================================================= --}}
                        <div class="details-engine">

                            <h4 class="details-engine-title" style="text-transform: uppercase;">
                                {{ $productionCost->engine_name }}
                            </h4>

                        </div>


                        {{-- =================================================
                            LOCATION ACCORDION
                        ================================================= --}}
                        <div class="details-locations">

                            <div class="details-location is-open" data-accordion>

                                <button type="button" class="details-location-header" aria-expanded="true"
                                    aria-controls="location-content-digital">

                                    <span class="details-location-info">

                                        <span class="details-location-name" style="text-transform: uppercase;">
                                            {{ $productionCost->location_name }}
                                        </span>

                                    </span>

                                    <span class="details-location-icon" aria-hidden="true">
                                        <i class="bi bi-chevron-down"></i>
                                    </span>

                                </button>


                                <div id="location-content-digital" class="details-location-content">

                                    <div class="details-location-inner">

                                        {{-- =================================================
                                            OUTSOURCING
                                            Location → Vendor → Category → Material
                                        ================================================= --}}
                                        @if ($productionCost->vendor_name)
                                            <div class="details-vendors">

                                                <div class="details-vendor is-open" data-vendor-accordion>

                                                    <button type="button" class="details-vendor-header"
                                                        aria-expanded="true" aria-controls="vendor-content-digital"
                                                        style="margin-bottom: 20px;">

                                                        <span class="details-vendor-info">

                                                            <span class="details-vendor-name"
                                                                style="text-transform: uppercase; font-weight: 600; margin-right: 10px;">
                                                                {{ $productionCost->vendor_name }}
                                                            </span>

                                                        </span>

                                                        <span class="details-vendor-icon" aria-hidden="true">
                                                            <i class="bi bi-chevron-down"></i>
                                                        </span>

                                                    </button>

                                                    <div id="vendor-content-digital" class="details-vendor-content">

                                                        <div class="details-vendor-inner">
                                        @endif

                                        {{-- =================================================
                                            CATEGORY
                                        ================================================= --}}
                                        <div class="details-categories">

                                            @forelse ($categories as $categoryIndex => $category)
                                                <div class="details-category" data-category-accordion>

                                                    {{-- CATEGORY HEADER --}}
                                                    <button type="button" class="details-category-header"
                                                        aria-expanded="false"
                                                        aria-controls="category-content-digital-{{ $categoryIndex }}">

                                                        <span class="details-category-info">

                                                            <span class="details-category-label"
                                                                style="text-transform: uppercase;">
                                                                Kategori
                                                            </span>

                                                            <span class="details-category-name"
                                                                style="text-transform: uppercase;">
                                                                {{ $category->category_name }}
                                                            </span>

                                                        </span>

                                                        <span class="details-category-icon" aria-hidden="true">
                                                            <i class="bi bi-chevron-down"></i>
                                                        </span>

                                                    </button>


                                                    {{-- CATEGORY CONTENT --}}
                                                    <div id="category-content-digital-{{ $categoryIndex }}"
                                                        class="details-category-content">

                                                        <div class="details-category-inner">

                                                            <div class="details-digital-table-wrapper">

                                                                <table class="details-digital-table">

                                                                    <thead>

                                                                        <tr class="details-digital-group-row">
                                                                            <th rowspan="3" class="details-digital-sticky details-digital-col-no">No</th>
                                                                            <th rowspan="3" class="details-digital-sticky details-digital-col-material">Item</th>
                                                                            <th rowspan="3">Ukuran</th>
                                                                            <th rowspan="3" class="details-digital-cost">Kertas</th>
                                                                            <th rowspan="3" class="details-digital-cost">Klik</th>
                                                                            <th rowspan="3" class="details-digital-cost">Cost Print<br>1 Sisi</th>
                                                                            <th rowspan="3" class="details-digital-cost">Cost Print<br>2 Sisi</th>
                                                                            @foreach ($priceGroups as $groupKey => $groupLabel)
                                                                                <th colspan="{{ count($tierLabels) }}"
                                                                                    class="details-digital-group details-digital-group--{{ $groupKey }}">
                                                                                    {{ $groupLabel }}
                                                                                </th>
                                                                            @endforeach
                                                                        </tr>

                                                                        <tr class="details-digital-tier-row">
                                                                            @foreach ($priceGroups as $groupKey => $groupLabel)
                                                                                @foreach ($tierLabels as $tierLabel)
                                                                                    <th class="details-digital-group--{{ $groupKey }}">{{ $tierLabel }}</th>
                                                                                @endforeach
                                                                            @endforeach
                                                                        </tr>

                                                                        <tr class="details-digital-markup-row">
                                                                            @foreach ($priceGroups as $groupKey => $groupLabel)
                                                                                @foreach ($tierLabels as $tier => $tierLabel)
                                                                                    <th class="details-digital-group--{{ $groupKey }}" title="Markup">
                                                                                        {{ $category->markups[$groupKey][$tier] }}
                                                                                    </th>
                                                                                @endforeach
                                                                            @endforeach
                                                                        </tr>

                                                                    </thead>

                                                                    <tbody>

                                                                        @foreach ($category->details as $detailIndex => $detail)
                                                                            <tr>
                                                                                <td class="details-digital-sticky details-digital-col-no">{{ $detailIndex + 1 }}</td>
                                                                                <td class="details-digital-sticky details-digital-col-material">{{ $detail->material_name }}</td>
                                                                                <td class="details-digital-size">{{ $detail->size_label ?: '-' }}</td>
                                                                                <td class="details-digital-cost">{{ $formatRupiah($detail->paper_cost) }}</td>
                                                                                <td class="details-digital-cost">{{ $formatRupiah($detail->click_cost) }}</td>
                                                                                <td class="details-digital-cost details-digital-cost-total">
                                                                                    {{ $formatRupiah($detail->single_print_cost) }}
                                                                                </td>
                                                                                <td class="details-digital-cost details-digital-cost-total">
                                                                                    {{ $formatRupiah($detail->double_print_cost) }}
                                                                                </td>
                                                                                @foreach ($priceGroups as $groupKey => $groupLabel)
                                                                                    @foreach ($tierLabels as $tier => $tierLabel)
                                                                                        @php
                                                                                            $price = $detail->prices[$groupKey][$tier];
                                                                                        @endphp
                                                                                        <td class="details-digital-price details-digital-group--{{ $groupKey }}">
                                                                                            {{ $formatRupiah($price ? $price->selling_price : null) }}
                                                                                        </td>
                                                                                    @endforeach
                                                                                @endforeach
                                                                            </tr>
                                                                        @endforeach

                                                                    </tbody>

                                                                </table>

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>
                                            @empty
                                                <span class="details-empty">
                                                    Belum ada material.
                                                </span>
                                            @endforelse

                                        </div>

                                        @if ($productionCost->vendor_name)
                                                        </div>

                                                    </div>

                                                </div>

                                            </div>
                                        @endif

                                    </div>

                                </div>

                            </div>


                            {{-- =================================================
                                ACTION
                            ================================================= --}}
                            <div class="form-actions">

                                <span class="spacer"></span>

                                <a href="{{ route('admin_pc_finishings') }}" class="btn btn--danger">
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const accordionTypes = [
                ['[data-accordion]', '.details-location-header'],
                ['[data-vendor-accordion]', '.details-vendor-header'],
                ['[data-category-accordion]', '.details-category-header'],
            ];

            accordionTypes.forEach(function([accordionSelector, headerSelector]) {
                document.querySelectorAll(accordionSelector).forEach(function(accordion) {
                    const button = accordion.querySelector(headerSelector);

                    if (!button) {
                        return;
                    }

                    button.addEventListener('click', function() {
                        const isOpen = accordion.classList.contains('is-open');

                        accordion.classList.toggle('is-open', !isOpen);
                        button.setAttribute('aria-expanded', String(!isOpen));
                    });
                });
            });

        });
    </script>
@endsection

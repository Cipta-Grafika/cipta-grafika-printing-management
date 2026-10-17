@extends('admin_master')

@section('contents')
    @php
        $formatRupiah = function ($value) {
            if ($value === null || $value === '') {
                return '-';
            }
            return 'Rp' . number_format((float) $value, 2, ',', '.');
        };
    @endphp

    <style>
        .details-cutting-category-chips {
            display: inline-flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
        }

        .details-cutting-category-chip {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            background: var(--primary-soft);
            color: var(--primary);
            border: 1px solid var(--border);
        }

        /* Styling Background Harga Umum (Amber / Kuning Keemasan) */
        .details-digital-table thead th.details-digital-group--general {
            background-color: var(--bg-muted) !important;
            background-image: linear-gradient(rgba(245, 158, 11, 0.22), rgba(245, 158, 11, 0.22)) !important;
            color: #f59e0b !important;
            font-weight: 700;
        }

        .details-digital-table tbody td.details-digital-group--general {
            background-color: var(--bg-card) !important;
            background-image: linear-gradient(rgba(245, 158, 11, 0.10), rgba(245, 158, 11, 0.10)) !important;
            color: var(--t-base) !important;
        }

        /* Styling Background Harga Divisi (Emerald / Hijau) */
        .details-digital-table thead th.details-digital-group--division {
            background-color: var(--bg-muted) !important;
            background-image: linear-gradient(rgba(16, 185, 129, 0.22), rgba(16, 185, 129, 0.22)) !important;
            color: #10b981 !important;
            font-weight: 700;
        }

        .details-digital-table tbody td.details-digital-group--division {
            background-color: var(--bg-card) !important;
            background-image: linear-gradient(rgba(16, 185, 129, 0.10), rgba(16, 185, 129, 0.10)) !important;
            color: #10b981 !important;
            font-weight: 600;
        }

        /* Border Pemisah Tegas Antara Harga Umum dan Harga Divisi */
        .details-digital-table th.details-digital-divider,
        .details-digital-table td.details-digital-divider {
            border-left: 2px solid var(--border) !important;
        }
    </style>

    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">BIAYA · ONGKOS POTONG (GUILLOTINE)</span>
                        <p class="hero-sub">
                            Detail rincian ongkos produksi potong minimal Digital Print A3+ berdasarkan mesin, lokasi, kategori, dan ukuran jadi (A3, A4, A5, &le; A6).
                        </p>
                    </div>
                </section>

                <div class="grid">
                    <section class="col-12 card">
                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">Detail Ongkos Produksi Potong</span>
                            </div>
                        </div>

                        {{-- ENGINE NAME --}}
                        <div class="details-engine">
                            <h4 class="details-engine-title" style="text-transform: uppercase;">
                                {{ $productionCost->engine_name }}
                            </h4>
                        </div>

                        {{-- LOCATIONS --}}
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
                                        {{-- VENDOR IF OUTSOURCING --}}
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

                                        {{-- CATEGORIES ACCORDION --}}
                                        <div class="details-categories">
                                            @forelse ($groupedCategories as $categoryIndex => $category)
                                                <div class="details-category is-open" data-category-accordion>
                                                    <button type="button" class="details-category-header"
                                                        aria-expanded="true"
                                                        aria-controls="category-content-potong-{{ $categoryIndex }}">
                                                        <span class="details-category-info" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                                            <span class="details-category-label" style="text-transform: uppercase; font-weight: 700;">
                                                                @if ($category->category_names->count() > 1)
                                                                    {{ $category->category_names->count() }} Kategori · Tarif Ongkos Potong Sama
                                                                @else
                                                                    Kategori
                                                                @endif
                                                            </span>
                                                            <span class="details-cutting-category-chips">
                                                                @foreach ($category->category_names as $categoryName)
                                                                    <span class="details-cutting-category-chip">{{ $categoryName }}</span>
                                                                @endforeach
                                                            </span>
                                                        </span>
                                                        <span class="details-category-icon" aria-hidden="true">
                                                            <i class="bi bi-chevron-down"></i>
                                                        </span>
                                                    </button>

                                                    <div id="category-content-potong-{{ $categoryIndex }}"
                                                        class="details-category-content">
                                                        <div class="details-category-inner" style="padding-top: 14px;">
                                                            <div class="details-digital-table-wrapper">
                                                                <table class="details-digital-table">
                                                                    <thead>
                                                                        <tr class="details-digital-group-row">
                                                                            <th rowspan="2" class="details-digital-sticky details-digital-col-no">No</th>
                                                                            <th rowspan="2" class="details-digital-sticky details-digital-col-material">Spesifikasi</th>
                                                                            @foreach ($priceGroups as $groupKey => $groupLabel)
                                                                                <th colspan="{{ count($sizes) }}"
                                                                                    class="details-digital-group details-digital-group--{{ $groupKey }} {{ $groupKey === 'division' ? 'details-digital-divider' : '' }}">
                                                                                    {{ $groupLabel }}
                                                                                </th>
                                                                            @endforeach
                                                                        </tr>
                                                                        <tr class="details-digital-tier-row">
                                                                            @foreach ($priceGroups as $groupKey => $groupLabel)
                                                                                @foreach ($sizes as $sizeKey => $sizeLabel)
                                                                                    <th class="details-digital-group--{{ $groupKey }} {{ $groupKey === 'division' && $loop->first ? 'details-digital-divider' : '' }}">
                                                                                        {{ $sizeLabel }}
                                                                                    </th>
                                                                                @endforeach
                                                                            @endforeach
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="details-digital-sticky details-digital-col-no">1</td>
                                                                            <td class="details-digital-sticky details-digital-col-material">
                                                                                <strong>Ongkos Potong Minimal</strong>
                                                                            </td>
                                                                            @foreach ($priceGroups as $groupKey => $groupLabel)
                                                                                @foreach ($sizes as $sizeKey => $sizeLabel)
                                                                                    @php
                                                                                        $priceObj = $category->prices[$groupKey][$sizeKey] ?? null;
                                                                                    @endphp
                                                                                    <td class="details-digital-price details-digital-group--{{ $groupKey }} {{ $groupKey === 'division' && $loop->first ? 'details-digital-divider' : '' }}">
                                                                                        {{ $formatRupiah($priceObj ? $priceObj->min_price : null) }}
                                                                                    </td>
                                                                                @endforeach
                                                                            @endforeach
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div style="padding: 24px; text-align: center; color: var(--t-muted);">
                                                    Belum ada rincian ongkos potong untuk lokasi ini.
                                                </div>
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
                        </div>

                        {{-- FOOTER FORM ACTIONS --}}
                        <div class="form-actions" style="margin-top: 24px; display: flex; justify-content: flex-end; gap: 12px;">
                            <a href="{{ route('admin_production_cost_finishing_cuttings') }}" class="btn btn--ghost">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                            <a href="{{ route('admin_edit_pc_finishing_potong', base64_encode($productionCost->id)) }}" class="btn btn--primary">
                                <i class="bi bi-pen"></i> Edit Ongkos
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


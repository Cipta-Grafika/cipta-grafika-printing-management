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
                            BIAYA · LAMINASI
                        </span>

                        <p class="hero-sub">
                            Detail ongkos produksi berdasarkan engine,
                            lokasi, dan laminasi.
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
                                {{ $engine->name }}
                            </h4>

                        </div>


                        {{-- =================================================
                    LOCATION ACCORDION
                ================================================= --}}
                        <div class="details-locations">

                            @foreach ($locations as $locationIndex => $location)
                                <div class="details-location" data-accordion>

                                    {{-- =====================================
                                LOCATION HEADER
                            ====================================== --}}
                                    <button type="button" class="details-location-header" aria-expanded="false"
                                        aria-controls="location-content-{{ $locationIndex }}">

                                        <span class="details-location-info">

                                            <span class="details-location-name" style="text-transform: uppercase;">
                                                {{ $location->location_name }}
                                            </span>

                                        </span>

                                        <span class="details-location-icon" aria-hidden="true">
                                            <i class="bi bi-chevron-down"></i>
                                        </span>

                                    </button>


                                    {{-- =====================================
                                LOCATION CONTENT
                            ====================================== --}}
                                    <div id="location-content-{{ $locationIndex }}" class="details-location-content">

                                        <div class="details-location-inner">


                                            {{-- =================================================
                                        OUTSOURCING
                                    ================================================= --}}
                                            @if (strtolower(trim($location->location_name)) === 'outsourcing')
                                                <div class="details-vendors">

                                                    @foreach ($location->vendors as $vendorIndex => $vendor)
                                                        <div class="details-vendor" data-vendor-accordion>

                                                            {{-- =====================================
                                                        VENDOR HEADER
                                                    ====================================== --}}
                                                            <button type="button" class="details-vendor-header"
                                                                style="margin-bottom: 10px !important;"
                                                                aria-expanded="false"
                                                                aria-controls="vendor-content-{{ $locationIndex }}-{{ $vendorIndex }}">

                                                                <span class="details-vendor-info">

                                                                    {{-- <span class="details-vendor-label"
                                                                        style="text-transform: uppercase;">
                                                                        Vendor
                                                                    </span> --}}

                                                                    <span class="details-vendor-name"
                                                                        style="text-transform: uppercase;">
                                                                        {{ $vendor->vendor_name }}
                                                                    </span>

                                                                </span>

                                                                <span class="details-vendor-icon" aria-hidden="true">
                                                                    <i class="bi bi-chevron-down"></i>
                                                                </span>

                                                            </button>


                                                            {{-- =====================================
                                                        VENDOR CONTENT
                                                    ====================================== --}}
                                                            <div id="vendor-content-{{ $locationIndex }}-{{ $vendorIndex }}"
                                                                class="details-vendor-content">

                                                                <div class="details-vendor-inner">


                                                                    {{-- =================================================
                                                                CATEGORY ACCORDION
                                                            ================================================= --}}
                                                                    <div class="details-categories">

                                                                        @foreach ($vendor->categories as $categoryIndex => $category)
                                                                            <div class="details-category"
                                                                                data-category-accordion>

                                                                                {{-- =====================================
                                                                            CATEGORY HEADER
                                                                        ====================================== --}}
                                                                                <button type="button"
                                                                                    class="details-category-header"
                                                                                    aria-expanded="false"
                                                                                    aria-controls="vendor-category-content-{{ $locationIndex }}-{{ $vendorIndex }}-{{ $categoryIndex }}">

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

                                                                                    <span class="details-category-icon"
                                                                                        aria-hidden="true">
                                                                                        <i class="bi bi-chevron-down"></i>
                                                                                    </span>

                                                                                </button>


                                                                                {{-- =====================================
                                                                            CATEGORY CONTENT
                                                                        ====================================== --}}
                                                                                <div id="vendor-category-content-{{ $locationIndex }}-{{ $vendorIndex }}-{{ $categoryIndex }}"
                                                                                    class="details-category-content">

                                                                                    <div class="details-category-inner">

                                                                                        <div class="details-table-wrapper">

                                                                                            <table class="details-table">

                                                                                                <thead>

                                                                                                    <tr
                                                                                                        class="details-table-group-header">

                                                                                                        <th rowspan="2">
                                                                                                            Item
                                                                                                        </th>

                                                                                                        <th colspan="2">
                                                                                                            Ukuran Tersedia
                                                                                                        </th>

                                                                                                        <th rowspan="2">
                                                                                                            Harga Vendor
                                                                                                        </th>

                                                                                                        <th rowspan="2">
                                                                                                            Harga Umum
                                                                                                        </th>

                                                                                                        <th rowspan="2">
                                                                                                            Harga Divisi
                                                                                                        </th>

                                                                                                        <th rowspan="2">
                                                                                                            Harga Polos
                                                                                                        </th>

                                                                                                    </tr>


                                                                                                    <tr
                                                                                                        class="details-table-sub-header">

                                                                                                        <th>
                                                                                                            Lebar
                                                                                                        </th>

                                                                                                        <th>
                                                                                                            Panjang
                                                                                                        </th>

                                                                                                    </tr>

                                                                                                </thead>


                                                                                                <tbody>

                                                                                                    @foreach ($category->details as $detail)
                                                                                                        <tr>

                                                                                                            {{-- =====================================
                                                                                                        LAMINASI
                                                                                                    ====================================== --}}
                                                                                                            <td
                                                                                                                class="details-material-cell">

                                                                                                                <div
                                                                                                                    class="details-material-name">

                                                                                                                    {{ $detail->lamination_name }}

                                                                                                                </div>

                                                                                                            </td>


                                                                                                            {{-- =====================================
                                                                                                        LEBAR
                                                                                                    ====================================== --}}
                                                                                                            <td>

                                                                                                                <div
                                                                                                                    class="details-width-list">

                                                                                                                    @forelse ($detail->sizes
                                                                                                                                                        as $size)
                                                                                                                        <span
                                                                                                                            class="details-width-badge">

                                                                                                                            {{ $size->width }}
                                                                                                                            {{ $size->unit }}

                                                                                                                        </span>

                                                                                                                    @empty

                                                                                                                        <span
                                                                                                                            class="details-empty">
                                                                                                                            -
                                                                                                                        </span>
                                                                                                                    @endforelse

                                                                                                                </div>

                                                                                                            </td>


                                                                                                            {{-- =====================================
                                                                                                        PANJANG
                                                                                                    ====================================== --}}
                                                                                                            <td>

                                                                                                                <div
                                                                                                                    class="details-width-list">

                                                                                                                    @forelse ($detail->sizes
                                                                                                                                                        as $size)
                                                                                                                        <span
                                                                                                                            class="details-width-badge">

                                                                                                                            @if ($size->length !== null)
                                                                                                                                {{ $size->length }}
                                                                                                                                {{ $size->unit }}
                                                                                                                            @else
                                                                                                                                -
                                                                                                                            @endif

                                                                                                                        </span>

                                                                                                                    @empty

                                                                                                                        <span
                                                                                                                            class="details-empty">
                                                                                                                            -
                                                                                                                        </span>
                                                                                                                    @endforelse

                                                                                                                </div>

                                                                                                            </td>


                                                                                                            {{-- =====================================
                                                                                                        VENDOR PRICE
                                                                                                    ====================================== --}}
                                                                                                            <td
                                                                                                                class="details-money">

                                                                                                                Rp{{ number_format($detail->vendor_price, 0, ',', '.') }}

                                                                                                            </td>


                                                                                                            {{-- =====================================
                                                                                                        GENERAL PRICE
                                                                                                    ====================================== --}}
                                                                                                            <td
                                                                                                                class="details-money details-price">

                                                                                                                Rp{{ number_format($detail->general_price, 0, ',', '.') }}

                                                                                                            </td>


                                                                                                            {{-- =====================================
                                                                                                        DIVISION PRICE
                                                                                                    ====================================== --}}
                                                                                                            <td
                                                                                                                class="details-money details-price">

                                                                                                                Rp{{ number_format($detail->division_price, 0, ',', '.') }}

                                                                                                            </td>


                                                                                                            {{-- =====================================
                                                                                                        PLAIN PRICE
                                                                                                    ====================================== --}}
                                                                                                            <td
                                                                                                                class="details-money details-price">

                                                                                                                Rp{{ number_format($detail->plain_price, 0, ',', '.') }}

                                                                                                            </td>

                                                                                                        </tr>
                                                                                                    @endforeach

                                                                                                </tbody>

                                                                                            </table>

                                                                                        </div>

                                                                                    </div>

                                                                                </div>

                                                                            </div>
                                                                        @endforeach

                                                                    </div>

                                                                </div>

                                                            </div>

                                                        </div>
                                                    @endforeach

                                                </div>


                                                {{-- =================================================
                                        NON-OUTSOURCING
                                    ================================================= --}}
                                            @else
                                                <div class="details-categories">

                                                    @foreach ($location->categories as $categoryIndex => $category)
                                                        <div class="details-category" data-category-accordion>

                                                            {{-- =====================================
                                                        CATEGORY HEADER
                                                    ====================================== --}}
                                                            <button type="button" class="details-category-header"
                                                                aria-expanded="false"
                                                                aria-controls="category-content-{{ $locationIndex }}-{{ $categoryIndex }}">

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


                                                            {{-- =====================================
                                                        CATEGORY CONTENT
                                                    ====================================== --}}
                                                            <div id="category-content-{{ $locationIndex }}-{{ $categoryIndex }}"
                                                                class="details-category-content">

                                                                <div class="details-category-inner">

                                                                    <div class="details-table-wrapper">

                                                                        <table class="details-table">

                                                                            <thead>

                                                                                <tr class="details-table-group-header">

                                                                                    <th rowspan="2">
                                                                                        Item
                                                                                    </th>

                                                                                    <th colspan="2">
                                                                                        Ukuran Tersedia
                                                                                    </th>

                                                                                    <th colspan="3">
                                                                                        Ongkos Produksi {{ $engine->name }}
                                                                                    </th>

                                                                                    <th rowspan="2">
                                                                                        Total Cost
                                                                                    </th>

                                                                                    <th rowspan="2">
                                                                                        Harga Umum
                                                                                    </th>

                                                                                    <th rowspan="2">
                                                                                        Harga Divisi
                                                                                    </th>

                                                                                    <th rowspan="2">
                                                                                        Harga Polos
                                                                                    </th>

                                                                                </tr>


                                                                                <tr class="details-table-sub-header">

                                                                                    <th>
                                                                                        Lebar
                                                                                    </th>

                                                                                    <th>
                                                                                        Panjang
                                                                                    </th>

                                                                                    <th>
                                                                                        Per Meter
                                                                                    </th>

                                                                                    <th>
                                                                                        Produksi
                                                                                    </th>

                                                                                    <th>
                                                                                        Finishing
                                                                                    </th>

                                                                                </tr>

                                                                            </thead>


                                                                            <tbody>

                                                                                @foreach ($category->details as $detail)
                                                                                    <tr>

                                                                                        {{-- =====================================
                                                                                    LAMINASI
                                                                                ====================================== --}}
                                                                                        <td class="details-material-cell">

                                                                                            <div
                                                                                                class="details-material-name">

                                                                                                {{ $detail->lamination_name }}

                                                                                            </div>

                                                                                        </td>


                                                                                        {{-- =====================================
                                                                                    LEBAR
                                                                                ====================================== --}}
                                                                                        <td>

                                                                                            <div
                                                                                                class="details-width-list">

                                                                                                @forelse ($detail->sizes
                                                                                                                                    as $size)
                                                                                                    <span
                                                                                                        class="details-width-badge">

                                                                                                        {{ $size->width }}
                                                                                                        {{ $size->unit }}

                                                                                                    </span>

                                                                                                @empty

                                                                                                    <span
                                                                                                        class="details-empty">
                                                                                                        -
                                                                                                    </span>
                                                                                                @endforelse

                                                                                            </div>

                                                                                        </td>


                                                                                        {{-- =====================================
                                                                                    PANJANG
                                                                                ====================================== --}}
                                                                                        <td>

                                                                                            <div
                                                                                                class="details-width-list">

                                                                                                @forelse ($detail->sizes
                                                                                                                                    as $size)
                                                                                                    <span
                                                                                                        class="details-width-badge">

                                                                                                        @if ($size->length !== null)
                                                                                                            {{ $size->length }}
                                                                                                            {{ $size->unit }}
                                                                                                        @else
                                                                                                            -
                                                                                                        @endif

                                                                                                    </span>

                                                                                                @empty

                                                                                                    <span
                                                                                                        class="details-empty">
                                                                                                        -
                                                                                                    </span>
                                                                                                @endforelse

                                                                                            </div>

                                                                                        </td>


                                                                                        {{-- =====================================
                                                                                    PRICE PER METER
                                                                                ====================================== --}}
                                                                                        <td class="details-money">

                                                                                            Rp{{ number_format($detail->price_per_meter, 0, ',', '.') }}

                                                                                        </td>


                                                                                        {{-- =====================================
                                                                                    PRODUCTION COST
                                                                                ====================================== --}}
                                                                                        <td class="details-money">

                                                                                            Rp{{ number_format($detail->production_cost, 0, ',', '.') }}

                                                                                        </td>


                                                                                        {{-- =====================================
                                                                                    FINISHING COST
                                                                                ====================================== --}}
                                                                                        <td class="details-money">

                                                                                            Rp{{ number_format($detail->finishing_cost, 0, ',', '.') }}

                                                                                        </td>


                                                                                        {{-- =====================================
                                                                                    TOTAL COST
                                                                                ====================================== --}}
                                                                                        <td
                                                                                            class="details-money details-total">

                                                                                            Rp{{ number_format($detail->total_cost, 0, ',', '.') }}

                                                                                        </td>


                                                                                        {{-- =====================================
                                                                                    GENERAL PRICE
                                                                                ====================================== --}}
                                                                                        <td
                                                                                            class="details-money details-price">

                                                                                            Rp{{ number_format($detail->general_price, 0, ',', '.') }}

                                                                                        </td>


                                                                                        {{-- =====================================
                                                                                    DIVISION PRICE
                                                                                ====================================== --}}
                                                                                        <td
                                                                                            class="details-money details-price">

                                                                                            Rp{{ number_format($detail->division_price, 0, ',', '.') }}

                                                                                        </td>


                                                                                        {{-- =====================================
                                                                                    PLAIN PRICE
                                                                                ====================================== --}}
                                                                                        <td
                                                                                            class="details-money details-price">

                                                                                            Rp{{ number_format($detail->plain_price, 0, ',', '.') }}

                                                                                        </td>

                                                                                    </tr>
                                                                                @endforeach

                                                                            </tbody>

                                                                        </table>

                                                                    </div>

                                                                </div>

                                                            </div>

                                                        </div>
                                                    @endforeach

                                                </div>
                                            @endif

                                        </div>

                                    </div>

                                </div>
                            @endforeach

                        </div>


                        {{-- =================================================
                    ACTION
                ================================================= --}}
                        <div class="form-actions">

                            <span class="spacer"></span>

                            <a href="{{ route('admin_pc_finishing_laminations') }}" class="btn btn--danger">

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

            /*
            |--------------------------------------------------------------------------
            | LOCATION ACCORDION
            |--------------------------------------------------------------------------
            */
            const locationAccordions =
                document.querySelectorAll(
                    '[data-accordion]'
                );

            locationAccordions.forEach(function(accordion) {

                const button =
                    accordion.querySelector(
                        '.details-location-header'
                    );

                if (!button) {
                    return;
                }

                button.addEventListener('click', function() {

                    const isOpen =
                        accordion.classList.contains(
                            'is-open'
                        );

                    accordion.classList.toggle(
                        'is-open',
                        !isOpen
                    );

                    button.setAttribute(
                        'aria-expanded',
                        String(!isOpen)
                    );

                });

            });


            /*
            |--------------------------------------------------------------------------
            | VENDOR ACCORDION
            |--------------------------------------------------------------------------
            */
            const vendorAccordions =
                document.querySelectorAll(
                    '[data-vendor-accordion]'
                );

            vendorAccordions.forEach(function(accordion) {

                const button =
                    accordion.querySelector(
                        '.details-vendor-header'
                    );

                if (!button) {
                    return;
                }

                button.addEventListener('click', function() {

                    const isOpen =
                        accordion.classList.contains(
                            'is-open'
                        );

                    accordion.classList.toggle(
                        'is-open',
                        !isOpen
                    );

                    button.setAttribute(
                        'aria-expanded',
                        String(!isOpen)
                    );

                });

            });


            /*
            |--------------------------------------------------------------------------
            | CATEGORY ACCORDION
            |--------------------------------------------------------------------------
            */
            const categoryAccordions =
                document.querySelectorAll(
                    '[data-category-accordion]'
                );

            categoryAccordions.forEach(function(accordion) {

                const button =
                    accordion.querySelector(
                        '.details-category-header'
                    );

                if (!button) {
                    return;
                }

                button.addEventListener('click', function() {

                    const isOpen =
                        accordion.classList.contains(
                            'is-open'
                        );

                    accordion.classList.toggle(
                        'is-open',
                        !isOpen
                    );

                    button.setAttribute(
                        'aria-expanded',
                        String(!isOpen)
                    );

                });

            });

        });
    </script>
@endsection

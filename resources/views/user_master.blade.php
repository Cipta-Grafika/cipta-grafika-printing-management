<!DOCTYPE html>
<html lang="en" x-data="app()" :class="{ 'dark': dark }" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Layanan percetakan dan estimasi harga dari Cipta Grafika.')">
    <meta name="author" content="Cipta Grafika">
    <meta property="og:title" content="@yield('meta_title', 'Cipta Grafika')">
    <meta property="og:description" content="@yield('meta_description', 'Layanan percetakan dan estimasi harga dari Cipta Grafika.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="@yield('meta_url', url()->current())">
    @hasSection('meta_image')
        <meta property="og:image" content="@yield('meta_image')">
    @endif
    <meta name="twitter:card" content="@hasSection('meta_image') summary_large_image @else summary @endif">
    <meta name="twitter:title" content="@yield('meta_title', 'Cipta Grafika')">
    <meta name="twitter:description" content="@yield('meta_description', 'Layanan percetakan dan estimasi harga dari Cipta Grafika.')">
    @hasSection('meta_image')
        <meta name="twitter:image" content="@yield('meta_image')">
    @endif
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DynaPuff:wght@400..700&display=swap" rel="stylesheet">
    <title>@yield('meta_title', 'Cipta Grafika')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=PT+Sans:ital,wght@0,400;0,700;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('vendor/select2/select2.min.css') }}">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        display: ['PT Sans', 'sans-serif'],
                        body: ['DM Sans', 'sans-serif']
                    },
                    colors: {
                        accent: '#c2a857',
                        'accent-light': '#FF8F5C'
                    }
                }
            }
        }
    </script>

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box
        }

        html,
        body {
            font-family: 'DM Sans', sans-serif
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6 {
            font-family: 'PT Sans', sans-serif
        }

        body {
            transition: background-color .3s, color .3s
        }

        /* noise overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            opacity: .35;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.05'/%3E%3C/svg%3E")
        }

        /* scrollbar */
        ::-webkit-scrollbar {
            width: 5px
        }

        ::-webkit-scrollbar-track {
            background: transparent
        }

        ::-webkit-scrollbar-thumb {
            background: #c2a857;
            border-radius: 99px
        }

        /* reveal on scroll */
        .reveal {
            opacity: 0;
            transform: translateY(26px);
            transition: opacity .6s cubic-bezier(.4, 0, .2, 1), transform .6s cubic-bezier(.4, 0, .2, 1)
        }

        .reveal.in {
            opacity: 1;
            transform: none
        }

        .d1 {
            transition-delay: .08s
        }

        .d2 {
            transition-delay: .16s
        }

        .d3 {
            transition-delay: .24s
        }

        .d4 {
            transition-delay: .32s
        }

        /* nav underline animation */
        .nl {
            position: relative
        }

        .nl::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1.5px;
            background: currentColor;
            transition: width .22s cubic-bezier(.4, 0, .2, 1)
        }

        .nl:hover::after,
        .nl.on::after {
            width: 100%
        }

        .nl.on {
            font-weight: 500
        }

        /* shimmer button */
        .shimmer {
            position: relative;
            overflow: hidden
        }

        .shimmer::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 60%;
            height: 100%;
            background: rgba(255, 255, 255, .18);
            transform: skewX(-20deg);
            transition: left .4s cubic-bezier(.4, 0, .2, 1)
        }

        .shimmer:hover::after {
            left: 160%
        }

        /* photo frames */
        .pf {
            overflow: hidden;
            background: #d4d4d8
        }

        .pf img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block
        }

        /* card hover */
        .card-h {
            transition: transform .28s cubic-bezier(.4, 0, .2, 1), border-color .18s
        }

        .card-h:hover {
            transform: translateY(-4px)
        }

        /* skill tag */
        .stag {
            transition: border-color .18s
        }

        [x-cloak] {
            display: none !important
        }

        /* .text-zinc-900 {
            color: #16294d !important;
        } */

        html:not(.dark) .text-zinc-900 {
            color: #16294d !important;
        }

        html:not(.dark) .bg-zinc-900 {
            background: #16294d !important;
        }

        html:not(.dark) .bg-zinc-800 {
            background: #182d53 !important;
        }

        .bg-zinc-800 {
            background: #274780 !important
        }

        html:not(.dark) input,
        html:not(.dark) textarea {
            border-color: #253f70 !important;
        }

        .border-zinc-700 {
            border-color: #2e4c84 !important;
        }

        .dark input::placeholder,
        .dark textarea::placeholder {
            color: #a1a1aa !important
        }

        .dark\:bg-zinc-900\/40:is(.dark *) {
            background: rgb(24 24 27 / 0.2) !important;
        }

        .dark\:border-zinc-700:is(.dark *) {
            border-color: #284376 !important;
        }

        html:not(.dark) input:focus,
        html:not(.dark) textarea:focus {
            border-color: #426098 !important;
        }

        .border-zinc-700:focus {
            border-color: #c2a857 !important;
        }

        .dark\:bg-zinc-700:is(.dark *) {
            background: #192f56 !important;
        }

        .dark\:bg-zinc-900:is(.dark *) {
            background: #192f56 !important;
            border-color: #1f3763 !important;
        }

        .dark\:border-zinc-800:is(.dark *):hover,
        .dark\:border-zinc-700:is(.dark *):hover {
            background: rgb(25, 47, 86) !important;
        }

        .dark\:bg-zinc-800:is(.dark *) {
            background: #234174 !important;
            border-color: #324e85 !important;
        }

        .dark\:bg-zinc-700:is(.dark *) {
            background: #27487f !important;
        }

        .dark\:border-zinc-800:is(.dark *) {
            border: .1px solid rgba(35, 60, 107, .2) !important;
        }

        .bg-accent {
            background-color: #c2a857 !important;
            transition: background-color 0.3s ease;
        }

        .bg-accent:hover {
            background-color: #9f873f !important;
        }

        .dark .bg-accent-2:hover {
            background: #1a3058 !important;
        }

        .bg-accent-2:hover {
            background: rgba(243, 243, 243, 0.3) !important
        }

        .bg-accent-2 {
            border: 1px solid #38558a !important;
            color: #16294d !important;
        }

        .dark .bg-accent-2 {
            color: #fff !important;
        }

        .dark\:bg-zinc-950:is(.dark *),
        .dark\:bg-zinc-950:is(.dark *) {
            background: #16294d !important;
        }

        .dark .bg-zinc-850 {
            background: #16294d !important;
            color-scheme: dark !important;
        }

        .dark\:border-zinc-900:is(.dark *) {
            border-top: 1px solid rgba(119, 143, 189, .2) !important;
        }

        .dark\:border-zinc-900:is(.dark *) {
            border-color: rgba(119, 143, 189, .2) !important;
        }

        .dark header.dark\:bg-zinc-950\/90 {
            background-color: rgba(15, 23, 42, 0.3) !important;
        }

        .bg-zinc-850 {
            background: transparent !important;
        }

        html:not(.dark) .bg-zinc-850 {
            color: #16294d !important;
        }

        /* Dark mode */
        html.dark select option {
            background-color: #27487f !important;
            color: #fff !important;
        }

        /* Light mode */
        html:not(.dark) select option {
            background-color: #fff !important;
            color: #16294d !important;
        }

        /* =========================
            THUMBNAIL HORIZONTAL SCROLL
        ========================== */

        .thumbnail-scroll {
            scrollbar-width: thin;

            /* Firefox */
            scrollbar-color: #d4a72c transparent;
        }


        /* =========================
            CHROME / EDGE / SAFARI
        ========================== */

        .thumbnail-scroll::-webkit-scrollbar {
            height: 5px;
        }


        /* TRACK */
        .thumbnail-scroll::-webkit-scrollbar-track {
            background: transparent;
            border-radius: 999px;
        }


        /* THUMB */
        .thumbnail-scroll::-webkit-scrollbar-thumb {
            background: #d4a72c;
            border-radius: 999px;
        }


        /* HOVER */
        .thumbnail-scroll::-webkit-scrollbar-thumb:hover {
            background: #c29720;
        }


        /* =========================
            HILANGKAN BUTTON PANAH
        ========================== */

        .thumbnail-scroll::-webkit-scrollbar-button {
            display: none;
            width: 0;
            height: 0;
        }


        .thumbnail-scroll::-webkit-scrollbar-button:start:decrement,
        .thumbnail-scroll::-webkit-scrollbar-button:end:increment {
            display: none;
        }


        /* Pastikan dark mode tetap orange */

        .dark .thumbnail-scroll {
            scrollbar-color: #d4a72c transparent;
        }


        .dark .thumbnail-scroll::-webkit-scrollbar-thumb {
            background: #d4a72c;
        }


        .dark .thumbnail-scroll::-webkit-scrollbar-thumb:hover {
            background: #c29720;
        }


        /* =========================
            MOBILE
        ========================== */

        @media (max-width: 640px) {

            .thumbnail-scroll::-webkit-scrollbar {
                height: 4px;
            }

        }

        .share-option {
            display: flex;
            align-items: center;
            gap: 0.875rem;

            width: 100%;

            padding: 0.75rem 1rem;

            border: 1px solid #e4e4e7;
            border-radius: 0.875rem;

            background: #ffffff;

            color: #27272a;

            font-size: 0.875rem;
            font-weight: 600;

            text-align: left;

            cursor: pointer;

            transition:
                background-color 0.2s ease,
                border-color 0.2s ease,
                transform 0.2s ease;
        }


        .share-option:hover {
            background: #fafafa;
            border-color: #d4d4d8;
            transform: translateY(-1px);
        }


        .dark .share-option {
            background: rgba(255, 255, 255, 0.03);

            border-color: rgba(255, 255, 255, 0.10);

            color: #ffffff;
        }


        .dark .share-option:hover {
            background: rgba(255, 255, 255, 0.07);

            border-color: rgba(255, 255, 255, 0.20);
        }


        /* =========================
            THUMBNAIL SCROLLBAR
        ========================== */

        .thumbnail-scroll {
            scrollbar-width: thin;
        }


        .thumbnail-scroll::-webkit-scrollbar {
            height: 3px;
        }


        .thumbnail-scroll::-webkit-scrollbar-track {
            background: transparent;
        }


        .thumbnail-scroll::-webkit-scrollbar-thumb {
            background: rgba(161, 161, 170, 0.45);
            border-radius: 999px;
        }


        .dark .thumbnail-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.20);
        }

        .bi-cart {
            color: #16294d;
        }

        .dark .bi-cart {
            color: #e5e7eb;
        }

        .bi-cart-plus {
            color: #c2a857 !important;
        }

        .pb-2 {
            padding-bottom: 1rem !important;
        }

        .thumbnail-scroll {
            scrollbar-color: #d4a72c !important;
        }

        .cart-button {
            border: 1px solid #c2a857 !important;
            color: #c2a857 !important;
        }

        .cart-button:hover {
            background: #fcfaf5;
        }

        :root {
            --est-gold: #c2a857;
            --est-gold-dark: #9f873f;
            --est-border: #253f70;
            --est-border-soft: rgba(37, 63, 112, .18);
            --est-field-bg: #ffffff;
            --est-field-text: #16294d;
            --est-muted: #6b7a99;
            --est-label: #44557a;
            --est-chevron: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2316294d' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        }

        html.dark {
            --est-border: #2e4c84;
            --est-border-soft: rgba(119, 143, 189, .22);
            --est-field-bg: #16294d;
            --est-field-text: #ffffff;
            --est-muted: #9fb0d0;
            --est-label: #b6c3de;
            --est-chevron: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23b6c3de' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        }


        /* ---------------------------------------------------------
        JUDUL ESTIMATOR
        --------------------------------------------------------- */

        .estimator>div:first-child>p:first-child {
            font-family: 'PT Sans', sans-serif;
            font-size: 1.375rem;
            font-weight: 700;
        }

        .estimator>div:first-child>p+p {
            margin-top: .25rem;
            font-size: .8125rem;
            letter-spacing: normal;
        }


        /* ---------------------------------------------------------
        JUDUL BAGIAN
        --------------------------------------------------------- */

        .estimator h4 {
            display: flex;
            align-items: center;
            gap: .625rem;
            font-size: .6875rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .estimator h4::before {
            content: '';
            width: .1875rem;
            height: 1rem;
            border-radius: 9999px;
            background: var(--est-gold);
        }


        /* ---------------------------------------------------------
        LABEL (termasuk label di komponen tambahan yang dibuat JS)
        --------------------------------------------------------- */

        .estimator label.block {
            margin-bottom: .5rem;
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .01em;
            color: var(--est-label);
        }

        .estimator label .text-red-500 {
            color: var(--est-gold-dark);
        }

        html.dark .estimator label .text-red-500 {
            color: var(--est-gold);
        }


        /* ---------------------------------------------------------
        FIELD: SELECT + INPUT (SATU TINGGI: 46px)
        --------------------------------------------------------- */

        .estimator select,
        .estimator input[type="number"],
        .estimator input[type="text"] {
            width: 100%;
            height: 2.875rem;
            padding: 0 1rem !important;
            border: 1px solid var(--est-border) !important;
            border-radius: .75rem !important;
            background-color: var(--est-field-bg) !important;
            color: var(--est-field-text) !important;
            font-size: .875rem;
            line-height: 1.25rem;
            transition: border-color .18s ease, box-shadow .18s ease, opacity .18s ease;
        }

        .estimator input[type="number"] {
            appearance: textfield;
            -moz-appearance: textfield;
        }

        .estimator input[type="number"]::-webkit-inner-spin-button,
        .estimator input[type="number"]::-webkit-outer-spin-button {
            margin: 0;
            -webkit-appearance: none;
        }

        html.dark .estimator select,
        html.dark .estimator input[type="number"],
        html.dark .estimator input[type="text"] {
            color-scheme: dark;
        }

        .estimator select:focus,
        .estimator input[type="number"]:focus,
        .estimator input[type="text"]:focus {
            outline: none;
            border-color: var(--est-gold) !important;
            box-shadow: 0 0 0 3px rgba(194, 168, 87, .22);
        }

        .estimator select:disabled,
        .estimator input[type="number"]:disabled,
        .estimator input[type="text"]:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .estimator input[readonly] {
            background-color: rgba(194, 168, 87, .10) !important;
            border-color: rgba(194, 168, 87, .35) !important;
            font-weight: 700;
            cursor: default;
        }

        .estimator input::placeholder {
            color: var(--est-muted) !important;
            opacity: .85;
        }

        /* hilangkan latar kuning/putih bawaan autofill browser */
        .estimator input:-webkit-autofill,
        .estimator input:-webkit-autofill:hover,
        .estimator input:-webkit-autofill:focus {
            -webkit-text-fill-color: var(--est-field-text);
            caret-color: var(--est-field-text);
            box-shadow: 0 0 0 1000px var(--est-field-bg) inset !important;
        }

        /* ruang untuk satuan di kanan (cm, %, Rp) */
        .estimator input.pr-12 {
            padding-right: 3rem !important;
        }

        .estimator #discount_value {
            padding-right: 3.5rem !important;
        }

        .estimator .relative>span.absolute,
        .estimator #discountSuffix {
            font-size: .75rem;
            font-weight: 600;
            color: var(--est-muted);
            pointer-events: none;
        }

        /* input custom lebar bahan / laminasi yang dibuat JS */
        .estimator input.form-control {
            margin-top: .5rem;
        }


        /* ---------------------------------------------------------
        SELECT NATIVE: PANAH KUSTOM
        --------------------------------------------------------- */

        .estimator select {
            appearance: none;
            -webkit-appearance: none;
            padding-right: 2.5rem !important;
            background-image: var(--est-chevron) !important;
            background-repeat: no-repeat !important;
            background-position: right 1rem center !important;
            background-size: 1rem !important;
        }


        /* ---------------------------------------------------------
        SELECT2: KOTAK PILIHAN (tinggi sama dengan field lain)
        --------------------------------------------------------- */

        .estimator .select2-container {
            width: 100% !important;
        }

        .estimator .select2-container--default .select2-selection--single {
            display: flex !important;
            align-items: center !important;
            height: 2.875rem !important;
            border: 1px solid var(--est-border) !important;
            border-radius: .75rem !important;
            background-color: var(--est-field-bg) !important;
            transition: border-color .18s ease, box-shadow .18s ease;
        }

        .estimator .select2-container--default .select2-selection--single .select2-selection__rendered {
            flex: 1;
            padding: 0 2.5rem 0 1rem !important;
            font-size: .875rem;
            line-height: 1.25rem !important;
            color: var(--est-field-text) !important;
        }

        .estimator .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: var(--est-muted) !important;
        }

        /* panah: gambar chevron yang sama dengan select native */
        .estimator .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 0 !important;
            right: 0 !important;
            width: 2.5rem !important;
            height: 100% !important;
            background-image: var(--est-chevron);
            background-repeat: no-repeat;
            background-position: center;
            background-size: 1rem;
        }

        .estimator .select2-container--default .select2-selection--single .select2-selection__arrow b {
            display: none !important;
        }

        .estimator .select2-container--default.select2-container--focus .select2-selection--single,
        .estimator .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--est-gold) !important;
            box-shadow: 0 0 0 3px rgba(194, 168, 87, .22);
        }


        /* ---------------------------------------------------------
            SELECT2: DROPDOWN
            Ditempel ke <body>, jadi tidak diberi awalan .estimator.
            --------------------------------------------------------- */

        .select2-dropdown {
            overflow: hidden;
            border: 1px solid var(--est-border) !important;
            border-radius: .75rem !important;
            background-color: var(--est-field-bg) !important;
            color: var(--est-field-text);
            box-shadow: 0 18px 40px -20px rgba(22, 41, 77, .55);
        }

        .select2-container--default .select2-search--dropdown {
            padding: .5rem;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            height: 2.25rem;
            padding: 0 .75rem;
            border: 1px solid var(--est-border) !important;
            border-radius: .5rem;
            background-color: var(--est-field-bg) !important;
            color: var(--est-field-text) !important;
            outline: none;
        }

        .select2-container--default .select2-results__option {
            padding: .625rem 1rem;
            font-size: .875rem;
            background-color: transparent !important;
            color: var(--est-field-text) !important;
        }

        .select2-container--default .select2-results__option[aria-selected=true],
        .select2-container--default .select2-results__option--selected {
            background-color: rgba(194, 168, 87, .12) !important;
            font-weight: 600;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected],
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: rgba(194, 168, 87, .26) !important;
            color: var(--est-field-text) !important;
        }


        /* ---------------------------------------------------------
            RADIO DISKON
        --------------------------------------------------------- */

        .estimator .discount-option-card {
            position: relative;
            display: flex;
            align-items: center;
            gap: .875rem;
            min-height: 3.75rem;
            padding: .5rem .875rem;
            border: 1px solid var(--est-border-soft);
            border-radius: .875rem;
            background: var(--est-field-bg);
            color: var(--est-muted);
            cursor: pointer;
            transition: border-color .18s ease, background-color .18s ease, color .18s ease;
        }

        .estimator .discount-option-card:hover {
            border-color: rgba(194, 168, 87, .65);
        }

        .estimator .discount-option-card:has(input[type="radio"]:checked) {
            border-color: var(--est-gold);
            background-color: rgba(194, 168, 87, .10);
            color: var(--est-gold-dark);
        }

        html.dark .estimator .discount-option-card:has(input[type="radio"]:checked) {
            color: var(--est-gold);
        }

        .estimator .discount-option-card:focus-within {
            border-color: var(--est-gold);
        }

        .estimator .discount-option-card input[type="radio"] {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .estimator .discount-option-icon {
            display: inline-flex;
            flex: 0 0 2.5rem;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: .625rem;
            background: var(--est-border-soft);
            color: var(--est-muted);
            font-size: 1.125rem;
            transition: background-color .18s ease, color .18s ease;
        }

        .estimator .discount-option-card:has(input[type="radio"]:checked) .discount-option-icon {
            background: var(--est-gold);
            color: #fff;
        }

        .estimator .discount-option-title {
            color: var(--est-field-text);
            font-size: .875rem;
        }

        .estimator .discount-option-card:has(input[type="radio"]:checked) .discount-option-title {
            color: inherit;
        }

        .estimator #discountValueField {
            animation: estimatorDiscountIn .18s ease;
        }

        @keyframes estimatorDiscountIn {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        /* ---------------------------------------------------------
        KOMPONEN TAMBAHAN
        (markup dari JS memakai class gray-*, ditimpa di sini)
        --------------------------------------------------------- */

        .estimator #addAdditionalComponentButton {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2rem;
            padding: .375rem .875rem;
            border: 1px dashed var(--est-gold);
            border-radius: 9999px;
            font-size: .75rem;
            font-weight: 600;
            color: var(--est-gold-dark);
            transition: background-color .18s ease;
        }

        html.dark .estimator #addAdditionalComponentButton {
            color: var(--est-gold);
        }

        .estimator #addAdditionalComponentButton:hover {
            background-color: rgba(194, 168, 87, .12);
            text-decoration: none;
        }

        .estimator .additional-component-item {
            position: relative;
            padding: 1.25rem !important;
            border: 1px solid var(--est-border-soft) !important;
            border-radius: 1rem !important;
            background: var(--est-field-bg) !important;
            transition: border-color .18s ease, box-shadow .18s ease;
        }

        .estimator .additional-component-item:hover {
            border-color: rgba(194, 168, 87, .35) !important;
            box-shadow: 0 10px 30px -24px rgba(22, 41, 77, .6);
        }

        .estimator .additional-component-item .text-gray-800 {
            color: var(--est-field-text);
        }

        .estimator .additional-component-item small {
            display: block;
            margin-top: .375rem;
            font-size: .6875rem;
            line-height: 1.1rem;
            color: var(--est-muted);
        }

        .estimator .btn-remove-additional-component {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: .625rem;
            color: var(--est-muted);
            transition: background-color .18s ease;
        }

        .estimator .btn-remove-additional-component:hover {
            background-color: var(--est-border-soft);
            color: var(--est-field-text);
        }


        /* ---------------------------------------------------------
        HARGA MATERIAL
        --------------------------------------------------------- */

        .estimator #materialPrice {
            display: inline-flex;
            align-items: center;
            min-height: 2.5rem;
            padding: .5rem .875rem;
            border: 1px solid rgba(194, 168, 87, .25);
            border-radius: .75rem;
            background-color: rgba(194, 168, 87, .08);
            color: var(--est-field-text);
        }


        /* ---------------------------------------------------------
        PANEL ESTIMASI HARGA
        --------------------------------------------------------- */

        .estimator .estimator-summary {
            border: 1px solid var(--est-border-soft) !important;
            border-radius: 1.25rem !important;
            background:
                linear-gradient(180deg, rgba(194, 168, 87, .10), rgba(194, 168, 87, 0) 42%),
                var(--est-field-bg) !important;
            box-shadow: 0 18px 40px -26px rgba(22, 41, 77, .45);
        }

        .estimator .estimator-summary .text-2xl {
            color: var(--est-gold-dark) !important;
            letter-spacing: -.01em;
        }

        html.dark .estimator .estimator-summary .text-2xl {
            color: var(--est-gold) !important;
        }

        /* tombol keranjang: hilangkan kilat putih saat hover di mode gelap */
        html.dark .estimator .cart-button:hover {
            background: rgba(194, 168, 87, .12) !important;
        }

        @media (min-width: 1024px) {

            .estimator .estimator-summary {
                position: sticky;
                top: 6.5rem;
            }
        }


        /* ---------------------------------------------------------
        MOBILE
        --------------------------------------------------------- */

        @media (max-width: 640px) {

            .estimator .additional-component-item {
                padding: 1rem !important;
            }

            .estimator #addAdditionalComponentButton {
                padding: .375rem .625rem;
                font-size: .6875rem;
            }

            .estimator .discount-option-card {
                width: 100%;
            }
        }
    </style>
</head>

<body class="bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased">

    <!-- ═══ NAV ═══ -->
    <header class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
        :class="sc ? 'bg-white/90 dark:bg-zinc-950/90 backdrop-blur-md shadow-sm shadow-black/5' : ''">
        <nav class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between" aria-label="Main navigation">
            @if (request()->routeIs('user_home'))
                <a href="#hero" class="relative z-10 inline-flex shrink-0 items-center" aria-label="Cipta Grafika">
                    <img :src="dark
                        ?
                        '{{ asset('images/user-logo-dark.png') }}' :
                        '{{ asset('images/user-logo-light.png') }}'"
                        alt="Cipta Grafika" class="h-auto w-[40px] sm:w-[55px]">
                </a>

                <ul class="hidden md:flex items-center gap-8 text-sm" role="list">
                    <li><a href="#products"
                            class="nl text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors"
                            :class="s === 'products' ? 'on !text-zinc-900 dark:!text-white' : ''">Produk</a></li>
                    <li><a href="#blog"
                            class="nl text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors"
                            :class="s === 'blog' ? 'on !text-zinc-900 dark:!text-white' : ''">Pertanyaan Umum</a>
                    </li>
                </ul>

                <div class="flex items-center gap-3">
                    <!-- CART -->
                    <a href="{{ route('user_baskets') }}"
                        class="relative
                  w-9 h-9
                  flex items-center justify-center
                  rounded-full
                  border border-zinc-200
                  dark:border-zinc-800
                  text-zinc-700
                  dark:text-zinc-300
                  hover:bg-zinc-100
                  dark:hover:bg-zinc-900
                  hover:text-zinc-900
                  dark:hover:text-white
                  transition-colors"
                        aria-label="Keranjang">

                        <i class="bi bi-cart"></i>

                    </a>

                    <!-- dark toggle -->
                    <button @click="dark=!dark"
                        class="w-9 h-9 flex items-center justify-center rounded-full border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-900 transition-colors"
                        :aria-label="dark ? 'Light mode' : 'Dark mode'">
                        <svg x-show="!dark" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                        </svg>
                        <svg x-show="dark" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                    </button>
                    <!-- hire me -->
                    <a href="https://wa.me/6282213290760" target="_blank"
                        class="hidden md:inline-flex items-center gap-2 shimmer bg-accent text-white text-sm font-medium px-5 py-2 rounded-full hover:bg-accent-light transition-colors">
                        <i class="bi bi-whatsapp"></i> Chat Admin
                    </a>
                    <!-- hamburger -->
                    <button @click="mm=!mm"
                        class="md:hidden w-9 h-9 flex items-center justify-center rounded-full border border-zinc-200 dark:border-zinc-800"
                        :aria-expanded="mm" aria-label="Toggle menu">
                        <svg x-show="!mm" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="mm" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @else
                <a href="{{ route('user_home') }}" class="relative z-10 inline-flex shrink-0 items-center"
                    aria-label="Cipta Grafika">
                    <img :src="dark
                        ?
                        '{{ asset('images/user-logo-dark.png') }}' :
                        '{{ asset('images/user-logo-light.png') }}'"
                        alt="Cipta Grafika" class="h-auto w-[40px] sm:w-[55px]">
                </a>

                <ul class="hidden md:flex items-center gap-8 text-sm font-medium" role="list">
                    <li><a href="{{ route('user_products') }}" class="nav-link text-accent font-medium">Produk</a>
                    </li>
                    <li><a href="{{ route('user_home') }}#blog"
                            class="nav-link text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors">Pertanyaan
                            Umum</a></li>
                </ul>
                <div class="flex items-center gap-3">
                    <!-- CART -->
                    <a href="{{ route('user_baskets') }}"
                        class="relative
                  w-9 h-9
                  flex items-center justify-center
                  rounded-full
                  border border-zinc-200
                  dark:border-zinc-800
                  text-zinc-700
                  dark:text-zinc-300
                  hover:bg-zinc-100
                  dark:hover:bg-zinc-800
                  hover:text-zinc-900
                  dark:hover:text-white
                  transition-colors"
                        aria-label="Keranjang">

                        <i class="bi bi-cart"></i>

                    </a>

                    <button @click="dark=!dark"
                        class="w-9 h-9 flex items-center justify-center rounded-full border border-zinc-200 dark:border-zinc-800 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
                        :aria-label="dark ? 'Light mode' : 'Dark mode'">
                        <svg x-show="!dark" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                        </svg>
                        <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                    </button>
                    <a href="https://wa.me/6282213290760" target="_blank"
                        class="hidden md:inline-flex items-center gap-2 btn-primary bg-accent text-white text-sm font-medium px-5 py-2 rounded-full hover:bg-accent-light transition-colors"><i
                            class="bi bi-whatsapp"></i> Chat Admin</a>
                    <button @click="mm=!mm"
                        class="md:hidden w-9 h-9 flex items-center justify-center rounded-full border border-zinc-200 dark:border-zinc-800"
                        aria-label="Toggle menu">

                        <svg x-show="!mm" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>

                        <svg x-show="mm" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif
        </nav>

        @if (request()->routeIs('user_home'))
            <!-- mobile menu -->
            <div x-show="mm" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                class="md:hidden bg-white dark:bg-zinc-950 border-t border-zinc-100 dark:border-zinc-900">
                <ul class="flex flex-col px-6 py-5 gap-4 text-sm font-medium" role="list">
                    <li><a href="#products" @click="mm=false"
                            class="block text-zinc-700 dark:text-zinc-300 hover:text-accent transition-colors">Produk</a>
                    </li>
                    <li><a href="#blog" @click="mm=false"
                            class="block text-zinc-700 dark:text-zinc-300 hover:text-accent transition-colors">Pertanyaan
                            Umum</a></li>
                    <li class="pt-2 border-t border-zinc-100 dark:border-zinc-900">
                        <a href="https://wa.me/6282213290760" target="_blank" @click="mm=false"
                            class="inline-flex shimmer bg-accent text-white font-medium text-sm px-5 py-2.5 rounded-full"><i
                                class="bi bi-whatsapp" style="margin-right: 10px;"></i> Chat Admin</a>
                    </li>
                </ul>
            </div>
        @else
            <!-- mobile menu -->
            <div x-show="mm" x-cloak x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                class="md:hidden bg-white dark:bg-zinc-950 border-t border-zinc-100 dark:border-zinc-900">
                <ul class="flex flex-col px-6 py-5 gap-4 text-sm font-medium" role="list">
                    <li><a href="{{ route('user_home') }}#products" @click="mm=false"
                            class="block text-zinc-700 dark:text-zinc-300 hover:text-accent transition-colors">Produk</a>
                    </li>
                    <li><a href="{{ route('user_home') }}#blog" @click="mm=false"
                            class="block text-zinc-700 dark:text-zinc-300 hover:text-accent transition-colors">Pertanyaan
                            Umum</a></li>
                    <li class="pt-2 border-t border-zinc-100 dark:border-zinc-900">
                        <a href="https://wa.me/6282213290760" target="_blank" @click="mm=false"
                            class="inline-flex shimmer bg-accent text-white font-medium text-sm px-5 py-2.5 rounded-full"><i
                                class="bi bi-whatsapp" style="margin-right: 10px;"></i> Chat Admin</a>
                    </li>
                </ul>
            </div>
        @endif
    </header>

    <main>

        @yield('contents')

    </main>

    <footer class="border-t border-zinc-100 dark:border-zinc-900">
        <div class="max-w-6xl mx-auto px-6 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-zinc-400">© <span id="yr"></span> Cipta Grafika. All rights reserved. <br>
                Developed by <span class="font-bold">Agus
                    Faisal</span></p>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('vendor/select2/select2.min.js') }}"></script>
    <script>
        function app() {
            return {
                dark: false,
                mm: false,
                sc: false,
                s: 'hero',

                init() {
                    // dark mode
                    this.dark = localStorage.getItem('theme') === 'dark' ||
                        (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches);
                    this.$watch('dark', v => localStorage.setItem('theme', v ? 'dark' : 'light'));

                    // scroll
                    window.addEventListener('scroll', () => {
                        this.sc = window.scrollY > 20;
                        this.updateSection();
                    }, {
                        passive: true
                    });

                    // reveal
                    const io = new IntersectionObserver(entries => {
                        entries.forEach(e => {
                            if (e.isIntersecting) {
                                e.target.classList.add('in');
                                io.unobserve(e.target);
                            }
                        });
                    }, {
                        threshold: 0.1,
                        rootMargin: '0px 0px -40px 0px'
                    });
                    document.querySelectorAll('.reveal').forEach(el => io.observe(el));

                    // year
                    document.getElementById('yr').textContent = new Date().getFullYear();
                },

                updateSection() {
                    const atBottom = (window.innerHeight + window.scrollY) >= document.body.scrollHeight - 60;
                    if (atBottom) {
                        this.s = 'contact';
                        return;
                    }
                    const ids = ['contact', 'blog', 'reviews', 'about', 'products', 'services', 'hero'];
                    for (const id of ids) {
                        const el = document.getElementById(id);
                        if (el && window.scrollY >= el.offsetTop - 130) {
                            this.s = id;
                            return;
                        }
                    }
                }
            }
        }

        document.querySelectorAll('.faq-toggle').forEach(button => {

            button.addEventListener('click', function() {

                const currentArticle = this.closest('article');
                const currentContent = currentArticle.querySelector('.faq-content');
                const currentIcon = currentArticle.querySelector('.faq-icon');

                // Tutup FAQ lain
                document.querySelectorAll('.faq-content').forEach(content => {
                    if (content !== currentContent) {
                        content.classList.remove('grid-rows-[1fr]');
                        content.classList.add('grid-rows-[0fr]');
                    }
                });

                document.querySelectorAll('.faq-icon').forEach(icon => {
                    if (icon !== currentIcon) {
                        icon.textContent = '+';
                        icon.classList.remove('rotate-180');
                    }
                });

                // Toggle FAQ yang diklik
                const isOpen = currentContent.classList.contains('grid-rows-[1fr]');

                if (isOpen) {
                    currentContent.classList.remove('grid-rows-[1fr]');
                    currentContent.classList.add('grid-rows-[0fr]');

                    currentIcon.textContent = '+';
                    currentIcon.classList.remove('rotate-180');

                } else {
                    currentContent.classList.remove('grid-rows-[0fr]');
                    currentContent.classList.add('grid-rows-[1fr]');

                    currentIcon.textContent = '−';
                    currentIcon.classList.add('rotate-180');
                }

            });

        });

        // function products() {

        //     return {

        //         /* =========================
        //           GLOBAL
        //         ========================== */

        //         dark: false,

        //         scrolled: false,

        //         mobileMenu: false,

        //         /* =========================
        //           BASKET / CART
        //         ========================= */

        //         baskets: [],

        //         /* =========================
        //           LOAD BASKETS
        //         ========================= */

        //         loadBaskets() {

        //             const savedBaskets = localStorage.getItem('baskets');

        //             if (savedBaskets) {

        //                 try {

        //                     this.baskets = JSON.parse(savedBaskets);

        //                 } catch (error) {

        //                     console.error(
        //                         'Gagal membaca basket:',
        //                         error
        //                     );

        //                     this.baskets = [];

        //                 }

        //             }

        //         },


        //         /* =========================
        //           SAVE BASKET
        //         ========================= */

        //         saveBaskets() {

        //             localStorage.setItem(
        //                 'baskets',
        //                 JSON.stringify(this.baskets)
        //             );

        //         },

        //         /* =========================
        //           TAMBAH KE KERANJANG
        //         ========================= */

        //         addToBasket(product) {

        //             const existingItem = this.baskets.find(
        //                 item => item.id === product.id
        //             );


        //             /* JIKA SUDAH ADA */

        //             if (existingItem) {

        //                 console.log(
        //                     'Produk sudah ada di keranjang'
        //                 );

        //                 return;

        //             }


        //             /* TAMBAHKAN PRODUK */

        //             this.baskets.push({

        //                 ...product,

        //                 quantity: 1

        //             });


        //             /* SIMPAN KE LOCAL STORAGE */

        //             this.saveBaskets();


        //             console.log(
        //                 'Basket berhasil disimpan:',
        //                 this.baskets
        //             );

        //         },


        //         /* =========================
        //           HAPUS DARI KERANJANG
        //         ========================= */

        //         removeFromBasket(productId) {

        //             this.baskets = this.baskets.filter(
        //                 item => item.id !== productId
        //             );


        //             this.saveBaskets();

        //         },


        //         /* =========================
        //           CEK PRODUK ADA DI KERANJANG
        //         ========================= */

        //         isInBasket(productId) {

        //             return this.baskets.some(
        //                 item => item.id === productId
        //             );

        //         },

        //         /* =========================
        //           FILTER
        //         ========================== */

        //         filterOpen: false,

        //         printType: 'all',

        //         selectedCategories: [],

        //         search: '',

        //         engines: [],

        //         /* =========================
        //           CATEGORY OPTIONS
        //         ========================== */

        //         categories: [],

        //         /* DITAMBAHKAN: penanda loading untuk teks "Memuat filter..." */
        //         filtersLoading: true,

        //         loadFilters() {
        //             fetch("{{ route('user_products_filters') }}", {
        //                     headers: {
        //                         'Accept': 'application/json'
        //                     }
        //                 })
        //                 .then(response => response.json())
        //                 .then(result => {
        //                     if (!result.success) {
        //                         return;
        //                     }

        //                     /*
        //                     | ID dijadikan string supaya seragam dengan nilai
        //                     | input radio dan checkbox.
        //                     */

        //                     this.engines = result.data.engines.map(engine => ({
        //                         id: String(engine.id),
        //                         name: engine.name
        //                     }));

        //                     this.categories = result.data.categories.map(category => ({
        //                         id: String(category.id),
        //                         name: category.name
        //                     }));
        //                 })
        //                 .catch(error => {
        //                     console.error('Gagal mengambil data filter:', error);
        //                 })
        //                 .finally(() => {
        //                     this.filtersLoading = false;
        //                 });
        //         },

        //         /* =========================
        //           PRODUCT DATA
        //         ========================== */

        //         allProducts: [{
        //                 id: 1,

        //                 title: 'Brosur',

        //                 category: 'Brosur',

        //                 printType: 'a3',

        //                 printTypeLabel: 'Digital Print A3+',

        //                 desc: 'Cetak brosur berkualitas untuk kebutuhan promosi, informasi, dan komunikasi bisnis.',

        //                 img: '{{ asset('images/example-15.jpg') }}',

        //                 url: '{{ route('user_product_details') }}'
        //             },
        //             {
        //                 id: 2,

        //                 title: 'Flyer',

        //                 category: 'Flyer',

        //                 printType: 'a3',

        //                 printTypeLabel: 'Digital Print A3+',

        //                 desc: 'Media promosi praktis dengan hasil cetak berkualitas untuk berbagai kebutuhan bisnis.',

        //                 img: '{{ asset('images/example-15.jpg') }}',

        //                 url: '{{ route('user_product_details') }}'
        //             },
        //             {
        //                 id: 3,

        //                 title: 'Poster',

        //                 category: 'Poster',

        //                 printType: 'a3',

        //                 printTypeLabel: 'Digital Print A3+',

        //                 desc: 'Cetak poster berkualitas untuk promosi, informasi, event, dan kebutuhan komunikasi visual.',

        //                 img: '{{ asset('images/example-9.jpg') }}',

        //                 url: '{{ route('user_product_details') }}'
        //             },
        //             {
        //                 id: 4,

        //                 title: 'Kartu Nama',

        //                 category: 'Kartu Nama',

        //                 printType: 'a3',

        //                 printTypeLabel: 'Digital Print A3+',

        //                 desc: 'Kartu nama profesional untuk memperkuat identitas personal maupun bisnis Anda.',

        //                 img: '{{ asset('images/example-9.jpg') }}',

        //                 url: '{{ route('user_product_details') }}'
        //             },
        //             {
        //                 id: 5,

        //                 title: 'Sticker',

        //                 category: 'Sticker',

        //                 printType: 'a3',

        //                 printTypeLabel: 'Digital Print A3+',

        //                 desc: 'Berbagai pilihan cetak sticker untuk kebutuhan promosi, branding, dan identitas produk.',

        //                 img: '{{ asset('images/example-9.jpg') }}',

        //                 url: '{{ route('user_product_details') }}'
        //             },
        //             {
        //                 id: 6,

        //                 title: 'Banner',

        //                 category: 'Banner',

        //                 printType: 'large-format',

        //                 printTypeLabel: 'Large Format',

        //                 desc: 'Solusi cetak banner berukuran besar untuk kebutuhan promosi indoor maupun outdoor.',

        //                 img: '{{ asset('images/example-8.jpg') }}',

        //                 url: '{{ route('user_product_details') }}'
        //             },
        //             {
        //                 id: 7,

        //                 title: 'Backdrop',

        //                 category: 'Backdrop',

        //                 printType: 'large-format',

        //                 printTypeLabel: 'Large Format',

        //                 desc: 'Cetak backdrop berkualitas untuk event, pameran, promosi, dan berbagai kegiatan.',

        //                 img: '{{ asset('images/example-8.jpg') }}',

        //                 url: '{{ route('user_product_details') }}'
        //             },
        //             {
        //                 id: 8,

        //                 title: 'X-Banner',

        //                 category: 'X-Banner',

        //                 printType: 'large-format',

        //                 printTypeLabel: 'Large Format',

        //                 desc: 'Media promosi praktis yang mudah dipasang dan cocok untuk berbagai kebutuhan promosi.',

        //                 img: '{{ asset('images/example-8.jpg') }}',

        //                 url: '{{ route('user_product_details') }}'
        //             }
        //         ],

        //         get visibleProducts() {

        //             return this.allProducts.filter(project => {

        //                 /* =========================
        //                   FILTER JENIS PENCETAKAN
        //                 ========================== */

        //                 const matchPrintType =
        //                     this.printType === 'all' ||
        //                     String(project.engine_id) === this.printType;

        //                 /* =========================
        //                   FILTER KATEGORI
        //                 ========================== */

        //                 const matchCategory =
        //                     this.selectedCategories.length === 0 ||
        //                     this.selectedCategories.includes(String(project.category_id));


        //                 /* =========================
        //                   FILTER SEARCH
        //                 ========================== */

        //                 const search = this.search.toLowerCase().trim();

        //                 const matchSearch =
        //                     search === '' ||
        //                     project.title.toLowerCase().includes(search) ||
        //                     project.category.toLowerCase().includes(search) ||
        //                     project.desc.toLowerCase().includes(search);


        //                 /* =========================
        //                   HASIL FILTER
        //                 ========================== */
        //                 return (
        //                     matchPrintType &&
        //                     matchCategory &&
        //                     matchSearch
        //                 );
        //             });
        //         },

        //         resetFilter() {
        //             this.printType = 'all';
        //             this.selectedCategories = [];
        //             this.search = '';
        //         },


        //         /* =========================
        //           INITIALIZATION
        //         ========================== */

        //         init() {

        //             /* =========================
        //               LOAD BASKET
        //             ========================= */

        //             this.loadBaskets();

        //             /* =========================
        //               LOAD FILTERS
        //             ========================= */

        //             this.loadFilters();

        //             /* DARK MODE */

        //             this.dark =

        //                 localStorage.getItem('theme') === 'dark'

        //                 ||

        //                 (

        //                     !localStorage.getItem('theme')

        //                     &&

        //                     window.matchMedia(
        //                         '(prefers-color-scheme: dark)'
        //                     ).matches

        //                 );


        //             /* SCROLL */

        //             window.addEventListener(
        //                 'scroll',
        //                 () => {

        //                     this.scrolled = window.scrollY > 20;

        //                 }, {
        //                     passive: true
        //                 }
        //             );


        //             /* REVEAL ANIMATION */

        //             this.$nextTick(() => {

        //                 const obs = new IntersectionObserver(

        //                     entries => {
        //                         entries.forEach(e => {
        //                             if (e.isIntersecting) {

        //                                 e.target.classList.add('visible');
        //                                 obs.unobserve(e.target);

        //                             }
        //                         });
        //                     }, {
        //                         threshold: 0.08,
        //                         rootMargin: '0px 0px -30px 0px'
        //                     }
        //                 );

        //                 document
        //                     .querySelectorAll('.reveal')
        //                     .forEach(el => obs.observe(el));
        //             });
        //         }
        //     }
        // }

        function products() {

            return {

                /* =========================
                  GLOBAL
                ========================== */

                dark: false,

                scrolled: false,

                mobileMenu: false,

                /* =========================
                  BASKET / CART
                ========================= */

                baskets: [],

                /* =========================
                  LOAD BASKETS
                ========================= */

                loadBaskets() {

                    const savedBaskets = localStorage.getItem('baskets');

                    if (savedBaskets) {

                        try {

                            this.baskets = JSON.parse(savedBaskets);
                            const savedItemCount = this.baskets.length;
                            this.baskets = this.baskets.filter(item => !(
                                item.id === 4 &&
                                item.title === 'Kartu Nama' &&
                                item.category === 'Kartu Nama' &&
                                item.printType === 'a3' &&
                                item.printTypeLabel === 'Digital Print A3+'
                            ));

                            if (this.baskets.length !== savedItemCount) {
                                this.saveBaskets();
                            }

                        } catch (error) {

                            console.error(
                                'Gagal membaca basket:',
                                error
                            );

                            this.baskets = [];

                        }

                    }

                },


                /* =========================
                  SAVE BASKET
                ========================= */

                saveBaskets() {

                    localStorage.setItem(
                        'baskets',
                        JSON.stringify(this.baskets)
                    );

                },

                /* =========================
                  TAMBAH KE KERANJANG
                ========================= */

                addToBasket(product) {

                    const existingItem = this.baskets.find(
                        item => item.id === product.id
                    );


                    /* JIKA SUDAH ADA */

                    if (existingItem) {

                        console.log(
                            'Produk sudah ada di keranjang'
                        );

                        return;

                    }


                    /* TAMBAHKAN PRODUK */

                    this.baskets.push({

                        ...product,

                        quantity: 1

                    });


                    /* SIMPAN KE LOCAL STORAGE */

                    this.saveBaskets();


                    console.log(
                        'Basket berhasil disimpan:',
                        this.baskets
                    );

                },


                /* =========================
                  HAPUS DARI KERANJANG
                ========================= */

                removeFromBasket(productId) {

                    this.baskets = this.baskets.filter(
                        item => item.id !== productId
                    );


                    this.saveBaskets();

                },


                /* =========================
                  CEK PRODUK ADA DI KERANJANG
                ========================= */

                isInBasket(productId) {

                    return this.baskets.some(
                        item => item.id === productId
                    );

                },

                /* =========================
                  FILTER
                ========================== */

                filterOpen: false,

                printType: 'all',

                selectedCategories: [],

                search: '',

                engines: [],

                categories: [],

                filtersLoading: true,

                loadFilters() {
                    fetch("{{ route('user_products_filters') }}", {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (!result.success) {
                                return;
                            }

                            /*
                            | ID dijadikan string supaya seragam dengan nilai
                            | input radio dan checkbox.
                            */

                            this.engines = result.data.engines.map(engine => ({
                                id: String(engine.id),
                                name: engine.name
                            }));

                            this.categories = result.data.categories.map(category => ({
                                id: String(category.id),
                                name: category.name
                            }));
                        })
                        .catch(error => {
                            console.error('Gagal mengambil data filter:', error);
                        })
                        .finally(() => {
                            this.filtersLoading = false;
                        });
                },

                /* =========================
                  PRODUCT DATA (BARU: DARI DATABASE)
                ========================== */

                allProducts: [],

                productsLoading: true,

                productsError: false,

                loadProducts() {
                    fetch("{{ route('user_products_list') }}", {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(result => {
                            if (!result.success) {
                                this.productsError = true;

                                return;
                            }

                            this.allProducts = result.data;
                        })
                        .catch(error => {
                            console.error('Gagal mengambil data produk:', error);

                            this.productsError = true;
                        })
                        .finally(() => {
                            this.productsLoading = false;
                        });
                },

                get visibleProducts() {

                    return this.allProducts.filter(project => {

                        /* =========================
                          FILTER JENIS PENCETAKAN
                          Display tidak punya mesin, jadi hanya
                          tampil saat "Semua" dipilih.
                        ========================== */

                        const matchPrintType =
                            this.printType === 'all' ||
                            (
                                project.engine_id !== null &&
                                String(project.engine_id) === this.printType
                            );

                        /* =========================
                          FILTER KATEGORI
                        ========================== */

                        const matchCategory =
                            this.selectedCategories.length === 0 ||
                            this.selectedCategories.includes(String(project.category_id));


                        /* =========================
                          FILTER SEARCH
                          (judul, kategori, nama mesin)
                        ========================== */

                        const search = this.search.toLowerCase().trim();

                        const matchSearch =
                            search === '' ||
                            project.title.toLowerCase().includes(search) ||
                            project.category_name.toLowerCase().includes(search) ||
                            (project.engine_name ?? '').toLowerCase().includes(search);


                        /* =========================
                          HASIL FILTER
                        ========================== */
                        return (
                            matchPrintType &&
                            matchCategory &&
                            matchSearch
                        );
                    });
                },

                resetFilter() {
                    this.printType = 'all';
                    this.selectedCategories = [];
                    this.search = '';
                },


                /* =========================
                  INITIALIZATION
                ========================== */

                init() {

                    /* =========================
                      LOAD BASKET
                    ========================= */

                    this.loadBaskets();

                    /* =========================
                      LOAD FILTERS
                    ========================= */

                    this.loadFilters();

                    /* =========================
                      LOAD PRODUCTS (BARU)
                    ========================= */

                    this.loadProducts();

                    /* DARK MODE */

                    this.dark =

                        localStorage.getItem('theme') === 'dark'

                        ||

                        (

                            !localStorage.getItem('theme')

                            &&

                            window.matchMedia(
                                '(prefers-color-scheme: dark)'
                            ).matches

                        );


                    /* SCROLL */

                    window.addEventListener(
                        'scroll',
                        () => {

                            this.scrolled = window.scrollY > 20;

                        }, {
                            passive: true
                        }
                    );


                    /* REVEAL ANIMATION */

                    this.$nextTick(() => {

                        const obs = new IntersectionObserver(

                            entries => {
                                entries.forEach(e => {
                                    if (e.isIntersecting) {

                                        e.target.classList.add('visible');
                                        obs.unobserve(e.target);

                                    }
                                });
                            }, {
                                threshold: 0.08,
                                rootMargin: '0px 0px -30px 0px'
                            }
                        );

                        document
                            .querySelectorAll('.reveal')
                            .forEach(el => obs.observe(el));
                    });
                }
            }
        }
    </script>

    @yield('scripts')
</body>

</html>

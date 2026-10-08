<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cipta Grafika Estimator System</title>
    <script>
        ! function() {
            try {
                var t = localStorage.getItem("dash26-theme"),
                    e = window.matchMedia("(prefers-color-scheme: dark)").matches;
                document.documentElement.setAttribute("data-theme", t || (e ? "dark" : "light"))
            } catch (t) {
                document.documentElement.setAttribute("data-theme", "light")
            }
        }()
    </script>

    <script defer="defer" src="{{ asset('js/runtime.js') }}"></script>
    <script defer="defer" src="{{ asset('js/vendor-fullcalendar.js') }}"></script>
    <script defer="defer" src="{{ asset('js/vendor-chartjs.js') }}"></script>
    <script defer="defer" src="{{ asset('js/vendors.js') }}"></script>
    <script defer="defer" src="{{ asset('js/2026.js') }}"></script>
    <script defer="defer" src="{{ asset('js/sidebar-collapse.js') }}"></script>
    {{-- <script defer="defer" src="{{ asset('js/nav-section-collapse.js') }}"></script> --}}

    <link rel="stylesheet" href="{{ asset('vendor/datatables/dataTables.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/datatables/responsive.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="{{ asset('vendor/datatables/dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.dataTables.styling.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/responsive.dataTables.styling.min.js') }}"></script>

    <script src="{{ asset('vendor/select2/select2.min.js') }}"></script>
</head>

@php
    $routeName = request()->route()?->getName();

    $pageData = match ($routeName) {
        'admin_home' => [
            'active' => 'dashboard',
            'crumbs' => 'Estimasi | Buat Estimasi',
        ],
        'admin_categories' => [
            'active' => 'categories',
            'crumbs' => 'Master Data | Kategori',
        ],
        /*
        |--------------------------------------------------------------------------
        | MASTER DATA - MESIN
        |--------------------------------------------------------------------------
        */
        'admin_engines' => [
            'active' => 'engines',
            'crumbs' => 'Master Data | Mesin',
        ],

        'admin_create_engine' => [
            'active' => 'engines',
            'crumbs' => 'Master Data | Tambah Mesin',
        ],
        'admin_edit_engine' => [
            'active' => 'engines',
            'crumbs' => 'Master Data | Edit Mesin',
        ],
        /*
        |--------------------------------------------------------------------------
        | MASTER DATA - VENDOR
        |--------------------------------------------------------------------------
        */
        'admin_vendors' => [
            'active' => 'vendors',
            'crumbs' => 'Master Data | Vendor',
        ],
        'admin_create_vendor' => [
            'active' => 'vendors',
            'crumbs' => 'Master Data | Tambah Vendor',
        ],
        'admin_edit_vendor' => [
            'active' => 'vendors',
            'crumbs' => 'Master Data | Edit Vendor',
        ],
        /*
        |--------------------------------------------------------------------------
        | HARGA & TARIF - TARIF MESIN
        |--------------------------------------------------------------------------
        */
        'admin_engine_rates' => [
            'active' => 'engine-rates',
            'crumbs' => 'Harga & Tarif | Tarif Mesin',
        ],

        'admin_create_engine_rate' => [
            'active' => 'engine-rates',
            'crumbs' => 'Harga & Tarif | Tambah Tarif Mesin',
        ],

        'admin_edit_engine_rate' => [
            'active' => 'engine-rates',
            'crumbs' => 'Harga & Tarif | Edit Tarif Mesin',
        ],

        'admin_locations' => [
            'active' => 'locations',
            'crumbs' => 'Master Data | Lokasi',
        ],
        'admin_material_sizes' => [
            'active' => 'material-sizes',
            'crumbs' => 'Master Data | Ukuran Material',
        ],
        'admin_create_material_size' => [
            'active' => 'material-sizes',
            'crumbs' => 'Master Data | Tambah Ukuran Material',
        ],
        'admin_material_size_details' => [
            'active' => 'material-sizes',
            'crumbs' => 'Master Data | Detail Ukuran Material',
        ],
        'admin_edit_material_size' => [
            'active' => 'material-sizes',
            'crumbs' => 'Master Data | Edit Ukuran Material',
        ],
        /*
        |--------------------------------------------------------------------------
        | MASTER DATA - FINISHING & JASA
        |--------------------------------------------------------------------------
        */
        'admin_finishing_services' => [
            'active' => 'finishings',
            'crumbs' => 'Master Data | Finishing & Jasa',
        ],

        'admin_create_finishing_service' => [
            'active' => 'finishings',
            'crumbs' => 'Master Data | Tambah Finishing & Jasa',
        ],

        'admin_edit_finishing_service' => [
            'active' => 'finishings',
            'crumbs' => 'Master Data | Edit Finishing & Jasa',
        ],
        /*
        |--------------------------------------------------------------------------
        | MASTER DATA - MATERIAL
        |--------------------------------------------------------------------------
        */
        'admin_materials' => [
            'active' => 'materials',
            'crumbs' => 'Master Data | Material',
        ],

        'admin_create_material' => [
            'active' => 'materials',
            'crumbs' => 'Master Data | Tambah Material',
        ],

        'admin_edit_material' => [
            'active' => 'materials',
            'crumbs' => 'Master Data | Edit Material',
        ],
        'admin_compatibility_materials' => [
            'active' => 'machine-material-compatibility',
            'crumbs' => 'Master Data | Kompatibilitas',
        ],
        'admin_create_compatibility_material' => [
            'active' => 'machine-material-compatibility',
            'crumbs' => 'Master Data | Tambah Kompatibilitas',
        ],
        'admin_edit_compatibility_material' => [
            'active' => 'machine-material-compatibility',
            'crumbs' => 'Master Data | Edit Kompatibilitas',
        ],
        'admin_laminations' => [
            'active' => 'laminations',
            'crumbs' => 'Master Data | Laminasi',
        ],
        /*
        |--------------------------------------------------------------------------
        | HARGA & TARIF - DAFTAR HARGA
        |--------------------------------------------------------------------------
        */
        'admin_material_prices' => [
            'active' => 'material-prices',
            'crumbs' => 'Harga & Tarif | Daftar Harga Material',
        ],
        'admin_create_material_price' => [
            'active' => 'material-prices',
            'crumbs' => 'Harga & Tarif | Tambah Harga Material',
        ],
        'admin_edit_material_price' => [
            'active' => 'material-prices',
            'crumbs' => 'Harga & Tarif | Edit Harga Material',
        ],

        'admin_production_costs' => [
            'active' => 'production-costs',
            'crumbs' => 'Harga & Tarif | Daftar Ongkos Harga',
        ],
        'admin_create_production_cost' => [
            'active' => 'production-costs',
            'crumbs' => 'Harga & Tarif | Tambah Ongkos Harga',
        ],
        'admin_edit_production_cost' => [
            'active' => 'production-costs',
            'crumbs' => 'Harga & Tarif | Edit Ongkos Harga',
        ],
        'admin_production_cost_details' => [
            'active' => 'production-costs',
            'crumbs' => 'Harga & Tarif | Detail Ongkos Harga',
        ],

        'admin_pricings' => [
            'active' => 'pricings',
            'crumbs' => 'Harga & Tarif | Daftar Harga',
        ],

        'admin_create_pricing' => [
            'active' => 'pricings',
            'crumbs' => 'Harga & Tarif | Tambah Daftar Harga',
        ],

        'admin_edit_pricing' => [
            'active' => 'pricings',
            'crumbs' => 'Harga & Tarif | Edit Daftar Harga',
        ],
        /*
        |--------------------------------------------------------------------------
        | HARGA & TARIF - KEBIJAKAN HARGA
        |--------------------------------------------------------------------------
        */
        'admin_pricing_policies' => [
            'active' => 'pricing-policy',
            'crumbs' => 'Harga & Tarif | Kebijakan Harga',
        ],

        'admin_create_pricing_policy' => [
            'active' => 'pricing-policy',
            'crumbs' => 'Harga & Tarif | Tambah Kebijakan Harga',
        ],
        /*
        |--------------------------------------------------------------------------
        | KONFIGURASI
        |--------------------------------------------------------------------------
        */
        'admin_pricing_rules' => [
            'active' => 'markup-price',
            'crumbs' => 'Konfigurasi | Harga Markup',
        ],

        'admin_lamination_pricing_rules' => [
            'active' => 'lamination-markup-price',
            'crumbs' => 'Konfigurasi | Harga Markup',
        ],

        'admin_display_pricing_rules' => [
            'active' => 'display-markup-price',
            'crumbs' => 'Konfigurasi | Harga Markup',
        ],

        'admin_pc_finishings' => [
            'active' => 'production-costs-finishig',
            'crumbs' => 'Konfigurasi | Biaya',
        ],
        'admin_create_pc_finishing' => [
            'active' => 'production-costs-finishig',
            'crumbs' => 'Konfigurasi | Biaya',
        ],
        'admin_edit_pc_finishing' => [
            'active' => 'production-costs-finishig',
            'crumbs' => 'Konfigurasi | Biaya',
        ],
        'admin_pc_finishing_details' => [
            'active' => 'production-costs-finishig',
            'crumbs' => 'Konfigurasi | Biaya',
        ],

        'admin_pc_finishing_laminations' => [
            'active' => 'production-costs-finishig-lamination',
            'crumbs' => 'Konfigurasi | Biaya',
        ],
        'admin_create_pc_finishing_lamination' => [
            'active' => 'production-costs-finishig-lamination',
            'crumbs' => 'Konfigurasi | Biaya',
        ],
        'admin_edit_pc_finishing_lamination' => [
            'active' => 'production-costs-finishig-lamination',
            'crumbs' => 'Konfigurasi | Biaya',
        ],
        'admin_details_pc_finishing_lamination' => [
            'active' => 'production-costs-finishig-lamination',
            'crumbs' => 'Konfigurasi | Biaya',
        ],

        'admin_pc_finishing_displays' => [
            'active' => 'production-costs-finishig-display',
            'crumbs' => 'Konfigurasi | Biaya',
        ],
        'admin_create_pc_finishing_display' => [
            'active' => 'production-costs-finishig-display',
            'crumbs' => 'Konfigurasi | Biaya',
        ],
        'admin_add_component_pc_finishing_display' => [
            'active' => 'production-costs-finishig-display',
            'crumbs' => 'Konfigurasi | Biaya',
        ],
        'admin_details_pc_finishing_display' => [
            'active' => 'production-costs-finishig-display',
            'crumbs' => 'Konfigurasi | Biaya',
        ],
        'admin_edit_component_pc_finishing_display' => [
            'active' => 'production-costs-finishig-display',
            'crumbs' => 'Konfigurasi | Biaya',
        ],

        'admin_display' => [
            'active' => 'displays',
            'crumbs' => 'Master Data | Display',
        ],
        'admin_create_display' => [
            'active' => 'displays',
            'crumbs' => 'Master Data | Display',
        ],
        'admin_display_details' => [
            'active' => 'displays',
            'crumbs' => 'Master Data | Display',
        ],

        'admin_gallery_samples' => [
            'active' => 'gallery-sample',
            'crumbs' => 'Master Data | Galeri Sampel',
        ],
        'admin_create_gallery_samples' => [
            'active' => 'gallery-sample',
            'crumbs' => 'Master Data | Galeri Sampel',
        ],
        /*
        |--------------------------------------------------------------------------
        | HARGA & TARIF - ATURAN PERHITUNGAN
        |--------------------------------------------------------------------------
        */
        // 'admin_calculation_rules' => [
        //     'active' => 'calculation-rules',
        //     'crumbs' => 'Harga & Tarif | Aturan Perhitungan',
        // ],

        // 'admin_create_calculation_rule' => [
        //     'active' => 'calculation-rules',
        //     'crumbs' => 'Harga & Tarif | Tambah Aturan Perhitungan',
        // ],

        // 'admin_edit_calculation_rule' => [
        //     'active' => 'calculation-rules',
        //     'crumbs' => 'Harga & Tarif | Edit Aturan Perhitungan',
        // ],
        // 'admin_calculation_rule_details' => [
        //     'active' => 'calculation-rules',
        //     'crumbs' => 'Harga & Tarif | Detail Aturan Perhitungan',
        // ],
        /*
        |--------------------------------------------------------------------------
        | DEFAULT
        |--------------------------------------------------------------------------
        */
        'admin_create_lf_estimation' => [
            'active' => 'dashboard',
            'crumbs' => 'Estimasi | Tambah Estimasi Large Format',
        ],
        default => [
            'active' => 'dashboard',
            'crumbs' => 'Estimasi | Daftar Estimasi',
        ],
    };
@endphp

<body data-active="{{ $pageData['active'] }}" data-crumbs="{{ $pageData['crumbs'] }}">
    @if (session('success'))
        <div class="toast toast-success" id="notificationToast">

            <div class="toast-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M20 6 9 17l-5-5" />
                </svg>
            </div>

            <div class="toast-content">
                <strong>Berhasil</strong>

                <span>
                    {{ session('success') }}
                </span>
            </div>

            <button type="button" class="toast-close" id="closeNotification" aria-label="Tutup notifikasi">
                <svg viewBox="0 0 24 24">
                    <path d="M18 6 6 18M6 6l12 12" />
                </svg>
            </button>

        </div>
    @endif

    @if (session('error'))
        <div class="toast toast-error" id="notificationToast">

            <div class="toast-icon">

                <svg viewBox="0 0 24 24">

                    <path d="M18 6 6 18M6 6l12 12" />

                </svg>

            </div>

            <div class="toast-content">

                <strong>Gagal</strong>

                <span>
                    {{ session('error') }}
                </span>

            </div>

            <button type="button" class="toast-close" id="closeNotification" aria-label="Tutup notifikasi">

                <svg viewBox="0 0 24 24">

                    <path d="M18 6 6 18M6 6l12 12" />

                </svg>

            </button>

        </div>
    @endif

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                showToast(
                    'error',
                    '{{ $errors->first() }}'
                );

            });
        </script>
    @endif
    @yield('contents')

    <script>
        window.adminRoutes = {
            categories: "{{ route('admin_categories') }}",
            engines: "{{ route('admin_engines') }}",
            dashboard: "{{ route('admin_home') }}",
            vendors: "{{ route('admin_vendors') }}",
            engine_rates: "{{ route('admin_engine_rates') }}",
            finishing_services: "{{ route('admin_finishing_services') }}",
            materials: "{{ route('admin_materials') }}",
            pricings: "{{ route('admin_pricings') }}",
            pricing_policies: "{{ route('admin_pricing_policies') }}",
            calculation_rules: "{{ route('admin_calculation_rules') }}",
            compatibility_materials: "{{ route('admin_compatibility_materials') }}",
            locations: "{{ route('admin_locations') }}",
            material_sizes: "{{ route('admin_material_sizes') }}",
            production_costs: "{{ route('admin_production_costs') }}",
            material_prices: "{{ route('admin_material_prices') }}",
            production_costs_finishing: "{{ route('admin_pc_finishings') }}",
            pricing_rules: "{{ route('admin_pricing_rules') }}",
            laminations: "{{ route('admin_laminations') }}",
            lamination_pricing_rules: "{{ route('admin_lamination_pricing_rules') }}",
            pc_finishing_laminations: "{{ route('admin_pc_finishing_laminations') }}",
            admin_display: "{{ route('admin_display') }}",
            display_pricing_rules: "{{ route('admin_display_pricing_rules') }}",
            pc_finishing_displays: "{{ route('admin_pc_finishing_displays') }}",
            gallery_samples: "{{ route('admin_gallery_samples') }}"
        };

        window.appData = {
            brandName: @json(Auth::user()->name),
            brandEmail: @json(Auth::user()->email)
        };

        function getInitials(name) {
            if (!name) {
                return '-';
            }
            return name.trim().split(/\s+/).slice(0, 2).map(word => word.charAt(0).toUpperCase()).join('');
        }
    </script>

    <script>
        function showToast(type, message) {

            const existingToast = document.getElementById('notificationToast');

            if (existingToast) {
                existingToast.remove();
            }

            const toast = document.createElement('div');

            toast.id = 'notificationToast';

            toast.className = type === 'success' ?
                'toast toast-success' :
                'toast toast-error';

            const icon = type === 'success'

                ?
                `
                    <svg viewBox="0 0 24 24">
                        <path d="M20 6 9 17l-5-5" />
                    </svg>
                `

                :
                `
                    <svg viewBox="0 0 24 24">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>
                `;

            const title = type === 'success' ?
                'Berhasil' :
                'Gagal';

            toast.innerHTML = `

                <div class="toast-icon">

                    ${icon}

                </div>

                <div class="toast-content">

                    <strong>${title}</strong>

                    <span>${message}</span>

                </div>

                <button
                    type="button"
                    class="toast-close"
                    aria-label="Tutup notifikasi">

                    <svg viewBox="0 0 24 24">
                        <path d="M18 6 6 18M6 6l12 12" />
                    </svg>

                </button>

            `;

            document.body.appendChild(toast);


            // Tombol close

            toast.querySelector('.toast-close')
                .addEventListener('click', function() {

                    toast.remove();

                });


            // Hilang otomatis

            setTimeout(function() {

                toast.remove();

            }, 4000);

        }

        document.addEventListener('DOMContentLoaded', function() {

            const toast = document.getElementById('notificationToast');
            const closeButton = document.getElementById('closeNotification');

            if (!toast) {
                return;
            }

            let isClosing = false;

            function closeToast() {

                if (isClosing) {
                    return;
                }

                isClosing = true;

                toast.style.opacity = '0';
                toast.style.transform = 'translateX(30px)';

                setTimeout(function() {
                    toast.remove();
                }, 300);
            }

            // Tombol ✕
            if (closeButton) {
                closeButton.addEventListener('click', closeToast);
            }

            // Auto close setelah 5 detik
            setTimeout(closeToast, 5000);

        });

        document.addEventListener("click", function(event) {

            const logoutButton = event.target.closest("#logoutButton");

            if (!logoutButton) return;

            const form = document.createElement("form");

            form.method = "POST";
            form.action = "/admin/logout";

            const csrfToken = document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute("content");

            const csrfInput = document.createElement("input");

            csrfInput.type = "hidden";
            csrfInput.name = "_token";
            csrfInput.value = csrfToken;

            form.appendChild(csrfInput);

            document.body.appendChild(form);

            form.submit();

        });
    </script>

    @yield('scripts')
</body>


</html>

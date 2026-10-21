@extends('admin_master')

@section('contents')
    <div class="shell">

        <div data-shell-sidebar></div>

        <div class="main">

            <div data-shell-topbar></div>

            <main class="content">

                {{-- HERO --}}
                <section class="hero">
                    <div class="hero-text">

                        <span class="eyebrow">
                            DISPLAY · CONFIGURATION
                        </span>

                        <p class="hero-sub">
                            Atur konfigurasi Display berdasarkan produk,
                            lokasi, mesin, dan biaya yang digunakan.
                        </p>

                    </div>
                </section>

                <div class="grid">

                    <section class="col-12 card">

                        {{-- CARD HEADER --}}
                        <div class="card-head">

                            <div class="card-title-wrap">
                                <span class="eyebrow">
                                    Konfigurasi Display
                                </span>
                            </div>

                        </div>

                        {{-- FORM --}}
                        <form method="POST" action="{{ route('admin_store_pc_finishing_display') }}"
                            id="displayConfigurationForm">

                            @csrf

                            <div class="display-form-body">

                                {{-- CONTAINER HEADER --}}
                                <div id="displayConfigurationContainer" class="display-configuration-container">

                                    {{-- HEADER AWAL --}}
                                    <div class="display-configuration-item" data-configuration-index="0">

                                        <div class="display-form-head">

                                            <div class="display-form-head-title">

                                                <span class="eyebrow">
                                                    Informasi Konfigurasi
                                                </span>

                                                <button type="button" class="btn-remove-configuration" title="Hapus Header"
                                                    aria-label="Hapus Header"
                                                    style="border:none;background:transparent;box-shadow:none;outline:none;">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>

                                            </div>

                                            <div class="display-form-head-grid">

                                                {{-- DISPLAY --}}
                                                <div class="field">

                                                    <label class="field-label">
                                                        Display
                                                        <span class="req">*</span>
                                                    </label>

                                                    <select name="configurations[0][display_product_id]"
                                                        class="select select2 display-product-select" required>
                                                        <option value="">
                                                            Pilih Display
                                                        </option>

                                                        @foreach ($displayProducts as $displayProduct)
                                                            <option value="{{ $displayProduct->id }}">
                                                                {{ $displayProduct->display_name }}
                                                            </option>
                                                        @endforeach

                                                    </select>

                                                </div>

                                                {{-- LOKASI --}}
                                                <div class="field">

                                                    <label class="field-label">
                                                        Lokasi
                                                        <span class="req">*</span>
                                                    </label>

                                                    <select name="configurations[0][location_id]"
                                                        class="select select2 location-select" required>
                                                        <option value="">
                                                            Pilih Lokasi
                                                        </option>

                                                        @foreach ($locations as $location)
                                                            <option value="{{ $location->id }}">
                                                                {{ $location->name }}
                                                            </option>
                                                        @endforeach

                                                    </select>

                                                </div>

                                                {{-- MESIN --}}
                                                <div class="field">

                                                    <label class="field-label">
                                                        Mesin
                                                        <span class="req">*</span>
                                                    </label>

                                                    <select name="configurations[0][engine_id]"
                                                        class="select select2 engine-select" required>
                                                        <option value="">
                                                            Pilih Mesin
                                                        </option>

                                                        @foreach ($engines as $engine)
                                                            <option value="{{ $engine->id }}">
                                                                {{ $engine->name }}
                                                            </option>
                                                        @endforeach

                                                    </select>

                                                </div>

                                                {{-- COST RANGKA --}}
                                                <div class="field field-cost-rangka">

                                                    <label class="field-label label-cost-rangka">
                                                        Cost Rangka
                                                    </label>

                                                    <input type="text" name="configurations[0][cost_rangka]"
                                                        class="input currency-input input-cost-rangka" placeholder="Rp 0"
                                                        autocomplete="off">

                                                </div>

                                                {{-- COST FINISHING --}}
                                                <div class="field field-cost-finishing">

                                                    <label class="field-label label-cost-finishing">
                                                        Cost Finishing
                                                    </label>

                                                    <input type="text" name="configurations[0][cost_finishing]"
                                                        class="input currency-input input-cost-finishing" placeholder="Rp 0"
                                                        autocomplete="off">

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                </div>

                                {{-- TAMBAH HEADER --}}
                                <div class="display-configuration-add">

                                    <button type="button" id="btnAddDisplayConfiguration" class="btn btn--ghost">
                                        + Tambah Header
                                    </button>

                                </div>

                                {{-- ACTION --}}
                                <div class="form-actions">

                                    <span class="badge dot success">
                                        Siap disimpan
                                    </span>

                                    <span class="spacer"></span>

                                    <a href="{{ route('admin_pc_finishing_displays') }}" class="btn btn--ghost">
                                        Batal
                                    </a>

                                    <button type="submit" class="btn btn--primary">
                                        Simpan
                                    </button>

                                </div>

                            </div>

                        </form>

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

            console.log('JS DISPLAY CONFIGURATION BERJALAN');

            const dieCutPrices = @json($dieCutPrices ?? []);

            /*
            |--------------------------------------------------------------------------
            | UPDATE HEADER FIELDS (DIGITAL PRINT A3+ VS LARGE FORMAT)
            |--------------------------------------------------------------------------
            */
            function updateHeaderFields(configuration) {
                if (!configuration) return;

                const engineSelect = configuration.querySelector('.engine-select');
                const locationSelect = configuration.querySelector('.location-select');
                const labelCostRangka = configuration.querySelector('.label-cost-rangka');
                const labelCostFinishing = configuration.querySelector('.label-cost-finishing');
                const inputCostRangka = configuration.querySelector('.input-cost-rangka');
                const inputCostFinishing = configuration.querySelector('.input-cost-finishing');

                if (!engineSelect || !inputCostFinishing || !inputCostRangka) return;

                const engineText = engineSelect.selectedOptions[0] ? engineSelect.selectedOptions[0].textContent.trim().toLowerCase() : '';
                const isDigitalPrintA3 = engineText.includes('digital print');

                if (isDigitalPrintA3) {
                    if (labelCostRangka) {
                        labelCostRangka.textContent = 'Harga Box KN';
                    }
                    if (labelCostFinishing) {
                        labelCostFinishing.textContent = 'Ongkos Die Cut';
                    }
                    inputCostRangka.setAttribute('placeholder', 'Rp 0');

                    const locationId = locationSelect ? locationSelect.value : null;
                    if (locationId && dieCutPrices[locationId] !== undefined) {
                        const rawPrice = dieCutPrices[locationId];
                        const dieCutPrice = Math.round(Number(rawPrice));
                        inputCostFinishing.value = formatCurrency(dieCutPrice);
                    } else {
                        if (!locationId) {
                            inputCostFinishing.value = '';
                        }
                    }
                    inputCostFinishing.setAttribute('readonly', 'readonly');
                    inputCostFinishing.style.cursor = 'not-allowed';
                } else {
                    if (labelCostRangka) {
                        labelCostRangka.textContent = 'Cost Rangka';
                    }
                    if (labelCostFinishing) {
                        labelCostFinishing.textContent = 'Cost Finishing';
                    }
                    inputCostRangka.setAttribute('placeholder', 'Rp 0');
                    inputCostFinishing.removeAttribute('readonly');
                    inputCostFinishing.style.backgroundColor = '';
                    inputCostFinishing.style.cursor = '';
                }
            }

            /*
            |--------------------------------------------------------------------------
            | ELEMENT
            |--------------------------------------------------------------------------
            */

            const form = document.getElementById(
                'displayConfigurationForm'
            );

            const configurationContainer = document.getElementById(
                'displayConfigurationContainer'
            );

            const btnAddConfiguration = document.getElementById(
                'btnAddDisplayConfiguration'
            );

            if (
                !form ||
                !configurationContainer ||
                !btnAddConfiguration
            ) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | SELECT2
            |--------------------------------------------------------------------------
            */

            function initSelect2(container) {

                $(container)
                    .find('.select.select2')
                    .each(function() {

                        const select = $(this);

                        if (
                            select.hasClass(
                                'select2-hidden-accessible'
                            )
                        ) {
                            return;
                        }

                        select.select2({
                            allowClear: false,
                            width: '100%'
                        });

                    });

            }

            function destroySelect2(container) {

                $(container)
                    .find('.select2')
                    .each(function() {

                        const select = $(this);

                        if (
                            select.hasClass(
                                'select2-hidden-accessible'
                            )
                        ) {
                            select.select2('destroy');
                        }

                    });

            }

            /*
            |--------------------------------------------------------------------------
            | RESET HEADER
            |--------------------------------------------------------------------------
            */

            function resetConfiguration(configuration) {

                configuration
                    .querySelectorAll('input, textarea')
                    .forEach(function(field) {

                        field.value = '';
                        field.removeAttribute('readonly');
                        field.style.backgroundColor = '';
                        field.style.cursor = '';

                    });

                configuration
                    .querySelectorAll('select')
                    .forEach(function(select) {

                        select.value = '';

                        $(select).val('').trigger('change');

                    });

                updateHeaderFields(configuration);

            }

            /*
            |--------------------------------------------------------------------------
            | REINDEX CONFIGURATIONS
            |--------------------------------------------------------------------------
            */

            function reindexConfigurations() {

                const configurations =
                    configurationContainer.querySelectorAll(
                        '.display-configuration-item'
                    );

                configurations.forEach(function(
                    configuration,
                    configurationIndex
                ) {

                    configuration.dataset.configurationIndex =
                        configurationIndex;

                    configuration
                        .querySelectorAll('input, select, textarea')
                        .forEach(function(field) {

                            const name = field.getAttribute('name');

                            if (!name) {
                                return;
                            }

                            field.setAttribute(
                                'name',
                                name.replace(
                                    /configurations\[\d+\]/,
                                    'configurations[' +
                                    configurationIndex +
                                    ']'
                                )
                            );

                        });

                });

            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE HEADER ACTION
            |--------------------------------------------------------------------------
            */

            function updateConfigurationActions() {

                const configurations =
                    configurationContainer.querySelectorAll(
                        '.display-configuration-item'
                    );

                configurations.forEach(function(configuration) {

                    const removeButton =
                        configuration.querySelector(
                            '.btn-remove-configuration'
                        );

                    if (!removeButton) {
                        return;
                    }

                    removeButton.disabled =
                        configurations.length <= 1;

                    removeButton.style.opacity =
                        configurations.length <= 1 ? '0.35' : '1';

                    removeButton.style.cursor =
                        configurations.length <= 1 ?
                        'not-allowed' :
                        'pointer';

                });

            }

            /*
            |--------------------------------------------------------------------------
            | ADD HEADER
            |--------------------------------------------------------------------------
            */

            function addConfiguration() {

                const firstConfiguration =
                    configurationContainer.querySelector(
                        '.display-configuration-item'
                    );

                if (!firstConfiguration) {
                    return;
                }

                /*
                |----------------------------------------------------------------------
                | DESTROY SELECT2 SEBELUM CLONE
                |----------------------------------------------------------------------
                */

                destroySelect2(firstConfiguration);

                /*
                |----------------------------------------------------------------------
                | CLONE HEADER
                |----------------------------------------------------------------------
                */

                const newConfiguration =
                    firstConfiguration.cloneNode(true);

                /*
                |----------------------------------------------------------------------
                | RESET NILAI
                |----------------------------------------------------------------------
                */

                resetConfiguration(newConfiguration);

                /*
                |----------------------------------------------------------------------
                | APPEND HEADER
                |----------------------------------------------------------------------
                */

                configurationContainer.appendChild(
                    newConfiguration
                );

                /*
                |----------------------------------------------------------------------
                | INITIALIZE SELECT2
                |----------------------------------------------------------------------
                */

                initSelect2(configurationContainer);

                /*
                |----------------------------------------------------------------------
                | REINDEX
                |----------------------------------------------------------------------
                */

                reindexConfigurations();

                updateConfigurationActions();

                updateHeaderFields(newConfiguration);

            }

            /*
            |--------------------------------------------------------------------------
            | REMOVE HEADER
            |--------------------------------------------------------------------------
            */

            function removeConfiguration(button) {

                const configurations =
                    configurationContainer.querySelectorAll(
                        '.display-configuration-item'
                    );

                /*
                |----------------------------------------------------------------------
                | MINIMAL SATU HEADER
                |----------------------------------------------------------------------
                */

                if (configurations.length <= 1) {

                    showToast(
                        'error',
                        'Minimal harus ada satu Header.'
                    );

                    return;
                }

                const configuration =
                    button.closest(
                        '.display-configuration-item'
                    );

                if (!configuration) {
                    return;
                }

                /*
                |----------------------------------------------------------------------
                | DESTROY SELECT2
                |----------------------------------------------------------------------
                */

                destroySelect2(configuration);

                /*
                |----------------------------------------------------------------------
                | REMOVE
                |----------------------------------------------------------------------
                */

                configuration.remove();

                /*
                |----------------------------------------------------------------------
                | REINDEX
                |----------------------------------------------------------------------
                */

                reindexConfigurations();

                updateConfigurationActions();

            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI SATU HEADER
            |--------------------------------------------------------------------------
            */

            function validateConfiguration(configuration, index) {

                const displaySelect =
                    configuration.querySelector(
                        '.display-product-select'
                    );

                const locationSelect =
                    configuration.querySelector(
                        '.location-select'
                    );

                const engineSelect =
                    configuration.querySelector(
                        '.engine-select'
                    );

                const costRangkaInput =
                    configuration.querySelector(
                        '[name$="[cost_rangka]"]'
                    );

                const costFinishingInput =
                    configuration.querySelector(
                        '[name$="[cost_finishing]"]'
                    );

                /*
                |----------------------------------------------------------------------
                | VALIDASI DISPLAY
                |----------------------------------------------------------------------
                */

                if (
                    !displaySelect ||
                    !displaySelect.value
                ) {

                    showToast(
                        'error',
                        'Display pada Header ' +
                        (index + 1) +
                        ' wajib dipilih.'
                    );

                    if (displaySelect) {
                        $(displaySelect).select2('open');
                    }

                    return false;
                }

                /*
                |----------------------------------------------------------------------
                | VALIDASI LOKASI
                |----------------------------------------------------------------------
                */

                if (
                    !locationSelect ||
                    !locationSelect.value
                ) {

                    showToast(
                        'error',
                        'Lokasi pada Header ' +
                        (index + 1) +
                        ' wajib dipilih.'
                    );

                    if (locationSelect) {
                        $(locationSelect).select2('open');
                    }

                    return false;
                }

                /*
                |--------------------------------------------------------------------------
                | VALIDASI LOKASI PURWAKARTA
                |--------------------------------------------------------------------------
                */

                const selectedLocation =
                    locationSelect.options[
                        locationSelect.selectedIndex
                    ];

                const locationName =
                    selectedLocation ?
                    selectedLocation.text.trim().toLowerCase() :
                    '';

                if (locationName !== 'purwakarta') {

                    showToast(
                        'error',
                        'Untuk lokasi Graha atau Outsourcing belum tersedia.'
                    );

                    $(locationSelect).select2('open');

                    return false;
                }

                /*
                |----------------------------------------------------------------------
                | VALIDASI MESIN
                |----------------------------------------------------------------------
                */

                if (
                    !engineSelect ||
                    !engineSelect.value
                ) {

                    showToast(
                        'error',
                        'Mesin pada Header ' +
                        (index + 1) +
                        ' wajib dipilih.'
                    );

                    if (engineSelect) {
                        $(engineSelect).select2('open');
                    }

                    return false;
                }

                /*
                |----------------------------------------------------------------------
                | VALIDASI COST RANGKA
                |----------------------------------------------------------------------
                */

                const engineText = engineSelect.selectedOptions[0] ? engineSelect.selectedOptions[0].textContent.trim().toLowerCase() : '';
                const isDigitalPrintA3 = engineText.includes('digital print');
                const labelRangkaText = isDigitalPrintA3 ? 'Harga Box KN' : 'Cost Rangka';
                const labelFinishingText = isDigitalPrintA3 ? 'Ongkos Die Cut' : 'Cost Finishing';

                if (
                    !costRangkaInput ||
                    costRangkaInput.value.trim() === ''
                ) {

                    showToast(
                        'error',
                        labelRangkaText + ' pada Header ' +
                        (index + 1) +
                        ' wajib diisi.'
                    );

                    if (costRangkaInput) {
                        costRangkaInput.focus();
                    }

                    return false;
                }

                if (
                    Number(unformatCurrency(costRangkaInput.value)) < 0
                ) {

                    showToast(
                        'error',
                        labelRangkaText + ' tidak boleh negatif.'
                    );

                    costRangkaInput.focus();

                    return false;
                }

                /*
                |----------------------------------------------------------------------
                | VALIDASI COST FINISHING
                |----------------------------------------------------------------------
                */

                if (
                    !costFinishingInput ||
                    costFinishingInput.value.trim() === ''
                ) {

                    showToast(
                        'error',
                        labelFinishingText + ' pada Header ' +
                        (index + 1) +
                        (isDigitalPrintA3 ? ' belum terisi (pastikan Lokasi telah dipilih).' : ' wajib diisi.')
                    );

                    if (costFinishingInput && !isDigitalPrintA3) {
                        costFinishingInput.focus();
                    }

                    return false;
                }

                if (
                    Number(unformatCurrency(costFinishingInput.value)) < 0
                ) {

                    showToast(
                        'error',
                        labelFinishingText + ' tidak boleh negatif.'
                    );

                    costFinishingInput.focus();

                    return false;
                }

                return true;

            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI DUPLIKASI DISPLAY PRODUCT
            |--------------------------------------------------------------------------
            */

            function validateDuplicateDisplayProducts() {

                const configurations =
                    configurationContainer.querySelectorAll(
                        '.display-configuration-item'
                    );

                const selectedProducts = new Map();

                for (
                    let index = 0; index < configurations.length; index++
                ) {

                    const configuration = configurations[index];

                    const displaySelect =
                        configuration.querySelector(
                            '.display-product-select'
                        );

                    if (
                        !displaySelect ||
                        !displaySelect.value
                    ) {
                        continue;
                    }

                    const displayProductId =
                        String(displaySelect.value);

                    if (
                        selectedProducts.has(displayProductId)
                    ) {

                        const previousIndex =
                            selectedProducts.get(displayProductId);

                        showToast(
                            'error',
                            'Display Product pada Header ' +
                            (index + 1) +
                            ' duplikat dengan Header ' +
                            (previousIndex + 1) +
                            '. Setiap Display Product hanya boleh dipilih satu kali.'
                        );

                        $(displaySelect).select2('open');

                        return false;
                    }

                    selectedProducts.set(
                        displayProductId,
                        index
                    );

                }

                return true;

            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI SELURUH FORM
            |--------------------------------------------------------------------------
            */

            function validateForm() {

                const configurations =
                    configurationContainer.querySelectorAll(
                        '.display-configuration-item'
                    );

                if (!configurations.length) {

                    showToast(
                        'error',
                        'Minimal harus ada satu Header.'
                    );

                    return false;
                }

                /*
                |----------------------------------------------------------------------
                | VALIDASI SETIAP HEADER
                |----------------------------------------------------------------------
                */

                for (
                    let index = 0; index < configurations.length; index++
                ) {

                    const isValid =
                        validateConfiguration(
                            configurations[index],
                            index
                        );

                    if (!isValid) {
                        return false;
                    }

                }

                /*
                |----------------------------------------------------------------------
                | VALIDASI DUPLIKASI DISPLAY
                |----------------------------------------------------------------------
                */

                if (!validateDuplicateDisplayProducts()) {
                    return false;
                }

                return true;

            }

            /*
            |--------------------------------------------------------------------------
            | CURRENCY
            |--------------------------------------------------------------------------
            */

            function formatCurrency(value) {

                if (value === null || value === undefined || value === '') {
                    return '';
                }

                if (typeof value === 'number') {
                    value = Math.round(value);
                }

                const numericValue = String(value)
                    .replace(/\D/g, '');

                if (!numericValue) {
                    return '';
                }

                return new Intl.NumberFormat('id-ID')
                    .format(Number(numericValue));

            }

            function unformatCurrency(value) {

                return String(value)
                    .replace(/\D/g, '');

            }

            /*
            |--------------------------------------------------------------------------
            | FORMAT INPUT CURRENCY
            |--------------------------------------------------------------------------
            */

            form.addEventListener(
                'input',
                function(event) {

                    const input =
                        event.target.closest('.currency-input');

                    if (!input) {
                        return;
                    }

                    input.value = formatCurrency(input.value);

                }
            );

            /*
            |--------------------------------------------------------------------------
            | TAMBAH HEADER
            |--------------------------------------------------------------------------
            */

            btnAddConfiguration.addEventListener(
                'click',
                function(event) {

                    event.preventDefault();

                    addConfiguration();

                }
            );

            /*
            |--------------------------------------------------------------------------
            | HAPUS HEADER
            |--------------------------------------------------------------------------
            */

            configurationContainer.addEventListener(
                'click',
                function(event) {

                    const removeButton =
                        event.target.closest(
                            '.btn-remove-configuration'
                        );

                    if (!removeButton) {
                        return;
                    }

                    event.preventDefault();

                    removeConfiguration(removeButton);

                }
            );

            /*
            |--------------------------------------------------------------------------
            | SUBMIT
            |--------------------------------------------------------------------------
            */

            form.addEventListener(
                'submit',
                function(event) {

                    /*
                    |------------------------------------------------------------------
                    | REINDEX
                    |------------------------------------------------------------------
                    */

                    reindexConfigurations();

                    /*
                    |------------------------------------------------------------------
                    | VALIDASI
                    |------------------------------------------------------------------
                    */

                    const isValid = validateForm();

                    if (!isValid) {

                        event.preventDefault();

                        return;
                    }

                    /*
                    |------------------------------------------------------------------
                    | UNFORMAT CURRENCY
                    |------------------------------------------------------------------
                    */

                    form.querySelectorAll('.currency-input')
                        .forEach(function(input) {

                            input.value =
                                unformatCurrency(input.value);

                        });

                }
            );

            /*
            |--------------------------------------------------------------------------
            | LISTENER PERUBAHAN MESIN & LOKASI
            |--------------------------------------------------------------------------
            */
            $(configurationContainer).on('change', '.engine-select, .location-select', function() {
                const item = this.closest('.display-configuration-item');
                if (item) {
                    updateHeaderFields(item);
                }
            });

            /*
            |--------------------------------------------------------------------------
            | INITIALIZE
            |--------------------------------------------------------------------------
            */

            initSelect2(configurationContainer);

            reindexConfigurations();

            updateConfigurationActions();

            configurationContainer.querySelectorAll('.display-configuration-item').forEach(function(item) {
                updateHeaderFields(item);
            });

        });
    </script>
@endsection

@extends('admin_master')

@section('contents')
    <div class="shell">

        <div data-shell-sidebar></div>

        <div class="main">

            <div data-shell-topbar></div>

            <main class="content">

                <section class="hero">

                    <div class="hero-text">

                        <span class="eyebrow">
                            BIAYA · LAMINASI
                        </span>

                        <p class="hero-sub">
                            Tambahkan ongkos produksi laminasi berdasarkan lokasi,
                            engine, kategori, dan laminasi. Satu konfigurasi bisa
                            berlaku untuk beberapa lokasi, kategori, dan ukuran sekaligus.
                        </p>

                    </div>

                </section>


                <div class="grid">

                    <section class="col-12 card">

                        <div class="card-head">

                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Konfigurasi Ongkos Produksi Laminasi
                                </span>

                            </div>

                        </div>


                        <form method="POST" action="{{ route('admin_store_lamination_pc_finishing') }}"
                            id="laminationProductionCostForm">

                            @csrf


                            {{-- =========================================================
                            KONFIGURASI PRODUCTION COST LAMINASI
                        ========================================================== --}}

                            <div id="laminationConfigurationContainer"></div>


                            {{-- =========================================================
                            TAMBAH KONFIGURASI
                        ========================================================== --}}

                            <div style="margin-top: 16px;">

                                <button type="button" id="btnAddLaminationConfiguration" class="btn btn--ghost">
                                    + Tambah Konfigurasi
                                </button>

                            </div>


                            {{-- =========================================================
                            ACTION
                        ========================================================== --}}

                            <div class="form-actions" style="margin-top: 24px;">

                                <span class="badge dot success">
                                    Siap disimpan
                                </span>

                                <span class="spacer"></span>


                                <a href="{{ route('admin_pc_finishing_laminations') }}" class="btn btn--ghost">
                                    Batal
                                </a>


                                <button type="submit" class="btn btn--primary">
                                    Simpan
                                </button>

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

            const configurationContainer =
                document.getElementById(
                    'laminationConfigurationContainer'
                );

            const btnAddConfiguration =
                document.getElementById(
                    'btnAddLaminationConfiguration'
                );

            const productionCostForm =
                document.getElementById(
                    'laminationProductionCostForm'
                );

            let configurationIndex = 0;


            /*
            |--------------------------------------------------------------------------
            | SELECT2
            |--------------------------------------------------------------------------
            */

            function initSelect2(element) {

                $(element)
                    .find('.select2')
                    .each(function() {

                        if (
                            $(this).hasClass(
                                'select2-hidden-accessible'
                            )
                        ) {
                            return;
                        }

                        $(this).select2({
                            width: '100%'
                        });

                    });

            }


            /*
            |--------------------------------------------------------------------------
            | FORMAT NUMBER
            |--------------------------------------------------------------------------
            */

            function formatNumberInput(value) {

                if (value === '') {
                    return '';
                }

                value = value.replace(/\D/g, '');

                if (value === '') {
                    return '';
                }

                return Number(value).toLocaleString('id-ID');

            }


            @include('admin.production-cost-finishing-lamination._digital-rupiah-input')

            function formatRupiah(value) {

                if (value === '') {
                    return '';
                }

                value = value.replace(/\D/g, '');

                if (value === '') {
                    return '';
                }

                return Number(value).toLocaleString('id-ID');

            }


            /*
            |--------------------------------------------------------------------------
            | LOCATION
            |--------------------------------------------------------------------------
            |
            | Outsourcing tidak boleh digabung dengan lokasi lain.
            |
            */

            function validateLocationSelection(select) {

                const selectedOptions =
                    Array.from(
                        select.selectedOptions
                    );

                const selectedNames =
                    selectedOptions.map(function(option) {
                        return option.text
                            .trim()
                            .toLowerCase();
                    });

                const hasOutsourcing =
                    selectedNames.includes(
                        'outsourcing'
                    );

                if (
                    hasOutsourcing &&
                    selectedNames.length > 1
                ) {
                    return false;
                }

                return true;

            }


            function isOutsourcingLocation(select) {

                const selectedOptions =
                    Array.from(
                        select.selectedOptions
                    );

                return selectedOptions.some(
                    function(option) {

                        return option.text
                            .trim()
                            .toLowerCase() ===
                            'outsourcing';

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE FORM BERDASARKAN LOCATION
            |--------------------------------------------------------------------------
            */

            function isDigitalPrintConfiguration(configuration) {
                const select = configuration.querySelector('.engine-select');
                const option = select.options[select.selectedIndex];

                return option && option.textContent.trim().toLowerCase() === 'digital print a3+';
            }

            function updateLaminationForm(
                configuration
            ) {

                const isDigital = isDigitalPrintConfiguration(configuration);
                const locationSelect =
                    configuration.querySelector(
                        '.location-select'
                    );

                const vendorField =
                    configuration.querySelector(
                        '.vendor-field'
                    );

                const vendorSelect =
                    configuration.querySelector(
                        '.vendor-select'
                    );

                const outsourcing =
                    isOutsourcingLocation(
                        locationSelect
                    );


                /*
                |--------------------------------------------------------------------------
                | VENDOR
                |--------------------------------------------------------------------------
                */

                if (outsourcing) {

                    vendorField.style.display = '';

                    vendorSelect.disabled = false;
                    vendorSelect.required = true;

                } else {

                    vendorField.style.display = 'none';

                    vendorSelect.value = '';
                    vendorSelect.disabled = true;
                    vendorSelect.required = false;

                    /*
                    | Update Select2 setelah value dikosongkan.
                    */

                    $(vendorSelect)
                        .trigger('change.select2');

                }


                /*
                |--------------------------------------------------------------------------
                | DETAIL TABLE
                |--------------------------------------------------------------------------
                */

                configuration.querySelector('.digital-form-notice').hidden = !isDigital;

                configuration.querySelectorAll('.th-width, .td-width').forEach(function(element) {
                    element.style.display = isDigital ? 'none' : '';
                });

                configuration.querySelectorAll('.width-select').forEach(function(select) {
                    select.disabled = isDigital;
                    select.required = !isDigital;
                });

                configuration.querySelectorAll('.digital-price-header, .digital-price-cell').forEach(function(element) {
                    element.style.display = isDigital ? '' : 'none';
                });

                configuration.querySelectorAll('.digital-price-cell input').forEach(function(input) {
                    input.disabled = !isDigital;
                    input.required = isDigital;
                });

                if (isDigital) {
                    configuration.querySelectorAll(
                        '.th-price-per-meter, .th-production-cost, .th-finishing-cost, .th-vendor-price, ' +
                        '.td-price-per-meter, .td-production-cost, .td-finishing-cost, .td-vendor-price'
                    ).forEach(function(element) {
                        element.style.display = 'none';
                    });

                    configuration.querySelectorAll(
                        '.price-per-meter-input, .production-cost-input, .finishing-cost-input, .vendor-price-input'
                    ).forEach(function(input) {
                        input.disabled = true;
                        input.required = false;
                    });

                    return;
                }

                const pricePerMeterHeaders =
                    configuration.querySelectorAll(
                        '.th-price-per-meter'
                    );

                const productionCostHeaders =
                    configuration.querySelectorAll(
                        '.th-production-cost'
                    );

                const finishingCostHeaders =
                    configuration.querySelectorAll(
                        '.th-finishing-cost'
                    );

                const vendorPriceHeaders =
                    configuration.querySelectorAll(
                        '.th-vendor-price'
                    );


                /*
                |--------------------------------------------------------------------------
                | HEADER
                |--------------------------------------------------------------------------
                */

                pricePerMeterHeaders.forEach(
                    function(header) {

                        header.style.display =
                            outsourcing ?
                            'none' :
                            '';

                    }
                );

                productionCostHeaders.forEach(
                    function(header) {

                        header.style.display =
                            outsourcing ?
                            'none' :
                            '';

                    }
                );

                finishingCostHeaders.forEach(
                    function(header) {

                        header.style.display =
                            outsourcing ?
                            'none' :
                            '';

                    }
                );

                vendorPriceHeaders.forEach(
                    function(header) {

                        header.style.display =
                            outsourcing ?
                            '' :
                            'none';

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | ROW
                |--------------------------------------------------------------------------
                */

                const rows =
                    configuration.querySelectorAll(
                        '.configuration-lamination-body tr'
                    );

                rows.forEach(function(row) {

                    const pricePerMeterCell =
                        row.querySelector(
                            '.td-price-per-meter'
                        );

                    const productionCostCell =
                        row.querySelector(
                            '.td-production-cost'
                        );

                    const finishingCostCell =
                        row.querySelector(
                            '.td-finishing-cost'
                        );

                    const vendorPriceCell =
                        row.querySelector(
                            '.td-vendor-price'
                        );


                    const pricePerMeterInput =
                        row.querySelector(
                            '.price-per-meter-input'
                        );

                    const productionCostInput =
                        row.querySelector(
                            '.production-cost-input'
                        );

                    const finishingCostInput =
                        row.querySelector(
                            '.finishing-cost-input'
                        );

                    const vendorPriceInput =
                        row.querySelector(
                            '.vendor-price-input'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | OUTSOURCING
                    |--------------------------------------------------------------------------
                    */

                    if (outsourcing) {

                        pricePerMeterCell.style.display =
                            'none';

                        productionCostCell.style.display =
                            'none';

                        finishingCostCell.style.display =
                            'none';

                        vendorPriceCell.style.display =
                            '';


                        pricePerMeterInput.disabled =
                            true;

                        pricePerMeterInput.required =
                            false;

                        productionCostInput.disabled =
                            true;

                        productionCostInput.required =
                            false;

                        finishingCostInput.disabled =
                            true;

                        finishingCostInput.required =
                            false;


                        vendorPriceInput.disabled =
                            false;

                        vendorPriceInput.required =
                            true;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | NON OUTSOURCING
                    |--------------------------------------------------------------------------
                    */
                    else {

                        pricePerMeterCell.style.display =
                            '';

                        productionCostCell.style.display =
                            '';

                        finishingCostCell.style.display =
                            '';

                        vendorPriceCell.style.display =
                            'none';


                        pricePerMeterInput.disabled =
                            false;

                        pricePerMeterInput.required =
                            true;

                        productionCostInput.disabled =
                            false;

                        productionCostInput.required =
                            true;

                        finishingCostInput.disabled =
                            false;

                        finishingCostInput.required =
                            true;


                        vendorPriceInput.disabled =
                            true;

                        vendorPriceInput.required =
                            false;

                    }

                });

            }


            /*
            |--------------------------------------------------------------------------
            | LOAD LAMINATION SIZES
            |--------------------------------------------------------------------------
            */

            function loadLaminationSizes(row) {

                const laminationSelect =
                    $(row).find(
                        '.lamination-select'
                    );

                const widthSelect =
                    $(row).find(
                        '.width-select'
                    );


                laminationSelect
                    .off('change.lamination')
                    .on(
                        'change.lamination',
                        function() {

                            const laminationId =
                                $(this).val();

                            widthSelect.empty();


                            if (
                                !laminationId ||
                                isDigitalPrintConfiguration(row.closest('.configuration-card'))
                            ) {

                                widthSelect.trigger(
                                    'change'
                                );

                                return;

                            }


                            fetch(
                                    `{{ route('get_lamination_sizes') }}?id=${laminationId}`
                                )
                                .then(
                                    response => {

                                        if (!response.ok) {

                                            throw new Error(
                                                'Gagal mengambil ukuran laminasi.'
                                            );

                                        }

                                        return response.json();

                                    }
                                )
                                .then(
                                    data => {

                                        data.forEach(
                                            function(size) {

                                                let label =
                                                    `${size.width} ${size.unit}`;


                                                if (
                                                    size.length !== null &&
                                                    size.length !== undefined &&
                                                    size.length !== ''
                                                ) {

                                                    label +=
                                                        ` x ${size.length} ${size.unit}`;

                                                }


                                                widthSelect.append(
                                                    new Option(
                                                        label,
                                                        size.id
                                                    )
                                                );

                                            }
                                        );


                                        widthSelect.trigger(
                                            'change'
                                        );

                                    }
                                )
                                .catch(
                                    error => {

                                        console.error(
                                            'Gagal mengambil ukuran laminasi:',
                                            error
                                        );

                                    }
                                );

                        }
                    );

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE NOMOR KONFIGURASI
            |--------------------------------------------------------------------------
            */

            function updateConfigurationNumbers() {

                const configurations =
                    configurationContainer.querySelectorAll(
                        '.configuration-card'
                    );


                configurations.forEach(
                    function(
                        configuration,
                        index
                    ) {

                        const number =
                            index + 1;


                        const title =
                            configuration.querySelector(
                                '.configuration-number'
                            );


                        if (title) {

                            title.textContent =
                                `Konfigurasi ${number}`;

                        }


                        const deleteButton =
                            configuration.querySelector(
                                '.btn-remove-configuration'
                            );


                        if (!deleteButton) {
                            return;
                        }


                        if (
                            configurations.length === 1
                        ) {

                            deleteButton.disabled =
                                true;

                            deleteButton.style.opacity =
                                '0.5';

                            deleteButton.style.cursor =
                                'not-allowed';

                            deleteButton.style.display =
                                'none';

                        } else {

                            deleteButton.disabled =
                                false;

                            deleteButton.style.opacity =
                                '';

                            deleteButton.style.cursor =
                                '';

                            deleteButton.style.display =
                                '';

                        }

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | ADD LAMINATION ROW
            |--------------------------------------------------------------------------
            */

            function addLaminationRow(
                configuration,
                configIndex,
                laminationIndex
            ) {

                const tbody =
                    configuration.querySelector(
                        '.configuration-lamination-body'
                    );


                const row =
                    document.createElement('tr');


                row.innerHTML = `

                <td style="width: 300px;">

                    <select
                        name="configurations[${configIndex}][items][${laminationIndex}][lamination_id]"
                        class="select select2 lamination-select"
                        required
                    >

                        <option value="">
                            Pilih Laminasi
                        </option>

                        @foreach ($laminations as $lamination)

                            <option value="{{ $lamination->id }}">
                                {{ $lamination->name }}
                            </option>

                        @endforeach

                    </select>

                </td>


                <td class="td-width">

                    <select
                        name="configurations[${configIndex}][items][${laminationIndex}][size_ids][]"
                        class="select select2 width-select"
                        multiple="multiple"
                        required
                    ></select>

                </td>


                <!-- HARGA PER METER -->

                <td class="td-price-per-meter">

                    <input
                        type="text"
                        name="configurations[${configIndex}][items][${laminationIndex}][price_per_meter]"
                        class="input rupiah-input price-per-meter-input"
                        inputmode="decimal"
                        autocomplete="off"
                        placeholder="Contoh: 6600.00"
                        required
                    >

                </td>


                <!-- ONGKOS PRODUKSI -->

                <td class="td-production-cost">

                    <input
                        type="text"
                        name="configurations[${configIndex}][items][${laminationIndex}][production_cost]"
                        class="input rupiah-input production-cost-input"
                        inputmode="decimal"
                        autocomplete="off"
                        placeholder="Contoh: 9740.00"
                        required
                    >

                </td>


                <!-- ONGKOS FINISHING -->

                <td class="td-finishing-cost">

                    <input
                        type="text"
                        name="configurations[${configIndex}][items][${laminationIndex}][finishing_cost]"
                        class="input rupiah-input finishing-cost-input"
                        inputmode="decimal"
                        autocomplete="off"
                        placeholder="Contoh: 600.50"
                        required
                    >

                </td>


                <!-- HARGA VENDOR -->

                <td
                    class="td-vendor-price"
                    style="display: none;"
                >

                    <input
                        type="text"
                        name="configurations[${configIndex}][items][${laminationIndex}][vendor_price]"
                        class="input rupiah-input vendor-price-input"
                        inputmode="decimal"
                        autocomplete="off"
                        placeholder="Contoh: 10000.00"
                        disabled
                    >

                </td>


                <td class="digital-price-cell" style="display: none;">
                    <input
                        type="text"
                        name="configurations[${configIndex}][items][${laminationIndex}][unit_price]"
                        class="input rupiah-input"
                        inputmode="decimal"
                        maxlength="20"
                        pattern="(?:[0-9]+|[0-9]{1,3}(?:[.][0-9]{3})+)(?:,[0-9]{1,2})?"
                        autocomplete="off"
                        placeholder="Contoh: 316,70"
                        disabled
                    >
                </td>

                <td class="digital-price-cell" style="display: none;">
                    <input
                        type="text"
                        name="configurations[${configIndex}][items][${laminationIndex}][click_price]"
                        class="input rupiah-input"
                        inputmode="decimal"
                        maxlength="20"
                        pattern="(?:[0-9]+|[0-9]{1,3}(?:[.][0-9]{3})+)(?:,[0-9]{1,2})?"
                        autocomplete="off"
                        placeholder="Contoh: 1.500,00"
                        disabled
                    >
                </td>

                <td class="text-center">

                    <button
                        type="button"
                        class="btn--icon btn-remove-lamination"
                        aria-label="Hapus Laminasi"
                    >

                        <i class="bi bi-trash3-fill"></i>

                    </button>

                </td>

            `;


                tbody.appendChild(row);


                initSelect2(row);


                loadLaminationSizes(row);


                /*
                |--------------------------------------------------------------------------
                | APPLY CURRENT CONFIGURATION LOCATION
                |--------------------------------------------------------------------------
                */

                updateLaminationForm(
                    configuration
                );

            }


            /*
            |--------------------------------------------------------------------------
            | ADD CONFIGURATION
            |--------------------------------------------------------------------------
            */

            function addConfiguration() {

                const currentIndex =
                    configurationIndex;


                const configuration =
                    document.createElement('div');


                configuration.className =
                    'configuration-card';


                configuration.dataset.configurationIndex =
                    currentIndex;


                configuration.innerHTML = `

                <div
                    class="card"
                    style="
                        margin-bottom: 20px;
                        box-shadow: none !important;
                    "
                >


                    <div
                        class="card-head"
                        style="
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                        "
                    >

                        <div class="card-title-wrap">

                            <span
                                class="eyebrow configuration-number"
                            >
                                Konfigurasi ${currentIndex + 1}
                            </span>

                        </div>


                        <button
                            type="button"
                            class="btn--icon btn-remove-configuration"
                            aria-label="Hapus Konfigurasi"
                            title="Hapus Konfigurasi"
                        >

                            <i class="bi bi-trash3-fill"></i>

                        </button>

                    </div>


                    {{-- =====================================================
                        HEADER CONFIGURATION
                    ====================================================== --}}

                    <div class="form-grid">


                        {{-- =================================================
                            LOKASI
                        ================================================== --}}

                        <div class="field">

                            <label class="field-label">

                                Lokasi

                                <span class="req">
                                    *
                                </span>

                            </label>


                            <select
                                name="configurations[${currentIndex}][location_ids][]"
                                class="select select2 location-select"
                                multiple="multiple"
                                required
                            >

                                @foreach ($locations as $location)

                                    <option value="{{ $location->id }}">
                                        {{ $location->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- =================================================
                            ENGINE
                        ================================================== --}}

                        <div class="field">

                            <label class="field-label">

                                Engine

                                <span class="req">
                                    *
                                </span>

                            </label>


                            <select
                                name="configurations[${currentIndex}][engine_id]"
                                class="select select2 engine-select"
                                required
                            >

                                <option value="">
                                    Pilih Engine
                                </option>

                                @foreach ($engines as $engine)

                                    <option value="{{ $engine->id }}">
                                        {{ $engine->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- =================================================
                            KATEGORI
                        ================================================== --}}

                        <div class="form-field">

                            <label class="form-label">

                                Kategori

                                <span
                                    class="req"
                                    style="color: var(--danger);"
                                >
                                    *
                                </span>

                            </label>


                            <div class="category-checkboxes">

                                @foreach ($categories as $category)

                                    <label class="category-checkbox">

                                        <input
                                            type="checkbox"
                                            name="configurations[${currentIndex}][category_ids][]"
                                            value="{{ $category->id }}"
                                        >

                                        <span class="category-checkbox-box">

                                            <span class="category-checkbox-check">
                                                ✓
                                            </span>

                                            <span class="category-checkbox-label">
                                                {{ $category->name }}
                                            </span>

                                        </span>

                                    </label>

                                @endforeach

                            </div>

                        </div>


                        {{-- =================================================
                            VENDOR
                        ================================================== --}}

                        <div
                            class="field vendor-field"
                            style="display: none;"
                        >

                            <label class="field-label">

                                Vendor

                                <span class="req">
                                    *
                                </span>

                            </label>


                            <select
                                name="configurations[${currentIndex}][vendor_id]"
                                class="select select2 vendor-select"
                                disabled
                            >

                                <option value="">
                                    Pilih Vendor
                                </option>

                                @foreach ($vendors as $vendor)

                                    <option value="{{ $vendor->id }}">
                                        {{ $vendor->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                    </div>


                    {{-- =====================================================
                        DETAIL LAMINASI
                    ====================================================== --}}

                    <div
                        class="field"
                        style="
                            margin-top: 24px;
                            overflow: auto;
                        "
                    >


                        <div
                            style="
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                margin-bottom: 12px;
                            "
                        >

                            <label
                                class="field-label"
                                style="margin-bottom: 0;"
                            >

                                Detail Ongkos Produksi Laminasi

                            </label>


                            <button
                                type="button"
                                class="btn btn--ghost btn-add-lamination"
                            >

                                + Tambah Laminasi

                            </button>

                        </div>


                        <p class="field-help digital-form-notice" hidden>
                            Ukuran laminasi mengikuti master dan tidak dipilih ulang di form ini.
                            Harga dihitung per kategori, sisi, dan tier dari master pricing rules laminasi Digital Print A3+.
                            Format harga: titik untuk ribuan dan koma untuk desimal, maksimal 2 angka desimal
                            (contoh: 316,70 atau 1.500,00).
                        </p>

                        <div class="table-responsive">

                            <table class="display data-table">

                                <thead>

                                    <tr>

                                        <th>
                                            Laminasi
                                        </th>


                                        <th class="th-width">
                                            Lebar Dipakai
                                        </th>


                                        <th class="th-price-per-meter">
                                            Harga Permeter (Rp)
                                        </th>


                                        <th class="th-production-cost">
                                            Ongkos Produksi (Rp)
                                        </th>


                                        <th class="th-finishing-cost">
                                            Ongkos Finishing (Rp)
                                        </th>


                                        <th
                                            class="th-vendor-price"
                                            style="display: none;"
                                        >
                                            Harga Vendor (Rp)
                                        </th>


                                        <th class="digital-price-header" style="display: none;">
                                            Harga Satuan (Rp)
                                        </th>

                                        <th class="digital-price-header" style="display: none;">
                                            Harga Klik (Rp)
                                        </th>

                                        <th style="width: 80px;">
                                            Aksi
                                        </th>

                                    </tr>

                                </thead>


                                <tbody
                                    class="configuration-lamination-body"
                                ></tbody>

                            </table>

                        </div>

                    </div>


                </div>

            `;


                configurationContainer.appendChild(
                    configuration
                );


                initSelect2(
                    configuration
                );


                /*
                |--------------------------------------------------------------------------
                | LOCATION STATE
                |--------------------------------------------------------------------------
                */

                const locationSelect =
                    configuration.querySelector(
                        '.location-select'
                    );


                /*
                | Simpan pilihan lokasi terakhir yang valid.
                */

                let previousLocationValues = [];


                /*
                |--------------------------------------------------------------------------
                | LOCATION CHANGE
                |--------------------------------------------------------------------------
                */

                $(locationSelect)
                    .off('change.laminationLocation')
                    .on(
                        'change.laminationLocation',
                        function() {

                            if (
                                !validateLocationSelection(
                                    this
                                )
                            ) {

                                /*
                                | Kembalikan ke pilihan lokasi
                                | valid sebelumnya.
                                */

                                $(this).val(
                                    previousLocationValues
                                );


                                $(this).trigger(
                                    'change.select2'
                                );


                                showToast(
                                    'error',
                                    'Lokasi Outsourcing tidak dapat digabung dengan lokasi lainnya.'
                                );


                                updateLaminationForm(
                                    configuration
                                );

                                return;

                            }


                            previousLocationValues =
                                $(this).val() || [];


                            updateLaminationForm(
                                configuration
                            );

                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | ENGINE CHANGE
                |--------------------------------------------------------------------------
                */

                const engineSelect =
                    configuration.querySelector(
                        '.engine-select'
                    );


                $(engineSelect)
                    .off('change.laminationEngine')
                    .on(
                        'change.laminationEngine',
                        function() {

                            updateLaminationForm(configuration);

                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | ADD FIRST LAMINATION
                |--------------------------------------------------------------------------
                */

                let laminationIndex = 0;


                addLaminationRow(
                    configuration,
                    currentIndex,
                    laminationIndex
                );


                laminationIndex++;


                /*
                |--------------------------------------------------------------------------
                | ADD LAMINATION
                |--------------------------------------------------------------------------
                */

                const btnAddLamination =
                    configuration.querySelector(
                        '.btn-add-lamination'
                    );


                btnAddLamination.addEventListener(
                    'click',
                    function() {

                        addLaminationRow(
                            configuration,
                            currentIndex,
                            laminationIndex
                        );


                        laminationIndex++;

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | REMOVE CONFIGURATION
                |--------------------------------------------------------------------------
                */

                const btnRemoveConfiguration =
                    configuration.querySelector(
                        '.btn-remove-configuration'
                    );


                btnRemoveConfiguration.addEventListener(
                    'click',
                    function() {

                        const configurations =
                            configurationContainer
                            .querySelectorAll(
                                '.configuration-card'
                            );


                        if (
                            configurations.length <= 1
                        ) {

                            return;

                        }


                        configuration
                            .querySelectorAll('.select2')
                            .forEach(
                                function(select) {

                                    if (
                                        $(select).hasClass(
                                            'select2-hidden-accessible'
                                        )
                                    ) {

                                        $(select).select2(
                                            'destroy'
                                        );

                                    }

                                }
                            );


                        configuration.remove();


                        updateConfigurationNumbers();

                    }
                );


                updateConfigurationNumbers();


                configurationIndex++;

            }


            /*
            |--------------------------------------------------------------------------
            | ADD CONFIGURATION BUTTON
            |--------------------------------------------------------------------------
            */

            btnAddConfiguration.addEventListener(
                'click',
                function() {

                    addConfiguration();

                }
            );


            /*
            |--------------------------------------------------------------------------
            | FORMAT INPUT
            |--------------------------------------------------------------------------
            */

            configurationContainer.addEventListener(
                'input',
                function(event) {

                    if (
                        !event.target.classList.contains(
                            'rupiah-input'
                        )
                    ) {

                        return;

                    }


                    const input =
                        event.target;

                    const regroup = event.inputType &&
                        (event.inputType.startsWith('delete') ||
                            (event.inputType === 'insertText' && /^[0-9,]+$/.test(event.data || '')));

                    input.value =
                        input.closest('.digital-price-cell') ?
                        formatDigitalRupiahInput(input, false, regroup) :
                        formatNumberInput(
                            input.value
                        );


                    input.setSelectionRange(
                        input.value.length,
                        input.value.length
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | FORMAT ON BLUR
            |--------------------------------------------------------------------------
            */

            configurationContainer.addEventListener(
                'blur',
                function(event) {

                    if (
                        !event.target.classList.contains(
                            'rupiah-input'
                        )
                    ) {

                        return;

                    }


                    const input =
                        event.target;


                    if (
                        input.value === ''
                    ) {

                        return;

                    }


                    input.value =
                        input.closest('.digital-price-cell') ?
                        formatDigitalRupiahInput(input, true) :
                        formatRupiah(
                            input.value
                        );

                },
                true
            );


            /*
            |--------------------------------------------------------------------------
            | REMOVE LAMINATION ROW
            |--------------------------------------------------------------------------
            */

            configurationContainer.addEventListener(
                'click',
                function(event) {

                    const deleteButton =
                        event.target.closest(
                            '.btn-remove-lamination'
                        );


                    if (!deleteButton) {
                        return;
                    }


                    const row =
                        deleteButton.closest('tr');


                    if (!row) {
                        return;
                    }


                    const tbody =
                        row.closest(
                            '.configuration-lamination-body'
                        );


                    if (!tbody) {
                        return;
                    }


                    row
                        .querySelectorAll('.select2')
                        .forEach(
                            function(select) {

                                if (
                                    $(select).hasClass(
                                        'select2-hidden-accessible'
                                    )
                                ) {

                                    $(select).select2(
                                        'destroy'
                                    );

                                }

                            }
                        );


                    row.remove();


                }
            );


            /*
            |--------------------------------------------------------------------------
            | VALIDATE CATEGORY + SUBMIT
            |--------------------------------------------------------------------------
            */

            productionCostForm.addEventListener(
                'submit',
                async function(event) {

                    event.preventDefault();


                    const form = this;


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDASI HTML
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !form.checkValidity()
                    ) {

                        form.reportValidity();

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDASI KATEGORI
                    |--------------------------------------------------------------------------
                    */

                    const configurations =
                        configurationContainer.querySelectorAll(
                            '.configuration-card'
                        );


                    for (
                        let i = 0; i < configurations.length; i++
                    ) {

                        const configuration =
                            configurations[i];

                        const checkedCategories =
                            configuration.querySelectorAll(
                                '.category-checkbox input:checked'
                            );


                        if (
                            checkedCategories.length === 0
                        ) {

                            showToast(
                                'error',
                                `Konfigurasi ${i + 1} harus memiliki minimal satu kategori.`
                            );


                            return;

                        }

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SUBMIT
                    |--------------------------------------------------------------------------
                    */

                    const submitButton =
                        form.querySelector(
                            'button[type="submit"]'
                        );


                    const originalText =
                        submitButton.innerHTML;


                    submitButton.disabled =
                        true;


                    submitButton.innerHTML =
                        'Menyimpan...';


                    try {

                        const response =
                            await fetch(
                                form.action, {
                                    method: 'POST',
                                    body: new FormData(form),
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                }
                            );


                        const data =
                            await response.json();


                        /*
                        |--------------------------------------------------------------------------
                        | BERHASIL
                        |--------------------------------------------------------------------------
                        */

                        if (
                            response.ok &&
                            data.status === 'success'
                            // data.success
                        ) {

                            showToast(
                                'success',
                                data.message
                            );


                            setTimeout(
                                function() {

                                    window.location.href =
                                        "{{ route('admin_pc_finishing_laminations') }}";

                                },
                                800
                            );


                            return;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | WARNING / ERROR
                        |--------------------------------------------------------------------------
                        */

                        showToast(
                            data.type || 'error',
                            (data.errors ? Object.values(data.errors).flat().join(' ') : data.message) ||
                            'Terjadi kesalahan saat menyimpan Production Cost Laminasi.'
                        );


                    } catch (error) {

                        console.error(
                            'Gagal menyimpan Production Cost Laminasi:',
                            error
                        );


                        showToast(
                            'error',
                            'Terjadi kesalahan saat menyimpan Production Cost Laminasi.'
                        );


                    } finally {

                        submitButton.disabled =
                            false;


                        submitButton.innerHTML =
                            originalText;

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | KONFIGURASI PERTAMA
            |--------------------------------------------------------------------------
            */

            addConfiguration();

        });


        /*
        |--------------------------------------------------------------------------
        | SESSION TOAST
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                @if (session('success'))

                    showToast(
                        'success',
                        @json(session('success'))
                    );
                @endif


                @if (session('error'))

                    showToast(
                        'error',
                        @json(session('error'))
                    );
                @endif

            }
        );
    </script>
@endsection

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
                            Ubah ongkos produksi laminasi berdasarkan lokasi,
                            engine, kategori, dan laminasi. Satu konfigurasi bisa
                            berlaku untuk beberapa lokasi, kategori, dan ukuran
                            sekaligus.
                        </p>

                    </div>

                </section>


                <div class="grid">

                    <section class="col-12 card">

                        <div class="card-head">

                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Edit Konfigurasi Ongkos Produksi Laminasi
                                </span>

                            </div>

                        </div>


                        <form method="POST"
                            action="{{ route('admin_update_pc_finishing_lamination', ['id' => request()->route('id')]) }}"
                            id="laminationProductionCostForm">

                            @csrf

                            @method('PUT')


                            {{-- 
                        |--------------------------------------------------------------------------
                        | EXISTING CONFIGURATIONS
                        |--------------------------------------------------------------------------
                        |
                        | Konfigurasi existing akan dibuat oleh JavaScript
                        | berdasarkan data dari controller:
                        |
                        | $configurations
                        | $engines
                        | $locations
                        | $categories
                        | $laminations
                        | $laminationSizes
                        | $vendors
                        |
                        | Jangan membuat konfigurasi secara manual di sini.
                        |
                        |--------------------------------------------------------------------------
                        --}}

                            <div id="laminationConfigurationContainer"></div>


                            {{-- 
                        |--------------------------------------------------------------------------
                        | FORM ACTION
                        |--------------------------------------------------------------------------
                        --}}

                            <div class="form-actions" style="margin-top: 24px;">

                                <span class="badge dot success">
                                    Siap diperbarui
                                </span>

                                <span class="spacer"></span>


                                <a href="{{ route('admin_pc_finishing_laminations') }}" class="btn btn--ghost">
                                    Batal
                                </a>


                                <button type="submit" class="btn btn--primary" id="btnSaveLaminationProductionCost">
                                    Simpan Perubahan
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

            const productionCostForm =
                document.getElementById(
                    'laminationProductionCostForm'
                );


            /*
            |--------------------------------------------------------------------------
            | DATA DARI CONTROLLER
            |--------------------------------------------------------------------------
            */

            const configurations =
                @json($configurations);

            const engines =
                @json($engines);

            const locations =
                @json($locations);

            const categories =
                @json($categories);

            const laminations =
                @json($laminations);

            const laminationSizes =
                @json($laminationSizes);

            const vendors =
                @json($vendors);


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
            | FORMAT EXISTING VALUE
            |--------------------------------------------------------------------------
            */

            function formatExistingValue(value) {

                if (
                    value === null ||
                    value === undefined ||
                    value === ''
                ) {
                    return '';
                }

                return Number(value).toLocaleString('id-ID');

            }


            /*
            |--------------------------------------------------------------------------
            | BUILD LAMINATION OPTIONS
            |--------------------------------------------------------------------------
            */

            function buildLaminationOptions(
                selectedLaminationId
            ) {

                let html = `
            <option value="">
                Pilih Laminasi
            </option>
        `;

                laminations.forEach(function(lamination) {

                    const selected =
                        Number(lamination.id) ===
                        Number(selectedLaminationId) ?
                        'selected' :
                        '';

                    html += `
                <option
                    value="${lamination.id}"
                    ${selected}
                >
                    ${lamination.name}
                </option>
            `;

                });

                return html;

            }


            /*
            |--------------------------------------------------------------------------
            | BUILD LOCATION OPTIONS
            |--------------------------------------------------------------------------
            */

            function buildLocationOptions(
                selectedLocationIds
            ) {

                let html = '';

                locations.forEach(function(location) {

                    const selected =
                        selectedLocationIds
                        .map(Number)
                        .includes(Number(location.id)) ?
                        'selected' :
                        '';

                    html += `
                <option
                    value="${location.id}"
                    ${selected}
                >
                    ${location.name}
                </option>
            `;

                });

                return html;

            }


            /*
            |--------------------------------------------------------------------------
            | BUILD ENGINE OPTIONS
            |--------------------------------------------------------------------------
            */

            function buildEngineOptions(
                selectedEngineId
            ) {

                let html = `
            <option value="">
                Pilih Engine
            </option>
        `;

                engines.forEach(function(engine) {

                    const selected =
                        Number(engine.id) ===
                        Number(selectedEngineId) ?
                        'selected' :
                        '';

                    html += `
                <option
                    value="${engine.id}"
                    ${selected}
                >
                    ${engine.name}
                </option>
            `;

                });

                return html;

            }


            /*
            |--------------------------------------------------------------------------
            | BUILD VENDOR OPTIONS
            |--------------------------------------------------------------------------
            */

            function buildVendorOptions(
                selectedVendorId
            ) {

                let html = `
            <option value="">
                Pilih Vendor
            </option>
        `;

                vendors.forEach(function(vendor) {

                    const selected =
                        Number(vendor.id) ===
                        Number(selectedVendorId) ?
                        'selected' :
                        '';

                    html += `
                <option
                    value="${vendor.id}"
                    ${selected}
                >
                    ${vendor.name}
                </option>
            `;

                });

                return html;

            }


            /*
            |--------------------------------------------------------------------------
            | BUILD CATEGORY CHECKBOXES
            |--------------------------------------------------------------------------
            */

            function buildCategoryCheckboxes(
                configIndex,
                selectedCategoryIds
            ) {

                let html = '';

                categories.forEach(function(category) {

                    const checked =
                        selectedCategoryIds
                        .map(Number)
                        .includes(Number(category.id)) ?
                        'checked' :
                        '';

                    html += `
                <label class="category-checkbox">

                    <input
                        type="checkbox"
                        name="configurations[${configIndex}][category_ids][]"
                        value="${category.id}"
                        ${checked}
                    >

                    <span class="category-checkbox-box">

                        <span class="category-checkbox-check">
                            ✓
                        </span>

                        <span class="category-checkbox-label">
                            ${category.name}
                        </span>

                    </span>

                </label>
            `;

                });

                return html;

            }


            /*
            |--------------------------------------------------------------------------
            | BUILD SIZE OPTIONS
            |--------------------------------------------------------------------------
            */

            function buildSizeOptions(
                laminationId,
                selectedSizeIds
            ) {

                let html = '';

                const sizes =
                    laminationSizes[laminationId] || [];

                const normalizedSelectedSizeIds =
                    selectedSizeIds.map(Number);

                sizes.forEach(function(size) {

                    const selected =
                        normalizedSelectedSizeIds.includes(
                            Number(size.id)
                        ) ?
                        'selected' :
                        '';

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

                    html += `
                <option
                    value="${size.id}"
                    ${selected}
                >
                    ${label}
                </option>
            `;

                });

                return html;

            }


            /*
            |--------------------------------------------------------------------------
            | CHECK OUTSOURCING LOCATION
            |--------------------------------------------------------------------------
            */

            function isOutsourcingLocation(
                configuration
            ) {

                const locationSelect =
                    configuration.querySelector(
                        '.location-select'
                    );

                if (!locationSelect) {
                    return false;
                }

                const selectedOption =
                    locationSelect.options[
                        locationSelect.selectedIndex
                    ];

                if (!selectedOption) {
                    return false;
                }

                return (
                    selectedOption.textContent
                    .trim()
                    .toLowerCase() === 'outsourcing'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE LAMINATION FORM
            |--------------------------------------------------------------------------
            |
            | Outsourcing:
            | - Vendor aktif
            | - Harga Vendor aktif
            | - Harga Permeter disabled
            | - Ongkos Produksi disabled
            | - Ongkos Finishing disabled
            |
            | Non-Outsourcing:
            | - Vendor disabled
            | - Harga Vendor disabled
            | - Harga Permeter aktif
            | - Ongkos Produksi aktif
            | - Ongkos Finishing aktif
            |
            */

            function updateLaminationForm(
                configuration
            ) {

                const outsourcing =
                    isOutsourcingLocation(
                        configuration
                    );


                /*
                |--------------------------------------------------------------------------
                | HEADER VENDOR
                |--------------------------------------------------------------------------
                */

                const vendorField =
                    configuration.querySelector(
                        '.vendor-field'
                    );

                const vendorSelect =
                    configuration.querySelector(
                        '.vendor-select'
                    );


                /*
                |--------------------------------------------------------------------------
                | TABLE HEADERS
                |--------------------------------------------------------------------------
                */

                const thPricePerMeter =
                    configuration.querySelector(
                        '.th-price-per-meter'
                    );

                const thProductionCost =
                    configuration.querySelector(
                        '.th-production-cost'
                    );

                const thFinishingCost =
                    configuration.querySelector(
                        '.th-finishing-cost'
                    );

                const thVendorPrice =
                    configuration.querySelector(
                        '.th-vendor-price'
                    );


                /*
                |--------------------------------------------------------------------------
                | TABLE CELLS
                |--------------------------------------------------------------------------
                */

                const priceCells =
                    configuration.querySelectorAll(
                        '.td-price-per-meter'
                    );

                const productionCells =
                    configuration.querySelectorAll(
                        '.td-production-cost'
                    );

                const finishingCells =
                    configuration.querySelectorAll(
                        '.td-finishing-cost'
                    );

                const vendorPriceCells =
                    configuration.querySelectorAll(
                        '.td-vendor-price'
                    );


                /*
                |--------------------------------------------------------------------------
                | INPUTS
                |--------------------------------------------------------------------------
                */

                const priceInputs =
                    configuration.querySelectorAll(
                        '.price-per-meter-input'
                    );

                const productionInputs =
                    configuration.querySelectorAll(
                        '.production-cost-input'
                    );

                const finishingInputs =
                    configuration.querySelectorAll(
                        '.finishing-cost-input'
                    );

                const vendorPriceInputs =
                    configuration.querySelectorAll(
                        '.vendor-price-input'
                    );


                if (outsourcing) {

                    /*
                    |--------------------------------------------------------------------------
                    | VENDOR
                    |--------------------------------------------------------------------------
                    */

                    if (vendorField) {
                        vendorField.style.display = '';
                    }

                    if (vendorSelect) {

                        vendorSelect.disabled = false;
                        vendorSelect.required = true;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | HIDE NON-OUTSOURCING COLUMNS
                    |--------------------------------------------------------------------------
                    */

                    if (thPricePerMeter) {
                        thPricePerMeter.style.display = 'none';
                    }

                    if (thProductionCost) {
                        thProductionCost.style.display = 'none';
                    }

                    if (thFinishingCost) {
                        thFinishingCost.style.display = 'none';
                    }


                    priceCells.forEach(function(cell) {
                        cell.style.display = 'none';
                    });

                    productionCells.forEach(function(cell) {
                        cell.style.display = 'none';
                    });

                    finishingCells.forEach(function(cell) {
                        cell.style.display = 'none';
                    });


                    /*
                    |--------------------------------------------------------------------------
                    | DISABLE NON-OUTSOURCING INPUT
                    |--------------------------------------------------------------------------
                    */

                    priceInputs.forEach(function(input) {

                        input.disabled = true;
                        input.required = false;

                    });

                    productionInputs.forEach(function(input) {

                        input.disabled = true;
                        input.required = false;

                    });

                    finishingInputs.forEach(function(input) {

                        input.disabled = true;
                        input.required = false;

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | SHOW VENDOR PRICE
                    |--------------------------------------------------------------------------
                    */

                    if (thVendorPrice) {
                        thVendorPrice.style.display = '';
                    }

                    vendorPriceCells.forEach(function(cell) {
                        cell.style.display = '';
                    });

                    vendorPriceInputs.forEach(function(input) {

                        input.disabled = false;
                        input.required = true;

                    });

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | VENDOR
                    |--------------------------------------------------------------------------
                    */

                    if (vendorField) {
                        vendorField.style.display = 'none';
                    }

                    if (vendorSelect) {

                        vendorSelect.disabled = true;
                        vendorSelect.required = false;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SHOW NON-OUTSOURCING COLUMNS
                    |--------------------------------------------------------------------------
                    */

                    if (thPricePerMeter) {
                        thPricePerMeter.style.display = '';
                    }

                    if (thProductionCost) {
                        thProductionCost.style.display = '';
                    }

                    if (thFinishingCost) {
                        thFinishingCost.style.display = '';
                    }


                    priceCells.forEach(function(cell) {
                        cell.style.display = '';
                    });

                    productionCells.forEach(function(cell) {
                        cell.style.display = '';
                    });

                    finishingCells.forEach(function(cell) {
                        cell.style.display = '';
                    });


                    /*
                    |--------------------------------------------------------------------------
                    | ENABLE NON-OUTSOURCING INPUT
                    |--------------------------------------------------------------------------
                    */

                    priceInputs.forEach(function(input) {

                        input.disabled = false;
                        input.required = true;

                    });

                    productionInputs.forEach(function(input) {

                        input.disabled = false;
                        input.required = true;

                    });

                    finishingInputs.forEach(function(input) {

                        input.disabled = false;
                        input.required = true;

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | HIDE VENDOR PRICE
                    |--------------------------------------------------------------------------
                    */

                    if (thVendorPrice) {
                        thVendorPrice.style.display = 'none';
                    }

                    vendorPriceCells.forEach(function(cell) {
                        cell.style.display = 'none';
                    });

                    vendorPriceInputs.forEach(function(input) {

                        input.disabled = true;
                        input.required = false;

                    });

                }

            }


            /*
            |--------------------------------------------------------------------------
            | LOAD LAMINATION SIZES
            |--------------------------------------------------------------------------
            */

            function loadLaminationSizes(
                row,
                selectedSizeIds = []
            ) {

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


                            if (!laminationId) {

                                widthSelect.trigger(
                                    'change'
                                );

                                return;

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | LOAD FROM DATABASE
                            |--------------------------------------------------------------------------
                            */

                            fetch(
                                    `{{ route('get_lamination_sizes') }}?id=${laminationId}`
                                )
                                .then(response => {

                                    if (!response.ok) {

                                        throw new Error(
                                            'Gagal mengambil ukuran laminasi.'
                                        );

                                    }

                                    return response.json();

                                })
                                .then(data => {

                                    data.forEach(function(size) {

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

                                        const option =
                                            new Option(
                                                label,
                                                size.id
                                            );

                                        widthSelect.append(
                                            option
                                        );

                                    });


                                    /*
                                    |--------------------------------------------------------------------------
                                    | SELECT EXISTING SIZE
                                    |--------------------------------------------------------------------------
                                    */

                                    if (
                                        selectedSizeIds &&
                                        selectedSizeIds.length
                                    ) {

                                        widthSelect.val(
                                            selectedSizeIds.map(
                                                String
                                            )
                                        );

                                    }

                                    widthSelect.trigger(
                                        'change'
                                    );

                                })
                                .catch(error => {

                                    console.error(
                                        'Gagal mengambil ukuran laminasi:',
                                        error
                                    );

                                    showToast(
                                        'error',
                                        'Gagal mengambil ukuran laminasi.'
                                    );

                                });

                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | TRIGGER EXISTING LAMINATION
                |--------------------------------------------------------------------------
                */

                if (laminationSelect.val()) {

                    laminationSelect.trigger(
                        'change.lamination'
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE NOMOR DETAIL
            |--------------------------------------------------------------------------
            */

            function updateLaminationNumbers(
                configuration
            ) {

                const rows =
                    configuration.querySelectorAll(
                        '.configuration-lamination-body tr'
                    );

                rows.forEach(function(row, index) {

                    const numberCell =
                        row.querySelector(
                            '.lamination-number'
                        );

                    if (numberCell) {

                        numberCell.textContent =
                            index + 1;

                    }

                });

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE INPUT NAMES
            |--------------------------------------------------------------------------
            */

            function updateLaminationNames(
                configuration,
                configIndex
            ) {

                const rows =
                    configuration.querySelectorAll(
                        '.configuration-lamination-body tr'
                    );


                rows.forEach(function(row, index) {

                    const laminationSelect =
                        row.querySelector(
                            '.lamination-select'
                        );

                    const widthSelect =
                        row.querySelector(
                            '.width-select'
                        );

                    const priceInput =
                        row.querySelector(
                            '[data-field="price_per_meter"]'
                        );

                    const productionInput =
                        row.querySelector(
                            '[data-field="production_cost"]'
                        );

                    const finishingInput =
                        row.querySelector(
                            '[data-field="finishing_cost"]'
                        );

                    const vendorPriceInput =
                        row.querySelector(
                            '[data-field="vendor_price"]'
                        );


                    if (laminationSelect) {

                        laminationSelect.name =
                            `configurations[${configIndex}][items][${index}][lamination_id]`;

                    }


                    if (widthSelect) {

                        widthSelect.name =
                            `configurations[${configIndex}][items][${index}][size_ids][]`;

                    }


                    if (priceInput) {

                        priceInput.name =
                            `configurations[${configIndex}][items][${index}][price_per_meter]`;

                    }


                    if (productionInput) {

                        productionInput.name =
                            `configurations[${configIndex}][items][${index}][production_cost]`;

                    }


                    if (finishingInput) {

                        finishingInput.name =
                            `configurations[${configIndex}][items][${index}][finishing_cost]`;

                    }


                    if (vendorPriceInput) {

                        vendorPriceInput.name =
                            `configurations[${configIndex}][items][${index}][vendor_price]`;

                    }

                });

            }


            /*
            |--------------------------------------------------------------------------
            | ADD LAMINATION ROW
            |--------------------------------------------------------------------------
            */

            function addLaminationRow(
                configuration,
                configIndex,
                detail = null
            ) {

                const tbody =
                    configuration.querySelector(
                        '.configuration-lamination-body'
                    );

                const laminationIndex =
                    tbody.querySelectorAll('tr').length;

                const row =
                    document.createElement('tr');


                row.innerHTML = `

            <td class="lamination-number">
                ${laminationIndex + 1}
            </td>


            <td>

                <select
                    name="configurations[${configIndex}][items][${laminationIndex}][lamination_id]"
                    class="select select2 lamination-select"
                    required
                >

                    ${buildLaminationOptions(
                        detail
                            ? detail.lamination_id
                            : ''
                    )}

                </select>

            </td>


            <td>

                <select
                    name="configurations[${configIndex}][items][${laminationIndex}][size_ids][]"
                    class="select select2 width-select"
                    multiple="multiple"
                    required
                >

                </select>

            </td>


            <td class="td-price-per-meter">

                <input
                    type="text"
                    name="configurations[${configIndex}][items][${laminationIndex}][price_per_meter]"
                    class="input rupiah-input price-per-meter-input"
                    data-field="price_per_meter"
                    inputmode="decimal"
                    autocomplete="off"
                    placeholder="Contoh: 6600.00"
                    value="${
                        detail
                            ? formatExistingValue(
                                detail.price_per_meter
                            )
                            : ''
                    }"
                    required
                >

            </td>


            <td class="td-production-cost">

                <input
                    type="text"
                    name="configurations[${configIndex}][items][${laminationIndex}][production_cost]"
                    class="input rupiah-input production-cost-input"
                    data-field="production_cost"
                    inputmode="decimal"
                    autocomplete="off"
                    placeholder="Contoh: 9740.00"
                    value="${
                        detail
                            ? formatExistingValue(
                                detail.production_cost
                            )
                            : ''
                    }"
                    required
                >

            </td>


            <td class="td-finishing-cost">

                <input
                    type="text"
                    name="configurations[${configIndex}][items][${laminationIndex}][finishing_cost]"
                    class="input rupiah-input finishing-cost-input"
                    data-field="finishing_cost"
                    inputmode="decimal"
                    autocomplete="off"
                    placeholder="Contoh: 600.50"
                    value="${
                        detail
                            ? formatExistingValue(
                                detail.finishing_cost
                            )
                            : ''
                    }"
                    required
                >

            </td>


            <td
                class="td-vendor-price"
                style="display: none;"
            >

                <input
                    type="text"
                    name="configurations[${configIndex}][items][${laminationIndex}][vendor_price]"
                    class="input rupiah-input vendor-price-input"
                    data-field="vendor_price"
                    inputmode="decimal"
                    autocomplete="off"
                    placeholder="Contoh: 10000.00"
                    value="${
                        detail
                            ? formatExistingValue(
                                detail.vendor_price
                            )
                            : ''
                    }"
                    disabled
                >

            </td>


            <td class="text-center">

                <button
                    type="button"
                    class="btn--icon btn-remove-lamination"
                    aria-label="Hapus Laminasi"
                    title="Hapus Laminasi"
                >

                    <i class="bi bi-trash3-fill"></i>

                </button>

            </td>

        `;


                tbody.appendChild(row);


                /*
                |--------------------------------------------------------------------------
                | SELECT2
                |--------------------------------------------------------------------------
                */

                initSelect2(row);


                /*
                |--------------------------------------------------------------------------
                | EXISTING SIZE
                |--------------------------------------------------------------------------
                */

                const selectedSizeIds =
                    detail ?
                    (
                        detail.sizes || []
                    ).map(function(size) {

                        return Number(
                            size.lamination_size_id
                        );

                    }) :
                    [];


                loadLaminationSizes(
                    row,
                    selectedSizeIds
                );


                /*
                |--------------------------------------------------------------------------
                | UPDATE FORM STATE
                |--------------------------------------------------------------------------
                */

                updateLaminationForm(
                    configuration
                );

            }


            /*
            |--------------------------------------------------------------------------
            | RENDER CONFIGURATION
            |--------------------------------------------------------------------------
            */

            function renderConfiguration(
                configuration,
                configIndex
            ) {

                const configurationElement =
                    document.createElement('div');

                configurationElement.className =
                    'configuration-card';


                configurationElement.dataset.configurationIndex =
                    configIndex;


                configurationElement.dataset.productionCostId =
                    configuration.production_cost_id;


                const selectedLocationIds =
                    (
                        configuration.location_ids || []
                    ).map(Number);


                const selectedCategoryIds =
                    (
                        configuration.category_ids || []
                    ).map(Number);


                /*
                |--------------------------------------------------------------------------
                | EXISTING VENDOR
                |--------------------------------------------------------------------------
                |
                | Vendor tersimpan di detail, bukan di header configuration.
                | Karena satu configuration seharusnya memiliki konfigurasi vendor
                | yang sama, ambil dari detail pertama.
                |
                */

                const firstDetail =
                    (
                        configuration.details || []
                    )[0] || null;


                const selectedVendorId =
                    firstDetail &&
                    firstDetail.vendor_id ?
                    Number(firstDetail.vendor_id) :
                    '';


                configurationElement.innerHTML = `

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
                            Konfigurasi ${configIndex + 1}
                        </span>

                    </div>

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
                            name="configurations[${configIndex}][location_ids][]"
                            class="select select2 location-select"
                            required
                        >

                            ${buildLocationOptions(
                                selectedLocationIds
                            )}

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
                            name="configurations[${configIndex}][engine_id]"
                            class="select select2 engine-select"
                            required
                        >

                            ${buildEngineOptions(
                                configuration.engine_id
                            )}

                        </select>

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
                            name="configurations[${configIndex}][vendor_id]"
                            class="select select2 vendor-select"
                            disabled
                        >

                            ${buildVendorOptions(
                                selectedVendorId
                            )}

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

                            ${buildCategoryCheckboxes(
                                configIndex,
                                selectedCategoryIds
                            )}

                        </div>

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


                    <div class="table-responsive">

                        <table class="display data-table">

                            <thead>

                                <tr>

                                    <th>
                                        No
                                    </th>

                                    <th>
                                        Laminasi
                                    </th>

                                    <th>
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


                /*
                |--------------------------------------------------------------------------
                | PRODUCTION COST ID
                |--------------------------------------------------------------------------
                */

                const productionCostIdInput =
                    document.createElement('input');

                productionCostIdInput.type =
                    'hidden';

                productionCostIdInput.name =
                    `configurations[${configIndex}][production_cost_id]`;

                productionCostIdInput.value =
                    configuration.production_cost_id;


                configurationElement.appendChild(
                    productionCostIdInput
                );


                configurationContainer.appendChild(
                    configurationElement
                );


                /*
                |--------------------------------------------------------------------------
                | SELECT2 HEADER
                |--------------------------------------------------------------------------
                */

                initSelect2(
                    configurationElement
                );


                /*
                |--------------------------------------------------------------------------
                | DETAIL EXISTING
                |--------------------------------------------------------------------------
                */

                const details =
                    configuration.details || [];


                details.forEach(function(detail) {

                    addLaminationRow(
                        configurationElement,
                        configIndex,
                        detail
                    );

                });


                /*
                |--------------------------------------------------------------------------
                | TAMBAH LAMINASI
                |--------------------------------------------------------------------------
                */

                const btnAddLamination =
                    configurationElement.querySelector(
                        '.btn-add-lamination'
                    );


                btnAddLamination.addEventListener(
                    'click',
                    function() {

                        addLaminationRow(
                            configurationElement,
                            configIndex
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | UPDATE NAME
                |--------------------------------------------------------------------------
                */

                updateLaminationNames(
                    configurationElement,
                    configIndex
                );


                /*
                |--------------------------------------------------------------------------
                | UPDATE NUMBER
                |--------------------------------------------------------------------------
                */

                updateLaminationNumbers(
                    configurationElement
                );


                /*
                |--------------------------------------------------------------------------
                | UPDATE FORM STATE
                |--------------------------------------------------------------------------
                */

                updateLaminationForm(
                    configurationElement
                );


                /*
                |--------------------------------------------------------------------------
                | LOCATION CHANGE
                |--------------------------------------------------------------------------
                */

                const locationSelect =
                    configurationElement.querySelector(
                        '.location-select'
                    );


                if (locationSelect) {

                    $(locationSelect).on(
                        'change',
                        function() {

                            updateLaminationForm(
                                configurationElement
                            );

                        }
                    );

                }

            }


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


                    input.value =
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


                    if (input.value === '') {
                        return;
                    }


                    input.value =
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


                    const configuration =
                        row.closest(
                            '.configuration-card'
                        );


                    const tbody =
                        row.closest(
                            '.configuration-lamination-body'
                        );


                    if (
                        !configuration ||
                        !tbody
                    ) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | MINIMUM ONE DETAIL
                    |--------------------------------------------------------------------------
                    */

                    const rows =
                        tbody.querySelectorAll('tr');


                    if (rows.length <= 1) {

                        showToast(
                            'error',
                            'Minimal satu laminasi harus tersedia dalam konfigurasi.'
                        );

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DESTROY SELECT2
                    |--------------------------------------------------------------------------
                    */

                    row
                        .querySelectorAll('.select2')
                        .forEach(function(select) {

                            if (
                                $(select).hasClass(
                                    'select2-hidden-accessible'
                                )
                            ) {

                                $(select).select2(
                                    'destroy'
                                );

                            }

                        });


                    row.remove();


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE INDEX
                    |--------------------------------------------------------------------------
                    */

                    const configIndex =
                        Number(
                            configuration.dataset
                            .configurationIndex
                        );


                    updateLaminationNumbers(
                        configuration
                    );


                    updateLaminationNames(
                        configuration,
                        configIndex
                    );


                    updateLaminationForm(
                        configuration
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | VALIDATE CATEGORY
            |--------------------------------------------------------------------------
            */

            productionCostForm.addEventListener(
                'submit',
                async function(event) {

                    event.preventDefault();


                    const form =
                        this;


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE FORM STATE
                    |--------------------------------------------------------------------------
                    */

                    const configurationElements =
                        configurationContainer
                        .querySelectorAll(
                            '.configuration-card'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | HTML VALIDATION
                    |--------------------------------------------------------------------------
                    */

                    if (!form.checkValidity()) {

                        form.reportValidity();

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDASI KONFIGURASI
                    |--------------------------------------------------------------------------
                    */

                    if (
                        configurationElements.length === 0
                    ) {

                        showToast(
                            'error',
                            'Minimal satu konfigurasi harus tersedia.'
                        );

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDASI CATEGORY
                    |--------------------------------------------------------------------------
                    */

                    for (
                        let i = 0; i < configurationElements.length; i++
                    ) {

                        const configuration =
                            configurationElements[i];


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
                    | UPDATE NAME SEBELUM SUBMIT
                    |--------------------------------------------------------------------------
                    */

                    configurationElements.forEach(
                        function(
                            configuration,
                            index
                        ) {

                            updateLaminationNames(
                                configuration,
                                index
                            );

                        }
                    );


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
                            data.success
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
                            data.message ||
                            'Terjadi kesalahan saat memperbarui Production Cost Laminasi.'
                        );


                    } catch (error) {

                        console.error(
                            'Gagal memperbarui Production Cost Laminasi:',
                            error
                        );


                        showToast(
                            'error',
                            'Terjadi kesalahan saat memperbarui Production Cost Laminasi.'
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
            | RENDER EXISTING CONFIGURATIONS
            |--------------------------------------------------------------------------
            */

            configurations.forEach(
                function(
                    configuration,
                    configIndex
                ) {

                    renderConfiguration(
                        configuration,
                        configIndex
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | SESSION TOAST
            |--------------------------------------------------------------------------
            */

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

        });
    </script>
@endsection

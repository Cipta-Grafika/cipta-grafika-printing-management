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
                            BIAYA · MATERIAL
                        </span>

                        <p class="hero-sub">
                            Tambahkan ongkos produksi berdasarkan lokasi, engine, dan material. Satu konfigurasi bisa
                            berlaku untuk beberapa lokasi dan lebar sekaligus.
                        </p>

                    </div>

                </section>

                <div class="grid">

                    <section class="col-12 card">

                        <div class="card-head">

                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Konfigurasi Ongkos Produksi
                                </span>

                            </div>

                        </div>

                        <form method="POST" action="{{ route('admin_store_pc_finishing') }}" id="productionCostForm">

                            @csrf

                            {{-- =========================================================
                                KONFIGURASI PRODUCTION COST
                            ========================================================== --}}

                            <div id="configurationContainer"></div>

                            {{-- =========================================================
                                TAMBAH KONFIGURASI
                            ========================================================== --}}

                            <div style="margin-top: 16px;">
                                <button type="button" id="btnAddConfiguration" class="btn btn--ghost">

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

                                <a href="{{ route('admin_pc_finishings') }}" class="btn btn--ghost">

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
                document.getElementById('configurationContainer');

            const btnAddConfiguration =
                document.getElementById('btnAddConfiguration');

            const productionCostForm =
                document.getElementById('productionCostForm');

            let configurationIndex = 0;

            const materialsByEngine = @json($materialsByEngine);

            function isDigitalPrintConfiguration(configuration) {
                const engine = configuration.querySelector('.engine-select');
                const option = engine.options[engine.selectedIndex];

                return option && option.textContent.trim().toLowerCase() === 'digital print a3+';
            }

            function filterMaterialsByEngine(configuration, rows) {
                const engineId = $(configuration).find('.engine-select').val();
                const materials = materialsByEngine[engineId] || [];

                $(rows).find('.material-select').each(function() {
                    const select = $(this);
                    const previousValue = select.val();

                    select.empty().append(new Option('Pilih Material', ''));

                    materials.forEach(function(material) {
                        select.append(new Option(
                            `${material.material_name} (${material.category_name})`,
                            material.id
                        ));
                    });

                    const stillAvailable = materials.some(function(material) {
                        return String(material.id) === previousValue;
                    });

                    select.val(stillAvailable ? previousValue : '');

                    if (previousValue && !stillAvailable) {
                        select.trigger('change');
                    } else {
                        select.trigger('change.select2');
                    }
                });
            }

            function initSelect2(element) {
                $(element)
                    .find('.select2')
                    .each(function() {
                        if ($(this).hasClass('select2-hidden-accessible')) {
                            return;
                        }

                        $(this).select2({
                            width: '100%'
                        });
                    });
            }

            function validateLocationSelection(select) {
                const selectedOptions = Array.from(select.selectedOptions);

                const selectedLocations = selectedOptions.map(function(option) {
                    return option.textContent.trim().toLowerCase();
                });

                const hasOutsourcing = selectedLocations.some(function(location) {
                    return location === 'outsourcing';
                });

                if (hasOutsourcing && selectedLocations.length > 1) {
                    return false;
                }

                return true;
            }

            function isOutsourcingLocation(select) {

                const selectedOptions =
                    Array.from(select.selectedOptions);

                return selectedOptions.some(function(option) {

                    return option.textContent
                        .trim()
                        .toLowerCase() === 'outsourcing';

                });
            }

            function formatNumberInput(value) {
                if (value === '') return '';

                value = value.replace(/\D/g, '');

                if (value === '') return '';

                return Number(value).toLocaleString('id-ID');
            }

            function formatRupiah(value) {
                if (value === '') return '';

                value = value.replace(/\D/g, '');

                if (value === '') return '';

                return Number(value).toLocaleString('id-ID');
            }

            function loadMaterialSizes(row) {

                const materialSelect =
                    $(row).find('.material-select');

                const widthSelect =
                    $(row).find('.width-select');

                materialSelect
                    .off('change.material')
                    .on('change.material', function() {

                        const materialId = $(this).val();

                        widthSelect.empty();

                        if (!materialId || isDigitalPrintConfiguration(row.closest('.configuration-card'))) {
                            widthSelect.trigger('change');
                            return;
                        }

                        fetch(
                                `{{ route('get_material_sizes') }}?id=${materialId}`
                            )
                            .then(response => {

                                if (!response.ok) {
                                    throw new Error(
                                        'Gagal mengambil ukuran material.'
                                    );
                                }

                                return response.json();
                            })
                            .then(data => {

                                data.forEach(function(size) {

                                    widthSelect.append(
                                        new Option(
                                            `${size.width} ${size.unit}`,
                                            size.id
                                        )
                                    );

                                });

                                widthSelect.trigger('change');

                            })
                            .catch(error => {

                                console.error(
                                    'Gagal mengambil ukuran material:',
                                    error
                                );

                            });

                    });
            }

            function updateConfigurationNumbers() {

                const configurations =
                    configurationContainer.querySelectorAll(
                        '.configuration-card'
                    );

                configurations.forEach(function(configuration, index) {

                    const number = index + 1;

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

                    if (!deleteButton) return;

                    if (configurations.length === 1) {

                        deleteButton.disabled = true;
                        deleteButton.style.opacity = '0.5';
                        deleteButton.style.cursor = 'not-allowed';
                        deleteButton.style.display = 'none';

                    } else {

                        deleteButton.disabled = false;
                        deleteButton.style.opacity = '';
                        deleteButton.style.cursor = '';
                        deleteButton.style.display = '';

                    }

                });
            }

            function addMaterialRow(
                configuration,
                configIndex,
                materialIndex
            ) {

                const tbody =
                    configuration.querySelector(
                        '.configuration-material-body'
                    );

                const row =
                    document.createElement('tr');

                row.innerHTML = `
                    <td style="width: 300px;">
                        <select
                            name="configurations[${configIndex}][items][${materialIndex}][material_id]"
                            class="select select2 material-select"
                            required
                        >
                            <option value="">
                                Pilih Material
                            </option>

                        </select>
                    </td>

                    <td class="width-cell">
                        <select
                            name="configurations[${configIndex}][items][${materialIndex}][width_ids][]"
                            class="select select2 width-select"
                            multiple="multiple"
                            required
                        ></select>
                    </td>

                    {{-- Harga Permeter --}}
                    <td class="price-per-meter-cell">
                        <input
                            type="text"
                            name="configurations[${configIndex}][items][${materialIndex}][price_per_meter]"
                            class="input rupiah-input price-per-meter-input"
                            inputmode="decimal"
                            autocomplete="off"
                            placeholder="Contoh: 6600.00"
                            required
                        >
                    </td>

                    {{-- Ongkos Produksi --}}
                    <td class="production-cost-cell">
                        <input
                            type="text"
                            name="configurations[${configIndex}][items][${materialIndex}][production_cost]"
                            class="input rupiah-input production-cost-input"
                            inputmode="decimal"
                            autocomplete="off"
                            placeholder="Contoh: 9740.00"
                            required
                        >
                    </td>

                    {{-- Ongkos Finishing --}}
                    <td class="finishing-cost-cell">
                        <input
                            type="text"
                            name="configurations[${configIndex}][items][${materialIndex}][finishing_cost]"
                            class="input rupiah-input finishing-cost-input"
                            inputmode="decimal"
                            autocomplete="off"
                            placeholder="Contoh: 600.50"
                            required
                        >
                    </td>

                    {{-- Harga Vendor --}}
                    <td
                        class="vendor-price-cell"
                        style="display: none;"
                    >
                        <input
                            type="text"
                            name="configurations[${configIndex}][items][${materialIndex}][vendor_price]"
                            class="input rupiah-input vendor-price-input"
                            inputmode="decimal"
                            autocomplete="off"
                            placeholder="Contoh: 6600.00"
                            disabled
                            required
                        >
                    </td>

                    <td class="digital-price-cell" style="display: none;">
                        <input
                            type="text"
                            name="configurations[${configIndex}][items][${materialIndex}][unit_price]"
                            class="input rupiah-input"
                            inputmode="numeric"
                            autocomplete="off"
                            placeholder="Contoh: 5.000"
                            disabled
                        >
                    </td>

                    <td class="digital-price-cell" style="display: none;">
                        <input
                            type="text"
                            name="configurations[${configIndex}][items][${materialIndex}][click_price]"
                            class="input rupiah-input"
                            inputmode="numeric"
                            autocomplete="off"
                            placeholder="Contoh: 1.000"
                            disabled
                        >
                    </td>

                    <td class="text-center">
                        <button
                            type="button"
                            class="btn--icon btn-remove-material"
                            aria-label="Hapus Material"
                        >
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </td>
                `;

                tbody.appendChild(row);

                initSelect2(row);

                loadMaterialSizes(row);

                filterMaterialsByEngine(configuration, row);
            }

            function updateMaterialForm(configuration) {

                const isDigital = isDigitalPrintConfiguration(configuration);
                const locationSelect =
                    configuration.querySelector('.location-select');

                const isOutsourcing =
                    isOutsourcingLocation(locationSelect);

                const vendorGroup =
                    configuration.querySelector('.vendor-group');

                const vendorSelect =
                    configuration.querySelector('.vendor-select');

                if (vendorGroup && vendorSelect) {
                    if (isOutsourcing) {
                        vendorGroup.style.display = '';
                        vendorSelect.disabled = false;
                        vendorSelect.required = true;
                    } else {
                        vendorGroup.style.display = 'none';
                        vendorSelect.disabled = true;
                        vendorSelect.required = false;

                        $(vendorSelect)
                            .val(null)
                            .trigger('change');
                    }
                }

                const table =
                    configuration.querySelector(
                        '.configuration-material-body'
                    ).closest('table');

                const headers =
                    table.querySelectorAll('thead th');

                const pricePerMeterHeaders =
                    table.querySelectorAll('.price-per-meter-header');

                const productionCostHeaders =
                    table.querySelectorAll('.production-cost-header');

                const finishingCostHeaders =
                    table.querySelectorAll('.finishing-cost-header');

                const vendorPriceHeaders =
                    table.querySelectorAll('.vendor-price-header');

                table.querySelectorAll('.width-header, .width-cell').forEach(function(element) {
                    element.style.display = isDigital ? 'none' : '';
                });

                table.querySelectorAll('.width-select').forEach(function(select) {
                    select.disabled = isDigital;
                    select.required = !isDigital;
                });

                table.querySelectorAll('.digital-price-header, .digital-price-cell').forEach(function(element) {
                    element.style.display = isDigital ? '' : 'none';
                });

                table.querySelectorAll('.digital-price-cell input').forEach(function(input) {
                    input.disabled = !isDigital;
                    input.required = isDigital;
                });

                if (isDigital) {
                    table.querySelectorAll(
                        '.price-per-meter-header, .production-cost-header, .finishing-cost-header, ' +
                        '.vendor-price-header, .price-per-meter-cell, .production-cost-cell, ' +
                        '.finishing-cost-cell, .vendor-price-cell'
                    ).forEach(function(element) {
                        element.style.display = 'none';
                    });

                    table.querySelectorAll(
                        '.price-per-meter-input, .production-cost-input, ' +
                        '.finishing-cost-input, .vendor-price-input'
                    ).forEach(function(input) {
                        input.disabled = true;
                        input.required = false;
                    });

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | HEADER
                |--------------------------------------------------------------------------
                */

                pricePerMeterHeaders.forEach(function(header) {
                    header.style.display =
                        isOutsourcing ? 'none' : '';
                });

                productionCostHeaders.forEach(function(header) {
                    header.style.display =
                        isOutsourcing ? 'none' : '';
                });

                finishingCostHeaders.forEach(function(header) {
                    header.style.display =
                        isOutsourcing ? 'none' : '';
                });

                vendorPriceHeaders.forEach(function(header) {
                    header.style.display =
                        isOutsourcing ? '' : 'none';
                });

                /*
                |--------------------------------------------------------------------------
                | ROW
                |--------------------------------------------------------------------------
                */

                configuration
                    .querySelectorAll(
                        '.configuration-material-body tr'
                    )
                    .forEach(function(row) {

                        const pricePerMeterCell =
                            row.querySelector(
                                '.price-per-meter-cell'
                            );

                        const productionCostCell =
                            row.querySelector(
                                '.production-cost-cell'
                            );

                        const finishingCostCell =
                            row.querySelector(
                                '.finishing-cost-cell'
                            );

                        const vendorPriceCell =
                            row.querySelector(
                                '.vendor-price-cell'
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

                        if (isOutsourcing) {

                            pricePerMeterCell.style.display = 'none';
                            productionCostCell.style.display = 'none';
                            finishingCostCell.style.display = 'none';

                            vendorPriceCell.style.display = '';

                            pricePerMeterInput.disabled = true;
                            productionCostInput.disabled = true;
                            finishingCostInput.disabled = true;

                            pricePerMeterInput.required = false;
                            productionCostInput.required = false;
                            finishingCostInput.required = false;

                            vendorPriceInput.disabled = false;
                            vendorPriceInput.required = true;

                        } else {

                            pricePerMeterCell.style.display = '';
                            productionCostCell.style.display = '';
                            finishingCostCell.style.display = '';

                            vendorPriceCell.style.display = 'none';

                            pricePerMeterInput.disabled = false;
                            productionCostInput.disabled = false;
                            finishingCostInput.disabled = false;

                            pricePerMeterInput.required = true;
                            productionCostInput.required = true;
                            finishingCostInput.required = true;

                            vendorPriceInput.disabled = true;
                            vendorPriceInput.required = false;
                        }

                    });
            }

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

                        <div class="form-grid">

                            <div class="field">

                                <label class="field-label">
                                    Lokasi
                                    <span class="req">*</span>
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

                            <div class="field">

                                <label class="field-label">
                                    Engine
                                    <span class="req">*</span>
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

                            <div
                                class="field vendor-group"
                                style="display: none;">
                                <label class="field-label">
                                    Vendor
                                    <span class="req">*</span>
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
                                    Detail Ongkos Produksi
                                </label>

                                <button
                                    type="button"
                                    class="btn btn--ghost btn-add-material"
                                >
                                    + Tambah Material
                                </button>

                            </div>

                            <div class="table-responsive">

                                <table class="display data-table">

                                    <thead>
                                        <tr>
                                            <th>Material</th>

                                            <th class="width-header">Lebar Dipakai</th>

                                            <th class="price-per-meter-header">
                                                Harga Permeter (Rp)
                                            </th>

                                            <th class="production-cost-header">
                                                Ongkos Produksi (Rp)
                                            </th>

                                            <th class="finishing-cost-header">
                                                Ongkos Finishing (Rp)
                                            </th>

                                            <th
                                                class="vendor-price-header"
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
                                        class="configuration-material-body"
                                    ></tbody>

                                </table>

                            </div>

                        </div>

                    </div>
                `;

                configurationContainer.appendChild(
                    configuration
                );

                initSelect2(configuration);

                const locationSelect =
                    configuration.querySelector('.location-select');

                $(locationSelect).on('change.outsourcing', function() {
                    if (!validateLocationSelection(this)) {
                        const selectedValues = $(this).val() || [];

                        const validValues = selectedValues.filter(function(value) {
                            const option = locationSelect.querySelector(
                                'option[value="' + value + '"]'
                            );

                            if (!option) {
                                return true;
                            }

                            return option.textContent.trim().toLowerCase() !== 'outsourcing';
                        });

                        $(this).val(validValues).trigger('change.select2');

                        showToast(
                            'error',
                            'Lokasi Outsourcing tidak dapat digabung dengan lokasi lainnya. Karena memiliki komponen berbeda.'
                        );
                    }
                });

                $(locationSelect).on(
                    'change.materialForm',
                    function() {

                        updateMaterialForm(
                            configuration
                        );

                    }
                );

                let materialIndex = 0;

                addMaterialRow(
                    configuration,
                    currentIndex,
                    materialIndex
                );

                materialIndex++;

                updateMaterialForm(configuration);

                const btnAddMaterial =
                    configuration.querySelector(
                        '.btn-add-material'
                    );

                btnAddMaterial.addEventListener(
                    'click',
                    function() {

                        addMaterialRow(
                            configuration,
                            currentIndex,
                            materialIndex
                        );

                        materialIndex++;

                        updateMaterialForm(configuration);
                    }
                );

                const btnRemoveConfiguration =
                    configuration.querySelector(
                        '.btn-remove-configuration'
                    );

                btnRemoveConfiguration.addEventListener(
                    'click',
                    function() {

                        const configurations =
                            configurationContainer.querySelectorAll(
                                '.configuration-card'
                            );

                        if (configurations.length <= 1) {
                            return;
                        }

                        configuration
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

                        configuration.remove();

                        updateConfigurationNumbers();

                    }
                );


                const engineSelect =
                    configuration.querySelector('.engine-select');

                $(engineSelect).on('change.digitalPrint', function() {

                    updateMaterialForm(configuration);

                    filterMaterialsByEngine(
                        configuration,
                        configuration.querySelectorAll('.configuration-material-body tr')
                    );
                });

                updateConfigurationNumbers();

                configurationIndex++;
            }

            btnAddConfiguration.addEventListener(
                'click',
                function() {
                    addConfiguration();
                }
            );

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

            configurationContainer.addEventListener(
                'click',
                function(event) {

                    const deleteButton =
                        event.target.closest(
                            '.btn-remove-material'
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
                            '.configuration-material-body'
                        );

                    if (!tbody) {
                        return;
                    }

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

                }
            );

            /*
            |--------------------------------------------------------------------------
            | SUBMIT PRODUCTION COST
            |--------------------------------------------------------------------------
            */

            productionCostForm.addEventListener(
                'submit',
                async function(event) {

                    event.preventDefault();

                    const form = this;

                    /*
                    |--------------------------------------------------------------------------
                    | Validasi HTML bawaan form
                    |--------------------------------------------------------------------------
                    */

                    if (!form.checkValidity()) {

                        form.reportValidity();

                        return;
                    }

                    const locationSelects =
                        configurationContainer.querySelectorAll('.location-select');

                    for (const locationSelect of locationSelects) {
                        if (!validateLocationSelection(locationSelect)) {
                            showToast(
                                'error',
                                'Lokasi Outsourcing tidak dapat digabung dengan lokasi lainnya. Karena memiliki komponen berbeda.'
                            );
                            return;
                        }
                    }

                    const submitButton =
                        form.querySelector(
                            'button[type="submit"]'
                        );

                    const originalText =
                        submitButton.innerHTML;

                    submitButton.disabled = true;

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
                        | Berhasil
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

                            /*
                            |--------------------------------------------------------------------------
                            | Tetap menuju halaman Production Cost
                            | seperti behavior store sebelumnya.
                            |--------------------------------------------------------------------------
                            */

                            setTimeout(function() {

                                window.location.href =
                                    "{{ route('admin_pc_finishings') }}";

                            }, 800);

                            return;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Warning / Error dari controller
                        |--------------------------------------------------------------------------
                        */

                        showToast(
                            data.type || 'error',
                            data.message ||
                            'Terjadi kesalahan saat menyimpan Production Cost.'
                        );

                    } catch (error) {

                        console.error(
                            'Gagal menyimpan Production Cost:',
                            error
                        );

                        showToast(
                            'error',
                            'Terjadi kesalahan saat menyimpan Production Cost.'
                        );

                    } finally {

                        submitButton.disabled = false;

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

        document.addEventListener('DOMContentLoaded', function() {

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

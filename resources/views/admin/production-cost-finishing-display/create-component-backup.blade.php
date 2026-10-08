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
                            Kelola komponen berdasarkan konfigurasi Display,
                            lokasi, mesin, dan biaya produksi yang telah tersimpan.
                        </p>

                    </div>

                </section>

                <div class="grid">

                    <section class="col-12 card">

                        {{-- CARD HEADER --}}
                        <div class="card-head">

                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Tambah Komponen Display
                                </span>

                            </div>

                        </div>

                        <div class="display-form-body">

                            <form id="displayComponentForm" method="POST"
                                action="{{ route('admin_store_component_pc_finishing_display', ['id' => request()->route('id')]) }}">
                                @csrf

                                <input type="hidden" name="configuration_id"
                                    value="{{ $configuration->configuration_id }}">

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

                                        <div class="display-form-head">

                                            <div class="display-form-head-title" style="margin-bottom: 20px;">

                                                <div>
                                                    <span class="eyebrow">
                                                        KOMPONEN DISPLAY
                                                    </span>
                                                </div>

                                            </div>


                                            <div class="component-workspace" style="margin-bottom: 20px;">

                                                <div class="display-components-container" data-component-container
                                                    data-location-id="{{ $configuration->location_id }}"
                                                    data-engine-id="{{ $configuration->engine_id }}">

                                                    {{-- HEADER KOMPONEN --}}
                                                    <div class="display-component-header">

                                                        <div class="component-column">
                                                            <label class="field-label">
                                                                Material
                                                                <span class="req">*</span>
                                                            </label>
                                                        </div>

                                                        <div class="component-column">
                                                            <label class="field-label">
                                                                Laminasi
                                                            </label>
                                                        </div>

                                                        <div class="component-column">
                                                            <label class="field-label">
                                                                Nilai Pembulatan (Rp)
                                                            </label>
                                                        </div>

                                                        <div class="component-column component-column-action">
                                                            <label class="field-label">
                                                                Aksi
                                                            </label>
                                                        </div>

                                                    </div>

                                                    {{-- BODY KOMPONEN --}}
                                                    <div class="display-component-list" data-component-list>

                                                        <div class="display-component-row" data-component-index="0">

                                                            {{-- MATERIAL --}}
                                                            <div class="component-column">
                                                                <select name="components[0][material_id]"
                                                                    class="select component-select2" data-material-id
                                                                    required>
                                                                    <option value="">Pilih Material</option>
                                                                </select>
                                                            </div>

                                                            {{-- LAMINASI --}}
                                                            <div class="component-column">
                                                                <select name="components[0][lamination_id]"
                                                                    class="select component-select2" data-lamination-id>
                                                                    <option value="">Pilih Laminasi</option>
                                                                </select>
                                                            </div>

                                                            {{-- PEMBULATAN --}}
                                                            <input type="text" name="components[0][rounding_value]"
                                                                class="input rupiah-format" placeholder="Nilai pembulatan"
                                                                inputmode="numeric" autocomplete="off">

                                                            {{-- AKSI --}}
                                                            <div class="component-column component-column-action">
                                                                <button type="button" class="btn btn--ghost"
                                                                    data-remove-component title="Hapus komponen">
                                                                    <i class="bi bi-trash3-fill"></i>
                                                                </button>
                                                            </div>

                                                        </div>

                                                    </div>


                                                </div>

                                            </div>


                                            {{-- TAMBAH KOMPONEN --}}
                                            <div class="component-actions">
                                                <button type="button" class="btn btn--ghost" data-add-component>
                                                    <i class="bi bi-plus-lg"></i>
                                                    Tambah Komponen
                                                </button>
                                            </div>
                                        </div>

                                    </div>

                                </section>

                                {{-- =========================================
                                    ACTION
                                ========================================== --}}
                                <div class="form-actions">

                                    <span class="badge dot success">
                                        Siap disimpan
                                    </span>

                                    <span class="spacer"></span>

                                    <a href="{{ route('admin_pc_finishing_displays') }}" class="btn btn--ghost">
                                        <i class="bi bi-arrow-left"></i>
                                        Batal
                                    </a>

                                    <button type="submit" class="btn btn--primary" id="submitComponentButton">
                                        Simpan Komponen
                                    </button>

                                </div>
                            </form>
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
        $(document).ready(function() {

            /*
            |--------------------------------------------------------------------------
            | ELEMENT
            |--------------------------------------------------------------------------
            */

            const componentContainer = document.querySelector(
                '[data-component-container]'
            );

            const addComponentButton = document.querySelector(
                '[data-add-component]'
            );

            const componentForm = document.querySelector(
                '#displayComponentForm'
            );

            const componentUrl = @json(route('admin_pc_finishing_display_components'));

            let availableMaterials = [];
            let availableLaminations = [];


            if (!componentContainer || !addComponentButton || !componentForm) {
                console.error('Container, tombol tambah, atau form komponen tidak ditemukan.');
                return;
            }

            const locationId = componentContainer.dataset.locationId;
            const engineId = componentContainer.dataset.engineId;

            /*
            |--------------------------------------------------------------------------
            | TEMPLATE BARIS KOMPONEN
            |--------------------------------------------------------------------------
            */


            const componentTemplate = `
                <div class="display-component-row"
                    data-component-index="__INDEX__">

                    {{-- MATERIAL --}}
                    <div class="component-column">
                        <select
                            name="components[__INDEX__][material_id]"
                            class="select component-select2"
                            data-material-id
                            required
                        >
                            <option value="">Pilih Material</option>
                        </select>
                    </div>

                    {{-- LAMINASI --}}
                    <div class="component-column">
                        <select
                            name="components[__INDEX__][lamination_id]"
                            class="select component-select2"
                            data-lamination-id
                        >
                            <option value="">Pilih Laminasi</option>
                        </select>
                    </div>

                    {{-- PEMBULATAN --}}
                    <div class="component-column">
                        <input
                            type="text"
                            name="components[__INDEX__][rounding_value]"
                            class="input rupiah-format"
                            placeholder="Nilai pembulatan"
                        >
                    </div>

                    {{-- AKSI --}}
                    <div class="component-column component-column-action">
                        <button
                            type="button"
                            class="btn btn--ghost"
                            data-remove-component
                            title="Hapus komponen"
                        >
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </div>

                </div>
            `;
            /*
            |--------------------------------------------------------------------------
            | SELECT2
            |--------------------------------------------------------------------------
            */

            function initComponentSelect2(container) {

                $(container)
                    .find('.component-select2')
                    .each(function() {

                        const $select = $(this);

                        if ($select.hasClass('select2-hidden-accessible')) {
                            return;
                        }

                        $select.select2({
                            width: '100%',
                            placeholder: 'Pilih pilihan',
                            // allowClear: true
                        });

                    });

            }


            /*
            |--------------------------------------------------------------------------
            | POPULATE MATERIAL DAN LAMINASI
            |--------------------------------------------------------------------------
            */

            function populateComponentOptions(container) {

                $(container).find('[data-material-id]').each(function() {

                    const select = this;

                    select.innerHTML = '<option value="">Pilih Material</option>';

                    availableMaterials.forEach(function(item) {

                        const option = new Option(
                            item.name,
                            item.id,
                            false,
                            false
                        );

                        select.add(option);

                    });

                    $(select).val(null).trigger('change');

                });

                $(container).find('[data-lamination-id]').each(function() {

                    const select = this;

                    // select.innerHTML = '<option value="">Pilih Laminasi</option>';
                    select.innerHTML = '<option value="">Tanpa Laminasi</option>';

                    availableLaminations.forEach(function(item) {

                        const option = new Option(
                            item.name,
                            item.id,
                            false,
                            false
                        );

                        select.add(option);

                    });

                    $(select).val(null).trigger('change');

                });

            }



            /*
            |--------------------------------------------------------------------------
            | LOAD DATA MATERIAL DAN LAMINASI
            |--------------------------------------------------------------------------
            */

            async function loadComponentOptions() {

                if (!locationId || !engineId) {

                    showToast(
                        'error',
                        'Lokasi atau Engine konfigurasi tidak ditemukan.'
                    );

                    return;

                }

                try {

                    const params = new URLSearchParams({
                        location_id: locationId,
                        engine_id: engineId
                    });

                    const response = await fetch(
                        `${componentUrl}?${params.toString()}`, {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    );

                    if (!response.ok) {
                        throw new Error('Gagal mengambil data komponen.');
                    }

                    const data = await response.json();

                    availableMaterials = data.materials || [];
                    availableLaminations = data.laminations || [];

                    populateComponentOptions(componentContainer);

                } catch (error) {

                    console.error('Error load komponen:', error);

                    showToast(
                        'error',
                        'Gagal memuat pilihan Material dan Laminasi.'
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | GET ROWS
            |--------------------------------------------------------------------------
            */

            function getComponentRows() {

                return componentContainer.querySelectorAll(
                    '.display-component-row'
                );

            }

            /*
            |--------------------------------------------------------------------------
            | UPDATE INDEX
            |--------------------------------------------------------------------------
            */

            function updateComponentIndex() {

                const rows = getComponentRows();

                rows.forEach(function(row, index) {

                    row.dataset.componentIndex = index;

                    const materialInput = row.querySelector(
                        '[data-material-id]'
                    );

                    const laminationInput = row.querySelector(
                        '[data-lamination-id]'
                    );

                    const roundingInput = row.querySelector(
                        'input[name*="[rounding_value]"]'
                    );


                    if (materialInput) {
                        materialInput.name =
                            `components[${index}][material_id]`;
                    }

                    if (laminationInput) {
                        laminationInput.name =
                            `components[${index}][lamination_id]`;
                    }

                    if (roundingInput) {
                        roundingInput.name =
                            `components[${index}][rounding_value]`;
                    }

                });

            }


            /*
            |--------------------------------------------------------------------------
            | FORMAT NILAI RUPIAH
            |--------------------------------------------------------------------------
            */

            function formatRupiah(value) {

                const numericValue = String(value).replace(/\D/g, '');

                if (!numericValue) {
                    return '';
                }

                return numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

            }

            function initRupiahFormat(container) {

                $(container)
                    .find('.rupiah-format')
                    .each(function() {

                        const input = this;

                        if (input.dataset.rupiahInitialized === 'true') {
                            return;
                        }

                        input.dataset.rupiahInitialized = 'true';

                        input.addEventListener('input', function() {

                            const cursorPosition = this.selectionStart;
                            const oldValue = this.value;
                            const oldLength = oldValue.length;

                            this.value = formatRupiah(oldValue);

                            const newLength = this.value.length;

                            const newPosition = Math.max(
                                0,
                                cursorPosition + (newLength - oldLength)
                            );

                            this.setSelectionRange(
                                newPosition,
                                newPosition
                            );

                        });

                    });

            }

            /*
            |--------------------------------------------------------------------------
            | VALIDASI DAN NORMALISASI SEBELUM SUBMIT
            |--------------------------------------------------------------------------
            */

            componentForm.addEventListener('submit', function(event) {

                const rows = getComponentRows();

                if (rows.length === 0) {
                    event.preventDefault();

                    showToast(
                        'error',
                        'Minimal harus terdapat 1 baris komponen.'
                    );

                    return;
                }

                let isValid = true;

                rows.forEach(function(row) {

                    const materialInput = row.querySelector(
                        '[data-material-id]'
                    );

                    const laminationInput = row.querySelector(
                        '[data-lamination-id]'
                    );

                    const roundingInput = row.querySelector(
                        'input[name*="[rounding_value]"]'
                    );

                    if (
                        !materialInput ||
                        !materialInput.value
                    ) {
                        isValid = false;
                    }

                    if (
                        !roundingInput ||
                        roundingInput.value.trim() === ''
                    ) {
                        isValid = false;
                    }

                });

                if (!isValid) {
                    event.preventDefault();

                    showToast(
                        'error',
                        'Material dan Nilai Pembulatan wajib diisi pada setiap baris.'
                    );

                    return;
                }

                componentContainer
                    .querySelectorAll('.rupiah-format')
                    .forEach(function(input) {

                        input.value = input.value.replace(/\./g, '');

                    });

            });

            /*
            |--------------------------------------------------------------------------
            | TAMBAH BARIS
            |--------------------------------------------------------------------------
            */


            function addComponentRow() {

                const newIndex = getComponentRows().length;

                const html = componentTemplate.replace(
                    /__INDEX__/g,
                    newIndex
                );

                componentContainer.insertAdjacentHTML(
                    'beforeend',
                    html
                );

                const newRow = componentContainer.lastElementChild;

                if (newRow) {
                    populateComponentOptions(newRow);
                    initComponentSelect2(newRow);
                    initRupiahFormat(newRow);
                }

                updateComponentIndex();
            }


            /*
            |--------------------------------------------------------------------------
            | EVENT HAPUS BARIS
            |--------------------------------------------------------------------------
            */

            $(componentContainer).on(
                'click',
                '[data-remove-component]',
                function(event) {

                    event.preventDefault();
                    event.stopPropagation();

                    console.log('Tombol hapus diklik');

                    const row = this.closest(
                        '.display-component-row'
                    );

                    if (!row) {
                        console.error('Baris komponen tidak ditemukan.');
                        return;
                    }

                    const rows = getComponentRows();

                    console.log('Jumlah baris:', rows.length);

                    /*
                    |--------------------------------------------------------------------------
                    | VALIDASI MINIMAL 1 BARIS
                    |--------------------------------------------------------------------------
                    */

                    if (rows.length <= 1) {

                        showToast(
                            'error',
                            'Minimal harus terdapat 1 baris komponen.'
                        );

                        return;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | HANCURKAN SELECT2
                    |--------------------------------------------------------------------------
                    */

                    $(row)
                        .find('.component-select2')
                        .each(function() {

                            const $select = $(this);

                            if ($select.hasClass('select2-hidden-accessible')) {
                                $select.select2('destroy');
                            }

                        });

                    /*
                    |--------------------------------------------------------------------------
                    | HAPUS BARIS
                    |--------------------------------------------------------------------------
                    */

                    row.remove();

                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE INDEX
                    |--------------------------------------------------------------------------
                    */

                    updateComponentIndex();

                    console.log('Baris berhasil dihapus.');

                }
            );

            /*
            |--------------------------------------------------------------------------
            | EVENT TAMBAH
            |--------------------------------------------------------------------------
            */

            addComponentButton.addEventListener('click', function(event) {

                event.preventDefault();

                addComponentRow();

            });

            /*
            |--------------------------------------------------------------------------
            | INITIALIZATION
            |--------------------------------------------------------------------------
            */

            initComponentSelect2(componentContainer);
            initRupiahFormat(componentContainer);

            updateComponentIndex();
            loadComponentOptions();

        });
    </script>
@endsection

@extends('admin_master')

@section('contents')


    <div class="shell display-edit-page">

        <div data-shell-sidebar></div>

        <div class="main">

            <div data-shell-topbar></div>

            <main class="content">

                {{-- PAGE HEADER --}}
                <section class="edit-page-heading">

                    <div class="edit-heading-content">

                        <div class="edit-heading-icon">
                            <i class="bi bi-pencil-square"></i>
                        </div>

                        <div>
                            <span class="edit-eyebrow">
                                DISPLAY / COMPONENT
                            </span>

                            <h1>Edit Display Product</h1>

                            <p>
                                Perbarui informasi Display, konfigurasi produksi,
                                serta harga komponen.
                            </p>
                        </div>

                    </div>

                    <span class="edit-status">
                        <i class="bi bi-pencil"></i>
                        Mode Edit
                    </span>

                </section>

                <form id="formEditDisplayComponent" method="POST"
                    action="{{ route('admin_update_component_pc_finishing_display', ['id' => base64_encode($displayProduct->id)]) }}">

                    @csrf
                    @method('PUT')

                    {{-- =========================================
                        INFORMASI DISPLAY
                    ========================================== --}}
                    <section class="edit-panel">

                        <div class="edit-panel-heading">

                            <div class="edit-section-icon">
                                <i class="bi bi-display"></i>
                            </div>

                            <div>
                                <h2>Informasi Display</h2>
                                <p>Identitas utama Display Product.</p>
                            </div>

                        </div>

                        <div class="edit-form-grid">

                            {{-- NAMA --}}
                            <div class="edit-field">
                                <label for="display_name">
                                    Nama Display
                                    <span class="required">*</span>
                                </label>

                                <select id="display_name" name="display_product_id" class="select component-select2"
                                    required>

                                    <option value="">Pilih Nama Display</option>

                                    @foreach ($displayProducts as $displayProductOption)
                                        <option value="{{ $displayProductOption->id }}"
                                            {{ old('display_product_id', $displayProduct->display_product_id) == $displayProductOption->id ? 'selected' : '' }}>
                                            {{ $displayProductOption->display_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- KATEGORI --}}
                            <div class="edit-field">

                                <label for="category_name">
                                    Kategori
                                </label>

                                <input type="text" id="category_name" value="{{ $displayProduct->category_name ?? '-' }}"
                                    readonly>

                                <input type="hidden" name="category_id" value="{{ $displayProduct->category_id }}">

                            </div>

                            {{-- PANJANG --}}
                            <div class="edit-field">
                                <label for="length">
                                    Panjang (cm)
                                    <span class="required">*</span>
                                </label>

                                <input type="number" id="length" name="length" min="0" step="0.01"
                                    value="{{ old('length', number_format((float) $displayProduct->length, 0, '.', '')) }}"
                                    readonly>
                            </div>

                            {{-- LEBAR --}}
                            <div class="edit-field">
                                <label for="width">
                                    Lebar (cm)
                                    <span class="required">*</span>
                                </label>

                                <input type="number" id="width" name="width" min="0" step="0.01"
                                    value="{{ old('width', number_format((float) $displayProduct->width, 0, '.', '')) }}"
                                    readonly>
                            </div>

                        </div>

                    </section>

                    {{-- =========================================
                        HARGA RANGKA
                    ========================================== --}}
                    <section class="edit-panel">

                        <div class="edit-panel-heading">

                            <div class="edit-section-icon">
                                <i class="bi bi-rulers"></i>
                            </div>

                            <div>
                                <h2>Harga Rangka</h2>
                                <p>Perbarui harga jual rangka Display.</p>
                            </div>

                        </div>

                        <div class="edit-price-grid">

                            <div class="edit-field">

                                <label for="frame_general_price">
                                    Harga Umum
                                </label>

                                <div class="edit-input-prefix">
                                    {{-- <span>Rp</span> --}}

                                    <input type="text" id="frame_general_price" name="frame_price[general_price]"
                                        min="0" step="0.01" class="js-rupiah"
                                        value="{{ old('frame_price.general_price', $framePrice->general_price ?? 0) }}"
                                        readonly>
                                </div>

                            </div>

                            <div class="edit-field">

                                <label for="frame_division_price">
                                    Harga Divisi
                                </label>

                                <div class="edit-input-prefix">
                                    {{-- <span>Rp</span> --}}

                                    <input type="text" id="frame_division_price" name="frame_price[division_price]"
                                        min="0" step="0.01" class="js-rupiah"
                                        value="{{ old('frame_price.division_price', $framePrice->division_price ?? 0) }}"
                                        readonly>
                                </div>

                            </div>

                            <div class="edit-field">

                                <label for="frame_plain_price">
                                    Harga Polos
                                </label>

                                <div class="edit-input-prefix">
                                    {{-- <span>Rp</span> --}}

                                    <input type="text" id="frame_plain_price" name="frame_price[plain_price]"
                                        min="0" step="0.01" class="js-rupiah"
                                        value="{{ old('frame_price.plain_price', $framePrice->plain_price ?? 0) }}"
                                        readonly>
                                </div>

                            </div>

                        </div>

                    </section>

                    {{-- =========================================
                        KONFIGURASI PRODUKSI
                    ========================================== --}}
                    <section class="edit-panel">

                        <div class="edit-panel-heading">

                            <div class="edit-section-icon">
                                <i class="bi bi-gear"></i>
                            </div>

                            <div>
                                <h2>Konfigurasi Produksi</h2>
                                <p>Biaya produksi berdasarkan konfigurasi yang tersimpan.</p>
                            </div>

                        </div>

                        @foreach ($configurations as $configIndex => $configuration)
                            <div class="edit-configuration">

                                <div class="edit-configuration-heading">

                                    <div>
                                        <span class="edit-config-label">
                                            KONFIGURASI {{ $configIndex + 1 }}
                                        </span>

                                        <h3>
                                            {{ $configuration->location_name }}
                                        </h3>

                                        <p>
                                            <i class="bi bi-printer"></i>
                                            {{ $configuration->engine_name }}
                                        </p>
                                    </div>

                                    <span class="edit-config-id">
                                        #{{ $configuration->configuration_id }}
                                    </span>

                                </div>

                                <input type="hidden" name="configurations[{{ $configIndex }}][configuration_id]"
                                    value="{{ $configuration->configuration_id }}">

                                <div class="edit-form-grid">

                                    {{-- Lokasi --}}
                                    <div class="edit-field">
                                        <label>
                                            Lokasi
                                            <span class="required">*</span>
                                        </label>

                                        <select name="configurations[{{ $configIndex }}][location_id]"
                                            class="select component-select2" required>

                                            <option value="">Pilih Lokasi</option>

                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ old('configurations.' . $configIndex . '.location_id', $configuration->location_id) == $location->id
                                                        ? 'selected'
                                                        : '' }}>
                                                    {{ $location->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Mesin --}}
                                    <div class="edit-field">
                                        <label>
                                            Mesin
                                            <span class="required">*</span>
                                        </label>

                                        <select name="configurations[{{ $configIndex }}][engine_id]"
                                            class="select component-select2" required>

                                            <option value="">Pilih Mesin</option>

                                            @foreach ($engines as $engine)
                                                <option value="{{ $engine->id }}"
                                                    {{ old('configurations.' . $configIndex . '.engine_id', $configuration->engine_id) == $engine->id
                                                        ? 'selected'
                                                        : '' }}>
                                                    {{ $engine->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="edit-field">

                                        <label>
                                            Cost Rangka
                                        </label>

                                        <div class="edit-input-prefix">
                                            {{-- <span>Rp</span> --}}

                                            <input type="text" name="configurations[{{ $configIndex }}][cost_rangka]"
                                                min="0" step="0.01" class="js-rupiah"
                                                value="{{ old('configurations.' . $configIndex . '.cost_rangka', $configuration->cost_rangka) }}">
                                        </div>

                                    </div>

                                    <div class="edit-field">

                                        <label>
                                            Cost Finishing
                                        </label>

                                        <div class="edit-input-prefix">
                                            {{-- <span>Rp</span> --}}

                                            <input type="text"
                                                name="configurations[{{ $configIndex }}][cost_finishing]" min="0"
                                                step="0.01" class="js-rupiah"
                                                value="{{ old('configurations.' . $configIndex . '.cost_finishing', $configuration->cost_finishing) }}">
                                        </div>

                                    </div>

                                    <div class="edit-field">

                                        <label>
                                            Total Cost Saat Ini
                                        </label>

                                        <input type="text" readonly step="0.01" class="js-rupiah"
                                            value="{{ $configuration->total_cost }}">
                                        {{-- <div class="edit-total-display">
                                            {{ number_format((float) $configuration->total_cost, 0, ',', '.') }}
                                        </div> --}}

                                    </div>

                                </div>

                            </div>
                        @endforeach

                    </section>


                    {{-- =========================================
                        DETAIL HARGA KOMPONEN
                    ========================================== --}}
                    <section class="edit-panel">

                        <div class="edit-panel-heading">

                            <div class="edit-section-icon">
                                <i class="bi bi-layers"></i>
                            </div>

                            <div>
                                <h2>Harga Komponen</h2>
                                <p>Perbarui konfigurasi komponen dan lihat harga jualnya.</p>
                            </div>

                        </div>

                        @php
                            $groupedComponents = collect($components)->groupBy('configuration_id');
                        @endphp

                        @forelse ($groupedComponents as $configurationId => $items)
                            @php
                                $config = collect($configurations)->firstWhere('configuration_id', $configurationId);

                                $editableComponents = collect($editComponents ?? [])
                                    ->where('configuration_id', $configurationId)
                                    ->values();
                            @endphp

                            <div class="edit-component-group">

                                {{-- HEADER KONFIGURASI --}}

                                <div class="edit-component-group-heading">

                                    <div>
                                        <span class="edit-config-label">
                                            KONFIGURASI PRODUKSI
                                        </span>

                                        <h3>
                                            {{ $config->location_name }}
                                        </h3>

                                        <p>
                                            {{ $config->engine_name }}
                                        </p>
                                    </div>

                                    <span class="edit-component-count">
                                        {{ $editableComponents->count() }} Komponen
                                    </span>

                                </div>


                                {{-- =========================================
                                    EDITOR KOMPONEN
                                ========================================== --}}

                                <div class="component-workspace">

                                    <div class="display-components-container" data-component-container
                                        data-location-id="{{ $config->location_id }}"
                                        data-engine-id="{{ $config->engine_id }}"
                                        data-configuration-id="{{ $configurationId }}">

                                        {{-- HEADER KOMPONEN (3 kolom, sama dengan row) --}}
                                        <div class="display-component-header">
                                            <div class="component-column">
                                                <label class="field-label">
                                                    Material
                                                    <span class="req">*</span>
                                                </label>
                                            </div>

                                            <div class="component-column">
                                                <label class="field-label">Laminasi</label>
                                            </div>

                                            <div class="component-column">
                                                <label class="field-label">Nilai Pembulatan (Rp)</label>
                                            </div>

                                            {{-- HARGA UMUM --}}
                                            <div class="component-column">
                                                <label class="field-label">Harga Umum (Rp)</label>
                                            </div>

                                            {{-- HARGA DIVISI --}}
                                            <div class="component-column">
                                                <label class="field-label">Harga Divisi (Rp)</label>
                                            </div>

                                            {{-- AKSI --}}
                                            <div class="component-column component-action">
                                                <label class="field-label">Aksi</label>
                                            </div>
                                        </div>

                                        {{-- BODY KOMPONEN --}}
                                        <div class="display-component-list" data-component-list>

                                            @forelse ($editableComponents as $index => $component)
                                                <div class="display-component-row"
                                                    data-component-index="{{ $index }}">

                                                    {{-- ID komponen yang sudah tersimpan --}}
                                                    <input type="hidden"
                                                        name="components[{{ $configurationId }}][{{ $index }}][component_id]"
                                                        value="{{ $component->component_id }}">

                                                    {{-- MATERIAL --}}
                                                    <div class="component-column">
                                                        <select
                                                            name="components[{{ $configurationId }}][{{ $index }}][material_id]"
                                                            class="select component-select2" data-material-id
                                                            data-selected-id="{{ $component->material_id ?? '' }}"
                                                            required>
                                                            <option value="">Pilih Material</option>
                                                        </select>
                                                    </div>

                                                    {{-- LAMINASI --}}
                                                    <div class="component-column">
                                                        <select
                                                            name="components[{{ $configurationId }}][{{ $index }}][lamination_id]"
                                                            class="select component-select2" data-lamination-id
                                                            data-selected-id="{{ $component->lamination_id ?? '' }}">
                                                            <option value="">Tanpa Laminasi</option>
                                                        </select>
                                                    </div>

                                                    {{-- PEMBULATAN --}}
                                                    <div class="component-column">
                                                        <input type="text"
                                                            name="components[{{ $configurationId }}][{{ $index }}][rounding_value]"
                                                            class="input js-rupiah"
                                                            value="{{ $component->rounding_value_component ?? '' }}"
                                                            placeholder="Nilai pembulatan" inputmode="numeric"
                                                            autocomplete="off">
                                                    </div>

                                                    {{-- HARGA UMUM --}}
                                                    <div class="component-column">
                                                        <input type="text" class="input js-rupiah"
                                                            value="{{ $component->general_price ?? 0 }}"
                                                            placeholder="Harga Umum" readonly>
                                                    </div>

                                                    {{-- HARGA DIVISI --}}
                                                    <div class="component-column">
                                                        <input type="text" class="input js-rupiah"
                                                            value="{{ $component->division_price ?? 0 }}"
                                                            placeholder="Harga Divisi" readonly>
                                                    </div>

                                                    {{-- AKSI HAPUS --}}
                                                    <div class="component-column component-action">
                                                        <button type="button" class="btn-remove-component"
                                                            data-remove-component title="Hapus Komponen">
                                                            <i class="bi bi-trash3-fill"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="edit-empty-state">
                                                    <i class="bi bi-inbox"></i>
                                                    <strong>Belum Ada Komponen</strong>
                                                    <p>Tidak ada konfigurasi komponen yang tersedia.</p>
                                                </div>
                                            @endforelse

                                        </div>

                                        {{-- TAMBAH KOMPONEN --}}
                                        <div class="display-component-actions" style="margin-bottom: 20px;">
                                            <button type="button" class="btn btn--ghost btn-add-component"
                                                data-add-component>
                                                <i class="bi bi-plus-lg"></i>
                                                Tambah Komponen
                                            </button>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        @empty

                            <div class="edit-empty-state">

                                <i class="bi bi-inbox"></i>

                                <strong>Belum Ada Komponen</strong>

                                <p>
                                    Tidak ada komponen yang dapat diedit.
                                </p>

                            </div>
                        @endforelse


                        {{-- =========================================
                            FORM ACTION
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

                            <button type="submit" class="btn btn--primary">
                                <i class="bi bi-check-lg"></i>
                                Simpan Perubahan
                            </button>

                        </div>

                    </section>



                </form>

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

            const form = document.getElementById(
                'formEditDisplayComponent'
            );

            const componentContainers = document.querySelectorAll(
                '[data-component-container]'
            );

            const componentUrl = @json(route('admin_pc_finishing_display_components'));
            const componentTemplates = new Map();

            if (!form) {
                console.error('Form Edit Display Component tidak ditemukan.');
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | FORMAT RUPIAH
            |--------------------------------------------------------------------------
            */

            function formatRupiah(value) {

                let number = String(value).replace(/[^\d.]/g, '');

                if (!number) {
                    return '';
                }

                let parts = number.split('.');

                let integerPart = parts[0] || '0';

                let decimalPart = parts.length > 1 ?
                    parts.slice(1).join('').substring(0, 2) :
                    '';

                integerPart = integerPart.replace(
                    /\B(?=(\d{3})+(?!\d))/g,
                    '.'
                );

                return decimalPart !== '' ?
                    integerPart + ',' + decimalPart :
                    integerPart;
            }

            function normalizeRupiah(value) {

                return String(value)
                    .replace(/\./g, '')
                    .replace(',', '.');
            }

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

                        if (
                            $select.hasClass('select2-hidden-accessible')
                        ) {
                            return;
                        }

                        $select.select2({
                            width: '100%',
                            placeholder: 'Pilih pilihan',
                            allowClear: false
                        });

                    });

            }

            /*
            |--------------------------------------------------------------------------
            | POPULATE MATERIAL
            |--------------------------------------------------------------------------
            */

            function populateMaterials(container, materials) {

                const selects = container.matches('[data-material-id]') ? [container] :
                    container.querySelectorAll('[data-material-id]');

                selects.forEach(function(select) {

                    const selectedId = select.dataset.selectedId || '';

                    select.innerHTML = '<option value="">Pilih Material</option>';

                    materials.forEach(function(item) {

                        const option = new Option(
                            item.name,
                            item.id,
                            false,
                            String(item.id) === String(selectedId)
                        );

                        select.add(option);

                    });

                    $(select)
                        .val(selectedId || null)
                        .trigger('change');

                    delete select.dataset.selectedId;

                });

            }

            /*
            |--------------------------------------------------------------------------
            | POPULATE LAMINASI
            |--------------------------------------------------------------------------
            */

            function populateLaminations(container, laminations) {

                const selects = container.matches('[data-lamination-id]') ? [container] :
                    container.querySelectorAll('[data-lamination-id]');

                selects.forEach(function(select) {

                    const selectedId = select.dataset.selectedId || '';

                    select.innerHTML = '<option value="">Tanpa Laminasi</option>';

                    laminations.forEach(function(item) {

                        const option = new Option(
                            item.name,
                            item.id,
                            false,
                            String(item.id) === String(selectedId)
                        );

                        select.add(option);

                    });

                    $(select)
                        .val(selectedId || null)
                        .trigger('change');

                    delete select.dataset.selectedId;

                });

            }

            /*
            |--------------------------------------------------------------------------
            | LOAD MATERIAL DAN LAMINASI PER KONFIGURASI
            |--------------------------------------------------------------------------
            */

            async function loadComponentOptions(container) {

                const locationId =
                    container.dataset.locationId;

                const engineId =
                    container.dataset.engineId;

                if (!locationId || !engineId) {

                    console.error(
                        'Lokasi atau Engine tidak ditemukan.', {
                            locationId: locationId,
                            engineId: engineId,
                            configurationId: container.dataset.configurationId
                        }
                    );

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
                        throw new Error(
                            'Gagal mengambil data komponen.'
                        );
                    }

                    const data = await response.json();

                    const availableMaterials =
                        data.materials || [];

                    const availableLaminations =
                        data.laminations || [];

                    componentTemplates.set(
                        container.dataset.configurationId, {
                            materials: availableMaterials,
                            laminations: availableLaminations
                        }
                    );


                    populateMaterials(
                        container,
                        availableMaterials
                    );

                    populateLaminations(
                        container,
                        availableLaminations
                    );

                    console.log(
                        'Komponen berhasil dimuat:',
                        container.dataset.configurationId
                    );

                } catch (error) {

                    console.error(
                        'Error load komponen:',
                        error
                    );

                    showToast(
                        'error',
                        'Gagal memuat pilihan Material dan Laminasi.'
                    );

                }

            }

            /*
            |--------------------------------------------------------------------------
            | LOAD SELURUH KONFIGURASI
            |--------------------------------------------------------------------------
            */

            async function loadAllComponentOptions() {

                for (const container of componentContainers) {

                    await loadComponentOptions(container);

                }

            }

            /*
            |--------------------------------------------------------------------------
            | CREATE COMPONENT ROW
            |--------------------------------------------------------------------------
            */

            function createComponentRow(configurationId, index) {

                const row = document.createElement('div');

                row.className = 'display-component-row';
                row.dataset.componentIndex = index;
                row.dataset.isNew = 'true';

                row.innerHTML = `
                    <div class="component-column">
                        <select
                            name="components[${configurationId}][${index}][material_id]"
                            class="select component-select2"
                            data-material-id
                            required>
                            <option value="">Pilih Material</option>
                        </select>
                    </div>

                    <div class="component-column">
                        <select
                            name="components[${configurationId}][${index}][lamination_id]"
                            class="select component-select2"
                            data-lamination-id>
                            <option value="">Tanpa Laminasi</option>
                        </select>
                    </div>

                    <div class="component-column">
                        <input
                            type="text"
                            name="components[${configurationId}][${index}][rounding_value]"
                            class="input js-rupiah"
                            value=""
                            placeholder="Nilai pembulatan"
                            inputmode="numeric"
                            autocomplete="off">
                    </div>

                    <div class="component-column">
                        <input
                            type="text"
                            class="input js-rupiah"
                            value="0"
                            placeholder="Harga Umum"
                            readonly>
                    </div>

                    <div class="component-column">
                        <input
                            type="text"
                            class="input js-rupiah"
                            value="0"
                            placeholder="Harga Divisi"
                            readonly>
                    </div>

                    <div class="component-column component-action">
                        <button
                            type="button"
                            class="btn-remove-component"
                            data-remove-component
                            title="Hapus Komponen">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </div>
                `;

                return row;
            }

            /*
            |--------------------------------------------------------------------------
            | ADD COMPONENT ROW
            |--------------------------------------------------------------------------
            */

            function addComponentRow(container) {

                const list = container.querySelector('[data-component-list]');

                if (!list) {
                    return;
                }

                const configurationId = container.dataset.configurationId;

                const existingRows = list.querySelectorAll(
                    '.display-component-row'
                );

                const usedIndexes = Array.from(existingRows).map(function(row) {
                    return Number(row.dataset.componentIndex);
                });

                let index = 0;

                while (usedIndexes.includes(index)) {
                    index++;
                }

                const row = createComponentRow(configurationId, index);

                list.appendChild(row);

                initComponentSelect2(row);

                const template = componentTemplates.get(configurationId);

                if (template) {

                    populateMaterials(
                        row,
                        template.materials
                    );

                    populateLaminations(
                        row,
                        template.laminations
                    );

                }

            }

            /*
            |--------------------------------------------------------------------------
            | EVENT TAMBAH KOMPONEN
            |--------------------------------------------------------------------------
            */

            form.addEventListener('click', function(event) {

                const addButton = event.target.closest('[data-add-component]');

                if (!addButton) {
                    return;
                }

                const container = addButton.closest('[data-component-container]');

                if (!container) {
                    return;
                }

                addComponentRow(container);

            });


            /*
            |--------------------------------------------------------------------------
            | EVENT HAPUS KOMPONEN
            |--------------------------------------------------------------------------
            */

            form.addEventListener('click', function(event) {

                const removeButton = event.target.closest('[data-remove-component]');

                if (!removeButton) {
                    return;
                }

                const row = removeButton.closest('.display-component-row');

                if (!row) {
                    return;
                }

                const materialSelect = row.querySelector('[data-material-id]');
                const laminationSelect = row.querySelector('[data-lamination-id]');

                if (
                    materialSelect &&
                    $(materialSelect).hasClass('select2-hidden-accessible')
                ) {
                    $(materialSelect).select2('destroy');
                }

                if (
                    laminationSelect &&
                    $(laminationSelect).hasClass('select2-hidden-accessible')
                ) {
                    $(laminationSelect).select2('destroy');
                }

                row.remove();

            });

            /*
            |--------------------------------------------------------------------------
            | FORMAT INPUT RUPIAH AWAL
            |--------------------------------------------------------------------------
            */

            $('.js-rupiah').each(function() {

                this.value = formatRupiah(this.value);

            });

            /*
            |--------------------------------------------------------------------------
            | FORMAT INPUT SAAT DIKETIK
            |--------------------------------------------------------------------------
            */

            $(document).on(
                'input',
                '.js-rupiah',
                function() {

                    this.value = formatRupiah(this.value);

                }
            );

            /*
            |--------------------------------------------------------------------------
            | VALIDASI DAN SUBMIT
            |--------------------------------------------------------------------------
            */

            form.addEventListener('submit', function(event) {

                const configurations = form.querySelectorAll(
                    '.edit-configuration'
                );

                let isValid = true;

                configurations.forEach(function(configuration) {

                    const locationSelect = configuration.querySelector(
                        'select[name$="[location_id]"]'
                    );

                    const engineSelect = configuration.querySelector(
                        'select[name$="[engine_id]"]'
                    );

                    if (!locationSelect || !engineSelect) {
                        isValid = false;
                        return;
                    }

                    const locationName = locationSelect
                        .selectedOptions[0]
                        .textContent
                        .trim()
                        .toLowerCase();

                    const engineName = engineSelect
                        .selectedOptions[0]
                        .textContent
                        .trim()
                        .toLowerCase();

                    if (
                        locationName !== 'purwakarta' ||
                        engineName !== 'large format'
                    ) {
                        isValid = false;
                    }

                });

                if (!isValid) {

                    event.preventDefault();

                    showToast(
                        'error',
                        'Konfigurasi hanya tersedia untuk lokasi Purwakarta dengan mesin Large Format.'
                    );

                    return;
                }

                /*
                |--------------------------------------------------------------------------
                | NORMALISASI RUPIAH
                |--------------------------------------------------------------------------
                */

                form.querySelectorAll('.js-rupiah').forEach(function(input) {

                    input.value = normalizeRupiah(input.value);

                });

            });

            /*
            |--------------------------------------------------------------------------
            | INITIALIZATION
            |--------------------------------------------------------------------------
            */

            initComponentSelect2(form);

            loadAllComponentOptions();

        });
    </script>
@endsection

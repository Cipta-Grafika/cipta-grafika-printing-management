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
                            Ubah ongkos produksi berdasarkan lokasi, engine, dan material.
                            Setiap lokasi dapat memiliki material dan lebar yang berbeda.
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


                        <form method="POST" action="{{ route('admin_update_pc_finishing', request()->route('id')) }}"
                            id="productionCostForm">

                            @csrf
                            @method('PUT')

                            <input type="hidden" name="engine_id" value="{{ $engine->id }}">


                            {{-- ==========================================================
                        | ENGINE
                        =========================================================== --}}

                            <div class="form-grid">

                                <div class="field">

                                    <label class="field-label" for="engine_id_display">
                                        Engine <span class="req">*</span>
                                    </label>

                                    <select id="engine_id_display" class="select select2" disabled>

                                        <option value="">
                                            Pilih Engine
                                        </option>

                                        @foreach ($engines as $engineOption)
                                            <option value="{{ $engineOption->id }}"
                                                @if ($engineOption->id == $engine->id) selected @endif>
                                                {{ $engineOption->name }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                            </div>


                            {{-- ==========================================================
                        | DETAIL ONGKOS PRODUKSI
                        =========================================================== --}}

                            <div class="field" style="margin-top:24px; overflow:auto;">

                                <div
                                    style="
                                    display:flex;
                                    justify-content:space-between;
                                    align-items:center;
                                    margin-bottom:12px;
                                ">

                                    <label class="field-label" style="margin-bottom:0;">
                                        Detail Ongkos Produksi
                                    </label>

                                </div>


                                <div id="locationContainer">

                                    @foreach ($locations as $locationIndex => $location)
                                        @php
                                            $isOutsourcing =
                                                strtolower(trim($location->location_name)) === 'outsourcing';

                                            $materialFormIndex = 0;
                                        @endphp


                                        {{-- ==================================================
                                    | LOCATION
                                    =================================================== --}}

                                        <div class="location-block" data-location-id="{{ $location->location_id }}"
                                            data-outsourcing="{{ $isOutsourcing ? '1' : '0' }}"
                                            style="
                                            border:1px solid rgba(255,255,255,.08);
                                            border-radius:12px;
                                            padding:16px;
                                            margin-bottom:20px;
                                        ">

                                            {{-- LOCATION HEADER --}}

                                            <div
                                                style="
                                                display:flex;
                                                justify-content:space-between;
                                                align-items:center;
                                                margin-bottom:14px;
                                            ">

                                                <div>

                                                    <div
                                                        style="
                                                        font-weight:600;
                                                        font-size:14px;
                                                    ">
                                                        Lokasi
                                                    </div>

                                                    <div
                                                        style="
                                                        margin-top:4px;
                                                        font-size:13px;
                                                        opacity:.75;
                                                    ">
                                                        {{ $location->location_name }}
                                                    </div>

                                                </div>


                                                <button type="button" class="btn--icon btn-delete-location"
                                                    aria-label="Hapus lokasi" title="Hapus lokasi">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>

                                            </div>


                                            {{-- LOCATION ID --}}

                                            <input type="hidden" name="locations[{{ $locationIndex }}][location_id]"
                                                value="{{ $location->location_id }}">


                                            {{-- ==================================================
                                        | NON OUTSOURCING
                                        |
                                        | LOCATION
                                        | └── CATEGORY
                                        |     └── MATERIAL
                                        =================================================== --}}

                                            @if (!$isOutsourcing)
                                                <div class="category-container">

                                                    @foreach ($location->categories as $categoryIndex => $category)
                                                        <div class="category-block"
                                                            data-category-id="{{ $category->category_id }}"
                                                            style="
                                                            margin-bottom:20px;
                                                            padding:14px;
                                                            border:1px solid rgba(255,255,255,.06);
                                                            border-radius:10px;
                                                        ">

                                                            {{-- CATEGORY HEADER --}}

                                                            <div
                                                                style="
                                                                display:flex;
                                                                justify-content:space-between;
                                                                align-items:center;
                                                                margin-bottom:12px;
                                                            ">

                                                                <div>

                                                                    <div
                                                                        style="
                                                                        font-weight:600;
                                                                        font-size:13px;
                                                                    ">
                                                                        Kategori
                                                                    </div>

                                                                    <div
                                                                        style="
                                                                        margin-top:3px;
                                                                        font-size:13px;
                                                                        opacity:.75;
                                                                    ">
                                                                        {{ $category->category_name }}
                                                                    </div>

                                                                </div>

                                                            </div>


                                                            {{-- MATERIAL TABLE --}}

                                                            <div class="table-responsive">

                                                                <table class="display data-table location-material-table">

                                                                    <thead>

                                                                        <tr>

                                                                            <th>
                                                                                Material
                                                                            </th>

                                                                            <th>
                                                                                Lebar Dipakai
                                                                            </th>

                                                                            <th>
                                                                                Harga Permeter (Rp)
                                                                            </th>

                                                                            <th>
                                                                                Ongkos Produksi (Rp)
                                                                            </th>

                                                                            <th>
                                                                                Ongkos Finishing (Rp)
                                                                            </th>

                                                                            <th style="width:80px;">
                                                                                Aksi
                                                                            </th>

                                                                        </tr>

                                                                    </thead>


                                                                    <tbody class="location-material-body">

                                                                        @foreach ($category->details as $detail)
                                                                            @php
                                                                                $currentMaterialIndex = $materialFormIndex;

                                                                                $materialSizesForRow =
                                                                                    $materialSizes[
                                                                                        $detail->material_id
                                                                                    ] ?? collect();

                                                                                $selectedWidthIds = $detail->widths
                                                                                    ->pluck('material_size_id')
                                                                                    ->toArray();
                                                                            @endphp


                                                                            <tr class="material-row"
                                                                                data-category-id="{{ $category->category_id }}">

                                                                                <td style="width:300px;">

                                                                                    <select
                                                                                        name="locations[{{ $locationIndex }}][items][{{ $currentMaterialIndex }}][material_id]"
                                                                                        class="select select2 material-select"
                                                                                        data-category-id="{{ $category->category_id }}"
                                                                                        required>

                                                                                        <option value="">
                                                                                            Pilih Material
                                                                                        </option>

                                                                                        @foreach ($materials as $material)
                                                                                            <option
                                                                                                value="{{ $material->id }}"
                                                                                                data-category-id="{{ $material->category_id }}"
                                                                                                @if ($material->id == $detail->material_id) selected @endif>
                                                                                                {{ $material->material_name }}
                                                                                                ({{ $material->category_name }})
                                                                                            </option>
                                                                                        @endforeach

                                                                                    </select>

                                                                                </td>


                                                                                <td>

                                                                                    <select
                                                                                        name="locations[{{ $locationIndex }}][items][{{ $currentMaterialIndex }}][width_ids][]"
                                                                                        class="select select2 width-select"
                                                                                        multiple="multiple" required>

                                                                                        @foreach ($materialSizesForRow as $size)
                                                                                            <option
                                                                                                value="{{ $size->id }}"
                                                                                                @if (in_array($size->id, $selectedWidthIds)) selected @endif>
                                                                                                {{ $size->width }}
                                                                                                {{ $size->unit }}
                                                                                            </option>
                                                                                        @endforeach

                                                                                    </select>

                                                                                </td>


                                                                                <td>

                                                                                    <input type="text"
                                                                                        name="locations[{{ $locationIndex }}][items][{{ $currentMaterialIndex }}][price_per_meter]"
                                                                                        class="input rupiah-input"
                                                                                        inputmode="decimal"
                                                                                        autocomplete="off"
                                                                                        placeholder="Contoh: 6600.00"
                                                                                        value="{{ number_format($detail->price_per_meter, 0, ',', '.') }}"
                                                                                        required>

                                                                                </td>


                                                                                <td>

                                                                                    <input type="text"
                                                                                        name="locations[{{ $locationIndex }}][items][{{ $currentMaterialIndex }}][production_cost]"
                                                                                        class="input rupiah-input"
                                                                                        inputmode="decimal"
                                                                                        autocomplete="off"
                                                                                        placeholder="Contoh: 9740.00"
                                                                                        value="{{ number_format($detail->production_cost, 0, ',', '.') }}"
                                                                                        required>

                                                                                </td>


                                                                                <td>

                                                                                    <input type="text"
                                                                                        name="locations[{{ $locationIndex }}][items][{{ $currentMaterialIndex }}][finishing_cost]"
                                                                                        class="input rupiah-input"
                                                                                        inputmode="decimal"
                                                                                        autocomplete="off"
                                                                                        placeholder="Contoh: 600.50"
                                                                                        value="{{ number_format($detail->finishing_cost, 0, ',', '.') }}"
                                                                                        required>

                                                                                </td>


                                                                                <td class="text-center">

                                                                                    <button type="button"
                                                                                        class="btn--icon btn-delete-row"
                                                                                        aria-label="Hapus material"
                                                                                        title="Hapus material">
                                                                                        <i class="bi bi-trash3-fill"></i>
                                                                                    </button>

                                                                                </td>

                                                                            </tr>

                                                                            @php
                                                                                $materialFormIndex++;
                                                                            @endphp
                                                                        @endforeach

                                                                    </tbody>

                                                                </table>

                                                            </div>


                                                            {{-- ADD MATERIAL --}}

                                                            <div
                                                                style="
                                                                display:flex;
                                                                justify-content:flex-end;
                                                                margin-top:12px;
                                                            ">

                                                                <button type="button"
                                                                    class="btn btn--ghost btn-add-material">
                                                                    + Tambah Material
                                                                </button>

                                                            </div>

                                                        </div>
                                                    @endforeach

                                                </div>


                                                {{-- ==================================================
                                                | OUTSOURCING
                                                |
                                                | LOCATION
                                                | └── VENDOR
                                                |     └── CATEGORY
                                                |         └── MATERIAL
                                                |
                                                | FIELD:
                                                | Material
                                                | Lebar
                                                | Harga Vendor
                                                | Aksi
                                                =================================================== --}}
                                            @else
                                                <div class="vendor-container">

                                                    @foreach ($location->vendors as $vendorIndex => $vendor)
                                                        <div class="vendor-block"
                                                            data-vendor-id="{{ $vendor->vendor_id }}"
                                                            style="
                                                            margin-bottom:20px;
                                                            padding:14px;
                                                            border:1px solid rgba(255,255,255,.06);
                                                            border-radius:10px;
                                                        ">

                                                            {{-- VENDOR HEADER --}}

                                                            <div
                                                                style="
                                                                display:flex;
                                                                justify-content:space-between;
                                                                align-items:center;
                                                                margin-bottom:14px;
                                                            ">

                                                                <div>

                                                                    <div
                                                                        style="
                                                                        font-weight:600;
                                                                        font-size:13px;
                                                                    ">
                                                                        Vendor
                                                                    </div>

                                                                    <div
                                                                        style="
                                                                        margin-top:3px;
                                                                        font-size:13px;
                                                                        opacity:.75;
                                                                    ">
                                                                        {{ $vendor->vendor_name }}
                                                                    </div>

                                                                </div>

                                                            </div>


                                                            <div class="category-container">

                                                                @foreach ($vendor->categories as $categoryIndex => $category)
                                                                    <div class="category-block"
                                                                        data-category-id="{{ $category->category_id }}"
                                                                        style="
                                                                        margin-bottom:18px;
                                                                        padding:14px;
                                                                        border:1px solid rgba(255,255,255,.05);
                                                                        border-radius:10px;
                                                                    ">

                                                                        {{-- CATEGORY HEADER --}}

                                                                        <div
                                                                            style="
                                                                            display:flex;
                                                                            justify-content:space-between;
                                                                            align-items:center;
                                                                            margin-bottom:12px;
                                                                        ">

                                                                            <div>

                                                                                <div
                                                                                    style="
                                                                                    font-weight:600;
                                                                                    font-size:13px;
                                                                                ">
                                                                                    Kategori
                                                                                </div>

                                                                                <div
                                                                                    style="
                                                                                    margin-top:3px;
                                                                                    font-size:13px;
                                                                                    opacity:.75;
                                                                                ">
                                                                                    {{ $category->category_name }}
                                                                                </div>

                                                                            </div>

                                                                        </div>


                                                                        {{-- MATERIAL TABLE --}}

                                                                        <div class="table-responsive">

                                                                            <table
                                                                                class="display data-table location-material-table">

                                                                                <thead>

                                                                                    <tr>

                                                                                        <th>
                                                                                            Material
                                                                                        </th>

                                                                                        <th>
                                                                                            Lebar Dipakai
                                                                                        </th>

                                                                                        <th>
                                                                                            Harga Vendor (Rp)
                                                                                        </th>

                                                                                        <th style="width:80px;">
                                                                                            Aksi
                                                                                        </th>

                                                                                    </tr>

                                                                                </thead>


                                                                                <tbody class="location-material-body">

                                                                                    @foreach ($category->details as $detail)
                                                                                        @php
                                                                                            $currentMaterialIndex = $materialFormIndex;

                                                                                            $materialSizesForRow =
                                                                                                $materialSizes[
                                                                                                    $detail->material_id
                                                                                                ] ?? collect();

                                                                                            $selectedWidthIds = $detail->widths
                                                                                                ->pluck(
                                                                                                    'material_size_id',
                                                                                                )
                                                                                                ->toArray();
                                                                                        @endphp


                                                                                        <tr class="material-row"
                                                                                            data-category-id="{{ $category->category_id }}">

                                                                                            <td style="width:300px;">

                                                                                                <input type="hidden"
                                                                                                    name="locations[{{ $locationIndex }}][items][{{ $currentMaterialIndex }}][vendor_id]"
                                                                                                    class="item-vendor-id"
                                                                                                    value="{{ $vendor->vendor_id }}">


                                                                                                <select
                                                                                                    name="locations[{{ $locationIndex }}][items][{{ $currentMaterialIndex }}][material_id]"
                                                                                                    class="select select2 material-select"
                                                                                                    data-category-id="{{ $category->category_id }}"
                                                                                                    required>

                                                                                                    <option value="">
                                                                                                        Pilih Material
                                                                                                    </option>

                                                                                                    @foreach ($materials as $material)
                                                                                                        <option
                                                                                                            value="{{ $material->id }}"
                                                                                                            data-category-id="{{ $material->category_id }}"
                                                                                                            @if ($material->id == $detail->material_id) selected @endif>
                                                                                                            {{ $material->material_name }}
                                                                                                            ({{ $material->category_name }})
                                                                                                        </option>
                                                                                                    @endforeach

                                                                                                </select>

                                                                                            </td>


                                                                                            <td>

                                                                                                <select
                                                                                                    name="locations[{{ $locationIndex }}][items][{{ $currentMaterialIndex }}][width_ids][]"
                                                                                                    class="select select2 width-select"
                                                                                                    multiple="multiple"
                                                                                                    required>

                                                                                                    @foreach ($materialSizesForRow as $size)
                                                                                                        <option
                                                                                                            value="{{ $size->id }}"
                                                                                                            @if (in_array($size->id, $selectedWidthIds)) selected @endif>
                                                                                                            {{ $size->width }}
                                                                                                            {{ $size->unit }}
                                                                                                        </option>
                                                                                                    @endforeach

                                                                                                </select>

                                                                                            </td>


                                                                                            <td>

                                                                                                <input type="text"
                                                                                                    name="locations[{{ $locationIndex }}][items][{{ $currentMaterialIndex }}][vendor_price]"
                                                                                                    class="input rupiah-input"
                                                                                                    inputmode="decimal"
                                                                                                    autocomplete="off"
                                                                                                    placeholder="Contoh: 15000"
                                                                                                    value="{{ number_format($detail->vendor_price, 0, ',', '.') }}"
                                                                                                    required>

                                                                                            </td>


                                                                                            <td class="text-center">

                                                                                                <button type="button"
                                                                                                    class="btn--icon btn-delete-row"
                                                                                                    aria-label="Hapus material"
                                                                                                    title="Hapus material">
                                                                                                    <i
                                                                                                        class="bi bi-trash3-fill"></i>
                                                                                                </button>

                                                                                            </td>

                                                                                        </tr>
                                                                                        @php
                                                                                            $materialFormIndex++;
                                                                                        @endphp
                                                                                    @endforeach

                                                                                </tbody>

                                                                            </table>

                                                                        </div>


                                                                        {{-- ADD MATERIAL --}}

                                                                        <div
                                                                            style="
                                                                            display:flex;
                                                                            justify-content:flex-end;
                                                                            margin-top:12px;
                                                                        ">

                                                                            <button type="button"
                                                                                class="btn btn--ghost btn-add-material">
                                                                                + Tambah Material
                                                                            </button>

                                                                        </div>

                                                                    </div>
                                                                @endforeach

                                                            </div>

                                                        </div>
                                                    @endforeach

                                                </div>
                                            @endif

                                        </div>
                                    @endforeach

                                </div>

                            </div>


                            {{-- ==========================================================
                        | ACTION
                        =========================================================== --}}

                            <div class="form-actions">

                                <span class="badge dot success">
                                    Siap disimpan
                                </span>

                                <span class="spacer"></span>

                                <a href="{{ route('admin_pc_finishings') }}" class="btn btn--ghost">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn--primary">
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

            /*
            |--------------------------------------------------------------------------
            | SELECT2
            |--------------------------------------------------------------------------
            */

            $('.select2').select2({
                width: '100%'
            });


            /*
            |--------------------------------------------------------------------------
            | FORM
            |--------------------------------------------------------------------------
            */

            const productionCostForm =
                document.getElementById('productionCostForm');


            /*
            |--------------------------------------------------------------------------
            | LOCATION CONTAINER
            |--------------------------------------------------------------------------
            */

            const locationContainer =
                document.getElementById('locationContainer');


            /*
            |--------------------------------------------------------------------------
            | FORMAT ANGKA SAAT MENGETIK
            |--------------------------------------------------------------------------
            */

            function formatNumberInput(value) {

                if (value === '') {
                    return '';
                }

                value = value.replace(/[^\d]/g, '');

                if (value === '') {
                    return '';
                }

                return Number(value).toLocaleString('id-ID');
            }


            /*
            |--------------------------------------------------------------------------
            | FORMAT RUPIAH SAAT BLUR
            |--------------------------------------------------------------------------
            */

            function formatRupiah(value) {

                if (value === '') {
                    return '';
                }

                value = value.replace(/[^\d]/g, '');

                if (value === '') {
                    return '';
                }

                return Number(value).toLocaleString('id-ID');
            }


            /*
            |--------------------------------------------------------------------------
            | FILTER MATERIAL BERDASARKAN CATEGORY
            |--------------------------------------------------------------------------
            */

            function filterMaterialOptions(select) {

                const categoryId =
                    $(select).data('category-id');

                if (!categoryId) {
                    return;
                }

                $(select).find('option').each(function() {

                    const option =
                        $(this);

                    const optionCategoryId =
                        option.data('category-id');


                    if (!option.val()) {

                        option.prop(
                            'disabled',
                            false
                        );

                        return;
                    }


                    if (
                        String(optionCategoryId) !==
                        String(categoryId)
                    ) {

                        option.prop(
                            'disabled',
                            true
                        );

                    } else {

                        option.prop(
                            'disabled',
                            false
                        );

                    }

                });

            }


            /*
            |--------------------------------------------------------------------------
            | INIT MATERIAL SELECT
            |--------------------------------------------------------------------------
            */

            function initMaterialSelect(row) {

                const materialSelect =
                    $(row).find('.material-select');

                const widthSelect =
                    $(row).find('.width-select');


                /*
                |--------------------------------------------------------------------------
                | FILTER MATERIAL SESUAI CATEGORY
                |--------------------------------------------------------------------------
                */

                filterMaterialOptions(
                    materialSelect
                );


                /*
                |--------------------------------------------------------------------------
                | CHANGE MATERIAL
                |--------------------------------------------------------------------------
                */

                materialSelect.off(
                    'change.material'
                );


                materialSelect.on(
                    'change.material',
                    function() {

                        const materialId =
                            $(this).val();


                        /*
                        |--------------------------------------------------------------------------
                        | RESET WIDTH
                        |--------------------------------------------------------------------------
                        */

                        widthSelect.empty();


                        /*
                        |--------------------------------------------------------------------------
                        | JIKA MATERIAL KOSONG
                        |--------------------------------------------------------------------------
                        */

                        if (!materialId) {

                            widthSelect.trigger(
                                'change'
                            );

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | AMBIL WIDTH MATERIAL
                        |--------------------------------------------------------------------------
                        */

                        fetch(
                                `{{ route('get_material_sizes') }}?id=${materialId}`
                            )
                            .then(function(response) {

                                if (!response.ok) {

                                    throw new Error(
                                        'Gagal mengambil data width.'
                                    );

                                }

                                return response.json();

                            })
                            .then(function(data) {

                                data.forEach(function(size) {

                                    widthSelect.append(
                                        new Option(
                                            `${size.width} ${size.unit}`,
                                            size.id
                                        )
                                    );

                                });


                                widthSelect.trigger(
                                    'change'
                                );

                            })
                            .catch(function(error) {

                                console.error(
                                    'Gagal mengambil ukuran material:',
                                    error
                                );

                            });

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | INIT EXISTING MATERIAL ROW
            |--------------------------------------------------------------------------
            |
            | Jangan trigger change pada row existing.
            | Width yang sudah tersimpan harus tetap dipertahankan.
            |--------------------------------------------------------------------------
            */

            $('#locationContainer .material-row').each(
                function() {

                    initMaterialSelect(this);

                }
            );


            /*
            |--------------------------------------------------------------------------
            | INDEX MATERIAL
            |--------------------------------------------------------------------------
            */

            const materialIndexes = {};


            $('.location-block').each(
                function() {

                    const locationBlock =
                        $(this);

                    const locationId =
                        locationBlock.data(
                            'location-id'
                        );

                    const existingRows =
                        locationBlock.find(
                            '.material-row'
                        ).length;

                    materialIndexes[locationId] =
                        existingRows;

                }
            );


            /*
            |--------------------------------------------------------------------------
            | GET LOCATION INDEX
            |--------------------------------------------------------------------------
            */

            function getLocationIndex(locationBlock) {

                const locationName =
                    $(locationBlock)
                    .find(
                        'input[name$="[location_id]"]'
                    )
                    .attr('name');


                if (!locationName) {

                    console.error(
                        'Location index tidak ditemukan.'
                    );

                    return null;

                }


                const locationIndexMatch =
                    locationName.match(
                        /locations\[(\d+)\]/
                    );


                if (!locationIndexMatch) {

                    console.error(
                        'Location index tidak valid.'
                    );

                    return null;

                }


                return locationIndexMatch[1];

            }


            /*
            |--------------------------------------------------------------------------
            | BUILD MATERIAL OPTIONS
            |--------------------------------------------------------------------------
            */

            function buildMaterialOptions(categoryId) {

                let options = `
            <option value="">
                Pilih Material
            </option>
        `;


                @foreach ($materials as $material)

                    if (
                        String(
                            {{ $material->category_id }}
                        ) === String(categoryId)
                    ) {

                        options += `
                    <option
                        value="{{ $material->id }}"
                        data-category-id="{{ $material->category_id }}"
                    >
                        {{ $material->material_name }}
                        ({{ $material->category_name }})
                    </option>
                `;

                    }
                @endforeach


                return options;

            }


            /*
            |--------------------------------------------------------------------------
            | ADD MATERIAL ROW
            |--------------------------------------------------------------------------
            */

            function addMaterialRow(
                locationBlock,
                categoryBlock
            ) {

                const $locationBlock =
                    $(locationBlock);

                const $categoryBlock =
                    $(categoryBlock);


                /*
                |--------------------------------------------------------------------------
                | LOCATION ID
                |--------------------------------------------------------------------------
                */

                const locationId =
                    $locationBlock.data(
                        'location-id'
                    );


                /*
                |--------------------------------------------------------------------------
                | CATEGORY ID
                |--------------------------------------------------------------------------
                */

                const categoryId =
                    $categoryBlock.data(
                        'category-id'
                    );


                /*
                |--------------------------------------------------------------------------
                | LOCATION INDEX
                |--------------------------------------------------------------------------
                */

                const locationIndex =
                    getLocationIndex(
                        locationBlock
                    );


                if (locationIndex === null) {
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | MATERIAL INDEX
                |--------------------------------------------------------------------------
                */

                if (
                    typeof materialIndexes[locationId] ===
                    'undefined'
                ) {

                    materialIndexes[locationId] = 0;

                }


                const materialIndex =
                    materialIndexes[locationId];


                /*
                |--------------------------------------------------------------------------
                | CEK OUTSOURCING
                |--------------------------------------------------------------------------
                */

                const isOutsourcing =
                    String(
                        $locationBlock.data(
                            'outsourcing'
                        )
                    ) === '1';


                /*
                |--------------------------------------------------------------------------
                | VENDOR ID
                |--------------------------------------------------------------------------
                */

                let vendorId = '';


                if (isOutsourcing) {

                    const vendorBlock =
                        $categoryBlock.closest(
                            '.vendor-block'
                        );


                    vendorId =
                        vendorBlock.data(
                            'vendor-id'
                        );


                    if (!vendorId) {

                        console.error(
                            'Vendor ID tidak ditemukan.'
                        );

                        return;

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | VENDOR INPUT
                |--------------------------------------------------------------------------
                */

                const vendorInput =
                    isOutsourcing ?
                    `
                    <input
                        type="hidden"
                        name="locations[${locationIndex}][items][${materialIndex}][vendor_id]"
                        class="item-vendor-id"
                        value="${vendorId}"
                    >
                ` :
                    '';


                /*
                |--------------------------------------------------------------------------
                | COST INPUT
                |--------------------------------------------------------------------------
                */

                let costInputs = '';


                if (isOutsourcing) {

                    /*
                    |--------------------------------------------------------------------------
                    | OUTSOURCING
                    |
                    | Hanya:
                    | Material
                    | Width
                    | Vendor Price
                    | Aksi
                    |--------------------------------------------------------------------------
                    */

                    costInputs = `

                <td>

                    <input
                        type="text"
                        name="locations[${locationIndex}][items][${materialIndex}][vendor_price]"
                        class="input rupiah-input"
                        inputmode="decimal"
                        autocomplete="off"
                        placeholder="Contoh: 15000"
                        required
                    >

                </td>

            `;

                } else {

                    /*
                    |--------------------------------------------------------------------------
                    | NON OUTSOURCING
                    |
                    | Price per meter
                    | Production cost
                    | Finishing cost
                    |--------------------------------------------------------------------------
                    */

                    costInputs = `

                <td>

                    <input
                        type="text"
                        name="locations[${locationIndex}][items][${materialIndex}][price_per_meter]"
                        class="input rupiah-input"
                        inputmode="decimal"
                        autocomplete="off"
                        placeholder="Contoh: 6600.00"
                        required
                    >

                </td>


                <td>

                    <input
                        type="text"
                        name="locations[${locationIndex}][items][${materialIndex}][production_cost]"
                        class="input rupiah-input"
                        inputmode="decimal"
                        autocomplete="off"
                        placeholder="Contoh: 9740.00"
                        required
                    >

                </td>


                <td>

                    <input
                        type="text"
                        name="locations[${locationIndex}][items][${materialIndex}][finishing_cost]"
                        class="input rupiah-input"
                        inputmode="decimal"
                        autocomplete="off"
                        placeholder="Contoh: 600.50"
                        required
                    >

                </td>

            `;

                }


                /*
                |--------------------------------------------------------------------------
                | CREATE ROW
                |--------------------------------------------------------------------------
                */

                const row =
                    document.createElement('tr');


                row.className =
                    'material-row';


                row.dataset.categoryId =
                    categoryId;


                row.innerHTML = `

            <td style="width:300px;">

                ${vendorInput}

                <select
                    name="locations[${locationIndex}][items][${materialIndex}][material_id]"
                    class="select select2 material-select"
                    data-category-id="${categoryId}"
                    required
                >

                    ${buildMaterialOptions(categoryId)}

                </select>

            </td>


            <td>

                <select
                    name="locations[${locationIndex}][items][${materialIndex}][width_ids][]"
                    class="select select2 width-select"
                    multiple="multiple"
                    required
                >
                </select>

            </td>


            ${costInputs}


            <td class="text-center">

                <button
                    type="button"
                    class="btn--icon btn-delete-row"
                    aria-label="Hapus material"
                    title="Hapus material"
                >
                    <i class="bi bi-trash3-fill"></i>
                </button>

            </td>

        `;


                /*
                |--------------------------------------------------------------------------
                | APPEND ROW
                |--------------------------------------------------------------------------
                */

                $categoryBlock
                    .find('.location-material-body')
                    .append(row);


                /*
                |--------------------------------------------------------------------------
                | INIT SELECT2
                |--------------------------------------------------------------------------
                */

                $(row)
                    .find('.select2')
                    .select2({
                        width: '100%'
                    });


                /*
                |--------------------------------------------------------------------------
                | INIT MATERIAL CHANGE
                |--------------------------------------------------------------------------
                */

                initMaterialSelect(row);


                /*
                |--------------------------------------------------------------------------
                | NEXT INDEX
                |--------------------------------------------------------------------------
                */

                materialIndexes[locationId]++;

            }


            /*
            |--------------------------------------------------------------------------
            | TOMBOL TAMBAH MATERIAL
            |--------------------------------------------------------------------------
            */

            locationContainer.addEventListener(
                'click',
                function(event) {

                    const button =
                        event.target.closest(
                            '.btn-add-material'
                        );


                    if (!button) {
                        return;
                    }


                    const categoryBlock =
                        button.closest(
                            '.category-block'
                        );


                    if (!categoryBlock) {
                        return;
                    }


                    const locationBlock =
                        button.closest(
                            '.location-block'
                        );


                    if (!locationBlock) {
                        return;
                    }


                    addMaterialRow(
                        locationBlock,
                        categoryBlock
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | FORMAT INPUT SAAT MENGETIK
            |--------------------------------------------------------------------------
            */

            locationContainer.addEventListener(
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
            | FORMAT RUPIAH SAAT BLUR
            |--------------------------------------------------------------------------
            */

            locationContainer.addEventListener(
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
            | HAPUS MATERIAL
            |--------------------------------------------------------------------------
            */

            locationContainer.addEventListener(
                'click',
                function(event) {

                    const deleteButton =
                        event.target.closest(
                            '.btn-delete-row'
                        );


                    if (!deleteButton) {
                        return;
                    }


                    const row =
                        deleteButton.closest(
                            '.material-row'
                        );


                    if (!row) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DESTROY SELECT2
                    |--------------------------------------------------------------------------
                    */

                    $(row)
                        .find('.select2')
                        .each(function() {

                            if (
                                $(this).hasClass(
                                    'select2-hidden-accessible'
                                )
                            ) {

                                $(this).select2(
                                    'destroy'
                                );

                            }

                        });


                    /*
                    |--------------------------------------------------------------------------
                    | HAPUS ROW
                    |--------------------------------------------------------------------------
                    */

                    row.remove();

                }
            );


            /*
            |--------------------------------------------------------------------------
            | HAPUS LOKASI
            |--------------------------------------------------------------------------
            */

            locationContainer.addEventListener(
                'click',
                function(event) {

                    const deleteButton =
                        event.target.closest(
                            '.btn-delete-location'
                        );


                    if (!deleteButton) {
                        return;
                    }


                    const locationBlock =
                        deleteButton.closest(
                            '.location-block'
                        );


                    if (!locationBlock) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DESTROY SEMUA SELECT2 DI LOKASI
                    |--------------------------------------------------------------------------
                    */

                    $(locationBlock)
                        .find('.select2')
                        .each(function() {

                            if (
                                $(this).hasClass(
                                    'select2-hidden-accessible'
                                )
                            ) {

                                $(this).select2(
                                    'destroy'
                                );

                            }

                        });


                    /*
                    |--------------------------------------------------------------------------
                    | HAPUS SELURUH PARENT LOKASI
                    |--------------------------------------------------------------------------
                    */

                    locationBlock.remove();

                }
            );


            /*
            |--------------------------------------------------------------------------
            | SUBMIT UPDATE PRODUCTION COST
            |--------------------------------------------------------------------------
            */

            if (productionCostForm) {

                productionCostForm.addEventListener(
                    'submit',
                    async function(event) {

                        event.preventDefault();


                        const form =
                            this;


                        /*
                        |--------------------------------------------------------------------------
                        | VALIDASI HTML
                        |--------------------------------------------------------------------------
                        */

                        if (!form.checkValidity()) {

                            form.reportValidity();

                            return;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | SUBMIT BUTTON
                        |--------------------------------------------------------------------------
                        */

                        const submitButton =
                            form.querySelector(
                                'button[type="submit"]'
                            );


                        if (!submitButton) {
                            return;
                        }


                        const originalText =
                            submitButton.innerHTML;


                        /*
                        |--------------------------------------------------------------------------
                        | DISABLE BUTTON
                        |--------------------------------------------------------------------------
                        */

                        submitButton.disabled =
                            true;


                        submitButton.innerHTML =
                            'Menyimpan...';


                        try {

                            /*
                            |--------------------------------------------------------------------------
                            | KIRIM FORM
                            |--------------------------------------------------------------------------
                            */

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


                            /*
                            |--------------------------------------------------------------------------
                            | RESPONSE
                            |--------------------------------------------------------------------------
                            */

                            const data =
                                await response.json();


                            /*
                            |--------------------------------------------------------------------------
                            | SUCCESS
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
                                            "{{ route('admin_pc_finishings') }}";

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
                                data.type ||
                                'error',

                                data.message ||
                                'Terjadi kesalahan saat memperbarui Production Cost.'
                            );

                        } catch (error) {

                            console.error(
                                'Gagal memperbarui Production Cost:',
                                error
                            );


                            showToast(
                                'error',
                                'Terjadi kesalahan saat memperbarui Production Cost.'
                            );

                        } finally {

                            /*
                            |--------------------------------------------------------------------------
                            | AKTIFKAN KEMBALI BUTTON
                            |--------------------------------------------------------------------------
                            */

                            submitButton.disabled =
                                false;


                            submitButton.innerHTML =
                                originalText;

                        }

                    }
                );

            }

        });
    </script>
@endsection

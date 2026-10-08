@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">HARGA & TARIF · ONGKOS PRODUKSI</span>
                        {{-- <h1 class="hero-title">Tambah Kebijakan Harga</h1> --}}
                        <p class="hero-sub">Perbarui dan konfigurasi ongkos produksi berdasarkan lokasi, engine, dan
                            material sebagai acuan dalam perhitungan estimasi biaya.</p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">Konfigurasi Ongkos Produksi</span>
                                {{-- <h2 class="card-title">Konfigurasi Ongkos Produksi</h2> --}}
                            </div>
                        </div>

                        <form method="POST" action="{{ route('admin_update_production_cost', $id) }}">
                            @csrf
                            @method('PUT')

                            {{-- Lokasi & Engine --}}
                            <div class="form-grid">

                                {{-- Lokasi --}}
                                <div class="field">
                                    <label class="field-label" for="location_id">
                                        Lokasi <span class="req">*</span>
                                    </label>

                                    <select id="location_id" class="select select2" disabled>
                                        <option value="">Pilih Lokasi</option>

                                        @foreach ($locations as $locationItem)
                                            <option value="{{ $locationItem->id }}"
                                                {{ $locationItem->id == $location->id ? 'selected' : '' }}>
                                                {{ $locationItem->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <input type="hidden" name="location_id" value="{{ $location->id }}">
                                </div>


                                {{-- Engine --}}
                                <div class="field">
                                    <label class="field-label" for="engine_id">
                                        Engine <span class="req">*</span>
                                    </label>

                                    <select id="engine_id" class="select select2" disabled>
                                        <option value="">Pilih Engine</option>

                                        @foreach ($engines as $engineItem)
                                            <option value="{{ $engineItem->id }}"
                                                {{ $engineItem->id == $engine->id ? 'selected' : '' }}>
                                                {{ $engineItem->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <input type="hidden" name="engine_id" value="{{ $engine->id }}">
                                </div>

                            </div>


                            {{-- Detail Ongkos --}}
                            <div class="field" style="margin-top: 24px; overflow: auto;">

                                <div
                                    style="
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: center;
                                        margin-bottom: 12px;
                                    ">
                                    <label class="field-label" style="margin-bottom: 0;">
                                        Detail Ongkos Produksi
                                    </label>

                                    <button type="button" id="btnAddRow" class="btn btn--ghost">
                                        + Tambah
                                    </button>
                                </div>


                                <div class="table-responsive">

                                    <table class="display data-table" id="productionCostTable">
                                        <thead>
                                            <tr>
                                                <th>Material</th>
                                                <th>Ongkos Produksi (Rp)</th>
                                                <th>Ongkos Finishing (Rp)</th>
                                                <th style="width: 80px;">Aksi</th>
                                            </tr>
                                        </thead>

                                        <tbody id="productionCostBody">

                                            {{-- Data existing --}}
                                            @foreach ($productionCosts as $productionCost)
                                                <tr>

                                                    {{-- MATERIAL --}}
                                                    <td>
                                                        <select name="items[{{ $loop->index }}][material_id]"
                                                            class="select select2 material-select" required>
                                                            <option value="">
                                                                Pilih Material
                                                            </option>

                                                            @foreach ($materials as $material)
                                                                <option value="{{ $material->id }}"
                                                                    {{ $material->id == $productionCost->material_id ? 'selected' : '' }}>
                                                                    {{ $material->material_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>


                                                    {{-- ONGKOS PRODUKSI --}}
                                                    <td>
                                                        <input type="text"
                                                            name="items[{{ $loop->index }}][production_cost]"
                                                            class="input rupiah-input" inputmode="numeric"
                                                            autocomplete="off" placeholder="Contoh: 9.740,00"
                                                            value="{{ number_format($productionCost->production_cost, 2, ',', '.') }}"
                                                            required>
                                                    </td>


                                                    {{-- ONGKOS FINISHING --}}
                                                    <td>
                                                        <input type="text"
                                                            name="items[{{ $loop->index }}][finishing_cost]"
                                                            class="input rupiah-input" inputmode="numeric"
                                                            autocomplete="off" placeholder="Contoh: 600,00"
                                                            value="{{ number_format($productionCost->finishing_cost, 2, ',', '.') }}"
                                                            required>
                                                    </td>


                                                    {{-- AKSI --}}
                                                    <td class="text-center">
                                                        <button type="button" class="btn--icon btn-delete-row"
                                                            aria-label="Hapus">
                                                            <i class="bi bi-trash3-fill"></i>
                                                        </button>
                                                    </td>

                                                </tr>
                                            @endforeach

                                        </tbody>

                                    </table>

                                </div>

                            </div>


                            {{-- Action --}}
                            <div class="form-actions">

                                <span class="badge dot success">
                                    Siap disimpan
                                </span>

                                <span class="spacer"></span>

                                <a href="{{ route('admin_production_costs') }}" class="btn btn--ghost">
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
            $('.select2').select2({
                width: '100%'
            });

            const productionCostBody = document.getElementById('productionCostBody');
            const btnAddRow = document.getElementById('btnAddRow');

            let rowIndex = {{ $productionCosts->count() }};

            /**
             * Format angka saat user mengetik.
             *
             * Contoh:
             * 1000      -> 1.000
             * 10000     -> 10.000
             * 533,33    -> 533,33
             * 1000,33   -> 1.000,33
             * 1000.33   -> 1.000,33
             */
            function formatNumberInput(value) {
                if (value === '') return '';

                value = value.replace(/[^\d.,]/g, '');

                const dotIndex = value.indexOf('.');
                const commaIndex = value.indexOf(',');

                let decimalIndex = -1;

                if (commaIndex !== -1) {
                    decimalIndex = commaIndex;
                } else if (dotIndex !== -1) {
                    decimalIndex = dotIndex;
                }

                if (decimalIndex !== -1) {
                    let integerPart = value.substring(0, decimalIndex);

                    let decimalPart = value
                        .substring(decimalIndex + 1)
                        .replace(/[^\d]/g, '')
                        .substring(0, 2);

                    integerPart = integerPart.replace(/[.,]/g, '');

                    if (integerPart === '') {
                        integerPart = '0';
                    }

                    integerPart = Number(integerPart).toLocaleString('id-ID');

                    return integerPart + ',' + decimalPart;
                }

                const numericValue = value.replace(/[^\d]/g, '');

                if (numericValue === '') {
                    return '';
                }

                return Number(numericValue).toLocaleString('id-ID');
            }

            /**
             * Format angka saat input kehilangan fokus.
             *
             * Contoh:
             * 9740       -> 9.740,00
             * 600        -> 600,00
             * 533,33     -> 533,33
             * 1000,33    -> 1.000,33
             */
            function formatRupiah(value) {
                if (value === '') return '';

                value = value.replace(/[^\d.,]/g, '');

                /**
                 * Jika ada koma:
                 * koma dianggap sebagai pemisah desimal.
                 *
                 * Contoh:
                 * 9.740,00 -> 9740 + 00
                 * 1000,33  -> 1000 + 33
                 */
                if (value.includes(',')) {
                    const parts = value.split(',');

                    let integerPart = parts[0] || '0';
                    let decimalPart = parts[1] || '';

                    integerPart = integerPart.replace(/\./g, '');

                    decimalPart = decimalPart
                        .replace(/\D/g, '')
                        .substring(0, 2);

                    if (integerPart === '') {
                        integerPart = '0';
                    }

                    const number = Number(integerPart);

                    if (isNaN(number)) {
                        return '';
                    }

                    while (decimalPart.length < 2) {
                        decimalPart += '0';
                    }

                    return number.toLocaleString('id-ID') + ',' + decimalPart;
                }

                /**
                 * Jika tidak ada koma:
                 * titik dianggap sebagai pemisah ribuan.
                 *
                 * Contoh:
                 * 9.740 -> 9740 -> 9.740,00
                 * 600   -> 600 -> 600,00
                 */
                let integerPart = value.replace(/\./g, '');

                integerPart = integerPart.replace(/\D/g, '');

                if (integerPart === '') {
                    return '';
                }

                const number = Number(integerPart);

                if (isNaN(number)) {
                    return '';
                }

                return number.toLocaleString('id-ID') + ',00';
            }

            /**
             * Membuat dropdown Material untuk row baru.
             */
            function createMaterialSelect() {
                const select = document.createElement('select');

                select.className = 'select select2 material-select';
                select.required = true;
                select.name = 'items[' + rowIndex + '][material_id]';

                const defaultOption = document.createElement('option');

                defaultOption.value = '';
                defaultOption.textContent = 'Pilih Material';

                select.appendChild(defaultOption);

                @foreach ($materials as $material)
                    const option{{ $material->id }} = document.createElement('option');

                    option{{ $material->id }}.value = '{{ $material->id }}';
                    option{{ $material->id }}.textContent = '{{ $material->material_name }}';

                    select.appendChild(option{{ $material->id }});
                @endforeach

                return select;
            }

            /**
             * Membuat input biaya.
             */
            function createCostInput(fieldName, placeholder) {
                const input = document.createElement('input');

                input.type = 'text';
                input.name = 'items[' + rowIndex + '][' + fieldName + ']';
                input.className = 'input rupiah-input';
                input.inputMode = 'decimal';
                input.autocomplete = 'off';
                input.placeholder = placeholder;
                input.required = true;

                return input;
            }

            /**
             * Membuat tombol hapus row.
             */
            function createDeleteButton() {
                const button = document.createElement('button');

                button.type = 'button';
                button.className = 'btn--icon btn-delete-row';
                button.setAttribute('aria-label', 'Hapus');

                const icon = document.createElement('i');

                icon.className = 'bi bi-trash3-fill';

                button.appendChild(icon);

                return button;
            }

            /**
             * Menambahkan row Production Cost baru.
             */
            function addProductionCostRow() {
                const row = document.createElement('tr');

                // Material
                const materialCell = document.createElement('td');

                const materialSelect = createMaterialSelect();

                materialCell.appendChild(materialSelect);
                row.appendChild(materialCell);

                // Production Cost
                const productionCell = document.createElement('td');

                const productionInput = createCostInput(
                    'production_cost',
                    'Contoh: 9740.33'
                );

                productionCell.appendChild(productionInput);
                row.appendChild(productionCell);

                // Finishing Cost
                const finishingCell = document.createElement('td');

                const finishingInput = createCostInput(
                    'finishing_cost',
                    'Contoh: 600.50'
                );

                finishingCell.appendChild(finishingInput);
                row.appendChild(finishingCell);

                // Action
                const actionCell = document.createElement('td');

                actionCell.className = 'text-center';

                const deleteButton = createDeleteButton();

                actionCell.appendChild(deleteButton);
                row.appendChild(actionCell);

                productionCostBody.appendChild(row);

                // Initialize Select2 hanya untuk select baru
                $(materialSelect).select2({
                    width: '100%'
                });

                rowIndex++;
            }

            /**
             * Tombol Tambah Row.
             */
            btnAddRow.addEventListener('click', function() {
                addProductionCostRow();
            });

            /**
             * Format input saat mengetik.
             *
             * Delegated event karena row dapat ditambahkan
             * secara dinamis.
             */
            productionCostBody.addEventListener('input', function(event) {
                if (!event.target.classList.contains('rupiah-input')) {
                    return;
                }

                const input = event.target;

                input.value = formatNumberInput(input.value);

                /**
                 * Cursor selalu dipindahkan ke akhir
                 * setelah formatting.
                 */
                input.setSelectionRange(
                    input.value.length,
                    input.value.length
                );
            });

            /**
             * Format menjadi 2 angka desimal saat blur.
             */
            productionCostBody.addEventListener('blur', function(event) {
                if (!event.target.classList.contains('rupiah-input')) {
                    return;
                }

                const input = event.target;

                if (input.value === '') {
                    return;
                }

                input.value = formatRupiah(input.value);
            }, true);

            /**
             * Hapus row.
             */
            productionCostBody.addEventListener('click', function(event) {
                const deleteButton = event.target.closest('.btn-delete-row');

                if (!deleteButton) {
                    return;
                }

                const row = deleteButton.closest('tr');

                if (row) {
                    row.remove();
                }
            });
        });
    </script>
@endsection

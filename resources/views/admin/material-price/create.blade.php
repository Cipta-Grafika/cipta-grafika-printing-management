@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">HARGA & TARIF · HARGA MATERIAL</span>
                        {{-- <h1 class="hero-title">Tambah Daftar Harga</h1> --}}
                        <p class="hero-sub">Kelola harga material per meter sebagai acuan dalam perhitungan biaya dan
                            estimasi harga produksi.
                        </p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">HARGA & TARIF</span>
                                <h2 class="card-title">Konfigurasi Harga Material</h2>
                            </div>
                        </div>
                        {{-- form disini --}}
                        <form action="{{ route('admin_store_material_price') }}" method="POST" id="materialPriceForm">
                            @csrf

                            <div id="materialPriceRows">

                                {{-- ROW PERTAMA --}}
                                <div class="material-price-row">
                                    <div class="form-grid material-price-form-grid">

                                        {{-- MATERIAL --}}
                                        <div class="field">
                                            <label class="field-label">
                                                Material <span class="req">*</span>
                                            </label>

                                            <select name="material_prices[0][material_id]"
                                                class="select select2 material-select" required>
                                                <option value="">Pilih Material</option>

                                                @foreach ($materials as $material)
                                                    <option value="{{ $material->id }}">
                                                        {{ $material->material_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- HARGA PER METER --}}
                                        <div class="field">
                                            <label class="field-label">
                                                Harga per Meter <span class="req">*</span>
                                            </label>

                                            <input type="text" name="material_prices[0][price_per_meter]"
                                                class="input price-per-meter" inputmode="decimal"
                                                placeholder="Contoh: 6.000,00" required>
                                        </div>

                                        {{-- AKSI --}}
                                        <div class="field material-price-action" style="margin-bottom: 20px;">
                                            <label class="field-label">Aksi</label>

                                            <button type="button" class="btn btn--danger remove-material-price">
                                                Hapus
                                            </button>
                                        </div>

                                    </div>
                                </div>

                            </div>

                            {{-- TAMBAH ROW --}}
                            <div style="margin-top: 15px;">
                                <button type="button" class="btn btn--ghost" id="addMaterialPrice">
                                    + Tambah Material
                                </button>
                            </div>

                            <div class="form-actions">
                                <span class="spacer"></span>

                                <a href="{{ route('admin_material_prices') }}" class="btn btn--ghost">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn--primary">
                                    Simpan Semua
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

            let materialPriceIndex = 1;

            const materialPriceRows = document.getElementById('materialPriceRows');
            const addMaterialPriceButton = document.getElementById('addMaterialPrice');

            /*
            |--------------------------------------------------------------------------
            | FORMAT RUPIAH SAAT MENGETIK
            |--------------------------------------------------------------------------
            */

            function formatRupiahTyping(value) {

                if (value === '') {
                    return '';
                }

                value = value.replace(/[^\d,]/g, '');

                let parts = value.split(',');

                let integerPart = parts[0] || '';
                let decimalPart = parts[1] || '';

                integerPart = integerPart.replace(/\./g, '');

                if (integerPart === '') {
                    integerPart = '0';
                }

                integerPart = Number(integerPart).toLocaleString('id-ID');

                decimalPart = decimalPart
                    .replace(/\D/g, '')
                    .substring(0, 2);

                if (value.includes(',')) {
                    return integerPart + ',' + decimalPart;
                }

                return integerPart;
            }

            /*
            |--------------------------------------------------------------------------
            | FORMAT RUPIAH FINAL
            |--------------------------------------------------------------------------
            */

            function formatRupiahFinal(value) {

                if (value === '') {
                    return '';
                }

                let numericValue = value.replace(/\./g, '');

                numericValue = numericValue.replace(',', '.');

                const number = Number(numericValue);

                if (isNaN(number)) {
                    return '';
                }

                return number.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            /*
            |--------------------------------------------------------------------------
            | INIT FORMATTER HARGA
            |--------------------------------------------------------------------------
            */

            function initPriceFormatter() {

                document.querySelectorAll('.price-per-meter').forEach(function(input) {

                    if (input.dataset.formatterInitialized === 'true') {
                        return;
                    }

                    input.dataset.formatterInitialized = 'true';

                    input.addEventListener('input', function() {
                        this.value = formatRupiahTyping(this.value);
                    });

                    input.addEventListener('focus', function() {

                        if (this.value === '') {
                            return;
                        }

                        this.value = this.value.replace(/\./g, '');
                    });

                    input.addEventListener('blur', function() {

                        if (this.value === '') {
                            return;
                        }

                        this.value = formatRupiahFinal(this.value);
                    });

                });
            }

            /*
            |--------------------------------------------------------------------------
            | TAMBAH ROW
            |--------------------------------------------------------------------------
            */

            addMaterialPriceButton.addEventListener('click', function() {

                const row = document.createElement('div');

                row.classList.add('material-price-row');

                row.innerHTML = `
                <div class="form-grid material-price-form-grid">

                    {{-- MATERIAL --}}
                    <div class="field">
                        <label class="field-label">
                            Material <span class="req">*</span>
                        </label>

                        <select
                            name="material_prices[${materialPriceIndex}][material_id]"
                            class="select select2 material-select"
                            required>
                            <option value="">Pilih Material</option>

                            @foreach ($materials as $material)
                                <option value="{{ $material->id }}">
                                    {{ $material->material_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- HARGA PER METER --}}
                    <div class="field">
                        <label class="field-label">
                            Harga per Meter <span class="req">*</span>
                        </label>

                        <input
                            type="text"
                            name="material_prices[${materialPriceIndex}][price_per_meter]"
                            class="input price-per-meter"
                            inputmode="decimal"
                            placeholder="Contoh: 6.000,00"
                            required>
                    </div>

                    {{-- AKSI --}}
                    <div class="field material-price-action" style="margin-bottom: 20px;">
                        <label class="field-label">Aksi</label>

                        <button
                            type="button"
                            class="btn btn--danger remove-material-price">
                            Hapus
                        </button>
                    </div>

                </div>
            `;

                materialPriceRows.appendChild(row);

                /*
                 * Inisialisasi Select2 hanya untuk row baru
                 */
                $(row).find('.select2').select2({
                    width: '100%'
                });

                /*
                 * Aktifkan formatter harga
                 */
                initPriceFormatter();

                materialPriceIndex++;
            });

            /*
            |--------------------------------------------------------------------------
            | HAPUS ROW
            |--------------------------------------------------------------------------
            */

            materialPriceRows.addEventListener('click', function(event) {

                const removeButton = event.target.closest(
                    '.remove-material-price'
                );

                if (!removeButton) {
                    return;
                }

                const rows = materialPriceRows.querySelectorAll(
                    '.material-price-row'
                );

                /*
                 * Minimal harus ada satu row
                 */
                if (rows.length <= 1) {
                    return;
                }

                removeButton
                    .closest('.material-price-row')
                    .remove();

            });

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
            | INIT FORMATTER AWAL
            |--------------------------------------------------------------------------
            */

            initPriceFormatter();

        });
    </script>
@endsection

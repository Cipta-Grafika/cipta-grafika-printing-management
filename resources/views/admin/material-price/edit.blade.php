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
                            HARGA & TARIF · HARGA MATERIAL
                        </span>

                        <p class="hero-sub">
                            Kelola harga material per meter sebagai acuan dalam
                            perhitungan biaya dan estimasi harga produksi.
                        </p>
                    </div>
                </section>


                <div class="grid">

                    <section class="col-12 card">

                        {{-- CARD HEADER --}}
                        <div class="card-head">
                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    HARGA & TARIF
                                </span>

                                <h2 class="card-title">
                                    Edit Harga Material
                                </h2>

                            </div>
                        </div>


                        {{-- FORM --}}
                        <form action="{{ route('admin_update_material_price', $encodedId) }}" method="POST"
                            id="materialPriceForm">

                            @csrf
                            @method('PUT')


                            <div id="materialPriceRows">

                                {{-- ROW --}}
                                <div class="material-price-row">

                                    <div class="form-grid material-price-form-grid">

                                        {{-- MATERIAL --}}
                                        <div class="field">

                                            <label class="field-label">
                                                Material
                                                <span class="req">*</span>
                                            </label>

                                            <select name="material_id" class="select select2 material-select" required>

                                                <option value="">
                                                    Pilih Material
                                                </option>

                                                @foreach ($materials as $material)
                                                    <option value="{{ $material->id }}"
                                                        {{ $material->id == $materialPrice->material_id ? 'selected' : '' }}>

                                                        {{ $material->material_name }}

                                                    </option>
                                                @endforeach

                                            </select>

                                        </div>


                                        {{-- HARGA PER METER --}}
                                        <div class="field">

                                            <label class="field-label">
                                                Harga per Meter
                                                <span class="req">*</span>
                                            </label>

                                            <input type="text" name="price_per_meter" class="input price-per-meter"
                                                inputmode="decimal" placeholder="Contoh: 6.000,00"
                                                value="{{ number_format((float) $materialPrice->price_per_meter, 2, ',', '.') }}"
                                                required>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            {{-- FORM ACTIONS --}}
                            <div class="form-actions">

                                <span class="spacer"></span>

                                <a href="{{ route('admin_material_prices') }}" class="btn btn--ghost">

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


                    /*
                    |--------------------------------------------------------------------------
                    | INPUT
                    |--------------------------------------------------------------------------
                    */

                    input.addEventListener('input', function() {

                        this.value =
                            formatRupiahTyping(this.value);

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | FOCUS
                    |--------------------------------------------------------------------------
                    */

                    input.addEventListener('focus', function() {

                        if (this.value === '') {
                            return;
                        }

                        this.value =
                            this.value.replace(/\./g, '');

                    });


                    /*
                    |--------------------------------------------------------------------------
                    | BLUR
                    |--------------------------------------------------------------------------
                    */

                    input.addEventListener('blur', function() {

                        if (this.value === '') {
                            return;
                        }

                        this.value =
                            formatRupiahFinal(this.value);

                    });

                });

            }


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

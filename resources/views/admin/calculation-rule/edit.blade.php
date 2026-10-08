@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">HARGA & TARIF · ATURAN PERHITUNGAN</span>
                        <h1 class="hero-title">Edit Aturan Perhitungan</h1>
                        <p class="hero-sub">Perbarui dan konfigurasi aturan perhitungan untuk menyesuaikan dasar perhitungan
                            biaya dan penentuan estimasi harga.</p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">Konfigurasi Aturan Perhitungan</span>
                                {{-- <h2 class="card-title">Konfigurasi Aturan Perhitungan</h2> --}}
                            </div>
                        </div>

                        <form action="{{ route('admin_update_calculation_rule', $calculationRule->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-grid">

                                <div class="field">
                                    <label class="field-label" for="code">
                                        Kode
                                    </label>

                                    <input id="code" name="code" class="input" type="text"
                                        placeholder="Contoh: CR-LF-01" value="{{ old('code', $calculationRule->code) }}">
                                </div>

                                <div class="field">
                                    <label class="field-label" for="name">
                                        Nama Aturan
                                        <span class="req">*</span>
                                    </label>

                                    <input id="name" name="name" class="input" type="text"
                                        placeholder="Contoh: Aturan Perhitungan Large Format"
                                        value="{{ old('name', $calculationRule->name) }}" required>
                                </div>

                                <div class="field">
                                    <label class="field-label" for="engine_id">
                                        Mesin
                                        <span class="req">*</span>
                                    </label>

                                    <select id="engine_id" name="engine_id" class="select select2" required>
                                        <option value="">
                                            Pilih Mesin
                                        </option>

                                        @foreach ($engines as $engine)
                                            <option value="{{ $engine->id }}"
                                                {{ old('engine_id', $calculationRule->engine_id) == $engine->id ? 'selected' : '' }}>
                                                {{ $engine->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="field">
                                    <label class="field-label" for="minimum_charge">
                                        Minimum Charge
                                        <span class="req">*</span>
                                    </label>

                                    <input id="minimum_charge" name="minimum_charge" class="input minimum-charge-input"
                                        type="text" inputmode="decimal" placeholder="Contoh: 1,00"
                                        value="{{ old('minimum_charge', number_format($calculationRule->minimum_charge, 2, ',', '.')) }}"
                                        required>
                                </div>

                                <div class="field">
                                    <label class="field-label" for="status">
                                        Status
                                    </label>

                                    <select id="status" name="status" class="select select2">
                                        <option value="Active"
                                            {{ old('status', $calculationRule->status) === 'Active' ? 'selected' : '' }}>
                                            Active
                                        </option>

                                        <option value="Inactive"
                                            {{ old('status', $calculationRule->status) === 'Inactive' ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                    </select>
                                </div>

                                <div class="field">
                                    <label class="field-label" for="description">
                                        Deskripsi
                                    </label>

                                    <textarea id="description" name="description" class="input" rows="4"
                                        placeholder="Masukkan deskripsi aturan perhitungan...">{{ old('description', $calculationRule->description) }}</textarea>
                                </div>

                            </div>

                            <div class="form-actions">

                                <span class="badge dot success">
                                    Siap disimpan
                                </span>

                                <span class="spacer"></span>

                                <a href="{{ route('admin_calculation_rules') }}" class="btn btn--ghost">
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

            $('.select2').select2({
                width: '100%',
                placeholder: 'Pilih...'
            });

            const minimumChargeInput =
                document.getElementById('minimum_charge');

            function formatNumberInput(value) {

                if (value === '') {
                    return '';
                }

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

                    let integerPart =
                        value.substring(0, decimalIndex);

                    let decimalPart = value
                        .substring(decimalIndex + 1)
                        .replace(/[^\d]/g, '')
                        .substring(0, 2);

                    integerPart =
                        integerPart.replace(/[.,]/g, '');

                    if (integerPart === '') {
                        integerPart = '0';
                    }

                    integerPart =
                        Number(integerPart)
                        .toLocaleString('id-ID');

                    return integerPart + ',' + decimalPart;
                }

                const numericValue =
                    value.replace(/[^\d]/g, '');

                if (numericValue === '') {
                    return '';
                }

                return Number(numericValue)
                    .toLocaleString('id-ID');
            }

            function formatDecimal(value) {

                if (value === '') {
                    return '';
                }

                value = value.replace(/[^\d.,]/g, '');

                const commaIndex = value.indexOf(',');
                const dotIndex = value.indexOf('.');

                let decimalIndex = -1;

                if (commaIndex !== -1) {
                    decimalIndex = commaIndex;
                } else if (dotIndex !== -1) {
                    decimalIndex = dotIndex;
                }

                if (decimalIndex !== -1) {

                    let integerPart =
                        value.substring(0, decimalIndex);

                    let decimalPart = value
                        .substring(decimalIndex + 1)
                        .replace(/\D/g, '')
                        .substring(0, 2);

                    integerPart =
                        integerPart.replace(/[.,]/g, '');

                    if (integerPart === '') {
                        integerPart = '0';
                    }

                    integerPart =
                        Number(integerPart)
                        .toLocaleString('id-ID');

                    while (decimalPart.length < 2) {
                        decimalPart += '0';
                    }

                    return integerPart + ',' + decimalPart;
                }

                const numericValue =
                    value.replace(/[^\d]/g, '');

                if (numericValue === '') {
                    return '';
                }

                return Number(numericValue)
                    .toLocaleString('id-ID') + ',00';
            }

            minimumChargeInput.addEventListener('input', function() {

                this.value =
                    formatNumberInput(this.value);

                this.setSelectionRange(
                    this.value.length,
                    this.value.length
                );
            });

            minimumChargeInput.addEventListener('blur', function() {

                if (this.value === '') {
                    return;
                }

                this.value =
                    formatDecimal(this.value);
            });

        });
    </script>
@endsection

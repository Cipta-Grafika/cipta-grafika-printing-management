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
                        <p class="hero-sub">Tambahkan dan konfigurasi ongkos produksi berdasarkan lokasi, engine, dan
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

                        <form method="POST" action="{{ route('admin_store_production_cost') }}">
                            @csrf

                            {{-- Lokasi & Engine --}}
                            <div class="form-grid">

                                {{-- Lokasi --}}
                                <div class="field">
                                    <label class="field-label" for="location_id">
                                        Lokasi <span class="req">*</span>
                                    </label>

                                    <select id="location_id" name="location_ids[]" class="select select2" required>
                                        <option value="">Pilih Lokasi</option>

                                        @foreach ($locations as $location)
                                            <option value="{{ $location->id }}">
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Engine --}}
                                <div class="field">
                                    <label class="field-label" for="engine_id">
                                        Engine <span class="req">*</span>
                                    </label>

                                    <select id="engine_id" name="engine_id" class="select select2" required>
                                        <option value="">Pilih Engine</option>

                                        @foreach ($engines as $engine)
                                            <option value="{{ $engine->id }}">
                                                {{ $engine->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>

                            {{-- Detail Ongkos --}}
                            <div class="field" style="margin-top: 24px; overflow: auto;">

                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
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
                                            {{-- Row pertama dibuat oleh JavaScript --}}
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

            // =========================================================
            // SELECT2
            // =========================================================
            $('.select2').select2({
                width: '100%'
            });


            // =========================================================
            // VARIABLE
            // =========================================================
            const productionCostBody = document.getElementById('productionCostBody');
            const btnAddRow = document.getElementById('btnAddRow');

            let rowIndex = 0;


            // =========================================================
            // FORMAT ANGKA SAAT MENGETIK
            //
            // 1000      -> 1.000
            // 10000     -> 10.000
            // 533,33    -> 533,33
            // 533.33    -> 533,33
            // 1000,33   -> 1.000,33
            // 1000.33   -> 1.000,33
            // =========================================================
            function formatNumberInput(value) {

                if (value === '') {
                    return '';
                }

                // Hanya izinkan angka, titik, dan koma
                value = value.replace(/[^\d.,]/g, '');

                // Cari posisi titik dan koma
                const dotIndex = value.indexOf('.');
                const commaIndex = value.indexOf(',');

                let decimalIndex = -1;

                // =====================================================
                // KALAU ADA KOMA
                // Koma dianggap decimal separator
                // =====================================================
                if (commaIndex !== -1) {

                    decimalIndex = commaIndex;

                    // =====================================================
                    // KALAU HANYA ADA TITIK
                    // Titik dianggap decimal separator SAAT INPUT USER
                    // =====================================================
                } else if (dotIndex !== -1) {

                    decimalIndex = dotIndex;
                }


                // =====================================================
                // ADA DECIMAL SEPARATOR
                // =====================================================
                if (decimalIndex !== -1) {

                    let integerPart = value.substring(0, decimalIndex);

                    let decimalPart = value
                        .substring(decimalIndex + 1)
                        .replace(/[^\d]/g, '')
                        .substring(0, 2);


                    // Bersihkan separator dari integer
                    integerPart = integerPart.replace(/[.,]/g, '');


                    if (integerPart === '') {
                        integerPart = '0';
                    }


                    // Format ribuan
                    integerPart = Number(integerPart).toLocaleString('id-ID');


                    // Gunakan koma sebagai decimal separator
                    return integerPart + ',' + decimalPart;
                }


                // =====================================================
                // BILANGAN BULAT
                // =====================================================
                const numericValue = value.replace(/[^\d]/g, '');

                if (numericValue === '') {
                    return '';
                }


                return Number(numericValue).toLocaleString('id-ID');
            }


            // =========================================================
            // FORMAT RUPIAH SAAT BLUR
            //
            // 1000      -> 1.000,00
            // 9740      -> 9.740,00
            // 533,33    -> 533,33
            // 533.33    -> 533,33
            // 1000,33   -> 1.000,33
            // =========================================================
            function formatRupiah(value) {

                if (value === '') {
                    return '';
                }

                value = value.replace(/[^\d.,]/g, '');


                // =====================================================
                // KOMA = DECIMAL
                // =====================================================
                if (value.includes(',')) {

                    let parts = value.split(',');

                    let integerPart = parts[0] || '0';

                    let decimalPart = parts[1] || '';


                    // Bersihkan titik ribuan
                    integerPart = integerPart.replace(/\./g, '');

                    // Hanya angka untuk decimal
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


                    // Tambahkan 0 sampai 2 digit decimal
                    while (decimalPart.length < 2) {
                        decimalPart += '0';
                    }


                    return number.toLocaleString('id-ID') + ',' + decimalPart;
                }


                // =====================================================
                // TIDAK ADA KOMA
                //
                // Kalau hanya ada titik:
                // 9.740 -> 9.740,00
                // 10.000 -> 10.000,00
                //
                // Titik dianggap PEMISAH RIBUAN.
                // =====================================================
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


            // =========================================================
            // TAMBAH ROW
            // =========================================================
            function addProductionCostRow() {

                const row = document.createElement('tr');

                row.innerHTML = `
                <td>
                    <select
                        name="items[${rowIndex}][material_id]"
                        class="select select2 material-select"
                        required
                    >
                        <option value="">Pilih Material</option>

                        @foreach ($materials as $material)
                            <option value="{{ $material->id }}">
                                {{ $material->material_name }}
                            </option>
                        @endforeach
                    </select>
                </td>

                <td>
                    <input
                        type="text"
                        name="items[${rowIndex}][production_cost]"
                        class="input rupiah-input"
                        inputmode="decimal"
                        autocomplete="off"
                        placeholder="Contoh: 9740.33"
                        required
                    >
                </td>

                <td>
                    <input
                        type="text"
                        name="items[${rowIndex}][finishing_cost]"
                        class="input rupiah-input"
                        inputmode="decimal"
                        autocomplete="off"
                        placeholder="Contoh: 600.50"
                        required
                    >
                </td>

                <td class="text-center">
                    <button
                        type="button"
                        class="btn--icon btn-delete-row"
                        aria-label="Hapus"
                    >
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                </td>
            `;


                productionCostBody.appendChild(row);


                // =====================================================
                // SELECT2 UNTUK ROW BARU
                // =====================================================
                $(row).find('.select2').select2({
                    width: '100%'
                });


                rowIndex++;
            }


            // =========================================================
            // TOMBOL TAMBAH ROW
            // =========================================================
            btnAddRow.addEventListener('click', function() {
                addProductionCostRow();
            });


            // =========================================================
            // FORMAT INPUT SAAT MENGETIK
            // =========================================================
            productionCostBody.addEventListener('input', function(event) {

                if (!event.target.classList.contains('rupiah-input')) {
                    return;
                }

                const input = event.target;


                input.value = formatNumberInput(input.value);


                // Cursor ke posisi paling belakang
                input.setSelectionRange(
                    input.value.length,
                    input.value.length
                );
            });


            // =========================================================
            // FORMAT RUPIAH SAAT BLUR
            // =========================================================
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


            // =========================================================
            // HAPUS ROW
            // =========================================================
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


            // =========================================================
            // ROW PERTAMA
            // =========================================================
            addProductionCostRow();

        });
    </script>
@endsection

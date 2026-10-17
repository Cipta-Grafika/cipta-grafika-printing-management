@extends('admin_master')

@section('contents')
    <style>
        .configuration-card {
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--bg-card);
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-card);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .configuration-card:hover {
            border-color: var(--primary);
        }

        .configuration-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 16px;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-soft);
        }

        .configuration-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .configuration-title h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
            color: var(--t-base);
        }

        .category-checkboxes {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        .category-checkbox {
            cursor: pointer;
            user-select: none;
            display: inline-flex;
            align-items: center;
            margin: 0;
        }

        .category-checkbox input {
            display: none;
        }

        .category-checkbox-box {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--t-base);
            transition: all 0.15s ease;
        }

        .category-checkbox:hover .category-checkbox-box {
            background: var(--bg-hover);
            border-color: var(--border);
        }

        .category-checkbox input:checked + .category-checkbox-box {
            background: var(--primary-soft);
            border-color: var(--primary);
            color: var(--primary);
            font-weight: 600;
        }

        .category-checkbox-check {
            width: 18px;
            height: 18px;
            flex: 0 0 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            color: transparent;
            background: transparent;
            transition: all 0.15s ease;
        }

        .category-checkbox input:checked + .category-checkbox-box .category-checkbox-check {
            color: #ffffff;
            background: var(--primary);
            border-color: var(--primary);
        }

        .cutting-item-box {
            border: 1px solid var(--border);
            border-radius: 10px;
            background: var(--bg-card);
            padding: 16px;
            margin-bottom: 16px;
            box-shadow: var(--shadow-sm);
        }

        .cutting-item-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
            gap: 16px;
        }

        .cutting-tier-wrapper {
            border-radius: 8px;
            border: 1px solid var(--border);
            overflow-x: auto;
            background: var(--bg-card);
        }

        .cutting-tier-table {
            margin: 0;
            width: 100%;
            border-collapse: collapse;
        }

        .cutting-tier-table thead tr {
            background: var(--bg-muted);
        }

        .cutting-tier-table th {
            font-size: 11px;
            font-family: 'JetBrains Mono', monospace;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 10px 12px;
            color: var(--t-base);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        .cutting-tier-table td {
            padding: 8px 10px;
            border-bottom: 1px solid var(--border-soft);
            vertical-align: middle;
        }

        .cutting-tier-table .cell-side {
            vertical-align: middle;
            font-weight: 600;
            background: var(--bg-card);
            color: var(--t-base);
            text-align: center;
        }

        .cutting-tier-table tbody tr.row-division {
            background: var(--bg-hover);
        }

        .cutting-tier-table tbody tr.row-divider {
            border-top: 2px solid var(--border);
        }

        .tier-price-input {
            width: 100%;
            min-width: 110px;
            padding: 8px 10px;
            font-size: 13px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--t-base);
            text-align: right;
            font-family: inherit;
            transition: border-color 0.18s, box-shadow 0.18s;
        }

        .tier-price-input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px var(--primary-ring, rgba(59, 130, 246, 0.25));
        }

        .tier-price-readonly {
            background-color: var(--bg-muted) !important;
            color: var(--t-muted) !important;
            cursor: not-allowed;
            border-color: var(--border-soft) !important;
        }
    </style>

    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">BIAYA · JASA POTONG & ONGKOS POTONG</span>
                        <p class="hero-sub">
                            Tambahkan ongkos produksi jasa potong (cutting & potong guillotine) berdasarkan mesin, lokasi, kategori, dan jenis potong.
                            Untuk potong guillotine, tentukan ongkos minimal per ukuran (A3, A4, A5, &le; A6). Untuk jasa cutting lain, tarif diatur per tier kuantiti.
                        </p>
                    </div>
                </section>

                <div class="grid">
                    <section class="col-12 card">
                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">Konfigurasi Ongkos Produksi Jasa Potong</span>
                            </div>
                        </div>

                        <form id="cuttingProductionCostForm" method="POST" action="{{ route('admin_store_pc_finishing_cutting') }}">
                            @csrf

                            {{-- CONTAINER KONFIGURASI --}}
                            <div id="cuttingConfigurationContainer"></div>

                            {{-- BUTTON TAMBAH KONFIGURASI --}}
                            <div style="margin-top: 16px;">
                                <button type="button" id="btnAddCuttingConfiguration" class="btn btn--ghost">
                                    + Tambah Konfigurasi
                                </button>
                            </div>

                            {{-- FORM ACTIONS --}}
                            <div class="form-actions" style="margin-top: 24px;">
                                <span class="badge dot success">
                                    Formulir Aktif
                                </span>
                                <span class="spacer"></span>
                                <a href="{{ route('admin_production_cost_finishing_cuttings') }}" class="btn btn--ghost">
                                    Batal
                                </a>
                                <button type="submit" id="btnSubmitForm" class="btn btn--primary">
                                    Simpan Ongkos
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
            const container = document.getElementById('cuttingConfigurationContainer');
            const btnAddConfiguration = document.getElementById('btnAddCuttingConfiguration');

            // Data Master dari Controller (Dinamis dari Database)
            const engines = @json($engines);
            const locations = @json($locations);
            const categories = @json($categories);
            const cuttings = @json($cuttings);
            const vendors = @json($vendors);
            const defaultCuttingType = @json($default_cutting_type ?? 'cutting');

            let configurationIndex = 0;

            const tiers = [
                { key: 'tier_1', label: '1 lbr' },
                { key: 'tier_2_5', label: '2-5 lbr' },
                { key: 'tier_6_50', label: '6-50 lbr' },
                { key: 'tier_100', label: '100 lbr' },
                { key: 'tier_300', label: '300 lbr' },
                { key: 'tier_500', label: '500 lbr' }
            ];

            const potongSizes = [
                { key: 'A3', label: 'A3' },
                { key: 'A4', label: 'A4' },
                { key: 'A5', label: 'A5' },
                { key: '<=A6', label: '≤ A6' }
            ];

            function initSelect2(element) {
                $(element).find('.select2').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        return;
                    }
                    $(this).select2({ width: '100%' });
                });
            }

            function formatRupiahNumber(number) {
                if (isNaN(number) || number === null || number === '') {
                    return '';
                }
                return Number(number).toLocaleString('id-ID');
            }

            function parseRupiahInput(value) {
                if (!value) return 0;
                const clean = value.toString().replace(/[^0-9]/g, '');
                return clean ? parseInt(clean, 10) : 0;
            }

            /*
            |--------------------------------------------------------------------------
            | RENDER TABEL RINCIAN HARGA (POTONG VS TIER CUTTING)
            |--------------------------------------------------------------------------
            */
            function renderCuttingTable(tableContainer, confIndex, itemIndex, isPotong) {
                if (isPotong) {
                    let sizeHeaderHtml = '';
                    let potongUmumInputs = '';
                    let potongDivisiInputs = '';

                    potongSizes.forEach(s => {
                        sizeHeaderHtml += `<th class="text-center" style="font-weight: 600; min-width: 130px;">${s.label}</th>`;
                        potongUmumInputs += `
                            <td>
                                <input type="text"
                                    class="tier-price-input potong-price-input input-umum"
                                    data-size="${s.key}"
                                    data-type="general"
                                    name="configurations[${confIndex}][items][${itemIndex}][potong_prices][general][${s.key}]"
                                    placeholder="Rp 0"
                                    autocomplete="off"
                                    required>
                            </td>
                        `;
                        potongDivisiInputs += `
                            <td>
                                <input type="text"
                                    class="tier-price-input potong-price-input input-divisi"
                                    data-size="${s.key}"
                                    data-type="division"
                                    name="configurations[${confIndex}][items][${itemIndex}][potong_prices][division][${s.key}]"
                                    placeholder="Rp 0"
                                    autocomplete="off"
                                    required>
                            </td>
                        `;
                    });

                    tableContainer.innerHTML = `
                        <div class="table-responsive cutting-tier-wrapper">
                            <table class="display data-table cutting-tier-table">
                                <thead>
                                    <tr>
                                        <th style="width: 170px; font-weight: 600;">Tipe Harga</th>
                                        ${sizeHeaderHtml}
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="vertical-align: middle;">
                                            <span class="badge info">
                                                Harga Umum
                                            </span>
                                        </td>
                                        ${potongUmumInputs}
                                    </tr>
                                    <tr class="row-division">
                                        <td style="vertical-align: middle;">
                                            <span class="badge success">
                                                Harga Divisi
                                            </span>
                                        </td>
                                        ${potongDivisiInputs}
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="field-help" style="margin-top: 6px; margin-bottom: 0; font-size: 12px;">
                            Ongkos Potong Minimal berlaku per ukuran jadi cetakan (A3, A4, A5, &le; A6). Input manual untuk <strong>Harga Umum</strong> dan <strong>Harga Divisi</strong>.
                        </p>
                    `;
                } else {
                    let tierHeaderHtml = '';
                    tiers.forEach(t => {
                        tierHeaderHtml += `<th class="text-center" style="font-weight: 600;">${t.label}</th>`;
                    });

                    let umumInputs = '';
                    let divisiInputs = '';
                    tiers.forEach(t => {
                        umumInputs += `
                            <td>
                                <input type="text"
                                    class="tier-price-input input-umum"
                                    data-tier="${t.key}"
                                    name="configurations[${confIndex}][items][${itemIndex}][prices][general][${t.key}]"
                                    placeholder="Rp 0"
                                    autocomplete="off"
                                    required>
                            </td>
                        `;
                        divisiInputs += `
                            <td>
                                <input type="text"
                                    class="tier-price-input tier-price-readonly input-divisi"
                                    data-tier="${t.key}"
                                    name="configurations[${confIndex}][items][${itemIndex}][prices][division][${t.key}]"
                                    placeholder="Rp 0"
                                    readonly
                                    tabindex="-1">
                            </td>
                        `;
                    });

                    tableContainer.innerHTML = `
                        <div class="table-responsive cutting-tier-wrapper">
                            <table class="display data-table cutting-tier-table">
                                <thead>
                                    <tr>
                                        <th style="width: 170px; font-weight: 600;">Tipe Harga</th>
                                        ${tierHeaderHtml}
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="vertical-align: middle;">
                                            <span class="badge info">
                                                Harga Umum
                                            </span>
                                        </td>
                                        ${umumInputs}
                                    </tr>
                                    <tr class="row-division">
                                        <td style="vertical-align: middle;">
                                            <span class="badge success" title="Rumus: Harga Umum × 80%">
                                                Harga Divisi (80%)
                                            </span>
                                        </td>
                                        ${divisiInputs}
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="field-help" style="margin-top: 6px; margin-bottom: 0; font-size: 12px;">
                            Input manual <strong>Harga Umum</strong> per tier kuantiti. <strong>Harga Divisi</strong> otomatis dihitung sebesar <strong>80%</strong>.
                        </p>
                    `;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | BUAT BLOK ITEM JASA POTONG (CUTTING / POTONG)
            |--------------------------------------------------------------------------
            */
            function createCuttingItem(confIndex, itemIndex, defaultCuttingId = null) {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'cutting-item-box';
                itemDiv.dataset.itemIndex = itemIndex;

                let selectedCuttingId = defaultCuttingId;
                if (!selectedCuttingId && cuttings.length > 0) {
                    if (defaultCuttingType === 'potong') {
                        const potongItem = cuttings.find(c => c.name.toLowerCase().trim() === 'potong');
                        selectedCuttingId = potongItem ? potongItem.id : cuttings[0].id;
                    } else {
                        const nonPotongItem = cuttings.find(c => c.name.toLowerCase().trim() !== 'potong');
                        selectedCuttingId = nonPotongItem ? nonPotongItem.id : cuttings[0].id;
                    }
                }

                let cuttingOptions = '<option value="">Pilih Jasa Potong</option>';
                cuttings.forEach(c => {
                    const isSelected = selectedCuttingId && String(c.id) === String(selectedCuttingId);
                    cuttingOptions += `<option value="${c.id}" ${isSelected ? 'selected' : ''}>${c.name}</option>`;
                });

                itemDiv.innerHTML = `
                    <div class="cutting-item-header">
                        <div style="flex: 1; max-width: 380px;">
                            <label class="field-label" style="font-size: 13px; font-weight: 600; margin-bottom: 4px;">
                                Jasa Potong <span class="req">*</span>
                            </label>
                            <select name="configurations[${confIndex}][items][${itemIndex}][cutting_id]" class="select select2 cutting-select" required>
                                ${cuttingOptions}
                            </select>
                        </div>
                        <div>
                            <button type="button" class="btn btn--ghost btn-remove-cutting-item" style="color: var(--danger); padding: 8px 12px;">
                                <i class="bi bi-trash3-fill"></i> Hapus Jasa Potong
                            </button>
                        </div>
                    </div>

                    <div class="cutting-table-container"></div>
                `;

                const selectedObj = cuttings.find(c => String(c.id) === String(selectedCuttingId));
                const isPotong = selectedObj && selectedObj.name.toLowerCase().trim() === 'potong';
                const tableContainer = itemDiv.querySelector('.cutting-table-container');
                renderCuttingTable(tableContainer, confIndex, itemIndex, isPotong);

                return itemDiv;
            }

            /*
            |--------------------------------------------------------------------------
            | BUAT BLOK KONFIGURASI
            |--------------------------------------------------------------------------
            */
            function createConfigurationBlock() {
                const currentIndex = configurationIndex++;
                const card = document.createElement('div');
                card.className = 'configuration-card';
                card.dataset.index = currentIndex;

                // Bangun Opsi Mesin (Default ke Digital Print A3+)
                let engineOptions = '';
                engines.forEach(e => {
                    const isSelected = e.name.toLowerCase().includes('digital print a3');
                    engineOptions += `<option value="${e.id}" ${isSelected ? 'selected' : ''}>${e.name}</option>`;
                });

                // Bangun Opsi Lokasi
                let locationOptions = '';
                locations.forEach(l => {
                    locationOptions += `<option value="${l.id}">${l.name}</option>`;
                });

                // Bangun Opsi Vendor (Outsourcing)
                let vendorOptions = '<option value="">Pilih Vendor</option>';
                vendors.forEach(v => {
                    vendorOptions += `<option value="${v.id}">${v.name}</option>`;
                });

                // Bangun Checkbox Kategori
                let categoryCheckboxes = '';
                categories.forEach(c => {
                    categoryCheckboxes += `
                        <label class="category-checkbox">
                            <input type="checkbox" name="configurations[${currentIndex}][category_ids][]" value="${c.id}">
                            <span class="category-checkbox-box">
                                <span class="category-checkbox-check">✓</span>
                                <span class="category-checkbox-label">${c.name}</span>
                            </span>
                        </label>
                    `;
                });

                card.innerHTML = `
                    <div class="configuration-header">
                        <div class="configuration-title">
                            <span class="badge primary">
                                Konfigurasi #${currentIndex + 1}
                            </span>
                            <h3>Lingkup Mesin, Lokasi & Kategori</h3>
                        </div>
                        <div>
                            <button type="button" class="btn btn--ghost btn-remove-configuration" style="color: var(--danger); border: none;">
                                <i class="bi bi-trash3-fill"></i> Hapus Konfigurasi
                            </button>
                        </div>
                    </div>

                    <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px; margin-bottom: 20px;">
                        {{-- MESIN --}}
                        <div class="field">
                            <label class="field-label">Mesin <span class="req">*</span></label>
                            <select name="configurations[${currentIndex}][engine_id]" class="select select2 engine-select" required>
                                ${engineOptions}
                            </select>
                        </div>

                        {{-- LOKASI --}}
                        <div class="field">
                            <label class="field-label">Lokasi <span class="req">*</span></label>
                            <select name="configurations[${currentIndex}][location_ids][]" class="select select2 location-select" multiple required>
                                ${locationOptions}
                            </select>
                        </div>

                        {{-- VENDOR (OUTSOURCING) --}}
                        <div class="field vendor-field" style="display: none;">
                            <label class="field-label">Vendor Outsourcing <span class="req">*</span></label>
                            <select name="configurations[${currentIndex}][vendor_id]" class="select select2 vendor-select">
                                ${vendorOptions}
                            </select>
                        </div>
                    </div>

                    {{-- KATEGORI --}}
                    <div class="field" style="margin-bottom: 24px;">
                        <label class="field-label">
                            Pilih Kategori Kertas / Bahan Yang Berlaku <span class="req">*</span>
                        </label>
                        <p class="field-help" style="margin-top: 2px;">
                            Pilih satu atau beberapa kategori kertas yang memiliki biaya jasa potong yang sama pada baris ini.
                        </p>
                        <div class="category-checkboxes">
                            ${categoryCheckboxes}
                        </div>
                    </div>

                    {{-- ITEM CUTTING & HARGA TIER --}}
                    <div class="field" style="border-top: 1px dashed var(--border); padding-top: 20px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                            <div>
                                <label class="field-label" style="margin-bottom: 2px; font-weight: 700; font-size: 15px;">
                                    Daftar Jasa Potong & Rincian Tier Harga
                                </label>
                                <p class="field-help" style="margin: 0;">
                                    Input manual <strong>Harga Umum</strong> per tier kuantiti. <strong>Harga Divisi</strong> otomatis dihitung sebesar <strong>80%</strong>.
                                </p>
                            </div>
                            <button type="button" class="btn btn--ghost btn-add-cutting" style="border-color: var(--border);">
                                + Tambah Jasa Potong
                            </button>
                        </div>

                        <div class="cutting-items-container"></div>
                    </div>
                `;

                container.appendChild(card);
                initSelect2(card);

                // Tambahkan 1 item cutting default
                const itemsContainer = card.querySelector('.cutting-items-container');
                const initialItem = createCuttingItem(currentIndex, 0);
                itemsContainer.appendChild(initialItem);
                initSelect2(initialItem);

                // Handle toggle Vendor Outsourcing
                const locationSelect = $(card).find('.location-select');
                const vendorField = card.querySelector('.vendor-field');

                locationSelect.on('change', function() {
                    const selectedTexts = $(this).find('option:selected').map(function() {
                        return $(this).text().trim().toLowerCase();
                    }).get();

                    const isOutsourcing = selectedTexts.includes('outsourcing');
                    if (isOutsourcing) {
                        vendorField.style.display = 'block';
                    } else {
                        vendorField.style.display = 'none';
                        $(card).find('.vendor-select').val('').trigger('change');
                    }
                });
            }

            // Inisialisasi blok konfigurasi pertama saat halaman dimuat
            createConfigurationBlock();

            // Tambah Konfigurasi Baru
            btnAddConfiguration.addEventListener('click', function() {
                createConfigurationBlock();
            });

            // Event Delegation: Hapus Konfigurasi
            container.addEventListener('click', function(event) {
                const removeConfBtn = event.target.closest('.btn-remove-configuration');
                if (removeConfBtn) {
                    const cards = container.querySelectorAll('.configuration-card');
                    if (cards.length <= 1) {
                        showToast('error', 'Minimal harus ada 1 blok konfigurasi.');
                        return;
                    }
                    const card = removeConfBtn.closest('.configuration-card');
                    card.remove();
                    return;
                }

                // Tambah Jasa Potong di dalam Konfigurasi
                const addCuttingBtn = event.target.closest('.btn-add-cutting');
                if (addCuttingBtn) {
                    const card = addCuttingBtn.closest('.configuration-card');
                    const confIndex = card.dataset.index;
                    const itemsContainer = card.querySelector('.cutting-items-container');
                    const currentItemsCount = itemsContainer.querySelectorAll('.cutting-item-box').length;
                    const newItem = createCuttingItem(confIndex, currentItemsCount);
                    itemsContainer.appendChild(newItem);
                    initSelect2(newItem);
                    return;
                }

                // Hapus Jasa Potong di dalam Konfigurasi
                const removeCuttingBtn = event.target.closest('.btn-remove-cutting-item');
                if (removeCuttingBtn) {
                    const itemsContainer = removeCuttingBtn.closest('.cutting-items-container');
                    const items = itemsContainer.querySelectorAll('.cutting-item-box');
                    if (items.length <= 1) {
                        showToast('error', 'Minimal harus ada 1 jasa potong dalam konfigurasi.');
                        return;
                    }
                    removeCuttingBtn.closest('.cutting-item-box').remove();
                    return;
                }
            });

            /*
            |--------------------------------------------------------------------------
            | EVENT LISTENER: PERUBAHAN PILIHAN JASA POTONG (CUTTING VS POTONG)
            |--------------------------------------------------------------------------
            */
            $(container).on('change', '.cutting-select', function() {
                const itemBox = $(this).closest('.cutting-item-box');
                const confCard = $(this).closest('.configuration-card');
                const confIndex = confCard.data('index');
                const itemIndex = itemBox.data('itemIndex');
                const selectedId = $(this).val();

                const cuttingObj = cuttings.find(c => String(c.id) === String(selectedId));
                const isPotong = cuttingObj && cuttingObj.name.toLowerCase().trim() === 'potong';
                const tableContainer = itemBox.find('.cutting-table-container')[0];

                if (tableContainer) {
                    renderCuttingTable(tableContainer, confIndex, itemIndex, isPotong);
                }
            });

            /*
            |--------------------------------------------------------------------------
            | KALKULASI REAL-TIME & FORMAT RUPIAH
            |--------------------------------------------------------------------------
            */
            container.addEventListener('input', function(event) {
                const priceInput = event.target.closest('.tier-price-input');
                if (!priceInput) return;

                const rawVal = parseRupiahInput(priceInput.value);
                // Format input kembali ke Rupiah dengan pemisah ribuan
                priceInput.value = rawVal > 0 ? formatRupiahNumber(rawVal) : '';

                // Khusus untuk tier table non-potong: hitung harga divisi otomatis (80%)
                if (priceInput.classList.contains('input-umum') && priceInput.dataset.tier) {
                    const tier = priceInput.dataset.tier;
                    const cuttingBox = priceInput.closest('.cutting-item-box');

                    if (cuttingBox) {
                        const inputDivisi = cuttingBox.querySelector(`.input-divisi[data-tier="${tier}"]`);
                        if (inputDivisi) {
                            if (rawVal > 0) {
                                // Rumus: tier dari harga umum dikali 80%
                                const hargaDivisi = Math.round(rawVal * 0.8);
                                inputDivisi.value = formatRupiahNumber(hargaDivisi);
                            } else {
                                inputDivisi.value = '';
                            }
                        }
                    }
                }
            });

            /*
            |--------------------------------------------------------------------------
            | SUBMIT FORM DENGAN VALIDASI & TOAST
            |--------------------------------------------------------------------------
            */
            const form = document.getElementById('cuttingProductionCostForm');
            form.addEventListener('submit', async function(event) {
                event.preventDefault();

                // 1. Validasi HTML5 bawaan form
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                // 2. Validasi Kategori: Setiap konfigurasi harus memiliki minimal satu kategori yang dicentang
                const configurations = container.querySelectorAll('.configuration-card');
                if (configurations.length === 0) {
                    showToast('error', 'Minimal harus ada 1 blok konfigurasi ongkos potong.');
                    return;
                }

                for (let i = 0; i < configurations.length; i++) {
                    const conf = configurations[i];
                    const checkedCategories = conf.querySelectorAll('.category-checkbox input:checked');
                    if (checkedCategories.length === 0) {
                        showToast('error', `Konfigurasi #${i + 1}: Minimal pilih satu kategori bahan/kertas.`);
                        return;
                    }

                    // Cek minimal satu item jasa potong
                    const cuttingItems = conf.querySelectorAll('.cutting-item-box');
                    if (cuttingItems.length === 0) {
                        showToast('error', `Konfigurasi #${i + 1}: Minimal harus ada 1 item jasa potong.`);
                        return;
                    }

                    const selectedCuttingIds = [];
                    for (let k = 0; k < cuttingItems.length; k++) {
                        const itemBox = cuttingItems[k];
                        const select = itemBox.querySelector('.cutting-select');
                        const selectedId = select ? select.value : '';

                        if (!selectedId) {
                            showToast('error', `Konfigurasi #${i + 1}: Jasa potong wajib dipilih.`);
                            if (select) $(select).select2('open');
                            return;
                        }

                        if (selectedCuttingIds.includes(selectedId)) {
                            const cuttingObj = cuttings.find(c => String(c.id) === String(selectedId));
                            const cuttingName = cuttingObj ? cuttingObj.name : 'Jasa potong';
                            showToast('error', `Konfigurasi #${i + 1}: "${cuttingName}" duplikat. Setiap jenis jasa potong hanya boleh dipilih satu kali dalam satu konfigurasi.`);
                            return;
                        }
                        selectedCuttingIds.push(selectedId);

                        const cuttingObj = cuttings.find(c => String(c.id) === String(selectedId));
                        const isPotong = cuttingObj && cuttingObj.name.toLowerCase().trim() === 'potong';

                        if (isPotong) {
                            const potongInputs = itemBox.querySelectorAll('.potong-price-input');
                            for (let p = 0; p < potongInputs.length; p++) {
                                if (!potongInputs[p].value || parseRupiahInput(potongInputs[p].value) <= 0) {
                                    showToast('error', `Konfigurasi #${i + 1} (Potong): Harap lengkapi semua tarif ongkos potong minimal (A3, A4, A5, ≤A6) untuk Harga Umum dan Harga Divisi.`);
                                    potongInputs[p].focus();
                                    return;
                                }
                            }
                        } else {
                            const umumInputs = itemBox.querySelectorAll('.input-umum:not([disabled])');
                            for (let j = 0; j < umumInputs.length; j++) {
                                if (!umumInputs[j].value || parseRupiahInput(umumInputs[j].value) <= 0) {
                                    showToast('error', `Konfigurasi #${i + 1}: Harap lengkapi semua rincian harga tier.`);
                                    umumInputs[j].focus();
                                    return;
                                }
                            }
                        }
                    }
                }

                // 3. Proses Pengiriman ke Server
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = 'Menyimpan...';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.status === 'success') {
                        showToast('success', data.message);
                        setTimeout(function() {
                            window.location.href = "{{ route('admin_production_cost_finishing_cuttings') }}";
                        }, 800);
                        return;
                    }

                    // Tampilkan pesan error dari backend
                    const errorMessage = data.errors ?
                        Object.values(data.errors).flat().join('<br>') :
                        (data.message || 'Terjadi kesalahan saat menyimpan data ongkos potong.');
                    showToast('error', errorMessage);

                } catch (error) {
                    console.error('Error saat menyimpan ongkos potong:', error);
                    showToast('error', 'Terjadi kesalahan koneksi saat mengirim formulir.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        });
    </script>
@endsection


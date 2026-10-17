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

        .cutting-tier-table tbody tr.row-division {
            background: var(--bg-hover);
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

        .cutting-edit-readonly {
            background-color: var(--bg-muted) !important;
            color: var(--t-base) !important;
            border-color: var(--border) !important;
            font-weight: 600;
            cursor: default;
        }
    </style>

    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">BIAYA · JASA POTONG</span>
                        <p class="hero-sub">
                            Perbarui ongkos produksi jasa potong (cutting) Digital Print A3+. Rincian biaya harga umum diatur per tier kuantiti, dan harga divisi dihitung otomatis 80% dari harga umum.
                        </p>
                    </div>
                </section>

                <div class="grid">
                    <section class="col-12 card">
                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">Edit Ongkos Produksi Jasa Potong</span>
                            </div>
                        </div>

                        <form id="cuttingProductionCostEditForm" method="POST"
                            action="{{ route('admin_update_pc_finishing_cutting', base64_encode($productionCost->id)) }}">
                            @csrf
                            @method('PUT')

                            {{-- READONLY SCOPE HEADER --}}
                            <div class="form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 24px;">
                                <div class="field">
                                    <label class="field-label">Mesin</label>
                                    <input type="text" class="input cutting-edit-readonly" value="{{ $productionCost->engine_name }}" readonly tabindex="-1">
                                </div>
                                <div class="field">
                                    <label class="field-label">Lokasi</label>
                                    <input type="text" class="input cutting-edit-readonly" value="{{ $productionCost->location_name }}" readonly tabindex="-1">
                                </div>
                                @if (!empty($productionCost->vendor_name))
                                    <div class="field">
                                        <label class="field-label">Vendor Outsourcing</label>
                                        <input type="text" class="input cutting-edit-readonly" value="{{ $productionCost->vendor_name }}" readonly tabindex="-1">
                                    </div>
                                @endif
                            </div>

                            {{-- CONTAINER KELOMPOK KONFIGURASI --}}
                            <div id="cuttingGroupContainer"></div>

                            {{-- BUTTON TAMBAH KELOMPOK --}}
                            <div style="margin-top: 16px;">
                                <button type="button" id="btnAddCuttingGroup" class="btn btn--ghost">
                                    + Tambah Kelompok
                                </button>
                            </div>

                            {{-- FORM ACTIONS --}}
                            <div class="form-actions" style="margin-top: 24px;">
                                <span class="badge dot warning">
                                    Mode Edit
                                </span>
                                <span class="spacer"></span>
                                <a href="{{ route('admin_production_cost_finishing_cuttings') }}" class="btn btn--ghost">
                                    Batal
                                </a>
                                <button type="submit" id="btnSubmitForm" class="btn btn--primary">
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
            const container = document.getElementById('cuttingGroupContainer');
            const btnAddGroup = document.getElementById('btnAddCuttingGroup');

            // Data Master & Data Tersimpan dari Controller
            const savedGroups = @json($groups);
            const categories = @json($categories);
            const cuttings = @json($cuttings);

            let groupIndex = 0;

            const tiers = [
                { key: 'tier_1', label: '1 lbr' },
                { key: 'tier_2_5', label: '2-5 lbr' },
                { key: 'tier_6_50', label: '6-50 lbr' },
                { key: 'tier_100', label: '100 lbr' },
                { key: 'tier_300', label: '300 lbr' },
                { key: 'tier_500', label: '500 lbr' }
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
            | BUAT BLOK ITEM JASA POTONG (CUTTING)
            |--------------------------------------------------------------------------
            */
            function createCuttingItem(confIndex, itemIndex, itemData = null) {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'cutting-item-box';
                itemDiv.dataset.itemIndex = itemIndex;

                let cuttingOptions = '<option value="">Pilih Jasa Potong</option>';
                cuttings.forEach(c => {
                    const isSelected = itemData && Number(itemData.cutting_id) === Number(c.id);
                    const statusLabel = c.status === 'Active' ? '' : ' (Tidak Aktif)';
                    cuttingOptions += `<option value="${c.id}" ${isSelected ? 'selected' : ''}>${c.name}${statusLabel}</option>`;
                });

                let tierHeaderHtml = '';
                tiers.forEach(t => {
                    tierHeaderHtml += `<th class="text-center" style="font-weight: 600;">${t.label}</th>`;
                });

                // Inputs for Tier - Umum & Divisi
                let umumInputs = '';
                let divisiInputs = '';
                tiers.forEach(t => {
                    const rawUmum = itemData && itemData.prices && itemData.prices.general ? (itemData.prices.general[t.key] || '') : '';
                    const rawDivisi = itemData && itemData.prices && itemData.prices.division ? (itemData.prices.division[t.key] || '') : '';

                    umumInputs += `
                        <td>
                            <input type="text"
                                class="tier-price-input input-umum"
                                data-tier="${t.key}"
                                name="configurations[${confIndex}][items][${itemIndex}][prices][general][${t.key}]"
                                value="${rawUmum}"
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
                                value="${rawDivisi}"
                                placeholder="Rp 0"
                                readonly
                                tabindex="-1">
                        </td>
                    `;
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

                    <div class="table-responsive cutting-tier-wrapper">
                        <table class="display data-table cutting-tier-table">
                            <thead>
                                <tr>
                                    <th style="width: 170px; font-weight: 600;">Tipe Harga</th>
                                    ${tierHeaderHtml}
                                </tr>
                            </thead>
                            <tbody>
                                {{-- HARGA UMUM --}}
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <span class="badge info">
                                            Harga Umum
                                        </span>
                                    </td>
                                    ${umumInputs}
                                </tr>
                                {{-- HARGA DIVISI 80% --}}
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
                `;

                return itemDiv;
            }

            /*
            |--------------------------------------------------------------------------
            | BUAT BLOK KELOMPOK KONFIGURASI
            |--------------------------------------------------------------------------
            */
            function createGroupBlock(groupData = null) {
                const currentIndex = groupIndex++;
                const card = document.createElement('div');
                card.className = 'configuration-card';
                card.dataset.index = currentIndex;

                // Bangun Checkbox Kategori
                let categoryCheckboxes = '';
                categories.forEach(c => {
                    const isChecked = groupData && groupData.category_ids && groupData.category_ids.includes(Number(c.id));
                    categoryCheckboxes += `
                        <label class="category-checkbox">
                            <input type="checkbox" name="configurations[${currentIndex}][category_ids][]" value="${c.id}" ${isChecked ? 'checked' : ''}>
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
                            <span class="badge primary group-title-badge">
                                Kelompok #${container.children.length + 1}
                            </span>
                            <h3>Lingkup Kategori Kertas & Jasa Potong</h3>
                        </div>
                        <div>
                            <button type="button" class="btn btn--ghost btn-remove-configuration" style="color: var(--danger); border: none;">
                                <i class="bi bi-trash3-fill"></i> Hapus Kelompok
                            </button>
                        </div>
                    </div>

                    {{-- KATEGORI --}}
                    <div class="field" style="margin-bottom: 24px;">
                        <label class="field-label">
                            Pilih Kategori Kertas / Bahan Yang Berlaku <span class="req">*</span>
                        </label>
                        <p class="field-help" style="margin-top: 2px;">
                            Pilih satu atau beberapa kategori kertas yang memiliki biaya jasa potong yang sama pada kelompok ini.
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

                const itemsContainer = card.querySelector('.cutting-items-container');
                const itemsToLoad = groupData && groupData.items && groupData.items.length > 0 ? groupData.items : [null];

                itemsToLoad.forEach((item, itemIdx) => {
                    const cuttingItemEl = createCuttingItem(currentIndex, itemIdx, item);
                    itemsContainer.appendChild(cuttingItemEl);
                    initSelect2(cuttingItemEl);
                });

                renumberGroups();
            }

            function renumberGroups() {
                const cards = container.querySelectorAll('.configuration-card');
                cards.forEach((card, idx) => {
                    const badge = card.querySelector('.group-title-badge');
                    if (badge) {
                        badge.textContent = `Kelompok #${idx + 1}`;
                    }
                });
            }

            // Inisialisasi kelompok dari savedGroups atau buat 1 kelompok baru jika kosong
            if (savedGroups && savedGroups.length > 0) {
                savedGroups.forEach(groupData => {
                    createGroupBlock(groupData);
                });
            } else {
                createGroupBlock(null);
            }

            // Tambah Kelompok Baru
            btnAddGroup.addEventListener('click', function() {
                createGroupBlock(null);
            });

            // Event Delegation: Hapus & Tambah Jasa Potong / Kelompok
            container.addEventListener('click', function(event) {
                const removeConfBtn = event.target.closest('.btn-remove-configuration');
                if (removeConfBtn) {
                    const cards = container.querySelectorAll('.configuration-card');
                    if (cards.length <= 1) {
                        showToast('error', 'Minimal harus ada 1 kelompok konfigurasi ongkos potong.');
                        return;
                    }
                    const card = removeConfBtn.closest('.configuration-card');
                    card.remove();
                    renumberGroups();
                    return;
                }

                const addCuttingBtn = event.target.closest('.btn-add-cutting');
                if (addCuttingBtn) {
                    const card = addCuttingBtn.closest('.configuration-card');
                    const confIndex = card.dataset.index;
                    const itemsContainer = card.querySelector('.cutting-items-container');
                    const currentItemsCount = itemsContainer.querySelectorAll('.cutting-item-box').length;
                    const newItem = createCuttingItem(confIndex, currentItemsCount, null);
                    itemsContainer.appendChild(newItem);
                    initSelect2(newItem);
                    return;
                }

                const removeCuttingBtn = event.target.closest('.btn-remove-cutting-item');
                if (removeCuttingBtn) {
                    const itemsContainer = removeCuttingBtn.closest('.cutting-items-container');
                    const items = itemsContainer.querySelectorAll('.cutting-item-box');
                    if (items.length <= 1) {
                        showToast('error', 'Minimal harus ada 1 jasa potong dalam setiap kelompok.');
                        return;
                    }
                    removeCuttingBtn.closest('.cutting-item-box').remove();
                    return;
                }
            });

            /*
            |--------------------------------------------------------------------------
            | KALKULASI REAL-TIME: HARGA UMUM -> HARGA DIVISI (80%) + FORMAT RUPIAH
            |--------------------------------------------------------------------------
            */
            container.addEventListener('input', function(event) {
                const inputUmum = event.target.closest('.input-umum');
                if (!inputUmum) return;

                const rawVal = parseRupiahInput(inputUmum.value);
                inputUmum.value = rawVal > 0 ? formatRupiahNumber(rawVal) : '';

                const tier = inputUmum.dataset.tier;
                const cuttingBox = inputUmum.closest('.cutting-item-box');

                if (cuttingBox) {
                    const inputDivisi = cuttingBox.querySelector(`.input-divisi[data-tier="${tier}"]`);
                    if (inputDivisi) {
                        if (rawVal > 0) {
                            const hargaDivisi = Math.round(rawVal * 0.8);
                            inputDivisi.value = formatRupiahNumber(hargaDivisi);
                        } else {
                            inputDivisi.value = '';
                        }
                    }
                }
            });

            /*
            |--------------------------------------------------------------------------
            | SUBMIT FORM DENGAN AJAX VALIDASI & TOAST
            |--------------------------------------------------------------------------
            */
            const form = document.getElementById('cuttingProductionCostEditForm');
            form.addEventListener('submit', async function(event) {
                event.preventDefault();

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const configurations = container.querySelectorAll('.configuration-card');
                if (configurations.length === 0) {
                    showToast('error', 'Minimal harus ada 1 kelompok konfigurasi ongkos potong.');
                    return;
                }

                for (let i = 0; i < configurations.length; i++) {
                    const conf = configurations[i];
                    const checkedCategories = conf.querySelectorAll('.category-checkbox input:checked');
                    if (checkedCategories.length === 0) {
                        showToast('error', `Kelompok #${i + 1}: Minimal pilih satu kategori bahan/kertas.`);
                        return;
                    }

                    const cuttingItems = conf.querySelectorAll('.cutting-item-box');
                    if (cuttingItems.length === 0) {
                        showToast('error', `Kelompok #${i + 1}: Minimal harus ada 1 item jasa potong.`);
                        return;
                    }

                    const emptyInputs = conf.querySelectorAll('.input-umum:not([disabled])');
                    for (let j = 0; j < emptyInputs.length; j++) {
                        if (!emptyInputs[j].value || parseRupiahInput(emptyInputs[j].value) < 0) {
                            showToast('error', `Kelompok #${i + 1}: Harap lengkapi semua rincian harga tier.`);
                            emptyInputs[j].focus();
                            return;
                        }
                    }
                }

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

                    const errorMessage = data.errors ?
                        Object.values(data.errors).flat().join('<br>') :
                        (data.message || 'Terjadi kesalahan saat memperbarui ongkos potong.');
                    showToast('error', errorMessage);

                } catch (error) {
                    console.error('Error saat memperbarui ongkos potong:', error);
                    showToast('error', 'Terjadi kesalahan koneksi saat mengirim formulir.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        });
    </script>
@endsection


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
                        <span class="eyebrow">BIAYA · ONGKOS POTONG (GUILLOTINE)</span>
                        <p class="hero-sub">
                            Perbarui ongkos produksi potong minimal Digital Print A3+ berdasarkan mesin, lokasi, dan kategori. Atur ongkos potong minimal per ukuran (A3, A4, A5, &le; A6) untuk Harga Umum dan Harga Divisi.
                        </p>
                    </div>
                </section>

                <div class="grid">
                    <section class="col-12 card">
                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">Edit Ongkos Produksi Potong</span>
                            </div>
                        </div>

                        <form id="potongProductionCostEditForm" method="POST"
                            action="{{ route('admin_update_pc_finishing_potong', base64_encode($productionCost->id)) }}">
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
                            <div id="potongGroupContainer"></div>

                            {{-- BUTTON TAMBAH KELOMPOK --}}
                            <div style="margin-top: 16px;">
                                <button type="button" id="btnAddPotongGroup" class="btn btn--ghost">
                                    + Tambah Kelompok Kategori
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
            const container = document.getElementById('potongGroupContainer');
            const btnAddGroup = document.getElementById('btnAddPotongGroup');

            // Data Master & Data Tersimpan dari Controller
            const savedGroups = @json($groups);
            const categories = @json($categories);

            let groupIndex = 0;

            const potongSizes = [
                { key: 'A3', label: 'A3' },
                { key: 'A4', label: 'A4' },
                { key: 'A5', label: 'A5' },
                { key: '<=A6', label: '≤ A6' }
            ];

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
            | BUAT BLOK KELOMPOK KATEGORI & TARIF POTONG
            |--------------------------------------------------------------------------
            */
            function createGroupBlock(data = null) {
                const currentIndex = groupIndex++;
                const card = document.createElement('div');
                card.className = 'configuration-card';
                card.dataset.index = currentIndex;

                const selectedCatIds = data && data.category_ids ?
                    data.category_ids.map(id => Number(id)) : [];

                // Checkbox Kategori Bahan
                let categoryCheckboxes = '';
                categories.forEach(c => {
                    const isChecked = selectedCatIds.includes(Number(c.id));
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

                // Input Rincian Harga per Ukuran (Umum & Divisi)
                let sizeHeaderHtml = '';
                let potongUmumInputs = '';
                let potongDivisiInputs = '';

                potongSizes.forEach(s => {
                    sizeHeaderHtml += `<th class="text-center" style="font-weight: 600; min-width: 130px;">${s.label}</th>`;

                    const generalVal = data && data.prices && data.prices.general && data.prices.general[s.key] ?
                        data.prices.general[s.key] : '';
                    const divisionVal = data && data.prices && data.prices.division && data.prices.division[s.key] ?
                        data.prices.division[s.key] : '';

                    potongUmumInputs += `
                        <td>
                            <input type="text"
                                class="tier-price-input potong-price-input input-umum"
                                data-size="${s.key}"
                                data-type="general"
                                name="configurations[${currentIndex}][prices][general][${s.key}]"
                                value="${generalVal}"
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
                                name="configurations[${currentIndex}][prices][division][${s.key}]"
                                value="${divisionVal}"
                                placeholder="Rp 0"
                                autocomplete="off"
                                required>
                        </td>
                    `;
                });

                card.innerHTML = `
                    <div class="configuration-header">
                        <div class="configuration-title">
                            <span class="badge primary">
                                Kelompok #${currentIndex + 1}
                            </span>
                            <h3>Kategori Kertas & Tarif Ongkos Potong Minimal</h3>
                        </div>
                        <div>
                            <button type="button" class="btn btn--ghost btn-remove-group" style="color: var(--danger); border: none;">
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
                            Pilih kategori kertas yang memiliki tarif ongkos potong minimal yang sama pada kelompok ini.
                        </p>
                        <div class="category-checkboxes">
                            ${categoryCheckboxes}
                        </div>
                    </div>

                    {{-- TABEL TARIF POTONG --}}
                    <div class="field" style="border-top: 1px dashed var(--border); padding-top: 20px;">
                        <label class="field-label" style="margin-bottom: 4px; font-weight: 700; font-size: 15px;">
                            Tarif Ongkos Potong Minimal
                        </label>
                        <p class="field-help" style="margin-bottom: 12px;">
                            Ongkos Potong Minimal berlaku per ukuran jadi cetakan (A3, A4, A5, &le; A6). Input nominal untuk <strong>Harga Umum</strong> dan <strong>Harga Divisi</strong>.
                        </p>

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
                                            <span class="badge info">Harga Umum</span>
                                        </td>
                                        ${potongUmumInputs}
                                    </tr>
                                    <tr class="row-division">
                                        <td style="vertical-align: middle;">
                                            <span class="badge success">Harga Divisi</span>
                                        </td>
                                        ${potongDivisiInputs}
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;

                container.appendChild(card);
            }

            // Inisialisasi data tersimpan
            if (savedGroups && savedGroups.length > 0) {
                savedGroups.forEach(group => {
                    createGroupBlock(group);
                });
            } else {
                createGroupBlock();
            }

            // Tambah Kelompok Baru
            btnAddGroup.addEventListener('click', function() {
                createGroupBlock();
            });

            // Event Delegation: Hapus Kelompok
            container.addEventListener('click', function(event) {
                const removeBtn = event.target.closest('.btn-remove-group');
                if (removeBtn) {
                    const cards = container.querySelectorAll('.configuration-card');
                    if (cards.length <= 1) {
                        showToast('error', 'Minimal harus ada 1 kelompok konfigurasi ongkos potong.');
                        return;
                    }
                    removeBtn.closest('.configuration-card').remove();
                }
            });

            /*
            |--------------------------------------------------------------------------
            | KALKULASI REAL-TIME & FORMAT RUPIAH
            |--------------------------------------------------------------------------
            */
            container.addEventListener('input', function(event) {
                const priceInput = event.target.closest('.potong-price-input');
                if (!priceInput) return;

                const rawVal = parseRupiahInput(priceInput.value);
                priceInput.value = rawVal > 0 ? formatRupiahNumber(rawVal) : '';
            });

            /*
            |--------------------------------------------------------------------------
            | SUBMIT FORM DENGAN VALIDASI & TOAST
            |--------------------------------------------------------------------------
            */
            const form = document.getElementById('potongProductionCostEditForm');
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

                const selectedCategories = [];

                for (let i = 0; i < configurations.length; i++) {
                    const conf = configurations[i];
                    const checkedCats = conf.querySelectorAll('.category-checkbox input:checked');

                    if (checkedCats.length === 0) {
                        showToast('error', `Kelompok #${i + 1}: Minimal pilih satu kategori bahan/kertas.`);
                        return;
                    }

                    for (let c = 0; c < checkedCats.length; c++) {
                        const catId = checkedCats[c].value;
                        if (selectedCategories.includes(catId)) {
                            const catLabel = checkedCats[c].closest('.category-checkbox').querySelector('.category-checkbox-label')?.textContent || 'Kategori';
                            showToast('error', `Kategori "${catLabel}" duplikat. Setiap kategori kertas hanya boleh dipilih satu kali.`);
                            return;
                        }
                        selectedCategories.push(catId);
                    }

                    // Cek kelengkapan input tarif
                    const priceInputs = conf.querySelectorAll('.potong-price-input');
                    for (let p = 0; p < priceInputs.length; p++) {
                        if (!priceInputs[p].value || parseRupiahInput(priceInputs[p].value) <= 0) {
                            showToast('error', `Kelompok #${i + 1}: Harap lengkapi semua tarif ongkos potong minimal (A3, A4, A5, ≤A6) untuk Harga Umum dan Harga Divisi.`);
                            priceInputs[p].focus();
                            return;
                        }
                    }
                }

                // Kirim perubahan ke backend
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
                        (data.message || 'Terjadi kesalahan saat menyimpan data ongkos potong.');
                    showToast('error', errorMessage);

                } catch (error) {
                    console.error('Error saat menyimpan perubahan ongkos potong:', error);
                    showToast('error', 'Terjadi kesalahan koneksi saat mengirim formulir.');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        });
    </script>
@endsection


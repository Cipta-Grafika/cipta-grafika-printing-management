@extends('admin_master')

@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">BIAYA · LAMINASI</span>
                        <p class="hero-sub">
                            Perbarui ongkos produksi laminasi Digital Print A3+. Harga per sisi dan tier
                            dihitung ulang menggunakan pricing rules aktif. Kelompok dengan biaya atau kategori
                            berbeda tetap dipisahkan.
                        </p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">Edit Ongkos Produksi Laminasi Digital Print A3+</span>
                            </div>
                        </div>
                        <form method="POST" id="digitalLaminationEditForm"
                            action="{{ route('admin_update_pc_finishing_lamination_digital', base64_encode($productionCost->id)) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-grid">
                                <div class="field">
                                    <label class="field-label">Lokasi</label>
                                    <input class="input digital-edit-readonly" value="{{ $productionCost->location_name }}"
                                        readonly tabindex="-1">
                                </div>
                                <div class="field">
                                    <label class="field-label">Engine</label>
                                    <input class="input digital-edit-readonly" value="{{ $productionCost->engine_name }}"
                                        readonly tabindex="-1">
                                </div>
                                @if ($productionCost->vendor_name)
                                    <div class="field">
                                        <label class="field-label">Vendor</label>
                                        <input class="input digital-edit-readonly"
                                            value="{{ $productionCost->vendor_name }}" readonly tabindex="-1">
                                    </div>
                                @endif
                            </div>
                            <p class="field-help">
                                Pilihan kategori berlaku untuk seluruh item dalam kelompoknya.
                                Format harga: 316,70 atau 1.500,00. Ukuran mengikuti master laminasi.
                            </p>
                            <div id="digitalLaminationGroups"></div>
                            <button type="button" class="btn btn--ghost" id="btnAddDigitalLaminationGroup"
                                style="margin-top: 20px;">
                                + Tambah Kelompok
                            </button>
                            <div class="form-actions" style="margin-top: 24px;">
                                <span class="spacer"></span>
                                <a href="{{ route('admin_pc_finishing_laminations') }}" class="btn btn--ghost">Batal</a>
                                <button type="submit" class="btn btn--primary">Simpan Perubahan</button>
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
            const form = document.getElementById('digitalLaminationEditForm');
            const container = document.getElementById('digitalLaminationGroups');
            const savedGroups = @json($groups);
            const categories = @json($categories);
            const laminations = @json($laminations);
            let groupIndex = 0;
            let rowIndex = 0;

            @include('admin.production-cost-finishing-lamination._digital-rupiah-input')

            function addRow(group, detail) {
                const index = rowIndex++;
                const row = document.createElement('tr');
                const prefix = `configurations[${group.dataset.index}][items][${index}]`;
                row.innerHTML = `
                    <td style="min-width: 260px;">
                        <select name="${prefix}[lamination_id]" class="select lamination-select" required>
                            <option value="">Pilih Laminasi</option>
                        </select>
                    </td>
                    <td style="min-width: 160px;">
                        <input type="text" name="${prefix}[unit_price]" class="input rupiah-input" required
                            inputmode="decimal" maxlength="20" autocomplete="off" placeholder="Contoh: 316,70"
                            pattern="(?:[0-9]+|[0-9]{1,3}(?:[.][0-9]{3})+)(?:,[0-9]{1,2})?">
                    </td>
                    <td style="min-width: 160px;">
                        <input type="text" name="${prefix}[click_price]" class="input rupiah-input" required
                            inputmode="decimal" maxlength="20" autocomplete="off" placeholder="Contoh: 1.500,00"
                            pattern="(?:[0-9]+|[0-9]{1,3}(?:[.][0-9]{3})+)(?:,[0-9]{1,2})?">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn--icon remove-row" aria-label="Hapus Laminasi">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </td>`;
                const select = row.querySelector('.lamination-select');
                laminations.forEach(lamination => {
                    select.appendChild(new Option(
                        lamination.name + (lamination.status === 'Active' ? '' : ' (Tidak Aktif)'),
                        lamination.id
                    ));
                });
                if (detail) {
                    select.value = detail.lamination_id;
                    row.querySelector('[name$="[unit_price]"]').value = detail.unit_price;
                    row.querySelector('[name$="[click_price]"]').value = detail.click_price;
                }
                group.querySelector('tbody').appendChild(row);
                $(select).select2({
                    width: '100%'
                });
            }

            function addGroup(saved) {
                const group = document.createElement('div');
                group.className = 'digital-lamination-group field';
                group.dataset.index = groupIndex++;
                group.style.marginTop = '24px';
                group.innerHTML = `
                    <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
                        <label class="field-label group-title" style="margin-bottom:0;"></label>
                        <button type="button" class="btn btn--ghost add-row">+ Tambah Laminasi</button>
                        <button type="button" class="btn btn--ghost remove-group">Hapus Kelompok</button>
                    </div>
                    <div class="field">
                        <label class="field-label" for="digital_categories_${group.dataset.index}">Kategori <span class="req">*</span></label>
                        <select id="digital_categories_${group.dataset.index}"
                            name="configurations[${group.dataset.index}][category_ids][]"
                            class="select category-select" multiple required></select>
                    </div>
                    <div class="table-responsive">
                        <table class="display data-table">
                            <thead><tr><th>Laminasi</th><th>Harga Satuan (Rp)</th><th>Harga Klik (Rp)</th><th class="text-center">Aksi</th></tr></thead>
                            <tbody></tbody>
                        </table>
                    </div>`;
                const categorySelect = group.querySelector('.category-select');
                categories.forEach(category => categorySelect.appendChild(new Option(
                    category.name, category.id, false,
                    Boolean(saved && saved.category_ids.includes(Number(category.id)))
                )));
                container.appendChild(group);
                $(categorySelect).select2({
                    width: '100%',
                    placeholder: 'Pilih Kategori'
                });
                (saved && saved.items.length ? saved.items : [null]).forEach(detail => addRow(group, detail));
                renumber();
            }

            function renumber() {
                container.querySelectorAll('.group-title').forEach((label, index) => {
                    label.textContent = `Kelompok ${index + 1}`;
                });
            }

            (savedGroups.length ? savedGroups : [null]).forEach(addGroup);
            document.getElementById('btnAddDigitalLaminationGroup').addEventListener('click', () => addGroup(null));
            container.addEventListener('click', function(event) {
                const button = event.target.closest('button');
                if (!button) return;
                const group = button.closest('.digital-lamination-group');
                if (button.classList.contains('add-row')) addRow(group, null);
                if (button.classList.contains('remove-row')) {
                    if (group.querySelectorAll('tbody tr').length <= 1) {
                        showToast('error', 'Minimal satu laminasi harus tersedia dalam setiap kelompok.');
                        return;
                    }
                    const row = button.closest('tr');
                    $(row.querySelector('.lamination-select')).select2('destroy');
                    row.remove();
                }
                if (button.classList.contains('remove-group')) {
                    if (container.children.length <= 1) {
                        showToast('error', 'Minimal satu kelompok harus tersedia.');
                        return;
                    }
                    $(group).find('.category-select, .lamination-select').select2('destroy');
                    group.remove();
                    renumber();
                }
            });

            container.addEventListener('input', function(event) {
                const input = event.target;
                if (!input.classList.contains('rupiah-input')) return;
                const regroup = event.inputType &&
                    (event.inputType.startsWith('delete') ||
                        (event.inputType === 'insertText' && /^[0-9,]+$/.test(event.data || '')));
                input.value = formatDigitalRupiahInput(input, false, regroup);
                input.setSelectionRange(input.value.length, input.value.length);
            });
            container.addEventListener('blur', function(event) {
                if (event.target.classList.contains('rupiah-input')) {
                    event.target.value = formatDigitalRupiahInput(event.target, true);
                }
            }, true);

            form.addEventListener('submit', async function(event) {
                event.preventDefault();
                if (!form.reportValidity()) return;
                const button = form.querySelector('[type="submit"]');
                if (button.disabled) return;
                button.disabled = true;
                button.textContent = 'Menyimpan...';
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                    });
                    const data = await response.json();
                    if (!response.ok || data.status !== 'success') {
                        showToast('error', data.errors ? Object.values(data.errors).flat().join(' ') :
                            (data.message || 'Gagal memperbarui ongkos produksi laminasi.'));
                        return;
                    }
                    showToast('success', data.message);
                    setTimeout(() => {
                        window.location.href = @json(route('admin_pc_finishing_laminations'));
                    }, 800);
                } catch (error) {
                    console.error('Update Digital Lamination Error:', error);
                    showToast('error', 'Terjadi kesalahan saat memperbarui ongkos produksi laminasi.');
                } finally {
                    button.disabled = false;
                    button.textContent = 'Simpan Perubahan';
                }
            });
        });
    </script>
@endsection

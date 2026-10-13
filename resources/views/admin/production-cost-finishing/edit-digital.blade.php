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
                            Perbarui ongkos produksi Digital Print A3+. Harga jual per tier akan dihitung ulang
                            berdasarkan pricing rule yang aktif saat ini.
                        </p>

                    </div>

                </section>

                <div class="grid">

                    <section class="col-12 card">

                        <div class="card-head">

                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Edit Ongkos Produksi Digital Print A3+
                                </span>

                            </div>

                        </div>

                        <form method="POST"
                            action="{{ route('admin_update_pc_finishing_digital', base64_encode($productionCost->id)) }}"
                            id="digitalProductionCostForm">

                            @csrf
                            @method('PUT')

                            <div class="form-grid">

                                <div class="field">
                                    <label class="field-label">Lokasi</label>
                                    <input type="text" class="input digital-edit-readonly"
                                        value="{{ $productionCost->location_name }}" readonly tabindex="-1">
                                </div>

                                <div class="field">
                                    <label class="field-label">Engine</label>
                                    <input type="text" class="input digital-edit-readonly"
                                        value="{{ $productionCost->engine_name }}" readonly tabindex="-1">
                                </div>

                                @if ($productionCost->vendor_name)
                                    <div class="field">
                                        <label class="field-label">Vendor</label>
                                        <input type="text" class="input digital-edit-readonly"
                                            value="{{ $productionCost->vendor_name }}" readonly tabindex="-1">
                                    </div>
                                @endif

                            </div>

                            <div class="field" style="margin-top: 24px; overflow: auto;">

                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 12px;">

                                    <label class="field-label" style="margin-bottom: 0;">
                                        Detail Ongkos Produksi
                                    </label>

                                    <button type="button" class="btn btn--ghost" id="btnAddDigitalMaterial">
                                        + Tambah Material
                                    </button>

                                </div>

                                <div class="table-responsive">

                                    <table class="display data-table">

                                        <thead>
                                            <tr>
                                                <th>Material</th>
                                                <th>Harga Satuan (Rp)</th>
                                                <th>Harga Klik (Rp)</th>
                                                <th style="width: 80px;">Aksi</th>
                                            </tr>
                                        </thead>

                                        <tbody id="digitalMaterialBody"></tbody>

                                    </table>

                                </div>

                            </div>

                            <div class="form-actions" style="margin-top: 24px;">

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

            const form = document.getElementById('digitalProductionCostForm');
            const tbody = document.getElementById('digitalMaterialBody');
            const btnAddMaterial = document.getElementById('btnAddDigitalMaterial');

            const materials = @json($materials);
            const savedDetails = @json($details);

            let rowIndex = 0;

            function formatRupiah(value) {
                const digits = String(value || '').replace(/\D/g, '');

                return digits === '' ? '' : Number(digits).toLocaleString('id-ID');
            }

            function addMaterialRow(detail) {
                const currentIndex = rowIndex++;
                const row = document.createElement('tr');

                row.innerHTML = `
                    <td style="min-width: 260px;">
                        <select name="items[${currentIndex}][material_id]" class="select select2 material-select" required>
                            <option value="">Pilih Material</option>
                        </select>
                    </td>
                    <td style="min-width: 160px;">
                        <input type="text" name="items[${currentIndex}][unit_price]" class="input rupiah-input"
                            inputmode="numeric" autocomplete="off" placeholder="Contoh: 5.000" required>
                    </td>
                    <td style="min-width: 160px;">
                        <input type="text" name="items[${currentIndex}][click_price]" class="input rupiah-input"
                            inputmode="numeric" autocomplete="off" placeholder="Contoh: 1.000" required>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn--icon btn-remove-material" aria-label="Hapus Material">
                            <i class="bi bi-trash3-fill"></i>
                        </button>
                    </td>
                `;

                const select = row.querySelector('.material-select');

                materials.forEach(function(material) {
                    select.appendChild(new Option(
                        `${material.material_name} (${material.category_name})`,
                        material.id
                    ));
                });

                if (detail) {
                    select.value = detail.material_id;
                    row.querySelector('[name$="[unit_price]"]').value = detail.unit_price;
                    row.querySelector('[name$="[click_price]"]').value = detail.click_price;
                }

                tbody.appendChild(row);

                $(select).select2({
                    width: '100%'
                });
            }

            function hasDuplicateMaterial() {
                const selectedIds = Array.from(tbody.querySelectorAll('.material-select'))
                    .map(function(select) {
                        return select.value;
                    })
                    .filter(Boolean);

                return new Set(selectedIds).size !== selectedIds.length;
            }

            btnAddMaterial.addEventListener('click', function() {
                addMaterialRow(null);
            });

            tbody.addEventListener('input', function(event) {
                if (!event.target.classList.contains('rupiah-input')) {
                    return;
                }

                event.target.value = formatRupiah(event.target.value);
            });

            tbody.addEventListener('click', function(event) {
                const deleteButton = event.target.closest('.btn-remove-material');

                if (!deleteButton) {
                    return;
                }

                if (tbody.querySelectorAll('tr').length <= 1) {
                    showToast('warning', 'Minimal harus ada 1 material.');
                    return;
                }

                const row = deleteButton.closest('tr');
                const select = row.querySelector('.material-select');

                if ($(select).hasClass('select2-hidden-accessible')) {
                    $(select).select2('destroy');
                }

                row.remove();
            });

            form.addEventListener('submit', async function(event) {
                event.preventDefault();

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                if (tbody.querySelectorAll('tr').length === 0) {
                    showToast('warning', 'Minimal harus ada 1 material.');
                    return;
                }

                if (hasDuplicateMaterial()) {
                    showToast('warning', 'Material tidak boleh dipilih lebih dari satu kali.');
                    return;
                }

                const submitButton = form.querySelector('button[type="submit"]');
                const originalText = submitButton.innerHTML;

                submitButton.disabled = true;
                submitButton.innerHTML = 'Menyimpan...';

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

                    if (response.ok && data.success) {
                        showToast('success', data.message);

                        setTimeout(function() {
                            window.location.href = "{{ route('admin_pc_finishings') }}";
                        }, 800);

                        return;
                    }

                    showToast(
                        data.type || 'error',
                        data.message || 'Terjadi kesalahan saat memperbarui Production Cost.'
                    );
                } catch (error) {
                    console.error('Gagal memperbarui Production Cost:', error);

                    showToast('error', 'Terjadi kesalahan saat memperbarui Production Cost.');
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            });

            if (savedDetails.length > 0) {
                savedDetails.forEach(addMaterialRow);
            } else {
                addMaterialRow(null);
            }

        });
    </script>
@endsection

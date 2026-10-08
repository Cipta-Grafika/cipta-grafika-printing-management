@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">MASTER DATA · UKURAN MATERIAL</span>
                        {{-- <h1 class="hero-title">Tambah Ukuran Material</h1> --}}
                        <p class="hero-sub">Tambahkan dan konfigurasi ukuran material yang tersedia sebagai acuan dalam
                            proses perhitungan dan estimasi harga.</p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">Konfigurasi Ukuran Material</span>
                                {{-- <h2 class="card-title">Konfigurasi Ukuran Material</h2> --}}
                            </div>
                        </div>
                        <form action="{{ route('admin_store_material_size') }}" method="POST">
                            @csrf

                            <div id="materialSizeRows">

                                {{-- MATERIAL SIZE PERTAMA --}}
                                <div class="material-size-row">

                                    <div class="form-grid material-size-form-grid" style="margin-bottom: 15px;">

                                        {{-- MATERIAL --}}
                                        <div class="field">
                                            <label class="field-label">
                                                Material
                                                <span class="req">*</span>
                                            </label>

                                            <select name="material_sizes[0][material_id]" class="select select2" required>
                                                <option value="">Pilih Material</option>

                                                @foreach ($materials as $material)
                                                    <option value="{{ $material->id }}">
                                                        {{ $material->material_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- LEBAR --}}
                                        <div class="field">
                                            <label class="field-label">
                                                Lebar
                                                <span class="req">*</span>
                                            </label>

                                            <input type="number" name="material_sizes[0][width]" class="input"
                                                step="0.01" min="0" placeholder="Contoh: 320" required>
                                        </div>

                                        {{-- SATUAN --}}
                                        <div class="field">
                                            <label class="field-label">
                                                Satuan
                                                <span class="req">*</span>
                                            </label>

                                            <select name="material_sizes[0][unit]" class="select select2" required>
                                                <option value="">Pilih Satuan</option>
                                                <option value="cm">Centimeter (cm)</option>
                                                <option value="mm">Millimeter (mm)</option>
                                                <option value="m">Meter (m)</option>
                                            </select>
                                        </div>

                                        {{-- AKSI --}}
                                        <div class="field material-size-action">
                                            <label class="field-label">
                                                Aksi
                                            </label>

                                            <button type="button" class="btn btn--danger remove-material-size">
                                                Hapus
                                            </button>
                                        </div>

                                    </div>

                                </div>

                            </div>


                            {{-- BUTTON TAMBAH --}}
                            <div class="form-actions mt-3" style="margin-bottom: 15px;">
                                <button type="button" id="addMaterialSize" class="btn btn--ghost">
                                    + Tambah Baris
                                </button>
                            </div>


                            {{-- BUTTON FORM --}}
                            <div class="form-actions">
                                <span class="badge dot success">
                                    Siap disimpan
                                </span>

                                <span class="spacer"></span>

                                <a href="{{ route('admin_material_sizes') }}" class="btn btn--ghost">
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

            let materialSizeIndex = 1;

            const materialSizeRows =
                document.getElementById('materialSizeRows');

            const addMaterialSizeButton =
                document.getElementById('addMaterialSize');


            /* ============================================================
                TAMBAH MATERIAL SIZE
            ============================================================ */

            addMaterialSizeButton.addEventListener('click', function() {

                const row = document.createElement('div');

                row.classList.add('material-size-row');

                row.innerHTML = `
                <div class="form-grid material-size-form-grid" style="margin-bottom: 15px;">

                    {{-- MATERIAL --}}
                    <div class="field">
                        <label class="field-label">
                            Material
                            <span class="req">*</span>
                        </label>

                        <select
                            name="material_sizes[${materialSizeIndex}][material_id]"
                            class="select select2"
                            required
                        >
                            <option value="">Pilih Material</option>

                            @foreach ($materials as $material)
                                <option value="{{ $material->id }}">
                                    {{ $material->material_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- LEBAR --}}
                    <div class="field">
                        <label class="field-label">
                            Lebar
                            <span class="req">*</span>
                        </label>

                        <input
                            type="number"
                            name="material_sizes[${materialSizeIndex}][width]"
                            class="input"
                            step="0.01"
                            min="0"
                            placeholder="Contoh: 320"
                            required
                        >
                    </div>


                    {{-- SATUAN --}}
                    <div class="field">
                        <label class="field-label">
                            Satuan
                            <span class="req">*</span>
                        </label>

                        <select
                            name="material_sizes[${materialSizeIndex}][unit]"
                            class="select select2"
                            required
                        >
                            <option value="">Pilih Satuan</option>
                            <option value="cm">Centimeter (cm)</option>
                            <option value="mm">Millimeter (mm)</option>
                            <option value="m">Meter (m)</option>
                        </select>
                    </div>

                    {{-- AKSI --}}
                    <div class="field material-size-action">
                        <label class="field-label">
                            Aksi
                        </label>

                        <button
                            type="button"
                            class="btn btn--danger remove-material-size"
                        >
                            Hapus
                        </button>
                    </div>

                </div>
            `;


                /*
                 * Tambahkan row ke container
                 */
                materialSizeRows.appendChild(row);


                /*
                 * INIT SELECT2 UNTUK ROW BARU
                 *
                 * Sama persis dengan metode Engine
                 */
                $(row).find('.select2').select2({
                    width: '100%'
                });


                materialSizeIndex++;
            });


            /* ============================================================
                HAPUS MATERIAL SIZE
            ============================================================ */

            materialSizeRows.addEventListener('click', function(event) {

                const removeButton =
                    event.target.closest('.remove-material-size');

                if (!removeButton) {
                    return;
                }


                const rows =
                    materialSizeRows.querySelectorAll('.material-size-row');


                /*
                 * Jangan boleh menghapus semua row
                 */
                if (rows.length <= 1) {
                    return;
                }


                /*
                 * Hapus row
                 */
                removeButton
                    .closest('.material-size-row')
                    .remove();

            });


            /* ============================================================
                SELECT2 ROW PERTAMA
            ============================================================ */

            $(document).ready(function() {

                $('.select2').select2({
                    width: '100%'
                });

            });

        });
    </script>
@endsection

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
                            MASTER DATA · UKURAN MATERIAL
                        </span>

                        {{-- <h1 class="hero-title">
                            Edit Ukuran Material
                        </h1> --}}

                        <p class="hero-sub">
                            Perbarui konfigurasi ukuran material yang tersedia sebagai acuan dalam
                            proses perhitungan dan estimasi harga.
                        </p>

                    </div>

                </section>


                <div class="grid">

                    <section class="col-12 card">

                        <div class="card-head">

                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    Konfigurasi Ukuran Material
                                </span>

                                {{-- <h2 class="card-title">
                                    Konfigurasi Ukuran Material
                                </h2> --}}

                            </div>

                        </div>


                        <form action="{{ route('admin_update_material_size', base64_encode($material->id)) }}"
                            method="POST">

                            @csrf
                            @method('PUT')


                            <div id="materialSizeRows">

                                @foreach ($sizes as $index => $size)
                                    <div class="material-size-row">

                                        <div class="form-grid material-size-form-grid" style="margin-bottom: 15px;">

                                            {{-- MATERIAL --}}
                                            <div class="field">

                                                <label class="field-label">

                                                    Material

                                                    <span class="req">*</span>

                                                </label>

                                                <select name="material_sizes[{{ $index }}][material_id]"
                                                    class="select select2" required disabled>

                                                    <option value="">
                                                        Pilih Material
                                                    </option>

                                                    @foreach ($materials as $materialOption)
                                                        <option value="{{ $materialOption->id }}"
                                                            {{ $materialOption->id == $material->id ? 'selected' : '' }}>
                                                            {{ $materialOption->material_name }}
                                                        </option>
                                                    @endforeach

                                                </select>

                                                {{-- ID MATERIAL YANG DIKIRIM --}}
                                                <input type="hidden"
                                                    name="material_sizes[{{ $index }}][material_id]"
                                                    value="{{ $material->id }}">

                                            </div>


                                            {{-- LEBAR --}}
                                            <div class="field">

                                                <label class="field-label">

                                                    Lebar

                                                    <span class="req">*</span>

                                                </label>

                                                <input type="number" name="material_sizes[{{ $index }}][width]"
                                                    class="input" step="0.01" min="0" placeholder="Contoh: 320"
                                                    value="{{ $size->width }}" required>

                                            </div>


                                            {{-- SATUAN --}}
                                            <div class="field">

                                                <label class="field-label">

                                                    Satuan

                                                    <span class="req">*</span>

                                                </label>

                                                <select name="material_sizes[{{ $index }}][unit]"
                                                    class="select select2" required>

                                                    <option value="">
                                                        Pilih Satuan
                                                    </option>

                                                    <option value="cm"
                                                        {{ strtolower($size->unit) === 'cm' ? 'selected' : '' }}>
                                                        Centimeter (cm)
                                                    </option>

                                                    <option value="mm"
                                                        {{ strtolower($size->unit) === 'mm' ? 'selected' : '' }}>
                                                        Millimeter (mm)
                                                    </option>

                                                    <option value="m"
                                                        {{ strtolower($size->unit) === 'm' ? 'selected' : '' }}>
                                                        Meter (m)
                                                    </option>

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
                                @endforeach


                                {{-- JIKA BELUM ADA SIZE --}}
                                @if ($sizes->count() === 0)
                                    <div class="material-size-row">

                                        <div class="form-grid material-size-form-grid" style="margin-bottom: 15px;">

                                            {{-- MATERIAL --}}
                                            <div class="field">

                                                <label class="field-label">

                                                    Material

                                                    <span class="req">*</span>

                                                </label>

                                                <select name="material_sizes[0][material_id]" class="select select2"
                                                    required disabled>

                                                    <option value="">
                                                        Pilih Material
                                                    </option>

                                                    @foreach ($materials as $materialOption)
                                                        <option value="{{ $materialOption->id }}"
                                                            {{ $materialOption->id == $material->id ? 'selected' : '' }}>
                                                            {{ $materialOption->material_name }}
                                                        </option>
                                                    @endforeach

                                                </select>

                                                <input type="hidden" name="material_sizes[0][material_id]"
                                                    value="{{ $material->id }}">

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

                                                    <option value="">
                                                        Pilih Satuan
                                                    </option>

                                                    <option value="cm">
                                                        Centimeter (cm)
                                                    </option>

                                                    <option value="mm">
                                                        Millimeter (mm)
                                                    </option>

                                                    <option value="m">
                                                        Meter (m)
                                                    </option>

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
                                @endif

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

            let materialSizeIndex = {{ $sizes->count() }};

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

                    <div
                        class="form-grid material-size-form-grid"
                        style="margin-bottom: 15px;"
                    >

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
                                disabled
                            >

                                <option value="">
                                    Pilih Material
                                </option>

                                @foreach ($materials as $materialOption)

                                    <option
                                        value="{{ $materialOption->id }}"
                                        {{ $materialOption->id == $material->id ? 'selected' : '' }}
                                    >
                                        {{ $materialOption->material_name }}
                                    </option>

                                @endforeach

                            </select>

                            <input
                                type="hidden"
                                name="material_sizes[${materialSizeIndex}][material_id]"
                                value="{{ $material->id }}"
                            >

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

                                <option value="">
                                    Pilih Satuan
                                </option>

                                <option value="cm">
                                    Centimeter (cm)
                                </option>

                                <option value="mm">
                                    Millimeter (mm)
                                </option>

                                <option value="m">
                                    Meter (m)
                                </option>

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
                SELECT2 ROW PERTAMA / DATA EXISTING
            ============================================================ */

            $(document).ready(function() {

                $('.select2').select2({
                    width: '100%'
                });

            });

        });
    </script>
@endsection

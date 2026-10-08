@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">MASTER DATA · KOMPATIBILITAS</span>
                        {{-- <h1 class="hero-title">Tambah Kompatibilitas Mesin & Material</h1> --}}
                        <p class="hero-sub">Tambahkan dan atur hubungan kompatibilitas antara mesin dan material yang dapat
                            digunakan dalam proses produksi.</p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">Konfigurasi Kompatibilitas Mesin & Material</span>
                                {{-- <h2 class="card-title">Konfigurasi Kompatibilitas Mesin & Material</h2> --}}
                            </div>
                        </div>
                        <form action="{{ route('admin_store_compatibility_material') }}" method="POST">

                            @csrf

                            {{-- =====================================================
                                MESIN
                            ===================================================== --}}

                            <div class="form-grid">

                                <div class="field">

                                    <label class="field-label" for="engine_id">

                                        Mesin

                                        <span class="req">*</span>

                                    </label>

                                    <select name="engine_id" id="engine_id" class="select select2" required>

                                        <option value="">
                                            Pilih Mesin
                                        </option>

                                        @foreach ($engines as $engine)
                                            <option value="{{ $engine->id }}">

                                                {{ $engine->name }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                            </div>


                            {{-- =====================================================
                                MATERIAL
                            ===================================================== --}}

                            <div class="form-section" style="margin-top: 24px;">

                                <div class="section-heading">

                                    <div>

                                        <span class="eyebrow">
                                            MATERIAL
                                        </span>

                                        <h3>
                                            Material yang Kompatibel
                                        </h3>

                                    </div>

                                </div>


                                {{-- CONTAINER ROW MATERIAL --}}

                                <div id="materialRows">


                                    {{-- ROW PERTAMA --}}

                                    <div class="material-row">

                                        <div class="form-grid material-form-grid" style="margin-bottom: 15px;">


                                            {{-- MATERIAL --}}

                                            <div class="field">

                                                <label class="field-label">

                                                    Material

                                                    <span class="req">*</span>

                                                </label>

                                                <select name="materials[0][material_id]" class="select select2" required>

                                                    <option value="">
                                                        Pilih Material
                                                    </option>

                                                    @foreach ($materials as $material)
                                                        <option value="{{ $material->id }}">

                                                            {{ $material->material_name }} - {{ $material->category_name }}

                                                        </option>
                                                    @endforeach

                                                </select>

                                            </div>


                                            {{-- STATUS --}}

                                            <div class="field">

                                                <label class="field-label">

                                                    Status

                                                    <span class="req">*</span>

                                                </label>

                                                <select name="materials[0][status]" class="select select2" required>

                                                    <option value="Active" selected>
                                                        Active
                                                    </option>

                                                    <option value="Inactive">
                                                        Inactive
                                                    </option>

                                                </select>

                                            </div>


                                            {{-- ACTION ROW --}}

                                            <div class="field material-action">

                                                <label class="field-label">

                                                    &nbsp;

                                                </label>

                                                <button type="button" class="btn btn--danger btn-remove-material" disabled>

                                                    Hapus

                                                </button>

                                            </div>


                                        </div>

                                    </div>

                                </div>


                                {{-- =====================================================
                                    BUTTON TAMBAH MATERIAL
                                ===================================================== --}}

                                <div class="form-actions mt-3" style="margin-bottom: 15px;">

                                    <button type="button" id="addMaterial" class="btn btn--ghost">

                                        + Tambah Material

                                    </button>

                                </div>

                            </div>


                            {{-- =====================================================
                                FORM ACTION
                            ===================================================== --}}

                            <div class="form-actions">

                                <span class="badge dot success">
                                    Siap disimpan
                                </span>

                                <span class="spacer"></span>

                                <a href="{{ route('admin_compatibility_materials') }}" class="btn btn--ghost">

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

            const materialRows = document.getElementById('materialRows');

            const addMaterial = document.getElementById('addMaterial');

            let materialIndex = 1;


            /*
            =====================================================
            TAMBAH MATERIAL
            =====================================================
            */

            addMaterial.addEventListener('click', function() {

                const row = document.createElement('div');

                row.classList.add('material-row');


                row.innerHTML = `

            <div
                class="form-grid material-form-grid"
                style="margin-bottom: 15px;"
            >


                <!-- MATERIAL -->

                <div class="field">

                    <label class="field-label">

                        Material

                        <span class="req">*</span>

                    </label>


                    <select
                        name="materials[${materialIndex}][material_id]"
                        class="select select2"
                        required
                    >

                        <option value="">
                            Pilih Material
                        </option>

                        @foreach ($materials as $material)

                            <option value="{{ $material->id }}">

                                {{ $material->material_name }} -{{ $material->category_name }}

                            </option>

                        @endforeach

                    </select>

                </div>


                <!-- STATUS -->

                <div class="field">

                    <label class="field-label">

                        Status

                        <span class="req">*</span>

                    </label>


                    <select
                        name="materials[${materialIndex}][status]"
                        class="select select2"
                        required
                    >

                        <option value="Active" selected>
                            Active
                        </option>

                        <option value="Inactive">
                            Inactive
                        </option>

                    </select>

                </div>


                <!-- HAPUS -->

                <div class="field material-action">

                    <label class="field-label">

                        &nbsp;

                    </label>


                    <button
                        type="button"
                        class="btn btn--danger btn-remove-material"
                    >

                        Hapus

                    </button>

                </div>


            </div>

        `;


                materialRows.appendChild(row);

                // === INIT SELECT2 UNTUK ROW BARU ===
                $(row).find('.select2').select2({
                    width: '100%'
                });

                materialIndex++;

                updateRemoveButtons();

            });


            /*
            =====================================================
            HAPUS MATERIAL
            =====================================================
            */

            materialRows.addEventListener('click', function(event) {

                const button = event.target.closest(
                    '.btn-remove-material'
                );


                if (!button) {

                    return;

                }


                const rows = materialRows.querySelectorAll(
                    '.material-row'
                );


                /*
                JANGAN BOLEH HAPUS SEMUA ROW
                */

                if (rows.length <= 1) {

                    return;

                }


                button.closest('.material-row').remove();

                updateRemoveButtons();

            });


            /*
            =====================================================
            UPDATE BUTTON HAPUS
            =====================================================
            */

            function updateRemoveButtons() {

                const rows = materialRows.querySelectorAll(
                    '.material-row'
                );


                rows.forEach(function(row) {

                    const button = row.querySelector(
                        '.btn-remove-material'
                    );


                    if (rows.length === 1) {

                        button.disabled = true;

                    } else {

                        button.disabled = false;

                    }

                });

            }

        });

        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });
        });
    </script>
@endsection

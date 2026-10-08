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
                        {{-- <h1 class="hero-title">Edit Kompatibilitas Mesin & Material</h1> --}}
                        <p class="hero-sub">Perbarui dan atur hubungan kompatibilitas antara mesin dan material yang dapat
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

                        <form action="{{ route('admin_update_compatibility_material', $compatibility->id) }}"
                            method="POST">

                            @csrf
                            @method('PUT')

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
                                            <option value="{{ $engine->id }}"
                                                {{ $engine->id == $compatibility->engine_id ? 'selected' : '' }}>

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


                                <div class="form-grid material-form-grid" style="margin-bottom: 15px;">

                                    {{-- MATERIAL --}}

                                    <div class="field">

                                        <label class="field-label">

                                            Material

                                            <span class="req">*</span>

                                        </label>

                                        <select name="material_id" class="select select2" required>

                                            <option value="">
                                                Pilih Material
                                            </option>

                                            @foreach ($materials as $material)
                                                <option value="{{ $material->id }}"
                                                    {{ $material->id == $compatibility->material_id ? 'selected' : '' }}>

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

                                        <select name="status" class="select select2" required>

                                            <option value="Active"
                                                {{ $compatibility->status == 'Active' ? 'selected' : '' }}>
                                                Active
                                            </option>

                                            <option value="Inactive"
                                                {{ $compatibility->status == 'Inactive' ? 'selected' : '' }}>
                                                Inactive
                                            </option>

                                        </select>

                                    </div>

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
        $(document).ready(function() {
            $('.select2').select2({
                width: '100%'
            });
        });
    </script>
@endsection

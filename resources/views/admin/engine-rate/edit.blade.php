@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">HARGA & TARIF · TARIF MESIN</span>
                        <h1 class="hero-title">Edit Tarif Mesin</h1>
                        <p class="hero-sub">Perbarui dan konfigurasi tarif mesin sebagai dasar perhitungan biaya dan estimasi
                            harga.</p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">HARGA & TARIF</span>
                                <h2 class="card-title">Konfigurasi Tarif Mesin</h2>
                            </div>
                        </div>
                        <form onsubmit="event.preventDefault(); alert(\'Saved (demo)\');">
                            <div class="form-grid">
                                <div class="field">
                                    <label class="field-label" for="engine_id">Nama Mesin<span
                                            class="req">*</span></label></label>
                                    <select id="engine_id" class="select" required>
                                        <option selected="selected">
                                            A3+
                                        </option>
                                        <option>Large Format</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label class="field-label" for="variant">Varian
                                        <span class="req">*</span></label>
                                    <input id="variant" class="input" type="text" required />
                                </div>
                                <div class="field">
                                    <label class="field-label" for="unit">Unit
                                        <span class="req">*</span></label>
                                    <input id="unit" class="input" type="text" required />
                                </div>
                                <div class="field">
                                    <label class="field-label" for="cost_rate">Cost Rate
                                        <span class="req">*</span></label>
                                    <input id="cost_rate" class="input" type="text" required />
                                </div>
                                <div class="field">
                                    <label class="field-label" for="minimum_charge">Minimumm Charge
                                        <span class="req">*</span></label>
                                    <input id="minimum_charge" class="input" type="text" required />
                                </div>
                                <div class="field">
                                    <label class="field-label" for="source">Sumber
                                        <span class="req">*</span></label>
                                    <input id="source" class="input" type="text" required />
                                </div>
                                <div class="field">
                                    <label class="field-label" for="vendor_id">Vendor<span
                                            class="req">*</span></label></label>
                                    <select id="vendor_id" class="select" required>
                                        <option selected="selected">
                                            Vendor LF Outsourcing — Print Standar
                                        </option>
                                        <option>Vendor LF Outsourcing — Indoor & UV</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label class="field-label" for="effective_date">Tanggal Efektif
                                        <span class="req">*</span></label>
                                    <input id="effective_date" class="input" type="date" required />
                                </div>
                                <div class="field">
                                    <label class="field-label" for="status">Status</label>
                                    <select id="status" class="select">
                                        <option selected="selected">
                                            Active
                                        </option>
                                        <option>In Active</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions">
                                <span class="badge dot success">All changes saved automatically</span>
                                <span class="spacer"></span>
                                <a href="{{ route('admin_engine_rates') }}" class="btn btn--ghost">
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

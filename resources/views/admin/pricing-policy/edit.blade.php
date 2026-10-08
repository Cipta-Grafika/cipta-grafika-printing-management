@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">HARGA & TARIF · KEBIJAKAN HARGA</span>
                        <h1 class="hero-title">Edit Kebijakan Harga</h1>
                        <p class="hero-sub">Perbarui dan konfigurasi kebijakan harga untuk menyesuaikan aturan dalam
                            menentukan harga jual dan perhitungan estimasi harga.</p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">HARGA & TARIF</span>
                                <h2 class="card-title">Konfigurasi Kebijakan Harga</h2>
                            </div>
                        </div>
                        <form onsubmit="event.preventDefault(); alert(\'Saved (demo)\');">
                            <div class="form-grid">
                                <div class="field">
                                    <label class="field-label" for="engine_id">Mesin<span
                                            class="req">*</span></label></label>
                                    <select id="engine_id" class="select" required>
                                        <option selected="selected">
                                            A3+
                                        </option>
                                        <option>Large Format</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label class="field-label" for="metode">Metode<span
                                            class="req">*</span></label></label>
                                    <select id="metode" class="select" required>
                                        <option selected="selected">
                                            Margin (% dari harga jual)
                                        </option>
                                        <option>Markup (% dari cost)</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label class="field-label" for="margin_markup">Margin/Markup (%)
                                        <span class="req">*</span></label>
                                    <input id="margin_markup" class="input" type="text" required />
                                </div>
                                <div class="field">
                                    <label class="field-label" for="minimum_charge">Minumum Charge (Rp, opsional)
                                        <span class="req">*</span></label>
                                    <input id="minimum_charge" class="input" type="text" required />
                                </div>
                                <div class="field">
                                    <label class="field-label" for="rounding">Pembulatan (Rp, opsional)<span
                                            class="req">*</span></label></label>
                                    <select id="rounding" class="select" required>
                                        <option selected="selected">
                                            Tidak dibulatkan
                                        </option>
                                        <option>Ke Rp100 terdekat</option>
                                        <option>Ke Rp500 terdekat</option>
                                        <option>Ke Rp1.000 terdekat</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-actions">
                                <span class="badge dot success">All changes saved automatically</span>
                                <span class="spacer"></span>
                                <a href="{{ route('admin_pricing_policies') }}" class="btn btn--ghost">
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

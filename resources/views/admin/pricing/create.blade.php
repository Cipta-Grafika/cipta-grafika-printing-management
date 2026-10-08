@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">HARGA & TARIF · DAFTAR HARGA</span>
                        <h1 class="hero-title">Tambah Daftar Harga</h1>
                        <p class="hero-sub">Tambahkan dan konfigurasi daftar harga sebagai acuan dalam penentuan harga jual
                            dan perhitungan estimasi harga.</p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">HARGA & TARIF</span>
                                <h2 class="card-title">Konfigurasi Daftar Harga</h2>
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
                                    <label class="field-label" for="material_code">Kode Material<span
                                            class="req">*</span></label></label>
                                    <select id="material_code" class="select" required>
                                        <option selected="selected">
                                            A3FC-ARTCARTON210GSM-matx1
                                        </option>
                                        <option>A3FC-ARTCARTON260GSM-matx2</option>
                                        <option>A3FC-ARTCARTON230GSM-matx3</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label class="field-label" for="profile">Profil<span
                                            class="req">*</span></label></label>
                                    <select id="profile" class="select" required>
                                        <option selected="selected">
                                            Umum
                                        </option>
                                        <option>Divisi</option>
                                        <option>Polos</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label class="field-label" for="print_side">Sisi<span
                                            class="req">*</span></label></label>
                                    <select id="print_side" class="select" required>
                                        <option selected="selected">
                                            1
                                        </option>
                                        <option>2</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label class="field-label" for="color">Warna<span
                                            class="req">*</span></label></label>
                                    <select id="color" class="select" required>
                                        <option selected="selected">
                                            Full Color
                                        </option>
                                        <option>Black White</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label class="field-label" for="qty_min">Quantity Min
                                        <span class="req">*</span></label>
                                    <input id="qty_min" class="input" type="text" required />
                                </div>
                                <div class="field">
                                    <label class="field-label" for="qty_max">Quantity Max
                                        <span class="req">*</span></label>
                                    <input id="qty_max" class="input" type="text" required />
                                </div>
                                <div class="field">
                                    <label class="field-label" for="location">Lokasi<span
                                            class="req">*</span></label></label>
                                    <select id="location" class="select" required>
                                        <option selected="selected">
                                            Graha
                                        </option>
                                        <option>Outsourcing</option>
                                        <option>Purwakarta</option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label class="field-label" for="unit_price">Harga Unit<span
                                            class="req">*</span></label></label>
                                    <input id="unit_price" class="input" type="text" required />
                                </div>
                                <div class="field">
                                    <label class="field-label" for="effective_date">Tanggal Efektif
                                        <span class="req">*</span></label>
                                    <input id="effective_date" class="input" type="date" required />
                                </div>
                            </div>
                            <div class="form-actions">
                                <span class="badge dot success">All changes saved automatically</span>
                                <span class="spacer"></span>
                                <a href="{{ route('admin_pricings') }}" class="btn btn--ghost">
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

@extends('admin_master')

@section('contents')
    <div class="shell">

        <div data-shell-sidebar></div>

        <div class="main">

            <div data-shell-topbar></div>

            <main class="content">

                {{-- HERO --}}
                <section class="hero"
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
                    <div class="hero-text" style="flex: 1; min-width: 280px;">
                        <span class="eyebrow">
                            ESTIMASI PRODUKSI BROSUR &amp; FLYER
                        </span>
                        <h1 class="hero-title" style="margin: 6px 0 8px;">
                            Buat Estimasi Cetak Brosur (Digital &amp; Offset)
                        </h1>
                        <p class="hero-sub" style="margin: 0;">
                            Satu halaman terpadu untuk kalkulasi paket cepat <strong>Digital Print A3+</strong> (100 &amp;
                            500 lembar) maupun cetak massal <strong>Mesin Offset SM 52</strong> (1 - 10+ rim) sesuai standar
                            produksi.
                        </p>
                    </div>
                    {{-- <div>
                        <a href="{{ route('admin_home') }}" class="btn-back-header">
                            <i class="bi bi-arrow-left"></i>
                            <span>Kembali</span>
                        </a>
                    </div> --}}
                </section>

                <div class="offset-estimator-grid">

                    {{-- =========================================================
                        KOLOM KIRI: FORM ESTIMASI (7 COLS / FLEX 1.25)
                    ========================================================== --}}
                    <div class="offset-form-col">
                        <section class="card" style="margin-bottom: 24px;">

                            <div class="card-head">
                                <div class="card-title-wrap">
                                    <span class="eyebrow">
                                        FORMULIR SPESIFIKASI ESTIMASI
                                    </span>
                                </div>
                            </div>

                            <form id="offsetEstimationForm">
                                @csrf

                                {{-- =========================================================
                                    1. INFORMASI ESTIMASI & PELANGGAN
                                ========================================================== --}}
                                <div class="form-section"
                                    style="padding: 16px 20px; border-bottom: 1px solid var(--border);">
                                    <div class="form-section-head" style="margin-bottom: 16px;">
                                        <span class="eyebrow"
                                            style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-person-badge"></i> 1. INFORMASI PELANGGAN &amp; LOKASI
                                        </span>
                                    </div>

                                    <div class="form-grid"
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px;">
                                        {{-- Lokasi Cabang --}}
                                        <div class="field">
                                            <label class="field-label" for="location_id">
                                                Lokasi Cabang
                                                <span class="req">*</span>
                                            </label>
                                            <select id="location_id" name="location_id" class="select select2" required>
                                                <option value="">Pilih Cabang</option>
                                                @foreach ($locations ?? [] as $loc)
                                                    <option value="{{ $loc->id }}"
                                                        {{ $loop->first ? 'selected' : '' }}>
                                                        {{ $loc->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        {{-- Tipe Harga --}}
                                        <div class="field">
                                            <label class="field-label" for="price_type">
                                                Profil Harga
                                                <span class="req">*</span>
                                            </label>
                                            <select id="price_type" name="price_type" class="select select2" required>
                                                <option value="general" selected>Harga Umum (Markup
                                                    {{ (float) ($pricingRule->markup_general ?? 100) }}%)</option>
                                                <option value="division">Harga Divisi (Markup
                                                    {{ (float) ($pricingRule->markup_division ?? 80) }}%)</option>
                                            </select>
                                        </div>

                                        {{-- Estimasi Lead Time --}}
                                        <div class="field">
                                            <label class="field-label" for="lead_time">
                                                Estimasi Waktu Kerja
                                            </label>
                                            <div style="position: relative;">
                                                <input type="number" id="lead_time" name="lead_time" class="input"
                                                    value="3" min="1" max="30"
                                                    style="padding-right: 70px;">
                                                <span
                                                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 13px; color: var(--t-muted);">hari</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-grid"
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 14px;">
                                        {{-- Nama Pelanggan --}}
                                        <div class="field">
                                            <label class="field-label" for="customer_name">
                                                Nama Pelanggan / Perusahaan
                                            </label>
                                            <input type="text" id="customer_name" name="customer_name" class="input"
                                                placeholder="Contoh: PT. Sumber Makmur">
                                        </div>

                                        {{-- No. WhatsApp / Telepon --}}
                                        <div class="field">
                                            <label class="field-label" for="customer_phone">
                                                No. WhatsApp / Telepon
                                            </label>
                                            <input type="text" id="customer_phone" name="customer_phone" class="input"
                                                placeholder="Contoh: 08123456789">
                                        </div>
                                    </div>
                                </div>

                                {{-- =========================================================
                                    2. PILIHAN METODE CETAK (DIGITAL vs OFFSET)
                                ========================================================== --}}
                                <div class="form-section"
                                    style="padding: 16px 20px; border-bottom: 1px solid var(--border);">
                                    <div class="form-section-head" style="margin-bottom: 12px;">
                                        <span class="eyebrow"
                                            style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-gear-wide-connected"></i> 2. METODE PRODUKSI CETAK
                                        </span>
                                    </div>

                                    <div
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px;">
                                        <label class="method-card active" id="cardMethodOffset">
                                            <input type="radio" name="production_method" value="offset" checked
                                                style="display: none;">
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div class="method-icon-wrap"
                                                    style="width: 44px; height: 44px; border-radius: 10px; background: rgba(30, 58, 138, 0.15); color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
                                                    🏭
                                                </div>
                                                <div>
                                                    <div style="font-weight: 700; font-size: 14px; color: var(--t-base);">
                                                        Cetak Mesin Offset</div>
                                                    <small
                                                        style="color: var(--t-muted); font-size: 12px; display: block; margin-top: 2px;">
                                                        Kuantitas Massal (≥ 1 Rim). Plat CTP &amp; Kertas Plano SM 52.
                                                    </small>
                                                </div>
                                            </div>
                                        </label>

                                        <label class="method-card" id="cardMethodDigital">
                                            <input type="radio" name="production_method" value="digital"
                                                style="display: none;">
                                            <div style="display: flex; align-items: center; gap: 12px;">
                                                <div class="method-icon-wrap"
                                                    style="width: 44px; height: 44px; border-radius: 10px; background: rgba(16, 185, 129, 0.15); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;">
                                                    🖨️
                                                </div>
                                                <div>
                                                    <div style="font-weight: 700; font-size: 14px; color: var(--t-base);">
                                                        Digital Print A3+</div>
                                                    <small
                                                        style="color: var(--t-muted); font-size: 12px; display: block; margin-top: 2px;">
                                                        Paket Cepat (100 &amp; 500 Lembar). Cetak Lembaran A3+ &amp; Potong.
                                                    </small>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                {{-- =========================================================
                                    3. SPESIFIKASI PRODUK & BAHAN
                                ========================================================== --}}
                                <div class="form-section" style="padding: 16px 20px;">
                                    <div class="form-section-head" style="margin-bottom: 16px;">
                                        <span class="eyebrow"
                                            style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-journals"></i> 3. SPESIFIKASI PRODUK &amp; BAHAN
                                        </span>
                                    </div>

                                    {{-- Produk Brosur --}}
                                    <div class="field" style="margin-bottom: 16px;">
                                        <label class="field-label" for="product_id">
                                            Produk Cetak Brosur / Flyer
                                            <span class="req">*</span>
                                        </label>
                                        <select id="product_id" name="product_id" class="select select2" required>
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach ($products ?? [] as $prod)
                                                <option value="{{ $prod->id }}"
                                                    data-sheet-per-rim="{{ $prod->sheet_per_rim }}"
                                                    data-plano-yield="{{ $prod->plano_cut_yield }}"
                                                    data-final-size="{{ $prod->final_length }} x {{ $prod->final_width }} cm"
                                                    data-press-size="{{ $prod->print_sheet_length }} x {{ $prod->print_sheet_width }} cm"
                                                    {{ str_contains(strtolower($prod->product_name), 'a5') ? 'selected' : '' }}>
                                                    {{ $prod->product_name }} ({{ $prod->final_length }} x
                                                    {{ $prod->final_width }} cm)
                                                </option>
                                            @endforeach
                                        </select>

                                        {{-- Product Info Snippet --}}
                                        <div id="productSpecsBadge"
                                            style="margin-top: 8px; display: flex; flex-wrap: wrap; gap: 8px;">
                                            <span class="badge-spec badge-spec-primary">
                                                <i class="bi bi-arrows-fullscreen"></i> Ukuran Jadi: <strong
                                                    id="specFinalSize">-</strong>
                                            </span>
                                            <span class="badge-spec badge-spec-success">
                                                <i class="bi bi-stack"></i> Lembar Mesin / Rim: <strong
                                                    id="specSheetPerRim">-</strong> lbr
                                            </span>
                                            <span class="badge-spec badge-spec-warning">
                                                <i class="bi bi-scissors"></i> Potong Plano: <strong
                                                    id="specPlanoYield">-</strong> up
                                            </span>
                                            <span class="badge-spec badge-spec-purple">
                                                <i class="bi bi-printer"></i> Lembar Mesin: <strong
                                                    id="specPressSize">-</strong>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Kertas Plano --}}
                                    <div class="field" style="margin-bottom: 16px;">
                                        <label class="field-label" for="plano_material_id">
                                            Pilihan Kertas Plano
                                            <span class="req">*</span>
                                        </label>
                                        <select id="plano_material_id" name="plano_material_id" class="select select2"
                                            required>
                                            <option value="">-- Pilih Kertas Plano --</option>
                                            @foreach ($planoMaterials ?? [] as $plano)
                                                <option value="{{ $plano->id }}"
                                                    data-gramature="{{ $plano->gramature }}"
                                                    data-price="{{ (float) $plano->price_per_plano }}"
                                                    data-size="{{ $plano->width }} x {{ $plano->length }} cm"
                                                    data-paper-type="{{ $plano->paper_type }}"
                                                    {{ str_contains(strtolower($plano->material_name), '120') ? 'selected' : '' }}>
                                                    {{ $plano->material_name }} · Rp
                                                    {{ number_format($plano->price_per_plano, 0, ',', '.') }} / plano
                                                    ({{ $plano->width }} x {{ $plano->length }} cm)
                                                </option>
                                            @endforeach
                                        </select>

                                        {{-- Plano Info Snippet --}}
                                        <div id="planoSpecsBadge"
                                            style="margin-top: 8px; display: flex; flex-wrap: wrap; gap: 8px;">
                                            <span class="badge-spec badge-spec-primary">
                                                <i class="bi bi-file-earmark"></i> Gramatur: <strong
                                                    id="specGramature">-</strong> gsm
                                            </span>
                                            <span class="badge-spec badge-spec-success">
                                                <i class="bi bi-aspect-ratio"></i> Dimensi Plano: <strong
                                                    id="specPlanoDim">-</strong>
                                            </span>
                                            <span class="badge-spec badge-spec-warning">
                                                <i class="bi bi-tag"></i> Harga / Plano: <strong
                                                    id="specPlanoPrice">-</strong>
                                            </span>
                                            <span class="badge-spec badge-spec-purple" id="excelEquivBadge">
                                                <i class="bi bi-file-earmark-spreadsheet"></i> Ekuivalen Excel: <strong
                                                    id="specExcelEquiv">-</strong>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-grid"
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 18px;">
                                        {{-- Sisi Cetak --}}
                                        <div class="field">
                                            <label class="field-label" style="margin-bottom: 8px;">
                                                Sisi Cetak
                                                <span class="req">*</span>
                                            </label>
                                            <div class="radio-cards-wrap">
                                                <label class="radio-card">
                                                    <input type="radio" name="print_sides" value="1" checked
                                                        style="accent-color: var(--primary);">
                                                    <div>
                                                        <div class="radio-card-title">1 Sisi (1 Muka)</div>
                                                        <small class="radio-card-sub" id="labelSide1Sub">4/0 Full
                                                            Color</small>
                                                    </div>
                                                </label>
                                                <label class="radio-card">
                                                    <input type="radio" name="print_sides" value="2"
                                                        style="accent-color: var(--primary);">
                                                    <div>
                                                        <div class="radio-card-title">2 Sisi (2 Muka)</div>
                                                        <small class="radio-card-sub" id="labelSide2Sub">4/4
                                                            Bolak-Balik</small>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        {{-- Kuantitas Offset (Rim) --}}
                                        <div class="field" id="offsetQtyWrapper">
                                            <label class="field-label" for="rims">
                                                Kuantitas (Rim)
                                                <span class="req">*</span>
                                            </label>
                                            <div style="position: relative;">
                                                <input type="number" id="rims" name="rims" class="input"
                                                    value="1" min="0.5" step="0.5" required
                                                    style="padding-right: 60px; font-weight: 600; font-size: 15px;">
                                                <span
                                                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 13px; font-weight: 600; color: var(--t-muted);">Rim</span>
                                            </div>
                                            <small id="qtyPiecesNotice"
                                                style="display: block; margin-top: 6px; font-size: 12px; color: var(--primary); font-weight: 500;">
                                                = 500 lembar brosur jadi
                                            </small>
                                        </div>

                                        {{-- Kuantitas Digital (Lembar / Paket) --}}
                                        <div class="field" id="digitalQtyWrapper" style="display: none;">
                                            <label class="field-label" for="digital_pieces">
                                                Pilihan Paket Kuantitas
                                                <span class="req">*</span>
                                            </label>
                                            <div style="display: flex; gap: 8px; margin-bottom: 8px;">
                                                <button type="button" class="pkg-quick-btn active" data-pkg-qty="100">
                                                    100 Lembar
                                                </button>
                                                <button type="button" class="pkg-quick-btn" data-pkg-qty="500">
                                                    500 Lembar (1 Rim)
                                                </button>
                                            </div>
                                            <div style="position: relative;">
                                                <input type="number" id="digital_pieces" name="digital_pieces"
                                                    class="input" value="100" min="20" step="10"
                                                    style="padding-right: 80px; font-weight: 600; font-size: 15px;">
                                                <span
                                                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); font-size: 13px; font-weight: 600; color: var(--t-muted);">Lembar</span>
                                            </div>
                                            <small id="digitalSheetsNotice"
                                                style="display: block; margin-top: 6px; font-size: 12px; color: var(--primary); font-weight: 500;">
                                                Kebutuhan Cetak: 25 Lembar A3+ (4-up) · Ongkos Potong Rp 35.000
                                            </small>
                                        </div>
                                    </div>

                                    {{-- Catatan / Keterangan --}}
                                    <div class="field" style="margin-top: 16px;">
                                        <label class="field-label" for="notes">
                                            Catatan Khusus Produksi (Opsional)
                                        </label>
                                        <textarea id="notes" name="notes" class="input" rows="2"
                                            placeholder="Contoh: Packing per 100 pcs plastik, kirim ke cabang Purwakarta..." style="resize: vertical;"></textarea>
                                    </div>
                                </div>

                                {{-- =========================================================
                                    4. DISKON ESTIMASI
                                ========================================================== --}}
                                <div class="form-section"
                                    style="padding: 16px 20px; border-bottom: 1px solid var(--border);">
                                    <div class="form-section-head" style="margin-bottom: 16px;">
                                        <span class="eyebrow"
                                            style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-tag-fill"></i> 4. DISKON ESTIMASI
                                        </span>
                                    </div>

                                    <div class="field" style="margin-bottom: 16px;">
                                        <label class="field-label">
                                            Jenis Diskon
                                            <span class="req">*</span>
                                        </label>

                                        <div class="discount-options">
                                            {{-- Tanpa Diskon --}}
                                            <label class="discount-option">
                                                <input type="radio" name="discount_type" value="none" checked>
                                                <span class="discount-option__content">
                                                    <span class="discount-option__icon">
                                                        <i class="bi bi-tag"></i>
                                                    </span>
                                                    <span class="discount-option__text">
                                                        <strong>Tanpa Diskon</strong>
                                                        <small>Harga normal standar</small>
                                                    </span>
                                                </span>
                                            </label>

                                            {{-- Diskon Persen --}}
                                            <label class="discount-option">
                                                <input type="radio" name="discount_type" value="percentage">
                                                <span class="discount-option__content">
                                                    <span class="discount-option__icon">
                                                        <i class="bi bi-percent"></i>
                                                    </span>
                                                    <span class="discount-option__text">
                                                        <strong>Diskon Persen</strong>
                                                        <small>Potongan persentase (%)</small>
                                                    </span>
                                                </span>
                                            </label>

                                            {{-- Diskon Nominal --}}
                                            <label class="discount-option">
                                                <input type="radio" name="discount_type" value="nominal">
                                                <span class="discount-option__content">
                                                    <span class="discount-option__icon">
                                                        <i class="bi bi-cash-stack"></i>
                                                    </span>
                                                    <span class="discount-option__text">
                                                        <strong>Diskon Nominal</strong>
                                                        <small>Potongan nominal (Rp)</small>
                                                    </span>
                                                </span>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Input Nilai Diskon --}}
                                    <div class="field" id="discountValueField"
                                        style="display: none; margin-bottom: 8px;">
                                        <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                                            <label class="field-label" for="discount_value" id="discountValueLabel"
                                                style="margin-bottom: 0;">
                                                Nilai Diskon
                                            </label>
                                            <span class="req">*</span>
                                        </div>
                                        <div style="position: relative;">
                                            <input type="text" id="discount_value" name="discount_value"
                                                class="input" inputmode="decimal" placeholder="Contoh: 10" disabled
                                                style="padding-right: 50px; font-weight: 600; font-size: 14.5px;">
                                            <span id="discountSuffix"
                                                style="position: absolute; right: 14px; top: 50%; transform: translateY(-50%); font-weight: 700; font-size: 13.5px; color: var(--primary);">
                                                %
                                            </span>
                                        </div>
                                        <small id="discountHelpText"
                                            style="display: block; margin-top: 6px; font-size: 12px; color: var(--t-muted);">
                                            Masukkan besaran persentase diskon (1 - 100%).
                                        </small>
                                    </div>
                                </div>

                                {{-- ACTION BUTTONS --}}
                                <div class="form-action-bar">
                                    <a href="{{ route('admin_home') }}" class="btn btn-secondary-custom"
                                        style="display: inline-flex; align-items: center; gap: 8px; text-decoration: none;">
                                        <i class="bi bi-arrow-left"></i> Kembali
                                    </a>
                                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                                        <button type="reset" class="btn btn-secondary-custom">
                                            <i class="bi bi-arrow-counterclockwise"></i> Reset Form
                                        </button>
                                        <button type="button" id="btnCalculate" class="btn btn--primary"
                                            style="display: flex; align-items: center; gap: 8px; font-weight: 600; padding: 10px 22px;">
                                            <i class="bi bi-calculator"></i>
                                            <span>Hitung Estimasi</span>
                                        </button>
                                    </div>
                                </div>

                            </form>

                        </section>
                    </div>

                    {{-- =========================================================
                        KOLOM KANAN: LIVE BREAKDOWN & HASIL HARGA
                    ========================================================== --}}
                    <div class="offset-summary-col">
                        <section class="card offset-sticky-card">

                            {{-- CARD HEAD --}}
                            <div class="card-head"
                                style="background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); color: #fff; padding: 18px 20px; border-radius: 13px 13px 0 0; margin-bottom: 0;">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                                    <div>
                                        <span class="eyebrow"
                                            style="color: #93c5fd; font-size: 11px; letter-spacing: 0.8px;">
                                            HASIL KALKULASI ESTIMASI
                                        </span>
                                        <h2 style="font-size: 1.15rem; font-weight: 700; color: #ffffff; margin: 4px 0 0;">
                                            Ringkasan Harga &amp; HPP
                                        </h2>
                                    </div>
                                    <span id="priceTypeBadge" class="badge"
                                        style="background: rgba(255,255,255,0.15); color: #fff; border: 1px solid rgba(255,255,255,0.25); padding: 4px 10px; border-radius: 20px; font-size: 11.5px;">
                                        Harga Umum
                                    </span>
                                </div>
                            </div>

                            <div style="padding: 20px;">

                                {{-- HIGHLIGHT TOTAL HARGA JUAL --}}
                                <div class="selling-price-box">
                                    <div class="selling-price-box-label">
                                        Total Rekomendasi Harga Jual
                                    </div>

                                    {{-- Info Diskon (Muncul jika ada diskon) --}}
                                    <div id="liveDiscountWrap"
                                        style="display: none; margin: 8px 0 10px; padding: 8px 12px; background: rgba(239, 68, 68, 0.08); border-radius: 8px; border: 1px dashed rgba(239, 68, 68, 0.35);">
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: center; font-size: 12.5px;">
                                            <span style="color: var(--t-muted);">Harga Sebelum Diskon:</span>
                                            <s id="liveSubtotalPrice" style="color: var(--t-muted); font-weight: 600;">Rp
                                                -</s>
                                        </div>
                                        <div
                                            style="display: flex; justify-content: space-between; align-items: center; font-size: 12.5px; margin-top: 4px;">
                                            <span style="color: #ef4444; font-weight: 600;">Hemat Diskon (<span
                                                    id="liveDiscountDesc">-</span>):</span>
                                            <strong id="liveDiscountAmount" style="color: #ef4444; font-weight: 700;">- Rp
                                                -</strong>
                                        </div>
                                    </div>

                                    <div id="liveTotalSellingPrice" class="selling-price-box-val">
                                        Rp -
                                    </div>
                                    <div class="selling-price-box-sub">
                                        <div>
                                            <span id="lblUnitSelling1" class="sub-label">Per Rim:</span>
                                            <strong id="liveSellingPricePerRim" class="sub-val">Rp -</strong>
                                        </div>
                                        <div class="sub-divider">
                                            <span class="sub-label">Per Lembar/Pcs:</span>
                                            <strong id="liveSellingPricePerPcs" class="sub-val">Rp -</strong>
                                        </div>
                                    </div>
                                    <div style="margin-top: 8px;">
                                        <small class="rounding-pill">
                                            <i class="bi bi-check2-circle"></i> Pembulatan Kelipatan Rp 5.000
                                        </small>
                                    </div>
                                </div>

                                {{-- SMART COMPARISON BOX --}}
                                <div id="smartComparisonBox"
                                    style="display: none; margin-bottom: 18px; padding: 12px 14px; border-radius: 8px; background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.25); font-size: 12.5px; color: var(--t-base); line-height: 1.5;">
                                    <div style="display: flex; align-items: flex-start; gap: 8px;">
                                        <i class="bi bi-lightbulb-fill"
                                            style="color: #f59e0b; font-size: 16px; margin-top: 1px; flex-shrink: 0;"></i>
                                        <div id="smartComparisonText">
                                            <!-- Tips perbandingan cerdas -->
                                        </div>
                                    </div>
                                </div>

                                {{-- RINCIAN KOMPONEN BIAYA (HPP BREAKDOWN) --}}
                                <div style="margin-bottom: 18px;">
                                    <div
                                        style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: var(--t-muted); letter-spacing: 0.5px; margin-bottom: 12px; display: flex; align-items: center; justify-content: space-between;">
                                        <span>Rincian Biaya Pokok (HPP)</span>
                                        <span id="liveTotalHppBadge" class="badge"
                                            style="background: var(--bg-muted); color: var(--t-base); border: 1px solid var(--border); font-size: 12px;">HPP:
                                            Rp -</span>
                                    </div>

                                    <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px;">

                                        {{-- 1. Kertas Plano / Cetak A3+ --}}
                                        <div class="breakdown-row" id="liveRow1Wrap">
                                            <div>
                                                <div id="liveRow1Title" class="breakdown-row-title">1. Kertas Plano</div>
                                                <small id="livePaperDetail" class="breakdown-row-sub">-</small>
                                            </div>
                                            <strong id="livePaperCost" class="breakdown-row-cost">Rp -</strong>
                                        </div>

                                        {{-- 2. Plat CTP Mesin / Potong Digital --}}
                                        <div class="breakdown-row" id="liveRow2Wrap">
                                            <div>
                                                <div id="liveRow2Title" class="breakdown-row-title">2. Plat CTP Mesin
                                                </div>
                                                <small id="livePlateDetail" class="breakdown-row-sub">4 Plat CMYK @ Rp
                                                    16.000</small>
                                            </div>
                                            <strong id="livePlateCost" class="breakdown-row-cost">Rp -</strong>
                                        </div>

                                        {{-- 3. Cetak Mesin Offset --}}
                                        <div class="breakdown-row" id="liveRow3Wrap">
                                            <div>
                                                <div class="breakdown-row-title">3. Ongkos Cetak Mesin</div>
                                                <small id="livePrintDetail" class="breakdown-row-sub">-</small>
                                            </div>
                                            <strong id="livePrintCost" class="breakdown-row-cost">Rp -</strong>
                                        </div>

                                        {{-- 4. Potong Jadi --}}
                                        <div class="breakdown-row" id="liveRow4Wrap">
                                            <div>
                                                <div class="breakdown-row-title">4. Ongkos Potong</div>
                                                <small id="liveCuttingDetail" class="breakdown-row-sub">Rp 20.000 /
                                                    rim</small>
                                            </div>
                                            <strong id="liveCuttingCost" class="breakdown-row-cost">Rp -</strong>
                                        </div>

                                        {{-- Total HPP Bar --}}
                                        <div class="total-hpp-bar">
                                            <div>
                                                <strong class="total-hpp-bar-title">TOTAL HPP PRODUKSI</strong>
                                                <div class="total-hpp-bar-sub">(HPP / unit: <span id="liveHppPerRim">Rp
                                                        -</span> · /pcs: <span id="liveHppPerPcs">Rp -</span>)</div>
                                            </div>
                                            <strong id="liveTotalHpp" class="total-hpp-bar-val">Rp -</strong>
                                        </div>

                                    </div>
                                </div>

                                {{-- ACTION BUTTON: LIHAT DETAIL & PRINT --}}
                                <button type="button" id="btnOpenModal" class="btn-preview-estimation">
                                    <i class="bi bi-printer"></i> Preview &amp; Cetak Surat Estimasi
                                </button>

                            </div>

                        </section>
                    </div>

                    {{-- =========================================================
                        BAGIAN BAWAH: MATRIKS SIMULASI PRICELIST (FULL WIDTH)
                    ========================================================== --}}
                    <div class="offset-matrix-col">
                        <section class="card" style="margin-top: 10px;">

                            <div class="card-head"
                                style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 14px; border-bottom: 1px solid var(--border); padding: 16px 20px; margin-bottom: 0;">
                                <div>
                                    <span id="matrixHeaderTitle" class="eyebrow"
                                        style="color: var(--primary); font-weight: 700;">
                                        SIMULASI PRICELIST BERTINGKAT (1 S/D 10 RIM)
                                    </span>
                                    <p id="matrixHeaderSub"
                                        style="margin: 2px 0 0; font-size: 13px; color: var(--t-muted);">
                                        Perbandingan lengkap HPP dan harga jual rekomendasi per rim &amp; per lembar. Klik
                                        tombol <strong>"Pilih"</strong> untuk menerapkan kuantitas tersebut ke form.
                                    </p>
                                </div>

                                {{-- Tab Sisi Matrix (Offset) --}}
                                <div id="matrixTabWrap" class="matrix-tab-container">
                                    <button type="button" class="matrix-tab-btn is-active" data-side="one_side">
                                        1 Sisi (1 Muka)
                                    </button>
                                    <button type="button" class="matrix-tab-btn" data-side="two_sides">
                                        2 Sisi (2 Muka)
                                    </button>
                                </div>
                            </div>

                            <div class="matrix-table-wrap">
                                <table class="matrix-table">
                                    <thead id="matrixTableHead">
                                        <tr>
                                            <th>Qty (Rim)</th>
                                            <th>Total Lembar Jadi</th>
                                            <th>Total HPP</th>
                                            <th>HPP / Rim</th>
                                            <th class="col-highlight">Harga Jual / Rim</th>
                                            <th class="col-highlight">Harga Jual / Pcs</th>
                                            <th class="col-highlight">Total Harga Jual</th>
                                            <th style="text-align: center;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="matrixTableBody">
                                        {{-- Rows populated via JavaScript --}}
                                        <tr>
                                            <td colspan="8"
                                                style="text-align: center; padding: 24px; color: var(--t-muted);">
                                                <i class="bi bi-hourglass-split"></i> Memuat simulasi harga...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                        </section>
                    </div>

                </div>

            </main>

        </div>

    </div>

    {{-- =========================================================
        MODAL PREVIEW & CETAK ESTIMASI OFFSET & DIGITAL
    ========================================================== --}}
    <div class="modal-overlay" id="modalOffsetPreview">
        <div class="modal modal-content-custom">

            {{-- MODAL HEAD --}}
            <div class="modal-head-custom">
                <div>
                    <span class="eyebrow" style="color: var(--primary); font-weight: 700;">PREVIEW ESTIMASI</span>
                    <h3 class="modal-title-custom" id="modalMainTitle">Surat Penawaran Cetak Offset</h3>
                </div>
                <button type="button" id="btnCloseModal" class="modal-close-btn" aria-label="Tutup">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- MODAL BODY (PRINTABLE) --}}
            <div id="printableEstimationArea" style="padding: 24px; overflow-y: auto; flex: 1;">
                <div class="printable-header">
                    <div>
                        <h2 class="printable-company-name">CIPTA GRAFIKA</h2>
                        <div id="modalSystemSub" style="font-size: 12px; color: var(--t-muted);">Printing Management
                            System · Estimator Cetak Brosur</div>
                    </div>
                    <div style="text-align: right; font-size: 12px; color: var(--t-muted);">
                        <div>Tanggal: <strong>{{ date('d/m/Y') }}</strong></div>
                        <div>Lead Time: <strong id="modalLeadTime">-</strong></div>
                    </div>
                </div>

                {{-- Customer & Order Info --}}
                <div class="printable-info-grid">
                    <div class="printable-info-box">
                        <div style="font-weight: 700; color: var(--t-base); margin-bottom: 4px;">INFORMASI PELANGGAN:</div>
                        <div>Nama: <strong id="modalCustomerName">-</strong></div>
                        <div>Telepon: <strong id="modalCustomerPhone">-</strong></div>
                        <div>Cabang: <strong id="modalCustomerLocation">-</strong></div>
                        <div id="modalNotesWrap"
                            style="display: none; margin-top: 4px; padding-top: 4px; border-top: 1px dashed var(--border);">
                            Catatan: <strong id="modalCustomerNotes">-</strong></div>
                    </div>
                    <div class="printable-info-box">
                        <div style="font-weight: 700; color: var(--t-base); margin-bottom: 4px;">SPESIFIKASI CETAK:</div>
                        <div>Produk: <strong id="modalProductName">-</strong></div>
                        <div>Kertas: <strong id="modalMaterialName">-</strong></div>
                        <div>Sisi Cetak: <strong id="modalPrintSide">-</strong></div>
                        <div>Kuantitas: <strong id="modalQty">-</strong></div>
                    </div>
                </div>

                {{-- Table Summary --}}
                <table class="printable-table">
                    <thead>
                        <tr>
                            <th style="padding: 8px 10px; text-align: left;">Item Deskripsi</th>
                            <th style="padding: 8px 10px; text-align: center;">Jumlah</th>
                            <th style="padding: 8px 10px; text-align: right;">Harga Satuan</th>
                            <th style="padding: 8px 10px; text-align: right;">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 10px;">
                                <strong id="modalItemTitle">Brosur Offset</strong>
                                <div id="modalItemDesc" style="font-size: 11.5px; color: var(--t-muted);">-</div>
                            </td>
                            <td id="modalItemQty" style="padding: 10px; text-align: center;">-</td>
                            <td id="modalItemUnitPrice" style="padding: 10px; text-align: right;">-</td>
                            <td id="modalItemSubtotal" style="padding: 10px; text-align: right; font-weight: 700;">-</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr id="modalSubtotalRow" style="display: none;">
                            <th colspan="3"
                                style="padding: 6px 10px; text-align: right; color: var(--t-muted); font-weight: 500;">
                                Subtotal (Sebelum Diskon):
                            </th>
                            <th id="modalSubtotalAmount"
                                style="padding: 6px 10px; text-align: right; font-weight: 600; color: var(--t-base);">
                                -
                            </th>
                        </tr>
                        <tr id="modalDiscountRow" style="display: none;">
                            <th colspan="3"
                                style="padding: 6px 10px; text-align: right; color: #ef4444; font-weight: 600;">
                                Potongan Diskon (<span id="modalDiscountLabel">-</span>):
                            </th>
                            <th id="modalDiscountAmount"
                                style="padding: 6px 10px; text-align: right; font-weight: 700; color: #ef4444;">
                                -
                            </th>
                        </tr>
                        <tr>
                            <th colspan="3" style="padding: 12px 10px; text-align: right; text-transform: uppercase;">
                                Total Harga Jual:</th>
                            <th id="modalGrandTotal"
                                style="padding: 12px 10px; text-align: right; font-weight: 800; color: var(--primary); font-size: 1.15rem;">
                                -</th>
                        </tr>
                    </tfoot>
                </table>

                <div id="modalFootTerms"
                    style="font-size: 11px; color: var(--t-muted); border-top: 1px dashed var(--border); padding-top: 10px;">
                    * Penawaran ini berlaku selama 14 hari sejak tanggal diterbitkan. Harga sudah termasuk plat CTP, ongkos
                    cetak mesin offset, dan ongkos potong standar.
                </div>
            </div>

            {{-- MODAL FOOT --}}
            <div class="modal-foot-custom">
                <button type="button" id="btnCancelModal" class="btn btn-secondary-custom">
                    Tutup
                </button>
                <button type="button" id="btnPrintEstimation" class="btn btn--primary"
                    style="display: flex; align-items: center; gap: 6px;">
                    <i class="bi bi-printer-fill"></i> Cetak Dokumen
                </button>
            </div>

        </div>
    </div>

    {{-- =========================================================
        JAVASCRIPT INTERACTIVITY & LIVE CALCULATION
    ========================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Inisialisasi Select2 jika tersedia
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.select2').select2({
                    width: '100%'
                });
            }

            // Elemen Form & Mode
            const form = document.getElementById('offsetEstimationForm');
            const cardMethodOffset = document.getElementById('cardMethodOffset');
            const cardMethodDigital = document.getElementById('cardMethodDigital');
            const offsetQtyWrapper = document.getElementById('offsetQtyWrapper');
            const digitalQtyWrapper = document.getElementById('digitalQtyWrapper');
            const productSelect = document.getElementById('product_id');
            const planoSelect = document.getElementById('plano_material_id');
            const rimsInput = document.getElementById('rims');
            const digitalPiecesInput = document.getElementById('digital_pieces');
            const pkgQuickButtons = document.querySelectorAll('.pkg-quick-btn');
            const priceTypeSelect = document.getElementById('price_type');
            const btnCalculate = document.getElementById('btnCalculate');
            const qtyPiecesNotice = document.getElementById('qtyPiecesNotice');
            const digitalSheetsNotice = document.getElementById('digitalSheetsNotice');

            // Snippet Badges
            const specFinalSize = document.getElementById('specFinalSize');
            const specSheetPerRim = document.getElementById('specSheetPerRim');
            const specPlanoYield = document.getElementById('specPlanoYield');
            const specPressSize = document.getElementById('specPressSize');
            const specGramature = document.getElementById('specGramature');
            const specPlanoDim = document.getElementById('specPlanoDim');
            const specPlanoPrice = document.getElementById('specPlanoPrice');
            const specExcelEquiv = document.getElementById('specExcelEquiv');

            // Live Output Card
            const liveTotalSellingPrice = document.getElementById('liveTotalSellingPrice');
            const lblUnitSelling1 = document.getElementById('lblUnitSelling1');
            const liveSellingPricePerRim = document.getElementById('liveSellingPricePerRim');
            const liveSellingPricePerPcs = document.getElementById('liveSellingPricePerPcs');
            const liveDiscountWrap = document.getElementById('liveDiscountWrap');
            const liveSubtotalPrice = document.getElementById('liveSubtotalPrice');
            const liveDiscountDesc = document.getElementById('liveDiscountDesc');
            const liveDiscountAmount = document.getElementById('liveDiscountAmount');

            // Discount Form Elements
            const discountTypeInputs = document.querySelectorAll('input[name="discount_type"]');
            const discountValueField = document.getElementById('discountValueField');
            const discountValueInput = document.getElementById('discount_value');
            const discountValueLabel = document.getElementById('discountValueLabel');
            const discountSuffix = document.getElementById('discountSuffix');
            const discountHelpText = document.getElementById('discountHelpText');
            const smartComparisonBox = document.getElementById('smartComparisonBox');
            const smartComparisonText = document.getElementById('smartComparisonText');
            const liveTotalHppBadge = document.getElementById('liveTotalHppBadge');
            const liveTotalHpp = document.getElementById('liveTotalHpp');
            const liveHppPerRim = document.getElementById('liveHppPerRim');
            const liveHppPerPcs = document.getElementById('liveHppPerPcs');

            // Breakdown Rows
            const liveRow1Wrap = document.getElementById('liveRow1Wrap');
            const liveRow1Title = document.getElementById('liveRow1Title');
            const livePaperCost = document.getElementById('livePaperCost');
            const livePaperDetail = document.getElementById('livePaperDetail');

            const liveRow2Wrap = document.getElementById('liveRow2Wrap');
            const liveRow2Title = document.getElementById('liveRow2Title');
            const livePlateCost = document.getElementById('livePlateCost');
            const livePlateDetail = document.getElementById('livePlateDetail');

            const liveRow3Wrap = document.getElementById('liveRow3Wrap');
            const livePrintCost = document.getElementById('livePrintCost');
            const livePrintDetail = document.getElementById('livePrintDetail');

            const liveRow4Wrap = document.getElementById('liveRow4Wrap');
            const liveCuttingCost = document.getElementById('liveCuttingCost');
            const liveCuttingDetail = document.getElementById('liveCuttingDetail');
            const priceTypeBadge = document.getElementById('priceTypeBadge');

            // Matrix Table & Headers
            const matrixHeaderTitle = document.getElementById('matrixHeaderTitle');
            const matrixHeaderSub = document.getElementById('matrixHeaderSub');
            const matrixTabWrap = document.getElementById('matrixTabWrap');
            const matrixTableHead = document.getElementById('matrixTableHead');
            const matrixTableBody = document.getElementById('matrixTableBody');
            const matrixTabButtons = document.querySelectorAll('.matrix-tab-btn');
            let currentMethod = 'offset';
            let currentMatrixData = null;
            let currentMatrixSide = 'one_side';

            // Modal Preview
            const modal = document.getElementById('modalOffsetPreview');
            const btnOpenModal = document.getElementById('btnOpenModal');
            const btnCloseModal = document.getElementById('btnCloseModal');
            const btnCancelModal = document.getElementById('btnCancelModal');
            const btnPrintEstimation = document.getElementById('btnPrintEstimation');
            let lastCalculatedData = null;

            // Helper Format Rupiah
            function formatRupiah(number) {
                if (number === null || number === undefined || isNaN(number)) return 'Rp 0';
                return 'Rp ' + Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            // Switch Method (Offset vs Digital)
            function setMethod(method) {
                currentMethod = method;
                if (method === 'digital') {
                    cardMethodDigital.classList.add('active');
                    cardMethodOffset.classList.remove('active');
                    document.querySelector('input[name="production_method"][value="digital"]').checked = true;

                    offsetQtyWrapper.style.display = 'none';
                    digitalQtyWrapper.style.display = 'block';

                    liveRow3Wrap.style.display = 'none';
                    liveRow4Wrap.style.display = 'none';
                    liveRow1Title.textContent = '1. Ongkos Cetak Lembar A3+';
                    liveRow2Title.textContent = '2. Ongkos Potong Digital';
                    lblUnitSelling1.textContent = 'Paket:';

                    matrixHeaderTitle.textContent = 'TABEL PAKET BROSUR DIGITAL A3+ (100 & 500 LEMBAR)';
                    matrixHeaderSub.textContent =
                        'Perbandingan paket cetak cepat digital A3+ (1 Sisi & 2 Sisi) lengkap ongkos print & potong. Klik "Pilih Paket" untuk menerapkan.';
                    matrixTabWrap.style.display = 'none';

                    updateDigitalSheetsNotice();
                } else {
                    cardMethodOffset.classList.add('active');
                    cardMethodDigital.classList.remove('active');
                    document.querySelector('input[name="production_method"][value="offset"]').checked = true;

                    offsetQtyWrapper.style.display = 'block';
                    digitalQtyWrapper.style.display = 'none';

                    liveRow3Wrap.style.display = 'flex';
                    liveRow4Wrap.style.display = 'flex';
                    liveRow1Title.textContent = '1. Kertas Plano';
                    liveRow2Title.textContent = '2. Plat CTP Mesin';
                    lblUnitSelling1.textContent = 'Per Rim:';

                    matrixHeaderTitle.textContent = 'SIMULASI PRICELIST BERTINGKAT MESIN OFFSET (1 S/D 10 RIM)';
                    matrixHeaderSub.textContent =
                        'Perbandingan lengkap HPP dan harga jual rekomendasi per rim & per lembar. Klik tombol "Pilih" untuk menerapkan kuantitas tersebut ke form.';
                    matrixTabWrap.style.display = 'flex';

                    updateQtyPieces();
                }
                executeCalculation();
            }

            cardMethodOffset.addEventListener('click', function() {
                setMethod('offset');
            });

            cardMethodDigital.addEventListener('click', function() {
                setMethod('digital');
            });

            // Quick package buttons for Digital
            pkgQuickButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    pkgQuickButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    const qty = parseInt(this.getAttribute('data-pkg-qty')) || 100;
                    digitalPiecesInput.value = qty;
                    updateDigitalSheetsNotice();
                    executeCalculation();
                });
            });

            // Update notice digital
            function updateDigitalSheetsNotice() {
                const opt = productSelect.options[productSelect.selectedIndex];
                const isA4 = opt && opt.text.toLowerCase().includes('a4') && !opt.text.toLowerCase().includes(
                    '1/3') && !opt.text.toLowerCase().includes('dl');
                const up = isA4 ? 2 : 4;
                const pieces = parseInt(digitalPiecesInput.value) || 100;
                const sheets = Math.ceil(pieces / up);
                digitalSheetsNotice.textContent =
                    `Kebutuhan Cetak: ${sheets} Lembar A3+ (${up}-up) · Ongkos Potong Rp 35.000`;
            }

            // Update badge info produk saat opsi berubah
            function updateProductBadges() {
                const opt = productSelect.options[productSelect.selectedIndex];
                if (!opt || !opt.value) {
                    specFinalSize.textContent = '-';
                    specSheetPerRim.textContent = '-';
                    specPlanoYield.textContent = '-';
                    specPressSize.textContent = '-';
                    return;
                }
                specFinalSize.textContent = opt.getAttribute('data-final-size') || '-';
                specSheetPerRim.textContent = opt.getAttribute('data-sheet-per-rim') || '-';
                specPlanoYield.textContent = opt.getAttribute('data-plano-yield') || '-';
                specPressSize.textContent = opt.getAttribute('data-press-size') || '-';

                updateDigitalSheetsNotice();
                updateExcelEquivBadge();
            }

            // Update badge info kertas plano saat opsi berubah
            function updatePlanoBadges() {
                const opt = planoSelect.options[planoSelect.selectedIndex];
                if (!opt || !opt.value) {
                    specGramature.textContent = '-';
                    specPlanoDim.textContent = '-';
                    specPlanoPrice.textContent = '-';
                    updateExcelEquivBadge();
                    return;
                }
                specGramature.textContent = opt.getAttribute('data-gramature') || '-';
                specPlanoDim.textContent = opt.getAttribute('data-size') || '-';
                specPlanoPrice.textContent = formatRupiah(parseFloat(opt.getAttribute('data-price') || 0));

                updateExcelEquivBadge();
            }

            // Update badge ekuivalen item Excel
            function updateExcelEquivBadge() {
                if (!specExcelEquiv) return;
                const prodOpt = productSelect.options[productSelect.selectedIndex];
                const matOpt = planoSelect.options[planoSelect.selectedIndex];
                if (!prodOpt || !matOpt || !prodOpt.value || !matOpt.value) {
                    specExcelEquiv.textContent = '-';
                    return;
                }
                const prodText = prodOpt.text.toLowerCase();
                const matText = matOpt.text.toLowerCase();

                let size = 'A5';
                if (prodText.includes('a4') && !prodText.includes('1/3') && !prodText.includes('dl')) {
                    size = 'A4';
                }

                let matName = 'AP 120gsm';
                if (matText.includes('150')) {
                    matName = 'AP 150gsm';
                } else if (matText.includes('hvs') || matText.includes('100')) {
                    matName = 'HVS 100gsm';
                } else if (matText.includes('120')) {
                    matName = 'AP 120gsm';
                }

                specExcelEquiv.textContent = `Brosur ${matName} ${size}`;
            }

            // Update notifikasi jumlah pcs offset
            function updateQtyPieces() {
                const rims = parseFloat(rimsInput.value) || 0;
                const pcs = Math.round(rims * 500);
                qtyPiecesNotice.textContent = '= ' + pcs.toLocaleString('id-ID') + ' lembar brosur jadi';
            }

            // Render Matriks Tabel Offset
            function renderOffsetMatrixTable(side) {
                matrixTableHead.innerHTML = `
                    <tr>
                        <th>Qty (Rim)</th>
                        <th>Total Lembar Jadi</th>
                        <th>Total HPP</th>
                        <th>HPP / Rim</th>
                        <th class="col-highlight">Harga Jual / Rim</th>
                        <th class="col-highlight">Harga Jual / Pcs</th>
                        <th class="col-highlight">Total Harga Jual</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                `;

                if (!currentMatrixData || !currentMatrixData[side]) {
                    matrixTableBody.innerHTML =
                        `<tr><td colspan="8" style="text-align: center; padding: 24px; color: var(--t-muted);">Data matriks belum tersedia. Silakan klik Hitung Estimasi.</td></tr>`;
                    return;
                }

                const list = currentMatrixData[side];
                let rowsHtml = '';
                const currentRims = parseFloat(rimsInput.value) || 1;

                Object.keys(list).forEach(key => {
                    const row = list[key];
                    const isSelected = (parseFloat(row.rims) === currentRims);
                    const selectedClass = isSelected ? 'is-selected' : '';

                    rowsHtml += `
                        <tr class="${selectedClass}">
                            <td style="font-weight: 700;">
                                ${row.rims} Rim
                                ${isSelected ? '<span class="badge-selected">Dipilih</span>' : ''}
                            </td>
                            <td>${row.pcs.toLocaleString('id-ID')} pcs</td>
                            <td>${formatRupiah(row.total_hpp)}</td>
                            <td>${formatRupiah(row.hpp_per_rim)}</td>
                            <td class="col-highlight">${formatRupiah(row.selling_price_per_rim)}</td>
                            <td class="col-highlight">${formatRupiah(row.selling_price_per_pcs)}</td>
                            <td class="col-highlight">${formatRupiah(row.total_selling_price)}</td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-select-qty" data-rims="${row.rims}">
                                    Pilih
                                </button>
                            </td>
                        </tr>
                    `;
                });

                matrixTableBody.innerHTML = rowsHtml;

                // Event listener untuk tombol 'Pilih'
                document.querySelectorAll('.btn-select-qty').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const rims = this.getAttribute('data-rims');
                        rimsInput.value = rims;
                        updateQtyPieces();
                        executeCalculation();
                    });
                });
            }

            // Render Matriks Tabel Digital
            function renderDigitalMatrixTable() {
                matrixTableHead.innerHTML = `
                    <tr>
                        <th>Paket Brosur</th>
                        <th>Kebutuhan Cetak</th>
                        <th>Ongkos Print</th>
                        <th>Ongkos Potong</th>
                        <th>Total Biaya (HPP)</th>
                        <th class="col-highlight">Harga Jual Paket</th>
                        <th class="col-highlight">Harga / Lembar</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                `;

                if (!currentMatrixData || !Array.isArray(currentMatrixData)) {
                    matrixTableBody.innerHTML =
                        `<tr><td colspan="8" style="text-align: center; padding: 24px; color: var(--t-muted);">Data paket belum tersedia.</td></tr>`;
                    return;
                }

                const currentPieces = parseInt(digitalPiecesInput.value) || 100;
                const currentSide = parseInt(document.querySelector('input[name="print_sides"]:checked')?.value ||
                    1);

                let rowsHtml = '';
                currentMatrixData.forEach(row => {
                    const isSelected = (row.pieces === currentPieces && row.print_sides === currentSide);
                    const selectedClass = isSelected ? 'is-selected' : '';

                    rowsHtml += `
                        <tr class="${selectedClass}">
                            <td style="font-weight: 700;">
                                ${row.label}
                                ${isSelected ? '<span class="badge-selected">Dipilih</span>' : ''}
                            </td>
                            <td>${row.a3_sheets} Lembar A3+</td>
                            <td>${formatRupiah(row.print_cost)}</td>
                            <td>${formatRupiah(row.cutting_cost)}</td>
                            <td>${formatRupiah(row.total_hpp)}</td>
                            <td class="col-highlight">${formatRupiah(row.total_selling_price)}</td>
                            <td class="col-highlight">${formatRupiah(row.selling_price_per_pcs)} / pcs</td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-select-pkg" data-pieces="${row.pieces}" data-sides="${row.print_sides}">
                                    Pilih Paket
                                </button>
                            </td>
                        </tr>
                    `;
                });

                matrixTableBody.innerHTML = rowsHtml;

                // Event listener tombol 'Pilih Paket'
                document.querySelectorAll('.btn-select-pkg').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const p = parseInt(this.getAttribute('data-pieces'));
                        const s = this.getAttribute('data-sides');
                        digitalPiecesInput.value = p;
                        document.querySelector(`input[name="print_sides"][value="${s}"]`).checked =
                            true;
                        pkgQuickButtons.forEach(b => {
                            b.classList.toggle('active', parseInt(b.getAttribute(
                                'data-pkg-qty')) === p);
                        });
                        updateDigitalSheetsNotice();
                        executeCalculation();
                    });
                });
            }

            // Fungsi Eksekusi Perhitungan via AJAX
            async function executeCalculation() {
                const productId = productSelect.value;
                const planoMaterialId = planoSelect.value;
                const rims = rimsInput.value;
                const digitalPieces = digitalPiecesInput.value;
                const printSides = document.querySelector('input[name="print_sides"]:checked')?.value || 1;
                const priceType = priceTypeSelect.value;
                const locationId = document.getElementById('location_id')?.value || '';
                const customerName = document.getElementById('customer_name')?.value || '';
                const customerPhone = document.getElementById('customer_phone')?.value || '';
                const leadTime = document.getElementById('lead_time')?.value || 3;
                const notes = document.getElementById('notes')?.value || '';

                // Diskon input
                const discountType = document.querySelector('input[name="discount_type"]:checked')?.value ||
                    'none';
                let discountValue = discountValueInput ? discountValueInput.value.trim() : '';
                if (discountType === 'nominal') {
                    discountValue = discountValue.replace(/\./g, '');
                }

                if (!productId || !planoMaterialId) {
                    return;
                }

                btnCalculate.disabled = true;
                btnCalculate.innerHTML =
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menghitung...';

                try {
                    const response = await fetch("{{ route('admin_calculate_offset') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            production_method: currentMethod,
                            product_id: productId,
                            plano_material_id: planoMaterialId,
                            rims: rims,
                            digital_pieces: digitalPieces,
                            print_sides: printSides,
                            price_type: priceType,
                            location_id: locationId,
                            customer_name: customerName,
                            customer_phone: customerPhone,
                            lead_time: leadTime,
                            notes: notes,
                            discount_type: discountType,
                            discount_value: discountValue
                        })
                    });

                    const res = await response.json();

                    if (!res.success) {
                        alert('Gagal menghitung: ' + (res.message || 'Terjadi kesalahan sistem.'));
                        return;
                    }

                    const data = res.data;
                    lastCalculatedData = data;
                    currentMatrixData = res.matrix;

                    // Update Live Discount Box
                    if (data.totals && data.totals.discount_amount && data.totals.discount_amount > 0) {
                        liveDiscountWrap.style.display = 'block';
                        liveSubtotalPrice.textContent = formatRupiah(data.totals.subtotal);
                        const desc = data.totals.discount_type === 'percentage' ?
                            (data.totals.discount_value + '%') :
                            'Nominal';
                        liveDiscountDesc.textContent = desc;
                        liveDiscountAmount.textContent = '- ' + formatRupiah(data.totals.discount_amount);
                    } else {
                        liveDiscountWrap.style.display = 'none';
                    }

                    // Mode Digital Print A3+
                    if (res.method === 'digital') {
                        liveTotalSellingPrice.textContent = formatRupiah(data.totals.total_selling_price);
                        liveSellingPricePerRim.textContent = data.specs.pieces.toLocaleString('id-ID') +
                            ' Lembar';
                        liveSellingPricePerPcs.textContent = formatRupiah(data.totals.selling_price_per_pcs);
                        priceTypeBadge.textContent = priceType === 'division' ? 'Harga Divisi' : 'Harga Umum';

                        liveTotalHppBadge.textContent = 'HPP: ' + formatRupiah(data.totals.total_hpp);
                        liveTotalHpp.textContent = formatRupiah(data.totals.total_hpp);
                        liveHppPerRim.textContent = formatRupiah(data.totals.total_hpp);
                        liveHppPerPcs.textContent = formatRupiah(data.totals.hpp_per_pcs);

                        livePaperCost.textContent = formatRupiah(data.breakdown.print_cost);
                        livePaperDetail.textContent = data.specs.a3_sheets_needed + ' lbr A3+ @ ' +
                            formatRupiah(data.specs.rate_per_a3);

                        livePlateCost.textContent = formatRupiah(data.breakdown.cutting_cost);
                        livePlateDetail.textContent = 'Ongkos Potong Digital Standar (Flat)';

                        // Smart Comparison dengan Offset
                        if (data.offset_compare) {
                            smartComparisonBox.style.display = 'block';
                            smartComparisonText.innerHTML = `
                                <strong>Tips Rekomendasi CS:</strong> Di kuantitas 500 Lembar (1 Rim), harga Cetak Offset adalah <strong>${formatRupiah(data.offset_compare.total_selling_price)}</strong> (${formatRupiah(data.offset_compare.selling_price_per_pcs)}/pcs). Jika pelanggan mencetak di atas 1 rim, alihkan ke <strong>Cetak Offset</strong> untuk margin dan harga yang jauh lebih kompetitif!
                            `;
                        } else {
                            smartComparisonBox.style.display = 'none';
                        }

                        renderDigitalMatrixTable();
                    } else {
                        // Mode Cetak Mesin Offset
                        liveTotalSellingPrice.textContent = formatRupiah(data.totals.total_selling_price);
                        liveSellingPricePerRim.textContent = formatRupiah(data.totals.selling_price_per_rim);
                        liveSellingPricePerPcs.textContent = formatRupiah(data.totals.selling_price_per_pcs);
                        priceTypeBadge.textContent = priceType === 'division' ? 'Harga Divisi' : 'Harga Umum';

                        liveTotalHppBadge.textContent = 'HPP: ' + formatRupiah(data.totals.total_hpp);
                        liveTotalHpp.textContent = formatRupiah(data.totals.total_hpp);
                        liveHppPerRim.textContent = formatRupiah(data.totals.hpp_per_rim);
                        liveHppPerPcs.textContent = formatRupiah(data.totals.hpp_per_pcs);

                        livePaperCost.textContent = formatRupiah(data.breakdown.paper_cost);
                        livePaperDetail.textContent = data.breakdown.total_plano_needed + ' plano (' + data
                            .breakdown.plano_per_rim + ' pokok/rim + ' + data.breakdown.plano_inschiet +
                            ' inschiet)';

                        livePlateCost.textContent = formatRupiah(data.breakdown.plate_cost);
                        livePlateDetail.textContent = data.breakdown.plate_count + ' Plat CMYK @ ' +
                            formatRupiah(
                                data.breakdown.plate_price_unit);

                        livePrintCost.textContent = formatRupiah(data.breakdown.print_cost);
                        livePrintDetail.textContent = data.breakdown.machine_sheets_run.toLocaleString(
                            'id-ID') +
                            ' lbr mesin (min ' + formatRupiah(data.breakdown.min_print_cost) + ')';

                        liveCuttingCost.textContent = formatRupiah(data.breakdown.cutting_cost);
                        liveCuttingDetail.textContent = formatRupiah(data.breakdown.cut_cost_per_rim) +
                        ' / rim';

                        // Smart Comparison dengan Digital
                        if (data.digital_compare) {
                            smartComparisonBox.style.display = 'block';
                            const diff = data.digital_compare.total_selling_price - data.totals
                                .total_selling_price;
                            smartComparisonText.innerHTML = `
                                <strong>Tips Rekomendasi CS:</strong> Untuk 1 Rim (500 Lembar), Digital Print adalah <strong>${formatRupiah(data.digital_compare.total_selling_price)}</strong>. Dengan Mesin Offset <strong>${diff > 0 ? 'lebih hemat ' + formatRupiah(diff) : 'sangat optimal'}</strong>, dan semakin banyak rim dicetak harga per rim-nya semakin murah!
                            `;
                        } else {
                            smartComparisonBox.style.display = 'none';
                        }

                        renderOffsetMatrixTable(currentMatrixSide);
                    }

                } catch (err) {
                    console.error('Calculation Error:', err);
                } finally {
                    btnCalculate.disabled = false;
                    btnCalculate.innerHTML = '<i class="bi bi-calculator"></i> <span>Hitung Estimasi</span>';
                }
            }

            // Tab Switcher untuk Matriks Harga Offset
            matrixTabButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    matrixTabButtons.forEach(b => b.classList.remove('is-active'));
                    this.classList.add('is-active');

                    currentMatrixSide = this.getAttribute('data-side');
                    renderOffsetMatrixTable(currentMatrixSide);
                });
            });

            // Modal Preview Event Listeners
            function populateModal() {
                if (!lastCalculatedData) return;
                const d = lastCalculatedData;

                const custName = (document.getElementById('customer_name')?.value || '').trim() || (d.customer && d
                    .customer.name !== '-' ? d.customer.name : '') || 'Pelanggan Umum';
                const custPhone = (document.getElementById('customer_phone')?.value || '').trim() || (d.customer &&
                    d.customer.phone !== '-' ? d.customer.phone : '') || '-';
                const leadTimeVal = (document.getElementById('lead_time')?.value || '').trim();
                const leadTimeStr = leadTimeVal ? (leadTimeVal + ' hari kerja') : (d.customer?.lead_time ||
                    '3 hari kerja');
                const locSelect = document.getElementById('location_id');
                const custLocation = locSelect && locSelect.selectedIndex >= 0 && locSelect.options[locSelect
                    .selectedIndex].text ? locSelect.options[locSelect.selectedIndex].text : (d.customer
                    ?.location || '-');
                const notesVal = (document.getElementById('notes')?.value || '').trim() || (d.customer?.notes ||
                '');

                document.getElementById('modalLeadTime').textContent = leadTimeStr;
                document.getElementById('modalCustomerName').textContent = custName;
                document.getElementById('modalCustomerPhone').textContent = custPhone;
                document.getElementById('modalCustomerLocation').textContent = custLocation;

                const notesWrap = document.getElementById('modalNotesWrap');
                const notesText = document.getElementById('modalCustomerNotes');
                if (notesWrap && notesText) {
                    if (notesVal) {
                        notesWrap.style.display = 'block';
                        notesText.textContent = notesVal;
                    } else {
                        notesWrap.style.display = 'none';
                    }
                }

                if (currentMethod === 'digital' || d.mode === 'digital' || d.method === 'digital') {
                    document.getElementById('modalMainTitle').textContent =
                        'Surat Penawaran Cetak Brosur (Digital Print A3+)';
                    document.getElementById('modalSystemSub').textContent =
                        'Printing Management System · Estimator Digital Print A3+';

                    document.getElementById('modalProductName').textContent = d.product.name + ' (' + d.product
                        .size_name + ')';
                    document.getElementById('modalMaterialName').textContent = d.material.name;
                    document.getElementById('modalPrintSide').textContent = d.specs.print_sides === 2 ?
                        '2 Sisi (Bolak-Balik)' : '1 Sisi (1 Muka)';
                    document.getElementById('modalQty').textContent = d.specs.pieces.toLocaleString('id-ID') +
                        ' Lembar (' + d.specs.a3_sheets_needed + ' lbr A3+)';

                    document.getElementById('modalItemTitle').textContent = 'Brosur ' + d.material.name + ' (' + d
                        .product.name + ')';
                    document.getElementById('modalItemDesc').textContent = 'Digital Print A3+ Full Color · ' + (d
                            .specs.print_sides === 2 ? '2 Sisi Bolak-Balik' : '1 Sisi') + ' · ' + d.specs
                        .a3_sheets_needed + ' lbr A3+ · Potong Standar';
                    document.getElementById('modalItemQty').textContent = d.specs.pieces.toLocaleString('id-ID') +
                        ' Lembar';
                    document.getElementById('modalItemUnitPrice').textContent = formatRupiah(d.totals
                        .selling_price_per_pcs) + ' / pcs';
                    document.getElementById('modalItemSubtotal').textContent = formatRupiah(d.totals.subtotal || d
                        .totals.total_selling_price);
                    document.getElementById('modalGrandTotal').textContent = formatRupiah(d.totals
                        .total_selling_price);

                    document.getElementById('modalFootTerms').textContent =
                        '* Penawaran ini berlaku selama 14 hari sejak tanggal diterbitkan. Harga sudah termasuk cetak mesin Digital Print A3+ Full Color dan ongkos potong standar.';
                } else {
                    document.getElementById('modalMainTitle').textContent =
                        'Surat Penawaran Cetak Brosur (Mesin Offset SM 52)';
                    document.getElementById('modalSystemSub').textContent =
                        'Printing Management System · Estimator Cetak Mesin Offset';

                    document.getElementById('modalProductName').textContent = d.product.name + ' (' + d.product
                        .final_length + ' x ' + d.product.final_width + ' cm)';
                    document.getElementById('modalMaterialName').textContent = d.material.name;
                    document.getElementById('modalPrintSide').textContent = d.input.print_sides === 2 ?
                        '2 Sisi (2 Muka Balikan)' : '1 Sisi (1 Muka)';
                    document.getElementById('modalQty').textContent = d.input.rims + ' Rim (' + d.input.total_pcs
                        .toLocaleString('id-ID') + ' pcs)';

                    document.getElementById('modalItemTitle').textContent = 'Brosur ' + d.material.name + ' (' + d
                        .product.name + ')';
                    document.getElementById('modalItemDesc').textContent = 'Offset SM 52 4 Warna (CMYK) · ' + (d
                            .input
                            .print_sides === 2 ? '2 Muka' : '1 Muka') +
                        ' · Plat CTP & Kertas Plano · Potong Standar';
                    document.getElementById('modalItemQty').textContent = d.input.rims + ' Rim';
                    document.getElementById('modalItemUnitPrice').textContent = formatRupiah(d.totals
                        .selling_price_per_rim) + ' / rim';
                    document.getElementById('modalItemSubtotal').textContent = formatRupiah(d.totals
                        .subtotal || d.totals.total_selling_price);
                    document.getElementById('modalGrandTotal').textContent = formatRupiah(d.totals
                        .total_selling_price);

                    document.getElementById('modalFootTerms').textContent =
                        '* Penawaran ini berlaku selama 14 hari sejak tanggal diterbitkan. Harga sudah termasuk plat CTP 4 warna, kertas plano, cetak mesin offset SM 52, dan ongkos potong standar.';
                }

                // Tampilkan Rincian Subtotal & Diskon jika ada diskon
                const subtotalRow = document.getElementById('modalSubtotalRow');
                const subtotalAmount = document.getElementById('modalSubtotalAmount');
                const discountRow = document.getElementById('modalDiscountRow');
                const discountLabel = document.getElementById('modalDiscountLabel');
                const discountAmount = document.getElementById('modalDiscountAmount');

                if (d.totals && d.totals.discount_amount && d.totals.discount_amount > 0) {
                    if (subtotalRow) subtotalRow.style.display = 'table-row';
                    if (subtotalAmount) subtotalAmount.textContent = formatRupiah(d.totals.subtotal);
                    if (discountRow) discountRow.style.display = 'table-row';
                    const desc = d.totals.discount_type === 'percentage' ?
                        (d.totals.discount_value + '%') :
                        'Nominal';
                    if (discountLabel) discountLabel.textContent = desc;
                    if (discountAmount) discountAmount.textContent = '- ' + formatRupiah(d.totals.discount_amount);
                } else {
                    if (subtotalRow) subtotalRow.style.display = 'none';
                    if (discountRow) discountRow.style.display = 'none';
                }
            }

            function openModal() {
                populateModal();
                modal.classList.add('is-open');
                modal.style.display = 'flex';
            }

            function closeModal() {
                modal.classList.remove('is-open');
                modal.style.display = 'none';
            }

            btnOpenModal.addEventListener('click', async function() {
                if (!lastCalculatedData) {
                    await executeCalculation();
                }
                openModal();
            });

            btnCloseModal.addEventListener('click', closeModal);
            btnCancelModal.addEventListener('click', closeModal);

            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });

            btnPrintEstimation.addEventListener('click', function() {
                window.print();
            });

            // Event Listeners Input
            productSelect.addEventListener('change', function() {
                updateProductBadges();
                executeCalculation();
            });

            planoSelect.addEventListener('change', function() {
                updatePlanoBadges();
                executeCalculation();
            });

            rimsInput.addEventListener('input', function() {
                updateQtyPieces();
            });

            rimsInput.addEventListener('change', function() {
                updateQtyPieces();
                executeCalculation();
            });

            digitalPiecesInput.addEventListener('input', function() {
                updateDigitalSheetsNotice();
            });

            digitalPiecesInput.addEventListener('change', function() {
                updateDigitalSheetsNotice();
                executeCalculation();
            });

            priceTypeSelect.addEventListener('change', function() {
                executeCalculation();
            });

            document.querySelectorAll('input[name="print_sides"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    executeCalculation();
                });
            });

            // Event Listeners Diskon
            let calcDebounceTimer = null;

            function debounceCalculation(delay = 350) {
                clearTimeout(calcDebounceTimer);
                calcDebounceTimer = setTimeout(() => {
                    executeCalculation();
                }, delay);
            }

            discountTypeInputs.forEach(radio => {
                radio.addEventListener('change', function() {
                    const type = this.value;

                    if (type === 'none') {
                        discountValueField.style.display = 'none';
                        discountValueInput.disabled = true;
                        discountValueInput.value = '';
                        executeCalculation();
                        return;
                    }

                    discountValueField.style.display = 'block';
                    discountValueInput.disabled = false;

                    if (type === 'percentage') {
                        discountValueLabel.textContent = 'Persentase Diskon';
                        discountValueInput.placeholder = 'Contoh: 10';
                        discountSuffix.textContent = '%';
                        discountHelpText.textContent = 'Masukkan persentase diskon (1 - 100%).';
                    } else if (type === 'nominal') {
                        discountValueLabel.textContent = 'Nominal Diskon';
                        discountValueInput.placeholder = 'Contoh: 50.000';
                        discountSuffix.textContent = 'Rp';
                        discountHelpText.textContent = 'Masukkan nominal potongan diskon (Rp).';
                    }

                    discountValueInput.focus();
                    if (discountValueInput.value.trim() !== '') {
                        executeCalculation();
                    }
                });
            });

            discountValueInput.addEventListener('input', function() {
                const selectedType = document.querySelector('input[name="discount_type"]:checked')?.value;

                if (selectedType === 'percentage') {
                    let val = this.value.replace(/[^0-9.]/g, '');
                    const parts = val.split('.');
                    if (parts.length > 2) {
                        val = parts[0] + '.' + parts.slice(1).join('');
                    }
                    if (parseFloat(val) > 100) {
                        val = '100';
                    }
                    this.value = val;
                } else if (selectedType === 'nominal') {
                    const raw = this.value.replace(/\D/g, '');
                    this.value = raw ? new Intl.NumberFormat('id-ID', {
                        maximumFractionDigits: 0
                    }).format(Number(raw)) : '';
                }

                debounceCalculation(350);
            });

            form.addEventListener('reset', function() {
                setTimeout(() => {
                    if (discountValueField) discountValueField.style.display = 'none';
                    if (discountValueInput) {
                        discountValueInput.disabled = true;
                        discountValueInput.value = '';
                    }
                    if (liveDiscountWrap) liveDiscountWrap.style.display = 'none';
                    updateProductBadges();
                    updatePlanoBadges();
                    updateExcelEquivBadge();
                    updateQtyPieces();
                    executeCalculation();
                }, 50);
            });

            btnCalculate.addEventListener('click', function(e) {
                e.preventDefault();
                executeCalculation();
            });

            // Inisialisasi awal saat halaman dimuat
            updateProductBadges();
            updatePlanoBadges();
            updateExcelEquivBadge();
            updateQtyPieces();
            executeCalculation();

        });

        $(document).on('wheel', 'input[type="number"]', function() {
            this.blur();
        });
    </script>

    {{-- Styling Khusus Cetak Brosur (Dukungan Light & Dark Mode Penuh & Responsive) --}}
    <style>
        /* Responsive Grid System */
        .offset-estimator-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
            width: 100%;
        }

        .offset-form-col {
            min-width: 0;
            width: 100%;
        }

        .offset-summary-col {
            min-width: 0;
            width: 100%;
        }

        .offset-matrix-col {
            grid-column: 1 / -1;
            min-width: 0;
            width: 100%;
        }

        @media (min-width: 1024px) {
            .offset-estimator-grid {
                grid-template-columns: 1.25fr 1fr;
                gap: 24px;
                align-items: start;
            }

            .offset-sticky-card {
                position: sticky;
                top: 20px;
            }
        }

        /* Discount Options Styling & Theme Alignment */
        .discount-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }

        @media (max-width: 991px) {
            .discount-options {
                grid-template-columns: 1fr;
            }
        }

        .discount-option {
            position: relative;
            display: block;
            margin: 0;
            cursor: pointer;
        }

        .discount-option>input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .discount-option__content {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 64px;
            padding: 12px 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            background: var(--bg-card);
            color: var(--t-base);
            transition: all 0.2s ease;
        }

        .discount-option:hover .discount-option__content {
            border-color: var(--primary) !important;
            background: var(--bg-hover) !important;
        }

        .discount-option>input:checked+.discount-option__content {
            border-color: var(--primary) !important;
            background: var(--primary-soft) !important;
            color: var(--primary) !important;
            box-shadow: 0 0 0 1px var(--primary);
        }

        .discount-option__icon {
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background: var(--bg-muted);
            color: var(--t-base);
            font-size: 16px;
            transition: all 0.2s ease;
        }

        .discount-option>input:checked+.discount-option__content .discount-option__icon {
            background: var(--primary) !important;
            color: #fff !important;
        }

        .discount-option__text {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        .discount-option__text strong {
            font-size: 13px;
            font-weight: 700;
            color: inherit;
        }

        .discount-option__text small {
            font-size: 11px;
            line-height: 1.3;
            color: var(--t-muted);
        }

        .discount-option>input:checked+.discount-option__content .discount-option__text small {
            color: var(--primary);
            opacity: 0.85;
        }

        /* Radio Cards Responsive Wrap */
        .radio-cards-wrap {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        @media (max-width: 480px) {
            .radio-cards-wrap {
                flex-direction: column;
            }

            .radio-card {
                width: 100%;
            }
        }

        /* Matrix Table Wrapper */
        .matrix-table-wrap {
            padding: 16px 20px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Modal Printable Info Grid */
        .printable-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 18px;
            font-size: 12.5px;
        }

        @media (max-width: 640px) {
            .printable-info-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }

        /* Method Selector Cards */
        .method-card {
            border: 2px solid var(--border);
            border-radius: 10px;
            padding: 12px 16px;
            cursor: pointer;
            background: var(--bg-card);
            transition: all 0.2s ease;
            display: block;
        }

        .method-card:hover {
            border-color: var(--primary) !important;
            background: var(--bg-hover) !important;
        }

        .method-card.active {
            border-color: var(--primary) !important;
            background: var(--primary-soft) !important;
            box-shadow: 0 0 0 1px var(--primary);
        }

        /* Quick Package Buttons */
        .pkg-quick-btn {
            padding: 6px 14px;
            font-size: 12.5px;
            font-weight: 600;
            border: 1px solid var(--border);
            border-radius: 6px;
            background: var(--bg-muted);
            color: var(--t-base);
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .pkg-quick-btn:hover,
        .pkg-quick-btn.active {
            background: var(--primary) !important;
            color: #fff !important;
            border-color: var(--primary) !important;
        }

        .btn-select-pkg {
            padding: 5px 12px;
            font-size: 11.5px;
            border: 1px solid var(--primary);
            border-radius: 5px;
            background: var(--primary-soft);
            color: var(--primary);
            cursor: pointer;
            font-weight: 600;
            transition: all 0.15s ease;
        }

        .btn-select-pkg:hover {
            background: var(--primary);
            color: #fff;
        }

        /* Back Header Button */
        .btn-back-header {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 18px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--t-base);
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.15s ease;
            box-shadow: var(--shadow-sm);
        }

        .btn-back-header:hover {
            color: var(--primary);
            border-color: var(--primary);
            background: var(--primary-soft);
        }

        /* Snippet Badges */
        .badge-spec {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            border: 1px solid var(--border);
        }

        .badge-spec-primary {
            background: var(--primary-soft);
            color: var(--primary);
        }

        .badge-spec-success {
            background: var(--success-soft);
            color: var(--success);
        }

        .badge-spec-warning {
            background: var(--warning-soft);
            color: var(--warning);
        }

        .badge-spec-purple {
            background: var(--purple-soft);
            color: var(--purple);
        }

        /* Radio Card */
        .radio-card {
            flex: 1;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--bg-card);
            transition: all 0.2s ease;
        }

        .radio-card:hover {
            border-color: var(--primary) !important;
            background: var(--bg-hover) !important;
        }

        .radio-card:has(input:checked) {
            border-color: var(--primary) !important;
            background: var(--primary-soft) !important;
        }

        .radio-card-title {
            font-weight: 600;
            font-size: 13.5px;
            color: var(--t-base);
        }

        .radio-card-sub {
            color: var(--t-muted);
            font-size: 11.5px;
        }

        /* Form Action Bar */
        .form-action-bar {
            padding: 16px 20px;
            background: var(--bg-muted);
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 0 0 13px 13px;
        }

        .btn-secondary-custom {
            background: var(--bg-card);
            color: var(--t-base);
            border: 1px solid var(--border);
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-secondary-custom:hover {
            background: var(--bg-hover);
            color: var(--t-base);
            border-color: var(--primary);
        }

        /* Selling Price Box */
        .selling-price-box {
            background: var(--bg-muted);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 18px;
            text-align: center;
            margin-bottom: 20px;
        }

        .selling-price-box-label {
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--t-muted);
            letter-spacing: 0.5px;
        }

        .selling-price-box-val {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
            margin: 6px 0;
        }

        .selling-price-box-sub {
            display: flex;
            justify-content: center;
            gap: 16px;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed var(--border);
        }

        .sub-label {
            font-size: 11.5px;
            color: var(--t-muted);
        }

        .sub-val {
            font-size: 13.5px;
            color: var(--t-base);
            display: block;
        }

        .sub-divider {
            border-left: 1px solid var(--border);
            padding-left: 16px;
        }

        .rounding-pill {
            font-size: 11px;
            color: var(--success);
            background: var(--success-soft);
            padding: 3px 10px;
            border-radius: 4px;
            border: 1px solid var(--border);
            display: inline-block;
        }

        /* Breakdown Rows */
        .breakdown-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 9px 12px;
            background: var(--bg-muted);
            border-radius: 6px;
            border: 1px solid var(--border);
            transition: background 0.15s ease;
        }

        .breakdown-row:hover {
            background: var(--bg-hover);
        }

        .breakdown-row-title {
            font-weight: 600;
            color: var(--t-base);
        }

        .breakdown-row-sub {
            color: var(--t-muted);
            font-size: 11.5px;
        }

        .breakdown-row-cost {
            color: var(--t-base);
            font-weight: 700;
        }

        /* Total HPP Bar */
        .total-hpp-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 12px;
            background: var(--primary-soft);
            border-radius: 6px;
            border: 1.5px solid var(--primary-ring);
            margin-top: 4px;
        }

        .total-hpp-bar-title {
            color: var(--primary);
            font-weight: 700;
        }

        .total-hpp-bar-sub {
            font-size: 11px;
            color: var(--t-muted);
        }

        .total-hpp-bar-val {
            font-size: 14.5px;
            color: var(--primary);
            font-weight: 800;
        }

        /* Preview Estimation Button */
        .btn-preview-estimation {
            width: 100%;
            border: 1.5px solid var(--border);
            background: var(--bg-card);
            color: var(--t-base);
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-preview-estimation:hover {
            background: var(--bg-hover);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Matrix Tab Container */
        .matrix-tab-container {
            display: flex;
            gap: 6px;
            background: var(--bg-muted);
            padding: 4px;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .matrix-tab-btn {
            padding: 6px 14px;
            font-size: 12.5px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            background: transparent;
            color: var(--t-muted);
            transition: all 0.15s ease;
        }

        .matrix-tab-btn.is-active {
            background: var(--bg-card);
            color: var(--primary);
            box-shadow: var(--shadow-sm);
        }

        /* Matrix Table */
        .matrix-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .matrix-table thead tr {
            background: var(--bg-muted);
            border-bottom: 2px solid var(--border);
            text-align: left;
        }

        .matrix-table th {
            padding: 12px 14px;
            font-weight: 700;
            color: var(--t-base);
        }

        .matrix-table th.col-highlight {
            color: var(--primary);
            background: var(--primary-soft);
        }

        .matrix-table td {
            padding: 10px 14px;
            color: var(--t-base);
            border-bottom: 1px solid var(--border);
        }

        .matrix-table td.col-highlight {
            font-weight: 700;
            color: var(--primary);
            background: var(--primary-soft);
        }

        .matrix-table tr.is-selected {
            background: var(--primary-soft) !important;
        }

        .badge-selected {
            font-size: 10px;
            background: var(--primary);
            color: #fff;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 6px;
        }

        .btn-select-qty {
            padding: 4px 10px;
            font-size: 11.5px;
            border: 1px solid var(--border);
            border-radius: 5px;
            background: var(--bg-card);
            color: var(--t-base);
            cursor: pointer;
            font-weight: 600;
            transition: all 0.15s ease;
        }

        .btn-select-qty:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
        }

        /* Modal Custom Styles */
        #modalOffsetPreview.modal-overlay {
            position: fixed !important;
            inset: 0 !important;
            display: none;
            align-items: center !important;
            justify-content: center !important;
            background: rgba(11, 17, 32, 0.75) !important;
            backdrop-filter: blur(4px) !important;
            z-index: 99999 !important;
            padding: 20px !important;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease !important;
        }

        #modalOffsetPreview.modal-overlay.is-open {
            display: flex !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .modal-content-custom {
            background: var(--bg-card);
            color: var(--t-base);
            border: 1px solid var(--border);
            border-radius: 12px;
            max-width: 750px;
            width: 100%;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
        }

        .modal-head-custom {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title-custom {
            margin: 0;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--t-base);
        }

        .modal-close-btn {
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 20px;
            color: var(--t-muted);
            transition: color 0.15s ease;
        }

        .modal-close-btn:hover {
            color: var(--t-base);
        }

        .printable-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--border);
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .printable-company-name {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--t-base);
        }

        .printable-info-box {
            background: var(--bg-muted);
            padding: 12px;
            border-radius: 6px;
            border: 1px solid var(--border);
            color: var(--t-base);
        }

        .printable-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .printable-table thead tr {
            background: var(--bg-muted);
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            color: var(--t-base);
        }

        .printable-table tbody tr {
            border-bottom: 1px solid var(--border);
            color: var(--t-base);
        }

        .printable-table tfoot tr {
            border-top: 2px solid var(--border);
            font-size: 14px;
            color: var(--t-base);
        }

        .modal-foot-custom {
            padding: 14px 20px;
            background: var(--bg-muted);
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-radius: 0 0 12px 12px;
        }

        /* Mobile & Tablet Responsive Enhancements */
        @media (max-width: 576px) {
            .form-action-bar {
                flex-direction: column-reverse;
                align-items: stretch;
                gap: 12px;
            }

            .form-action-bar>* {
                width: 100%;
                justify-content: center;
            }

            .form-action-bar div {
                flex-direction: column;
                width: 100%;
            }

            .form-action-bar button,
            .form-action-bar a {
                width: 100%;
                justify-content: center;
            }

            .selling-price-box-sub {
                flex-direction: column;
                gap: 8px;
            }

            .sub-divider {
                border-left: none !important;
                border-top: 1px dashed var(--border);
                padding-left: 0 !important;
                padding-top: 8px;
                width: 100%;
            }

            .selling-price-box-val {
                font-size: 1.65rem;
            }

            .modal-content-custom {
                max-height: 96vh;
                border-radius: 8px;
            }

            #printableEstimationArea {
                padding: 14px !important;
            }

            .printable-header {
                flex-direction: column;
                gap: 8px;
            }

            .printable-table th,
            .printable-table td {
                padding: 6px 8px !important;
                font-size: 12px;
            }

            .modal-foot-custom {
                flex-direction: column;
            }

            .modal-foot-custom button {
                width: 100%;
                justify-content: center;
            }
        }

        /* Media Print Rules */
        @media print {
            body {
                background: #fff !important;
                color: #000 !important;
            }

            .shell,
            .hero,
            nav,
            header,
            footer,
            aside {
                display: none !important;
            }

            #modalOffsetPreview.modal-overlay {
                display: block !important;
                position: static !important;
                background: transparent !important;
                padding: 0 !important;
                opacity: 1 !important;
                visibility: visible !important;
                backdrop-filter: none !important;
            }

            #modalOffsetPreview .modal-head-custom,
            #modalOffsetPreview .modal-foot-custom {
                display: none !important;
            }

            #modalOffsetPreview .modal-content-custom {
                box-shadow: none !important;
                border: none !important;
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                color: #000 !important;
            }

            #printableEstimationArea {
                display: block !important;
                padding: 0 !important;
                margin: 0 !important;
                color: #000 !important;
                background: #fff !important;
            }

            #printableEstimationArea * {
                color: #000 !important;
                background: transparent !important;
            }

            .printable-info-box {
                border: 1px solid #d1d5db !important;
                background: #f9fafb !important;
            }

            .printable-table,
            .printable-table th,
            .printable-table td {
                border: 1px solid #d1d5db !important;
            }

            .printable-table thead tr {
                background: #f3f4f6 !important;
            }
        }
    </style>
@endsection

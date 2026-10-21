@extends('admin_master')

@section('contents')
    <div class="shell">

        <div data-shell-sidebar></div>

        <div class="main">

            <div data-shell-topbar></div>

            <main class="content">

                {{-- HERO --}}
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">
                            ESTIMASI
                        </span>
                        <p class="hero-sub">
                            Buat estimasi biaya produksi untuk kebutuhan cetak menggunakan mesin Digital Print A3+.
                        </p>
                    </div>
                </section>

                <div class="grid">
                    <section class="col-12 card">

                        {{-- CARD HEADER --}}
                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">
                                    ESTIMASI DIGITAL PRINT A3+
                                </span>
                            </div>
                        </div>

                        {{-- FORM ESTIMASI A3+ --}}
                        <form action="{{ route('admin_store_a3plus') }}" method="POST" id="a3PlusEstimationForm">
                            @csrf

                            {{-- =========================================================
                                1. INFORMASI ESTIMASI
                            ========================================================== --}}
                            <div class="form-section">
                                <div class="form-section-head">
                                    <span class="eyebrow">
                                        INFORMASI ESTIMASI
                                    </span>
                                </div>

                                <div class="form-grid">
                                    {{-- Lokasi --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="location_id">
                                            Lokasi
                                            <span class="req">*</span>
                                        </label>

                                        <select id="location_id" name="location_id" class="select select2" required>
                                            <option value="">Pilih Lokasi</option>
                                            @foreach ($locations ?? [] as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- Vendor (Opsional / Internal) --}}
                                    {{-- <div class="field" id="vendorField" style="margin-bottom: 20px;">
                                        <label class="field-label" for="vendor_id">
                                            Vendor / In-House
                                        </label>

                                        <select id="vendor_id" name="vendor_id" class="select select2">
                                            <option value="">Produksi Internal (In-House)</option>
                                            @foreach ($vendors ?? [] as $vendor)
                                                <option value="{{ $vendor->id }}">
                                                    {{ $vendor->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div> --}}

                                    {{-- Pilihan Harga / Pricing Profile --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="price_type">
                                            Pilihan Harga
                                            <span class="req">*</span>
                                        </label>

                                        <select id="price_type" name="price_type" class="select select2" required>
                                            <option value="general" selected>Harga Umum</option>
                                            <option value="division">Harga Divisi</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- =========================================================
                                2. MODE PRODUKSI & SPESIFIKASI CETAK
                            ========================================================== --}}
                            <div class="form-section">
                                <div class="form-section-head">
                                    <span class="eyebrow">
                                        SPESIFIKASI CETAK A3+
                                    </span>
                                </div>

                                <div class="form-grid">
                                    {{-- Kategori --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="category_id">
                                            Kategori
                                            <span class="req">*</span>
                                        </label>
                                        <select id="category_id" name="category_id" class="select select2" required>
                                            <option value="">Pilih Kategori</option>
                                            @foreach ($categories ?? [] as $category)
                                                <option value="{{ $category->id }}"
                                                    data-name="{{ strtolower(trim($category->name)) }}"
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="field-help" id="categoryHelp">Pilih kategori untuk memfilter material
                                            atau produk.</small>
                                    </div>

                                    {{-- Warna Cetak --}}
                                    <div class="field" id="printColorField" style="margin-bottom: 20px;">
                                        <label class="field-label" for="print_color">
                                            Warna Cetak
                                            <span class="req">*</span>
                                        </label>

                                        <select id="print_color" name="print_color" class="select select2" required>
                                            <option value="fc" selected>Full Color (FC)</option>
                                            <option value="bw">Hitam Putih (B/W)</option>
                                        </select>
                                        <small class="field-help">Pilih mode warna cetak.</small>
                                    </div>
                                </div>

                                <div class="form-grid">
                                    {{-- Material A3+ / Komponen Display --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="material_id">
                                            <span id="materialLabelText">Material Kertas / Stiker</span>
                                            <span class="req">*</span>
                                        </label>

                                        <select name="material_id" id="material_id" class="select select2" required>
                                            <option value="">Pilih Lokasi &amp; Kategori terlebih dahulu</option>
                                        </select>
                                        <small class="field-help" id="materialHelp">
                                            Material akan ditampilkan otomatis berdasarkan Lokasi, Kategori, dan Warna
                                            Cetak.
                                        </small>

                                        {{-- Info Ringkas HPP Material dari Master --}}
                                        <div id="materialMasterInfo"
                                            style="display: none; font-size: 11.5px; color: #3b82f6; margin-top: 6px; padding: 6px 10px;  background: rgba(59, 130, 246, 0.14); border-radius: 6px;">
                                            <span>Paper Cost: <strong id="infoPaperCost">Rp 0</strong></span> &middot;
                                            <span>Biaya 1 Sisi: <strong id="infoSingleCost">Rp 0</strong></span> &middot;
                                            <span>Biaya 2 Sisi: <strong id="infoDoubleCost">Rp 0</strong></span>
                                        </div>
                                    </div>

                                    {{-- Sisi Cetak --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="print_side">
                                            Sisi Cetak
                                            <span class="req">*</span>
                                        </label>

                                        <select id="print_side" name="print_side" class="select select2" required>
                                            <option value="1" selected>1 Sisi</option>
                                            <option value="2">2 Sisi (Bolak-Balik)</option>
                                        </select>
                                        <small class="field-help">Pilih cetak 1 sisi atau 2 sisi bolak-balik.</small>
                                    </div>
                                </div>

                                {{-- Mode Produksi (Layout vs Direct Sheet - Khusus Non-Display) --}}
                                <div class="form-grid" id="productionModeWrapper">
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="production_mode">
                                            Mode Produksi
                                            <span class="req">*</span>
                                        </label>

                                        <select id="production_mode" name="production_mode" class="select select2"
                                            required>
                                            <option value="layout" selected>Hitung Layout (Hitung Muat per Lembar A3+)
                                            </option>
                                            <option value="direct">Cetak Langsung per Lembar (Direct Sheet)</option>
                                        </select>
                                        <small class="field-help" id="productionModeHelp">
                                            Mode layout otomatis menghitung berapa objek produk jadi yang muat di lembar
                                            A3+.
                                        </small>
                                    </div>
                                </div>

                                {{-- CONTAINER MODE DISPLAY (KHUSUS DISPLAY) --}}
                                <div id="displayFieldsContainer" style="display: none;">
                                    <div class="form-grid">
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="display_qty">
                                                Jumlah Paket (Box)
                                                <span class="req">*</span>
                                            </label>
                                            <div class="input-suffix">
                                                <input id="display_qty" class="input" type="number" min="1"
                                                    step="1" placeholder="Contoh: 1" value="1">
                                            </div>
                                            <small class="field-help" id="displayQtyHelp">1 Paket = 100 pcs (Kebutuhan
                                                Cetak: 4 Lembar A3+).</small>
                                        </div>
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label">Informasi Produk Display</label>
                                            <div
                                                style="padding: 10px 14px; border-radius: 8px; background: rgba(59, 130, 246, 0.08); border: 1px solid rgba(59, 130, 246, 0.2); font-size: 13px; color: #1e40af;">
                                                <i class="bi bi-box-seam me-1"></i>
                                                <span id="displayPackageSummary">Paket display siap cetak lengkap dengan
                                                    Box KN dan Finishing Die Cut.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- CONTAINER MODE HITUNG LAYOUT (DEFAULT) --}}
                                <div id="layoutFieldsContainer">
                                    <div class="form-grid">
                                        {{-- Panjang Jadi --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="length">
                                                Panjang Jadi (cm)
                                                <span class="req">*</span>
                                            </label>
                                            <div class="input-suffix">
                                                <input id="length" name="length" class="input" type="number"
                                                    min="0.1" step="0.1" inputmode="decimal"
                                                    placeholder="Contoh: 9" value="{{ old('length') }}">
                                            </div>
                                            <small class="field-help">Ukuran panjang potongan produk jadi.</small>
                                        </div>

                                        {{-- Lebar Jadi --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="width">
                                                Lebar Jadi (cm)
                                                <span class="req">*</span>
                                            </label>
                                            <div class="input-suffix">
                                                <input id="width" name="width" class="input" type="number"
                                                    min="0.1" step="0.1" inputmode="decimal"
                                                    placeholder="Contoh: 5.5" value="{{ old('width') }}">
                                            </div>
                                            <small class="field-help">Ukuran lebar potongan produk jadi.</small>
                                        </div>
                                    </div>

                                    <div class="form-grid">
                                        {{-- Jumlah Produk Jadi (pcs) --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="qty">
                                                Jumlah Produk Jadi (pcs)
                                                <span class="req">*</span>
                                            </label>
                                            <div class="input-suffix">
                                                <input id="qty" name="qty" class="input" type="number"
                                                    min="1" step="1" inputmode="numeric"
                                                    placeholder="Contoh: 500" value="{{ old('qty') }}">
                                            </div>
                                            <small class="field-help">Total kuantiti pcs produk jadi yang diinginkan
                                                pelanggan.</small>
                                        </div>

                                        {{-- Info Card Imposisi Realtime --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label">
                                                Hasil Imposisi Lembar
                                            </label>
                                            <div class="estimation-info"
                                                style="border: 1px solid var(--border); border-radius: 8px; padding: 10px 14px; background: rgba(0,0,0,0.02);">
                                                <div
                                                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                                    <span style="font-size: 12px; color: var(--text-muted, #64748b);">Muat
                                                        per Lembar:</span>
                                                    <strong id="layoutUpCount" style="font-size: 14px;">—</strong>
                                                </div>
                                                <div
                                                    style="display: flex; justify-content: space-between; align-items: center;">
                                                    <span
                                                        style="font-size: 12px; color: var(--text-muted, #64748b);">Kebutuhan
                                                        Lembar A3+:</span>
                                                    <strong id="layoutTotalSheets"
                                                        style="font-size: 14px; color: var(--primary, #1e3a8a);">—</strong>
                                                </div>
                                            </div>
                                            <small class="field-help" id="impositionNotes">Dihitung otomatis berdasarkan
                                                ukuran lembar A3+ (31.5 × 47.5 cm area cetak).</small>
                                        </div>
                                    </div>
                                </div>

                                {{-- CONTAINER MODE CETAK LANGSUNG (DIRECT SHEET) --}}
                                <div id="directSheetFieldsContainer" style="display: none;">
                                    <div class="form-grid">
                                        {{-- Ukuran Lembar --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="sheet_size_id">
                                                Ukuran Lembar A3+
                                                <span class="req">*</span>
                                            </label>
                                            <select id="sheet_size_id" name="sheet_size_id" class="select select2">
                                                <option value="standard" selected>A3+ Standar (32.5 × 48.5 cm)</option>
                                                <option value="custom">Ukuran Khusus</option>
                                            </select>
                                        </div>

                                        {{-- Jumlah Lembar Cetak --}}
                                        <div class="field" style="margin-bottom: 20px;">
                                            <label class="field-label" for="sheet_qty">
                                                Jumlah Lembar Cetak
                                                <span class="req">*</span>
                                            </label>
                                            <div class="input-suffix">
                                                <input id="sheet_qty" name="sheet_qty" class="input" type="number"
                                                    min="1" step="1" inputmode="numeric"
                                                    placeholder="Contoh: 100" value="{{ old('sheet_qty') }}">
                                            </div>
                                            <small class="field-help">Langsung cetak per lembar tanpa pemotongan
                                                n-up.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- =========================================================
                                3. FINISHING: LAMINASI & CUTTING
                            ========================================================== --}}
                            <div class="form-section" id="finishingSection">
                                <div class="form-section-head">
                                    <span class="eyebrow">
                                        FINISHING: LAMINASI &amp; CUTTING
                                    </span>
                                </div>

                                <div class="form-grid">
                                    {{-- Laminasi --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="lamination_type">
                                            Finishing Laminasi
                                        </label>

                                        <select id="lamination_type" name="lamination_type" class="select select2">
                                            <option value="none" selected>Tanpa Laminasi</option>
                                            @foreach ($laminations ?? [] as $lam)
                                                <option value="{{ $lam->id }}_1"
                                                    data-lamination-id="{{ $lam->id }}" data-side="1"
                                                    data-paper-cost="{{ $lam->paper_cost ?? 0 }}"
                                                    data-click-cost="{{ $lam->click_cost ?? 0 }}"
                                                    data-cost="{{ $lam->single_print_cost ?? 0 }}">
                                                    {{ $lam->name }} (1 Sisi)
                                                </option>
                                                <option value="{{ $lam->id }}_2"
                                                    data-lamination-id="{{ $lam->id }}" data-side="2"
                                                    data-paper-cost="{{ $lam->paper_cost ?? 0 }}"
                                                    data-click-cost="{{ $lam->click_cost ?? 0 }}"
                                                    data-cost="{{ $lam->double_print_cost ?? 0 }}">
                                                    {{ $lam->name }} (2 Sisi)
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="field-help" id="laminationHelp">
                                            Pilihan laminasi aktif dari Master Biaya Produksi Laminasi Digital Print A3+.
                                        </small>

                                        {{-- Info Ringkas HPP Laminasi dari Master --}}
                                        <div id="laminationMasterInfo"
                                            style="display: none; font-size: 11.5px; color: rgb(59, 130, 246); margin-top: 6px; padding: 6px 10px; background: rgba(59, 130, 246, 0.14); border-radius: 6px;">
                                            <span>Biaya Dasar Laminasi: <strong id="infoLaminationCost">Rp 0</strong>
                                                /lbr</span>
                                        </div>
                                    </div>

                                    {{-- Cutting / Potong --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="cutting_type">
                                            Finishing Cutting / Potong
                                        </label>

                                        <select id="cutting_type" name="cutting_type" class="select select2">
                                            <option value="none" selected>Tanpa Cutting / Tanpa Potong</option>
                                            @foreach ($cuttings ?? [] as $cut)
                                                @php
                                                    $desc = '';
                                                    $lowerName = strtolower(trim($cut->name));
                                                    $isPotong = ($lowerName === 'potong');
                                                    if ($isPotong) {
                                                        $desc = ' Lurus (Guillotine)';
                                                    } elseif (str_contains($lowerName, 'kiss')) {
                                                        $desc = ' (Cutting Sticker Setengah Putus)';
                                                    } elseif (str_contains($lowerName, 'die')) {
                                                        $desc = ' (Cutting Sticker Putus)';
                                                    } elseif (str_contains($lowerName, 'perforasi')) {
                                                        $desc = ' (Garis Cacah)';
                                                    }
                                                    $val = $isPotong ? 'potong' : $cut->id;
                                                @endphp
                                                <option value="{{ $val }}" {{ old('cutting_type') == $val ? 'selected' : '' }}>
                                                    {{ $cut->name }}{{ $desc }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="field-help" id="cuttingHelpText">
                                            Pilihan jasa potong aktif dari Master Biaya Produksi Finishing Jasa Potong.
                                        </small>

                                        {{-- Info Ringkas HPP Cutting dari Master --}}
                                        <div id="cuttingMasterInfo"
                                            style="display: none; font-size: 11.5px; color: rgb(16, 185, 129); margin-top: 6px; padding: 6px 10px; background: rgba(16, 185, 129, 0.14); border-radius: 6px;">
                                            <span>HPP Mesin Cutting: <strong>Rp 1.000</strong> /lbr</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Opsi Potong Override (Hanya muncul jika Potong dipilih) --}}
                                <div class="form-grid" id="potongOverrideContainer" style="display: none;">
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="potong_size_override">
                                            Kategori Ukuran Potong
                                        </label>
                                        <select id="potong_size_override" name="potong_size_override"
                                            class="select select2">
                                            <option value="" selected>Otomatis (Berdasarkan Luas Ukuran Jadi)
                                            </option>
                                            <option value="A3">A3 (Besar / 29.7 × 42 cm)</option>
                                            <option value="A4">A4 (Standar / 21 × 29.7 cm)</option>
                                            <option value="A5">A5 (Sedang / 14.8 × 21 cm)</option>
                                            <option value="<=A6">≤ A6 (Kecil / Kartu Nama)</option>
                                        </select>
                                        <small class="field-help" id="potongOverrideHelp">
                                            Biaya potong dihitung per batch produksi berdasarkan tarif ongkos potong minimal
                                            dari master data.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{-- Notice Finishing untuk Display --}}
                            <div id="displayFinishingNotice" class="form-section"
                                style="display: none; margin-bottom: 20px;">
                                <div
                                    style="padding: 14px 18px; border-radius: 8px; background: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); font-size: 13.5px; color: #065f46;">
                                    <i class="bi bi-check-circle-fill me-2 text-success"></i>
                                    <strong>Finishing Otomatis:</strong> Ongkos Die Cut dan Box KN sudah otomatis
                                    diperhitungkan ke dalam paket Display.
                                </div>
                            </div>

                            {{-- =========================================================
                                4. KOMPONEN TAMBAHAN
                            ========================================================== --}}
                            <div class="form-section"
                                style="margin-top: 20px; margin-bottom: 20px; display: flex; flex-direction: column; gap: 20px;">

                                <div class="form-section-header"
                                    style="display: flex; flex-direction: column; gap: 15px;">
                                    <div>
                                        <div class="form-section-title">
                                            Komponen Tambahan
                                        </div>
                                        <div class="form-section-description eyebrow">
                                            Tambahkan komponen tambahan / jasa khusus yang digunakan dalam estimasi
                                            (opsional).
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn--primary" id="addAdditionalComponentButton"
                                        style="max-width: 220px;">
                                        + Tambah Komponen
                                    </button>
                                </div>

                                <div id="additionalComponentsContainer">
                                    {{-- Komponen tambahan akan digenerate via JavaScript --}}
                                </div>
                            </div>

                            {{-- =========================================================
                                5. DISKON & LEAD TIME
                            ========================================================== --}}
                            <div class="form-section">
                                <div class="form-section-head">
                                    <span class="eyebrow">
                                        DISKON &amp; ESTIMASI WAKTU
                                    </span>
                                </div>

                                <div class="field" style="margin-bottom: 20px;">
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
                                                    <small>Harga normal</small>
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
                                                    <small>Potongan berdasarkan persentase</small>
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
                                                    <small>Potongan berdasarkan nominal rupiah</small>
                                                </span>
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-grid">
                                    {{-- Input Diskon --}}
                                    <div class="field" id="discountValueField" hidden>
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <label class="field-label" for="discount_value" id="discountValueLabel">
                                                Nilai Diskon
                                            </label>
                                            <span class="input-suffix__text" id="discountSuffix">
                                                %
                                            </span>
                                        </div>

                                        <div class="input-suffix" id="discountInputWrapper">
                                            <input id="discount_value" name="discount_value" class="input"
                                                type="text" inputmode="decimal" placeholder="0" disabled>
                                        </div>
                                    </div>

                                    {{-- Estimasi Waktu (Lead Time) --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label" for="estimation_time">
                                            Estimasi Waktu Pengerjaan (Hari Kerja)
                                            <span class="req">*</span>
                                        </label>

                                        <div class="input-suffix">
                                            <input id="estimation_time" name="estimation_time" class="input"
                                                type="number" min="1" step="1" inputmode="numeric"
                                                placeholder="Contoh: 1" value="{{ old('estimation_time', 1) }}" required>
                                        </div>
                                        <small class="field-help">Ditampilkan pada ringkasan penawaran pelanggan.</small>
                                    </div>
                                </div>
                            </div>

                            {{-- =========================================================
                                6. ACTIONS
                            ========================================================== --}}
                            <div class="form-actions">
                                <span class="badge dot success">
                                    Siap dihitung
                                </span>

                                <span class="spacer"></span>

                                <a href="{{ route('admin_home') }}" class="btn btn--ghost">
                                    Batal
                                </a>

                                <button type="submit" class="btn btn--primary" id="calculateEstimationButton">
                                    Hitung Estimasi
                                </button>
                            </div>
                        </form>
                    </section>
                </div>
            </main>

            <div data-shell-footer></div>
        </div>
    </div>

    {{-- =========================================================
        MODAL PREVIEW ESTIMASI DIGITAL PRINT A3+
    ========================================================== --}}
    <div class="modal-overlay" id="modalEstimationPreview">
        <div class="modal modal-estimation">

            {{-- HEADER --}}
            <div class="modal-header">
                <div>
                    <span class="eyebrow">ESTIMASI · DIGITAL PRINT A3+</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseEstimationPreview" aria-label="Tutup">
                    <svg viewBox="0 0 24 24">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            {{-- TOGGLE PREVIEW (Summary vs Details) --}}
            <div class="estimation-preview-toggle">
                <button type="button" class="estimation-preview-toggle-btn is-active" id="btnSummaryEstimation">
                    Summary Estimasi
                </button>

                <button type="button" class="estimation-preview-toggle-btn" id="btnDetailsEstimation">
                    Details Estimasi
                </button>
            </div>

            {{-- BODY --}}
            <div class="modal-body">

                {{-- TAB 1: SUMMARY ESTIMASI --}}
                <div id="estimationSummary" class="estimation-preview-content">
                    <div class="preview-section">
                        <div class="preview-section-header">
                            <div>
                                <h3 class="preview-section-title">SUMMARY ESTIMASI</h3>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted">Mesin</small>
                                <div id="summaryEngineName">Digital Print A3+</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Lokasi</small>
                                <div id="summaryLocationName">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Tipe Harga</small>
                                <div id="summaryPriceType">Harga Umum</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Material</small>
                                <div id="summaryMaterialName">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Warna / Sisi</small>
                                <div id="summaryPrintSpec">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Finishing</small>
                                <div id="summaryFinishing">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Jumlah Pesanan</small>
                                <div id="summaryQty">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Imposisi (Objek/Lembar)</small>
                                <div id="summaryImposition">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Total Lembar A3+</small>
                                <strong id="summaryTotalSheets" style="color: var(--primary, #1e3a8a);">-</strong>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- HARGA --}}
                    <div class="preview-section">
                        <div class="preview-section-header">
                            <div>
                                <h3 class="preview-section-title">HARGA</h3>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted">Harga / Lembar Cetak</small>
                                <div id="summaryUnitPrice">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Subtotal Harga Jual</small>
                                <div id="summarySubtotal">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Diskon</small>
                                <div id="summaryDiscount">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Harga / PCS Jadi</small>
                                <div id="summaryPricePerPcs">-</div>
                            </div>

                            <div class="col-md-8">
                                <small class="text-muted">Grand Total</small>
                                <strong id="summaryGrandTotal"
                                    style="font-size: 1.25rem; color: var(--primary, #1e3a8a);">
                                    -
                                </strong>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- PROFITABILITAS --}}
                    <div class="preview-section">
                        <div class="preview-section-header">
                            <div>
                                <h3 class="preview-section-title">PROFITABILITAS</h3>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted">Total HPP</small>
                                <strong id="summaryTotalHpp">-</strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Total Profit</small>
                                <strong id="summaryProfit" style="color: #16a34a;">-</strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Margin (%)</small>
                                <div id="summaryMargin">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Markup (%)</small>
                                <div id="summaryMarkup">-</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: DETAILS ESTIMASI --}}
                <div id="estimationDetails" class="estimation-preview-content" style="display: none;">

                    {{-- INFORMASI PRODUKSI --}}
                    <div class="preview-section">
                        <div class="preview-section-header">
                            <div>
                                <h3 class="preview-section-title">INFORMASI PRODUKSI</h3>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted">Mesin</small>
                                <div>Digital Print A3+</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Lokasi</small>
                                <div id="previewLocationName">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Material</small>
                                <div id="previewMaterialName">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Warna &amp; Sisi</small>
                                <div id="previewColorSide">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Pilihan Harga</small>
                                <div id="previewPriceType">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Estimasi Waktu</small>
                                <div id="previewLeadTime">-</div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- DETAIL IMPOSISI --}}
                    <div class="preview-section">
                        <div class="preview-section-header">
                            <div>
                                <h3 class="preview-section-title">DETAIL IMPOSISI &amp; LEMBAR</h3>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted">Mode Produksi</small>
                                <div id="previewMode">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Ukuran Jadi</small>
                                <div id="previewFinishedSize">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Ukuran Lembar A3+</small>
                                <div id="previewSheetSize">32.5 × 48.5 cm (Area Cetak 31.5 × 47.5 cm)</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Muat per Lembar (Up)</small>
                                <div id="previewUpCount">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Orientasi Layout</small>
                                <div id="previewOrientation">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Total Kebutuhan Lembar</small>
                                <strong id="previewTotalSheets" style="color: var(--primary, #1e3a8a);">-</strong>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- KOMPONEN HARGA --}}
                    <div class="preview-section">
                        <div class="preview-section-header">
                            <div>
                                <h3 class="preview-section-title">RINCIAN KOMPONEN HARGA JUAL</h3>
                            </div>
                        </div>

                        {{-- Cetak & Material --}}
                        <div class="preview-component" style="margin-bottom: 12px;">
                            <div class="preview-component-header">
                                <strong>KERTAS &amp; CETAK A3+</strong>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <small class="text-muted">Basis Lembar</small>
                                    <div id="previewPrintBasis">-</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Harga Satuan</small>
                                    <div id="previewPrintUnitPrice">-</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Subtotal Cetak</small>
                                    <div id="previewPrintSubtotal">-</div>
                                </div>
                            </div>
                        </div>

                        {{-- Laminasi --}}
                        <div class="preview-component" id="previewLaminationBlock" style="margin-bottom: 12px;">
                            <div class="preview-component-header">
                                <strong>FINISHING LAMINASI</strong>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <small class="text-muted">Jenis Laminasi</small>
                                    <div id="previewLaminationName">Tanpa Laminasi</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Harga Satuan</small>
                                    <div id="previewLaminationUnitPrice">-</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Subtotal Laminasi</small>
                                    <div id="previewLaminationSubtotal">-</div>
                                </div>
                            </div>
                        </div>

                        {{-- Cutting --}}
                        <div class="preview-component" id="previewCuttingBlock" style="margin-bottom: 12px;">
                            <div class="preview-component-header">
                                <strong>FINISHING CUTTING / POTONG</strong>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <small class="text-muted">Jenis Cutting</small>
                                    <div id="previewCuttingName">Tanpa Cutting</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Tarif Satuan / Minimum</small>
                                    <div id="previewCuttingUnitPrice">-</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Subtotal Cutting</small>
                                    <div id="previewCuttingSubtotal">-</div>
                                </div>
                            </div>
                        </div>

                        {{-- Komponen Tambahan --}}
                        <div id="previewAdditionalComponentsContainer"></div>
                    </div>

                    <hr>

                    {{-- RINGKASAN KEUANGAN --}}
                    <div class="preview-section">
                        <div class="preview-section-header">
                            <div>
                                <h3 class="preview-section-title">RINGKASAN KEUANGAN</h3>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted">Subtotal Harga Jual</small>
                                <div id="previewSubtotal">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Diskon</small>
                                <div id="previewDiscount">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Grand Total</small>
                                <strong id="previewGrandTotal">-</strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Harga Per PCS</small>
                                <div id="previewPricePerPcs">-</div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- HPP --}}
                    <div class="preview-section">
                        <div class="preview-section-header">
                            <div>
                                <h3 class="preview-section-title">HPP (HARGA POKOK PRODUKSI)</h3>
                            </div>
                        </div>

                        <div class="preview-component" style="margin-bottom: 12px;">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <small class="text-muted">HPP Kertas &amp; Klik Cetak</small>
                                    <div id="previewPrintHpp">-</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">HPP Laminasi</small>
                                    <div id="previewLaminationHpp">-</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">HPP Cutting</small>
                                    <div id="previewCuttingHpp">-</div>
                                </div>
                            </div>
                        </div>

                        <div id="previewAdditionalComponentsHppContainer"></div>

                        <div class="preview-component">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <small class="text-muted">Total HPP Produksi</small>
                                    <strong id="previewTotalHpp" style="color: var(--primary, #1e3a8a);">-</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- PROFITABILITAS --}}
                    <div class="preview-section">
                        <div class="preview-section-header">
                            <div>
                                <h3 class="preview-section-title">ANALISIS PROFITABILITAS</h3>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <small class="text-muted">Total Profit</small>
                                <strong id="previewProfit" style="color: #16a34a;">-</strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Margin (%)</small>
                                <div id="previewMargin">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Markup (%)</small>
                                <div id="previewMarkup">-</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER MODAL --}}
            <div class="modal-footer">
                <button type="button" class="btn btn--ghost" id="btnCancelEstimationPreview">
                    Kembali
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            /* =====================================================
               1. SELECT2 INITIALIZATION
            ===================================================== */
            $('.select2').select2({
                width: '100%',
                placeholder: 'Pilih...'
            });

            /* =====================================================
               2. ELEMENTS
            ===================================================== */
            const form = document.getElementById('a3PlusEstimationForm');
            const calculateButton = document.getElementById('calculateEstimationButton');

            const modeSelect = $('#production_mode');
            const layoutFields = document.getElementById('layoutFieldsContainer');
            const directFields = document.getElementById('directSheetFieldsContainer');

            const lengthInput = document.getElementById('length');
            const widthInput = document.getElementById('width');
            const qtyInput = document.getElementById('qty');
            const sheetQtyInput = document.getElementById('sheet_qty');

            const layoutUpCountEl = document.getElementById('layoutUpCount');
            const layoutTotalSheetsEl = document.getElementById('layoutTotalSheets');

            const cuttingSelect = $('#cutting_type');
            const potongOverrideWrap = document.getElementById('potongOverrideContainer');

            // Diskon
            const discountRadios = document.querySelectorAll('input[name="discount_type"]');
            const discountValueField = document.getElementById('discountValueField');
            const discountValueInput = document.getElementById('discount_value');
            const discountSuffix = document.getElementById('discountSuffix');

            // Modal Preview
            const modalPreview = document.getElementById('modalEstimationPreview');
            const btnClosePreview = document.getElementById('btnCloseEstimationPreview');
            const btnCancelPreview = document.getElementById('btnCancelEstimationPreview');
            const btnSummary = document.getElementById('btnSummaryEstimation');
            const btnDetails = document.getElementById('btnDetailsEstimation');
            const summaryView = document.getElementById('estimationSummary');
            const detailsView = document.getElementById('estimationDetails');

            /* =====================================================
               3. PRODUCTION MODE TOGGLE (Layout vs Direct Sheet)
            ===================================================== */
            modeSelect.on('change', function() {
                const val = $(this).val();
                const potongHelp = document.getElementById('potongOverrideHelp');
                if (val === 'layout') {
                    layoutFields.style.display = 'block';
                    directFields.style.display = 'none';
                    lengthInput.setAttribute('required', 'required');
                    widthInput.setAttribute('required', 'required');
                    qtyInput.setAttribute('required', 'required');
                    sheetQtyInput.removeAttribute('required');
                    if (potongHelp) {
                        potongHelp.textContent =
                            'Biaya potong dihitung per batch produksi berdasarkan tarif ongkos potong minimal dari master data.';
                    }
                } else {
                    layoutFields.style.display = 'none';
                    directFields.style.display = 'block';
                    lengthInput.removeAttribute('required');
                    widthInput.removeAttribute('required');
                    qtyInput.removeAttribute('required');
                    sheetQtyInput.setAttribute('required', 'required');
                    if (potongHelp) {
                        potongHelp.textContent =
                            'Pada mode cetak langsung, silakan tentukan kategori ukuran potong secara spesifik (A3, A4, A5, atau ≤ A6).';
                    }
                }
            });

            /* =====================================================
               4. REALTIME IMPOSITION PREVIEW (N-UP CALCULATION)
            ===================================================== */
            // Standar area cetak A3+: 31.5 x 47.5 cm (dengan toleransi potong/bleed)
            const SHEET_PRINTABLE_L = 47.5;
            const SHEET_PRINTABLE_W = 31.5;

            function updateImpositionCalculation() {
                const l = parseFloat(lengthInput.value) || 0;
                const w = parseFloat(widthInput.value) || 0;
                const qty = parseInt(qtyInput.value) || 0;

                if (l > 0 && w > 0) {
                    const upNormal = Math.floor(SHEET_PRINTABLE_L / l) * Math.floor(SHEET_PRINTABLE_W / w);
                    const upRotated = Math.floor(SHEET_PRINTABLE_L / w) * Math.floor(SHEET_PRINTABLE_W / l);
                    const up = Math.max(upNormal, upRotated);
                    const orientation = upRotated > upNormal ? 'Rotated' : 'Normal';

                    if (up > 0) {
                        layoutUpCountEl.textContent = `${up} pcs (${orientation})`;
                        if (qty > 0) {
                            const totalSheets = Math.ceil(qty / up);
                            layoutTotalSheetsEl.textContent = `${totalSheets.toLocaleString('id-ID')} lembar A3+`;
                        } else {
                            layoutTotalSheetsEl.textContent = '—';
                        }
                    } else {
                        layoutUpCountEl.textContent = 'Ukuran melebihi lembar A3+';
                        layoutTotalSheetsEl.textContent = '—';
                    }
                } else {
                    layoutUpCountEl.textContent = '—';
                    layoutTotalSheetsEl.textContent = '—';
                }
            }

            lengthInput.addEventListener('input', updateImpositionCalculation);
            widthInput.addEventListener('input', updateImpositionCalculation);
            qtyInput.addEventListener('input', updateImpositionCalculation);

            /* =====================================================
               5. CUTTING TYPE TOGGLE
            ===================================================== */
            const cuttingMasterInfo = document.getElementById('cuttingMasterInfo');
            cuttingSelect.on('change', function() {
                const val = $(this).val();
                if (val === 'potong') {
                    potongOverrideWrap.style.display = 'block';
                    if (cuttingMasterInfo) cuttingMasterInfo.style.display = 'none';
                } else if (val && val !== 'none') {
                    potongOverrideWrap.style.display = 'none';
                    if (cuttingMasterInfo) cuttingMasterInfo.style.display = 'block';
                } else {
                    potongOverrideWrap.style.display = 'none';
                    if (cuttingMasterInfo) cuttingMasterInfo.style.display = 'none';
                }
            });

            /* =====================================================
               5B. MATERIAL FILTER BERDASARKAN LOKASI, KATEGORI & WARNA CETAK
            ===================================================== */
            const locationSelect = $('#location_id');
            const categorySelect = $('#category_id');
            const printColorSelect = $('#print_color');
            const materialSelect = $('#material_id');
            const displayQtyInput = document.getElementById('display_qty');
            const displayPackageSummary = document.getElementById('displayPackageSummary');
            const displayQtyHelp = document.getElementById('displayQtyHelp');

            const materialMasterInfo = document.getElementById('materialMasterInfo');
            const infoPaperCost = document.getElementById('infoPaperCost');
            const infoSingleCost = document.getElementById('infoSingleCost');
            const infoDoubleCost = document.getElementById('infoDoubleCost');
            const materialHelp = document.getElementById('materialHelp');

            const getMaterialsUrl = "{{ route('admin_get_materials_a3plus') }}";

            function toggleDisplayMode(isDisplay) {
                const materialLabel = document.getElementById('materialLabelText');
                const productionModeWrapper = document.getElementById('productionModeWrapper');
                const printColorField = document.getElementById('printColorField');
                const layoutFields = document.getElementById('layoutFieldsContainer');
                const directFields = document.getElementById('directSheetFieldsContainer');
                const displayFields = document.getElementById('displayFieldsContainer');
                const finishingSection = document.getElementById('finishingSection');
                const displayFinishingNotice = document.getElementById('displayFinishingNotice');

                if (isDisplay) {
                    if (materialLabel) materialLabel.textContent = 'Produk & Komponen Display';
                    if (productionModeWrapper) productionModeWrapper.style.display = 'none';
                    if (printColorField) printColorField.style.display = 'none';
                    if (layoutFields) layoutFields.style.display = 'none';
                    if (directFields) directFields.style.display = 'none';
                    if (displayFields) displayFields.style.display = 'block';
                    if (finishingSection) finishingSection.style.display = 'none';
                    if (displayFinishingNotice) displayFinishingNotice.style.display = 'block';

                    lengthInput.removeAttribute('required');
                    widthInput.removeAttribute('required');
                    qtyInput.removeAttribute('required');
                    sheetQtyInput.removeAttribute('required');
                } else {
                    if (materialLabel) materialLabel.textContent = 'Material Kertas / Stiker';
                    if (productionModeWrapper) productionModeWrapper.style.display = 'grid';
                    if (printColorField) printColorField.style.display = 'block';
                    if (displayFields) displayFields.style.display = 'none';
                    if (finishingSection) finishingSection.style.display = 'block';
                    if (displayFinishingNotice) displayFinishingNotice.style.display = 'none';

                    modeSelect.trigger('change');
                }
            }

            async function loadMaterials() {
                const locationId = locationSelect.val();
                const categoryId = categorySelect.val();
                const selectedCatOption = categorySelect.find('option:selected');
                const catName = (selectedCatOption.data('name') || selectedCatOption.text() || '').trim()
                    .toLowerCase();
                const isDisplay = catName.includes('display');
                const printColor = printColorSelect.val() || 'fc';
                const currentMaterialId = materialSelect.val();

                toggleDisplayMode(isDisplay);

                if (!locationId) {
                    materialSelect.empty();
                    materialSelect.append('<option value="">Pilih Lokasi terlebih dahulu</option>');
                    materialSelect.trigger('change.select2');
                    materialSelect.trigger('change');
                    if (materialHelp) {
                        materialHelp.textContent = 'Silakan pilih lokasi terlebih dahulu.';
                    }
                    return;
                }

                materialSelect.prop('disabled', true);
                if (materialHelp) {
                    materialHelp.textContent = isDisplay ?
                        'Memuat produk & komponen display untuk lokasi terpilih...' :
                        'Memuat material untuk lokasi terpilih...';
                }

                try {
                    const url = new URL(getMaterialsUrl, window.location.origin);
                    url.searchParams.set('location_id', locationId);
                    if (categoryId) {
                        url.searchParams.set('category_id', categoryId);
                    }
                    if (!isDisplay) {
                        url.searchParams.set('print_color', printColor);
                    }

                    const response = await fetch(url.toString(), {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const res = await response.json();

                    materialSelect.empty();
                    materialSelect.append('<option value="">' + (isDisplay ? 'Pilih Produk & Komponen Display' :
                        'Pilih Material') + '</option>');

                    if (response.ok && res.success && Array.isArray(res.data) && res.data.length > 0) {
                        if (isDisplay) {
                            res.data.forEach(function(item) {
                                const $option = $('<option>')
                                    .val(item.id)
                                    .text(item.name)
                                    .attr('data-is-display', 'true')
                                    .attr('data-display-name', item.display_name)
                                    .attr('data-package-qty', item.package_qty)
                                    .attr('data-sheet-qty', item.sheet_qty)
                                    .attr('data-gen1', item.general_price)
                                    .attr('data-gen2', item.general_price_2_side)
                                    .attr('data-div1', item.division_price)
                                    .attr('data-div2', item.division_price_2_side);
                                materialSelect.append($option);
                            });
                        } else {
                            // Kelompokkan data berdasarkan category_name
                            const grouped = {};
                            res.data.forEach(function(mat) {
                                const cat = mat.category_name || 'Lainnya';
                                if (!grouped[cat]) grouped[cat] = [];
                                grouped[cat].push(mat);
                            });

                            Object.keys(grouped).forEach(function(cName) {
                                const $optgroup = $('<optgroup>').attr('label', cName);
                                grouped[cName].forEach(function(mat) {
                                    const $option = $('<option>')
                                        .val(mat.id)
                                        .text(mat.name)
                                        .attr('data-category', mat.category_name)
                                        .attr('data-paper-cost', mat.paper_cost || 0)
                                        .attr('data-single-cost', mat.single_print_cost || 0)
                                        .attr('data-double-cost', mat.double_print_cost || 0);
                                    $optgroup.append($option);
                                });
                                materialSelect.append($optgroup);
                            });
                        }

                        // Re-select jika sebelumnya sudah memilih material yang sama
                        if (currentMaterialId && materialSelect.find(`option[value="${currentMaterialId}"]`)
                            .length > 0) {
                            materialSelect.val(currentMaterialId);
                        } else {
                            materialSelect.val('');
                        }

                        if (materialHelp) {
                            if (isDisplay) {
                                materialHelp.textContent = 'Pilih kombinasi produk display dan bahan cetak.';
                            } else {
                                const colorLabel = printColor === 'bw' ? 'Hitam Putih (BW)' : 'Full Color';
                                materialHelp.textContent =
                                    `Menampilkan material ${colorLabel} yang aktif pada kategori dan lokasi terpilih.`;
                            }
                        }
                    } else {
                        materialSelect.append(
                            `<option value="" disabled>${isDisplay ? 'Belum ada produk display di lokasi ini' : 'Belum ada material untuk kategori ini di lokasi ini'}</option>`
                        );
                        if (materialHelp) {
                            materialHelp.textContent = isDisplay ?
                                'Belum ada produk display yang dikonfigurasi untuk lokasi ini.' :
                                'Belum ada material di master untuk kategori dan lokasi terpilih.';
                        }
                    }
                } catch (err) {
                    console.error('Gagal mengambil material:', err);
                    materialSelect.empty();
                    materialSelect.append('<option value="" disabled>Gagal memuat material</option>');
                    if (materialHelp) {
                        materialHelp.textContent = 'Gagal memuat material dari server.';
                    }
                } finally {
                    materialSelect.prop('disabled', false);
                    materialSelect.trigger('change.select2');
                    materialSelect.trigger('change');
                }
            }

            // Event listener saat Lokasi, Kategori, atau Warna Cetak berubah
            locationSelect.on('change', loadMaterials);
            categorySelect.on('change', loadMaterials);
            printColorSelect.on('change', loadMaterials);

            // Panggil saat inisialisasi jika lokasi sudah terpilih
            if (locationSelect.val()) {
                loadMaterials();
            }

            materialSelect.on('change', function() {
                const opt = $(this).find(':selected');
                const isDisplay = opt.attr('data-is-display') === 'true';

                if (isDisplay) {
                    const pkgQty = parseInt(opt.attr('data-package-qty')) || 100;
                    const sheetQty = parseInt(opt.attr('data-sheet-qty')) || 4;
                    const dName = opt.attr('data-display-name') || 'Produk Display';

                    if (displayPackageSummary) {
                        displayPackageSummary.textContent =
                            `1 Box ${dName} berisi ${pkgQty} pcs. Memerlukan ${sheetQty} lembar A3+. Ongkos Die Cut dan Box KN sudah otomatis diperhitungkan.`;
                    }
                    if (displayQtyHelp) {
                        displayQtyHelp.textContent =
                            `1 Paket = ${pkgQty} pcs (Kebutuhan Cetak: ${sheetQty} Lembar A3+).`;
                    }
                    materialMasterInfo.style.display = 'none';
                } else {
                    const paperCost = parseFloat(opt.data('paper-cost') || opt.attr('data-paper-cost')) ||
                    0;
                    const singleCost = parseFloat(opt.data('single-cost') || opt.attr(
                        'data-single-cost')) || 0;
                    const doubleCost = parseFloat(opt.data('double-cost') || opt.attr(
                        'data-double-cost')) || 0;

                    if ($(this).val()) {
                        infoPaperCost.textContent = 'Rp ' + Math.round(paperCost).toLocaleString('id-ID');
                        infoSingleCost.textContent = 'Rp ' + Math.round(singleCost).toLocaleString('id-ID');
                        infoDoubleCost.textContent = 'Rp ' + Math.round(doubleCost).toLocaleString('id-ID');
                        materialMasterInfo.style.display = 'block';
                    } else {
                        materialMasterInfo.style.display = 'none';
                    }
                }
            });

            const laminationSelect = $('#lamination_type');
            const laminationMasterInfo = document.getElementById('laminationMasterInfo');
            const infoLaminationCost = document.getElementById('infoLaminationCost');

            laminationSelect.on('change', function() {
                const opt = $(this).find(':selected');
                const cost = parseFloat(opt.data('cost')) || 0;

                if ($(this).val() && $(this).val() !== 'none') {
                    infoLaminationCost.textContent = 'Rp ' + Math.round(cost).toLocaleString('id-ID');
                    laminationMasterInfo.style.display = 'block';
                } else {
                    laminationMasterInfo.style.display = 'none';
                }
            });

            /* =====================================================
               6. KOMPONEN TAMBAHAN DINAMIS
            ===================================================== */
            const addBtn = document.getElementById('addAdditionalComponentButton');
            const compContainer = document.getElementById('additionalComponentsContainer');
            let compIdx = 0;

            function formatAdditionalComponentNumber(value) {
                const rawValue = String(value || '').replace(/\D/g, '');
                if (!rawValue) {
                    return '';
                }
                return new Intl.NumberFormat('id-ID', {
                    maximumFractionDigits: 0
                }).format(Number(rawValue));
            }

            function updateAdditionalComponentNumbers() {
                const components = compContainer.querySelectorAll('.additional-component-item');
                components.forEach(function(component, index) {
                    const numberElement = component.querySelector('.additional-component-number');
                    if (numberElement) {
                        numberElement.textContent = index + 1;
                    }
                });
            }

            function calculateAdditionalComponentPrice(component) {
                const priceType = component.querySelector('.additional-component-price-type').value;
                const calculatedPriceInput = component.querySelector('.additional-component-calculated-price');

                if (priceType !== 'hpp_margin') {
                    calculatedPriceInput.value = 'Rp 0';
                    return;
                }

                const hpp = Number(
                    String(component.querySelector('.additional-component-hpp').value || '').replace(/\./g, '')
                ) || 0;

                const margin = Number(
                    component.querySelector('.additional-component-margin').value
                ) || 0;

                if (hpp <= 0 || margin <= 0) {
                    calculatedPriceInput.value = 'Rp 0';
                    return;
                }

                const marginDecimal = margin / 100;
                const divisor = 1 - marginDecimal;

                if (divisor <= 0) {
                    calculatedPriceInput.value = 'Rp 0';
                    return;
                }

                const sellingPrice = hpp / divisor;
                calculatedPriceInput.value = formatRupiah(sellingPrice);
            }

            function initializeAdditionalComponent(component) {
                const priceTypeSelect = component.querySelector('.additional-component-price-type');
                const unitPriceWrapper = component.querySelector('.additional-component-unit-price-wrapper');
                const hppMarginWrapper = component.querySelector('.additional-component-hpp-margin-wrapper');
                const hppInput = component.querySelector('.additional-component-hpp');
                const unitPriceInput = component.querySelector('.additional-component-unit-price');
                const marginInput = component.querySelector('.additional-component-margin');
                const calculatedPriceInput = component.querySelector('.additional-component-calculated-price');

                unitPriceInput.addEventListener('input', function() {
                    this.value = formatAdditionalComponentNumber(this.value);
                });

                hppInput.addEventListener('input', function() {
                    this.value = formatAdditionalComponentNumber(this.value);
                    calculateAdditionalComponentPrice(component);
                });

                $(priceTypeSelect).on('change', function() {
                    if (this.value === 'hpp_margin') {
                        unitPriceWrapper.style.display = 'none';
                        hppMarginWrapper.style.display = 'block';
                        calculateAdditionalComponentPrice(component);
                    } else {
                        unitPriceWrapper.style.display = 'block';
                        hppMarginWrapper.style.display = 'none';
                        calculatedPriceInput.value = 'Rp 0';
                    }
                });

                marginInput.addEventListener('input', function() {
                    calculateAdditionalComponentPrice(component);
                });

                const removeButton = component.querySelector('.btn-remove-additional-component');
                removeButton.addEventListener('click', function() {
                    const pSelect = $(component).find('.additional-component-price-type');
                    const cSelect = $(component).find('.additional-component-type');

                    if (pSelect.hasClass('select2-hidden-accessible')) {
                        pSelect.select2('destroy');
                    }
                    if (cSelect.hasClass('select2-hidden-accessible')) {
                        cSelect.select2('destroy');
                    }

                    component.remove();
                    updateAdditionalComponentNumbers();
                });
            }

            function addAdditionalComponent() {
                const index = compIdx++;
                const componentHtml = `
                    <div class="additional-component-item" data-component-index="${index}" style="border: 1px solid var(--border-color, #e5e7eb); border-radius: 12px; padding: 20px; margin-bottom: 16px; background: rgba(0,0,0,0.01);">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px;">
                            <div style="font-size: 15px; font-weight: 600;">
                                Komponen Tambahan #<span class="additional-component-number">${index + 1}</span>
                            </div>
                            <button type="button" class="btn btn-danger btn-remove-additional-component" style="border: none; outline: none; border-radius: 8px; width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; box-shadow: none;">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>

                        <div class="form-grid">
                            {{-- Tipe Komponen --}}
                            <div class="field">
                                <label class="field-label" for="additional_component_type_${index}">
                                    Tipe
                                </label>
                                <select id="additional_component_type_${index}" name="additional_components[${index}][type]" class="select select2 additional-component-type">
                                    <option value=""></option>
                                    <option value="Material">Material</option>
                                    <option value="Finishing">Finishing</option>
                                    <option value="Jasa Pemasangan">Jasa Pemasangan</option>
                                </select>
                            </div>

                            {{-- Nama Komponen --}}
                            <div class="field">
                                <label class="field-label" for="additional_component_name_${index}">
                                    Nama Komponen
                                </label>
                                <input type="text" id="additional_component_name_${index}" name="additional_components[${index}][name]" class="input additional-component-name" placeholder="Contoh: Mata Ayam, Dus Kemasan" autocomplete="off">
                            </div>

                            {{-- Qty --}}
                            <div class="field">
                                <label class="field-label" for="additional_component_qty_${index}">
                                    Qty
                                </label>
                                <input type="number" id="additional_component_qty_${index}" name="additional_components[${index}][qty]" class="input additional-component-qty" min="1" step="1" value="1">
                            </div>

                            {{-- Tipe Harga --}}
                            <div class="field">
                                <label class="field-label" for="additional_component_price_type_${index}">
                                    Tipe Harga
                                </label>
                                <select id="additional_component_price_type_${index}" name="additional_components[${index}][price_type]" class="select select2 additional-component-price-type">
                                    <option value="unit_price">Harga Satuan (Rp)</option>
                                    <option value="hpp_margin">HPP + Margin %</option>
                                </select>
                            </div>
                        </div>

                        {{-- HARGA SATUAN --}}
                        <div class="additional-component-unit-price-wrapper" style="margin-top: 18px;">
                            <div class="field">
                                <label class="field-label" for="additional_component_unit_price_${index}">
                                    Harga Satuan
                                </label>
                                <input type="text" id="additional_component_unit_price_${index}" name="additional_components[${index}][unit_price]" class="input additional-component-unit-price" inputmode="numeric" placeholder="Contoh: 200.000">
                                <small class="field-help">Harga jual per unit komponen.</small>
                            </div>
                        </div>

                        {{-- HPP + MARGIN --}}
                        <div class="additional-component-hpp-margin-wrapper" style="display: none; margin-top: 18px;">
                            <div class="form-grid">
                                {{-- HPP / Unit --}}
                                <div class="field">
                                    <label class="field-label" for="additional_component_hpp_${index}">
                                        HPP / Unit
                                    </label>
                                    <input type="text" id="additional_component_hpp_${index}" name="additional_components[${index}][hpp]" class="input additional-component-hpp" inputmode="numeric" placeholder="Contoh: 100.000">
                                    <small class="field-help">HPP per unit komponen.</small>
                                </div>

                                {{-- Margin --}}
                                <div class="field">
                                    <label class="field-label" for="additional_component_margin_${index}">
                                        Margin
                                    </label>
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <input type="number" id="additional_component_margin_${index}" name="additional_components[${index}][margin]" class="input additional-component-margin" min="0" max="99.99" step="0.01" placeholder="Contoh: 50">
                                        <span>%</span>
                                    </div>
                                    <small class="field-help">Margin dihitung berdasarkan harga jual.</small>
                                </div>
                            </div>

                            {{-- Harga Jual / Unit --}}
                            <div class="field" style="margin-top: 18px;">
                                <label class="field-label">
                                    Harga Jual / Unit
                                </label>
                                <input type="text" class="input additional-component-calculated-price" value="Rp 0" readonly>
                                <small class="field-help">Otomatis dihitung dari HPP dan margin.</small>
                            </div>
                        </div>
                    </div>
                `;

                compContainer.insertAdjacentHTML('beforeend', componentHtml);
                const component = compContainer.lastElementChild;

                $(component).find('.additional-component-price-type').select2({
                    width: '100%',
                    placeholder: 'Pilih...'
                });

                $(component).find('.additional-component-type').select2({
                    width: '100%',
                    placeholder: 'Pilih...'
                });

                initializeAdditionalComponent(component);
                updateAdditionalComponentNumbers();
            }

            if (addBtn) {
                addBtn.addEventListener('click', addAdditionalComponent);
            }

            /* =====================================================
               7. DISCOUNT RADIO TOGGLE & NOMINAL RUPIAH FORMAT
            ===================================================== */
            discountRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const type = this.value;
                    if (type === 'none') {
                        discountValueField.hidden = true;
                        discountValueInput.disabled = true;
                        discountValueInput.value = '';
                    } else if (type === 'percentage') {
                        discountValueField.hidden = false;
                        discountValueInput.disabled = false;
                        discountValueInput.value = '';
                        discountSuffix.textContent = '%';
                        discountValueLabel.textContent = 'Persentase Diskon';
                        discountValueInput.placeholder = 'Contoh: 10';
                    } else if (type === 'nominal') {
                        discountValueField.hidden = false;
                        discountValueInput.disabled = false;
                        discountValueInput.value = '';
                        discountSuffix.textContent = 'Rp';
                        discountValueLabel.textContent = 'Nominal Diskon';
                        discountValueInput.placeholder = 'Contoh: 25.000';
                    }
                });
            });

            discountValueInput.addEventListener('input', function() {
                const selectedType = document.querySelector('input[name="discount_type"]:checked')?.value;

                if (selectedType === 'percentage') {
                    // Hanya izinkan angka dan satu titik desimal
                    let value = this.value.replace(/[^0-9.]/g, '');
                    const parts = value.split('.');
                    if (parts.length > 2) {
                        value = parts[0] + '.' + parts.slice(1).join('');
                    }
                    this.value = value;
                }

                if (selectedType === 'nominal') {
                    // Format ribuan menggunakan titik
                    const rawValue = this.value.replace(/\D/g, '');
                    this.value = rawValue ?
                        new Intl.NumberFormat('id-ID', {
                            maximumFractionDigits: 0
                        }).format(Number(rawValue)) :
                        '';
                }
            });

            /* =====================================================
               8. MODAL PREVIEW TABS & CONTROLS
            ===================================================== */
            function openPreviewModal() {
                modalPreview.classList.add('is-open');
            }

            function closePreviewModal() {
                modalPreview.classList.remove('is-open');
            }

            if (btnClosePreview) btnClosePreview.addEventListener('click', closePreviewModal);
            if (btnCancelPreview) btnCancelPreview.addEventListener('click', closePreviewModal);

            if (modalPreview) {
                modalPreview.addEventListener('click', function(e) {
                    if (e.target === modalPreview) closePreviewModal();
                });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') closePreviewModal();
            });

            if (btnSummary && btnDetails) {
                btnSummary.addEventListener('click', function() {
                    btnSummary.classList.add('is-active');
                    btnDetails.classList.remove('is-active');
                    summaryView.style.display = 'block';
                    detailsView.style.display = 'none';
                });

                btnDetails.addEventListener('click', function() {
                    btnDetails.classList.add('is-active');
                    btnSummary.classList.remove('is-active');
                    summaryView.style.display = 'none';
                    detailsView.style.display = 'block';
                });
            }

            /* =====================================================
               9. FORM SUBMIT (UI/UX SIMULATOR / AJAX READY)
            ===================================================== */
            function formatRupiah(num) {
                const n = Number(num) || 0;
                return 'Rp ' + Math.round(n).toLocaleString('id-ID');
            }

            function formatPercentage(num) {
                const n = Number(num) || 0;
                return n.toFixed(1).replace('.', ',') + '%';
            }

            function renderAdditionalComponentsPreview(components) {
                const container = document.getElementById('previewAdditionalComponentsContainer');
                if (!container) return;
                container.innerHTML = '';
                if (!Array.isArray(components) || components.length === 0) return;

                components.forEach(function(component) {
                    const componentElement = document.createElement('div');
                    componentElement.className = 'preview-component';
                    componentElement.style.marginBottom = '12px';

                    const priceTypeLabel = component.price_type === 'hpp_margin' ? 'HPP + Margin' :
                        'Harga Satuan';
                    const salePricePerUnit = Number(component.sale_price_per_unit) || 0;
                    const subtotal = Number(component.subtotal) || 0;
                    const qty = Number(component.qty) || 0;

                    let additionalInfo = '';
                    if (component.price_type === 'hpp_margin') {
                        const hppPerUnit = Number(component.hpp_per_unit) || 0;
                        const margin = Number(component.margin) || 0;
                        const totalHpp = Number(component.total_hpp) || 0;
                        const profit = Number(component.profit) || 0;

                        additionalInfo = `
                            <div class="col-md-6">
                                <small class="text-muted">HPP / Unit</small>
                                <div>${formatRupiah(hppPerUnit)}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Margin</small>
                                <div>${formatPercentage(margin)}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Total HPP</small>
                                <div>${formatRupiah(totalHpp)}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Profit</small>
                                <div>${formatRupiah(profit)}</div>
                            </div>
                        `;
                    }

                    componentElement.innerHTML = `
                        <div class="preview-component-header">
                            <strong>${component.type ? (component.type.toUpperCase() + ': ') : ''}${component.name || 'KOMPONEN TAMBAHAN'}</strong>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted">Nama Komponen</small>
                                <div>${component.name || '-'}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Qty</small>
                                <div>${qty.toLocaleString('id-ID')}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Jenis Harga</small>
                                <div>${priceTypeLabel}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Harga Jual / Unit</small>
                                <div>${formatRupiah(salePricePerUnit)}</div>
                            </div>
                            ${additionalInfo}
                            <div class="col-md-6">
                                <small class="text-muted">Subtotal</small>
                                <div>${formatRupiah(subtotal)}</div>
                            </div>
                        </div>
                    `;
                    container.appendChild(componentElement);
                });
            }

            function renderAdditionalComponentsHppPreview(components) {
                const container = document.getElementById('previewAdditionalComponentsHppContainer');
                if (!container) return;
                container.innerHTML = '';
                if (!Array.isArray(components) || components.length === 0) return;

                components.forEach(function(component) {
                    if (component.price_type !== 'hpp_margin') return;

                    const hppPerUnit = Number(component.hpp_per_unit) || 0;
                    const totalHpp = Number(component.total_hpp) || 0;
                    const profit = Number(component.profit) || 0;
                    const margin = Number(component.margin_percentage) || 0;

                    const componentElement = document.createElement('div');
                    componentElement.className = 'preview-component';
                    componentElement.style.marginBottom = '12px';

                    componentElement.innerHTML = `
                        <div class="preview-component-header">
                            <strong>${component.type ? (component.type.toUpperCase() + ': ') : ''}${component.name || '-'}</strong>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted">HPP / Unit</small>
                                <div>${formatRupiah(hppPerUnit)}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Total HPP</small>
                                <div>${formatRupiah(totalHpp)}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Profit</small>
                                <div>${formatRupiah(profit)}</div>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted">Margin</small>
                                <div>${formatPercentage(margin)}</div>
                            </div>
                        </div>
                    `;
                    container.appendChild(componentElement);
                });
            }

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                calculateButton.disabled = true;
                calculateButton.textContent = 'Menghitung...';

                try {
                    const formData = new FormData(form);

                    // Khusus mode display, sinkronkan kuantiti paket dan production mode
                    const catOption = categorySelect.find('option:selected');
                    const catName = (catOption.data('name') || catOption.text() || '').trim()
                        .toLowerCase();
                    const isDisplay = catName.includes('display');
                    if (isDisplay) {
                        const dQty = displayQtyInput ? (parseInt(displayQtyInput.value) || 1) : 1;
                        formData.set('qty', dQty);
                        formData.set('production_mode', 'display');
                    }

                    // Bersihkan tanda titik pada diskon nominal
                    const discountType = formData.get('discount_type');
                    if (discountType === 'nominal') {
                        const rawDisc = String(formData.get('discount_value') || '').replace(/\./g, '');
                        formData.set('discount_value', rawDisc);
                    }

                    // Bersihkan tanda titik pada input komponen tambahan
                    const additionalComponentInputs = compContainer.querySelectorAll(
                        '.additional-component-unit-price, .additional-component-hpp'
                    );
                    additionalComponentInputs.forEach(function(input) {
                        const fieldName = input.name;
                        const rawVal = String(input.value || '').replace(/\./g, '');
                        formData.set(fieldName, rawVal);
                    });

                    // Request ke backend storeA3plus
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const res = await response.json();

                    if (!response.ok || !res.success) {
                        const errMsg = res.message || (res.errors ? Object.values(res.errors).flat()
                            .join('<br>') : 'Gagal menghitung estimasi.');
                        if (typeof showToast === 'function') {
                            showToast('error', errMsg);
                        } else {
                            alert(errMsg);
                        }
                        return;
                    }

                    const resultData = res.data;

                    // =========================================================
                    // POPULATE MODAL DATA
                    // =========================================================
                    // Summary Tab
                    document.getElementById('summaryLocationName').textContent = resultData
                        .location_name || '-';
                    document.getElementById('summaryPriceType').textContent = resultData.price_type ||
                        '-';
                    document.getElementById('summaryMaterialName').textContent = resultData
                        .material_name || '-';
                    document.getElementById('summaryPrintSpec').textContent = resultData.color_side ||
                        '-';
                    document.getElementById('summaryFinishing').textContent = resultData
                        .finishing_summary || '-';
                    document.getElementById('summaryQty').textContent =
                        `${(resultData.qty || 0).toLocaleString('id-ID')} pcs`;
                    document.getElementById('summaryImposition').textContent = resultData
                        .imposition_info || '-';
                    document.getElementById('summaryTotalSheets').textContent =
                        `${(resultData.total_sheets || 0).toLocaleString('id-ID')} Lembar A3+`;

                    document.getElementById('summaryUnitPrice').textContent = formatRupiah(resultData
                        .unit_price_sheet);
                    document.getElementById('summarySubtotal').textContent = formatRupiah(resultData
                        .subtotal);
                    document.getElementById('summaryDiscount').textContent = formatRupiah(resultData
                        .discount_amount);
                    document.getElementById('summaryPricePerPcs').textContent = formatRupiah(resultData
                        .price_per_pcs) + ' /pcs';
                    document.getElementById('summaryGrandTotal').textContent = formatRupiah(resultData
                        .grand_total);

                    document.getElementById('summaryTotalHpp').textContent = formatRupiah(resultData
                        .total_hpp);
                    document.getElementById('summaryProfit').textContent = formatRupiah(resultData
                        .profit);
                    document.getElementById('summaryMargin').textContent = formatPercentage(resultData
                        .margin);
                    document.getElementById('summaryMarkup').textContent = formatPercentage(resultData
                        .markup);

                    // Details Tab
                    document.getElementById('previewLocationName').textContent = resultData
                        .location_name || '-';
                    document.getElementById('previewMaterialName').textContent = resultData
                        .material_name || '-';
                    document.getElementById('previewColorSide').textContent = resultData.color_side ||
                        '-';
                    document.getElementById('previewPriceType').textContent = resultData.price_type ||
                        '-';
                    document.getElementById('previewLeadTime').textContent = resultData.lead_time ||
                        '-';

                    document.getElementById('previewMode').textContent = resultData.production_mode ||
                        '-';
                    document.getElementById('previewFinishedSize').textContent = resultData
                        .finished_size || '-';
                    document.getElementById('previewUpCount').textContent = resultData.up_count ?
                        `${resultData.up_count} objek/lembar` : 'Direct Sheet';
                    document.getElementById('previewOrientation').textContent = resultData
                        .orientation || 'Normal';
                    document.getElementById('previewTotalSheets').textContent =
                        `${(resultData.total_sheets || 0).toLocaleString('id-ID')} Lembar A3+`;

                    document.getElementById('previewPrintBasis').textContent =
                        `${(resultData.total_sheets || 0).toLocaleString('id-ID')} lembar A3+`;
                    document.getElementById('previewPrintUnitPrice').textContent = formatRupiah(
                        resultData.unit_price_sheet);
                    document.getElementById('previewPrintSubtotal').textContent = formatRupiah(
                        resultData.print_subtotal);

                    document.getElementById('previewLaminationName').textContent = resultData
                        .lamination_name || 'Tanpa Laminasi';
                    document.getElementById('previewLaminationUnitPrice').textContent = resultData
                        .lamination_subtotal > 0 ? formatRupiah(resultData.lamination_subtotal /
                            resultData.total_sheets) : '—';
                    document.getElementById('previewLaminationSubtotal').textContent = formatRupiah(
                        resultData.lamination_subtotal);

                    document.getElementById('previewCuttingName').textContent = resultData
                        .cutting_name || 'Tanpa Cutting';
                    if (resultData.is_potong) {
                        document.getElementById('previewCuttingUnitPrice').textContent = resultData
                            .cutting_subtotal > 0 ?
                            `${formatRupiah(resultData.cutting_subtotal)} (Min)` : '—';
                    } else {
                        document.getElementById('previewCuttingUnitPrice').textContent = resultData
                            .cutting_subtotal > 0 ? formatRupiah(resultData.cutting_subtotal /
                                resultData
                                .total_sheets) : '—';
                    }
                    document.getElementById('previewCuttingSubtotal').textContent = formatRupiah(
                        resultData.cutting_subtotal);

                    // Render Komponen Tambahan (Harga Jual & HPP)
                    renderAdditionalComponentsPreview(resultData.additional_components);
                    renderAdditionalComponentsHppPreview(resultData.additional_components);

                    document.getElementById('previewSubtotal').textContent = formatRupiah(resultData
                        .subtotal);
                    document.getElementById('previewDiscount').textContent = formatRupiah(resultData
                        .discount_amount);
                    document.getElementById('previewGrandTotal').textContent = formatRupiah(resultData
                        .grand_total);
                    document.getElementById('previewPricePerPcs').textContent = formatRupiah(resultData
                        .price_per_pcs) + ' /pcs';

                    document.getElementById('previewPrintHpp').textContent = formatRupiah(resultData
                        .print_hpp || resultData.print_total_hpp);
                    document.getElementById('previewLaminationHpp').textContent = formatRupiah(
                        resultData.lamination_hpp);
                    document.getElementById('previewCuttingHpp').textContent = formatRupiah(resultData
                        .cutting_hpp);
                    document.getElementById('previewTotalHpp').textContent = formatRupiah(resultData
                        .total_hpp);

                    document.getElementById('previewProfit').textContent = formatRupiah(resultData
                        .profit);
                    document.getElementById('previewMargin').textContent = formatPercentage(resultData
                        .margin);
                    document.getElementById('previewMarkup').textContent = formatPercentage(resultData
                        .markup);

                    // Show Summary as default tab & open modal
                    btnSummary.click();
                    openPreviewModal();

                } catch (err) {
                    console.error('Calculation Error:', err);
                    if (typeof showToast === 'function') {
                        showToast('error', 'Terjadi kesalahan sistem saat menghitung estimasi.');
                    } else {
                        alert('Terjadi kesalahan: ' + err.message);
                    }
                } finally {
                    calculateButton.disabled = false;
                    calculateButton.textContent = 'Hitung Estimasi';
                }
            });

        });

        $(document).on('wheel', 'input[type="number"]', function() {
            this.blur();
        });
    </script>
@endsection

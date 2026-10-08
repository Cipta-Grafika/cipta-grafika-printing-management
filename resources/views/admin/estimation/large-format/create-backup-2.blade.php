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
                            Buat estimasi biaya produksi untuk kebutuhan cetak
                            menggunakan mesin Large Format.
                        </p>

                    </div>

                </section>


                <div class="grid">

                    <section class="col-12 card">

                        {{-- CARD HEADER --}}
                        <div class="card-head">

                            <div class="card-title-wrap">

                                <span class="eyebrow">
                                    ESTIMASI LARGE FORMAT
                                </span>

                            </div>

                        </div>


                        {{-- FORM --}}
                        <form action="{{ route('admin_store_lf') }}" method="POST" id="largeFormatEstimationForm">

                            @csrf


                            {{-- =========================================================
                                INFORMASI ESTIMASI
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

                                            <option value=""></option>

                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                    <div class="field" id="vendorField" style="margin-bottom: 20px;">
                                        <label class="field-label" for="vendor_id">
                                            Vendor
                                        </label>

                                        <select id="vendor_id" name="vendor_id" class="select select2">
                                            <option value=""></option>

                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->id }}">
                                                    {{ $vendor->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>


                                    {{-- Pilihan Harga --}}
                                    <div class="field" style="margin-bottom: 20px;">

                                        <label class="field-label" for="price_type">
                                            Pilihan Harga
                                            <span class="req">*</span>
                                        </label>

                                        <select id="price_type" name="price_type" class="select select2" required>
                                            <option value=""></option>

                                            <option value="general">
                                                Harga Umum
                                            </option>

                                            <option value="division">
                                                Harga Divisi
                                            </option>
                                        </select>

                                    </div>

                                </div>

                            </div>


                            {{-- =========================================================
                                SPESIFIKASI CETAK
                            ========================================================== --}}
                            <div class="form-section">

                                <div class="form-section-head">

                                    <span class="eyebrow">
                                        SPESIFIKASI CETAK
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

                                            <option value=""></option>

                                            {{-- @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach --}}

                                        </select>

                                    </div>


                                    {{-- Material --}}
                                    <div class="field" style="margin-bottom: 20px;">

                                        <label class="field-label" for="material_id">
                                            Material
                                            <span class="req">*</span>
                                        </label>

                                        <select id="material_id" name="material_id" class="select select2" required>

                                            <option value=""></option>

                                        </select>

                                        <small class="field-help">
                                            Pilih lokasi dan kategori terlebih dahulu.
                                        </small>

                                    </div>

                                </div>


                                <div class="form-grid">

                                    {{-- Panjang --}}
                                    <div class="field" style="margin-bottom: 20px;">

                                        <label class="field-label" for="length">
                                            Panjang (cm)
                                            <span class="req">*</span>
                                        </label>

                                        <div class="input-suffix">

                                            <input id="length" name="length" class="input" type="number"
                                                min="0" step="0.01" inputmode="decimal" placeholder="Contoh: 300"
                                                value="{{ old('length') }}" required>

                                        </div>

                                    </div>


                                    {{-- Lebar --}}
                                    <div class="field" style="margin-bottom: 20px;">

                                        <label class="field-label" for="width">
                                            Lebar (cm)
                                            <span class="req">*</span>
                                        </label>

                                        <div class="input-suffix">

                                            <input id="width" name="width" class="input" type="number"
                                                min="0" step="0.01" inputmode="decimal"
                                                placeholder="Contoh: 150" value="{{ old('width') }}" required>

                                        </div>

                                    </div>

                                </div>


                                <div class="form-grid">

                                    {{-- Quantity --}}
                                    <div class="field" style="margin-bottom: 20px;">

                                        <label class="field-label" for="qty">
                                            Jumlah (pcs)
                                            <span class="req">*</span>
                                        </label>

                                        <div class="input-suffix">

                                            <input id="qty" name="qty" class="input" type="number"
                                                min="1" step="1" inputmode="numeric"
                                                placeholder="Contoh: 2" value="{{ old('qty') }}" required>

                                        </div>

                                    </div>


                                    {{-- Lebar Bahan --}}
                                    <div class="field" style="margin-bottom: 20px;">

                                        <label class="field-label" for="material_size_id">
                                            Lebar Bahan Tersedia
                                            <span class="req">*</span>
                                        </label>

                                        <select id="material_size_id" name="material_size_id" class="select select2"
                                            required>

                                            <option value=""></option>

                                        </select>

                                        <small class="field-help">
                                            Pilih material terlebih dahulu.
                                        </small>

                                    </div>

                                </div>

                            </div>

                            <div class="form-grid">

                                {{-- Laminasi --}}
                                <div class="field" style="margin-bottom: 20px;">
                                    <label class="field-label" for="lamination_id">
                                        Laminasi
                                    </label>

                                    <select id="lamination_id" name="lamination_id" class="select select2">
                                        <option value="none">Tanpa Laminasi</option>
                                    </select>

                                    <small class="field-help">
                                        Pilih lokasi dan kategori terlebih dahulu.
                                    </small>
                                </div>

                                {{-- Ukuran Laminasi --}}
                                <div class="field" style="margin-bottom: 20px;">
                                    <label class="field-label" for="lamination_size_id">
                                        Ukuran Laminasi
                                    </label>

                                    <select id="lamination_size_id" name="lamination_size_id" class="select select2">
                                        <option value=""></option>
                                    </select>

                                    <small class="field-help">
                                        Pilih laminasi terlebih dahulu.
                                    </small>
                                </div>

                            </div>

                            {{-- =========================================================
                                KOMPONEN TAMBAHAN
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
                                            Tambahkan komponen tambahan yang digunakan dalam estimasi.
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn--primary" id="addAdditionalComponentButton">
                                        + Tambah Komponen
                                    </button>
                                </div>

                                <div id="additionalComponentsContainer">

                                    {{-- Komponen pertama akan dibuat oleh JavaScript --}}

                                </div>

                            </div>

                            {{-- =========================================================
                                HARGA MATERIAL
                            ========================================================== --}}
                            <div class="form-section">

                                <div class="form-section-head">

                                    <span class="eyebrow">
                                        HARGA MATERIAL
                                    </span>

                                </div>


                                <div class="estimation-info">

                                    <div class="estimation-info__item">

                                        <span class="estimation-info__label">
                                            Harga Material
                                        </span>

                                        <strong class="estimation-info__value" id="materialPrice">
                                            —
                                        </strong>

                                        <span class="estimation-info__unit">
                                            / m²
                                        </span>

                                    </div>

                                </div>

                            </div>


                            {{-- =========================================================
                                DISKON
                            ========================================================== --}}
                            <div class="form-section">

                                <div class="form-section-head">

                                    <span class="eyebrow">
                                        DISKON
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

                                                    <strong>
                                                        Tanpa Diskon
                                                    </strong>

                                                    <small>
                                                        Harga normal
                                                    </small>

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

                                                    <strong>
                                                        Diskon Persen
                                                    </strong>

                                                    <small>
                                                        Potongan berdasarkan persentase
                                                    </small>

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

                                                    <strong>
                                                        Diskon Nominal
                                                    </strong>

                                                    <small>
                                                        Potongan berdasarkan nominal
                                                    </small>

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
                                                type="text" inputmode="decimal" placeholder="" disabled>

                                        </div>

                                    </div>


                                    {{-- Estimasi Waktu --}}
                                    <div class="field" style="margin-bottom: 20px;">

                                        <label class="field-label" for="estimation_time">
                                            Estimasi Waktu
                                            <span class="req">*</span>
                                        </label>

                                        <div class="input-suffix">

                                            <input id="estimation_time" name="estimation_time" class="input"
                                                type="number" min="1" step="1" inputmode="numeric"
                                                placeholder="Contoh: 2" value="{{ old('estimation_time') }}" required>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            {{-- =========================================================
                                ACTION
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

    <div class="modal-overlay" id="modalEstimationPreview">

        <div class="modal modal-estimation">

            {{-- =========================================================
            HEADER
            ========================================================== --}}
            <div class="modal-header">

                <div>
                    <span class="eyebrow">ESTIMASI · LARGE FORMAT</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseEstimationPreview" aria-label="Tutup">
                    <svg viewBox="0 0 24 24">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>

            </div>

            {{-- TOGGLE PREVIEW --}}
            <div class="estimation-preview-toggle">
                <button type="button" class="estimation-preview-toggle-btn is-active" id="btnSummaryEstimation">
                    Summary Estimasi
                </button>

                <button type="button" class="estimation-preview-toggle-btn" id="btnDetailsEstimation">
                    Details Estimasi
                </button>
            </div>


            {{-- =========================================================
            BODY
            ========================================================== --}}
            <div class="modal-body">

                {{-- SUMMARY ESTIMASI --}}
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
                                <div id="summaryEngineName">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Lokasi</small>
                                <div id="summaryLocationName">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Vendor</small>
                                <div id="summaryVendorName">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Kategori</small>
                                <div id="summaryCategoryName">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Material</small>
                                <div id="summaryMaterialName">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Tipe Harga</small>
                                <div id="summaryPriceType">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Ukuran Produk</small>
                                <div id="summarySize">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Qty</small>
                                <div id="summaryQty">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Luas Tagihan</small>
                                <div id="summaryBillingArea">-</div>
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
                                <small class="text-muted">Harga / m²</small>
                                <div id="summaryMaterialPrice">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Subtotal</small>
                                <div id="summarySubtotal">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Diskon</small>
                                <div id="summaryDiscount">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Harga / PCS</small>
                                <div id="summaryPricePerPcs">-</div>
                            </div>

                            <div class="col-md-8">
                                <small class="text-muted">Grand Total</small>
                                <strong id="summaryGrandTotal" style="font-size: 1.15rem;">
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
                                <strong id="summaryProfit">-</strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Margin</small>
                                <div id="summaryMargin">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Markup</small>
                                <div id="summaryMarkup">-</div>
                            </div>

                        </div>

                    </div>

                </div>

                {{-- DETAILS ESTIMASI --}}
                <div id="estimationDetails" class="estimation-preview-content" style="display: none;">

                    {{-- =====================================================
                    INFORMASI PRODUKSI
                    ====================================================== --}}
                    <div class="preview-section">

                        <div class="preview-section-header">
                            <div>
                                <h3 class="preview-section-title">
                                    INFORMASI PRODUKSI
                                </h3>
                                {{-- <h3 class="preview-section-title">
                                    Sumber Produksi
                                </h3> --}}
                            </div>
                        </div>

                        <div class="row g-3">

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Mesin
                                </small>
                                <div id="previewEngineName">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Lokasi
                                </small>
                                <div id="previewLocationName">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">Vendor</small>
                                <div id="previewVendorName">-</div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Kategori
                                </small>
                                <div id="previewCategoryName">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Material
                                </small>
                                <div id="previewMaterialName">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Tipe Harga
                                </small>
                                <div id="previewPriceType">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Estimasi Waktu
                                </small>
                                <div id="previewEstimationTime">
                                    -
                                </div>
                            </div>

                        </div>

                    </div>


                    <hr>


                    {{-- =====================================================
                    SPESIFIKASI PRODUK
                    ====================================================== --}}
                    <div class="preview-section">

                        <div class="preview-section-header">
                            <div>
                                <h3 class="preview-section-title">
                                    SPESIFIKASI PRODUK
                                </h3>
                                {{-- <h3 class="preview-section-title">
                                    Detail Produksi
                                </h3> --}}
                            </div>
                        </div>

                        <div class="row g-3">

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Qty
                                </small>
                                <div id="previewQty">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Ukuran Produk
                                </small>
                                <div id="previewSize">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Lebar Bahan
                                </small>
                                <div id="previewMaterialWidth">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Objek / Baris
                                </small>
                                <div id="previewObjectsPerRow">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Jumlah Baris
                                </small>
                                <div id="previewTotalRows">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Panjang Produksi
                                </small>
                                <div id="previewProductionLength">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Lebar Produksi
                                </small>
                                <div id="previewProductionWidth">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Luas Hasil Hitung
                                </small>
                                <div id="previewProductionArea">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Minimum Charge
                                </small>
                                <div id="previewMinimumCharge">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Luas Tagihan Final
                                </small>
                                <div id="previewBillingArea">
                                    -
                                </div>
                            </div>

                        </div>

                    </div>


                    <hr>


                    {{-- =====================================================
                    KOMPONEN HARGA
                    ====================================================== --}}
                    <div class="preview-section">

                        <div class="preview-section-header">
                            <div>
                                <h3 class="preview-section-title">
                                    KOMPONEN HARGA
                                </h3>
                                {{-- <h3 class="preview-section-title">
                                    Rincian Harga Jual
                                </h3> --}}
                            </div>
                        </div>


                        {{-- =================================================
                        MATERIAL
                        ================================================== --}}
                        <div class="preview-component">

                            <div class="preview-component-header">
                                <strong>
                                    MATERIAL
                                </strong>
                            </div>

                            <div class="row g-3">

                                <div class="col-md-4">
                                    <small class="text-muted">
                                        Nama Material
                                    </small>
                                    <div id="previewComponentMaterialName">
                                        -
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted">
                                        Luas Tagihan
                                    </small>
                                    <div id="previewComponentMaterialArea">
                                        -
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted">
                                        Harga Satuan
                                    </small>
                                    <div id="previewMaterialPrice">
                                        -
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted">
                                        Subtotal Material
                                    </small>
                                    <div id="previewMaterialSubtotal">
                                        -
                                    </div>
                                </div>

                            </div>

                        </div>


                        {{-- =================================================
                        LAMINASI
                        ================================================== --}}
                        <div id="previewLaminationContainer"></div>

                        <!-- KOMPONEN TAMBAHAN -->
                        <div id="previewAdditionalComponentsContainer"></div>

                    </div>


                    <hr>


                    {{-- =====================================================
                    RINGKASAN KEUANGAN
                    ====================================================== --}}
                    <div class="preview-section">

                        <div class="preview-section-header">
                            <div>
                                <h3 class="preview-section-title">
                                    RINGKASAN KEUANGAN
                                </h3>
                                {{-- <h3 class="preview-section-title">
                                    Ringkasan Harga Jual
                                </h3> --}}
                            </div>
                        </div>

                        <div class="row g-3">

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Subtotal Harga Jual
                                </small>
                                <div id="previewSubtotal">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Diskon
                                </small>
                                <div id="previewDiscount">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Grand Total
                                </small>
                                <strong id="previewGrandTotal">
                                    -
                                </strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Harga Per PCS
                                </small>
                                <div id="previewPricePerPcs">
                                    -
                                </div>
                            </div>

                        </div>

                    </div>


                    <hr>


                    {{-- =====================================================
                    HPP
                    ====================================================== --}}
                    <div class="preview-section">

                        <div class="preview-section-header">
                            <div>
                                <h3 class="preview-section-title">
                                    HPP
                                </h3>
                                {{-- <h3 class="preview-section-title">
                                    Harga Pokok Produksi
                                </h3> --}}
                            </div>
                        </div>


                        {{-- MATERIAL HPP --}}
                        <div class="preview-component">

                            <div class="preview-component-header">
                                <strong>
                                    HPP MATERIAL
                                </strong>
                            </div>

                            <div class="row g-3">

                                <div class="col-md-4">
                                    <small class="text-muted">
                                        HPP / m²
                                    </small>
                                    <div id="previewMaterialHppPerM2">
                                        -
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted">
                                        Total HPP Material
                                    </small>
                                    <div id="previewMaterialTotalHpp">
                                        -
                                    </div>
                                </div>

                            </div>

                        </div>


                        {{-- LAMINASI HPP --}}
                        <div class="preview-component" id="previewLaminationHppContainer" style="display: none;">

                            <div class="preview-component-header">
                                <strong>
                                    HPP LAMINASI
                                </strong>
                            </div>

                            <div class="row g-3">

                                <div class="col-md-4">
                                    <small class="text-muted">
                                        HPP / m
                                    </small>
                                    <div id="previewLaminationHppPerMeter">
                                        -
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <small class="text-muted">
                                        Total HPP Laminasi
                                    </small>
                                    <div id="previewLaminationTotalHpp">
                                        -
                                    </div>
                                </div>

                            </div>

                        </div>

                        <!-- KOMPONEN TAMBAHAN HPP -->
                        <div id="previewAdditionalComponentsHppContainer"></div>

                        {{-- TOTAL HPP --}}
                        <div class="preview-component">

                            <div class="row g-3">

                                <div class="col-md-4">
                                    <small class="text-muted">
                                        Total HPP
                                    </small>
                                    <strong id="previewTotalHpp">
                                        -
                                    </strong>
                                </div>

                            </div>

                        </div>

                    </div>


                    <hr>


                    {{-- =====================================================
                    PROFITABILITY
                    ====================================================== --}}
                    <div class="preview-section">

                        <div class="preview-section-header">
                            <div>
                                <h3 class="preview-section-title">
                                    PROFITABILITAS
                                </h3>
                                {{-- <h3 class="preview-section-title">
                                    Analisis Keuntungan
                                </h3> --}}
                            </div>
                        </div>

                        <div class="row g-3">

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Total Profit
                                </small>
                                <strong id="previewProfit">
                                    -
                                </strong>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Margin
                                </small>
                                <div id="previewMargin">
                                    -
                                </div>
                            </div>

                            <div class="col-md-4">
                                <small class="text-muted">
                                    Markup (thd HPP)
                                </small>
                                <div id="previewMarkup">
                                    -
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            {{-- =========================================================
            FOOTER
            ========================================================== --}}
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCancelEstimationPreview"
                    style="
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    gap: 10px;
                ">
                    Kembali
                </button>

                <button type="button" class="btn btn--primary" id="btnSaveEstimation"
                    style="
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    gap: 10px;
                ">
                    Simpan Estimasi
                </button>

            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script>
        /* =========================================================
                                                                                                                                                                                                                                                                                                                                                            SELECT2
                                                                                                                                                                                                                                                                                                                                                        ========================================================== */

        $(document).ready(function() {

            /* =====================================================
               SELECT2s
            ===================================================== */

            $('.select2').select2({
                width: '100%',
                placeholder: 'Pilih...'
            });


            /* =====================================================
               ELEMENT
            ===================================================== */

            const locationSelect =
                $('#location_id');

            const vendorSelect = $('#vendor_id');
            const vendorField = $('#vendorField');

            const categorySelect =
                $('#category_id');

            const materialSelect =
                $('#material_id');

            const materialSizeSelect =
                $('#material_size_id');

            const laminationSelect =
                $('#lamination_id');

            const laminationSizeSelect =
                $('#lamination_size_id');


            /* =====================================================
               URL
            ===================================================== */

            const materialsUrl =
                "{{ route('admin_get_materials') }}";

            const materialSizesUrl =
                "{{ route('admin_get_material_sizes') }}";

            const laminationsUrl =
                "{{ route('admin_get_laminations') }}";

            const laminationSizesUrl =
                "{{ route('admin_get_laminations_sizes') }}";

            const categoriesUrl = "{{ route('admin_get_lf_categories') }}"

            const engineName = 'Large Format';

            function loadCategories() {
                categorySelect
                    .empty()
                    .append('<option value=""></option>')
                    .trigger('change');

                fetch(categoriesUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            showToast(
                                'error',
                                data.message || 'Gagal mengambil kategori.'
                            );
                            return;
                        }

                        data.data.forEach(category => {
                            categorySelect.append(
                                new Option(
                                    category.name,
                                    category.id,
                                    false,
                                    false
                                )
                            );
                        });

                        categorySelect.trigger('change');
                        toggleDisplayFields();
                    })
                    .catch(error => {
                        console.error(error);

                        showToast(
                            'error',
                            'Gagal mengambil data kategori.'
                        );
                    });
            }

            function toggleDisplayFields() {

                const selectedCategory = categorySelect
                    .find('option:selected')
                    .text()
                    .trim()
                    .toLowerCase();

                const isDisplay = selectedCategory === 'display';

                $('#length').closest('.field').toggle(!isDisplay);
                $('#width').closest('.field').toggle(!isDisplay);
                $('#material_size_id').closest('.field').toggle(!isDisplay);

                if (isDisplay) {
                    $('#length').val('');
                    $('#width').val('');

                    materialSizeSelect
                        .val(null)
                        .trigger('change');
                }
            }

            function toggleVendorField() {
                const locationName = locationSelect
                    .find('option:selected')
                    .text()
                    .trim()
                    .toLowerCase();

                if (locationName === 'outsourcing') {
                    vendorField.show();
                } else {
                    vendorSelect.val('').trigger('change');
                    vendorField.hide();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | ERROR MESSAGE
            |--------------------------------------------------------------------------
            */

            function getErrorMessage(result, fallback) {

                if (result?.message) {
                    return result.message;
                }

                if (result?.errors) {

                    const firstError =
                        Object.values(result.errors)[0];

                    if (Array.isArray(firstError) && firstError.length) {
                        return firstError[0];
                    }
                }

                return fallback;
            }

            /* =====================================================
               LOAD MATERIAL
               Filter:
               Lokasi + Kategori
            ===================================================== */

            async function loadMaterials() {

                const locationId =
                    locationSelect.val();

                const categoryId =
                    categorySelect.val();

                const vendorId =
                    vendorSelect.val();

                const locationName =
                    locationSelect
                    .find('option:selected')
                    .text()
                    .trim()
                    .toLowerCase();

                const isOutsourcing =
                    locationName === 'outsourcing';


                /* =================================================
                   RESET MATERIAL
                ================================================= */

                materialSelect
                    .empty()
                    .append('<option value=""></option>');

                materialSelect
                    .val(null)
                    .trigger('change');


                /* =================================================
                   RESET MATERIAL SIZE
                ================================================= */

                materialSizeSelect
                    .empty()
                    .append('<option value=""></option>');

                materialSizeSelect
                    .val(null)
                    .trigger('change');


                /* =================================================
                   CEK PARAMETER
                ================================================= */

                if (
                    !locationId ||
                    !categoryId
                ) {
                    return;
                }

                /*
                 * Khusus Outsourcing:
                 * Vendor wajib dipilih sebelum mengambil Material.
                 */
                if (
                    isOutsourcing &&
                    !vendorId
                ) {
                    return;
                }


                try {

                    /* =============================================
                       BUILD URL
                    ============================================= */

                    const url =
                        new URL(
                            materialsUrl,
                            window.location.origin
                        );

                    url.searchParams.set(
                        'location_id',
                        locationId
                    );

                    url.searchParams.set(
                        'category_id',
                        categoryId
                    );

                    url.searchParams.set(
                        'engine',
                        engineName
                    );

                    /*
                     * Vendor hanya dikirim untuk Outsourcing.
                     */
                    if (isOutsourcing) {
                        url.searchParams.set(
                            'vendor_id',
                            vendorId
                        );
                    }


                    /* =============================================
                       REQUEST
                    ============================================= */

                    const response =
                        await fetch(
                            url, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }
                        );


                    /* =============================================
                       CHECK RESPONSE
                    ============================================= */

                    if (!response.ok) {
                        throw new Error(
                            'Gagal mengambil data material.'
                        );
                    }


                    /* =============================================
                       RESPONSE JSON
                    ============================================= */

                    const result =
                        await response.json();


                    /* =============================================
                       CHECK SUCCESS
                    ============================================= */

                    if (!result.success) {
                        throw new Error(
                            'Data material tidak berhasil diambil.'
                        );
                    }


                    /* =============================================
                       INSERT MATERIAL
                    ============================================= */

                    result.data.forEach(
                        function(material) {

                            const option =
                                new Option(
                                    material.material_name,
                                    material.id,
                                    false,
                                    false
                                );

                            materialSelect.append(
                                option
                            );
                        }
                    );


                    /* =============================================
                       REFRESH SELECT2
                    ============================================= */

                    materialSelect.trigger('change');

                } catch (error) {

                    console.error(
                        'Load Material Error:',
                        error
                    );

                    showToast(
                        'error',
                        error.message ||
                        'Gagal mengambil data material.'
                    );
                }
            }


            /* =====================================================
               LOAD MATERIAL SIZE
               Filter:
               Lokasi + Kategori + Material
            ===================================================== */

            async function loadMaterialSizes() {

                const locationId =
                    locationSelect.val();

                const categoryId =
                    categorySelect.val();

                const materialId =
                    materialSelect.val();

                const vendorId =
                    vendorSelect.val();

                const locationName =
                    locationSelect
                    .find('option:selected')
                    .text()
                    .trim()
                    .toLowerCase();

                const isOutsourcing =
                    locationName === 'outsourcing';


                /* =================================================
                   RESET MATERIAL SIZE
                ================================================= */

                materialSizeSelect
                    .empty()
                    .append('<option value=""></option>');

                materialSizeSelect
                    .val(null)
                    .trigger('change');


                /* =================================================
                   CEK PARAMETER
                ================================================= */

                if (
                    !locationId ||
                    !categoryId ||
                    !materialId
                ) {
                    return;
                }


                /*
                 * Khusus Outsourcing:
                 * Vendor wajib dipilih sebelum mengambil Material Size.
                 */
                if (
                    isOutsourcing &&
                    !vendorId
                ) {
                    return;
                }


                try {

                    /* =============================================
                       BUILD URL
                    ============================================= */

                    const url =
                        new URL(
                            materialSizesUrl,
                            window.location.origin
                        );


                    url.searchParams.set(
                        'location_id',
                        locationId
                    );

                    url.searchParams.set(
                        'category_id',
                        categoryId
                    );

                    url.searchParams.set(
                        'material_id',
                        materialId
                    );

                    url.searchParams.set(
                        'engine',
                        engineName
                    );

                    /*
                     * Vendor hanya dikirim untuk Outsourcing.
                     */
                    if (isOutsourcing) {

                        url.searchParams.set(
                            'vendor_id',
                            vendorId
                        );

                    }


                    /* =============================================
                       REQUEST
                    ============================================= */

                    const response =
                        await fetch(
                            url, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }
                        );


                    /* =============================================
                       CHECK RESPONSE
                    ============================================= */

                    if (!response.ok) {

                        throw new Error(
                            'Gagal mengambil data lebar bahan.'
                        );

                    }


                    /* =============================================
                       RESPONSE JSON
                    ============================================= */

                    const result =
                        await response.json();


                    /* =============================================
                       CHECK SUCCESS
                    ============================================= */

                    if (!result.success) {

                        throw new Error(
                            'Data lebar bahan tidak berhasil diambil.'
                        );

                    }


                    /* =============================================
                       INSERT MATERIAL SIZE
                    ============================================= */

                    result.data.forEach(
                        function(size) {

                            const option =
                                new Option(
                                    size.width + ' cm',
                                    size.id,
                                    false,
                                    false
                                );

                            materialSizeSelect.append(
                                option
                            );

                        }
                    );


                    /* =============================================
                       REFRESH SELECT2
                    ============================================= */

                    materialSizeSelect.trigger(
                        'change'
                    );


                } catch (error) {

                    console.error(
                        'Load Material Size Error:',
                        error
                    );

                    showToast(
                        'error',
                        error.message ||
                        'Gagal mengambil data lebar bahan.'
                    );

                }

            }

            async function loadLaminations() {
                const locationId = locationSelect.val();
                const categoryId = categorySelect.val();

                // Hapus hanya option laminasi dinamis.
                // "Tanpa Laminasi" tetap dipertahankan.
                laminationSelect
                    .find('option')
                    .not('[value="none"]')
                    .remove();

                // Pastikan option "Tanpa Laminasi" selalu ada
                if (laminationSelect.find('option[value="none"]').length === 0) {
                    laminationSelect.prepend(
                        '<option value="none">Tanpa Laminasi</option>'
                    );
                }

                // Selalu kembali ke Tanpa Laminasi
                laminationSelect
                    .val('none')
                    .trigger('change');

                // Reset ukuran laminasi
                laminationSizeSelect
                    .empty()
                    .append('<option value=""></option>');

                laminationSizeSelect
                    .val(null)
                    .trigger('change');

                if (!locationId || !categoryId) {
                    return;
                }

                try {
                    const url =
                        new URL(
                            laminationsUrl,
                            window.location.origin
                        );

                    url.searchParams.set(
                        'location_id',
                        locationId
                    );

                    url.searchParams.set(
                        'category_id',
                        categoryId
                    );

                    const response =
                        await fetch(
                            url, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }
                        );

                    if (!response.ok) {
                        throw new Error(
                            'Gagal mengambil data laminasi.'
                        );
                    }

                    const result =
                        await response.json();

                    if (!result.success) {
                        throw new Error(
                            result.message ||
                            'Data laminasi tidak berhasil diambil.'
                        );
                    }

                    result.data.forEach(
                        function(lamination) {
                            const option =
                                new Option(
                                    lamination.name,
                                    lamination.id,
                                    false,
                                    false
                                );

                            laminationSelect.append(option);
                        }
                    );

                    // Tetap pilih Tanpa Laminasi setelah data selesai dimuat
                    laminationSelect
                        .val('none')
                        .trigger('change');

                } catch (error) {
                    console.error(
                        'Load Lamination Error:',
                        error
                    );

                    showToast(
                        'error',
                        error.message ||
                        'Gagal mengambil data laminasi.'
                    );
                }
            }

            /* =====================================================
               LOAD LAMINATION SIZE
               Filter:
               Lokasi + Kategori + Lamination
               Engine Large Format ditentukan oleh Controller
            ===================================================== */
            async function loadLaminationSizes() {

                const locationId =
                    locationSelect.val();

                const categoryId =
                    categorySelect.val();

                const laminationId =
                    laminationSelect.val();

                /* =================================================
                   RESET LAMINATION SIZE
                ================================================= */
                laminationSizeSelect
                    .empty()
                    .append('<option value=""></option>');

                laminationSizeSelect
                    .val(null)
                    .trigger('change');

                /* =================================================
                   CEK PARAMETER
                ================================================= */
                if (
                    !locationId ||
                    !categoryId ||
                    !laminationId ||
                    laminationId == 'none'
                ) {
                    return;
                }

                try {

                    /* =============================================
                       BUILD URL
                    ============================================= */
                    const url =
                        new URL(
                            laminationSizesUrl,
                            window.location.origin
                        );

                    url.searchParams.set(
                        'location_id',
                        locationId
                    );

                    url.searchParams.set(
                        'category_id',
                        categoryId
                    );

                    url.searchParams.set(
                        'lamination_id',
                        laminationId
                    );

                    /* =============================================
                       REQUEST
                    ============================================= */
                    const response =
                        await fetch(
                            url, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }
                        );

                    /* =============================================
                       CHECK RESPONSE
                    ============================================= */
                    if (!response.ok) {
                        throw new Error(
                            'Gagal mengambil ukuran laminasi.'
                        );
                    }

                    /* =============================================
                       RESPONSE JSON
                    ============================================= */
                    const result =
                        await response.json();

                    /* =============================================
                       CHECK SUCCESS
                    ============================================= */
                    if (!result.success) {
                        throw new Error(
                            result.message ||
                            'Ukuran laminasi tidak berhasil diambil.'
                        );
                    }

                    /* =============================================
                       INSERT LAMINATION SIZE
                    ============================================= */
                    result.data.forEach(
                        function(size) {

                            let label =
                                formatNumberResult(size.width) +
                                ' cm';

                            if (
                                size.length !== null &&
                                size.length !== undefined
                            ) {
                                label +=
                                    ' × ' +
                                    formatNumberResult(size.length) +
                                    ' cm';
                            }

                            const option =
                                new Option(
                                    label,
                                    size.id,
                                    false,
                                    false
                                );

                            laminationSizeSelect.append(
                                option
                            );
                        }
                    );

                    /* =============================================
                       REFRESH SELECT2
                    ============================================= */
                    laminationSizeSelect.trigger(
                        'change'
                    );

                } catch (error) {

                    console.error(
                        'Load Lamination Size Error:',
                        error
                    );

                    showToast(
                        'error',
                        error.message ||
                        'Gagal mengambil ukuran laminasi.'
                    );
                }
            }

            /* =====================================================
               EVENT LOKASI
            ===================================================== */

            locationSelect.on(
                'change',
                function() {

                    toggleVendorField();
                    loadMaterials();
                    loadLaminations();
                }
            );


            /* =====================================================
               EVENT KATEGORI
            ===================================================== */

            categorySelect.on(
                'change',
                function() {

                    toggleDisplayFields();
                    loadMaterials();
                    loadLaminations();
                }
            );

            /* =====================================================
            EVENT VENDOR
            ===================================================== */

            vendorSelect.on(
                'change',
                function() {
                    loadMaterials();
                }
            );


            /* =====================================================
       EVENT MATERIAL
    ===================================================== */

            materialSelect.on(
                'change',
                function() {

                    const selectedCategory = categorySelect
                        .find('option:selected')
                        .text()
                        .trim()
                        .toLowerCase();

                    // Khusus Display, tidak perlu memanggil loadMaterialSizes()
                    if (selectedCategory === 'display') {

                        materialSizeSelect
                            .empty()
                            .append('<option value=""></option>');

                        materialSizeSelect
                            .val(null)
                            .trigger('change');

                        return;
                    }

                    // Kategori selain Display tetap menggunakan alur lama
                    loadMaterialSizes();

                }
            );

            /* =====================================================
            EVENT LAMINATION
            ===================================================== */
            laminationSelect.on(
                'change',
                function() {
                    loadLaminationSizes();
                }
            );

            toggleVendorField();
            loadCategories();

            // =========================================================
            // KOMPONEN TAMBAHAN
            // =========================================================

            const addAdditionalComponentButton =
                document.getElementById(
                    'addAdditionalComponentButton'
                );

            const additionalComponentsContainer =
                document.getElementById(
                    'additionalComponentsContainer'
                );

            let additionalComponentIndex = 0;


            // =========================================================
            // TAMBAH KOMPONEN
            // =========================================================

            function addAdditionalComponent() {

                const index = additionalComponentIndex++;

                const componentHtml = `
                    <div
                        class="additional-component-item"
                        data-component-index="${index}"
                        style="
                            border: 1px solid rgba(255,255,255,0.08);
                            border-radius: 12px;
                            padding: 20px;
                            margin-bottom: 16px;
                        "
                    >

                        <div
                            style="
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                                gap: 12px;
                                margin-bottom: 18px;
                            "
                        >

                            <div
                                style="
                                    font-size: 15px;
                                    font-weight: 600;
                                "
                            >
                                Komponen Tambahan #
                                <span class="additional-component-number">
                                    ${index + 1}
                                </span>
                            </div>

                            <button
                                type="button"
                                class="btn btn-danger btn-remove-additional-component"
                            >
                                <i class="bi bi-trash3-fill"></i>
                            </button>

                        </div>


                        <div class="form-grid">

                            {{-- Tipe Komponen --}}
                            <div class="field">

                                <label
                                    class="field-label"
                                    for="additional_component_type_${index}"
                                >
                                    Tipe
                                </label>

                                <select
                                    id="additional_component_type_${index}"
                                    name="additional_components[${index}][type]"
                                    class="select select2 additional-component-type"
                                >
                                    <option value=""></option>

                                    <option value="Material">
                                        Material
                                    </option>

                                    <option value="Finishing">
                                        Finishing
                                    </option>

                                    <option value="Jasa Pemasangan">
                                        Jasa Pemasangan
                                    </option>
                                </select>

                            </div>

                            {{-- Nama Komponen --}}
                            <div class="field">

                                <label
                                    class="field-label"
                                    for="additional_component_name_${index}"
                                >
                                    Nama Komponen
                                </label>

                                <input
                                    type="text"
                                    id="additional_component_name_${index}"
                                    name="additional_components[${index}][name]"
                                    class="input additional-component-name"
                                    placeholder="Contoh: Mata Ayam"
                                    autocomplete="off"
                                >

                            </div>


                            {{-- Qty --}}
                            <div class="field">

                                <label
                                    class="field-label"
                                    for="additional_component_qty_${index}"
                                >
                                    Qty
                                </label>

                                <input
                                    type="number"
                                    id="additional_component_qty_${index}"
                                    name="additional_components[${index}][qty]"
                                    class="input additional-component-qty"
                                    min="1"
                                    step="1"
                                    value="1"
                                >

                            </div>


                            {{-- Tipe Harga --}}
                            <div class="field">

                                <label
                                    class="field-label"
                                    for="additional_component_price_type_${index}"
                                >
                                    Tipe Harga
                                </label>

                                <select
                                    id="additional_component_price_type_${index}"
                                    name="additional_components[${index}][price_type]"
                                    class="select select2 additional-component-price-type"
                                >
                                    <option value="unit_price">
                                        Harga Satuan (Rp)
                                    </option>

                                    <option value="hpp_margin">
                                        HPP + Margin %
                                    </option>
                                </select>

                            </div>

                        </div>


                        {{-- =========================================
                            HARGA SATUAN
                        ========================================== --}}

                        <div
                            class="additional-component-unit-price-wrapper"
                            style="margin-top: 18px;"
                        >

                            <div class="field">

                                <label
                                    class="field-label"
                                    for="additional_component_unit_price_${index}"
                                >
                                    Harga Satuan
                                </label>

                                <input
                                    type="text"
                                    id="additional_component_unit_price_${index}"
                                    name="additional_components[${index}][unit_price]"
                                    class="input additional-component-unit-price"
                                    inputmode="numeric"
                                    placeholder="Contoh: 200000"
                                >

                                <small class="field-help">
                                    Harga jual per unit komponen.
                                </small>

                            </div>

                        </div>


                        {{-- =========================================
                            HPP + MARGIN
                        ========================================== --}}

                        <div
                            class="additional-component-hpp-margin-wrapper"
                            style="
                                display: none;
                                margin-top: 18px;
                            "
                        >

                            <div class="form-grid">

                                {{-- HPP / Unit --}}
                                <div class="field">

                                    <label
                                        class="field-label"
                                        for="additional_component_hpp_${index}"
                                    >
                                        HPP / Unit
                                    </label>

                                    <input
                                        type="text"
                                        id="additional_component_hpp_${index}"
                                        name="additional_components[${index}][hpp]"
                                        class="input additional-component-hpp"
                                        inputmode="numeric"
                                        placeholder="Contoh: 100000"
                                    >

                                    <small class="field-help">
                                        HPP per unit komponen.
                                    </small>

                                </div>


                                {{-- Margin --}}
                                <div class="field">

                                    <label
                                        class="field-label"
                                        for="additional_component_margin_${index}"
                                    >
                                        Margin
                                    </label>

                                    <div
                                        style="
                                            display: flex;
                                            align-items: center;
                                            gap: 8px;
                                        "
                                    >

                                        <input
                                            type="number"
                                            id="additional_component_margin_${index}"
                                            name="additional_components[${index}][margin]"
                                            class="input additional-component-margin"
                                            min="0"
                                            max="99.99"
                                            step="0.01"
                                            placeholder="Contoh: 50"
                                        >

                                        <span>%</span>

                                    </div>

                                    <small class="field-help">
                                        Margin dihitung berdasarkan harga jual.
                                    </small>

                                </div>

                            </div>


                            {{-- Harga Jual / Unit --}}
                            <div
                                class="field"
                                style="margin-top: 18px;"
                            >

                                <label class="field-label">
                                    Harga Jual / Unit
                                </label>

                                <input
                                    type="text"
                                    class="input additional-component-calculated-price"
                                    value="Rp 0"
                                    readonly
                                >

                                <small class="field-help">
                                    Otomatis dihitung dari HPP dan margin.
                                </small>

                            </div>

                        </div>

                    </div>
                `;


                additionalComponentsContainer.insertAdjacentHTML(
                    'beforeend',
                    componentHtml
                );


                const component =
                    additionalComponentsContainer.lastElementChild;


                // =========================================================
                // INISIALISASI SELECT2
                // =========================================================

                // $(component)
                //     .find('.additional-component-price-type')
                //     .select2({
                //         width: '100%',
                //         placeholder: 'Pilih...'
                //     });

                $(component)
                    .find('.additional-component-price-type')
                    .select2({
                        width: '100%',
                        placeholder: 'Pilih...'
                    });


                $(component)
                    .find('.additional-component-type')
                    .select2({
                        width: '100%',
                        placeholder: 'Pilih...'
                    });

                initializeAdditionalComponent(
                    component
                );


                updateAdditionalComponentNumbers();
            }


            // =========================================================
            // INISIALISASI COMPONENT
            // =========================================================

            function initializeAdditionalComponent(component) {

                const priceTypeSelect =
                    component.querySelector(
                        '.additional-component-price-type'
                    );

                const unitPriceWrapper =
                    component.querySelector(
                        '.additional-component-unit-price-wrapper'
                    );

                const hppMarginWrapper =
                    component.querySelector(
                        '.additional-component-hpp-margin-wrapper'
                    );

                const hppInput =
                    component.querySelector(
                        '.additional-component-hpp'
                    );

                const unitPriceInput =
                    component.querySelector(
                        '.additional-component-unit-price'
                    );

                const marginInput =
                    component.querySelector(
                        '.additional-component-margin'
                    );

                const calculatedPriceInput =
                    component.querySelector(
                        '.additional-component-calculated-price'
                    );

                // =========================================================
                // FORMAT HARGA
                // =========================================================

                function formatAdditionalComponentNumber(value) {

                    const rawValue =
                        String(value || '')
                        .replace(/\D/g, '');

                    if (!rawValue) {
                        return '';
                    }

                    return new Intl.NumberFormat('id-ID', {
                        maximumFractionDigits: 0
                    }).format(Number(rawValue));
                }


                unitPriceInput.addEventListener(
                    'input',
                    function() {

                        this.value =
                            formatAdditionalComponentNumber(
                                this.value
                            );

                    }
                );


                hppInput.addEventListener(
                    'input',
                    function() {

                        this.value =
                            formatAdditionalComponentNumber(
                                this.value
                            );

                        calculateAdditionalComponentPrice(
                            component
                        );

                    }
                );

                // =========================================================
                // CHANGE TIPE HARGA
                // =========================================================

                $(priceTypeSelect).on(
                    'change',
                    function() {

                        if (this.value === 'hpp_margin') {

                            unitPriceWrapper.style.display =
                                'none';

                            hppMarginWrapper.style.display =
                                'block';

                            calculateAdditionalComponentPrice(
                                component
                            );

                        } else {

                            unitPriceWrapper.style.display =
                                'block';

                            hppMarginWrapper.style.display =
                                'none';

                            calculatedPriceInput.value =
                                'Rp 0';
                        }
                    }
                );


                // =========================================================
                // HITUNG HARGA JUAL
                // =========================================================

                hppInput.addEventListener(
                    'input',
                    function() {

                        calculateAdditionalComponentPrice(
                            component
                        );

                    }
                );


                marginInput.addEventListener(
                    'input',
                    function() {

                        calculateAdditionalComponentPrice(
                            component
                        );

                    }
                );


                // =========================================================
                // HAPUS COMPONENT
                // =========================================================

                const removeButton =
                    component.querySelector(
                        '.btn-remove-additional-component'
                    );


                removeButton.addEventListener(
                    'click',
                    function() {

                        const priceTypeSelect =
                            $(component).find(
                                '.additional-component-price-type'
                            );

                        const componentTypeSelect =
                            $(component).find(
                                '.additional-component-type'
                            );


                        // Destroy Select2 sebelum element dihapus
                        if (
                            priceTypeSelect.hasClass(
                                'select2-hidden-accessible'
                            )
                        ) {

                            priceTypeSelect.select2(
                                'destroy'
                            );

                        }

                        if (
                            componentTypeSelect.hasClass(
                                'select2-hidden-accessible'
                            )
                        ) {

                            componentTypeSelect.select2(
                                'destroy'
                            );

                        }


                        component.remove();

                        updateAdditionalComponentNumbers();

                    }
                );
            }


            // =========================================================
            // HITUNG HARGA JUAL COMPONENT
            // =========================================================

            function calculateAdditionalComponentPrice(
                component
            ) {

                const priceType =
                    component.querySelector(
                        '.additional-component-price-type'
                    ).value;


                const calculatedPriceInput =
                    component.querySelector(
                        '.additional-component-calculated-price'
                    );


                if (priceType !== 'hpp_margin') {

                    calculatedPriceInput.value =
                        'Rp 0';

                    return;
                }


                // const hpp =
                //     Number(
                //         component.querySelector(
                //             '.additional-component-hpp'
                //         ).value
                //     ) || 0;

                const hpp =
                    Number(
                        String(
                            component.querySelector(
                                '.additional-component-hpp'
                            ).value || ''
                        ).replace(/\./g, '')
                    ) || 0;


                const margin =
                    Number(
                        component.querySelector(
                            '.additional-component-margin'
                        ).value
                    ) || 0;


                // Belum ada input lengkap
                if (hpp <= 0 || margin <= 0) {

                    calculatedPriceInput.value =
                        'Rp 0';

                    return;
                }


                // =========================================================
                // HARGA JUAL
                //
                // Harga Jual =
                // HPP / (1 - Margin)
                // =========================================================

                const marginDecimal =
                    margin / 100;


                const divisor =
                    1 - marginDecimal;


                if (divisor <= 0) {

                    calculatedPriceInput.value =
                        'Rp 0';

                    return;
                }


                const sellingPrice =
                    hpp / divisor;


                calculatedPriceInput.value =
                    formatRupiahResult(
                        sellingPrice
                    );
            }


            // =========================================================
            // UPDATE NOMOR COMPONENT
            // =========================================================

            function updateAdditionalComponentNumbers() {

                const components =
                    additionalComponentsContainer.querySelectorAll(
                        '.additional-component-item'
                    );


                components.forEach(
                    function(component, index) {

                        const numberElement =
                            component.querySelector(
                                '.additional-component-number'
                            );


                        if (numberElement) {

                            numberElement.textContent =
                                index + 1;

                        }

                    }
                );
            }


            // =========================================================
            // BUTTON TAMBAH COMPONENT
            // =========================================================

            addAdditionalComponentButton.addEventListener(
                'click',
                function() {

                    addAdditionalComponent();

                }
            );


            // =========================================================
            // COMPONENT PERTAMA
            // =========================================================

            addAdditionalComponent();

            /* =====================================================
               DISCOUNT
            ===================================================== */

            const discountTypeInputs =
                document.querySelectorAll(
                    'input[name="discount_type"]'
                );

            const discountValueField =
                document.getElementById(
                    'discountValueField'
                );

            const discountValueInput =
                document.getElementById(
                    'discount_value'
                );

            const discountValueLabel =
                document.getElementById(
                    'discountValueLabel'
                );

            const discountSuffix =
                document.getElementById(
                    'discountSuffix'
                );


            /* =====================================================
               DISCOUNT EVENT
            ===================================================== */

            discountTypeInputs.forEach(
                function(input) {

                    input.addEventListener(
                        'change',
                        function() {

                            const type =
                                this.value;


                            /* =====================================
                               TANPA DISKON
                            ===================================== */

                            if (type === 'none') {

                                discountValueField.hidden =
                                    true;

                                discountValueInput.disabled =
                                    true;

                                discountValueInput.value =
                                    '';

                                return;
                            }


                            /* =====================================
                               DENGAN DISKON
                            ===================================== */

                            discountValueField.hidden =
                                false;

                            discountValueInput.disabled =
                                false;


                            /* =====================================
                               DISKON PERSENTASE
                            ===================================== */

                            if (
                                type === 'percentage'
                            ) {

                                discountValueLabel.textContent =
                                    'Persentase Diskon';

                                discountValueInput.placeholder =
                                    'Contoh: 10';

                                discountSuffix.textContent =
                                    '%';
                            }


                            /* =====================================
                               DISKON NOMINAL
                            ===================================== */

                            if (
                                type === 'nominal'
                            ) {

                                discountValueLabel.textContent =
                                    'Nominal Diskon';

                                discountValueInput.placeholder =
                                    'Contoh: 25.000';

                                discountSuffix.textContent =
                                    'Rp';
                            }

                        }
                    );

                }
            );


            /* =====================================================
               ESTIMATION FORM
            ===================================================== */

            const estimationForm =
                document.getElementById(
                    'largeFormatEstimationForm'
                );

            const calculateButton =
                document.getElementById(
                    'calculateEstimationButton'
                );


            /* =====================================================
               ESTIMATION PREVIEW MODAL
            ===================================================== */

            const modalEstimationPreview =
                document.getElementById(
                    'modalEstimationPreview'
                );

            const btnCloseEstimationPreview =
                document.getElementById(
                    'btnCloseEstimationPreview'
                );

            const btnCancelEstimationPreview =
                document.getElementById(
                    'btnCancelEstimationPreview'
                );

            const btnSummaryEstimation =
                document.getElementById('btnSummaryEstimation');

            const btnDetailsEstimation =
                document.getElementById('btnDetailsEstimation');

            const estimationSummary =
                document.getElementById('estimationSummary');

            const estimationDetails =
                document.getElementById('estimationDetails');

            /* =====================================================
               OPEN PREVIEW MODAL
            ===================================================== */

            function openEstimationPreview() {

                if (!modalEstimationPreview) {
                    return;
                }

                modalEstimationPreview.classList.add(
                    'is-open'
                );
            }

            function showSummaryEstimation() {

                if (!estimationSummary || !estimationDetails) {
                    return;
                }

                estimationSummary.style.display = 'block';
                estimationDetails.style.display = 'none';

                if (btnSummaryEstimation) {
                    btnSummaryEstimation.classList.add('is-active');
                }

                if (btnDetailsEstimation) {
                    btnDetailsEstimation.classList.remove('is-active');
                }
            }

            function showDetailsEstimation() {

                if (!estimationSummary || !estimationDetails) {
                    return;
                }

                estimationSummary.style.display = 'none';
                estimationDetails.style.display = 'block';

                if (btnSummaryEstimation) {
                    btnSummaryEstimation.classList.remove('is-active');
                }

                if (btnDetailsEstimation) {
                    btnDetailsEstimation.classList.add('is-active');
                }
            }

            if (btnSummaryEstimation) {
                btnSummaryEstimation.addEventListener(
                    'click',
                    showSummaryEstimation
                );
            }

            if (btnDetailsEstimation) {
                btnDetailsEstimation.addEventListener(
                    'click',
                    showDetailsEstimation
                );
            }

            /* =====================================================
               CLOSE PREVIEW MODAL
            ===================================================== */

            function closeEstimationPreview() {

                if (!modalEstimationPreview) {
                    return;
                }

                modalEstimationPreview.classList.remove(
                    'is-open'
                );
            }


            /* =====================================================
               CLOSE BUTTON
            ===================================================== */

            if (btnCloseEstimationPreview) {

                btnCloseEstimationPreview.addEventListener(
                    'click',
                    closeEstimationPreview
                );

            }


            /* =====================================================
               CANCEL BUTTON
            ===================================================== */

            if (btnCancelEstimationPreview) {

                btnCancelEstimationPreview.addEventListener(
                    'click',
                    closeEstimationPreview
                );

            }


            /* =====================================================
               CLICK OVERLAY
            ===================================================== */

            if (modalEstimationPreview) {

                modalEstimationPreview.addEventListener(
                    'click',
                    function(event) {

                        if (
                            event.target ===
                            modalEstimationPreview
                        ) {

                            closeEstimationPreview();

                        }

                    }
                );

            }


            /* =====================================================
               ESCAPE KEY
            ===================================================== */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape'
                    ) {

                        closeEstimationPreview();

                    }

                }
            );

            discountValueInput.addEventListener(
                'input',
                function() {

                    const selectedType =
                        document.querySelector(
                            'input[name="discount_type"]:checked'
                        )?.value;

                    if (selectedType === 'percentage') {

                        // Hanya izinkan angka dan satu titik desimal
                        let value =
                            this.value.replace(/[^0-9.]/g, '');

                        const parts = value.split('.');

                        if (parts.length > 2) {
                            value =
                                parts[0] +
                                '.' +
                                parts.slice(1).join('');
                        }

                        this.value = value;
                    }

                    if (selectedType === 'nominal') {

                        // Hanya izinkan angka
                        const rawValue =
                            this.value.replace(/\D/g, '');

                        // Format ribuan menggunakan titik
                        this.value = rawValue ?
                            new Intl.NumberFormat('id-ID', {
                                maximumFractionDigits: 0
                            }).format(Number(rawValue)) :
                            '';
                    }
                }
            );

            /* =====================================================
               SUBMIT ESTIMATION
            ===================================================== */

            estimationForm.addEventListener(
                'submit',
                async function(event) {

                    event.preventDefault();


                    /* =============================================
                       DISABLE BUTTON
                    ============================================= */

                    calculateButton.disabled =
                        true;

                    calculateButton.textContent =
                        'Menghitung...';


                    try {

                        /* =========================================
                           FORM DATA
                        ========================================= */

                        const formData =
                            new FormData(
                                estimationForm
                            );

                        /* =========================================
                        FILTER KOMPONEN TAMBAHAN KOSONG
                        ========================================= */

                        const additionalComponents =
                            additionalComponentsContainer.querySelectorAll(
                                '.additional-component-item'
                            );

                        additionalComponents.forEach(
                            function(component) {

                                const typeInput =
                                    component.querySelector(
                                        '.additional-component-type'
                                    );

                                const nameInput =
                                    component.querySelector(
                                        '.additional-component-name'
                                    );

                                const qtyInput =
                                    component.querySelector(
                                        '.additional-component-qty'
                                    );

                                const priceTypeInput =
                                    component.querySelector(
                                        '.additional-component-price-type'
                                    );

                                const unitPriceInput =
                                    component.querySelector(
                                        '.additional-component-unit-price'
                                    );

                                const hppInput =
                                    component.querySelector(
                                        '.additional-component-hpp'
                                    );

                                const marginInput =
                                    component.querySelector(
                                        '.additional-component-margin'
                                    );


                                const type =
                                    String(
                                        typeInput?.value || ''
                                    ).trim();

                                const name =
                                    String(
                                        nameInput?.value || ''
                                    ).trim();

                                const qty =
                                    String(
                                        qtyInput?.value || ''
                                    ).trim();

                                const priceType =
                                    String(
                                        priceTypeInput?.value || ''
                                    ).trim();

                                const unitPrice =
                                    String(
                                        unitPriceInput?.value || ''
                                    ).trim();

                                const hpp =
                                    String(
                                        hppInput?.value || ''
                                    ).trim();

                                const margin =
                                    String(
                                        marginInput?.value || ''
                                    ).trim();


                                /*
                                |-------------------------------------------------------------
                                | KOMPONEN BENAR-BENAR BELUM DIISI
                                |
                                | Nilai default:
                                | type       = kosong
                                | name       = kosong
                                | qty        = 1
                                | price_type = unit_price
                                | harga      = kosong
                                |-------------------------------------------------------------
                                */

                                const isEmpty = !type &&
                                    !name &&
                                    qty === '1' &&
                                    priceType === 'unit_price' &&
                                    !unitPrice &&
                                    !hpp &&
                                    !margin;


                                if (isEmpty) {

                                    const fields =
                                        component.querySelectorAll(
                                            '[name^="additional_components["]'
                                        );

                                    fields.forEach(
                                        function(field) {

                                            formData.delete(
                                                field.name
                                            );

                                        }
                                    );
                                }
                            }
                        );

                        const discountType =
                            formData.get('discount_type');

                        if (discountType === 'nominal') {
                            const discountValue =
                                String(
                                    formData.get('discount_value') || ''
                                ).replace(/\./g, '');

                            formData.set(
                                'discount_value',
                                discountValue
                            );
                        }

                        const discountValue =
                            formData.get('discount_value');

                        // console.log('Discount Type:', discountType);
                        // console.log('Discount Value:', discountValue);

                        const additionalComponentInputs =
                            additionalComponentsContainer.querySelectorAll(
                                '.additional-component-unit-price, .additional-component-hpp'
                            );

                        additionalComponentInputs.forEach(
                            function(input) {

                                const fieldName =
                                    input.name;

                                const rawValue =
                                    String(
                                        input.value || ''
                                    ).replace(/\./g, '');

                                formData.set(
                                    fieldName,
                                    rawValue
                                );

                            }
                        );

                        const laminationId =
                            formData.get('lamination_id');

                        if (laminationId === 'none') {
                            formData.set('lamination_id', '');
                            formData.set('lamination_size_id', '');
                        }


                        /* =========================================
                           REQUEST
                        ========================================= */

                        const response =
                            await fetch(
                                estimationForm.action, {

                                    method: 'POST',

                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },

                                    body: formData

                                }
                            );


                        /* =========================================
                           RESPONSE JSON
                        ========================================= */

                        const result =
                            await response.json();


                        /* =========================================
                           CHECK RESPONSE
                        ========================================= */

                        if (!response.ok) {

                            throw new Error(
                                result.message ||
                                'Gagal menghitung estimasi.'
                            );

                        }


                        /* =========================================
                           CHECK SUCCESS
                        ========================================= */

                        if (!result.success) {

                            throw new Error(
                                result.message ||
                                'Gagal menghitung estimasi.'
                            );

                        }


                        /* =========================================
                           SHOW PREVIEW
                        ========================================= */

                        showEstimationPreview(
                            result.data
                        );

                    } catch (error) {

                        console.error(
                            'Estimation Error:',
                            error
                        );

                        showToast(
                            'error',
                            error.message ||
                            'Gagal menghitung estimasi.'
                        );

                    } finally {

                        calculateButton.disabled =
                            false;

                        calculateButton.textContent =
                            'Hitung Estimasi';
                    }


                }
            );


            /* =====================================================
               FORMAT RUPIAH
            ===================================================== */

            function formatRupiahResult(value) {

                return new Intl.NumberFormat(
                    'id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }
                ).format(value);

            }


            /* =====================================================
               FORMAT NUMBER
               Tanpa angka 0 di belakang koma
            ===================================================== */

            function formatNumberResult(value) {

                return new Intl.NumberFormat(
                    'id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 4
                    }
                ).format(value);

            }

            /* =====================================================
               FORMAT METER
               Dari cm → meter
               Maksimal 2 angka di belakang koma
            ===================================================== */

            function formatMeterResult(value) {

                const meter =
                    Number(value) / 100;

                return new Intl.NumberFormat(
                    'id-ID', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    }
                ).format(meter);
            }


            /* =====================================================
               FORMAT PERCENTAGE
               Selalu 2 angka di belakang koma
            ===================================================== */

            function formatPercentageResult(value) {

                return new Intl.NumberFormat(
                    'id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }
                ).format(value) + '%';
            }

            function renderLaminationPreview(lamination) {

                const container =
                    document.getElementById(
                        'previewLaminationContainer'
                    );

                const hppContainer =
                    document.getElementById(
                        'previewLaminationHppContainer'
                    );

                if (!container) {
                    return;
                }

                container.innerHTML = '';

                if (hppContainer) {
                    hppContainer.style.display = 'none';
                }

                // Jika tidak ada laminasi, jangan tampilkan section apa pun
                if (!lamination) {
                    return;
                }

                const section =
                    document.createElement('div');

                section.className =
                    'preview-component';

                section.innerHTML = `
                <div class="preview-component-header">
                    <strong>
                        LAMINASI
                    </strong>
                </div>

                <div class="row g-3">

                    <div class="col-md-4">
                        <small class="text-muted">
                            Nama Laminasi
                        </small>
                        <div>
                            ${lamination.name || '-'}
                        </div>
                    </div>

                    <div class="col-md-4">
                        <small class="text-muted">
                            Ukuran Laminasi
                        </small>
                    <div>
                    ${
                        formatNumberResult(
                            lamination.width
                        )
                    } cm

                    ${
                        lamination.length !== null &&
                        lamination.length !== undefined
                            ? ' × ' +
                              formatNumberResult(
                                  lamination.length
                              ) +
                              ' cm'
                            : ''
                    }
                    </div>
                        </div>

                        <div class="col-md-4">
                            <small class="text-muted">
                                Harga Satuan
                            </small>
                            <div>
                                ${
                                    formatRupiahResult(
                                        lamination.price
                                    )
                                } /${lamination.unit || 'm'}
                            </div>
                        </div>

                        <div class="col-md-4">
                            <small class="text-muted">
                                Luas Laminasi
                            </small>
                            <div>
                                ${
                                    formatNumberResult(
                                        lamination.area
                                    )
                                } m²
                            </div>
                        </div>

                        <div class="col-md-4">
                            <small class="text-muted">
                                Panjang Produksi
                            </small>
                            <div>
                                ${
                                    formatNumberResult(
                                        lamination.billing_length
                                    )
                                } m
                            </div>
                        </div>

                        <div class="col-md-4">
                            <small class="text-muted">
                                Subtotal Laminasi
                            </small>
                            <div>
                                ${
                                    formatRupiahResult(
                                        lamination.subtotal
                                    )
                                }
                            </div>
                        </div>

                    </div>
                `;

                container.appendChild(section);


                // =========================================================
                // HPP LAMINASI
                // =========================================================

                const hppPerMeter =
                    document.getElementById(
                        'previewLaminationHppPerMeter'
                    );

                const totalHpp =
                    document.getElementById(
                        'previewLaminationTotalHpp'
                    );

                if (hppPerMeter) {
                    hppPerMeter.textContent =
                        formatRupiahResult(
                            lamination.hpp_per_meter
                        ) + ' /m';
                }

                if (totalHpp) {
                    totalHpp.textContent =
                        formatRupiahResult(
                            lamination.total_hpp
                        );
                }

                if (hppContainer) {
                    hppContainer.style.display = '';
                }
            }

            function renderAdditionalComponentsPreview(components) {
                const container = document.getElementById(
                    'previewAdditionalComponentsContainer'
                );

                if (!container) {
                    return;
                }

                container.innerHTML = '';

                if (!Array.isArray(components) || components.length === 0) {
                    return;
                }

                components.forEach(function(component) {
                    const componentElement =
                        document.createElement('div');

                    componentElement.className = 'preview-component';

                    const priceTypeLabel =
                        component.price_type === 'hpp_margin' ?
                        'HPP + Margin' :
                        'Harga Satuan';

                    const salePricePerUnit =
                        Number(component.sale_price_per_unit) || 0;

                    const subtotal =
                        Number(component.subtotal) || 0;

                    const qty =
                        Number(component.qty) || 0;

                    let additionalInfo = '';

                    if (component.price_type === 'hpp_margin') {
                        const hppPerUnit =
                            Number(component.hpp_per_unit) || 0;

                        const margin =
                            Number(component.margin) || 0;

                        const totalHpp =
                            Number(component.total_hpp) || 0;

                        const profit =
                            Number(component.profit) || 0;

                        additionalInfo = `
                            <div class="col-md-6">
                                <div class="preview-label">
                                    HPP / Unit
                                </div>
                                <div class="preview-value">
                                    ${formatRupiahResult(hppPerUnit)}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="preview-label">
                                    Margin
                                </div>
                                <div class="preview-value">
                                    ${formatPercentageResult(margin)}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="preview-label">
                                    Total HPP
                                </div>
                                <div class="preview-value">
                                    ${formatRupiahResult(totalHpp)}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="preview-label">
                                    Profit
                                </div>
                                <div class="preview-value">
                                    ${formatRupiahResult(profit)}
                                </div>
                            </div>
                        `;
                    }

                    componentElement.innerHTML = `
                        <div class="preview-component-header">
                            <strong>
                                ${component.type || 'KOMPONEN TAMBAHAN'}
                            </strong>
                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <div class="preview-label">
                                    Nama Komponen
                                </div>
                                <div class="preview-value">
                                    ${component.name || '-'}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="preview-label">
                                    Qty
                                </div>
                                <div class="preview-value">
                                    ${qty}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="preview-label">
                                    Jenis Harga
                                </div>
                                <div class="preview-value">
                                    ${priceTypeLabel}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="preview-label">
                                    Harga Jual / Unit
                                </div>
                                <div class="preview-value">
                                    ${formatRupiahResult(salePricePerUnit)}
                                </div>
                            </div>

                            ${additionalInfo}

                            <div class="col-md-6">
                                <div class="preview-label">
                                    Subtotal
                                </div>
                                <div class="preview-value">
                                    ${formatRupiahResult(subtotal)}
                                </div>
                            </div>

                        </div>
                    `;

                    container.appendChild(componentElement);
                });
            }

            function renderAdditionalComponentsHppPreview(components) {
                const container =
                    document.getElementById(
                        'previewAdditionalComponentsHppContainer'
                    );

                if (!container) {
                    return;
                }

                container.innerHTML = '';

                if (
                    !Array.isArray(components) ||
                    components.length === 0
                ) {
                    return;
                }

                components.forEach(function(component) {

                    // Hanya komponen HPP + Margin
                    if (component.price_type !== 'hpp_margin') {
                        return;
                    }

                    const hppPerUnit =
                        Number(component.hpp_per_unit) || 0;

                    const totalHpp =
                        Number(component.total_hpp) || 0;

                    const profit =
                        Number(component.profit) || 0;

                    const margin =
                        Number(component.margin_percentage) || 0;

                    const componentElement =
                        document.createElement('div');

                    componentElement.className =
                        'preview-component';

                    componentElement.innerHTML = `
                        <div class="preview-component-header">
                            <strong>
                                ${component.type || 'KOMPONEN TAMBAHAN'}
                                : ${component.name || '-'}
                            </strong>
                        </div>

                        <div class="row g-3">

                            <div class="col-md-6">
                                <div class="preview-label">
                                    HPP / Unit
                                </div>
                                <div class="preview-value">
                                    ${formatRupiahResult(hppPerUnit)}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="preview-label">
                                    Total HPP
                                </div>
                                <div class="preview-value">
                                    ${formatRupiahResult(totalHpp)}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="preview-label">
                                    Profit
                                </div>
                                <div class="preview-value">
                                    ${formatRupiahResult(profit)}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="preview-label">
                                    Margin
                                </div>
                                <div class="preview-value">
                                    ${formatPercentageResult(margin)}
                                </div>
                            </div>

                        </div>
                    `;

                    container.appendChild(componentElement);
                });
            }

            function renderEstimationSummary(data) {

                // INFORMASI UTAMA
                document.getElementById('summaryEngineName').textContent =
                    data.engine_name || '-';

                document.getElementById('summaryLocationName').textContent =
                    data.location_name || '-';

                document.getElementById('summaryVendorName').textContent =
                    data.vendor_name || '-';

                document.getElementById('summaryCategoryName').textContent =
                    data.category_name || '-';

                document.getElementById('summaryMaterialName').textContent =
                    data.material_name || '-';

                document.getElementById('summaryPriceType').textContent =
                    data.price_type ?
                    data.price_type.charAt(0).toUpperCase() +
                    data.price_type.slice(1) :
                    '-';

                document.getElementById('summarySize').textContent =
                    formatNumberResult(data.length) +
                    ' × ' +
                    formatNumberResult(data.width) +
                    ' cm';

                document.getElementById('summaryQty').textContent =
                    (data.qty ?? 0) + ' pcs';

                document.getElementById('summaryBillingArea').textContent =
                    formatNumberResult(data.billing_area) + ' m²';


                // HARGA
                document.getElementById('summaryMaterialPrice').textContent =
                    formatRupiahResult(data.material_price) + ' /m²';

                document.getElementById('summarySubtotal').textContent =
                    formatRupiahResult(data.subtotal);

                document.getElementById('summaryDiscount').textContent =
                    formatRupiahResult(data.discount_amount);

                const pricePerPcs =
                    Number(data.qty) > 0 ?
                    Number(data.grand_total) / Number(data.qty) :
                    0;

                document.getElementById('summaryPricePerPcs').textContent =
                    formatRupiahResult(pricePerPcs);

                document.getElementById('summaryGrandTotal').textContent =
                    formatRupiahResult(data.grand_total);


                // PROFITABILITAS
                document.getElementById('summaryTotalHpp').textContent =
                    formatRupiahResult(data.total_hpp);

                document.getElementById('summaryProfit').textContent =
                    formatRupiahResult(data.profit);

                document.getElementById('summaryMargin').textContent =
                    formatPercentageResult(data.margin);

                document.getElementById('summaryMarkup').textContent =
                    formatPercentageResult(data.markup);
            }

            /* =====================================================
               SHOW ESTIMATION PREVIEW
            ===================================================== */

            function showEstimationPreview(data) {

                // =========================================================
                // INFORMASI PRODUKSI
                // =========================================================

                document.getElementById(
                        'previewEngineName'
                    ).textContent =
                    data.engine_name || '-';

                document.getElementById(
                        'previewLocationName'
                    ).textContent =
                    data.location_name || '-';

                document.getElementById('previewVendorName').textContent =
                    data.vendor_name || '-';

                document.getElementById(
                        'previewCategoryName'
                    ).textContent =
                    data.category_name || '-';

                document.getElementById(
                        'previewMaterialName'
                    ).textContent =
                    data.material_name || '-';

                document.getElementById(
                        'previewPriceType'
                    ).textContent =
                    data.price_type ?
                    data.price_type.charAt(0).toUpperCase() +
                    data.price_type.slice(1) :
                    '-';

                document.getElementById(
                        'previewEstimationTime'
                    ).textContent =
                    data.estimation_time || '-';


                // =========================================================
                // SPESIFIKASI PRODUK
                // =========================================================

                document.getElementById(
                        'previewQty'
                    ).textContent =
                    data.qty + ' pcs';

                document.getElementById(
                        'previewSize'
                    ).textContent =
                    formatNumberResult(data.length) +
                    ' × ' +
                    formatNumberResult(data.width) +
                    ' cm';

                document.getElementById(
                        'previewMaterialWidth'
                    ).textContent =
                    formatNumberResult(
                        data.material_width
                    ) + ' cm';

                document.getElementById(
                        'previewObjectsPerRow'
                    ).textContent =
                    data.objects_per_row;

                document.getElementById(
                        'previewTotalRows'
                    ).textContent =
                    data.total_rows;

                document.getElementById(
                        'previewProductionLength'
                    ).textContent =
                    formatMeterResult(
                        data.production_length
                    ) + ' m';

                document.getElementById(
                        'previewProductionWidth'
                    ).textContent =
                    formatNumberResult(
                        data.production_width
                    ) + ' m';

                document.getElementById(
                        'previewProductionArea'
                    ).textContent =
                    formatNumberResult(
                        data.production_area
                    ) + ' m²';

                document.getElementById(
                        'previewMinimumCharge'
                    ).textContent =
                    formatNumberResult(
                        data.minimum_charge
                    ) + ' m²';

                document.getElementById(
                        'previewBillingArea'
                    ).textContent =
                    formatNumberResult(
                        data.billing_area
                    ) + ' m²';


                // =========================================================
                // KOMPONEN MATERIAL
                // =========================================================

                document.getElementById(
                        'previewComponentMaterialName'
                    ).textContent =
                    data.material_name || '-';

                document.getElementById(
                        'previewComponentMaterialArea'
                    ).textContent =
                    formatNumberResult(
                        data.billing_area
                    ) + ' m²';

                document.getElementById(
                        'previewMaterialPrice'
                    ).textContent =
                    formatRupiahResult(
                        data.material_price
                    ) + ' /m²';

                document.getElementById(
                        'previewMaterialSubtotal'
                    ).textContent =
                    formatRupiahResult(
                        data.material_subtotal
                    );


                // =========================================================
                // RINGKASAN KEUANGAN
                // =========================================================

                document.getElementById(
                        'previewSubtotal'
                    ).textContent =
                    formatRupiahResult(
                        data.subtotal
                    );

                document.getElementById(
                        'previewDiscount'
                    ).textContent =
                    formatRupiahResult(
                        data.discount_amount
                    );

                document.getElementById(
                        'previewGrandTotal'
                    ).textContent =
                    formatRupiahResult(
                        data.grand_total
                    );


                // =========================================================
                // HARGA PER PCS
                // =========================================================

                const pricePerPcs =
                    Number(data.qty) > 0 ?
                    Number(data.grand_total) /
                    Number(data.qty) :
                    0;

                document.getElementById(
                        'previewPricePerPcs'
                    ).textContent =
                    formatRupiahResult(
                        pricePerPcs
                    );


                // =========================================================
                // HPP MATERIAL
                // =========================================================

                document.getElementById(
                        'previewMaterialHppPerM2'
                    ).textContent =
                    formatRupiahResult(
                        data.material_hpp_per_m2
                    ) + ' /m²';

                document.getElementById(
                        'previewMaterialTotalHpp'
                    ).textContent =
                    formatRupiahResult(
                        data.material_total_hpp
                    );


                // =========================================================
                // HPP TOTAL
                // =========================================================

                document.getElementById(
                        'previewTotalHpp'
                    ).textContent =
                    formatRupiahResult(
                        data.total_hpp
                    );


                // =========================================================
                // PROFITABILITY
                // =========================================================

                document.getElementById(
                        'previewProfit'
                    ).textContent =
                    formatRupiahResult(
                        data.profit
                    );

                document.getElementById(
                        'previewMargin'
                    ).textContent =
                    formatPercentageResult(
                        data.margin
                    );

                document.getElementById(
                        'previewMarkup'
                    ).textContent =
                    formatPercentageResult(
                        data.markup
                    );


                // =========================================================
                // LAMINASI
                // =========================================================

                renderLaminationPreview(
                    data.lamination
                );

                renderAdditionalComponentsPreview(
                    data.additional_components
                );

                renderAdditionalComponentsHppPreview(
                    data.additional_components
                );

                // SUMMARY
                renderEstimationSummary(data);

                // DEFAULT TAMPILAN: SUMMARY
                showSummaryEstimation();

                // =========================================================
                // OPEN MODAL
                // =========================================================

                openEstimationPreview();
            }

        });
    </script>
@endsection

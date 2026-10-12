@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">HARGA MARKUP · LAMINASI</span>
                        {{-- <h1 class="hero-title">Daftar Material</h1> --}}
                        <p class="hero-sub">Kelola dan pantau seluruh data laminasi yang digunakan dalam proses produksi dan
                            perhitungan estimasi harga.</p>
                    </div>
                    <div class="hero-actions material-hero-actions"><a id="btnExportLaminationPricingRules"
                            href="{{ route('admin_lamination_export_pricing_rules') }}"
                            class="btn btn--ghost"><svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg> Export</a>
                        <div class="material-category-filter">
                            <select id="laminationPricingRuleEngineFilter" class="select select2"
                                aria-label="Filter mesin pricing rule laminasi">
                                <option value="large_format" selected>Large Format</option>
                                <option value="digital_print_a3">Digital Print A3+</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn--ghost" id="btnDownloadTemplateLaminationPricingRules">

                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Download Template

                        </button>
                        <button type="button" class="btn btn--ghost" id="btnImportLaminationPricingRules">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg>
                            Import
                        </button>



                        <button type="button" class="btn btn--primary" id="btnTambahMarkupLaminasi">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Markup
                        </button>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="laminationPricingRuleTable" class="data-table">

                            <thead>

                                <tr>

                                    <th></th>

                                    <th>No</th>

                                    <th>Mesin</th>

                                    <th>Lokasi</th>

                                    <th>Vendor</th>

                                    <th>Kategori</th>

                                    <th style="text-align: center;">Jumlah Markup</th>

                                    <th>Aksi</th>

                                </tr>

                            </thead>

                            <tbody></tbody>

                        </table>
                    </section>
                </div>
            </main>
            <div data-shell-footer></div>
        </div>
    </div>

    <!-- Modal Download Template Markup Laminasi -->
    <div class="modal-overlay" id="modalDownloadTemplateLaminationPricingRules">

        <div class="modal modal-import">

            <div class="modal-header">

                <div>
                    <span class="eyebrow">HARGA MARKUP · LAMINASI</span>
                    <br>
                    <span class="eyebrow">Download Template Excel</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseDownloadTemplateLaminationPricingRules"
                    aria-label="Tutup">
                    <svg viewBox="0 0 24 24">
                        <path d="M18 6 6 18" />
                        <path d="M6 6 18 18" />
                    </svg>
                </button>

            </div>

            <div class="modal-body">

                <div class="import-description">
                    <p>
                        Pilih template Excel sesuai dengan jenis mesin markup
                        laminasi yang ingin diinput.
                    </p>
                </div>

                <div class="template-download-options">

                    <a href="{{ asset('templates/template_harga_markup_laminasi.xlsx') }}"
                        class="template-download-card file-upload-box">

                        <div class="template-download-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 3v12" />
                                <path d="m7 10 5 5 5-5" />
                                <path d="M5 21h14" />
                                <path d="M5 17v4" />
                                <path d="M19 17v4" />
                            </svg>
                        </div>

                        <div class="template-download-content">
                            <strong>Template Large Format</strong>
                            <span>
                                Template markup laminasi untuk mesin Large
                                Format.
                            </span>
                        </div>

                        <div class="template-download-arrow">
                            <svg viewBox="0 0 24 24">
                                <path d="M5 12h14" />
                                <path d="m13 6 6 6-6 6" />
                            </svg>
                        </div>

                    </a>

                    <a href="{{ asset('templates/template_harga_markup_laminasi_digital_print_a3.xlsx') }}"
                        class="template-download-card file-upload-box">

                        <div class="template-download-icon">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 3v12" />
                                <path d="m7 10 5 5 5-5" />
                                <path d="M5 21h14" />
                                <path d="M5 17v4" />
                                <path d="M19 17v4" />
                            </svg>
                        </div>

                        <div class="template-download-content">
                            <strong>Template Digital Print A3+</strong>
                            <span>
                                Template markup laminasi untuk mesin Digital
                                Print A3+.
                            </span>
                        </div>

                        <div class="template-download-arrow">
                            <svg viewBox="0 0 24 24">
                                <path d="M5 12h14" />
                                <path d="m13 6 6 6-6 6" />
                            </svg>
                        </div>

                    </a>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCancelDownloadTemplateLaminationPricingRules">
                    Batal
                </button>

            </div>

        </div>

    </div>

    <!-- Modal Import Markup Laminasi -->
    <div class="modal-overlay" id="modalImportLaminationPricingRules">

        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">

                <div>
                    <span class="eyebrow">
                        HARGA MARKUP · LAMINASI
                    </span>
                    <br>
                    <span class="eyebrow">
                        Import Markup Harga
                    </span>
                </div>

                <button type="button" class="modal-close" id="btnCloseImportLaminationPricingRules" aria-label="Tutup">
                    <svg viewBox="0 0 24 24">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>

            </div>


            <!-- Body -->
            <div class="modal-body">

                <div class="import-description">
                    <p>
                        Pilih file Excel yang berisi data
                        markup harga laminasi untuk diimport
                        ke dalam sistem.
                    </p>
                </div>


                <!-- Custom File Input -->
                <label for="fileImportLaminationPricingRules" class="file-upload-box"
                    id="fileUploadBoxLaminationPricingRules">

                    <input type="file" id="fileImportLaminationPricingRules" name="file" accept=".xlsx,.xls" hidden>


                    <div class="file-upload-content">

                        <div class="file-upload-icon">

                            <svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg>

                        </div>


                        <div class="file-upload-text">

                            <strong>Pilih file Excel</strong>

                            <span>
                                Klik untuk memilih file dari komputer
                            </span>

                        </div>

                    </div>


                    <div class="file-selected-name" id="fileSelectedNameLaminationPricingRules">
                        Belum ada file dipilih
                    </div>

                </label>

            </div>


            <!-- Footer -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCancelImportLaminationPricingRules">
                    Batal
                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportLaminationPricingRules">

                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8 5-5 5 5" />
                        <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                    </svg>

                    Import Data

                </button>

            </div>

        </div>

    </div>

    <!-- Modal Tambah Markup Laminasi -->
    <div class="modal-overlay" id="modalTambahMarkupLaminasi">

        <div class="modal-dialog modal-dialog--wide">

            <form method="POST" action="{{ route('admin_store_lamination_pricing_rule') }}" id="formTambahMarkupLaminasi"
                novalidate>

                @csrf

                <!-- HEADER MODAL -->
                <div class="modal-header">

                    <div>

                        <span class="eyebrow">
                            HARGA MARKUP · LAMINASI
                        </span>

                        <br>

                        <span class="eyebrow">
                            Tambah Markup Laminasi
                        </span>

                    </div>

                    <button type="button" class="modal-close" id="btnTutupModalTambahMarkupLaminasi" aria-label="Tutup">

                        <svg viewBox="0 0 24 24">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>

                    </button>

                </div>


                <!-- BODY MODAL -->
                <div class="modal-body">

                    <div class="category-form-header">

                        <div>

                            <label class="category-label">
                                Konfigurasi Markup Laminasi
                            </label>

                            <p class="category-description">
                                Tambahkan satu atau beberapa konfigurasi
                                mesin, lokasi dan kategori. Setiap konfigurasi
                                dapat memiliki beberapa tipe harga.
                            </p>

                        </div>

                        <button type="button" class="btn btn--ghost" id="btnTambahHeaderMarkupLaminasi">

                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14" />
                                <path d="M5 12h14" />
                            </svg>

                            Tambah Header

                        </button>

                    </div>


                    <!-- CONTAINER HEADER MARKUP -->
                    <div id="markupLaminasiHeaderContainer">

                        <!-- HEADER MARKUP #0 -->
                        <div class="markup-header-group" data-header-index="0">

                            <!-- HEADER MESIN + LOKASI + VENDOR + KATEGORI -->
                            <div class="markup-row markup-header-row">

                                <div
                                    style="
                                display: flex;
                                justify-content: right;
                                align-items: center;
                                gap: 10px;
                            ">

                                    <!-- Hapus Header -->
                                    <div class="markup-header-action">

                                        <i class="bi bi-trash3-fill btn-remove-header-laminasi"
                                            style="
                                        cursor: pointer;
                                        border: none !important;
                                    "></i>

                                    </div>

                                </div>


                                <div class="markup-row-top">

                                    <!-- Mesin -->
                                    <div class="markup-field">

                                        <label class="category-label">
                                            Mesin
                                        </label>

                                        <select class="select markup-laminasi-engine" required>

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


                                    <!-- Lokasi -->
                                    <div class="markup-field">

                                        <label class="category-label">
                                            Lokasi
                                        </label>

                                        <select class="select markup-laminasi-location" required>

                                            <option value="">
                                                Pilih Lokasi
                                            </option>

                                            @foreach ($locations as $location)
                                                <option value="{{ $location->id }}">
                                                    {{ $location->name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>


                                    <!-- Vendor -->
                                    <div class="markup-field markup-laminasi-vendor-field" style="display: none;">

                                        <label class="category-label">
                                            Vendor
                                        </label>

                                        <select class="select markup-laminasi-vendor" disabled>

                                            <option value="">
                                                Pilih Vendor
                                            </option>

                                            @foreach ($vendors as $vendor)
                                                <option value="{{ $vendor->id }}">
                                                    {{ $vendor->name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>


                                    <!-- Kategori -->
                                    <div class="markup-field markup-laminasi-category-field">

                                        <label class="category-label">
                                            Kategori
                                        </label>

                                        <select class="select markup-laminasi-category" required>

                                            <option value="">
                                                Pilih Kategori
                                            </option>

                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}">
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach

                                        </select>

                                    </div>

                                    <!-- Kategori Digital Print A3+ (multi pilih) -->
                                    <div class="markup-field markup-laminasi-category-checkbox-field"
                                        style="display: none;">

                                        <label class="category-label">
                                            Kategori
                                        </label>

                                                                            <div class="digital-lam-category-group category-checkboxes">
                                            @foreach ($categories as $category)
                                                @continue(in_array(strtolower(preg_replace('/[\s\/]+/', '', trim($category->name))), ['indoor&outdoor', 'indooroutdoor'], true))
                                                                                    <label class="category-checkbox">
                                                    <input type="checkbox" class="markup-laminasi-category-checkbox"
                                                        value="{{ $category->id }}">
                                                                                        <span class="category-checkbox-box">
                                                                                            <span class="category-checkbox-check">✓</span>
                                                                                            <span class="category-checkbox-label">{{ $category->name }}</span>
                                                                                        </span>
                                                                                    </label>
                                            @endforeach
                                        </div>

                                    </div>

                                </div>

                            </div>


                            <!-- BODY HEADER -->
                            <div class="markup-header-body">

                                <div class="markup-body-header">

                                    <div>

                                        <label class="category-label">
                                            Aturan Markup
                                        </label>

                                        <p class="category-description">
                                            Tambahkan tipe harga yang berlaku
                                            untuk mesin, lokasi dan kategori ini.
                                        </p>

                                    </div>

                                    <button type="button" class="btn btn--ghost btnTambahMarkupLaminasi">

                                        <svg viewBox="0 0 24 24">
                                            <path d="M12 5v14" />
                                            <path d="M5 12h14" />
                                        </svg>

                                        Tambah Markup

                                    </button>

                                </div>


                                <!-- ROW MARKUP -->
                                <div class="markup-rules-container">

                                    <!-- MARKUP ROW #0 -->
                                    <div class="markup-row markup-rule-row" data-rule-index="0">

                                        <input type="hidden" name="pricing_rules[0][engine_id]"
                                            class="markup-laminasi-engine-id">

                                        <input type="hidden" name="pricing_rules[0][location_id]"
                                            class="markup-laminasi-location-id">

                                        <input type="hidden" name="pricing_rules[0][vendor_id]"
                                            class="markup-laminasi-vendor-id">

                                        <input type="hidden" name="pricing_rules[0][category_id]"
                                            class="markup-laminasi-category-id">


                                        <!-- Tombol hapus -->
                                        <div class="markup-remove">

                                            <i class="bi bi-trash3-fill btn-remove-markup-laminasi"
                                                style="
                                            cursor: pointer;
                                            border: none !important;
                                        "></i>

                                        </div>


                                        <!-- Tipe Harga -->
                                        <div class="markup-field">

                                            <label class="category-label">
                                                Tipe Harga
                                            </label>

                                            <select name="pricing_rules[0][price_type]"
                                                class="select markup-laminasi-price-type" required>

                                                <option value="">
                                                    Pilih Tipe Harga
                                                </option>

                                                <option value="general">
                                                    Harga Umum
                                                </option>

                                                <option value="division">
                                                    Harga Divisi
                                                </option>

                                                <option value="plain">
                                                    Harga Polos
                                                </option>

                                            </select>

                                        </div>


                                        <!-- Markup -->
                                        <div class="markup-field">

                                            <label class="category-label">
                                                Markup (%)
                                            </label>

                                            <input type="number" name="pricing_rules[0][markup_percentage]"
                                                class="input markup-laminasi-percentage" min="0" step="0.01"
                                                placeholder="Contoh: 10" required>

                                        </div>


                                        <!-- Pembulatan -->
                                        <div class="markup-field">

                                            <label class="category-label">
                                                Pembulatan (Rp)
                                            </label>

                                            <input type="text" name="pricing_rules[0][rounding_value]"
                                                class="input markup-laminasi-rounding" inputmode="numeric"
                                                autocomplete="off" placeholder="Contoh: Rp 1.000" required>

                                        </div>


                                        <!-- Status -->
                                        <div class="markup-field">

                                            <label class="category-label">
                                                Status
                                            </label>

                                            <select name="pricing_rules[0][status]" class="select markup-laminasi-status"
                                                required>

                                                <option value="Active">
                                                    Active
                                                </option>

                                                <option value="Inactive">
                                                    Inactive
                                                </option>

                                            </select>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- FOOTER -->
                <div class="modal-footer">

                    <button type="button" class="btn btn--ghost" id="btnBatalTambahMarkupLaminasi">
                        Batal
                    </button>

                    <button type="submit" class="btn btn--primary" id="btnSimpanMarkupLaminasi">
                        Simpan Markup
                    </button>

                </div>

            </form>

        </div>

    </div>

    {{-- Modal edit --}}

    <div class="modal-overlay" id="modalEditMarkupLaminasi">
        <div class="modal-dialog modal-dialog--wide">
            <form method="POST" id="formEditMarkupLaminasi" novalidate>
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <div>
                        <span class="eyebrow">MARKUP HARGA · LAMINASI</span>
                        <br>
                        <span class="eyebrow">Edit Markup Harga</span>
                    </div>

                    <button type="button" class="modal-close" id="btnTutupModalEditMarkupLaminasi" aria-label="Tutup">
                        <svg viewBox="0 0 24 24">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="category-form-header">
                        <div>
                            <label class="category-label">
                                Data Markup Harga
                            </label>

                            <p class="category-description">
                                Edit aturan markup harga laminasi berdasarkan mesin, lokasi, vendor dan kategori.
                            </p>
                        </div>
                    </div>

                    <div class="markup-row" style="margin-bottom: 20px;">
                        <div class="markup-row-top">

                            {{-- Mesin --}}
                            <div class="markup-field">
                                <label class="category-label">
                                    Mesin
                                </label>

                                <select id="editMarkupLaminasiEngine" class="select select2" disabled>
                                    <option value="">
                                        Memuat mesin...
                                    </option>
                                </select>
                            </div>

                            {{-- Lokasi --}}
                            <div class="markup-field">
                                <label class="category-label">
                                    Lokasi
                                </label>

                                <select id="editMarkupLaminasiLocation" class="select select2" disabled>
                                    <option value="">
                                        Memuat lokasi...
                                    </option>
                                </select>
                            </div>

                            {{-- Vendor --}}
                            <div class="markup-field">
                                <label class="category-label">
                                    Vendor
                                </label>

                                <select id="editMarkupLaminasiVendor" class="select select2" disabled>
                                    <option value="">
                                        Memuat vendor...
                                    </option>
                                </select>
                            </div>

                            {{-- Kategori --}}
                            <div class="markup-field">
                                <label class="category-label">
                                    Kategori
                                </label>

                                <select id="editMarkupLaminasiCategory" class="select select2" disabled>
                                    <option value="">
                                        Memuat kategori...
                                    </option>
                                </select>
                            </div>

                        </div>
                    </div>

                    <div
                        style="
                        margin-top: 20px;
                        margin-bottom: 20px;
                        display: flex;
                        justify-content: right;
                        align-items: center;
                        gap: 20px;
                    ">
                        <button type="button" class="btn btn--ghost" id="btnTambahEditMarkupLaminasi">
                            <i class="bi bi-plus-lg"></i>
                            Tambah Markup
                        </button>
                    </div>

                    <div id="editMarkupLaminasiRows" class="mt-3"
                        style="
                        display: flex;
                        flex-direction: column;
                        gap: 20px;
                    ">
                        <!-- Row markup akan diisi oleh JavaScript -->
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn--ghost" id="btnBatalEditMarkupLaminasi">
                        Batal
                    </button>

                    <button type="submit" class="btn btn--primary">
                        <svg viewBox="0 0 24 24">
                            <path d="M5 12l4 4L19 6" />
                        </svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal detail --}}
    <div class="modal-overlay" id="modalDetailMarkupLaminasi">

        <div class="modal-dialog modal-dialog--wide">

            <div class="modal-header">

                <div>
                    <span class="eyebrow">
                        MARKUP HARGA · LAMINASI
                    </span>

                    <br>

                    <span class="eyebrow">
                        Detail Markup Harga
                    </span>
                </div>

                <button type="button" class="modal-close" id="btnTutupModalDetailMarkupLaminasi" aria-label="Tutup">
                    <svg viewBox="0 0 24 24">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>

            </div>


            <div class="modal-body">

                <div class="category-form-header">

                    <div>

                        <label class="category-label">
                            Detail Markup Harga Laminasi
                        </label>

                        <p class="category-description">
                            Informasi aturan markup harga laminasi
                            berdasarkan mesin, lokasi, vendor dan kategori.
                        </p>

                    </div>

                </div>


                {{-- =====================================================
            ENGINE, LOCATION, VENDOR & CATEGORY
            ====================================================== --}}

                <div class="markup-row" style="margin-bottom: 20px;">

                    <div class="markup-row-top">

                        {{-- MESIN --}}
                        <div class="markup-field">

                            <label class="category-label">
                                Mesin
                            </label>

                            <div class="input" id="detailMarkupLaminasiEngine">
                                -
                            </div>

                        </div>


                        {{-- LOKASI --}}
                        <div class="markup-field">

                            <label class="category-label">
                                Lokasi
                            </label>

                            <div class="input" id="detailMarkupLaminasiLocation">
                                -
                            </div>

                        </div>


                        {{-- VENDOR --}}
                        <div class="markup-field">

                            <label class="category-label">
                                Vendor
                            </label>

                            <div class="input" id="detailMarkupLaminasiVendor">
                                -
                            </div>

                        </div>


                        {{-- KATEGORI --}}
                        <div class="markup-field">

                            <label class="category-label">
                                Kategori
                            </label>

                            <div class="input" id="detailMarkupLaminasiCategory">
                                -
                            </div>

                        </div>

                    </div>

                </div>


                {{-- =====================================================
            DETAIL CONTENT
            ====================================================== --}}

                <div id="detailMarkupLaminasiContent"
                    style="
                    margin-top: 20px;
                    display: flex;
                    flex-direction: column;
                    gap: 20px;
                ">

                    {{-- Konten detail akan diisi melalui JavaScript --}}

                </div>

            </div>


            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDetailMarkupLaminasi">
                    Tutup
                </button>

            </div>

        </div>

    </div>

    <!-- MODAL DELETE PRICING RULE LAMINASI -->
    <div class="modal-overlay" id="modalDeleteMarkupLaminasi">
        <div class="modal-dialog modal-delete">

            <div class="modal-header">
                <div>
                    <span class="eyebrow">
                        MARKUP HARGA · LAMINASI
                    </span>
                    <br>
                    <span class="eyebrow">
                        Hapus Markup Harga
                    </span>
                </div>

                <button type="button" class="modal-close" id="btnTutupDeleteMarkupLaminasi" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6L6 18" />
                        <path d="M6 6L18 18" />
                    </svg>
                </button>
            </div>

            <div class="modal-body">

                <div class="delete-confirmation">

                    <div class="delete-icon">
                        <i class="bi bi-trash3-fill"></i>
                    </div>

                    <div class="delete-content">

                        <h3>
                            Hapus markup harga laminasi?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus seluruh
                            konfigurasi markup harga laminasi untuk mesin,
                            lokasi, dan kategori ini? Data yang sudah
                            dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteMarkupLaminasi">
                    Batal
                </button>

                <button type="button" class="btn btn--danger" id="btnConfirmDeleteMarkupLaminasi">
                    <i class="bi bi-trash3-fill"></i>
                    Hapus
                </button>

            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let laminationPricingRuleTable;

        document.addEventListener('DOMContentLoaded', function() {

            $('#laminationPricingRuleEngineFilter').select2({
                width: '100%'
            });

            const modalDownloadTemplateLaminationPricingRules =
                document.getElementById(
                    'modalDownloadTemplateLaminationPricingRules'
                );

            function closeDownloadTemplateLaminationPricingRulesModal() {
                modalDownloadTemplateLaminationPricingRules.classList.remove(
                    'is-open'
                );
            }

            document.getElementById('btnDownloadTemplateLaminationPricingRules')
                .addEventListener('click', function() {
                    modalDownloadTemplateLaminationPricingRules.classList.add(
                        'is-open'
                    );
                });

            document.getElementById('btnCloseDownloadTemplateLaminationPricingRules')
                .addEventListener(
                    'click',
                    closeDownloadTemplateLaminationPricingRulesModal
                );

            document.getElementById('btnCancelDownloadTemplateLaminationPricingRules')
                .addEventListener(
                    'click',
                    closeDownloadTemplateLaminationPricingRulesModal
                );

            modalDownloadTemplateLaminationPricingRules.addEventListener(
                'click',
                function(event) {
                    if (event.target === modalDownloadTemplateLaminationPricingRules) {
                        closeDownloadTemplateLaminationPricingRulesModal();
                    }
                }
            );

            document.addEventListener('keydown', function(event) {
                if (
                    event.key === 'Escape' &&
                    modalDownloadTemplateLaminationPricingRules.classList.contains(
                        'is-open'
                    )
                ) {
                    closeDownloadTemplateLaminationPricingRulesModal();
                }
            });

            laminationPricingRuleTable = new DataTable('#laminationPricingRuleTable', {

                /*
                |--------------------------------------------------------------------------
                | PROCESSING
                |--------------------------------------------------------------------------
                */

                processing: true,
                serverSide: true,
                ordering: false,

                /*
                |--------------------------------------------------------------------------
                | RESPONSIVE
                |--------------------------------------------------------------------------
                */

                responsive: {
                    details: {
                        type: 'column',
                        target: 0
                    }
                },

                /*
                |--------------------------------------------------------------------------
                | AJAX
                |--------------------------------------------------------------------------
                */

                ajax: {
                    url: "{{ route('admin_data_lamination_pricing_rule') }}",
                    type: "GET",
                    data: function(data) {
                        data.pricing_engine =
                            document.getElementById(
                                'laminationPricingRuleEngineFilter'
                            ).value;
                    }
                },

                /*
                |--------------------------------------------------------------------------
                | COLUMNS
                |--------------------------------------------------------------------------
                */

                columns: [

                    /*
                    |--------------------------------------------------------------------------
                    | RESPONSIVE CONTROL
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: null,
                        defaultContent: '',
                        className: 'dtr-control',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | NO
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',
                        render: function(data, type, row, meta) {

                            const pageInfo =
                                laminationPricingRuleTable.page.info();

                            return pageInfo.start + meta.row + 1;
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | MESIN
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'engine_name',
                        responsivePriority: 1,
                        render: function(data) {
                            return data ?? '-';
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | LOKASI
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'location_name',
                        responsivePriority: 1,
                        render: function(data) {
                            return data ?? '-';
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | VENDOR
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'vendor_name',
                        responsivePriority: 1,
                        render: function(data) {
                            return data ?? '-';
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | KATEGORI
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'category_name',
                        responsivePriority: 1,
                        render: function(data) {
                            return data ?? '-';
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | JUMLAH MARKUP
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'jumlah_markup',
                        responsivePriority: 2,
                        className: 'text-center',

                        render: function(data) {

                            if (
                                data === null ||
                                data === undefined ||
                                data === ''
                            ) {
                                return '0';
                            }

                            return Number(data)
                                .toLocaleString('id-ID');
                        },

                        createdCell: function(
                            td,
                            cellData,
                            rowData,
                            row,
                            col
                        ) {
                            $(td).css('text-align', 'center');
                        }
                    },

                    /*
                    |--------------------------------------------------------------------------
                    | AKSI
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',

                        render: function(data, type, row) {

                            return `
                        <div
                            class="data-cell-actions"
                            style="
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                gap: 5px;
                            "
                        >

                            <button
                                type="button"
                                class="btn--icon btn-detail-lamination-pricing-rule"
                                data-pricing-rule-source="${row.pricing_rule_source}"
                                data-digital-pricing-rule-id="${row.digital_pricing_rule_id ?? ''}"
                                data-engine-id="${row.engine_id}"
                                data-location-id="${row.location_id}"
                                data-vendor-id="${row.vendor_id ?? 'null'}"
                                data-category-id="${row.category_id}"
                                aria-label="Detail"
                            >
                                <i class="bi bi-arrows-fullscreen"></i>
                            </button>

                            ${row.pricing_rule_source === 'digital' ? `
                            <button
                                type="button"
                                class="btn--icon btn-edit-digital-lamination-pricing-rule"
                                data-digital-pricing-rule-id="${row.digital_pricing_rule_id}"
                                aria-label="Edit"
                            >
                                <i class="bi bi-pen"></i>
                            </button>
                            ` : `
                            <button
                                type="button"
                                class="btn--icon btn-edit-lamination-pricing-rule"
                                data-engine-id="${row.engine_id}"
                                data-location-id="${row.location_id}"
                                data-vendor-id="${row.vendor_id ?? 'null'}"
                                data-category-id="${row.category_id}"
                                aria-label="Edit"
                            >
                                <i class="bi bi-pen"></i>
                            </button>
                            `}

                            ${row.pricing_rule_source === 'digital' ? `
                            <button
                                type="button"
                                class="btn--icon btn-delete-digital-lamination-pricing-rule"
                                data-digital-pricing-rule-id="${row.digital_pricing_rule_id}"
                                aria-label="Hapus"
                            >
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                            ` : `
                            <button
                                type="button"
                                class="btn--icon btn-delete-lamination-pricing-rule"
                                data-engine-id="${row.engine_id}"
                                data-location-id="${row.location_id}"
                                data-vendor-id="${row.vendor_id ?? 'null'}"
                                data-category-id="${row.category_id}"
                                aria-label="Hapus"
                            >
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                            `}

                        </div>
                    `;
                        }
                    }
                ],

                /*
                |--------------------------------------------------------------------------
                | COLUMN DEFINITION
                |--------------------------------------------------------------------------
                */

                columnDefs: [{
                    targets: 0,
                    className: 'dtr-control'
                }],

                /*
                |--------------------------------------------------------------------------
                | PAGE LENGTH
                |--------------------------------------------------------------------------
                */

                pageLength: 10,

                /*
                |--------------------------------------------------------------------------
                | PAGE LENGTH OPTIONS
                |--------------------------------------------------------------------------
                */

                lengthMenu: [
                    10,
                    25,
                    50,
                    100
                ],

                /*
                |--------------------------------------------------------------------------
                | LAYOUT
                |--------------------------------------------------------------------------

                */

                layout: {
                    topStart: 'pageLength',
                    topEnd: 'search',
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },

                /*
                |--------------------------------------------------------------------------
                | LANGUAGE
                |--------------------------------------------------------------------------
                */

                language: {
                    search: 'Search:',
                    lengthMenu: '_MENU_ entries per page',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    zeroRecords: 'Data markup laminasi tidak ditemukan',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari mesin, lokasi, vendor, atau kategori...'
                },

                /*
                |--------------------------------------------------------------------------
                | INIT COMPLETE
                |--------------------------------------------------------------------------
                */

                initComplete: function() {

                    $(this)
                        .closest(
                            '.dt-container, .dataTables_wrapper'
                        )
                        .find(
                            '.dt-search input, .dataTables_filter input'
                        )
                        .css({
                            'padding': '9px 14px',
                            'border-radius': '8px',
                            'border': '1px solid #e2e8f0'
                        });
                }
            });

            $('#btnExportLaminationPricingRules').on('click', function(e) {
                e.preventDefault();
                const engine = $('#laminationPricingRuleEngineFilter').val() || 'large_format';
                window.location.href = $(this).attr('href') + '?pricing_engine=' + encodeURIComponent(engine);
            });

            $('#laminationPricingRuleEngineFilter').on('change', function() {
                laminationPricingRuleTable.ajax.reload(null, true);
            });
        });

        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('modalTambahMarkupLaminasi');
            const form = document.getElementById('formTambahMarkupLaminasi');
            const btnClose = document.getElementById('btnTutupModalTambahMarkupLaminasi');
            const btnCancel = document.getElementById('btnBatalTambahMarkupLaminasi');
            const btnTambahHeader = document.getElementById('btnTambahHeaderMarkupLaminasi');
            const headerContainer = document.getElementById('markupLaminasiHeaderContainer');
            const btnSimpan = document.getElementById('btnSimpanMarkupLaminasi');

            let headerIndex = 1;
            let ruleIndex = 1;


            /* =========================================================
             * RUPIAH FORMAT
             * ========================================================= */

            function formatRupiah(value) {

                const number = String(value).replace(/\D/g, '');

                if (!number) {
                    return '';
                }

                return 'Rp ' + number.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function unformatRupiah(value) {

                return String(value).replace(/\D/g, '');
            }


            /* =========================================================
             * SELECT2
             * ========================================================= */

            function initSelect2(container) {

                $(container).find('select.select').each(function() {

                    const $select = $(this);

                    if ($select.hasClass('select2-hidden-accessible')) {
                        return;
                    }

                    $select.select2({
                        width: '100%',
                        dropdownParent: $('#modalTambahMarkupLaminasi')
                    });

                });
            }


            function destroySelect2(container) {

                $(container).find('select.select').each(function() {

                    const $select = $(this);

                    if ($select.hasClass('select2-hidden-accessible')) {
                        $select.select2('destroy');
                    }

                });
            }


            /* =========================================================
             * MODAL
             * ========================================================= */

            function openTambahMarkupLaminasiModal() {

                modal.classList.add('is-open');

                initSelect2(modal);

                syncAllHeaderValues();
            }


            function closeTambahMarkupLaminasiModal() {

                modal.classList.remove('is-open');

                resetTambahMarkupLaminasiForm();
            }


            const btnOpen = document.getElementById('btnTambahMarkupLaminasi');

            if (btnOpen) {

                btnOpen.addEventListener('click', function() {

                    openTambahMarkupLaminasiModal();

                });

            }


            if (btnClose) {

                btnClose.addEventListener('click', function() {

                    closeTambahMarkupLaminasiModal();

                });

            }


            if (btnCancel) {

                btnCancel.addEventListener('click', function() {

                    closeTambahMarkupLaminasiModal();

                });

            }


            if (modal) {

                modal.addEventListener('click', function(e) {

                    if (e.target === modal) {

                        closeTambahMarkupLaminasiModal();

                    }

                });

            }


            document.addEventListener('keydown', function(e) {

                if (
                    e.key === 'Escape' &&
                    modal.classList.contains('is-open')
                ) {

                    closeTambahMarkupLaminasiModal();

                }

            });


            /* =========================================================
             * HEADER / RULE GETTER
             * ========================================================= */

            function getHeaderGroups() {

                return headerContainer.querySelectorAll(
                    '.markup-header-group'
                );

            }


            function getRuleRows(header) {

                return header.querySelectorAll(
                    '.markup-rule-row'
                );

            }


            /* =========================================================
             * SYNC HEADER VALUES
             * ========================================================= */

            function syncHeaderValues(header) {

                const engine = header.querySelector(
                    '.markup-laminasi-engine'
                );

                const location = header.querySelector(
                    '.markup-laminasi-location'
                );

                const vendor = header.querySelector(
                    '.markup-laminasi-vendor'
                );

                const category = header.querySelector(
                    '.markup-laminasi-category'
                );

                const engineId = header.querySelectorAll(
                    '.markup-laminasi-engine-id'
                );

                const locationId = header.querySelectorAll(
                    '.markup-laminasi-location-id'
                );

                const vendorId = header.querySelectorAll(
                    '.markup-laminasi-vendor-id'
                );

                const categoryId = header.querySelectorAll(
                    '.markup-laminasi-category-id'
                );


                const selectedEngine = engine ?
                    engine.value :
                    '';

                const selectedLocation = location ?
                    location.value :
                    '';

                const selectedVendor = vendor ?
                    vendor.value :
                    '';

                const selectedCategory = category ?
                    category.value :
                    '';


                engineId.forEach(function(input) {

                    input.value = selectedEngine;

                });


                locationId.forEach(function(input) {

                    input.value = selectedLocation;

                });


                vendorId.forEach(function(input) {

                    input.value = selectedVendor;

                });


                categoryId.forEach(function(input) {

                    input.value = selectedCategory;

                });


                /* Digital Print A3+: kategori multi pilih (checkbox) */

                const isDigital = isDigitalLaminationHeader(header);

                const checkedCategories = isDigital ?
                    Array.from(header.querySelectorAll(
                        '.markup-laminasi-category-checkbox:checked'
                    )).map(function(checkbox) {
                        return checkbox.value;
                    }) :
                    [];

                getRuleRows(header).forEach(function(row) {

                    row.querySelectorAll('.markup-laminasi-category-ids')
                        .forEach(function(input) {
                            input.remove();
                        });

                    checkedCategories.forEach(function(value) {

                        const input = document.createElement('input');

                        input.type = 'hidden';
                        input.className = 'markup-laminasi-category-ids';
                        input.name =
                            `pricing_rules[${row.dataset.ruleIndex}][category_ids][]`;
                        input.value = value;

                        row.appendChild(input);

                    });

                });

            }


            function syncAllHeaderValues() {

                getHeaderGroups().forEach(function(header) {

                    syncHeaderValues(header);

                });

            }


            /* =========================================================
             * REINDEX RULE
             * ========================================================= */

            function reindexRuleRows() {

                let index = 0;

                getHeaderGroups().forEach(function(header) {

                    const rows = getRuleRows(header);

                    rows.forEach(function(row) {

                        row.dataset.ruleIndex = index;


                        const engineId = row.querySelector(
                            '.markup-laminasi-engine-id'
                        );

                        const locationId = row.querySelector(
                            '.markup-laminasi-location-id'
                        );

                        const vendorId = row.querySelector(
                            '.markup-laminasi-vendor-id'
                        );

                        const categoryId = row.querySelector(
                            '.markup-laminasi-category-id'
                        );

                        const priceType = row.querySelector(
                            '.markup-laminasi-price-type'
                        );

                        const percentage = row.querySelector(
                            '.markup-laminasi-percentage'
                        );

                        const rounding = row.querySelector(
                            '.markup-laminasi-rounding'
                        );

                        const status = row.querySelector(
                            '.markup-laminasi-status'
                        );


                        if (engineId) {

                            engineId.name =
                                `pricing_rules[${index}][engine_id]`;

                        }


                        if (locationId) {

                            locationId.name =
                                `pricing_rules[${index}][location_id]`;

                        }


                        if (vendorId) {

                            vendorId.name =
                                `pricing_rules[${index}][vendor_id]`;

                        }


                        if (categoryId) {

                            categoryId.name =
                                `pricing_rules[${index}][category_id]`;

                        }


                        row.querySelectorAll('.markup-laminasi-category-ids')
                            .forEach(function(input) {

                                input.name =
                                    `pricing_rules[${index}][category_ids][]`;

                            });


                        if (priceType) {

                            priceType.name =
                                `pricing_rules[${index}][price_type]`;

                        }


                        if (percentage) {

                            percentage.name =
                                `pricing_rules[${index}][markup_percentage]`;

                        }


                        if (rounding) {

                            rounding.name =
                                `pricing_rules[${index}][rounding_value]`;

                        }


                        if (status) {

                            status.name =
                                `pricing_rules[${index}][status]`;

                        }


                        const printType = row.querySelector(
                            '.markup-laminasi-print-type'
                        );

                        if (printType) {

                            printType.name =
                                `pricing_rules[${index}][print_type]`;

                        }


                        row.querySelectorAll('[data-pricing-suffix]')
                            .forEach(function(input) {

                                input.name =
                                    `pricing_rules[${index}][${input.dataset.pricingSuffix}]`;

                            });


                        index++;

                    });

                });


                ruleIndex = index;

            }


            /* =========================================================
             * VALIDATION HELPER
             * ========================================================= */

            function clearFieldError(field) {

                if (!field) {
                    return;
                }

                field.classList.remove('is-invalid');


                const formGroup = field.closest('.form-group');

                if (formGroup) {

                    const feedback = formGroup.querySelector(
                        '.invalid-feedback'
                    );

                    if (feedback) {

                        feedback.textContent = '';

                    }

                }


                if ($(field).hasClass('select2-hidden-accessible')) {

                    $(field)
                        .next('.select2')
                        .find('.select2-selection')
                        .removeClass('is-invalid');

                }

            }


            function setFieldError(field, message) {

                if (!field) {
                    return;
                }

                field.classList.add('is-invalid');


                const formGroup = field.closest('.form-group');

                if (formGroup) {

                    const feedback = formGroup.querySelector(
                        '.invalid-feedback'
                    );

                    if (feedback) {

                        feedback.textContent = message;

                    }

                }


                if ($(field).hasClass('select2-hidden-accessible')) {

                    $(field)
                        .next('.select2')
                        .find('.select2-selection')
                        .addClass('is-invalid');

                }

            }


            function clearAllValidation() {

                form.querySelectorAll('.is-invalid')
                    .forEach(function(element) {

                        element.classList.remove('is-invalid');

                    });


                form.querySelectorAll('.invalid-feedback')
                    .forEach(function(element) {

                        element.textContent = '';

                    });


                form.querySelectorAll(
                    '.select2-selection.is-invalid'
                ).forEach(function(element) {

                    element.classList.remove('is-invalid');

                });

            }


            /* =========================================================
             * DUPLICATE PRICE TYPE
             *
             * Kombinasi:
             * engine + location + vendor + category + price_type
             * ========================================================= */

            function validateDuplicatePriceTypes() {

                const combinations = new Map();

                let isValid = true;


                form.querySelectorAll(
                    '.markup-rule-row'
                ).forEach(function(row) {

                    const engineId = row.querySelector(
                        '.markup-laminasi-engine-id'
                    )?.value || '';

                    const locationId = row.querySelector(
                        '.markup-laminasi-location-id'
                    )?.value || '';

                    const vendorId = row.querySelector(
                        '.markup-laminasi-vendor-id'
                    )?.value || '';

                    const categoryId = row.querySelector(
                        '.markup-laminasi-category-id'
                    )?.value || '';

                    const digitalCategoryIds = Array.from(
                        row.querySelectorAll('.markup-laminasi-category-ids')
                    ).map(function(input) {
                        return input.value;
                    });

                    const priceType = row.querySelector(
                        '.markup-laminasi-price-type'
                    )?.value || '';

                    const printType = row.querySelector(
                        '.markup-laminasi-print-type:not(:disabled)'
                    )?.value || '';


                    const categoryKeys = digitalCategoryIds.length > 0 ?
                        digitalCategoryIds :
                        [categoryId];

                    if (
                        !engineId ||
                        !locationId ||
                        !categoryKeys[0] ||
                        !priceType
                    ) {

                        return;

                    }


                    let isDuplicate = false;

                    categoryKeys.forEach(function(categoryKey) {

                        const key = [

                            engineId,
                            locationId,
                            vendorId,
                            categoryKey,
                            priceType,
                            printType

                        ].join('|');

                        if (combinations.has(key)) {

                            isDuplicate = true;

                        } else {

                            combinations.set(key, row);

                        }

                    });


                    if (isDuplicate) {

                        const priceTypeField = row.querySelector(
                            '.markup-laminasi-price-type'
                        );


                        setFieldError(
                            priceTypeField,
                            'Price Type sudah digunakan pada kombinasi Engine, Location, Vendor, dan Category yang sama.'
                        );


                        isValid = false;

                    }

                });


                return isValid;

            }


            /* =========================================================
             * VENDOR FIELD
             * ========================================================= */

            function updateVendorField(header) {

                const location = header.querySelector(
                    '.markup-laminasi-location'
                );

                const vendorField = header.querySelector(
                    '.markup-laminasi-vendor-field'
                );

                const vendor = header.querySelector(
                    '.markup-laminasi-vendor'
                );


                if (
                    !location ||
                    !vendorField ||
                    !vendor
                ) {

                    return;

                }


                const selectedOption =
                    location.options[location.selectedIndex];


                const locationName = selectedOption ?
                    selectedOption.textContent
                    .trim()
                    .toLowerCase() :
                    '';


                if (locationName === 'outsourcing') {

                    vendorField.style.display = '';

                    vendor.disabled = false;

                } else {

                    vendorField.style.display = 'none';

                    vendor.disabled = true;

                    vendor.value = '';

                    $(vendor)
                        .val('')
                        .trigger('change');

                }


                syncHeaderValues(header);

            }


            /* =========================================================
             * FORM VALIDATION
             * ========================================================= */

            function validateForm() {

                clearAllValidation();

                let isValid = true;

                const headers = getHeaderGroups();


                if (headers.length === 0) {

                    if (typeof showToast === 'function') {

                        showToast(
                            'error',
                            'Minimal harus ada satu konfigurasi markup.'
                        );

                    }

                    return false;

                }


                headers.forEach(function(header) {

                    const engine = header.querySelector(
                        '.markup-laminasi-engine'
                    );

                    const location = header.querySelector(
                        '.markup-laminasi-location'
                    );

                    const vendor = header.querySelector(
                        '.markup-laminasi-vendor'
                    );

                    const category = header.querySelector(
                        '.markup-laminasi-category'
                    );


                    const selectedLocation =
                        location?.options[
                            location.selectedIndex
                        ];


                    const locationName = selectedLocation ?
                        selectedLocation.textContent
                        .trim()
                        .toLowerCase() :
                        '';


                    /* ENGINE */

                    if (!engine || !engine.value) {

                        setFieldError(
                            engine,
                            'Engine wajib dipilih.'
                        );

                        isValid = false;

                    }


                    /* LOCATION */

                    if (!location || !location.value) {

                        setFieldError(
                            location,
                            'Location wajib dipilih.'
                        );

                        isValid = false;

                    }


                    /* VENDOR */

                    if (locationName === 'outsourcing') {

                        if (!vendor || !vendor.value) {

                            setFieldError(
                                vendor,
                                'Vendor wajib dipilih untuk Location Outsourcing.'
                            );

                            isValid = false;

                        }

                    } else {

                        if (vendor) {

                            vendor.value = '';

                            $(vendor)
                                .val('')
                                .trigger('change');

                        }

                    }


                    /* CATEGORY */

                    if (isDigitalLaminationHeader(header)) {

                        const categoryGroup = header.querySelector(
                            '.digital-lam-category-group'
                        );

                        if (!header.querySelector(
                                '.markup-laminasi-category-checkbox:checked'
                            )) {

                            setFieldError(
                                categoryGroup,
                                'Minimal pilih satu kategori.'
                            );

                            if (typeof showToast === 'function') {

                                showToast(
                                    'error',
                                    'Minimal pilih satu kategori.'
                                );

                            }

                            isValid = false;

                        }

                    } else if (!category || !category.value) {

                        setFieldError(
                            category,
                            'Category wajib dipilih.'
                        );

                        isValid = false;

                    }


                    /* RULE */

                    const rows = getRuleRows(header);


                    if (rows.length === 0) {

                        if (typeof showToast === 'function') {

                            showToast(
                                'error',
                                'Setiap header harus memiliki minimal satu markup.'
                            );

                        }

                        isValid = false;

                    }


                    rows.forEach(function(row) {

                        const priceType = row.querySelector(
                            '.markup-laminasi-price-type'
                        );

                        const percentage = row.querySelector(
                            '.markup-laminasi-percentage'
                        );

                        const rounding = row.querySelector(
                            '.markup-laminasi-rounding'
                        );

                        const status = row.querySelector(
                            '.markup-laminasi-status'
                        );


                        /* PRICE TYPE */

                        if (!priceType || !priceType.value) {

                            setFieldError(
                                priceType,
                                'Price Type wajib dipilih.'
                            );

                            isValid = false;

                        }


                        /* DIGITAL PRINT A3+ */

                        const isDigitalRow =
                            row.classList.contains('is-digital-lamination');

                        if (isDigitalRow) {

                            const digitalPrintType = row.querySelector(
                                '.markup-laminasi-print-type'
                            );

                            if (!digitalPrintType || !digitalPrintType.value) {

                                setFieldError(
                                    digitalPrintType,
                                    'Harga Print wajib dipilih.'
                                );

                                isValid = false;

                            }

                            row.querySelectorAll('.digital-lam-tier-percentage')
                                .forEach(function(input) {

                                    const value = input.value.trim();

                                    if (
                                        !value ||
                                        isNaN(value) ||
                                        Number(value) < 0
                                    ) {

                                        setFieldError(
                                            input,
                                            'Markup wajib berupa angka dan tidak boleh negatif.'
                                        );

                                        isValid = false;

                                    }

                                });

                        }


                        /* MARKUP */

                        const markupValue = percentage ?
                            percentage.value.trim() :
                            '';


                        if (
                            !isDigitalRow &&
                            (
                                !markupValue ||
                                isNaN(markupValue) ||
                                Number(markupValue) < 0
                            )
                        ) {

                            setFieldError(
                                percentage,
                                'Markup wajib berupa angka dan tidak boleh negatif.'
                            );

                            isValid = false;

                        }


                        /* ROUNDING */

                        if (
                            rounding &&
                            rounding.value.trim() !== ''
                        ) {

                            const roundingValue =
                                unformatRupiah(
                                    rounding.value
                                );


                            if (
                                roundingValue !== '' &&
                                Number(roundingValue) < 0
                            ) {

                                setFieldError(
                                    rounding,
                                    'Rounding tidak boleh negatif.'
                                );

                                isValid = false;

                            }

                        }


                        /* STATUS */

                        if (!status || !status.value) {

                            setFieldError(
                                status,
                                'Status wajib dipilih.'
                            );

                            isValid = false;

                        }

                    });

                });


                syncAllHeaderValues();


                if (!validateDuplicatePriceTypes()) {

                    isValid = false;

                }


                return isValid;

            }


            /* =========================================================
             * DIGITAL PRINT A3+ FIELDS
             * ========================================================= */

            function isDigitalLaminationHeader(header) {

                const engine = header.querySelector('.markup-laminasi-engine');

                const engineName = engine && engine.selectedIndex >= 0 ?
                    engine.options[engine.selectedIndex].textContent
                    .trim()
                    .toLowerCase()
                    .replace(/\s+/g, '') :
                    '';

                return engineName === 'digitalprinta3+';

            }


            function createDigitalLaminationFields(index) {

                const tiers = [
                    ['1 lbr', 'tier_1'],
                    ['2 - 5 lbr', 'tier_2_5'],
                    ['6 - 50 lbr', 'tier_6_50'],
                    ['100 lbr', 'tier_100'],
                    ['300 lbr', 'tier_300'],
                    ['500 lbr', 'tier_500']
                ];

                return `
                    <div class="markup-laminasi-digital-fields" style="display: none;">
                        <div class="markup-field digital-lam-print-type-field">
                            <label class="category-label">Harga Print</label>
                            <select
                                name="pricing_rules[${index}][print_type]"
                                class="select markup-laminasi-print-type"
                                required
                                disabled
                            >
                                <option value="">Pilih Harga Print</option>
                                <option value="single">1 Muka</option>
                                <option value="double">2 Muka</option>
                            </select>
                        </div>
                        <div class="digital-lam-pricing-tiers">
                            ${tiers.map(([label, suffix]) => `
                                <div class="digital-lam-price-tier">
                                    <label class="category-label">${label}</label>
                                    <div class="markup-field">
                                        <label class="category-label">Markup (%)</label>
                                        <input
                                            type="number"
                                            name="pricing_rules[${index}][${suffix}_markup_percentage]"
                                            data-pricing-suffix="${suffix}_markup_percentage"
                                            class="input digital-lam-tier-percentage"
                                            min="0"
                                            step="0.01"
                                            placeholder="Contoh: 10"
                                            required
                                            disabled
                                        >
                                    </div>
                                    <div class="markup-field">
                                        <label class="category-label">Pembulatan (Rp)</label>
                                        <input
                                            type="text"
                                            name="pricing_rules[${index}][${suffix}_rounding_value]"
                                            data-pricing-suffix="${suffix}_rounding_value"
                                            class="input digital-lam-tier-rounding"
                                            inputmode="numeric"
                                            autocomplete="off"
                                            placeholder="Contoh: Rp 1.000"
                                            disabled
                                        >
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;

            }


            function installDigitalLaminationFields(row, index) {

                const statusField = row.querySelector('.markup-laminasi-status')
                    .closest('.markup-field');

                const wrapper = document.createElement('div');

                wrapper.innerHTML = createDigitalLaminationFields(index);

                row.insertBefore(wrapper.firstElementChild, statusField);

                statusField.classList.add('markup-laminasi-status-field');

            }


            function updateLaminationBody(header) {

                const isDigital = isDigitalLaminationHeader(header);

                const categorySelectField = header.querySelector('.markup-laminasi-category-field');
                const categoryCheckboxField = header.querySelector('.markup-laminasi-category-checkbox-field');
                const categorySelect = header.querySelector('.markup-laminasi-category');

                if (categorySelectField && categoryCheckboxField && categorySelect) {

                    categorySelectField.style.display = isDigital ? 'none' : '';
                    categoryCheckboxField.style.display = isDigital ? '' : 'none';
                    categorySelect.disabled = isDigital;

                    if (isDigital && categorySelect.value !== '') {

                        $(categorySelect).val('').trigger('change.select2');

                    }

                    if (!isDigital) {

                        header.querySelectorAll('.markup-laminasi-category-checkbox')
                            .forEach(function(checkbox) {
                                checkbox.checked = false;
                            });

                    }

                }

                getRuleRows(header).forEach(function(row) {

                    row.classList.toggle('is-digital-lamination', isDigital);

                    const percentage = row.querySelector('.markup-laminasi-percentage');
                    const rounding = row.querySelector('.markup-laminasi-rounding');
                    const digitalFields = row.querySelector('.markup-laminasi-digital-fields');
                    const printType = row.querySelector('.markup-laminasi-print-type');
                    const priceType = row.querySelector('.markup-laminasi-price-type');
                    const plainOption = priceType ?
                        priceType.querySelector('option[value="plain"]') :
                        null;

                    [percentage, rounding].forEach(function(input) {

                        if (!input) {
                            return;
                        }

                        input.closest('.markup-field').style.display =
                            isDigital ? 'none' : '';

                        input.disabled = isDigital;

                    });

                    if (digitalFields) {
                        digitalFields.style.display = isDigital ? '' : 'none';
                    }

                    if (printType) {
                        printType.disabled = !isDigital;
                    }

                    row.querySelectorAll(
                        '.digital-lam-tier-percentage, .digital-lam-tier-rounding'
                    ).forEach(function(input) {
                        input.disabled = !isDigital;
                    });

                    if (plainOption) {

                        plainOption.disabled = isDigital;
                        plainOption.hidden = isDigital;

                        if (isDigital && priceType.value === 'plain') {

                            priceType.value = '';

                            $(priceType).trigger('change.select2');

                        }

                    }

                });

            }


            /* =========================================================
             * CREATE RULE ROW
             * ========================================================= */

            function createMarkupRow(header, index) {

                const ruleContainer = header.querySelector(
                    '.markup-rules-container'
                );


                if (!ruleContainer) {

                    return null;

                }


                const row = document.createElement('div');

                row.className =
                    'markup-row markup-rule-row';

                row.dataset.ruleIndex = index;


                row.innerHTML = `

            <input type="hidden"
                name="pricing_rules[${index}][engine_id]"
                class="markup-laminasi-engine-id">

            <input type="hidden"
                name="pricing_rules[${index}][location_id]"
                class="markup-laminasi-location-id">

            <input type="hidden"
                name="pricing_rules[${index}][vendor_id]"
                class="markup-laminasi-vendor-id">

            <input type="hidden"
                name="pricing_rules[${index}][category_id]"
                class="markup-laminasi-category-id">


            <div class="markup-remove">

                <i class="bi bi-trash3-fill btn-remove-markup-laminasi"
                    style="
                    cursor: pointer;
                    border: none !important;
                "></i>

            </div>


            <div class="markup-field">

                <label class="category-label">
                    Tipe Harga
                </label>

                <select name="pricing_rules[${index}][price_type]"
                    class="select markup-laminasi-price-type"
                    required>

                    <option value="">
                        Pilih Tipe Harga
                    </option>

                    <option value="general">
                        Harga Umum
                    </option>

                    <option value="division">
                        Harga Divisi
                    </option>

                    <option value="plain">
                        Harga Polos
                    </option>

                </select>

            </div>


            <div class="markup-field">

                <label class="category-label">
                    Markup (%)
                </label>

                <input type="number"
                    name="pricing_rules[${index}][markup_percentage]"
                    class="input markup-laminasi-percentage"
                    min="0"
                    step="0.01"
                    placeholder="Contoh: 10"
                    required>

            </div>


            <div class="markup-field">

                <label class="category-label">
                    Pembulatan (Rp)
                </label>

                <input type="text"
                    name="pricing_rules[${index}][rounding_value]"
                    class="input markup-laminasi-rounding"
                    inputmode="numeric"
                    autocomplete="off"
                    placeholder="Contoh: Rp 1.000"
                    required>

            </div>


            <div class="markup-field">

                <label class="category-label">
                    Status
                </label>

                <select name="pricing_rules[${index}][status]"
                    class="select markup-laminasi-status"
                    required>

                    <option value="Active">
                        Active
                    </option>

                    <option value="Inactive">
                        Inactive
                    </option>

                </select>

            </div>

        `;


                installDigitalLaminationFields(row, index);

                ruleContainer.appendChild(row);


                initSelect2(row);

                updateLaminationBody(header);

                syncHeaderValues(header);


                const btnRemove = row.querySelector(
                    '.btn-remove-markup-laminasi'
                );


                btnRemove.addEventListener(
                    'click',
                    function() {

                        const rows = getRuleRows(header);


                        if (rows.length <= 1) {

                            if (typeof showToast === 'function') {

                                showToast(
                                    'error',
                                    'Minimal harus ada satu markup.'
                                );

                            }

                            return;

                        }


                        destroySelect2(row);

                        row.remove();

                        reindexRuleRows();

                        syncHeaderValues(header);

                        updateRemoveButtons(header);

                    }
                );


                updateRemoveButtons(header);


                return row;

            }


            /* =========================================================
             * UPDATE REMOVE BUTTONS
             * ========================================================= */

            function updateRemoveButtons(header) {

                const rows = getRuleRows(header);


                rows.forEach(function(row) {

                    const button = row.querySelector(
                        '.btn-remove-markup-laminasi'
                    );


                    if (!button) {
                        return;
                    }


                    button.style.display = '';

                });

            }


            /* =========================================================
             * CREATE HEADER
             * ========================================================= */

            function createMarkupHeader(index) {

                const header = document.createElement('div');

                header.className =
                    'markup-header-group';

                header.dataset.headerIndex =
                    index;


                header.innerHTML = `

            <div class="markup-row markup-header-row">

                <div
                    style="
                    display: flex;
                    justify-content: right;
                    align-items: center;
                    gap: 10px;
                ">

                    <div class="markup-header-action">

                        <i class="bi bi-trash3-fill btn-remove-header-laminasi"
                            style="
                            cursor: pointer;
                            border: none !important;
                        "></i>

                    </div>

                </div>


                <div class="markup-row-top">


                    <!-- MESIN -->

                    <div class="markup-field">

                        <label class="category-label">
                            Mesin
                        </label>

                        <select class="select markup-laminasi-engine"
                            required>

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


                    <!-- LOKASI -->

                    <div class="markup-field">

                        <label class="category-label">
                            Lokasi
                        </label>

                        <select class="select markup-laminasi-location"
                            required>

                            <option value="">
                                Pilih Lokasi
                            </option>

                            @foreach ($locations as $location)

                                <option value="{{ $location->id }}">
                                    {{ $location->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <!-- VENDOR -->

                    <div
                        class="markup-field markup-laminasi-vendor-field"
                        style="display: none;">

                        <label class="category-label">
                            Vendor
                        </label>

                        <select class="select markup-laminasi-vendor"
                            disabled>

                            <option value="">
                                Pilih Vendor
                            </option>

                            @foreach ($vendors as $vendor)

                                <option value="{{ $vendor->id }}">
                                    {{ $vendor->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    <!-- KATEGORI -->

                    <div class="markup-field markup-laminasi-category-field">

                        <label class="category-label">
                            Kategori
                        </label>

                        <select class="select markup-laminasi-category"
                            required>

                            <option value="">
                                Pilih Kategori
                            </option>

                            @foreach ($categories as $category)

                                <option value="{{ $category->id }}">
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="markup-field markup-laminasi-category-checkbox-field"
                        style="display: none;">

                        <label class="category-label">
                            Kategori
                        </label>

                        <div class="digital-lam-category-group category-checkboxes">
                            @foreach ($categories as $category)
                                @continue(in_array(strtolower(preg_replace('/[\s\/]+/', '', trim($category->name))), ['indoor&outdoor', 'indooroutdoor'], true))
                                <label class="category-checkbox">
                                    <input type="checkbox" class="markup-laminasi-category-checkbox"
                                        value="{{ $category->id }}">
                                    <span class="category-checkbox-box">
                                        <span class="category-checkbox-check">✓</span>
                                        <span class="category-checkbox-label">{{ $category->name }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>

                    </div>

                </div>

            </div>


            <div class="markup-header-body">

                <div class="markup-body-header">

                    <div>

                        <label class="category-label">
                            Aturan Markup
                        </label>

                        <p class="category-description">
                            Tambahkan tipe harga yang berlaku
                            untuk mesin, lokasi dan kategori ini.
                        </p>

                    </div>


                    <button type="button"
                        class="btn btn--ghost btnTambahMarkupLaminasi">

                        <svg viewBox="0 0 24 24">
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>

                        Tambah Markup

                    </button>

                </div>


                <div class="markup-rules-container">

                    <div class="markup-row markup-rule-row"
                        data-rule-index="${index}">


                        <input type="hidden"
                            name="pricing_rules[${index}][engine_id]"
                            class="markup-laminasi-engine-id">


                        <input type="hidden"
                            name="pricing_rules[${index}][location_id]"
                            class="markup-laminasi-location-id">


                        <input type="hidden"
                            name="pricing_rules[${index}][vendor_id]"
                            class="markup-laminasi-vendor-id">


                        <input type="hidden"
                            name="pricing_rules[${index}][category_id]"
                            class="markup-laminasi-category-id">


                        <div class="markup-remove">

                            <i class="bi bi-trash3-fill btn-remove-markup-laminasi"
                                style="
                                cursor: pointer;
                                border: none !important;
                            "></i>

                        </div>


                        <div class="markup-field">

                            <label class="category-label">
                                Tipe Harga
                            </label>

                            <select
                                name="pricing_rules[${index}][price_type]"
                                class="select markup-laminasi-price-type"
                                required>

                                <option value="">
                                    Pilih Tipe Harga
                                </option>

                                <option value="general">
                                    Harga Umum
                                </option>

                                <option value="division">
                                    Harga Divisi
                                </option>

                                <option value="plain">
                                    Harga Polos
                                </option>

                            </select>

                        </div>


                        <div class="markup-field">

                            <label class="category-label">
                                Markup (%)
                            </label>

                            <input type="number"
                                name="pricing_rules[${index}][markup_percentage]"
                                class="input markup-laminasi-percentage"
                                min="0"
                                step="0.01"
                                placeholder="Contoh: 10"
                                required>

                        </div>


                        <div class="markup-field">

                            <label class="category-label">
                                Pembulatan (Rp)
                            </label>

                            <input type="text"
                                name="pricing_rules[${index}][rounding_value]"
                                class="input markup-laminasi-rounding"
                                inputmode="numeric"
                                autocomplete="off"
                                placeholder="Contoh: Rp 1.000"
                                required>

                        </div>


                        <div class="markup-field">

                            <label class="category-label">
                                Status
                            </label>

                            <select
                                name="pricing_rules[${index}][status]"
                                class="select markup-laminasi-status"
                                required>

                                <option value="Active">
                                    Active
                                </option>

                                <option value="Inactive">
                                    Inactive
                                </option>

                            </select>

                        </div>


                    </div>

                </div>

            </div>

        `;


                const firstRuleRow = header.querySelector('.markup-rule-row');

                if (firstRuleRow) {

                    installDigitalLaminationFields(
                        firstRuleRow,
                        firstRuleRow.dataset.ruleIndex
                    );

                }


                headerContainer.appendChild(header);


                const engine = header.querySelector(
                    '.markup-laminasi-engine'
                );

                const location = header.querySelector(
                    '.markup-laminasi-location'
                );

                const vendor = header.querySelector(
                    '.markup-laminasi-vendor'
                );

                const category = header.querySelector(
                    '.markup-laminasi-category'
                );

                const btnAddMarkup = header.querySelector(
                    '.btnTambahMarkupLaminasi'
                );

                const btnRemoveHeader = header.querySelector(
                    '.btn-remove-header-laminasi'
                );


                /* =====================================================
                 * ENGINE
                 * ===================================================== */

                engine.addEventListener(
                    'change',
                    function() {

                        syncHeaderValues(header);

                        clearFieldError(engine);

                    }
                );

                $(engine).on(
                    'change select2:select select2:clear',
                    function() {

                        updateLaminationBody(header);

                    }
                );


                /* =====================================================
                 * LOCATION
                 * ===================================================== */

                $(location).on(
                    'select2:select',
                    function() {

                        syncHeaderValues(header);

                        clearFieldError(location);

                        updateVendorField(header);

                    }
                );


                location.addEventListener(
                    'change',
                    function() {

                        syncHeaderValues(header);

                        clearFieldError(location);

                        updateVendorField(header);

                    }
                );


                /* =====================================================
                 * VENDOR
                 * ===================================================== */

                $(vendor).on(
                    'select2:select',
                    function() {

                        syncHeaderValues(header);

                        clearFieldError(vendor);

                    }
                );


                vendor.addEventListener(
                    'change',
                    function() {

                        syncHeaderValues(header);

                        clearFieldError(vendor);

                    }
                );


                /* =====================================================
                 * CATEGORY
                 * ===================================================== */

                category.addEventListener(
                    'change',
                    function() {

                        syncHeaderValues(header);

                        clearFieldError(category);

                    }
                );

                $(category).on(
                    'change select2:select select2:clear',
                    function() {

                        updateLaminationBody(header);

                    }
                );


                /* =====================================================
                 * ADD RULE
                 * ===================================================== */

                btnAddMarkup.addEventListener(
                    'click',
                    function() {

                        createMarkupRow(
                            header,
                            ruleIndex
                        );

                        ruleIndex++;

                        reindexRuleRows();

                        syncHeaderValues(header);

                    }
                );


                /* =====================================================
                 * REMOVE HEADER
                 * ===================================================== */

                btnRemoveHeader.addEventListener(
                    'click',
                    function() {

                        const headers = getHeaderGroups();


                        if (headers.length <= 1) {

                            if (typeof showToast === 'function') {

                                showToast(
                                    'error',
                                    'Minimal harus ada satu header.'
                                );

                            }

                            return;

                        }


                        destroySelect2(header);

                        header.remove();

                        reindexHeaderGroups();

                        reindexRuleRows();

                        syncAllHeaderValues();

                    }
                );


                /* =====================================================
                 * REMOVE FIRST RULE
                 * ===================================================== */

                const firstRule = header.querySelector(
                    '.markup-rule-row'
                );


                const firstRemoveButton =
                    firstRule.querySelector(
                        '.btn-remove-markup-laminasi'
                    );


                firstRemoveButton.addEventListener(
                    'click',
                    function() {

                        const rows = getRuleRows(header);


                        if (rows.length <= 1) {

                            if (typeof showToast === 'function') {

                                showToast(
                                    'error',
                                    'Minimal harus ada satu markup.'
                                );

                            }

                            return;

                        }


                        destroySelect2(firstRule);

                        firstRule.remove();

                        reindexRuleRows();

                        syncHeaderValues(header);

                        updateRemoveButtons(header);

                    }
                );


                initSelect2(header);

                updateRemoveButtons(header);

                updateVendorField(header);

                syncHeaderValues(header);


                return header;

            }


            /* =========================================================
             * REINDEX HEADER
             * ========================================================= */

            function reindexHeaderGroups() {

                const headers = getHeaderGroups();


                headers.forEach(
                    function(header, index) {

                        header.dataset.headerIndex =
                            index;


                        const btnRemove =
                            header.querySelector(
                                '.btn-remove-header-laminasi'
                            );


                        if (btnRemove) {

                            btnRemove.style.display = '';

                        }

                    }
                );

            }


            /* =========================================================
             * ADD HEADER
             * ========================================================= */

            if (btnTambahHeader) {

                btnTambahHeader.addEventListener(
                    'click',
                    function() {

                        createMarkupHeader(
                            headerIndex
                        );

                        headerIndex++;

                        reindexHeaderGroups();

                        reindexRuleRows();

                        syncAllHeaderValues();

                    }
                );

            }


            /* =========================================================
             * INPUT EVENTS
             * ========================================================= */

            form.addEventListener(
                'input',
                function(e) {

                    const target = e.target;


                    /* MARKUP */

                    if (
                        target.classList.contains(
                            'markup-laminasi-percentage'
                        )
                    ) {

                        target.value =
                            target.value.replace(
                                /[^\d.]/g,
                                ''
                            );

                        clearFieldError(target);

                    }


                    /* ROUNDING DIGITAL */

                    if (
                        target.classList.contains(
                            'digital-lam-tier-rounding'
                        )
                    ) {

                        target.value =
                            formatRupiah(
                                unformatRupiah(
                                    target.value
                                )
                            );

                        clearFieldError(target);

                    }


                    if (
                        target.classList.contains(
                            'digital-lam-tier-percentage'
                        )
                    ) {

                        clearFieldError(target);

                    }


                    /* ROUNDING */

                    if (
                        target.classList.contains(
                            'markup-laminasi-rounding'
                        )
                    ) {

                        const rawValue =
                            unformatRupiah(
                                target.value
                            );


                        target.value =
                            formatRupiah(
                                rawValue
                            );


                        clearFieldError(target);

                    }

                }
            );


            form.addEventListener(
                'change',
                function(e) {

                    const target = e.target;


                    if (target.classList.contains('markup-laminasi-category-checkbox')) {

                        const checkboxHeader = target.closest('.markup-header-group');

                        if (checkboxHeader) {

                            syncHeaderValues(checkboxHeader);

                            const group = checkboxHeader.querySelector('.digital-lam-category-group');

                            if (group) {
                                group.classList.remove('is-invalid');
                            }

                        }

                    }


                    if (
                        target.classList.contains(
                            'markup-laminasi-price-type'
                        ) ||
                        target.classList.contains(
                            'markup-laminasi-status'
                        )
                    ) {

                        clearFieldError(target);

                    }

                }
            );


            /* =========================================================
             * SUBMIT
             * ========================================================= */

            form.addEventListener(
                'submit',
                async function(e) {

                    e.preventDefault();


                    if (!validateForm()) {

                        return;

                    }


                    const digitalModes = Array.from(getHeaderGroups())
                        .map(isDigitalLaminationHeader);

                    if (digitalModes.some(Boolean) && !digitalModes.every(Boolean)) {

                        if (typeof showToast === 'function') {

                            showToast(
                                'error',
                                'Pisahkan konfigurasi Digital Print A3+ dan mesin lain ke submit yang berbeda.'
                            );

                        }

                        return;

                    }

                    const submitUrl = digitalModes.every(Boolean) ?
                        "{{ route('admin_store_digital_lamination_pricing_rule') }}" :
                        "{{ route('admin_store_lamination_pricing_rule') }}";


                    syncAllHeaderValues();

                    reindexRuleRows();

                    form.querySelectorAll(
                        '.markup-laminasi-rounding, .digital-lam-tier-rounding'
                    ).forEach(
                        function(input) {

                           input.value =
                               unformatRupiah(
                                   input.value
                               );

                        }
                    );


                    btnSimpan.disabled = true;

                    const originalText =
                        btnSimpan.innerHTML;

                    btnSimpan.innerHTML =
                        'Menyimpan...';


                    try {

                        const response =
                            await fetch(
                                submitUrl, {
                                    method: 'POST',

                                    headers: {

                                        'Accept': 'application/json',

                                        'X-CSRF-TOKEN': document
                                            .querySelector(
                                                'meta[name="csrf-token"]'
                                            )
                                            ?.getAttribute(
                                                'content'
                                            ) ||
                                            form
                                            .querySelector(
                                                'input[name="_token"]'
                                            )
                                            ?.value

                                    },

                                    body: new FormData(form)

                                }
                            );


                        const data =
                            await response.json();


                        if (
                            !response.ok ||
                            !data.success
                        ) {

                            throw new Error(
                                data.message ||
                                'Gagal menyimpan markup laminasi.'
                            );

                        }


                        if (
                            typeof showToast ===
                            'function'
                        ) {

                            showToast(
                                'success',
                                data.message ||
                                'Markup laminasi berhasil disimpan.'
                            );

                        }


                        closeTambahMarkupLaminasiModal();


                        if (
                            typeof laminationPricingRuleTable !==
                            'undefined' &&
                            laminationPricingRuleTable
                        ) {

                            laminationPricingRuleTable.ajax.reload(
                                null,
                                false
                            );

                        } else if (
                            typeof pricingRuleLaminasiTable !==
                            'undefined' &&
                            pricingRuleLaminasiTable
                        ) {

                            pricingRuleLaminasiTable.ajax.reload(
                                null,
                                false
                            );

                        } else if (
                            typeof pricingRuleTable !==
                            'undefined' &&
                            pricingRuleTable
                        ) {

                            pricingRuleTable.ajax.reload(
                                null,
                                false
                            );

                        } else {

                            location.reload();

                        }


                    } catch (error) {

                        console.error(
                            'Error store markup laminasi:',
                            error
                        );


                        if (
                            typeof showToast ===
                            'function'
                        ) {

                            showToast(
                                'error',
                                error.message ||
                                'Terjadi kesalahan saat menyimpan markup laminasi.'
                            );

                        }


                    } finally {

                        btnSimpan.disabled = false;

                        btnSimpan.innerHTML =
                            originalText;

                    }

                }
            );


            /* =========================================================
             * RESET FORM
             * ========================================================= */

            function resetTambahMarkupLaminasiForm() {

                destroySelect2(
                    headerContainer
                );


                headerContainer.innerHTML = '';


                headerIndex = 1;

                ruleIndex = 0;


                createMarkupHeader(0);


                headerIndex = 1;


                reindexHeaderGroups();

                reindexRuleRows();


                initSelect2(
                    headerContainer
                );


                clearAllValidation();

                syncAllHeaderValues();

            }


            /* =========================================================
             * INITIALIZE
             * ========================================================= */

            resetTambahMarkupLaminasiForm();

        });

        document.addEventListener('DOMContentLoaded', function() {

            const modalEdit =
                document.getElementById('modalEditMarkupLaminasi');

            const formEditMarkup =
                document.getElementById('formEditMarkupLaminasi');

            const editMarkupRows =
                document.getElementById('editMarkupLaminasiRows');

            const editMarkupEngine =
                document.getElementById('editMarkupLaminasiEngine');

            const editMarkupLocation =
                document.getElementById('editMarkupLaminasiLocation');

            const editMarkupVendor =
                document.getElementById('editMarkupLaminasiVendor');

            const editMarkupCategory =
                document.getElementById('editMarkupLaminasiCategory');

            const btnTutupEdit =
                document.getElementById(
                    'btnTutupModalEditMarkupLaminasi'
                );

            const btnBatalEdit =
                document.getElementById(
                    'btnBatalEditMarkupLaminasi'
                );

            const btnTambahEditMarkup =
                document.getElementById(
                    'btnTambahEditMarkupLaminasi'
                );


            if (
                !modalEdit ||
                !formEditMarkup ||
                !editMarkupRows ||
                !editMarkupEngine ||
                !editMarkupLocation ||
                !editMarkupVendor ||
                !editMarkupCategory ||
                !btnTutupEdit ||
                !btnBatalEdit ||
                !btnTambahEditMarkup
            ) {
                return;
            }


            let currentEngineId = null;
            let currentLocationId = null;
            let currentVendorId = null;
            let currentCategoryId = null;

            let newEditMarkupIndex = 0;
            let isSubmittingEdit = false;


            /*
            =========================================================
            FORMAT RUPIAH
            =========================================================
            */

            function formatRupiah(value) {

                const number =
                    String(value).replace(/\D/g, '');

                if (!number) {
                    return '';
                }

                return 'Rp ' + number.replace(
                    /\B(?=(\d{3})+(?!\d))/g,
                    '.'
                );
            }


            function unformatRupiah(value) {

                return String(value).replace(/\D/g, '');

            }


            /*
            =========================================================
            SELECT2
            =========================================================
            */

            function initEditSelect2(container) {

                $(container)
                    .find('.select2')
                    .each(function() {

                        if (
                            !$(this).hasClass(
                                'select2-hidden-accessible'
                            )
                        ) {

                            $(this).select2({
                                width: '100%',
                                dropdownParent: $(modalEdit)
                            });

                        }

                    });

            }


            function destroyEditSelect2(container) {

                $(container)
                    .find('select.select2')
                    .each(function() {

                        if (
                            $(this).hasClass(
                                'select2-hidden-accessible'
                            )
                        ) {

                            $(this).select2('destroy');

                        }

                    });

            }


            /*
            =========================================================
            MODAL
            =========================================================
            */

            function bukaModalEdit() {

                modalEdit.classList.add('is-open');

            }


            function tutupModalEdit() {

                modalEdit.classList.remove('is-open');

            }


            function resetModalEdit() {

                destroyEditSelect2(modalEdit);

                editMarkupRows.innerHTML = '';

                newEditMarkupIndex = 0;

                currentEngineId = null;
                currentLocationId = null;
                currentVendorId = null;
                currentCategoryId = null;

                formEditMarkup.action = '';


                editMarkupEngine.innerHTML = `
            <option value="">
                Memuat mesin...
            </option>
        `;


                editMarkupLocation.innerHTML = `
            <option value="">
                Memuat lokasi...
            </option>
        `;


                editMarkupVendor.innerHTML = `
            <option value="">
                Memuat vendor...
            </option>
        `;


                editMarkupCategory.innerHTML = `
            <option value="">
                Memuat kategori...
            </option>
        `;

            }


            /*
            =========================================================
            CREATE ROW
            =========================================================
            */

            function createEditMarkupRow(rule, index) {

                const row =
                    document.createElement('div');

                row.classList.add('markup-row');


                const priceType =
                    rule && rule.price_type ?
                    rule.price_type :
                    '';


                const markupPercentage =
                    rule &&
                    rule.markup_percentage !== null ?
                    rule.markup_percentage :
                    '';


                const roundingValue =
                    rule &&
                    rule.rounding_value !== null ?
                    parseFloat(rule.rounding_value) :
                    '';


                const status =
                    rule && rule.status ?
                    rule.status :
                    'Active';


                row.innerHTML = `
            <div class="markup-remove">

                <button
                    type="button"
                    class="btn-remove-markup"
                    aria-label="Hapus markup"
                >
                    <i class="bi bi-trash3-fill"></i>
                </button>

            </div>


            <div class="markup-row-top">

                <div class="markup-field">

                    <label class="category-label">
                        Tipe Harga
                    </label>

                    <select
                        name="pricing_rules[${index}][price_type]"
                        class="select select2"
                        required
                    >

                        <option value="">
                            Pilih Tipe Harga
                        </option>

                        <option
                            value="general"
                            ${
                                priceType === 'general'
                                    ? 'selected'
                                    : ''
                            }
                        >
                            Harga Umum
                        </option>

                        <option
                            value="division"
                            ${
                                priceType === 'division'
                                    ? 'selected'
                                    : ''
                            }
                        >
                            Harga Divisi
                        </option>

                        <option
                            value="plain"
                            ${
                                priceType === 'plain'
                                    ? 'selected'
                                    : ''
                            }
                        >
                            Harga Polos
                        </option>

                    </select>

                </div>

            </div>


            <div class="markup-row-bottom">

                <div class="markup-field">

                    <label class="category-label">
                        Markup (%)
                    </label>

                    <div class="input-with-suffix">

                        <input
                            type="number"
                            name="pricing_rules[${index}][markup_percentage]"
                            class="input"
                            value="${markupPercentage}"
                            placeholder="Contoh: 90"
                            min="0"
                            step="0.01"
                            required
                        >

                    </div>

                </div>


                <div class="markup-field">

                    <label class="category-label">
                        Pembulatan (Rp)
                    </label>

                    <input
                        type="text"
                        name="pricing_rules[${index}][rounding_value]"
                        class="input markup-rounding"
                        value="${
                            roundingValue !== ''
                                ? formatRupiah(
                                    Math.round(
                                        roundingValue
                                    ).toString()
                                )
                                : ''
                        }"
                        inputmode="numeric"
                        autocomplete="off"
                        placeholder="Contoh: Rp 1.000"
                    >

                    <small class="category-description">
                        Kosongkan jika tidak menggunakan pembulatan.
                    </small>

                </div>


                <div class="markup-field">

                    <label class="category-label">
                        Status
                    </label>

                    <select
                        name="pricing_rules[${index}][status]"
                        class="select select2"
                        required
                    >

                        <option
                            value="Active"
                            ${
                                status === 'Active'
                                    ? 'selected'
                                    : ''
                            }
                        >
                            Active
                        </option>

                        <option
                            value="Inactive"
                            ${
                                status === 'Inactive'
                                    ? 'selected'
                                    : ''
                            }
                        >
                            Inactive
                        </option>

                    </select>

                </div>

            </div>
        `;


                return row;

            }


            /*
            =========================================================
            RE-INDEX ROW
            =========================================================
            */

            function reindexEditMarkupRows() {

                const rows =
                    editMarkupRows.querySelectorAll(
                        '.markup-row'
                    );


                rows.forEach(function(row, index) {

                    const fields =
                        row.querySelectorAll('[name]');


                    fields.forEach(function(field) {

                        const name =
                            field.getAttribute('name');


                        if (!name) {
                            return;
                        }


                        const newName =
                            name.replace(
                                /^pricing_rules\[[^\]]+\]/,
                                `pricing_rules[${index}]`
                            );


                        field.setAttribute(
                            'name',
                            newName
                        );

                    });

                });

            }


            /*
            =========================================================
            TAMBAH ROW BARU
            =========================================================
            */

            function tambahMarkupEditRow() {

                newEditMarkupIndex++;


                const row =
                    createEditMarkupRow({
                            price_type: '',
                            markup_percentage: '',
                            rounding_value: '',
                            status: 'Active'
                        },
                        `new_${newEditMarkupIndex}`
                    );


                editMarkupRows.appendChild(row);


                initEditSelect2(row);


                reindexEditMarkupRows();

            }


            /*
            =========================================================
            BUKA EDIT
            =========================================================
            */

            document.addEventListener(
                'click',
                function(event) {

                    const editButton =
                        event.target.closest(
                            '.btn-edit-lamination-pricing-rule'
                        );


                    if (!editButton) {
                        return;
                    }


                    const engineId =
                        editButton.dataset.engineId;


                    const locationId =
                        editButton.dataset.locationId;


                    const vendorId =
                        editButton.dataset.vendorId ??
                        'null';


                    const categoryId =
                        editButton.dataset.categoryId;


                    /*
                    =====================================================
                    VALIDASI DATA BUTTON
                    =====================================================
                    */

                    if (
                        !engineId ||
                        !locationId ||
                        !categoryId
                    ) {

                        showToast(
                            'error',
                            'Data mesin, lokasi atau kategori tidak ditemukan.'
                        );

                        return;

                    }


                    /*
                    Vendor boleh bernilai "null"
                    untuk lokasi non-Outsourcing.
                    */

                    if (
                        vendorId === undefined ||
                        vendorId === ''
                    ) {

                        showToast(
                            'error',
                            'Data vendor tidak ditemukan.'
                        );

                        return;

                    }


                    /*
                    =====================================================
                    RESET MODAL
                    =====================================================
                    */

                    resetModalEdit();


                    currentEngineId = engineId;
                    currentLocationId = locationId;
                    currentVendorId = vendorId;
                    currentCategoryId = categoryId;


                    /*
                    =====================================================
                    URL EDIT
                    =====================================================
                    */

                    const editUrl =
                        `{{ url('/admin/lamination-pricing-rules') }}` +
                        `/${engineId}` +
                        `/${locationId}` +
                        `/${vendorId}` +
                        `/${categoryId}` +
                        `/edit`;


                    const updateUrl =
                        `{{ url('/admin/lamination-pricing-rules') }}` +
                        `/${engineId}` +
                        `/${locationId}` +
                        `/${vendorId}` +
                        `/${categoryId}` +
                        `/update`;


                    /*
                    =====================================================
                    FORM ACTION
                    =====================================================
                    */

                    formEditMarkup.action =
                        updateUrl;


                    /*
                    =====================================================
                    AMBIL DATA
                    =====================================================
                    */

                    fetch(editUrl, {

                            method: 'GET',

                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',

                                'Accept': 'application/json'
                            }

                        })

                        .then(async function(response) {

                            let data;


                            try {

                                data =
                                    await response.json();

                            } catch (error) {

                                throw new Error(
                                    'Terjadi kesalahan pada response server.'
                                );

                            }


                            if (
                                !response.ok ||
                                !data.success
                            ) {

                                throw new Error(
                                    data.message ||
                                    'Data markup harga laminasi gagal dimuat.'
                                );

                            }


                            return data;

                        })


                        .then(function(data) {

                            const pricingRules =
                                data.data;


                            if (
                                !pricingRules ||
                                !Array.isArray(pricingRules) ||
                                !pricingRules.length
                            ) {

                                throw new Error(
                                    'Data markup harga laminasi tidak ditemukan.'
                                );

                            }


                            const firstRule =
                                pricingRules[0];


                            /*
                            =================================================
                            ISI MESIN
                            =================================================
                            */

                            editMarkupEngine.innerHTML = `
                        <option value="${firstRule.engine_id}">
                            ${firstRule.engine_name}
                        </option>
                    `;


                            editMarkupEngine.value =
                                firstRule.engine_id;


                            /*
                            =================================================
                            ISI LOKASI
                            =================================================
                            */

                            editMarkupLocation.innerHTML = `
                        <option value="${firstRule.location_id}">
                            ${firstRule.location_name}
                        </option>
                    `;


                            editMarkupLocation.value =
                                firstRule.location_id;


                            /*
                            =================================================
                            ISI VENDOR
                            =================================================
                            */

                            const responseVendorId =
                                firstRule.vendor_id === null ||
                                firstRule.vendor_id === undefined ?
                                'null' :
                                String(firstRule.vendor_id);


                            currentVendorId =
                                responseVendorId;


                            editMarkupVendor.innerHTML = `
                        <option value="${responseVendorId}">
                            ${
                                firstRule.vendor_name ||
                                '-'
                            }
                        </option>
                    `;


                            editMarkupVendor.value =
                                responseVendorId;


                            /*
                            =================================================
                            ISI KATEGORI
                            =================================================
                            */

                            editMarkupCategory.innerHTML = `
                        <option value="${firstRule.category_id}">
                            ${firstRule.category_name}
                        </option>
                    `;


                            editMarkupCategory.value =
                                firstRule.category_id;


                            /*
                            =================================================
                            BERSIHKAN ROW LAMA
                            =================================================
                            */

                            editMarkupRows.innerHTML = '';

                            newEditMarkupIndex = 0;


                            /*
                            =================================================
                            RENDER SEMUA MARKUP
                            =================================================
                            */

                            pricingRules.forEach(
                                function(rule, index) {

                                    const row =
                                        createEditMarkupRow(
                                            rule,
                                            index
                                        );


                                    editMarkupRows.appendChild(
                                        row
                                    );


                                    initEditSelect2(row);

                                }
                            );


                            /*
                            =================================================
                            RE-INDEX
                            =================================================
                            */

                            reindexEditMarkupRows();


                            /*
                            =================================================
                            BUKA MODAL
                            =================================================
                            */

                            bukaModalEdit();

                        })


                        .catch(function(error) {

                            showToast(
                                'error',
                                error.message ||
                                'Terjadi kesalahan saat memuat markup harga laminasi.'
                            );

                        });

                }
            );


            /*
            =========================================================
            TUTUP MODAL
            =========================================================
            */

            btnTutupEdit.addEventListener(
                'click',
                function() {

                    tutupModalEdit();

                }
            );


            btnBatalEdit.addEventListener(
                'click',
                function() {

                    tutupModalEdit();

                }
            );


            modalEdit.addEventListener(
                'click',
                function(event) {

                    if (
                        event.target === modalEdit
                    ) {

                        tutupModalEdit();

                    }

                }
            );


            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modalEdit.classList.contains(
                            'is-open'
                        )
                    ) {

                        tutupModalEdit();

                    }

                }
            );


            /*
            =========================================================
            HAPUS ROW
            =========================================================
            */

            editMarkupRows.addEventListener(
                'click',
                function(event) {

                    const removeButton =
                        event.target.closest(
                            '.btn-remove-markup'
                        );


                    if (!removeButton) {
                        return;
                    }


                    const row =
                        removeButton.closest(
                            '.markup-row'
                        );


                    if (!row) {
                        return;
                    }


                    row.remove();


                    reindexEditMarkupRows();

                }
            );


            /*
            =========================================================
            TAMBAH MARKUP
            =========================================================
            */

            btnTambahEditMarkup.addEventListener(
                'click',
                function() {

                    tambahMarkupEditRow();

                }
            );


            /*
            =========================================================
            FORMAT RUPIAH PEMBULATAN
            =========================================================
            */

            formEditMarkup.addEventListener(
                'input',
                function(event) {

                    if (
                        event.target.matches(
                            '.markup-rounding'
                        )
                    ) {

                        event.target.value =
                            formatRupiah(
                                event.target.value
                            );

                    }

                }
            );


            /*
            =========================================================
            SUBMIT UPDATE
            =========================================================
            */

            formEditMarkup.addEventListener(
                'submit',
                function(event) {

                    event.preventDefault();


                    if (isSubmittingEdit) {
                        return;
                    }


                    /*
                    =====================================================
                    CEK ROW
                    =====================================================
                    */

                    const rows =
                        editMarkupRows.querySelectorAll(
                            '.markup-row'
                        );


                    if (rows.length === 0) {

                        showToast(
                            'error',
                            'Minimal harus ada satu markup.'
                        );

                        return;

                    }


                    /*
                    =====================================================
                    CEK VALIDASI FORM
                    =====================================================
                    */

                    if (!formEditMarkup.checkValidity()) {

                        formEditMarkup.reportValidity();

                        return;

                    }


                    /*
                    =====================================================
                    CEK URL UPDATE
                    =====================================================
                    */

                    if (!formEditMarkup.action) {

                        showToast(
                            'error',
                            'URL update markup tidak ditemukan.'
                        );

                        return;

                    }


                    /*
                    =====================================================
                    BERSIHKAN NILAI RUPIAH
                    =====================================================
                    */

                    editMarkupRows
                        .querySelectorAll(
                            '.markup-rounding'
                        )
                        .forEach(function(input) {

                            input.value =
                                unformatRupiah(
                                    input.value
                                );

                        });


                    /*
                    =====================================================
                    RE-INDEX SEBELUM SUBMIT
                    =====================================================
                    */

                    reindexEditMarkupRows();


                    /*
                    =====================================================
                    BUAT FORMDATA
                    =====================================================
                    */

                    const formData =
                        new FormData(
                            formEditMarkup
                        );


                    /*
                    =====================================================
                    SUBMIT
                    =====================================================
                    */

                    isSubmittingEdit = true;


                    fetch(
                            formEditMarkup.action, {
                                method: 'POST',

                                headers: {

                                    'X-CSRF-TOKEN': document
                                        .querySelector(
                                            'meta[name="csrf-token"]'
                                        )
                                        .getAttribute(
                                            'content'
                                        ),

                                    'X-Requested-With': 'XMLHttpRequest',

                                    'Accept': 'application/json'

                                },

                                body: formData

                            }
                        )


                        .then(async function(response) {

                            let data;


                            try {

                                data =
                                    await response.json();

                            } catch (error) {

                                throw new Error(
                                    'Response server tidak valid.'
                                );

                            }


                            if (!response.ok) {

                                /*
                                Laravel validation error
                                */

                                if (
                                    response.status === 422 &&
                                    data.errors
                                ) {

                                    const messages = [];


                                    Object.keys(
                                        data.errors
                                    ).forEach(function(key) {

                                        data.errors[key]
                                            .forEach(function(message) {

                                                messages.push(
                                                    message
                                                );

                                            });

                                    });


                                    throw new Error(
                                        messages.join('\n')
                                    );

                                }


                                throw new Error(
                                    data.message ||
                                    'Terjadi kesalahan saat memperbarui markup.'
                                );

                            }


                            return data;

                        })


                        .then(function(data) {

                            if (!data.success) {

                                throw new Error(
                                    data.message ||
                                    'Gagal memperbarui markup laminasi.'
                                );

                            }


                            /*
                            =================================================
                            BERHASIL
                            =================================================
                            */

                            showToast(
                                'success',
                                data.message ||
                                'Markup harga laminasi berhasil diperbarui.'
                            );


                            tutupModalEdit();


                            /*
                            =================================================
                            RELOAD DATATABLE
                            =================================================
                            */

                            if (
                                $.fn.DataTable.isDataTable(
                                    '#laminationPricingRuleTable'
                                )
                            ) {

                                $('#laminationPricingRuleTable')
                                    .DataTable()
                                    .ajax.reload(
                                        null,
                                        false
                                    );

                            }

                        })


                        .catch(function(error) {

                            console.error(
                                'Update markup laminasi error:',
                                error
                            );


                            showToast(
                                'error',
                                error.message ||
                                'Terjadi kesalahan saat memperbarui markup laminasi.'
                            );

                        })


                        .finally(function() {

                            isSubmittingEdit = false;

                        });

                }
            );

        });



        document.addEventListener('DOMContentLoaded', function() {

            const modalDetail =
                document.getElementById(
                    'modalDetailMarkupLaminasi'
                );

            const btnTutupDetail =
                document.getElementById(
                    'btnTutupModalDetailMarkupLaminasi'
                );

            const btnBatalDetail =
                document.getElementById(
                    'btnBatalDetailMarkupLaminasi'
                );

            const detailMarkupEngine =
                document.getElementById(
                    'detailMarkupLaminasiEngine'
                );

            const detailMarkupLocation =
                document.getElementById(
                    'detailMarkupLaminasiLocation'
                );

            const detailMarkupVendor =
                document.getElementById(
                    'detailMarkupLaminasiVendor'
                );

            const detailMarkupCategory =
                document.getElementById(
                    'detailMarkupLaminasiCategory'
                );

            const detailMarkupContent =
                document.getElementById(
                    'detailMarkupLaminasiContent'
                );


            if (
                !modalDetail ||
                !btnTutupDetail ||
                !btnBatalDetail ||
                !detailMarkupEngine ||
                !detailMarkupLocation ||
                !detailMarkupVendor ||
                !detailMarkupCategory ||
                !detailMarkupContent
            ) {
                return;
            }


            /* =========================================================
             * BUKA MODAL
             * ========================================================= */

            function bukaModalDetail() {

                modalDetail.classList.add('is-open');

            }


            /* =========================================================
             * TUTUP MODAL
             * ========================================================= */

            function tutupModalDetail() {

                modalDetail.classList.remove('is-open');

            }


            /* =========================================================
             * FORMAT RUPIAH
             * ========================================================= */

            function formatRupiah(value) {

                if (
                    value === null ||
                    value === undefined ||
                    value === ''
                ) {
                    return '-';
                }

                const number = Number(value);

                if (Number.isNaN(number)) {
                    return '-';
                }

                return 'Rp' + number.toLocaleString(
                    'id-ID', {
                        maximumFractionDigits: 2
                    }
                );

            }


            /* =========================================================
             * LABEL TIPE HARGA
             * ========================================================= */

            function getPriceTypeLabel(priceType) {

                const labels = {
                    general: 'Harga Umum',
                    division: 'Harga Divisi',
                    plain: 'Harga Polos'
                };

                return labels[priceType] || priceType;

            }


            /* =========================================================
             * RENDER DETAIL MARKUP
             * ========================================================= */

            function renderDetailMarkup(pricingRules) {

                detailMarkupContent.innerHTML = '';


                if (
                    !pricingRules ||
                    !Array.isArray(pricingRules) ||
                    !pricingRules.length
                ) {

                    detailMarkupContent.innerHTML = `
                <div class="details-empty">
                    Tidak ada data markup harga laminasi.
                </div>
            `;

                    return;

                }


                /* =====================================================
                 * MARKUP ACCORDION
                 * ===================================================== */

                const accordion =
                    document.createElement('div');

                accordion.classList.add(
                    'details-category'
                );

                accordion.setAttribute(
                    'data-markup-accordion',
                    ''
                );


                /* =====================================================
                 * ACCORDION HEADER
                 * ===================================================== */

                const header =
                    document.createElement('button');

                header.type = 'button';

                header.classList.add(
                    'details-category-header'
                );

                header.setAttribute(
                    'aria-expanded',
                    'false'
                );

                header.innerHTML = `
            <span class="details-category-info">

                <span
                    class="details-category-label"
                    style="text-transform: uppercase;"
                >
                    MARKUP HARGA LAMINASI
                </span>

                <span
                    class="details-category-name"
                    style="text-transform: uppercase;"
                >
                    ${pricingRules.length} Aturan
                </span>

            </span>

            <span
                class="details-category-icon"
                aria-hidden="true"
            >
                <i class="bi bi-chevron-down"></i>
            </span>
        `;


                /* =====================================================
                 * ACCORDION CONTENT
                 * ===================================================== */

                const content =
                    document.createElement('div');

                content.classList.add(
                    'details-category-content'
                );


                const inner =
                    document.createElement('div');

                inner.classList.add(
                    'details-category-inner'
                );


                /* =====================================================
                 * TABLE
                 * ===================================================== */

                const tableWrapper =
                    document.createElement('div');

                tableWrapper.classList.add(
                    'details-table-wrapper'
                );


                const table =
                    document.createElement('table');

                table.classList.add(
                    'details-table'
                );


                table.innerHTML = `
            <thead>

                <tr class="details-table-group-header">

                    <th>
                        Tipe Harga
                    </th>

                    <th>
                        Markup
                    </th>

                    <th>
                        Pembulatan (Rp)
                    </th>

                    <th>
                        Status
                    </th>

                </tr>

            </thead>

            <tbody></tbody>
        `;


                const tbody =
                    table.querySelector('tbody');


                /* =====================================================
                 * ROW DATA
                 * ===================================================== */

                pricingRules.forEach(function(rule) {

                    const tr =
                        document.createElement('tr');


                    const statusClass =
                        rule.status === 'Active' ?
                        'details-status-active' :
                        'details-status-inactive';


                    const rounding =
                        rule.rounding_value !== null &&
                        rule.rounding_value !== '' ?
                        formatRupiah(
                            rule.rounding_value
                        ) :
                        '-';


                    tr.innerHTML = `
                <td>

                    <div class="details-material-name">
                        ${getPriceTypeLabel(
                            rule.price_type
                        )}
                    </div>

                </td>

                <td class="details-money">

                    ${rule.markup_percentage ?? '-'}%

                </td>

                <td class="details-money">

                    ${rounding}

                </td>

                <td>

                    <span class="${statusClass}">
                        ${rule.status ?? '-'}
                    </span>

                </td>
            `;


                    tbody.appendChild(tr);

                });


                tableWrapper.appendChild(table);

                inner.appendChild(tableWrapper);

                content.appendChild(inner);

                accordion.appendChild(header);

                accordion.appendChild(content);

                detailMarkupContent.appendChild(
                    accordion
                );


                /* =====================================================
                 * ACCORDION BEHAVIOR
                 * ===================================================== */

                header.addEventListener(
                    'click',
                    function() {

                        const isOpen =
                            accordion.classList.contains(
                                'is-open'
                            );


                        accordion.classList.toggle(
                            'is-open',
                            !isOpen
                        );


                        header.setAttribute(
                            'aria-expanded',
                            String(!isOpen)
                        );

                    }
                );

            }


            function renderDigitalDetailMarkup(pricingRules) {

                detailMarkupContent.innerHTML = '';

                [
                    ['general', 'single'],
                    ['general', 'double'],
                    ['division', 'single'],
                    ['division', 'double']
                ].forEach(function([priceType, printType]) {

                    const groupRules = pricingRules.filter(function(rule) {
                        return rule.price_type === priceType &&
                            rule.print_type === printType;
                    });

                    if (!groupRules.length) {
                        return;
                    }

                    const section = document.createElement('section');

                    section.style.cssText = `
                        border: 1px solid var(--border);
                        border-radius: 10px;
                        overflow: hidden;
                        background: var(--bg-card);
                    `;

                    section.innerHTML = `
                        <div style="
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            gap: 12px;
                            padding: 12px 16px;
                            background: var(--bg-muted);
                            border-bottom: 1px solid var(--border);
                        ">
                            <strong>${getPriceTypeLabel(priceType)}</strong>
                            <span class="badge">${printType === 'single' ? '1 Muka' : '2 Muka'}</span>
                        </div>
                    `;

                    const tableWrapper = document.createElement('div');
                    tableWrapper.classList.add('details-table-wrapper');

                    const table = document.createElement('table');
                    table.classList.add('details-table');

                    table.innerHTML = `
                        <thead>
                            <tr class="details-table-group-header">
                                <th>Jumlah Lembar</th>
                                <th>Markup</th>
                                <th style="text-align: center;">Pembulatan (Rp)</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    `;

                    const tbody = table.querySelector('tbody');

                    groupRules.forEach(function(rule) {

                        const statusClass = rule.status === 'Active' ?
                            'details-status-active' :
                            'details-status-inactive';

                        const maximumQuantity = rule.maximum_quantity === null ?
                            '+' :
                            rule.minimum_quantity === rule.maximum_quantity ?
                            '' :
                            ` - ${rule.maximum_quantity}`;

                        const tr = document.createElement('tr');

                        tr.innerHTML = `
                            <td>${rule.minimum_quantity}${maximumQuantity} lbr</td>
                            <td class="details-money">${rule.markup_percentage ?? '-'}%</td>
                            <td class="details-money" style="text-align: center;">
                                ${formatRupiah(rule.rounding_value)}
                            </td>
                            <td>
                                <span class="${statusClass}">${rule.status ?? '-'}</span>
                            </td>
                        `;

                        tbody.appendChild(tr);
                    });

                    tableWrapper.appendChild(table);
                    section.appendChild(tableWrapper);
                    detailMarkupContent.appendChild(section);
                });
            }


            /* =========================================================
             * TOMBOL DETAIL DATATABLE
             * ========================================================= */

            document.addEventListener(
                'click',
                function(event) {

                    const detailButton =
                        event.target.closest(
                            '.btn-detail-lamination-pricing-rule'
                        );


                    if (!detailButton) {
                        return;
                    }


                    const engineId =
                        detailButton.dataset.engineId;


                    const locationId =
                        detailButton.dataset.locationId;


                    const vendorId =
                        detailButton.dataset.vendorId;


                    const categoryId =
                        detailButton.dataset.categoryId;


                    const pricingRuleSource =
                        detailButton.dataset.pricingRuleSource;

                    const digitalPricingRuleId =
                        detailButton.dataset.digitalPricingRuleId;

                    const isDigitalDetail =
                        pricingRuleSource === 'digital';


                    /* =================================================
                     * VALIDASI DATA
                     * ================================================= */

                    if (isDigitalDetail && !digitalPricingRuleId) {

                        showToast(
                            'error',
                            'Data markup laminasi Digital Print tidak ditemukan.'
                        );

                        return;

                    }

                    if (
                        !isDigitalDetail &&
                        (
                            !engineId ||
                            !locationId ||
                            !vendorId ||
                            !categoryId
                        )
                    ) {

                        showToast(
                            'error',
                            'Data mesin, lokasi, vendor atau kategori tidak ditemukan.'
                        );

                        return;

                    }


                    /* =================================================
                     * SIMPAN ID PADA MODAL
                     * ================================================= */

                    modalDetail.dataset.engineId =
                        engineId;

                    modalDetail.dataset.locationId =
                        locationId;

                    modalDetail.dataset.vendorId =
                        vendorId;

                    modalDetail.dataset.categoryId =
                        categoryId;


                    /* =================================================
                     * RESET ISI MODAL
                     * ================================================= */

                    detailMarkupEngine.textContent =
                        'Memuat...';

                    detailMarkupLocation.textContent =
                        'Memuat...';

                    detailMarkupVendor.textContent =
                        'Memuat...';

                    detailMarkupCategory.textContent =
                        'Memuat...';


                    detailMarkupContent.innerHTML = `
                <div class="details-empty">
                    Memuat data markup harga laminasi...
                </div>
            `;


                    /* =================================================
                     * BUKA MODAL TERLEBIH DAHULU
                     * ================================================= */

                    bukaModalDetail();


                    /* =================================================
                     * URL DETAIL
                     * ================================================= */

                    const detailUrl = isDigitalDetail ?
                        `{{ url('/admin/lamination-pricing-rules/digital') }}/${digitalPricingRuleId}/details` :
                        `{{ url('/admin/lamination-pricing-rules') }}/${engineId}/${locationId}/${vendorId}/${categoryId}/details`;


                    /* =================================================
                     * FETCH DATA
                     * ================================================= */

                    fetch(
                            detailUrl, {
                                method: 'GET',

                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',

                                    'Accept': 'application/json'
                                }
                            }
                        )

                        .then(async function(response) {

                            let data;


                            try {

                                data =
                                    await response.json();

                            } catch (error) {

                                throw new Error(
                                    'Terjadi kesalahan pada response server.'
                                );

                            }


                            if (
                                !response.ok ||
                                !data.success
                            ) {

                                throw new Error(
                                    data.message ||
                                    'Data markup harga laminasi gagal dimuat.'
                                );

                            }


                            return data;

                        })

                        .then(function(data) {

                            const pricingRules =
                                data.data;


                            if (
                                !pricingRules ||
                                !Array.isArray(pricingRules) ||
                                !pricingRules.length
                            ) {

                                throw new Error(
                                    'Data markup harga laminasi tidak ditemukan.'
                                );

                            }


                            const firstRule =
                                pricingRules[0];


                            /* =================================================
                             * ENGINE
                             * ================================================= */

                            detailMarkupEngine.textContent =
                                firstRule.engine_name || '-';


                            /* =================================================
                             * LOCATION
                             * ================================================= */

                            detailMarkupLocation.textContent =
                                firstRule.location_name || '-';


                            /* =================================================
                             * VENDOR
                             * ================================================= */

                            detailMarkupVendor.textContent =
                                firstRule.vendor_name || '-';


                            /* =================================================
                             * CATEGORY
                             * ================================================= */

                            detailMarkupCategory.textContent =
                                firstRule.category_name || '-';


                            /* =================================================
                             * RENDER DETAIL
                             * ================================================= */

                            if (isDigitalDetail) {

                                renderDigitalDetailMarkup(
                                    pricingRules
                                );

                            } else {

                                renderDetailMarkup(
                                    pricingRules
                                );

                            }

                        })

                        .catch(function(error) {

                            detailMarkupEngine.textContent =
                                '-';

                            detailMarkupLocation.textContent =
                                '-';

                            detailMarkupVendor.textContent =
                                '-';

                            detailMarkupCategory.textContent =
                                '-';


                            detailMarkupContent.innerHTML = `
                    <div class="details-empty">
                        Data markup harga laminasi gagal dimuat.
                    </div>
                `;


                            showToast(
                                'error',
                                error.message ||
                                'Terjadi kesalahan saat memuat detail markup laminasi.'
                            );

                        });

                }
            );


            /* =========================================================
             * TUTUP MODAL
             * ========================================================= */

            btnTutupDetail.addEventListener(
                'click',
                function() {

                    tutupModalDetail();

                }
            );


            btnBatalDetail.addEventListener(
                'click',
                function() {

                    tutupModalDetail();

                }
            );


            /* =========================================================
             * KLIK BACKDROP
             * ========================================================= */

            modalDetail.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalDetail) {

                        tutupModalDetail();

                    }

                }
            );


            /* =========================================================
             * ESCAPE
             * ========================================================= */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modalDetail.classList.contains(
                            'is-open'
                        )
                    ) {

                        tutupModalDetail();

                    }

                }
            );

        });

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | DELETE MARKUP HARGA LAMINASI
            |--------------------------------------------------------------------------
            */

            let markupLaminasiEngineIdToDelete = null;
            let markupLaminasiLocationIdToDelete = null;
            let markupLaminasiVendorIdToDelete = null;
            let markupLaminasiCategoryIdToDelete = null;


            const modalDeleteMarkupLaminasi =
                document.getElementById(
                    'modalDeleteMarkupLaminasi'
                );


            const btnTutupDeleteMarkupLaminasi =
                document.getElementById(
                    'btnTutupDeleteMarkupLaminasi'
                );


            const btnBatalDeleteMarkupLaminasi =
                document.getElementById(
                    'btnBatalDeleteMarkupLaminasi'
                );


            const btnConfirmDeleteMarkupLaminasi =
                document.getElementById(
                    'btnConfirmDeleteMarkupLaminasi'
                );


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL DELETE
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                function(event) {

                    const deleteButton =
                        event.target.closest(
                            '.btn-delete-lamination-pricing-rule'
                        );


                    if (!deleteButton) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL ENGINE ID
                    |--------------------------------------------------------------------------
                    */

                    markupLaminasiEngineIdToDelete =
                        deleteButton.dataset.engineId;


                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL LOCATION ID
                    |--------------------------------------------------------------------------
                    */

                    markupLaminasiLocationIdToDelete =
                        deleteButton.dataset.locationId;


                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL VENDOR ID
                    |--------------------------------------------------------------------------
                    |
                    | Vendor "null" merupakan nilai valid untuk lokasi non-Outsourcing.
                    |
                    */

                    markupLaminasiVendorIdToDelete =
                        deleteButton.dataset.vendorId;


                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL CATEGORY ID
                    |--------------------------------------------------------------------------
                    */

                    markupLaminasiCategoryIdToDelete =
                        deleteButton.dataset.categoryId;


                    /*
                    |--------------------------------------------------------------------------
                    | CEK DATA
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !markupLaminasiEngineIdToDelete ||
                        !markupLaminasiLocationIdToDelete ||
                        typeof markupLaminasiVendorIdToDelete ===
                        'undefined' ||
                        !markupLaminasiCategoryIdToDelete
                    ) {

                        showToast(
                            'error',
                            'Data mesin, lokasi, vendor, atau kategori tidak ditemukan.'
                        );

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | BUKA MODAL
                    |--------------------------------------------------------------------------
                    */

                    modalDeleteMarkupLaminasi.classList.add(
                        'is-open'
                    );
                }
            );


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupDeleteMarkupLaminasiModal() {

                modalDeleteMarkupLaminasi.classList.remove(
                    'is-open'
                );


                markupLaminasiEngineIdToDelete = null;

                markupLaminasiLocationIdToDelete = null;

                markupLaminasiVendorIdToDelete = null;

                markupLaminasiCategoryIdToDelete = null;
            }


            btnTutupDeleteMarkupLaminasi.addEventListener(
                'click',
                tutupDeleteMarkupLaminasiModal
            );


            btnBatalDeleteMarkupLaminasi.addEventListener(
                'click',
                tutupDeleteMarkupLaminasiModal
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modalDeleteMarkupLaminasi.addEventListener(
                'click',
                function(event) {

                    if (
                        event.target ===
                        modalDeleteMarkupLaminasi
                    ) {

                        tutupDeleteMarkupLaminasiModal();
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | TEKAN ESC
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modalDeleteMarkupLaminasi.classList.contains(
                            'is-open'
                        )
                    ) {

                        tutupDeleteMarkupLaminasiModal();
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI DELETE
            |--------------------------------------------------------------------------
            */

            btnConfirmDeleteMarkupLaminasi.addEventListener(
                'click',
                async function() {

                    /*
                    |--------------------------------------------------------------------------
                    | CEK ID
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !markupLaminasiEngineIdToDelete ||
                        !markupLaminasiLocationIdToDelete ||
                        typeof markupLaminasiVendorIdToDelete ===
                        'undefined' ||
                        !markupLaminasiCategoryIdToDelete
                    ) {

                        return;
                    }


                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | REQUEST DELETE
                        |--------------------------------------------------------------------------
                        */

                        const response =
                            await fetch(
                                `/admin/lamination-pricing-rules/${markupLaminasiEngineIdToDelete}/${markupLaminasiLocationIdToDelete}/${markupLaminasiVendorIdToDelete}/${markupLaminasiCategoryIdToDelete}/delete`, {
                                    method: 'DELETE',

                                    headers: {
                                        'X-CSRF-TOKEN': document
                                            .querySelector(
                                                'meta[name="csrf-token"]'
                                            )
                                            .getAttribute(
                                                'content'
                                            ),

                                        'Accept': 'application/json'
                                    }
                                }
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | RESPONSE JSON
                        |--------------------------------------------------------------------------
                        */

                        const result =
                            await response.json();


                        /*
                        |--------------------------------------------------------------------------
                        | BERHASIL
                        |--------------------------------------------------------------------------
                        */

                        if (
                            response.ok &&
                            result.success
                        ) {

                            /*
                            |--------------------------------------------------------------------------
                            | TUTUP MODAL
                            |--------------------------------------------------------------------------
                            */

                            tutupDeleteMarkupLaminasiModal();


                            /*
                            |--------------------------------------------------------------------------
                            | RELOAD DATATABLE
                            |--------------------------------------------------------------------------
                            */

                            const tableElement =
                                $('#laminationPricingRuleTable');


                            if (tableElement.length) {

                                const table =
                                    tableElement.DataTable();

                                table.ajax.reload(
                                    null,
                                    false
                                );
                            }


                            /*
                            |--------------------------------------------------------------------------
                            | NOTIFIKASI
                            |--------------------------------------------------------------------------
                            */

                            showToast(
                                'success',
                                result.message
                            );
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | GAGAL
                        |--------------------------------------------------------------------------
                        */
                        else {

                            showToast(
                                'error',
                                result.message ||
                                'Markup harga laminasi gagal dihapus.'
                            );
                        }

                    } catch (error) {

                        console.error(
                            'Delete markup laminasi error:',
                            error
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | ERROR SERVER
                        |--------------------------------------------------------------------------
                        */

                        showToast(
                            'error',
                            'Terjadi kesalahan saat menghapus markup harga laminasi.'
                        );
                    }
                }
            );

        });

        document.addEventListener('DOMContentLoaded', function() {

            const btnImportLaminationPricingRules =
                document.getElementById('btnImportLaminationPricingRules');

            const modalImportLaminationPricingRules =
                document.getElementById('modalImportLaminationPricingRules');

            const btnCloseImportLaminationPricingRules =
                document.getElementById('btnCloseImportLaminationPricingRules');

            const btnCancelImportLaminationPricingRules =
                document.getElementById('btnCancelImportLaminationPricingRules');

            const btnSaveImportLaminationPricingRules =
                document.getElementById('btnSaveImportLaminationPricingRules');

            const fileImportLaminationPricingRules =
                document.getElementById('fileImportLaminationPricingRules');

            const fileSelectedNameLaminationPricingRules =
                document.getElementById('fileSelectedNameLaminationPricingRules');


            /*
             * ==========================================================
             * OPEN MODAL
             * ==========================================================
             */
            btnImportLaminationPricingRules.addEventListener('click', function() {

                modalImportLaminationPricingRules.classList.add('is-open');

            });


            /*
             * ==========================================================
             * CLOSE MODAL
             * ==========================================================
             */
            function closeImportLaminationPricingRulesModal() {

                modalImportLaminationPricingRules.classList.remove('is-open');

                fileImportLaminationPricingRules.value = '';

                fileSelectedNameLaminationPricingRules.textContent =
                    'Belum ada file dipilih';

                fileSelectedNameLaminationPricingRules.classList.remove(
                    'has-file'
                );

            }


            btnCloseImportLaminationPricingRules.addEventListener(
                'click',
                closeImportLaminationPricingRulesModal
            );


            btnCancelImportLaminationPricingRules.addEventListener(
                'click',
                closeImportLaminationPricingRulesModal
            );


            /*
             * ==========================================================
             * CLOSE KETIKA KLIK BACKDROP
             * ==========================================================
             */
            modalImportLaminationPricingRules.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalImportLaminationPricingRules) {

                        closeImportLaminationPricingRulesModal();

                    }

                }
            );


            /*
             * ==========================================================
             * CLOSE DENGAN ESCAPE
             * ==========================================================
             */
            document.addEventListener('keydown', function(event) {

                if (
                    event.key === 'Escape' &&
                    modalImportLaminationPricingRules.classList.contains('is-open')
                ) {

                    closeImportLaminationPricingRulesModal();

                }

            });


            /*
             * ==========================================================
             * FILE CHANGE
             * ==========================================================
             */
            fileImportLaminationPricingRules.addEventListener(
                'change',
                function() {

                    if (this.files.length > 0) {

                        fileSelectedNameLaminationPricingRules.textContent =
                            this.files[0].name;

                        fileSelectedNameLaminationPricingRules.classList.add(
                            'has-file'
                        );

                    } else {

                        fileSelectedNameLaminationPricingRules.textContent =
                            'Belum ada file dipilih';

                        fileSelectedNameLaminationPricingRules.classList.remove(
                            'has-file'
                        );

                    }

                }
            );


            /*
             * ==========================================================
             * IMPORT DATA
             * ==========================================================
             */
            btnSaveImportLaminationPricingRules.addEventListener(
                'click',
                async function() {

                    /*
                     * Pastikan file sudah dipilih
                     */
                    if (fileImportLaminationPricingRules.files.length === 0) {

                        showToast(
                            'error',
                            'Silakan pilih file Excel terlebih dahulu.'
                        );

                        return;

                    }


                    /*
                     * Simpan HTML tombol
                     */
                    const originalButtonHTML =
                        btnSaveImportLaminationPricingRules.innerHTML;


                    /*
                     * Loading state
                     */
                    btnSaveImportLaminationPricingRules.disabled = true;

                    btnSaveImportLaminationPricingRules.innerHTML = `
                <span>Memproses...</span>
            `;


                    try {

                        const formData = new FormData();

                        formData.append(
                            'file',
                            fileImportLaminationPricingRules.files[0]
                        );

                        formData.append(
                            'pricing_engine',
                            $('#laminationPricingRuleEngineFilter').val() ||
                            'large_format'
                        );


                        /*
                         * CSRF Token
                         */
                        const csrfMeta =
                            document.querySelector(
                                'meta[name="csrf-token"]'
                            );

                        if (!csrfMeta) {

                            throw new Error(
                                'CSRF token tidak ditemukan.'
                            );

                        }

                        const csrfToken =
                            csrfMeta.getAttribute('content');


                        /*
                         * Kirim file ke controller
                         */
                        const response = await fetch(
                            "{{ route('admin_import_lamination_pricing_rules') }}", {
                                method: 'POST',

                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },

                                body: formData
                            }
                        );


                        /*
                         * Ambil response JSON
                         */
                        const data = await response.json();


                        /*
                         * Jika response gagal
                         */
                        if (!response.ok || !data.success) {

                            throw new Error(
                                data.message ||
                                'Import data markup harga laminasi gagal.'
                            );

                        }


                        /*
                         * Success toast
                         */
                        showToast(
                            'success',
                            data.message
                        );


                        /*
                         * Reset file
                         */
                        fileImportLaminationPricingRules.value = '';

                        fileSelectedNameLaminationPricingRules.textContent =
                            'Belum ada file dipilih';

                        fileSelectedNameLaminationPricingRules.classList.remove(
                            'has-file'
                        );


                        /*
                         * Tutup modal
                         */
                        closeImportLaminationPricingRulesModal();


                        /*
                         * Reload DataTable
                         */
                        if (
                            typeof laminationPricingRuleTable !== 'undefined' &&
                            laminationPricingRuleTable
                        ) {

                            laminationPricingRuleTable.ajax.reload(
                                null,
                                false
                            );

                        } else {

                            location.reload();

                        }

                    } catch (error) {

                        console.error(
                            'Import Lamination Pricing Rules Error:',
                            error
                        );


                        /*
                         * Error toast
                         */
                        showToast(
                            'error',
                            error.message ||
                            'Terjadi kesalahan saat import data markup harga laminasi.'
                        );

                    } finally {

                        /*
                         * Kembalikan tombol
                         */
                        btnSaveImportLaminationPricingRules.disabled = false;

                        btnSaveImportLaminationPricingRules.innerHTML =
                            originalButtonHTML;

                    }

                }
            );

        });

        document.addEventListener('DOMContentLoaded', function() {

            const tierLabels = [
                ['tier_1', '1 lbr'],
                ['tier_2_5', '2 - 5 lbr'],
                ['tier_6_50', '6 - 50 lbr'],
                ['tier_100', '51 - 100 lbr'],
                ['tier_300', '101 - 300 lbr'],
                ['tier_500', '301 - 500 lbr']
            ];

            const ruleGroups = [
                ['general', 'single'],
                ['general', 'double'],
                ['division', 'single'],
                ['division', 'double']
            ];

            const priceTypeLabel = (type) =>
                type === 'general' ? 'Harga Umum' : 'Harga Divisi';

            const printTypeLabel = (type) =>
                type === 'single' ? '1 Muka' : '2 Muka';

            const escapeHtml = (value) =>
                String(value ?? '').replace(/[&<>"']/g, (char) => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                } [char]));

            function formatRp(value) {
                const raw = String(value ?? '').trim();

                if (raw === '') {
                    return '';
                }

                const number = /^rp/i.test(raw) ?
                    Number(raw.replace(/^rp\s*/i, '').replace(/\./g, '').replace(',', '.')) :
                    (/^\d+(\.\d{1,2})?$/.test(raw) ?
                        Number(raw) :
                        Number(raw.replace(/\./g, '').replace(',', '.')));

                return Number.isFinite(number) ?
                    'Rp ' + number.toLocaleString('id-ID', {
                        maximumFractionDigits: 2
                    }) :
                    '';
            }

            function unformatRp(value) {
                const raw = String(value ?? '').replace(/^rp\s*/i, '').trim();

                if (!raw) {
                    return '';
                }

                if (raw.includes(',')) {
                    return raw.replace(/\./g, '').replace(',', '.');
                }

                return /^\d+\.\d{1,2}$/.test(raw) ? raw : raw.replace(/\./g, '');
            }

            document.body.insertAdjacentHTML('beforeend', `
                <div class="modal-overlay" id="modalEditDigitalLamination">
                    <div class="modal-dialog modal-dialog--wide">
                        <form id="formEditDigitalLamination" novalidate>
                            <div class="modal-header">
                                <div>
                                    <span class="eyebrow">MARKUP HARGA · LAMINASI</span>
                                    <br>
                                    <span class="eyebrow">Edit Markup Laminasi Digital Print A3+</span>
                                </div>
                                <button type="button" class="modal-close" data-close-digital-lam-edit
                                    aria-label="Tutup">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M18 6 6 18" />
                                        <path d="m6 6 12 12" />
                                    </svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="markup-row" style="margin-bottom: 20px;">
                                    <div class="markup-row-top" id="digitalLamEditHeader"></div>
                                </div>
                                <div id="digitalLamEditRules"
                                    style="display: flex; flex-direction: column; gap: 20px;"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn--ghost" data-close-digital-lam-edit>
                                    Batal
                                </button>
                                <button type="submit" class="btn btn--primary">
                                    <svg viewBox="0 0 24 24">
                                        <path d="M5 12l4 4L19 6" />
                                    </svg>
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            `);

            const modal = document.getElementById('modalEditDigitalLamination');
            const form = document.getElementById('formEditDigitalLamination');
            const header = document.getElementById('digitalLamEditHeader');
            const rulesContainer = document.getElementById('digitalLamEditRules');

            function closeModal() {
                modal.classList.remove('is-open');
                form.reset();
                form.action = '';
                header.innerHTML = '';
                rulesContainer.innerHTML = '';
            }

            function toast(type, message) {
                if (typeof showToast === 'function') {
                    showToast(type, message);
                }
            }

            function renderHeader(rule) {
                header.innerHTML = [
                    ['Mesin', rule.engine_name],
                    ['Lokasi', rule.location_name],
                    ['Vendor', rule.vendor_name || '-'],
                    ['Kategori', rule.category_name]
                ].map(([label, value]) => `
                    <div class="markup-field">
                        <label class="category-label">${label}</label>
                        <div class="input">${escapeHtml(value || '-')}</div>
                    </div>
                `).join('');
            }

            function renderRuleGroup(index, priceType, printType, rules) {
                const groupRules = rules.filter((rule) =>
                    rule.price_type === priceType && rule.print_type === printType
                );

                if (!groupRules.length) {
                    return '';
                }

                const byTier = groupRules.reduce((result, rule) => {
                    result[rule.quantity_tier] = rule;
                    return result;
                }, {});

                const first = groupRules[0];

                return `
                    <section style="border: 1px solid var(--border); border-radius: 10px; overflow: hidden; background: var(--bg-card);">
                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 12px 16px; background: var(--bg-muted); border-bottom: 1px solid var(--border);">
                            <strong>${priceTypeLabel(priceType)}</strong>
                            <span class="badge">${printTypeLabel(printType)}</span>
                        </div>
                        <div style="padding: 16px;">
                            <input type="hidden" name="pricing_rules[${index}][detail_id]" value="${first.detail_id}">
                            <input type="hidden" name="pricing_rules[${index}][price_type]" value="${priceType}">
                            <input type="hidden" name="pricing_rules[${index}][print_type]" value="${printType}">
                            <div class="markup-field" style="max-width: 220px; margin-bottom: 16px;">
                                <label class="category-label">Status</label>
                                <select name="pricing_rules[${index}][status]" class="select" required>
                                    <option value="Active" ${first.status === 'Active' ? 'selected' : ''}>Active</option>
                                    <option value="Inactive" ${first.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                                </select>
                            </div>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 12px;">
                                ${tierLabels.map(([tier, label]) => {
                                    const tierRule = byTier[tier];
                                    return `
                                        <div style="min-width: 0; padding: 12px; border: 1px solid var(--border); border-radius: 10px;">
                                            <label class="category-label">${label}</label>
                                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-top: 10px;">
                                                <div class="markup-field">
                                                    <label class="category-label">Markup (%)</label>
                                                    <input type="number"
                                                        name="pricing_rules[${index}][${tier}_markup_percentage]"
                                                        class="input"
                                                        value="${tierRule ? tierRule.markup_percentage : ''}"
                                                        min="0" max="999999.99" step="0.01" required>
                                                </div>
                                                <div class="markup-field">
                                                    <label class="category-label">Pembulatan (Rp)</label>
                                                    <input type="text"
                                                        name="pricing_rules[${index}][${tier}_rounding_value]"
                                                        class="input digital-lam-edit-rounding"
                                                        value="${tierRule && tierRule.rounding_value !== null ? formatRp(tierRule.rounding_value) : ''}"
                                                        inputmode="numeric" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                }).join('')}
                            </div>
                        </div>
                    </section>
                `;
            }

            document.addEventListener('click', function(event) {
                const editButton =
                    event.target.closest('.btn-edit-digital-lamination-pricing-rule');

                if (!editButton) {
                    return;
                }

                const ruleId = editButton.dataset.digitalPricingRuleId;

                if (!ruleId) {
                    toast('error', 'Data markup laminasi Digital Print tidak ditemukan.');
                    return;
                }

                header.innerHTML = '<div class="input">Memuat data...</div>';
                rulesContainer.innerHTML = '';
                modal.classList.add('is-open');

                fetch(`{{ url('/admin/lamination-pricing-rules/digital') }}/${ruleId}/details`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(async (response) => {
                        const data = await response.json();

                        if (!response.ok || !data.success || !data.data.length) {
                            throw new Error(
                                data.message ||
                                'Data markup laminasi Digital Print gagal dimuat.'
                            );
                        }

                        return data.data;
                    })
                    .then((rules) => {
                        renderHeader(rules[0]);

                        rulesContainer.innerHTML = ruleGroups.map(
                            ([priceType, printType], index) =>
                            renderRuleGroup(index, priceType, printType, rules)
                        ).join('');

                        form.action =
                            `{{ url('/admin/lamination-pricing-rules/digital') }}/${ruleId}`;
                    })
                    .catch((error) => {
                        closeModal();
                        toast(
                            'error',
                            error.message ||
                            'Terjadi kesalahan saat memuat markup laminasi Digital Print.'
                        );
                    });
            });

            form.addEventListener('submit', async function(event) {
                event.preventDefault();

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const submitButton = form.querySelector('button[type="submit"]');
                const originalHtml = submitButton.innerHTML;

                submitButton.disabled = true;
                submitButton.innerHTML = 'Menyimpan...';

                try {
                    const formData = new FormData(form);

                    form.querySelectorAll('.digital-lam-edit-rounding').forEach((input) => {
                        formData.set(input.name, unformatRp(input.value));
                    });

                    formData.append('_method', 'PUT');

                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector(
                                'meta[name="csrf-token"]'
                            ).getAttribute('content')
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!response.ok || !data.success) {
                        const firstError = data.errors ?
                            Object.values(data.errors)[0][0] :
                            null;

                        throw new Error(
                            firstError ||
                            data.message ||
                            'Markup laminasi Digital Print gagal diperbarui.'
                        );
                    }

                    closeModal();
                    toast('success', data.message);

                    if (typeof laminationPricingRuleTable !== 'undefined' && laminationPricingRuleTable) {
                        laminationPricingRuleTable.ajax.reload(null, false);
                    }
                } catch (error) {
                    toast(
                        'error',
                        error.message ||
                        'Terjadi kesalahan saat memperbarui markup laminasi Digital Print.'
                    );
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalHtml;
                }
            });

            form.addEventListener('input', function(event) {
                if (event.target.matches('.digital-lam-edit-rounding')) {
                    event.target.value = formatRp(event.target.value);
                }
            });

            modal.addEventListener('click', function(event) {
                if (
                    event.target === modal ||
                    event.target.closest('[data-close-digital-lam-edit]')
                ) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                    closeModal();
                }
            });

            /* DELETE Digital: memakai modal delete laminasi yang sama */

            const deleteModal = document.getElementById('modalDeleteMarkupLaminasi');
            const btnConfirmDelete = document.getElementById('btnConfirmDeleteMarkupLaminasi');
            let digitalLamIdToDelete = null;

            function closeDigitalDelete() {
                digitalLamIdToDelete = null;
                deleteModal.classList.remove('is-open');
            }

            document.addEventListener('click', function(event) {
                const deleteButton =
                    event.target.closest('.btn-delete-digital-lamination-pricing-rule');

                if (!deleteButton) {
                    return;
                }

                digitalLamIdToDelete = deleteButton.dataset.digitalPricingRuleId || null;

                if (!digitalLamIdToDelete) {
                    toast('error', 'Data markup laminasi Digital Print tidak ditemukan.');
                    return;
                }

                deleteModal.classList.add('is-open');
            });

            ['btnTutupDeleteMarkupLaminasi', 'btnBatalDeleteMarkupLaminasi'].forEach(function(id) {
                document.getElementById(id).addEventListener('click', function() {
                    digitalLamIdToDelete = null;
                });
            });

            deleteModal.addEventListener('click', function(event) {
                if (event.target === deleteModal) {
                    digitalLamIdToDelete = null;
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    digitalLamIdToDelete = null;
                }
            });

            btnConfirmDelete.addEventListener('click', async function() {
                if (!digitalLamIdToDelete) {
                    return;
                }

                const id = digitalLamIdToDelete;

                try {
                    const response = await fetch(
                        `{{ url('/admin/lamination-pricing-rules/digital') }}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        }
                    );

                    const result = await response.json();

                    if (response.ok && result.success) {
                        closeDigitalDelete();

                        if (typeof laminationPricingRuleTable !== 'undefined' && laminationPricingRuleTable) {
                            laminationPricingRuleTable.ajax.reload(null, false);
                        }

                        toast('success', result.message);
                    } else {
                        toast(
                            'error',
                            result.message ||
                            'Markup laminasi Digital Print gagal dihapus.'
                        );
                    }
                } catch (error) {
                    console.error(error);
                    toast(
                        'error',
                        'Terjadi kesalahan saat menghapus markup laminasi Digital Print.'
                    );
                }
            });

        });
    </script>
@endsection

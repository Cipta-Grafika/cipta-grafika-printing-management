@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">HARGA MARKUP · MATERIAL</span>
                        {{-- <h1 class="hero-title">Daftar Material</h1> --}}
                        <p class="hero-sub">Kelola dan pantau seluruh data material yang digunakan dalam proses produksi dan
                            perhitungan estimasi harga.</p>
                    </div>
                    <div class="hero-actions material-hero-actions"><a id="btnExportPricingRules"
                            href="{{ route('admin_export_pricing_rules') }}" class="btn btn--ghost"><svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg> Export</a>
                        <div class="material-category-filter">
                            <select id="pricingRuleEngineFilter" class="select select2"
                                aria-label="Filter mesin pricing rule">
                                <option value="large_format" selected>Large Format</option>
                                <option value="digital_print_a3">Digital Print A3+</option>
                            </select>
                        </div>
                        <button type="button" class="btn btn--ghost" id="btnDownloadTemplatePricingRules">

                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Download Template

                        </button>
                        <button class="btn btn--ghost" id="btnImportPricingRules"><svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg> Import</button>

                        <button type="button" class="btn btn--primary" id="btnTambahMarkup">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Markup
                        </button>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="pricingRuleTable" class="data-table">

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

    <!-- Modal Download Template Pricing Rules -->
    <div class="modal-overlay" id="modalDownloadTemplatePricingRules">

        <div class="modal modal-import">

            <div class="modal-header">

                <div>
                    <span class="eyebrow">HARGA MARKUP · MATERIAL</span>
                    <br>
                    <span class="eyebrow">Download Template Excel</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseDownloadTemplatePricingRules"
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
                        Pilih template Excel sesuai dengan jenis mesin pricing
                        rules yang ingin diinput.
                    </p>
                </div>

                <div class="template-download-options">

                    <a href="{{ asset('templates/template_harga_markup.xlsx') }}"
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
                                Template pricing rules untuk mesin Large
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

                    <a href="{{ asset('templates/template_harga_markup_a3.xlsx') }}"
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
                                Template pricing rules untuk mesin Digital
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

                <button type="button" class="btn btn--ghost" id="btnCancelDownloadTemplatePricingRules">
                    Batal
                </button>

            </div>

        </div>

    </div>

    <!-- Modal Import Pricing Rules -->
    <div class="modal-overlay" id="modalImportPricingRules">

        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">

                <div>
                    <span class="eyebrow">HARGA MARKUP · MATERIAL</span>
                    <br>
                    <span class="eyebrow">Import Markup Harga</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseImportPricingRules" aria-label="Tutup">
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
                        Pilih file Excel yang berisi data markup harga untuk
                        diimport ke dalam sistem.
                    </p>
                </div>


                <!-- Custom File Input -->
                <label for="fileImportPricingRules" class="file-upload-box" id="fileUploadBoxPricingRules">

                    <input type="file" id="fileImportPricingRules" name="file" accept=".xlsx,.xls" hidden>


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


                    <div class="file-selected-name" id="fileSelectedNamePricingRules">
                        Belum ada file dipilih
                    </div>

                </label>

            </div>


            <!-- Footer -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCancelImportPricingRules">
                    Batal
                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportPricingRules">

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

    <!-- MODAL DELETE PRICING RULE -->
    <div class="modal-overlay" id="modalDeletePricingRule">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MARKUP HARGA · MATERIAL
                    </span>

                    <br>

                    <span class="eyebrow">
                        Hapus Markup Harga
                    </span>

                </div>

                <button type="button" class="modal-close" id="btnTutupDeletePricingRule" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M18 6L6 18" />
                        <path d="M6 6L18 18" />
                    </svg>
                </button>

            </div>


            <!-- BODY -->
            <div class="modal-body">

                <div class="delete-confirmation">

                    <div class="delete-icon">
                        <i class="bi bi-trash3-fill"></i>
                    </div>

                    <div class="delete-content">

                        <h3>
                            Hapus markup harga?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus seluruh
                            konfigurasi markup harga untuk lokasi dan
                            kategori ini? Data yang sudah dihapus tidak
                            dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeletePricingRule">
                    Batal
                </button>

                <button type="button" class="btn btn--danger" id="btnConfirmDeletePricingRule">
                    <i class="bi bi-trash3-fill"></i>
                    Hapus
                </button>

            </div>

        </div>

    </div>

    <!-- MODAL VIEW MATERIAL -->
    <div class="modal-overlay" id="modalDetailMarkup">

        <div class="modal-dialog modal-dialog--wide">

            <div class="modal-header">

                <div>
                    <span class="eyebrow">
                        MARKUP HARGA · MATERIAL
                    </span>

                    <br>

                    <span class="eyebrow">
                        Detail Markup Harga
                    </span>
                </div>

                <button type="button" class="modal-close" id="btnTutupModalDetailMarkup" aria-label="Tutup">
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
                            Detail Markup Harga
                        </label>

                        <p class="category-description">
                            Informasi aturan markup harga berdasarkan mesin, lokasi dan kategori.
                        </p>

                    </div>

                </div>


                {{-- =====================================================
            ENGINE, LOCATION & CATEGORY
        ====================================================== --}}

                <div class="markup-row" style="margin-bottom: 20px;">

                    <div class="markup-row-top">

                        <div class="markup-field">

                            <label class="category-label">
                                Mesin
                            </label>

                            <div class="input" id="detailMarkupEngine">
                                -
                            </div>

                        </div>


                        <div class="markup-field">

                            <label class="category-label">
                                Lokasi
                            </label>

                            <div class="input" id="detailMarkupLocation">
                                -
                            </div>

                        </div>

                        <div class="markup-field">

                            <label class="category-label">
                                Vendor
                            </label>

                            <div class="input" id="detailMarkupVendor">
                                -
                            </div>

                        </div>

                        <div class="markup-field">

                            <label class="category-label">
                                Kategori
                            </label>

                            <div class="input" id="detailMarkupCategory">
                                -
                            </div>

                        </div>

                    </div>

                </div>


                {{-- =====================================================
            DETAIL CONTENT
        ====================================================== --}}

                <div id="detailMarkupContent"
                    style="
                margin-top: 20px;
                display: flex;
                flex-direction: column;
                gap: 20px;
            ">

                    {{-- Konten detail akan kita isi pada tahap berikutnya --}}

                </div>

            </div>


            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDetailMarkup">
                    Tutup
                </button>

            </div>

        </div>

    </div>



    <!-- Modal Tambah Markup Harga -->

    <div class="modal-overlay" id="modalTambahMarkup">

        <div class="modal-dialog modal-dialog--wide">

            <form method="POST" action="{{ route('admin_store_pricing_rule') }}" id="formTambahMarkup" novalidate>
                @csrf

                <!-- HEADER MODAL -->

                <div class="modal-header">

                    <div>
                        <span class="eyebrow">
                            HARGA MARKUP · MATERIAL
                        </span>
                        <br>
                        <span class="eyebrow">
                            Tambah Markup Harga
                        </span>
                    </div>

                    <button type="button" class="modal-close" id="btnTutupModalTambahMarkup" aria-label="Tutup">
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
                                Konfigurasi Markup Harga
                            </label>

                            <p class="category-description">
                                Tambahkan satu atau beberapa konfigurasi
                                mesin, lokasi dan kategori. Setiap konfigurasi
                                dapat memiliki beberapa tipe harga.
                            </p>
                        </div>

                        <button type="button" class="btn btn--ghost" id="btnTambahHeaderMarkup">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14" />
                                <path d="M5 12h14" />
                            </svg>

                            Tambah Header
                        </button>

                    </div>


                    <!-- CONTAINER HEADER MARKUP -->

                    <div id="markupHeaderContainer">

                        <!-- =====================================================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                 HEADER MARKUP #0
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            ====================================================== -->

                        <div class="markup-header-group" data-header-index="0">

                            <!-- HEADER MESIN + LOKASI + KATEGORI -->

                            <div class="markup-row markup-header-row">

                                <div style="display: flex; justify-content: right; align-items: center; gap: 10px;">

                                    <!-- Hapus Header -->

                                    <div class="markup-header-action" style="display: none;">
                                        <i class="bi bi-trash3-fill btn-remove-header"
                                            style="cursor: pointer; border: none !important;"></i>
                                    </div>

                                </div>


                                <div class="markup-row-top">

                                    <!-- Mesin -->

                                    <div class="markup-field">

                                        <label class="category-label">
                                            Mesin
                                        </label>

                                        <select class="select markup-engine" required>
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

                                        <select class="select markup-location" required>
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


                                    <!-- Kategori -->

                                    <div class="markup-field">

                                        <label class="category-label">
                                            Kategori
                                        </label>

                                        <select class="select markup-category" required>
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

                                    <button type="button" class="btn btn--ghost btnTambahMarkup">
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
                                            class="markup-engine-id">

                                        <input type="hidden" name="pricing_rules[0][location_id]"
                                            class="markup-location-id">

                                        <input type="hidden" name="pricing_rules[0][category_id]"
                                            class="markup-category-id">


                                        <!-- Tombol hapus -->

                                        <div class="markup-remove">

                                            <i class="bi bi-trash3-fill btn-remove-markup"
                                                style="cursor: pointer; border: none !important;"></i>

                                        </div>


                                        <!-- Tipe Harga -->

                                        <div class="markup-field">

                                            <label class="category-label">
                                                Tipe Harga
                                            </label>

                                            <select name="pricing_rules[0][price_type]" class="select markup-price-type"
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


                                        <!-- Markup -->

                                        <div class="markup-field">

                                            <label class="category-label">
                                                Markup (%)
                                            </label>

                                            <input type="number" name="pricing_rules[0][markup_percentage]"
                                                class="input markup-percentage" min="0" step="0.01"
                                                placeholder="Contoh: 10" required>

                                        </div>


                                        <!-- Pembulatan -->

                                        <div class="markup-field">
                                            <label class="category-label">
                                                Pembulatan (Rp)
                                            </label>
                                            <input type="text" name="pricing_rules[0][rounding_value]"
                                                class="input markup-rounding" inputmode="numeric" autocomplete="off"
                                                placeholder="Contoh: Rp 1.000" required>
                                        </div>


                                        <!-- Status -->

                                        <div class="markup-field">

                                            <label class="category-label">
                                                Status
                                            </label>

                                            <select name="pricing_rules[0][status]" class="select markup-status" required>
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

                    <button type="button" class="btn btn--ghost" id="btnBatalTambahMarkup">
                        Batal
                    </button>

                    <button type="submit" class="btn btn--primary" id="btnSimpanMarkup">
                        Simpan Markup
                    </button>

                </div>

            </form>

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- Modal Edit Markup Harga --}}
    {{-- ========================================================= --}}

    <div class="modal-overlay" id="modalEditMarkup">

        <div class="modal-dialog modal-dialog--wide">

            <form method="POST" id="formEditMarkup" novalidate>

                @csrf
                @method('PUT')

                {{-- ================================================= --}}
                {{-- MODAL HEADER --}}
                {{-- ================================================= --}}

                <div class="modal-header">

                    <div>

                        <span class="eyebrow">
                            MARKUP HARGA · MATERIAL
                        </span>

                        <br>

                        <span class="eyebrow">
                            Edit Markup Harga
                        </span>

                    </div>

                    <button type="button" class="modal-close" id="btnTutupModalEditMarkup" aria-label="Tutup">
                        <svg viewBox="0 0 24 24">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>

                </div>


                {{-- ================================================= --}}
                {{-- MODAL BODY --}}
                {{-- ================================================= --}}

                <div class="modal-body">

                    {{-- ================================================= --}}
                    {{-- GROUP INFORMATION --}}
                    {{-- ================================================= --}}

                    <div class="category-form-header">

                        <div>

                            <label class="category-label">
                                Data Markup Harga
                            </label>

                            <p class="category-description">
                                Edit aturan markup harga berdasarkan mesin, lokasi dan kategori.
                            </p>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- MESIN + LOKASI + KATEGORI --}}
                    {{-- ================================================= --}}

                    <div class="markup-row" style="margin-bottom: 20px;">

                        <div class="markup-row-top">

                            {{-- MESIN --}}

                            <div class="markup-field">

                                <label class="category-label">
                                    Mesin
                                </label>

                                <select id="editMarkupEngine" class="select select2" disabled>
                                    <option value="">
                                        Memuat mesin...
                                    </option>
                                </select>

                            </div>


                            {{-- LOKASI --}}

                            <div class="markup-field">

                                <label class="category-label">
                                    Lokasi
                                </label>

                                <select id="editMarkupLocation" class="select select2" disabled>
                                    <option value="">
                                        Memuat lokasi...
                                    </option>
                                </select>

                            </div>

                            {{-- VENDOR --}}
                            <div class="markup-field">

                                <label class="category-label">
                                    Vendor
                                </label>

                                <select id="editMarkupVendor" class="select select2" disabled>
                                    <option value="">
                                        Memuat vendor...
                                    </option>
                                </select>

                            </div>

                            {{-- KATEGORI --}}

                            <div class="markup-field">

                                <label class="category-label">
                                    Kategori
                                </label>

                                <select id="editMarkupCategory" class="select select2" disabled>
                                    <option value="">
                                        Memuat kategori...
                                    </option>
                                </select>

                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- TAMBAH MARKUP --}}
                    {{-- ================================================= --}}

                    <div
                        style="margin-top: 20px; margin-bottom: 20px; display: flex; justify-content: right; align-items: center; gap: 20px;">

                        <button type="button" class="btn btn--ghost" id="btnTambahEditMarkup">
                            <i class="bi bi-plus-lg"></i>
                            Tambah Markup
                        </button>

                    </div>


                    {{-- ================================================= --}}
                    {{-- MARKUP ROWS --}}
                    {{-- ================================================= --}}

                    <div id="editMarkupRows" class="mt-3" style="display: flex; flex-direction: column; gap: 20px;">

                        {{-- Row markup akan diisi oleh JavaScript --}}

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- MODAL FOOTER --}}
                {{-- ================================================= --}}

                <div class="modal-footer">

                    <button type="button" class="btn btn--ghost" id="btnBatalEditMarkup">
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
@endsection

@section('scripts')
    <script>
        let pricingRuleTable;

        document.addEventListener('DOMContentLoaded', function() {

            $('#pricingRuleEngineFilter').select2({
                width: '100%'
            });

            const btnDownloadTemplatePricingRules =
                document.getElementById(
                    'btnDownloadTemplatePricingRules'
                );

            const modalDownloadTemplatePricingRules =
                document.getElementById(
                    'modalDownloadTemplatePricingRules'
                );

            const btnCloseDownloadTemplatePricingRules =
                document.getElementById(
                    'btnCloseDownloadTemplatePricingRules'
                );

            const btnCancelDownloadTemplatePricingRules =
                document.getElementById(
                    'btnCancelDownloadTemplatePricingRules'
                );

            function closeDownloadTemplatePricingRulesModal() {
                modalDownloadTemplatePricingRules.classList.remove(
                    'is-open'
                );
            }

            btnDownloadTemplatePricingRules.addEventListener(
                'click',
                function() {
                    modalDownloadTemplatePricingRules.classList.add(
                        'is-open'
                    );
                }
            );

            btnCloseDownloadTemplatePricingRules.addEventListener(
                'click',
                closeDownloadTemplatePricingRulesModal
            );

            btnCancelDownloadTemplatePricingRules.addEventListener(
                'click',
                closeDownloadTemplatePricingRulesModal
            );

            modalDownloadTemplatePricingRules.addEventListener(
                'click',
                function(event) {
                    if (event.target === modalDownloadTemplatePricingRules) {
                        closeDownloadTemplatePricingRulesModal();
                    }
                }
            );

            document.addEventListener('keydown', function(event) {
                if (
                    event.key === 'Escape' &&
                    modalDownloadTemplatePricingRules.classList.contains(
                        'is-open'
                    )
                ) {
                    closeDownloadTemplatePricingRulesModal();
                }
            });

            pricingRuleTable = new DataTable('#pricingRuleTable', {

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

                    url: "{{ route('admin_data_pricing_rule') }}",

                    type: "GET",

                    data: function(data) {
                        data.pricing_engine =
                            document.getElementById(
                                'pricingRuleEngineFilter'
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
                                pricingRuleTable.page.info();

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
                        render: function(data, type, row) {

                            /*
                            |--------------------------------------------------------------------------
                            | Vendor hanya ada untuk Outsourcing
                            |--------------------------------------------------------------------------
                            */

                            if (
                                !data ||
                                data === null ||
                                data === undefined
                            ) {
                                return '-';
                            }

                            return data;
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
                        createdCell: function(td, cellData, rowData, row, col) {
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
                                    class="btn--icon btn-detail-pricing-rule"
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
                                        class="btn--icon btn-edit-digital-pricing-rule"
                                        data-digital-pricing-rule-id="${row.digital_pricing_rule_id}"
                                        aria-label="Edit"
                                    >
                                        <i class="bi bi-pen"></i>
                                    </button>

                                    <button
                                        type="button"
                                        class="btn--icon btn-delete-digital-pricing-rule"
                                        data-digital-pricing-rule-id="${row.digital_pricing_rule_id}"
                                        aria-label="Hapus"
                                    >
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                    ` : `
                                    <button
                                        type="button"
                                        class="btn--icon btn-edit-pricing-rule"
                                        data-engine-id="${row.engine_id}"
                                        data-location-id="${row.location_id}"
                                        data-vendor-id="${row.vendor_id ?? 'null'}"
                                        data-category-id="${row.category_id}"
                                        aria-label="Edit"
                                    >
                                        <i class="bi bi-pen"></i>
                                    </button>


                                    <button
                                        type="button"
                                        class="btn--icon btn-delete-pricing-rule"
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

                columnDefs: [

                    {

                        targets: 0,

                        className: 'dtr-control'

                    }

                ],


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

                    zeroRecords: 'Data markup harga tidak ditemukan',

                    processing: 'Memuat data...',

                    searchPlaceholder: 'Cari lokasi atau kategori...'

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

            $('#pricingRuleEngineFilter').on('change', function() {
                pricingRuleTable.ajax.reload(null, true);
            });

            $('#btnExportPricingRules').on('click', function(event) {
                event.preventDefault();

                const pricingEngine =
                    $('#pricingRuleEngineFilter').val() || 'large_format';

                const exportUrl =
                    `{{ route('admin_export_pricing_rules') }}?pricing_engine=${encodeURIComponent(pricingEngine)}`;

                window.location.href = exportUrl;
            });

        });

        document.addEventListener('DOMContentLoaded', function() {

            const modalTambahMarkup =
                document.getElementById('modalTambahMarkup');

            const formTambahMarkup =
                document.getElementById('formTambahMarkup');

            const btnTutupModalTambahMarkup =
                document.getElementById('btnTutupModalTambahMarkup');

            const btnBatalTambahMarkup =
                document.getElementById('btnBatalTambahMarkup');

            const btnTambahHeaderMarkup =
                document.getElementById('btnTambahHeaderMarkup');

            const markupHeaderContainer =
                document.getElementById('markupHeaderContainer');

            const btnSimpanMarkup =
                document.getElementById('btnSimpanMarkup');

            let headerIndex = 1;
            let ruleIndex = 1;

            function formatRupiah(value) {
                const number = String(value).replace(/\D/g, '');

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
            |--------------------------------------------------------------------------
            | SELECT2
            |--------------------------------------------------------------------------
            */

            function initSelect2(container) {

                if (
                    typeof $ === 'undefined' ||
                    typeof $.fn.select2 === 'undefined'
                ) {
                    return;
                }

                $(container)
                    .find('select.select')
                    .each(function() {

                        const $select = $(this);

                        if (
                            $select.hasClass(
                                'select2-hidden-accessible'
                            )
                        ) {
                            return;
                        }

                        $select.select2({
                            width: '100%',
                            dropdownParent: $('#modalTambahMarkup')
                        });
                    });
            }


            function destroySelect2(container) {

                if (
                    typeof $ === 'undefined' ||
                    typeof $.fn.select2 === 'undefined'
                ) {
                    return;
                }

                $(container)
                    .find('select.select')
                    .each(function() {

                        const $select = $(this);

                        if (
                            $select.hasClass(
                                'select2-hidden-accessible'
                            )
                        ) {
                            $select.select2('destroy');
                        }
                    });
            }


            /*
            |--------------------------------------------------------------------------
            | MODAL
            |--------------------------------------------------------------------------
            */

            function openTambahMarkupModal() {

                modalTambahMarkup.classList.add('is-open');

                initSelect2(modalTambahMarkup);
            }


            function closeTambahMarkupModal() {

                modalTambahMarkup.classList.remove('is-open');

                resetTambahMarkupForm();
            }


            const btnBukaModalTambahMarkup =
                document.getElementById('btnTambahMarkupHarga') ||
                document.getElementById('btnTambahMarkup');

            if (btnBukaModalTambahMarkup) {

                btnBukaModalTambahMarkup.addEventListener(
                    'click',
                    function() {
                        openTambahMarkupModal();
                    }
                );
            }


            btnTutupModalTambahMarkup.addEventListener(
                'click',
                closeTambahMarkupModal
            );


            btnBatalTambahMarkup.addEventListener(
                'click',
                closeTambahMarkupModal
            );


            modalTambahMarkup.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalTambahMarkup) {
                        closeTambahMarkupModal();
                    }
                }
            );


            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modalTambahMarkup.classList.contains('is-open')
                    ) {
                        closeTambahMarkupModal();
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | GET HEADER & ROW
            |--------------------------------------------------------------------------
            */

            function getHeaderGroups() {

                return markupHeaderContainer.querySelectorAll(
                    '.markup-header-group'
                );
            }


            function getRuleRows(header) {

                return header.querySelectorAll(
                    '.markup-rule-row'
                );
            }

            function isDigitalPricingHeader(header) {

                const engine = header.querySelector('.markup-engine');
                const category = header.querySelector('.markup-category');

                const engineName = engine && engine.selectedIndex >= 0 ?
                    engine.options[engine.selectedIndex].textContent
                    .trim()
                    .toLowerCase()
                    .replace(/\s+/g, '') :
                    '';

                const categoryName = category && category.selectedIndex >= 0 ?
                    category.options[category.selectedIndex].textContent
                    .trim()
                    .toLowerCase()
                    .replace(/[\s/]+/g, '') :
                    '';

                return engineName === 'digitalprinta3+' &&
                    category.value !== '' &&
                    categoryName !== 'indoor&outdoor' &&
                    categoryName !== 'indooroutdoor';
            }

            function createDigitalPricingFields(index) {
                const tiers = [
                    ['1 lbr', 'tier_1'],
                    ['2 - 5 lbr', 'tier_2_5'],
                    ['6 - 50 lbr', 'tier_6_50'],
                    ['100 lbr', 'tier_100'],
                    ['300 lbr', 'tier_300'],
                    ['500 lbr', 'tier_500']
                ];

                return `
                    <div class="markup-digital-fields" style="display: none;">
                        <div class="markup-field digital-print-type-field">
                            <label class="category-label">Harga Print</label>
                            <select
                                name="pricing_rules[${index}][print_type]"
                                class="select markup-print-type"
                                required
                                disabled
                            >
                                <option value="">Pilih Harga Print</option>
                                <option value="single">1 Muka</option>
                                <option value="double">2 Muka</option>
                            </select>
                        </div>
                        <div class="digital-pricing-tiers">
                            ${tiers.map(([label, suffix]) => `
                                        <div class="digital-price-tier">
                                            <label class="category-label">${label}</label>
                                            <div class="markup-field">
                                                <label class="category-label">Markup (%)</label>
                                                <input
                                                    type="number"
                                                    name="pricing_rules[${index}][${suffix}_markup_percentage]"
                                                    data-pricing-suffix="${suffix}_markup_percentage"
                                                    class="input digital-tier-percentage"
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
                                                    class="input markup-rounding digital-tier-rounding"
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

            function installDigitalPricingFields(row, index) {
                const statusField = row.querySelector('.markup-status')
                    .closest('.markup-field');
                const digitalFields = document.createElement('div');

                digitalFields.innerHTML = createDigitalPricingFields(index);
                row.insertBefore(digitalFields.firstElementChild, statusField);
                statusField.classList.add('markup-status-field');
            }

            function updateMarkupBody(header) {
                const isDigitalPricing = isDigitalPricingHeader(header);

                getRuleRows(header).forEach(function(row) {
                    row.classList.toggle('is-digital-pricing', isDigitalPricing);

                    const markup = row.querySelector('.markup-percentage');
                    const rounding = row.querySelector('.markup-rounding:not(.digital-tier-rounding)');
                    const digitalFields = row.querySelector('.markup-digital-fields');
                    const printType = row.querySelector('.markup-print-type');
                    const plainPriceOption = row.querySelector(
                        '.markup-price-type option[value="plain"]'
                    );

                    if (markup) {
                        markup.closest('.markup-field').style.display =
                            isDigitalPricing ? 'none' : '';
                        markup.disabled = isDigitalPricing;
                    }

                    if (rounding) {
                        rounding.closest('.markup-field').style.display =
                            isDigitalPricing ? 'none' : '';
                        rounding.disabled = isDigitalPricing;
                    }

                    if (digitalFields) {
                        digitalFields.style.display =
                            isDigitalPricing ? '' : 'none';
                    }

                    if (printType) {
                        printType.disabled = !isDigitalPricing;
                    }

                    row.querySelectorAll('.digital-tier-percentage, .digital-tier-rounding')
                        .forEach(function(input) {
                            input.disabled = !isDigitalPricing;
                        });

                    if (plainPriceOption) {
                        plainPriceOption.disabled = isDigitalPricing;
                        plainPriceOption.hidden = isDigitalPricing;

                        if (isDigitalPricing && row.querySelector('.markup-price-type').value === 'plain') {
                            row.querySelector('.markup-price-type').value = '';

                            if (typeof $ !== 'undefined') {
                                $(row.querySelector('.markup-price-type'))
                                    .trigger('change.select2');
                            }
                        }
                    }
                });
            }


            /*
            |--------------------------------------------------------------------------
            | SYNC LOCATION & CATEGORY
            |--------------------------------------------------------------------------
            */

            function syncHeaderValues(header) {

                const engineSelect =
                    header.querySelector(
                        '.markup-engine'
                    );

                const locationSelect =
                    header.querySelector(
                        '.markup-location'
                    );

                const categorySelect =
                    header.querySelector(
                        '.markup-category'
                    );

                const vendorSelect =
                    header.querySelector(
                        '.markup-vendor'
                    );

                const engineId =
                    engineSelect ?
                    engineSelect.value :
                    '';

                const locationId =
                    locationSelect ?
                    locationSelect.value :
                    '';

                const categoryId =
                    categorySelect ?
                    categorySelect.value :
                    '';

                const vendorId =
                    vendorSelect ?
                    vendorSelect.value :
                    '';

                getRuleRows(header).forEach(function(row) {

                    const engineInput =
                        row.querySelector(
                            '.markup-engine-id'
                        );

                    const locationInput =
                        row.querySelector(
                            '.markup-location-id'
                        );

                    const categoryInput =
                        row.querySelector(
                            '.markup-category-id'
                        );

                    const vendorInput =
                        row.querySelector(
                            '.markup-vendor-id'
                        );

                    if (engineInput) {
                        engineInput.value = engineId;
                    }

                    if (locationInput) {
                        locationInput.value = locationId;
                    }

                    if (categoryInput) {
                        categoryInput.value = categoryId;
                    }

                    if (vendorInput) {
                        vendorInput.value = vendorId;
                    }

                });
            }


            function syncAllHeaderValues() {

                getHeaderGroups().forEach(
                    function(header) {
                        syncHeaderValues(header);
                    }
                );
            }


            /*
            |--------------------------------------------------------------------------
            | REINDEX MARKUP ROW
            |--------------------------------------------------------------------------
            |
            | Contoh:
            |
            | pricing_rules[0]
            | pricing_rules[1]
            | pricing_rules[2]
            |
            | Hapus index 0
            |
            | menjadi:
            |
            | pricing_rules[0]
            | pricing_rules[1]
            |
            |--------------------------------------------------------------------------
            */

            function reindexRuleRows() {

                let newIndex = 0;

                getHeaderGroups().forEach(
                    function(header) {

                        getRuleRows(header).forEach(
                            function(row) {

                                row.dataset.ruleIndex =
                                    newIndex;

                                const engineInput =
                                    row.querySelector(
                                        '.markup-engine-id'
                                    );

                                const locationInput =
                                    row.querySelector(
                                        '.markup-location-id'
                                    );

                                const categoryInput =
                                    row.querySelector(
                                        '.markup-category-id'
                                    );

                                const vendorInput =
                                    row.querySelector(
                                        '.markup-vendor-id'
                                    );

                                const printType =
                                    row.querySelector(
                                        '.markup-print-type'
                                    );

                                const priceType =
                                    row.querySelector(
                                        '.markup-price-type'
                                    );

                                const markup =
                                    row.querySelector(
                                        '.markup-percentage'
                                    );

                                const rounding =
                                    row.querySelector(
                                        '.markup-rounding'
                                    );

                                const status =
                                    row.querySelector(
                                        '.markup-status'
                                    );

                                if (engineInput) {
                                    engineInput.name =
                                        `pricing_rules[${newIndex}][engine_id]`;
                                }

                                if (locationInput) {

                                    locationInput.name =
                                        `pricing_rules[${newIndex}][location_id]`;
                                }


                                if (categoryInput) {

                                    categoryInput.name =
                                        `pricing_rules[${newIndex}][category_id]`;
                                }

                                if (vendorInput) {

                                    vendorInput.name =
                                        `pricing_rules[${newIndex}][vendor_id]`;
                                }


                                if (priceType) {

                                    priceType.name =
                                        `pricing_rules[${newIndex}][price_type]`;
                                }

                                if (printType) {
                                    printType.name =
                                        `pricing_rules[${newIndex}][print_type]`;
                                }

                                row.querySelectorAll('[data-pricing-suffix]')
                                    .forEach(function(input) {
                                        input.name =
                                            `pricing_rules[${newIndex}][${input.dataset.pricingSuffix}]`;
                                    });


                                if (markup) {

                                    markup.name =
                                        `pricing_rules[${newIndex}][markup_percentage]`;
                                }


                                if (rounding) {

                                    rounding.name =
                                        `pricing_rules[${newIndex}][rounding_value]`;
                                }


                                if (status) {

                                    status.name =
                                        `pricing_rules[${newIndex}][status]`;
                                }


                                newIndex++;
                            }
                        );
                    }
                );


                ruleIndex = newIndex;
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDATION ERROR
            |--------------------------------------------------------------------------
            */

            function clearFieldError(field) {

                if (!field) {
                    return;
                }

                field.classList.remove(
                    'is-invalid'
                );

                const wrapper =
                    field.closest('.markup-field');

                if (wrapper) {

                    wrapper.classList.remove(
                        'has-error'
                    );
                }

                if (
                    typeof $ !== 'undefined' &&
                    $(field).hasClass(
                        'select2-hidden-accessible'
                    )
                ) {

                    $(field)
                        .next('.select2')
                        .find('.select2-selection')
                        .removeClass('is-invalid');
                }
            }


            function setFieldError(field) {

                if (!field) {
                    return;
                }

                field.classList.add(
                    'is-invalid'
                );

                const wrapper =
                    field.closest('.markup-field');

                if (wrapper) {

                    wrapper.classList.add(
                        'has-error'
                    );
                }

                if (
                    typeof $ !== 'undefined' &&
                    $(field).hasClass(
                        'select2-hidden-accessible'
                    )
                ) {

                    $(field)
                        .next('.select2')
                        .find('.select2-selection')
                        .addClass('is-invalid');
                }
            }


            function clearAllValidation() {

                formTambahMarkup
                    .querySelectorAll(
                        '.is-invalid, .has-error'
                    )
                    .forEach(function(element) {

                        element.classList.remove(
                            'is-invalid'
                        );

                        element.classList.remove(
                            'has-error'
                        );
                    });


                if (
                    typeof $ !== 'undefined' &&
                    typeof $.fn.select2 !== 'undefined'
                ) {

                    $(formTambahMarkup)
                        .find('.select2-selection')
                        .removeClass('is-invalid');
                }
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDASI DUPLIKASI
            |--------------------------------------------------------------------------
            |
            | Validasi dilakukan GLOBAL untuk seluruh header.
            |
            | Yang dianggap duplikat hanya:
            |
            | location_id + category_id + price_type
            |
            | Jadi:
            |
            | Karawang + Digital Print + General
            | Karawang + Digital Print + Division
            |
            | => BOLEH
            |
            | Karawang + Digital Print + General
            | Karawang + Large Format + General
            |
            | => BOLEH
            |
            | Karawang + Digital Print + General
            | Karawang + Digital Print + General
            |
            | => TIDAK BOLEH
            |
            |--------------------------------------------------------------------------
            */

            function validateDuplicatePriceTypes() {

                const headers =
                    getHeaderGroups();

                const usedCombinations = {};


                for (
                    let i = 0; i < headers.length; i++
                ) {

                    const header =
                        headers[i];

                    const engine =
                        header.querySelector(
                            '.markup-engine'
                        );

                    const engineId =
                        engine ?
                        engine.value :
                        '';

                    const location =
                        header.querySelector(
                            '.markup-location'
                        );

                    const category =
                        header.querySelector(
                            '.markup-category'
                        );

                    const vendor =
                        header.querySelector(
                            '.markup-vendor'
                        );

                    const locationId =
                        location ?
                        location.value :
                        '';

                    const categoryId =
                        category ?
                        category.value :
                        '';

                    const vendorId =
                        vendor ?
                        vendor.value :
                        '';

                    const rows =
                        getRuleRows(header);


                    for (
                        let j = 0; j < rows.length; j++
                    ) {

                        const row =
                            rows[j];

                        const priceType =
                            row.querySelector(
                                '.markup-price-type'
                            );

                        if (
                            !priceType ||
                            !priceType.value
                        ) {
                            continue;
                        }


                        const isDigitalPricing =
                            isDigitalPricingHeader(header);

                        const printType =
                            row.querySelector('.markup-print-type');

                        const combination =
                            engineId +
                            '|' +
                            locationId +
                            '|' +
                            vendorId +
                            '|' +
                            categoryId +
                            '|' +
                            priceType.value +
                            (isDigitalPricing ? '|' + (printType ? printType.value : '') : '');


                        if (usedCombinations[combination]) {
                            setFieldError(priceType);


                            showToast(
                                'error',
                                isDigitalPricing ?
                                'Kombinasi mesin, lokasi, kategori, tipe harga, dan harga print tidak boleh sama.' :
                                'Kombinasi mesin, lokasi, kategori, dan tipe harga tidak boleh sama.'
                            );


                            if (priceType) {
                                priceType.focus();
                            }


                            return false;
                        }


                        usedCombinations[
                            combination
                        ] = true;
                    }
                }


                return true;
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDASI FORM
            |--------------------------------------------------------------------------
            */

            function validateForm() {

                clearAllValidation();

                syncAllHeaderValues();

                const headers =
                    getHeaderGroups();


                if (headers.length === 0) {

                    showToast(
                        'error',
                        'Minimal harus ada satu konfigurasi lokasi dan kategori.'
                    );

                    return false;
                }


                for (
                    let i = 0; i < headers.length; i++
                ) {

                    const header =
                        headers[i];

                    /*
                    |--------------------------------------------------------------------------
                    | MESIN
                    |--------------------------------------------------------------------------
                    */

                    const engine =
                        header.querySelector(
                            '.markup-engine'
                        );

                    if (
                        !engine ||
                        !engine.value
                    ) {

                        setFieldError(
                            engine
                        );

                        showToast(
                            'error',
                            'Mesin wajib dipilih.'
                        );

                        if (engine) {
                            engine.focus();
                        }

                        return false;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | LOKASI
                    |--------------------------------------------------------------------------
                    */

                    const location =
                        header.querySelector(
                            '.markup-location'
                        );


                    if (
                        !location ||
                        !location.value
                    ) {

                        setFieldError(
                            location
                        );

                        showToast(
                            'error',
                            'Lokasi wajib dipilih.'
                        );

                        if (location) {
                            location.focus();
                        }

                        return false;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | VENDOR
                    |--------------------------------------------------------------------------
                    */

                    const vendor =
                        header.querySelector(
                            '.markup-vendor'
                        );

                    const locationOption =
                        location &&
                        location.selectedIndex >= 0 ?
                        location.options[location.selectedIndex] :
                        null;

                    const locationName =
                        locationOption ?
                        locationOption.textContent.trim().toLowerCase() :
                        '';

                    if (
                        locationName === 'outsourcing'
                    ) {

                        if (
                            !vendor ||
                            !vendor.value
                        ) {

                            setFieldError(
                                vendor
                            );

                            showToast(
                                'error',
                                'Vendor wajib dipilih untuk lokasi Outsourcing.'
                            );

                            if (vendor) {
                                vendor.focus();
                            }

                            return false;
                        }

                    } else {

                        if (vendor) {
                            vendor.value = '';
                        }
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | KATEGORI
                    |--------------------------------------------------------------------------
                    */

                    const category =
                        header.querySelector(
                            '.markup-category'
                        );


                    if (
                        !category ||
                        !category.value
                    ) {

                        setFieldError(
                            category
                        );

                        showToast(
                            'error',
                            'Kategori wajib dipilih.'
                        );

                        if (category) {
                            category.focus();
                        }

                        return false;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | MINIMAL SATU MARKUP
                    |--------------------------------------------------------------------------
                    */

                    const rows =
                        getRuleRows(header);


                    if (rows.length === 0) {

                        showToast(
                            'error',
                            'Setiap header harus memiliki minimal satu markup.'
                        );

                        return false;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDASI SETIAP MARKUP
                    |--------------------------------------------------------------------------
                    */

                    for (
                        let j = 0; j < rows.length; j++
                    ) {

                        const row =
                            rows[j];


                        const priceType =
                            row.querySelector(
                                '.markup-price-type'
                            );


                        const status =
                            row.querySelector(
                                '.markup-status'
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | TIPE HARGA
                        |--------------------------------------------------------------------------
                        */

                        if (
                            !priceType ||
                            !priceType.value
                        ) {

                            setFieldError(
                                priceType
                            );

                            showToast(
                                'error',
                                'Tipe harga wajib dipilih.'
                            );

                            if (priceType) {
                                priceType.focus();
                            }

                            return false;
                        }


                        if (row.classList.contains('is-digital-pricing')) {
                            const printType =
                                row.querySelector('.markup-print-type');

                            if (!printType || !printType.value) {
                                setFieldError(printType);
                                showToast('error', 'Harga Print wajib dipilih.');
                                if (printType) {
                                    printType.focus();
                                }
                                return false;
                            }

                            const digitalMarkups =
                                row.querySelectorAll('.digital-tier-percentage');

                            for (const digitalMarkup of digitalMarkups) {
                                if (
                                    digitalMarkup.value === '' ||
                                    isNaN(digitalMarkup.value)
                                ) {
                                    setFieldError(digitalMarkup);
                                    showToast(
                                        'error',
                                        'Markup wajib diisi dengan angka untuk setiap jumlah lembar.'
                                    );
                                    digitalMarkup.focus();
                                    return false;
                                }

                                if (Number(digitalMarkup.value) < 0) {
                                    setFieldError(digitalMarkup);
                                    showToast(
                                        'error',
                                        'Markup tidak boleh bernilai negatif.'
                                    );
                                    digitalMarkup.focus();
                                    return false;
                                }
                            }

                            const digitalRoundings =
                                row.querySelectorAll('.digital-tier-rounding');

                            for (const digitalRounding of digitalRoundings) {
                                if (
                                    Number(unformatRupiah(digitalRounding.value)) < 0
                                ) {
                                    setFieldError(digitalRounding);
                                    showToast(
                                        'error',
                                        'Pembulatan tidak boleh bernilai negatif.'
                                    );
                                    digitalRounding.focus();
                                    return false;
                                }
                            }
                        } else {
                            const markup =
                                row.querySelector('.markup-percentage');
                            const rounding =
                                row.querySelector('.markup-rounding:not(.digital-tier-rounding)');

                            if (
                                !markup ||
                                markup.value === '' ||
                                isNaN(markup.value)
                            ) {
                                setFieldError(markup);
                                showToast(
                                    'error',
                                    'Markup wajib diisi dengan angka.'
                                );
                                if (markup) {
                                    markup.focus();
                                }
                                return false;
                            }

                            if (Number(markup.value) < 0) {
                                setFieldError(markup);
                                showToast(
                                    'error',
                                    'Markup tidak boleh bernilai negatif.'
                                );
                                markup.focus();
                                return false;
                            }

                            if (
                                Number(unformatRupiah(rounding.value)) < 0
                            ) {
                                setFieldError(rounding);
                                showToast(
                                    'error',
                                    'Pembulatan tidak boleh bernilai negatif.'
                                );
                                rounding.focus();
                                return false;
                            }
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | STATUS
                        |--------------------------------------------------------------------------
                        */

                        if (
                            !status ||
                            !status.value
                        ) {

                            setFieldError(
                                status
                            );

                            showToast(
                                'error',
                                'Status wajib dipilih.'
                            );

                            if (status) {
                                status.focus();
                            }

                            return false;
                        }
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | CEK DUPLIKASI GLOBAL
                |--------------------------------------------------------------------------
                */

                if (
                    !validateDuplicatePriceTypes()
                ) {
                    return false;
                }


                return true;
            }


            /*
            |--------------------------------------------------------------------------
            | CREATE MARKUP ROW
            |--------------------------------------------------------------------------
            */

            function createMarkupRow(
                header,
                index
            ) {

                const container =
                    header.querySelector(
                        '.markup-rules-container'
                    );


                const row =
                    document.createElement('div');


                row.className =
                    'markup-row markup-rule-row';


                row.dataset.ruleIndex =
                    index;


                row.innerHTML = `
                    <input
                        type="hidden"
                        name="pricing_rules[${index}][engine_id]"
                        class="markup-engine-id"
                    >

                    <input
                        type="hidden"
                        name="pricing_rules[${index}][location_id]"
                        class="markup-location-id"
                    >

                    <input
                        type="hidden"
                        name="pricing_rules[${index}][category_id]"
                        class="markup-category-id"
                    >

                    <input
                        type="hidden"
                        name="pricing_rules[${index}][vendor_id]"
                        class="markup-vendor-id"
                    >

                    <div class="markup-remove">
                        <i
                            class="bi bi-trash3-fill btn-remove-markup"
                            style="cursor: pointer; border: none !important;"
                        ></i>
                    </div>

                    <div class="markup-field">
                        <label class="category-label">
                            Tipe Harga
                        </label>

                        <select
                            name="pricing_rules[${index}][price_type]"
                            class="select markup-price-type"
                            required
                        >
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

                        <input
                            type="number"
                            name="pricing_rules[${index}][markup_percentage]"
                            class="input markup-percentage"
                            min="0"
                            step="0.01"
                            placeholder="Contoh: 10"
                            required
                        >
                    </div>

                    <div class="markup-field">
                        <label class="category-label">
                            Pembulatan (Rp)
                        </label>

                        <input
                            type="text"
                            name="pricing_rules[${index}][rounding_value]"
                            class="input markup-rounding"
                            inputmode="numeric"
                            autocomplete="off"
                            placeholder="Contoh: Rp 1.000"
                            required
                        >
                    </div>

                    <div class="markup-field">
                        <label class="category-label">
                            Status
                        </label>

                        <select
                            name="pricing_rules[${index}][status]"
                            class="select markup-status"
                            required
                        >
                            <option value="Active">
                                Active
                            </option>

                            <option value="Inactive">
                                Inactive
                            </option>
                        </select>
                    </div>
                `;

                installDigitalPricingFields(row, index);

                container.appendChild(row);


                initSelect2(row);
                updateMarkupBody(header);

                syncHeaderValues(header);


                /*
                |--------------------------------------------------------------------------
                | HAPUS MARKUP
                |--------------------------------------------------------------------------
                */

                const btnRemove =
                    row.querySelector(
                        '.btn-remove-markup'
                    );


                btnRemove.addEventListener(
                    'click',
                    function() {

                        const rows =
                            getRuleRows(header);


                        /*
                        |--------------------------------------------------------------------------
                        | JIKA MASIH > 1
                        |--------------------------------------------------------------------------
                        |
                        | Row manapun boleh dihapus,
                        | termasuk row pertama.
                        |
                        |--------------------------------------------------------------------------
                        */

                        if (rows.length > 1) {

                            destroySelect2(row);

                            row.remove();

                            reindexRuleRows();

                            syncHeaderValues(header);

                            updateRemoveButtons(
                                header
                            );

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | JIKA TINGGAL 1
                        |--------------------------------------------------------------------------
                        */

                        showToast(
                            'error',
                            'Setiap header harus memiliki minimal satu markup.'
                        );
                    }
                );


                updateRemoveButtons(header);
            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE REMOVE BUTTON
            |--------------------------------------------------------------------------
            */

            function updateRemoveButtons(header) {

                const rows =
                    getRuleRows(header);


                rows.forEach(function(row) {

                    const button =
                        row.querySelector(
                            '.btn-remove-markup'
                        );


                    if (!button) {
                        return;
                    }


                    /*
                     * Tombol selalu ditampilkan.
                     *
                     * Jika tinggal satu row,
                     * ketika diklik akan muncul toast.
                     */
                    button.style.display = '';
                });
            }

            function updateVendorField(header) {
                const location = header.querySelector('.markup-location');
                const vendorField = header.querySelector('.markup-vendor');
                const vendorWrapper = header.querySelector('.markup-vendor-field');

                if (!location || !vendorField || !vendorWrapper) {
                    return;
                }

                const selectedOption = location.options[location.selectedIndex];

                const locationName = selectedOption && location.value ?
                    selectedOption.textContent.trim().toLowerCase() :
                    '';

                console.log('Location ID:', location.value);
                console.log('Location Name:', locationName);

                if (locationName === 'outsourcing') {
                    vendorWrapper.style.display = '';
                    vendorField.disabled = false;
                } else {
                    vendorWrapper.style.display = 'none';
                    vendorField.disabled = true;
                    vendorField.value = '';
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CREATE HEADER
            |--------------------------------------------------------------------------
            */

            function createMarkupHeader(index) {

                const header =
                    document.createElement('div');


                header.className =
                    'markup-header-group';


                header.dataset.headerIndex =
                    index;


                header.innerHTML = `
                    <div class="markup-row markup-header-row">

                        <div style="display: flex; justify-content: right; align-items: center; gap: 10px;">
                            <div class="markup-header-action">
                                <i
                                    class="bi bi-trash3-fill btn-remove-header"
                                    style="cursor: pointer; border: none !important;"
                                ></i>
                            </div>
                        </div>

                        <div class="markup-row-top">
                            <div class="markup-field">

                                <label class="category-label">
                                    Mesin
                                </label>

                                <select
                                    class="select markup-engine"
                                    required
                                >
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

                            <div class="markup-field">
                                <label class="category-label">
                                    Lokasi
                                </label>

                                <select
                                    class="select markup-location"
                                    required
                                >
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

                            <div class="markup-field markup-vendor-field" style="display: none;">
                                <label class="category-label">
                                    Vendor
                                </label>

                                <select class="select markup-vendor" disabled>
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

                            <div class="markup-field">
                                <label class="category-label">
                                    Kategori
                                </label>

                                <select
                                    class="select markup-category"
                                    required
                                >
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
                                    untuk lokasi dan kategori ini.
                                </p>
                            </div>

                            <button
                                type="button"
                                class="btn btn--ghost btnTambahMarkup"
                            >
                                <svg viewBox="0 0 24 24">
                                    <path d="M12 5v14" />
                                    <path d="M5 12h14" />
                                </svg>

                                Tambah Markup
                            </button>

                        </div>

                        <div class="markup-rules-container">

                            <div
                                class="markup-row markup-rule-row"
                                data-rule-index="${ruleIndex}"
                            >

                                <input
                                    type="hidden"
                                    name="pricing_rules[${ruleIndex}][engine_id]"
                                    class="markup-engine-id"
                                >

                                <input
                                    type="hidden"
                                    name="pricing_rules[${ruleIndex}][location_id]"
                                    class="markup-location-id"
                                >

                                <input
                                    type="hidden"
                                    name="pricing_rules[${ruleIndex}][category_id]"
                                    class="markup-category-id"
                                >

                                <input
                                    type="hidden"
                                    name="pricing_rules[${ruleIndex}][vendor_id]"
                                    class="markup-vendor-id"
                                >

                                <div class="markup-remove">
                                    <i
                                        class="bi bi-trash3-fill btn-remove-markup"
                                        style="cursor: pointer; border: none !important;"
                                    ></i>
                                </div>

                                <div class="markup-field">
                                    <label class="category-label">
                                        Tipe Harga
                                    </label>

                                    <select
                                        name="pricing_rules[${ruleIndex}][price_type]"
                                        class="select markup-price-type"
                                        required
                                    >
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

                                    <input
                                        type="number"
                                        name="pricing_rules[${ruleIndex}][markup_percentage]"
                                        class="input markup-percentage"
                                        min="0"
                                        step="0.01"
                                        placeholder="Contoh: 10"
                                        required
                                    >
                                </div>

                                <div class="markup-field">
                                    <label class="category-label">
                                        Pembulatan (Rp)
                                    </label>

                                    <input
                                        type="text"
                                        name="pricing_rules[${ruleIndex}][rounding_value]"
                                        class="input markup-rounding"
                                        inputmode="numeric"
                                        autocomplete="off"
                                        placeholder="Contoh: Rp 1.000"
                                        required
                                    >
                                </div>

                                <div class="markup-field">
                                    <label class="category-label">
                                        Status
                                    </label>

                                    <select
                                        name="pricing_rules[${ruleIndex}][status]"
                                        class="select markup-status"
                                        required
                                    >
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

                const firstRuleRow =
                    header.querySelector('.markup-rule-row');

                if (firstRuleRow) {
                    installDigitalPricingFields(
                        firstRuleRow,
                        firstRuleRow.dataset.ruleIndex
                    );
                }

                updateMarkupBody(header);

                markupHeaderContainer.appendChild(
                    header
                );


                ruleIndex++;


                initSelect2(header);

                const engine =
                    header.querySelector(
                        '.markup-engine'
                    );

                const location =
                    header.querySelector(
                        '.markup-location'
                    );


                const category =
                    header.querySelector(
                        '.markup-category'
                    );

                if (engine) {

                    $(engine).on(
                        'change select2:select select2:clear',
                        function() {

                            syncHeaderValues(
                                header
                            );

                            updateMarkupBody(header);

                            clearFieldError(
                                engine
                            );

                        }
                    );

                }

                // if (location) {

                //     location.addEventListener(
                //         'change',
                //         function() {

                //             console.log('CHANGE LOCATION TERPICU');
                //             console.log('VALUE:', this.value);
                //             console.log('SELECTED:', this.options[this.selectedIndex]?.text);

                //             syncHeaderValues(
                //                 header
                //             );

                //             clearFieldError(
                //                 location
                //             );

                //             updateVendorField(header);
                //         }
                //     );
                // }

                if (location) {
                    $(location).on('select2:select', function() {
                        syncHeaderValues(header);
                        clearFieldError(location);
                        updateVendorField(header);
                    });
                }

                const vendor =
                    header.querySelector(
                        '.markup-vendor'
                    );

                if (vendor) {

                    $(vendor).on(
                        'select2:select',
                        function() {

                            syncHeaderValues(
                                header
                            );

                            clearFieldError(
                                vendor
                            );
                        }
                    );
                }

                if (category) {

                    $(category).on(
                        'change select2:select select2:clear',
                        function() {

                            syncHeaderValues(
                                header
                            );

                            updateMarkupBody(header);

                            clearFieldError(
                                category
                            );
                        }
                    );
                }


                /*
                |--------------------------------------------------------------------------
                | TAMBAH MARKUP
                |--------------------------------------------------------------------------
                */

                const btnTambahMarkup =
                    header.querySelector(
                        '.btnTambahMarkup'
                    );


                btnTambahMarkup.addEventListener(
                    'click',
                    function() {

                        createMarkupRow(
                            header,
                            ruleIndex
                        );

                        ruleIndex++;
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | HAPUS HEADER
                |--------------------------------------------------------------------------
                */

                const btnRemoveHeader =
                    header.querySelector(
                        '.btn-remove-header'
                    );


                btnRemoveHeader.addEventListener(
                    'click',
                    function() {

                        const headers =
                            getHeaderGroups();


                        if (headers.length <= 1) {

                            showToast(
                                'error',
                                'Minimal harus ada satu header.'
                            );

                            return;
                        }


                        destroySelect2(header);

                        header.remove();


                        reindexHeaderGroups();

                        reindexRuleRows();
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | HAPUS MARKUP PERTAMA
                |--------------------------------------------------------------------------
                |
                | Row pertama dibuat langsung dari template,
                | jadi event listener harus dipasang manual.
                |
                |--------------------------------------------------------------------------
                */

                const firstRow =
                    header.querySelector(
                        '.markup-rule-row'
                    );


                if (firstRow) {

                    const btnRemoveFirstRow =
                        firstRow.querySelector(
                            '.btn-remove-markup'
                        );


                    if (btnRemoveFirstRow) {

                        btnRemoveFirstRow.addEventListener(
                            'click',
                            function() {

                                const rows =
                                    getRuleRows(header);


                                /*
                                 * Jika masih lebih dari satu,
                                 * row pertama boleh dihapus.
                                 */
                                if (rows.length > 1) {

                                    destroySelect2(
                                        firstRow
                                    );

                                    firstRow.remove();

                                    reindexRuleRows();

                                    syncHeaderValues(
                                        header
                                    );

                                    updateRemoveButtons(
                                        header
                                    );

                                    return;
                                }


                                /*
                                 * Jika hanya tersisa satu,
                                 * jangan hapus.
                                 */
                                showToast(
                                    'error',
                                    'Setiap header harus memiliki minimal satu markup.'
                                );
                            }
                        );
                    }
                }


                updateRemoveButtons(header);

                updateVendorField(header);
                return header;
            }


            /*
            |--------------------------------------------------------------------------
            | REINDEX HEADER
            |--------------------------------------------------------------------------
            */

            function reindexHeaderGroups() {

                let newHeaderIndex = 0;


                getHeaderGroups().forEach(
                    function(header) {

                        header.dataset.headerIndex =
                            newHeaderIndex;

                        newHeaderIndex++;
                    }
                );


                headerIndex =
                    newHeaderIndex;
            }


            /*
            |--------------------------------------------------------------------------
            | TAMBAH HEADER
            |--------------------------------------------------------------------------
            */

            btnTambahHeaderMarkup.addEventListener(
                'click',
                function() {

                    const header =
                        createMarkupHeader(
                            headerIndex
                        );


                    headerIndex++;


                    initSelect2(header);
                }
            );


            /*
            |--------------------------------------------------------------------------
            | CLEAR VALIDATION SAAT INPUT
            |--------------------------------------------------------------------------
            */

            formTambahMarkup.addEventListener(
                'input',
                function(event) {

                    if (
                        event.target.matches(
                            '.markup-percentage, .digital-tier-percentage, .markup-rounding'
                        )
                    ) {

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

                        clearFieldError(
                            event.target
                        );
                    }
                }
            );


            formTambahMarkup.addEventListener(
                'change',
                function(event) {

                    if (
                        event.target.matches(
                            '.markup-engine, .markup-location, .markup-vendor, .markup-category, .markup-price-type, .markup-print-type, .markup-status'
                        )
                    ) {

                        clearFieldError(
                            event.target
                        );
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | SUBMIT
            |--------------------------------------------------------------------------
            */

            formTambahMarkup.addEventListener(
                'submit',
                async function(event) {

                    event.preventDefault();


                    if (!validateForm()) {
                        return;
                    }

                    const digitalPricingModes =
                        Array.from(getHeaderGroups()).map(isDigitalPricingHeader);

                    if (
                        digitalPricingModes.some(Boolean) &&
                        !digitalPricingModes.every(Boolean)
                    ) {
                        showToast(
                            'error',
                            'Pisahkan konfigurasi Digital Print A3+ dan mesin lain ke submit yang berbeda.'
                        );
                        return;
                    }

                    formTambahMarkup.action = digitalPricingModes.every(Boolean) ?
                        "{{ route('admin_store_digital_pricing_rule') }}" :
                        "{{ route('admin_store_pricing_rule') }}";


                    syncAllHeaderValues();

                    /*
                     * Pastikan index request selalu berurutan
                     * sebelum dikirim ke backend.
                     */

                    reindexRuleRows();

                    formTambahMarkup
                        .querySelectorAll('.markup-rounding')
                        .forEach(function(input) {
                            input.value =
                                unformatRupiah(
                                    input.value
                                );
                        });

                    const originalButtonHTML =
                        btnSimpanMarkup.innerHTML;


                    btnSimpanMarkup.disabled =
                        true;


                    btnSimpanMarkup.innerHTML = `
                <span>Menyimpan...</span>
            `;


                    try {

                        const formData =
                            new FormData(
                                formTambahMarkup
                            );


                        const csrfToken =
                            document
                            .querySelector(
                                'meta[name="csrf-token"]'
                            )
                            .getAttribute(
                                'content'
                            );


                        const response =
                            await fetch(
                                formTambahMarkup.action, {
                                    method: 'POST',

                                    headers: {
                                        'Accept': 'application/json',

                                        'X-CSRF-TOKEN': csrfToken
                                    },

                                    body: formData
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
                                'Gagal menyimpan markup harga.'
                            );
                        }


                        showToast(
                            'success',
                            data.message ||
                            'Markup harga berhasil disimpan.'
                        );


                        closeTambahMarkupModal();


                        if (
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
                            'Tambah Pricing Rules Error:',
                            error
                        );


                        showToast(
                            'error',
                            error.message ||
                            'Terjadi kesalahan saat menyimpan markup harga.'
                        );


                    } finally {

                        btnSimpanMarkup.disabled =
                            false;


                        btnSimpanMarkup.innerHTML =
                            originalButtonHTML;
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | RESET FORM
            |--------------------------------------------------------------------------
            */

            function resetTambahMarkupForm() {

                destroySelect2(
                    markupHeaderContainer
                );


                markupHeaderContainer.innerHTML =
                    '';


                headerIndex = 1;

                ruleIndex = 0;


                createMarkupHeader(0);


                headerIndex = 1;


                reindexRuleRows();


                initSelect2(
                    markupHeaderContainer
                );


                clearAllValidation();
            }


            /*
            |--------------------------------------------------------------------------
            | INITIALIZE
            |--------------------------------------------------------------------------
            */

            resetTambahMarkupForm();

        });


        document.addEventListener('DOMContentLoaded', function() {

            const modalEdit = document.getElementById('modalEditMarkup');
            const formEditMarkup = document.getElementById('formEditMarkup');
            const editMarkupRows = document.getElementById('editMarkupRows');
            const editMarkupEngine = document.getElementById('editMarkupEngine');
            const editMarkupLocation = document.getElementById('editMarkupLocation');
            const editMarkupVendor = document.getElementById('editMarkupVendor');
            const editMarkupCategory = document.getElementById('editMarkupCategory');
            const btnTutupEdit = document.getElementById('btnTutupModalEditMarkup');
            const btnBatalEdit = document.getElementById('btnBatalEditMarkup');
            const btnTambahEditMarkup = document.getElementById('btnTambahEditMarkup');

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

            function formatRupiah(value) {
                const number = String(value).replace(/\D/g, '');

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

            /* =========================================================
             * SELECT2
             * ========================================================= */

            function initEditSelect2(container) {

                $(container)
                    .find('.select2')
                    .each(function() {

                        if (!$(this).hasClass('select2-hidden-accessible')) {

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

                        if ($(this).hasClass('select2-hidden-accessible')) {

                            $(this).select2('destroy');

                        }

                    });

            }


            /* =========================================================
             * MODAL
             * ========================================================= */

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


            /* =========================================================
             * CREATE ROW
             * ========================================================= */

            function createEditMarkupRow(rule, index) {

                const row = document.createElement('div');

                row.classList.add('markup-row');

                const priceType = rule && rule.price_type ?
                    rule.price_type :
                    '';

                const markupPercentage = rule && rule.markup_percentage !== null ?
                    rule.markup_percentage :
                    '';

                const roundingValue = rule && rule.rounding_value !== null ?
                    rule.rounding_value :
                    '';

                const status = rule && rule.status ?
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
                                    ${priceType === 'general' ? 'selected' : ''}
                                >
                                    Harga Umum
                                </option>

                                <option
                                    value="division"
                                    ${priceType === 'division' ? 'selected' : ''}
                                >
                                    Harga Divisi
                                </option>

                                <option
                                    value="plain"
                                    ${priceType === 'plain' ? 'selected' : ''}
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
                                value="${formatRupiah(Number(roundingValue))}"
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
                                    ${status === 'Active' ? 'selected' : ''}
                                >
                                    Active
                                </option>

                                <option
                                    value="Inactive"
                                    ${status === 'Inactive' ? 'selected' : ''}
                                >
                                    Inactive
                                </option>
                            </select>

                        </div>

                    </div>
                `;

                return row;

            }


            /* =========================================================
             * RE-INDEX ROW
             * =========================================================
             *
             * Contoh:
             *
             * Sebelum:
             * pricing_rules[0]
             * pricing_rules[1]
             * pricing_rules[3]
             *
             * Setelah:
             * pricing_rules[0]
             * pricing_rules[1]
             * pricing_rules[2]
             *
             * Ini penting karena row bisa dihapus.
             * ========================================================= */

            function reindexEditMarkupRows() {

                const rows = editMarkupRows.querySelectorAll('.markup-row');

                rows.forEach(function(row, index) {

                    const fields = row.querySelectorAll('[name]');

                    fields.forEach(function(field) {

                        const name = field.getAttribute('name');

                        if (!name) {
                            return;
                        }

                        const newName = name.replace(
                            /^pricing_rules\[[^\]]+\]/,
                            `pricing_rules[${index}]`
                        );

                        field.setAttribute('name', newName);

                    });

                });

            }


            /* =========================================================
             * TAMBAH ROW BARU
             * ========================================================= */

            function tambahMarkupEditRow() {

                newEditMarkupIndex++;

                const row = createEditMarkupRow({
                        price_type: '',
                        markup_percentage: '',
                        rounding_value: '',
                        status: 'Active'
                    },
                    `new_${newEditMarkupIndex}`
                );

                editMarkupRows.appendChild(row);

                initEditSelect2(row);

            }


            /* =========================================================
             * BUKA EDIT
             * ========================================================= */

            document.addEventListener('click', function(event) {

                const editButton =
                    event.target.closest('.btn-edit-pricing-rule');

                if (!editButton) {
                    return;
                }

                const engineId =
                    editButton.dataset.engineId;

                const locationId =
                    editButton.dataset.locationId;

                const vendorId =
                    editButton.dataset.vendorId;

                const categoryId =
                    editButton.dataset.categoryId;


                if (!engineId || !locationId || !vendorId || !categoryId) {

                    showToast(
                        'error',
                        'Data mesin, lokasi, vendor atau kategori tidak ditemukan.'
                    );

                    return;

                }

                currentEngineId = engineId;
                currentLocationId = locationId;
                currentVendorId = vendorId;
                currentCategoryId = categoryId;


                resetModalEdit();

                currentEngineId = engineId;
                currentLocationId = locationId;
                currentVendorId = vendorId;
                currentCategoryId = categoryId;


                const editUrl =
                    `{{ url('/admin/pricing-rules') }}/${engineId}/${locationId}/${vendorId}/${categoryId}/edit`;


                const updateUrl =
                    `{{ url('/admin/pricing-rules') }}/${engineId}/${locationId}/${vendorId}/${categoryId}/edit`;


                /*
                 * Action form diarahkan ke endpoint update.
                 */
                formEditMarkup.action = updateUrl;


                /*
                 * Ambil data markup berdasarkan
                 * location_id + category_id.
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

                            data = await response.json();

                        } catch (error) {

                            throw new Error(
                                'Terjadi kesalahan pada response server.'
                            );

                        }


                        if (!response.ok || !data.success) {

                            throw new Error(
                                data.message ||
                                'Data markup harga gagal dimuat.'
                            );

                        }


                        return data;

                    })

                    .then(function(data) {

                        const pricingRules = data.data;


                        if (
                            !pricingRules ||
                            !Array.isArray(pricingRules) ||
                            !pricingRules.length
                        ) {

                            throw new Error(
                                'Data markup harga tidak ditemukan.'
                            );

                        }


                        const firstRule = pricingRules[0];

                        editMarkupEngine.innerHTML = `
                            <option value="${firstRule.engine_id}">
                                ${firstRule.engine_name}
                            </option>
                        `;

                        editMarkupEngine.value =
                            firstRule.engine_id;


                        /*
                         * Isi informasi lokasi.
                         */
                        editMarkupLocation.innerHTML = `
                            <option value="${firstRule.location_id}">
                                ${firstRule.location_name}
                            </option>
                        `;

                        editMarkupLocation.value =
                            firstRule.location_id;

                        editMarkupVendor.innerHTML = `
                            <option value="${firstRule.vendor_id ?? 'null'}">
                                ${firstRule.vendor_name || '-'}
                            </option>
                        `;

                        editMarkupVendor.value =
                            firstRule.vendor_id ?? 'null';


                        /*
                         * Isi informasi kategori.
                         */
                        editMarkupCategory.innerHTML = `
                            <option value="${firstRule.category_id}">
                                ${firstRule.category_name}
                            </option>
                        `;

                        editMarkupCategory.value =
                            firstRule.category_id;


                        /*
                         * Bersihkan row lama.
                         */
                        editMarkupRows.innerHTML = '';

                        newEditMarkupIndex = 0;


                        /*
                         * Render semua markup yang sudah ada.
                         *
                         * Tidak menggunakan ID database
                         * sebagai key request.
                         */
                        pricingRules.forEach(function(rule, index) {

                            const row =
                                createEditMarkupRow(rule, index);

                            editMarkupRows.appendChild(row);

                            initEditSelect2(row);

                        });


                        /*
                         * Pastikan index rapi.
                         */
                        reindexEditMarkupRows();


                        bukaModalEdit();

                    })

                    .catch(function(error) {

                        showToast(
                            'error',
                            error.message ||
                            'Terjadi kesalahan saat memuat markup harga.'
                        );

                    });

            });


            /* =========================================================
             * TUTUP MODAL
             * ========================================================= */

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

                    if (event.target === modalEdit) {

                        tutupModalEdit();

                    }

                }
            );


            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modalEdit.classList.contains('is-open')
                    ) {

                        tutupModalEdit();

                    }

                }
            );


            /* =========================================================
             * HAPUS ROW
             * ========================================================= */

            editMarkupRows.addEventListener(
                'click',
                function(event) {

                    const removeButton =
                        event.target.closest('.btn-remove-markup');

                    if (!removeButton) {
                        return;
                    }


                    const row =
                        removeButton.closest('.markup-row');

                    if (!row) {
                        return;
                    }


                    row.remove();


                    /*
                     * Langsung rapikan index setelah row dihapus.
                     */
                    reindexEditMarkupRows();

                }
            );


            /* =========================================================
             * TAMBAH MARKUP
             * ========================================================= */

            btnTambahEditMarkup.addEventListener(
                'click',
                function() {

                    tambahMarkupEditRow();

                }
            );

            /* =========================================================
             * FORMAT RUPIAH PEMBULATAN
             * ========================================================= */

            formEditMarkup.addEventListener(
                'input',
                function(event) {

                    if (
                        event.target.matches('.markup-rounding')
                    ) {
                        event.target.value =
                            formatRupiah(
                                event.target.value
                            );
                    }

                }
            );

            /* =========================================================
             * SUBMIT UPDATE
             * ========================================================= */

            formEditMarkup.addEventListener(
                'submit',
                function(event) {

                    event.preventDefault();


                    /*
                     * Cegah submit double-click.
                     */
                    if (isSubmittingEdit) {
                        return;
                    }


                    /*
                     * Pastikan minimal ada satu row.
                     *
                     * Backend juga tetap melakukan validasi.
                     */
                    const rows =
                        editMarkupRows.querySelectorAll('.markup-row');


                    if (!rows.length) {

                        showToast(
                            'error',
                            'Minimal harus ada satu markup harga.'
                        );

                        return;

                    }


                    /*
                     * Validasi HTML5.
                     */
                    if (!formEditMarkup.checkValidity()) {

                        formEditMarkup.reportValidity();

                        showToast(
                            'error',
                            'Mohon lengkapi data markup harga terlebih dahulu.'
                        );

                        return;

                    }


                    /*
                     * Rapikan index sebelum dikirim.
                     */
                    reindexEditMarkupRows();


                    formEditMarkup
                        .querySelectorAll('.markup-rounding')
                        .forEach(function(input) {
                            input.value =
                                unformatRupiah(
                                    input.value
                                );
                        });

                    /*
                     * Ambil semua data form.
                     *
                     * Form sudah mempunyai:
                     * @csrf
                     * @method('PUT')
                     */
                    const formData =
                        new FormData(formEditMarkup);


                    isSubmittingEdit = true;


                    const submitButton =
                        formEditMarkup.querySelector(
                            'button[type="submit"]'
                        );


                    if (submitButton) {

                        submitButton.disabled = true;

                    }


                    fetch(formEditMarkup.action, {

                            /*
                             * Laravel membaca _method=PUT
                             * dari FormData.
                             */
                            method: 'POST',

                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },

                            body: formData

                        })

                        .then(async function(response) {

                            let data;

                            try {

                                data = await response.json();

                            } catch (error) {

                                throw new Error(
                                    'Terjadi kesalahan pada response server.'
                                );

                            }


                            if (!response.ok || !data.success) {

                                /*
                                 * Jika Laravel mengembalikan
                                 * validation errors.
                                 */
                                if (
                                    data.errors &&
                                    typeof data.errors === 'object'
                                ) {

                                    const firstErrorKey =
                                        Object.keys(data.errors)[0];

                                    if (firstErrorKey) {

                                        const firstError =
                                            data.errors[firstErrorKey];

                                        if (
                                            Array.isArray(firstError) &&
                                            firstError.length
                                        ) {

                                            throw new Error(
                                                firstError[0]
                                            );

                                        }

                                        if (typeof firstError === 'string') {

                                            throw new Error(
                                                firstError
                                            );

                                        }

                                    }

                                }


                                throw new Error(
                                    data.message ||
                                    'Markup harga gagal diperbarui.'
                                );

                            }


                            return data;

                        })

                        .then(function(data) {

                            showToast(
                                'success',
                                data.message ||
                                'Markup harga berhasil diperbarui.'
                            );


                            tutupModalEdit();


                            /*
                             * Bersihkan modal setelah berhasil.
                             */
                            resetModalEdit();


                            /*
                             * Reload DataTable.
                             *
                             * Tidak mengubah halaman saat ini.
                             */
                            if (
                                typeof $ !== 'undefined' &&
                                $.fn.DataTable &&
                                $('#pricingRuleTable').length
                            ) {

                                $('#pricingRuleTable')
                                    .DataTable()
                                    .ajax
                                    .reload(null, false);

                            }

                        })

                        .catch(function(error) {

                            showToast(
                                'error',
                                error.message ||
                                'Terjadi kesalahan saat memperbarui markup harga.'
                            );

                        })

                        .finally(function() {

                            isSubmittingEdit = false;


                            if (submitButton) {

                                submitButton.disabled = false;

                            }

                        });

                }
            );

        });

        document.addEventListener('DOMContentLoaded', function() {

            const modalDetail =
                document.getElementById('modalDetailMarkup');

            const btnTutupDetail =
                document.getElementById('btnTutupModalDetailMarkup');

            const btnBatalDetail =
                document.getElementById('btnBatalDetailMarkup');

            const detailMarkupEngine =
                document.getElementById('detailMarkupEngine');

            const detailMarkupLocation =
                document.getElementById('detailMarkupLocation');

            const detailMarkupVendor =
                document.getElementById('detailMarkupVendor');

            const detailMarkupCategory =
                document.getElementById('detailMarkupCategory');

            const detailMarkupContent =
                document.getElementById('detailMarkupContent');


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
                            Tidak ada data markup harga.
                        </div>
                    `;

                    return;

                }

                /*
                 * =====================================================
                 * MARKUP ACCORDION
                 * =====================================================
                 */

                const accordion =
                    document.createElement('div');

                accordion.classList.add('details-category');

                accordion.setAttribute(
                    'data-markup-accordion',
                    ''
                );


                /*
                 * =====================================================
                 * ACCORDION HEADER
                 * =====================================================
                 */

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
                            MARKUP HARGA
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


                /*
                 * =====================================================
                 * ACCORDION CONTENT
                 * =====================================================
                 */

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


                /*
                 * =====================================================
                 * TABLE
                 * =====================================================
                 */

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


                /*
                 * =====================================================
                 * ROW DATA
                 * =====================================================
                 */

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
                        formatRupiah(rule.rounding_value) :
                        '-';


                    tr.innerHTML = `
                        <td>
                            <div class="details-material-name">
                                ${getPriceTypeLabel(rule.price_type)}
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

                detailMarkupContent.appendChild(accordion);


                /*
                 * =====================================================
                 * ACCORDION BEHAVIOR
                 * =====================================================
                 */

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

                const groups = [
                    ['general', 'single'],
                    ['general', 'double'],
                    ['division', 'single'],
                    ['division', 'double']
                ];

                groups.forEach(function([priceType, printType]) {

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

                    const printTypeLabel =
                        printType === 'single' ?
                        '1 Muka' :
                        '2 Muka';

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
                            <span class="badge">${printTypeLabel}</span>
                        </div>
                    `;

                    const tableWrapper =
                        document.createElement('div');

                    tableWrapper.classList.add(
                        'details-table-wrapper'
                    );

                    const table =
                        document.createElement('table');

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

                        const statusClass =
                            rule.status === 'Active' ?
                            'details-status-active' :
                            'details-status-inactive';

                        const maximumQuantity =
                            rule.maximum_quantity === null ?
                            '+' :
                            rule.minimum_quantity === rule.maximum_quantity ?
                            '' :
                            ` - ${rule.maximum_quantity}`;

                        const tr = document.createElement('tr');

                        tr.innerHTML = `
                            <td>${rule.minimum_quantity}${maximumQuantity} lbr</td>
                            <td class="details-money">
                                ${rule.markup_percentage ?? '-'}%
                            </td>
                            <td class="details-money" style="text-align: center;">
                                ${formatRupiah(rule.rounding_value)}
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
                            '.btn-detail-pricing-rule'
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


                    // if (!locationId || !categoryId) {

                    //     showToast(
                    //         'error',
                    //         'Data mesin,  lokasi atau kategori tidak ditemukan.'
                    //     );

                    //     return;

                    // }

                    if (
                        pricingRuleSource === 'digital' &&
                        !digitalPricingRuleId
                    ) {
                        showToast(
                            'error',
                            'Data markup Digital Print tidak ditemukan.'
                        );
                        return;
                    }

                    if (
                        pricingRuleSource !== 'digital' &&
                        (!engineId || !locationId || !vendorId || !categoryId)
                    ) {
                        showToast(
                            'error',
                            'Data mesin, lokasi, vendor atau kategori tidak ditemukan.'
                        );
                        return;
                    }


                    /*
                     * Simpan ID pada modal.
                     */

                    modalDetail.dataset.engineId =
                        engineId;

                    modalDetail.dataset.locationId =
                        locationId;

                    modalDetail.dataset.categoryId =
                        categoryId;


                    /*
                     * Reset isi modal.
                     */

                    detailMarkupLocation.textContent =
                        'Memuat...';

                    detailMarkupVendor.textContent =
                        'Memuat...';

                    detailMarkupCategory.textContent =
                        'Memuat...';

                    detailMarkupContent.innerHTML = `
                        <div class="details-empty">
                            Memuat data markup harga...
                        </div>
                    `;


                    /*
                     * Buka modal terlebih dahulu.
                     */

                    bukaModalDetail();


                    /*
                     * URL DETAIL
                     */

                    const detailUrl =
                        pricingRuleSource === 'digital' ?
                        `{{ url('/admin/pricing-rules/digital') }}/${digitalPricingRuleId}/details` :
                        `{{ url('/admin/pricing-rules') }}/${engineId}/${locationId}/${vendorId}/${categoryId}/details`;


                    /*
                     * FETCH DATA
                     */

                    fetch(detailUrl, {

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
                                    'Data markup harga gagal dimuat.'
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
                                    'Data markup harga tidak ditemukan.'
                                );

                            }


                            const firstRule =
                                pricingRules[0];


                            detailMarkupEngine.textContent =
                                firstRule.engine_name || '-';

                            /*
                             * =================================================
                             * LOCATION
                             * =================================================
                             */

                            detailMarkupLocation.textContent =
                                firstRule.location_name || '-';


                            detailMarkupVendor.textContent =
                                firstRule.vendor_name || '-';

                            /*
                             * =================================================
                             * CATEGORY
                             * =================================================
                             */

                            detailMarkupCategory.textContent =
                                firstRule.category_name || '-';


                            /*
                             * =================================================
                             * RENDER DETAIL
                             * =================================================
                             */

                            if (pricingRuleSource === 'digital') {
                                renderDigitalDetailMarkup(pricingRules);
                            } else {
                                renderDetailMarkup(pricingRules);
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
                                    Data markup harga gagal dimuat.
                                </div>
                            `;


                            showToast(
                                'error',
                                error.message ||
                                'Terjadi kesalahan saat memuat detail markup harga.'
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
                        modalDetail.classList.contains('is-open')
                    ) {

                        tutupModalDetail();

                    }

                }
            );

        });

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | DELETE PRICING RULE
            |--------------------------------------------------------------------------
            */

            let pricingRuleEngineIdToDelete = null;
            let pricingRuleLocationIdToDelete = null;
            let pricingRuleVendorIdToDelete = null;
            let pricingRuleCategoryIdToDelete = null;
            let digitalPricingRuleIdToDelete = null;


            const modalDeletePricingRule =
                document.getElementById(
                    'modalDeletePricingRule'
                );

            const btnTutupDeletePricingRule =
                document.getElementById(
                    'btnTutupDeletePricingRule'
                );

            const btnBatalDeletePricingRule =
                document.getElementById(
                    'btnBatalDeletePricingRule'
                );

            const btnConfirmDeletePricingRule =
                document.getElementById(
                    'btnConfirmDeletePricingRule'
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
                            '.btn-delete-pricing-rule, .btn-delete-digital-pricing-rule'
                        );

                    if (!deleteButton) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL LOCATION ID DAN CATEGORY ID
                    |--------------------------------------------------------------------------
                    */

                    digitalPricingRuleIdToDelete =
                        deleteButton.dataset.digitalPricingRuleId || null;

                    pricingRuleEngineIdToDelete =
                        deleteButton.dataset.engineId || null;

                    pricingRuleLocationIdToDelete =
                        deleteButton.dataset.locationId || null;

                    pricingRuleVendorIdToDelete =
                        deleteButton.dataset.vendorId;

                    pricingRuleCategoryIdToDelete =
                        deleteButton.dataset.categoryId || null;


                    /*
                    |--------------------------------------------------------------------------
                    | CEK DATA
                    |--------------------------------------------------------------------------
                    */

                    const isDigitalDelete =
                        Boolean(digitalPricingRuleIdToDelete);

                    if (
                        (isDigitalDelete &&
                            !digitalPricingRuleIdToDelete) ||
                        (!isDigitalDelete &&
                            (
                                !pricingRuleEngineIdToDelete ||
                                !pricingRuleLocationIdToDelete ||
                                !pricingRuleCategoryIdToDelete ||
                                typeof pricingRuleVendorIdToDelete === 'undefined'
                            ))
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

                    modalDeletePricingRule.classList.add(
                        'is-open'
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupDeletePricingRuleModal() {

                modalDeletePricingRule.classList.remove(
                    'is-open'
                );

                pricingRuleEngineIdToDelete = null;
                pricingRuleLocationIdToDelete = null;
                pricingRuleVendorIdToDelete = null;
                pricingRuleCategoryIdToDelete = null;
                digitalPricingRuleIdToDelete = null;

            }


            btnTutupDeletePricingRule.addEventListener(
                'click',
                tutupDeletePricingRuleModal
            );


            btnBatalDeletePricingRule.addEventListener(
                'click',
                tutupDeletePricingRuleModal
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modalDeletePricingRule.addEventListener(
                'click',
                function(event) {

                    if (
                        event.target === modalDeletePricingRule
                    ) {

                        tutupDeletePricingRuleModal();

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
                        modalDeletePricingRule.classList.contains(
                            'is-open'
                        )
                    ) {

                        tutupDeletePricingRuleModal();

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI HAPUS PRICING RULE
            |--------------------------------------------------------------------------
            */

            btnConfirmDeletePricingRule.addEventListener(
                'click',
                async function() {


                    /*
                    |--------------------------------------------------------------------------
                    | CEK LOCATION ID DAN CATEGORY ID
                    |--------------------------------------------------------------------------
                    */

                    const isDigitalDelete =
                        Boolean(digitalPricingRuleIdToDelete);

                    if (
                        (isDigitalDelete &&
                            !digitalPricingRuleIdToDelete) ||
                        (!isDigitalDelete &&
                            (
                                !pricingRuleEngineIdToDelete ||
                                !pricingRuleLocationIdToDelete ||
                                typeof pricingRuleVendorIdToDelete === 'undefined' ||
                                !pricingRuleCategoryIdToDelete
                            ))
                    ) {

                        return;

                    }


                    try {


                        /*
                        |--------------------------------------------------------------------------
                        | REQUEST DELETE
                        |--------------------------------------------------------------------------
                        */

                        const deleteUrl = isDigitalDelete ?
                            `/admin/pricing-rules/digital/${digitalPricingRuleIdToDelete}` :
                            `/admin/pricing-rules/${pricingRuleEngineIdToDelete}/${pricingRuleLocationIdToDelete}/${pricingRuleVendorIdToDelete}/${pricingRuleCategoryIdToDelete}/delete`;

                        const response =
                            await fetch(deleteUrl, {
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
                            });


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

                            tutupDeletePricingRuleModal();


                            /*
                            |--------------------------------------------------------------------------
                            | RELOAD DATATABLE
                            |--------------------------------------------------------------------------
                            */

                            pricingRuleTable.ajax.reload(
                                null,
                                false
                            );


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
                                'Markup harga gagal dihapus.'
                            );

                        }


                    } catch (error) {


                        console.error(error);


                        /*
                        |--------------------------------------------------------------------------
                        | ERROR SERVER
                        |--------------------------------------------------------------------------
                        */

                        showToast(
                            'error',
                            'Terjadi kesalahan saat menghapus markup harga.'
                        );

                    }

                }
            );

        });

        document.addEventListener('DOMContentLoaded', function() {

            const btnImportPricingRules =
                document.getElementById('btnImportPricingRules');

            const modalImportPricingRules =
                document.getElementById('modalImportPricingRules');

            const btnCloseImportPricingRules =
                document.getElementById('btnCloseImportPricingRules');

            const btnCancelImportPricingRules =
                document.getElementById('btnCancelImportPricingRules');

            const btnSaveImportPricingRules =
                document.getElementById('btnSaveImportPricingRules');

            const fileImportPricingRules =
                document.getElementById('fileImportPricingRules');

            const fileSelectedNamePricingRules =
                document.getElementById('fileSelectedNamePricingRules');


            /*
             * ==========================================================
             * OPEN MODAL
             * ==========================================================
             */

            btnImportPricingRules.addEventListener('click', function() {

                modalImportPricingRules.classList.add('is-open');

            });


            /*
             * ==========================================================
             * CLOSE MODAL
             * ==========================================================
             */

            function closeImportPricingRulesModal() {

                modalImportPricingRules.classList.remove('is-open');

                fileImportPricingRules.value = '';

                fileSelectedNamePricingRules.textContent =
                    'Belum ada file dipilih';

                fileSelectedNamePricingRules.classList.remove('has-file');

            }


            btnCloseImportPricingRules.addEventListener(
                'click',
                closeImportPricingRulesModal
            );


            btnCancelImportPricingRules.addEventListener(
                'click',
                closeImportPricingRulesModal
            );


            /*
             * ==========================================================
             * CLOSE KETIKA KLIK BACKDROP
             * ==========================================================
             */

            modalImportPricingRules.addEventListener('click', function(event) {

                if (event.target === modalImportPricingRules) {

                    closeImportPricingRulesModal();

                }

            });


            /*
             * ==========================================================
             * CLOSE DENGAN ESCAPE
             * ==========================================================
             */

            document.addEventListener('keydown', function(event) {

                if (
                    event.key === 'Escape' &&
                    modalImportPricingRules.classList.contains('is-open')
                ) {

                    closeImportPricingRulesModal();

                }

            });


            /*
             * ==========================================================
             * FILE CHANGE
             * ==========================================================
             */

            fileImportPricingRules.addEventListener('change', function() {

                if (this.files.length > 0) {

                    fileSelectedNamePricingRules.textContent =
                        this.files[0].name;

                    fileSelectedNamePricingRules.classList.add('has-file');

                } else {

                    fileSelectedNamePricingRules.textContent =
                        'Belum ada file dipilih';

                    fileSelectedNamePricingRules.classList.remove('has-file');

                }

            });


            /*
             * ==========================================================
             * IMPORT DATA
             * ==========================================================
             */

            btnSaveImportPricingRules.addEventListener(
                'click',
                async function() {

                    /*
                     * Pastikan file sudah dipilih
                     */

                    if (fileImportPricingRules.files.length === 0) {

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
                        btnSaveImportPricingRules.innerHTML;


                    /*
                     * Loading state
                     */

                    btnSaveImportPricingRules.disabled = true;

                    btnSaveImportPricingRules.innerHTML = `
                <span>Memproses...</span>
            `;


                    try {

                        const formData = new FormData();

                        formData.append(
                            'file',
                            fileImportPricingRules.files[0]
                        );

                        formData.append(
                            'pricing_engine',
                            $('#pricingRuleEngineFilter').val() ||
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
                            "{{ route('admin_import_pricing_rules') }}", {
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
                                'Import data gagal.'
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

                        fileImportPricingRules.value = '';

                        fileSelectedNamePricingRules.textContent =
                            'Belum ada file dipilih';

                        fileSelectedNamePricingRules.classList.remove(
                            'has-file'
                        );


                        /*
                         * Tutup modal
                         */

                        closeImportPricingRulesModal();


                        /*
                         * Reload DataTable
                         */

                        if (
                            typeof pricingRuleTable !== 'undefined' &&
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
                            'Import Pricing Rules Error:',
                            error
                        );


                        /*
                         * Error toast
                         */

                        showToast(
                            'error',
                            error.message ||
                            'Terjadi kesalahan saat import data.'
                        );

                    } finally {

                        /*
                         * Kembalikan tombol
                         */

                        btnSaveImportPricingRules.disabled = false;

                        btnSaveImportPricingRules.innerHTML =
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

            function priceTypeLabel(priceType) {
                return priceType === 'general' ?
                    'Harga Umum' :
                    'Harga Divisi';
            }

            function printTypeLabel(printType) {
                return printType === 'single' ?
                    '1 Muka' :
                    '2 Muka';
            }

            function formatRupiah(value) {
                let rawValue = String(value ?? '').trim();

                if (!rawValue) {
                    return '';
                }

                const hasRupiahPrefix =
                    /^rp\s*/i.test(rawValue);

                rawValue =
                    rawValue.replace(/^rp\s*/i, '').trim();

                let numericValue;

                if (hasRupiahPrefix) {
                    numericValue = Number(
                        rawValue
                        .replace(/\./g, '')
                        .replace(',', '.')
                    );
                } else if (/^\d+\.\d{1,2}$/.test(rawValue)) {
                    numericValue = Number(rawValue);
                } else {
                    numericValue = Number(
                        rawValue
                        .replace(/\./g, '')
                        .replace(',', '.')
                    );
                }

                if (!Number.isFinite(numericValue)) {
                    return '';
                }

                return 'Rp ' + numericValue.toLocaleString('id-ID', {
                    maximumFractionDigits: 2
                });
            }

            function unformatRupiah(value) {
                let rawValue =
                    String(value ?? '')
                    .replace(/^rp\s*/i, '')
                    .trim();

                if (!rawValue) {
                    return '';
                }

                if (rawValue.includes(',')) {
                    return rawValue
                        .replace(/\./g, '')
                        .replace(',', '.');
                }

                if (/^\d+\.\d{1,2}$/.test(rawValue)) {
                    return rawValue;
                }

                return rawValue.replace(/\./g, '');
            }

            function createDigitalEditModal() {

                document.body.insertAdjacentHTML('beforeend', `
                    <div class="modal-overlay" id="modalEditDigitalMarkup">
                        <div class="modal-dialog modal-dialog--wide">
                            <form id="formEditDigitalMarkup" novalidate>
                                <div class="modal-header">
                                    <div>
                                        <span class="eyebrow">MARKUP HARGA · DIGITAL PRINT</span>
                                        <br>
                                        <span class="eyebrow">Edit Markup Digital Print A3+</span>
                                    </div>
                                    <button type="button" class="modal-close" data-close-digital-edit
                                        aria-label="Tutup">
                                        <svg viewBox="0 0 24 24">
                                            <path d="M18 6 6 18" />
                                            <path d="m6 6 12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="markup-row" style="margin-bottom: 20px;">
                                        <div class="markup-row-top" id="digitalEditHeader"></div>
                                    </div>
                                    <div id="digitalEditRules"
                                        style="display: flex; flex-direction: column; gap: 20px;"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn--ghost" data-close-digital-edit>
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

                return document.getElementById('modalEditDigitalMarkup');
            }

            const modal =
                createDigitalEditModal();

            const form =
                document.getElementById('formEditDigitalMarkup');

            const header =
                document.getElementById('digitalEditHeader');

            const rulesContainer =
                document.getElementById('digitalEditRules');

            function closeModal() {
                modal.classList.remove('is-open');
                form.reset();
                form.action = '';
                header.innerHTML = '';
                rulesContainer.innerHTML = '';
            }

            function renderHeader(rule) {
                const fields = [
                    ['Mesin', rule.engine_name],
                    ['Lokasi', rule.location_name],
                    ['Vendor', rule.vendor_name || '-'],
                    ['Kategori', rule.category_name]
                ];

                header.innerHTML = fields.map(function([label, value]) {
                    return `
                        <div class="markup-field">
                            <label class="category-label">${label}</label>
                            <div class="input">${value || '-'}</div>
                        </div>
                    `;
                }).join('');
            }

            function renderRuleGroup(index, priceType, printType, rules) {
                const groupRules = rules.filter(function(rule) {
                    return rule.price_type === priceType &&
                        rule.print_type === printType;
                });

                if (!groupRules.length) {
                    return '';
                }

                const byTier = groupRules.reduce(function(result, rule) {
                    result[rule.quantity_tier] = rule;
                    return result;
                }, {});

                const firstRule = groupRules[0];

                return `
                    <section style="
                        border: 1px solid var(--border);
                        border-radius: 10px;
                        overflow: hidden;
                        background: var(--bg-card);
                    ">
                        <div style="
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            gap: 12px;
                            padding: 12px 16px;
                            background: var(--bg-muted);
                            border-bottom: 1px solid var(--border);
                        ">
                            <strong>${priceTypeLabel(priceType)}</strong>
                            <span class="badge">${printTypeLabel(printType)}</span>
                        </div>
                        <div style="padding: 16px;">
                            <input type="hidden" name="pricing_rules[${index}][detail_id]"
                                value="${firstRule.detail_id}">
                            <input type="hidden" name="pricing_rules[${index}][price_type]"
                                value="${priceType}">
                            <input type="hidden" name="pricing_rules[${index}][print_type]"
                                value="${printType}">
                            <div class="markup-field" style="max-width: 220px; margin-bottom: 16px;">
                                <label class="category-label">Status</label>
                                <select name="pricing_rules[${index}][status]" class="select" required>
                                    <option value="Active"
                                        ${firstRule.status === 'Active' ? 'selected' : ''}>Active</option>
                                    <option value="Inactive"
                                        ${firstRule.status === 'Inactive' ? 'selected' : ''}>Inactive</option>
                                </select>
                            </div>
                            <div style="
                                display: grid;
                                grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
                                gap: 12px;
                            ">
                                ${tierLabels.map(function([tier, label]) {
                                    const tierRule = byTier[tier];
                                    return `
                                            <div style="
                                                min-width: 0;
                                                padding: 12px;
                                                border: 1px solid var(--border);
                                                border-radius: 10px;
                                            ">
                                                <label class="category-label">${label}</label>
                                                <div style="
                                                    display: grid;
                                                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                                                    gap: 12px;
                                                    margin-top: 10px;
                                                ">
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
                                                            class="input digital-edit-rounding"
                                                            value="${tierRule && tierRule.rounding_value !== null ?
                                                                formatRupiah(tierRule.rounding_value) : ''}"
                                                            inputmode="numeric"
                                                            autocomplete="off">
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
                    event.target.closest('.btn-edit-digital-pricing-rule');

                if (!editButton) {
                    return;
                }

                const digitalPricingRuleId =
                    editButton.dataset.digitalPricingRuleId;

                if (!digitalPricingRuleId) {
                    showToast('error', 'Data markup Digital Print tidak ditemukan.');
                    return;
                }

                header.innerHTML = '<div class="input">Memuat data...</div>';
                rulesContainer.innerHTML = '';
                modal.classList.add('is-open');

                fetch(
                        `{{ url('/admin/pricing-rules/digital') }}/${digitalPricingRuleId}/details`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }
                    )
                    .then(async function(response) {
                        const data = await response.json();

                        if (!response.ok || !data.success || !data.data.length) {
                            throw new Error(
                                data.message ||
                                'Data markup Digital Print gagal dimuat.'
                            );
                        }

                        return data.data;
                    })
                    .then(function(rules) {
                        renderHeader(rules[0]);

                        rulesContainer.innerHTML = ruleGroups.map(
                            function([priceType, printType], index) {
                                return renderRuleGroup(
                                    index,
                                    priceType,
                                    printType,
                                    rules
                                );
                            }
                        ).join('');

                        form.action =
                            `{{ url('/admin/pricing-rules/digital') }}/${digitalPricingRuleId}`;
                    })
                    .catch(function(error) {
                        closeModal();
                        showToast(
                            'error',
                            error.message ||
                            'Terjadi kesalahan saat memuat markup Digital Print.'
                        );
                    });
            });

            form.addEventListener('submit', async function(event) {
                event.preventDefault();

                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }

                const submitButton =
                    form.querySelector('button[type="submit"]');

                const originalButtonHtml =
                    submitButton.innerHTML;

                submitButton.disabled = true;
                submitButton.innerHTML = 'Menyimpan...';

                try {
                    const formData =
                        new FormData(form);

                    form.querySelectorAll('.digital-edit-rounding')
                        .forEach(function(input) {
                            formData.set(
                                input.name,
                                unformatRupiah(input.value)
                            );
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
                        throw new Error(
                            data.message ||
                            'Markup Digital Print gagal diperbarui.'
                        );
                    }

                    closeModal();
                    showToast('success', data.message);
                    pricingRuleTable.ajax.reload(null, false);
                } catch (error) {
                    showToast(
                        'error',
                        error.message ||
                        'Terjadi kesalahan saat memperbarui markup Digital Print.'
                    );
                } finally {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonHtml;
                }
            });

            form.addEventListener('input', function(event) {
                if (event.target.matches('.digital-edit-rounding')) {
                    event.target.value =
                        formatRupiah(event.target.value);
                }
            });

            modal.addEventListener('click', function(event) {
                if (
                    event.target === modal ||
                    event.target.closest('[data-close-digital-edit]')
                ) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                    closeModal();
                }
            });
        });

        $(document).on('wheel', 'input[type="number"]', function() {
            this.blur();
        });
    </script>
@endsection

@extends('admin_master')

@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">HARGA MARKUP · DISPLAY</span>
                        {{-- <h1 class="hero-title">Daftar Material</h1> --}}
                        <p class="hero-sub">Kelola dan pantau data rangka Display yang digunakan dalam proses produksi dan
                            perhitungan biaya.</p>
                    </div>
                    <div class="hero-actions"><a href="{{ route('admin_export_display_pricing_rules') }}"
                            class="btn btn--ghost"><svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg> Export</a>
                        <a href="{{ asset('templates/template_markup_display.xlsx') }}" class="btn btn--ghost">

                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Download Template

                        </a>
                        <button class="btn btn--ghost" id="btnImportDisplayPricingRules"><svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg> Import</button>

                        <button type="button" class="btn btn--primary" id="btnTambahDisplayMarkup">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Markup
                        </button>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="displayPricingRuleTable" class="data-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Mesin</th>
                                    <th>Lokasi</th>
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

    <!--
                                                                            |--------------------------------------------------------------------------
                                                                            | MODAL TAMBAH MARKUP DISPLAY
                                                                            |--------------------------------------------------------------------------
                                                                            -->

    <div class="modal-overlay" id="modalTambahDisplayMarkup">
        <div class="modal-dialog modal-dialog--wide">

            <form method="POST" action="{{ route('admin_store_display_pricing_rule') }}" id="formTambahDisplayMarkup"
                novalidate>

                @csrf

                <!-- HEADER MODAL -->
                <div class="modal-header">
                    <div>
                        <span class="eyebrow">
                            HARGA MARKUP · DISPLAY
                        </span>
                        <br>
                        <span class="eyebrow">
                            Tambah Markup Harga
                        </span>
                    </div>

                    <button type="button" class="modal-close" id="btnTutupModalTambahDisplayMarkup" aria-label="Tutup">
                        <svg viewBox="0 0 24 24">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <!-- BODY MODAL -->
                <div class="modal-body">

                    <!-- FORM HEADER -->
                    <div class="category-form-header">
                        <div>
                            <label class="category-label">
                                Konfigurasi Markup Display
                            </label>

                            <p class="category-description">
                                Tambahkan konfigurasi markup Display
                                berdasarkan mesin, lokasi, dan kategori.
                                Setiap header memiliki aturan markup
                                untuk masing-masing tipe harga.
                            </p>
                        </div>

                        <button type="button" class="btn btn--ghost" id="btnTambahHeaderDisplayMarkup">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14" />
                                <path d="M5 12h14" />
                            </svg>

                            Tambah Header
                        </button>
                    </div>

                    <!-- HEADER CONTAINER -->
                    <div id="displayMarkupHeaderContainer">

                        <!-- HEADER GROUP -->
                        <div class="markup-header-group" data-header-index="0">

                            <!-- HEADER ROW -->
                            <div class="markup-row markup-header-row">

                                <div class="markup-row-top">

                                    <!-- MESIN -->
                                    <div class="markup-field">
                                        <label class="category-label">
                                            Mesin
                                            <span class="req">*</span>
                                        </label>

                                        <select name="headers[0][engine_id]" class="select select2 markup-engine" required>
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
                                            <span class="req">*</span>
                                        </label>

                                        <select name="headers[0][location_id]" class="select select2 markup-location"
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

                                    <!-- KATEGORI -->
                                    <div class="markup-field">
                                        <label class="category-label">
                                            Kategori
                                            <span class="req">*</span>
                                        </label>

                                        <select name="headers[0][category_id]" class="select select2 markup-category"
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

                                    <!-- HEADER ACTION -->
                                    <div
                                        style="
                                        display:flex;
                                        justify-content:flex-end;
                                        align-items:center;
                                        gap:10px;
                                    ">
                                        <div class="markup-header-action">
                                            <i class="bi bi-trash3-fill btn-remove-header"
                                                style="
                                                cursor:pointer;
                                                border:none !important;
                                            "
                                                aria-label="Hapus Header"></i>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- HEADER BODY / CHILD -->
                            <div class="markup-header-body">

                                <!-- CHILD HEADER -->
                                <div class="markup-body-header">

                                    <div>
                                        <label class="category-label">
                                            Aturan Markup
                                        </label>

                                        <p class="category-description">
                                            Tentukan nilai markup,
                                            pembulatan, dan status
                                            untuk setiap tipe harga Display.
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

                                <!-- CHILD MARKUP CONTAINER -->
                                <div class="markup-rules-container">

                                    <!-- CHILD : HARGA UMUM -->
                                    <div class="markup-row markup-rule-row" data-rule-index="0">

                                        <!-- TIPE HARGA -->
                                        <div class="markup-field">
                                            <label class="category-label">
                                                Tipe Harga
                                            </label>

                                            <select name="headers[0][pricing_rules][0][price_type]"
                                                class="select select2 markup-price-type" required>
                                                <option value="">Pilih Tipe Harga</option>
                                                <option value="general">Harga Umum</option>
                                                <option value="division">Harga Divisi</option>
                                                <option value="plain">Harga Polos</option>
                                            </select>
                                        </div>

                                        <!-- MARKUP -->
                                        <div class="markup-field">
                                            <label class="category-label">
                                                Markup (%)
                                            </label>

                                            <input type="number" name="headers[0][pricing_rules][0][markup_percentage]"
                                                class="input markup-percentage" min="0" step="0.01"
                                                placeholder="Contoh: 10" required>
                                        </div>

                                        <!-- PEMBULATAN -->
                                        <div class="markup-field">
                                            <label class="category-label">
                                                Pembulatan (Rp)
                                            </label>

                                            <input type="text" name="headers[0][pricing_rules][0][rounding_value]"
                                                class="input markup-rounding" inputmode="numeric" autocomplete="off"
                                                placeholder="Contoh: Rp 1.000" required>
                                        </div>

                                        <!-- STATUS -->
                                        <div class="markup-field">
                                            <label class="category-label">
                                                Status
                                            </label>

                                            <select name="headers[0][pricing_rules][0][status]"
                                                class="select select2 markup-status" required>
                                                <option value="Active">
                                                    Active
                                                </option>

                                                <option value="Inactive">
                                                    Inactive
                                                </option>
                                            </select>
                                        </div>

                                        <!-- ACTION -->
                                        <div class="markup-row-action">
                                            <button type="button" class="btn-remove-markup" aria-label="Hapus markup">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </div>

                                    </div>

                                    <!-- CHILD : HARGA DIVISI -->
                                    <div class="markup-row markup-rule-row" data-rule-index="1">

                                        <!-- TIPE HARGA -->
                                        <div class="markup-field">
                                            <label class="category-label">
                                                Tipe Harga
                                            </label>

                                            <input type="text" class="input markup-price-type-label"
                                                value="Harga Divisi" readonly>

                                            <input type="hidden" name="headers[0][pricing_rules][1][price_type]"
                                                class="markup-price-type" value="division">
                                        </div>

                                        <!-- MARKUP -->
                                        <div class="markup-field">
                                            <label class="category-label">
                                                Markup (%)
                                            </label>

                                            <input type="number" name="headers[0][pricing_rules][1][markup_percentage]"
                                                class="input markup-percentage" min="0" step="0.01"
                                                placeholder="Contoh: 10" required>
                                        </div>

                                        <!-- PEMBULATAN -->
                                        <div class="markup-field">
                                            <label class="category-label">
                                                Pembulatan (Rp)
                                            </label>

                                            <input type="text" name="headers[0][pricing_rules][1][rounding_value]"
                                                class="input markup-rounding" inputmode="numeric" autocomplete="off"
                                                placeholder="Contoh: Rp 1.000" required>
                                        </div>

                                        <!-- STATUS -->
                                        <div class="markup-field">
                                            <label class="category-label">
                                                Status
                                            </label>

                                            <select name="headers[0][pricing_rules][1][status]"
                                                class="select select2 markup-status" required>
                                                <option value="Active">
                                                    Active
                                                </option>

                                                <option value="Inactive">
                                                    Inactive
                                                </option>
                                            </select>
                                        </div>

                                        <!-- ACTION -->
                                        <div class="markup-row-action">
                                            <button type="button" class="btn-remove-markup" aria-label="Hapus markup">
                                                <i class="bi bi-trash3-fill"></i>
                                            </button>
                                        </div>

                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- FOOTER -->
                <div class="modal-footer">

                    <button type="button" class="btn btn--ghost" id="btnBatalTambahDisplayMarkup">
                        Batal
                    </button>

                    <button type="submit" class="btn btn--primary" id="btnSimpanDisplayMarkup">
                        Simpan Markup
                    </button>

                </div>

            </form>
        </div>
    </div>

    <!-- MODAL DELETE DISPLAY PRICING RULE -->
    <div class="modal-overlay" id="modalDeleteDisplayPricingRule">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MARKUP DISPLAY · RANGKA
                    </span>

                    <br>

                    <span class="eyebrow">
                        Hapus Markup Display
                    </span>

                </div>

                <button type="button" class="modal-close" id="btnTutupDeleteDisplayPricingRule" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
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
                            Hapus markup Display?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus seluruh
                            konfigurasi markup Display untuk mesin, lokasi,
                            dan kategori ini? Data yang sudah dihapus
                            tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteDisplayPricingRule">
                    Batal
                </button>

                <button type="button" class="btn btn--danger" id="btnConfirmDeleteDisplayPricingRule">
                    <i class="bi bi-trash3-fill"></i>
                    Hapus
                </button>

            </div>

        </div>

    </div>

    <!-- MODAL DETAIL DISPLAY PRICING RULE -->
    <div class="modal-overlay" id="modalDetailDisplayPricingRule">

        <div class="modal-dialog modal-dialog--wide">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MARKUP DISPLAY · RANGKA
                    </span>

                    <br>

                    <span class="eyebrow">
                        Detail Markup Display
                    </span>

                </div>

                <button type="button" class="modal-close" id="btnTutupModalDetailDisplayPricingRule"
                    aria-label="Tutup">
                    <svg viewBox="0 0 24 24">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>

            </div>


            <!-- BODY -->
            <div class="modal-body">

                <div class="category-form-header">

                    <div>

                        <label class="category-label">
                            Detail Markup Display
                        </label>

                        <p class="category-description">
                            Informasi aturan markup Rangka berdasarkan mesin,
                            lokasi, dan kategori.
                        </p>

                    </div>

                </div>


                <!-- =====================================================
                                                                                     ENGINE, LOCATION & CATEGORY
                                                                                ====================================================== -->

                <div class="markup-row" style="margin-bottom: 20px;">

                    <div class="markup-row-top">

                        <!-- MESIN -->
                        <div class="markup-field">

                            <label class="category-label">
                                Mesin
                            </label>

                            <div class="input" id="detailDisplayMarkupEngine">
                                -
                            </div>

                        </div>


                        <!-- LOKASI -->
                        <div class="markup-field">

                            <label class="category-label">
                                Lokasi
                            </label>

                            <div class="input" id="detailDisplayMarkupLocation">
                                -
                            </div>

                        </div>


                        <!-- KATEGORI -->
                        <div class="markup-field">

                            <label class="category-label">
                                Kategori
                            </label>

                            <div class="input" id="detailDisplayMarkupCategory">
                                -
                            </div>

                        </div>

                    </div>

                </div>


                <!-- =====================================================
                                                                                     DETAIL CONTENT
                                                                                ====================================================== -->

                <div id="detailDisplayMarkupContent"
                    style="
                    margin-top: 20px;
                    display: flex;
                    flex-direction: column;
                    gap: 20px;
                ">
                    <!-- Konten detail akan diisi melalui JavaScript -->
                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDetailDisplayPricingRule">
                    Tutup
                </button>

            </div>

        </div>

    </div>

    <div class="modal-overlay" id="modalEditDisplayPricingRule">

        <div class="modal-dialog modal-dialog--wide">

            <form method="POST" id="formEditDisplayPricingRule" novalidate>

                @csrf
                @method('PUT')

                {{-- ================================================= --}}
                {{-- MODAL HEADER --}}
                {{-- ================================================= --}}

                <div class="modal-header">

                    <div>

                        <span class="eyebrow">
                            MARKUP DISPLAY · RANGKA
                        </span>

                        <br>

                        <span class="eyebrow">
                            Edit Markup Display
                        </span>

                    </div>

                    <button type="button" class="modal-close" id="btnTutupModalEditDisplayPricingRule"
                        aria-label="Tutup">
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

                    <div class="category-form-header">

                        <div>

                            <label class="category-label">
                                Data Markup Display
                            </label>

                            <p class="category-description">
                                Edit aturan markup Rangka berdasarkan
                                mesin, lokasi, dan kategori.
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

                                <select id="editDisplayMarkupEngine" class="select select2" disabled>
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

                                <select id="editDisplayMarkupLocation" class="select select2" disabled>
                                    <option value="">
                                        Memuat lokasi...
                                    </option>
                                </select>

                            </div>


                            {{-- KATEGORI --}}

                            <div class="markup-field">

                                <label class="category-label">
                                    Kategori
                                </label>

                                <select id="editDisplayMarkupCategory" class="select select2" disabled>
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
                        style="
                        margin-top: 20px;
                        margin-bottom: 20px;
                        display: flex;
                        justify-content: right;
                        align-items: center;
                        gap: 20px;
                    ">

                        <button type="button" class="btn btn--ghost" id="btnTambahEditDisplayMarkup">
                            <i class="bi bi-plus-lg"></i>
                            Tambah Markup
                        </button>

                    </div>


                    {{-- ================================================= --}}
                    {{-- MARKUP ROWS --}}
                    {{-- ================================================= --}}

                    <div id="editDisplayMarkupRows" class="mt-3"
                        style="
                        display: flex;
                        flex-direction: column;
                        gap: 20px;
                    ">
                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- MODAL FOOTER --}}
                {{-- ================================================= --}}

                <div class="modal-footer">

                    <button type="button" class="btn btn--ghost" id="btnBatalEditDisplayPricingRule">
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

    <div class="modal-overlay" id="modalImportDisplayPricingRules">

        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">

                <div>
                    <span class="eyebrow">MARKUP DISPLAY · RANGKA</span>
                    <br>
                    <span class="eyebrow">Import Markup Display</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseImportDisplayPricingRules" aria-label="Tutup">
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
                        Pilih file Excel yang berisi data markup Display
                        untuk diimport ke dalam sistem.
                    </p>
                </div>


                <!-- Custom File Input -->
                <label for="fileImportDisplayPricingRules" class="file-upload-box" id="fileUploadBoxDisplayPricingRules">

                    <input type="file" id="fileImportDisplayPricingRules" name="file" accept=".xlsx,.xls" hidden>


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


                    <div class="file-selected-name" id="fileSelectedNameDisplayPricingRules">
                        Belum ada file dipilih
                    </div>

                </label>

            </div>


            <!-- Footer -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCancelImportDisplayPricingRules">
                    Batal
                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportDisplayPricingRules">

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
@endsection

@section('scripts')
    <script>
        let displayPricingRuleTable;

        $(document).ready(function() {

            displayPricingRuleTable = $('#displayPricingRuleTable').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                responsive: {
                    details: {
                        type: 'column',
                        target: 0
                    }
                },

                ajax: {
                    url: "{{ route('admin_data_display_pricing_rule') }}",
                    type: 'GET'
                },

                columns: [

                    // Responsive control
                    {
                        data: null,
                        defaultContent: '',
                        className: 'control',
                        orderable: false,
                        searchable: false
                    },

                    // No
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },

                    // Mesin
                    {
                        data: 'engine_name',
                        name: 'engine_name',
                        defaultContent: '-',
                        render: function(data) {
                            return data || '-';
                        }
                    },

                    // Lokasi
                    {
                        data: 'location_name',
                        name: 'location_name',
                        defaultContent: '-',
                        render: function(data) {
                            return data || '-';
                        }
                    },

                    // Kategori
                    {
                        data: 'category_name',
                        name: 'category_name',
                        defaultContent: '-',
                        render: function(data) {
                            return data || '-';
                        }
                    },

                    // Jumlah Markup
                    {
                        data: 'jumlah_markup',
                        name: 'jumlah_markup',
                        className: 'text-center',
                        render: function(data) {
                            return Number(data || 0).toLocaleString('id-ID');
                        },
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).css('text-align', 'center');
                        }
                    },

                    // Aksi
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {

                            return `
                        <div class="table-actions">

                            <button
                                type="button"
                                class="btn--icon btn-detail-display-pricing-rule"
                                data-engine-id="${row.engine_id}"
                                data-location-id="${row.location_id}"
                                data-category-id="${row.category_id}"
                                aria-label="Detail"
                            >
                                <i class="bi bi-arrows-fullscreen"></i>
                            </button>

                            <button
                                type="button"
                                class="btn--icon btn-edit-display-pricing-rule"
                                data-engine-id="${row.engine_id}"
                                data-location-id="${row.location_id}"
                                data-category-id="${row.category_id}"
                                aria-label="Edit"
                            >
                                <i class="bi bi-pen"></i>
                            </button>

                            <button
                                type="button"
                                class="btn--icon btn-delete-display-pricing-rule"
                                data-engine-id="${row.engine_id}"
                                data-location-id="${row.location_id}"
                                data-category-id="${row.category_id}"
                                aria-label="Hapus"
                            >
                                <i class="bi bi-trash3-fill"></i>
                            </button>

                        </div>
                    `;
                        }
                    }

                ],

                pageLength: 10,

                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ],

                layout: {
                    topStart: 'pageLength',
                    topEnd: 'search',
                    bottomStart: 'info',
                    bottomEnd: 'paging'
                },

                language: {
                    search: 'Search:',
                    lengthMenu: '_MENU_ entries per page',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    zeroRecords: 'Data markup display tidak ditemukan',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari mesin, lokasi atau kategori...'
                },

                initComplete: function() {

                    const searchInput = $('#displayPricingRuleTable_filter input');

                    searchInput.css({
                        'padding': '9px 14px',
                        'border-radius': '8px',
                        'border': '1px solid #e2e8f0'
                    });
                }
            });


            /*
            |--------------------------------------------------------------------------
            | DETAIL
            |--------------------------------------------------------------------------
            */

            $(document).on(
                'click',
                '.btn-detail-display-pricing-rule',
                function(event) {

                    event.preventDefault();

                    const engineId = $(this).data('engine-id');
                    const locationId = $(this).data('location-id');
                    const categoryId = $(this).data('category-id');

                    if (!engineId || !locationId || !categoryId) {
                        return;
                    }

                    /*
                     * Detail/Edit/Delete menggunakan kombinasi:
                     * engine_id
                     * location_id
                     * category_id
                     */

                    // Sesuaikan route detail jika route sudah tersedia.
                    console.log(
                        'Detail Display Pricing Rule:', {
                            engineId,
                            locationId,
                            categoryId
                        }
                    );
                }
            );


            /*
            |--------------------------------------------------------------------------
            | EDIT
            |--------------------------------------------------------------------------
            */

            $(document).on(
                'click',
                '.btn-edit-display-pricing-rule',
                function(event) {

                    event.preventDefault();

                    const engineId = $(this).data('engine-id');
                    const locationId = $(this).data('location-id');
                    const categoryId = $(this).data('category-id');

                    if (!engineId || !locationId || !categoryId) {
                        return;
                    }

                    /*
                     * Untuk sementara hanya membawa identifier
                     * kombinasi header.
                     *
                     * Jangan menggunakan row.id karena DataTable sekarang
                     * menampilkan 1 row untuk 1 kombinasi header.
                     */

                    console.log(
                        'Edit Display Pricing Rule:', {
                            engineId,
                            locationId,
                            categoryId
                        }
                    );
                }
            );

        });

        document.addEventListener('DOMContentLoaded', function() {

            const modalTambahDisplayMarkup =
                document.getElementById('modalTambahDisplayMarkup');

            const formTambahDisplayMarkup =
                document.getElementById('formTambahDisplayMarkup');

            const btnTambahDisplayMarkup =
                document.getElementById('btnTambahDisplayMarkup');

            const btnTutupModalTambahDisplayMarkup =
                document.getElementById('btnTutupModalTambahDisplayMarkup');

            const btnBatalTambahDisplayMarkup =
                document.getElementById('btnBatalTambahDisplayMarkup');

            const btnTambahHeaderDisplayMarkup =
                document.getElementById('btnTambahHeaderDisplayMarkup');

            const displayMarkupHeaderContainer =
                document.getElementById('displayMarkupHeaderContainer');

            const btnSimpanDisplayMarkup =
                document.getElementById('btnSimpanDisplayMarkup');


            /*
            |--------------------------------------------------------------------------
            | INDEX
            |--------------------------------------------------------------------------
            */

            let headerIndex = 1;
            let ruleIndex = 2;


            /*
            |--------------------------------------------------------------------------
            | FORMAT RUPIAH
            |--------------------------------------------------------------------------
            */

            function formatRupiah(value) {

                if (value === null || value === undefined) {
                    return '';
                }

                value = String(value).replace(/\D/g, '');

                if (value === '') {
                    return '';
                }

                return 'Rp ' + value.replace(
                    /\B(?=(\d{3})+(?!\d))/g,
                    '.'
                );
            }


            function unformatRupiah(value) {

                if (value === null || value === undefined) {
                    return '';
                }

                return String(value).replace(/\D/g, '');
            }


            /*
            |--------------------------------------------------------------------------
            | GET HEADER / CHILD
            |--------------------------------------------------------------------------
            */

            function getHeaderGroups() {

                return displayMarkupHeaderContainer.querySelectorAll(
                    '.markup-header-group'
                );
            }


            function getRuleRows(header) {

                return header.querySelectorAll(
                    '.markup-rule-row'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | SELECT2
            |--------------------------------------------------------------------------
            */

            function initSelect2(element) {

                if (!element) {
                    return;
                }

                if ($(element).hasClass('select2-hidden-accessible')) {
                    return;
                }

                $(element).select2({
                    width: '100%',
                    allowClear: false,
                    dropdownParent: $('#modalTambahDisplayMarkup')
                });
            }


            function initHeaderSelect2(header) {

                if (!header) {
                    return;
                }

                initSelect2(
                    header.querySelector('.markup-engine')
                );

                initSelect2(
                    header.querySelector('.markup-location')
                );

                initSelect2(
                    header.querySelector('.markup-category')
                );
            }


            function initMarkupRowSelect2(row) {

                if (!row) {
                    return;
                }

                /*
                 * Tipe Harga
                 */
                initSelect2(
                    row.querySelector('.markup-price-type')
                );

                /*
                 * Status
                 */
                initSelect2(
                    row.querySelector('.markup-status')
                );
            }


            function destroySelect2(element) {

                if (!element) {
                    return;
                }

                if ($(element).hasClass('select2-hidden-accessible')) {
                    $(element).select2('destroy');
                }
            }


            function destroyHeaderSelect2(header) {

                if (!header) {
                    return;
                }

                destroySelect2(
                    header.querySelector('.markup-engine')
                );

                destroySelect2(
                    header.querySelector('.markup-location')
                );

                destroySelect2(
                    header.querySelector('.markup-category')
                );
            }


            function destroyMarkupRowSelect2(row) {

                if (!row) {
                    return;
                }

                destroySelect2(
                    row.querySelector('.markup-price-type')
                );

                destroySelect2(
                    row.querySelector('.markup-status')
                );
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

                field.classList.remove('is-invalid');

                const fieldWrapper =
                    field.closest('.markup-field');

                if (fieldWrapper) {
                    fieldWrapper.classList.remove('has-error');
                }

                /*
                 * Select2
                 */
                if ($(field).hasClass('select2-hidden-accessible')) {

                    $(field)
                        .next('.select2')
                        .removeClass('is-invalid');

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

                field.classList.add('is-invalid');

                const fieldWrapper =
                    field.closest('.markup-field');

                if (fieldWrapper) {
                    fieldWrapper.classList.add('has-error');
                }

                /*
                 * Select2
                 */
                if ($(field).hasClass('select2-hidden-accessible')) {

                    $(field)
                        .next('.select2')
                        .addClass('is-invalid');

                    $(field)
                        .next('.select2')
                        .find('.select2-selection')
                        .addClass('is-invalid');
                }
            }


            function clearAllValidation() {

                formTambahDisplayMarkup
                    .querySelectorAll('.is-invalid')
                    .forEach(function(element) {

                        element.classList.remove(
                            'is-invalid'
                        );
                    });

                formTambahDisplayMarkup
                    .querySelectorAll('.has-error')
                    .forEach(function(element) {

                        element.classList.remove(
                            'has-error'
                        );
                    });

                formTambahDisplayMarkup
                    .querySelectorAll('.select2-selection.is-invalid')
                    .forEach(function(element) {

                        element.classList.remove(
                            'is-invalid'
                        );
                    });
            }


            /*
            |--------------------------------------------------------------------------
            | INIT MARKUP ROW
            |--------------------------------------------------------------------------
            */

            function initMarkupRow(row) {

                if (!row) {
                    return;
                }

                const priceType =
                    row.querySelector('.markup-price-type');

                const markup =
                    row.querySelector('.markup-percentage');

                const rounding =
                    row.querySelector('.markup-rounding');

                const status =
                    row.querySelector('.markup-status');


                /*
                 * Initialize Select2
                 *
                 * Tipe Harga + Status
                 */
                initMarkupRowSelect2(row);


                /*
                 * Tipe Harga
                 */
                if (priceType) {

                    $(priceType)
                        .off('change.displayMarkupValidation')
                        .on(
                            'change.displayMarkupValidation',
                            function() {

                                clearFieldError(priceType);
                            }
                        );
                }


                /*
                 * Markup
                 */
                if (markup) {

                    if (markup.dataset.initialized !== 'true') {

                        markup.addEventListener(
                            'input',
                            function() {

                                clearFieldError(markup);
                            }
                        );

                        markup.dataset.initialized =
                            'true';
                    }
                }


                /*
                 * Pembulatan
                 */
                if (rounding) {

                    if (rounding.dataset.initialized !== 'true') {

                        rounding.addEventListener(
                            'input',
                            function() {

                                const value =
                                    unformatRupiah(
                                        rounding.value
                                    );

                                rounding.value =
                                    formatRupiah(value);

                                clearFieldError(rounding);
                            }
                        );

                        rounding.dataset.initialized =
                            'true';
                    }
                }


                /*
                 * Status
                 */
                if (status) {

                    $(status)
                        .off('change.displayMarkupValidation')
                        .on(
                            'change.displayMarkupValidation',
                            function() {

                                clearFieldError(status);
                            }
                        );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE REMOVE BUTTON CHILD
            |--------------------------------------------------------------------------
            */

            function updateRemoveMarkupButtons(header) {

                if (!header) {
                    return;
                }

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

                    if (rows.length <= 1) {
                        button.style.display = 'none';
                    } else {
                        button.style.display = '';
                    }
                });
            }


            /*
            |--------------------------------------------------------------------------
            | CREATE MARKUP ROW
            |--------------------------------------------------------------------------
            */

            function createMarkupRow(
                header,
                index,
                priceType = ''
            ) {

                const container =
                    header.querySelector(
                        '.markup-rules-container'
                    );

                if (!container) {
                    return null;
                }


                const row =
                    document.createElement('div');

                row.className =
                    'markup-row markup-rule-row';

                row.dataset.ruleIndex =
                    index;


                row.innerHTML = `
            <div class="markup-field">

                <label class="category-label">
                    Tipe Harga
                    <span class="req">*</span>
                </label>

                <select
                    name="headers[0][pricing_rules][${index}][price_type]"
                    class="select select2 markup-price-type"
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
                    name="headers[0][pricing_rules][${index}][markup_percentage]"
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
                    name="headers[0][pricing_rules][${index}][rounding_value]"
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
                    name="headers[0][pricing_rules][${index}][status]"
                    class="select select2 markup-status"
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


            <div class="markup-row-action">

                <button
                    type="button"
                    class="btn-remove-markup"
                    aria-label="Hapus markup"
                >
                    <i class="bi bi-trash3-fill"></i>
                </button>

            </div>
        `;


                container.appendChild(row);


                /*
                 * Set value awal jika diberikan.
                 */
                const priceTypeInput =
                    row.querySelector(
                        '.markup-price-type'
                    );

                if (
                    priceTypeInput &&
                    priceType
                ) {

                    $(priceTypeInput)
                        .val(priceType)
                        .trigger('change');
                }


                /*
                 * Initialize row
                 */
                initMarkupRow(row);


                updateRemoveMarkupButtons(
                    header
                );


                return row;
            }


            /*
            |--------------------------------------------------------------------------
            | REINDEX RULE ROW
            |--------------------------------------------------------------------------
            */

            function reindexRuleRows() {

                let currentIndex = 0;


                getHeaderGroups().forEach(
                    function(header, currentHeaderIndex) {

                        const rows =
                            getRuleRows(header);


                        rows.forEach(
                            function(row) {

                                row.dataset.ruleIndex =
                                    currentIndex;


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


                                if (priceType) {

                                    priceType.name =
                                        `headers[${currentHeaderIndex}][pricing_rules][${currentIndex}][price_type]`;
                                }


                                if (markup) {

                                    markup.name =
                                        `headers[${currentHeaderIndex}][pricing_rules][${currentIndex}][markup_percentage]`;
                                }


                                if (rounding) {

                                    rounding.name =
                                        `headers[${currentHeaderIndex}][pricing_rules][${currentIndex}][rounding_value]`;
                                }


                                if (status) {

                                    status.name =
                                        `headers[${currentHeaderIndex}][pricing_rules][${currentIndex}][status]`;
                                }


                                currentIndex++;
                            }
                        );
                    }
                );


                ruleIndex =
                    currentIndex;
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
            <div class="markup-row markup-header-row" style="display: flex; flex-direction: column; gap: 10px;">
                <div class="markup-header-action" style="display: flex; align-items: center; justify-content: right;">

                    <i
                        class="bi bi-trash3-fill btn-remove-header"
                        style="
                            cursor:pointer;
                            border:none !important;
                        "
                        aria-label="Hapus Header"
                    ></i>

                </div>
                <div class="markup-row-top">

                    <!-- MESIN -->
                    <div class="markup-field">

                        <label class="category-label">
                            Mesin
                            <span class="req">*</span>
                        </label>

                        <select
                            name="headers[${index}][engine_id]"
                            class="select select2 markup-engine"
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


                    <!-- LOKASI -->
                    <div class="markup-field">

                        <label class="category-label">
                            Lokasi
                            <span class="req">*</span>
                        </label>

                        <select
                            name="headers[${index}][location_id]"
                            class="select select2 markup-location"
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


                    <!-- KATEGORI -->
                    <div class="markup-field">

                        <label class="category-label">
                            Kategori
                            <span class="req">*</span>
                        </label>

                        <select
                            name="headers[${index}][category_id]"
                            class="select select2 markup-category"
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


                    <!-- REMOVE HEADER -->
                    <div
                        style="
                            display:flex;
                            justify-content:flex-end;
                            align-items:center;
                            gap:10px;
                        "
                    >

                    </div>

                </div>

            </div>


            <!-- CHILD -->
            <div class="markup-header-body">

                <div class="markup-body-header">

                    <div>

                        <label class="category-label">
                            Aturan Markup
                        </label>

                        <p class="category-description">
                            Tentukan nilai markup,
                            pembulatan, dan status
                            untuk setiap tipe harga Display.
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


                <div class="markup-rules-container"></div>

            </div>
        `;


                displayMarkupHeaderContainer.appendChild(
                    header
                );


                /*
                 * Initialize Header Select2
                 */
                initHeaderSelect2(header);


                /*
                 * Child awal
                 *
                 * Tipe harga tetap tersedia sebagai Select2.
                 * Nilai awal general/division masih bisa
                 * diberikan jika memang diinginkan.
                 */
                createMarkupRow(
                    header,
                    ruleIndex++,
                    'general'
                );

                createMarkupRow(
                    header,
                    ruleIndex++,
                    'division'
                );


                updateRemoveMarkupButtons(
                    header
                );


                return header;
            }


            /*
            |--------------------------------------------------------------------------
            | REINDEX HEADER
            |--------------------------------------------------------------------------
            */

            function reindexHeaderGroups() {

                getHeaderGroups().forEach(
                    function(header, index) {

                        header.dataset.headerIndex =
                            index;


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

                            engine.name =
                                `headers[${index}][engine_id]`;
                        }


                        if (location) {

                            location.name =
                                `headers[${index}][location_id]`;
                        }


                        if (category) {

                            category.name =
                                `headers[${index}][category_id]`;
                        }
                    }
                );


                headerIndex =
                    getHeaderGroups().length;
            }


            /*
            |--------------------------------------------------------------------------
            | REMOVE HEADER
            |--------------------------------------------------------------------------
            */

            function removeMarkupHeader(header) {

                const headers =
                    getHeaderGroups();


                if (headers.length <= 1) {

                    showToast(
                        'error',
                        'Minimal harus terdapat satu header.'
                    );

                    return;
                }


                /*
                 * Destroy Header Select2
                 */
                destroyHeaderSelect2(
                    header
                );


                /*
                 * Destroy Child Select2
                 */
                getRuleRows(header).forEach(
                    function(row) {

                        destroyMarkupRowSelect2(
                            row
                        );
                    }
                );


                header.remove();


                reindexHeaderGroups();
                reindexRuleRows();
            }


            /*
            |--------------------------------------------------------------------------
            | ADD MARKUP CHILD
            |--------------------------------------------------------------------------
            */

            function addMarkupRow(header) {

                /*
                 * Tidak ada pengecekan duplicate.
                 *
                 * User bebas menambah child.
                 * Duplicate price_type akan ditangani
                 * oleh backend.
                 */
                createMarkupRow(
                    header,
                    ruleIndex++
                );


                reindexRuleRows();


                updateRemoveMarkupButtons(
                    header
                );
            }


            /*
            |--------------------------------------------------------------------------
            | VALIDATE FORM
            |--------------------------------------------------------------------------
            */

            function validateForm() {

                clearAllValidation();


                const headers =
                    getHeaderGroups();


                /*
                 * HEADER
                 */
                if (headers.length === 0) {

                    showToast(
                        'error',
                        'Minimal harus terdapat satu header.'
                    );

                    return false;
                }


                /*
                 * LOOP HEADER
                 */
                for (
                    let i = 0; i < headers.length; i++
                ) {

                    const header =
                        headers[i];


                    /*
                     * MESIN
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
                            $(engine).select2('open');
                        }

                        return false;
                    }


                    /*
                     * LOKASI
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
                            $(location).select2('open');
                        }

                        return false;
                    }


                    /*
                     * KATEGORI
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
                            $(category).select2('open');
                        }

                        return false;
                    }


                    /*
                     * CHILD
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
                     * LOOP CHILD
                     */
                    for (
                        let j = 0; j < rows.length; j++
                    ) {

                        const row =
                            rows[j];


                        /*
                         * TIPE HARGA
                         */
                        const priceType =
                            row.querySelector(
                                '.markup-price-type'
                            );


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
                                $(priceType).select2('open');
                            }

                            return false;
                        }


                        /*
                         * MARKUP
                         */
                        const markup =
                            row.querySelector(
                                '.markup-percentage'
                            );


                        if (
                            !markup ||
                            markup.value === '' ||
                            isNaN(markup.value)
                        ) {

                            setFieldError(
                                markup
                            );

                            showToast(
                                'error',
                                'Markup wajib diisi dengan angka.'
                            );

                            if (markup) {
                                markup.focus();
                            }

                            return false;
                        }


                        if (
                            Number(markup.value) < 0
                        ) {

                            setFieldError(
                                markup
                            );

                            showToast(
                                'error',
                                'Markup tidak boleh bernilai negatif.'
                            );

                            markup.focus();

                            return false;
                        }


                        /*
                         * PEMBULATAN
                         */
                        const rounding =
                            row.querySelector(
                                '.markup-rounding'
                            );


                        const roundingValue =
                            unformatRupiah(
                                rounding ?
                                rounding.value :
                                ''
                            );


                        // if (
                        //     !rounding ||
                        //     roundingValue === ''
                        // ) {

                        //     setFieldError(
                        //         rounding
                        //     );

                        //     showToast(
                        //         'error',
                        //         'Pembulatan wajib diisi.'
                        //     );

                        //     if (rounding) {
                        //         rounding.focus();
                        //     }

                        //     return false;
                        // }


                        if (
                            Number(roundingValue) < 0
                        ) {

                            setFieldError(
                                rounding
                            );

                            showToast(
                                'error',
                                'Pembulatan tidak boleh bernilai negatif.'
                            );

                            rounding.focus();

                            return false;
                        }


                        /*
                         * STATUS
                         */
                        const status =
                            row.querySelector(
                                '.markup-status'
                            );


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
                                $(status).select2('open');
                            }

                            return false;
                        }
                    }
                }


                /*
                 * TIDAK ADA:
                 *
                 * validateDuplicatePriceTypes()
                 *
                 * Duplicate price_type ditangani backend.
                 */


                return true;
            }


            /*
            |--------------------------------------------------------------------------
            | OPEN MODAL
            |--------------------------------------------------------------------------
            */

            function openTambahDisplayMarkupModal() {

                if (!modalTambahDisplayMarkup) {
                    return;
                }

                modalTambahDisplayMarkup.classList.add(
                    'is-open'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CLOSE MODAL
            |--------------------------------------------------------------------------
            */

            function closeTambahDisplayMarkupModal() {

                if (!modalTambahDisplayMarkup) {
                    return;
                }

                modalTambahDisplayMarkup.classList.remove(
                    'is-open'
                );

                resetTambahDisplayMarkupForm();
            }


            /*
            |--------------------------------------------------------------------------
            | BUTTON OPEN
            |--------------------------------------------------------------------------
            */

            if (btnTambahDisplayMarkup) {

                btnTambahDisplayMarkup.addEventListener(
                    'click',
                    function() {

                        resetTambahDisplayMarkupForm();

                        openTambahDisplayMarkupModal();
                    }
                );
            }


            /*
            |--------------------------------------------------------------------------
            | BUTTON CLOSE
            |--------------------------------------------------------------------------
            */

            if (btnTutupModalTambahDisplayMarkup) {

                btnTutupModalTambahDisplayMarkup
                    .addEventListener(
                        'click',
                        function() {

                            closeTambahDisplayMarkupModal();
                        }
                    );
            }


            if (btnBatalTambahDisplayMarkup) {

                btnBatalTambahDisplayMarkup
                    .addEventListener(
                        'click',
                        function() {

                            closeTambahDisplayMarkupModal();
                        }
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | OVERLAY
            |--------------------------------------------------------------------------
            */

            if (modalTambahDisplayMarkup) {

                modalTambahDisplayMarkup.addEventListener(
                    'click',
                    function(event) {

                        if (
                            event.target ===
                            modalTambahDisplayMarkup
                        ) {

                            closeTambahDisplayMarkupModal();
                        }
                    }
                );
            }


            /*
            |--------------------------------------------------------------------------
            | ESC
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modalTambahDisplayMarkup &&
                        modalTambahDisplayMarkup.classList.contains(
                            'is-open'
                        )
                    ) {

                        closeTambahDisplayMarkupModal();
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | ADD HEADER
            |--------------------------------------------------------------------------
            */

            if (btnTambahHeaderDisplayMarkup) {

                btnTambahHeaderDisplayMarkup
                    .addEventListener(
                        'click',
                        function() {

                            createMarkupHeader(
                                headerIndex
                            );

                            headerIndex++;

                            reindexHeaderGroups();
                            reindexRuleRows();
                        }
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | EVENT DELEGATION
            |--------------------------------------------------------------------------
            */

            if (displayMarkupHeaderContainer) {

                displayMarkupHeaderContainer
                    .addEventListener(
                        'click',
                        function(event) {

                            /*
                             * REMOVE HEADER
                             */
                            const removeHeader =
                                event.target.closest(
                                    '.btn-remove-header'
                                );


                            if (removeHeader) {

                                const header =
                                    removeHeader.closest(
                                        '.markup-header-group'
                                    );


                                if (header) {

                                    removeMarkupHeader(
                                        header
                                    );
                                }

                                return;
                            }


                            /*
                             * ADD CHILD
                             */
                            const addMarkup =
                                event.target.closest(
                                    '.btnTambahMarkup'
                                );


                            if (addMarkup) {

                                const header =
                                    addMarkup.closest(
                                        '.markup-header-group'
                                    );


                                if (header) {

                                    addMarkupRow(
                                        header
                                    );
                                }

                                return;
                            }


                            /*
                             * REMOVE CHILD
                             */
                            const removeMarkup =
                                event.target.closest(
                                    '.btn-remove-markup'
                                );


                            if (removeMarkup) {

                                const row =
                                    removeMarkup.closest(
                                        '.markup-rule-row'
                                    );

                                const header =
                                    removeMarkup.closest(
                                        '.markup-header-group'
                                    );


                                if (
                                    !row ||
                                    !header
                                ) {
                                    return;
                                }


                                const rows =
                                    getRuleRows(
                                        header
                                    );


                                if (
                                    rows.length <= 1
                                ) {

                                    showToast(
                                        'error',
                                        'Minimal harus terdapat satu markup.'
                                    );

                                    return;
                                }


                                /*
                                 * Destroy Select2
                                 */
                                destroyMarkupRowSelect2(
                                    row
                                );


                                row.remove();


                                reindexRuleRows();


                                updateRemoveMarkupButtons(
                                    header
                                );

                                return;
                            }
                        }
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | SUBMIT
            |--------------------------------------------------------------------------
            */

            if (formTambahDisplayMarkup) {

                formTambahDisplayMarkup.addEventListener(
                    'submit',
                    async function(event) {

                        event.preventDefault();


                        /*
                         * VALIDASI
                         */
                        if (!validateForm()) {
                            return;
                        }


                        /*
                         * Simpan nilai rounding
                         */
                        const roundingInputs =
                            formTambahDisplayMarkup
                            .querySelectorAll(
                                '.markup-rounding'
                            );


                        const originalRoundingValues = [];


                        roundingInputs.forEach(
                            function(
                                input,
                                index
                            ) {

                                originalRoundingValues[index] =
                                    input.value;

                                input.value =
                                    unformatRupiah(
                                        input.value
                                    );
                            }
                        );


                        /*
                         * Disable button
                         */
                        if (btnSimpanDisplayMarkup) {

                            btnSimpanDisplayMarkup.disabled =
                                true;

                            btnSimpanDisplayMarkup.dataset
                                .originalText =
                                btnSimpanDisplayMarkup.textContent;

                            btnSimpanDisplayMarkup.textContent =
                                'Menyimpan...';
                        }


                        try {

                            const formData =
                                new FormData(
                                    formTambahDisplayMarkup
                                );


                            const response =
                                await fetch(
                                    formTambahDisplayMarkup.action, {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json'
                                        }
                                    }
                                );


                            const data =
                                await response.json();


                            /*
                             * ERROR
                             */
                            if (!response.ok) {

                                if (data.errors) {

                                    const firstError =
                                        Object.values(
                                            data.errors
                                        )[0];


                                    if (
                                        Array.isArray(
                                            firstError
                                        ) &&
                                        firstError.length
                                    ) {

                                        showToast(
                                            'error',
                                            firstError[0]
                                        );

                                    } else {

                                        showToast(
                                            'error',
                                            'Data yang dikirim tidak valid.'
                                        );
                                    }

                                } else {

                                    showToast(
                                        'error',
                                        data.message ||
                                        'Terjadi kesalahan saat menyimpan markup.'
                                    );
                                }

                                return;
                            }


                            /*
                             * SUCCESS
                             */
                            if (data.success) {

                                showToast(
                                    'success',
                                    data.message ||
                                    'Markup Display berhasil disimpan.'
                                );


                                closeTambahDisplayMarkupModal();


                                /*
                                 * Reload DataTable
                                 */
                                if (
                                    typeof displayPricingRuleTable !==
                                    'undefined' &&
                                    displayPricingRuleTable
                                ) {

                                    displayPricingRuleTable
                                        .ajax
                                        .reload(
                                            null,
                                            false
                                        );
                                }

                            } else {

                                showToast(
                                    'error',
                                    data.message ||
                                    'Markup Display gagal disimpan.'
                                );
                            }

                        } catch (error) {

                            console.error(
                                'Error submit markup display:',
                                error
                            );


                            showToast(
                                'error',
                                'Terjadi kesalahan saat menghubungi server.'
                            );

                        } finally {

                            /*
                             * Restore Rupiah
                             */
                            formTambahDisplayMarkup
                                .querySelectorAll(
                                    '.markup-rounding'
                                )
                                .forEach(
                                    function(
                                        input,
                                        index
                                    ) {

                                        if (
                                            originalRoundingValues[
                                                index
                                            ] !== undefined
                                        ) {

                                            input.value =
                                                formatRupiah(
                                                    unformatRupiah(
                                                        originalRoundingValues[
                                                            index
                                                        ]
                                                    )
                                                );
                                        }
                                    }
                                );


                            /*
                             * Enable button
                             */
                            if (btnSimpanDisplayMarkup) {

                                btnSimpanDisplayMarkup.disabled =
                                    false;

                                btnSimpanDisplayMarkup.textContent =
                                    btnSimpanDisplayMarkup.dataset
                                    .originalText ||
                                    'Simpan Markup';
                            }
                        }
                    }
                );
            }


            /*
            |--------------------------------------------------------------------------
            | RESET
            |--------------------------------------------------------------------------
            */

            function resetTambahDisplayMarkupForm() {

                /*
                 * Destroy semua Select2
                 * sebelum container dikosongkan.
                 */
                getHeaderGroups().forEach(
                    function(header) {

                        destroyHeaderSelect2(
                            header
                        );


                        getRuleRows(header).forEach(
                            function(row) {

                                destroyMarkupRowSelect2(
                                    row
                                );
                            }
                        );
                    }
                );


                /*
                 * Reset container
                 */
                displayMarkupHeaderContainer.innerHTML =
                    '';


                /*
                 * Reset index
                 */
                headerIndex = 1;
                ruleIndex = 0;


                /*
                 * Buat Header pertama
                 */
                createMarkupHeader(0);


                headerIndex = 1;


                /*
                 * Reindex
                 */
                reindexHeaderGroups();
                reindexRuleRows();


                /*
                 * Clear validation
                 */
                clearAllValidation();
            }


            /*
            |--------------------------------------------------------------------------
            | INITIALIZE
            |--------------------------------------------------------------------------
            */

            resetTambahDisplayMarkupForm();

        });

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | DELETE DISPLAY PRICING RULE
            |--------------------------------------------------------------------------
            */

            let displayPricingRuleEngineIdToDelete = null;
            let displayPricingRuleLocationIdToDelete = null;
            let displayPricingRuleCategoryIdToDelete = null;


            const modalDeleteDisplayPricingRule =
                document.getElementById(
                    'modalDeleteDisplayPricingRule'
                );

            const btnTutupDeleteDisplayPricingRule =
                document.getElementById(
                    'btnTutupDeleteDisplayPricingRule'
                );

            const btnBatalDeleteDisplayPricingRule =
                document.getElementById(
                    'btnBatalDeleteDisplayPricingRule'
                );

            const btnConfirmDeleteDisplayPricingRule =
                document.getElementById(
                    'btnConfirmDeleteDisplayPricingRule'
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
                            '.btn-delete-display-pricing-rule'
                        );

                    if (!deleteButton) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL ENGINE ID, LOCATION ID, CATEGORY ID
                    |--------------------------------------------------------------------------
                    */

                    displayPricingRuleEngineIdToDelete =
                        deleteButton.dataset.engineId;

                    displayPricingRuleLocationIdToDelete =
                        deleteButton.dataset.locationId;

                    displayPricingRuleCategoryIdToDelete =
                        deleteButton.dataset.categoryId;


                    /*
                    |--------------------------------------------------------------------------
                    | CEK DATA
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !displayPricingRuleEngineIdToDelete ||
                        !displayPricingRuleLocationIdToDelete ||
                        !displayPricingRuleCategoryIdToDelete
                    ) {

                        showToast(
                            'error',
                            'Data mesin, lokasi, atau kategori tidak ditemukan.'
                        );

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | BUKA MODAL
                    |--------------------------------------------------------------------------
                    */

                    modalDeleteDisplayPricingRule.classList.add(
                        'is-open'
                    );
                }
            );


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupDeleteDisplayPricingRuleModal() {

                modalDeleteDisplayPricingRule.classList.remove(
                    'is-open'
                );


                displayPricingRuleEngineIdToDelete = null;
                displayPricingRuleLocationIdToDelete = null;
                displayPricingRuleCategoryIdToDelete = null;
            }


            btnTutupDeleteDisplayPricingRule.addEventListener(
                'click',
                tutupDeleteDisplayPricingRuleModal
            );


            btnBatalDeleteDisplayPricingRule.addEventListener(
                'click',
                tutupDeleteDisplayPricingRuleModal
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modalDeleteDisplayPricingRule.addEventListener(
                'click',
                function(event) {

                    if (
                        event.target ===
                        modalDeleteDisplayPricingRule
                    ) {

                        tutupDeleteDisplayPricingRuleModal();
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
                        modalDeleteDisplayPricingRule.classList.contains(
                            'is-open'
                        )
                    ) {

                        tutupDeleteDisplayPricingRuleModal();
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI HAPUS DISPLAY PRICING RULE
            |--------------------------------------------------------------------------
            */

            btnConfirmDeleteDisplayPricingRule.addEventListener(
                'click',
                async function() {

                    /*
                    |--------------------------------------------------------------------------
                    | CEK ENGINE ID, LOCATION ID, CATEGORY ID
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !displayPricingRuleEngineIdToDelete ||
                        !displayPricingRuleLocationIdToDelete ||
                        !displayPricingRuleCategoryIdToDelete
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
                                `/admin/display-pricing-rules/${displayPricingRuleEngineIdToDelete}/${displayPricingRuleLocationIdToDelete}/${displayPricingRuleCategoryIdToDelete}/delete`, {
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

                            tutupDeleteDisplayPricingRuleModal();


                            /*
                            |--------------------------------------------------------------------------
                            | RELOAD DATATABLE
                            |--------------------------------------------------------------------------
                            */

                            displayPricingRuleTable.ajax.reload(
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
                                'Markup Display gagal dihapus.'
                            );
                        }

                    } catch (error) {

                        console.error(error);


                        /*
                        |--------------------------------------------------------------------------
                        | ERROR SERVER / NETWORK
                        |--------------------------------------------------------------------------
                        */

                        showToast(
                            'error',
                            'Terjadi kesalahan saat menghapus markup Display.'
                        );
                    }

                }
            );

        });

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | DETAIL DISPLAY PRICING RULE
            |--------------------------------------------------------------------------
            */

            const modalDetailDisplayPricingRule =
                document.getElementById(
                    'modalDetailDisplayPricingRule'
                );

            const btnTutupDetailDisplayPricingRule =
                document.getElementById(
                    'btnTutupModalDetailDisplayPricingRule'
                );

            const btnBatalDetailDisplayPricingRule =
                document.getElementById(
                    'btnBatalDetailDisplayPricingRule'
                );

            const detailDisplayMarkupEngine =
                document.getElementById(
                    'detailDisplayMarkupEngine'
                );

            const detailDisplayMarkupLocation =
                document.getElementById(
                    'detailDisplayMarkupLocation'
                );

            const detailDisplayMarkupCategory =
                document.getElementById(
                    'detailDisplayMarkupCategory'
                );

            const detailDisplayMarkupContent =
                document.getElementById(
                    'detailDisplayMarkupContent'
                );


            /*
            |--------------------------------------------------------------------------
            | CEK ELEMENT
            |--------------------------------------------------------------------------
            */

            if (
                !modalDetailDisplayPricingRule ||
                !btnTutupDetailDisplayPricingRule ||
                !btnBatalDetailDisplayPricingRule ||
                !detailDisplayMarkupEngine ||
                !detailDisplayMarkupLocation ||
                !detailDisplayMarkupCategory ||
                !detailDisplayMarkupContent
            ) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL
            |--------------------------------------------------------------------------
            */

            function bukaModalDetailDisplayPricingRule() {

                modalDetailDisplayPricingRule.classList.add(
                    'is-open'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupModalDetailDisplayPricingRule() {

                modalDetailDisplayPricingRule.classList.remove(
                    'is-open'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | FORMAT RUPIAH
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | LABEL TIPE HARGA
            |--------------------------------------------------------------------------
            */

            function getPriceTypeLabel(priceType) {

                const labels = {
                    general: 'Harga Umum',
                    division: 'Harga Divisi',
                    plain: 'Harga Polos'
                };

                return labels[priceType] || priceType;
            }


            /*
            |--------------------------------------------------------------------------
            | RENDER DETAIL MARKUP DISPLAY
            |--------------------------------------------------------------------------
            */

            function renderDetailDisplayMarkup(pricingRules) {

                detailDisplayMarkupContent.innerHTML = '';


                if (
                    !pricingRules ||
                    !Array.isArray(pricingRules) ||
                    !pricingRules.length
                ) {

                    detailDisplayMarkupContent.innerHTML = `
                <div class="details-empty">
                    Tidak ada data markup Display.
                </div>
            `;

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | ACCORDION
                |--------------------------------------------------------------------------
                */

                const accordion =
                    document.createElement('div');

                accordion.classList.add(
                    'details-category'
                );

                accordion.setAttribute(
                    'data-display-markup-accordion',
                    ''
                );


                /*
                |--------------------------------------------------------------------------
                | ACCORDION HEADER
                |--------------------------------------------------------------------------
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
                    MARKUP DISPLAY
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
                |--------------------------------------------------------------------------
                | ACCORDION CONTENT
                |--------------------------------------------------------------------------
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
                |--------------------------------------------------------------------------
                | TABLE WRAPPER
                |--------------------------------------------------------------------------
                */

                const tableWrapper =
                    document.createElement('div');

                tableWrapper.classList.add(
                    'details-table-wrapper'
                );


                /*
                |--------------------------------------------------------------------------
                | TABLE
                |--------------------------------------------------------------------------
                */

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
                |--------------------------------------------------------------------------
                | ROW DATA
                |--------------------------------------------------------------------------
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


                /*
                |--------------------------------------------------------------------------
                | SUSUN ELEMENT
                |--------------------------------------------------------------------------
                */

                tableWrapper.appendChild(table);

                inner.appendChild(tableWrapper);

                content.appendChild(inner);

                accordion.appendChild(header);

                accordion.appendChild(content);

                detailDisplayMarkupContent.appendChild(
                    accordion
                );


                /*
                |--------------------------------------------------------------------------
                | ACCORDION BEHAVIOR
                |--------------------------------------------------------------------------
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


            /*
            |--------------------------------------------------------------------------
            | TOMBOL DETAIL DATATABLE
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                function(event) {

                    const detailButton =
                        event.target.closest(
                            '.btn-detail-display-pricing-rule'
                        );


                    if (!detailButton) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL ID
                    |--------------------------------------------------------------------------
                    */

                    const engineId =
                        detailButton.dataset.engineId;

                    const locationId =
                        detailButton.dataset.locationId;

                    const categoryId =
                        detailButton.dataset.categoryId;


                    /*
                    |--------------------------------------------------------------------------
                    | CEK DATA
                    |--------------------------------------------------------------------------
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
                    |--------------------------------------------------------------------------
                    | SIMPAN ID PADA MODAL
                    |--------------------------------------------------------------------------
                    */

                    modalDetailDisplayPricingRule.dataset.engineId =
                        engineId;

                    modalDetailDisplayPricingRule.dataset.locationId =
                        locationId;

                    modalDetailDisplayPricingRule.dataset.categoryId =
                        categoryId;


                    /*
                    |--------------------------------------------------------------------------
                    | RESET ISI MODAL
                    |--------------------------------------------------------------------------
                    */

                    detailDisplayMarkupEngine.textContent =
                        'Memuat...';

                    detailDisplayMarkupLocation.textContent =
                        'Memuat...';

                    detailDisplayMarkupCategory.textContent =
                        'Memuat...';

                    detailDisplayMarkupContent.innerHTML = `
                <div class="details-empty">
                    Memuat data markup Display...
                </div>
            `;


                    /*
                    |--------------------------------------------------------------------------
                    | BUKA MODAL
                    |--------------------------------------------------------------------------
                    */

                    bukaModalDetailDisplayPricingRule();


                    /*
                    |--------------------------------------------------------------------------
                    | URL DETAIL
                    |--------------------------------------------------------------------------
                    */

                    const detailUrl =
                        `{{ url('/admin/display-pricing-rules') }}/${engineId}/${locationId}/${categoryId}/details`;


                    /*
                    |--------------------------------------------------------------------------
                    | FETCH DATA
                    |--------------------------------------------------------------------------
                    */

                    fetch(
                            detailUrl, {
                                method: 'GET',

                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',

                                    'Accept': 'application/json'
                                }
                            }
                        )
                        .then(
                            async function(response) {

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
                                        'Data markup Display gagal dimuat.'
                                    );
                                }


                                return data;
                            }
                        )
                        .then(
                            function(data) {

                                const pricingRules =
                                    data.data;


                                if (
                                    !pricingRules ||
                                    !Array.isArray(
                                        pricingRules
                                    ) ||
                                    !pricingRules.length
                                ) {

                                    throw new Error(
                                        'Data markup Display tidak ditemukan.'
                                    );
                                }


                                /*
                                |--------------------------------------------------------------------------
                                | AMBIL HEADER DARI RULE PERTAMA
                                |--------------------------------------------------------------------------
                                */

                                const firstRule =
                                    pricingRules[0];


                                detailDisplayMarkupEngine.textContent =
                                    firstRule.engine_name || '-';


                                detailDisplayMarkupLocation.textContent =
                                    firstRule.location_name || '-';


                                detailDisplayMarkupCategory.textContent =
                                    firstRule.category_name || '-';


                                /*
                                |--------------------------------------------------------------------------
                                | RENDER DETAIL
                                |--------------------------------------------------------------------------
                                */

                                renderDetailDisplayMarkup(
                                    pricingRules
                                );
                            }
                        )
                        .catch(
                            function(error) {

                                detailDisplayMarkupEngine.textContent =
                                    '-';

                                detailDisplayMarkupLocation.textContent =
                                    '-';

                                detailDisplayMarkupCategory.textContent =
                                    '-';


                                detailDisplayMarkupContent.innerHTML = `
                            <div class="details-empty">
                                Data markup Display gagal dimuat.
                            </div>
                        `;


                                showToast(
                                    'error',
                                    error.message ||
                                    'Terjadi kesalahan saat memuat detail markup Display.'
                                );
                            }
                        );
                }
            );


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            btnTutupDetailDisplayPricingRule.addEventListener(
                'click',
                function() {

                    tutupModalDetailDisplayPricingRule();
                }
            );


            btnBatalDetailDisplayPricingRule.addEventListener(
                'click',
                function() {

                    tutupModalDetailDisplayPricingRule();
                }
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKDROP
            |--------------------------------------------------------------------------
            */

            modalDetailDisplayPricingRule.addEventListener(
                'click',
                function(event) {

                    if (
                        event.target ===
                        modalDetailDisplayPricingRule
                    ) {

                        tutupModalDetailDisplayPricingRule();
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | ESCAPE
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modalDetailDisplayPricingRule.classList.contains(
                            'is-open'
                        )
                    ) {

                        tutupModalDetailDisplayPricingRule();
                    }
                }
            );

        });

        document.addEventListener('DOMContentLoaded', function() {

            const modalEditDisplayPricingRule =
                document.getElementById(
                    'modalEditDisplayPricingRule'
                );

            const formEditDisplayPricingRule =
                document.getElementById(
                    'formEditDisplayPricingRule'
                );

            const editDisplayMarkupRows =
                document.getElementById(
                    'editDisplayMarkupRows'
                );

            const editDisplayMarkupEngine =
                document.getElementById(
                    'editDisplayMarkupEngine'
                );

            const editDisplayMarkupLocation =
                document.getElementById(
                    'editDisplayMarkupLocation'
                );

            const editDisplayMarkupCategory =
                document.getElementById(
                    'editDisplayMarkupCategory'
                );

            const btnTutupEditDisplay =
                document.getElementById(
                    'btnTutupModalEditDisplayPricingRule'
                );

            const btnBatalEditDisplay =
                document.getElementById(
                    'btnBatalEditDisplayPricingRule'
                );

            const btnTambahEditDisplayMarkup =
                document.getElementById(
                    'btnTambahEditDisplayMarkup'
                );

            if (
                !modalEditDisplayPricingRule ||
                !formEditDisplayPricingRule ||
                !editDisplayMarkupRows ||
                !editDisplayMarkupEngine ||
                !editDisplayMarkupLocation ||
                !editDisplayMarkupCategory ||
                !btnTutupEditDisplay ||
                !btnBatalEditDisplay ||
                !btnTambahEditDisplayMarkup
            ) {
                return;
            }


            let currentEngineId = null;
            let currentLocationId = null;
            let currentCategoryId = null;

            let newEditDisplayMarkupIndex = 0;


            /* =========================================================
             * FORMAT RUPIAH
             * ========================================================= */

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

                return String(value).replace(
                    /\D/g,
                    ''
                );
            }


            /* =========================================================
             * SELECT2
             * ========================================================= */

            function initEditDisplaySelect2(container) {

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
                                dropdownParent: $(modalEditDisplayPricingRule)
                            });
                        }
                    });
            }


            function destroyEditDisplaySelect2(container) {

                $(container)
                    .find('select.select2')
                    .each(function() {

                        if (
                            $(this).hasClass(
                                'select2-hidden-accessible'
                            )
                        ) {

                            $(this).select2(
                                'destroy'
                            );
                        }
                    });
            }


            /* =========================================================
             * MODAL
             * ========================================================= */

            function bukaModalEditDisplay() {

                modalEditDisplayPricingRule.classList.add(
                    'is-open'
                );
            }


            function tutupModalEditDisplay() {

                modalEditDisplayPricingRule.classList.remove(
                    'is-open'
                );
            }


            function resetModalEditDisplay() {

                destroyEditDisplaySelect2(
                    modalEditDisplayPricingRule
                );

                editDisplayMarkupRows.innerHTML = '';

                newEditDisplayMarkupIndex = 0;

                currentEngineId = null;
                currentLocationId = null;
                currentCategoryId = null;

                formEditDisplayPricingRule.action = '';

                editDisplayMarkupEngine.innerHTML = `
            <option value="">
                Memuat mesin...
            </option>
        `;

                editDisplayMarkupLocation.innerHTML = `
            <option value="">
                Memuat lokasi...
            </option>
        `;

                editDisplayMarkupCategory.innerHTML = `
            <option value="">
                Memuat kategori...
            </option>
        `;
            }


            /* =========================================================
             * CREATE ROW
             * ========================================================= */

            function createEditDisplayMarkupRow(
                rule,
                index
            ) {

                const row =
                    document.createElement('div');

                row.classList.add(
                    'markup-row'
                );

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
                    rule.rounding_value :
                    '';

                const status =
                    rule && rule.status ?
                    rule.status :
                    'Active';


                row.innerHTML = `

            <div class="markup-remove">

                <button
                    type="button"
                    class="btn-remove-display-markup"
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
             * ========================================================= */

            function reindexEditDisplayMarkupRows() {

                const rows =
                    editDisplayMarkupRows.querySelectorAll(
                        '.markup-row'
                    );

                rows.forEach(
                    function(row, index) {

                        const fields =
                            row.querySelectorAll(
                                '[name]'
                            );

                        fields.forEach(
                            function(field) {

                                const name =
                                    field.getAttribute(
                                        'name'
                                    );

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
                            }
                        );
                    }
                );
            }


            /* =========================================================
             * TAMBAH ROW BARU
             * ========================================================= */

            function tambahDisplayMarkupEditRow() {

                newEditDisplayMarkupIndex++;

                const row =
                    createEditDisplayMarkupRow({
                            price_type: '',
                            markup_percentage: '',
                            rounding_value: '',
                            status: 'Active'
                        },
                        `new_${newEditDisplayMarkupIndex}`
                    );

                editDisplayMarkupRows.appendChild(
                    row
                );

                initEditDisplaySelect2(
                    row
                );
            }


            /* =========================================================
             * BUKA EDIT
             * ========================================================= */

            document.addEventListener(
                'click',
                function(event) {

                    const editButton =
                        event.target.closest(
                            '.btn-edit-display-pricing-rule'
                        );

                    if (!editButton) {
                        return;
                    }


                    const engineId =
                        editButton.dataset.engineId;

                    const locationId =
                        editButton.dataset.locationId;

                    const categoryId =
                        editButton.dataset.categoryId;


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


                    currentEngineId =
                        engineId;

                    currentLocationId =
                        locationId;

                    currentCategoryId =
                        categoryId;


                    resetModalEditDisplay();


                    currentEngineId =
                        engineId;

                    currentLocationId =
                        locationId;

                    currentCategoryId =
                        categoryId;


                    const editUrl =
                        `{{ url('/admin/display-pricing-rules') }}/${engineId}/${locationId}/${categoryId}/edit`;

                    const updateUrl =
                        `{{ url('/admin/display-pricing-rules') }}/${engineId}/${locationId}/${categoryId}/update`;

                    /*
                     * Action form diarahkan ke
                     * endpoint update.
                     *
                     * Backend update belum dibuat.
                     */
                    formEditDisplayPricingRule.action =
                        updateUrl;


                    fetch(
                            editUrl, {
                                method: 'GET',

                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',

                                    'Accept': 'application/json'
                                }
                            }
                        )
                        .then(
                            async function(response) {

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
                                        'Data markup Display gagal dimuat.'
                                    );
                                }


                                return data;
                            }
                        )
                        .then(
                            function(data) {

                                const pricingRules =
                                    data.data;


                                if (
                                    !pricingRules ||
                                    !Array.isArray(
                                        pricingRules
                                    ) ||
                                    !pricingRules.length
                                ) {

                                    throw new Error(
                                        'Data markup Display tidak ditemukan.'
                                    );
                                }


                                const firstRule =
                                    pricingRules[0];


                                /* =========================
                                 * MESIN
                                 * ========================= */

                                editDisplayMarkupEngine.innerHTML = `
                            <option value="${firstRule.engine_id}">
                                ${firstRule.engine_name}
                            </option>
                        `;

                                editDisplayMarkupEngine.value =
                                    firstRule.engine_id;


                                /* =========================
                                 * LOKASI
                                 * ========================= */

                                editDisplayMarkupLocation.innerHTML = `
                            <option value="${firstRule.location_id}">
                                ${firstRule.location_name}
                            </option>
                        `;

                                editDisplayMarkupLocation.value =
                                    firstRule.location_id;


                                /* =========================
                                 * KATEGORI
                                 * ========================= */

                                editDisplayMarkupCategory.innerHTML = `
                            <option value="${firstRule.category_id}">
                                ${firstRule.category_name}
                            </option>
                        `;

                                editDisplayMarkupCategory.value =
                                    firstRule.category_id;


                                /* =========================
                                 * RENDER ROW
                                 * ========================= */

                                editDisplayMarkupRows.innerHTML =
                                    '';

                                newEditDisplayMarkupIndex =
                                    0;


                                pricingRules.forEach(
                                    function(
                                        rule,
                                        index
                                    ) {

                                        const row =
                                            createEditDisplayMarkupRow(
                                                rule,
                                                index
                                            );

                                        editDisplayMarkupRows.appendChild(
                                            row
                                        );

                                        initEditDisplaySelect2(
                                            row
                                        );
                                    }
                                );


                                reindexEditDisplayMarkupRows();


                                bukaModalEditDisplay();
                            }
                        )
                        .catch(
                            function(error) {

                                showToast(
                                    'error',
                                    error.message ||
                                    'Terjadi kesalahan saat memuat markup Display.'
                                );
                            }
                        );
                }
            );


            /* =========================================================
             * TUTUP MODAL
             * ========================================================= */

            btnTutupEditDisplay.addEventListener(
                'click',
                function() {

                    tutupModalEditDisplay();
                }
            );


            btnBatalEditDisplay.addEventListener(
                'click',
                function() {

                    tutupModalEditDisplay();
                }
            );


            modalEditDisplayPricingRule.addEventListener(
                'click',
                function(event) {

                    if (
                        event.target ===
                        modalEditDisplayPricingRule
                    ) {

                        tutupModalEditDisplay();
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
                        modalEditDisplayPricingRule.classList.contains(
                            'is-open'
                        )
                    ) {

                        tutupModalEditDisplay();
                    }
                }
            );


            /* =========================================================
             * HAPUS ROW
             * ========================================================= */

            editDisplayMarkupRows.addEventListener(
                'click',
                function(event) {

                    const removeButton =
                        event.target.closest(
                            '.btn-remove-display-markup'
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


                    reindexEditDisplayMarkupRows();
                }
            );


            /* =========================================================
             * TAMBAH MARKUP
             * ========================================================= */

            btnTambahEditDisplayMarkup.addEventListener(
                'click',
                function() {

                    tambahDisplayMarkupEditRow();
                }
            );


            /* =========================================================
             * FORMAT RUPIAH PEMBULATAN
             * ========================================================= */

            formEditDisplayPricingRule.addEventListener(
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

            /* =========================================================
             * SUBMIT UPDATE
             * ========================================================= */

            formEditDisplayPricingRule.addEventListener(
                'submit',
                async function(event) {

                    event.preventDefault();

                    if (!formEditDisplayPricingRule.action) {
                        showToast(
                            'error',
                            'URL update markup Display tidak ditemukan.'
                        );

                        return;
                    }

                    /*
                     * Pastikan index pricing_rules sudah berurutan
                     */
                    reindexEditDisplayMarkupRows();

                    /*
                     * Ambil semua input pembulatan
                     */
                    const roundingInputs =
                        formEditDisplayPricingRule.querySelectorAll(
                            '.markup-rounding'
                        );

                    /*
                     * Ubah format Rupiah menjadi angka
                     *
                     * Contoh:
                     * Rp 1.000 -> 1000
                     * Rp 10.000 -> 10000
                     */
                    roundingInputs.forEach(
                        function(input) {

                            input.value =
                                unformatRupiah(
                                    input.value
                                );
                        }
                    );

                    /*
                     * BARU setelah nilai Rupiah di-unformat,
                     * buat FormData.
                     */
                    const formData =
                        new FormData(
                            formEditDisplayPricingRule
                        );

                    /*
                     * Method PUT Laravel
                     */
                    formData.append(
                        '_method',
                        'PUT'
                    );

                    const submitButton =
                        formEditDisplayPricingRule.querySelector(
                            '[type="submit"]'
                        );

                    if (submitButton) {
                        submitButton.disabled = true;
                    }

                    try {

                        const response =
                            await fetch(
                                formEditDisplayPricingRule.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                }
                            );

                        const result =
                            await response.json();

                        if (
                            response.ok &&
                            result.success
                        ) {

                            tutupModalEditDisplay();

                            displayPricingRuleTable.ajax.reload(
                                null,
                                false
                            );

                            showToast(
                                'success',
                                result.message ||
                                'Markup Display berhasil diperbarui.'
                            );

                        } else {

                            showToast(
                                'error',
                                result.message ||
                                'Markup Display gagal diperbarui.'
                            );
                        }

                    } catch (error) {

                        console.error(error);

                        showToast(
                            'error',
                            'Terjadi kesalahan saat memperbarui markup Display.'
                        );

                    } finally {

                        /*
                         * Kembalikan tampilan input ke format Rupiah
                         */
                        roundingInputs.forEach(
                            function(input) {

                                input.value =
                                    formatRupiah(
                                        input.value
                                    );
                            }
                        );

                        if (submitButton) {
                            submitButton.disabled = false;
                        }
                    }
                }
            );


            /* =========================================================
             * RESET AWAL
             * ========================================================= */

            resetModalEditDisplay();

        });

        document.addEventListener('DOMContentLoaded', function() {

            const btnImportDisplayPricingRules =
                document.getElementById('btnImportDisplayPricingRules');

            const modalImportDisplayPricingRules =
                document.getElementById('modalImportDisplayPricingRules');

            const btnCloseImportDisplayPricingRules =
                document.getElementById('btnCloseImportDisplayPricingRules');

            const btnCancelImportDisplayPricingRules =
                document.getElementById('btnCancelImportDisplayPricingRules');

            const btnSaveImportDisplayPricingRules =
                document.getElementById('btnSaveImportDisplayPricingRules');

            const fileImportDisplayPricingRules =
                document.getElementById('fileImportDisplayPricingRules');

            const fileSelectedNameDisplayPricingRules =
                document.getElementById(
                    'fileSelectedNameDisplayPricingRules'
                );


            /*
             * ==========================================================
             * OPEN MODAL
             * ==========================================================
             */

            btnImportDisplayPricingRules.addEventListener(
                'click',
                function() {

                    modalImportDisplayPricingRules.classList.add(
                        'is-open'
                    );

                }
            );


            /*
             * ==========================================================
             * CLOSE MODAL
             * ==========================================================
             */

            function closeImportDisplayPricingRulesModal() {

                modalImportDisplayPricingRules.classList.remove(
                    'is-open'
                );

                fileImportDisplayPricingRules.value = '';

                fileSelectedNameDisplayPricingRules.textContent =
                    'Belum ada file dipilih';

                fileSelectedNameDisplayPricingRules.classList.remove(
                    'has-file'
                );

            }


            btnCloseImportDisplayPricingRules.addEventListener(
                'click',
                closeImportDisplayPricingRulesModal
            );


            btnCancelImportDisplayPricingRules.addEventListener(
                'click',
                closeImportDisplayPricingRulesModal
            );


            /*
             * ==========================================================
             * CLOSE KETIKA KLIK BACKDROP
             * ==========================================================
             */

            modalImportDisplayPricingRules.addEventListener(
                'click',
                function(event) {

                    if (
                        event.target ===
                        modalImportDisplayPricingRules
                    ) {

                        closeImportDisplayPricingRulesModal();

                    }

                }
            );


            /*
             * ==========================================================
             * CLOSE DENGAN ESCAPE
             * ==========================================================
             */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modalImportDisplayPricingRules.classList.contains(
                            'is-open'
                        )
                    ) {

                        closeImportDisplayPricingRulesModal();

                    }

                }
            );


            /*
             * ==========================================================
             * FILE CHANGE
             * ==========================================================
             */

            fileImportDisplayPricingRules.addEventListener(
                'change',
                function() {

                    if (this.files.length > 0) {

                        fileSelectedNameDisplayPricingRules.textContent =
                            this.files[0].name;

                        fileSelectedNameDisplayPricingRules.classList.add(
                            'has-file'
                        );

                    } else {

                        fileSelectedNameDisplayPricingRules.textContent =
                            'Belum ada file dipilih';

                        fileSelectedNameDisplayPricingRules.classList.remove(
                            'has-file'
                        );

                    }

                }
            );


            /*
             * ==========================================================
             * IMPORT DATA
             * ==========================================================
             *
             * Route dan backend Import Display akan kita sambungkan
             * setelah DisplayPricingRulesImport.php selesai dibuat.
             *
             */

            btnSaveImportDisplayPricingRules.addEventListener(
                'click',
                async function() {

                    if (
                        fileImportDisplayPricingRules.files.length === 0
                    ) {

                        showToast(
                            'error',
                            'Silakan pilih file Excel terlebih dahulu.'
                        );

                        return;

                    }


                    const originalButtonHtml =
                        btnSaveImportDisplayPricingRules.innerHTML;


                    btnSaveImportDisplayPricingRules.disabled = true;

                    btnSaveImportDisplayPricingRules.innerHTML =
                        'Memproses...';


                    try {

                        const formData = new FormData();

                        formData.append(
                            'file',
                            fileImportDisplayPricingRules.files[0]
                        );


                        const csrfToken =
                            document.querySelector(
                                'meta[name="csrf-token"]'
                            )?.getAttribute('content');


                        const response = await fetch(
                            "{{ route('admin_import_display_pricing_rule') }}", {
                                method: 'POST',

                                headers: {
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'X-Requested-With': 'XMLHttpRequest'
                                },

                                body: formData
                            }
                        );


                        const data = await response.json();


                        if (!response.ok || !data.success) {

                            throw new Error(
                                data.message ||
                                'Import data gagal.'
                            );

                        }


                        showToast(
                            'success',
                            data.message
                        );


                        closeImportDisplayPricingRulesModal();


                        if (
                            typeof displayPricingRuleTable !==
                            'undefined' &&
                            displayPricingRuleTable.ajax
                        ) {

                            displayPricingRuleTable.ajax.reload(
                                null,
                                false
                            );

                        } else {

                            window.location.reload();

                        }


                    } catch (error) {

                        console.error(
                            'Import Display Pricing Rules Error:',
                            error
                        );


                        showToast(
                            'error',
                            error.message ||
                            'Terjadi kesalahan saat melakukan import.'
                        );


                    } finally {

                        btnSaveImportDisplayPricingRules.disabled =
                            false;

                        btnSaveImportDisplayPricingRules.innerHTML =
                            originalButtonHtml;

                    }

                }
            );

        });
    </script>
@endsection

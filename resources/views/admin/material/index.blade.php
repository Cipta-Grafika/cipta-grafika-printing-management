@extends('admin_master')
@section('contents')
    <link rel="stylesheet" href="{{ asset('css/material-index.css') }}">
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero cat-hero-compact">
                    <div class="hero-text"><span class="eyebrow">MASTER DATA · MATERIAL</span>
                        {{-- <h1 class="hero-title">Daftar Material</h1> --}}
                        <p class="hero-sub">Kelola dan pantau seluruh data material yang digunakan dalam proses produksi dan
                            perhitungan estimasi harga.</p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card cat-card-container">
                        <div class="cat-card-header">
                            <div class="cat-card-header-main">
                                <div class="cat-card-title-wrap">
                                    <div class="cat-card-icon"><i class="bi bi-box-seam-fill"></i></div>
                                    <div>
                                        <div class="cat-title-row">
                                            <h3 class="cat-card-title">Daftar Material</h3>
                                            <span class="cat-badge-total"><span class="cat-pulse-dot"></span><span id="materialTotalCount">Memuat data...</span></span>
                                        </div>
                                        <p class="cat-card-desc">Master material untuk produksi dan perhitungan estimasi harga.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="cat-card-actions">
                                <a id="btnExportMaterial" href="{{ route('admin_export_material') }}" class="cat-btn-head" title="Export Excel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg><span>Export</span></a>
                                <a href="{{ asset('templates/template_material.xlsx') }}" class="cat-btn-head" title="Download Template Excel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg><span>Template</span></a>
                                <button type="button" class="cat-btn-head" id="btnImportMaterial" title="Import Excel"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M12 16V3"/><path d="m7 8 5-5 5 5"/><path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6"/></svg><span>Import</span></button>
                                <button type="button" class="cat-btn-head-primary" id="btnTambahMaterial"><i class="bi bi-plus-lg"></i><span>Tambah Material</span></button>
                            </div>
                        </div>
                        <div class="cat-bulk-toolbar" id="materialBulkToolbar" style="display: none;">
                            <div class="cat-bulk-info"><span class="cat-bulk-badge" id="materialBulkCount">0 terpilih</span><span class="cat-bulk-text">Material yang dipilih siap untuk tindakan massal</span></div>
                            <div class="cat-bulk-actions">
                                <button type="button" class="btn btn--ghost btn--sm" id="cancelMaterialSelection"><i class="bi bi-x-circle me-1"></i> Batal Pilihan</button>
                                <button type="button" class="btn btn--danger btn--sm" id="bulkDeleteMaterials"><i class="bi bi-trash3-fill me-1"></i> Hapus Terpilih</button>
                            </div>
                        </div>
                        <div class="cat-filter-card">
                            <div class="cat-filter-group-left">
                                <div class="cat-search-box">
                                    <i class="bi bi-search cat-search-icon"></i>
                                    <input type="text" id="materialSearch" class="cat-filter-input" placeholder="Cari kode, nama, kategori, atau pembuat..." aria-label="Cari material">
                                    <button type="button" id="clearMaterialSearch" class="cat-clear-btn" title="Hapus pencarian" aria-label="Hapus pencarian" style="display: none;"><i class="bi bi-x-circle-fill"></i></button>
                                </div>
                                <div class="cat-select-box">
                                    <i class="bi bi-tags-fill cat-select-icon"></i>
                                    <select id="filterMaterialCategory" class="select2" style="width: 100%;" aria-label="Filter kategori material">
                                        <option value="">Semua Kategori</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="cat-select-box">
                                    <i class="bi bi-person-fill cat-select-icon"></i>
                                    <select id="materialCreator" class="select2" style="width: 100%;" aria-label="Filter pembuat">
                                        <option value="all">Semua Pembuat</option>
                                        @foreach ($creators as $creator)
                                            <option value="{{ $creator }}">{{ $creator }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="cat-filter-subgroup">
                                    <div class="cat-select-box">
                                        <i class="bi bi-calendar3 cat-select-icon"></i>
                                        <select id="materialPeriod" class="select2" style="width: 100%;" aria-label="Filter periode pembuatan">
                                            <option value="all">Semua Periode</option><option value="today">Hari Ini</option><option value="7_days">7 Hari Terakhir</option><option value="month">Bulan Ini</option><option value="year">Tahun Ini</option>
                                        </select>
                                    </div>
                                    <button type="button" id="resetMaterialFilters" class="cat-btn-reset" title="Reset semua filter"><i class="bi bi-arrow-counterclockwise"></i><span>Reset</span></button>
                                </div>
                            </div>
                            <div class="cat-filter-group-right"><div class="cat-length-box"><span class="cat-length-label">Baris:</span>
                                <select id="materialPageLength" class="select2" style="width: 76px;" aria-label="Jumlah baris"><option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option></select>
                            </div></div>
                        </div>
                        <div class="cat-table-wrap">
                            <table id="materialTable" class="cat-modern-table" style="width: 100%;" data-url="{{ route('admin_data_material') }}" data-bulk-url="{{ route('admin_bulk_destroy_materials') }}">
                                <thead><tr>
                                    <th class="cat-th-check text-center"><label class="cat-custom-checkbox" title="Pilih semua di halaman ini"><input type="checkbox" id="checkAllMaterials" aria-label="Pilih semua material di halaman ini"><span class="cat-checkbox-mark"><svg class="check-icon" viewBox="0 0 14 14" fill="none"><path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#fff" stroke-width="2.2"/></svg><svg class="minus-icon" viewBox="0 0 14 14" fill="none"><path d="M3 7H11" stroke="#fff" stroke-width="2.2"/></svg></span></label></th>
                                    <th class="cat-th-no text-center" style="width: 60px;">No</th>
                                    <th style="min-width: 150px;">Kode Material</th>
                                    <th style="min-width: 220px;">Nama Material</th>
                                    <th style="min-width: 150px;">Kategori</th>
                                    <th style="min-width: 120px;">Status</th>
                                    <th style="min-width: 170px;">Dibuat Oleh</th>
                                    <th style="min-width: 150px;">Dibuat Pada</th>
                                    <th class="text-center" style="width: 140px;">Aksi</th>
                                </tr></thead><tbody></tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </main>
            <div data-shell-footer></div>
        </div>
    </div>

    <div class="modal-overlay" id="modalBulkDeleteMaterials">
        <div class="modal-dialog modal-delete">
            <div class="modal-header">
                <div><span class="eyebrow">MASTER DATA · MATERIAL</span><br><span class="eyebrow">Hapus Material Terpilih</span></div>
                <button type="button" class="modal-close" id="closeBulkDeleteMaterials" aria-label="Tutup"><svg viewBox="0 0 24 24"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg></button>
            </div>
            <div class="modal-body"><div class="delete-confirmation"><div class="delete-icon"><i class="bi bi-trash3-fill"></i></div><div class="delete-content"><h3>Hapus material terpilih?</h3><p id="bulkDeleteMaterialDescription"></p><p>Material yang masih digunakan oleh data lain, termasuk ukuran dan harga, tidak dapat dihapus.</p></div></div></div>
            <div class="modal-footer"><button type="button" class="btn btn--ghost" id="cancelBulkDeleteMaterials">Batal</button><button type="button" class="btn btn--danger" id="confirmBulkDeleteMaterials"><i class="bi bi-trash3-fill"></i> Hapus Terpilih</button></div>
        </div>
    </div>

    <!-- Modal Import Material -->
    <div class="modal-overlay" id="modalImportMaterial">

        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">

                <div>
                    <span class="eyebrow">MASTER DATA · MATERIAL</span>
                    <br>
                    <span class="eyebrow">Import Material</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseImportMaterial" aria-label="Tutup">

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
                        Pilih file Excel yang berisi data material untuk diimport
                        ke dalam sistem.
                    </p>
                </div>


                <!-- Custom File Input -->
                <label for="fileImportMaterial" class="file-upload-box" id="fileUploadBox">

                    <input type="file" id="fileImportMaterial" name="file" accept=".xlsx,.xls" hidden>


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


                    <div class="file-selected-name" id="fileSelectedName">

                        Belum ada file dipilih

                    </div>

                </label>

            </div>


            <!-- Footer -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCancelImportMaterial">

                    Batal

                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportMaterial">

                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8 5-5 5 5" />
                    </svg>

                    Import Data

                </button>

            </div>

        </div>

    </div>

    <!-- MODAL DELETE MATERIAL -->
    <div class="modal-overlay" id="modalDeleteMaterial">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MASTER DATA · MATERIAL
                    </span>
                    <br>
                    <span class="eyebrow">
                        Hapus Material
                    </span>

                </div>


                <button type="button" class="modal-close" id="btnTutupDeleteMaterial" aria-label="Tutup">

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
                            Hapus material?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus material ini?
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteMaterial">

                    Batal

                </button>


                <button type="button" class="btn btn--danger" id="btnConfirmDeleteMaterial">

                    <i class="bi bi-trash3-fill"></i>


                    Hapus

                </button>

            </div>

        </div>

    </div>

    <!-- MODAL VIEW MATERIAL -->
    <div class="modal-overlay" id="modalViewMaterial">

        <div class="modal-dialog modal-dialog--wide">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MASTER DATA · MATERIAL
                    </span>
                    <br>

                    <span class="eyebrow">
                        Detail Material
                    </span>

                </div>

                <button type="button" class="modal-close" id="btnTutupViewMaterial" aria-label="Tutup">

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

                    <div class="delete-content">

                        <p>
                            Berikut adalah detail data material yang dipilih.
                        </p>

                    </div>

                </div>

                <!-- DETAIL MATERIAL -->
                <div style="margin-top: 20px;">

                    <!-- DATA MATERIAL -->
                    <div class="category-form-header">
                        Data Material
                    </div>

                    <div class="material-row" style="margin-bottom: 20px;">

                        <div class="material-row-top">

                            <!-- KATEGORI -->
                            <div class="material-field">

                                <div class="eyebrow">
                                    Kategori
                                </div>

                                <div id="viewMaterialCategory">
                                    -
                                </div>

                            </div>

                            <!-- KODE MATERIAL -->
                            <div class="material-field">

                                <div class="eyebrow">
                                    Kode Material
                                </div>

                                <div id="viewMaterialCode">
                                    -
                                </div>

                            </div>

                            <!-- NAMA MATERIAL -->
                            <div class="material-field">

                                <div class="eyebrow">
                                    Nama Material
                                </div>

                                <div id="viewMaterialName">
                                    -
                                </div>

                            </div>

                        </div>

                        <div class="material-row-bottom">

                            <!-- STATUS -->
                            <div class="material-field">

                                <div class="eyebrow">
                                    Status
                                </div>

                                <div id="viewMaterialStatus">
                                    -
                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- MATERIAL SIZES -->
                    <div class="material-sizes">

                        <div class="material-sizes-header">

                            <div>

                                <div class="eyebrow">
                                    Material Sizes
                                </div>

                                <div class="material-sizes-title">
                                    Daftar Ukuran Material
                                </div>

                            </div>

                        </div>

                        <div class="material-size-rows" id="viewMaterialSizeRows">

                            <!-- SIZE ROW AKAN DIISI JS -->

                        </div>

                    </div>

                </div>

            </div>

            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCloseViewMaterial">
                    Tutup
                </button>

            </div>

        </div>

    </div>


    {{-- Modal Tambah Material --}}

    <div class="modal-overlay" id="modalTambahMaterial">

        <div class="modal-dialog modal-dialog--wide">

            <form method="POST" action="{{ route('admin_store_material') }}" id="formTambahMaterial" novalidate>

                @csrf

                <div class="modal-header">

                    <div>

                        <span class="eyebrow">
                            MASTER DATA · MATERIAL
                        </span>

                        <br>

                        <span class="eyebrow">
                            Tambah Material
                        </span>

                    </div>

                    <button type="button" class="modal-close" id="btnTutupModalMaterial" aria-label="Tutup">

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
                                Data Material
                            </label>

                            <p class="category-description">
                                Tambahkan satu atau beberapa material sekaligus.
                            </p>

                        </div>

                        <button type="button" class="btn btn--ghost" id="btnTambahBarisMaterial">

                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>

                            Tambah Material

                        </button>

                    </div>


                    {{-- ================================================= --}}
                    {{-- MATERIAL ROWS --}}
                    {{-- ================================================= --}}

                    <div id="materialRows">

                        {{-- ================================================= --}}
                        {{-- MATERIAL PERTAMA --}}
                        {{-- ================================================= --}}

                        <div class="material-row">

                            {{-- ========================= --}}
                            {{-- DATA MATERIAL --}}
                            {{-- ========================= --}}

                            <div class="material-row-top">

                                {{-- KATEGORI --}}

                                <div class="material-field">

                                    <label class="category-label">
                                        Kategori
                                    </label>

                                    <select name="materials[0][category_id]" class="select select2" required>

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


                                {{-- KODE MATERIAL --}}

                                <div class="material-field">

                                    <label class="category-label">
                                        Kode Material
                                    </label>

                                    <input type="text" name="materials[0][material_code]" class="input"
                                        placeholder="Masukkan kode material...">

                                </div>


                                {{-- NAMA MATERIAL --}}

                                <div class="material-field">

                                    <label class="category-label">
                                        Nama Material
                                    </label>

                                    <input type="text" name="materials[0][material_name]" class="input"
                                        placeholder="Masukkan nama material..." required>

                                </div>

                            </div>


                            {{-- ========================= --}}
                            {{-- STATUS MATERIAL --}}
                            {{-- ========================= --}}

                            <div class="material-row-bottom">

                                <select name="materials[0][status]" class="select select2" required>

                                    <option value="Active" selected>
                                        Active
                                    </option>

                                    <option value="Inactive">
                                        Inactive
                                    </option>

                                </select>


                                {{-- HAPUS MATERIAL --}}

                                <button type="button" class="btn-remove-category" aria-label="Hapus material">

                                    <i class="bi bi-trash3-fill"></i>

                                </button>

                            </div>


                            {{-- ========================= --}}
                            {{-- MATERIAL SIZES --}}
                            {{-- ========================= --}}

                            <div class="material-sizes">

                                <div class="material-sizes-header">

                                    <div>

                                        <label class="category-label">
                                            Ukuran Material
                                        </label>

                                        <p class="category-description">
                                            Tambahkan satu atau beberapa ukuran material.
                                        </p>

                                    </div>

                                    <button type="button" class="btn btn--ghost btnTambahUkuran">

                                        <svg viewBox="0 0 24 24">
                                            <path d="M12 5v14M5 12h14" />
                                        </svg>

                                        Tambah Ukuran

                                    </button>

                                </div>


                                <div class="material-size-rows">

                                    {{-- SIZE PERTAMA --}}

                                    <div class="material-size-row">

                                        {{-- LEBAR --}}

                                        <div class="material-field">

                                            <label class="category-label">
                                                Lebar
                                            </label>

                                            <input type="number" name="materials[0][sizes][0][width]" class="input"
                                                placeholder="Masukkan lebar..." min="0" step="0.01" required>

                                        </div>


                                        {{-- PANJANG --}}

                                        <div class="material-field length-field" style="display: none;">

                                            <label class="category-label">
                                                Panjang
                                            </label>

                                            <input type="number" name="materials[0][sizes][0][length]" class="input"
                                                placeholder="Masukkan panjang..." min="0" step="0.01">

                                        </div>


                                        {{-- UNIT --}}

                                        <div class="material-field">

                                            <label class="category-label">
                                                Unit
                                            </label>

                                            <select name="materials[0][sizes][0][unit]" class="select select2" required>

                                                <option value="">
                                                    Pilih Unit
                                                </option>

                                                <option value="cm">
                                                    cm
                                                </option>

                                                <option value="m">
                                                    m
                                                </option>

                                            </select>

                                        </div>


                                        {{-- HAPUS SIZE --}}

                                        <button type="button" class="btn-remove-size" aria-label="Hapus ukuran">

                                            <i class="bi bi-trash3-fill"></i>

                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- MODAL FOOTER --}}
                {{-- ================================================= --}}

                <div class="modal-footer">

                    <button type="button" class="btn btn--ghost" id="btnBatalMaterial">
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


    {{-- Modal Edit Material --}}
    <div class="modal-overlay" id="modalEditMaterial">

        <div class="modal-dialog modal-dialog--wide">

            <form method="POST" id="formEditMaterial" novalidate>

                @csrf
                @method('PUT')

                <div class="modal-header">

                    <div>
                        <span class="eyebrow">
                            MASTER DATA · MATERIAL
                        </span>
                        <br>
                        <span class="eyebrow">
                            Edit Material
                        </span>
                    </div>

                    <button type="button" class="modal-close" id="btnTutupModalEditMaterial" aria-label="Tutup">
                        <svg viewBox="0 0 24 24">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>

                </div>


                <div class="modal-body">

                    {{-- ================================================= --}}
                    {{-- DATA MATERIAL --}}
                    {{-- ================================================= --}}

                    <div class="category-form-header">

                        <div>

                            <label class="category-label">
                                Data Material
                            </label>

                            <p class="category-description">
                                Ubah informasi material yang dipilih.
                            </p>

                        </div>

                    </div>


                    <div class="material-row">

                        {{-- ================================================= --}}
                        {{-- DATA MATERIAL --}}
                        {{-- ================================================= --}}

                        <div class="material-row-top">

                            {{-- KATEGORI --}}
                            <div class="material-field">

                                <label class="category-label">
                                    Kategori
                                </label>

                                <select name="category_id" id="editMaterialCategory" class="select select2" required>

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


                            {{-- KODE MATERIAL --}}
                            <div class="material-field">

                                <label class="category-label">
                                    Kode Material
                                </label>

                                <input type="text" name="material_code" id="editMaterialCode" class="input"
                                    placeholder="Masukkan kode material...">

                            </div>


                            {{-- NAMA MATERIAL --}}
                            <div class="material-field">

                                <label class="category-label">
                                    Nama Material
                                </label>

                                <input type="text" name="material_name" id="editMaterialName" class="input"
                                    placeholder="Masukkan nama material..." required>

                            </div>

                        </div>


                        {{-- ================================================= --}}
                        {{-- STATUS --}}
                        {{-- ================================================= --}}

                        <div class="material-row-bottom">

                            <select name="status" id="editMaterialStatus" class="select select2" required>

                                <option value="Active">
                                    Active
                                </option>

                                <option value="Inactive">
                                    Inactive
                                </option>

                            </select>

                        </div>


                        {{-- ================================================= --}}
                        {{-- MATERIAL SIZES --}}
                        {{-- ================================================= --}}

                        <div class="material-sizes">

                            <div class="material-sizes-header">

                                <div>

                                    <label class="category-label">
                                        Ukuran Material
                                    </label>

                                    <p class="category-description">
                                        Ubah, hapus, atau tambahkan ukuran material.
                                    </p>

                                </div>


                                <button type="button" class="btn btn--ghost" id="btnTambahUkuranEditMaterial">

                                    <svg viewBox="0 0 24 24">
                                        <path d="M12 5v14M5 12h14" />
                                    </svg>

                                    Tambah Ukuran

                                </button>

                            </div>


                            {{-- ================================================= --}}
                            {{-- SIZE ROWS --}}
                            {{-- ================================================= --}}

                            <div class="material-size-rows" id="editMaterialSizeRows">

                                {{-- Size akan diisi melalui JavaScript --}}

                            </div>

                        </div>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- MODAL FOOTER --}}
                {{-- ================================================= --}}

                <div class="modal-footer">

                    <button type="button" class="btn btn--ghost" id="btnBatalEditMaterial">
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
    <script src="{{ asset('js/material-index.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const btnImport = document.getElementById('btnImportMaterial');

            const modal = document.getElementById('modalImportMaterial');

            const btnClose = document.getElementById('btnCloseImportMaterial');

            const btnCancel = document.getElementById('btnCancelImportMaterial');

            const btnSave = document.getElementById('btnSaveImportMaterial');

            const fileInput = document.getElementById('fileImportMaterial');

            const fileName = document.getElementById('fileSelectedName');


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL
            |--------------------------------------------------------------------------
            */

            if (btnImport) {

                btnImport.addEventListener('click', function() {

                    modal.classList.add('is-open');

                });

            }


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function closeModal() {

                modal.classList.remove('is-open');

            }


            btnClose.addEventListener('click', closeModal);

            btnCancel.addEventListener('click', closeModal);


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modal.addEventListener('click', function(event) {

                if (event.target === modal) {

                    closeModal();

                }

            });


            /*
            |--------------------------------------------------------------------------
            | TEKAN ESC
            |--------------------------------------------------------------------------
            */

            document.addEventListener('keydown', function(event) {

                if (event.key === 'Escape') {

                    closeModal();

                }

            });


            /*
            |--------------------------------------------------------------------------
            | FILE DIPILIH
            |--------------------------------------------------------------------------
            */

            fileInput.addEventListener('change', function() {

                if (this.files.length > 0) {

                    fileName.textContent = this.files[0].name;

                    fileName.classList.add('has-file');

                } else {

                    fileName.textContent = 'Belum ada file dipilih';

                    fileName.classList.remove('has-file');

                }

            });


            /*
            |--------------------------------------------------------------------------
            | IMPORT DATA
            |--------------------------------------------------------------------------
            */

            btnSave.addEventListener('click', function() {


                /*
                |--------------------------------------------------------------------------
                | VALIDASI FILE
                |--------------------------------------------------------------------------
                */

                if (!fileInput.files.length) {

                    showToast(
                        'error',
                        'Silakan pilih file Excel terlebih dahulu.'
                    );

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | FORM DATA
                |--------------------------------------------------------------------------
                */

                const formData = new FormData();

                formData.append(
                    'file',
                    fileInput.files[0]
                );


                /*
                |--------------------------------------------------------------------------
                | DISABLE BUTTON
                |--------------------------------------------------------------------------
                */

                btnSave.disabled = true;

                btnSave.innerHTML = 'Mengimport...';


                /*
                |--------------------------------------------------------------------------
                | KIRIM KE CONTROLLER
                |--------------------------------------------------------------------------
                */

                fetch("{{ route('admin_import_material') }}", {

                        method: 'POST',

                        headers: {

                            'X-CSRF-TOKEN': "{{ csrf_token() }}",

                            'Accept': 'application/json'

                        },

                        body: formData

                    })


                    /*
                    |--------------------------------------------------------------------------
                    | RESPONSE
                    |--------------------------------------------------------------------------
                    */

                    .then(response => {

                        if (!response.ok) {

                            return response.json().then(error => {

                                throw error;

                            });

                        }

                        return response.json();

                    })


                    /*
                    |--------------------------------------------------------------------------
                    | SUCCESS
                    |--------------------------------------------------------------------------
                    */

                    .then(data => {

                        if (data.success) {


                            /*
                            |--------------------------------------------------------------------------
                            | BUAT DETAIL HASIL IMPORT
                            |--------------------------------------------------------------------------
                            */

                            let message = data.message;


                            if (data.skipped_duplicate > 0) {

                                message +=
                                    ` ${data.skipped_duplicate} data duplikat dilewati.`;

                            }


                            if (data.skipped_invalid > 0) {

                                message +=
                                    ` ${data.skipped_invalid} data tidak valid dilewati.`;

                            }


                            if (data.skipped_category > 0) {

                                message +=
                                    ` ${data.skipped_category} kategori tidak ditemukan.`;

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | TOAST SUCCESS
                            |--------------------------------------------------------------------------
                            */

                            showToast(
                                'success',
                                message
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | TUTUP MODAL
                            |--------------------------------------------------------------------------
                            */

                            closeModal();


                            /*
                            |--------------------------------------------------------------------------
                            | RESET FILE
                            |--------------------------------------------------------------------------
                            */

                            fileInput.value = '';

                            fileName.textContent =
                                'Belum ada file dipilih';

                            fileName.classList.remove('has-file');


                            /*
                            |--------------------------------------------------------------------------
                            | REFRESH DATATABLE
                            |--------------------------------------------------------------------------
                            */

                            if (
                                typeof materialTable !== 'undefined'
                            ) {

                                materialTable.ajax.reload(
                                    null,
                                    false
                                );

                            }

                        }

                    })


                    /*
                    |--------------------------------------------------------------------------
                    | ERROR
                    |--------------------------------------------------------------------------
                    */

                    .catch(error => {

                        console.error(error);


                        if (error.errors) {

                            const messages = Object.values(error.errors)

                                .flat()

                                .join('\n');


                            showToast(
                                'error',
                                messages
                            );

                        } else {

                            showToast(

                                'error',

                                error.message ||
                                'Terjadi kesalahan saat mengimport data.'

                            );

                        }

                    })


                    /*
                    |--------------------------------------------------------------------------
                    | AKTIFKAN BUTTON KEMBALI
                    |--------------------------------------------------------------------------
                    */

                    .finally(() => {

                        btnSave.disabled = false;

                        btnSave.innerHTML = `

                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8 5-5 5 5" />
                    </svg>

                    Import Data

                `;

                    });

            });

        });

        document.addEventListener('DOMContentLoaded', function() {
            /*
            |--------------------------------------------------------------------------
            | DELETE MATERIAL
            |--------------------------------------------------------------------------
            */

            let materialIdToDelete = null;

            const modalDeleteMaterial = document.getElementById(
                'modalDeleteMaterial'
            );

            const btnTutupDeleteMaterial = document.getElementById(
                'btnTutupDeleteMaterial'
            );

            const btnBatalDeleteMaterial = document.getElementById(
                'btnBatalDeleteMaterial'
            );

            const btnConfirmDeleteMaterial = document.getElementById(
                'btnConfirmDeleteMaterial'
            );


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL DELETE
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const deleteButton = event.target.closest(
                    '.btn-delete-material'
                );

                if (!deleteButton) {
                    return;
                }

                materialIdToDelete = deleteButton.dataset.id;

                modalDeleteMaterial.classList.add('is-open');

            });


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupDeleteMaterialModal() {

                modalDeleteMaterial.classList.remove('is-open');

                materialIdToDelete = null;

            }


            btnTutupDeleteMaterial.addEventListener(
                'click',
                tutupDeleteMaterialModal
            );


            btnBatalDeleteMaterial.addEventListener(
                'click',
                tutupDeleteMaterialModal
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modalDeleteMaterial.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalDeleteMaterial) {

                        tutupDeleteMaterialModal();

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
                        modalDeleteMaterial.classList.contains('is-open')
                    ) {

                        tutupDeleteMaterialModal();

                    }

                }
            );

            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI HAPUS MATERIAL
            |--------------------------------------------------------------------------
            */

            btnConfirmDeleteMaterial.addEventListener(
                'click',
                async function() {

                    /*
                    |--------------------------------------------------------------------------
                    | CEK ID MATERIAL
                    |--------------------------------------------------------------------------
                    */

                    if (!materialIdToDelete) {
                        return;
                    }


                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | REQUEST DELETE
                        |--------------------------------------------------------------------------
                        */

                        const response = await fetch(

                            `/admin/materials/${materialIdToDelete}/delete`,

                            {
                                method: 'DELETE',

                                headers: {

                                    'X-CSRF-TOKEN': document
                                        .querySelector(
                                            'meta[name="csrf-token"]'
                                        )
                                        .getAttribute('content'),

                                    'Accept': 'application/json'

                                }

                            }

                        );


                        /*
                        |--------------------------------------------------------------------------
                        | RESPONSE JSON
                        |--------------------------------------------------------------------------
                        */

                        const result = await response.json();


                        /*
                        |--------------------------------------------------------------------------
                        | BERHASIL
                        |--------------------------------------------------------------------------
                        */

                        if (response.ok && result.success) {

                            selectedMaterialIds.delete(Number(materialIdToDelete));

                            /*
                            |--------------------------------------------------------------------------
                            | TUTUP MODAL
                            |--------------------------------------------------------------------------
                            */

                            tutupDeleteMaterialModal();


                            /*
                            |--------------------------------------------------------------------------
                            | RELOAD DATATABLE
                            |--------------------------------------------------------------------------
                            */

                            materialTable.ajax.reload(
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
                                'Material gagal dihapus.'
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
                            'Terjadi kesalahan saat menghapus material.'
                        );

                    }

                }
            );
        })

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | VIEW MATERIAL
            |--------------------------------------------------------------------------
            */

            const modalViewMaterial = document.getElementById(
                'modalViewMaterial'
            );

            const btnTutupViewMaterial = document.getElementById(
                'btnTutupViewMaterial'
            );

            const btnCloseViewMaterial = document.getElementById(
                'btnCloseViewMaterial'
            );

            const viewMaterialSizeRows = document.getElementById(
                'viewMaterialSizeRows'
            );


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL
            |--------------------------------------------------------------------------
            */

            function bukaViewMaterial() {
                modalViewMaterial.classList.add('is-open');
            }


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupViewMaterial() {
                modalViewMaterial.classList.remove('is-open');
            }


            /*
            |--------------------------------------------------------------------------
            | RESET DATA
            |--------------------------------------------------------------------------
            */

            function resetViewMaterial() {

                document.getElementById(
                    'viewMaterialCategory'
                ).textContent = '-';

                document.getElementById(
                    'viewMaterialCode'
                ).textContent = '-';

                document.getElementById(
                    'viewMaterialName'
                ).textContent = '-';

                document.getElementById(
                    'viewMaterialStatus'
                ).textContent = '-';

                viewMaterialSizeRows.innerHTML = '';
            }


            /*
            |--------------------------------------------------------------------------
            | RENDER MATERIAL SIZES
            |--------------------------------------------------------------------------
            */

            function renderMaterialSizes(sizes) {

                viewMaterialSizeRows.innerHTML = '';

                /*
                |--------------------------------------------------------------------------
                | JIKA TIDAK ADA SIZE
                |--------------------------------------------------------------------------
                */

                if (!sizes || sizes.length === 0) {

                    const emptyRow = document.createElement('div');

                    emptyRow.className = 'material-size-row';

                    emptyRow.innerHTML = `
                        <div class="material-field">
                            <div class="eyebrow">
                                Ukuran
                            </div>

                            <div>
                                Tidak ada ukuran material.
                            </div>
                        </div>
                    `;

                    viewMaterialSizeRows.appendChild(emptyRow);

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | RENDER SETIAP SIZE
                |--------------------------------------------------------------------------
                */

                sizes.forEach(function(size) {

                    const row = document.createElement('div');

                    row.className = 'material-size-row';

                    /*
                    |--------------------------------------------------------------------------
                    | WIDTH
                    |--------------------------------------------------------------------------
                    */

                    const widthField = document.createElement('div');

                    widthField.className = 'material-field';

                    const widthLabel = document.createElement('div');

                    widthLabel.className = 'eyebrow';

                    widthLabel.textContent = 'Lebar';

                    const widthValue = document.createElement('div');

                    widthValue.textContent =
                        size.width ?? '-';

                    /*
                    |--------------------------------------------------------------------------
                    | LENGTH
                    |--------------------------------------------------------------------------
                    */

                    const lengthField = document.createElement('div');

                    lengthField.className = 'material-field';

                    const lengthLabel = document.createElement('div');

                    lengthLabel.className = 'eyebrow';

                    lengthLabel.textContent = 'Panjang';

                    const lengthValue = document.createElement('div');

                    // lengthValue.textContent =
                    //     size.length ?? '-';

                    lengthValue.textContent =
                        size.length !== null && size.length !== undefined ?
                        Number(size.length).toString() :
                        '-';

                    /*
                    |--------------------------------------------------------------------------
                    | UNIT
                    |--------------------------------------------------------------------------
                    */

                    const unitField = document.createElement('div');

                    unitField.className = 'material-field';

                    const unitLabel = document.createElement('div');

                    unitLabel.className = 'eyebrow';

                    unitLabel.textContent = 'Satuan';

                    const unitValue = document.createElement('div');

                    unitValue.textContent =
                        size.unit ?? '-';


                    /*
                    |--------------------------------------------------------------------------
                    | SUSUN FIELD
                    |--------------------------------------------------------------------------
                    */

                    widthField.appendChild(widthLabel);
                    widthField.appendChild(widthValue);

                    lengthField.appendChild(lengthLabel);
                    lengthField.appendChild(lengthValue);

                    unitField.appendChild(unitLabel);
                    unitField.appendChild(unitValue);

                    row.appendChild(widthField);
                    row.appendChild(lengthField);
                    row.appendChild(unitField);

                    viewMaterialSizeRows.appendChild(row);

                });
            }


            /*
            |--------------------------------------------------------------------------
            | TOMBOL CLOSE
            |--------------------------------------------------------------------------
            */

            btnTutupViewMaterial.addEventListener(
                'click',
                tutupViewMaterial
            );

            btnCloseViewMaterial.addEventListener(
                'click',
                tutupViewMaterial
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modalViewMaterial.addEventListener(
                'click',
                function(event) {

                    if (event.target === modalViewMaterial) {
                        tutupViewMaterial();
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

                    if (event.key === 'Escape') {
                        tutupViewMaterial();
                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK TOMBOL VIEW
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                async function(event) {

                    const button = event.target.closest(
                        '.btn-view-material'
                    );

                    if (!button) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL ID MATERIAL
                    |--------------------------------------------------------------------------
                    */

                    const materialId = button.dataset.id;


                    /*
                    |--------------------------------------------------------------------------
                    | RESET MODAL
                    |--------------------------------------------------------------------------
                    */

                    resetViewMaterial();


                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL DATA MATERIAL
                    |--------------------------------------------------------------------------
                    */

                    try {

                        const response = await fetch(
                            `/admin/materials/${materialId}/details`, {
                                method: 'GET',

                                headers: {
                                    'Accept': 'application/json'
                                }
                            }
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | RESPONSE JSON
                        |--------------------------------------------------------------------------
                        */

                        const result = await response.json();


                        /*
                        |--------------------------------------------------------------------------
                        | CEK RESPONSE
                        |--------------------------------------------------------------------------
                        */

                        if (
                            !response.ok ||
                            !result.success
                        ) {

                            showToast(
                                'error',
                                result.message ||
                                'Data material tidak ditemukan.'
                            );

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | ISI DATA MATERIAL
                        |--------------------------------------------------------------------------
                        */

                        document.getElementById(
                                'viewMaterialCategory'
                            ).textContent =
                            result.data.category_name ?? '-';


                        document.getElementById(
                                'viewMaterialCode'
                            ).textContent =
                            result.data.material_code ?? '-';


                        document.getElementById(
                                'viewMaterialName'
                            ).textContent =
                            result.data.material_name ?? '-';


                        document.getElementById(
                                'viewMaterialStatus'
                            ).textContent =
                            result.data.status ?? '-';


                        /*
                        |--------------------------------------------------------------------------
                        | ISI MATERIAL SIZES
                        |--------------------------------------------------------------------------
                        */

                        renderMaterialSizes(
                            result.data.sizes ?? []
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | BUKA MODAL
                        |--------------------------------------------------------------------------
                        */

                        bukaViewMaterial();

                    } catch (error) {

                        console.error(error);

                        showToast(
                            'error',
                            'Terjadi kesalahan saat mengambil data material.'
                        );
                    }

                }
            );

        });

        document.addEventListener('DOMContentLoaded', function() {

            const modal =
                document.getElementById('modalTambahMaterial');

            const btnTambah =
                document.getElementById('btnTambahMaterial');

            const btnTutup =
                document.getElementById('btnTutupModalMaterial');

            const btnBatal =
                document.getElementById('btnBatalMaterial');

            const btnTambahBaris =
                document.getElementById('btnTambahBarisMaterial');

            const materialRows =
                document.getElementById('materialRows');

            let materialIndex = 1;


            // =========================================================
            // VALIDASI ELEMENT
            // =========================================================

            if (
                !modal ||
                !btnTambah ||
                !btnTutup ||
                !btnBatal ||
                !btnTambahBaris ||
                !materialRows
            ) {
                return;
            }


            // =========================================================
            // INIT SELECT2
            // =========================================================

            function initSelect2(container) {

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
                                dropdownParent: $(modal)
                            });

                        }

                    });

            }

            initSelect2(materialRows);

            // =========================================================
            // VALIDASI FORM
            // =========================================================

            const formTambahMaterial =
                document.getElementById('formTambahMaterial');


            formTambahMaterial.addEventListener(
                'submit',
                function(event) {

                    // =====================================================
                    // RESET ERROR SELECT2
                    // =====================================================

                    $(materialRows)
                        .find('.select2')
                        .each(function() {

                            $(this)
                                .next('.select2-container')
                                .removeClass('select2-invalid');

                        });


                    // =====================================================
                    // VALIDASI FIELD BIASA
                    // =====================================================

                    const requiredFields =
                        materialRows.querySelectorAll(
                            'input[required], select[required]'
                        );


                    let firstInvalidField = null;


                    for (const field of requiredFields) {

                        /*
                        |--------------------------------------------------------------------------
                        | FIELD YANG SEDANG HIDDEN
                        |--------------------------------------------------------------------------
                        |
                        | Contoh:
                        | Panjang untuk kategori Outdoor / Indoor.
                        |
                        */

                        if (
                            field.offsetParent === null &&
                            !field.classList.contains('select2-hidden-accessible')
                        ) {
                            continue;
                        }


                        // =================================================
                        // SELECT2
                        // =================================================

                        if (
                            field.classList.contains(
                                'select2-hidden-accessible'
                            )
                        ) {

                            if (!$(field).val()) {

                                const select2Container =
                                    $(field).next(
                                        '.select2-container'
                                    );


                                select2Container.addClass(
                                    'select2-invalid'
                                );


                                if (!firstInvalidField) {
                                    firstInvalidField = field;
                                }

                                continue;
                            }

                            continue;
                        }


                        // =================================================
                        // INPUT BIASA
                        // =================================================

                        if (!field.value.trim()) {

                            if (!firstInvalidField) {
                                firstInvalidField = field;
                            }

                        }

                    }


                    // =====================================================
                    // ADA FIELD INVALID
                    // =====================================================

                    if (firstInvalidField) {

                        event.preventDefault();


                        // Jika Select2
                        if (
                            firstInvalidField.classList.contains(
                                'select2-hidden-accessible'
                            )
                        ) {

                            $(firstInvalidField)
                                .select2('open');

                            return;
                        }


                        // Input biasa
                        firstInvalidField.focus();

                        return;
                    }

                }
            );


            // =========================================================
            // DESTROY SELECT2
            // =========================================================

            function destroySelect2(container) {

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


            // =========================================================
            // CEK APAKAH KATEGORI DISPLAY
            // =========================================================

            function requiresLength(materialRow) {

                const categorySelect =
                    materialRow.querySelector(
                        'select[name*="[category_id]"]'
                    );

                if (!categorySelect) {
                    return false;
                }

                const selectedOption =
                    categorySelect.options[
                        categorySelect.selectedIndex
                    ];

                if (!selectedOption) {
                    return false;
                }

                const categoryName =
                    selectedOption.textContent
                    .trim()
                    .toLowerCase();

                return (
                    categoryName !== 'outdoor' &&
                    categoryName !== 'indoor'
                );
            }


            // =========================================================
            // UPDATE FIELD PANJANG
            //
            // Fungsi ini hanya membaca kondisi kategori.
            // Trigger utamanya berasal dari Select2 kategori.
            // =========================================================

            function updateLengthFields(materialRow) {

                const requiresLengthField =
                    requiresLength(materialRow);

                const lengthFields =
                    materialRow.querySelectorAll(
                        '.length-field'
                    );

                lengthFields.forEach(function(lengthField) {

                    const lengthInput =
                        lengthField.querySelector(
                            'input[name*="[length]"]'
                        );

                    if (!lengthInput) {
                        return;
                    }

                    if (requiresLengthField) {

                        lengthField.style.display = '';
                        lengthInput.required = true;

                    } else {

                        lengthField.style.display = 'none';
                        lengthInput.required = false;
                        lengthInput.value = '';

                    }

                });
            }

            // =========================================================
            // TRIGGER SELECT2 KATEGORI
            //
            // INI TRIGGER UTAMA.
            //
            // Ketika Select2 memilih Display:
            // -> updateLengthFields()
            //
            // Ketika Select2 memilih kategori lain:
            // -> updateLengthFields()
            //
            // Ketika Select2 di-clear:
            // -> updateLengthFields()
            // =========================================================

            $(materialRows).on(
                'change select2:select select2:clear',
                'select[name*="[category_id]"]',
                function() {

                    const materialRow =
                        this.closest('.material-row');

                    if (!materialRow) {
                        return;
                    }

                    updateLengthFields(materialRow);

                }
            );


            // =========================================================
            // BUKA MODAL
            // =========================================================

            btnTambah.addEventListener(
                'click',
                function(event) {

                    event.preventDefault();

                    modal.classList.add('is-open');

                }
            );


            // =========================================================
            // TUTUP MODAL
            // =========================================================

            function tutupModal() {

                modal.classList.remove('is-open');

            }


            btnTutup.addEventListener(
                'click',
                tutupModal
            );


            btnBatal.addEventListener(
                'click',
                tutupModal
            );


            // =========================================================
            // KLIK BACKGROUND
            // =========================================================

            modal.addEventListener(
                'click',
                function(event) {

                    if (event.target === modal) {

                        tutupModal();

                    }

                }
            );


            // =========================================================
            // ESC
            // =========================================================

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modal.classList.contains('is-open')
                    ) {

                        tutupModal();

                    }

                }
            );


            // =========================================================
            // TAMBAH MATERIAL
            // =========================================================

            btnTambahBaris.addEventListener(
                'click',
                function() {

                    const currentMaterialIndex =
                        materialIndex;


                    const row =
                        document.createElement('div');

                    row.classList.add(
                        'material-row'
                    );


                    row.innerHTML = `

                <div class="material-row-top">

                    <div class="material-field">

                        <label class="category-label">
                            Kategori
                        </label>

                        <select
                            name="materials[${currentMaterialIndex}][category_id]"
                            class="select select2"
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


                    <div class="material-field">

                        <label class="category-label">
                            Kode Material
                        </label>

                        <input
                            type="text"
                            name="materials[${currentMaterialIndex}][material_code]"
                            class="input"
                            placeholder="Masukkan kode material..."
                        >

                    </div>


                    <div class="material-field">

                        <label class="category-label">
                            Nama Material
                        </label>

                        <input
                            type="text"
                            name="materials[${currentMaterialIndex}][material_name]"
                            class="input"
                            placeholder="Masukkan nama material..."
                            required
                        >

                    </div>

                </div>


                <div class="material-row-bottom">

                    <select
                        name="materials[${currentMaterialIndex}][status]"
                        class="select select2"
                        required
                    >

                        <option
                            value="Active"
                            selected
                        >
                            Active
                        </option>

                        <option value="Inactive">
                            Inactive
                        </option>

                    </select>


                    <button
                        type="button"
                        class="btn-remove-category"
                        aria-label="Hapus material"
                    >

                        <i class="bi bi-trash3-fill"></i>

                    </button>

                </div>


                <div class="material-sizes">

                    <div class="material-sizes-header">

                        <div>

                            <label class="category-label">
                                Ukuran Material
                            </label>

                            <p class="category-description">
                                Tambahkan satu atau beberapa ukuran material.
                            </p>

                        </div>


                        <button
                            type="button"
                            class="btn btn--ghost btnTambahUkuran"
                        >

                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>

                            Tambah Ukuran

                        </button>

                    </div>


                    <div class="material-size-rows">

                        <div class="material-size-row">

                            <div class="material-field">

                                <label class="category-label">
                                    Lebar
                                </label>

                                <input
                                    type="number"
                                    name="materials[${currentMaterialIndex}][sizes][0][width]"
                                    class="input"
                                    placeholder="Masukkan lebar..."
                                    min="0"
                                    step="0.01"
                                    required
                                >

                            </div>


                            <div
                                class="material-field length-field"
                                style="display: none;"
                            >

                                <label class="category-label">
                                    Panjang
                                </label>

                                <input
                                    type="number"
                                    name="materials[${currentMaterialIndex}][sizes][0][length]"
                                    class="input"
                                    placeholder="Masukkan panjang..."
                                    min="0"
                                    step="0.01"
                                >

                            </div>


                            <div class="material-field">

                                <label class="category-label">
                                    Unit
                                </label>

                                <select
                                    name="materials[${currentMaterialIndex}][sizes][0][unit]"
                                    class="select select2"
                                    required
                                >

                                    <option value="">
                                        Pilih Unit
                                    </option>

                                    <option value="cm">
                                        cm
                                    </option>

                                    <option value="m">
                                        m
                                    </option>

                                </select>

                            </div>


                            <button
                                type="button"
                                class="btn-remove-size"
                                aria-label="Hapus ukuran"
                            >

                                <i class="bi bi-trash3-fill"></i>

                            </button>

                        </div>

                    </div>

                </div>

            `;


                    materialRows.appendChild(row);


                    // =====================================================
                    // INIT SELECT2 MATERIAL BARU
                    // =====================================================

                    initSelect2(row);


                    // =====================================================
                    // MATERIAL BARU BELUM PUNYA KATEGORI
                    // PANJANG TETAP HIDDEN
                    // =====================================================

                    updateLengthFields(row);


                    const materialNameInput =
                        row.querySelector(
                            'input[name*="[material_name]"]'
                        );

                    if (materialNameInput) {

                        materialNameInput.focus();

                    }


                    materialIndex++;

                }
            );


            // =========================================================
            // TAMBAH UKURAN
            //
            // TIDAK MENJADI TRIGGER KATEGORI.
            //
            // Hanya membuat row baru dan mengikuti kondisi kategori
            // yang sudah dipilih.
            // =========================================================

            materialRows.addEventListener(
                'click',
                function(event) {

                    const button =
                        event.target.closest(
                            '.btnTambahUkuran'
                        );

                    if (!button) {
                        return;
                    }


                    const materialRow =
                        button.closest(
                            '.material-row'
                        );

                    if (!materialRow) {
                        return;
                    }


                    const sizeRows =
                        materialRow.querySelector(
                            '.material-size-rows'
                        );

                    if (!sizeRows) {
                        return;
                    }


                    const materialRowsList =
                        Array.from(
                            materialRows.querySelectorAll(
                                '.material-row'
                            )
                        );


                    const currentMaterialIndex =
                        materialRowsList.indexOf(
                            materialRow
                        );


                    const sizeIndex =
                        sizeRows.querySelectorAll(
                            '.material-size-row'
                        ).length;


                    const row =
                        document.createElement('div');

                    row.classList.add(
                        'material-size-row'
                    );


                    row.innerHTML = `

                <div class="material-field">

                    <label class="category-label">
                        Lebar
                    </label>

                    <input
                        type="number"
                        name="materials[${currentMaterialIndex}][sizes][${sizeIndex}][width]"
                        class="input"
                        placeholder="Masukkan lebar..."
                        min="0"
                        step="0.01"
                        required
                    >

                </div>


                <div
                    class="material-field length-field"
                    style="display: none;"
                >

                    <label class="category-label">
                        Panjang
                    </label>

                    <input
                        type="number"
                        name="materials[${currentMaterialIndex}][sizes][${sizeIndex}][length]"
                        class="input"
                        placeholder="Masukkan panjang..."
                        min="0"
                        step="0.01"
                    >

                </div>


                <div class="material-field">

                    <label class="category-label">
                        Unit
                    </label>

                    <select
                        name="materials[${currentMaterialIndex}][sizes][${sizeIndex}][unit]"
                        class="select select2"
                        required
                    >

                        <option value="">
                            Pilih Unit
                        </option>

                        <option value="cm">
                            cm
                        </option>

                        <option value="m">
                            m
                        </option>

                    </select>

                </div>


                <button
                    type="button"
                    class="btn-remove-size"
                    aria-label="Hapus ukuran"
                >

                    <i class="bi bi-trash3-fill"></i>

                </button>

            `;


                    sizeRows.appendChild(row);


                    // =====================================================
                    // INIT SELECT2 ROW BARU
                    // =====================================================

                    initSelect2(row);


                    // =====================================================
                    // IKUTI KONDISI KATEGORI SAAT INI
                    //
                    // BUKAN TRIGGER UTAMA.
                    // =====================================================

                    updateLengthFields(
                        materialRow
                    );


                    const widthInput =
                        row.querySelector(
                            'input[name*="[width]"]'
                        );

                    if (widthInput) {

                        widthInput.focus();

                    }

                }
            );


            // =========================================================
            // HAPUS MATERIAL / SIZE
            // =========================================================

            materialRows.addEventListener(
                'click',
                function(event) {


                    // =====================================================
                    // HAPUS MATERIAL
                    // =====================================================

                    const removeMaterialButton =
                        event.target.closest(
                            '.btn-remove-category'
                        );


                    if (removeMaterialButton) {

                        const rows =
                            materialRows.querySelectorAll(
                                '.material-row'
                            );


                        // =================================================
                        // JIKA HANYA ADA SATU MATERIAL
                        // RESET SAJA
                        // =================================================

                        if (rows.length === 1) {

                            const firstRow =
                                rows[0];


                            // KATEGORI

                            const category =
                                firstRow.querySelector(
                                    'select[name*="[category_id]"]'
                                );

                            if (category) {

                                $(category)
                                    .val('')
                                    .trigger('change');

                            }


                            // KODE

                            const code =
                                firstRow.querySelector(
                                    'input[name*="[material_code]"]'
                                );

                            if (code) {

                                code.value = '';

                            }


                            // NAMA

                            const name =
                                firstRow.querySelector(
                                    'input[name*="[material_name]"]'
                                );

                            if (name) {

                                name.value = '';

                            }


                            // STATUS

                            const status =
                                firstRow.querySelector(
                                    'select[name*="[status]"]'
                                );

                            if (status) {

                                $(status)
                                    .val('Active')
                                    .trigger('change');

                            }


                            // RESET SIZE

                            const sizeRows =
                                firstRow.querySelectorAll(
                                    '.material-size-row'
                                );


                            sizeRows.forEach(
                                function(sizeRow, index) {

                                    if (index === 0) {

                                        const width =
                                            sizeRow.querySelector(
                                                'input[name*="[width]"]'
                                            );

                                        const length =
                                            sizeRow.querySelector(
                                                'input[name*="[length]"]'
                                            );


                                        if (width) {

                                            width.value = '';

                                        }


                                        if (length) {

                                            length.value = '';

                                        }


                                        const unit =
                                            sizeRow.querySelector(
                                                'select[name*="[unit]"]'
                                            );


                                        if (unit) {

                                            $(unit)
                                                .val('')
                                                .trigger('change');

                                        }

                                    } else {

                                        destroySelect2(
                                            sizeRow
                                        );

                                        sizeRow.remove();

                                    }

                                }
                            );


                            // Pastikan Panjang hidden

                            updateLengthFields(
                                firstRow
                            );


                            if (name) {

                                name.focus();

                            }


                            return;

                        }


                        // =================================================
                        // HAPUS MATERIAL NORMAL
                        // =================================================

                        const materialRow =
                            removeMaterialButton.closest(
                                '.material-row'
                            );


                        if (materialRow) {

                            destroySelect2(
                                materialRow
                            );

                            materialRow.remove();

                        }


                        return;

                    }


                    // =====================================================
                    // HAPUS SIZE
                    // =====================================================

                    const removeSizeButton =
                        event.target.closest(
                            '.btn-remove-size'
                        );


                    if (!removeSizeButton) {

                        return;

                    }


                    const sizeRow =
                        removeSizeButton.closest(
                            '.material-size-row'
                        );


                    if (!sizeRow) {

                        return;

                    }


                    const sizeRows =
                        sizeRow.parentElement;


                    const rows =
                        sizeRows.querySelectorAll(
                            '.material-size-row'
                        );


                    // =====================================================
                    // JIKA TINGGAL SATU SIZE
                    // =====================================================

                    if (rows.length === 1) {

                        const width =
                            rows[0].querySelector(
                                'input[name*="[width]"]'
                            );

                        const length =
                            rows[0].querySelector(
                                'input[name*="[length]"]'
                            );


                        if (width) {

                            width.value = '';

                        }


                        if (length) {

                            length.value = '';

                        }


                        const unit =
                            rows[0].querySelector(
                                'select[name*="[unit]"]'
                            );


                        if (unit) {

                            $(unit)
                                .val('')
                                .trigger('change');

                        }


                        if (width) {

                            width.focus();

                        }


                        return;

                    }


                    destroySelect2(
                        sizeRow
                    );

                    sizeRow.remove();

                }
            );

        });

        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | ELEMENT
            |--------------------------------------------------------------------------
            */

            const modalEditMaterial =
                document.getElementById('modalEditMaterial');

            const formEditMaterial =
                document.getElementById('formEditMaterial');

            const btnTutup =
                document.getElementById('btnTutupModalEditMaterial');

            const btnBatal =
                document.getElementById('btnBatalEditMaterial');

            const btnTambahUkuran =
                document.getElementById('btnTambahUkuranEditMaterial');

            const category =
                document.getElementById('editMaterialCategory');

            const code =
                document.getElementById('editMaterialCode');

            const name =
                document.getElementById('editMaterialName');

            const status =
                document.getElementById('editMaterialStatus');

            const sizeRows =
                document.getElementById('editMaterialSizeRows');


            /*
            |--------------------------------------------------------------------------
            | CEK ELEMENT
            |--------------------------------------------------------------------------
            */

            if (
                !modalEditMaterial ||
                !formEditMaterial ||
                !sizeRows
            ) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | SELECT2
            |--------------------------------------------------------------------------
            */

            function initSelect2(container) {

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
                                dropdownParent: $(modalEditMaterial)
                            });

                        }

                    });

            }


            initSelect2(formEditMaterial);


            /*
            |--------------------------------------------------------------------------
            | DESTROY SELECT2
            |--------------------------------------------------------------------------
            */

            function destroySelect2(container) {

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
            |--------------------------------------------------------------------------
            | CEK APAKAH LENGTH DIPERLUKAN
            |--------------------------------------------------------------------------
            |
            | Outdoor  -> tidak perlu length
            | Indoor   -> tidak perlu length
            | Lainnya  -> wajib length
            |
            */

            function requiresLength() {

                if (!category) {
                    return false;
                }


                const selectedOption =
                    category.options[
                        category.selectedIndex
                    ];


                if (!selectedOption) {
                    return false;
                }


                const categoryName =
                    selectedOption.textContent
                    .trim()
                    .toLowerCase();


                return (
                    categoryName !== 'outdoor' &&
                    categoryName !== 'indoor'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE FIELD PANJANG
            |--------------------------------------------------------------------------
            */

            function updateLengthFields() {

                const isLengthRequired =
                    requiresLength();


                const rows =
                    sizeRows.querySelectorAll(
                        '.material-size-row'
                    );


                rows.forEach(function(row) {

                    const lengthField =
                        row.querySelector(
                            '.length-field'
                        );


                    const lengthInput =
                        row.querySelector(
                            'input[name*="[length]"]'
                        );


                    if (
                        !lengthField ||
                        !lengthInput
                    ) {
                        return;
                    }


                    if (isLengthRequired) {

                        /*
                        |--------------------------------------------------------------------------
                        | LENGTH DIBUTUHKAN
                        |--------------------------------------------------------------------------
                        */

                        lengthField.style.display = '';

                        lengthInput.required = true;


                    } else {

                        /*
                        |--------------------------------------------------------------------------
                        | LENGTH TIDAK DIBUTUHKAN
                        |--------------------------------------------------------------------------
                        */

                        lengthField.style.display = 'none';

                        lengthInput.required = false;

                        lengthInput.value = '';

                    }

                });

            }


            /*
            |--------------------------------------------------------------------------
            | CATEGORY CHANGE
            |--------------------------------------------------------------------------
            */

            if (category) {

                $(category).on(
                    'change select2:select select2:clear',
                    function() {

                        updateLengthFields();

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | RESET SIZE ROWS
            |--------------------------------------------------------------------------
            */

            function resetSizeRows() {

                destroySelect2(sizeRows);

                sizeRows.innerHTML = '';

            }


            /*
            |--------------------------------------------------------------------------
            | RESET DELETED SIZES
            |--------------------------------------------------------------------------
            */

            function resetDeletedSizes() {

                formEditMaterial
                    .querySelectorAll(
                        'input[name="deleted_sizes[]"]'
                    )
                    .forEach(function(input) {

                        input.remove();

                    });

            }


            /*
            |--------------------------------------------------------------------------
            | CREATE SIZE ROW
            |--------------------------------------------------------------------------
            */

            function createSizeRow(size, index) {

                const row =
                    document.createElement('div');


                row.classList.add(
                    'material-size-row'
                );


                /*
                |--------------------------------------------------------------------------
                | SIZE ID
                |--------------------------------------------------------------------------
                */

                const sizeId =
                    size &&
                    size.id ?
                    size.id :
                    '';


                /*
                |--------------------------------------------------------------------------
                | WIDTH
                |--------------------------------------------------------------------------
                */

                const width =
                    size &&
                    size.width !== null &&
                    size.width !== undefined ?
                    size.width :
                    '';


                /*
                |--------------------------------------------------------------------------
                | LENGTH
                |--------------------------------------------------------------------------
                */

                const length =
                    size &&
                    size.length !== null &&
                    size.length !== undefined ?
                    size.length :
                    '';


                /*
                |--------------------------------------------------------------------------
                | UNIT
                |--------------------------------------------------------------------------
                */

                const unit =
                    size &&
                    size.unit ?
                    size.unit :
                    '';


                /*
                |--------------------------------------------------------------------------
                | HTML SIZE ROW
                |--------------------------------------------------------------------------
                */

                row.innerHTML = `

            <input
                type="hidden"
                name="sizes[${index}][id]"
                value="${sizeId}"
            >


            {{-- WIDTH --}}

            <div class="material-field">

                <label class="category-label">
                    Lebar
                </label>

                <input
                    type="number"
                    name="sizes[${index}][width]"
                    class="input"
                    placeholder="Masukkan lebar..."
                    min="0"
                    step="0.01"
                    value="${width}"
                    required
                >

            </div>


            {{-- LENGTH --}}

            <div
                class="material-field length-field"
                style="display: none;"
            >

                <label class="category-label">
                    Panjang
                </label>

                <input
                    type="number"
                    name="sizes[${index}][length]"
                    class="input"
                    placeholder="Masukkan panjang..."
                    min="0"
                    step="0.01"
                    value="${length}"
                >

            </div>


            {{-- UNIT --}}

            <div class="material-field">

                <label class="category-label">
                    Unit
                </label>

                <select
                    name="sizes[${index}][unit]"
                    class="select select2"
                    required
                >

                    <option value="">
                        Pilih Unit
                    </option>

                    <option
                        value="cm"
                        ${unit === 'cm' ? 'selected' : ''}
                    >
                        cm
                    </option>

                    <option
                        value="m"
                        ${unit === 'm' ? 'selected' : ''}
                    >
                        m
                    </option>

                </select>

            </div>


            {{-- HAPUS SIZE --}}

            <button
                type="button"
                class="btn-remove-size"
                aria-label="Hapus ukuran"
            >

                <i class="bi bi-trash3-fill"></i>

            </button>

        `;


                /*
                |--------------------------------------------------------------------------
                | APPEND
                |--------------------------------------------------------------------------
                */

                sizeRows.appendChild(row);


                /*
                |--------------------------------------------------------------------------
                | INIT SELECT2
                |--------------------------------------------------------------------------
                */

                initSelect2(row);


                /*
                |--------------------------------------------------------------------------
                | UPDATE LENGTH
                |--------------------------------------------------------------------------
                */

                updateLengthFields();


                return row;

            }


            /*
            |--------------------------------------------------------------------------
            | LOAD SIZE DATA
            |--------------------------------------------------------------------------
            */

            function loadSizes(sizes) {

                resetSizeRows();


                /*
                |--------------------------------------------------------------------------
                | JIKA TIDAK ADA SIZE
                |--------------------------------------------------------------------------
                */

                if (
                    !Array.isArray(sizes) ||
                    sizes.length === 0
                ) {

                    createSizeRow({}, 0);

                    return;

                }


                /*
                |--------------------------------------------------------------------------
                | BUAT SETIAP SIZE
                |--------------------------------------------------------------------------
                */

                sizes.forEach(
                    function(size, index) {

                        createSizeRow(
                            size,
                            index
                        );

                    }
                );


                updateLengthFields();

            }


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL & AMBIL DATA
            |--------------------------------------------------------------------------
            */

            $(document).on(
                'click',
                '.btn-edit-material',
                function(event) {

                    event.preventDefault();


                    const id =
                        this.dataset.id;


                    if (!id) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | URL EDIT
                    |--------------------------------------------------------------------------
                    */

                    const url =
                        "{{ route('admin_edit_material', ':id') }}"
                        .replace(':id', id);


                    /*
                    |--------------------------------------------------------------------------
                    | RESET FORM
                    |--------------------------------------------------------------------------
                    */

                    resetDeletedSizes();


                    $(category)
                        .val('')
                        .trigger('change');


                    $(code)
                        .val('');


                    $(name)
                        .val('');


                    $(status)
                        .val('Active')
                        .trigger('change');


                    resetSizeRows();


                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL DATA
                    |--------------------------------------------------------------------------
                    */

                    fetch(
                            url, {
                                method: 'GET',

                                headers: {
                                    'Accept': 'application/json',

                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }
                        )

                        .then(function(response) {

                            if (!response.ok) {

                                throw new Error(
                                    'Data material gagal dimuat.'
                                );

                            }

                            return response.json();

                        })


                        .then(function(material) {

                            /*
                            |--------------------------------------------------------------------------
                            | ISI DATA MATERIAL
                            |--------------------------------------------------------------------------
                            */

                            $(category)
                                .val(material.category_id)
                                .trigger('change');


                            $(code)
                                .val(
                                    material.material_code ?? ''
                                );


                            $(name)
                                .val(
                                    material.material_name ?? ''
                                );


                            $(status)
                                .val(material.status)
                                .trigger('change');


                            /*
                            |--------------------------------------------------------------------------
                            | LOAD SIZES
                            |--------------------------------------------------------------------------
                            */

                            loadSizes(
                                material.sizes ?? []
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | SET FORM ACTION
                            |--------------------------------------------------------------------------
                            */

                            formEditMaterial.action =
                                "{{ route('admin_update_material', ':id') }}"
                                .replace(':id', id);


                            /*
                            |--------------------------------------------------------------------------
                            | BUKA MODAL
                            |--------------------------------------------------------------------------
                            */

                            modalEditMaterial.classList.add(
                                'is-open'
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | FOCUS
                            |--------------------------------------------------------------------------
                            */

                            setTimeout(
                                function() {

                                    $(name).trigger(
                                        'focus'
                                    );

                                },
                                100
                            );

                        })


                        .catch(function(error) {

                            console.error(
                                'Edit Material Error:',
                                error
                            );


                            alert(
                                'Data material gagal dimuat.'
                            );

                        });

                }
            );


            /*
            |--------------------------------------------------------------------------
            | TAMBAH UKURAN
            |--------------------------------------------------------------------------
            */

            if (btnTambahUkuran) {

                btnTambahUkuran.addEventListener(
                    'click',
                    function() {

                        const index =
                            sizeRows.querySelectorAll(
                                '.material-size-row'
                            ).length;


                        const row =
                            createSizeRow({},
                                index
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | FOCUS WIDTH
                        |--------------------------------------------------------------------------
                        */

                        const widthInput =
                            row.querySelector(
                                'input[name*="[width]"]'
                            );


                        if (widthInput) {

                            widthInput.focus();

                        }

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | HAPUS SIZE
            |--------------------------------------------------------------------------
            */

            sizeRows.addEventListener(
                'click',
                function(event) {

                    const removeButton =
                        event.target.closest(
                            '.btn-remove-size'
                        );


                    if (!removeButton) {
                        return;
                    }


                    const row =
                        removeButton.closest(
                            '.material-size-row'
                        );


                    if (!row) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | AMBIL SEMUA ROW
                    |--------------------------------------------------------------------------
                    */

                    const rows =
                        sizeRows.querySelectorAll(
                            '.material-size-row'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | ID SIZE
                    |--------------------------------------------------------------------------
                    */

                    const sizeIdInput =
                        row.querySelector(
                            'input[name$="[id]"]'
                        );


                    const sizeId =
                        sizeIdInput ?
                        sizeIdInput.value :
                        '';


                    /*
                    |--------------------------------------------------------------------------
                    | JIKA HANYA TERSISA SATU SIZE
                    |--------------------------------------------------------------------------
                    */

                    if (rows.length === 1) {

                        /*
                        |--------------------------------------------------------------------------
                        | SIZE LAMA → TANDAI DELETED
                        |--------------------------------------------------------------------------
                        */

                        if (sizeId) {

                            const deletedInput =
                                document.createElement(
                                    'input'
                                );


                            deletedInput.type =
                                'hidden';


                            deletedInput.name =
                                'deleted_sizes[]';


                            deletedInput.value =
                                sizeId;


                            formEditMaterial.appendChild(
                                deletedInput
                            );

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | RESET WIDTH
                        |--------------------------------------------------------------------------
                        */

                        const width =
                            row.querySelector(
                                'input[name*="[width]"]'
                            );


                        if (width) {
                            width.value = '';
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | RESET LENGTH
                        |--------------------------------------------------------------------------
                        */

                        const length =
                            row.querySelector(
                                'input[name*="[length]"]'
                            );


                        if (length) {
                            length.value = '';
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | RESET UNIT
                        |--------------------------------------------------------------------------
                        */

                        const unit =
                            row.querySelector(
                                'select[name*="[unit]"]'
                            );


                        if (unit) {

                            $(unit)
                                .val('')
                                .trigger('change');

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | HAPUS ID
                        |--------------------------------------------------------------------------
                        */

                        if (sizeIdInput) {

                            sizeIdInput.value = '';

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | FOCUS WIDTH
                        |--------------------------------------------------------------------------
                        */

                        if (width) {

                            width.focus();

                        }


                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SIZE LAMA → TANDAI DELETED
                    |--------------------------------------------------------------------------
                    */

                    if (sizeId) {

                        const deletedInput =
                            document.createElement(
                                'input'
                            );


                        deletedInput.type =
                            'hidden';


                        deletedInput.name =
                            'deleted_sizes[]';


                        deletedInput.value =
                            sizeId;


                        formEditMaterial.appendChild(
                            deletedInput
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | DESTROY SELECT2
                    |--------------------------------------------------------------------------
                    */

                    destroySelect2(row);


                    /*
                    |--------------------------------------------------------------------------
                    | HAPUS ROW
                    |--------------------------------------------------------------------------
                    */

                    row.remove();

                }
            );


            /*
            |--------------------------------------------------------------------------
            | VALIDASI FORM
            |--------------------------------------------------------------------------
            |
            | Sama seperti Create:
            | browser native validation tidak digunakan
            | untuk menghindari masalah fokus Select2.
            |
            */

            formEditMaterial.addEventListener(
                'submit',
                function(event) {

                    /*
                    |--------------------------------------------------------------------------
                    | RESET ERROR SELECT2
                    |--------------------------------------------------------------------------
                    */

                    $(formEditMaterial)
                        .find('.select2')
                        .each(function() {

                            $(this)
                                .next('.select2-container')
                                .removeClass(
                                    'select2-invalid'
                                );

                        });


                    let firstInvalidField = null;


                    /*
                    |--------------------------------------------------------------------------
                    | REQUIRED FIELDS
                    |--------------------------------------------------------------------------
                    */

                    const requiredFields =
                        formEditMaterial.querySelectorAll(
                            'input[required], select[required]'
                        );


                    for (
                        const field of requiredFields
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | SELECT2
                        |--------------------------------------------------------------------------
                        */

                        if (
                            field.classList.contains(
                                'select2-hidden-accessible'
                            )
                        ) {

                            if (!$(field).val()) {

                                const select2Container =
                                    $(field).next(
                                        '.select2-container'
                                    );


                                select2Container.addClass(
                                    'select2-invalid'
                                );


                                if (!firstInvalidField) {

                                    firstInvalidField =
                                        field;

                                }

                            }


                            continue;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | FIELD HIDDEN
                        |--------------------------------------------------------------------------
                        */

                        if (
                            field.offsetParent === null
                        ) {

                            continue;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | INPUT BIASA
                        |--------------------------------------------------------------------------
                        */

                        if (
                            !field.value.trim()
                        ) {

                            if (!firstInvalidField) {

                                firstInvalidField =
                                    field;

                            }

                        }

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | ADA ERROR
                    |--------------------------------------------------------------------------
                    */

                    if (firstInvalidField) {

                        event.preventDefault();


                        /*
                        |--------------------------------------------------------------------------
                        | SELECT2
                        |--------------------------------------------------------------------------
                        */

                        if (
                            firstInvalidField.classList.contains(
                                'select2-hidden-accessible'
                            )
                        ) {

                            $(firstInvalidField)
                                .select2('open');


                            return;

                        }


                        /*
                        |--------------------------------------------------------------------------
                        | INPUT BIASA
                        |--------------------------------------------------------------------------
                        */

                        firstInvalidField.focus();

                        return;

                    }

                }
            );


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupModalEditMaterial() {

                modalEditMaterial.classList.remove(
                    'is-open'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | TOMBOL TUTUP
            |--------------------------------------------------------------------------
            */

            if (btnTutup) {

                btnTutup.addEventListener(
                    'click',
                    tutupModalEditMaterial
                );

            }


            /*
            |--------------------------------------------------------------------------
            | TOMBOL BATAL
            |--------------------------------------------------------------------------
            */

            if (btnBatal) {

                btnBatal.addEventListener(
                    'click',
                    tutupModalEditMaterial
                );

            }


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKDROP
            |--------------------------------------------------------------------------
            */

            modalEditMaterial.addEventListener(
                'click',
                function(event) {

                    if (
                        event.target ===
                        modalEditMaterial
                    ) {

                        tutupModalEditMaterial();

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
                        modalEditMaterial.classList.contains(
                            'is-open'
                        )
                    ) {

                        tutupModalEditMaterial();

                    }

                }
            );

        });
    </script>
@endsection

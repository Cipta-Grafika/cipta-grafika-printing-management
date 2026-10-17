@extends('admin_master')

@section('contents')
    <style>
        .material-row-top--cutting {
            display: grid;
            grid-template-columns: minmax(260px, 2fr) minmax(160px, 1fr);
            gap: 16px;
            align-items: end;
        }

        @media (max-width: 768px) {
            .material-row-top--cutting {
                grid-template-columns: 1fr;
            }
        }

        /* DETAIL JASA POTONG */
        #modalDetailCutting .modal-dialog--wide {
            width: min(900px, calc(100vw - 40px));
            max-width: 900px;
        }

        #modalDetailCutting .modal-body {
            padding: 28px;
        }

        #modalDetailCutting .cutting-detail-section {
            margin-bottom: 24px;
        }

        #modalDetailCutting .cutting-detail-section:last-child {
            margin-bottom: 0;
        }

        #modalDetailCutting .cutting-detail-section-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        #modalDetailCutting .cutting-main-fields {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 180px;
            gap: 16px;
        }

        #modalDetailCutting .cutting-field {
            min-width: 0;
        }

        #modalDetailCutting .detail-value {
            min-height: 46px;
            display: flex;
            align-items: center;
            padding: 0 15px;
            border: 1px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
        }

        #modalDetailCutting #detailCuttingStatus {
            justify-content: center;
            font-weight: 700;
        }

        #modalDetailCutting .cutting-detail-table-wrapper {
            width: 100%;
            overflow-x: auto;
            border: 1px solid var(--border);
            border-radius: 12px;
        }

        #modalDetailCutting .cutting-detail-table {
            width: 100%;
            min-width: 500px;
            border-collapse: collapse;
        }

        #modalDetailCutting .cutting-detail-table thead th {
            padding: 13px 16px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            border-bottom: 1px solid var(--border);
            background: var(--bg-card);
            white-space: nowrap;
        }

        #modalDetailCutting .cutting-detail-table tbody td {
            padding: 13px 16px;
            font-size: 13px;
        }

        #modalDetailCutting .cutting-detail-table tbody tr {
            transition: background 0.15s ease;
        }

        #modalDetailCutting .cutting-detail-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.025);
        }

        #modalDetailCutting .cutting-detail-empty {
            padding: 30px 16px !important;
            text-align: center !important;
            opacity: 0.55;
        }

        #modalDetailCutting .cutting-audit-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        #modalDetailCutting .cutting-audit-grid .detail-value {
            font-size: 13px;
        }

        @media (max-width: 640px) {

            #modalDetailCutting .cutting-main-fields,
            #modalDetailCutting .cutting-audit-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">MASTER DATA · JASA POTONG</span>
                        <p class="hero-sub">Kelola data jasa potong yang tersedia untuk digunakan pada proses produksi dan
                            perhitungan estimasi harga.</p>
                    </div>
                    <div class="hero-actions">
                        <a href="{{ route('admin_export_cuttings') }}" class="btn btn--ghost" id="btnExportCutting">
                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>
                            Export
                        </a>
                        <a href="{{ asset('templates/template_jasa_potong.xlsx') }}" class="btn btn--ghost">
                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>
                            Download Template
                        </a>
                        <button type="button" class="btn btn--ghost" id="btnImportCutting">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg>
                            Import
                        </button>
                        <button type="button" class="btn btn--primary" id="btnTambahCutting">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Jasa Potong
                        </button>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="cuttingTable" class="display data-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Nama Jasa Potong</th>
                                    <th style="text-align: center;">Total Ukuran</th>
                                    <th>Status</th>
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

    {{-- ========================================================= --}}
    {{-- MODAL TAMBAH JASA POTONG                                  --}}
    {{-- ========================================================= --}}

    <div class="modal-overlay" id="modalTambahCutting">
        <div class="modal-dialog modal-dialog--wide">

            <div class="modal-header">
                <div>
                    <h3 class="modal-title">
                        Tambah Jasa Potong
                    </h3>

                    <p class="modal-description">
                        Tambahkan satu atau beberapa data jasa potong.
                    </p>
                </div>

                <button type="button" class="modal-close" id="btnTutupModalCutting" aria-label="Tutup">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form id="formTambahCutting" action="{{ route('admin_store_cutting') }}" method="POST">
                @csrf

                <div class="modal-body">

                    <div class="modal-section-header" style="margin-bottom: 20px;">

                        <div>
                            <h4 class="modal-section-title">
                                Data Jasa Potong
                            </h4>

                            <p class="modal-section-description">
                                Masukkan informasi jasa potong dan ukuran yang tersedia.
                            </p>
                        </div>

                        <button type="button" class="btn btn--ghost" id="btnTambahBarisCutting">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 5v14"></path>
                                <path d="M5 12h14"></path>
                            </svg>

                            Tambah Jasa Potong
                        </button>

                    </div>

                    <div id="cuttingRows">

                        <!-- ========================= -->
                        <!-- JASA POTONG PERTAMA       -->
                        <!-- ========================= -->

                        <div class="material-row" data-cutting-index="0" data-size-index="1">

                            <div style="display: flex; justify-content: right; align-items: center;">
                                <!-- HAPUS -->

                                <div class="material-field material-field--action">

                                    <button type="button" class="btn-remove-category" aria-label="Hapus jasa potong"
                                        style="border: none !important;">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>

                                </div>
                            </div>

                            <!-- DATA JASA POTONG -->

                            <div class="material-row-top material-row-top--cutting">

                                <!-- NAMA -->

                                <div class="material-field">

                                    <label class="category-label">
                                        Nama Jasa Potong
                                    </label>

                                    <input type="text" name="cuttings[0][name]" class="input"
                                        placeholder="Masukkan nama jasa potong..." required>

                                </div>


                                <!-- STATUS -->

                                <div class="material-field">

                                    <label class="category-label">
                                        Status
                                    </label>

                                    <select name="cuttings[0][status]" class="select select2" required>
                                        <option value="Active" selected>
                                            Active
                                        </option>

                                        <option value="Inactive">
                                            Inactive
                                        </option>
                                    </select>

                                </div>
                            </div>


                            <!-- ========================= -->
                            <!-- UKURAN JASA POTONG        -->
                            <!-- ========================= -->

                            <div class="material-sizes">

                                <div class="material-sizes-header">

                                    <div>

                                        <label class="category-label">
                                            Ukuran Jasa Potong
                                        </label>

                                        <p class="category-description">
                                            Tambahkan satu atau beberapa ukuran jasa potong.
                                        </p>

                                    </div>

                                    <button type="button" class="btn btn--ghost btnTambahUkuran">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 5v14"></path>
                                            <path d="M5 12h14"></path>
                                        </svg>

                                        Tambah Ukuran
                                    </button>

                                </div>


                                <div class="material-size-rows">

                                    <div class="material-size-row">

                                        <!-- LEBAR -->

                                        <div class="material-field">

                                            <label class="category-label">
                                                Lebar
                                            </label>

                                            <input type="number" name="cuttings[0][sizes][0][width]" class="input"
                                                placeholder="Masukkan lebar..." min="0" step="0.01" required>

                                        </div>


                                        <!-- PANJANG -->

                                        <div class="material-field">

                                            <label class="category-label">
                                                Panjang
                                            </label>

                                            <input type="number" name="cuttings[0][sizes][0][length]" class="input"
                                                placeholder="Masukkan panjang..." min="0" step="0.01">

                                        </div>


                                        <!-- UNIT -->

                                        <div class="material-field">

                                            <label class="category-label">
                                                Unit
                                            </label>

                                            <select name="cuttings[0][sizes][0][unit]" class="select select2" required>
                                                <option value="">
                                                    Pilih Unit
                                                </option>

                                                <option value="cm" selected>
                                                    cm
                                                </option>

                                                <option value="m">
                                                    m
                                                </option>
                                            </select>

                                        </div>


                                        <!-- HAPUS UKURAN -->

                                        <button type="button" class="btn-remove-size" aria-label="Hapus ukuran">
                                            <i class="bi bi-trash3-fill"></i>
                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- FOOTER -->

                <div class="modal-footer">
                    <button type="button" class="btn btn--secondary" id="btnBatalCutting">
                        Batal
                    </button>

                    <button type="submit" class="btn btn--primary">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL EDIT JASA POTONG                                    --}}
    {{-- ========================================================= --}}

    <div class="modal-overlay" id="modalEditCutting">
        <div class="modal-dialog modal-dialog--wide">
            <form method="POST" action="" id="formEditCutting" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editCuttingId">

                {{-- MODAL HEADER --}}
                <div class="modal-header">
                    <div>
                        <span class="eyebrow">
                            MASTER DATA · JASA POTONG
                        </span>
                        <br>
                        <span class="eyebrow">
                            Edit Jasa Potong
                        </span>
                    </div>

                    <button type="button" class="modal-close" id="btnTutupModalEditCutting" aria-label="Tutup">
                        <svg viewBox="0 0 24 24">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                {{-- MODAL BODY --}}
                <div class="modal-body">
                    <div class="category-form-header">
                        <div>
                            <label class="category-label">
                                Data Jasa Potong
                            </label>
                            <p class="category-description">
                                Ubah informasi jasa potong.
                            </p>
                        </div>
                    </div>

                    <div id="editCuttingRows">
                        <div class="material-row">
                            {{-- DATA UTAMA --}}
                            <div class="material-row-top material-row-top--cutting">
                                {{-- NAMA --}}
                                <div class="material-field">
                                    <label class="category-label">
                                        Nama Jasa Potong
                                    </label>
                                    <input type="text" name="name" id="editCuttingName" class="input"
                                        placeholder="Masukkan nama jasa potong..." autocomplete="off" required>
                                </div>

                                {{-- STATUS --}}
                                <div class="material-field">
                                    <label class="category-label">
                                        Status
                                    </label>
                                    <select name="status" id="editCuttingStatus" class="select select2" required>
                                        <option value="Active">
                                            Active
                                        </option>
                                        <option value="Inactive">
                                            Inactive
                                        </option>
                                    </select>
                                </div>
                            </div>

                            {{-- UKURAN --}}
                            <div class="material-sizes">
                                <div class="material-sizes-header">
                                    <div>
                                        <label class="category-label">
                                            Ukuran Jasa Potong
                                        </label>
                                        <p class="category-description">
                                            Atur satu atau beberapa ukuran jasa potong.
                                        </p>
                                    </div>

                                    <button type="button" class="btn btn--ghost btnTambahUkuranEdit">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 5v14" />
                                            <path d="M5 12h14" />
                                        </svg>
                                        Tambah Ukuran
                                    </button>
                                </div>

                                <div class="material-size-rows">
                                    {{-- Row ukuran dibuat oleh JavaScript --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MODAL FOOTER --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn--ghost" id="btnBatalEditCutting">
                        Batal
                    </button>

                    <button type="submit" class="btn btn--primary" id="btnSimpanEditCutting">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12l4 4L19 6" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================================= --}}
    {{-- MODAL DELETE JASA POTONG                                  --}}
    {{-- ========================================================= --}}

    <div class="modal-overlay" id="modalDeleteCutting">
        <div class="modal-dialog modal-delete">
            <!-- HEADER -->
            <div class="modal-header">
                <div>
                    <span class="eyebrow">
                        MASTER DATA · JASA POTONG
                    </span>
                    <br>
                    <span class="eyebrow">
                        Hapus Jasa Potong
                    </span>
                </div>

                <button type="button" class="modal-close" id="btnTutupDeleteCutting" aria-label="Tutup">
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
                            Hapus jasa potong?
                        </h3>
                        <p>
                            Apakah kamu yakin ingin menghapus jasa potong ini?
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
                <button type="button" class="btn btn--ghost" id="btnBatalDeleteCutting">
                    Batal
                </button>

                <button type="button" class="btn btn--danger" id="btnConfirmDeleteCutting">
                    <i class="bi bi-trash3-fill"></i>
                    Hapus
                </button>
            </div>
        </div>
    </div>

    {{-- ==========================================================
    | MODAL IMPORT JASA POTONG
    =========================================================== --}}
    <div class="modal-overlay" id="modalImportCutting">
        <div class="modal modal-import">

            {{-- HEADER --}}
            <div class="modal-header">
                <div>
                    <span class="eyebrow">MASTER DATA · JASA POTONG</span>
                    <br>
                    <span class="eyebrow">Import Jasa Potong</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseImportCutting" aria-label="Tutup">
                    <svg viewBox="0 0 24 24">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            {{-- BODY --}}
            <div class="modal-body">
                <div class="import-description">
                    <p>
                        Pilih file Excel yang berisi data jasa potong untuk diimport
                        ke dalam sistem.
                    </p>
                </div>

                <label for="fileImportCutting" class="file-upload-box" id="fileUploadBoxCutting">
                    <input type="file" id="fileImportCutting" name="file" accept=".xlsx,.xls" hidden>

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

                    <div class="file-selected-name" id="fileSelectedNameCutting">
                        Belum ada file dipilih
                    </div>
                </label>
            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">
                <button type="button" class="btn btn--ghost" id="btnCancelImportCutting">
                    Batal
                </button>

                <button type="button" class="btn btn--primary" id="btnSaveImportCutting">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8 5-5 5 5" />
                    </svg>
                    Import Data
                </button>
            </div>

        </div>
    </div>

    {{-- ==========================================================
    | MODAL DETAIL JASA POTONG
    =========================================================== --}}
    <div class="modal-overlay" id="modalDetailCutting">
        <div class="modal-dialog modal-dialog--wide">

            {{-- HEADER --}}
            <div class="modal-header">
                <div>
                    <span class="eyebrow">
                        MASTER DATA · JASA POTONG
                    </span>
                    <br>
                    <span class="eyebrow">
                        Detail Jasa Potong
                    </span>
                </div>

                <button type="button" class="modal-close" id="btnTutupModalDetailCutting" aria-label="Tutup">
                    <svg viewBox="0 0 24 24">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
            </div>

            {{-- BODY --}}
            <div class="modal-body">

                {{-- DATA JASA POTONG --}}
                <div class="cutting-detail-section">
                    <div class="cutting-detail-section-header">
                        <div>
                            <label class="category-label">
                                Data Jasa Potong
                            </label>
                            <p class="category-description">
                                Informasi utama jasa potong.
                            </p>
                        </div>
                    </div>

                    <div class="cutting-main-fields">
                        {{-- Nama Jasa Potong --}}
                        <div class="cutting-field">
                            <label class="category-label">
                                Nama Jasa Potong
                            </label>
                            <div class="detail-value" id="detailCuttingName">
                                -
                            </div>
                        </div>

                        {{-- Status --}}
                        <div class="cutting-field">
                            <label class="category-label">
                                Status
                            </label>
                            <div class="detail-value" id="detailCuttingStatus">
                                -
                            </div>
                        </div>
                    </div>
                </div>

                {{-- UKURAN JASA POTONG --}}
                <div class="cutting-detail-section">
                    <div class="cutting-detail-section-header">
                        <div>
                            <label class="category-label">
                                Ukuran Jasa Potong
                            </label>
                            <p class="category-description">
                                Daftar ukuran jasa potong yang tersedia.
                            </p>
                        </div>
                    </div>

                    <div class="cutting-detail-table-wrapper">
                        <table class="cutting-detail-table">
                            <thead>
                                <tr>
                                    <th>Lebar</th>
                                    <th>Panjang</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>
                            <tbody id="detailCuttingSizes">
                                <tr>
                                    <td colspan="3" class="cutting-detail-empty">
                                        Belum ada ukuran.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- INFORMASI AUDIT --}}
                <div class="cutting-detail-section">
                    <div class="cutting-detail-section-header">
                        <div>
                            <label class="category-label">
                                Informasi Audit
                            </label>
                            <p class="category-description">
                                Informasi pembuatan dan perubahan data jasa potong.
                            </p>
                        </div>
                    </div>

                    <div class="cutting-audit-grid">
                        {{-- Dibuat Oleh --}}
                        <div class="cutting-field">
                            <label class="category-label">
                                Dibuat Oleh
                            </label>
                            <div class="detail-value" id="detailCreatedBy">
                                -
                            </div>
                        </div>

                        {{-- Dibuat Pada --}}
                        <div class="cutting-field">
                            <label class="category-label">
                                Dibuat Pada
                            </label>
                            <div class="detail-value" id="detailCreatedAt">
                                -
                            </div>
                        </div>

                        {{-- Diubah Oleh --}}
                        <div class="cutting-field">
                            <label class="category-label">
                                Diubah Oleh
                            </label>
                            <div class="detail-value" id="detailUpdatedBy">
                                -
                            </div>
                        </div>

                        {{-- Diubah Pada --}}
                        <div class="cutting-field">
                            <label class="category-label">
                                Diubah Pada
                            </label>
                            <div class="detail-value" id="detailUpdatedAt">
                                -
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="modal-footer">
                <button type="button" class="btn btn--ghost" id="btnBatalDetailCutting">
                    Tutup
                </button>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let cuttingTable;

        document.addEventListener('DOMContentLoaded', function() {
            /*
            |--------------------------------------------------------------------------
            | DATATABLE JASA POTONG
            |--------------------------------------------------------------------------
            */
            cuttingTable = new DataTable('#cuttingTable', {
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
                    url: "{{ route('admin_data_cuttings') }}",
                    type: "GET"
                },

                columns: [{
                        data: null,
                        defaultContent: '',
                        className: 'dtr-control',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            const pageInfo = cuttingTable.page.info();
                            return pageInfo.start + meta.row + 1;
                        }
                    },
                    {
                        data: 'name',
                        responsivePriority: 1,
                        render: function(data) {
                            return `
                                <div>
                                    ${data ?? '-'}
                                </div>
                            `;
                        },
                        createdCell: function(td) {
                            td.style.whiteSpace = 'normal';
                            td.style.wordBreak = 'break-word';
                            td.style.overflowWrap = 'break-word';
                        }
                    },
                    {
                        data: 'total_sizes',
                        responsivePriority: 100,
                        className: 'text-center',
                        render: function(data) {
                            return data ?? 0;
                        },
                        createdCell: function(td) {
                            td.style.textAlign = 'center';
                        }
                    },
                    {
                        data: 'status',
                        responsivePriority: 100,
                        render: function(data) {
                            const statusClass =
                                data === 'Active' ? 'success' : 'danger';

                            return `
                                <span class="badge ${statusClass} dot">
                                    ${data}
                                </span>
                            `;
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 100,
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
                                    <!-- VIEW -->
                                    <button
                                        type="button"
                                        class="btn--icon btn-view-cutting"
                                        data-id="${data}"
                                        aria-label="View"
                                    >
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </button>

                                    <!-- EDIT -->
                                    <button
                                        type="button"
                                        class="btn--icon btn-edit-cutting"
                                        data-id="${data}"
                                        aria-label="Edit"
                                    >
                                        <i class="bi bi-pen"></i>
                                    </button>

                                    <!-- DELETE -->
                                    <button
                                        type="button"
                                        class="btn--icon btn-delete-cutting"
                                        data-id="${data}"
                                        aria-label="Hapus"
                                    >
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </div>
                            `;
                        }
                    }
                ],

                columnDefs: [{
                    targets: 0,
                    className: 'dtr-control'
                }],

                pageLength: 10,
                lengthMenu: [10, 25, 50, 100],

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
                    zeroRecords: 'Data jasa potong tidak ditemukan',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari jasa potong...'
                },

                initComplete: function() {
                    $(this)
                        .closest('.dt-container, .dataTables_wrapper')
                        .find('.dt-search input, .dataTables_filter input')
                        .css({
                            'padding': '9px 14px',
                            'border-radius': '8px',
                            'border': '1px solid #e2e8f0'
                        });
                }
            });

            /*
            |--------------------------------------------------------------------------
            | MODAL TAMBAH JASA POTONG
            |--------------------------------------------------------------------------
            */
            const modal = document.getElementById('modalTambahCutting');
            const btnTambah = document.getElementById('btnTambahCutting');
            const btnTutup = document.getElementById('btnTutupModalCutting');
            const btnBatal = document.getElementById('btnBatalCutting');
            const btnTambahBaris = document.getElementById('btnTambahBarisCutting');
            const form = document.getElementById('formTambahCutting');
            const cuttingRows = document.getElementById('cuttingRows');

            if (!modal || !form || !cuttingRows) {
                return;
            }

            let cuttingIndex = 1;

            /* =========================================================
               SELECT2
            ========================================================= */
            function initSelect2(container) {
                if (
                    typeof $ === 'undefined' ||
                    typeof $.fn.select2 === 'undefined'
                ) {
                    return;
                }

                $(container).find('.select2').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        return;
                    }

                    $(this).select2({
                        width: '100%',
                        dropdownParent: $(modal)
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

                $(container).find('.select2').each(function() {
                    if ($(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2('destroy');
                    }
                });
            }

            initSelect2(cuttingRows);

            /* =========================================================
               TOGGLE UKURAN UNTUK JASA POTONG "POTONG" (TAMBAH)
            ========================================================= */
            function checkPotongRow(row) {
                if (!row) return;
                const nameInput = row.querySelector('input[name*="[name]"]');
                const sizesSection = row.querySelector('.material-sizes');
                if (!nameInput || !sizesSection) return;

                const isPotong = nameInput.value.trim().toLowerCase() === 'potong';
                const inputs = sizesSection.querySelectorAll('input, select, button');

                if (isPotong) {
                    sizesSection.style.display = 'none';
                    inputs.forEach(function(el) {
                        el.disabled = true;
                        if (el.hasAttribute('required')) {
                            el.dataset.wasRequired = 'true';
                            el.removeAttribute('required');
                        }
                        if (typeof $ !== 'undefined' && $(el).hasClass('select2-hidden-accessible')) {
                            $(el).prop('disabled', true);
                        }
                    });
                } else {
                    sizesSection.style.display = '';
                    inputs.forEach(function(el) {
                        el.disabled = false;
                        if (el.dataset.wasRequired === 'true' || el.name?.includes('[width]') || el.name?.includes('[unit]')) {
                            el.setAttribute('required', 'required');
                        }
                        if (typeof $ !== 'undefined' && $(el).hasClass('select2-hidden-accessible')) {
                            $(el).prop('disabled', false);
                        }
                    });
                }
            }

            cuttingRows.addEventListener('input', function(event) {
                const target = event.target;
                if (target && target.matches('input[name*="[name]"]')) {
                    const row = target.closest('.material-row');
                    if (row) {
                        checkPotongRow(row);
                    }
                }
            });

            cuttingRows.addEventListener('change', function(event) {
                const target = event.target;
                if (target && target.matches('input[name*="[name]"]')) {
                    const row = target.closest('.material-row');
                    if (row) {
                        checkPotongRow(row);
                    }
                }
            });

            cuttingRows.querySelectorAll('.material-row').forEach(function(row) {
                checkPotongRow(row);
            });

            /* =========================================================
               VALIDASI FORM
            ========================================================= */
            form.addEventListener('submit', function(event) {
                form.querySelectorAll('.select2-invalid').forEach(function(element) {
                    element.classList.remove('select2-invalid');
                });

                const requiredFields = form.querySelectorAll(
                    'input[required], select[required]'
                );

                let firstInvalid = null;

                requiredFields.forEach(function(field) {
                    if (firstInvalid) {
                        return;
                    }

                    if (field.disabled || field.closest('.material-sizes[style*="display: none"]')) {
                        return;
                    }

                    /* Abaikan field yang tersembunyi selain select2 */
                    if (
                        field.offsetParent === null &&
                        !(
                            typeof $ !== 'undefined' &&
                            $(field).hasClass('select2-hidden-accessible')
                        )
                    ) {
                        return;
                    }

                    /* SELECT2 */
                    if (
                        typeof $ !== 'undefined' &&
                        $(field).hasClass('select2-hidden-accessible')
                    ) {
                        if (!$(field).val()) {
                            $(field)
                                .next('.select2-container')
                                .addClass('select2-invalid');
                            firstInvalid = field;
                        }
                        return;
                    }

                    /* INPUT */
                    if (!field.value.trim()) {
                        firstInvalid = field;
                    }
                });

                if (!firstInvalid) {
                    return;
                }

                event.preventDefault();

                if (
                    typeof $ !== 'undefined' &&
                    $(firstInvalid).hasClass('select2-hidden-accessible')
                ) {
                    $(firstInvalid).select2('open');
                } else {
                    firstInvalid.focus();
                }
            });

            /* =========================================================
               BUKA MODAL
            ========================================================= */
            if (btnTambah) {
                btnTambah.addEventListener('click', function(event) {
                    event.preventDefault();
                    modal.classList.add('is-open');
                });
            }

            /* =========================================================
               TUTUP MODAL
            ========================================================= */
            function tutupModal() {
                modal.classList.remove('is-open');
            }

            if (btnTutup) {
                btnTutup.addEventListener('click', tutupModal);
            }

            if (btnBatal) {
                btnBatal.addEventListener('click', tutupModal);
            }

            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    tutupModal();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (
                    event.key === 'Escape' &&
                    modal.classList.contains('is-open')
                ) {
                    tutupModal();
                }
            });

            /* =========================================================
               TAMBAH BARIS JASA POTONG
            ========================================================= */
            if (btnTambahBaris) {
                btnTambahBaris.addEventListener('click', function() {
                    const index = cuttingIndex;

                    const row = document.createElement('div');
                    row.className = 'material-row';
                    row.dataset.cuttingIndex = index;
                    row.dataset.sizeIndex = 1;

                    row.innerHTML = `
                        <div style="display: flex; justify-content: right; align-items: center;">
                            <div class="material-field material-field--action">
                                <button
                                    type="button"
                                    class="btn-remove-category"
                                    aria-label="Hapus jasa potong"
                                    style="border: none !important;"
                                >
                                    <i class="bi bi-trash3-fill"></i>
                                </button>
                            </div>
                        </div>

                        <div class="material-row-top material-row-top--cutting">
                            <div class="material-field">
                                <label class="category-label">
                                    Nama Jasa Potong
                                </label>
                                <input
                                    type="text"
                                    name="cuttings[${index}][name]"
                                    class="input"
                                    placeholder="Masukkan nama jasa potong..."
                                    required
                                >
                            </div>

                            <div class="material-field">
                                <label class="category-label">
                                    Status
                                </label>
                                <select
                                    name="cuttings[${index}][status]"
                                    class="select select2"
                                    required
                                >
                                    <option value="Active" selected>
                                        Active
                                    </option>
                                    <option value="Inactive">
                                        Inactive
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="material-sizes">
                            <div class="material-sizes-header">
                                <div>
                                    <label class="category-label">
                                        Ukuran Jasa Potong
                                    </label>
                                    <p class="category-description">
                                        Tambahkan satu atau beberapa ukuran jasa potong.
                                    </p>
                                </div>

                                <button
                                    type="button"
                                    class="btn btn--ghost btnTambahUkuran"
                                >
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M12 5v14"></path>
                                        <path d="M5 12h14"></path>
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
                                            name="cuttings[${index}][sizes][0][width]"
                                            class="input"
                                            placeholder="Masukkan lebar..."
                                            min="0"
                                            step="0.01"
                                            required
                                        >
                                    </div>

                                    <div class="material-field">
                                        <label class="category-label">
                                            Panjang
                                        </label>
                                        <input
                                            type="number"
                                            name="cuttings[${index}][sizes][0][length]"
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
                                            name="cuttings[${index}][sizes][0][unit]"
                                            class="select select2"
                                            required
                                        >
                                            <option value="">
                                                Pilih Unit
                                            </option>
                                            <option value="cm" selected>
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

                    cuttingRows.appendChild(row);
                    initSelect2(row);
                    checkPotongRow(row);

                    const nameInput = row.querySelector('input[name*="[name]"]');
                    if (nameInput) {
                        nameInput.focus();
                    }

                    cuttingIndex++;
                });
            }

            /* =========================================================
               TAMBAH UKURAN
            ========================================================= */
            cuttingRows.addEventListener('click', function(event) {
                const addSizeButton = event.target.closest('.btnTambahUkuran');
                if (!addSizeButton) {
                    return;
                }

                const cuttingRow = addSizeButton.closest('.material-row');
                if (!cuttingRow) {
                    return;
                }

                const sizeRows = cuttingRow.querySelector('.material-size-rows');
                if (!sizeRows) {
                    return;
                }

                const currentCuttingIndex = cuttingRow.dataset.cuttingIndex;
                const sizeIndex = parseInt(cuttingRow.dataset.sizeIndex || '1', 10);

                const row = document.createElement('div');
                row.className = 'material-size-row';

                row.innerHTML = `
                    <div class="material-field">
                        <label class="category-label">
                            Lebar
                        </label>
                        <input
                            type="number"
                            name="cuttings[${currentCuttingIndex}][sizes][${sizeIndex}][width]"
                            class="input"
                            placeholder="Masukkan lebar..."
                            min="0"
                            step="0.01"
                            required
                        >
                    </div>

                    <div class="material-field">
                        <label class="category-label">
                            Panjang
                        </label>
                        <input
                            type="number"
                            name="cuttings[${currentCuttingIndex}][sizes][${sizeIndex}][length]"
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
                            name="cuttings[${currentCuttingIndex}][sizes][${sizeIndex}][unit]"
                            class="select select2"
                            required
                        >
                            <option value="">
                                Pilih Unit
                            </option>
                            <option value="cm" selected>
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
                cuttingRow.dataset.sizeIndex = sizeIndex + 1;
                initSelect2(row);

                const widthInput = row.querySelector('input[name*="[width]"]');
                if (widthInput) {
                    widthInput.focus();
                }
            });

            /* =========================================================
               HAPUS BARIS JASA POTONG
            ========================================================= */
            cuttingRows.addEventListener('click', function(event) {
                const removeButton = event.target.closest('.btn-remove-category');
                if (!removeButton) {
                    return;
                }

                const rows = cuttingRows.querySelectorAll('.material-row');
                const cuttingRow = removeButton.closest('.material-row');
                if (!cuttingRow) {
                    return;
                }

                if (rows.length <= 1) {
                    const name = cuttingRow.querySelector('input[name*="[name]"]');
                    const status = cuttingRow.querySelector('select[name*="[status]"]');

                    if (name) {
                        name.value = '';
                    }

                    if (status) {
                        $(status).val('Active').trigger('change');
                    }

                    const sizeRows = cuttingRow.querySelectorAll('.material-size-row');
                    sizeRows.forEach(function(sizeRow, index) {
                        if (index === 0) {
                            const width = sizeRow.querySelector('input[name*="[width]"]');
                            const length = sizeRow.querySelector('input[name*="[length]"]');
                            const unit = sizeRow.querySelector('select[name*="[unit]"]');

                            if (width) width.value = '';
                            if (length) length.value = '';
                            if (unit) $(unit).val('cm').trigger('change');
                        } else {
                            destroySelect2(sizeRow);
                            sizeRow.remove();
                        }
                    });

                    cuttingRow.dataset.sizeIndex = 1;
                    checkPotongRow(cuttingRow);
                    if (name) {
                        name.focus();
                    }
                    return;
                }

                destroySelect2(cuttingRow);
                cuttingRow.remove();
            });

            /* =========================================================
               HAPUS UKURAN
            ========================================================= */
            cuttingRows.addEventListener('click', function(event) {
                const removeButton = event.target.closest('.btn-remove-size');
                if (!removeButton) {
                    return;
                }

                const cuttingRow = removeButton.closest('.material-row');
                if (!cuttingRow) {
                    return;
                }

                const sizeRows = cuttingRow.querySelectorAll('.material-size-row');
                const sizeRow = removeButton.closest('.material-size-row');
                if (!sizeRow) {
                    return;
                }

                if (sizeRows.length <= 1) {
                    const width = sizeRow.querySelector('input[name*="[width]"]');
                    const length = sizeRow.querySelector('input[name*="[length]"]');
                    const unit = sizeRow.querySelector('select[name*="[unit]"]');

                    if (width) width.value = '';
                    if (length) length.value = '';
                    if (unit) $(unit).val('cm').trigger('change');
                    return;
                }

                destroySelect2(sizeRow);
                sizeRow.remove();
            });

            /*
            |--------------------------------------------------------------------------
            | MODAL EDIT JASA POTONG
            |--------------------------------------------------------------------------
            */
            const modalEdit = document.getElementById('modalEditCutting');
            const formEdit = document.getElementById('formEditCutting');
            const btnTutupEdit = document.getElementById('btnTutupModalEditCutting');
            const btnBatalEdit = document.getElementById('btnBatalEditCutting');
            const editCuttingId = document.getElementById('editCuttingId');
            const editCuttingName = document.getElementById('editCuttingName');
            const editCuttingStatus = document.getElementById('editCuttingStatus');
            const editCuttingRows = document.getElementById('editCuttingRows');
            const btnSimpanEdit = document.getElementById('btnSimpanEditCutting');

            let editSizeIndex = 0;

            if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined' && modalEdit) {
                $('#editCuttingStatus').select2({
                    width: '100%',
                    dropdownParent: $(modalEdit)
                });
            }

            function tutupModalEdit() {
                if (modalEdit) {
                    modalEdit.classList.remove('is-open');
                }
            }

            if (btnTutupEdit) {
                btnTutupEdit.addEventListener('click', tutupModalEdit);
            }

            if (btnBatalEdit) {
                btnBatalEdit.addEventListener('click', tutupModalEdit);
            }

            if (modalEdit) {
                modalEdit.addEventListener('click', function(event) {
                    if (event.target === modalEdit) {
                        tutupModalEdit();
                    }
                });
            }

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && modalEdit && modalEdit.classList.contains('is-open')) {
                    tutupModalEdit();
                }
            });

            /* CREATE SIZE ROW FOR EDIT */
            function createEditSizeRow(size = {}, index = 0) {
                const sizeRows = editCuttingRows.querySelector('.material-size-rows');
                if (!sizeRows) {
                    return;
                }

                const row = document.createElement('div');
                row.className = 'material-size-row';

                row.innerHTML = `
                    <input
                        type="hidden"
                        name="sizes[${index}][id]"
                        value="${size.id || ''}"
                    >

                    <!-- LEBAR -->
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
                            value="${size.width ?? ''}"
                            required
                        >
                    </div>

                    <!-- PANJANG -->
                    <div class="material-field">
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
                            value="${size.length ?? ''}"
                        >
                    </div>

                    <!-- UNIT -->
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
                                ${size.unit === 'cm' ? 'selected' : ''}
                            >
                                cm
                            </option>
                            <option
                                value="m"
                                ${size.unit === 'm' ? 'selected' : ''}
                            >
                                m
                            </option>
                        </select>
                    </div>

                    <!-- HAPUS -->
                    <button
                        type="button"
                        class="btn-remove-size"
                        aria-label="Hapus ukuran"
                    >
                        <i class="bi bi-trash3-fill"></i>
                    </button>
                `;

                sizeRows.appendChild(row);

                if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                    $(row).find('.select2').select2({
                        width: '100%',
                        dropdownParent: $(modalEdit)
                    });
                }
            }

            /* TAMBAH UKURAN DALAM MODAL EDIT */
            if (editCuttingRows) {
                editCuttingRows.addEventListener('click', function(event) {
                    const addSizeButton = event.target.closest('.btnTambahUkuranEdit');
                    if (!addSizeButton) {
                        return;
                    }

                    createEditSizeRow({}, editSizeIndex);
                    editSizeIndex++;

                    const rows = editCuttingRows.querySelectorAll('.material-size-row');
                    const lastRow = rows[rows.length - 1];
                    if (lastRow) {
                        const widthInput = lastRow.querySelector('input[name*="[width]"]');
                        if (widthInput) {
                            widthInput.focus();
                        }
                    }
                });

                /* HAPUS UKURAN DALAM MODAL EDIT */
                editCuttingRows.addEventListener('click', function(event) {
                    const removeButton = event.target.closest('.btn-remove-size');
                    if (!removeButton) {
                        return;
                    }

                    const sizeRow = removeButton.closest('.material-size-row');
                    if (!sizeRow) {
                        return;
                    }

                    const sizeRows = editCuttingRows.querySelectorAll('.material-size-row');

                    if (sizeRows.length <= 1) {
                        const width = sizeRow.querySelector('input[name*="[width]"]');
                        const length = sizeRow.querySelector('input[name*="[length]"]');
                        const unit = sizeRow.querySelector('select[name*="[unit]"]');

                        if (width) width.value = '';
                        if (length) length.value = '';
                        if (unit) {
                            if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                                $(unit).val('').trigger('change');
                            } else {
                                unit.value = '';
                            }
                        }
                        if (width) width.focus();
                        return;
                    }

                    if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                        $(sizeRow).find('.select2').each(function() {
                            if ($(this).hasClass('select2-hidden-accessible')) {
                                $(this).select2('destroy');
                            }
                        });
                    }

                    sizeRow.remove();
                });
            }

            /* =========================================================
               TOGGLE UKURAN UNTUK JASA POTONG "POTONG" (EDIT)
            ========================================================= */
            function checkPotongEdit() {
                const sizesSection = editCuttingRows ? editCuttingRows.querySelector('.material-sizes') : null;
                if (!editCuttingName || !sizesSection) return;

                const isPotong = editCuttingName.value.trim().toLowerCase() === 'potong';
                const inputs = sizesSection.querySelectorAll('input, select, button');

                if (isPotong) {
                    sizesSection.style.display = 'none';
                    inputs.forEach(function(el) {
                        el.disabled = true;
                        if (el.hasAttribute('required')) {
                            el.dataset.wasRequired = 'true';
                            el.removeAttribute('required');
                        }
                        if (typeof $ !== 'undefined' && $(el).hasClass('select2-hidden-accessible')) {
                            $(el).prop('disabled', true);
                        }
                    });
                } else {
                    sizesSection.style.display = '';
                    inputs.forEach(function(el) {
                        el.disabled = false;
                        if (el.dataset.wasRequired === 'true' || el.name?.includes('[width]') || el.name?.includes('[unit]')) {
                            el.setAttribute('required', 'required');
                        }
                        if (typeof $ !== 'undefined' && $(el).hasClass('select2-hidden-accessible')) {
                            $(el).prop('disabled', false);
                        }
                    });

                    const currentRows = editCuttingRows.querySelectorAll('.material-size-row');
                    if (currentRows.length === 0) {
                        createEditSizeRow({}, editSizeIndex);
                        editSizeIndex++;
                    }
                }
            }

            if (editCuttingName) {
                editCuttingName.addEventListener('input', checkPotongEdit);
                editCuttingName.addEventListener('change', checkPotongEdit);
            }

            /* BUKA MODAL EDIT DARI TOMBOL AKSI DATATABLE */
            document.addEventListener('click', function(event) {
                const button = event.target.closest('.btn-edit-cutting');
                if (!button) {
                    return;
                }

                const id = button.getAttribute('data-id');
                if (!id) {
                    showToast('error', 'ID jasa potong tidak ditemukan.');
                    return;
                }

                fetch(`{{ url('/admin/cuttings') }}/${id}/edit`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function(response) {
                        if (!response.ok) {
                            throw new Error('Gagal mengambil data jasa potong.');
                        }
                        return response.json();
                    })
                    .then(function(result) {
                        if (!result.success) {
                            showToast('error', result.message || 'Data jasa potong gagal dimuat.');
                            return;
                        }

                        const cutting = result.data;

                        editCuttingId.value = cutting.id || '';
                        editCuttingName.value = cutting.name || '';

                        if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                            $('#editCuttingStatus').val(cutting.status || 'Active').trigger('change');
                        } else {
                            editCuttingStatus.value = cutting.status || 'Active';
                        }

                        formEdit.action = `{{ url('/admin/cuttings') }}/${cutting.id}/update`;

                        const sizeRows = editCuttingRows.querySelector('.material-size-rows');
                        if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                            $(sizeRows).find('.select2').each(function() {
                                if ($(this).hasClass('select2-hidden-accessible')) {
                                    $(this).select2('destroy');
                                }
                            });
                        }
                        sizeRows.innerHTML = '';
                        editSizeIndex = 0;

                        if (Array.isArray(cutting.sizes) && cutting.sizes.length > 0) {
                            cutting.sizes.forEach(function(size) {
                                createEditSizeRow(size, editSizeIndex);
                                editSizeIndex++;
                            });
                        } else {
                            createEditSizeRow({}, editSizeIndex);
                            editSizeIndex++;
                        }

                        checkPotongEdit();

                        modalEdit.classList.add('is-open');
                    })
                    .catch(function(error) {
                        console.error(error);
                        showToast('error', 'Terjadi kesalahan saat mengambil data jasa potong.');
                    });
            });

            /* VALIDASI & SUBMIT FORM EDIT */
            function validateEditForm() {
                if (!editCuttingName.value.trim()) {
                    showToast('error', 'Nama jasa potong wajib diisi.');
                    editCuttingName.focus();
                    return false;
                }

                if (!editCuttingStatus.value) {
                    showToast('error', 'Status jasa potong wajib dipilih.');
                    if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                        $('#editCuttingStatus').select2('open');
                    }
                    return false;
                }

                const isPotong = editCuttingName.value.trim().toLowerCase() === 'potong';
                if (isPotong) {
                    return true;
                }

                const sizeRows = editCuttingRows.querySelectorAll('.material-size-row');
                if (sizeRows.length === 0) {
                    showToast('error', 'Minimal harus ada satu ukuran jasa potong.');
                    return false;
                }

                let valid = true;
                sizeRows.forEach(function(sizeRow) {
                    const width = sizeRow.querySelector('input[name*="[width]"]');
                    const length = sizeRow.querySelector('input[name*="[length]"]');
                    const unit = sizeRow.querySelector('select[name*="[unit]"]');

                    if (!width.value || parseFloat(width.value) <= 0) {
                        valid = false;
                        width.classList.add('is-invalid');
                    } else {
                        width.classList.remove('is-invalid');
                    }

                    if (length && length.value !== '' && parseFloat(length.value) <= 0) {
                        valid = false;
                        length.classList.add('is-invalid');
                    } else if (length) {
                        length.classList.remove('is-invalid');
                    }

                    if (!unit.value) {
                        valid = false;
                        if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                            $(unit).next('.select2-container').addClass('select2-invalid');
                        }
                    } else {
                        if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
                            $(unit).next('.select2-container').removeClass('select2-invalid');
                        }
                    }
                });

                if (!valid) {
                    showToast('error', 'Periksa kembali data ukuran jasa potong.');
                }

                return valid;
            }

            if (formEdit) {
                formEdit.addEventListener('submit', function(event) {
                    event.preventDefault();

                    if (!validateEditForm()) {
                        return;
                    }

                    const originalBtnContent = btnSimpanEdit ? btnSimpanEdit.innerHTML : '';
                    if (btnSimpanEdit) {
                        btnSimpanEdit.disabled = true;
                        btnSimpanEdit.innerHTML = `
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Menyimpan...
                        `;
                    }

                    const formData = new FormData(formEdit);

                    fetch(formEdit.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    ?.getAttribute('content') ||
                                    document.querySelector('input[name="_token"]')?.value || ''
                            },
                            body: formData
                        })
                        .then(function(response) {
                            return response.json().then(function(result) {
                                return {
                                    ok: response.ok,
                                    result: result
                                };
                            }).catch(function() {
                                return {
                                    ok: response.ok,
                                    result: {
                                        success: false,
                                        message: 'Response server tidak valid.'
                                    }
                                };
                            });
                        })
                        .then(function(responseData) {
                            const result = responseData.result;

                            if (!responseData.ok || !result.success) {
                                showToast(result.type || 'error', result.message ||
                                    'Jasa potong gagal diperbarui.');
                                return;
                            }

                            showToast('success', result.message || 'Jasa potong berhasil diperbarui.');
                            tutupModalEdit();

                            if (cuttingTable) {
                                cuttingTable.ajax.reload(null, false);
                            }
                        })
                        .catch(function(error) {
                            console.error(error);
                            showToast('error', 'Terjadi kesalahan saat memperbarui jasa potong.');
                        })
                        .finally(function() {
                            if (btnSimpanEdit) {
                                btnSimpanEdit.disabled = false;
                                btnSimpanEdit.innerHTML = originalBtnContent;
                            }
                        });
                });
            }

            /*
            |--------------------------------------------------------------------------
            | DELETE JASA POTONG
            |--------------------------------------------------------------------------
            */
            let cuttingIdToDelete = null;
            let isDeletingCutting = false;

            const modalDeleteCutting = document.getElementById('modalDeleteCutting');
            const btnTutupDeleteCutting = document.getElementById('btnTutupDeleteCutting');
            const btnBatalDeleteCutting = document.getElementById('btnBatalDeleteCutting');
            const btnConfirmDeleteCutting = document.getElementById('btnConfirmDeleteCutting');

            /* BUKA MODAL DELETE */
            document.addEventListener('click', function(event) {
                const deleteButton = event.target.closest('.btn-delete-cutting');
                if (!deleteButton) {
                    return;
                }

                if (isDeletingCutting) {
                    return;
                }

                cuttingIdToDelete = deleteButton.dataset.id;
                if (modalDeleteCutting) {
                    modalDeleteCutting.classList.add('is-open');
                }
            });

            /* TUTUP MODAL DELETE */
            function tutupDeleteCuttingModal() {
                if (modalDeleteCutting) {
                    modalDeleteCutting.classList.remove('is-open');
                }
                cuttingIdToDelete = null;
            }

            if (btnTutupDeleteCutting) {
                btnTutupDeleteCutting.addEventListener('click', function() {
                    if (isDeletingCutting) return;
                    tutupDeleteCuttingModal();
                });
            }

            if (btnBatalDeleteCutting) {
                btnBatalDeleteCutting.addEventListener('click', function() {
                    if (isDeletingCutting) return;
                    tutupDeleteCuttingModal();
                });
            }

            if (modalDeleteCutting) {
                modalDeleteCutting.addEventListener('click', function(event) {
                    if (event.target === modalDeleteCutting && !isDeletingCutting) {
                        tutupDeleteCuttingModal();
                    }
                });
            }

            document.addEventListener('keydown', function(event) {
                if (
                    event.key === 'Escape' &&
                    modalDeleteCutting &&
                    modalDeleteCutting.classList.contains('is-open') &&
                    !isDeletingCutting
                ) {
                    tutupDeleteCuttingModal();
                }
            });

            /* KONFIRMASI HAPUS */
            if (btnConfirmDeleteCutting) {
                btnConfirmDeleteCutting.addEventListener('click', async function() {
                    if (!cuttingIdToDelete || isDeletingCutting) {
                        return;
                    }

                    isDeletingCutting = true;
                    const originalButtonContent = btnConfirmDeleteCutting.innerHTML;
                    btnConfirmDeleteCutting.disabled = true;
                    btnConfirmDeleteCutting.innerHTML = `
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Menghapus...
                    `;

                    try {
                        const response = await fetch(
                            `{{ url('/admin/cuttings') }}/${cuttingIdToDelete}/delete`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]')?.getAttribute('content') ||
                                        document.querySelector('input[name="_token"]')?.value || '',
                                    'Accept': 'application/json'
                                }
                            });

                        let result = null;
                        try {
                            result = await response.json();
                        } catch (jsonError) {
                            result = null;
                        }

                        if (response.ok && result && result.success) {
                            tutupDeleteCuttingModal();
                            if (cuttingTable) {
                                cuttingTable.ajax.reload(null, false);
                            }
                            showToast('success', result.message || 'Jasa potong berhasil dihapus.');
                            return;
                        }

                        tutupDeleteCuttingModal();
                        showToast('error', result && result.message ? result.message :
                            'Jasa potong gagal dihapus.');
                    } catch (error) {
                        console.error(error);
                        tutupDeleteCuttingModal();
                        showToast('error', 'Terjadi kesalahan saat menghapus jasa potong.');
                    } finally {
                        isDeletingCutting = false;
                        btnConfirmDeleteCutting.disabled = false;
                        btnConfirmDeleteCutting.innerHTML = originalButtonContent;
                    }
                });
            }

            /*
            |--------------------------------------------------------------------------
            | MODAL DETAIL JASA POTONG
            |--------------------------------------------------------------------------
            */
            const modalDetailCutting = document.getElementById('modalDetailCutting');
            const btnTutupModalDetailCutting = document.getElementById('btnTutupModalDetailCutting');
            const btnBatalDetailCutting = document.getElementById('btnBatalDetailCutting');

            function setDetailValue(id, value) {
                const element = document.getElementById(id);
                if (!element) {
                    return;
                }
                element.textContent =
                    value !== null &&
                    value !== undefined &&
                    value !== '' ?
                    value :
                    '-';
            }

            function setDetailStatus(status) {
                const element = document.getElementById('detailCuttingStatus');
                if (!element) {
                    return;
                }
                if (!status) {
                    element.textContent = '-';
                    return;
                }
                const statusClass = status === 'Active' ? 'success' : 'danger';
                element.innerHTML = `
                    <span class="badge ${statusClass} dot">
                        ${status}
                    </span>
                `;
            }

            function resetDetailModal() {
                setDetailValue('detailCuttingName', '-');
                const statusElement = document.getElementById('detailCuttingStatus');
                if (statusElement) {
                    statusElement.textContent = '-';
                }
                setDetailValue('detailCreatedBy', '-');
                setDetailValue('detailCreatedAt', '-');
                setDetailValue('detailUpdatedBy', '-');
                setDetailValue('detailUpdatedAt', '-');

                const sizeBody = document.getElementById('detailCuttingSizes');
                if (sizeBody) {
                    sizeBody.innerHTML = `
                        <tr>
                            <td colspan="3" class="cutting-detail-empty">
                                Belum ada ukuran.
                            </td>
                        </tr>
                    `;
                }
            }

            function setLoadingState() {
                const sizeBody = document.getElementById('detailCuttingSizes');
                if (sizeBody) {
                    sizeBody.innerHTML = `
                        <tr>
                            <td colspan="3" class="cutting-detail-empty">
                                Memuat data...
                            </td>
                        </tr>
                    `;
                }
            }

            function formatDateTime(value) {
                if (!value) {
                    return '-';
                }
                const date = new Date(value.replace(' ', 'T'));
                if (isNaN(date.getTime())) {
                    return value;
                }
                return date.toLocaleString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            function renderDetailSizes(sizes, cuttingName = '') {
                const sizeBody = document.getElementById('detailCuttingSizes');
                if (!sizeBody) {
                    return;
                }
                sizeBody.innerHTML = '';

                if (!sizes || sizes.length === 0) {
                    const emptyText = (cuttingName && cuttingName.trim().toLowerCase() === 'potong')
                        ? 'Jasa potong ini tidak memerlukan ukuran khusus.'
                        : 'Belum ada ukuran.';

                    sizeBody.innerHTML = `
                        <tr>
                            <td colspan="3" class="cutting-detail-empty">
                                ${emptyText}
                            </td>
                        </tr>
                    `;
                    return;
                }

                sizes.forEach(function(size) {
                    const width = size.width !== null && size.width !== undefined && size.width !== '' ?
                        size.width : '-';
                    const length = size.length !== null && size.length !== undefined && size.length !== '' ?
                        size.length : '-';
                    const unit = size.unit || '-';

                    const row = document.createElement('tr');
                    const widthCell = document.createElement('td');
                    const lengthCell = document.createElement('td');
                    const unitCell = document.createElement('td');

                    widthCell.textContent = width;
                    lengthCell.textContent = length;
                    unitCell.textContent = unit;

                    row.appendChild(widthCell);
                    row.appendChild(lengthCell);
                    row.appendChild(unitCell);

                    sizeBody.appendChild(row);
                });
            }

            function tutupDetailCuttingModal() {
                if (modalDetailCutting) {
                    modalDetailCutting.classList.remove('is-open');
                }
            }

            /* KLIK TOMBOL VIEW */
            document.addEventListener('click', function(event) {
                const button = event.target.closest('.btn-view-cutting');
                if (!button) {
                    return;
                }

                const id = button.dataset.id;
                if (!id) {
                    console.error('ID jasa potong tidak ditemukan.');
                    showToast('error', 'ID jasa potong tidak ditemukan.');
                    return;
                }

                resetDetailModal();
                setLoadingState();

                fetch(`{{ url('/admin/cuttings') }}/${id}/details`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(async function(response) {
                        const data = await response.json();
                        if (!response.ok) {
                            throw new Error(data.message || 'Gagal mengambil detail jasa potong.');
                        }
                        return data;
                    })
                    .then(function(data) {
                        if (!data.success) {
                            showToast('error', data.message || 'Gagal mengambil detail jasa potong.');
                            return;
                        }

                        const cutting = data.data;

                        setDetailValue('detailCuttingName', cutting.name);
                        setDetailStatus(cutting.status);
                        renderDetailSizes(cutting.sizes, cutting.name);

                        setDetailValue('detailCreatedBy', cutting.created_by);
                        setDetailValue('detailCreatedAt', formatDateTime(cutting.created_at));
                        setDetailValue('detailUpdatedBy', cutting.updated_by);
                        setDetailValue('detailUpdatedAt', formatDateTime(cutting.updated_at));

                        if (modalDetailCutting) {
                            modalDetailCutting.classList.add('is-open');
                        }
                    })
                    .catch(function(error) {
                        console.error(error);
                        showToast('error', error.message ||
                            'Terjadi kesalahan saat mengambil detail jasa potong.');
                    });
            });

            /* TUTUP MODAL DETAIL */
            if (btnTutupModalDetailCutting) {
                btnTutupModalDetailCutting.addEventListener('click', tutupDetailCuttingModal);
            }

            if (btnBatalDetailCutting) {
                btnBatalDetailCutting.addEventListener('click', tutupDetailCuttingModal);
            }

            if (modalDetailCutting) {
                modalDetailCutting.addEventListener('click', function(event) {
                    if (event.target === modalDetailCutting) {
                        tutupDetailCuttingModal();
                    }
                });
            }

            document.addEventListener('keydown', function(event) {
                if (
                    event.key === 'Escape' &&
                    modalDetailCutting &&
                    modalDetailCutting.classList.contains('is-open')
                ) {
                    tutupDetailCuttingModal();
                }
            });

            /*
            |--------------------------------------------------------------------------
            | MODAL IMPORT JASA POTONG
            |--------------------------------------------------------------------------
            */
            const btnImportCutting = document.getElementById('btnImportCutting');
            const modalImportCutting = document.getElementById('modalImportCutting');
            const btnCloseImportCutting = document.getElementById('btnCloseImportCutting');
            const btnCancelImportCutting = document.getElementById('btnCancelImportCutting');
            const btnSaveImportCutting = document.getElementById('btnSaveImportCutting');
            const fileImportCutting = document.getElementById('fileImportCutting');
            const fileSelectedNameCutting = document.getElementById('fileSelectedNameCutting');
            let isImportingCutting = false;

            function resetImportCuttingFile() {
                if (fileImportCutting) {
                    fileImportCutting.value = '';
                }
                if (fileSelectedNameCutting) {
                    fileSelectedNameCutting.textContent = 'Belum ada file dipilih';
                    fileSelectedNameCutting.classList.remove('has-file');
                }
            }

            function tutupImportCuttingModal() {
                if (isImportingCutting || !modalImportCutting) {
                    return;
                }
                modalImportCutting.classList.remove('is-open');
            }

            /* BUKA MODAL */
            if (btnImportCutting && modalImportCutting) {
                btnImportCutting.addEventListener('click', function() {
                    resetImportCuttingFile();
                    modalImportCutting.classList.add('is-open');
                });
            }

            /* TUTUP MODAL */
            if (btnCloseImportCutting) {
                btnCloseImportCutting.addEventListener('click', tutupImportCuttingModal);
            }

            if (btnCancelImportCutting) {
                btnCancelImportCutting.addEventListener('click', tutupImportCuttingModal);
            }

            if (modalImportCutting) {
                modalImportCutting.addEventListener('click', function(event) {
                    if (event.target === modalImportCutting) {
                        tutupImportCuttingModal();
                    }
                });
            }

            document.addEventListener('keydown', function(event) {
                if (
                    event.key === 'Escape' &&
                    modalImportCutting &&
                    modalImportCutting.classList.contains('is-open')
                ) {
                    tutupImportCuttingModal();
                }
            });

            /* FILE DIPILIH */
            if (fileImportCutting) {
                fileImportCutting.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        fileSelectedNameCutting.textContent = this.files[0].name;
                        fileSelectedNameCutting.classList.add('has-file');
                    } else {
                        resetImportCuttingFile();
                    }
                });
            }

            /* PROSES IMPORT */
            if (btnSaveImportCutting) {
                btnSaveImportCutting.addEventListener('click', function() {
                    if (isImportingCutting) {
                        return;
                    }

                    if (!fileImportCutting.files || fileImportCutting.files.length === 0) {
                        showToast('error', 'Silakan pilih file Excel terlebih dahulu.');
                        return;
                    }

                    const formData = new FormData();
                    formData.append('file', fileImportCutting.files[0]);

                    isImportingCutting = true;
                    btnSaveImportCutting.disabled = true;
                    const originalContent = btnSaveImportCutting.innerHTML;
                    btnSaveImportCutting.innerHTML = `
                        <svg viewBox="0 0 24 24">
                            <path d="M12 16V3" />
                            <path d="m7 8 5-5 5 5" />
                        </svg>
                        Mengimport...
                    `;

                    fetch('{{ route('admin_import_cuttings') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                                            document.querySelector('input[name="_token"]')?.value || '',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(async function(response) {
                        const data = await response.json();
                        if (!response.ok) {
                            throw data;
                        }
                        return data;
                    })
                    .then(function(data) {
                        if (!data.success) {
                            showToast('error', data.message || 'Import jasa potong gagal.');
                            return;
                        }

                        showToast('success', data.message || 'Data jasa potong berhasil diimport.');

                        isImportingCutting = false;
                        tutupImportCuttingModal();
                        resetImportCuttingFile();

                        if (cuttingTable) {
                            cuttingTable.ajax.reload(null, false);
                        }
                    })
                    .catch(function(error) {
                        let message = 'Terjadi kesalahan saat mengimport data jasa potong.';

                        if (error && error.errors) {
                            const messages = [];
                            Object.values(error.errors).forEach(function(errors) {
                                if (Array.isArray(errors)) {
                                    errors.forEach(function(errorMessage) {
                                        messages.push(errorMessage);
                                    });
                                }
                            });
                            if (messages.length > 0) {
                                message = messages.join('<br>');
                            }
                        } else if (error && error.message) {
                            message = error.message;
                        }

                        showToast('error', message);
                    })
                    .finally(function() {
                        isImportingCutting = false;
                        btnSaveImportCutting.disabled = false;
                        btnSaveImportCutting.innerHTML = originalContent;
                    });
                });
            }
        });
    </script>
@endsection

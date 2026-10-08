@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">MASTER DATA · LAMINASI</span>
                        {{-- <h1 class="hero-title">Kebijakan Harga</h1> --}}
                        <p class="hero-sub">Kelola data laminasi yang tersedia untuk digunakan pada proses produksi dan
                            perhitungan estimasi harga.</p>
                    </div>
                    <div class="hero-actions"><a href="{{ route('admin_export_laminations') }}" class="btn btn--ghost"><svg
                                viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg> Export</a>
                        <a href="{{ asset('templates/template_laminasi.xlsx') }}" class="btn btn--ghost">

                            <svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg>

                            Download Template

                        </a>
                        <button class="btn btn--ghost" id="btnImportLamination">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg>
                            Import
                        </button>
                        <button type="button" class="btn btn--primary" id="btnTambahLaminasi">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Laminasi
                        </button>

                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="laminationTable" class="display data-table">

                            <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th style="text-align: center;">Kode Laminasi</th>
                                    <th>Nama Laminasi</th>
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

    <!-- Modal Import Laminasi -->
    <div class="modal-overlay" id="modalImportLamination">

        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">

                <div>
                    <span class="eyebrow">MASTER DATA · LAMINASI</span>
                    <br>
                    <span class="eyebrow">Import Laminasi</span>
                </div>

                <button type="button" class="modal-close" id="btnCloseImportLamination" aria-label="Tutup">
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
                        Pilih file Excel yang berisi data laminasi untuk diimport
                        ke dalam sistem.
                    </p>
                </div>


                <!-- Custom File Input -->
                <label for="fileImportLamination" class="file-upload-box" id="fileUploadBox">

                    <input type="file" id="fileImportLamination" name="file" accept=".xlsx,.xls" hidden>


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


                    <div class="file-selected-name" id="fileSelectedNameLamination">
                        Belum ada file dipilih
                    </div>

                </label>

            </div>


            <!-- Footer -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnCancelImportLamination">
                    Batal
                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportLamination">

                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8 5-5 5 5" />
                    </svg>

                    Import Data

                </button>

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- MODAL TAMBAH LAMINASI --}}
    {{-- ========================================================= --}}

    <div class="modal-overlay" id="modalTambahLaminasi">
        <div class="modal-dialog modal-dialog--wide">

            <div class="modal-header">
                <div>
                    <h3 class="modal-title">
                        Tambah Laminasi
                    </h3>

                    <p class="modal-description">
                        Tambahkan satu atau beberapa data laminasi.
                    </p>
                </div>

                <button type="button" class="modal-close" id="btnTutupModalLaminasi" aria-label="Tutup">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <form id="formTambahLaminasi" action="{{ route('admin_store_lamination') }}" method="POST">
                @csrf

                <div class="modal-body">

                    <div class="modal-section-header" style="margin-bottom: 20px;">

                        <div>
                            <h4 class="modal-section-title">
                                Data Laminasi
                            </h4>

                            <p class="modal-section-description">
                                Masukkan informasi laminasi dan ukuran yang tersedia.
                            </p>
                        </div>

                        <button type="button" class="btn btn--ghost" id="btnTambahBarisLaminasi">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 5v14"></path>
                                <path d="M5 12h14"></path>
                            </svg>

                            Tambah Laminasi
                        </button>

                    </div>

                    <div id="laminationRows">

                        <!-- ========================= -->
                        <!-- LAMINASI PERTAMA -->
                        <!-- ========================= -->

                        <div class="material-row" data-lamination-index="0" data-size-index="1">

                            <div style="display: flex; justify-content: right; align-items: center;">
                                <!-- HAPUS -->

                                <div class="material-field material-field--action">

                                    <button type="button" class="btn-remove-category" aria-label="Hapus laminasi"
                                        style="border: none;">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>

                                </div>
                            </div>

                            <!-- DATA LAMINASI -->

                            <div class="material-row-top material-row-top--lamination">

                                <!-- KODE -->

                                <div class="material-field">

                                    <label class="category-label">
                                        Kode Laminasi
                                    </label>

                                    <input type="text" name="laminations[0][lamination_code]" class="input"
                                        placeholder="Masukkan kode laminasi...">

                                </div>


                                <!-- NAMA -->

                                <div class="material-field">

                                    <label class="category-label">
                                        Nama Laminasi
                                    </label>

                                    <input type="text" name="laminations[0][name]" class="input"
                                        placeholder="Masukkan nama laminasi..." required>

                                </div>


                                <!-- STATUS -->

                                <div class="material-field">

                                    <label class="category-label">
                                        Status
                                    </label>

                                    <select name="laminations[0][status]" class="select select2" required>
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
                            <!-- UKURAN LAMINASI -->
                            <!-- ========================= -->

                            <div class="material-sizes">

                                <div class="material-sizes-header">

                                    <div>

                                        <label class="category-label">
                                            Ukuran Laminasi
                                        </label>

                                        <p class="category-description">
                                            Tambahkan satu atau beberapa ukuran laminasi.
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

                                            <input type="number" name="laminations[0][sizes][0][width]" class="input"
                                                placeholder="Masukkan lebar..." min="0" step="0.01" required>

                                        </div>


                                        <!-- PANJANG -->

                                        <div class="material-field">

                                            <label class="category-label">
                                                Panjang
                                            </label>

                                            <input type="number" name="laminations[0][sizes][0][length]" class="input"
                                                placeholder="Masukkan panjang..." min="0" step="0.01">

                                        </div>


                                        <!-- UNIT -->

                                        <div class="material-field">

                                            <label class="category-label">
                                                Unit
                                            </label>

                                            <select name="laminations[0][sizes][0][unit]" class="select select2" required>
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

                    <button type="button" class="btn btn--secondary" id="btnBatalLaminasi">
                        Batal
                    </button>

                    <button type="submit" class="btn btn--primary">
                        Simpan
                    </button>

                </div>

            </form>

        </div>
    </div>

    {{-- Modal Edit --}}
    {{-- Modal Edit --}}
    <div class="modal-overlay" id="modalEditLaminasi">

        <div class="modal-dialog modal-dialog--wide">

            <form method="POST" action="" id="formEditLaminasi" novalidate>

                @csrf
                @method('PUT')

                <input type="hidden" name="id" id="editLaminationId">


                {{-- ================================================= --}}
                {{-- MODAL HEADER --}}
                {{-- ================================================= --}}

                <div class="modal-header">

                    <div>

                        <span class="eyebrow">
                            MASTER DATA · LAMINASI
                        </span>

                        <br>

                        <span class="eyebrow">
                            Edit Laminasi
                        </span>

                    </div>


                    <button type="button" class="modal-close" id="btnTutupModalEditLaminasi" aria-label="Tutup">

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
                    {{-- DATA LAMINASI --}}
                    {{-- ================================================= --}}

                    <div class="category-form-header">

                        <div>

                            <label class="category-label">
                                Data Laminasi
                            </label>

                            <p class="category-description">
                                Ubah informasi laminasi.
                            </p>

                        </div>

                    </div>


                    <div id="editLaminationRows">

                        <div class="material-row">


                            {{-- ================================================= --}}
                            {{-- DATA UTAMA --}}
                            {{-- ================================================= --}}

                            <div class="material-row-top material-row-top--lamination">


                                {{-- KODE --}}

                                <div class="material-field">

                                    <label class="category-label">
                                        Kode Laminasi
                                    </label>

                                    <input type="text" name="lamination_code" id="editLaminationCode" class="input"
                                        placeholder="Masukkan kode laminasi..." autocomplete="off">

                                </div>


                                {{-- NAMA --}}

                                <div class="material-field">

                                    <label class="category-label">
                                        Nama Laminasi
                                    </label>

                                    <input type="text" name="name" id="editLaminationName" class="input"
                                        placeholder="Masukkan nama laminasi..." autocomplete="off" required>

                                </div>


                                {{-- STATUS --}}

                                <div class="material-field">

                                    <label class="category-label">
                                        Status
                                    </label>

                                    <select name="status" id="editLaminationStatus" class="select select2" required>

                                        <option value="Active">
                                            Active
                                        </option>

                                        <option value="Inactive">
                                            Inactive
                                        </option>

                                    </select>

                                </div>

                            </div>


                            {{-- ================================================= --}}
                            {{-- UKURAN --}}
                            {{-- ================================================= --}}

                            <div class="material-sizes">


                                <div class="material-sizes-header">

                                    <div>

                                        <label class="category-label">
                                            Ukuran Laminasi
                                        </label>

                                        <p class="category-description">
                                            Atur satu atau beberapa ukuran laminasi.
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


                {{-- ================================================= --}}
                {{-- MODAL FOOTER --}}
                {{-- ================================================= --}}

                <div class="modal-footer">

                    <button type="button" class="btn btn--ghost" id="btnBatalEditLaminasi">
                        Batal
                    </button>


                    <button type="submit" class="btn btn--primary">

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

    <div class="modal-overlay" id="modalDetailLaminasi">

        <div class="modal-dialog modal-dialog--wide">

            {{-- ==========================================================
        | HEADER
        =========================================================== --}}
            <div class="modal-header">

                <div>
                    <span class="eyebrow">
                        MASTER DATA · LAMINASI
                    </span>

                    <br>

                    <span class="eyebrow">
                        Detail Laminasi
                    </span>
                </div>

                <button type="button" class="modal-close" id="btnTutupModalDetailLaminasi" aria-label="Tutup">

                    <svg viewBox="0 0 24 24">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>

                </button>

            </div>


            {{-- ==========================================================
        | BODY
        =========================================================== --}}
            <div class="modal-body">

                {{-- ======================================================
            | DATA LAMINASI
            ======================================================= --}}
                <div class="lamination-section">

                    <div class="lamination-section-header">

                        <div>
                            <label class="category-label">
                                Data Laminasi
                            </label>

                            <p class="category-description">
                                Informasi utama laminasi.
                            </p>
                        </div>

                    </div>


                    <div class="lamination-main-fields">

                        {{-- Kode Laminasi --}}
                        <div class="lamination-field">

                            <label class="category-label">
                                Kode Laminasi
                            </label>

                            <div class="detail-value" id="detailLaminationCode"
                                style="display: flex; justify-content: center;">
                                -
                            </div>

                        </div>


                        {{-- Nama Laminasi --}}
                        <div class="lamination-field">

                            <label class="category-label">
                                Nama Laminasi
                            </label>

                            <div class="detail-value" id="detailLaminationName">
                                -
                            </div>

                        </div>


                        {{-- Status --}}
                        <div class="lamination-field">

                            <label class="category-label">
                                Status
                            </label>

                            <div class="detail-value" id="detailLaminationStatus">
                                -
                            </div>

                        </div>

                    </div>

                </div>


                {{-- ======================================================
            | UKURAN LAMINASI
            ======================================================= --}}
                <div class="lamination-section">

                    <div class="lamination-section-header">

                        <div>
                            <label class="category-label">
                                Ukuran Laminasi
                            </label>

                            <p class="category-description">
                                Daftar ukuran laminasi yang tersedia.
                            </p>
                        </div>

                    </div>


                    <div class="lamination-detail-table-wrapper">

                        <table class="lamination-detail-table">

                            <thead>
                                <tr>
                                    <th>Lebar</th>
                                    <th>Panjang</th>
                                    <th>Unit</th>
                                </tr>
                            </thead>

                            <tbody id="detailLaminationSizes">

                                <tr>
                                    <td colspan="3" class="lamination-detail-empty">
                                        Belum ada ukuran.
                                    </td>
                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>


                {{-- ======================================================
            | INFORMASI AUDIT
            ======================================================= --}}
                <div class="lamination-section">

                    <div class="lamination-section-header">

                        <div>
                            <label class="category-label">
                                Informasi Audit
                            </label>

                            <p class="category-description">
                                Informasi pembuatan dan perubahan data laminasi.
                            </p>
                        </div>

                    </div>


                    <div class="lamination-audit-grid">

                        {{-- Dibuat Oleh --}}
                        <div class="lamination-field">

                            <label class="category-label">
                                Dibuat Oleh
                            </label>

                            <div class="detail-value" id="detailCreatedBy">
                                -
                            </div>

                        </div>


                        {{-- Dibuat Pada --}}
                        <div class="lamination-field">

                            <label class="category-label">
                                Dibuat Pada
                            </label>

                            <div class="detail-value" id="detailCreatedAt">
                                -
                            </div>

                        </div>


                        {{-- Diubah Oleh --}}
                        <div class="lamination-field">

                            <label class="category-label">
                                Diubah Oleh
                            </label>

                            <div class="detail-value" id="detailUpdatedBy">
                                -
                            </div>

                        </div>


                        {{-- Diubah Pada --}}
                        <div class="lamination-field">

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


            {{-- ==========================================================
        | FOOTER
        =========================================================== --}}
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDetailLaminasi">
                    Tutup
                </button>

            </div>

        </div>

    </div>

    <!-- MODAL DELETE LAMINASI -->
    <div class="modal-overlay" id="modalDeleteLaminasi">

        <div class="modal-dialog modal-delete">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <span class="eyebrow">
                        MASTER DATA · LAMINASI
                    </span>

                    <br>

                    <span class="eyebrow">
                        Hapus Laminasi
                    </span>

                </div>

                <button type="button" class="modal-close" id="btnTutupDeleteLaminasi" aria-label="Tutup">

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
                            Hapus laminasi?
                        </h3>

                        <p>
                            Apakah kamu yakin ingin menghapus laminasi ini?
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-footer">

                <button type="button" class="btn btn--ghost" id="btnBatalDeleteLaminasi">
                    Batal
                </button>


                <button type="button" class="btn btn--danger" id="btnConfirmDeleteLaminasi">

                    <i class="bi bi-trash3-fill"></i>

                    Hapus

                </button>

            </div>

        </div>

    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const btnImport = document.getElementById('btnImportLamination');
            const modal = document.getElementById('modalImportLamination');
            const btnClose = document.getElementById('btnCloseImportLamination');
            const btnCancel = document.getElementById('btnCancelImportLamination');
            const btnSave = document.getElementById('btnSaveImportLamination');
            const fileInput = document.getElementById('fileImportLamination');
            const fileName = document.getElementById('fileSelectedNameLamination');


            /* =========================
               BUKA MODAL
            ========================= */

            btnImport.addEventListener('click', function() {
                modal.classList.add('is-open');
            });


            /* =========================
               TUTUP MODAL
            ========================= */

            function closeModal() {
                modal.classList.remove('is-open');
            }


            btnClose.addEventListener('click', closeModal);

            btnCancel.addEventListener('click', closeModal);


            /* Klik background */

            modal.addEventListener('click', function(event) {

                if (event.target === modal) {
                    closeModal();
                }

            });


            /* Tombol ESC */

            document.addEventListener('keydown', function(event) {

                if (event.key === 'Escape') {
                    closeModal();
                }

            });


            /* =========================
               FILE SELECTED
            ========================= */

            fileInput.addEventListener('change', function() {

                if (this.files.length > 0) {

                    fileName.textContent = this.files[0].name;

                    fileName.classList.add('has-file');

                } else {

                    fileName.textContent = 'Belum ada file dipilih';

                    fileName.classList.remove('has-file');

                }

            });


            /* =========================
               IMPORT DATA
            ========================= */

            btnSave.addEventListener('click', function() {

                if (!fileInput.files || fileInput.files.length === 0) {

                    showToast(
                        'error',
                        'Silakan pilih file Excel terlebih dahulu.'
                    );

                    return;
                }


                const file = fileInput.files[0];

                const formData = new FormData();

                formData.append('file', file);


                /* =========================
                   DISABLE BUTTON
                ========================= */

                btnSave.disabled = true;

                const originalContent = btnSave.innerHTML;

                btnSave.innerHTML = `
            <svg viewBox="0 0 24 24">
                <path d="M12 16V3" />
                <path d="m7 8 5-5 5 5" />
            </svg>
            Mengimport...
        `;


                /* =========================
                   REQUEST
                ========================= */

                fetch('{{ route('admin_import_laminations') }}', {

                        method: 'POST',

                        headers: {
                            'X-CSRF-TOKEN': document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),

                            'Accept': 'application/json'
                        },

                        body: formData

                    })

                    .then(async response => {

                        const data = await response.json();

                        if (!response.ok) {
                            throw data;
                        }

                        return data;

                    })

                    .then(data => {

                        if (!data.success) {

                            showToast(
                                'error',
                                data.message || 'Import laminasi gagal.'
                            );

                            return;
                        }


                        /* =========================
                           SUCCESS
                        ========================= */

                        showToast(
                            'success',
                            data.message || 'Data laminasi berhasil diimport.'
                        );


                        closeModal();


                        /* Reset file */

                        fileInput.value = '';

                        fileName.textContent = 'Belum ada file dipilih';

                        fileName.classList.remove('has-file');


                        /* Reload DataTable */

                        if (
                            typeof laminationTable !== 'undefined' &&
                            laminationTable
                        ) {
                            laminationTable.ajax.reload(null, false);
                        }

                    })

                    .catch(error => {

                        let message =
                            'Terjadi kesalahan saat mengimport data laminasi.';


                        /* =========================
                           VALIDATION ERROR
                        ========================= */

                        if (error && error.errors) {

                            const messages = [];

                            Object.values(error.errors).forEach(errors => {

                                if (Array.isArray(errors)) {

                                    errors.forEach(errorMessage => {
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

                    .finally(() => {

                        /* =========================
                           ENABLE BUTTON
                        ========================= */

                        btnSave.disabled = false;

                        btnSave.innerHTML = originalContent;

                    });

            });

        });

        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('modalTambahLaminasi');
            const btnTambah = document.getElementById('btnTambahLaminasi');
            const btnTutup = document.getElementById('btnTutupModalLaminasi');
            const btnBatal = document.getElementById('btnBatalLaminasi');
            const btnTambahBaris = document.getElementById('btnTambahBarisLaminasi');
            const form = document.getElementById('formTambahLaminasi');
            const laminationRows = document.getElementById('laminationRows');

            if (!modal || !form || !laminationRows) {
                return;
            }

            let laminationIndex = 1;


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


            initSelect2(laminationRows);


            /* =========================================================
               VALIDASI FORM
            ========================================================= */

            form.addEventListener('submit', function(event) {

                form.querySelectorAll('.select2-invalid')
                    .forEach(function(element) {

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


                    /*
                     * Abaikan field yang berada di dalam elemen
                     * yang benar-benar tidak terlihat.
                     */

                    if (
                        field.offsetParent === null &&
                        !(
                            typeof $ !== 'undefined' &&
                            $(field).hasClass('select2-hidden-accessible')
                        )
                    ) {
                        return;
                    }


                    /*
                     * SELECT2
                     */

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


                    /*
                     * INPUT
                     */

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
               TAMBAH BARIS LAMINASI
            ========================================================= */

            if (btnTambahBaris) {

                btnTambahBaris.addEventListener('click', function() {

                    const index = laminationIndex;


                    const row = document.createElement('div');

                    row.className = 'material-row';

                    row.dataset.laminationIndex = index;
                    row.dataset.sizeIndex = 1;


                    row.innerHTML = `

                        <!-- DATA LAMINASI -->

                        <div style="display: flex; justify-content: right; align-items: center;">
                            <!-- HAPUS -->

                            <div class="material-field material-field--action">
                                <button
                                    type="button"
                                    class="btn-remove-category"
                                    aria-label="Hapus laminasi"
                                    style="border: none !important;"
                                >
                                    <i class="bi bi-trash3-fill"></i>
                                </button>

                            </div>
                        </div>

                        <div class="material-row-top material-row-top--lamination">

                            <!-- KODE -->

                            <div class="material-field">

                                <label class="category-label">
                                    Kode Laminasi
                                </label>

                                <input
                                    type="text"
                                    name="laminations[${index}][lamination_code]"
                                    class="input"
                                    placeholder="Masukkan kode laminasi..."
                                >

                            </div>


                            <!-- NAMA -->

                            <div class="material-field">

                                <label class="category-label">
                                    Nama Laminasi
                                </label>

                                <input
                                    type="text"
                                    name="laminations[${index}][name]"
                                    class="input"
                                    placeholder="Masukkan nama laminasi..."
                                    required
                                >

                            </div>


                            <!-- STATUS -->

                            <div class="material-field">

                                <label class="category-label">
                                    Status
                                </label>

                                <select
                                    name="laminations[${index}][status]"
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


                        <!-- UKURAN -->

                        <div class="material-sizes">

                            <div class="material-sizes-header">

                                <div>

                                    <label class="category-label">
                                        Ukuran Laminasi
                                    </label>

                                    <p class="category-description">
                                        Tambahkan satu atau beberapa ukuran laminasi.
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

                                    <!-- LEBAR -->

                                    <div class="material-field">

                                        <label class="category-label">
                                            Lebar
                                        </label>

                                        <input
                                            type="number"
                                            name="laminations[${index}][sizes][0][width]"
                                            class="input"
                                            placeholder="Masukkan lebar..."
                                            min="0"
                                            step="0.01"
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
                                            name="laminations[${index}][sizes][0][length]"
                                            class="input"
                                            placeholder="Masukkan panjang..."
                                            min="0"
                                            step="0.01"
                                        >

                                    </div>


                                    <!-- UNIT -->

                                    <div class="material-field">

                                        <label class="category-label">
                                            Unit
                                        </label>

                                        <select
                                            name="laminations[${index}][sizes][0][unit]"
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


                                    <!-- HAPUS -->

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


                    laminationRows.appendChild(row);


                    initSelect2(row);


                    const nameInput = row.querySelector(
                        'input[name*="[name]"]'
                    );


                    if (nameInput) {
                        nameInput.focus();
                    }


                    laminationIndex++;

                });

            }


            /* =========================================================
               TAMBAH UKURAN
            ========================================================= */

            laminationRows.addEventListener('click', function(event) {

                const addSizeButton = event.target.closest(
                    '.btnTambahUkuran'
                );


                if (!addSizeButton) {
                    return;
                }


                const laminationRow = addSizeButton.closest(
                    '.material-row'
                );


                if (!laminationRow) {
                    return;
                }


                const sizeRows = laminationRow.querySelector(
                    '.material-size-rows'
                );


                if (!sizeRows) {
                    return;
                }


                const currentLaminationIndex =
                    laminationRow.dataset.laminationIndex;


                const sizeIndex =
                    parseInt(
                        laminationRow.dataset.sizeIndex || '1',
                        10
                    );


                const row = document.createElement('div');

                row.className = 'material-size-row';


                row.innerHTML = `

                    <!-- LEBAR -->

                    <div class="material-field">

                        <label class="category-label">
                            Lebar
                        </label>

                        <input
                            type="number"
                            name="laminations[${currentLaminationIndex}][sizes][${sizeIndex}][width]"
                            class="input"
                            placeholder="Masukkan lebar..."
                            min="0"
                            step="0.01"
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
                            name="laminations[${currentLaminationIndex}][sizes][${sizeIndex}][length]"
                            class="input"
                            placeholder="Masukkan panjang..."
                            min="0"
                            step="0.01"
                        >

                    </div>


                    <!-- UNIT -->

                    <div class="material-field">

                        <label class="category-label">
                            Unit
                        </label>

                        <select
                            name="laminations[${currentLaminationIndex}][sizes][${sizeIndex}][unit]"
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


                laminationRow.dataset.sizeIndex =
                    sizeIndex + 1;


                initSelect2(row);


                const widthInput = row.querySelector(
                    'input[name*="[width]"]'
                );


                if (widthInput) {
                    widthInput.focus();
                }

            });


            /* =========================================================
               HAPUS LAMINASI
            ========================================================= */

            laminationRows.addEventListener('click', function(event) {

                const removeButton = event.target.closest(
                    '.btn-remove-category'
                );


                if (!removeButton) {
                    return;
                }


                const rows = laminationRows.querySelectorAll(
                    '.material-row'
                );


                const laminationRow = removeButton.closest(
                    '.material-row'
                );


                if (!laminationRow) {
                    return;
                }


                /*
                 * Kalau hanya tersisa satu baris,
                 * jangan hapus elemennya.
                 * Cukup reset isi form.
                 */

                if (rows.length <= 1) {

                    const code = laminationRow.querySelector(
                        'input[name*="[lamination_code]"]'
                    );


                    const name = laminationRow.querySelector(
                        'input[name*="[name]"]'
                    );


                    const status = laminationRow.querySelector(
                        'select[name*="[status]"]'
                    );


                    if (code) {
                        code.value = '';
                    }


                    if (name) {
                        name.value = '';
                    }


                    if (status) {

                        $(status)
                            .val('Active')
                            .trigger('change');

                    }


                    const sizeRows =
                        laminationRow.querySelectorAll(
                            '.material-size-row'
                        );


                    sizeRows.forEach(function(sizeRow, index) {

                        if (index === 0) {

                            const width = sizeRow.querySelector(
                                'input[name*="[width]"]'
                            );


                            const length = sizeRow.querySelector(
                                'input[name*="[length]"]'
                            );


                            const unit = sizeRow.querySelector(
                                'select[name*="[unit]"]'
                            );


                            if (width) {
                                width.value = '';
                            }


                            if (length) {
                                length.value = '';
                            }


                            if (unit) {

                                $(unit)
                                    .val('')
                                    .trigger('change');

                            }

                        } else {

                            destroySelect2(sizeRow);

                            sizeRow.remove();

                        }

                    });


                    laminationRow.dataset.sizeIndex = 1;


                    if (name) {
                        name.focus();
                    }


                    return;

                }


                destroySelect2(laminationRow);

                laminationRow.remove();

            });


            /* =========================================================
               HAPUS UKURAN
            ========================================================= */

            laminationRows.addEventListener('click', function(event) {

                const removeButton = event.target.closest(
                    '.btn-remove-size'
                );


                if (!removeButton) {
                    return;
                }


                const laminationRow = removeButton.closest(
                    '.material-row'
                );


                if (!laminationRow) {
                    return;
                }


                const sizeRows =
                    laminationRow.querySelectorAll(
                        '.material-size-row'
                    );


                const sizeRow = removeButton.closest(
                    '.material-size-row'
                );


                if (!sizeRow) {
                    return;
                }


                /*
                 * Kalau hanya ada satu ukuran,
                 * jangan hapus barisnya.
                 * Reset saja.
                 */

                if (sizeRows.length <= 1) {

                    const width = sizeRow.querySelector(
                        'input[name*="[width]"]'
                    );


                    const length = sizeRow.querySelector(
                        'input[name*="[length]"]'
                    );


                    const unit = sizeRow.querySelector(
                        'select[name*="[unit]"]'
                    );


                    if (width) {
                        width.value = '';
                    }


                    if (length) {
                        length.value = '';
                    }


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


                destroySelect2(sizeRow);

                sizeRow.remove();

            });

        });

        let laminationTable;

        document.addEventListener('DOMContentLoaded', function() {

            laminationTable = new DataTable('#laminationTable', {

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
                    url: "{{ route('admin_data_laminations') }}",
                    type: "GET"
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

                            const pageInfo = laminationTable.page.info();

                            return pageInfo.start + meta.row + 1;
                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | LAMINATION CODE
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'lamination_code',
                        responsivePriority: 100,

                        render: function(data) {
                            return data ?? '-';
                        },

                        createdCell: function(td) {
                            td.style.whiteSpace = 'normal';
                            td.style.wordBreak = 'break-word';
                            td.style.overflowWrap = 'break-word';
                            td.style.textAlign = 'center';
                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | NAMA LAMINASI
                    |--------------------------------------------------------------------------
                    */

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

                    /*
                    |--------------------------------------------------------------------------
                    | TOTAL UKURAN
                    |--------------------------------------------------------------------------
                    */

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


                    /*
                    |--------------------------------------------------------------------------
                    | STATUS
                    |--------------------------------------------------------------------------
                    */

                    {
                        data: 'status',
                        responsivePriority: 100,

                        render: function(data) {

                            const statusClass =
                                data === 'Active' ?
                                'success' :
                                'danger';

                            return `
                        <span class="badge ${statusClass} dot">
                            ${data}
                        </span>
                    `;
                        }
                    },


                    /*
                    |--------------------------------------------------------------------------
                    | AKSI
                    |--------------------------------------------------------------------------
                    */

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
                                class="btn--icon btn-view-lamination"
                                data-id="${data}"
                                aria-label="View"
                            >
                                <i class="bi bi-arrows-fullscreen"></i>
                            </button>


                            <!-- EDIT -->
                            <button
                                type="button"
                                class="btn--icon btn-edit-lamination"
                                data-id="${data}"
                                aria-label="Edit"
                            >
                                <i class="bi bi-pen"></i>
                            </button>


                            <!-- DELETE -->
                            <button
                                type="button"
                                class="btn--icon btn-delete-lamination"
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
                    zeroRecords: 'Data laminasi tidak ditemukan',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari laminasi...'
                },


                /*
                |--------------------------------------------------------------------------
                | INIT COMPLETE
                |--------------------------------------------------------------------------
                */

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

        });

        document.addEventListener('DOMContentLoaded', function() {

            /* =========================================================
               ELEMENT
            ========================================================= */

            const modal =
                document.getElementById('modalEditLaminasi');

            const form =
                document.getElementById('formEditLaminasi');

            const editLaminationId =
                document.getElementById('editLaminationId');

            const editLaminationCode =
                document.getElementById('editLaminationCode');

            const editLaminationName =
                document.getElementById('editLaminationName');

            const editLaminationStatus =
                document.getElementById('editLaminationStatus');

            const editLaminationRows =
                document.getElementById('editLaminationRows');

            const btnTutup =
                document.getElementById(
                    'btnTutupModalEditLaminasi'
                );

            const btnBatal =
                document.getElementById(
                    'btnBatalEditLaminasi'
                );

            const btnHapusLaminasi =
                document.getElementById(
                    'btnHapusEditLaminasi'
                );


            if (!modal || !form) {
                return;
            }


            /* =========================================================
               STATE
            ========================================================= */

            let editSizeIndex = 0;


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


                $(container)
                    .find('.select2')
                    .each(function() {

                        if (
                            $(this).hasClass(
                                'select2-hidden-accessible'
                            )
                        ) {
                            return;
                        }


                        $(this).select2({
                            width: '100%',
                            dropdownParent: $(modal)
                        });

                    });

            }


            /* =========================================================
               DESTROY SELECT2
            ========================================================= */

            function destroySelect2(container) {

                if (
                    typeof $ === 'undefined' ||
                    typeof $.fn.select2 === 'undefined'
                ) {
                    return;
                }


                $(container)
                    .find('.select2')
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


            /* =========================================================
               INITIAL SELECT2
            ========================================================= */

            initSelect2(form);


            /* =========================================================
               CREATE SIZE ROW
            ========================================================= */

            function createSizeRow(size = {}, index = 0) {

                const sizeRows =
                    editLaminationRows.querySelector(
                        '.material-size-rows'
                    );


                if (!sizeRows) {
                    return;
                }


                const row =
                    document.createElement('div');


                row.className =
                    'material-size-row';


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


                initSelect2(row);

            }


            /* =========================================================
               TAMBAH UKURAN
            ========================================================= */

            editLaminationRows.addEventListener(
                'click',
                function(event) {

                    const addSizeButton =
                        event.target.closest(
                            '.btnTambahUkuranEdit'
                        );


                    if (!addSizeButton) {
                        return;
                    }


                    createSizeRow({},
                        editSizeIndex
                    );


                    editSizeIndex++;


                    const rows =
                        editLaminationRows.querySelectorAll(
                            '.material-size-row'
                        );


                    const lastRow =
                        rows[rows.length - 1];


                    if (lastRow) {

                        const widthInput =
                            lastRow.querySelector(
                                'input[name*="[width]"]'
                            );


                        if (widthInput) {
                            widthInput.focus();
                        }

                    }

                }
            );


            /* =========================================================
               HAPUS UKURAN
            ========================================================= */

            editLaminationRows.addEventListener(
                'click',
                function(event) {

                    const removeButton =
                        event.target.closest(
                            '.btn-remove-size'
                        );


                    if (!removeButton) {
                        return;
                    }


                    const sizeRow =
                        removeButton.closest(
                            '.material-size-row'
                        );


                    if (!sizeRow) {
                        return;
                    }


                    const sizeRows =
                        editLaminationRows.querySelectorAll(
                            '.material-size-row'
                        );


                    /* ---------------------------------------------
                       MINIMAL SATU UKURAN
                    --------------------------------------------- */

                    if (sizeRows.length <= 1) {

                        const width =
                            sizeRow.querySelector(
                                'input[name*="[width]"]'
                            );


                        const length =
                            sizeRow.querySelector(
                                'input[name*="[length]"]'
                            );


                        const unit =
                            sizeRow.querySelector(
                                'select[name*="[unit]"]'
                            );


                        if (width) {
                            width.value = '';
                        }


                        if (length) {
                            length.value = '';
                        }


                        if (unit) {

                            if (
                                typeof $ !== 'undefined' &&
                                typeof $.fn.select2 !== 'undefined'
                            ) {

                                $(unit)
                                    .val('')
                                    .trigger('change');

                            } else {

                                unit.value = '';

                            }

                        }


                        if (width) {
                            width.focus();
                        }


                        return;

                    }


                    /* ---------------------------------------------
                       HAPUS ROW
                    --------------------------------------------- */

                    destroySelect2(sizeRow);

                    sizeRow.remove();

                }
            );


            /* =========================================================
               BUKA EDIT
            ========================================================= */

            document.addEventListener(
                'click',
                function(event) {

                    const button =
                        event.target.closest(
                            '.btn-edit-lamination'
                        );


                    if (!button) {
                        return;
                    }


                    const id =
                        button.getAttribute('data-id');


                    if (!id) {

                        showToast(
                            'error',
                            'ID laminasi tidak ditemukan.'
                        );

                        return;

                    }


                    fetch(
                            `{{ url('/admin/laminations') }}/${id}/edit`, {
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
                                    'Gagal mengambil data laminasi.'
                                );

                            }


                            return response.json();

                        })

                        .then(function(result) {

                            if (!result.success) {

                                showToast(
                                    'error',
                                    result.message ||
                                    'Data laminasi gagal dimuat.'
                                );

                                return;

                            }


                            const lamination =
                                result.data;


                            /* =========================================
                               RESET DATA UTAMA
                            ========================================= */

                            editLaminationId.value =
                                lamination.id || '';


                            editLaminationCode.value =
                                lamination.lamination_code || '';


                            editLaminationName.value =
                                lamination.name || '';


                            /* =========================================
                               STATUS
                            ========================================= */

                            if (
                                typeof $ !== 'undefined' &&
                                typeof $.fn.select2 !== 'undefined'
                            ) {

                                $('#editLaminationStatus')
                                    .val(
                                        lamination.status || 'Active'
                                    )
                                    .trigger('change');

                            } else {

                                editLaminationStatus.value =
                                    lamination.status || 'Active';

                            }


                            /* =========================================
                               FORM ACTION
                            ========================================= */

                            form.action =
                                `{{ url('/admin/laminations') }}/${lamination.id}/update`;


                            /* =========================================
                               RESET SIZE
                            ========================================= */

                            const sizeRows =
                                editLaminationRows.querySelector(
                                    '.material-size-rows'
                                );


                            destroySelect2(sizeRows);


                            sizeRows.innerHTML = '';


                            editSizeIndex = 0;


                            /* =========================================
                               LOAD SIZE
                            ========================================= */

                            if (
                                Array.isArray(
                                    lamination.sizes
                                ) &&
                                lamination.sizes.length > 0
                            ) {

                                lamination.sizes.forEach(
                                    function(size) {

                                        createSizeRow(
                                            size,
                                            editSizeIndex
                                        );


                                        editSizeIndex++;

                                    }
                                );

                            } else {

                                createSizeRow({},
                                    editSizeIndex
                                );


                                editSizeIndex++;

                            }


                            /* =========================================
                               OPEN MODAL
                            ========================================= */

                            modal.classList.add(
                                'is-open'
                            );

                        })

                        .catch(function(error) {

                            console.error(error);


                            showToast(
                                'error',
                                'Terjadi kesalahan saat mengambil data laminasi.'
                            );

                        });

                }
            );


            /* =========================================================
               CLOSE MODAL
            ========================================================= */

            function tutupModal() {

                modal.classList.remove(
                    'is-open'
                );

            }


            if (btnTutup) {

                btnTutup.addEventListener(
                    'click',
                    tutupModal
                );

            }


            if (btnBatal) {

                btnBatal.addEventListener(
                    'click',
                    tutupModal
                );

            }


            /* =========================================================
               CLICK OVERLAY
            ========================================================= */

            modal.addEventListener(
                'click',
                function(event) {

                    if (event.target === modal) {
                        tutupModal();
                    }

                }
            );


            /* =========================================================
               ESC
            ========================================================= */

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


            /* =========================================================
               VALIDATE FORM
            ========================================================= */

            function validateForm() {

                /* ---------------------------------------------
                   NAME
                --------------------------------------------- */

                if (
                    !editLaminationName.value.trim()
                ) {

                    showToast(
                        'error',
                        'Nama laminasi wajib diisi.'
                    );


                    editLaminationName.focus();


                    return false;

                }


                /* ---------------------------------------------
                   STATUS
                --------------------------------------------- */

                if (
                    !editLaminationStatus.value
                ) {

                    showToast(
                        'error',
                        'Status laminasi wajib dipilih.'
                    );


                    if (
                        typeof $ !== 'undefined' &&
                        typeof $.fn.select2 !== 'undefined'
                    ) {

                        $('#editLaminationStatus')
                            .select2('open');

                    }


                    return false;

                }


                /* ---------------------------------------------
                   SIZE
                --------------------------------------------- */

                const sizeRows =
                    editLaminationRows.querySelectorAll(
                        '.material-size-row'
                    );


                if (sizeRows.length === 0) {

                    showToast(
                        'error',
                        'Minimal harus ada satu ukuran laminasi.'
                    );


                    return false;

                }


                let valid = true;


                sizeRows.forEach(
                    function(sizeRow) {

                        const width =
                            sizeRow.querySelector(
                                'input[name*="[width]"]'
                            );


                        const length =
                            sizeRow.querySelector(
                                'input[name*="[length]"]'
                            );


                        const unit =
                            sizeRow.querySelector(
                                'select[name*="[unit]"]'
                            );


                        /* -----------------------------------------
                           WIDTH
                        ----------------------------------------- */

                        if (
                            !width.value ||
                            parseFloat(width.value) <= 0
                        ) {

                            valid = false;


                            width.classList.add(
                                'is-invalid'
                            );

                        } else {

                            width.classList.remove(
                                'is-invalid'
                            );

                        }


                        /* -----------------------------------------
                           LENGTH
                           OPTIONAL
                        ----------------------------------------- */

                        if (
                            length.value !== '' &&
                            parseFloat(length.value) <= 0
                        ) {

                            valid = false;


                            length.classList.add(
                                'is-invalid'
                            );

                        } else {

                            length.classList.remove(
                                'is-invalid'
                            );

                        }


                        /* -----------------------------------------
                           UNIT
                        ----------------------------------------- */

                        if (!unit.value) {

                            valid = false;


                            if (
                                typeof $ !== 'undefined' &&
                                typeof $.fn.select2 !== 'undefined'
                            ) {

                                $(unit)
                                    .next('.select2-container')
                                    .addClass(
                                        'select2-invalid'
                                    );

                            }

                        } else {

                            if (
                                typeof $ !== 'undefined' &&
                                typeof $.fn.select2 !== 'undefined'
                            ) {

                                $(unit)
                                    .next('.select2-container')
                                    .removeClass(
                                        'select2-invalid'
                                    );

                            }

                        }

                    }
                );


                if (!valid) {

                    showToast(
                        'error',
                        'Periksa kembali data laminasi.'
                    );

                }


                return valid;

            }


            /* =========================================================
               SUBMIT
            ========================================================= */

            form.addEventListener(
                'submit',
                function(event) {

                    event.preventDefault();


                    /* ---------------------------------------------
                       VALIDATE
                    --------------------------------------------- */

                    if (!validateForm()) {
                        return;
                    }


                    /* ---------------------------------------------
                       FORM DATA
                    --------------------------------------------- */

                    const formData =
                        new FormData(form);


                    /* ---------------------------------------------
                       SUBMIT
                    --------------------------------------------- */

                    fetch(
                            form.action, {
                                method: 'POST',

                                headers: {
                                    'Accept': 'application/json',

                                    'X-Requested-With': 'XMLHttpRequest',

                                    'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]'
                                        )?.getAttribute(
                                            'content'
                                        ) ||
                                        document.querySelector(
                                            'input[name="_token"]'
                                        )?.value ||
                                        ''
                                },

                                body: formData

                            }
                        )

                        .then(function(response) {

                            return response
                                .json()
                                .then(function(result) {

                                    return {
                                        ok: response.ok,
                                        result: result
                                    };

                                })
                                .catch(function() {

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

                            const result =
                                responseData.result;


                            if (
                                !responseData.ok ||
                                !result.success
                            ) {

                                showToast(
                                    result.type || 'error',

                                    result.message ||
                                    'Laminasi gagal diperbarui.'
                                );


                                return;

                            }


                            /* -----------------------------------------
                               SUCCESS
                            ----------------------------------------- */

                            showToast(
                                'success',

                                result.message ||
                                'Laminasi berhasil diperbarui.'
                            );


                            tutupModal();


                            /* -----------------------------------------
                               DATATABLE
                            ----------------------------------------- */

                            if (
                                typeof $ !== 'undefined' &&
                                $.fn.DataTable &&
                                $.fn.DataTable.isDataTable(
                                    '#laminationTable'
                                )
                            ) {

                                $('#laminationTable')
                                    .DataTable()
                                    .ajax
                                    .reload(
                                        null,
                                        false
                                    );

                            }

                        })

                        .catch(function(error) {

                            console.error(error);


                            showToast(
                                'error',
                                'Terjadi kesalahan saat memperbarui laminasi.'
                            );

                        });

                }
            );


            /* =========================================================
               HAPUS LAMINASI
            ========================================================= */

            if (btnHapusLaminasi) {

                btnHapusLaminasi.addEventListener(
                    'click',
                    function() {

                        const id =
                            editLaminationId.value;


                        if (!id) {

                            showToast(
                                'error',
                                'ID laminasi tidak ditemukan.'
                            );


                            return;

                        }


                        if (
                            typeof Swal === 'undefined'
                        ) {
                            return;
                        }


                        Swal.fire({

                            title: 'Hapus Laminasi?',

                            text: 'Data laminasi beserta ukurannya akan dihapus.',

                            icon: 'warning',

                            showCancelButton: true,

                            confirmButtonText: 'Ya, Hapus',

                            cancelButtonText: 'Batal'

                        }).then(function(result) {

                            if (!result.isConfirmed) {
                                return;
                            }


                            fetch(
                                    `{{ url('/admin/laminations') }}/${id}/delete`, {
                                        method: 'POST',

                                        headers: {

                                            'Accept': 'application/json',

                                            'X-Requested-With': 'XMLHttpRequest',

                                            'X-CSRF-TOKEN': document.querySelector(
                                                    'meta[name="csrf-token"]'
                                                )?.getAttribute(
                                                    'content'
                                                ) ||
                                                document.querySelector(
                                                    'input[name="_token"]'
                                                )?.value ||
                                                ''

                                        },

                                        body: new URLSearchParams({
                                            _method: 'DELETE'
                                        })

                                    }
                                )

                                .then(function(response) {

                                    return response
                                        .json()
                                        .then(function(data) {

                                            return {
                                                ok: response.ok,
                                                data: data
                                            };

                                        });

                                })

                                .then(function(responseData) {

                                    const data =
                                        responseData.data;


                                    if (
                                        !responseData.ok ||
                                        !data.success
                                    ) {

                                        showToast(
                                            data.type || 'error',

                                            data.message ||
                                            'Laminasi gagal dihapus.'
                                        );


                                        return;

                                    }


                                    showToast(
                                        'success',

                                        data.message ||
                                        'Laminasi berhasil dihapus.'
                                    );


                                    tutupModal();


                                    if (
                                        typeof $ !== 'undefined' &&
                                        $.fn.DataTable &&
                                        $.fn.DataTable.isDataTable(
                                            '#laminationTable'
                                        )
                                    ) {

                                        $('#laminationTable')
                                            .DataTable()
                                            .ajax
                                            .reload(
                                                null,
                                                false
                                            );

                                    }

                                })

                                .catch(function(error) {

                                    console.error(error);


                                    showToast(
                                        'error',

                                        'Terjadi kesalahan saat menghapus laminasi.'
                                    );

                                });

                        });

                    }
                );

            }

        });

        document.addEventListener('DOMContentLoaded', function() {

            const modalDetailLaminasi =
                document.getElementById('modalDetailLaminasi');


            /*
            |--------------------------------------------------------------------------
            | Helper
            |--------------------------------------------------------------------------
            */

            function setDetailValue(id, value) {

                const element =
                    document.getElementById(id);

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


            function setStatus(status) {

                const element =
                    document.getElementById(
                        'detailLaminationStatus'
                    );

                if (!element) {
                    return;
                }

                if (!status) {
                    element.textContent = '-';
                    return;
                }

                const statusClass =
                    status === 'Active' ?
                    'success' :
                    'danger';

                element.innerHTML = `
            <span class="badge ${statusClass} dot">
                ${status}
            </span>
        `;
            }


            /*
            |--------------------------------------------------------------------------
            | Reset Modal
            |--------------------------------------------------------------------------
            */

            function resetDetailModal() {

                setDetailValue(
                    'detailLaminationCode',
                    '-'
                );

                setDetailValue(
                    'detailLaminationName',
                    '-'
                );

                const statusElement =
                    document.getElementById(
                        'detailLaminationStatus'
                    );

                if (statusElement) {
                    statusElement.textContent = '-';
                }

                setDetailValue(
                    'detailCreatedBy',
                    '-'
                );

                setDetailValue(
                    'detailCreatedAt',
                    '-'
                );

                setDetailValue(
                    'detailUpdatedBy',
                    '-'
                );

                setDetailValue(
                    'detailUpdatedAt',
                    '-'
                );


                const sizeBody =
                    document.getElementById(
                        'detailLaminationSizes'
                    );

                if (sizeBody) {

                    sizeBody.innerHTML = `
                <tr>
                    <td
                        colspan="3"
                        class="lamination-detail-empty"
                    >
                        Belum ada ukuran.
                    </td>
                </tr>
            `;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Loading State
            |--------------------------------------------------------------------------
            */

            function setLoadingState() {

                const sizeBody =
                    document.getElementById(
                        'detailLaminationSizes'
                    );

                if (sizeBody) {

                    sizeBody.innerHTML = `
                <tr>
                    <td
                        colspan="3"
                        class="lamination-detail-empty"
                    >
                        Memuat data...
                    </td>
                </tr>
            `;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Format Tanggal
            |--------------------------------------------------------------------------
            */

            function formatDateTime(value) {

                if (!value) {
                    return '-';
                }

                const date = new Date(
                    value.replace(' ', 'T')
                );

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


            /*
            |--------------------------------------------------------------------------
            | Render Ukuran
            |--------------------------------------------------------------------------
            */

            function renderSizes(sizes) {

                const sizeBody =
                    document.getElementById(
                        'detailLaminationSizes'
                    );

                if (!sizeBody) {
                    return;
                }

                sizeBody.innerHTML = '';


                if (!sizes || sizes.length === 0) {

                    sizeBody.innerHTML = `
                <tr>
                    <td
                        colspan="3"
                        class="lamination-detail-empty"
                    >
                        Belum ada ukuran.
                    </td>
                </tr>
            `;

                    return;
                }


                sizes.forEach(function(size) {

                    const width =
                        size.width !== null &&
                        size.width !== undefined &&
                        size.width !== '' ?
                        size.width :
                        '-';


                    const length =
                        size.length !== null &&
                        size.length !== undefined &&
                        size.length !== '' ?
                        size.length :
                        '-';


                    const unit =
                        size.unit ||
                        '-';


                    const row =
                        document.createElement('tr');


                    const widthCell =
                        document.createElement('td');

                    const lengthCell =
                        document.createElement('td');

                    const unitCell =
                        document.createElement('td');


                    widthCell.textContent =
                        width;

                    lengthCell.textContent =
                        length;

                    unitCell.textContent =
                        unit;


                    row.appendChild(
                        widthCell
                    );

                    row.appendChild(
                        lengthCell
                    );

                    row.appendChild(
                        unitCell
                    );


                    sizeBody.appendChild(
                        row
                    );
                });
            }


            /*
            |--------------------------------------------------------------------------
            | Klik Tombol Detail
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'click',
                function(event) {

                    const button =
                        event.target.closest(
                            '.btn-view-lamination'
                        );

                    if (!button) {
                        return;
                    }


                    const id =
                        button.dataset.id;


                    if (!id) {

                        console.error(
                            'ID laminasi tidak ditemukan.'
                        );

                        showToast(
                            'error',
                            'ID laminasi tidak ditemukan.'
                        );

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Reset Data Sebelum Request
                    |--------------------------------------------------------------------------
                    */

                    resetDetailModal();
                    setLoadingState();


                    /*
                    |--------------------------------------------------------------------------
                    | Ambil Data Laminasi
                    |--------------------------------------------------------------------------
                    */

                    fetch(
                            `{{ url('/admin/laminations') }}/${id}/details`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json'
                                }
                            }
                        )

                        .then(async function(response) {

                            const data =
                                await response.json();


                            if (!response.ok) {

                                throw new Error(
                                    data.message ||
                                    'Gagal mengambil detail laminasi.'
                                );
                            }


                            return data;
                        })


                        .then(function(data) {

                            if (!data.success) {

                                showToast(
                                    'error',
                                    data.message ||
                                    'Gagal mengambil detail laminasi.'
                                );

                                return;
                            }


                            const lamination =
                                data.data;


                            /*
                            |--------------------------------------------------------------------------
                            | Data Laminasi
                            |--------------------------------------------------------------------------
                            */

                            setDetailValue(
                                'detailLaminationCode',
                                lamination.lamination_code
                            );


                            setDetailValue(
                                'detailLaminationName',
                                lamination.name
                            );


                            setStatus(
                                lamination.status
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | Ukuran Laminasi
                            |--------------------------------------------------------------------------
                            */

                            renderSizes(
                                lamination.sizes
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | Informasi Audit
                            |--------------------------------------------------------------------------
                            */

                            setDetailValue(
                                'detailCreatedBy',
                                lamination.created_by
                            );


                            setDetailValue(
                                'detailCreatedAt',
                                formatDateTime(
                                    lamination.created_at
                                )
                            );


                            setDetailValue(
                                'detailUpdatedBy',
                                lamination.updated_by
                            );


                            setDetailValue(
                                'detailUpdatedAt',
                                formatDateTime(
                                    lamination.updated_at
                                )
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | Buka Modal
                            |--------------------------------------------------------------------------
                            */

                            modalDetailLaminasi.classList.add(
                                'is-open'
                            );
                        })


                        .catch(function(error) {

                            console.error(error);

                            showToast(
                                'error',
                                error.message ||
                                'Terjadi kesalahan saat mengambil detail laminasi.'
                            );
                        });
                }
            );


            /*
            |--------------------------------------------------------------------------
            | Tutup Modal - Tombol X
            |--------------------------------------------------------------------------
            */

            document
                .getElementById(
                    'btnTutupModalDetailLaminasi'
                )
                .addEventListener(
                    'click',
                    function() {

                        modalDetailLaminasi.classList.remove(
                            'is-open'
                        );
                    }
                );


            /*
            |--------------------------------------------------------------------------
            | Tutup Modal - Tombol Tutup
            |--------------------------------------------------------------------------
            */

            document
                .getElementById(
                    'btnBatalDetailLaminasi'
                )
                .addEventListener(
                    'click',
                    function() {

                        modalDetailLaminasi.classList.remove(
                            'is-open'
                        );
                    }
                );


            /*
            |--------------------------------------------------------------------------
            | Tutup Modal - Klik Overlay
            |--------------------------------------------------------------------------
            */

            modalDetailLaminasi.addEventListener(
                'click',
                function(event) {

                    if (
                        event.target ===
                        modalDetailLaminasi
                    ) {

                        modalDetailLaminasi.classList.remove(
                            'is-open'
                        );
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | Tutup Modal - Tombol ESC
            |--------------------------------------------------------------------------
            */

            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        modalDetailLaminasi.classList.contains(
                            'is-open'
                        )
                    ) {

                        modalDetailLaminasi.classList.remove(
                            'is-open'
                        );
                    }
                }
            );

        });


        document.addEventListener('DOMContentLoaded', function() {

            /*
            |--------------------------------------------------------------------------
            | DELETE LAMINASI
            |--------------------------------------------------------------------------
            */

            let laminationIdToDelete = null;
            let isDeletingLamination = false;

            const modalDeleteLaminasi = document.getElementById(
                'modalDeleteLaminasi'
            );

            const btnTutupDeleteLaminasi = document.getElementById(
                'btnTutupDeleteLaminasi'
            );

            const btnBatalDeleteLaminasi = document.getElementById(
                'btnBatalDeleteLaminasi'
            );

            const btnConfirmDeleteLaminasi = document.getElementById(
                'btnConfirmDeleteLaminasi'
            );


            /*
            |--------------------------------------------------------------------------
            | BUKA MODAL DELETE
            |--------------------------------------------------------------------------
            */

            document.addEventListener('click', function(event) {

                const deleteButton = event.target.closest(
                    '.btn-delete-lamination'
                );

                if (!deleteButton) {
                    return;
                }

                if (isDeletingLamination) {
                    return;
                }

                laminationIdToDelete = deleteButton.dataset.id;

                modalDeleteLaminasi.classList.add('is-open');
            });


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL
            |--------------------------------------------------------------------------
            */

            function tutupDeleteLaminasiModal() {

                modalDeleteLaminasi.classList.remove('is-open');

                laminationIdToDelete = null;
            }


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL - TOMBOL X
            |--------------------------------------------------------------------------
            */

            btnTutupDeleteLaminasi.addEventListener(
                'click',
                function() {

                    if (isDeletingLamination) {
                        return;
                    }

                    tutupDeleteLaminasiModal();
                }
            );


            /*
            |--------------------------------------------------------------------------
            | TUTUP MODAL - TOMBOL BATAL
            |--------------------------------------------------------------------------
            */

            btnBatalDeleteLaminasi.addEventListener(
                'click',
                function() {

                    if (isDeletingLamination) {
                        return;
                    }

                    tutupDeleteLaminasiModal();
                }
            );


            /*
            |--------------------------------------------------------------------------
            | KLIK BACKGROUND
            |--------------------------------------------------------------------------
            */

            modalDeleteLaminasi.addEventListener(
                'click',
                function(event) {

                    if (
                        event.target === modalDeleteLaminasi &&
                        !isDeletingLamination
                    ) {
                        tutupDeleteLaminasiModal();
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
                        modalDeleteLaminasi.classList.contains('is-open') &&
                        !isDeletingLamination
                    ) {
                        tutupDeleteLaminasiModal();
                    }
                }
            );


            /*
            |--------------------------------------------------------------------------
            | KONFIRMASI HAPUS LAMINASI
            |--------------------------------------------------------------------------
            */

            btnConfirmDeleteLaminasi.addEventListener(
                'click',
                async function() {

                    /*
                    |--------------------------------------------------------------------------
                    | CEK ID LAMINASI
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !laminationIdToDelete ||
                        isDeletingLamination
                    ) {
                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | LOCK PROSES DELETE
                    |--------------------------------------------------------------------------
                    */

                    isDeletingLamination = true;

                    const originalButtonContent =
                        btnConfirmDeleteLaminasi.innerHTML;

                    btnConfirmDeleteLaminasi.disabled = true;

                    btnConfirmDeleteLaminasi.innerHTML = `
                <span
                    class="spinner-border spinner-border-sm"
                    role="status"
                    aria-hidden="true">
                </span>
                Menghapus...
            `;


                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | REQUEST DELETE
                        |--------------------------------------------------------------------------
                        */

                        const response = await fetch(
                            `/admin/laminations/${laminationIdToDelete}/delete`, {
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

                        let result = null;

                        try {
                            result = await response.json();
                        } catch (jsonError) {
                            result = null;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | BERHASIL
                        |--------------------------------------------------------------------------
                        */

                        if (
                            response.ok &&
                            result &&
                            result.success
                        ) {

                            /*
                            |--------------------------------------------------------------------------
                            | TUTUP MODAL
                            |--------------------------------------------------------------------------
                            */

                            tutupDeleteLaminasiModal();


                            /*
                            |--------------------------------------------------------------------------
                            | RELOAD DATATABLE
                            |--------------------------------------------------------------------------
                            */

                            laminationTable.ajax.reload(
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

                            return;
                        }


                        /*
                        |--------------------------------------------------------------------------
                        | GAGAL
                        |--------------------------------------------------------------------------
                        */

                        tutupDeleteLaminasiModal();

                        showToast(
                            'error',
                            result && result.message ?
                            result.message :
                            'Laminasi gagal dihapus.'
                        );

                    } catch (error) {

                        console.error(error);


                        /*
                        |--------------------------------------------------------------------------
                        | TUTUP MODAL
                        |--------------------------------------------------------------------------
                        */

                        tutupDeleteLaminasiModal();


                        /*
                        |--------------------------------------------------------------------------
                        | NOTIFIKASI ERROR
                        |--------------------------------------------------------------------------
                        */

                        showToast(
                            'error',
                            'Terjadi kesalahan saat menghapus laminasi.'
                        );

                    } finally {

                        /*
                        |--------------------------------------------------------------------------
                        | RESET DELETE STATE
                        |--------------------------------------------------------------------------
                        */

                        isDeletingLamination = false;

                        btnConfirmDeleteLaminasi.disabled = false;

                        btnConfirmDeleteLaminasi.innerHTML =
                            originalButtonContent;
                    }
                }
            );

        });
    </script>
@endsection

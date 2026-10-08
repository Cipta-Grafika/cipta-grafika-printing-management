@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">KONFIGURASI</span>
                        {{-- <h1 class="hero-title">Daftar Finishing & Jasa</h1> --}}
                        <p class="hero-sub">Kelola biaya material dan finishing yang digunakan sebagai dasar perhitungan
                            biaya serta estimasi harga produk.</p>
                    </div>
                    <div class="hero-actions"><button class="btn btn--ghost"><svg viewBox="0 0 24 24">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                                <path d="M7 10l5 5 5-5" />
                                <path d="M12 15V3" />
                            </svg> Export</button>
                        <button class="btn btn--ghost" id="btnImportFinishingService"><svg viewBox="0 0 24 24">
                                <path d="M12 16V3" />
                                <path d="m7 8 5-5 5 5" />
                                <path d="M5 13v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6" />
                            </svg> Import</button>
                        <a href="{{ route('admin_create_pc_finishing') }}" class="btn btn--primary"><svg
                                viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg> Tambah Ongkos Produksi</a>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <div class="data-toolbar">
                            <div class="data-toolbar-left">
                                <div class="input-icon" style="flex:1;max-width:320px"><span class="ico"><svg
                                            viewBox="0 0 24 24">
                                            <circle cx="11" cy="11" r="7" />
                                            <path d="m21 21-4.3-4.3" />
                                        </svg></span><input class="input" type="search"
                                        placeholder="Search users by name, email, or ID..."></div><button
                                    class="btn btn--ghost"><svg viewBox="0 0 24 24">
                                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3" />
                                    </svg> Filter <span class="badge primary" style="margin-left:4px">2</span></button>
                            </div>
                            <div class="data-toolbar-right">
                                <select class="select" style="width:auto;padding:7px 28px 7px 10px;font-size:12px">
                                    <option>All status</option>
                                    <option>Active</option>
                                    <option>Pending</option>
                                    <option>Inactive</option>
                                </select> <button class="btn btn--ghost btn--icon" aria-label="Columns"><svg
                                        viewBox="0 0 24 24">
                                        <rect x="3" y="3" width="7" height="18" />
                                        <rect x="14" y="3" width="7" height="11" />
                                    </svg></button>
                            </div>
                        </div>
                        <div style="overflow-x:auto;margin:0 -22px">
                            <table class="data-table" style="margin:0 22px;min-width:900px">
                                <thead>
                                    <tr>
                                        <th style="width:32px"><label class="check"><input type="checkbox"><span
                                                    class="box"></span></label></th>
                                        <th class="sorted-asc">Nama <span class="sort"><svg viewBox="0 0 24 24">
                                                    <path d="m6 9 6 6 6-6" />
                                                </svg></span></th>
                                        <th>Mesin<span class="sort"><svg viewBox="0 0 24 24">
                                                    <path d="m6 9 6 6 6-6" />
                                                </svg></span></th>
                                        <th>Kategori</th>
                                        <th>Unit</th>
                                        <th>Source</th>
                                        <th>Cost Rate</th>
                                        <th>Harga Market</th>
                                        <th>Status</th>
                                        <th class="action-column">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="data-row">
                                        <td><label class="check"><input type="checkbox"><span
                                                    class="box"></span></label></td>
                                        <td>
                                            <div class="data-cell-user-name">Laminasi Glossy A3+</div>
                                        </td>
                                        <td>Digital A3+</td>
                                        <td><span class="data-cell-mono">Finishing Cetak</span></td>
                                        <td><span class="data-cell-mono">Lembar</span></td>
                                        <td><span class="data-cell-mono">Internal</span></td>
                                        <td><span class="data-cell-mono">Rp 817</span></td>
                                        <td><span class="data-cell-mono">-</span></td>
                                        <td><span class="badge success dot">Active</span></td>
                                        <td class="action-column">
                                            <div class="data-cell-actions"><button class="btn--icon" aria-label="View"><svg
                                                        viewBox="0 0 24 24">
                                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                                        <circle cx="12" cy="12" r="3" />
                                                    </svg></button> <a href="{{ route('admin_edit_finishing_service') }}"
                                                    class="btn--icon" aria-label="Edit"><svg viewBox="0 0 24 24">
                                                        <path d="M12 20h9" />
                                                        <path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4z" />
                                                    </svg></a> <button class="btn--icon" aria-label="More"><svg
                                                        viewBox="0 0 24 24">
                                                        <circle cx="12" cy="5" r="1" />
                                                        <circle cx="12" cy="12" r="1" />
                                                        <circle cx="12" cy="19" r="1" />
                                                    </svg></button></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="data-foot">
                            <div class="data-foot-info"><span>Showing <strong style="color:var(--t-base)">1–15</strong> of
                                    <strong style="color:var(--t-base)">142</strong></span> <select class="select">
                                    <option>15 per page</option>
                                    <option>25 per page</option>
                                    <option>50 per page</option>
                                    <option>100 per page</option>
                                </select></div>
                            <div class="pager"><button class="pager-btn" disabled="disabled" aria-label="Previous"><svg
                                        viewBox="0 0 24 24">
                                        <path d="m15 18-6-6 6-6" />
                                    </svg></button> <button class="pager-btn is-active">1</button> <button
                                    class="pager-btn">2</button> <button class="pager-btn">3</button> <button
                                    class="pager-btn">…</button> <button class="pager-btn">10</button> <button
                                    class="pager-btn" aria-label="Next"><svg viewBox="0 0 24 24">
                                        <path d="m9 18 6-6-6-6" />
                                    </svg></button></div>
                        </div>
                    </section>
                </div>
            </main>
            <div data-shell-footer></div>
        </div>
    </div>

    <!-- Modal Import FinishingService -->
    <div class="modal-overlay" id="modalImportFinishingService">

        <div class="modal modal-import">

            <!-- Header -->
            <div class="modal-header">

                <div>
                    <span class="eyebrow">IMPORT DATA</span>
                    <h2 class="modal-title">Import Finishing Service</h2>
                </div>

                <button type="button" class="modal-close" id="btnCloseImportFinishingService" aria-label="Tutup">

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
                        Pilih file Excel yang berisi data mesin untuk diimport
                        ke dalam sistem.
                    </p>
                </div>


                <!-- Custom File Input -->
                <label for="fileImportFinishingService" class="file-upload-box" id="fileUploadBox">

                    <input type="file" id="fileImportFinishingService" name="file" accept=".xlsx,.xls" hidden>


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

                <button type="button" class="btn btn--ghost" id="btnCancelImportFinishingService">

                    Batal

                </button>


                <button type="button" class="btn btn--primary" id="btnSaveImportFinishingService">

                    <svg viewBox="0 0 24 24">
                        <path d="M12 16V3" />
                        <path d="m7 8 5-5 5 5" />
                    </svg>

                    Import Data

                </button>

            </div>

        </div>

    </div>
@endsection


@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const btnImport = document.getElementById('btnImportFinishingService');

            const modal = document.getElementById('modalImportFinishingService');

            const btnClose = document.getElementById('btnCloseImportFinishingService');

            const btnCancel = document.getElementById('btnCancelImportFinishingService');

            const fileInput = document.getElementById('fileImportFinishingService');

            const fileName = document.getElementById('fileSelectedName');


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

        });
    </script>
@endsection

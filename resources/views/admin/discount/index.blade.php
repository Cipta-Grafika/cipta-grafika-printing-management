@extends('admin_master')
@section('contents')
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text"><span class="eyebrow">KONFIGURASI · DISKON</span>
                        <p class="hero-sub">Kelola diskon dan promo berdasarkan periode berlaku serta cakupan mesin dan
                            kategori produk.</p>
                    </div>

                    <div class="hero-actions" style="display: flex; justify-content: right;">
                        <button type="button" class="btn btn--primary" id="btnTambahDiskon">
                            <svg viewBox="0 0 24 24">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                            Tambah Diskon
                        </button>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card">
                        <table id="discountTable" class="display data-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>No</th>
                                    <th>Nama Diskon</th>
                                    <th>Diskon</th>
                                    <th>Periode</th>
                                    <th>Cakupan</th>
                                    <th>Status</th>
                                    <th>Dibuat Oleh</th>
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

    @php
        $reopenCreateDiscount = $errors->any() && old('form_context') === 'create_discount';
        $reopenEditDiscount =
            $errors->any() && old('form_context') === 'edit_discount' && ctype_digit((string) old('discount_id'));
    @endphp

    {{-- Modal Tambah Diskon --}}
    <div class="modal-overlay" id="modalTambahDiskon" data-reopen="{{ $reopenCreateDiscount ? 'true' : 'false' }}">
        <div class="modal-dialog modal-dialog--wide discount-modal">
            <form method="POST" action="{{ route('admin_store_discount') }}" id="formTambahDiskon" novalidate>
                @csrf
                <input type="hidden" name="form_context" value="create_discount">

                <div class="modal-header">
                    <div>
                        <span class="eyebrow">KONFIGURASI · DISKON</span>
                        <br>
                        <span class="eyebrow">Tambah Diskon</span>
                    </div>
                    <button type="button" class="modal-close" id="btnTutupModalDiskon" data-modal-close aria-label="Tutup">
                        <svg viewBox="0 0 24 24">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <div class="modal-body">
                    @include('admin.discount._form-fields', [
                        'prefix' => 'discount',
                        'reopen' => $reopenCreateDiscount,
                    ])
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn--ghost" id="btnBatalDiskon" data-modal-close>Batal</button>
                    <button type="submit" class="btn btn--primary" id="btnSimpanDiskon" data-submit>
                        <svg viewBox="0 0 24 24">
                            <path d="M5 12l4 4L19 6" />
                        </svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit Diskon --}}
    <div class="modal-overlay" id="modalEditDiskon" data-reopen="{{ $reopenEditDiscount ? 'true' : 'false' }}">
        <div class="modal-dialog modal-dialog--wide discount-modal">
            <form method="POST"
                action="{{ $reopenEditDiscount ? route('admin_update_discount', old('discount_id')) : '' }}"
                id="formEditDiskon" data-update-url="{{ route('admin_update_discount', ':id') }}"
                data-original-ends-at="{{ $reopenEditDiscount ? old('original_ends_at') : '' }}" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="form_context" value="edit_discount">
                <input type="hidden" name="discount_id" value="{{ $reopenEditDiscount ? old('discount_id') : '' }}">
                <input type="hidden" name="original_ends_at"
                    value="{{ $reopenEditDiscount ? old('original_ends_at') : '' }}">

                <div class="modal-header">
                    <div>
                        <span class="eyebrow">KONFIGURASI · DISKON</span>
                        <br>
                        <span class="eyebrow">Edit Diskon</span>
                    </div>
                    <button type="button" class="modal-close" data-modal-close aria-label="Tutup">
                        <svg viewBox="0 0 24 24">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <div class="modal-body">
                    @include('admin.discount._form-fields', [
                        'prefix' => 'editDiscount',
                        'reopen' => $reopenEditDiscount,
                    ])
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn--ghost" data-modal-close>Batal</button>
                    <button type="submit" class="btn btn--primary" data-submit>
                        <svg viewBox="0 0 24 24">
                            <path d="M5 12l4 4L19 6" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="modalDeleteDiskon">
        <div class="modal-dialog modal-delete">
            <div class="modal-header">
                <div>
                    <span class="eyebrow">KONFIGURASI · DISKON</span>
                    <br>
                    <span class="eyebrow">Hapus Diskon</span>
                </div>
                <button type="button" class="modal-close" data-delete-close aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
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
                        <h3>Hapus diskon?</h3>
                        <p>
                            Apakah kamu yakin ingin menghapus diskon <strong data-delete-name></strong>
                            beserta seluruh cakupan mesin dan kategorinya?
                            Data yang sudah dihapus tidak dapat dikembalikan.
                        </p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn--ghost" data-delete-close>Batal</button>
                <button type="button" class="btn btn--danger" id="btnConfirmDeleteDiskon">
                    <i class="bi bi-trash3-fill"></i>
                    Hapus
                </button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let discountTable;

        document.addEventListener('DOMContentLoaded', function() {
            const escapeHtml = function(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            };

            const parseDate = function(value) {
                const match = String(value || '').match(/^(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2})/);

                if (!match) {
                    return null;
                }

                return new Date(
                    Number(match[1]),
                    Number(match[2]) - 1,
                    Number(match[3]),
                    Number(match[4]),
                    Number(match[5])
                );
            };

            const formatDate = function(value, withTime = false) {
                const date = parseDate(value);

                if (!date) {
                    return '-';
                }

                const options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                };

                if (withTime) {
                    options.hour = '2-digit';
                    options.minute = '2-digit';
                }

                return date.toLocaleString('id-ID', options);
            };

            const formatPercentage = function(value) {
                const number = Number(value);

                if (!Number.isFinite(number)) {
                    return '-';
                }

                return number.toLocaleString('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 2
                }) + '%';
            };

            const detailUrlTemplate = "{{ route('admin_discount_details', ':id') }}";

            const periodStates = {
                upcoming: {
                    label: 'Terjadwal',
                    className: 'info'
                },
                running: {
                    label: 'Berlangsung',
                    className: 'success'
                },
                expired: {
                    label: 'Berakhir',
                    className: 'warning'
                }
            };

            discountTable = new DataTable('#discountTable', {
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
                    url: "{{ route('admin_data_discounts') }}",
                    type: 'GET'
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
                            return discountTable.page.info().start + meta.row + 1;
                        }
                    },
                    {
                        data: 'name',
                        responsivePriority: 1,
                        render: function(data) {
                            return `<div class="discount-name">${escapeHtml(data)}</div>`;
                        },
                        createdCell: function(td) {
                            td.style.whiteSpace = 'normal';
                            td.style.wordBreak = 'break-word';
                            td.style.overflowWrap = 'break-word';
                        }
                    },
                    {
                        data: 'discount_percentage',
                        responsivePriority: 1,
                        className: 'text-center',
                        render: function(data) {
                            return `<span class="discount-percentage">${formatPercentage(data)}</span>`;
                        }
                    },
                    {
                        data: 'starts_at',
                        responsivePriority: 2,
                        className: 'text-center',
                        render: function(data, type, row) {
                            const state = periodStates[row.period_state] || periodStates.running;

                            return `
                                <div class="discount-period">
                                    <span>${formatDate(row.starts_at, true)}</span>
                                    <span class="discount-period-separator">s/d</span>
                                    <span>${formatDate(row.ends_at, true)}</span>
                                </div>
                                <span class="badge ${state.className}" style="font-weight: 400; margin-top: 6px;">
                                    ${state.label}
                                </span>
                            `;
                        }
                    },
                    {
                        data: 'engines',
                        responsivePriority: 3,
                        className: 'text-center',
                        render: function(data) {
                            if (!Array.isArray(data) || data.length === 0) {
                                return '<span class="discount-scope-empty">Belum ada cakupan</span>';
                            }

                            const engines = data.map(function(engine) {
                                const categories = engine.categories || [];

                                return `
                                    <span class="discount-scope-chip" title="${categories.map(escapeHtml).join(', ')}">
                                        ${escapeHtml(engine.name)}
                                        <small>${categories.length} kategori</small>
                                    </span>
                                `;
                            }).join('');

                            return `<div class="discount-scope-list">${engines}</div>`;
                        },
                        createdCell: function(td) {
                            td.style.whiteSpace = 'normal';
                        }
                    },
                    {
                        data: 'status',
                        responsivePriority: 1,
                        className: 'text-center',
                        render: function(data) {
                            const statusClass = data === 'Active' ? 'success' : 'danger';

                            return `
                                <span class="badge ${statusClass} dot" style="font-weight: 400;">
                                    ${escapeHtml(data)}
                                </span>
                            `;
                        }
                    },
                    {
                        data: 'created_by',
                        responsivePriority: 100,
                        className: 'text-center',
                        render: function(data) {
                            return escapeHtml(data || '-');
                        }
                    },
                    {
                        data: 'id',
                        orderable: false,
                        searchable: false,
                        responsivePriority: 1,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                                <div class="discount-actions">
                                    <button type="button" class="btn--icon btn-view-discount"
                                        data-url="${detailUrlTemplate.replace(':id', encodeURIComponent(data))}"
                                        aria-label="Detail" title="Detail">
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </button>
                                    <button type="button" class="btn--icon btn-edit-discount"
                                        data-id="${escapeHtml(data)}" aria-label="Edit" title="Edit">
                                        <i class="bi bi-pen"></i>
                                    </button>
                                    <button type="button" class="btn--icon btn-delete-discount"
                                        data-id="${escapeHtml(data)}" data-name="${escapeHtml(row.name)}"
                                        aria-label="Hapus" title="Hapus">
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
                    zeroRecords: 'Data diskon tidak ditemukan',
                    emptyTable: 'Belum ada data diskon',
                    processing: 'Memuat data...',
                    searchPlaceholder: 'Cari diskon...'
                },
                initComplete: function() {
                    $(this).closest('.dt-container, .dataTables_wrapper')
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
            const pad = function(value) {
                return String(value).padStart(2, '0');
            };

            const nowInputValue = function() {
                const now = new Date();

                return `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
            };

            const setupDiscountForm = function(modal) {
                const form = modal?.querySelector('form');

                if (!form) {
                    return null;
                }

                const btnSave = form.querySelector('[data-submit]');
                const scopePicker = form.querySelector('[data-scope-picker]');
                const scopeTotal = form.querySelector('[data-scope-total]');
                const legacy = form.querySelector('[data-scope-legacy]');
                const legacyText = form.querySelector('[data-scope-legacy-text]');
                const legacyInputs = form.querySelector('[data-scope-legacy-inputs]');
                const statusSelect = form.querySelector('[data-discount-status]');

                const fields = {
                    code: form.querySelector('[name="code"]'),
                    name: form.querySelector('[name="name"]'),
                    discount_percentage: form.querySelector('[name="discount_percentage"]'),
                    starts_at: form.querySelector('[name="starts_at"]'),
                    ends_at: form.querySelector('[name="ends_at"]'),
                    description: form.querySelector('[name="description"]')
                };

                $(statusSelect).select2({
                    width: '100%',
                    minimumResultsForSearch: 0,
                    dropdownParent: $(modal)
                });

                const setError = function(field, message) {
                    const error = form.querySelector(`[data-error-for="${field}"]`);

                    if (error) {
                        error.textContent = message || '';
                    }

                    if (field === 'scopes') {
                        scopePicker?.classList.toggle('is-invalid', Boolean(message));
                        return;
                    }

                    fields[field]?.classList.toggle('is-invalid', Boolean(message));
                };

                const clearErrors = function() {
                    form.querySelectorAll('[data-error-for]').forEach(function(error) {
                        setError(error.dataset.errorFor, '');
                    });
                };

                const legacyCount = function() {
                    return legacyInputs ? legacyInputs.querySelectorAll('input').length : 0;
                };

                const updateScopeState = function() {
                    let total = legacyCount();

                    scopePicker?.querySelectorAll('[data-scope-group]').forEach(function(group) {
                        const items = Array.from(group.querySelectorAll('[data-scope-item]'));
                        const checked = items.filter(item => item.checked).length;
                        const all = group.querySelector('[data-scope-all]');

                        total += checked;
                        all.checked = items.length > 0 && checked === items.length;
                        all.indeterminate = checked > 0 && checked < items.length;
                        group.classList.toggle('has-selection', checked > 0);
                        group.querySelector('[data-scope-count]').textContent =
                            `${checked}/${items.length}`;
                    });

                    scopeTotal.textContent = `${total} dipilih`;
                    scopeTotal.classList.toggle('is-active', total > 0);
                };

                const setLegacyScopes = function(scopes) {
                    if (!legacy || !legacyInputs) {
                        return;
                    }

                    legacyInputs.innerHTML = '';

                    scopes.forEach(function(scope) {
                        const input = document.createElement('input');

                        input.type = 'hidden';
                        input.name = 'scopes[]';
                        input.value = scope.value;
                        legacyInputs.appendChild(input);
                    });

                    legacy.hidden = scopes.length === 0;
                    legacyText.textContent = scopes.length === 0 ? '' :
                        `${scopes.length} cakupan lama dari mesin yang sudah tidak aktif tetap dipertahankan: ` +
                        scopes.map(scope => scope.label).join(', ') + '.';
                };

                scopePicker?.addEventListener('change', function(event) {
                    if (event.target.matches('[data-scope-all]')) {
                        event.target.closest('[data-scope-group]')
                            .querySelectorAll('[data-scope-item]')
                            .forEach(item => item.checked = event.target.checked);
                    }

                    updateScopeState();
                    setError('scopes', '');
                });

                Object.entries(fields).forEach(function([name, field]) {
                    field?.addEventListener('input', function() {
                        setError(name, '');
                    });
                });

                fields.discount_percentage.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9.,]/g, '');
                });

                fields.code.addEventListener('input', function() {
                    const start = this.selectionStart;
                    const end = this.selectionEnd;
                    this.value = this.value.toUpperCase();
                    this.setSelectionRange(start, end);
                });

                fields.starts_at.addEventListener('change', function() {
                    if (this.value) {
                        fields.ends_at.min = this.value;
                    }
                });

                const validate = function() {
                    const errors = {};
                    const code = fields.code.value.trim().toUpperCase();
                    const name = fields.name.value.trim();
                    const percentageText = fields.discount_percentage.value.trim();
                    const percentage = Number(percentageText.replace(',', '.'));
                    const startsAt = fields.starts_at.value;
                    const endsAt = fields.ends_at.value;
                    const endsAtUnchanged = Boolean(form.dataset.originalEndsAt) &&
                        endsAt === form.dataset.originalEndsAt;

                    if (!code) {
                        errors.code = 'Kode diskon wajib diisi.';
                    } else if (code.length < 3) {
                        errors.code = 'Kode diskon minimal 3 karakter.';
                    } else if (code.length > 30) {
                        errors.code = 'Kode diskon maksimal 30 karakter.';
                    } else if (!/^[A-Z0-9_-]+$/.test(code)) {
                        errors.code =
                            'Kode diskon hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_), tanpa spasi.';
                    }

                    if (!name) {
                        errors.name = 'Nama diskon wajib diisi.';
                    } else if (name.length > 255) {
                        errors.name = 'Nama diskon maksimal 255 karakter.';
                    }

                    if (!percentageText) {
                        errors.discount_percentage = 'Persentase diskon wajib diisi.';
                    } else if (!/^\d{1,3}([.,]\d{1,2})?$/.test(percentageText)) {
                        errors.discount_percentage =
                            'Persentase diskon harus berupa angka dengan maksimal 2 angka desimal.';
                    } else if (percentage <= 0) {
                        errors.discount_percentage = 'Persentase diskon harus lebih dari 0%.';
                    } else if (percentage > 100) {
                        errors.discount_percentage = 'Persentase diskon maksimal 100%.';
                    }

                    if (!startsAt) {
                        errors.starts_at = 'Tanggal mulai wajib diisi.';
                    }

                    if (!endsAt) {
                        errors.ends_at = 'Tanggal selesai wajib diisi.';
                    } else if (startsAt && endsAt <= startsAt) {
                        errors.ends_at = 'Tanggal selesai harus setelah tanggal mulai.';
                    } else if (!endsAtUnchanged && endsAt < nowInputValue()) {
                        errors.ends_at = 'Tanggal selesai sudah lewat. Periode diskon harus masih berlaku.';
                    }

                    if (fields.description.value.trim().length > 1000) {
                        errors.description = 'Deskripsi maksimal 1.000 karakter.';
                    }

                    if (!form.querySelector('[data-scope-item]:checked') && legacyCount() === 0) {
                        errors.scopes = 'Pilih minimal satu kategori pada cakupan diskon.';
                    }

                    return errors;
                };

                form.addEventListener('submit', function(event) {
                    clearErrors();

                    const errors = validate();
                    const invalidFields = Object.keys(errors);

                    if (invalidFields.length > 0) {
                        event.preventDefault();

                        invalidFields.forEach(field => setError(field, errors[field]));

                        const first = invalidFields[0];

                        if (first === 'scopes') {
                            scopePicker?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        } else {
                            fields[first]?.focus();
                        }

                        return;
                    }

                    btnSave.disabled = true;
                    fields.code.value = fields.code.value.trim().toUpperCase();
                    btnSave.classList.add('is-loading');
                });

                const open = function() {
                    modal.classList.add('is-open');
                    setTimeout(() => fields.code.focus(), 150);
                };

                const close = function() {
                    modal.classList.remove('is-open');
                };

                const fill = function(values) {
                    clearErrors();

                    fields.code.value = values.code ?? '';
                    fields.name.value = values.name ?? '';
                    fields.discount_percentage.value = values.discount_percentage ?? '';
                    fields.starts_at.value = values.starts_at ?? '';
                    fields.ends_at.value = values.ends_at ?? '';
                    fields.ends_at.min = fields.starts_at.value;
                    fields.description.value = values.description ?? '';
                    $(statusSelect).val(values.status || 'Active').trigger('change');

                    const scopes = Array.isArray(values.scopes) ? values.scopes : [];
                    const available = scopes.filter(scope => scope.available).map(scope => scope.value);

                    form.querySelectorAll('[data-scope-item]').forEach(function(item) {
                        item.checked = available.includes(item.value);
                    });

                    setLegacyScopes(scopes.filter(scope => !scope.available));
                    updateScopeState();
                };

                modal.querySelectorAll('[data-modal-close]').forEach(function(button) {
                    button.addEventListener('click', close);
                });

                modal.addEventListener('click', function(event) {
                    if (event.target === modal) {
                        close();
                    }
                });

                document.addEventListener('keydown', function(event) {
                    if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                        close();
                    }
                });

                if (fields.starts_at.value) {
                    fields.ends_at.min = fields.starts_at.value;
                }

                updateScopeState();

                return {
                    form,
                    open,
                    close,
                    fill,
                    shouldReopen: modal.dataset.reopen === 'true'
                };
            };

            const createModal = setupDiscountForm(document.getElementById('modalTambahDiskon'));
            const editModal = setupDiscountForm(document.getElementById('modalEditDiskon'));

            document.getElementById('btnTambahDiskon')?.addEventListener('click', function(event) {
                event.preventDefault();
                createModal?.open();
            });

            $(document).on('click', '.btn-view-discount', function(event) {
                event.preventDefault();
                if (this.dataset.url) {
                    window.location.href = this.dataset.url;
                }
            });

            if (editModal) {
                const editForm = editModal.form;
                const editUrlTemplate = "{{ route('admin_edit_discount', ':id') }}";
                let loadingEdit = false;

                $(document).on('click', '.btn-edit-discount', function(event) {
                    event.preventDefault();

                    const id = this.dataset.id;

                    if (!id || loadingEdit) {
                        return;
                    }

                    loadingEdit = true;
                    this.disabled = true;

                    const button = this;

                    fetch(editUrlTemplate.replace(':id', encodeURIComponent(id)), {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(function(response) {
                            if (!response.ok) {
                                throw new Error('Data diskon gagal dimuat.');
                            }

                            return response.json();
                        })
                        .then(function(discount) {
                            editForm.action = editForm.dataset.updateUrl.replace(':id', discount.id);
                            editForm.querySelector('[name="discount_id"]').value = discount.id;
                            editForm.querySelector('[name="original_ends_at"]').value = discount
                                .ends_at;
                            editForm.dataset.originalEndsAt = discount.ends_at;

                            editModal.fill(discount);
                            editModal.open();
                        })
                        .catch(function(error) {
                            console.error('Edit Diskon Error:', error);
                            showToast('error', 'Data diskon gagal dimuat.');
                        })
                        .finally(function() {
                            loadingEdit = false;
                            button.disabled = false;
                        });
                });
            }

            if (createModal?.shouldReopen) {
                createModal.open();
            } else if (editModal?.shouldReopen) {
                editModal.open();
            }

            const deleteModal = document.getElementById('modalDeleteDiskon');
            const btnConfirmDelete = document.getElementById('btnConfirmDeleteDiskon');
            const deleteUrlTemplate = "{{ route('admin_destroy_discount', ':id') }}";
            let discountIdToDelete = null;
            let deletingDiscount = false;

            const closeDeleteModal = function() {
                if (deletingDiscount) {
                    return;
                }

                deleteModal.classList.remove('is-open');
                discountIdToDelete = null;
            };

            $(document).on('click', '.btn-delete-discount', function(event) {
                event.preventDefault();

                discountIdToDelete = this.dataset.id;
                deleteModal.querySelector('[data-delete-name]').textContent = `"${this.dataset.name || ''}"`;
                deleteModal.classList.add('is-open');
            });

            deleteModal.querySelectorAll('[data-delete-close]').forEach(function(button) {
                button.addEventListener('click', closeDeleteModal);
            });

            deleteModal.addEventListener('click', function(event) {
                if (event.target === deleteModal) {
                    closeDeleteModal();
                }
            });

            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && deleteModal.classList.contains('is-open')) {
                    closeDeleteModal();
                }
            });

            btnConfirmDelete.addEventListener('click', async function() {
                if (!discountIdToDelete || deletingDiscount) {
                    return;
                }

                deletingDiscount = true;
                btnConfirmDelete.disabled = true;

                try {
                    const response = await fetch(deleteUrlTemplate.replace(':id', encodeURIComponent(discountIdToDelete)), {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const result = await response.json().catch(() => ({}));

                    deletingDiscount = false;

                    if (response.ok && result.success) {
                        closeDeleteModal();
                        discountTable.ajax.reload(null, false);
                        showToast('success', result.message);
                        return;
                    }

                    if (response.status === 404) {
                        closeDeleteModal();
                        discountTable.ajax.reload(null, false);
                    }

                    showToast('error', result.message || 'Diskon gagal dihapus.');
                } catch (error) {
                    console.error('Hapus Diskon Error:', error);
                    deletingDiscount = false;
                    showToast('error', 'Diskon gagal dihapus.');
                } finally {
                    btnConfirmDelete.disabled = false;
                }
            });
        });
    </script>
@endsection

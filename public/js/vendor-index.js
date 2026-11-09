let vendorTable;
const selectedVendorIds = new Set();

function escapeVendorHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[char]);
}

function updateVendorSelection() {
    const toolbar = document.getElementById('vendorBulkToolbar');
    const checkAll = document.getElementById('checkAllVendors');
    const rows = [...document.querySelectorAll('.vendor-row-check')];
    toolbar.style.display = selectedVendorIds.size ? 'flex' : 'none';
    document.getElementById('vendorBulkCount').textContent = `${selectedVendorIds.size} terpilih`;
    rows.forEach(input => {
        input.checked = selectedVendorIds.has(Number(input.value));
        input.closest('tr').classList.toggle('is-selected', input.checked);
    });
    const checked = rows.filter(input => input.checked).length;
    checkAll.checked = rows.length > 0 && checked === rows.length;
    checkAll.indeterminate = checked > 0 && checked < rows.length;
    checkAll.closest('label').classList.toggle('is-indeterminate', checkAll.indeterminate);
}

document.addEventListener('DOMContentLoaded', function () {
    const tableElement = document.getElementById('vendorTable');
    const search = document.getElementById('vendorSearch');
    const clearSearch = document.getElementById('clearVendorSearch');
    const creator = document.getElementById('vendorCreator');
    const period = document.getElementById('vendorPeriod');
    const length = document.getElementById('vendorPageLength');
    let searchTimer;

    vendorTable = new DataTable('#vendorTable', {
        processing: true,
        serverSide: true,
        ordering: true,
        order: [[2, 'asc']],
        stripeClasses: [],
        ajax: {
            url: tableElement.dataset.url,
            type: 'GET',
            data: function (data) {
                data.creator = creator.value;
                data.period = period.value;
            }
        },
        columns: [
            {
                data: 'id', orderable: false, searchable: false, className: 'text-center cat-td-check',
                render: function (id, type, row) {
                    return `<label class="cat-custom-checkbox"><input type="checkbox" class="vendor-row-check" value="${Number(id)}" aria-label="Pilih vendor ${escapeVendorHtml(row.name)}" ${selectedVendorIds.has(Number(id)) ? 'checked' : ''}><span class="cat-checkbox-mark"><svg class="check-icon" viewBox="0 0 14 14" fill="none"><path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#fff" stroke-width="2.2"/></svg></span></label>`;
                }
            },
            {
                data: null, orderable: false, searchable: false, className: 'text-center cat-td-no',
                render: (data, type, row, meta) => `<span class="cat-num-pill">${meta.settings._iDisplayStart + meta.row + 1}</span>`
            },
            {
                data: 'name', className: 'cat-td-name',
                render: data => `<div class="cat-cell-name-box"><div class="cat-icon-symbol"><i class="bi bi-building"></i></div><div class="cat-name-details"><span class="cat-name-primary">${escapeVendorHtml(data || '-')}</span><span class="cat-name-badge">Vendor Produksi</span></div></div>`
            },
            {
                data: 'contact',
                render: data => escapeVendorHtml(data || '-')
            },
            {
                data: 'status',
                render: data => `<span class="badge ${data === 'Active' ? 'success' : 'danger'} dot">${escapeVendorHtml(data)}</span>`
            },
            {
                data: 'created_by', className: 'cat-td-creator',
                render: function (data) {
                    const name = (data || '').trim() || 'Administrator';
                    const words = name.split(/\s+/);
                    const initials = words.length > 1 ? words[0][0] + words[1][0] : words[0].substring(0, 2);
                    const colors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#6366f1', '#14b8a6'];
                    let hash = 0;
                    for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
                    return `<div class="cat-user-badge"><span class="cat-user-avatar" style="background: ${colors[Math.abs(hash) % colors.length]};">${escapeVendorHtml(initials.toUpperCase())}</span><div class="cat-user-info"><span class="cat-user-name">${escapeVendorHtml(name)}</span><span class="cat-user-role">Administrator</span></div></div>`;
                }
            },
            {
                data: 'created_at', className: 'cat-td-date',
                render: function (data) {
                    if (!data) return '<span class="text-muted">-</span>';
                    const date = new Date(data);
                    const formatted = Number.isNaN(date.getTime()) ? '-' : date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                    return `<span class="cat-date-chip"><i class="bi bi-calendar3"></i><span>${formatted}</span></span>`;
                }
            },
            {
                data: 'id', orderable: false, searchable: false, className: 'text-center cat-td-actions',
                render: id => `<div class="cat-action-group"><button type="button" class="cat-btn-action cat-btn-edit btn-edit-vendor" data-id="${Number(id)}" title="Edit Vendor" aria-label="Edit vendor"><i class="bi bi-pen"></i></button><button type="button" class="cat-btn-action cat-btn-del btn-delete-vendor" data-id="${Number(id)}" title="Hapus Vendor" aria-label="Hapus vendor"><i class="bi bi-trash3-fill"></i></button></div>`
            }
        ],
        pageLength: 10,
        layout: { topStart: null, topEnd: null, bottomStart: 'info', bottomEnd: 'paging' },
        language: {
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ vendor',
            infoEmpty: 'Menampilkan 0 sampai 0 dari 0 vendor',
            zeroRecords: 'Data vendor tidak ditemukan',
            processing: 'Memuat data...'
        }
    });

    vendorTable.on('draw', function () {
        document.getElementById('vendorTotalCount').textContent = `${vendorTable.page.info().recordsTotal.toLocaleString('id-ID')} Total Vendor`;
        updateVendorSelection();
    });

    search.addEventListener('input', function () {
        clearSearch.style.display = this.value ? 'flex' : 'none';
        clearTimeout(searchTimer);
        selectedVendorIds.clear();
        updateVendorSelection();
        searchTimer = setTimeout(() => {
            vendorTable.search(search.value.trim()).draw();
        }, 250);
    });
    clearSearch.addEventListener('click', function () {
        clearTimeout(searchTimer);
        search.value = '';
        clearSearch.style.display = 'none';
        selectedVendorIds.clear();
        updateVendorSelection();
        vendorTable.search('').draw();
        search.focus();
    });

    if (typeof $.fn.select2 !== 'undefined') {
        $(creator).select2({ minimumResultsForSearch: 6, width: '100%' });
        $(period).select2({ minimumResultsForSearch: -1, width: '100%' });
        $(length).select2({ minimumResultsForSearch: -1, width: '76px' });
        $(creator).add(period).on('change', function () {
            selectedVendorIds.clear();
            updateVendorSelection();
            vendorTable.ajax.reload();
        }).on('select2:open', function () {
            $(this).closest('.cat-select-box').addClass('is-open');
        }).on('select2:close', function () {
            $(this).closest('.cat-select-box').removeClass('is-open');
        });
        $(length).on('change', function () { vendorTable.page.len(Number(this.value)).draw(); });
    } else {
        [creator, period].forEach(select => select.addEventListener('change', function () {
            selectedVendorIds.clear();
            updateVendorSelection();
            vendorTable.ajax.reload();
        }));
        length.addEventListener('change', function () { vendorTable.page.len(Number(this.value)).draw(); });
    }

    document.getElementById('resetVendorFilters').addEventListener('click', function () {
        clearTimeout(searchTimer);
        search.value = '';
        clearSearch.style.display = 'none';
        creator.value = 'all';
        period.value = 'all';
        length.value = '10';
        if (typeof $.fn.select2 !== 'undefined') $(creator).add(period).add(length).trigger('change.select2');
        selectedVendorIds.clear();
        updateVendorSelection();
        vendorTable.search('');
        vendorTable.page.len(10);
        vendorTable.ajax.reload();
    });

    document.getElementById('checkAllVendors').addEventListener('change', function () {
        document.querySelectorAll('.vendor-row-check').forEach(input => {
            if (this.checked) selectedVendorIds.add(Number(input.value));
            else selectedVendorIds.delete(Number(input.value));
        });
        updateVendorSelection();
    });
    tableElement.addEventListener('change', function (event) {
        if (!event.target.classList.contains('vendor-row-check')) return;
        const id = Number(event.target.value);
        if (event.target.checked) selectedVendorIds.add(id);
        else selectedVendorIds.delete(id);
        updateVendorSelection();
    });
    document.getElementById('cancelVendorSelection').addEventListener('click', function () {
        selectedVendorIds.clear();
        updateVendorSelection();
    });

    const modal = document.getElementById('modalBulkDeleteVendors');
    const confirmButton = document.getElementById('confirmBulkDeleteVendors');
    function closeModal() {
        if (!confirmButton.disabled) modal.classList.remove('is-open');
    }
    document.getElementById('bulkDeleteVendors').addEventListener('click', function () {
        if (!selectedVendorIds.size) return;
        document.getElementById('bulkDeleteVendorDescription').textContent = `Apakah Anda yakin ingin menghapus ${selectedVendorIds.size} vendor yang dipilih? Data yang sudah dihapus tidak dapat dikembalikan.`;
        modal.classList.add('is-open');
    });
    document.getElementById('closeBulkDeleteVendors').addEventListener('click', closeModal);
    document.getElementById('cancelBulkDeleteVendors').addEventListener('click', closeModal);
    modal.addEventListener('click', event => { if (event.target === modal) closeModal(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') closeModal(); });
    confirmButton.addEventListener('click', async function () {
        if (!selectedVendorIds.size) return;
        confirmButton.disabled = true;
        try {
            const response = await fetch(tableElement.dataset.bulkUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: Array.from(selectedVendorIds) })
            });
            const result = await response.json();
            if (!response.ok || !result.success) {
                showToast('error', result.message || 'Gagal menghapus vendor terpilih.');
                return;
            }
            selectedVendorIds.clear();
            modal.classList.remove('is-open');
            updateVendorSelection();
            vendorTable.ajax.reload(null, false);
            showToast('success', result.message);
        } catch (error) {
            console.error('Gagal menghapus vendor:', error);
            showToast('error', 'Terjadi kesalahan saat menghapus vendor.');
        } finally {
            confirmButton.disabled = false;
        }
    });
});

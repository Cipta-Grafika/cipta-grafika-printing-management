let cuttingTable;
const selectedCuttingIds = new Set();

function escapeCuttingHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[char]);
}

function updateCuttingSelection() {
    const rows = [...document.querySelectorAll('.cutting-row-check')];
    const checkAll = document.getElementById('checkAllCuttings');
    document.getElementById('cuttingBulkToolbar').style.display = selectedCuttingIds.size ? 'flex' : 'none';
    document.getElementById('cuttingBulkCount').textContent = `${selectedCuttingIds.size} terpilih`;
    rows.forEach(input => {
        input.checked = selectedCuttingIds.has(Number(input.value));
        input.closest('tr').classList.toggle('is-selected', input.checked);
    });
    const checked = rows.filter(input => input.checked).length;
    checkAll.checked = rows.length > 0 && checked === rows.length;
    checkAll.indeterminate = checked > 0 && checked < rows.length;
    checkAll.closest('label').classList.toggle('is-indeterminate', checkAll.indeterminate);
}

document.addEventListener('DOMContentLoaded', function () {
    const tableElement = document.getElementById('cuttingTable');
    const search = document.getElementById('cuttingSearch');
    const clearSearch = document.getElementById('clearCuttingSearch');
    const creator = document.getElementById('cuttingCreator');
    const period = document.getElementById('cuttingPeriod');
    const length = document.getElementById('cuttingPageLength');
    let searchTimer;

    cuttingTable = new DataTable('#cuttingTable', {
        processing: true, serverSide: true, ordering: true, order: [], stripeClasses: [],
        ajax: {
            url: tableElement.dataset.url,
            type: 'GET',
            data: data => { data.creator = creator.value; data.period = period.value; }
        },
        columns: [
            {
                data: 'id', orderable: false, searchable: false, className: 'text-center cat-td-check',
                render: (id, type, row) => `<label class="cat-custom-checkbox"><input type="checkbox" class="cutting-row-check" value="${Number(id)}" aria-label="Pilih jasa potong ${escapeCuttingHtml(row.name)}"><span class="cat-checkbox-mark"><svg class="check-icon" viewBox="0 0 14 14" fill="none"><path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#fff" stroke-width="2.2"/></svg></span></label>`
            },
            { data: null, orderable: false, searchable: false, className: 'text-center cat-td-no', render: (data, type, row, meta) => `<span class="cat-num-pill">${meta.settings._iDisplayStart + meta.row + 1}</span>` },
            { data: 'name', className: 'cat-td-name', render: data => `<div class="cat-cell-name-box"><div class="cat-name-details"><span class="cat-name-primary">${escapeCuttingHtml(data || '-')}</span><span class="cat-name-badge">Jasa Potong Produksi</span></div></div>` },
            { data: 'total_sizes', className: 'text-center', render: data => Number(data) || 0 },
            { data: 'status', render: data => `<span class="badge ${data === 'Active' ? 'success' : 'danger'} dot">${escapeCuttingHtml(data)}</span>` },
            {
                data: 'created_by', className: 'cat-td-creator',
                render: function (data) {
                    const name = (data || '').trim() || 'Administrator';
                    return `<div class="cat-user-badge"><div class="cat-user-info"><span class="cat-user-name">${escapeCuttingHtml(name)}</span><span class="cat-user-role">Administrator</span></div></div>`;
                }
            },
            {
                data: 'created_at', className: 'cat-td-date',
                render: function (data) {
                    if (!data) return '<span class="text-muted">-</span>';
                    const date = new Date(data);
                    const formatted = Number.isNaN(date.getTime()) ? '-' : date.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                    return `<span class="cat-date-chip"><span>${formatted}</span></span>`;
                }
            },
            {
                data: 'id', orderable: false, searchable: false, className: 'text-center cat-td-actions',
                render: id => `<div class="cat-action-group"><button type="button" class="cat-btn-action cat-btn-edit btn-view-cutting" data-id="${Number(id)}" title="Detail Jasa Potong" aria-label="Detail jasa potong"><i class="bi bi-arrows-fullscreen"></i></button><button type="button" class="cat-btn-action cat-btn-edit btn-edit-cutting" data-id="${Number(id)}" title="Edit Jasa Potong" aria-label="Edit jasa potong"><i class="bi bi-pen"></i></button><button type="button" class="cat-btn-action cat-btn-del btn-delete-cutting" data-id="${Number(id)}" title="Hapus Jasa Potong" aria-label="Hapus jasa potong"><i class="bi bi-trash3-fill"></i></button></div>`
            }
        ],
        pageLength: 10,
        layout: { topStart: null, topEnd: null, bottomStart: 'info', bottomEnd: 'paging' },
        language: { info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ jasa potong', infoEmpty: 'Menampilkan 0 sampai 0 dari 0 jasa potong', zeroRecords: 'Data jasa potong tidak ditemukan', processing: 'Memuat data...' }
    });
    cuttingTable.on('draw', function () {
        document.getElementById('cuttingTotalCount').textContent = `${cuttingTable.page.info().recordsTotal.toLocaleString('id-ID')} Total Jasa Potong`;
        updateCuttingSelection();
    });
    function clearSelection() {
        selectedCuttingIds.clear();
        updateCuttingSelection();
    }
    search.addEventListener('input', function () {
        clearSearch.style.display = this.value ? 'flex' : 'none';
        clearTimeout(searchTimer);
        clearSelection();
        searchTimer = setTimeout(() => cuttingTable.search(search.value.trim()).draw(), 250);
    });
    clearSearch.addEventListener('click', function () {
        clearTimeout(searchTimer);
        search.value = '';
        clearSearch.style.display = 'none';
        clearSelection();
        cuttingTable.search('').draw();
        search.focus();
    });
    function filterChanged() {
        clearSelection();
        cuttingTable.ajax.reload();
    }
    if (typeof $.fn.select2 !== 'undefined') {
        $(creator).select2({ minimumResultsForSearch: 6, width: '100%' });
        $(period).select2({ minimumResultsForSearch: -1, width: '100%' });
        $(length).select2({ minimumResultsForSearch: -1, width: '76px' });
        $(creator).add(period).on('change', filterChanged)
            .on('select2:open', function () { $(this).closest('.cat-select-box').addClass('is-open'); })
            .on('select2:close', function () { $(this).closest('.cat-select-box').removeClass('is-open'); });
        $(length).on('change', function () { cuttingTable.page.len(Number(this.value)).draw(); });
    } else {
        [creator, period].forEach(select => select.addEventListener('change', filterChanged));
        length.addEventListener('change', function () { cuttingTable.page.len(Number(this.value)).draw(); });
    }
    document.getElementById('resetCuttingFilters').addEventListener('click', function () {
        clearTimeout(searchTimer);
        search.value = '';
        clearSearch.style.display = 'none';
        creator.value = 'all';
        period.value = 'all';
        length.value = '10';
        if (typeof $.fn.select2 !== 'undefined') $(creator).add(period).add(length).trigger('change.select2');
        clearSelection();
        cuttingTable.search('');
        cuttingTable.page.len(10);
        cuttingTable.ajax.reload();
    });
    document.getElementById('checkAllCuttings').addEventListener('change', function () {
        document.querySelectorAll('.cutting-row-check').forEach(input => {
            if (this.checked) selectedCuttingIds.add(Number(input.value));
            else selectedCuttingIds.delete(Number(input.value));
        });
        updateCuttingSelection();
    });
    tableElement.addEventListener('change', function (event) {
        if (!event.target.classList.contains('cutting-row-check')) return;
        const id = Number(event.target.value);
        if (event.target.checked) selectedCuttingIds.add(id);
        else selectedCuttingIds.delete(id);
        updateCuttingSelection();
    });
    document.getElementById('cancelCuttingSelection').addEventListener('click', clearSelection);

    const modal = document.getElementById('modalBulkDeleteCuttings');
    const confirmButton = document.getElementById('confirmBulkDeleteCuttings');
    function closeModal() { if (!confirmButton.disabled) modal.classList.remove('is-open'); }
    document.getElementById('bulkDeleteCuttings').addEventListener('click', function () {
        if (!selectedCuttingIds.size) return;
        document.getElementById('bulkDeleteCuttingDescription').textContent = `Apakah Anda yakin ingin menghapus ${selectedCuttingIds.size} jasa potong yang dipilih? Data yang sudah dihapus tidak dapat dikembalikan.`;
        modal.classList.add('is-open');
    });
    document.getElementById('closeBulkDeleteCuttings').addEventListener('click', closeModal);
    document.getElementById('cancelBulkDeleteCuttings').addEventListener('click', closeModal);
    modal.addEventListener('click', event => { if (event.target === modal) closeModal(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') closeModal(); });
    confirmButton.addEventListener('click', async function () {
        if (!selectedCuttingIds.size) return;
        confirmButton.disabled = true;
        try {
            const response = await fetch(tableElement.dataset.bulkUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ ids: Array.from(selectedCuttingIds) })
            });
            const result = await response.json();
            if (!response.ok || !result.success) {
                showToast('error', result.message || 'Gagal menghapus jasa potong terpilih.');
                return;
            }
            clearSelection();
            modal.classList.remove('is-open');
            cuttingTable.ajax.reload(null, false);
            showToast('success', result.message);
        } catch (error) {
            console.error('Gagal menghapus jasa potong:', error);
            showToast('error', 'Terjadi kesalahan saat menghapus jasa potong.');
        } finally {
            confirmButton.disabled = false;
        }
    });
});

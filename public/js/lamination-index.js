let laminationTable;
const selectedLaminationIds = new Set();

function escapeLaminationHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[char]);
}

function updateLaminationSelection() {
    const rows = [...document.querySelectorAll('.lamination-row-check')];
    const checkAll = document.getElementById('checkAllLaminations');
    document.getElementById('laminationBulkToolbar').style.display = selectedLaminationIds.size ? 'flex' : 'none';
    document.getElementById('laminationBulkCount').textContent = `${selectedLaminationIds.size} terpilih`;
    rows.forEach(input => {
        input.checked = selectedLaminationIds.has(Number(input.value));
        input.closest('tr').classList.toggle('is-selected', input.checked);
    });
    const checked = rows.filter(input => input.checked).length;
    checkAll.checked = rows.length > 0 && checked === rows.length;
    checkAll.indeterminate = checked > 0 && checked < rows.length;
    checkAll.closest('label').classList.toggle('is-indeterminate', checkAll.indeterminate);
}

document.addEventListener('DOMContentLoaded', function () {
    const tableElement = document.getElementById('laminationTable');
    const search = document.getElementById('laminationSearch');
    const clearSearch = document.getElementById('clearLaminationSearch');
    const creator = document.getElementById('laminationCreator');
    const period = document.getElementById('laminationPeriod');
    const length = document.getElementById('laminationPageLength');
    let searchTimer;

    laminationTable = new DataTable('#laminationTable', {
        processing: true, serverSide: true, ordering: true, order: [], stripeClasses: [],
        ajax: {
            url: tableElement.dataset.url,
            type: 'GET',
            data: data => { data.creator = creator.value; data.period = period.value; }
        },
        columns: [
            {
                data: 'id', orderable: false, searchable: false, className: 'text-center cat-td-check',
                render: (id, type, row) => `<label class="cat-custom-checkbox"><input type="checkbox" class="lamination-row-check" value="${Number(id)}" aria-label="Pilih laminasi ${escapeLaminationHtml(row.name)}"><span class="cat-checkbox-mark"><svg class="check-icon" viewBox="0 0 14 14" fill="none"><path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#fff" stroke-width="2.2"/></svg></span></label>`
            },
            { data: null, orderable: false, searchable: false, className: 'text-center cat-td-no', render: (data, type, row, meta) => `<span class="cat-num-pill">${meta.settings._iDisplayStart + meta.row + 1}</span>` },
            { data: 'lamination_code', className: 'text-center', render: data => escapeLaminationHtml(data || '-') },
            { data: 'name', className: 'cat-td-name', render: data => `<div class="cat-cell-name-box"><div class="cat-icon-symbol"><i class="bi bi-layers-fill"></i></div><div class="cat-name-details"><span class="cat-name-primary">${escapeLaminationHtml(data || '-')}</span><span class="cat-name-badge">Laminasi Produksi</span></div></div>` },
            { data: 'total_sizes', className: 'text-center', render: data => Number(data) || 0 },
            { data: 'status', render: data => `<span class="badge ${data === 'Active' ? 'success' : 'danger'} dot">${escapeLaminationHtml(data)}</span>` },
            {
                data: 'created_by', className: 'cat-td-creator',
                render: function (data) {
                    const name = (data || '').trim() || 'Administrator';
                    const words = name.split(/\s+/);
                    const initials = words.length > 1 ? words[0][0] + words[1][0] : words[0].substring(0, 2);
                    const colors = ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#6366f1', '#14b8a6'];
                    let hash = 0;
                    for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
                    return `<div class="cat-user-badge"><span class="cat-user-avatar" style="background: ${colors[Math.abs(hash) % colors.length]};">${escapeLaminationHtml(initials.toUpperCase())}</span><div class="cat-user-info"><span class="cat-user-name">${escapeLaminationHtml(name)}</span><span class="cat-user-role">Administrator</span></div></div>`;
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
                render: id => `<div class="cat-action-group"><button type="button" class="cat-btn-action cat-btn-edit btn-view-lamination" data-id="${Number(id)}" title="Detail Laminasi" aria-label="Detail laminasi"><i class="bi bi-arrows-fullscreen"></i></button><button type="button" class="cat-btn-action cat-btn-edit btn-edit-lamination" data-id="${Number(id)}" title="Edit Laminasi" aria-label="Edit laminasi"><i class="bi bi-pen"></i></button><button type="button" class="cat-btn-action cat-btn-del btn-delete-lamination" data-id="${Number(id)}" title="Hapus Laminasi" aria-label="Hapus laminasi"><i class="bi bi-trash3-fill"></i></button></div>`
            }
        ],
        pageLength: 10,
        layout: { topStart: null, topEnd: null, bottomStart: 'info', bottomEnd: 'paging' },
        language: { info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ laminasi', infoEmpty: 'Menampilkan 0 sampai 0 dari 0 laminasi', zeroRecords: 'Data laminasi tidak ditemukan', processing: 'Memuat data...' }
    });
    laminationTable.on('draw', function () {
        document.getElementById('laminationTotalCount').textContent = `${laminationTable.page.info().recordsTotal.toLocaleString('id-ID')} Total Laminasi`;
        updateLaminationSelection();
    });
    function clearSelection() {
        selectedLaminationIds.clear();
        updateLaminationSelection();
    }
    search.addEventListener('input', function () {
        clearSearch.style.display = this.value ? 'flex' : 'none';
        clearTimeout(searchTimer);
        clearSelection();
        searchTimer = setTimeout(() => laminationTable.search(search.value.trim()).draw(), 250);
    });
    clearSearch.addEventListener('click', function () {
        clearTimeout(searchTimer);
        search.value = '';
        clearSearch.style.display = 'none';
        clearSelection();
        laminationTable.search('').draw();
        search.focus();
    });
    function filterChanged() {
        clearSelection();
        laminationTable.ajax.reload();
    }
    if (typeof $.fn.select2 !== 'undefined') {
        $(creator).select2({ minimumResultsForSearch: 6, width: '100%' });
        $(period).select2({ minimumResultsForSearch: -1, width: '100%' });
        $(length).select2({ minimumResultsForSearch: -1, width: '76px' });
        $(creator).add(period).on('change', filterChanged)
            .on('select2:open', function () { $(this).closest('.cat-select-box').addClass('is-open'); })
            .on('select2:close', function () { $(this).closest('.cat-select-box').removeClass('is-open'); });
        $(length).on('change', function () { laminationTable.page.len(Number(this.value)).draw(); });
    } else {
        [creator, period].forEach(select => select.addEventListener('change', filterChanged));
        length.addEventListener('change', function () { laminationTable.page.len(Number(this.value)).draw(); });
    }
    document.getElementById('resetLaminationFilters').addEventListener('click', function () {
        clearTimeout(searchTimer);
        search.value = '';
        clearSearch.style.display = 'none';
        creator.value = 'all';
        period.value = 'all';
        length.value = '10';
        if (typeof $.fn.select2 !== 'undefined') $(creator).add(period).add(length).trigger('change.select2');
        clearSelection();
        laminationTable.search('');
        laminationTable.page.len(10);
        laminationTable.ajax.reload();
    });
    document.getElementById('checkAllLaminations').addEventListener('change', function () {
        document.querySelectorAll('.lamination-row-check').forEach(input => {
            if (this.checked) selectedLaminationIds.add(Number(input.value));
            else selectedLaminationIds.delete(Number(input.value));
        });
        updateLaminationSelection();
    });
    tableElement.addEventListener('change', function (event) {
        if (!event.target.classList.contains('lamination-row-check')) return;
        const id = Number(event.target.value);
        if (event.target.checked) selectedLaminationIds.add(id);
        else selectedLaminationIds.delete(id);
        updateLaminationSelection();
    });
    document.getElementById('cancelLaminationSelection').addEventListener('click', clearSelection);

    const modal = document.getElementById('modalBulkDeleteLaminations');
    const confirmButton = document.getElementById('confirmBulkDeleteLaminations');
    function closeModal() { if (!confirmButton.disabled) modal.classList.remove('is-open'); }
    document.getElementById('bulkDeleteLaminations').addEventListener('click', function () {
        if (!selectedLaminationIds.size) return;
        document.getElementById('bulkDeleteLaminationDescription').textContent = `Apakah Anda yakin ingin menghapus ${selectedLaminationIds.size} laminasi yang dipilih? Data yang sudah dihapus tidak dapat dikembalikan.`;
        modal.classList.add('is-open');
    });
    document.getElementById('closeBulkDeleteLaminations').addEventListener('click', closeModal);
    document.getElementById('cancelBulkDeleteLaminations').addEventListener('click', closeModal);
    modal.addEventListener('click', event => { if (event.target === modal) closeModal(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') closeModal(); });
    confirmButton.addEventListener('click', async function () {
        if (!selectedLaminationIds.size) return;
        confirmButton.disabled = true;
        try {
            const response = await fetch(tableElement.dataset.bulkUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ ids: Array.from(selectedLaminationIds) })
            });
            const result = await response.json();
            if (!response.ok || !result.success) {
                showToast('error', result.message || 'Gagal menghapus laminasi terpilih.');
                return;
            }
            clearSelection();
            modal.classList.remove('is-open');
            laminationTable.ajax.reload(null, false);
            showToast('success', result.message);
        } catch (error) {
            console.error('Gagal menghapus laminasi:', error);
            showToast('error', 'Terjadi kesalahan saat menghapus laminasi.');
        } finally {
            confirmButton.disabled = false;
        }
    });
});

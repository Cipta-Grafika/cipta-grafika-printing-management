let materialTable;
const selectedMaterialIds = new Set();

function escapeMaterialHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[char]);
}

function updateMaterialSelection() {
    const toolbar = document.getElementById('materialBulkToolbar');
    const checkAll = document.getElementById('checkAllMaterials');
    const rows = [...document.querySelectorAll('.material-row-check')];
    toolbar.style.display = selectedMaterialIds.size ? 'flex' : 'none';
    document.getElementById('materialBulkCount').textContent = `${selectedMaterialIds.size} terpilih`;
    rows.forEach(input => {
        input.checked = selectedMaterialIds.has(Number(input.value));
        input.closest('tr').classList.toggle('is-selected', input.checked);
    });
    const checked = rows.filter(input => input.checked).length;
    checkAll.checked = rows.length > 0 && checked === rows.length;
    checkAll.indeterminate = checked > 0 && checked < rows.length;
    checkAll.closest('label').classList.toggle('is-indeterminate', checkAll.indeterminate);
}

document.addEventListener('DOMContentLoaded', function () {
    const tableElement = document.getElementById('materialTable');
    const search = document.getElementById('materialSearch');
    const clearSearch = document.getElementById('clearMaterialSearch');
    const creator = document.getElementById('materialCreator');
    const period = document.getElementById('materialPeriod');
    const length = document.getElementById('materialPageLength');
    const category = document.getElementById('filterMaterialCategory');
    const exportLink = document.getElementById('btnExportMaterial');
    let searchTimer;

    function updateExportUrl() {
        const url = new URL(exportLink.href);
        if (category.value) url.searchParams.set('category_id', category.value);
        else url.searchParams.delete('category_id');
        exportLink.href = url.toString();
    }

    materialTable = new DataTable('#materialTable', {
        processing: true,
        serverSide: true,
        ordering: true,
        order: [],
        stripeClasses: [],
        ajax: {
            url: tableElement.dataset.url,
            type: 'GET',
            data: function (data) {
                data.creator = creator.value;
                data.period = period.value;
                data.category_id = category.value;
            }
        },
        columns: [
            {
                data: 'id', orderable: false, searchable: false, className: 'text-center cat-td-check',
                render: function (id, type, row) {
                    return `<label class="cat-custom-checkbox"><input type="checkbox" class="material-row-check" value="${Number(id)}" aria-label="Pilih material ${escapeMaterialHtml(row.material_name)}" ${selectedMaterialIds.has(Number(id)) ? 'checked' : ''}><span class="cat-checkbox-mark"><svg class="check-icon" viewBox="0 0 14 14" fill="none"><path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#fff" stroke-width="2.2"/></svg></span></label>`;
                }
            },
            {
                data: null, orderable: false, searchable: false, className: 'text-center cat-td-no',
                render: (data, type, row, meta) => `<span class="cat-num-pill">${meta.settings._iDisplayStart + meta.row + 1}</span>`
            },
            {
                data: 'material_code', className: 'text-center',
                render: data => escapeMaterialHtml(data || '-')
            },
            {
                data: 'material_name', className: 'cat-td-name',
                render: data => `<div class="cat-cell-name-box"><div class="cat-icon-symbol"><i class="bi bi-box-seam-fill"></i></div><div class="cat-name-details"><span class="cat-name-primary">${escapeMaterialHtml(data || '-')}</span><span class="cat-name-badge">Material Produksi</span></div></div>`
            },
            {
                data: 'category_name',
                render: data => escapeMaterialHtml(data || '-')
            },
            {
                data: 'status',
                render: data => `<span class="badge ${data === 'Active' ? 'success' : 'danger'} dot">${escapeMaterialHtml(data)}</span>`
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
                    return `<div class="cat-user-badge"><span class="cat-user-avatar" style="background: ${colors[Math.abs(hash) % colors.length]};">${escapeMaterialHtml(initials.toUpperCase())}</span><div class="cat-user-info"><span class="cat-user-name">${escapeMaterialHtml(name)}</span><span class="cat-user-role">Administrator</span></div></div>`;
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
                render: id => `<div class="cat-action-group"><button type="button" class="cat-btn-action cat-btn-edit btn-view-material" data-id="${Number(id)}" title="Detail Material" aria-label="Detail material"><i class="bi bi-arrows-fullscreen"></i></button><button type="button" class="cat-btn-action cat-btn-edit btn-edit-material" data-id="${Number(id)}" title="Edit Material" aria-label="Edit material"><i class="bi bi-pen"></i></button><button type="button" class="cat-btn-action cat-btn-del btn-delete-material" data-id="${Number(id)}" title="Hapus Material" aria-label="Hapus material"><i class="bi bi-trash3-fill"></i></button></div>`
            }
        ],
        pageLength: 10,
        layout: { topStart: null, topEnd: null, bottomStart: 'info', bottomEnd: 'paging' },
        language: {
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ material',
            infoEmpty: 'Menampilkan 0 sampai 0 dari 0 material',
            zeroRecords: 'Data material tidak ditemukan',
            processing: 'Memuat data...'
        }
    });

    materialTable.on('draw', function () {
        document.getElementById('materialTotalCount').textContent = `${materialTable.page.info().recordsTotal.toLocaleString('id-ID')} Total Material`;
        updateMaterialSelection();
    });

    search.addEventListener('input', function () {
        clearSearch.style.display = this.value ? 'flex' : 'none';
        clearTimeout(searchTimer);
        selectedMaterialIds.clear();
        updateMaterialSelection();
        searchTimer = setTimeout(() => {
            materialTable.search(search.value.trim()).draw();
        }, 250);
    });
    clearSearch.addEventListener('click', function () {
        clearTimeout(searchTimer);
        search.value = '';
        clearSearch.style.display = 'none';
        selectedMaterialIds.clear();
        updateMaterialSelection();
        materialTable.search('').draw();
        search.focus();
    });

    if (typeof $.fn.select2 !== 'undefined') {
        $(category).select2({ width: '100%' });
        $(creator).select2({ minimumResultsForSearch: 6, width: '100%' });
        $(period).select2({ minimumResultsForSearch: -1, width: '100%' });
        $(length).select2({ minimumResultsForSearch: -1, width: '76px' });
        $(creator).add(period).add(category).on('change', function () {
            updateExportUrl();
            selectedMaterialIds.clear();
            updateMaterialSelection();
            materialTable.ajax.reload();
        }).on('select2:open', function () {
            $(this).closest('.cat-select-box').addClass('is-open');
        }).on('select2:close', function () {
            $(this).closest('.cat-select-box').removeClass('is-open');
        });
        $(length).on('change', function () { materialTable.page.len(Number(this.value)).draw(); });
    } else {
        [creator, period, category].forEach(select => select.addEventListener('change', function () {
            updateExportUrl();
            selectedMaterialIds.clear();
            updateMaterialSelection();
            materialTable.ajax.reload();
        }));
        length.addEventListener('change', function () { materialTable.page.len(Number(this.value)).draw(); });
    }

    document.getElementById('resetMaterialFilters').addEventListener('click', function () {
        clearTimeout(searchTimer);
        search.value = '';
        clearSearch.style.display = 'none';
        creator.value = 'all';
        period.value = 'all';
        category.value = '';
        length.value = '10';
        if (typeof $.fn.select2 !== 'undefined') $(creator).add(period).add(category).add(length).trigger('change.select2');
        updateExportUrl();
        selectedMaterialIds.clear();
        updateMaterialSelection();
        materialTable.search('');
        materialTable.page.len(10);
        materialTable.ajax.reload();
    });

    document.getElementById('checkAllMaterials').addEventListener('change', function () {
        document.querySelectorAll('.material-row-check').forEach(input => {
            if (this.checked) selectedMaterialIds.add(Number(input.value));
            else selectedMaterialIds.delete(Number(input.value));
        });
        updateMaterialSelection();
    });
    tableElement.addEventListener('change', function (event) {
        if (!event.target.classList.contains('material-row-check')) return;
        const id = Number(event.target.value);
        if (event.target.checked) selectedMaterialIds.add(id);
        else selectedMaterialIds.delete(id);
        updateMaterialSelection();
    });
    document.getElementById('cancelMaterialSelection').addEventListener('click', function () {
        selectedMaterialIds.clear();
        updateMaterialSelection();
    });

    const modal = document.getElementById('modalBulkDeleteMaterials');
    const confirmButton = document.getElementById('confirmBulkDeleteMaterials');
    function closeModal() {
        if (!confirmButton.disabled) modal.classList.remove('is-open');
    }
    document.getElementById('bulkDeleteMaterials').addEventListener('click', function () {
        if (!selectedMaterialIds.size) return;
        document.getElementById('bulkDeleteMaterialDescription').textContent = `Apakah Anda yakin ingin menghapus ${selectedMaterialIds.size} material yang dipilih? Data yang sudah dihapus tidak dapat dikembalikan.`;
        modal.classList.add('is-open');
    });
    document.getElementById('closeBulkDeleteMaterials').addEventListener('click', closeModal);
    document.getElementById('cancelBulkDeleteMaterials').addEventListener('click', closeModal);
    modal.addEventListener('click', event => { if (event.target === modal) closeModal(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') closeModal(); });
    confirmButton.addEventListener('click', async function () {
        if (!selectedMaterialIds.size) return;
        confirmButton.disabled = true;
        try {
            const response = await fetch(tableElement.dataset.bulkUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: Array.from(selectedMaterialIds) })
            });
            const result = await response.json();
            if (!response.ok || !result.success) {
                showToast('error', result.message || 'Gagal menghapus material terpilih.');
                return;
            }
            selectedMaterialIds.clear();
            modal.classList.remove('is-open');
            updateMaterialSelection();
            materialTable.ajax.reload(null, false);
            showToast('success', result.message);
        } catch (error) {
            console.error('Gagal menghapus material:', error);
            showToast('error', 'Terjadi kesalahan saat menghapus material.');
        } finally {
            confirmButton.disabled = false;
        }
    });
});

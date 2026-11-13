let locationTable;
const selectedLocationIds = new Set();

function escapeLocationHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[char]);
}

function updateLocationSelection() {
    const toolbar = document.getElementById('locationBulkToolbar');
    const checkAll = document.getElementById('checkAllLocations');
    const rows = [...document.querySelectorAll('.location-row-check')];
    toolbar.style.display = selectedLocationIds.size ? 'flex' : 'none';
    document.getElementById('locationBulkCount').textContent = `${selectedLocationIds.size} terpilih`;
    rows.forEach(input => {
        input.checked = selectedLocationIds.has(Number(input.value));
        input.closest('tr').classList.toggle('is-selected', input.checked);
    });
    const checked = rows.filter(input => input.checked).length;
    checkAll.checked = rows.length > 0 && checked === rows.length;
    checkAll.indeterminate = checked > 0 && checked < rows.length;
    checkAll.closest('label').classList.toggle('is-indeterminate', checkAll.indeterminate);
}

document.addEventListener('DOMContentLoaded', function () {
    const tableElement = document.getElementById('locationTable');
    const search = document.getElementById('locationSearch');
    const clearSearch = document.getElementById('clearLocationSearch');
    const creator = document.getElementById('locationCreator');
    const period = document.getElementById('locationPeriod');
    const length = document.getElementById('locationPageLength');
    let searchTimer;

    locationTable = new DataTable('#locationTable', {
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
                    const name = escapeLocationHtml(row.name);
                    return `<label class="cat-custom-checkbox"><input type="checkbox" class="location-row-check" value="${Number(id)}" aria-label="Pilih lokasi ${name}" ${selectedLocationIds.has(Number(id)) ? 'checked' : ''}><span class="cat-checkbox-mark"><svg class="check-icon" viewBox="0 0 14 14" fill="none"><path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#fff" stroke-width="2.2"/></svg></span></label>`;
                }
            },
            {
                data: null, orderable: false, searchable: false, className: 'text-center cat-td-no',
                render: (data, type, row, meta) => `<span class="cat-num-pill">${meta.settings._iDisplayStart + meta.row + 1}</span>`
            },
            {
                data: 'name', className: 'cat-td-name',
                render: data => `<div class="cat-cell-name-box"><div class="cat-name-details"><span class="cat-name-primary">${escapeLocationHtml(data || '-')}</span><span class="cat-name-badge">Lokasi Operasional</span></div></div>`
            },
            {
                data: 'status',
                render: data => `<span class="badge ${data === 'Active' ? 'success' : 'danger'} dot" style="font-weight: 400;">${escapeLocationHtml(data)}</span>`
            },
            {
                data: 'created_by', className: 'cat-td-creator',
                render: function (data) {
                    const name = (data || '').trim() || 'Administrator';
                    return `<div class="cat-user-badge"><div class="cat-user-info"><span class="cat-user-name">${escapeLocationHtml(name)}</span><span class="cat-user-role">Administrator</span></div></div>`;
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
                render: function (id, type, row) {
                    return `<div class="cat-action-group"><button type="button" class="cat-btn-action cat-btn-edit btn-edit-location" data-id="${Number(id)}" data-name="${escapeLocationHtml(row.name)}" data-status="${escapeLocationHtml(row.status)}" title="Edit Lokasi" aria-label="Edit lokasi"><i class="bi bi-pen"></i></button><button type="button" class="cat-btn-action cat-btn-del btn-delete-location" data-id="${Number(id)}" title="Hapus Lokasi" aria-label="Hapus lokasi"><i class="bi bi-trash3-fill"></i></button></div>`;
                }
            }
        ],
        pageLength: 10,
        layout: { topStart: null, topEnd: null, bottomStart: 'info', bottomEnd: 'paging' },
        language: {
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ lokasi',
            infoEmpty: 'Menampilkan 0 sampai 0 dari 0 lokasi',
            zeroRecords: 'Data lokasi tidak ditemukan',
            processing: 'Memuat data...'
        }
    });

    locationTable.on('draw', function () {
        document.getElementById('locationTotalCount').textContent = `${locationTable.page.info().recordsTotal.toLocaleString('id-ID')} Total Lokasi`;
        updateLocationSelection();
    });

    search.addEventListener('input', function () {
        clearSearch.style.display = this.value ? 'flex' : 'none';
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            selectedLocationIds.clear();
            locationTable.search(search.value.trim()).draw();
        }, 250);
    });
    clearSearch.addEventListener('click', function () {
        clearTimeout(searchTimer);
        search.value = '';
        clearSearch.style.display = 'none';
        selectedLocationIds.clear();
        locationTable.search('').draw();
        search.focus();
    });

    if (typeof $.fn.select2 !== 'undefined') {
        $(creator).select2({ minimumResultsForSearch: 6, width: '100%' });
        $(period).select2({ minimumResultsForSearch: -1, width: '100%' });
        $(length).select2({ minimumResultsForSearch: -1, width: '76px' });
        $(creator).add(period).on('change', function () {
            selectedLocationIds.clear();
            locationTable.ajax.reload();
        }).on('select2:open', function () {
            $(this).closest('.cat-select-box').addClass('is-open');
        }).on('select2:close', function () {
            $(this).closest('.cat-select-box').removeClass('is-open');
        });
        $(length).on('change', function () { locationTable.page.len(Number(this.value)).draw(); });
    } else {
        [creator, period].forEach(select => select.addEventListener('change', function () {
            selectedLocationIds.clear();
            locationTable.ajax.reload();
        }));
        length.addEventListener('change', function () { locationTable.page.len(Number(this.value)).draw(); });
    }

    document.getElementById('resetLocationFilters').addEventListener('click', function () {
        clearTimeout(searchTimer);
        search.value = '';
        clearSearch.style.display = 'none';
        creator.value = 'all';
        period.value = 'all';
        length.value = '10';
        if (typeof $.fn.select2 !== 'undefined') $(creator).add(period).add(length).trigger('change.select2');
        selectedLocationIds.clear();
        locationTable.search('');
        locationTable.page.len(10);
        locationTable.ajax.reload();
    });

    document.getElementById('checkAllLocations').addEventListener('change', function () {
        document.querySelectorAll('.location-row-check').forEach(input => {
            if (this.checked) selectedLocationIds.add(Number(input.value));
            else selectedLocationIds.delete(Number(input.value));
        });
        updateLocationSelection();
    });
    tableElement.addEventListener('change', function (event) {
        if (!event.target.classList.contains('location-row-check')) return;
        const id = Number(event.target.value);
        if (event.target.checked) selectedLocationIds.add(id);
        else selectedLocationIds.delete(id);
        updateLocationSelection();
    });
    document.getElementById('cancelLocationSelection').addEventListener('click', function () {
        selectedLocationIds.clear();
        updateLocationSelection();
    });

    const modal = document.getElementById('modalBulkDeleteLocations');
    const confirmButton = document.getElementById('confirmBulkDeleteLocations');
    function closeModal() {
        if (!confirmButton.disabled) modal.classList.remove('is-open');
    }
    document.getElementById('bulkDeleteLocations').addEventListener('click', function () {
        if (!selectedLocationIds.size) return;
        document.getElementById('bulkDeleteLocationDescription').textContent = `Apakah Anda yakin ingin menghapus ${selectedLocationIds.size} lokasi yang dipilih? Data yang sudah dihapus tidak dapat dikembalikan.`;
        modal.classList.add('is-open');
    });
    document.getElementById('closeBulkDeleteLocations').addEventListener('click', closeModal);
    document.getElementById('cancelBulkDeleteLocations').addEventListener('click', closeModal);
    modal.addEventListener('click', event => { if (event.target === modal) closeModal(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') closeModal(); });
    confirmButton.addEventListener('click', async function () {
        if (!selectedLocationIds.size) return;
        confirmButton.disabled = true;
        try {
            const response = await fetch(tableElement.dataset.bulkUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: Array.from(selectedLocationIds) })
            });
            const result = await response.json();
            if (!response.ok || !result.success) {
                showToast('error', result.message || 'Gagal menghapus lokasi terpilih.');
                return;
            }
            selectedLocationIds.clear();
            modal.classList.remove('is-open');
            updateLocationSelection();
            locationTable.ajax.reload(null, false);
            showToast('success', result.message);
        } catch (error) {
            console.error('Gagal menghapus lokasi:', error);
            showToast('error', 'Terjadi kesalahan saat menghapus lokasi.');
        } finally {
            confirmButton.disabled = false;
        }
    });
});

let displayPricingRuleTable;
const selectedDisplayPricingRules = new Map();
const escapeDisplayPricingRule = value => String(value ?? '').replace(/[&<>"']/g, char => ({
    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
})[char]);
const displayPricingRuleKey = row => `${row.engine_id}:${row.location_id}:${row.category_id}`;

function updateDisplayPricingRuleSelection() {
    const rows = [...document.querySelectorAll('.display-rule-check')];
    const all = document.getElementById('checkAllDisplayPricingRules');
    document.getElementById('displayPricingRuleBulkToolbar').style.display = selectedDisplayPricingRules.size ? 'flex' : 'none';
    document.getElementById('displayPricingRuleBulkCount').textContent = `${selectedDisplayPricingRules.size} terpilih`;
    rows.forEach(input => {
        input.checked = selectedDisplayPricingRules.has(input.value);
        input.closest('tr').classList.toggle('is-selected', input.checked);
    });
    const checked = rows.filter(input => input.checked).length;
    all.checked = rows.length > 0 && checked === rows.length;
    all.indeterminate = checked > 0 && checked < rows.length;
    all.closest('label').classList.toggle('is-indeterminate', all.indeterminate);
}

function clearDisplayPricingRuleSelection() {
    selectedDisplayPricingRules.clear();
    updateDisplayPricingRuleSelection();
}

document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('displayPricingRuleTable');
    const search = document.getElementById('displayPricingRuleSearch');
    const clearSearch = document.getElementById('clearDisplayPricingRuleSearch');
    const engine = document.getElementById('displayPricingRuleEngineFilter');
    const location = document.getElementById('displayPricingRuleLocationFilter');
    const length = document.getElementById('displayPricingRulePageLength');
    let searchTimer;

    displayPricingRuleTable = new DataTable(table, {
        processing: true, serverSide: true, ordering: true, order: [], stripeClasses: [],
        ajax: { url: table.dataset.url, type: 'GET', data: data => { data.engine_id = engine.value; data.location_id = location.value; } },
        columns: [
            { data: null, orderable: false, searchable: false, className: 'text-center cat-td-check',
                render: (data, type, row) => `<label class="cat-custom-checkbox"><input type="checkbox" class="display-rule-check" value="${escapeDisplayPricingRule(displayPricingRuleKey(row))}" aria-label="Pilih pricing rule ${escapeDisplayPricingRule(row.engine_name)} ${escapeDisplayPricingRule(row.location_name)}"><span class="cat-checkbox-mark"><svg class="check-icon" viewBox="0 0 14 14" fill="none"><path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#fff" stroke-width="2.2"/></svg></span></label>` },
            { data: null, orderable: false, searchable: false, className: 'text-center cat-td-no', render: (data, type, row, meta) => `<span class="cat-num-pill">${meta.settings._iDisplayStart + meta.row + 1}</span>` },
            { data: 'engine_name', className: 'cat-td-name', render: data => `<div class="cat-cell-name-box"><div class="cat-icon-symbol"><i class="bi bi-gear-fill"></i></div><div class="cat-name-details"><span class="cat-name-primary">${escapeDisplayPricingRule(data || '-')}</span><span class="cat-name-badge">Pricing Rule Display</span></div></div>` },
            { data: 'location_name', render: data => escapeDisplayPricingRule(data || '-') },
            { data: 'category_name', render: data => escapeDisplayPricingRule(data || '-') },
            { data: 'jumlah_markup', className: 'text-center', render: data => Number(data || 0).toLocaleString('id-ID') },
            { data: null, orderable: false, searchable: false, className: 'text-center cat-td-actions', render: (data, type, row) => {
                const attrs = `data-engine-id="${Number(row.engine_id)}" data-location-id="${Number(row.location_id)}" data-category-id="${Number(row.category_id)}"`;
                return `<div class="cat-action-group"><button type="button" class="cat-btn-action cat-btn-edit btn-detail-display-pricing-rule" ${attrs} title="Detail" aria-label="Detail"><i class="bi bi-arrows-fullscreen"></i></button><button type="button" class="cat-btn-action cat-btn-edit btn-edit-display-pricing-rule" ${attrs} title="Edit" aria-label="Edit"><i class="bi bi-pen"></i></button><button type="button" class="cat-btn-action cat-btn-del btn-delete-display-pricing-rule" ${attrs} title="Hapus" aria-label="Hapus"><i class="bi bi-trash3-fill"></i></button></div>`;
            } }
        ],
        pageLength: 10, layout: { topStart: null, topEnd: null, bottomStart: 'info', bottomEnd: 'paging' },
        language: { info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ kelompok', infoEmpty: 'Menampilkan 0 sampai 0 dari 0 kelompok', zeroRecords: 'Pricing rule Display tidak ditemukan', processing: 'Memuat data...' }
    });
    displayPricingRuleTable.on('draw', function () {
        document.getElementById('displayPricingRuleTotalCount').textContent = `${displayPricingRuleTable.page.info().recordsTotal.toLocaleString('id-ID')} Total Kelompok`;
        updateDisplayPricingRuleSelection();
    });
    search.addEventListener('input', function () {
        clearSearch.style.display = this.value ? 'flex' : 'none';
        clearTimeout(searchTimer);
        clearDisplayPricingRuleSelection();
        searchTimer = setTimeout(() => displayPricingRuleTable.search(search.value.trim()).draw(), 250);
    });
    clearSearch.addEventListener('click', function () {
        clearTimeout(searchTimer); search.value = ''; clearSearch.style.display = 'none';
        clearDisplayPricingRuleSelection(); displayPricingRuleTable.search('').draw(); search.focus();
    });
    function filterChanged() { clearDisplayPricingRuleSelection(); displayPricingRuleTable.ajax.reload(); }
    if (typeof $.fn.select2 !== 'undefined') {
        $(engine).add(location).select2({ width: '100%' }).on('change', filterChanged)
            .on('select2:open', function () { $(this).closest('.cat-select-box').addClass('is-open'); })
            .on('select2:close', function () { $(this).closest('.cat-select-box').removeClass('is-open'); });
        $(length).select2({ minimumResultsForSearch: -1, width: '76px' }).on('change', function () { displayPricingRuleTable.page.len(Number(this.value)).draw(); });
    } else {
        [engine, location].forEach(select => select.addEventListener('change', filterChanged));
        length.addEventListener('change', function () { displayPricingRuleTable.page.len(Number(this.value)).draw(); });
    }
    document.getElementById('resetDisplayPricingRuleFilters').addEventListener('click', function () {
        clearTimeout(searchTimer); search.value = ''; clearSearch.style.display = 'none';
        engine.value = location.value = 'all'; length.value = '10';
        if (typeof $.fn.select2 !== 'undefined') $(engine).add(location).add(length).trigger('change.select2');
        clearDisplayPricingRuleSelection(); displayPricingRuleTable.search(''); displayPricingRuleTable.page.len(10); displayPricingRuleTable.ajax.reload();
    });
    function selectRow(input, selected) {
        if (!selected) { selectedDisplayPricingRules.delete(input.value); return; }
        const row = displayPricingRuleTable.row(input.closest('tr')).data();
        selectedDisplayPricingRules.set(input.value, { engine_id: row.engine_id, location_id: row.location_id, category_id: row.category_id, member_ids: row.member_ids });
    }
    document.getElementById('checkAllDisplayPricingRules').addEventListener('change', function () {
        document.querySelectorAll('.display-rule-check').forEach(input => selectRow(input, this.checked)); updateDisplayPricingRuleSelection();
    });
    table.addEventListener('change', function (event) {
        if (!event.target.classList.contains('display-rule-check')) return;
        selectRow(event.target, event.target.checked); updateDisplayPricingRuleSelection();
    });
    document.getElementById('cancelDisplayPricingRuleSelection').addEventListener('click', clearDisplayPricingRuleSelection);
    const modal = document.getElementById('modalBulkDeleteDisplayPricingRules');
    const confirmButton = document.getElementById('confirmBulkDeleteDisplayPricingRules');
    function closeModal() { if (!confirmButton.disabled) modal.classList.remove('is-open'); }
    document.getElementById('bulkDeleteDisplayPricingRules').addEventListener('click', function () {
        if (!selectedDisplayPricingRules.size) return;
        document.getElementById('bulkDeleteDisplayPricingRuleDescription').textContent = `${selectedDisplayPricingRules.size} kelompok akan dihapus beserta aturan miliknya. Data yang dihapus tidak dapat dikembalikan.`;
        modal.classList.add('is-open');
    });
    document.getElementById('closeBulkDeleteDisplayPricingRules').addEventListener('click', closeModal);
    document.getElementById('cancelBulkDeleteDisplayPricingRules').addEventListener('click', closeModal);
    modal.addEventListener('click', event => { if (event.target === modal) closeModal(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') closeModal(); });
    confirmButton.addEventListener('click', async function () {
        if (!selectedDisplayPricingRules.size) return;
        confirmButton.disabled = true;
        try {
            const response = await fetch(table.dataset.bulkUrl, { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ groups: Array.from(selectedDisplayPricingRules.values()) }) });
            const result = await response.json();
            if (!response.ok || !result.success) { showToast('error', result.message || 'Gagal menghapus pricing rule Display.'); return; }
            clearDisplayPricingRuleSelection(); modal.classList.remove('is-open'); displayPricingRuleTable.ajax.reload(null, false); showToast('success', result.message);
        } catch (error) {
            console.error('Gagal menghapus pricing rule Display:', error);
            showToast('error', 'Terjadi kesalahan saat menghapus pricing rule Display.');
        } finally { confirmButton.disabled = false; }
    });
});

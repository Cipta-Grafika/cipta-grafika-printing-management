let pricingRuleTable;
const selectedPricingRules = new Map();

function escapePricingRuleHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    })[char]);
}

function pricingRuleKey(row) {
    return row.pricing_rule_source === 'digital'
        ? `digital:${row.digital_pricing_rule_id}`
        : `large:${row.engine_id}:${row.location_id}:${row.vendor_id ?? 'null'}:${row.category_id}`;
}

function updatePricingRuleSelection() {
    const rows = [...document.querySelectorAll('.pricing-rule-row-check')];
    const checkAll = document.getElementById('checkAllPricingRules');
    document.getElementById('pricingRuleBulkToolbar').style.display = selectedPricingRules.size ? 'flex' : 'none';
    document.getElementById('pricingRuleBulkCount').textContent = `${selectedPricingRules.size} terpilih`;
    rows.forEach(input => {
        input.checked = selectedPricingRules.has(input.value);
        input.closest('tr').classList.toggle('is-selected', input.checked);
    });
    const checked = rows.filter(input => input.checked).length;
    checkAll.checked = rows.length > 0 && checked === rows.length;
    checkAll.indeterminate = checked > 0 && checked < rows.length;
    checkAll.closest('label').classList.toggle('is-indeterminate', checkAll.indeterminate);
}

function clearPricingRuleSelection() {
    selectedPricingRules.clear();
    updatePricingRuleSelection();
}

document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('pricingRuleTable');
    const search = document.getElementById('pricingRuleSearch');
    const clearSearch = document.getElementById('clearPricingRuleSearch');
    const engine = document.getElementById('pricingRuleEngineFilter');
    const location = document.getElementById('pricingRuleLocationFilter');
    const vendor = document.getElementById('pricingRuleVendorFilter');
    const category = document.getElementById('pricingRuleCategoryFilter');
    const length = document.getElementById('pricingRulePageLength');
    const filters = [engine, location, vendor, category];
    let searchTimer;

    pricingRuleTable = new DataTable(table, {
        processing: true, serverSide: true, ordering: true, order: [], stripeClasses: [],
        ajax: {
            url: table.dataset.url, type: 'GET',
            data: data => {
                data.pricing_engine = engine.value;
                data.location_id = location.value;
                data.vendor_id = vendor.value;
                data.category_id = category.value;
            }
        },
        columns: [
            { data: null, orderable: false, searchable: false, className: 'text-center cat-td-check',
                render: (data, type, row) => `<label class="cat-custom-checkbox"><input type="checkbox" class="pricing-rule-row-check" value="${escapePricingRuleHtml(pricingRuleKey(row))}" aria-label="Pilih pricing rule ${escapePricingRuleHtml(row.category_name)}"><span class="cat-checkbox-mark"><svg class="check-icon" viewBox="0 0 14 14" fill="none"><path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#fff" stroke-width="2.2"/></svg></span></label>` },
            { data: null, orderable: false, searchable: false, className: 'text-center cat-td-no', render: (data, type, row, meta) => `<span class="cat-num-pill">${meta.settings._iDisplayStart + meta.row + 1}</span>` },
            { data: 'engine_name', className: 'cat-td-name', render: data => `<div class="cat-cell-name-box"><div class="cat-icon-symbol"><i class="bi bi-gear-fill"></i></div><div class="cat-name-details"><span class="cat-name-primary">${escapePricingRuleHtml(data || '-')}</span><span class="cat-name-badge">Pricing Rule Material</span></div></div>` },
            { data: 'location_name', render: data => escapePricingRuleHtml(data || '-') },
            { data: 'vendor_name', render: data => escapePricingRuleHtml(data || '-') },
            { data: 'category_name', render: data => escapePricingRuleHtml(data || '-') },
            { data: 'jumlah_markup', className: 'text-center', render: data => Number(data || 0).toLocaleString('id-ID') },
            { data: null, orderable: false, searchable: false, className: 'text-center cat-td-actions',
                render: function (data, type, row) {
                    const attrs = `data-engine-id="${Number(row.engine_id)}" data-location-id="${Number(row.location_id)}" data-vendor-id="${row.vendor_id == null ? 'null' : Number(row.vendor_id)}" data-category-id="${Number(row.category_id)}"`;
                    const digitalId = row.digital_pricing_rule_id == null ? '' : Number(row.digital_pricing_rule_id);
                    const detail = `<button type="button" class="cat-btn-action cat-btn-edit btn-detail-pricing-rule" data-pricing-rule-source="${escapePricingRuleHtml(row.pricing_rule_source)}" data-digital-pricing-rule-id="${digitalId}" ${attrs} aria-label="Detail pricing rule" title="Detail"><i class="bi bi-arrows-fullscreen"></i></button>`;
                    const edit = row.pricing_rule_source === 'digital'
                        ? `<button type="button" class="cat-btn-action cat-btn-edit btn-edit-digital-pricing-rule" data-digital-pricing-rule-id="${digitalId}" aria-label="Edit" title="Edit"><i class="bi bi-pen"></i></button><button type="button" class="cat-btn-action cat-btn-del btn-delete-digital-pricing-rule" data-digital-pricing-rule-id="${digitalId}" aria-label="Hapus" title="Hapus"><i class="bi bi-trash3-fill"></i></button>`
                        : `<button type="button" class="cat-btn-action cat-btn-edit btn-edit-pricing-rule" ${attrs} aria-label="Edit" title="Edit"><i class="bi bi-pen"></i></button><button type="button" class="cat-btn-action cat-btn-del btn-delete-pricing-rule" ${attrs} aria-label="Hapus" title="Hapus"><i class="bi bi-trash3-fill"></i></button>`;
                    return `<div class="cat-action-group">${detail}${edit}</div>`;
                }
            }
        ],
        pageLength: 10,
        layout: { topStart: null, topEnd: null, bottomStart: 'info', bottomEnd: 'paging' },
        language: { info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ kelompok', infoEmpty: 'Menampilkan 0 sampai 0 dari 0 kelompok', zeroRecords: 'Pricing rule tidak ditemukan', processing: 'Memuat data...' }
    });
    pricingRuleTable.on('draw', function () {
        document.getElementById('pricingRuleTotalCount').textContent = `${pricingRuleTable.page.info().recordsTotal.toLocaleString('id-ID')} Total Kelompok`;
        updatePricingRuleSelection();
    });
    search.addEventListener('input', function () {
        clearSearch.style.display = this.value ? 'flex' : 'none';
        clearTimeout(searchTimer);
        clearPricingRuleSelection();
        searchTimer = setTimeout(() => pricingRuleTable.search(search.value.trim()).draw(), 250);
    });
    clearSearch.addEventListener('click', function () {
        clearTimeout(searchTimer);
        search.value = '';
        clearSearch.style.display = 'none';
        clearPricingRuleSelection();
        pricingRuleTable.search('').draw();
        search.focus();
    });
    function filterChanged() {
        clearPricingRuleSelection();
        pricingRuleTable.ajax.reload();
    }
    if (typeof $.fn.select2 !== 'undefined') {
        filters.forEach(select => $(select).select2({ width: '100%' }));
        $(length).select2({ minimumResultsForSearch: -1, width: '76px' });
        $(filters).on('change', filterChanged)
            .on('select2:open', function () { $(this).closest('.cat-select-box').addClass('is-open'); })
            .on('select2:close', function () { $(this).closest('.cat-select-box').removeClass('is-open'); });
        $(length).on('change', function () { pricingRuleTable.page.len(Number(this.value)).draw(); });
    } else {
        filters.forEach(select => select.addEventListener('change', filterChanged));
        length.addEventListener('change', function () { pricingRuleTable.page.len(Number(this.value)).draw(); });
    }
    document.getElementById('resetPricingRuleFilters').addEventListener('click', function () {
        clearTimeout(searchTimer);
        search.value = '';
        clearSearch.style.display = 'none';
        engine.value = 'large_format';
        location.value = vendor.value = category.value = 'all';
        length.value = '10';
        if (typeof $.fn.select2 !== 'undefined') $(filters).add(length).trigger('change.select2');
        clearPricingRuleSelection();
        pricingRuleTable.search('');
        pricingRuleTable.page.len(10);
        pricingRuleTable.ajax.reload();
    });
    function selectRow(input, selected) {
        const row = pricingRuleTable.row(input.closest('tr')).data();
        if (selected) {
            selectedPricingRules.set(input.value, {
                source: row.pricing_rule_source, member_ids: row.member_ids,
                ...(row.pricing_rule_source === 'digital' ? { detail_ids: row.detail_ids } : {}),
                engine_id: row.engine_id, location_id: row.location_id,
                vendor_id: row.vendor_id, category_id: row.category_id
            });
        } else selectedPricingRules.delete(input.value);
    }
    document.getElementById('checkAllPricingRules').addEventListener('change', function () {
        document.querySelectorAll('.pricing-rule-row-check').forEach(input => selectRow(input, this.checked));
        updatePricingRuleSelection();
    });
    table.addEventListener('change', function (event) {
        if (!event.target.classList.contains('pricing-rule-row-check')) return;
        selectRow(event.target, event.target.checked);
        updatePricingRuleSelection();
    });
    document.getElementById('cancelPricingRuleSelection').addEventListener('click', clearPricingRuleSelection);
    const modal = document.getElementById('modalBulkDeletePricingRules');
    const confirmButton = document.getElementById('confirmBulkDeletePricingRules');
    function closeModal() { if (!confirmButton.disabled) modal.classList.remove('is-open'); }
    document.getElementById('bulkDeletePricingRules').addEventListener('click', function () {
        if (!selectedPricingRules.size) return;
        document.getElementById('bulkDeletePricingRuleDescription').textContent = `${selectedPricingRules.size} kelompok akan dihapus beserta aturan markup miliknya. Data yang dihapus tidak dapat dikembalikan.`;
        modal.classList.add('is-open');
    });
    document.getElementById('closeBulkDeletePricingRules').addEventListener('click', closeModal);
    document.getElementById('cancelBulkDeletePricingRules').addEventListener('click', closeModal);
    modal.addEventListener('click', event => { if (event.target === modal) closeModal(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') closeModal(); });
    confirmButton.addEventListener('click', async function () {
        if (!selectedPricingRules.size) return;
        confirmButton.disabled = true;
        try {
            const response = await fetch(table.dataset.bulkUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                body: JSON.stringify({ groups: Array.from(selectedPricingRules.values()) })
            });
            const result = await response.json();
            if (!response.ok || !result.success) {
                showToast('error', result.message || 'Gagal menghapus pricing rule terpilih.');
                return;
            }
            clearPricingRuleSelection();
            modal.classList.remove('is-open');
            pricingRuleTable.ajax.reload(null, false);
            showToast('success', result.message);
        } catch (error) {
            console.error('Gagal menghapus pricing rule:', error);
            showToast('error', 'Terjadi kesalahan saat menghapus pricing rule.');
        } finally {
            confirmButton.disabled = false;
        }
    });
});

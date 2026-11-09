$(function () {
    const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, char => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[char]));
    const checkMark = '<span class="cat-checkbox-mark"><svg class="check-icon" viewBox="0 0 14 14" fill="none"><path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>';
    const notify = (type, message) => {
        if (typeof showToast === 'function') showToast(type, message);
        else alert(message);
    };
    const number = value => Number(value).toLocaleString('id-ID');
    const chip = (icon, value) => `<span class="cat-date-chip"><i class="bi ${icon}"></i><span>${escapeHtml(value ?? '-')}</span></span>`;
    const name = (icon, value, badge) => `<div class="cat-cell-name-box">
        <div class="cat-icon-symbol"><i class="bi ${icon}"></i></div>
        <div class="cat-name-details"><span class="cat-name-primary">${escapeHtml(value)}</span>
        <span class="cat-name-badge">${escapeHtml(badge)}</span></div></div>`;
    const textColumn = (field, icon) => ({
        data: field, className: 'text-center',
        render: (value, type) => type === 'display' ? chip(icon, value) : value
    });
    const nameColumn = (field, icon, badge) => ({
        data: field,
        render: (value, type) => type === 'display' ? name(icon, value, badge) : value
    });
    const moneyColumn = (field, optional = false, fractional = false) => ({
        data: field, className: 'text-end', render: (value, type) => {
            if (type !== 'display') return value;
            if (optional && !Number(value)) return '-';
            const digits = fractional && Number(value) < 10 ? 2 : 0;
            return `<span style="font-weight:600;">Rp ${Number(value).toLocaleString('id-ID', {minimumFractionDigits: digits, maximumFractionDigits: digits})}</span>`;
        }
    });
    const qtyColumn = (field, unit, unlimited = false) => ({
        data: field, className: 'text-center', render: (value, type) => {
            if (type !== 'display') return value;
            return unlimited && value == null ? chip('bi-infinity', 'Tak Terbatas') : chip('bi-stack', `${number(value)} ${unit}`);
        }
    });
    const columns = {
        product: [
            textColumn('calendar_type', 'bi-calendar3'),
            nameColumn('product_name', 'bi-calendar2-range', 'Produk Kalender'),
            {data: null, className: 'text-center', render: (value, type, row) => type === 'display' ?
                chip('bi-aspect-ratio', `${number(row.final_width)} x ${number(row.final_height)} cm`) : row.final_width},
            textColumn('orientation', 'bi-arrow-left-right'),
            {data: 'digital_up', className: 'text-center', render: (value, type, row) => type === 'display' ?
                chip('bi-printer', row.digital_available ? `${number(value)} up` : 'Over Area') : value},
            textColumn('offset_machine', 'bi-printer'),
            {data: 'offset_up', className: 'text-center', render: (value, type, row) => type === 'display' ?
                chip('bi-stack', `${number(value)} up${row.offset_up_bw ? ` (BW: ${number(row.offset_up_bw)})` : ''}`) : value},
            textColumn('recommended_plano', 'bi-file-earmark'),
            qtyColumn('default_leaf_count', 'lbr')
        ],
        material: [
            nameColumn('material_name', 'bi-file-earmark-text', 'Bahan Kertas'),
            textColumn('category', 'bi-tag-fill'),
            ...['digital_cost_print_1_side', 'digital_cost_print_2_side', 'digital_paper_cost'].map(field => moneyColumn(field)),
            ...['offset_price_65x90', 'offset_price_65x100', 'offset_price_109x79'].map(field => moneyColumn(field, true))
        ],
        component: [
            textColumn('component_code', 'bi-upc-scan'),
            nameColumn('name', 'bi-tools', 'Komponen Finishing'),
            textColumn('component_type', 'bi-tag-fill'),
            textColumn('unit', 'bi-box'),
            moneyColumn('price', false, true),
            moneyColumn('minimum_fee', true),
            {data: 'description', render: (value, type) => type === 'display' ?
                `<span style="display:block;max-width:260px;white-space:normal;color:var(--t-muted);">${escapeHtml(value ?? '-')}</span>` : value}
        ],
        rule: [
            nameColumn('rule_name', 'bi-percent', 'Pricing Rule'),
            qtyColumn('min_qty', 'pcs'),
            qtyColumn('max_qty', 'pcs', true),
            {data: 'markup_percent', className: 'text-center', render: (value, type) => type === 'display' ?
                chip('bi-percent', `+${number(value)}%`) : value},
            moneyColumn('rounding_value')
        ]
    };
    const edit = {
        product: openEditProductModal, material: openEditMaterialModal,
        component: openEditComponentModal, rule: openEditRuleModal
    };
    const modal = document.getElementById('modalBulkDeleteCalendar');
    const confirmButton = document.getElementById('confirmBulkDeleteCalendar');
    let activeBulk = null;
    let deleting = false;
    window.calTables = {};
    window.calSelections = {};

    document.querySelectorAll('[data-calendar-type]').forEach(card => {
        const {calendarType: type, prefix: p, entity, nameField, url, defaultOrder} = card.dataset;
        const get = suffix => document.getElementById(p + suffix);
        const tableElement = get('Table');
        const wrap = tableElement.closest('.cat-table-wrap');
        const selected = new Set();
        window.calSelections[type] = selected;
        const rowClass = p + '-row-check';
        const checkAll = get('CheckAll');
        const search = get('Search');
        const clear = get('ClearSearch');
        const order = [[Number(defaultOrder), 'asc']];
        let searchTimer;

        function refreshSelection() {
            get('BulkToolbar').style.display = selected.size ? 'flex' : 'none';
            get('BulkCount').textContent = `${selected.size} terpilih`;
            const inputs = tableElement.querySelectorAll('.' + rowClass);
            let count = 0;
            inputs.forEach(input => {
                input.checked = selected.has(Number(input.value));
                input.closest('tr').classList.toggle('is-selected', input.checked);
                if (input.checked) count++;
            });
            checkAll.checked = inputs.length > 0 && count === inputs.length;
            checkAll.indeterminate = count > 0 && count < inputs.length;
            checkAll.closest('label').classList.toggle('is-indeterminate', checkAll.indeterminate);
        }

        function centerProcessing() {
            const indicator = document.getElementById(p + 'Table_processing');
            if (indicator && indicator.parentElement !== wrap) {
                indicator.classList.add('cat-processing-centered');
                wrap.appendChild(indicator);
            }
            if (indicator && wrap.clientWidth && wrap.clientHeight) {
                indicator.style.setProperty('left', `${wrap.scrollLeft + wrap.clientWidth / 2}px`, 'important');
                indicator.style.setProperty('top', `${wrap.scrollTop + wrap.clientHeight / 2}px`, 'important');
            }
        }
        $('#' + p + 'Status, #' + p + 'Period').select2({width: '100%', minimumResultsForSearch: -1});
        $(get('PageLength')).select2({width: '76px', minimumResultsForSearch: -1});
        $(tableElement).on('processing.dt', centerProcessing);
        $(tableElement).on('column-sizing.dt', centerProcessing);
        wrap.addEventListener('scroll', centerProcessing, {passive: true});
        window.addEventListener('resize', centerProcessing);
        const table = new DataTable(tableElement, {
            processing: true, serverSide: true, responsive: false, order,
            ajax: {url, type: 'GET', data: data => {
                data.status = get('Status').value;
                data.period = get('Period').value;
            }},
            columns: [
                {data: null, orderable: false, searchable: false, className: 'text-center cat-td-check',
                    render: (value, renderType, row) => `<label class="cat-custom-checkbox">
                        <input type="checkbox" class="${rowClass}" value="${Number(row.id)}" aria-label="Pilih ${escapeHtml(entity)} ${escapeHtml(row[nameField])}">
                        ${checkMark}</label>`},
                {data: null, orderable: false, searchable: false, className: 'text-center cat-td-no',
                    render: (value, renderType, row, meta) => `<span class="cat-num-pill">${meta.settings._iDisplayStart + meta.row + 1}</span>`},
                ...columns[type],
                {data: 'status', className: 'text-center', render: (value, renderType) => {
                    if (renderType !== 'display') return value;
                    const status = String(value ?? '').toLowerCase();
                    return `<span class="badge ${status === 'active' ? 'success' : 'danger'} dot">${escapeHtml(status === 'active' ? 'Active' : status === 'inactive' ? 'Inactive' : value)}</span>`;
                }},
                {data: 'id', orderable: false, searchable: false, className: 'text-center cat-td-actions',
                    render: () => `<div class="cat-action-group">
                        <button type="button" class="cat-btn-action cat-btn-view btn-detail-cal" title="Lihat detail" aria-label="View"><i class="bi bi-arrows-fullscreen"></i></button>
                        <button type="button" class="cat-btn-action cat-btn-edit btn-edit-cal" title="Edit" aria-label="Edit"><i class="bi bi-pen"></i></button>
                        <button type="button" class="cat-btn-action cat-btn-del btn-delete-cal" title="Hapus" aria-label="Hapus"><i class="bi bi-trash3-fill"></i></button>
                    </div>`}
            ],
            pageLength: 10,
            layout: {topStart: null, topEnd: null, bottomStart: 'info', bottomEnd: 'paging'},
            language: {
                info: `Menampilkan _START_ sampai _END_ dari _TOTAL_ ${entity.toLowerCase()}`,
                infoEmpty: `Menampilkan 0 sampai 0 dari 0 ${entity.toLowerCase()}`,
                zeroRecords: `Data ${entity.toLowerCase()} tidak ditemukan`, processing: 'Memuat data...'
            }
        });
        window.calTables[type] = table;
        centerProcessing();
        table.on('draw', () => {
            const total = table.page.info().recordsTotal;
            get('TotalCount').textContent = `${number(total)} Total ${entity}`;
            const suffix = p.replace('calendar', '');
            $('#badgeCount' + suffix + ', #statTotal' + suffix).text(total);
            refreshSelection();
            centerProcessing();
        });
        $(tableElement).on('click', '.btn-detail-cal, .btn-edit-cal, .btn-delete-cal', function () {
            const row = table.row(this.closest('tr')).data();
            if (this.classList.contains('btn-detail-cal')) openDetailModal(type, row);
            else if (this.classList.contains('btn-edit-cal')) edit[type](row);
            else confirmDeleteCalendar(type, Number(row.id), row[nameField]);
        });
        search.addEventListener('input', () => {
            clear.style.display = search.value.trim() ? 'flex' : 'none';
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => table.search(search.value.trim()).draw(), 250);
        });
        clear.addEventListener('click', () => {
            clearTimeout(searchTimer);
            search.value = '';
            clear.style.display = 'none';
            table.search('').draw();
            search.focus();
        });
        $('#' + p + 'Status, #' + p + 'Period').on('change', () => table.ajax.reload(null, true));
        $(get('PageLength')).on('change', () => table.page.len(Number(get('PageLength').value)).draw());
        get('Reset').addEventListener('click', () => {
            clearTimeout(searchTimer);
            search.value = '';
            clear.style.display = 'none';
            $('#' + p + 'Status, #' + p + 'Period').val('all').trigger('change.select2');
            $(get('PageLength')).val('10').trigger('change.select2');
            selected.clear();
            table.search('').order(order).page.len(10).ajax.reload(null, true);
        });
        $(tableElement).on('change', '.' + rowClass, function () {
            const id = Number(this.value);
            if (this.checked && !selected.has(id) && selected.size >= 100) {
                this.checked = false;
                notify('error', 'Maksimal 100 data dapat dipilih sekaligus.');
                return;
            }
            if (this.checked) selected.add(id);
            else selected.delete(id);
            refreshSelection();
        });
        checkAll.addEventListener('change', () => {
            let limited = false;
            tableElement.querySelectorAll('.' + rowClass).forEach(input => {
                const id = Number(input.value);
                if (!checkAll.checked) selected.delete(id);
                else if (!selected.has(id)) {
                    if (selected.size >= 100) limited = true;
                    else selected.add(id);
                }
            });
            refreshSelection();
            if (limited) notify('error', 'Maksimal 100 data dapat dipilih sekaligus.');
        });
        get('CancelSelection').addEventListener('click', () => {
            selected.clear();
            refreshSelection();
        });
        get('BulkDelete').addEventListener('click', () => {
            if (!selected.size || deleting) return;
            activeBulk = {type, entity, table, selected, refreshSelection};
            document.getElementById('bulkDeleteCalendarTitle').textContent = `Hapus ${entity.toLowerCase()} terpilih?`;
            document.getElementById('bulkDeleteCalendarDescription').textContent =
                `${selected.size} ${entity.toLowerCase()} terpilih akan dihapus permanen. Data yang sudah dihapus tidak dapat dikembalikan.`;
            modal.classList.add('is-open');
        });
    });
    ['closeBulkDeleteCalendar', 'cancelBulkDeleteCalendar'].forEach(id => {
        document.getElementById(id).addEventListener('click', () => {
            if (!deleting) modal.classList.remove('is-open');
        });
    });
    confirmButton.addEventListener('click', async () => {
        if (deleting || !activeBulk || !activeBulk.selected.size) return;
        const context = activeBulk;
        deleting = true;
        confirmButton.disabled = true;
        const original = confirmButton.innerHTML;
        confirmButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Menghapus...';
        const payload = new FormData();
        payload.append('type', context.type);
        context.selected.forEach(id => payload.append('ids[]', id));
        try {
            const response = await fetch(confirmButton.dataset.url, {
                method: 'POST', headers: {'Accept': 'application/json', 'X-CSRF-TOKEN': confirmButton.dataset.csrf}, body: payload
            });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.message || 'Gagal menghapus data kalender.');
            context.selected.clear();
            context.refreshSelection();
            modal.classList.remove('is-open');
            context.table.ajax.reload(() => {
                if (!context.table.rows().count() && context.table.page.info().recordsFiltered > 0) {
                    context.table.page('first').draw('page');
                }
            }, false);
            notify('success', result.message);
        } catch (error) {
            notify('error', error.message);
        } finally {
            deleting = false;
            confirmButton.disabled = false;
            confirmButton.innerHTML = original;
        }
    });
});

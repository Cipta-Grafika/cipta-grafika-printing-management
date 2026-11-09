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
    const textColumn = (field, icon, labels = {}) => ({
        data: field, className: 'text-center',
        render: (value, type) => type === 'display' ? chip(icon, labels[value] ?? value) : value
    });
    const nameColumn = (field, icon, badge) => ({
        data: field, render: (value, type, row) => {
            if (type !== 'display') return value;
            const note = row.description ? `<span style="font-size:11px;color:var(--t-muted);max-width:260px;white-space:normal;">${escapeHtml(row.description)}</span>` : '';
            return `<div class="cat-cell-name-box"><div class="cat-icon-symbol"><i class="bi ${icon}"></i></div>
                <div class="cat-name-details"><span class="cat-name-primary">${escapeHtml(value)}</span>
                <span class="cat-name-badge">${escapeHtml(badge)}</span>${note}</div></div>`;
        }
    });
    const moneyColumn = {
        data: 'price', className: 'text-end', render: (value, type) => type === 'display' ?
            `<span style="font-weight:600;">${escapeHtml(formatRupiahDisplay(value))}</span>` : value
    };
    const qtyColumn = (field, unlimited = false) => ({
        data: field, className: 'text-center', render: (value, type) => type === 'display' ?
            chip(unlimited && value == null ? 'bi-infinity' : 'bi-stack',
                unlimited && value == null ? 'Tak Terbatas' : `${number(value)} pcs`) : value
    });
    const columns = {
        lanyard: [
            textColumn('width_type', 'bi-rulers', {'2cm': '2.0 cm', '2.5cm': '2.5 cm'}),
            textColumn('package_type', 'bi-box-seam', {tali: 'Hanya Tali', set: '1 Set (Holder + PVC)'}),
            nameColumn('tier_label', 'bi-award', 'Tier Lanyard'),
            qtyColumn('min_qty'), qtyColumn('max_qty', true), moneyColumn
        ],
        nametag: [
            nameColumn('name', 'bi-person-badge', 'Papan Nama'),
            textColumn('type_code', 'bi-tag-fill', {biasa: 'Akrilik Biasa', resin: 'Resin Cembung', resin_emas: 'Resin Emas'}),
            textColumn('fastener_options', 'bi-pin'),
            qtyColumn('min_order_qty'), moneyColumn
        ],
        plakat: [
            nameColumn('name', 'bi-trophy', 'Plakat'),
            textColumn('material_type', 'bi-tag-fill', {akrilik: 'Akrilik UV', kayu: 'Kayu Solid'}),
            textColumn('thickness', 'bi-rulers'),
            textColumn('base_type', 'bi-box', {akrilik: 'Akrilik', hitam: 'Hitam', kayu: 'Kayu'}),
            textColumn('box_type', 'bi-gift', {tanpa_box: 'Tanpa Box', box: 'Box Beludru'}),
            textColumn('dimensions', 'bi-aspect-ratio'), moneyColumn
        ],
        nota: [
            nameColumn('name', 'bi-journal-text', 'Buku Nota'),
            textColumn('paper_type', 'bi-file-earmark-text', {ncr: 'NCR Tembus', hvs: 'HVS Putih'}),
            {data: 'ply_count', className: 'text-center', render: (value, type, row) => type === 'display' ?
                chip('bi-stack', row.ply_label) : value},
            {data: 'ink_count', className: 'text-center', render: (value, type) => type === 'display' ?
                chip('bi-palette', `${number(value)} Tinta`) : value},
            {data: 'sheets_per_book', className: 'text-center', render: (value, type, row) => type === 'display' ?
                chip('bi-book', `${number(value)} ${row.paper_type === 'ncr' ? 'Set/Buku' : 'Lbr/Buku'}`) : value},
            moneyColumn
        ]
    };
    const handlers = {
        lanyard: {detail: openDetailLanyardModal, edit: openEditLanyardModal, delete: confirmDeleteLanyard},
        nametag: {detail: openDetailNameTagModal, edit: openEditNameTagModal, delete: confirmDeleteNameTag},
        plakat: {detail: openDetailPlakatModal, edit: openEditPlakatModal, delete: confirmDeletePlakat},
        nota: {detail: openDetailNotaModal, edit: openEditNotaModal, delete: confirmDeleteNota}
    };
    const modal = document.getElementById('modalBulkDeleteMerchandise');
    const confirmButton = document.getElementById('confirmBulkDeleteMerchandise');
    let activeBulk = null;
    let deleting = false;
    window.merchTables = {};
    window.merchSelections = {};

    document.querySelectorAll('[data-merchandise-type]').forEach(card => {
        const {merchandiseType: type, prefix: p, entity, nameField, url, defaultOrder, badge, stat, unit} = card.dataset;
        const get = suffix => document.getElementById(p + suffix);
        const tableElement = get('Table');
        const wrap = tableElement.closest('.cat-table-wrap');
        const selected = new Set();
        window.merchSelections[type] = selected;
        const rowClass = p + '-row-check';
        const checkAll = get('CheckAll');
        const search = get('Search');
        const clear = get('ClearSearch');
        const order = [[Number(defaultOrder), 'asc']];
        const filters = ['Variant', 'Status', 'Period'];
        const filterSelector = filters.map(suffix => '#' + p + suffix).join(', ');
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
        $(filterSelector).select2({width: '100%', minimumResultsForSearch: -1});
        $(get('PageLength')).select2({width: '76px', minimumResultsForSearch: -1});
        $(tableElement).on('processing.dt column-sizing.dt', centerProcessing);
        wrap.addEventListener('scroll', centerProcessing, {passive: true});
        window.addEventListener('resize', centerProcessing);
        const table = new DataTable(tableElement, {
            processing: true, serverSide: true, responsive: false, order,
            ajax: {url, type: 'GET', data: data => {
                data.variant = get('Variant').value;
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
        window.merchTables[type] = table;
        centerProcessing();
        table.on('draw', () => {
            const total = table.page.info().recordsTotal;
            get('TotalCount').textContent = `${number(total)} Total ${entity}`;
            document.getElementById(badge).textContent = total;
            document.getElementById(stat).textContent = `${number(total)} ${unit}`;
            refreshSelection();
            centerProcessing();
        });
        $(tableElement).on('click', '.btn-detail-cal, .btn-edit-cal, .btn-delete-cal', function () {
            const row = table.row(this.closest('tr')).data();
            if (this.classList.contains('btn-detail-cal')) handlers[type].detail(row);
            else if (this.classList.contains('btn-edit-cal')) handlers[type].edit(row);
            else handlers[type].delete(Number(row.id), row[nameField]);
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
        $(filterSelector).on('change', () => table.ajax.reload(null, true));
        $(get('PageLength')).on('change', () => table.page.len(Number(get('PageLength').value)).draw());
        get('Reset').addEventListener('click', () => {
            clearTimeout(searchTimer);
            search.value = '';
            clear.style.display = 'none';
            $(filterSelector).val('all').trigger('change.select2');
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
            document.getElementById('bulkDeleteMerchandiseTitle').textContent = `Hapus ${entity.toLowerCase()} terpilih?`;
            document.getElementById('bulkDeleteMerchandiseDescription').textContent =
                `${selected.size} ${entity.toLowerCase()} terpilih akan dihapus permanen. Data yang sudah dihapus tidak dapat dikembalikan.`;
            modal.classList.add('is-open');
        });
    });
    ['closeBulkDeleteMerchandise', 'cancelBulkDeleteMerchandise'].forEach(id => {
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
            if (!response.ok || !result.success) throw new Error(result.message || 'Gagal menghapus data merchandise.');
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

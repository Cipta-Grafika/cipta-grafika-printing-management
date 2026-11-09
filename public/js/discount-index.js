const selectedDiscountIds = new Set();

function updateDiscountSelection() {
    const toolbar = document.getElementById('discountBulkToolbar');
    const checkAll = document.getElementById('checkAllDiscounts');
    const rows = [...document.querySelectorAll('.discount-row-check')];
    toolbar.style.display = selectedDiscountIds.size ? 'flex' : 'none';
    document.getElementById('discountBulkCount').textContent = `${selectedDiscountIds.size} terpilih`;
    rows.forEach(input => {
        input.checked = selectedDiscountIds.has(Number(input.value));
        input.closest('tr').classList.toggle('is-selected', input.checked);
    });
    const checked = rows.filter(input => input.checked).length;
    checkAll.checked = rows.length > 0 && checked === rows.length;
    checkAll.indeterminate = checked > 0 && checked < rows.length;
    checkAll.closest('label').classList.toggle('is-indeterminate', checkAll.indeterminate);
}

document.addEventListener('DOMContentLoaded', function () {
    const tableElement = document.getElementById('discountTable');
    const search = document.getElementById('discountSearch');
    const clearSearch = document.getElementById('clearDiscountSearch');
    const creator = document.getElementById('discountCreator');
    const period = document.getElementById('discountPeriod');
    const length = document.getElementById('discountPageLength');
    let searchTimer;

    discountTable.on('draw', function () {
        document.getElementById('discountTotalCount').textContent = `${discountTable.page.info().recordsTotal.toLocaleString('id-ID')} Total Diskon`;
        updateDiscountSelection();
    });

    search.addEventListener('input', function () {
        clearSearch.style.display = this.value ? 'flex' : 'none';
        clearTimeout(searchTimer);
        selectedDiscountIds.clear();
        updateDiscountSelection();
        searchTimer = setTimeout(() => {
            discountTable.search(search.value.trim()).draw();
        }, 250);
    });
    clearSearch.addEventListener('click', function () {
        clearTimeout(searchTimer);
        search.value = '';
        clearSearch.style.display = 'none';
        selectedDiscountIds.clear();
        updateDiscountSelection();
        discountTable.search('').draw();
        search.focus();
    });

    if (typeof $.fn.select2 !== 'undefined') {
        $(creator).select2({ minimumResultsForSearch: 6, width: '100%' });
        $(period).select2({ minimumResultsForSearch: -1, width: '100%' });
        $(length).select2({ minimumResultsForSearch: -1, width: '76px' });
        $(creator).add(period).on('change', function () {
            selectedDiscountIds.clear();
            updateDiscountSelection();
            discountTable.ajax.reload();
        }).on('select2:open', function () {
            $(this).closest('.cat-select-box').addClass('is-open');
        }).on('select2:close', function () {
            $(this).closest('.cat-select-box').removeClass('is-open');
        });
        $(length).on('change', function () { discountTable.page.len(Number(this.value)).draw(); });
    } else {
        [creator, period].forEach(select => select.addEventListener('change', function () {
            selectedDiscountIds.clear();
            updateDiscountSelection();
            discountTable.ajax.reload();
        }));
        length.addEventListener('change', function () { discountTable.page.len(Number(this.value)).draw(); });
    }

    document.getElementById('resetDiscountFilters').addEventListener('click', function () {
        clearTimeout(searchTimer);
        search.value = '';
        clearSearch.style.display = 'none';
        creator.value = 'all';
        period.value = 'all';
        length.value = '10';
        if (typeof $.fn.select2 !== 'undefined') $(creator).add(period).add(length).trigger('change.select2');
        selectedDiscountIds.clear();
        updateDiscountSelection();
        discountTable.search('');
        discountTable.page.len(10);
        discountTable.ajax.reload();
    });

    document.getElementById('checkAllDiscounts').addEventListener('change', function () {
        document.querySelectorAll('.discount-row-check').forEach(input => {
            if (this.checked) selectedDiscountIds.add(Number(input.value));
            else selectedDiscountIds.delete(Number(input.value));
        });
        updateDiscountSelection();
    });
    tableElement.addEventListener('change', function (event) {
        if (!event.target.classList.contains('discount-row-check')) return;
        const id = Number(event.target.value);
        if (event.target.checked) selectedDiscountIds.add(id);
        else selectedDiscountIds.delete(id);
        updateDiscountSelection();
    });
    document.getElementById('cancelDiscountSelection').addEventListener('click', function () {
        selectedDiscountIds.clear();
        updateDiscountSelection();
    });

    const modal = document.getElementById('modalBulkDeleteDiscounts');
    const confirmButton = document.getElementById('confirmBulkDeleteDiscounts');
    function closeModal() {
        if (!confirmButton.disabled) modal.classList.remove('is-open');
    }
    document.getElementById('bulkDeleteDiscounts').addEventListener('click', function () {
        if (!selectedDiscountIds.size) return;
        document.getElementById('bulkDeleteDiscountDescription').textContent = `Apakah Anda yakin ingin menghapus ${selectedDiscountIds.size} diskon yang dipilih? Data yang sudah dihapus tidak dapat dikembalikan.`;
        modal.classList.add('is-open');
    });
    document.getElementById('closeBulkDeleteDiscounts').addEventListener('click', closeModal);
    document.getElementById('cancelBulkDeleteDiscounts').addEventListener('click', closeModal);
    modal.addEventListener('click', event => { if (event.target === modal) closeModal(); });
    document.addEventListener('keydown', event => { if (event.key === 'Escape') closeModal(); });
    confirmButton.addEventListener('click', async function () {
        if (!selectedDiscountIds.size) return;
        confirmButton.disabled = true;
        try {
            const response = await fetch(tableElement.dataset.bulkUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ids: Array.from(selectedDiscountIds) })
            });
            const result = await response.json();
            if (!response.ok || !result.success) {
                showToast('error', result.message || 'Gagal menghapus diskon terpilih.');
                return;
            }
            selectedDiscountIds.clear();
            modal.classList.remove('is-open');
            updateDiscountSelection();
            discountTable.ajax.reload(null, false);
            showToast('success', result.message);
        } catch (error) {
            console.error('Gagal menghapus diskon:', error);
            showToast('error', 'Terjadi kesalahan saat menghapus diskon.');
        } finally {
            confirmButton.disabled = false;
        }
    });
});

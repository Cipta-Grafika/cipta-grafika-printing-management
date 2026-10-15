function formatDigitalRupiahInput(input, finalize = false, regroup = false) {
    const value = input.value;
    const candidate = regroup ?
        value.replace(/^[0-9.]+/, integer => integer.replace(/\./g, '')) :
        value;
    const valid = value === '' ||
        /^(?:[0-9]+|[0-9]{1,3}(?:\.[0-9]{3})+)(?:,[0-9]{0,2})?$/.test(candidate);

    input.setCustomValidity(valid ? '' :
        'Gunakan titik untuk ribuan dan koma untuk desimal, maksimal 2 angka desimal.');

    if (!valid || value === '') {
        return value;
    }

    const parts = value.split(',');
    const integer = parts[0].replace(/\./g, '').replace(/^0+(?=\d)/, '');
    const grouped = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    if (finalize) {
        return grouped + ',' + (parts[1] || '').padEnd(2, '0');
    }

    return grouped + (parts.length > 1 ? ',' + parts[1] : '');
}

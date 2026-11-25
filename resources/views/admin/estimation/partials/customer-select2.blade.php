{{-- Select2 Cetakia Customer Infinite Scroll Component with Animated Loading --}}
<style>
    /* Sembunyikan icon clear (X) pada Select2 */
    .select2-container .select2-selection__clear {
        display: none !important;
    }

    /* Reset wrapper bawaan Select2 agar tampilan kartu loading rapi */
    .select2-results__option--load-more,
    .select2-results__option.loading-results {
        padding: 0 !important;
        background: transparent !important;
        cursor: default !important;
    }

    /* Indikator Loading Animasi Halus untuk Select2 Infinite Scroll (Warna Biru / Blue Theme) */
    .select2-infinite-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 10px 14px;
        color: #60a5fa;
        font-size: 13px;
        font-weight: 600;
        background: rgba(59, 130, 246, 0.12);
        border: 1px dashed rgba(59, 130, 246, 0.45);
        border-radius: 8px;
        margin: 6px 8px;
        box-shadow: 0 0 10px rgba(59, 130, 246, 0.15);
        animation: select2PulseAnim 1.6s infinite ease-in-out;
    }
    .select2-spinner-ring {
        width: 16px;
        height: 16px;
        border: 2.5px solid #3b82f6;
        border-right-color: transparent;
        border-radius: 50%;
        display: inline-block;
        box-sizing: border-box;
        animation: select2SpinAnim 0.75s linear infinite;
    }
    @keyframes select2SpinAnim {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @keyframes select2PulseAnim {
        0%, 100% { opacity: 0.85; transform: scale(0.995); }
        50% { opacity: 1; transform: scale(1); }
    }

    /* Tombol Close Popup Konfirmasi Cetakia (Support Light & Dark Theme) */
    #modalCetakiaConfirm #btnCloseConfirmModal,
    #modalCetakiaConfirm .modal-close-btn,
    .btn-modal-close-confirm {
        background: rgba(255, 255, 255, 0.16) !important;
        border: 1px solid rgba(255, 255, 255, 0.28) !important;
        outline: none !important;
        width: 34px !important;
        height: 34px !important;
        border-radius: 8px !important;
        color: #ffffff !important;
        font-size: 15px !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        margin: 0 !important;
        transition: background 0.2s ease, transform 0.15s ease !important;
        box-shadow: none !important;
    }
    #modalCetakiaConfirm #btnCloseConfirmModal:hover,
    #modalCetakiaConfirm .modal-close-btn:hover,
    .btn-modal-close-confirm:hover {
        background: rgba(255, 255, 255, 0.3) !important;
        transform: scale(1.05);
    }
    #modalCetakiaConfirm #btnCloseConfirmModal i,
    #modalCetakiaConfirm .modal-close-btn i,
    .btn-modal-close-confirm i {
        color: #ffffff !important;
        font-size: 15px !important;
        line-height: 1 !important;
        display: inline-block !important;
    }
</style>

<script>
    /**
     * Inisialisasi Select2 dengan AJAX Infinite Scroll & Animasi Loading
     */
    function initCetakiaCustomerSelect2(selector, options = {}) {
        if (typeof $ === 'undefined' || !$.fn.select2) return;

        const $el = $(selector);
        if (!$el.length) return;

        const nameInputId = options.nameInputId || 'customer_name';
        const billToId    = options.billToId || 'bill_to';
        const shipToId    = options.shipToId || 'ship_to';
        const placeholder = options.placeholder || '-- Pilih Pelanggan (Cetakia) --';

        // Destroy instance lama jika sudah ada
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }

        $el.select2({
            width: '100%',
            placeholder: placeholder,
            allowClear: false,
            ajax: {
                url: '{{ route("admin_cetakia_customers_ajax") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        keyword: params.term || '',
                        page: params.page || 1,
                        limit: 50
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    const items = (data.results || []).map(function(item) {
                        return {
                            id: item.id,
                            text: item.text || item.name,
                            name: item.name,
                            customer_code: item.customer_code,
                            bill_to: item.bill_to,
                            ship_to: item.ship_to
                        };
                    });
                    return {
                        results: items,
                        pagination: {
                            more: Boolean(data.pagination && data.pagination.more)
                        }
                    };
                },
                cache: true
            },
            templateResult: function (item) {
                if (item.loading) {
                    return item.text;
                }
                const codeBadge = item.customer_code ? ' <span style="font-size: 11px; opacity: 0.7; font-family: monospace;">(' + item.customer_code + ')</span>' : '';
                return $('<span>' + (item.text || item.name) + codeBadge + '</span>');
            },
            templateSelection: function (item) {
                return item.text || item.name || placeholder;
            },
            language: {
                loadingMore: function () {
                    return '<div class="select2-infinite-loading"><span class="select2-spinner-ring"></span><span>Memuat pelanggan selanjutnya...</span></div>';
                },
                searching: function () {
                    return '<div class="select2-infinite-loading"><span class="select2-spinner-ring"></span><span>Mencari data pelanggan...</span></div>';
                },
                noResults: function () {
                    return '<div style="padding: 10px 14px; color: var(--t-muted, #71717a); font-size: 13px; text-align: center;">Tidak ada data pelanggan yang cocok</div>';
                }
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        // Event listener saat pelanggan dipilih
        $el.on('select2:select', function(e) {
            const item = e.params.data || {};
            const sel = $el.find('option:selected');
            sel.data('name', item.name || item.text);
            sel.data('bill-to', item.bill_to || '');
            sel.data('ship-to', item.ship_to || '');

            const custName = (item.name || item.text || '').trim();
            const cNameInput = document.getElementById(nameInputId);
            if (cNameInput) cNameInput.value = custName;

            const rawBillTo = (item.bill_to || '').toString().trim();
            const rawShipTo = (item.ship_to || '').toString().trim();
            const finalBillTo = rawBillTo || custName;
            const finalShipTo = rawShipTo || finalBillTo;

            $('#' + billToId).val(finalBillTo);
            $('#' + shipToId).val(finalShipTo);

            if (typeof updateQuotationCustomerInfo === 'function') {
                updateQuotationCustomerInfo();
            }
        });

        // Event listener saat dihapus / dibatalkan
        $el.on('select2:clear', function() {
            const cNameInput = document.getElementById(nameInputId);
            if (cNameInput) cNameInput.value = '';
            $('#' + billToId).val('');
            $('#' + shipToId).val('');

            if (typeof updateQuotationCustomerInfo === 'function') {
                updateQuotationCustomerInfo();
            }
        });
    }

    // Global Event Listener: Tutup Modal saat Backdrop di-klik atau tombol ESC ditekan
    if (typeof $ !== 'undefined') {
        $(document).on('click', '#modalCetakiaConfirm, #modalCetakiaSuccess', function(e) {
            if (e.target === this) {
                $(this).removeClass('is-open');
            }
        });

        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' || e.key === 'Esc') {
                $('#modalCetakiaConfirm').removeClass('is-open');
                $('#modalCetakiaSuccess').removeClass('is-open');
            }
        });
    }
</script>

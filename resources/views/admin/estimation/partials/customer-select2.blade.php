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

    /* ==========================================================================
       UNIVERSAL RESPONSIVE ENHANCEMENTS UNTUK SEMUA FORMULIR ESTIMASI
       (Digital Print A3+, Large Format, Offset/Brosur, Kalender, Merchandise)
       ========================================================================== */
    @media (max-width: 1024px) {
        /* Hilangkan horizontal scrollbar pada window / body di tablet & ponsel */
        html,
        body {
            overflow-x: hidden !important;
            max-width: 100vw !important;
            width: 100% !important;
            position: relative;
        }

        .shell,
        .main {
            overflow-x: hidden !important;
            max-width: 100% !important;
            width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }

        /* Susun kolom estimator menjadi vertikal (Form di atas, Summary di bawah) */
        .a3plus-estimator-grid,
        .lf-estimator-grid,
        .offset-estimator-grid,
        .calendar-estimator-grid,
        .merch-estimator-grid {
            grid-template-columns: 1fr !important;
            gap: 20px !important;
            width: 100% !important;
        }

        /* Lepas sticky positioning pada mobile/tablet agar scroll mulus */
        .calendar-sticky-wrap,
        .merch-sticky-col,
        .merch-sticky-card {
            position: static !important;
            top: auto !important;
        }

        .content {
            overflow-x: hidden !important;
            max-width: 100% !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
    }

    @media (max-width: 768px) {
        /* Container dan Layout Utama */
        .shell,
        .main,
        .content {
            max-width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }
        .content {
            padding: 16px 12px 24px !important;
            overflow-x: hidden !important;
        }

        /* Pastikan footer tidak memicu horizontal scroll */
        .d-footer {
            max-width: 100% !important;
            box-sizing: border-box !important;
            overflow-x: hidden !important;
        }

        /* Hero Section Header Form Estimasi */
        .hero {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 12px !important;
            margin-bottom: 18px !important;
        }
        .hero-text {
            min-width: 0 !important;
            width: 100% !important;
        }
        .hero-title {
            font-size: 1.35rem !important;
            line-height: 1.3 !important;
        }
        .hero-sub {
            font-size: 12.5px !important;
        }

        /* Kartu Form Estimasi */
        .card,
        .merch-form-card,
        .merch-sticky-card,
        .merch-matrix-card,
        .calendar-form-card,
        .a3plus-form-col .card,
        .lf-form-col .card,
        .offset-form-col .card {
            max-width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
            border-radius: 12px !important;
        }

        /* Header Kartu & Eyebrow: Mencegah Teks Terpotong di Layar Kecil */
        .card-head {
            padding: 14px 16px !important;
            flex-wrap: wrap !important;
            gap: 10px !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }
        .card-head > :last-child,
        .card-title-wrap {
            flex-shrink: 1 !important;
            min-width: 0 !important;
            max-width: 100% !important;
        }
        .card-head .eyebrow,
        .card-title-wrap .eyebrow,
        .form-section-head .eyebrow,
        .hero .eyebrow,
        .eyebrow {
            white-space: normal !important;
            word-break: break-word !important;
            overflow-wrap: break-word !important;
            letter-spacing: 0.05em !important;
            line-height: 1.4 !important;
            display: block !important;
            max-width: 100% !important;
            font-size: 11px !important;
        }

        /* Padding Responsif pada Bagian Formulir & Tab Panel */
        .form-section,
        .tab-panel,
        .card-body,
        .card-foot {
            padding-left: 14px !important;
            padding-right: 14px !important;
            box-sizing: border-box !important;
        }
        .form-section {
            padding-top: 16px !important;
            padding-bottom: 16px !important;
        }

        /* Elemen Input & Select2 */
        .field,
        .input,
        .select,
        .textarea,
        .select2-container {
            max-width: 100% !important;
            width: 100% !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }
        .select2-container .select2-selection--single {
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        .select2-container .select2-selection__rendered {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            max-width: calc(100vw - 80px) !important;
        }

        /* Cegah flex label & button penagihan keluar batas */
        .field > div[style*="display: flex"] {
            flex-wrap: wrap !important;
            gap: 6px !important;
        }

        /* Cegah toast notifikasi memicu horizontal scroll */
        .wa-toast-container,
        .toast-container,
        #toastContainer {
            overflow: hidden !important;
            max-width: 100vw !important;
        }

        /* Tombol & Aksi Form */
        .form-actions,
        .card-foot-actions,
        .btn-group-responsive {
            flex-direction: column !important;
            width: 100% !important;
            gap: 8px !important;
        }
        .form-actions .btn,
        .form-actions button,
        .btn-submit-main {
            width: 100% !important;
            justify-content: center !important;
        }
        .btn-copy-matrix,
        .btn-copy-matrix-wa {
            max-width: 100% !important;
            white-space: normal !important;
            word-break: break-word !important;
        }

        /* Tabel Matriks / Pricelist Scroll Horizontal Halus */
        .matrix-table-wrap,
        .table-responsive,
        .table-scroll,
        .details-digital-table-wrapper,
        .dual-engine-table-wrap,
        .merch-matrix-table-wrap,
        .lf-matrix-table-wrap,
        .a3plus-matrix-table-wrap,
        .offset-matrix-table-wrap,
        .calendar-matrix-table-wrap {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            display: block !important;
            box-sizing: border-box !important;
        }

        /* Toolbar Pencarian Matriks Harga */
        .matrix-toolbar-wrap {
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 10px !important;
            width: 100% !important;
        }
        .matrix-search-box {
            min-width: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }

        /* Tab Navigasi Merchandise */
        .merch-tabs-nav {
            gap: 8px !important;
        }
        .merch-tab-btn {
            padding: 9px 12px !important;
            font-size: 12px !important;
            border-radius: 8px !important;
            flex: 1 1 calc(50% - 8px);
            min-width: 135px;
            box-sizing: border-box;
        }
    }

    @media (max-width: 640px) {
        /* Form Grid Menjadi 1 Kolom Rapi di Smartphone */
        .form-grid,
        [style*="grid-template-columns"],
        .choice-cards-grid,
        .cal-type-grid {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
        }

        /* Pilihan Radio & Pill Option */
        .choice-pills,
        .laminate-mode-pills,
        .radio-pill-group {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
        }
        .choice-pill,
        .radio-pill {
            flex: 1 1 auto !important;
            text-align: center !important;
        }

        /* Tombol Cepat Masa Berlaku Penawaran */
        .quote-preset-buttons {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 6px !important;
        }
        .quote-preset-btn {
            flex: 1 1 auto !important;
        }

        /* Tombol Salin Alamat */
        .btn-copy-address {
            white-space: normal !important;
            word-break: break-word !important;
            font-size: 11px !important;
        }

        /* Modal Dialog Konfirmasi Cetakia */
        .cetakia-confirm-modal-box,
        #modalCetakiaConfirm .modal-dialog,
        #modalCetakiaConfirm > div {
            max-width: calc(100vw - 20px) !important;
            margin: 10px !important;
            width: 100% !important;
        }
    }

    @media (max-width: 420px) {
        .content {
            padding: 12px 8px 20px !important;
        }
        .card-head {
            padding: 12px 12px !important;
        }
        .form-section,
        .tab-panel,
        .card-body {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }
        .merch-tab-btn {
            flex: 1 1 100% !important;
            width: 100% !important;
        }
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

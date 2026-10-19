document.addEventListener('DOMContentLoaded', function() {
        const previewUrl = "{{ route('user_preview_a3plus_estimation') }}";
        const getMaterialsUrl = "{{ route('user_get_materials_a3plus') }}";
        const csrfToken = @json(csrf_token());
        const whatsappNumber = '6285212821022';

        let appliedPromoCode = '';
        let previewRequest = null;
        let previewTimer = null;
        let latestEstimationMessage = '';

        function formatRupiah(num) {
            const n = Number(num) || 0;
            return 'Rp ' + Math.round(n).toLocaleString('id-ID');
        }

        /* =====================================================================
           PILL BUTTON GENERATOR (SAMA SEPERTI LARGE FORMAT)
           ===================================================================== */
        function setupChoiceButtons(select) {
            const element = select[0];
            if (!element) return;

            const label = element.parentElement ? element.parentElement.querySelector('label') : null;
            const labelId = `a3-choice-label-${element.id}`;
            if (label) {
                label.id = labelId;
            }

            const buttonGroup = document.createElement('div');
            buttonGroup.className = 'estimator-choice-buttons';
            buttonGroup.setAttribute('role', 'radiogroup');
            if (label) {
                buttonGroup.setAttribute('aria-labelledby', labelId);
            }

            element.insertAdjacentElement('afterend', buttonGroup);

            function renderButtons() {
                const selectedValue = select.val();
                buttonGroup.replaceChildren();

                Array.from(element.options)
                    .filter(option => option.value !== '')
                    .forEach(option => {
                        const button = document.createElement('button');
                        const isSelected = option.value === selectedValue;

                        button.type = 'button';
                        button.className = 'estimator-choice-button';
                        button.textContent = option.text.trim();
                        button.dataset.value = option.value;
                        button.setAttribute('role', 'radio');
                        button.setAttribute('aria-checked', String(isSelected));
                        button.disabled = option.disabled;

                        button.addEventListener('click', function() {
                            select.val(option.value).trigger('change');
                        });

                        buttonGroup.appendChild(button);
                    });

                buttonGroup.hidden = buttonGroup.childElementCount === 0;
            }

            select.on('change', renderButtons);
            new MutationObserver(renderButtons).observe(element, {
                childList: true
            });

            element.classList.add('estimator-choice-source');
            element.setAttribute('aria-hidden', 'true');
            element.tabIndex = -1;
            renderButtons();
        }

        const modeSelect = $('#a3_production_mode');
        const colorSelect = $('#a3_print_color');
        const sideSelect = $('#a3_print_side');
        const materialSelect = $('#a3_material_id');
        const laminationSelect = $('#a3_lamination_type');
        const cuttingSelect = $('#a3_cutting_type');
        const potongOverrideSelect = $('#a3_potong_size_override');

        [
            modeSelect,
            colorSelect,
            sideSelect,
            materialSelect,
            laminationSelect,
            cuttingSelect,
            potongOverrideSelect
        ].forEach(setupChoiceButtons);

        /* =====================================================================
           IMPOSISI REALTIME (UNTUK MODE LAYOUT)
           ===================================================================== */
        function updateLiveImposition() {
            const mode = modeSelect.val();
            if (mode !== 'layout') return;

            const length = parseFloat($('#a3_length').val()) || 9.0;
            const width = parseFloat($('#a3_width').val()) || 5.5;
            const qty = parseInt($('#a3_qty').val()) || 1;

            const sheetL = 47.5;
            const sheetW = 31.5;

            const upNormal = Math.floor(sheetL / length) * Math.floor(sheetW / width);
            const upRotated = Math.floor(sheetL / width) * Math.floor(sheetW / length);
            const upCount = Math.max(1, Math.max(upNormal, upRotated));
            const orientation = upRotated > upNormal ? 'Rotated' : 'Normal';
            const totalSheets = Math.ceil(qty / upCount);

            $('#a3ImpositionUp').text(`${upCount} pcs (${orientation})`);
            $('#a3ImpositionSheets').text(`${totalSheets.toLocaleString('id-ID')} Lembar`);
        }

        /* =====================================================================
           MODE & CUTTING VISIBILITY TOGGLES
           ===================================================================== */
        modeSelect.on('change', function() {
            const mode = $(this).val();
            if (mode === 'direct') {
                $('#a3LayoutContainer').hide();
                $('#a3DirectContainer').show();
            } else {
                $('#a3LayoutContainer').show();
                $('#a3DirectContainer').hide();
                updateLiveImposition();
            }
            scheduleEstimation();
        });

        cuttingSelect.on('change', function() {
            if ($(this).val() === 'potong') {
                $('#a3PotongOverrideField').show();
            } else {
                $('#a3PotongOverrideField').hide();
                potongOverrideSelect.val('').trigger('change');
            }
            scheduleEstimation();
        });

        /* =====================================================================
           WARNA CETAK -> RELOAD MATERIAL
           ===================================================================== */
        colorSelect.on('change', function() {
            const selectedColor = $(this).val();
            const categoryId = $('#a3_category_id').val();

            $.ajax({
                url: getMaterialsUrl,
                type: 'GET',
                data: {
                    print_color: selectedColor,
                    category_id: categoryId
                },
                success: function(res) {
                    if (res.success && Array.isArray(res.data)) {
                        materialSelect.empty();
                        res.data.forEach(function(m, idx) {
                            materialSelect.append(new Option(m.name, m.id, idx === 0, idx === 0));
                        });
                        materialSelect.trigger('change');
                    }
                }
            });
        });

        /* =====================================================================
           INPUT LISTENERS (RECALCULATION)
           ===================================================================== */
        $('#a3_length, #a3_width, #a3_qty').on('input change', function() {
            updateLiveImposition();
            scheduleEstimation();
        });

        $('#a3_sheet_qty, #a3_print_side, #a3_material_id, #a3_lamination_type, #a3_potong_size_override')
            .on('change', function() {
                scheduleEstimation();
            });

        function scheduleEstimation() {
            clearTimeout(previewTimer);
            previewTimer = setTimeout(fetchA3Estimation, 300);
        }

        /* =====================================================================
           PROMO CODE HANDLERS
           ===================================================================== */
        $('#a3ApplyPromoCode').on('click', function() {
            const code = $('#a3_promo_code').val().trim().toUpperCase();
            if (!code) {
                setPromoStatus('Masukkan kode promo terlebih dahulu.', true);
                return;
            }
            appliedPromoCode = code;
            $('#a3_promo_code').val(code);
            $('#a3ApplyPromoCode').hide();
            $('#a3RemovePromoCode').show();
            setPromoStatus('Menerapkan kode promo ' + code + '...', false);
            fetchA3Estimation();
        });

        $('#a3RemovePromoCode').on('click', function() {
            appliedPromoCode = '';
            $('#a3_promo_code').val('');
            $('#a3RemovePromoCode').hide();
            $('#a3ApplyPromoCode').show();
            setPromoStatus('Kode promo dihapus.', false);
            fetchA3Estimation();
        });

        function setPromoStatus(msg, isError) {
            $('#a3PromoCodeStatus').text(msg)
                .toggleClass('text-red-600 dark:text-red-400', isError)
                .toggleClass('text-emerald-600 dark:text-emerald-400', !isError && appliedPromoCode);
        }

        /* =====================================================================
           AJAX ESTIMATION PREVIEW
           ===================================================================== */
        function fetchA3Estimation() {
            const materialId = materialSelect.val();
            if (!materialId) {
                $('#a3EstimateStatus').text('Pilih material terlebih dahulu.').show();
                $('#a3EstimateBreakdown').hide();
                $('#a3EstimateGrandTotal').text('—');
                $('#a3EstimatePerPcs').text('');
                $('#a3ContinueOrderButton').prop('disabled', true);
                return;
            }

            const payload = {
                _token: csrfToken,
                production_mode: modeSelect.val(),
                print_color: colorSelect.val(),
                print_side: sideSelect.val(),
                material_id: materialId,
                length: $('#a3_length').val(),
                width: $('#a3_width').val(),
                qty: $('#a3_qty').val(),
                sheet_qty: $('#a3_sheet_qty').val(),
                lamination_type: laminationSelect.val(),
                cutting_type: cuttingSelect.val(),
                potong_size_override: potongOverrideSelect.val(),
                promo_code: appliedPromoCode || null,
                category_id: $('#a3_category_id').val()
            };

            if (previewRequest) {
                previewRequest.abort();
            }

            $('#a3EstimateStatus').text('Menghitung estimasi...').show();

            previewRequest = $.ajax({
                url: previewUrl,
                type: 'POST',
                data: payload,
                success: function(res) {
                    if (!res.success || !res.data) {
                        $('#a3EstimateStatus').text(res.message || 'Gagal menghitung estimasi.').show();
                        $('#a3EstimateBreakdown').hide();
                        $('#a3ContinueOrderButton').prop('disabled', true);
                        return;
                    }

                    const d = res.data;
                    $('#a3EstimateStatus').hide();
                    $('#a3EstimateBreakdown').show();

                    // Cetak row
                    $('#a3EstimatePrintMeta').text(`${d.total_sheets} lembar @ ${formatRupiah(d.unit_price_sheet)} (${d.color_side})`);
                    $('#a3EstimatePrintAmount').text(formatRupiah(d.print_subtotal));

                    // Laminasi row
                    if (d.lamination_subtotal > 0) {
                        $('#a3EstimateLaminationRow').show();
                        $('#a3EstimateLaminationLabel').text(d.lamination_name);
                        $('#a3EstimateLaminationMeta').text(`${d.total_sheets} lembar`);
                        $('#a3EstimateLaminationAmount').text(formatRupiah(d.lamination_subtotal));
                    } else {
                        $('#a3EstimateLaminationRow').hide();
                    }

                    // Cutting row
                    if (d.cutting_subtotal > 0) {
                        $('#a3EstimateCuttingRow').show();
                        $('#a3EstimateCuttingLabel').text(d.cutting_name);
                        $('#a3EstimateCuttingMeta').text(d.production_mode === 'Direct Sheet' ? `${d.total_sheets} lembar` : `${d.qty} pcs`);
                        $('#a3EstimateCuttingAmount').text(formatRupiah(d.cutting_subtotal));
                    } else {
                        $('#a3EstimateCuttingRow').hide();
                    }

                    // Diskon row
                    if (d.promo && d.discount_amount > 0) {
                        $('#a3EstimateDiscountRow').show();
                        $('#a3EstimateDiscountLabel').text(`Diskon Promo (${d.promo.code} - ${d.promo.percentage}%)`);
                        $('#a3EstimateDiscountAmount').text(`- ${formatRupiah(d.discount_amount)}`);
                        setPromoStatus(`Kode promo ${d.promo.code} (${d.promo.percentage}%) berhasil diterapkan.`, false);
                    } else {
                        $('#a3EstimateDiscountRow').hide();
                        if (appliedPromoCode) {
                            setPromoStatus('Kode promo diterapkan.', false);
                        }
                    }

                    // Rounding row
                    if (d.rounding_amount > 0) {
                        $('#a3EstimateRoundingRow').show();
                        $('#a3EstimateRoundingAmount').text(`+ ${formatRupiah(d.rounding_amount)}`);
                    } else {
                        $('#a3EstimateRoundingRow').hide();
                    }

                    // Grand total
                    $('#a3EstimateGrandTotal').text(formatRupiah(d.grand_total));

                    // Price per pcs
                    if (d.production_mode !== 'Direct Sheet' && d.price_per_pcs > 0) {
                        $('#a3EstimatePerPcs').text(`${formatRupiah(d.price_per_pcs)} / pcs`);
                    } else {
                        $('#a3EstimatePerPcs').text('');
                    }

                    // WhatsApp Order Text
                    const orderLines = [
                        'Halo Cipta Grafika, saya ingin memesan cetak digital:',
                        '',
                        'Produk: ' + (d.category_name || 'Digital Print A3+'),
                        'Mesin: Digital Print A3+',
                        'Warna: ' + (d.color_side || d.print_color),
                        'Material: ' + (d.material_name || '-'),
                        d.production_mode === 'Direct Sheet'
                            ? ('Jumlah: ' + (d.total_sheets || 0) + ' lembar A3+ (Direct Sheet)')
                            : ('Ukuran Jadi: ' + (d.finished_size || '-') + '\nJumlah: ' + (d.qty || 0).toLocaleString('id-ID') + ' pcs (' + (d.total_sheets || 0) + ' lembar A3+ / ' + (d.imposition_info || '-') + ')'),
                        d.lamination_name && d.lamination_name !== 'Tanpa Laminasi' ? ('Laminasi: ' + d.lamination_name) : null,
                        d.cutting_name && d.cutting_name !== 'Tanpa Cutting' ? ('Cutting: ' + d.cutting_name) : null,
                    ].filter(Boolean);

                    if (d.promo && d.discount_amount > 0) {
                        orderLines.push('Promo: ' + d.promo.code + ' (' + d.promo.percentage + '%)');
                        orderLines.push('Diskon: -' + formatRupiah(d.discount_amount));
                    }
                    if (d.rounding_amount > 0) {
                        orderLines.push('Pembulatan ke atas (Rp 1.000): +' + formatRupiah(d.rounding_amount));
                    }

                    orderLines.push(
                        '',
                        'Total Estimasi: ' + formatRupiah(d.grand_total) + (d.price_per_pcs > 0 ? ' (' + formatRupiah(d.price_per_pcs) + ' / pcs)' : ''),
                        'Estimasi Waktu: ' + (d.lead_time || '1 hari kerja'),
                        '',
                        'Mohon info proses selanjutnya. Terima kasih.'
                    );

                    latestEstimationMessage = orderLines.join('\n');
                    $('#a3ContinueOrderButton').prop('disabled', false);
                },
                error: function(xhr) {
                    if (xhr.statusText === 'abort') return;
                    let errorMsg = 'Gagal menghitung estimasi.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat().join(' ');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }

                    if (appliedPromoCode && errorMsg.toLowerCase().includes('promo')) {
                        setPromoStatus(errorMsg, true);
                    } else {
                        $('#a3EstimateStatus').text(errorMsg).show();
                    }
                    $('#a3ContinueOrderButton').prop('disabled', true);
                }
            });
        }

        /* =====================================================================
           CONTINUE TO WHATSAPP
           ===================================================================== */
        $('#a3ContinueOrderButton').on('click', function() {
            if ($(this).prop('disabled') || !latestEstimationMessage) return;
            const waUrl = 'https://wa.me/' + whatsappNumber + '?text=' + encodeURIComponent(latestEstimationMessage);
            window.open(waUrl, '_blank', 'noopener,noreferrer');
        });

        // Inisialisasi awal
        updateLiveImposition();
        fetchA3Estimation();
    });

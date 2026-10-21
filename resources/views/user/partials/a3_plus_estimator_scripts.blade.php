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
           IMPOSISI REALTIME (UNTUK MODE LAYOUT) & SUMMARY PAKET DISPLAY
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

        function updateDisplayPackageSummary() {
            const isDisplay = $('#a3_is_display').val() === '1';
            if (!isDisplay) return;

            const boxCount = parseInt($('#a3_display_qty').val()) || 1;
            const basePkgQty = parseInt(@js($estimator['packageQty'] ?? 100)) || 100;
            const baseSheetQty = parseInt(@js($estimator['sheetQty'] ?? 4)) || 4;

            const totalPcs = boxCount * basePkgQty;
            const totalSheets = boxCount * baseSheetQty;

            $('#a3DisplayPackagePcs').text(`${totalPcs.toLocaleString('id-ID')} pcs`);
            $('#a3DisplayTotalSheets').text(`${totalSheets.toLocaleString('id-ID')} Lembar A3+`);
        }

        /* =====================================================================
           MODE & CUTTING VISIBILITY TOGGLES
           ===================================================================== */
        modeSelect.on('change', function() {
            const mode = $(this).val();
            if (mode === 'direct') {
                $('#a3LayoutContainer').hide();
                $('#a3DirectContainer').show();
            } else if (mode === 'layout') {
                $('#a3LayoutContainer').show();
                $('#a3DirectContainer').hide();
                updateLiveImposition();
            } else {
                $('#a3LayoutContainer').hide();
                $('#a3DirectContainer').hide();
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

            if (!selectedColor) {
                materialSelect.empty();
                materialSelect.append(new Option('Pilih material...', ''));
                materialSelect.val('').trigger('change');
                return;
            }

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
                        materialSelect.append(new Option('Pilih material...', ''));
                        res.data.forEach(function(m) {
                            materialSelect.append(new Option(m.name, m.id));
                        });
                        materialSelect.val('').trigger('change');
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

        $('#a3_display_qty').on('input change', function() {
            updateDisplayPackageSummary();
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
           PROMO CODE HANDLERS (KONSISTEN DENGAN LARGE FORMAT)
           ===================================================================== */
        const promoCodeInput = $('#a3_promo_code');

        promoCodeInput.on('input', function() {
            const start = this.selectionStart;
            const end = this.selectionEnd;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(start, end);
            appliedPromoCode = '';
            if (this.value) {
                $('#a3RemovePromoCode').removeAttr('hidden').prop('hidden', false).show();
            } else {
                $('#a3RemovePromoCode').attr('hidden', '').prop('hidden', true).hide();
            }
            setPromoStatus('Klik Terapkan untuk memeriksa kode promo.', false);
            scheduleEstimation();
        });

        $('#a3ApplyPromoCode').on('click', function() {
            const code = promoCodeInput.val().trim().toUpperCase();
            if (!/^[A-Z0-9_-]{3,30}$/.test(code)) {
                setPromoStatus('Masukkan kode promo 3–30 karakter: huruf, angka, - atau _, tanpa spasi.', true);
                promoCodeInput.trigger('focus');
                return;
            }

            promoCodeInput.val(code);
            appliedPromoCode = code;
            $('#a3RemovePromoCode').removeAttr('hidden').prop('hidden', false).show();
            setPromoStatus('Memeriksa kode promo...', false);
            fetchA3Estimation();
        });

        promoCodeInput.on('keydown', function(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                $('#a3ApplyPromoCode').trigger('click');
            }
        });

        $('#a3RemovePromoCode').on('click', function() {
            promoCodeInput.val('');
            appliedPromoCode = '';
            $(this).attr('hidden', '').prop('hidden', true).hide();
            setPromoStatus('Kode promo dihapus.', false);
            fetchA3Estimation();
        });

        function setPromoStatus(msg, isError) {
            $('#a3PromoCodeStatus').text(msg)
                .toggleClass('text-red-600 dark:text-red-400', Boolean(isError))
                .toggleClass('text-emerald-600 dark:text-emerald-400', !isError && Boolean(appliedPromoCode));
            promoCodeInput.attr('aria-invalid', isError ? 'true' : 'false');
        }

        /* =====================================================================
           AJAX ESTIMATION PREVIEW
           ===================================================================== */
        function fetchA3Estimation() {
            const isDisplay = $('#a3_is_display').val() === '1';
            const materialId = materialSelect.val();
            const side = sideSelect.val();
            const mode = isDisplay ? 'display' : modeSelect.val();
            const color = isDisplay ? 'fc' : colorSelect.val();

            // Cek kelengkapan input wajib
            if (isDisplay) {
                if (!side || !materialId) {
                    let missing = [];
                    if (!side) missing.push('sisi cetak');
                    if (!materialId) missing.push('varian bahan');
                    $('#a3EstimateStatus')
                        .text('Silakan pilih ' + missing.join(' dan ') + ' untuk melihat estimasi.')
                        .removeClass('text-red-500')
                        .prop('hidden', false).show();
                    $('#a3EstimateBreakdown').prop('hidden', true).hide();
                    $('#a3EstimateGrandTotal').text('—');
                    $('#a3EstimatePerPcs').text('');
                    $('#a3ContinueOrderButton').prop('disabled', true);
                    return;
                }
            } else {
                if (!mode || !color || !side || !materialId) {
                    let missing = [];
                    if (!mode) missing.push('mode produksi');
                    if (!color) missing.push('warna cetak');
                    if (!side) missing.push('sisi cetak');
                    if (!materialId) missing.push('material');
                    $('#a3EstimateStatus')
                        .text('Silakan pilih ' + missing.join(', ') + ' untuk melihat estimasi.')
                        .removeClass('text-red-500')
                        .prop('hidden', false).show();
                    $('#a3EstimateBreakdown').prop('hidden', true).hide();
                    $('#a3EstimateGrandTotal').text('—');
                    $('#a3EstimatePerPcs').text('');
                    $('#a3ContinueOrderButton').prop('disabled', true);
                    return;
                }
            }

            let qty = $('#a3_qty').val();
            if (isDisplay) {
                qty = $('#a3_display_qty').val() || 1;
            }

            const payload = {
                _token: csrfToken,
                production_mode: isDisplay ? 'display' : mode,
                print_color: isDisplay ? 'fc' : color,
                print_side: side,
                material_id: materialId,
                length: isDisplay ? null : $('#a3_length').val(),
                width: isDisplay ? null : $('#a3_width').val(),
                qty: qty,
                sheet_qty: isDisplay ? null : $('#a3_sheet_qty').val(),
                lamination_type: isDisplay ? 'none' : (laminationSelect.val() || 'none'),
                cutting_type: isDisplay ? 'none' : (cuttingSelect.val() || 'none'),
                potong_size_override: isDisplay ? null : potongOverrideSelect.val(),
                promo_code: appliedPromoCode || null,
                category_id: $('#a3_category_id').val(),
                display_product_id: $('#a3_display_product_id').val()
            };

            if (previewRequest) {
                previewRequest.abort();
            }

            $('#a3EstimateStatus')
                .text('Memperbarui estimasi...')
                .removeClass('text-red-500')
                .prop('hidden', false).show();

            previewRequest = $.ajax({
                url: previewUrl,
                type: 'POST',
                data: payload,
                success: function(res) {
                    if (!res.success || !res.data) {
                        $('#a3EstimateStatus')
                            .text(res.message || 'Gagal menghitung estimasi.')
                            .addClass('text-red-500')
                            .prop('hidden', false).show();
                        $('#a3EstimateBreakdown').prop('hidden', true).hide();
                        $('#a3EstimateGrandTotal').text('—');
                        $('#a3EstimatePerPcs').text('');
                        $('#a3ContinueOrderButton').prop('disabled', true);
                        return;
                    }

                    const d = res.data;
                    $('#a3EstimateStatus')
                        .text('Estimasi diperbarui otomatis berdasarkan konfigurasi saat ini.')
                        .removeClass('text-red-500')
                        .prop('hidden', false).show();

                    $('#a3EstimateBreakdown').prop('hidden', false).show();

                    // Cetak / Paket Display row
                    if (isDisplay) {
                        const pkgCount = parseInt(qty) || 1;
                        const unitName = @js($estimator['packageUnit'] ?? 'Box');
                        const unitPrice = d.unit_price_sheet || (pkgCount > 0 ? (d.print_subtotal / pkgCount) : 0);

                        // Label informatif: Nama Produk / Varian Bahan + Sisi Cetak
                        const itemLabel = d.material_name 
                            ? (d.material_name + (d.print_side ? ` · ${d.print_side}` : ''))
                            : (d.production_mode || 'Paket Display');
                        $('#a3EstimatePrintLabel').text(itemLabel);

                        // Meta rincian: Jumlah paket (pcs) x Harga satuan / paket
                        $('#a3EstimatePrintMeta').text(`${pkgCount} ${unitName} (${d.qty} pcs) × ${formatRupiah(unitPrice)} / ${unitName}`);
                        $('#a3EstimatePrintAmount').text(formatRupiah(d.print_subtotal));

                        // Sembunyikan baris laminasi terpisah karena sudah include di paket
                        $('#a3EstimateLaminationRow').prop('hidden', true).hide();
                        $('#a3EstimateCuttingRow').prop('hidden', true).hide();

                        // Baris Finishing Paket Display (Termasuk)
                        if (d.finishing_summary) {
                            $('#a3EstimateDisplayFinishingRow').prop('hidden', false).show();
                            $('#a3EstimateDisplayFinishingLabel').text('Finishing Paket (Termasuk)');
                            $('#a3EstimateDisplayFinishingMeta').text(d.finishing_summary);
                            $('#a3EstimateDisplayFinishingAmount').text('Termasuk');
                        } else {
                            $('#a3EstimateDisplayFinishingRow').prop('hidden', true).hide();
                        }
                    } else {
                        const colorSide = d.color_side || ((d.print_color || 'Full Color') + ' · ' + (d.print_side || '1 Sisi'));
                        $('#a3EstimatePrintLabel').text((d.material_name || 'Cetak Digital A3+') + ` · ${colorSide}`);

                        if (d.production_mode === 'Direct Sheet') {
                            $('#a3EstimatePrintMeta').text(`${d.total_sheets} lembar × ${formatRupiah(d.unit_price_sheet)} / lembar`);
                        } else {
                            const impMeta = d.imposition_info ? ` · ${d.imposition_info}` : '';
                            $('#a3EstimatePrintMeta').text(`${d.qty} pcs (${d.total_sheets} lembar A3+${impMeta}) × ${formatRupiah(d.unit_price_sheet)} / lembar`);
                        }
                        $('#a3EstimatePrintAmount').text(formatRupiah(d.print_subtotal));

                        // Laminasi row
                        if (d.lamination_subtotal > 0) {
                            $('#a3EstimateLaminationRow').prop('hidden', false).show();
                            $('#a3EstimateLaminationLabel').text(d.lamination_name || 'Laminasi');
                            $('#a3EstimateLaminationMeta').text(`${d.total_sheets} lembar × ${formatRupiah(d.lamination_subtotal / d.total_sheets)} / lembar`);
                            $('#a3EstimateLaminationAmount').text(formatRupiah(d.lamination_subtotal));
                        } else {
                            $('#a3EstimateLaminationRow').prop('hidden', true).hide();
                        }

                        // Cutting row
                        if (d.cutting_subtotal > 0) {
                            $('#a3EstimateCuttingRow').prop('hidden', false).show();
                            $('#a3EstimateCuttingLabel').text(d.cutting_name || 'Cutting / Potong');
                            const cutMeta = d.production_mode === 'Direct Sheet' ? `${d.total_sheets} lembar` : `${d.qty} pcs`;
                            $('#a3EstimateCuttingMeta').text(cutMeta);
                            $('#a3EstimateCuttingAmount').text(formatRupiah(d.cutting_subtotal));
                        } else {
                            $('#a3EstimateCuttingRow').prop('hidden', true).hide();
                        }

                        $('#a3EstimateDisplayFinishingRow').prop('hidden', true).hide();
                    }

                    // Diskon row (Format: -Rp 21.750 konsisten dengan Large Format)
                    if (d.promo && d.discount_amount > 0) {
                        $('#a3EstimateDiscountRow').prop('hidden', false).show();
                        $('#a3EstimateDiscountLabel').text(`Diskon ${d.promo.percentage}% (${d.promo.code})`);
                        $('#a3EstimateDiscountAmount').text(`-${formatRupiah(d.discount_amount)}`);
                        setPromoStatus(`Promo ${d.promo.code} diterapkan: diskon ${d.promo.percentage}%.`, false);
                        $('#a3RemovePromoCode').removeAttr('hidden').prop('hidden', false).show();
                    } else {
                        $('#a3EstimateDiscountRow').prop('hidden', true).hide();
                        if (appliedPromoCode) {
                            setPromoStatus('Kode promo diterapkan.', false);
                        } else {
                            setPromoStatus(promoCodeInput.val() ? 'Klik Terapkan untuk memeriksa kode promo.' : 'Diskon dihitung dari master promo setelah kode diterapkan.', false);
                        }
                    }

                    // Rounding row (Format: +Rp 750 konsisten dengan Large Format)
                    if (d.rounding_amount > 0) {
                        $('#a3EstimateRoundingRow').prop('hidden', false).show();
                        $('#a3EstimateRoundingAmount').text(`+${formatRupiah(d.rounding_amount)}`);
                    } else {
                        $('#a3EstimateRoundingRow').prop('hidden', true).hide();
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
                    let orderLines;
                    if (isDisplay) {
                        const pkgCount = parseInt(qty) || 1;
                        const unitName = @js($estimator['packageUnit'] ?? 'Box');
                        orderLines = [
                            'Halo Cipta Grafika, saya ingin memesan cetak display:',
                            '',
                            'Produk: ' + (d.material_name || d.category_name || 'Kartu Nama'),
                            'Mesin: Digital Print A3+',
                            'Sisi Cetak: ' + (d.print_side || '1 Sisi'),
                            'Jumlah Pesanan: ' + pkgCount + ' ' + unitName + ' (' + (d.qty || 0) + ' pcs)',
                            'Kebutuhan Cetak: ' + (d.total_sheets || 0) + ' lembar A3+',
                            'Finishing: ' + (d.finishing_summary || 'Termasuk Box KN & Die Cut'),
                        ].filter(Boolean);
                    } else {
                        orderLines = [
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
                    }

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
        if ($('#a3_is_display').val() === '1') {
            updateDisplayPackageSummary();
        } else {
            if (modeSelect.val() === 'layout') {
                $('#a3LayoutContainer').show();
                updateLiveImposition();
            } else if (modeSelect.val() === 'direct') {
                $('#a3DirectContainer').show();
            }
        }
        fetchA3Estimation();
    });

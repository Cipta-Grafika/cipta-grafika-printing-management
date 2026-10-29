    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const typeButtons = document.querySelectorAll('.cal-choice-type');
        const productButtons = document.querySelectorAll('.cal-choice-prod');
        const materialButtons = document.querySelectorAll('.cal-choice-mat');
        const leafButtons = document.querySelectorAll('.cal-choice-leaf');
        const laminationButtons = document.querySelectorAll('.cal-choice-lam');
        const supportButtons = document.querySelectorAll('.cal-choice-support');
        const supportSection = document.getElementById('calendarSupportSection');
        const pkgButtons = document.querySelectorAll('.cal-pkg-btn');
        const quantityInput = document.getElementById('calendarQuantityInput');
        const qtyMeta = document.getElementById('calendarQtyMeta');

        const loadingIndicator = document.getElementById('calendarEstimateLoading');
        const summaryType = document.getElementById('calendarSummaryType');
        const summarySize = document.getElementById('calendarSummarySize');
        const summaryMaterial = document.getElementById('calendarSummaryMaterial');
        const summaryLeaf = document.getElementById('calendarSummaryLeaf');
        const summaryFinishing = document.getElementById('calendarSummaryFinishing');
        const summaryQty = document.getElementById('calendarSummaryQty');
        const summaryUnitPrice = document.getElementById('calendarSummaryUnitPrice');
        const grandTotalText = document.getElementById('calendarEstimateGrandTotal');
        const noteText = document.getElementById('calendarEstimateNote');

        const methodBadge = document.getElementById('calendarMethodBadge');
        const methodIcon = document.getElementById('calendarMethodIcon');
        const methodTitle = document.getElementById('calendarMethodTitle');
        const methodDesc = document.getElementById('calendarMethodDesc');
        const continueBtn = document.getElementById('calendarContinueOrderButton');
        const continueBtnText = document.getElementById('calendarContinueBtnText');
        const whatsappNumber = '6285212821022';
        let latestEstimationMessage = '';
        const csrfToken = @json(csrf_token());

        // Promo Code Elements
        const promoCodeInput = document.getElementById('calendar_promo_code');
        const applyPromoBtn = document.getElementById('calendarApplyPromoCode');
        const removePromoBtn = document.getElementById('calendarRemovePromoCode');
        const promoStatus = document.getElementById('calendarPromoCodeStatus');

        const discountSection = document.getElementById('calendarDiscountSummarySection');
        const subtotalRow = document.getElementById('calendarEstimateSubtotalRow');
        const subtotalText = document.getElementById('calendarEstimateSubtotal');
        const discountRow = document.getElementById('calendarEstimateDiscountRow');
        const discountLabel = document.getElementById('calendarEstimateDiscountLabel');
        const discountAmountText = document.getElementById('calendarEstimateDiscountAmount');
        const roundingRow = document.getElementById('calendarEstimateRoundingRow');
        const roundingAmountText = document.getElementById('calendarEstimateRoundingAmount');

        let appliedPromoCode = '';

        // State Variables
        let selectedCalendarType = 'dinding';
        let selectedProductId = 0;
        let selectedProductName = '';
        let selectedSizeLabel = '';
        let selectedMaterialId = 2; // Default Art Paper 150 gsm
        let selectedMaterialName = 'Art Paper 150 gsm';
        let selectedLeafCount = 7;
        let selectedLamination = 'none';
        let selectedSupportType = 'printing_fc';
        let selectedBoardType = 'boot_30';
        let currentQty = parseInt(quantityInput?.value || 300);
        let debounceTimer = null;

        function formatRupiah(number) {
            if (number === null || number === undefined || isNaN(number)) return 'Rp 0';
            return 'Rp ' + Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // 1. Filter Ukuran Berdasarkan Kategori (Dinding / Meja)
        function filterProductsByCategory(type) {
            let firstMatched = null;
            productButtons.forEach(btn => {
                const prodType = btn.getAttribute('data-calendar-type');
                if (prodType === type) {
                    btn.style.display = 'block';
                    if (!firstMatched) firstMatched = btn;
                } else {
                    btn.style.display = 'none';
                    btn.setAttribute('aria-checked', 'false');
                }
            });

            if (firstMatched) {
                productButtons.forEach(b => b.setAttribute('aria-checked', 'false'));
                firstMatched.setAttribute('aria-checked', 'true');
                selectedProductId = parseInt(firstMatched.getAttribute('data-product-id'));
                selectedProductName = firstMatched.getAttribute('data-product-name');
                selectedSizeLabel = firstMatched.getAttribute('data-size');
            }
        }

        // Helper untuk switch rekomendasi bahan kertas secara otomatis & friendly
        function autoSelectMaterialForType(type) {
            let targetMatBtn = null;
            if (type === 'meja') {
                // Untuk meja, pilih Art Carton 230 gsm (ID 4) atau 260 gsm (ID 5) jika saat ini masih Art Paper
                if (selectedMaterialId === 1 || selectedMaterialId === 2) {
                    targetMatBtn = document.querySelector('.cal-choice-mat[data-material-id="4"]') ||
                                   document.querySelector('.cal-choice-mat[data-material-id="5"]');
                }
            } else {
                // Untuk dinding, pilih Art Paper 150 gsm (ID 2) jika saat ini Art Carton tebal
                if (selectedMaterialId > 2) {
                    targetMatBtn = document.querySelector('.cal-choice-mat[data-material-id="2"]') ||
                                   document.querySelector('.cal-choice-mat[data-material-id="1"]');
                }
            }

            if (targetMatBtn) {
                materialButtons.forEach(b => b.setAttribute('aria-checked', 'false'));
                targetMatBtn.setAttribute('aria-checked', 'true');
                selectedMaterialId = parseInt(targetMatBtn.getAttribute('data-material-id'));
                selectedMaterialName = targetMatBtn.getAttribute('data-material-name');
            }
        }

        // Event: Pilih Jenis Kalender (Dinding vs Meja)
        typeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                typeButtons.forEach(b => b.setAttribute('aria-checked', 'false'));
                this.setAttribute('aria-checked', 'true');
                selectedCalendarType = this.getAttribute('data-type');

                if (selectedCalendarType === 'meja') {
                    if (supportSection) supportSection.style.display = 'block';
                    document.querySelectorAll('.leaf-dinding-only').forEach(el => el.style.display = 'none');
                    // Meja tidak mendukung format 1 lembar poster. Jika sebelumnya 1 lembar, switch ke 13 lembar.
                    if (selectedLeafCount === 1) {
                        selectedLeafCount = 13;
                        leafButtons.forEach(b => b.setAttribute('aria-checked', b.getAttribute('data-leaf') === '13' ? 'true' : 'false'));
                    }
                } else {
                    if (supportSection) supportSection.style.display = 'none';
                    document.querySelectorAll('.leaf-dinding-only').forEach(el => el.style.display = 'block');
                }

                autoSelectMaterialForType(selectedCalendarType);
                filterProductsByCategory(selectedCalendarType);
                calculateCalendar();
            });
        });

        // 2. Event: Pilih Ukuran Kalender
        productButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                productButtons.forEach(b => b.setAttribute('aria-checked', 'false'));
                this.setAttribute('aria-checked', 'true');
                selectedProductId = parseInt(this.getAttribute('data-product-id'));
                selectedProductName = this.getAttribute('data-product-name');
                selectedSizeLabel = this.getAttribute('data-size');
                calculateCalendar();
            });
        });

        // 3. Event: Pilih Bahan Kertas
        materialButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                materialButtons.forEach(b => b.setAttribute('aria-checked', 'false'));
                this.setAttribute('aria-checked', 'true');
                selectedMaterialId = parseInt(this.getAttribute('data-material-id'));
                selectedMaterialName = this.getAttribute('data-material-name');
                calculateCalendar();
            });
        });

        // 4. Event: Pilih Lembar Isi
        leafButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                leafButtons.forEach(b => b.setAttribute('aria-checked', 'false'));
                this.setAttribute('aria-checked', 'true');
                selectedLeafCount = parseInt(this.getAttribute('data-leaf'));
                calculateCalendar();
            });
        });

        // 5. Event: Pilih Laminasi
        laminationButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                laminationButtons.forEach(b => b.setAttribute('aria-checked', 'false'));
                this.setAttribute('aria-checked', 'true');
                selectedLamination = this.getAttribute('data-lam');
                calculateCalendar();
            });
        });

        // 6. Event: Pilih Penopang Dudukan (Kalender Meja)
        supportButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                supportButtons.forEach(b => b.setAttribute('aria-checked', 'false'));
                this.setAttribute('aria-checked', 'true');
                selectedSupportType = this.getAttribute('data-support');
                calculateCalendar();
            });
        });

        // 7. Event: Quick Package QTY
        pkgButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                pkgButtons.forEach(b => b.classList.remove('is-active'));
                this.classList.add('is-active');

                const qty = parseInt(this.getAttribute('data-qty'));
                currentQty = qty;
                if (quantityInput) quantityInput.value = qty;
                calculateCalendar();
            });
        });

        // 8. Event: Input Kuantitas Manual
        if (quantityInput) {
            quantityInput.addEventListener('input', function() {
                const val = parseInt(this.value);
                if (val && val >= 1) {
                    currentQty = val;
                    pkgButtons.forEach(b => {
                        b.classList.toggle('is-active', parseInt(b.getAttribute('data-qty')) === val);
                    });
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        calculateCalendar();
                    }, 400);
                }
            });
        }

        // 9. Promo Code Handlers
        function setPromoStatus(msg, isError = false) {
            if (!promoStatus) return;
            promoStatus.textContent = msg;
            promoStatus.className = 'mt-2 text-xs ' + (isError ? 'text-red-500 font-medium' : 'text-zinc-500 dark:text-zinc-400');
        }

        if (applyPromoBtn) {
            applyPromoBtn.addEventListener('click', function() {
                const code = promoCodeInput?.value.trim().toUpperCase() || '';
                if (!/^[A-Z0-9_-]{3,30}$/.test(code)) {
                    setPromoStatus('Masukkan kode promo 3–30 karakter: huruf, angka, - atau _, tanpa spasi.', true);
                    promoCodeInput?.focus();
                    return;
                }
                if (promoCodeInput) promoCodeInput.value = code;
                appliedPromoCode = code;
                if (removePromoBtn) removePromoBtn.removeAttribute('hidden');
                setPromoStatus('Memeriksa kode promo...', false);
                calculateCalendar();
            });
        }

        if (removePromoBtn) {
            removePromoBtn.addEventListener('click', function() {
                if (promoCodeInput) promoCodeInput.value = '';
                appliedPromoCode = '';
                this.setAttribute('hidden', '');
                setPromoStatus('Kode promo dihapus.', false);
                calculateCalendar();
            });
        }

        // 10. Kalkulasi AJAX Kalender
        async function calculateCalendar() {
            if (!selectedProductId || !selectedMaterialId) return;
            if (loadingIndicator) loadingIndicator.style.display = 'flex';
            if (continueBtn) {
                continueBtn.disabled = true;
                if (continueBtnText) {
                    continueBtnText.innerHTML = '<span class="inline-block animate-spin mr-1">⏳</span> Menghitung...';
                }
            }

            try {
                const response = await fetch("{{ route('user_preview_calendar_estimation') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        _token: csrfToken,
                        calendar_type: selectedCalendarType,
                        product_id: selectedProductId,
                        material_id: selectedMaterialId,
                        leaf_count: selectedLeafCount,
                        lamination: selectedLamination,
                        support_type: selectedSupportType,
                        board_type: selectedBoardType,
                        qty: currentQty,
                        promo_code: appliedPromoCode || null
                    })
                });

                const rawText = await response.text();
                let res;
                try {
                    res = JSON.parse(rawText);
                } catch (parseErr) {
                    console.error('Server returned non-JSON response:', rawText);
                    if (continueBtn) {
                        continueBtn.disabled = true;
                        if (continueBtnText) continueBtnText.textContent = 'Gagal memuat estimasi';
                    }
                    return;
                }

                if (!res.success) {
                    console.error('Calculation Error:', res.message);
                    if (appliedPromoCode) {
                        setPromoStatus(res.message || 'Kode promo tidak valid.', true);
                    }
                    if (continueBtn) {
                        continueBtn.disabled = true;
                        if (continueBtnText) continueBtnText.textContent = 'Lengkapi Pilihan Spesifikasi';
                    }
                    return;
                }

                const d = res.data;
                const method = res.method; // 'digital' atau 'offset'

                const leafDescMap = {
                    1: '1 Lembar (Poster 1 Muka)',
                    3: '3 Lembar (4 Bulanan / Triwulan)',
                    4: '4 Lembar (3 Bulanan / Caturwulan)',
                    6: '6 Lembar (2 Bulanan Tanpa Cover)',
                    7: '7 Lembar (2 Bulanan + Cover)',
                    12: '12 Lembar (1 Bulanan Tanpa Cover)',
                    13: '13 Lembar (1 Bulanan + Cover)'
                };
                const leafDisplay = leafDescMap[selectedLeafCount] || `${selectedLeafCount} Lembar`;

                // Update Summary Info
                if (summaryType) summaryType.textContent = selectedCalendarType === 'meja' ? 'Kalender Meja (Desk)' : 'Kalender Dinding (Wall)';
                if (summarySize) summarySize.textContent = `${selectedProductName} (${d.size_label || selectedSizeLabel})`;
                if (summaryMaterial) summaryMaterial.textContent = selectedMaterialName;
                if (summaryLeaf) summaryLeaf.textContent = leafDisplay;
                if (summaryFinishing) {
                    if (selectedCalendarType === 'meja') {
                        summaryFinishing.textContent = `Spiral Kawat + Dudukan ${selectedSupportType === 'printing_fc' ? 'Print FC' : 'Hardcover'}`;
                    } else {
                        summaryFinishing.textContent = d.finishing_detail?.name || 'Jepit Klem Seng (Auto)';
                    }
                }
                if (summaryQty) summaryQty.textContent = `${currentQty.toLocaleString('id-ID')} pcs`;

                const unitPrice = d.totals?.selling_price_per_pcs || d.recommended_price_pcs;
                if (summaryUnitPrice) summaryUnitPrice.textContent = formatRupiah(unitPrice) + ' / pcs';

                const grandTotal = d.totals?.total_selling_price || d.recommended_total_offer;
                if (grandTotalText) grandTotalText.textContent = formatRupiah(grandTotal);

                // Diskon Section
                if (d.promo && d.totals?.discount_amount > 0) {
                    if (discountSection) discountSection.style.display = 'block';
                    if (subtotalText) subtotalText.textContent = formatRupiah(d.totals.subtotal);
                    if (discountLabel) discountLabel.textContent = `Diskon (${d.promo.code} - ${d.promo.percentage}%):`;
                    if (discountAmountText) discountAmountText.textContent = '- ' + formatRupiah(d.totals.discount_amount);
                    if (d.totals.rounding_amount > 0) {
                        if (roundingRow) roundingRow.style.display = 'flex';
                        if (roundingAmountText) roundingAmountText.textContent = '+ ' + formatRupiah(d.totals.rounding_amount);
                    } else {
                        if (roundingRow) roundingRow.style.display = 'none';
                    }
                    setPromoStatus(`Kode promo ${d.promo.code} diterapkan (-${d.promo.percentage}%).`, false);
                } else {
                    if (discountSection) discountSection.style.display = 'none';
                }

                // Update Smart Method Badge
                if (methodBadge && methodTitle && methodDesc) {
                    if (method === 'digital') {
                        methodIcon.textContent = '🖨️';
                        methodTitle.textContent = 'Metode Produksi: Digital Print A3+ (Cepat & Hemat)';
                        methodDesc.textContent = d.recommendation_reason || 'Kuantitas ini paling ekonomis diproduksi dengan mesin Digital Print A3+ resolusi tinggi.';
                    } else {
                        methodIcon.textContent = '🏭';
                        methodTitle.textContent = 'Metode Produksi: Mesin Offset SM52 (Hemat Cetak Massal)';
                        methodDesc.textContent = d.recommendation_reason || 'Pesanan massal ini dicetak dengan mesin offset Heidelberg untuk harga per pcs jauh lebih hemat.';
                    }
                }

                // WhatsApp Draft Message
                const typeLabel = selectedCalendarType === 'meja' ? 'Kalender Meja' : 'Kalender Dinding';
                const lamLabel = selectedLamination === 'none' ? 'Tanpa Laminasi' : `Laminasi ${selectedLamination.toUpperCase()}`;
                let supportStr = '';
                if (selectedCalendarType === 'meja') {
                    supportStr = `\n• Model Dudukan: ${selectedSupportType === 'printing_fc' ? 'Printing Full Color' : 'Hardcover Polos Linen'}`;
                }

                let promoWaLine = '';
                if (d.promo && d.totals?.discount_amount > 0) {
                    promoWaLine = `• Diskon Promo: ${d.promo.name} (${d.promo.code} - ${d.promo.percentage}%)\n` +
                        `• Hemat Diskon: - ${formatRupiah(d.totals.discount_amount)}\n`;
                }

                latestEstimationMessage = `Halo Cipta Grafika, saya ingin memesan Cetak ${typeLabel}:\n\n` +
                    `• Jenis: ${typeLabel}\n` +
                    `• Ukuran: ${selectedProductName} (${d.size_label || selectedSizeLabel})\n` +
                    `• Bahan Kertas: ${selectedMaterialName}\n` +
                    `• Lembar Isi: ${leafDisplay}\n` +
                    `• Laminasi: ${lamLabel}` +
                    supportStr + `\n` +
                    `• Finishing: ${selectedCalendarType === 'meja' ? 'Spiral Kawat Duduk' : (d.finishing_detail?.name || 'Jepit Klem Seng')}\n` +
                    `• Jumlah: ${currentQty.toLocaleString('id-ID')} pcs\n` +
                    (promoWaLine ? `• Subtotal: ${formatRupiah(d.totals.subtotal)}\n` + promoWaLine : '') +
                    `• Total Estimasi: ${formatRupiah(grandTotal)} (${formatRupiah(unitPrice)} / pcs)\n` +
                    `• Rekomendasi Jalur: ${method === 'digital' ? 'Digital Print A3+' : 'Mesin Offset SM52'}\n\n` +
                    `Mohon dibantu info pengiriman dan proses pemesanannya. Terima kasih!`;

                if (continueBtn) {
                    continueBtn.disabled = false;
                    if (continueBtnText) {
                        continueBtnText.textContent = 'Lanjutkan Pesanan via WhatsApp';
                    }
                }

            } catch (err) {
                console.error('AJAX Error:', err);
                if (continueBtn) {
                    continueBtn.disabled = true;
                    if (continueBtnText) continueBtnText.textContent = 'Gagal memuat estimasi';
                }
            } finally {
                if (loadingIndicator) loadingIndicator.style.display = 'none';
            }
        }

        // WhatsApp Order Button Event (Responsive & Interactive)
        if (continueBtn) {
            continueBtn.addEventListener('click', function(e) {
                if (this.disabled || !latestEstimationMessage) {
                    e.preventDefault();
                    return;
                }
                const waUrl = 'https://wa.me/' + whatsappNumber + '?text=' + encodeURIComponent(latestEstimationMessage);
                window.open(waUrl, '_blank', 'noopener,noreferrer');
            });
        }

        // Inisialisasi awal saat halaman dibuka
        filterProductsByCategory(selectedCalendarType);
        const activeMat = document.querySelector('.cal-choice-mat[aria-checked="true"]') || materialButtons[0];
        if (activeMat) {
            selectedMaterialId = parseInt(activeMat.getAttribute('data-material-id'));
            selectedMaterialName = activeMat.getAttribute('data-material-name');
        }
        calculateCalendar();
    });

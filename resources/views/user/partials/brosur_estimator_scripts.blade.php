    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const productButtons = document.querySelectorAll('.brosur-choice-prod');
        const materialButtons = document.querySelectorAll('.brosur-choice-mat');
        const sidesButtons = document.querySelectorAll('.brosur-choice-side');
        const pkgButtons = document.querySelectorAll('.brosur-pkg-btn');
        const quantityInput = document.getElementById('brosurQuantityInput');
        const unitLabel = document.getElementById('brosurUnitLabel');
        const toggleUnitBtn = document.getElementById('brosurToggleUnit');
        const qtyMeta = document.getElementById('brosurQtyMeta');

        const loadingIndicator = document.getElementById('brosurEstimateLoading');
        const breakdownWrap = document.getElementById('brosurEstimateBreakdown');
        const summarySize = document.getElementById('brosurSummarySize');
        const summaryMaterial = document.getElementById('brosurSummaryMaterial');
        const summarySides = document.getElementById('brosurSummarySides');
        const summaryQty = document.getElementById('brosurSummaryQty');
        const summaryUnitPrice = document.getElementById('brosurSummaryUnitPrice');
        const grandTotalText = document.getElementById('brosurEstimateGrandTotal');
        const perRimText = document.getElementById('brosurEstimatePerRim');

        const methodBadge = document.getElementById('brosurMethodBadge');
        const methodIcon = document.getElementById('brosurMethodIcon');
        const methodTitle = document.getElementById('brosurMethodTitle');
        const methodDesc = document.getElementById('brosurMethodDesc');
        const continueBtn = document.getElementById('brosurContinueOrderButton');
        const whatsappNumber = '6285212821022';
        let latestEstimationMessage = '';

        // Promo Code Elements
        const promoCodeInput = document.getElementById('brosur_promo_code');
        const applyPromoBtn = document.getElementById('brosurApplyPromoCode');
        const removePromoBtn = document.getElementById('brosurRemovePromoCode');
        const promoStatus = document.getElementById('brosurPromoCodeStatus');

        const discountSection = document.getElementById('brosurDiscountSummarySection');
        const subtotalRow = document.getElementById('brosurEstimateSubtotalRow');
        const subtotalText = document.getElementById('brosurEstimateSubtotal');
        const discountRow = document.getElementById('brosurEstimateDiscountRow');
        const discountLabel = document.getElementById('brosurEstimateDiscountLabel');
        const discountAmountText = document.getElementById('brosurEstimateDiscountAmount');
        const roundingRow = document.getElementById('brosurEstimateRoundingRow');
        const roundingAmountText = document.getElementById('brosurEstimateRoundingAmount');

        let appliedPromoCode = '';

        // State
        let selectedProductId = productButtons.length > 0 ? parseInt(productButtons[0].getAttribute('data-product-id')) : 8;
        let selectedProductName = productButtons.length > 0 ? productButtons[0].getAttribute('data-product-name') : 'Brosur A5';
        let selectedSizeName = productButtons.length > 0 ? productButtons[0].getAttribute('data-size') : '21.00 × 14.80 cm';

        let selectedMaterialId = materialButtons.length > 0 ? parseInt(materialButtons[0].getAttribute('data-material-id')) : 13;
        let selectedMaterialName = materialButtons.length > 0 ? materialButtons[0].getAttribute('data-material-name') : 'Art Paper 120 gsm';

        let selectedPrintSides = 1;
        let currentQty = parseInt(quantityInput?.value || 500);
        let currentQtyType = 'pieces'; // 'pieces' atau 'rims'
        let debounceTimer = null;

        function formatRupiah(number) {
            if (number === null || number === undefined || isNaN(number)) return 'Rp 0';
            return 'Rp ' + Math.round(number).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // 1. Event: Pilih Ukuran
        productButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                productButtons.forEach(b => b.setAttribute('aria-checked', 'false'));
                this.setAttribute('aria-checked', 'true');
                selectedProductId = parseInt(this.getAttribute('data-product-id'));
                selectedProductName = this.getAttribute('data-product-name');
                selectedSizeName = this.getAttribute('data-size');
                calculateBrosur();
            });
        });

        // 2. Event: Pilih Bahan Kertas
        materialButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                materialButtons.forEach(b => b.setAttribute('aria-checked', 'false'));
                this.setAttribute('aria-checked', 'true');
                selectedMaterialId = parseInt(this.getAttribute('data-material-id'));
                selectedMaterialName = this.getAttribute('data-material-name');
                calculateBrosur();
            });
        });

        // 3. Event: Pilih Sisi Cetak
        sidesButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                sidesButtons.forEach(b => b.setAttribute('aria-checked', 'false'));
                this.setAttribute('aria-checked', 'true');
                selectedPrintSides = parseInt(this.getAttribute('data-side'));
                calculateBrosur();
            });
        });

        // 4. Event: Quick Package Buttons
        pkgButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                pkgButtons.forEach(b => b.classList.remove('is-active'));
                this.classList.add('is-active');

                const qty = parseInt(this.getAttribute('data-qty'));
                const type = this.getAttribute('data-type');
                currentQty = qty;
                currentQtyType = type;

                if (quantityInput) {
                    quantityInput.value = qty;
                    unitLabel.textContent = type === 'rims' ? 'Rim' : 'Lembar';
                }

                updateQtyMeta();
                calculateBrosur();
            });
        });

        // 5. Event: Input Kuantitas Manual
        if (quantityInput) {
            quantityInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    const val = parseInt(quantityInput.value) || 1;
                    currentQty = val;
                    // Reset quick buttons active state jika tidak cocok
                    pkgButtons.forEach(b => {
                        const bQty = parseInt(b.getAttribute('data-qty'));
                        const bType = b.getAttribute('data-type');
                        b.classList.toggle('is-active', bQty === currentQty && bType === currentQtyType);
                    });
                    updateQtyMeta();
                    calculateBrosur();
                }, 400);
            });
        }

        // 6. Toggle Satuan (Rim / Lembar)
        if (toggleUnitBtn) {
            toggleUnitBtn.addEventListener('click', function() {
                if (currentQtyType === 'pieces') {
                    currentQtyType = 'rims';
                    currentQty = Math.max(1, Math.round(currentQty / 500));
                    unitLabel.textContent = 'Rim';
                } else {
                    currentQtyType = 'pieces';
                    currentQty = currentQty * 500;
                    unitLabel.textContent = 'Lembar';
                }
                if (quantityInput) {
                    quantityInput.value = currentQty;
                }
                updateQtyMeta();
                calculateBrosur();
            });
        }

        function updateQtyMeta() {
            if (!qtyMeta) return;
            if (currentQtyType === 'rims') {
                const pcs = currentQty * 500;
                qtyMeta.textContent = `= ${pcs.toLocaleString('id-ID')} lembar brosur`;
            } else {
                const rims = (currentQty / 500).toFixed(1).replace('.0', '');
                qtyMeta.textContent = currentQty >= 500 ? `(Setara ± ${rims} Rim)` : `(Paket hemat lembaran)`;
            }
        }

        /* =====================================================================
           PROMO CODE HANDLERS (KONSISTEN DENGAN A3+ & LARGE FORMAT)
           ===================================================================== */
        function setPromoStatus(msg, isError) {
            if (!promoStatus) return;
            promoStatus.textContent = msg;
            promoStatus.className = 'mt-2 text-xs ' + (isError ? 'text-red-600 dark:text-red-400' : (appliedPromoCode ? 'text-emerald-600 dark:text-emerald-400 font-medium' : 'text-zinc-500 dark:text-zinc-400'));
            if (promoCodeInput) {
                promoCodeInput.setAttribute('aria-invalid', isError ? 'true' : 'false');
            }
        }

        if (promoCodeInput) {
            promoCodeInput.addEventListener('input', function() {
                const start = this.selectionStart;
                const end = this.selectionEnd;
                this.value = this.value.toUpperCase();
                this.setSelectionRange(start, end);
                appliedPromoCode = '';
                if (this.value) {
                    if (removePromoBtn) removePromoBtn.removeAttribute('hidden');
                } else {
                    if (removePromoBtn) removePromoBtn.setAttribute('hidden', '');
                }
                setPromoStatus('Klik Terapkan untuk memeriksa kode promo.', false);
            });

            promoCodeInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    if (applyPromoBtn) applyPromoBtn.click();
                }
            });
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
                calculateBrosur();
            });
        }

        if (removePromoBtn) {
            removePromoBtn.addEventListener('click', function() {
                if (promoCodeInput) promoCodeInput.value = '';
                appliedPromoCode = '';
                this.setAttribute('hidden', '');
                setPromoStatus('Kode promo dihapus.', false);
                calculateBrosur();
            });
        }

        // 7. Kalkulasi via AJAX
        async function calculateBrosur() {
            if (loadingIndicator) loadingIndicator.style.display = 'flex';
            if (continueBtn) continueBtn.disabled = true;

            try {
                const response = await fetch("{{ route('user_preview_brosur_estimation') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: selectedProductId,
                        plano_material_id: selectedMaterialId,
                        print_sides: selectedPrintSides,
                        quantity: currentQty,
                        quantity_type: currentQtyType,
                        promo_code: appliedPromoCode || null
                    })
                });

                const res = await response.json();

                if (!res.success) {
                    console.error('Calculation Error:', res.message);
                    if (appliedPromoCode) {
                        setPromoStatus(res.message || 'Kode promo tidak valid.', true);
                    }
                    return;
                }

                const d = res.data;
                const method = res.method;

                // Update Summary Info
                if (summarySize) summarySize.textContent = `${selectedProductName} (${selectedSizeName})`;
                if (summaryMaterial) summaryMaterial.textContent = selectedMaterialName;
                if (summarySides) summarySides.textContent = selectedPrintSides === 2 ? '2 Sisi (Bolak-Balik)' : '1 Sisi (1 Muka)';

                const totalPcs = method === 'digital' ? d.specs.pieces : d.input.total_pcs;
                const totalRims = (totalPcs / 500).toFixed(1).replace('.0', '');
                if (summaryQty) {
                    summaryQty.textContent = totalPcs >= 500 ? `${totalPcs.toLocaleString('id-ID')} Lembar (${totalRims} Rim)` : `${totalPcs.toLocaleString('id-ID')} Lembar`;
                }

                const unitPrice = d.totals.selling_price_per_pcs;
                if (summaryUnitPrice) summaryUnitPrice.textContent = formatRupiah(unitPrice) + ' / pcs';

                const grandTotal = d.totals.total_selling_price;
                if (grandTotalText) grandTotalText.textContent = formatRupiah(grandTotal);

                if (perRimText) {
                    if (totalPcs >= 500 && method === 'offset') {
                        perRimText.textContent = `(${formatRupiah(d.totals.selling_price_per_rim)} / rim)`;
                    } else {
                        perRimText.textContent = `(${totalPcs} lembar × ${formatRupiah(unitPrice)})`;
                    }
                }

                // Update Promo & Discount Row
                if (d.promo && d.totals.discount_amount > 0) {
                    if (discountSection) discountSection.style.display = 'block';
                    if (subtotalRow) subtotalRow.removeAttribute('hidden');
                    if (subtotalText) subtotalText.textContent = formatRupiah(d.totals.subtotal);
                    if (discountRow) discountRow.removeAttribute('hidden');
                    if (discountLabel) discountLabel.textContent = `Diskon (${d.promo.percentage}%):`;
                    if (discountAmountText) discountAmountText.textContent = `- ${formatRupiah(d.totals.discount_amount)}`;
                    if (d.totals.rounding_amount > 0) {
                        if (roundingRow) roundingRow.style.display = 'flex';
                        if (roundingAmountText) roundingAmountText.textContent = `+ ${formatRupiah(d.totals.rounding_amount)}`;
                    } else {
                        if (roundingRow) roundingRow.style.display = 'none';
                    }
                    setPromoStatus(`Promo ${d.promo.code} diterapkan: diskon ${d.promo.percentage}%.`, false);
                } else {
                    if (discountSection) discountSection.style.display = 'none';
                    if (subtotalRow) subtotalRow.setAttribute('hidden', '');
                    if (discountRow) discountRow.setAttribute('hidden', '');
                    if (roundingRow) roundingRow.style.display = 'none';
                    if (!appliedPromoCode) {
                        setPromoStatus(promoCodeInput?.value ? 'Klik Terapkan untuk memeriksa kode promo.' : 'Diskon dihitung dari master promo setelah kode diterapkan.', false);
                    }
                }

                // Update Method Badge
                if (methodBadge && methodTitle && methodDesc && methodIcon) {
                    if (method === 'digital') {
                        methodIcon.textContent = '🖨️';
                        methodTitle.textContent = 'Metode Produksi: Digital Print A3+ (Paket Cepat)';
                        methodDesc.textContent = 'Kuantitas ini paling ekonomis & cepat dicetak menggunakan mesin Digital Print A3+ beresolusi tinggi (estimasi 1–2 hari kerja).';
                    } else {
                        methodIcon.textContent = '🏭';
                        methodTitle.textContent = 'Metode Produksi: Cetak Mesin Offset SM 52 (Cetak Massal)';
                        methodDesc.textContent = 'Kuantitas massal ini diproduksi dengan Plat CTP & Mesin Cetak Offset Heidelberg SM 52. Biaya cetak per lembar jauh lebih hemat!';
                    }
                }

                // Update WhatsApp Message
                const sidesStr = selectedPrintSides === 2 ? '2 Sisi (Bolak-Balik)' : '1 Sisi (1 Muka)';
                const qtyStr = totalPcs >= 500 ? `${totalPcs.toLocaleString('id-ID')} lembar (${totalRims} Rim)` : `${totalPcs.toLocaleString('id-ID')} lembar`;
                let promoWaLine = '';
                if (d.promo && d.totals.discount_amount > 0) {
                    promoWaLine = `• Diskon Promo: ${d.promo.name} (${d.promo.code} - ${d.promo.percentage}%)\n` +
                        `• Hemat Diskon: - ${formatRupiah(d.totals.discount_amount)}\n`;
                }

                latestEstimationMessage = `Halo Cipta Grafika, saya ingin melanjutkan pesanan Brosur:\n\n` +
                    `• Produk: ${selectedProductName} (${selectedSizeName})\n` +
                    `• Bahan Kertas: ${selectedMaterialName}\n` +
                    `• Sisi Cetak: ${sidesStr}\n` +
                    `• Jumlah: ${qtyStr}\n` +
                    (promoWaLine ? `• Subtotal: ${formatRupiah(d.totals.subtotal)}\n` + promoWaLine : '') +
                    `• Total Estimasi: ${formatRupiah(grandTotal)} (${formatRupiah(unitPrice)} / pcs)\n` +
                    `• Jalur Mesin: ${method === 'digital' ? 'Digital Print A3+ (Express)' : 'Mesin Offset SM 52 (Massal)'}\n\n` +
                    `Mohon informasi langkah selanjutnya dan jadwal produksinya. Terima kasih!`;

                if (continueBtn) {
                    continueBtn.disabled = false;
                }

            } catch (err) {
                console.error('AJAX Error:', err);
                if (continueBtn) continueBtn.disabled = true;
            } finally {
                if (loadingIndicator) loadingIndicator.style.display = 'none';
            }
        }

        // Event: Lanjutkan Pesanan WhatsApp
        if (continueBtn) {
            continueBtn.addEventListener('click', function() {
                if (this.disabled || !latestEstimationMessage) return;
                const waUrl = 'https://wa.me/' + whatsappNumber + '?text=' + encodeURIComponent(latestEstimationMessage);
                window.open(waUrl, '_blank', 'noopener,noreferrer');
            });
        }

        // Inisialisasi awal saat halaman dibuka
        updateQtyMeta();
        calculateBrosur();
    });


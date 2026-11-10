    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const quickQtyButtons = document.querySelectorAll('.mug-quick-qty');
        const quantityInput = document.getElementById('mugQuantityInput');
        const tierBadge = document.getElementById('mugTierBadge');

        const printButtons = document.querySelectorAll('.mug-choice-print');
        const packageButtons = document.querySelectorAll('.mug-choice-pkg');

        const summaryPrint = document.getElementById('mugSummaryPrint');
        const summaryPackage = document.getElementById('mugSummaryPackage');
        const summaryQty = document.getElementById('mugSummaryQty');
        const summaryTier = document.getElementById('mugSummaryTier');
        const summaryUnitPrice = document.getElementById('mugSummaryUnitPrice');
        const grandTotalText = document.getElementById('mugEstimateGrandTotal');
        const savingsHint = document.getElementById('mugEstimateSavingsHint');
        const continueBtn = document.getElementById('mugContinueOrderButton');

        const whatsappNumber = '6285212821022';
        let latestEstimationMessage = '';

        // State Pilihan
        let selectedPrintName = 'Cetak Melingkar / Full Wrap (20 × 8.5 cm)';
        let selectedPackageName = 'Termasuk Box Putih Individual';

        // Master Tabel Harga Dinamis dari Database (m_mug_pricings)
        const dbTiers = @json($mugItems ?? []);
        let mugTiers = [];

        if (Array.isArray(dbTiers) && dbTiers.length > 0) {
            mugTiers = dbTiers.map(t => ({
                id: t.id,
                name: t.name,
                min: parseInt(t.min_qty),
                max: t.max_qty !== null ? parseInt(t.max_qty) : Infinity,
                label: t.tier_label,
                price: parseFloat(t.price)
            }));
        } else {
            // Fallback resmi
            mugTiers = [
                { min: 1, max: 5, label: '1–5 pcs', price: 30000 },
                { min: 6, max: 10, label: '6–10 pcs', price: 28000 },
                { min: 11, max: 20, label: '11–20 pcs', price: 26000 },
                { min: 21, max: 30, label: '21–30 pcs', price: 25000 },
                { min: 31, max: 50, label: '31–50 pcs', price: 24000 },
                { min: 51, max: Infinity, label: '> 50 pcs', price: 23000 }
            ];
        }

        let currentQty = parseInt(quantityInput?.value || 12);

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(number);
        }

        function calculateEstimation() {
            if (isNaN(currentQty) || currentQty < 1) currentQty = 1;

            // Cari tier yang cocok
            let matchedTier = mugTiers[0];
            let activeIndex = 0;

            mugTiers.forEach((tier, idx) => {
                if (currentQty >= tier.min && currentQty <= tier.max) {
                    matchedTier = tier;
                    activeIndex = idx;
                }
            });

            const unitPrice = matchedTier ? matchedTier.price : 25000;
            const grandTotal = unitPrice * currentQty;

            // Update UI Summary
            if (summaryPrint) summaryPrint.textContent = selectedPrintName.includes('Full Wrap') ? 'Full Wrap (20×8.5cm)' : '2 Sisi (Depan & Belakang)';
            if (summaryPackage) summaryPackage.textContent = selectedPackageName.includes('Termasuk Box') ? 'Box Putih Satuan' : 'Tanpa Box (Standar)';
            if (summaryQty) summaryQty.textContent = `${currentQty} pcs`;
            if (summaryTier) summaryTier.textContent = matchedTier?.label || '-';
            if (summaryUnitPrice) summaryUnitPrice.textContent = `${formatRupiah(unitPrice)} / pcs`;
            if (grandTotalText) grandTotalText.textContent = formatRupiah(grandTotal);
            if (tierBadge) tierBadge.textContent = `Tier: ${matchedTier?.label || '-'} (${formatRupiah(unitPrice)}/pcs)`;

            // Savings hint jika bisa naik ke tier lebih hemat
            if (savingsHint) {
                if (activeIndex < mugTiers.length - 1) {
                    const nextTier = mugTiers[activeIndex + 1];
                    const diffQty = nextTier.min - currentQty;
                    const diffPrice = unitPrice - nextTier.price;
                    if (diffPrice > 0) {
                        savingsHint.textContent = `💡 Tambah ${diffQty} pcs lagi untuk dapat harga ${formatRupiah(nextTier.price)}/pcs (Hemat ${formatRupiah(diffPrice)}/pcs)!`;
                        savingsHint.style.display = 'block';
                    } else {
                        savingsHint.style.display = 'none';
                    }
                } else {
                    savingsHint.textContent = `🎉 Selamat! Anda telah mendapatkan harga tier grosir terbaik!`;
                    savingsHint.style.display = 'block';
                }
            }

            // Siapkan Pesan WhatsApp
            latestEstimationMessage =
                `Halo Cipta Grafika, saya ingin konsultasi dan pesan Mug Sublimasi Keramik Custom:\n\n` +
                `*Rincian Estimasi Pesanan:*\n` +
                `• Produk: Mug Sublimasi Keramik SNI Full Color (11 oz)\n` +
                `• Area Cetak: ${selectedPrintName}\n` +
                `• Pilihan Kemasan: ${selectedPackageName}\n` +
                `• Jumlah Pesanan: ${currentQty} pcs\n` +
                `• Tier Harga: ${matchedTier?.label || '-'}\n` +
                `• Harga Satuan: ${formatRupiah(unitPrice)} / pcs\n` +
                `• Total Estimasi: *${formatRupiah(grandTotal)}*\n\n` +
                `Mohon informasi mengenai pengiriman file desain dan estimasi waktu pengerjaannya. Terima kasih!`;
        }

        // Print Choice Listeners
        printButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                printButtons.forEach(b => b.setAttribute('aria-checked', 'false'));
                this.setAttribute('aria-checked', 'true');
                selectedPrintName = this.getAttribute('data-name') || this.innerText.trim();
                calculateEstimation();
            });
        });

        // Package Choice Listeners
        packageButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                packageButtons.forEach(b => b.setAttribute('aria-checked', 'false'));
                this.setAttribute('aria-checked', 'true');
                selectedPackageName = this.getAttribute('data-name') || this.innerText.trim();
                calculateEstimation();
            });
        });

        // Quick Qty Listeners
        quickQtyButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                quickQtyButtons.forEach(b => b.classList.remove('is-active'));
                this.classList.add('is-active');
                currentQty = parseInt(this.getAttribute('data-qty'));
                if (quantityInput) quantityInput.value = currentQty;
                calculateEstimation();
            });
        });

        // Quantity Input Listener
        if (quantityInput) {
            quantityInput.addEventListener('input', function() {
                currentQty = parseInt(this.value) || 1;
                quickQtyButtons.forEach(btn => {
                    btn.classList.toggle('is-active', parseInt(btn.getAttribute('data-qty')) === currentQty);
                });
                calculateEstimation();
            });
        }

        // WhatsApp Button Listener
        if (continueBtn) {
            continueBtn.addEventListener('click', function() {
                const url = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(latestEstimationMessage)}`;
                window.open(url, '_blank');
            });
        }

        // Initial Calculation
        calculateEstimation();
    });

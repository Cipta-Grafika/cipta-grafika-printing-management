    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const quickQtyButtons = document.querySelectorAll('.pvccard-quick-qty');
        const quantityInput = document.getElementById('pvcCardQuantityInput');
        const tierBadge = document.getElementById('pvcCardTierBadge');

        const typeButtons = document.querySelectorAll('.pvccard-choice-type');
        const holeButtons = document.querySelectorAll('.pvccard-choice-hole');
        const sideButtons = document.querySelectorAll('.pvccard-choice-side');

        const summaryType = document.getElementById('pvcCardSummaryType');
        const summaryHole = document.getElementById('pvcCardSummaryHole');
        const summarySide = document.getElementById('pvcCardSummarySide');
        const summaryQty = document.getElementById('pvcCardSummaryQty');
        const summaryTier = document.getElementById('pvcCardSummaryTier');
        const summaryUnitPrice = document.getElementById('pvcCardSummaryUnitPrice');
        const grandTotalText = document.getElementById('pvcCardEstimateGrandTotal');
        const savingsHint = document.getElementById('pvcCardEstimateSavingsHint');
        const continueBtn = document.getElementById('pvcCardContinueOrderButton');

        const whatsappNumber = '6285212821022';
        let latestEstimationMessage = '';

        // State Pilihan
        let selectedTypeName = 'ID Card Karyawan & Panitia';
        let selectedHoleName = 'Tanpa Lubang (Standar Kartu ATM)';
        let selectedSideName = 'Cetak 2 Sisi Bolak-Balik (Full Color HD)';

        // Master Tabel Harga Dinamis dari Database (m_pvc_card_pricings)
        const dbTiers = @json($pvcCardItems ?? []);
        let pvcCardTiers = [];

        if (Array.isArray(dbTiers) && dbTiers.length > 0) {
            pvcCardTiers = dbTiers.map(t => ({
                id: t.id,
                name: t.name,
                min: parseInt(t.min_qty),
                max: t.max_qty !== null ? parseInt(t.max_qty) : Infinity,
                label: t.tier_label,
                price: parseFloat(t.price)
            }));
        } else {
            // Fallback resmi sesuai instruksi
            pvcCardTiers = [
                { min: 1, max: 1, label: '1 pcs', price: 30000 },
                { min: 2, max: 5, label: '2–5 pcs', price: 20000 },
                { min: 6, max: 10, label: '6–10 pcs', price: 15000 },
                { min: 11, max: 50, label: '11–50 pcs', price: 10000 },
                { min: 51, max: 100, label: '51–100 pcs', price: 7000 },
                { min: 101, max: 300, label: '101–300 pcs', price: 5000 },
                { min: 301, max: Infinity, label: '> 300 pcs', price: 5000 }
            ];
        }

        let currentQty = parseInt(quantityInput?.value || 20);

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
            let matchedTier = pvcCardTiers[0];
            let activeIndex = 0;

            pvcCardTiers.forEach((tier, idx) => {
                if (currentQty >= tier.min && currentQty <= tier.max) {
                    matchedTier = tier;
                    activeIndex = idx;
                }
            });

            const unitPrice = matchedTier ? matchedTier.price : 10000;
            const grandTotal = unitPrice * currentQty;

            // Update UI Summary
            if (summaryType) summaryType.textContent = selectedTypeName.includes('KTA') ? 'Kartu Anggota (KTA)' : 'ID Card Karyawan';
            if (summaryHole) summaryHole.textContent = selectedHoleName.includes('Lubang Oval') ? 'Lubang Oval (Tali)' : 'Tanpa Lubang (ATM)';
            if (summarySide) summarySide.textContent = selectedSideName.includes('1 Sisi') ? '1 Sisi (Muka Saja)' : '2 Sisi Bolak-Balik';
            if (summaryQty) summaryQty.textContent = `${currentQty} pcs`;
            if (summaryTier) summaryTier.textContent = matchedTier?.label || '-';
            if (summaryUnitPrice) summaryUnitPrice.textContent = `${formatRupiah(unitPrice)} / pcs`;
            if (grandTotalText) grandTotalText.textContent = formatRupiah(grandTotal);
            if (tierBadge) tierBadge.textContent = `Tier: ${matchedTier?.label || '-'} (${formatRupiah(unitPrice)}/pcs)`;

            // Savings hint jika bisa naik ke tier lebih hemat
            if (savingsHint) {
                if (activeIndex < pvcCardTiers.length - 1) {
                    const nextTier = pvcCardTiers[activeIndex + 1];
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
                `Halo Cipta Grafika, saya ingin konsultasi dan pesan Cetak Kartu PVC Custom:\n\n` +
                `*Rincian Estimasi Pesanan:*\n` +
                `• Produk: Kartu PVC Tebal 0.76 mm Standar ATM\n` +
                `• Tipe Kartu: ${selectedTypeName}\n` +
                `• Finishing Lubang: ${selectedHoleName}\n` +
                `• Sisi Cetak: ${selectedSideName}\n` +
                `• Jumlah Pesanan: ${currentQty} pcs\n` +
                `• Tier Harga: ${matchedTier?.label || '-'}\n` +
                `• Harga Satuan: ${formatRupiah(unitPrice)} / pcs\n` +
                `• Total Estimasi: *${formatRupiah(grandTotal)}*\n\n` +
                `Mohon informasi mengenai pengiriman file data/desain dan estimasi waktu cetaknya. Terima kasih!`;
        }

        // Type Choice Listeners
        typeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                typeButtons.forEach(b => b.setAttribute('aria-checked', 'false'));
                this.setAttribute('aria-checked', 'true');
                selectedTypeName = this.getAttribute('data-name') || this.innerText.trim();
                calculateEstimation();
            });
        });

        // Hole Choice Listeners
        holeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                holeButtons.forEach(b => b.setAttribute('aria-checked', 'false'));
                this.setAttribute('aria-checked', 'true');
                selectedHoleName = this.getAttribute('data-name') || this.innerText.trim();
                calculateEstimation();
            });
        });

        // Side Choice Listeners
        sideButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                sideButtons.forEach(b => b.setAttribute('aria-checked', 'false'));
                this.setAttribute('aria-checked', 'true');
                selectedSideName = this.getAttribute('data-name') || this.innerText.trim();
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

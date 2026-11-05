    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const widthButtons = document.querySelectorAll('.lanyard-choice-width');
        const pkgButtons = document.querySelectorAll('.lanyard-choice-pkg');
        const quickQtyButtons = document.querySelectorAll('.lanyard-quick-qty');
        const quantityInput = document.getElementById('lanyardQuantityInput');
        const tierBadge = document.getElementById('lanyardTierBadge');
        const tierTableBody = document.getElementById('lanyardTierTableBody');

        const summaryWidth = document.getElementById('lanyardSummaryWidth');
        const summaryPackage = document.getElementById('lanyardSummaryPackage');
        const summaryQty = document.getElementById('lanyardSummaryQty');
        const summaryTier = document.getElementById('lanyardSummaryTier');
        const summaryUnitPrice = document.getElementById('lanyardSummaryUnitPrice');
        const grandTotalText = document.getElementById('lanyardEstimateGrandTotal');
        const savingsHint = document.getElementById('lanyardEstimateSavingsHint');
        const continueBtn = document.getElementById('lanyardContinueOrderButton');

        const whatsappNumber = '6285212821022';
        let latestEstimationMessage = '';

        // Master Tabel Harga Dinamis dari Database (m_lanyard_pricings)
        const dbTiers = @json($lanyardTiers ?? []);
        let lanyardPricing = {
            '2cm': { 'tali': [], 'set': [] },
            '2.5cm': { 'tali': [], 'set': [] }
        };

        if (Array.isArray(dbTiers) && dbTiers.length > 0) {
            dbTiers.forEach(t => {
                if (lanyardPricing[t.width_type] && lanyardPricing[t.width_type][t.package_type]) {
                    lanyardPricing[t.width_type][t.package_type].push({
                        id: t.id,
                        min: parseInt(t.min_qty),
                        max: t.max_qty !== null ? parseInt(t.max_qty) : Infinity,
                        label: t.tier_label,
                        price: parseFloat(t.price)
                    });
                }
            });
        }

        // Fallback jika belum terisi di database
        if (!lanyardPricing['2cm']['tali'].length) {
            lanyardPricing = {
                '2cm': {
                    'tali': [
                        { min: 1, max: 4, label: '1–4 pcs', price: 20000 },
                        { min: 5, max: 12, label: '5–12 pcs', price: 14000 },
                        { min: 13, max: 25, label: '13–25 pcs', price: 13000 },
                        { min: 26, max: 50, label: '26–50 pcs', price: 12000 },
                        { min: 51, max: 100, label: '51–100 pcs', price: 11000 },
                        { min: 101, max: Infinity, label: '> 100 pcs', price: 10000 }
                    ],
                    'set': [
                        { min: 1, max: 4, label: '1–4 pcs', price: 25000 },
                        { min: 5, max: 12, label: '5–12 pcs', price: 24000 },
                        { min: 13, max: 25, label: '13–25 pcs', price: 23000 },
                        { min: 26, max: 50, label: '26–50 pcs', price: 22000 },
                        { min: 51, max: 100, label: '51–100 pcs', price: 21000 },
                        { min: 101, max: Infinity, label: '> 100 pcs', price: 20000 }
                    ]
                },
                '2.5cm': {
                    'tali': [
                        { min: 1, max: 4, label: '1–4 pcs', price: 23000 },
                        { min: 5, max: 12, label: '5–12 pcs', price: 16000 },
                        { min: 13, max: 25, label: '13–25 pcs', price: 15000 },
                        { min: 26, max: 50, label: '26–50 pcs', price: 14000 },
                        { min: 51, max: 100, label: '51–100 pcs', price: 13000 },
                        { min: 101, max: Infinity, label: '> 100 pcs', price: 12000 }
                    ],
                    'set': [
                        { min: 1, max: 4, label: '1–4 pcs', price: 30000 },
                        { min: 5, max: 12, label: '5–12 pcs', price: 29000 },
                        { min: 13, max: 25, label: '13–25 pcs', price: 28000 },
                        { min: 26, max: 50, label: '26–50 pcs', price: 26000 },
                        { min: 51, max: 100, label: '51–100 pcs', price: 25000 },
                        { min: 101, max: Infinity, label: '> 100 pcs', price: 24000 }
                    ]
                }
            };
        }

        // State
        let selectedWidth = '2cm';
        let selectedPackage = 'set';
        let currentQty = parseInt(quantityInput?.value || 10);

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(number);
        }

        function calculateLanyard() {
            const tiers = lanyardPricing[selectedWidth]?.[selectedPackage] || [];
            if (!tiers.length) return;

            // Cari Tier yang Aktif
            let activeTier = tiers[0];
            let activeTierIndex = 0;
            for (let i = 0; i < tiers.length; i++) {
                if (currentQty >= tiers[i].min && currentQty <= tiers[i].max) {
                    activeTier = tiers[i];
                    activeTierIndex = i;
                    break;
                }
            }

            const unitPrice = activeTier.price;
            const grandTotal = currentQty * unitPrice;

            // Render Table Tiering
            if (tierTableBody) {
                tierTableBody.innerHTML = '';
                tiers.forEach((tier, idx) => {
                    const isCurrent = (idx === activeTierIndex);
                    const tr = document.createElement('tr');
                    tr.className = isCurrent ? 'lanyard-tier-active text-zinc-900 dark:text-white' : 'text-zinc-600 dark:text-zinc-400';
                    tr.innerHTML = `
                        <td class="py-1.5 px-2 font-medium">${tier.label}</td>
                        <td class="py-1.5 px-2 text-right ${isCurrent ? 'text-accent font-semibold' : ''}">${formatRupiah(tier.price)} / pcs</td>
                        <td class="py-1.5 px-2 text-center">
                            ${isCurrent ? '<span class="inline-block px-1.5 py-0.5 text-[10px] rounded bg-accent text-white font-semibold">Dipilih</span>' : '<span class="text-zinc-400">-</span>'}
                        </td>
                    `;
                    tierTableBody.appendChild(tr);
                });
            }

            // Update Tier Badge
            if (tierBadge) {
                tierBadge.textContent = `Tier: ${activeTier.label}`;
            }

            // Update Summary
            const widthName = selectedWidth === '2cm' ? 'Lebar 2.0 cm' : 'Lebar 2.5 cm';
            const packageName = selectedPackage === 'tali' ? 'Hanya Tali Saja' : '1 Set Lengkap (Holder + PVC)';

            if (summaryWidth) summaryWidth.textContent = widthName;
            if (summaryPackage) summaryPackage.textContent = packageName;
            if (summaryQty) summaryQty.textContent = `${currentQty.toLocaleString('id-ID')} pcs`;
            if (summaryTier) summaryTier.textContent = activeTier.label;
            if (summaryUnitPrice) summaryUnitPrice.textContent = `${formatRupiah(unitPrice)} / pcs`;
            if (grandTotalText) grandTotalText.textContent = formatRupiah(grandTotal);

            // Hint Penghematan
            if (savingsHint) {
                if (activeTierIndex < tiers.length - 1) {
                    const nextTier = tiers[activeTierIndex + 1];
                    const diffQty = nextTier.min - currentQty;
                    const diffPrice = unitPrice - nextTier.price;
                    savingsHint.textContent = `💡 Tambah ${diffQty} pcs lagi untuk dapat harga ${formatRupiah(nextTier.price)}/pcs (hemat ${formatRupiah(diffPrice)}/pcs)!`;
                    savingsHint.style.display = 'block';
                } else {
                    savingsHint.textContent = '🎉 Anda sudah mendapatkan harga grosir terbaik!';
                    savingsHint.style.display = 'block';
                }
            }

            // Update WhatsApp Link Message
            latestEstimationMessage =
                `Halo Cipta Grafika, saya ingin melanjutkan pemesanan Lanyard:\n\n` +
                `• Produk: Tali Lanyard & ID Card Custom\n` +
                `• Lebar Tali: ${widthName}\n` +
                `• Kelengkapan: ${packageName}\n` +
                `• Jumlah Pesanan: ${currentQty.toLocaleString('id-ID')} pcs (${activeTier.label})\n` +
                `• Harga Satuan: ${formatRupiah(unitPrice)} / pcs\n` +
                `• Total Estimasi: ${formatRupiah(grandTotal)}\n\n` +
                `Mohon panduan pengiriman file desain logo dan jadwal produksinya. Terima kasih!`;
        }

        // Event: Pilihan Lebar Tali
        widthButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                widthButtons.forEach(b => {
                    b.setAttribute('aria-checked', 'false');
                    b.classList.remove('is-active');
                });
                this.setAttribute('aria-checked', 'true');
                this.classList.add('is-active');
                selectedWidth = this.getAttribute('data-width') || '2cm';
                calculateLanyard();
            });
        });

        // Event: Pilihan Paket
        pkgButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                pkgButtons.forEach(b => {
                    b.setAttribute('aria-checked', 'false');
                    b.classList.remove('is-active');
                });
                this.setAttribute('aria-checked', 'true');
                this.classList.add('is-active');
                selectedPackage = this.getAttribute('data-pkg') || 'set';
                calculateLanyard();
            });
        });

        // Event: Quick Qty Buttons
        quickQtyButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                quickQtyButtons.forEach(b => b.classList.remove('is-active'));
                this.classList.add('is-active');
                const val = parseInt(this.getAttribute('data-qty') || 10);
                if (quantityInput) {
                    quantityInput.value = val;
                }
                currentQty = val;
                calculateLanyard();
            });
        });

        // Event: Manual Qty Input
        if (quantityInput) {
            quantityInput.addEventListener('input', function() {
                let val = parseInt(this.value);
                if (isNaN(val) || val < 1) val = 1;
                currentQty = val;

                // Sync quick buttons
                quickQtyButtons.forEach(btn => {
                    if (parseInt(btn.getAttribute('data-qty')) === val) {
                        btn.classList.add('is-active');
                    } else {
                        btn.classList.remove('is-active');
                    }
                });

                calculateLanyard();
            });
        }

        // Event: Klik Lanjutkan Pesanan WhatsApp
        if (continueBtn) {
            continueBtn.addEventListener('click', function() {
                if (!latestEstimationMessage) return;
                const waUrl = 'https://wa.me/' + whatsappNumber + '?text=' + encodeURIComponent(latestEstimationMessage);
                window.open(waUrl, '_blank', 'noopener,noreferrer');
            });
        }

        // Jalankan kalkulasi pertama kali
        calculateLanyard();
    });


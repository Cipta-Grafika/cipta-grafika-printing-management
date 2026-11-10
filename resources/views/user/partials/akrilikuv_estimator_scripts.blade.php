    document.addEventListener('DOMContentLoaded', function() {
        // Elements
        const variantButtons = document.querySelectorAll('.akrilik-choice-variant');
        const quickQtyButtons = document.querySelectorAll('.akrilik-quick-qty');
        const quantityInput = document.getElementById('akrilikUvQuantityInput');

        const summaryVariant = document.getElementById('akrilikUvSummaryVariant');
        const summaryQty = document.getElementById('akrilikUvSummaryQty');
        const summaryUnitPrice = document.getElementById('akrilikUvSummaryUnitPrice');
        const grandTotalText = document.getElementById('akrilikUvEstimateGrandTotal');
        const continueBtn = document.getElementById('akrilikUvContinueOrderButton');

        const whatsappNumber = '6285212821022';
        let latestEstimationMessage = '';

        // Master Data dari Database (m_akrilik_uv_pricings)
        const dbItems = @json($akrilikUvItems ?? []);
        let akrilikItems = {
            'gantungan': { name: 'Gantungan Kunci Akrilik UV (2 Muka 1 Lapis)', price: 20000, spec: '2 Muka 1 Lapis Akrilik Bening 3 mm' },
            'standee': { name: 'Standee Mini Akrilik UV (2 Muka 1 Lapis)', price: 20000, spec: '2 Muka 1 Lapis Akrilik Bening 3 mm + Base Meja' }
        };

        if (Array.isArray(dbItems) && dbItems.length > 0) {
            dbItems.forEach(item => {
                const nameLower = (item.name || '').toLowerCase();
                if (nameLower.includes('standee')) {
                    akrilikItems['standee'] = {
                        name: item.name,
                        price: parseFloat(item.price) || 20000,
                        spec: `${item.spec_label || '2 Muka 1 Lapis'} · ${item.thickness || '3 mm'}`
                    };
                } else {
                    akrilikItems['gantungan'] = {
                        name: item.name,
                        price: parseFloat(item.price) || 20000,
                        spec: `${item.spec_label || '2 Muka 1 Lapis'} · ${item.thickness || '3 mm'}`
                    };
                }
            });
        }

        let selectedVariant = 'gantungan';
        let currentQty = parseInt(quantityInput?.value || 10);

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(number);
        }

        function calculateEstimation() {
            if (isNaN(currentQty) || currentQty < 1) currentQty = 1;

            const currentItem = akrilikItems[selectedVariant] || akrilikItems['gantungan'];
            const unitPrice = currentItem.price;
            const grandTotal = unitPrice * currentQty;

            // Update UI
            if (summaryVariant) summaryVariant.textContent = currentItem.name;
            if (summaryQty) summaryQty.textContent = `${currentQty} pcs`;
            if (summaryUnitPrice) summaryUnitPrice.textContent = `${formatRupiah(unitPrice)} / pcs`;
            if (grandTotalText) grandTotalText.textContent = formatRupiah(grandTotal);

            // Siapkan Pesan WhatsApp
            latestEstimationMessage =
                `Halo Cipta Grafika, saya ingin konsultasi dan pesan Merchandise Akrilik UV:\n\n` +
                `*Rincian Estimasi Pesanan:*\n` +
                `• Produk: ${currentItem.name}\n` +
                `• Spesifikasi: Akrilik Bening 3 mm · Flatbed UV 2 Muka · 1 Lapis Akrilik Solid\n` +
                `• Jumlah Pesanan: ${currentQty} pcs\n` +
                `• Harga Satuan: ${formatRupiah(unitPrice)} / pcs\n` +
                `• Total Estimasi: *${formatRupiah(grandTotal)}*\n\n` +
                `Mohon informasi mengenai pengiriman file desain dan estimasi waktu pembuatannya. Terima kasih!`;
        }

        // Variant Button Listener
        variantButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                variantButtons.forEach(b => {
                    b.setAttribute('aria-checked', 'false');
                    b.classList.remove('is-active', 'border-accent', 'bg-accent/5');
                });
                this.setAttribute('aria-checked', 'true');
                this.classList.add('is-active', 'border-accent', 'bg-accent/5');
                selectedVariant = this.getAttribute('data-variant');
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
                const encodedMsg = encodeURIComponent(latestEstimationMessage);
                const url = `https://wa.me/${whatsappNumber}?text=${encodedMsg}`;
                window.open(url, '_blank');
            });
        }

        // Initial Calculation
        calculateEstimation();
    });

    document.addEventListener('DOMContentLoaded', function() {
        const typeButtons = document.querySelectorAll('.nametag-choice-type');
        const fastenerButtons = document.querySelectorAll('.nametag-choice-fastener');
        const quickQtyButtons = document.querySelectorAll('.nametag-quick-qty');
        const qtyInput = document.getElementById('nameTagQtyInput');
        const qtyMinus = document.getElementById('nameTagQtyMinus');
        const qtyPlus = document.getElementById('nameTagQtyPlus');
        const customNotes = document.getElementById('nameTagCustomNotes');

        const summaryType = document.getElementById('summaryNameTagType');
        const summaryFastener = document.getElementById('summaryNameTagFastener');
        const summaryQty = document.getElementById('summaryNameTagQty');
        const summaryUnitPrice = document.getElementById('summaryNameTagUnitPrice');
        const summaryTotalPrice = document.getElementById('summaryNameTagTotalPrice');
        const btnWhatsApp = document.getElementById('btnNameTagWhatsApp');
        const btnAddToCart = document.getElementById('btnNameTagAddToCart');

        const whatsappNumber = '6285212821022';

        // Master data varian papan nama dari database
        const dbItems = @json($nameTagItems ?? []);
        let nameTagCatalog = {};

        if (Array.isArray(dbItems) && dbItems.length > 0) {
            dbItems.forEach(item => {
                nameTagCatalog[item.type_code] = {
                    id: item.id,
                    name: item.name,
                    price: parseFloat(item.price),
                    fasteners: item.fastener_options || 'Peniti, Magnet',
                    description: item.description
                };
            });
        } else {
            nameTagCatalog = {
                'biasa': { id: 1, name: 'Papan Nama Biasa', price: 25000, fasteners: 'Peniti, Magnet' },
                'resin': { id: 2, name: 'Papan Nama Resin', price: 35000, fasteners: 'Peniti, Magnet' },
                'resin_emas': { id: 3, name: 'Papan Nama Resin Emas', price: 35000, fasteners: 'Peniti, Magnet' }
            };
        }

        // State aktif
        let selectedTypeCode = 'resin';
        let selectedFastener = 'magnet';
        let currentQty = parseInt(qtyInput?.value || 5);

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(number);
        }

        function calculateNameTag() {
            const item = nameTagCatalog[selectedTypeCode] || Object.values(nameTagCatalog)[0];
            const unitPrice = item ? item.price : 35000;
            const totalPrice = unitPrice * currentQty;

            const fastenerLabel = selectedFastener === 'magnet' ? 'Magnet Neodymium' : 'Peniti Stainless';

            // Update Ringkasan UI
            if (summaryType) summaryType.textContent = item ? item.name : 'Papan Nama Resin';
            if (summaryFastener) summaryFastener.textContent = fastenerLabel;
            if (summaryQty) summaryQty.textContent = currentQty + ' pcs';
            if (summaryUnitPrice) summaryUnitPrice.textContent = formatRupiah(unitPrice);
            if (summaryTotalPrice) summaryTotalPrice.textContent = formatRupiah(totalPrice);

            // Highlight baris di tabel pricelist
            document.querySelectorAll('.nametag-row-highlight').forEach(row => {
                const code = row.getAttribute('data-code');
                if (code === selectedTypeCode) {
                    row.classList.add('bg-accent/15', 'font-semibold');
                    row.classList.remove('opacity-70');
                } else {
                    row.classList.remove('bg-accent/15', 'font-semibold');
                    row.classList.add('opacity-70');
                }
            });

            // Update WhatsApp CTA Link
            if (btnWhatsApp) {
                const notes = customNotes?.value?.trim() || '';
                const msg = [
                    'Halo Admin Cipta Grafika, saya ingin memesan *Papan Nama Dada (Name Tag)*:',
                    '',
                    `• *Varian Bahan*: ${item ? item.name : 'Papan Nama Resin'}`,
                    `• *Model Pengait*: ${fastenerLabel}`,
                    `• *Jumlah*: ${currentQty} pcs`,
                    `• *Harga Satuan*: ${formatRupiah(unitPrice)}`,
                    `• *Estimasi Total*: ${formatRupiah(totalPrice)}`,
                    notes ? `• *Daftar Nama/Tulisan*:\n${notes}` : '• *Daftar Nama*: (akan saya kirimkan detailnya)',
                    '',
                    'Mohon informasi ketersediaan dan proses pemesanannya. Terima kasih!'
                ].join('\n');

                btnWhatsApp.href = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(msg)}`;
            }
        }

        // Handler Pilihan Varian Bahan
        typeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                typeButtons.forEach(b => {
                    b.setAttribute('aria-checked', 'false');
                    b.classList.remove('border-accent', 'bg-accent/5', 'shadow-sm');
                });
                this.setAttribute('aria-checked', 'true');
                this.classList.add('border-accent', 'bg-accent/5', 'shadow-sm');
                selectedTypeCode = this.getAttribute('data-code');
                calculateNameTag();
            });
        });

        // Handler Pilihan Pengait
        fastenerButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                fastenerButtons.forEach(b => {
                    b.setAttribute('aria-checked', 'false');
                    b.classList.remove('border-accent', 'bg-accent/5', 'shadow-sm');
                });
                this.setAttribute('aria-checked', 'true');
                this.classList.add('border-accent', 'bg-accent/5', 'shadow-sm');
                selectedFastener = this.getAttribute('data-fastener');
                calculateNameTag();
            });
        });

        // Handler Preset Kuantiti Cepat
        quickQtyButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                quickQtyButtons.forEach(b => b.classList.remove('is-active', 'border-accent', 'bg-accent', 'text-white'));
                this.classList.add('is-active', 'border-accent', 'bg-accent', 'text-white');
                currentQty = parseInt(this.getAttribute('data-qty')) || 1;
                if (qtyInput) qtyInput.value = currentQty;
                calculateNameTag();
            });
        });

        // Stepper Minus & Plus
        if (qtyMinus) {
            qtyMinus.addEventListener('click', function() {
                if (currentQty > 1) {
                    currentQty--;
                    if (qtyInput) qtyInput.value = currentQty;
                    syncQuickButtons();
                    calculateNameTag();
                }
            });
        }

        if (qtyPlus) {
            qtyPlus.addEventListener('click', function() {
                currentQty++;
                if (qtyInput) qtyInput.value = currentQty;
                syncQuickButtons();
                calculateNameTag();
            });
        }

        if (qtyInput) {
            qtyInput.addEventListener('input', function() {
                let val = parseInt(this.value);
                if (isNaN(val) || val < 1) val = 1;
                currentQty = val;
                syncQuickButtons();
                calculateNameTag();
            });
        }

        if (customNotes) {
            customNotes.addEventListener('input', function() {
                calculateNameTag();
            });
        }

        function syncQuickButtons() {
            quickQtyButtons.forEach(b => {
                if (parseInt(b.getAttribute('data-qty')) === currentQty) {
                    b.classList.add('is-active', 'border-accent', 'bg-accent', 'text-white');
                } else {
                    b.classList.remove('is-active', 'border-accent', 'bg-accent', 'text-white');
                }
            });
        }

        // Inisialisasi awal
        calculateNameTag();
    });


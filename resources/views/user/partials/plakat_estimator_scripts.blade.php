    document.addEventListener('DOMContentLoaded', function() {
        const materialButtons = document.querySelectorAll('.plakat-choice-material');
        const thicknessButtons = document.querySelectorAll('.plakat-choice-thickness');
        const baseButtons = document.querySelectorAll('.plakat-choice-base');
        const boxButtons = document.querySelectorAll('.plakat-choice-box');
        const quickQtyButtons = document.querySelectorAll('.plakat-quick-qty');
        const qtyInput = document.getElementById('plakatQtyInput');
        const qtyMinus = document.getElementById('plakatQtyMinus');
        const qtyPlus = document.getElementById('plakatQtyPlus');
        const checkDesign = document.getElementById('checkDesignService');
        const customNotes = document.getElementById('plakatCustomNotes');

        const sectionAkrilikOptions = document.getElementById('sectionAkrilikOptions');
        const btnBaseHitam = document.getElementById('btnBaseHitam');

        const summaryMaterial = document.getElementById('summaryPlakatMaterial');
        const summaryThickness = document.getElementById('summaryPlakatThickness');
        const summaryBase = document.getElementById('summaryPlakatBase');
        const summaryBox = document.getElementById('summaryPlakatBox');
        const summarySize = document.getElementById('summaryPlakatSize');
        const summaryQty = document.getElementById('summaryPlakatQty');
        const summaryUnitPrice = document.getElementById('summaryPlakatUnitPrice');
        const summaryDesignFee = document.getElementById('summaryPlakatDesignFee');
        const summaryTotalPrice = document.getElementById('summaryPlakatTotalPrice');
        const summaryRowThickness = document.getElementById('summaryRowThickness');
        const summaryRowBase = document.getElementById('summaryRowBase');

        const btnWhatsApp = document.getElementById('btnPlakatWhatsApp');
        const btnAddToCart = document.getElementById('btnPlakatAddToCart');

        const whatsappNumber = '6285212821022';

        // Master data varian plakat dari database
        const dbItems = @json($plakatItems ?? []);
        let plakatCatalog = [];

        if (Array.isArray(dbItems) && dbItems.length > 0) {
            plakatCatalog = dbItems.map(item => ({
                id: item.id,
                name: item.name,
                material: item.material_type,
                thickness: item.thickness,
                base: item.base_type,
                box: item.box_type,
                dimensions: item.dimensions || '20x15 cm',
                price: parseFloat(item.price)
            }));
        }

        // State aktif
        let selectedMaterial = 'akrilik';
        let selectedThickness = '3mm';
        let selectedBase = 'akrilik';
        let selectedBox = 'box';
        let currentQty = parseInt(qtyInput?.value || 1);
        let hasDesignFee = false;
        const DESIGN_FEE_AMOUNT = 35000;

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(number);
        }

        // Sinkronisasi ketersediaan dudukan hitam (khusus 3mm & 5mm)
        function syncBaseOptions() {
            if (selectedMaterial === 'akrilik') {
                if (selectedThickness === '2mm' || selectedThickness === '1cm') {
                    if (btnBaseHitam) {
                        btnBaseHitam.style.opacity = '0.4';
                        btnBaseHitam.style.pointerEvents = 'none';
                        btnBaseHitam.setAttribute('title', 'Dudukan hitam hanya tersedia untuk ketebalan 3mm dan 5mm');
                    }
                    if (selectedBase === 'hitam') {
                        selectedBase = 'akrilik';
                        baseButtons.forEach(b => {
                            const val = b.getAttribute('data-base');
                            b.setAttribute('aria-checked', val === 'akrilik' ? 'true' : 'false');
                            if (val === 'akrilik') {
                                b.classList.add('border-accent', 'bg-accent/5', 'shadow-sm');
                            } else {
                                b.classList.remove('border-accent', 'bg-accent/5', 'shadow-sm');
                            }
                        });
                    }
                } else {
                    if (btnBaseHitam) {
                        btnBaseHitam.style.opacity = '1';
                        btnBaseHitam.style.pointerEvents = 'auto';
                        btnBaseHitam.removeAttribute('title');
                    }
                }
            }
        }

        function calculatePlakat() {
            let matchedItem = null;

            if (selectedMaterial === 'kayu') {
                matchedItem = plakatCatalog.find(item => item.material === 'kayu');
            } else {
                matchedItem = plakatCatalog.find(item => 
                    item.material === 'akrilik' &&
                    item.thickness === selectedThickness &&
                    item.base === selectedBase &&
                    item.box === selectedBox
                );
            }

            const unitPrice = matchedItem ? matchedItem.price : 210000;
            const designFee = hasDesignFee ? DESIGN_FEE_AMOUNT : 0;
            const totalPrice = (unitPrice * currentQty) + designFee;

            // Update Ringkasan UI
            if (summaryMaterial) {
                summaryMaterial.textContent = selectedMaterial === 'kayu' ? 'Plakat Kayu Solid' : 'Akrilik Cetak UV';
            }

            if (selectedMaterial === 'kayu') {
                if (summaryRowThickness) summaryRowThickness.style.display = 'none';
                if (summaryRowBase) summaryRowBase.style.display = 'none';
                if (summaryBox) summaryBox.textContent = 'Termasuk Box Beludru Eksklusif';
            } else {
                if (summaryRowThickness) summaryRowThickness.style.display = 'flex';
                if (summaryRowBase) summaryRowBase.style.display = 'flex';
                if (summaryThickness) summaryThickness.textContent = selectedThickness.toUpperCase();
                if (summaryBase) summaryBase.textContent = selectedBase === 'hitam' ? 'Dudukan Hitam Elegan' : 'Dudukan Akrilik Bening';
                if (summaryBox) summaryBox.textContent = selectedBox === 'box' ? 'Termasuk Box Beludru (+ Box)' : 'Tanpa Box (Plakat Saja)';
            }

            if (summarySize) summarySize.textContent = '20 x 15 cm';
            if (summaryQty) summaryQty.textContent = currentQty + ' pcs';
            if (summaryUnitPrice) summaryUnitPrice.textContent = formatRupiah(unitPrice);
            if (summaryDesignFee) {
                summaryDesignFee.textContent = hasDesignFee ? formatRupiah(DESIGN_FEE_AMOUNT) + ' (Jasa Setting/Desain)' : 'Rp 0 (Punya Sendiri)';
            }
            if (summaryTotalPrice) summaryTotalPrice.textContent = formatRupiah(totalPrice);

            // Highlight baris di tabel pricelist
            document.querySelectorAll('.plakat-row-highlight').forEach(row => {
                const mat = row.getAttribute('data-material');
                const thk = row.getAttribute('data-thickness');
                const bas = row.getAttribute('data-base');
                const box = row.getAttribute('data-box');

                let isMatch = false;
                if (selectedMaterial === 'kayu') {
                    isMatch = (mat === 'kayu');
                } else {
                    isMatch = (mat === 'akrilik' && thk === selectedThickness && bas === selectedBase && box === selectedBox);
                }

                if (isMatch) {
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
                const specLines = [
                    'Halo Admin Cipta Grafika, saya ingin memesan *Plakat Cinderamata (20x15 cm)*:',
                    '',
                    `• *Bahan*: ${selectedMaterial === 'kayu' ? 'Kayu Solid + Box' : 'Akrilik Cetak UV'}`,
                ];

                if (selectedMaterial === 'akrilik') {
                    specLines.push(`• *Ketebalan*: ${selectedThickness.toUpperCase()}`);
                    specLines.push(`• *Dudukan*: ${selectedBase === 'hitam' ? 'Dudukan Hitam Elegan' : 'Dudukan Akrilik Bening'}`);
                    specLines.push(`• *Kemasan*: ${selectedBox === 'box' ? 'Termasuk Box Beludru (+ Box)' : 'Tanpa Box'}`);
                } else {
                    specLines.push('• *Kemasan*: Termasuk Box Beludru Eksklusif');
                }

                specLines.push(`• *Ukuran*: 20 x 15 cm`);
                specLines.push(`• *Jumlah*: ${currentQty} pcs`);
                specLines.push(`• *Harga Satuan*: ${formatRupiah(unitPrice)}`);
                if (hasDesignFee) {
                    specLines.push(`• *Jasa Desain Grafis*: ${formatRupiah(DESIGN_FEE_AMOUNT)}`);
                }
                specLines.push(`• *Total Estimasi*: ${formatRupiah(totalPrice)}`);

                if (notes) {
                    specLines.push(`• *Keterangan Acara / Tulisan*:\n${notes}`);
                } else {
                    specLines.push('• *Keterangan Acara*: (file/tulisan akan saya kirimkan detailnya)');
                }

                specLines.push('', 'Mohon info ketersediaan dan proses pemesanannya. Terima kasih!');

                btnWhatsApp.href = `https://wa.me/${whatsappNumber}?text=${encodeURIComponent(specLines.join('\n'))}`;
            }
        }

        // Handler Pilihan Bahan
        materialButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                materialButtons.forEach(b => {
                    b.setAttribute('aria-checked', 'false');
                    b.classList.remove('border-accent', 'bg-accent/5', 'shadow-sm');
                });
                this.setAttribute('aria-checked', 'true');
                this.classList.add('border-accent', 'bg-accent/5', 'shadow-sm');
                selectedMaterial = this.getAttribute('data-material');

                if (selectedMaterial === 'kayu') {
                    if (sectionAkrilikOptions) sectionAkrilikOptions.style.display = 'none';
                } else {
                    if (sectionAkrilikOptions) sectionAkrilikOptions.style.display = 'block';
                    syncBaseOptions();
                }

                calculatePlakat();
            });
        });

        // Handler Pilihan Ketebalan
        thicknessButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                thicknessButtons.forEach(b => {
                    b.setAttribute('aria-checked', 'false');
                    b.classList.remove('border-accent', 'bg-accent/5', 'shadow-sm');
                });
                this.setAttribute('aria-checked', 'true');
                this.classList.add('border-accent', 'bg-accent/5', 'shadow-sm');
                selectedThickness = this.getAttribute('data-thickness');
                syncBaseOptions();
                calculatePlakat();
            });
        });

        // Handler Pilihan Dudukan
        baseButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                baseButtons.forEach(b => {
                    b.setAttribute('aria-checked', 'false');
                    b.classList.remove('border-accent', 'bg-accent/5', 'shadow-sm');
                });
                this.setAttribute('aria-checked', 'true');
                this.classList.add('border-accent', 'bg-accent/5', 'shadow-sm');
                selectedBase = this.getAttribute('data-base');
                calculatePlakat();
            });
        });

        // Handler Pilihan Kemasan
        boxButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                boxButtons.forEach(b => {
                    b.setAttribute('aria-checked', 'false');
                    b.classList.remove('border-accent', 'bg-accent/5', 'shadow-sm');
                });
                this.setAttribute('aria-checked', 'true');
                this.classList.add('border-accent', 'bg-accent/5', 'shadow-sm');
                selectedBox = this.getAttribute('data-box');
                calculatePlakat();
            });
        });

        // Handler Checkbox Desain
        if (checkDesign) {
            checkDesign.addEventListener('change', function() {
                hasDesignFee = this.checked;
                calculatePlakat();
            });
        }

        // Handler Preset Kuantiti Cepat
        quickQtyButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                quickQtyButtons.forEach(b => b.classList.remove('is-active', 'border-accent', 'bg-accent', 'text-white'));
                this.classList.add('is-active', 'border-accent', 'bg-accent', 'text-white');
                currentQty = parseInt(this.getAttribute('data-qty')) || 1;
                if (qtyInput) qtyInput.value = currentQty;
                calculatePlakat();
            });
        });

        // Stepper Minus & Plus
        if (qtyMinus) {
            qtyMinus.addEventListener('click', function() {
                if (currentQty > 1) {
                    currentQty--;
                    if (qtyInput) qtyInput.value = currentQty;
                    syncQuickButtons();
                    calculatePlakat();
                }
            });
        }

        if (qtyPlus) {
            qtyPlus.addEventListener('click', function() {
                currentQty++;
                if (qtyInput) qtyInput.value = currentQty;
                syncQuickButtons();
                calculatePlakat();
            });
        }

        if (qtyInput) {
            qtyInput.addEventListener('input', function() {
                let val = parseInt(this.value);
                if (isNaN(val) || val < 1) val = 1;
                currentQty = val;
                syncQuickButtons();
                calculatePlakat();
            });
        }

        function syncQuickButtons() {
            quickQtyButtons.forEach(btn => {
                const bQty = parseInt(btn.getAttribute('data-qty'));
                if (bQty === currentQty) {
                    btn.classList.add('is-active', 'border-accent', 'bg-accent', 'text-white');
                } else {
                    btn.classList.remove('is-active', 'border-accent', 'bg-accent', 'text-white');
                }
            });
        }

        if (customNotes) {
            customNotes.addEventListener('input', function() {
                calculatePlakat();
            });
        }

        // Initial setup
        syncBaseOptions();
        calculatePlakat();
    });


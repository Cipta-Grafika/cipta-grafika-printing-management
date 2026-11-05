    document.addEventListener('DOMContentLoaded', function() {
        const paperButtons = document.querySelectorAll('.nota-choice-paper');
        const plyButtons = document.querySelectorAll('.nota-choice-ply');
        const inkButtons = document.querySelectorAll('.nota-choice-ink');
        const sizeButtons = document.querySelectorAll('.nota-choice-size');
        const quickQtyButtons = document.querySelectorAll('.nota-quick-qty');
        const qtyInput = document.getElementById('notaQtyInput');
        const qtyMinus = document.getElementById('notaQtyMinus');
        const qtyPlus = document.getElementById('notaQtyPlus');
        const checkNumerator = document.getElementById('checkNumerator');
        const checkDesign = document.getElementById('checkDesignService');
        const customNotes = document.getElementById('notaCustomNotes');

        const notaSheetInfo = document.getElementById('notaSheetInfo');
        const notaTotalBooksBanner = document.getElementById('notaTotalBooksBanner');
        const btnPly1 = document.getElementById('btnPly1');
        const btnPly2 = document.getElementById('btnPly2');
        const btnPly3 = document.getElementById('btnPly3');
        const btnPly4 = document.getElementById('btnPly4');
        const btnInk3 = document.getElementById('btnInk3');

        const summaryPaper = document.getElementById('summaryNotaPaper');
        const summaryPly = document.getElementById('summaryNotaPly');
        const summaryInk = document.getElementById('summaryNotaInk');
        const summarySize = document.getElementById('summaryNotaSize');
        const summaryPackages = document.getElementById('summaryNotaPackages');
        const summaryTotalBooks = document.getElementById('summaryNotaTotalBooks');
        const summaryPackagePrice = document.getElementById('summaryNotaPackagePrice');
        const summaryDesignFee = document.getElementById('summaryNotaDesignFee');
        const summaryFinishing = document.getElementById('summaryNotaFinishing');
        const summaryTotalPrice = document.getElementById('summaryNotaTotalPrice');
        const summaryPerBookPrice = document.getElementById('summaryNotaPerBookPrice');

        const btnWhatsApp = document.getElementById('btnNotaWhatsApp');
        const btnAddToCart = document.getElementById('btnNotaAddToCart');

        const whatsappNumber = '6285212821022';
        const DESIGN_FEE_AMOUNT = 30000;

        // Master data varian nota dari database
        const dbItems = @json($notaItems ?? []);
        let notaCatalog = [];

        if (Array.isArray(dbItems) && dbItems.length > 0) {
            notaCatalog = dbItems.map(item => ({
                id: item.id,
                name: item.name,
                paper_type: item.paper_type,
                ply_count: parseInt(item.ply_count),
                ply_label: item.ply_label,
                ink_count: parseInt(item.ink_count),
                ink_label: item.ink_label,
                sheets_per_book: parseInt(item.sheets_per_book),
                price: parseFloat(item.price)
            }));
        }

        // State aktif
        let selectedPaper = 'ncr';
        let selectedPly = 2;
        let selectedInk = 1;
        let selectedSize = '1/4';
        let booksPerPackage = 40;
        let sizeLabel = '1/4 Folio (± 10.5 x 16.5 cm)';
        let currentQty = parseInt(qtyInput?.value || 1);
        let hasDesignFee = false;
        let hasNumerator = false;

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(number);
        }

        // Sinkronisasi opsi Ply & Tinta ketika jenis kertas berubah
        function syncPaperDependentOptions() {
            if (selectedPaper === 'hvs') {
                // HVS hanya 1 ply (100 sheet/buku)
                if (btnPly1) btnPly1.style.display = 'block';
                if (btnPly2) btnPly2.style.display = 'none';
                if (btnPly3) btnPly3.style.display = 'none';
                if (btnPly4) btnPly4.style.display = 'none';
                selectedPly = 1;

                // HVS hanya ada 1 Tinta & 2 Tinta
                if (btnInk3) {
                    btnInk3.style.display = 'none';
                    if (selectedInk === 3) selectedInk = 1;
                }

                if (notaSheetInfo) {
                    notaSheetInfo.textContent = 'Isi: 100 Lembar per Buku (1 Ply Putih)';
                }
            } else {
                // NCR: 2, 3, 4 Ply (50 set/buku)
                if (btnPly1) btnPly1.style.display = 'none';
                if (btnPly2) btnPly2.style.display = 'block';
                if (btnPly3) btnPly3.style.display = 'block';
                if (btnPly4) btnPly4.style.display = 'block';
                if (selectedPly === 1) selectedPly = 2;

                // Tinta 3 tersedia untuk NCR
                if (btnInk3) btnInk3.style.display = 'block';

                if (notaSheetInfo) {
                    notaSheetInfo.textContent = 'Isi: 50 Set per Buku (Tembus Tanpa Karbon)';
                }
            }

            // Perbarui visual state button Ply
            plyButtons.forEach(btn => {
                const plyVal = parseInt(btn.getAttribute('data-ply'));
                const isChecked = plyVal === selectedPly;
                btn.setAttribute('aria-checked', isChecked ? 'true' : 'false');
                if (isChecked) {
                    btn.classList.add('border-accent', 'bg-accent/5', 'shadow-sm');
                } else {
                    btn.classList.remove('border-accent', 'bg-accent/5', 'shadow-sm');
                }
            });

            // Perbarui visual state button Ink
            inkButtons.forEach(btn => {
                const inkVal = parseInt(btn.getAttribute('data-ink'));
                const isChecked = inkVal === selectedInk;
                btn.setAttribute('aria-checked', isChecked ? 'true' : 'false');
                if (isChecked) {
                    btn.classList.add('border-accent', 'bg-accent/5', 'shadow-sm');
                } else {
                    btn.classList.remove('border-accent', 'bg-accent/5', 'shadow-sm');
                }
            });
        }

        function calculateNota() {
            let matchedItem = null;

            if (notaCatalog.length > 0) {
                matchedItem = notaCatalog.find(item =>
                    item.paper_type === selectedPaper &&
                    item.ply_count === selectedPly &&
                    item.ink_count === selectedInk
                );
            }

            const packagePrice = matchedItem ? matchedItem.price : 0;
            const packageSubtotal = packagePrice * currentQty;
            const designFee = hasDesignFee ? DESIGN_FEE_AMOUNT : 0;
            const totalPrice = packageSubtotal + designFee;
            const totalBooks = booksPerPackage * currentQty;
            const perBookPrice = totalBooks > 0 ? Math.round(totalPrice / totalBooks) : 0;

            // Update UI teks
            if (notaTotalBooksBanner) {
                notaTotalBooksBanner.textContent = totalBooks + ' Buku';
            }

            if (summaryPaper) {
                summaryPaper.textContent = selectedPaper === 'ncr' 
                    ? 'Kertas NCR Carbonless (50 Set)' 
                    : 'Kertas HVS Putih (100 Lembar)';
            }

            if (summaryPly) {
                summaryPly.textContent = selectedPaper === 'ncr' 
                    ? selectedPly + ' Ply (' + selectedPly + ' Rangkap Tembus)' 
                    : '1 Ply (1 Lembar Tanpa Tembusan)';
            }

            if (summaryInk) {
                summaryInk.textContent = selectedInk + ' Warna Tinta Cetak';
            }

            if (summarySize) {
                summarySize.textContent = sizeLabel;
            }

            if (summaryPackages) {
                summaryPackages.textContent = currentQty + ' Paket (' + currentQty + ' Rim)';
            }

            if (summaryTotalBooks) {
                summaryTotalBooks.textContent = totalBooks + ' Buku';
            }

            if (summaryPackagePrice) {
                summaryPackagePrice.textContent = formatRupiah(packagePrice);
            }

            if (summaryDesignFee) {
                summaryDesignFee.textContent = hasDesignFee ? formatRupiah(DESIGN_FEE_AMOUNT) : 'Rp 0 (Punya Sendiri)';
            }

            if (summaryFinishing) {
                const finishingText = hasNumerator 
                    ? 'Porporasi + Jilid Lem + Nomorator' 
                    : 'Porporasi + Jilid Lem Standar';
                summaryFinishing.textContent = finishingText;
            }

            if (summaryTotalPrice) {
                summaryTotalPrice.textContent = formatRupiah(totalPrice);
            }

            if (summaryPerBookPrice) {
                summaryPerBookPrice.textContent = formatRupiah(perBookPrice) + ' / buku';
            }

            // Highlight baris tabel yang cocok
            const tableRows = document.querySelectorAll('.nota-row-highlight');
            tableRows.forEach(row => {
                const rPaper = row.getAttribute('data-paper');
                const rPly = parseInt(row.getAttribute('data-ply'));
                const rInk = parseInt(row.getAttribute('data-ink'));

                if (rPaper === selectedPaper && rPly === selectedPly && rInk === selectedInk) {
                    row.classList.add('bg-accent/10', 'font-bold', 'border-l-4', 'border-accent');
                } else {
                    row.classList.remove('bg-accent/10', 'font-bold', 'border-l-4', 'border-accent');
                }
            });

            // Update WhatsApp CTA link
            updateWhatsAppLink(matchedItem, packagePrice, packageSubtotal, designFee, totalPrice, totalBooks, perBookPrice);
        }

        function updateWhatsAppLink(item, packagePrice, packageSubtotal, designFee, totalPrice, totalBooks, perBookPrice) {
            if (!btnWhatsApp) return;

            const notesText = customNotes && customNotes.value.trim() !== '' ? customNotes.value.trim() : '-';
            const paperName = selectedPaper === 'ncr' ? 'Kertas NCR Carbonless (50 set/buku)' : 'Kertas HVS Putih (100 lembar/buku)';
            const plyName = selectedPaper === 'ncr' ? selectedPly + ' Ply' : '1 Ply';
            const numeratorText = hasNumerator ? 'Ya (Numerator Urut)' : 'Tidak';
            const designText = hasDesignFee ? 'Ya (+ Rp 30.000)' : 'Tidak (File Siap Cetak)';

            let message = `Halo Cipta Grafika, saya ingin pesan cetak Buku Nota Form Bisnis:\n\n`;
            message += `*Spesifikasi Pesanan:*\n`;
            message += `• Jenis Kertas: ${paperName}\n`;
            message += `• Rangkap (Ply): ${plyName}\n`;
            message += `• Tinta Cetak: ${selectedInk} Warna Tinta\n`;
            message += `• Ukuran Buku: ${sizeLabel}\n`;
            message += `• Jumlah Paket: ${currentQty} Paket (${currentQty} Rim)\n`;
            message += `• Estimasi Hasil Buku: ${totalBooks} Buku\n`;
            message += `• Nomorator Mesin: ${numeratorText}\n`;
            message += `• Jasa Desain/Setting: ${designText}\n`;
            message += `• Catatan Form/Toko: ${notesText}\n\n`;
            message += `*Rincian Estimasi Biaya:*\n`;
            message += `• Harga per Paket: ${formatRupiah(packagePrice)}\n`;
            if (hasDesignFee) {
                message += `• Jasa Desain: ${formatRupiah(designFee)}\n`;
            }
            message += `• Total Estimasi: ${formatRupiah(totalPrice)} (± ${formatRupiah(perBookPrice)}/buku)\n\n`;
            message += `Mohon dibantu verifikasi dan langkah selanjutnya. Terima kasih!`;

            btnWhatsApp.href = `https://api.whatsapp.com/send?phone=${whatsappNumber}&text=${encodeURIComponent(message)}`;
        }

        // =========================================
        // EVENT LISTENERS
        // =========================================

        // 1. Pilihan Kertas (NCR vs HVS)
        paperButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                paperButtons.forEach(b => {
                    b.setAttribute('aria-checked', 'false');
                    b.classList.remove('border-accent', 'bg-accent/5', 'shadow-sm');
                });
                this.setAttribute('aria-checked', 'true');
                this.classList.add('border-accent', 'bg-accent/5', 'shadow-sm');

                selectedPaper = this.getAttribute('data-paper');
                syncPaperDependentOptions();
                calculateNota();
            });
        });

        // 2. Pilihan Ply
        plyButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                plyButtons.forEach(b => {
                    b.setAttribute('aria-checked', 'false');
                    b.classList.remove('border-accent', 'bg-accent/5', 'shadow-sm');
                });
                this.setAttribute('aria-checked', 'true');
                this.classList.add('border-accent', 'bg-accent/5', 'shadow-sm');

                selectedPly = parseInt(this.getAttribute('data-ply'));
                calculateNota();
            });
        });

        // 3. Pilihan Tinta
        inkButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                inkButtons.forEach(b => {
                    b.setAttribute('aria-checked', 'false');
                    b.classList.remove('border-accent', 'bg-accent/5', 'shadow-sm');
                });
                this.setAttribute('aria-checked', 'true');
                this.classList.add('border-accent', 'bg-accent/5', 'shadow-sm');

                selectedInk = parseInt(this.getAttribute('data-ink'));
                calculateNota();
            });
        });

        // 4. Pilihan Ukuran
        sizeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                sizeButtons.forEach(b => {
                    b.setAttribute('aria-checked', 'false');
                    b.classList.remove('border-accent', 'bg-accent/5', 'shadow-sm');
                });
                this.setAttribute('aria-checked', 'true');
                this.classList.add('border-accent', 'bg-accent/5', 'shadow-sm');

                selectedSize = this.getAttribute('data-size');
                booksPerPackage = parseInt(this.getAttribute('data-books'));

                switch (selectedSize) {
                    case '1/4':
                        sizeLabel = '1/4 Folio (± 10.5 x 16.5 cm)';
                        break;
                    case '1/2':
                        sizeLabel = '1/2 Folio (± 16.5 x 21.5 cm)';
                        break;
                    case '1/3':
                        sizeLabel = '1/3 Folio (± 10.8 x 21.5 cm)';
                        break;
                    case '1':
                        sizeLabel = '1 Folio Penuh (± 21.5 x 33 cm)';
                        break;
                    default:
                        sizeLabel = selectedSize;
                }

                calculateNota();
            });
        });

        // 5. Preset Qty
        quickQtyButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                quickQtyButtons.forEach(b => b.classList.remove('is-active', 'bg-accent', 'text-white', 'border-accent'));
                this.classList.add('is-active', 'bg-accent', 'text-white', 'border-accent');

                currentQty = parseInt(this.getAttribute('data-qty'));
                if (qtyInput) qtyInput.value = currentQty;
                calculateNota();
            });
        });

        // Stepper Qty
        if (qtyMinus) {
            qtyMinus.addEventListener('click', function() {
                if (currentQty > 1) {
                    currentQty--;
                    if (qtyInput) qtyInput.value = currentQty;
                    syncQuickQtyState();
                    calculateNota();
                }
            });
        }

        if (qtyPlus) {
            qtyPlus.addEventListener('click', function() {
                currentQty++;
                if (qtyInput) qtyInput.value = currentQty;
                syncQuickQtyState();
                calculateNota();
            });
        }

        if (qtyInput) {
            qtyInput.addEventListener('input', function() {
                let val = parseInt(this.value);
                if (isNaN(val) || val < 1) val = 1;
                currentQty = val;
                syncQuickQtyState();
                calculateNota();
            });
        }

        function syncQuickQtyState() {
            quickQtyButtons.forEach(btn => {
                const q = parseInt(btn.getAttribute('data-qty'));
                if (q === currentQty) {
                    btn.classList.add('is-active', 'bg-accent', 'text-white', 'border-accent');
                } else {
                    btn.classList.remove('is-active', 'bg-accent', 'text-white', 'border-accent');
                }
            });
        }

        // 6. Checkboxes & Notes
        if (checkNumerator) {
            checkNumerator.addEventListener('change', function() {
                hasNumerator = this.checked;
                calculateNota();
            });
        }

        if (checkDesign) {
            checkDesign.addEventListener('change', function() {
                hasDesignFee = this.checked;
                calculateNota();
            });
        }

        if (customNotes) {
            customNotes.addEventListener('input', function() {
                calculateNota();
            });
        }

        // Inisialisasi awal
        syncPaperDependentOptions();
        calculateNota();
    });


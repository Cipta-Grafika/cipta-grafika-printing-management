{{-- =====================================================================
     BUKU NOTA (NCR & HVS) ESTIMATOR (VERSI USER / CUSTOMER)
     ===================================================================== --}}
<div class="estimator reveal d2 grid grid-cols-1 gap-4 py-8 border-t border-b border-zinc-100 dark:border-zinc-900"
    style="margin-top: 20px; border-bottom: none !important;">

    <!-- =========================
         HEADER
    ========================== -->
    <div>
        <p class="font-medium text-zinc-900 dark:text-white text-lg flex items-center gap-2">
            <span>Estimator Buku Nota &amp; Form Bisnis (NCR &amp; HVS)</span>
            <span class="text-xs px-2.5 py-0.5 rounded-full bg-accent/15 text-accent font-semibold border border-accent/20">Offset Commercial Print</span>
        </p>
        <p class="text-xs text-zinc-400 tracking-widest mb-1">
            Cetak buku nota penjualan, invoice, kwitansi, surat jalan, tanda terima kantor, dan surat pesanan. Kertas NCR carbonless berkualitas (tembus otomatis tanpa kertas karbon) &amp; form HVS putih tebal dengan finishing porporasi rapi dan jilid kuat.
        </p>
    </div>

    <!-- =========================
         ESTIMATOR FORM & SUMMARY
    ========================== -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">

        <!-- =========================
             LEFT FORM (2 COLS)
        ========================== -->
        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- 1. Pilihan Jenis Kertas & Form --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    1. Pilihan Jenis Kertas &amp; Karakter Form <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons grid grid-cols-1 sm:grid-cols-2 gap-3" id="notaPaperButtons">
                    <button type="button" class="estimator-choice-button nota-choice-paper text-left p-3.5 rounded-xl border transition-all"
                        data-paper="ncr" aria-checked="true">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm flex items-center gap-2">
                                <span>📄</span> <span>Kertas NCR Carbonless</span>
                            </span>
                            <span class="text-xs font-bold text-accent">Mulai Rp 329.000</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Tembus otomatis ke lembar bawah tanpa karbon. Isi <strong>50 set per buku</strong>. Pilihan 2, 3, hingga 4 Ply.
                        </div>
                    </button>

                    <button type="button" class="estimator-choice-button nota-choice-paper text-left p-3.5 rounded-xl border transition-all"
                        data-paper="hvs" aria-checked="false">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm flex items-center gap-2">
                                <span>📝</span> <span>Kertas HVS Putih (1 Ply)</span>
                            </span>
                            <span class="text-xs font-bold text-accent">Mulai Rp 200.000</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Form 1 lembar tanpa tembusan, kertas putih bersih &amp; tebal. Isi <strong>100 lembar per buku</strong>.
                        </div>
                    </button>
                </div>
            </div>

            {{-- 2. Pilihan Rangkap / Ply --}}
            <div class="sm:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        2. Pilihan Jumlah Rangkap (Ply) <span class="text-red-500">*</span>
                    </label>
                    <span id="notaSheetInfo" class="text-[11px] text-accent font-semibold">Isi: 50 Set per Buku</span>
                </div>
                <div class="estimator-choice-buttons grid grid-cols-2 sm:grid-cols-4 gap-2.5" id="notaPlyButtons">
                    <button type="button" class="estimator-choice-button nota-choice-ply text-center p-2.5 rounded-xl border transition-all"
                        data-ply="1" aria-checked="false" id="btnPly1" style="display: none;">
                        <div class="font-bold text-sm">1 Ply</div>
                        <div class="text-[10.5px] text-zinc-400 mt-0.5">100 Lembar/Buku</div>
                    </button>
                    <button type="button" class="estimator-choice-button nota-choice-ply text-center p-2.5 rounded-xl border transition-all"
                        data-ply="2" aria-checked="true" id="btnPly2">
                        <div class="font-bold text-sm">2 Ply</div>
                        <div class="text-[10.5px] text-zinc-400 mt-0.5">50 Set (Rangkap 2)</div>
                    </button>
                    <button type="button" class="estimator-choice-button nota-choice-ply text-center p-2.5 rounded-xl border transition-all"
                        data-ply="3" aria-checked="false" id="btnPly3">
                        <div class="font-bold text-sm">3 Ply</div>
                        <div class="text-[10.5px] text-zinc-400 mt-0.5">50 Set (Rangkap 3)</div>
                    </button>
                    <button type="button" class="estimator-choice-button nota-choice-ply text-center p-2.5 rounded-xl border transition-all"
                        data-ply="4" aria-checked="false" id="btnPly4">
                        <div class="font-bold text-sm">4 Ply</div>
                        <div class="text-[10.5px] text-zinc-400 mt-0.5">50 Set (Rangkap 4)</div>
                    </button>
                </div>
            </div>

            {{-- 3. Pilihan Jumlah Warna Tinta --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    3. Pilihan Jumlah Warna Tinta Cetak <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons grid grid-cols-1 sm:grid-cols-3 gap-2.5" id="notaInkButtons">
                    <button type="button" class="estimator-choice-button nota-choice-ink text-left p-3 rounded-xl border transition-all"
                        data-ink="1" aria-checked="true">
                        <div class="font-bold text-sm flex items-center justify-between">
                            <span>1 Warna Tinta</span>
                            <span class="w-3 h-3 rounded-full bg-zinc-800 dark:bg-zinc-200"></span>
                        </div>
                        <div class="text-[11px] text-zinc-400 mt-1">Hitam, Biru Reflex, atau Merah standar. Paling ekonomis &amp; populer.</div>
                    </button>

                    <button type="button" class="estimator-choice-button nota-choice-ink text-left p-3 rounded-xl border transition-all"
                        data-ink="2" aria-checked="false">
                        <div class="font-bold text-sm flex items-center justify-between">
                            <span>2 Warna Tinta</span>
                            <span class="flex gap-0.5">
                                <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                                <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>
                            </span>
                        </div>
                        <div class="text-[11px] text-zinc-400 mt-1">Kombinasi 2 warna, misal Biru untuk kop/logo + Hitam untuk tabel.</div>
                    </button>

                    <button type="button" class="estimator-choice-button nota-choice-ink text-left p-3 rounded-xl border transition-all"
                        data-ink="3" aria-checked="false" id="btnInk3">
                        <div class="font-bold text-sm flex items-center justify-between">
                            <span>3 Warna Tinta</span>
                            <span class="flex gap-0.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                <span class="w-2 h-2 rounded-full bg-red-600"></span>
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            </span>
                        </div>
                        <div class="text-[11px] text-zinc-400 mt-1">Kombinasi 3 warna tinta tajam untuk identitas brand profesional.</div>
                    </button>
                </div>
            </div>

            {{-- 4. Pilihan Ukuran Potongan Buku (Hasil Jadi per Paket 1 Rim) --}}
            <div class="sm:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        4. Ukuran Buku Jadi (Hasil per 1 Paket Cetak) <span class="text-red-500">*</span>
                    </label>
                    <span class="text-[11px] text-zinc-400">Standar Folio F4 (21.5 x 33 cm)</span>
                </div>
                <div class="estimator-choice-buttons grid grid-cols-2 sm:grid-cols-4 gap-2.5" id="notaSizeButtons">
                    <button type="button" class="estimator-choice-button nota-choice-size text-center p-3 rounded-xl border transition-all"
                        data-size="1/4" data-books="40" aria-checked="true">
                        <div class="font-bold text-sm text-accent">1/4 Folio</div>
                        <div class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 mt-0.5">Dapat 40 Buku</div>
                        <div class="text-[10px] text-zinc-400 mt-0.5">± 10.5 x 16.5 cm (Nota Kasir)</div>
                    </button>

                    <button type="button" class="estimator-choice-button nota-choice-size text-center p-3 rounded-xl border transition-all"
                        data-size="1/2" data-books="20" aria-checked="false">
                        <div class="font-bold text-sm text-accent">1/2 Folio</div>
                        <div class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 mt-0.5">Dapat 20 Buku</div>
                        <div class="text-[10px] text-zinc-400 mt-0.5">± 16.5 x 21.5 cm (Invoice/PO)</div>
                    </button>

                    <button type="button" class="estimator-choice-button nota-choice-size text-center p-3 rounded-xl border transition-all"
                        data-size="1/3" data-books="30" aria-checked="false">
                        <div class="font-bold text-sm text-accent">1/3 Folio</div>
                        <div class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 mt-0.5">Dapat 30 Buku</div>
                        <div class="text-[10px] text-zinc-400 mt-0.5">± 10.8 x 21.5 cm (Tanda Terima)</div>
                    </button>

                    <button type="button" class="estimator-choice-button nota-choice-size text-center p-3 rounded-xl border transition-all"
                        data-size="1" data-books="10" aria-checked="false">
                        <div class="font-bold text-sm text-accent">1 Folio Penuh</div>
                        <div class="text-xs font-semibold text-zinc-800 dark:text-zinc-200 mt-0.5">Dapat 10 Buku</div>
                        <div class="text-[10px] text-zinc-400 mt-0.5">± 21.5 x 33 cm (Surat Jalan/SPK)</div>
                    </button>
                </div>
            </div>

            {{-- 5. Jumlah Paket Cetak (Rim) --}}
            <div class="sm:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        5. Jumlah Paket Cetak (Rim Folio) <span class="text-red-500">*</span>
                    </label>
                    <span class="text-xs text-zinc-400 font-medium">1 Paket = 1 Rim Plano Folio</span>
                </div>

                {{-- Preset Cepat --}}
                <div class="flex flex-wrap gap-2 mb-3">
                    <button type="button" class="nota-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] text-zinc-700 dark:text-zinc-300 transition-colors is-active" data-qty="1">1 Paket (1 Rim)</button>
                    <button type="button" class="nota-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors" data-qty="2">2 Paket (2 Rim)</button>
                    <button type="button" class="nota-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors" data-qty="3">3 Paket (3 Rim)</button>
                    <button type="button" class="nota-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors" data-qty="5">5 Paket (5 Rim)</button>
                    <button type="button" class="nota-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors" data-qty="10">10 Paket (10 Rim)</button>
                </div>

                {{-- Input Stepper --}}
                <div class="flex items-center gap-3">
                    <button type="button" id="notaQtyMinus"
                        class="w-10 h-10 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent flex items-center justify-center text-lg font-bold text-zinc-700 dark:text-zinc-200 transition-colors">
                        −
                    </button>
                    <input type="number" id="notaQtyInput" min="1" step="1" value="1"
                        class="w-32 text-center text-base font-bold bg-white dark:bg-[#16294d] border border-zinc-300 dark:border-zinc-700 rounded-lg py-2 text-zinc-900 dark:text-white focus:outline-none focus:border-accent">
                    <button type="button" id="notaQtyPlus"
                        class="w-10 h-10 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent flex items-center justify-center text-lg font-bold text-zinc-700 dark:text-zinc-200 transition-colors">
                        +
                    </button>
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">Paket Rim (Total Buku Jadi: <strong id="notaTotalBooksBanner" class="text-accent font-bold">40 Buku</strong>)</span>
                </div>
            </div>

            {{-- 6. Opsi Tambahan (Finishing & Desain) --}}
            <div class="sm:col-span-2 space-y-3 pt-2">
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                    6. Opsi Tambahan &amp; Desain:
                </label>

                {{-- Checkbox Nomorator --}}
                <label class="custom-checkbox-card flex items-start gap-3.5 p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700/60 hover:border-accent/60 bg-white dark:bg-[#16294d] cursor-pointer transition-all select-none">
                    <input type="checkbox" id="checkNumerator" class="custom-checkbox-input sr-only">
                    <div class="custom-checkbox-box w-5 h-5 rounded-lg border border-zinc-300 dark:border-white/20 bg-zinc-100 dark:bg-white/10 flex items-center justify-center text-white shrink-0 mt-0.5 transition-all">
                        <i class="bi bi-check-lg text-xs font-black opacity-0 transition-all duration-150 transform scale-50"></i>
                    </div>
                    <div class="text-xs flex-1">
                        <span class="font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                            <span>🔢 Tambah Nomorator (Numerator Otomatis 6 Digit)</span>
                            <span class="text-[10px] px-2 py-0.5 rounded bg-accent/15 text-accent font-bold border border-accent/25">Gratis / Standar</span>
                        </span>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5 leading-relaxed">
                            Penomoran otomatis berurutan (misal: No. 000001 s.d selesai) di sudut form nota untuk pencatatan rapi.
                        </p>
                    </div>
                </label>

                {{-- Checkbox Jasa Desain --}}
                <label class="custom-checkbox-card flex items-start gap-3.5 p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700/60 hover:border-accent/60 bg-white dark:bg-[#16294d] cursor-pointer transition-all select-none">
                    <input type="checkbox" id="checkDesignService" class="custom-checkbox-input sr-only">
                    <div class="custom-checkbox-box w-5 h-5 rounded-lg border border-zinc-300 dark:border-white/20 bg-zinc-100 dark:bg-white/10 flex items-center justify-center text-white shrink-0 mt-0.5 transition-all">
                        <i class="bi bi-check-lg text-xs font-black opacity-0 transition-all duration-150 transform scale-50"></i>
                    </div>
                    <div class="text-xs flex-1">
                        <span class="font-semibold text-zinc-900 dark:text-white flex items-center gap-2">
                            <span>🎨 Jasa Pembuatan / Setting Desain Nota Form (+ Rp 30.000)</span>
                        </span>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5 leading-relaxed">
                            Centang jika Anda belum memiliki file desain siap cetak (PDF/Corel/AI) atau butuh dibuatkan layout nota baru dari awal.
                        </p>
                    </div>
                </label>
            </div>

            {{-- 7. Catatan Custom Pesanan --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                    7. Keterangan Custom Nota <span class="text-zinc-400 dark:text-zinc-500 font-normal">(Nama Toko, Warna Tinta, Nomor Awal, dll.)</span>
                </label>
                <textarea id="notaCustomNotes" rows="2"
                    placeholder="Contoh: Nama Toko: Toko Berkah Abadi, Telp: 0812..., Warna tinta: Biru BCA, Nomorator mulai dari: 001001, Porporasi atas."
                    class="w-full text-xs p-3 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent"></textarea>
            </div>

        </div>

        <!-- =========================
             RIGHT SUMMARY CARD (1 COL)
        ========================== -->
        <div class="lg:col-span-1">
            <div class="h-fit rounded-2xl p-5 border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-white/5 lg:sticky lg:top-24 space-y-4">
                
                <h4 class="font-semibold text-sm text-zinc-900 dark:text-white pb-3 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                    <span>Ringkasan Estimasi Nota</span>
                    <span class="text-xs text-accent font-semibold flex items-center gap-1">
                        <i class="bi bi-shield-check"></i> Harga Pasti
                    </span>
                </h4>

                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Jenis Kertas:</span>
                        <span id="summaryNotaPaper" class="font-semibold text-zinc-900 dark:text-white text-right">Kertas NCR Carbonless</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Rangkap (Ply):</span>
                        <span id="summaryNotaPly" class="font-semibold text-zinc-900 dark:text-white">2 Ply (50 Set/Buku)</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Warna Tinta:</span>
                        <span id="summaryNotaInk" class="font-semibold text-zinc-900 dark:text-white">1 Warna Tinta</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Ukuran Buku:</span>
                        <span id="summaryNotaSize" class="font-semibold text-zinc-900 dark:text-white">1/4 Folio (10.5x16.5 cm)</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Jumlah Paket:</span>
                        <span id="summaryNotaPackages" class="font-semibold text-zinc-900 dark:text-white">1 Paket (1 Rim)</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400 bg-accent/10 p-2.5 rounded-xl border border-accent/25">
                        <span class="font-medium text-accent">Total Hasil Buku:</span>
                        <span id="summaryNotaTotalBooks" class="font-bold text-accent text-sm">40 Buku</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Harga per Paket:</span>
                        <span id="summaryNotaPackagePrice" class="font-bold text-zinc-900 dark:text-white">Rp 329.000</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400" id="rowSummaryDesignFee">
                        <span>Jasa Desain:</span>
                        <span id="summaryNotaDesignFee" class="font-semibold text-zinc-900 dark:text-white">Rp 0 (Punya Sendiri)</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Finishing:</span>
                        <span id="summaryNotaFinishing" class="font-medium text-zinc-700 dark:text-zinc-300 text-right">Porporasi + Jilid Lem</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="flex justify-between items-baseline mb-1">
                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Estimasi:</span>
                        <span id="summaryNotaTotalPrice" class="text-2xl font-extrabold text-accent">
                            Rp 329.000
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-zinc-500 dark:text-zinc-400 pt-1">
                        <span>Estimasi per Buku:</span>
                        <span id="summaryNotaPerBookPrice" class="font-bold text-accent text-sm">
                            Rp 8.225 / buku
                        </span>
                    </div>
                </div>

                {{-- Action Buttons (Konsisten Tema Emas & Responsive) --}}
                <div class="pt-2 space-y-2.5">
                    {{-- Order via WhatsApp Button (Emas Konsisten) --}}
                    <a id="btnNotaWhatsApp" href="#" target="_blank"
                        class="w-full py-3.5 px-4 rounded-xl bg-accent hover:opacity-90 active:scale-[0.99] text-white font-semibold text-xs sm:text-sm flex items-center justify-center gap-2 transition-all shadow-sm hover:shadow">
                        <i class="bi bi-whatsapp text-sm sm:text-base shrink-0"></i>
                        <span>Pesan via WhatsApp Sekarang</span>
                    </a>

                    {{-- Add to Cart Button (Konsisten Tema Emas & Terintegrasi Basket) --}}
                    <button type="button" id="btnNotaAddToCart"
                        @click="
                            isInBasket(@js($product['basketItem']['id']))
                                ? removeFromBasket(@js($product['basketItem']['id']))
                                : addToBasket(@js($product['basketItem']))
                        "
                        class="w-full py-3 rounded-xl cart-button text-xs sm:text-sm font-medium hover:opacity-90 transition-all duration-200 flex items-center justify-center gap-2">
                        <template x-if="!isInBasket(@js($product['basketItem']['id']))">
                            <span class="flex items-center justify-center gap-2">
                                <i class="bi bi-cart-plus text-sm sm:text-base shrink-0"></i>
                                <span>Tambah ke Keranjang</span>
                            </span>
                        </template>
                        <template x-if="isInBasket(@js($product['basketItem']['id']))">
                            <span class="flex items-center justify-center gap-2">
                                <i class="bi bi-cart-x text-sm sm:text-base shrink-0"></i>
                                <span>Hapus dari Keranjang</span>
                            </span>
                        </template>
                    </button>
                </div>

                {{-- CS Assistance note --}}
                <div class="text-[11px] text-zinc-400 dark:text-zinc-500 text-center flex items-center justify-center gap-1.5 pt-1">
                    <i class="bi bi-info-circle"></i>
                    <span>Butuh ukuran custom, warna kertas khusus, atau cetak ribuan buku? Hubungi CS kami.</span>
                </div>

            </div>
        </div>

    </div>
</div>


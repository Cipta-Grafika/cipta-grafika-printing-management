{{-- =====================================================================
     PLAKAT AKRILIK UV & KAYU ESTIMATOR (VERSI USER / CUSTOMER)
     ===================================================================== --}}
<div class="estimator reveal d2 grid grid-cols-1 gap-4 py-8 border-t border-b border-zinc-100 dark:border-zinc-900"
    style="margin-top: 20px; border-bottom: none !important;">

    <!-- =========================
         HEADER
    ========================== -->
    <div>
        <p class="font-medium text-zinc-900 dark:text-white text-lg flex items-center gap-2">
            <span>Estimator Plakat Akrilik UV &amp; Kayu (20x15 cm)</span>
            <span class="text-xs px-2.5 py-0.5 rounded-full bg-accent/15 text-accent font-semibold border border-accent/20">Cinderamata &amp; Trophy</span>
        </p>
        <p class="text-xs text-zinc-400 tracking-widest mb-1">
            Plakat custom eksklusif untuk kenang-kenangan seminar, wisuda, penghargaan kkn, magang, purna tugas, dan cinderamata instansi. Cetak UV flatbed definisi tinggi di media akrilik tebal &amp; plakat kayu solid mewah.
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

            {{-- 1. Pilihan Bahan Dasar Plakat --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    1. Pilihan Bahan Dasar Plakat <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons grid grid-cols-1 sm:grid-cols-2 gap-3" id="plakatMaterialButtons">
                    <button type="button" class="estimator-choice-button plakat-choice-material text-left p-3.5 rounded-xl border transition-all"
                        data-material="akrilik" aria-checked="true">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm flex items-center gap-2">
                                <span>✨</span> <span>Akrilik Cetak UV</span>
                            </span>
                            <span class="text-xs font-bold text-accent">Mulai Rp 66.000</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Akrilik bening kristal dengan cetak langsung flatbed UV tajam beresolusi tinggi, aneka ketebalan 2mm s.d 1cm.
                        </div>
                    </button>

                    <button type="button" class="estimator-choice-button plakat-choice-material text-left p-3.5 rounded-xl border transition-all"
                        data-material="kayu" aria-checked="false">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm flex items-center gap-2">
                                <span>🪵</span> <span>Plakat Kayu Solid + Box</span>
                            </span>
                            <span class="text-xs font-bold text-accent">Rp 180.000</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Bahan kayu finishing plitur mewah &amp; plat ukir logam/akrilik, sudah lengkap termasuk box beludru eksklusif.
                        </div>
                    </button>
                </div>
            </div>

            {{-- 2. Spesifikasi Khusus Akrilik (Ketebalan, Dudukan, Kemasan) --}}
            <div class="sm:col-span-2 space-y-4" id="sectionAkrilikOptions">
                
                {{-- 2A. Ketebalan Akrilik --}}
                <div>
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                        2. Ketebalan Plat Akrilik <span class="text-red-500">*</span>
                    </label>
                    <div class="estimator-choice-buttons grid grid-cols-2 sm:grid-cols-4 gap-2.5" id="plakatThicknessButtons">
                        <button type="button" class="estimator-choice-button plakat-choice-thickness text-center p-2.5 rounded-xl border transition-all"
                            data-thickness="2mm" aria-checked="false">
                            <div class="font-bold text-sm">2 mm</div>
                            <div class="text-[10.5px] text-zinc-400 mt-0.5">Ekonomis</div>
                        </button>
                        <button type="button" class="estimator-choice-button plakat-choice-thickness text-center p-2.5 rounded-xl border transition-all"
                            data-thickness="3mm" aria-checked="true">
                            <div class="font-bold text-sm">3 mm</div>
                            <div class="text-[10.5px] text-zinc-400 mt-0.5">Terfavorit</div>
                        </button>
                        <button type="button" class="estimator-choice-button plakat-choice-thickness text-center p-2.5 rounded-xl border transition-all"
                            data-thickness="5mm" aria-checked="false">
                            <div class="font-bold text-sm">5 mm</div>
                            <div class="text-[10.5px] text-zinc-400 mt-0.5">Tebal Premium</div>
                        </button>
                        <button type="button" class="estimator-choice-button plakat-choice-thickness text-center p-2.5 rounded-xl border transition-all"
                            data-thickness="1cm" aria-checked="false">
                            <div class="font-bold text-sm">1 cm (10mm)</div>
                            <div class="text-[10.5px] text-zinc-400 mt-0.5">Super Solid</div>
                        </button>
                    </div>
                </div>

                {{-- 2B. Model Dudukan / Tatakan Bawah --}}
                <div>
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                        3. Pilihan Tatakan / Dudukan Bawah <span class="text-red-500">*</span>
                    </label>
                    <div class="estimator-choice-buttons grid grid-cols-1 sm:grid-cols-2 gap-3" id="plakatBaseButtons">
                        <button type="button" class="estimator-choice-button plakat-choice-base text-left p-3 rounded-xl border transition-all"
                            data-base="akrilik" aria-checked="true">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-base">🧊</span>
                                <span class="font-semibold text-xs">Dudukan Akrilik Bening</span>
                            </div>
                            <div class="text-[11px] text-zinc-400">Tatakan bahan akrilik tebal senada, transparan dan minimalis modern.</div>
                        </button>
                        <button type="button" class="estimator-choice-button plakat-choice-base text-left p-3 rounded-xl border transition-all"
                            data-base="hitam" aria-checked="false" id="btnBaseHitam">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-base">⬛</span>
                                <span class="font-semibold text-xs">Dudukan Hitam Elegan</span>
                            </div>
                            <div class="text-[11px] text-zinc-400">Tatakan hitam pekat kokoh, kontras mewah (tersedia tebal 3mm &amp; 5mm).</div>
                        </button>
                    </div>
                </div>

                {{-- 2C. Pilihan Kemasan / Box Beludru --}}
                <div>
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                        4. Pilihan Kemasan / Packaging <span class="text-red-500">*</span>
                    </label>
                    <div class="estimator-choice-buttons grid grid-cols-1 sm:grid-cols-2 gap-3" id="plakatBoxButtons">
                        <button type="button" class="estimator-choice-button plakat-choice-box text-left p-3 rounded-xl border transition-all"
                            data-box="tanpa_box" aria-checked="false">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-base">🏷️</span>
                                <span class="font-semibold text-xs">Tanpa Box (Plakat Saja)</span>
                            </div>
                            <div class="text-[11px] text-zinc-400">Dipacking rapi dengan bubble wrap aman untuk pengiriman.</div>
                        </button>
                        <button type="button" class="estimator-choice-button plakat-choice-box text-left p-3 rounded-xl border transition-all"
                            data-box="box" aria-checked="true">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-base">🎁</span>
                                <span class="font-semibold text-xs">Termasuk Box Beludru Eksklusif (+ Box)</span>
                            </div>
                            <div class="text-[11px] text-zinc-400">Kotak beludru mewah berbusa presisi khusus plakat, siap diserahkan saat seremoni.</div>
                        </button>
                    </div>
                </div>

            </div>

            {{-- 3. Layanan Jasa Desain Grafis (Opsional) --}}
            <label class="custom-checkbox-card sm:col-span-2 p-3.5 rounded-xl border border-zinc-200 dark:border-zinc-700/60 hover:border-accent/60 bg-zinc-50 dark:bg-[#16294d] flex items-start gap-3.5 cursor-pointer transition-all select-none">
                <input type="checkbox" id="checkDesignService" class="custom-checkbox-input sr-only">
                <div class="custom-checkbox-box w-5 h-5 rounded-lg border border-zinc-300 dark:border-white/20 bg-zinc-100 dark:bg-white/10 flex items-center justify-center text-white shrink-0 mt-0.5 transition-all">
                    <i class="bi bi-check-lg text-xs font-black opacity-0 transition-all duration-150 transform scale-50"></i>
                </div>
                <div class="flex-1 text-xs">
                    <span class="font-bold text-zinc-800 dark:text-zinc-200 flex items-center justify-between">
                        <span>🎨 Bantuan Pembuatan Desain Grafis Plakat (+ Rp 35.000)</span>
                        <span class="text-[11px] font-normal text-zinc-400">Opsional</span>
                    </span>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 mt-0.5 leading-snug">
                        Centang jika Anda belum memiliki file desain siap cetak (vector/PDF hi-res). Tim desainer kami akan membuatkan layout tulisan, tata letak logo, dan background plakat sampai fix.
                    </p>
                </div>
            </label>

            {{-- 4. Kuantitas Pesanan (Pcs) --}}
            <div class="sm:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        5. Jumlah Pesanan Plakat <span class="text-red-500">*</span>
                    </label>
                    <span class="text-xs text-zinc-400 font-medium">Bisa pesan satuan (min. 1 pcs)</span>
                </div>

                {{-- Preset Cepat --}}
                <div class="flex flex-wrap gap-2 mb-3">
                    <button type="button" class="plakat-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors is-active" data-qty="1">1 pcs</button>
                    <button type="button" class="plakat-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors" data-qty="2">2 pcs</button>
                    <button type="button" class="plakat-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors" data-qty="5">5 pcs</button>
                    <button type="button" class="plakat-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors" data-qty="10">10 pcs</button>
                    <button type="button" class="plakat-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors" data-qty="25">25 pcs</button>
                    <button type="button" class="plakat-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors" data-qty="50">50 pcs</button>
                </div>

                {{-- Input Stepper --}}
                <div class="flex items-center gap-3">
                    <button type="button" id="plakatQtyMinus"
                        class="w-10 h-10 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent flex items-center justify-center text-lg font-bold text-zinc-700 dark:text-zinc-200 transition-colors">
                        −
                    </button>
                    <input type="number" id="plakatQtyInput" min="1" step="1" value="1"
                        class="w-32 text-center text-base font-bold bg-white dark:bg-[#16294d] border border-zinc-300 dark:border-zinc-700 rounded-lg py-2 text-zinc-900 dark:text-white focus:outline-none focus:border-accent">
                    <button type="button" id="plakatQtyPlus"
                        class="w-10 h-10 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent flex items-center justify-center text-lg font-bold text-zinc-700 dark:text-zinc-200 transition-colors">
                        +
                    </button>
                    <span class="text-xs text-zinc-400">Pcs Plakat</span>
                </div>
            </div>

            {{-- 5. Catatan Acara / Tulisan Plakat (Opsional) --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                    5. Keterangan Tulisan / Judul Acara <span class="text-zinc-400 dark:text-zinc-500 font-normal">(Opsional)</span>
                </label>
                <textarea id="plakatCustomNotes" rows="2"
                    placeholder="Contoh: Diberikan kepada Bapak Dr. Ir. ..., sebagai Narasumber Seminar Nasional ..., tanggal 25 Mei 2026."
                    class="w-full text-xs p-3 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] text-zinc-900 dark:text-white placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:border-accent focus:ring-1 focus:ring-accent"></textarea>
            </div>

        </div>

        <!-- =========================
             RIGHT SUMMARY CARD (1 COL)
        ========================== -->
        <div class="lg:col-span-1">
            <div class="h-fit rounded-2xl p-5 border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-white/5 lg:sticky lg:top-24 space-y-4">
                
                <h4 class="font-semibold text-sm text-zinc-900 dark:text-white pb-3 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                    <span>Ringkasan Estimasi</span>
                    <span class="text-xs text-accent font-semibold flex items-center gap-1">
                        <i class="bi bi-shield-check"></i> Harga Pasti
                    </span>
                </h4>

                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Bahan Plakat:</span>
                        <span id="summaryPlakatMaterial" class="font-semibold text-zinc-900 dark:text-white text-right">Akrilik Cetak UV</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400" id="summaryRowThickness">
                        <span>Ketebalan:</span>
                        <span id="summaryPlakatThickness" class="font-semibold text-zinc-900 dark:text-white">3 mm</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400" id="summaryRowBase">
                        <span>Dudukan Bawah:</span>
                        <span id="summaryPlakatBase" class="font-semibold text-zinc-900 dark:text-white">Dudukan Akrilik</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Kemasan:</span>
                        <span id="summaryPlakatBox" class="font-semibold text-zinc-900 dark:text-white">Termasuk Box Beludru</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Ukuran Standar:</span>
                        <span id="summaryPlakatSize" class="font-semibold text-zinc-900 dark:text-white">20 x 15 cm</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Kuantitas:</span>
                        <span id="summaryPlakatQty" class="font-semibold text-zinc-900 dark:text-white">1 pcs</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Harga Satuan Plakat:</span>
                        <span id="summaryPlakatUnitPrice" class="font-bold text-accent">Rp 210.000</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Jasa Desain:</span>
                        <span id="summaryPlakatDesignFee" class="font-semibold text-zinc-900 dark:text-white">Rp 0 (Punya Sendiri)</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="flex justify-between items-baseline mb-1">
                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Estimasi:</span>
                        <span id="summaryPlakatTotalPrice" class="text-2xl font-extrabold text-accent">
                            Rp 210.000
                        </span>
                    </div>
                    <div class="text-[11px] text-zinc-400 dark:text-zinc-500 text-right">
                        Sudah termasuk cetak UV flatbed &amp; finishing presisi.
                    </div>
                </div>

                {{-- Action Buttons (Konsisten Tema Emas & Responsive) --}}
                <div class="pt-2 space-y-2.5">
                    {{-- Order via WhatsApp Button (Emas Konsisten) --}}
                    <a id="btnPlakatWhatsApp" href="#" target="_blank"
                        class="w-full py-3.5 px-4 rounded-xl bg-accent hover:opacity-90 active:scale-[0.99] text-white font-semibold text-xs sm:text-sm flex items-center justify-center gap-2 transition-all shadow-sm hover:shadow">
                        <i class="bi bi-whatsapp text-sm sm:text-base shrink-0"></i>
                        <span>Pesan via WhatsApp Sekarang</span>
                    </a>

                    {{-- Add to Cart Button (Konsisten Tema Emas & Terintegrasi Basket) --}}
                    <button type="button" id="btnPlakatAddToCart"
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
                    <span>Butuh ukuran custom lain atau jumlah besar? Hubungi CS kami untuk penawaran khusus.</span>
                </div>

            </div>
        </div>

    </div>
</div>


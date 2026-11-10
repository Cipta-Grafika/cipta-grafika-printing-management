{{-- =====================================================================
     MUG SUBLIMASI ESTIMATOR (VERSI USER / CUSTOMER)
     ===================================================================== --}}
<div class="estimator reveal d2 grid grid-cols-1 gap-4 py-8 border-t border-b border-zinc-100 dark:border-zinc-900"
    style="margin-top: 20px; border-bottom: none !important;">

    <!-- =========================
         HEADER
    ========================== -->
    <div>
        <p class="font-medium text-zinc-900 dark:text-white text-lg flex items-center gap-2">
            <span>Estimator Mug Sublimasi Keramik SNI</span>
            <span class="text-xs px-2.5 py-0.5 rounded-full bg-accent/15 text-accent font-semibold border border-accent/20">Merchandise &amp; Souvenir</span>
        </p>
        <p class="text-xs text-zinc-400 tracking-widest mb-1">
            Mug keramik putih standar SNI (11 oz) dicetak sublimasi full color tahan cuci, mengkilap, dan anti luntur. Cocok untuk souvenir kantor, event, hampers, atau merchandise kustom.
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

            {{-- 1. Pilihan Jenis & Material Mug --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    1. Jenis &amp; Material Mug <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons grid grid-cols-1 gap-3" id="mugMaterialButtons">
                    <button type="button" class="estimator-choice-button mug-choice-mat text-left p-3.5 rounded-xl border transition-all"
                        data-mat="keramik_sni"
                        data-name="Keramik Putih Super White Standar SNI (11 oz)"
                        aria-checked="true">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm flex items-center gap-2">
                                <span>☕</span>
                                <span>Mug Keramik Putih Super White (SNI)</span>
                            </span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-accent/15 text-accent">Standar SNI 11 oz</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Bahan keramik porselen putih mengkilap tebal standar SNI Food-Grade (Kapasitas 11 oz / ~325 ml, Tinggi 9.5 cm, Diameter 8.2 cm). Tahan panas mendidih &amp; aman untuk microwave.
                        </div>
                    </button>
                </div>
            </div>

            {{-- 2. Pilihan Area Cetak Desain --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    2. Pilihan Area Cetak Desain <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons grid grid-cols-1 sm:grid-cols-2 gap-3" id="mugPrintButtons">
                    <button type="button" class="estimator-choice-button mug-choice-print text-left p-3.5 rounded-xl border transition-all"
                        data-print="fullwrap"
                        data-name="Cetak Melingkar / Full Wrap (20 × 8.5 cm)"
                        aria-checked="true">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm">Cetak Melingkar (Full Wrap)</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-accent/15 text-accent">Terpopuler</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Area cetak maksimal 20 × 8.5 cm mengelilingi badan mug, sangat cocok untuk panorama, foto, &amp; desain penuh.
                        </div>
                    </button>
                    <button type="button" class="estimator-choice-button mug-choice-print text-left p-3.5 rounded-xl border transition-all"
                        data-print="2sisi"
                        data-name="Cetak 2 Sisi (Logo Depan & Belakang)"
                        aria-checked="false">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm">Cetak 2 Sisi (Depan &amp; Belakang)</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">Simetris</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Cetak logo / tulisan di dua sisi hadap kanan &amp; kiri samping gagang mug, tampilan formal dan minimalis.
                        </div>
                    </button>
                </div>
            </div>

            {{-- 3. Pilihan Kemasan & Kelengkapan --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    3. Pilihan Kemasan &amp; Kelengkapan Mug <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons grid grid-cols-1 sm:grid-cols-2 gap-3" id="mugPackageButtons">
                    <button type="button" class="estimator-choice-button mug-choice-pkg text-left p-3.5 rounded-xl border transition-all"
                        data-pkg="box"
                        data-name="Termasuk Box Putih Individual"
                        aria-checked="true">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm">Termasuk Box Putih Individual</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">Rekomendasi</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Tiap mug dikemas dalam box karton putih bersih per satuan, rapi, aman, dan siap dibagikan untuk event/souvenir.
                        </div>
                    </button>
                    <button type="button" class="estimator-choice-button mug-choice-pkg text-left p-3.5 rounded-xl border transition-all"
                        data-pkg="nobox"
                        data-name="Tanpa Box (Packing Kardus Bersama)"
                        aria-checked="false">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm">Tanpa Box Satuan</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">Ekonomis</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Pengemasan standar berbusa aman tanpa box individual, praktis untuk pemakaian langsung di kantor / resto.
                        </div>
                    </button>
                </div>
            </div>

            {{-- 4. Kuantitas Pesanan Mug --}}
            <div class="sm:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        4. Kuantitas Pesanan (Pcs) <span class="text-red-500">*</span>
                    </label>
                    <span id="mugTierBadge" class="text-xs font-semibold px-2 py-0.5 rounded-md bg-accent/15 text-accent border border-accent/20">
                        Tier: 11–20 pcs
                    </span>
                </div>

                {{-- Quick Buttons --}}
                <div class="flex flex-wrap gap-2 mb-3">
                    <button type="button" class="mug-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="1">
                        1 pcs
                    </button>
                    <button type="button" class="mug-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="6">
                        6 pcs
                    </button>
                    <button type="button" class="mug-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors is-active"
                        data-qty="12">
                        12 pcs
                    </button>
                    <button type="button" class="mug-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="24">
                        24 pcs
                    </button>
                    <button type="button" class="mug-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="50">
                        50 pcs
                    </button>
                    <button type="button" class="mug-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="100">
                        100 pcs
                    </button>
                </div>

                {{-- Input Kustom --}}
                <div class="relative max-w-xs">
                    <input type="number" id="mugQuantityInput" min="1" step="1" value="12"
                        class="w-full bg-white dark:bg-[#16294d] border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm rounded-xl px-4 py-3 pr-16 focus:border-accent focus:ring-1 focus:ring-accent">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-zinc-500 font-semibold">Pcs</span>
                </div>
            </div>

            {{-- 5. Spesifikasi Lengkap Produk --}}
            <div class="sm:col-span-2">
                <div class="p-4 rounded-xl border border-accent/20 bg-accent/5 flex items-start gap-3">
                    <div class="text-xl shrink-0 mt-0.5">☕</div>
                    <div class="text-xs leading-relaxed">
                        <strong class="text-zinc-900 dark:text-white font-semibold block mb-0.5">
                            Spesifikasi Material &amp; Produksi Mug Sublimasi Cipta Grafika
                        </strong>
                        <ul class="text-zinc-600 dark:text-zinc-400 space-y-1 list-disc list-inside">
                            <li><strong>Material Bahan:</strong> Keramik Putih Super White Kualitas SNI Food-Grade (Kapasitas 11 oz / ~325 ml).</li>
                            <li><strong>Dimensi Ukuran:</strong> Tinggi 9.5 cm | Diameter 8.2 cm | Area Cetak Maksimal 20 × 8.5 cm.</li>
                            <li><strong>Metode Cetak:</strong> Sublimasi Digital High-Resolution warna hidup, anti luntur &amp; tahan panas.</li>
                            <li><strong>Keamanan:</strong> Aman untuk minuman hangat/dingin sehari-hari &amp; aman dimasukkan microwave.</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>

        <!-- =========================
             RIGHT SUMMARY (PRICE SUMMARY)
        ========================== -->
        <div class="h-fit rounded-2xl p-5 border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-white/5">
            <p class="text-sm font-medium text-zinc-900 dark:text-white">
                Estimasi Harga Mug Sublimasi
            </p>
            <p class="text-xs text-zinc-400 mt-1">
                Perhitungan otomatis harga grosir resmi
            </p>

            <div id="mugEstimateBreakdown" class="mt-5 space-y-2.5">
                <!-- Material & Varian -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Material:</span>
                    <strong class="text-zinc-800 dark:text-zinc-200 text-right">Keramik SNI Putih (11 oz)</strong>
                </div>

                <!-- Dimensi -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Ukuran:</span>
                    <strong class="text-zinc-800 dark:text-zinc-200 text-right">T 9.5cm × D 8.2cm</strong>
                </div>

                <!-- Area Cetak -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Area Cetak:</span>
                    <strong id="mugSummaryPrint" class="text-zinc-800 dark:text-zinc-200 text-right">Cetak Full Wrap</strong>
                </div>

                <!-- Kemasan -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Kemasan:</span>
                    <strong id="mugSummaryPackage" class="text-zinc-800 dark:text-zinc-200 text-right">Termasuk Box Putih</strong>
                </div>

                <!-- Jumlah Pesanan -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Jumlah Pesanan:</span>
                    <strong id="mugSummaryQty" class="text-zinc-800 dark:text-zinc-200 text-right">12 pcs</strong>
                </div>

                <!-- Tier Harga Aktif -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Tier Qty:</span>
                    <strong id="mugSummaryTier" class="text-zinc-800 dark:text-zinc-200 text-right">11–20 pcs</strong>
                </div>

                <!-- Harga Satuan -->
                <div class="flex justify-between gap-4 text-xs pt-1 border-t border-zinc-200/60 dark:border-zinc-800">
                    <span class="text-zinc-500 dark:text-zinc-400">Harga Satuan:</span>
                    <strong id="mugSummaryUnitPrice" class="text-accent font-semibold text-right">Rp 26.000 / pcs</strong>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-zinc-200 dark:border-zinc-700 my-4"></div>

            <!-- Total Estimasi -->
            <div>
                <p class="text-xs text-zinc-400">
                    Total Harga Estimasi
                </p>
                <p id="mugEstimateGrandTotal" class="mt-1.5 text-2xl font-bold text-zinc-900 dark:text-white">
                    Rp 312.000
                </p>
                <p id="mugEstimateSavingsHint" class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 font-medium"></p>
            </div>

            <!-- Tombol Lanjutkan Pesanan WhatsApp -->
            <button type="button" id="mugContinueOrderButton"
                class="w-full mt-6 py-3.5 px-4 rounded-xl bg-accent hover:opacity-90 active:scale-[0.99] text-white font-semibold text-xs sm:text-sm flex items-center justify-center gap-2.5 transition-all shadow-sm hover:shadow text-center">
                <i class="bi bi-whatsapp text-base sm:text-lg shrink-0"></i>
                <span class="leading-tight">Konsultasi &amp; Pesan via WhatsApp</span>
            </button>

            <!-- Tombol Tambahkan ke Keranjang -->
            <button type="button"
                @click="
                    isInBasket(@js($product['basketItem']['id']))
                        ? removeFromBasket(@js($product['basketItem']['id']))
                        : addToBasket(@js($product['basketItem']))
                "
                class="w-full mt-3 py-3.5 px-4 rounded-xl text-white cart-button text-xs sm:text-sm font-medium hover:opacity-90 active:scale-[0.99] transition-all duration-200 flex items-center justify-center gap-2.5">
                <template x-if="!isInBasket(@js($product['basketItem']['id']))">
                    <span class="flex items-center justify-center gap-2">
                        <i class="bi bi-cart-plus text-base shrink-0"></i>
                        <span>Tambahkan ke Keranjang</span>
                    </span>
                </template>
                <template x-if="isInBasket(@js($product['basketItem']['id']))">
                    <span class="flex items-center justify-center gap-2">
                        <i class="bi bi-cart-x text-base shrink-0"></i>
                        <span>Hapus dari Keranjang</span>
                    </span>
                </template>
            </button>
        </div>
    </div>

    <!-- Catatan Tambahan -->
    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-2">
        * Estimasi waktu pengerjaan berkisar 1–3 hari kerja. File desain siap cetak (format JPG, PNG, PDF) dapat dikirimkan langsung melalui chat WhatsApp setelah konsultasi.
    </p>
</div>

<style>
    .mug-quick-qty.is-active {
        background-color: var(--accent, #c2a857);
        color: #fff !important;
        border-color: var(--accent, #c2a857) !important;
    }
</style>

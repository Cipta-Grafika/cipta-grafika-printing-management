{{-- =====================================================================
     LANYARD & ID CARD ESTIMATOR (VERSI USER / CUSTOMER)
     ===================================================================== --}}
<div class="estimator reveal d2 grid grid-cols-1 gap-4 py-8 border-t border-b border-zinc-100 dark:border-zinc-900"
    style="margin-top: 20px; border-bottom: none !important;">

    <!-- =========================
         HEADER
    ========================== -->
    <div>
        <p class="font-medium text-zinc-900 dark:text-white text-lg flex items-center gap-2">
            <span>Estimator Tali Lanyard &amp; ID Card</span>
            <span class="text-xs px-2.5 py-0.5 rounded-full bg-accent/15 text-accent font-semibold border border-accent/20">Merchandise &amp; ID</span>
        </p>
        <p class="text-xs text-zinc-400 tracking-widest mb-1">
            Cetak tali lanyard custom printing sublim full-color dengan opsi paket kartu ID Card PVC + holder berkualitas tinggi.
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

            {{-- 1. Pilihan Lebar Tali --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    1. Lebar Tali Lanyard <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons" id="lanyardWidthButtons">
                    <button type="button" class="estimator-choice-button lanyard-choice-width"
                        data-width="2cm" aria-checked="true">
                        <div class="font-semibold text-sm">Lebar 2.0 cm</div>
                        <div class="text-[11px] text-zinc-400 opacity-90">Standar paling populer untuk kantor, seminar &amp; event</div>
                    </button>
                    <button type="button" class="estimator-choice-button lanyard-choice-width"
                        data-width="2.5cm" aria-checked="false">
                        <div class="font-semibold text-sm">Lebar 2.5 cm</div>
                        <div class="text-[11px] text-zinc-400 opacity-90">Lebar ekstra, tampilan lebih premium &amp; area desain lebih luas</div>
                    </button>
                </div>
            </div>

            {{-- 2. Pilihan Paket --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    2. Pilihan Paket Kelengkapan <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons" id="lanyardPackageButtons">
                    <button type="button" class="estimator-choice-button lanyard-choice-pkg"
                        data-pkg="tali" aria-checked="false">
                        <div class="font-semibold text-sm">Hanya Tali Saja</div>
                        <div class="text-[11px] text-zinc-400 opacity-90">Tali Lanyard Full Color + Stopper + Kait Logam Oval</div>
                    </button>
                    <button type="button" class="estimator-choice-button lanyard-choice-pkg"
                        data-pkg="set" aria-checked="true">
                        <div class="font-semibold text-sm">1 Set Lengkap (Holder + Kartu PVC)</div>
                        <div class="text-[11px] text-zinc-400 opacity-90">Tali Lanyard + Casing Holder ID Card + Kartu ID Card Cetak PVC Tebal</div>
                    </button>
                </div>
            </div>

            {{-- 3. Kuantitas Pesanan --}}
            <div class="sm:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        3. Kuantitas Pesanan (Pcs) <span class="text-red-500">*</span>
                    </label>
                    <span id="lanyardTierBadge" class="text-xs font-semibold px-2 py-0.5 rounded-md bg-accent/15 text-accent border border-accent/20">
                        Tier: 5–12 pcs
                    </span>
                </div>

                {{-- Quick Buttons --}}
                <div class="flex flex-wrap gap-2 mb-3">
                    <button type="button" class="lanyard-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="1">
                        1 pcs
                    </button>
                    <button type="button" class="lanyard-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="5">
                        5 pcs
                    </button>
                    <button type="button" class="lanyard-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors is-active"
                        data-qty="10">
                        10 pcs
                    </button>
                    <button type="button" class="lanyard-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="25">
                        25 pcs
                    </button>
                    <button type="button" class="lanyard-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="50">
                        50 pcs
                    </button>
                    <button type="button" class="lanyard-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="100">
                        100 pcs
                    </button>
                    <button type="button" class="lanyard-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="200">
                        200 pcs
                    </button>
                </div>

                {{-- Input Kustom --}}
                <div class="relative max-w-xs">
                    <input type="number" id="lanyardQuantityInput" min="1" step="1" value="10"
                        class="w-full bg-white dark:bg-[#16294d] border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm rounded-xl px-4 py-3 pr-16 focus:border-accent focus:ring-1 focus:ring-accent">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-zinc-500 font-semibold">Pcs</span>
                </div>
            </div>

            {{-- 4. Spesifikasi Lengkap Produk --}}
            <div class="sm:col-span-2">
                <div class="p-4 rounded-xl border border-accent/20 bg-accent/5 flex items-start gap-3">
                    <div class="text-xl shrink-0 mt-0.5">🎖️</div>
                    <div class="text-xs leading-relaxed">
                        <strong class="text-zinc-900 dark:text-white font-semibold block mb-0.5">
                            Spesifikasi Produksi Lanyard &amp; ID Card
                        </strong>
                        <ul class="text-zinc-600 dark:text-zinc-400 space-y-1 list-disc list-inside">
                            <li>Bahan Tali: Tissue Polyester lembut, nyaman di leher dan tidak gatal.</li>
                            <li>Metode Cetak: Sublimasi digital full-color HD 2 sisi bolak-balik (anti luntur).</li>
                            <li>Finishing: Stopper plastik lepas-pasang + Pengait besi kokoh anti karat.</li>
                            <li>Paket 1 Set sudah termasuk: Holder ID Card (Hardcase/Plastik) + Kartu PVC standar ATM tebal 0.76mm.</li>
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
                Estimasi Harga Lanyard
            </p>
            <p class="text-xs text-zinc-400 mt-1">
                Perhitungan otomatis sesuai paket &amp; kuantiti
            </p>

            <div id="lanyardEstimateBreakdown" class="mt-5 space-y-2.5">
                <!-- Spesifikasi Lebar -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Lebar Tali:</span>
                    <strong id="lanyardSummaryWidth" class="text-zinc-800 dark:text-zinc-200 text-right">Lebar 2.0 cm</strong>
                </div>

                <!-- Pilihan Paket -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Paket:</span>
                    <strong id="lanyardSummaryPackage" class="text-zinc-800 dark:text-zinc-200 text-right">1 Set (Holder + PVC)</strong>
                </div>

                <!-- Jumlah Pesanan -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Jumlah Pesanan:</span>
                    <strong id="lanyardSummaryQty" class="text-zinc-800 dark:text-zinc-200 text-right">10 pcs</strong>
                </div>

                <!-- Tier Harga Aktif -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Tier Qty:</span>
                    <strong id="lanyardSummaryTier" class="text-zinc-800 dark:text-zinc-200 text-right">5–12 pcs</strong>
                </div>

                <!-- Harga Satuan -->
                <div class="flex justify-between gap-4 text-xs pt-1 border-t border-zinc-200/60 dark:border-zinc-800">
                    <span class="text-zinc-500 dark:text-zinc-400">Harga Satuan:</span>
                    <strong id="lanyardSummaryUnitPrice" class="text-accent font-semibold text-right">Rp 24.000 / pcs</strong>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-zinc-200 dark:border-zinc-700 my-4"></div>

            <!-- Total Estimasi -->
            <div>
                <p class="text-xs text-zinc-400">
                    Total Harga Estimasi
                </p>
                <p id="lanyardEstimateGrandTotal" class="mt-1.5 text-2xl font-bold text-zinc-900 dark:text-white">
                    Rp 240.000
                </p>
                <p id="lanyardEstimateSavingsHint" class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 font-medium"></p>
            </div>

            <!-- Tombol Lanjutkan Pesanan WhatsApp -->
            <button type="button" id="lanyardContinueOrderButton"
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
        * Estimasi waktu pengerjaan berkisar 2–4 hari kerja tergantung kuantiti dan antrean produksi. File desain siap cetak dapat dikirimkan langsung melalui chat WhatsApp.
    </p>
</div>

<style>
    .lanyard-quick-qty.is-active {
        background-color: var(--accent, #c2a857);
        color: #fff !important;
        border-color: var(--accent, #c2a857) !important;
    }
    .lanyard-tier-active {
        background-color: rgba(194, 168, 87, 0.12);
        font-weight: 600;
    }
</style>


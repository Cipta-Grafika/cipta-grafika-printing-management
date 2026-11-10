{{-- =====================================================================
     MERCHANDISE AKRILIK UV ESTIMATOR (VERSI USER / CUSTOMER)
     ===================================================================== --}}
<div class="estimator reveal d2 grid grid-cols-1 gap-4 py-8 border-t border-b border-zinc-100 dark:border-zinc-900"
    style="margin-top: 20px; border-bottom: none !important;">

    <!-- =========================
         HEADER
    ========================== -->
    <div>
        <p class="font-medium text-zinc-900 dark:text-white text-lg flex items-center gap-2">
            <span>Estimator Merchandise Akrilik UV (2 Muka)</span>
            <span class="text-xs px-2.5 py-0.5 rounded-full bg-accent/15 text-accent font-semibold border border-accent/20">Merchandise &amp; Souvenir</span>
        </p>
        <p class="text-xs text-zinc-400 tracking-widest mb-1">
            Cetak Flatbed UV resolusi tinggi langsung di atas akrilik bening tebal 3 mm dengan hasil cetak 2 muka bolak-balik (Double Sided) 1 lapis akrilik solid. Anti air, anti gores, tahan lama, dan warna sangat tajam.
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

            {{-- 1. Pilihan Material & Ketebalan Akrilik --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    1. Bahan Material &amp; Ketebalan Akrilik <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons grid grid-cols-1 gap-3" id="akrilikMaterialButtons">
                    <button type="button" class="estimator-choice-button akrilik-choice-mat text-left p-3.5 rounded-xl border transition-all"
                        data-mat="akrilik_3mm"
                        data-name="Akrilik Bening Solid Tebal 3 mm Kualitas Super (Clear Cast)"
                        aria-checked="true">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm flex items-center gap-2">
                                <span>💎</span>
                                <span>Akrilik Bening Solid (Tebal 3 mm)</span>
                            </span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-accent/15 text-accent">Clear Cast 3 mm</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Akrilik murni transparansi 92% bening jernih kristal, dipotong laser presisi (bisa potong pola kustom / die cut mengikuti lekuk desain). Cetak 2 muka bolak-balik 1 lapis akrilik solid anti gores.
                        </div>
                    </button>
                </div>
            </div>

            {{-- 2. Pilihan Varian Produk Akrilik --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    2. Pilihan Varian Produk &amp; Aksesoris <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons grid grid-cols-1 sm:grid-cols-2 gap-3" id="akrilikVariantButtons">
                    <button type="button" class="estimator-choice-button akrilik-choice-variant text-left p-3.5 rounded-xl border transition-all"
                        data-variant="gantungan"
                        data-name="Gantungan Kunci Akrilik UV (2 Muka 1 Lapis)"
                        data-price="20000"
                        aria-checked="true">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm">Gantungan Kunci Akrilik</span>
                            <span class="text-xs font-bold text-accent">Rp 20.000</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Cetak 2 muka 1 lapis akrilik bening 3 mm + Ring gantungan kunci putar kuat anti karat.
                        </div>
                    </button>
                    <button type="button" class="estimator-choice-button akrilik-choice-variant text-left p-3.5 rounded-xl border transition-all"
                        data-variant="standee"
                        data-name="Standee Mini Akrilik UV (2 Muka 1 Lapis)"
                        data-price="20000"
                        aria-checked="false">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm">Standee Mini Akrilik</span>
                            <span class="text-xs font-bold text-accent">Rp 20.000</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Cetak 2 muka 1 lapis akrilik bening 3 mm + Tatakan/dudukan base meja akrilik mini.
                        </div>
                    </button>
                </div>
            </div>

            {{-- 3. Kuantitas Pesanan Akrilik UV --}}
            <div class="sm:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        3. Jumlah Pesanan (Pcs) <span class="text-red-500">*</span>
                    </label>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded-md bg-accent/15 text-accent border border-accent/20">
                        Min. Order: 1 pcs
                    </span>
                </div>

                {{-- Quick Buttons --}}
                <div class="flex flex-wrap gap-2 mb-3">
                    <button type="button" class="akrilik-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="1">
                        1 pcs
                    </button>
                    <button type="button" class="akrilik-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="5">
                        5 pcs
                    </button>
                    <button type="button" class="akrilik-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors is-active"
                        data-qty="10">
                        10 pcs
                    </button>
                    <button type="button" class="akrilik-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="25">
                        25 pcs
                    </button>
                    <button type="button" class="akrilik-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="50">
                        50 pcs
                    </button>
                    <button type="button" class="akrilik-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="100">
                        100 pcs
                    </button>
                </div>

                {{-- Input Kustom --}}
                <div class="relative max-w-xs">
                    <input type="number" id="akrilikUvQuantityInput" min="1" step="1" value="10"
                        class="w-full bg-white dark:bg-[#16294d] border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm rounded-xl px-4 py-3 pr-16 focus:border-accent focus:ring-1 focus:ring-accent">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-zinc-500 font-semibold">Pcs</span>
                </div>
            </div>

            {{-- 4. Spesifikasi Lengkap Produk --}}
            <div class="sm:col-span-2">
                <div class="p-4 rounded-xl border border-accent/20 bg-accent/5 flex items-start gap-3">
                    <div class="text-xl shrink-0 mt-0.5">💎</div>
                    <div class="text-xs leading-relaxed">
                        <strong class="text-zinc-900 dark:text-white font-semibold block mb-0.5">
                            Spesifikasi Material &amp; Produksi Akrilik UV Cipta Grafika
                        </strong>
                        <ul class="text-zinc-600 dark:text-zinc-400 space-y-1 list-disc list-inside">
                            <li><strong>Material Bahan:</strong> Akrilik Bening (Clear Cast Acrylic) Solid Premium tebal 3 mm.</li>
                            <li><strong>Metode Cetak:</strong> Direct-to-Substrate Flatbed UV High-Resolution 2 Sisi (Bolak-Balik).</li>
                            <li><strong>Spesifikasi:</strong> 2 Muka 1 Lapis Akrilik (Gambar terlindung, cerah &amp; jernih dari kedua sisi).</li>
                            <li><strong>Pemotongan:</strong> Laser Cutting Presisi CNC (bisa pola persegi, lingkaran, atau kustom die cut).</li>
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
                Estimasi Harga Akrilik UV
            </p>
            <p class="text-xs text-zinc-400 mt-1">
                Perhitungan otomatis produk kustom
            </p>

            <div id="akrilikUvEstimateBreakdown" class="mt-5 space-y-2.5">
                <!-- Material -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Material:</span>
                    <strong class="text-zinc-800 dark:text-zinc-200 text-right">Akrilik Bening 3 mm</strong>
                </div>

                <!-- Varian -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Varian:</span>
                    <strong id="akrilikUvSummaryVariant" class="text-zinc-800 dark:text-zinc-200 text-right">Gantungan Kunci Akrilik UV</strong>
                </div>

                <!-- Teknik Cetak -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Teknik Cetak:</span>
                    <strong class="text-zinc-800 dark:text-zinc-200 text-right">Flatbed UV 2 Muka</strong>
                </div>

                <!-- Jumlah Pesanan -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Jumlah Pesanan:</span>
                    <strong id="akrilikUvSummaryQty" class="text-zinc-800 dark:text-zinc-200 text-right">10 pcs</strong>
                </div>

                <!-- Harga Satuan -->
                <div class="flex justify-between gap-4 text-xs pt-1 border-t border-zinc-200/60 dark:border-zinc-800">
                    <span class="text-zinc-500 dark:text-zinc-400">Harga Satuan:</span>
                    <strong id="akrilikUvSummaryUnitPrice" class="text-accent font-semibold text-right">Rp 20.000 / pcs</strong>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-zinc-200 dark:border-zinc-700 my-4"></div>

            <!-- Total Estimasi -->
            <div>
                <p class="text-xs text-zinc-400">
                    Total Harga Estimasi
                </p>
                <p id="akrilikUvEstimateGrandTotal" class="mt-1.5 text-2xl font-bold text-zinc-900 dark:text-white">
                    Rp 200.000
                </p>
            </div>

            <!-- Tombol Lanjutkan Pesanan WhatsApp -->
            <button type="button" id="akrilikUvContinueOrderButton"
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
        * Estimasi waktu pengerjaan berkisar 2–3 hari kerja. File desain bebas bentuk (vektor AI, CDR, PDF, atau PNG transparan) dapat dikirim langsung melalui chat WhatsApp.
    </p>
</div>

<style>
    .akrilik-quick-qty.is-active {
        background-color: var(--accent, #c2a857);
        color: #fff !important;
        border-color: var(--accent, #c2a857) !important;
    }
</style>

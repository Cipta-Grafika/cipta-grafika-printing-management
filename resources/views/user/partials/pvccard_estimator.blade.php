{{-- =====================================================================
     KARTU PVC (ID CARD / KTA) ESTIMATOR (VERSI USER / CUSTOMER)
     ===================================================================== --}}
<div class="estimator reveal d2 grid grid-cols-1 gap-4 py-8 border-t border-b border-zinc-100 dark:border-zinc-900"
    style="margin-top: 20px; border-bottom: none !important;">

    <!-- =========================
         HEADER
    ========================== -->
    <div>
        <p class="font-medium text-zinc-900 dark:text-white text-lg flex items-center gap-2">
            <span>Estimator Kartu PVC (ID Card &amp; KTA)</span>
            <span class="text-xs px-2.5 py-0.5 rounded-full bg-accent/15 text-accent font-semibold border border-accent/20">Merchandise &amp; ID</span>
        </p>
        <p class="text-xs text-zinc-400 tracking-widest mb-1">
            Material kartu PVC standar ATM tebal 0.76 mm cetak full color bolak-balik. Awet, tahan air, tidak luntur, dan anti patah untuk ID Card Karyawan, KTA Komunitas, Kartu Pelajar, atau Member Card.
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

            {{-- 1. Pilihan Material & Ketebalan Kartu --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    1. Bahan Material &amp; Ketebalan Kartu <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons grid grid-cols-1 gap-3" id="pvcMaterialButtons">
                    <button type="button" class="estimator-choice-button pvccard-choice-mat text-left p-3.5 rounded-xl border transition-all"
                        data-mat="pvc_atm"
                        data-name="Polyvinyl Chloride (PVC) Solid Tebal 0.76 mm Standar ISO ATM"
                        aria-checked="true">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm flex items-center gap-2">
                                <span>🪪</span>
                                <span>Bahan PVC Solid Tebal (Standar ATM / KTP)</span>
                            </span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-accent/15 text-accent">Tebal 0.76 mm (CR-80)</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Material Polyvinyl Chloride (PVC) solid kualitas tinggi standar ISO internasional. Ukuran 85.6 × 54 mm dengan sudut round corner presisi mesin. Kaku, tebal, tahan air, tidak patah &amp; tidak mengelupas.
                        </div>
                    </button>
                </div>
            </div>

            {{-- 2. Pilihan Tipe / Fungsi Kartu --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    2. Pilihan Tipe &amp; Fungsi Kartu <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons grid grid-cols-1 sm:grid-cols-2 gap-3" id="pvcCardTypeButtons">
                    <button type="button" class="estimator-choice-button pvccard-choice-type text-left p-3.5 rounded-xl border transition-all"
                        data-type="idcard"
                        data-name="ID Card Karyawan & Panitia"
                        aria-checked="true">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm">ID Card Karyawan &amp; Panitia</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-accent/15 text-accent">Standar</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Format kartu identitas pegawai, panitia acara, barcode, nomor induk pegawai, dan foto profil tajam.
                        </div>
                    </button>
                    <button type="button" class="estimator-choice-button pvccard-choice-type text-left p-3.5 rounded-xl border transition-all"
                        data-type="kta"
                        data-name="Kartu Anggota (KTA) & Member"
                        aria-checked="false">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm">Kartu Anggota (KTA) &amp; Member</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">Komunitas</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Format kartu anggota klub / ormas, member diskon pelanggan, kartu pelajar, santri, dan kartu alumni.
                        </div>
                    </button>
                </div>
            </div>

            {{-- 3. Pilihan Finishing Lubang Kartu --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    3. Pilihan Finishing Lubang Kartu <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons grid grid-cols-1 sm:grid-cols-2 gap-3" id="pvcCardHoleButtons">
                    <button type="button" class="estimator-choice-button pvccard-choice-hole text-left p-3.5 rounded-xl border transition-all"
                        data-hole="none"
                        data-name="Tanpa Lubang (Standar Kartu ATM)"
                        aria-checked="true">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm">Tanpa Lubang (Standar ATM)</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-600 dark:text-emerald-400">Rapi</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Sudut membulat presisi rapi, kartu utuh elegan untuk dimasukkan ke card holder plastik/kulit atau dompet.
                        </div>
                    </button>
                    <button type="button" class="estimator-choice-button pvccard-choice-hole text-left p-3.5 rounded-xl border transition-all"
                        data-hole="oval"
                        data-name="Lubang Oval (Untuk Klip / Kait Lanyard)"
                        aria-checked="false">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm">Lubang Oval Atas (Kait Lanyard)</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">Praktis</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Sudah dilubangi plong oval di bagian atas tengah, siap langsung dikaitkan ke tali lanyard tanpa perlu casing.
                        </div>
                    </button>
                </div>
            </div>

            {{-- 4. Pilihan Sisi Cetak Kartu --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    4. Pilihan Sisi Cetak Kartu <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons grid grid-cols-1 sm:grid-cols-2 gap-3" id="pvcCardSideButtons">
                    <button type="button" class="estimator-choice-button pvccard-choice-side text-left p-3.5 rounded-xl border transition-all"
                        data-side="2sisi"
                        data-name="Cetak 2 Sisi Bolak-Balik (Full Color HD)"
                        aria-checked="true">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm">Cetak 2 Sisi Bolak-Balik</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-accent/15 text-accent">Rekomendasi</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Cetak warna depan &amp; belakang (misal: sisi depan foto identitas, sisi belakang tata tertib / barcode).
                        </div>
                    </button>
                    <button type="button" class="estimator-choice-button pvccard-choice-side text-left p-3.5 rounded-xl border transition-all"
                        data-side="1sisi"
                        data-name="Cetak 1 Sisi (Depan Saja, Belakang Polos)"
                        aria-checked="false">
                        <div class="flex items-center justify-between mb-1">
                            <span class="font-semibold text-sm">Cetak 1 Sisi Depan Saja</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300">Simpel</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 leading-snug">
                            Cetak penuh bagian depan saja dengan sisi belakang putih bersih standar.
                        </div>
                    </button>
                </div>
            </div>

            {{-- 5. Kuantitas Pesanan Kartu PVC --}}
            <div class="sm:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        5. Kuantitas Pesanan (Pcs) <span class="text-red-500">*</span>
                    </label>
                    <span id="pvcCardTierBadge" class="text-xs font-semibold px-2 py-0.5 rounded-md bg-accent/15 text-accent border border-accent/20">
                        Tier: 11–50 pcs
                    </span>
                </div>

                {{-- Quick Buttons --}}
                <div class="flex flex-wrap gap-2 mb-3">
                    <button type="button" class="pvccard-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="1">
                        1 pcs
                    </button>
                    <button type="button" class="pvccard-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="5">
                        5 pcs
                    </button>
                    <button type="button" class="pvccard-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="10">
                        10 pcs
                    </button>
                    <button type="button" class="pvccard-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors is-active"
                        data-qty="20">
                        20 pcs
                    </button>
                    <button type="button" class="pvccard-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="50">
                        50 pcs
                    </button>
                    <button type="button" class="pvccard-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="100">
                        100 pcs
                    </button>
                    <button type="button" class="pvccard-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="250">
                        250 pcs
                    </button>
                    <button type="button" class="pvccard-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="500">
                        500 pcs
                    </button>
                </div>

                {{-- Input Kustom --}}
                <div class="relative max-w-xs">
                    <input type="number" id="pvcCardQuantityInput" min="1" step="1" value="20"
                        class="w-full bg-white dark:bg-[#16294d] border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm rounded-xl px-4 py-3 pr-16 focus:border-accent focus:ring-1 focus:ring-accent">
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-zinc-500 font-semibold">Pcs</span>
                </div>
            </div>

            {{-- 6. Spesifikasi Lengkap Produk --}}
            <div class="sm:col-span-2">
                <div class="p-4 rounded-xl border border-accent/20 bg-accent/5 flex items-start gap-3">
                    <div class="text-xl shrink-0 mt-0.5">🪪</div>
                    <div class="text-xs leading-relaxed">
                        <strong class="text-zinc-900 dark:text-white font-semibold block mb-0.5">
                            Spesifikasi Material &amp; Produksi Kartu PVC Cipta Grafika
                        </strong>
                        <ul class="text-zinc-600 dark:text-zinc-400 space-y-1 list-disc list-inside">
                            <li><strong>Material Bahan:</strong> Polyvinyl Chloride (PVC) Solid Premium Standar ISO ATM / KTP.</li>
                            <li><strong>Dimensi Ukuran:</strong> 85.6 × 54 mm dengan sudut tumpul (Round Corner) presisi.</li>
                            <li><strong>Ketebalan:</strong> 0.76 mm (Tebal, kaku, kokoh, tahan air dan anti patah).</li>
                            <li><strong>Metode Cetak:</strong> Digital Printing HD 2 Sisi Bolak-Balik + Laminasi Press Panas Anti Pudar.</li>
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
                Estimasi Harga Kartu PVC
            </p>
            <p class="text-xs text-zinc-400 mt-1">
                Perhitungan otomatis harga grosir resmi
            </p>

            <div id="pvcCardEstimateBreakdown" class="mt-5 space-y-2.5">
                <!-- Varian Material -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Material:</span>
                    <strong class="text-zinc-800 dark:text-zinc-200 text-right">PVC Solid 0.76 mm</strong>
                </div>

                <!-- Dimensi -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Ukuran:</span>
                    <strong class="text-zinc-800 dark:text-zinc-200 text-right">85.6 × 54 mm (ATM)</strong>
                </div>

                <!-- Tipe Kartu -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Tipe Kartu:</span>
                    <strong id="pvcCardSummaryType" class="text-zinc-800 dark:text-zinc-200 text-right">ID Card Karyawan</strong>
                </div>

                <!-- Finishing Lubang -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Finishing:</span>
                    <strong id="pvcCardSummaryHole" class="text-zinc-800 dark:text-zinc-200 text-right">Tanpa Lubang (ATM)</strong>
                </div>

                <!-- Sisi Cetak -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Sisi Cetak:</span>
                    <strong id="pvcCardSummarySide" class="text-zinc-800 dark:text-zinc-200 text-right">2 Sisi Bolak-Balik</strong>
                </div>

                <!-- Jumlah Pesanan -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Jumlah Pesanan:</span>
                    <strong id="pvcCardSummaryQty" class="text-zinc-800 dark:text-zinc-200 text-right">20 pcs</strong>
                </div>

                <!-- Tier Harga Aktif -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Tier Qty:</span>
                    <strong id="pvcCardSummaryTier" class="text-zinc-800 dark:text-zinc-200 text-right">11–50 pcs</strong>
                </div>

                <!-- Harga Satuan -->
                <div class="flex justify-between gap-4 text-xs pt-1 border-t border-zinc-200/60 dark:border-zinc-800">
                    <span class="text-zinc-500 dark:text-zinc-400">Harga Satuan:</span>
                    <strong id="pvcCardSummaryUnitPrice" class="text-accent font-semibold text-right">Rp 10.000 / pcs</strong>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-zinc-200 dark:border-zinc-700 my-4"></div>

            <!-- Total Estimasi -->
            <div>
                <p class="text-xs text-zinc-400">
                    Total Harga Estimasi
                </p>
                <p id="pvcCardEstimateGrandTotal" class="mt-1.5 text-2xl font-bold text-zinc-900 dark:text-white">
                    Rp 200.000
                </p>
                <p id="pvcCardEstimateSavingsHint" class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 font-medium"></p>
            </div>

            <!-- Tombol Lanjutkan Pesanan WhatsApp -->
            <button type="button" id="pvcCardContinueOrderButton"
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
        * Estimasi waktu pengerjaan berkisar 1–3 hari kerja. File desain atau format data Excel (nama, NIK/NIM, foto) dapat dikirimkan langsung melalui chat WhatsApp.
    </p>
</div>

<style>
    .pvccard-quick-qty.is-active {
        background-color: var(--accent, #c2a857);
        color: #fff !important;
        border-color: var(--accent, #c2a857) !important;
    }
</style>

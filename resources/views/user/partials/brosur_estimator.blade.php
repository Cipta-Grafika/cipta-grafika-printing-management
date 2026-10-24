{{-- =====================================================================
     BROSUR & FLYER ESTIMATOR (VERSI USER / CUSTOMER)
     ===================================================================== --}}
<div class="estimator reveal d2 grid grid-cols-1 gap-4 py-8 border-t border-b border-zinc-100 dark:border-zinc-900"
    style="margin-top: 20px; border-bottom: none !important;">

    <!-- =========================
         HEADER
    ========================== -->
    <div>
        <p class="font-medium text-zinc-900 dark:text-white text-lg flex items-center gap-2">
            <span>Estimator Brosur &amp; Flyer</span>
            <span class="text-xs px-2.5 py-0.5 rounded-full bg-accent/15 text-accent font-semibold border border-accent/20">Digital &amp; Offset</span>
        </p>
        <p class="text-xs text-zinc-400 tracking-widest mb-1">
            Kalkulasi instan untuk kebutuhan cetak cepat (100–500 lembar) maupun cetak massal (1–10+ rim) dengan jaminan harga terbaik.
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

            {{-- 1. Pilihan Ukuran Brosur --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    1. Ukuran Brosur <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons" id="brosurProductButtons">
                    @foreach ($brochureProducts ?? [] as $prod)
                        <button type="button" class="estimator-choice-button brosur-choice-prod"
                            data-product-id="{{ $prod->id }}"
                            data-product-name="{{ $prod->product_name }}"
                            data-size="{{ $prod->final_length }} × {{ $prod->final_width }} cm"
                            aria-checked="{{ $loop->first ? 'true' : 'false' }}">
                            <div class="font-semibold text-sm">{{ $prod->product_name }}</div>
                            <div class="text-[11px] text-zinc-400 opacity-90">{{ $prod->final_length }} × {{ $prod->final_width }} cm</div>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 2. Pilihan Kertas / Bahan --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    2. Pilihan Kertas / Bahan <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons" id="brosurMaterialButtons">
                    @foreach ($brochureMaterials ?? [] as $mat)
                        <button type="button" class="estimator-choice-button brosur-choice-mat"
                            data-material-id="{{ $mat->id }}"
                            data-material-name="{{ $mat->material_name }}"
                            aria-checked="{{ $loop->first ? 'true' : 'false' }}">
                            <div class="font-semibold text-sm">{{ $mat->material_name }}</div>
                            <div class="text-[11px] text-zinc-400 opacity-90">
                                @if(str_contains(strtolower($mat->material_name), '120'))
                                    Standar flyer promosi hemat &amp; licin
                                @elseif(str_contains(strtolower($mat->material_name), '150'))
                                    Lebih tebal, premium &amp; mengkilap
                                @else
                                    Kertas HVS doff halus
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 3. Sisi Cetak --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    3. Sisi Cetak <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons" id="brosurSidesButtons">
                    <button type="button" class="estimator-choice-button brosur-choice-side"
                        data-side="1" aria-checked="true">
                        <div class="font-semibold text-sm">1 Sisi (1 Muka)</div>
                        <div class="text-[11px] text-zinc-400 opacity-90">Full Color tampak depan</div>
                    </button>
                    <button type="button" class="estimator-choice-button brosur-choice-side"
                        data-side="2" aria-checked="false">
                        <div class="font-semibold text-sm">2 Sisi (Bolak-Balik)</div>
                        <div class="text-[11px] text-zinc-400 opacity-90">Full Color depan &amp; belakang</div>
                    </button>
                </div>
            </div>

            {{-- 4. Pilihan Kuantitas --}}
            <div class="sm:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        4. Kuantitas Cetak <span class="text-red-500">*</span>
                    </label>
                    <span id="brosurQtyMeta" class="text-xs font-medium text-accent"></span>
                </div>

                {{-- Quick Buttons --}}
                <div class="flex flex-wrap gap-2 mb-3">
                    <button type="button" class="brosur-pkg-btn px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="100" data-type="pieces">
                        100 Lembar
                    </button>
                    <button type="button" class="brosur-pkg-btn px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors is-active"
                        data-qty="500" data-type="pieces">
                        500 Lembar (1 Rim)
                    </button>
                    <button type="button" class="brosur-pkg-btn px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="2" data-type="rims">
                        2 Rim (1.000 lbr)
                    </button>
                    <button type="button" class="brosur-pkg-btn px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="3" data-type="rims">
                        3 Rim (1.500 lbr)
                    </button>
                    <button type="button" class="brosur-pkg-btn px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="5" data-type="rims">
                        5 Rim (2.500 lbr)
                    </button>
                    <button type="button" class="brosur-pkg-btn px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="10" data-type="rims">
                        10 Rim (5.000 lbr)
                    </button>
                </div>

                {{-- Input Kustom --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="relative">
                        <input type="number" id="brosurQuantityInput" min="100" step="50" value="500"
                            class="w-full bg-white dark:bg-zinc-850 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm rounded-xl px-4 py-3 pr-16 focus:border-accent focus:ring-1 focus:ring-accent">
                        <span id="brosurUnitLabel" class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-zinc-500 font-semibold">Lembar</span>
                    </div>

                    <div class="flex items-center gap-2">
                        <button type="button" id="brosurToggleUnit" class="text-xs text-zinc-500 hover:text-accent underline transition-colors">
                            Ganti satuan ke Rim / Lembar
                        </button>
                    </div>
                </div>
            </div>

            {{-- 5. Smart Production Badge (Info Otomatis) --}}
            <div class="sm:col-span-2">
                <div id="brosurMethodBadge" class="p-4 rounded-xl border border-accent/25 bg-accent/5 flex items-start gap-3">
                    <div class="text-xl shrink-0 mt-0.5" id="brosurMethodIcon">🖨️</div>
                    <div class="text-xs leading-relaxed">
                        <strong id="brosurMethodTitle" class="text-zinc-900 dark:text-white font-semibold block mb-0.5">
                            Metode Cetak: Digital Print A3+
                        </strong>
                        <span id="brosurMethodDesc" class="text-zinc-600 dark:text-zinc-400">
                            Dicetak dengan mesin digital print warna tajam beresolusi tinggi, siap cepat dalam 1-2 hari kerja.
                        </span>
                    </div>
                </div>
            </div>

            <!-- Kode Promo -->
            <div class="sm:col-span-2 mt-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <label for="brosur_promo_code"
                    class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                    Kode Promo <span class="font-normal">(opsional)</span>
                </label>
                <div class="flex items-center gap-2">
                    <div class="relative flex-1 min-w-0">
                        <input type="text" id="brosur_promo_code" name="promo_code" maxlength="30"
                            autocomplete="off" autocapitalize="characters" spellcheck="false"
                            placeholder="Masukkan kode promo (contoh: S4KTI-PANCA5IL4)" aria-describedby="brosurPromoCodeStatus"
                            class="w-full bg-white dark:bg-zinc-850 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm rounded-xl px-4 py-2.5 focus:border-accent focus:ring-1 focus:ring-accent">
                    </div>
                    <button type="button" id="brosurApplyPromoCode"
                        class="shrink-0 rounded-xl bg-accent px-5 py-2.5 text-sm font-medium text-white hover:opacity-90 transition-opacity">
                        Terapkan
                    </button>
                    <button type="button" id="brosurRemovePromoCode"
                        class="shrink-0 rounded-xl border border-zinc-300 dark:border-zinc-700 px-4 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
                        hidden>
                        Hapus
                    </button>
                </div>
                <p id="brosurPromoCodeStatus" class="mt-2 text-xs text-zinc-500 dark:text-zinc-400" aria-live="polite">
                    Diskon dihitung dari master promo setelah kode diterapkan.
                </p>
            </div>

        </div>

        <!-- =========================
             RIGHT SUMMARY (PRICE SUMMARY)
        ========================== -->
        <div class="h-fit rounded-2xl p-5 border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-white/5">
            <p class="text-sm font-medium text-zinc-900 dark:text-white">
                Estimasi Harga Cetak
            </p>
            <p class="text-xs text-zinc-400 mt-1">
                Perhitungan otomatis berdasarkan kuantitas &amp; spesifikasi
            </p>

            <div id="brosurEstimateLoading" class="text-xs text-zinc-400 mt-4 flex items-center gap-2" style="display: none;">
                <span class="inline-block animate-spin">⏳</span>
                <span>Menghitung estimasi harga terbaik...</span>
            </div>

            <div id="brosurEstimateBreakdown" class="mt-5 space-y-2.5">
                <!-- Spesifikasi Ukuran -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Ukuran Brosur:</span>
                    <strong id="brosurSummarySize" class="text-zinc-800 dark:text-zinc-200 text-right">-</strong>
                </div>

                <!-- Bahan Kertas -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Kertas / Gramatur:</span>
                    <strong id="brosurSummaryMaterial" class="text-zinc-800 dark:text-zinc-200 text-right">-</strong>
                </div>

                <!-- Sisi Cetak -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Sisi Cetak:</span>
                    <strong id="brosurSummarySides" class="text-zinc-800 dark:text-zinc-200 text-right">-</strong>
                </div>

                <!-- Jumlah Jadi -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Jumlah Cetak:</span>
                    <strong id="brosurSummaryQty" class="text-zinc-800 dark:text-zinc-200 text-right">-</strong>
                </div>

                <!-- Harga Satuan -->
                <div class="flex justify-between gap-4 text-xs pt-1 border-t border-zinc-200/60 dark:border-zinc-800">
                    <span class="text-zinc-500 dark:text-zinc-400">Harga Satuan / Lembar:</span>
                    <strong id="brosurSummaryUnitPrice" class="text-accent font-semibold text-right">-</strong>
                </div>
            </div>

            <!-- Rincian Biaya & Diskon Promo -->
            <div id="brosurDiscountSummarySection" class="mt-3 space-y-1.5 text-xs border-t border-zinc-200/60 dark:border-zinc-800 pt-3" style="display: none;">
                <div id="brosurEstimateSubtotalRow" class="flex justify-between text-zinc-500 dark:text-zinc-400">
                    <span>Subtotal:</span>
                    <strong id="brosurEstimateSubtotal" class="text-zinc-700 dark:text-zinc-300 font-medium">Rp 0</strong>
                </div>
                <div id="brosurEstimateDiscountRow" class="flex justify-between text-emerald-600 dark:text-emerald-400 font-medium">
                    <span id="brosurEstimateDiscountLabel">Diskon Promo:</span>
                    <strong id="brosurEstimateDiscountAmount">- Rp 0</strong>
                </div>
                <div id="brosurEstimateRoundingRow" class="flex justify-between text-zinc-400 text-[11px]" style="display: none;">
                    <span>Pembulatan:</span>
                    <span id="brosurEstimateRoundingAmount">Rp 0</span>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-zinc-200 dark:border-zinc-700 my-4"></div>

            <!-- Total Estimasi -->
            <div>
                <p class="text-xs text-zinc-400">
                    Total Harga Estimasi
                </p>
                <p id="brosurEstimateGrandTotal" class="mt-1.5 text-2xl font-bold text-zinc-900 dark:text-white">
                    Rp -
                </p>
                <p id="brosurEstimatePerRim" class="text-xs text-zinc-400 mt-1"></p>
            </div>

            <!-- Tombol Lanjutkan Pesanan WhatsApp -->
            <button type="button" id="brosurContinueOrderButton" disabled
                class="w-full mt-6 py-3 rounded-xl bg-accent text-white text-sm font-medium hover:opacity-90 disabled:opacity-50 disabled:cursor-not-allowed transition-opacity">
                Lanjutkan Pesanan →
            </button>

            <!-- Tombol Tambahkan ke Keranjang -->
            <button type="button"
                @click="
                    isInBasket(@js($product['basketItem']['id']))
                        ? removeFromBasket(@js($product['basketItem']['id']))
                        : addToBasket(@js($product['basketItem']))
                "
                class="w-full mt-3 py-3 rounded-xl text-white cart-button text-sm font-medium hover:opacity-90 transition-opacity duration-200 flex items-center justify-center gap-2">
                <template x-if="!isInBasket(@js($product['basketItem']['id']))">
                    <span class="flex items-center justify-center gap-2">
                        Tambahkan ke Keranjang
                        <i class="bi bi-cart-plus"></i>
                    </span>
                </template>
                <template x-if="isInBasket(@js($product['basketItem']['id']))">
                    <span class="flex items-center justify-center gap-2">
                        Hapus dari Keranjang
                        <i class="bi bi-cart-x"></i>
                    </span>
                </template>
            </button>
        </div>
    </div>

    <!-- Catatan -->
    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-2">
        * Harga estimasi sudah termasuk plat CTP / klik cetak, kertas, dan finishing potong lurus jadi.
    </p>
</div>

<style>
    .brosur-pkg-btn.is-active {
        background-color: var(--accent, #c2a857);
        color: #fff !important;
        border-color: var(--accent, #c2a857) !important;
    }
</style>


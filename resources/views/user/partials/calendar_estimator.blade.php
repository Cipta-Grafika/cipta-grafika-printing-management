{{-- =====================================================================
     KALENDER ESTIMATOR (VERSI USER / CUSTOMER - DINDING & MEJA)
     ===================================================================== --}}
<div class="estimator reveal d2 grid grid-cols-1 gap-4 py-8 border-t border-b border-zinc-100 dark:border-zinc-900"
    style="margin-top: 20px; border-bottom: none !important;">

    <!-- =========================
         HEADER
    ========================== -->
    <div>
        <p class="font-medium text-zinc-900 dark:text-white text-lg flex items-center gap-2">
            <span>Kalkulator &amp; Estimasi Cetak Kalender</span>
            <span class="text-xs px-2.5 py-0.5 rounded-full bg-accent/15 text-accent font-semibold border border-accent/20">
                Digital &amp; Offset Cerdas
            </span>
        </p>
        <p class="text-xs text-zinc-400 tracking-widest mb-1">
            Hitung otomatis kebutuhan cetak kalender dinding &amp; meja Anda dengan rute produksi paling efisien (Digital Print A3+ vs Mesin Offset).
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

            {{-- 1. Jenis / Kategori Kalender --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    1. Jenis Kalender <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons" id="calendarTypeButtons">
                    <button type="button" class="estimator-choice-button cal-choice-type"
                        data-type="dinding" aria-checked="true" style="flex: 1; min-width: 220px;">
                        <div class="flex items-center gap-2.5">
                            <span class="text-lg">📅</span>
                            <div class="text-left">
                                <div class="font-semibold text-sm">Kalender Dinding (Wall)</div>
                                <div class="text-[11px] text-zinc-400 opacity-90">Cetak lembaran gantung, jepit klem seng / spiral hanger</div>
                            </div>
                        </div>
                    </button>
                    <button type="button" class="estimator-choice-button cal-choice-type"
                        data-type="meja" aria-checked="false" style="flex: 1; min-width: 220px;">
                        <div class="flex items-center gap-2.5">
                            <span class="text-lg">🖥️</span>
                            <div class="text-left">
                                <div class="font-semibold text-sm">Kalender Meja (Desk)</div>
                                <div class="text-[11px] text-zinc-400 opacity-90">Dudukan karton boot kokoh, jilid spiral kawat elegan</div>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            {{-- 2. Pilihan Ukuran Kalender --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    2. Ukuran Kalender <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons" id="calendarProductButtons">
                    @foreach ($calendarProducts ?? [] as $prod)
                        <button type="button" class="estimator-choice-button cal-choice-prod"
                            data-product-id="{{ $prod->id }}"
                            data-product-name="{{ $prod->product_name }}"
                            data-calendar-type="{{ $prod->calendar_type }}"
                            data-width="{{ $prod->final_width }}"
                            data-height="{{ $prod->final_height }}"
                            data-size="{{ $prod->size_label }}"
                            data-def-leaf="{{ $prod->default_leaf_count }}"
                            aria-checked="{{ $loop->first ? 'true' : 'false' }}">
                            <div class="font-semibold text-sm">{{ $prod->product_name }}</div>
                            <div class="text-[11px] text-zinc-400 opacity-90">
                                @if($prod->calendar_type === 'meja')
                                    Ukuran Standar Meja &bull; {{ ucfirst($prod->orientation) }}
                                @else
                                    {{ $prod->size_label }} &bull; Gantung Dinding
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 3. Pilihan Kertas / Bahan --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    3. Pilihan Kertas / Bahan <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons" id="calendarMaterialButtons">
                    @foreach ($calendarMaterials ?? [] as $mat)
                        <button type="button" class="estimator-choice-button cal-choice-mat"
                            data-material-id="{{ $mat->id }}"
                            data-material-name="{{ $mat->material_name }}"
                            data-category="{{ $mat->category }}"
                            aria-checked="{{ $mat->id == 2 ? 'true' : 'false' }}">
                            <div class="font-semibold text-sm">{{ $mat->material_name }}</div>
                            <div class="text-[11px] text-zinc-400 opacity-90">
                                @if(str_contains(strtolower($mat->material_name), '150'))
                                    Standar dinding terfavorit (halus &amp; rapi)
                                @elseif(str_contains(strtolower($mat->material_name), '120'))
                                    Ekonomis &amp; tipis
                                @elseif(str_contains(strtolower($mat->material_name), '210') || str_contains(strtolower($mat->material_name), '230'))
                                    Tebal &amp; kaku (cocok meja &amp; dinding)
                                @elseif(str_contains(strtolower($mat->material_name), '260'))
                                    Super tebal &amp; kokoh (favorit kalender meja)
                                @elseif(str_contains(strtolower($mat->material_name), '310'))
                                    Paling tebal &amp; premium
                                @elseif(str_contains(strtolower($mat->material_name), 'white'))
                                    Doff impor, mudah ditulis pulpen
                                @else
                                    Kertas cetak berkualitas
                                @endif
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- 4. Jumlah Lembar Isi --}}
            <div class="sm:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        4. Jumlah Lembar Isi <span class="text-red-500">*</span>
                    </label>
                    <span class="text-[11px] text-zinc-400">Pilih format bulanan per lembar</span>
                </div>
                <div class="estimator-choice-buttons" id="calendarLeafButtons">
                    {{-- 7 Lembar (2 Bulanan + Cover) --}}
                    <button type="button" class="estimator-choice-button cal-choice-leaf"
                        data-leaf="7" aria-checked="true">
                        <div class="flex items-center justify-between gap-2">
                            <div class="font-semibold text-sm">7 Lembar</div>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-accent/20 text-accent font-semibold">Populer</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 opacity-90 mt-0.5">2 Bulanan + Cover Depan</div>
                    </button>

                    {{-- 6 Lembar (2 Bulanan Tanpa Cover) --}}
                    <button type="button" class="estimator-choice-button cal-choice-leaf"
                        data-leaf="6" aria-checked="false">
                        <div class="flex items-center justify-between gap-2">
                            <div class="font-semibold text-sm">6 Lembar</div>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">Hemat</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 opacity-90 mt-0.5">2 Bulanan (Tanpa Cover)</div>
                    </button>

                    {{-- 13 Lembar (1 Bulanan + Cover) --}}
                    <button type="button" class="estimator-choice-button cal-choice-leaf"
                        data-leaf="13" aria-checked="false">
                        <div class="flex items-center justify-between gap-2">
                            <div class="font-semibold text-sm">13 Lembar</div>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 font-semibold">Eksklusif</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 opacity-90 mt-0.5">1 Bulanan + Cover Depan</div>
                    </button>

                    {{-- 12 Lembar (1 Bulanan Tanpa Cover) --}}
                    <button type="button" class="estimator-choice-button cal-choice-leaf"
                        data-leaf="12" aria-checked="false">
                        <div class="flex items-center justify-between gap-2">
                            <div class="font-semibold text-sm">12 Lembar</div>
                        </div>
                        <div class="text-[11px] text-zinc-400 opacity-90 mt-0.5">1 Bulanan (Tanpa Cover)</div>
                    </button>

                    {{-- 4 Lembar (3 Bulanan) --}}
                    <button type="button" class="estimator-choice-button cal-choice-leaf"
                        data-leaf="4" aria-checked="false">
                        <div class="flex items-center justify-between gap-2">
                            <div class="font-semibold text-sm">4 Lembar</div>
                        </div>
                        <div class="text-[11px] text-zinc-400 opacity-90 mt-0.5">3 Bulanan (Caturwulan)</div>
                    </button>

                    {{-- 3 Lembar (4 Bulanan) --}}
                    <button type="button" class="estimator-choice-button cal-choice-leaf"
                        data-leaf="3" aria-checked="false">
                        <div class="flex items-center justify-between gap-2">
                            <div class="font-semibold text-sm">3 Lembar</div>
                        </div>
                        <div class="text-[11px] text-zinc-400 opacity-90 mt-0.5">4 Bulanan (Triwulan)</div>
                    </button>

                    {{-- 1 Lembar (Poster) --}}
                    <button type="button" class="estimator-choice-button cal-choice-leaf leaf-dinding-only"
                        data-leaf="1" aria-checked="false">
                        <div class="flex items-center justify-between gap-2">
                            <div class="font-semibold text-sm">1 Lembar</div>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">Poster</span>
                        </div>
                        <div class="text-[11px] text-zinc-400 opacity-90 mt-0.5">12 Bulan Sekaligus (1 Muka)</div>
                    </button>
                </div>
            </div>

            {{-- 5. Laminasi (Opsional) --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    5. Lapisan Laminasi <span class="text-zinc-400 font-normal">(Opsional)</span>
                </label>
                <div class="estimator-choice-buttons" id="calendarLaminationButtons">
                    <button type="button" class="estimator-choice-button cal-choice-lam"
                        data-lam="none" aria-checked="true">
                        <div class="font-semibold text-sm">Tanpa Laminasi</div>
                        <div class="text-[11px] text-zinc-400 opacity-90">Standar cetak kalender bersih</div>
                    </button>
                    <button type="button" class="estimator-choice-button cal-choice-lam"
                        data-lam="doff" aria-checked="false">
                        <div class="font-semibold text-sm">Laminasi Doff</div>
                        <div class="text-[11px] text-zinc-400 opacity-90">Matte elegan, halus &amp; anti silau</div>
                    </button>
                    <button type="button" class="estimator-choice-button cal-choice-lam"
                        data-lam="glossy" aria-checked="false">
                        <div class="font-semibold text-sm">Laminasi Glossy</div>
                        <div class="text-[11px] text-zinc-400 opacity-90">Mengkilap mewah &amp; cerah</div>
                    </button>
                </div>
            </div>

            {{-- 6. Khusus Kalender Meja: Model Penopang / Dudukan --}}
            <div class="sm:col-span-2" id="calendarSupportSection" style="display: none;">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    6. Dudukan / Penopang Kalender Meja <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons" id="calendarSupportButtons">
                    <button type="button" class="estimator-choice-button cal-choice-support"
                        data-support="printing_fc" aria-checked="true">
                        <div class="font-semibold text-sm">Printing Full Color (Custom Design)</div>
                        <div class="text-[11px] text-zinc-400 opacity-90">Dudukan karton boot dilapisi Art Paper cetak custom brand Anda</div>
                    </button>
                    <button type="button" class="estimator-choice-button cal-choice-support"
                        data-support="hardcover_polos" aria-checked="false">
                        <div class="font-semibold text-sm">Hardcover Polos (Linen Hitam Rapi)</div>
                        <div class="text-[11px] text-zinc-400 opacity-90">Dudukan karton boot kokoh dilapisi kain linen hitam elegan</div>
                    </button>
                </div>
            </div>

            {{-- 7. Pilihan Kuantitas Cetak --}}
            <div class="sm:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        7. Kuantitas Cetak <span class="text-red-500">*</span>
                    </label>
                    <span id="calendarQtyMeta" class="text-xs font-medium text-accent"></span>
                </div>

                {{-- Quick Buttons --}}
                <div class="flex flex-wrap gap-2 mb-3">
                    <button type="button" class="cal-pkg-btn px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="50">
                        50 pcs
                    </button>
                    <button type="button" class="cal-pkg-btn px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="100">
                        100 pcs
                    </button>
                    <button type="button" class="cal-pkg-btn px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="200">
                        200 pcs
                    </button>
                    <button type="button" class="cal-pkg-btn px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors is-active"
                        data-qty="300">
                        300 pcs
                    </button>
                    <button type="button" class="cal-pkg-btn px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="500">
                        500 pcs
                    </button>
                    <button type="button" class="cal-pkg-btn px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors"
                        data-qty="1000">
                        1.000 pcs
                    </button>
                </div>

                {{-- Input Kustom --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="relative">
                        <input type="number" id="calendarQuantityInput" min="1" step="1" value="300"
                            class="w-full bg-white dark:bg-zinc-850 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm rounded-xl px-4 py-3 pr-16 focus:border-accent focus:ring-1 focus:ring-accent">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-zinc-500 font-semibold">pcs</span>
                    </div>
                    <div class="flex items-center text-xs text-zinc-400">
                        Semakin banyak kuantitas, harga satuan per pcs semakin hemat.
                    </div>
                </div>
            </div>

            {{-- 8. Smart Production Badge (Info Otomatis Rekomendasi Cerdas) --}}
            <div class="sm:col-span-2">
                <div id="calendarMethodBadge" class="p-4 rounded-xl border border-accent/25 bg-accent/5 flex items-start gap-3">
                    <div class="text-xl shrink-0 mt-0.5" id="calendarMethodIcon">🏭</div>
                    <div class="text-xs leading-relaxed">
                        <strong id="calendarMethodTitle" class="text-zinc-900 dark:text-white font-semibold block mb-0.5">
                            Metode Cetak: Mesin Offset SM52 (Hemat Cetak Massal)
                        </strong>
                        <span id="calendarMethodDesc" class="text-zinc-600 dark:text-zinc-400">
                            Pesanan ini otomatis dialokasikan ke mesin offset Heidelberg untuk harga paling hemat dan efisien.
                        </span>
                    </div>
                </div>
            </div>

            <!-- Kode Promo -->
            <div class="sm:col-span-2 mt-2 pt-4 border-t border-zinc-200 dark:border-zinc-800">
                <label for="calendar_promo_code"
                    class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                    Kode Promo <span class="font-normal">(opsional)</span>
                </label>
                <div class="flex items-center gap-2">
                    <div class="relative flex-1 min-w-0">
                        <input type="text" id="calendar_promo_code" name="promo_code" maxlength="30"
                            autocomplete="off" autocapitalize="characters" spellcheck="false"
                            placeholder="Masukkan kode promo (contoh: DISKON-KALENDER)" aria-describedby="calendarPromoCodeStatus"
                            class="w-full bg-white dark:bg-zinc-850 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm rounded-xl px-4 py-2.5 focus:border-accent focus:ring-1 focus:ring-accent">
                    </div>
                    <button type="button" id="calendarApplyPromoCode"
                        class="shrink-0 rounded-xl bg-accent px-5 py-2.5 text-sm font-medium text-white hover:opacity-90 transition-opacity">
                        Terapkan
                    </button>
                    <button type="button" id="calendarRemovePromoCode"
                        class="shrink-0 rounded-xl border border-zinc-300 dark:border-zinc-700 px-4 py-2.5 text-sm text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
                        hidden>
                        Hapus
                    </button>
                </div>
                <p id="calendarPromoCodeStatus" class="mt-2 text-xs text-zinc-500 dark:text-zinc-400" aria-live="polite">
                    Diskon otomatis dihitung setelah kode promo diterapkan.
                </p>
            </div>

        </div>

        <!-- =========================
             RIGHT SUMMARY (PRICE SUMMARY CARD)
        ========================== -->
        <div class="h-fit rounded-2xl p-5 border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-white/5 lg:sticky lg:top-24">
            <p class="text-sm font-medium text-zinc-900 dark:text-white">
                Ringkasan Estimasi Kalender
            </p>
            <p class="text-xs text-zinc-400 mt-1">
                Kalkulasi otomatis &amp; deteksi jalur hemat langsung
            </p>

            <div id="calendarEstimateLoading" class="text-xs text-zinc-400 mt-4 flex items-center gap-2" style="display: none;">
                <span class="inline-block animate-spin">⏳</span>
                <span>Menghitung estimasi harga terbaik...</span>
            </div>

            <div id="calendarEstimateBreakdown" class="mt-5 space-y-2.5">
                <!-- Jenis Kalender -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Jenis Kalender:</span>
                    <strong id="calendarSummaryType" class="text-zinc-800 dark:text-zinc-200 text-right">Kalender Dinding</strong>
                </div>

                <!-- Spesifikasi Ukuran -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Ukuran:</span>
                    <strong id="calendarSummarySize" class="text-zinc-800 dark:text-zinc-200 text-right">-</strong>
                </div>

                <!-- Bahan Kertas -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Kertas / Bahan:</span>
                    <strong id="calendarSummaryMaterial" class="text-zinc-800 dark:text-zinc-200 text-right">-</strong>
                </div>

                <!-- Lembar Isi -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Jumlah Lembar:</span>
                    <strong id="calendarSummaryLeaf" class="text-zinc-800 dark:text-zinc-200 text-right">-</strong>
                </div>

                <!-- Finishing & Penopang -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Finishing:</span>
                    <strong id="calendarSummaryFinishing" class="text-zinc-800 dark:text-zinc-200 text-right">-</strong>
                </div>

                <!-- Kuantitas Cetak -->
                <div class="flex justify-between gap-4 text-xs">
                    <span class="text-zinc-500 dark:text-zinc-400">Kuantitas Order:</span>
                    <strong id="calendarSummaryQty" class="text-zinc-800 dark:text-zinc-200 text-right">300 pcs</strong>
                </div>

                <!-- Harga Satuan -->
                <div class="flex justify-between gap-4 text-xs pt-2 border-t border-zinc-200/60 dark:border-zinc-800">
                    <span class="text-zinc-500 dark:text-zinc-400">Harga Satuan:</span>
                    <strong id="calendarSummaryUnitPrice" class="text-accent font-semibold text-right text-sm">-</strong>
                </div>
            </div>

            <!-- Rincian Biaya & Diskon Promo -->
            <div id="calendarDiscountSummarySection" class="mt-3 space-y-1.5 text-xs border-t border-zinc-200/60 dark:border-zinc-800 pt-3" style="display: none;">
                <div id="calendarEstimateSubtotalRow" class="flex justify-between text-zinc-500 dark:text-zinc-400">
                    <span>Subtotal:</span>
                    <strong id="calendarEstimateSubtotal" class="text-zinc-700 dark:text-zinc-300 font-medium">Rp 0</strong>
                </div>
                <div id="calendarEstimateDiscountRow" class="flex justify-between text-emerald-600 dark:text-emerald-400 font-medium">
                    <span id="calendarEstimateDiscountLabel">Diskon Promo:</span>
                    <strong id="calendarEstimateDiscountAmount">- Rp 0</strong>
                </div>
                <div id="calendarEstimateRoundingRow" class="flex justify-between text-zinc-400 text-[11px]" style="display: none;">
                    <span>Pembulatan:</span>
                    <span id="calendarEstimateRoundingAmount">Rp 0</span>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-zinc-200 dark:border-zinc-700 my-4"></div>

            <!-- Total Estimasi -->
            <div>
                <p class="text-xs text-zinc-400">
                    Total Estimasi Harga
                </p>
                <p id="calendarEstimateGrandTotal" class="mt-1.5 text-2xl font-bold text-zinc-900 dark:text-white">
                    Rp -
                </p>
                <p id="calendarEstimateNote" class="text-xs text-zinc-400 mt-1"></p>
            </div>

            <!-- Tombol Lanjutkan Pesanan WhatsApp (Emas Konsisten & Responsive) -->
            <button type="button" id="calendarContinueOrderButton" disabled
                class="w-full mt-5 py-3.5 px-4 rounded-xl bg-accent hover:opacity-90 active:scale-[0.99] text-white text-xs sm:text-sm font-medium tracking-wide shadow-sm hover:shadow disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:shadow-none disabled:active:scale-100 transition-all duration-200 flex items-center justify-center gap-2">
                <i class="bi bi-whatsapp text-base sm:text-lg shrink-0"></i>
                <span id="calendarContinueBtnText" class="truncate">Lanjutkan Pesanan via WhatsApp</span>
            </button>

            <!-- Tombol Tambahkan ke Keranjang -->
            <button type="button"
                @click="
                    isInBasket(@js($product['basketItem']['id']))
                        ? removeFromBasket(@js($product['basketItem']['id']))
                        : addToBasket(@js($product['basketItem']))
                "
                class="w-full mt-2.5 py-3 rounded-xl text-white cart-button text-xs sm:text-sm font-medium hover:opacity-90 transition-opacity duration-200 flex items-center justify-center gap-2">
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

    <!-- Catatan Tambahan -->
    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-2">
        * Estimasi harga sudah mencakup bahan kertas kalender, ongkos cetak (plat/klik), jilid finishing (klem seng / spiral), dan quality control rapi.
    </p>
</div>

<style>
    .cal-pkg-btn.is-active {
        background-color: var(--accent, #c2a857);
        color: #fff !important;
        border-color: var(--accent, #c2a857) !important;
    }
</style>

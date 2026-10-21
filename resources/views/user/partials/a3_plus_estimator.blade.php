@php
    $isDisplayMode = !empty($estimator['isDisplay']) || (isset($estimator['categoryId']) && (int)$estimator['categoryId'] === 236) || (($product['type'] ?? '') === 'display');
@endphp

{{-- =====================================================================
     DIGITAL PRINT A3+ ESTIMATOR (VERSI USER)
     ===================================================================== --}}
<div class="estimator reveal d2 grid grid-cols-1 gap-4 py-8 border-t border-b border-zinc-100 dark:border-zinc-900"
    style="margin-top: 20px; border-bottom: none !important;">

    <!-- =========================
         HEADER
    ========================== -->
    <div>
        <p class="font-medium text-zinc-900 dark:text-white text-lg">
            {{ $isDisplayMode ? 'Estimator Paket Display A3+' : 'Digital Print A3+ Estimator' }}
        </p>
        <p class="text-xs text-zinc-400 tracking-widest mb-1">
            {{ $isDisplayMode ? 'Kalkulasi paket cetak otomatis termasuk Box dan finishing potong.' : 'Kalkulasi cetak digital A3+ otomatis berdasarkan spesifikasi dan finishing pilihan Anda.' }}
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
            {{-- Parameter Internal Terkunci untuk User --}}
            <input type="hidden" id="a3_location_id" name="location_id" value="{{ $purwakartaLocationId ?? 54 }}">
            <input type="hidden" id="a3_price_type" name="price_type" value="general">
            <input type="hidden" id="a3_category_id" name="category_id" value="{{ $estimator['categoryId'] ?? '' }}">
            <input type="hidden" id="a3_is_display" value="{{ $isDisplayMode ? '1' : '0' }}">
            <input type="hidden" id="a3_display_product_id" name="display_product_id" value="{{ $estimator['displayProductId'] ?? '' }}">

            <!-- Mode Produksi -->
            <div class="sm:col-span-2" @if($isDisplayMode) style="display: none;" @endif>
                <label for="a3_production_mode"
                    class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                    Mode Produksi <span class="text-red-500">*</span>
                </label>
                <select id="a3_production_mode" name="production_mode" required class="estimator-choice-source">
                    @if($isDisplayMode)
                        <option value="display" selected>Paket Display</option>
                    @else
                        <option value="">Pilih Mode Produksi...</option>
                        <option value="layout">Hitung Layout (Otomatis Muat per Lembar A3+)</option>
                        <option value="direct">Cetak Langsung per Lembar (Direct Sheet)</option>
                    @endif
                </select>
            </div>

            <!-- Header Spesifikasi Cetak -->
            <div class="sm:col-span-2 mt-2">
                <h4 class="text-sm font-semibold text-zinc-900 dark:text-white mb-1">
                    {{ $isDisplayMode ? 'Pilihan Bahan & Sisi Cetak' : 'Spesifikasi Cetak' }}
                </h4>
            </div>

            <!-- Warna Cetak -->
            <div @if($isDisplayMode) style="display: none;" @endif>
                <label for="a3_print_color"
                    class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                    Warna Cetak <span class="text-red-500">*</span>
                </label>
                <select id="a3_print_color" name="print_color" required class="estimator-choice-source">
                    <option value="">Pilih Warna Cetak...</option>
                    <option value="fc">Full Color (FC)</option>
                    <option value="bw">Hitam Putih (B/W)</option>
                </select>
            </div>

            <!-- Sisi Cetak -->
            <div class="{{ $isDisplayMode ? 'sm:col-span-2' : '' }}">
                <label for="a3_print_side"
                    class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                    Sisi Cetak <span class="text-red-500">*</span>
                </label>
                <select id="a3_print_side" name="print_side" required class="estimator-choice-source">
                    <option value="">Pilih Sisi Cetak...</option>
                    <option value="1">1 Sisi</option>
                    <option value="2">2 Sisi (Bolak-Balik)</option>
                </select>
            </div>

            <!-- Material Kertas / Stiker / Komponen Display -->
            <div class="sm:col-span-2">
                <label for="a3_material_id"
                    class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                    {{ $isDisplayMode ? 'Pilihan Bahan & Finishing' : 'Material Kertas / Stiker' }} <span class="text-red-500">*</span>
                </label>
                <select id="a3_material_id" name="material_id" required class="estimator-choice-source">
                    <option value="">{{ $isDisplayMode ? 'Pilih varian bahan...' : 'Pilih material...' }}</option>
                    @foreach ($a3Materials ?? [] as $m)
                        <option value="{{ $m->id }}">
                            {{ $m->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if ($isDisplayMode)
                <!-- Mode Display Fields (Paket / Box) -->
                <div id="a3DisplayContainer" class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="a3_display_qty"
                            class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                            Jumlah Paket ({{ $estimator['packageUnit'] ?? 'Box' }}) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="a3_display_qty" min="1" step="1" value="1" required
                            class="w-full bg-white dark:bg-zinc-850 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm rounded-xl px-4 py-3">
                        <small class="text-xs text-zinc-400 mt-1 block">
                            1 {{ $estimator['packageUnit'] ?? 'Box' }} = {{ $estimator['packageQty'] ?? 100 }} pcs (kebutuhan cetak {{ $estimator['sheetQty'] ?? 4 }} lembar A3+).
                        </small>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                            Informasi Cetak Paket
                        </label>
                        <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-white/5 p-3 text-xs space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-zinc-500 dark:text-zinc-400">Total pcs:</span>
                                <strong id="a3DisplayPackagePcs" class="text-zinc-900 dark:text-white">{{ $estimator['packageQty'] ?? 100 }} pcs</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-500 dark:text-zinc-400">Kebutuhan lembar:</span>
                                <strong id="a3DisplayTotalSheets" class="text-accent font-semibold">{{ $estimator['sheetQty'] ?? 4 }} Lembar A3+</strong>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Mode Layout Fields -->
                <div id="a3LayoutContainer" class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5" style="display: none;">
                    <div>
                        <label for="a3_length"
                            class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                            Panjang Jadi (cm) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" id="a3_length" name="length" min="0.1" step="0.1" value="9" required
                                class="w-full bg-white dark:bg-zinc-850 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm rounded-xl px-4 py-3 pr-12">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-zinc-500">cm</span>
                        </div>
                    </div>

                    <div>
                        <label for="a3_width"
                            class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                            Lebar Jadi (cm) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" id="a3_width" name="width" min="0.1" step="0.1" value="5.5" required
                                class="w-full bg-white dark:bg-zinc-850 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm rounded-xl px-4 py-3 pr-12">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-zinc-500">cm</span>
                        </div>
                    </div>

                    <div>
                        <label for="a3_qty"
                            class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                            Jumlah Produk Jadi (pcs) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="a3_qty" name="qty" min="1" step="1" value="100" required
                            class="w-full bg-white dark:bg-zinc-850 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm rounded-xl px-4 py-3">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                            Hasil Imposisi Lembar
                        </label>
                        <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-white/5 p-3 text-xs space-y-1.5">
                            <div class="flex justify-between">
                                <span class="text-zinc-500 dark:text-zinc-400">Muat per lembar:</span>
                                <strong id="a3ImpositionUp" class="text-zinc-900 dark:text-white">—</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-zinc-500 dark:text-zinc-400">Kebutuhan lembar A3+:</span>
                                <strong id="a3ImpositionSheets" class="text-accent font-semibold">—</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mode Direct Sheet Fields (Hidden by Default) -->
                <div id="a3DirectContainer" class="sm:col-span-2" style="display: none;">
                    <label for="a3_sheet_qty"
                        class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                        Jumlah Lembar Cetak A3+ <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="a3_sheet_qty" name="sheet_qty" min="1" step="1" value="10"
                        class="w-full bg-white dark:bg-zinc-850 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm rounded-xl px-4 py-3">
                    <small class="text-xs text-zinc-400 mt-1 block">Cetak langsung di lembar ukuran standar A3+ (32.5 × 48.5 cm).</small>
                </div>
            @endif

            @if ($isDisplayMode)
                <!-- Finishing Info untuk Display -->
                <div class="sm:col-span-2 p-3.5 rounded-xl border border-emerald-500/20 bg-emerald-500/5 text-xs text-emerald-700 dark:text-emerald-400 flex items-center gap-2.5">
                    <i class="bi bi-check-circle-fill text-base shrink-0"></i>
                    <span>
                        Paket sudah <strong>termasuk Finishing Potong (Die Cut)</strong> dan <strong>Box Kartu Nama</strong>. Pilihan laminasi terintegrasi langsung pada varian bahan.
                    </span>
                </div>
            @else
                <!-- Header Finishing -->
                <div class="sm:col-span-2 mt-2">
                    <h4 class="text-sm font-semibold text-zinc-900 dark:text-white mb-1">
                        Finishing &amp; Pemotongan
                    </h4>
                </div>

                <!-- Finishing Laminasi -->
                <div>
                    <label for="a3_lamination_type"
                        class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                        Finishing Laminasi
                    </label>
                    <select id="a3_lamination_type" name="lamination_type" class="estimator-choice-source">
                        <option value="">Pilih Laminasi...</option>
                        <option value="none">Tanpa Laminasi</option>
                        @foreach ($a3Laminations ?? [] as $lam)
                            <option value="{{ $lam->id }}_1">{{ $lam->name }} (1 Sisi)</option>
                            <option value="{{ $lam->id }}_2">{{ $lam->name }} (2 Sisi)</option>
                        @endforeach
                    </select>
                </div>

                <!-- Finishing Cutting / Jasa Potong -->
                <div>
                    <label for="a3_cutting_type"
                        class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                        Finishing Cutting / Jasa Potong
                    </label>
                    <select id="a3_cutting_type" name="cutting_type" class="estimator-choice-source">
                        <option value="">Pilih Cutting...</option>
                        <option value="none">Tanpa Cutting</option>
                        @foreach ($a3Cuttings ?? [] as $cut)
                            @php
                                $lowerName = strtolower(trim($cut->name));
                                $isPotong = ($lowerName === 'potong');
                                $desc = '';
                                if ($isPotong) {
                                    $desc = ' Lurus (Guillotine)';
                                } elseif (str_contains($lowerName, 'kiss')) {
                                    $desc = ' (Stiker Setengah Putus)';
                                } elseif (str_contains($lowerName, 'die')) {
                                    $desc = ' (Stiker Putus)';
                                } elseif (str_contains($lowerName, 'perforasi')) {
                                    $desc = ' (Garis Cacah)';
                                }
                                $val = $isPotong ? 'potong' : $cut->id;
                            @endphp
                            <option value="{{ $val }}">{{ $cut->name }}{{ $desc }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Potong Size Override (Muncul jika potong dipilih) -->
                <div id="a3PotongOverrideField" class="sm:col-span-2" style="display: none;">
                    <label for="a3_potong_size_override"
                        class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                        Kategori Ukuran Potong
                    </label>
                    <select id="a3_potong_size_override" name="potong_size_override" class="estimator-choice-source">
                        <option value="" selected>Otomatis (Berdasarkan Ukuran Produk)</option>
                        <option value="A3">A3 (Besar / 29.7 × 42 cm)</option>
                        <option value="A4">A4 (Standar / 21 × 29.7 cm)</option>
                        <option value="A5">A5 (Sedang / 14.8 × 21 cm)</option>
                        <option value="<=A6">≤ A6 (Kecil / Kartu Nama)</option>
                    </select>
                </div>
            @endif

            <!-- Kode Promo -->
            <div class="sm:col-span-2">
                <label for="a3_promo_code"
                    class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                    Kode Promo <span class="font-normal">(opsional)</span>
                </label>
                <div class="flex flex-wrap gap-2">
                    <input type="text" id="a3_promo_code" name="promo_code" maxlength="30"
                        autocomplete="off" autocapitalize="characters" spellcheck="false"
                        placeholder="Masukkan kode promo" aria-describedby="a3PromoCodeStatus"
                        class="min-w-0 flex-1 bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white text-sm rounded-xl px-4 py-3">
                    <button type="button" id="a3ApplyPromoCode"
                        class="rounded-xl bg-accent px-4 py-3 text-sm font-medium text-white">Terapkan</button>
                    <button type="button" id="a3RemovePromoCode"
                        class="rounded-xl border border-zinc-300 dark:border-zinc-700 px-4 py-3 text-sm text-zinc-700 dark:text-zinc-300"
                        hidden>Hapus</button>
                </div>
                <p id="a3PromoCodeStatus" class="mt-2 text-xs text-zinc-500 dark:text-zinc-400" aria-live="polite">
                    Diskon dihitung dari master promo setelah kode diterapkan.
                </p>
            </div>
        </div>

        <!-- =========================
             RIGHT SUMMARY (PRICE SUMMARY)
        ========================== -->
        <div class="h-fit rounded-2xl p-5 border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-white/5">
            <p class="text-sm font-medium text-zinc-900 dark:text-white">
                Estimasi Harga
            </p>
            <p class="text-xs text-zinc-400 mt-1">
                Perkiraan berdasarkan konfigurasi saat ini
            </p>

            <p id="a3EstimateStatus" class="text-xs text-zinc-400 mt-4" aria-live="polite">
                {{ $isDisplayMode ? 'Pilih varian bahan dan sisi cetak untuk melihat estimasi.' : 'Pilih spesifikasi cetak dan material untuk melihat estimasi.' }}
            </p>

            <div id="a3EstimateBreakdown" class="mt-6 space-y-3" hidden>
                <!-- Cetak A3+ / Paket Display -->
                <div class="flex justify-between gap-4">
                    <div class="min-w-0">
                        <span id="a3EstimatePrintLabel" class="block text-sm text-zinc-500 dark:text-zinc-400">
                            Cetak Digital A3+
                        </span>
                        <span id="a3EstimatePrintMeta" class="block text-xs text-zinc-400 mt-1"></span>
                    </div>
                    <span id="a3EstimatePrintAmount" class="shrink-0 text-sm text-zinc-700 dark:text-zinc-200">
                        Rp 0
                    </span>
                </div>

                <!-- Laminasi -->
                <div id="a3EstimateLaminationRow" class="flex justify-between gap-4" hidden>
                    <div class="min-w-0">
                        <span id="a3EstimateLaminationLabel" class="block text-sm text-zinc-500 dark:text-zinc-400">
                            Laminasi
                        </span>
                        <span id="a3EstimateLaminationMeta" class="block text-xs text-zinc-400 mt-1"></span>
                    </div>
                    <span id="a3EstimateLaminationAmount" class="shrink-0 text-sm text-zinc-700 dark:text-zinc-200">
                        Rp 0
                    </span>
                </div>

                <!-- Cutting -->
                <div id="a3EstimateCuttingRow" class="flex justify-between gap-4" hidden>
                    <div class="min-w-0">
                        <span id="a3EstimateCuttingLabel" class="block text-sm text-zinc-500 dark:text-zinc-400">
                            Cutting / Potong
                        </span>
                        <span id="a3EstimateCuttingMeta" class="block text-xs text-zinc-400 mt-1"></span>
                    </div>
                    <span id="a3EstimateCuttingAmount" class="shrink-0 text-sm text-zinc-700 dark:text-zinc-200">
                        Rp 0
                    </span>
                </div>

                <!-- Finishing Paket Display -->
                <div id="a3EstimateDisplayFinishingRow" class="flex justify-between gap-4" hidden>
                    <div class="min-w-0">
                        <span id="a3EstimateDisplayFinishingLabel" class="block text-sm text-zinc-500 dark:text-zinc-400">
                            Finishing Paket (Termasuk)
                        </span>
                        <span id="a3EstimateDisplayFinishingMeta" class="block text-xs text-zinc-400 mt-1"></span>
                    </div>
                    <span id="a3EstimateDisplayFinishingAmount" class="shrink-0 text-sm text-zinc-700 dark:text-zinc-200">
                        Termasuk
                    </span>
                </div>

                <!-- Diskon Promo -->
                <div id="a3EstimateDiscountRow" class="flex justify-between gap-4" hidden>
                    <span id="a3EstimateDiscountLabel" class="text-sm text-zinc-500 dark:text-zinc-400">
                        Diskon
                    </span>
                    <span id="a3EstimateDiscountAmount" class="text-sm text-zinc-700 dark:text-zinc-200">
                        Rp 0
                    </span>
                </div>

                <!-- Pembulatan -->
                <div id="a3EstimateRoundingRow" class="flex justify-between gap-4" hidden>
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">
                        Pembulatan ke atas (Rp 1.000)
                    </span>
                    <span id="a3EstimateRoundingAmount" class="text-sm text-zinc-700 dark:text-zinc-200">
                        Rp 0
                    </span>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-zinc-200 dark:border-zinc-700 my-5"></div>

            <!-- Total Estimasi -->
            <div>
                <p class="text-xs text-zinc-400">
                    Total Estimasi
                </p>
                <p id="a3EstimateGrandTotal" class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">
                    —
                </p>
                <p id="a3EstimatePerPcs" class="text-xs text-zinc-400 mt-1"></p>
            </div>

            <!-- Tombol Lanjutkan Pesanan WhatsApp -->
            <button type="button" id="a3ContinueOrderButton" disabled
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
                class="w-full mt-6 py-3 rounded-xl text-white cart-button text-sm font-medium hover:opacity-90 transition-opacity duration-200 flex items-center justify-center gap-2">
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
        * Harga yang ditampilkan merupakan estimasi sementara dan dapat berubah sesuai konfigurasi produksi.
    </p>
</div>


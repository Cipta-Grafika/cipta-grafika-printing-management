{{-- =====================================================================
     PAPAN NAMA (NAME TAG DADA) ESTIMATOR (VERSI USER / CUSTOMER)
     ===================================================================== --}}
<div class="estimator reveal d2 grid grid-cols-1 gap-4 py-8 border-t border-b border-zinc-100 dark:border-zinc-900"
    style="margin-top: 20px; border-bottom: none !important;">

    <!-- =========================
         HEADER
    ========================== -->
    <div>
        <p class="font-medium text-zinc-900 dark:text-white text-lg flex items-center gap-2">
            <span>Estimator Papan Nama Dada (Name Tag)</span>
            <span class="text-xs px-2.5 py-0.5 rounded-full bg-accent/15 text-accent font-semibold border border-accent/20">Merchandise &amp; ID</span>
        </p>
        <p class="text-xs text-zinc-400 tracking-widest mb-1">
            Papan nama dada resmi seragam kantor, instansi, medis, dan profesional. Tersedia opsi Akrilik Biasa, Resin Lycal Cembung, dan Resin Emas Eksklusif dengan pengait Peniti atau Magnet.
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

            {{-- 1. Pilihan Varian Bahan Papan Nama --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    1. Pilihan Bahan &amp; Tipe Papan Nama <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons grid grid-cols-1 sm:grid-cols-3 gap-3" id="nameTagTypeButtons">
                    @forelse ($nameTagItems as $index => $nt)
                        <button type="button" class="estimator-choice-button nametag-choice-type text-left p-3.5 rounded-xl border transition-all"
                            data-id="{{ $nt->id }}"
                            data-code="{{ $nt->type_code }}"
                            data-name="{{ $nt->name }}"
                            data-price="{{ (int) $nt->price }}"
                            aria-checked="{{ $index === 1 ? 'true' : 'false' }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-semibold text-sm">{{ $nt->name }}</span>
                                <span class="text-xs font-bold text-accent">Rp {{ number_format($nt->price, 0, ',', '.') }}</span>
                            </div>
                            <div class="text-[11px] text-zinc-400 leading-snug">
                                @if ($nt->type_code === 'biasa')
                                    Plat akrilik standar tanpa resin, rapi &amp; formal.
                                @elseif ($nt->type_code === 'resin')
                                    Lapisan resin cembung lycal mengkilap, tahan gores &amp; elegan.
                                @else
                                    Dasar plat emas cermin mewah + resin cembung glossy eksklusif.
                                @endif
                            </div>
                        </button>
                    @empty
                        {{-- Fallback default jika database kosong --}}
                        <button type="button" class="estimator-choice-button nametag-choice-type" data-code="biasa" data-name="Papan Nama Biasa" data-price="25000" aria-checked="false">
                            <div class="font-semibold text-sm">Papan Nama Biasa</div>
                            <div class="text-xs font-bold text-accent">Rp 25.000</div>
                        </button>
                        <button type="button" class="estimator-choice-button nametag-choice-type" data-code="resin" data-name="Papan Nama Resin" data-price="35000" aria-checked="true">
                            <div class="font-semibold text-sm">Papan Nama Resin</div>
                            <div class="text-xs font-bold text-accent">Rp 35.000</div>
                        </button>
                        <button type="button" class="estimator-choice-button nametag-choice-type" data-code="resin_emas" data-name="Papan Nama Resin Emas" data-price="35000" aria-checked="false">
                            <div class="font-semibold text-sm">Papan Nama Resin Emas</div>
                            <div class="text-xs font-bold text-accent">Rp 35.000</div>
                        </button>
                    @endforelse
                </div>
            </div>

            {{-- 2. Pilihan Pengait (Peniti vs Magnet) --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                    2. Pilihan Model Pengait Belakang <span class="text-red-500">*</span>
                </label>
                <div class="estimator-choice-buttons grid grid-cols-1 sm:grid-cols-2 gap-3" id="nameTagFastenerButtons">
                    <button type="button" class="estimator-choice-button nametag-choice-fastener text-left p-3.5 rounded-xl border transition-all"
                        data-fastener="peniti" aria-checked="false">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-base">🧷</span>
                            <span class="font-semibold text-sm">Peniti Kuat (Safety Pin)</span>
                        </div>
                        <div class="text-[11px] text-zinc-400">Pengait jarum peniti stainless kokoh, cocok untuk kemeja, seragam dinas, dan jas.</div>
                    </button>
                    <button type="button" class="estimator-choice-button nametag-choice-fastener text-left p-3.5 rounded-xl border transition-all"
                        data-fastener="magnet" aria-checked="true">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-base">🧲</span>
                            <span class="font-semibold text-sm">Magnet Neodymium (Terfavorit)</span>
                        </div>
                        <div class="text-[11px] text-zinc-400">Daya rekat magnet ekstra kuat, praktis dipakai tanpa melubangi atau merusak serat kain baju.</div>
                    </button>
                </div>
            </div>

            {{-- 3. Kuantitas Pesanan (Pcs) --}}
            <div class="sm:col-span-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        3. Jumlah Pesanan (Pcs) <span class="text-red-500">*</span>
                    </label>
                    <span class="text-xs text-zinc-400 font-medium">Bisa pesan satuan (min. 1 pcs)</span>
                </div>

                {{-- Preset Cepat --}}
                <div class="flex flex-wrap gap-2 mb-3">
                    <button type="button" class="nametag-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors" data-qty="1">1 pcs</button>
                    <button type="button" class="nametag-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors" data-qty="2">2 pcs</button>
                    <button type="button" class="nametag-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors is-active" data-qty="5">5 pcs</button>
                    <button type="button" class="nametag-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors" data-qty="10">10 pcs</button>
                    <button type="button" class="nametag-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors" data-qty="20">20 pcs</button>
                    <button type="button" class="nametag-quick-qty px-3 py-1.5 text-xs font-medium rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent text-zinc-700 dark:text-zinc-300 transition-colors" data-qty="50">50 pcs</button>
                </div>

                {{-- Input Stepper --}}
                <div class="flex items-center gap-3">
                    <button type="button" id="nameTagQtyMinus"
                        class="w-10 h-10 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent flex items-center justify-center text-lg font-bold text-zinc-700 dark:text-zinc-200 transition-colors">
                        −
                    </button>
                    <input type="number" id="nameTagQtyInput" min="1" step="1" value="5"
                        class="w-32 text-center text-base font-bold bg-white dark:bg-[#16294d] border border-zinc-300 dark:border-zinc-700 rounded-lg py-2 text-zinc-900 dark:text-white focus:outline-none focus:border-accent">
                    <button type="button" id="nameTagQtyPlus"
                        class="w-10 h-10 rounded-lg border border-zinc-300 dark:border-zinc-700 bg-white dark:bg-[#16294d] hover:border-accent flex items-center justify-center text-lg font-bold text-zinc-700 dark:text-zinc-200 transition-colors">
                        +
                    </button>
                    <span class="text-xs text-zinc-400">Pcs Papan Nama</span>
                </div>
            </div>

            {{-- 4. Catatan Tulisan / Nama Dada (Opsional) --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                    4. Daftar Nama / Jabatan / Gelar <span class="text-zinc-400 dark:text-zinc-500 font-normal">(Opsional)</span>
                </label>
                <textarea id="nameTagCustomNotes" rows="2"
                    placeholder="Contoh: 1. dr. Ahmad Fauzi, Sp.A, 2. Siti Nurhaliza, S.Kep (bisa juga dikirim langsung via WhatsApp)"
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
                        <span>Varian Bahan:</span>
                        <span id="summaryNameTagType" class="font-semibold text-zinc-900 dark:text-white text-right">Papan Nama Resin</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Model Pengait:</span>
                        <span id="summaryNameTagFastener" class="font-semibold text-zinc-900 dark:text-white">Magnet Neodymium</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Kuantitas:</span>
                        <span id="summaryNameTagQty" class="font-semibold text-zinc-900 dark:text-white">5 pcs</span>
                    </div>

                    <div class="flex justify-between items-center text-zinc-600 dark:text-zinc-400">
                        <span>Harga Satuan:</span>
                        <span id="summaryNameTagUnitPrice" class="font-bold text-accent">Rp 35.000</span>
                    </div>
                </div>

                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800">
                    <div class="flex justify-between items-baseline mb-1">
                        <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">Total Biaya:</span>
                        <span id="summaryNameTagTotalPrice" class="text-2xl font-extrabold text-accent">
                            Rp 175.000
                        </span>
                    </div>
                    <div class="text-[11px] text-zinc-400 dark:text-zinc-500 text-right">
                        Sudah termasuk plat, grafir/cetak nama &amp; pengait.
                    </div>
                </div>

                {{-- Action Buttons (Konsisten Tema Emas & Responsive) --}}
                <div class="pt-2 space-y-2.5">
                    {{-- Order via WhatsApp Button (Emas Konsisten) --}}
                    <a id="btnNameTagWhatsApp" href="#" target="_blank"
                        class="w-full py-3.5 px-4 rounded-xl bg-accent hover:opacity-90 active:scale-[0.99] text-white font-semibold text-xs sm:text-sm flex items-center justify-center gap-2 transition-all shadow-sm hover:shadow">
                        <i class="bi bi-whatsapp text-sm sm:text-base shrink-0"></i>
                        <span>Pesan via WhatsApp Sekarang</span>
                    </a>

                    {{-- Add to Cart Button (Konsisten Tema Emas & Terintegrasi Basket) --}}
                    <button type="button" id="btnNameTagAddToCart"
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
                    <span>Butuh bantuan format penulisan nama? Konsultasikan gratis ke tim CS kami.</span>
                </div>

            </div>
        </div>

    </div>
</div>


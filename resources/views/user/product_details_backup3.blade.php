@extends('user_master')

@section('contents')
    <style>
        .select2-container--default .select2-results__option[aria-selected=true],
        .select2-container--default .select2-results__option--selected,
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            font-weight: 400 !important;
        }

        .estimator [hidden] {
            display: none !important;
        }
    </style>

    @php
        $productDescription =
            'Pilih ukuran, bahan, sisi cetak, dan finishing sesuai kebutuhan Anda, lalu lihat estimasi harganya langsung di halaman ini.';
    @endphp

    <div x-data="products()">
        <!-- ═══ CASE STUDY HEADER ═══ -->
        <article>

            <header class="pt-32 pb-10 max-w-4xl mx-auto px-6">

                <!-- =========================
                                                                                                                                                                BACK LINK
                                                                                                                                                            ========================== -->
                <a href="{{ route('user_products') }}"
                    class="inline-flex items-center gap-2
                       text-sm
                       text-zinc-500 dark:text-zinc-400
                       hover:text-accent
                       transition-colors
                       mb-8">

                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" aria-hidden="true">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16l-4-4m0 0l4-4m-4 4h18" />

                    </svg>

                    Kembali

                </a>


                <!-- =========================
                                                                                                                                                                PRODUCT HEADER
                                                                                                                                                            ========================== -->
                <div class="grid md:grid-cols-2 gap-6">


                    <!-- =========================
                                                                                                                                                                    PRODUCT IMAGE GALLERY
                                                                                                                                                                ========================== -->
                    <div x-data='{
                        title: @json($product['title'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS),
                        images: @json($product['images'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS),
                        activeImage: null
                    }'
                        x-init="activeImage = images[0] ?? null"
                        class="flex flex-col gap-4
                           w-full
                           min-w-0"
                        style="margin-bottom: 20px;">


                        <!-- =========================
                                                                                                                                                                        MAIN IMAGE
                                                                                                                                                                    ========================== -->
                        <div
                            class="pf
                               w-full
                               h-64
                               sm:h-72
                               md:h-80
                               lg:h-[420px]
 
                               rounded-xl
                               overflow-hidden
 
                               bg-zinc-100
                               dark:bg-zinc-900
 
                               border
                               border-zinc-200
                               dark:border-zinc-800">

                            <img x-show="activeImage" :src="activeImage" :alt="title" loading="lazy"
                                class="w-full
                                   h-full
                                   object-cover
                                   transition-all
                                   duration-300">

                            <div x-show="!activeImage"
                                class="w-full
                                   h-full
                                   flex
                                   items-center
                                   justify-center
                                   text-sm
                                   text-zinc-400">

                                Gambar belum tersedia

                            </div>

                        </div>


                        <!-- =========================
                                                                                                                                                                        THUMBNAIL GALLERY
                                                                                                                                                                    ========================== -->
                        <div x-show="images.length > 1"
                            class="thumbnail-scroll
                               flex
                               gap-2
                               sm:gap-3
 
                               w-full
                               min-w-0
 
                               overflow-x-auto
                               overflow-y-hidden
 
                               pb-2">


                            <template x-for="(image, index) in images" :key="index">


                                <button type="button" @click="activeImage = image"
                                    class="relative
                                       shrink-0
 
                                       w-20
                                       h-14
 
                                       sm:w-24
                                       sm:h-16
 
                                       md:w-28
                                       md:h-[72px]
 
                                       rounded-lg
                                       overflow-hidden
 
                                       border-2
 
                                       transition-all
                                       duration-200
 
                                       focus:outline-none"
                                    :class="activeImage === image ?
                                        'border-accent dark:border-white scale-[1.02]' :
                                        'border-transparent hover:border-zinc-300 dark:hover:border-zinc-600 opacity-70 hover:opacity-100'">


                                    <!-- THUMBNAIL IMAGE -->
                                    <img :src="image" :alt="title + ' ' + (index + 1)"
                                        class="w-full
                                           h-full
                                           object-cover">


                                    <!-- ACTIVE OVERLAY -->
                                    <div x-show="activeImage === image" x-transition.opacity
                                        class="absolute
                                           inset-0
 
                                           bg-accent/10
                                           dark:bg-white/10
 
                                           pointer-events-none">

                                    </div>


                                </button>


                            </template>


                        </div>


                        <!-- INFO -->
                        <p x-show="images.length > 1"
                            class="text-xs
                               sm:text-sm
 
                               text-zinc-400
                               dark:text-zinc-500">

                            Klik gambar untuk melihat contoh lainnya

                        </p>


                    </div>



                    <!-- =========================
                                                                                                                                                                    PRODUCT INFORMATION
                                                                                                                                                                ========================== -->
                    <div class="flex flex-col gap-3">


                        <!-- CATEGORY -->
                        <p
                            class="reveal
                               text-xs
                               font-medium
                               text-accent
                               tracking-widest
                               uppercase
                               mb-3">

                            {{ $product['label'] }}

                        </p>


                        <!-- PRODUCT TITLE -->
                        <h1
                            class="reveal
                               font-display
                               font-bold
 
                               text-4xl
                               md:text-5xl
                               lg:text-6xl
 
                               text-zinc-900
                               dark:text-white
 
                               leading-tight
                               mb-6">

                            {{ $product['title'] }}

                        </h1>


                        <!-- DESCRIPTION -->
                        <p class="reveal d1
                               text-xl
 
                               text-zinc-500
                               dark:text-zinc-400
 
                               leading-relaxed
                               max-w-2xl"
                            style="font-size: 16px;">

                            {{ $productDescription }}

                        </p>


                        <!-- =========================
                                                                                                                                                                        SHARE PRODUCT
                                                                                                                                                                    ========================== -->
                        <div x-data='shareProduct(
                            @json($product['title'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS),
                            @json($productDescription, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS)
                        )'
                            class="reveal d2 mt-6" style="margin-bottom: 15px;">


                            <!-- SHARE BUTTON -->
                            <button type="button" @click="open = true"
                                class="inline-flex
                                   items-center
                                   justify-center
                                   gap-3
 
                                   px-5
                                   py-3
 
                                   rounded-xl
 
                                   border
                                   border-zinc-300
                                   dark:border-white/15
 
                                   bg-white
                                   dark:bg-white/5
 
                                   text-zinc-800
                                   dark:text-white
 
                                   font-medium
 
                                   transition-all
                                   duration-200
 
                                   hover:border-accent
                                   hover:text-accent
 
                                   dark:hover:border-white/40
                                   dark:hover:bg-white/10
                                   dark:hover:text-white">


                                <!-- SHARE ICON -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">

                                    <circle cx="18" cy="5" r="3"></circle>

                                    <circle cx="6" cy="12" r="3"></circle>

                                    <circle cx="18" cy="19" r="3"></circle>

                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.59 13.51l6.83 3.98M15.41 6.51L8.59 10.49"></path>

                                </svg>


                                <span>Bagikan Produk</span>


                            </button>


                            <!-- =========================
                                                                                                                                                                            SHARE MODAL
                                                                                                                                                                        ========================== -->
                            <div x-show="open" x-transition.opacity @keydown.escape.window="open = false"
                                class="fixed
                                   inset-0
                                   z-[9999]
 
                                   flex
                                   items-center
                                   justify-center
 
                                   p-4
 
                                   bg-black/50
                                   backdrop-blur-sm"
                                style="display: none;">


                                <!-- MODAL -->
                                <div @click.outside="open = false" x-show="open" x-transition:
                                    enter="transition ease-out duration-200" enter-start="opacity-0 scale-95"
                                    enter-end="opacity-100 scale-100" leave="transition ease-in duration-150"
                                    leave-start="opacity-100 scale-100" leave-end="opacity-0 scale-95"
                                    class="relative
 
                                       w-full
                                       max-w-xl
 
                                       max-h-[90vh]
                                       overflow-y-auto
 
                                       rounded-2xl
 
                                       bg-white
                                       dark:bg-zinc-900
 
                                       border
                                       border-zinc-200
                                       dark:border-white/10
 
                                       p-5
                                       sm:p-6">


                                    <!-- HEADER -->
                                    <div
                                        class="flex
                                           items-center
                                           justify-between
 
                                           gap-4
                                           mb-6">


                                        <div>

                                            <h2
                                                class="text-lg
                                                   sm:text-xl
 
                                                   font-semibold
 
                                                   text-zinc-900
                                                   dark:text-white">

                                                Bagikan melalui

                                            </h2>


                                            <p
                                                class="mt-1
 
                                                   text-sm
 
                                                   text-zinc-500
                                                   dark:text-zinc-400">

                                                Bagikan halaman produk ini kepada orang lain

                                            </p>

                                        </div>


                                        <!-- CLOSE -->
                                        <button type="button" @click="open = false"
                                            class="w-9
                                               h-9
 
                                               shrink-0
 
                                               rounded-full
 
                                               flex
                                               items-center
                                               justify-center
 
                                               text-zinc-500
                                               dark:text-zinc-400
 
                                               hover:bg-zinc-100
                                               dark:hover:bg-white/10
 
                                               hover:text-zinc-900
                                               dark:hover:text-white
 
                                               transition-colors"
                                            aria-label="Tutup">


                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12"></path>

                                            </svg>


                                        </button>


                                    </div>



                                    <!-- =========================
                                                                                                                                                                                    SHARE OPTIONS
                                                                                                                                                                                ========================== -->
                                    <div
                                        class="grid
                                           grid-cols-1
                                           sm:grid-cols-2
                                           gap-3">


                                        <!-- WHATSAPP -->
                                        <button type="button" @click="share('whatsapp')" class="share-option">

                                            <span
                                                class="w-11 h-11 rounded-full flex items-center justify-center bg-green-500 text-white font-bold">
                                                WA
                                            </span>

                                            <span>WhatsApp</span>

                                        </button>


                                        <!-- FACEBOOK -->
                                        <button type="button" @click="share('facebook')" class="share-option">

                                            <span
                                                class="w-11 h-11 rounded-full flex items-center justify-center bg-blue-600 text-white text-xl font-bold">
                                                f
                                            </span>

                                            <span>Facebook</span>

                                        </button>


                                        <!-- X -->
                                        <button type="button" @click="share('x')" class="share-option">

                                            <span
                                                class="w-11 h-11 rounded-full flex items-center justify-center bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-bold text-lg">
                                                X
                                            </span>

                                            <span>X</span>

                                        </button>


                                        <!-- TELEGRAM -->
                                        <button type="button" @click="share('telegram')" class="share-option">

                                            <span
                                                class="w-11 h-11 rounded-full flex items-center justify-center bg-sky-500 text-white text-xl">
                                                ✈
                                            </span>

                                            <span>Telegram</span>

                                        </button>


                                        <!-- LINE -->
                                        <button type="button" @click="share('line')" class="share-option">

                                            <span
                                                class="w-11 h-11 rounded-full flex items-center justify-center bg-green-600 text-white font-bold text-xl">
                                                L
                                            </span>

                                            <span>LINE</span>

                                        </button>


                                        <!-- PINTEREST -->
                                        <button type="button" @click="share('pinterest')" class="share-option">

                                            <span
                                                class="w-11 h-11 rounded-full flex items-center justify-center bg-red-600 text-white font-bold text-lg">
                                                P
                                            </span>

                                            <span>Pinterest</span>

                                        </button>


                                        <!-- EMAIL -->
                                        <button type="button" @click="share('email')" class="share-option">

                                            <span
                                                class="w-11 h-11 rounded-full flex items-center justify-center bg-red-500 text-white font-bold">
                                                &#64;
                                            </span>

                                            <span>Email</span>

                                        </button>


                                        <!-- COPY LINK -->
                                        <button type="button" @click="copyLink()" class="share-option">

                                            <span
                                                class="w-11 h-11 rounded-full flex items-center justify-center bg-zinc-600 dark:bg-zinc-700 text-white">

                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M13.828 10.172a4 4 0 015.656 5.656l-3 3a4 4 0 01-5.656 0m-1.656-5.172a4 4 0 00-5.656-5.656l-3 3a4 4 0 000 5.656">
                                                    </path>

                                                </svg>

                                            </span>

                                            <span x-text="copied ? 'Link tersalin!' : 'Salin link'"></span>

                                        </button>


                                    </div>



                                    <!-- =========================
                                                                                                                                                                                    OTHER APPS
                                                                                                                                                                                ========================== -->
                                    <button type="button" @click="shareNative()"
                                        class="share-option
                                           w-full
                                           mt-3">


                                        <span
                                            class="w-11 h-11 rounded-full flex items-center justify-center bg-zinc-200 dark:bg-white/10 text-zinc-700 dark:text-white">

                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">

                                                <circle cx="5" cy="12" r="1.5"></circle>

                                                <circle cx="12" cy="12" r="1.5"></circle>

                                                <circle cx="19" cy="12" r="1.5"></circle>

                                            </svg>

                                        </span>

                                        <span>Aplikasi lainnya</span>

                                    </button>


                                    <!-- COPY MESSAGE -->
                                    <p x-show="copied" x-transition
                                        class="mt-4
 
                                           text-sm
                                           text-center
 
                                           text-green-600
                                           dark:text-green-400"
                                        style="display: none;">

                                        Link berhasil disalin ke clipboard

                                    </p>


                                </div>


                            </div>


                        </div>

                        <p class="reveal d1
                        text-xl
 
                        text-zinc-500
                        dark:text-zinc-400
 
                        leading-relaxed
                        max-w-2xl"
                            style="font-size: 16px;">Pre-order. Estimasi proses 2 hari kerja. Produk diperkirakan tersedia
                            mulai 01 September 2026.</p>
                    </div>
                </div>

                @if ($product['label'] === 'Display' || $product['label'] === 'Large Format')
                    <div x-data="{
                        /* =========================================
                         * MASTER DATA
                         * ========================================= */
                        sizes: [{
                                id: 'a5',
                                name: 'A5',
                                multiplier: 0.5
                            },
                            {
                                id: 'a4',
                                name: 'A4',
                                multiplier: 1
                            },
                            {
                                id: 'a3',
                                name: 'A3',
                                multiplier: 2
                            },
                            {
                                id: 'a3plus',
                                name: 'A3+',
                                multiplier: 2.2
                            }
                        ],
                    
                        materials: [{
                                id: 'art-paper-100',
                                name: 'Art Paper 100 gsm',
                                price: 900
                            },
                            {
                                id: 'art-paper-120',
                                name: 'Art Paper 120 gsm',
                                price: 1100
                            },
                            {
                                id: 'art-paper-150',
                                name: 'Art Paper 150 gsm',
                                price: 1400
                            },
                            {
                                id: 'art-paper-210',
                                name: 'Art Paper 210 gsm',
                                price: 1800
                            },
                            {
                                id: 'ivory-230',
                                name: 'Ivory 230 gsm',
                                price: 2200
                            }
                        ],
                    
                        finishings: [{
                                id: 'laminasi-glossy',
                                name: 'Laminasi Glossy',
                                price: 500
                            },
                            {
                                id: 'laminasi-doff',
                                name: 'Laminasi Doff',
                                price: 700
                            },
                            {
                                id: 'potong',
                                name: 'Potong',
                                price: 200
                            },
                            {
                                id: 'lipat',
                                name: 'Lipat',
                                price: 300
                            }
                        ],
                    
                        /* =========================================
                         * FORM VALUE
                         * ========================================= */
                        size: 'a4',
                        material: 'art-paper-120',
                        printSide: '1',
                        colorMode: 'full-color',
                        selectedFinishings: [],
                        quantity: 100,
                    
                        /* =========================================
                         * GET SELECTED SIZE
                         * ========================================= */
                        get selectedSize() {
                            return this.sizes.find(item => item.id === this.size) || this.sizes[0];
                        },
                    
                        /* =========================================
                         * GET SELECTED MATERIAL
                         * ========================================= */
                        get selectedMaterial() {
                            return this.materials.find(item => item.id === this.material) || this.materials[0];
                        },
                    
                        /* =========================================
                         * MATERIAL COST
                         * ========================================= */
                        get materialCost() {
                            return this.selectedMaterial.price * this.selectedSize.multiplier;
                        },
                    
                        /* =========================================
                         * PRINT COST
                         * ========================================= */
                        get printCost() {
                            let price = this.colorMode === 'full-color' ?
                                1500 :
                                500;
                    
                            if (this.printSide === '2') {
                                price *= 2;
                            }
                    
                            return price * this.selectedSize.multiplier;
                        },
                    
                        /* =========================================
                         * FINISHING COST
                         * ========================================= */
                        get finishingCost() {
                            return this.selectedFinishings.reduce((total, finishingId) => {
                                const finishing = this.finishings.find(
                                    item => item.id === finishingId
                                );
                    
                                return total + (finishing ? finishing.price : 0);
                            }, 0);
                        },
                    
                        /* =========================================
                         * COST PER PCS
                         * ========================================= */
                        get costPerPcs() {
                            return this.materialCost +
                                this.printCost +
                                this.finishingCost;
                        },
                    
                        /* =========================================
                         * TOTAL COST
                         * ========================================= */
                        get totalPrice() {
                            return this.costPerPcs * Math.max(this.quantity, 1);
                        },
                    
                        /* =========================================
                         * FORMAT RUPIAH
                         * ========================================= */
                        formatCurrency(value) {
                            return new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(value);
                        },
                    
                        /* =========================================
                         * FINISHING
                         * ========================================= */
                        toggleFinishing(id) {
                            if (this.selectedFinishings.includes(id)) {
                                this.selectedFinishings =
                                    this.selectedFinishings.filter(item => item !== id);
                    
                                return;
                            }
                    
                            this.selectedFinishings.push(id);
                        },
                    
                        isFinishingSelected(id) {
                            return this.selectedFinishings.includes(id);
                        },
                    
                        /* =========================================
                         * QUANTITY
                         * ========================================= */
                        increaseQuantity() {
                            this.quantity = Math.max(
                                50,
                                Number(this.quantity || 0) + 50
                            );
                        },
                    
                        decreaseQuantity() {
                            this.quantity = Math.max(
                                50,
                                Number(this.quantity || 0) - 50
                            );
                        },
                    
                        normalizeQuantity() {
                            let value = Number(this.quantity);
                    
                            if (!Number.isFinite(value) || value < 50) {
                                this.quantity = 50;
                                return;
                            }
                    
                            value = Math.round(value / 50) * 50;
                    
                            this.quantity = Math.max(50, value);
                        }
                    }"
                        class="estimator reveal d2 grid grid-cols-1 gap-4 py-8 border-t border-b border-zinc-100 dark:border-zinc-900"
                        style="margin-top: 20px; border-bottom: none !important;">


                        <!-- =========================
                                                                                                                            HEADER
                                                                                                                        ========================== -->

                        <div>

                            <p
                                class="font-medium
                                    text-zinc-900
                                    dark:text-white
                                    text-lg">

                                Large Format Estimator

                            </p>


                            <p
                                class="text-xs
                                    text-zinc-400
                                    tracking-widest
                                    mb-1">

                                Kategori dan material tersaring sesuai kapabilitas sumber produksi.

                            </p>

                        </div>



                        <!-- =========================
                                                                                                                            ESTIMATOR FORM
                                                                                                                        ========================== -->

                        <div
                            class="grid
                                grid-cols-1
                                lg:grid-cols-3
                                gap-6
                                mt-4">


                            <!-- =========================
                                                                                                LEFT FORM
                                                                                            ========================== -->

                            <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-5">

                                <!-- =========================
                                                                                                    INFORMASI
                                                                                                ========================= -->

                                <div class="sm:col-span-2">
                                    <h4 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">
                                        Informasi
                                    </h4>
                                </div>

                                <div>
                                    <label for="location_id"
                                        class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                        Lokasi
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <select id="location_id" name="location_id" required
                                        class="select2 w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-accent transition-colors">
                                        <option value="">Pilih lokasi...</option>

                                        @foreach ($locations ?? [] as $location)
                                            <option value="{{ $location->id }}">
                                                {{ $location->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div id="vendorField" style="display: none;">
                                    <label for="vendor_id"
                                        class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                        Vendor
                                    </label>

                                    <select id="vendor_id" name="vendor_id"
                                        class="select2 w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-accent transition-colors">
                                        <option value="">Pilih vendor...</option>

                                        @foreach ($vendors ?? [] as $vendor)
                                            <option value="{{ $vendor->id }}">
                                                {{ $vendor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="price_type"
                                        class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                        Jenis Harga
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <select id="price_type" name="price_type" required
                                        class="select2 w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 focus:outline-none focus:border-accent transition-colors">
                                        <option value="">Pilih jenis harga...</option>
                                        <option value="general">Harga Umum</option>
                                        <option value="division">Harga Divisi</option>
                                    </select>
                                </div>


                                <!-- =========================
                                                                                                    SPESIFIKASI CETAK
                                                                                                ========================= -->

                                <div class="sm:col-span-2 mt-2">
                                    <h4 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3">
                                        Spesifikasi Cetak
                                    </h4>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                        Kategori
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <input type="hidden" id="category_id" name="category_id"
                                        value="{{ $estimator['categoryId'] ?? '' }}"
                                        data-category-name="{{ ($product['type'] ?? null) === 'display' ? $product['label'] ?? '' : $product['title'] ?? '' }}">

                                    <div
                                        class="w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3">
                                        {{ ($product['type'] ?? null) === 'display' ? $product['label'] ?? '' : $product['title'] ?? '' }}
                                    </div>
                                </div>

                                <div>
                                    <label for="material_id"
                                        class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                        Material
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <select id="material_id" name="material_id" required
                                        class="select2 w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3">
                                        <option value="">Pilih material...</option>
                                    </select>
                                </div>

                                <div id="lengthField">
                                    <label for="length"
                                        class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                        Panjang
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <div class="relative">
                                        <input type="number" id="length" name="length" min="0.01"
                                            step="any" required placeholder="Contoh: 100"
                                            class="w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 pr-12">
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-zinc-500">
                                            cm
                                        </span>
                                    </div>
                                </div>

                                <div id="widthField">
                                    <label for="width"
                                        class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                        Lebar
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <div class="relative">
                                        <input type="number" id="width" name="width" min="0.01"
                                            step="any" required placeholder="Contoh: 50"
                                            class="w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 pr-12">
                                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-zinc-500">
                                            cm
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <label for="qty"
                                        class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                        Quantity
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <input type="number" id="qty" name="qty" min="1" step="1"
                                        value="1" required
                                        class="w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3">
                                </div>

                                <div id="materialSizeField">
                                    <label for="material_size_id"
                                        class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                        Ukuran Material
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <select id="material_size_id" name="material_size_id" required
                                        class="select2 w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3">
                                        <option value="">Pilih ukuran material...</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="lamination_id"
                                        class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                        Laminasi
                                    </label>

                                    <select id="lamination_id" name="lamination_id"
                                        class="select2 w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3">
                                        <option value="none">Tanpa Laminasi</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="lamination_size_id"
                                        class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                        Ukuran Laminasi
                                    </label>

                                    <select id="lamination_size_id" name="lamination_size_id"
                                        class="select2 w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3">
                                        <option value="">Pilih ukuran laminasi...</option>
                                    </select>
                                </div>


                                <!-- =========================
                                                                                                    HIDDEN DISPLAY IDENTITY
                                                                                                ========================= -->

                                <input type="hidden" name="display_configuration_id" id="display_configuration_id">

                                <input type="hidden" name="display_product_id" id="display_product_id">

                                <input type="hidden" name="display_component_id" id="display_component_id">

                                <input type="hidden" name="display_component_type" id="display_component_type">


                                <!-- =========================
                                                                                                    KOMPONEN TAMBAHAN
                                                                                                ========================= -->

                                <div class="sm:col-span-2 mt-2">

                                    <div class="flex items-center justify-between mb-3">

                                        <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">
                                            Komponen Tambahan
                                        </h4>

                                        <button type="button" id="addAdditionalComponentButton"
                                            class="text-xs font-medium text-accent hover:underline">
                                            + Tambah Komponen
                                        </button>

                                    </div>

                                    <div id="additionalComponentsContainer" class="space-y-4">
                                    </div>

                                </div>


                                <!-- =========================
                                            HARGA MATERIAL
                                        ========================= -->

                                {{-- <div class="sm:col-span-2">

                                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                        Harga Material
                                    </label>

                                    <div id="materialPrice" class="text-sm font-semibold text-zinc-900 dark:text-white">
                                        Rp 0
                                    </div>

                                </div> --}}


                                <!-- =========================
                                                                                                    DISKON
                                                                                                ========================= -->

                                <div class="sm:col-span-2">

                                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-2">
                                        Diskon
                                    </label>

                                    <div class="discount-options grid grid-cols-1 sm:grid-cols-3 gap-3">

                                        <label class="discount-option-card">
                                            <input type="radio" name="discount_type" value="none" checked>
                                            <span class="discount-option-icon">
                                                <i class="bi bi-tag" aria-hidden="true"></i>
                                            </span>
                                            <span class="discount-option-copy">
                                                <span class="discount-option-title">Tanpa Diskon</span>
                                            </span>
                                        </label>

                                        <label class="discount-option-card">
                                            <input type="radio" name="discount_type" value="percentage">
                                            <span class="discount-option-icon">
                                                <i class="bi bi-percent" aria-hidden="true"></i>
                                            </span>
                                            <span class="discount-option-copy">
                                                <span class="discount-option-title">Diskon Persen</span>
                                            </span>
                                        </label>

                                        <label class="discount-option-card">
                                            <input type="radio" name="discount_type" value="nominal">
                                            <span class="discount-option-icon">
                                                <i class="bi bi-cash" aria-hidden="true"></i>
                                            </span>
                                            <span class="discount-option-copy">
                                                <span class="discount-option-title">Diskon Nominal</span>
                                            </span>
                                        </label>

                                    </div>

                                </div>

                                <div id="discountValueField" class="sm:col-span-2" style="display: none;">

                                    <label id="discountValueLabel" for="discount_value"
                                        class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                        Nilai Diskon
                                    </label>

                                    <div id="discountInputWrapper" class="relative">

                                        <input type="text" id="discount_value" name="discount_value" disabled
                                            class="w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3">

                                        <span id="discountSuffix"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-xs text-zinc-500">
                                        </span>

                                    </div>

                                </div>


                            </div>



                            <!-- =========================
                                                                                                    PRICE SUMMARY
                                                                                                ========================== -->

                            <div
                                class="h-fit
        
                                    rounded-2xl
        
                                    p-5
        
                                    border
                                    border-zinc-200
                                    dark:border-zinc-800
        
                                    bg-zinc-50
                                    dark:bg-white/5">


                                <!-- TITLE -->

                                <p
                                    class="text-sm
                                        font-medium
        
                                        text-zinc-900
                                        dark:text-white">

                                    Estimasi Harga

                                </p>


                                <p
                                    class="text-xs
                                        text-zinc-400
                                        mt-1">

                                    Perkiraan berdasarkan konfigurasi saat ini

                                </p>



                                <!-- DETAIL -->

                                <p id="estimateStatus" class="text-xs text-zinc-400 mt-4" aria-live="polite">
                                    Pilih lokasi, jenis harga, dan material untuk melihat estimasi.
                                </p>

                                <div id="estimateBreakdown" class="mt-6 space-y-3" hidden>
                                    <div class="flex justify-between gap-4">
                                        <div class="min-w-0">
                                            <span id="estimateMaterialLabel"
                                                class="block text-sm text-zinc-500 dark:text-zinc-400">
                                                Material
                                            </span>
                                            <span id="estimateMaterialMeta"
                                                class="block text-xs text-zinc-400 mt-1"></span>
                                        </div>
                                        <span id="estimateMaterialAmount"
                                            class="shrink-0 text-sm text-zinc-700 dark:text-zinc-200">
                                            Rp 0
                                        </span>
                                    </div>

                                    <div id="estimateLaminationRow" class="flex justify-between gap-4" hidden>
                                        <div class="min-w-0">
                                            <span id="estimateLaminationLabel"
                                                class="block text-sm text-zinc-500 dark:text-zinc-400">
                                                Laminasi
                                            </span>
                                            <span id="estimateLaminationMeta"
                                                class="block text-xs text-zinc-400 mt-1"></span>
                                        </div>
                                        <span id="estimateLaminationAmount"
                                            class="shrink-0 text-sm text-zinc-700 dark:text-zinc-200">
                                            Rp 0
                                        </span>
                                    </div>

                                    <div id="estimateComponentsRow" class="space-y-3" hidden>
                                        <div id="estimateComponentItems" class="space-y-3"></div>
                                        <div
                                            class="flex justify-between gap-4 border-t border-zinc-200 dark:border-zinc-700 pt-3">
                                            <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                                Total komponen tambahan
                                            </span>
                                            <span id="estimateComponentsAmount"
                                                class="shrink-0 text-sm text-zinc-700 dark:text-zinc-200">
                                                Rp 0
                                            </span>
                                        </div>
                                    </div>

                                    <div id="estimateDiscountRow" class="flex justify-between gap-4" hidden>
                                        <span class="text-sm text-zinc-500 dark:text-zinc-400">
                                            Diskon
                                        </span>
                                        <span id="estimateDiscountAmount"
                                            class="text-sm text-zinc-700 dark:text-zinc-200">
                                            Rp 0
                                        </span>
                                    </div>
                                </div>



                                <!-- DIVIDER -->

                                <div
                                    class="border-t
                                        border-zinc-200
                                        dark:border-zinc-700
                                        my-5">

                                </div>



                                <!-- TOTAL -->

                                <div>

                                    <p class="text-xs
                                            text-zinc-400">

                                        Total Estimasi

                                    </p>


                                    <p id="estimateGrandTotal"
                                        class="mt-2 text-2xl font-bold text-zinc-900 dark:text-white">
                                        —
                                    </p>

                                </div>



                                <!-- BUTTON -->

                                <button type="button" id="continueOrderButton" disabled
                                    class="w-full
        
                                        mt-6
        
                                        py-3
        
                                        rounded-xl
        
                                        bg-accent
        
                                        text-white
        
                                        text-sm
                                        font-medium
        
                                        hover:opacity-90
                                        disabled:opacity-50
                                        disabled:cursor-not-allowed
        
                                        transition-opacity">

                                    Lanjutkan Pesanan →

                                </button>

                                <!-- =========================
                                                                                                    ADD TO BASKET BUTTON
                                                                                                ========================= -->

                                <button type="button"
                                    @click="
                                        isInBasket(@js($product['basketItem']['id']))
                                            ? removeFromBasket(@js($product['basketItem']['id']))
                                            : addToBasket(@js($product['basketItem']))
                                    "
                                    class="w-full
                                        mt-6
                                        py-3
                                        rounded-xl
                                        text-white
                                        cart-button
                                        text-sm
                                        font-medium
                                        hover:opacity-90
                                        transition-opacity
                                        duration-200"
                                    style="
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        gap: 10px;
                                    ">


                                    <!-- TAMBAH KE KERANJANG -->

                                    <template x-if="!isInBasket(@js($product['basketItem']['id']))">

                                        <span
                                            class="flex
                                                items-center
                                                justify-center
                                                gap-2">

                                            Tambahkan ke Keranjang

                                            <i class="bi bi-cart-plus"></i>

                                        </span>

                                    </template>


                                    <!-- HAPUS DARI KERANJANG -->

                                    <template x-if="isInBasket(@js($product['basketItem']['id']))">

                                        <span
                                            class="flex
                                                items-center
                                                justify-center
                                                gap-2">

                                            Hapus dari Keranjang

                                            <i class="bi bi-cart-x"></i>

                                        </span>

                                    </template>


                                </button>
                            </div>


                        </div>


                        <!-- =========================
                                                                                                                            INFORMATION
                                                                                                                        ========================== -->

                        <p
                            class="text-xs
                                text-zinc-400
                                dark:text-zinc-500
                                mt-2">

                            * Harga yang ditampilkan merupakan estimasi sementara dan dapat berubah sesuai konfigurasi
                            produksi.

                        </p>


                    </div>
                @endif
            </header>


        </article>
    </div>
@endsection



@section('scripts')
    <script>
        /* =========================
                                                                    SHARE PRODUCT
                                                                    ========================== */

        function shareProduct(title = '', description = '') {

            return {

                open: false,

                copied: false,

                title: title,

                description: description,


                get url() {

                    return window.location.href;

                },


                /* =========================
                   SHARE SOCIAL MEDIA
                ========================== */

                share(platform) {

                    const url = encodeURIComponent(this.url);

                    const text = encodeURIComponent(
                        this.title + ' - ' + this.description
                    );


                    let shareUrl = '';


                    switch (platform) {


                        case 'whatsapp':

                            shareUrl =
                                'https://wa.me/?text=' +
                                encodeURIComponent(
                                    this.title +
                                    '\n\n' +
                                    this.description +
                                    '\n\n' +
                                    this.url
                                );

                            break;


                        case 'facebook':

                            shareUrl =
                                'https://www.facebook.com/sharer/sharer.php?u=' +
                                url;

                            break;


                        case 'x':

                            shareUrl =
                                'https://twitter.com/intent/tweet?text=' +
                                text +
                                '&url=' +
                                url;

                            break;


                        case 'telegram':

                            shareUrl =
                                'https://t.me/share/url?url=' +
                                url +
                                '&text=' +
                                text;

                            break;


                        case 'line':

                            shareUrl =
                                'https://social-plugins.line.me/lineit/share?url=' +
                                url;

                            break;


                        case 'pinterest':

                            shareUrl =
                                'https://www.pinterest.com/pin/create/button/?url=' +
                                url +
                                '&description=' +
                                text;

                            break;


                        case 'email':

                            window.location.href =
                                'mailto:?subject=' +
                                encodeURIComponent(this.title) +
                                '&body=' +
                                encodeURIComponent(
                                    this.description +
                                    '\n\n' +
                                    this.url
                                );

                            return;

                    }


                    if (shareUrl) {

                        window.open(
                            shareUrl,
                            '_blank',
                            'width=650,height=600'
                        );

                    }

                },


                /* =========================
                   COPY LINK
                ========================== */

                async copyLink() {

                    try {

                        await navigator.clipboard.writeText(this.url);

                    } catch (error) {

                        const textarea =
                            document.createElement('textarea');


                        textarea.value = this.url;


                        textarea.style.position = 'fixed';

                        textarea.style.opacity = '0';


                        document.body.appendChild(textarea);


                        textarea.select();


                        document.execCommand('copy');


                        textarea.remove();

                    }


                    this.copied = true;


                    setTimeout(() => {

                        this.copied = false;

                    }, 2500);

                },


                /* =========================
                   NATIVE SHARE
                ========================== */

                async shareNative() {

                    if (navigator.share) {

                        try {

                            await navigator.share({

                                title: this.title,

                                text: this.description,

                                url: this.url

                            });

                        } catch (error) {

                            /*
                             * User membatalkan share
                             * Tidak perlu menampilkan error
                             */

                        }

                    } else {

                        /*
                         * Jika browser desktop
                         * tidak mendukung Web Share API,
                         * gunakan copy link
                         */

                        this.copyLink();

                    }

                }

            }

        }



        /* =========================
           EXISTING COMPONENT SCRIPT
        ========================== */

        document.addEventListener(
            'DOMContentLoaded',
            function() {

                const addComponentButton =
                    document.getElementById('add-component');


                const componentContainer =
                    document.getElementById('component-container');


                if (
                    !addComponentButton ||
                    !componentContainer
                ) {

                    return;

                }


                addComponentButton.addEventListener(
                    'click',
                    function() {

                        const componentRow =
                            document.createElement('div');


                        componentRow.className =
                            'component-row grid sm:grid-cols-2 gap-4';


                        componentRow.innerHTML = `
                        <div>
 
                            <label
                                for="component_type"
                                class="block text-xs font-medium text-zinc-400 mb-1.5">
 
                                Tipe
                                <span aria-hidden="true">*</span>
 
                            </label>
 
 
                            <select
                                id="component_type"
                                name="component_type"
                                required
 
                                class="select2 w-full
                                       bg-zinc-850
                                       border
                                       border-zinc-700
                                       text-white
                                       text-sm
                                       rounded-xl
                                       px-4
                                       py-3
                                       placeholder-zinc-600
                                       focus:outline-none
                                       focus:border-accent
                                       transition-colors">
 
                                <option value="material">
 
                                    Material
 
                                </option>
 
                                <option value="finishing">
 
                                    Finishing
 
                                </option>
 
                                <option value="jasa pemasangan">
 
                                    Jasa Pemasangan
 
                                </option>
 
                            </select>
 
                        </div>
                    `;


                        componentContainer.appendChild(componentRow);

                        $(componentRow).find('.select2').select2({
                            width: '100%',
                            placeholder: 'Pilih...'
                        });

                    }
                );


                componentContainer.addEventListener(
                    'click',
                    function(event) {

                        const removeButton =
                            event.target.closest('.remove-component');


                        if (!removeButton) {

                            return;

                        }


                        const componentRow =
                            removeButton.closest('.component-row');


                        if (componentRow) {

                            componentRow.remove();

                        }

                    }
                );


            }
        );

        document.addEventListener('DOMContentLoaded', function() {

            /* =========================
               LARGE FORMAT ESTIMATOR
            ========================== */

            const locationSelect = $('#location_id');
            const vendorSelect = $('#vendor_id');
            const vendorField = $('#vendorField');

            const categorySelect = $('#category_id');
            const materialSelect = $('#material_id');

            const materialSizeSelect = $('#material_size_id');

            const laminationSelect = $('#lamination_id');
            const laminationSizeSelect = $('#lamination_size_id');

            const displayConfigurationInput =
                $('#display_configuration_id');

            const displayProductInput =
                $('#display_product_id');

            const displayComponentInput =
                $('#display_component_id');

            const displayComponentTypeInput =
                $('#display_component_type');


            /* =========================
               SELECT2
            ========================== */

            $('.select2').select2({
                width: '100%',
                placeholder: 'Pilih...'
            });


            /* =========================
               HIDDEN PRICE SIZE
            ========================== */

            const materialPriceSizeInput = $('<input>', {
                type: 'hidden',
                name: 'material_price_size_id',
                id: 'material_price_size_id'
            });

            const laminationPriceSizeInput = $('<input>', {
                type: 'hidden',
                name: 'lamination_price_size_id',
                id: 'lamination_price_size_id'
            });


            const estimatorContainer =
                $('#largeFormatEstimationForm');

            if (estimatorContainer.length) {

                estimatorContainer.append(
                    materialPriceSizeInput,
                    laminationPriceSizeInput
                );

            } else {

                $('body').append(
                    materialPriceSizeInput,
                    laminationPriceSizeInput
                );

            }


            /* =========================
               CUSTOM WIDTH
            ========================== */

            const materialCustomWidthInput = $(`
                <input
                    type="number"
                    id="material_custom_width"
                    name="material_custom_width"
                    class="form-control mt-2"
                    placeholder="Masukkan lebar bahan (cm)"
                    min="0.01"
                    step="any"
                    style="display:none;"
                    disabled>
            `);

            const laminationCustomWidthInput = $(`
                <input
                    type="number"
                    id="lamination_custom_width"
                    name="lamination_custom_width"
                    class="form-control mt-2"
                    placeholder="Masukkan lebar laminasi (cm)"
                    min="0.01"
                    step="any"
                    style="display:none;"
                    disabled>
            `);


            $('#material_size_id')
                .closest('div')
                .append(materialCustomWidthInput);

            $('#lamination_size_id')
                .closest('div')
                .append(laminationCustomWidthInput);


            /* =========================
               URL ENDPOINT
            ========================== */

            const materialsUrl =
                "{{ route('user_get_materials') }}";

            const materialSizesUrl =
                "{{ route('user_get_material_sizes') }}";

            const laminationsUrl =
                "{{ route('user_get_laminations') }}";

            const laminationSizesUrl =
                "{{ route('user_get_laminations_sizes') }}";

            const estimationPreviewUrl =
                "{{ route('user_preview_lf_estimation') }}";

            const csrfToken = @json(csrf_token());


            const engineName = 'Large Format';


            /* =========================
               DISPLAY CATEGORY
            ========================== */

            function isDisplayCategory() {

                return String(
                    categorySelect.attr('data-category-name') || ''
                ).trim().toLowerCase() === 'display';
            }


            function resetDisplayIdentity() {

                displayConfigurationInput.val('');
                displayProductInput.val('');
                displayComponentInput.val('');
                displayComponentTypeInput.val('');

            }


            /* =========================
               CUSTOM WIDTH TOGGLE
            ========================== */

            function toggleCustomWidth(type) {

                const isMaterial =
                    type === 'material';

                const select =
                    isMaterial ?
                    materialSizeSelect :
                    laminationSizeSelect;

                const input =
                    isMaterial ?
                    materialCustomWidthInput :
                    laminationCustomWidthInput;

                if (select.val() === '__custom__') {

                    input
                        .show()
                        .prop('disabled', false)
                        .prop('required', true);

                } else {

                    input
                        .val('')
                        .hide()
                        .prop('disabled', true)
                        .prop('required', false);

                }

            }


            /* =========================
               ERROR MESSAGE
            ========================== */

            function getErrorMessage(result, fallback) {

                if (result && result.message) {
                    return result.message;
                }

                if (
                    result &&
                    result.errors &&
                    typeof result.errors === 'object'
                ) {

                    const firstError =
                        Object.values(result.errors)[0];

                    if (Array.isArray(firstError) && firstError.length) {
                        return firstError[0];
                    }

                    if (typeof firstError === 'string') {
                        return firstError;
                    }

                }

                return fallback;

            }

            let estimationPreviewRequest = null;
            let estimationPreviewTimer = null;
            let estimationPreviewSequence = 0;
            let latestEstimationMessage = '';

            const continueOrderButton =
                document.getElementById('continueOrderButton');
            const whatsappPhoneNumber = '6282213290760';

            function setContinueOrderAvailable(isAvailable) {
                continueOrderButton.disabled = !isAvailable;

                if (!isAvailable) {
                    latestEstimationMessage = '';
                }
            }

            function formatCurrencyResult(value) {
                return 'Rp ' + new Intl.NumberFormat('id-ID', {
                    maximumFractionDigits: 0
                }).format(Number(value) || 0);
            }

            function setEstimationStatus(message, isError) {
                $('#estimateStatus')
                    .text(message)
                    .toggleClass('text-red-500', Boolean(isError))
                    .toggleClass('text-zinc-400', !isError);
            }

            function resetEstimationPreview(message, isError) {
                if (estimationPreviewRequest) {
                    estimationPreviewRequest.abort();
                    estimationPreviewRequest = null;
                }

                $('#estimateBreakdown').prop('hidden', true);
                $('#estimateGrandTotal').text('—');
                $('#estimateComponentItems').empty();
                setContinueOrderAvailable(false);
                setEstimationStatus(message, isError);
            }

            function getEstimationPreviewPayload() {
                const locationId = locationSelect.val();
                const categoryId = categorySelect.val();
                const priceType = $('#price_type').val();
                const materialId = materialSelect.val();
                const quantity = Number($('#qty').val());
                const display = isDisplayCategory();
                const locationName = locationSelect
                    .find('option:selected')
                    .text()
                    .trim()
                    .toLowerCase();

                if (!locationId || !priceType || !materialId) {
                    return {
                        message: 'Pilih lokasi, jenis harga, dan material untuk melihat estimasi.'
                    };
                }

                if (display && locationName !== 'purwakarta') {
                    return {
                        message: 'Perhitungan produk Display saat ini tersedia untuk lokasi Purwakarta.'
                    };
                }

                if (!Number.isInteger(quantity) || quantity < 1) {
                    return {
                        message: 'Masukkan jumlah produk yang valid untuk melihat estimasi.'
                    };
                }

                const materialSizeValue = materialSizeSelect.val();
                const isCustomMaterialSize = materialSizeValue === '__custom__';
                const materialCustomWidth = materialCustomWidthInput.val();

                if (!display) {
                    if (!$('#length').val() || !$('#width').val()) {
                        return {
                            message: 'Masukkan panjang dan lebar produk untuk melihat estimasi.'
                        };
                    }

                    if (
                        isCustomMaterialSize &&
                        (!materialCustomWidth || !materialPriceSizeInput.val())
                    ) {
                        return {
                            message: 'Pilih ukuran referensi dan isi lebar material custom.'
                        };
                    }

                    if (!isCustomMaterialSize && !materialSizeValue) {
                        return {
                            message: 'Pilih ukuran material untuk melihat estimasi.'
                        };
                    }
                } else if (
                    !displayConfigurationInput.val() ||
                    !displayProductInput.val()
                ) {
                    return {
                        message: 'Pilih konfigurasi produk Display untuk melihat estimasi.'
                    };
                }

                const laminationId = laminationSelect.val();
                const laminationSizeValue = laminationSizeSelect.val();
                const isCustomLaminationSize =
                    laminationSizeValue === '__custom__';

                if (laminationId && laminationId !== 'none') {
                    if (
                        isCustomLaminationSize &&
                        (
                            !laminationCustomWidthInput.val() ||
                            !laminationPriceSizeInput.val()
                        )
                    ) {
                        return {
                            message: 'Pilih ukuran referensi dan isi lebar laminasi custom.'
                        };
                    }

                    if (!isCustomLaminationSize && !laminationSizeValue) {
                        return {
                            message: 'Pilih ukuran laminasi untuk melihat estimasi.'
                        };
                    }
                }

                const additionalComponents = [];

                additionalComponentsContainer
                    .querySelectorAll('.additional-component-item')
                    .forEach(function(component) {
                        const priceType = component.querySelector(
                            '.additional-component-price-type'
                        ).value;
                        const unitPrice = component.querySelector(
                            '.additional-component-unit-price'
                        ).value;
                        const hpp = component.querySelector(
                            '.additional-component-hpp'
                        ).value;
                        const margin = component.querySelector(
                            '.additional-component-margin'
                        ).value;
                        const item = {
                            type: component.querySelector(
                                '.additional-component-type'
                            ).value,
                            name: component.querySelector(
                                '.additional-component-name'
                            ).value.trim(),
                            qty: component.querySelector(
                                '.additional-component-qty'
                            ).value,
                            price_type: priceType,
                            unit_price: unitPrice.replace(/\D/g, ''),
                            hpp: hpp.replace(/\D/g, ''),
                            margin: margin
                        };

                        const hasPrice = priceType === 'hpp_margin' ?
                            hpp !== '' && margin !== '' :
                            unitPrice !== '';

                        if (item.type && item.name && item.qty && hasPrice) {
                            additionalComponents.push(item);
                        }
                    });

                const discountType = document.querySelector(
                    'input[name="discount_type"]:checked'
                )?.value || 'none';
                const discountValue = $('#discount_value').val() || '';

                return {
                    payload: {
                        location_id: locationId,
                        vendor_id: vendorSelect.val() || '',
                        price_type: priceType,
                        category_id: categoryId,
                        material_id: materialId,
                        length: $('#length').val() || '',
                        width: $('#width').val() || '',
                        qty: quantity,
                        material_size_id: isCustomMaterialSize ?
                            '' : (materialSizeValue || ''),
                        material_price_size_id: materialPriceSizeInput.val() || '',
                        material_custom_width: isCustomMaterialSize ?
                            materialCustomWidth : '',
                        lamination_id: laminationId === 'none' ?
                            '' : (laminationId || ''),
                        lamination_size_id: isCustomLaminationSize ?
                            '' : (laminationSizeValue || ''),
                        lamination_price_size_id: laminationPriceSizeInput.val() || '',
                        lamination_custom_width: isCustomLaminationSize ?
                            laminationCustomWidthInput.val() : '',
                        display_configuration_id: displayConfigurationInput.val() || '',
                        display_product_id: displayProductInput.val() || '',
                        display_component_id: displayComponentInput.val() || '',
                        display_component_type: displayComponentTypeInput.val() || '',
                        discount_type: discountType,
                        discount_value: discountType === 'nominal' ?
                            discountValue.replace(/\D/g, '') : discountValue,
                        additional_components: additionalComponents,
                        _token: csrfToken
                    }
                };
            }

            function renderEstimationPreview(data) {
                const componentsSubtotal =
                    Number(data.additional_components_subtotal) || 0;
                const laminationSubtotal =
                    Number(data.lamination_subtotal) || 0;
                const discountAmount =
                    Number(data.discount_amount) || 0;
                const materialQuantity =
                    Number(data.material_quantity) || 0;
                const materialUnit = data.material_quantity_unit || 'm²';

                $('#estimateMaterialLabel').text(data.material_name || 'Material');
                $('#estimateMaterialMeta').text(
                    (materialUnit === 'm²' ?
                        formatNumberResult(data.material_product_quantity) + ' pcs · ' :
                        '') +
                    formatNumberResult(materialQuantity) + ' ' + materialUnit +
                    ' × ' + formatCurrencyResult(data.material_unit_price) +
                    ' / ' + materialUnit
                );
                $('#estimateMaterialAmount').text(
                    formatCurrencyResult(data.material_subtotal)
                );

                $('#estimateLaminationLabel').text(
                    data.lamination_name || 'Laminasi'
                );
                $('#estimateLaminationMeta').text(
                    formatNumberResult(data.lamination_quantity) + ' m × ' +
                    formatCurrencyResult(data.lamination_unit_price) + ' / m'
                );
                $('#estimateLaminationAmount').text(
                    formatCurrencyResult(laminationSubtotal)
                );
                $('#estimateLaminationRow').prop(
                    'hidden',
                    !data.lamination_name
                );

                const componentItems = $('#estimateComponentItems').empty();
                (data.additional_components || []).forEach(function(component) {
                    const componentRow = $('<div>', {
                        class: 'flex justify-between gap-4'
                    });
                    const componentDetails = $('<div>', {
                        class: 'min-w-0'
                    });
                    const componentLabel = $('<span>', {
                        class: 'block text-sm text-zinc-500 dark:text-zinc-400'
                    }).text(component.name);
                    const componentMeta = $('<span>', {
                        class: 'block text-xs text-zinc-400 mt-1'
                    }).text(
                        component.type + ' · ' +
                        formatNumberResult(component.qty) + ' pcs × ' +
                        formatCurrencyResult(component.unit_price) + ' / pcs'
                    );
                    const componentSubtotal = $('<span>', {
                        class: 'shrink-0 text-sm text-zinc-700 dark:text-zinc-200'
                    }).text(formatCurrencyResult(component.subtotal));

                    componentDetails.append(componentLabel, componentMeta);
                    componentRow.append(componentDetails, componentSubtotal);
                    componentItems.append(componentRow);
                });

                $('#estimateComponentsAmount').text(
                    formatCurrencyResult(componentsSubtotal)
                );
                $('#estimateComponentsRow').prop(
                    'hidden',
                    componentsSubtotal <= 0
                );

                $('#estimateDiscountAmount').text(
                    '-' + formatCurrencyResult(discountAmount)
                );
                $('#estimateDiscountRow').prop(
                    'hidden',
                    discountAmount <= 0
                );

                $('#estimateBreakdown').prop('hidden', false);
                $('#estimateGrandTotal').text(
                    formatCurrencyResult(data.grand_total)
                );
                const productTitle = @json($product['title'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
                const categoryName = categorySelect.attr('data-category-name') || '';
                const priceTypeLabel = $('#price_type option:selected').text().trim();
                const locationName = locationSelect.find('option:selected').text().trim();
                const vendorName = vendorSelect.find('option:selected').text().trim();
                const orderLines = [
                    'Halo, saya ingin melanjutkan pesanan dengan detail berikut:',
                    '',
                    'Produk: ' + productTitle,
                    'Kategori: ' + categoryName,
                    'Lokasi: ' + locationName,
                    ...(locationName.toLowerCase() === 'outsourcing' && vendorSelect.val() ?
                        ['Vendor: ' + vendorName] :
                        []),
                    'Jenis harga: ' + priceTypeLabel,
                    ...(!isDisplayCategory() ?
                        ['Ukuran: ' + $('#length').val() + ' × ' + $('#width').val() + ' cm'] :
                        []),
                    '',
                    'Rincian estimasi:',
                    '• ' + (data.material_name || 'Material') + ': ' +
                    (materialUnit === 'm²' ?
                        formatNumberResult(data.material_product_quantity) + ' pcs · ' :
                        '') +
                    formatNumberResult(materialQuantity) + ' ' + materialUnit +
                    ' × ' + formatCurrencyResult(data.material_unit_price) +
                    ' / ' + materialUnit + ' = ' +
                    formatCurrencyResult(data.material_subtotal)
                ];

                if (data.lamination_name) {
                    orderLines.push(
                        '• Laminasi ' + data.lamination_name + ': ' +
                        formatNumberResult(data.lamination_quantity) + ' m × ' +
                        formatCurrencyResult(data.lamination_unit_price) +
                        ' / m = ' + formatCurrencyResult(laminationSubtotal)
                    );
                }

                (data.additional_components || []).forEach(function(component) {
                    orderLines.push(
                        '• ' + component.name + ' (' + component.type + '): ' +
                        component.qty + ' pcs × ' +
                        formatCurrencyResult(component.unit_price) +
                        ' / pcs = ' + formatCurrencyResult(component.subtotal)
                    );
                });

                if (discountAmount > 0) {
                    orderLines.push('Diskon: -' + formatCurrencyResult(discountAmount));
                }

                orderLines.push(
                    '',
                    'Total estimasi: ' + formatCurrencyResult(data.grand_total),
                    '',
                    'Mohon info proses selanjutnya. Terima kasih.'
                );

                latestEstimationMessage = orderLines.join('\n');
                setContinueOrderAvailable(true);
                setEstimationStatus(
                    'Estimasi diperbarui otomatis berdasarkan konfigurasi saat ini.',
                    false
                );
            }

            function updateEstimationPreview() {
                const sequence = ++estimationPreviewSequence;
                const result = getEstimationPreviewPayload();

                if (!result.payload) {
                    resetEstimationPreview(result.message);
                    return;
                }

                setEstimationStatus('Memperbarui estimasi...', false);

                estimationPreviewRequest = $.ajax({
                        url: estimationPreviewUrl,
                        method: 'POST',
                        data: result.payload,
                        dataType: 'json',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    })
                    .done(function(response) {
                        if (sequence !== estimationPreviewSequence) {
                            return;
                        }

                        if (!response.success || !response.data) {
                            resetEstimationPreview(
                                getErrorMessage(
                                    response,
                                    'Gagal menghitung estimasi.'
                                ),
                                true
                            );
                            return;
                        }

                        renderEstimationPreview(response.data);
                    })
                    .fail(function(xhr, status) {
                        if (
                            status === 'abort' ||
                            sequence !== estimationPreviewSequence
                        ) {
                            return;
                        }

                        resetEstimationPreview(
                            getErrorMessage(
                                xhr.responseJSON,
                                'Gagal menghitung estimasi.'
                            ),
                            true
                        );
                    });
            }

            function scheduleEstimationPreview() {
                estimationPreviewSequence++;
                window.clearTimeout(estimationPreviewTimer);
                setContinueOrderAvailable(false);
                setEstimationStatus('Memperbarui estimasi...', false);
                estimationPreviewTimer = window.setTimeout(
                    updateEstimationPreview,
                    300
                );
            }


            /* =========================
               TOGGLE VENDOR
            ========================== */

            function toggleVendorField() {

                const locationName =
                    locationSelect
                    .find('option:selected')
                    .text()
                    .trim()
                    .toLowerCase();

                if (locationName === 'outsourcing') {

                    vendorField.show();

                } else {

                    vendorSelect
                        .val(null)
                        .trigger('change');

                    vendorField.hide();

                }

            }


            /* =========================
               DISPLAY FIELDS
            ========================== */

            function toggleDisplayFields() {

                const display =
                    isDisplayCategory();

                const fields = [
                    '#lengthField',
                    '#widthField',
                    '#materialSizeField'
                ];

                fields.forEach(function(selector) {

                    const field = $(selector);

                    if (!field.length) {
                        return;
                    }

                    if (display) {

                        field.hide();

                        field
                            .find('input, select')
                            .prop('required', false);

                        field
                            .find('input, select')
                            .val('');

                    } else {

                        field.show();

                        if (selector === '#lengthField' ||
                            selector === '#widthField' ||
                            selector === '#materialSizeField') {

                            field
                                .find('input, select')
                                .prop('required', true);

                        }

                    }

                });

            }


            /* =========================
               LOAD MATERIALS
            ========================== */

            function loadMaterials() {

                const locationId =
                    locationSelect.val();

                const categoryId =
                    categorySelect.val();

                const vendorId =
                    vendorSelect.val();

                if (!locationId || !categoryId) {

                    materialSelect
                        .empty()
                        .append('<option value="">Pilih material...</option>')
                        .trigger('change');

                    return;

                }


                const locationName =
                    locationSelect
                    .find('option:selected')
                    .text()
                    .trim()
                    .toLowerCase();

                const isOutsourcing =
                    locationName === 'outsourcing';


                if (isOutsourcing && !vendorId) {

                    materialSelect
                        .empty()
                        .append('<option value="">Pilih material...</option>')
                        .trigger('change');

                    return;

                }


                resetDisplayIdentity();


                materialSelect
                    .empty()
                    .append('<option value="">Memuat material...</option>')
                    .trigger('change');


                const params = {
                    location_id: locationId,
                    category_id: categoryId,
                    engine: engineName
                };


                if (isOutsourcing) {
                    params.vendor_id = vendorId;
                }


                $.getJSON(materialsUrl, params)
                    .done(function(result) {

                        materialSelect
                            .empty()
                            .append('<option value="">Pilih material...</option>');


                        if (!result.success) {

                            showToast(
                                getErrorMessage(
                                    result,
                                    'Gagal memuat material.'
                                ),
                                'error'
                            );

                            materialSelect.trigger('change');

                            return;
                        }


                        result.data.forEach(function(material) {

                            const option =
                                $('<option>', {
                                    value: material.id,
                                    text: material.material_name
                                });


                            if (isDisplayCategory()) {

                                option.attr(
                                    'data-configuration-id',
                                    material.configuration_id || ''
                                );

                                option.attr(
                                    'data-display-product-id',
                                    material.display_product_id || ''
                                );

                                option.attr(
                                    'data-component-id',
                                    material.component_id || ''
                                );

                                option.attr(
                                    'data-component-type',
                                    material.component_type || ''
                                );

                            }


                            materialSelect.append(option);

                        });


                        materialSelect.trigger('change');

                    })
                    .fail(function() {

                        materialSelect
                            .empty()
                            .append('<option value="">Pilih material...</option>')
                            .trigger('change');

                        showToast(
                            'Gagal memuat material.',
                            'error'
                        );

                    });

            }


            /* =========================
               LOAD MATERIAL SIZES
            ========================== */

            function loadMaterialSizes() {

                const locationId =
                    locationSelect.val();

                const categoryId =
                    categorySelect.val();

                const materialId =
                    materialSelect.val();

                const vendorId =
                    vendorSelect.val();


                materialSizeSelect
                    .empty()
                    .append('<option value="">Pilih ukuran material...</option>')
                    .trigger('change');


                materialCustomWidthInput
                    .val('')
                    .hide()
                    .prop('disabled', true)
                    .prop('required', false);


                if (
                    !locationId ||
                    !categoryId ||
                    !materialId
                ) {
                    return;
                }


                const locationName =
                    locationSelect
                    .find('option:selected')
                    .text()
                    .trim()
                    .toLowerCase();

                const isOutsourcing =
                    locationName === 'outsourcing';


                if (isOutsourcing && !vendorId) {
                    return;
                }


                const params = {
                    location_id: locationId,
                    category_id: categoryId,
                    material_id: materialId,
                    engine: engineName
                };


                if (isOutsourcing) {
                    params.vendor_id = vendorId;
                }


                $.getJSON(materialSizesUrl, params)
                    .done(function(result) {

                        if (!result.success) {

                            showToast(
                                getErrorMessage(
                                    result,
                                    'Gagal memuat ukuran material.'
                                ),
                                'error'
                            );

                            return;
                        }


                        result.data.forEach(function(size) {

                            materialSizeSelect.append(`
                        <option value="${size.id}">
                            ${formatNumberResult(size.width)} cm
                        </option>
                    `);

                        });


                        materialSizeSelect.append(`
                    <option value="__custom__">
                        + Custom / Input Manual
                    </option>
                `);


                        materialSizeSelect.trigger('change');

                    })
                    .fail(function() {

                        showToast(
                            'Gagal memuat ukuran material.',
                            'error'
                        );

                    });

            }


            /* =========================
               LOAD LAMINATIONS
            ========================== */

            function loadLaminations() {

                const locationId =
                    locationSelect.val();

                const categoryId =
                    categorySelect.val();


                laminationSelect
                    .empty()
                    .append(`
                <option value="none">
                    Tanpa Laminasi
                </option>
            `);

                laminationSizeSelect
                    .empty()
                    .append(`
                <option value="">
                    Pilih ukuran laminasi...
                </option>
            `)
                    .trigger('change');


                laminationCustomWidthInput
                    .val('')
                    .hide()
                    .prop('disabled', true)
                    .prop('required', false);


                if (!locationId || !categoryId) {
                    return;
                }


                const params = {
                    location_id: locationId,
                    category_id: categoryId,
                    engine: engineName
                };


                $.getJSON(laminationsUrl, params)
                    .done(function(result) {

                        if (!result.success) {

                            showToast(
                                getErrorMessage(
                                    result,
                                    'Gagal memuat laminasi.'
                                ),
                                'error'
                            );

                            return;
                        }


                        result.data.forEach(function(lamination) {

                            laminationSelect.append(`
                        <option value="${lamination.id}">
                            ${lamination.name}
                        </option>
                    `);

                        });


                        laminationSelect
                            .val('none')
                            .trigger('change');

                    })
                    .fail(function() {

                        showToast(
                            'Gagal memuat laminasi.',
                            'error'
                        );

                    });

            }


            /* =========================
               LOAD LAMINATION SIZES
            ========================== */

            function loadLaminationSizes() {

                const locationId =
                    locationSelect.val();

                const categoryId =
                    categorySelect.val();

                const laminationId =
                    laminationSelect.val();


                laminationSizeSelect
                    .empty()
                    .append(`
                <option value="">
                    Pilih ukuran laminasi...
                </option>
            `)
                    .trigger('change');


                laminationCustomWidthInput
                    .val('')
                    .hide()
                    .prop('disabled', true)
                    .prop('required', false);


                if (
                    !locationId ||
                    !categoryId ||
                    !laminationId ||
                    laminationId === 'none'
                ) {
                    return;
                }


                const params = {
                    location_id: locationId,
                    category_id: categoryId,
                    lamination_id: laminationId,
                    engine: engineName
                };


                $.getJSON(laminationSizesUrl, params)
                    .done(function(result) {

                        if (!result.success) {

                            showToast(
                                getErrorMessage(
                                    result,
                                    'Gagal memuat ukuran laminasi.'
                                ),
                                'error'
                            );

                            return;
                        }


                        result.data.forEach(function(size) {

                            let label =
                                formatNumberResult(size.width) +
                                ' cm';


                            if (size.length !== null &&
                                size.length !== undefined &&
                                size.length !== '') {

                                label +=
                                    ' × ' +
                                    formatNumberResult(size.length) +
                                    ' cm';

                            }


                            laminationSizeSelect.append(`
                        <option value="${size.id}">
                            ${label}
                        </option>
                    `);

                        });


                        laminationSizeSelect.append(`
                    <option value="__custom__">
                        + Custom / Input Manual
                    </option>
                `);


                        laminationSizeSelect.trigger('change');

                    })
                    .fail(function() {

                        showToast(
                            'Gagal memuat ukuran laminasi.',
                            'error'
                        );

                    });

            }


            /* =========================
               NUMBER FORMAT
            ========================== */

            function formatNumberResult(value) {

                return new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 4
                }).format(value);

            }

            /* =========================================================
             * KOMPONEN TAMBAHAN
             * ========================================================= */

            const addAdditionalComponentButton =
                document.getElementById('addAdditionalComponentButton');

            const additionalComponentsContainer =
                document.getElementById('additionalComponentsContainer');

            let additionalComponentIndex = 0;

            function formatRupiahResult(value) {
                const number = Number(value) || 0;

                return 'Rp ' + new Intl.NumberFormat('id-ID', {
                    maximumFractionDigits: 0
                }).format(number);
            }

            function calculateAdditionalComponentPrice(component) {
                const priceType = component.querySelector(
                    '.additional-component-price-type'
                ).value;

                const calculatedPriceInput = component.querySelector(
                    '.additional-component-calculated-price'
                );

                if (priceType !== 'hpp_margin') {
                    calculatedPriceInput.value = 'Rp 0';
                    return;
                }

                const hpp = Number(
                    String(
                        component.querySelector('.additional-component-hpp').value || ''
                    ).replace(/\./g, '')
                ) || 0;

                const margin = Number(
                    component.querySelector('.additional-component-margin').value
                ) || 0;

                if (hpp <= 0 || margin <= 0) {
                    calculatedPriceInput.value = 'Rp 0';
                    return;
                }

                const marginDecimal = margin / 100;
                const divisor = 1 - marginDecimal;

                if (divisor <= 0) {
                    calculatedPriceInput.value = 'Rp 0';
                    return;
                }

                const sellingPrice = hpp / divisor;

                calculatedPriceInput.value = formatRupiahResult(sellingPrice);
            }

            function updateAdditionalComponentNumbers() {
                const components = additionalComponentsContainer.querySelectorAll(
                    '.additional-component-item'
                );

                components.forEach((component, index) => {
                    const number = component.querySelector(
                        '.additional-component-number'
                    );

                    if (number) {
                        number.textContent = index + 1;
                    }
                });
            }

            function initializeAdditionalComponent(component) {
                const priceTypeSelect = component.querySelector(
                    '.additional-component-price-type'
                );

                const unitPriceWrapper = component.querySelector(
                    '.additional-component-unit-price-wrapper'
                );

                const hppMarginWrapper = component.querySelector(
                    '.additional-component-hpp-margin-wrapper'
                );

                const unitPriceInput = component.querySelector(
                    '.additional-component-unit-price'
                );

                const hppInput = component.querySelector(
                    '.additional-component-hpp'
                );

                const marginInput = component.querySelector(
                    '.additional-component-margin'
                );

                function formatCurrencyInput(input) {
                    const rawValue = input.value.replace(/\D/g, '');

                    input.value = rawValue ?
                        new Intl.NumberFormat('id-ID', {
                            maximumFractionDigits: 0
                        }).format(Number(rawValue)) :
                        '';
                }

                unitPriceInput.addEventListener('input', function() {
                    formatCurrencyInput(this);
                });

                hppInput.addEventListener('input', function() {
                    formatCurrencyInput(this);
                    calculateAdditionalComponentPrice(component);
                });

                marginInput.addEventListener('input', function() {
                    calculateAdditionalComponentPrice(component);
                });

                $(priceTypeSelect).on('change', function() {
                    if (this.value === 'hpp_margin') {
                        unitPriceWrapper.style.display = 'none';
                        hppMarginWrapper.style.display = 'block';

                        calculateAdditionalComponentPrice(component);
                    } else {
                        unitPriceWrapper.style.display = 'block';
                        hppMarginWrapper.style.display = 'none';

                        component.querySelector(
                            '.additional-component-calculated-price'
                        ).value = 'Rp 0';
                    }
                });

                $(component).find('.additional-component-price-type').select2({
                    width: '100%',
                    placeholder: 'Pilih...'
                });

                $(component).find('.additional-component-type').select2({
                    width: '100%',
                    placeholder: 'Pilih...'
                });

                component.querySelector(
                    '.btn-remove-additional-component'
                ).addEventListener('click', function() {
                    $(component)
                        .find('.additional-component-price-type')
                        .select2('destroy');

                    $(component)
                        .find('.additional-component-type')
                        .select2('destroy');

                    component.remove();

                    updateAdditionalComponentNumbers();
                });
            }

            function addAdditionalComponent() {
                const index = additionalComponentIndex++;

                const componentHtml = `
                    <div class="additional-component-item border border-zinc-200 dark:border-zinc-700 rounded-xl p-4 mb-4"
                        data-component-index="${index}">

                        <div class="flex items-center justify-between gap-3 mb-4">
                            <div class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">
                                Komponen Tambahan #
                                <span class="additional-component-number">${index + 1}</span>
                            </div>

                            <button type="button"
                                    class="btn-remove-additional-component"
                                    title="Hapus komponen">
                                <i class="bi bi-trash3-fill"></i>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tipe
                                </label>

                                <select id="additional_component_type_${index}"
                                        name="additional_components[${index}][type]"
                                        class="additional-component-type w-full">
                                    <option value=""></option>
                                    <option value="Material">Material</option>
                                    <option value="Finishing">Finishing</option>
                                    <option value="Jasa Pemasangan">
                                        Jasa Pemasangan
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nama Komponen
                                </label>

                                <input type="text"
                                    id="additional_component_name_${index}"
                                    name="additional_components[${index}][name]"
                                    class="additional-component-name w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 pr-12"
                                    placeholder="Contoh: Mata Ayam"
                                    autocomplete="off">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Qty
                                </label>

                                <input type="number"
                                    id="additional_component_qty_${index}"
                                    name="additional_components[${index}][qty]"
                                    class="additional-component-qty w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3 pr-12"
                                    min="1"
                                    step="1"
                                    value="1">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Tipe Harga
                                </label>

                                <select id="additional_component_price_type_${index}"
                                        name="additional_components[${index}][price_type]"
                                        class="additional-component-price-type w-full">
                                    <option value="unit_price">
                                        Harga Satuan (Rp)
                                    </option>
                                    <option value="hpp_margin">
                                        HPP + Margin %
                                    </option>
                                </select>
                            </div>

                        </div>

                        <div class="additional-component-unit-price-wrapper mt-4">
                            <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                Harga Satuan
                            </label>

                            <input type="text"
                                id="additional_component_unit_price_${index}"
                                name="additional_components[${index}][unit_price]"
                                class="additional-component-unit-price w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3"
                                inputmode="numeric"
                                placeholder="Contoh: 200000">

                            <small class="block text-xs text-zinc-400 mt-1.5">
                                Harga jual per unit komponen.
                            </small>
                        </div>

                        <div class="additional-component-hpp-margin-wrapper mt-4"
                            style="display:none;">

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                                <div>
                                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                        HPP / Unit
                                    </label>

                                    <input type="text"
                                        id="additional_component_hpp_${index}"
                                        name="additional_components[${index}][hpp]"
                                        class="additional-component-hpp w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3"
                                        inputmode="numeric"
                                        placeholder="Contoh: 100000">

                                    <small class="block text-xs text-zinc-400 mt-1.5">
                                        HPP per unit komponen.
                                    </small>
                                </div>

                                <div>
                                    <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                        Margin
                                    </label>

                                    <div class="flex items-center gap-2">
                                        <input type="number"
                                            id="additional_component_margin_${index}"
                                            name="additional_components[${index}][margin]"
                                            class="additional-component-margin w-full bg-zinc-850 border border-zinc-700 text-white text-sm rounded-xl px-4 py-3"
                                            min="0"
                                            max="99.99"
                                            step="0.01"
                                            placeholder="Contoh: 50">

                                        <span class="text-sm text-zinc-500 dark:text-zinc-400">%</span>
                                    </div>

                                    <small class="block text-xs text-zinc-400 mt-1.5">
                                        Margin dihitung berdasarkan harga jual.
                                    </small>
                                </div>

                            </div>

                            <div class="mt-4">
                                <label class="block text-xs font-medium text-zinc-500 dark:text-zinc-400 mb-1.5">
                                    Harga Jual / Unit
                                </label>

                                <input type="text"
                                    class="additional-component-calculated-price w-full bg-zinc-900 border border-zinc-700 text-zinc-300 text-sm rounded-xl px-4 py-3"
                                    value="Rp 0"
                                    readonly>

                                <small class="block text-xs text-zinc-400 mt-1.5">
                                    Otomatis dihitung dari HPP dan margin.
                                </small>
                            </div>

                        </div>
                    </div>
                `;

                additionalComponentsContainer.insertAdjacentHTML(
                    'beforeend',
                    componentHtml
                );

                const component =
                    additionalComponentsContainer.lastElementChild;

                initializeAdditionalComponent(component);

                updateAdditionalComponentNumbers();
            }

            if (
                addAdditionalComponentButton &&
                additionalComponentsContainer
            ) {
                addAdditionalComponentButton.addEventListener(
                    'click',
                    function() {
                        addAdditionalComponent();
                    }
                );

                // Komponen pertama langsung dibuat seperti Admin
                addAdditionalComponent();
            }

            /* =========================================================
             * DISKON
             * ========================================================= */

            const discountTypeInputs =
                document.querySelectorAll('input[name="discount_type"]');

            const discountValueField =
                document.getElementById('discountValueField');

            const discountValueInput =
                document.getElementById('discount_value');

            const discountValueLabel =
                document.getElementById('discountValueLabel');

            const discountSuffix =
                document.getElementById('discountSuffix');

            discountTypeInputs.forEach(function(input) {
                input.addEventListener('change', function() {
                    const type = this.value;

                    if (type === 'none') {
                        discountValueField.style.display = 'none';

                        discountValueInput.disabled = true;
                        discountValueInput.value = '';

                        discountSuffix.textContent = '';

                        return;
                    }

                    discountValueField.style.display = 'block';
                    discountValueInput.disabled = false;

                    if (type === 'percentage') {
                        discountValueLabel.textContent = 'Persentase Diskon';

                        discountValueInput.placeholder = 'Contoh: 10';

                        discountSuffix.textContent = '%';
                    }

                    if (type === 'nominal') {
                        discountValueLabel.textContent = 'Nominal Diskon';

                        discountValueInput.placeholder = 'Contoh: 25.000';

                        discountSuffix.textContent = 'Rp';
                    }
                });
            });

            if (discountValueInput) {
                discountValueInput.addEventListener('input', function() {
                    const selectedType =
                        document.querySelector(
                            'input[name="discount_type"]:checked'
                        )?.value;

                    if (selectedType === 'percentage') {
                        let value = this.value.replace(/[^0-9.]/g, '');

                        const parts = value.split('.');

                        if (parts.length > 2) {
                            value =
                                parts[0] +
                                '.' +
                                parts.slice(1).join('');
                        }

                        this.value = value;
                    }

                    if (selectedType === 'nominal') {
                        const rawValue =
                            this.value.replace(/\D/g, '');

                        this.value = rawValue ?
                            new Intl.NumberFormat('id-ID', {
                                maximumFractionDigits: 0
                            }).format(Number(rawValue)) :
                            '';
                    }
                });
            }

            /* =========================
               EVENT
            ========================== */

            locationSelect.on('change', function() {

                toggleVendorField();

                loadMaterials();

                loadLaminations();

            });


            categorySelect.on('change', function() {

                resetDisplayIdentity();

                toggleDisplayFields();

                loadMaterials();

                loadLaminations();

            });


            vendorSelect.on('change', function() {

                loadMaterials();

            });

            continueOrderButton.addEventListener('click', function() {
                if (this.disabled || !latestEstimationMessage) {
                    return;
                }

                const whatsappUrl =
                    'https://wa.me/' + whatsappPhoneNumber +
                    '?text=' + encodeURIComponent(latestEstimationMessage);

                window.open(whatsappUrl, '_blank', 'noopener,noreferrer');
            });


            materialSelect.on('change', function() {

                if (isDisplayCategory()) {

                    const selectedOption =
                        this.options[this.selectedIndex];


                    displayConfigurationInput.val(
                        selectedOption?.dataset.configurationId || ''
                    );

                    displayProductInput.val(
                        selectedOption?.dataset.displayProductId || ''
                    );

                    displayComponentInput.val(
                        selectedOption?.dataset.componentId || ''
                    );

                    displayComponentTypeInput.val(
                        selectedOption?.dataset.componentType || ''
                    );


                    materialSizeSelect
                        .empty()
                        .append('<option value=""></option>')
                        .val(null)
                        .trigger('change');


                    return;

                }


                resetDisplayIdentity();

                loadMaterialSizes();

            });


            laminationSelect.on('change', function() {

                loadLaminationSizes();

            });


            materialSizeSelect.on('change', function() {

                const selectedValue =
                    this.value;


                if (
                    selectedValue &&
                    selectedValue !== '__custom__'
                ) {

                    materialPriceSizeInput.val(
                        selectedValue
                    );

                }


                toggleCustomWidth('material');

            });


            laminationSizeSelect.on('change', function() {

                const selectedValue =
                    this.value;


                if (
                    selectedValue &&
                    selectedValue !== '__custom__'
                ) {

                    laminationPriceSizeInput.val(
                        selectedValue
                    );

                }


                toggleCustomWidth('lamination');

            });

            $(document).on(
                'input change',
                '#location_id, #vendor_id, #price_type, #material_id, #length, #width, #qty, #material_size_id, #lamination_id, #lamination_size_id, #material_custom_width, #lamination_custom_width, #discount_value, input[name="discount_type"], .additional-component-type, .additional-component-name, .additional-component-qty, .additional-component-price-type, .additional-component-unit-price, .additional-component-hpp, .additional-component-margin',
                scheduleEstimationPreview
            );


            /* =========================
               INITIALIZE
            ========================== */

            toggleVendorField();

            toggleDisplayFields();

        });

        $(document).on('wheel', 'input[type="number"]', function() {
            this.blur();
        });
    </script>
@endsection

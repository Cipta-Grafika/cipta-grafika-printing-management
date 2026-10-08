@extends('user_master')

@section('contents')

    <div x-data="products()">
        <!-- ═══ CASE STUDY HEADER ═══ -->
        <article>
    
            <header class="pt-32 pb-10 max-w-4xl mx-auto px-6">
    
                <!-- =========================
                    BACK LINK
                ========================== -->
                <a
                    href="{{ route('user_products') }}"
                    class="inline-flex items-center gap-2
                           text-sm
                           text-zinc-500 dark:text-zinc-400
                           hover:text-accent
                           transition-colors
                           mb-8">
    
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-4 h-4"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        aria-hidden="true">
    
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M7 16l-4-4m0 0l4-4m-4 4h18"/>
    
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
                    <div
                        x-data="{
                            activeImage: '{{ asset('images/example-8.jpg') }}',
    
                            images: [
                                '{{ asset('images/example-8.jpg') }}',
                                '{{ asset('images/example-9.jpg') }}',
                                '{{ asset('images/example-10.jpg') }}',
                                '{{ asset('images/example-11.jpg') }}',
                                '{{ asset('images/example-12.jpg') }}'
                            ]
                        }"
    
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
    
                            <img
                                :src="activeImage"
                                alt="Contoh Kartu Nama"
                                loading="lazy"
    
                                class="w-full
                                       h-full
                                       object-cover
                                       transition-all
                                       duration-300">
    
                        </div>
    
    
                        <!-- =========================
                            THUMBNAIL GALLERY
                        ========================== -->
                        <div
                            class="thumbnail-scroll
                                   flex
                                   gap-2
                                   sm:gap-3
    
                                   w-full
                                   min-w-0
    
                                   overflow-x-auto
                                   overflow-y-hidden
    
                                   pb-2">
    
    
                            <template
                                x-for="(image, index) in images"
                                :key="index">
    
    
                                <button
                                    type="button"
    
                                    @click="activeImage = image"
    
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
    
                                    :class="activeImage === image
                                        ? 'border-accent dark:border-white scale-[1.02]'
                                        : 'border-transparent hover:border-zinc-300 dark:hover:border-zinc-600 opacity-70 hover:opacity-100'">
    
    
                                    <!-- THUMBNAIL IMAGE -->
                                    <img
                                        :src="image"
    
                                        :alt="'Contoh Kartu Nama ' + (index + 1)"
    
                                        class="w-full
                                               h-full
                                               object-cover">
    
    
                                    <!-- ACTIVE OVERLAY -->
                                    <div
                                        x-show="activeImage === image"
    
                                        x-transition.opacity
    
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
                        <p
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
    
                            Media Promosi
    
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
    
                            Kartu Nama
    
                        </h1>
    
    
                        <!-- DESCRIPTION -->
                        <p
                            class="reveal d1
                                   text-xl
    
                                   text-zinc-500
                                   dark:text-zinc-400
    
                                   leading-relaxed
                                   max-w-2xl"
    
                            style="font-size: 16px;">
    
                            Kartu nama premium isi 100 pcs lengkap dengan box plastik.
                            Pilih bahan, sisi cetak, dan finishing sesuai karakter bisnis Anda.
    
                        </p>
    
    
                        <!-- =========================
                            SHARE PRODUCT
                        ========================== -->
                        <div
                            x-data="shareProduct()"
                            class="reveal d2 mt-6" style="margin-bottom: 15px;">
    
    
                            <!-- SHARE BUTTON -->
                            <button
                                type="button"
    
                                @click="open = true"
    
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
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2">
    
                                    <circle
                                        cx="18"
                                        cy="5"
                                        r="3">
    
                                    </circle>
    
                                    <circle
                                        cx="6"
                                        cy="12"
                                        r="3">
    
                                    </circle>
    
                                    <circle
                                        cx="18"
                                        cy="19"
                                        r="3">
    
                                    </circle>
    
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M8.59 13.51l6.83 3.98M15.41 6.51L8.59 10.49">
    
                                    </path>
    
                                </svg>
    
    
                                <span>
    
                                    Bagikan Produk
    
                                </span>
    
    
                            </button>
    
    
                            <!-- =========================
                                SHARE MODAL
                            ========================== -->
                            <div
                                x-show="open"
    
                                x-transition.opacity
    
                                @keydown.escape.window="open = false"
    
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
                                <div
                                    @click.outside="open = false"
    
                                    x-show="open"
    
                                    x-transition:
                                        enter="transition ease-out duration-200"
                                        enter-start="opacity-0 scale-95"
                                        enter-end="opacity-100 scale-100"
                                        leave="transition ease-in duration-150"
                                        leave-start="opacity-100 scale-100"
                                        leave-end="opacity-0 scale-95"
    
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
                                        <button
                                            type="button"
    
                                            @click="open = false"
    
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
    
    
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="w-5 h-5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2">
    
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M6 18L18 6M6 6l12 12">
    
                                                </path>
    
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
                                        <button
                                            type="button"
    
                                            @click="share('whatsapp')"
    
                                            class="share-option">
    
                                            <span
                                                class="w-11 h-11
    
                                                       rounded-full
    
                                                       flex
                                                       items-center
                                                       justify-center
    
                                                       bg-green-500
                                                       text-white
    
                                                       font-bold">
    
                                                WA
    
                                            </span>
    
    
                                            <span>
    
                                                WhatsApp
    
                                            </span>
    
                                        </button>
    
    
    
                                        <!-- FACEBOOK -->
                                        <button
                                            type="button"
    
                                            @click="share('facebook')"
    
                                            class="share-option">
    
                                            <span
                                                class="w-11 h-11
    
                                                       rounded-full
    
                                                       flex
                                                       items-center
                                                       justify-center
    
                                                       bg-blue-600
                                                       text-white
    
                                                       text-xl
                                                       font-bold">
    
                                                f
    
                                            </span>
    
    
                                            <span>
    
                                                Facebook
    
                                            </span>
    
                                        </button>
    
    
    
                                        <!-- X -->
                                        <button
                                            type="button"
    
                                            @click="share('x')"
    
                                            class="share-option">
    
                                            <span
                                                class="w-11 h-11
    
                                                       rounded-full
    
                                                       flex
                                                       items-center
                                                       justify-center
    
                                                       bg-zinc-900
                                                       dark:bg-white
    
                                                       text-white
                                                       dark:text-zinc-900
    
                                                       font-bold
                                                       text-lg">
    
                                                X
    
                                            </span>
    
    
                                            <span>
    
                                                X
    
                                            </span>
    
                                        </button>
    
    
    
                                        <!-- TELEGRAM -->
                                        <button
                                            type="button"
    
                                            @click="share('telegram')"
    
                                            class="share-option">
    
                                            <span
                                                class="w-11 h-11
    
                                                       rounded-full
    
                                                       flex
                                                       items-center
                                                       justify-center
    
                                                       bg-sky-500
                                                       text-white
    
                                                       text-xl">
    
                                                ✈
    
                                            </span>
    
    
                                            <span>
    
                                                Telegram
    
                                            </span>
    
                                        </button>
    
    
    
                                        <!-- LINE -->
                                        <button
                                            type="button"
    
                                            @click="share('line')"
    
                                            class="share-option">
    
                                            <span
                                                class="w-11 h-11
    
                                                       rounded-full
    
                                                       flex
                                                       items-center
                                                       justify-center
    
                                                       bg-green-600
                                                       text-white
    
                                                       font-bold
                                                       text-xl">
    
                                                L
    
                                            </span>
    
    
                                            <span>
    
                                                LINE
    
                                            </span>
    
                                        </button>
    
    
    
                                        <!-- PINTEREST -->
                                        <button
                                            type="button"
    
                                            @click="share('pinterest')"
    
                                            class="share-option">
    
                                            <span
                                                class="w-11 h-11
    
                                                       rounded-full
    
                                                       flex
                                                       items-center
                                                       justify-center
    
                                                       bg-red-600
                                                       text-white
    
                                                       font-bold
                                                       text-lg">
    
                                                P
    
                                            </span>
    
    
                                            <span>
    
                                                Pinterest
    
                                            </span>
    
                                        </button>
    
    
    
                                        <!-- EMAIL -->
                                        <button
                                            type="button"
    
                                            @click="share('email')"
    
                                            class="share-option">
    
                                            <span
                                                class="w-11 h-11
    
                                                       rounded-full
    
                                                       flex
                                                       items-center
                                                       justify-center
    
                                                       bg-red-500
                                                       text-white
    
                                                       font-bold">
    
                                                @
    
                                            </span>
    
    
                                            <span>
    
                                                Email
    
                                            </span>
    
                                        </button>
    
    
    
                                        <!-- COPY LINK -->
                                        <button
                                            type="button"
    
                                            @click="copyLink()"
    
                                            class="share-option">
    
                                            <span
                                                class="w-11 h-11
    
                                                       rounded-full
    
                                                       flex
                                                       items-center
                                                       justify-center
    
                                                       bg-zinc-600
                                                       dark:bg-zinc-700
    
                                                       text-white">
    
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="w-5 h-5"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                    stroke-width="2">
    
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M13.828 10.172a4 4 0 015.656 5.656l-3 3a4 4 0 01-5.656 0m-1.656-5.172a4 4 0 00-5.656-5.656l-3 3a4 4 0 000 5.656">
    
                                                    </path>
    
                                                </svg>
    
                                            </span>
    
    
                                            <span
                                                x-text="copied ? 'Link tersalin!' : 'Salin link'">
    
                                            </span>
    
                                        </button>
    
    
                                    </div>
    
    
    
                                    <!-- =========================
                                        OTHER APPS
                                    ========================== -->
                                    <button
                                        type="button"
    
                                        @click="shareNative()"
    
                                        class="share-option
                                               w-full
                                               mt-3">
    
    
                                        <span
                                            class="w-11 h-11
    
                                                   rounded-full
    
                                                   flex
                                                   items-center
                                                   justify-center
    
                                                   bg-zinc-200
                                                   dark:bg-white/10
    
                                                   text-zinc-700
                                                   dark:text-white">
    
    
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="w-5 h-5"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                stroke-width="2">
    
                                                <circle cx="5" cy="12" r="1.5"></circle>
    
                                                <circle cx="12" cy="12" r="1.5"></circle>
    
                                                <circle cx="19" cy="12" r="1.5"></circle>
    
                                            </svg>
    
                                        </span>
    
    
                                        <span>
    
                                            Aplikasi lainnya
    
                                        </span>
    
    
                                    </button>
    
    
                                    <!-- COPY MESSAGE -->
                                    <p
                                        x-show="copied"
    
                                        x-transition
    
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
                            max-w-2xl" style="font-size: 16px;">Pre-order. Estimasi proses 2 hari kerja. Produk diperkirakan tersedia mulai 01 September 2026.</p>
                    </div>
                </div>
    
                <div
    
                    x-data="{
                        /* =========================
                        STATIC MASTER DATA
                        ========================== */
    
                        sizes: [
                            {
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
    
    
                        materials: [
                            {
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
    
    
                        finishings: [
                            {
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
    
    
                        /* =========================
                        FORM VALUE
                        ========================== */
    
                        size: 'a4',
    
                        material: 'art-paper-120',
    
                        printSide: '1',
    
                        colorMode: 'full-color',
    
                        selectedFinishings: [],
    
                        quantity: 100,
    
    
                        /* =========================
                        GET SELECTED SIZE
                        ========================== */
    
                        get selectedSize() {
    
                            return this.sizes.find(
                                item => item.id === this.size
                            );
    
                        },
    
    
                        /* =========================
                        GET SELECTED MATERIAL
                        ========================== */
    
                        get selectedMaterial() {
    
                            return this.materials.find(
                                item => item.id === this.material
                            );
    
                        },
    
    
                        /* =========================
                        MATERIAL COST
                        ========================== */
    
                        get materialCost() {
    
                            if (!this.selectedMaterial) return 0;
    
                            return (
                                this.selectedMaterial.price *
                                this.selectedSize.multiplier
                            );
    
                        },
    
    
                        /* =========================
                        PRINT COST
                        ========================== */
    
                        get printCost() {
    
                            let cost = 0;
    
    
                            if (this.colorMode === 'full-color') {
    
                                cost = 1500;
    
                            } else {
    
                                cost = 500;
    
                            }
    
    
                            if (this.printSide === '2') {
    
                                cost *= 2;
    
                            }
    
    
                            return cost *
                                this.selectedSize.multiplier;
    
                        },
    
    
                        /* =========================
                        FINISHING COST
                        ========================== */
    
                        get finishingCost() {
    
                            return this.selectedFinishings.reduce(
                                (total, finishingId) => {
    
                                    const finishing =
                                        this.finishings.find(
                                            item => item.id === finishingId
                                        );
    
    
                                    return total +
                                        (finishing ? finishing.price : 0);
    
                                },
                                0
                            );
    
                        },
    
    
                        /* =========================
                        COST PER PCS
                        ========================== */
    
                        get costPerPcs() {
    
                            return (
                                this.materialCost +
                                this.printCost +
                                this.finishingCost
                            );
    
                        },
    
    
                        /* =========================
                        TOTAL COST
                        ========================== */
    
                        get totalPrice() {
    
                            return this.costPerPcs *
                                Math.max(
                                    Number(this.quantity) || 0,
                                    1
                                );
    
                        },
    
    
                        /* =========================
                        FORMAT RUPIAH
                        ========================== */
    
                        formatCurrency(value) {
    
                            return new Intl.NumberFormat(
                                'id-ID',
                                {
                                    style: 'currency',
                                    currency: 'IDR',
                                    minimumFractionDigits: 0
                                }
                            ).format(value);
    
                        },
    
    
                        /* =========================
                        QUANTITY
                        ========================== */
    
                        increaseQuantity() {
    
                            this.quantity =
                                Number(this.quantity) + 50;
    
                        },
    
    
                        decreaseQuantity() {
    
                            if (this.quantity > 50) {
    
                                this.quantity =
                                    Number(this.quantity) - 50;
    
                            }
    
                        }
    
                    }"
    
    
                    class="reveal d2
    
                        grid
                        grid-cols-1
                        gap-4
    
                        py-8
    
                        border-t
                        border-b
    
                        border-zinc-100
                        dark:border-zinc-900"
    
                    style="margin-top: 20px;">
    
    
                    <!-- =========================
                        HEADER
                    ========================== -->
    
                    <div>
    
                        <p
                            class="font-medium
                                text-zinc-900
                                dark:text-white
                                text-lg">
    
                            A3+ Estimator
    
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
    
                        <div
                            class="lg:col-span-2
    
                                grid
                                grid-cols-1
                                sm:grid-cols-2
                                gap-5">
    
    
                            <!-- =========================
                                UKURAN
                            ========================== -->
    
                            <div>
    
                                <label
                                    class="block
                                        text-sm
                                        font-medium
                                        text-zinc-700
                                        dark:text-zinc-300
                                        mb-2">
    
                                    Ukuran
    
                                </label>
    
    
                                <select
    
                                    x-model="size"
    
                                    class="w-full
                                        px-4
                                        py-3
    
                                        rounded-xl
    
                                        bg-white
                                        dark:bg-zinc-900
    
                                        border
                                        border-zinc-200
                                        dark:border-zinc-700
    
                                        text-sm
                                        text-zinc-700
                                        dark:text-white
    
                                        outline-none
    
                                        focus:border-accent
                                        dark:focus:border-white
    
                                        transition-colors">
    
                                    <template
                                        x-for="item in sizes"
                                        :key="item.id">
    
                                        <option
                                            :value="item.id"
                                            x-text="item.name">
    
                                        </option>
    
                                    </template>
    
                                </select>
    
                            </div>
    
    
    
                            <!-- =========================
                                MATERIAL
                            ========================== -->
    
                            <div>
    
                                <label
                                    class="block
                                        text-sm
                                        font-medium
                                        text-zinc-700
                                        dark:text-zinc-300
                                        mb-2">
    
                                    Material
    
                                </label>
    
    
                                <select
    
                                    x-model="material"
    
                                    class="w-full
                                        px-4
                                        py-3
    
                                        rounded-xl
    
                                        bg-white
                                        dark:bg-zinc-900
    
                                        border
                                        border-zinc-200
                                        dark:border-zinc-700
    
                                        text-sm
                                        text-zinc-700
                                        dark:text-white
    
                                        outline-none
    
                                        focus:border-accent
                                        dark:focus:border-white
    
                                        transition-colors">
    
                                    <template
                                        x-for="item in materials"
                                        :key="item.id">
    
                                        <option
                                            :value="item.id"
                                            x-text="item.name">
    
                                        </option>
    
                                    </template>
    
                                </select>
    
                            </div>
    
    
    
                            <!-- =========================
                                SISI CETAK
                            ========================== -->
    
                            <div>
    
                                <label
                                    class="block
                                        text-sm
                                        font-medium
                                        text-zinc-700
                                        dark:text-zinc-300
                                        mb-3">
    
                                    Sisi Cetak
    
                                </label>
    
    
                                <div
                                    class="grid
                                        grid-cols-2
                                        gap-3">
    
    
                                    <!-- 1 SISI -->
    
                                    <button
    
                                        type="button"
    
                                        @click="printSide = '1'"
    
                                        class="px-4
                                            py-3
    
                                            rounded-xl
    
                                            border
    
                                            text-sm
    
                                            transition-all"
    
                                        :class="printSide === '1'
    
                                            ? 'border-accent bg-orange-50 text-zinc-900 dark:border-white dark:bg-white/10 dark:text-white'
    
                                            : 'border-zinc-200 text-zinc-500 hover:border-zinc-300 dark:border-zinc-700 dark:text-zinc-400 dark:hover:border-zinc-600'">
    
                                        1 Sisi
    
                                    </button>
    
    
    
                                    <!-- 2 SISI -->
    
                                    <button
    
                                        type="button"
    
                                        @click="printSide = '2'"
    
                                        class="px-4
                                            py-3
    
                                            rounded-xl
    
                                            border
    
                                            text-sm
    
                                            transition-all"
    
                                        :class="printSide === '2'
    
                                            ? 'border-accent bg-orange-50 text-zinc-900 dark:border-white dark:bg-white/10 dark:text-white'
    
                                            : 'border-zinc-200 text-zinc-500 hover:border-zinc-300 dark:border-zinc-700 dark:text-zinc-400 dark:hover:border-zinc-600'">
    
                                        2 Sisi
    
                                    </button>
    
    
                                </div>
    
                            </div>
    
    
    
                            <!-- =========================
                                WARNA CETAK
                            ========================== -->
    
                            <div>
    
                                <label
                                    class="block
                                        text-sm
                                        font-medium
                                        text-zinc-700
                                        dark:text-zinc-300
                                        mb-3">
    
                                    Warna Cetak
    
                                </label>
    
    
                                <div
                                    class="grid
                                        grid-cols-2
                                        gap-3">
    
    
                                    <!-- FULL COLOR -->
    
                                    <button
    
                                        type="button"
    
                                        @click="colorMode = 'full-color'"
    
                                        class="px-4
                                            py-3
    
                                            rounded-xl
    
                                            border
    
                                            text-sm
    
                                            transition-all"
    
                                        :class="colorMode === 'full-color'
    
                                            ? 'border-accent bg-orange-50 text-zinc-900 dark:border-white dark:bg-white/10 dark:text-white'
    
                                            : 'border-zinc-200 text-zinc-500 hover:border-zinc-300 dark:border-zinc-700 dark:text-zinc-400 dark:hover:border-zinc-600'">
    
                                        Full Color
    
                                    </button>
    
    
    
                                    <!-- BLACK WHITE -->
    
                                    <button
    
                                        type="button"
    
                                        @click="colorMode = 'black-white'"
    
                                        class="px-4
                                            py-3
    
                                            rounded-xl
    
                                            border
    
                                            text-sm
    
                                            transition-all"
    
                                        :class="colorMode === 'black-white'
    
                                            ? 'border-accent bg-orange-50 text-zinc-900 dark:border-white dark:bg-white/10 dark:text-white'
    
                                            : 'border-zinc-200 text-zinc-500 hover:border-zinc-300 dark:border-zinc-700 dark:text-zinc-400 dark:hover:border-zinc-600'">
    
                                        Black & White
    
                                    </button>
    
    
                                </div>
    
                            </div>
    
    
    
                            <!-- =========================
                                FINISHING
                            ========================== -->
    
                            <div
                                class="sm:col-span-2">
    
                                <label
                                    class="block
                                        text-sm
                                        font-medium
                                        text-zinc-700
                                        dark:text-zinc-300
                                        mb-3">
    
                                    Finishing
    
                                </label>
    
    
                                <div
                                    class="grid
                                        grid-cols-1
                                        sm:grid-cols-2
                                        gap-3">
    
    
                                    <template
                                        x-for="item in finishings"
                                        :key="item.id">
    
    
                                        <label
    
                                            class="flex
                                                items-center
                                                justify-between
    
                                                gap-3
    
                                                px-4
                                                py-3
    
                                                rounded-xl
    
                                                cursor-pointer
    
                                                border
    
                                                transition-all"
    
                                            :class="selectedFinishings.includes(item.id)
    
                                                ? 'border-accent bg-orange-50 dark:border-white dark:bg-white/10'
    
                                                : 'border-zinc-200 hover:border-zinc-300 dark:border-zinc-700 dark:hover:border-zinc-600'">
    
    
                                            <div
                                                class="flex
                                                    items-center
                                                    gap-3">
    
    
                                                <input
    
                                                    type="checkbox"
    
                                                    :value="item.id"
    
                                                    x-model="selectedFinishings"
    
                                                    class="sr-only">
    
    
                                                <!-- CUSTOM CHECK -->
    
                                                <div
    
                                                    class="w-5
                                                        h-5
    
                                                        rounded-md
    
                                                        flex
                                                        items-center
                                                        justify-center
    
                                                        border
    
                                                        transition-all"
    
                                                    :class="selectedFinishings.includes(item.id)
    
                                                        ? 'bg-accent border-accent dark:bg-white dark:border-white'
    
                                                        : 'border-zinc-300 dark:border-zinc-600'">
    
    
                                                    <svg
    
                                                        x-show="selectedFinishings.includes(item.id)"
    
                                                        xmlns="http://www.w3.org/2000/svg"
    
                                                        class="w-3
                                                            h-3
    
                                                            text-white
                                                            dark:text-zinc-900"
    
                                                        fill="none"
    
                                                        viewBox="0 0 24 24"
    
                                                        stroke="currentColor"
    
                                                        stroke-width="3">
    
                                                        <path
    
                                                            stroke-linecap="round"
    
                                                            stroke-linejoin="round"
    
                                                            d="M5 12l4 4L19 6">
    
                                                        </path>
    
                                                    </svg>
    
                                                </div>
    
    
                                                <span
    
                                                    class="text-sm
                                                        text-zinc-600
                                                        dark:text-zinc-300"
    
                                                    x-text="item.name">
    
                                                </span>
    
                                            </div>
    
    
                                            <span
    
                                                class="text-xs
                                                    text-zinc-400"
    
                                                x-text="'+' + formatCurrency(item.price)">
    
                                            </span>
    
    
                                        </label>
    
    
                                    </template>
    
                                </div>
    
                            </div>
    
    
    
                            <!-- =========================
                                QUANTITY
                            ========================== -->
    
                            <div
                                class="sm:col-span-2">
    
    
                                <label
                                    class="block
                                        text-sm
                                        font-medium
                                        text-zinc-700
                                        dark:text-zinc-300
                                        mb-3">
    
                                    Jumlah Pesanan
    
                                </label>
    
    
                                <div
                                    class="flex
                                        items-center
                                        gap-3">
    
    
                                    <!-- MINUS -->
    
                                    <button
    
                                        type="button"
    
                                        @click="decreaseQuantity()"
    
                                        class="w-12
                                            h-12
    
                                            rounded-xl
    
                                            border
                                            border-zinc-200
                                            dark:border-zinc-700
    
                                            flex
                                            items-center
                                            justify-center
    
                                            text-zinc-600
                                            dark:text-zinc-300
    
                                            hover:border-accent
                                            dark:hover:border-white
    
                                            transition-colors">
    
                                        −
    
                                    </button>
    
    
    
                                    <!-- INPUT -->
    
                                    <input
    
                                        type="number"
    
                                        min="50"
    
                                        step="50"
    
                                        x-model.number="quantity"
    
                                        class="w-full
    
                                            max-w-[180px]
    
                                            px-4
                                            py-3
    
                                            text-center
    
                                            rounded-xl
    
                                            bg-white
                                            dark:bg-zinc-900
    
                                            border
                                            border-zinc-200
                                            dark:border-zinc-700
    
                                            text-zinc-900
                                            dark:text-white
    
                                            outline-none
    
                                            focus:border-accent
                                            dark:focus:border-white">
    
    
                                    <!-- PLUS -->
    
                                    <button
    
                                        type="button"
    
                                        @click="increaseQuantity()"
    
                                        class="w-12
                                            h-12
    
                                            rounded-xl
    
                                            border
                                            border-zinc-200
                                            dark:border-zinc-700
    
                                            flex
                                            items-center
                                            justify-center
    
                                            text-zinc-600
                                            dark:text-zinc-300
    
                                            hover:border-accent
                                            dark:hover:border-white
    
                                            transition-colors">
    
                                        +
    
                                    </button>
    
    
                                    <span
                                        class="text-sm
                                            text-zinc-400">
    
                                        pcs
    
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
    
                            <div
                                class="mt-6
                                    space-y-3">
    
    
                                <div
                                    class="flex
                                        justify-between
                                        gap-4">
    
                                    <span
                                        class="text-sm
                                            text-zinc-500
                                            dark:text-zinc-400">
    
                                        Material
    
                                    </span>
    
    
                                    <span
                                        class="text-sm
                                            text-zinc-700
                                            dark:text-zinc-200"
    
                                        x-text="formatCurrency(materialCost)">
    
                                    </span>
    
                                </div>
    
    
    
                                <div
                                    class="flex
                                        justify-between
                                        gap-4">
    
                                    <span
                                        class="text-sm
                                            text-zinc-500
                                            dark:text-zinc-400">
    
                                        Cetak
    
                                    </span>
    
    
                                    <span
                                        class="text-sm
                                            text-zinc-700
                                            dark:text-zinc-200"
    
                                        x-text="formatCurrency(printCost)">
    
                                    </span>
    
                                </div>
    
    
    
                                <div
                                    class="flex
                                        justify-between
                                        gap-4">
    
                                    <span
                                        class="text-sm
                                            text-zinc-500
                                            dark:text-zinc-400">
    
                                        Finishing
    
                                    </span>
    
    
                                    <span
                                        class="text-sm
                                            text-zinc-700
                                            dark:text-zinc-200"
    
                                        x-text="formatCurrency(finishingCost)">
    
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
    
                                <p
                                    class="text-xs
                                        text-zinc-400">
    
                                    Total Estimasi
    
                                </p>
    
    
                                <p
                                    class="mt-2
    
                                        text-2xl
                                        font-bold
    
                                        text-zinc-900
                                        dark:text-white"
    
                                    x-text="formatCurrency(totalPrice)">
    
                                </p>
    
    
                                <p
                                    class="text-xs
                                        text-zinc-400
                                        mt-1">
    
                                    <span x-text="quantity"></span>
                
                                    pcs
    
                                </p>
    
                            </div>
    
    
    
                            <!-- BUTTON -->
    
                            <button
    
                                type="button"
    
                                class="w-full
    
                                    mt-6
    
                                    py-3
    
                                    rounded-xl
    
                                    bg-accent
    
                                    text-white
    
                                    text-sm
                                    font-medium
    
                                    hover:opacity-90
    
                                    transition-opacity">
    
                                Lanjutkan Pesanan →
    
                            </button>
    
                            <!-- =========================
                                ADD TO BASKET BUTTON
                            ========================= -->
    
                            <button
                                type="button"

                                @click="
                                    isInBasket(4)
                                        ? removeFromBasket(4)
                                        : addToBasket({
                                            id: 4,
                                            title: 'Kartu Nama',
                                            category: 'Kartu Nama',
                                            printType: 'a3',
                                            printTypeLabel: 'Digital Print A3+',
                                            img: '{{ asset('images/example-9.jpg') }}',
                                            url: '{{ route('user_product_details') }}'
                                        })
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

                                <template x-if="!isInBasket(4)">

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

                                <template x-if="isInBasket(4)">

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
    
                        * Harga yang ditampilkan merupakan estimasi sementara dan dapat berubah sesuai konfigurasi produksi.
    
                    </p>
    
    
                </div>
    
            </header>
    
    
        </article>
    </div>
@endsection



@section('scripts')

    <script>

        /* =========================
           SHARE PRODUCT
        ========================== */

        function shareProduct() {

            return {

                open: false,

                copied: false,

                title: 'Kartu Nama',

                description:
                    'Kartu nama premium isi 100 pcs lengkap dengan box plastik.',


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
            function () {

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
                    function () {

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

                                    class="w-full
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

                    }
                );


                componentContainer.addEventListener(
                    'click',
                    function (event) {

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

    </script>

@endsection
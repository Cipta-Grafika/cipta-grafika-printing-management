@extends('user_master')

@section('contents')
    <div x-data="products()" x-init="init()">

        <!-- =========================
                    HERO SECTION
                ========================== -->

        <section class="pt-36 pb-12 relative overflow-hidden">

            <div
                class="absolute top-0 right-0
                    w-80 h-80
                    bg-accent/10
                    rounded-full
                    blur-3xl
                    pointer-events-none">
            </div>


            <div class="max-w-6xl mx-auto px-6 relative z-10">


                <p
                    class="reveal
                        text-xs
                        font-medium
                        text-accent
                        tracking-widest
                        uppercase
                        mb-3">

                    Keranjang Belanja

                </p>


                <h1
                    class="reveal
                        font-display
                        font-bold
                        text-5xl
                        md:text-6xl
                        text-zinc-900
                        dark:text-white
                        leading-tight
                        mb-4">

                    Siap untuk dicetak.

                </h1>


                <!-- JUMLAH PRODUK -->

                <p
                    class="reveal
                        text-lg
                        text-zinc-500
                        dark:text-zinc-400
                        max-w-xl
                        leading-relaxed">

                    <span x-text="baskets.length"></span>

                    konfigurasi produk tersimpan.

                </p>


            </div>

        </section>



        <!-- =========================
                    BASKET LIST
                ========================== -->

        <section class="pb-24">

            <div class="max-w-6xl mx-auto px-6">


                <!-- =========================
                            EMPTY BASKET
                        ========================== -->

                <div x-show="baskets.length === 0" x-transition class="py-20
                        text-center">


                    <div
                        class="w-16 h-16
                            mx-auto
                            mb-5
                            rounded-full
                            flex
                            items-center
                            justify-center
                            bg-zinc-100
                            dark:bg-zinc-900">

                        <i
                            class="bi bi-cart
                                text-2xl
                                text-zinc-400">
                        </i>

                    </div>


                    <h2
                        class="text-xl
                            font-semibold
                            text-zinc-900
                            dark:text-white
                            mb-2">

                        Keranjang masih kosong

                    </h2>


                    <p
                        class="text-sm
                            text-zinc-500
                            dark:text-zinc-400
                            mb-6">

                        Belum ada produk yang ditambahkan ke keranjang.

                    </p>


                    <a href="{{ route('user_products') }}"
                        class="inline-flex
                            items-center
                            gap-2
                            bg-accent
                            text-white
                            px-5
                            py-3
                            rounded-xl
                            text-sm
                            font-medium
                            hover:opacity-90
                            transition-opacity">

                        <i class="bi bi-grid"></i>

                        Lihat Produk

                    </a>


                </div>



                <!-- =========================
                            BASKET PRODUCTS
                        ========================== -->

                <div x-show="baskets.length > 0" x-transition
                    class="grid
                        grid-cols-1
                        sm:grid-cols-2
                        lg:grid-cols-3
                        gap-6">


                    <template x-for="product in baskets" :key="product.id">


                        <!-- PRODUCT CARD -->

                        <div
                            class="group
                                rounded-2xl
                                overflow-hidden
                                border
                                border-zinc-200
                                dark:border-zinc-800
                                bg-white
                                dark:bg-zinc-950
                                transition-all
                                duration-300">


                            <!-- IMAGE -->

                            <div
                                class="relative
                                    h-56
                                    overflow-hidden
                                    bg-zinc-100
                                    dark:bg-zinc-900">


                                <template x-if="product.img">

                                    <img :src="product.img" :alt="product.title"
                                        class="w-full
                                            h-full
                                            object-cover
                                            transition-transform
                                            duration-500
                                            group-hover:scale-105">

                                </template>


                                <!-- PRINT TYPE -->

                                <div
                                    class="absolute
                                        top-4
                                        left-4
                                        px-3
                                        py-1.5
                                        rounded-full
                                        text-xs
                                        font-medium
                                        bg-white/90
                                        dark:bg-zinc-900/90
                                        text-zinc-700
                                        dark:text-zinc-300
                                        backdrop-blur-sm">

                                    <span x-text="product.printTypeLabel">
                                    </span>

                                </div>


                            </div>



                            <!-- CONTENT -->

                            <div class="p-5">


                                <p class="text-xs
                                        text-accent
                                        font-medium
                                        mb-2"
                                    x-text="product.category">

                                </p>


                                <h3 class="font-display
                                        font-bold
                                        text-xl
                                        text-zinc-900
                                        dark:text-white
                                        mb-2"
                                    x-text="product.title">

                                </h3>


                                <p
                                    class="text-sm
                                        text-zinc-500
                                        dark:text-zinc-400
                                        leading-relaxed
                                        mb-5">

                                    Produk telah disimpan di keranjang dan siap dikonfigurasi.

                                </p>



                                <!-- ACTION -->

                                <div
                                    class="flex
                                        items-center
                                        gap-3">


                                    <!-- CONFIGURE -->

                                    <a :href="product.url"
                                        class="flex-1
                                            text-center
                                            py-2.5
                                            rounded-xl
                                            bg-accent
                                            text-white
                                            text-sm
                                            font-medium
                                            hover:opacity-90
                                            transition-opacity">

                                        Konfigurasi

                                    </a>



                                    <!-- REMOVE -->

                                    <button type="button" @click="removeFromBasket(product.id)"
                                        class="w-11
                                            h-11
                                            shrink-0
                                            flex
                                            items-center
                                            justify-center
                                            rounded-xl
                                            border
                                            border-zinc-200
                                            dark:border-zinc-800
                                            text-zinc-500
                                            dark:text-zinc-400
                                            hover:text-red-500
                                            hover:border-red-200
                                            dark:hover:border-red-900
                                            transition-colors"
                                        title="Hapus dari keranjang">

                                        <i class="bi bi-trash3-fill"></i>

                                    </button>


                                </div>


                            </div>


                        </div>


                    </template>


                </div>


            </div>

        </section>


    </div>
@endsection

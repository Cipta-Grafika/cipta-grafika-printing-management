@extends('user_master')

@section('contents')
    <div x-data="products()">

        <!-- =========================
                HERO SECTION
            ========================== -->
        <section class="pt-36 pb-12 relative overflow-hidden">

            <div class="absolute top-0 right-0 w-80 h-80 bg-accent/10 rounded-full blur-3xl pointer-events-none">
            </div>

            <div class="max-w-6xl mx-auto px-6 relative z-10">

                <p class="reveal text-xs font-medium text-accent tracking-widest uppercase mb-3">
                    Produk Kami
                </p>

                <h1
                    class="reveal font-display font-bold text-5xl md:text-6xl text-zinc-900 dark:text-white leading-tight mb-4">
                    Semua yang kamu butuhkan,<br>
                    siap dicetak.
                </h1>

                <p class="reveal text-lg text-zinc-500 dark:text-zinc-400 max-w-xl leading-relaxed">
                    Pilih produk sesuai kebutuhan Anda dan temukan solusi percetakan
                    yang tepat dari Digital Print A3+ hingga Large Format.
                </p>

            </div>

        </section>



        <!-- =========================
                PRODUCT & FILTER SECTION
            ========================== -->
        <section class="pb-24">

            <div class="max-w-6xl mx-auto px-6">

                <div class="flex flex-col lg:flex-row gap-8">

                    <!-- =========================
                            FILTER SIDEBAR
                        ========================== -->
                    <aside class="lg:w-64 shrink-0">
                        <!-- MOBILE FILTER BUTTON -->
                        <button @click="filterOpen = !filterOpen"
                            class="lg:hidden w-full flex items-center justify-between
                                px-5 py-4 mb-5
                                rounded-2xl
                                bg-white dark:bg-zinc-900
                                border border-zinc-100 dark:border-zinc-800
                                text-zinc-900 dark:text-white">

                            <span class="font-medium">
                                Filter Produk
                            </span>

                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                stroke-linejoin="round" class="transition-transform duration-300"
                                :class="filterOpen ? 'rotate-180' : ''">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <!-- FILTER CONTENT -->
                        <div x-show="filterOpen || window.innerWidth >= 1024" x-transition class="lg:block">
                            <div
                                class="rounded-2xl
                                    bg-white dark:bg-zinc-900
                                    border border-zinc-100 dark:border-zinc-800
                                    p-6">


                                <!-- FILTER HEADER -->
                                <div class="flex items-center justify-between mb-7">

                                    <h2 class="font-display font-bold text-lg text-zinc-900 dark:text-white">
                                        Filter Produk
                                    </h2>


                                    <button @click="resetFilter()"
                                        class="text-xs font-medium text-accent hover:opacity-70 transition-opacity">
                                        Reset
                                    </button>
                                </div>

                                <!-- =========================
                                        JENIS PENCETAKAN
                                    ========================== -->
                                <div>
                                    <p class="text-xs font-medium tracking-widest uppercase text-zinc-400 mb-4">
                                        Jenis Pencetakan
                                    </p>


                                    <div class="space-y-3">
                                        <!-- =========================
                                                SEMUA
                                            ========================== -->
                                        <label
                                            class="flex items-center gap-3
                                                cursor-pointer group
                                                px-3 py-2.5
                                                rounded-xl
                                                border border-transparent
                                                transition-all duration-200
                                                hover:bg-zinc-100
                                                dark:hover:bg-white/5"
                                            :class="printType === 'all'
                                                ?
                                                'bg-orange-50 border-orange-200 dark:bg-white/10 dark:border-white/20' :
                                                ''">

                                            <input type="radio" name="printType" value="all" x-model="printType"
                                                class="sr-only">


                                            <!-- CUSTOM RADIO -->
                                            <div class="w-5 h-5 shrink-0
                                                    rounded-full
                                                    transition-all duration-200"
                                                :class="printType === 'all'
                                                    ?
                                                    'bg-accent dark:bg-zinc-700 dark:border-2 dark:border-zinc-500' :
                                                    'bg-white border-2 border-zinc-300 dark:bg-zinc-800 dark:border-zinc-600'">

                                            </div>


                                            <span
                                                class="text-sm
                                                    text-zinc-600 dark:text-zinc-400
                                                    transition-colors duration-200
                                                    group-hover:text-zinc-900
                                                    dark:group-hover:text-white"
                                                :class="printType === 'all'
                                                    ?
                                                    'font-semibold text-zinc-900 dark:text-white' :
                                                    ''">
                                                Semua
                                            </span>
                                        </label>

                                        <!-- =========================
                                                DIGITAL PRINT A3+
                                            ========================== -->
                                        <label
                                            class="flex items-center gap-3
                                                cursor-pointer group
                                                px-3 py-2.5
                                                rounded-xl
                                                border border-transparent
                                                transition-all duration-200
                                                hover:bg-zinc-100
                                                dark:hover:bg-white/5"
                                            :class="printType === 'a3'
                                                ?
                                                'bg-orange-50 border-orange-200 dark:bg-white/10 dark:border-white/20' :
                                                ''">

                                            <input type="radio" name="printType" value="a3" x-model="printType"
                                                class="sr-only">


                                            <!-- CUSTOM RADIO -->
                                            <div class="w-5 h-5 shrink-0
                                                    rounded-full
                                                    transition-all duration-200"
                                                :class="printType === 'a3'
                                                    ?
                                                    'bg-accent dark:bg-zinc-700 dark:border-2 dark:border-zinc-500' :
                                                    'bg-white border-2 border-zinc-300 dark:bg-zinc-800 dark:border-zinc-600'">

                                            </div>

                                            <span
                                                class="text-sm
                                                    text-zinc-600 dark:text-zinc-400
                                                    transition-colors duration-200
                                                    group-hover:text-zinc-900
                                                    dark:group-hover:text-white"
                                                :class="printType === 'a3'
                                                    ?
                                                    'font-semibold text-zinc-900 dark:text-white' :
                                                    ''">

                                                Digital Print A3+
                                            </span>
                                        </label>

                                        <!-- =========================
                                                LARGE FORMAT
                                            ========================== -->
                                        <label
                                            class="flex items-center gap-3
                                                cursor-pointer group
                                                px-3 py-2.5
                                                rounded-xl
                                                border border-transparent
                                                transition-all duration-200
                                                hover:bg-zinc-100
                                                dark:hover:bg-white/5"
                                            :class="printType === 'large-format'
                                                ?
                                                'bg-orange-50 border-orange-200 dark:bg-white/10 dark:border-white/20' :
                                                ''">

                                            <input type="radio" name="printType" value="large-format" x-model="printType"
                                                class="sr-only">


                                            <!-- CUSTOM RADIO -->
                                            <div class="w-5 h-5 shrink-0
                                                    rounded-full
                                                    transition-all duration-200"
                                                :class="printType === 'large-format'
                                                    ?
                                                    'bg-accent dark:bg-zinc-700 dark:border-2 dark:border-zinc-500' :
                                                    'bg-white border-2 border-zinc-300 dark:bg-zinc-800 dark:border-zinc-600'">

                                            </div>

                                            <span
                                                class="text-sm
                                                    text-zinc-600 dark:text-zinc-400
                                                    transition-colors duration-200
                                                    group-hover:text-zinc-900
                                                    dark:group-hover:text-white"
                                                :class="printType === 'large-format'
                                                    ?
                                                    'font-semibold text-zinc-900 dark:text-white' :
                                                    ''">
                                                Large Format
                                            </span>
                                        </label>
                                    </div>
                                </div>

                                <!-- DIVIDER -->
                                <div class="border-t border-zinc-100 dark:border-zinc-800 my-7">
                                </div>

                                <!-- =========================
                                        KATEGORI PRODUK
                                    ========================== -->
                                <div>
                                    <p
                                        class="text-xs font-medium tracking-widest uppercase
                                            text-zinc-400 mb-4">
                                        Kategori Produk
                                    </p>


                                    <div class="space-y-3">
                                        <template x-for="category in categories" :key="category">

                                            <label
                                                class="flex items-center gap-3
                                                    cursor-pointer group
                                                    px-3 py-2.5
                                                    rounded-xl
                                                    border
                                                    border-transparent
                                                    transition-all duration-200

                                                    hover:bg-zinc-100
                                                    dark:hover:bg-white/5"
                                                :class="selectedCategories.includes(category) ?
                                                    'bg-orange-50 border-orange-200 dark:bg-white/10 dark:border-white/20' :
                                                    ''">

                                                <!-- CHECKBOX ASLI -->
                                                <input type="checkbox" :value="category" x-model="selectedCategories"
                                                    class="peer sr-only">

                                                <!-- CUSTOM CHECKBOX -->
                                                <div
                                                    class="w-5 h-5 shrink-0
                                                        rounded-md
                                                        border-2
                                                        border-zinc-300
                                                        dark:border-zinc-600

                                                        bg-white
                                                        dark:bg-zinc-800

                                                        flex items-center justify-center
                                                        transition-all duration-200

                                                        group-hover:border-accent

                                                        peer-checked:bg-accent
                                                        peer-checked:border-accent

                                                        dark:peer-checked:bg-white
                                                        dark:peer-checked:border-white">

                                                    <!-- CHECK ICON -->
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                        class="w-3.5 h-3.5

                                                            text-white
                                                            dark:text-zinc-900

                                                            opacity-0
                                                            scale-50

                                                            transition-all duration-200

                                                            peer-checked:opacity-100
                                                            peer-checked:scale-100"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                        stroke-width="3">

                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M5 12l4 4L19 6" />
                                                    </svg>
                                                </div>

                                                <!-- CATEGORY NAME -->
                                                <span
                                                    class="text-sm
                                                        text-zinc-600
                                                        dark:text-zinc-400

                                                        transition-colors duration-200

                                                        group-hover:text-zinc-900
                                                        dark:group-hover:text-white"
                                                    :class="selectedCategories.includes(category) ?
                                                        'font-semibold text-zinc-900 dark:text-white' :
                                                        ''"
                                                    x-text="category">
                                                </span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <!-- =========================
                            PRODUCT AREA
                        ========================== -->
                    <div class="flex-1 min-w-0">
                        <!-- SEARCH PRODUCT -->
                        <div class="mb-6">
                            <div class="relative">
                                <input type="text" x-model="search" placeholder="Cari produk..."
                                    class="w-full
                                        pl-11 pr-4 py-3.5
                                        rounded-xl
                                        bg-white dark:bg-zinc-900
                                        border border-zinc-100 dark:border-zinc-800
                                        text-sm text-zinc-900 dark:text-white
                                        placeholder:text-zinc-400
                                        focus:outline-none
                                        focus:border-accent
                                        transition-colors">
                                <!-- SEARCH ICON -->
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="absolute left-4 top-1/2 -translate-y-1/2
                                        w-5 h-5
                                        text-zinc-400"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">

                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                                </svg>
                            </div>
                        </div>

                        <!-- RESULT INFO -->
                        <div class="flex items-center justify-between mb-6">
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                Menampilkan
                                <span class="font-semibold text-zinc-900 dark:text-white" x-text="visibleProducts.length">
                                </span> produk
                            </p>
                        </div>



                        <!-- =========================
                                PRODUCT GRID
                            ========================== -->
                        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6">
                            <template x-for="p in visibleProducts" :key="p.id">
                                <article
                                    class="project-card group h-full rounded-2xl overflow-hidden
                                        bg-white dark:bg-zinc-900
                                        border border-zinc-100 dark:border-zinc-800
                                        hover:border-accent
                                        transition-colors">
                                    <!-- PRODUCT IMAGE -->
                                    <a :href="p.url" class="block photo-frame w-full h-52">

                                        <img :src="p.img" :alt="p.title" loading="lazy"
                                            class="w-full h-full object-cover">
                                    </a>

                                    <!-- PRODUCT CONTENT -->
                                    <div class="p-6">
                                        <!-- TAG -->
                                        <div class="flex flex-wrap gap-2 mb-3">

                                            <!-- PRINT TYPE -->
                                            <span
                                                class="text-xs px-2.5 py-1 rounded-full
                                                    bg-orange-50 dark:bg-zinc-800
                                                    text-accent
                                                    border border-orange-200 dark:border-zinc-700"
                                                x-text="p.printTypeLabel">

                                            </span>

                                            <!-- CATEGORY -->
                                            <span
                                                class="text-xs px-2.5 py-1 rounded-full
                                                    bg-zinc-100 dark:bg-zinc-800
                                                    text-zinc-600 dark:text-zinc-400"
                                                x-text="p.category">
                                            </span>
                                        </div>

                                        <!-- TITLE -->
                                        <a :href="p.url">
                                            <h3 class="font-display font-bold text-lg
                                                    text-zinc-900 dark:text-white
                                                    mb-2
                                                    group-hover:text-accent
                                                    transition-colors"
                                                x-text="p.title">
                                            </h3>
                                        </a>

                                        <!-- DESCRIPTION -->
                                        <p class="text-sm
                                                text-zinc-500 dark:text-zinc-400
                                                leading-relaxed mb-5"
                                            x-text="p.desc">

                                        </p>

                                        <!-- FOOTER -->
                                        <div class="flex items-center justify-between">
                                            <a :href="p.url"
                                                class="inline-flex items-center gap-1.5
                                                    text-sm font-medium
                                                    text-zinc-900 dark:text-white
                                                    nav-link">
                                                Lihat Produk →
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            </template>
                        </div>

                        <!-- =========================
                                EMPTY STATE
                            ========================== -->
                        <div x-show="visibleProducts.length === 0" x-cloak class="text-center py-20">
                            <p class="text-zinc-400 text-sm">
                                Produk tidak ditemukan.
                            </p>

                            <button @click="resetFilter()"
                                class="mt-4 text-sm font-medium
                                    text-accent hover:opacity-70
                                    transition-opacity">
                                Reset Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- =========================
                CTA SECTION
            ========================== -->
        <section class="pb-24">
            <div class="max-w-6xl mx-auto px-6">
                <div
                    class="bg-zinc-900 dark:bg-zinc-800
                        rounded-3xl
                        p-10 md:p-14
                        text-center
                        relative overflow-hidden">

                    <div
                        class="absolute top-0 right-0
                            w-64 h-64
                            bg-accent/20
                            rounded-full blur-3xl
                            pointer-events-none">
                    </div>

                    <div
                        class="absolute bottom-0 left-0
                            w-40 h-40
                            bg-accent/10
                            rounded-full blur-2xl
                            pointer-events-none">
                    </div>

                    <div class="relative z-10">
                        <p
                            class="text-xs font-medium
                                text-accent
                                tracking-widest uppercase mb-4">
                            Butuh Solusi Printing?
                        </p>

                        <h2
                            class="font-display font-bold
                                text-3xl md:text-4xl
                                text-white mb-4">
                            Ayo Wujudkan Ide-Ide Kamu
                        </h2>

                        <p class="text-zinc-400
                                max-w-md mx-auto mb-8">
                            Beritahu kami apa yang kamu butuhkan dan temukan
                            solusi percetakan yang tepat untuk proyekmu.
                            Dari cetak digital hingga format besar,
                            Cipta Grafika siap membantu.
                        </p>

                        <a href="https://wa.me/6282213290760" target="_blank"
                            class="inline-flex items-center gap-2
                                btn-primary
                                bg-accent text-white
                                font-medium
                                px-8 py-3.5
                                rounded-full
                                hover:bg-accent-light
                                transition-colors">
                            Butuh Bantuan? Hubungi Kami →
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

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
                    <aside class="lg:w-64 shrink-0 lg:sticky lg:top-20 lg:self-start">

                        <!-- FILTER CONTENT -->
                        <div x-show="filterOpen || window.innerWidth >= 1024" x-transition class="lg:block">
                            <div
                                class="fixed inset-x-4 top-20 z-50 max-h-[calc(100dvh-6rem)] overflow-y-auto overscroll-contain
                                    rounded-2xl bg-white dark:bg-[#1b3157] border border-zinc-200 dark:border-white/10
                                    p-5 shadow-2xl shadow-zinc-950/20
                                    lg:static lg:max-h-none lg:overflow-visible lg:rounded-2xl lg:p-6 lg:shadow-sm lg:shadow-zinc-900/5">


                                <!-- FILTER HEADER -->
                                <div class="flex items-center justify-between mb-6">

                                    <div>
                                        <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-accent mb-1">
                                            Sesuaikan pilihan
                                        </p>
                                        <h2 class="font-display font-bold text-lg text-zinc-900 dark:text-white">
                                            Filter Produk
                                        </h2>
                                    </div>

                                    <div class="flex items-center gap-3">
                                        <button @click="resetFilter()"
                                            class="text-xs font-semibold text-accent hover:opacity-70 transition-opacity">
                                            Reset
                                        </button>

                                        <button type="button" @click="filterOpen = false"
                                            class="lg:hidden flex h-9 w-9 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300"
                                            aria-label="Tutup filter">
                                            <i class="bi bi-x-lg text-sm" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- =========================
                                                                                        JENIS PENCETAKAN
                                                                                    ========================== -->
                                <div>
                                    <p class="text-[11px] font-semibold tracking-[0.16em] uppercase text-zinc-400 mb-3">
                                        Jenis Pencetakan
                                    </p>


                                    <p x-show="filtersLoading" x-cloak class="text-xs text-zinc-400 mt-3">
                                        Memuat filter...
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
                                                ? 'bg-accent/10 border-accent/25 dark:bg-accent/10 dark:border-accent/25' :
                                                ''">

                                            <input type="radio" name="printType" value="all" x-model="printType"
                                                class="sr-only">


                                            <!-- CUSTOM RADIO -->
                                            <div class="w-5 h-5 shrink-0 rounded-full border-2 flex items-center justify-center transition-all duration-200"
                                                :class="printType === 'all'
                                                    ? 'border-accent bg-white dark:bg-zinc-950' :
                                                    'border-zinc-300 bg-white dark:border-zinc-600 dark:bg-zinc-900'">
                                                <span x-show="printType === 'all'"
                                                    class="h-2.5 w-2.5 rounded-full bg-accent shadow-sm shadow-accent/40"></span>
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

                                        <template x-for="engine in engines" :key="engine.id">

                                            <label
                                                class="flex items-center gap-3
                                                    cursor-pointer group
                                                    px-3 py-2.5
                                                    rounded-xl
                                                    border border-transparent
                                                    transition-all duration-200
                                                    hover:bg-zinc-100
                                                    dark:hover:bg-white/5"
                                                :class="printType === engine.id ?
                                                    'bg-accent/10 border-accent/25 dark:bg-accent/10 dark:border-accent/25' :
                                                    ''">

                                                <input type="radio" name="printType" :value="engine.id"
                                                    x-model="printType" class="sr-only">


                                                <!-- CUSTOM RADIO -->
                                                <div class="w-5 h-5 shrink-0 rounded-full border-2 flex items-center justify-center transition-all duration-200"
                                                    :class="printType === engine.id ?
                                                        'border-accent bg-white dark:bg-zinc-950' :
                                                        'border-zinc-300 bg-white dark:border-zinc-600 dark:bg-zinc-900'">
                                                    <span x-show="printType === engine.id"
                                                        class="h-2.5 w-2.5 rounded-full bg-accent shadow-sm shadow-accent/40"></span>
                                                </div>

                                                <span
                                                    class="text-sm
                                                        text-zinc-600 dark:text-zinc-400
                                                        transition-colors duration-200
                                                        group-hover:text-zinc-900
                                                        dark:group-hover:text-white"
                                                    :class="printType === engine.id ?
                                                        'font-semibold text-zinc-900 dark:text-white' :
                                                        ''"
                                                    x-text="engine.name">
                                                </span>
                                            </label>

                                        </template>
                                    </div>
                                </div>

                                <!-- DIVIDER -->
                                <div class="border-t border-zinc-100 dark:border-zinc-800 my-5">
                                </div>

                                <!-- =========================
                                                        KATEGORI PRODUK
                                                    ========================== -->
                                <div>
                                    <p
                                        class="text-[11px] font-semibold tracking-[0.16em] uppercase
                                            text-zinc-400 mb-3">
                                        Kategori Produk
                                    </p>

                                    <p x-show="filtersLoading" x-cloak class="text-xs text-zinc-400 mt-3">
                                        Memuat filter...
                                    </p>

                                    <div class="space-y-3">
                                        <template x-for="category in categories" :key="category.id">

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
                                                :class="selectedCategories.includes(category.id) ?
                                                    'bg-accent/10 border-accent/25 dark:bg-accent/10 dark:border-accent/25' :
                                                    ''">

                                                <!-- CHECKBOX ASLI -->
                                                <input type="checkbox" :value="category.id" x-model="selectedCategories"
                                                    class="peer sr-only">

                                                <!-- CUSTOM CHECKBOX -->
                                                <div
                                                    class="w-5 h-5 shrink-0 rounded-md border-2 flex items-center justify-center transition-all duration-200 group-hover:border-accent"
                                                    :style="selectedCategories.includes(category.id)
                                                        ? 'border-color: #c2a857; background-color: rgba(194, 168, 87, 0.12); box-shadow: 0 0 0 2px rgba(194, 168, 87, 0.12);'
                                                        : 'border-color: rgba(148, 163, 184, 0.55); background-color: transparent;'">

                                                    <!-- CHECK ICON -->
                                                    <svg x-show="selectedCategories.includes(category.id)"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        class="w-3.5 h-3.5 transition-transform duration-200"
                                                        fill="none" viewBox="0 0 24 24" stroke="#c2a857"
                                                        stroke-width="3.2">

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
                                                    :class="selectedCategories.includes(category.id) ?
                                                        'font-semibold text-zinc-900 dark:text-white' :
                                                        ''"
                                                    x-text="category.name">
                                                </span>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <div x-show="filterOpen" x-cloak x-transition.opacity
                        @click="filterOpen = false"
                        class="lg:hidden fixed inset-0 z-40 bg-zinc-950/45 backdrop-blur-[2px]"
                        aria-hidden="true"></div>

                    <!-- =========================
                                                                            PRODUCT AREA
                                                                        ========================== -->
                    <div class="flex-1 min-w-0">
                        <!-- MOBILE STICKY SEARCH & FILTER -->
                        <div
                            class="lg:hidden sticky top-16 z-30 -mx-6 mb-5 px-6 py-3 bg-white/70 dark:bg-[#16294d]/90 backdrop-blur-xl">
                            <div class="flex items-center gap-2">
                                <label class="relative min-w-0 flex-1">
                                    <span class="sr-only">Cari produk</span>
                                    <input type="text" x-model="search" placeholder="Cari produk..."
                                        class="w-full rounded-2xl border border-zinc-200/80 dark:border-white/10 bg-zinc-100/80 dark:bg-white/5 py-3 pl-11 pr-10 text-sm text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:border-accent/70 focus:outline-none focus:ring-4 focus:ring-accent/10 transition">
                                    <i class="bi bi-search absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400"
                                        aria-hidden="true"></i>
                                    <button x-show="search" x-cloak type="button" @click="search = ''"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 flex h-7 w-7 items-center justify-center rounded-full text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700"
                                        aria-label="Hapus pencarian">
                                        <i class="bi bi-x-lg text-xs" aria-hidden="true"></i>
                                    </button>
                                </label>
                                <button type="button" @click="filterOpen = !filterOpen"
                                    class="relative flex h-12 shrink-0 items-center gap-2 rounded-2xl bg-accent px-4 text-sm font-semibold text-white shadow-md shadow-accent/20 transition hover:brightness-105"
                                    :aria-expanded="filterOpen.toString()">
                                    <i class="bi bi-sliders2-vertical" aria-hidden="true"></i>
                                    <span>Filter</span>
                                    <span x-show="printType !== 'all' || selectedCategories.length > 0"
                                        x-cloak
                                        class="flex min-w-5 h-5 items-center justify-center rounded-full bg-white px-1 text-[10px] font-bold text-zinc-900"
                                        x-text="Number(printType !== 'all') + selectedCategories.length">
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- SEARCH PRODUCT -->
                        <div class="hidden lg:block mb-6">
                            <div class="relative">
                                <input type="text" x-model="search" placeholder="Cari produk..."
                                    class="w-full
                                        pl-11 pr-4 py-3.5
                                        rounded-2xl
                                        bg-white dark:bg-zinc-900
                                        border border-zinc-200 dark:border-zinc-800
                                        text-sm text-zinc-900 dark:text-white
                                        placeholder:text-zinc-400
                                        focus:outline-none
                                        focus:border-accent
                                        focus:ring-4 focus:ring-accent/10
                                        transition">
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
                        <div class="flex items-center justify-between mb-6 border-b border-zinc-200/70 dark:border-white/10 px-1 py-3">
                            <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                Menampilkan
                                <span class="font-semibold text-zinc-900 dark:text-white" x-text="visibleProducts.length">
                                </span> produk
                            </p>
                            <button x-show="search || printType !== 'all' || selectedCategories.length > 0"
                                x-cloak @click="resetFilter()"
                                class="inline-flex items-center gap-1.5 text-xs font-semibold text-accent hover:opacity-70">
                                <i class="bi bi-arrow-counterclockwise" aria-hidden="true"></i>
                                Hapus filter
                            </button>
                        </div>



                        <!-- =========================
                                                                                PRODUCT GRID
                                                                            ========================== -->
                        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6">
                            <template x-for="p in visibleProducts" :key="p.key">
                                <article
                                    class="project-card group flex h-full flex-col rounded-2xl overflow-hidden
                                        bg-white dark:bg-zinc-900
                                        border border-zinc-200/80 dark:border-zinc-800
                                        shadow-sm hover:-translate-y-1 hover:border-accent
                                        hover:shadow-xl hover:shadow-zinc-900/10 dark:hover:shadow-black/30
                                        transition-all duration-300">
                                    <!-- PRODUCT IMAGE -->
                                    <a :href="p.url"
                                        class="relative block photo-frame w-full h-52 overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                        <img :src="p.cover_url" :alt="p.title" loading="lazy"
                                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                                        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/65 via-transparent to-zinc-950/10"
                                            aria-hidden="true"></div>
                                        <span
                                            class="absolute top-3 left-3 max-w-[75%] truncate rounded-full border border-white/25 bg-zinc-950/45 px-3 py-1.5 text-[11px] font-medium text-white backdrop-blur-md"
                                            x-text="p.engine_name || 'Large Format'">
                                        </span>
                                        <span
                                            class="absolute top-3 right-3 flex h-9 w-9 items-center justify-center rounded-full border border-white/25 bg-zinc-950/40 text-white backdrop-blur-md transition-all duration-300 group-hover:border-accent group-hover:bg-accent"
                                            aria-hidden="true">
                                            <i class="bi bi-arrow-up-right text-sm"></i>
                                        </span>
                                        <span class="absolute bottom-3 left-3 text-xs font-medium text-white/90"
                                            x-text="p.category_name">
                                        </span>
                                    </a>

                                    <!-- PRODUCT CONTENT -->
                                    <div class="flex flex-1 flex-col p-5">
                                        <div class="mb-3 flex items-center gap-2">
                                            <span class="h-1.5 w-1.5 rounded-full bg-accent" aria-hidden="true"></span>
                                            <span
                                                class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                                Produk cetak
                                            </span>
                                        </div>

                                        <!-- TITLE -->
                                        <a :href="p.url">
                                            <h3 class="font-display font-bold text-lg
                                                    text-zinc-900 dark:text-white
                                                    mb-2 line-clamp-2
                                                    group-hover:text-accent
                                                    transition-colors"
                                                x-text="p.title">
                                            </h3>
                                        </a>

                                        <!-- DESCRIPTION -->
                                        <p class="flex-1 text-sm
                                                text-zinc-500 dark:text-zinc-400
                                                leading-relaxed mb-5 line-clamp-2"
                                            x-text="p.desc">

                                        </p>

                                        <!-- FOOTER -->
                                        <div class="mt-auto border-t border-zinc-100 dark:border-zinc-800 pt-4">
                                            <a :href="p.url"
                                                class="inline-flex w-full items-center justify-between text-sm font-semibold text-zinc-800 dark:text-zinc-200 group-hover:text-accent transition-colors">
                                                <span>Lihat detail produk</span>
                                                <span
                                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 transition-colors group-hover:bg-accent/10 group-hover:text-accent"
                                                    aria-hidden="true">
                                                    <i class="bi bi-arrow-right"></i>
                                                </span>
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

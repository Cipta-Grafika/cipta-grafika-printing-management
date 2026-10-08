@extends('user_master')

@section('contents')
    <!-- ═══ HERO ═══ -->
    <section id="hero" class="relative min-h-[88vh] flex items-center pt-20 pb-16 overflow-hidden">
        <div class="absolute -top-20 right-0 w-[34rem] h-[34rem] bg-accent/10 rounded-full blur-3xl pointer-events-none"
            aria-hidden="true"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-zinc-200/50 dark:bg-zinc-800/30 rounded-full blur-3xl pointer-events-none"
            aria-hidden="true"></div>
        <div class="absolute inset-0 pointer-events-none opacity-[0.035] dark:opacity-[0.06]"
            style="background-image: radial-gradient(currentColor 1px, transparent 1px); background-size: 28px 28px;"
            aria-hidden="true"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 py-16 w-full">
            <div class="grid lg:grid-cols-[1.05fr_0.95fr] gap-14 lg:gap-20 items-center">

                <div>
                    <p class="reveal inline-flex items-center gap-3 text-xs md:text-sm font-semibold text-accent tracking-[0.2em] uppercase mb-6">
                        <span class="w-8 h-px bg-accent" aria-hidden="true"></span>
                        Solusi Cetak untuk Bisnis & Kebutuhan Anda
                    </p>
                    <h1
                        class="reveal d1 font-display font-bold text-5xl md:text-6xl lg:text-[4.5rem] leading-[1.04] tracking-tight text-zinc-900 dark:text-white mb-7">
                        Cetak ide Anda,<br>
                        <span class="text-accent">wujudkan</span> dengan kami.
                    </h1>
                    <p
                        class="reveal d2 text-base md:text-lg text-zinc-600 dark:text-zinc-300 leading-relaxed max-w-xl mb-9">
                        Dari cetak A3+ hingga Large Format, pilih produk, material, dan finishing yang sesuai—lalu lihat
                        estimasi harga sebelum memesan.
                    </p>
                    <div class="reveal d3 flex flex-wrap gap-4">
                        <a href="#products"
                            class="shimmer inline-flex items-center gap-2 bg-zinc-900 dark:bg-accent text-white font-semibold px-7 py-3.5 rounded-full shadow-lg shadow-zinc-900/10 dark:shadow-accent/20 hover:bg-zinc-700 dark:hover:brightness-110 transition text-sm">
                            Jelajahi Produk
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 12h14m-6-6 6 6-6 6" />
                            </svg>
                        </a>
                        <a href="{{ route('user_products') }}"
                            class="inline-flex items-center gap-2 border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-200 font-medium px-7 py-3.5 rounded-full hover:border-accent hover:text-accent transition-colors text-sm">
                            Lihat Semua Katalog
                        </a>
                    </div>
                    <div class="reveal d4 flex flex-wrap gap-x-9 gap-y-5 mt-12 pt-7 border-t border-zinc-200 dark:border-zinc-800">
                        <div>
                            <p class="font-display font-bold text-3xl text-zinc-900 dark:text-white">34+</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Pilihan Produk</p>
                        </div>
                        <div>
                            <p class="font-display font-bold text-3xl text-zinc-900 dark:text-white">21+</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Solusi Cetak</p>
                        </div>
                        <div>
                            <p class="font-display font-bold text-3xl text-zinc-900 dark:text-white">2</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Kategori Utama</p>
                        </div>
                    </div>
                </div>

                <div class="reveal d2 relative flex justify-center lg:justify-end">
                    <div class="absolute inset-8 rounded-[2.5rem] bg-accent/15 blur-2xl" aria-hidden="true"></div>
                    <div class="relative w-full max-w-[460px]">
                        <div class="absolute -top-5 -right-4 md:-right-6 w-28 h-28 rounded-3xl border border-accent/30 rotate-12"
                            aria-hidden="true"></div>
                        <div class="absolute -bottom-4 -left-4 w-24 h-24 rounded-full border-[10px] border-accent/20"
                            aria-hidden="true"></div>
                        <div class="relative aspect-[4/4.25] overflow-hidden rounded-[2rem] border border-white/70 dark:border-zinc-700 bg-zinc-100 dark:bg-zinc-900 shadow-2xl shadow-zinc-900/15">
                            <img src="{{ asset('images/hero-printing-showcase.svg') }}"
                                alt="Ilustrasi beragam produk cetak: brosur, kartu nama, kemasan, dan banner"
                                loading="eager" class="absolute inset-0 w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/85 via-zinc-950/10 to-transparent"
                                aria-hidden="true"></div>
                            <div class="absolute inset-x-0 bottom-0 p-6 md:p-8">
                                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/15 px-3 py-1.5 text-xs font-medium text-white backdrop-blur-md">
                                    <span class="w-1.5 h-1.5 rounded-full bg-accent"></span>
                                    A3+ · Large Format
                                </span>
                                <h2 class="mt-4 font-display font-bold text-2xl md:text-3xl text-white">
                                    Beragam Hasil Cetak
                                </h2>
                                <p class="mt-1 text-sm text-white/75">
                                    Kartu nama · Brosur · Kemasan · Banner
                                </p>
                            </div>
                        </div>
                        <div
                            class="absolute -top-5 left-5 md:left-8 flex items-center gap-3 rounded-2xl border border-zinc-100 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-4 py-3 shadow-xl shadow-zinc-900/10">
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-accent/10 text-accent">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M4 7.5A2.5 2.5 0 0 1 6.5 5h11A2.5 2.5 0 0 1 20 7.5v9a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 16.5v-9ZM8 9h8M8 12h8m-8 3h4" />
                                </svg>
                            </span>
                            <span>
                                <span class="block text-xs text-zinc-500 dark:text-zinc-400">Cetak sesuai kebutuhan</span>
                                <span class="block text-sm font-semibold text-zinc-900 dark:text-white">Material & finishing pilihan</span>
                            </span>
                        </div>
                        <div class="absolute -bottom-5 right-4 md:right-8 rounded-full bg-accent px-4 py-2.5 text-xs font-bold text-white shadow-lg shadow-accent/25">
                            Partner Cetak Anda
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ═══ PRODUCTS ═══ -->
    <section id="products" class="py-24 bg-zinc-50 dark:bg-zinc-900/40">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-14">
                <div>
                    <p class="reveal text-xs font-medium text-accent tracking-widest uppercase mb-3">PALING SERING DICETAK
                    </p>
                    <h2 class="reveal d1 font-display font-bold text-4xl md:text-5xl text-zinc-900 dark:text-white">Mulai
                        dari yang bisnis kamu butuhkan.</h2>
                </div>
                <a href="{{ route('user_products') }}"
                    class="reveal d1 text-sm font-medium text-zinc-500 dark:text-zinc-400 hover:text-accent transition-colors self-start sm:self-auto nl">Lihat
                    semua produk →</a>
            </div>



            <div class="grid md:grid-cols-4 gap-6">

                @forelse ($products as $product)
                    <article
                        class="project-card group flex h-full flex-col rounded-2xl overflow-hidden bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-sm hover:-translate-y-1 hover:border-accent hover:shadow-xl hover:shadow-zinc-900/10 dark:hover:shadow-black/30 transition-all duration-300">
                        <a href="{{ $product['url'] }}"
                            class="relative block photo-frame w-full h-52 overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                            @if ($product['cover_url'])
                                <img src="{{ $product['cover_url'] }}" alt="{{ $product['title'] }}" loading="lazy"
                                    class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center gap-2 text-zinc-400">
                                    <i class="bi bi-image text-3xl" aria-hidden="true"></i>
                                    <span class="text-xs">Gambar segera tersedia</span>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/65 via-transparent to-zinc-950/10"
                                aria-hidden="true"></div>
                            <span
                                class="absolute top-3 left-3 max-w-[75%] truncate rounded-full border border-white/25 bg-zinc-950/45 px-3 py-1.5 text-[11px] font-medium text-white backdrop-blur-md">
                                {{ $product['engine_name'] ?? 'Large Format' }}
                            </span>
                            <span
                                class="absolute top-3 right-3 flex h-9 w-9 items-center justify-center rounded-full border border-white/25 bg-zinc-950/40 text-white backdrop-blur-md transition-all duration-300 group-hover:border-accent group-hover:bg-accent"
                                aria-hidden="true">
                                <i class="bi bi-arrow-up-right text-sm"></i>
                            </span>
                            <span class="absolute bottom-3 left-3 text-xs font-medium text-white/90">
                                {{ $product['category_name'] }}
                            </span>
                        </a>

                        <div class="flex flex-1 flex-col p-5">
                            <div class="mb-3 flex items-center gap-2">
                                <span class="h-1.5 w-1.5 rounded-full bg-accent" aria-hidden="true"></span>
                                <span class="text-xs font-medium uppercase tracking-wider text-zinc-500 dark:text-zinc-400">
                                    Produk cetak
                                </span>
                            </div>

                            <a href="{{ $product['url'] }}">
                                <h3
                                    class="font-display font-bold text-lg text-zinc-900 dark:text-white mb-2 line-clamp-2 group-hover:text-accent transition-colors">
                                    {{ $product['title'] }}
                                </h3>
                            </a>

                            <p class="flex-1 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed mb-5 line-clamp-2">
                                {{ $product['desc'] }}
                            </p>

                            <div class="mt-auto border-t border-zinc-100 dark:border-zinc-800 pt-4">
                                <a href="{{ $product['url'] }}"
                                    class="inline-flex w-full items-center justify-between text-sm font-semibold text-zinc-800 dark:text-zinc-200 group-hover:text-accent transition-colors">
                                    <span>Lihat detail produk</span>
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 transition-colors group-hover:bg-accent/10 group-hover:text-accent"
                                        aria-hidden="true">
                                        <i class="bi bi-arrow-right"></i>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="md:col-span-4 text-sm text-zinc-500 dark:text-zinc-400">
                        Produk belum tersedia.
                    </p>
                @endforelse

            </div>
        </div>
    </section>

    <!-- ═══ BLOG ═══ -->
    <section id="blog" class="py-24">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-14">
                <div>
                    <p class="reveal text-xs font-medium text-accent tracking-widest uppercase mb-3">Pertanyaan Umum</p>
                    <h2 class="reveal d1 font-display font-bold text-4xl md:text-5xl text-zinc-900 dark:text-white">Informasi
                        penting sebelum memilih produk dan membuat pesanan.</h2>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-6"
                    style="height: auto; display: flex; justify-content: center; align-items: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="90%" height="400" viewBox="0 0 32 32" fill="none"
                        stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"
                        class="text-[#16294d] dark:text-[#c2a857]">

                        <!-- Oval Speech Bubble -->
                        <path d="M10 6h12a7 7 0 0 1 0 14H14l-5 4 1.5-4H10a7 7 0 0 1 0-14Z" />

                        <!-- Q&A -->
                        <text x="9" y="15.5" font-family="DynaPuff, sans-serif" font-size="7" font-weight="300"
                            fill="currentColor" stroke="none">
                            Q<tspan font-size="5">&amp;</tspan>A
                        </text>
                    </svg>
                </div>
                <div class="flex flex-col gap-4">

                    <!-- FAQ 1 -->
                    <article
                        class="reveal d1 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 overflow-hidden hover:border-accent transition-colors">
                        <button type="button"
                            class="faq-toggle w-full flex items-center justify-between gap-6 p-6 text-left">
                            <span class="font-display font-bold text-lg text-zinc-900 dark:text-white">
                                Bagaimana cara menemukan produk yang ingin saya cetak?
                            </span>

                            <span
                                class="faq-icon shrink-0 text-2xl font-light text-accent transition-transform duration-300">
                                +
                            </span>

                        </button>

                        <div class="faq-content grid grid-rows-[0fr] transition-[grid-template-rows] duration-300">
                            <div class="overflow-hidden">
                                <p class="px-6 pb-6 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    Jelajahi katalog produk, lalu pilih produk untuk melihat detail dan pilihan konfigurasi
                                    yang tersedia. Katalog mencakup produk cetak A3+ dan Large Format.
                                </p>
                            </div>
                        </div>
                    </article>


                    <!-- FAQ 2 -->
                    <article
                        class="reveal d2 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 overflow-hidden hover:border-accent transition-colors">
                        <button type="button"
                            class="faq-toggle w-full flex items-center justify-between gap-6 p-6 text-left">

                            <span class="font-display font-bold text-lg text-zinc-900 dark:text-white">
                                Bagaimana cara melihat estimasi harga?
                            </span>

                            <span
                                class="faq-icon shrink-0 text-2xl font-light text-accent transition-transform duration-300">
                                +
                            </span>

                        </button>

                        <div class="faq-content grid grid-rows-[0fr] transition-[grid-template-rows] duration-300">
                            <div class="overflow-hidden">
                                <p class="px-6 pb-6 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    Buka halaman detail produk, pilih lokasi dan jenis harga, lalu lengkapi material,
                                    ukuran, jumlah, serta finishing yang tersedia. Estimasi akan diperbarui berdasarkan
                                    konfigurasi tersebut.
                                </p>
                            </div>
                        </div>
                    </article>


                    <!-- FAQ 3 -->
                    <article
                        class="reveal d3 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 overflow-hidden hover:border-accent transition-colors">
                        <button type="button"
                            class="faq-toggle w-full flex items-center justify-between gap-6 p-6 text-left">

                            <span class="font-display font-bold text-lg text-zinc-900 dark:text-white">
                                Mengapa pilihan material bisa berbeda?
                            </span>

                            <span
                                class="faq-icon shrink-0 text-2xl font-light text-accent transition-transform duration-300">
                                +
                            </span>

                        </button>

                        <div class="faq-content grid grid-rows-[0fr] transition-[grid-template-rows] duration-300">
                            <div class="overflow-hidden">
                                <p class="px-6 pb-6 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    Pilihan material mengikuti lokasi dan kategori produk. Untuk kategori Display, pilihan
                                    juga disesuaikan dengan nama produk Display yang sedang Anda lihat.
                                </p>
                            </div>
                        </div>
                    </article>

                    <!-- FAQ 4 -->
                    <article
                        class="reveal d4 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 overflow-hidden hover:border-accent transition-colors">
                        <button type="button"
                            class="faq-toggle w-full flex items-center justify-between gap-6 p-6 text-left">
                            <span class="font-display font-bold text-lg text-zinc-900 dark:text-white">
                                Apakah semua produk tersedia di setiap lokasi atau cabang?
                            </span>
                            <span
                                class="faq-icon shrink-0 text-2xl font-light text-accent transition-transform duration-300">
                                +
                            </span>
                        </button>
                        <div class="faq-content grid grid-rows-[0fr] transition-[grid-template-rows] duration-300">
                            <div class="overflow-hidden">
                                <p class="px-6 pb-6 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    Saat ini produk dan material yang tersedia di katalog hanya dapat dipesan melalui
                                    lokasi atau cabang Purwakarta. Lokasi atau cabang lainnya belum tersedia untuk
                                    pemesanan melalui katalog ini.
                                </p>
                            </div>
                        </div>
                    </article>

                    <!-- FAQ 5 -->
                    <article
                        class="reveal d5 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 overflow-hidden hover:border-accent transition-colors">
                        <button type="button"
                            class="faq-toggle w-full flex items-center justify-between gap-6 p-6 text-left">
                            <span class="font-display font-bold text-lg text-zinc-900 dark:text-white">
                                Bagaimana jika ukuran material yang saya butuhkan tidak tersedia?
                            </span>
                            <span
                                class="faq-icon shrink-0 text-2xl font-light text-accent transition-transform duration-300">
                                +
                            </span>
                        </button>
                        <div class="faq-content grid grid-rows-[0fr] transition-[grid-template-rows] duration-300">
                            <div class="overflow-hidden">
                                <p class="px-6 pb-6 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    Masukkan panjang dan lebar produk pada form. Jika ukuran material custom tersedia,
                                    pilih ukuran referensi terlebih dahulu, kemudian pilih “Custom / Input Manual” dan
                                    isi lebar material yang dibutuhkan.
                                </p>
                            </div>
                        </div>
                    </article>

                    <!-- FAQ 6 -->
                    <article
                        class="reveal d6 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 overflow-hidden hover:border-accent transition-colors">
                        <button type="button"
                            class="faq-toggle w-full flex items-center justify-between gap-6 p-6 text-left">
                            <span class="font-display font-bold text-lg text-zinc-900 dark:text-white">
                                Apakah laminasi wajib dipilih?
                            </span>
                            <span
                                class="faq-icon shrink-0 text-2xl font-light text-accent transition-transform duration-300">
                                +
                            </span>
                        </button>
                        <div class="faq-content grid grid-rows-[0fr] transition-[grid-template-rows] duration-300">
                            <div class="overflow-hidden">
                                <p class="px-6 pb-6 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    Tidak. Jika tidak membutuhkan laminasi, pilih “Tanpa Laminasi”. Jika memilih
                                    laminasi, lengkapi ukuran laminasi yang tersedia agar estimasi dapat dihitung.
                                </p>
                            </div>
                        </div>
                    </article>

                    <!-- FAQ 7 -->
                    <article
                        class="reveal d7 bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-100 dark:border-zinc-800 overflow-hidden hover:border-accent transition-colors">
                        <button type="button"
                            class="faq-toggle w-full flex items-center justify-between gap-6 p-6 text-left">
                            <span class="font-display font-bold text-lg text-zinc-900 dark:text-white">
                                Apakah harga estimasi merupakan harga final?
                            </span>
                            <span
                                class="faq-icon shrink-0 text-2xl font-light text-accent transition-transform duration-300">
                                +
                            </span>
                        </button>
                        <div class="faq-content grid grid-rows-[0fr] transition-[grid-template-rows] duration-300">
                            <div class="overflow-hidden">
                                <p class="px-6 pb-6 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    Belum tentu. Harga pada estimator merupakan estimasi sementara berdasarkan
                                    konfigurasi yang dipilih dan dapat berubah sesuai konfigurasi produksi.
                                    Gunakan tombol lanjutkan pesanan untuk mengirim rincian estimasi melalui WhatsApp.
                                </p>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('user_master')

@section('contents')
    <!-- ═══ HERO ═══ -->
    <section id="hero" class="relative min-h-screen flex items-center pt-16 overflow-hidden">
        <div class="absolute top-1/4 right-0 w-96 h-96 bg-accent/10 rounded-full blur-3xl pointer-events-none"
            aria-hidden="true"></div>
        <div class="absolute bottom-1/4 left-0 w-64 h-64 bg-zinc-200/50 dark:bg-zinc-800/30 rounded-full blur-3xl pointer-events-none"
            aria-hidden="true"></div>

        <div class="relative z-10 max-w-6xl mx-auto px-6 py-24 w-full">
            <div class="grid md:grid-cols-2 gap-12 items-center">

                <div>
                    <p class="reveal text-sm font-medium text-accent tracking-widest uppercase mb-4">Printing Solutions</p>
                    <h1
                        class="reveal d1 font-display font-bold text-5xl md:text-6xl lg:text-7xl leading-[1.05] tracking-tight text-zinc-900 dark:text-white mb-6">
                        Cipta <span class="text-accent">Grafika</span>
                    </h1>
                    <p
                        class="reveal d2 text-lg md:text-xl text-zinc-500 dark:text-zinc-400 font-light leading-relaxed max-w-md mb-10">
                        <strong class="font-medium text-zinc-700 dark:text-zinc-300">Cipta Grafika</strong> menghadirkan
                        berbagai produk cetak A3+ dan Large Format dengan pilihan material, finishing, dan spesifikasi yang
                        dapat disesuaikan dengan kebutuhan Anda.</p>
                    <div class="reveal d3 flex flex-wrap gap-4">
                        <a href="#products"
                            class="shimmer inline-flex items-center gap-2 bg-zinc-900 dark:bg-white text-white dark:text-zinc-900 font-medium px-7 py-3.5 rounded-full hover:bg-zinc-700 dark:hover:bg-zinc-200 transition-colors text-sm">
                            Explore Products
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                        {{-- <a href="#contact" class="inline-flex items-center gap-2 border border-zinc-300 dark:border-zinc-700 text-zinc-700 dark:text-zinc-300 font-medium px-7 py-3.5 rounded-full hover:bg-zinc-50 dark:hover:bg-zinc-900 transition-colors text-sm">Get in touch</a> --}}
                    </div>
                    <div class="reveal d4 flex gap-8 mt-14 pt-8 border-t border-zinc-100 dark:border-zinc-900">
                        <div>
                            <p class="font-display font-bold text-3xl text-zinc-900 dark:text-white">34+</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Products</p>
                        </div>
                        <div>
                            <p class="font-display font-bold text-3xl text-zinc-900 dark:text-white">21+</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Printing Solutions</p>
                        </div>
                        <div>
                            <p class="font-display font-bold text-3xl text-zinc-900 dark:text-white">2</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">Main Categories</p>
                        </div>
                    </div>
                </div>

                <div class="reveal d2 flex justify-center md:justify-end">
                    <div class="relative w-72 h-72 md:w-80 md:h-80 lg:w-96 lg:h-96">
                        <div class="pf w-full h-full rounded-3xl">
                            <img src="{{ asset('images/example-7.png') }}" alt="Eliott — Freelance UI/UX Designer"
                                loading="eager">
                        </div>
                        <div
                            class="absolute -bottom-4 -left-4 bg-accent text-white font-display font-bold text-sm px-4 py-2.5 rounded-2xl shadow-lg">
                            Your Printing Partner</div>
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

                <article
                    class="card-h reveal d2 group rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 hover:border-accent">
                    <div class="pf w-full h-48">
                        <img src="{{ asset('images/example-8.jpg') }}" alt="Finlo Fintech App" loading="lazy">
                    </div>
                    <div class="p-6">
                        <a href="{{ route('user_product_details') }}">
                            <h3 class="font-display font-bold text-xl text-zinc-900 dark:text-white mb-1.5">Kartu Nama</h3>
                        </a>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed mb-4">Kartu nama premium isi 100
                            pcs lengkap dengan box plastik. Pilih bahan, sisi cetak, dan finishing sesuai karakter bisnis
                            Anda.</p>
                        <a href="{{ route('user_product_details') }}"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-zinc-900 dark:text-white nl">Lihat
                            produk →</a>
                    </div>
                </article>
                <article
                    class="card-h reveal d2 group rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 hover:border-accent">
                    <div class="pf w-full h-48">
                        <img src="{{ asset('images/example-10.jpg') }}" alt="Finlo Fintech App" loading="lazy">
                    </div>
                    <div class="p-6">
                        <a href="{{ route('user_product_details') }}">
                            <h3 class="font-display font-bold text-xl text-zinc-900 dark:text-white mb-1.5">Brosur & Flyer
                            </h3>
                        </a>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed mb-4">Cetak brosur dan flyer
                            mulai 5 lembar A3+. Pilih bahan, sisi cetak, laminasi, dan ukuran potong sesuai kebutuhan
                            promosi.</p>
                        <a href="{{ route('user_product_details') }}"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-zinc-900 dark:text-white nl">Lihat
                            produk →</a>
                    </div>
                </article>
                <article
                    class="card-h reveal d2 group rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 hover:border-accent">
                    <div class="pf w-full h-48">
                        <img src="{{ asset('images/example-11.jpg') }}" alt="Finlo Fintech App" loading="lazy">
                    </div>
                    <div class="p-6">
                        <a href="{{ route('user_product_details') }}">
                            <h3 class="font-display font-bold text-xl text-zinc-900 dark:text-white mb-1.5">Sticker Label
                            </h3>
                        </a>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed mb-4">Stiker label custom mulai 5
                            lembar A3+. Pilih bahan, laminasi, dan jenis cutting sesuai kebutuhan kemasan atau promosi.</p>
                        <a href="{{ route('user_product_details') }}"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-zinc-900 dark:text-white nl">Lihat
                            produk →</a>
                    </div>
                </article>
                <article
                    class="card-h reveal d2 group rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 hover:border-accent">
                    <div class="pf w-full h-48">
                        <img src="{{ asset('images/example-12.jpg') }}" alt="Finlo Fintech App" loading="lazy">
                    </div>
                    <div class="p-6">
                        <a href="{{ route('user_product_details') }}">
                            <h3 class="font-display font-bold text-xl text-zinc-900 dark:text-white mb-1.5">Gantungan Kunci
                                Aklirik</h3>
                        </a>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed mb-4">Gantungan kunci akrilik
                            custom untuk souvenir, merchandise, komunitas, dan promosi brand dengan bentuk mengikuti desain.
                        </p>
                        <a href="{{ route('user_product_details') }}"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-zinc-900 dark:text-white nl">Lihat
                            produk →</a>
                    </div>
                </article>
                <article
                    class="card-h reveal d2 group rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 hover:border-accent">
                    <div class="pf w-full h-48">
                        <img src="{{ asset('images/example-13.jpg') }}" alt="Finlo Fintech App" loading="lazy">
                    </div>
                    <div class="p-6">
                        <a href="{{ route('user_product_details') }}">
                            <h3 class="font-display font-bold text-xl text-zinc-900 dark:text-white mb-1.5">Packaging Box
                            </h3>
                        </a>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed mb-4">Packaging box custom yang
                            membantu produk tampil lebih bernilai, rapi, dan konsisten dengan karakter brand.</p>
                        <a href="{{ route('user_product_details') }}"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-zinc-900 dark:text-white nl">Lihat
                            produk →</a>
                    </div>
                </article>
                <article
                    class="card-h reveal d2 group rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800 hover:border-accent">
                    <div class="pf w-full h-48">
                        <img src="{{ asset('images/example-14.jpg') }}" alt="Finlo Fintech App" loading="lazy">
                    </div>
                    <div class="p-6">
                        <a href="{{ route('user_product_details') }}">
                            <h3 class="font-display font-bold text-xl text-zinc-900 dark:text-white mb-1.5">Gantungan Kunci
                                Aklirik</h3>
                        </a>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed mb-4">Gantungan kunci akrilik
                            custom untuk souvenir, merchandise, komunitas, dan promosi brand dengan bentuk mengikuti desain.
                        </p>
                        <a href="{{ route('user_product_details') }}"
                            class="inline-flex items-center gap-1.5 text-sm font-medium text-zinc-900 dark:text-white nl">Lihat
                            produk →</a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- ═══ BLOG ═══ -->
    <section id="blog" class="py-24">
        <div class="max-w-6xl mx-auto px-6">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-14">
                <div>
                    <p class="reveal text-xs font-medium text-accent tracking-widest uppercase mb-3">Pertanyaan Umum</p>
                    <h2 class="reveal d1 font-display font-bold text-4xl md:text-5xl text-zinc-900 dark:text-white">Temukan
                        jawaban untuk pertanyaan umum seputar produk dan layanan Cipta Grafika.</h2>
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-6">
                <div class="flex flex-col gap-6"
                    style="height: auto; display: flex; justify-content: center; align-items: center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="90%" height="400" viewBox="0 0 32 32"
                        fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round"
                        stroke-linejoin="round" class="text-[#16294d] dark:text-[#c2a857]">

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
                                Apa saja produk dan layanan yang tersedia di Cipta Grafika?
                            </span>

                            <span
                                class="faq-icon shrink-0 text-2xl font-light text-accent transition-transform duration-300">
                                +
                            </span>

                        </button>

                        <div class="faq-content grid grid-rows-[0fr] transition-[grid-template-rows] duration-300">
                            <div class="overflow-hidden">
                                <p class="px-6 pb-6 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    Cipta Grafika menyediakan berbagai produk dan layanan
                                    digital printing, large format, serta finishing untuk
                                    memenuhi berbagai kebutuhan cetak.
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
                                Apakah bisa melakukan custom ukuran dan material?
                            </span>

                            <span
                                class="faq-icon shrink-0 text-2xl font-light text-accent transition-transform duration-300">
                                +
                            </span>

                        </button>

                        <div class="faq-content grid grid-rows-[0fr] transition-[grid-template-rows] duration-300">
                            <div class="overflow-hidden">
                                <p class="px-6 pb-6 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    Ya, kami menyediakan berbagai pilihan ukuran, material,
                                    dan finishing yang dapat disesuaikan dengan kebutuhan
                                    dan spesifikasi pesanan Anda.
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
                                Bagaimana cara mendapatkan estimasi harga?
                            </span>

                            <span
                                class="faq-icon shrink-0 text-2xl font-light text-accent transition-transform duration-300">
                                +
                            </span>

                        </button>

                        <div class="faq-content grid grid-rows-[0fr] transition-[grid-template-rows] duration-300">
                            <div class="overflow-hidden">
                                <p class="px-6 pb-6 text-sm text-zinc-500 dark:text-zinc-400 leading-relaxed">
                                    Anda dapat menggunakan katalog produk dan estimator
                                    untuk mendapatkan perkiraan harga berdasarkan produk,
                                    ukuran, material, dan kebutuhan finishing.
                                </p>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
@endsection

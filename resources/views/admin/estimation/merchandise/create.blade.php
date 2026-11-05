@extends('admin_master')

@section('contents')
    <div class="shell">

        <div data-shell-sidebar></div>

        <div class="main">

            <div data-shell-topbar></div>

            <main class="content">

                {{-- HERO --}}
                <section class="hero"
                    style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 24px;">
                    <div class="hero-text" style="flex: 1; min-width: 280px;">
                        <span class="eyebrow" style="color: var(--primary); font-weight: 700; letter-spacing: 0.8px;">
                            ESTIMASI PRODUKSI MERCHANDISE
                        </span>
                        <h1 class="hero-title" style="margin: 6px 0 8px; font-size: 1.75rem; font-weight: 800;">
                            Buat Estimasi Merchandise &amp; Custom Form
                        </h1>
                        <p class="hero-sub" style="margin: 0; color: var(--t-muted); font-size: 13.5px; line-height: 1.5;">
                            Satu halaman terpadu untuk kalkulasi instan pesanan <strong>Tali Lanyard &amp; ID Card</strong>,
                            <strong>Papan Nama Dada (Name Tag)</strong>, <strong>Plakat Akrilik &amp; Kayu</strong>, serta
                            <strong>Buku Nota NCR &amp; HVS</strong>.
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('admin_home') }}" class="btn-back-header"
                            style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-card); color: var(--t-base); text-decoration: none; font-size: 13px; font-weight: 500; transition: all 0.2s;">
                            <i class="bi bi-arrow-left"></i>
                            <span>Kembali</span>
                        </a>
                    </div>
                </section>

                {{-- =========================================================
                    TABS KATEGORI MERCHANDISE (4 KATEGORI UTAMA)
                ========================================================== --}}
                <div class="merch-tab-container" style="margin-bottom: 20px;">
                    <div class="merch-tabs-nav" style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <button type="button" class="merch-tab-btn active" data-tab="lanyard">
                            <span class="tab-icon">🎗️</span>
                            <span class="tab-label">Tali Lanyard &amp; ID Card</span>
                            <span class="tab-badge">24 Tier</span>
                        </button>
                        <button type="button" class="merch-tab-btn" data-tab="nametag">
                            <span class="tab-icon">🏷️</span>
                            <span class="tab-label">Papan Nama Dada (Name Tag)</span>
                            <span class="tab-badge">3 Varian</span>
                        </button>
                        <button type="button" class="merch-tab-btn" data-tab="plakat">
                            <span class="tab-icon">🏆</span>
                            <span class="tab-label">Plakat Akrilik UV &amp; Kayu</span>
                            <span class="tab-badge">13 Tipe</span>
                        </button>
                        <button type="button" class="merch-tab-btn" data-tab="nota">
                            <span class="tab-icon">📑</span>
                            <span class="tab-label">Buku Nota (NCR &amp; HVS)</span>
                            <span class="tab-badge">11 Varian</span>
                        </button>
                    </div>
                </div>

                {{-- =========================================================
                    GRID ESTIMATOR: FORM (KIRI) & SUMMARY (KANAN)
                ========================================================== --}}
                <div class="merch-estimator-grid">

                    {{-- =========================================================
                        KOLOM KIRI: FORM ESTIMASI
                    ========================================================== --}}
                    <div class="merch-form-col">
                        <section class="card merch-form-card">

                            <div class="card-head" style="border-bottom: 1px solid var(--border); padding: 18px 22px; margin-bottom: 0;">
                                <div class="card-title-wrap">
                                    <span class="eyebrow" style="color: var(--primary); font-weight: 700;">
                                        FORMULIR SPESIFIKASI ESTIMASI MERCHANDISE
                                    </span>
                                </div>
                            </div>

                            <form id="merchEstimationForm" onsubmit="event.preventDefault();">
                                @csrf

                                {{-- =========================================================
                                    1. INFORMASI PELANGGAN & LOKASI
                                ========================================================== --}}
                                <div class="form-section"
                                    style="padding: 20px 22px; border-bottom: 1px solid var(--border);">
                                    <div class="form-section-head" style="margin-bottom: 16px;">
                                        <span class="eyebrow"
                                            style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-person-badge"></i> 1. INFORMASI PELANGGAN &amp; LOKASI
                                        </span>
                                    </div>

                                    <div class="form-grid"
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">

                                        {{-- Nama Pelanggan (Select2 Cetakia - Sama seperti Kalender) --}}
                                        <div class="field">
                                            <label for="merch_customer_id" class="field-label">
                                                Nama Pelanggan / Perusahaan (Opsional)
                                            </label>
                                            <select id="merch_customer_id" name="customer_id" class="select select2">
                                                <option value="">-- Pilih Pelanggan (Cetakia) --</option>
                                                @foreach ($customers ?? \App\Support\CetakiaCustomer::all() as $cust)
                                                    <option value="{{ $cust['id'] }}"
                                                        data-code="{{ $cust['customer_code'] }}"
                                                        data-name="{{ $cust['name'] }}">
                                                        {{ $cust['name'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" id="merch_customer_name" name="customer_name" value="">
                                        </div>

                                        {{-- Kontak WhatsApp --}}
                                        <div class="field">
                                            <label for="merch_customer_phone" class="field-label">
                                                No. WhatsApp / Telepon (Opsional)
                                            </label>
                                            <input type="text" id="merch_customer_phone" class="input"
                                                placeholder="Contoh: 081234567890">
                                        </div>

                                        {{-- Cabang Produksi --}}
                                        <div class="field">
                                            <label for="merch_location_id" class="field-label">
                                                Cabang Lokasi Layanan <span class="req">*</span>
                                            </label>
                                            <select id="merch_location_id" class="select select2" required>
                                                @foreach ($locations as $loc)
                                                    <option value="{{ $loc->id }}"
                                                        {{ $loop->first ? 'selected' : '' }}>
                                                        {{ $loc->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                    </div>
                                </div>

                                {{-- =========================================================
                                    2. PANEL SPESIFIKASI DINAMIS (BERDASARKAN TAB AKTIF)
                                ========================================================== --}}

                                {{-- A. PANEL TALI LANYARD & ID CARD --}}
                                <div id="panelLanyard" class="tab-panel" style="display: block; padding: 20px 22px; border-bottom: 1px solid var(--border);">
                                    <div class="form-section-head" style="margin-bottom: 18px;">
                                        <span class="eyebrow"
                                            style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-gear"></i> 2. SPESIFIKASI TALI LANYARD &amp; ID CARD
                                        </span>
                                    </div>

                                    {{-- Lebar Tali --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label">Pilihan Lebar Tali Lanyard <span
                                                class="req">*</span></label>
                                        <div class="choice-cards-grid">
                                            <label class="choice-card active" id="cardLanyardWidth2cm">
                                                <input type="radio" name="lanyard_width" value="2cm" checked
                                                    style="display:none;">
                                                <div class="choice-title">Lebar 2.0 cm</div>
                                                <div class="choice-sub">Standar kantor, instansi, &amp; event populer.
                                                </div>
                                            </label>
                                            <label class="choice-card" id="cardLanyardWidth25cm">
                                                <input type="radio" name="lanyard_width" value="2.5cm"
                                                    style="display:none;">
                                                <div class="choice-title">Lebar 2.5 cm</div>
                                                <div class="choice-sub">Ukuran lebar premium, desain teks &amp; logo lebih
                                                    jelas.</div>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Kelengkapan Paket --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label">Kelengkapan Paket Cetak <span
                                                class="req">*</span></label>
                                        <div class="choice-cards-grid">
                                            <label class="choice-card active" id="cardLanyardPkgSet">
                                                <input type="radio" name="lanyard_package" value="set" checked
                                                    style="display:none;">
                                                <div class="choice-title">1 Set Lengkap (Rekomendasi)</div>
                                                <div class="choice-sub">Termasuk Tali Lanyard + Casing Holder + ID Card PVC
                                                    Tebal. Siap pakai.</div>
                                            </label>
                                            <label class="choice-card" id="cardLanyardPkgTali">
                                                <input type="radio" name="lanyard_package" value="tali"
                                                    style="display:none;">
                                                <div class="choice-title">Hanya Tali Saja</div>
                                                <div class="choice-sub">Tali Lanyard Printing 2 Sisi + Kait Besi Kokoh +
                                                    Stopper Putar.</div>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Kuantitas & Quick Select --}}
                                    <div class="field">
                                        <label class="field-label">Jumlah Kuantitas Pesanan (Pcs) <span
                                                class="req">*</span></label>
                                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                            <div style="display: inline-flex; align-items: center;">
                                                <button type="button" class="qty-btn" id="btnLanyardQtyMinus">-</button>
                                                <input type="number" id="lanyardQtyInput" class="input qty-input"
                                                    value="25" min="1" step="1">
                                                <button type="button" class="qty-btn" id="btnLanyardQtyPlus">+</button>
                                            </div>
                                            <div class="quick-qty-group">
                                                <button type="button" class="quick-qty-btn" data-qty="5">5
                                                    pcs</button>
                                                <button type="button" class="quick-qty-btn" data-qty="10">10
                                                    pcs</button>
                                                <button type="button" class="quick-qty-btn active" data-qty="25">25
                                                    pcs</button>
                                                <button type="button" class="quick-qty-btn" data-qty="50">50
                                                    pcs</button>
                                                <button type="button" class="quick-qty-btn" data-qty="100">100
                                                    pcs</button>
                                                <button type="button" class="quick-qty-btn" data-qty="200">&gt; 100
                                                    pcs</button>
                                            </div>
                                        </div>
                                        <small class="field-help" style="margin-top: 2px;">
                                            Harga otomatis berpindah ke tier kuantitas resmi (1-4 pcs, 5-12 pcs, 13-25
                                            pcs, 26-50 pcs, 51-100 pcs, &gt;100 pcs).
                                        </small>
                                    </div>
                                </div>

                                {{-- B. PANEL PAPAN NAMA DADA (NAME TAG) --}}
                                <div id="panelNametag" class="tab-panel" style="display: none; padding: 20px 22px; border-bottom: 1px solid var(--border);">
                                    <div class="form-section-head" style="margin-bottom: 18px;">
                                        <span class="eyebrow"
                                            style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-tag"></i> 2. SPESIFIKASI PAPAN NAMA DADA (NAME TAG)
                                        </span>
                                    </div>

                                    {{-- Tipe Finishing Name Tag --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label">Varian Lapisan &amp; Finishing Papan Nama <span
                                                class="req">*</span></label>
                                        <div class="choice-cards-grid">
                                            <label class="choice-card" id="cardNametagBiasa">
                                                <input type="radio" name="nametag_type" value="biasa"
                                                    style="display:none;">
                                                <div class="choice-title">Papan Nama Biasa</div>
                                                <div class="choice-sub">Akrilik standar tanpa lapisan resin cembung.</div>
                                                <div class="choice-price">Rp 25.000 / pcs</div>
                                            </label>
                                            <label class="choice-card active" id="cardNametagResin">
                                                <input type="radio" name="nametag_type" value="resin" checked
                                                    style="display:none;">
                                                <div class="choice-title">Papan Nama Resin</div>
                                                <div class="choice-sub">Lapisan resin Lycal cembung jernih, mengkilap &amp;
                                                    tahan gores.</div>
                                                <div class="choice-price">Rp 35.000 / pcs</div>
                                            </label>
                                            <label class="choice-card" id="cardNametagResinEmas">
                                                <input type="radio" name="nametag_type" value="resin_emas"
                                                    style="display:none;">
                                                <div class="choice-title">Papan Nama Resin Emas</div>
                                                <div class="choice-sub">Base plat kuningan / logam emas mewah + resin
                                                    cembung.</div>
                                                <div class="choice-price">Rp 35.000 / pcs</div>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Jenis Pengait --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label">Model Pengait Belakang <span class="req">*</span></label>
                                        <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                                            <label class="choice-pill active" id="pillFastenerMagnet">
                                                <input type="radio" name="nametag_fastener" value="magnet" checked>
                                                <i class="bi bi-magnet me-1"></i> Magnet Neodymium (Kuat &amp; Tidak
                                                Merusak Baju)
                                            </label>
                                            <label class="choice-pill" id="pillFastenerPeniti">
                                                <input type="radio" name="nametag_fastener" value="peniti">
                                                <i class="bi bi-pin-angle me-1"></i> Peniti Stainless Standar
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Kuantitas Name Tag --}}
                                    <div class="field">
                                        <label class="field-label">Jumlah Pesanan (Pcs) <span class="req">*</span></label>
                                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                            <div style="display: inline-flex; align-items: center;">
                                                <button type="button" class="qty-btn" id="btnNametagQtyMinus">-</button>
                                                <input type="number" id="nametagQtyInput" class="input qty-input"
                                                    value="10" min="1" step="1">
                                                <button type="button" class="qty-btn" id="btnNametagQtyPlus">+</button>
                                            </div>
                                            <div class="quick-qty-group">
                                                <button type="button" class="quick-qty-btn" data-qty="1">1
                                                    pcs</button>
                                                <button type="button" class="quick-qty-btn" data-qty="5">5
                                                    pcs</button>
                                                <button type="button" class="quick-qty-btn active" data-qty="10">10
                                                    pcs</button>
                                                <button type="button" class="quick-qty-btn" data-qty="25">25
                                                    pcs</button>
                                                <button type="button" class="quick-qty-btn" data-qty="50">50
                                                    pcs</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- C. PANEL PLAKAT AKRILIK UV & KAYU --}}
                                <div id="panelPlakat" class="tab-panel" style="display: none; padding: 20px 22px; border-bottom: 1px solid var(--border);">
                                    <div class="form-section-head" style="margin-bottom: 18px;">
                                        <span class="eyebrow"
                                            style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-trophy"></i> 2. SPESIFIKASI PLAKAT AKRILIK UV &amp; KAYU
                                        </span>
                                    </div>

                                    {{-- Bahan Plakat --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label">Pilihan Bahan Dasar Plakat <span
                                                class="req">*</span></label>
                                        <div class="choice-cards-grid">
                                            <label class="choice-card active" id="cardPlakatMatAkrilik">
                                                <input type="radio" name="plakat_material" value="akrilik" checked
                                                    style="display:none;">
                                                <div class="choice-title">Plakat Akrilik UV (20x15 cm)</div>
                                                <div class="choice-sub">Cetak flatbed UV Resolusi Tinggi, tajam &amp; tahan
                                                    luntur.</div>
                                            </label>
                                            <label class="choice-card" id="cardPlakatMatKayu">
                                                <input type="radio" name="plakat_material" value="kayu"
                                                    style="display:none;">
                                                <div class="choice-title">Plakat Kayu (+ Box Beludru)</div>
                                                <div class="choice-sub">Kayu solid ukir mewah, pelat logam
                                                    kuningan/akrilik, bonus box beludru.</div>
                                                <div class="choice-price">Rp 180.000 / pcs</div>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Sub-opsi Khusus Akrilik --}}
                                    <div id="plakatAkrilikOptions"
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 18px; margin-bottom: 20px;">

                                        {{-- Ketebalan Akrilik --}}
                                        <div class="field">
                                            <label class="field-label">Ketebalan Akrilik <span class="req">*</span></label>
                                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                                <label class="choice-pill" id="pillPlakatThk2mm">
                                                    <input type="radio" name="plakat_thickness" value="2mm"> 2 mm
                                                </label>
                                                <label class="choice-pill active" id="pillPlakatThk3mm">
                                                    <input type="radio" name="plakat_thickness" value="3mm" checked>
                                                    3 mm
                                                </label>
                                                <label class="choice-pill" id="pillPlakatThk5mm">
                                                    <input type="radio" name="plakat_thickness" value="5mm"> 5 mm
                                                </label>
                                                <label class="choice-pill" id="pillPlakatThk1cm">
                                                    <input type="radio" name="plakat_thickness" value="1cm"> 1 cm
                                                    (10mm)
                                                </label>
                                            </div>
                                        </div>

                                        {{-- Jenis Dudukan (Base) --}}
                                        <div class="field">
                                            <label class="field-label">Jenis Kaki / Dudukan <span
                                                    class="req">*</span></label>
                                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                                <label class="choice-pill active" id="pillPlakatBaseAkrilik">
                                                    <input type="radio" name="plakat_base" value="akrilik" checked>
                                                    Dudukan Akrilik Bening
                                                </label>
                                                <label class="choice-pill" id="pillPlakatBaseHitam">
                                                    <input type="radio" name="plakat_base" value="hitam"> Dudukan
                                                    Hitam Elegan
                                                </label>
                                            </div>
                                        </div>

                                        {{-- Kemasan Box Beludru --}}
                                        <div class="field">
                                            <label class="field-label">Kemasan Kotak (Box Beludru) <span
                                                    class="req">*</span></label>
                                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                                <label class="choice-pill active" id="pillPlakatWithBox">
                                                    <input type="radio" name="plakat_box" value="box" checked>
                                                    <i class="bi bi-box2 me-1"></i> Termasuk Box Beludru
                                                </label>
                                                <label class="choice-pill" id="pillPlakatNoBox">
                                                    <input type="radio" name="plakat_box" value="nobox">
                                                    Tanpa Box (Plakat Saja)
                                                </label>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- Kuantitas & Biaya Desain --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label">Jumlah Pesanan (Pcs) <span class="req">*</span></label>
                                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                            <div style="display: inline-flex; align-items: center;">
                                                <button type="button" class="qty-btn" id="btnPlakatQtyMinus">-</button>
                                                <input type="number" id="plakatQtyInput" class="input qty-input"
                                                    value="1" min="1" step="1">
                                                <button type="button" class="qty-btn" id="btnPlakatQtyPlus">+</button>
                                            </div>
                                            <div class="quick-qty-group">
                                                <button type="button" class="quick-qty-btn active" data-qty="1">1
                                                    pcs</button>
                                                <button type="button" class="quick-qty-btn" data-qty="3">3
                                                    pcs</button>
                                                <button type="button" class="quick-qty-btn" data-qty="5">5
                                                    pcs</button>
                                                <button type="button" class="quick-qty-btn" data-qty="10">10
                                                    pcs</button>
                                                <button type="button" class="quick-qty-btn" data-qty="20">20
                                                    pcs</button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Opsi Desain --}}
                                    <div class="field"
                                        style="padding: 13px 16px; border-radius: 10px; background: var(--bg-muted); border: 1px solid var(--border);">
                                        <label
                                            style="display: flex; align-items: center; gap: 12px; cursor: pointer; margin: 0; user-select: none;">
                                            <input type="checkbox" id="checkPlakatDesign"
                                                style="width: 18px; height: 18px; accent-color: var(--primary); flex-shrink: 0;">
                                            <div>
                                                <span style="font-weight: 600; color: var(--t-base); font-size: 13px;">Tambah Jasa Desain /
                                                    Setting Plakat (+ Rp 35.000)</span>
                                                <small style="display: block; color: var(--t-muted); font-size: 11.5px; margin-top: 2px;">
                                                    Centang jika pemesan belum memiliki file vector/siap cetak dan
                                                    membutuhkan tim desainer membuat layout plakat.
                                                </small>
                                            </div>
                                        </label>
                                    </div>

                                </div>

                                {{-- D. PANEL BUKU NOTA (NCR & HVS) --}}
                                <div id="panelNota" class="tab-panel" style="display: none; padding: 20px 22px; border-bottom: 1px solid var(--border);">
                                    <div class="form-section-head" style="margin-bottom: 18px;">
                                        <span class="eyebrow"
                                            style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-journal-text"></i> 2. SPESIFIKASI BUKU NOTA (NCR &amp; HVS)
                                        </span>
                                    </div>

                                    {{-- Jenis Kertas --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label">Jenis Kertas Nota <span class="req">*</span></label>
                                        <div class="choice-cards-grid">
                                            <label class="choice-card active" id="cardNotaPaperNcr">
                                                <input type="radio" name="nota_paper" value="ncr" checked
                                                    style="display:none;">
                                                <div class="choice-title">Kertas NCR Carbonless (50 Set)</div>
                                                <div class="choice-sub">Tembus otomatis tanpa kertas karbon tambahan. Isi
                                                    50 set per buku.</div>
                                            </label>
                                            <label class="choice-card" id="cardNotaPaperHvs">
                                                <input type="radio" name="nota_paper" value="hvs"
                                                    style="display:none;">
                                                <div class="choice-title">Kertas HVS Putih (100 Sheet)</div>
                                                <div class="choice-sub">Kertas HVS putih standar 1 ply, tebal &amp; bersih.
                                                    Isi 100 lembar per buku.</div>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="form-grid"
                                        style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 20px;">

                                        {{-- Jumlah Rangkap (Ply) --}}
                                        <div class="field">
                                            <label class="field-label">Jumlah Rangkap (Ply) <span
                                                    class="req">*</span></label>
                                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                                <label class="choice-pill" id="pillNotaPly1" style="display: none;">
                                                    <input type="radio" name="nota_ply" value="1"> 1 Ply (HVS)
                                                </label>
                                                <label class="choice-pill active" id="pillNotaPly2">
                                                    <input type="radio" name="nota_ply" value="2" checked> 2 Ply
                                                    (2 Rangkap)
                                                </label>
                                                <label class="choice-pill" id="pillNotaPly3">
                                                    <input type="radio" name="nota_ply" value="3"> 3 Ply (3
                                                    Rangkap)
                                                </label>
                                                <label class="choice-pill" id="pillNotaPly4">
                                                    <input type="radio" name="nota_ply" value="4"> 4 Ply (4
                                                    Rangkap)
                                                </label>
                                            </div>
                                        </div>

                                        {{-- Jumlah Warna Tinta Cetak --}}
                                        <div class="field">
                                            <label class="field-label">Jumlah Warna Tinta Cetak <span
                                                    class="req">*</span></label>
                                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                                <label class="choice-pill active" id="pillNotaInk1">
                                                    <input type="radio" name="nota_ink" value="1" checked> 1 Warna
                                                    Tinta
                                                </label>
                                                <label class="choice-pill" id="pillNotaInk2">
                                                    <input type="radio" name="nota_ink" value="2"> 2 Warna Tinta
                                                </label>
                                                <label class="choice-pill" id="pillNotaInk3">
                                                    <input type="radio" name="nota_ink" value="3"> 3 Warna Tinta
                                                </label>
                                            </div>
                                        </div>

                                        {{-- Ukuran Buku Jadi --}}
                                        <div class="field">
                                            <label class="field-label" for="nota_size_select">Ukuran Buku Jadi (Per Paket Rim) <span
                                                    class="req">*</span></label>
                                            <select id="nota_size_select" class="select select2">
                                                <option value="1/4" data-books="40" selected>1/4 Folio (10.5 x 16.5 cm)
                                                    - Dapat 40 Buku / rim</option>
                                                <option value="1/3" data-books="30">1/3 Folio (10.8 x 21.5 cm) - Dapat
                                                    30 Buku / rim</option>
                                                <option value="1/2" data-books="20">1/2 Folio (16.5 x 21.5 cm) - Dapat
                                                    20 Buku / rim</option>
                                                <option value="1" data-books="10">1 Folio Penuh (21.5 x 33 cm) -
                                                    Dapat 10 Buku / rim</option>
                                            </select>
                                        </div>

                                    </div>

                                    {{-- Kuantitas Paket Rim --}}
                                    <div class="field" style="margin-bottom: 20px;">
                                        <label class="field-label">Jumlah Paket Cetak (Rim Plano Folio) <span
                                                class="req">*</span></label>
                                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                            <div style="display: inline-flex; align-items: center;">
                                                <button type="button" class="qty-btn" id="btnNotaQtyMinus">-</button>
                                                <input type="number" id="notaQtyInput" class="input qty-input"
                                                    value="1" min="1" step="1">
                                                <button type="button" class="qty-btn" id="btnNotaQtyPlus">+</button>
                                            </div>
                                            <div class="quick-qty-group">
                                                <button type="button" class="quick-qty-btn active" data-qty="1">1 Rim
                                                    (Paket)</button>
                                                <button type="button" class="quick-qty-btn" data-qty="2">2
                                                    Rim</button>
                                                <button type="button" class="quick-qty-btn" data-qty="3">3
                                                    Rim</button>
                                                <button type="button" class="quick-qty-btn" data-qty="5">5
                                                    Rim</button>
                                                <button type="button" class="quick-qty-btn" data-qty="10">10
                                                    Rim</button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Opsi Nomorator & Jasa Desain Nota --}}
                                    <div style="display: flex; flex-direction: column; gap: 10px;">
                                        <div
                                            style="padding: 13px 16px; border-radius: 10px; background: var(--bg-muted); border: 1px solid var(--border);">
                                            <label
                                                style="display: flex; align-items: center; gap: 12px; cursor: pointer; margin: 0; user-select: none;">
                                                <input type="checkbox" id="checkNotaNumerator" checked
                                                    style="width: 18px; height: 18px; accent-color: var(--primary); flex-shrink: 0;">
                                                <div>
                                                    <span style="font-weight: 600; color: var(--t-base); font-size: 13px;">Tambah Nomorator
                                                        Otomatis 6 Digit (Gratis / Standar)</span>
                                                    <small
                                                        style="display: block; color: var(--t-muted); font-size: 11.5px; margin-top: 2px;">
                                                        Penomoran berurutan rapi di pojok nota (misal: No. 000001 s.d
                                                        selesai).
                                                    </small>
                                                </div>
                                            </label>
                                        </div>

                                        <div
                                            style="padding: 13px 16px; border-radius: 10px; background: var(--bg-muted); border: 1px solid var(--border);">
                                            <label
                                                style="display: flex; align-items: center; gap: 12px; cursor: pointer; margin: 0; user-select: none;">
                                                <input type="checkbox" id="checkNotaDesign"
                                                    style="width: 18px; height: 18px; accent-color: var(--primary); flex-shrink: 0;">
                                                <div>
                                                    <span style="font-weight: 600; color: var(--t-base); font-size: 13px;">Jasa Setting /
                                                        Pembuatan Desain Form Nota (+ Rp 30.000)</span>
                                                    <small
                                                        style="display: block; color: var(--t-muted); font-size: 11.5px; margin-top: 2px;">
                                                        Centang jika pemesan belum memiliki file layout nota dan butuh
                                                        didesainkan oleh tim kami.
                                                    </small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>

                                </div>

                                {{-- =========================================================
                                    3. CATATAN KHUSUS ESTIMASI
                                ========================================================== --}}
                                <div class="form-section" style="padding: 20px 22px;">
                                    <div class="form-section-head" style="margin-bottom: 14px;">
                                        <span class="eyebrow"
                                            style="font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-chat-left-text"></i> 3. CATATAN KHUSUS / KETERANGAN
                                        </span>
                                    </div>
                                    <div class="field">
                                        <label class="field-label" for="merch_custom_notes">
                                            Catatan Produksi / Penawaran (Opsional)
                                        </label>
                                        <textarea id="merch_custom_notes" class="textarea" rows="2"
                                            placeholder="Catatan tambahan (contoh: warna tali biru BCA, logo di lanyard diulang setiap 5 cm, nomorator mulai dari 001501)..."
                                            style="width: 100%; min-height: 76px;"></textarea>
                                    </div>
                                </div>

                                {{-- ACTION BUTTONS: KEMBALI & RESET FORM (SEPERTI KALENDER) --}}
                                <div class="card-foot"
                                    style="padding: 16px 22px; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: var(--bg-muted); border-radius: 0 0 14px 14px;">
                                    <a href="{{ route('admin_home') }}" class="btn-secondary-merch">
                                        <i class="bi bi-arrow-left"></i>
                                        <span>Kembali</span>
                                    </a>
                                    <button type="button" id="btnResetMerchForm" class="btn-secondary-merch">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                        <span>Reset Form</span>
                                    </button>
                                </div>

                            </form>

                        </section>
                    </div>

                    {{-- =========================================================
                        KOLOM KANAN: LIVE SUMMARY HARGA (STICKY)
                    ========================================================== --}}
                    <div class="merch-summary-col">
                        <section class="card merch-sticky-card">

                            {{-- CARD HEAD DENGAN STYLE SELARAS --}}
                            <div class="card-head"
                                style="background: var(--bg-card); border-bottom: 1px solid var(--border); padding: 18px 22px; border-radius: 14px 14px 0 0; margin-bottom: 0;">
                                <div
                                    style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; width: 100%;">
                                    <div>
                                        <span class="eyebrow"
                                            style="color: var(--primary); font-size: 11px; font-weight: 700; letter-spacing: 0.6px; display: block;">
                                            HASIL KALKULASI ESTIMASI
                                        </span>
                                        <h3
                                            style="font-size: 1.15rem; font-weight: 700; color: var(--t-base); margin: 4px 0 0;">
                                            Ringkasan Estimasi
                                        </h3>
                                    </div>
                                    <span id="badgeMerchStatus"
                                        style="display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; background: rgba(34, 197, 94, 0.15); color: #22c55e; font-size: 11px; font-weight: 600; flex-shrink: 0;">
                                        <i class="bi bi-check-circle-fill"></i> Terhitung
                                    </span>
                                </div>
                            </div>

                            <div class="card-body" style="padding: 20px 22px;">
                                {{-- Item Spesifikasi Terpilih --}}
                                <div
                                    style="margin-bottom: 16px; padding-bottom: 14px; border-bottom: 1px dashed var(--border);">
                                    <span id="summaryMerchCategoryBadge" class="summary-cat-badge">
                                        TALI LANYARD
                                    </span>
                                    <h3 id="summaryMerchProductTitle"
                                        style="font-size: 15px; font-weight: 700; color: var(--t-base); margin: 0 0 6px;">
                                        Lanyard 2.0 cm (1 Set Lengkap)
                                    </h3>
                                    <p id="summaryMerchSpecs"
                                        style="font-size: 12.5px; color: var(--t-muted); margin: 0; line-height: 1.4;">
                                        Paket: Tali Lanyard + Holder + Kartu PVC
                                    </p>
                                </div>

                                {{-- Rincian Biaya & Subtotal --}}
                                <div class="summary-breakdown-rows" style="font-size: 13px;">
                                    <div class="summary-row">
                                        <span class="row-label">Jumlah Pesanan:</span>
                                        <span id="summaryMerchQty" class="row-val font-semibold">25 pcs</span>
                                    </div>
                                    <div class="summary-row">
                                        <span class="row-label">Tier / Varian Aktif:</span>
                                        <span id="summaryMerchTier" class="row-val"
                                            style="color: var(--primary); font-weight: 600;">Tier: 13–25 pcs</span>
                                    </div>
                                    <div class="summary-row">
                                        <span id="summaryMerchUnitLabel" class="row-label">Harga Satuan:</span>
                                        <span id="summaryMerchUnitPrice" class="row-val">Rp 23.000 / pcs</span>
                                    </div>
                                    <div id="rowSummaryExtraFee" class="summary-row" style="display: none;">
                                        <span id="summaryMerchExtraLabel" class="row-label">Jasa Tambahan:</span>
                                        <span id="summaryMerchExtraFee" class="row-val">Rp 0</span>
                                    </div>
                                </div>

                                {{-- GRAND TOTAL BOX --}}
                                <div class="grand-total-box"
                                    style="margin: 18px 0; padding: 14px 16px; border-radius: 10px; background: var(--bg-muted); border: 1px solid var(--border); text-align: center;">
                                    <span
                                        style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--t-muted); display: block;">
                                        TOTAL ESTIMASI HARGA
                                    </span>
                                    <div id="summaryMerchGrandTotal"
                                        style="font-size: 1.85rem; font-weight: 800; color: var(--primary); margin: 4px 0 2px;">
                                        Rp 575.000
                                    </div>
                                    <small id="summaryMerchPerUnit"
                                        style="font-size: 12px; color: #10b981; font-weight: 600;">
                                        (Rp 23.000 / pcs)
                                    </small>
                                </div>

                                {{-- TOMBOL AKSI ESTIMASI --}}
                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                    {{-- Tombol Detail Estimasi (Membuka Modal Pop-up Rincian SPH) --}}
                                    <button type="button" id="btnOpenDetailEstimasiModal" class="btn-detail-estimation">
                                        <i class="bi bi-file-earmark-spreadsheet"></i>
                                        <span>Detail Estimasi</span>
                                    </button>

                                    {{-- Tombol Salin WA --}}
                                    <button type="button" id="btnCopyWaQuotation" class="btn-action-wa">
                                        <i class="bi bi-whatsapp"></i>
                                        <span>Salin Penawaran WhatsApp</span>
                                    </button>
                                </div>
                            </div>
                        </section>
                    </div>

                </div>

                {{-- =========================================================
                    BAGIAN BAWAH: MATRIKS DAFTAR HARGA / PRICELIST LENGKAP
                ========================================================== --}}
                <div class="merch-matrix-col" style="margin-top: 24px;">
                    <section class="card merch-matrix-card">
                        <div class="card-head"
                            style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; padding: 18px 22px; margin-bottom: 0; border-bottom: 1px solid var(--border);">
                            <div>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span class="eyebrow" style="color: var(--primary); font-weight: 700;">
                                        TABEL DAFTAR HARGA &amp; PRICELIST RESMI
                                    </span>
                                    <span id="matrixCategoryIndicatorBadge" class="badge-cat-indicator">
                                        Lanyard (24 Data)
                                    </span>
                                </div>
                                <h3 id="matrixHeaderTitle"
                                    style="font-size: 1.15rem; font-weight: 700; margin: 4px 0 0; color: var(--t-base);">
                                    Daftar Harga Tali Lanyard &amp; ID Card
                                </h3>
                                <p id="matrixHeaderSub"
                                    style="margin: 2px 0 0; font-size: 12.5px; color: var(--t-muted);">
                                    Tabel interaktif harga resmi. Klik <strong>"Pilih"</strong> pada baris tabel untuk
                                    langsung menerapkan spesifikasi ke formulir estimasi di atas.
                                </p>
                            </div>

                            {{-- TOOLBAR: CARI & ENTRIES PER PAGE --}}
                            <div class="matrix-toolbar-wrap"
                                style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                <div class="select2-sm-wrap" style="display: flex; align-items: center; gap: 6px;">
                                    <span style="font-size: 12px; color: var(--t-muted); font-weight: 500;">Baris:</span>
                                    <select id="merchMatrixPageSize" class="select select2 select-sm"
                                        style="width: 85px;">
                                        <option value="5">5</option>
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="all">Semua</option>
                                    </select>
                                </div>

                                <div class="matrix-search-box" style="position: relative; min-width: 240px;">
                                    <input type="text" id="merchMatrixSearch" class="input"
                                        placeholder="Cari varian, ketebalan, harga..."
                                        style="padding-left: 34px; padding-right: 30px; font-size: 12.5px; height: 36px; border-radius: 8px;">
                                    <i class="bi bi-search"
                                        style="position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: var(--t-muted); font-size: 12px;"></i>
                                    <button type="button" id="merchMatrixSearchClear"
                                        style="display: none; position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: transparent; border: none; font-size: 14px; color: var(--t-muted); cursor: pointer; padding: 2px 4px;">&times;</button>
                                </div>
                            </div>
                        </div>

                        <div class="card-body" style="padding: 0;">
                            <div class="table-responsive" style="overflow-x: auto; max-width: 100%;">
                                <table class="table"
                                    style="width: 100%; border-collapse: collapse; text-align: left; font-size: 13px;">
                                    <thead id="merchMatrixTableHead"
                                        style="background: var(--bg-muted); border-bottom: 2px solid var(--border);">
                                        {{-- Header di-render dinamis via JS --}}
                                    </thead>
                                    <tbody id="merchMatrixTableBody">
                                        {{-- Baris di-render dinamis via JS --}}
                                    </tbody>
                                </table>
                            </div>

                            {{-- PAGINATION CONTROLS --}}
                            <div id="merchMatrixPaginationWrap"
                                style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; padding: 14px 20px; border-top: 1px solid var(--border); background: var(--bg-card); border-radius: 0 0 12px 12px;">
                                <div id="merchMatrixPageInfo" style="font-size: 12.5px; color: var(--t-muted);">
                                    Menampilkan 0 dari 0 data
                                </div>
                                <div id="merchMatrixPagination" class="pagination-btn-group"
                                    style="display: flex; gap: 6px; align-items: center;">
                                    {{-- Tombol pagination dinamis --}}
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

            </main>

            <div data-shell-footer></div>
        </div>

    </div>

    {{-- =========================================================
        MODAL POPUP: DETAIL RINCIAN ESTIMASI & SURAT PENAWARAN (SPH)
    ========================================================== --}}
    <div class="modal-overlay" id="modalMerchandiseDetail">
        <div class="modal modal-content-custom" style="max-width: 820px;">

            {{-- MODAL HEAD --}}
            <div class="modal-head-custom">
                <div>
                    <span class="eyebrow" style="color: var(--primary); font-weight: 700;">PREVIEW PENAWARAN &amp; DETAIL
                        ESTIMASI</span>
                    <h3 class="modal-title-custom">Detail Estimasi Merchandise &amp; Custom</h3>
                </div>
                <button type="button" id="btnCloseDetailModal" class="modal-close-btn" aria-label="Tutup">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            {{-- MODAL BODY (PRINTABLE AREA) --}}
            <div id="printableMerchArea" style="padding: 24px; overflow-y: auto; flex: 1;">
                <div class="printable-header">
                    <div>
                        <h2 class="printable-company-name">CIPTA GRAFIKA</h2>
                        <div style="font-size: 12px; color: var(--t-muted);">Printing Management System &middot; Estimator
                            Merchandise &amp; Custom</div>
                    </div>
                    <div style="text-align: right; font-size: 12px; color: var(--t-muted);">
                        <div>Tanggal: <strong id="modalQuoteDate">{{ date('d/m/Y') }}</strong></div>
                        <div>Berlaku s/d: <strong id="modalQuoteExpired">{{ date('d/m/Y', strtotime('+7 days')) }}</strong>
                        </div>
                        <div>Status: <strong style="color: #22c55e;">Estimasi Resmi</strong></div>
                    </div>
                </div>

                {{-- Customer & Production Info Grid --}}
                <div class="printable-info-grid">
                    <div class="printable-info-box">
                        <div
                            style="font-weight: 700; color: var(--t-base); margin-bottom: 6px; display: flex; align-items: center; gap: 5px;">
                            <i class="bi bi-person-fill"></i> INFORMASI PELANGGAN:
                        </div>
                        <div>Nama: <strong id="modalCustomerName">-</strong></div>
                        <div>Telepon / WA: <strong id="modalCustomerPhone">-</strong></div>
                        <div>Cabang / Lokasi: <strong id="modalCustomerLocation">-</strong></div>
                        <div id="modalNotesWrap"
                            style="display: none; margin-top: 6px; padding-top: 6px; border-top: 1px dashed var(--border);">
                            Catatan: <strong id="modalCustomerNotes">-</strong>
                        </div>
                    </div>

                    <div class="printable-info-box">
                        <div
                            style="font-weight: 700; color: var(--t-base); margin-bottom: 6px; display: flex; align-items: center; gap: 5px;">
                            <i class="bi bi-gear-fill"></i> SPESIFIKASI PRODUKSI:
                        </div>
                        <div>Kategori: <strong id="modalCategoryName">-</strong></div>
                        <div>Produk: <strong id="modalProductTitle">-</strong></div>
                        <div>Kuantitas: <strong id="modalQtyDisplay">-</strong></div>
                        <div>Spesifikasi: <strong id="modalSpecsDetail">-</strong></div>
                    </div>
                </div>

                {{-- Table Rincian Biaya Penawaran --}}
                <table class="printable-table">
                    <thead>
                        <tr>
                            <th style="padding: 10px 12px; text-align: left;">Item Deskripsi</th>
                            <th style="padding: 10px 12px; text-align: center;">Jumlah / Satuan</th>
                            <th style="padding: 10px 12px; text-align: right;">Harga Satuan</th>
                            <th style="padding: 10px 12px; text-align: right;">Total Harga</th>
                        </tr>
                    </thead>
                    <tbody id="modalQuoteTableBody">
                        <tr>
                            <td style="padding: 12px;">
                                <strong id="modalTableItemName">Produk Merchandise</strong>
                                <div id="modalTableItemSpecs"
                                    style="font-size: 11.5px; color: var(--t-muted); margin-top: 2px;">-</div>
                            </td>
                            <td id="modalTableItemQty" style="padding: 12px; text-align: center;">-</td>
                            <td id="modalTableItemUnitPrice" style="padding: 12px; text-align: right;">-</td>
                            <td id="modalTableItemSubtotal" style="padding: 12px; text-align: right; font-weight: 700;">-
                            </td>
                        </tr>
                        <tr id="modalTableExtraRow" style="display: none;">
                            <td style="padding: 10px 12px;">
                                <strong id="modalTableExtraLabel">Jasa Tambahan</strong>
                            </td>
                            <td style="padding: 10px 12px; text-align: center;">1 Paket</td>
                            <td id="modalTableExtraUnitPrice" style="padding: 10px 12px; text-align: right;">-</td>
                            <td id="modalTableExtraSubtotal"
                                style="padding: 10px 12px; text-align: right; font-weight: 700;">-</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" style="padding: 14px 12px; text-align: right; text-transform: uppercase;">
                                TOTAL ESTIMASI HARGA:
                            </th>
                            <th id="modalGrandTotal"
                                style="padding: 14px 12px; text-align: right; font-weight: 800; color: var(--primary); font-size: 1.25rem;">
                                Rp 0
                            </th>
                        </tr>
                    </tfoot>
                </table>

                <div
                    style="font-size: 11px; color: var(--t-muted); border-top: 1px dashed var(--border); padding-top: 10px; line-height: 1.5;">
                    * Penawaran resmi berlaku selama 7 hari kalender dari tanggal diterbitkan. Harga sudah termasuk proses
                    cetak terstandar dan quality control Cipta Grafika.
                </div>
            </div>

            {{-- MODAL FOOT --}}
            <div class="modal-foot-custom">
                <button type="button" id="btnCancelDetailModal" class="btn btn-secondary-custom"
                    style="padding: 9px 16px; border-radius: 8px; border: 1px solid var(--border); background: var(--bg-card); color: var(--t-base); font-size: 13px; font-weight: 600; cursor: pointer;">
                    Tutup
                </button>
                <button type="button" id="btnModalCopyWa"
                    style="display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 8px; border: 1px solid #22c55e; background: rgba(34, 197, 94, 0.12); color: #22c55e; font-size: 13px; font-weight: 600; cursor: pointer;">
                    <i class="bi bi-whatsapp"></i> Salin WhatsApp
                </button>
                <button type="button" id="btnModalPrint" class="btn btn--primary"
                    style="display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: 8px; background: var(--primary); color: #ffffff; border: none; font-size: 13px; font-weight: 700; cursor: pointer;">
                    <i class="bi bi-printer"></i> Cetak SPH / PDF
                </button>
            </div>

        </div>
    </div>

    {{-- =========================================================
        CUSTOM STYLES (THEME-AWARE, HARMONIZED WITH CALENDAR ESTIMATOR)
    ========================================================== --}}
    <style>
        /* =========================================================
           LAYOUT, OVERFLOW & TOPBAR STACKING PROTECTION
        ========================================================== */
        .main,
        .content,
        .merch-form-col,
        .merch-summary-col,
        .merch-matrix-col {
            min-width: 0;
            max-width: 100%;
        }

        /* Ensure topbar always stays above sticky summary card on scroll */
        .d-topbar {
            z-index: 40;
        }

        .merch-estimator-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.35fr) minmax(350px, 0.85fr);
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 1080px) {
            .merch-estimator-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }
        }

        /* Prevent double padding on sectioned cards (.card has padding: 22px in style.css) */
        .merch-form-card,
        .merch-sticky-card,
        .merch-matrix-card {
            padding: 0;
        }

        /* ---------------------------------------------------------
           FORM FIELDS, LABELS & SECONDARY BUTTONS (MATCHING CALENDAR)
        --------------------------------------------------------- */
        .merch-form-col .field {
            display: flex;
            flex-direction: column;
            gap: 9px;
            min-width: 0;
        }

        .merch-form-col .field-label {
            font-size: 12.5px;
            font-weight: 600;
            color: var(--t-base);
            margin: 0;
            line-height: 1.4;
        }

        .merch-form-col .field-label .req {
            color: #ef4444;
            margin-left: 2px;
        }

        .merch-form-col input.input:not(.qty-input) {
            height: 40px;
            padding: 8px 12px;
            font-size: 13.5px;
            border-radius: 8px;
        }

        .merch-form-col textarea.textarea {
            padding: 10px 12px;
            font-size: 13.5px;
            border-radius: 8px;
            line-height: 1.5;
        }

        .btn-back-header:hover {
            color: var(--primary) !important;
            border-color: var(--primary) !important;
            background: var(--primary-soft, rgba(59, 130, 246, 0.08)) !important;
            transform: translateY(-1px);
        }

        .btn-secondary-merch {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--bg-card);
            color: var(--t-base);
            border: 1px solid var(--border);
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-secondary-merch:hover {
            background: var(--bg-hover);
            color: var(--primary);
            border-color: var(--primary);
        }

        /* ---------------------------------------------------------
           TAB NAVIGATION BUTTONS
        --------------------------------------------------------- */
        .merch-tab-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--t-muted);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            user-select: none;
        }

        .merch-tab-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-soft, rgba(59, 130, 246, 0.06));
        }

        .merch-tab-btn.active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 4px 14px var(--primary-ring, rgba(59, 130, 246, 0.28));
        }

        .merch-tab-btn .tab-badge {
            font-size: 10px;
            padding: 2px 7px;
            border-radius: 12px;
            background: var(--bg-muted);
            color: var(--t-muted);
            font-weight: 700;
        }

        .merch-tab-btn.active .tab-badge {
            background: rgba(255, 255, 255, 0.25);
            color: #ffffff;
        }

        /* ---------------------------------------------------------
           CHOICE CARDS & PILLS
        --------------------------------------------------------- */
        .choice-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 12px;
        }

        .choice-card {
            position: relative;
            border: 1.5px solid var(--border);
            border-radius: 11px;
            padding: 14px 40px 14px 16px;
            cursor: pointer;
            background: var(--bg-card);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            user-select: none;
        }

        .choice-card::after {
            content: '';
            position: absolute;
            top: 14px;
            right: 14px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            border: 1.5px solid var(--border);
            background: var(--bg-card);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10.5px;
            font-weight: 800;
            color: transparent;
            transition: all 0.2s ease;
        }

        .choice-card:hover {
            border-color: var(--primary);
            background: var(--bg-hover, rgba(59, 130, 246, 0.04));
            transform: translateY(-1px);
        }

        .choice-card.active {
            border-color: var(--primary);
            background: var(--primary-soft, rgba(59, 130, 246, 0.08));
            box-shadow: 0 4px 12px var(--primary-ring, rgba(59, 130, 246, 0.18));
        }

        .choice-card.active::after {
            content: '✓';
            border-color: var(--primary);
            background: var(--primary);
            color: #ffffff;
        }

        .choice-title {
            font-weight: 700;
            font-size: 13.5px;
            color: var(--t-base);
            line-height: 1.35;
            transition: color 0.15s ease;
        }

        .choice-card.active .choice-title {
            color: var(--primary);
        }

        .choice-sub {
            font-size: 11.5px;
            color: var(--t-muted);
            margin-top: 4px;
            line-height: 1.4;
        }

        .choice-price {
            font-size: 12px;
            font-weight: 700;
            color: var(--primary);
            margin-top: 8px;
        }

        .choice-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 14px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--t-base);
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            user-select: none;
        }

        .choice-pill input {
            display: none;
        }

        .choice-pill:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        .choice-pill.active {
            border-color: var(--primary);
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 2px 8px var(--primary-ring, rgba(59, 130, 246, 0.28));
        }

        /* ---------------------------------------------------------
           STEPPER & QUICK QUANTITY BUTTONS
        --------------------------------------------------------- */
        .qty-btn {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            background: var(--bg-muted);
            color: var(--t-base);
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
        }

        .qty-btn:first-child {
            border-radius: 8px 0 0 8px;
        }

        .qty-btn:last-child {
            border-radius: 0 8px 8px 0;
        }

        .qty-btn:hover {
            background: var(--border);
            color: var(--primary);
        }

        .qty-input {
            width: 70px;
            height: 38px;
            text-align: center;
            border-radius: 0;
            border-left: none;
            border-right: none;
            font-weight: 700;
        }

        .quick-qty-group {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .quick-qty-btn {
            padding: 6px 11px;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--t-muted);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s;
        }

        .quick-qty-btn:hover {
            color: var(--primary);
            border-color: var(--primary);
        }

        .quick-qty-btn.active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        /* ---------------------------------------------------------
           STICKY SUMMARY CARD (SAFE STACKING BELOW TOPBAR)
        --------------------------------------------------------- */
        .merch-sticky-card {
            position: static;
        }

        @media (min-width: 1081px) {
            .merch-sticky-card {
                position: sticky;
                top: 76px;
                z-index: 1;
            }
        }

        .summary-cat-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            background: var(--primary-soft, rgba(59, 130, 246, 0.12));
            color: var(--primary);
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 7px 0;
        }

        .summary-row .row-label {
            color: var(--t-muted);
        }

        .summary-row .row-val {
            color: var(--t-base);
            font-weight: 500;
        }

        /* Tombol Detail Estimasi */
        .btn-detail-estimation {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 11px 16px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 700;
            background: var(--primary);
            color: #ffffff;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px var(--primary-ring, rgba(59, 130, 246, 0.25));
        }

        .btn-detail-estimation:hover {
            opacity: 0.93;
            transform: translateY(-1px);
        }

        /* Tombol Salin WA */
        .btn-action-wa {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 10px 16px;
            border-radius: 8px;
            background: var(--bg-card);
            color: var(--t-base);
            font-weight: 600;
            font-size: 13px;
            border: 1.5px solid var(--border);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-action-wa:hover {
            background: var(--bg-hover);
            border-color: #22c55e;
            color: #22c55e;
        }

        /* ---------------------------------------------------------
                   MATRIKS DAFTAR HARGA & PAGINATION
                   --------------------------------------------------------- */
        .badge-cat-indicator {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 6px;
            background: var(--primary-soft, rgba(59, 130, 246, 0.12));
            color: var(--primary);
            border: 1px solid var(--border);
        }

        .table th,
        .table td {
            padding: 11px 14px;
            border-bottom: 1px solid var(--border);
        }

        .table tbody tr:hover {
            background: var(--primary-soft, rgba(59, 130, 246, 0.04));
        }

        .table tbody tr.row-active {
            background: var(--primary-soft, rgba(59, 130, 246, 0.08)) !important;
            border-left: 3.5px solid var(--primary);
        }

        .btn-table-pick {
            padding: 5px 12px;
            font-size: 11.5px;
            font-weight: 600;
            border-radius: 6px;
            border: 1.5px solid var(--primary);
            background: transparent;
            color: var(--primary);
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-table-pick:hover {
            background: var(--primary);
            color: #ffffff;
            box-shadow: 0 2px 6px var(--primary-ring, rgba(59, 130, 246, 0.25));
        }

        .btn-page-nav {
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--t-base);
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-page-nav:hover:not(:disabled) {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--bg-hover);
        }

        .btn-page-nav.active {
            background: var(--primary);
            color: #ffffff;
            border-color: var(--primary);
        }

        .btn-page-nav:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        /* ---------------------------------------------------------
                   MODAL POPUP STYLES
                   --------------------------------------------------------- */
        .modal-overlay {
            position: fixed !important;
            inset: 0 !important;
            display: none;
            align-items: center !important;
            justify-content: center !important;
            background: rgba(11, 17, 32, 0.75) !important;
            backdrop-filter: blur(4px) !important;
            z-index: 99999 !important;
            padding: 20px !important;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease !important;
        }

        .modal-overlay.is-open {
            display: flex !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        .modal-content-custom {
            background: var(--bg-card);
            color: var(--t-base);
            border: 1px solid var(--border);
            border-radius: 12px;
            width: 100%;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
            overflow: hidden;
        }

        .modal-head-custom {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--bg-card);
        }

        .modal-title-custom {
            margin: 2px 0 0;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--t-base);
        }

        .modal-close-btn {
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 18px;
            color: var(--t-muted);
            transition: color 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
            border-radius: 6px;
        }

        .modal-close-btn:hover {
            color: var(--t-base);
            background: var(--bg-hover);
        }

        .modal-foot-custom {
            padding: 14px 20px;
            background: var(--bg-muted);
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-radius: 0 0 12px 12px;
        }

        .printable-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--border);
            padding-bottom: 16px;
            margin-bottom: 18px;
        }

        .printable-company-name {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: var(--primary);
            margin: 0;
        }

        .printable-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-bottom: 18px;
        }

        @media (max-width: 640px) {
            .printable-info-grid {
                grid-template-columns: 1fr;
            }
        }

        .printable-info-box {
            padding: 12px 14px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--bg-muted);
            font-size: 12px;
            line-height: 1.6;
            color: var(--t-muted);
        }

        .printable-info-box strong {
            color: var(--t-base);
        }

        .printable-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
            margin-bottom: 18px;
        }

        .printable-table th {
            background: var(--bg-muted);
            color: var(--t-base);
            font-weight: 700;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
            padding: 10px 12px;
        }

        .printable-table td {
            border-bottom: 1px solid var(--border);
            padding: 10px 12px;
            color: var(--t-base);
        }

        /* ---------------------------------------------------------
                   PRINT STYLES
                   --------------------------------------------------------- */
        @media print {
            body * {
                visibility: hidden !important;
            }

            #modalMerchandiseDetail,
            #modalMerchandiseDetail * {
                visibility: visible !important;
            }

            #modalMerchandiseDetail {
                position: absolute !important;
                inset: 0 !important;
                background: #ffffff !important;
                padding: 0 !important;
                display: block !important;
                opacity: 1 !important;
            }

            .modal-content-custom {
                border: none !important;
                box-shadow: none !important;
                max-height: none !important;
                background: #ffffff !important;
                color: #000000 !important;
            }

            .modal-head-custom,
            .modal-foot-custom {
                display: none !important;
            }

            #printableMerchArea {
                padding: 0 !important;
                color: #0f172a !important;
            }

            .printable-company-name {
                color: #1e40af !important;
            }

            .printable-info-box {
                background: #f8fafc !important;
                border-color: #cbd5e1 !important;
                color: #334155 !important;
            }

            .printable-info-box strong {
                color: #0f172a !important;
            }

            .printable-table th {
                background: #f1f5f9 !important;
                color: #0f172a !important;
            }

            .printable-table td {
                color: #0f172a !important;
            }
        }

        /* ---------------------------------------------------------
           TOAST NOTIFICATION (COPY SUCCESS)
           --------------------------------------------------------- */
        .merch-toast-container {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 1000000;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .merch-toast {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 320px;
            max-width: 440px;
            padding: 14px 18px;
            border-radius: 12px;
            background: var(--bg-card);
            color: var(--t-base);
            border: 1px solid var(--border);
            border-left: 4.5px solid #22c55e;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.3);
            pointer-events: auto;
            transform: translateX(120%);
            opacity: 0;
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
        }

        .merch-toast.is-show {
            transform: translateX(0);
            opacity: 1;
        }

        .merch-toast-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(34, 197, 94, 0.15);
            color: #22c55e;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .merch-toast-content {
            flex: 1;
        }

        .merch-toast-title {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--t-base);
            margin: 0 0 3px;
        }

        .merch-toast-desc {
            font-size: 12px;
            color: var(--t-muted);
            margin: 0;
            line-height: 1.45;
        }

        .merch-toast-close {
            background: transparent;
            border: none;
            color: var(--t-muted);
            font-size: 18px;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.15s;
        }

        .merch-toast-close:hover {
            color: var(--t-base);
            background: var(--bg-hover);
        }

        /* =========================================================
           SELECT2 STYLING HARMONIZATION (IDENTICAL TO CALENDAR ESTIMATOR)
        ========================================================== */
        /* Scope 100% width ONLY to the field select2 container, NEVER globally */
        .field > .select2-container,
        .field .select2-container--default {
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
        }

        /* Scope compact width ONLY to table page-size select2 */
        .select2-sm-wrap > .select2-container,
        .select2-sm-wrap .select2-container--default {
            width: 85px !important;
            min-width: 85px !important;
        }

        /* Prevent Select2 dropdown container from causing horizontal overflow */
        .select2-container {
            max-width: 100vw !important;
            box-sizing: border-box !important;
        }

        .select2-container--default .select2-selection--single {
            background-color: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            height: 40px;
            display: flex;
            align-items: center;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-ring);
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: var(--t-base);
            padding-left: 12px;
            padding-right: 28px;
            font-size: 13.5px;
            line-height: 38px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px;
            right: 8px;
        }

        .select2-dropdown {
            background-color: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: var(--shadow-lg);
            z-index: 99999;
            box-sizing: border-box !important;
            max-width: 100vw !important;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field {
            background-color: var(--bg-muted);
            border: 1px solid var(--border);
            border-radius: 6px;
            color: var(--t-base);
            padding: 6px 10px;
            font-size: 13px;
            outline: none;
        }

        .select2-container--default .select2-search--dropdown .select2-search__field:focus {
            border-color: var(--primary);
        }

        .select2-container--default .select2-results__option {
            color: var(--t-base);
            font-size: 13px;
            padding: 8px 12px;
        }

        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: var(--primary);
            color: #ffffff;
        }

        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: var(--bg-muted);
            color: var(--primary);
            font-weight: 600;
        }

        /* Compact Select2 Variant for Table Page-Size ("Baris") */
        .select2-sm-wrap .select2-container--default .select2-selection--single {
            height: 36px;
        }

        .select2-sm-wrap .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 34px;
            font-size: 12.5px;
            padding-left: 10px;
            padding-right: 24px;
        }

        .select2-sm-wrap .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 34px;
            right: 6px;
        }
    </style>

    {{-- =========================================================
        JAVASCRIPT ESTIMATOR MERCHANDISE
    ========================================================== --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Master Data dari Database
            const dbLanyardTiers = @json($lanyardTiers ?? []);
            const dbNameTagItems = @json($nameTagItems ?? []);
            const dbPlakatItems = @json($plakatItems ?? []);
            const dbNotaItems = @json($notaItems ?? []);

            let activeTab = 'lanyard';

            // State Lanyard
            let lanyardWidth = '2cm';
            let lanyardPackage = 'set';
            let lanyardQty = 25;

            // State Nametag
            let nametagType = 'resin';
            let nametagFastener = 'magnet';
            let nametagQty = 10;

            // State Plakat
            let plakatMaterial = 'akrilik';
            let plakatThickness = '3mm';
            let plakatBase = 'akrilik';
            let plakatBox = 'box';
            let plakatQty = 1;
            let plakatDesignFee = false;

            // State Nota
            let notaPaper = 'ncr';
            let notaPly = 2;
            let notaInk = 1;
            let notaSize = '1/4';
            let notaBooksPerRim = 40;
            let notaRimQty = 1;
            let notaNumerator = true;
            let notaDesignFee = false;

            // State Pagination & Filter Matrix
            let matrixCurrentPage = 1;
            let matrixPageSize = 10;

            // Inisialisasi Select2 (Konsisten dengan Estimasi Kalender)
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('.field .select2').select2({
                    width: '100%'
                });

                $('#merchMatrixPageSize').select2({
                    width: '85px',
                    minimumResultsForSearch: -1
                });

                $('#merch_customer_id').on('change', function() {
                    const sel = $(this).find('option:selected');
                    const cNameInput = document.getElementById('merch_customer_name');
                    if (cNameInput) {
                        cNameInput.value = sel.data('name') || '';
                    }
                });
            } else {
                document.getElementById('merch_customer_id')?.addEventListener('change', function() {
                    const opt = this.options[this.selectedIndex];
                    const cNameInput = document.getElementById('merch_customer_name');
                    if (cNameInput) {
                        cNameInput.value = opt ? (opt.getAttribute('data-name') || '') : '';
                    }
                });
            }

            function formatRp(val) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    maximumFractionDigits: 0
                }).format(val || 0);
            }

            // 1. Tab Navigation
            const tabButtons = document.querySelectorAll('.merch-tab-btn');
            const tabPanels = {
                lanyard: document.getElementById('panelLanyard'),
                nametag: document.getElementById('panelNametag'),
                plakat: document.getElementById('panelPlakat'),
                nota: document.getElementById('panelNota')
            };

            tabButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const tab = this.getAttribute('data-tab');
                    activeTab = tab;

                    tabButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    Object.keys(tabPanels).forEach(k => {
                        if (tabPanels[k]) {
                            tabPanels[k].style.display = (k === tab) ? 'block' : 'none';
                        }
                    });

                    if (tab === 'nota' && typeof $ !== 'undefined' && $.fn.select2) {
                        $('#nota_size_select').trigger('change.select2');
                    }

                    matrixCurrentPage = 1;
                    updateCalculation();
                    renderMatrixTable();
                });
            });

            // 2. Lanyard Event Handlers
            document.querySelectorAll('input[name="lanyard_width"]').forEach(r => {
                r.addEventListener('change', function() {
                    lanyardWidth = this.value;
                    document.querySelectorAll('#cardLanyardWidth2cm, #cardLanyardWidth25cm')
                        .forEach(c => c.classList.remove('active'));
                    this.closest('.choice-card').classList.add('active');
                    updateCalculation();
                    renderMatrixTable();
                });
            });

            document.querySelectorAll('input[name="lanyard_package"]').forEach(r => {
                r.addEventListener('change', function() {
                    lanyardPackage = this.value;
                    document.querySelectorAll('#cardLanyardPkgSet, #cardLanyardPkgTali').forEach(
                        c => c.classList.remove('active'));
                    this.closest('.choice-card').classList.add('active');
                    updateCalculation();
                    renderMatrixTable();
                });
            });

            const lanyardQtyInput = document.getElementById('lanyardQtyInput');
            if (lanyardQtyInput) {
                lanyardQtyInput.addEventListener('input', function() {
                    lanyardQty = Math.max(1, parseInt(this.value) || 1);
                    updateCalculation();
                    renderMatrixTable();
                });
            }
            document.getElementById('btnLanyardQtyMinus')?.addEventListener('click', function() {
                lanyardQty = Math.max(1, lanyardQty - 1);
                if (lanyardQtyInput) lanyardQtyInput.value = lanyardQty;
                updateCalculation();
                renderMatrixTable();
            });
            document.getElementById('btnLanyardQtyPlus')?.addEventListener('click', function() {
                lanyardQty += 1;
                if (lanyardQtyInput) lanyardQtyInput.value = lanyardQty;
                updateCalculation();
                renderMatrixTable();
            });
            document.querySelectorAll('#panelLanyard .quick-qty-btn').forEach(b => {
                b.addEventListener('click', function() {
                    document.querySelectorAll('#panelLanyard .quick-qty-btn').forEach(btn => btn
                        .classList.remove('active'));
                    this.classList.add('active');
                    lanyardQty = parseInt(this.getAttribute('data-qty'));
                    if (lanyardQtyInput) lanyardQtyInput.value = lanyardQty;
                    updateCalculation();
                    renderMatrixTable();
                });
            });

            // 3. Nametag Event Handlers
            document.querySelectorAll('input[name="nametag_type"]').forEach(r => {
                r.addEventListener('change', function() {
                    nametagType = this.value;
                    document.querySelectorAll(
                        '#cardNametagBiasa, #cardNametagResin, #cardNametagResinEmas').forEach(
                        c => c.classList.remove('active'));
                    this.closest('.choice-card').classList.add('active');
                    updateCalculation();
                    renderMatrixTable();
                });
            });

            document.querySelectorAll('input[name="nametag_fastener"]').forEach(r => {
                r.addEventListener('change', function() {
                    nametagFastener = this.value;
                    document.querySelectorAll('#pillFastenerMagnet, #pillFastenerPeniti').forEach(
                        c => c.classList.remove('active'));
                    this.closest('.choice-pill').classList.add('active');
                    updateCalculation();
                });
            });
            const nametagQtyInput = document.getElementById('nametagQtyInput');
            if (nametagQtyInput) {
                nametagQtyInput.addEventListener('input', function() {
                    nametagQty = Math.max(1, parseInt(this.value) || 1);
                    updateCalculation();
                });
            }
            document.getElementById('btnNametagQtyMinus')?.addEventListener('click', function() {
                nametagQty = Math.max(1, nametagQty - 1);
                if (nametagQtyInput) nametagQtyInput.value = nametagQty;
                updateCalculation();
            });
            document.getElementById('btnNametagQtyPlus')?.addEventListener('click', function() {
                nametagQty += 1;
                if (nametagQtyInput) nametagQtyInput.value = nametagQty;
                updateCalculation();
            });
            document.querySelectorAll('#panelNametag .quick-qty-btn').forEach(b => {
                b.addEventListener('click', function() {
                    document.querySelectorAll('#panelNametag .quick-qty-btn').forEach(btn => btn
                        .classList.remove('active'));
                    this.classList.add('active');
                    nametagQty = parseInt(this.getAttribute('data-qty'));
                    if (nametagQtyInput) nametagQtyInput.value = nametagQty;
                    updateCalculation();
                });
            });

            // 4. Plakat Event Handlers
            document.querySelectorAll('input[name="plakat_material"]').forEach(r => {
                r.addEventListener('change', function() {
                    plakatMaterial = this.value;
                    document.querySelectorAll('#cardPlakatMatAkrilik, #cardPlakatMatKayu').forEach(
                        c => c.classList.remove('active'));
                    this.closest('.choice-card').classList.add('active');
                    const akrilikOpts = document.getElementById('plakatAkrilikOptions');
                    if (akrilikOpts) {
                        akrilikOpts.style.display = (plakatMaterial === 'akrilik') ? 'grid' :
                            'none';
                    }
                    updateCalculation();
                    renderMatrixTable();
                });
            });

            document.querySelectorAll('input[name="plakat_thickness"]').forEach(r => {
                r.addEventListener('change', function() {
                    plakatThickness = this.value;
                    document.querySelectorAll(
                        '#pillPlakatThk2mm, #pillPlakatThk3mm, #pillPlakatThk5mm, #pillPlakatThk1cm'
                    ).forEach(p => p.classList.remove('active'));
                    this.closest('.choice-pill').classList.add('active');
                    updateCalculation();
                    renderMatrixTable();
                });
            });

            document.querySelectorAll('input[name="plakat_base"]').forEach(r => {
                r.addEventListener('change', function() {
                    plakatBase = this.value;
                    document.querySelectorAll('#pillPlakatBaseAkrilik, #pillPlakatBaseHitam')
                        .forEach(p => p.classList.remove('active'));
                    this.closest('.choice-pill').classList.add('active');
                    updateCalculation();
                    renderMatrixTable();
                });
            });

            document.querySelectorAll('input[name="plakat_box"]').forEach(r => {
                r.addEventListener('change', function() {
                    plakatBox = this.value;
                    document.querySelectorAll('#pillPlakatWithBox, #pillPlakatNoBox').forEach(p => p
                        .classList.remove('active'));
                    this.closest('.choice-pill').classList.add('active');
                    updateCalculation();
                    renderMatrixTable();
                });
            });

            const plakatQtyInput = document.getElementById('plakatQtyInput');
            if (plakatQtyInput) {
                plakatQtyInput.addEventListener('input', function() {
                    plakatQty = Math.max(1, parseInt(this.value) || 1);
                    updateCalculation();
                });
            }
            document.getElementById('btnPlakatQtyMinus')?.addEventListener('click', function() {
                plakatQty = Math.max(1, plakatQty - 1);
                if (plakatQtyInput) plakatQtyInput.value = plakatQty;
                updateCalculation();
            });
            document.getElementById('btnPlakatQtyPlus')?.addEventListener('click', function() {
                plakatQty += 1;
                if (plakatQtyInput) plakatQtyInput.value = plakatQty;
                updateCalculation();
            });
            document.querySelectorAll('#panelPlakat .quick-qty-btn').forEach(b => {
                b.addEventListener('click', function() {
                    document.querySelectorAll('#panelPlakat .quick-qty-btn').forEach(btn => btn
                        .classList.remove('active'));
                    this.classList.add('active');
                    plakatQty = parseInt(this.getAttribute('data-qty'));
                    if (plakatQtyInput) plakatQtyInput.value = plakatQty;
                    updateCalculation();
                });
            });

            document.getElementById('checkPlakatDesign')?.addEventListener('change', function() {
                plakatDesignFee = this.checked;
                updateCalculation();
            });

            // 5. Nota Event Handlers
            document.querySelectorAll('input[name="nota_paper"]').forEach(r => {
                r.addEventListener('change', function() {
                    notaPaper = this.value;
                    document.querySelectorAll('#cardNotaPaperNcr, #cardNotaPaperHvs').forEach(c => c
                        .classList.remove('active'));
                    this.closest('.choice-card').classList.add('active');

                    const pillPly1 = document.getElementById('pillNotaPly1');
                    const pillPly2 = document.getElementById('pillNotaPly2');
                    const pillPly3 = document.getElementById('pillNotaPly3');
                    const pillPly4 = document.getElementById('pillNotaPly4');
                    const pillInk3 = document.getElementById('pillNotaInk3');

                    if (notaPaper === 'hvs') {
                        if (pillPly1) pillPly1.style.display = 'inline-flex';
                        if (pillPly2) pillPly2.style.display = 'none';
                        if (pillPly3) pillPly3.style.display = 'none';
                        if (pillPly4) pillPly4.style.display = 'none';
                        if (pillInk3) pillInk3.style.display = 'none';
                        notaPly = 1;
                        if (notaInk === 3) notaInk = 1;
                        pillPly1?.classList.add('active');
                        document.querySelectorAll('#pillNotaPly2, #pillNotaPly3, #pillNotaPly4')
                            .forEach(p => p.classList.remove('active'));
                    } else {
                        if (pillPly1) pillPly1.style.display = 'none';
                        if (pillPly2) pillPly2.style.display = 'inline-flex';
                        if (pillPly3) pillPly3.style.display = 'inline-flex';
                        if (pillPly4) pillPly4.style.display = 'inline-flex';
                        if (pillInk3) pillInk3.style.display = 'inline-flex';
                        if (notaPly === 1) notaPly = 2;
                        document.querySelectorAll(
                                '#pillNotaPly1, #pillNotaPly2, #pillNotaPly3, #pillNotaPly4')
                            .forEach(p => p.classList.remove('active'));
                        document.getElementById('pillNotaPly' + notaPly)?.classList.add('active');
                    }

                    updateCalculation();
                    renderMatrixTable();
                });
            });

            document.querySelectorAll('input[name="nota_ply"]').forEach(r => {
                r.addEventListener('change', function() {
                    notaPly = parseInt(this.value);
                    document.querySelectorAll(
                        '#pillNotaPly1, #pillNotaPly2, #pillNotaPly3, #pillNotaPly4').forEach(
                        p => p.classList.remove('active'));
                    this.closest('.choice-pill').classList.add('active');
                    updateCalculation();
                    renderMatrixTable();
                });
            });

            document.querySelectorAll('input[name="nota_ink"]').forEach(r => {
                r.addEventListener('change', function() {
                    notaInk = parseInt(this.value);
                    document.querySelectorAll('#pillNotaInk1, #pillNotaInk2, #pillNotaInk3')
                        .forEach(p => p.classList.remove('active'));
                    this.closest('.choice-pill').classList.add('active');
                    updateCalculation();
                    renderMatrixTable();
                });
            });

            function handleNotaSizeChange() {
                const sel = document.getElementById('nota_size_select');
                if (!sel) return;
                notaSize = sel.value;
                const opt = sel.options[sel.selectedIndex];
                notaBooksPerRim = parseInt(opt ? opt.getAttribute('data-books') : 40) || 40;
                updateCalculation();
            }

            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#nota_size_select').on('change.select2 change', handleNotaSizeChange);
            } else {
                document.getElementById('nota_size_select')?.addEventListener('change', handleNotaSizeChange);
            }

            const notaQtyInput = document.getElementById('notaQtyInput');
            if (notaQtyInput) {
                notaQtyInput.addEventListener('input', function() {
                    notaRimQty = Math.max(1, parseInt(this.value) || 1);
                    updateCalculation();
                });
            }
            document.getElementById('btnNotaQtyMinus')?.addEventListener('click', function() {
                notaRimQty = Math.max(1, notaRimQty - 1);
                if (notaQtyInput) notaQtyInput.value = notaRimQty;
                updateCalculation();
            });
            document.getElementById('btnNotaQtyPlus')?.addEventListener('click', function() {
                notaRimQty += 1;
                if (notaQtyInput) notaQtyInput.value = notaRimQty;
                updateCalculation();
            });
            document.querySelectorAll('#panelNota .quick-qty-btn').forEach(b => {
                b.addEventListener('click', function() {
                    document.querySelectorAll('#panelNota .quick-qty-btn').forEach(btn => btn
                        .classList.remove('active'));
                    this.classList.add('active');
                    notaRimQty = parseInt(this.getAttribute('data-qty'));
                    if (notaQtyInput) notaQtyInput.value = notaRimQty;
                    updateCalculation();
                });
            });

            document.getElementById('checkNotaNumerator')?.addEventListener('change', function() {
                notaNumerator = this.checked;
                updateCalculation();
            });
            document.getElementById('checkNotaDesign')?.addEventListener('change', function() {
                notaDesignFee = this.checked;
                updateCalculation();
            });

            // 6. Master Calculation Function
            function updateCalculation() {
                const categoryBadge = document.getElementById('summaryMerchCategoryBadge');
                const productTitle = document.getElementById('summaryMerchProductTitle');
                const specsDesc = document.getElementById('summaryMerchSpecs');
                const qtyVal = document.getElementById('summaryMerchQty');
                const tierVal = document.getElementById('summaryMerchTier');
                const unitPriceLabel = document.getElementById('summaryMerchUnitLabel');
                const unitPriceVal = document.getElementById('summaryMerchUnitPrice');
                const rowExtra = document.getElementById('rowSummaryExtraFee');
                const extraLabel = document.getElementById('summaryMerchExtraLabel');
                const extraVal = document.getElementById('summaryMerchExtraFee');
                const grandTotal = document.getElementById('summaryMerchGrandTotal');
                const perUnit = document.getElementById('summaryMerchPerUnit');

                let total = 0;
                let unit = 0;

                if (activeTab === 'lanyard') {
                    categoryBadge.textContent = 'TALI LANYARD';
                    const widthLabel = (lanyardWidth === '2cm') ? '2.0 cm' : '2.5 cm';
                    const pkgLabel = (lanyardPackage === 'set') ? '1 Set (Holder + ID Card PVC)' :
                        'Hanya Tali Saja';
                    productTitle.textContent = `Tali Lanyard ${widthLabel} (${pkgLabel})`;
                    specsDesc.textContent = `Lebar ${widthLabel} · Cetak 2 Sisi · Paket: ${pkgLabel}`;

                    // Cari tier
                    const tiers = dbLanyardTiers.filter(t => t.width_type === lanyardWidth && t.package_type ===
                        lanyardPackage);
                    let activeTier = tiers[0];
                    tiers.forEach(t => {
                        const min = parseInt(t.min_qty);
                        const max = t.max_qty !== null ? parseInt(t.max_qty) : Infinity;
                        if (lanyardQty >= min && lanyardQty <= max) {
                            activeTier = t;
                        }
                    });

                    unit = activeTier ? parseFloat(activeTier.price) : 20000;
                    total = unit * lanyardQty;

                    qtyVal.textContent = `${lanyardQty} pcs`;
                    tierVal.textContent = activeTier ? `Tier: ${activeTier.tier_label}` : '-';
                    unitPriceLabel.textContent = 'Harga Satuan:';
                    unitPriceVal.textContent = `${formatRp(unit)} / pcs`;
                    if (rowExtra) rowExtra.style.display = 'none';

                    grandTotal.textContent = formatRp(total);
                    perUnit.textContent = `(${formatRp(unit)} / pcs)`;

                } else if (activeTab === 'nametag') {
                    categoryBadge.textContent = 'PAPAN NAMA DADA';
                    const names = {
                        biasa: 'Papan Nama Biasa',
                        resin: 'Papan Nama Resin',
                        resin_emas: 'Papan Nama Resin Emas'
                    };
                    const fastenerLabel = (nametagFastener === 'magnet') ? 'Magnet Neodymium' : 'Peniti Stainless';
                    const item = dbNameTagItems.find(i => i.type_code === nametagType);

                    productTitle.textContent = item ? item.name : names[nametagType];
                    specsDesc.textContent = `Model Pengait: ${fastenerLabel} · ${item ? item.description : ''}`;

                    unit = item ? parseFloat(item.price) : 35000;
                    total = unit * nametagQty;

                    qtyVal.textContent = `${nametagQty} pcs`;
                    tierVal.textContent = `Pengait: ${fastenerLabel}`;
                    unitPriceLabel.textContent = 'Harga Satuan:';
                    unitPriceVal.textContent = `${formatRp(unit)} / pcs`;
                    if (rowExtra) rowExtra.style.display = 'none';

                    grandTotal.textContent = formatRp(total);
                    perUnit.textContent = `(${formatRp(unit)} / pcs)`;

                } else if (activeTab === 'plakat') {
                    categoryBadge.textContent = 'PLAKAT PENGHARGAAN';
                    let item = null;
                    if (plakatMaterial === 'kayu') {
                        item = dbPlakatItems.find(i => i.material_type === 'kayu');
                        productTitle.textContent = 'Plakat Kayu Solid (+ Box Beludru)';
                        specsDesc.textContent = 'Bahan Kayu Solid Ukir Mewah · Termasuk Box Beludru';
                    } else {
                        item = dbPlakatItems.find(i =>
                            i.material_type === 'akrilik' &&
                            i.thickness === plakatThickness &&
                            i.base_type === plakatBase &&
                            i.box_type === plakatBox
                        );
                        const thk = plakatThickness.toUpperCase();
                        const baseLbl = (plakatBase === 'hitam') ? 'Dudukan Hitam' : 'Dudukan Akrilik';
                        const boxLbl = (plakatBox === 'box') ? 'Termasuk Box (+ Box)' : 'Tanpa Box';
                        productTitle.textContent = `Plakat Akrilik ${thk} (${baseLbl})`;
                        specsDesc.textContent = `Tebal ${thk} · ${baseLbl} · Kemasan: ${boxLbl} · Ukuran 20x15 cm`;
                    }

                    unit = item ? parseFloat(item.price) : 210000;
                    const designExtra = plakatDesignFee ? 35000 : 0;
                    total = (unit * plakatQty) + designExtra;

                    qtyVal.textContent = `${plakatQty} pcs`;
                    tierVal.textContent = item ? item.dimensions || '20x15 cm' : '20x15 cm';
                    unitPriceLabel.textContent = 'Harga Satuan:';
                    unitPriceVal.textContent = `${formatRp(unit)} / pcs`;

                    if (rowExtra) {
                        if (plakatDesignFee) {
                            rowExtra.style.display = 'flex';
                            extraLabel.textContent = 'Jasa Desain:';
                            extraVal.textContent = formatRp(designExtra);
                        } else {
                            rowExtra.style.display = 'none';
                        }
                    }

                    grandTotal.textContent = formatRp(total);
                    perUnit.textContent = `(${formatRp(unit)} / pcs${plakatDesignFee ? ' + Desain' : ''})`;

                } else if (activeTab === 'nota') {
                    categoryBadge.textContent = 'BUKU NOTA FORM';
                    const paperLabel = (notaPaper === 'ncr') ? 'Kertas NCR Carbonless' : 'Kertas HVS Putih';
                    const item = dbNotaItems.find(i =>
                        i.paper_type === notaPaper &&
                        parseInt(i.ply_count) === notaPly &&
                        parseInt(i.ink_count) === notaInk
                    );

                    const sizeSelect = document.getElementById('nota_size_select');
                    const sizeText = sizeSelect ? sizeSelect.options[sizeSelect.selectedIndex].text.split(' - ')[
                        0] : '1/4 Folio';
                    productTitle.textContent = item ? item.name : `Nota ${paperLabel} ${notaPly} Ply`;
                    specsDesc.textContent =
                        `Ukuran Jadi: ${sizeText} · ${notaNumerator ? 'Nomorator Otomatis' : ''}`;

                    unit = item ? parseFloat(item.price) : 329000; // Harga per rim
                    const designExtra = notaDesignFee ? 30000 : 0;
                    total = (unit * notaRimQty) + designExtra;
                    const totalBooks = notaBooksPerRim * notaRimQty;
                    const perBook = totalBooks > 0 ? Math.round(total / totalBooks) : 0;

                    qtyVal.textContent = `${notaRimQty} Paket (${notaRimQty} Rim Plano) = ${totalBooks} Buku`;
                    tierVal.textContent = `${notaPly} Ply · ${notaInk} Tinta`;
                    unitPriceLabel.textContent = 'Harga Paket Rim:';
                    unitPriceVal.textContent = `${formatRp(unit)} / paket rim`;

                    if (rowExtra) {
                        if (notaDesignFee) {
                            rowExtra.style.display = 'flex';
                            extraLabel.textContent = 'Jasa Desain:';
                            extraVal.textContent = formatRp(designExtra);
                        } else {
                            rowExtra.style.display = 'none';
                        }
                    }

                    grandTotal.textContent = formatRp(total);
                    perUnit.textContent = `(~ ${formatRp(perBook)} / buku)`;
                }
            }

            // 7. Render Matriks Tabel Harga (Di Bawah Estimator) dengan Pagination
            function renderMatrixTable() {
                const thead = document.getElementById('merchMatrixTableHead');
                const tbody = document.getElementById('merchMatrixTableBody');
                const title = document.getElementById('matrixHeaderTitle');
                const catBadge = document.getElementById('matrixCategoryIndicatorBadge');
                const searchInput = document.getElementById('merchMatrixSearch');
                const searchClear = document.getElementById('merchMatrixSearchClear');
                const query = (searchInput?.value || '').toLowerCase().trim();

                if (!thead || !tbody) return;

                if (searchClear) {
                    searchClear.style.display = query ? 'block' : 'none';
                }

                thead.innerHTML = '';
                tbody.innerHTML = '';

                let filtered = [];

                if (activeTab === 'lanyard') {
                    title.textContent = 'Daftar Harga Tali Lanyard & ID Card (24 Tier)';
                    if (catBadge) catBadge.textContent = 'Tali Lanyard & ID Card · 24 Data';
                    thead.innerHTML = `
                        <tr>
                            <th style="width: 55px; text-align: center;">No</th>
                            <th>Lebar Tali</th>
                            <th>Jenis Paket</th>
                            <th>Rentang Kuantiti (Tier)</th>
                            <th style="text-align: right;">Harga Satuan</th>
                            <th style="text-align: center; width: 110px;">Aksi</th>
                        </tr>
                    `;

                    filtered = dbLanyardTiers.filter(t => {
                        if (!query) return true;
                        return t.width_type.toLowerCase().includes(query) ||
                            t.package_type.toLowerCase().includes(query) ||
                            t.tier_label.toLowerCase().includes(query) ||
                            t.price.toString().includes(query);
                    });

                } else if (activeTab === 'nametag') {
                    title.textContent = 'Daftar Harga Papan Nama Dada / Name Tag (3 Varian)';
                    if (catBadge) catBadge.textContent = 'Papan Nama Dada · 3 Data';
                    thead.innerHTML = `
                        <tr>
                            <th style="width: 55px; text-align: center;">No</th>
                            <th>Varian Papan Nama</th>
                            <th>Opsi Pengait</th>
                            <th>Keterangan Finishing</th>
                            <th style="text-align: right;">Harga Satuan</th>
                            <th style="text-align: center; width: 110px;">Aksi</th>
                        </tr>
                    `;

                    filtered = dbNameTagItems.filter(i => {
                        if (!query) return true;
                        return i.name.toLowerCase().includes(query) ||
                            (i.description && i.description.toLowerCase().includes(query)) ||
                            i.price.toString().includes(query);
                    });

                } else if (activeTab === 'plakat') {
                    title.textContent = 'Daftar Harga Plakat Akrilik UV & Kayu (13 Tipe)';
                    if (catBadge) catBadge.textContent = 'Plakat Akrilik & Kayu · 13 Data';
                    thead.innerHTML = `
                        <tr>
                            <th style="width: 55px; text-align: center;">No</th>
                            <th>Tipe Plakat</th>
                            <th>Tebal</th>
                            <th>Dudukan</th>
                            <th>Box Beludru</th>
                            <th style="text-align: right;">Harga Satuan</th>
                            <th style="text-align: center; width: 110px;">Aksi</th>
                        </tr>
                    `;

                    filtered = dbPlakatItems.filter(i => {
                        if (!query) return true;
                        return i.name.toLowerCase().includes(query) ||
                            i.thickness.toLowerCase().includes(query) ||
                            i.base_type.toLowerCase().includes(query) ||
                            i.box_type.toLowerCase().includes(query) ||
                            i.price.toString().includes(query);
                    });

                } else if (activeTab === 'nota') {
                    title.textContent = 'Daftar Harga Cetak Buku Nota NCR & HVS (11 Varian)';
                    if (catBadge) catBadge.textContent = 'Buku Nota NCR & HVS · 11 Data';
                    thead.innerHTML = `
                        <tr>
                            <th style="width: 55px; text-align: center;">No</th>
                            <th>Varian Nota</th>
                            <th>Jenis Kertas</th>
                            <th>Rangkap</th>
                            <th>Tinta</th>
                            <th>Isi Buku</th>
                            <th style="text-align: right;">Harga Paket Rim</th>
                            <th style="text-align: center; width: 110px;">Aksi</th>
                        </tr>
                    `;

                    filtered = dbNotaItems.filter(i => {
                        if (!query) return true;
                        return i.name.toLowerCase().includes(query) ||
                            i.paper_type.toLowerCase().includes(query) ||
                            i.ply_label.toLowerCase().includes(query) ||
                            i.ink_label.toLowerCase().includes(query) ||
                            i.price.toString().includes(query);
                    });
                }

                // Hitung Pagination
                const totalItems = filtered.length;
                let pageSize = (matrixPageSize === 'all') ? totalItems : parseInt(matrixPageSize);
                if (!pageSize || pageSize < 1) pageSize = 10;

                const totalPages = Math.max(1, Math.ceil(totalItems / pageSize));
                if (matrixCurrentPage > totalPages) matrixCurrentPage = totalPages;
                if (matrixCurrentPage < 1) matrixCurrentPage = 1;

                const startIndex = (matrixCurrentPage - 1) * pageSize;
                const endIndex = Math.min(startIndex + pageSize, totalItems);
                const paginatedItems = filtered.slice(startIndex, endIndex);

                // Render Baris Kosong jika 0 hasil
                if (totalItems === 0) {
                    const colSpan = (activeTab === 'nota') ? 8 : (activeTab === 'plakat' ? 7 : 6);
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="${colSpan}" style="text-align: center; padding: 32px 20px; color: var(--t-muted);">
                                <i class="bi bi-search" style="font-size: 24px; display: block; margin-bottom: 8px;"></i>
                                Tidak ditemukan data harga yang sesuai dengan kata kunci "${query}".
                            </td>
                        </tr>
                    `;
                    renderPaginationControls(0, 0, 0, 1);
                    return;
                }

                // Render Baris Data
                paginatedItems.forEach((row, idx) => {
                    const rowNumber = startIndex + idx + 1;
                    const tr = document.createElement('tr');

                    if (activeTab === 'lanyard') {
                        const isMatchConfig = (row.width_type === lanyardWidth && row.package_type ===
                            lanyardPackage);
                        const min = parseInt(row.min_qty);
                        const max = row.max_qty !== null ? parseInt(row.max_qty) : Infinity;
                        const isCurrentActive = isMatchConfig && (lanyardQty >= min && lanyardQty <= max);
                        if (isCurrentActive) tr.className = 'row-active';

                        const widthLabel = (row.width_type === '2cm') ? '2.0 cm' : '2.5 cm';
                        const pkgLabel = (row.package_type === 'set') ? '1 Set (Holder + ID Card PVC)' :
                            'Hanya Tali Saja';

                        tr.innerHTML = `
                            <td style="text-align: center; color: var(--t-muted);">${rowNumber}</td>
                            <td><strong style="color: var(--t-base);">${widthLabel}</strong></td>
                            <td>${pkgLabel}</td>
                            <td>
                                <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; background: var(--bg-muted); font-weight: 600; font-size: 12px;">
                                    ${row.tier_label}
                                </span>
                                ${isCurrentActive ? '<span class="badge" style="background: var(--primary); color: #fff; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px; margin-left: 6px;"><i class="bi bi-check2"></i> Dipilih</span>' : ''}
                            </td>
                            <td style="text-align: right; font-weight: 700; color: var(--primary);">${formatRp(parseFloat(row.price))} / pcs</td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-table-pick" data-pick-lanyard="${row.id}" data-width="${row.width_type}" data-pkg="${row.package_type}" data-min="${row.min_qty}">
                                    Pilih
                                </button>
                            </td>
                        `;

                    } else if (activeTab === 'nametag') {
                        const isCurrentActive = (row.type_code === nametagType);
                        if (isCurrentActive) tr.className = 'row-active';

                        tr.innerHTML = `
                            <td style="text-align: center; color: var(--t-muted);">${rowNumber}</td>
                            <td>
                                <strong style="color: var(--t-base);">${row.name}</strong>
                                ${isCurrentActive ? '<span class="badge" style="background: var(--primary); color: #fff; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px; margin-left: 6px;"><i class="bi bi-check2"></i> Dipilih</span>' : ''}
                            </td>
                            <td>${row.fastener_options || 'Peniti, Magnet'}</td>
                            <td style="font-size: 12px; color: var(--t-muted); max-width: 320px;">${row.description || '-'}</td>
                            <td style="text-align: right; font-weight: 700; color: var(--primary);">${formatRp(parseFloat(row.price))} / pcs</td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-table-pick" data-pick-nametag="${row.type_code}">
                                    Pilih
                                </button>
                            </td>
                        `;

                    } else if (activeTab === 'plakat') {
                        let isCurrentActive = false;
                        if (plakatMaterial === 'kayu' && row.material_type === 'kayu') {
                            isCurrentActive = true;
                        } else if (plakatMaterial === 'akrilik' && row.material_type === 'akrilik' &&
                            row.thickness === plakatThickness && row.base_type === plakatBase && row
                            .box_type === plakatBox) {
                            isCurrentActive = true;
                        }
                        if (isCurrentActive) tr.className = 'row-active';

                        tr.innerHTML = `
                            <td style="text-align: center; color: var(--t-muted);">${rowNumber}</td>
                            <td>
                                <strong style="color: var(--t-base);">${row.name}</strong>
                                ${isCurrentActive ? '<span class="badge" style="background: var(--primary); color: #fff; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px; margin-left: 6px;"><i class="bi bi-check2"></i> Dipilih</span>' : ''}
                            </td>
                            <td>${row.thickness.toUpperCase()}</td>
                            <td>${row.base_type === 'hitam' ? 'Dudukan Hitam' : (row.base_type === 'kayu' ? 'Dudukan Kayu' : 'Dudukan Akrilik')}</td>
                            <td>${row.box_type === 'box' ? '<span style="color: #22c55e; font-weight: 600;"><i class="bi bi-check-lg"></i> Ya (+ Box)</span>' : '<span style="color: var(--t-muted);">Tanpa Box</span>'}</td>
                            <td style="text-align: right; font-weight: 700; color: var(--primary);">${formatRp(parseFloat(row.price))} / pcs</td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-table-pick" data-pick-plakat="${row.id}" data-mat="${row.material_type}" data-thk="${row.thickness}" data-base="${row.base_type}" data-box="${row.box_type}">
                                    Pilih
                                </button>
                            </td>
                        `;

                    } else if (activeTab === 'nota') {
                        const isCurrentActive = (row.paper_type === notaPaper && parseInt(row.ply_count) ===
                            notaPly && parseInt(row.ink_count) === notaInk);
                        if (isCurrentActive) tr.className = 'row-active';

                        tr.innerHTML = `
                            <td style="text-align: center; color: var(--t-muted);">${rowNumber}</td>
                            <td>
                                <strong style="color: var(--t-base);">${row.name}</strong>
                                ${isCurrentActive ? '<span class="badge" style="background: var(--primary); color: #fff; font-size: 10px; font-weight: 700; padding: 2px 7px; border-radius: 4px; margin-left: 6px;"><i class="bi bi-check2"></i> Dipilih</span>' : ''}
                            </td>
                            <td>${row.paper_type === 'ncr' ? 'Kertas NCR' : 'Kertas HVS'}</td>
                            <td><span style="font-weight: 600;">${row.ply_label}</span></td>
                            <td>${row.ink_label}</td>
                            <td>${row.sheets_per_book} ${row.paper_type === 'ncr' ? 'Set' : 'Lembar'}</td>
                            <td style="text-align: right; font-weight: 700; color: var(--primary);">${formatRp(parseFloat(row.price))} / rim</td>
                            <td style="text-align: center;">
                                <button type="button" class="btn-table-pick" data-pick-nota="${row.id}" data-paper="${row.paper_type}" data-ply="${row.ply_count}" data-ink="${row.ink_count}">
                                    Pilih
                                </button>
                            </td>
                        `;
                    }

                    tbody.appendChild(tr);
                });

                // Attach event listener pada tombol "Pilih"
                document.querySelectorAll('.btn-table-pick').forEach(btn => {
                    btn.addEventListener('click', function() {
                        if (this.hasAttribute('data-pick-lanyard')) {
                            lanyardWidth = this.getAttribute('data-width');
                            lanyardPackage = this.getAttribute('data-pkg');
                            lanyardQty = parseInt(this.getAttribute('data-min')) || 10;
                            if (lanyardQtyInput) lanyardQtyInput.value = lanyardQty;

                            document.querySelector(
                                `input[name="lanyard_width"][value="${lanyardWidth}"]`)?.click();
                            document.querySelector(
                                    `input[name="lanyard_package"][value="${lanyardPackage}"]`)
                                ?.click();
                        } else if (this.hasAttribute('data-pick-nametag')) {
                            nametagType = this.getAttribute('data-pick-nametag');
                            document.querySelector(
                                `input[name="nametag_type"][value="${nametagType}"]`)?.click();
                        } else if (this.hasAttribute('data-pick-plakat')) {
                            const mat = this.getAttribute('data-mat');
                            document.querySelector(`input[name="plakat_material"][value="${mat}"]`)
                                ?.click();
                            if (mat === 'akrilik') {
                                const thk = this.getAttribute('data-thk');
                                const base = this.getAttribute('data-base');
                                const box = this.getAttribute('data-box');
                                document.querySelector(
                                    `input[name="plakat_thickness"][value="${thk}"]`)?.click();
                                document.querySelector(`input[name="plakat_base"][value="${base}"]`)
                                    ?.click();
                                document.querySelector(`input[name="plakat_box"][value="${box}"]`)
                                    ?.click();
                            }
                        } else if (this.hasAttribute('data-pick-nota')) {
                            const paper = this.getAttribute('data-paper');
                            const ply = this.getAttribute('data-ply');
                            const ink = this.getAttribute('data-ink');
                            document.querySelector(`input[name="nota_paper"][value="${paper}"]`)
                                ?.click();
                            document.querySelector(`input[name="nota_ply"][value="${ply}"]`)
                                ?.click();
                            document.querySelector(`input[name="nota_ink"][value="${ink}"]`)
                                ?.click();
                        }

                        // Smooth scroll ke atas form
                        document.querySelector('.merch-form-col')?.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    });
                });

                // Render Kontrol Navigasi Pagination
                renderPaginationControls(startIndex + 1, endIndex, totalItems, totalPages);
            }

            function renderPaginationControls(start, end, total, totalPages) {
                const info = document.getElementById('merchMatrixPageInfo');
                const controls = document.getElementById('merchMatrixPagination');
                if (!info || !controls) return;

                if (total === 0) {
                    info.textContent = 'Menampilkan 0 data';
                    controls.innerHTML = '';
                    return;
                }

                info.textContent = `Menampilkan ${start} - ${end} dari ${total} data`;
                controls.innerHTML = '';

                // Tombol Prev
                const btnPrev = document.createElement('button');
                btnPrev.type = 'button';
                btnPrev.className = 'btn-page-nav';
                btnPrev.innerHTML = '<i class="bi bi-chevron-left"></i>';
                btnPrev.disabled = (matrixCurrentPage <= 1);
                btnPrev.addEventListener('click', () => {
                    if (matrixCurrentPage > 1) {
                        matrixCurrentPage--;
                        renderMatrixTable();
                    }
                });
                controls.appendChild(btnPrev);

                // Tombol Angka Halaman
                for (let p = 1; p <= totalPages; p++) {
                    const btnPage = document.createElement('button');
                    btnPage.type = 'button';
                    btnPage.className = 'btn-page-nav' + (p === matrixCurrentPage ? ' active' : '');
                    btnPage.textContent = p;
                    btnPage.addEventListener('click', () => {
                        matrixCurrentPage = p;
                        renderMatrixTable();
                    });
                    controls.appendChild(btnPage);
                }

                // Tombol Next
                const btnNext = document.createElement('button');
                btnNext.type = 'button';
                btnNext.className = 'btn-page-nav';
                btnNext.innerHTML = '<i class="bi bi-chevron-right"></i>';
                btnNext.disabled = (matrixCurrentPage >= totalPages);
                btnNext.addEventListener('click', () => {
                    if (matrixCurrentPage < totalPages) {
                        matrixCurrentPage++;
                        renderMatrixTable();
                    }
                });
                controls.appendChild(btnNext);
            }

            // Listener Search & Page Size
            document.getElementById('merchMatrixSearch')?.addEventListener('input', function() {
                matrixCurrentPage = 1;
                renderMatrixTable();
            });
            document.getElementById('merchMatrixSearchClear')?.addEventListener('click', function() {
                const sInput = document.getElementById('merchMatrixSearch');
                if (sInput) sInput.value = '';
                matrixCurrentPage = 1;
                renderMatrixTable();
            });
            function handleMatrixPageSizeChange() {
                const sel = document.getElementById('merchMatrixPageSize');
                if (!sel) return;
                matrixPageSize = sel.value;
                matrixCurrentPage = 1;
                renderMatrixTable();
            }

            if (typeof $ !== 'undefined' && $.fn.select2) {
                $('#merchMatrixPageSize').on('change.select2 change', handleMatrixPageSizeChange);
            } else {
                document.getElementById('merchMatrixPageSize')?.addEventListener('change', handleMatrixPageSizeChange);
            }

            // Helper ambil nama pelanggan terpilih dari Select2 Cetakia
            function getSelectedCustomerName(fallback) {
                const hiddenVal = document.getElementById('merch_customer_name')?.value.trim();
                if (hiddenVal) return hiddenVal;
                const custSel = document.getElementById('merch_customer_id');
                if (custSel && custSel.value && custSel.selectedIndex >= 0) {
                    const opt = custSel.options[custSel.selectedIndex];
                    const dName = opt?.getAttribute('data-name') || opt?.textContent || '';
                    if (dName.trim()) return dName.trim();
                }
                return fallback;
            }

            // 8. Logika Modal Pop-up "Detail Estimasi"
            const modalDetail = document.getElementById('modalMerchandiseDetail');

            function openDetailEstimasiModal() {
                const custName = getSelectedCustomerName('Umum / Pelanggan Langsung');
                const custPhone = document.getElementById('merch_customer_phone')?.value.trim() || '-';
                const locSelect = document.getElementById('merch_location_id');
                const locName = locSelect ? locSelect.options[locSelect.selectedIndex].text : 'Cipta Grafika';
                const notes = document.getElementById('merch_custom_notes')?.value.trim() || '';

                const prodTitle = document.getElementById('summaryMerchProductTitle')?.textContent.trim() || '';
                const specs = document.getElementById('summaryMerchSpecs')?.textContent.trim() || '';
                const qtyText = document.getElementById('summaryMerchQty')?.textContent.trim() || '';
                const unitPrice = document.getElementById('summaryMerchUnitPrice')?.textContent.trim() || '';
                const grandTotal = document.getElementById('summaryMerchGrandTotal')?.textContent.trim() || '';
                const catBadge = document.getElementById('summaryMerchCategoryBadge')?.textContent.trim() || '';

                // Isi data modal
                document.getElementById('modalCustomerName').textContent = custName;
                document.getElementById('modalCustomerPhone').textContent = custPhone;
                document.getElementById('modalCustomerLocation').textContent = locName;

                const notesWrap = document.getElementById('modalNotesWrap');
                if (notes) {
                    notesWrap.style.display = 'block';
                    document.getElementById('modalCustomerNotes').textContent = notes;
                } else {
                    notesWrap.style.display = 'none';
                }

                document.getElementById('modalCategoryName').textContent = catBadge;
                document.getElementById('modalProductTitle').textContent = prodTitle;
                document.getElementById('modalQtyDisplay').textContent = qtyText;
                document.getElementById('modalSpecsDetail').textContent = specs;

                document.getElementById('modalTableItemName').textContent = prodTitle;
                document.getElementById('modalTableItemSpecs').textContent = specs;
                document.getElementById('modalTableItemQty').textContent = qtyText;
                document.getElementById('modalTableItemUnitPrice').textContent = unitPrice;

                // Hitung subtotal & biaya ekstra
                const rowExtra = document.getElementById('rowSummaryExtraFee');
                const extraRow = document.getElementById('modalTableExtraRow');
                if (rowExtra && rowExtra.style.display !== 'none') {
                    const extraLbl = document.getElementById('summaryMerchExtraLabel')?.textContent.trim() ||
                        'Jasa Tambahan';
                    const extraVal = document.getElementById('summaryMerchExtraFee')?.textContent.trim() || 'Rp 0';
                    extraRow.style.display = 'table-row';
                    document.getElementById('modalTableExtraLabel').textContent = extraLbl;
                    document.getElementById('modalTableExtraUnitPrice').textContent = extraVal;
                    document.getElementById('modalTableExtraSubtotal').textContent = extraVal;
                } else {
                    extraRow.style.display = 'none';
                }

                document.getElementById('modalTableItemSubtotal').textContent = grandTotal;
                document.getElementById('modalGrandTotal').textContent = grandTotal;

                // Tampilkan Modal
                modalDetail.classList.add('is-open');
                document.body.style.overflow = 'hidden';
            }

            function closeDetailEstimasiModal() {
                modalDetail.classList.remove('is-open');
                document.body.style.overflow = '';
            }

            // Tombol Trigger Modal
            document.getElementById('btnOpenDetailEstimasiModal')?.addEventListener('click',
                openDetailEstimasiModal);
            document.getElementById('btnCloseDetailModal')?.addEventListener('click', closeDetailEstimasiModal);
            document.getElementById('btnCancelDetailModal')?.addEventListener('click', closeDetailEstimasiModal);

            // Close on overlay click
            modalDetail?.addEventListener('click', function(e) {
                if (e.target === modalDetail) {
                    closeDetailEstimasiModal();
                }
            });

            // Close on Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modalDetail.classList.contains('is-open')) {
                    closeDetailEstimasiModal();
                }
            });

            // Toast Notification Helper (Modern, Elegant, No Browser Alert)
            function showMerchToast(title, desc) {
                let container = document.getElementById('merchToastContainer');
                if (!container) {
                    container = document.createElement('div');
                    container.id = 'merchToastContainer';
                    container.className = 'merch-toast-container';
                    document.body.appendChild(container);
                }

                const toast = document.createElement('div');
                toast.className = 'merch-toast';
                toast.innerHTML = `
                    <div class="merch-toast-icon">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div class="merch-toast-content">
                        <div class="merch-toast-title">${title}</div>
                        <div class="merch-toast-desc">${desc}</div>
                    </div>
                    <button type="button" class="merch-toast-close" aria-label="Tutup">&times;</button>
                `;

                container.appendChild(toast);

                // Smooth slide in
                requestAnimationFrame(() => {
                    toast.classList.add('is-show');
                });

                const closeFn = () => {
                    toast.classList.remove('is-show');
                    setTimeout(() => toast.remove(), 350);
                };

                toast.querySelector('.merch-toast-close').addEventListener('click', closeFn);

                // Auto dismiss after 3 seconds
                setTimeout(closeFn, 3000);
            }

            // Fallback clipboard method
            function fallbackCopyText(text, cb) {
                const ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.left = '-9999px';
                document.body.appendChild(ta);
                ta.focus();
                ta.select();
                try {
                    document.execCommand('copy');
                    if (cb) cb();
                } catch (err) {
                    console.error('Fallback copy error:', err);
                }
                document.body.removeChild(ta);
            }

            // 9. Tombol Salin Format WhatsApp
            function copyFormattedWhatsApp(e) {
                const btn = e ? e.currentTarget : null;
                const custName = getSelectedCustomerName('Pelanggan');
                const notes = document.getElementById('merch_custom_notes')?.value.trim() || '';
                const grandTotal = document.getElementById('summaryMerchGrandTotal')?.textContent || '';
                const perUnit = document.getElementById('summaryMerchPerUnit')?.textContent || '';
                const prodTitle = document.getElementById('summaryMerchProductTitle')?.textContent || '';
                const specs = document.getElementById('summaryMerchSpecs')?.textContent || '';
                const qty = document.getElementById('summaryMerchQty')?.textContent || '';

                const textLines = [
                    `*SURAT PENAWARAN HARGA (SPH) - CIPTA GRAFIKA*`,
                    `Kepada Yth: *${custName}*`,
                    `Tanggal: ${new Date().toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}`,
                    `---------------------------------------------`,
                    `*Produk:* ${prodTitle}`,
                    `*Spesifikasi:* ${specs}`,
                    `*Kuantitas:* ${qty}`,
                    `*Total Estimasi:* *${grandTotal}* ${perUnit}`,
                    notes ? `*Catatan Khusus:* ${notes}` : '',
                    `---------------------------------------------`,
                    `_Harga resmi terstandar dan berlaku selama penawaran masih aktif._`,
                    `Terima kasih telah mempercayakan kebutuhan percetakan & merchandise kepada Cipta Grafika!`
                ].filter(Boolean).join('\n');

                const onSuccess = () => {
                    // Tactile button feedback
                    if (btn) {
                        const originalHtml = btn.innerHTML;
                        btn.innerHTML = `<i class="bi bi-check-circle-fill"></i> <span>Tersalin ke Clipboard!</span>`;
                        btn.style.pointerEvents = 'none';
                        setTimeout(() => {
                            btn.innerHTML = originalHtml;
                            btn.style.pointerEvents = '';
                        }, 2200);
                    }

                    // Tampilkan notifikasi toast elegan
                    showMerchToast(
                        'Format Penawaran Disalin!',
                        'Format teks penawaran harga telah disalin ke clipboard dan siap dikirim ke WhatsApp.'
                    );
                };

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(textLines).then(onSuccess).catch(() => {
                        fallbackCopyText(textLines, onSuccess);
                    });
                } else {
                    fallbackCopyText(textLines, onSuccess);
                }
            }

            document.getElementById('btnCopyWaQuotation')?.addEventListener('click', copyFormattedWhatsApp);
            document.getElementById('btnModalCopyWa')?.addEventListener('click', copyFormattedWhatsApp);

            // 10. Tombol Cetak SPH
            document.getElementById('btnModalPrint')?.addEventListener('click', function() {
                window.print();
            });

            // 11. Tombol Reset Form (Konsisten dengan Kalender)
            document.getElementById('btnResetMerchForm')?.addEventListener('click', function() {
                // Reset Pelanggan, Kontak, Lokasi & Catatan
                const custSelect = document.getElementById('merch_customer_id');
                if (custSelect) custSelect.value = '';
                const custHidden = document.getElementById('merch_customer_name');
                if (custHidden) custHidden.value = '';
                const custPhone = document.getElementById('merch_customer_phone');
                if (custPhone) custPhone.value = '';
                const customNotes = document.getElementById('merch_custom_notes');
                if (customNotes) customNotes.value = '';

                const locSelect = document.getElementById('merch_location_id');
                if (locSelect && locSelect.options.length > 0) {
                    locSelect.selectedIndex = 0;
                }

                // Reset Lanyard
                document.querySelector('input[name="lanyard_width"][value="2cm"]')?.click();
                document.querySelector('input[name="lanyard_package"][value="set"]')?.click();
                lanyardQty = 25;
                if (lanyardQtyInput) lanyardQtyInput.value = 25;
                document.querySelectorAll('#panelLanyard .quick-qty-btn').forEach(b => {
                    b.classList.toggle('active', b.getAttribute('data-qty') === '25');
                });

                // Reset Nametag
                document.querySelector('input[name="nametag_type"][value="resin"]')?.click();
                document.querySelector('input[name="nametag_fastener"][value="magnet"]')?.click();
                nametagQty = 10;
                if (nametagQtyInput) nametagQtyInput.value = 10;
                document.querySelectorAll('#panelNametag .quick-qty-btn').forEach(b => {
                    b.classList.toggle('active', b.getAttribute('data-qty') === '10');
                });

                // Reset Plakat
                document.querySelector('input[name="plakat_material"][value="akrilik"]')?.click();
                document.querySelector('input[name="plakat_thickness"][value="3mm"]')?.click();
                document.querySelector('input[name="plakat_base"][value="akrilik"]')?.click();
                document.querySelector('input[name="plakat_box"][value="box"]')?.click();
                plakatQty = 1;
                if (plakatQtyInput) plakatQtyInput.value = 1;
                document.querySelectorAll('#panelPlakat .quick-qty-btn').forEach(b => {
                    b.classList.toggle('active', b.getAttribute('data-qty') === '1');
                });
                const chkPlakatDes = document.getElementById('checkPlakatDesign');
                if (chkPlakatDes) chkPlakatDes.checked = false;
                plakatDesignFee = false;

                // Reset Nota
                document.querySelector('input[name="nota_paper"][value="ncr"]')?.click();
                document.querySelector('input[name="nota_ply"][value="2"]')?.click();
                document.querySelector('input[name="nota_ink"][value="1"]')?.click();
                const notaSel = document.getElementById('nota_size_select');
                if (notaSel) notaSel.value = '1/4';
                notaSize = '1/4';
                notaBooksPerRim = 40;
                notaRimQty = 1;
                if (notaQtyInput) notaQtyInput.value = 1;
                document.querySelectorAll('#panelNota .quick-qty-btn').forEach(b => {
                    b.classList.toggle('active', b.getAttribute('data-qty') === '1');
                });
                const chkNotaNum = document.getElementById('checkNotaNumerator');
                if (chkNotaNum) chkNotaNum.checked = true;
                notaNumerator = true;
                const chkNotaDes = document.getElementById('checkNotaDesign');
                if (chkNotaDes) chkNotaDes.checked = false;
                notaDesignFee = false;

                if (typeof $ !== 'undefined' && $.fn.select2) {
                    $('#merch_customer_id').trigger('change.select2');
                    $('#merch_location_id').trigger('change.select2');
                    $('#nota_size_select').trigger('change.select2');
                }

                updateCalculation();
                renderMatrixTable();
                showMerchToast('Formulir Direset', 'Spesifikasi dan informasi pelanggan dikembalikan ke pengaturan awal.');
            });

            // Inisialisasi awal kalkulasi & tabel
            updateCalculation();
            renderMatrixTable();
        });
    </script>
@endsection

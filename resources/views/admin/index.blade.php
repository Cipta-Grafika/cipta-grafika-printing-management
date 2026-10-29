@extends('admin_master')

@section('contents')
    <style>
        /* =========================================================
           ESTIMATOR MENU GRID & THEMED CARDS (3 COLUMNS)
           ========================================================= */
        /* Desktop: 3 columns (>= 992px)
           Tablet: 2 columns (640px - 991px)
           Mobile: 1 column (< 640px) */
        .estimator-menu-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 24px;
        }

        @media (max-width: 991px) {
            .estimator-menu-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 18px;
            }
        }

        @media (max-width: 640px) {
            .estimator-menu-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }

        .estimator-card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 360px;
            border-radius: 16px;
            background: var(--bg-card);
            border: 1.5px solid var(--card-border);
            border-top: 3.5px solid var(--accent);
            overflow: hidden;
            box-shadow: 0 4px 18px var(--card-shadow);
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                        border-color 0.28s ease,
                        box-shadow 0.28s ease;
            box-sizing: border-box;
        }

        .estimator-card:hover {
            transform: translateY(-6px);
            border-color: var(--accent);
            box-shadow: 0 0 0 1px var(--accent), 0 20px 40px var(--accent-glow);
        }

        /* ---------------------------------------------------------
           THEME ACCENTS PER PRODUCT TYPE (CRISP VISIBLE BORDERS)
           --------------------------------------------------------- */
        /* Theme: Digital Print A3+ (Blue / Indigo) */
        .estimator-card.theme-digital {
            --accent: #3b82f6;
            --accent-glow: rgba(59, 130, 246, 0.28);
            --visual-bg: linear-gradient(135deg, rgba(59, 130, 246, 0.18) 0%, rgba(59, 130, 246, 0.04) 100%);
            --card-border: rgba(59, 130, 246, 0.35);
            --card-shadow: rgba(0, 0, 0, 0.3);
            --chip-bg: rgba(59, 130, 246, 0.1);
            --chip-border: rgba(59, 130, 246, 0.3);
            --chip-color: #93c5fd;
            --btn-bg: rgba(59, 130, 246, 0.12);
            --btn-border: rgba(59, 130, 246, 0.35);
            --btn-color: #60a5fa;
        }
        [data-theme="light"] .estimator-card.theme-digital {
            --card-border: rgba(59, 130, 246, 0.4);
            --card-shadow: rgba(59, 130, 246, 0.1);
            --chip-bg: rgba(59, 130, 246, 0.08);
            --chip-border: rgba(59, 130, 246, 0.28);
            --chip-color: #1d4ed8;
            --btn-bg: #eff6ff;
            --btn-border: rgba(59, 130, 246, 0.38);
            --btn-color: #1d4ed8;
        }

        /* Theme: Large Format (Cyan / Teal) */
        .estimator-card.theme-largeformat {
            --accent: #06b6d4;
            --accent-glow: rgba(6, 182, 212, 0.28);
            --visual-bg: linear-gradient(135deg, rgba(6, 182, 212, 0.18) 0%, rgba(6, 182, 212, 0.04) 100%);
            --card-border: rgba(6, 182, 212, 0.35);
            --card-shadow: rgba(0, 0, 0, 0.3);
            --chip-bg: rgba(6, 182, 212, 0.1);
            --chip-border: rgba(6, 182, 212, 0.3);
            --chip-color: #67e8f9;
            --btn-bg: rgba(6, 182, 212, 0.12);
            --btn-border: rgba(6, 182, 212, 0.35);
            --btn-color: #22d3ee;
        }
        [data-theme="light"] .estimator-card.theme-largeformat {
            --card-border: rgba(6, 182, 212, 0.4);
            --card-shadow: rgba(6, 182, 212, 0.1);
            --chip-bg: rgba(6, 182, 212, 0.08);
            --chip-border: rgba(6, 182, 212, 0.28);
            --chip-color: #0e7490;
            --btn-bg: #ecfeff;
            --btn-border: rgba(6, 182, 212, 0.38);
            --btn-color: #0e7490;
        }

        /* Theme: Offset / Brosur & Flyer (Emerald / Green) */
        .estimator-card.theme-offset {
            --accent: #10b981;
            --accent-glow: rgba(16, 185, 129, 0.28);
            --visual-bg: linear-gradient(135deg, rgba(16, 185, 129, 0.18) 0%, rgba(16, 185, 129, 0.04) 100%);
            --card-border: rgba(16, 185, 129, 0.35);
            --card-shadow: rgba(0, 0, 0, 0.3);
            --chip-bg: rgba(16, 185, 129, 0.1);
            --chip-border: rgba(16, 185, 129, 0.3);
            --chip-color: #6ee7b7;
            --btn-bg: rgba(16, 185, 129, 0.12);
            --btn-border: rgba(16, 185, 129, 0.35);
            --btn-color: #34d399;
        }
        [data-theme="light"] .estimator-card.theme-offset {
            --card-border: rgba(16, 185, 129, 0.4);
            --card-shadow: rgba(16, 185, 129, 0.1);
            --chip-bg: rgba(16, 185, 129, 0.08);
            --chip-border: rgba(16, 185, 129, 0.28);
            --chip-color: #047857;
            --btn-bg: #ecfdf5;
            --btn-border: rgba(16, 185, 129, 0.38);
            --btn-color: #047857;
        }

        /* Theme: Kalender (Purple / Violet) */
        .estimator-card.theme-calendar {
            --accent: #8b5cf6;
            --accent-glow: rgba(139, 92, 246, 0.28);
            --visual-bg: linear-gradient(135deg, rgba(139, 92, 246, 0.18) 0%, rgba(139, 92, 246, 0.04) 100%);
            --card-border: rgba(139, 92, 246, 0.35);
            --card-shadow: rgba(0, 0, 0, 0.3);
            --chip-bg: rgba(139, 92, 246, 0.1);
            --chip-border: rgba(139, 92, 246, 0.3);
            --chip-color: #c4b5fd;
            --btn-bg: rgba(139, 92, 246, 0.12);
            --btn-border: rgba(139, 92, 246, 0.35);
            --btn-color: #a78bfa;
        }
        [data-theme="light"] .estimator-card.theme-calendar {
            --card-border: rgba(139, 92, 246, 0.4);
            --card-shadow: rgba(139, 92, 246, 0.1);
            --chip-bg: rgba(139, 92, 246, 0.08);
            --chip-border: rgba(139, 92, 246, 0.28);
            --chip-color: #6d28d9;
            --btn-bg: #f5f3ff;
            --btn-border: rgba(139, 92, 246, 0.38);
            --btn-color: #6d28d9;
        }

        /* Default Fallback */
        .estimator-card.theme-default {
            --accent: #64748b;
            --accent-glow: rgba(100, 116, 139, 0.28);
            --visual-bg: linear-gradient(135deg, rgba(100, 116, 139, 0.14) 0%, rgba(100, 116, 139, 0.03) 100%);
            --card-border: rgba(100, 116, 139, 0.35);
            --card-shadow: rgba(0, 0, 0, 0.25);
            --chip-bg: rgba(100, 116, 139, 0.08);
            --chip-border: rgba(100, 116, 139, 0.25);
            --chip-color: #94a3b8;
            --btn-bg: rgba(100, 116, 139, 0.1);
            --btn-border: rgba(100, 116, 139, 0.3);
            --btn-color: #64748b;
        }

        /* ---------------------------------------------------------
           CARD VISUAL HEADER
           --------------------------------------------------------- */
        .estimator-card__visual {
            position: relative;
            height: 90px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            background: var(--visual-bg);
            border-bottom: 1.5px solid var(--card-border);
            overflow: hidden;
            transition: background 0.3s ease;
        }

        .estimator-card__icon-wrap {
            position: relative;
            z-index: 2;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--bg-card);
            border: 1.5px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent);
            box-shadow: 0 4px 14px var(--accent-glow);
            transition: transform 0.28s cubic-bezier(0.16, 1, 0.3, 1),
                        box-shadow 0.28s ease,
                        color 0.28s ease;
        }

        .estimator-card__icon-wrap svg {
            width: 24px;
            height: 24px;
            stroke: currentColor;
            stroke-width: 1.8;
            stroke-linecap: round;
            stroke-linejoin: round;
            transition: transform 0.28s ease;
        }

        .estimator-card:hover .estimator-card__icon-wrap {
            transform: scale(1.08);
            box-shadow: 0 8px 24px var(--accent-glow);
        }

        /* Watermark Background */
        .estimator-card__watermark {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 68px;
            color: var(--accent);
            opacity: 0.09;
            pointer-events: none;
            z-index: 1;
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
            line-height: 1;
        }

        .estimator-card:hover .estimator-card__watermark {
            transform: translateY(-50%) scale(1.15);
            opacity: 0.18;
        }

        /* Status Badge */
        .estimator-status-badge {
            position: relative;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10.5px;
            font-weight: 700;
            padding: 3.5px 9px;
            border-radius: 20px;
            background: rgba(16, 185, 129, 0.14);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.32);
            backdrop-filter: blur(6px);
            letter-spacing: 0.02em;
        }

        .estimator-status-badge .dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
        }

        .estimator-status-badge.is-inactive {
            background: rgba(239, 68, 68, 0.14);
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.32);
        }

        .estimator-status-badge.is-inactive .dot {
            background: #ef4444;
            box-shadow: 0 0 8px #ef4444;
        }

        /* ---------------------------------------------------------
           CARD CONTENT
           --------------------------------------------------------- */
        .estimator-card__content {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px 22px 22px;
            box-sizing: border-box;
        }

        .estimator-card__eyebrow {
            font-family: JetBrains Mono, monospace;
            font-size: 10.5px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 6px;
            line-height: 1.4;
        }

        .estimator-card__title {
            margin: 0 0 7px;
            font-size: 20px;
            font-weight: 800;
            color: var(--t-base);
            line-height: 1.25;
            transition: color 0.25s ease;
            letter-spacing: -0.01em;
            word-break: break-word;
        }

        .estimator-card:hover .estimator-card__title {
            color: var(--accent);
        }

        .estimator-card__description {
            margin: 0 0 16px;
            font-size: 13px;
            line-height: 1.6;
            color: var(--t-muted);
        }

        /* Specification Feature Chips */
        .estimator-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: auto;
            margin-bottom: 18px;
        }

        .estimator-chip {
            display: inline-flex;
            align-items: center;
            gap: 5.5px;
            font-size: 11.5px;
            font-weight: 600;
            padding: 4.5px 9.5px;
            border-radius: 6px;
            background: var(--chip-bg);
            color: var(--chip-color);
            border: 1px solid var(--chip-border);
            line-height: 1.35;
            white-space: nowrap;
        }

        .estimator-chip i {
            font-size: 12px;
            flex-shrink: 0;
        }

        .estimator-chip span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Action CTA Button */
        .estimator-card__action {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 9.5px 14px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--btn-color);
            background: var(--btn-bg);
            border: 1.5px solid var(--btn-border);
            text-decoration: none;
            transition: all 0.25s ease;
            box-sizing: border-box;
            line-height: 1.35;
        }

        .estimator-card__action .action-arrow {
            display: inline-flex;
            align-items: center;
            transition: transform 0.25s ease;
        }

        .estimator-card__action:hover {
            background: var(--accent);
            color: #ffffff !important;
            border-color: var(--accent);
            box-shadow: 0 6px 18px var(--accent-glow);
            transform: translateY(-2px);
        }

        .estimator-card__action:hover .action-arrow {
            transform: translateX(4px);
        }

        .estimator-card__action.is-disabled {
            opacity: 0.6;
            cursor: not-allowed;
            pointer-events: none;
            background: var(--bg-muted);
            border-color: var(--border);
            color: var(--t-muted);
        }
    </style>

    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">

                {{-- HERO HEADER --}}
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow" id="heroDate">Thursday · April 23 · 2026</span>
                        <h1 class="hero-title">Selamat datang, <span class="accent">{{ Auth::user()->name }}</span></h1>
                        <p class="hero-sub">
                            Buat dan kelola estimasi harga berdasarkan produk, material, proses produksi, serta komponen biaya yang tersedia.
                        </p>
                    </div>
                </section>

                {{-- ESTIMATOR MENU GRID (4 COLUMNS ON DESKTOP) --}}
                <section class="estimator-menu-grid" aria-label="Menu Estimasi Percetakan">

                    {{-- 1. ENGINES LOOP (Digital Print A3+, Large Format, etc.) --}}
                    @foreach ($engines as $engine)
                        @php
                            $isActive = strtolower($engine->status) === 'active';
                            $lowerName = strtolower(trim($engine->name));

                            if (str_contains($lowerName, 'digital')) {
                                $themeClass = 'theme-digital';
                                $eyebrow = 'DIGITAL PRESS';
                                $chips = [
                                    ['icon' => 'bi-aspect-ratio', 'label' => 'Area: 32.5 × 48.5 cm'],
                                    ['icon' => 'bi-layers', 'label' => 'Art Paper / Ivory / Vinyl'],
                                    ['icon' => 'bi-lightning-charge-fill', 'label' => 'Lembaran & Cetak Kilat'],
                                ];
                                $desc = 'Cetak cepat lembaran A3+ untuk brosur, flyer, kartu nama, & poster dengan simulasi material dan laminasi presisi.';
                                $watermarkIcon = 'bi-file-earmark-richtext';
                                $routeUrl = route('admin_create_a3plus_estimation');
                            } elseif (str_contains($lowerName, 'large') || str_contains($lowerName, 'format') || str_contains($lowerName, 'banner')) {
                                $themeClass = 'theme-largeformat';
                                $eyebrow = 'WIDE FORMAT';
                                $chips = [
                                    ['icon' => 'bi-rulers', 'label' => 'Hitungan: Meter (m²)'],
                                    ['icon' => 'bi-image', 'label' => 'Flexi 280-440g / Albatros'],
                                    ['icon' => 'bi-tools', 'label' => 'Mata Ayam & Selongsong'],
                                ];
                                $desc = 'Estimasi biaya cetak spanduk, banner, sticker ritrama, dan display promosi berbasis hitungan per meter persegi.';
                                $watermarkIcon = 'bi-badge-ad';
                                $routeUrl = route('admin_create_lf_estimation');
                            } else {
                                $themeClass = 'theme-default';
                                $eyebrow = 'ESTIMASI CETAK';
                                $chips = [
                                    ['icon' => 'bi-gear-wide-connected', 'label' => 'Mesin Produksi'],
                                    ['icon' => 'bi-calculator', 'label' => 'Kalkulasi Otomatis'],
                                    ['icon' => 'bi-check2-circle', 'label' => 'Siap Produksi'],
                                ];
                                $desc = 'Buat estimasi biaya untuk kebutuhan cetak menggunakan mesin ' . $engine->name . ' dengan konfigurasi produksi yang tersedia.';
                                $watermarkIcon = 'bi-printer';
                                $routeUrl = '#';
                            }
                        @endphp

                        <article class="estimator-card {{ $themeClass }}">

                            {{-- VISUAL HEADER --}}
                            <div class="estimator-card__visual">

                                <div class="estimator-card__icon-wrap">
                                    @if (str_contains($lowerName, 'large'))
                                        {{-- Large Format / Wide Format SVG Icon --}}
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path d="M4 6h16" />
                                            <path d="M4 18h16" />
                                            <path d="M6 6v12" />
                                            <path d="M18 6v12" />
                                            <path d="M8 9h8v6H8z" />
                                            <path d="M10 9v6" />
                                            <path d="M14 9v6" />
                                        </svg>
                                    @elseif (str_contains($lowerName, 'digital'))
                                        {{-- Digital Print A3+ Sheet SVG Icon --}}
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path d="M6 3h9l3 3v15H6z" />
                                            <path d="M15 3v4h4" />
                                            <path d="M9 11h6" />
                                            <path d="M9 14h6" />
                                            <path d="M9 17h4" />
                                        </svg>
                                    @else
                                        {{-- Default Printer SVG Icon --}}
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path d="M6 9V4h12v5" />
                                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                                            <path d="M6 14h12v7H6z" />
                                            <path d="M17 12h2" />
                                        </svg>
                                    @endif
                                </div>

                                {{-- Decorative Watermark --}}
                                <div class="estimator-card__watermark">
                                    <i class="bi {{ $watermarkIcon }}"></i>
                                </div>

                                {{-- Status Badge --}}
                                <div class="estimator-status-badge {{ $isActive ? '' : 'is-inactive' }}">
                                    <span class="dot"></span>
                                    <span>{{ $isActive ? 'Aktif' : 'Nonaktif' }}</span>
                                </div>

                            </div>

                            {{-- CONTENT AREA --}}
                            <div class="estimator-card__content">

                                <span class="estimator-card__eyebrow">{{ $eyebrow }}</span>

                                <h2 class="estimator-card__title">{{ $engine->name }}</h2>

                                <p class="estimator-card__description">{{ $desc }}</p>

                                {{-- FEATURE CHIPS --}}
                                <div class="estimator-chips">
                                    @foreach ($chips as $c)
                                        <span class="estimator-chip" title="{{ $c['label'] }}">
                                            <i class="bi {{ $c['icon'] }}"></i>
                                            <span>{{ $c['label'] }}</span>
                                        </span>
                                    @endforeach
                                </div>

                                {{-- ACTION BUTTON --}}
                                @if ($isActive)
                                    <a href="{{ $routeUrl }}" class="estimator-card__action">
                                        <span>Buat Estimasi</span>
                                        <span class="action-arrow">
                                            <i class="bi bi-arrow-right"></i>
                                        </span>
                                    </a>
                                @else
                                    <span class="estimator-card__action is-disabled" aria-disabled="true">
                                        <span>Nonaktif</span>
                                        <i class="bi bi-slash-circle"></i>
                                    </span>
                                @endif

                            </div>

                        </article>
                    @endforeach

                    {{-- 2. CETAK BROSUR & FLYER (OFFSET & DIGITAL HYBRID) --}}
                    <article class="estimator-card theme-offset">

                        <div class="estimator-card__visual">

                            <div class="estimator-card__icon-wrap">
                                {{-- Offset Press / Cylinder SVG Icon --}}
                                <svg viewBox="0 0 24 24" fill="none">
                                    <rect width="18" height="18" x="3" y="3" rx="3" />
                                    <circle cx="8.5" cy="8.5" r="1.5" />
                                    <circle cx="15.5" cy="8.5" r="1.5" />
                                    <circle cx="8.5" cy="15.5" r="1.5" />
                                    <circle cx="15.5" cy="15.5" r="1.5" />
                                </svg>
                            </div>

                            <div class="estimator-card__watermark">
                                <i class="bi bi-stack"></i>
                            </div>

                            <div class="estimator-status-badge">
                                <span class="dot"></span>
                                <span>Aktif</span>
                            </div>

                        </div>

                        <div class="estimator-card__content">

                            <span class="estimator-card__eyebrow">OFFSET &amp; DIGITAL</span>

                            <h2 class="estimator-card__title">Cetak Brosur &amp; Flyer</h2>

                            <p class="estimator-card__description">
                                Komparasi rute cerdas Digital A3+ (100–500 lbr) vs Mesin Offset (1–10+ rim) dengan simulasi matriks harga otomatis.
                            </p>

                            {{-- FEATURE CHIPS --}}
                            <div class="estimator-chips">
                                <span class="estimator-chip" title="Komparasi Digital vs Offset">
                                    <i class="bi bi-cpu"></i>
                                    <span>Komparasi Digital vs Offset</span>
                                </span>
                                <span class="estimator-chip" title="Simulasi Matriks Harga">
                                    <i class="bi bi-table"></i>
                                    <span>Simulasi Matriks Harga</span>
                                </span>
                                <span class="estimator-chip" title="Cetak Massal Skala Rim">
                                    <i class="bi bi-boxes"></i>
                                    <span>Cetak Massal Skala Rim</span>
                                </span>
                            </div>

                            {{-- ACTION BUTTON --}}
                            <a href="{{ route('admin_create_offset_estimation') }}" class="estimator-card__action">
                                <span>Buat Estimasi</span>
                                <span class="action-arrow">
                                    <i class="bi bi-arrow-right"></i>
                                </span>
                            </a>

                        </div>

                    </article>

                    {{-- 3. CETAK KALENDER (DINDING & MEJA SPECIALTY) --}}
                    <article class="estimator-card theme-calendar">

                        <div class="estimator-card__visual">

                            <div class="estimator-card__icon-wrap">
                                {{-- Calendar SVG Icon --}}
                                <svg viewBox="0 0 24 24" fill="none">
                                    <rect width="18" height="18" x="3" y="4" rx="2" ry="2" />
                                    <line x1="16" x2="16" y1="2" y2="6" />
                                    <line x1="8" x2="8" y1="2" y2="6" />
                                    <line x1="3" x2="21" y1="10" y2="10" />
                                    <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" />
                                </svg>
                            </div>

                            <div class="estimator-card__watermark">
                                <i class="bi bi-calendar3"></i>
                            </div>

                            <div class="estimator-status-badge">
                                <span class="dot"></span>
                                <span>Aktif</span>
                            </div>

                        </div>

                        <div class="estimator-card__content">

                            <span class="estimator-card__eyebrow">KALENDER &amp; BINDING</span>

                            <h2 class="estimator-card__title">Cetak Kalender</h2>

                            <p class="estimator-card__description">
                                Kalkulasi lengkap Kalender Dinding &amp; Meja dengan komparasi Digital vs Offset dan price list multi-tier siap salin WA.
                            </p>

                            {{-- FEATURE CHIPS --}}
                            <div class="estimator-chips">
                                <span class="estimator-chip" title="Kalender Dinding & Meja">
                                    <i class="bi bi-calendar-check"></i>
                                    <span>Kalender Dinding &amp; Meja</span>
                                </span>
                                <span class="estimator-chip" title="Spiral Kawat & Klem Seng">
                                    <i class="bi bi-paperclip"></i>
                                    <span>Spiral Kawat &amp; Klem Seng</span>
                                </span>
                                <span class="estimator-chip" title="Multi-Tier Simulasi Salin WA">
                                    <i class="bi bi-whatsapp"></i>
                                    <span>Multi-Tier Salin WA</span>
                                </span>
                            </div>

                            {{-- ACTION BUTTON --}}
                            <a href="{{ route('admin_create_calendar_estimation') }}" class="estimator-card__action">
                                <span>Buat Estimasi</span>
                                <span class="action-arrow">
                                    <i class="bi bi-arrow-right"></i>
                                </span>
                            </a>

                        </div>

                    </article>

                </section>

            </main>
            <div data-shell-footer></div>
        </div>
    </div>
@endsection

@extends('admin_master')

@section('contents')
    @php
        $formatPercentage = fn($value) => rtrim(rtrim(number_format((float) $value, 2, ',', '.'), '0'), ',') . '%';
        $formatDatetime = fn($value) => $value ? $value->locale('id')->translatedFormat('d M Y, H:i') . ' WIB' : '-';
        $periodStates = [
            'upcoming' => ['label' => 'Terjadwal', 'class' => 'info'],
            'running' => ['label' => 'Berlangsung', 'class' => 'success'],
            'expired' => ['label' => 'Berakhir', 'class' => 'warning'],
        ];
        $periodState = $periodStates[$summary->period_state];
    @endphp
    <div class="shell">
        <div data-shell-sidebar></div>
        <div class="main">
            <div data-shell-topbar></div>
            <main class="content">
                <section class="hero">
                    <div class="hero-text">
                        <span class="eyebrow">KONFIGURASI · DISKON</span>
                        <p class="hero-sub">Detail diskon beserta periode berlaku dan cakupan mesin serta kategori produk.</p>
                    </div>
                </section>
                <div class="grid">
                    <section class="col-12 card discount-details">
                        <div class="card-head">
                            <div class="card-title-wrap">
                                <span class="eyebrow">Detail Diskon</span>
                            </div>
                        </div>

                        <div class="discount-details-hero">
                            <div class="discount-details-percentage">
                                <span>{{ $formatPercentage($discount->discount_percentage) }}</span>
                                <small>Diskon</small>
                            </div>
                            <div class="discount-details-heading">
                                <h3 class="discount-details-name">{{ $discount->name }}</h3>
                                <p class="discount-details-description">
                                    <span class="discount-details-stat-label">Kode Promo:</span>
                                    <strong>{{ $discount->code }}</strong>
                                </p>
                                <div class="discount-details-badges">
                                    <span class="badge {{ $discount->status === 'Active' ? 'success' : 'danger' }} dot"
                                        style="font-weight: 400;">{{ $discount->status }}</span>
                                    <span class="badge {{ $periodState['class'] }}" style="font-weight: 400;">
                                        {{ $periodState['label'] }}
                                    </span>
                                </div>
                                <p class="discount-details-description {{ $discount->description ? '' : 'is-empty' }}">
                                    {{ $discount->description ?: 'Tidak ada deskripsi.' }}
                                </p>
                            </div>
                        </div>

                        <div class="discount-details-stats">
                            <div class="discount-details-stat">
                                <span class="discount-details-stat-label">Mulai Berlaku</span>
                                <span class="discount-details-stat-value">{{ $formatDatetime($summary->starts_at) }}</span>
                            </div>
                            <div class="discount-details-stat">
                                <span class="discount-details-stat-label">Berakhir</span>
                                <span class="discount-details-stat-value">{{ $formatDatetime($summary->ends_at) }}</span>
                            </div>
                            <div class="discount-details-stat">
                                <span class="discount-details-stat-label">Durasi</span>
                                <span class="discount-details-stat-value">{{ $summary->duration_days }} hari</span>
                                @if ($summary->days_left !== null)
                                    <span class="discount-details-stat-hint">Sisa {{ $summary->days_left }} hari lagi</span>
                                @elseif ($summary->days_to_start !== null)
                                    <span class="discount-details-stat-hint">Dimulai {{ $summary->days_to_start }} hari lagi</span>
                                @else
                                    <span class="discount-details-stat-hint">Periode sudah berakhir</span>
                                @endif
                            </div>
                            <div class="discount-details-stat">
                                <span class="discount-details-stat-label">Cakupan</span>
                                <span class="discount-details-stat-value">
                                    {{ $engines->count() }} mesin · {{ $summary->category_total }} kategori
                                </span>
                            </div>
                        </div>

                        <div class="discount-details-section">
                            <div class="discount-details-section-head">
                                <span class="eyebrow">Cakupan Diskon</span>
                            </div>

                            @forelse ($engines as $engine)
                                <div class="discount-details-engine">
                                    <div class="discount-details-engine-head">
                                        <span class="discount-details-engine-name">{{ $engine->name }}</span>
                                        <span class="discount-details-engine-meta">
                                            @unless ($engine->is_active)
                                                <span class="badge danger" style="font-weight: 400;">Mesin nonaktif</span>
                                            @endunless
                                            {{ $engine->categories->count() }} kategori
                                        </span>
                                    </div>
                                    <div class="discount-details-categories">
                                        @foreach ($engine->categories as $category)
                                            <span class="discount-details-category {{ $category->available ? '' : 'is-unavailable' }}"
                                                @unless ($category->available) title="Tidak lagi memiliki aturan harga aktif" @endunless>
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <span class="details-empty">Belum ada cakupan.</span>
                            @endforelse
                        </div>

                        <div class="discount-details-section">
                            <div class="discount-details-section-head">
                                <span class="eyebrow">Riwayat</span>
                            </div>
                            <div class="discount-details-audit">
                                <div class="discount-details-audit-item">
                                    <span class="discount-details-stat-label">Dibuat Oleh</span>
                                    <span class="discount-details-stat-value">{{ $discount->created_by ?: '-' }}</span>
                                    <span class="discount-details-stat-hint">{{ $formatDatetime($summary->created_at) }}</span>
                                </div>
                                <div class="discount-details-audit-item">
                                    <span class="discount-details-stat-label">Terakhir Diperbarui</span>
                                    <span class="discount-details-stat-value">{{ $discount->updated_by ?: '-' }}</span>
                                    <span class="discount-details-stat-hint">
                                        {{ $summary->updated_at ? $formatDatetime($summary->updated_at) : 'Belum pernah diperbarui' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <span class="spacer"></span>
                            <a href="{{ route('admin_discounts') }}" class="btn btn--danger">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </section>
                </div>
            </main>
            <div data-shell-footer></div>
        </div>
    </div>
@endsection

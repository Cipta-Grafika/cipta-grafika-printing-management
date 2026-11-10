@php
    $merchTabs = [
        ['suffix' => 'Lanyard', 'pane' => 'Lanyard', 'type' => 'lanyard', 'name' => 'tier_label',
            'icon' => 'bi-award', 'title' => 'Daftar Tier Lanyard & ID Card',
            'desc' => 'Lebar tali, jenis paket, rentang kuantitas pesanan, dan harga satuan lanyard.',
            'entity' => 'Tier Lanyard', 'add' => 'Tambah Tier Lanyard', 'modal' => 'Lanyard', 'order' => 5,
            'badge' => 'badgeCountLanyard', 'stat' => 'statTotalLanyard', 'unit' => 'Tier',
            'variants' => ['width_2cm' => 'Lebar 2.0 cm', 'width_25cm' => 'Lebar 2.5 cm', 'tali' => 'Hanya Tali', 'set' => '1 Set Komplit'],
            'columns' => ['Lebar Tali', 'Jenis Paket', 'Rentang Kuantiti (Tier)', 'Min Qty', 'Max Qty', 'Harga Satuan', 'Status']],
        ['suffix' => 'NameTag', 'pane' => 'PapanNama', 'type' => 'nametag', 'name' => 'name',
            'icon' => 'bi-person-badge', 'title' => 'Daftar Papan Nama (Name Tag)',
            'desc' => 'Varian bahan papan nama, pilihan pengait, minimal order, dan harga satuan.',
            'entity' => 'Papan Nama', 'add' => 'Tambah Varian Papan Nama', 'modal' => 'NameTag', 'order' => 2,
            'badge' => 'badgeCountPapanNama', 'stat' => 'statTotalNameTags', 'unit' => 'Varian',
            'variants' => ['biasa' => 'Akrilik Biasa', 'resin' => 'Resin Cembung', 'resin_emas' => 'Resin Emas'],
            'columns' => ['Nama Varian Papan Nama', 'Tipe Bahan', 'Pilihan Pengait', 'Min. Order', 'Harga Satuan', 'Status']],
        ['suffix' => 'Plakat', 'pane' => 'Plakat', 'type' => 'plakat', 'name' => 'name',
            'icon' => 'bi-trophy', 'title' => 'Daftar Plakat Akrilik & Kayu',
            'desc' => 'Bahan, ketebalan, dudukan, kemasan, ukuran, dan harga varian plakat.',
            'entity' => 'Plakat', 'add' => 'Tambah Varian Plakat', 'modal' => 'Plakat', 'order' => 2,
            'badge' => 'badgeCountPlakat', 'stat' => 'statTotalPlakats', 'unit' => 'Tipe',
            'variants' => ['tanpa_box' => 'Tanpa Box', 'box' => 'Box Beludru', 'kayu' => 'Plakat Kayu'],
            'columns' => ['Nama Varian Plakat', 'Bahan', 'Tebal', 'Dudukan', 'Kemasan', 'Ukuran', 'Harga Satuan', 'Status']],
        ['suffix' => 'Nota', 'pane' => 'Nota', 'type' => 'nota', 'name' => 'name',
            'icon' => 'bi-journal-text', 'title' => 'Daftar Buku Nota (NCR & HVS)',
            'desc' => 'Jenis kertas, rangkap, warna tinta, isi buku, dan harga paket rim cetak.',
            'entity' => 'Buku Nota', 'add' => 'Tambah Varian Nota', 'modal' => 'Nota', 'order' => 2,
            'badge' => 'badgeCountNota', 'stat' => 'statTotalNotas', 'unit' => 'Varian',
            'variants' => ['ncr' => 'NCR Tembus', 'hvs' => 'HVS Putih', 'ink_1' => '1 Tinta', 'ink_2' => '2 Tinta', 'ink_3' => '3 Tinta'],
            'columns' => ['Nama Varian Buku Nota', 'Bahan Kertas', 'Rangkap (Ply)', 'Warna Tinta', 'Isi / Buku', 'Harga Satuan (Per Rim)', 'Status']],
        ['suffix' => 'Mug', 'pane' => 'Mug', 'type' => 'mug', 'name' => 'tier_label',
            'icon' => 'bi-cup-hot', 'title' => 'Daftar Tier Mug Sublimasi',
            'desc' => 'Rentang kuantitas pesanan dan harga satuan cetak mug keramik sublimasi SNI.',
            'entity' => 'Tier Mug', 'add' => 'Tambah Tier Mug', 'modal' => 'Mug', 'order' => 3,
            'badge' => 'badgeCountMug', 'stat' => 'statTotalMugs', 'unit' => 'Tier',
            'variants' => [],
            'columns' => ['Rentang Kuantiti (Tier)', 'Min Qty', 'Max Qty', 'Harga Satuan', 'Status']],
        ['suffix' => 'PvcCard', 'pane' => 'PvcCard', 'type' => 'pvccard', 'name' => 'tier_label',
            'icon' => 'bi-credit-card-2-front', 'title' => 'Daftar Tier Kartu PVC (ID Card / KTA)',
            'desc' => 'Rentang kuantitas pesanan dan harga satuan cetak kartu PVC standar ATM.',
            'entity' => 'Tier Kartu PVC', 'add' => 'Tambah Tier PVC', 'modal' => 'PvcCard', 'order' => 3,
            'badge' => 'badgeCountPvcCard', 'stat' => 'statTotalPvcCards', 'unit' => 'Tier',
            'variants' => [],
            'columns' => ['Rentang Kuantiti (Tier)', 'Min Qty', 'Max Qty', 'Harga Satuan', 'Status']],
        ['suffix' => 'AkrilikUv', 'pane' => 'AkrilikUv', 'type' => 'akrilikuv', 'name' => 'name',
            'icon' => 'bi-shield-shaded', 'title' => 'Daftar Bahan Akrilik UV (2 Muka 1 Lapis)',
            'desc' => 'Varian merchandise cetak akrilik UV 2 muka 1 lapis dengan potong custom presisi.',
            'entity' => 'Akrilik UV', 'add' => 'Tambah Varian Akrilik UV', 'modal' => 'AkrilikUv', 'order' => 2,
            'badge' => 'badgeCountAkrilikUv', 'stat' => 'statTotalAkrilikUv', 'unit' => 'Varian',
            'variants' => [],
            'columns' => ['Nama Produk Akrilik UV', 'Spesifikasi Lapisan', 'Ketebalan', 'Min. Order', 'Harga Satuan', 'Status']],
    ];
@endphp
@foreach ($merchTabs as $tab)
    @php $p = 'merch' . $tab['suffix']; @endphp
    <div id="tabContent{{ $tab['pane'] }}" class="merch-tab-pane" style="margin-top:16px;{{ $loop->first ? '' : 'display:none;' }}">
        <section class="card cat-card-container" data-merchandise-type="{{ $tab['type'] }}"
            data-prefix="{{ $p }}" data-entity="{{ $tab['entity'] }}" data-name-field="{{ $tab['name'] }}"
            data-default-order="{{ $tab['order'] }}" data-url="{{ route('admin_data_merchandise', $tab['type']) }}"
            data-badge="{{ $tab['badge'] }}" data-stat="{{ $tab['stat'] }}" data-unit="{{ $tab['unit'] }}">
            <div class="cat-card-header">
                <div class="cat-card-header-main">
                    <div class="cat-card-title-wrap">
                        <div class="cat-card-icon"><i class="bi {{ $tab['icon'] }}"></i></div>
                        <div>
                            <div class="cat-title-row">
                                <h3 class="cat-card-title">{{ $tab['title'] }}</h3>
                                <span class="cat-badge-total"><span class="cat-pulse-dot"></span><span id="{{ $p }}TotalCount">Memuat data...</span></span>
                            </div>
                            <p class="cat-card-desc">{{ $tab['desc'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="cat-card-actions">
                    <button type="button" class="cat-btn-head-primary" onclick="openCreate{{ $tab['modal'] }}Modal()">
                        <i class="bi bi-plus-lg"></i><span>{{ $tab['add'] }}</span>
                    </button>
                </div>
            </div>
            <div class="cat-bulk-toolbar" id="{{ $p }}BulkToolbar" style="display:none;">
                <div class="cat-bulk-info">
                    <span class="cat-bulk-badge" id="{{ $p }}BulkCount">0 terpilih</span>
                    <span class="cat-bulk-text">{{ $tab['entity'] }} terpilih siap untuk tindakan massal</span>
                </div>
                <div class="cat-bulk-actions">
                    <button type="button" class="btn btn--ghost btn--sm" id="{{ $p }}CancelSelection"><i class="bi bi-x-circle me-1"></i>Batal Pilihan</button>
                    <button type="button" class="btn btn--danger btn--sm" id="{{ $p }}BulkDelete"><i class="bi bi-trash3-fill me-1"></i>Hapus Terpilih</button>
                </div>
            </div>
            <div class="cat-filter-card">
                <div class="cat-filter-group-left">
                    <div class="cat-search-box">
                        <i class="bi bi-search cat-search-icon"></i>
                        <input type="text" id="{{ $p }}Search" class="cat-filter-input" placeholder="Cari {{ strtolower($tab['entity']) }}..." aria-label="Cari {{ strtolower($tab['entity']) }}">
                        <button type="button" id="{{ $p }}ClearSearch" class="cat-clear-btn" title="Hapus pencarian" aria-label="Hapus pencarian" style="display:none;"><i class="bi bi-x-circle-fill"></i></button>
                    </div>
                    <div class="cat-select-box">
                        <i class="bi bi-funnel cat-select-icon"></i>
                        <select id="{{ $p }}Variant" aria-label="Filter varian">
                            <option value="all">Semua Varian</option>
                            @foreach ($tab['variants'] as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cat-select-box">
                        <i class="bi bi-toggle-on cat-select-icon"></i>
                        <select id="{{ $p }}Status" aria-label="Filter status">
                            <option value="all">Semua Status</option><option value="active">Active</option><option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="cat-filter-subgroup">
                        <div class="cat-select-box">
                            <i class="bi bi-calendar3 cat-select-icon"></i>
                            <select id="{{ $p }}Period" aria-label="Filter periode">
                                <option value="all">Semua Periode</option><option value="today">Hari Ini</option>
                                <option value="7_days">7 Hari Terakhir</option><option value="month">Bulan Ini</option><option value="year">Tahun Ini</option>
                            </select>
                        </div>
                        <button type="button" id="{{ $p }}Reset" class="cat-btn-reset" title="Reset semua filter"><i class="bi bi-arrow-counterclockwise"></i><span>Reset</span></button>
                    </div>
                </div>
                <div class="cat-filter-group-right">
                    <div class="cat-length-box">
                        <span class="cat-length-label">Baris:</span>
                        <select id="{{ $p }}PageLength" aria-label="Jumlah baris">
                            <option value="10">10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="cat-table-wrap">
                <table id="{{ $p }}Table" class="cat-modern-table" style="width:100%;">
                    <thead><tr>
                        <th class="cat-th-check text-center" style="width:52px;">
                            <label class="cat-custom-checkbox">
                                <input type="checkbox" id="{{ $p }}CheckAll" aria-label="Pilih semua {{ strtolower($tab['entity']) }} di halaman ini">
                                <span class="cat-checkbox-mark">
                                    <svg class="check-icon" viewBox="0 0 14 14" fill="none"><path d="M2.5 7.5L5.5 10.5L11.5 3.5" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <svg class="minus-icon" viewBox="0 0 14 14" fill="none"><path d="M3 7H11" stroke="#ffffff" stroke-width="2.2" stroke-linecap="round"/></svg>
                                </span>
                            </label>
                        </th>
                        <th class="cat-th-no text-center" style="width:60px;">No</th>
                        @foreach ($tab['columns'] as $column)
                            <th class="{{ str_starts_with($column, 'Nama') || str_starts_with($column, 'Rentang') ? 'cat-th-name' : 'cat-th-date text-center' }}"
                                style="width:{{ str_starts_with($column, 'Nama') || str_starts_with($column, 'Rentang') ? 250 : 150 }}px;">{{ $column }}</th>
                        @endforeach
                        <th class="cat-th-actions text-center" style="width:130px;">Aksi</th>
                    </tr></thead>
                    <tbody></tbody>
                </table>
            </div>
        </section>
    </div>
@endforeach

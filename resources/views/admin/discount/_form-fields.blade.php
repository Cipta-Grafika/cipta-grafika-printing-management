@php
    /** @var string $prefix */
    /** @var bool $reopen */
    $fieldError = fn($field) => $reopen ? $errors->first($field) : null;
    $oldValue = fn($field, $default = '') => $reopen ? old($field, $default) : $default;
    $oldScopes = $reopen ? array_map('strval', (array) old('scopes', [])) : [];
    $scopeError = $reopen ? ($errors->first('scopes') ?: $errors->first('scopes.*')) : null;
    $optionValues = collect($scopeOptions ?? [])
        ->flatMap(fn($engine) => collect($engine->categories)->map(fn($category) => $engine->id . ':' . $category->id))
        ->all();
    $legacyScopes = array_values(array_diff($oldScopes, $optionValues));
    $oldStatus = $oldValue('status', 'Active');
@endphp

<div class="discount-form-grid">
    <div class="discount-field discount-field--full">
        <label class="category-label" for="{{ $prefix }}Code">Kode Diskon</label>
        <input type="text" id="{{ $prefix }}Code" name="code" minlength="3" maxlength="30"
            class="input {{ $fieldError('code') ? 'is-invalid' : '' }}" value="{{ $oldValue('code') }}"
            placeholder="Contoh: PROMO-AGUSTUS26" autocomplete="off" autocapitalize="characters"
            spellcheck="false" required>
        <p class="category-description">3–30 karakter: huruf, angka, tanda hubung (-), atau garis bawah (_). Tanpa spasi.</p>
        <div class="field-error" data-error-for="code">{{ $fieldError('code') }}</div>
    </div>

    <div class="discount-field discount-field--full">
        <label class="category-label" for="{{ $prefix }}Name">Nama Diskon</label>
        <input type="text" id="{{ $prefix }}Name" name="name" maxlength="255"
            class="input {{ $fieldError('name') ? 'is-invalid' : '' }}" value="{{ $oldValue('name') }}"
            placeholder="Contoh: Promo Kemerdekaan RI 2026" required>
        <div class="field-error" data-error-for="name">{{ $fieldError('name') }}</div>
    </div>

    <div class="discount-field">
        <label class="category-label" for="{{ $prefix }}Percentage">Persentase Diskon</label>
        <div class="discount-percentage-input">
            <input type="text" id="{{ $prefix }}Percentage" name="discount_percentage" inputmode="decimal"
                maxlength="6" autocomplete="off"
                class="input {{ $fieldError('discount_percentage') ? 'is-invalid' : '' }}"
                value="{{ $oldValue('discount_percentage') }}" placeholder="Contoh: 17,5" required>
            <span>%</span>
        </div>
        <div class="field-error" data-error-for="discount_percentage">{{ $fieldError('discount_percentage') }}</div>
    </div>

    <div class="discount-field">
        <label class="category-label" for="{{ $prefix }}Status">Status</label>
        <select id="{{ $prefix }}Status" name="status" class="select select2" data-discount-status required>
            <option value="Active" @selected($oldStatus === 'Active')>Active</option>
            <option value="Inactive" @selected($oldStatus === 'Inactive')>Inactive</option>
        </select>
        <div class="field-error" data-error-for="status">{{ $fieldError('status') }}</div>
    </div>

    <div class="discount-field">
        <label class="category-label" for="{{ $prefix }}StartsAt">Mulai Berlaku</label>
        <input type="datetime-local" id="{{ $prefix }}StartsAt" name="starts_at"
            class="input {{ $fieldError('starts_at') ? 'is-invalid' : '' }}" value="{{ $oldValue('starts_at') }}"
            required>
        <div class="field-error" data-error-for="starts_at">{{ $fieldError('starts_at') }}</div>
    </div>

    <div class="discount-field">
        <label class="category-label" for="{{ $prefix }}EndsAt">Berakhir</label>
        <input type="datetime-local" id="{{ $prefix }}EndsAt" name="ends_at"
            class="input {{ $fieldError('ends_at') ? 'is-invalid' : '' }}" value="{{ $oldValue('ends_at') }}"
            required>
        <div class="field-error" data-error-for="ends_at">{{ $fieldError('ends_at') }}</div>
    </div>

    <div class="discount-field discount-field--full">
        <label class="category-label" for="{{ $prefix }}Description">
            Deskripsi <span class="discount-optional">(opsional)</span>
        </label>
        <textarea id="{{ $prefix }}Description" name="description" maxlength="1000" rows="3"
            class="textarea {{ $fieldError('description') ? 'is-invalid' : '' }}" placeholder="Keterangan singkat promo...">{{ $oldValue('description') }}</textarea>
        <div class="field-error" data-error-for="description">{{ $fieldError('description') }}</div>
    </div>

    <div class="discount-field discount-field--full">
        <div class="discount-scope-header">
            <div>
                <label class="category-label">Cakupan Diskon</label>
                <p class="category-description">
                    Pilih mesin dan kategori yang mendapatkan diskon ini.
                </p>
            </div>
            <span class="discount-scope-total" id="{{ $prefix }}ScopeTotal" data-scope-total>0 dipilih</span>
        </div>

        <div class="discount-scope-picker {{ $scopeError ? 'is-invalid' : '' }}" id="{{ $prefix }}ScopePicker"
            data-scope-picker>
            @forelse ($scopeOptions ?? [] as $engine)
                <div class="discount-scope-group" data-scope-group>
                    <div class="discount-scope-group-head">
                        <label class="discount-scope-all">
                            <input type="checkbox" data-scope-all>
                            <span>{{ $engine->name }}</span>
                        </label>
                        <span class="discount-scope-group-count" data-scope-count>
                            0/{{ count($engine->categories) }}
                        </span>
                    </div>
                    <div class="discount-scope-options">
                        @foreach ($engine->categories as $category)
                            @php $scopeValue = $engine->id . ':' . $category->id; @endphp
                            <label class="discount-scope-option">
                                <input type="checkbox" name="scopes[]" value="{{ $scopeValue }}" data-scope-item
                                    @checked(in_array($scopeValue, $oldScopes, true))>
                                <span>{{ $category->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @empty
                <p class="discount-scope-empty-state">
                    Belum ada mesin dan kategori yang memiliki aturan harga (markup). Tambahkan
                    aturan harga terlebih dahulu.
                </p>
            @endforelse
        </div>

        {{-- Cakupan lama dari mesin/kategori yang sudah tidak tersedia, dipertahankan saat edit. --}}
        <div class="discount-scope-legacy" data-scope-legacy @if (empty($legacyScopes)) hidden @endif>
            <span data-scope-legacy-text>
                {{ count($legacyScopes) }} cakupan lama dari mesin yang sudah tidak aktif tetap dipertahankan.
            </span>
            <div data-scope-legacy-inputs>
                @foreach ($legacyScopes as $legacyScope)
                    <input type="hidden" name="scopes[]" value="{{ $legacyScope }}">
                @endforeach
            </div>
        </div>

        <div class="field-error" data-error-for="scopes">{{ $scopeError }}</div>
    </div>
</div>

<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DiscountCT extends Controller
{
    private const TIMEZONE = 'Asia/Jakarta';

    private const INPUT_DATETIME_FORMAT = 'Y-m-d\TH:i';

    /**
     * Pasangan mesin–kategori yang dianggap valid adalah pasangan yang sudah
     * memiliki aturan harga (markup) di salah satu master berikut.
     */
    private const SCOPE_SOURCE_TABLES = [
        'm_pricing_rules',
        'm_lamination_pricing_rules',
        'm_display_pricing_rules',
        'm_digital_pricing_rules',
        'm_digital_lamination_pricing_rules',
    ];

    public function index()
    {
        $scopeOptions = $this->availableScopePairs()
            ->groupBy('engine_id')
            ->map(fn ($pairs) => (object) [
                'id' => $pairs->first()->engine_id,
                'name' => $pairs->first()->engine_name,
                'categories' => $pairs->map(fn ($pair) => (object) [
                    'id' => $pair->category_id,
                    'name' => $pair->category_name,
                ])->values(),
            ])
            ->values();

        return view('admin.discount.index', compact('scopeOptions'));
    }

    public function store(Request $request)
    {
        [$validated, $pairs, $startsAt, $endsAt] = $this->validateDiscountInput($request);

        DB::transaction(function () use ($validated, $pairs, $startsAt, $endsAt) {
            $this->lockEngines($pairs);
            $this->ensureUniqueName($validated['name']);
            $this->ensureUniqueCode($validated['code']);

            if ($validated['status'] === 'Active') {
                $this->ensureNoActiveOverlap($pairs, $startsAt, $endsAt);
            }

            try {
                $discountId = DB::table('m_discounts')->insertGetId([
                    'code' => $validated['code'],
                    'name' => $validated['name'],
                    'description' => $validated['description'] !== '' ? $validated['description'] : null,
                    'discount_percentage' => round((float) $validated['discount_percentage'], 2),
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'status' => $validated['status'],
                    'created_by' => Auth::user()->name,
                    'created_at' => now(),
                    'updated_by' => null,
                    'updated_at' => null,
                ]);
            } catch (UniqueConstraintViolationException $exception) {
                $this->handleCodeConstraintViolation($exception);
            }

            $this->insertScopes($discountId, $pairs);
        });

        return redirect()
            ->route('admin_discounts')
            ->with('success', 'Diskon "' . $validated['name'] . '" berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $discount = DB::table('m_discounts')->where('id', (int) $id)->first();

        if (!$discount) {
            return response()->json(['message' => 'Data diskon tidak ditemukan.'], 404);
        }

        $availableKeys = $this->availableScopePairs()
            ->map(fn ($pair) => $pair->engine_id . ':' . $pair->category_id)
            ->flip();

        $scopes = DB::table('m_discount_scopes')
            ->join('m_engines', 'm_engines.id', '=', 'm_discount_scopes.engine_id')
            ->join('m_categories', 'm_categories.id', '=', 'm_discount_scopes.category_id')
            ->where('m_discount_scopes.discount_id', $discount->id)
            ->orderBy('m_engines.name')
            ->orderBy('m_categories.name')
            ->get([
                'm_discount_scopes.engine_id',
                'm_discount_scopes.category_id',
                'm_engines.name as engine_name',
                'm_categories.name as category_name',
            ])
            ->map(function ($scope) use ($availableKeys) {
                $value = $scope->engine_id . ':' . $scope->category_id;

                return [
                    'value' => $value,
                    'label' => $scope->engine_name . ' – ' . $scope->category_name,
                    'available' => $availableKeys->has($value),
                ];
            })
            ->values();

        return response()->json([
            'id' => (int) $discount->id,
            'code' => $discount->code,
            'name' => $discount->name,
            'description' => $discount->description,
            'discount_percentage' => $this->toPercentageInput($discount->discount_percentage),
            'starts_at' => $this->toInputDatetime($discount->starts_at),
            'ends_at' => $this->toInputDatetime($discount->ends_at),
            'status' => $discount->status,
            'scopes' => $scopes,
        ]);
    }

    public function details($id)
    {
        $discount = DB::table('m_discounts')->where('id', (int) $id)->first();

        if (!$discount) {
            return redirect()
                ->route('admin_discounts')
                ->with('error', 'Data diskon tidak ditemukan.');
        }

        $availableKeys = $this->availableScopePairs()
            ->map(fn ($pair) => $pair->engine_id . ':' . $pair->category_id)
            ->flip();

        $engines = DB::table('m_discount_scopes')
            ->join('m_engines', 'm_engines.id', '=', 'm_discount_scopes.engine_id')
            ->join('m_categories', 'm_categories.id', '=', 'm_discount_scopes.category_id')
            ->where('m_discount_scopes.discount_id', $discount->id)
            ->orderBy('m_engines.name')
            ->orderBy('m_categories.name')
            ->get([
                'm_discount_scopes.engine_id',
                'm_discount_scopes.category_id',
                'm_engines.name as engine_name',
                'm_engines.status as engine_status',
                'm_categories.name as category_name',
            ])
            ->groupBy('engine_id')
            ->map(fn ($items) => (object) [
                'name' => $items->first()->engine_name,
                'is_active' => $items->first()->engine_status === 'Active',
                'categories' => $items->map(fn ($item) => (object) [
                    'name' => $item->category_name,
                    'available' => $availableKeys->has($item->engine_id . ':' . $item->category_id),
                ])->values(),
            ])
            ->values();

        $startsAt = Carbon::parse($discount->starts_at);
        $endsAt = Carbon::parse($discount->ends_at);
        $now = Carbon::now(self::TIMEZONE)->format('Y-m-d H:i:s');
        $periodState = $this->periodState($discount, $now);
        $nowWallClock = Carbon::parse($now);

        $summary = (object) [
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'duration_days' => (int) $startsAt->copy()->startOfDay()->diffInDays($endsAt->copy()->startOfDay()) + 1,
            'period_state' => $periodState,
            'days_to_start' => $periodState === 'upcoming'
                ? (int) ceil($nowWallClock->diffInMinutes($startsAt) / 1440)
                : null,
            'days_left' => $periodState === 'running'
                ? (int) ceil($nowWallClock->diffInMinutes($endsAt) / 1440)
                : null,
            'category_total' => $engines->sum(fn ($engine) => $engine->categories->count()),
            'created_at' => $this->toWibDatetime($discount->created_at),
            'updated_at' => $this->toWibDatetime($discount->updated_at),
        ];

        return view('admin.discount.details', compact('discount', 'engines', 'summary'));
    }

    public function update(Request $request, $id)
    {
        $discount = DB::table('m_discounts')->where('id', (int) $id)->first();

        if (!$discount) {
            return redirect()
                ->route('admin_discounts')
                ->with('error', 'Data diskon tidak ditemukan.');
        }

        [$validated, $pairs, $startsAt, $endsAt] = $this->validateDiscountInput($request, $discount);

        DB::transaction(function () use ($discount, $validated, $pairs, $startsAt, $endsAt) {
            DB::table('m_discounts')->where('id', $discount->id)->lockForUpdate()->first(['id']);
            $this->lockEngines($pairs);
            $this->ensureUniqueName($validated['name'], (int) $discount->id);
            $this->ensureUniqueCode($validated['code'], (int) $discount->id);

            if ($validated['status'] === 'Active') {
                $this->ensureNoActiveOverlap($pairs, $startsAt, $endsAt, (int) $discount->id);
            }

            try {
                DB::table('m_discounts')->where('id', $discount->id)->update([
                    'code' => $validated['code'],
                    'name' => $validated['name'],
                    'description' => $validated['description'] !== '' ? $validated['description'] : null,
                    'discount_percentage' => round((float) $validated['discount_percentage'], 2),
                    'starts_at' => $startsAt,
                    'ends_at' => $endsAt,
                    'status' => $validated['status'],
                    'updated_by' => Auth::user()->name,
                    'updated_at' => now(),
                ]);
            } catch (UniqueConstraintViolationException $exception) {
                $this->handleCodeConstraintViolation($exception);
            }

            DB::table('m_discount_scopes')->where('discount_id', $discount->id)->delete();
            $this->insertScopes((int) $discount->id, $pairs);
        });

        return redirect()
            ->route('admin_discounts')
            ->with('success', 'Diskon "' . $validated['name'] . '" berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $deleted = DB::transaction(function () use ($id) {
            $discount = DB::table('m_discounts')->where('id', (int) $id)->lockForUpdate()->first(['id', 'name']);

            if (!$discount) {
                return null;
            }

            DB::table('m_discount_scopes')->where('discount_id', $discount->id)->delete();
            DB::table('m_discounts')->where('id', $discount->id)->delete();

            return $discount;
        });

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Data diskon tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Diskon "' . $deleted->name . '" berhasil dihapus.',
        ]);
    }

    /**
     * @return array{0: array, 1: Collection, 2: string, 3: string}
     */
    private function validateDiscountInput(Request $request, ?object $existing = null): array
    {
        $input = [
            'code' => is_string($request->input('code')) ? strtoupper(trim($request->input('code'))) : $request->input('code'),
            'name' => trim((string) preg_replace('/\s+/u', ' ', (string) $request->input('name', ''))),
            'description' => trim((string) $request->input('description', '')),
            'discount_percentage' => str_replace(',', '.', trim((string) $request->input('discount_percentage', ''))),
            'starts_at' => $request->input('starts_at'),
            'ends_at' => $request->input('ends_at'),
            'status' => $request->input('status'),
            'scopes' => $request->input('scopes'),
        ];

        $validator = Validator::make($input, [
            'code' => ['bail', 'required', 'string', 'min:3', 'max:30', 'regex:/\A[A-Z0-9_-]+\z/'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'discount_percentage' => ['required', 'regex:/^\d{1,3}(\.\d{1,2})?$/', 'numeric', 'gt:0', 'lte:100'],
            'starts_at' => ['required', 'date_format:' . self::INPUT_DATETIME_FORMAT],
            'ends_at' => ['required', 'date_format:' . self::INPUT_DATETIME_FORMAT, 'after:starts_at'],
            'status' => ['required', 'in:Active,Inactive'],
            'scopes' => ['required', 'array', 'min:1', 'max:500'],
            'scopes.*' => ['required', 'string', 'distinct', 'regex:/^[1-9]\d*:[1-9]\d*$/'],
        ], [
            'code.required' => 'Kode diskon wajib diisi.',
            'code.string' => 'Kode diskon harus berupa teks.',
            'code.min' => 'Kode diskon minimal 3 karakter.',
            'code.max' => 'Kode diskon maksimal 30 karakter.',
            'code.regex' => 'Kode diskon hanya boleh berisi huruf, angka, tanda hubung (-), dan garis bawah (_), tanpa spasi.',
            'name.required' => 'Nama diskon wajib diisi.',
            'name.max' => 'Nama diskon maksimal 255 karakter.',
            'description.max' => 'Deskripsi maksimal 1.000 karakter.',
            'discount_percentage.required' => 'Persentase diskon wajib diisi.',
            'discount_percentage.regex' => 'Persentase diskon harus berupa angka dengan maksimal 2 angka desimal.',
            'discount_percentage.numeric' => 'Persentase diskon harus berupa angka.',
            'discount_percentage.gt' => 'Persentase diskon harus lebih dari 0%.',
            'discount_percentage.lte' => 'Persentase diskon maksimal 100%.',
            'starts_at.required' => 'Tanggal mulai wajib diisi.',
            'starts_at.date_format' => 'Format tanggal mulai tidak valid.',
            'ends_at.required' => 'Tanggal selesai wajib diisi.',
            'ends_at.date_format' => 'Format tanggal selesai tidak valid.',
            'ends_at.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'status.required' => 'Status diskon wajib dipilih.',
            'status.in' => 'Status diskon tidak valid.',
            'scopes.required' => 'Pilih minimal satu kategori pada cakupan diskon.',
            'scopes.array' => 'Cakupan diskon tidak valid.',
            'scopes.min' => 'Pilih minimal satu kategori pada cakupan diskon.',
            'scopes.max' => 'Cakupan diskon terlalu banyak.',
            'scopes.*.required' => 'Cakupan diskon tidak valid.',
            'scopes.*.string' => 'Cakupan diskon tidak valid.',
            'scopes.*.distinct' => 'Cakupan diskon tidak boleh duplikat.',
            'scopes.*.regex' => 'Cakupan diskon tidak valid.',
        ]);

        $validator->after(function ($validator) use ($input, $existing) {
            if ($validator->errors()->has('ends_at')) {
                return;
            }

            $endsAt = $this->toStoredTimestamp($input['ends_at'], true);

            // Saat edit, tanggal selesai yang tidak diubah tidak dicek lagi agar diskon yang sudah berakhir tetap bisa disunting.
            if ($existing && substr($endsAt, 0, 16) === substr((string) $existing->ends_at, 0, 16)) {
                return;
            }

            if ($endsAt <= Carbon::now(self::TIMEZONE)->format('Y-m-d H:i:s')) {
                $validator->errors()->add('ends_at', 'Tanggal selesai sudah lewat. Periode diskon harus masih berlaku.');
            }
        });

        $validated = $validator->validate();

        $pairs = collect($validated['scopes'])
            ->map(function (string $scope) {
                [$engineId, $categoryId] = array_map('intval', explode(':', $scope));

                return ['engine_id' => $engineId, 'category_id' => $categoryId];
            })
            ->values();

        $allowedKeys = $this->availableScopePairs()
            ->map(fn ($pair) => $pair->engine_id . ':' . $pair->category_id);

        // Cakupan lama tetap diizinkan walaupun mesinnya sudah tidak aktif, supaya tidak hilang saat diskon diedit.
        if ($existing) {
            $allowedKeys = $allowedKeys->merge(
                DB::table('m_discount_scopes')
                    ->where('discount_id', $existing->id)
                    ->get(['engine_id', 'category_id'])
                    ->map(fn ($scope) => $scope->engine_id . ':' . $scope->category_id)
            );
        }

        $allowedKeys = $allowedKeys->flip();

        if ($pairs->contains(fn ($pair) => !$allowedKeys->has($pair['engine_id'] . ':' . $pair['category_id']))) {
            throw ValidationException::withMessages([
                'scopes' => 'Cakupan diskon berisi mesin atau kategori yang tidak tersedia. Muat ulang halaman lalu pilih kembali.',
            ]);
        }

        return [
            $validated,
            $pairs,
            $this->toStoredTimestamp($validated['starts_at']),
            $this->toStoredTimestamp($validated['ends_at'], true),
        ];
    }

    private function lockEngines(Collection $pairs): void
    {
        // Mengunci mesin terkait agar penyimpanan diskon yang bersamaan tidak lolos cek tumpang tindih.
        DB::table('m_engines')
            ->whereIn('id', $pairs->pluck('engine_id')->unique()->values())
            ->orderBy('id')
            ->lockForUpdate()
            ->get(['id']);
    }

    private function ensureUniqueCode(string $code, ?int $ignoreId = null): void
    {
        $exists = DB::table('m_discounts')
            ->whereRaw('UPPER(code) = ?', [$code])
            ->when($ignoreId !== null, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages(['code' => 'Kode diskon sudah digunakan. Gunakan kode lain.']);
        }
    }

    private function handleCodeConstraintViolation(UniqueConstraintViolationException $exception): never
    {
        // Constraint database tetap melindungi kode jika dua permintaan menyimpan kode yang sama bersamaan.
        $message = $exception->errorInfo[2] ?? '';

        if (str_contains($message, 'discounts_code_unique') || str_contains($message, 'm_discounts.code')) {
            throw ValidationException::withMessages(['code' => 'Kode diskon sudah digunakan. Gunakan kode lain.']);
        }

        throw $exception;
    }

    private function ensureUniqueName(string $name, ?int $ignoreId = null): void
    {
        $nameExists = DB::table('m_discounts')
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists();

        if ($nameExists) {
            throw ValidationException::withMessages([
                'name' => 'Nama diskon "' . $name . '" sudah digunakan.',
            ]);
        }
    }

    private function insertScopes(int $discountId, Collection $pairs): void
    {
        DB::table('m_discount_scopes')->insert(
            $pairs->map(fn ($pair) => [
                'discount_id' => $discountId,
                'engine_id' => $pair['engine_id'],
                'category_id' => $pair['category_id'],
            ])->all()
        );
    }

    private function toWibDatetime($value): ?Carbon
    {
        // created_at/updated_at disimpan dengan zona waktu aplikasi, sedangkan tampilan memakai WIB.
        return $value ? Carbon::parse((string) $value, config('app.timezone'))->setTimezone(self::TIMEZONE) : null;
    }

    private function toInputDatetime($value): string
    {
        return Carbon::parse((string) $value)->format(self::INPUT_DATETIME_FORMAT);
    }

    private function toPercentageInput($value): string
    {
        $formatted = rtrim(rtrim(number_format((float) $value, 2, '.', ''), '0'), '.');

        return str_replace('.', ',', $formatted);
    }

    private function ensureNoActiveOverlap(Collection $pairs, string $startsAt, string $endsAt, ?int $ignoreDiscountId = null): void
    {
        $conflicts = DB::table('m_discount_scopes')
            ->join('m_discounts', 'm_discounts.id', '=', 'm_discount_scopes.discount_id')
            ->join('m_engines', 'm_engines.id', '=', 'm_discount_scopes.engine_id')
            ->join('m_categories', 'm_categories.id', '=', 'm_discount_scopes.category_id')
            ->where('m_discounts.status', 'Active')
            ->where('m_discounts.starts_at', '<=', $endsAt)
            ->where('m_discounts.ends_at', '>=', $startsAt)
            ->when($ignoreDiscountId, fn ($query) => $query->where('m_discounts.id', '!=', $ignoreDiscountId))
            ->where(function ($query) use ($pairs) {
                foreach ($pairs as $pair) {
                    $query->orWhere(function ($pairQuery) use ($pair) {
                        $pairQuery->where('m_discount_scopes.engine_id', $pair['engine_id'])
                            ->where('m_discount_scopes.category_id', $pair['category_id']);
                    });
                }
            })
            ->orderBy('m_discounts.name')
            ->orderBy('m_engines.name')
            ->orderBy('m_categories.name')
            ->get([
                'm_discounts.name as discount_name',
                'm_engines.name as engine_name',
                'm_categories.name as category_name',
            ]);

        if ($conflicts->isEmpty()) {
            return;
        }

        $first = $conflicts->first();
        $message = 'Diskon "' . $first->discount_name . '" sudah aktif untuk '
            . $first->engine_name . ' – ' . $first->category_name
            . ' pada periode yang beririsan';

        if ($conflicts->count() > 1) {
            $message .= ' (dan ' . ($conflicts->count() - 1) . ' cakupan lain yang bentrok)';
        }

        throw ValidationException::withMessages([
            'scopes' => $message . '. Ubah periode, cakupan, atau simpan sebagai Inactive.',
        ]);
    }

    private function toStoredTimestamp(string $value, bool $endOfMinute = false): string
    {
        return Carbon::createFromFormat(self::INPUT_DATETIME_FORMAT, $value, self::TIMEZONE)
            ->format('Y-m-d H:i:' . ($endOfMinute ? '59' : '00'));
    }

    private function availableScopePairs(): Collection
    {
        $pairQueries = collect(self::SCOPE_SOURCE_TABLES)
            ->map(fn (string $table) => DB::table($table)->select('engine_id', 'category_id'));

        $union = $pairQueries->shift();

        foreach ($pairQueries as $query) {
            $union->union($query);
        }

        return DB::query()
            ->fromSub($union, 'pairs')
            ->join('m_engines', 'm_engines.id', '=', 'pairs.engine_id')
            ->join('m_categories', 'm_categories.id', '=', 'pairs.category_id')
            ->where('m_engines.status', 'Active')
            ->orderBy('m_engines.name')
            ->orderBy('m_categories.name')
            ->get([
                'pairs.engine_id',
                'm_engines.name as engine_name',
                'pairs.category_id',
                'm_categories.name as category_name',
            ]);
    }

    public function data(Request $request)
    {
        $totalRecords = DB::table('m_discounts')->count();

        $query = DB::table('m_discounts');

        $search = trim((string) $request->input('search.value', ''));

        if ($search !== '') {
            $keyword = '%' . mb_strtolower($search) . '%';

            $query->where(function ($q) use ($keyword) {
                $q->whereRaw('LOWER(m_discounts.name) LIKE ?', [$keyword])
                    ->orWhereRaw('LOWER(m_discounts.description) LIKE ?', [$keyword])
                    ->orWhereRaw('LOWER(m_discounts.status) LIKE ?', [$keyword])
                    ->orWhereRaw('LOWER(m_discounts.created_by) LIKE ?', [$keyword])
                    ->orWhereExists(function ($scope) use ($keyword) {
                        $scope->select(DB::raw(1))
                            ->from('m_discount_scopes')
                            ->join('m_engines', 'm_engines.id', '=', 'm_discount_scopes.engine_id')
                            ->join('m_categories', 'm_categories.id', '=', 'm_discount_scopes.category_id')
                            ->whereColumn('m_discount_scopes.discount_id', 'm_discounts.id')
                            ->where(function ($names) use ($keyword) {
                                $names->whereRaw('LOWER(m_engines.name) LIKE ?', [$keyword])
                                    ->orWhereRaw('LOWER(m_categories.name) LIKE ?', [$keyword]);
                            });
                    });
            });
        }

        $totalFiltered = $query->count();

        $start = max(0, (int) $request->input('start', 0));
        $length = (int) $request->input('length', 10);
        $length = $length > 0 ? min($length, 100) : 10;

        $discounts = $query
            ->select(
                'm_discounts.id',
                'm_discounts.name',
                'm_discounts.description',
                'm_discounts.discount_percentage',
                'm_discounts.starts_at',
                'm_discounts.ends_at',
                'm_discounts.status',
                'm_discounts.created_by',
                'm_discounts.created_at'
            )
            ->orderByRaw("
                CASE
                    WHEN m_discounts.status = 'Active' THEN 1
                    WHEN m_discounts.status = 'Inactive' THEN 2
                    ELSE 3
                END
            ")
            ->orderBy('m_discounts.starts_at', 'desc')
            ->orderBy('m_discounts.id', 'desc')
            ->offset($start)
            ->limit($length)
            ->get();

        $scopesByDiscount = DB::table('m_discount_scopes')
            ->join('m_engines', 'm_engines.id', '=', 'm_discount_scopes.engine_id')
            ->join('m_categories', 'm_categories.id', '=', 'm_discount_scopes.category_id')
            ->whereIn('m_discount_scopes.discount_id', $discounts->pluck('id'))
            ->select(
                'm_discount_scopes.discount_id',
                'm_discount_scopes.engine_id',
                'm_engines.name as engine_name',
                'm_categories.name as category_name'
            )
            ->orderBy('m_engines.name')
            ->orderBy('m_categories.name')
            ->get()
            ->groupBy('discount_id');

        $now = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');

        $data = $discounts->map(function ($discount) use ($scopesByDiscount, $now) {
            $scopes = $scopesByDiscount->get($discount->id, collect());

            $engines = $scopes
                ->groupBy('engine_id')
                ->map(fn ($items) => [
                    'name' => $items->first()->engine_name,
                    'categories' => $items->pluck('category_name')->values(),
                ])
                ->values();

            $discount->engines = $engines;
            $discount->category_total = $scopes->count();
            $discount->period_state = $this->periodState($discount, $now);

            return $discount;
        });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFiltered,
            'data' => $data,
        ]);
    }

    /**
     * Periode diskon disimpan sebagai jam WIB sesuai input pengguna.
     */
    private function periodState(object $discount, string $now): string
    {
        if ($now < Carbon::parse($discount->starts_at)->format('Y-m-d H:i:s')) {
            return 'upcoming';
        }

        if ($now > Carbon::parse($discount->ends_at)->format('Y-m-d H:i:s')) {
            return 'expired';
        }

        return 'running';
    }
}

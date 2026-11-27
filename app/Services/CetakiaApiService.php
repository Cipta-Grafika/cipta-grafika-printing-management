<?php

namespace App\Services;

use App\Support\CetakiaCustomer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CetakiaApiService
{
    protected const QUOTATION_CACHE_VERSION_KEY = 'cetakia_quotations_cache_version';
    protected const QUOTATION_TOTAL_CACHE_KEY   = 'cetakia_quotations_overall_total';
    protected const QUOTATION_ALLOWED_STATUSES  = ['draft', 'sent', 'accepted', 'rejected', 'expired'];
    protected const QUOTATION_ALLOWED_SORT_FIELDS = [
        'quotation_code',
        'status',
        'quotation_date',
        'quotation_expired',
        'amount',
    ];

    protected string $baseUrl;
    protected string $apiKey;
    protected int $timeout;
    protected int $cacheTtl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.cetakia.base_url', 'https://dev.cetakia.com'), '/');
        $this->apiKey  = (string) config('services.cetakia.api_key', '');
        $this->timeout = 8; // detik
        $this->cacheTtl = 1800; // 30 menit
    }

    /**
     * Header standar autentikasi Cetakia API
     */
    protected function getHeaders(): array
    {
        return [
            'x-api-key'    => $this->apiKey,
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent'   => 'CiptaGrafikaEstimator/1.0 (DevMode)',
        ];
    }

    /**
     * Ambil daftar Customer dari Cetakia (dengan caching & fallback)
     *
     * @return array<int, array{id: string, name: string, npwp: ?string, bill_to: ?string, ship_to: ?string}>
     */
    public function getCustomers(bool $forceRefresh = false): array
    {
        $cacheKey = 'cetakia_customers_list';

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            try {
                if (empty($this->apiKey)) {
                    Log::warning('CetakiaApiService: API Key kosong, menggunakan fallback customer.');
                    return CetakiaCustomer::all();
                }

                $response = Http::withHeaders($this->getHeaders())
                    ->timeout($this->timeout)
                    ->get("{$this->baseUrl}/customers/list");

                if ($response->successful()) {
                    $json = $response->json();
                    if (!empty($json['data']) && is_array($json['data'])) {
                        return $json['data'];
                    }
                }

                Log::warning('CetakiaApiService: Gagal mengambil data customers, status: ' . $response->status());
            } catch (\Throwable $e) {
                Log::error('CetakiaApiService Error getCustomers: ' . $e->getMessage());
            }

            return CetakiaCustomer::all();
        });
    }

    /**
     * Ambil daftar Customer dari Cetakia dengan paginasi & keyword pencarian (Select2 Infinite Scroll)
     *
     * @param int $page
     * @param int $limit
     * @param string|null $keyword
     * @return array{results: array, pagination: array{more: bool, page: int, limit: int}}
     */
    public function getCustomersPaginated(int $page = 1, int $limit = 50, ?string $keyword = null): array
    {
        $limit = max(1, min(200, $limit));
        $page = max(1, $page);
        $keyword = trim((string) $keyword);

        $cacheKey = 'cetakia_cust_page_' . md5("p_{$page}_l_{$limit}_k_{$keyword}");

        return Cache::remember($cacheKey, 300, function () use ($page, $limit, $keyword) {
            try {
                if (empty($this->apiKey)) {
                    return $this->searchFallbackCustomers($page, $limit, $keyword);
                }

                $queryParams = [
                    'page'  => $page,
                    'limit' => $limit,
                ];
                if ($keyword !== '') {
                    $queryParams['keyword'] = $keyword;
                }

                $response = Http::withHeaders($this->getHeaders())
                    ->timeout($this->timeout)
                    ->get("{$this->baseUrl}/customers/list", $queryParams);

                if ($response->successful()) {
                    $json = $response->json();
                    $data = $json['data'] ?? [];
                    $pag  = $json['pagination'] ?? [];

                    $results = [];
                    foreach ($data as $c) {
                        $name = $c['name'] ?? '-';
                        $results[] = [
                            'id'            => $c['id'],
                            'text'          => $name,
                            'name'          => $name,
                            'customer_code' => $c['customer_code'] ?? '',
                            'bill_to'       => $c['bill_to'] ?? '',
                            'ship_to'       => $c['ship_to'] ?? '',
                        ];
                    }

                    return [
                        'results'    => $results,
                        'pagination' => [
                            'more'  => !empty($pag['hasNext']),
                            'page'  => $page,
                            'limit' => $limit,
                        ],
                    ];
                }

                Log::warning('CetakiaApiService: Gagal mengambil data customers paginated, status: ' . $response->status());
            } catch (\Throwable $e) {
                Log::error('CetakiaApiService Error getCustomersPaginated: ' . $e->getMessage());
            }

            return $this->searchFallbackCustomers($page, $limit, $keyword);
        });
    }

    /**
     * Fallback pencarian customer lokal jika offline
     */
    protected function searchFallbackCustomers(int $page = 1, int $limit = 50, string $keyword = ''): array
    {
        $all = CetakiaCustomer::all();
        if ($keyword !== '') {
            $kw = strtolower($keyword);
            $all = array_filter($all, function ($item) use ($kw) {
                $name = strtolower($item['name'] ?? '');
                $code = strtolower($item['customer_code'] ?? '');
                return str_contains($name, $kw) || str_contains($code, $kw);
            });
            $all = array_values($all);
        }

        $total = count($all);
        $offset = ($page - 1) * $limit;
        $slice = array_slice($all, $offset, $limit);
        $more = ($offset + $limit) < $total;

        $results = [];
        foreach ($slice as $c) {
            $name = $c['name'] ?? '-';
            $results[] = [
                'id'            => $c['id'],
                'text'          => $name,
                'name'          => $name,
                'customer_code' => $c['customer_code'] ?? '',
                'bill_to'       => $c['bill_to'] ?? '',
                'ship_to'       => $c['ship_to'] ?? '',
            ];
        }

        return [
            'results'    => $results,
            'pagination' => [
                'more'  => $more,
                'page'  => $page,
                'limit' => $limit,
            ],
        ];
    }

    /**
     * Ambil daftar Sales & Manajer dari Cetakia (dengan caching & fallback)
     *
     * @return array<int, array{id: string, name: string, username: string, phone: ?string, role: string, role_name: string}>
     */
    public function getSalesUsers(bool $forceRefresh = false): array
    {
        $cacheKey = 'cetakia_sales_list';

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            try {
                if (empty($this->apiKey)) {
                    return $this->fallbackSales();
                }

                $response = Http::withHeaders($this->getHeaders())
                    ->timeout($this->timeout)
                    ->get("{$this->baseUrl}/users/sales/list");

                if ($response->successful()) {
                    $json = $response->json();
                    if (!empty($json['data']) && is_array($json['data'])) {
                        return $json['data'];
                    }
                }

                Log::warning('CetakiaApiService: Gagal mengambil data sales, status: ' . $response->status());
            } catch (\Throwable $e) {
                Log::error('CetakiaApiService Error getSalesUsers: ' . $e->getMessage());
            }

            return $this->fallbackSales();
        });
    }

    /**
     * Ambil daftar Produk Master dari Cetakia
     */
    public function getProducts(bool $forceRefresh = false): array
    {
        $cacheKey = 'cetakia_products_list';

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            try {
                if (empty($this->apiKey)) {
                    return [];
                }

                $response = Http::withHeaders($this->getHeaders())
                    ->timeout($this->timeout)
                    ->get("{$this->baseUrl}/products/list");

                if ($response->successful()) {
                    $json = $response->json();
                    return $json['data'] ?? [];
                }
            } catch (\Throwable $e) {
                Log::error('CetakiaApiService Error getProducts: ' . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Ambil daftar Units dari Cetakia
     */
    public function getUnits(bool $forceRefresh = false): array
    {
        $cacheKey = 'cetakia_units_list';

        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            try {
                if (empty($this->apiKey)) {
                    return [];
                }

                $response = Http::withHeaders($this->getHeaders())
                    ->timeout($this->timeout)
                    ->get("{$this->baseUrl}/units/list");

                if ($response->successful()) {
                    $json = $response->json();
                    return $json['data'] ?? [];
                }
            } catch (\Throwable $e) {
                Log::error('CetakiaApiService Error getUnits: ' . $e->getMessage());
            }

            return [];
        });
    }

    /**
     * Kirim pembuatan Quotation baru ke Cetakia (POST /quotations)
     *
     * @param array $payload
     * @return array{success: bool, statusCode: int, message: string, data: ?array}
     */
    public function createQuotation(array $payload): array
    {
        try {
            if (empty($this->apiKey)) {
                return [
                    'success'    => false,
                    'statusCode' => 401,
                    'message'    => 'API Key Cetakia belum dikonfigurasi.',
                    'data'       => null,
                ];
            }

            Log::info('CetakiaApiService: Mengirim POST /quotations', ['customer_id' => $payload['customer_id'] ?? null]);

            $response = Http::withHeaders($this->getHeaders())
                ->timeout(15)
                ->post("{$this->baseUrl}/quotations", $payload);

            $json = $response->json() ?? [];
            $statusCode = $response->status();

            if ($response->successful()) {
                $this->invalidateQuotationsCache();

                Log::info('CetakiaApiService: Berhasil membuat quotation', [
                    'statusCode' => $statusCode,
                    'data'       => $json['data'] ?? null,
                ]);

                return [
                    'success'    => true,
                    'statusCode' => $statusCode,
                    'message'    => $json['message'] ?? 'Quotation created successfully',
                    'data'       => $json['data'] ?? null,
                ];
            }

            Log::warning('CetakiaApiService: Gagal membuat quotation', [
                'statusCode' => $statusCode,
                'response'   => $json,
            ]);

            return [
                'success'    => false,
                'statusCode' => $statusCode,
                'message'    => $json['message'] ?? 'Gagal membuat quotation di Cetakia.',
                'data'       => $json['data'] ?? null,
            ];
        } catch (\Throwable $e) {
            Log::error('CetakiaApiService Exception createQuotation: ' . $e->getMessage());

            return [
                'success'    => false,
                'statusCode' => 500,
                'message'    => 'Terjadi kesalahan jaringan atau server saat menghubungi Cetakia: ' . $e->getMessage(),
                'data'       => null,
            ];
        }
    }

    /**
     * Ambil versi cache quotation saat ini (untuk invalidasi O(1) lintas driver cache)
     */
    protected function getQuotationCacheVersion(): int
    {
        return (int) Cache::rememberForever(self::QUOTATION_CACHE_VERSION_KEY, fn () => 1);
    }

    /**
     * Invalidasi seluruh cache daftar quotation (dipanggil saat membuat quotation baru atau refresh manual)
     */
    public function invalidateQuotationsCache(): void
    {
        Cache::forever(self::QUOTATION_CACHE_VERSION_KEY, $this->getQuotationCacheVersion() + 1);
        Cache::forget(self::QUOTATION_TOTAL_CACHE_KEY);
    }

    /**
     * Ambil satu window data quotation dari cache atau Cetakia API.
     * Hanya respons sukses yang di-cache agar error sementara tidak tersimpan.
     *
     * @param array<string, mixed> $queryParams
     * @return array{success: bool, data: array, meta: array, message: string}
     */
    protected function fetchQuotationWindow(array $queryParams, bool $isUnfiltered, int $ttl): array
    {
        $normalizedForKey = $queryParams;
        if (isset($normalizedForKey['search'])) {
            $normalizedForKey['search'] = mb_strtolower((string) $normalizedForKey['search']);
        }
        $version = $this->getQuotationCacheVersion();
        $cacheKey = 'cetakia_quotations_v' . $version . '_' . md5(json_encode($normalizedForKey));

        $cached = Cache::get($cacheKey);
        if (is_array($cached) && !empty($cached['success'])) {
            return $cached;
        }

        $response = Http::withHeaders($this->getHeaders())
            ->connectTimeout(5)
            ->timeout(20)
            ->get("{$this->baseUrl}/quotations", $queryParams);

        if ($response->successful()) {
            $json = $response->json() ?? [];
            $meta = $json['meta'] ?? $json['pagination'] ?? [];
            $payload = [
                'success' => true,
                'data'    => $json['data'] ?? [],
                'meta'    => $meta,
                'message' => $json['message'] ?? 'Quotations retrieved successfully',
            ];

            Cache::put($cacheKey, $payload, $ttl);

            if ($isUnfiltered && isset($meta['total'])) {
                Cache::put(self::QUOTATION_TOTAL_CACHE_KEY, (int) $meta['total'], 300);
            }

            return $payload;
        }

        Log::warning('CetakiaApiService: Gagal mengambil quotations', [
            'status'   => $response->status(),
            'response' => $response->json(),
        ]);

        return [
            'success' => false,
            'data'    => [],
            'meta'    => [],
            'message' => 'Gagal mengambil data quotation dari Cetakia.',
        ];
    }

    /**
     * Ambil total quotation keseluruhan (tanpa filter) dari cache, atau hangatkan cache via halaman 1.
     */
    protected function resolveOverallQuotationTotal(): ?int
    {
        $cachedTotal = Cache::get(self::QUOTATION_TOTAL_CACHE_KEY);
        if ($cachedTotal !== null) {
            return (int) $cachedTotal;
        }

        try {
            $window = $this->fetchQuotationWindow(['page' => 1, 'limit' => 50], true, 120);
            if (!empty($window['success']) && isset($window['meta']['total'])) {
                return (int) $window['meta']['total'];
            }
        } catch (\Throwable $e) {
            Log::warning('CetakiaApiService resolveOverallQuotationTotal gagal: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Ambil daftar Quotations dari Cetakia (dengan window caching, pagination & filter)
     *
     * @param array{search?: string, status?: string, startDate?: string, endDate?: string, sortField?: string, sortDirection?: string, page?: int, limit?: int, force_refresh?: bool} $params
     * @return array{success: bool, data: array, meta: array, message: string}
     */
    public function getQuotations(array $params = []): array
    {
        try {
            if (empty($this->apiKey)) {
                return [
                    'success' => false,
                    'data'    => [],
                    'meta'    => [],
                    'message' => 'API Key Cetakia belum dikonfigurasi.',
                ];
            }

            if (!empty($params['force_refresh'])) {
                $this->invalidateQuotationsCache();
            }

            $page          = max(1, (int) ($params['page'] ?? 1));
            $limit         = max(1, min(100, (int) ($params['limit'] ?? 10)));
            $search        = trim((string) ($params['search'] ?? ''));
            $status        = strtolower(trim((string) ($params['status'] ?? '')));
            $startDate     = trim((string) ($params['startDate'] ?? ''));
            $endDate       = trim((string) ($params['endDate'] ?? ''));
            $sortField     = trim((string) ($params['sortField'] ?? ''));
            $sortDirection = strtoupper(trim((string) ($params['sortDirection'] ?? 'DESC')));

            $hasStatusFilter = ($status !== '' && $status !== 'all');
            if ($hasStatusFilter && !in_array($status, self::QUOTATION_ALLOWED_STATUSES, true)) {
                return [
                    'success' => false,
                    'data'    => [],
                    'meta'    => Cache::has(self::QUOTATION_TOTAL_CACHE_KEY)
                        ? ['overall_total' => (int) Cache::get(self::QUOTATION_TOTAL_CACHE_KEY)]
                        : [],
                    'message' => 'Status filter tidak valid. Pilih: draft, sent, accepted, rejected, atau expired.',
                ];
            }

            $isUnfiltered = ($search === '' && !$hasStatusFilter && $startDate === '' && $endDate === '');

            // Ambil 50 baris per window API saat limit membagi habis 50 (10/25/50) agar klik halaman berikutnya instan.
            $chunkSize   = (50 % $limit === 0) ? 50 : $limit;
            $offset      = ($page - 1) * $limit;
            $apiPage     = intdiv($offset, $chunkSize) + 1;
            $sliceOffset = $offset % $chunkSize;

            $queryParams = [
                'page'  => $apiPage,
                'limit' => $chunkSize,
            ];
            if ($search !== '') {
                $queryParams['search'] = $search;
            }
            if ($hasStatusFilter) {
                $queryParams['status'] = $status;
            }
            if ($startDate !== '') {
                $queryParams['start_date'] = $startDate;
            }
            if ($endDate !== '') {
                $queryParams['end_date'] = $endDate;
            }
            if ($sortField !== '' && in_array($sortField, self::QUOTATION_ALLOWED_SORT_FIELDS, true)) {
                $queryParams['sort_field'] = $sortField;
                $queryParams['sort_direction'] = $sortDirection === 'ASC' ? 'ASC' : 'DESC';
            }

            $ttl = $isUnfiltered ? 120 : 180;
            $window = $this->fetchQuotationWindow($queryParams, $isUnfiltered, $ttl);

            if (empty($window['success'])) {
                $cachedOverall = Cache::get(self::QUOTATION_TOTAL_CACHE_KEY);
                return [
                    'success' => false,
                    'data'    => [],
                    'meta'    => $cachedOverall !== null ? ['overall_total' => (int) $cachedOverall] : [],
                    'message' => $window['message'] ?? 'Gagal mengambil data quotation dari Cetakia.',
                ];
            }

            $rows = array_values(array_slice($window['data'] ?? [], $sliceOffset, $limit));
            $meta = $window['meta'] ?? [];
            $filteredTotal = isset($meta['total']) ? (int) $meta['total'] : count($rows);
            $overallTotal = $isUnfiltered
                ? $filteredTotal
                : ($this->resolveOverallQuotationTotal() ?? $filteredTotal);

            return [
                'success' => true,
                'data'    => $rows,
                'meta'    => array_merge($meta, [
                    'page'          => $page,
                    'limit'         => $limit,
                    'total'         => $filteredTotal,
                    'overall_total' => $overallTotal,
                ]),
                'message' => $window['message'] ?? 'Quotations retrieved successfully',
            ];
        } catch (\Throwable $e) {
            Log::error('CetakiaApiService Exception getQuotations: ' . $e->getMessage());
            $cachedOverall = Cache::get(self::QUOTATION_TOTAL_CACHE_KEY);
            return [
                'success' => false,
                'data'    => [],
                'meta'    => $cachedOverall !== null ? ['overall_total' => (int) $cachedOverall] : [],
                'message' => 'Terjadi kesalahan jaringan saat mengambil quotations: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Ambil detail lengkap satu Quotation beserta items dari Cetakia (GET /quotations/{id}?include_items=true)
     *
     * @param string $id
     * @return array{success: bool, statusCode: int, data: ?array, message: string}
     */
    public function getQuotationDetail(string $id): array
    {
        $quotationId = trim($id);
        if ($quotationId === '') {
            return [
                'success'    => false,
                'statusCode' => 422,
                'data'       => null,
                'message'    => 'ID quotation tidak valid.',
            ];
        }

        if (empty($this->apiKey)) {
            return [
                'success'    => false,
                'statusCode' => 401,
                'data'       => null,
                'message'    => 'API Key Cetakia belum dikonfigurasi.',
            ];
        }

        $version = $this->getQuotationCacheVersion();
        $cacheKey = 'cetakia_quotation_detail_v' . $version . '_' . md5($quotationId);
        $cached = Cache::get($cacheKey);
        if (is_array($cached) && !empty($cached['success']) && !empty($cached['data'])) {
            return $cached;
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->connectTimeout(5)
                ->timeout(15)
                ->get("{$this->baseUrl}/quotations/{$quotationId}", [
                    'include_items' => 'true',
                ]);

            $statusCode = $response->status();
            $json = $response->json() ?? [];

            if ($response->successful() && !empty($json['data']) && is_array($json['data'])) {
                $payload = [
                    'success'    => true,
                    'statusCode' => $statusCode,
                    'data'       => $json['data'],
                    'message'    => $json['message'] ?? 'Quotation retrieved successfully',
                ];

                Cache::put($cacheKey, $payload, 120);

                return $payload;
            }

            if ($statusCode === 404) {
                return [
                    'success'    => false,
                    'statusCode' => 404,
                    'data'       => null,
                    'message'    => 'Detail item tidak tersedia karena quotation berasal dari divisi lain atau tidak ditemukan di divisi aktif.',
                ];
            }

            Log::warning('CetakiaApiService: Gagal mengambil detail quotation', [
                'id'       => $quotationId,
                'status'   => $statusCode,
                'response' => $json,
            ]);

            return [
                'success'    => false,
                'statusCode' => $statusCode ?: 502,
                'data'       => null,
                'message'    => $json['message'] ?? 'Gagal mengambil detail quotation dari Cetakia.',
            ];
        } catch (\Throwable $e) {
            Log::error('CetakiaApiService Exception getQuotationDetail: ' . $e->getMessage(), [
                'id' => $quotationId,
            ]);

            return [
                'success'    => false,
                'statusCode' => 500,
                'data'       => null,
                'message'    => 'Terjadi kesalahan jaringan saat mengambil detail quotation.',
            ];
        }
    }

    /**
     * Fallback data sales jika Cetakia API offline
     */
    protected function fallbackSales(): array
    {
        return [
            [
                'id'        => '019e8773-181b-73f9-a2d5-ff5ecf4089f7',
                'name'      => 'Akun Sendiri',
                'username'  => 'akunsendiri',
                'phone'     => null,
                'role'      => 'SALES',
                'role_name' => 'Sales/CS',
            ],
            [
                'id'        => '019ffa1e-6ef0-75f2-9e2f-94452801ed48',
                'name'      => 'Choi Seungcheol',
                'username'  => 'choi_seungcheol',
                'phone'     => '2585124574164',
                'role'      => 'SALES',
                'role_name' => 'Sales/CS',
            ],
        ];
    }
}

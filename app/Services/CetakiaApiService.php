<?php

namespace App\Services;

use App\Support\CetakiaCustomer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CetakiaApiService
{
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

<?php

namespace Tests\Feature;

use App\Http\Controllers\admin\EstimationCT;
use App\Services\CetakiaApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class EstimationQuotationPaginationTest extends TestCase
{
    public function test_it_requests_only_the_requested_page_and_keeps_the_api_total(): void
    {
        $service = Mockery::mock(CetakiaApiService::class);
        $service->shouldReceive('getQuotations')
            ->once()
            ->with([
                'page' => 2,
                'limit' => 10,
                'search' => 'GRAS2611260001',
                'status' => 'all',
                'startDate' => '',
                'endDate' => '',
            ])
            ->andReturn([
                'success' => true,
                'data' => [['quotation_code' => 'GRAS2611260001']],
                'meta' => [
                    'total' => 1,
                    'overall_total' => 288285,
                ],
            ]);

        $request = Request::create('/admin/estimations/data', 'GET', [
            'draw' => 3,
            'start' => 10,
            'length' => 10,
            'search' => ['value' => 'GRAS2611260001'],
            'status' => 'all',
            'start_date' => '',
            'end_date' => '',
        ]);

        $response = (new EstimationCT($service))->data($request);
        $payload = $response->getData(true);

        $this->assertSame(3, $payload['draw']);
        $this->assertSame(288285, $payload['recordsTotal']);
        $this->assertSame(1, $payload['recordsFiltered']);
        $this->assertSame([['quotation_code' => 'GRAS2611260001']], $payload['data']);
    }

    public function test_it_caps_page_size_to_one_hundred_rows(): void
    {
        $service = Mockery::mock(CetakiaApiService::class);
        $service->shouldReceive('getQuotations')
            ->once()
            ->with(Mockery::on(fn (array $params) => $params['page'] === 11 && $params['limit'] === 100))
            ->andReturn([
                'success' => true,
                'data' => [],
                'meta' => ['total' => 0],
            ]);

        $request = Request::create('/admin/estimations/data', 'GET', [
            'start' => 1000,
            'length' => 5000,
        ]);

        (new EstimationCT($service))->data($request);
    }

    public function test_it_invalidates_cache_when_refresh_flag_is_set(): void
    {
        $service = Mockery::mock(CetakiaApiService::class);
        $service->shouldReceive('invalidateQuotationsCache')->once();
        $service->shouldReceive('getQuotations')
            ->once()
            ->andReturn([
                'success' => true,
                'data' => [],
                'meta' => ['total' => 288285, 'overall_total' => 288285],
            ]);

        $request = Request::create('/admin/estimations/data', 'GET', [
            'start' => 0,
            'length' => 10,
            'refresh' => 1,
        ]);

        $response = (new EstimationCT($service))->data($request);
        $payload = $response->getData(true);

        $this->assertSame(288285, $payload['recordsTotal']);
    }

    public function test_cetakia_service_caches_fifty_row_window_and_preserves_overall_total_during_search(): void
    {
        Cache::flush();
        config()->set('services.cetakia.base_url', 'https://cetakia.test');
        config()->set('services.cetakia.api_key', 'test-key');

        $windowRows = [];
        for ($i = 1; $i <= 50; $i++) {
            $windowRows[] = ['quotation_code' => sprintf('GRSA261126%04d', $i)];
        }

        Http::fake([
            'https://cetakia.test/quotations*' => function ($request) use ($windowRows) {
                $query = [];
                parse_str(parse_url($request->url(), PHP_URL_QUERY) ?? '', $query);

                if (($query['search'] ?? '') === 'GRSA2611260001') {
                    return Http::response([
                        'data' => [$windowRows[0]],
                        'pagination' => ['total' => 1, 'page' => 1, 'limit' => 50],
                    ], 200);
                }

                return Http::response([
                    'data' => $windowRows,
                    'pagination' => ['total' => 288285, 'page' => (int) ($query['page'] ?? 1), 'limit' => 50],
                ], 200);
            },
        ]);

        $service = new CetakiaApiService();

        // Page 1 & Page 2 share a single 50-row API call
        $p1 = $service->getQuotations(['page' => 1, 'limit' => 10]);
        $p2 = $service->getQuotations(['page' => 2, 'limit' => 10]);

        $this->assertTrue($p1['success']);
        $this->assertCount(10, $p1['data']);
        $this->assertSame('GRSA2611260001', $p1['data'][0]['quotation_code']);
        $this->assertCount(10, $p2['data']);
        $this->assertSame('GRSA2611260011', $p2['data'][0]['quotation_code']);
        $this->assertSame(288285, $p1['meta']['overall_total']);

        // Searching hits API once, caches result, and preserves overall_total = 288285
        $s1 = $service->getQuotations(['page' => 1, 'limit' => 10, 'search' => 'GRSA2611260001']);
        $s2 = $service->getQuotations(['page' => 1, 'limit' => 10, 'search' => 'grsa2611260001']);

        $this->assertSame(1, $s1['meta']['total']);
        $this->assertSame(288285, $s1['meta']['overall_total']);
        $this->assertSame($s1['data'], $s2['data']);

        // Total HTTP requests: 1 for page 1..5 window + 1 for search (case-insensitive cache hit on s2) = 2
        Http::assertSentCount(2);
    }

    public function test_it_forwards_supported_sort_and_snake_case_date_filters_and_rejects_invalid_status(): void
    {
        Cache::flush();
        config()->set('services.cetakia.base_url', 'https://cetakia.test');
        config()->set('services.cetakia.api_key', 'test-key');

        Http::fake([
            'https://cetakia.test/quotations*' => Http::response([
                'data' => [['quotation_code' => 'GRSA2611260001']],
                'pagination' => ['total' => 14, 'page' => 1, 'limit' => 50],
            ], 200),
        ]);

        $ct = new EstimationCT(new CetakiaApiService());

        $request = Request::create('/admin/estimations/data', 'GET', [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'status' => 'accepted',
            'start_date' => '2026-11-26',
            'end_date' => '2026-11-26',
            'order' => [['column' => 6, 'dir' => 'asc']],
        ]);

        $response = $ct->data($request);
        $payload = $response->getData(true);
        $this->assertSame(14, $payload['recordsFiltered']);

        Http::assertSent(function ($req) {
            $query = [];
            parse_str(parse_url($req->url(), PHP_URL_QUERY) ?? '', $query);
            return ($query['status'] ?? null) === 'accepted'
                && ($query['start_date'] ?? null) === '2026-11-26'
                && ($query['end_date'] ?? null) === '2026-11-26'
                && ($query['sort_field'] ?? null) === 'amount'
                && ($query['sort_direction'] ?? null) === 'ASC';
        });

        // Invalid status should fail cleanly without hitting Cetakia API
        $invalid = (new CetakiaApiService())->getQuotations(['status' => 'so']);
        $this->assertFalse($invalid['success']);
    }

    public function test_cetakia_service_fetches_and_caches_quotation_detail_with_items(): void
    {
        Cache::flush();
        config()->set('services.cetakia.base_url', 'https://cetakia.test');
        config()->set('services.cetakia.api_key', 'test-key');

        $detailId = '01a216f6-d1b1-7315-8546-ea34465189f0';
        $notFoundId = '01a1e7a2-a16a-7656-b162-eefc22648397';

        Http::fake([
            "https://cetakia.test/quotations/{$detailId}*" => Http::response([
                'statusCode' => 200,
                'data' => [
                    'id' => $detailId,
                    'quotation_code' => 'GRAS2611260013',
                    'grand_total' => 18000,
                    'items' => [
                        ['item' => 'Book Paper 70 gsm', 'qty' => 3, 'price' => 6000, 'amount' => 18000],
                    ],
                ],
            ], 200),
            "https://cetakia.test/quotations/{$notFoundId}*" => Http::response([
                'statusCode' => 404,
                'message' => 'Quotation not found',
                'data' => null,
            ], 404),
        ]);

        $ct = new EstimationCT(new CetakiaApiService());

        $res1 = $ct->show($detailId);
        $res2 = $ct->show($detailId);
        $payload1 = $res1->getData(true);

        $this->assertSame(200, $res1->status());
        $this->assertTrue($payload1['success']);
        $this->assertSame('GRAS2611260013', $payload1['data']['quotation_code']);
        $this->assertCount(1, $payload1['data']['items']);
        $this->assertSame($payload1['data'], $res2->getData(true)['data']);

        $res404 = $ct->show($notFoundId);
        $this->assertSame(404, $res404->status());
        $this->assertFalse($res404->getData(true)['success']);

        // 1 cached call for $detailId + 1 uncached 404 call for $notFoundId = 2 HTTP calls
        Http::assertSentCount(2);
    }

    public function test_index_defaults_to_last_seven_days_date_range(): void
    {
        $expectedEnd = now('Asia/Jakarta')->toDateString();
        $expectedStart = now('Asia/Jakarta')->subDays(6)->toDateString();

        $service = Mockery::mock(CetakiaApiService::class);
        $service->shouldReceive('getQuotations')
            ->once()
            ->with(Mockery::on(fn (array $params) =>
                $params['page'] === 1
                && $params['limit'] === 10
                && $params['startDate'] === $expectedStart
                && $params['endDate'] === $expectedEnd
            ))
            ->andReturn([
                'success' => true,
                'data' => [],
                'meta' => ['total' => 34, 'overall_total' => 288279],
            ]);

        $view = (new EstimationCT($service))->index();
        $viewData = $view->getData();

        $this->assertSame($expectedStart, $viewData['defaultStartDate']);
        $this->assertSame($expectedEnd, $viewData['defaultEndDate']);
        $this->assertSame(34, $viewData['initialQuotationsPayload']['recordsFiltered']);
    }
}

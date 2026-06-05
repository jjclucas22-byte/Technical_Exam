<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Services\CustomerSearchService;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CustomerSearchServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.elasticsearch.url', 'http://searcher:9200');
        config()->set('services.elasticsearch.index', 'customers');

        Http::preventStrayRequests();
    }

    public function test_it_indexes_a_customer_document(): void
    {
        Http::fake(function (HttpRequest $request) {
            if ($request->method() === 'HEAD') {
                return Http::response([], 200);
            }

            return Http::response([
                'result' => 'updated',
            ]);
        });

        $customer = new Customer([
            'first_name' => 'Maria',
            'last_name' => 'Santos',
            'email' => 'maria.santos@example.com',
            'contact_number' => '09171234567',
        ]);

        $customer->id = 15;

        app(CustomerSearchService::class)->index($customer);

        Http::assertSent(
            fn (HttpRequest $request): bool => $request->method() === 'PUT'
                && $request->url() === 'http://searcher:9200/customers/_doc/15?refresh=wait_for'
                && $request['id'] === 15
                && $request['first_name'] === 'Maria'
                && $request['last_name'] === 'Santos'
                && $request['email'] === 'maria.santos@example.com'
                && $request['contact_number'] === '09171234567'
        );
    }

    public function test_it_creates_the_index_when_the_index_does_not_exist(): void
    {
        Http::fake(function (HttpRequest $request) {
            if ($request->method() === 'HEAD') {
                return Http::response([], 404);
            }

            return Http::response([
                'acknowledged' => true,
                'result' => 'updated',
            ]);
        });

        $customer = new Customer([
            'first_name' => 'Juan',
            'last_name' => 'Dela Cruz',
            'email' => 'juan.delacruz@example.com',
            'contact_number' => '09181234567',
        ]);

        $customer->id = 22;

        app(CustomerSearchService::class)->index($customer);

        Http::assertSent(
            fn (HttpRequest $request): bool => $request->method() === 'PUT'
                && $request->url() === 'http://searcher:9200/customers'
                && isset($request['mappings']['properties']['first_name'])
                && isset($request['mappings']['properties']['last_name'])
                && isset($request['mappings']['properties']['email'])
                && isset($request['mappings']['properties']['contact_number'])
        );

        Http::assertSent(
            fn (HttpRequest $request): bool => $request->method() === 'PUT'
                && $request->url() === 'http://searcher:9200/customers/_doc/22?refresh=wait_for'
        );
    }

    public function test_it_searches_customer_ids_by_name_or_email(): void
    {
        Http::fake(function (HttpRequest $request) {
            if ($request->method() === 'HEAD') {
                return Http::response([], 200);
            }

            return Http::response([
                'hits' => [
                    'hits' => [
                        ['_id' => '9'],
                        ['_id' => '12'],
                    ],
                ],
            ]);
        });

        $ids = app(CustomerSearchService::class)->searchIds('Maria Santos');

        $this->assertSame([9, 12], $ids);

        Http::assertSent(
            fn (HttpRequest $request): bool => $request->method() === 'POST'
                && $request->url() === 'http://searcher:9200/customers/_search'
                && $request['query']['multi_match']['query'] === 'Maria Santos'
                && in_array('first_name^2', $request['query']['multi_match']['fields'], true)
                && in_array('last_name^2', $request['query']['multi_match']['fields'], true)
                && in_array('email^3', $request['query']['multi_match']['fields'], true)
        );
    }

    public function test_it_returns_an_empty_array_when_search_has_no_hits(): void
    {
        Http::fake(function (HttpRequest $request) {
            if ($request->method() === 'HEAD') {
                return Http::response([], 200);
            }

            return Http::response([
                'hits' => [
                    'hits' => [],
                ],
            ]);
        });

        $ids = app(CustomerSearchService::class)->searchIds('No Match');

        $this->assertSame([], $ids);
    }

    public function test_it_deletes_a_customer_document(): void
    {
        Http::fake([
            'http://searcher:9200/customers/_doc/7?refresh=wait_for' => Http::response([
                'result' => 'deleted',
            ]),
        ]);

        $customer = new Customer();
        $customer->id = 7;

        app(CustomerSearchService::class)->delete($customer);

        Http::assertSent(
            fn (HttpRequest $request): bool => $request->method() === 'DELETE'
                && $request->url() === 'http://searcher:9200/customers/_doc/7?refresh=wait_for'
        );
    }

    public function test_delete_does_not_fail_when_the_document_is_missing(): void
    {
        Http::fake([
            'http://searcher:9200/customers/_doc/99?refresh=wait_for' => Http::response([
                'found' => false,
            ], 404),
        ]);

        $customer = new Customer();
        $customer->id = 99;

        app(CustomerSearchService::class)->delete($customer);

        Http::assertSent(
            fn (HttpRequest $request): bool => $request->method() === 'DELETE'
                && $request->url() === 'http://searcher:9200/customers/_doc/99?refresh=wait_for'
        );
    }
}

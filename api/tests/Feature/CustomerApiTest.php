<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CustomerApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.elasticsearch.url', 'http://searcher:9200');
        config()->set('services.elasticsearch.index', 'customers');

        Http::preventStrayRequests();
        $this->fakeElasticsearch();
    }

   
    public function test_it_requires_customer_fields(): void
    {
        $response = $this->postJson('/api/customers', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'first_name',
                'last_name',
                'email',
                'contact_number',
            ]);
    }

    public function test_it_rejects_duplicate_email_addresses(): void
    {
        Customer::query()->create([
            'first_name' => 'Miguel',
            'last_name' => 'Garcia',
            'email' => 'miguel.garcia@example.com',
            'contact_number' => '09201234567',
        ]);

        $response = $this->postJson('/api/customers', [
            'first_name' => 'Another',
            'last_name' => 'Customer',
            'email' => 'miguel.garcia@example.com',
            'contact_number' => '09209999999',
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    
    public function test_update_allows_the_customer_to_keep_the_same_email(): void
    {
        $customer = Customer::query()->create([
            'first_name' => 'Sofia',
            'last_name' => 'Villanueva',
            'email' => 'sofia.villanueva@example.com',
            'contact_number' => '09231234567',
        ]);

        $response = $this->putJson("/api/customers/{$customer->id}", [
            'first_name' => 'Sofia',
            'last_name' => 'Villanueva-Ramos',
            'email' => 'sofia.villanueva@example.com',
            'contact_number' => '09238888888',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.last_name', 'Villanueva-Ramos');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'email' => 'sofia.villanueva@example.com',
            'last_name' => 'Villanueva-Ramos',
        ]);
    }

    
    /**
     * @param array<int, array<string, mixed>> $searchHits
     */
    private function fakeElasticsearch(array $searchHits = []): void
    {
        Http::fake(function (HttpRequest $request) use ($searchHits) {
            if ($request->method() === 'HEAD') {
                return Http::response([], 200);
            }

            if (
                $request->method() === 'POST'
                && str_ends_with($request->url(), '/customers/_search')
            ) {
                return Http::response([
                    'hits' => [
                        'hits' => $searchHits,
                    ],
                ]);
            }

            if ($request->method() === 'PUT') {
                return Http::response([
                    'acknowledged' => true,
                    'result' => 'updated',
                ]);
            }

            if ($request->method() === 'DELETE') {
                return Http::response([
                    'result' => 'deleted',
                ]);
            }

            return Http::response([
                'acknowledged' => true,
            ]);
        });
    }
}

<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class CustomerSearchService
{
    public function index(Customer $customer): void
    {
        $this->ensureIndexExists();

        $this->client()
            ->put(
                "/{$this->indexName()}/_doc/{$customer->getKey()}?refresh=wait_for",
                [
                    'id' => $customer->getKey(),
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'email' => $customer->email,
                    'contact_number' => $customer->contact_number,
                ]
            )
            ->throw();
    }

    public function delete(Customer $customer): void
    {
        $response = $this->client()
            ->delete(
                "/{$this->indexName()}/_doc/{$customer->getKey()}?refresh=wait_for"
            );

        if (! $response->notFound()) {
            $response->throw();
        }
    }

    /**
     * @return array<int>
     */
    public function searchIds(string $search): array
    {
        $this->ensureIndexExists();

        $response = $this->client()
            ->post("/{$this->indexName()}/_search", [
                '_source' => false,
                'size' => 100,
                'query' => [
                    'multi_match' => [
                        'query' => $search,
                        'fields' => [
                            'first_name^2',
                            'last_name^2',
                            'email^3',
                        ],
                        'type' => 'cross_fields',
                        'operator' => 'and',
                    ],
                ],
            ])
            ->throw();

        return collect($response->json('hits.hits', []))
            ->pluck('_id')
            ->map(static fn (string $id): int => (int) $id)
            ->all();
    }

    private function ensureIndexExists(): void
    {
        $indexPath = "/{$this->indexName()}";
        $response = $this->client()->head($indexPath);

        if ($response->successful()) {
            return;
        }

        if (! $response->notFound()) {
            $response->throw();
        }

        $createResponse = $this->client()->put($indexPath, [
            'mappings' => [
                'properties' => [
                    'id' => ['type' => 'long'],
                    'first_name' => ['type' => 'text'],
                    'last_name' => ['type' => 'text'],
                    'email' => [
                        'type' => 'text',
                        'fields' => [
                            'keyword' => ['type' => 'keyword'],
                        ],
                    ],
                    'contact_number' => ['type' => 'keyword'],
                ],
            ],
        ]);

        if (
            $createResponse->failed()
            && $createResponse->json('error.type') !== 'resource_already_exists_exception'
        ) {
            $createResponse->throw();
        }
    }

    private function client(): PendingRequest
    {
        return Http::baseUrl(
            rtrim((string) config('services.elasticsearch.url'), '/')
        )
            ->acceptJson()
            ->asJson()
            ->connectTimeout(2)
            ->timeout(5);
    }

    private function indexName(): string
    {
        return (string) config('services.elasticsearch.index');
    }
}

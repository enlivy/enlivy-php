<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\EnlivyClient;
use Enlivy\Organization\Invoice;
use Enlivy\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

final class CollectionAutoPagingTest extends TestCase
{
    private MockHttpClient $httpClient;

    private EnlivyClient $client;

    protected function setUp(): void
    {
        $this->httpClient = new MockHttpClient();
        $this->client = new EnlivyClient([
            'api_key' => '1|test_token',
            'organization_id' => 'org_test',
            'http_client' => $this->httpClient,
        ]);
    }

    private function pageResponse(int $page, int $totalPages, array $ids): array
    {
        return [
            'data' => array_map(
                static fn(string $id): array => ['id' => $id, 'object' => 'invoice'],
                $ids,
            ),
            'meta' => [
                'pagination' => [
                    'total' => 3,
                    'count' => count($ids),
                    'per_page' => 2,
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                ],
            ],
        ];
    }

    public function testAutoPagingIteratorWalksAllPages(): void
    {
        $this->httpClient
            ->addResponse(200, $this->pageResponse(1, 2, ['org_inv_1', 'org_inv_2']))
            ->addResponse(200, $this->pageResponse(2, 2, ['org_inv_3']));

        $collection = $this->client->invoices->list(['per_page' => 2]);
        $items = iterator_to_array($collection->autoPagingIterator(), false);

        $this->assertCount(3, $items);
        $this->assertContainsOnlyInstancesOf(Invoice::class, $items);
        $this->assertSame(['org_inv_1', 'org_inv_2', 'org_inv_3'], array_map(
            static fn(Invoice $i): string => $i->id,
            $items,
        ));

        $this->assertSame(2, $this->httpClient->getRequestCount());
        $secondRequest = $this->httpClient->getRequests()[1];
        $this->assertSame(2, $secondRequest['params']['page'] ?? null);
    }

    public function testAutoPagingIteratorStopsOnSinglePage(): void
    {
        $this->httpClient->addResponse(200, $this->pageResponse(1, 1, ['org_inv_1']));

        $collection = $this->client->invoices->list();
        $items = iterator_to_array($collection->autoPagingIterator(), false);

        $this->assertCount(1, $items);
        $this->assertSame(1, $this->httpClient->getRequestCount());
    }

    public function testPlainCollectionWithoutContextYieldsOwnData(): void
    {
        $collection = new \Enlivy\Collection();
        $collection->refreshFromWithClass(['data' => [['id' => 'x', 'object' => 'invoice']]], Invoice::class);

        $items = iterator_to_array($collection->autoPagingIterator(), false);

        $this->assertCount(1, $items);
    }
}

<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\EnlivyClient;
use Enlivy\Exception\NotFoundException;
use Enlivy\Exception\ServerException;
use Enlivy\Tests\Mock\MockHttpClient;
use Enlivy\Util\RequestOptions;
use PHPUnit\Framework\TestCase;

final class RetryTest extends TestCase
{
    private MockHttpClient $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = new MockHttpClient();
    }

    private function makeClient(int $maxRetries = 1): EnlivyClient
    {
        return new EnlivyClient([
            'api_key' => '1|test_token',
            'organization_id' => 'org_test',
            'http_client' => $this->httpClient,
            'max_retries' => $maxRetries,
        ]);
    }

    public function testGetIsRetriedOnServerErrorThenSucceeds(): void
    {
        $this->httpClient
            ->addResponse(503, ['message' => 'Service unavailable'])
            ->addResponse(200, ['data' => ['id' => 'org_inv_1', 'object' => 'invoice']]);

        $invoice = $this->makeClient()->invoices->retrieve('org_inv_1');

        $this->assertSame('org_inv_1', $invoice->id);
        $this->assertSame(2, $this->httpClient->getRequestCount());
    }

    public function testGetIsNotRetriedOnClientError(): void
    {
        $this->httpClient->addResponse(404, ['message' => 'Not found']);

        try {
            $this->makeClient()->invoices->retrieve('org_inv_missing');
            $this->fail('Expected NotFoundException');
        } catch (NotFoundException) {
        }

        $this->assertSame(1, $this->httpClient->getRequestCount());
    }

    public function testPostWithoutIdempotencyKeyIsNotRetried(): void
    {
        $this->httpClient->addResponse(503, ['message' => 'Service unavailable']);

        try {
            $this->makeClient()->invoices->create(['currency' => 'EUR']);
            $this->fail('Expected ServerException');
        } catch (ServerException) {
        }

        $this->assertSame(1, $this->httpClient->getRequestCount());
    }

    public function testPostWithIdempotencyKeyIsRetried(): void
    {
        $this->httpClient
            ->addResponse(503, ['message' => 'Service unavailable'])
            ->addResponse(201, ['data' => ['id' => 'org_inv_2', 'object' => 'invoice']]);

        $invoice = $this->makeClient()->invoices->create(
            ['currency' => 'EUR'],
            new RequestOptions(idempotencyKey: 'idem_123'),
        );

        $this->assertSame('org_inv_2', $invoice->id);
        $this->assertSame(2, $this->httpClient->getRequestCount());

        foreach ($this->httpClient->getRequests() as $request) {
            $this->assertSame('idem_123', $request['headers']['Idempotency-Key'] ?? null);
        }
    }

    public function testRetriesStopAfterMaxRetries(): void
    {
        $this->httpClient
            ->addResponse(503, ['message' => 'down'])
            ->addResponse(503, ['message' => 'down'])
            ->addResponse(503, ['message' => 'down']);

        try {
            $this->makeClient(maxRetries: 2)->invoices->retrieve('org_inv_1');
            $this->fail('Expected ServerException');
        } catch (ServerException) {
        }

        $this->assertSame(3, $this->httpClient->getRequestCount());
    }

    public function testRequestRawThrowsTypedExceptionOnHttpError(): void
    {
        $this->httpClient->addResponse(404, ['message' => 'Not found']);

        $this->expectException(NotFoundException::class);

        $this->makeClient()->invoices->download('org_inv_missing');
    }
}

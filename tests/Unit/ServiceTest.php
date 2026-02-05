<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\Collection;
use Enlivy\EnlivyClient;
use Enlivy\EnlivyObject;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Prospect;
use Enlivy\Tests\Mock\MockHttpClient;
use Enlivy\Util\RequestOptions;
use PHPUnit\Framework\TestCase;

final class ServiceTest extends TestCase
{
    private MockHttpClient $httpClient;
    private EnlivyClient $client;

    protected function setUp(): void
    {
        $this->httpClient = new MockHttpClient();
        $this->client = new EnlivyClient([
            'api_key' => '1|test_token',
            'organization_id' => 'org_default',
            'http_client' => $this->httpClient,
        ]);
    }

    public function testListReturnsCollection(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [
                ['id' => 'org_pros_1', 'object' => 'prospect', 'title' => 'Prospect 1'],
                ['id' => 'org_pros_2', 'object' => 'prospect', 'title' => 'Prospect 2'],
            ],
            'meta' => [
                'pagination' => [
                    'total' => 2,
                    'current_page' => 1,
                    'total_pages' => 1,
                ],
            ],
        ]);

        $result = $this->client->prospects->list();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(2, $result);

        // Verify items are typed Prospect objects
        $first = $result->first();
        $this->assertInstanceOf(Prospect::class, $first);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('GET', $request['method']);
        $this->assertStringContainsString('/organizations/org_default/prospects', $request['url']);
    }

    public function testRetrieveReturnsTypedObject(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [
                'id' => 'org_pros_xxx',
                'object' => 'prospect',
                'title' => 'Test Prospect',
                'email' => 'test@example.com',
            ],
        ]);

        $result = $this->client->prospects->retrieve('org_pros_xxx');

        $this->assertInstanceOf(Prospect::class, $result);
        $this->assertSame('org_pros_xxx', $result->id);
        $this->assertSame('Test Prospect', $result->title);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('GET', $request['method']);
        $this->assertStringContainsString('/prospects/org_pros_xxx', $request['url']);
    }

    public function testCreateSendsPostRequest(): void
    {
        $this->httpClient->addResponse(201, [
            'data' => [
                'id' => 'org_pros_new',
                'object' => 'prospect',
                'title' => 'New Prospect',
            ],
        ]);

        $result = $this->client->prospects->create([
            'title' => 'New Prospect',
            'email' => 'new@example.com',
        ]);

        $this->assertInstanceOf(Prospect::class, $result);
        $this->assertSame('org_pros_new', $result->id);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertSame('New Prospect', $request['params']['title']);
    }

    public function testUpdateSendsPutRequest(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [
                'id' => 'org_pros_xxx',
                'object' => 'prospect',
                'title' => 'Updated Prospect',
            ],
        ]);

        $result = $this->client->prospects->update('org_pros_xxx', [
            'title' => 'Updated Prospect',
        ]);

        $this->assertInstanceOf(Prospect::class, $result);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('PUT', $request['method']);
    }

    public function testDeleteSendsDeleteRequest(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [
                'id' => 'org_pros_xxx',
                'object' => 'prospect',
            ],
        ]);

        $this->client->prospects->delete('org_pros_xxx');

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('DELETE', $request['method']);
    }

    public function testOrganizationIdCanBeOverriddenPerRequest(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->prospects->list(['organization_id' => 'org_other']);

        $request = $this->httpClient->getLastRequest();
        $this->assertStringContainsString('/organizations/org_other/', $request['url']);
    }

    public function testOrganizationIdCanBeOverriddenViaRequestOptions(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $opts = new RequestOptions(organizationId: 'org_opts');
        $this->client->prospects->list([], $opts);

        $request = $this->httpClient->getLastRequest();
        $this->assertStringContainsString('/organizations/org_opts/', $request['url']);
    }

    public function testNestedResourceUsesParentId(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->projectMembers->list('org_proj_xxx');

        $request = $this->httpClient->getLastRequest();
        $this->assertStringContainsString('/projects/org_proj_xxx/members', $request['url']);
    }

    public function testThrowsExceptionWithoutOrganizationId(): void
    {
        $client = new EnlivyClient([
            'api_key' => '1|test_token',
            // No organization_id
            'http_client' => $this->httpClient,
        ]);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('organization_id');

        $client->prospects->list();
    }

    public function testNonOrgScopedServiceDoesNotRequireOrgId(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [['id' => 'org_xxx', 'object' => 'organization']],
        ]);

        $client = new EnlivyClient([
            'api_key' => '1|test_token',
            // No organization_id
            'http_client' => $this->httpClient,
        ]);

        // Organizations service doesn't require org_id
        $result = $client->organizations->list();

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testIdempotencyKeyIsPassedInHeaders(): void
    {
        $this->httpClient->addResponse(201, [
            'data' => [
                'id' => 'org_pros_test',
                'object' => 'prospect',
            ],
        ]);

        $opts = new RequestOptions(idempotencyKey: 'unique-key-123');
        $this->client->prospects->create(['title' => 'Test'], $opts);

        $request = $this->httpClient->getLastRequest();
        $this->assertArrayHasKey('Idempotency-Key', $request['headers']);
        $this->assertSame('unique-key-123', $request['headers']['Idempotency-Key']);
    }

    public function testUnknownObjectTypeReturnsEnlivyObject(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [
                'id' => 'unknown_xxx',
                'object' => 'unknown_type',
                'name' => 'Test',
            ],
        ]);

        // Use a service that returns EnlivyObject (like board)
        $result = $this->client->prospects->board();

        $this->assertInstanceOf(EnlivyObject::class, $result);
    }
}

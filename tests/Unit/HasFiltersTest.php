<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\EnlivyClient;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

final class HasFiltersTest extends TestCase
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

    public function testGlobalFiltersAlwaysPass(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->invoices->list([
            'q' => 'test search',
            'page' => 2,
            'per_page' => 10,
            'order_by' => 'created_at',
            'order' => 'desc',
            'deleted' => 0,
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('test search', $request['params']['q']);
    }

    public function testIdsGlobalFilterPasses(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->invoices->list([
            'ids' => 'id_1,id_2,id_3',
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('id_1,id_2,id_3', $request['params']['ids']);
    }

    public function testTagIdsGlobalFilterPasses(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->invoices->list([
            'tag_ids' => 'tag_1,tag_2',
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('tag_1,tag_2', $request['params']['tag_ids']);
    }

    public function testResourceSpecificFilterPasses(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->invoices->list([
            'direction' => 'outbound',
            'status' => 'draft',
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('outbound', $request['params']['direction']);
        $this->assertSame('draft', $request['params']['status']);
    }

    public function testUnknownFilterThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown filter(s): nonexistent_field');

        $this->client->invoices->list([
            'nonexistent_field' => 'value',
        ]);
    }

    public function testExceptionListsAvailableFilters(): void
    {
        try {
            $this->client->invoices->list([
                'bad_filter' => 'value',
            ]);
            $this->fail('Expected InvalidArgumentException');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('Available filters for this resource:', $e->getMessage());
            $this->assertStringContainsString('direction', $e->getMessage());
            $this->assertStringContainsString('status', $e->getMessage());
            $this->assertStringContainsString('plus global filters:', $e->getMessage());
        }
    }

    public function testIncludeParamBypassesFilterValidation(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->invoices->list([
            'include' => 'bank_account,sender_user',
            'direction' => 'outbound',
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('outbound', $request['params']['direction']);
    }

    public function testOrganizationIdBypassesFilterValidation(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->invoices->list([
            'organization_id' => 'org_custom',
            'direction' => 'inbound',
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('inbound', $request['params']['direction']);
    }

    public function testEmptyParamsPassSilently(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->invoices->list();

        $this->addToAssertionCount(1);
    }

    public function testServiceWithEmptyFiltersAllowsGlobalFilters(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        // Tags service has AVAILABLE_FILTERS = [] but should accept global filters
        $this->client->tags->list([
            'q' => 'search term',
            'per_page' => 5,
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('search term', $request['params']['q']);
    }

    public function testServiceWithEmptyFiltersRejectsResourceFilters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown filter(s): direction');

        // Tags service has no resource-specific filters
        $this->client->tags->list([
            'direction' => 'outbound',
        ]);
    }

    public function testMixedValidAndInvalidFiltersThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown filter(s): bad_filter');

        $this->client->invoices->list([
            'direction' => 'outbound',
            'bad_filter' => 'value',
        ]);
    }

    public function testFilterValidationOnlyOnList(): void
    {
        // Filters should NOT be validated on retrieve/create/update/delete
        // Only the include validation applies there
        $this->httpClient->addResponse(200, ['id' => 'org_inv_123']);

        // This should NOT throw even though 'some_field' isn't a valid filter,
        // because retrieve doesn't call validateFilters
        $this->client->invoices->retrieve('org_inv_123', [
            'some_field' => 'value',
        ]);

        $this->addToAssertionCount(1);
    }

    public function testDateRangeFiltersPass(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->invoices->list([
            'created_at_from' => '2024-01-01',
            'created_at_to' => '2024-12-31',
            'issued_at_from' => '2024-06-01',
            'issued_at_to' => '2024-06-30',
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('2024-01-01', $request['params']['created_at_from']);
    }

    public function testBooleanFiltersPass(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->invoices->list([
            'is_downloadable' => true,
            'is_tax_charged' => false,
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertTrue($request['params']['is_downloadable']);
    }
}

<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\EnlivyClient;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

final class HasIncludesTest extends TestCase
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

    public function testValidIncludesAsArrayPass(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->prospects->list([
            'include' => ['organization', 'organization_prospect_status'],
        ]);

        $request = $this->httpClient->getLastRequest();
        // Array format is normalized to comma-separated string
        $this->assertSame('organization,organization_prospect_status', $request['params']['include']);
    }

    public function testValidIncludesAsStringPass(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->prospects->list([
            'include' => 'organization,organization_prospect_status',
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('organization,organization_prospect_status', $request['params']['include']);
    }

    public function testInvalidIncludeThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid include(s): nonexistent');

        $this->client->prospects->list([
            'include' => ['nonexistent'],
        ]);
    }

    public function testInvalidIncludeListsAvailableIncludes(): void
    {
        try {
            $this->client->prospects->list([
                'include' => ['bad_include'],
            ]);
            $this->fail('Expected InvalidArgumentException');
        } catch (InvalidArgumentException $e) {
            $this->assertStringContainsString('Available includes for this resource:', $e->getMessage());
            $this->assertStringContainsString('organization', $e->getMessage());
            $this->assertStringContainsString('organization_prospect_status', $e->getMessage());
        }
    }

    public function testMixedValidAndInvalidIncludesThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid include(s): bad_one');

        $this->client->prospects->list([
            'include' => ['organization', 'bad_one'],
        ]);
    }

    public function testNoIncludeParamPassesSilently(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->prospects->list();

        $request = $this->httpClient->getLastRequest();
        $this->assertArrayNotHasKey('include', $request['params'] ?? []);
    }

    public function testEmptyIncludeArrayPassesSilently(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->prospects->list(['include' => []]);

        $this->addToAssertionCount(1);
    }

    public function testIncludeValidationOnRetrieve(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->client->prospects->retrieve('org_pros_xxx', [
            'include' => ['bad_include'],
        ]);
    }

    public function testIncludeValidationOnCreate(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->client->prospects->create([
            'title' => 'Test',
            'include' => ['bad_include'],
        ]);
    }

    public function testIncludeValidationOnUpdate(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->client->prospects->update('org_pros_xxx', [
            'title' => 'Test',
            'include' => ['bad_include'],
        ]);
    }

    public function testIncludeValidationOnDelete(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->client->prospects->delete('org_pros_xxx', [
            'include' => ['bad_include'],
        ]);
    }

    public function testArrayFormatNormalizedToCommaString(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->prospects->list([
            'include' => ['organization', 'deleted_by_user'],
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('organization,deleted_by_user', $request['params']['include']);
    }

    public function testSingleIncludeAsStringPasses(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->prospects->list([
            'include' => 'organization',
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('organization', $request['params']['include']);
    }

    public function testInvalidIncludeAsStringThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid include(s): fake');

        $this->client->prospects->list([
            'include' => 'fake',
        ]);
    }
}

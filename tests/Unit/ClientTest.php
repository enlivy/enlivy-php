<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\EnlivyClient;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Service\Organization\Prospect\ProspectService;
use Enlivy\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

final class ClientTest extends TestCase
{
    public function testApiKeyAuthenticationConfigures(): void
    {
        $client = new EnlivyClient([
            'api_key' => '1|test_token',
            'organization_id' => 'org_test',
        ]);

        $this->assertSame('org_test', $client->getOrganizationId());
        $this->assertSame('https://api.enlivy.com', $client->getApiBase());
    }

    public function testOAuthAuthenticationConfigures(): void
    {
        $client = new EnlivyClient([
            'access_token' => 'eat_test',
            'refresh_token' => 'ert_test',
            'client_id' => 'oac_xxx',
            'client_secret' => 'secret',
            'organization_id' => 'org_test',
        ]);

        $this->assertSame('org_test', $client->getOrganizationId());
    }

    public function testCustomApiBaseConfigures(): void
    {
        $client = new EnlivyClient([
            'api_key' => '1|test_token',
            'api_base' => 'http://enlivy_api.test',
        ]);

        $this->assertSame('http://enlivy_api.test', $client->getApiBase());
    }

    public function testRequiresAuthCredentials(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('api_key');

        new EnlivyClient([]);
    }

    public function testOAuthWithoutRefreshTokenStillWorks(): void
    {
        // OAuth without refresh_token should work, just won't auto-refresh on 401
        $client = new EnlivyClient([
            'access_token' => 'eat_test',
        ]);

        $this->assertNotNull($client);
    }

    public function testOAuthWithoutClientCredentialsStillWorks(): void
    {
        // OAuth without client credentials should work, just won't auto-refresh on 401
        $client = new EnlivyClient([
            'access_token' => 'eat_test',
            'refresh_token' => 'ert_test',
        ]);

        $this->assertNotNull($client);
    }

    public function testServicesAreLazilyLoaded(): void
    {
        $client = new EnlivyClient([
            'api_key' => '1|test_token',
            'organization_id' => 'org_test',
        ]);

        $this->assertInstanceOf(ProspectService::class, $client->prospects);
        // Same instance on repeated access
        $this->assertSame($client->prospects, $client->prospects);
    }

    public function testAllServicesAreAccessible(): void
    {
        $client = new EnlivyClient([
            'api_key' => '1|test_token',
            'organization_id' => 'org_test',
        ]);

        // Test a sampling of services
        $services = [
            'prospects',
            'invoices',
            'contracts',
            'users',
            'organizations',
            'projects',
            'tasks',
            'files',
            'webhooks',
        ];

        foreach ($services as $service) {
            $this->assertNotNull($client->$service, "Service '{$service}' should be accessible");
        }
    }

    public function testCustomHttpClientCanBeProvided(): void
    {
        $mockClient = new MockHttpClient();
        $mockClient->addResponse(200, ['data' => []]);

        $client = new EnlivyClient([
            'api_key' => '1|test_token',
            'organization_id' => 'org_test',
            'http_client' => $mockClient,
        ]);

        // Accessing prospects and making a request should use our mock
        $client->prospects->list();

        $this->assertSame(1, $mockClient->getRequestCount());
    }
}

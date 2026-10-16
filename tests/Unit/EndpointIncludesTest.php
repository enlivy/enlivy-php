<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\EnlivyClient;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class EndpointIncludesTest extends TestCase
{
    private MockHttpClient $http;

    private EnlivyClient $client;

    protected function setUp(): void
    {
        $this->http = new MockHttpClient();
        $this->client = new EnlivyClient(['api_key' => '1|test_token', 'organization_id' => 'org_default', 'http_client' => $this->http]);
    }

    /** @return iterable<string, array{callable(EnlivyClient, array): mixed}> */
    public static function endpointsWithoutIncludes(): iterable
    {
        yield 'guideline revisions' => [fn (EnlivyClient $c, array $p) => $c->guidelines->listRevisions('org_guide_1', $p)];
        yield 'guideline revision' => [fn (EnlivyClient $c, array $p) => $c->guidelines->retrieveRevision('org_guide_1', 'rev_1', $p)];
        yield 'playbook revisions' => [fn (EnlivyClient $c, array $p) => $c->playbooks->listRevisions('org_play_1', $p)];
        yield 'playbook revision' => [fn (EnlivyClient $c, array $p) => $c->playbooks->retrieveRevision('org_play_1', 'rev_1', $p)];
        yield 'prospect board' => [fn (EnlivyClient $c, array $p) => $c->prospects->board($p)];
        yield 'pipeline board' => [fn (EnlivyClient $c, array $p) => $c->prospectPipelines->board('org_pipe_1', $p)];
        yield 'user activity' => [fn (EnlivyClient $c, array $p) => $c->organizationUsers->activity('org_user_1', $p)];
        yield 'bank balance' => [fn (EnlivyClient $c, array $p) => $c->bankAccounts->balance($p)];
        yield 'tenant billing state' => [fn (EnlivyClient $c, array $p) => $c->tenantBilling->state($p)];
    }

    #[DataProvider('endpointsWithoutIncludes')]
    public function testAnEndpointWithoutIncludesRefusesTheServiceIncludes(callable $call): void
    {
        $this->expectException(InvalidArgumentException::class);

        $call($this->client, ['include' => 'deleted_by_user']);
    }

    public function testNetworkExchangeAssistiveIncludesAreRetrieveOnly(): void
    {
        $this->http->addResponse(200, ['data' => ['id' => 'org_inv_peppol_1']]);
        $this->client->invoiceNetworkExchanges->retrieve('org_inv_peppol_1', ['include' => 'tax_mapping,recording_suggestions']);

        $this->expectException(InvalidArgumentException::class);
        $this->client->invoiceNetworkExchanges->list(['include' => 'tax_mapping']);
    }

    public function testReferralsTakeTheOrganizationsOfEachReferredUser(): void
    {
        $this->http->addResponse(200, ['data' => [], 'meta' => ['pagination' => ['total' => 0, 'current_page' => 1, 'total_pages' => 1]]]);
        $this->client->invitationCodes->referrals(['include' => 'organizations']);

        $this->assertSame('organizations', $this->http->getLastRequest()['params']['include']);
    }

    public function testAnOrganizationIncludesItsTenantBillingAndNoLongerTheRemovedKeys(): void
    {
        $this->http->addResponse(200, ['data' => ['id' => 'org_1']]);
        $this->client->organizations->retrieve('org_1', ['include' => 'tenant_billing,tenant_billing.billing_schedule']);

        $this->expectException(InvalidArgumentException::class);
        $this->client->organizations->retrieve('org_1', ['include' => 'membership_features']);
    }
}

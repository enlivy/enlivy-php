<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\EnlivyPortalClient;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Organization\Proposal;
use Enlivy\Service\ClientPortal\BillingPackageService;
use Enlivy\Service\ClientPortal\ProposalService;
use Enlivy\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PortalIncludesTest extends TestCase
{
    private MockHttpClient $httpClient;
    private EnlivyPortalClient $portal;

    protected function setUp(): void
    {
        $this->httpClient = new MockHttpClient();
        $this->portal = new EnlivyPortalClient([
            'portal_token' => 'portal_tok',
            'organization_id' => 'org_default',
            'http_client' => $this->httpClient,
        ]);
    }

    /**
     * @return array<string, array{string, list<string>, string}>
     */
    public static function portalDocuments(): array
    {
        return [
            'invoices' => ['invoices', ['line_items', 'latest_charge_log'], 'deleted_by_user'],
            'receipts' => ['receipts', ['invoice', 'contract'], 'organization'],
            'contracts' => ['contracts', ['contract_parties', 'abilities'], 'parent_contract'],
            'proposals' => ['proposals', ['payments', 'payments.line_items'], 'prospect'],
        ];
    }

    /**
     * @param list<string> $accepted
     */
    #[DataProvider('portalDocuments')]
    public function testListAndRetrieveSendTheIncludesThePortalLists(string $accessor, array $accepted, string $refused): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);
        $this->portal->{$accessor}->list(['include' => $accepted]);
        $this->assertSame(implode(',', $accepted), $this->httpClient->getLastRequest()['params']['include']);

        $this->httpClient->addResponse(200, ['data' => ['id' => 'x_1']]);
        $this->portal->{$accessor}->retrieve('x_1', ['include' => implode(',', $accepted)]);
        $this->assertStringContainsString("{$accessor}/x_1", $this->httpClient->getLastRequest()['url']);
    }

    /**
     * @param list<string> $accepted
     */
    #[DataProvider('portalDocuments')]
    public function testAnIncludeThePortalDoesNotListIsRefusedBeforeSending(string $accessor, array $accepted, string $refused): void
    {
        foreach (['list', 'retrieve'] as $method) {
            try {
                $method === 'list'
                    ? $this->portal->{$accessor}->list(['include' => [$refused]])
                    : $this->portal->{$accessor}->retrieve('x_1', ['include' => $refused]);
                $this->fail("{$accessor}->{$method} accepted {$refused}");
            } catch (InvalidArgumentException $e) {
                $this->assertStringContainsString($refused, $e->getMessage());
            }
        }

        $this->assertSame(0, $this->httpClient->getRequestCount());
    }

    public function testOnlyTheDeclaredNestedProposalPathIsAccepted(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->portal->proposals->list(['include' => 'invoice.line_items']);
    }

    public function testClaimTakesTheProposalIncludes(): void
    {
        $this->assertSame(ProposalService::AVAILABLE_INCLUDES, BillingPackageService::CLAIM_INCLUDES);

        $this->httpClient->addResponse(201, ['data' => ['id' => 'org_prop_1', 'object' => 'proposal', 'locale' => 'ro']]);
        $proposal = $this->portal->billingPackages->claim('org_billpkg_1', ['include' => ['payments', 'payments.line_items']]);

        $this->assertInstanceOf(Proposal::class, $proposal);
        $this->assertSame('ro', $proposal->locale);
        $request = $this->httpClient->getLastRequest();
        $this->assertStringContainsString('billing-packages/org_billpkg_1/claim', $request['url']);
        $this->assertSame('payments,payments.line_items', $request['params']['include']);

        $this->expectException(InvalidArgumentException::class);
        $this->portal->billingPackages->claim('org_billpkg_1', ['include' => 'organization']);
    }
}

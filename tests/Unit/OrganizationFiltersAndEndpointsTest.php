<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\EnlivyClient;
use Enlivy\EnlivyObject;
use Enlivy\Organization\BankTransaction;
use Enlivy\Organization\Invoice;
use Enlivy\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class OrganizationFiltersAndEndpointsTest extends TestCase
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

    /**
     * @return array<string, array{string, array<string, mixed>}>
     */
    public static function honouredFilters(): array
    {
        return [
            'contracts awaiting a signature' => ['contracts', ['awaiting_signature' => true]],
            'contracts awaiting this party' => ['contracts', ['organization_user_id' => 'org_user_1', 'awaiting_my_signature' => true]],
            'contracts awaiting the others' => ['contracts', ['organization_user_id' => 'org_user_1', 'awaiting_others_signature' => true]],
            'payslips by either party' => ['payslips', ['organization_user_id' => 'org_user_1']],
            'subscription schedules only' => ['billingSchedules', ['subscription_only' => true, 'exclude_tenant_billing' => true]],
        ];
    }

    /**
     * @param array<string, mixed> $filters
     */
    #[DataProvider('honouredFilters')]
    public function testListSendsTheFiltersTheApiHonours(string $accessor, array $filters): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->{$accessor}->list($filters);

        $params = $this->httpClient->getLastRequest()['params'];
        foreach ($filters as $key => $value) {
            $this->assertSame($value, $params[$key]);
        }
    }

    public function testInvoiceCarriesItsLanguages(): void
    {
        $this->httpClient->addResponse(201, ['data' => [
            'id' => 'org_inv_1',
            'object' => 'invoice',
            'locale' => null,
            'locale_list' => ['ro'],
        ]]);

        $invoice = $this->client->invoices->create(['locale' => null, 'locale_list' => ['ro']]);

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertNull($invoice->locale);
        $this->assertSame(['ro'], $invoice->locale_list);
        $this->assertSame(['ro'], $this->httpClient->getLastRequest()['params']['locale_list']);
    }

    public function testRecordDocumentPaymentPostsToTheTransactionAndAnswersIt(): void
    {
        $this->httpClient->addResponse(201, ['data' => ['id' => 'org_banktran_1', 'object' => 'bank_transaction']]);

        $transaction = $this->client->bankTransactions->recordDocumentPayment('org_banktran_1', [
            'connection_entity_type' => 'invoice',
            'connection_entity_id' => 'org_inv_1',
            'amount' => 250.00,
            'include' => ['connection_entities'],
        ]);

        $this->assertInstanceOf(BankTransaction::class, $transaction);
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringEndsWith('/organizations/org_default/bank-transactions/org_banktran_1/document-payments', $request['url']);
        $this->assertSame('org_inv_1', $request['params']['connection_entity_id']);
        $this->assertSame('connection_entities', $request['params']['include']);
    }

    public function testSummaryOverviewReadsTheRecordPageSections(): void
    {
        $this->httpClient->addResponse(200, ['data' => [
            'documents' => ['invoice' => ['received' => 3, 'sent' => 0]],
            'card' => ['addresses' => 1, 'bank_accounts' => 0, 'payment_methods' => 2],
            'activity' => ['total' => 7],
            'last_activity_at' => null,
        ]]);

        $summary = $this->client->organizationUsers->summaryOverview('org_user_1');

        $this->assertSame(EnlivyObject::class, $summary::class);
        $this->assertSame(3, $summary->documents['invoice']['received']);
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('GET', $request['method']);
        $this->assertStringEndsWith('/organizations/org_default/users/org_user_1/overview/summary', $request['url']);
    }
}

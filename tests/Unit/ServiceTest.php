<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\Collection;
use Enlivy\EnlivyClient;
use Enlivy\EnlivyObject;
use Enlivy\Enums\Organization\Environments;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Organization;
use Enlivy\Organization\BillingSchedule;
use Enlivy\Organization\ContractConnection;
use Enlivy\Organization\Prospect;
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
            'http_client' => $this->httpClient,
        ]);

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

        $result = $this->client->prospects->board();

        $this->assertInstanceOf(EnlivyObject::class, $result);
    }

    public function testTypedResourceCarriesLastResponse(): void
    {
        $this->httpClient->addResponse(
            200,
            ['data' => ['id' => 'org_pros_xxx', 'object' => 'prospect', 'title' => 'X']],
            ['X-Request-Id' => 'req_123'],
        );

        $result = $this->client->prospects->retrieve('org_pros_xxx');

        $this->assertNotNull($result->lastResponse());
        $this->assertSame(200, $result->lastResponse()->statusCode);
        $this->assertSame('req_123', $result->lastResponse()->getHeader('X-Request-Id'));
    }

    public function testBillingScheduleFromBillingPackagePostsAndExposesChargeMeta(): void
    {
        $this->httpClient->addResponse(201, [
            'data' => [
                'id' => 'org_bill_sch_new',
                'object' => 'billing_schedule',
                'status' => 'active',
            ],
            'meta' => [
                'charge_result' => [
                    'status' => 'succeeded',
                    'error_code' => null,
                    'error_message' => null,
                    'provider_reference' => 'charge:ch_123',
                    'next_action_url' => null,
                ],
                'invoice_id' => 'org_inv_123',
            ],
        ]);

        $result = $this->client->billingSchedules->fromBillingPackage([
            'organization_billing_package_id' => 'org_bp_1',
            'organization_sender_user_id' => 'org_user_s',
            'organization_receiver_user_id' => 'org_user_r',
            'status' => 'active',
        ]);

        $this->assertInstanceOf(BillingSchedule::class, $result);
        $this->assertSame('org_bill_sch_new', $result->id);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringContainsString('/billing-schedules/from-billing-package', $request['url']);

        $meta = $result->lastResponse()?->json['meta'] ?? [];
        $this->assertSame('succeeded', $meta['charge_result']['status']);
        $this->assertSame('org_inv_123', $meta['invoice_id']);
    }

    public function testMiscDetermineIsTaxChargedSendsGet(): void
    {
        $this->httpClient->addResponse(200, [
            'is_tax_charged' => true,
            'reason' => 'domestic',
            'needs_attention' => false,
        ]);

        $result = $this->client->misc->determineIsTaxCharged([
            'country_code' => 'RO',
            'is_business_entity' => true,
        ]);

        $this->assertInstanceOf(EnlivyObject::class, $result);
        $this->assertTrue($result->is_tax_charged);
        $this->assertSame('domestic', $result->reason);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('GET', $request['method']);
        $this->assertStringContainsString('/misc/determine-is-tax-charged', $request['url']);
    }

    public function testInvoiceChargeLogsListHitsTheNestedInvoicesPath(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->invoiceChargeLogs->list(['organization_invoice_id' => 'org_inv_1']);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('GET', $request['method']);
        $this->assertStringContainsString('/organizations/org_default/invoices/charge-logs', $request['url']);
    }

    public function testInvoiceNotificationLogsListHitsTheNestedInvoicesPathAndAcceptsTypeFilters(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->invoiceNotificationLogs->list([
            'types' => 'email_reminder_upcoming,email_reminder_overdue',
            'created_at_from' => '2026-07-01T00:00:00Z',
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertStringContainsString('/organizations/org_default/invoices/notification-logs', $request['url']);
        $this->assertSame('email_reminder_upcoming,email_reminder_overdue', $request['params']['types']);
        $this->assertSame('2026-07-01T00:00:00Z', $request['params']['created_at_from']);
    }

    public function testInvoiceNotificationLogRestoreKeepsItsNestedPath(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_inv_nl_1']]);

        $this->client->invoiceNotificationLogs->restore('org_inv_nl_1');

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringContainsString(
            '/organizations/org_default/invoices/notification-logs/restore/org_inv_nl_1',
            $request['url'],
        );
    }

    public function testScheduledRemindersHydrateWithoutAnIdAndCarryTheWindowMeta(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [
                [
                    'organization_invoice_id' => 'org_inv_1',
                    'organization_invoice_number' => 'INV-0001',
                    'type' => 'email_reminder_upcoming',
                    'scheduled_for' => '2026-08-01T05:00:00Z',
                    'sequence' => 1,
                    'due_at' => '2026-08-04T00:00:00Z',
                    'total' => '120.000000',
                    'currency' => 'EUR',
                    'recipient_email' => 'billing@example.com',
                ],
            ],
            'meta' => ['from' => '2026-08-01T00:00:00Z', 'to' => '2026-08-31T00:00:00Z', 'count' => 1],
        ]);

        $reminders = $this->client->invoiceScheduledReminders->list([
            'from' => '2026-08-01T00:00:00Z',
            'to' => '2026-08-31T00:00:00Z',
            'type' => 'email_reminder_upcoming',
        ]);

        $this->assertInstanceOf(Collection::class, $reminders);
        $this->assertCount(1, $reminders->getData());

        $reminder = $reminders->getData()[0];
        $this->assertInstanceOf(EnlivyObject::class, $reminder);
        $this->assertSame('email_reminder_upcoming', $reminder->type);
        $this->assertSame(1, $reminder->sequence);
        $this->assertSame('INV-0001', $reminder->organization_invoice_number);

        $this->assertFalse($reminders->hasMore());
        $this->assertSame('2026-08-31T00:00:00Z', $reminders->meta['to']);

        $request = $this->httpClient->getLastRequest();
        $this->assertStringContainsString('/organizations/org_default/invoices/scheduled-reminders', $request['url']);
    }

    public function testScheduledRemindersRejectAnUnknownFilter(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->client->invoiceScheduledReminders->list(['status' => 'pending']);
    }

    public function testContractConnectionsReturnTypedRows(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [
                [
                    'id' => 'org_inv_1',
                    'entity' => 'invoice',
                    'title' => 'INV-0001',
                    'status' => 'issued',
                    'total' => '120.000000',
                    'currency' => 'EUR',
                    'created_at' => '2026-07-01T10:00:00Z',
                    'updated_at' => '2026-07-01T10:00:00Z',
                ],
            ],
        ]);

        $connections = $this->client->contracts->connections('org_cont_1', ['entity' => ['invoice']]);

        $row = $connections->getData()[0];
        $this->assertInstanceOf(ContractConnection::class, $row);
        $this->assertSame('invoice', $row->entity);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('GET', $request['method']);
        $this->assertStringContainsString('/organizations/org_default/contracts/org_cont_1/connections', $request['url']);
    }

    public function testProductImportLifecycleHitsTheProductImportPaths(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => ['field_position_alias' => 1, 'field_position_price' => 3],
        ]);
        $this->client->products->importDetectColumns(['headers' => ['SKU', 'Name', 'Price']]);
        $this->assertStringContainsString(
            '/organizations/org_default/products/imports/detect-columns',
            $this->httpClient->getLastRequest()['url'],
        );

        $this->httpClient->addResponse(200, [
            'data' => ['id' => 'org_pd_1', 'type' => 'product_import'],
        ]);
        $import = $this->client->products->importCreate(['start_from_row' => 2]);
        $this->assertInstanceOf(EnlivyObject::class, $import);
        $this->assertStringContainsString(
            '/organizations/org_default/products/imports',
            $this->httpClient->getLastRequest()['url'],
        );

        $this->httpClient->addResponse(200, [
            'data' => ['id' => 'org_pd_1', 'summary_json' => ['stop_reason' => 'usage_limit']],
        ]);
        $this->client->products->importRetrieve('org_pd_1');
        $this->assertStringContainsString(
            '/organizations/org_default/products/imports/org_pd_1',
            $this->httpClient->getLastRequest()['url'],
        );

        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_pd_1']]);
        $this->client->products->importResume('org_pd_1');

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringContainsString(
            '/organizations/org_default/products/imports/org_pd_1/resume',
            $request['url'],
        );
    }

    public function testOrganizationUserImportsResolveUnderTheUsersPath(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_pd_2']]);

        $this->client->organizationUsers->importCreate(['start_from_row' => 2]);

        $this->assertStringContainsString(
            '/organizations/org_default/users/imports',
            $this->httpClient->getLastRequest()['url'],
        );
    }

    public function testBankTransactionAndProspectImportsAreResumable(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_pd_3']]);
        $this->client->bankTransactions->importResume('org_pd_3');
        $this->assertStringContainsString(
            '/organizations/org_default/bank-transactions/imports/org_pd_3/resume',
            $this->httpClient->getLastRequest()['url'],
        );

        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_pd_4']]);
        $this->client->prospects->importResume('org_pd_4');
        $this->assertStringContainsString(
            '/organizations/org_default/prospects/imports/org_pd_4/resume',
            $this->httpClient->getLastRequest()['url'],
        );
    }

    /**
     * Billing-schedule imports are create/list/retrieve only — there is no
     * resume route behind them, so the method must not exist on that service.
     */
    public function testBillingScheduleImportsAreNotResumable(): void
    {
        $this->assertTrue(method_exists($this->client->billingSchedules, 'importCreate'));
        $this->assertFalse(method_exists($this->client->billingSchedules, 'importResume'));
        $this->assertFalse(method_exists($this->client->billingSchedules, 'importDetectColumns'));
    }

    public function testCreateSandboxReturnsATypedSandboxOrganization(): void
    {
        $this->httpClient->addResponse(201, [
            'data' => [
                'id' => 'org_2',
                'object' => 'organization',
                'organization_id' => 'org_1',
                'environment' => 'sandbox',
                'name' => 'Acme Sandbox',
            ],
        ]);

        $sandbox = $this->client->organizations->createSandbox('org_1', ['name' => 'Acme Sandbox']);

        $this->assertInstanceOf(Organization::class, $sandbox);
        $this->assertSame(Environments::SANDBOX->value, $sandbox->environment);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringContainsString('/organizations/org_1/sandboxes', $request['url']);
    }

    /**
     * The abilities endpoints answer with a plain list, not a record resource —
     * the typed return these carried before could only ever raise a TypeError.
     */
    public function testUserRoleAbilitiesReturnTheRawAbilityList(): void
    {
        $this->httpClient->addResponse(200, [
            ['id' => null, 'organization_user_role_id' => 'org_role_1', 'ability' => 'invoices.manage'],
            ['id' => null, 'organization_user_role_id' => 'org_role_1', 'ability' => 'products.manage'],
        ]);

        $abilities = $this->client->userRoleAbilities->list('org_role_1');

        $this->assertInstanceOf(EnlivyObject::class, $abilities);
        $this->assertCount(2, $abilities->toArray());
        $this->assertStringContainsString(
            '/organizations/org_default/user-roles/org_role_1/abilities',
            $this->httpClient->getLastRequest()['url'],
        );

        $this->httpClient->addResponse(201, [
            ['id' => 'org_ura_1', 'ability' => 'invoices.manage'],
        ]);
        $synced = $this->client->userRoleAbilities->sync('org_role_1', ['abilities' => ['invoices.manage']]);
        $this->assertInstanceOf(EnlivyObject::class, $synced);
        $this->assertSame('POST', $this->httpClient->getLastRequest()['method']);

        $this->httpClient->addResponse(200, ['status' => 'ok']);
        $removed = $this->client->userRoleAbilities->delete('org_role_1', ['abilities' => ['invoices.manage']]);
        $this->assertSame('ok', $removed->status);
        $this->assertSame('DELETE', $this->httpClient->getLastRequest()['method']);
    }

    public function testOAuthAuthorizationUpdateSendsPatch(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => ['id' => 'oauth_cua_1', 'scopes' => ['accounting:read']],
        ]);

        $this->client->oauthAuthorizations->update('oauth_cua_1', ['scopes' => ['accounting:read']]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('PATCH', $request['method']);
        $this->assertStringContainsString('/oauth/authorizations/oauth_cua_1', $request['url']);
    }
}

<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\Collection;
use Enlivy\EnlivyClient;
use Enlivy\EnlivyObject;
use Enlivy\Enums\BlockedIdentifier\Sources;
use Enlivy\Enums\BlockedIdentifier\Types;
use Enlivy\Enums\Organization\Environments;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Organization;
use Enlivy\Organization\BillingSchedule;
use Enlivy\Organization\BlockedIdentifier;
use Enlivy\Organization\Connection;
use Enlivy\Organization\Employment;
use Enlivy\Organization\EventTrail;
use Enlivy\Organization\Invoice;
use Enlivy\Organization\ProposalNotificationLog;
use Enlivy\Organization\PayslipLine;
use Enlivy\Organization\Prospect;
use Enlivy\Organization\ProspectDuplicate;
use Enlivy\Organization\ProspectPipeline;
use Enlivy\Organization\ProspectStage;
use Enlivy\Organization\WorkingTimeDay;
use Enlivy\Organization\WorkingTimeTerm;
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

    public function testProposalNotificationLogsListHitsTheNestedProposalsPathAndAcceptsTypeFilters(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->proposalNotificationLogs->list([
            'types' => 'email_seller_viewed,email_seller_accepted',
            'organization_proposal_id' => 'org_prop_1',
            'created_at_from' => '2026-08-01T00:00:00Z',
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertStringContainsString('/organizations/org_default/proposals/notification-logs', $request['url']);
        $this->assertSame('email_seller_viewed,email_seller_accepted', $request['params']['types']);
        $this->assertSame('org_prop_1', $request['params']['organization_proposal_id']);
        $this->assertSame('2026-08-01T00:00:00Z', $request['params']['created_at_from']);
    }

    public function testProposalNotificationLogRetrieveHydratesTypedAndRestoreKeepsItsNestedPath(): void
    {
        $this->httpClient->addResponse(200, ['data' => [
            'id' => 'org_prop_nl_1',
            'object' => 'proposal_notification_log',
            'type' => 'email_seller_accepted',
            'is_seller_notification' => true,
        ]]);

        $log = $this->client->proposalNotificationLogs->retrieve('org_prop_nl_1');

        $this->assertInstanceOf(ProposalNotificationLog::class, $log);
        $this->assertSame('email_seller_accepted', $log->type);
        $this->assertTrue($log->is_seller_notification);

        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_prop_nl_1']]);
        $this->client->proposalNotificationLogs->restore('org_prop_nl_1');

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringContainsString(
            '/organizations/org_default/proposals/notification-logs/restore/org_prop_nl_1',
            $request['url'],
        );
    }

    public function testPortalProposalRefreshConversionAndCurrencyAcceptance(): void
    {
        $portal = new \Enlivy\EnlivyPortalClient([
            'portal_token' => 'portal_tok',
            'organization_id' => 'org_default',
            'http_client' => $this->httpClient,
        ]);

        $this->httpClient->addResponse(200, ['data' => ['amount' => '4995.00', 'currency' => 'RON']]);
        $conversion = $portal->proposals->refreshConversion('org_prop_1');

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringContainsString('proposals/org_prop_1/refresh-conversion', $request['url']);
        $this->assertSame('4995.00', $conversion->amount);

        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_prop_1', 'object' => 'proposal']]);
        $portal->proposals->accept('org_prop_1', [
            'billed_currency' => 'RON',
            'displayed_amount' => 4995.00,
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertStringContainsString('proposals/org_prop_1/accept', $request['url']);
        $this->assertSame('RON', $request['params']['billed_currency']);
        $this->assertSame(4995.00, $request['params']['displayed_amount']);
    }

    public function testPortalProposalPaysInOneCallAndAnswersWhatTheBrowserConfirms(): void
    {
        $portal = new \Enlivy\EnlivyPortalClient([
            'portal_token' => 'portal_tok',
            'organization_id' => 'org_default',
            'http_client' => $this->httpClient,
        ]);
        $this->httpClient->addResponse(200, ['data' => [
            'payment_method_kind' => 'card',
            'payment_provider' => 'stripe',
            'client_secret' => 'pi_1_secret',
        ]]);

        $payment = $portal->proposals->pay('org_prop_1', [
            'payment_method_kind' => 'card',
            'organization_user_payment_method_id' => 'org_userpm_1',
        ]);

        $this->assertNotInstanceOf(\Enlivy\Organization\Proposal::class, $payment);
        $this->assertSame('pi_1_secret', $payment->client_secret);
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringEndsWith('/proposals/org_prop_1/pay', $request['url']);
        $this->assertSame('card', $request['params']['payment_method_kind']);
        $this->assertFalse(method_exists($portal->proposals, 'selectPaymentMethod'));
        $this->assertFalse(method_exists($portal->proposals, 'createPaymentIntent'));
    }

    public function testPortalCustomerOpensACheckoutSessionAndGetsItsTokenOnce(): void
    {
        $portal = new \Enlivy\EnlivyPortalClient([
            'portal_token' => 'portal_tok',
            'organization_id' => 'org_default',
            'http_client' => $this->httpClient,
        ]);
        $this->httpClient->addResponse(201, [
            'data' => ['id' => 'org_chk_sess_1', 'object' => 'checkout_session', 'payment_method_kinds' => ['card']],
            'meta' => ['client_token' => 'tok_once'],
        ]);

        $session = $portal->billingPackages->openCheckoutSession('org_bp_1', [
            'organization_billing_package_subscription_term_id' => 'org_bp_st_1',
            'source_campaign' => 'autumn',
        ]);

        $this->assertSame(['card'], $session->payment_method_kinds);
        $this->assertSame('tok_once', $session->lastResponse()?->json['meta']['client_token']);
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringEndsWith('/billing-packages/org_bp_1/checkout-session', $request['url']);
        $this->assertSame('autumn', $request['params']['source_campaign']);
    }

    public function testStaffSendAnInvoicesPaymentLink(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['message' => 'sent']]);

        $result = $this->client->invoices->sendPaymentLink('org_inv_1', ['message' => 'Pay online']);

        $this->assertNotInstanceOf(\Enlivy\Organization\Invoice::class, $result);
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringEndsWith('/organizations/org_default/invoices/org_inv_1/payment-link', $request['url']);
        $this->assertSame('Pay online', $request['params']['message']);
    }

    public function testMiscPreviewsABillingPackagePriceForAVisitor(): void
    {
        $this->httpClient->addResponse(200, ['data' => [
            'mode' => 'payment',
            'total' => '121.000000',
            'payments' => [['frequency' => 'monthly', 'occurrences' => null]],
        ]]);

        $preview = $this->client->misc->calculateBillingPackagePrice([
            'organization_billing_package_id' => 'org_bp_1',
            'country_code' => 'RO',
            'is_business_entity' => false,
        ]);

        $this->assertSame('payment', $preview->mode);
        $this->assertNull($preview->payments[0]['occurrences']);
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('GET', $request['method']);
        $this->assertStringContainsString('/organizations/org_default/misc/calculate-billing-package-price', $request['url']);
        $this->assertSame('org_bp_1', $request['params']['organization_billing_package_id']);
    }

    public function testProspectAnalyticsResolveUnderTheProspectsPath(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->analytics->prospectsByType('funnel', [
            'start_date' => '2026-08-01T00:00:00Z',
            'end_date' => '2026-08-31T00:00:00Z',
            'convert_to_currency' => 'EUR',
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertStringContainsString('/organizations/org_default/prospects/analytics/funnel', $request['url']);
        $this->assertSame('EUR', $request['params']['convert_to_currency']);
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

    public function testConnectionsReturnTypedRowsAndFacetCounts(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [
                [
                    'id' => 'org_inv_1',
                    'entity' => 'invoice',
                    'liveness' => 'live',
                    'item' => ['id' => 'org_inv_1', 'currency' => 'EUR'],
                ],
            ],
            'meta' => [
                'connections' => [
                    'entities' => [
                        ['entity' => 'invoice', 'restricted' => false, 'live' => 1, 'historical' => 0, 'trashed' => 0, 'total' => 1],
                    ],
                    'totals' => ['live' => 1, 'historical' => 0, 'trashed' => 0, 'total' => 1],
                ],
            ],
        ]);

        $connections = $this->client->contracts->connections('org_cont_1', ['entity' => ['invoice'], 'liveness' => 'live']);

        $row = $connections->getData()[0];
        $this->assertInstanceOf(Connection::class, $row);
        $this->assertSame('invoice', $row->entity);
        $this->assertSame('live', $row->liveness);

        // The facet counts live in meta, which is only reachable because Collection exposes it.
        $this->assertSame(1, $connections->getMeta()['connections']['totals']['total']);

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

    public function testOrganizationRetrieveIsTypedWithoutAnObjectField(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_1', 'name' => 'Acme']]);

        $organization = $this->client->organizations->retrieve('org_1');

        $this->assertInstanceOf(Organization::class, $organization);
        $this->assertSame('Acme', $organization->name);
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

    public function testBlockedIdentifierCrudResolvesUnderTheOrganization(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [[
                'id' => 'org_bi_1',
                'object' => 'blocked_identifier',
                'source' => 'platform',
                'type' => 'email_domain',
                'value' => 'spam.example',
                'normalized_value' => 'spam.example',
            ]],
        ]);

        $rows = $this->client->blockedIdentifiers->list([
            'type' => ['email', 'email_domain'],
            'source' => Sources::ALL->value,
        ]);

        $this->assertInstanceOf(BlockedIdentifier::class, $rows->getData()[0]);
        $this->assertSame('platform', $rows->getData()[0]->source);
        $this->assertStringContainsString(
            '/organizations/org_default/blocked-identifiers',
            $this->httpClient->getLastRequest()['url'],
        );

        $this->httpClient->addResponse(201, [
            'data' => ['id' => 'org_bi_2', 'object' => 'blocked_identifier', 'type' => 'email'],
        ]);
        $created = $this->client->blockedIdentifiers->create([
            'type' => Types::EMAIL->value,
            'value' => 'blocked@example.com',
            'reason' => 'chargeback',
        ]);
        $this->assertInstanceOf(BlockedIdentifier::class, $created);
        $this->assertSame('POST', $this->httpClient->getLastRequest()['method']);

        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_bi_2', 'object' => 'blocked_identifier']]);
        $this->client->blockedIdentifiers->delete('org_bi_2');
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('DELETE', $request['method']);
        $this->assertStringContainsString('/blocked-identifiers/org_bi_2', $request['url']);
    }

    public function testBlockedIdentifierListRejectsAnUnknownFilter(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->client->blockedIdentifiers->list(['value' => 'spam.example']);
    }

    public function testDetermineIsBlockedHelpersHitTheMiscPlane(): void
    {
        $this->httpClient->addResponse(200, [
            'is_blocked' => true,
            'type' => 'email_domain',
            'source' => 'platform',
            'value' => 'spam.example',
            'reason' => null,
        ]);

        $answer = $this->client->misc->determineIsEmailBlocked(['value' => 'x@spam.example']);

        $this->assertTrue($answer->is_blocked);
        $this->assertSame('email_domain', $answer->type);
        $this->assertStringContainsString(
            '/organizations/org_default/misc/determine-is-email-blocked',
            $this->httpClient->getLastRequest()['url'],
        );

        $this->httpClient->addResponse(200, ['is_blocked' => false]);
        $this->client->misc->determineIsPhoneNumberBlocked(['value' => '0746047047', 'country_code' => 'RO']);
        $this->assertStringContainsString(
            '/organizations/org_default/misc/determine-is-phone-number-blocked',
            $this->httpClient->getLastRequest()['url'],
        );
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

    public function testTrashedItemsReportsInventoryAndPurges(): void
    {
        $this->httpClient->addResponse(200, [
            'total_items' => 12,
            'reclaimable_bytes' => 4096,
            'entities' => [
                ['entity' => 'files', 'count' => 12, 'purgeable' => true],
                ['entity' => 'invoices', 'count' => 3, 'purgeable' => false],
            ],
        ]);

        $inventory = $this->client->trashedItems->list();

        $this->assertSame(12, $inventory->total_items);
        $this->assertStringContainsString(
            '/organizations/org_default/trashed-items',
            $this->httpClient->getLastRequest()['url'],
        );

        $this->httpClient->addResponse(200, ['deleted' => 12, 'blocked' => 0, 'errored' => 0, 'entities' => []]);
        $result = $this->client->trashedItems->purge(['entities' => ['files', 'tags']]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('DELETE', $request['method']);
        $this->assertSame(12, $result->deleted);
        $this->assertSame(['entities' => ['files', 'tags']], $request['params']);
    }

    public function testTenantBillingIdentityReadAndUpdate(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => ['is_custom' => false, 'custom_identity_name' => null, 'effective' => ['name' => 'Acme']],
        ]);

        $identity = $this->client->tenantBilling->billingIdentity();

        $this->assertFalse($identity->is_custom);
        $this->assertStringContainsString(
            '/organizations/org_default/tenant-billing/billing-identity',
            $this->httpClient->getLastRequest()['url'],
        );

        $this->httpClient->addResponse(200, ['data' => ['is_custom' => true]]);
        $this->client->tenantBilling->updateBillingIdentity(['custom_identity_name' => null]);

        $this->assertSame('PUT', $this->httpClient->getLastRequest()['method']);
    }

    public function testTenantBillingInvoiceChargeReturnsTypedInvoice(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => ['id' => 'org_inv_1', 'object' => 'invoice', 'charge_retry_count' => 2],
            'meta' => ['charge_result' => ['status' => 'succeeded']],
        ]);

        $invoice = $this->client->tenantBillingInvoices->charge('org_inv_1');

        $request = $this->httpClient->getLastRequest();
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertSame('POST', $request['method']);
        $this->assertStringContainsString(
            '/organizations/org_default/tenant-billing/invoices/org_inv_1/charge',
            $request['url'],
        );
    }

    public function testOrganizationUsersExposeEventTrails(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [['id' => 'org_et_1', 'object' => 'event_trail', 'event_type' => 'updated']],
            'meta' => ['pagination' => ['total' => 1, 'count' => 1, 'per_page' => 15, 'current_page' => 1, 'total_pages' => 1]],
        ]);

        $trails = $this->client->organizationUsers->eventTrails();

        $this->assertInstanceOf(EventTrail::class, $trails->getData()[0]);
        $this->assertStringContainsString(
            '/organizations/org_default/users/event-trails',
            $this->httpClient->getLastRequest()['url'],
        );

        $this->httpClient->addResponse(200, [
            'data' => ['id' => 'org_et_1', 'object' => 'event_trail'],
        ]);
        $this->client->organizationUsers->retrieveEventTrail('org_et_1');
        $this->assertStringContainsString(
            '/organizations/org_default/users/event-trails/org_et_1',
            $this->httpClient->getLastRequest()['url'],
        );
    }

    public function testProspectActivityAcceptsTheFileInclude(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [['id' => 'org_pa_1', 'object' => 'prospect_activity', 'organization_file_id' => 'org_file_1']],
            'meta' => ['pagination' => ['total' => 1, 'count' => 1, 'per_page' => 15, 'current_page' => 1, 'total_pages' => 1]],
        ]);

        $activities = $this->client->prospectActivities->list(['include' => 'organization_file']);

        $this->assertSame('org_file_1', $activities->getData()[0]->organization_file_id);
    }

    public function testInvoicesAndReceiptsFilterOnTheDateTheyFallDue(): void
    {
        foreach (['invoices', 'receipts'] as $accessor) {
            $this->httpClient->addResponse(200, ['data' => []]);

            $this->client->{$accessor}->list([
                'due_at_from' => '2026-09-01T00:00:00Z',
                'due_at_to' => '2026-09-30T23:59:59Z',
            ]);

            $params = $this->httpClient->getLastRequest()['params'];
            $this->assertSame('2026-09-01T00:00:00Z', $params['due_at_from']);
            $this->assertSame('2026-09-30T23:59:59Z', $params['due_at_to']);
        }
    }

    public function testBankTransactionsAcceptTheMergedCompletedState(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->bankTransactions->list(['state' => 'completed']);
        $this->assertSame('completed', $this->httpClient->getLastRequest()['params']['state']);
    }

    public function testProspectStagesResolveUnderTheRenamedPathAndAcceptThePipelineFilter(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [['id' => 'org_pros_stg_1', 'object' => 'prospect_stage', 'stage_type' => 'open']],
        ]);

        $stages = $this->client->prospectStages->list(['organization_prospect_pipeline_id' => 'org_pros_pipe_1']);

        $request = $this->httpClient->getLastRequest();
        $this->assertStringContainsString('/organizations/org_default/prospect-stages', $request['url']);
        $this->assertStringNotContainsString('prospect-statuses', $request['url']);
        $this->assertSame('org_pros_pipe_1', $request['params']['organization_prospect_pipeline_id']);
        $this->assertInstanceOf(ProspectStage::class, $stages->data[0]);
        $this->assertSame('open', $stages->data[0]->stage_type);
    }

    public function testProspectPipelineBoardAndRestoreKeepTheirPaths(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_pros_pipe_1', 'object' => 'prospect_pipeline']]);
        $pipeline = $this->client->prospectPipelines->retrieve('org_pros_pipe_1', ['include' => 'stages']);

        $this->assertInstanceOf(ProspectPipeline::class, $pipeline);
        $this->assertStringContainsString('/prospect-pipelines/org_pros_pipe_1', $this->httpClient->getLastRequest()['url']);

        $this->httpClient->addResponse(200, ['data' => ['columns' => []]]);
        $this->client->prospectPipelines->board('org_pros_pipe_1', ['stage_types' => 'open']);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('GET', $request['method']);
        $this->assertStringContainsString('/prospect-pipelines/org_pros_pipe_1/board', $request['url']);

        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_pros_pipe_1', 'object' => 'prospect_pipeline']]);
        $this->client->prospectPipelines->restore('org_pros_pipe_1');
        $this->assertStringContainsString('/prospect-pipelines/restore/org_pros_pipe_1', $this->httpClient->getLastRequest()['url']);
    }

    public function testProspectDuplicatesAndMergeResolveUnderTheProspectPath(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [[
                'object' => 'prospect_duplicate',
                'organization_prospect_id' => 'org_pros_2',
                'confidence' => 'high',
                'signals' => ['email'],
                'blockers' => [],
                'organization_proposals_count' => 2,
                'organization_prospect_activities_count' => 7,
            ]],
        ]);

        $duplicates = $this->client->prospects->duplicates('org_pros_1');

        $this->assertStringContainsString('/prospects/org_pros_1/duplicates', $this->httpClient->getLastRequest()['url']);
        $this->assertInstanceOf(ProspectDuplicate::class, $duplicates->data[0]);
        $this->assertSame('high', $duplicates->data[0]->confidence);
        $this->assertSame(2, $duplicates->data[0]->organization_proposals_count);

        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_pros_1', 'object' => 'prospect']]);
        $merged = $this->client->prospects->merge('org_pros_1', [
            'merge_organization_prospect_ids' => ['org_pros_2'],
            'field_choices' => ['email' => 'org_pros_2'],
        ]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringContainsString('/prospects/org_pros_1/merge', $request['url']);
        $this->assertSame(['org_pros_2'], $request['params']['merge_organization_prospect_ids']);
        $this->assertInstanceOf(Prospect::class, $merged);
    }

    public function testProspectLinksAreSentAsGivenAndReadAsRows(): void
    {
        $links = [
            ['kind' => 'website', 'url' => 'https://bigcorp.example'],
            ['kind' => 'linkedin', 'url' => 'https://linkedin.com/in/sarah', 'label' => 'Sarah'],
        ];
        $this->httpClient->addResponse(201, ['data' => [
            'id' => 'org_pros_1',
            'links' => [
                ['kind' => 'website', 'url' => 'https://bigcorp.example', 'label' => null],
                ['kind' => 'linkedin', 'url' => 'https://linkedin.com/in/sarah', 'label' => 'Sarah'],
            ],
        ]]);

        $prospect = $this->client->prospects->create(['first_name' => 'Sarah', 'links' => $links]);

        $this->assertSame($links, $this->httpClient->getLastRequest()['params']['links']);
        $this->assertSame(['website', 'linkedin'], array_map(static fn ($link) => $link['kind'], $prospect->links));
        $this->assertNull($prospect->links[0]['label']);
        $this->assertSame('Sarah', $prospect->links[1]->label);
    }

    public function testEmploymentAndWorkingTimeTermResolveTypedUnderTheirPaths(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [['id' => 'org_empl_1', 'object' => 'employment', 'lifecycle' => 'active']],
        ]);

        $employments = $this->client->employments->list(['type' => 'permanent', 'active_on' => '2026-09-01']);

        $request = $this->httpClient->getLastRequest();
        $this->assertStringContainsString('/organizations/org_default/employments', $request['url']);
        $this->assertSame('permanent', $request['params']['type']);
        $this->assertInstanceOf(Employment::class, $employments->data[0]);
        $this->assertSame('active', $employments->data[0]->lifecycle);

        $this->httpClient->addResponse(200, [
            'data' => ['id' => 'org_wtt_1', 'object' => 'working_time_term', 'unit' => 'hours'],
        ]);
        $term = $this->client->workingTimeTerms->retrieve('org_wtt_1');

        $this->assertStringContainsString('/working-time-terms/org_wtt_1', $this->httpClient->getLastRequest()['url']);
        $this->assertInstanceOf(WorkingTimeTerm::class, $term);
    }

    public function testWorkingTimeDayMonthLaneUsesTheRightVerbs(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [['id' => 'org_wtd_1', 'object' => 'working_time_day', 'disposition' => 'worked']],
        ]);
        $days = $this->client->workingTimeDays->list([
            'organization_employment_id' => 'org_empl_1',
            'date_from' => '2026-09-01',
            'date_to' => '2026-09-30',
        ]);
        $this->assertInstanceOf(WorkingTimeDay::class, $days->data[0]);

        $this->httpClient->addResponse(200, ['data' => ['month' => '2026-09']]);
        $this->client->workingTimeDays->month(['organization_employment_id' => 'org_empl_1', 'month' => '2026-09']);
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('GET', $request['method']);
        $this->assertStringContainsString('/working-time-days/month', $request['url']);

        $this->httpClient->addResponse(200, ['data' => ['month' => '2026-09']]);
        $this->client->workingTimeDays->upsertMonth([
            'organization_employment_id' => 'org_empl_1',
            'month' => '2026-09',
            'days' => [],
        ]);
        $this->assertSame('PUT', $this->httpClient->getLastRequest()['method']);

        $this->httpClient->addResponse(200, ['data' => ['month' => '2026-09']]);
        $this->client->workingTimeDays->attestMonth([
            'organization_employment_id' => 'org_empl_1',
            'month' => '2026-09',
            'attestation_method' => 'supervisor_approved',
        ]);
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringContainsString('/working-time-days/month/attest', $request['url']);
    }

    public function testPayslipGainsLinesDownloadAndTheLineCodeHelper(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [
                'id' => 'org_pay_1',
                'object' => 'payslip',
                'gross_total' => 5000.0,
                'employer_contributions_total' => 112.5,
                'lines' => [['id' => 'org_pay_line_1', 'object' => 'payslip_line', 'code' => 'base_salary']],
            ],
        ]);

        $payslip = $this->client->payslips->retrieve('org_pay_1', ['include' => 'lines,organization_employment']);
        $this->assertSame(5000.0, $payslip->gross_total);
        $this->assertInstanceOf(PayslipLine::class, $payslip->lines[0]);

        $this->httpClient->addResponse(200, ['data' => []]);
        $this->client->payslips->download('org_pay_1');
        $this->assertStringContainsString('/payslips/org_pay_1/download', $this->httpClient->getLastRequest()['url']);

        $this->httpClient->addResponse(200, ['data' => ['codes' => ['base_salary']]]);
        $this->client->misc->determinePayslipLineCodes([
            'organization_employment_id' => 'org_empl_1',
            'period_end' => '2026-09-30',
        ]);
        $this->assertStringContainsString('/misc/determine-payslip-line-codes', $this->httpClient->getLastRequest()['url']);
    }

    public function testProposalReopenAndNetworkExchangeTaxMappingResolve(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_prop_1', 'object' => 'proposal']]);
        $this->client->proposals->reopen('org_prop_1');

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringContainsString('/proposals/org_prop_1/reopen', $request['url']);

        $this->httpClient->addResponse(200, ['data' => ['outcome' => 'candidates']]);
        $mapping = $this->client->invoiceNetworkExchanges->taxMapping('org_inv_pnx_1');

        $this->assertStringContainsString('/invoices/network-exchanges/org_inv_pnx_1/tax-mapping', $this->httpClient->getLastRequest()['url']);
        $this->assertSame('candidates', $mapping->outcome);
    }

    public function testPortalGainsPayslipDownloadAndTheWorkingTimeMonthLane(): void
    {
        $portal = new \Enlivy\EnlivyPortalClient([
            'portal_token' => 'portal_tok',
            'organization_id' => 'org_default',
            'http_client' => $this->httpClient,
        ]);

        $this->httpClient->addResponse(200, ['data' => []]);
        $portal->payslips->download('org_pay_1');
        $this->assertStringContainsString('payslips/org_pay_1/download', $this->httpClient->getLastRequest()['url']);

        $this->httpClient->addResponse(200, ['data' => ['month' => '2026-09']]);
        $portal->workingTimeDays->month(['organization_employment_id' => 'org_empl_1', 'month' => '2026-09']);
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('GET', $request['method']);
        $this->assertStringContainsString('working-time-days/month', $request['url']);

        $this->httpClient->addResponse(200, ['data' => ['month' => '2026-09']]);
        $portal->workingTimeDays->attestMonth([
            'organization_employment_id' => 'org_empl_1',
            'month' => '2026-09',
        ]);
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringContainsString('working-time-days/month/attest', $request['url']);
    }
}

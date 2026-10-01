<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\EnlivyClient;
use Enlivy\Enums\CheckoutSession\Modes;
use Enlivy\Enums\CheckoutSession\PaymentStatuses;
use Enlivy\Enums\CheckoutSession\Statuses;
use Enlivy\Enums\EventDelivery\TriggerEvent;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Organization\BillingSchedule;
use Enlivy\Organization\CheckoutSession;
use Enlivy\Service\Organization\CheckoutSessionService;
use Enlivy\Tests\Mock\MockHttpClient;
use Enlivy\Util\ObjectTypes;
use Enlivy\Util\RequestOptions;
use PHPUnit\Framework\TestCase;

final class CheckoutSessionServiceTest extends TestCase
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

    public function testTheServiceIsWiredToItsResource(): void
    {
        $this->assertInstanceOf(CheckoutSessionService::class, $this->client->checkoutSessions);
        $this->assertSame(CheckoutSession::class, ObjectTypes::getClass('checkout_session'));
    }

    public function testCreateSendsTheOrderAndReturnsTheClientTokenOnce(): void
    {
        $this->httpClient->addResponse(201, [
            'data' => [
                'id' => 'org_chk_sess_1',
                'status' => 'open',
                'payment_status' => 'unpaid',
                'mode' => 'payment',
                'priced_total' => '121.000000',
            ],
            'meta' => ['client_token' => 'tok_once'],
        ]);

        $session = $this->client->checkoutSessions->create([
            'organization_billing_package_id' => 'org_bp_1',
            'organization_receiver_user_id' => 'org_user_1',
            'client_reference_id' => 'order-42',
            'metadata' => ['plan' => 'pro'],
            'include' => ['billing_package', 'receiver_user'],
        ], new RequestOptions(idempotencyKey: 'order-42-attempt'));

        $this->assertInstanceOf(CheckoutSession::class, $session);
        $this->assertSame('org_chk_sess_1', $session->id);
        $this->assertSame(Statuses::OPEN, Statuses::from($session->status));
        $this->assertSame(PaymentStatuses::UNPAID, PaymentStatuses::from($session->payment_status));
        $this->assertSame(Modes::PAYMENT, Modes::from($session->mode));
        $this->assertSame('tok_once', $session->lastResponse()?->json['meta']['client_token']);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringEndsWith('/organizations/org_default/checkout-sessions', $request['url']);
        $this->assertSame('order-42-attempt', $request['headers']['Idempotency-Key']);
        $this->assertSame('billing_package,receiver_user', $request['params']['include']);
        $this->assertSame('org_bp_1', $request['params']['organization_billing_package_id']);
        $this->assertSame(['plan' => 'pro'], $request['params']['metadata']);
    }

    public function testCreateWithoutAKeySendsNoIdempotencyHeader(): void
    {
        $this->httpClient->addResponse(201, ['data' => ['id' => 'org_chk_sess_1']]);

        $this->client->checkoutSessions->create([
            'organization_billing_package_id' => 'org_bp_1',
            'organization_receiver_user_id' => 'org_user_1',
        ]);

        $this->assertArrayNotHasKey('Idempotency-Key', $this->httpClient->getLastRequest()['headers']);
    }

    /**
     * A server error on a keyed create is replayed, because the API answers a retried key with the
     * session it already opened.
     */
    public function testAKeyedCreateIsRetriedWithTheSameKey(): void
    {
        $client = new EnlivyClient([
            'api_key' => '1|test_token',
            'organization_id' => 'org_default',
            'http_client' => $this->httpClient,
            'max_retries' => 1,
        ]);
        $this->httpClient->addResponse(503, ['message' => 'unavailable']);
        $this->httpClient->addResponse(201, ['data' => ['id' => 'org_chk_sess_1']]);

        $session = $client->checkoutSessions->create(
            ['organization_billing_package_id' => 'org_bp_1', 'organization_receiver_user_id' => 'org_user_1'],
            new RequestOptions(idempotencyKey: 'order-42-attempt'),
        );

        $this->assertSame('org_chk_sess_1', $session->id);
        $this->assertCount(2, $this->httpClient->getRequests());
        foreach ($this->httpClient->getRequests() as $request) {
            $this->assertSame('order-42-attempt', $request['headers']['Idempotency-Key']);
        }
    }

    public function testListTakesItsFiltersAndIncludes(): void
    {
        $this->httpClient->addResponse(200, ['data' => [['id' => 'org_chk_sess_1', 'status' => 'complete']]]);

        $sessions = $this->client->checkoutSessions->list([
            'status' => 'complete',
            'client_reference_id' => 'order-42',
            'organization_receiver_user_id' => 'org_user_1',
            'ids' => ['org_chk_sess_1'],
            'limit' => 10,
            'include' => 'billing_schedule,invoice',
        ]);

        $this->assertInstanceOf(CheckoutSession::class, $sessions->getData()[0]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('GET', $request['method']);
        $this->assertStringEndsWith('/organizations/org_default/checkout-sessions', $request['url']);
        $this->assertSame('complete', $request['params']['status']);
        $this->assertSame('order-42', $request['params']['client_reference_id']);
        $this->assertSame('billing_schedule,invoice', $request['params']['include']);
    }

    public function testRetrieveHydratesTheScheduleItSold(): void
    {
        $this->httpClient->addResponse(200, ['data' => [
            'id' => 'org_chk_sess_1',
            'status' => 'complete',
            'organization_billing_schedule_id' => 'org_bill_sch_1',
            'billing_schedule' => ['data' => ['id' => 'org_bill_sch_1', 'object' => 'billing_schedule']],
        ]]);

        $session = $this->client->checkoutSessions->retrieve('org_chk_sess_1', [
            'include' => ['billing_schedule', 'proforma_invoice'],
            'organization_id' => 'org_other',
        ]);

        $this->assertInstanceOf(CheckoutSession::class, $session);
        $this->assertInstanceOf(BillingSchedule::class, $session->billing_schedule->data);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('GET', $request['method']);
        $this->assertStringEndsWith('/organizations/org_other/checkout-sessions/org_chk_sess_1', $request['url']);
        $this->assertSame('billing_schedule,proforma_invoice', $request['params']['include']);
        $this->assertArrayNotHasKey('organization_id', $request['params']);
    }

    public function testUpdatePutsTheCorrectionsToTheSession(): void
    {
        $this->httpClient->addResponse(200, ['data' => [
            'id' => 'org_chk_sess_1',
            'client_reference_id' => 'order-42-b',
            'expires_at' => '2026-10-20T12:00:00+00:00',
        ]]);

        $session = $this->client->checkoutSessions->update('org_chk_sess_1', [
            'client_reference_id' => 'order-42-b',
            'metadata' => ['plan' => 'pro'],
            'expires_at' => '2026-10-20T12:00:00Z',
            'include' => ['receiver_user'],
        ]);

        $this->assertInstanceOf(CheckoutSession::class, $session);
        $this->assertSame('order-42-b', $session->client_reference_id);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('PUT', $request['method']);
        $this->assertStringEndsWith('/organizations/org_default/checkout-sessions/org_chk_sess_1', $request['url']);
        $this->assertSame(['plan' => 'pro'], $request['params']['metadata']);
        $this->assertSame('2026-10-20T12:00:00Z', $request['params']['expires_at']);
        $this->assertSame('receiver_user', $request['params']['include']);
    }

    public function testExpirePostsToItsOwnPath(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_chk_sess_1', 'status' => 'expired']]);

        $session = $this->client->checkoutSessions->expire('org_chk_sess_1', ['include' => 'proforma_invoice']);

        $this->assertInstanceOf(CheckoutSession::class, $session);
        $this->assertSame('expired', $session->status);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringEndsWith('/organizations/org_default/checkout-sessions/org_chk_sess_1/expire', $request['url']);
        $this->assertSame('proforma_invoice', $request['params']['include']);
    }

    public function testIncludesAndFiltersOutsideTheContractAreRefused(): void
    {
        foreach (['list', 'retrieve', 'update', 'expire', 'create'] as $method) {
            try {
                match ($method) {
                    'list' => $this->client->checkoutSessions->list(['include' => 'proposal']),
                    'create' => $this->client->checkoutSessions->create(['include' => 'proposal']),
                    default => $this->client->checkoutSessions->{$method}('org_chk_sess_1', ['include' => 'proposal']),
                };
                $this->fail("{$method} should refuse an include the API does not offer.");
            } catch (InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }

        $this->assertSame([], $this->httpClient->getRequests());

        $this->expectException(InvalidArgumentException::class);
        $this->client->checkoutSessions->list(['payment_status' => 'paid']);
    }

    public function testTheSessionEventsAreSubscribable(): void
    {
        $this->assertSame('checkout_session.completed', TriggerEvent::CHECKOUT_SESSION_COMPLETED->value);
        $this->assertSame('checkout_session.expired', TriggerEvent::CHECKOUT_SESSION_EXPIRED->value);
    }
}

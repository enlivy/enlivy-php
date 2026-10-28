<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\AppRegistrationRequest;
use Enlivy\Collection;
use Enlivy\EnlivyClient;
use Enlivy\EnlivyPortalClient;
use Enlivy\Organization\AppInstallation;
use Enlivy\Organization\AppInstallRequest;
use Enlivy\Organization\EventDestination;
use Enlivy\Tests\Mock\MockHttpClient;
use Enlivy\UserApp;
use Enlivy\UserAppRevision;
use PHPUnit\Framework\TestCase;

final class AppsTest extends TestCase
{
    private MockHttpClient $http;
    private EnlivyClient $client;
    private EnlivyPortalClient $portal;

    protected function setUp(): void
    {
        $this->http = new MockHttpClient();
        $this->client = new EnlivyClient([
            'api_key' => '1|test_token',
            'organization_id' => 'org_default',
            'http_client' => $this->http,
        ]);
        $this->portal = new EnlivyPortalClient([
            'portal_token' => 'portal_tok',
            'organization_id' => 'org_default',
            'http_client' => $this->http,
        ]);
    }

    public function testADeveloperInstallsTheirOwnAppWithItsApprovedPermissions(): void
    {
        $this->http->addResponse(201, ['data' => ['id' => 'org_app_inst_1', 'generation' => 1, 'status' => 'active', 'connect_url' => 'https://links.example.com/connect']]);
        $installation = $this->client->appInstallations->create([
            'user_app_id' => 'app_1',
            'user_app_revision_id' => 'app_rev_1',
            'permissions' => ['oauth_scopes' => ['accounting:read'], 'connector_capabilities' => ['links.read'], 'context_fields' => []],
            'capability_holders' => 'roles',
            'capability_holder_role_ids' => ['org_role_1'],
        ]);

        $this->assertInstanceOf(AppInstallation::class, $installation);
        $this->assertSame('active', $installation->status);
        $request = $this->http->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringEndsWith('/organizations/org_default/app-installations', $request['url']);
        $this->assertSame('roles', $request['params']['capability_holders']);
        $this->assertSame(['links.read'], $request['params']['permissions']['connector_capabilities']);
    }

    public function testADeveloperAsksAnOrganizationToInstallTheirApp(): void
    {
        $this->http->addResponse(201, ['data' => ['id' => 'org_app_req_1', 'status' => 'pending', 'manifest_version' => '1.2.0']]);
        $request = $this->client->appInstallRequests->create(['user_app_id' => 'app_1']);

        $this->assertInstanceOf(AppInstallRequest::class, $request);
        $this->assertSame('pending', $request->status);
        $sent = $this->http->getLastRequest();
        $this->assertSame('POST', $sent['method']);
        $this->assertStringEndsWith('/organizations/org_default/app-install-requests', $sent['url']);
        $this->assertSame('app_1', $sent['params']['user_app_id']);
    }

    public function testTheDashboardOnlyAppAdministrationIsNotOnTheClient(): void
    {
        foreach (['apps', 'appWidgets', 'appPages', 'appConnections', 'pageLayouts', 'navigationLayout'] as $accessor) {
            try {
                $this->client->{$accessor};
                $this->fail("{$accessor} should not resolve");
            } catch (\InvalidArgumentException $e) {
                $this->assertStringContainsString($accessor, $e->getMessage());
            }
        }

        $this->assertFalse(method_exists($this->client->appInstallations, 'list'));
        $this->assertFalse(method_exists($this->client->appInstallations, 'retrieve'));
        $this->assertFalse(method_exists($this->client->appInstallRequests, 'approve'));
        $this->assertFalse(method_exists($this->client->appRegistrationRequests, 'approve'));
        $this->assertFalse(method_exists($this->client->userRoles, 'appCapabilities'));
    }

    public function testADeveloperRegistersRevisesAndPublishesAnApp(): void
    {
        $this->http->addResponse(200, ['data' => ['valid' => false, 'issues' => [['code' => 'missing_key', 'message' => 'The manifest needs a key.']]]]);
        $check = $this->client->userApps->validate('usr_1', ['manifest' => ['format' => 'enlivy.app-manifest.v1'], 'documents' => new \stdClass()]);
        $this->assertFalse($check->valid);
        $request = $this->http->getLastRequest();
        $this->assertStringEndsWith('/users/usr_1/apps/validate', $request['url']);
        $this->assertSame('{"manifest":{"format":"enlivy.app-manifest.v1"},"documents":{}}', json_encode($request['params']));

        $this->http->addResponse(200, ['data' => ['id' => 'app_1', 'key' => 'links', 'revisions' => [['revision' => 1]]]]);
        $app = $this->client->userApps->register('usr_1', ['manifest' => ['key' => 'links', 'version' => '1.0.0'], 'documents' => new \stdClass()]);
        $this->assertInstanceOf(UserApp::class, $app);
        $this->assertStringEndsWith('/users/usr_1/apps', $this->http->getLastRequest()['url']);

        $this->http->addResponse(200, ['data' => ['id' => 'app_rev_2', 'revision' => 2, 'version' => '1.1.0']]);
        $revision = $this->client->userApps->submitRevision('usr_1', 'app_1', ['manifest' => ['key' => 'links', 'version' => '1.1.0'], 'documents' => new \stdClass()]);
        $this->assertInstanceOf(UserAppRevision::class, $revision);

        $this->http->addResponse(200, ['data' => ['id' => 'app_rev_2', 'published_at' => '2026-10-28T10:00:00Z']]);
        $published = $this->client->userApps->publishRevision('usr_1', 'app_1', 'app_rev_2');
        $this->assertInstanceOf(UserAppRevision::class, $published);
        $this->assertStringEndsWith('/users/usr_1/apps/app_1/revisions/app_rev_2/publish', $this->http->getLastRequest()['url']);

        $this->http->addResponse(200, ['data' => [['id' => 'app_1']]]);
        $this->assertInstanceOf(UserApp::class, $this->client->userApps->list('usr_1')->first());

        $this->http->addResponse(200, ['data' => ['id' => 'app_1']]);
        $this->assertInstanceOf(UserApp::class, $this->client->userApps->retrieve('usr_1', 'app_1'));

        $this->http->addResponse(200, ['data' => [['id' => 'org_app_req_1', 'status' => 'pending']]]);
        $this->assertInstanceOf(AppInstallRequest::class, $this->client->userApps->installRequests('usr_1', 'app_1')->first());
        $this->assertStringEndsWith('/users/usr_1/apps/app_1/install-requests', $this->http->getLastRequest()['url']);
    }

    public function testSecretsComeBackSealedToTheRecipientKey(): void
    {
        $key = str_repeat('k', 43);
        $sealed = ['format' => 'enlivy.sealed/v1', 'kind' => 'oauth_client_secret', 'for' => 'oac_1', 'recipient' => $key, 'epk' => 'e', 'nonce' => 'n', 'ciphertext' => 'c'];

        $this->http->addResponse(201, ['data' => ['app' => ['id' => 'app_1'], 'oauth_client' => ['id' => 'oac_1', 'plain_secret' => null, 'sealed_secret' => $sealed]]]);
        $made = $this->client->userApps->createOAuthClient('usr_1', 'app_1', ['redirect_uris' => ['https://links.example.com/oauth/callback'], 'recipient_key' => $key]);
        $this->assertSame('enlivy.sealed/v1', $made->oauth_client->sealed_secret->format);
        $this->assertSame($key, $this->http->getLastRequest()['params']['recipient_key']);

        $this->http->addResponse(200, ['data' => ['id' => 'oac_1', 'plain_secret' => null, 'sealed_secret' => $sealed]]);
        $rotated = $this->client->userApps->rotateOAuthClientSecret('usr_1', 'app_1', 'oac_1', ['recipient_key' => $key]);
        $this->assertNull($rotated->plain_secret);
        $this->assertStringEndsWith('/users/usr_1/apps/app_1/oauth-clients/oac_1/rotate-secret', $this->http->getLastRequest()['url']);

        $this->http->addResponse(200, ['data' => ['id' => 'app_1']]);
        $this->assertInstanceOf(UserApp::class, $this->client->userApps->attachOAuthClient('usr_1', 'app_1', 'oac_2'));
        $this->assertSame('PUT', $this->http->getLastRequest()['method']);

        $this->http->addResponse(200, ['data' => ['id' => 'app_1']]);
        $this->assertInstanceOf(UserApp::class, $this->client->userApps->detachOAuthClient('usr_1', 'app_1', 'oac_2'));
        $this->assertSame('DELETE', $this->http->getLastRequest()['method']);

        $this->http->addResponse(200, ['data' => ['rotated_at' => null, 'previous_secret_valid_until' => null]]);
        $state = $this->client->userApps->webhookSecret('usr_1', 'app_1');
        $this->assertNull($state->rotated_at);

        $this->http->addResponse(200, ['data' => ['secret' => null, 'sealed_secret' => $sealed + ['kind' => 'app_webhook_secret'], 'rotated_at' => '2026-10-28T10:00:00Z']]);
        $secret = $this->client->userApps->rotateWebhookSecret('usr_1', 'app_1', ['recipient_key' => $key]);
        $this->assertNull($secret->secret);
        $this->assertStringEndsWith('/users/usr_1/apps/app_1/webhook-secret/rotate', $this->http->getLastRequest()['url']);
    }

    public function testAnAgentDraftsAndClaimsARegistrationRequest(): void
    {
        $draft = [
            'manifest' => ['key' => 'links', 'version' => '1.0.0'],
            'documents' => new \stdClass(),
            'organization_id' => 'org_default',
            'redirect_uris' => ['https://links.example.com/oauth/callback'],
            'permissions' => ['oauth_scopes' => ['accounting:read'], 'connector_capabilities' => [], 'context_fields' => []],
            'recipient_key' => str_repeat('k', 43),
        ];

        $this->http->addResponse(200, ['data' => ['valid' => true]]);
        $this->assertTrue($this->client->appRegistrationRequests->validate('usr_1', $draft)->valid);
        $this->assertStringEndsWith('/users/usr_1/app-registration-requests/validate', $this->http->getLastRequest()['url']);

        $this->http->addResponse(201, ['data' => ['id' => 'app_reg_1', 'status' => 'pending', 'approval_url' => 'https://app.enlivy.com/apps/requests/app_reg_1']]);
        $request = $this->client->appRegistrationRequests->create('usr_1', $draft);
        $this->assertInstanceOf(AppRegistrationRequest::class, $request);
        $this->assertSame('pending', $request->status);

        $this->http->addResponse(200, ['data' => [['id' => 'app_reg_1']]]);
        $this->assertInstanceOf(AppRegistrationRequest::class, $this->client->appRegistrationRequests->list('usr_1')->first());

        $this->http->addResponse(200, ['data' => ['id' => 'app_reg_1', 'status' => 'approved']]);
        $this->assertSame('approved', $this->client->appRegistrationRequests->retrieve('usr_1', 'app_reg_1')->status);

        $this->http->addResponse(200, ['data' => ['id' => 'app_reg_1', 'status' => 'claimed', 'user_app_id' => 'app_1', 'sealed_secrets' => [['kind' => 'oauth_client_secret', 'for' => 'oac_1']]]]);
        $claimed = $this->client->appRegistrationRequests->claim('usr_1', 'app_reg_1');
        $this->assertSame('oac_1', $claimed->sealed_secrets[0]->for);
        $this->assertStringEndsWith('/app-registration-requests/app_reg_1/claim', $this->http->getLastRequest()['url']);

        $this->http->addResponse(200, ['data' => ['id' => 'app_reg_1', 'status' => 'cancelled']]);
        $this->assertSame('cancelled', $this->client->appRegistrationRequests->cancel('usr_1', 'app_reg_1')->status);
    }

    public function testTheAppBuilderDiscoveryAndTheProviderKeysArePublic(): void
    {
        $this->http->addResponse(200, ['meta' => ['version' => 'enlivy.app-discovery.v1'], 'sections' => [['key' => 'overview']]]);
        $index = $this->client->discovery->apps();
        $this->assertSame('enlivy.app-discovery.v1', $index->meta->version);
        $this->assertStringEndsWith('/discovery/apps', $this->http->getLastRequest()['url']);

        $this->http->addResponse(200, ['section' => 'development', 'paths' => []]);
        $this->assertSame('development', $this->client->discovery->appsSection('development')->section);
        $this->assertStringEndsWith('/discovery/apps/development', $this->http->getLastRequest()['url']);

        $this->http->addResponse(200, ['title' => 'enlivy.sealed/v1', 'type' => 'object']);
        $this->assertSame('object', $this->client->discovery->appsSchema('sealed_secret')->type);
        $this->assertStringEndsWith('/discovery/apps/schemas/sealed_secret', $this->http->getLastRequest()['url']);

        $this->http->addResponse(200, ['keys' => [['kty' => 'EC', 'kid' => 'k1']]]);
        $keys = $this->client->frontend->appProviderKeys();
        $this->assertSame('k1', $keys->keys[0]->kid);
        $this->assertStringEndsWith('/misc/app-provider-keys', $this->http->getLastRequest()['url']);
    }

    public function testADeviceSignInStartsOnTheOAuthService(): void
    {
        $this->http->addResponse(200, ['device_code' => 'dc_1', 'user_code' => 'ABCD-EFGH', 'verification_uri' => 'https://identity.enlivy.com/device', 'expires_in' => 600, 'interval' => 5]);
        $started = $this->client->oauthTokens->deviceCode(['client_id' => 'oac_cli', 'scope' => 'apps:develop']);
        $this->assertSame('ABCD-EFGH', $started->user_code);
        $request = $this->http->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringEndsWith('/oauth/device/code', $request['url']);
        $this->assertSame('oac_cli', $request['params']['client_id']);
    }

    public function testAReportDownloadsOnBothLanesAndAPortalProjectNamesItsReportRecipients(): void
    {
        $this->http->addResponse(200, ['pdf' => true]);
        $pdf = $this->client->reports->download('org_rep_1', ['layout' => 'compact', 'locale' => 'ro']);
        $this->assertIsString($pdf);
        $request = $this->http->getLastRequest();
        $this->assertStringContainsString('/organizations/org_default/reports/org_rep_1/download', $request['url']);
        $this->assertSame('compact', $request['params']['layout']);

        $this->http->addResponse(200, ['pdf' => true]);
        $this->portal->reports->download('org_rep_1');
        $this->assertStringContainsString('/user-client-portal/organizations/org_default/reports/org_rep_1/download', $this->http->getLastRequest()['url']);

        $this->http->addResponse(200, ['data' => [['id' => 'org_proj_mem_1', 'organization_user_id' => 'org_user_1']]]);
        $recipients = $this->portal->projectMembers->reportRecipients('org_proj_1');
        $this->assertInstanceOf(Collection::class, $recipients);
        $this->assertStringContainsString('/user-client-portal/organizations/org_default/reports/recipients/org_proj_1', $this->http->getLastRequest()['url']);
    }

    public function testAnEventDestinationRotatesItsSecretAndAnOrganizationAnswersEmailSending(): void
    {
        $this->http->addResponse(200, ['data' => ['id' => 'org_evt_dst_1', 'signing_secret' => null, 'sealed_signing_secret' => ['format' => 'enlivy.sealed/v1'], 'signing_secret_rotated_at' => '2026-10-28T10:00:00Z']]);
        $destination = $this->client->eventDestinations->rotateSecret('org_evt_dst_1', ['recipient_key' => str_repeat('k', 43)]);
        $this->assertInstanceOf(EventDestination::class, $destination);
        $this->assertNull($destination->signing_secret);
        $this->assertSame('enlivy.sealed/v1', $destination->sealed_signing_secret->format);
        $this->assertStringEndsWith('/event-destinations/org_evt_dst_1/rotate-secret', $this->http->getLastRequest()['url']);

        $this->http->addResponse(200, ['data' => ['id' => 'org_default', 'email_sending' => ['connected' => false, 'provider' => null]]]);
        $organization = $this->client->organizations->retrieve('org_default', ['include' => 'email_sending']);
        $this->assertFalse($organization->email_sending->connected);
        $this->assertSame('email_sending', $this->http->getLastRequest()['params']['include']);
    }
}

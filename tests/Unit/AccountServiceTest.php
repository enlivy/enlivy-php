<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\AiAgent;
use Enlivy\EnlivyClient;
use Enlivy\EnlivyObject;
use Enlivy\InvitationCode;
use Enlivy\OAuthToken;
use Enlivy\Organization\UserAddress;
use Enlivy\Service\AuthenticationService;
use Enlivy\Tests\Mock\MockHttpClient;
use Enlivy\User;
use PHPUnit\Framework\TestCase;

final class AccountServiceTest extends TestCase
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

    public function testUserRetrieveAndUpdateReturnUser(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'user_1', 'object' => 'user', 'first_name' => null]]);
        $user = $this->client->users->retrieve('user_1');
        $this->assertInstanceOf(User::class, $user);
        $this->assertNull($user->first_name);

        $this->httpClient->addResponse(200, ['data' => ['id' => 'user_1', 'object' => 'user', 'timezone' => 'Europe/Bucharest']]);
        $user = $this->client->users->update('user_1', ['timezone' => 'Europe/Bucharest']);
        $this->assertInstanceOf(User::class, $user);
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('PUT', $request['method']);
        $this->assertStringContainsString('/users/user_1', $request['url']);
    }

    public function testInvitationCodeRetrieveTypedAndDeleteAnswersStatus(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'inv_code_1', 'code' => 'ABC']]);
        $this->assertInstanceOf(InvitationCode::class, $this->client->invitationCodes->retrieve('ABC'));

        $this->httpClient->addResponse(200, ['data' => ['status' => 'ok']]);
        $deleted = $this->client->invitationCodes->delete('inv_code_1');
        $this->assertNotInstanceOf(InvitationCode::class, $deleted);
        $this->assertSame('ok', $deleted->status);

        $this->httpClient->addResponse(200, ['data' => ['status' => 'ok']]);
        $restored = $this->client->invitationCodes->restore('inv_code_1');
        $this->assertSame('ok', $restored->status);
        $this->assertStringContainsString('/invitation-codes/restore/inv_code_1', $this->httpClient->getLastRequest()['url']);
    }

    public function testAiAgentRetrieveTypedAndRunUntyped(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'ai_agent_1']]);
        $this->assertInstanceOf(AiAgent::class, $this->client->aiAgents->retrieve('ai_agent_1'));

        $this->httpClient->addResponse(200, ['data' => ['output' => ['category' => 'services']]]);
        $result = $this->client->aiAgents->run('ai_agent_1', ['input' => []]);
        $this->assertNotInstanceOf(AiAgent::class, $result);
        $this->assertSame('services', $result->output['category']);
    }

    public function testUserAddressRetrieveTypedAndDeleteAnswersStatus(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_user_addr_1']]);
        $address = $this->client->userAddresses->retrieve('org_user_1', 'org_user_addr_1');
        $this->assertInstanceOf(UserAddress::class, $address);

        $this->httpClient->addResponse(200, ['data' => ['status' => 'ok']]);
        $deleted = $this->client->userAddresses->delete('org_user_1', 'org_user_addr_1');
        $this->assertNotInstanceOf(UserAddress::class, $deleted);
        $this->assertSame('ok', $deleted->status);
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('DELETE', $request['method']);
        $this->assertStringContainsString('/organizations/org_default/users/org_user_1/addresses/org_user_addr_1', $request['url']);

        $this->httpClient->addResponse(200, ['data' => ['status' => 'ok']]);
        $this->assertSame('ok', $this->client->userAddresses->restore('org_user_1', 'org_user_addr_1')->status);
    }

    public function testOAuthTokenExchangeReturnsTypedToken(): void
    {
        $this->httpClient->addResponse(200, [
            'access_token' => 'eat_1',
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'refresh_token' => 'ert_1',
            'scope' => 'accounting:read',
        ]);

        $token = $this->client->oauthTokens->create(['grant_type' => 'refresh_token', 'refresh_token' => 'ert_0']);

        $this->assertInstanceOf(OAuthToken::class, $token);
        $this->assertSame('eat_1', $token->access_token);
        $this->assertSame('ert_1', $token->refresh_token);

        $this->httpClient->addResponse(200, ['id' => 'user_1', 'client_id' => 'oac_1', 'scopes' => ['accounting:read']]);
        $me = $this->client->oauthTokens->me();
        $this->assertInstanceOf(EnlivyObject::class, $me);
        $this->assertSame('oac_1', $me->client_id);
    }

    public function testRemovedAccountSurfaceIsGone(): void
    {
        foreach (['userTokens', 'oauthClients', 'oauthAuthorizations'] as $name) {
            try {
                $this->client->{$name};
                $this->fail("{$name} should not resolve");
            } catch (\InvalidArgumentException $e) {
                $this->assertSame("Unknown service: {$name}", $e->getMessage());
            }
        }

        foreach (['login', 'register', 'forgotPassword', 'resetPassword', 'listTokens', 'deleteToken'] as $method) {
            $this->assertFalse(method_exists(AuthenticationService::class, $method), $method);
        }
        $this->assertTrue(method_exists(AuthenticationService::class, 'logout'));
    }
}

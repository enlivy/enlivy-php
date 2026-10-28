<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\AppInstallRequest;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\UserApp;
use Enlivy\UserAppRevision;
use Enlivy\Util\RequestOptions;

/**
 * A developer's own Apps, under `/users/{user}/apps`. These routes answer a signed-in dashboard session
 * or an OAuth token holding the `apps:develop` scope, for its own person only; an API key is refused
 * with a 403.
 *
 * A bundle is `manifest` (an object) and `documents` (an object keyed by document key). An App
 * without presentation documents sends `'documents' => new \stdClass`, since `[]` is sent as a list.
 *
 * Every answer that carries a secret (`createOAuthClient()`, `rotateOAuthClientSecret()`,
 * `rotateWebhookSecret()`) takes a `recipient_key`, an X25519 public key (32 bytes, base64url without
 * padding), and answers the secret sealed to it as `sealed_secret`; without a key, a dashboard
 * sign-in gets it in plain text once and an OAuth grant is refused with a 422. The sealed shape is
 * `GET /discovery/apps/schemas/sealed_secret`; the SDK does not open it.
 */
class UserAppService extends AbstractService
{
    use HasIncludes;

    protected const ?string RESOURCE_CLASS = UserApp::class;

    public const array AVAILABLE_INCLUDES = [];

    /**
     * @return Collection<UserApp>
     */
    public function list(string $userId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);

        /** @var Collection<UserApp> */
        return $this->requestCollection('GET', "/users/{$userId}/apps", $params, $opts);
    }

    public function retrieve(string $userId, string $appId, array $params = [], ?RequestOptions $opts = null): UserApp
    {
        $this->validateIncludes($params);

        /** @var UserApp */
        return $this->request('GET', "/users/{$userId}/apps/{$appId}", $params, $opts);
    }

    /**
     * Register an App from its bundle as revision 1. A personal App installs in every organization the
     * developer belongs to; `organization_id` may name one they manage Apps in and limits nothing.
     */
    public function register(string $userId, array $bundle, ?RequestOptions $opts = null): UserApp
    {
        /** @var UserApp */
        return $this->request('POST', "/users/{$userId}/apps", $bundle, $opts);
    }

    /**
     * Check a bundle without storing it: `valid`, and each issue's `code` and `message`.
     */
    public function validate(string $userId, array $bundle, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', "/users/{$userId}/apps/validate", $bundle, $opts, EnlivyObject::class);
    }

    /**
     * Submit a new bundle; its manifest `version` must be higher than the App's latest.
     */
    public function submitRevision(string $userId, string $appId, array $bundle, ?RequestOptions $opts = null): UserAppRevision
    {
        /** @var UserAppRevision */
        return $this->request('POST', "/users/{$userId}/apps/{$appId}/revisions", $bundle, $opts, UserAppRevision::class);
    }

    public function publishRevision(string $userId, string $appId, string $revisionId, ?RequestOptions $opts = null): UserAppRevision
    {
        /** @var UserAppRevision */
        return $this->request('POST', "/users/{$userId}/apps/{$appId}/revisions/{$revisionId}/publish", null, $opts, UserAppRevision::class);
    }

    /**
     * A new confidential OAuth client for the App, attached to it in the same step: `redirect_uris` on
     * the provider origin, and `recipient_key`. Answers `app` and `oauth_client`, the client with its
     * `plain_secret` or `sealed_secret`, once.
     */
    public function createOAuthClient(string $userId, string $appId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', "/users/{$userId}/apps/{$appId}/oauth-clients", $params, $opts, EnlivyObject::class);
    }

    /**
     * Let one of the developer's existing OAuth clients act for the App's provider.
     */
    public function attachOAuthClient(string $userId, string $appId, string $clientId, ?RequestOptions $opts = null): UserApp
    {
        /** @var UserApp */
        return $this->request('PUT', "/users/{$userId}/apps/{$appId}/oauth-clients/{$clientId}", null, $opts);
    }

    /**
     * A new secret for one of the App's clients, as `plain_secret` or `sealed_secret`; the previous one
     * keeps working for the overlap.
     */
    public function rotateOAuthClientSecret(string $userId, string $appId, string $clientId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', "/users/{$userId}/apps/{$appId}/oauth-clients/{$clientId}/rotate-secret", $params, $opts, EnlivyObject::class);
    }

    public function detachOAuthClient(string $userId, string $appId, string $clientId, ?RequestOptions $opts = null): UserApp
    {
        /** @var UserApp */
        return $this->request('DELETE', "/users/{$userId}/apps/{$appId}/oauth-clients/{$clientId}", null, $opts);
    }

    /**
     * When the App's webhook secret last changed (`rotated_at`, `previous_secret_valid_until`); the
     * secret itself is never read back. A 404 means the App declares no webhooks.
     */
    public function webhookSecret(string $userId, string $appId, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('GET', "/users/{$userId}/apps/{$appId}/webhook-secret", null, $opts, EnlivyObject::class);
    }

    /**
     * A new webhook secret as `secret` or `sealed_secret`; the previous one keeps signing for the overlap.
     */
    public function rotateWebhookSecret(string $userId, string $appId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', "/users/{$userId}/apps/{$appId}/webhook-secret/rotate", $params, $opts, EnlivyObject::class);
    }

    /**
     * The organizations the developer asked to install this App, and what each answered.
     *
     * @return Collection<AppInstallRequest>
     */
    public function installRequests(string $userId, string $appId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        return $this->requestCollection('GET', "/users/{$userId}/apps/{$appId}/install-requests", $params, $opts, AppInstallRequest::class);
    }
}

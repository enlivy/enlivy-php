<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\AppRegistrationRequest;
use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Apps an agent drafts for its person, under `/users/{user}/app-registration-requests`. The agent
 * (an OAuth token with the `apps:draft` scope) drafts, reads, cancels and claims the requests its own
 * client made; the person's API key reaches all of their requests the same way. The person approves
 * in the dashboard, at the request's `approval_url`; approving answers no API credential. Approval
 * registers the App, publishes revision 1, makes
 * its OAuth client when the manifest uses OAuth or a data connector, and installs it in the
 * organization the draft names, with the permissions the person left on. A request lapses after 24 hours.
 *
 * A draft carries the bundle (`manifest`, `documents`), `organization_id`, `redirect_uris`, the
 * `permissions` to grant and a `recipient_key` (an X25519 public key, base64url without padding).
 * `claim()` answers the created ids and `sealed_secrets`, once; the sealed copies are then erased.
 * The sealed shape is `GET /discovery/apps/schemas/sealed_secret`; the SDK does not open it.
 */
class AppRegistrationRequestService extends AbstractService
{
    use HasIncludes;

    protected const ?string RESOURCE_CLASS = AppRegistrationRequest::class;

    public const array AVAILABLE_INCLUDES = [];

    /**
     * @return Collection<AppRegistrationRequest>
     */
    public function list(string $userId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);

        /** @var Collection<AppRegistrationRequest> */
        return $this->requestCollection('GET', "/users/{$userId}/app-registration-requests", $params, $opts);
    }

    public function retrieve(string $userId, string $id, array $params = [], ?RequestOptions $opts = null): AppRegistrationRequest
    {
        $this->validateIncludes($params);

        /** @var AppRegistrationRequest */
        return $this->request('GET', "/users/{$userId}/app-registration-requests/{$id}", $params, $opts);
    }

    public function create(string $userId, array $params, ?RequestOptions $opts = null): AppRegistrationRequest
    {
        /** @var AppRegistrationRequest */
        return $this->request('POST', "/users/{$userId}/app-registration-requests", $params, $opts);
    }

    /**
     * The checks a draft would meet, with nothing stored: `valid` true, or a 422 naming what is wrong.
     */
    public function validate(string $userId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        return $this->request('POST', "/users/{$userId}/app-registration-requests/validate", $params, $opts, EnlivyObject::class);
    }

    public function cancel(string $userId, string $id, ?RequestOptions $opts = null): AppRegistrationRequest
    {
        /** @var AppRegistrationRequest */
        return $this->request('POST', "/users/{$userId}/app-registration-requests/{$id}/cancel", null, $opts);
    }

    /**
     * Collect an approved draft's `user_app_id`, `oauth_client_id`, `organization_app_installation_id`
     * and `sealed_secrets`, once.
     */
    public function claim(string $userId, string $id, ?RequestOptions $opts = null): AppRegistrationRequest
    {
        /** @var AppRegistrationRequest */
        return $this->request('POST', "/users/{$userId}/app-registration-requests/{$id}/claim", null, $opts);
    }
}

<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\Collection;
use Enlivy\Organization\CheckoutSession;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

class CheckoutSessionService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'checkout-sessions';
    protected const ?string RESOURCE_CLASS = CheckoutSession::class;

    public const array AVAILABLE_INCLUDES = [
        'receiver_user',
        'billing_package',
        'billing_schedule',
        'invoice',
        'proforma_invoice',
    ];

    public const array AVAILABLE_FILTERS = [
        'status',
        'client_reference_id',
        'organization_receiver_user_id',
    ];

    /**
     * @return Collection<CheckoutSession>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<CheckoutSession> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): CheckoutSession
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var CheckoutSession */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * The browser's token is in the response meta, never on the session, and is answered only here:
     * `$session->lastResponse()?->json['meta']['client_token']`. Pass an `idempotencyKey` in the options
     * so a retry answers with the same session and a fresh token instead of opening a second one.
     */
    public function create(array $params, ?RequestOptions $opts = null): CheckoutSession
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var CheckoutSession */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    /**
     * Corrects only `client_reference_id`, the `source_*` fields, `metadata` (replaced whole) and, while the
     * session is open, a later `expires_at`. The API ignores any other field: the order never changes.
     */
    public function update(string $id, array $params = [], ?RequestOptions $opts = null): CheckoutSession
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var CheckoutSession */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function expire(string $id, array $params = [], ?RequestOptions $opts = null): CheckoutSession
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var CheckoutSession */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/expire"), $params, $opts);
    }
}

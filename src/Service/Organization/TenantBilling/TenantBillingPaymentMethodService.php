<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\TenantBilling;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\UserPaymentMethod;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing the payment methods used to pay the organization's
 * Enlivy subscription (tenant-billing invoices).
 *
 * These belong to Enlivy SRL's mirror user, not the tenant organization's own users.
 */
class TenantBillingPaymentMethodService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'tenant-billing/payment-methods';
    protected const ?string RESOURCE_CLASS = UserPaymentMethod::class;

    public const array AVAILABLE_INCLUDES = [
        'organization_user',
        'stripe_data',
        'created_by_user',
        'deleted_by_user',
    ];

    public const array AVAILABLE_FILTERS = [];

    /**
     * @return Collection<UserPaymentMethod>
     *
     * @see HasFilters::GLOBAL_FILTERS for global filters (q, ids, page, per_page, etc.)
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): UserPaymentMethod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    /**
     * Create a Stripe SetupIntent for collecting a new card off-session.
     */
    public function setupIntent(array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . '/setup-intent'), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): UserPaymentMethod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): UserPaymentMethod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): UserPaymentMethod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function setAsDefault(string $id, array $params = [], ?RequestOptions $opts = null): UserPaymentMethod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/set-as-default"), $params, $opts);
    }

    public function restore(string $id, array $params = [], ?RequestOptions $opts = null): UserPaymentMethod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/restore"), $params, $opts);
    }
}

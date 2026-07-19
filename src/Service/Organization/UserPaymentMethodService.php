<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\UserPaymentMethod;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing an organization user's payment methods.
 */
class UserPaymentMethodService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const ?string RESOURCE_CLASS = UserPaymentMethod::class;

    public const array AVAILABLE_INCLUDES = [
        'organization_user',
        'stripe_data',
        'created_by_user',
        'deleted_by_user',
    ];

    public const array AVAILABLE_FILTERS = [
        'provider',
        'type',
        'origin',
        'status',
        'is_default',
    ];

    /**
     * @return Collection<UserPaymentMethod>
     *
     * @see HasFilters::GLOBAL_FILTERS for global filters (q, ids, page, per_page, etc.)
     */
    public function list(string $userId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<UserPaymentMethod> */
        return $this->requestCollection('GET', $this->orgPath($orgId, "users/{$userId}/payment-methods"), $params, $opts);
    }

    public function create(string $userId, array $params, ?RequestOptions $opts = null): UserPaymentMethod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserPaymentMethod */
        return $this->request('POST', $this->orgPath($orgId, "users/{$userId}/payment-methods"), $params, $opts);
    }

    public function retrieve(string $userId, string $paymentMethodId, array $params = [], ?RequestOptions $opts = null): UserPaymentMethod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserPaymentMethod */
        return $this->request('GET', $this->orgPath($orgId, "users/{$userId}/payment-methods/{$paymentMethodId}"), $params, $opts);
    }

    public function update(string $userId, string $paymentMethodId, array $params, ?RequestOptions $opts = null): UserPaymentMethod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserPaymentMethod */
        return $this->request('PUT', $this->orgPath($orgId, "users/{$userId}/payment-methods/{$paymentMethodId}"), $params, $opts);
    }

    public function delete(string $userId, string $paymentMethodId, array $params = [], ?RequestOptions $opts = null): UserPaymentMethod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserPaymentMethod */
        return $this->request('DELETE', $this->orgPath($orgId, "users/{$userId}/payment-methods/{$paymentMethodId}"), $params, $opts);
    }

    public function restore(string $userId, string $paymentMethodId, array $params = [], ?RequestOptions $opts = null): UserPaymentMethod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserPaymentMethod */
        return $this->request('POST', $this->orgPath($orgId, "users/{$userId}/payment-methods/restore/{$paymentMethodId}"), $params, $opts);
    }

    public function importFromStripe(string $userId, array $params, ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('POST', $this->orgPath($orgId, "users/{$userId}/payment-methods/import-from-stripe"), $params, $opts);
    }

    public function setAsDefault(string $userId, string $paymentMethodId, array $params = [], ?RequestOptions $opts = null): UserPaymentMethod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserPaymentMethod */
        return $this->request('POST', $this->orgPath($orgId, "users/{$userId}/payment-methods/{$paymentMethodId}/set-as-default"), $params, $opts);
    }

    public function syncFromStripe(string $userId, string $paymentMethodId, array $params = [], ?RequestOptions $opts = null): UserPaymentMethod
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var UserPaymentMethod */
        return $this->request('POST', $this->orgPath($orgId, "users/{$userId}/payment-methods/{$paymentMethodId}/sync-from-stripe"), $params, $opts);
    }
}

<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\Collection;
use Enlivy\Organization\Offer;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing offers.
 *
 * @method Offer restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class OfferService extends AbstractService
{
    use HasRestore;
    use HasIncludes;

    protected const string RESOURCE = 'offers';
    protected const ?string RESOURCE_CLASS = Offer::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'project',
        'payment_plans',
        'contract_template',
        'created_by_user',
        'deleted_by_user',
        'expired_by_user',
        'contract_default_sender_user',
    ];

    /**
     * @return Collection<Offer>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Offer
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Offer
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Offer
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Offer
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function expire(string $id, array $params = [], ?RequestOptions $opts = null): Offer
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/expire"), $params, $opts);
    }
}

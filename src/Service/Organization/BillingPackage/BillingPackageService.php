<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\BillingPackage;

use Enlivy\Collection;
use Enlivy\Organization\BillingPackage;
use Enlivy\Organization\Contract;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing billing packages.
 *
 * @method BillingPackage restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class BillingPackageService extends AbstractService
{
    use HasRestore;
    use HasIncludes;
    use HasFilters;

    protected const string RESOURCE = 'billing-packages';
    protected const ?string RESOURCE_CLASS = BillingPackage::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'project',
        'groups',
        'payment_plans',
        'contract_templates',
        'subscription_terms',
        'created_by_user',
        'deleted_by_user',
        'expired_by_user',
    ];

    public const array AVAILABLE_FILTERS = [
        'is_active',
        'type',
        'organization_project_id',
        'only_available',
    ];

    /**
     * List all billing packages.
     *
     * Resource-specific filters:
     * - `is_active` (bool) - Filter by active packages
     * - `type` (string) - Filter by package type
     * - `organization_project_id` (string) - Filter by project
     * - `only_available` (bool) - Show only available packages
     *
     * @return Collection<BillingPackage>
     *
     * @see HasFilters::GLOBAL_FILTERS for global filters (q, ids, page, per_page, etc.)
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<BillingPackage> */
        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): BillingPackage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var BillingPackage */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): BillingPackage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var BillingPackage */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): BillingPackage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var BillingPackage */
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): BillingPackage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var BillingPackage */
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function expire(string $id, array $params = [], ?RequestOptions $opts = null): BillingPackage
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        /** @var BillingPackage */
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE . "/{$id}/expire"), $params, $opts);
    }

    /**
     * Download the billing package as a document.
     *
     * Accepts optional `layout` and `locale` query params.
     */
    public function download(string $id, array $params = [], ?RequestOptions $opts = null): string
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestRaw('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/download"), $params, $opts);
    }

    /**
     * Render a preview of one of the package's contract templates. Accepts an
     * optional `locale` (must be one of the package's locales).
     */
    public function previewContractTemplate(string $id, string $templateId, array $params = [], ?RequestOptions $opts = null): Contract
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Contract */
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/contract-templates/{$templateId}/preview"), $params, $opts, Contract::class);
    }
}

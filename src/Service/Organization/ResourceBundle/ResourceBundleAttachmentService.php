<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\ResourceBundle;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\ResourceBundleAttachment;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasFilters;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

class ResourceBundleAttachmentService extends AbstractService
{
    use HasIncludes;
    use HasFilters;

    protected const ?string RESOURCE_CLASS = ResourceBundleAttachment::class;

    public const array AVAILABLE_INCLUDES = [
        'organization_project',
        'organization_resource_bundle',
    ];

    /**
     * An empty `organization_project_id` (`''`) lists the organization-wide attachments.
     */
    public const array AVAILABLE_FILTERS = [
        'organization_project_id',
    ];

    /**
     * @return Collection<ResourceBundleAttachment>
     */
    public function list(string $bundleId, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $this->validateFilters($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Collection<ResourceBundleAttachment> */
        return $this->requestCollection('GET', $this->orgPath($orgId, "resource-bundles/{$bundleId}/attachments"), $params, $opts);
    }

    /**
     * `organization_project_id` is required and may be null, which applies the bundle to the whole
     * organization. Attaching where the bundle already applies returns that attachment.
     */
    public function create(string $bundleId, array $params, ?RequestOptions $opts = null): ResourceBundleAttachment
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var ResourceBundleAttachment */
        return $this->request('POST', $this->orgPath($orgId, "resource-bundles/{$bundleId}/attachments"), $params, $opts);
    }

    public function delete(string $bundleId, string $attachmentId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, "resource-bundles/{$bundleId}/attachments/{$attachmentId}"), $params, $opts, EnlivyObject::class);
    }
}

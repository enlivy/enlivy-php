<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Playbook;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasDownload;
use Enlivy\Service\Concern\HasRestore;
use Enlivy\Service\Concern\HasTagging;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Service for managing playbooks.
 *
 * @method Playbook restore(string $id, array $params = [], ?RequestOptions $opts = null)
 */
class PlaybookService extends AbstractService
{
    use HasRestore;
    use HasTagging;
    use HasDownload;
    use HasIncludes;

    protected const string RESOURCE = 'playbooks';
    protected const ?string RESOURCE_CLASS = Playbook::class;

    public const array AVAILABLE_INCLUDES = [
        'organization',
        'procedure_organization_owner_user',
        'deleted_by_user',
        'organization_project',
        'parent_organization_playbook',
        'tag_ids',
        'procedure_process_steps_files',
        'procedure_files',
    ];

    /**
     * @return Collection<Playbook>
     */
    public function list(array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function retrieve(string $id, array $params = [], ?RequestOptions $opts = null): Playbook
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function create(array $params, ?RequestOptions $opts = null): Playbook
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('POST', $this->orgPath($orgId, self::RESOURCE), $params, $opts);
    }

    public function update(string $id, array $params, ?RequestOptions $opts = null): Playbook
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('PUT', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    public function delete(string $id, array $params = [], ?RequestOptions $opts = null): Playbook
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);
        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}"), $params, $opts);
    }

    /**
     * @return Collection<EnlivyObject>
     */
    public function listRevisions(string $id, array $params = [], ?RequestOptions $opts = null): Collection
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->requestCollection('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/revisions"), $params, $opts);
    }

    public function retrieveRevision(string $id, string $revisionId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('GET', $this->orgPath($orgId, self::RESOURCE . "/{$id}/revisions/{$revisionId}"), $params, $opts);
    }

    public function deleteRevision(string $id, string $revisionId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, self::RESOURCE . "/{$id}/revisions/{$revisionId}"), $params, $opts);
    }
}

<?php

declare(strict_types=1);

namespace Enlivy\Service\Organization\Task;

use Enlivy\EnlivyObject;
use Enlivy\Organization\Comment;
use Enlivy\Service\AbstractService;
use Enlivy\Service\Concern\HasIncludes;
use Enlivy\Util\RequestOptions;

/**
 * Comments are read through `tasks->feed()`. Writing needs a key held by a member of the
 * organization, since a comment has an author; only that author may edit it.
 */
class TaskCommentService extends AbstractService
{
    use HasIncludes;

    protected const ?string RESOURCE_CLASS = Comment::class;

    public const array AVAILABLE_INCLUDES = [
        'author_organization_user',
        'deleted_by_user',
    ];

    public function create(string $taskId, array $params, ?RequestOptions $opts = null): Comment
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Comment */
        return $this->request('POST', $this->orgPath($orgId, "tasks/{$taskId}/comments"), $params, $opts);
    }

    public function update(string $taskId, string $commentId, array $params, ?RequestOptions $opts = null): Comment
    {
        $this->validateIncludes($params);
        $orgId = $this->resolveOrganizationId($params, $opts);

        /** @var Comment */
        return $this->request('PUT', $this->orgPath($orgId, "tasks/{$taskId}/comments/{$commentId}"), $params, $opts);
    }

    public function delete(string $taskId, string $commentId, array $params = [], ?RequestOptions $opts = null): EnlivyObject
    {
        $orgId = $this->resolveOrganizationId($params, $opts);

        return $this->request('DELETE', $this->orgPath($orgId, "tasks/{$taskId}/comments/{$commentId}"), $params, $opts, EnlivyObject::class);
    }
}

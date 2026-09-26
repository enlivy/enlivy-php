<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * A removed comment stays in the feed with a null `body`.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_commentable_type
 * @property string $organization_commentable_id
 * @property string|null $author_organization_user_id
 * @property string|null $body
 * @property string|null $edited_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class Comment extends ApiResource
{
    public const ?string OBJECT_NAME = 'comment';
}

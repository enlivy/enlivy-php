<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * One submitted bundle. `revision` is Enlivy's number, `version` the developer's `major.minor.patch`.
 * `review` is `not_required`, `required` (the revision asks for a sensitive scope and waits on Enlivy) or `approved`.
 *
 * @property string $id
 * @property string $object
 * @property string $user_app_id
 * @property int $revision
 * @property string $version
 * @property string|null $published_at
 * @property string $review
 * @property string|null $reviewed_at
 * @property string|null $review_note
 * @property array|null $manifest
 * @property array|null $documents
 * @property string $created_at
 */
class UserAppRevision extends ApiResource
{
    public const ?string OBJECT_NAME = 'user_app_revision';
}

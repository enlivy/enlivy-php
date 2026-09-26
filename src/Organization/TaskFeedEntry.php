<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;
use Enlivy\EnlivyObject;

/**
 * Either `comment` or `event` is filled, as `kind` says; the other is null.
 *
 * @property string $id
 * @property string $object
 * @property string $kind
 * @property string|null $occurred_at
 * @property EnlivyObject|null $comment
 * @property EnlivyObject|null $event
 */
class TaskFeedEntry extends ApiResource
{
    public const ?string OBJECT_NAME = 'task_feed_entry';
}

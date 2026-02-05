<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Playbook in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $title
 * @property string $content
 * @property string|null $category
 * @property int $order
 * @property bool $is_published
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class Playbook extends ApiResource
{
    public const ?string OBJECT_NAME = 'playbook';
}

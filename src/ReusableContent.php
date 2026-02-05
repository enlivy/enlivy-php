<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a Reusable Content in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property string $content
 * @property string $type
 * @property string $created_at
 * @property string $updated_at
 */
class ReusableContent extends ApiResource
{
    public const ?string OBJECT_NAME = 'reusable_content';
}

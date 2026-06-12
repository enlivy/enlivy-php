<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a single field-level change within an Event Trail entry.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_event_trail_id
 * @property string $subject_type
 * @property string $subject_id
 * @property string $event_type
 * @property array|null $changed_fields
 * @property array|null $old_values
 * @property array|null $new_values
 * @property array|null $metadata
 * @property string $created_at
 */
class EventTrailChange extends ApiResource
{
    public const ?string OBJECT_NAME = 'event_trail_change';
}

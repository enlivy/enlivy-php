<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an Event Trail entry in the Enlivy API.
 *
 * `subject_type` is the snake_case entity name the trail belongs to
 * (e.g. `invoice`, `receipt`, `billing_schedule`).
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $subject_type
 * @property string $subject_id
 * @property string $event_type
 * @property string $origin
 * @property string|null $actor_organization_user_id
 * @property string|null $organization_invoice_charge_log_id
 * @property array|null $metadata
 * @property string $occurred_at
 * @property string $created_at
 */
class EventTrail extends ApiResource
{
    public const ?string OBJECT_NAME = 'event_trail';
}

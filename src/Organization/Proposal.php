<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Proposal in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $organization_offer_id
 * @property string|null $organization_prospect_id
 * @property string|null $organization_user_id
 * @property string $status
 * @property string|null $sent_at
 * @property string|null $viewed_at
 * @property string|null $accepted_at
 * @property string|null $rejected_at
 * @property string|null $expires_at
 * @property string $created_at
 * @property string $updated_at
 */
class Proposal extends ApiResource
{
    public const ?string OBJECT_NAME = 'proposal';
}

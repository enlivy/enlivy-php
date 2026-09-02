<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $object
 * @property string $organization_prospect_id
 * @property string|null $confidence
 * @property array $signals
 * @property array $blockers
 * @property int $organization_proposals_count
 * @property int $organization_prospect_activities_count
 */
class ProspectDuplicate extends ApiResource
{
    public const ?string OBJECT_NAME = 'prospect_duplicate';
}

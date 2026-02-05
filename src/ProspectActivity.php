<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a Prospect Activity in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $organization_prospect_id
 * @property string $type
 * @property string $description
 * @property array $data
 * @property string|null $created_by_user_id
 * @property string $created_at
 * @property string $updated_at
 */
class ProspectActivity extends ApiResource
{
    public const ?string OBJECT_NAME = 'prospect_activity';
}

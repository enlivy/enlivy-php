<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents a Prospect in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string|null $organization_prospect_status_id
 * @property string|null $title
 * @property string $first_name
 * @property string $last_name
 * @property string|null $company_name
 * @property string $email
 * @property string|null $phone_number
 * @property string|null $phone_number_country_code
 * @property string|null $country_code
 * @property string|null $source_type
 * @property string|null $source_channel
 * @property string|null $source_campaign
 * @property string|null $summary
 * @property float|null $budget
 * @property string|null $budget_currency
 * @property array $social_profiles
 * @property string|null $linked_organization_user_id
 * @property string|null $assigned_organization_user_id
 * @property string|null $assigned_organization_project_id
 * @property string|null $state_qualified_at
 * @property string|null $state_disqualified_at
 * @property string|null $state_won_at
 * @property string|null $state_lost_at
 * @property string|null $state_lost_reason
 * @property string|null $created_by_user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class Prospect extends ApiResource
{
    public const ?string OBJECT_NAME = 'prospect';
}

<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_user_id
 * @property string|null $organization_contract_id
 * @property string|null $type
 * @property string|null $jurisdiction_code
 * @property string|null $jurisdiction_subdivision_iso_3166
 * @property string|null $timezone
 * @property string|null $workweek_start
 * @property string|null $contract_number
 * @property string|null $contract_date
 * @property string|null $occupation_code
 * @property string|null $job_title
 * @property string|null $work_site
 * @property string|null $start_date
 * @property string|null $start_date_source
 * @property string|null $end_date
 * @property string|null $end_date_source
 * @property string|null $end_reason
 * @property string $lifecycle
 * @property string $registry_status
 * @property string|null $registry_reference
 * @property string|null $registry_last_verified_at
 * @property string|null $pay_period
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class Employment extends ApiResource
{
    public const ?string OBJECT_NAME = 'employment';
}

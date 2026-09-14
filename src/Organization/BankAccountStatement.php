<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * @property string $id
 * @property string $organization_id
 * @property string $organization_bank_account_id
 * @property string $period_start
 * @property string $period_end
 * @property string $format
 * @property string|null $reference
 * @property string $file_name
 * @property string $file_extension
 * @property int $file_size
 * @property string|null $uploaded_by_user_id
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 */
class BankAccountStatement extends ApiResource
{
    public const ?string OBJECT_NAME = 'bank_account_statement';
}

<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Contract Status in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property array|null $title_lang_map
 * @property array|null $description_lang_map
 * @property string|null $contract_state
 * @property string|null $action_required_type
 * @property string $direction
 * @property string|null $rgba_color_code
 * @property int $order
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class ContractStatus extends ApiResource
{
    public const ?string OBJECT_NAME = 'contract_status';
}

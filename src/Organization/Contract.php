<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Contract in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_contract_id
 * @property string|null $organization_contract_prefix_id
 * @property string|null $organization_contract_status_id
 * @property string|null $organization_sender_user_id
 * @property string|null $organization_receiver_user_id
 * @property string|null $organization_file_id
 * @property string|null $number
 * @property string $formatted_number
 * @property string $title
 * @property string|null $sub_title
 * @property string|null $category
 * @property string|null $source
 * @property string $direction
 * @property string|null $layout
 * @property string|null $content_introduction
 * @property string|null $content_signature_disclaimer
 * @property string|null $issued_at
 * @property string|null $ends_at
 * @property string|null $renewed_at
 * @property string|null $signed_by_all_parties_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class Contract extends ApiResource
{
    public const ?string OBJECT_NAME = 'contract';
}

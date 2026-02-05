<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents a Proposal in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string|null $organization_project_id
 * @property string|null $organization_offer_id
 * @property string|null $organization_offer_payment_plan_id
 * @property string|null $organization_prospect_id
 * @property string|null $organization_receiver_user_id
 * @property string|null $organization_sender_user_id
 * @property string|null $organization_contract_id
 * @property string|null $organization_order_id
 * @property string|null $contract_default_sender_user_id
 * @property string|null $created_by_user_id
 * @property string|null $expired_by_user_id
 * @property string|null $access_token
 * @property string $status
 * @property string|null $recipient_email
 * @property string|null $recipient_name
 * @property string $currency
 * @property float $sub_total
 * @property float $discount
 * @property float $total
 * @property array|null $allowed_payment_methods
 * @property bool $contract_is_required
 * @property string|null $contract_trigger
 * @property array|null $note_lang_map
 * @property string|null $sent_at
 * @property string|null $expires_at
 * @property string|null $expired_at
 * @property string|null $viewed_at
 * @property string|null $accepted_at
 * @property string|null $rejected_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class Proposal extends ApiResource
{
    public const ?string OBJECT_NAME = 'proposal';
}

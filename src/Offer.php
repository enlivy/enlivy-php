<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents an Offer in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $name
 * @property string|null $description
 * @property array $price_map
 * @property string $currency
 * @property array $payment_plans
 * @property array $line_items
 * @property string|null $contract_template_id
 * @property bool $is_active
 * @property string $created_at
 * @property string $updated_at
 */
class Offer extends ApiResource
{
    public const ?string OBJECT_NAME = 'offer';
}

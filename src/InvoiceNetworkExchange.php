<?php

declare(strict_types=1);

namespace Enlivy;

/**
 * Represents an Invoice Network Exchange in the Enlivy API.
 *
 * @property string $id
 * @property string $organization_id
 * @property string $organization_invoice_id
 * @property string $network
 * @property string $status
 * @property string|null $external_id
 * @property array $response
 * @property string $created_at
 * @property string $updated_at
 */
class InvoiceNetworkExchange extends ApiResource
{
    public const ?string OBJECT_NAME = 'invoice_network_exchange';
}

<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an Invoice Peppol Network Exchange in the Enlivy API.
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_invoice_id
 * @property string $network
 * @property string $direction
 * @property string $status
 * @property string|null $external_id
 * @property string|null $exchange_file_name
 * @property string|null $pdf_file_name
 * @property array|null $response_json
 * @property string|null $institution_exchange_message_created_at
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 * @property string|null $deleted_by_user_id
 */
class InvoiceNetworkExchange extends ApiResource
{
    public const ?string OBJECT_NAME = 'invoice_network_exchange';
}

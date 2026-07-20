<?php

declare(strict_types=1);

namespace Enlivy\Organization;

use Enlivy\ApiResource;

/**
 * Represents an Invoice Charge Log entry in the Enlivy API.
 *
 * Audit record of an attempt to collect payment on an invoice (off-session card charge,
 * portal-initiated payment, cron sweep, etc.).
 *
 * @property string $id
 * @property string $object
 * @property string $organization_id
 * @property string $organization_invoice_id
 * @property string|null $organization_user_payment_method_id
 * @property string|null $initiated_by_organization_user_id
 * @property string $source One of: cron, admin, portal_user, standalone, webhook
 * @property string $status One of: in_progress, succeeded, failed, requires_action, already_paid
 * @property string $payment_provider One of: stripe, paypal
 * @property string|null $payment_provider_reference
 * @property string|null $error_code
 * @property string|null $error_message
 * @property float $amount
 * @property string $currency
 * @property string|null $attempted_at
 * @property string|null $completed_at
 * @property string $created_at
 * @property string $updated_at
 */
class InvoiceChargeLog extends ApiResource
{
    public const ?string OBJECT_NAME = 'invoice_charge_log';
}

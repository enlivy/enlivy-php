<?php

declare(strict_types=1);

namespace Enlivy\Enums\TenantBilling;

use Enlivy\Enums\Concern\EnumValues;

enum MeteredDomains: string
{
    use EnumValues;

    case INVOICE = 'invoice';
    case RECEIPT = 'receipt';
    case PAYSLIP = 'payslip';
    case BANK_TRANSACTION = 'bank_transaction';
    case CONTRACT = 'contract';
    case BILLING_SCHEDULE = 'billing_schedule';
    case PROPOSAL = 'proposal';
    case REPORT_SUBMISSION = 'report_submission';
    case AI_CALL = 'ai_call';
    case TRANSLATION_CALL = 'translation_call';
    case PROSPECT = 'prospect';
    case BANK_CONNECTION = 'bank_connection';
    case WEBHOOK_ENDPOINT = 'webhook_endpoint';
    case STORAGE_BYTE = 'storage_byte';
}

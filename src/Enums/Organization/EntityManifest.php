<?php

declare(strict_types=1);

namespace Enlivy\Enums\Organization;

use Enlivy\Enums\Concern\EnumValues;

enum EntityManifest: string
{
    use EnumValues;

    case USER = 'user';
    case USER_ADDRESS = 'user_address';
    case USER_ROLE = 'user_role';
    case TAG = 'tag';
    case INVOICE = 'invoice';
    case INVOICE_PREFIX = 'invoice_prefix';
    case RECEIPT = 'receipt';
    case RECEIPT_PREFIX = 'receipt_prefix';
    case PRODUCT = 'product';
    case TAX_RATE = 'tax_rate';
    case TAX_CLASS = 'tax_class';
    case BILLING_SCHEDULE = 'billing_schedule';
    case BANK_ACCOUNT = 'bank_account';
    case BANK_TRANSACTION = 'bank_transaction';
    case BANK_TRANSACTION_COST_TYPE = 'bank_transaction_cost_type';
    case CONTRACT = 'contract';
    case CONTRACT_PREFIX = 'contract_prefix';
    case CONTRACT_STATUS = 'contract_status';
    case CONTRACT_SIGNATURE = 'contract_signature';
    case PAYSLIP = 'payslip';
    case PAYSLIP_SCHEMA = 'payslip_schema';
    case PROJECT = 'project';
    case TASK = 'task';
    case TASK_STATUS = 'task_status';
    case PROSPECT = 'prospect';
    case PROSPECT_STATUS = 'prospect_status';
    case PROSPECT_ACTIVITY = 'prospect_activity';
    case GUIDELINE = 'guideline';
    case PLAYBOOK = 'playbook';
    case REPORT = 'report';
    case REPORT_SCHEMA = 'report_schema';
}

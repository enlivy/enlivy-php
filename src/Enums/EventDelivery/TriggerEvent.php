<?php

declare(strict_types=1);

namespace Enlivy\Enums\EventDelivery;

use Enlivy\Enums\Concern\EnumValues;

enum TriggerEvent: string
{
    use EnumValues;

    case BANK_ACCOUNT_CREATED = 'bank_account.created';
    case BANK_ACCOUNT_UPDATED = 'bank_account.updated';
    case BANK_ACCOUNT_DELETED = 'bank_account.deleted';
    case BANK_ACCOUNT_RESTORED = 'bank_account.restored';
    case BANK_TRANSACTION_CREATED = 'bank_transaction.created';
    case BANK_TRANSACTION_UPDATED = 'bank_transaction.updated';
    case BANK_TRANSACTION_DELETED = 'bank_transaction.deleted';
    case BANK_TRANSACTION_RESTORED = 'bank_transaction.restored';
    case INVOICE_CREATED = 'invoice.created';
    case INVOICE_UPDATED = 'invoice.updated';
    case INVOICE_DELETED = 'invoice.deleted';
    case INVOICE_RESTORED = 'invoice.restored';
    case INVOICE_PAID = 'invoice.paid';
    case PAYSLIP_CREATED = 'payslip.created';
    case PAYSLIP_UPDATED = 'payslip.updated';
    case PAYSLIP_DELETED = 'payslip.deleted';
    case PAYSLIP_RESTORED = 'payslip.restored';
    case RECEIPT_CREATED = 'receipt.created';
    case RECEIPT_UPDATED = 'receipt.updated';
    case RECEIPT_DELETED = 'receipt.deleted';
    case RECEIPT_RESTORED = 'receipt.restored';
    case USER_CREATED = 'user.created';
    case USER_UPDATED = 'user.updated';
    case USER_DELETED = 'user.deleted';
    case USER_RESTORED = 'user.restored';
    case NETWORK_EXCHANGE_CREATED = 'network_exchange.created';
    case NETWORK_EXCHANGE_UPDATED = 'network_exchange.updated';
    case CONTRACT_CREATED = 'contract.created';
    case CONTRACT_UPDATED = 'contract.updated';
    case CONTRACT_DELETED = 'contract.deleted';
    case CONTRACT_RESTORED = 'contract.restored';
    case BILLING_SCHEDULE_CREATED = 'billing_schedule.created';
    case BILLING_SCHEDULE_UPDATED = 'billing_schedule.updated';
    case BILLING_SCHEDULE_DELETED = 'billing_schedule.deleted';
    case BILLING_SCHEDULE_RESTORED = 'billing_schedule.restored';
    case BILLING_SCHEDULED_PAYMENT_CREATED = 'billing_scheduled_payment.created';
    case BILLING_SCHEDULED_PAYMENT_UPDATED = 'billing_scheduled_payment.updated';
    case PLAYBOOK_CREATED = 'playbook.created';
    case PLAYBOOK_UPDATED = 'playbook.updated';
    case PLAYBOOK_DELETED = 'playbook.deleted';
    case PLAYBOOK_RESTORED = 'playbook.restored';
    case PROJECT_CREATED = 'project.created';
    case PROJECT_UPDATED = 'project.updated';
    case PROJECT_DELETED = 'project.deleted';
    case PROJECT_RESTORED = 'project.restored';
    case PROJECT_MEMBER_CREATED = 'project.member.created';
    case PROJECT_MEMBER_UPDATED = 'project.member.updated';
    case PROJECT_MEMBER_DELETED = 'project.member.deleted';
    case PROSPECT_CREATED = 'prospect.created';
    case PROSPECT_UPDATED = 'prospect.updated';
    case PROSPECT_DELETED = 'prospect.deleted';
    case PROSPECT_RESTORED = 'prospect.restored';
    case PROSPECT_ACTIVITY_CREATED = 'prospect_activity.created';
    case PROSPECT_ACTIVITY_UPDATED = 'prospect_activity.updated';
    case PROSPECT_ACTIVITY_DELETED = 'prospect_activity.deleted';
    case PROSPECT_ACTIVITY_RESTORED = 'prospect_activity.restored';
    case BILLING_PACKAGE_CREATED = 'billing_package.created';
    case BILLING_PACKAGE_UPDATED = 'billing_package.updated';
    case BILLING_PACKAGE_DELETED = 'billing_package.deleted';
    case BILLING_PACKAGE_RESTORED = 'billing_package.restored';
    case PROPOSAL_CREATED = 'proposal.created';
    case PROPOSAL_UPDATED = 'proposal.updated';
    case PROPOSAL_DELETED = 'proposal.deleted';
    case PROPOSAL_RESTORED = 'proposal.restored';
    case PROPOSAL_SENT = 'proposal.sent';
    case PROPOSAL_ACCEPTED = 'proposal.accepted';
    case PROPOSAL_REJECTED = 'proposal.rejected';
    case PROPOSAL_EXPIRED = 'proposal.expired';
    case PROPOSAL_VIEWED = 'proposal.viewed';
    case PRODUCT_CREATED = 'product.created';
    case PRODUCT_UPDATED = 'product.updated';
    case PRODUCT_DELETED = 'product.deleted';
    case CONTRACT_SIGNATURE_CREATED = 'contract_signature.created';
    case CONTRACT_SIGNATURE_UPDATED = 'contract_signature.updated';
    case CONTRACT_SIGNATURE_DELETED = 'contract_signature.deleted';
    case CONTRACT_SIGNATURE_RESTORED = 'contract_signature.restored';
    case CONTRACT_ALL_PARTIES_SIGNED = 'contract.all_parties_signed';
}

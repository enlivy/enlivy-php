<?php

declare(strict_types=1);

namespace Enlivy\Util;

use Enlivy\AiAgent;
use Enlivy\ApiResource;
use Enlivy\InvitationCode;
use Enlivy\OAuthAuthorization;
use Enlivy\OAuthClient;
use Enlivy\OAuthToken;
use Enlivy\Organization;
use Enlivy\Organization\ApiCredential;
use Enlivy\Organization\BankAccount;
use Enlivy\Organization\BankTransaction;
use Enlivy\Organization\BankTransactionCostType;
use Enlivy\Organization\BillingSchedule;
use Enlivy\Organization\Contract;
use Enlivy\Organization\ContractPrefix;
use Enlivy\Organization\ContractSignature;
use Enlivy\Organization\ContractStatus;
use Enlivy\Organization\ExportData;
use Enlivy\Organization\File;
use Enlivy\Organization\Guideline;
use Enlivy\Organization\Invoice;
use Enlivy\Organization\InvoiceNetworkExchange;
use Enlivy\Organization\InvoiceNotificationLog;
use Enlivy\Organization\InvoicePrefix;
use Enlivy\Organization\Notification;
use Enlivy\Organization\Offer;
use Enlivy\Organization\Payslip;
use Enlivy\Organization\PayslipSchema;
use Enlivy\Organization\Playbook;
use Enlivy\Organization\Product;
use Enlivy\Organization\Project;
use Enlivy\Organization\ProjectMember;
use Enlivy\Organization\Proposal;
use Enlivy\Organization\Prospect;
use Enlivy\Organization\ProspectActivity;
use Enlivy\Organization\ProspectStatus;
use Enlivy\Organization\Receipt;
use Enlivy\Organization\ReceiptPrefix;
use Enlivy\Organization\Report;
use Enlivy\Organization\ReportSchema;
use Enlivy\Organization\ReportSchemaField;
use Enlivy\Organization\ResourceBundle;
use Enlivy\Organization\ReusableContent;
use Enlivy\Organization\Tag;
use Enlivy\Organization\Task;
use Enlivy\Organization\TaskStatus;
use Enlivy\Organization\TaxClass;
use Enlivy\Organization\TaxFilingJurisdiction;
use Enlivy\Organization\TaxRate;
use Enlivy\Organization\TaxType;
use Enlivy\Organization\User as OrganizationUser;
use Enlivy\Organization\UserAddress;
use Enlivy\Organization\UserRole;
use Enlivy\Organization\UserRoleAbility;
use Enlivy\Organization\Webhook;
use Enlivy\User;
use Enlivy\UserToken;

/**
 * Maps API object types to their corresponding PHP classes.
 *
 * This enables automatic hydration of API responses into typed objects.
 */
final class ObjectTypes
{
    /**
     * Map of object type names to their PHP class names.
     *
     * @var array<string, class-string<ApiResource>>
     */
    private static array $typeMap = [
        // Global (non-org-scoped)
        'organization' => Organization::class,
        'user' => User::class,
        'user_token' => UserToken::class,
        'ai_agent' => AiAgent::class,
        'oauth_client' => OAuthClient::class,
        'oauth_authorization' => OAuthAuthorization::class,
        'oauth_token' => OAuthToken::class,
        'invitation_code' => InvitationCode::class,

        // Organization-scoped: CRM
        'prospect' => Prospect::class,
        'prospect_activity' => ProspectActivity::class,
        'prospect_status' => ProspectStatus::class,
        'project' => Project::class,
        'project_member' => ProjectMember::class,

        // Organization-scoped: Users & Roles
        'organization_user' => OrganizationUser::class,
        'user_role' => UserRole::class,
        'user_role_ability' => UserRoleAbility::class,
        'user_address' => UserAddress::class,

        // Organization-scoped: Accounting
        'invoice' => Invoice::class,
        'invoice_prefix' => InvoicePrefix::class,
        'invoice_notification_log' => InvoiceNotificationLog::class,
        'invoice_network_exchange' => InvoiceNetworkExchange::class,
        'receipt' => Receipt::class,
        'receipt_prefix' => ReceiptPrefix::class,
        'product' => Product::class,
        'bank_account' => BankAccount::class,
        'bank_transaction' => BankTransaction::class,
        'bank_transaction_cost_type' => BankTransactionCostType::class,
        'billing_schedule' => BillingSchedule::class,

        // Organization-scoped: Contracts
        'contract' => Contract::class,
        'contract_prefix' => ContractPrefix::class,
        'contract_status' => ContractStatus::class,
        'contract_signature' => ContractSignature::class,

        // Organization-scoped: Tax
        'tax_class' => TaxClass::class,
        'tax_rate' => TaxRate::class,
        'tax_type' => TaxType::class,
        'tax_filing_jurisdiction' => TaxFilingJurisdiction::class,

        // Organization-scoped: Payroll
        'payslip_schema' => PayslipSchema::class,
        'payslip' => Payslip::class,

        // Organization-scoped: Reports
        'report' => Report::class,
        'report_schema' => ReportSchema::class,
        'report_schema_field' => ReportSchemaField::class,

        // Organization-scoped: Content & Files
        'file' => File::class,
        'guideline' => Guideline::class,
        'playbook' => Playbook::class,
        'reusable_content' => ReusableContent::class,

        // Organization-scoped: Tasks
        'task' => Task::class,
        'task_status' => TaskStatus::class,

        // Organization-scoped: Settings & Tags
        'tag' => Tag::class,
        'notification' => Notification::class,

        // Organization-scoped: Webhooks & Export
        'webhook' => Webhook::class,
        'export_data' => ExportData::class,

        // Organization-scoped: Membership & Billing
        'offer' => Offer::class,
        'proposal' => Proposal::class,

        // Organization-scoped: API & Integrations
        'api_credential' => ApiCredential::class,
        'resource_bundle' => ResourceBundle::class,
    ];

    /**
     * Get the PHP class for a given object type.
     *
     * @return class-string<ApiResource>|null
     */
    public static function getClass(string $objectType): ?string
    {
        return self::$typeMap[$objectType] ?? null;
    }

    /**
     * Register a custom object type mapping.
     *
     * @param class-string<ApiResource> $class
     */
    public static function register(string $objectType, string $class): void
    {
        self::$typeMap[$objectType] = $class;
    }

    /**
     * Check if an object type is registered.
     */
    public static function has(string $objectType): bool
    {
        return isset(self::$typeMap[$objectType]);
    }
}

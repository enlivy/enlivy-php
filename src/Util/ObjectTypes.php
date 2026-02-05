<?php

declare(strict_types=1);

namespace Enlivy\Util;

use Enlivy\AiAgent;
use Enlivy\ApiCredential;
use Enlivy\ApiResource;
use Enlivy\BankAccount;
use Enlivy\BankTransaction;
use Enlivy\BankTransactionCostType;
use Enlivy\BillingSchedule;
use Enlivy\Contract;
use Enlivy\ContractPrefix;
use Enlivy\ContractSignature;
use Enlivy\ContractStatus;
use Enlivy\ExportData;
use Enlivy\File;
use Enlivy\Guideline;
use Enlivy\InvitationCode;
use Enlivy\Invoice;
use Enlivy\InvoiceNetworkExchange;
use Enlivy\InvoiceNotificationLog;
use Enlivy\InvoicePrefix;
use Enlivy\Notification;
use Enlivy\OAuthAuthorization;
use Enlivy\OAuthClient;
use Enlivy\OAuthToken;
use Enlivy\Offer;
use Enlivy\Organization;
use Enlivy\OrganizationUser;
use Enlivy\Payslip;
use Enlivy\PayslipSchema;
use Enlivy\Playbook;
use Enlivy\Product;
use Enlivy\Project;
use Enlivy\ProjectMember;
use Enlivy\Proposal;
use Enlivy\Prospect;
use Enlivy\ProspectActivity;
use Enlivy\ProspectStatus;
use Enlivy\Receipt;
use Enlivy\ReceiptPrefix;
use Enlivy\Report;
use Enlivy\ReportSchema;
use Enlivy\ReportSchemaField;
use Enlivy\ResourceBundle;
use Enlivy\ReusableContent;
use Enlivy\Tag;
use Enlivy\Task;
use Enlivy\TaskStatus;
use Enlivy\TaxClass;
use Enlivy\TaxFilingJurisdiction;
use Enlivy\TaxRate;
use Enlivy\TaxType;
use Enlivy\User;
use Enlivy\UserAddress;
use Enlivy\UserRole;
use Enlivy\UserRoleAbility;
use Enlivy\UserToken;
use Enlivy\Webhook;

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
        // Non-org-scoped
        'organization' => Organization::class,
        'user' => User::class,
        'user_token' => UserToken::class,
        'ai_agent' => AiAgent::class,
        'oauth_client' => OAuthClient::class,
        'oauth_authorization' => OAuthAuthorization::class,
        'oauth_token' => OAuthToken::class,
        'invitation_code' => InvitationCode::class,

        // Org-scoped: CRM
        'prospect' => Prospect::class,
        'prospect_activity' => ProspectActivity::class,
        'prospect_status' => ProspectStatus::class,
        'project' => Project::class,
        'project_member' => ProjectMember::class,

        // Org-scoped: Users & Roles
        'organization_user' => OrganizationUser::class,
        'user_role' => UserRole::class,
        'user_role_ability' => UserRoleAbility::class,
        'user_address' => UserAddress::class,

        // Org-scoped: Accounting
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

        // Org-scoped: Contracts
        'contract' => Contract::class,
        'contract_prefix' => ContractPrefix::class,
        'contract_status' => ContractStatus::class,
        'contract_signature' => ContractSignature::class,

        // Org-scoped: Tax
        'tax_class' => TaxClass::class,
        'tax_rate' => TaxRate::class,
        'tax_type' => TaxType::class,
        'tax_filing_jurisdiction' => TaxFilingJurisdiction::class,

        // Org-scoped: Payroll
        'payslip_schema' => PayslipSchema::class,
        'payslip' => Payslip::class,

        // Org-scoped: Reports
        'report' => Report::class,
        'report_schema' => ReportSchema::class,
        'report_schema_field' => ReportSchemaField::class,

        // Org-scoped: Content & Files
        'file' => File::class,
        'guideline' => Guideline::class,
        'playbook' => Playbook::class,
        'reusable_content' => ReusableContent::class,

        // Org-scoped: Tasks
        'task' => Task::class,
        'task_status' => TaskStatus::class,

        // Org-scoped: Settings & Tags
        'tag' => Tag::class,
        'notification' => Notification::class,

        // Org-scoped: Webhooks & Export
        'webhook' => Webhook::class,
        'export_data' => ExportData::class,

        // Org-scoped: Membership & Billing
        'offer' => Offer::class,
        'proposal' => Proposal::class,

        // Org-scoped: API & Integrations
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

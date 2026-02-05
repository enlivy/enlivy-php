<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\Service\BankAccount\BankAccountDataService;
use Enlivy\Service\BankAccount\BankAccountService;
use Enlivy\Service\BankAccount\BankTransactionCostTypeService;
use Enlivy\Service\BankAccount\BankTransactionService;
use Enlivy\Service\Contract\ContractPrefixService;
use Enlivy\Service\Contract\ContractService;
use Enlivy\Service\Contract\ContractSignatureService;
use Enlivy\Service\Contract\ContractStatusService;
use Enlivy\Service\Invoice\InvoiceNetworkExchangeService;
use Enlivy\Service\Invoice\InvoiceNotificationLogService;
use Enlivy\Service\Invoice\InvoicePrefixService;
use Enlivy\Service\Invoice\InvoiceService;
use Enlivy\Service\OAuth\OAuthAuthorizationService;
use Enlivy\Service\OAuth\OAuthClientService;
use Enlivy\Service\OAuth\OAuthTokenService;
use Enlivy\Service\Payslip\PayslipSchemaService;
use Enlivy\Service\Payslip\PayslipService;
use Enlivy\Service\Project\ProjectMemberService;
use Enlivy\Service\Project\ProjectPermissionGuidelineService;
use Enlivy\Service\Project\ProjectPermissionPlaybookService;
use Enlivy\Service\Project\ProjectPermissionProspectService;
use Enlivy\Service\Project\ProjectPermissionReportService;
use Enlivy\Service\Project\ProjectProspectStatusService;
use Enlivy\Service\Project\ProjectService;
use Enlivy\Service\Prospect\ProspectActivityService;
use Enlivy\Service\Prospect\ProspectService;
use Enlivy\Service\Prospect\ProspectStatusService;
use Enlivy\Service\Receipt\ReceiptPrefixService;
use Enlivy\Service\Receipt\ReceiptService;
use Enlivy\Service\Report\ReportSchemaFieldService;
use Enlivy\Service\Report\ReportSchemaService;
use Enlivy\Service\Report\ReportService;
use Enlivy\Service\ResourceBundle\ResourceBundlePermissionGuidelineService;
use Enlivy\Service\ResourceBundle\ResourceBundlePermissionPlaybookService;
use Enlivy\Service\ResourceBundle\ResourceBundlePermissionReportService;
use Enlivy\Service\ResourceBundle\ResourceBundleService;
use Enlivy\Service\ServiceIntegration\ServiceIntegrationAnafService;
use Enlivy\Service\ServiceIntegration\ServiceIntegrationService;
use Enlivy\Service\ServiceIntegration\ServiceIntegrationStripeService;
use Enlivy\Service\Task\TaskService;
use Enlivy\Service\Task\TaskStatusService;
use Enlivy\Service\Tax\TaxClassService;
use Enlivy\Service\Tax\TaxFilingJurisdictionService;
use Enlivy\Service\Tax\TaxRateService;
use Enlivy\Service\Tax\TaxTypeService;
use Enlivy\Service\User\UserAddressService;
use Enlivy\Service\User\UserClientPortalSessionService;
use Enlivy\Service\User\UserOrganizationSettingService;
use Enlivy\Service\User\UserPortalDomainService;
use Enlivy\Service\User\UserRoleAbilityService;
use Enlivy\Service\User\UserRoleService;
use Enlivy\Service\User\UserService;
use Enlivy\Service\User\UserTokenService;

class CoreServiceFactory extends AbstractServiceFactory
{
    protected function getServiceMap(): array
    {
        return [
            // Non-org-scoped
            'authentication' => AuthenticationService::class,
            'organizations' => OrganizationService::class,
            'users' => UserService::class,
            'userTokens' => UserTokenService::class,
            'aiAgents' => AiAgentService::class,
            'oauthClients' => OAuthClientService::class,
            'oauthAuthorizations' => OAuthAuthorizationService::class,
            'oauthTokens' => OAuthTokenService::class,
            'invitationCodes' => InvitationCodeService::class,
            'serviceIntegration' => ServiceIntegrationService::class,
            'frontend' => FrontendService::class,
            'userOrganizationSettings' => UserOrganizationSettingService::class,

            // Org-scoped: CRM
            'prospects' => ProspectService::class,
            'prospectActivities' => ProspectActivityService::class,
            'prospectStatuses' => ProspectStatusService::class,
            'projects' => ProjectService::class,
            'projectMembers' => ProjectMemberService::class,
            'projectPermissionProspects' => ProjectPermissionProspectService::class,
            'projectPermissionGuidelines' => ProjectPermissionGuidelineService::class,
            'projectPermissionPlaybooks' => ProjectPermissionPlaybookService::class,
            'projectPermissionReports' => ProjectPermissionReportService::class,
            'projectProspectStatuses' => ProjectProspectStatusService::class,

            // Org-scoped: Users & Roles
            'organizationUsers' => OrganizationUserService::class,
            'userRoles' => UserRoleService::class,
            'userRoleAbilities' => UserRoleAbilityService::class,
            'userAddresses' => UserAddressService::class,

            // Org-scoped: Accounting
            'invoices' => InvoiceService::class,
            'invoicePrefixes' => InvoicePrefixService::class,
            'invoiceNotificationLogs' => InvoiceNotificationLogService::class,
            'invoiceNetworkExchanges' => InvoiceNetworkExchangeService::class,
            'receipts' => ReceiptService::class,
            'receiptPrefixes' => ReceiptPrefixService::class,
            'products' => ProductService::class,
            'bankAccounts' => BankAccountService::class,
            'bankTransactions' => BankTransactionService::class,
            'bankTransactionCostTypes' => BankTransactionCostTypeService::class,
            'bankAccountData' => BankAccountDataService::class,
            'billingSchedules' => BillingScheduleService::class,

            // Org-scoped: Contracts
            'contracts' => ContractService::class,
            'contractPrefixes' => ContractPrefixService::class,
            'contractStatuses' => ContractStatusService::class,
            'contractSignatures' => ContractSignatureService::class,

            // Org-scoped: Tax
            'taxClasses' => TaxClassService::class,
            'taxRates' => TaxRateService::class,
            'taxTypes' => TaxTypeService::class,
            'taxFilingJurisdictions' => TaxFilingJurisdictionService::class,

            // Org-scoped: Payroll
            'payslipSchemas' => PayslipSchemaService::class,
            'payslips' => PayslipService::class,

            // Org-scoped: Reports
            'reports' => ReportService::class,
            'reportSchemas' => ReportSchemaService::class,
            'reportSchemaFields' => ReportSchemaFieldService::class,

            // Org-scoped: Content & Files
            'files' => FileService::class,
            'guidelines' => GuidelineService::class,
            'playbooks' => PlaybookService::class,
            'reusableContent' => ReusableContentService::class,
            'emailPreviews' => EmailPreviewService::class,

            // Org-scoped: Tasks
            'tasks' => TaskService::class,
            'taskStatuses' => TaskStatusService::class,

            // Org-scoped: Settings & Tags
            'tags' => TagService::class,
            'settings' => SettingService::class,
            'preferences' => PreferenceService::class,
            'notifications' => NotificationService::class,

            // Org-scoped: Webhooks & Export
            'webhooks' => WebhookService::class,
            'exportData' => ExportDataService::class,

            // Org-scoped: Portal
            'userClientPortalSessions' => UserClientPortalSessionService::class,
            'userPortalDomain' => UserPortalDomainService::class,

            // Org-scoped: Membership & Billing
            'membership' => MembershipService::class,
            'offers' => OfferService::class,
            'proposals' => ProposalService::class,

            // Org-scoped: Search & AI
            'search' => SearchService::class,
            'match' => MatchService::class,
            'misc' => MiscService::class,
            'analytics' => AnalyticsService::class,

            // Org-scoped: API & Integrations
            'apiCredentials' => ApiCredentialService::class,
            'resourceBundles' => ResourceBundleService::class,
            'resourceBundlePermissionGuidelines' => ResourceBundlePermissionGuidelineService::class,
            'resourceBundlePermissionPlaybooks' => ResourceBundlePermissionPlaybookService::class,
            'resourceBundlePermissionReports' => ResourceBundlePermissionReportService::class,
            'stripeWebhookCallbacks' => StripeWebhookCallbackService::class,
            'serviceIntegrationAnaf' => ServiceIntegrationAnafService::class,
            'serviceIntegrationStripe' => ServiceIntegrationStripeService::class,
        ];
    }
}

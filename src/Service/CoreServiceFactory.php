<?php

declare(strict_types=1);

namespace Enlivy\Service;

use Enlivy\Service\OAuth\OAuthAuthorizationService;
use Enlivy\Service\OAuth\OAuthClientService;
use Enlivy\Service\OAuth\OAuthTokenService;
use Enlivy\Service\Organization\AnalyticsService;
use Enlivy\Service\Organization\ApiCredentialService;
use Enlivy\Service\Organization\BankAccount\BankAccountDataService;
use Enlivy\Service\Organization\BankAccount\BankAccountService;
use Enlivy\Service\Organization\BankAccount\BankTransactionCostTypeService;
use Enlivy\Service\Organization\BankAccount\BankTransactionService;
use Enlivy\Service\Organization\BillingScheduleService;
use Enlivy\Service\Organization\Contract\ContractPrefixService;
use Enlivy\Service\Organization\Contract\ContractService;
use Enlivy\Service\Organization\Contract\ContractSignatureService;
use Enlivy\Service\Organization\Contract\ContractStatusService;
use Enlivy\Service\Organization\EmailPreviewService;
use Enlivy\Service\Organization\ExportDataService;
use Enlivy\Service\Organization\FileService;
use Enlivy\Service\Organization\GuidelineService;
use Enlivy\Service\Organization\Invoice\InvoiceNetworkExchangeService;
use Enlivy\Service\Organization\Invoice\InvoiceNotificationLogService;
use Enlivy\Service\Organization\Invoice\InvoicePrefixService;
use Enlivy\Service\Organization\Invoice\InvoiceService;
use Enlivy\Service\Organization\MatchService;
use Enlivy\Service\Organization\MembershipService;
use Enlivy\Service\Organization\MiscService;
use Enlivy\Service\Organization\NotificationService;
use Enlivy\Service\Organization\OfferService;
use Enlivy\Service\Organization\Payslip\PayslipSchemaService;
use Enlivy\Service\Organization\Payslip\PayslipService;
use Enlivy\Service\Organization\PlaybookService;
use Enlivy\Service\Organization\PreferenceService;
use Enlivy\Service\Organization\ProductService;
use Enlivy\Service\Organization\Project\ProjectMemberService;
use Enlivy\Service\Organization\Project\ProjectPermissionGuidelineService;
use Enlivy\Service\Organization\Project\ProjectPermissionPlaybookService;
use Enlivy\Service\Organization\Project\ProjectPermissionProspectService;
use Enlivy\Service\Organization\Project\ProjectPermissionReportService;
use Enlivy\Service\Organization\Project\ProjectProspectStatusService;
use Enlivy\Service\Organization\Project\ProjectService;
use Enlivy\Service\Organization\ProposalService;
use Enlivy\Service\Organization\Prospect\ProspectActivityService;
use Enlivy\Service\Organization\Prospect\ProspectService;
use Enlivy\Service\Organization\Prospect\ProspectStatusService;
use Enlivy\Service\Organization\Receipt\ReceiptPrefixService;
use Enlivy\Service\Organization\Receipt\ReceiptService;
use Enlivy\Service\Organization\Report\ReportSchemaFieldService;
use Enlivy\Service\Organization\Report\ReportSchemaService;
use Enlivy\Service\Organization\Report\ReportService;
use Enlivy\Service\Organization\ResourceBundle\ResourceBundlePermissionGuidelineService;
use Enlivy\Service\Organization\ResourceBundle\ResourceBundlePermissionPlaybookService;
use Enlivy\Service\Organization\ResourceBundle\ResourceBundlePermissionReportService;
use Enlivy\Service\Organization\ResourceBundle\ResourceBundleService;
use Enlivy\Service\Organization\ReusableContentService;
use Enlivy\Service\Organization\SearchService;
use Enlivy\Service\Organization\ServiceIntegration\ServiceIntegrationAnafService;
use Enlivy\Service\Organization\ServiceIntegration\ServiceIntegrationService;
use Enlivy\Service\Organization\ServiceIntegration\ServiceIntegrationStripeService;
use Enlivy\Service\Organization\SettingService;
use Enlivy\Service\Organization\StripeWebhookCallbackService;
use Enlivy\Service\Organization\TagService;
use Enlivy\Service\Organization\Task\TaskService;
use Enlivy\Service\Organization\Task\TaskStatusService;
use Enlivy\Service\Organization\Tax\TaxClassService;
use Enlivy\Service\Organization\Tax\TaxFilingJurisdictionService;
use Enlivy\Service\Organization\Tax\TaxRateService;
use Enlivy\Service\Organization\Tax\TaxTypeService;
use Enlivy\Service\Organization\UserAddressService;
use Enlivy\Service\Organization\UserClientPortalSessionService;
use Enlivy\Service\Organization\UserOrganizationSettingService;
use Enlivy\Service\Organization\UserPortalDomainService;
use Enlivy\Service\Organization\UserRoleAbilityService;
use Enlivy\Service\Organization\UserRoleService;
use Enlivy\Service\Organization\UserService as OrganizationUserService;
use Enlivy\Service\Organization\WebhookService;

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

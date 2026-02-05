<?php

declare(strict_types=1);

namespace Enlivy;

use Enlivy\Service;

/**
 * Enlivy API Client.
 *
 * Global services:
 * @property Service\AuthenticationService $authentication
 * @property Service\OrganizationService $organizations
 * @property Service\UserService $users
 * @property Service\UserTokenService $userTokens
 * @property Service\AiAgentService $aiAgents
 * @property Service\InvitationCodeService $invitationCodes
 * @property Service\FrontendService $frontend
 * @property Service\OAuth\OAuthClientService $oauthClients
 * @property Service\OAuth\OAuthAuthorizationService $oauthAuthorizations
 * @property Service\OAuth\OAuthTokenService $oauthTokens
 *
 * Organization-scoped services - Users & Roles:
 * @property Service\Organization\UserService $organizationUsers
 * @property Service\Organization\UserRoleService $userRoles
 * @property Service\Organization\UserRoleAbilityService $userRoleAbilities
 * @property Service\Organization\UserAddressService $userAddresses
 * @property Service\Organization\UserOrganizationSettingService $userOrganizationSettings
 *
 * Organization-scoped services - CRM:
 * @property Service\Organization\Prospect\ProspectService $prospects
 * @property Service\Organization\Prospect\ProspectActivityService $prospectActivities
 * @property Service\Organization\Prospect\ProspectStatusService $prospectStatuses
 * @property Service\Organization\Project\ProjectService $projects
 * @property Service\Organization\Project\ProjectMemberService $projectMembers
 * @property Service\Organization\Project\ProjectPermissionProspectService $projectPermissionProspects
 * @property Service\Organization\Project\ProjectPermissionGuidelineService $projectPermissionGuidelines
 * @property Service\Organization\Project\ProjectPermissionPlaybookService $projectPermissionPlaybooks
 * @property Service\Organization\Project\ProjectPermissionReportService $projectPermissionReports
 * @property Service\Organization\Project\ProjectProspectStatusService $projectProspectStatuses
 *
 * Organization-scoped services - Accounting:
 * @property Service\Organization\Invoice\InvoiceService $invoices
 * @property Service\Organization\Invoice\InvoicePrefixService $invoicePrefixes
 * @property Service\Organization\Invoice\InvoiceNotificationLogService $invoiceNotificationLogs
 * @property Service\Organization\Invoice\InvoiceNetworkExchangeService $invoiceNetworkExchanges
 * @property Service\Organization\Receipt\ReceiptService $receipts
 * @property Service\Organization\Receipt\ReceiptPrefixService $receiptPrefixes
 * @property Service\Organization\ProductService $products
 * @property Service\Organization\BankAccount\BankAccountService $bankAccounts
 * @property Service\Organization\BankAccount\BankTransactionService $bankTransactions
 * @property Service\Organization\BankAccount\BankTransactionCostTypeService $bankTransactionCostTypes
 * @property Service\Organization\BankAccount\BankAccountDataService $bankAccountData
 * @property Service\Organization\BillingScheduleService $billingSchedules
 *
 * Organization-scoped services - Contracts:
 * @property Service\Organization\Contract\ContractService $contracts
 * @property Service\Organization\Contract\ContractPrefixService $contractPrefixes
 * @property Service\Organization\Contract\ContractStatusService $contractStatuses
 * @property Service\Organization\Contract\ContractSignatureService $contractSignatures
 *
 * Organization-scoped services - Tax:
 * @property Service\Organization\Tax\TaxClassService $taxClasses
 * @property Service\Organization\Tax\TaxRateService $taxRates
 * @property Service\Organization\Tax\TaxTypeService $taxTypes
 * @property Service\Organization\Tax\TaxFilingJurisdictionService $taxFilingJurisdictions
 *
 * Organization-scoped services - Payroll:
 * @property Service\Organization\Payslip\PayslipSchemaService $payslipSchemas
 * @property Service\Organization\Payslip\PayslipService $payslips
 *
 * Organization-scoped services - Reports:
 * @property Service\Organization\Report\ReportService $reports
 * @property Service\Organization\Report\ReportSchemaService $reportSchemas
 * @property Service\Organization\Report\ReportSchemaFieldService $reportSchemaFields
 *
 * Organization-scoped services - Content & Files:
 * @property Service\Organization\FileService $files
 * @property Service\Organization\GuidelineService $guidelines
 * @property Service\Organization\PlaybookService $playbooks
 * @property Service\Organization\ReusableContentService $reusableContent
 * @property Service\Organization\EmailPreviewService $emailPreviews
 *
 * Organization-scoped services - Tasks:
 * @property Service\Organization\Task\TaskService $tasks
 * @property Service\Organization\Task\TaskStatusService $taskStatuses
 *
 * Organization-scoped services - Settings & Tags:
 * @property Service\Organization\TagService $tags
 * @property Service\Organization\SettingService $settings
 * @property Service\Organization\PreferenceService $preferences
 * @property Service\Organization\NotificationService $notifications
 *
 * Organization-scoped services - Webhooks & Export:
 * @property Service\Organization\WebhookService $webhooks
 * @property Service\Organization\ExportDataService $exportData
 *
 * Organization-scoped services - Portal:
 * @property Service\Organization\UserClientPortalSessionService $userClientPortalSessions
 * @property Service\Organization\UserPortalDomainService $userPortalDomain
 *
 * Organization-scoped services - Membership & Billing:
 * @property Service\Organization\MembershipService $membership
 * @property Service\Organization\OfferService $offers
 * @property Service\Organization\ProposalService $proposals
 *
 * Organization-scoped services - Search & AI:
 * @property Service\Organization\SearchService $search
 * @property Service\Organization\MatchService $match
 * @property Service\Organization\MiscService $misc
 * @property Service\Organization\AnalyticsService $analytics
 *
 * Organization-scoped services - API & Integrations:
 * @property Service\Organization\ApiCredentialService $apiCredentials
 * @property Service\Organization\ResourceBundle\ResourceBundleService $resourceBundles
 * @property Service\Organization\ResourceBundle\ResourceBundlePermissionGuidelineService $resourceBundlePermissionGuidelines
 * @property Service\Organization\ResourceBundle\ResourceBundlePermissionPlaybookService $resourceBundlePermissionPlaybooks
 * @property Service\Organization\ResourceBundle\ResourceBundlePermissionReportService $resourceBundlePermissionReports
 * @property Service\Organization\StripeWebhookCallbackService $stripeWebhookCallbacks
 * @property Service\Organization\ServiceIntegration\ServiceIntegrationService $serviceIntegration
 * @property Service\Organization\ServiceIntegration\ServiceIntegrationAnafService $serviceIntegrationAnaf
 * @property Service\Organization\ServiceIntegration\ServiceIntegrationStripeService $serviceIntegrationStripe
 */
class EnlivyClient extends BaseEnlivyClient {}

<?php

declare(strict_types=1);

namespace Enlivy;

use Enlivy\Service;

/**
 * Enlivy API Client.
 *
 * Non-org-scoped services:
 * @property Service\AuthenticationService $authentication
 * @property Service\OrganizationService $organizations
 * @property Service\User\UserService $users
 * @property Service\User\UserTokenService $userTokens
 * @property Service\AiAgentService $aiAgents (read-only + run for non-admins)
 * @property Service\OAuth\OAuthClientService $oauthClients
 * @property Service\OAuth\OAuthAuthorizationService $oauthAuthorizations
 * @property Service\OAuth\OAuthTokenService $oauthTokens
 * @property Service\InvitationCodeService $invitationCodes
 * @property Service\ServiceIntegration\ServiceIntegrationService $serviceIntegration
 * @property Service\FrontendService $frontend
 * @property Service\User\UserOrganizationSettingService $userOrganizationSettings
 *
 * Org-scoped services - CRM:
 * @property Service\Prospect\ProspectService $prospects
 * @property Service\Prospect\ProspectActivityService $prospectActivities
 * @property Service\Prospect\ProspectStatusService $prospectStatuses
 * @property Service\Project\ProjectService $projects
 * @property Service\Project\ProjectMemberService $projectMembers
 * @property Service\Project\ProjectPermissionProspectService $projectPermissionProspects
 * @property Service\Project\ProjectPermissionGuidelineService $projectPermissionGuidelines
 * @property Service\Project\ProjectPermissionPlaybookService $projectPermissionPlaybooks
 * @property Service\Project\ProjectPermissionReportService $projectPermissionReports
 * @property Service\Project\ProjectProspectStatusService $projectProspectStatuses
 *
 * Org-scoped services - Users & Roles:
 * @property Service\OrganizationUserService $organizationUsers
 * @property Service\User\UserRoleService $userRoles
 * @property Service\User\UserRoleAbilityService $userRoleAbilities
 * @property Service\User\UserAddressService $userAddresses
 *
 * Org-scoped services - Accounting:
 * @property Service\Invoice\InvoiceService $invoices
 * @property Service\Invoice\InvoicePrefixService $invoicePrefixes
 * @property Service\Invoice\InvoiceNotificationLogService $invoiceNotificationLogs
 * @property Service\Invoice\InvoiceNetworkExchangeService $invoiceNetworkExchanges
 * @property Service\Receipt\ReceiptService $receipts
 * @property Service\Receipt\ReceiptPrefixService $receiptPrefixes
 * @property Service\ProductService $products
 * @property Service\BankAccount\BankAccountService $bankAccounts
 * @property Service\BankAccount\BankTransactionService $bankTransactions
 * @property Service\BankAccount\BankTransactionCostTypeService $bankTransactionCostTypes
 * @property Service\BankAccount\BankAccountDataService $bankAccountData
 * @property Service\BillingScheduleService $billingSchedules
 *
 * Org-scoped services - Contracts:
 * @property Service\Contract\ContractService $contracts
 * @property Service\Contract\ContractPrefixService $contractPrefixes
 * @property Service\Contract\ContractStatusService $contractStatuses
 * @property Service\Contract\ContractSignatureService $contractSignatures
 *
 * Org-scoped services - Tax:
 * @property Service\Tax\TaxClassService $taxClasses
 * @property Service\Tax\TaxRateService $taxRates
 * @property Service\Tax\TaxTypeService $taxTypes
 * @property Service\Tax\TaxFilingJurisdictionService $taxFilingJurisdictions
 *
 * Org-scoped services - Payroll:
 * @property Service\Payslip\PayslipSchemaService $payslipSchemas
 * @property Service\Payslip\PayslipService $payslips
 *
 * Org-scoped services - Reports:
 * @property Service\Report\ReportService $reports
 * @property Service\Report\ReportSchemaService $reportSchemas
 * @property Service\Report\ReportSchemaFieldService $reportSchemaFields
 *
 * Org-scoped services - Content & Files:
 * @property Service\FileService $files
 * @property Service\GuidelineService $guidelines
 * @property Service\PlaybookService $playbooks
 * @property Service\ReusableContentService $reusableContent
 * @property Service\EmailPreviewService $emailPreviews
 *
 * Org-scoped services - Tasks:
 * @property Service\Task\TaskService $tasks
 * @property Service\Task\TaskStatusService $taskStatuses
 *
 * Org-scoped services - Settings & Tags:
 * @property Service\TagService $tags
 * @property Service\SettingService $settings
 * @property Service\PreferenceService $preferences
 * @property Service\NotificationService $notifications
 *
 * Org-scoped services - Webhooks & Export:
 * @property Service\WebhookService $webhooks
 * @property Service\ExportDataService $exportData
 *
 * Org-scoped services - Portal:
 * @property Service\User\UserClientPortalSessionService $userClientPortalSessions
 * @property Service\User\UserPortalDomainService $userPortalDomain
 *
 * Org-scoped services - Membership & Billing:
 * @property Service\MembershipService $membership
 * @property Service\OfferService $offers
 * @property Service\ProposalService $proposals
 *
 * Org-scoped services - Search & AI:
 * @property Service\SearchService $search
 * @property Service\MatchService $match
 * @property Service\MiscService $misc
 * @property Service\AnalyticsService $analytics
 *
 * Org-scoped services - API & Integrations:
 * @property Service\ApiCredentialService $apiCredentials
 * @property Service\ResourceBundle\ResourceBundleService $resourceBundles
 * @property Service\ResourceBundle\ResourceBundlePermissionGuidelineService $resourceBundlePermissionGuidelines
 * @property Service\ResourceBundle\ResourceBundlePermissionPlaybookService $resourceBundlePermissionPlaybooks
 * @property Service\ResourceBundle\ResourceBundlePermissionReportService $resourceBundlePermissionReports
 * @property Service\StripeWebhookCallbackService $stripeWebhookCallbacks
 * @property Service\ServiceIntegration\ServiceIntegrationAnafService $serviceIntegrationAnaf
 * @property Service\ServiceIntegration\ServiceIntegrationStripeService $serviceIntegrationStripe
 */
class EnlivyClient extends BaseEnlivyClient {}

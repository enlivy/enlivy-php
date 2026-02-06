<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Tests\Integration\IntegrationTestCase;

/**
 * Integration tests that verify all AVAILABLE_INCLUDES constants match the real API.
 *
 * Each test fetches a record and retrieves it with ALL available includes.
 * If a constant is wrong (include doesn't exist on the API), the API returns a 422.
 *
 * Run with:
 *   ENLIVY_API_KEY="1|xxx" ENLIVY_ORGANIZATION_ID="org_xxx" ./vendor/bin/phpunit -c phpunit.integration.xml tests/Integration/View/IncludeValidationTest.php
 */
class IncludeValidationTest extends IntegrationTestCase
{
    // =========================================================================
    // Helper
    // =========================================================================

    /**
     * List a resource and retrieve the first record with all available includes.
     * Validates that the API accepts every include defined in the service constant.
     */
    private function assertAllIncludesWork(string $serviceName, ?string $idPrefix = null): void
    {
        $service = $this->getClient()->{$serviceName};

        $availableIncludes = $service::AVAILABLE_INCLUDES;
        $this->assertNotEmpty($availableIncludes, "Service {$serviceName} should have AVAILABLE_INCLUDES");

        // List first record
        $collection = $service->list(['per_page' => 1]);
        $this->assertInstanceOf(Collection::class, $collection);

        if (count($collection->data) === 0) {
            $this->markTestSkipped("No {$serviceName} records available for include testing");
        }

        $record = $collection->data[0];

        if ($idPrefix !== null) {
            $this->assertStringStartsWith($idPrefix, $record->id);
        }

        // Retrieve with ALL includes at once
        $result = $service->retrieve($record->id, [
            'include' => implode(',', $availableIncludes),
        ]);

        $this->assertNotNull($result->id, "Retrieve with all includes should return a valid record for {$serviceName}");
    }

    /**
     * Test that invalid includes throw an InvalidArgumentException at SDK level.
     */
    private function assertInvalidIncludeThrows(string $serviceName): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->getClient()->{$serviceName}->list([
            'include' => ['__nonexistent_include__'],
        ]);
    }

    // =========================================================================
    // Invoicing
    // =========================================================================

    public function testInvoiceAllIncludes(): void
    {
        $this->assertAllIncludesWork('invoices', 'org_inv_');
    }

    public function testInvoiceInvalidIncludeThrows(): void
    {
        $this->assertInvalidIncludeThrows('invoices');
    }

    public function testInvoicePrefixAllIncludes(): void
    {
        $this->assertAllIncludesWork('invoicePrefixes');
    }

    public function testReceiptAllIncludes(): void
    {
        $this->assertAllIncludesWork('receipts');
    }

    public function testReceiptPrefixAllIncludes(): void
    {
        $this->assertAllIncludesWork('receiptPrefixes');
    }

    public function testInvoiceNetworkExchangeAllIncludes(): void
    {
        try {
            $this->assertAllIncludesWork('invoiceNetworkExchanges');
        } catch (\Enlivy\Exception\ForbiddenException) {
            $this->markTestSkipped('Insufficient permissions for invoice network exchanges');
        }
    }

    // =========================================================================
    // CRM
    // =========================================================================

    public function testProspectAllIncludes(): void
    {
        $this->assertAllIncludesWork('prospects', 'org_pros_');
    }

    public function testProspectInvalidIncludeThrows(): void
    {
        $this->assertInvalidIncludeThrows('prospects');
    }

    public function testProspectStatusAllIncludes(): void
    {
        $this->assertAllIncludesWork('prospectStatuses');
    }

    // =========================================================================
    // Contracts
    // =========================================================================

    public function testContractAllIncludes(): void
    {
        $this->assertAllIncludesWork('contracts', 'org_cont_');
    }

    public function testContractStatusAllIncludes(): void
    {
        $this->assertAllIncludesWork('contractStatuses');
    }

    public function testContractPrefixAllIncludes(): void
    {
        $this->assertAllIncludesWork('contractPrefixes');
    }

    // =========================================================================
    // Users & Roles
    // =========================================================================

    public function testOrganizationUserAllIncludes(): void
    {
        $this->assertAllIncludesWork('organizationUsers', 'org_user_');
    }

    public function testUserRoleAllIncludes(): void
    {
        $this->assertAllIncludesWork('userRoles');
    }

    // =========================================================================
    // Projects
    // =========================================================================

    public function testProjectAllIncludes(): void
    {
        $this->assertAllIncludesWork('projects');
    }

    // =========================================================================
    // Products
    // =========================================================================

    public function testProductAllIncludes(): void
    {
        $this->assertAllIncludesWork('products');
    }

    // =========================================================================
    // Banking
    // =========================================================================

    public function testBankAccountAllIncludes(): void
    {
        $this->assertAllIncludesWork('bankAccounts');
    }

    public function testBankTransactionAllIncludes(): void
    {
        $this->assertAllIncludesWork('bankTransactions');
    }

    public function testBankTransactionCostTypeAllIncludes(): void
    {
        $this->assertAllIncludesWork('bankTransactionCostTypes');
    }

    // =========================================================================
    // Tax
    // =========================================================================

    public function testTaxClassAllIncludes(): void
    {
        $this->assertAllIncludesWork('taxClasses');
    }

    public function testTaxRateAllIncludes(): void
    {
        $this->assertAllIncludesWork('taxRates');
    }

    public function testTaxTypeAllIncludes(): void
    {
        $this->assertAllIncludesWork('taxTypes');
    }

    // =========================================================================
    // Payroll
    // =========================================================================

    public function testPayslipAllIncludes(): void
    {
        $this->assertAllIncludesWork('payslips');
    }

    public function testPayslipSchemaAllIncludes(): void
    {
        $this->assertAllIncludesWork('payslipSchemas');
    }

    // =========================================================================
    // Reports
    // =========================================================================

    public function testReportAllIncludes(): void
    {
        $this->assertAllIncludesWork('reports');
    }

    public function testReportSchemaAllIncludes(): void
    {
        $this->assertAllIncludesWork('reportSchemas');
    }

    // =========================================================================
    // Content & Files
    // =========================================================================

    public function testFileAllIncludes(): void
    {
        $this->assertAllIncludesWork('files');
    }

    public function testGuidelineAllIncludes(): void
    {
        $this->assertAllIncludesWork('guidelines');
    }

    public function testPlaybookAllIncludes(): void
    {
        $this->assertAllIncludesWork('playbooks');
    }

    // =========================================================================
    // Tasks
    // =========================================================================

    public function testTaskAllIncludes(): void
    {
        $this->assertAllIncludesWork('tasks');
    }

    public function testTaskStatusAllIncludes(): void
    {
        $this->assertAllIncludesWork('taskStatuses');
    }

    // =========================================================================
    // Tags & Webhooks
    // =========================================================================

    public function testTagAllIncludes(): void
    {
        $this->assertAllIncludesWork('tags');
    }

    public function testWebhookAllIncludes(): void
    {
        $this->assertAllIncludesWork('webhooks');
    }

    // =========================================================================
    // Billing & Offers
    // =========================================================================

    public function testBillingScheduleAllIncludes(): void
    {
        $this->assertAllIncludesWork('billingSchedules');
    }

    public function testOfferAllIncludes(): void
    {
        $this->assertAllIncludesWork('offers');
    }

    public function testProposalAllIncludes(): void
    {
        $this->assertAllIncludesWork('proposals');
    }

    // =========================================================================
    // Global Services
    // =========================================================================

    public function testOrganizationAllIncludes(): void
    {
        $service = $this->getClient()->organizations;

        $collection = $service->list(['per_page' => 1]);
        if (count($collection->data) === 0) {
            $this->markTestSkipped('No organizations available');
        }

        $orgId = $collection->data[0]->id;

        // Use list with filter instead of retrieve, since OrganizationService::retrieve()
        // has a pre-existing return type issue unrelated to includes.
        $result = $service->list([
            'per_page' => 1,
            'include' => implode(',', $service::AVAILABLE_INCLUDES),
        ]);

        $this->assertInstanceOf(\Enlivy\Collection::class, $result);
        $this->assertGreaterThan(0, count($result->data));
    }

    public function testOrganizationInvalidIncludeThrows(): void
    {
        $this->assertInvalidIncludeThrows('organizations');
    }
}

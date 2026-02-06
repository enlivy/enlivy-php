<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Tests\Integration\IntegrationTestCase;

/**
 * Integration tests that verify AVAILABLE_FILTERS constants work with the real API.
 *
 * Tests that:
 * 1. Resource-specific filters are accepted by the API without errors
 * 2. Global filters (per_page, order_by) work on all endpoints
 * 3. Invalid filters are caught client-side before reaching the API
 *
 * Run with:
 *   ENLIVY_API_KEY="1|xxx" ENLIVY_ORGANIZATION_ID="org_xxx" ./vendor/bin/phpunit -c phpunit.integration.xml tests/Integration/View/FilterValidationTest.php
 */
class FilterValidationTest extends IntegrationTestCase
{
    // =========================================================================
    // Helpers
    // =========================================================================

    /**
     * List a resource with global filters and verify the API responds.
     */
    private function assertGlobalFiltersWork(string $serviceName): void
    {
        $service = $this->getClient()->{$serviceName};

        $collection = $service->list([
            'per_page' => 2,
            'order_by' => 'created_at',
            'order' => 'desc',
        ]);

        $this->assertInstanceOf(Collection::class, $collection);
    }

    /**
     * List a resource with resource-specific filters and verify the API responds.
     */
    private function assertResourceFiltersWork(string $serviceName, array $filters): void
    {
        $service = $this->getClient()->{$serviceName};

        $collection = $service->list(array_merge(
            ['per_page' => 1],
            $filters,
        ));

        $this->assertInstanceOf(Collection::class, $collection);
    }

    /**
     * Test that an unknown filter is caught by the SDK before hitting the API.
     */
    private function assertInvalidFilterThrows(string $serviceName): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->getClient()->{$serviceName}->list([
            '__nonexistent_filter__' => 'value',
        ]);
    }

    // =========================================================================
    // Invoicing
    // =========================================================================

    public function testInvoiceGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('invoices');
    }

    public function testInvoiceDirectionFilter(): void
    {
        $this->assertResourceFiltersWork('invoices', [
            'direction' => 'outbound',
        ]);
    }

    public function testInvoiceStatusFilter(): void
    {
        $this->assertResourceFiltersWork('invoices', [
            'status' => 'draft',
        ]);
    }

    public function testInvoiceDateRangeFilters(): void
    {
        $this->assertResourceFiltersWork('invoices', [
            'created_at_from' => '2024-01-01T00:00:00Z',
            'created_at_to' => '2026-12-31T23:59:59Z',
        ]);
    }

    public function testInvoiceInvalidFilterThrows(): void
    {
        $this->assertInvalidFilterThrows('invoices');
    }

    public function testReceiptGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('receipts');
    }

    public function testReceiptDirectionFilter(): void
    {
        $this->assertResourceFiltersWork('receipts', [
            'direction' => 'outbound',
        ]);
    }

    public function testInvoicePrefixTypeFilter(): void
    {
        $this->assertResourceFiltersWork('invoicePrefixes', [
            'type' => 'standard',
        ]);
    }

    public function testInvoiceNotificationLogGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('invoiceNotificationLogs');
    }

    // =========================================================================
    // CRM
    // =========================================================================

    public function testProspectGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('prospects');
    }

    public function testProspectSourceTypeFilter(): void
    {
        $this->assertResourceFiltersWork('prospects', [
            'source_type' => 'inbound',
        ]);
    }

    public function testProspectDateRangeFilters(): void
    {
        $this->assertResourceFiltersWork('prospects', [
            'created_at_from' => '2024-01-01T00:00:00Z',
            'created_at_to' => '2026-12-31T23:59:59Z',
        ]);
    }

    public function testProspectInvalidFilterThrows(): void
    {
        $this->assertInvalidFilterThrows('prospects');
    }

    /**
     * The "fun" test: pick a random prospect from the list, extract its filterable
     * properties (email, status ID, assigned user ID, source type), then search
     * back using all those filters combined. The original prospect MUST appear
     * in the filtered results — proving the filters actually narrow down correctly.
     */
    public function testProspectFiltersCombinedFindSameRecord(): void
    {
        $client = $this->getClient();

        // Step 1: Get a page of prospects
        $prospects = $client->prospects->list([
            'per_page' => 50,
            'include' => 'organization_prospect_status,assigned_organization_user',
        ]);

        if (count($prospects->data) === 0) {
            $this->markTestSkipped('No prospects available');
        }

        // Step 2: Pick a random one that has at least one filterable field populated
        $candidates = array_filter(
            $prospects->data,
            fn ($p) => $p->email !== null
                || $p->organization_prospect_status_id !== null
                || $p->assigned_organization_user_id !== null,
        );

        if (empty($candidates)) {
            $this->markTestSkipped('No prospects with filterable fields');
        }

        $candidates = array_values($candidates);
        $random = $candidates[array_rand($candidates)];

        // Step 3: Build filters from the prospect's actual data
        $filters = [];
        $filterDescriptions = [];

        if ($random->email !== null) {
            $filters['email'] = $random->email;
            $filterDescriptions[] = "email={$random->email}";
        }

        if ($random->organization_prospect_status_id !== null) {
            $filters['organization_prospect_status_id'] = $random->organization_prospect_status_id;
            $filterDescriptions[] = "status={$random->organization_prospect_status_id}";
        }

        if ($random->assigned_organization_user_id !== null) {
            $filters['assigned_organization_user_id'] = $random->assigned_organization_user_id;
            $filterDescriptions[] = "assigned_user={$random->assigned_organization_user_id}";
        }

        if ($random->source_type !== null) {
            $filters['source_type'] = $random->source_type;
            $filterDescriptions[] = "source_type={$random->source_type}";
        }

        $this->assertNotEmpty($filters, 'Picked prospect should have at least one filterable field');

        // Step 4: Search back with all filters combined
        $filtered = $client->prospects->list(array_merge(
            ['per_page' => 50],
            $filters,
        ));

        // Step 5: The original prospect MUST be in the filtered results
        $foundIds = array_map(fn ($p) => $p->id, $filtered->data);

        $this->assertContains(
            $random->id,
            $foundIds,
            sprintf(
                'Prospect %s (%s) should appear in filtered results. Got %d results: [%s]',
                $random->id,
                implode(', ', $filterDescriptions),
                count($filtered->data),
                implode(', ', $foundIds),
            ),
        );
    }

    // =========================================================================
    // Contracts
    // =========================================================================

    public function testContractGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('contracts');
    }

    public function testContractCategoryFilter(): void
    {
        $this->assertResourceFiltersWork('contracts', [
            'category' => 'core',
        ]);
    }

    public function testContractSourceFilter(): void
    {
        $this->assertResourceFiltersWork('contracts', [
            'source' => 'internal',
        ]);
    }

    // =========================================================================
    // Banking
    // =========================================================================

    public function testBankTransactionGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('bankTransactions');
    }

    public function testBankTransactionStateFilter(): void
    {
        $this->assertResourceFiltersWork('bankTransactions', [
            'state' => 'classified',
        ]);
    }

    public function testBankTransactionDirectionFilter(): void
    {
        $this->assertResourceFiltersWork('bankTransactions', [
            'direction' => 'inbound',
        ]);
    }

    public function testBankAccountGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('bankAccounts');
    }

    // =========================================================================
    // Billing & Offers
    // =========================================================================

    public function testBillingScheduleGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('billingSchedules');
    }

    public function testBillingScheduleStatusFilter(): void
    {
        $this->assertResourceFiltersWork('billingSchedules', [
            'status' => 'active',
        ]);
    }

    public function testBillingScheduleTypeFilter(): void
    {
        $this->assertResourceFiltersWork('billingSchedules', [
            'type' => 'subscription',
        ]);
    }

    public function testOfferGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('offers');
    }

    public function testOfferBooleanFilters(): void
    {
        $this->assertResourceFiltersWork('offers', [
            'is_active' => true,
        ]);
    }

    public function testProposalGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('proposals');
    }

    public function testProposalStatusFilter(): void
    {
        $this->assertResourceFiltersWork('proposals', [
            'status' => 'draft',
        ]);
    }

    // =========================================================================
    // Payroll
    // =========================================================================

    public function testPayslipGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('payslips');
    }

    public function testPayslipStatusFilter(): void
    {
        $this->assertResourceFiltersWork('payslips', [
            'status' => 'pending',
        ]);
    }

    // =========================================================================
    // Reports
    // =========================================================================

    public function testReportGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('reports');
    }

    public function testReportDateRangeFilter(): void
    {
        $this->assertResourceFiltersWork('reports', [
            'reported_at_from' => '2024-01-01T00:00:00Z',
            'reported_at_to' => '2026-12-31T23:59:59Z',
        ]);
    }

    // =========================================================================
    // Content & Files
    // =========================================================================

    public function testGuidelineGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('guidelines');
    }

    public function testPlaybookGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('playbooks');
    }

    public function testPlaybookDateRangeFilters(): void
    {
        $this->assertResourceFiltersWork('playbooks', [
            'created_at_from' => '2024-01-01T00:00:00Z',
            'created_at_to' => '2026-12-31T23:59:59Z',
        ]);
    }

    public function testReusableContentGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('reusableContent');
    }

    public function testReusableContentEntityTypeFilter(): void
    {
        $this->assertResourceFiltersWork('reusableContent', [
            'entity_type' => 'contract',
        ]);
    }

    // =========================================================================
    // Users & Projects
    // =========================================================================

    public function testOrganizationUserGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('organizationUsers');
    }

    public function testProjectGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('projects');
    }

    public function testProjectDateRangeFilters(): void
    {
        $this->assertResourceFiltersWork('projects', [
            'created_at_from' => '2024-01-01T00:00:00Z',
            'created_at_to' => '2026-12-31T23:59:59Z',
        ]);
    }

    // =========================================================================
    // Tasks & Tags
    // =========================================================================

    public function testTaskGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('tasks');
    }

    public function testTagGlobalFilters(): void
    {
        $this->assertGlobalFiltersWork('tags');
    }

    public function testTagInvalidFilterThrows(): void
    {
        $this->assertInvalidFilterThrows('tags');
    }

    // =========================================================================
    // Global Services
    // =========================================================================

    public function testOrganizationGlobalFilters(): void
    {
        $service = $this->getClient()->organizations;

        $collection = $service->list([
            'per_page' => 2,
        ]);

        $this->assertInstanceOf(Collection::class, $collection);
        $this->assertGreaterThan(0, count($collection->data));
    }

    public function testOrganizationInvalidFilterThrows(): void
    {
        $this->assertInvalidFilterThrows('organizations');
    }
}

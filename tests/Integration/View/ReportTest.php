<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\Report;
use Enlivy\Organization\ReportSchema;
use Enlivy\Tests\Integration\IntegrationTestCase;

/**
 * Integration tests for Report-related endpoints.
 */
class ReportTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Reports
    // -------------------------------------------------------------------------

    public function testListReports(): void
    {
        $reports = $this->getClient()->reports->list();

        $this->assertInstanceOf(Collection::class, $reports);
        $this->assertIsArray($reports->data);

        if (count($reports->data) > 0) {
            $report = $reports->data[0];
            $this->assertInstanceOf(Report::class, $report);
            $this->assertNotNull($report->id);
            $this->assertNotNull($report->organization_id);
        }
    }

    public function testListReportsWithPagination(): void
    {
        $reports = $this->getClient()->reports->list(['page' => 1]);

        $this->assertInstanceOf(Collection::class, $reports);
        $this->assertNotNull($reports->meta);
    }

    public function testRetrieveReport(): void
    {
        $reports = $this->getClient()->reports->list(['per_page' => 1]);

        if (count($reports->data) === 0) {
            $this->markTestSkipped('No reports available for testing');
        }

        $reportId = $reports->data[0]->id;
        $report = $this->getClient()->reports->retrieve($reportId);

        $this->assertInstanceOf(Report::class, $report);
        $this->assertEquals($reportId, $report->id);
    }

    // -------------------------------------------------------------------------
    // Report Schemas
    // -------------------------------------------------------------------------

    public function testListReportSchemas(): void
    {
        $schemas = $this->getClient()->reportSchemas->list();

        $this->assertInstanceOf(Collection::class, $schemas);
        $this->assertIsArray($schemas->data);

        if (count($schemas->data) > 0) {
            $schema = $schemas->data[0];
            $this->assertInstanceOf(ReportSchema::class, $schema);
            $this->assertNotNull($schema->id);
        }
    }

    public function testRetrieveReportSchema(): void
    {
        $schemas = $this->getClient()->reportSchemas->list(['per_page' => 1]);

        if (count($schemas->data) === 0) {
            $this->markTestSkipped('No report schemas available for testing');
        }

        $schemaId = $schemas->data[0]->id;
        $schema = $this->getClient()->reportSchemas->retrieve($schemaId);

        $this->assertInstanceOf(ReportSchema::class, $schema);
        $this->assertEquals($schemaId, $schema->id);
    }
}

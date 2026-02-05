<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Prospect;
use Enlivy\Organization\ProspectStatus;
use Enlivy\Organization\ProspectActivity;
use Enlivy\Tests\Integration\IntegrationTestCase;

/**
 * Integration tests for Prospect/CRM-related endpoints.
 */
class ProspectTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Prospects
    // -------------------------------------------------------------------------

    public function testListProspects(): void
    {
        $prospects = $this->getClient()->prospects->list();

        $this->assertInstanceOf(Collection::class, $prospects);
        $this->assertIsArray($prospects->data);

        if (count($prospects->data) > 0) {
            $prospect = $prospects->data[0];
            $this->assertInstanceOf(Prospect::class, $prospect);
            $this->assertIdPrefix('org_pros_', $prospect->id);
            $this->assertNotNull($prospect->organization_id);
        }
    }

    public function testListProspectsWithPagination(): void
    {
        $prospects = $this->getClient()->prospects->list(['page' => 1]);

        $this->assertInstanceOf(Collection::class, $prospects);
        $this->assertNotNull($prospects->meta);
    }

    public function testListProspectsWithInclude(): void
    {
        $prospects = $this->getClient()->prospects->list([
            'include' => 'organization_prospect_status,assigned_organization_user',
        ]);

        $this->assertInstanceOf(Collection::class, $prospects);

        if (count($prospects->data) > 0) {
            $prospect = $prospects->data[0];
            // Status should be included
            if ($prospect->organization_prospect_status_id !== null) {
                $this->assertNotNull($prospect->organization_prospect_status);
            }
        }
    }

    public function testRetrieveProspect(): void
    {
        $prospects = $this->getClient()->prospects->list(['per_page' => 1]);

        if (count($prospects->data) === 0) {
            $this->markTestSkipped('No prospects available for testing');
        }

        $prospectId = $prospects->data[0]->id;
        $prospect = $this->getClient()->prospects->retrieve($prospectId);

        $this->assertInstanceOf(Prospect::class, $prospect);
        $this->assertEquals($prospectId, $prospect->id);
    }

    public function testProspectsBoard(): void
    {
        $board = $this->getClient()->prospects->board();

        // Board returns a structured object with columns
        $this->assertInstanceOf(EnlivyObject::class, $board);
        $this->assertNotNull($board->columns);
        $this->assertIsArray($board->columns);
    }

    // -------------------------------------------------------------------------
    // Prospect Statuses
    // -------------------------------------------------------------------------

    public function testListProspectStatuses(): void
    {
        $statuses = $this->getClient()->prospectStatuses->list();

        $this->assertInstanceOf(Collection::class, $statuses);
        $this->assertIsArray($statuses->data);

        if (count($statuses->data) > 0) {
            $status = $statuses->data[0];
            $this->assertInstanceOf(ProspectStatus::class, $status);
            $this->assertNotNull($status->id);
        }
    }

    public function testRetrieveProspectStatus(): void
    {
        $statuses = $this->getClient()->prospectStatuses->list(['per_page' => 1]);

        if (count($statuses->data) === 0) {
            $this->markTestSkipped('No prospect statuses available for testing');
        }

        $statusId = $statuses->data[0]->id;
        $status = $this->getClient()->prospectStatuses->retrieve($statusId);

        $this->assertInstanceOf(ProspectStatus::class, $status);
        $this->assertEquals($statusId, $status->id);
    }

    // -------------------------------------------------------------------------
    // Prospect Activities
    // -------------------------------------------------------------------------

    public function testListProspectActivities(): void
    {
        // ProspectActivityService lists all activities in the organization (not per-prospect)
        $activities = $this->getClient()->prospectActivities->list();

        $this->assertInstanceOf(Collection::class, $activities);
        $this->assertIsArray($activities->data);

        if (count($activities->data) > 0) {
            $activity = $activities->data[0];
            $this->assertInstanceOf(ProspectActivity::class, $activity);
        }
    }
}

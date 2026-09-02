<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\EnlivyObject;
use Enlivy\Organization\Prospect;
use Enlivy\Organization\ProspectStage;
use Enlivy\Organization\ProspectActivity;
use Enlivy\Tests\Integration\IntegrationTestCase;

class ProspectTest extends IntegrationTestCase
{
    // Prospects

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
            'include' => 'organization_prospect_stage,assigned_organization_user',
        ]);

        $this->assertInstanceOf(Collection::class, $prospects);

        if (count($prospects->data) > 0) {
            $prospect = $prospects->data[0];
            // Status should be included
            if ($prospect->organization_prospect_stage_id !== null) {
                $this->assertNotNull($prospect->organization_prospect_stage);
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

    // Prospect Statuses

    public function testListProspectStages(): void
    {
        $stages = $this->getClient()->prospectStages->list();

        $this->assertInstanceOf(Collection::class, $stages);
        $this->assertIsArray($stages->data);

        if (count($stages->data) > 0) {
            $stage = $stages->data[0];
            $this->assertInstanceOf(ProspectStage::class, $stage);
            $this->assertNotNull($stage->id);
        }
    }

    public function testRetrieveProspectStage(): void
    {
        $stages = $this->getClient()->prospectStages->list(['per_page' => 1]);

        if (count($stages->data) === 0) {
            $this->markTestSkipped('No prospect stages available for testing');
        }

        $stageId = $stages->data[0]->id;
        $stage = $this->getClient()->prospectStages->retrieve($stageId);

        $this->assertInstanceOf(ProspectStage::class, $stage);
        $this->assertEquals($stageId, $stage->id);
    }

    // Prospect Activities

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

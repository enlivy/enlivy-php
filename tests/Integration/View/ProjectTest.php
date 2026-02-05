<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\Project;
use Enlivy\Tests\Integration\IntegrationTestCase;

/**
 * Integration tests for Project-related endpoints.
 */
class ProjectTest extends IntegrationTestCase
{
    public function testListProjects(): void
    {
        $projects = $this->getClient()->projects->list();

        $this->assertInstanceOf(Collection::class, $projects);
        $this->assertIsArray($projects->data);

        if (count($projects->data) > 0) {
            $project = $projects->data[0];
            $this->assertInstanceOf(Project::class, $project);
            $this->assertIdPrefix('org_proj_', $project->id);
            $this->assertNotNull($project->organization_id);
        }
    }

    public function testListProjectsWithPagination(): void
    {
        $projects = $this->getClient()->projects->list(['page' => 1]);

        $this->assertInstanceOf(Collection::class, $projects);
        $this->assertNotNull($projects->meta);
    }

    public function testRetrieveProject(): void
    {
        $projects = $this->getClient()->projects->list(['per_page' => 1]);

        if (count($projects->data) === 0) {
            $this->markTestSkipped('No projects available for testing');
        }

        $projectId = $projects->data[0]->id;
        $project = $this->getClient()->projects->retrieve($projectId);

        $this->assertInstanceOf(Project::class, $project);
        $this->assertEquals($projectId, $project->id);
    }

    public function testRetrieveProjectWithInclude(): void
    {
        $projects = $this->getClient()->projects->list(['per_page' => 1]);

        if (count($projects->data) === 0) {
            $this->markTestSkipped('No projects available for testing');
        }

        $project = $this->getClient()->projects->retrieve(
            $projects->data[0]->id,
            ['include' => 'resource_bundles']
        );

        $this->assertInstanceOf(Project::class, $project);
    }
}

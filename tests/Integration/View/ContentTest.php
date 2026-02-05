<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\Guideline;
use Enlivy\Organization\Playbook;
use Enlivy\Organization\ReusableContent;
use Enlivy\Organization\File;
use Enlivy\Tests\Integration\IntegrationTestCase;

/**
 * Integration tests for Content-related endpoints (Guidelines, Playbooks, Files).
 */
class ContentTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Guidelines
    // -------------------------------------------------------------------------

    public function testListGuidelines(): void
    {
        $guidelines = $this->getClient()->guidelines->list();

        $this->assertInstanceOf(Collection::class, $guidelines);
        $this->assertIsArray($guidelines->data);

        if (count($guidelines->data) > 0) {
            $guideline = $guidelines->data[0];
            $this->assertInstanceOf(Guideline::class, $guideline);
            $this->assertNotNull($guideline->id);
        }
    }

    public function testRetrieveGuideline(): void
    {
        $guidelines = $this->getClient()->guidelines->list(['per_page' => 1]);

        if (count($guidelines->data) === 0) {
            $this->markTestSkipped('No guidelines available for testing');
        }

        $guidelineId = $guidelines->data[0]->id;
        $guideline = $this->getClient()->guidelines->retrieve($guidelineId);

        $this->assertInstanceOf(Guideline::class, $guideline);
        $this->assertEquals($guidelineId, $guideline->id);
    }

    // -------------------------------------------------------------------------
    // Playbooks
    // -------------------------------------------------------------------------

    public function testListPlaybooks(): void
    {
        $playbooks = $this->getClient()->playbooks->list();

        $this->assertInstanceOf(Collection::class, $playbooks);
        $this->assertIsArray($playbooks->data);

        if (count($playbooks->data) > 0) {
            $playbook = $playbooks->data[0];
            $this->assertInstanceOf(Playbook::class, $playbook);
            $this->assertNotNull($playbook->id);
        }
    }

    public function testRetrievePlaybook(): void
    {
        $playbooks = $this->getClient()->playbooks->list(['per_page' => 1]);

        if (count($playbooks->data) === 0) {
            $this->markTestSkipped('No playbooks available for testing');
        }

        $playbookId = $playbooks->data[0]->id;
        $playbook = $this->getClient()->playbooks->retrieve($playbookId);

        $this->assertInstanceOf(Playbook::class, $playbook);
        $this->assertEquals($playbookId, $playbook->id);
    }

    // -------------------------------------------------------------------------
    // Reusable Content
    // -------------------------------------------------------------------------

    public function testListReusableContent(): void
    {
        $contents = $this->getClient()->reusableContent->list();

        $this->assertInstanceOf(Collection::class, $contents);
        $this->assertIsArray($contents->data);

        if (count($contents->data) > 0) {
            $content = $contents->data[0];
            $this->assertInstanceOf(ReusableContent::class, $content);
            $this->assertNotNull($content->id);
        }
    }

    public function testRetrieveReusableContent(): void
    {
        $contents = $this->getClient()->reusableContent->list(['per_page' => 1]);

        if (count($contents->data) === 0) {
            $this->markTestSkipped('No reusable content available for testing');
        }

        $contentId = $contents->data[0]->id;
        $content = $this->getClient()->reusableContent->retrieve($contentId);

        $this->assertInstanceOf(ReusableContent::class, $content);
        $this->assertEquals($contentId, $content->id);
    }

    // -------------------------------------------------------------------------
    // Files
    // -------------------------------------------------------------------------

    public function testListFiles(): void
    {
        $files = $this->getClient()->files->list();

        $this->assertInstanceOf(Collection::class, $files);
        $this->assertIsArray($files->data);

        if (count($files->data) > 0) {
            $file = $files->data[0];
            $this->assertInstanceOf(File::class, $file);
            $this->assertNotNull($file->id);
        }
    }

    public function testRetrieveFile(): void
    {
        $files = $this->getClient()->files->list(['per_page' => 1]);

        if (count($files->data) === 0) {
            $this->markTestSkipped('No files available for testing');
        }

        $fileId = $files->data[0]->id;
        $file = $this->getClient()->files->retrieve($fileId);

        $this->assertInstanceOf(File::class, $file);
        $this->assertEquals($fileId, $file->id);
    }
}

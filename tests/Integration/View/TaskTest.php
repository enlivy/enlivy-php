<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\Task;
use Enlivy\Organization\TaskStatus;
use Enlivy\Tests\Integration\IntegrationTestCase;

/**
 * Integration tests for Task-related endpoints.
 */
class TaskTest extends IntegrationTestCase
{
    // -------------------------------------------------------------------------
    // Tasks
    // -------------------------------------------------------------------------

    public function testListTasks(): void
    {
        $tasks = $this->getClient()->tasks->list();

        $this->assertInstanceOf(Collection::class, $tasks);
        $this->assertIsArray($tasks->data);

        if (count($tasks->data) > 0) {
            $task = $tasks->data[0];
            $this->assertInstanceOf(Task::class, $task);
            $this->assertNotNull($task->id);
            $this->assertNotNull($task->organization_id);
        }
    }

    public function testListTasksWithPagination(): void
    {
        $tasks = $this->getClient()->tasks->list(['page' => 1]);

        $this->assertInstanceOf(Collection::class, $tasks);
        $this->assertNotNull($tasks->meta);
    }

    public function testRetrieveTask(): void
    {
        $tasks = $this->getClient()->tasks->list(['per_page' => 1]);

        if (count($tasks->data) === 0) {
            $this->markTestSkipped('No tasks available for testing');
        }

        $taskId = $tasks->data[0]->id;
        $task = $this->getClient()->tasks->retrieve($taskId);

        $this->assertInstanceOf(Task::class, $task);
        $this->assertEquals($taskId, $task->id);
    }

    // -------------------------------------------------------------------------
    // Task Statuses
    // -------------------------------------------------------------------------

    public function testListTaskStatuses(): void
    {
        $statuses = $this->getClient()->taskStatuses->list();

        $this->assertInstanceOf(Collection::class, $statuses);
        $this->assertIsArray($statuses->data);

        if (count($statuses->data) > 0) {
            $status = $statuses->data[0];
            $this->assertInstanceOf(TaskStatus::class, $status);
            $this->assertNotNull($status->id);
        }
    }

    public function testRetrieveTaskStatus(): void
    {
        $statuses = $this->getClient()->taskStatuses->list(['per_page' => 1]);

        if (count($statuses->data) === 0) {
            $this->markTestSkipped('No task statuses available for testing');
        }

        $statusId = $statuses->data[0]->id;
        $status = $this->getClient()->taskStatuses->retrieve($statusId);

        $this->assertInstanceOf(TaskStatus::class, $status);
        $this->assertEquals($statusId, $status->id);
    }
}

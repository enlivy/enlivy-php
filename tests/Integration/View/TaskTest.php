<?php

declare(strict_types=1);

namespace Enlivy\Tests\Integration\View;

use Enlivy\Collection;
use Enlivy\Organization\Task;
use Enlivy\Organization\TaskBoardColumn;
use Enlivy\Organization\TaskStage;
use Enlivy\Tests\Integration\IntegrationTestCase;

class TaskTest extends IntegrationTestCase
{
    // Tasks

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

    public function testTaskBoard(): void
    {
        $board = $this->getClient()->tasks->board();

        $this->assertInstanceOf(Collection::class, $board);

        foreach ($board->data as $column) {
            $this->assertInstanceOf(TaskBoardColumn::class, $column);
            $this->assertIsInt($column->total_count);
        }
    }

    // Task Stages

    public function testListTaskStages(): void
    {
        $stages = $this->getClient()->taskStages->list();

        $this->assertInstanceOf(Collection::class, $stages);
        $this->assertIsArray($stages->data);

        if (count($stages->data) > 0) {
            $stage = $stages->data[0];
            $this->assertInstanceOf(TaskStage::class, $stage);
            $this->assertNotNull($stage->id);
            $this->assertNotNull($stage->stage_type);
        }
    }

    public function testRetrieveTaskStage(): void
    {
        $stages = $this->getClient()->taskStages->list(['per_page' => 1]);

        if (count($stages->data) === 0) {
            $this->markTestSkipped('No task stages available for testing');
        }

        $stageId = $stages->data[0]->id;
        $stage = $this->getClient()->taskStages->retrieve($stageId);

        $this->assertInstanceOf(TaskStage::class, $stage);
        $this->assertEquals($stageId, $stage->id);
    }
}

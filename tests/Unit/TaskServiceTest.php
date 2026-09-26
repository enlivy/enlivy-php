<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\EnlivyClient;
use Enlivy\EnlivyObject;
use Enlivy\Exception\InvalidArgumentException;
use Enlivy\Organization\Comment;
use Enlivy\Organization\EventTrail;
use Enlivy\Organization\Notification;
use Enlivy\Organization\Prospect;
use Enlivy\Organization\Task;
use Enlivy\Organization\TaskBoardColumn;
use Enlivy\Organization\TaskFeedEntry;
use Enlivy\Organization\TaskParticipant;
use Enlivy\Organization\TaskStage;
use Enlivy\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

final class TaskServiceTest extends TestCase
{
    private MockHttpClient $httpClient;
    private EnlivyClient $client;

    protected function setUp(): void
    {
        $this->httpClient = new MockHttpClient();
        $this->client = new EnlivyClient([
            'api_key' => '1|test_token',
            'organization_id' => 'org_default',
            'http_client' => $this->httpClient,
        ]);
    }

    public function testTaskStatusesAreGoneAndStagesTakeTheirPlace(): void
    {
        $this->assertIsObject($this->client->tasks);
        $this->assertIsObject($this->client->taskStages);
        $this->assertIsObject($this->client->taskComments);

        $this->httpClient->addResponse(200, ['data' => [['id' => 'org_task_stat_1', 'stage_type' => 'in_progress']]]);
        $stages = $this->client->taskStages->list();

        $this->assertInstanceOf(TaskStage::class, $stages->getData()[0]);
        $this->assertStringContainsString('/organizations/org_default/task-stages', $this->httpClient->getLastRequest()['url']);

        $this->expectException(\InvalidArgumentException::class);
        $this->client->taskStatuses;
    }

    /**
     * A retired include answers 422, but a retired filter is silently ignored, so the SDK has to
     * refuse both.
     */
    public function testRetiredIncludesAndFiltersAreRejected(): void
    {
        foreach (['organization_task_status', 'assigned_to_organization_user', 'organization_report'] as $include) {
            try {
                $this->client->tasks->list(['include' => $include]);
                $this->fail("{$include} should be rejected.");
            } catch (InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }

        foreach (['assigned_to_organization_user_id', 'has_lang_map'] as $filter) {
            try {
                $this->client->tasks->list([$filter => 'x']);
                $this->fail("{$filter} should be rejected.");
            } catch (InvalidArgumentException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function testTheListTakesTheSharedBoardFilters(): void
    {
        $this->httpClient->addResponse(200, ['data' => [['id' => 'org_task_1', 'status' => 'waiting']]]);

        $tasks = $this->client->tasks->list([
            'status' => ['not_started', 'waiting'],
            'without_project' => true,
            'assignee_organization_user_id' => 'org_user_1',
            'organization_invoice_id' => 'org_inv_1',
            'order_by' => 'board',
            'include' => 'organization_task_participants,organization_task_stage',
        ]);

        $this->assertInstanceOf(Task::class, $tasks->getData()[0]);
        $this->assertSame('waiting', $tasks->getData()[0]->status);
        $this->assertSame(['not_started', 'waiting'], $this->httpClient->getLastRequest()['params']['status']);
    }

    /**
     * Without the explicit class, columns would hydrate as `Task` objects with no id or title.
     */
    public function testTheBoardAnswersWithColumns(): void
    {
        $this->httpClient->addResponse(200, ['data' => [
            [
                'organization_task_stage_id' => null,
                'total_count' => 1,
                'has_more' => false,
                'organization_task_stage' => null,
                'tasks' => ['data' => [['id' => 'org_task_1', 'title' => 'Call back']]],
            ],
        ]]);

        $board = $this->client->tasks->board(['include' => 'organization_task_participants', 'q' => 'call']);

        $column = $board->getData()[0];
        $this->assertInstanceOf(TaskBoardColumn::class, $column);
        $this->assertNull($column->organization_task_stage_id);
        $this->assertSame('Call back', $column->tasks->data[0]->title);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('GET', $request['method']);
        $this->assertStringContainsString('/organizations/org_default/tasks/board', $request['url']);
    }

    public function testEachLifecycleVerbReachesItsOwnPath(): void
    {
        $verbs = [
            'complete' => ['POST', 'complete', []],
            'cancel' => ['POST', 'cancel', ['cancel_reason' => 'duplicate']],
            'reopen' => ['POST', 'reopen', []],
            'moveOnBoard' => ['PUT', 'board-position', ['organization_task_stage_id' => 'org_task_stat_1', 'place' => 'top']],
        ];

        foreach ($verbs as $method => [$httpMethod, $segment, $params]) {
            $this->httpClient->addResponse(200, ['data' => ['id' => 'org_task_1']]);
            $task = $this->client->tasks->{$method}('org_task_1', $params);

            $this->assertInstanceOf(Task::class, $task, "{$method} should answer with the task.");
            $request = $this->httpClient->getLastRequest();
            $this->assertSame($httpMethod, $request['method'], "{$method} should {$httpMethod}.");
            $this->assertStringContainsString("/tasks/org_task_1/{$segment}", $request['url']);
        }
    }

    /**
     * `Task` has a real `status` field, so the envelope's `status: ok` must not hydrate as one.
     */
    public function testDeletingAnswersWithTheEnvelopeNotATask(): void
    {
        $this->httpClient->addResponse(200, ['status' => 'ok']);

        $result = $this->client->tasks->delete('org_task_1');

        $this->assertNotInstanceOf(Task::class, $result);
        $this->assertSame('ok', $result->status);
        $this->assertSame('DELETE', $this->httpClient->getLastRequest()['method']);
    }

    public function testFollowingAnswersWithTheCallersParticipantRow(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_task_part_1', 'role' => 'follower', 'notifications_enabled' => false]]);

        $participant = $this->client->tasks->follow('org_task_1', ['notifications_enabled' => false]);

        $this->assertInstanceOf(TaskParticipant::class, $participant);
        $this->assertSame('follower', $participant->role);
        $this->assertSame('POST', $this->httpClient->getLastRequest()['method']);
        $this->assertStringContainsString('/tasks/org_task_1/follow', $this->httpClient->getLastRequest()['url']);

        $this->httpClient->addResponse(200, ['status' => 'ok']);
        $result = $this->client->tasks->unfollow('org_task_1');

        $this->assertNotInstanceOf(Task::class, $result);
        $this->assertSame('DELETE', $this->httpClient->getLastRequest()['method']);
        $this->assertStringContainsString('/tasks/org_task_1/follow', $this->httpClient->getLastRequest()['url']);
    }

    public function testTheFeedIsCursorPaged(): void
    {
        $this->httpClient->addResponse(200, [
            'data' => [
                ['kind' => 'comment', 'id' => 'org_comment_1', 'comment' => ['data' => ['body' => 'Friday']], 'event' => null],
                ['kind' => 'event', 'id' => 'org_evt_1', 'comment' => null, 'event' => ['data' => ['event_type' => 'updated']]],
            ],
            'meta' => ['next_cursor' => 'abc'],
        ]);

        $feed = $this->client->tasks->feed('org_task_1', ['limit' => 2]);

        $this->assertInstanceOf(TaskFeedEntry::class, $feed->getData()[0]);
        $this->assertSame(['comment', 'event'], array_map(static fn (TaskFeedEntry $entry) => $entry->kind, $feed->getData()));
        $this->assertSame('abc', $feed->getMeta()['next_cursor']);
        $this->assertStringContainsString('/tasks/org_task_1/feed', $this->httpClient->getLastRequest()['url']);

        $this->httpClient->addResponse(200, ['data' => [], 'meta' => ['next_cursor' => null]]);
        $this->client->tasks->feed('org_task_1', ['cursor' => 'abc']);
        $this->assertSame('abc', $this->httpClient->getLastRequest()['params']['cursor']);
    }

    public function testTaskNotificationsHydrateAsNotifications(): void
    {
        $this->httpClient->addResponse(200, ['data' => [['id' => 'org_notif_1', 'sent_by_user_id' => 'user_1']]]);

        $notifications = $this->client->tasks->notifications('org_task_1', ['include' => 'subjects']);

        $this->assertInstanceOf(Notification::class, $notifications->getData()[0]);
        $this->assertStringContainsString('/tasks/org_task_1/notifications', $this->httpClient->getLastRequest()['url']);
    }

    public function testCommentsHangOffTheirTask(): void
    {
        $this->httpClient->addResponse(201, ['data' => ['id' => 'org_comment_1', 'body' => 'Friday']]);
        $comment = $this->client->taskComments->create('org_task_1', [
            'body' => 'Friday',
            'mentioned_organization_user_ids' => ['org_user_2'],
            'include' => 'author_organization_user',
        ]);
        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertSame('POST', $this->httpClient->getLastRequest()['method']);
        $this->assertStringContainsString('/tasks/org_task_1/comments', $this->httpClient->getLastRequest()['url']);

        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_comment_1', 'body' => 'Monday']]);
        $this->assertInstanceOf(Comment::class, $this->client->taskComments->update('org_task_1', 'org_comment_1', ['body' => 'Monday']));
        $this->assertSame('PUT', $this->httpClient->getLastRequest()['method']);
        $this->assertStringContainsString('/tasks/org_task_1/comments/org_comment_1', $this->httpClient->getLastRequest()['url']);

        $this->httpClient->addResponse(200, ['status' => 'ok']);
        $this->assertNotInstanceOf(Comment::class, $this->client->taskComments->delete('org_task_1', 'org_comment_1'));
        $this->assertSame('DELETE', $this->httpClient->getLastRequest()['method']);

        $this->expectException(InvalidArgumentException::class);
        $this->client->taskComments->create('org_task_1', ['body' => 'x', 'include' => 'organization']);
    }

    public function testTasksReadTheirEventTrail(): void
    {
        $this->httpClient->addResponse(200, ['data' => [['id' => 'org_evt_1']]]);

        $trail = $this->client->tasks->eventTrails(['subject_id' => 'org_task_1', 'include' => 'changes']);

        $this->assertInstanceOf(EventTrail::class, $trail->getData()[0]);
        $this->assertStringContainsString('/organizations/org_default/tasks/event-trails', $this->httpClient->getLastRequest()['url']);
    }

    public function testOnlyStagesReorder(): void
    {
        $this->assertFalse(method_exists($this->client->tasks, 'reorder'));

        $this->httpClient->addResponse(200, ['data' => []]);
        $this->client->taskStages->reorder(['organization_task_stage_ids' => ['org_task_stat_2', 'org_task_stat_1']]);

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('PUT', $request['method']);
        $this->assertStringContainsString('/task-stages/reorder', $request['url']);
    }

    public function testAStageIsRestoredThroughItsOwnPath(): void
    {
        $this->httpClient->addResponse(200, ['status' => 'ok']);

        $this->client->taskStages->restore('org_task_stat_1');

        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringContainsString('/task-stages/restore/org_task_stat_1', $request['url']);
    }

    public function testDeletingAStageNamesWhereItsTasksGo(): void
    {
        $this->httpClient->addResponse(200, ['status' => 'ok']);

        $result = $this->client->taskStages->delete('org_task_stat_1', ['move_to_organization_task_stage_id' => 'org_task_stat_2']);

        $this->assertInstanceOf(EnlivyObject::class, $result);
        $this->assertNotInstanceOf(TaskStage::class, $result);
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('DELETE', $request['method']);
        $this->assertSame('org_task_stat_2', $request['params']['move_to_organization_task_stage_id']);
    }

    public function testInvoicesAndProspectsCanCarryTheirTasks(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_inv_1']]);
        $this->client->invoices->retrieve('org_inv_1', ['include' => 'organization_tasks']);
        $this->assertSame('organization_tasks', $this->httpClient->getLastRequest()['params']['include']);

        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_pros_1']]);
        $this->client->prospects->retrieve('org_pros_1', ['include' => 'organization_tasks']);
        $this->assertSame('organization_tasks', $this->httpClient->getLastRequest()['params']['include']);
    }

    public function testAProspectMovesWithinItsColumn(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_pros_1', 'board_rank' => 'a0v']]);

        $prospect = $this->client->prospects->moveOnBoard('org_pros_1', [
            'previous_organization_prospect_id' => 'org_pros_2',
            'next_organization_prospect_id' => 'org_pros_3',
        ]);

        $this->assertInstanceOf(Prospect::class, $prospect);
        $this->assertSame('a0v', $prospect->board_rank);
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('PUT', $request['method']);
        $this->assertStringContainsString('/prospects/org_pros_1/board-position', $request['url']);
    }
}

<?php

declare(strict_types=1);

namespace Enlivy\Tests\Unit;

use Enlivy\EnlivyClient;
use Enlivy\EnlivyPortalClient;
use Enlivy\Organization\HelpdeskConversation;
use Enlivy\Organization\HelpdeskConversationMessage;
use Enlivy\Organization\HelpdeskConversationRead;
use Enlivy\Organization\HelpdeskInboundEmail;
use Enlivy\Organization\HelpdeskInbox;
use Enlivy\Tests\Mock\MockHttpClient;
use PHPUnit\Framework\TestCase;

final class HelpdeskServiceTest extends TestCase
{
    private MockHttpClient $httpClient;
    private EnlivyClient $client;
    private EnlivyPortalClient $portal;

    protected function setUp(): void
    {
        $this->httpClient = new MockHttpClient();
        $this->client = new EnlivyClient([
            'api_key' => '1|test_token',
            'organization_id' => 'org_default',
            'http_client' => $this->httpClient,
        ]);
        $this->portal = new EnlivyPortalClient([
            'portal_token' => 'portal_token',
            'organization_id' => 'org_default',
            'http_client' => $this->httpClient,
        ]);
    }

    public function testEveryDeskAccessorResolvesToItsService(): void
    {
        $accessors = [
            'helpdeskConversations', 'helpdeskConversationMessages', 'helpdeskConversationAttachments',
            'helpdeskConversationParticipants', 'helpdeskInboxes', 'helpdeskTeammates', 'helpdeskSettings',
            'helpdeskInboundEmails', 'helpdeskInboundEmailRules', 'helpdeskProactiveMessages', 'helpdeskVisitors',
        ];

        foreach ($accessors as $accessor) {
            $this->assertIsObject($this->client->{$accessor}, "The client cannot reach {$accessor}.");
        }

        $this->assertIsObject($this->portal->helpdeskConversations);
        $this->assertIsObject($this->portal->helpdeskAttachments);
    }

    public function testTheQueueIsReadFromTheDeskPath(): void
    {
        $this->httpClient->addResponse(200, ['data' => [['id' => 'org_hdcnv_1', 'subject' => 'Cannot log in']]]);

        $conversations = $this->client->helpdeskConversations->list(['state' => 'open']);

        $this->assertInstanceOf(HelpdeskConversation::class, $conversations->getData()[0]);
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('GET', $request['method']);
        $this->assertStringContainsString('/organizations/org_default/helpdesk/conversations', $request['url']);
        $this->assertSame('open', $request['params']['state']);
    }

    /**
     * The verbs are the only way to move a thread, because its state is derived rather than stored.
     */
    public function testEachLifecycleVerbPostsToItsOwnPath(): void
    {
        $verbs = [
            'assign' => 'assign', 'unassign' => 'unassign', 'snooze' => 'snooze',
            'resolve' => 'resolve', 'close' => 'close', 'reopen' => 'reopen',
            'markSpam' => 'spam', 'unmarkSpam' => 'not-spam',
            'promoteProspect' => 'prospect', 'merge' => 'merge',
            'blockSender' => 'block-sender',
        ];

        foreach ($verbs as $method => $segment) {
            $this->httpClient->addResponse(200, ['data' => ['id' => 'org_hdcnv_1']]);
            $this->client->helpdeskConversations->{$method}('org_hdcnv_1', []);

            $request = $this->httpClient->getLastRequest();
            $this->assertSame('POST', $request['method'], "{$method} should POST.");
            $this->assertStringContainsString(
                "/helpdesk/conversations/org_hdcnv_1/{$segment}",
                $request['url'],
                "{$method} should reach the {$segment} verb.",
            );
        }
    }

    /**
     * Marking a thread read answers with the watermark row, not the conversation — a caller that
     * assumed otherwise would read an id that belongs to something else.
     */
    public function testMarkingReadAnswersWithTheReadRow(): void
    {
        $this->httpClient->addResponse(200, ['data' => [
            'id' => 'org_hdread_1',
            'last_read_organization_helpdesk_conversation_message_id' => 'org_hdmsg_9',
        ]]);

        $read = $this->client->helpdeskConversations->markRead('org_hdcnv_1', [
            'organization_helpdesk_conversation_message_id' => 'org_hdmsg_9',
        ]);

        $this->assertInstanceOf(HelpdeskConversationRead::class, $read);
        $this->assertSame('org_hdmsg_9', $read->last_read_organization_helpdesk_conversation_message_id);
    }

    public function testMessagesHangOffTheirConversation(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_hdmsg_1', 'type' => 'outgoing']]);

        $message = $this->client->helpdeskConversationMessages->create('org_hdcnv_1', [
            'type' => 'outgoing',
            'content' => 'On it.',
        ]);

        $this->assertInstanceOf(HelpdeskConversationMessage::class, $message);
        $this->assertStringContainsString(
            '/helpdesk/conversations/org_hdcnv_1/messages',
            $this->httpClient->getLastRequest()['url'],
        );
    }

    /**
     * A file can be uploaded before the thread exists, which is why staging has no conversation id.
     */
    public function testStagingAnAttachmentDoesNotNameAConversation(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_hdatt_1', 'file_name' => 'log.txt']]);

        $this->client->helpdeskConversationAttachments->stage([]);

        $url = $this->httpClient->getLastRequest()['url'];
        $this->assertStringContainsString('/organizations/org_default/helpdesk/attachments', $url);
        $this->assertStringNotContainsString('conversations', $url);
    }

    public function testSettingsAreASingletonWithNoId(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_hdset_1', 'close_after_resolved_days' => 7]]);

        $this->client->helpdeskSettings->retrieve();

        $this->assertStringContainsString(
            '/organizations/org_default/helpdesk/settings',
            $this->httpClient->getLastRequest()['url'],
        );
    }

    /**
     * The raw body is withheld unless it is asked for by name, so the SDK must accept it as an
     * include rather than rejecting it client-side.
     */
    public function testTheRawBodyOfAnInboundEmailIsAnInclude(): void
    {
        $this->assertContains(
            'content',
            \Enlivy\Service\Organization\Helpdesk\HelpdeskInboundEmailService::AVAILABLE_INCLUDES,
        );

        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_hdinb_1']]);
        $this->client->helpdeskInboundEmails->retrieve('org_hdinb_1', ['include' => 'content']);

        $this->assertSame('content', $this->httpClient->getLastRequest()['params']['include']);
    }

    public function testTheCustomerLaneNeverNamesAnInbox(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_hdcnv_1']]);

        $this->portal->helpdeskConversations->create(['content' => 'My login does not work.']);

        $url = $this->httpClient->getLastRequest()['url'];
        $this->assertStringContainsString('/user-client-portal/organizations/org_default/helpdesk/conversations', $url);
        $this->assertStringNotContainsString('inbox', $url);
    }

    public function testInboxesCarryTheirOwnCopyPerLocale(): void
    {
        $this->httpClient->addResponse(200, ['data' => [
            'id' => 'org_hdinb_1',
            'name' => 'Support',
            'welcome_title_lang_map' => ['en' => 'Hello', 'ro' => 'Salut'],
        ]]);

        $inbox = $this->client->helpdeskInboxes->retrieve('org_hdinb_1');

        $this->assertInstanceOf(HelpdeskInbox::class, $inbox);
        $this->assertSame('Salut', $inbox->welcome_title_lang_map['ro']);
    }

    /**
     * Unlike `markRead()`, this answers with the conversation rather than the read row.
     */
    public function testMarkingUnreadAnswersWithTheConversation(): void
    {
        $this->httpClient->addResponse(200, ['data' => ['id' => 'org_hdcnv_1', 'last_message_type' => 'incoming']]);

        $conversation = $this->client->helpdeskConversations->markUnread('org_hdcnv_1');

        $this->assertInstanceOf(HelpdeskConversation::class, $conversation);
        $this->assertSame('incoming', $conversation->last_message_type);
        $request = $this->httpClient->getLastRequest();
        $this->assertSame('POST', $request['method']);
        $this->assertStringContainsString('/helpdesk/conversations/org_hdcnv_1/unread', $request['url']);
    }

    public function testTheQueueFiltersByAssignmentAndUnread(): void
    {
        $this->httpClient->addResponse(200, ['data' => []]);

        $this->client->helpdeskConversations->list([
            'assignment' => 'unassigned',
            'has_unread' => true,
            'include' => 'lifecycle',
        ]);

        $params = $this->httpClient->getLastRequest()['params'];
        $this->assertSame('unassigned', $params['assignment']);
        $this->assertTrue($params['has_unread']);
    }

    public function testEachInboundEmailVerbPostsToItsOwnPath(): void
    {
        $verbs = [
            'reprocess' => ['reprocess', []],
            'promote' => ['promote', ['trust_sender' => false]],
            'classify' => ['classify', ['interpretation' => 'bulk', 'apply_to_sender' => true]],
            'blockSender' => ['block-sender', ['whole_domain' => true]],
            'fetchOriginal' => ['fetch-original', []],
        ];

        foreach ($verbs as $method => [$segment, $params]) {
            $this->httpClient->addResponse(200, ['data' => ['id' => 'org_hdinb_1']]);
            $email = $this->client->helpdeskInboundEmails->{$method}('org_hdinb_1', $params);

            $this->assertInstanceOf(HelpdeskInboundEmail::class, $email, "{$method} should answer with the email.");
            $request = $this->httpClient->getLastRequest();
            $this->assertSame('POST', $request['method']);
            $this->assertStringContainsString("/helpdesk/inbound-emails/org_hdinb_1/{$segment}", $request['url']);
        }
    }

    public function testTheMailLogFiltersByCategory(): void
    {
        $this->httpClient->addResponse(200, ['data' => [['id' => 'org_hdinb_1', 'category' => 'held', 'trust_score' => 70]]]);

        $emails = $this->client->helpdeskInboundEmails->list([
            'category' => 'held',
            'include' => 'headers,trust_assessment,blocked_identifier',
            'include_meta' => 'navigation_by_category',
        ]);

        $this->assertSame('held', $emails->getData()[0]->category);
        $this->assertSame(70, $emails->getData()[0]->trust_score);
        $params = $this->httpClient->getLastRequest()['params'];
        $this->assertSame('held', $params['category']);
        $this->assertSame('headers,trust_assessment,blocked_identifier', $params['include']);
    }
}

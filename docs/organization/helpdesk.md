# Helpdesk

Run a support desk on your own data: inboxes, conversations, teammates and the widget's visitors.

Two lanes reach the same desk. This page covers the **organization lane**, which your staff use.
Customers reach their own threads through the portal lane, at the end. Putting an authenticated
chat on your own application is [Embedded Support](../embedded-support.md).

The desk needs the `helpdesk` feature pack. Reads work without it; writes answer `402` until the
pack is held, or `403` when the account type may not hold it at all. Seating a teammate consumes a
seat on top of that, so it can be refused even when the pack is held.

## Key concepts

### State is derived, never written

A conversation has no status field. What it is follows from what has happened to it, so you move a
thread with a verb rather than by assigning a value:

| state | what makes it so |
|---|---|
| `pending` | nobody is assigned |
| `open` | somebody is |
| `snoozed` | `snoozed_until` is still ahead |
| `resolved` | `resolved_at` is set |
| `closed` | `closed_at` is set |
| `spam` | marked as such |

A customer replying wakes a snoozed or resolved thread. A closed one refuses the reply and the mail
pipeline starts a fresh conversation that carries the old one.

### Who wrote what

A message's `type` says which side it came from: `incoming` from the customer, `outgoing` from you,
`note` for an internal remark the customer never sees, and `activity` for the desk's own record of
an assignment or a resolution. You may only write `outgoing` and `note` — an incoming message is
the customer's to send.

## Working the queue

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$conversations = $client->helpdeskConversations->list([
    'state' => 'pending',
    'organization_helpdesk_inbox_id' => 'org_hdinb_xxx',
    'include' => ['contact_organization_user', 'assigned_teammate'],
]);

foreach ($conversations as $conversation) {
    echo "{$conversation->display_number}  {$conversation->subject}\n";
    echo "  unread: {$conversation->unread_count}\n";
}
```

`unread_count` is counted per reader, so it is null when the desk had nobody to count it for.

## Answering

```php
<?php

$client->helpdeskConversationMessages->create('org_hdcnv_xxx', [
    'type' => 'outgoing',
    'content' => 'We have reset it. Try again and tell us how you get on.',
    'content_type' => 'text',
]);

// Something for your colleagues rather than the customer
$client->helpdeskConversationMessages->create('org_hdcnv_xxx', [
    'type' => 'note',
    'content' => 'Second time this month. Worth checking their SSO config.',
]);
```

Pass `deliver_email` as false to record a reply you have already sent by other means.

To take back a line that should not have been sent, `redact()` it. There is no edit and no delete:
a thread is a record.

```php
$client->helpdeskConversationMessages->redact('org_hdcnv_xxx', 'org_hdmsg_xxx');
```

## Moving a thread along

```php
<?php

$client->helpdeskConversations->assign('org_hdcnv_xxx', [
    'organization_helpdesk_teammate_id' => 'org_hdtm_xxx',
]);

$client->helpdeskConversations->snooze('org_hdcnv_xxx', [
    'snoozed_until' => '2026-09-22T09:00:00Z',
]);

$client->helpdeskConversations->resolve('org_hdcnv_xxx');
$client->helpdeskConversations->close('org_hdcnv_xxx');
$client->helpdeskConversations->reopen('org_hdcnv_xxx');
```

Two threads about the same thing fold together. The merged one stays as a closed stub pointing at
the survivor, so an old link still resolves:

```php
$client->helpdeskConversations->merge('org_hdcnv_duplicate', [
    'organization_helpdesk_conversation_id' => 'org_hdcnv_survivor',
]);
```

Marking spam withholds the attachments and tells the sender nothing:

```php
$client->helpdeskConversations->markSpam('org_hdcnv_xxx');
$client->helpdeskConversations->unmarkSpam('org_hdcnv_xxx');
```

## Attachments

A file can be uploaded before there is a thread to hang it on. Stage it, then pass the id when you
create the conversation or the message.

```php
<?php

$staged = $client->helpdeskConversationAttachments->stage(['file' => $fileHandle]);

$client->helpdeskConversationMessages->create('org_hdcnv_xxx', [
    'type' => 'outgoing',
    'content' => 'The corrected invoice is attached.',
    'attachment_ids' => [$staged->id],
]);

$bytes = $client->helpdeskConversationAttachments->download('org_hdcnv_xxx', 'org_hdatt_xxx');
```

Staged files that nothing claims are swept, so submit within the day.

## Inboxes

An inbox is where conversations arrive and the settings they inherit: its own numbering, its
working hours, how it assigns, and its copy per locale.

```php
<?php

$inbox = $client->helpdeskInboxes->create([
    'name' => 'Billing',
    'number_prefix' => 'BILL',
    'locale' => 'en',
    'assignment_mode' => 'round_robin',
    'welcome_title_lang_map' => ['en' => 'How can we help?', 'ro' => 'Cu ce vă putem ajuta?'],
    'auto_response_enabled' => true,
]);
```

`widget_origins` is the allow-list of sites the chat may be embedded on. An empty list lets any
site through, so set it once the widget has a home.

Desk-wide defaults live in settings, and an inbox that sets the same field wins:

```php
$settings = $client->helpdeskSettings->retrieve();
$client->helpdeskSettings->update(['close_after_resolved_days' => 14]);
```

Reading settings creates the row, so it never answers a 404 and takes no id.

## Teammates

A teammate is an organization user who can sign in, and creating one consumes a seat.

```php
<?php

$client->helpdeskTeammates->create([
    'organization_user_id' => 'org_user_xxx',
    'display_name' => 'Mihaela',
    'is_available' => true,
]);
```

There is no inactive flag. Somebody who should not be answering has no row; somebody on holiday has
an `away_until`.

## Mail

Every message the desk took in is kept with what it decided to do with it. The raw body is withheld
unless you ask for it by name.

```php
<?php

$emails = $client->helpdeskInboundEmails->list([
    'interpretation' => 'unmatched',
    'include' => ['content'],
]);

// After fixing a routing rule, run one back through the pipeline
$client->helpdeskInboundEmails->reprocess('org_hdine_xxx');
```

Routing rules decide which inbox a message lands in, in order:

```php
$client->helpdeskInboundEmailRules->create([
    'name' => 'Billing questions',
    'from_domain' => 'accounts.example.com',
    'organization_helpdesk_inbox_id' => 'org_hdinb_billing',
    'action' => 'route',
    'order' => 1,
]);
```

## Visitors and proactive messages

A visitor is a returning browser on a site running the widget, one row per organization. They are
never created through the API; the widget mints them.

```php
<?php

$visitors = $client->helpdeskVisitors->list(['identified' => true]);
$events = $client->helpdeskVisitors->events('org_hdvis_xxx');

$client->helpdeskVisitors->block('org_hdvis_xxx');
```

A proactive message opens the conversation from your side when the conditions match:

```php
$client->helpdeskProactiveMessages->create([
    'name' => 'Pricing page help',
    'message_lang_map' => ['en' => 'Questions about a plan? Ask away.'],
    'conditions' => [
        ['type' => 'page_visited', 'match_mode' => 'starts_with', 'url_pattern' => 'https://example.com/pricing'],
    ],
    'delay_seconds' => 20,
    'is_business_hours_only' => true,
]);
```

## The customer's own view

Through a portal session, a customer reaches their own threads and nothing else. Internal notes and
activity rows never appear, and a thread you marked spam is never reported as such.

```php
<?php

use Enlivy\EnlivyPortalClient;

$portal = new EnlivyPortalClient([
    'portal_token' => $session->token,
    'organization_id' => 'org_xxx',
]);

$mine = $portal->helpdeskConversations->list(['include' => ['messages']]);

$portal->helpdeskConversations->create([
    'subject' => 'Invoice query',
    'content' => 'The March invoice looks wrong.',
]);

$portal->helpdeskConversations->rate('org_hdcnv_xxx', ['rating' => 5, 'rating_comment' => 'Quick and clear.']);
```

The inbox is not the customer's to pick; the organization's default customer-facing one is used.

## Related

- [Embedded Support](../embedded-support.md) — putting an authenticated chat on your own application
- [Customer Portal](customer-portal.md) — sessions and permissions
- [Prospects](prospects.md) — a conversation can become a lead

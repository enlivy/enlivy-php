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
    'organization_helpdesk_inbox_id' => 'org_hdinbox_xxx',
    'include' => ['contact_organization_user', 'assigned_teammate'],
]);

foreach ($conversations as $conversation) {
    echo "{$conversation->display_number}  {$conversation->subject}\n";
    echo "  {$conversation->last_message_type}: {$conversation->last_message_preview}\n";
    echo "  unread: {$conversation->unread_count}\n";
}
```

`unread_count` is counted per reader, so it is null when the desk had nobody to count it for.
`last_message_type` and `last_message_preview` describe the newest message the customer and the
desk exchanged; notes, activity rows and the desk's automatic notices never count as the last word.

A desk reads its queue by who owes the work. `assignment` takes `mine` (read off your own seat),
`unassigned` or `all`, and `has_unread` narrows to threads with something you have not read.
Counts per state and per assignment — each under the rest of the filters you sent, across every
page — ride along in the response `meta` when you ask for them:

```php
<?php

$queue = $client->helpdeskConversations->list([
    'assignment' => 'mine',
    'has_unread' => true,
    'include_meta' => 'navigation_by_state,navigation_by_assignment',
]);

$byState = $queue->getMeta()['navigation_by_state'];           // pending, open, snoozed, …
$byAssignment = $queue->getMeta()['navigation_by_assignment']; // mine, unassigned, all
```

Reading moves forward only, with one exception: `markUnread()` clears your own read watermark so
the whole thread reads unread again, for you and nobody else.

```php
$client->helpdeskConversations->markUnread('org_hdconv_xxx');
```

## Answering

```php
<?php

$client->helpdeskConversationMessages->create('org_hdconv_xxx', [
    'type' => 'outgoing',
    'content' => 'We have reset it. Try again and tell us how you get on.',
    'content_type' => 'text',
]);

// Something for your colleagues rather than the customer
$client->helpdeskConversationMessages->create('org_hdconv_xxx', [
    'type' => 'note',
    'content' => 'Second time this month. Worth checking their SSO config.',
]);
```

Pass `deliver_email` as false to record a reply you have already sent by other means.

To take back a line that should not have been sent, `redact()` it. There is no edit and no delete:
a thread is a record.

```php
$client->helpdeskConversationMessages->redact('org_hdconv_xxx', 'org_hdconvmsg_xxx');
```

## Moving a thread along

```php
<?php

$client->helpdeskConversations->assign('org_hdconv_xxx', [
    'organization_helpdesk_teammate_id' => 'org_hdteammate_xxx',
]);

$client->helpdeskConversations->snooze('org_hdconv_xxx', [
    'snoozed_until' => '2026-09-22T09:00:00Z',
]);

$client->helpdeskConversations->resolve('org_hdconv_xxx');
$client->helpdeskConversations->close('org_hdconv_xxx');
$client->helpdeskConversations->reopen('org_hdconv_xxx');
```

Two threads about the same thing fold together. The merged one stays as a closed stub pointing at
the survivor, so an old link still resolves:

```php
$client->helpdeskConversations->merge('org_hdconv_duplicate', [
    'organization_helpdesk_conversation_id' => 'org_hdconv_survivor',
]);
```

Marking spam withholds the attachments and tells the sender nothing:

```php
$client->helpdeskConversations->markSpam('org_hdconv_xxx');
$client->helpdeskConversations->unmarkSpam('org_hdconv_xxx');
```

Blocking the sender writes the contact's address, or with `whole_domain` their domain, to the
organization's [blocklist](blocked-identifiers.md) and files the thread as spam, so nobody is left
owing a blocked sender a reply. A thread with no contact email answers `422`.

```php
$client->helpdeskConversations->blockSender('org_hdconv_xxx', [
    'whole_domain' => false,
    'reason' => 'Cold outreach',
]);
```

The `lifecycle` include says what the desk's timers will do next on a thread — `reminder`,
`auto_resolve` and the `reopen_window` in which an emailed reply still reopens it — each with a
`status`, its date (`due_at`, or `ends_at` for the window) and the `source` of the setting it
follows (`inbox`, `organization` or `platform`):

```php
$conversation = $client->helpdeskConversations->retrieve('org_hdconv_xxx', ['include' => 'lifecycle']);

echo $conversation->lifecycle['auto_resolve']['due_at'];
```

## Attachments

A file can be uploaded before there is a thread to hang it on. Stage it, then pass the id when you
create the conversation or the message.

```php
<?php

$staged = $client->helpdeskConversationAttachments->stage(['file' => $fileHandle]);

$client->helpdeskConversationMessages->create('org_hdconv_xxx', [
    'type' => 'outgoing',
    'content' => 'The corrected invoice is attached.',
    'attachment_ids' => [$staged->id],
]);

$bytes = $client->helpdeskConversationAttachments->download('org_hdconv_xxx', 'org_hdconvatt_xxx');
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

Two switches decide where the chat may be embedded. `is_widget_enabled` turns the widget off
outright. `is_widget_origin_restricted` decides whether `widget_origins`, the allow-list of sites,
applies at all; it is off by default, because what proves a visitor is the portal session rather
than the page they are on. Inboxes that carried a list before these switches existed were migrated
as restricted, so nothing opened up by being migrated.

```php
$client->helpdeskInboxes->update('org_hdinbox_xxx', [
    'is_widget_origin_restricted' => true,
    'widget_origins' => ['https://app.example.com'],
]);
```

`auto_response_chat_delay_seconds` is how long the chat waits before acknowledging, so a teammate
can speak first. `pending_conversations_count` and `open_conversations_count` are read-only.

Desk-wide defaults live in settings, and an inbox that sets the same field wins. Ask for
`inbox_defaults` to see what an empty inbox field falls back to, and where that value comes from:

```php
$settings = $client->helpdeskSettings->retrieve(['include' => 'inbox_defaults']);
$client->helpdeskSettings->update(['close_after_resolved_days' => 14]);

echo $settings->inbox_defaults['quarantine_score_threshold']['value'];
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

Every message the desk took in is kept with what it decided to do with it: its `interpretation`,
and a coarser `category` that answers whether anybody needs to look — `held`, `tickets`,
`marketing`, `automated`, `spam`, `failed`, or `unclassified` for mail nobody ruled on. The body,
the raw headers and the sender weighing are withheld unless you ask for them by name.

```php
<?php

$emails = $client->helpdeskInboundEmails->list([
    'category' => 'held',
    'include' => ['content', 'headers', 'trust_assessment'],
    'include_meta' => 'navigation_by_category',
]);

// After fixing a routing rule, run one back through the pipeline
$client->helpdeskInboundEmails->reprocess('org_hdinmail_xxx');
```

### Connecting a mailbox

The desk reads mail through an email account's mailbox: the `imap_*` half of an SMTP credential,
beside the half it sends with. `synced_since_at` is the day to import from.

```php
$client->apiCredentials->update('org_apicred_xxx', [
    'credentials' => [
        'imap_host' => 'imap.example.com',
        'imap_port' => 993,
        'imap_encryption' => 'ssl',
        'imap_username' => 'support@example.com',
        'imap_password' => 'app-password',
    ],
    'synced_since_at' => '2026-09-01',
]);
```

`credentials` is write-only and a write merges into it: keys you send are set, keys you send empty
are removed, and keys you leave out are kept, so rotating the SMTP password leaves the mailbox in
place. A credential for any other service refuses the `imap_*` keys. The credential answers
`receives` (it has a mailbox), `is_active_sender` (it is the account recipients will see mail from),
`synced_since_at` and `last_synced_at`, never the secrets themselves.

`misc->testEmail()` checks a whole account: pass `organization_api_credential_id` (an
organization-wide account; a member's own account is never opened), and `send_to` if you also want
a send check. The answer's `checks` has a `send` and a `receive` entry for whatever
was tested; a failed check answers `422` carrying them under `metadata`:

```php
use Enlivy\Exception\ValidationException;

try {
    $result = $client->misc->testEmail([
        'organization_api_credential_id' => 'org_apicred_xxx',
        'send_to' => 'me@example.com',
    ]);
} catch (ValidationException $e) {
    $checks = $e->getBody()['metadata']['checks'] ?? [];
}
```

### Holding mail from strangers

An inbox with `is_quarantine_enabled` weighs each sender it does not know and holds the message
whole, rather than opening a conversation, when the `trust_score` reaches the inbox's
`quarantine_score_threshold`. Existing inboxes keep it off; inboxes created through the API hold by
default. `trust_assessment` shows the weighing: every signal the desk looks for, whether it fired,
the score and the threshold.

A person decides what held mail was. Promoting releases it into a conversation; only held, `bulk`,
`auto_reply` and unclassified mail can be promoted. `trust_sender` (default true) and
`trust_domain` also write trust rules, which decide the sender's *next* message rather than this
one:

```php
$client->helpdeskInboundEmails->promote('org_hdinmail_xxx', ['trust_domain' => true]);
```

Mail the desk did not open can be filed by hand as `bulk`, `discarded` or `spam`. `apply_to_sender`
and `apply_to_domain` also add a discard rule, so that sender's (or domain's) next message is filed
as `discarded`, whichever reading you chose for this one. A delivery report
cannot be filed, and neither can mail that already became a conversation.

```php
$client->helpdeskInboundEmails->classify('org_hdinmail_xxx', [
    'interpretation' => 'bulk',
    'apply_to_sender' => true,
]);

$client->helpdeskInboundEmails->blockSender('org_hdinmail_xxx', ['whole_domain' => true]);
```

A message's stored body ages out after thirty days, and mail that opened nothing is removed after
ninety; the headers and addresses stay for as long as the row does. Promoting needs the body, so it
is refused once the body is gone. `fetchOriginal()` reads the body back from the mailbox the
message arrived in and returns it in `content` without storing it again:

```php
$email = $client->helpdeskInboundEmails->fetchOriginal('org_hdinmail_xxx');
echo $email->content_type; // html or text
```

Routing rules decide which inbox a message lands in, in order. Besides `route` and `discard`, a
rule's `action` can be `trust`, which vouches for a sender: the desk does not hold their mail or
file it away as spam, a mailing or an automatic reply. A `discard` rule beats a `trust` rule
whatever their order, and neither lifts a block or a bounce:

```php
$client->helpdeskInboundEmailRules->create([
    'name' => 'Billing questions',
    'from_domain' => 'accounts.example.com',
    'organization_helpdesk_inbox_id' => 'org_hdinbox_billing',
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
$events = $client->helpdeskVisitors->events('org_hdvisitor_xxx');

$client->helpdeskVisitors->block('org_hdvisitor_xxx');
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

$portal->helpdeskConversations->rate('org_hdconv_xxx', ['rating' => 5, 'rating_comment' => 'Quick and clear.']);
```

The inbox is not the customer's to pick; the organization's default customer-facing one is used.

## Related

- [Embedded Support](../embedded-support.md) — putting an authenticated chat on your own application
- [Customer Portal](customer-portal.md) — sessions and permissions
- [Prospects](prospects.md) — a conversation can become a lead

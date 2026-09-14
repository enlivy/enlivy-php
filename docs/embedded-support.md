# Embedded Support (authenticated chat)

Put a support chat on your own application and have it know who the customer is,
without ever putting an API key in the browser.

This guide covers the identity half. Managing the desk itself — inboxes,
conversations, teammates — is [Helpdesk](organization/helpdesk.md).

## The model: a session, not a signature

A widget that lets a visitor type their email knows nothing. Anyone can type
anyone's address, so the desk shows the name but cannot stand behind it.

The usual fix elsewhere is to have your server sign the email with a shared
secret and send the signature along. Enlivy does **not** do that, on purpose. A
signature proves your server wrote the address; it says nothing about what that
person is entitled to see. Honouring one would mean the desk grants access on
your say-so, and a leaked or misused secret would mint access to anything.

Instead, identity in the chat is a **customer portal session** — the same
short-lived, permissioned session that already governs what a customer may read
in the portal. Your backend creates one for the customer it has already
authenticated, and the widget presents it. The desk then marks that visitor
verified because it issued the session itself, not because it was told to.

```
your server (holds the API key)                    the browser                    Enlivy
  authenticates your user                              │                             │
  POST /organizations/{org}/user-client-portal-sessions│                             │
  ──────────────────────────────────────────────────────────────────────────────────►│
  ◄────────────────────────────────────────────────────────────────────────────────  │
  { token, expires_at }                                │                             │
        │  render the token into the page              │                             │
        └────────────────────────────────────────────► │  identify with the token    │
                                                       │ ───────────────────────────►│
                                                       │                    verified ✓
```

The API key never leaves your server. The browser only ever holds a session
token that is scoped to one customer and expires.

## Minting the session

Use `magic_authentication` — your application has already authenticated this
person, so a second verification code would ask them to prove what you have
just proven.

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient([
    'api_key' => '1|your_token',
    'organization_id' => 'org_xxx',
]);

$session = $client->userClientPortalSessions->create([
    'name' => 'Support chat for ' . $customer->email,
    'organization_user_id' => 'org_user_xxx',

    'authentication_method' => 'magic_authentication',
    'validity_hours' => 72,
]);

// Hand $session->token to the page. Never the API key.
echo $session->token;
echo $session->expires_at;
```

Identify the customer by `organization_user_id` when you store it, or by
`email` when you do not — the address must already belong to an organization
user, so this is a lookup, not a way to invent one.

## How long the token should live

**Use 72 hours. Never go below 24 when the token is rendered into a page.**

The API will let you go to 365 days. Do not read that as permission: a token
that lives a year is a standing credential for that customer's identity sitting
in a page. Pick the shortest window that still outlives a visit.

A chat widget is not a request. It is written into the markup once and stays
alive as long as the tab does, so the token has to outlive the visit rather
than the page load:

| | why |
|---|---|
| **24 hours — the floor** | a working day must never straddle an expiry; somebody who opens your app in the morning still has a working chat after lunch |
| **72 hours — the recommendation** | the gap between two visits to the same page is usually a weekend, and Monday should still know who they are |

An expired token is **worse than sending none at all**. Identify carries the
name and email on the same call that carries the token, so a dead token fails
the whole call: the visitor arrives at the desk as an anonymous browser, and
the name is lost with it. Sending no token at all still identifies them, just
without verification. That asymmetry is why the floor is a floor.

If you re-mint on every page load you can safely go shorter, since a reload
issues a fresh token. The floor is for the common case, where the token is
rendered once.

## Two ways to embed

### Your own front end

If you are writing the browser code yourself, mint the session as above, send
it with the customer's details when the chat opens, and the visitor becomes
verified. This works today.

Refresh the token on the same cadence you refresh anything else about the
signed-in user. A page that lives longer than the token should fetch a new one
rather than let the next identify fail.

### The hosted widget

If you load the Enlivy widget bundle, it takes the customer's name and email
from its own configuration and the visitor becomes **identified but not
verified** — the desk shows who they say they are, with no claim from us that
it checked. Forwarding a session token through the bundle is not yet wired; use
your own front end when verification matters.

## Identified is not verified

Two different states, and the desk distinguishes them:

| state | what it means |
|---|---|
| **identified** | an email is attached to the browser. It may have been typed by anyone. |
| **verified** | the email came from a portal session we issued. |

Both show a name to the agent. Only the second is evidence.

## Keeping it safe

1. **The API key stays on your server.** Nothing in this flow needs it in the browser.
2. **One session per customer.** Never reuse a token between people; it carries their identity.
3. **Grant only what they need.** `permissions` scopes what the session reaches in the portal — an empty or narrow list is fine for chat.
4. **Re-mint rather than extend.** Issue a fresh token when the old one nears expiry.
5. **Treat the token like a password in transit.** Render it over HTTPS, and do not log it or put it in a URL.

## Related

- [Customer Portal](organization/customer-portal.md) — sessions, permissions, authentication methods
- [Helpdesk](organization/helpdesk.md) — inboxes, conversations, teammates
- [Authentication](authentication.md) — API keys and OAuth

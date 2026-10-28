# Developer Apps

Build an App that Enlivy shows: widgets, pages, menu points and records administered through
Enlivy's screens, all declared in one bundle (a manifest and its presentation documents) and answered
by your own service. This page is the developer's side: registering, revising, publishing, the App's
OAuth clients and secrets, and the drafts an agent makes. Installing and administering an App in an
organization is in [Apps](organization/apps.md).

The contract itself (the manifest, fields, resources, presentations, the protocol your service
answers) is served by the API as the App builder discovery, one section at a time. Start there:

```php
<?php

use Enlivy\EnlivyClient;

$client = new EnlivyClient(['api_key' => '1|your_token']);

$index = $client->discovery->apps();
foreach ($index->start_here as $step) {
    echo "{$step->step}: " . implode(', ', $step->sections) . "\n";
}

$manifest = $client->discovery->appsSection('manifest');
$schema = $client->discovery->appsSchema('manifest');
```

Sections: `overview`, `manifest`, `fields`, `resources`, `pages_and_menu`, `presentations`,
`translations`, `protocol`, `connection`, `registration`, `development`, `scopes`, `example`.
Schemas: `manifest`, `presentation`, `field`, `provider_request`, `provider_response`,
`provider_access`, `connection`, `sealed_secret`. All public, no credentials needed.

## Who may call

The developer's own Apps live under `/users/{user}/apps`, so every method takes the user id first.
These routes answer a signed-in dashboard session, or an OAuth token holding the `apps:develop`
scope, which only Enlivy's own clients (the CLI, the MCP gateway) ask for and which reaches only its
own person's Apps; an API key is refused with a `403`. Registration drafts are listed, made, cancelled
and claimed by an OAuth token holding `apps:draft` (its own client's drafts) or by the person's API
key (all of theirs); approving one answers a dashboard session only. The App builder discovery and
the provider keys are public.

## Bundles

A bundle is `manifest` (one object) and `documents` (an object keyed by document key). Both are sent
as JSON objects, so an App without presentation documents sends `new \stdClass`: a PHP `[]` is sent as
a list, which the API refuses.

```php
<?php

$bundle = [
    'manifest' => [
        'format' => 'enlivy.app-manifest.v1',
        'key' => 'links',
        'version' => '1.0.0',
        'name_lang_map' => ['en' => 'Links'],
        'distribution' => 'personal',
        // ...
    ],
    'documents' => new \stdClass(),
];

$check = $client->userApps->validate('usr_xxx', $bundle);

if (! $check->valid) {
    foreach ($check->issues as $issue) {
        echo "{$issue->code}: {$issue->message}\n";
    }
}
```

## Registering, revising, publishing

```php
<?php

$app = $client->userApps->register('usr_xxx', $bundle);
echo $app->id;
print_r($app->revisions);      // revision 1, not yet published
print_r($app->installations);  // where it is installed, among the organizations you work in

$revision = $client->userApps->submitRevision('usr_xxx', $app->id, $nextBundle);
echo $revision->revision; // Enlivy's number
echo $revision->version;  // your major.minor.patch, higher than the latest

$client->userApps->publishRevision('usr_xxx', $app->id, $revision->id);

$apps = $client->userApps->list('usr_xxx');
$app = $client->userApps->retrieve('usr_xxx', $app->id);
```

Stored revisions never change. A revision whose `review` is `required` asked for a sensitive scope and
waits on Enlivy before organizations can install it. A personal App installs in every organization
you belong to with a back office role; `register()` may name an `organization_id` you manage Apps in,
which limits nothing. Where you manage services, `appInstallations->create()` installs it; organizations
you only belong to install it through an
[install request](organization/apps.md#asking-an-organization-to-install-it) made with
`appInstallRequests->create()`, which `installRequests()` lists:

```php
<?php

foreach ($client->userApps->installRequests('usr_xxx', $app->id) as $request) {
    echo "{$request->organization_id}: {$request->status}\n";
}
```

## Secrets arrive sealed

Three answers carry a secret: a new OAuth client's, a rotated client secret, and a rotated webhook
secret. Each takes a `recipient_key`: an X25519 public key you made on your own machine, 32 bytes,
base64url without padding. The answer carries the secret sealed to that key (`sealed_secret`) and
nothing in plain text, so a log or a transcript that holds the answer holds only ciphertext. A
dashboard sign-in may leave the key out and receives the secret in plain text once; an OAuth grant
must send one (`422` on `recipient_key` otherwise).

The sealed object is `format`, `kind`, `for`, `recipient`, `epk`, `nonce` and `ciphertext`
(`discovery->appsSchema('sealed_secret')` has the opening recipe). The SDK carries it as data and
does not open it; open it on the machine that holds the private key.

```php
<?php

$recipientKey = 'your_x25519_public_key_base64url';

// A confidential client for the App, named after it, redirecting to its provider origin, attached in the same step
$made = $client->userApps->createOAuthClient('usr_xxx', $app->id, [
    'redirect_uris' => ['https://links.example.com/oauth/callback'],
    'recipient_key' => $recipientKey,
]);
echo $made->oauth_client->id;
$sealed = $made->oauth_client->sealed_secret; // open locally

// Later: a new secret; the previous one keeps working for the overlap
$rotated = $client->userApps->rotateOAuthClientSecret('usr_xxx', $app->id, $made->oauth_client->id, [
    'recipient_key' => $recipientKey,
]);

// One of your existing clients may act for the App instead
$client->userApps->attachOAuthClient('usr_xxx', $app->id, 'oac_xxx');
$client->userApps->detachOAuthClient('usr_xxx', $app->id, 'oac_xxx');
```

The App's webhook secret signs every organization's deliveries to its webhook. It is answered only
when rotated; reading it answers when it last changed:

```php
<?php

$state = $client->userApps->webhookSecret('usr_xxx', $app->id);
echo $state->rotated_at;
echo $state->previous_secret_valid_until;

$secret = $client->userApps->rotateWebhookSecret('usr_xxx', $app->id, ['recipient_key' => $recipientKey]);
$sealed = $secret->sealed_secret;
```

Both answer `404` when the manifest declares no webhooks.

## Drafts an agent makes

An agent (an OAuth token with the `apps:draft` scope) drafts an App for its person; the person
approves it signed in, at the request's `approval_url`; the agent then claims the created ids and the
sealed secrets, once. Approval registers the App, publishes revision 1, makes its OAuth client when
the manifest uses OAuth or a data connector, and installs it in the organization the draft names,
with the permissions the person left on. A request lapses after 24 hours.

```php
<?php

$draft = [
    'manifest' => $bundle['manifest'],
    'documents' => $bundle['documents'],
    'organization_id' => 'org_xxx',
    'redirect_uris' => ['https://links.example.com/oauth/callback'],
    'permissions' => [
        'oauth_scopes' => ['accounting:read'],
        'connector_capabilities' => ['links.read', 'links.write'],
        'context_fields' => [],
    ],
    'recipient_key' => $recipientKey,
];

$client->appRegistrationRequests->validate('usr_xxx', $draft); // 422 names what is wrong

$request = $client->appRegistrationRequests->create('usr_xxx', $draft);
echo $request->approval_url; // the person opens this and approves

// Poll until approved, then claim once
$request = $client->appRegistrationRequests->retrieve('usr_xxx', $request->id);

if ($request->status === 'approved') {
    $claimed = $client->appRegistrationRequests->claim('usr_xxx', $request->id);
    echo $claimed->user_app_id;
    echo $claimed->oauth_client_id;
    echo $claimed->organization_app_installation_id;
    foreach ($claimed->sealed_secrets as $sealed) {
        echo "{$sealed->kind} for {$sealed->for}\n"; // open locally
    }
}

$client->appRegistrationRequests->cancel('usr_xxx', $request->id);
$client->appRegistrationRequests->list('usr_xxx');
```

Approving happens in the dashboard only, at the `approval_url`; the person may switch off optional
permissions there, the required ones stay and nothing outside the draft is added. There is no SDK
method for it.

Statuses are `Enums\Apps\RegistrationRequestStatuses`: `pending`, `approved`, `claimed`, `cancelled`,
`expired`.

## Verifying what Enlivy sends your service

Enlivy calls your service with a short lived access token for each widget or page it draws. The public
keys that verify those tokens are a JWK set at `GET /misc/app-provider-keys`, public, to be cached for
a minute:

```php
<?php

$keys = $client->frontend->appProviderKeys()->keys;
```

The token's claims, the request and response shapes and the refusal reasons your service may answer
(`Enums\Apps\ProviderAccessRefusals`) are in the `protocol` and `connection` sections of the App
builder discovery.

## Signing in from a terminal

A CLI or assistant that develops Apps signs in with the device flow: see
[OAuth Server](oauth.md#device-sign-in).

## Related

- [Apps](organization/apps.md): installing your App in an organization, or asking one to
- [Event Destinations](organization/event-destinations.md): how a destination's signing secret is handed over the same way

# Errors

Every failed request throws an `Enlivy\Exception\ApiException` or one of its subclasses, chosen by
status. The exception keeps the status, the decoded body and the headers.

| Status | Exception |
|--------|-----------|
| `401` | `AuthenticationException` |
| `403` | `ForbiddenException` |
| `404` | `NotFoundException` |
| `422` | `ValidationException`, with `errors()` keyed by field |
| `429` | `RateLimitException`, with `retryAfter()` in seconds |
| `5xx` | `ServerException` |
| anything else (`400`, `402`, `409`, `410`, `498`) | `ApiException` |

A connection that never reached the API throws `ApiConnectionException`; a call the SDK refuses before
sending (an unknown include or filter, a missing organization id) throws `InvalidArgumentException`.

An API key or an OAuth token may read 300 times a minute and write 60 times a minute, counted apart,
so a loop of reads never blocks a save. Past either figure the API answers `429` with `Retry-After`,
which the SDK honours when it retries (`max_retries`).

```php
<?php

use Enlivy\Exception\ApiException;
use Enlivy\Exception\ValidationException;

try {
    $client->invoices->create($params);
} catch (ValidationException $e) {
    $errors = $e->errors(); // ['field' => ['message']]
} catch (ApiException $e) {
    $e->getStatusCode();
    $e->getMessage();  // the API's message, written for a person in the request's language
    $e->getBody();     // the decoded body
    $e->getHeaders();
}
```

## Refusals

A refusal tells a client two things: whether asking again can help, and what to do instead. The
status answers the first, the refusal code the second.

- `422`: the input as sent is wrong; `errors()` names the fields.
- `403`: the caller may not do this.
- `402`: the plan or its capacity does not cover it.
- `409`: the current state of the record or organization blocks it, in one of two ways below.
- `503`: something Enlivy depends on is unavailable; retry later.

A refusal the client should act on beyond showing its message carries `error` in the body:
`{message, error: {code, ...facts}}`. `code` is a stable snake_case name that is never renamed; the
facts are what a client needs to act. The SDK reads them on every `ApiException`:

| Method | Returns |
|--------|---------|
| `getRefusalCode()` | the `error.code`, or null when the answer carries none |
| `getFacts()` | everything under `error` except `code`, as an array |
| `isConflict()` | true for a `409` without a code |

### 409 has two kinds

A `409` **without a code** means the record moved since the caller read it: a stale revision, a
concurrent write, work already in flight, a uniqueness race after validation passed. Reread the record
and send again, once.

A `409` **with a code** means retrying cannot change the answer: setup is missing, a final state was
reached, the wrong browser is asking. Do what the code names first.

```php
<?php

use Enlivy\Exception\ApiException;

try {
    $client->invoices->update('org_inv_xxx', $changes);
} catch (ApiException $e) {
    if ($e->isConflict()) {
        $fresh = $client->invoices->retrieve('org_inv_xxx');
        // apply the changes to what the record is now, then send again
    }
}

try {
    $client->invoices->email('org_inv_xxx', ['send_to' => 'client@example.com']);
} catch (ApiException $e) {
    if ($e->getRefusalCode() === 'email_sending_not_connected') {
        $facts = $e->getFacts(); // ['can_connect' => true]
        // connect the organization's email account first
    }
}
```

### Codes you will meet

| Status | Code | Facts | What to do |
|--------|------|-------|------------|
| `403` | `route_denied` | `denied_by` (`membership`, `scope`, `role`, `organization`, `first_party`), `required_abilities`, `required_scopes` | ask for the scopes or the role the facts name |
| `402` | `tenant_feature_required` | `required_pack`, `alternatives`, `metered_domain`, `used_this_period`, `limit`, `period_resets_at` | activate the pack in tenant billing |
| `402` | `tenant_capacity_required` | `required_addon`, `metered_domain`, `used_this_period`, `limit`, `period_resets_at` | add the capacity |
| `402` | `storage_limit_exceeded` | the quota reached | free storage or add capacity |
| `403` | `feature_pack_not_allowed_for_account_type` | `required_pack_any_of` | the account type cannot hold the pack |
| `409` | `email_sending_not_connected` | `can_connect` | connect the organization's email account; the `organizations` include `email_sending` answers the same check before a send |
| `409` | `invoice_not_pushable`, `peppol_institution_not_connected`, `organization_tax_identifier_missing` | | an e-invoice push needs an issued Enlivy invoice, a connected network and the organization's tax identifier |
| `409` | `billing_schedule_already_cancelled`, `proposal_conversion_refreshed`, `oauth_client_has_no_secret`, `authenticator_app_exists` | `proposal_conversion_refreshed` carries the refreshed figures in `billed_conversion` | a final state was reached or the figures moved; show the way forward |
| `503` | `integration_not_configured` | | the server has no keys for that service |

The codes an operation can answer are enumerated on its OpenAPI response. `meta` repeats some facts
for older readers; read `error` through `getFacts()`.

### OAuth token errors

The token endpoints answer their refusals in the OAuth shape, `{error, error_description}`, with
`400` or `401`: `invalid_client`, `invalid_grant`, `invalid_scope`, `authorization_pending`,
`slow_down`, `access_denied`, `expired_token`. Read them from `getBody()['error']`;
`getRefusalCode()` is null for these, since the OAuth `error` is a string, not a coded refusal.

## Retries

`429` and `5xx` answers, and connection failures, are retried with backoff for `GET` requests and for
writes that carry an `Idempotency-Key` (see [Request Options](../README.md#request-options)). A `409`
is never retried by the SDK: decide with `isConflict()`.

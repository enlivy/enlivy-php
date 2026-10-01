# Checkout example

A small PHP site that sells a billing package end to end with this SDK: connect with an API key,
pick an organization, a package and what the buyer chooses in it, pick the customer, preview the
price, open a checkout session, take the payment with the checkout embed, then watch the session
settle. It also corrects and expires sessions and lists them.

It is a playground, not a template to deploy: it keeps the API key in the visitor's PHP session and
writes only to a sandbox organization. See [Checkout Sessions](../../docs/organization/checkout-sessions.md)
for the integration itself.

## Run it

```bash
composer install
php -S localhost:8080 -t examples/checkout/public
```

Open http://localhost:8080 and paste an API key that can read the organization's billing packages,
customers and checkout sessions, and manage checkout sessions in a sandbox.
Tick "Remember the key in this browser" to keep it in this browser's `localStorage` and have it filled
in next time; "Forget it" removes it. Leave it unticked on a shared machine.

## What each step calls

| Step | SDK |
|------|-----|
| Connect, pick an organization | `organizations->list()`, `organizations->retrieve()` |
| Packages | `billingPackages->list(['include' => ['groups', 'payment_plans', 'subscription_terms', 'contract_templates']])` |
| The buyer's choices | [`OrderForm`](src/OrderForm.php) turns a package into a cadence or payment plan, group items and quantities, and back into an order |
| Customer | `organizationUsers->list(['can_be_invoiced' => true, 'q' => …])` |
| Price | `misc->calculateBillingPackagePrice()` |
| Open | `checkoutSessions->create($order, new RequestOptions(idempotencyKey: …))`, then the `client_token` from the response meta |
| Pay | `Enlivy\Embed\CheckoutEmbed` renders the checkout embed from the organization's `customer_portal_base_url` |
| Settle | `checkoutSessions->retrieve()` every few seconds; in production, the `checkout_session.completed` event ([`webhook.php`](public/webhook.php)) |
| Correct, expire, list | `checkoutSessions->update()`, `->expire()`, `->list()` |

## What a real host does differently

- The API key lives in server configuration, never in a session or the browser.
- The order comes from the host's own cart; the customer is the signed-in buyer, created or found
  with `organizationUsers` first.
- The client token is stored with the host's pending order and handed only to that buyer's page.
- Access is granted from the `checkout_session.completed` event or a retrieve that reads
  `complete`, never from the embed's `confirm` event alone.

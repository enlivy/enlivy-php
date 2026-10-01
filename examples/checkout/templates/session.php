<?php

use Enlivy\Embed\CheckoutEmbed;
use Enlivy\Enums\CheckoutSession\Statuses;
use Example\Checkout\OrderForm;
use Example\Checkout\Playground;
use Example\Checkout\View;

/** @var CheckoutEmbed|null $embed */
$open = $session->status === Statuses::OPEN->value;
$data = $session->toArray();
$package = $data['billing_package']['data'] ?? $data['billing_package'] ?? null;
$customer = $data['receiver_user']['data'] ?? $data['receiver_user'] ?? null;
$back = 'page=session&id=' . urlencode($session->id);
?>
<p><a class="link" href="<?= h(View::url(['page' => 'sessions'])) ?>">← Sessions</a></p>
<h1><?= h(OrderForm::text($package['name_lang_map'] ?? null) ?: 'Checkout session') ?></h1>
<p class="muted mono"><?= h($session->id) ?></p>

<div class="split">
    <div class="stack">
        <section class="card">
            <h2>Status</h2>
            <dl id="status" data-url="<?= h(View::url(['page' => 'status', 'id' => $session->id])) ?>" data-open="<?= $open ? '1' : '0' ?>">
                <dt>Status</dt><dd data-key="status"><span class="badge"><?= h($session->status) ?></span></dd>
                <dt>Payment</dt><dd data-key="payment_status"><?= h($session->payment_status) ?></dd>
                <dt>Paying by</dt><dd data-key="payment_method_kind"><?= h($session->payment_method_kind ?? 'not chosen') ?></dd>
                <dt>Schedule</dt><dd data-key="organization_billing_schedule_id" class="mono"><?= h($session->organization_billing_schedule_id ?? '') ?></dd>
                <dt>Invoice</dt><dd data-key="organization_invoice_id" class="mono"><?= h($session->organization_invoice_id ?? '') ?></dd>
                <dt>Proforma</dt><dd data-key="organization_proforma_invoice_id" class="mono"><?= h($session->organization_proforma_invoice_id ?? '') ?></dd>
                <dt>Customer</dt><dd><?= h($customer['name'] ?? $session->organization_receiver_user_id) ?></dd>
                <dt>Mode</dt><dd><?= h($session->mode) ?></dd>
                <dt>Expires</dt><dd><?= h($session->expires_at) ?></dd>
                <dt>Price held until</dt><dd><?= h($session->price_held_until ?? '') ?></dd>
                <dt>Your order id</dt><dd><?= h($session->client_reference_id ?? '') ?></dd>
            </dl>
            <p class="hint">
                <?= $open ? 'Updates on its own every few seconds. ' : '' ?>Give the customer what they bought only once this reads complete.
            </p>
        </section>

        <section class="card">
            <h2>What they buy</h2>
            <?= View::partial('price', ['priced' => $data]) ?>
        </section>

        <?php if (Playground::isSandbox()): ?>
            <section class="card">
                <h2>Change details</h2>
                <p class="hint">You can change your reference, the campaign, metadata and, while it is open, push the expiry later. What the customer buys never changes.</p>
                <form method="post" class="stack">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id" value="<?= h($session->id) ?>">
                    <input type="hidden" name="back" value="<?= h($back) ?>">
                    <div class="row wrap">
                        <label>Your order id <input name="client_reference_id" value="<?= h($session->client_reference_id ?? '') ?>"></label>
                        <label>Channel <input name="source_channel" value="<?= h($session->source_channel ?? '') ?>"></label>
                        <label>Campaign <input name="source_campaign" value="<?= h($session->source_campaign ?? '') ?>"></label>
                        <?php if ($open): ?>
                            <label>Expires at <input type="datetime-local" name="expires_at"></label>
                        <?php endif; ?>
                    </div>
                    <?php $metadata = (array) ($data['metadata'] ?? []); $i = 0; ?>
                    <?php foreach ($metadata + ['' => ''] as $key => $metaValue): ?>
                        <div class="row">
                            <input name="metadata[<?= $i ?>][key]" value="<?= h($key) ?>" placeholder="key" size="10">
                            <input name="metadata[<?= $i ?>][value]" value="<?= h($metaValue) ?>" placeholder="value">
                        </div>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                    <div class="actions">
                        <button name="action" value="update" class="secondary">Save corrections</button>
                        <?php if ($open): ?>
                            <button name="action" value="expire" class="danger">Expire now</button>
                        <?php endif; ?>
                    </div>
                </form>
            </section>
        <?php endif; ?>
    </div>

    <section class="card">
        <h2>Payment</h2>
        <?php if ($embed !== null): ?>
            <p class="hint">
                This is the payment form your customer sees on your page.
                <span class="theme-switch">Theme:
                    <a class="link" href="<?= h(View::url(['page' => 'session', 'id' => $session->id, 'theme' => 'light'])) ?>">light</a>
                    <a class="link" href="<?= h(View::url(['page' => 'session', 'id' => $session->id, 'theme' => 'dark'])) ?>">dark</a>
                </span>
            </p>
            <div class="host">
                <?= $embed->html() ?>
            </div>
            <details class="inline-details"><summary>Events from the payment form</summary>
            <ol id="events" class="events"></ol></details>
        <?php elseif (! $open): ?>
            <p class="muted">This session is <?= h($session->status) ?>; there is nothing to pay.</p>
        <?php elseif (! $portal): ?>
            <p class="muted">This organization has no customer portal address, so the embed has nowhere to load from.</p>
        <?php else: ?>
            <p class="muted">This checkout was opened elsewhere, so this browser does not hold its payment token. Open a new checkout to try paying.</p>
        <?php endif; ?>
    </section>
</div>

<details class="card quiet">
    <summary>What this page calls in the SDK</summary>
    <ul class="plain">
        <li><code>$client->checkoutSessions->retrieve($id, ['include' => [...]])</code> reads the session; the status box repeats it every few seconds.</li>
        <li><code>(new CheckoutEmbed($portalBaseUrl, $clientToken, $packageId))->html()</code> renders the payment form from <span class="mono"><?= h($embed?->loaderUrl() ?? (($portal ?? '') . '/embed.js')) ?></span>.</li>
        <li><code>$client->checkoutSessions->update($id, [...])</code> and <code>->expire($id)</code> change or close it.</li>
        <li>In production, the <code>checkout_session.completed</code> event tells your server it was paid.</li>
    </ul>
    <details class="inline-details">
        <summary>Raw session</summary>
        <pre><?= View::json($data) ?></pre>
    </details>
</details>

<script src="/session.js"></script>

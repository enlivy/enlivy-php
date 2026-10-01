<?php

use Enlivy\Enums\BillingPackage\SelectionMode;
use Example\Checkout\OrderForm;
use Example\Checkout\Playground;
use Example\Checkout\View;

/** @var OrderForm $form */
$value = fn (string $key, mixed $default = null): mixed => $input[$key] ?? $default;
$quantity = fn (string $id, mixed $default): mixed => $input['quantity'][$id] ?? $default;
$refusals = $form->refusals();
$currencies = $form->currencies();
$customerName = fn ($customer): string => $customer->name ?: trim($customer->first_name . ' ' . $customer->last_name);
?>
<p><a class="link" href="/">← All packages</a></p>
<h1><?= h($form->name()) ?></h1>
<p class="lead">
    <?= $form->isSubscription() ? 'Subscription' : 'One-time purchase' ?>
    · <?= h(implode(' or ', $form->paymentMethods()) ?: 'no payment method') ?>
</p>

<?php if ($refusals !== []): ?>
    <p class="flash warn">A checkout cannot sell this package: <?= h(implode(' ', $refusals)) ?></p>
<?php endif; ?>

<div class="checkout-layout">
    <div class="stack">
        <section class="card">
            <h2><span class="step">1</span> Customer</h2>
            <form method="get" class="row">
                <input type="hidden" name="page" value="order">
                <input type="hidden" name="package" value="<?= h($form->id()) ?>">
                <input type="search" name="customer_q" value="<?= h($customerQuery) ?>" placeholder="Search by name or email" aria-label="Search customers">
                <button class="secondary">Search</button>
            </form>
            <select name="customer" form="order" aria-label="Customer">
                <option value="">Choose a customer…</option>
                <?php foreach ($customers as $customer): ?>
                    <option value="<?= h($customer->id) ?>" <?= $value('customer') === $customer->id ? 'selected' : '' ?>>
                        <?= h($customerName($customer)) ?><?= $customer->email ? ' · ' . h($customer->email) : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (count($customers) === 0): ?>
                <p class="hint">No customer matches. Customers are the organization's users who can be invoiced.</p>
            <?php endif; ?>
            <details class="inline-details" <?= ($value('customer') ?? '') === '' && isset($input['preview']) ? 'open' : '' ?>>
                <summary>No customer yet? See what a visitor would pay</summary>
                <div class="row wrap">
                    <label>Country <input name="country_code" form="order" value="<?= h($value('country_code', 'RO')) ?>" maxlength="2" size="3"></label>
                    <label class="check"><input type="checkbox" name="is_business_entity" form="order" value="1" <?= $value('is_business_entity') ? 'checked' : '' ?>> Buying as a business</label>
                </div>
                <p class="hint">Only the price preview works without a customer. Opening a checkout needs one.</p>
            </details>
        </section>

        <form method="post" id="order" class="card stack">
            <?= csrf_field() ?>
            <input type="hidden" name="package" value="<?= h($form->id()) ?>">
            <input type="hidden" name="customer_q" value="<?= h($customerQuery) ?>">
            <input type="hidden" name="back" value="<?= h('page=order&package=' . urlencode($form->id())) ?>">
            <input type="hidden" name="idempotency_key" value="<?= h($idempotencyKey) ?>">

            <h2><span class="step">2</span> What they buy</h2>

            <?php if ($form->isSubscription()): ?>
                <?php if (count($form->terms()) > 1): ?>
                    <fieldset>
                        <legend>Billing cycle</legend>
                        <div class="options">
                            <?php foreach ($form->terms() as $term): ?>
                                <label class="option">
                                    <input type="radio" name="term" value="<?= h($term['id']) ?>"
                                        <?= ($value('term') ?? ($term['is_default'] ? $term['id'] : null)) === $term['id'] ? 'checked' : '' ?>>
                                    <span class="option-body">
                                        <span class="option-title"><?= h(OrderForm::text($term['name_lang_map'] ?? null) ?: OrderForm::frequencyLabel($term['frequency'])) ?></span>
                                        <span class="option-meta"><?= h(OrderForm::frequencyLabel($term['frequency'])) ?><?= $term['trial_days'] ? ' · ' . (int) $term['trial_days'] . '-day free trial' : '' ?></span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </fieldset>
                <?php elseif ($form->terms() !== []): ?>
                    <?php $term = $form->terms()[0]; ?>
                    <input type="hidden" name="term" value="<?= h($term['id']) ?>">
                    <p class="muted">Billed <?= h(strtolower(OrderForm::frequencyLabel($term['frequency']))) ?><?= $term['trial_days'] ? ', after a ' . (int) $term['trial_days'] . '-day free trial' : '' ?>.</p>
                <?php endif; ?>

                <?php foreach ($form->groups() as $group): ?>
                    <?php $mode = $group['selection_mode'] ?? SelectionMode::SELECT_MANY->value; ?>
                    <fieldset>
                        <legend>
                            <?= h(OrderForm::text($group['name_lang_map'] ?? null) ?: 'Items') ?>
                            <span class="legend-note"><?= h(OrderForm::selectionLabel($group)) ?></span>
                        </legend>
                        <?php foreach (OrderForm::items($group) as $item): ?>
                            <div class="item">
                                <label class="check grow">
                                    <?php if ($mode === SelectionMode::FIXED->value): ?>
                                        <input type="checkbox" checked disabled>
                                    <?php elseif ($mode === SelectionMode::SELECT_ONE->value): ?>
                                        <input type="radio" name="group[<?= h($group['id']) ?>]" value="<?= h($item['id']) ?>"
                                            <?= ($input['group'][$group['id']] ?? ($item['is_default'] ? $item['id'] : null)) === $item['id'] ? 'checked' : '' ?>>
                                    <?php else: ?>
                                        <input type="checkbox" name="pick[<?= h($item['id']) ?>]" value="1"
                                            <?= (isset($input['pick']) ? isset($input['pick'][$item['id']]) : $item['is_default']) ? 'checked' : '' ?>>
                                    <?php endif; ?>
                                    <?= h(OrderForm::itemName($item)) ?>
                                </label>
                                <?php if ($group['allow_quantity'] ?? false): ?>
                                    <label class="qty-label">Qty
                                        <input type="number" name="quantity[<?= h($item['id']) ?>]" class="qty"
                                               min="<?= (int) ($item['min_quantity'] ?? 1) ?>"
                                               <?= ($item['max_quantity'] ?? null) !== null ? 'max="' . (int) $item['max_quantity'] . '"' : '' ?>
                                               value="<?= h($quantity($item['id'], $item['default_quantity'] ?? $item['min_quantity'] ?? 1)) ?>">
                                    </label>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </fieldset>
                <?php endforeach; ?>
            <?php else: ?>
                <fieldset>
                    <legend>How they pay</legend>
                    <div class="options">
                        <?php foreach ($form->plans() as $index => $plan): ?>
                            <label class="option">
                                <input type="radio" name="plan" value="<?= h($plan['id']) ?>"
                                    <?= ($value('plan') ?? ($index === 0 ? $plan['id'] : null)) === $plan['id'] ? 'checked' : '' ?>>
                                <span class="option-body">
                                    <span class="option-title"><?= h(OrderForm::text($plan['name_lang_map'] ?? null) ?: 'Plan ' . ($index + 1)) ?></span>
                                    <?php foreach (OrderForm::phases($plan) as $phase): ?>
                                        <?php $repeats = ($phase['frequency'] ?? 'one_time') !== 'one_time'; ?>
                                        <span class="option-meta">
                                            <?= (int) $phase['days_after_anchor'] === 0 ? 'At purchase' : 'After ' . (int) $phase['days_after_anchor'] . ' days' ?>,
                                            <?= $repeats ? h(strtolower(OrderForm::frequencyLabel($phase['frequency']))) . ', ' . ($phase['max_occurrences'] === null ? 'until cancelled' : (int) $phase['max_occurrences'] . ' times') : 'once' ?>
                                        </span>
                                        <?php foreach (OrderForm::lines($phase) as $line): ?>
                                            <span class="item">
                                                <span class="grow"><?= h(OrderForm::itemName($line)) ?></span>
                                                <?php if ($line['allow_quantity']): ?>
                                                    <span class="qty-label">Qty
                                                        <input type="number" name="quantity[<?= h($line['id']) ?>]" class="qty"
                                                               min="<?= (int) $line['min_quantity'] ?>"
                                                               <?= $line['max_quantity'] !== null ? 'max="' . (int) $line['max_quantity'] . '"' : '' ?>
                                                               value="<?= h($quantity($line['id'], $line['quantity'] ?? $line['min_quantity'])) ?>">
                                                    </span>
                                                <?php endif; ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </fieldset>
            <?php endif; ?>

            <?php if (count($currencies) === 1): ?>
                <input type="hidden" name="currency" value="<?= h($currencies[0]) ?>">
            <?php else: ?>
                <label>Currency
                    <select name="currency">
                        <?php foreach ($currencies as $currency): ?>
                            <option <?= $value('currency') === $currency ? 'selected' : '' ?>><?= h($currency) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            <?php endif; ?>

            <details class="inline-details">
                <summary>More options</summary>
                <div class="form-grid">
                    <label>Starts on <input type="date" name="start_at" value="<?= h($value('start_at')) ?>"></label>
                    <label>Checkout expires after (minutes) <input type="number" name="expires_in_minutes" min="30" max="1440" placeholder="1440" value="<?= h($value('expires_in_minutes')) ?>"></label>
                    <label>Your order reference <input name="client_reference_id" value="<?= h($value('client_reference_id', 'example-' . date('Ymd-His'))) ?>"></label>
                    <label>Campaign channel <input name="source_channel" value="<?= h($value('source_channel')) ?>" placeholder="newsletter"></label>
                    <label>Campaign name <input name="source_campaign" value="<?= h($value('source_campaign')) ?>"></label>
                    <label>Metadata
                        <span class="row">
                            <input name="metadata[0][key]" value="<?= h($input['metadata'][0]['key'] ?? 'source') ?>" aria-label="Metadata key">
                            <input name="metadata[0][value]" value="<?= h($input['metadata'][0]['value'] ?? 'enlivy-php example') ?>" aria-label="Metadata value">
                        </span>
                    </label>
                </div>
            </details>
        </form>
    </div>

    <aside class="card summary" id="price">
        <h2><span class="step">3</span> Price</h2>
        <?php if ($previewError !== null): ?>
            <p class="flash error"><?= h($previewError) ?></p>
        <?php elseif ($preview !== null): ?>
            <?= View::partial('price', ['priced' => $preview->toArray()]) ?>
        <?php else: ?>
            <p class="muted">Choose a customer and what they buy, then preview the price. Nothing is saved.</p>
        <?php endif; ?>
        <div class="summary-actions">
            <button form="order" name="action" value="preview" class="secondary">Preview the price</button>
            <?php if (Playground::isSandbox()): ?>
                <button form="order" name="action" value="open">Open the checkout</button>
                <p class="hint">Creates a checkout session for the chosen customer, then shows the payment form.</p>
            <?php else: ?>
                <button disabled>Open the checkout</button>
                <p class="hint">Opening a checkout is limited to sandbox organizations in this example.</p>
            <?php endif; ?>
        </div>
    </aside>
</div>

<details class="card quiet">
    <summary>What this page calls in the SDK</summary>
    <ul class="plain">
        <li><code>$client->billingPackages->retrieve($id, ['include' => OrderForm::INCLUDES])</code> reads the package and its choices.</li>
        <li><code>$client->organizationUsers->list(['can_be_invoiced' => true, 'q' => $search])</code> lists customers.</li>
        <li><code>$client->misc->calculateBillingPackagePrice($order)</code> prices the order and saves nothing.</li>
        <li><code>$client->checkoutSessions->create($order, new RequestOptions(idempotencyKey: $key))</code> opens the checkout; a second click with the same key returns the same session.</li>
    </ul>
</details>

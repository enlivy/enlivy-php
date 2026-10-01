<?php

use Enlivy\Enums\CheckoutSession\Statuses;
use Example\Checkout\OrderForm;
use Example\Checkout\View;

?>
<h1>Checkout sessions</h1>
<p class="lead">Every checkout opened for this organization, newest first.</p>

<p class="row">
    <a class="link <?= $status === '' ? 'active' : '' ?>" href="<?= h(View::url(['page' => 'sessions'])) ?>">All</a>
    <?php foreach (Statuses::values() as $value): ?>
        <a class="link <?= $status === $value ? 'active' : '' ?>" href="<?= h(View::url(['page' => 'sessions', 'status' => $value])) ?>"><?= h($value) ?></a>
    <?php endforeach; ?>
</p>

<div class="card table-scroll">
    <table>
        <thead><tr><th>Package</th><th>Customer</th><th>Status</th><th>Payment</th><th class="num">Due today</th><th>Opened</th></tr></thead>
        <tbody>
        <?php foreach ($sessions as $session): ?>
            <?php $row = $session->toArray(); $package = $row['billing_package']['data'] ?? $row['billing_package'] ?? null; $customer = $row['receiver_user']['data'] ?? $row['receiver_user'] ?? null; ?>
            <tr>
                <td><a href="<?= h(View::url(['page' => 'session', 'id' => $session->id])) ?>"><?= h(OrderForm::text($package['name_lang_map'] ?? null) ?: $session->organization_billing_package_id) ?></a>
                    <div class="muted mono"><?= h($session->client_reference_id ?? $session->id) ?></div></td>
                <td><?= h($customer['name'] ?? $session->organization_receiver_user_id) ?></td>
                <td><span class="badge"><?= h($session->status) ?></span></td>
                <td><?= h($session->payment_status) ?><?= $session->payment_method_kind ? ' · ' . h($session->payment_method_kind) : '' ?></td>
                <td class="num"><?= h(View::money($session->priced_total, $session->currency)) ?></td>
                <td class="muted"><?= h(View::date($session->created_at)) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if (count($sessions) === 0): ?>
            <tr><td colspan="6" class="muted">No sessions yet.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    <?php if ($sessions->hasMore()): ?>
        <p><a class="link" href="<?= h(View::url(['page' => 'sessions', 'status' => $status, 'p' => $sessions->getCurrentPage() + 1])) ?>">Next page →</a></p>
    <?php endif; ?>
</div>

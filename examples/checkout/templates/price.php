<?php

use Example\Checkout\OrderForm;
use Example\Checkout\View;

$currency = $priced['currency'] ?? null;
$lines = $priced['lines'] ?? $priced['priced_lines'] ?? [];
$payments = $priced['payments'] ?? $priced['priced_terms']['plan_payments'] ?? [];
$renewal = $priced['renewal'] ?? null;
?>
<?php if (($priced['mode'] ?? null) === 'setup'): ?>
    <p class="flash">Nothing is charged today: the customer only saves a card for later payments.</p>
<?php endif; ?>
<div class="table-scroll"><table>
    <thead><tr><th>Item</th><th class="num">Qty</th><th class="num">Price</th><th class="num">Total</th></tr></thead>
    <tbody>
    <?php foreach ($lines as $line): ?>
        <tr>
            <td><?= h(OrderForm::text($line['name_lang_map'] ?? null) ?: ($line['organization_product_id'] ?? '')) ?>
                <?php if (! empty($line['original'])): ?>
                    <div class="hint">Original price <?= h(View::money($line['original']['unit_price'], $line['original']['currency'])) ?><?= (float) $line['quantity'] !== 1.0 ? ' each' : '' ?></div>
                <?php endif; ?>
            </td>
            <td class="num"><?= h(rtrim(rtrim((string) $line['quantity'], '0'), '.')) ?></td>
            <td class="num"><?= h(View::money($line['unit_price'] ?? null, $currency)) ?></td>
            <td class="num"><?= h(View::money($line['sub_total'] ?? null, $currency)) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr><td colspan="3">Subtotal</td><td class="num"><?= h(View::money($priced['sub_total'] ?? $priced['priced_sub_total'] ?? null, $currency)) ?></td></tr>
    <tr><td colspan="3">VAT and taxes</td><td class="num"><?= h(View::money($priced['tax_total'] ?? $priced['priced_tax_total'] ?? null, $currency)) ?></td></tr>
    <tr class="total"><td colspan="3">Due today</td><td class="num"><?= h(View::money($priced['total'] ?? $priced['priced_total'] ?? null, $currency)) ?></td></tr>
    </tfoot>
</table></div>

<?php $conversion = $priced['conversion'] ?? null; ?>
<?php if ($conversion): ?>
    <p class="muted">
        Converted at 1 <?= h(strtoupper($conversion['source'])) ?> = <?= h(rtrim(rtrim(number_format((float) $conversion['rate'], 4, '.', ''), '0'), '.')) ?> <?= h(strtoupper($conversion['target'])) ?>, the rate held for this order.
        <?php if ((float) ($conversion['fee'] ?? 0) > 0): ?>This includes a <?= h(rtrim(rtrim((string) $conversion['fee'], '0'), '.')) ?>% conversion fee.<?php endif; ?>
    </p>
<?php endif; ?>
<?php if ($renewal): ?>
    <p class="muted">Then <?= h(View::money($renewal['total'], $renewal['currency'])) ?> <?= h(strtolower(OrderForm::frequencyLabel($renewal['frequency']))) ?>, from <?= h(View::date($renewal['first_charge_at'])) ?><?= $renewal['reconverts_each_renewal'] ? ', converted again at each renewal' : '' ?>.</p>
<?php endif; ?>

<?php if ($payments !== []): ?>
    <h3>Payment schedule</h3>
    <div class="table-scroll"><table>
        <thead><tr><th>From</th><th>Repeats</th><th class="num">Times</th><th class="num">Each</th></tr></thead>
        <tbody>
        <?php foreach ($payments as $payment): ?>
            <tr>
                <td><?= h(View::date($payment['due_at'])) ?></td>
                <td><?= h(OrderForm::frequencyLabel($payment['frequency'] ?? null)) ?></td>
                <td class="num"><?= $payment['occurrences'] === null ? 'no end' : (int) $payment['occurrences'] ?></td>
                <td class="num"><?= h(View::money($payment['total'], $currency)) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
<?php endif; ?>

<details class="inline-details">
    <summary>Raw API answer</summary>
    <pre><?= View::json($priced) ?></pre>
</details>

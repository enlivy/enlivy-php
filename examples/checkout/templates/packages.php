<?php

use Example\Checkout\OrderForm;
use Example\Checkout\View;

$sellable = [];
$unsellable = [];
foreach ($packages as $package) {
    $form = OrderForm::fromPackage($package);
    if ($form->refusals() === []) {
        $sellable[] = $form;
    } else {
        $unsellable[] = $form;
    }
}
?>
<h1>Choose a package</h1>
<p class="lead">Pick what you want to sell. Next you choose the customer and what they buy, and see the price.</p>

<?php if ($sellable === []): ?>
    <div class="card empty">
        <p>None of this organization's packages can be sold through a checkout yet. The reasons are listed below.</p>
    </div>
<?php else: ?>
    <div class="grid">
        <?php foreach ($sellable as $form): ?>
            <a class="card package" href="<?= h(View::url(['page' => 'order', 'package' => $form->id()])) ?>">
                <span class="package-type"><?= $form->isSubscription() ? 'Subscription' : 'One-time' ?></span>
                <h2><?= h($form->name()) ?></h2>
                <span class="package-meta">
                    <?= h(implode(' · ', $form->paymentMethods())) ?>
                    <span class="sep">·</span>
                    <?= h(implode(', ', $form->currencies())) ?>
                </span>
                <span class="package-cta">Sell this →</span>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if ($unsellable !== []): ?>
    <details class="card quiet">
        <summary><?= count($unsellable) ?> <?= count($unsellable) === 1 ? 'package' : 'packages' ?> a checkout cannot sell</summary>
        <ul class="plain">
            <?php foreach ($unsellable as $form): ?>
                <li>
                    <strong><?= h($form->name()) ?></strong>
                    <span class="muted"><?= h(implode(' ', $form->refusals())) ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </details>
<?php endif; ?>

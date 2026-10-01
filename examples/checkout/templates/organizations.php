<?php

use Enlivy\Enums\Organization\Environments;
use Example\Checkout\Playground;

?>
<h1>Organization</h1>
<p class="lead">Pick the organization whose packages you sell. Only a sandbox accepts writes here.</p>

<form method="post" class="card">
    <?= csrf_field() ?>
    <input type="hidden" name="back" value="page=organizations">
    <div class="options">
        <?php foreach ($organizations as $index => $organization): ?>
            <?php
            $sandbox = $organization->environment === Environments::SANDBOX->value;
            $checked = Playground::organizationId() !== null ? Playground::organizationId() === $organization->id : $index === 0;
            ?>
            <label class="option">
                <input type="radio" name="organization" value="<?= h($organization->id) ?>" required <?= $checked ? 'checked' : '' ?>>
                <span class="option-body">
                    <span class="option-title">
                        <?= h($organization->name) ?>
                        <span class="badge <?= $sandbox ? 'ok' : 'warn' ?>"><?= h($organization->environment) ?></span>
                    </span>
                    <span class="option-meta">
                        <span class="mono"><?= h($organization->id) ?></span>
                        <span><?= h($organization->customer_portal_base_url ?? 'No portal address') ?></span>
                    </span>
                    <?php if (! $sandbox): ?>
                        <span class="option-note">Read only: packages, prices and sessions, no writes.</span>
                    <?php endif; ?>
                </span>
            </label>
        <?php endforeach; ?>
    </div>
    <div class="actions">
        <button name="action" value="organization">Use this organization</button>
    </div>
</form>

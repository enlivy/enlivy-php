<?php

use Example\Checkout\Playground;
use Example\Checkout\View;

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($title) ?> · Enlivy checkout example</title>
    <link rel="stylesheet" href="/app.css">
</head>
<body>
<header class="bar">
    <a class="brand" href="/">Enlivy checkout example</a>
    <?php if (Playground::isConnected()): ?>
        <nav>
            <?php if ($organization !== null): ?>
                <a href="/">Packages</a>
                <a href="<?= h(View::url(['page' => 'sessions'])) ?>">Sessions</a>
                <a href="<?= h(View::url(['page' => 'organizations'])) ?>" class="org">
                    <?= h($organization['name']) ?>
                    <span class="badge <?= Playground::isSandbox() ? 'ok' : 'warn' ?>"><?= h($organization['environment']) ?></span>
                </a>
            <?php endif; ?>
            <form method="post" class="inline">
                <?= csrf_field() ?>
                <button name="action" value="disconnect" class="link">Disconnect</button>
            </form>
        </nav>
    <?php endif; ?>
</header>
<main>
    <?php if ($flash !== null): ?>
        <p class="flash <?= h($flash['tone']) ?>"><?= h($flash['message']) ?></p>
    <?php endif; ?>
    <?= $content ?>
</main>
</body>
</html>

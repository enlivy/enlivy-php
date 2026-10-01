<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/src/Playground.php';
require __DIR__ . '/src/OrderForm.php';
require __DIR__ . '/src/View.php';
require __DIR__ . '/src/helpers.php';

session_name('enlivy_checkout_example');
session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'cookie_secure' => ($_SERVER['HTTPS'] ?? '') !== '',
]);

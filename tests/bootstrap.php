<?php

declare(strict_types=1);

/**
 * PHPUnit bootstrap file.
 *
 * Loads environment variables from .env.testing if it exists.
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load .env.testing if it exists (for integration tests)
$envFile = __DIR__ . '/../.env.testing';

if (file_exists($envFile)) {
    // Use createUnsafeImmutable to populate getenv() in addition to $_ENV
    $dotenv = Dotenv\Dotenv::createUnsafeImmutable(dirname($envFile), '.env.testing');
    $dotenv->safeLoad();
}

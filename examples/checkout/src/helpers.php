<?php

declare(strict_types=1);

use Example\Checkout\Playground;

function h(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES);
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . h(Playground::csrf()) . '">';
}

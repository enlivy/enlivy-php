<?php

declare(strict_types=1);

namespace Example\Checkout;

final class View
{
    /**
     * @param array<string, mixed> $vars
     */
    public static function page(string $template, string $title, array $vars = []): never
    {
        $content = self::partial($template, $vars);
        $flash = Playground::flash();
        $organization = Playground::organization();
        require __DIR__ . '/../templates/layout.php';
        exit;
    }

    /**
     * @param array<string, mixed> $vars
     */
    public static function partial(string $template, array $vars = []): string
    {
        extract($vars);
        ob_start();
        require __DIR__ . '/../templates/' . $template . '.php';

        return (string) ob_get_clean();
    }

    public static function redirect(string $query = ''): never
    {
        header('Location: /' . ($query === '' ? '' : '?' . $query), true, 303);
        exit;
    }

    public static function url(array $query): string
    {
        return '/?' . http_build_query($query);
    }

    public static function money(?string $amount, ?string $currency): string
    {
        if ($amount === null || $amount === '') {
            return '';
        }

        return number_format((float) $amount, 2) . ' ' . $currency;
    }

    public static function date(?string $moment): string
    {
        if ($moment === null || $moment === '') {
            return '';
        }

        return (new \DateTimeImmutable($moment))->format('j M Y');
    }

    /**
     * @param array<string, mixed>|null $value
     */
    public static function json(mixed $value): string
    {
        return \h((string) json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
    }
}

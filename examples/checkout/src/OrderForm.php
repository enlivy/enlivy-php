<?php

declare(strict_types=1);

namespace Example\Checkout;

use Enlivy\Enums\BillingPackage\PackageType;
use Enlivy\Enums\PaymentMethod;
use Enlivy\Enums\BillingPackage\SelectionMode;
use Enlivy\Enums\BillingPackage\SubscriptionTermStatuses;
use Enlivy\Organization\BillingPackage;

/**
 * Turns a billing package into the choices a buyer makes, and those choices into the order that
 * `misc->calculateBillingPackagePrice()` and `checkoutSessions->create()` both take. The API checks the
 * order again, so this only has to offer what the package offers.
 */
final class OrderForm
{
    public const array INCLUDES = ['groups', 'payment_plans', 'subscription_terms', 'contract_templates'];

    /**
     * @param array<string, mixed> $package
     */
    private function __construct(private readonly array $package)
    {
    }

    public static function fromPackage(BillingPackage $package): self
    {
        return new self($package->toArray());
    }

    public function id(): string
    {
        return (string) $this->package['id'];
    }

    public function name(): string
    {
        return self::text($this->package['name_lang_map'] ?? null) ?: $this->id();
    }

    public function isSubscription(): bool
    {
        return ($this->package['type'] ?? null) === PackageType::SUBSCRIPTION->value;
    }

    /**
     * @return list<string>
     */
    public function currencies(): array
    {
        $list = $this->package['currency_list'] ?? null;

        return is_array($list) && $list !== [] ? array_values($list) : [(string) ($this->package['currency'] ?? '')];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function terms(): array
    {
        return array_values(array_filter(
            self::listOf($this->package['subscription_terms'] ?? null),
            fn (array $term): bool => ($term['status'] ?? SubscriptionTermStatuses::ACTIVE->value) === SubscriptionTermStatuses::ACTIVE->value,
        ));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function plans(): array
    {
        return array_values(array_filter(
            self::listOf($this->package['payment_plans'] ?? null),
            fn (array $plan): bool => (bool) ($plan['is_active'] ?? true),
        ));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function groups(): array
    {
        return self::listOf($this->package['groups'] ?? null);
    }

    /**
     * @param array<string, mixed> $group
     * @return list<array<string, mixed>>
     */
    public static function items(array $group): array
    {
        return self::listOf($group['items'] ?? null);
    }

    /**
     * @param array<string, mixed> $plan
     * @return list<array<string, mixed>>
     */
    public static function phases(array $plan): array
    {
        return self::listOf($plan['phases'] ?? null);
    }

    /**
     * @param array<string, mixed> $phase
     * @return list<array<string, mixed>>
     */
    public static function lines(array $phase): array
    {
        return self::listOf($phase['line_items'] ?? null);
    }

    /**
     * What makes a checkout refuse this package, read off the package itself. The API is the final word:
     * it answers 422 `checkout_session_package_not_sellable` with the reason.
     *
     * @return list<string>
     */
    public function refusals(): array
    {
        $refusals = [];
        if (! ($this->package['is_active'] ?? false)) {
            $refusals[] = 'It is switched off.';
        }
        if (($this->package['is_expired'] ?? false) === true) {
            $refusals[] = 'It has expired.';
        }
        if (self::listOf($this->package['contract_templates'] ?? null) !== []) {
            $refusals[] = 'It needs a signed contract, which checkouts do not support yet.';
        }
        if ($this->paymentMethods() === []) {
            $refusals[] = 'It takes neither card nor bank transfer.';
        }
        if (! $this->isSubscription() && $this->plans() === []) {
            $refusals[] = 'It has no active payment plan.';
        }

        return $refusals;
    }

    /**
     * The ways a checkout can take payment for this package. An empty list on the package allows every
     * method; a card also needs the organization to have Stripe connected, which only the API knows.
     *
     * @return list<string>
     */
    public function paymentMethods(): array
    {
        $allowed = $this->package['allowed_payment_methods'] ?? null;
        $offered = [];
        foreach ([PaymentMethod::STRIPE_CARD_PAYMENT->value => 'Card', PaymentMethod::BANK_TRANSFER->value => 'Bank transfer'] as $method => $label) {
            if (! is_array($allowed) || $allowed === [] || in_array($method, $allowed, true)) {
                $offered[] = $label;
            }
        }

        return $offered;
    }

    public static function selectionLabel(array $group): string
    {
        return match ($group['selection_mode'] ?? null) {
            SelectionMode::FIXED->value => 'Always included',
            SelectionMode::SELECT_ONE->value => 'Pick one',
            default => ($group['is_required'] ?? false) ? 'Pick at least one' : 'Optional, pick any',
        };
    }

    public static function frequencyLabel(?string $frequency): string
    {
        return $frequency === null ? 'once' : ucfirst(str_replace('_', ' ', $frequency));
    }

    /**
     * @param array<string, mixed> $input the submitted form
     * @return array<string, mixed>
     */
    public function order(array $input): array
    {
        $order = [
            'organization_billing_package_id' => $this->id(),
            'currency' => in_array($input['currency'] ?? null, $this->currencies(), true) ? $input['currency'] : null,
        ];

        if ($this->isSubscription()) {
            $order['organization_billing_package_subscription_term_id'] = self::chosen($input['term'] ?? null);
            $order['selected_group_items'] = $this->selectedGroupItems($input) ?: null;
        } else {
            $plan = self::chosen($input['plan'] ?? null);
            $order['organization_billing_package_payment_plan_id'] = $plan;
            $order['line_quantities'] = $this->lineQuantities($plan, $input) ?: null;
        }

        return array_filter($order, fn (mixed $value): bool => $value !== null);
    }

    /**
     * @param array<string, mixed> $input
     * @return list<array{id: string, quantity?: int}>
     */
    private function selectedGroupItems(array $input): array
    {
        $selected = [];
        foreach ($this->groups() as $group) {
            $chosen = match ($group['selection_mode'] ?? null) {
                SelectionMode::FIXED->value => array_column(self::items($group), 'id'),
                SelectionMode::SELECT_ONE->value => array_filter([self::chosen($input['group'][$group['id']] ?? null)]),
                default => array_keys(array_filter((array) ($input['pick'] ?? []))),
            };
            foreach (self::items($group) as $item) {
                if (! in_array($item['id'], $chosen, true)) {
                    continue;
                }
                $quantity = (int) ($input['quantity'][$item['id']] ?? 0);
                $selected[] = ($group['allow_quantity'] ?? false) && $quantity > 0
                    ? ['id' => $item['id'], 'quantity' => $quantity]
                    : ['id' => $item['id']];
            }
        }

        return $selected;
    }

    /**
     * @param array<string, mixed> $input
     * @return list<array{id: string, quantity: int}>
     */
    private function lineQuantities(?string $planId, array $input): array
    {
        $quantities = [];
        foreach ($this->plans() as $plan) {
            if ($plan['id'] !== $planId) {
                continue;
            }
            foreach (self::phases($plan) as $phase) {
                foreach (self::lines($phase) as $line) {
                    $quantity = (int) ($input['quantity'][$line['id']] ?? 0);
                    if (($line['allow_quantity'] ?? false) && $quantity > 0) {
                        $quantities[] = ['id' => $line['id'], 'quantity' => $quantity];
                    }
                }
            }
        }

        return $quantities;
    }

    /**
     * @param array<string, mixed> $item a group item or a phase line item
     */
    public static function itemName(array $item): string
    {
        $product = $item['product'] ?? null;
        if (is_array($product) && isset($product['data']) && is_array($product['data'])) {
            $product = $product['data'];
        }

        return self::text($item['name_lang_map'] ?? null)
            ?: self::text(is_array($product) ? ($product['name_lang_map'] ?? null) : null)
            ?: (string) ($item['organization_product_id'] ?? $item['id']);
    }

    private static function chosen(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * An include answers either a list or `{data: [...]}`; this reads both.
     *
     * @return list<array<string, mixed>>
     */
    private static function listOf(mixed $value): array
    {
        if (is_array($value) && array_key_exists('data', $value)) {
            $value = $value['data'];
        }

        return is_array($value) ? array_values(array_filter($value, 'is_array')) : [];
    }

    /**
     * @param array<string, string>|null $langMap
     */
    public static function text(?array $langMap, string $locale = 'en'): string
    {
        if ($langMap === null || $langMap === []) {
            return '';
        }

        return (string) ($langMap[$locale] ?? reset($langMap));
    }
}

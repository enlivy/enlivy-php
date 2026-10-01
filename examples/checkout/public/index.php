<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

use Enlivy\Embed\CheckoutEmbed;
use Enlivy\Enlivy;
use Enlivy\Exception\ApiException;
use Enlivy\Exception\AuthenticationException;
use Enlivy\Exception\ValidationException;
use Enlivy\Util\RequestOptions;
use Example\Checkout\OrderForm;
use Example\Checkout\Playground;
use Example\Checkout\View;

const SESSION_INCLUDES = ['receiver_user', 'billing_package', 'billing_schedule', 'invoice', 'proforma_invoice'];

function apiError(Throwable $error): string
{
    if ($error instanceof ValidationException) {
        $messages = [];
        foreach ($error->errors() as $field => $fieldMessages) {
            $messages[] = $field . ': ' . implode(' ', (array) $fieldMessages);
        }

        return $error->getMessage() . ($messages === [] ? '' : ' (' . implode('; ', $messages) . ')');
    }
    if ($error instanceof ApiException) {
        $code = $error->getBody()['code'] ?? $error->getBody()['error_code'] ?? null;

        return $error->getStatusCode() . ' ' . $error->getMessage() . ($code === null ? '' : " [{$code}]");
    }

    return $error->getMessage();
}

function requireSandbox(): void
{
    if (! Playground::isSandbox()) {
        Playground::flash('This example writes only to a sandbox organization. Pick one first.', 'error');
        View::redirect('page=organizations');
    }
}

/**
 * @return array<string, mixed>
 */
function sessionOptions(array $input): array
{
    $metadata = [];
    foreach ((array) ($input['metadata'] ?? []) as $row) {
        $key = trim((string) ($row['key'] ?? ''));
        if ($key !== '') {
            $metadata[$key] = (string) ($row['value'] ?? '');
        }
    }

    return array_filter([
        'organization_receiver_user_id' => $input['customer'] ?? null,
        'start_at' => ($input['start_at'] ?? '') ?: null,
        'expires_in_minutes' => ($input['expires_in_minutes'] ?? '') === '' ? null : (int) $input['expires_in_minutes'],
        'client_reference_id' => ($input['client_reference_id'] ?? '') ?: null,
        'metadata' => $metadata ?: null,
        'source_channel' => ($input['source_channel'] ?? '') ?: null,
        'source_campaign' => ($input['source_campaign'] ?? '') ?: null,
    ], fn (mixed $value): bool => $value !== null);
}

$action = $_SERVER['REQUEST_METHOD'] === 'POST' ? (string) ($_POST['action'] ?? '') : null;
$page = (string) ($_GET['page'] ?? '');

if ($action !== null) {
    Playground::checkCsrf();

    try {
        switch ($action) {
            case 'connect':
                $apiKey = trim((string) ($_POST['api_key'] ?? ''));
                $apiBase = rtrim(trim((string) ($_POST['api_base'] ?? '')), '/') ?: Enlivy::DEFAULT_API_BASE;
                Playground::connect($apiKey, $apiBase);
                Playground::client()->organizations->list(['per_page' => 1]);
                View::redirect('page=organizations');

            case 'disconnect':
                Playground::disconnect();
                View::redirect();

            case 'organization':
                Playground::useOrganization(Playground::client()->organizations->retrieve((string) $_POST['organization']));
                View::redirect('page=packages');

            case 'preview':
                $query = array_diff_key($_POST, array_flip(['csrf', 'action', 'idempotency_key', 'back']));
                View::redirect(http_build_query(['page' => 'order', 'preview' => 1] + $query) . '#price');

            case 'open':
                requireSandbox();
                if (($_POST['customer'] ?? '') === '') {
                    Playground::flash('Choose a customer first: a checkout is opened for one customer.', 'error');
                    $query = array_diff_key($_POST, array_flip(['csrf', 'action', 'idempotency_key', 'back']));
                    View::redirect(http_build_query(['page' => 'order'] + $query));
                }
                $client = Playground::client();
                $form = OrderForm::fromPackage($client->billingPackages->retrieve((string) $_POST['package'], ['include' => OrderForm::INCLUDES]));
                $order = $form->order($_POST) + sessionOptions($_POST);
                $idempotencyKey = (string) ($_POST['idempotency_key'] ?? '') ?: bin2hex(random_bytes(16));

                $session = $client->checkoutSessions->create($order, new RequestOptions(idempotencyKey: $idempotencyKey));
                $token = $session->lastResponse()?->json['meta']['client_token'] ?? null;
                if (is_string($token)) {
                    Playground::rememberClientToken($session->id, $token);
                }
                Playground::flash(is_string($token)
                    ? 'Session opened. The token below was answered once; this example keeps it so you can reload the page.'
                    : 'The same key answered a closed session, so there is no token to pay with.');
                View::redirect('page=session&id=' . urlencode($session->id));

            case 'update':
                requireSandbox();
                $id = (string) $_POST['id'];
                $params = sessionOptions($_POST);
                unset($params['organization_receiver_user_id'], $params['start_at'], $params['expires_in_minutes']);
                if (($_POST['expires_at'] ?? '') !== '') {
                    $params['expires_at'] = (new DateTimeImmutable((string) $_POST['expires_at']))->format(DATE_ATOM);
                }
                Playground::client()->checkoutSessions->update($id, $params);
                Playground::flash('Session corrected.');
                View::redirect('page=session&id=' . urlencode($id));

            case 'expire':
                requireSandbox();
                $id = (string) $_POST['id'];
                Playground::client()->checkoutSessions->expire($id);
                Playground::flash('Session expired.');
                View::redirect('page=session&id=' . urlencode($id));
        }
    } catch (AuthenticationException $error) {
        Playground::disconnect();
        Playground::flash('The API refused that key: ' . apiError($error), 'error');
        View::redirect();
    } catch (Throwable $error) {
        Playground::flash(apiError($error), 'error');
        $back = (string) ($_POST['back'] ?? '');
        View::redirect(str_starts_with($back, 'page=') ? $back : '');
    }

    http_response_code(400);
    exit('Unknown action.');
}

if (! Playground::isConnected()) {
    View::page('connect', 'Connect', ['apiBase' => Enlivy::DEFAULT_API_BASE]);
}
if ($page !== 'organizations' && Playground::organizationId() === null) {
    View::redirect('page=organizations');
}

try {
    $client = Playground::client();

    switch ($page) {
        case 'organizations':
            View::page('organizations', 'Organization', [
                'organizations' => Playground::client()->organizations->list(['per_page' => 100]),
            ]);

        case 'order':
            $form = OrderForm::fromPackage($client->billingPackages->retrieve((string) $_GET['package'], ['include' => OrderForm::INCLUDES]));
            $customerQuery = trim((string) ($_GET['customer_q'] ?? ''));
            $customers = $client->organizationUsers->list(array_filter([
                'can_be_invoiced' => true,
                'q' => $customerQuery ?: null,
                'per_page' => 25,
            ]));

            $preview = null;
            $previewError = null;
            if (isset($_GET['preview'])) {
                $buyer = ($_GET['customer'] ?? '') !== ''
                    ? ['organization_receiver_user_id' => $_GET['customer']]
                    : ['country_code' => $_GET['country_code'] ?? 'RO', 'is_business_entity' => ($_GET['is_business_entity'] ?? '') === '1'];
                try {
                    $preview = $client->misc->calculateBillingPackagePrice($form->order($_GET) + $buyer + array_filter(['start_at' => ($_GET['start_at'] ?? '') ?: null]));
                } catch (ApiException $error) {
                    $previewError = apiError($error);
                }
            }

            View::page('order', $form->name(), [
                'form' => $form,
                'input' => $_GET,
                'customers' => $customers,
                'customerQuery' => $customerQuery,
                'preview' => $preview,
                'previewError' => $previewError,
                'idempotencyKey' => bin2hex(random_bytes(16)),
            ]);

        case 'session':
            $session = $client->checkoutSessions->retrieve((string) $_GET['id'], ['include' => SESSION_INCLUDES]);
            $token = Playground::clientToken($session->id);
            $portal = Playground::organization()['portal'] ?? null;
            $embed = $token !== null && $session->status === 'open' && $portal
                ? new CheckoutEmbed($portal, $token, $session->organization_billing_package_id, ['theme' => (string) ($_GET['theme'] ?? 'light')])
                : null;

            View::page('session', 'Checkout session', ['session' => $session, 'embed' => $embed, 'portal' => $portal]);

        case 'status':
            $session = $client->checkoutSessions->retrieve((string) $_GET['id']);
            header('Content-Type: application/json');
            echo json_encode([
                'status' => $session->status,
                'payment_status' => $session->payment_status,
                'payment_method_kind' => $session->payment_method_kind,
                'organization_billing_schedule_id' => $session->organization_billing_schedule_id,
                'organization_invoice_id' => $session->organization_invoice_id,
                'organization_proforma_invoice_id' => $session->organization_proforma_invoice_id,
            ]);
            exit;

        case 'sessions':
            View::page('sessions', 'Checkout sessions', [
                'sessions' => $client->checkoutSessions->list(array_filter([
                    'status' => ($_GET['status'] ?? '') ?: null,
                    'include' => ['receiver_user', 'billing_package'],
                    'per_page' => 25,
                    'page' => max(1, (int) ($_GET['p'] ?? 1)),
                ])),
                'status' => (string) ($_GET['status'] ?? ''),
            ]);

        default:
            View::page('packages', 'Billing packages', [
                'packages' => $client->billingPackages->list([
                    'include' => OrderForm::INCLUDES,
                    'per_page' => 50,
                ]),
            ]);
    }
} catch (AuthenticationException $error) {
    Playground::disconnect();
    Playground::flash('The API refused that key: ' . apiError($error), 'error');
    View::redirect();
} catch (Throwable $error) {
    View::page('error', 'Something went wrong', ['message' => apiError($error)]);
}

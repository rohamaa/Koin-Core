# Usage

How to use any Koin gateway driver. Every driver follows **exactly the same** API — whatever the gateway, the code is identical except for the class name and constructor arguments.

## Install

Install the driver you need; it pulls in the core (and Guzzle) automatically:

```bash
composer require rohama/koin-zarinpal   # or any other driver
```

## The payment flow

Three steps, always the same shape:

1. **request** — start the transaction, get the redirect URL
2. **redirect** — send the user to `redirectUrl`
3. **parseCallback** — the callback page; a successful callback is **already verified** (`verify()` runs inside `parseCallback()`)

```php
use Rohama\Koin\Zarinpal\ZarinpalGateway;   // or any driver class
use Rohama\Koin\Support\PaymentStatus;

$gateway = new ZarinpalGateway('merchant-code-36-chars', 'https://example.com/callback');

$payment = $gateway->request(150000, 'ORDER-1', 'Subscription purchase');

if ($payment->isSuccessful()) {
    header('Location: ' . $payment->redirectUrl);
    exit;
}

// In the callback page — parseCallback runs verify() before reporting success
$callback = $gateway->parseCallback($_GET, $_POST);
if ($callback->status === PaymentStatus::SUCCESS) {
    // Verified
}
```

> Exception: ZarinPal's `Verify` requires an amount its callback does not carry, so `parseCallback()` there reports the status without calling `verify()`.

## `request()`

```php
public function request(
    int $amount,               // amount (IRT toman by default; IRR rial supported)
    string $orderId = '',
    ?string $callbackUrl = null,   // overrides the constructor callback URL
    string $description = '',
    ?string $mobile = null,
    ?string $email = null,
    ?string $currency = null,      // Currency::TOMAN (IRT, default) or Currency::RIAL (IRR)
    array $metadata = [],
    array $extra = [],             // gateway-specific request fields
): PaymentResponse
```

Returns a **`PaymentResponse`**:

| Property | Type | Meaning |
| --- | --- | --- |
| `status` | string | `PaymentStatus` value |
| `transactionId` | string | The gateway's transaction/token/authority id |
| `redirectUrl` | ?string | Where to send the user |
| `orderId` | string | Your order id, echoed back |
| `message` | ?string | Gateway message (on failure) |
| `data`, `response` | array / `ResponseInterface` | The raw gateway payload (via `JsonResponse`) |

Throws `PaymentException` subclasses on request failures (configuration, validation, connectivity, gateway rejection).

## The callback

```php
$callback = $gateway->parseCallback($_GET, $_POST);
```

A **`Callback`**:

| Property | Meaning |
| --- | --- |
| `status` | `PaymentStatus` — `SUCCESS` means the payment is verified |
| `transactionId` | The gateway's transaction id (start of the follow-up) |
| `orderId` | Your order id, if it was carried through |
| `extra` | All raw callback data; raw keys are also readable via `$callback->anything` |

## `verify()`

Generally you do **not** call `verify()` yourself — `parseCallback()` handles it. But it is available, e.g. for re-checking:

```php
public function verify(
    string $transactionId,
    string $orderId = '',
    ?int $amount = null,      // only needed by gateways whose verify API requires it
    ?string $currency = null,
    array $extra = [],
): VerificationResponse
```

Returns a **`VerificationResponse`**: `status`, `transactionId`, `referenceId`, `amount`, `cardNumber`, `message`, plus the raw payload. Throws `VerificationFailedException` when verification fails.

## Statuses

`PaymentStatus` constants — every gateway maps its raw provider status onto these:

| Constant | Value |
| --- | --- |
| `PENDING` | `pending` |
| `SUCCESS` | `success` |
| `FAILED` | `failed` |
| `CANCELED` | `canceled` |
| `REFUNDED` | `refunded` |

## Optional capabilities

Drivers implement only what the provider's official API offers, so **check the driver's page or interface list before calling**:

| Interface | Method | Notes |
| --- | --- | --- |
| `RefundableGateway` | `refund($transactionId, $orderId = '', $amount = null, $currency = null, $extra = [])` | Requires the developer-only 5th constructor parameter `refundable`; throws `InvalidConfigException` otherwise |
| `ReverseGateway` | `reverse(...)` | Cancel/reverse a payment before confirmation |
| `InquiryGateway` | `inquiry(...)` | Current status of a payment (`VerificationResponse`) |
| `TransactionListGateway` | `transactions($page = 0, $pageSize = 25, $filters = [])` | Paginated transaction list |
| `UnverifiedGateway` | `unverified()` | Payments awaiting verification |
| `FeeCalculatorGateway` | `calculateFee($amount, $currency = null)` | Gateway fee for an amount |
| `SandboxGateway` | — (marker) | The driver's `sandbox` flag is a real test mode |

Common response types: `RefundResponse`, `ReverseResponse`, `FeeResponse`, `TransactionsResponse`, `UnverifiedResponse` — same `data`/`response` raw payload access.

## Configuration helpers

```php
$fields   = ZarinpalGateway::configFields();       // declared fields, for your admin form
$gateway  = ZarinpalGateway::fromFields($filled);  // build from filled form values
$gateway  = ZarinpalGateway::toFields($gateway);   // back to filled fields
$config   = $gateway->toArray();                   // serialize, includes driver name
$gateway  = ZarinpalGateway::fromArray($config);   // restore
```

## Amounts & currency

The default currency is toman (`Currency::TOMAN`, `IRT`). Pass `currency: Currency::RIAL` (or an IRR `currency` string) to work in rials. Gateways whose uplink API speaks rials convert automatically; `VerificationResponse->amount` is converted back to your currency.

## Exceptions

| Exception | Raised |
| --- | --- |
| `InvalidConfigException` | Incomplete/invalid configuration (e.g. `refund()` without the flag) |
| `InvalidRequestException` | Bad input (e.g. callback without a transaction id) |
| `GatewayConnectionException` | Network/connectivity error |
| `PaymentFailedException` | The gateway rejected the payment request |
| `VerificationFailedException` | `verify()` failed |
| `PaymentException` | Base class — catch-all |

All exceptions carry the gateway name in `$exception->gateway`.

## Logging safely

Drivers expose `sensitiveKeys()` and `redacted()` — a payload with API keys, tokens and card numbers masked, safe to log.

# Core concepts

## The `PaymentGateway` interface

Every driver class implements `Rohama\Koin\PaymentGateway`:

```php
public function __construct(
    string $apiKey,              // API key / merchant code
    ?string $callbackUrl = null, // return URL
    bool $sandbox = false,       // test mode (only gateways with an official test API)
    array $extra = [],           // gateway-specific extra fields
    bool $refundable = false,    // enables refund() (developer-only flag)
);
```

## Primary methods

| Method | Description |
| --- | --- |
| `request(...)` | Starts a transaction; returns `PaymentResponse` |
| `parseCallback($query, $body)` | Converts the raw callback request into a structured `Callback` |
| `verify(...)` | Verifies the payment; returns `VerificationResponse` or throws `VerificationFailedException` |
| `configFields()` | Configuration fields for panel forms |
| `fromFields()`, `toFields()`, `fromArray()`, `toArray()` | Configuration conversion helpers |
| `getName()`, `displayName()` | Gateway naming |

## Optional capabilities

Each driver implements only the capabilities its provider officially offers:

| Capability | Method | Gateways |
| --- | --- | --- |
| `RefundableGateway` | `refund()` | NextPay, PayPing, SEP, ZarinPal |
| `ReverseGateway` | `reverse()` | SizPay |
| `InquiryGateway` | `inquiry()` | IDPay, Zibal |
| `TransactionListGateway` | `transactions()` | IDPay |
| `UnverifiedGateway` | `unverified()` | ZarinPal |
| `FeeCalculatorGateway` | `calculateFee()` | ZarinPal |
| `SandboxGateway` | (marker) | IDPay, ZarinPal |

## Responses & requests

- `PaymentResponse` — `status`, `transactionId`, `redirectUrl`, `orderId`, `message`, plus the raw payload (`data`, `response`)
- `VerificationResponse` — `status`, `transactionId`, `referenceId`, `amount`, `cardNumber`, `message`
- `Callback` — `status`, `transactionId`, `orderId` and an `extra` array for raw data (raw keys are also reachable via `__get`)
- `RefundResponse`, `ReverseResponse`, `FeeResponse`, `TransactionsResponse`, `UnverifiedResponse`

## Statuses (`PaymentStatus`)

`PENDING`, `SUCCESS`, `FAILED`, `CANCELED`, `REFUNDED` — every gateway maps its raw provider status onto one of these.

## Currency (`Currency`)

| Constant | Meaning |
| --- | --- |
| `Currency::TOMAN` (`IRT`) | Toman — default |
| `Currency::RIAL` (`IRR`) | Rial |

Conversion helpers: `toRial`, `toToman`, `fromRial`, `fromToman`. Gateways whose API speaks rial convert toman amounts internally.

## Exceptions

| Exception | When |
| --- | --- |
| `InvalidConfigException` | Incomplete or invalid configuration (e.g. refund without the flag) |
| `InvalidRequestException` | Bad input, e.g. a callback without a transaction id |
| `GatewayConnectionException` | Network / connectivity error |
| `PaymentFailedException` | The gateway rejected the payment request |
| `VerificationFailedException` | Verification failed |
| `PaymentException` | Base class — catch-all |

All exceptions carry the gateway name in `gateway`.

## The callback→verify rule

In every driver `parseCallback()` calls `verify()` before reporting `SUCCESS`. The single documented exception is ZarinPal (its callback carries no amount while `Verify` requires one).

# ZarinPal

The **ZarinPal** gateway driver — built on ZarinPal's classic REST v4 payment gateway (merchant code).

- Composer: `rohama/koin-zarinpal`
- Namespace: `Rohama\Koin\Zarinpal`

## Install

```bash
composer require rohama/koin-zarinpal
```

## Features

ZarinPal REST API v4 features implemented:

- Payment request — returns authority + StartPay redirect URL
- Verify
- Reverse (refund)
- unVerified
- Fee calculation
- Callback parsing
- Sandbox

## Configuration

| Field | Type | Required | Description |
| --- | --- | --- | --- |
| `apiKey` | text | ✅ | 36-char merchant code |
| `sandbox` | checkbox | ❌ | Test mode (ZarinPal sandbox merchant) |

## Usage

```php
use Rohama\Koin\Zarinpal\ZarinpalGateway;
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

## Important notes

- **Documented callback exception:** ZarinPal's `Verify` requires `amount && authority`, but its callback does not carry the amount; therefore `parseCallback()` reports the status without calling `verify()` — the only exception to the package-wide verify rule.
- `refund()` is available once the `refundable` flag (5th constructor parameter) is enabled.
- `unverified()` lists payments awaiting verification; `calculateFee()` computes the gateway fee.

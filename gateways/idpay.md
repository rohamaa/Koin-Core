# IDPay

The **IDPay** gateway driver — built on IDPay's REST API v1.1.

- Composer: `rohama/koin-idpay`
- Namespace: `Rohama\Koin\IDPay`

## Install

```bash
composer require rohama/koin-idpay
```

## Features

IDPay REST API v1.1 features implemented:

- Payment request — returns id + StartPay redirect URL
- Verify
- Inquiry
- Transaction list
- Callback parsing
- Sandbox

## Configuration

| Field | Type | Required | Description |
| --- | --- | --- | --- |
| `apiKey` | text | ✅ | 36-char API key |
| `sandbox` | checkbox | ❌ | IDPay's official test mode |

## Usage

```php
use Rohama\Koin\IDPay\IDPayGateway;
use Rohama\Koin\Support\PaymentStatus;

$gateway = new IDPayGateway('api-key-36-chars', 'https://example.com/callback');

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

In the callback page `parseCallback()` calls `verify()` before reporting the result. `inquiry()` and `transactions()` are also available.

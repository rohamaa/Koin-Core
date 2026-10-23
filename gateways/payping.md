# PayPing

The **PayPing** gateway driver — built on PayPing's REST API v3.

- Composer: `rohama/koin-payping`
- Namespace: `Rohama\Koin\Payping`

## Install

```bash
composer require rohama/koin-payping
```

## Features

PayPing REST API v3 features implemented:

- Payment request — returns code + redirect URL (/pay/)
- Verify
- Refund
- Callback parsing

## Configuration

| Field | Type | Required | Description |
| --- | --- | --- | --- |
| `apiKey` | text | ✅ | API token |

## Usage

```php
use Rohama\Koin\Payping\PaypingGateway;
use Rohama\Koin\Support\PaymentStatus;

$gateway = new PaypingGateway('token', 'https://example.com/callback');

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

`refund()` is available once the `refundable` flag (5th constructor parameter) is enabled.

# SizPay

The **SizPay** gateway driver — built on SizPay's REST **Simple** API (base `rt.sizpay.ir`).

- Composer: `rohama/koin-sizpay`
- Namespace: `Rohama\Koin\Sizpay`

## Install

```bash
composer require rohama/koin-sizpay
```

## Features

SizPay REST Simple API features implemented:

- Payment request — GetToken + redirect URL
- Verify — Confirm
- Reverse
- Callback parsing

Note: SizPay's official documentation provides no test (sandbox) environment for this API version; the `sandbox` flag is accepted but is effectively a no-op.

## Configuration

| Field | Type | Required | Description |
| --- | --- | --- | --- |
| `apiKey` | text | ✅ | API key |
| `terminalId` | text | ✅ | Terminal id |
| `userName` | text | ✅ | User name |
| `password` | password | ✅ | Password |

## Usage

```php
use Rohama\Koin\Sizpay\SizpayGateway;
use Rohama\Koin\Support\PaymentStatus;

$gateway = new SizpayGateway(
    'api-key',
    'https://example.com/callback',
    extra: [
        'terminalId' => '…',
        'userName'   => '…',
        'password'   => '…',
    ],
);

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

`reverse()` (the `ReverseGateway` capability) cancels/reverses a payment before it is confirmed.

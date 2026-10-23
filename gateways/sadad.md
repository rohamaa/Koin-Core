# SADAD (Bank Melli)

The **SADAD** gateway driver (Bank Melli's unified acquisition service) — built on SADAD's REST WebAPI v0.

- Composer: `rohama/koin-sadad`
- Namespace: `Rohama\Koin\Sadad`

## Install

```bash
composer require rohama/koin-sadad
```

## Features

SADAD REST WebAPI v0 features implemented:

- Payment request — returns token + redirect URL
- Verify
- Callback parsing

Note: per SADAD's official documentation, this API version exposes no refund, inquiry, or public test environment; the related capabilities are not implemented.

## Configuration

| Field | Type | Required | Description |
| --- | --- | --- | --- |
| `apiKey` | text | ✅ | API key |
| `merchantId` | text | ✅ | Merchant / terminal id |
| `terminalKey` | text | ✅ | Terminal key |

## Usage

```php
use Rohama\Koin\Sadad\SadadGateway;
use Rohama\Koin\Support\PaymentStatus;

$gateway = new SadadGateway(
    'api-key',
    'https://example.com/callback',
    extra: [
        'merchantId'  => '…',
        'terminalKey' => '…',
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

# SEP (Bank Saman)

The **SEP** gateway driver (Saman Bank's Internet Payment Service) — built on the SEP OnlinePG REST API.

- Composer: `rohama/koin-sep`
- Namespace: `Rohama\Koin\Sep`

Besides `sep`, the **Bluepay** sub-driver (`Rohama\Koin\Sep\BluepayGateway`) ships in the same package.

## Install

```bash
composer require rohama/koin-sep
```

## Features

SEP OnlinePG REST API features implemented:

- Payment request — returns token + redirect URL
- Verify
- Refund
- Callback parsing

## Configuration

| Field | Type | Required | Description |
| --- | --- | --- | --- |
| `apiKey` | text | ✅ | API key |

## Usage

```php
use Rohama\Koin\Sep\SepGateway;
use Rohama\Koin\Support\PaymentStatus;

$gateway = new SepGateway('api-key', 'https://example.com/callback');

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

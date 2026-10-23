# Zibal

The **Zibal** gateway driver — built on Zibal's IPG REST v1 (`gateway.zibal.ir`).

- Composer: `rohama/koin-zibal`
- Namespace: `Rohama\Koin\Zibal`

## Install

```bash
composer require rohama/koin-zibal
```

## Features

Zibal REST API v1 features implemented:

- Payment request — returns trackId + StartPay redirect URL, lazy mode supported
- Verify
- Inquiry
- Callback parsing

Note: the current official documentation exposes no refund endpoint; the refund capability is not implemented.

## Configuration

| Field | Type | Required | Description |
| --- | --- | --- | --- |
| `apiKey` | text | ✅ | Merchant key (`merchant`) |
| `lazy` | checkbox | ❌ | LazyPay mode (JSON POST callback) |

## Usage

```php
use Rohama\Koin\Zibal\ZibalGateway;
use Rohama\Koin\Support\PaymentStatus;

$gateway = new ZibalGateway('merchant-key', 'https://example.com/callback');

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

## Notes

- **Official test account:** use the merchant key `zibal` — no `sandbox` switch needed.
- **LazyPay:** with `lazy` enabled, the request goes to `/v1/request/lazy`, verification to `/lazy/verify`, and the callback arrives as JSON POST (both `$_GET` and `$_POST` are covered).
- In inquiries, only status `1` (paid - verified) is reported as success; status `2` (paid - unverified) is reported as failed.

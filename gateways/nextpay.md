# NextPay

The **NextPay** gateway driver (direct gateway).

- Composer: `rohama/koin-nextpay`
- Namespace: `Rohama\Koin\Nextpay`

## Install

```bash
composer require rohama/koin-nextpay
```

## Features

NextPay REST API features implemented:

- Payment request — returns trans_id + redirect to gateway
- Verify
- Refund
- Callback parsing

## Configuration

| Field | Type | Required | Description |
| --- | --- | --- | --- |
| `apiKey` | text | ✅ | API key (`api_key`) |

## Usage

```php
use Rohama\Koin\Nextpay\NextpayGateway;
use Rohama\Koin\Support\PaymentStatus;

$gateway = new NextpayGateway('api-key', 'https://example.com/callback');

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

# Installation & configuration

## Requirements

- PHP 8.0+
- Guzzle 7 or 8 (installed automatically)

## Install

Install the core together with the drivers you need:

```bash
composer require rohama/koin-idpay
composer require rohama/koin-nextpay
composer require rohama/koin-payping
composer require rohama/koin-sadad
composer require rohama/koin-sep
composer require rohama/koin-sizpay
composer require rohama/koin-zarinpal
composer require rohama/koin-zibal
```

## Payment flow

Every gateway follows the same three steps:

1. **request** — create a transaction and get the redirect URL
2. **redirect** — send the user to `redirectUrl`
3. **parseCallback + verify** — handle the user's return and verify the payment

```php
use Rohama\Koin\Exception\PaymentException;
use Rohama\Koin\Exception\VerificationFailedException;
use Rohama\Koin\Support\PaymentStatus;

try {
    // 1) Start the payment
    $payment = $gateway->request(150000, 'ORDER-1', 'Subscription purchase');
    if ($payment->isSuccessful()) {
        // 2) Redirect the user to the gateway
        header('Location: ' . $payment->redirectUrl);
        exit;
    }
} catch (PaymentException $e) {
    // Pre-redirect errors: validation, configuration or connectivity
}

// 3) In the callback page:
try {
    $callback = $gateway->parseCallback($_GET, $_POST);

    if ($callback->status === PaymentStatus::SUCCESS) {
        // Verified — verify() has already been called inside parseCallback
    }
} catch (VerificationFailedException $e) {
    // Verification failed
}
```

!> Every gateway **always** reports success only after `verify()`; the single documented exception is ZarinPal, whose `Verify` requires an amount that its callback does not carry.

## Configuring a gateway

Each gateway defines its own configuration fields — see the tables on its gateway page. Helpers for panel-style configuration:

```php
// Fields declared by the gateway (for your configuration form)
$fields = ZarinpalGateway::configFields();

// Build a gateway from filled form values
$gateway = ZarinpalGateway::fromFields($filledFields);

// Persist / restore configuration
$serialized = $gateway->toArray();   // includes the driver name
$gateway   = ZarinpalGateway::fromArray($serialized);
```

## The `refundable` flag

```php
new ZarinpalGateway($apiKey, $callbackUrl, /* sandbox */ false, /* extra */ [], /* refundable */ true);
```

`refundable` is **the 5th constructor parameter and is developer-only**; it must never be exposed as an end-user configuration field. Calling `refund()` without it throws `InvalidConfigException`.

## Localisation drivers

Every package registers its own translation files through `rohama/translator`; display names and field labels are available automatically.

## Sandbox mode

Only gateways whose provider ships an official test API have a real `sandbox` flag (IDPay and ZarinPal). Other gateways accept the flag but ignore it. For Zibal use the official test merchant key `zibal`.

<h1 align="center">Koin</h1>

<p align="center">
  <a href="https://rohamaa.github.io/Koin-Core/">📖 Documentation</a> ·
  <a href="https://github.com/rohamaa/Koin-Core/blob/docs/README.md">Docs on GitHub</a>
</p>

**Koin** is a family of PHP packages for a unified payment experience with Iranian payment gateways. One interface (`PaymentGateway`), structured responses, and identical behaviour across all drivers — no build tooling, no vendor lock-in.

## Packages

| Package | Gateway | Composer |
| --- | --- | --- |
| Core | — | `rohama/koin-core` |
| IDPay | IDPay | `rohama/koin-idpay` |
| NextPay | NextPay (direct gateway) | `rohama/koin-nextpay` |
| PayPing | PayPing | `rohama/koin-payping` |
| SADAD | SADAD (Bank Melli) | `rohama/koin-sadad` |
| SEP | SEP (Bank Saman) + Bluepay | `rohama/koin-sep` |
| SizPay | SizPay | `rohama/koin-sizpay` |
| ZarinPal | ZarinPal | `rohama/koin-zarinpal` |
| Zibal | Zibal | `rohama/koin-zibal` |

## Quick start

```bash
composer require rohama/koin-zarinpal
```

```php
use Rohama\Koin\Zarinpal\ZarinpalGateway;
use Rohama\Koin\Support\PaymentStatus;

$gateway = new ZarinpalGateway('merchant-code-36-chars', 'https://example.com/callback');

$payment = $gateway->request(150000, 'ORDER-1', 'Subscription purchase');

if ($payment->isSuccessful()) {
    header('Location: ' . $payment->redirectUrl);
    exit;
}

$callback = $gateway->parseCallback($_GET, $_POST);
if ($callback->status === PaymentStatus::SUCCESS) {
    // Verified via verify() before reporting success
}
```

## Highlights

- One interface for every gateway — switching a gateway is a one-line change
- Structured responses: `PaymentResponse`, `VerificationResponse`, `Callback`
- Standard statuses: `success`, `failed`, `pending`, `canceled`, `refunded`
- IRT/IRR currency support with automatic conversion
- Optional capabilities (refund, inquiry, transaction list, ...) implemented only when the provider officially offers the API
- Guaranteed behaviour: every successful callback is **verified** before success is reported

## Documentation

Full docs — installation, payment flow, core concepts, and one page per gateway — live on the `docs` branch of this repo (readable on GitHub or rendered by docsify):

**<https://rohamaa.github.io/Koin-Core/>**

- [Introduction](https://github.com/rohamaa/Koin-Core/blob/docs/README.md)
- [Usage guide](https://github.com/rohamaa/Koin-Core/blob/docs/usage.md)
- [Installation & configuration](https://github.com/rohamaa/Koin-Core/blob/docs/installation.md)
- [Core concepts](https://github.com/rohamaa/Koin-Core/blob/docs/core-concepts.md)
- Gateways: [IDPay](https://github.com/rohamaa/Koin-Core/blob/docs/gateways/idpay.md) · [NextPay](https://github.com/rohamaa/Koin-Core/blob/docs/gateways/nextpay.md) · [PayPing](https://github.com/rohamaa/Koin-Core/blob/docs/gateways/payping.md) · [SADAD](https://github.com/rohamaa/Koin-Core/blob/docs/gateways/sadad.md) · [SEP](https://github.com/rohamaa/Koin-Core/blob/docs/gateways/sep.md) · [SizPay](https://github.com/rohamaa/Koin-Core/blob/docs/gateways/sizpay.md) · [ZarinPal](https://github.com/rohamaa/Koin-Core/blob/docs/gateways/zarinpal.md) · [Zibal](https://github.com/rohamaa/Koin-Core/blob/docs/gateways/zibal.md)

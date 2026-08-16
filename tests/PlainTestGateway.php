<?php

use GuzzleHttp\Client;
use Rohama\Koin\Enums\Currency;
use Rohama\Koin\Enums\PaymentStatus;
use Rohama\Koin\PaymentGateway;
use Rohama\Koin\Requests\Callback;
use Rohama\Koin\Responses\PaymentResponse;
use Rohama\Koin\Responses\VerificationResponse;
use Rohama\Koin\Traits\IsPayment;

/**
 * Test gateway using IsPayment without sandbox support and with the default
 * configFields() (apiKey + callbackUrl only).
 */
class PlainTestGateway implements PaymentGateway
{
    use IsPayment { __construct as protected paymentConstruct; }

    public const name = 'plain';

    public function __construct(
        string $apiKey,
        ?string $callbackUrl = null,
        bool $sandbox = false,
        array $extra = [],
    ) {
        $this->paymentConstruct($apiKey, $callbackUrl, $sandbox, $extra);
        $this->client = new Client();
    }

    public function request(
        int $amount,
        string $orderId = '',
        ?string $callbackUrl = null,
        string $description = '',
        ?string $mobile = null,
        ?string $email = null,
        ?Currency $currency = null,
        array $metadata = [],
        array $extra = [],
    ): PaymentResponse {
        return new PaymentResponse(
            [],
            new \GuzzleHttp\Psr7\Response(200),
            PaymentStatus::PENDING,
            'tx-' . $orderId,
            'https://plain.example/pay',
            $orderId,
        );
    }

    public function parseCallback(array $query, array $body): Callback
    {
        return new Callback(PaymentStatus::SUCCESS, (string) ($body['id'] ?? ''), (string) ($body['order_id'] ?? ''));
    }

    public function verify(
        string $transactionId,
        string $orderId = '',
        ?int $amount = null,
        ?Currency $currency = null,
        array $extra = [],
    ): VerificationResponse {
        return new VerificationResponse([], new \GuzzleHttp\Psr7\Response(200), PaymentStatus::SUCCESS, $transactionId);
    }
}

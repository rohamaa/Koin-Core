<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Rohama\Koin\PaymentGateway;
use Rohama\Koin\Requests\Callback;
use Rohama\Koin\Responses\PaymentResponse;
use Rohama\Koin\Responses\VerificationResponse;
use Rohama\Koin\Support\PaymentStatus;
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
        bool $refundable = false,
    ) {
        $this->paymentConstruct($apiKey, $callbackUrl, $sandbox, $extra, $refundable);
        $this->client = new Client();
    }

    public function request(
        int $amount,
        string $orderId = '',
        ?string $callbackUrl = null,
        string $description = '',
        ?string $mobile = null,
        ?string $email = null,
        ?string $currency = null,
        array $metadata = [],
        array $extra = [],
    ): PaymentResponse {
        return new PaymentResponse(
            [],
            new Response(200),
            PaymentStatus::PENDING,
            'tx-'.$orderId,
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
        ?string $currency = null,
        array $extra = [],
    ): VerificationResponse {
        return new VerificationResponse([], new Response(200), PaymentStatus::SUCCESS, $transactionId);
    }
}

<?php

use GuzzleHttp\Client;
use Rohama\Koin\Enums\Currency;
use Rohama\Koin\Enums\PaymentStatus;
use Rohama\Koin\PaymentGateway;
use Rohama\Koin\Requests\Callback;
use Rohama\Koin\Responses\PaymentResponse;
use Rohama\Koin\Responses\VerificationResponse;
use Rohama\Koin\SandboxGateway;
use Rohama\Koin\Traits\IsPayment;

/**
 * Test gateway using IsPayment that supports sandbox mode and carries an
 * extra credential (terminalId) to exercise extra-routing and validation.
 */
class TestGateway implements PaymentGateway, SandboxGateway
{
    use IsPayment { __construct as protected paymentConstruct; }

    public const name = 'test';

    public function __construct(
        string $apiKey,
        ?string $callbackUrl = null,
        bool $sandbox = false,
        array $extra = [],
    ) {
        $this->paymentConstruct($apiKey, $callbackUrl, $sandbox, $extra);
        $this->client = new Client();
    }

    public static function configFields(): array
    {
        return [
            [
                'key' => 'apiKey',
                'name' => 'API Key',
                'desc' => 'The gateway credential / API key.',
                'type' => 'text',
                'required' => true,
                'validation' => ['required', 'string'],
                'regex' => '/^.+$/',
            ],
            [
                'key' => 'callbackUrl',
                'name' => 'Callback URL',
                'desc' => 'The return URL the gateway redirects the payer to after payment.',
                'type' => 'url',
                'required' => false,
                'validation' => ['nullable', 'url'],
                'regex' => null,
            ],
            [
                'key' => 'sandbox',
                'name' => 'Sandbox mode',
                'desc' => 'Use the gateway sandbox/test environment instead of the live one.',
                'type' => 'checkbox',
                'required' => false,
                'validation' => ['boolean'],
                'regex' => null,
            ],
            [
                'key' => 'terminalId',
                'name' => 'Terminal ID',
                'desc' => 'The gateway terminal identifier.',
                'type' => 'text',
                'required' => true,
                'validation' => ['required', 'string'],
                'regex' => '/^\d+$/',
            ],
        ];
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
            'https://test.example/pay',
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

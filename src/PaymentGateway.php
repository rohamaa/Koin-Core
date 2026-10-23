<?php

namespace Rohama\Koin;

use GuzzleHttp\Client;
use Rohama\Koin\Exception\PaymentException;
use Rohama\Koin\Exception\VerificationFailedException;
use Rohama\Koin\Requests\Callback;
use Rohama\Koin\Responses\PaymentResponse;
use Rohama\Koin\Responses\VerificationResponse;

/**
 * Base interface every payment gateway must implement.
 */
interface PaymentGateway
{
    public function __construct(
        string $apiKey,
        ?string $callbackUrl = null,
        bool $sandbox = false,
        array $extra = [],
        bool $refundable = false,
    );

    /**
     * Driver name of the gateway.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Display name of the gateway.
     *
     * @return string
     */
    public function displayName(?string $locale = null): string;

    /**
     * Return the configuration inputs for this gateway.
     *
     * @return array
     */
    public static function configFields(): array;

    /**
     * Build a gateway from filled inputs.
     *
     * @return self
     */
    public static function fromFields(array $fields): self;

    /**
     * Return the gateway's configuration as filled inputs.
     *
     * @return array
     */
    public function toFields(): array;

    /**
     * Build a gateway from a serialized config array.
     *
     * @return self
     */
    public static function fromArray(array $config): self;

    /**
     * Serialize the gateway's configuration, including the driver name.
     *
     * @return array
     */
    public function toArray(): array;

    /**
     * Get the Guzzle HTTP Client.
     *
     * @return Client
     */
    public function client(): Client;

    /**
     * Ask the gateway to initiate a payment and return the redirect target.
     *
     * @return PaymentResponse
     * @throws PaymentException
     */
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
    ): PaymentResponse;

    /**
     * Parse a raw incoming callback into a structured result.
     *
     * @param  array  $query  the raw query parameters
     * @param  array  $body  the raw request body
     *
     * @return Callback
     */
    public function parseCallback(array $query, array $body): Callback;

    /**
     * Verify an incoming payment callback for a previously requested payment.
     *
     * @return VerificationResponse
     * @throws VerificationFailedException
     */
    public function verify(
        string $transactionId,
        string $orderId = '',
        ?int $amount = null,
        ?string $currency = null,
        array $extra = [],
    ): VerificationResponse;
}

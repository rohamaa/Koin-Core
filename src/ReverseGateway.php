<?php

namespace Rohama\Koin;

use Rohama\Koin\Exception\PaymentException;
use Rohama\Koin\Responses\ReverseResponse;

/**
 * Optional capability for gateways that expose a pre-confirm cancel/reverse API.
 */
interface ReverseGateway
{
    /**
     * Cancel a payment before it is confirmed.
     *
     * @return ReverseResponse
     * @throws PaymentException
     */
    public function reverse(
        string $transactionId,
        string $orderId = '',
        ?int $amount = null,
        ?string $currency = null,
        array $extra = [],
    ): ReverseResponse;
}

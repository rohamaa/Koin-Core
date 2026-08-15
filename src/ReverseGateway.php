<?php

namespace Rohama\Koin;

use Rohama\Koin\Enums\Currency;
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
     * @throws \Rohama\Koin\Exception\PaymentException
     */
    public function reverse(
        string $transactionId,
        string $orderId = '',
        ?int $amount = null,
        ?Currency $currency = null,
        array $extra = [],
    ): ReverseResponse;
}

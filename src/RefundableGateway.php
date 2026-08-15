<?php

namespace Rohama\Koin;

use Rohama\Koin\Enums\Currency;
use Rohama\Koin\Responses\RefundResponse;

/**
 * Optional capability for gateways that expose a refund API.
 */
interface RefundableGateway
{
    /**
     * Refund a previously verified payment.
     *
     * @return RefundResponse
     * @throws \Rohama\Koin\Exception\PaymentException
     */
    public function refund(
        string $transactionId,
        string $orderId = '',
        ?int $amount = null,
        ?Currency $currency = null,
        array $extra = [],
    ): RefundResponse;
}

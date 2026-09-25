<?php

namespace Rohama\Koin;

use Rohama\Koin\Exception\PaymentException;
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
     * @throws PaymentException
     */
    public function refund(
        string $transactionId,
        string $orderId = '',
        ?int $amount = null,
        ?string $currency = null,
        array $extra = [],
    ): RefundResponse;
}

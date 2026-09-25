<?php

namespace Rohama\Koin;

use Rohama\Koin\Exception\PaymentException;
use Rohama\Koin\Responses\FeeResponse;

/**
 * Optional capability for gateways that expose a fee calculation API.
 */
interface FeeCalculatorGateway
{
    /**
     * Calculate the gateway fee for an amount.
     *
     * @return FeeResponse
     * @throws PaymentException
     */
    public function calculateFee(int $amount, ?string $currency = null): FeeResponse;
}

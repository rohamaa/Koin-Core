<?php

namespace Rohama\Koin;

use Rohama\Koin\Enums\Currency;
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
     * @throws \Rohama\Koin\Exception\PaymentException
     */
    public function calculateFee(int $amount, ?Currency $currency = null): FeeResponse;
}

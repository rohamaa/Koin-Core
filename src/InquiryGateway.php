<?php

namespace Rohama\Koin;

use Rohama\Koin\Exception\PaymentException;
use Rohama\Koin\Responses\VerificationResponse;

/**
 * Optional capability for gateways that expose a payment inquiry API.
 */
interface InquiryGateway
{
    /**
     * Inquire the current status of a payment.
     *
     * @return VerificationResponse
     * @throws PaymentException
     */
    public function inquiry(
        string $transactionId,
        string $orderId = '',
        ?int $amount = null,
        ?string $currency = null,
        array $extra = [],
    ): VerificationResponse;
}

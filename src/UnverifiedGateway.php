<?php

namespace Rohama\Koin;

use Rohama\Koin\Responses\UnverifiedResponse;

/**
 * Optional capability for gateways that expose a list of unverified payments.
 */
interface UnverifiedGateway
{
    /**
     * List payments awaiting verification.
     *
     * @return UnverifiedResponse
     * @throws \Rohama\Koin\Exception\PaymentException
     */
    public function unverified(): UnverifiedResponse;
}

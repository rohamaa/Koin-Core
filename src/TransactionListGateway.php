<?php

namespace Rohama\Koin;

use Rohama\Koin\Responses\TransactionsResponse;

/**
 * Optional capability for gateways that expose a transaction list API.
 */
interface TransactionListGateway
{
    /**
     * List the gateway's transactions.
     *
     * @return TransactionsResponse
     * @throws \Rohama\Koin\Exception\PaymentException
     */
    public function transactions(int $page = 0, int $pageSize = 25, array $filters = []): TransactionsResponse;
}

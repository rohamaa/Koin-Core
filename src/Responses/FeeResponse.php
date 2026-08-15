<?php

namespace Rohama\Koin\Responses;

use Psr\Http\Message\ResponseInterface;

class FeeResponse extends JsonResponse
{
    public function __construct(
        array $data,
        ResponseInterface $response,
        public int $amount,
        public int $fee,
        public string $feeType,
        public int $suggestedAmount,
    ) {
        parent::__construct($data, $response);
    }
}

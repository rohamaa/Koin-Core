<?php

namespace Rohama\Koin\Responses;

use Psr\Http\Message\ResponseInterface;

class TransactionsResponse extends JsonResponse
{
    public function __construct(
        array $data,
        ResponseInterface $response,
        public array $records,
        public ?array $attachment = null,
    ) {
        parent::__construct($data, $response);
    }
}

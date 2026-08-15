<?php

namespace Rohama\Koin\Responses;

use Psr\Http\Message\ResponseInterface;

class UnverifiedResponse extends JsonResponse
{
    public function __construct(
        array $data,
        ResponseInterface $response,
        public array $authorities,
    ) {
        parent::__construct($data, $response);
    }
}

<?php

namespace Rohama\Koin\Responses;

use Psr\Http\Message\ResponseInterface;
use Rohama\Koin\Enums\PaymentStatus;

class RefundResponse extends JsonResponse
{
    public function __construct(
        array $data,
        ResponseInterface $response,
        public PaymentStatus $status,
        public string $transactionId,
        public ?string $referenceId = null,
        public ?string $message = null,
    ) {
        parent::__construct($data, $response);
    }

    public function isSuccessful(): bool
    {
        return $this->status === PaymentStatus::REFUNDED;
    }
}

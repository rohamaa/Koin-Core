<?php

namespace Rohama\Koin\Requests;

use JsonSerializable;
use Rohama\Koin\Enums\PaymentStatus;

/**
 * Parsed gateway callback returned by parseCallback().
 */
class Callback implements JsonSerializable
{
    public function __construct(
        public PaymentStatus $status,
        public string $transactionId,
        public string $orderId = '',
        public array $extra = [],
    ) {
    }

    public function __get(string $name): mixed
    {
        return $this->extra[$name] ?? null;
    }

    public function __put(string $name, mixed $value): void
    {
        $this->extra[$name] = $value;
    }

    public function jsonSerialize(): array
    {
        return [
            'status' => $this->status->value,
            'transactionId' => $this->transactionId,
            'orderId' => $this->orderId,
            ...$this->extra,
        ];
    }
}

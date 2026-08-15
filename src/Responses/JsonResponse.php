<?php

namespace Rohama\Koin\Responses;

use JsonSerializable;
use Psr\Http\Message\ResponseInterface;
use Rohama\Koin\Redactable;
use Rohama\Koin\Traits\HasRedaction;

class JsonResponse implements Redactable, JsonSerializable
{
    use HasRedaction;

    public function __construct(
        public array $data,
        public ResponseInterface $response,
    ) {
    }

    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    public function __put(string $name, mixed $value): void
    {
        $this->data[$name] = $value;
    }

    public function jsonSerialize(): array
    {
        return $this->data;
    }

    public function redacted(): array
    {
        return self::redact($this->data, $this->sensitiveKeys());
    }
}

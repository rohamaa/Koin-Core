<?php

namespace Rohama\Koin\Exception;

use Rohama\Koin\Redactable;
use Rohama\Koin\Traits\HasRedaction;
use Rohama\Translator\Translator;

class PaymentException extends \RuntimeException implements Redactable
{
    use HasRedaction;

    public function __construct(
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
        public array $raw = [],
        public ?string $gateway = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function message(?string $locale = null): string
    {
        return Translator::trans('messages.'.$this->getMessage(), [], $this->getMessage(), $locale ?? Translator::getDefaultLocale());
    }

    public function redacted(): array
    {
        return self::redact($this->raw, $this->sensitiveKeys());
    }
}

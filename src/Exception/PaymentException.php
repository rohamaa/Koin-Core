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
        $message = (string) $this->getMessage();

        if ($this->gateway !== null && $this->gateway !== '') {
            $key = 'Koin-'.$this->gateway.':messages.'.$message;
            $translation = Translator::trans($key, [], $locale);

            if ($translation !== $key) {
                return $translation;
            }
        }

        $key = 'Koin:messages.'.$message;
        $translation = Translator::trans($key, [], $locale);

        return $translation !== $key ? $translation : $message;
    }

    public function redacted(): array
    {
        return self::redact($this->raw, $this->sensitiveKeys());
    }
}

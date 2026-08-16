<?php

namespace Rohama\Koin\Enums;

use Rohama\Translator\Translator;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SUCCESS = 'success';
    case FAILED = 'failed';
    case CANCELED = 'canceled';
    case REFUNDED = 'refunded';

    /**
     * Display name of the status
     */
    public function name(?string $locale = null): string
    {
        return Translator::trans('Koin:payment_status.'.$this->value, [], $locale);
    }
}

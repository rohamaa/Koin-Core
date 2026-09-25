<?php

namespace Rohama\Koin\Support;

use Rohama\Translator\Translator;

final class PaymentStatus
{
    public const PENDING = 'pending';

    public const SUCCESS = 'success';

    public const FAILED = 'failed';

    public const CANCELED = 'canceled';

    public const REFUNDED = 'refunded';

    public static function name(string $status, ?string $locale = null): string
    {
        return Translator::trans('Koin:payment_status.'.$status, [], $locale);
    }
}

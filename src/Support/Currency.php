<?php

namespace Rohama\Koin\Support;

final class Currency
{
    public const TOMAN = 'IRT';

    public const RIAL = 'IRR';

    public static function isToman(string $currency): bool
    {
        return $currency === self::TOMAN;
    }

    public static function toRial(string $currency, int|float $amount): int|float
    {
        return self::isToman($currency) ? $amount * 10 : $amount;
    }

    public static function toToman(string $currency, int|float $amount): int|float
    {
        return self::isToman($currency) ? $amount : $amount / 10;
    }

    public static function fromRial(string $currency, int|float $rial): int|float
    {
        return self::isToman($currency) ? $rial / 10 : $rial;
    }

    public static function fromToman(string $currency, int|float $toman): int|float
    {
        return self::isToman($currency) ? $toman : $toman * 10;
    }
}

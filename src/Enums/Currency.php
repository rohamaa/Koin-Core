<?php

namespace Rohama\Koin\Enums;

enum Currency: string
{
    case TOMAN = 'IRT';
    case RIAL = 'IRR';

    public function isToman(): bool
    {
        return $this === self::TOMAN;
    }

    /**
     * Convert an amount given in this currency to Rial (IRR).
     */
    public function toRial(int|float $amount): int|float
    {
        return $this->isToman() ? $amount * 10 : $amount;
    }

    /**
     * Convert an amount given in this currency to Toman (IRT).
     */
    public function toToman(int|float $amount): int|float
    {
        return $this->isToman() ? $amount : $amount / 10;
    }

    /**
     * Convert a Rial (IRR) amount to this currency.
     */
    public function fromRial(int|float $rial): int|float
    {
        return $this->isToman() ? $rial / 10 : $rial;
    }

    /**
     * Convert a Toman (IRT) amount to this currency.
     */
    public function fromToman(int|float $toman): int|float
    {
        return $this->isToman() ? $toman : $toman * 10;
    }
}

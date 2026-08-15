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

    public function toRial(int $amount): int
    {
        return $this->isToman() ? $amount * 10 : $amount;
    }

    public function fromRial(int $rial): int
    {
        return $this->isToman() ? intdiv($rial, 10) : $rial;
    }
}

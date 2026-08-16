<?php

use Rohama\Koin\Enums\Currency;

final class CurrencyTest extends TestCase
{
    public function testTomanIsToman(): void
    {
        $this->assertTrue(Currency::TOMAN->isToman());
        $this->assertFalse(Currency::RIAL->isToman());
    }

    public function testToRialMultipliesTomanByTen(): void
    {
        $this->assertSame(100, Currency::TOMAN->toRial(10));
        $this->assertSame(0, Currency::TOMAN->toRial(0));
    }

    public function testToRialLeavesRialUntouched(): void
    {
        $this->assertSame(10, Currency::RIAL->toRial(10));
    }

    public function testToTomanLeavesTomanUntouched(): void
    {
        $this->assertSame(10, Currency::TOMAN->toToman(10));
    }

    public function testToTomanDividesRialByTen(): void
    {
        $this->assertSame(10, Currency::RIAL->toToman(100));
    }

    public function testFromRialDividesTomanByTen(): void
    {
        $this->assertSame(10, Currency::TOMAN->fromRial(100));
    }

    public function testFromRialKeepsFractionalToman(): void
    {
        $this->assertSame(10.5, Currency::TOMAN->fromRial(105));
    }

    public function testFromRialLeavesRialUntouched(): void
    {
        $this->assertSame(100, Currency::RIAL->fromRial(100));
    }

    public function testFromTomanMultipliesRialByTen(): void
    {
        $this->assertSame(1000, Currency::RIAL->fromToman(100));
    }

    public function testFromTomanLeavesTomanUntouched(): void
    {
        $this->assertSame(100, Currency::TOMAN->fromToman(100));
    }

    public function testRoundTripToRialAndBack(): void
    {
        $this->assertSame(250, Currency::TOMAN->fromRial(Currency::TOMAN->toRial(250)));
        $this->assertSame(250, Currency::RIAL->fromRial(Currency::RIAL->toRial(250)));
    }

    public function testRoundTripToTomanAndBack(): void
    {
        $this->assertSame(250, Currency::TOMAN->toToman(Currency::TOMAN->fromToman(250)));
        $this->assertSame(250, Currency::RIAL->toToman(Currency::RIAL->fromToman(250)));
    }

    public function testRoundTripKeepsFractionalTomanExact(): void
    {
        $rial = Currency::TOMAN->toRial(10.5);
        $toman = Currency::TOMAN->fromRial($rial);

        $this->assertSame(105.0, $rial);
        $this->assertSame(10.5, $toman);
    }

    public function testStringValues(): void
    {
        $this->assertSame('IRT', Currency::TOMAN->value);
        $this->assertSame('IRR', Currency::RIAL->value);
    }
}

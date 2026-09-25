<?php

use Rohama\Koin\Support\Currency;

final class CurrencyTest extends TestCase
{
    public function testTomanIsToman(): void
    {
        $this->assertTrue(Currency::isToman(Currency::TOMAN));
        $this->assertFalse(Currency::isToman(Currency::RIAL));
    }

    public function testToRialMultipliesTomanByTen(): void
    {
        $this->assertSame(100, Currency::toRial(Currency::TOMAN, 10));
        $this->assertSame(0, Currency::toRial(Currency::TOMAN, 0));
    }

    public function testToRialLeavesRialUntouched(): void
    {
        $this->assertSame(10, Currency::toRial(Currency::RIAL, 10));
    }

    public function testToTomanLeavesTomanUntouched(): void
    {
        $this->assertSame(10, Currency::toToman(Currency::TOMAN, 10));
    }

    public function testToTomanDividesRialByTen(): void
    {
        $this->assertSame(10, Currency::toToman(Currency::RIAL, 100));
    }

    public function testFromRialDividesTomanByTen(): void
    {
        $this->assertSame(10, Currency::fromRial(Currency::TOMAN, 100));
    }

    public function testFromRialKeepsFractionalToman(): void
    {
        $this->assertSame(10.5, Currency::fromRial(Currency::TOMAN, 105));
    }

    public function testFromRialLeavesRialUntouched(): void
    {
        $this->assertSame(100, Currency::fromRial(Currency::RIAL, 100));
    }

    public function testFromTomanMultipliesRialByTen(): void
    {
        $this->assertSame(1000, Currency::fromToman(Currency::RIAL, 100));
    }

    public function testFromTomanLeavesTomanUntouched(): void
    {
        $this->assertSame(100, Currency::fromToman(Currency::TOMAN, 100));
    }

    public function testRoundTripToRialAndBack(): void
    {
        $this->assertSame(250, Currency::fromRial(Currency::TOMAN, Currency::toRial(Currency::TOMAN, 250)));
        $this->assertSame(250, Currency::fromRial(Currency::RIAL, Currency::toRial(Currency::RIAL, 250)));
    }

    public function testRoundTripToTomanAndBack(): void
    {
        $this->assertSame(250, Currency::toToman(Currency::TOMAN, Currency::fromToman(Currency::TOMAN, 250)));
        $this->assertSame(250, Currency::toToman(Currency::RIAL, Currency::fromToman(Currency::RIAL, 250)));
    }

    public function testRoundTripKeepsFractionalTomanExact(): void
    {
        $rial = Currency::toRial(Currency::TOMAN, 10.5);
        $toman = Currency::fromRial(Currency::TOMAN, $rial);

        $this->assertSame(105.0, $rial);
        $this->assertSame(10.5, $toman);
    }

    public function testStringValues(): void
    {
        $this->assertSame('IRT', Currency::TOMAN);
        $this->assertSame('IRR', Currency::RIAL);
    }
}

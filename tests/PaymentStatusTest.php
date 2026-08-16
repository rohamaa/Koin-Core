<?php

use Rohama\Koin\Enums\PaymentStatus;
use Rohama\Translator\Translator;

final class PaymentStatusTest extends TestCase
{
    protected function setUp(): void
    {
        Translator::setDefaultLocale('en');
    }

    public function testCaseValues(): void
    {
        $this->assertSame('pending', PaymentStatus::PENDING->value);
        $this->assertSame('success', PaymentStatus::SUCCESS->value);
        $this->assertSame('failed', PaymentStatus::FAILED->value);
        $this->assertSame('canceled', PaymentStatus::CANCELED->value);
        $this->assertSame('refunded', PaymentStatus::REFUNDED->value);
    }

    public function testNameResolvesEnglishByDefault(): void
    {
        $this->assertSame('Successful', PaymentStatus::SUCCESS->name());
        $this->assertSame('Pending', PaymentStatus::PENDING->name('en'));
    }

    public function testNameResolvesALocale(): void
    {
        $this->assertSame('موفق', PaymentStatus::SUCCESS->name('fa'));
        $this->assertSame('لغو شده', PaymentStatus::CANCELED->name('fa'));
    }

    public function testNameFollowsTheDefaultLocale(): void
    {
        Translator::setDefaultLocale('fa');

        $this->assertSame('موفق', PaymentStatus::SUCCESS->name());
    }
}

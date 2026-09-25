<?php

use Rohama\Koin\Support\PaymentStatus;
use Rohama\Translator\Translator;

final class PaymentStatusTest extends TestCase
{
    protected function setUp(): void
    {
        Translator::setDefaultLocale('en');
    }

    public function testCaseValues(): void
    {
        $this->assertSame('pending', PaymentStatus::PENDING);
        $this->assertSame('success', PaymentStatus::SUCCESS);
        $this->assertSame('failed', PaymentStatus::FAILED);
        $this->assertSame('canceled', PaymentStatus::CANCELED);
        $this->assertSame('refunded', PaymentStatus::REFUNDED);
    }

    public function testNameResolvesEnglishByDefault(): void
    {
        $this->assertSame('Successful', PaymentStatus::name(PaymentStatus::SUCCESS));
        $this->assertSame('Pending', PaymentStatus::name(PaymentStatus::PENDING, 'en'));
    }

    public function testNameResolvesALocale(): void
    {
        $this->assertSame('موفق', PaymentStatus::name(PaymentStatus::SUCCESS, 'fa'));
        $this->assertSame('لغو شده', PaymentStatus::name(PaymentStatus::CANCELED, 'fa'));
    }

    public function testNameFollowsTheDefaultLocale(): void
    {
        Translator::setDefaultLocale('fa');

        $this->assertSame('موفق', PaymentStatus::name(PaymentStatus::SUCCESS));
    }
}

<?php

use Rohama\Koin\Exception\GatewayConnectionException;
use Rohama\Koin\Exception\InvalidConfigException;
use Rohama\Koin\Exception\InvalidRequestException;
use Rohama\Koin\Exception\PaymentException;
use Rohama\Koin\Exception\PaymentFailedException;
use Rohama\Koin\Exception\VerificationFailedException;
use Rohama\Translator\Translator;

final class PaymentExceptionTest extends TestCase
{
    protected function setUp(): void
    {
        Translator::reset();
    }

    protected function tearDown(): void
    {
        Translator::reset();
    }

    public function testConstructorCarriesCode(): void
    {
        $e = new PaymentFailedException('boom', 42);

        $this->assertSame(42, $e->getCode());
    }

    public function testConstructorCarriesRawAndGatewayViaNamedArguments(): void
    {
        $e = new PaymentFailedException('boom', 1, raw: ['status' => 'fail'], gateway: 'test');

        $this->assertSame(['status' => 'fail'], $e->raw);
        $this->assertSame('test', $e->gateway);
    }

    public function testConstructorCarriesPrevious(): void
    {
        $previous = new RuntimeException('root');
        $e = new VerificationFailedException('boom', 0, $previous);

        $this->assertSame($previous, $e->getPrevious());
    }

    public function testRedactedMasksSensitiveKeys(): void
    {
        $e = new PaymentFailedException('boom', 0, raw: ['cardnumber' => '6037991234567890', 'order' => 'o1']);

        $this->assertSame('60************90', $e->redacted()['cardnumber']);
        $this->assertSame('o1', $e->redacted()['order']);
    }

    public function testMessageFallsBackToEnglishWhenNoTranslationExists(): void
    {
        $e = new InvalidRequestException('Amount must be greater than zero.');

        $this->assertSame('Amount must be greater than zero.', $e->message('fa'));
        $this->assertSame('Amount must be greater than zero.', $e->message());
    }

    public function testMessageResolvesAnOverride(): void
    {
        Translator::setTranslations('fa', ['messages.Amount must be greater than zero.' => 'مبلغ باید بزرگ تر از صفر باشد.']);

        $e = new InvalidRequestException('Amount must be greater than zero.');

        $this->assertSame('مبلغ باید بزرگ تر از صفر باشد.', $e->message('fa'));
    }

    public function testSubclassesExtendTheBaseAndRuntimeException(): void
    {
        $classes = [
            InvalidConfigException::class,
            InvalidRequestException::class,
            GatewayConnectionException::class,
            PaymentFailedException::class,
            VerificationFailedException::class,
        ];

        foreach ($classes as $class) {
            $this->assertTrue(is_subclass_of($class, PaymentException::class));
            $this->assertTrue(is_subclass_of($class, RuntimeException::class));
        }
    }
}

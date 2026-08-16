<?php

use Rohama\Koin\Enums\PaymentStatus;
use Rohama\Koin\Requests\Callback;

final class CallbackTest extends TestCase
{
    public function testConstructorSetsTheFields(): void
    {
        $callback = new Callback(PaymentStatus::SUCCESS, 't123', 'o123');

        $this->assertSame(PaymentStatus::SUCCESS, $callback->status);
        $this->assertSame('t123', $callback->transactionId);
        $this->assertSame('o123', $callback->orderId);
        $this->assertSame([], $callback->extra);
    }

    public function testOrderIdDefaultsToEmpty(): void
    {
        $callback = new Callback(PaymentStatus::CANCELED, 't123');

        $this->assertSame('', $callback->orderId);
    }

    public function testExtraIsAccessibleViaMagicGetter(): void
    {
        $callback = new Callback(PaymentStatus::SUCCESS, 't123', extra: ['paymentRefId' => 'ref-1']);

        $this->assertSame('ref-1', $callback->paymentRefId);
        $this->assertNull($callback->missing);
    }

    public function testJsonSerializeSpreadsTheExtraFields(): void
    {
        $callback = new Callback(PaymentStatus::SUCCESS, 't123', 'o123', ['ref' => 'r1']);

        $this->assertSame([
            'status' => 'success',
            'transactionId' => 't123',
            'orderId' => 'o123',
            'ref' => 'r1',
        ], $callback->jsonSerialize());
    }
}

<?php

use Rohama\Koin\Enums\PaymentStatus;
use Rohama\Koin\Responses\FeeResponse;
use Rohama\Koin\Responses\JsonResponse;
use Rohama\Koin\Responses\PaymentResponse;
use Rohama\Koin\Responses\RefundResponse;
use Rohama\Koin\Responses\ReverseResponse;
use Rohama\Koin\Responses\TransactionsResponse;
use Rohama\Koin\Responses\UnverifiedResponse;
use Rohama\Koin\Responses\VerificationResponse;

final class ResponsesTest extends TestCase
{
    public function testJsonResponseExposesDataViaMagicGetter(): void
    {
        $response = new JsonResponse(['id' => 'i1'], $this->response(200, []));

        $this->assertSame('i1', $response->id);
        $this->assertNull($response->missing);
    }

    public function testJsonResponseJsonSerializesTheData(): void
    {
        $response = new JsonResponse(['id' => 'i1'], $this->response(200, []));

        $this->assertSame(['id' => 'i1'], $response->jsonSerialize());
    }

    public function testJsonResponseRedactsSensitiveData(): void
    {
        $response = new JsonResponse(['cardnumber' => '6037991234567890', 'status' => 'ok'], $this->response(200, []));

        $this->assertSame('60************90', $response->redacted()['cardnumber']);
        $this->assertSame('ok', $response->redacted()['status']);
    }

    public function testPaymentResponseIsSuccessfulOnlyOnSuccess(): void
    {
        $success = new PaymentResponse(['id' => 'i1'], $this->response(200, []), PaymentStatus::SUCCESS, 't123', 'https://pay.example');
        $pending = new PaymentResponse(['id' => 'i1'], $this->response(200, []), PaymentStatus::PENDING, 't123', 'https://pay.example');

        $this->assertTrue($success->isSuccessful());
        $this->assertFalse($pending->isSuccessful());
    }

    public function testVerificationResponseCarriesReferenceAmountAndCard(): void
    {
        $response = new VerificationResponse(
            ['ref' => 'r1'],
            $this->response(200, []),
            PaymentStatus::SUCCESS,
            't123',
            'ref-1',
            1000,
            '6037991234567890',
        );

        $this->assertSame('ref-1', $response->referenceId);
        $this->assertSame(1000, $response->amount);
        $this->assertSame('6037991234567890', $response->cardNumber);
        $this->assertTrue($response->isSuccessful());
    }

    public function testRefundResponseIsSuccessfulOnlyOnRefunded(): void
    {
        $refunded = new RefundResponse(['ok' => true], $this->response(200, []), PaymentStatus::REFUNDED, 't123', 'ref-1');
        $pending = new RefundResponse(['ok' => true], $this->response(200, []), PaymentStatus::PENDING, 't123');

        $this->assertTrue($refunded->isSuccessful());
        $this->assertFalse($pending->isSuccessful());
    }

    public function testReverseResponseIsSuccessfulOnlyOnRefunded(): void
    {
        $reversed = new ReverseResponse(['ok' => true], $this->response(200, []), PaymentStatus::REFUNDED, 't123', 'ref-1');

        $this->assertTrue($reversed->isSuccessful());
    }

    public function testTransactionsResponseCarriesRecordsAndAttachment(): void
    {
        $response = new TransactionsResponse(['count' => 1], $this->response(200, []), [['id' => 't1']], ['total' => 1]);

        $this->assertSame([['id' => 't1']], $response->records);
        $this->assertSame(['total' => 1], $response->attachment);
    }

    public function testFeeResponseCarriesTheFeeFields(): void
    {
        $response = new FeeResponse(['fee' => 100], $this->response(200, []), 10000, 100, 'fixed', 10100);

        $this->assertSame(10000, $response->amount);
        $this->assertSame(100, $response->fee);
        $this->assertSame('fixed', $response->feeType);
        $this->assertSame(10100, $response->suggestedAmount);
    }

    public function testUnverifiedResponseCarriesAuthorities(): void
    {
        $response = new UnverifiedResponse(['count' => 2], $this->response(200, []), [['id' => 'a1'], ['id' => 'a2']]);

        $this->assertSame([['id' => 'a1'], ['id' => 'a2']], $response->authorities);
    }

    public function testResponsesExtendJsonResponse(): void
    {
        $classes = [
            PaymentResponse::class,
            VerificationResponse::class,
            RefundResponse::class,
            ReverseResponse::class,
            TransactionsResponse::class,
            FeeResponse::class,
            UnverifiedResponse::class,
        ];

        foreach ($classes as $class) {
            $this->assertTrue(is_subclass_of($class, JsonResponse::class));
        }
    }
}

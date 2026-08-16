<?php

use Rohama\Koin\Traits\HasRedaction;

final class HasRedactionTest extends TestCase
{
    private function redactor(): object
    {
        return new class {
            use HasRedaction;

            public function run(array $array, array $keys): array
            {
                return self::redact($array, $keys);
            }
        };
    }

    public function testSensitiveKeysListsCredentialLikeKeys(): void
    {
        $keys = (new class {
            use HasRedaction;

            public function list(): array
            {
                return $this->sensitiveKeys();
            }
        })->list();

        $this->assertContains('cardnumber', $keys);
        $this->assertContains('password', $keys);
        $this->assertContains('apikey', $keys);
        $this->assertContains('terminalkey', $keys);
    }

    public function testShortValuesAreFullyMasked(): void
    {
        $redacted = $this->redactor()->run(['pin' => '1234'], ['pin']);

        $this->assertSame('****', $redacted['pin']);
    }

    public function testLongValuesKeepFirstAndLastTwoCharacters(): void
    {
        $redacted = $this->redactor()->run(['cardnumber' => '6037991234567890'], ['cardnumber']);

        $this->assertSame('60************90', $redacted['cardnumber']);
    }

    public function testSixCharacterValueIsFullyMasked(): void
    {
        $redacted = $this->redactor()->run(['cvv' => 'abcdef'], ['cvv']);

        $this->assertSame('******', $redacted['cvv']);
    }

    public function testKeyMatchingIsCaseInsensitive(): void
    {
        $redacted = $this->redactor()->run(['CardNumber' => '6037991234567890', 'PASSWORD' => 'supersecret'], ['cardnumber', 'password']);

        $this->assertSame('60************90', $redacted['CardNumber']);
        $this->assertSame('su*******et', $redacted['PASSWORD']);
    }

    public function testNestedArraysAreRedactedRecursively(): void
    {
        $redacted = $this->redactor()->run([
            'payment' => ['cardnumber' => '6037991234567890', 'status' => 'ok'],
        ], ['cardnumber']);

        $this->assertSame('60************90', $redacted['payment']['cardnumber']);
        $this->assertSame('ok', $redacted['payment']['status']);
    }

    public function testNonSensitiveKeysAreUntouched(): void
    {
        $redacted = $this->redactor()->run(['orderId' => 'o1', 'amount' => 1000], ['cardnumber']);

        $this->assertSame(['orderId' => 'o1', 'amount' => 1000], $redacted);
    }
}

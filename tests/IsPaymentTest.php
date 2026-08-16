<?php

use Rohama\Koin\Exception\InvalidConfigException;
use Rohama\Translator\Translator;

final class IsPaymentTest extends TestCase
{
    protected function setUp(): void
    {
        Translator::reset();
    }

    protected function tearDown(): void
    {
        Translator::reset();
    }

    public function testConstructorSetsTheFields(): void
    {
        $gateway = new TestGateway('k', 'https://example.com/cb', true, ['terminalId' => '1234']);

        $this->assertSame('k', $gateway->apiKey);
        $this->assertSame('https://example.com/cb', $gateway->callbackUrl);
        $this->assertTrue($gateway->sandbox);
        $this->assertSame(['terminalId' => '1234'], $gateway->extra);
    }

    public function testMagicGetterAndSetterRouteToExtra(): void
    {
        $gateway = new TestGateway('k');
        $gateway->terminalId = '1234';

        $this->assertSame('1234', $gateway->terminalId);
        $this->assertNull($gateway->missing);
        $this->assertSame(['terminalId' => '1234'], $gateway->extra);
    }

    public function testGetNameReturnsTheDriverName(): void
    {
        $gateway = new TestGateway('k');

        $this->assertSame('test', $gateway->getName());
    }

    public function testDisplayNameFallsBackToTheRawKey(): void
    {
        $gateway = new TestGateway('k');

        $this->assertSame('test.name', $gateway->displayName());
        $this->assertSame('test.name', $gateway->displayName('fa'));
    }

    public function testDisplayNameResolvesAnOverride(): void
    {
        Translator::setTranslations('fa', ['test.name' => 'درگاه تست']);

        $gateway = new TestGateway('k');

        $this->assertSame('درگاه تست', $gateway->displayName('fa'));
    }

    public function testConfigFieldsListsTheCommonFieldsByDefault(): void
    {
        $keys = array_map(fn ($field) => $field['key'], PlainTestGateway::configFields());

        $this->assertSame(['apiKey', 'callbackUrl'], $keys);
    }

    public function testConfigFieldsIncludesSandboxWhenSandboxGatewayIsImplemented(): void
    {
        $keys = array_map(fn ($field) => $field['key'], TestGateway::configFields());

        $this->assertSame(['apiKey', 'callbackUrl', 'sandbox', 'terminalId'], $keys);
    }

    public function testFromFieldsThrowsWhenARequiredFieldIsMissing(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('The "API Key" field is required.');

        TestGateway::fromFields(['callbackUrl' => 'https://example.com/cb', 'terminalId' => '1234']);
    }

    public function testFromFieldsThrowsWhenARequiredExtraIsMissing(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('The "Terminal ID" field is required.');

        TestGateway::fromFields(['apiKey' => 'k', 'callbackUrl' => 'https://example.com/cb']);
    }

    public function testFromFieldsThrowsWhenRegexDoesNotMatch(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('The "Terminal ID" field is invalid.');

        TestGateway::fromFields(['apiKey' => 'k', 'callbackUrl' => 'https://example.com/cb', 'terminalId' => 'abc']);
    }

    public function testFromFieldsRoutesExtraFieldsIntoExtra(): void
    {
        $gateway = TestGateway::fromFields(['apiKey' => 'k', 'callbackUrl' => 'https://example.com/cb', 'terminalId' => '1234']);

        $this->assertSame('k', $gateway->apiKey);
        $this->assertSame('https://example.com/cb', $gateway->callbackUrl);
        $this->assertFalse($gateway->sandbox);
        $this->assertSame(['terminalId' => '1234'], $gateway->extra);
    }

    public function testFromFieldsCoercesAnEmptyCallbackUrlToNull(): void
    {
        $gateway = TestGateway::fromFields(['apiKey' => 'k', 'callbackUrl' => '', 'terminalId' => '1234']);

        $this->assertNull($gateway->callbackUrl);
    }

    public function testToFieldsIncludesTheExtraFields(): void
    {
        $gateway = new TestGateway('k', 'https://example.com/cb', true, ['terminalId' => '1234']);

        $this->assertSame([
            'apiKey' => 'k',
            'callbackUrl' => 'https://example.com/cb',
            'sandbox' => true,
            'terminalId' => '1234',
        ], $gateway->toFields());
    }

    public function testFromFieldsToFieldsRoundTrip(): void
    {
        $fields = ['apiKey' => 'k', 'callbackUrl' => 'https://example.com/cb', 'terminalId' => '1234'];

        $gateway = TestGateway::fromFields($fields);

        $this->assertSame([
            'apiKey' => 'k',
            'callbackUrl' => 'https://example.com/cb',
            'sandbox' => false,
            'terminalId' => '1234',
        ], $gateway->toFields());
    }

    public function testToArraySerializesTheDriverAndConfig(): void
    {
        $gateway = new TestGateway('k', 'https://example.com/cb', true, ['terminalId' => '1234']);

        $this->assertSame([
            'driver' => TestGateway::class,
            'apiKey' => 'k',
            'callbackUrl' => 'https://example.com/cb',
            'sandbox' => true,
            'extra' => ['terminalId' => '1234'],
        ], $gateway->toArray());
    }

    public function testFromArrayDispatchesOnTheDriverClass(): void
    {
        $gateway = TestGateway::fromArray([
            'driver' => TestGateway::class,
            'apiKey' => 'k',
            'callbackUrl' => 'https://example.com/cb',
            'sandbox' => true,
            'extra' => ['terminalId' => '1234'],
        ]);

        $this->assertInstanceOf(TestGateway::class, $gateway);
        $this->assertSame('k', $gateway->apiKey);
        $this->assertTrue($gateway->sandbox);
        $this->assertSame(['terminalId' => '1234'], $gateway->extra);
    }

    public function testFromArrayToArrayRoundTrip(): void
    {
        $gateway = new TestGateway('k', 'https://example.com/cb', true, ['terminalId' => '1234']);

        $roundTripped = TestGateway::fromArray($gateway->toArray());

        $this->assertSame($gateway->toArray(), $roundTripped->toArray());
    }

    public function testFromArrayThrowsOnUnknownDriver(): void
    {
        $this->expectException(InvalidConfigException::class);
        $this->expectExceptionMessage('No gateway is registered under the driver "Unknown\\Gateway".');

        TestGateway::fromArray(['driver' => 'Unknown\\Gateway', 'apiKey' => 'k']);
    }

    public function testClientReturnsTheConfiguredClient(): void
    {
        $gateway = new TestGateway('k');

        $this->assertInstanceOf(\GuzzleHttp\Client::class, $gateway->client());
    }

    public function testSetClientReplacesTheClient(): void
    {
        $gateway = new TestGateway('k');
        $mock = $this->mockHttp([[200, ['id' => 'i1']]]);

        $gateway->setClient($mock);

        $this->assertSame($mock, $gateway->client());
    }
}

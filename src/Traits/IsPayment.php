<?php

namespace Rohama\Koin\Traits;

use GuzzleHttp\Client;
use Rohama\Koin\Exception\InvalidConfigException;
use Rohama\Koin\PaymentGateway;
use Rohama\Translator\Translator;

trait IsPayment
{
    public function __construct(
        public readonly string $apiKey,
        public readonly ?string $callbackUrl = null,
        public readonly bool $sandbox = false,
        public readonly array $extra = [],
    ) {}

    protected Client $client;

    public function getName(): string
    {
        return self::name;
    }

    public function displayName(?string $locale = null): string
    {
        return Translator::trans(self::name.'.name', locale: $locale ?? Translator::getDefaultLocale());
    }

    public static function fromArray(array $config): PaymentGateway
    {
        $driver = (string) ($config['driver'] ?? '');

        if (is_a($driver, PaymentGateway::class, true)) {
            unset($config['driver']);

            return new $driver(...$config);
        }

        throw new InvalidConfigException(sprintf('No gateway is registered under the driver "%s".', $driver));
    }

    public function toArray(): array
    {
        return [
            'driver' => self::class,
            'apiKey' => $this->apiKey,
            'callbackUrl' => $this->callbackUrl,
            'sandbox' => $this->sandbox,
            'extra' => $this->extra,
        ];
    }

    public function client(): Client
    {
        return $this->client;
    }
}

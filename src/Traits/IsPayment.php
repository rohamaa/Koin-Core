<?php

namespace Rohama\Koin\Traits;

use GuzzleHttp\Client;
use Rohama\Koin\Exception\InvalidConfigException;
use Rohama\Koin\PaymentGateway;
use Rohama\Koin\SandboxGateway;
use Rohama\Translator\Translator;

trait IsPayment
{
    public function __construct(
        public string $apiKey,
        public ?string $callbackUrl = null,
        public bool $sandbox = false,
        public array $extra = [],
    ) {
    }

    protected Client $client;

    public function __get(string $name): mixed
    {
        return $this->extra[$name] ?? null;
    }

    public function __set(string $name, mixed $value): void
    {
        $this->extra[$name] = $value;
    }

    public function getName(): string
    {
        return self::name;
    }

    public function displayName(?string $locale = null): string
    {
        return Translator::trans(self::name.'.name', locale: $locale ?? Translator::getDefaultLocale());
    }

    public static function configFields(): array
    {
        $fields = [
            [
                'key' => 'apiKey',
                'name' => 'API Key',
                'desc' => 'The gateway credential / API key.',
                'type' => 'text',
                'required' => true,
                'validation' => ['required', 'string'],
                'regex' => '/^.+$/',
            ],
            [
                'key' => 'callbackUrl',
                'name' => 'Callback URL',
                'desc' => 'The return URL the gateway redirects the payer to after payment.',
                'type' => 'url',
                'required' => false,
                'validation' => ['nullable', 'url'],
                'regex' => null,
            ],
        ];

        if (is_a(static::class, SandboxGateway::class, true)) {
            $fields[] = [
                'key' => 'sandbox',
                'name' => 'Sandbox mode',
                'desc' => 'Use the gateway sandbox/test environment instead of the live one.',
                'type' => 'checkbox',
                'required' => false,
                'validation' => ['boolean'],
                'regex' => null,
            ];
        }

        return $fields;
    }

    public static function fromFields(array $fields): PaymentGateway
    {
        $extra = [];

        foreach (static::configFields() as $field) {
            $value = $fields[$field['key']] ?? null;

            if (($field['required'] ?? false) && ($value === null || $value === '')) {
                throw new InvalidConfigException(sprintf('The "%s" field is required.', $field['name']));
            }

            if (isset($field['regex']) && $value !== null && $value !== '' && !preg_match($field['regex'], (string) $value)) {
                throw new InvalidConfigException(sprintf('The "%s" field is invalid.', $field['name']));
            }

            if (!in_array($field['key'], ['apiKey', 'callbackUrl', 'sandbox'], true) && $value !== null && $value !== '') {
                $extra[$field['key']] = $value;
            }
        }

        $callbackUrl = isset($fields['callbackUrl']) && $fields['callbackUrl'] !== '' ? $fields['callbackUrl'] : null;

        return new static(
            (string) $fields['apiKey'],
            $callbackUrl,
            (bool) ($fields['sandbox'] ?? false),
            $extra,
        );
    }

    public function toFields(): array
    {
        return [
            'apiKey' => $this->apiKey,
            'callbackUrl' => $this->callbackUrl,
            'sandbox' => $this->sandbox,
            ...$this->extra,
        ];
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

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }
}

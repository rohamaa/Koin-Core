<?php

namespace Rohama\Koin\Http;

use GuzzleHttp\Client as Guzzle;
use Psr\Http\Message\ResponseInterface;

/**
 * The default transport: Guzzle wrapped behind the small Client contract.
 */
final class GuzzleClient extends Client
{
    private ?Guzzle $client = null;

    public function __construct(private array $config = [])
    {
    }

    public function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        return $this->guzzle()->request(strtoupper($method), $uri, $options);
    }

    private function guzzle(): Guzzle
    {
        return $this->client ??= new Guzzle($this->config);
    }
}

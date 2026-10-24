<?php

use Rohama\Koin\Http\GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

abstract class TestCase extends PHPUnit\Framework\TestCase
{
    protected function response(int $status, array $data): ResponseInterface
    {
        return new Response($status, ['Content-Type' => 'application/json'], json_encode($data));
    }

    protected function mockHttp(array $responses): GuzzleClient
    {
        $queue = [];

        foreach ($responses as [$status, $payload]) {
            $queue[] = new Response($status, ['Content-Type' => 'application/json'], json_encode($payload));
        }

        return new GuzzleClient(['handler' => new MockHandler($queue)]);
    }
}

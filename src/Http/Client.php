<?php

namespace Rohama\Koin\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * The transport contract behind every driver.
 *
 * The API deliberately mirrors Guzzle: `get()` / `post()` with Guzzle-style
 * options (`query`, `json`, `form_params`, `headers`). The default transport
 * is `GuzzleClient`; to supply your own HTTP transport (e.g. a WordPress
 * plugin wrapping WP_Http) extend this class and implement request() — the
 * drivers cannot tell the difference because they only call these methods.
 */
abstract class Client
{
    /**
     * Perform the HTTP request and return the response.
     *
     * @param  string  $method  HTTP method (GET, POST, DELETE, ...)
     * @param  string  $uri     request URL (absolute, or resolved against your base URI)
     * @param  array  $options  Guzzle-style options: query, json, form_params, headers, body
     */
    abstract public function request(string $method, string $uri, array $options = []): ResponseInterface;

    public function get(string $uri, array $options = []): ResponseInterface
    {
        return $this->request('GET', $uri, $options);
    }

    public function post(string $uri, array $options = []): ResponseInterface
    {
        return $this->request('POST', $uri, $options);
    }

    public function put(string $uri, array $options = []): ResponseInterface
    {
        return $this->request('PUT', $uri, $options);
    }

    public function patch(string $uri, array $options = []): ResponseInterface
    {
        return $this->request('PATCH', $uri, $options);
    }

    public function delete(string $uri, array $options = []): ResponseInterface
    {
        return $this->request('DELETE', $uri, $options);
    }

    public function head(string $uri, array $options = []): ResponseInterface
    {
        return $this->request('HEAD', $uri, $options);
    }
}

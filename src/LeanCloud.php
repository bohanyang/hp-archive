<?php

declare(strict_types=1);

namespace App;

use Symfony\Contracts\HttpClient\HttpClientInterface;

use function is_scalar;
use function json_encode;
use function parse_url;

use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use const PHP_URL_PATH;

class LeanCloud
{
    private array $options;
    private string $basePath;

    public function __construct(
        private HttpClientInterface $client,
        string $endpoint,
        string $appId,
        string $appKey,
        string $sessionToken = '',
    ) {
        $this->basePath = parse_url($endpoint, PHP_URL_PATH);
        $headers        = [
            'Content-Type' => 'application/json',
            'X-LC-Id' => $appId,
            'X-LC-Key' => $appKey,
        ];

        if ($sessionToken !== '') {
            $headers['X-LC-Session'] = $sessionToken;
        }

        $this->options = ['base_uri' => $endpoint, 'headers' => $headers];
    }

    private function encodeQuery(array $query)
    {
        foreach ($query as $key => $value) {
            if ($value === null) {
                unset($query[$key]);
                continue;
            }

            if (! is_scalar($value)) {
                $value = $this->jsonEncode($value);
            }

            $query[$key] = $value;
        }

        return $query;
    }

    public function query(string $collection, array $query = [], array $body = []): array
    {
        return $this->client->request('GET', "classes/$collection", [
            'query' => $this->encodeQuery($query),
            'body' => $this->encodeQuery($body),
        ] + $this->options)->toArray();
    }

    public function update(string $collection, string $objectId, array $object): array
    {
        return $this->client
            ->request('PUT', "classes/$collection/$objectId", ['json' => $object] + $this->options)
            ->toArray();
    }

    public function insert(string $collection, array $object): array
    {
        return $this->client
            ->request('POST', "classes/$collection", ['json' => $object] + $this->options)
            ->toArray();
    }

    /** @param array $requests [ [$collection, $objectId, $object], ... ] */
    public function batchUpdate(array $requests): array
    {
        return $this->batchRequest($requests, function (array $request) {
            [$collection, $objectId, $object] = $request;

            return [
                'method' => 'PUT',
                'path' => "{$this->basePath}classes/$collection/$objectId",
                'body' => $object,
            ];
        });
    }

    private function batchRequest(array $requests, callable $filter): array
    {
        foreach ($requests as $i => $request) {
            $requests[$i] = $filter($request);
        }

        return $this->client->request('POST', 'batch', [
            'json' => ['requests' => $requests],
        ] + $this->options)->toArray();
    }

    private function jsonEncode(array $value)
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

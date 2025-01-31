<?php

declare(strict_types=1);

namespace App\Downloader\Storage;

use GuzzleHttp\Promise\PromiseInterface;
use Manyou\PromiseHttpClient\PromiseHttpClientInterface;

use function Safe\rewind;
use function substr_compare;

class BunnyCDNStorage implements Storage
{
    private string $baseUri;

    public function __construct(
        string $baseUri,
        private string $accessKey,
        private string $prefix,
        private PromiseHttpClientInterface $httpClient,
    ) {
        $this->baseUri = substr_compare($baseUri, '/', -1) === 0 ? $baseUri : $baseUri . '/';
    }

    public function put($stream, string $path, string $contentType): PromiseInterface
    {
        rewind($stream);

        return $this->httpClient->request('PUT', $this->prefix . $path, [
            'headers' => ['AccessKey' => $this->accessKey],
            'timeout' => 31536000.0,
            'http_version' => '1.1',
            'base_uri' => $this->baseUri,
            'body' => $stream,
        ]);
    }
}

<?php

declare(strict_types=1);

namespace App\Bundle\Downloader\Storage;

use AsyncAws\Core\Result;
use AsyncAws\S3\S3Client;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;

use function Safe\rewind;

class S3Storage implements Storage
{
    public function __construct(
        private S3Client $client,
        private string $bucket,
        private string $prefix,
        private array $options,
    ) {
    }

    public function put($stream, string $path, string $contentType): PromiseInterface
    {
        rewind($stream);

        $options = [
            'Bucket' => $this->bucket,
            'Key' => $this->prefix . $path,
            'Body' => $stream,
            'ContentType' => $contentType,
        ];

        return self::promise($this->client->putObject($options + $this->options));
    }

    private static function promise(Result $result): PromiseInterface
    {
        $promise = new Promise(
            static function () use (&$promise, $result) {
                $result->resolve();
                $promise->resolve($result);
            },
            static function () use ($result) {
                $result->cancel();
            },
        );

        return $promise;
    }
}

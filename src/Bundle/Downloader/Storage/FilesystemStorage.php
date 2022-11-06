<?php

declare(strict_types=1);

namespace App\Bundle\Downloader\Storage;

use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;

use function dirname;
use function is_dir;
use function Safe\fopen;
use function Safe\mkdir;
use function Safe\rewind;
use function Safe\stream_copy_to_stream;

class FilesystemStorage implements Storage
{
    public function __construct(private string $prefix)
    {
    }

    public function put($stream, string $path, string $contentType): PromiseInterface
    {
        $promise = new Promise(function () use (&$promise, $stream, $path) {
            rewind($stream);

            $path = $this->prefix . $path;
            if (! is_dir($dir = dirname($path))) {
                mkdir($dir, 0755, true);
            }

            $writer = fopen($path, 'w');
            stream_copy_to_stream($stream, $writer);
            $promise->resolve(true);
        });

        return $promise;
    }
}

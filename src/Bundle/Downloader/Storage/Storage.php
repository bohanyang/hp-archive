<?php

declare(strict_types=1);

namespace App\Bundle\Downloader\Storage;

use GuzzleHttp\Promise\PromiseInterface;

interface Storage
{
     /** @param resource $stream */
    public function put($stream, string $path, string $contentType): PromiseInterface;
}

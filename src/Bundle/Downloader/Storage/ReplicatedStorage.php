<?php

declare(strict_types=1);

namespace App\Bundle\Downloader\Storage;

use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Promise\Utils;

use function Safe\fopen;
use function Safe\rewind;
use function Safe\stream_copy_to_stream;

class ReplicatedStorage implements Storage
{
    /** @var Storage[] */
    private array $replicas;

    public function __construct(Storage ...$replicas)
    {
        $this->replicas = $replicas;
    }

    public function put($stream, string $path, string $contentType): PromiseInterface
    {
        $promises = [];

        foreach ($this->replicas as $replica) {
            rewind($stream);
            $copy = fopen('php://memory', 'w+');
            stream_copy_to_stream($stream, $copy);
            $promises[] = $replica->put($copy, $path, $contentType);
        }

        return Utils::all($promises);
    }
}

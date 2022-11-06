<?php

declare(strict_types=1);

namespace App\Bundle\Downloader;

use App\Bundle\Downloader\Storage\Storage;
use Manyou\PromiseHttpClient\PromiseHttpClientInterface;
use Manyou\PromiseHttpClient\RequiresPromiseHttpClient;
use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

use function Safe\fopen;
use function sprintf;
use function str_ends_with;
use function str_starts_with;
use function strlen;
use function substr;

class VideoDownloader
{
    use RequiresPromiseHttpClient;

    private int $prefixToRemoveLength;

    public function __construct(
        PromiseHttpClientInterface|HttpClientInterface $httpClient,
        private Storage $storage,
    ) {
        $this->setHttpClient($httpClient);
    }

    public function __invoke(array $video): void
    {
        if (null === $url = $video['sources'][1][2] ?? null) {
            throw new RuntimeException('No video URL.');
        }

        if (
            ! str_starts_with($url, $prefix = '//az29176.vo.msecnd.net/videocontent/') ||
            ! str_ends_with($url, '.mp4')
        ) {
            throw new RuntimeException(sprintf('Unexpected video URL: "%s".', $url));
        }

        $response = $this->httpClient->request('GET', 'https:' . $url, [
            'buffer' => $stream = fopen('php://memory', 'w+'),
        ]);

        $savePath = substr($url, strlen($prefix));

        $response->then(function (ResponseInterface $response) use ($stream, $savePath) {
            $response->getContent();

            return $this->storage->put($stream, $savePath, 'video/mp4');
        })->wait();
    }
}

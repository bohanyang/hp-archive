<?php

declare(strict_types=1);

namespace App\Downloader;

use App\Downloader\Storage\Storage;
use App\ImageSpec\ImageSpec;
use GuzzleHttp\Promise\Utils;
use Manyou\PromiseHttpClient\PromiseHttpClientInterface;
use RuntimeException;
use Symfony\Contracts\HttpClient\ResponseInterface;

use function basename;
use function fopen;
use function mb_strlen;
use function sprintf;
use function str_starts_with;
use function substr;

class ImageDownloader
{
    private int $prefixToRemoveLength;

    public function __construct(
        private PromiseHttpClientInterface $httpClient,
        private Storage $storage,
        private string $prefixToRemove = '',
        private string $endpoint = 'https://www.bing.com/th?id=OHR.',
    ) {
        $this->prefixToRemoveLength = mb_strlen($prefixToRemove);
    }

    private function removePrefix(string $urlbase): string
    {
        if (! str_starts_with($urlbase, $this->prefixToRemove)) {
            throw new RuntimeException(sprintf(
                'Base URL does not start with prefix "%s": "%s".',
                $this->prefixToRemove,
                $urlbase,
            ));
        }

        return substr($urlbase, $this->prefixToRemoveLength);
    }

    /** @param ImageSpec[] $specs */
    public function __invoke(string $urlbase, array $specs): void
    {
        $urlbase = $this->removePrefix($urlbase);

        $promises = [];
        foreach ($specs as $spec) {
            $savePath = $spec->generateUrl($urlbase);

            $response = $this->httpClient->request('GET', $this->endpoint . basename($savePath), [
                'buffer' => $stream = fopen('php://memory', 'w+'),
                'max_redirects' => 0,
            ]);

            $promises[] = $response->then(function (ResponseInterface $response) use ($spec, $stream, $savePath) {
                $response->getContent();
                $spec->assert($stream);

                return $this->storage->put($stream, $savePath, $spec->getMimeType());
            });
        }

        Utils::unwrap($promises);
    }
}

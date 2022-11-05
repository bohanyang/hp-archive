<?php

declare(strict_types=1);

namespace Manyou\PromiseHttpClient;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

trait RequiresPromiseHttpClient
{
    private PromiseHttpClientInterface $httpClient;

    private function setHttpClient(PromiseHttpClientInterface|HttpClientInterface|null $httpClient = null): void
    {
        $httpClient ??= HttpClient::create();

        if ($httpClient instanceof HttpClientInterface) {
            $httpClient = new RetryableHttpClient(new PromiseHttpClient($httpClient));
        }

        $this->httpClient = $httpClient;
    }
}

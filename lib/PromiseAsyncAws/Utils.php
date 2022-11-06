<?php

declare(strict_types=1);

namespace Manyou\PromiseAsyncAws;

use AsyncAws\Core\Result;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;

class Utils
{
    public static function promise(Result $result): PromiseInterface
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

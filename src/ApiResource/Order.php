<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;

#[ApiResource(
    provider: OrderProvider::class,
    processor: OrderProcessor::class,
    operations: [
        new Get('/orders/{id}'),
        new GetCollection('/orders'),
        new Post('/orders', input: NewOrder::class),
    ],
)]
class Order
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $status,
    ) {
    }
}

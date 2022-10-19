<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;

#[ApiResource(
    provider: OrderProvider::class,
    processor: OrderProcessor::class,
    operations: [
        new Get('/orders/{id}'),
        new Post('/orders', input: NewOrder::class),
    ],
)]
class Order
{
    public function __construct(
        public int $id,
        public string $name,
        public string $status,
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ApiResource(
    provider: ImageProvider::class,
    processor: StateProcessor::class,
    operations: [
        new Get('/images/{name}', uriVariables: ['name'], requirements: ['name' => '\w+']),
        new Post('/images'),
    ],
)]
class Image
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        #[SerializedName('debut_on')]
        public readonly DateTimeImmutable $debutOn,
        public readonly string $urlbase,
        public readonly string $copyright,
        public readonly bool $downloadable,
        public readonly ?array $video = null,
        ...$args,
    ) {
    }
}

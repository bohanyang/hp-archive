<?php

declare(strict_types=1);

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use DateTimeImmutable;

#[ApiResource(
    provider: RecordProvider::class,
    processor: StateProcessor::class,
    operations: [
        new Get(
            '/records/{market}/{date}',
            uriVariables: [
                'market',
                'date' => new Link(fromClass: self::class, identifiers: ['dateString']),
            ],
            requirements: ['market' => '[a-z]{2}-[A-Z]{2}', 'date' => '\d{8}'],
        ),
        new Post('/records'),
    ],
)]
class Record
{
    public readonly string $imageId;

    public function __construct(
        public readonly string $id,
        #[ApiProperty(readableLink: true)]
        public readonly Image $image,
        public readonly DateTimeImmutable $date,
        public readonly string $market,
        public readonly string $title,
        public readonly string $keyword,
        public readonly ?string $headline = null,
        public readonly ?string $description = null,
        public readonly ?string $quickfact = null,
        public readonly ?array $hotspots = null,
        public readonly ?array $messages = null,
        public readonly ?array $coverstory = null,
        ...$args,
    ) {
        $this->imageId = $image->id;
    }

    public function getDateString(): string
    {
        return $this->date->format('Ymd');
    }
}

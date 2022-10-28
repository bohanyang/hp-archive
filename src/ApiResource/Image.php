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
        public string $id,
        public readonly string $name,
        #[SerializedName('debut_on')]
        public readonly DateTimeImmutable $debutOn,
        public readonly string $urlbase,
        public readonly string $copyright,
        public readonly bool $downloadable,
        public readonly ?array $video = null,
    ) {
    }

    public static function createFromLeanCloud(array $data): self
    {
        return new self(
            $data['objectId'],
            $data['name'],
            DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.vp', $data['firstAppearedOn']['iso']),
            $data['urlbase'],
            $data['copyright'],
            $data['wp'],
            $data['vid'] ?? null,
        );
    }

    public function toLeanCloud(): array
    {
        return [
            'objectId' => $this->id,
            'name' => $this->name,
            'firstAppearedOn' => [
                '__type' => 'Date',
                'iso' => $this->debutOn->format('Y-m-d\TH:i:s.vp'),
            ],
            'urlbase' => $this->urlbase,
            'copyright' => $this->copyright,
            'wp' => $this->downloadable,
            'vid' => $this->video,
        ];
    }

    public function equalsTo(self $image): bool
    {
        return $image->copyright === $this->copyright
            && $image->downloadable === $this->downloadable
            && $image->name === $this->name;
    }

    public function getDataForUpdate(): array
    {
        return [
            'copyright' => $this->copyright,
            'downloadable' => $this->downloadable,
        ];
    }

    public function getDataForUpdateLeanCloud(): array
    {
        return [
            'copyright' => $this->copyright,
            'wp' => $this->downloadable,
        ];
    }
}

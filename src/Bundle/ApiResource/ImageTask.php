<?php

declare(strict_types=1);

namespace App\Bundle\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Bundle\ApiPlatform\ImageTaskProcessor;
use App\Bundle\ApiPlatform\ImageTaskProvider;
use App\Bundle\Message\RetryDownloadImage;
use Manyou\Mango\TaskQueue\Enum\TaskStatus;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;

#[ApiResource(provider: ImageTaskProvider::class, security: 'is_granted("ROLE_STAFF")')]
#[GetCollection('/image_operations', normalizationContext: ['groups' => ['read']])]
#[Get('/image_operations/{id}')]
#[Post('/image_operations/{id}/retry', input: RetryDownloadImage::class, output: RetryDownloadImage::class, status: 202, processor: ImageTaskProcessor::class)]
class ImageTask
{
    public function __construct(
        #[Groups('read')]
        public readonly Ulid $id,
        #[Groups('read')]
        public readonly string $name,
        #[Groups('read')]
        public readonly string $urlbase,
        #[Groups('read')]
        public readonly ?array $video,
        #[Groups('read')]
        public readonly TaskStatus $status,
        public readonly array $logs = [],
    ) {
    }
}

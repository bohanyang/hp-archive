<?php

declare(strict_types=1);

namespace App\Bundle\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Bundle\ApiPlatform\ImageOperationProcessor;
use App\Bundle\ApiPlatform\ImageOperationProvider;
use App\Bundle\Message\RetryDownloadImage;
use Manyou\Mango\Operation\Enum\OperationStatus;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;

#[ApiResource(provider: ImageOperationProvider::class, security: 'is_authenticated()')]
#[GetCollection('/image_operations', normalizationContext: ['groups' => ['read']])]
#[Get('/image_operations/{id}')]
#[Post('/image_operations/{id}/retry', input: RetryDownloadImage::class, output: RetryDownloadImage::class, status: 202, processor: ImageOperationProcessor::class)]
class ImageOperation
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
        public readonly OperationStatus $status,
        public readonly array $logs = [],
    ) {
    }
}

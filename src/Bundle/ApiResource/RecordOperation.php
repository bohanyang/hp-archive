<?php

declare(strict_types=1);

namespace App\Bundle\ApiResource;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Bundle\ApiPlatform\RecordOperationProcessor;
use App\Bundle\ApiPlatform\RecordOperationProvider;
use App\Bundle\Message\RetryCollectRecord;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;

#[ApiResource(provider: RecordOperationProvider::class, security: 'is_authenticated()')]
#[GetCollection('/record_operations', normalizationContext: ['groups' => ['read']])]
#[Get('/record_operations/{id}')]
#[Post('/record_operations/{id}/retry', input: RetryCollectRecord::class, output: RetryCollectRecord::class, status: 202, processor: RecordOperationProcessor::class)]
class RecordOperation
{
    public function __construct(
        #[Groups('read')]
        public readonly Ulid $id,
        #[Groups('read')]
        public readonly string $market,
        #[Groups('read')]
        public readonly DateTimeImmutable $date,
        #[Groups('read')]
        public readonly string $status,
        public readonly array $logs = [],
    ) {
    }
}

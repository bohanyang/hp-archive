<?php

declare(strict_types=1);

namespace App\Bundle\Message;

use ApiPlatform\Metadata\ApiProperty;
use App\Bundle\ApiResource\RecordOperation;
use App\Bundle\Message\SaveRecord\OnDuplicateImage;
use Symfony\Component\Uid\Ulid;

class RetryCollectRecord
{
    public readonly OnDuplicateImage $policy;

    public readonly Ulid $id;

    public function __construct(RecordOperation $operation)
    {
        $this->id = $operation->id;
    }

    #[ApiProperty(openapiContext: ['enum' => ['error', 'update', 'ignore']])]
    public function setPolicy(string $policy = 'error')
    {
        $this->policy = match ($policy) {
            'error' => OnDuplicateImage::THROW_IF_DIFFER,
            'update' => OnDuplicateImage::UPDATE_EXISTING,
            'ignore' => OnDuplicateImage::REFER_EXISTING,
        };
    }
}

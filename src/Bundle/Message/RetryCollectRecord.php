<?php

declare(strict_types=1);

namespace App\Bundle\Message;

use App\Bundle\ApiResource\RecordTask;
use App\Bundle\Message\SaveRecord\OnDuplicateImage;
use Symfony\Component\Uid\Ulid;

class RetryCollectRecord
{
    public readonly OnDuplicateImage $policy;

    public readonly Ulid $id;

    public function __construct(RecordTask $task)
    {
        $this->id = $task->id;
    }

    public function setPolicy(string $policy = 'error')
    {
        $this->policy = match ($policy) {
            'error' => OnDuplicateImage::THROW_IF_DIFFER,
            'update' => OnDuplicateImage::UPDATE_EXISTING,
            'ignore' => OnDuplicateImage::REFER_EXISTING,
        };
    }
}

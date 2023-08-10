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

    public function setPolicy(OnDuplicateImage $policy)
    {
        $this->policy = $policy;
    }
}

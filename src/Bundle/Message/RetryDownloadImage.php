<?php

declare(strict_types=1);

namespace App\Bundle\Message;

use App\Bundle\ApiResource\ImageTask;
use Symfony\Component\Uid\Ulid;

class RetryDownloadImage
{
    public readonly Ulid $id;

    public function __construct(ImageTask $task)
    {
        $this->id = $task->id;
    }
}

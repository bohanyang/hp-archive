<?php

declare(strict_types=1);

namespace Manyou\Mango\Operation\Messenger\Stamp;

use Closure;
use Symfony\Component\Messenger\Stamp\StampInterface;
use Symfony\Component\Uid\Ulid;

class CreateOperationStamp implements StampInterface
{
    public function __construct(private Closure $callback)
    {
    }

    public function callback(Ulid $id): void
    {
        ($this->callback)($id);
    }
}

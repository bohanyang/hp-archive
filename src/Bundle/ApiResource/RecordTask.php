<?php

declare(strict_types=1);

namespace App\Bundle\ApiResource;

use DateTimeImmutable;
use Manyou\Mango\TaskQueue\Enum\TaskStatus;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;

class RecordTask
{
    public function __construct(
        #[Groups('read')]
        public readonly Ulid $id,
        #[Groups('read')]
        public readonly string $market,
        #[Groups('read')]
        public readonly DateTimeImmutable $date,
        #[Groups('read')]
        public readonly TaskStatus $status,
        public readonly array $logs = [],
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\Bundle\ApiResource;

use Manyou\Mango\TaskQueue\Enum\TaskStatus;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Ulid;

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

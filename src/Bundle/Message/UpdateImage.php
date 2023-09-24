<?php

declare(strict_types=1);

namespace App\Bundle\Message;

use DateTimeImmutable;

class UpdateImage
{
    public function __construct(
        public readonly string $name,
        public readonly DateTimeImmutable $date,
        public readonly string $market,
    ) {
    }
}

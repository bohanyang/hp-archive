<?php

declare(strict_types=1);

namespace App\Bundle\Message;

use DateTimeImmutable;
use Manyou\BingHomepage\Market;

class CollectRecords
{
    /** @param Market[] $markets */
    public function __construct(
        public readonly DateTimeImmutable $date,
        public readonly array $markets,
    ) {
    }
}

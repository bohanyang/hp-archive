<?php

declare(strict_types=1);

namespace App\Message;

use DateTimeInterface;

class ImportFromLeanCloud
{
    public function __construct(public readonly ?DateTimeInterface $createdLaterThan = null)
    {
    }
}

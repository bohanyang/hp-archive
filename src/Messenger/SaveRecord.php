<?php

declare(strict_types=1);

namespace App\Messenger;

use App\ApiResource\Record;

class SaveRecord
{
    public function __construct(
        public readonly Record $record,
        public readonly OnDuplicateImage $policy,
    ) {
    }

    public function throwIfDiffer(): bool
    {
        return $this->policy === OnDuplicateImage::THROW_IF_DIFFER;
    }

    public function updateExisting(): bool
    {
        return $this->policy === OnDuplicateImage::UPDATE_EXISTING;
    }

    public function referExisting(): bool
    {
        return $this->policy === OnDuplicateImage::REFER_EXISTING;
    }
}

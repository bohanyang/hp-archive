<?php

declare(strict_types=1);

namespace App\Message;

use App\Repository\DoctrineRepository;
use App\Repository\LeanCloudRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ImportFromLeanCloudHandler
{
    public function __construct(
        private DoctrineRepository $doctrine,
        private LeanCloudRepository $leanCloud,
    ) {
    }

    public function __invoke(ImportFromLeanCloud $command): void
    {
        Utils::iterate($this->leanCloud->exportImages($command->createdLaterThan), [$this->doctrine, 'importImages']);
        Utils::iterate($this->leanCloud->exportRecords($command->createdLaterThan), [$this->doctrine, 'importRecords']);
    }
}

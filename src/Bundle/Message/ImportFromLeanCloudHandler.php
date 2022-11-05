<?php

declare(strict_types=1);

namespace App\Bundle\Message;

use App\Bundle\Repository\DoctrineRepository;
use App\Bundle\Repository\LeanCloudRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ImportFromLeanCloudHandler
{
    public function __construct(
        private DoctrineRepository $doctrine,
        private LeanCloudRepository $leancloud,
    ) {
    }

    public function __invoke(ImportFromLeanCloud $command): void
    {
        Utils::iterate($this->leancloud->exportImages($command->createdLaterThan), [$this->doctrine, 'importImages']);
        Utils::iterate($this->leancloud->exportRecords($command->createdLaterThan), [$this->doctrine, 'importRecords']);
    }
}

<?php

declare(strict_types=1);

namespace App\Message;

use App\Repository\DoctrineRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class ImportFromSqlHandler
{
    public function __construct(private DoctrineRepository $source, private DoctrineRepository $destination)
    {
    }

    public function __invoke(ImportFromSql $command): void
    {
        Utils::iterate($this->source->exportImages(), [$this->destination, 'importImages']);
        Utils::iterate($this->source->exportRecords(), [$this->destination, 'importRecords']);
    }
}

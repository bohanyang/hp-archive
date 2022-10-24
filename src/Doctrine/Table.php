<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table as DoctrineTable;

class Table
{
    public function __construct(
        private DoctrineTable $table,
        private array $columnNameMap = [],
    ) {
    }

    public function getColumn(string $name): Column
    {
        return $this->table->getColumn($this->columnNameMap[$name] ?? $name);
    }
}

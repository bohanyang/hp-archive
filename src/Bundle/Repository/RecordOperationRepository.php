<?php

declare(strict_types=1);

namespace App\Bundle\Repository;

use App\Bundle\Doctrine\TableProvider\RecordOperationsTable;
use Manyou\Mango\Doctrine\Contract\PolymorphicSubType;

class RecordOperationRepository implements PolymorphicSubType
{
    public function getTableName(): string
    {
        return RecordOperationsTable::NAME;
    }
}

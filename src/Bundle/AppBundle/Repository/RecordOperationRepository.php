<?php

declare(strict_types=1);

namespace App\Bundle\AppBundle\Repository;

use App\Bundle\AppBundle\Doctrine\TableProvider\RecordOperationsTable;
use App\Bundle\CoreBundle\Doctrine\Contract\PolymorphicSubType;

class RecordOperationRepository implements PolymorphicSubType
{
    public function getTableName(): string
    {
        return RecordOperationsTable::NAME;
    }
}

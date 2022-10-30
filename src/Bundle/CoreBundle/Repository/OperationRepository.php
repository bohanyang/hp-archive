<?php

declare(strict_types=1);

namespace App\Bundle\CoreBundle\Repository;

use App\Bundle\CoreBundle\Doctrine\PolymorphicBase;
use App\Bundle\CoreBundle\Doctrine\SchemaProvider;
use App\Bundle\CoreBundle\Doctrine\TableProvider\OperationsTable;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class OperationRepository
{
    use PolymorphicBase;

    public function __construct(
        #[TaggedIterator('core.operation')]
        iterable $repositories,
        private SchemaProvider $schema,
    ) {
        $this->subTypes  = $repositories;
        $this->tableName = OperationsTable::NAME;
    }
}

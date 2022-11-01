<?php

declare(strict_types=1);

namespace Manyou\Mango\Operation\Repository;

use Manyou\Mango\Doctrine\PolymorphicBase;
use Manyou\Mango\Doctrine\SchemaProvider;
use Manyou\Mango\Doctrine\TableProvider\OperationsTable;
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

<?php

declare(strict_types=1);

namespace Manyou\Mango\Doctrine\TableProvider;

use Manyou\Mango\Doctrine\Contract\TableProvider;
use Manyou\Mango\Doctrine\Table;
use Manyou\Mango\Doctrine\Type\OperationStatusType;
use Doctrine\DBAL\Schema\Schema;

class OperationsTable implements TableProvider
{
    public const NAME = 'operations';

    public function __invoke(Schema $schema): Table
    {
        $table = new Table($schema, self::NAME);
        $table->addColumn('id', 'ulid');
        $table->addColumn('status', OperationStatusType::NAME, OperationStatusType::DEFAULT_OPTIONS);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['status']);

        return $table;
    }
}
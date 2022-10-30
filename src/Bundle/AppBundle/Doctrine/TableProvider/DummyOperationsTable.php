<?php

declare(strict_types=1);

namespace App\Bundle\AppBundle\Doctrine\TableProvider;

use App\Bundle\CoreBundle\Doctrine\Contract\TableProvider;
use App\Bundle\CoreBundle\Doctrine\Table;
use App\Bundle\CoreBundle\Doctrine\TableProvider\OperationsTable;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;

class DummyOperationsTable implements TableProvider
{
    public const NAME = 'dummy_operations';

    public function __invoke(Schema $schema): Table
    {
        $table = new Table($schema, self::NAME);
        $table->addColumn('id', 'ulid');
        $table->addColumn('content', Types::TEXT);
        $table->setPrimaryKey(['id']);
        $table->addForeignKeyConstraint(OperationsTable::NAME, ['id'], ['id']);

        return $table;
    }
}

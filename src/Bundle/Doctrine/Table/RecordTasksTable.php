<?php

declare(strict_types=1);

namespace App\Bundle\Doctrine\Table;

use App\Bundle\Doctrine\Type\BingMarketType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Manyou\Mango\Doctrine\Contract\TableProvider;
use Manyou\Mango\Doctrine\Table;
use Manyou\Mango\TaskQueue\Doctrine\Table\TasksTable;

class RecordTasksTable implements TableProvider
{
    public const NAME = 'record_tasks';

    public function __invoke(Schema $schema): Table
    {
        $table = new Table($schema, self::NAME);
        $table->addColumn('id', 'ulid');
        $table->addColumn('date', Types::DATE_IMMUTABLE);
        $table->addColumn('market', BingMarketType::NAME);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['date', 'market']);
        $table->addForeignKeyConstraint(TasksTable::NAME, ['id'], ['id']);

        return $table;
    }
}

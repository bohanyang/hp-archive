<?php

declare(strict_types=1);

namespace App\Bundle\Doctrine\Table;

use App\Bundle\Doctrine\Type\BingMarketType;
use Doctrine\DBAL\Types\Types;
use Mango\Doctrine\Schema\TableBuilder;
use Mango\Doctrine\Table;
use Mango\TaskQueue\Doctrine\Table\TasksTable;

class RecordTasksTable implements TableBuilder
{
    public const NAME = 'record_tasks';

    public function getName(): string
    {
        return self::NAME;
    }

    public function build(Table $table): void
    {
        $table->addColumn('id', 'ulid');
        $table->addColumn('date', Types::DATE_IMMUTABLE);
        $table->addColumn('market', BingMarketType::NAME);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['date', 'market']);
        $table->addForeignKeyConstraint(TasksTable::NAME, ['id'], ['id']);
    }
}

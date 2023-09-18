<?php

declare(strict_types=1);

namespace App\Bundle\Doctrine\Table;

use App\Bundle\Doctrine\Type\ObjectIdType;
use Mango\Doctrine\Schema\TableBuilder;
use Mango\Doctrine\Table;
use Mango\TaskQueue\Doctrine\Table\TasksTable;

class ImageTasksTable implements TableBuilder
{
    public const NAME = 'image_tasks';

    public function getName(): string
    {
        return self::NAME;
    }

    public function build(Table $table): void
    {
        $table->addColumn('id', 'ulid');
        $table->addColumn('image_id', ObjectIdType::NAME, ObjectIdType::DEFAULT_OPTIONS);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['image_id']);
        $table->addForeignKeyConstraint(TasksTable::NAME, ['id'], ['id']);
        $table->addForeignKeyConstraint(ImagesTable::NAME, ['image_id'], ['id']);
    }
}

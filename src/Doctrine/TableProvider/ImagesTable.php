<?php

declare(strict_types=1);

namespace App\Doctrine\TableProvider;

use App\Doctrine\Contract\TableProvider;
use App\Doctrine\Table;
use App\Doctrine\Type\JsonTextType;
use App\Doctrine\Type\ObjectIdType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;

class ImagesTable implements TableProvider
{
    public function __invoke(Schema $schema): Table
    {
        $table = new Table($schema, 'images');
        $table->addColumn('id', ObjectIdType::NAME, ObjectIdType::DEFAULT_OPTIONS);
        $table->addColumn('name', Types::STRING, ['length' => 255]);
        $table->addColumn('debut_on', Types::DATE_IMMUTABLE, alias: 'debutOn');
        $table->addColumn('urlbase', Types::STRING, ['length' => 255]);
        $table->addColumn('copyright', Types::STRING, ['length' => 255]);
        $table->addColumn('downloadable', Types::BOOLEAN);
        $table->addColumn('video', JsonTextType::NAME, ['length' => 2000, 'notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['name']);
        $table->addIndex(['debut_on']);

        return $table;
    }
}

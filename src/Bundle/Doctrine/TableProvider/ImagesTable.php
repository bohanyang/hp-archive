<?php

declare(strict_types=1);

namespace App\Bundle\Doctrine\TableProvider;

use App\Bundle\Doctrine\Type\JsonTextType;
use App\Bundle\Doctrine\Type\ObjectIdType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Manyou\Mango\Doctrine\Contract\TableProvider;
use Manyou\Mango\Doctrine\Table;

class ImagesTable implements TableProvider
{
    public const NAME = 'images';

    public function __invoke(Schema $schema): Table
    {
        $table = new Table($schema, self::NAME);
        $table->addColumn('id', ObjectIdType::NAME, ObjectIdType::DEFAULT_OPTIONS);
        $table->addColumn('name', Types::STRING, ['length' => 500]);
        $table->addColumn('debut_on', Types::DATE_IMMUTABLE, alias: 'debutOn');
        $table->addColumn('urlbase', Types::STRING, ['length' => 500]);
        $table->addColumn('copyright', Types::STRING, ['length' => 500]);
        $table->addColumn('downloadable', Types::BOOLEAN);
        $table->addColumn('video', JsonTextType::NAME, ['length' => 2000, 'notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['name']);
        $table->addIndex(['debut_on', 'id']);

        return $table;
    }
}

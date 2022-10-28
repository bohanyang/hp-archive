<?php

declare(strict_types=1);

namespace App\Bundle\AppBundle\Doctrine\TableProvider;

use App\Bundle\AppBundle\Doctrine\Type\JsonTextType;
use App\Bundle\AppBundle\Doctrine\Type\ObjectIdType;
use App\Bundle\CoreBundle\Doctrine\Contract\TableProvider;
use App\Bundle\CoreBundle\Doctrine\Table;
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

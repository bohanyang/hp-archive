<?php

declare(strict_types=1);

namespace App\Bundle\Doctrine\TableProvider;

use App\Bundle\Doctrine\Type\BingMarketType;
use App\Bundle\Doctrine\Type\JsonTextType;
use App\Bundle\Doctrine\Type\ObjectIdType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Manyou\Mango\Doctrine\Contract\TableProvider;
use Manyou\Mango\Doctrine\Table;

class RecordsTable implements TableProvider
{
    public function __invoke(Schema $schema): Table
    {
        $table = new Table($schema, 'records');
        $table->addColumn('id', ObjectIdType::NAME, ObjectIdType::DEFAULT_OPTIONS);
        $table->addColumn('image_id', ObjectIdType::NAME, ObjectIdType::DEFAULT_OPTIONS);
        $table->addColumn('date', Types::DATE_IMMUTABLE);
        $table->addColumn('market', BingMarketType::NAME, BingMarketType::DEFAULT_OPTIONS);
        $table->addColumn('title', Types::STRING, ['length' => 500]);
        $table->addColumn('keyword', Types::STRING, ['length' => 255, 'notnull' => false]);
        $table->addColumn('headline', Types::STRING, ['length' => 255, 'notnull' => false]);
        $table->addColumn('description', Types::STRING, ['length' => 1000, 'notnull' => false]);
        $table->addColumn('quickfact', Types::STRING, ['length' => 500, 'notnull' => false]);
        $table->addColumn('hotspots', JsonTextType::NAME, ['length' => 2000, 'notnull' => false]);
        $table->addColumn('messages', JsonTextType::NAME, ['length' => 2000, 'notnull' => false]);
        $table->addColumn('coverstory', JsonTextType::NAME, ['length' => 2000, 'notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['date', 'market']);
        $table->addForeignKeyConstraint('images', ['image_id'], ['id']);

        return $table;
    }
}

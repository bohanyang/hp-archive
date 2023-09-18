<?php

declare(strict_types=1);

namespace App\Bundle\Doctrine\Table;

use App\Bundle\Doctrine\Type\BingMarketType;
use App\Bundle\Doctrine\Type\JsonTextType;
use App\Bundle\Doctrine\Type\ObjectIdType;
use Doctrine\DBAL\Types\Types;
use Mango\Doctrine\Schema\TableBuilder;
use Mango\Doctrine\Table;

class RecordsTable implements TableBuilder
{
    public const NAME = 'records';

    public function getName(): string
    {
        return self::NAME;
    }

    public function build(Table $table): void
    {
        $table->addColumn('id', ObjectIdType::NAME, ObjectIdType::DEFAULT_OPTIONS);
        $table->addColumn('image_id', ObjectIdType::NAME, ObjectIdType::DEFAULT_OPTIONS, alias: 'imageId');
        $table->addColumn('date', Types::DATE_IMMUTABLE);
        $table->addColumn('market', BingMarketType::NAME);
        $table->addColumn('title', Types::STRING, ['length' => 500]);
        $table->addColumn('keyword', Types::STRING, ['length' => 500, 'notnull' => false]);
        $table->addColumn('headline', Types::STRING, ['length' => 500, 'notnull' => false]);
        $table->addColumn('description', Types::STRING, ['length' => 1000, 'notnull' => false]);
        $table->addColumn('quickfact', Types::STRING, ['length' => 500, 'notnull' => false]);
        $table->addColumn('hotspots', JsonTextType::NAME, ['length' => 2000, 'notnull' => false]);
        $table->addColumn('messages', JsonTextType::NAME, ['length' => 2000, 'notnull' => false]);
        $table->addColumn('coverstory', JsonTextType::NAME, ['length' => 2000, 'notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['date', 'market']);
        $table->addForeignKeyConstraint(ImagesTable::NAME, ['image_id'], ['id']);
    }
}

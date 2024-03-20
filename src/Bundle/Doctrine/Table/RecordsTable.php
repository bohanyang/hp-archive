<?php

declare(strict_types=1);

namespace App\Bundle\Doctrine\Table;

use App\Bundle\Doctrine\Type\BingMarketType;
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
        $table->addColumn('image_id', ObjectIdType::NAME, ObjectIdType::DEFAULT_OPTIONS);
        $table->addColumn('date', Types::DATE_IMMUTABLE);
        $table->addColumn('market', BingMarketType::NAME);
        $table->addColumn('title', Types::TEXT);
        $table->addColumn('keyword', Types::TEXT)->setNotnull(false);
        $table->addColumn('headline', Types::TEXT)->setNotnull(false);
        $table->addColumn('description', Types::TEXT)->setNotnull(false);
        $table->addColumn('quickfact', Types::TEXT)->setNotnull(false);
        $table->addColumn('hotspots', Types::JSON)->setNotnull(false);
        $table->addColumn('messages', Types::JSON)->setNotnull(false);
        $table->addColumn('coverstory', Types::JSON)->setNotnull(false);
        $table->setPrimaryKey(['id'], 'records_pk');
        $table->addUniqueIndex(['date', 'market'], 'records_date_market_uindex');
        $table->addForeignKeyConstraint(ImagesTable::NAME, ['image_id'], ['id'], ['onUpdate' => 'CASCADE', 'onDelete' => 'RESTRICT'], 'records_images_id_fk');
    }
}

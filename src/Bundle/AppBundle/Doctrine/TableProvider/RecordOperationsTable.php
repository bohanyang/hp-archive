<?php

declare(strict_types=1);

namespace App\Bundle\AppBundle\Doctrine\TableProvider;

use App\Bundle\AppBundle\Doctrine\Type\BingMarketType;
use App\Bundle\CoreBundle\Doctrine\Contract\TableProvider;
use App\Bundle\CoreBundle\Doctrine\Table;
use App\Bundle\CoreBundle\Doctrine\TableProvider\OperationsTable;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;

class RecordOperationsTable implements TableProvider
{
    public const NAME = 'record_operations';

    public function __invoke(Schema $schema): Table
    {
        $table = new Table($schema, self::NAME);
        $table->addColumn('id', 'ulid');
        $table->addColumn('date', Types::DATE_IMMUTABLE);
        $table->addColumn('market', BingMarketType::NAME, BingMarketType::DEFAULT_OPTIONS);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['date', 'market']);
        $table->addForeignKeyConstraint(OperationsTable::NAME, ['id'], ['id']);

        return $table;
    }
}

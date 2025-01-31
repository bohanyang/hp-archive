<?php

declare(strict_types=1);

namespace App\Doctrine\Table;

use App\Doctrine\Type\ObjectIdType;
use Doctrine\DBAL\Types\Types;
use Mango\Doctrine\Schema\TableBuilder;
use Mango\Doctrine\Table;

class ImagesTable implements TableBuilder
{
    public const NAME = 'images';

    public function getName(): string
    {
        return self::NAME;
    }

    public function build(Table $table): void
    {
        $table->addColumn('id', ObjectIdType::NAME, ObjectIdType::DEFAULT_OPTIONS);
        $table->addColumn('name', Types::STRING)->setLength(255);
        $table->addColumn('debut_on', Types::DATE_IMMUTABLE);
        $table->addColumn('urlbase', Types::STRING)->setLength(255);
        $table->addColumn('copyright', Types::TEXT)->setLength(65535);
        $table->addColumn('downloadable', Types::BOOLEAN);
        $table->addColumn('video', Types::JSON)->setNotnull(false)->setPlatformOptions(['jsonb' => true]);
        $table->setPrimaryKey(['id'], 'images_pk');
        $table->addUniqueIndex(['name'], 'images_name_uindex');
        $table->addIndex(['debut_on', 'id'], 'images_debut_on_id_index');
    }
}

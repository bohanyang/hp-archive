<?php

declare(strict_types=1);

namespace App\Doctrine;

use App\Doctrine\Type\BingMarketType;
use App\Doctrine\Type\JsonTextType;
use App\Doctrine\Type\ObjectIdType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\Provider\SchemaProvider as SchemaProviderInterface;

class SchemaProvider implements SchemaProviderInterface
{
    private Schema $schema;

    private array $tables = [];

    public function __construct(
        private Connection $connection,
    ) {
        $this->createSchema();
    }

    public function createSchema(): Schema
    {
        if (isset($this->schema)) {
            return $this->schema;
        }

        $schemaManager = $this->connection->createSchemaManager();
        $schema        = new Schema(schemaConfig: $schemaManager->createSchemaConfig());

        foreach ($this->getTableProviders() as $tableName => $tableProvider) {
            $this->tables[$tableName] = $tableProvider($schema);
        }

        return $this->schema = $schema;
    }

    private function getTableProviders(): iterable
    {
        yield 'images' => static function (Schema $schema): Table {
            $table = $schema->createTable('images');
            $table->addColumn('id', ObjectIdType::NAME, ObjectIdType::DEFAULT_OPTIONS);
            $table->addColumn('name', Types::STRING, ['length' => 255]);
            $table->addColumn('debut_on', Types::DATE_IMMUTABLE);
            $table->addColumn('urlbase', Types::STRING, ['length' => 255]);
            $table->addColumn('copyright', Types::STRING, ['length' => 255]);
            $table->addColumn('downloadable', Types::BOOLEAN);
            $table->addColumn('video', JsonTextType::NAME, ['length' => 2000, 'notnull' => false]);
            $table->setPrimaryKey(['id']);
            $table->addUniqueIndex(['name']);
            $table->addIndex(['debut_on']);

            return new Table($table, ['debutOn' => 'debut_on']);
        };

        yield 'records' => static function (Schema $schema): Table {
            $table = $schema->createTable('records');
            $table->addColumn('id', ObjectIdType::NAME, ObjectIdType::DEFAULT_OPTIONS);
            $table->addColumn('image_id', ObjectIdType::NAME, ObjectIdType::DEFAULT_OPTIONS);
            $table->addColumn('date', Types::DATE_IMMUTABLE);
            $table->addColumn('market', BingMarketType::NAME, BingMarketType::DEFAULT_OPTIONS);
            $table->addColumn('title', Types::STRING, ['length' => 255]);
            $table->addColumn('keyword', Types::STRING, ['length' => 255]);
            $table->addColumn('headline', Types::STRING, ['length' => 255, 'notnull' => false]);
            $table->addColumn('description', Types::STRING, ['length' => 1000, 'notnull' => false]);
            $table->addColumn('quickfact', Types::STRING, ['length' => 255, 'notnull' => false]);
            $table->addColumn('hotspots', JsonTextType::NAME, ['length' => 2000, 'notnull' => false]);
            $table->addColumn('messages', JsonTextType::NAME, ['length' => 2000, 'notnull' => false]);
            $table->addColumn('coverstory', JsonTextType::NAME, ['length' => 2000, 'notnull' => false]);
            $table->setPrimaryKey(['id']);
            $table->addUniqueIndex(['date', 'market']);
            $table->addForeignKeyConstraint('images', ['image_id'], ['id']);

            return new Table($table, ['imageId' => 'image_id']);
        };
    }

    public function createQuery(): Query
    {
        return new Query($this->connection, $this);
    }

    public function getTable(string $name): Table
    {
        return $this->tables[$name];
    }
}

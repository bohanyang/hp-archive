<?php

declare(strict_types=1);

namespace App\Doctrine;

use App\Doctrine\Type\OrderStatusType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\Provider\SchemaProvider as SchemaProviderInterface;

class SchemaProvider implements SchemaProviderInterface
{
    private Schema $schema;

    public function __construct(
        private Connection $connection,
    ) {
    }

    public function createSchema(): Schema
    {
        if (isset($this->schema)) {
            return $this->schema;
        }

        $schemaManager = $this->connection->createSchemaManager();
        $schema        = new Schema(schemaConfig: $schemaManager->createSchemaConfig());

        foreach ($this->getTableProviders() as $tableProvider) {
            $tableProvider($schema);
        }

        return $this->schema = $schema;
    }

    private function getTableProviders(): iterable
    {
        yield static function (Schema $schema): void {
            $table = $schema->createTable('orders');
            $table->addColumn('id', Types::INTEGER, ['unsigned' => true]);
            $table->addColumn('name', Types::STRING, ['length' => 255]);
            $table->addColumn('status', OrderStatusType::NAME, OrderStatusType::DEFAULT_OPTIONS);
            $table->setPrimaryKey(['id']);
            $table->addIndex(['status']);
        };
    }

    public function createQuery(): Query
    {
        return new Query($this->connection, $this->createSchema());
    }
}

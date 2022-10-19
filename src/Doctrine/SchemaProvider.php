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
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function createSchema(): Schema
    {
        $schemaManager = $this->connection->createSchemaManager();
        $schema        = new Schema([], [], $schemaManager->createSchemaConfig());

        foreach ($this->getTableSchemaProviders() as $tableSchemaProvider) {
            $tableSchemaProvider($schema);
        }

        return $schema;
    }

    private function getTableSchemaProviders(): iterable
    {
        yield static function (Schema $schema): void {
            $table = $schema->createTable('orders');
            $table->addColumn('id', Types::INTEGER, ['unsigned' => true]);
            $table->addColumn('name', Types::STRING, ['length' => 255]);
            $table->addColumn('status', OrderStatusType::NAME, OrderStatusType::DEFAULT_OPTIONS);
            $table->setPrimaryKey(['id']);
        };
    }
}

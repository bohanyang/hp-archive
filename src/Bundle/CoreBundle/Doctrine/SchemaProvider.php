<?php

declare(strict_types=1);

namespace App\Bundle\CoreBundle\Doctrine;

use App\Bundle\CoreBundle\Doctrine\Contract\TableProvider;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\Provider\SchemaProvider as SchemaProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class SchemaProvider implements SchemaProviderInterface
{
    private Schema $schema;

    private array $tables = [];

    /** @param TableProvider[] $tableProviders */
    public function __construct(
        private Connection $connection,
        #[TaggedIterator('core.doctrine.table_provider')]
        private iterable $tableProviders,
    ) {
        $this->createSchema();
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function createSchema(): Schema
    {
        if (isset($this->schema)) {
            return $this->schema;
        }

        $schemaManager = $this->connection->createSchemaManager();

        $schema = new Schema(schemaConfig: $schemaManager->createSchemaConfig());

        foreach ($this->tableProviders as $tableProvider) {
            $table = $tableProvider($schema);

            $this->tables[$table->name] = $table;
        }

        return $this->schema = $schema;
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

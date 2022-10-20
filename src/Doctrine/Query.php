<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use ErrorException;
use InvalidArgumentException;

use function explode;
use function is_string;

/**
 * @method $this where($predicates)
 * @method $this andWhere($where)
 * @method $this orWhere($where)
 * @method $this setMaxResults(int|null $maxResults)
 */
class Query
{
    public const EQ  = '=';
    public const NEQ = '<>';
    public const LT  = '<';
    public const LTE = '<=';
    public const GT  = '>';
    public const GTE = '>=';

    private QueryBuilder $builder;

    private AbstractPlatform $platform;

    private array $selects = [];

    /** @var Type[] */
    private array $resultTypeMap = [];

    /** @var Table[] */
    private array $refTableMap = [];

    public function __construct(
        private Connection $connection,
        private Schema $schema,
    ) {
        $this->builder  = $this->connection->createQueryBuilder();
        $this->platform = $this->connection->getDatabasePlatform();
    }

    public function insert(string $into, array $data)
    {
        $table = $this->schema->getTable($into);

        $this->builder->insert($into);

        $values = [];
        foreach ($data as $key => $value) {
            $values[$key] = $this->builder->createPositionalParameter(
                $value,
                $table->getColumn($key)->getType(),
            );
        }

        $this->builder->values($values);

        return $this;
    }

    public function selectFrom(string|array $from, string ...$args)
    {
        [$fromTable, $fromAlias] = is_string($from) ? [$from, null] : $from;

        $ref   = $fromAlias ?? $fromTable;
        $table = $this->schema->getTable($fromTable);

        $this->refTableMap[$ref] = $table;

        $this->builder->from($fromTable, $fromAlias);

        foreach ($args as $select => $column) {
            $alias = is_string($select) ? $select : $column;

            $this->resultTypeMap[$alias] = $type = $table->getColumn($column)->getType();

            $this->selects[] = $type->convertToPHPValueSQL("{$ref}.{$column}", $this->platform) . ' ' . $this->platform->quoteSingleIdentifier($alias);
        }

        return $this;
    }

    public function executeQuery(): Result
    {
        if ($this->selects !== []) {
            $this->builder->select(...$this->selects);
        }

        return $this->builder->executeQuery();
    }

    public function getBuilder(): QueryBuilder
    {
        return $this->builder;
    }

    public function comparison(string $x, string $operator, $y): string
    {
        try {
            [$ref, $column] = explode('.', $x);
        } catch (ErrorException) {
            throw new InvalidArgumentException(
                'The left hand side of a comparison should be like "<table or alias>.<column>".',
            );
        }

        return $x . ' ' . $operator . ' ' . $this->builder
            ->createPositionalParameter($y, $this->refTableMap[$ref]->getColumn($column)->getType());
    }

    public function eq(string $x, $y): string
    {
        return $this->comparison($x, self::EQ, $y);
    }

    public function neq(string $x, $y): string
    {
        return $this->comparison($x, self::NEQ, $y);
    }

    public function lt(string $x, $y): string
    {
        return $this->comparison($x, self::LT, $y);
    }

    public function lte(string $x, $y): string
    {
        return $this->comparison($x, self::LTE, $y);
    }

    public function gt(string $x, $y): string
    {
        return $this->comparison($x, self::GT, $y);
    }

    public function gte(string $x, $y): string
    {
        return $this->comparison($x, self::GTE, $y);
    }

    public function executeStatement(): int
    {
        return $this->builder->executeStatement();
    }

    public function __call($name, $arguments)
    {
        $this->builder->{$name}(...$arguments);

        return $this;
    }
}

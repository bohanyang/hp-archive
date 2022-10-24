<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Schema\SchemaException;
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

    private Result $result;

    private array $selects = [];

    /** @var Type[] */
    private array $resultTypeMap = [];

    /** @var string[] */
    private array $resultTableAliasMap = [];

    /** @var string[] */
    private array $resultColumnAliasMap = [];

    private int $resultAliasCounter = 0;

    /** @var Table[] Tracking table alias for reference in expressions */
    private array $selectTableMap = [];

    public function __construct(
        private Connection $connection,
        private SchemaProvider $schema,
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
            try {
                $column = $table->getColumn($key);
            } catch (SchemaException) {
                // ignore unknown columns
                continue;
            }

            $values[$column->getQuotedName($this->platform)] = $this->builder->createPositionalParameter(
                $value,
                $column->getType(),
            );
        }

        $this->builder->values($values);

        return $this;
    }

    private function getResultAlias(): string
    {
        return 'c' . $this->resultAliasCounter++;
    }

    public function selectFrom(string|array $from, string ...$selects)
    {
        [$fromTable, $fromAlias] = is_string($from) ? [$from, null] : $from;

        $table = $this->schema->getTable($fromTable);
        $this->builder->from($fromTable, $fromAlias);

        $fromAlias ??= $fromTable;

        $this->selectTableMap[$fromAlias] = $table;
        $this->addSelects($fromAlias, $selects);

        return $this;
    }

    public function join(string $fromAlias, string $joinTable, string $joinAlias, string $on, string ...$selects)
    {
        $table = $this->schema->getTable($joinTable);
        $this->builder->join($fromAlias, $joinTable, $joinAlias, $on);

        $this->selectTableMap[$joinAlias] = $table;
        $this->addSelects($joinAlias, $selects);

        return $this;
    }

    private function addSelects(string $tableAlias, array $selects)
    {
        $table = $this->selectTableMap[$tableAlias];

        foreach ($selects as $columnAlias => $column) {
            $columnAlias = is_string($columnAlias)
                // Named parameter: `$this->selectFrom('table', alias1: 'column1')`
                ? $columnAlias
                // Positional parameter: `$this->selectFrom('table', 'column1', 'column2')`
                : $column;

            $column      = $table->getColumn($column);
            $resultAlias = $this->getResultAlias();

            $this->resultTypeMap[$resultAlias]        = $type = $column->getType();
            $this->resultTableAliasMap[$resultAlias]  = $tableAlias;
            $this->resultColumnAliasMap[$resultAlias] = $columnAlias;

            $this->selects[] =
                $type->convertToPHPValueSQL(
                    $tableAlias . '.' . $column->getQuotedName($this->platform),
                    $this->platform,
                )
                . ' ' . $this->platform->quoteSingleIdentifier($resultAlias);
        }
    }

    private function getQueryResult(): Result
    {
        if (isset($this->result)) {
            return $this->result;
        }

        if ($this->selects !== []) {
            $this->builder->select(...$this->selects);
        }

        return $this->result = $this->builder->executeQuery();
    }

    private function convertResultValues(array $result): array
    {
        foreach ($result as $resultAlias => $value) {
            $value = $this->resultTypeMap[$resultAlias]
                ->convertToPHPValue($value, $this->platform);

            $tableAlias  = $this->resultTableAliasMap[$resultAlias];
            $columnAlias = $this->resultColumnAliasMap[$resultAlias];

            $row[$tableAlias][$columnAlias] = $value;
        }

        return $row;
    }

    public function fetchAssociative(): array|false
    {
        if (false !== $row = $this->getQueryResult()->fetchAssociative()) {
            return $this->convertResultValues($row);
        }

        return $row;
    }

    public function fetchAllAssociative(): array
    {
        $rows = $this->getQueryResult()->fetchAllAssociative();

        foreach ($rows as $i => $row) {
            $rows[$i] = $this->convertResultValues($row);
        }

        return $rows;
    }

    public function getBuilder(): QueryBuilder
    {
        return $this->builder;
    }

    public function comparison(string $x, string $operator, $y): string
    {
        try {
            [$tableAlias, $column] = explode('.', $x);
        } catch (ErrorException) {
            throw new InvalidArgumentException(
                'The left hand side of a comparison should be like "<tableAlias>.<column>".',
            );
        }

        $column = $this->selectTableMap[$tableAlias]->getColumn($column);

        return "{$tableAlias}.{$column->getQuotedName($this->platform)} {$operator} "
            . $this->builder->createPositionalParameter($y, $column->getType());
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

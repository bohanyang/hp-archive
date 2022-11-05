<?php

declare(strict_types=1);

namespace Manyou\Mango\Operation\Monolog;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Manyou\Mango\Doctrine\Type\LogLevelType;
use Manyou\Mango\Operation\Doctrine\TableProvider\OperationLogsTable;
use Monolog\Handler\AbstractHandler;
use Monolog\Handler\FormattableHandlerInterface;
use Monolog\Handler\FormattableHandlerTrait;
use Monolog\Level;
use Monolog\LogRecord;
use Symfony\Component\Uid\Ulid;

class OperationLogHandler extends AbstractHandler implements FormattableHandlerInterface
{
    use FormattableHandlerTrait;

    public const CONTEXT_KEY = 'operation_id';

    public function __construct(private Connection $connection, int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
    }

    public function handle(LogRecord $record): bool
    {
        if (! $this->isHandling($record)) {
            return false;
        }

        $operationId = $record->context[self::CONTEXT_KEY] ?? null;

        if (! $operationId instanceof Ulid) {
            return false;
        }

        $record->formatted = $this->getFormatter()->format($record);

        $rowNum = $this->connection->createQueryBuilder()->insert(OperationLogsTable::NAME)
            ->values([
                'id' => '?',
                'operation_id' => '?',
                'level' => '?',
                'message' => '?',
                'context' => '?',
                'extra' => '?',
            ])
            ->setParameters([
                new Ulid(Ulid::generate($record->datetime)),
                $operationId,
                $record->level,
                $record->formatted['message'],
                $record->formatted['context'],
                $record->formatted['extra'],
            ], [
                'ulid',
                'ulid',
                LogLevelType::NAME,
                Types::TEXT,
                Types::JSON,
                Types::JSON,
            ])->executeStatement();

        if ($rowNum !== 1) {
            return false;
        }

        return false === $this->bubble;
    }
}

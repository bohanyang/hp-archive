<?php

declare(strict_types=1);

namespace App\Messenger\Middleware;

use App\Bundle\CoreBundle\Doctrine\SchemaProvider;
use App\Bundle\CoreBundle\Doctrine\Type\OperationStatusType;
use App\Bundle\CoreBundle\Messenger\Stamp\CreateOperationStamp;
use App\Bundle\CoreBundle\Messenger\Stamp\OperationStamp;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Uid\Ulid;
use Throwable;

class OperationMiddware implements MiddlewareInterface
{
    public function __construct(private SchemaProvider $schema)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        /** @var CreateOperationStamp|null */
        if (null !== $stamp = $envelope->last(CreateOperationStamp::class)) {
            return $this->schema->getConnection()->transactional(function () use ($envelope, $stack, $stamp) {
                $this->schema->createQuery()->insert(
                    'operations',
                    ['id' => $id = new Ulid(), 'status' => OperationStatusType::QUEUEING],
                )->executeStatement();

                $stamp->callback($id);

                $envelope = $envelope
                    ->withoutStampsOfType(CreateOperationStamp::class)
                    ->with(new OperationStamp($id));

                $envelope = $stack->next()->handle($envelope, $stack);

                return $envelope;
            });
        }

        /** @var OperationStamp|null */
        if (null !== $stamp = $envelope->last(OperationStamp::class)) {
            return $this->schema->getConnection()->transactional(function () use ($envelope, $stack, $stamp) {
                $q = $this->schema->createQuery();

                $rowNum = $q
                    ->update('operations', ['status' => OperationStatusType::PROCESSING])
                    ->where($q->eq('operations.id', $stamp->getId()), $q->eq('operations.status', 'queueing'))
                    ->executeStatement();

                if ($rowNum !== 1) {
                    // not in a processable state
                    return $envelope;
                }

                try {
                    $envelope = $stack->next()->handle($envelope, $stack);
                } catch (Throwable) {
                    $q = $this->schema->createQuery();
                    $q->update('operations', ['status' => OperationStatusType::FAILED])
                        ->where($q->eq('operations.id', $stamp->getId()))
                        ->executeStatement();

                    // TODO: Log Throwable
                    return $envelope;
                }

                $q = $this->schema->createQuery();
                $q->update('operations', ['status' => OperationStatusType::COMPLETED])
                    ->where($q->eq('operations.id', $stamp->getId()))
                    ->executeStatement();

                return $envelope;
            });
        }

        return $envelope;
    }
}

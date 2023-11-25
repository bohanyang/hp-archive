<?php
/*
* This file is part of the Symfony package.
*
* (c) Fabien Potencier <fabien@symfony.com>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

declare(strict_types=1);

namespace App\Bundle\Message;

use Doctrine\DBAL\Exception as DBALException;
use Mango\Doctrine\SchemaProvider;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ConsumedByWorkerStamp;

/**
 * Checks whether the connection is still open or reconnects otherwise.
 *
 * @author Fuong <insidestyles@gmail.com>
 */
class DoctrinePingConnectionMiddleware implements MiddlewareInterface
{
    public function __construct(private SchemaProvider $schema)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        if (null !== $envelope->last(ConsumedByWorkerStamp::class)) {
            $connection = $this->schema->getConnection();

            try {
                $connection->executeQuery($connection->getDatabasePlatform()->getDummySelectSQL());
            } catch (DBALException) {
                $connection->close();
                $connection->connect();
            }
        }

        return $stack->next()->handle($envelope, $stack);
    }
}

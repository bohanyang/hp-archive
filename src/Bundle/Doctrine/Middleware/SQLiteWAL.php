<?php

declare(strict_types=1);

namespace App\Bundle\Doctrine\Middleware;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;
use Doctrine\DBAL\Driver\SQLite3;
use SensitiveParameter;

use function class_exists;

// Help opcache.preload discover always-needed symbols
class_exists(AbstractDriverMiddleware::class);

class SQLiteWAL implements Middleware
{
    public function wrap(Driver $driver): Driver
    {
        if (! $driver instanceof SQLite3\Driver) {
            return $driver;
        }

        return new class ($driver) extends AbstractDriverMiddleware {
            /**
             * {@inheritDoc}
             */
            public function connect(
                #[SensitiveParameter]
                array $params,
            ): Connection {
                $connection = parent::connect($params);

                $connection->exec('
                    PRAGMA busy_timeout = 5000;
                    PRAGMA synchronous = NORMAL;
                    PRAGMA cache_size = 1000000000;
                    PRAGMA foreign_keys = true;
                    PRAGMA temp_store = memory;
                ');

                return $connection;
            }
        };
    }
}

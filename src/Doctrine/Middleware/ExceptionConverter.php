<?php

declare(strict_types=1);

namespace App\Doctrine\Middleware;

use Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Exception\ConnectionLost;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Query;

use function str_contains;

class ExceptionConverter implements ExceptionConverterInterface
{
    public function __construct(
        private readonly ExceptionConverterInterface $decorated,
    ) {
    }

    public const CONNECTION_LOST_ERRORS = [
        'terminating connection due to administrator command SSL connection has been closed unexpectedly',
        'no connection to the server',
        'SSL SYSCALL error: EOF detected',
    ];

    public function convert(Exception $exception, ?Query $query): DriverException
    {
        if ($exception->getSQLState() === 'HY000') {
            foreach (self::CONNECTION_LOST_ERRORS as $needle) {
                if (str_contains($exception->getMessage(), $needle)) {
                    return new ConnectionLost($exception, $query);
                }
            }
        }

        return $this->decorated->convert($exception, $query);
    }
}

<?php

declare(strict_types=1);

namespace App\Bundle\Doctrine\Middleware;

use Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Exception\ConnectionLost;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Query;

use function preg_match;

class ExceptionConverter implements ExceptionConverterInterface
{
    public function __construct(
        private readonly ExceptionConverterInterface $decorated,
    ) {
    }

    public function convert(Exception $exception, ?Query $query): DriverException
    {
        if (preg_match('/terminating connection due to administrator command SSL connection has been closed unexpectedly/', $exception->getMessage())) {
            return new ConnectionLost($exception, $query);
        }

        if (preg_match('/no connection to the server/', $exception->getMessage())) {
            return new ConnectionLost($exception, $query);
        }

        return $this->decorated->convert($exception, $query);
    }
}

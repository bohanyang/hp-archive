<?php

declare(strict_types=1);

namespace App\Bundle\Doctrine\Middleware;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\API\ExceptionConverter as ExceptionConverterInterface;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;

use function class_exists;

// Help opcache.preload discover always-needed symbols
class_exists(AbstractDriverMiddleware::class);

class ExceptionConverterMiddleware implements Middleware
{
    public function wrap(Driver $driver): Driver
    {
        return new class ($driver) extends AbstractDriverMiddleware {
            public function getExceptionConverter(): ExceptionConverterInterface
            {
                return new ExceptionConverter(parent::getExceptionConverter());
            }
        };
    }
}

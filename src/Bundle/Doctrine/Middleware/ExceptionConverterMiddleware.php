<?php

declare(strict_types=1);

namespace App\Bundle\Doctrine\Middleware;

use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Driver\Middleware;
use Doctrine\DBAL\Driver\Middleware\AbstractDriverMiddleware;

class ExceptionConverterMiddleware implements Middleware
{
    public function wrap(Driver $driver): Driver
    {
        return new class ($driver) extends AbstractDriverMiddleware {
            public function getExceptionConverter(): ExceptionConverter
            {
                return new ExceptionConverter(parent::getExceptionConverter());
            }
        };
    }
}

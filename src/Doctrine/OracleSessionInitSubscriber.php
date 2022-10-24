<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Event\Listeners\OracleSessionInit;
use Doctrine\DBAL\Platforms\OraclePlatform;

class OracleSessionInitSubscriber extends OracleSessionInit
{
    public function postConnect(ConnectionEventArgs $args)
    {
        if ($args->getConnection()->getDatabasePlatform() instanceof OraclePlatform) {
            parent::postConnect($args);
        }
    }
}

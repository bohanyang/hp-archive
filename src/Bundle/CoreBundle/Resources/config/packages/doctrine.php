<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Bundle\CoreBundle\Doctrine\Type\OperationStatusType;
use Symfony\Config\DoctrineConfig;

return static function (DoctrineConfig $doctrine): void {
    $dbal = $doctrine->dbal();
    $dbal->type(OperationStatusType::NAME, OperationStatusType::class);
};

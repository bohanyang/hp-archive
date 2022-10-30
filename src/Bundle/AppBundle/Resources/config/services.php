<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Bundle\AppBundle\Doctrine\TableProvider\ImagesTable;
use App\Bundle\AppBundle\Doctrine\TableProvider\RecordOperationsTable;
use App\Bundle\AppBundle\Doctrine\TableProvider\RecordsTable;
use App\Bundle\AppBundle\Repository\RecordOperationRepository;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(ImagesTable::class);
    $services->set(RecordsTable::class);
    $services->set(RecordOperationsTable::class);
    $services->set(RecordOperationRepository::class);
};

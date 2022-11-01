<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Bundle\ApiPlatform\ImageProvider;
use App\Bundle\ApiPlatform\RecordProvider;
use App\Bundle\ApiPlatform\StateProcessor;
use App\Bundle\Doctrine\TableProvider\ImagesTable;
use App\Bundle\Doctrine\TableProvider\RecordOperationsTable;
use App\Bundle\Doctrine\TableProvider\RecordsTable;
use App\Bundle\Repository\DoctrineRepository;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(ImagesTable::class);
    $services->set(RecordsTable::class);
    $services->set(RecordOperationsTable::class);
    $services->set(DoctrineRepository::class);
    $services->set(RecordProvider::class);
    $services->set(ImageProvider::class);
    $services->set(StateProcessor::class);
};

<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Manyou\Mango\Doctrine\Driver\Oci8InitializeSession;
use Manyou\Mango\Doctrine\SchemaProvider;
use Manyou\Mango\Doctrine\TableProvider\OperationsTable;
use Manyou\Mango\Operation\Messenger\Middleware\OperationMiddware;
use Manyou\Mango\Operation\Repository\OperationRepository;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(SchemaProvider::class)->public();
    $services->set(Oci8InitializeSession::class);
    $services->set(OperationsTable::class);
    $services->set(OperationMiddware::class);
    $services->set(OperationRepository::class)->public();
};

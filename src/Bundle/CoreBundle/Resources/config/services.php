<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\Bundle\CoreBundle\Doctrine\Driver\Oci8InitializeSession;
use App\Bundle\CoreBundle\Doctrine\SchemaProvider;
use App\Bundle\CoreBundle\Doctrine\TableProvider\OperationsTable;
use App\Bundle\CoreBundle\Repository\OperationRepository;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services
        ->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(SchemaProvider::class)->public();
    $services->set(Oci8InitializeSession::class);
    $services->set(OperationsTable::class);
    $services->set(OperationRepository::class)->public();
};
